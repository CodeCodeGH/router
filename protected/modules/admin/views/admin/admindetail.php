<div class="container-fluid">
	<div class="row" id="show-table">
		<div class="panel panel-primary">
			<div class="panel-heading">
				管理员信息查看
			</div>
			<div class="panel-body">
				<?php  if(!empty($adminData)){  ?>
				<?php  $this->beginWidget('CActiveForm',array('action'=>$this->createUrl('admin/admindetail'),'htmlOptions'=>array('class'=>'form-horizontal')));  ?>
				<input type="hidden" name="id" value="<?php echo $adminData->id?>">
				<div class="form-group">
					<label class="col-md-2 text-right">用户名</label>
					<div class="col-md-8">
						<input  type="text" name="name"  class="form-control"  value="<?php echo $adminData->name ?>">
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
						<input type="text"  name="phone"  class="form-control"  value="<?php echo $adminData->phone ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 text-right">真实姓名</label>
					<div class="col-md-8">
						<input type="text" name="real_name" class="form-control" value="<?php  echo $adminData->real_name ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 text-right">创建时间</label>
					<div class="col-md-8">
						<input type="text"  name="ctime"  class="form-control"  value="<?php  echo  $adminData->ctime ?>"  disabled>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 text-right">修改时间</label>
					<div class="col-md-8">
						<input type="text" name="mtime"  class="form-control" value="<?php  echo  $adminData->mtime?>"  disabled>
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
	 if(Yii::app()->user->hasFlash('systemUserShow')){
	 	$info=Yii::app()->user->getFlash('systemUserShow');
	 	echo "<script>$('#showInfo').text("."'$info'".")</script>";
	 	echo "<script>$('#showInfo').fadeIn('slow').fadeOut(1500)</script>";
	}
?>