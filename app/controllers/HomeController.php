<?php

use OpenStack\Identity\v2\IdentityService;
use OpenStack\ObjectStore\v1\ObjectStorage;
use OpenStack\ObjectStore\v1\Resource\Object;

class HomeController extends BaseController {

    private $objectname;
    private $objectcontentlength;
    private $objectcontenttype;
    private $data;

    public function __construct()
    {
        $this->objectname = '';
        $this->objectcontentlength = '';
        $this->objectcontenttype = '';
        $this->data = '';
    }

	public function index()
	{
        // Create a new identity service object, and tell it where to
        // go to authenticate. This URL can be found in your console.
        $identity = new IdentityService('http://95.110.165.22:35357/v2.0');

        // You can authenticate with a username/password (IdentityService::authenticateAsUser()).
        // In either case you can get the info you need from the console.
        $username = 'demo';
        $password = '5f423b77';
        $tenantName = 'admin';

        // $token will be your authorization key when you connect to other
        // services. You can also get it from $identity->token().
        $token = $identity->authenticateAsUser($username, $password, null, $tenantName);

        // Get a listing of all of the services you currently have configured in
        // OpenStack.
        //$catalog = $identity->serviceCatalog();
        //$tenantName = $identity->tenantName();

        $storageList = $identity->serviceCatalog('object-store');
        $objectStorageUrl = $storageList[0]['endpoints'][0]['publicURL'];

        // Create a new ObjectStorage instance:
        $objectStore = new \OpenStack\ObjectStore\v1\ObjectStorage($token, $objectStorageUrl);

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
        $this->objectname = $object->name();
        $this->objectcontentlength = $object->contentLength();
        $this->objectcontenttype = $object->contentType();

        // Use stream for large objects
        $content = $object->stream(true);

        // Data containing file contents
        while(!feof($content)) {
            $this->data .= fread($content, 1024);
        }

        fclose($content);

		return View::make('hello')->with('downURL', URL::to('downloadfile'));
	}

    public function downloadfile()
    {
        header('Content-Description: File Transfer');
        header('Content-Type: '.$this->objectcontenttype);
        header('Content-disposition: attachment; filename='.$this->objectname);
        header('Content-Length: '.$this->objectcontentlength);
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Pragma: public');

        return View::make('downloadfile')->with('data', $this->data);
    }
}
