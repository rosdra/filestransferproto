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
            //$success = $objectStoreUtils->uploadFile($container, $fileFullPath);
            $success = $objectStoreUtils->uploadFileChunks($container,$objectStore, $fileFullPath);

            if($success == true) {
                $slug = Str::slug($fileOriginalName);
                if (!$slug) {
                    $slug = str_random(9);
                }

                //Save/Update container and file information into Database
                if ($transfer_id == null) {
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
                } else {
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
                $response = array('success' => $success, 'file_name' => $fileName);
            }
            else{
                $response = array('success' => $success);
            }
            return \Response::json($response);
        }
    }
}