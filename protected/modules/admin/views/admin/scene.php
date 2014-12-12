<div class="container-fluid">
	<div class="row text-primary">
		<button type="button" class="btn btn-primary"  data-toggle="modal"  data-target="#addUser"  data-backdrop="static">
			<span class="glyphicon glyphicon-plus"></span>
			添加场景
		</button>
	</div>
	<div class="row" id="show-table"> 
		<div class="panel panel-primary">
			<div class="panel-heading">
				场景列表
			</div>
			<div class="panel-body">
				<table class="table table-hover table-striped">
					<tr>
						<th class="text-center">id</th>
						<th class="text-center">场景名字</th>
						<th class="text-center">创建时间</th>
						<th class="text-center">修改时间</th>
						<th class="text-center">操作</th>
					</tr>
					<?php  if(!empty($sceneData)){  ?>
					<?php foreach($sceneData  as $sceneVal){  ?>
					<tr>
						<td class="text-center"><?php echo $sceneVal->id ?></td>
						<td class="text-center"><?php echo $sceneVal->name?></td>
						<td class="text-center"><?php echo $sceneVal->ctime?></td>
						<td class="text-center"><?php echo $sceneVal->mtime?></td>
						<td class="text-center">
							<a href="<?php echo $this->createUrl('router/routerdetail',array('scene_id'=>$sceneVal->id)) ?>" type="button" class="btn btn-danger">删除</a>
							<a href="<?php echo $this->createUrl('router/routerdetail',array('scene_id'=>$sceneVal->id)) ?>" type="button" class="btn btn-warning">编辑</a>
							<a href="<?php echo $this->createUrl('admin/se',array('scene_id'=>$sceneVal->id,'user_id'=>$sceneVal->user_id)) ?>" type="button" class="btn btn-info">设备</a>
						</td>
					</tr>
					<?php }  ?>

					<?php  }else{ ?>
					<tr>
						<td colspan="5" class="alert alert-danger  text-center">
							暂无数据
						</td>
					</tr>
					<?php }  ?>
				</table>
			</div>
		</div>
	</div>
</div>


<!-- modal -->
<div class="modal fade"  id="addUser"  role="dialog"  tabindex="-1" aria-labelledby="myModelLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span> <span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title" id="myModelLabel">添加路由</h4>
			</div>
			<?php $form=$this->beginWidget('CActiveForm',array('action'=>$this->createUrl('router/addrouter'),'htmlOptions'=>array('class'=>'form-horizontal','enctype'=>'multipart/form-data')))  ?>
			<div class="modal-body">
				<div class="form-group">
					<label for="inputNetRouter" class="col-sm-3  text-right">路由id</label>
					<div class="col-sm-8">
					       <input type="text"  name="net_router_id"  class="form-control"  id="inputNetRouter"  placeholder="请输入路由id">
					</div>
					<div class="col-sm-1 text-left  text-danger">
						<span class="glyphicon glyphicon-asterisk"></span>
					</div>
				</div>
				<div class="form-group">
					<label for="inputMac" class="col-sm-3  text-right">Mac地址</label>
					<div class="col-sm-8">
						<input type="text"  name="mac" class="form-control" id="inputMac" placeholder="请输入Mac地址" >
					</div>
					<div class="col-sm-1 text-left  text-danger">
						<span class="glyphicon glyphicon-asterisk"></span>
					</div>
				</div>
				<div class="form-group">
					<label  for="inputVersion" class="col-sm-3 text-right">版本号</label>
					<div class="col-sm-8">
						<input type="text" name="version" class="form-control" id="inputVersion" placeholder="请输入版本号">
					</div>
					<div class="col-sm-1 text-left  text-danger">
						<span class="glyphicon glyphicon-asterisk"></span>
					</div>
				</div>
				<div class="form-group">
					<label for="inputModel" class="col-sm-3 text-right">型号</label>
					<div class="col-sm-8">
						<input type="text"  name="model" class="form-control" id="inputModel" placeholder="请输入型号">
					</div>
					<div class="col-sm-1 text-left  text-danger">
						<span class="glyphicon glyphicon-asterisk"></span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type='button'  class='btn btn-default' data-dismiss="modal">Close</button>
				<button type='sumbit' class="btn btn-primary"  id="checkEmpty">Save</button>
			</div>
			<?php $this->endWidget(); ?>
		</div>
	</div>
</div>

<?php
	 if(Yii::app()->user->hasFlash('SceneInfo')){
	 	$info=Yii::app()->user->getFlash('SceneInfo');
	 	echo "<script>$('#showInfo').text("."'$info'".")</script>";
	 	echo "<script>$('#showInfo').fadeIn('slow').fadeOut(1500)</script>";
	}
?>