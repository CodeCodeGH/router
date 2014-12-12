<?php

/**
 * This is the model class for table "{{scene_equipment}}".
 *
 * The followings are the available columns in table '{{scene_equipment}}':
 * @property integer $id
 * @property integer $scene_id
 * @property integer $equipment_id
 * @property string $ctime
 * @property string $mtime
 */
class SceneEquipment extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{scene_equipment}}';
	}

	

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * 修改设备在场景下的位置
	 * @param  int  scene_id
	 * @param  int  equipment_id 
	 * @param  int  position
	 */
	public function changePosition($position){
		$time=current_time();
		foreach($position as  $p_val){
			$scene_id=$p_val->scene_id;
			$equipment_id=$p_val->equipment_id;
			$position=$p_val->position;
			$type=$p_val->type;
			$bool=$this->updateAll(array('position'=>$position,'mtime'=>$time),'scene_id=:scene_id  AND equipment_id=:equipment_id AND type=:type',array(':scene_id'=>$scene_id,':equipment_id'=>$equipment_id,':type'=>$type));
			if(!$bool){
				return false;
			}
		}
		return true;
	}

	/**
	 * 获取场景下的设备信息
	 */
	public function getAllEquipment($scene_id,$user_id){
		$data=$this->findAllByAttributes(array('scene_id'=>$scene_id));
		if(empty($data)){
			return 400;
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
			return 400;
		}

	}

	
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SceneEquipment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
