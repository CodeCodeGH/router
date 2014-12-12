<?php

/**
 * This is the model class for table "{{scene}}".
 *
 * The followings are the available columns in table '{{scene}}':
 * @property integer $id
 * @property integer $router_id
 * @property string $name
 * @property integer $position
 * @property string $ctime
 * @property string $mtime
 */
class Scene extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{scene}}';
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
	 * App用户获取一个路由下自己设定的的所有场景信息,场景下的设备信息
	 */
	public function  getAllScene($router_id,$userId){
		$scene_data=$this->findAllByAttributes(array('router_id'=>$router_id,'user_id'=>$userId));
		if(empty($scene_data)){
			return false;
		}
		foreach($scene_data  as $scene_val){
			$sce['id']=$scene_val->id;
			$sce['user_id']=$scene_val->user_id;
			$sce['router_id']=$scene_val->router_id;
			$sce['name']=$scene_val->name;
			$sce['picture_id']=$scene_val->picture_id;
			//获取分类
			$SED=SceneEquipment::model()->findAllByAttributes(array('scene_id'=>$sce['id']));
			if(empty($SED)){
				$sce['category']=array();
			}else{
				$hasType=array();
				foreach($SED as $val){
					$type=$val->type;
					if(!in_array($type,$hasType)){
						array_push($hasType,$type);	
					}else{
						continue;
					}
				}
				$sce['category']=$hasType;
			}
			$arr[]=$sce;
		}
		if(!empty($arr)){
			return $arr;
		}else{
			return false;
		}
	}	

	/**
	 * App删除场景
	 */
	public function appDel($scene_id){
		$time=date("Y-m-d H:i:s",time());
		$transaction=Yii::app()->db->beginTransaction();
		try{
			$this->deleteByPK($scene_id);
			$SE_model=SceneEquipment::model();
			$data=$SE_model->findAll('scene_id=:scene_id',array(":scene_id"=>$scene_id));
			if(!empty($data)){
				$SE_model->deleteAll('scene_id=:scene_id',array(":scene_id"=>$scene_id));
			}
			
	
			$transaction->commit();
			return true;
		}catch(Exception $e){
			$transaction->rollback();
			return false;
		}
	}

	/**
	 * App增加场景
	 */
	public function addScene($router_id,$name,$position,$picture_id,$user_id){
		$time=date("Y-m-d H:i:s",time());
		$bool=$this->exists("router_id=:router_id  AND  name=:name  AND user_id=:user_id",array(':router_id'=>$router_id,':name'=>$name,':user_id'=>$user_id));
		if($bool){
			$arr['type']=300;
			$arr['message']=300;
			return  $arr;
		}
		$scene_model=new Scene;
		$scene_model->router_id=$router_id;
		$scene_model->user_id=$user_id;
		$scene_model->name=$name;
		$scene_model->position=$position;
		$scene_model->picture_id=$picture_id;
		$scene_model->ctime=$time;
		$scene_model->mtime=$time;
		$id=$scene_model->save();
		if($id>0){
			$arr['type']=200;
			$arr['message']=$scene_model->attributes['id'];
			return $arr;
		}else{
			$arr['type']=500;
			$arr['message']=500;
			return  $arr;
		}
	}

	/**
	 * App 查找默认场景下的设备
	 */
	public function  getEquipment($type,$scene_id,$user_id,$router_id){
		if($type==1){
			$equipment_model=Equipment::model();
			$equipment_data=$equipment_model->findAllByAttributes(array('router_id'=>$router_id));
			if(!empty($equipment_data)){
				//用户已有场景下的设备
				$S_data=$this->findAllByAttributes(array('router_id'=>$router_id,'user_id'=>$user_id));
				$ids=array();
				if(!empty($S_data)){
					foreach($S_data  as $S_val){
						$S_id=$S_val->id;
						$SE_model=SceneEquipment::model();
						$SE_data=$SE_model->findAllByAttributes(array('scene_id'=>$S_id));
						if(!empty($SE_data)){
							foreach($SE_data as $SE_val){
								$ids[]=$SE_val->equipment_id;
							}
						}
					}
				}
				
				//获取当前用户未设场景的设备
				foreach($equipment_data as $equipment_val){
					$ee_id=$equipment_val->id;
					if(!in_array($ee_id,$ids)){
						$equipment['id']=$ee_id;
						$equipment['router_id']=$equipment_val->router_id;
						$equipment['router_equipment_id']=$equipment_val->router_equipment_id;

						$equipment['category']=substr($equipment['router_equipment_id'],2,2);
						$equipment['status']=$equipment_val->status;
						$equipment['position']=0;
						//获取设备别名
						// $equipment['name']=$equipment_val->name;
						$UE_model=UserEquipment::model();
						$UE_data=$UE_model->findByAttributes(array('equipment_id'=>$equipment['id'],'user_id'=>$user_id));
						if(empty($UR_data)){
							$equipment['name']=$equipment['router_equipment_id'];
						}else{
							$equipment['name']=$UE_data->name;
						}
						$E_arr[]=$equipment;
					}
				}
				return $E_arr;
			}else{
				return false;
			}

		}elseif($type==2){
			$SE_model=SceneEquipment::model();
			$SE_data=$SE_model->findAllByAttributes(array('scene_id'=>$scene_id));
			if(!empty($SE_data)){
				foreach($SE_data as $SE_val){
					$equipment_id=$SE_val->equipment_id;
					$EM_model=Equipment::model();
					$EM_data=$EM_model->findByPK($equipment_id);
					if(!empty($EM_data)){
						$equipment['id']=$EM_data->id;
						$equipment['router_id']=$EM_data->router_id;
						$equipment['router_equipment_id']=$EM_data->router_equipment_id;

						$equipment['category']=substr($equipment['router_equipment_id'],2,2);
						$equipment['status']=$EM_data->status;
						//设备在该场景下的位置
						$equipment['position']=$SE_val->position;
						//获取设备别名
						$UE_model=UserEquipment::model();
						$UE_data=$UE_model->findByAttributes(array('equipment_id'=>$equipment['id'],'user_id'=>$user_id));
						if(empty($UE_data)){
							$equipment['name']=$equipment['router_equipment_id'];
						}else{
							$equipment['name']=$UE_data->name;
						}
						$E_arr[]=$equipment;
					}
				}

				if(!empty($E_arr)){
					return $E_arr;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
	}

	/**
	 * App 端同步场景信息
	* $_REQUEST['scene']=array(
	*		array('mac'=>3,
	*		          'scene_info'=>array(
	*					array('scene_id'=>'1','scene_name'=>'场景名字','picture_id'=>1,'net_equipment_info'=>array(
	*							array('net_equipment_id'=>'1','position'=>'1'),
	*							array('net_equipment_id'=>'2','position'=>'2')
	*						)),
	*					array('scene_id'=>'0','scene_name'=>'刚添加的','picture_id'=>1,'net_equipment_info'=>array(
	*							array('net_equipment_id'=>'3','position'=>'1'),
	*						)),
	*				)
	*		),
	*		array('mac'=>6,
	*		          'scene_info'=>array(
	*		          			array('scene_id'=>'3','scene_name'=>'场景','picture_id'=>1,'net_equipment_info'=>array(
	*		          					array('net_equipment_id'=>'4','position'=>'1'),
	*		          				)),	
	*		          	)
	*		)
	*	);
	 */
	public function appSynchronization($scene,$user_id){
		$time=current_time();
		$scene=json_decode($scene);
		//记录在数据库中路由器不存在的路由id
		$not_synchronization=array();

		foreach($scene as $scene_val){
			$net_router_id=$scene_val->net_router_id;
			$scene_data=$scene_val->scene_info;

			$RM=Router::model();
			$bool=$RM->exists('net_router_id=:net_router_id',array(':net_router_id'=>$net_router_id));
			if($bool){
				$router_data=$RM->findByAttributes(array('net_router_id'=>$net_router_id));
				$router_id=$router_data->id;
				//路由器的别名
				$net_router_name=$scene_val->net_router_name;
				$URD=UserRouter::model()->findByAttributes(array('router_id'=>$router_id,'user_id'=>$user_id));
				if(!empty($URD)){
					$UR_id=$URD->id;
					UserRouter::model()->updateByPK($UR_id,array('alias'=>$net_router_name,'mtime'=>$time));
				}else{
					$URMO=new UserRouter;
					$URMO->user_id=$user_id;
					$URMO->router_id=$router_id;
					$URMO->alias=$net_router_name;
					$URMO->ctime=$time;
					$URMO->mtime=$time;
					if(!($URMO->save()>0)){
						return false;
					}
				}			
	
				$transaction=Yii::app()->db->beginTransaction();
				try{	
					//路由存在,但路由的场景为空,删除数据库中所有已有的场景
					if(empty($scene_data)){
						//删除一个路由下的所有场景及场景的关联数据
						$this->delAllScene($router_id,$user_id);
					}else{
						//路由下有场景信息，进行同步，删除数据库中多余的，增加数据库中没有的
						foreach($scene_data as $scene_val){
							$scene_id=$scene_val->scene_id;
							$scene_name=$scene_val->scene_name;
							$picture_id=$scene_val->picture_id;

							if(intval($scene_id)===0){//新添加的场景
								$SM=new Scene;
								$SM->user_id=$user_id;
								$SM->router_id=$router_id;
								$SM->name=$scene_val->scene_name;
								$SM->picture_id=$scene_val->picture_id;
								$SM->ctime=$time;
								$SM->mtime=$time;
								$SM->save();
								$s_id=$SM->attributes['id'];

								$equipment_info=$scene_val->net_equipment_info;
								if(!empty($equipment_info)){

									foreach($equipment_info  as $equipment_info_val){
										$position=$equipment_info_val->position;

										$router_equipment_id=$equipment_info_val->net_equipment_id;
										$e_data=Equipment::model()->findByAttributes(array('router_id'=>$router_id,'router_equipment_id'=>$router_equipment_id));
										if(!empty($e_data)){//设备存在
											$equipment_id=$e_data->id;

											$SE_model=new SceneEquipment;
											$SE_model->scene_id=$s_id;
											$SE_model->equipment_id=$equipment_id;
											$SE_model->position=$position;
											$SE_model->ctime=$time;
											$SE_model->mtime=$time;
											$SE_model->save();


											//设备别名
											$net_equipment_name=$equipment_info_val->net_equipment_name;
											$UED=UserEquipment::model()->findByAttributes(array('equipment_id'=>$equipment_id,'user_id'=>$user_id));
											if(empty($UED)){
												$UEM=new UserEquipment;
												$UEM->user_id=$user->id;
												$UEM->equipment_id=$equipment_id;
												$UEM->name=$net_equipment_name;
												$UEM->ctime=$time;
												$UEM->mtime;
												$UEM->save();
											}else{
												$UED_id=$UED->id;
												UserEquipment::model()->updateByPK($UED_id,array('name'=>$net_equipment_name,'mtime'=>$time));
											}

										}else{
											return false;
										}
									}
								}
							}

							if(intval($scene_id)!=0){
								$sce_data=Scene::model()->findAllByAttributes(array('router_id'=>$router_id));
								if(!empty($sce_data)){
									
									foreach($scene_data as $scene_val){
										if(intval($scene_val->scene_id)!=0){
											$scene_idS[]=$scene_val->scene_id;
										}	
									}

									//获取数据库中的所有场景id
									foreach($sce_data as $sce_val){
										$sce_id=$sce_val->id;
										if(in_array($sce_id,$scene_idS)){
											Scene::model()->updateByPK($sce_id,array('name'=>$scene_name,'picture_id'=>$picture_id,'mtime'=>$time));

											$equipment_info=$scene_val->net_equipment_info;
											if(!empty($equipment_info)){

												foreach($equipment_info  as $equipment_info_val){
													$position=$equipment_info_val->position;

													$router_equipment_id=$equipment_info_val->net_equipment_id;
													$e_data=Equipment::model()->findByAttributes(array('router_id'=>$router_id,'router_equipment_id'=>$router_equipment_id));
													if(!empty($e_data)){//设备存在
														$equipment_id=$e_data->id;
														$bool=SceneEquipment::model()->exists('equipment_id=:equipment_id  AND scene_id=:scene_id',array(':equipment_id'=>$equipment_id,':scene_id'=>$sce_id));
														if($bool){
															SceneEquipment::model()->deleteAll('equipment_id=:equipment_id  AND scene_id=:scene_id',array(':equipment_id'=>$equipment_id,':scene_id'=>$sce_id));
															$SE_model=new SceneEquipment;
															$SE_model->scene_id=$sce_id;
															$SE_model->equipment_id=$equipment_id;
															$SE_model->position=$position;
															$SE_model->ctime=$time;
															$SE_model->mtime=$time;
															$SE_model->save();

															//设备别名
															$net_equipment_name=$equipment_info_val->net_equipment_name;
															$UED=UserEquipment::model()->findByAttributes(array('equipment_id'=>$equipment_id,'user_id'=>$user_id));
															if(empty($UED)){
																$UEM=new UserEquipment;
																$UEM->user_id=$user->id;
																$UEM->equipment_id=$equipment_id;
																$UEM->name=$net_equipment_name;
																$UEM->ctime=$time;
																$UEM->mtime;
																$UEM->save();
															}else{
																$UED_id=$UED->id;
																UserEquipment::model()->updateByPK($UED_id,array('name'=>$net_equipment_name,'mtime'=>$time));
															}
														}else{
															$SE_model=new SceneEquipment;
															$SE_model->scene_id=$sce_id;
															$SE_model->equipment_id=$equipment_id;
															$SE_model->position=$position;
															$SE_model->ctime=$time;
															$SE_model->mtime=$time;
															$SE_model->save();

															//设备别名
															$net_equipment_name=$equipment_info_val->net_equipment_name;
															$UED=UserEquipment::model()->findByAttributes(array('equipment_id'=>$equipment_id,'user_id'=>$user_id));
															if(empty($UED)){
																$UEM=new UserEquipment;
																$UEM->user_id=$user->id;
																$UEM->equipment_id=$equipment_id;
																$UEM->name=$net_equipment_name;
																$UEM->ctime=$time;
																$UEM->mtime;
																$UEM->save();
															}else{
																$UED_id=$UED->id;
																UserEquipment::model()->updateByPK($UED_id,array('name'=>$net_equipment_name,'mtime'=>$time));
															}

														}
													}else{
														return false;
													}
												}
											}else{
												SceneEquipment::model()->deleteAll('scene_id=:scene_id',array(':scene_id'=>$sce_id));
											}
										}else{

										}
									}			

								}else{
									return false;
								}
							}
						}	
					}
					$transaction->commit();
				}catch(Exception $e){
					$transaction->rollback();
					return false;
				}
			}else{
				array_push($not_synchronization,$net_router_id);
			}
		}

		if(empty($not_synchronization)){
			return true;
		}else{
			return $not_synchronization;
		}
	}	




	public function delAllScene($router_id,$user_id){
		$scene_data=$this->findAllByAttributes(array('router_id'=>$router_id,'user_id'=>$user_id));
		if(!empty($scene_data)){
			foreach($scene_data as  $scene_val){
				$scene_id=$scene_val->id;
				SceneEquipment::model()->deleteAll("scene_id=:scene_id",array(':scene_id'=>$scene_id));
				$this->deleteByPK($scene_id);
			}
		}
		return true;
	}
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Scene the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
