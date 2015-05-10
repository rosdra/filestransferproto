<?php
use OpenStack\Identity\v2\IdentityService;

/**
 * Created by PhpStorm.
 * User: PHP_RAUL
 * Date: 5/7/2015
 * Time: 5:37 PM
 */

class UploadController extends BaseController {

    public function __construct()
    {

    }



    public function index()
    {
        // Create a new identity service object, and tell it where to
        // go to authenticate. This URL can be found in your console.
        $identity = new IdentityService($_ENV['swiftendpoint']);

        // Init Utils and authenticate
        $objectStoreUtils = new ObjectStoreUtils($identity, $_ENV['swiftusername'], $_ENV['swiftpassword'], $_ENV['swifttenantname']);

        Session::put('objectStoreUtils', $objectStoreUtils);

        $containerName = uniqid();
        Session::put('containerName', $containerName);

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
            $containerName = Session::get('containerName');

            // Get object service
            $objectStore = $objectStoreUtils->getObjectStore();

            // Create and retrieve the container
            // To get the file AND delete the container when the file is downloaded
            $container = $objectStoreUtils->createAndOrRetrieveContainer($objectStore, $containerName);

            // Upload file to swift
            $success = $objectStoreUtils->uploadFile($container, $fileFullPath);

            //$object = $container->object($fileName);

            //return \Response::json( array('success' => false, 'message' => 'File is not a video'));

            // response
            $response = array('success'=> $success, 'file_name' => $fileName);
            return \Response::json($response);
        }
    }
} 