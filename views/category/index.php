<script>
function hideSpan () {
  var span_arr = $('#regisDataCategory span');
    $.each( span_arr, function( key, value ) {
          $(value).hide(); 
    });
}
 $(function(){
     $('#dataTable-category >tbody >tr').click(function(){
     $('#dataTable-category >tbody >tr').removeClass('info');   
     $('#regisDataCategory >tbody >tr').removeClass('has-warning');  
     hideSpan();        
     $(this).addClass('info');  
     // $('#company_id').val(this.cells[0].textContent);
     // $("#company_id").attr("disabled", true);
     $('#category_id').val(this.cells[0].textContent);
     $("#category_id").attr("readonly", true);
     $('#category_name').val(this.cells[1].textContent);
     $('#message_category_div').hide();
     $('#save_category').hide();
     $('#update_category').show();
     $('#delete_category').show();
     });
     $('#dataTable-category').submit(function() {
            // DO STUFF
            var btn= $(this).find("button[type=submit]:hover").val();
            
            if (btn == 'delete_category'){
                // return false to cancel form action
               
                $('#message_category_div').hide();
                // ban co chac xoa data nay khong
                if (!confirm("このデータを削除しますか。"))
                {
                    return false;
                }
                return true;
                
                
            }
            return true; 
        });
 });
  var cancel_category= function() {
      hideSpan(); 
    $('#save_category').show();
    $('#update_category').hide();
    $("#company_id").attr("disabled", false);
    $("#category_id").attr("readonly", false);
    $('#dataTable-category >tbody >tr').removeClass('info');   
    $('#regisDataCategory >tbody >tr').removeClass('has-warning');
    $('#category_id').val(""); 
    $('#category_name').val("");
    $('#company_id').val("");
    $("#company_id").attr("readonly", false);
    $('#message_category_div').hide();
 };
 
   $(document).ready(function() {
	    $('#dataTable-category').DataTable( {
	        "scrollY":        "300px",
	        "scrollCollapse": true,
	        "paging":         false,
	        "searching": 	false,
	        "bInfo": 	false,
	        "bSort": 	false
	    } );
    } );
 </script>
<div class="container"> 
<div class="col-xs-8" style="margin:0 auto; float: inherit;">
    <form style="height: 100px;" id="category-form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
 
        <div class="col-xs-12" style="border: 1px solid #e4e4e4;padding:10px 20px;">
                 
            <h4 class="modal-title">カテゴリ管理</h4>            
            <table id="regisDataCategory" class="regisTable" >   
                 <!-- <tr class="<?php if ($data['company_id_error'] === true): ?>
                    <?php echo "has-warning" ?>
                <?php endif ?>" 
                    style ="<?php echo ($data['maintenance_flag'] )? "" :"display: none;"?>">
                    <th style="width: 120px;padding: 6px;vertical-align:middle; background: #A9D0F5;
                    <?php ?>">会社コード</th>                 
                    <td style="margin-left: 5px;padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
                        <select style="width: 150px; height: 25px; padding: 1px;" id="company_id" name="company_id" <?php if (isset($data['company_id'])): ?>disabled<?php endif ?>class="form-control input-sm" >
                            <option value="">--</option>
                            <?php foreach ($data['company_list'] as $key => $c): ?>                                                                    
                                <option value="<?php echo $c['company_id'] ?>"
                                <?php if (isset($data['company_id'])): ?>
                                    <?php if ($c['company_id'] == $data['company_id']): ?> selected<?php endif ?>>
                                <?php else: ?>
                                    <?php if (User::getCurrentUser()->maintenance_flag == false && $c['company_id'] == User::getCurrentUser()->company_id): ?> selected<?php endif ?>>
                                <?php endif ?>
                                <?php echo $c['company_id']?></option>
                             <?php endforeach ?>                            
                        </select>
                    </td>               
                </tr> -->              
                <tr class="<?php if ($data['category_id_error'] === true): ?>
                    <?php echo "has-warning" ?>
                <?php endif ?>">
                    <th style="width: 120px;padding: 6px;vertical-align:middle; background: #A9D0F5;">カテゴリコード</th>                 
                    <td style="margin-left: 5px;padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
                        <input maxlength="4" style="width: 100px;"type="text" class="form-control input-sm" id="category_id" name="category_id" value="<?php echo $data["category_id"] ?>"></input>
                    </td>               
                </tr>
                <tr class="<?php if ($data['category_name_error'] === true): ?>
                    <?php echo "has-warning" ?>
                <?php endif ?>">
                    <th style="width: 120px;padding: 6px;vertical-align:middle; background: #A9D0F5;">カテゴリ名</th>                 
                    <td style="margin-left: 5px;padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
                        <input maxlength="30" style="width: 200px;"type="text" class="form-control input-sm" id="category_name" name="category_name" value="<?php echo $data["category_name"] ?>"></input>
                    </td>               
                </tr> 
                  
             </table>
              <div style="text-align: right; margin: 10px 0;" >   
                    <?php $update_save_success = $data['save_success'] == true || $data['update_success'] == true ?>                                         
                    <button class="btn btn-default" type="submit" name="submit" value="save_category" id="save_category" style="width: 80px;<?php if ($update_save_success == true): ?>display: none;<?php endif ?>">保存</button>
                    <button class="btn btn-default" type="submit" name="submit" value="update_category" id="update_category" style="width: 80px; <?php if (!$update_save_success == true): ?>display: none;<?php endif ?>">更新</button>
                    <a class="btn btn-default" id="cancel_category" onclick="cancel_category()" href="<?php echo $this->url("category","index") ?>">キャンセル</a>
              </div> 
              <div class="" id="message_category_div">
                
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
     $('#category-form').validate({
        rules: {
            category_id:{
                required: true,
                pattern : /^.*$/,
                remote: function(){
                    var res = {
                        type: 'post',
                        url: "service.php",
                        data: {
                            "c": "category",
                            "a": "checkNewCategory",
                            "new_category_id": function() {
                                return $("#category_id").val();
                             },
                             "old_category_id": function(){
                                if($("#category_id").attr("readonly") == "readonly"){
                                    return $("#category_id").val();
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
            category_name: {
                required: true,
                pattern : /^.*$/,
            },
            // company_id: {
                // required: true,
                // pattern : /^.*$/,
            // },
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
    <div class="col-xs-12" style="margin-top: 10px;padding: 0 0 10px 0;"> 
        <table  id="dataTable-category" class="display table table-bordered" cellspacing="0">
        <thead class="titledt">
          <tr>
            <!-- <th style="text-align:center;vertical-align:middle;width: 20%
            <?php echo ($data['maintenance_flag'] )? "" :"display: none;"?>">会社コード</th> -->
            <th style="text-align:center;vertical-align:middle;width: 20%">カテゴリコード</th>    
            <th style="text-align:center;vertical-align:middle">カテゴリ名</th>
            <th style="text-align:center;vertical-align:middle">動作</th>
          </tr>
        </thead>
        
        <tbody>  
            <?php foreach ($data['category_list'] as $key => $row): { ?>
                <tr>
                    <!-- <td style='text-align:center;vertical-align:middle;
                    <?php echo ($data['maintenance_flag'] )? "" :"display: none;"?>'><?php echo $row['company_id']; ?></td>
                     -->
                    <td style='text-align:center;vertical-align:middle'><?php echo $row['category_id']; ?></td>
                    <td style='text-align:center;vertical-align:middle'><?php echo $row['category_name']; ?></td>
                    <td style='width:12%; text-align:center;vertical-align:middle'>
                        <form class="row-form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" style="display: inline-flex;"> 
                            <input style="display:none;" name = "category_id" id="category_id" value="<?php echo $row['category_id'] ?>"/>  
                            <input style="display:none;" name = "company_id" id="company_id" value="<?php echo $row['company_id'] ?>"/>
                            <button type="submit" class="btn btn-default" name="submit" value="delete_category" id="delete_category" style="width: 50px;margin-left: 5px;margin-right: 2px;">削除</button>               
                        </form> 
                    </td>
                </tr>
            <?php } ?>                   
            <?php endforeach ?>   
                   
        </tbody>
      </table> 
  </div>
</div>
</div>
