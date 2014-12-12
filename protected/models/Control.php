<?php
/**
 * This is the model class for table "{{control}}".
 *
 * The followings are the available columns in table '{{control}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $equipment_id
 * @property integer $scene_id
 * @property integer $brand_id
 * @property string $ctime
 * @property string $mtime
 */
class Control extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{control}}';
	}
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Control the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
