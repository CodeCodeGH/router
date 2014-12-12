<?php
class SceneEquipment  extends  CActiveRecord{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{scene_equipment}}';
	}

	/**
	 * 获取场景下的设备信息
	 */
	public function getAllEquipment($scene_id,$user_id){
		$data=$this->findAllByAttributes(array('scene_id'=>$scene_id));
		if(empty($data)){
			$data=array();
			return $data;
		}

		$arr=array();
		foreach($data as $val){
			$equipment=array();
			$equipment_id=$val->equipment_id;
			$type=$val->type;
			$equipment['position']=$val->position;
			//虚拟遥控器
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
					$arr[]=$equipment;
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
				$arr[]=$equipment;
			}
		}

		if(!empty($arr)){
			return $arr;
		}else{
			$arr=array();
			return $arr;
		}

	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}