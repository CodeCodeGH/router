<?php

class DefaultController extends Controller
{

	//登录
	public function actionLogin()
	{
		$adminModel=new Admin('search');
		if(!empty($_POST['Admin'])){
			$adminModel->attributes=$_POST['Admin'];
			if($adminModel->validate()){
				//验证用户名密码是否存在
				$name=$adminModel->name;
				$password=md5($adminModel->password);
				$bool=$adminModel->identifityUser($name,$password);
				if($bool){
					$this->redirect(array('default/index'));
					Yii::app()->end();
				}
			}
		}
		$this->layout='/layouts/column1';
		$this->render('login',array('adminModel'=>$adminModel));
	}

	//验证码
	public function actions(){
		return array(
				'captcha'=>array(
					'class'=>'system.web.widgets.captcha.CCaptchaAction',
					'height'=>'50',
					'width'=>'80',
					'minLength'=>4,
					'maxLength'=>4
				)
		);
	}

	//退出
	public function actionLogout(){
		unset( Yii::app()->session['adminUserName']);
		unset(Yii::app()->session['adminUserId']);
		$this->redirect(array('default/login'));
		Yii::app()->end();
	}

	//后台首页
	public function actionIndex(){
		$this->layout="/layouts/column3";
		$this->render('index');
		Yii::app()->end();
	}



}