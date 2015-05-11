<?php namespace app\Lib\Storage;
 
use Illuminate\Support\ServiceProvider;

class StorageServiceProvider extends ServiceProvider {

  public function register()
  {
    $this->app->bind(
      'app\Lib\Storage\Security\IUserRepository',
      'app\Lib\Storage\Security\EloquentUserRepository'
    );


    $this->app->bind(
      'app\Lib\Storage\Transfers\ITransferRepository',
      'app\Lib\Storage\Transfers\EloquentTransferRepository'
    );

    /* Examples
    
    $this->app->bind(
    	'Storage\Resource\ResourceRepository',
    	'Storage\Resource\EloquentResourceRepository'
    );*/
  }
 
}