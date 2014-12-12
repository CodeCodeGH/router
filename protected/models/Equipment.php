<?php

/**
 * This is the model class for table "{{equipment}}".
 *
 * The followings are the available columns in table '{{equipment}}':
 * @property integer $id
 * @property integer $scene_id
 * @property string $router_equipment_id
 * @property string $name
 * @property integer $category_id
 * @property integer $position
 * @property string $ctime
 * @property string $mtime
 */
class Equipment extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{equipment}}';
	}


	/**
	 * 路由离线到在线
	 *$_REQUEST['update']=array(
	*		'net_router_id'=>'00:01:6C:06:A6:30',
	*		'mac'=>' rr:01:6C:06:A6:29',
	*		'version'=>'版本1',
	*		'model'=>'S-501',
	*		'count'=>'3',
	*		'net_equipment_id1'=>'10',
	*		'status1'=>'0101',
	*		'net_equipment_id2'=>'11',
	*		'status2'=>'0101',
	*		'net_equipment_id3'=>'12',
	*		'status3'=>'0101',
	*		
	*);
	 */

	/*	
	public function doUpdate($update){
		$time=current_time();

		//路由信息
		$net_router_id=$update['net_router_id'];
		$mac=$update['mac'];
		$model=$update['model'];
		$version=$update['version'];
		$count=$update['count'];
		
		$router_model=Router::model();
		$equipment_model=Equipment::model();

		$router_exists=$router_model->exists('net_router_id=:net_router_id',array(':net_router_id'=>$net_router_id));

		//路由是否已存在
		if($router_exists){
			//路由存在，更新路由信息
			$router_model->updateAll(array('mac'=>$mac,'model'=>$model,'version'=>$version,'access_time'=>$time,'mtime'=>$time),'net_router_id=:net_router_id',array(':net_router_id'=>$net_router_id));
			
			//获取数据库中路由器的id
			$router_data=$router_model->findByAttributes(array('net_router_id'=>$net_router_id));
			if(empty($router_data)){
				return false;
			}
			$router_id=$router_data->id;

			//路由器下的设备信息
			if(intval($count)!=0){//路由器下有设备
				
				//路由器下当前有设备,获取设备id
				for($i=1;$i<=$count;$i++){
					$net_equipment_idS[]=$update["net_equipment_id".$i];
				}

				//数据库中路由器下设备信息
				$equipment_data=$equipment_model->findAllByAttributes(array('router_id'=>$router_id));
				if(!empty($equipment_data)){
					
					//数据库中的设备id
					foreach($equipment_data  as $equipment_valS){
						$router_equipment_idS[]=$equipment_valS->router_equipment_id;
					}

					$transaction=Yii::app()->db->beginTransaction();
					try{

						//数据库中有的设备, 路由器没有, 删除数据库中设备信息
						foreach($equipment_data  as $equipment_val){
							$router_equipment_id=$equipment_val->router_equipment_id;
							if(!in_array($router_equipment_id,$net_equipment_idS)){	
								$E_data=$equipment_model->findAllByAttributes(array('router_equipment_id'=>$router_equipment_id,'router_id'=>$router_id));
								if(!empty($E_data)){
									foreach($E_data as $E_val){
										$equipment_id=$E_val->id;
										//删除用户设备表
										UserEquipment::model()->deleteAll('equipment_id=:equipment_id',array(':equipment_id'=>$equipment_id));
										//删除设备场景表
										$ca=substr($router_equipment_id,2,2);
										if($ca=="02" || $ca=="03"){
											$cdata=Control::model()->findAllByAttributes(array('equipment_id'=>$equipment_id));
											if(!empty($cdata)){
												foreach($cdata as $cval){
													$control_id=$cval->id;
													SceneEquipment::model()->deleteAll('equipment_id=:equipment_id AND type=:type',array(':equipment_id'=>$control_id,':type'=>$ca));
													Control::model()->deleteAll('equipment_id=:equipment_id',array(':equipment_id'=>$equipment_id));
												}
											}
										}else{
											SceneEquipment::model()->deleteAll('equipment_id=:equipment_id AND type=:type',array(':equipment_id'=>$equipment_id,':type'=>$ca));
										}
									}
									
								}
								$equipment_model->deleteAll('router_equipment_id=:router_equipment_id AND router_id=:router_id',array(':router_equipment_id'=>$router_equipment_id,'router_id'=>$router_id));
							}
						}

						//路由器有的，数据库中没有的添加信息,否则更新信息
						for($i=1;$i<=$count;$i++){
							$net_equipment_id=$update["net_equipment_id".$i];
							$status=$update["status".$i];
							if(in_array($net_equipment_id,$router_equipment_idS)){
								//更新设备
								$equipment_model->updateAll(array('status'=>$status,'mtime'=>$time),'router_equipment_id=:router_equipment_id',array('router_equipment_id'=>$net_equipment_id));
							}else{
								//添加设备
								$EM=new Equipment;
								$EM->status=$status;
								$EM->router_id=$router_id;
								$EM->router_equipment_id=$net_equipment_id;
								$EM->ctime=$time;
								$EM->mtime=$time;
								$EM->save();	
							}

						}
						$transaction->commit();
						return true;
					}catch(Exception $e){
						$transaction->rollback();
						return false;
					}
				}else{
					$transaction=Yii::app()->db->beginTransaction();
				   	 //数据库中没有设备信息,全部添加
					try{
						for($i=1;$i<=$count;$i++){
							$net_equipment_id=$update["net_equipment_id".$i];
							$status=$update["status".$i];

							$EM=new Equipment;
							$EM->status=$status;
							$EM->router_id=$router_id;
							$EM->router_equipment_id=$net_equipment_id;
							$EM->ctime=$time;
							$EM->mtime=$time;
							$EM->save();
							
						}
						$transaction->commit();
						return true;
					}catch(Exception $e){
						$transaction->rollback();
						return false;
					}
				}
			}else{
				$transaction=Yii::app()->db->beginTransaction();
				//当前路由器下无设备,删除数据库中所有信息
				try{
					$E_data=$equipment_model->findAllByAttributes(array('router_id'=>$router_id));
					if(!empty($E_data)){
						foreach($E_data as $E_val){
							$equipment_id=$E_val->id;
							$router_e_id=$E_val->router_equipment_id;
							//删除用户设备表
							UserEquipment::model()->deleteAll('equipment_id=:equipment_id',array(':equipment_id'=>$equipment_id));
							//删除设备场景表
							$ca=substr($router_e_id,2,2);
							if($ca=="02" || $ca=="03"){
								$cdata=Control::model()->findAllByAttributes(array('equipment_id'=>$equipment_id));
								if(!empty($cdata)){
									foreach($cdata as $cval){
										$control_id=$cval->id;
										SceneEquipment::model()->deleteAll('equipment_id=:equipment_id AND type=:type',array(':equipment_id'=>$control_id,':type'=>$ca));
										Control::model()->deleteAll('equipment_id=:equipment_id',array(':equipment_id'=>$equipment_id));
									}
								}
							}else{
								SceneEquipment::model()->deleteAll('equipment_id=:equipment_id AND type=:type',array(':equipment_id'=>$equipment_id,':type'=>$ca));
							}
						}
						$equipment_model->deleteAll('router_id=:router_id',array('router_id'=>$router_id));
						$transaction->commit();
						return true;
					}else{
						return true;
					}	
				}catch(Exception $e){
					$transaction->rollback();
					return false;
				}				
			}
		}else{
			
			//数据库中没有该路由，添加该路由信息
			$transaction=Yii::app()->db->beginTransaction();
			try{

				$RM=new Router;
				$RM->net_router_id=$net_router_id;
				$RM->mac=$mac;
				$RM->version=$version;
				$RM->model=$model;
				$RM->access_time=$time;
				$RM->ctime=$time;
				$RM->mtime=$time;
				$RM->save();
				$router_id=$RM->attributes['id'];

				//添加设备信息
				if(intval($count)!=0){
					  for($i=1;$i<=$count;$i++){
						$net_equipment_id=$update["net_equipment_id".$i];
						$status=$update["status".$i];
						
						$EM=new Equipment;
						$EM->status=$status;
						$EM->router_id=$router_id;
						$EM->router_equipment_id=$net_equipment_id;
						$EM->ctime=$time;
						$EM->mtime=$time;
						$EM->save();	
					}
				}
				$transaction->commit();
				return true;
			}catch(Exception $e){
				$transaction->rollback();
				return false;
			}
		}
		
	}
	*/

	public function doUpdate($update){
		$time=current_time();

		//路由信息
		$net_router_id=$update['net_router_id'];
		$mac=$update['mac'];
		$model=$update['model'];
		$version=$update['version'];
		$count=$update['count'];
		
		$router_model=Router::model();
		$equipment_model=Equipment::model();

		$router_exists=$router_model->exists('net_router_id=:net_router_id',array(':net_router_id'=>$net_router_id));

		//路由是否已存在
		if($router_exists){
			//路由存在，更新路由信息
			$router_model->updateAll(array('mac'=>$mac,'model'=>$model,'version'=>$version,'access_time'=>$time,'mtime'=>$time),'net_router_id=:net_router_id',array(':net_router_id'=>$net_router_id));
			
			//获取数据库中路由器的id
			$router_data=$router_model->findByAttributes(array('net_router_id'=>$net_router_id));
			if(empty($router_data)){
				return false;
			}
			$router_id=$router_data->id;

			//路由器下的设备信息
			if(intval($count)!=0){//路由器下有设备
				
				//路由器下当前有设备,获取设备id
				for($i=1;$i<=$count;$i++){
					$net_equipment_idS[]=$update["net_equipment_id".$i];
				}

				//数据库中路由器下设备信息
				$equipment_data=$equipment_model->findAllByAttributes(array('router_id'=>$router_id));
				if(!empty($equipment_data)){
					
					//数据库中的设备id
					foreach($equipment_data  as $equipment_valS){
						$router_equipment_idS[]=$equipment_valS->router_equipment_id;
					}

					$transaction=Yii::app()->db->beginTransaction();
					try{

						//数据库中有的设备, 路由器没有, 删除数据库中设备信息
						foreach($equipment_data  as $equipment_val){
							$router_equipment_id=$equipment_val->router_equipment_id;
							if(!in_array($router_equipment_id,$net_equipment_idS)){	
								$E_data=$equipment_model->findAllByAttributes(array('router_equipment_id'=>$router_equipment_id,'router_id'=>$router_id));
								if(!empty($E_data)){
									foreach($E_data as $E_val){
										$equipment_id=$E_val->id;
										//删除用户设备表
										UserEquipment::model()->deleteAll('equipment_id=:equipment_id',array(':equipment_id'=>$equipment_id));
										//删除设备场景表
										$ca=substr($router_equipment_id,2,2);
										if($ca=="02" || $ca=="03"){
											$cdata=Control::model()->findAllByAttributes(array('equipment_id'=>$equipment_id));
											if(!empty($cdata)){
												foreach($cdata as $cval){
													$control_id=$cval->id;
													SceneEquipment::model()->deleteAll('equipment_id=:equipment_id AND type=:type',array(':equipment_id'=>$control_id,':type'=>$ca));
													Control::model()->deleteAll('equipment_id=:equipment_id',array(':equipment_id'=>$equipment_id));
												}
											}
										}else{
											SceneEquipment::model()->deleteAll('equipment_id=:equipment_id AND type=:type',array(':equipment_id'=>$equipment_id,':type'=>$ca));
										}
									}
									
								}
								$equipment_model->deleteAll('router_equipment_id=:router_equipment_id AND router_id=:router_id',array(':router_equipment_id'=>$router_equipment_id,'router_id'=>$router_id));
							}
						}

						//路由器有的，数据库中没有的添加信息,否则更新信息
						for($i=1;$i<=$count;$i++){
							$net_equipment_id=$update["net_equipment_id".$i];
							$status=$update["status".$i];
							if(in_array($net_equipment_id,$router_equipment_idS)){
								//更新设备
								$tp=substr($net_equipment_id,2,2);
								if($tp=="08"){
									$set_time=$update["set_time".$i];
									$equipment_model->updateAll(array('status'=>$status,'mtime'=>$time,'set_time'=>$set_time),'router_equipment_id=:router_equipment_id',array('router_equipment_id'=>$net_equipment_id));
								}else{
									$equipment_model->updateAll(array('status'=>$status,'mtime'=>$time),'router_equipment_id=:router_equipment_id',array('router_equipment_id'=>$net_equipment_id));
								}
							}else{
								//添加设备
								$EM=new Equipment;
								$EM->status=$status;
								$EM->router_id=$router_id;
								$EM->router_equipment_id=$net_equipment_id;
								$EM->ctime=$time;
								$EM->mtime=$time;
								$tp=substr($net_equipment_id,2,2);
								if($tp=="08"){
									$EM->set_time=$update["set_time".$i];
								}
								$EM->save();	
							}

						}
						$transaction->commit();
						return true;
					}catch(Exception $e){
						$transaction->rollback();
						return false;
					}
				}else{
					$transaction=Yii::app()->db->beginTransaction();
				   	 //数据库中没有设备信息,全部添加
					try{
						for($i=1;$i<=$count;$i++){
							$net_equipment_id=$update["net_equipment_id".$i];
							$status=$update["status".$i];

							$EM=new Equipment;
							$EM->status=$status;
							$EM->router_id=$router_id;
							$EM->router_equipment_id=$net_equipment_id;
							$EM->ctime=$time;
							$EM->mtime=$time;

							$tp=substr($net_equipment_id,2,2);
							if($tp=="08"){
								$EM->set_time=$update["set_time".$i];
							}
							$EM->save();
							
						}
						$transaction->commit();
						return true;
					}catch(Exception $e){
						$transaction->rollback();
						return false;
					}
				}
			}else{
				$transaction=Yii::app()->db->beginTransaction();
				//当前路由器下无设备,删除数据库中所有信息
				try{
					$E_data=$equipment_model->findAllByAttributes(array('router_id'=>$router_id));
					if(!empty($E_data)){
						foreach($E_data as $E_val){
							$equipment_id=$E_val->id;
							$router_e_id=$E_val->router_equipment_id;
							//删除用户设备表
							UserEquipment::model()->deleteAll('equipment_id=:equipment_id',array(':equipment_id'=>$equipment_id));
							//删除设备场景表
							$ca=substr($router_e_id,2,2);
							if($ca=="02" || $ca=="03"){
								$cdata=Control::model()->findAllByAttributes(array('equipment_id'=>$equipment_id));
								if(!empty($cdata)){
									foreach($cdata as $cval){
										$control_id=$cval->id;
										SceneEquipment::model()->deleteAll('equipment_id=:equipment_id AND type=:type',array(':equipment_id'=>$control_id,':type'=>$ca));
										Control::model()->deleteAll('equipment_id=:equipment_id',array(':equipment_id'=>$equipment_id));
									}
								}
							}else{
								SceneEquipment::model()->deleteAll('equipment_id=:equipment_id AND type=:type',array(':equipment_id'=>$equipment_id,':type'=>$ca));
							}
						}
						$equipment_model->deleteAll('router_id=:router_id',array('router_id'=>$router_id));
						$transaction->commit();
						return true;
					}else{
						return true;
					}	
				}catch(Exception $e){
					$transaction->rollback();
					return false;
				}				
			}
		}else{
			
			//数据库中没有该路由，添加该路由信息
			$transaction=Yii::app()->db->beginTransaction();
			try{

				$RM=new Router;
				$RM->net_router_id=$net_router_id;
				$RM->mac=$mac;
				$RM->version=$version;
				$RM->model=$model;
				$RM->access_time=$time;
				$RM->ctime=$time;
				$RM->mtime=$time;
				$RM->save();
				$router_id=$RM->attributes['id'];

				//添加设备信息
				if(intval($count)!=0){
					  for($i=1;$i<=$count;$i++){
						$net_equipment_id=$update["net_equipment_id".$i];
						$status=$update["status".$i];
						
						$EM=new Equipment;
						$EM->status=$status;
						$EM->router_id=$router_id;
						$EM->router_equipment_id=$net_equipment_id;
						$EM->ctime=$time;
						$EM->mtime=$time;
						$tp=substr($net_equipment_id,2,2);
						if($tp=="08"){
							$EM->set_time=$update["set_time".$i];
						}
						$EM->save();	
					}
				}
				$transaction->commit();
				return true;
			}catch(Exception $e){
				$transaction->rollback();
				return false;
			}
		}
		
	}

	/**
	 * 用户没设场景的设备
	 */
	public function  noScene($router_id,$user_id){
		$equipment_model=Equipment::model();
		$equipment_data=$equipment_model->findAllByAttributes(array('router_id'=>$router_id));
		if(!empty($equipment_data)){
			//用户已有场景下的设备
			$S_data=Scene::model()->findAllByAttributes(array('router_id'=>$router_id,'user_id'=>$user_id));
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
					//$equipment['position']=0;
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
	}


	/**
	 * App 添加设备 
	 */
	public function addAE($data){
		$time=current_time();
		$number=array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24);

		$scene_id=$data->scene_id;
		$info=$data->info;
		
		$SE_model=SceneEquipment::model();
		$SE_data=$SE_model->findAllByAttributes(array('scene_id'=>$scene_id));
		if(!empty($SE_data)){
			foreach($SE_data as $SE_val){
				$has_position[]=$SE_val->position;
			}
		}else{
			$has_position=array();
		}
		//场景下可使用的位置
		$able_position=array_diff($number,$has_position);

		foreach($info as  $info_val){
			$equipment_id=$info_val->equipment_id;
			$type=$info_val->type;
			//确定位置
			$index=array_rand($able_position);
			$position=$able_position[$index];
			unset($able_position[$index]);

			//判断是否已存在
			$bool=SceneEquipment::model()->exists('scene_id=:scene_id AND  equipment_id=:equipment_id AND type=:type',array(':scene_id'=>$scene_id,':equipment_id'=>$equipment_id,':type'=>$type));
			if($bool){
				continue;
			}

			$SE_NEW=new SceneEquipment;
			$SE_NEW->scene_id=$scene_id;
			$SE_NEW->equipment_id=$equipment_id;
			$SE_NEW->type=$type;
			$SE_NEW->position=$position;
			$SE_NEW->ctime=$time;
			$SE_NEW->mtime=$time;
			if(!$SE_NEW->save()){
				return false;
			}	
		}
		
		return true;
	}

	/**
	 * APP删除设备
	 */
	public function doDel($equipment_id,$operation){
		$time=current_time();
		$EM=Equipment::model();
		$E_data=$EM->findByPK($equipment_id);
		//路由器端设备id
		$net_equipment_id=$E_data->router_equipment_id;
		//路由id
		$router_id=$E_data->router_id;
		$RM=Router::model();
		$R_data=$RM->findByPK($router_id);
		$net_router_id=$R_data->net_router_id;

		$TM=new Temp;
		$TM->net_router_id=$net_router_id;
		$TM->net_equipment_id=$net_equipment_id;
		$TM->operation=$operation;
		$TM->ctime=$time;
		$TM->mtime=$time;
		if($TM->save()>0){
			$id=$TM->attributes['id'];
			return $id;
		}else{
			return false;
		}
	}


	/**
	 * APP删除一个设备及关联数据
	 */
	public function delERelation($equipment_id,$del_type){

		$transaction=Yii::app()->db->beginTransaction();
		//物理设备
		if(intval($del_type)===1){
			try{
				
				$EM=Equipment::model();

				
				$EMD=$EM->findByPK($equipment_id);
				if(empty($EMD)){
					return false;
				}
				$router_equipment_id=$EMD->router_equipment_id;
				$e_type=substr($router_equipment_id,2,2);

				//删除虚拟空调设备
				if($e_type=="02" || $e_type=="03"){
					Control::model()->deleteAll('equipment_id=:equipment_id',array(':equipment_id'=>$equipment_id));
				}

				//删除设备表中的信息
				$EM->deleteByPK($equipment_id);

				//删除场景设备表中的信息
				$SE_model=SceneEquipment::model();
				//$SE_bool=$SE_model->exists("equipment_id=:equipment_id",array(":equipment_id"=>$equipment_id));
				$SE_model->deleteAll("equipment_id=:equipment_id AND type=:type",array(":equipment_id"=>$equipment_id,':type'=>$e_type));

				//删除用户设备表中的信息
				$UE_model=UserEquipment::model();
				$UE_model->deleteAll("equipment_id=:equipment_id",array(":equipment_id"=>$equipment_id));

				$transaction->commit();
				return true;
			}catch(Exception $e){
				$transaction->rollback();
				return false;
			}
		}
		//虚拟空调设备
		if(intval($del_type)===2){
			try{
				$CM=Control::model();
				$CMD=$CM->findByPK($equipment_id);
				if(empty($CMD)){
					return false;
				}
				$e_id=$CMD->equipment_id;
				$e_data=Equipment::model()->findByPK($e_id);
				if(empty($e_data)){
					return false;
				}
				$router_equipment_id=$e_data->router_equipment_id;
				$e_type=substr($router_equipment_id,2,2);

				//删除设备表中的信息
				$CM->deleteByPK($equipment_id);

				//删除场景设备表中的信息
				$SE_model=SceneEquipment::model();
				//$SE_bool=$SE_model->exists("equipment_id=:equipment_id",array(":equipment_id"=>$equipment_id));
				$SE_model->deleteAll("equipment_id=:equipment_id AND type=:type",array(":equipment_id"=>$equipment_id,':type'=>$e_type));

				$transaction->commit();
				return true;
			}catch(Exception $e){
				$transaction->rollback();
				return false;
			}
		}
		
			
	}

	/**
	 * App获取路由下的所有物理设备信息
	 */
	public function AllEquipment($router_id,$user_id){
		$ED=$this->findAllByAttributes(array('router_id'=>$router_id));
		if(!empty($ED)){
			foreach($ED as  $ED_val){
				$equipment['id']=$ED_val->id;
				$equipment['router_id']=$ED_val->router_id;
				$equipment['status']=$ED_val->status;
				$equipment['router_equipment_id']=$ED_val->router_equipment_id;

				//设备的种类
				$equipment['category']=substr($equipment['router_equipment_id'],2,2);
				$Cate=Category::model()->findByAttributes(array('code'=>$equipment['category']));
				if(!empty($Cate)){
					$equipment['category_name']=$Cate->name;
				}else{
					return false;
				}

				//获取设备别名
				$UE_model=UserEquipment::model();
				$UE_data=$UE_model->findByAttributes(array('equipment_id'=>$equipment['id'],'user_id'=>$user_id));
				if(empty($UE_data)){
					$equipment['name']=$equipment['router_equipment_id'];
				}else{
					$equipment['name']=$UE_data->name;
				}
				$arr[]=$equipment;
			}
			return $arr;
		}else{
			return false;
		}
	}

	/**
	 * App上报新配对的设备
	 */
	public function  match($user_id,$net_router_id,$net_equipment_id,$name,$scene_id,$status){
		$time=current_time();
		$RD=Router::model()->findByAttributes(array('net_router_id'=>$net_router_id));
		if(empty($RD)){
			return false;
		}
		$router_id=$RD->id;
		$E_exists=$this->findByAttributes(array('router_id'=>$router_id,'router_equipment_id'=>$net_equipment_id));

		$transaction=Yii::app()->db->beginTransaction();
		try{
			if(!empty($E_exists)){
				$equipment_id=$E_exists->id;
				$UEM=new UserEquipment;
				$UEM->user_id=$user_id;
				$UEM->equipment_id=$equipment_id;
				$UEM->name=$name;
				$UEM->ctime=$time;
				$UEM->mtime=$time;
				$UEM->save();

				if(intval($scene_id)!=0){
					$bool=$this->addAOE($scene_id,$equipment_id);
					if(!$bool){
						return false;
					}
				}
				
			}else{
				$NEM=new Equipment;
				$NEM->router_id=$router_id;
				$NEM->router_equipment_id=$net_equipment_id;
				$NEM->status=$status;
				$NEM->ctime=$time;
				$NEM->mtime=$time;
				$NEM->save();
				$equipment_id=$NEM->attributes['id'];

				$UEM=new UserEquipment;
				$UEM->user_id=$user_id;
				$UEM->equipment_id=$equipment_id;
				$UEM->name=$name;
				$UEM->ctime=$time;
				$UEM->mtime=$time;
				$UEM->save();

				if(intval($scene_id)!=0){
					$bool=$this->addAOE($scene_id,$equipment_id);
					if(!$bool){
						return false;
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
	 * App 添加设备 
	 */
	public function addAOE($scene_id,$equipment_id){
		$time=current_time();
		$number=array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24);

		$SE_model=SceneEquipment::model();
		$SE_data=$SE_model->findAllByAttributes(array('scene_id'=>$scene_id));
		if(!empty($SE_data)){
			foreach($SE_data as $SE_val){
				$has_position[]=$SE_val->position;
			}
		}else{
			$has_position=array();
		}
		//场景下可使用的位置
		$able_position=array_diff($number,$has_position);

		//设备类型
		$ED=Equipment::model()->findByPK($equipment_id);
		if(empty($ED)){
			return false;
		}
		$router_equipment_id=$ED->router_equipment_id;
		$type=substr($router_equipment_id,2,2);

		//确定位置
		$index=array_rand($able_position);
		$position=$able_position[$index];
		unset($able_position[$index]);

		//判断是否已存在
		$bool=SceneEquipment::model()->exists('scene_id=:scene_id AND  equipment_id=:equipment_id AND type=:type',array(':scene_id'=>$scene_id,':equipment_id'=>$equipment_id,':type'=>$type));
		if(!$bool){
			$SE_NEW=new SceneEquipment;
			$SE_NEW->scene_id=$scene_id;
			$SE_NEW->equipment_id=$equipment_id;
			$SE_NEW->type=$type;
			$SE_NEW->position=$position;
			$SE_NEW->ctime=$time;
			$SE_NEW->mtime=$time;
			if(!$SE_NEW->save()){
				return false;
			}
		}
		return true;
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Equipment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
