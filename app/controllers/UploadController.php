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

        // Get object service
        //$objectStore = $objectStoreUtils->getObjectStore();

        Session::put('objectStoreUtils', $objectStoreUtils);

        Session::forget('transfer_id');
        Session::forget('containerUrl');

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
            else
            {
                // Get object service
                $objectStore = $objectStoreUtils->getObjectStore();
                // Create and retrieve the container
                // To get the file AND delete the container when the file is downloaded
                $container = $objectStoreUtils->createAndOrRetrieveContainer($objectStore, $containerName);
                Session::put("containerUrl", $container->url());
            }

            // Upload file to swift
            //$success = $objectStoreUtils->uploadFile($container, $fileFullPath);
            $success = $objectStoreUtils->uploadFileChunks($containerName, $fileFullPath);

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
                    $newTransfer = $this->transfer->create($data);
                    // save new transfer in session
                    $transfer_id = $newTransfer->id;
                    Session::set('transfer_id', $transfer_id);

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
                $response = array('success' => $success, 'file_name' => $fileName, 'transfer_id' => $transfer_id);
            }
            else{
                $response = array('success' => $success);
            }
            return \Response::json($response);
        }
    }



    public function transferemail() {
        $allData = Input::all();

        $senderEmail = Input::get('from-email');
        $recipientEmailList = Input::get('recipient');

        $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';

        if(!empty($senderEmail) && count($recipientEmailList) > 0 && preg_match($pattern, $senderEmail) === 1) {
            //        $recipientEmailList = array_values($recipientEmailList);
            $transferid = Input::get('transfer_id');

            $transferData = $this->transfer->find($transferid);
            $fileExpirationDays = $_ENV["fileexpirationdays"];
            $srtToAddDays = " + " . $fileExpirationDays . " day";
            $expirationDate = date('j M, Y', strtotime($transferData->created_at . $srtToAddDays));

            $transferFiles = $transferData->files;

            $totalSize = 0;
            $fileNames = "";
            foreach ($transferFiles as $transferFile) {
                $totalSize += $transferFile->size;
                $fileNames .= $transferFile->original_name . "<br>";
            }

            $objectStoreUtils = Session::get('objectStoreUtils');

            $totalSize = $objectStoreUtils->byteFormat($totalSize);

            $transferMessage = Input::get('message');

            $downloadURL = url('/downloadTransfer/' . $transferData->unique_id);
            //        $downloadURL = $_SERVER['SERVER_NAME'] . "/downloadTransfer/" . $transferid . "/" . $transferData->unique_id;

            $recipientEmailListString = implode(';', $recipientEmailList);
            $data = [
                'expirationDate' => $expirationDate,
                'totalSize' => $totalSize,
                'fileNames' => $fileNames,
                'senderEmail' => $senderEmail,
                'recipientEmail' => $recipientEmailListString,
                'transferMessage' => $transferMessage,
                'downloadURL' => $downloadURL,
            ];


            if ($_ENV["emailsenabled"]) {
                Mail::send('emails.recipientConfirmation', $data, function ($message) use ($recipientEmailList, $senderEmail) {
                    $message->to($recipientEmailList, $recipientEmailList)->subject($senderEmail . " has sent you a file");
                });

                Mail::send('emails.senderConfirmation', $data, function ($message) use ($recipientEmailList, $senderEmail, $recipientEmailListString) {
                    $message->to($senderEmail, $senderEmail)->subject("Thank you - file sent to " . $recipientEmailListString);
                });
            }

            // Save emails related to this transfer
            $transferData->sender_email = $senderEmail;
            $transferData->recipient_email = $recipientEmailListString;
            $transferData->message = $transferMessage;
            $transferData->save();

            return Redirect::to('/')->with('message', 'Thank you!');
        }
    }
}