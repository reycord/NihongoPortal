<!DOCTYPE html>
<html>
    <head>
    	
    	<meta http-equiv="Content-Type"content="application/xhtml+xml; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <!-- <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css"> -->
        
        <link rel="stylesheet" href="resources/css/bootstrap.min.css">
        <link rel="stylesheet" href="resources/css/bootstrap-datepicker3.min.css">
        <link rel="stylesheet" href="resources/css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="resources/css/reset.css">
        <link rel="stylesheet" href="resources/css/style.css">
		
		<link rel="shortcut icon" type="image/x-icon" href="resources/icon/favicon.ico">
        
        <script src="resources/js/jquery.js"></script>
        <script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script src="resources/js/bootstrap.min.js"></script>
        
        <!-- <script src="resources/js/bootstrap-datepicker.min.js"></script> -->
        <!-- <script src="resources/js/bootstrap-datepicker.ja.min.js"></script> -->
        <script src="resources/js/myFunction.js"></script>
        <script src="resources/js/bootstrap-datepicker.js"></script>
        
        <script src="resources/js/jquery.dataTables.min.js"></script>
        <script src="resources/js/dataTables.bootstrap.min.js"></script>
        <script src="resources/js/dataTables.foundation.min.js"></script>
        <script src="resources/js/dataTables.jqueryui.min.js"></script>
		<script src="resources/js/jsapi.min.js"></script>
		<script src="resources/js/jquery.watermark.min.js"></script>
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script src="resources/js/jquery.validate.min.js"></script>
		<script src="resources/js/additional-methods.js"></script>
		<!-- <script src="resources/js/jquery.validate.messages_ja.js"></script> -->
		<script src="resources/js/jquery.chained.min.js"></script>
		
		<?php require_once 'views/javascript.php'; ?>
		
        <!-- <link rel="apple-touch-icon" sizes="57x57" href="resources/icon/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="resources/icon/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="resources/icon/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="resources/icon/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="resources/icon/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="resources/icon/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="resources/icon/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="resources/icon/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="resources/icon/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="resources/icon/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="resources/icon/icon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="resources/icon/icon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="resources/icon/icon-16x16.png">
        <link rel="manifest" href="resources/icon/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="resources/icon/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff"> -->

        <title>KPI</title>
    </head>
    <body>
    	<div id="page-wrap">
	    	<div class="container" >
	    		<div class="col-xs-5">
					<div class="level logo_kpi"></div>
				</div>
                <div class="col-xs-7" style="text-align: right;  margin-top: 30px;">
                    <!-- search year, ID -->
                     <label style="font-weight: bold;font-size: 20px"><?php echo User::getCurrentUser()->company_name ?></label>
                </div>
	    	</div>
	        <nav class="navbar navbar-default">
	            <div class="container">
	                <div class="navbar-header" style="width: 100%;">
	                	<ul class="nav navbar-nav" style="margin-left: 15px; float: left;">
	                	    <?php if (User::getCurrentUser()->maintenance_flag == true): ?>
							<li <?php if ($this->route->getControllerName() == 'company'): ?>
                                    class="active"
                                <?php endif ?>>
                                <a style="<?php (User::getCurrentUser()->maintenance_flag === true)?"" :"display: none;" ?>" href="<?php echo $this->url("company") ?>">会社管理</a>
                            </li>
							<?php endif ?>
	                	    <?php if (User::getCurrentUser()->maintenance_flag == true || User::getCurrentUser()->admin_flag == 1): ?>
	                		<li <?php if ($this->route->getControllerName() == 'department'): ?>
									class="active"
								<?php endif ?>>
	                			<a href="<?php echo $this->url("department") ?>">所属管理</a>
	                		</li>
	                		<?php endif ?>
	                		<li <?php if ($this->route->getControllerName() == 'user'): ?>
									class="active"
								<?php endif ?>>
	                			<a href="<?php echo $this->url("user") ?>">ユーザー管理</a>
	                		</li>                		  
	                        <?php if (User::getCurrentUser()->maintenance_flag == true): ?>
                                <li <?php if ($this->route->getControllerName() == 'category'): ?>
    									class="active"
    								<?php endif ?>>
    								<a  href="<?php echo $this->url("category") ?>">カテゴリ管理</a>
                                </li>
                            <?php endif ?>
                            <?php if (User::getCurrentUser()->maintenance_flag == true): ?>
                                <li <?php if ($this->route->getControllerName() == 'inquirymanager'): ?>
    									class="active"
    								<?php endif ?>>
    								<a  href="<?php echo $this->url("inquirymanager") ?>">問合せ管理</a>
                                </li>
                            <?php endif ?>
                            <?php if (User::getCurrentUser()->maintenance_flag == true): ?>
                                <li <?php if ($this->route->getControllerName() == 'release'): ?>
    									class="active"
    								<?php endif ?> role="presentation" class="dropdown">
    								
	                            <a style="" class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"> 
	                                	その他 <span class="caret"></span> 
                                </a> 
                                <ul class="dropdown-menu"> 
                                    <li>
                                        <a href="<?php echo $this->url("release") ?>">リリース管理</a>
                                    </li> 
                                    <li>
                                        <a href="<?php echo $this->url("termsmanager") ?>">利用規約管理</a>
                                    </li>  
                                </ul> 
                            </li>
                            <?php endif ?>
                            
                            
	                                              
	
	                    </ul>
	                    <ul class="nav navbar-nav navbar-right" style="float: right;">
	                        <li role="presentation" class="dropdown"> 
                                <a style="font-size:18px; color:#5124F2;" class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"> 
                                    <?php echo User::getCurrentUser()->name; ?> <span class="caret"></span> 
                                </a> 
                                <ul class="dropdown-menu"> 
                                    <li>
                                        <a href="<?php echo $this->url("employee","changepassword") ?>">パスワード変更</a>
                                    </li> 
                                    <!-- <li role="separator" class="divider">
                                        
                                    </li> --> 
                                </ul> 
                            </li>
	                        <li style="<?php echo (User::getCurrentUser()->maintenance_flag)? "display: none;":"" ?>">
                                <a href="<?php  echo $this->url("home") ?>">管理画面を出る</a>
                            </li>
	                        <li class="" >
							   <a href="<?php echo $this->url("authenticate", "logout") ?>"><?php						   
				                    echo _("ログアウト") ; ?>
				                </a>
				                           
	                        </li>
	                    </ul>
	                </div>
	            </div>
	        </nav>
	        <div class="main">
	            <?php require($this->viewFile); ?>
	        </div>
		</div>		
    </body>
</html>