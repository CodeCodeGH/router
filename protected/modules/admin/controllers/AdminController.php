<?php

class AdminController extends Controller
{
	//显示app用户
	public function actionApp()
	{
		if(isset($_POST['phone'])){
			if(!empty($_POST['phone'])){
				$phone=$_POST['phone'];
				$_GET['phone']=$phone;
			}else{
				$phone="";
				unset($_GET['phone']);
			}
			
		}else{
			if(!empty($_GET['phone'])){
				$phone=$_GET['phone'];
			}else{
				$phone="";
			}	
		}
		$userModel=User::model();
		$data=$userModel->adminUserList($phone);
		$this->render('app',array('userData'=>$data['userData'],'pager'=>$data['pager']));
		Yii::app()->end();
	}
	
	//增加app用户
	public function actionAddApp(){
		if(empty($_POST['app_name']) || empty($_POST['phone']) || empty($_POST['password'])){
			Yii::app()->user->setFlash('addAppInfo','数据不完整');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}

		$hasPhone=User::model()->exists('phone=:phone',array(':phone'=>$_POST['phone']));
		if($hasPhone){
			Yii::app()->user->setFlash('addAppInfo','手机已存在');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}

		$hasUser=User::model()->exists('app_name=:app_name',array(':app_name'=>$_POST['app_name']));
		if($hasUser){
			Yii::app()->user->setFlash('addAppInfo','用户已存在');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}
		
		$time=current_time();
		$userModel=new User;
		$userModel->app_name=$_POST['app_name'];
		$userModel->password=md5($_POST['password']);
		$userModel->phone=$_POST['phone'];
		if(!empty($_POST['real_name'])){
			$userModel->real_name=$_POST['real_name'];
		}
		$userModel->ctime=$time;
		$userModel->mtime=$time;

		if($userModel->save()>0){
			Yii::app()->user->setFlash('addAppInfo','SUCCESS');
			$this->redirect(array('admin/app'));
			Yii::app()->end();
		}else{
			Yii::app()->user->setFlash('addAppInfo','Save Failed');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}
	}

	//查看编辑app用户信息
	public  function  actionAppDetail(){
		$this->layout='/layouts/column3';
		//修改信息
		if(!empty($_POST['id'])){
			$id=$_POST['id'];
			if(!empty($_POST['app_name'])){
				$hasAppName=User::model()->find('app_name=:app_name  AND  id!=:id',array(':app_name'=>$_POST['app_name'],':id'=>$id));
				if($hasAppName){
					Yii::app()->user->setFlash('appUserShow','用户已存在');
					$this->redirect(array('admin/appdetail','user_id'=>$id));
					Yii::app()->end();
				}
				$user['app_name']=$_POST['app_name'];
			}

			if(!empty($_POST['phone'])){
				$hasPhone=User::model()->find('phone=:phone  AND  id!=:id',array(':phone'=>$_POST['phone'],':id'=>$id));
				if($hasPhone){
					Yii::app()->user->setFlash('appUserShow','手机已存在');
					$this->redirect(array('admin/appdetail','user_id'=>$id));
					Yii::app()->end();
				}
				$user['phone']=$_POST['phone'];
			}

			if(!empty($_POST['password'])){
				$user['password']=md5($_POST['password']);
			}

			if(!empty($_POST['real_name'])){
				$user['real_name']=$_POST['real_name'];
			}

			$user['mtime']=current_time();
			$bool=User::model()->updateByPK($id,$user);
			if($bool){
				Yii::app()->user->setFlash('appUserShow','更新成功');
				$this->redirect(array('admin/appdetail','user_id'=>$id));
				Yii::app()->end();
			}else{
				Yii::app()->user->setFlash('appUserShow','更新失败');
				$this->redirect(array('admin/appdetail','user_id'=>$id));
				Yii::app()->end();
			}
		}


		//查看信息
		if(empty($_GET['user_id'])){
			Yii::app()->user->setFlash('addAppInfo','Error');
			$this->redirect(array('admin/app'));
			Yii::app()->end();
		}
		$user_id=$_GET['user_id'];
		$userData=User::model()->findByPK($user_id);
		if(empty($userData)){
			Yii::app()->user->setFlash('addAppInfo','Error');
			$this->redirect(array('admin/app'));
			Yii::app()->end();
		}

		$this->render('appdetail',array('userData'=>$userData));
		Yii::app()->end();
	}


	//显示系统用户
	public function actionSystem(){
		$adminModel=Admin::model();
		$data=$adminModel->adminList();
		$this->render('system',array('adminData'=>$data['adminData'],'pager'=>$data['pager']));
		Yii::app()->end();
	}

	//添加管理员
	public function actionAddSystem(){
		if(empty($_POST['name']) || empty($_POST['phone']) || empty($_POST['password'])){
			Yii::app()->user->setFlash('addAdminInfo','数据不完整');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}


		$hasAdmin=Admin::model()->exists('name=:name',array(':name'=>$_POST['name']));
		if($hasAdmin){
			Yii::app()->user->setFlash('addAdminInfo','用户已存在');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}
		
		$time=current_time();
		$adminModel=new Admin;
		$adminModel->name=$_POST['name'];
		$adminModel->password=md5($_POST['password']);
		$adminModel->phone=$_POST['phone'];
		if(!empty($_POST['real_name'])){
			$adminModel->real_name=$_POST['real_name'];
		}
		$adminModel->ctime=$time;
		$adminModel->mtime=$time;

		if($adminModel->save()>0){
			Yii::app()->user->setFlash('addAppInfo','SUCCESS');
			$this->redirect(array('admin/system'));
			Yii::app()->end();
		}else{
			Yii::app()->user->setFlash('addAdminInfo','Save Failed');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}
	}

	//查看编辑管理员信息
	public  function  actionAdmindetail(){
		//修改信息
		if(!empty($_POST['id'])){
			$id=$_POST['id'];
			if(!empty($_POST['name'])){
				$hasAdminName=Admin::model()->find('name=:name  AND  id!=:id',array(':name'=>$_POST['name'],':id'=>$id));
				if($hasAdminName){
					Yii::app()->user->setFlash('systemUserShow','管理员已存在');
					$this->redirect(array('admin/admindetail','admin_id'=>$id));
					Yii::app()->end();
				}
				$admin['name']=$_POST['name'];
			}

			if(!empty($_POST['phone'])){
				$admin['phone']=$_POST['phone'];
			}

			if(!empty($_POST['password'])){
				$admin['password']=md5($_POST['password']);
			}

			if(!empty($_POST['real_name'])){
				$admin['real_name']=$_POST['real_name'];
			}

			$admin['mtime']=current_time();
			$bool=Admin::model()->updateByPK($id,$admin);
			if($bool){
				Yii::app()->user->setFlash('systemUserShow','更新成功');
				$this->redirect(array('admin/admindetail','admin_id'=>$id));
				Yii::app()->end();
			}else{
				Yii::app()->user->setFlash('systemUserShow','更新失败');
				$this->redirect(array('admin/admindetail','admin_id'=>$id));
				Yii::app()->end();
			}
		}


		//查看信息
		if(empty($_GET['admin_id'])){
			Yii::app()->user->setFlash('addAdminInfo','Error');
			$this->redirect(array('admin/system'));
			Yii::app()->end();
		}
		$admin_id=$_GET['admin_id'];
		$adminData=Admin::model()->findByPK($admin_id);
		if(empty($adminData)){
			Yii::app()->user->setFlash('addAdminInfo','Error');
			$this->redirect(array('admin/system'));
			Yii::app()->end();
		}

		$this->render('admindetail',array('adminData'=>$adminData));
		Yii::app()->end();
	}

	//查看某个用户的路由信息
	public  function  actionUR(){
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		if(empty($user_id)){
			Yii::app()->user->setFlash('URInfo','Error');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}

		$routerData=UserRouter::model()->findRouter($user_id);
		$this->layout='/layouts/column3';
		$this->render('ur',array('routerData'=>$routerData));
		Yii::app()->end();
	}

	//编辑路由别名和密码
	public function actionEditRouter(){
		$UR_id=$_REQUEST['UR_id']?$_REQUEST['UR_id']:"";
		$alias=$_REQUEST['alias']?$_REQUEST['alias']:"";
		$password=$_REQUEST['password']?$_REQUEST['password']:"";
		if(empty($UR_id) || empty($alias) || empty($password)){
			Yii::app()->user->setFlash('URInfo','Error');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}

		if(!is_numeric($UR_id)){
			Yii::app()->user->setFlash('URInfo','Error');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}

		$bool=UserRouter::model()->exists('id=:id',array(':id'=>$UR_id));
		if(!$bool){
			Yii::app()->user->setFlash('URInfo','Error');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}

		$time=current_time();
		UserRouter::model()->updateByPK($UR_id,array('alias'=>$alias,'router_login_password'=>$password,'mtime'=>$time));
		Yii::app()->user->setFlash('URInfo','SUCCESS');
		$this->redirect(Yii::app()->request->urlReferrer);
		Yii::app()->end();
	}

	//解除绑定路由
	public function  actionDelRouter(){
		$delId=isset($_REQUEST['delId'])?$_REQUEST['delId']:"";
		if(empty($delId)){
			Yii::app()->user->setFlash('URInfo','Error');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}

		$UR_data=UserRouter::model()->findByPK($delId);
		if(empty($UR_data) ){
			Yii::app()->user->setFlash('URInfo','Error');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}
		$user_id=$UR_data->user_id;
		$router_id=$UR_data->router_id;
		$router_model=Router::model();
		$bool=$router_model->deleteRouter($user_id,$router_id);
		if($bool){
			Yii::app()->user->setFlash('URInfo','SUCCESS');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}else{
			Yii::app()->user->setFlash('URInfo','Error');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}

		
	}

	//查看用户场景信息
	public function actionScene(){
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$router_id=isset($_REQUEST['router_id'])?$_REQUEST['router_id']:"";
		if(empty($user_id) || empty($router_id)){
			Yii::app()->user->setFlash('SceneInfo','Error');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}

		$sceneData=Scene::model()->findAll('user_id=:user_id AND router_id=:router_id',array(':user_id'=>$user_id,':router_id'=>$router_id));
		$this->render('scene',array('sceneData'=>$sceneData));
		Yii::app()->end();
	}

	//场景下的虚拟设备信息
	public function actionSE(){
		$scene_id=isset($_REQUEST['scene_id'])?$_REQUEST['scene_id']:0;
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		if(empty($user_id) || empty($scene_id)){
			Yii::app()->user->setFlash('SEInfo','Error');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}

		$SE_model=SceneEquipment::model();
		$data=$SE_model->getAllEquipment($scene_id,$user_id);
		$this->render('se',array('data'=>$data));
		Yii::app()->end();
	}


}