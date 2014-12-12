<?php

class RouterController extends Controller
{

	/**
	 *APP  添加路由
	 *@param int  user_id 用户id
	 *@param int  net_router_id 路由器端设置的路由id
	 *@param String alias  路由的别名
	 *@param String router_login_password 路由登录密码
	 */
	public function actionAddRouter(){
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$net_router_id=isset($_REQUEST['net_router_id'])?$_REQUEST['net_router_id']:"";
		$router_login_password=isset($_REQUEST['router_login_password'])?$_REQUEST['router_login_password']:"";
		$alias=isset($_REQUEST['alias'])?$_REQUEST['alias']:"";

		if(empty($user_id) || empty($router_login_password) || empty($net_router_id) ){
			echo json_encode(array('status'=>'300','message'=>'no  user_id  or net_router_id or router_login_password or alias'));
			Yii::app()->end();
		}
		if(empty($alias)){//没设置别名，默认路由器端的路由id
			$alias=$net_router_id;
		}
		
		$urouter_model=UserRouter::model();
		$bool=$urouter_model->addRouter($user_id,$net_router_id,$alias,$router_login_password);
		if(intval($bool)===200){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}
		if(intval($bool)===300){
			echo json_encode(array('status'=>'300','message'=>'路由已绑定'));
			Yii::app()->end();
		}
		if(intval($bool)===400){
			echo json_encode(array('status'=>'400','message'=>'路由不存在'));
			Yii::app()->end();
		}
		if(intval($bool)===500){
			echo json_encode(array('status'=>'500','message'=>'save error'));
			Yii::app()->end();
		}
	}

	/**
	 * APP 删除路由
	 */
	public function actionDelRouter(){
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$router_id=isset($_REQUEST['router_id'])?$_REQUEST['router_id']:"";
		if(empty($user_id) || empty($router_id) ){
			echo json_encode(array('status'=>'300','message'=>'no  user_id  or router_id'));
			Yii::app()->end();
		}
		$router_model=Router::model();
		$bool=$router_model->deleteRouter($user_id,$router_id);
		if($bool){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'delete error'));
			Yii::app()->end();
		}
	}

	/**
	 *APP  修改路由别名,自定义 
	 */
	public function actionChangeName(){
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$router_id=isset($_REQUEST['router_id'])?$_REQUEST['router_id']:"";
		$alias=isset($_REQUEST['alias'])?$_REQUEST['alias']:"";
		if(empty($user_id) || empty($router_id) || empty($alias)){
			echo json_encode(array('status'=>'300','message'=>'no  user_id  or router_id or alias'));
			Yii::app()->end();
		}
		$UR_model=UserRouter::model();
		$bool=$UR_model->changeName($user_id,$router_id,$alias);
		if($bool){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'update error'));
			Yii::app()->end();
		}
	}

	/**
	 * APP 修改路由别名,一个路由一个别名
	 */
	public function actionChangeAlias(){
		$router_id=isset($_REQUEST['router_id'])?$_REQUEST['router_id']:"";
		$alias=isset($_REQUEST['alias'])?$_REQUEST['alias']:"";
		if(empty($router_id) || empty($alias)){
			echo json_encode(array('status'=>'300','message'=>'no router_id or alias'));
			Yii::app()->end();
		}
		$router_model=Router::model();
		$id=$router_model->cname($router_id,$alias);
		if($id){
			echo json_encode(array('status'=>'200','message'=>$id));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'save error'));
			Yii::app()->end();
		}
	}	
	
	/**
	 *获取路由下所有设备（虚拟）
	 */	
	public function actionGetAXE(){
		$router_id=isset($_REQUEST['router_id'])?$_REQUEST['router_id']:"";
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		if(empty($router_id) || empty($user_id)){
			echo json_encode(array('status'=>'300','message'=>'no router_id user_id'));
			Yii::app()->end();
		}

		$EM=Equipment::model();
		$ED=$EM->findAllByAttributes(array('router_id'=>$router_id));
		if(empty($ED)){
			echo json_encode(array('status'=>'400','message'=>'无设备'));
			Yii::app()->end();
		}
		
		$arr=array();
		foreach($ED as $val){
			$equipment=array();
			$equipment_id=$val->id;
			$router_equipment_id=$val->router_equipment_id;
			$type=substr($router_equipment_id,2,2);
			if($type=="02" || $type=="03"){
				$control_data=Control::model()->findAllByAttributes(array('user_id'=>$user_id,'equipment_id'=>$equipment_id));
				if(!empty($control_data)){
					foreach($control_data as  $control_val){
						$equipment['id']=$control_val->id;
						$equipment['name']=$control_val->name;
						$equipment['type']=$type;
						$arr[]=$equipment;
					}	
				}
			}else{
				$equipment['id']=$equipment_id;
				$UE_data=UserEquipment::model()->findByAttributes(array('user_id'=>$user_id,'equipment_id'=>$equipment_id));
				if(empty($UE_data)){
					$equipment['name']=$router_equipment_id;
				}else{
					$equipment['name']=$UE_data->name;
				}
				$equipment['type']=$type;
				$arr[]=$equipment;
			}
		}
		echo json_encode(array('status'=>'200','message'=>$arr));
		Yii::app()->end();
	}
	
	//修改路由密码
	public function  actionCRP(){
		$net_router_id=isset($_REQUEST['net_router_id'])?$_REQUEST['net_router_id']:"";
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$password=isset($_REQUEST['password'])?$_REQUEST['password']:"";
		if(empty($net_router_id) || empty($user_id) || empty($password)){
			echo json_encode(array('status'=>'300','message'=>'request error'));
			Yii::app()->end();
		}
		$time=current_time();

		$rdata=Router::model()->findByAttributes(array('net_router_id'=>$net_router_id));
		if(empty($rdata)){
			echo json_encode(array('status'=>'500','message'=>'no data'));
			Yii::app()->end();
		}else{
			$router_id=$rdata->id;
		}
		$bool=UserRouter::model()->updateAll(array('router_login_password'=>$password,'mtime'=>$time),'router_id=:router_id  AND  user_id=:user_id',array(':router_id'=>$router_id,':user_id'=>$user_id));
		if($bool){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'error'));
			Yii::app()->end();
		}
	}

}
