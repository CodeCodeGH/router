<?php
/**
 * This is the model class for table "{{instruct}}".
 * The followings are the available columns in table '{{instruct}}':
 * @property integer $id
 * @property integer $control_id
 * @property string $instruct
 * @property string $name
 * @property string $ctime
 * @property string $mtime
 */
class Instruct extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{instruct}}';
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Instruct the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
