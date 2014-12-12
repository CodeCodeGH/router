<?php

/**
 * This is the model class for table "{{advert}}".
 *
 * The followings are the available columns in table '{{advert}}':
 * @property integer $id
 * @property string $small_picture
 * @property string $big_picture
 * @property string $url
 * @property string $status
 * @property string $ctime
 * @property string $mtime
 */
class Advert extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{advert}}';
	}


	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	public function getAdvert(){
		$criteria=new CDbCriteria;
		$criteria->select="id,small_picture,big_picture,url";
		$criteria->condition="status='1' ";
		$criteria->order="mtime DESC";
		$criteria->limit="3";
		$advert_model=Advert::model();
		$advert_data=$advert_model->findAll($criteria);
		if(empty($advert_data)){
			return false;
		}else{
			foreach($advert_data as $advert_val){
				$advert['id']=$advert_val->id;
				$advert['small_picture']=$advert_val->small_picture;
				$advert['big_picture']=$advert_val->big_picture;
				$advert['url']=$advert_val->url;
				$arr[]=$advert;
			}
			return $arr;
		}


	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Advert the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
