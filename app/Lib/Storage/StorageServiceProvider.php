<?php namespace app\Lib\Storage;
 
use Illuminate\Support\ServiceProvider;
 
class StorageServiceProvider extends ServiceProvider {
 
  public function register()
  {
    $this->app->bind(
      'app\Lib\Storage\Security\IUserRepository',
      'app\Lib\Storage\Security\EloquentUserRepository'
    );    

    /* Examples
    $this->app->bind(
    	'Storage\Group\IGroupRepository',
    	'Storage\Group\EloquentGroupRepository'
    );
    
    $this->app->bind(
    	'Storage\Resource\ResourceRepository',
    	'Storage\Resource\EloquentResourceRepository'
    );*/
  }
 
}