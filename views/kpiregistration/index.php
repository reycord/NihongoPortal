<script>

    function isNumberKey(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

    $(function(){ 
        var startdate = null;
        var enddate = null;
        
        $('#months').datepicker({
            format: "mm",
            startDate: new Date(<?php echo date("Y") ?>, 0, 1),
            endDate: new Date(<?php echo date("Y")?>, 11, 31),
            startAtMonth: <?php echo $data["startAtMonth"] ?>,
            minViewMode: 1,
            maxViewMode: 1,
            language: "ja",
            multidate: true,
            multidateSeparator: "-"
        });
        // .on("changeDate", function(e) {
            // var flag = false;
            // for (var i=0; i < e.dates.length; i++) {
                // if(e.dates[i].getDate() != 1){
                    // e.dates[i].setDate(1);
                    // flag=true;
                // }
                // if(e.dates[i].getFullYear() != <?php echo date("Y") ?>){
                    // e.dates[i].setFullYear(<?php echo date("Y") ?>);
                    // flag=true;
                // }
            // } 
            // if(flag) {
                // $(this).datepicker('setDates', e.dates);
            // }
        // });
        
        $('#register-form').submit(function() {
            // DO STUFF
            var btn= $(this).find("button[type=submit]:hover").val();
           
            if (btn == 'update'){
                // return false to cancel form action
                var selected_row = $('#dataTable-list >tbody >tr.info')[0]; 
                $("#error_div").hide();                    
            }
            if (btn == 'save'){
                
            }
            return true; 
        });
        $("#user_id").change(function(){
        
            $(location).attr('href', '<?php echo $this->url("kpiregistration") ?>'+ "&user_id=" + $(this).val());
        });
        
        $("#list_btn-up").click (function () {
            $("#list_btn-up").css("display", "none");
            $("#list_btn-down").css("display", "block");
            $("#listTable").css("display", "block");
        });
        
        $("#list_btn-down").click (function () {
            $("#list_btn-up").css("display", "block");
            $("#list_btn-down").css("display", "none");
            $("#listTable").css("display", "none");
        });
        
         $("#clear_list-btn").click (function () {
             var input_arr = $("#listTable input");
             $.each(input_arr, function (key, value) {
                $(value).val("");
            })
        });
        
        $("#clear_month-btn").click (function () {
            var input_arr = $('#goalMonthTable input');
            $.each(input_arr, function (key, value) {
                $(value).val("");
            })
            
        });
        $("#cancel_month-btn").click (function () {
            $("#goalMonthDiv").hide();
        });
        
        $("#cancel_list-btn").click (function () {
            var input_arr = $('#goalMonthTable input');
            $.each(input_arr, function (key, value) {
                $(value).val("");
            });
            
        });
        

        $('#register-form select[name=category_kpi]').change(function() {
           $("#message_div_error").hide();
           $("#success_div").hide();
           
           $('#register-form tr.has-warning:has(select[name=category_kpi])').removeClass('has-warning');
        });
        $('#register-form input[name=kpi_name]').change(function() {
           $("#message_div").hide();
           $('#register-form tr.has-warning:has(input[name=kpi_name])').removeClass('has-warning');
        });
        $('#register-form input[name=months]').change(function() {
           $("#message_div").hide();
           $('#register-form tr.has-warning:has(input[name=months])').removeClass('has-warning');
        });
        
        $('#register-form input[name="goal_markMonth[]"]').change(function() {
            $("#message_div").hide();
           $(this).closest('td').removeClass('has-warning');
           if (/\d+/m.test($(this).val()) == false){
               $(this).val("");
               return;
           }
            var $sum = 0; 
            var input_arr = $('#goalMonthTable input[name="goal_markMonth[]"]:not([disabled])');
            $.each( input_arr, function( key, input ) {
                var val = parseInt(input.value);
                if (isNaN(val) == true){
                    $sum  += 0;
                }
                else{
                    $sum += val;
                }
            });
            $("#goal").val( $sum);
            $("#goal").closest('td').removeClass('has-warning');
            
        });
        $('#register-form input[name="goal_month[]"]').change(function() {
            $("#message_div_error").hide();
           $(this).closest('td').removeClass('has-warning');
        });

        $('#listTable input[name="option[]"]').change(function() {
            $("#message_div").hide();
           $(this).closest('td').removeClass('has-warning');
           if (/\d+/m.test($(this).val()) == false){
               $(this).val("");
               return;
           }
           
        });
    });
    
    function validate(evt) {
          var theEvent = evt || window.event;
          var key = theEvent.keyCode || theEvent.which;
          key = String.fromCharCode( key );
          var regex = /[0-9]|\./;
          if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
          }
        }
    
   
    
    function cancel(){
        //hien luu, hien huy
        $("#save").show();
        $("#update").hide();
        $("#save").hide();
        $("#copy").hide();
        $("#edit").hide();
        $("#delete").hide();
        
        // xoa trang
    }
    
    function goalMonth(evt){
		if(evt == 'show') {
			$("#goalMonthDiv").show();
	        $("#message_div_error").hide();
	        $("#success_div").hide();
		}
        
        var month_arr = $('#months').val().split("-");
        var input_arr = $('#goalMonthTable input');

        $.each( input_arr, function( key, value ) {
              $(value).prop( "disabled", true ); //Disable
        });
            
        $.each( month_arr, function( key, value ) {
	        if(value != 0) {
	        	var month_arr_index = parseInt(value * 2) - 2 - (<?php echo $data["startAtMonth"] ?> - 1) * 2;
		          if(month_arr_index < 0) {
		          	month_arr_index += 24;
		          }
	        }
          
          $(input_arr[month_arr_index]).prop( "disabled", false ); //Enable $goal_month_arr
          $(input_arr[month_arr_index + 1]).prop( "disabled", false ); //Enable $goal_month_arr
          $(input_arr[month_arr_index]).watermark('月の詳細目標');
          $(input_arr[month_arr_index + 1]).watermark('値');    
                 
          // $(input_arr[parseInt(value *2) - 2]).prop( "disabled", false ); //Enable $goal_month_arr
          // $(input_arr[parseInt(value *2) - 1]).prop( "disabled", false ); //Enable $goal_month_arr
          // $(input_arr[parseInt(value *2) - 2]).watermark('月の詳細目標');
          // $(input_arr[parseInt(value *2) - 1]).watermark('値');
                                        
          // $(input_arr[parseInt(value *2) - 2]).attr('placeholder','月の実施内容');
          // $(input_arr[parseInt(value *2) - 1]).attr('placeholder','点数');        
        });
    }
</script>

<div class="container"> 

    <form style="height: 360px;" id="register-form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
     
        <div class="col-sm-4 col-xs-4" style="text-align: left;">           
            <h4 class="modal-title">担当者</h4>
             <table class="table table-bordered">           
                <tr  >
                    <th style="width: 100px;text-align:center;vertical-align:middle; background: #A9D0F5;">所属会社</th>
                    <td style="text-align:left;vertical-align:middle; padding-left: 15px"><?php echo $data['info']['company_name'] ?></td>                  
                </tr>
                <tr >
                    <th style="width: 100px;text-align:center;vertical-align:middle; background: #A9D0F5;">所属名</th>
                    <td style="text-align:left;vertical-align:middle; padding-left: 15px"><?php echo $data['info']['dep_name'] ?></td>                  
                </tr>
                <tr >
                    <th style="width: 100px;text-align:center;vertical-align:middle; background: #A9D0F5;">上司名</th>
                    <td style="text-align:left;vertical-align:middle; padding-left: 15px"><?php echo $data['info_leader_user']['name'] ?></td>                  
                </tr>
                <tr>
                    <th style="width: 100px;text-align:center;vertical-align:middle; background: #A9D0F5;">名　前</th>
                    <td style="text-align:left;vertical-align:middle; padding-left: 15px;">
                        <?php if (User::getCurrentUser()->admin_flag == 1 || User::getCurrentUser()->admin_flag == 2 || User::getCurrentUser()->isLeader()) { ?>
                            <select style="width: 150px; height: 25px; padding: 1px;" id="user_id" name="user_id" class="form-control input-sm" >
                                <?php foreach ($data['user_arr'] as $key => $u): ?>                                                                    
                                        <option value="<?php echo $key ?>"
                                        <?php if ((!isset($_GET['user_id']) && !isset($data['user_id_kpi']) && $key == User::getCurrentUser()->user_id )
                                        ||($key == $data['user']->user_id && $_GET['user_id'] != "") 
                                        || ($_GET['kpi_id'] !="" && $key == $data['user_id_kpi'])): ?> selected<?php endif ?>>
                                        <?php echo $u?></option>
                                 <?php endforeach ?>                            
                            </select>
                        <?php }else { ?>
                            <?php echo $data['user']->name ?>
                        <?php  } ?>     
                    </td>               
                </tr>
                <tr>
                    <th style="width: 100px;text-align:center;vertical-align:middle; background: #A9D0F5;">USER ID</th>
                    <td style="text-align:left;vertical-align:middle; padding-left: 15px"><?php echo $data['info']['user_id'] ?></td>              
                </tr>
                
                
             </table>
        </div>
        <div class="col-sm-5 col-xs-5" style="width: 37%;">
            <input id="kpi_id" type="hidden" name="kpi_id" value="<?php echo $data['kpi_id'] ?>">       
            
            <h4 class="modal-title">KPI詳細登録</h4>            
            <table id="regisTable" class="table" height="100px"; style="overflow: scroll";>         
                <tr class="<?php if ($data['category_kpi_error'] === true): ?>
                    <?php echo "has-warning" ?>
                <?php endif ?>">
                    <th style="width: 100px;text-align:center;vertical-align:middle; background: #A9D0F5;">カテゴリ</th>
                    <td colspan="3" style="padding-bottom: 2px;padding-top: 2px;">
                        <select style="width: 170px;" id="parent-kpi" name="category_kpi" class="form-control input-sm" >
                            <option value="">--</option>
                            <?php foreach ($data['category_arr'] as $key => $c): ?>                                                                   
                                    <option value="<?php echo $c ?>"
                                    <?php if ($c == $data['category_arr'][$data['category_kpi']]): ?> selected<?php endif ?>>
                                    <?php echo $c?></option>
                             <?php endforeach ?>
                        </select>
                    </td>               
                </tr>            
                <tr class="<?php if ($data['kpi_name_error'] === true): ?>
                    <?php echo "has-warning" ?>
                <?php endif ?>">
                    <th style="width: 100px;text-align:center;vertical-align:middle; background: #A9D0F5;">KPI</th>                 
                    <td colspan="3" style="padding-bottom: 2px;padding-top: 2px;">
                        <input maxlength="30" style="width: 230px;"type="text" class="form-control input-sm" id="kpi_name" name="kpi_name" value="<?php echo $data["kpi_name"] ?>"></input>
                    </td>               
                </tr>
                
                <tr class="<?php if ($data['months_error'] === true): ?>
                    <?php echo "has-warning" ?>
                <?php endif ?>">
                    <th style="width: 100px;text-align:center;vertical-align:middle; background: #A9D0F5;">期間</th>
                    <td colspan="3" style="padding-bottom: 2px;padding-top: 2px;">
                        <input style="width: 230px;" type="text" class="form-control input-sm" id="months" name="months" value="<?php echo $data["months"] ?>" onchange="goalMonth()" ></input>
                    </td>               
                </tr>
                <tr >
                    <th style="width: 100px;text-align:center;vertical-align:middle; background: #A9D0F5;">目標値</th>
                    <td style="display: inline-flex;" class="<?php if ($data['goal_error'] === true): ?>
                                    <?php echo "has-warning" ?>
                                <?php endif ?>">
                           <input readonly="true" onkeypress="return isNumberKey(event)" maxlength="5" style="width: 85px;float: left;" type="text" class="form-control input-sm" id="goal" name="goal" value="<?php echo $data["goal"] ?>"></input>          
                    </td>
                    <!-- <td>
                        <select style="width: 70px;float: left;margin: 0px;" id="point_kpi" name="point_kpi" class="form-control input-sm" >
                            <?php foreach ($data['pointList'] as $key => $c): ?>                                                                    
                                    <option value="<?php echo $c ?>"
                                    <?php if ($c == $data['point_kpi']): ?> selected<?php endif ?>>
                                    <?php echo $c?></option>
                             <?php endforeach ?>
                        </select>
                    </td>
                    <td>
                        <span style="padding-top: 2px;width: 110px;text-align:center;vertical-align:middle;">ポイント/目標値</span>
                    </td>  -->        
                </tr>
                
                <tr>
                    <th style="width: 100px;text-align:center;vertical-align:middle; background: #A9D0F5;">コメント</th>
                    <td colspan="3" >                            
                        <input maxlength="30" style="width: 230px;" type="text" class="form-control input-sm" id="comment_user" name="comment_user" value="<?php echo $data["comment_user"] ?>"></input>            
                    </td>               
                </tr>
             </table>
              <div style="text-align: right;">
                    <button type="submit" class="btn btn-default" name="submit" value="update" id="update"href="javascript:update()" style=" <?php echo isset($_GET['kpi_id'])? "":"display:none;" ?>
                        " >更新</button>
                    <?php $array = ($_GET['year'] == "")? array("kpi_id"=>$_GET['kpi_id']) : array("kpi_id"=>$_GET['kpi_id'],"year"=>$_GET['year']); ?>
                    <a class="btn btn-default" name="submit" value="cancel_update" id="cancel_update" style=" <?php echo isset($_GET['kpi_id'])? "":"display:none;" ?>
                        " href="<?php echo $this->url("list","index", $array) ?>"><?php echo _("キャンセル") ?></a>
                    <button type="submit" class="btn btn-default" name="submit" value="save" id="save" style=" <?php echo isset($_GET['kpi_id'])? "display:none;":"" ?>
                        "><?php echo _("新規登録") ?></button>                                                 
                    <button class="btn btn-default" name="submit" value="cancel" id="cancel" style="<?php echo isset($_GET['kpi_id'])? "display:none;":"" ?>
                        "><?php echo _("クリア") ?></button>
                    <a class="btn btn-default" name="submit" value="goalMonth" id="goalMonth" 
                    style="<?php echo ($data['goal_month_error'] == true ||$data['goal_markMonth_error'] == true )? "border-color: #7C7D11;": "" ?>"
                     href="javascript:goalMonth('show')"><?php echo _("次へ") ?></a>
        </div>  
        <div class="" style="margin-top: 20px;" id="message_div">
                
            <div id="message_div_error" <?php if ($data['success'] == true): ?> style="display: none; padding: 5px;" <?php endif ?> id="error_div"class="alert alert-warning alert-dismissible  " role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
              <strong>警告： </strong>
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
        <div  class="col-sm-3 col-xs-3" style="text-align: left;width:29%; display: none" id="goalMonthDiv">           
             <table id="goalMonthTable" > 
                 <?php $month_arr = array_map(function($n) {return (int)$n;}, explode("-", $data['months'])); ?> 
                 <?php for ($i=$data['startAtMonth']; $i <= 12 ; $i++):  ?>
                     <?php $is_exist = in_array(sprintf("%02d", $i), explode("-", $data['months'])) ?>
                       <tr>
                            <th style="width: 50px;text-align:center;vertical-align:middle; background: #A9D0F5;"><?php echo $i."月"; ?></th>
                            <td class="">
                                <!-- <?php  echo (in_array(sprintf("%20d",$i), explode("-", $data['months']))==true && $data['goal_month'][$i-1] == "") ? "has-warning" : ""?> -->
                                <input type="text" name= "goal_month[]"  style="margin-left:5px; width: 160px; float: left;"
                                 type="text" class="form-control input-sm" value="<?php echo $data['goal_month'][$i-1] ?>"></input>
                            </td> 
                            <td class="<?php $data['goal_markMonth_error'] == true? "has-warning": ""?>">
                                <input type="text" onkeypress="return isNumberKey(event)" style="width: 40px; float: left;" type="text" class="form-control input-sm" 
                                name= "goal_markMonth[]" value="<?php echo $data['goal_markMonth'][$i-1] ?>"></input>
                            </td>
                      </tr>
                <?php endfor ?>
                
                <?php for ($i=1; $i < $data['startAtMonth'] ; $i++):  ?>
                     <?php $is_exist = in_array(sprintf("%02d", $i), explode("-", $data['months'])) ?>
                       <tr>
                            <th style="width: 50px;text-align:center;vertical-align:middle; background: #A9D0F5;"><?php echo $i."月"; ?></th>
                            <td class="">
                                <!-- <?php  echo (in_array(sprintf("%20d",$i), explode("-", $data['months']))==true && $data['goal_month'][$i-1] == "") ? "has-warning" : ""?> -->
                                <input type="text" name= "goal_month[]"  style="margin-left:5px; width: 160px; float: left;"
                                 type="text" class="form-control input-sm" value="<?php echo $data['goal_month'][$i-1] ?>"></input>
                            </td> 
                            <td class="<?php $data['goal_markMonth_error'] == true? "has-warning": ""?>">
                                <input type="text" onkeypress="return isNumberKey(event)" style="width: 40px; float: left;" type="text" class="form-control input-sm" 
                                name= "goal_markMonth[]" value="<?php echo $data['goal_markMonth'][$i-1] ?>"></input>
                            </td>
                      </tr>
                <?php endfor ?>        
                
              </table>
              <div style="margin-top: 5px;"id="button-div" >
                  <a type="submit" class="btn btn-default" name="submit" value="clear_month-btn" id="clear_month-btn" ><?php echo _("クリア") ?></a>
                                                                   
                  <a type="submit" class="btn btn-default" name="submit" value="cancel_month-btn" id="cancel_month-btn" ><?php echo _("戻る"); ?></a>              
                    
              </div>
             
        </div>
    <div class="container">
        
    </div>

    </form>
    
    <div>
    	<div style="float: left;">
	        <span style="padding-left: 10px;">その他</span> 
	    </div>
        <div class="col-sm-1 col-xs-1" style="text-align: left;">
            <a id="list_btn-up" style="width: 35px;text-align: center;" class="btn btn-default">+</a>
            <a id="list_btn-down" style="display:none; width: 35px;text-align: center;" class="btn btn-default">-</a><br />
        
        </div>
        <div class="col-sm-5 col-xs-5" style="margin-top: 20px;"> 
            <table id="listTable" style="display: none;text-align: right;">
            <tr>
                <td >
                    <span style="padding-left: 10px;width: 160px;">数値管理項目１</span> 
                    <input name="option[]" onkeypress="return isNumberKey(event)" style="width: 200px;height: 20px;margin-left: 5px;margin-top: 2px;"/>     
                </td>
            </tr>
            <tr>
                <td >
                    <span style="padding-left: 10px;width: 160px;">数値管理項目２</span> 
                    <input name="option[]" onkeypress="return isNumberKey(event)" style="width: 200px;height: 20px;margin-left: 5px;margin-top: 2px;"/>     
                </td>
            </tr>
            <tr>
                <td >
                    <span style="padding-left: 10px;width: 160px;">その他管理項目１</span> 
                    <input style="width: 200px;height: 20px;margin-left: 5px;margin-top: 2px;"/>     
                </td>
            </tr>
            <tr>
                <td >
                    <span style="padding-left: 10px;width: 160px;">その他管理項目２</span> 
                    <input style="width: 200px;height: 20px;margin-left: 5px;margin-top: 2px;"/>     
                </td>
            </tr>
            <tr>
                <td>
                    <a class="btn btn-default" name="submit" value="clear_list-btn" id="clear_list-btn" style="margin-top: 5px;"><?php echo _("クリア") ?></a>
                    <a class="btn btn-default" name="submit" value="save_list-btn" id="save_list-btn" style="margin-top: 5px;"><?php echo _("登録") ?></a>
                
                </td>
            </tr>
 
        </table>
        </div>
                         
    </div>
     
</form> 
</div>
<?php require_once __DIR__. "/../kpiresult/include.php" ?>



 