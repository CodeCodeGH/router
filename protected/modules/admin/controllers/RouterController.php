<?php 
class  RouterController  extends  Controller{
	//路由列表
	public  function  actionShow(){
		$criteria=new CDbCriteria();
		if(isset($_POST['id'])){
			if(!empty($_POST['id'])){
				$id=$_POST['id'];
				$_GET['id']=$id;
				$criteria->condition='id=:id';
				$criteria->params=array(':id'=>$id);
			}else{
				$id="";
				unset($_GET['id']);
			}
			
		}else{
			if(!empty($_GET['id'])){
				$id=$_GET['id'];
				$criteria->condition='id=:id';
				$criteria->params=array(':id'=>$id);
			}	
		}
		$criteria->order='id ASC';
		$count=Router::model()->count($criteria);//计算总条数

		$pager=new CPagination($count);
		$pager->pageSize=12;
		$pager->applyLimit($criteria);

		$routerData=Router::model()->findAll($criteria);
		$data['routerData']=$routerData;
		$data['pager']=$pager;

		$this->render('show',array('routerData'=>$data['routerData'],'pager'=>$data['pager']));
		Yii::app()->end();
	}

	//添加路由
	public function  actionAddRouter(){
		if(empty($_POST['net_router_id'])  || empty($_POST['mac']) || empty($_POST['version']) || empty($_POST['model'])){
			Yii::app()->user->setFlash('addRouterInfo','数据不完整');
			$this->createUrl(array('router/show'));
			Yii::app()->end();
		}

		$hasRouter=Router::model()->exists('net_router_id=:net_router_id',array(':net_router_id'=>$_POST['net_router_id']));
		if($hasRouter){
			Yii::app()->user->setFlash('addRouterInfo','路由已存在');
			$this->redirect(array('router/show'));
			Yii::app()->end();
		}

		$time=current_time();
		$routerModel=new Router;
		$routerModel->net_router_id=$_POST['net_router_id'];
		$routerModel->mac=$_POST['mac'];
		$routerModel->version=$_POST['version'];
		$routerModel->model=$_POST['model'];
		$routerModel->ctime=$time;
		$routerModel->mtime=$time;
		if($routerModel->save()>0){
			Yii::app()->user->setFlash('addRouterInfo','SUCCESS');
			$this->redirect(array('router/show'));
			Yii::app()->end();
		}else{
			Yii::app()->user->setFlash('addRouterInfo','Save Failed');
			$this->redirect(array('router/show'));
			Yii::app()->end();
		}
	}

	//编辑查看路由
	public  function  actionRouterDetail(){
		$this->layout='/layouts/column3';
		//修改信息
		if(!empty($_POST['id'])){
			$id=$_POST['id'];
			if(!empty($_POST['net_router_id'])){
				$hasRouter=Router::model()->find('net_router_id=:net_router_id  AND  id!=:id',array(':net_router_id'=>$_POST['net_router_id'],':id'=>$id));
				if($hasRouter){
					Yii::app()->user->setFlash('routerDetailShow','路由已存在');
					$this->redirect(array('router/routerdetail','router_id'=>$id));
					Yii::app()->end();
				}
				$router['net_router_id']=$_POST['net_router_id'];
			}

			if(!empty($_POST['mac'])){
				$router['mac']=$_POST['mac'];
			}

			if(!empty($_POST['model'])){
				$router['model']=$_POST['model'];
			}

			if(!empty($_POST['version'])){
				$router['version']=$_POST['version'];
			}

			$router['mtime']=current_time();
			$bool=Router::model()->updateByPK($id,$router);
			if($bool){
				Yii::app()->user->setFlash('routerDetailShow','更新成功');
				$this->redirect(array('router/routerdetail','router_id'=>$id));
				Yii::app()->end();
			}else{
				Yii::app()->user->setFlash('routerDetailShow','更新失败');
				$this->redirect(array('router/routerdetail','router_id'=>$id));
				Yii::app()->end();
			}
		}


		//查看信息
		if(empty($_GET['router_id'])){
			Yii::app()->user->setFlash('addRouterInfo','Error');
			$this->redirect(array('router/show'));
			Yii::app()->end();
		}
		$router_id=$_GET['router_id'];
		$routerData=Router::model()->findByPK($router_id);
		if(empty($routerData)){
			Yii::app()->user->setFlash('addRouterInfo','Error');
			$this->redirect(array('router/show'));
			Yii::app()->end();
		}

		$this->render('routerdetail',array('routerData'=>$routerData));
		Yii::app()->end();
	}

	//获取路由下的设备信息
	public function actionGetEquip(){
		$router_id=isset($_REQUEST['router_id'])?$_REQUEST['router_id']:"";
		if(empty($router_id)){
			Yii::app()->user->setFlash('GetEquipInfo','Error');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}

		$criteria=new CDbCriteria();
		$criteria->order='id ASC';
		$criteria->condition='router_id=:router_id';
		$criteria->params=array(':router_id'=>$router_id);
		$count=Equipment::model()->count($criteria);//计算总条数

		$page=new CPagination($count);
		$page->pageSize=5;
		$page->applyLimit($criteria);

		$E_data=Equipment::model()->findAll($criteria);
		$this->render('getequip',array('E_data'=>$E_data,'pager'=>$page));
		Yii::app()->end();
	}
	
}