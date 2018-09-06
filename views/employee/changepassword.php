
<div class="col-sm-offset-3 col-sm-6" >
    <form id="register-form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
    	<h4 class="modal-title">パスワード変更</h4>
    	<table class="regisTable" style="overflow: scroll";>
    	    <tr class="<?php if ($data['password_error'] === true): ?>
                        <?php echo "has-warning" ?>
                    <?php endif ?>">
    	        <th style="width: 120px;padding: 6px;vertical-align:middle; background: #A9D0F5;">新パスワード</th>
    	        <td style="margin-left: 5px;padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
    	            <input maxlength="30" style="width: 230px;" type="password" class="form-control input-sm" id="password1" name="password1" value="<?php echo $data["password1"] ?>"></input>
    	        </td>
    	    </tr>
    	    <tr class="<?php if ($data['password_error'] === true): ?>
                        <?php echo "has-warning" ?>
                    <?php endif ?>">
                <th style="width: 120px;padding: 6px;vertical-align:middle; background: #A9D0F5;">確認パスワード</th>
                <td style="margin-left: 5px;padding-bottom: 2px;padding-top: 2px; display: inline-flex;">
                    <input maxlength="30" style="width: 230px;" type="password" class="form-control input-sm" id="password2" name="password2" value="<?php echo $data["password2"] ?>"></input>
                </td>
            </tr>
    	</table>
    	<div style="text-align: right;">
            <button type="submit" class="btn btn-default" name="submit" value="editPassword" id="editPassword" ><?php echo _("新規登録") ?></button>
            <a class="btn btn-default" name="submit" value="cancel_edit" id="cancel_edit" style="margin-right: 125px;" 
                href="<?php echo $this->url("employee","changepassword") ?>"
            >
                <?php echo _("キャンセル") ?>
            </a>
                    
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
    </form>
</div><!-- /.modal-dialog -->
