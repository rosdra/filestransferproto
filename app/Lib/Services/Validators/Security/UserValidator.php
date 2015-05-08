<?php namespace app\Lib\Services\Validators\Security;
 
use app\Lib\Services\Validators\Validator;

class UserValidator extends Validator {
 
  /**
   * Validation rules
   */
  public static $rules = array(
    'username' => 'required|unique:user,username,:id,id,deleted_at,NULL',
    'email' => 'required|email|unique:user,email,:id,id,deleted_at,NULL',
    'password' => 'required|min:6|confirmed',
    'password_confirmation' => 'required|min:6'
  );
 
  // create custom validation messages ------------------
  public static $messages = array(
  		//'required' => 'The :attribute is really important.',
  		//'same' 	=> 'The :others must match.'
  		//'username.required' => 'Message'
  );
}
