<?php

class UserController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}
	
	public function actionUR(){
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		if(empty($user_id)){
			echo json_encode(array('status'=>'300','message'=>'no user_id'));
			Yii::app()->end();
		}
		$userData=UserRouter::model()->findAllByAttributes(array('user_id'=>$user_id));
		if(empty($userData)){
			echo json_encode(array('status'=>'400','message'=>'no data'));
			Yii::app()->end();
		}
		
		$arr=array();
		foreach($userData as $userVal){
			$router_id=$userVal->router_id;
			$router_data=Router::model()->findByPK($router_id);
			if(empty($router_data)){
				continue;
			}
			$net_router_id=$router_data->net_router_id;
			$router['router_id']=$router_id;
			$router['net_router_id']=$net_router_id;
			$alias=$userVal->alias;
			if(empty($alias)){
				$router['alias']=$router['net_router_id'];
			}else{
				$router['alias']=$alias;
			}
			$arr[]=$router;
		}

		echo json_encode(array('status'=>'200','message'=>$arr));
		Yii::app()->end();
	}
	
}
