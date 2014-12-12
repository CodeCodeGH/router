<?php
class InstructController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	/**
	 * 学习型遥控器编码同步更新
	 */
	public function actionSynCode(){
		$user_id=isset($_REQUEST['user_id'])?$_REQUEST['user_id']:"";
		$equipment_id=isset($_REQUEST['equipment_id'])?$_REQUEST['equipment_id']:"";
		$time=isset($_REQUEST['time'])?$_REQUEST['time']:"";
		if(empty($equipment_id) || empty($user_id) || empty($time)){
			echo json_encode(array('status'=>'300','message'=>'no user_id  or  equipment_id or time'));
			Yii::app()->end();
		}
		$CM=Control::model();
		$CMD=$CM->findByAttributes(array('id'=>$equipment_id,'user_id'=>$user_id));

		$mtime=$CMD->mtime;
		$mtime=strtotime($mtime);
		//文件已是最新
		if($mtime==$time){
			echo json_encode(array('status'=>'400','message'=>"文件已是最新"));
			Yii::app()->end();
		}

		//文件不是最新
		$data=Instruct::model()->findAllByAttributes(array('control_id'=>$equipment_id));
		if(empty($data)){
			echo json_encode(array('status'=>'500','message'=>'no data'));
			Yii::app()->end();
		}

		foreach($data as $val){
			$button['instruct']=$val->instruct;
			$button['name']=$val->name;
			$arr[]=$button;
		}
		$set['button']=$arr;
		$set['mtime']=$mtime;
		echo json_encode(array('status'=>'200','message'=>$set));
		Yii::app()->end();
	}
}
