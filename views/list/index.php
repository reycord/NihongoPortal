
<script>
 $(function(){
     $('#dataTable-list >tbody >tr').click(function(){
     $('#dataTable-list >tbody >tr').removeClass('info');           
     $(this).addClass('info');  
     });
 });
$(document).ready(function() {
    $('#dataTable-list').DataTable( {
        "scrollY":        "500px",
        "scrollCollapse": true,
        "paging":         false,
        "searching": 	false,
        "bInfo": 	false,
        "bSort": 	false
    } );
    $(".row-form").submit(function() {
        // DO STUFF
        var btn= $(this).find("button[type=submit]:hover").val();
        
        if (btn == 'delete'){

            // ban co chac xoa data nay khong
            if (!confirm("<?php echo getMessageById("101") ?>"))
            {
                return false;
            }
            return true;
        }

    
        if (btn == 'edit'){
            return false;
        }
        
        return true; 
    });

} );
 
</script>
<style>
	

</style>

<div class="container" id="dataTablediv">
 
  <table  id="dataTable-list" class="display table table-bordered"   style="width: 100%; border-right: none;border-top: none;" cellspacing="0">
    <thead class="titledt">
      <tr>
        <th style="width:7%; text-align:center;vertical-align:middle;
        <?php echo (User::getCurrentUser()->admin_flag == 1 || User::getCurrentUser()->admin_flag == 2 || User::getCurrentUser()->isLeader() == true)? "" : "display: none;" ?>">USER ID</th>
        <th style="width:15%; text-align:center;vertical-align:middle;
        <?php echo (User::getCurrentUser()->admin_flag == 1 || User::getCurrentUser()->admin_flag == 2 || User::getCurrentUser()->isLeader() == true)? "" : "display: none;" ?>">氏名</th>
        <th style="width:10%; text-align:center;vertical-align:middle">カテゴリ</th>
        <th style="width:6%; text-align:center;vertical-align:middle">KPI ID</th>
        <th style="width:20%; text-align:center;vertical-align:middle">KPI</th>
        <th style="width:20%; text-align:center;vertical-align:middle">期間</th>
        <th style="width:6%; text-align:center;vertical-align:middle">目標</th>
        <th style="width:6%; text-align:center;vertical-align:middle">実績</th>    
        <th style="width:12%; text-align:center;vertical-align:middle">動作</th>
        
        
      </tr>
    </thead>
    
    <tbody>
    	<?php $old_item  = ""; 
    	$background_colors = array("#E0F2F1","#80CBC4","#26A69A","#00897B",
							"#B3E5FC", "#4FC3F7",  "#03A9F4", "#0288D1",
						    "#B2EBF2", "#4DD0E1", "#00BCD4", "#00ACC1", "#0097A7",
							"#C8E6C9", "#A5D6A7", "#81C784", "#66BB6A", "#4CAF50", "#43A047", "#DCEDC8", "#C5E1A5", "#AED581", "#9CCC65", "#8BC34A", "#7CB342", "#689F38",
							"#F0F4C3", "#E6EE9C", "#DCE775", "#D4E157", "#CDDC39", "#C0CA33");
    	$i =-1;
    	?>
    	<?php if (count($data['data']) > 0): ?>
			<?php foreach ($data['data'] as $key => $u): ?>
			    <?php foreach ($u as $key => $row): ?>
			    
                <tr class ="<?php echo (isset($_GET['kpi_id']) && $row['kpi_id'] == $_GET['kpi_id'])? "info": "" ?>">
                    <td style='width:7%; text-align:center;vertical-align:middle;
                    <?php echo (User::getCurrentUser()->admin_flag == 1 || User::getCurrentUser()->admin_flag == 2 || User::getCurrentUser()->isLeader() == true)? "" : "display: none;" ?>'><?php echo $row['user_id'] ?></td>
                    <td style='width:15%; text-align:left;vertical-align:middle; word-break: break-all;
                    <?php echo (User::getCurrentUser()->admin_flag == 1 || User::getCurrentUser()->admin_flag == 2 || User::getCurrentUser()->isLeader() == true)? "" : "display: none;" ?>'><?php echo $row['name'] ?></td>
                    <td style='width:10%; text-align:center;vertical-align:middle; word-break: break-all;
                    <?php
                    //set color cho カテゴリ giong nhau cung 1 mau 
                     //echo 'background :' . $background_colors[$i] ;
                     
                       if($data['category_arr'][$row['category_kpi']] !== $old_item){
                            $i++;
                            echo 'background :' . $background_colors[$i] ;                     
                            $old_item= $data['category_arr'][$row['category_kpi']];                    
                       } else {
                         echo 'background :' . $background_colors[$i] ;
                       }
                    ?>
                    '><?php echo $data['category_arr'][$row['category_kpi']] ?></td>
                    <td style='width:6%; text-align:center;vertical-align:middle;'><?php echo $row['kpi_id'] ?></td>
                    
                    <td style='width:20%; text-align:left;vertical-align:middle; word-break: break-all;'><?php echo $row['kpi_name'] ?></td>
                    <td style='width:20%; text-align:left;vertical-align:middle; word-break: break-all' >
                        <?php foreach ($row['months'] as $key => $kpimanager): ?>                       
                            <a style="background: #E6E6E6;width: 35px;padding-left: 5px;padding-right: 5px;
                            <?php 
                                //set color cho Thang chan le
                                if ($kpimanager['month']%2 == 1){
                                    echo 'background: #BCAAA4 ';    
                                } else {
                                    echo 'background: #FF8A65 ';
                                }
                            ?>"
                             class="btn btn-default btn-sm" href="javascript:showmodal(<?php echo $kpimanager['kpi_id'] ?>, <?php echo $kpimanager['month'] ?>)"><?php echo substr($kpimanager['month'], -2). '月' ?></a>                                                                                                            
                        <?php endforeach ?>                 
                    </td>
                            
                    <td style='width:6%; text-align:center;vertical-align:middle'><?php echo $row['goal'] ?></td>
                    <td style='width:6%; text-align:center;vertical-align:middle'><?php echo (!isset($row['mark_kpi']) || $row['mark_kpi'] == "") ? "0" : $row['mark_kpi'] ?></td>
                    <td style='width:12%; text-align:center;vertical-align:middle'>
                        <form class="row-form" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" style="display: inline-flex;"> 
                            <input style="display:none;" name = "kpi_id" id="kpi_id" value="<?php echo $row['kpi_id'] ?>"/>
                            <?php $array = ($_GET['year'] == "")? array("kpi_id"=>$row['kpi_id']) : array("kpi_id"=>$row['kpi_id'],"year"=>$_GET['year']); ?>
                            <a style="width: 50px;margin-left: 2px;" class="btn btn-default" href="<?php echo $this->url("kpiregistration","index", $array) ?>">更新</a>
                            <button type="submit" class="btn btn-default" name="submit" value="delete" id="delete" style="width: 50px;margin-left: 5px;margin-right: 2px;"><?php echo _("削除"); ?></button>               
                        </form> 
                    </td>
    
                                
                </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
		<?php else: ?>
			<tr>
                <td colspan="12" style="text-align:center;vertical-align:middle;color: red">KPIがまだ登録しませんので、登録してください。</td>
            </tr>
		<?php endif ?>
			    
    </tbody>
  </table>	

</div>

<?php require_once __DIR__ . '/../kpiresult/include.php'; ?>