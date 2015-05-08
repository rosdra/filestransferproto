<?php namespace app\models\Security;

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class User extends \Eloquent implements UserInterface, RemindableInterface {

    use SoftDeletingTrait;
	protected $table = "user";
	protected $hidden = ["password"];


	protected $guarded = [
			"id", "created_at","updated_at"
	];
	protected $fillable = ['username' ,'email', 'password'];
	
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}
	
	public function getAuthPassword()
	{
		return $this->password;
	}
	
	public function getRememberToken()
	{
		return $this->remember_token;
	}
	
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}
	
	public function getRememberTokenName()
	{
		return "remember_token";
	}
	
	public function getReminderEmail()
	{
		return $this->email;
	}

}
