<?php namespace app\Lib\Services\Validators\Security;
 
use app\Lib\Services\Validators\Validator;

class LoginValidator extends Validator {
 
  /**
   * Validation rules
   */
  public static $rules = array(
    'username' => 'required',
    'password' => 'required'
  );
 
  // create custom validation messages ------------------
  public static $messages = array(
  		//'required' => 'The :attribute is really important.',
  		//'same' 	=> 'The :others must match.'
  		//'username.required' => 'Message'
  );
}
