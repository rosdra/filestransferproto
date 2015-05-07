<?php

use OpenStack\Identity\v2\IdentityService;
use OpenStack\ObjectStore\v1\ObjectStorage;

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

    function generateUniqueId() {
        return uniqid();
    }

    function createAndRetrieveContainer(ObjectStorage $objectStore = null){
        // Generate a unique container name
        $containerName = uniqid();

        // Create the container for the file
        $objectStore->createContainer($containerName);

        // Retrieve the created container
        $container = $objectStore->container($containerName);

        return $container;
    }
}