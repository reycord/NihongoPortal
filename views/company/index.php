<script>
function hideSpan () {
  var span_arr = $('#regisDataCompany span');
    $.each( span_arr, function( key, value ) {
          $(value).hide(); 
    });
}
 $(function(){
     $('#dataTable-company >tbody >tr').click(function(){
     $('#dataTable-company >tbody >tr').removeClass('info');
     $('#regisDataCompany >tbody >tr').removeClass('has-warning'); 
     hideSpan();           
     $(this).addClass('info');  
     $('#company_id').val(this.cells[0].textContent);
     $("#company_id").attr("readonly", true);
     $('#company_name').val(this.cells[1].textContent);
     $('#message_company_div').hide();
     $('#save_company').hide();
     $('#update_company').show();
     $('#delete_company').show();
     });
     $('#dataTable-company').submit(function() {
            // DO STUFF
            var btn= $(this).find("button[type=submit]:hover").val();
            
            if (btn == 'delete_company'){
                // return false to cancel form action
                
                $('#message_company_div').hide();
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
 var cancel_company = function() {
    $('#company_id').val(""); 
    hideSpan(); 
    $('#save_company').show();
    $('#update_company').hide();
    $('#company_name').val("");
    $("#company_id").attr("readonly", false);
    $('#regisDataCompany >tbody >tr').removeClass('has-warning');
    $('#dataTable-company >tbody >tr').removeClass('info');
    $('#message_company_div').hide();
 };
 
 $(document).ready(function() {
	    $('#dataTable-company').DataTable( {
	        "scrollY":        "340px",
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
    <form style="height: 100px;" id="company-form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
 
        <div class="col-xs-12" style="border: 1px solid #e4e4e4;padding:10px 20px;">
            <h4 class="modal-title">会社情報</h4>            
            <table id="regisDataCompany" class="col-xs-12 regisTable" >                 
                <tr class="<?php if ($data['company_id_error'] === true): ?>
                    <?php echo "has-warning" ?>
                <?php endif ?>">
                    <th style="width: 100px;padding: 6px;vertical-align:middle; background: #A9D0F5;">会社コード</th>                 
                    <td class="form-group" style="margin: 0;padding-bottom: 2px;padding-top: 2px; margin-left: 5px;display: inline-flex;">
                        <input maxlength="4" style="width: 100px;"type="text" class="form-control input-sm" id="company_id" name="company_id" <?php if ($data["company_id"] != ""): ?>
                            readonly
                        <?php endif ?>value="<?php echo $data["company_id"] ?>"></input>
                    </td>               
                </tr>
                <tr class="<?php if ($data['company_name_error'] === true): ?>
                    <?php echo "has-warning" ?>
                <?php endif ?>">
                    <th style="width: 100px;padding: 6px;vertical-align:middle; background: #A9D0F5;">会社名</th>                 
                    <td class="form-group"  style="margin: 0;padding-bottom: 2px;padding-top: 2px; margin-left: 5px;display: inline-flex;">
                        <input maxlength="50" style="width: 250px;"type="text" class="form-control input-sm" id="company_name" name="company_name" value="<?php echo $data["company_name"] ?>"></input>
                    </td>               
                </tr> 
                  
             </table>
              <div style="text-align: right; margin: 10px 0;" > 
                    <?php $update_save_success = $data['save_success'] == true || $data['update_success'] == true ?>                                           
                    <button class="btn btn-default" type="submit" name="submit" value="save_company" id="save_company" style="width: 80px;<?php if ($update_save_success == true): ?>display: none;<?php endif ?>">保存</button>
                    <button class="btn btn-default" type="submit" name="submit" value="update_company" id="update_company" style="width: 80px; <?php if (!$update_save_success ): ?>display: none;<?php endif ?>">更新</button>   
                    <a class="btn btn-default" id="cancel_company" onclick="cancel_company()" href="<?php echo $this->url("company","index") ?>">キャンセル</a>
              </div> 
              <div class="" style="margin-top: 0px;" id="message_company_div">
                
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
        
  </form> 
  <script>
     $('#company-form').validate({
        rules: {
            company_id:{
                required: true,
                pattern : /^.*$/,
                remote: function(){
                    var res = {
                        type: 'post',
                        url: "service.php",
                        data: {
                            "c": "company",
                            "a": "checkNewCompany",
                            "new_company_id": function() {
                                return $("#company_id").val();
                             },
                             "old_company_id": function(){
                                if($("#company_id").attr("readonly") == "readonly"){
                                    return $("#company_id").val();
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
            company_name: {
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
    <div class="col-xs-12" style="margin-top: 10px;padding: 0 0 10px 0;"> 
        <table  id="dataTable-company" class="display table table-bordered" cellspacing="0">
        <thead class="titledt">
          <tr>
            <th style="text-align:center;vertical-align:middle;width: 20%;">会社コード</th>    
            <th style="text-align:center;vertical-align:middle">会社名</th>
            <th style="text-align:center;vertical-align:middle">動作</th>
          </tr>
        </thead>
        
        <tbody>  
            <?php foreach ($data['company_list'] as $key => $row): { ?>
                <tr>
                    <td style='text-align:center;vertical-align:middle'><?php echo $row['company_id']; ?></td>
                    <td style='text-align:center;vertical-align:middle'><?php echo $row['company_name']; ?></td>
                    <td style='width:12%; text-align:center;vertical-align:middle'>
                        <form class="row-form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" style="display: inline-flex;"> 
                            <input style="display:none;" name = "company_id" id="company_id" value="<?php echo $row['company_id'] ?>"/>  
                            <button type="submit" class="btn btn-default" name="submit" value="delete_company" id="delete_company" style="width: 50px;margin-left: 5px;margin-right: 2px;">削除</button>               
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