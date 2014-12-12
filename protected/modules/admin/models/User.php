<?php
class User extends CActiveRecord{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user}}';
	}

	/**
	 * 后台获取前台用户列表
	 */
	public function adminUserList($phone){
		$criteria=new CDbCriteria();
		$criteria->order='id ASC';
		if(!empty($phone)){
			$criteria->addSearchCondition('phone',$phone);
		}
		$count=$this->count($criteria);//计算总条数

		$pager=new CPagination($count);
		$pager->pageSize=12;
		$pager->applyLimit($criteria);

		$userData=$this->findAll($criteria);
		$arr['userData']=$userData;
		$arr['pager']=$pager;
		return $arr;
	}

	/**
	 * 获取用户的所有路由信息
	 */
	public function  adminUR($user_id){
		UserRouter::model()->findAll($user_id);
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