<div class="container-fluid">
	<div class="row" id="show-table">
		<div class="panel panel-primary">
			<div class="panel-heading">
				路由信息查看
			</div>
			<div class="panel-body">
				<?php  if(!empty($routerData)){  ?>
				<?php  $this->beginWidget('CActiveForm',array('action'=>$this->createUrl('router/routerdetail'),'htmlOptions'=>array('class'=>'form-horizontal')));  ?>
				<input type="hidden" name="id" value="<?php echo $routerData->id?>">
				<div class="form-group">
					<label class="col-md-2 text-right">路由id</label>
					<div class="col-md-8">
						<input  type="text" name="net_router_id"  class="form-control"  value="<?php echo $routerData->net_router_id ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 text-right">Mac地址</label>
					<div class="col-md-8">
						<input type="text" name="mac" class="form-control"  value="<?php echo $routerData->mac ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 text-right">版本号</label>
					<div class="col-md-8">
						<input type="text"  name="version"  class="form-control"  value="<?php echo $routerData->version ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 text-right">型号</label>
					<div class="col-md-8">
						<input type="text" name="model" class="form-control" value="<?php  echo $routerData->model ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 text-right">创建时间</label>
					<div class="col-md-8">
						<input type="text"  name="ctime"  class="form-control"  value="<?php  echo  $routerData->ctime ?>"  disabled>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 text-right">修改时间</label>
					<div class="col-md-8">
						<input type="text" name="mtime"  class="form-control" value="<?php  echo  $routerData->mtime?>"  disabled>
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
	 if(Yii::app()->user->hasFlash('routerDetailShow')){
	 	$info=Yii::app()->user->getFlash('routerDetailShow');
	 	echo "<script>$('#showInfo').text("."'$info'".")</script>";
	 	echo "<script>$('#showInfo').fadeIn('slow').fadeOut(1500)</script>";
	}
?>