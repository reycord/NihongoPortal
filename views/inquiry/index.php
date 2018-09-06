<script>
	function changeContent(){
      $('#message_div').hide();
    }
 </script>
 
<div class="container"> 
  <div class="col-xs-8" style="margin:0 auto; float: inherit;">
    <form style="height: 100px; margin-top: 7px;" id="inquiry-form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
 
        <div class="col-xs-12" style="border: 1px solid #e4e4e4;padding:10px 20px;">
            <h4 class="modal-title">問合せ</h4>            
            <table id="regisDataMessage" class="col-xs-12 regisTable" >                 
                <tr>
                    <th style="width: 100px;padding: 6px;vertical-align:middle; background: #A9D0F5;">区分</th>                 
                    <td style="padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
                        <select style="margin-left: 5px;width: 150px; height: 25px; padding: 1px;" id="message_code" name="message_code" class="form-control input-sm"  onChange="changeContent()">
                            <option value="改善要望" <?php echo ($data['message_code']=='改善要望') ? ' selected="selected"' : '';?> >改善要望</option>
                            <option value="不具合"	<?php echo ($data['message_code']=='不具合') ? ' selected="selected"' : '';?> >不具合</option>                  
                        </select>
                    </td>                
                </tr>
                <tr>
                    <th style="width: 100px;padding: 6px;vertical-align:middle; background: #A9D0F5;">端末</th>                 
                    <td style="padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
                        <select style="margin-left: 5px;width: 150px; height: 25px; padding: 1px;" id="message_terminal" name="message_terminal" class="form-control input-sm"  onChange="changeContent()">
                            <option value="PC"		<?php echo ($data['message_terminal']=='PC') ? ' selected="selected"' : '';?> >PC</option>
                            <option value="Android"	<?php echo ($data['message_terminal']=='Android') ? ' selected="selected"' : '';?> >Android</option>    
                            <option value="iOS"		<?php echo ($data['message_terminal']=='iOS') ? ' selected="selected"' : '';?> >iOS</option>                 
                        </select>
                    </td>              
                </tr> 
                <tr>
                    <th style="width: 100px;padding: 6px;vertical-align:middle; background: #A9D0F5;">機能</th>                 
                    <td style="padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
                        <select style="margin-left: 5px;width: 150px; height: 25px; padding: 1px;" id="message_function" name="message_function" class="form-control input-sm"  onChange="changeContent()">
                            <option value="ログイン" 		<?php echo ($data['message_function']=='ログイン') ? ' selected="selected"' : '';?> >ログイン</option>
                            <option value="ホーム画面" 	<?php echo ($data['message_function']=='ホーム画面') ? ' selected="selected"' : '';?>>ホーム画面</option>    
                            <option value="ユーザ状況一覧" <?php echo ($data['message_function']=='ユーザ状況一覧') ? ' selected="selected"' : '';?>>ユーザ状況一覧</option>   
                            <option value="KPI一覧" 		<?php echo ($data['message_function']=='KPI一覧') ? ' selected="selected"' : '';?>>KPI一覧</option> 
                            <option value="KPI登録" 		<?php echo ($data['message_function']=='KPI登録') ? ' selected="selected"' : '';?>>KPI登録</option> 
                            <option value="KPI実績登録" 	<?php echo ($data['message_function']=='KPI実績登録') ? ' selected="selected"' : '';?>>KPI実績登録</option> 
                            <option value="ファイルIMPORT/EXPORT" <?php echo ($data['message_function']=='ファイルIMPORT/EXPORT') ? ' selected="selected"' : '';?>>ファイルIMPORT/EXPORT</option> 
                            <option value="問合せ" 		<?php echo ($data['message_function']=='問合せ') ? ' selected="selected"' : '';?>>問合せ</option>               
                        </select>
                    </td>              
                </tr> 
                <tr>
                    <th style="width: 100px;padding: 6px;vertical-align:middle; background: #A9D0F5;">内容</th>                 
                    <td style="padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
                        <textarea style="margin-left: 5px;width:500px; height:100px;" id="message_content" name="message_content" class="form-control input-sm" onChange="changeContent()" ><?php echo $data['message_content'] ?></textarea>
                    </td>              
                </tr> 
                <!-- Captcha -->
                <!-- <tr>
                    <th style="width: 100px;padding: 6px;vertical-align:middle; background: #A9D0F5;"></th>                 
                    <td style="padding-bottom: 2px;padding-top: 2px;">
                        <input type="text" name="txtCaptcha" maxlength="10" size="32" style="margin: 0 5px; float: left; width: 150px;height:30px;"/>
                        <img src="views/inquiry/captcha.php" />
                    </td>              
                </tr>  -->
                <tr>       
                	<th style="width: 100px;padding: 6px;vertical-align:middle;border: none;"></th>           
                    <td style="padding-bottom: 2px;padding-top: 2px; float: right;">
                        <button class="btn btn-default" type="submit" name="submit" value="send_message" id="send_message" style="width: 80px;margin-top: 5px; float: right;">送信</button>
                    </td>              
                </tr> 
                  
             </table>
        </div>
        
        <div class="" style="float: left;width: 100%;z-index: 999; margin-top:5px;" id="message_div">  
            <div id="message_div_error" <?php if ($data['success'] == true): ?> style="display: none; padding: 5px;" <?php endif ?> 
                id="error_div"class="alert alert-warning alert-dismissible  " role="alert">
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
  </form> 
  
</div>

<?php require_once __DIR__. "/../kpiresult/include.php" ?>