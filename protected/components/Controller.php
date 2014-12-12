<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	public function beforeAction($action){
		parent::beforeAction($action);
		$ip = $_SERVER['REMOTE_ADDR'];
                	$request_data = json_encode($_REQUEST);
                	$controller_name=$this->getId();
                	$action_name=$this->getAction()->getId();
               	$interface_name =$controller_name.'/'.$action_name;

	             $session_id = session_id();
	             $login = isset(Yii::app()->user->id) ? Yii::app()->user->id : '';

	            // $LoginController=array('shop/sgcomment','shop/reserve','shop/haddress','shop/caddress','shop/add_address','shop/interseller','shop/blind','shop/smcomment','shop/sscomment','shop/shopReserve','shop/selfComment');
	             $LoginController=array();
	             $current_interface= strtolower($interface_name);
	             if(in_array($current_interface,$LoginController)){
	             	if(empty($login)){
	             		echo  json_encode(array('status'=>'300','message'=>'尚未登录'));
	             		Rain::app()->end();
	             	}
	             }

	             myAccessLog("ip:{$ip},request_data:{$request_data},interface:{$interface_name},key:{$session_id},login:{$login}");
	             return true;
	}
}