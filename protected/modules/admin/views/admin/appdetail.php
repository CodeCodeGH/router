<div class="container-fluid">
	<div class="row" id="show-table">
		<div class="panel panel-primary">
			<div class="panel-heading">
				用户信息查看
			</div>
			<div class="panel-body">
				<?php  if(!empty($userData)){  ?>
				<?php  $this->beginWidget('CActiveForm',array('action'=>$this->createUrl('admin/appdetail'),'htmlOptions'=>array('class'=>'form-horizontal')));  ?>
				<input type="hidden" name="id" value="<?php echo $userData->id?>">
				<div class="form-group">
					<label class="col-md-2 text-right">用户名</label>
					<div class="col-md-8">
						<input  type="text" name="app_name"  class="form-control"  value="<?php echo $userData->app_name ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 text-right">密码</label>
					<div class="col-md-8">
						<input type="password" name="password" class="form-control"  placeholder="不填默认保持原密码">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 text-right">手机号</label>
					<div class="col-md-8">
						<input type="text"  name="phone"  class="form-control"  value="<?php echo $userData->phone ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 text-right">真实姓名</label>
					<div class="col-md-8">
						<input type="text" name="real_name" class="form-control" value="<?php  echo $userData->real_name ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 text-right">创建时间</label>
					<div class="col-md-8">
						<input type="text"  name="ctime"  class="form-control"  value="<?php  echo  $userData->ctime ?>"  disabled>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 text-right">修改时间</label>
					<div class="col-md-8">
						<input type="text" name="mtime"  class="form-control" value="<?php  echo  $userData->mtime?>"  disabled>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-10  text-center">
						<button type="submit"  class="btn btn-primary">Save</button>
					</div>
				</div>
				<?php $this->endWidget();?>
				<?php  }?>
			</div>
		</div>
	</div>
</div>

<?php
	 if(Yii::app()->user->hasFlash('appUserShow')){
	 	$info=Yii::app()->user->getFlash('appUserShow');
	 	echo "<script>$('#showInfo').text("."'$info'".")</script>";
	 	echo "<script>$('#showInfo').fadeIn('slow').fadeOut(1500)</script>";
	}
?>