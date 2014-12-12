<?php

class VersionController extends Controller
{
	/**
	 * 用户版本号
	 */
	public function actionVersion(){
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		if(empty($user_id)){
			echo json_encode(array('status'=>'300','message'=>'no user_id'));
			Yii::app()->end();
		}
		$UR_model=Router::model();
	
	}
}