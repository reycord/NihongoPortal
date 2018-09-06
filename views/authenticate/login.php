<?php
require_once 'models/database.php';
 	$company = 0;
	if (isset($_GET['company_id'])) {
	$company=$_GET['company_id'];
							 }	
?>
<div class="container">
	<div class="col-xs-6" style="text-align: left; color: #37699a">
		<div class="level logo_kpi"></div>
	</div>
</div>
<div class="container" style= "margin-top: 150px;">
	<div class="container" align="center">
		<form id="loginform" class="form-horizontal" name="login" method="post" action="">
			 <?php 
	            if ($data['success'] == false) {
	                echo sprintf('<div class="alert alert-danger col-sm-offset-3 col-sm-6" role="alert"><strong>エラー：</strong>%s</div>', $data['message']);
	            }	            
	         ?>

	         <div class="form-group">
			    <label for="company_id" class="col-sm-offset-3 col-xs-offset-3 col-sm-2 col-xs-4 control-label titlehoz" style="width: 120px;padding: 7px;">所属会社:</label>
			    <div class="col-sm-5 col-xs-8" >
			      	
					<input type="text" class="form-control" id="company_id" name="company_id" value="<?php echo ($_POST['company_id'] != "" ? $_POST['company_id'] : $_GET['company_id']) ?>"></input>
			    
					</div>
			  </div>
			  <div class="form-group">
			    <label for="inputuser_id" class="col-sm-offset-3 col-xs-offset-3 col-sm-2 col-xs-4 control-label titlehoz" style="width: 120px;padding: 7px;">ID:</label>
			    <div class="col-sm-5 col-xs-8">
			      <input type="text" class="form-control" id="inputuser_id" name="user_id" value="<?php  echo ($_POST['user_id'] != "" ? $_POST['user_id'] : $_GET['user_id']) ?>"></input>
			    </div>
			  </div>
			  <div class="form-group">
			    <label for="inputpassword" class="col-sm-offset-3 col-xs-offset-3 col-sm-2 col-xs-4 control-label titlehoz" style="width: 120px;padding: 7px;">パスワード:</label>
			    <div class="col-sm-5 col-xs-8">
			      <input type="password" class="form-control" id="inputpassword" name="password"></input>
			    </div>
			  </div>		  
			  <div class="form-group">
			  	<div class="col-sm-offset-4 col-xs-offset-4 col-sm-5 col-xs-8"  style="text-align:center;">
	            	<button type="submit" class="btn btn-default" name="submit" value="login"><?php echo _("ログイン"); ?></button>
	           	</div>
	          </div>
	          <div>
	              <p style="font-size: 14px;margin-top: 40px;width: 594px;text-align: right;font-style: italic;">サポート：Chorme42以上、IE9以上、Firefox37以上</p>
	          </div>
	        <?php
	                if($data['user_id_error_message'] != null)
	                echo sprintf('<span class="help-block">%s</span>',$data['user_id_error_message']);
	            ?>
	
			    <?php
	                if($data['password_error_message'] != null)
	                echo sprintf('<span class="help-block">%s</span>',$data['password_error_message']);
	         ?> 
		</form>   
     </div>
</div>


