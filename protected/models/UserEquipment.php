<?php

/**
 * This is the model class for table "{{user_equipment}}".
 *
 * The followings are the available columns in table '{{user_equipment}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $equipment_id
 * @property string $name
 * @property string $ctime
 * @property string $mtime
 */
class UserEquipment extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_equipment}}';
	}

	/**
	 * 修改设备名字
	 */
	public function changeName($user_id,$equipment_id,$name){
		$time=current_time();
		$UE_model=UserEquipment::model();
		$UE_data=$UE_model->findByAttributes(array('user_id'=>$user_id,'equipment_id'=>$equipment_id));
		if(empty($UE_data)){
			$new_model=new UserEquipment;
			$new_model->user_id=$user_id;
			$new_model->equipment_id=$equipment_id;
			$new_model->name=$name;
			$new_model->ctime=$time;
			$new_model->mtime=$time;
			if($new_model->save()>0){
				return true;
			}else{
				return false;
			}
		}else{
			$id=$UE_data->id;
			$bool=$UE_model->updateByPK($id,array('name'=>$name,'mtime'=>$time),'user_id=:user_id AND equipment_id=:equipment_id',array(':equipment_id'=>$equipment_id,':user_id'=>$user_id));
			if($bool){
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserEquipment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
