<?php

class SiteController extends Controller
{
	
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	

	/**
	 * 用户登录
	 *@param String  app_name  用户名
	 *@param String  password   密码
	 *@param String  current_equipment 当前设备号
	 */
	public function actionLogin()
	{
		$app_name=isset($_REQUEST['app_name'])?$_REQUEST['app_name']:"";
		$password=isset($_REQUEST['password'])?$_REQUEST['password']:"";
		//当前手机设备号
		$current_equipment=isset($_REQUEST['current_equipment'])?$_REQUEST['current_equipment']:"";
		//当前手机ip
		$current_ip = $_SERVER['REMOTE_ADDR'];
		if(empty($app_name) || empty($password) || empty($current_equipment)){
			//P($_REQUEST);
			echo json_encode(array('status'=>'300','message'=>'app_name or password or current_equipment is empty'));
			Yii::app()->end();
		}
		
		$model=new LoginForm;
		$model->attributes=$_REQUEST;
		
		if($model->validate() && $model->login()){
			//在components文件夹下的UserIdentity.php里设置
			$appName=Yii::app()->user->name;
			$userId=Yii::app()->user->id;

			//插入当前登录信息,修改用户登录状态,获取用户的所有路由场景设备信息
			$user_model=User::model();		
			$user_bool=$user_model->getCInfo($userId,$current_ip,$current_equipment);
			if($user_bool){
				$result['user_id']=$userId;
				$result['app_name']=$appName;
				echo json_encode(array('status'=>'200','message'=>$result));
				Yii::app()->end();
			}else{
				echo json_encode(array('status'=>'500','message'=>"save  or  update  error"));
				Yii::app()->end();
			}
		}

		echo json_encode(array('status'=>'400','message'=>'用户名或密码错误'));
		Yii::app()->end();
	}

	/**
	 * 获取用户路由场景信息
	 *@param String  app_name  用户名
	 *@param String  password   密码
	 */
	public function actionGetARC()
	{
		$userId=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		if(empty($userId)){
			echo json_encode(array('status'=>'300','message'=>'user_id is empty'));
			Yii::app()->end();
		}
		
		//插入当前登录信息,修改用户登录状态,获取用户的所有路由场景设备信息
		$user_model=User::model();		
		$result['router']=$user_model->getAddInfo($userId);
		$reult['user_id']=$userId;
		$result['app_name']="";

		//获取广告位
		$advert_model=Advert::model();
		$advert_data=$advert_model->getAdvert();
		if(!$advert_data){
			$result['advert']=array();
		}else{
			$result['advert']=$advert_data;
		}

		//P($result);
		if(!empty($result)){
			echo json_encode(array('status'=>'200','message'=>$result));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'400','message'=>"no data or bad Internet"));
			Yii::app()->end();
		}

		echo json_encode(array('status'=>'300','message'=>'用户名或密码错误'));
		Yii::app()->end();
	}



	/**
	 * 用户注销
	 */
	public function actionLogout()
	{
		$userId=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		if(empty($userId)){
			echo json_encode(array('status'=>'300','message'=>'no user_id'));
			Yii::app()->end();
		}
		$user_model=User::model();
		$bool=$user_model->changeLastInfo($userId);
		if($bool){
			Yii::app()->user->logout();
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}
		echo json_encode(array('status'=>'500','message'=>'find error  or  update error'));
		Yii::app()->end();
	}

	/**
	 * 用户注册 判断用户名是否已存在
	 */
	public function actionRegister(){
		$app_name=isset($_REQUEST['app_name'])?$_REQUEST['app_name']:"";
		$password=isset($_REQUEST['password'])?$_REQUEST['password']:"";
		if(empty($app_name) || empty($password) ){
			echo json_encode(array('status'=>'300','message'=>'no password or app_name '));
			Yii::app()->end();
		}

		if(empty(Yii::app()->session['phone'])){
			echo json_encode(array('status'=>'500','message'=>'no session'));
			Yii::app()->end();
		}
		$phone=Yii::app()->session['phone'];

		$user_model=User::model();
		$bool=$user_model->register($app_name,$password,$phone);
		if(intval($bool)===200){
			echo json_encode(array('status'=>'200','message'=>'success'));
			Yii::app()->end();
		}

		if(intval($bool)===300){
			echo  json_encode(array('status'=>'400','message'=>'用户名已存在'));
			Yii::app()->end();
		}	

		if(intval($bool)===500){
			echo  json_encode(array('status'=>'500','message'=>'save error'));
			Yii::app()->end();
		}
	}


	/**
	 * 用户注册 验证码
	 */
	public function actionCode(){
		
		if(empty($_REQUEST['phone'])){
			echo json_encode(array('status'=>'300','message'=>'no_phone_number'));
			Yii::app()->end();
		}
		//手机号
		$telphone=$_REQUEST['phone'];
		//验证手机号是否已存在
		$exists=User::model()->exists("phone=:phone",array(':phone'=>$telphone));
		if($exists){
			echo json_encode(array('status'=>'400','message'=>'手机号已存在'));
	     		Yii::app()->end();
		}
		Yii::app()->session['phone']=$telphone;

		//获取验证码
		$code=verificationCode();
		Yii::app()->session['code']=$code;
		
	    	//短信内容 $message  
	    	$message="小悠智能家居验证码:".$code;
	    	$message=urlencode(mb_convert_encoding($message,"GBK","utf-8"));

	    	//短信网关
		$time=time();
	    	$password="Unest_sms_".$time."_topsky";
	    	$password=md5($password);
	    	$gateway="http://admin.sms9.net/houtai/sms.php?cpid=10995&password={$password}&channelid=14027&tele={$telphone}&msg={$message}&timestamp={$time}";
	    	//$gateway="http://admin.sms9.net/houtai/sms_ye.php?userid=10995&password={$password}&timestamp={$time}";
	    	//$result =xcurl($gateway);  
	    	$result=triggerRequest($gateway);
	     	if(!$result){
	     		unset(Yii::app()->session['code']);
	     		echo json_encode(array('status'=>'300','message'=>'message_send_fail'));
	     		Yii::app()->end();
	     	}
	

	     	echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
	     	Yii::app()->end();
	}

	/**
	 * 用户注册 验证验证码
	 */
	public function actionYCode(){
		$code=isset($_REQUEST['code'])?$_REQUEST['code']:"";
		if(intval(Yii::app()->session['code'])!=intval($code)){
			echo Yii::app()->session['code'];
			echo $code;
			echo json_encode(array('status'=>'300','message'=>'code not equal'));
			Yii::app()->end();
		}
		echo json_encode(array('status'=>'200','message'=>'code right'));
		Yii::app()->end();
	}
	
	
	/**
	 * 找回密码 验证码
	 */
	public function actionRECode(){
		
		if(empty($_REQUEST['phone']) ){
			echo json_encode(array('status'=>'300','message'=>'no_phone_number'));
			Yii::app()->end();
		}
		//手机号
		$telphone=$_REQUEST['phone'];
		//验证手机号是否已存在
		$exists=User::model()->exists("phone=:phone",array(':phone'=>$telphone));
		if(!$exists){
			echo json_encode(array('status'=>'400','message'=>'手机号不存在'));
	     		Yii::app()->end();
		}
		Yii::app()->session['change2']=array('phone'=>$telphone);

		//获取验证码
		$code=verificationCode();
		Yii::app()->session['change']=array('recode'=>$code);
		
	    	//短信内容 $message  
	    	$message="小悠智能家居验证码:".$code;
	    	$message=urlencode(mb_convert_encoding($message,"GBK","utf-8"));
	 
	    	//短信网关
	    	$time=time();
	    	$password="Unest_sms_".$time."_topsky";
	    	$password=md5($password);
	    	$gateway="http://admin.sms9.net/houtai/sms.php?cpid=10995&password={$password}&channelid=14027&tele={$telphone}&msg={$message}&timestamp={$time}";
	    	//$gateway="http://admin.sms9.net/houtai/sms_ye.php?userid=10995&password={$password}&timestamp={$time}";
	    	//$result =xcurl($gateway);  
	    	$result=triggerRequest($gateway);
	     	if(!$result){
	     		unset(Yii::app()->session['change']['recode']);
	     		echo json_encode(array('status'=>'300','message'=>'message_send_fail'));
	     		Yii::app()->end();
	     	}
	     	
	     	echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
	     	Yii::app()->end();
	}

	/**
	 * 找回密码 验证验证码
	 */
	public function actionYRECode(){
		$code=isset($_REQUEST['code'])?$_REQUEST['code']:"";
		if(intval(Yii::app()->session['change']['recode'])!=intval($code)){
			echo Yii::app()->session['change']['recode']."1";
			echo $code."2";
			echo json_encode(array('status'=>'300','message'=>'code not equal'));
			Yii::app()->end();
		}
		echo json_encode(array('status'=>'200','message'=>'code right'));
		Yii::app()->end();
	}

	/**
	 * 找回密码
	 */
	public function actionChangePassword(){
		$password=isset($_REQUEST['password'])?$_REQUEST['password']:"";
		if(empty($password)){
			echo json_encode(array('status'=>'300','message'=>'no user_id  or password'));
			Yii::app()->end();
		}
		$password=md5($password);
		$time=current_time();

		if(empty(Yii::app()->session['change2']['phone'])){
			echo json_encode(array('status'=>'500','message'=>'session error or attack'));
			Yii::app()->end();
		}
		$phone=Yii::app()->session['change2']['phone'];
		$user_model=User::model();
		$ud=$user_model->findByAttributes(array('phone'=>$phone));
		if(empty($ud)){
			echo json_encode(array('status'=>'500','messgae'=>'session  error'));
			Yii::app()->end();
		}
		$user_id=$ud->id;
		$bool=$user_model->updateByPK($user_id,array('password'=>$password,'mtime'=>$time));
		if($bool){
			unset(Yii::app()->session['change2']);
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'update error'));
			Yii::app()->end();
		}
	}

	

	/**
	 * 用户信息
	 */
	public function actionUserInfo(){
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		if(empty($user_id)){
			echo json_encode(array('status'=>'300','message'=>'no user_id'));
			Yii::app()->end();
		}
		$user_model=User::model();
		$user_data=$user_model->getInfo($user_id);
		if($user_data){
			echo json_encode(array('status'=>'200','message'=>$user_data));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>"find error"));
			Yii::app()->end();
		}
	}
       

	/**
	 * 修改密码
	 */
	public function  actionCP(){
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$password=isset($_REQUEST['password'])?$_REQUEST['password']:"";
		if(empty($user_id) || empty($password)){
			echo json_encode(array('status'=>'300','message'=>'no user_id or password'));
			Yii::app()->end();
		}

		$password=md5($password);
		$time=current_time();
		$bool=User::model()->updateByPK($user_id,array('password'=>$password,'mtime'=>$time));
		if($bool){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'Error'));
			Yii::app()->end();
		}
	}

	public function actionTest(){
	    $data=UserRouter::model()->find('id=:id',array(':id'=>68));
	    P(json_encode($data->router_login_password));
	}
	
	
}
