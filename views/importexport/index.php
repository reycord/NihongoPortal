
<style>

</style>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>



<script type="text/javascript">
$(function () {
    $('input[name=file_import]').change(function() {
       $("#message_div").hide();
    });
    $("#export_leader").click(function(){
       $("#message_div").hide();
    });
    $("#export_user").click(function(){
       $("#message_div").hide(); 
    });
    $('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'KPI　毎月'
        },
        subtitle: {
            text: 'Source: CubeSystem'
        },
        xAxis: {
            categories: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Rainfall (mm)'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: '内容１',
            data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]

        }, {
            name: '内容２',
            data: [83.6, 78.8, 98.5, 93.4, 106.0, 84.5, 105.0, 104.3, 91.2, 83.5, 106.6, 92.3]

        }, {
            name: '内容３',
            data: [48.9, 38.8, 39.3, 41.4, 47.0, 48.3, 59.0, 59.6, 52.4, 65.2, 59.3, 51.2]

        }, {
            name: '内容４',
            data: [42.4, 33.2, 34.5, 39.7, 52.6, 75.5, 57.4, 60.4, 47.6, 39.1, 46.8, 51.1]

        }]
    });
});
    
</script>
<body>

<div class="container"> 
<form action="" enctype="multipart/form-data" method="POST">
    <div class="container"> 
    	<div style="float: left">
    		<label style="font-weight: bold">ファイルIMPORT</label>
    		<input type="file" name="file_import" style="margin: 20px 50px 0;"/>
    		<div>
    		  <button style="margin-top: 15px;margin-left: 50px;" type="submit" class="btn btn-default" name="submit" value="import" id="import22222" >計画一括登録</button>               
    	    </div>
    	</div>
    	<div id="message_div"class="col-sm-7 col-xs-7" style="margin-top: 20px;">
                
            <?php if ($data['success'] == false && $data['message'] != "" ): ?>
            <div id="error_div"class="alert alert-danger alert-dismissible col-sm-8" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <strong>エラー： </strong> <?php echo $data['message'] ?>
            </div>
            <?php elseif ($data['success'] == false && $data['warning_message'] != ""): ?>
            <div id="warning_div"class="alert alert-warning alert-dismissible col-sm-8" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <strong>警告： </strong> <?php echo $data['warning_message'] ?>
            </div>
            <?php elseif ($data['success'] == true && $data['message'] != ""): ?>
            <div id="success_div"class="alert alert-success alert-dismissible col-sm-8" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <strong>アラーム： </strong> <?php echo $data['message'] ?>
            </div>    
            <?php endif ?>
            
        </div>
    </div>
    <div class="container"> 
    	<div style="margin-top: 40px;float: left">
    		<label style="font-weight: bold">ファイルEXPORT</label>
    		<!-- <input type="file" name="pic" ></input> -->
    		<div>
    			<button <?php echo (User::getCurrentUser()->admin_flag == 1 || User::getCurrentUser()->admin_flag == 2 || User::getCurrentUser()->isLeader() == true)? "" : "disabled" ?> style="margin-top: 20px;margin-left: 50px;" 
    			    type="submit" class="btn btn-default" name="submit" value="export_leader" id="export_leader">計画実績一括取得</button><br />		
    		    <button style="margin-top: 15px;margin-left: 50px;" type="submit" class="btn btn-default" name="submit" value="export_user" id="export_user" >個人実績取得</button>        
            
    		</div>
    					
    	</div>
	</div>
	     <!--Divs for our charts -->
        <div id="chart"></div>
 </form>


</div>

<div class="container" id="container_bk" style="min-width: 310px; height: 400px; margin: 0 auto; width: 900px"  ></div>
</body>

<?php require_once __DIR__. "/../kpiresult/include.php" ?>