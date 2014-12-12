<?php
class ControlController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}
	
	/**
	 * 遥控器编辑更新
	 */
	public function actionUpdateC(){
		/*
		$_REQUEST['instruct_data']=array(
			array('instruct'=>'11111111111','instruct_name'=>"音量加"),
			array('instruct'=>'2222222222','instruct_name'=>"音量减"),
			);
		$_REQUEST['instruct_data']=json_encode($_REQUEST['instruct_data']);
		*/
		$name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
		$equipment_id=isset($_REQUEST['equipment_id'])?$_REQUEST['equipment_id']:"";
		$brand_id=isset($_REQUEST['brand_id'])?$_REQUEST['brand_id']:"";
		$control_type=isset($_REQUEST['control_type'])?$_REQUEST['control_type']:"";
		$code_type=isset($_REQUEST['code_type'])?$_REQUEST['code_type']:"";
		$instruct_data=isset($_REQUEST['instruct_data'])?$_REQUEST['instruct_data']:"";
		$isStudy=isset($_REQUEST['isStudy'])?$_REQUEST['isStudy']:"";
		$app_time=isset($_REQUEST['time'])?$_REQUEST['time']:"";
		if(empty($name) ||  empty($brand_id) || empty($control_type) || empty($code_type) ||empty($equipment_id) || empty($app_time)){
			echo json_encode(array('status'=>'300','message'=>'request error'));
			Yii::app()->end();
		}
		$time=date("Y-m-d H:i:s",$app_time);
		$transaction=Yii::app()->db->beginTransaction();
		try{
			Control::model()->updateByPK($equipment_id,array('mtime'=>$time,'name'=>$name,'brand_id'=>$brand_id,'control_type'=>$control_type,'code_type'=>$code_type));

			if(intval($isStudy)===1){
				if(!empty($instruct_data)){
					Instruct::model()->deleteAll('control_id=:control_id',array(':control_id'=>$equipment_id));
					$instruct_data = json_decode($instruct_data);
					foreach($instruct_data as  $instruct_val){
						$instruct=$instruct_val->instruct;
						$name=$instruct_val->instruct_name;
						$IM=new Instruct;
						$IM->control_id=$equipment_id;
						$IM->instruct=$instruct;
						$IM->name=$name;
						$IM->ctime=$time;
						$IM->mtime=$time;
						$IM->save();
					}
				}else{
					echo json_encode(array('status'=>'500','message'=>"error"));
					Yii::app()->end();
				}
			}
			
			$transaction->commit();
			echo json_encode(array('status'=>'200','message'=>"successs"));
			Yii::app()->end();
		}catch(Exception $e){
			$transaction->rollback();
			echo json_encode(array('status'=>'500','message'=>'save error'));
			Yii::app()->end();
		}
	}	

}

