<?php namespace Base\Lib\Storage\Security;
 
interface IUserRepository {
   
  public function all();
 
  public function find($id);
 
  public function create($input);
  public function update($id,$input);
  public function delete($id);
  public function resetPassword($id,$pass);
 
}