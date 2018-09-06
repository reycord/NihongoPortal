<script>
function hideSpan () {
  var span_arr = $('#regisDataMessage span');
    $.each( span_arr, function( key, value ) {
          $(value).hide(); 
    });
}
 $(function(){
     $('#dataTable-message >tbody >tr').click(function(){
     $('#dataTable-message >tbody >tr').removeClass('info');
     $('#regisDataMessage >tbody >tr').removeClass('has-warning'); 
     hideSpan();           
     $(this).addClass('info');  
     $('#message_content').val(this.cells[6].textContent);
     $('#message_div').hide();
     });
     $('#dataTable-message').submit(function() {
            // DO STUFF
            var btn= $(this).find("button[type=submit]:hover").val();
            
            if (btn == 'delete_message'){
                // return false to cancel form action
                
                $('#message_div').hide();
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

 $(document).ready(function() {
	    $('#dataTable-message').DataTable( {
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
  <div class="col-xs-12" style="margin:0 auto; float: inherit;">
    <form style="height: 100px;" id="company-form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
 
		<div class="" style="position: absolute;margin-left: 152px;margin-top: 4px;width: 80%;z-index: 999" id="message_div">
            
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
            
        <div class="col-xs-12" style="border: 1px solid #e4e4e4;padding:10px 20px;">
            <h4 class="modal-title">問合せ管理</h4>            
            <table id="regisDataMessage" class="col-xs-12 regisTable" >                 
                <tr class="">
                    <th style="width: 120px;padding: 6px;vertical-align:middle; background: #A9D0F5;">問合せの内容</th>                 
                    <td class="form-group"  style="margin: 0;padding-bottom: 2px;padding-top: 2px; margin-left: 5px;display: inline-flex;width: 100%;">
                        <textarea style="margin-left: 5px;width:100%; height:100px;" id="message_content" name="message_content"  class="form-control input-sm" ></textarea>
                    </td>               
                </tr> 
                  
             </table>
  
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
                            "new_content": function() {
                                return $("#content").val();
                             },
                             "old_content": function(){
                                if($("#content").attr("readonly") == "readonly"){
                                    return $("#content").val();
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
        <table  id="dataTable-message" class="display table table-bordered" cellspacing="0">
        <thead class="titledt">
          <tr>
            <th style="text-align:center;vertical-align:middle;width: 15%;">日時</th>    
            <th style="text-align:center;vertical-align:middle;width: 10%;">会社CD</th>
            <th style="text-align:center;vertical-align:middle;width: 10%;">ユーザID</th>
            <th style="text-align:center;vertical-align:middle;width: 10%;">区分</th>
            <th style="text-align:center;vertical-align:middle;width: 10%;">端末</th>
            <th style="text-align:center;vertical-align:middle;width: 10%;">機能</th>
            <th style="text-align:center;vertical-align:middle;width: 30%;">内容</th>
            <th style="text-align:center;vertical-align:middle;width: 5%;">動作</th>
          </tr>
        </thead>
        
        <tbody>  
            <?php foreach ($data['message_list'] as $key => $row): { ?>
                <tr>
                    <td style='text-align:center;vertical-align:middle'><?php echo substr($row['send_time'],0,strpos($row['send_time'], '.')); ?></td>
                    <td style='text-align:center;vertical-align:middle'><?php echo $row['company_id']; ?></td>
                    <td style='text-align:center;vertical-align:middle'><?php echo $row['user_id']; ?></td>
                    <td style='text-align:center;vertical-align:middle'><?php echo $row['code']; ?></td>
                    <td style='text-align:center;vertical-align:middle'><?php echo $row['terminal']; ?></td>
                    <td style='text-align:center;vertical-align:middle'><?php echo $row['function']; ?></td>
                    <td style='text-align:center;vertical-align:middle'><?php echo $row['content']; ?></td>
                    <td style='text-align:center;vertical-align:middle'>
                        <form class="row-form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" style="display: inline-flex;"> 
                            <input style="display:none;" name = "message_id" id="message_id" value="<?php echo $row['id'] ?>"/>  
                            <button type="submit" class="btn btn-default" name="submit" value="delete_message" id="delete_message" style="width: 50px;margin-left: 5px;margin-right: 2px;">削除</button>               
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