<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	//UserIdentity的父类
	// public function __construct($username,$password){
	// 	$this->username=$username;
	// 	$this->password=$password;
	// }
	//所以$this->app_name 变成了$this->username;
	private $_id;//重新定义id,不是Yii::app()->user->id显示用户的真实id,而不是用户名
	public function authenticate()
	{
		$userInfo=User::model()->find('app_name=:app_name',array(':app_name'=>$this->username));
		if($userInfo==NULL){
			$this->errorCode=self::ERROR_USERNAME_INVALID;
			return false;
		}
		if($userInfo->password !==md5($this->password)){
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
			return false;
		}
		
		$this->_id=$userInfo->id; //重新定义id
		$this->setState('password',$userInfo->password); //设置password,可通过Yii::app()->user->getState('password')直接调用
		$this->setState('real_name',$userInfo->real_name); 
		$this->errorCode=self::ERROR_NONE;
		return true;
	}

	public function getId(){
		return $this->_id;
	}
}