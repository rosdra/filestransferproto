<?php
use OpenStack\Identity\v2\IdentityService;
use app\Lib\Storage\Transfers\ITransferRepository;
/**
 * Created by PhpStorm.
 * User: PHP_RAUL
 * Date: 5/7/2015
 * Time: 5:37 PM
 */

class UploadController extends BaseController {

    protected $transfer;
    public function __construct(ITransferRepository $transfer)
    {
        $this->transfer = $transfer;
    }



    public function index()
    {
        // Create a new identity service object, and tell it where to
        // go to authenticate. This URL can be found in your console.
        $identity = new IdentityService($_ENV['swiftendpoint']);

        // Init Utils and authenticate
        $objectStoreUtils = new ObjectStoreUtils($identity, $_ENV['swiftusername'], $_ENV['swiftpassword'], $_ENV['swifttenantname']);

        Session::put('objectStoreUtils', $objectStoreUtils);
        Session::forget('transfer_id');

        //$containerName = uniqid();
        //Session::put('containerName', $containerName);*/

        return View::make('upload.index');
    }



    public function upload()
    {
        if(\Request::ajax()){
            $storeFolder = "uploads/";

            $data = Input::all();

            $file = Input::file('files');
            $fileSize = @$file->getSize();

            if ( ! $fileSize || ! $file->getMimeType())
                return \Response::json(array('success'=> false));

            // NOTE: store file original Name in DB
            $fileOriginalName = $file->getClientOriginalName();
            $fileMymeType = $file->getMimeType();

            $extension = (explode(".", $_FILES['files']['name']));
            $extension = end($extension);

            // NOTE: Store file temp name in DB
            $fileName = uniqid() . "." . $extension;
            $targetFile = $storeFolder . $fileName;
            $fileFullPath = public_path($targetFile);

            $file->move($storeFolder, $fileName);

            // Upload files to Swift
            $objectStoreUtils = Session::get('objectStoreUtils');

            // NOTE: Store Container name in DB
            $containerName = uniqid();

            //Check if this is an multiple file operation
            $transfer_id = Session::get('transfer_id');
            if($transfer_id != null){
                $transfer = $this->transfer->find($transfer_id);
                if($transfer != null)
                    $containerName = $transfer->container_name;
            }

            // Get object service
            $objectStore = $objectStoreUtils->getObjectStore();

            // Create and retrieve the container
            // To get the file AND delete the container when the file is downloaded
            $container = $objectStoreUtils->createAndOrRetrieveContainer($objectStore, $containerName);

            // Upload file to swift
            $success = $objectStoreUtils->uploadFile($container, $fileFullPath);
            $success = true;
            //$object = $container->object($fileName);

            $slug = Str::slug($fileOriginalName);
            if(!$slug){
                $slug = str_random(9);
            }

            //Save/Update container and file information into Database
            if($transfer_id == null) {
                $data = [
                    'container_name' => $containerName,
                    'files' => [
                        [
                            'original_name' => $fileOriginalName,
                            'object_name' => $fileName,
                            'size' => $fileSize,
                            'mimetype' => $fileMymeType,
                            'slug' => $slug
                        ]
                    ]
                ];
                $this->transfer->create($data);
            }
            else {
                $data = [
                    'original_name' => $fileOriginalName,
                    'object_name' => $fileName,
                    'size' => $fileSize,
                    'mimetype' => $fileMymeType,
                    'slug' => $slug
                ];
                $this->transfer->addNewFile($transfer_id, $data);
            }

            // response
            $response = array('success'=> $success, 'file_name' => $fileName);
            return \Response::json($response);
        }
    }


    public function download($id)
    {
        $storeFolder = "downloads/";

        $identity = new IdentityService($_ENV['swiftendpoint']);

        // Init Utils and authenticate
        $objectStoreUtils = new ObjectStoreUtils($identity, $_ENV['swiftusername'], $_ENV['swiftpassword'], $_ENV['swifttenantname']);

        $transfer = $this->transfer->find($id);

        $zip =  $objectStoreUtils->download_transfer_files_as_zip($transfer,$storeFolder);

        $this->sendFile($zip);
    }



    /*
     *
     * Private Methods - Others
     *
     * */


    /**
     * Start a big file download on Laravel Framework 4.0 / 4.1
     * Source (originally for Laravel 3.*) : http://stackoverflow.com/questions/15942497/why-dont-large-files-download-easily-in-laravel
     * @param  string $path    Path to the big file
     * @param  string $name    Name of the file (used in Content-disposition header)
     * @param  array  $headers Some extra headers
     */
    protected function sendFile($path, $name = null, array $headers = array()){
        if (is_null($name)) $name = basename($path);

        $file = new Symfony\Component\HttpFoundation\File\File($path);
        $mime = $file->getMimeType();

        // Prepare the headers
        $headers = array_merge(array(
            'Content-Description'       => 'File Transfer',
            'Content-Type'              => $mime,
            'Content-Transfer-Encoding' => 'binary',
            'Expires'                   => 0,
            'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma'                    => 'public',
            'Content-Length'            => File::size($path),
            'Content-Disposition'	    => 'attachment; filename='.$name
        ), $headers);

        $response = new Symfony\Component\HttpFoundation\Response('', 200, $headers);

        // If there's a session we should save it now
        if (Config::get('session.driver') !== ''){
            Session::save();
        }

        session_write_close();
        if (ob_get_length()) ob_end_clean();

        $response->sendHeaders();

        // Read the file
        if ($file = fopen($path, 'rb')) {
            while(!feof($file) and (connection_status()==0)) {
                print(fread($file, 1024*8));
                flush();
            }
            fclose($file);
        }

        // Finish off, like Laravel would
        Event::fire('laravel.done', array($response));
        $response->send();

        exit;
    }
}