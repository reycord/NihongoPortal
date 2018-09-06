
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
		<script src="resources/js/jquery.chained.min.js"></script>

        <title>KPI</title>
    </head>
    <body>
    	<div id="page-wrap">
	    	<div class="container" style="height: 62px;" >
	    		<div class="col-xs-5">
					<div class="level logo_kpi"></div>
				</div>

	    		<div class="col-xs-7" style="text-align: right;  margin-top: 10px; padding: 0; margin-bottom: 5px;">
	    			<!-- search year, ID -->
	    		     <label style="font-weight: bold;font-size: 18px; margin-bottom: 4px; margin-right: 1px;"><?php echo User::getCurrentUser()->company_name ?></label>
	                 <div class="container1" style="text-align: right">
	               		<form action="<?php echo $_SERVER['REQUEST_URI'] ?>">
	               			<?php foreach ($_GET as $key => $value): ?>
	               				<?php if ($key != 'year' && $key != 'user_id'): ?>
									<input hidden="hidden" name="<?php echo $key ?>" value="<?php echo $value ?>"/>
								<?php endif ?>
							<?php endforeach ?>
		
							<?php if ($this->route->getControllerName() == 'home' || $this->route->getControllerName() == 'list'): ?>
								<input type="submit" class="btn btn-default" style="height: 26px;padding:0 10px;float: right;" value="ID検索">
								<input type="text" class="form-control input-sm"  id="inputsearch" name="search" value ="<?php echo $data['search'] ?>" style="height: 26px; width: 150px;margin: 0 2px;float: right;background: #F7F8E0;"></input>
								<select name="year" class="form-control input-sm" style="height: 26px; width: 75px;float: right;background: #F7F8E0;">                				         				
									<?php foreach ($data['yearList'] as $key => $y): ?>
										<option value="<?php echo $y ?>" <?php if ($y == $data['year'] || $y == $_GET['year']): ?> selected<?php endif ?>><?php echo $y ?></option>
									<?php endforeach ?>
								</select>	
							<?php endif ?>		
						      
						      					   
	               		</form>
						
				   </div>
	    		</div>
	    	</div>
	        <nav class="navbar navbar-default">
	            <div class="container">
	                <div class="navbar-header" style="width: 100%;">
	                	<ul class="nav navbar-nav" style="float: left;">
	                	    <li <?php if ($this->route->getControllerName() == 'home'): ?>
	                                class="active"
	                            <?php endif ?>>
	                            <a href="<?php echo $this->url("home") ?>">ユーザ状況一覧</a>
	                        </li>
	                		<li <?php if ($this->route->getControllerName() == 'list'): ?>
									class="active"
								<?php endif ?>>
	                			<a href="<?php echo $this->url("list") ?>">KPI一覧</a>
	                		</li>
	                		<li <?php if ($this->route->getControllerName() == 'kpiregistration'): ?>
									class="active"
								<?php endif ?>>
	                			<a href="<?php echo $this->url("kpiregistration") ?>">KPI登録</a>
	                		</li>                		  
	                        
                            <li <?php if ($this->route->getControllerName() == 'importexport'): ?>
									class="active"
								<?php endif ?>>
								<a  href="<?php echo $this->url("importexport") ?>">ファイルIMPORT/EXPORT</a>
                            </li>
                            
                            <li <?php if ($this->route->getControllerName() == 'inquiry'): ?>
									class="active"
								<?php endif ?>>
								<a  href="<?php echo $this->url("inquiry") ?>">問合せ</a>
                            </li>
                            <li role="presentation" class="dropdown"> 
	                            <a style="" class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"> 
	                                	その他 <span class="caret"></span> 
                                </a> 
                                <ul class="dropdown-menu"> 
                                    <li>
                                        <a href="javascript:showversioninfo()">バージョン情報</a>
                                    </li> 
                                    <li>
                                        <a href="javascript:showterms()">利用規約</a>
                                    </li>  
                                </ul> 
                            </li>
                            <!-- <li <?php if ($this->route->getControllerName() == 'agree'): ?>
									class="active"
								<?php endif ?>>
								<a  href="<?php echo $this->url("agree") ?>">承認</a>
                            </li> -->    
                                               
	
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
	                        <li>
                                <a style="<?php echo (User::getCurrentUser()->maintenance_flag || User::getCurrentUser()->admin_flag == 1  || User::getCurrentUser()->admin_flag == 2)? "":"display: none;" ?>"
                                    href="<?php  echo (User::getCurrentUser()->maintenance_flag)?  $this->url("company"): $this->url("user") ?>">管理画面</a>
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