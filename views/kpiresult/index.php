
<div class="modal-dialog">
	<form id="result-form" action="<?php echo $this->url('kpiresult') ?>" method="POST">
	<input id="submit" type="hidden" name="submit" value="" />
	<input id="delete_id" type="hidden" name="delete_id" value="" />
	<input id="kpi_id" type="hidden" name="kpi_id" value="<?php echo $_POST['kpi_id'] ?>" />
	<input id="month" type="hidden" name="month" value="<?php echo $_POST['month'] ?>" />
    <div class="modal-content" style="" >
    	<div style="display: none" id="modal-kpi-result"><?php echo $data['markKpi']['mark_kpi'] ?></div>
      <div class="modal-header">
      	<span style='float: right; font-weight: bold; font-size: 18px'><?php echo substr($_POST['month'], -2).'月' ?></span>
        <h4 class="modal-title">KPI実績登録</h4>
        
        <div  id="message_div" style="text-align: right; padding-right: 0px;";>
        	<?php if ($data['success'] == true && $data['message'] != "" && $data['warning'] != true): ?>
				<div style="margin-top: 10px;width: 65%;padding-bottom: 0;" id="modal_success_div"
				class="alert alert-success alert-dismissible col-sm-7" role="alert">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				  <strong>アラーム： </strong> <?php echo $data['message'] ?>
				</div>
			<?php endif ?>
        	 <div id="modal_error_div" style="margin-top: 10px;width: 65%; <?php if ($data['success'] == true ||$data['result_warning'] == true || $data['mark_warning'] == true): ?>display: none;<?php endif ?>"
        	      class="alert alert-danger alert-dismissible col-sm-7" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
				<strong>エラー：</strong><span id="message"><?php echo $data['message'] ?></span> 
			</div>
			<div id="modal_warning_div" style="margin-top: 10px;width: 65%; <?php echo ($data['result_warning'] == true || $data['mark_warning'] == true)? "" : "display: none;" ?>"
                  class="alert alert-warning alert-dismissible col-sm-7" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                <strong>警告：</strong><span id="message"><?php echo $data['message'] ?></span> 
            </div>	
		     <button type="button" class="btn btn-info" <?php if (isset($data['prev_month'])): ?>
				 onclick="getdata(<?php echo $_POST['kpi_id'] ?>, <?php echo $data['prev_month'] ?>)"
			 <?php else: ?>
				 disabled
			 <?php endif ?> name="month" value="<?php echo $_POST['prev_month'] ?>">前へ</button>
		     <button type="button" class="btn btn-info" <?php if (isset($data['next_month'])): ?>
				 onclick="getdata(<?php echo $_POST['kpi_id'] ?>, <?php echo $data['next_month'] ?>)"
			 <?php else: ?>
				 disabled
			 <?php endif ?>name="month" value="<?php echo $_POST['next_month'] ?>">次へ</button>
		</div>
      </div>
      <div class="modal-body" style="height: 400px" >
        <div class="col-xs-7" style="text-align: left; padding-left: 0px;">
        	<h4 class="modal-title">社員</h4>
        	<table id="resultTable"class="table" height="100px"; style="overflow: scroll";>
        		<tr>
					<th  class=" input-sm" style="width: 100px; text-align:center;vertical-align:middle; background: #A9D0F5;">社員ID</th>
					<td style="padding:7px 5px; width: 50px ; border:none" type="text" class="form-control input-sm"><?php echo $data['dataKpi']['user_id'] ?></td>			        
				</tr>
				<tr>
					<th class="input-sm"  style="width: 100px;text-align:center;vertical-align:middle; background: #A9D0F5;">カテゴリ</th>
					<td style="padding:7px 5px;width: 100px; border:none"" type="text" class="form-control input-sm"><?php echo $data['dataKpi']['category_kpi'] ?></td>		        
				</tr>
				<tr>
					<th class="input-sm"  style="width: 100px;text-align:center;vertical-align:middle; background: #A9D0F5;">KPI</th>
					<td style="padding:7px 5px;width: 200px; border:none"" type="text" class="form-control input-sm"><?php echo $data['dataKpi']['kpi_name'] ?></td>		        
				</tr>
				<tr>
                    <th class="input-sm"  style="width: 100px;text-align:center;vertical-align:middle; background: #A9D0F5;">実施内容</th>
                    <td style="padding:7px 5px;width: 200px; border:none"" type="text" class="form-control input-sm"><?php echo $data['dataKpi']['goal_month'] ?></td>                
                </tr>
        	</table>
        	<!-- ----- -->
        	
        	<!-- ----- -->
        </div>
        <div class="col-xs-5" style="padding-right: 0px;">
        	<h4 class="modal-title">集計</h4>        	        	
   			<div id="result" style="width: 100%; text-align: right;">
   			    <div id="explain" style="background: #BBDEFB" >
                    <table >
                        <!-- ---- -->
                        <tr>
                            <th>【Total目標】</th>
                            <th>【今月目標】</th>
                        </tr>
                        <tr>
                            <td class="result-num">
                                <span class="badge" style="background: #558B2F"><?php echo !empty($data['markKpi']['goal'])? $data['markKpi']['goal'] : 0 ?></span>
                            </td>
                            <td class="result-num">
                                <span class="badge" style="background: #558B2F"><?php echo !empty($data['markMonth']['mark_month'])? $data['markMonth']['mark_month'] : 0 ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>【Total実績】</th>
                            <th>【今月実績】</th>
                        </tr>
                        <tr>
                            <td class="result-num">
                                <span class="badge" style="background: #2f558b"><?php echo !empty($data['markKpi']['mark_kpi'])? $data['markKpi']['mark_kpi'] : 0 ?></span>
                            </td>
                            <td class="result-num">
                                <span class="badge" style="background: #2f558b"><?php echo !empty($data['markMonth']['mark'])? $data['markMonth']['mark'] : 0 ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>【Total残数】</th>
                            <th>【今月残数】</th>
                        </tr>
                        <tr>
                            <td class="result-num">
                                <span class="badge" style="background: 
                                    <?php 
                                        if  ($data['markKpi']['mark_kpi']-$data['markKpi']['goal'] < 0) {
                                            echo '#EF5350';//do
                                        } else {
                                            echo '#558B2F';//xanh
                                        }
                                    ?>"><?php echo $data['markKpi']['mark_kpi']-$data['markKpi']['goal'] ?></span></td>
                            <td class="result-num">
                                <span class="badge" style="background: 
                                    <?php 
                                        if  ($data['markMonth']['mark']-$data['markMonth']['mark_month'] < 0) {
                                            echo '#EF5350';//do
                                        } else {
                                            echo '#558B2F';//xanh
                                        }
                                    ?>"><?php echo $data['markMonth']['mark']-$data['markMonth']['mark_month'] ?></span></td>
                        </tr>
                                        
                    </table>
                </div>
   			    <div>
                        <span class="badge" style="background: #558B2F"><?php echo !empty($data['markKpi']['goal'])? $data['markKpi']['goal'] : 0 ?></span> 
                        <span class="badge" style="background: #2f558b"><?php echo !empty($data['markKpi']['mark_kpi'])? $data['markKpi']['mark_kpi'] : 0 ?></span> 
                        <span class="badge" style="background: 
                        <?php 
                            if  ($data['markKpi']['mark_kpi']-$data['markKpi']['goal'] < 0) {
                                echo '#EF5350';//do
                            } else {
                                echo '#558B2F';//xanh
                            }
                        ?>"><?php echo $data['markKpi']['mark_kpi']-$data['markKpi']['goal'] ?></span>                  
                </div>
   			    
   			    <!-- ----- -->
   			    
   				<div style="margin-top: 5px;">
       					<span class="badge" style="background: #558B2F"><?php echo !empty($data['markMonth']['mark_month'])? $data['markMonth']['mark_month'] : 0 ?></span>	
       					<span class="badge" style="background: #2f558b"><?php echo !empty($data['markMonth']['mark'])? $data['markMonth']['mark'] : 0 ?></span>	
       					<span class="badge" style="background: 
       					<?php 
       						if  ($data['markMonth']['mark']-$data['markMonth']['mark_month'] < 0) {
       							echo '#EF5350';//do
       						} else {
       							echo '#558B2F';//xanh
       						}
       					?>"><?php echo $data['markMonth']['mark']-$data['markMonth']['mark_month'] ?></span>   				
       			</div>
			    		    	    
   			</div>
   			<div style="margin-top: 10px;" class="<?php  $percent=$data['data']['goal']== 0 ? 0 : ($data['data']['mark_kpi']*100)/$data['data']['goal'] ?>	
							<?php if($percent <= 10): echo "level level-1"?>		
							<?php elseif ($percent <= 30): echo "level level-2" ?>
							<?php elseif ($percent <= 50): echo "level level-3" ?>
							<?php elseif ($percent <= 90): echo "level level-4" ?>
							<?php elseif ($percent <= 100): echo "level level-5" ?>
							<?php elseif ($percent > 100): echo "level level-5" ?>		
							<?php endif ?>">	
			    		
			</div>		     
   		</div>
   			
       <div class="col-xs-12"> 
       		<h4 class="modal-title">KPI実績登録</h4>
			<table id="modalTable" class=" table table-bordered" >
				<thead class="titledt">
					<tr　>
						<th style='width:10%; text-align:center;vertical-align:middle'>削除</th>
						<!-- <th style='width:10%; text-align:center;vertical-align:middle'>No</th> -->
						<th style='width:10%; text-align:center;vertical-align:middle'>日</th>
						<th style='width:55%; text-align:center;vertical-align:middle'>内容</th>
						<th style='width:15%; text-align:center;vertical-align:middle'>回数</th>
						<th style="display:none"></th>
					</tr>
				</thead>  
				<tbody id="data_body" >
					<?php foreach ($data['resultKpis'] as $key => $row): ?>
						 <tr style="height:15px" >
						 	
							<td style="text-align:center; vertical-align: middle ;padding-top: 0px;padding-bottom: 0px ; " >
								<!-- <input type="checkbox" name="delete_ids[]" value="<?php echo $row['id'] ?>" id ="chk_select"  class="chk_select"></input>
								 -->
								 <a class="btn btn-xs" href="javascript:delete_id(<?php echo $row['id'] ?>)" id="delete">
								 	<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
								 </a>
							</td>
							<!-- <td style='text-align:center;vertical-align:middle;padding-top: 0px;padding-bottom: 0px;'><?php echo $key + 1 ?></td> -->
							<td style='text-align:center;vertical-align:middle; padding-top: 0px;padding-bottom: 0px;'><?php echo $row['day'] ?></td>
							<td style='text-align:left;vertical-align:middle; padding-left: 15px;padding-top: 0px;padding-bottom: 0px;'><?php echo $row['result'] ?></td>
							<td style='text-align:center;vertical-align:middle;padding-top: 0px;padding-bottom: 0px;'><?php echo $row['mark'] ?></td>
							<td style="display:none"><?php echo $row['id'] ?></td>
						</tr>	  
					<?php endforeach ?>	
						<tr>
							<td></td>
							
							<td style='text-align:center;vertical-align:middle'>
								<select name="day" id = "create-day"> 
                                    <?php for ($day=1; $day <= $data['maxDayInMonth']; $day++) { ?>
                                        <option value="<?php echo $day ?>" <?php if ($day == $data['create_day']): ?> selected<?php endif ?>>
                                             <?php echo $day ?></option>
                                    <?php }  ?>                         
                                </select>
							</td>
							<td style='text-align:center;vertical-align:middle' class="<?php if($data['success'] == false && $data['result_warning'] == true): echo  "has-warning" ?>
						                                                             <?php elseif($data['success'] == false && $data['result_error'] == true): echo  "has-error" ?>
							                                                              <?php endif ?>">
								<input maxlength="30" type="text" class="form-control" id="create-result" name="result" value="<?php echo $data['create_result'] ?>"></input>
							</td>
							<td style='text-align:center;vertical-align:middle' class="<?php if($data['success'] == false && $data['mark_warning'] == true): echo  "has-warning" ?>
                                                                                     <?php elseif($data['success'] == false && $data['mark_error'] == true): echo  "has-error" ?>
                                                                                          <?php endif ?>">
								<input type="text" onkeypress="return isNumberKey(event)"　maxlength="5" type="text" class="form-control" id="create-mark" name="mark" value="<?php echo $data['create_mark'] ?>"></input>
							</td>
							
						</tr>								    			
				</tbody>     			
			</table> 	

			
       	</div>      		    	 
     </div>
      

      <div class="modal-footer" style="border-top: 0px;">
        <a class="btn btn-default" href="javascript:create()" id="save">登録</a>
      </div> 
    </div><!-- /.modal-content -->
    </form>
</div><!-- /.modal-dialog -->

<script>
    $(function(){
        $('#result-form input[name=result]').change(function() {
           $("#message_div").hide(); 
           $('#result-form td.has-warning:has(input[name=result])').removeClass('has-warning');
            
        });
        $('#result-form input[name=mark]').change(function() {
           $("#message_div").hide(); 
           $('#result-form td.has-warning:has(input[name=mark])').removeClass('has-warning');
           if (/\d+/m.test($(this).val()) == false){
               $(this).val("");
               return;
           }
        });
    });
</script>