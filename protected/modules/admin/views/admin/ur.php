<div class="container-fluid">
	<!-- <div class="row text-primary">
		<button type="button" class="btn btn-primary"  data-toggle="modal"  data-target="#addUser"  data-backdrop="static">
			<span class="glyphicon glyphicon-plus"></span>
			添加路由
		</button>
	</div>-->
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
						<th class="text-center">别名</th>
						<th class="text-center">路由密码</th>
						<th class="text-center">绑定时间</th>
						<!-- <th class="text-center">访问时间</th> -->
						<th class="text-center">操作</th>
					</tr>
					<?php  if(!empty($routerData)){  ?>
					<?php foreach($routerData  as $routerVal){  ?>
					<tr>
						<td class="text-center"><?php echo $routerVal['router_id'] ?></td>
						<td class="text-center"><?php echo $routerVal['net_router_id'] ?></td>
						<td class="text-center"><?php echo $routerVal['mac'] ?></td>
						<td class="text-center"><?php echo $routerVal['version'] ?></td>
						<td class="text-center"><?php echo $routerVal['model'] ?></td>
						<td class="text-center"><?php echo $routerVal['alias'] ?></td>
						<td class="text-center"><?php echo $routerVal['router_login_password'] ?></td>
						<td class="text-center"><?php echo $routerVal['ctime'] ?></td>
						<!-- <td class="text-center"><?php echo $routerVal['access_time'] ?></td> -->
						<td class="text-center">
							<a  type="button" class="btn btn-danger" id="dodel" data-toggle="modal" data-target="#del" data-backdrop="static" URID="<?php echo $routerVal['urid'] ?>">删除</a>
							<a  type="button" class="btn btn-warning" id="doedit" data-toggle="modal"  data-target="#edit"  data-backdrop="static" URID="<?php echo $routerVal['urid'] ?>" RLP="<?php echo $routerVal['router_login_password'] ?>" alias="<?php echo $routerVal['alias'] ?>" atime="<?php echo $routerVal['access_time'] ?>">编辑</a>
							<a  href="<?php echo $this->createUrl('admin/scene',array('router_id'=>$routerVal['router_id'],'user_id'=>$routerVal['user_id'])) ?>" type="button" class="btn btn-info">场景</a>
						</td>
					</tr>
					<?php }  ?>

					<?php  }else{ ?>
					<tr>
						<td colspan="10" class="alert alert-danger  text-center">
							暂无数据
						</td>
					</tr>
					<?php }  ?>
				</table>
			</div>
		</div>
	</div>
</div>


<!--edit modal -->
<div class="modal fade"  id="edit"  role="dialog"  tabindex="-1" aria-labelledby="myModelLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span> <span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title" id="myModelLabel">编辑路由信息</h4>
			</div>
			<?php $form=$this->beginWidget('CActiveForm',array('action'=>$this->createUrl('admin/editRouter'),'htmlOptions'=>array('class'=>'form-horizontal','enctype'=>'multipart/form-data')))  ?>
			<input type="hidden"  name="UR_id" value="" id="URID">
			<div class="modal-body">
				<div class="form-group">
					<label for="inputAlias" class="col-sm-3  text-right">路由别名</label>
					<div class="col-sm-8">
					       <input type="text"  name="alias"  class="form-control"  id="inputAlias"  placeholder="请输入路由别名" value="">
					</div>
					<div class="col-sm-1 text-left  text-danger">
						<span class="glyphicon glyphicon-asterisk"></span>
					</div>
				</div>
				<div class="form-group">
					<label for="inputPassword" class="col-sm-3  text-right">路由密码</label>
					<div class="col-sm-8">
						<input type="text"  name="password" class="form-control" id="inputPassword" placeholder="请输入路由密码" value="">
					</div>
					<div class="col-sm-1 text-left  text-danger">
						<span class="glyphicon glyphicon-asterisk"></span>
					</div>
				</div>
				<div class="form-group">
					<label for="inputPassword" class="col-sm-3  text-right">最新访问时间</label>
					<div class="col-sm-8">
						<input type="text"  name="password" class="form-control" id="atime"  value="" disabled>
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

<!-- del modal -->
<div class="modal fade" id="del" role="dialog" tabinde="-1" aria-labelledby="mydelModel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title" id="mydelModel">解除绑定路由</h4>
			</div>
			<?php $form=$this->beginWidget('CActiveForm',array('action'=>$this->createUrl('admin/delRouter'),'htmlOptions'=>array('class'=>'form-horizontal')))  ?>
			<input type="hidden"  name="delId" value="" id="delId">
			<div class="body">
				<p class="text-center  lead  text-danger" style="margin-top:15px">
					<span class="glyphicon glyphicon-warning-sign"></span>
					确定要解除路由绑定吗？
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidde="true">取消</button>
				<button type="submit" class="btn btn-danger">确定</button>
			</div>
			<?php  $this->endWidget(); ?>
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

<?php
	 if(Yii::app()->user->hasFlash('URInfo')){
	 	$info=Yii::app()->user->getFlash('URInfo');
	 	echo "<script>$('#showInfo').text("."'$info'".")</script>";
	 	echo "<script>$('#showInfo').fadeIn('slow').fadeOut(1500)</script>";
	}
?>

<script>
	$(document).ready(function(){
		$("#dodel").click(function(){
			delId=$(this).attr('URID');
			$("#delId").val(delId);
		});

		$("#doedit").click(function(){
			alias=$(this).attr('alias');
			RLP=$(this).attr('RLP');
			URID=$(this).attr('URID');
			access_time=$(this).attr('atime');

			$("#inputAlias").val(alias);
			$("#inputPassword").val(RLP);
			$("#URID").val(URID);
			$("#atime").val(access_time);
		});

		$("#checkEmpty").click(function(){
			subAlias=$("#inputAlias").val();
			subPasswd=$("#inputPassword").val();
			if(subAlias.length==0 || subPasswd.length==0){
				return false;
			}else{
				return true;
			}
		});

	})
	
</script>