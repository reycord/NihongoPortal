<style>
	.ellipsis {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
 	}
 	
 	.checked {
    	background-color:red;
    }
     	.highlight {
    	background-color:red;
    }
    .gray{
    	background-color:#E6E6E6;
    	bo
    }
    
 
input:checked {
    height: 20px;
    width: 20px;
    color: red;
    border: solid 5px;
}


</style>
<script>

	$(document).ready(function() {
	    $('#dataDetails').DataTable( {
	        "scrollY":        "450px",
	        "scrollCollapse": true,
	        "paging":         false,
	        "searching": 	false, 
	        "bInfo": 	false,   
	        "bSort": 	false   
	    } );
	    
	    //Check all/ uncheck all
	    $("#chk_all").change(function(){
	      $(".chk_select").prop('checked', $(this).prop("checked"));
	    });
	    
	    
	    $('#all').click(function() {
	    	$('#all').prop("checked", !$('#all').prop("checked"));	
			$(".chk_select").prop("checked", $('#all').prop("checked"));
	            
        });
    
	} );

</script>

<body>
	
	<form class="form-horizontal"  method="post" name="formAgree" action="<?php echo $_SERVER['REQUEST_URI'] ?>"> 
<div id="error_div" <?php if ($data['success'] == true): ?> style="display: none;" <?php endif ?> class=" alert-danger alert-dismissible col-sm-offset-3 col-sm-6" role="alert">
	<strong>エラー：</strong>
	<span><?php echo $data['message'] ?></span>
</div>

<?php if ($data['success'] == true && $data['message'] != ""): ?>
<div id="success_div"class=" alert-success alert-dismissible col-sm-offset-3 col-sm-6" role="alert">
  <strong>アラーム：</strong> <?php echo $data['message'] ?>
</div>
<?php endif ?>
		<div class="container";>    
		
		<table  class="display table table-bordered" cellspacing="0" width="100%"   id="dataDetails" name="dataDetails" >
		  <thead class="titledt">
		     <tr>
		     	<th id="all" name="0" style=" width:5%;  text-align: center; background: #64FFDA;" >選択</th>
		        <th style=" width:9%; text-align: center">カテゴリ</th>
		        <th style=" width:17%; text-align: center">KPI</th>
		        <th style=" width:11%; text-align: center">担当者</th>
		        <th style=" width:24%; text-align: center">期間</th>
		        <th style=" width:5%; text-align: center">目標</th>
		        <th style=" width:19%; text-align: center; background: #64FFDA">上司コメント</th>
		        <th style=" width:5%;  text-align: center; background: #64FFDA">優先</th>
		        <th style=" width:5%;  text-align: center; background: #64FFDA">確定</th>
		     </tr>
		   </thead>
		     <tbody>     	
		     	<?php $i=0;?>
		    	<?php foreach ($data['data'] as $key => $row): ?>
		    		<?php $i++; ?>
		    		<tr >
		    			<!-- 
		    			name="selectrow[<?php echo $i;  ?>][chk_select]"
		    			-->
		    			<td style="width:5% ; text-align:center ;">
		    				<input style='cursor:default' type="checkbox" class="btn btn-info chk_select" 
		    				name="selectrow[<?php echo $i;  ?>][chk_select]" value="<?php echo $row['kpi_id'] ?>" 
							<?php	if ($data['admin_flag']== 0) {echo 'disabled'; }?>>
							</input>
						</td>		    			
		    			<td style='width:9%; text-align:center;vertical-align:middle' bgcolor="#E6E6E6"; id ="val_cate" name ="selectrow[<?php echo $i;  ?>][val_category_kpi]" ><?php echo $row['category_kpi'] ?></td>
		    			<td style='width:17%; text-align:left;vertical-align:middle; word-break: break-all;' bgcolor="#E6E6E6"; id ="val_kpi_name" name ="nm_kpi_name"><?php echo $row['kpi_name'] ?></td>
		    			<td style='width:11%; text-align:left;vertical-align:middle' bgcolor="#E6E6E6";><?php echo $row['name'] ?></td>
		    			<td style='width:24%; text-align:left;vertical-align:middle; word-break: break-all' bgcolor="#E6E6E6" >
							<?php foreach ($row['month'] as $key => $kpimanager): ?>   					
								<?php echo substr($kpimanager['month'], -2) . '月' ?>
								<?php  if( end($row['month']) !== $kpimanager)
										{
										   echo ',';
										}
								?>	  				
							<?php endforeach ?>					
		    			</td>
		    			<td style='width:5%; text-align:center;vertical-align:middle; ' bgcolor="#E6E6E6";><?php echo $row['goal']  ?></td>
		    			<td style='width:19%; text-align:left;vertical-align:middle;'>
		    				<input style='cursor: default' type="text" class="form-control ellipsis"  name="selectrow[<?php echo $i; ?>][val_leader_comment]" value="<?php echo trim($row['comment_leader']) ?>"
		    				<?php
								if ($data['admin_flag']== 0) { 
									echo 'disabled'; 
								}
							?>></input></td>
		    				
		    			<td style='width:5%; text-align:center; vertical-align:middle;' ><input style='cursor:default' type="checkbox"  name="selectrow[<?php echo $i; ?>][chk_priority_flag]"   <?php echo $row["priority_flag"] ? 'checked="checked"' : ''; ?>
		    				<?php
								if ($data['admin_flag']== 0) { 
									echo 'disabled'; 
								}
							?>></td>
		    			<td style='width:5%; text-align:center; vertical-align:middle; ' ><input style='cursor:default' type="checkbox" name="selectrow[<?php echo $i; ?>][chk_accept_flag]"  <?php echo $row['accept_flag'] ? 'checked="checked"' : ''; ?>
		    				<?php
								if ($data['admin_flag']== 0) { 
									echo 'disabled'; 
								}
							?>></td>
		    		</tr>
				<?php endforeach ?>
		    </tbody>
		
		 </table>
		 
		<div  style="text-align: right;padding-top: 20px;padding-right: 35px; float: right">
			<button style='width: 100px;cursor: default' type="submit" class="btn btn-default" id="btn_regis" name="btnregis" value="regis" onclick="change()" 
				<?php
					if ($data['admin_flag']== 0) { 
						echo 'disabled'; 
					}
				?>>
			<?php echo _("登録"); ?>
			</button>
		</div>
		</div>
		
	</form>

</body>


