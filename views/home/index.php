<script type="text/javascript">
	google.charts.load('current', {'packages':['bar']});
  	google.charts.setOnLoadCallback(drawChart);
  	function drawChart() {
                
        //create data table object
		var dataTable = new google.visualization.DataTable();
		//dinh nghia cot
		dataTable.addColumn('string','<?php echo $data['year']?>年の各月');
		dataTable.addColumn('number', '目標');
		dataTable.addColumn('number', '実績');
		

		//data column
		<?php foreach ($data['goal_company'] as $key => $row): ?>
        
            <?php $value_arr[]= "['".($key + 1)."'," . $row['goal_month'] . ",". $data['mark_company'][$key]."]" ?>
        <?php endforeach ?>
        
        <?php $values = implode(",", $value_arr) ?>
        
        dataTable.addRows([<?php echo $values ?>]);

        var options = {
          chart: {
            title: '状況グラフ: <?php echo $data['year']?>年の社員の目標と実績',
            //subtitle: '社員全体の目標と実績: <?php //echo $data['year']?>年'
          }
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));
        

        chart.draw(dataTable, options);
      }
      
   $(document).ready(function() {
	    $('#dataTable').DataTable( {
	        "scrollY":        "500px",
	        "scrollCollapse": true,
	        "paging":         false,
	        "searching": 	false,
	        "bInfo": 	false,
	        "bSort": 	false
	    } );
    } );
    </script>
<body>
<div class="container" on >
    <form action="" enctype="multipart/form-data" method="POST"> 
	<div class="col-sm-8 col-xs-8" style="float: left">   
		<table  id="dataTable" class="display table table-bordered" cellspacing="0" >
			<thead class="titledt">
				<tr>
					<th style='width: 12%;text-align:center;vertical-align:middle'>USER ID</th>
					<th style='width: 25%;text-align:center;vertical-align:middle'>氏名</th>
					<!-- <th style='width: 12%;text-align:center;vertical-align:middle'>レベル</th> -->
					<th style='width: 18%;text-align:center;vertical-align:middle'>登録KPI数</th>
					<th style='width: 18%;text-align:center;vertical-align:middle'>実施中KPI数</th>
					<!-- <th style='width: 13%;text-align:center;vertical-align:middle'>確定済KPI</th> -->
					<th style='width: 15%;text-align:center;vertical-align:middle'>状況</th>
				</tr>
			</thead>
		<tbody>
			<?php foreach ($data['data'] as $key => $row): ?>
			<tr>
				<td style=' text-align:center;vertical-align:middle'><?php echo $row['user_id'] ?></td>
				<td style=' text-align:center;vertical-align:middle'><?php echo $row['name'] ?></td>
				<!-- <td style=' text-align:center;vertical-align:middle'><?php echo $row['level'] ?></td> -->
				<td style=' text-align:center;vertical-align:middle'><?php echo $row['sokpi'] ?></td>
				<td style=' text-align:center;vertical-align:middle'><?php echo $row['kpidangthuchien'] ?></td>
				<!-- <td style=' text-align:center;vertical-align:middle'><?php echo $row['kpi_accept'] ?></td> -->
				<td style=' text-align:center;vertical-align:middle'><div class="<?php if($row['sokpi'] == 0 && $row['kpidangthuchien'] == 0): echo "level level-1" ?><?php endif ?>
					<?php if($row['sokpi'] != 0): $percent=($row['thuctich'])*100/($row['sokpi']*100) ?>	
					<?php if($percent < 10): echo "level level-1"?>		
					<?php elseif ($percent < 30): echo "level level-2" ?>
					<?php elseif ($percent < 70): echo "level level-3" ?>
					<?php elseif ($percent < 100): echo "level level-4" ?>
					<?php elseif ($percent = 100): echo "level level-5" ?>	
				<?php endif ?>
				<?php endif ?>"></div>
				
				</td>  			
			</tr>
			
			<?php endforeach ?>
		</tbody>
		</table>
	</div>
   
	<div class="col-sm-4 col-xs-4" style="float: left;padding: 0;">
		<div style="height: 200px;margin-top: 6px; "  >
			<!-- 
			<textarea style="background:#E0F2F1; height: 65px;padding:5px 10px; width: 100%;line-height: 100%;border: 1px solid #ccc; font-size: 13px;" readonly ><?php echo $data['content_release'] ?></textarea>
			<textarea style="background:#E0F2F1; height: 65px;padding:5px 10px; width: 100%;line-height: 100%;border: 1px solid #ccc; font-size: 13px;" readonly ><?php echo $data['content_notification'] ?></textarea>
			<textarea style="background:#E0F2F1; height: 65px;padding:5px 10px; width: 100%;line-height: 100%;border: 1px solid #ccc; font-size: 13px;" readonly ><?php echo $data['content_history'] ?></textarea>
			-->
			<textarea style="background:#E0F2F1; height: 200px;padding:5px 10px; width: 100%;line-height: 100%;border: 1px solid #ccc; font-size: 13px;" readonly ><?php echo $data['content_view'] ?></textarea>
		</div>
		<!-- <div style="background:#E0F2F1;  height: 200px;margin-top: 6px; padding:5px 10px;  overflow:auto;"  >
			<?php
			        if ($data['success'] == true){
			            $path = "alarm". DIRECTORY_SEPARATOR .User::getCurrentUser()->company_id.".txt";
			        }
                    else{
                        $path = "alarm". DIRECTORY_SEPARATOR ."error.txt";
                    }
					$fp = @fopen($path, "r");
				
					// Kiem tr mo file
					if (!$fp) {
					    echo 'お知らせなし！';
					}
					else
					{
						 $i=0;
					    while(!feof($fp))
					    {
					    	if($i==0){
					    		 echo fgets($fp);
								echo "<ul>";
					    	}
							else{
								echo "<li>" .fgets($fp) . "</li>";
							}
					        $i++;
					    }
						echo "</ul>";
					}
				 ?>	
		</div> -->
		<?php if(User::getCurrentUser()->admin_flag == 1): ?>
    		<div style="display: inline-flex;margin-top: 5px;" >
    		    <button style="margin-right: 3px;padding: 1px 10px 2px;" type="submit" class="btn btn-default" name="submit" value="import" id="import" >インポート</button>
    		    <input  type="file" name="file_import"/>
                               
    		</div>
        <?php endif ?>
		<div >
			<!--Divs charts -->
        	<div id="columnchart_material" style="width: 420px; height: 300px;margin-top: 10px;"></div>
		</div>
	</div> 
</form>
</div>

</body>

<?php require_once __DIR__. "/../kpiresult/include.php" ?>
