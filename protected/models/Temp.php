<?php

/**
 * This is the model class for table "{{temp}}".
 *
 * The followings are the available columns in table '{{temp}}':
 * @property integer $id
 * @property integer $router_id
 * @property string $type
 * @property string $ctime
 * @property string $mtime
 */
class Temp extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{temp}}';
	}

	/**
	 * 根据路由器返回状态修改临时表数据
	 */
	public function getRouterReturn($data){
		$time=current_time();
		$id=$data['id'];
		$net_router_id=$data['net_router_id'];
		$result=$data['result'];
		$count=isset($data['count'])?$data['count']:0;

		if(intval($result)===200){//成功	
			$temp_data=$this->findByPK($id);
			if(empty($temp_data)){
				return false;
			}		
			$operation_type=$temp_data->operation_type;
			$net_equipment_id=$temp_data->net_equipment_id;

			$transaction=Yii::app()->db->beginTransaction();
			try{
				if(intval($count)!==0){
					if($operation_type=="deviceAllStatus"){
						for($i=1;$i<=$count;$i++){
							$data_net_equipment_id=$data["net_equipment_id".$i];
							$status=$data["status".$i];
							Equipment::model()->updateAll(array('mtime'=>$time,'last_status'=>$status),'router_equipment_id=:router_equipment_id',array(':router_equipment_id'=>$data_net_equipment_id));
						}
					}

					if($operation_type=="deviceAllNewStatus"){
						for($i=1;$i<=$count;$i++){
							$data_net_equipment_id=$data["net_equipment_id".$i];
							$status=$data["status".$i];
							$tp=substr($data_net_equipment_id,2,2);
							if($tp=='08'){
								Equipment::model()->updateAll(array('mtime'=>$time,'status'=>$status,'set_time'=>$tp),'router_equipment_id=:router_equipment_id',array(':router_equipment_id'=>$data_net_equipment_id));
							}else{
								Equipment::model()->updateAll(array('mtime'=>$time,'status'=>$status),'router_equipment_id=:router_equipment_id',array(':router_equipment_id'=>$data_net_equipment_id));
							}	
						}
					}

				}else{

					$equip_status=$temp_data->equip_status;
					if($operation_type=="plugeOpen-plugeClose-chargeOpen-chargeClose"){
						$operation=$temp_data->operation;
						Equipment::model()->updateAll(array('mtime'=>$time,'status'=>$equip_status,'set_time'=>$operation),'router_equipment_id=:router_equipment_id',array(':router_equipment_id'=>$net_equipment_id));
					}else{
						Equipment::model()->updateAll(array('mtime'=>$time,'status'=>$equip_status),'router_equipment_id=:router_equipment_id',array(':router_equipment_id'=>$net_equipment_id));
					}				
					

				}

				
				//更新指令表
				$this->updateByPK(array($id),array('status'=>'1','mtime'=>$time),'net_router_id=:net_router_id',array('net_router_id'=>$net_router_id));

				$transaction->commit();
				return true;
			}catch(Exception $e){
				$transaction->rollback();
				return false;
			}
		
		}
			
		if(intval($result)===300){//失败
			$bool=$this->updateByPK(array($id),array('status'=>'2','mtime'=>$time),'net_router_id=:net_router_id',array('net_router_id'=>$net_router_id));
			if($bool){
				return true;
			}else{
				return false;
			}
		}

	}


	/**
	 * 电灯  插座  空调遥控器 操作
	 */
	/*
	public function  addOperation($router_id,$equipment_id,$operation,$operation_type,$user_id,$type){
		$time=current_time();
		//获取路由器端的路由id
		$R_data=Router::model()->findByPK($router_id);
		if(empty($R_data)){
			return false;
		}
		$R_id=$R_data->net_router_id;

		//获取路由器端的设备id
		if($type=="02" || $type=="03"){
			$data=Control::model()->findByPK($equipment_id);
			if(empty($data)){
				return false;
			}
			$RE_id=$data->equipment_id;//获取虚拟遥控器对应的物理遥控器的id
			$E_data=Equipment::model()->findByPK($RE_id);
			if(empty($E_data)){
				return false;
			}
		}else{
			$E_data=Equipment::model()->findByPK($equipment_id);
			if(empty($E_data)){
				return false;
			}
		}
		
		$E_id=$E_data->router_equipment_id;


		$new_model=new Temp;
		if($operation!="default"){
		 	$new_model->operation=$operation;
		}
		$new_model->operation_type=$operation_type;
		$new_model->user_id=$user_id;
		$new_model->ctime=$time;
		$new_model->mtime=$time;
		$new_model->net_router_id=$R_id;
		$new_model->net_equipment_id=$E_id;
		if($new_model->save() > 0){
			return true;
		}else{
			return false;
		}	
	}
	*/
	
	public function  addOperation($router_id,$equipment_id,$operation,$operation_type,$user_id,$type,$equip_status){
		$time=current_time();
		//获取路由器端的路由id
		$R_data=Router::model()->findByPK($router_id);
		if(empty($R_data)){
			return false;
		}
		$R_id=$R_data->net_router_id;

		//获取路由器端的设备id
		if($type=="02" || $type=="03"){
			$data=Control::model()->findByPK($equipment_id);
			if(empty($data)){
				return false;
			}
			$RE_id=$data->equipment_id;//获取虚拟遥控器对应的物理遥控器的id
			$E_data=Equipment::model()->findByPK($RE_id);
			if(empty($E_data)){
				return false;
			}
		}else{
			$E_data=Equipment::model()->findByPK($equipment_id);
			if(empty($E_data)){
				return false;
			}
		}
		
		$E_id=$E_data->router_equipment_id;

		$new_model=new Temp;
		if($operation!="default"){
		 	$new_model->operation=$operation;
		}
		$new_model->operation_type=$operation_type;
		$new_model->equip_status=$equip_status;
		$new_model->user_id=$user_id;
		$new_model->ctime=$time;
		$new_model->mtime=$time;
		$new_model->net_router_id=$R_id;
		$new_model->net_equipment_id=$E_id;
		if($new_model->save() > 0){
			return true;
		}else{
			return false;
		}	
	}



	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Temp the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
