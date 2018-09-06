<script>
function hideSpan () {
  var span_arr = $('#regisDataUser1 span');
    $.each( span_arr, function( key, value ) {
          $(value).hide(); 
    });
  
  var span_arr2 = $('#regisDataUser2 span');
    $.each( span_arr2, function( key, value ) {
          $(value).hide(); 
    });
}
 $(function(){
     $('#company_id').change(function() { 
        if ($("#company_id").val() != "" && $("#company_id").prop("disabled") == false){
            $("#user_id").attr("readonly", false); 
        }
        else{
            $("#user_id").attr("readonly", true);
        }
    });
     $("#department_id").val($("#department_id option[selected]").val());
     $("#department_id").chained("#company_id");
     $("#leader_id").chained("#company_id");
     $("#department_id").change(function(){
           if($('#department_id').val() == ""){
               $('<span class="help-block">').insertAfter($('#department_id'));
           } 
     });
     $('#dataTable-user >tbody >tr').click(function(){
         $('#dataTable-user >tbody >tr').removeClass('info');
         $('#regisDataUser1 >tbody >tr').removeClass('has-warning');   
         $('#regisDataUser2 >tbody >tr').removeClass('has-warning');         
         $(this).addClass('info');  
         hideSpan(); 
         $("#user_id").attr("readonly", true);         
        
         $("#company_id").attr("disabled", true);
         
         var row = this;
         $.when( $('#company_id').val(row.cells[0].textContent).change() )
            .then(function() {
            $('#department_id').val(row.cells[9].textContent);
            $('#leader_id').val(row.cells[4].textContent);
        });
             
         $('#user_id').val(this.cells[2].textContent);
         $('#name').val(this.cells[3].textContent); 
         // $('#level').val(this.cells[5].textContent);
         if($(this).find("input[name=admin_flag]").is(":checked")){
             $('#admin_flag').prop( "checked", true );
         }
         else{
             $('#admin_flag').prop( "checked", false );
         }
         if($(this).find("input[name=manager_flag]").is(":checked")){
             $('#manager_flag').prop( "checked", true );
         }
         else{
             $('#manager_flag').prop( "checked", false );
         }
         $('#password').val(this.cells[7].textContent);
         
               
         $('#message_div').hide();
         $('#save_user').hide();
         $('#update_user').show();
         $('#delete_user').show();
         $('#message_user_div').hide();
     });
     $('#dataTable-user').submit(function() {
            // DO STUFF
            var btn= $(this).find("button[type=submit]:hover").val();
            
            if (btn == 'delete_user'){
                // return false to cancel form action
               
                $('#message_user_div').hide();
                // ban co chac xoa data nay khong
                if (!confirm("このデータを削除しますか。"))
                {
                    return false;
                } 
            }
            return true; 
    });
    $('#user-form').submit(function() {
        // if($('#department_id').val() == ""){
            // $('#department_id').closest('tr').addClass('has-warning');
            // $('<span class="help-block">このフィールドは必須です。</span>').insertAfter($('#department_id'));
            // return false;
        // }
        $("#company_id").attr("disabled", false);
        var btn= $(this).find("button[type=submit]:hover").val();
            
            if (btn == 'update_user'){
                $('#message_user_div').hide();
                $('#save_user').hide();
                $('#update_user').show();
            }
        return true; 
    });

 });
 var cancel_user = function() {
     hideSpan(); 
     $('#company_id').val("");
     $("#company_id").attr("disabled", false);
     $('#save_user').show();
     $('#update_user').hide();
     $('#department_id').val("");
     $("#user_id").attr("readonly", false);
     $('#user_id').val("");
     $('#name').val("");
     $('#leader_id').val("");
     $('#level').val("");
     $('#admin_flag').prop('checked', false);
     $('#password').val("");
     $('#message_user_div').hide();
     $('#dataTable-user >tbody >tr').removeClass('info');
     $('#regisDataUser1 >tbody >tr').removeClass('has-warning');
     $('#regisDataUser2 >tbody >tr').removeClass('has-warning');
    
     $("#user_id").attr("readonly", false); 
     
 };
  $(document).ready(function() {
	    $('#dataTable-user').DataTable( {
	        "scrollY":        "300px",
	        "scrollCollapse": true,
	        "paging":         false,
	        "searching": 	false,
	        "bInfo": 	false,
	        "bSort": 	false
	    } );
    } );
	function changeCheckboxAdmin(){
	     $('#manager_flag').prop( "checked", false );
	}
	function changeCheckboxManager(){
	     $('#admin_flag').prop( "checked", false );
	}
 </script>
<div class="container"> 
<div class="col-xs-12" style="margin:0 auto; float: inherit;">
    <form style="height: 100px;" id="user-form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
 
        <div class="col-xs-12" style="border: 1px solid #e4e4e4;padding:10px 0px 10px 20px;">
                 
            <h4 class="modal-title">ユーザー管理</h4>   
            <div style="float:left;width:100%">     
	            <table id="regisDataUser1" class="regisTable" style="width: 44%;float:left;">  
	                <tr class="<?php if ($data['company_id_error'] === true): ?><?php echo "has-warning" ?><?php endif ?>" 
	                    style ="<?php echo ($data['maintenance_flag'] )? "" :"display: none;"?>"
	                >
	                    <th style="width: 120px;padding: 6px;vertical-align:middle; background: #A9D0F5;">会社コード</th>                 
	                    <td style="margin-left: 5px;padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
	                        <select style="width: 100px; height: 25px; padding: 1px;" id="company_id" name="company_id" 
	                        <?php if (isset($data['company_id'])): ?> disabled <?php endif ?>class="form-control input-sm" >
	                            <option value="">--</option>
	                            <?php foreach ($data['company_list'] as $key => $c): ?>                                                                    
	                                <option value="<?php echo $c['company_id'] ?>"
	                                <?php if (isset($data['company_id'])): ?>
	                                    <?php if ($c['company_id'] == $data['company_id']): ?> selected<?php endif ?>
	                                <?php else: ?>
	                                    <?php if (User::getCurrentUser()->maintenance_flag == false && $c['company_id'] == User::getCurrentUser()->company_id): ?> selected<?php endif ?>
	                                <?php endif ?>
	                                >
	                                <?php echo $c['company_id']?>
	                                </option>
	                            <?php endforeach ?>                            
	                        </select>
	                    </td>               
	                </tr>  
	                
	                <tr class="<?php if ($data['department_id_error'] === true): ?><?php echo "has-warning" ?><?php endif ?>">
	                    <th style="width: 120px;padding: 6px;vertical-align:middle; background: #A9D0F5;">所属コード</th>                 
	                    <td style="margin-left: 5px;padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
	                        
	                        <select style="width: 100px;" id="department_id" name="department_id" class="form-control input-sm">
	                            <option value="">--</option>
	                            <?php foreach ($data['department_arr'] as $key => $c): ?>                                                                   
	                                    <option value="<?php echo $c['dep_id'] ?>" 
	                                             class="<?php echo $c['company_id'] ?>"
	                                           <?php if ($c['dep_id'] == $data['department_id']): ?> selected <?php endif ?>
	                                    >
	                                    <?php echo $c['dep_name']?>
	                                    </option>
	                            <?php endforeach ?>
	                        </select>
	                    </td>               
	                </tr>
	                <tr class="<?php if ($data['leader_id_error'] === true): ?><?php echo "has-warning" ?>
	                    <?php endif ?>">
	                    <th style="width: 120px;padding: 6px;vertical-align:middle; background: #A9D0F5;">リーダーコード</th>                 
	                    <td style="margin-left: 5px;padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
	                        <select style="width: 100px;" id="leader_id" name="leader_id" class="form-control input-sm" >
	                            <option value="">--</option>
	                            <?php foreach ($data['user_list_leader'] as $key => $c): ?>                                                                   
	                                    <option value="<?php echo $c['user_id'] ?>" 
	                                        class="<?php echo $c['company_id'] ?>"
	                                    <?php if ($c['user_id'] == $data['leader_id']): ?> selected<?php endif ?>>
	                                    <?php echo $c['user_id']?></option>
	                            <?php endforeach ?>
	                        </select>
	                    </td>               
	                </tr>       
	             </table>
	             
	             <table id="regisDataUser2" class="regisTable" style="width: 56%;float:right;">   
	                <tr class="<?php if ($data['user_id_error'] === true): ?>
	                    <?php echo "has-warning" ?>
	                <?php endif ?>">
	                    <th style="width: 130px;padding: 6px;vertical-align:middle; background: #A9D0F5;">ユーザーコード</th>                 
	                    <td style="margin-left: 5px;padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
	                        <input maxlength="10" style="width: 100px;"type="text" class="form-control input-sm" id="user_id" name="user_id" 
	                        <?php if (User::getCurrentUser()->maintenance_flag === true): ?>
	                            readonly
	                        <?php endif ?> value="<?php echo $data["user_id"] ?>"></input>
	                    </td>               
	                </tr>
	                <tr class="<?php if ($data['name_error'] === true): ?>
	                    <?php echo "has-warning" ?>
	                <?php endif ?>">
	                    <th style="width: 130px;padding: 6px;vertical-align:middle; background: #A9D0F5;">ユーザー名</th>                 
	                    <td style="margin-left: 5px;padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
	                        <input maxlength="50" style="width: 200px;"type="text" class="form-control input-sm" id="name" name="name" value="<?php echo $data["name"] ?>"></input>
	                    </td>               
	                </tr>
	                <!--
	                <tr class="<?php if ($data['level_error'] === true): ?>
	                    <?php echo "has-warning" ?>
	                <?php endif ?>">
	                    <th style="width: 130px;padding: 6px;vertical-align:middle; background: #A9D0F5;">レベル</th>                 
	                    <td style="margin-left: 5px;padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
	                        <input maxlength="4" style="width: 200px;"type="text" class="form-control input-sm" id="level" name="level" value="<?php echo $data["level"] ?>"></input>
	                    </td>               
	                </tr>
	                -->
	                <tr class="<?php if ($data['admin_flag_error'] === true): ?>
	                    <?php echo "has-warning" ?>
	                <?php endif ?>">
	                    <th style="width: 130px;padding: 6px;vertical-align:middle; background: #A9D0F5;">管理者</th>                 
	                    <td style="margin-left: 5px;padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
	                        <!-- <input maxlength="30" style=""type="checkbox" class="form-control input-sm" id="admin_flag" name="admin_flag" value="<?php echo $data["admin_flag"] ?>"></input> -->
	                       <?php if (User::getCurrentUser()->maintenance_flag == true || User::getCurrentUser()->admin_flag == 1): ?>
	                       <label><input name="admin_flag" id="admin_flag" type="checkbox" onChange="changeCheckboxAdmin()">全参照</label>
	                       <label style="margin-left: 15px;"></label>
	                       <?php endif ?>
	                       <label><input name="manager_flag" id="manager_flag" type="checkbox" onChange="changeCheckboxManager()">所属長</label>
	                    </td>           
	                </tr> 
	                <tr class="<?php if ($data['password_error'] === true): ?>
	                    <?php echo "has-warning" ?>
	                <?php endif ?>">
	                    <th style="width: 130px;padding: 6px;vertical-align:middle; background: #A9D0F5;">パスワード</th>                 
	                    <td style="margin-left: 5px;padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
	                        <input maxlength="30" style="width: 200px;"type="password" class="form-control input-sm" id="password" name="password" value="<?php echo $data["password"] ?>"></input>
	                    </td>               
	                </tr>
	                  
	             </table>
	             </div>    
             
              <div style="text-align: right; margin: 10px 20px;float: right;" >
                    <!-- <?php $update_save_success = ($data['save_success'] === true || $data['update_success'] === true) ?> -->                                           
                    <button class="btn btn-default" type="submit" name="submit" value="save_user" id="save_user" style="width: 80px;<?php if ($data['save_success'] === true || $data['update_success'] === true): ?>display: none;<?php endif ?>">保存</button>
                    <button class="btn btn-default" type="submit" name="submit" value="update_user" id="update_user" style="width: 80px;<?php if ($data['save_success'] === false && $data['update_success'] === false ): ?>display: none;<?php endif ?>">更新</button>
                    <a class="btn btn-default" id="cancel_user" onclick="cancel_user()" href="<?php echo $this->url("user","index")?>">キャンセル</a>
              </div> 
              <div class="" id="message_user_div" style="float: left;width: 98%;">
                
                <div id="message_div_error" <?php if ($data['success'] == true): ?> style="display: none; padding: 5px;" <?php endif ?> 
                    id="error_div"class="alert alert-warning alert-dismissible  " role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                    <strong>エラー： </strong>
                    <span id="message"><?php echo $data['message'] ?></span>
                </div>
                
                <?php if ($data['success'] == true && $data['message'] != ""): ?>
                    <div id="success_div"class="alert alert-success alert-dismissible " role="alert" style="padding: 5px;">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <strong>アラーム： </strong> <?php echo $data['message'] ?>
                    </div>
                <?php endif ?>
            </div> 
  
        </div>
  <script>
     $('#user-form').validate({
        rules: {
            user_id:{
                required: true,
                pattern : /^.*$/,
                remote: function(){
                    var res = {
                        type: 'post',
                        url: "service.php",
                        data: {
                            "c": "user",
                            "a": "checkNewUser",
                            "company_id": function() {
                                return $("#company_id").val();
                             },
                            "new_user_id": function() {
                                return $("#user_id").val();
                             },
                             "old_user_id": function(){
                                if($("#user_id").attr("readonly") == "readonly"){
                                    return $("#user_id").val();
                                }
                                else{
                                    return "";
                                }
                             }
                        },
                        async: false
                    };
                    return res;
                    
                }
            },
            name: {
                required: true,
                pattern : /^.*$/,
            },
            department_id: {
                required: true,
                pattern : /^.+$/,
            },
            level: {
                required: true,
                pattern : /^\d*$/,
            },
            
            password: {
                required: true,
                pattern : /^.*$/,
            },
            company_id: {
                required: true,
                pattern : /^.*$/,
            },
        },
        highlight: function(element) {
            $(element).closest('tr').addClass('has-warning');
        },
        unhighlight: function(element) {
            $(element).closest('tr').removeClass('has-warning');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
             error.insertAfter(element);
        }
    });

  </script>    
  </form> 
    <div class="col-xs-12" style="margin-top: 20px;padding: 0 0 10px 0;"> 
        <table  id="dataTable-user" class="display table table-bordered" cellspacing="0">
        <thead class="titledt">
          <tr>
              <th style="text-align:center;vertical-align:middle;
            <?php echo ($data['maintenance_flag'] )? "" :"display: none;"?>">会社コード</th>
            <th style="text-align:center;vertical-align:middle">所属名</th>
            <th style="text-align:center;vertical-align:middle">ユーザーコード</th>
            <th style="text-align:center;vertical-align:middle">ユーザー名</th>
            
            <th style="text-align:center;vertical-align:middle">上司コード</th>
            <!-- <th style="text-align:center;vertical-align:middle">レベル</th> --> 
            <th style="text-align:center;vertical-align:middle">全参照</th>  
            <th style="text-align:center;vertical-align:middle">所属長</th>    
            <th style="text-align:center;vertical-align:middle;display: none;">パスワード</th>
            <th style="text-align:center;vertical-align:middle">動作</th>
            <th style="text-align:center;vertical-align:middle;display: none">所属コード</th>
          </tr>
        </thead>
        
        <tbody>  
            <?php foreach ($data['user_list'] as $key => $row): { ?>
                <tr>
                    <td style='text-align:center;vertical-align:middle;
                    <?php echo ($data['maintenance_flag'] )? "" :"display: none;"?>'><?php echo $row['company_id']; ?></td>
                    <td style='text-align:center;vertical-align:middle'><?php echo $row['dep_name']; ?></td>
                    <td style='text-align:center;vertical-align:middle'><?php echo $row['user_id']; ?></td>
                    <td style='text-align:center;vertical-align:middle'><?php echo $row['name']; ?></td>
                    <td style='text-align:center;vertical-align:middle'><?php echo $row['leader_id']; ?></td>              
                    <!-- <td style='text-align:center;vertical-align:middle'><?php echo $row['level']; ?></td> --> 
                    <!-- <td style='text-align:center;vertical-align:middle'><?php echo $row['admin_flag']; ?></td> -->
                    <td style="text-align:center ;">
                        <input disabled="true" name="admin_flag" style='cursor:default' type="checkbox" class="btn btn-info chk_select" 
                        <?php if((int)$row['admin_flag'] == 1):  ?>
                            checked="checked"
                        <?php endif ?>>
                        </input>
                    </td>
                    <td style="text-align:center ;">
                        <input disabled="true" name="manager_flag" style='cursor:default' type="checkbox" class="btn btn-info chk_select" 
                        <?php if((int)$row['admin_flag'] == 2):  ?>
                            checked="checked"
                        <?php endif ?>>
                        </input>
                    </td>
                    <td style='text-align:center;vertical-align:middle;display: none;'><?php echo $row['password']; ?></td>
                    <td style='text-align:center;vertical-align:middle'>
                        <form class="row-form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" style="display: inline-flex;"> 
                            <input style="display:none;" name = "user_id" id="user_id" value="<?php echo $row['user_id'] ?>"/>  
                            <input style="display:none;" name = "company_id" id="company_id" value="<?php echo $row['company_id'] ?>"/>
                            <button class="btn btn-default" type="submit" name="submit" value="delete_user" id="delete_user" style="width: 80px;">削除</button>               
                        </form> 
                    </td>
                    <td style='text-align:center;vertical-align:middle;display: none'><?php echo $row['dep_id']; ?></td>
                </tr>
            <?php } ?>                   
            <?php endforeach ?>   
                   
        </tbody>
      </table> 
  </div>
</div>
</div>
