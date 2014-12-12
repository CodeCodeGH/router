<?php

class SceneController extends Controller
{
	/**
	 * App删除场景
	 */
	public function actionDel(){
		$scene_id=isset($_REQUEST['scene_id'])?$_REQUEST['scene_id']:"";
		if(empty($scene_id) ){
			echo json_encode(array('status'=>'300','message'=>'no scene_id'));
			Yii::app()->end();
		}
		$scene_model=Scene::model();
		$scene_bool=$scene_model->appDel($scene_id);
		if($scene_bool){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'delete error'));
			Yii::app()->end();
		}
	}

	/**
	 * App增加场景
	 */
	public function  actionAdd(){
		$router_id=isset($_REQUEST['router_id'])?$_REQUEST['router_id']:"";
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
		$position=isset($_REQUEST['position'])?$_REQUEST['position']:"default";
		$picture_id=isset($_REQUEST['picture_id'])?$_REQUEST['picture_id']:"";
		if(empty($router_id) || empty($name) ||  empty($picture_id) || empty($user_id)){
			echo json_encode(array('status'=>'300','message'=>'request error'));
			Yii::app()->end();
		}
		$scene_model=Scene::model();
		$data=$scene_model->addScene($router_id,$name,$position,$picture_id,$user_id);

		if(intval($data['type'])===300){
			echo json_encode(array('status'=>'300','message'=>'场景名字已存在'));
			Yii::app()->end();
		}

		if(intval($data['type'])===500){
			echo json_encode(array('status'=>'500','message'=>'SAVE  ERROR'));
			Yii::app()->end();
		}

		echo json_encode(array('status'=>'200','message'=>$data['message']));
		Yii::app()->end();
	}

	/**
	 *APP 修改场景的名字
	 */
	public function actionChangeName(){
		$scene_id=isset($_REQUEST['scene_id'])?$_REQUEST['scene_id']:"";
		$name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$router_id=isset($_REQUEST['router_id'])?$_REQUEST['router_id']:"";
		if(empty($scene_id) || empty($name) || empty($user_id) || empty($router_id)){
			echo json_encode(array('status'=>'300','message'=>'no scene_id'));
			Yii::app()->end();
		}

		$hasName=Scene::model()->find('user_id=:user_id  AND  router_id=:router_id  AND  name=:name',array(':user_id'=>$user_id,':router_id'=>$router_id,':name'=>$name));
		if(!empty($hasName)){
			echo json_encode(array('status'=>'400','message'=>'重名'));
			Yii::app()->end();
		}

		$time=date("Y-m-d H:i:s",time());
		$scene_model=Scene::model()->updateByPK($scene_id,array('name'=>$name,'mtime'=>$time));
		if($scene_model>0){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'update error'));
			Yii::app()->end();
		}

	}

	/**
	 *APP 修改场景的图片
	 */
	public function actionChangePicture(){
		$scene_id=isset($_REQUEST['scene_id'])?$_REQUEST['scene_id']:"";
		$picture_id=isset($_REQUEST['picture_id'])?$_REQUEST['picture_id']:"";
		if(empty($scene_id) || empty($picture_id)){
			echo json_encode(array('status'=>'300','message'=>'no scene_id'));
			Yii::app()->end();
		}
		$time=date("Y-m-d H:i:s",time());
		$scene_model=Scene::model()->updateByPK($scene_id,array('picture_id'=>$picture_id,'mtime'=>$time));
		if($scene_model>0){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'update error'));
			Yii::app()->end();
		}
	}

	/**
	 *APP 修改场景的图片和名字
	 */
	public function actionChangePN(){
		$scene_id=isset($_REQUEST['scene_id'])?$_REQUEST['scene_id']:"";
		$picture_id=isset($_REQUEST['picture_id'])?$_REQUEST['picture_id']:"";
		$name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$router_id=isset($_REQUEST['router_id'])?$_REQUEST['router_id']:"";
		if(empty($scene_id) || empty($picture_id) || empty($name) || empty($router_id) || empty($router_id)){
			echo json_encode(array('status'=>'300','message'=>'no scene_id picture_id  name'));
			Yii::app()->end();
		}
		
		$hasName=Scene::model()->find('user_id=:user_id  AND  router_id=:router_id  AND  name=:name AND id!=:scene_id',array(':user_id'=>$user_id,':router_id'=>$router_id,':name'=>$name,':scene_id'=>$scene_id));
		if(!empty($hasName)){
			echo json_encode(array('status'=>'400','message'=>'重名'));
			Yii::app()->end();
		}

		$time=date("Y-m-d H:i:s",time());
		$scene_model=Scene::model()->updateByPK($scene_id,array('picture_id'=>$picture_id,'mtime'=>$time,'name'=>$name));
		if($scene_model>0){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'update error'));
			Yii::app()->end();
		}
	}

	/**
	 *App移动场景位置
	 *@param JSON_OBJECT $position   
	 */
	public function actionChangePosition(){
		// $_REQUEST['position']=array(
		// 	array('scene_id'=>1,'position'=>2),
		// 	array('scene_id'=>2,'position'=>1),
		// );
		// $_REQUEST['position']=json_encode($_REQUEST['position']);
		// echo $_REQUEST['position'];
		// Yii::app()->end();
		if(empty($_REQUEST['position'])){
			echo json_encode(array('status'=>'300','message'=>'no  position'));
			Yii::app()->end();
		}

		$position=json_decode($_REQUEST['position']);
		$time=date("Y-m-d H:i:s",time());
		foreach($position as  $p_val){
			$scene_id=$p_val->scene_id;
			$position=$p_val->position;
			$scene_model=Scene::model();
			$counter=$scene_model->updateByPK($scene_id,array('position'=>$position,'mtime'=>$time));
			if(!$counter){
				echo json_encode(array('status'=>'500','message'=>'update error'));
				Yii::app()->end();
			}
		}
		echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
		Yii::app()->end();
	}

	
	/**
	 * 可以添加设备的场景
	 */
	/**
	 * 可以添加设备的场景
	 */
	public function  actionAbleAdd(){
		$router_id=isset($_REQUEST['router_id'])?$_REQUEST['router_id']:"";
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		if(empty($router_id) || empty($user_id)){
			echo json_encode(array('status'=>'300','message'=>'no router_id'));
			Yii::app()->end();
		}

		$list=array();
		$SD=Scene::model()->findAllByAttributes(array('router_id'=>$router_id,'user_id'=>$user_id));
		if(empty($SD)){
			echo json_encode(array('status'=>'200','message'=>$list));
			Yii::app()->end();
		}

		foreach($SD as  $val){
			$scene_id=$val->id;
			$scene_name=$val->name;
			$count=SceneEquipment::model()->count('scene_id=:scene_id',array(':scene_id'=>$scene_id));
			if(intval($count)<24){
				$list['scene_id']=$scene_id;
				$list['scene_name']=$scene_name;
				$list['space']=abs(24-$count);
			}
			$arr[]=$list;
		}
		echo json_encode(array('status'=>'200','message'=>$arr));
		Yii::app()->end();
	}

	/**
	 * 场景删除数据
	 */
	public function actionDelem(){
		$scene_id=isset($_REQUEST['scene_id'])?$_REQUEST['scene_id']:"";
		$equipment_id=isset($_REQUEST['equipment_id'])?$_REQUEST['equipment_id']:"";
		$type=isset($_REQUEST['type'])?$_REQUEST['type']:"";
		if(empty($scene_id) || empty($equipment_id) || empty($type)){
			echo json_encode(array('status'=>'300','message'=>'request error'));
			Yii::app()->end();
		}
		$bool=SceneEquipment::model()->deleteAll('scene_id=:scene_id AND equipment_id=:equipment_id AND type=:type',array(':scene_id'=>$scene_id,'equipment_id'=>$equipment_id,'type'=>$type));
		if($bool){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'delete error'));
			Yii::app()->end();
		}
	}	


}
