<?php
	if(!Yii::app()->session['adminUserId']) {
		$this->redirect(array('default/login'));
		YIi::app()->end();
	}
	 $controller=strtolower(Yii::app()->controller->id);
	 $action=strtolower($this->getAction()->getId());
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>小悠路由后台管理系统</title>
        <link href="<?php echo Yii::app()->baseUrl;?>/style/images/admin/favicon.ico" rel="shortcut icon"/>
        <link href="<?php echo Yii::app()->baseUrl;?>/style/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
          <link href="<?php echo Yii::app()->baseUrl;?>/style/css/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo Yii::app()->baseUrl;?>/style/css/admin.css" rel="stylesheet" type="text/css"/>
        <script src="<?php echo  Yii::app()->baseUrl; ?>/style/js/jquery.min.js"></script>
        <script src="<?php echo  Yii::app()->baseUrl; ?>/style/js/bootstrap.min.js"></script>
        <script src="<?php echo  Yii::app()->baseUrl; ?>/style/js/bootstrap-select.min.js"></script>
        <script src="<?php echo  Yii::app()->baseUrl; ?>/style/js/holder.min.js"></script>
        <script src="<?php echo  Yii::app()->baseUrl; ?>/style/js/admin.js"></script>
    </head>
<body  style="background:url(<?php echo Yii::app()->baseUrl ?>/style/images/admin/back3.jpg) no-repeat;">
<div class="bezel" id="bezel-id" style="height:1200px">
    <div class="head">
        <div class="hleft"><a href="<?php echo $this->createUrl('default/index') ?>"><span class="head-icon">小悠管理系统</span></a></div>
        <div class="hright">
        	<span>欢迎登录使用！管理员：<?php echo Yii::app()->session['adminUserName']?> &nbsp;&nbsp; <a class="outLogin" href="<?php echo $this->createUrl('admin/admindetail',array('admin_id'=> Yii::app()->session['adminUserId'])) ?>">修改口令</a>　<a class="outLogin" href="<?php echo $this->createUrl('default/logout') ?>">退出系统</a></span>
        </div>
    </div>
    <div class="center">
        <div class="cleft" id="cleft-id">	
            <h4  <?php  if($controller=="admin"){ echo " class='on' " ;} ?> >用户管理</h4>
            <ul   <?php  if($controller=="admin"){ echo " style='display:block' " ;} ?>  >
            	<li   <?php  if( ($action=="app")  ||  ($action=="appdetail") || ($action=="ur") || ($action=="scene")){ echo " class='on' " ;} ?> ><a href="<?php echo $this->createUrl('admin/app') ?>">App用户</a></li>
            	<li   <?php  if( ($action=="system") || ($action=="admindetail")){ echo " class='on' " ;} ?> ><a href="<?php echo $this->createUrl('admin/system') ?>">系统用户</a></li>
            </ul>
            <h4 <?php  if($controller=="router"){ echo " class='on' " ;} ?> >路由管理</h4>
            <ul  <?php  if($controller=="router"){ echo " style='display:block' " ;} ?> >
            	<li  <?php  if($action=="show"){ echo " class='on' " ;} ?> ><a href="<?php echo $this->createUrl('router/show') ?>">路由列表</a></li>
            </ul>
             <h4 <?php  if($controller=="appversion"){ echo " class='on' " ;} ?> >版本管理</h4>
            <ul  <?php  if($controller=="appversion"){ echo " style='display:block' " ;} ?> >
            <li  <?php  if($action=="avshow"){ echo " class='on' " ;} ?> ><a href="<?php echo $this->createUrl('appversion/avshow') ?>">App版本</a></li>
           <!--   <li  <?php  if($action=="otashow"){ echo " class='on' " ;} ?> ><a href="<?php echo $this->createUrl('ota/otashow') ?>">OTA升级</a></li> -->
            </ul>
        </div>
       
        <div class="rleft">
            <div class="jumbotron center-block">
                          <div id="showInfo" style="line-height:250px;z-index:100"></div>
	              <?php
			echo $content;
		 ?>

            </div>
        </div>
    </div>
    <div class="floot">版权所有</div> 
</div>
</body>
</html>
