<?php namespace app\Lib\Services\Validators;
 
abstract class Validator {
 
  protected $input;
 
  protected $errors;
 
  public function __construct($input = NULL)
  {
    $this->input = $input ?: \Input::all();
  }
 
  public function passes($id = null)
  {
  	$replace = ($id != null) ? $id : NULL;
  	foreach (static::$rules as $key => $rule)
  	{
  		static::$rules[$key] = str_replace(':id', $replace, $rule);
  	}
  	
    $validation = \Validator::make($this->input, static::$rules,static::$messages);
 
    if($validation->passes()) return true;
     
    $this->errors = $validation->messages();
 
    return false;
  }
 
  public function getErrors()
  {
    return $this->errors;
  }
 
}