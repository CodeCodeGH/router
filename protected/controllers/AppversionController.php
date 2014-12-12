<?php

class AppversionController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	/**
	 * 获取app当前最新版本
	 * @type Enum  1安卓  2IOS
	 */
	public function actionVersion(){
		$type=isset($_REQUEST['type'])?$_REQUEST['type']:"";
		if(empty($type)){
			echo json_encode(array('status'=>'300','message'=>'no type'));
			Yii::app()->end();
		}
		$data=Appversion::model()->find('type=:type ORDER  BY  id  DESC',array(':type'=>$type));
		if(empty($data)){
			echo json_encode(array('status'=>'400','message'=>'no version'));
			Yii::app()->end();
		}
		$version['id']=$data->id;
		$version['name']=$data->name;
		$version['path']=$data->path;
		$version['type']=$data->type;
		echo json_encode(array('status'=>'200','message'=>$version));
		Yii::app()->end();
	}
	

}