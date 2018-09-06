<?php

require_once "controllers/kpi.php";

class KPIRegistrationController extends KPIController
{
    
    protected function index()
    {
        User::getCurrentUser()->getListUserByLeader();
		
        // lay ten danh sach category
    	$this->setData('categoryKPList', CategoryKPI::getCategoryKPList());
        
        // lay danh sach category theo kye, value
        $this->setData('category_arr', CategoryKPI::getListCategoryById());  
        
        // lay danh sach point      
		// $this->setData('pointList', CategoryKPI::getPointList());
        
        // lay user_id cuar user hien tai
    	$user_id = User::getCurrentUser()->user_id;
        $user = User::getCurrentUser();
		
		// lay thang bat dau theo ma cong ty
		$this->setData("startAtMonth", User::getCurrentUser()->start_month);
        
        // lay thong tin user dang nhap
		$this->setData('data_info', Data::getInformationUser(User::getCurrentUser()->company_id, $user_id));
        
        // kiem tra user dang nhap co pahi la admin hay ko
		$this->setData('admin_flag', $this->data['data_info']['admin_flag']);
        
        // lay danh sach nhan vien cap duoi theo admin va leader

        if (User::getCurrentUser()->admin_flag ==  1){
            $users = User::getUserIDListByAdmin(User::getCurrentUser()->company_id);
            foreach ($users as $k => $u) {
                $user_arr[$u['user_id']] = $u['name']; 
            }
        }
		elseif(User::getCurrentUser()->admin_flag == 2){
			//lay admin_flag cua leader cua user hien tai de kiem tra co phai la truong phong hay khong
			$leaderId = User::getCurrentUser()->leader_id; 
			$tmpUser = User::getById($leaderId,User::getCurrentUser()->company_id);
			$flagleader = $tmpUser->admin_flag;
			if (($flagleader==1)||($leaderId==User::getCurrentUser()->user_id)||($leaderId==null)) {
				$users = User::getUserIDListByManager(User::getCurrentUser()->company_id, User::getCurrentUser()->dep_id);
				foreach ($users as $k => $u) {
					$user_arr[$u['user_id']] = $u['name']; 
				}
			} else {
				$users = User::getCurrentUser()->getListUserByLeader();
				foreach ($users as $k => $u) {
                $user_arr[$u->user_id] = $u->name; 
            }
			}
        }
		elseif(User::getCurrentUser()->isLeader() == true){
            $users = User::getCurrentUser()->getListUserByLeader();
            foreach ($users as $k => $u) {
                $user_arr[$u->user_id] = $u->name; 
            }
        }
      
        $this->setData('user_arr', $user_arr ); 
        
        // lay user_id tu duong link khi edit tu man hinh list
		if (isset($_GET['user_id']) && $_GET['user_id'] != $user_id) {
			
			if (User::getCurrentUser()->admin_flag == 1 || User::getCurrentUser()->admin_flag == 2 || User::getCurrentUser()->isLeader() == true) {
				$user_id = $_GET['user_id'];
                $user = User::getByID($user_id, User::getCurrentUser()->company_id);
			}
			else {
				$this->setData('success', false);
				// message: ko co quyen xem cua ng khac
				$this->setData('message', getMessageById("109"));
				$this->render();
				exit();
			}
            $user_list = array_map(function($e){return $e['user_id'];}, User::getUserIDListByAdmin(User::getCurrentUser()->company_id));
            if (!in_array($user_id, $user_list)) {
                $this->setData('success', false);
                // message: user ko ton tai
                $this->setData('message', getMessageById("206"));
                $this->render();
                exit();
            }   
                        
		}
        
         $this->setData("user", $user);
         $this->setData('goal_markMonth_error', false);
         
        if (isset($_GET['kpi_id'])) {
            $data_arr = array();
            if(User::getCurrentUser()->admin_flag == 1){
                $users = User::getUserIDListByAdmin(User::getCurrentUser()->company_id);
                foreach ($users as $key => $u) {
                    $getAllKpiOfUserInYear = Data::getAllKpiOfUserInYear($u['user_id'], User::getCurrentUser()->company_id, $this->getData('year'), $this ->getData('search'), User::getCurrentUser()->start_month);
                    foreach ($getAllKpiOfUserInYear as $k => $value) {
                        $data_arr[] = $value;
                    }          
                }
            }
			elseif(User::getCurrentUser()->admin_flag == 2){
                $users = User::getUserIDListByManager(User::getCurrentUser()->company_id, User::getCurrentUser()->dep_id);
                foreach ($users as $key => $u) {
                    $getAllKpiOfUserInYear = Data::getAllKpiOfUserInYear($u['user_id'], User::getCurrentUser()->company_id, $this->getData('year'), $this ->getData('search'), User::getCurrentUser()->start_month);
                    foreach ($getAllKpiOfUserInYear as $k => $value) {
                        $data_arr[] = $value;
                    }          
                }
            }
            elseif (User::getCurrentUser()->isLeader() == true) {
                $users = User::getCurrentUser()->getListUserByLeader();
                foreach ($users as $key => $u) {
                    $getAllKpiOfUserInYear = Data::getAllKpiOfUserInYear($u->user_id, User::getCurrentUser()->company_id, $this->getData('year'), $this ->getData('search'), User::getCurrentUser()->start_month);
                    foreach ($getAllKpiOfUserInYear as $k => $value) {
                        $data_arr[] = $value;
                    }             
                }
            }
            else {
                $getAllKpiOfUserInYear = Data::getAllKpiOfUserInYear(User::getCurrentUser()->user_id, User::getCurrentUser()->company_id, $this->getData('year'), $this ->getData('search'), User::getCurrentUser()->start_month);
                foreach ($getAllKpiOfUserInYear as $k => $value) {
                    $data_arr[] = $value;
                }
                
                
            }
            $kpi_id_arr = array_map(function($e){return $e['kpi_id'];}, $data_arr);
            if (!in_array($_GET['kpi_id'], $kpi_id_arr)){
                $this->setData('success', false);
                // message: user ko ton tai
                $this->setData('message', getMessageById("109"));
                $this->render();
                exit();
            }
            $this->setData('kpi_id', $_GET['kpi_id']);
            // lay data tu bang kpi_base_t
            $this->setData('data_kpi_base', Data::getDataKpiByKpiId(User::getCurrentUser()->company_id, $this->getData('year'), $this->data['kpi_id'], User::getCurrentUser()->start_month));
            
            // lay data tu bang kpi_manager_t
            $this->setData('data_kpi_manager', Data::getKpiManagerByKpiId($this->getData('year'),$this->data['kpi_id'], User::getCurrentUser()->start_month));
            
            //lay thong tin user cua kpi can edit
            $this->setData('user_id_kpi', $this->data['data_kpi_base'][user_id]);
            $this->setData('name_kpi', $this->data['data_kpi_base'][name]);
            // set data cho hang muc cua kpi can sua
            $this->setData('category_kpi', $this->data['data_kpi_base'][category_kpi]);
            $this->setData('kpi_name', $this->data['data_kpi_base'][kpi_name]);
            $this->setData('goal', $this->data['data_kpi_base'][goal]);
            // $this->setData('point_kpi', $this->data['data_kpi_base'][point_kpi]);
            $this->setData('comment_user', $this->data['data_kpi_base'][comment_user]);
            
			// set array months da dang ky
			$month_arr = array_map(function($e){return substr($e['month'], -2);}, $this->data['data_kpi_manager']);
            $this->setData('months', implode("-", $month_arr));
            
            $goal_month = array_map(function($e){return $e['goal_month'];}, $this->data['data_kpi_manager']);
            $goal_markMonth = array_map(function($e){return $e['mark_month'];}, $this->data['data_kpi_manager']);
           // set array muc tieu, diem cua 12 thang
            for ($i=1; $i <= 12 ; $i++) {
                 $search = array_search(sprintf("%'.02d", $i), $month_arr);
                 if($search === false){
                     $goal_month_arr[]="";
                     $goal_markMonth_arr[]="";
                 }else{
                     $goal_month_arr[]=$goal_month[$search];
                     $goal_markMonth_arr[]=$goal_markMonth[$search];
                 }
            }
            $this->setData('goal_month', $goal_month_arr);
            $this->setData('goal_markMonth', $goal_markMonth_arr);
            $user = User::getByID($this->data['user_id_kpi'], User::getCurrentUser()->company_id);
        }
		
		// check user_id
		
		
        $this->setData('info', Data::getInformationUser($this->data['user']->company_id, $this->data['user']->user_id));
        $this->setData('info_leader_user', Data::getInformationUser(User::getCurrentUser()->company_id, $this->data['info']['leader_id']));
		
    	if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submit'] =='save' || $_POST['submit'] =='update' ) {
			// kiem tra field  category_kpi bang rong => tra ve loi   
			if ($_POST['category_kpi'] == "") {
				$this->setData('success', false);
				$this->setData('category_kpi_error', true);
                $error_list[] = ' カテゴリ';
			}
          
			$this->setData('category_kpi', array_search($_POST['category_kpi'], $this->data['category_arr']));
            
			// kiem tra field  kpi_name bang rong => tra ve loi 
			if ($_POST['kpi_name'] == "") {
				$this->setData('success', false);
				$this->setData('kpi_name_error', true);
                $error_list[] = ' KPI';
			}
			$this->setData('kpi_name', $_POST['kpi_name']);
			// kiem tra field  months bang rong => tra ve loi 
			// if ($_POST['months'] == "") {
				// $this->setData('success', false);
				// $this->setData('months_error', true);
                // $error_list[] = ' 期間';
			// }
			if ($_POST['months'] != "" && !preg_match("/^(1[0-2]|0[1-9])(\-(1[0-2]|0[1-9]))*$/", $_POST['months'])) {
				$this->setData('success', false);
				$this->setData('months_error', true);
                $error_list[] = ' nhap vao kieu so';
			}
            // set array month, va sort
			$this->setData('months', $_POST['months']);
            $month_array = array();
			$tmp_arr = explode('-', $_POST['months']);
            // asort($tmp_arr);
			
            foreach ($tmp_arr as $key => $value) {
                $month_array[] = $value;
            }
            $goal_months_arr = array();
            $goal_markMonths_arr = array();  
            // tra ve goal, mark goal cua 12 thang
            for ($i=1; $i <= 12 ; $i++) {
                 $search = array_search(sprintf("%'.02d", $i), $month_array);
                 if($search === false){
                     $goal_months_arr[]="";
                     $goal_markMonths_arr[]="";
                 }
                 else
                 {
                    // if($_POST['goal_month'][$search] == ""){
                        // $this->setData('success', false);
                        // //$this->setData('months_error', true);
                        // $this->setData('goal_month_error', true);
                    // }
                    // if($_POST['goal_markMonth'][$search] == ""){
                        // $this->setData('success', false);
                        // //$this->setData('months_error', true);
                        // $this->setData('goal_month_error', true);
                    // }
                    if ($_POST['goal_month'][$search] != "" && !is_numeric($_POST['goal_markMonth'][$search])) {
                        $this->setData('success', false);
                        //$this->setData('months_error', true);
                        $this->setData('goal_markMonth_error', true);
                        $error_list[] = ' nhap vao kieu so';
                    }
                     $goal_months_arr[]=$_POST['goal_month'][$search];
                     $goal_markMonths_arr[]=$_POST['goal_markMonth'][$search];
                 }
            }
			
            $this->setData('goal_month', $goal_months_arr);
            $this->setData('goal_markMonth', $goal_markMonths_arr);
            
            // set goal bang tong cua diem 12 thang
            $sum = 0;
            foreach ($goal_markMonths_arr as $key => $value) {
                $sum += $value;
            }
            // kiem tra field goal rong, thi thong bao loi
			// if (empty($_POST['goal'])) {
				// $this->setData('success', false);
				// $this->setData('goal_error', true);
			// }
			
			$this->setData('goal', $sum);
            // $this->setData('point_kpi', $_POST['point_kpi']);
			
            // sort month theo thu tu tang dan
			$ms = explode("-", $_POST['months']);
			asort($ms);
            $month_array = array();
            foreach ($ms as $key => $value) {
                $month_array[] = $value;
            }
            // set ngay bat dau la 01
            // duc
            $startAtMonth = $this->getData('startAtMonth') - 1;
            $currentYear = (int)$this->getData('year');
            if((int)$month_array[0] > $startAtMonth) {
            	$start_date = $currentYear."-".$month_array[0]."-01";
				$day_in_maxMonth = cal_days_in_month(CAL_GREGORIAN,(int)$month_array[count($month_array) -1],$currentYear);
				$end_date = $currentYear."-".$month_array[count($month_array) -1]."-".$day_in_maxMonth;
            } else {
            	$temp = (int)$month_array[0];
				// $flag != 0 when $month_array include $value [1, 2, 3] of next year
				$flag = 0;
				for($i = 1; $i < count($month_array); $i++) {
					if((int)$month_array[$i] <= $startAtMonth) {
						// month in next year
						$temp = (int)$month_array[$i];
					} else {
						// month in current year
						$flag = $i;
						break;
					}
				}
				
				// next year
				if($flag != 0) {
					// months: 1-2-6-8
					$start_date = $currentYear."-".$month_array[$flag]."-01";
				} else {
					// 1-2
					$start_date = ($currentYear + 1)."-".$month_array[0]."-01";
				}
				$day_in_maxMonth = cal_days_in_month(CAL_GREGORIAN,$temp,($currentYear + 1));
				$end_date = ($currentYear + 1)."-".$temp."-".$day_in_maxMonth;
            }
			//
            
            // $start_date = $this->getData('year')."-".$month_array[0]."-01";
            // $day_in_maxMonth = cal_days_in_month(CAL_GREGORIAN,(int)$month_array[count($month_array) -1],(int)$this->getData('year'));
			// // set ngay ket thuc la ngay lon nhat trong thang
			// $end_date = $this->getData('year')."-".$month_array[count($month_array) -1]."-".$day_in_maxMonth;
            
            if ($_POST['months'] == ""){
                $start_date = date("Y-m-d");
                if ($this->getData('year') > date("Y")){
                    $start_date = $this->getData('year')."-01-01";
                }
                
                $end_date ="";
            }
			$this->setData('start_date', $start_date);
            $this->setData('end_date', $end_date);
			

			if ($this->data['success'] == false) {
				// message: Vui long nhap lai cac field sai (mau do)
				// $this->data['message'] = "項目が差異があって、再入力してください";	
                	
                $this->data['message'] =  getMessageById("103", implode(",", $error_list)) ;		
				
				if ($_POST['submit'] =='update'){
					$this->setData('kpi_id', $_POST['kpi_id']);
					$this->setData('update_error', true);
				}
			}
			else{
				// nhan button edit
				if ($_POST['submit'] =='update' && isset($_POST['kpi_id'])) {
					// edit kpi
					try{
					    $this->setData('kpi_id', $_POST['kpi_id']);
                        if (isset($_POST['months']) && $_POST['months'] != ""){
                        	// duc
                        	$checkMonth = explode('-', $_POST['months']);
							$checkedMonth = array();
							$currentYear = $this->getData('year');
							for($i = 0; $i < count($checkMonth); $i++) {
								if((int)$checkMonth[$i] > $startAtMonth) {
									$checkedMonth[$i] = $currentYear . $checkMonth[$i];
								} else {
									$nextYear = (int)$currentYear + 1;
									$checkedMonth[$i] = $nextYear . $checkMonth[$i];
								}
							}
							$this->setData('month_array_new', $checkedMonth);
							//
							
                            //$this->setData('month_array_new', array_map(function($e){return $this->getData('year').$e;}, explode('-', $_POST['months'])) );
                        }
                        else {
                            $this->setData('month_array_new', array());
                        }
                        $this->setData('comment_user', $_POST['comment_user']); 
						// Data to check change
						$dataKpiBase = Data::getKpiBaseInfoFromKpiId($this->data['kpi_id']);
						$dataKpiManager = Data::getKpiManagerInfoFromKpiId($this->data['kpi_id']);
						// Update data
                        Data::editKpiManager($user->company_id,
                                             $user->user_id, 
                                             $this->data['goal'],
                                             $this->data['start_date'], 
                                             $this->data['end_date'], 
                                             $this->data['category_kpi'], 
                                             $this->data['kpi_name'],
                                             $this->data['comment_user'],
                                             $this->data['month_array_new'],
                                             $this->data['goal_month'],
                                             $this->data['goal_markMonth'],
                                             $this->data['kpi_id'],
                                             $this->getData('year'),
                                             "",
											 User::getCurrentUser()->start_month);
                        // $this->setData('message', 'da chinh sua thanh cong');
                        $this->setData('message', getMessageById("002"));
                        $_POST['submit'] ='update';
						
						// Start process insert table History
						$changeFlag = 0; // 0: no change	1: change main content	2: change month content
						$code = "KPI変更";
						$username = Data::getUserNameByUserId($user->company_id, $user->user_id);
						if($user->user_id == User::getCurrentUser()->user_id) {
							$content = "ID：".$username."、KPI：".$this->data['kpi_name']."が変更されました。\n（";
						} else {
							$action_username = Data::getUserNameByUserId($user->company_id, User::getCurrentUser()->user_id);
							$content = "ID：".$action_username."、".$username."の"."KPI：".$this->data['kpi_name']."が変更されました。\n（";
						}
						
						// check change main content
						if($this->data['category_kpi'] != $dataKpiBase['category_kpi']){
							$content = $content."カテゴリ:".$dataKpiBase['category_kpi']."⇒".$this->data['category_kpi']."; ";
							$changeFlag = 1;
						}
						if($this->data['kpi_name'] != $dataKpiBase['kpi_name']){
							$content = $content."KPI:".$dataKpiBase['kpi_name']."⇒".$this->data['kpi_name']."; ";
							$changeFlag = 1;
						}
						if($this->data['goal'] != $dataKpiBase['goal']){
							$content = $content."目標値:".$dataKpiBase['goal']."⇒".$this->data['goal']."; ";
							$changeFlag = 1;
						}
						if($this->data['comment_user'] != $dataKpiBase['comment_user']){
							$content = $content."コメント:".$dataKpiBase['comment_user']."⇒".$this->data['comment_user']."; ";
							$changeFlag = 1;
						}
						if($changeFlag == 1){
							$content = $content."\n";
						}
						// check change month content
						$month_array_new = $this->data['month_array_new'];
						$goal_month_array = $this->data['goal_month'];
						$goal_markMonth_array = $this->data['goal_markMonth'];
						$month_add = "";
						$month_changed = "";
						$month_deleted = "";
						$month_old_array = array();
						foreach ($dataKpiManager as $key => $u) {
				                $month_old_array[] = $u['month'];
				        }
						foreach ($month_array_new as $key => $month_new) {
							if (!in_array($month_new, $month_old_array )){
								$month_add = $month_add.$month_new."、";
							}
						}
						foreach ($dataKpiManager as $key => $month_old) {
							if (!in_array($month_old['month'], $month_array_new )){
								$month_deleted = $month_deleted.$month_old['month']."、";
							}
					        else{
					            $goal_month = $goal_month_array[(int)substr($month_old['month'], -2) -1];
					            $goal_markMonth = $goal_markMonth_array[(int)substr($month_old['month'], -2) -1];
					            if($goal_month != $month_old['goal_month'] || $goal_markMonth != $month_old['mark_month']){
					            	$month_changed = $month_changed.$month_old['month'].":";
						            
									if($goal_month != $month_old['goal_month'] && $goal_markMonth != $month_old['mark_month']){
										$month_changed = $month_changed."目標が".$month_old['goal_month']."⇒".$goal_month."、";
										$month_changed = $month_changed."値が".$month_old['mark_month']."⇒".$goal_markMonth."; ";
									}else{
										if($goal_month != $month_old['goal_month']){
						            		$month_changed = $month_changed."目標が".$month_old['goal_month']."⇒".$goal_month.";";
										}
										if($goal_markMonth != $month_old['mark_month']){
											$month_changed = $month_changed."値が".$month_old['mark_month']."⇒".$goal_markMonth.";";
						            	}
										$month_changed = $month_changed."　";
						            }
								}
					        }	
						}
						if($month_add != "" || $month_changed != "" || $month_deleted != ""){
							$content = $content."期間:";
							if($month_add != ""){
								$content = $content.substr($month_add,0,strlen($month_add)-3)."が登録されました。";
								$changeFlag = 2;
							}
							if($month_changed != ""){
								$content = $content.$month_changed;
								$changeFlag = 2;
							}
							if($month_deleted != ""){
								$content = $content.substr($month_deleted,0,strlen($month_deleted)-3)."が削除されました。";
								$changeFlag = 2;
							}
						}
						if($changeFlag == 0){
							$content = $content."なし";
						}
						$content = $content."）";
						
						Data::insertHistory($user->company_id, $user->user_id, $this->data['kpi_id'], $code, $content, User::getCurrentUser()->user_id);
						
						// End process insert table History
					}
					catch(Exception $e){
					    $this->setData('success', false);
					    $this->setData('message', getMessageById("107"));
					}
				}
				else {
				    try{
				        // nhan button register 
                        if(isset($_POST['months']) && $_POST['months'] != ""){
                        	// duc
                        	$checkMonth = explode('-', $_POST['months']);
							$checkedMonth = array();
							$currentYear = $this->getData('year');
							for($i = 0; $i < count($checkMonth); $i++) {
								if((int)$checkMonth[$i] > $startAtMonth) {
									$checkedMonth[$i] = $currentYear . $checkMonth[$i];
								} else {
									$nextYear = (int)$currentYear + 1;
									$checkedMonth[$i] = $nextYear . $checkMonth[$i];
								}
							}
							$this->setData('month_array', $checkedMonth);
							//
							
                            // $this->setData('month_array', array_map(function($e){return $this->getData('year').$e;}, explode('-', $_POST['months'])) );
                        } else {
                            $this->setData('month_array', array());
                        }
                        
                        $this->setData('comment_user', $_POST['comment_user']);
						// Get KPI Name to insert History
						$kpi_name = $this->data['kpi_name'];
                        // insert kpi   
                        $kpi_id = Data::insertKpiManager($user->company_id,
                                                         $user->user_id, 
                                                         $this->data['goal'],
                                                         $this->data['start_date'], 
                                                         $this->data['end_date'], 
                                                         $this->data['category_kpi'], 
                                                         $this->data['kpi_name'],
                                                         $this->data['comment_user'],
                                                         $this->data['month_array'],
                                                         $this->data['goal_month'],
                                                         $this->data['goal_markMonth'],
                                                         $user->dep_id);
                        $this->setData('kpi_id', $kpi_id);
                        //ｔhis->setData('message', 'dk thanh cong');
                        $this->setData('message', getMessageById("001"));
                        $this->setData('category_kpi',"");
                        $this->setData('kpi_name',"");
                        $this->setData('months',"");
                        $this->setData('goal',"");
                        $this->setData('goal_month', array());
                        $this->setData('goal_markMonth', array());
						// Insert table History
						$code = "KPI登録";
						
						$username = Data::getUserNameByUserId($user->company_id, $user->user_id);
						if($user->user_id == User::getCurrentUser()->user_id) {
							$content = "ID：".$username."、KPI：".$kpi_name."が登録されました。";
						} else {
							$action_username = Data::getUserNameByUserId($user->company_id, User::getCurrentUser()->user_id);
							$content = "ID：".$action_username."、".$username."の"."KPI：".$kpi_name."が登録されました。";
						}
						Data::insertHistory($user->company_id, $user->user_id, $this->data['kpi_id'], $code, $content, User::getCurrentUser()->user_id);
        		    }
        			catch(Exception $e){
        			    $this->setData('success', false);
        			    $this->setData('message', getMessageById("106"));
        			}
				}	
			}						
									
		}
        // lay thong tin user dang nhap
    	
		$this->setData('dataKpi', Data::getAllKpiOfUserInYear($user->user_id,$user->company_id, $this->getData('year'), $this ->getData('search'), User::getCurrentUser()->start_month));	
		// lay danh sach user
		               
						 
        $this->render();
		
		}

	protected function insert_json()
    {      
        $user_id = User::getCurrentUser()->user_id;
        if (isset($_POST['user_id']) && $_POST['user_id'] != $user_id) {
            
            $post_user = User::getByID($_POST['user_id'], User::getCurrentUser()->company_id);
            
            if (User::getCurrentUser()->admin_flag == 1 || User::getCurrentUser()->admin_flag == 2 || User::getCurrentUser()->checkIsLeader($user_id, $post_user->user_id, User::getCurrentUser()->company_id)) {
                $user_id = $_POST['user_id'];
            }
            else {
                $res['success'] = false;
                $res['message'] =  getMessageById("109");
                
                echo json_encode($res);
                exit();
            }
        }
                
        $user = User::getByID($user_id, User::getCurrentUser()->company_id);  
        
        if ($user_id == " ") {
            $this->setData('user_id', User::getCurrentUser()->user_id);
        } else {
            $this->setData('user_id', $user_id);
        }
        if (empty($_POST['category_kpi'])) {
                $this->setData('success', false);
                $this->setData('category_kpi_error', true);
            }
            $this->setData('category_kpi', $_POST['category_kpi']);
            
            if (empty($_POST['kpi_name'])) {
                $this->setData('success', false);
                $this->setData('kpi_name_error', true);
            }
            $this->setData('kpi_name', $_POST['kpi_name']);
            
            $ms = $_POST['month_array'];
            asort($ms);
            $month_array = array();
            foreach ($ms as $key => $value) {
                $month_array[] = $value;
            }
            
            $goal_month_arr = $_POST['goal_month'];
            $goal_markMonth_arr = $_POST['goal_markMonth'];
             
            if (empty($_POST['goal'])) {
                $this->setData('success', false);
                $this->setData('goal_error', true);
            }
            elseif (!is_numeric($_POST['goal']) ) {
                $this->setData('success', false);
                $this->setData('goal_error', true);
            }
            $this->setData('goal', $_POST['goal']);
            $this->setData('comment_user', $_POST['comment_user']); 
            $this->setData('month_array', $month_array);
            $this->setData('goal_month', $goal_month_arr);
            $this->setData('goal_markMonth', $goal_markMonth_arr);
             
            if (empty($_POST['goal'])) {
                $this->setData('success', false);
                $this->setData('goal_error', true);
            }
            elseif (!is_numeric($_POST['goal']) ) {
                $this->setData('success', false);
                $this->setData('goal_error', true);
            }
            $this->setData('goal', $_POST['goal']);
            $this->setData('comment_user', $_POST['comment_user']); 
            $this->setData('dep_id', $_POST['dep_id']); 
            
            
            $this->getData('year');
            
            if (!empty($month_array)){
                $start_date = $this->getData('year')."-".substr($month_array[0], 4)."-01";
                $day_in_maxMonth = cal_days_in_month(CAL_GREGORIAN,(int)substr(max($month_array),4),(int)$this->getData('year'));
                $end_date = $this->getData('year')."-".substr(max($month_array),4)."-".$day_in_maxMonth;
                $this->setData('start_date', $start_date);
                $this->setData('end_date', $end_date);
            }else{
                $this->setData('start_date', date('Y-m-d'));
                $this->setData('end_date', null);
            }

        try {
            $this->setData("user", $user);     
   
         // nhan button register     
             $kpi_id = Data::insertKpiManager($user->company_id,
                                                     $this->data['user']->user_id, 
                                                     $this->data['goal'],
                                                     $this->data['start_date'],
                                                     $this->data['end_date'],
                                                     $this->data['category_kpi'], 
                                                     $this->data['kpi_name'],
                                                     $this->data['comment_user'],
                                                     $this->data['month_array'],
                                                     $this->data['goal_month'],
                                                     $this->data['goal_markMonth'],
                                                     $this->data['dep_id']);
             $this->setData('kpi_id', $kpi_id);
                                        
             $this->data['kpi_id'] = $kpi_id;
             $this->data['user'] = $user;
             $this->data['message'] = getMessageById("001");
             $this->setData('success', true);
			 // Insert table History
			$code = "KPI登録";
			// $action_username = Data::getUserNameByUserId($user->company_id, $this->data['user']->user_id);
			$action_username = Data::getUserNameByUserId($user->company_id, User::getCurrentUser()->user_id);
			$userid = Data::getUserIdByKpiId($kpi_id);
			
			if($userid == User::getCurrentUser()->user_id){
				$content = "ID：".$action_username."、KPI：".$this->data['kpi_name']."が登録されました。";
			} else {
				$username = Data::getUserNameByUserId($user->company_id, $userid);
				$content = "ID：".$action_username."、".$username."の"."KPI：".$this->data['kpi_name']."が登録されました。";
			}
			
			// $content = "ID：".$action_username."、KPI：".$this->data['kpi_name']."が登録されました。";
			Data::insertHistory($user->company_id, $userid, $kpi_id, $code, $content, User::getCurrentUser()->user_id);
             
        } catch (Exception $e) {
            $this->setData('success', false);
            $this->setData('code', $e->getCode());
            $this->setData('message', getMessageById("106"));
        }
        echo json_encode($this->data);  
    }           

    protected function update_json()
    {
        $user_id = User::getCurrentUser()->user_id;
        $user = User::getByID($user_id, User::getCurrentUser()->company_id);  
       
        try {
            if (isset($_POST['kpi_id'])) {     
                $this->setData('kpi_id', $_POST['kpi_id']);
                $this->setData("user", $user);   
                  
                // nhan button Update  
                $kpi_id = Data::editKpiManager($user->company_id,
                                         $user->user_id, 
                                         $_POST['goal'],
                                         $_POST['start_date'], 
                                         $_POST['end_date'], 
                                         $_POST['category_kpi'], 
                                         $_POST['kpi_name'],
                                         $_POST['comment_user'],
                                         $_POST['month_array_new'],
                                         $_POST['kpi_id'],
                                         $_POST['year'],
										 User::getCurrentUser()->start_month);
                                        
                    $this->setData('kpi_id', $kpi_id);
                    //$this->setData('user_id', $user);
                    $this->setData('message', getMessageById("002")); 
                }
            } catch (Exception $e) {
                $this->setData('success', false);
                $this->setData('code', $e->getCode());
                $this->setData('message', getMessageById("107"));
            }
        
        echo json_encode($this->data);
    }  

    protected function updateRegis_json()
    {
        $user_id = User::getCurrentUser()->user_id;
        if (isset($_POST['user_id']) && $_POST['user_id'] != $user_id) {
            
            $post_user = User::getByID($_POST['user_id'], User::getCurrentUser()->company_id);
            
            if (User::getCurrentUser()->admin_flag == 1 || User::getCurrentUser()->admin_flag == 2 || User::getCurrentUser()->checkIsLeader($user_id, $post_user->user_id, User::getCurrentUser()->company_id)) {
                $user_id = $_POST['user_id'];
            }
            else {
                $res['success'] = false;
                $res['message'] =  getMessageById("109");
                
                echo json_encode($res);
                exit();
            }
        }
                
        $user = User::getByID($user_id, User::getCurrentUser()->company_id);  
        $this->setData('user', $user);
        
        $ms = $_POST['month_array'];
        asort($ms);
        $month_array = array();
        foreach ($ms as $key => $value) {
            $month_array[] = $value;
        }
        
        $goal_month_arr = $_POST['goal_month'];
        $goal_markMonth_arr = $_POST['goal_markMonth'];
        $this->setData('kpi_id', $_POST['kpi_id']);
        // $this->setData('month_array', $_POST['month_array']);      
        $this->setData('goal', $_POST['goal']);
        $this->setData('kpi_name', $_POST['kpi_name']);
        $this->setData('comment_user', $_POST['comment_user']); 
        $goal_month_arr = $_POST['goal_month'];
        $goal_markMonth_arr = $_POST['goal_markMonth'];
        $this->getData('year');
        $this->setData('goal_month', $goal_month_arr); 
        $this->setData('goal_markMonth', $goal_markMonth_arr); 
        $this->setData('category_kpi', $_POST['category_kpi']);
        $this->setData('month_array', $_POST['month_array']);
        
        if (!empty($month_array)){
            $start_date = $this->getData('year')."-".substr($month_array[0], 4)."-01";
            $day_in_maxMonth = cal_days_in_month(CAL_GREGORIAN,(int)substr(max($month_array),4),(int)$this->getData('year'));
            $end_date = $this->getData('year')."-".substr(max($month_array),4)."-".$day_in_maxMonth;
            $this->setData('start_date', $start_date);
            $this->setData('end_date', $end_date);
        }else{
            $this->setData('start_date', date('Y-m-d'));
            $this->setData('end_date', null);
        }
        
        if (isset($_POST['kpi_id'])) {
				// Data to check change
				$dataKpiBase = Data::getKpiBaseInfoFromKpiId($this->data['kpi_id']);
				$dataKpiManager = Data::getKpiManagerInfoFromKpiId($this->data['kpi_id']);
				
				// edit kpi
                Data::editKpiManager($user->company_id,
                                     $this->data['user']->user_id, 
                                     $this->data['goal'],
                                     $this->data['start_date'], 
                                     $this->data['end_date'], 
                                     $this->data['category_kpi'], 
                                     $this->data['kpi_name'],
                                     $this->data['comment_user'],
                                     $this->data['month_array'],
                                     $this->data['goal_month'],
                                     $this->data['goal_markMonth'],
                                     $this->data['kpi_id'],
                                     $this->getData('year'),
                                     "",
									 User::getCurrentUser()->start_month);
                // $this->setData('message', 'da chinh sua thanh cong');
                $this->setData('message', getMessageById("002"));
				
                // Start process insert table History
				$changeFlag = 0; // 0: no change	1: change main content	2: change month content
				$code = "KPI変更";
				// $action_username = Data::getUserNameByUserId($user->company_id, $this->data['user']->user_id);
				$action_username = Data::getUserNameByUserId($user->company_id, User::getCurrentUser()->user_id);
				
				$userid = Data::getUserIdByKpiId($_POST['kpi_id']);
					
				if($userid == User::getCurrentUser()->user_id) {
					$content = "ID：".$action_username."、KPI：".$this->data['kpi_name']."が変更されました。\n（";
				} else {
					$username = Data::getUserNameByUserId($user->company_id, $userid);
					$content = "ID：".$action_username."、".$username."の"."KPI：".$this->data['kpi_name']."が変更されました。\n（";
				}
				// $content = "ID：".$action_username."、KPI：".$this->data['kpi_name']."が変更されました。\n（";
				
				// check change main content
				if($this->data['category_kpi'] != $dataKpiBase['category_kpi']){
					$content = $content."カテゴリ:".$dataKpiBase['category_kpi']."⇒".$this->data['category_kpi']."; ";
					$changeFlag = 1;
				}
				if($this->data['kpi_name'] != $dataKpiBase['kpi_name']){
					$content = $content."KPI:".$dataKpiBase['kpi_name']."⇒".$this->data['kpi_name']."; ";
					$changeFlag = 1;
				}
				if($this->data['goal'] != $dataKpiBase['goal']){
					$content = $content."目標値:".$dataKpiBase['goal']."⇒".$this->data['goal']."; ";
					$changeFlag = 1;
				}
				if($this->data['comment_user'] != $dataKpiBase['comment_user']){
					$content = $content."コメント:".$dataKpiBase['comment_user']."⇒".$this->data['comment_user']."; ";
					$changeFlag = 1;
				}
				if($changeFlag == 1){
					$content = $content."\n";
				}
				// check change month content
				$month_array_new = $this->data['month_array'];
				$goal_month_array = $this->data['goal_month'];
				$goal_markMonth_array = $this->data['goal_markMonth'];
				$month_add = "";
				$month_changed = "";
				$month_deleted = "";
				$month_old_array = array();
				foreach ($dataKpiManager as $key => $u) {
						$month_old_array[] = $u['month'];
				}
				foreach ($month_array_new as $key => $month_new) {
					if (!in_array($month_new, $month_old_array )){
						$month_add = $month_add.$month_new."、";
					}
				}
				foreach ($dataKpiManager as $key => $month_old) {
					if (!in_array($month_old['month'], $month_array_new )){
						$month_deleted = $month_deleted.$month_old['month']."、";
					}
					else{
						$goal_month = $goal_month_array[(int)substr($month_old['month'], -2) -1];
						$goal_markMonth = $goal_markMonth_array[(int)substr($month_old['month'], -2) -1];
						if($goal_month != $month_old['goal_month'] || $goal_markMonth != $month_old['mark_month']){
							$month_changed = $month_changed.$month_old['month'].":";
							
							if($goal_month != $month_old['goal_month'] && $goal_markMonth != $month_old['mark_month']){
								$month_changed = $month_changed."目標が".$month_old['goal_month']."⇒".$goal_month."、";
								$month_changed = $month_changed."値が".$month_old['mark_month']."⇒".$goal_markMonth."; ";
							}else{
								if($goal_month != $month_old['goal_month']){
									$month_changed = $month_changed."目標が".$month_old['goal_month']."⇒".$goal_month.";";
								}
								if($goal_markMonth != $month_old['mark_month']){
									$month_changed = $month_changed."値が".$month_old['mark_month']."⇒".$goal_markMonth.";";
								}
								$month_changed = $month_changed."　";
							}
						}
					}	
				}
				if($month_add != "" || $month_changed != "" || $month_deleted != ""){
					$content = $content."期間:";
					if($month_add != ""){
						$content = $content.substr($month_add,0,strlen($month_add)-3)."が登録されました。";
					}
					if($month_changed != ""){
						$content = $content.$month_changed;
					}
					if($month_deleted != ""){
						$content = $content.substr($month_deleted,0,strlen($month_deleted)-3)."が削除されました。";
					}
				}
				if($changeFlag == 0){
					$content = $content."なし";
				}
				$content = $content."）";
				Data::insertHistory($user->company_id, $user_id, $this->data['kpi_id'], $code, $content, User::getCurrentUser()->user_id);
				// End process insert table History
        }

       
        echo json_encode($this->data);
    }

    protected function loadKpiRegis_json()
    {
        if (isset($_POST['kpi_id'])) {
            $this->setData('kpi_id', $_POST['kpi_id']);
            // lay data tu bang kpi_base_t
           
            $this->setData('data_kpi_base', Data::getDataKpiByKpiId(User::getCurrentUser()->company_id, $this->getData('year'), $this->data['kpi_id'], User::getCurrentUser()->start_month));
            
            // lay data tu bang kpi_manager_t
            $this->setData('data_kpi_manager', Data::getKpiManagerByKpiId($this->getData('year'),$this->data['kpi_id'], User::getCurrentUser()->start_month));
            
            // set array months da dang ky
            $month_arr = array_map(function($e){return substr($e['month'], -2);}, $this->data['data_kpi_manager']);
            $this->setData('months', implode("-", $month_arr));
            
            $goal_month = array_map(function($e){return $e['goal_month'];}, $this->data['data_kpi_manager']);
            $goal_markMonth = array_map(function($e){return $e['mark_month'];}, $this->data['data_kpi_manager']);
           // set array muc tieu, diem cua 12 thang
            for ($i=1; $i <= 12 ; $i++) {
                 $search = array_search(sprintf("%'.02d", $i), $month_arr);
                 if($search === false){
                     $goal_month_arr[]="";
                     $goal_markMonth_arr[]="";
                 }else{
                     $goal_month_arr[]=$goal_month[$search];
                     $goal_markMonth_arr[]=$goal_markMonth[$search];
                 }     
            }
            $this->setData('goal_month', $goal_month_arr);
            $this->setData('goal_markMonth', $goal_markMonth_arr);  
            
        }
       
        echo json_encode($this->data);
    }

    


}

?>