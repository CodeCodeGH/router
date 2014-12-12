<?php
class Router extends CActiveRecord{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{router}}';
	}

	/**
	 * 用户删除路由
	 */
	public function deleteRouter($user_id,$router_id){
		$transaction=Yii::app()->db->beginTransaction();
		try{
			//删除该用户的路由记录
			$UR_model=UserRouter::model();
			$UR_model->deleteAll("user_id=:user_id AND router_id=:router_id",array(':user_id'=>$user_id,":router_id"=>$router_id));

			//删除该用户在路由下的场景设置
			$scene_model=Scene::model();
			$scene_data=$scene_model->findAll("user_id=:user_id AND router_id=:router_id",array(':user_id'=>$user_id,":router_id"=>$router_id));
			if(!empty($scene_data)){
				foreach($scene_data as  $scene_val){
					$scene_id=$scene_val->id;
					$SE_model=SceneEquipment::model();
					$SE_bool=$SE_model->exists("scene_id=:scene_id",array(':scene_id'=>$scene_id));
					if($SE_bool){
						$SE_model->deleteAll("scene_id=:scene_id",array(':scene_id'=>$scene_id));
					}
				}
				$scene_model->deleteAll("user_id=:user_id AND router_id=:router_id",array(':user_id'=>$user_id,":router_id"=>$router_id));
			}
			
			//删除该用户对路由下的设备设置的别名,及该用户创建的虚拟遥控器
			$equipment_model=Equipment::model();
			$equipment_data=$equipment_model->findAll("router_id=:router_id",array(':router_id'=>$router_id));
			if(!empty($equipment_data)){
				foreach($equipment_data as $equipment_val){
					$equipment_id=$equipment_val->id;
					$router_equipment_id=$equipment_val->router_equipment_id;
					$category=substr($router_equipment_id,2,2);
					$UE_model=UserEquipment::model();

					if($category=="02" || $category=="03"){
						//删除物理遥控器设置的别名
						$UE_model->deleteAll('equipment_id=:equipment_id AND  user_id=:user_id',array(':equipment_id'=>$equipment_id,':user_id'=>$user_id));
						//删除虚拟遥控器
						Control::model()->deleteAll('equipment_id=:equipment_id AND user_id=:user_id',array(':equipment_id'=>$equipment_id,':user_id'=>$user_id));
					}else{
						//删除一般设备设置的别名
						$UE_model->deleteAll('equipment_id=:equipment_id AND  user_id=:user_id',array(':equipment_id'=>$equipment_id,':user_id'=>$user_id));
					}		
				}
			}

			$transaction->commit();
			return true;
		}catch(Exception $e){
			$transaction->rollback();
			return false;
		}
	}



	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
?>