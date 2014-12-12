<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>小悠路由后台管理系统</title>
		<link href="<?php echo Yii::app()->baseUrl;?>/style/images/admin/favicon.ico" rel="shortcut icon"/>
		<link href="<?php echo Yii::app()->baseUrl;?>/style/css/admin.css" rel="stylesheet" type="text/css"/>
	</head>
	<body id="loginBody">
		<div id="loginBezel">
	 		<div id="loginChildTop">
	 			&nbsp;
	 			<img src="<?php echo Yii::app()->baseUrl;?>/style/images/admin/logo-right.png" width="57" height="50" align="absbottom" /> 
	 			小悠后台管理系统
	 		</div>
			<div id="loginChildBottom">
				<table cellpadding="0" cellspacing="0" width="100%" height="95%" border="0">
					<?php $form = $this->beginWidget('CActiveForm') ?>
						<tr> <!--用户名-->
							<td align="right" width="35%">
								<?php echo $form->label($adminModel,'name') ?>
							</td>
							<td align="left" width="65%">
								<?php echo $form->textField($adminModel,'name',array('id'=>'account','class'=>'inputUP'))?>
							</td>
						</tr>
						<tr> <!--密码-->
							<td align="right">
								<?php echo $form->label($adminModel,'password')?>
							</td> 
							<td align="left">
								<?php echo $form->passwordField($adminModel,'password',array('id'=>'pwd','class'=>'inputUP'))?>
							</td>
						</tr>
						<tr> <!--验证码-->
							<td align="right"><?php echo  $form->label($adminModel,'captcha')?></td>
							<td align="left">
								<?php echo $form->textField($adminModel,'captcha',array('id'=>'checkNum')) ?>&nbsp;
								<span id="checkNumResult">
									<?php $this->widget('CCaptcha',array('showRefreshButton'=>false,'clickableImage'=>true,'imageOptions'=>array('alt'=>'验证码','title'=>'点击换图','style'=>'cursor:pointer','id'=>'checkImage','align'=>'absbottom')));?>	
								</span>
							</td>
						</tr>
						<tr>
							<td align="center" colspan="2"><input type="submit"  id="toLogin" value="登　录"/> </td>
						</tr>
					<?php $this->endWidget();?>

				</table>
			</div>
			<div id="loginErrorMessage">
				<ul>
					<li><?php echo $form->error($adminModel,'captcha',array('class'=>'errorImage'))?></li>
					<li><?php echo $form->error($adminModel,'name',array('class'=>'errorImage'))?></li>
					<li><?php echo $form->error($adminModel,'password',array('class'=>'errorImage'))?></li>
				</ul>
			</div>
		</div>
	  	<script src="<?php echo  Yii::app()->baseUrl; ?>/style/js/jquery.min.js"></script>
		<script src="<?php echo  Yii::app()->baseUrl; ?>/style/js/bootstrap.min.js"></script>
	  	<script src="<?php echo  Yii::app()->baseUrl; ?>/style/js/holder.min.js"></script>
		<script src="<?php echo  Yii::app()->baseUrl; ?>/style/js/admin.js"></script>
	</body>
</html>


