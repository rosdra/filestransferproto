<?php namespace app\Lib\Storage\Security;
 
use app\models\Security\User;
 
class EloquentUserRepository implements IUserRepository {
 
  public function all()
  {
    return User::all();
  }
 
  public function find($id)
  {
    return User::find($id);
  }
 
  public function create($input)
  {
  	$u = new User;
  	
  	$u->username = $input["username"];
  	$u->email = $input["email"];
  	
  	$password = $input['password']; // password is form field
  	$hashed = \Hash::make($password);
  	$u->password = $hashed;
  	
    return $u->save();
  }
  
  public function update($id,$input)
  {
  	$u = $this->find($id);  	
  	$u->username = $input["username"];
  	$u->email = $input["email"];
  	$u->save();
  	
  	return $u;
  }
  
  public function resetPassword($id,$pass)
  {
  	$u = $this->find($id);  
  		
  	$password = $pass; // password is form field
  	$hashed = \Hash::make($password);
  	$u->password = $hashed;
  	
  	$u->save();
  	
  	return $u;
  }
  
  public function delete($id){

  	$u = $this->find($input);
  	return $u->delete();
  }
}