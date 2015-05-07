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

        // You can authenticate with a username/password (IdentityService::authenticateAsUser()).
        // In either case you can get the info you need from the console.
        $username = $_ENV['swiftusername'];
        $password = $_ENV['swiftpassword'];
        $tenantName = $_ENV['swifttenantname'];

        // Init Utils
        $objectStoreUtils = new ObjectStoreUtils($identity, $username, $password, $tenantName);

        // Get object service
        $objectStore = $objectStoreUtils->getObjectStore();

        //$objectStore->createContainer('Example');
        $container = $objectStore->container('Example');

        // File path
        $demofilepath = "/home/rosdra/Documents/laravel_commands.txt";
        $filename = basename($demofilepath);

        // get contents of file
        $filecontents = file_get_contents($demofilepath);

        // get file mime type
        $finfo = new finfo(FILEINFO_MIME);
        $type = $finfo->file($demofilepath);

        // Send file to save
        $localObject = new Object($filename, $filecontents, $type);
        $container->save($localObject);
        $object = $container->object($filename);

        /*printf("Name: %s \n", $object->name());
        printf("Size: %d \n", $object->contentLength());
        printf("Type: %s \n", $object->contentType());
        print $object->content() . PHP_EOL;*/

        // get basic file data
        Session::put('objectname', $object->name());
        Session::put('objectcontentlength', $object->contentLength());
        Session::put('objectcontenttype', $object->contentType());

        // Use stream for large objects
        $content = $object->stream(true);

        // Data containing file contents
        $data = '';
        while(!feof($content)) {
            $data .= fread($content, 1024);
        }

        Session::put('data', $data);

        fclose($content);

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
    }
}
