<?php

class EquipmentController extends Controller
{
	/**
	 * 获取场景下的设备信息
	 */
	public function actionShow(){
		$scene_id=isset($_REQUEST['scene_id'])?$_REQUEST['scene_id']:0;
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		if(empty($user_id) || empty($scene_id)){
			echo json_encode(array('status'=>'300','message'=>'no user_id or scene_id'));
			Yii::app()->end();
		}

		$SE_model=SceneEquipment::model();
		$data=$SE_model->getAllEquipment($scene_id,$user_id);
		//P($data);
		if(intval($data)===400){
			echo json_encode(array('status'=>'400','message'=>'暂无数据'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'200','message'=>$data));
			Yii::app()->end();
		}	
	}

	
	/**
	 * 操作设备  
	 *@param  Int  category_id   1电灯 2插座 3空调遥控器
	 *@param  Int  operation      1开 2关闭
	 */
	public function  actionOperation(){
		$router_id=isset($_REQUEST['router_id'])?$_REQUEST['router_id']:"";
		$equipment_id=isset($_REQUEST['equipment_id'])?$_REQUEST['equipment_id']:"";
		$operation=isset($_REQUEST['operation'])?$_REQUEST['operation']:"default";
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$operation_type=isset($_REQUEST['operation_type'])?$_REQUEST['operation_type']:"";
		$type=isset($_REQUEST['type'])?$_REQUEST['type']:"";
		$equip_status=isset($_REQUEST['equip_status'])?$_REQUEST['equip_status']:"";
		
		if(empty($router_id) || empty($equipment_id)  || empty($operation) || empty($user_id) || empty($operation_type) || empty($type) || empty($equip_status)){
			echo json_encode(array('status'=>'300','message'=>'no $router_id $equipment_id $user_id $operation'));
			Yii::app()->end();
		}
		 if(empty($_REQUEST['operation'])){
                        		$operation="default";
               	 }

		$r_data=Router::model()->findByPK($router_id);
		if(empty($r_data)){
			echo json_encode(array('status'=>'500','message'=>"no router"));
			Yii::app()->end();
		}
		$r_time=$r_data->access_time;
		$net_router_id=$r_data->net_router_id;
		$access_time=strtotime($r_time);
		$time=time();
		$cha=$time-$access_time;
		if(intval($cha)>=1000000000000000000000000000000000000000000000000000000){
			Temp::model()->deleteAll('net_router_id=:net_router_id  AND  status=:status',array(':net_router_id'=>$net_router_id,':status'=>'0'));
			echo json_encode(array('status'=>'400','message'=>"路由不在线"));
			Yii::app()->end();
		}
		

		$temp_model=Temp::model();
		$bool=$temp_model->addOperation($router_id,$equipment_id,$operation,$operation_type,$user_id,$type,$equip_status);
		if($bool){
			echo json_encode(array('status'=>'200','message'=>"SUCCESS"));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>"save  or  update error"));
			Yii::app()->end();
		}
	}

	/**
	 *发送更新所有设备指令
	 */
	public function actionUpdateAllS(){
		$router_id=isset($_REQUEST['router_id'])?$_REQUEST['router_id']:"";
		$operation=isset($_REQUEST['operation'])?$_REQUEST['operation']:"default";
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$operation_type=isset($_REQUEST['operation_type'])?$_REQUEST['operation_type']:"";

		if(empty($router_id)  || empty($operation) || empty($user_id) || empty($operation_type)){
			echo json_encode(array('status'=>'300','message'=>'request ERROR'));
			Yii::app()->end();
		}
		 if(empty($_REQUEST['operation'])){
                        		$operation="default";
               	 }

		$r_data=Router::model()->findByPK($router_id);
		if(empty($r_data)){
			echo json_encode(array('status'=>'404','message'=>"no router"));
			Yii::app()->end();
		}
		$r_time=$r_data->access_time;
		$net_router_id=$r_data->net_router_id;
		$access_time=strtotime($r_time);
		$time=time();
		$cha=$time-$access_time;
		if(intval($cha)>=10000000000000000000000000000000000000000000){
			Temp::model()->deleteAll('net_router_id=:net_router_id  AND  status=:status',array(':net_router_id'=>$net_router_id,':status'=>'0'));
			echo json_encode(array('status'=>'400','message'=>"路由不在线"));
			Yii::app()->end();
		}

		$new_model=new Temp;
		if($operation!="default"){
		 	$new_model->operation=$operation;
		}
		$new_model->operation_type="deviceAllNewStatus";
		$new_model->user_id=$user_id;
		$current_time=current_time();
		$new_model->ctime=$current_time;
		$new_model->mtime=$current_time;
		$new_model->net_router_id=$net_router_id;
		if($new_model->save() > 0){
			$id=$new_model->attributes['id'];
			echo json_encode(array('status'=>'200','message'=>$id));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>"save error"));
			Yii::app()->end();
		}
		
	}

	/**
	 * 根据id查询temp表的指令执行状态
	 */
	public function actionCheckStatus(){
		$temp_id=isset($_REQUEST['id'])?$_REQUEST['id']:"";
		if(empty($temp_id)){
			echo json_encode(array('status'=>'300','message'=>'request ERROR'));
			Yii::app()->end();
		}
		$temp_data=Temp::model()->findByPK($temp_id);
		if(empty($temp_data)){
			echo json_encode(array('status'=>'500','message'=>'no data'));
			Yii::app()->end();
		}
		$status=$temp_data->status;
		if(intval($status)==0 || intval($status)==3){
			echo json_encode(array('status'=>'401','message'=>'wait'));
			Yii::app()->end();
		}elseif(intval($status)==2){
			echo json_encode(array('status'=>'402','message'=>'try again'));
			Yii::app()->end();
		}elseif(intval($status)==1){
			echo json_encode(array('status'=>'200','message'=>'success'));
			Yii::app()->end();
		}
	}	

	/**
	 * 移动设备位置
	 *@param  json_object   position
	 */
	public function  actionChangePosition(){
		// $_REQUEST['position']=array(
		// 	array('scene_id'=>1,'equipment_id'=>1,'position'=>2),
		// 	array('scene_id'=>2,'equipment_id'=>2,'position'=>1),
		// );
		// $_REQUEST['position']=json_encode($_REQUEST['position']);
		// echo $_REQUEST['position'];
		if(empty($_REQUEST['position'])){
			echo json_encode(array('status'=>'300','message'=>'no position'));
			Yii::app()->end();
		}
		$position=json_decode($_REQUEST['position']);
		$SE_model=SceneEquipment::model();
		$bool=$SE_model->changePosition($position);
		if($bool){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'update error'));
			Yii::app()->end();
		}

	}

	/**
	 * 修改设备的名字(一个设备一个名字)
	 * @param int  equipment_id 数据库中的设备id
	 */
	public function actionChangeEName(){
		$equipment_id=isset($_REQUEST['equipment_id'])?$_REQUEST['equipment_id']:"";
		$name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
		if(empty($equipment_id) || empty($name) ){
			echo json_encode(array('status'=>'300','message'=>'no equipment_id or name'));
			Yii::app()->end();
		}
		$time=current_time();
		$equipment_model=Equipment::model();
		$bool=$equipment_model->updateByPK($equipment_id,array('name'=>$name,'mtime'=>$time));
		if($bool){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'update error'));
			Yii::app()->end();
		}
	}

	/**
	 * 修改物理设备的名字(用户自定义)
	 * @param int  equipment_id 数据库中的设备id
	 */
	public function actionCPEName(){
		$equipment_id=isset($_REQUEST['equipment_id'])?$_REQUEST['equipment_id']:"";
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
		if(empty($equipment_id) || empty($name) || empty($user_id)){
			echo json_encode(array('status'=>'300','message'=>'no equipment_id or name or user_id'));
			Yii::app()->end();
		}
	
		$UE_model=UserEquipment::model();
		$bool=$UE_model->changeName($user_id,$equipment_id,$name);
		if($bool){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'update error'));
			Yii::app()->end();
		}
	}


	/**
	 * 修改虚拟遥控器设备的名字
	 */
	public function actionCXEName(){
		$equipment_id=isset($_REQUEST['equipment_id'])?$_REQUEST['equipment_id']:"";
		$name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
		if(empty($equipment_id) || empty($name) ){
			echo json_encode(array('status'=>'300','message'=>'no equipment_id or name or user_id'));
			Yii::app()->end();
		}
		$time=current_time();
		$bool=Control::model()->updateByPK($equipment_id,array('name'=>$name,'mtime'=>$time));
		if($bool){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'update error'));
			Yii::app()->end();
		}
	}

	/**
	 * App 单情景 获取当前路由下没有场景的设备信息
	 */
	public function  actionGetNoScene(){
		$router_id=isset($_REQUEST['router_id'])?$_REQUEST['router_id']:"";
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		if(empty($router_id) || empty($user_id)){
			echo json_encode(array('status'=>'300','message'=>'no router_id or user_id'));
			Yii::app()->end();
		}
		$equipment_model=Equipment::model();
		$bool=$equipment_model->noScene($router_id,$user_id);
		if($bool){
			//P($bool);
			echo json_encode(array('status'=>'200','message'=>$bool));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'400','message'=>"no data"));
			Yii::app()->end();
		}
	}



	/**
	 *  App 多情景 获取当前路由下所有设备信息
	 */
	public function actionGetAllEquipment(){
		$router_id=isset($_REQUEST['router_id'])?$_REQUEST['router_id']:"";
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		if(empty($router_id) || empty($user_id)){
			echo json_encode(array('status'=>'300','message'=>'no router_id or user_id'));
			Yii::app()->end();
		}
		$EM=Equipment::model();
		$data=$EM->AllEquipment($router_id,$user_id);
		if($data){
			echo json_encode(array('status'=>'200','message'=>$data));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'400','message'=>"no data"));
			Yii::app()->end();
		}
	}

	/**
	 * App添加设备 
	 */
	public function actionAE(){
		/*		
		$_REQUEST['data']=array(
			'scene_id'=>'118',
			'info'=>array(
				array('equipment_id'=>'1','type'=>'02'),
				array('equipment_id'=>'48','type'=>'07'),
				),
			);
		$_REQUEST['data']=json_encode($_REQUEST['data']);
		//echo $_REQUEST['data'];
		//Yii::app()->end();
		*/
		if(empty($_REQUEST['data'])){
			echo json_encode(array('status'=>'300','message'=>'no data'));
			Yii::app()->end();
		}
		$data=json_decode($_REQUEST['data']);

		$equipment_model=Equipment::model();
		$bool=$equipment_model->addAE($data);
		if($bool){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'ERROR'));
			Yii::app()->end();
		}
	}

	/**
	 * App 删除设备
	 */
	public function actionDE(){
		$equipment_id=isset($_REQUEST['equipment_id'])?$_REQUEST['equipment_id']:"";
		$del_type=isset($_REQUEST['del_type'])?$_REQUEST['del_type']:"";
		if(empty($equipment_id) ||  empty($del_type)){
			echo json_encode(array('status'=>'300','message'=>'no  equipment_id '));
			Yii::app()->end();
		}
		$EM=Equipment::model();
		$id=$EM->delERelation($equipment_id,$del_type);
		if($id){
			echo json_encode(array('status'=>'200','message'=>"SUCCESS"));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'save error'));
			Yii::app()->end();
		}
	}

	/**
	 * 获取某一个设备状态
	 */
	public function actionGetOneStatus(){
		$equipment_id=isset($_REQUEST['equipment_id'])?$_REQUEST['equipment_id']:"";
		$type=isset($_REQUEST['type'])?$_REQUEST['type']:"";
		if(empty($equipment_id) || empty($type)){
			echo json_encode(array('status'=>'300','message'=>'no equipment_id'));
			Yii::app()->end();
		}
		//虚拟遥控器
		$equipment=array();
		if($type=="02" || $type=="03"){
			$control_data=Control::model()->findByPK($equipment_id);
			if(!empty($control_data)){
				$equipment['id']=$control_data->id;
				$equipment['name']=$control_data->name;
				//虚拟遥控器对应物理遥控器的信息
				$equipment['equipment_id']=$control_data->equipment_id;
				$xe_data=Equipment::model()->findByPK($equipment['equipment_id']);
				if(!empty($xe_data)){
					$equipment['router_equipment_id']=$xe_data->router_equipment_id;
					$equipment['status']=$xe_data->status;
				}
				$equipment['brand_id']=$control_data->brand_id;
				$equipment['control_type']=$control_data->control_type;
				$equipment['code_type']=$control_data->code_type;
				$equipment['isStudy']=$control_data->isStudy;
				$equipment['type']=$type;
			}

		}else{
			$e_data=Equipment::model()->findByPK($equipment_id);
			if(!empty($e_data)){
				$equipment['id']=$e_data->id;
				$equipment['router_equipment_id']=$e_data->router_equipment_id;
				$equipment['status']=$e_data->status;
				$equipment['type']=$type;
				$UE_data=UserEquipment::model()->findByAttributes(array('equipment_id'=>$equipment['id'],'user_id'=>$user_id));
				if(empty($UE_data)){
					$equipment['name']=$equipment['router_equipment_id'];
				}else{
					$equipment['name']=$UE_data->name;
				}
			}

		}
		echo json_encode(array('status'=>'200','message'=>$equipment));
		Yii::app()->end();	
	}


	/**
	 * 路由器从离线到在线 更新信息
	 */
	public function actionUpdateStatus(){
		// $_REQUEST=array(
		// 	'net_router_id'=>'00:01:6C:06:A6:3t',
		// 	'mac'=>' rr:01:6C:06:A6:2',
		// 	'model'=>'s-501',
		// 	'version'=>'版本test',
		// 	'count'=>'0',
		// );
		// $_REQUEST=json_encode($_REQUEST);
		if(empty($GLOBALS["HTTP_RAW_POST_DATA"])){
			echo json_encode(array('status'=>'300','message'=>'no update'));
			Yii::app()->end();
		}
		$update=json_decode($GLOBALS["HTTP_RAW_POST_DATA"],true);
		$equipment_model=Equipment::model();
		$bool=$equipment_model->doUpdate($update);

		if($bool){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'error'));
			Yii::app()->end();
		}
	}

	/**
	 * App上报新配对的设备
	 */
	public function actionNewE(){
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$net_router_id=isset($_REQUEST['net_router_id'])?$_REQUEST['net_router_id']:"";
		$net_equipment_id=isset($_REQUEST['net_equipment_id'])?$_REQUEST['net_equipment_id']:"";
		$name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
		$scene_id=isset($_REQUEST['scene_id'])?$_REQUEST['scene_id']:0;
		$status=isset($_REQUEST['status'])?$_REQUEST['status']:"";
		if(empty($user_id) || empty($net_router_id) || empty($net_equipment_id) || empty($name) || empty($status)){
			echo json_encode(array('status'=>'300','message'=>'request error'));
			Yii::app()->end();
		}
		$EM=Equipment::model();
		$bool=$EM->match($user_id,$net_router_id,$net_equipment_id,$name,$scene_id,$status);	
		if($bool){
			echo json_encode(array('status'=>'200','message'=>'success'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'error'));
			Yii::app()->end();
		}	
	}
	
	/**
	 * 添加普通空调设备
	 */
	public function actionAddC(){
		$name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
		$equipment_id=isset($_REQUEST['equipment_id'])?$_REQUEST['equipment_id']:"";
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$brand_id=isset($_REQUEST['brand_id'])?$_REQUEST['brand_id']:"";
		$control_type=isset($_REQUEST['control_type'])?$_REQUEST['control_type']:"";
		$code_type=isset($_REQUEST['code_type'])?$_REQUEST['code_type']:"";
		$scene_id=isset($_REQUEST['scene_id'])?$_REQUEST['scene_id']:0;
		if(empty($name) || empty($user_id) || empty($brand_id) || empty($control_type) || empty($code_type) ||empty($equipment_id)){
			echo json_encode(array('status'=>'300','message'=>'request error'));
			Yii::app()->end();
		}
		$transaction=Yii::app()->db->beginTransaction();
		try{
			if(intval($scene_id)!=0){
				$bool=Equipment::model()->addAOE($scene_id,$equipment_id);
				if(!$bool){
					echo json_encode(array('status'=>'500','message'=>'save error'));
					Yii::app()->end();
				}
			}

			$time=current_time();
			$CNM=new Control;
			$CNM->name=$name;
			$CNM->equipment_id=$equipment_id;
			$CNM->user_id=$user_id;
			$CNM->brand_id=$brand_id;
			$CNM->control_type=$control_type;
			$CNM->code_type=$code_type;
			$CNM->isStudy="0";
			$CNM->ctime=$time;
			$CNM->mtime=$time;
			$CNM->save();

			$transaction->commit();
			echo json_encode(array('status'=>'200','message'=>'success'));
			Yii::app()->end();
		}catch(Exception $e){
			$transaction->rollback();
			echo json_encode(array('status'=>'500','message'=>'save error'));
			Yii::app()->end();
		}
		
	}

	/**
	 * 添加空调 学习型
	 */
	public function  actionAddSC(){
		/*
		$_REQUEST['instruct_data']=array(
			array('instruct'=>'11111111111','instruct_name'=>"音量加"),
			array('instruct'=>'2222222222','instruct_name'=>"音量减"),
			);
		$_REQUEST['instruct_data']=json_encode($_REQUEST['instruct_data']);
		*/
		$name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
		$equipment_id=isset($_REQUEST['equipment_id'])?$_REQUEST['equipment_id']:"";
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$brand_id=isset($_REQUEST['brand_id'])?$_REQUEST['brand_id']:"";
		$control_type=isset($_REQUEST['control_type'])?$_REQUEST['control_type']:"";
		$code_type=isset($_REQUEST['code_type'])?$_REQUEST['code_type']:"";
		$scene_id=isset($_REQUEST['scene_id'])?$_REQUEST['scene_id']:0;
		$instruct_data=isset($_REQUEST['instruct_data'])?$_REQUEST['instruct_data']:"";
		$app_time=isset($_REQUEST['time'])?$_REQUEST['time']:"";
		if(empty($name) || empty($user_id) || empty($brand_id) || empty($control_type) || empty($code_type) ||empty($equipment_id)){
			echo json_encode(array('status'=>'300','message'=>'request error'));
			Yii::app()->end();
		}
		$transaction=Yii::app()->db->beginTransaction();
		try{
			if(intval($scene_id)!=0){
				$bool=Equipment::model()->addAOE($scene_id,$equipment_id);
				if(!$bool){
					echo json_encode(array('status'=>'500','message'=>'save error'));
					Yii::app()->end();
				}
			}

			$time=date("Y-m-d H:i:s",$app_time);
			$CNM=new Control;
			$CNM->name=$name;
			$CNM->equipment_id=$equipment_id;
			$CNM->user_id=$user_id;
			$CNM->brand_id=$brand_id;
			$CNM->control_type=$control_type;
			$CNM->code_type=$code_type;
			$CNM->isStudy="0";
			$CNM->ctime=$time;
			$CNM->mtime=$time;
			$CNM->save();
			$control_id=$CNM->attributes['id'];
			
			if(!empty($instruct_data)){
				$instruct_data = json_decode($instruct_data);
				foreach($instruct_data as  $instruct_val){
					$instruct=$instruct_val->instruct;
					$name=$instruct_val->instruct_name;
					$IM=new Instruct;
					$IM->control_id=$control_id;
					$IM->instruct=$instruct;
					$IM->name=$name;
					$IM->ctime=$time;
					$IM->mtime=$time;
					$IM->save();
				}
			}

			$transaction->commit();
			echo json_encode(array('status'=>'200','message'=>$control_id));
			Yii::app()->end();
		}catch(Exception $e){
			$transaction->rollback();
			echo json_encode(array('status'=>'500','message'=>'save error'));
			Yii::app()->end();
		}
	}
	

	//获取插座的具体信息（包括定时时间）
	public function actionGetTime(){
		$equipment_id=isset($_REQUEST['equipment_id'])?$_REQUEST['equipment_id']:"";
		if(empty($equipment_id)){
			echo json_encode(array('status'=>'300','message'=>'no equipment_id'));
			Yii::app()->end();
		}

		$equip_data=Equipment::model()->findByPK($equipment_id);
		if(empty($equip_data)){
			echo json_encode(array('status'=>'500','message'=>'no equipment'));
			Yii::app()->end();
		}
		$data[]=$equip_data->attributes;
		//P($data);
		echo json_encode(array('status'=>'200','message'=>$data));
		Yii::app()->end();
	}


}
