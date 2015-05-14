<?php
use OpenStack\Identity\v2\IdentityService;
use app\Lib\Storage\Transfers\ITransferRepository;
/**
 * Created by PhpStorm.
 * User: Victor Ortega
 * Date: 5/7/2015
 * Time: 5:37 PM
 */

class DownloadController extends BaseController {

    protected $transfer;
    protected static $STORE_FOLDER = 'downloads/';
    public function __construct(ITransferRepository $transfer)
    {
        $this->transfer = $transfer;
    }


    /*
     * Method that return the download view and receive the transfer unique_id as query string
     * */
    public function index($unique_id)
    {
        $transfer = $this->transfer->findByUniqueId($unique_id);
        $pid = uniqid("", true);
        //TODO return download view
        return View::make('download.index')->with(compact('transfer', 'pid'));
    }


    public function download($id,$pid = null)
    {
        $transfer = $this->transfer->find($id); // TODO change for findByUniqueId

        if($pid == null){
            $pid = uniqid("",true);
        }
        $progressFileName = static::$STORE_FOLDER . $pid.".txt";
        $progressFileName = public_path($progressFileName);

        $array = [
            "progress" => 0 ,
            "downloaded" => 0,
            "total" => ObjectStoreUtils::getTotalFileSize($transfer),
            "finished" => false
        ];
        $content = json_encode($array);
        $fp = fopen( $progressFileName, 'w' );
        fwrite( $fp, $content);
        fclose( $fp );


        $identity = new IdentityService($_ENV['swiftendpoint']);

        // Init Utils and authenticate
        $objectStoreUtils = new ObjectStoreUtils($identity, $_ENV['swiftusername'], $_ENV['swiftpassword'], $_ENV['swifttenantname']);

        $zip =  $objectStoreUtils->download_transfer_files_as_zip($transfer,static::$STORE_FOLDER,$progressFileName);

        // Download Email
        $fileExpirationDays = $_ENV["fileexpirationdays"];
        $srtToAddDays = " + " . $fileExpirationDays . " day";
        $expirationDate = date('j M, Y', strtotime($transfer->created_at . $srtToAddDays));

        $transferFiles = $transfer->files;

        $totalSize = 0;
        $fileNames = "";
        foreach($transferFiles as $transferFile) {
            $totalSize += $transferFile->size;
            $fileNames .= $transferFile->original_name . "<br>";
        }

        $totalSize = $objectStoreUtils->byteFormat($totalSize);

        $senderEmail = $transfer->sender_email;
        $recipientEmail = $transfer->recipient_email; // TODO parse multiple emails from input

        $transferMessage = $transfer->message;

        $downloadURL = $_SERVER['SERVER_NAME'] . "/download/" . $id . "/" . $transfer->unique_id;

        $data = [
            'expirationDate'  => $expirationDate,
            'totalSize'       => $totalSize,
            'fileNames'       => $fileNames,
            'senderEmail'     => $senderEmail,
            'recipientEmail'  => $recipientEmail,
            'transferMessage' => $transferMessage,
            'downloadURL'     => $downloadURL,
        ];

        if ($_ENV['emailsenabled']) {
            Mail::send('emails.downloadConfirmation', $data, function ($message) use ($recipientEmail, $senderEmail) {
                $message->to($senderEmail, $senderEmail)->subject("Download confirmation from " . $recipientEmail);
            });
        }

        return \Response::json(array("zip"=>url("/server")."/".basename($zip)));
    }


    public function progress($pid = null){

        if($pid == null){
            $pid = uniqid("",true);
        }
        $progressFileName = static::$STORE_FOLDER . $pid.".txt";
        $progressFileName = public_path($progressFileName);

        if(file_exists($progressFileName)) {
            $fp = fopen($progressFileName, 'r');
            $contents = fread($fp, filesize($progressFileName));
            fclose($fp);
            return \Response::json(json_decode($contents));
        }
        else {

            $array = [
                "progress" => 0 ,
                "downloaded" => 0,
                "total" => 0,
                "finished" => false
            ];
            $content = json_encode($array);
            $fp = fopen( $progressFileName, 'w' );
            fwrite( $fp, $content);
            fclose( $fp );

            return \Response::json($array);
        }
    }



    public function serveFile($fileName){
        $destination = static::$STORE_FOLDER . $fileName;
        $destination = public_path($destination);
        $this->sendFile($destination);
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