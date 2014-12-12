<?php

/**
 * This is the model class for table "{{aircondition}}".
 *
 * The followings are the available columns in table '{{aircondition}}':
 * @property integer $id
 * @property integer $equipment_id
 * @property string $mode
 * @property string $temperature
 * @property string $ctime
 * @property string $mtime
 */
class Aircondition extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{aircondition}}';
	}





	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Aircondition the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
