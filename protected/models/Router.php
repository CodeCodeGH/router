<?php

/**
 * This is the model class for table "{{router}}".
 *
 * The followings are the available columns in table '{{router}}':
 * @property string $id
 * @property string $mac
 * @property string $ctime
 * @property string $mtime
 */
class Router extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{router}}';
	}



	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		);
	}

	
	/**
	 * App获取用户在线路由,标识路由是否离线
	 */
	public function getOnlineRouter($userId){
		$arr=array();
		$UR_model=UserRouter::model();
		$UR_data=$UR_model->findAllByAttributes(array('user_id'=>$userId));
		if(!empty($UR_data)){
			foreach($UR_data as $UR_val){
				//获取路由固定信息
				$router_id=$UR_val->router_id;
				$router_data=$this->findByPK($router_id);
				if(empty($router_data)){
					continue;
				}
				$router['id']=$router_data->id;
				$router['net_router_id']=$router_data->net_router_id;
				$router['mac']=$router_data->mac;
				$router['version']=$router_data->version;
				$router['model']=$router_data->model;
				$router['ctime']=$router_data->ctime;
				$router['mtime']=$router_data->mtime;
				$router['access_time']=$router_data->access_time;
				
				//获取路由器与用户关联的信息
				$router['alias']=$UR_val->alias;
				$router['router_login_password']=$UR_val->router_login_password;

				//标识路由是否离线
				$router['isOnline']=$this->IsRouterOnline($router_id);
				
				//获取路由场景信息
				$scene_model=Scene::model();
				$scence_data=$scene_model->getAllScene($router_id,$userId);
				if($scence_data){
					$router['scene']=$scence_data;
				}else{
					$router['scene']=array();
				}
				$arr[]=$router;
			}
			
		}
		return $arr;
	}

	/**
	 * 标识路由是否离线
	 */
	public function IsRouterOnline($router_id){
		$router_data=$this->findByPK($router_id);
		$access_time=$router_data->access_time;
		$beforetime=intval(strtotime($access_time));
		$currenttime=intval(time());
		$difference=$currenttime-$beforetime;
		if($difference>10){
			return 0;//离线
		}else{
			return 1;//在线
		}
	}

	/**
	 * 通过路由器在数据库中的id查询路由器mac地址
	 */
	public function ITM($router_id){
		$router_data=$this->findByAttributes(array('router_id'=>$router_id));
		if(!empty($router_data)){
			$mac=$router_data->mac;
			return $mac;
		}else{
			return false;
		}	
	}


	/**
	 *  修改路由器名字，并存入临时操作表中
	 */
	public function cname($router_id,$alias){
		$time=current_time();
		//获取路由器端的路由id
		$R_data=$this->findByPK($router_id);
		if(empty($R_data)){
			return false;
		}
		$R_id=$R_data->net_router_id;

		//查看路由临时操作表中是否已经存在对该路由器修改别名的操作
		$temp_model=Temp::model();
		$temp_data=$temp_model->findByAttributes(array('net_router_id'=>$R_id,'type'=>'0','status'=>'0'));
		if(!empty($temp_data)){
			$id=$temp_data->id;
			$bool=$temp_model->updateByPK($id,array('operation'=>$alias,'mtime'=>$time));
			if($bool){
				return $id;
			}else{
				return false;
			}
		}else{
			$new_model=new Temp;
			$new_model->operation=$alias;
			$new_model->type='0';
			$new_model->ctime=$time;
			$new_model->mtime=$time;
			$new_model->net_router_id=$R_id;
			$new_model->net_equipment_id=0;
			if($new_model->save() > 0){
				$id=$new_model->attributes['id'];
				return $id;
			}else{
				return false;
			}
		}
	}


	/**
	 * App 用户删除路由
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
	 * @return Router the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
