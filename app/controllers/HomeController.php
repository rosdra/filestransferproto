<?php

use OpenStack\Identity\v2\IdentityService;
use OpenStack\ObjectStore\v1\ObjectStorage;
use OpenStack\ObjectStore\v1\Resource\Object;

class HomeController extends BaseController {

	public function index()
	{
        // Create a new identity service object, and tell it where to
        // go to authenticate. This URL can be found in your console.
        $identity = new IdentityService($_ENV['swiftendpoint']);

        // Init Utils and authenticate
        $objectStoreUtils = new ObjectStoreUtils($identity, $_ENV['swiftusername'], $_ENV['swiftpassword'], $_ENV['swifttenantname']);

        // Get object service
        $objectStore = $objectStoreUtils->getObjectStore();

        // Create and retrieve the container NOTE: has to be stored in Database
        // To get the file AND delete the container when the file is downloaded
        $container = $objectStoreUtils->createAndRetrieveContainer($objectStore);

        // File path (retrieved from the uploader component). TO DO: retrieve their physical address
        // NOTE: Filename has to be stored in database
        $fileArray = Array(
            0 => '/home/rosdra/Documents/laravel_commands.txt',
            1 => '/home/rosdra/Documents/laravel_commands2.txt'
        );

        // For every file from uploader, we send it to upload
        foreach($fileArray as $filepath){
            $objectStoreUtils->uploadFile($container, $filepath);
        }

        /****************************For download part************************************/
        // Dummy filename, should retrieve info from database
        $filename = 'laravel_commands.txt';

        // Get File from container
        $object = $container->object($filename);

        // get basic file data
        Session::put('objectname', $object->name());
        Session::put('objectcontentlength', $object->contentLength());
        Session::put('objectcontenttype', $object->contentType());

        // Use stream for large objects
        $content = $object->stream(true);

        // Data containing file contents (reading 1mb per iteration)
        $data = '';
        while(!feof($content)) {
            $data .= fread($content, 1024);
        }

        Session::put('data', $data);

        fclose($content);

        // Hack for local env. cambiar esto cuando estemos en despliegue
		return View::make('hello')->with('downURL', URL::to('index.php/downloadfile'));
	}

    public function downloadfile()
    {
        return Response::make(Session::get('data'), 200, array(
            'Content-Description'       => 'File Transfer',
            'Content-Type'              => Session::get('objectcontenttype'),
            'Content-Disposition'       => 'attachment; filename="' . Session::get('objectname') . '"',
            'Expires'                   => 0,
            'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma'                    => 'public',
            'Content-Length'            => Session::get('objectcontentlength')
        ));

        /* TO DO Clean session variables
        Session::forget('data');
        Session::forget('objectcontenttype');
        Session::forget('objectname');
        Session::forget('objectcontentlength');*/
    }
}
