<?php

/**
 * This is the model class for table "{{user}}".
 *
 * The followings are the available columns in table '{{user}}':
 * @property string $id
 * @property string $app_name
 * @property string $password
 * @property string $real_name
 * @property string $phone
 * @property string $identification_card
 * @property integer $question_id
 * @property string $answer
 * @property string $status
 * @property string $ctime
 * @property string $mtime
 */
class User extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user}}';
	}

	/**
	 * App用户注册 判断用户名是否已存在 存在返回true，否则返回false并将用户名，密码放到session数组中
	 */
	public function register($app_name,$password,$phone){
		$exists=$this->exists("app_name=:name",array(':name'=>$app_name));
		if($exists){
			return 300;
		}
		$time=current_time();

		$user_new=new User();
		$user_new->app_name=$app_name;
		$user_new->password=md5($password);
		$user_new->phone=$phone;
		$user_new->ctime=$time;
		$user_new->mtime=$time;
		if($user_new->save()>0){
			return 200;
		}else{
			return 500;
		}
		
	}

	/**
	 * App用户登录 插入当前登录信息 修改用户登录状态 
	 */
	public function getCInfo($userId,$current_ip,$current_equipment){
		$current_time=date("Y-m-d H:i:s",time());

		$UserLogin=new UserLogin;
		$UserLogin->user_id=$userId;
		$UserLogin->current_ip=$current_ip;
		$UserLogin->current_equipment=$current_equipment;
		$UserLogin->ctime=$current_time;
		$UserLogin->mtime=$current_time;
		$transaction=Yii::app()->db->beginTransaction();
		try{
			//修改上次登录信息
			$this->changeLastInfo($userId);

			//插入当前登录信息
			$UserLogin->save();

			//修改用户登录状态,即更新当前登录的设备号
			$this->updateByPK($userId,array('isLogin'=>$current_equipment,'mtime'=>$current_time));

			//事务提交
			$transaction->commit();
			return true;
		}catch(Exception $e){
			$transaction->rollback();
			return false;
		}	
	}

	/**
	 * App用户登录  获取用户所有路由
	 */
	public function getAddInfo($userId){
		$current_time=date("Y-m-d H:i:s",time());
		$transaction=Yii::app()->db->beginTransaction();
		try{
	
			//获取用户所有路由
			$router_model=Router::model();
			$router_data=$router_model->getOnlineRouter($userId);
			//事务提交
			$transaction->commit();

			if(!empty($router_data)){
				return $router_data;
			}else{
				$router_data=array();
				return $router_data;
			}

		}catch(Exception $e){
			$transaction->rollback();
			return false;
		}
		
	}
		


	/**
	 * App用户查询上次登录信息
	 */
	public function lookLastLogin($userId){
		$lastInfo=$this->findByPK($userId);
		if(!empty($lastInfo)){
			$user['last_login_ip']=$lastInfo->last_login_ip;
			$user['last_login_equipment']=$lastInfo->last_login_equipment;
			$user['last_login_time']=$lastInfo->last_login_time;
			return $user;	
		}
	}



	/**
	 * App用户注销，更新用户表中上次登录信息
	 */
	public function changeLastInfo($userId){
		$time=date("Y-m-d H:i:s",time());

		$criteria=new CDbCriteria;
		$criteria->select="ctime,current_equipment,current_ip";
		$criteria->condition="user_id=:userId";
		$criteria->params=array(':userId'=>$userId);
		$criteria->order="id DESC";
		$data=UserLogin::model()->find($criteria);
		if(empty($data)){
			return false;
		}
		$last_login_time=$data->ctime;
		$last_login_equipment=$data->current_equipment;
		$last_login_ip=$data->current_ip;

		$count=$this->updateByPK($userId,array('isLogin'=>'0','last_login_equipment'=>$last_login_equipment,'last_login_ip'=>$last_login_ip,'last_login_time'=>$last_login_time,'mtime'=>$time));
		if($count>0){
			return true;
		}
		return false;
	}

	/**
	 * App 获取用户信息
	 */
	public function getInfo($user_id){
		$UR_model=UserRouter::model();
		$router_num=$UR_model->count("user_id=:user_id",array(':user_id'=>$user_id));

		$user_data=$this->findByPK($user_id);
		if($user_data){
			$user['app_name']=$user_data->app_name;
			$user['real_name']=$user_data->real_name;
			$user['phone']=$user_data->phone;
			$user['identification_card']=$user_data->identification_card;
			$user['router_num']=$router_num;
			return $user;
		}else{
			return false;
		}
		
		
	}

	/**
	 * 后台用户列表
	 */
	public function adminUserList(){
		$criteria=new CDbCriteria();
		$criteria->order='id ASC';
		$count=$this->count($criteria);//计算总条数

		$pager=new CPagination($count);
		$pager->pageSize=10;
		$pager->applyLimit($criteria);

		$userData=$this->findAll($criteria);
		$arr['userData']=$userData;
		$arr['pager']=$pager;
		return $arr;
	}	


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
