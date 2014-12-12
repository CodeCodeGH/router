<?php 
class CommonController extends Controller{

	/**
	 * 给app端返回操作情况
	 */
	public  function  actionReturnToApp(){
		sleep(1);
		$id=isset($_REQUEST['id'])?$_REQUEST['id']:"";
		if(empty($id)){
			echo json_encode(array('status'=>'300','message'=>'no id'));
			Yii::app()->end();
		}
		$temp_model=Temp::model();
		$temp_data=$temp_model->findByPK($id);
		if(empty($temp_data)){
			echo json_encode(array('status'=>'500','message'=>'last implement error'));
			Yii::app()->end();
		}
		$status=$temp_data->status;
		if(intval($status)===1){
			echo json_encode(array('status'=>'200','message'=>'SUCCESS'));
			Yii::app()->end();
		}
		if(intval($status)===0){
			echo json_encode(array('status'=>'500','message'=>'router  not  do'));
			Yii::app()->end();
		}
		if(intval($status)===2){
			echo json_encode(array('status'=>'500','message'=>'router do  error'));
			Yii::app()->end();
		}
	}



	/**
	 *路由器端  获取当前路由需要操作的指令
	 */
	public function  actionRGetOperation(){
		$ip = $_SERVER['REMOTE_ADDR'];
                	$request_data =isset($GLOBALS['HTTP_RAW_POST_DATA'])?$GLOBALS['HTTP_RAW_POST_DATA']:"未传参数";
                	$controller_name=strtolower($this->getId());
                	$action_name=strtolower($this->getAction()->getId());
               	$interface_name =$controller_name.'/'.$action_name;
               	RouterAccessLog("ip:{$ip},request_data:{$request_data},interface:{$interface_name}");
		

		if(empty($GLOBALS['HTTP_RAW_POST_DATA'])){
			echo json_encode(array('status'=>'300','message'=>'no net_router_id'));
			Yii::app()->end();
		}
		$http=json_decode($GLOBALS['HTTP_RAW_POST_DATA']);

		$net_router_id=$http->net_router_id;		


		//修改路由器的访问时间
		$time=current_time();
		$isSuccess=Router::model()->updateAll(array('access_time'=>$time,'mtime'=>$time),'net_router_id=:net_router_id',array(':net_router_id'=>$net_router_id));
		if(!$isSuccess){
			echo json_encode(array('status'=>'500','message'=>'error'));
			Yii::app()->end();
		}

		$temp_model=Temp::model();
		$temp_data=$temp_model->findAll("net_router_id=:net_router_id AND status=:status",array(':net_router_id'=>$net_router_id,':status'=>'0'));
		if(empty($temp_data)){
			echo json_encode(array('status'=>'400','message'=>'no data'));
			Yii::app()->end();
		}else{
			$i=1;
			foreach($temp_data  as $temp_val){
				$operation['id'.$i]=$temp_val->id;
				$operation['net_router_id'.$i]=$temp_val->net_router_id;
				$operation['net_equipment_id'.$i]=$temp_val->net_equipment_id;
				$operation['operation_type'.$i]=$temp_val->operation_type;
				$operation['operation'.$i]=$temp_val->operation;
				$i++;
				$temp_model->updateByPK($temp_val->id,array('status'=>'0','mtime'=>$time));
			}
			$c=$i-1;
			$operation['count']="$c";
			$operation['status']="200";
			//$operation['message']="success";
			//P($operation);
			echo json_encode($operation);
			Yii::app()->end();
		}

	}

	/**
	 *路由器端 返回修改状况 
	 */
	public function actionGetReturn(){
		// $_REQUEST=array(
		// 	'id'=>'1',
		// 	'net_router_id'=>'00:0C:43:76:20:88',
		// 	'result'=>'200',
		// 	'count'=>'1',
		// 	'net_equipment_id1'=>'55024950524f',
		// 	'status1'=>'01',
		// );
		if(empty($GLOBALS['HTTP_RAW_POST_DATA'])){
			echo json_encode(array('status'=>'300','message'=>"no data"));
			Yii::app()->end();
		}
		$temp_model=Temp::model();
		$data=json_decode($GLOBALS['HTTP_RAW_POST_DATA'],true);

		$temp_model=Temp::model();
		$bool=$temp_model->getRouterReturn($data);
		if($bool){
			echo json_encode(array('status'=>'200','message'=>"SUCCESS"));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'update error'));
			Yii::app()->end();
		}
		
	}

	/**
	 * App 从离线到在线 端同步场景信息
	 *@param String  net_equipment_ids 路由器端的设备id
	 */
	public function actionSynchronization(){
		// $_REQUEST['user_id']=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		// $_REQUEST['scene']=array(
		// 	array('net_router_id'=>"00:0C:43:76:20:88",
		// 	          'scene_info'=>array(
		// 				array('scene_id'=>'1','scene_name'=>'场景名字','picture_id'=>'1','net_equipment_info'=>array(
		// 						array('net_equipment_id'=>'55024950524f','position'=>'1'),
		// 						array('net_equipment_id'=>'56064950524f','position'=>'2')
		// 					)),
		// 				array('scene_id'=>'0','picture_id'=>'1','scene_name'=>'刚添加的','net_equipment_info'=>array(
		// 						array('net_equipment_id'=>'57084950524f','position'=>'1'),
		// 					)),
		// 			)
		// 	),
		// 	array('net_router_id'=>"00:01:6C:06:A6:29",
		// 	          'scene_info'=>array(
		// 	          			array('scene_id'=>'5','picture_id'=>'1','scene_name'=>'场景','net_equipment_info'=>array(
		// 	          					array('net_equipment_id'=>'60084950524f','position'=>'1'),
		// 	          				)),	
		// 	          	)
		// 	)
		// );

		// $_REQUEST['scene']=json_encode($_REQUEST['scene']);
		// echo $_REQUEST['scene'];
		// Yii::app()->end();
		if(empty($_REQUEST['scene']) || empty($_REQUEST['user_id'])){
			echo json_encode(array('status'=>'300','message'=>'no user or scene'));
			Yii::app()->end();
		}
		$scene=$_REQUEST['scene'];
		$user_id=$_REQUEST['user_id'];
		$scene_model=Scene::model();
		$bool=$scene_model->appSynchronization($scene,$user_id);
		if($bool){
			echo json_encode(array('status'=>'200','message'=>'success'));
			Yii::app()->end();
		}else{
			echo json_encode(array('status'=>'500','message'=>'error'));
			Yii::app()->end();
		}
	}

	
}
