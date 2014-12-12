<?php

/**
 * This is the model class for table "{{admin}}".
 *
 * The followings are the available columns in table '{{admin}}':
 * @property integer $id
 * @property string $name
 * @property string $password
 * @property string $ctime
 * @property string $mtime
 */
class Admin extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public  $captcha;
	public function tableName()
	{
		return '{{admin}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required','message'=>"用户名不能为空"),
			array('password','required','message'=>'密码不能为空'),
			array('captcha','captcha','message'=>'验证码错误','on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => '帐&nbsp;&nbsp;&nbsp;号：',
			'password' => '密&nbsp;&nbsp;&nbsp;码：',
			'captcha'=>'验证码：'
		);
	}

	/**
	 * 验证用户名密码是否存在
	 * @param $name用户名
	 * @param $password密码
	 * @param $type用户类型 0普通用户 1店铺用户 2系统管理员
	 */
	public function identifityUser($name,$password){
	    $userData=$this->find('name=:name AND password=:password',array(':name'=>$name,':password'=>$password));
	    if(empty($userData)){
	        $this->addError('password','用户名或密码错误');
	        return false;
	    }else{
	            Yii::app()->session['adminUserName']=$userData->name;
	            Yii::app()->session['adminUserId']=$userData->id;
	            return true;
	    }
	}

	/**
	 * 后台获取系统列表
	 */
	public function adminList(){
		$criteria=new CDbCriteria();
		$criteria->order='id ASC';
		$count=$this->count($criteria);//计算总条数

		$pager=new CPagination($count);
		$pager->pageSize=8;
		$pager->applyLimit($criteria);

		$adminData=$this->findAll($criteria);
		$arr['adminData']=$adminData;
		$arr['pager']=$pager;
		return $arr;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Admin the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
