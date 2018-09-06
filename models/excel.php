<?php
require_once "controllers/kpi.php";
define("TemplateFileType", 'Excel2007');
define("TemplateFileName_leader", 'resources/leader_export_KPI.xlsx');
define("TemplateFileName_user", 'resources/currentUser_export_KPI.xlsx');

// Cell leader export
// define("MARK_COLUMN_CHARS", "I,K,M,O,Q,S,U,W,Y,AA,AC,AE");
// define("RESULT_COLUMN_CHARS", "H,J,L,N,P,R,T,V,X,Z,AB,AD");

define("MARK_COLUMN_CHARS", "H,J,L,N,P,R,T,V,X,Z,AB,AD");
define("RESULT_COLUMN_CHARS", "G,I,K,M,O,Q,S,U,W,Y,AA,AC");
define("COL_NO", "B");
define("COL_CATEGORY", "C");
define("COL_KPI_NAME", "D");
define("COL_USER_ID", "E");
define("POINT_KPI", "F");
define("COL_MARK", "G");
define("START_ROW", 5);

// user export
define("COL_CATEGORY_USER", "B");
define("COL_KPI_NAME_USER", "D");
define("COL_MONTHS_USER", "F");
define("COL_GOAL_USER", "H");
define("COL_MARK_USER", "I");
define("COL_RESULT_USER", "J");
define("COL_COMMENT", "K");
define("START_ROW_USER", 9);

require_once 'classes/phpexcel/PHPExcel/IOFactory.php';
require_once 'classes/phpexcel/PHPExcel.php';

class Excel
{
    /**
     * @var PHPExcel $objPHPExcel
     */
    public $objPHPExcel;

    //add to the parent constructor
    public function __construct($fileName = TemplateFileName_user, $fileType = TemplateFileType) {
        
        $objReader = PHPExcel_IOFactory::createReader($fileType);
        $this->objPHPExcel = $objReader->load($fileName);
    }
    
    // ghi file
    public function writeFile(){ 
        $writer = PHPExcel_IOFactory::createWriter($this->objPHPExcel, TemplateFileType);
        $writer->save('php://output');
    }
    
    // import file
    public function import()
    {
        // lay danh sach category
        $category_arr = CategoryKPI::getListCategoryById();
        // lay danh sach cell cua 12 thang
        $mark_col_arr = explode(",", MARK_COLUMN_CHARS);
        $result_col_arr = explode(",", RESULT_COLUMN_CHARS);
        
        // import tu cell so 0 cua file excel
        $sheet = $this->objPHPExcel->getSheet(0);
        $data_arr = array();
        $current_row = START_ROW;
        $year = (int)substr($sheet->getCell("C1")->getCalculatedValue(),0,4);
        $error_kpi = 0;
        $error_user = 0;
        $success_kpi = 0;
        $user_arr = array();
        
        // list user    
        if(User::getCurrentUser()->admin_flag == 1){
            $users = array_map(function($e){return $e['user_id'];}, User::getUserIDListByAdmin(User::getCurrentUser()->company_id));
        }
		elseif(User::getCurrentUser()->admin_flag == 2){
			$users = array_map(function($e){return $e['user_id'];}, User::getUserIDListByManager(User::getCurrentUser()->company_id, User::getCurrentUser()->dep_id));
        }
        elseif (User::getCurrentUser()->isLeader() == true) {
            $users =  array_map(function($e){return $e->user_id;}, User::getCurrentUser()->getListUserByLeader());
        }
        else {
            $users = array(User::getCurrentUser()->user_id);   
        }
         
        while (1) {
            $category_kpi = $sheet->getCell(COL_CATEGORY.$current_row)->getCalculatedValue();
            $kpi_name = $sheet->getCell(COL_KPI_NAME.$current_row)->getCalculatedValue();
            $user_id = $sheet->getCell(COL_USER_ID.$current_row)->getCalculatedValue();
            $data_user = Data::getInformationUser(User::getCurrentUser()->company_id, $user_id);
            $dep_id = $data_user['dep_id'];
            // kiem tra xem neu category, kpi_name, user_id ko co thi dung doc file
            if ($category_kpi == "" && $kpi_name == "" && $user_id == "" || $year == 0) {
                break;  
            }
            // user import phai la user hien tai hoac user duoc quan ly
            if (!in_array($user_id, $users)){
                $current_row += 2; 
                $error_kpi ++;
                $error_user ++;
                continue;
            }

            // kiem tra neu 1 trong category, kpi_name, user_id la null thi error_kpi import + 1
            elseif ($category_kpi == "" || $kpi_name == "" || $user_id == "") {
                $error_kpi ++;
                $current_row += 2; 
            }
            else {
                $months = array();
                $start_date = "";
                $end_date ="";
				$goal = 0;
                $month_array = array();
                $goal_month_array = array();
                        $goal_markMonth_array = array();
                        
                foreach ($mark_col_arr as $month_index => $mark_col) {
                    $days = array();
                    
                    // lay result, mark tu 1 month
                    $goal_month = $sheet->getCell($result_col_arr[$month_index].$current_row)->getCalculatedValue();            
                    $goal_markMonth = $sheet->getCell($mark_col.$current_row)->getCalculatedValue();                
                    $result = $sheet->getCell($result_col_arr[$month_index].($current_row+1))->getCalculatedValue();
                    $mark_month = $sheet->getCell($mark_col.($current_row +1))->getCalculatedValue();
                    
                    
                    if ($goal_month != "" && $goal_markMonth != "") {  
                        // lay ket qua tung ngay
                        $lines = explode("\n", $result);
                        foreach ($lines as $key_line => $line) {
                            
                            // kiem tra format ket qua tung ngay co hop le hay ko
                            if (preg_match("/^(0?[1-9]|[1-2][0-9]|3[0-1])日:(.+)$/", $line, $matches)) {
                                
                                // kiem tra ket qua, mark tung ngay   
                                if (preg_match("/^(.*?)(\\[[0-9]+\\])?$/", trim($matches[2], " "), $ms)) {
                                    //  luu day, result, mark cua tung ngay
                                    $days[] = array('day' => $matches[1],
                                            'result_day' => $ms[1],
                                            'mark_day' => $ms[2]);
                                    
                                }

                                          
                                    
                            }
                        }
                        // luu ket qua cua tung thang
                        $start_month = User::getCurrentUser()->start_month;
						
						$year_fixed = (int)$year;
                        $month_index_fixed = ($month_index + $start_month) > 12? ($month_index + $start_month - 12): ($month_index + $start_month);
						if($month_index_fixed < $start_month) {
							$year_fixed += 1;
						}
						
                        $months[] = array(
                                "month" => $year_fixed.sprintf("%'.02d", $month_index_fixed),
                                "goal_month" => $goal_month,
                                "goal_markMonth" => $goal_markMonth,
                                "result" => $result,
                                "mark_month" => $mark_month,
                                "days" => $days
                        );
                        
                        // if goal, mark_goal != null ---> insert
                        
                        if($goal_month != "" || $goal_markMonth != "" || $result != "" || $mark_month != ""){
                            if($start_date == ""){
                                // set start day la thang dau tien co data
                                $start_date = $year_fixed."-".sprintf("%'.02d", $month_index_fixed)."-"."01";   
                            }  
                            // set end date la thang cuoi cung co data
                            $end_date = $year_fixed."-".sprintf("%'.02d", $month_index_fixed)."-".cal_days_in_month(CAL_GREGORIAN, $month_index_fixed, $year_fixed);   
                        }
                        
                        // set muc tieu cua tung thang
                        $goal_month_array[] = $goal_month;
                        $goal_markMonth_array[] = $goal_markMonth;
                        
                        $goal += $goal_markMonth;
                        
                    }else{
                        $goal_month_array[] = "";
                        $goal_markMonth_array[] = "";
                    }
                }
                
                // data_array luu ket qua kpi
                if (count($months) > 0){ 
                    $month_array = array_map(function($e){return $e['month'];}, $months);
                }

                $category_id = array_search($category_kpi, $category_arr);
                $data_arr[] = array(
                    'category_kpi' => $category_id,
                    'kpi_name' => $kpi_name,
                    'user_id' => $user_id,
                    'goal' => $goal,
                    'months' => $months,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'month_array' => $month_array,
                    'goal_month_array' => $goal_month_array,
                    'goal_markMonth_array' => $goal_markMonth_array,
                    'dep_id' =>$dep_id
                );
                // doc kpi dopng tiep theo
                $current_row += 2; 
                $success_kpi ++;
            }
        }
		
        if ($data_arr){
            // co data import thanh cong 
            foreach ($data_arr as $key_data => $kpi) {
                Data::insertKpiManager(User::getCurrentUser()->company_id, $kpi['user_id'], $kpi['goal'],
                                     $kpi['start_date'], $kpi['end_date'], $kpi['category_kpi'], $kpi['kpi_name'],
                                     $comment_user, $kpi['month_array'], $kpi['goal_month_array'], $kpi['goal_markMonth_array'],$kpi['dep_id']);
                // lay kpi_id vua moi insert
                $record = Database::currentDb()->getRecord("SELECT last_value FROM kpi_base_t_kpi_id_seq;");
                $last_kpi_id = $record['last_value'];
                
                foreach ($kpi['months'] as $key => $data_month) {
                    foreach ($data_month['days'] as $key => $data_day) {
                            Data::insertResultKpi($last_kpi_id, $data_month['month'], $data_day['day'], $data_day['result_day'], substr($data_day['mark_day'], 1,strlen($data_day['mark_day'])-2));
                        
                        
                    } 
                }
                
            }  
            $message = getMessageById("004");
        }
        else{
            // ko co data import ko thanh cong
            $message = getMessageById("203");
        }
        if($year == 0 ){
            $message = getMessageById("105");
        }
        $result_import = array("error_kpi" => $error_kpi,
                               "error_user" => $error_user,
                               "success_kpi" => $success_kpi,
                               "message" => $message
                        );
        return $result_import;
        
    }
    public function export_leader($company_id, $user_id, $year, $search, $start_month){
        
        
       if(User::getCurrentUser()->admin_flag == 1){
            $users = User::getUserIDListByAdmin(User::getCurrentUser()->company_id);
            $data_arr = array();
            foreach ($users as $key => $u) {
                $data_arr[] = Data::getAllKpiOfUserInYear($u['user_id'], User::getCurrentUser()->company_id, $year, $search, $start_month);          
            }
        }
		elseif(User::getCurrentUser()->admin_flag == 2){
            $users = User::getUserIDListByManager(User::getCurrentUser()->company_id, User::getCurrentUser()->dep_id);
            $data_arr = array();
            foreach ($users as $key => $u) {
                $data_arr[] = Data::getAllKpiOfUserInYear($u['user_id'], User::getCurrentUser()->company_id, $year, $search, $start_month);          
            }
        }
        elseif (User::getCurrentUser()->isLeader() == true) {
            $users = User::getCurrentUser()->getListUserByLeader();
            $data_arr = array();
            foreach ($users as $key => $u) {
                $data_arr[] = Data::getAllKpiOfUserInYear($u->user_id, User::getCurrentUser()->company_id, $year, $search, $start_month);          
            }
        }
        else {
            $data_arr[] = Data::getAllKpiOfUserInYear(User::getCurrentUser()->user_id, User::getCurrentUser()->company_id, $year, $search, $start_month);
            
        }
        
        foreach ($data_arr as $k => $u) {
            foreach ($u as $key => $row) {
                $data_kpi[] = $row;
            }
            
        }
        
        $sheet = $this->objPHPExcel->getSheet(0);   
        //$data_kpi = Data::exportLeader($company_id, $year, $search);
        $data_user = Data::getInformationUser($company_id, $user_id);
        $category_arr = CategoryKPI::getListCategoryById();
        $sheet->insertNewRowBefore(7,sizeof($data_kpi) * 2 - 2);
        
        $mark_col_arr = explode(",", MARK_COLUMN_CHARS);
        $result_col_arr = explode(",", RESULT_COLUMN_CHARS);
        foreach ($result_col_arr as $key_date => $cell_date) {
        	$key_date_fixed = $key_date + $start_month;
			$year_fixed = $year;
			if($key_date_fixed > 12) {
				$key_date_fixed -= 12;
				$year_fixed = $year + 1;
			}
			$date = $year_fixed."年".($key_date_fixed)."月";
            // $date = $year."年".($key_date + 1)."月";
			$sheet->setCellValue($cell_date . 3, $date); 
        }
        foreach ($data_kpi as $kpi_index => $kpi) {
            
            // merge cell
            $sheet->mergeCells(COL_NO.($kpi_index * 2 + START_ROW).":B".($kpi_index * 2 + START_ROW + 1));
            $sheet->mergeCells(COL_CATEGORY.($kpi_index * 2 + START_ROW).":C".($kpi_index * 2 + START_ROW + 1));
            $sheet->mergeCells(COL_KPI_NAME.($kpi_index * 2 + START_ROW).":D".($kpi_index * 2 + START_ROW + 1));
            $sheet->mergeCells(COL_USER_ID.($kpi_index * 2 + START_ROW).":E".($kpi_index * 2 + START_ROW + 1));
            
            // luu category, kpi_name, user id, 
            $sheet->setCellValue("F" . ($kpi_index * 2 + START_ROW), "計画");
            $sheet->setCellValue("F" . ($kpi_index * 2 + 1 + START_ROW), "実績");
            $sheet->setCellValue("B" . ($kpi_index * 2 + START_ROW), $kpi_index + 1);
            $sheet->setCellValue("A" . 1, "KPI状況:");
			
			if(date("m") < $start_month) {
				$sheet->setCellValue("C" . 1, (date("Y") - 1)."年");
			} else {
				$sheet->setCellValue("C" . 1, date("Y")."年");
			}
			
            $sheet->setCellValue(COL_CATEGORY . ($kpi_index * 2 + START_ROW), $category_arr[$kpi['category_kpi']]);
            $sheet->setCellValue(COL_KPI_NAME . ($kpi_index * 2 + START_ROW), $kpi['kpi_name']);
            $sheet->setCellValue(COL_USER_ID .  ($kpi_index * 2 + START_ROW), $kpi['user_id']);
            // $sheet->setCellValue(POINT_KPI .  ($kpi_index * 2 + START_ROW), $kpi['point_kpi']);
            
            // luu ket qua tung thang
            foreach ($kpi['months'] as $index => $kpimanager) {
                $month = (int)substr($kpimanager['month'], -2);
				$month -= $start_month;
				if($month < 0) {
					$month += 12;
				}
				
				$result_col = $result_col_arr[$month];
				$mark_col = $mark_col_arr[$month];
				
                // $mark_col = $mark_col_arr[$month -1];
                // $result_col = $result_col_arr[$month -1];
                
                // luu muc tieu, diem cua tung thang
                $sheet->setCellValue($result_col .  ($kpi_index * 2 + START_ROW), $kpimanager['goal_month']);
                $sheet->setCellValue($mark_col .  ($kpi_index * 2 + START_ROW), $kpimanager['mark_month']);
                $sheet->setCellValue($mark_col .  ($kpi_index * 2 + 1 + START_ROW), $kpimanager['mark']);
                
                // luu ket qua tung ngay
                $string_arr = array();
                foreach ($kpimanager['days'] as $key => $day) {
                    $day_str = $day['result']."[" .(int) $day['mark_day'] ."]";
                    if (isset($string_arr[$day['day']])) {
                        $string_arr[$day['day']][] = $day_str;

                    } else {
                        $string_arr[$day['day']] = array($day_str);

                    }
                    
                }
                
                
                $string_arr3 = array();
                foreach ($string_arr as $key => $ele) {
                    if($key != ""){
                        // $string_arr3[] = $key . "日: " . implode(", ", $ele);   
                         foreach ($ele as $k1 => $v) {
                            $string_arr3[] = $key . "日: " . $v;
                        }
                    }
                }
                
                $sheet->setCellValue($result_col .  ($kpi_index * 2 + 1 + START_ROW), implode("\n", $string_arr3));
            } 
                
        }
        
        
    }
        public function export_user($company_id, $user_id, $year, $search, $start_month){
                
                // ghi vao cell thu nhat
                $sheet = $this->objPHPExcel->getSheet(0);   
                
                // lay data 1 nguoi
                $data_kpi = Data::getAllKpiOfUserInYear($user_id, $company_id, $year, $search, $start_month);
                $data_user = Data::getInformationUser($company_id, $user_id);
                $category_arr = CategoryKPI::getListCategoryById();
                $current_row = START_ROW_USER;
                $sheet->insertNewRowBefore(10,sizeof($data_kpi)-1);
                
                // ghi thong tin user export
                $sheet->setCellValue("C" . 2, $data_user['user_id']);
                $sheet->setCellValue("C" . 1, $data_user['company_id']);
                $sheet->setCellValue("C" . 3, $data_user['level']);
                $sheet->setCellValue("C" . 4, date("Y-m-d"));
                
                foreach ($data_kpi as $kpi2 => $kpi) {
                    $goal = ($kpi['goal'] == "")? 0: $kpi['goal'];
                    $mark_kpi = ($kpi['mark_kpi'] == "")? 0: $kpi['mark_kpi'];

                    $sheet->mergeCells("B".($kpi2 + $current_row).":C".($kpi2 + $current_row));
                    $sheet->mergeCells("D".($kpi2 + $current_row).":E".($kpi2 + $current_row));
                    $sheet->mergeCells("F".($kpi2 + $current_row).":G".($kpi2 + $current_row));
                    // ghi thong tin category, kpi_name, goal, mark kpi cua tung kpi
                    $sheet->setCellValue(COL_CATEGORY_USER . ($kpi2 + $current_row), $category_arr[$kpi['category_kpi']]);
                    $sheet->setCellValue(COL_KPI_NAME_USER . ($kpi2 + $current_row), $kpi['kpi_name']);
                    $sheet->setCellValue(COL_MONTHS_USER . ($kpi2 + $current_row), implode(", ", array_map(function($n) {return (int)substr($n['month'],-2)."月";}, $kpi['months']) ));
                    $sheet->setCellValue(COL_GOAL_USER . ($kpi2 + $current_row), $goal);
                    $sheet->setCellValue(COL_MARK_USER . ($kpi2 + $current_row), $mark_kpi);
                    if(date($kpi['start_date']) > date("Y-m-d") && $mark_kpi > 0){
                        // lam truoc ngay bat dau
                        $result = getMessageById("007");
                    }
                    if(date($kpi['start_date']) > date("Y-m-d") && $mark_kpi == 0){
                        // chua bat dau
                        $result = getMessageById("008");
                    }
                    // qua han
                    if(date($kpi['end_date']) < date("Y-m-d") && $mark_kpi <  $goal){
                        $result = getMessageById("009");
                    }
                    // trong thoi gian thuc hien
                    if(date($kpi['start_date']) <= date("Y-m-d") && date($kpi['end_date']) >= date("Y-m-d")){
                        
                        $percent = ($goal == 0 || $goal == "" || $mark_kpi == "" || $mark_kpi == 0 ) ? 0: ($mark_kpi*100)/$goal;  
                         if($percent == 0): $result = getMessageById("008");  // chua lam
                         elseif($percent > 0 && $percent <30): $result = getMessageById("010");    
                         elseif ($percent < 70): $result = getMessageById("011");
                         elseif ($percent < 100): $result = getMessageById("012");
                         elseif ($percent == 100): $result = getMessageById("013");
                         endif; 
                    }
                    
                    $sheet->setCellValue(COL_RESULT_USER . ($kpi2 + $current_row), $result);
            
                        
                }
                
                
            }
}

?>