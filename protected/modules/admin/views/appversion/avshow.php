<div class="container-fluid">
	<div class="row text-primary">
		<div class="input-group">
			<button type="button" class="btn btn-primary"  data-toggle="modal"  data-target="#addVersion"  data-backdrop="static">
				<span class="glyphicon glyphicon-plus"></span>
				添加新版本
			</button>
		</div>		
	</div>
	<div class="row" id="show-table">
		<div class="panel panel-primary">
			<div class="panel-heading">
				版本列表
			</div>
			<div class="panel-body">
				<table class="table table-hover table-striped">
					<tr>
						<th class="text-center">id</th>
						<th class="text-center">版本号</th>
						<th class="text-center">适用系统</th>
						<th class="text-center">创建时间</th>
						<th class="text-center">修改时间</th>
						<th class="text-center">操作</th>
					</tr>
					<?php  if(!empty($data)){  ?>
					<?php foreach($data  as $dataVal){  ?>
					<tr>
						<td class="text-center"><?php echo $dataVal->id ?></td>
						<td class="text-center"><?php echo $dataVal->name ?></td>
						<td class="text-center">
						<?php 
							$type=$dataVal->type;
							if($type=="1"){
								echo  "Android";
							}elseif($type=="2"){
								echo "IOS";
							}
						?>
						</td>
						<td class="text-center"><?php echo $dataVal->ctime ?></td>
						<td class="text-center"><?php echo $dataVal->mtime ?></td>
						<td class="text-center">
							<a href="<?php echo $this->createUrl('appversion/avedit',array('id'=>$dataVal->id)) ?>" type="button" class="btn btn-warning">编辑</a>
							<a href="<?php echo $this->createUrl('appversion/avdel',array('id'=>$dataVal->id)) ?>" type="button" class="btn btn-danger">删除</a>
						</td>
					</tr>
					<?php }  ?>
					<!-- 页码 -->
					<?php 
						/*
						$this->widget('CLinkPager',array(
									    'pages'=>$pager,
									    'maxButtonCount'=>'5',
									    'header'=>'共'.$pager->itemCount.'条记录, Go to page: ',
									    'firstPageLabel'=>'首页',
									    'nextPageLabel'=>'下一页',
									    'prevPageLabel'=>'上一页',
									    'lastPageLabel'=>'尾页',
						 		  	  )
						 		 )
						*/
					 ?>

					<?php  }else{ ?>
					<tr>
						<td colspan="6" class="alert alert-danger  text-center">
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
<div class="modal fade"  id="addVersion"  role="dialog"  tabindex="-1" aria-labelledby="myModelLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span> <span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title" id="myModelLabel">添加新版本</h4>
			</div>
			<?php $form=$this->beginWidget('CActiveForm',array('action'=>$this->createUrl('appversion/upload'),'htmlOptions'=>array('class'=>'form-horizontal','enctype'=>'multipart/form-data')))  ?>
			<div class="modal-body">
				<div class="form-group">
					<label for="inputVersionName" class="col-sm-3  text-right">版本号</label>
					<div class="col-sm-8">
					       <input type="text"  name="name"  class="form-control"  id="inputVersionName"  placeholder="Enter  VersionName">
					</div>
					<div class="col-sm-1 text-left  text-danger">
						<span class="glyphicon glyphicon-asterisk"></span>
					</div>
				</div>
				<div class="form-group">
					<label for="inputType" class="col-sm-3  text-right">适用系统</label>
					<div class="col-sm-8">
						<select  class="form-control  selectpicker  show-tick" name="type" id="inputType">
							<option value="1">Android</option>
							<option value="2">IOS</option>
						</select>
					</div>
					<div class="col-sm-1 text-left  text-danger">
						<span class="glyphicon glyphicon-asterisk"></span>
					</div>
				</div>
				<div class="form-group">
					<label  for="inputFile" class="col-sm-3 text-right">版本文件</label>
					<div class="col-sm-8">
						<input type="file" name="file"  id="inputFile">
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
	 if(Yii::app()->user->hasFlash('appVersionInfo')){
	 	$info=Yii::app()->user->getFlash('appVersionInfo');
	 	echo "<script>$('#showInfo').text("."'$info'".")</script>";
	 	echo "<script>$('#showInfo').fadeIn('slow').fadeOut(1500)</script>";
	}
?>

<script type="text/javascript">
	//判断内容是否为空
	$('#checkEmpty').click(function(){
		versionName=$('#inputVersionName').val();
		type=$('#inputType').val();
		picture=$('#inputFile').val();

		if(versionName.length==0 || type.length==0 || picture.length==0){
			return false;
		}else{
			return true;
		}
	})	
</script>