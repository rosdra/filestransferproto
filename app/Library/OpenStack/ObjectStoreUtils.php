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

    public function downloadByCurl($objectStore, $transfer)
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


    function download_transfer_files_as_zip($transfer,$destinationPath, $overwrite = true) {
        // Get object service
        $objectStore = $this->getObjectStore();

        $objects = $this->downloadByCurl($objectStore,$transfer);
        $destination = uniqid("filetransfer_",true).".zip";
        $destination = $destinationPath . $destination;
        $destination = public_path($destination);

        //if the zip file already exists and overwrite is false, return false
        if(file_exists($destination) && !$overwrite) { return false; }

        //vars
        $valid_files = array();
        //if files were passed in...
        if(is_array($objects)) {
            //cycle through each file
            foreach($objects as $obj) {
                $extension = (explode(".", $obj["object"]->name()));
                $extension = end($extension);
                $fileTemp = uniqid("temp_",true).".".$extension;
                $fileTemp = $destinationPath . $fileTemp;
                $fileTemp = public_path($fileTemp);

                file_put_contents($fileTemp,$obj["object"]->stream(true));

                //make sure the file exists
                if(file_exists($fileTemp)) {
                    $valid_files[] = [ "original_name" => $obj["original_name"], "fileTemp" => $fileTemp ];
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
                $zip->addFile($file["fileTemp"], $file["original_name"]);
            }

            // pruebas
            //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

            //close the zip finich!
            $zip->close();

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
}