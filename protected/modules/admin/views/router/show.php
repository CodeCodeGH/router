<div class="container-fluid">
	<div class="row text-primary">
		<?php  $this->beginWidget('CActiveForm',array('method'=>'POST','htmlOptions'=>array('class'=>'form-inline'))); ?>
		<button type="button" class="btn btn-primary"  data-toggle="modal"  data-target="#addUser"  data-backdrop="static">
			<span class="glyphicon glyphicon-plus"></span>
			添加路由
		</button>
		
		<div class="input-group pull-right">
			<div class="input-group-btn">
				<button class="btn btn-primary" type="button">搜索</button>
			</div>
			<input class="form-control" type="text" placeholder="Enter id" name="id">
			<div class="input-group-btn">
				<input class="btn btn-primary form-control" type="submit" value="提交查询">
			</div>
		</div>
		<!-- <div class="input-group pull-right" style="margin-right:10px">
			<div class="input-group-btn">
				<button class="btn btn-primary" type="button">版本</button>
			</div>	
			<select class="form-control selectpicker show-tick" name="roles">
				<option value="1">T01</option>
				<option value="2">T02</option>
			</select>
		</div> -->
		<?php $this->endWidget(); ?>
	</div>
	<div class="row" id="show-table">
		<div class="panel panel-primary">
			<div class="panel-heading">
				路由列表
			</div>
			<div class="panel-body">
				<table class="table table-hover table-striped">
					<tr>
						<th class="text-center">id</th>
						<th class="text-center">路由id</th>
						<th class="text-center">Mac地址</th>
						<th class="text-center">版本号</th>
						<th class="text-center">型号</th>
						<th class="text-center">创建时间</th>
						<th class="text-center">修改时间</th>
						<th class="text-center">操作</th>
					</tr>
					<?php  if(!empty($routerData)){  ?>
					<?php foreach($routerData  as $routerVal){  ?>
					<tr>
						<td class="text-center"><?php echo $routerVal->id ?></td>
						<td class="text-center"><?php echo $routerVal->net_router_id ?></td>
						<td class="text-center"><?php echo $routerVal->mac ?></td>
						<td class="text-center"><?php echo $routerVal->version ?></td>
						<td class="text-center"><?php echo $routerVal->model ?></td>
						<td class="text-center"><?php echo $routerVal->ctime ?></td>
						<td class="text-center"><?php echo $routerVal->mtime ?></td>
						<td class="text-center">
							<a href="<?php echo $this->createUrl('router/GetEquip',array('router_id'=>$routerVal->id)) ?>" type="button" class="btn btn-info">设备</a>
							<a href="<?php echo $this->createUrl('router/routerdetail',array('router_id'=>$routerVal->id)) ?>" type="button" class="btn btn-warning">编辑</a>	
						</td>
					</tr>
					<?php }  ?>
					<!-- 页码 -->
					<?php $this->widget('CLinkPager',array(
									    'pages'=>$pager,
									    'maxButtonCount'=>'5',
									    'header'=>'共'.$pager->itemCount.'条记录, Go to page: ',
									    'firstPageLabel'=>'首页',
									    'nextPageLabel'=>'下一页',
									    'prevPageLabel'=>'上一页',
									    'lastPageLabel'=>'尾页',
									    )
							) ?>

					<?php  }else{ ?>
					<tr>
						<td colspan="8" class="alert alert-danger  text-center">
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
	 if(Yii::app()->user->hasFlash('addRouterInfo')){
	 	$info=Yii::app()->user->getFlash('addRouterInfo');
	 	echo "<script>$('#showInfo').text("."'$info'".")</script>";
	 	echo "<script>$('#showInfo').fadeIn('slow').fadeOut(1500)</script>";
	}
	 if(Yii::app()->user->hasFlash('GetEquipInfo')){
	 	$info=Yii::app()->user->getFlash('GetEquipInfo');
	 	echo "<script>$('#showInfo').text("."'$info'".")</script>";
	 	echo "<script>$('#showInfo').fadeIn('slow').fadeOut(1500)</script>";
	}
?>

<script type="text/javascript">
	//判断内容是否为空
	$('#checkEmpty').click(function(){
		netRouterId=$('#inputNetRouter').val();
		mac=$('#inputMac').val();
		version=$('#inputVersion').val();
		model=$('#inputModel').val();

		if(netRouterId.length==0 || mac.length==0 || version.length==0 || model.length==0){
			return false;
		}else{
			return true;
		}
	})	
</script>