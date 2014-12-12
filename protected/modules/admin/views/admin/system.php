<div class="container-fluid">
    <div class="row text-primary">
        <button type="button" class="btn btn-primary"  data-toggle="modal"  data-target="#addUser"  data-backdrop="static">
            <span class="glyphicon glyphicon-plus"></span>
            添加管理员
        </button>
    </div>
    <div class="row" id="show-table">
        <div class="panel panel-primary">
            <div class="panel-heading">
                管理员列表
            </div>
            <div class="panel-body">
                <table class="table table-hover table-striped">
                    <tr>
                        <th class="text-center">管理员id</th>
                        <th class="text-center">用户名</th>
                        <th class="text-center">真实姓名</th>
                        <th class="text-center">手机号</th>
                        <th class="text-center">创建时间</th>
                        <th class="text-center">修改时间</th>
                        <th class="text-center">操作</th>
                    </tr>
                    <?php  if(!empty($adminData)){  ?>
                    <?php foreach($adminData  as $adminVal){  ?>
                    <tr>
                        <td class="text-center"><?php echo $adminVal->id ?></td>
                        <td class="text-center"><?php echo $adminVal->name ?></td>
                        <td class="text-center"><?php echo $adminVal->real_name ?></td>
                        <td class="text-center"><?php echo $adminVal->phone ?></td>
                        <td class="text-center"><?php echo $adminVal->ctime ?></td>
                        <td class="text-center"><?php echo $adminVal->mtime ?></td>
                        <td class="text-center">
                            <a href="<?php echo $this->createUrl('admin/admindetail',array('admin_id'=>$adminVal->id)) ?>" type="button" class="btn btn-danger">删除</a>
                            <a href="<?php echo $this->createUrl('admin/admindetail',array('admin_id'=>$adminVal->id)) ?>" type="button" class="btn btn-info">查看详情</a>
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
                        <td colspan="7" class="alert alert-danger  text-center">
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
                <h4 class="modal-title" id="myModelLabel">添加管理员</h4>
            </div>
            <?php $form=$this->beginWidget('CActiveForm',array('action'=>$this->createUrl('admin/addSystem'),'htmlOptions'=>array('class'=>'form-horizontal','enctype'=>'multipart/form-data')))  ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="inputName" class="col-sm-3  text-right">用户名</label>
                    <div class="col-sm-8">
                           <input type="text"  name="name"  class="form-control"  id="inputName"  placeholder="Enter  AdminName">
                    </div>
                    <div class="col-sm-1 text-left  text-danger">
                        <span class="glyphicon glyphicon-asterisk"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="col-sm-3  text-right">密码</label>
                    <div class="col-sm-8">
                        <input type="password"  name="password" class="form-control" id="inputPassword" placeholder="Enter Password" >
                    </div>
                    <div class="col-sm-1 text-left  text-danger">
                        <span class="glyphicon glyphicon-asterisk"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label  for="inputPhone" class="col-sm-3 text-right">手机号</label>
                    <div class="col-sm-8">
                        <input type="text" name="phone" class="form-control" id="inputPhone" placeholder="Enter  phone">
                    </div>
                    <div class="col-sm-1 text-left  text-danger">
                        <span class="glyphicon glyphicon-asterisk"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputRealName" class="col-sm-3 text-right">真实姓名</label>
                    <div class="col-sm-8">
                        <input type="text"  name="real_name" class="form-control" id="inputRealName" placeholder="Enter Name">
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
     if(Yii::app()->user->hasFlash('addAdminInfo')){
        $info=Yii::app()->user->getFlash('addAdminInfo');
        echo "<script>$('#showInfo').text("."'$info'".")</script>";
        echo "<script>$('#showInfo').fadeIn('slow').fadeOut(1500)</script>";
    }
?>

<script type="text/javascript">
    //判断内容是否为空
    $('#checkEmpty').click(function(){
        appName=$('#inputName').val();
        password=$('#inputPassword').val();
        phone=$('#inputPhone').val();

        if(appName.length==0 || password.length==0 || phone.length==0){
            return false;
        }else{
            return true;
        }
    })  
</script>