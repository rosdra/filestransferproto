<?php
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

            $tempFile = $_FILES['files']['tmp_name'];
            //$extension = preg_replace('/video\//', '', $file->getMimeType());
            $extension = (explode(".", $_FILES['files']['name']));
            $extension = end($extension);

            $fileName = uniqid() . "." . $extension;
            $targetFile = $storeFolder . $fileName;
            $fileFullPath = public_path($targetFile);
            /*$fileType = getFileType($tempFile);
            if(isSupportedFile($extension)){
                if($fileType == 2){
                    if(!FileConverter::isHTML5Playable($tempFile)){
                        // convert and save
                        $fileName = FileConverter::ConvertToPlayable($tempFile, public_path($storeFolder), basename($fileName, "." . $extension));
                        // take file name from converter
                        $targetFile = $storeFolder . $fileName;
                    }else{
                        // handle image file
                        $file->move($storeFolder, $fileName);
                    }
                }
                else{
                    return \Response::json( array('success' => false, 'message' => 'File is not a video'));
                }
            }
            else{
                return \Response::json( array('success' => false, 'message' => 'File type is not supported'));
            }*/

            // response
            $response = array('success'=> true, 'file_name' => $fileName);
            return \Response::json($response);
        }
    }
} 