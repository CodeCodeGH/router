<?php

/**
 * This is the model class for table "{{user_router}}".
 *
 * The followings are the available columns in table '{{user_router}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $router_id
 * @property string $alias
 * @property string $router_login_username
 * @property string $router_login_password
 * @property string $ctime
 * @property string $mtime
 */
class UserRouter extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_router}}';
	}

	/**
	 * App修改路由别名
	 */
	public function changeName($user_id,$router_id,$alias){
		$time=date("Y-m-d H:i:s",time());
		$count=$this->updateAll(array('alias'=>$alias,'mtime'=>$time),'user_id=:user_id  AND router_id=:router_id',array(':user_id'=>$user_id,':router_id'=>$router_id));
		if($count){
			return true;
		}else{
			return false;
		}

	}


	/**
	 * App 添加路由
	 */
	public function addRouter($user_id,$net_router_id,$alias,$router_login_password){
		$router_data=Router::model()->findByAttributes(array('net_router_id'=>$net_router_id));
		if(empty($router_data)){
			return 400;
		}

		$router_id=$router_data->id;
		$r_bool=UserRouter::model()->exists("router_id=:router_id AND  user_id=:user_id",array(':router_id'=>$router_id,':user_id'=>$user_id));
		if($r_bool){
			return 300;
		}

		$time=current_time();
		$transaction=Yii::app()->db->beginTransaction();
		try{
			//用户路由关联信息
			$RU_model=new UserRouter;
			$RU_model->user_id=$user_id;
			$RU_model->router_id=$router_id;
			$RU_model->alias=$alias;
			$RU_model->router_login_password=$router_login_password;
			$RU_model->ctime=$time;
			$RU_model->mtime=$time;
			$RU_model->save();

			//创建一个客厅场景
			$S_model=new Scene;
			$S_model->user_id=$user_id;
			$S_model->router_id=$router_id;
			$S_model->name="客厅";
			$S_model->position="default";
			$S_model->picture_id='1';
			$S_model->ctime=$time;
			$S_model->mtime=$time;
			$S_model->save();

			$transaction->commit();
			return 200;
		}catch(Exception $e){
			$transaction->rollback();
			return 500;
		}
		
	}



	

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserRouter the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
