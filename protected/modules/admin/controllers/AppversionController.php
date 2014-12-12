<?php

class AppversionController extends Controller
{
	//查看版本号
	public function actionAvshow()
	{
		$criteria=new CDbCriteria();
		$criteria->order='ctime DESC,type ASC';
		$data=Appversion::model()->findAll($criteria);
		$this->render('avshow',array('data'=>$data));
		Yii::app()->end();
	}

	//版本上传
	public function  actionUpload(){
		if(empty($_POST['name']) || empty($_POST['type']) || empty($_FILES['file']['name'])){
			Yii::app()->user->setFlash('appVersionInfo','Error');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}
		$name=$_POST['name'];
		$type=$_POST['type'];
		$time=current_time();

		$file=CUploadedFile::getInstanceByName('file');
		$srcName=$file->name;
		$suffix=strrchr($srcName,'.') ;
		$pathTime=date("Y-m-d_H-i-s",time());
		if($type=='1'){
			$typeName="android";
		}elseif($type=='2'){
			$typeName="ios";
		}else{
			$typeName="error";
		}
		$pathname=$typeName."_".$pathTime."_".rand(9,9999).$suffix;
		$path=Yii::app()->basePath.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'appversion'.DIRECTORY_SEPARATOR;
		if(!is_dir($path)){
			mkdir($path,0777);
		}
		$uploadUrl=$path.$pathname;
		$bool=$file->saveAs($uploadUrl);
		if($bool){
			$av_model=new Appversion;
			$av_model->type=$type;
			$av_model->name=$name;
			$av_model->path=$pathname;
			$av_model->ctime=$time;
			$av_model->mtime=$time;
			if($av_model->save()>0){
				Yii::app()->user->setFlash('appVersionInfo','SUCCESS');
				$this->redirect(Yii::app()->request->urlReferrer);
				Yii::app()->end();
			}else{
				@unlink($uploadUrl);
				Yii::app()->user->setFlash('appVersionInfo','saveError');
				$this->redirect(Yii::app()->request->urlReferrer);
				Yii::app()->end();
			}
		}else{
			@unlink($uploadUrl);
			Yii::app()->user->setFlash('appVersionInfo','uploadError');
			$this->redirect(Yii::app()->request->urlReferrer);
			Yii::app()->end();
		}
	}

}