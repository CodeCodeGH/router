<?php
class UserRouter extends CActiveRecord{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_router}}';
	}



	public function findRouter($user_id){
		$connection=Yii::app()->db;
		$sql="select  ur.id as urid,ur.router_id as router_id,ur.user_id,ur.alias,ur.ctime,ur.mtime,ur.router_login_password,r.net_router_id,r.mac,r.version,r.model,r.access_time  from  {{user_router}}  as ur  left  join  {{router}} as  r  ON ur.router_id=r.id  where ur.user_id=$user_id";

		$results=$connection->createCommand($sql)->queryAll();	
		if(empty($results)){
			return false;
		}else{
			return $results;
		}
		
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
?>