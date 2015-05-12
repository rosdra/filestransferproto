<?php

use OpenStack\Identity\v2\IdentityService;
use OpenStack\ObjectStore\v1\ObjectStorage;
use OpenStack\ObjectStore\v1\Resource\Container;
use OpenStack\ObjectStore\v1\Resource\Object;

class ObjectStoreUtils
{
    private $identity;
    private $username;
    private $password;
    private $tenantName;
    protected $total_files_size;
    protected $total_files_downloaded;

    public function __construct(IdentityService $identity = null, $username, $password, $tenantName) {
        $this->identity = $identity;
        $this->username = $username;
        $this->password = $password;
        $this->tenantName = $tenantName;
    }

    function getObjectStore()
    {
        // $token will be your authorization key when you connect to other
        // services. You can also get it from $identity->token().
        $token = $this->identity->authenticateAsUser($this->username, $this->password, null, $this->tenantName);

        // Get a listing of all of the services you currently have configured in
        // OpenStack.
        //$catalog = $identity->serviceCatalog();
        //$tenantName = $identity->tenantName();

        $storageList = $this->identity->serviceCatalog('object-store');
        $objectStorageUrl = $storageList[0]['endpoints'][0]['publicURL'];

        // Create a new ObjectStorage instance:
        $objectStore = new ObjectStorage($token, $objectStorageUrl);

        return $objectStore;
    }

    function createAndOrRetrieveContainer(ObjectStorage $objectStore = null, $containerName){
        $container = '';

        // Retrieve the container (check if exists)
        try {
            $container = $objectStore->container($containerName);
        } catch (Exception $ex) {
            // Create the container for the file
            $objectStore->createContainer($containerName);
            // load the container
            $container = $objectStore->container($containerName);
        }

        return $container;
    }

    function uploadFile(Container $container, $filepath) {
        $filename = basename($filepath);

        // get contents of file
        $filecontents = file_get_contents($filepath);

        // get file mime type
        $finfo = new finfo(FILEINFO_MIME);
        $type = $finfo->file($filepath);

        // Send file to save
        $localObject = new Object($filename, $filecontents, $type);
        return $container->save($localObject);
    }

    public function downloadObjectsByTransfer($objectStore, $transfer)
    {
        $container = $this->createAndOrRetrieveContainer($objectStore,$transfer->container_name);

        $files = $transfer->files()->get();
        $objects = [];
        foreach($files as $f){
            // Get File from container
            $object = $container->object($f->object_name);
            $objects[] = [ "original_name" => $f->original_name, "object" => $object ];
        }

        return $objects;
    }

    public function downloadByCurl($destinationPath, $progressFileName, $transfer)
    {
        // Get object service
        $objectStore = $this->getObjectStore();
        $valid_files = [];
        $this->total_files_size =$this->getTotalFileSize($transfer);
        $this->total_files_downloaded = 0;

        $res = false;
        foreach($transfer->files()->get() as $file) {
            $url = $objectStore->url() . "/" . $transfer->container_name . "/" . $file->object_name;

            $extension = (explode(".", $file->original_name));
            $extension = end($extension);
            $destination = uniqid("temp_", true) . "." . $extension;
            $destination = $destinationPath . $destination;
            $destination = public_path($destination);

            $targetFile = fopen($destination, 'w');
            $token = $objectStore->token();

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Auth-Token: ' . $token));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

            $me = $this;
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($resource, $download_size, $downloaded_size) use ($me, $progressFileName) {
                $me->progressCallback($resource, $download_size, $downloaded_size, $me, $progressFileName);
            });
            curl_setopt($ch, CURLOPT_FILE, $targetFile);
            $res = curl_exec($ch);
            curl_close($ch);
            fclose($targetFile);

            if($res == false){
                break;
            }
            $valid_files[] = [ "original_name" => $file->original_name, "fileTemp" => $destination ];
        }

        return $valid_files;
    }

    function uploadFileChunks(Container $container, $filepath) {

        $curl = new anlutro\cURL\cURL;



        $filename = basename($filepath);

        // get contents of file
        $filecontents = file_get_contents($filepath);

        // get file mime type
        $finfo = new finfo(FILEINFO_MIME);
        $type = $finfo->file($filepath);

        // Send file to save
        $localObject = new Object($filename, $filecontents, $type);
        return $container->save($localObject);
    }


    function download_transfer_files_as_zip($transfer,$destinationPath, $progressFileName, $overwrite = true) {
        // Get object service
        $objectStore = $this->getObjectStore();

        $valid_files = $this->downloadByCurl($destinationPath,$progressFileName,$transfer);

        $destination = uniqid("filetransfer_",true).".zip";
        $destination = $destinationPath . $destination;
        $destination = public_path($destination);

        //if the zip file already exists and overwrite is false, return false
        if(file_exists($destination) && !$overwrite) { return false; }

        //if we have good files...
        if(count($valid_files)) {
            //create the archive
            $zip = new ZipArchive();
            if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                return false;
            }
            //add the files
            foreach($valid_files as $file) {
                $zip->addFile($file["fileTemp"], $file["original_name"]);
            }

            //close the zip finish!
            $zip->close();

            //delete temp files
            foreach($valid_files as $file) {
                unlink($file["fileTemp"]);
            }

            $array = ["finished" => true , "zip" => $destination];
            $fp = fopen($progressFileName, 'w');
            fwrite($fp, json_encode($array));
            fclose($fp);

            //check to make sure the file exists
            return $destination;
        }
        else
        {
            return false;
        }
    }


    function create_zip($files = array(), $destination = '', $overwrite = false) {
        //if the zip file already exists and overwrite is false, return false
        if(file_exists($destination) && !$overwrite) { return false; }
        //vars
        $valid_files = array();
        //if files were passed in...
        if(is_array($files)) {
            //cycle through each file
            foreach($files as $file) {
                //make sure the file exists
                if(file_exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }
        //if we have good files...
        if(count($valid_files)) {
            //create the archive
            $zip = new ZipArchive();
            if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                return false;
            }
            //add the files
            foreach($valid_files as $file) {
                $zip->addFile($file,$file);
            }

            // pruebas
            //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

            //close the zip finich!
            $zip->close();

            //check to make sure the file exists
            return file_exists($destination);
        }
        else
        {
            return false;
        }
    }


    function progressCallback($resource, $download_size, $downloaded_size,$me,$progressFileName)
    {
        static $previousProgress = 0;
        static $previousDownloaded = 0;
        if($download_size == 0 && $downloaded_size == 0){
            $previousDownloaded = 0;
            $previousProgress = 0;
        }

        if ( $download_size == 0 )
            $progress = 0;
        else
            $progress = round( $downloaded_size * 100 / $download_size );

        if ( $progress > $previousProgress)
        {
            if($previousDownloaded == 0 && $download_size != $downloaded_size){
                $bytesDownloaded = $previousDownloaded = $downloaded_size;
            }
            else{
                $bytesDownloaded =  $downloaded_size - $previousDownloaded;
                $previousDownloaded = $downloaded_size;
            }
            $me->total_files_downloaded = $me->total_files_downloaded + $bytesDownloaded;
            $previousProgress = $progress;

            if ( $me->total_files_size == 0 )
                $total_progress = 0;
            else
                $total_progress = round( $me->total_files_downloaded * 100 / $me->total_files_size );

            $array = ["progress" => $total_progress , "finished" => false];
            $fp = fopen( $progressFileName, 'w' );
            fwrite( $fp, json_encode($array));
            fclose( $fp );
        }
    }


    function getTotalFileSize($transfer){
        $total = 0;
        foreach($transfer->files()->get() as $f){
            $total = $total + $f->size;
        }

        return $total;
    }
}