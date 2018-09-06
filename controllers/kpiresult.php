<?php

require_once "controllers/kpi.php";

class KPIResultController extends KPIController {

    protected function index() {
        // get data kpi theo kpi_id
        $this -> setData('dataKpi', Data::getKpiBaseByKpiId($_POST['kpi_id'], $_POST['month']));
        
        $user_id = User::getCurrentUser() -> user_id;
        $this -> setData('data_info', Data::getInformationUser(User::getCurrentUser() -> company_id, $user_id));
        
        // kiem tra user co admin_flag = 1. thi co quyen xem data cua user khac
        $this -> setData('admin_flag', $this -> data['data_info']['admin_flag']);
        if (isset($_GET['user_id']) && $_GET['user_id'] != $user_id) {
            if ($this -> data['admin_flag'] == 1) {
                $user_id = $_GET['user_id'];
            } else {
                $this -> setData('success', false);
                $this -> setData('message', getMessageById("109"));
                $this -> render();
                exit();
            }
        }
        $user = User::getByID($user_id, User::getCurrentUser() -> company_id);
        // $year = date('Y');
        $this -> setData('data', Data::getDataKpiByKpiId(User::getCurrentUser() -> company_id, $this->getData('year'), $_POST['kpi_id'], User::getCurrentUser()->start_month));

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['submit'] == 'create' || $_POST['submit'] == 'delete'|| $_POST['submit'] == 'edit')) {
            $this -> setData('id', $_POST['id']);
            // nhan button dang ki thanh tich 
            if ($_POST['submit'] == 'create') {
                
                    // kiem tra result bang rong
                    if ($_POST['result'] =="") {
                        $this -> setData('success', false);
                        $this -> setData('result_warning', true);
                        $error_list[] = ' 内容';
                    }

                    $this -> setData('create_result', $_POST['result']);
                    $this -> setData('create_day', $_POST['day']);
                    
                    // kiem tra mark bang rong
                    if ($_POST['mark'] == "") {
                        $this -> setData('success', false);
                        $this -> setData('mark_warning', true);  
                        $error_list[] = ' 回数';       

                    }
                    $this -> setData('create_mark', $_POST['mark']);
                    $this -> setData('create_day', $_POST['day']);

                    if ($this -> data['success'] == false) {
                        //$this->data['message'] = "Vui long nhap vao cac field mau do";
                        $this->data['message'] =  getMessageById("102", implode(",", $error_list));    
                        //$this -> data['message'] = "項目が差異があって、再入力してください";
                    } elseif ($_POST['mark'] < 1) {
                        $this -> setData('mark_warning', true);
                        $this -> setData('warning', true);
                        //$this->data['message'] = "Vui long nhap diem lon hon 0";
                        $this -> data['message'] = getMessageById("118");
                        $this -> setData('success', false);
                    } else {
                        try{
                             // dang ky thanh tich 
                            Data::insertResultKpi($_POST['kpi_id'], $_POST['month'], $_POST['day'], $_POST['result'], $_POST['mark']);
                            $this -> setData('create_result', "");
                            $this -> setData('create_mark', "");
                            $this -> setData('create_day', "");
                            $this -> setData('id', $_POST['id']);
                            $this -> data['message'] = getMessageById("001");
							// Insert table History
							$code = "KPI実績登録";
							$day = "";
							if($_POST['day']>9)
								$day = $_POST['day'];
							else 
								$day = "0". $_POST['day'];
							$action_username = Data::getUserNameByUserId($user->company_id, $user->user_id);
							$kpi_name = Data::getKpiNameByKpiId($_POST['kpi_id']);
							$user_id = Data::getUserIdByKpiId($_POST['kpi_id']);
							
							if($user_id == $user->user_id) {
								$content = "ID：".$action_username."、KPI：".$kpi_name."の".$_POST['month'].$day."の実績が登録されました。";
							} else {
								$username = Data::getUserNameByUserId($user->company_id, $user_id);
								$content = "ID：".$action_username."、".$username."の"."KPI：".$kpi_name."の".$_POST['month'].$day."の実績が登録されました。";
							}

							// $content = "ID：".$action_username."、KPI：".$kpi_name."の".$_POST['month'].$day."の実績が登録されました。";
							Data::insertHistory($user->company_id, $user_id, $_POST['kpi_id'], $code, $content, $user->user_id);
                        }
                        catch(Exception $e){
                            $this->setData('success', false);
                            $this -> data['message'] = getMessageById("106");
                        }
                    }
                }
                
            
            // nhan button xoa 
            if ($_POST['submit'] == 'delete') {
                try{
                    Data::deleteResultKpi($_POST['delete_id']);
                    //$this->setData('message', 'Xoa thanh cong');
                    $this -> setData('message', getMessageById("003"));
										
					// Insert table History
					$code = "KPI実績削除";
					$action_username = Data::getUserNameByUserId($user->company_id, $user->user_id);
					$dataResult = Data::getResultFromId($_POST['delete_id']); 
					$day = "";
					if($dataResult['day']>9)
						$day = $dataResult['day'];
					else 
						$day = "0". $dataResult['day'];
					$kpi_name = Data::getKpiNameByKpiId($dataResult['kpi_id']);
					$user_id = Data::getUserIdByKpiId($_POST['kpi_id']);
					
					if($user_id == $user->user_id) {
						$content = "ID：".$action_username."、KPI：".$kpi_name."の".$dataResult['month'].$day."の実績が削除されました。";
					} else {
						$username = Data::getUserNameByUserId($user->company_id, $user_id);
						$content = "ID：".$action_username."、".$username."の"."KPI：".$kpi_name."の".$dataResult['month'].$day."の実績が削除されました。";
					}
					
					// $content = "ID：".$action_username."、KPI：".$kpi_name."の".$dataResult['month'].$day."の実績が削除されました。";
					Data::insertHistory($user->company_id, $user_id, $_POST['kpi_id'], $code, $content, $user->user_id);
                }
                catch(Exception $e){
                    $this->setData('success', false);
                    $this -> setData('message', getMessageById("108"));
                }   
            }

         }
        // kiem tra so ngay trong thang
        $this ->setData('maxDayInMonth', cal_days_in_month(CAL_GREGORIAN, (int)substr($_POST['month'], 4), (int)substr($_POST['month'], 0, 4)));
        $months_array = array_map(function($n) {
            return $n['month'];
        }, Data::getMonthOfKpiId($_POST['kpi_id']));
        $current_month_index = array_search($_POST['month'], $months_array);
        
        // kiem tra thang truoc
        $prev_month_index = $current_month_index - 1;
        if ($prev_month_index >= 0) {
            $this -> setData('prev_month', $months_array[$prev_month_index]);
        }
        // kiem tra thang sau
        $next_month_index = $current_month_index + 1;
        if ($next_month_index < sizeof($months_array)) {
            $this -> setData('next_month', $months_array[$next_month_index]);
        }
        
        // lay data cua 1 thang
        $this -> setData('resultKpis', Data::getResultKpi($_POST['kpi_id'], $_POST['month']));
        $this->setData('day_arr', array_map(function($e){return (int)$e['day'];}, $this->data['resultKpis']));
        $day_list = array();
        // danh sach ngay chua dang ky ket qua
        // for ($i=1; $i <= $this->data['maxDayInMonth']; $i++) {
            // if(!in_array($i, $this->data['day_arr'])){
                // $day_list[] = $i;
            // }  
        // }
        // $this->setData('day_list', $day_list);
        // diem cua 1 thang
        $this -> setData('markMonth', Data::getMarkMonth($_POST['kpi_id'], $_POST['month']));
        // diem cua 1 kpi
        $this -> setData('markKpi', Data::getMarkKpi($_POST['kpi_id'], $_POST['month']));
        $this -> render('blank');
    }

    protected function index_json() {
        $kpi_id = "";
        if (isset($_POST['kpi_id'])) {
            $year = $_POST['kpi_id'];
        }

        $month = "";
        if (isset($_POST['month'])) {
            $month = $_POST['month'];
        }

        $user = Data::getResultKpi($_POST['kpi_id'], $_POST['month']);

        $res = array('success' => true, 'data_json' => Data::getResultKpi($_POST['kpi_id'], $_POST['month']));

        echo json_encode($res);
    }

    //lay ra data cua cac KPI 1 ngay hoac 1 thang
    protected function getKpiOneMonthDay_json() {
        $user_id = User::getCurrentUser() -> user_id;
        $month = "";

        if (isset($_POST['month'])) {
            $month = $_POST['month'];
        }

        $day = "";
        if (isset($_POST['day'])) {
            $day = $_POST['day'];
        }
        $user = User::getByID(User::getCurrentUser() -> user_id, User::getCurrentUser() -> company_id);

        $data_arr[] = Data::getAllKpiOfUserInMonthAndDay(User::getCurrentUser()->user_id, User::getCurrentUser()->company_id, $month);
		
        foreach ($data_arr as $key => $u) {
            foreach ($u as $key => $row) {
                $data[] = $row;
            }
            
        }

        $res = array(
                'success' => true,
                'data_json' => $data
            );

        echo json_encode($res);
    }

    //lay ra data cua cac KPI trong 1 thang
    protected function getKpiOneMonth_json() {
        $month = "";

        if (isset($_POST['month'])) {
            $month = $_POST['month'];
        }

        $user = User::getByID(User::getCurrentUser() -> user_id, User::getCurrentUser() -> company_id);
        
		$data_arr[] = Data::getAllKpiOfUserInMonthAndDay(User::getCurrentUser()->user_id, User::getCurrentUser()->company_id, $month);
		
		$data = array();
        foreach ($data_arr as $key => $u) {
            foreach ($u as $key => $row) {
                $data[] = $row;
            }
            
        }

        $res = array(
                'success' => true,
                'data_json' => $data
            );

        echo json_encode($res);
    }
        
    //lay ra data cua cac KPI trong 1 thang
    protected function getKpiOneMonthByKpiId_json() {
        $month = "";
        $kpiId = "";

        if (isset($_POST['month'])) {
            $month = $_POST['month'];
        }
        
        if (isset($_POST['kpiId'])) {
            $kpiId = $_POST['kpiId'];
        }

        $user = User::getByID(User::getCurrentUser() -> user_id, User::getCurrentUser() -> company_id);

        $data_arr[] = Data::getAllKpiOfUserInMonthAndDayByKpiId(User::getCurrentUser()->user_id, User::getCurrentUser()->company_id, $month, $kpiId);

        foreach ($data_arr as $key => $u) {
            foreach ($u as $key => $row) {
                $data[] = $row;
            }
            
        }

        $res = array(
                'success' => true,
                'data_json' => $data
            );

        echo json_encode($res);
    }

    protected function insertKpiOneDay_json() {

        try {

            if (empty($_POST['result'])) {
                $this -> setData('success', false);
                $this -> setData('result_error', true);
                $error_list[] = ' 内容';
            }

            $this -> setData('create_result', $_POST['result']);
            $this -> setData('create_day', $_POST['day']);

            if (empty($_POST['mark'])) {
                if ($_POST['mark'] == 0) {
                    $this -> setData('success', true);
                    $this -> setData('mark_error', false);
                } else {
                    $this -> setData('success', false);
                    $this -> setData('mark_error', true);
                    $error_list[] = ' 回数';
                }

            }

            $this -> setData('create_mark', $_POST['mark']);
            $this -> setData('create_day', $_POST['day']);

            if ($this -> data['success'] == false) {
                //$this->data['message'] = "Vui long nhap vao cac field mau do";
                $this -> data['message'] = getMessageById("102", implode(",", $error_list));
            } 
            elseif ($_POST['mark'] < 1) {
                $this -> setData('mark_error', true);
                $this -> setData('success', false);
                //$this->data['message'] = "Vui long nhap diem lon hon 0";
                $this -> data['message'] = getMessageById("118");
            } else {
                Data::insertResultKpi($_POST['kpi_id'], $_POST['month'], $_POST['day'], $_POST['result'], $_POST['mark']);
                $this -> data['message'] = getMessageById("001");
				// Insert table History
				$code = "KPI実績登録";
				$day = "";
				if($_POST['day']>9)
					$day = $_POST['day'];
				else 
					$day = "0". $_POST['day'];
				$user_id = Data::getUserIdByKpiId($_POST['kpi_id']);
				$username = Data::getUserNameByUserId(User::getCurrentUser() -> company_id, $user_id);
				$kpi_name = Data::getKpiNameByKpiId($_POST['kpi_id']);
				
				if($user_id == User::getCurrentUser()->user_id) {
					$content = "ID：".$username."、KPI：".$kpi_name."の".$_POST['month'].$day."の実績が登録されました。";
				} else {
					$action_username = Data::getUserNameByUserId(User::getCurrentUser()->company_id, User::getCurrentUser()->user_id);
					$content = "ID：".$action_username."、".$username."の"."KPI：".$kpi_name."の".$_POST['month'].$day."の実績が登録されました。";
				}
				// $content = "ID：".$username."、KPI：".$kpi_name."の".$_POST['month'].$day."の実績が登録されました。";
				Data::insertHistory(User::getCurrentUser() -> company_id, $user_id, $_POST['kpi_id'], $code, $content, User::getCurrentUser()->user_id);
            }
        } catch (Exception $e) {
            $this -> setData('success', false);
            $this -> setData('code', $e -> getCode());
            $this -> setData('message', getMessageById("106"));
        }
        
        $this -> data['data_json'] = Data::getKpiOneMonth_jSon(User::getCurrentUser() -> user_id, $_POST['month']);
        
        
        echo json_encode($this -> data);
    }

    
    //2015/12/24 THAI
    //update cac kpi trong vong 1 ngay 
    protected function updateKpiOneDay_json(){

        $res = array(
            'success' => true,
        );
        try {
            if (!isset($_POST['id']) && $_POST['kpi_id'] == "" && $_POST['month'] == "" && $_POST['day'] == "" && $_POST['result'] == ""&& $_POST['mark'] == "" ) {
                throw new Exception(__('不足しているパラメータ: kpiresult'), ERR_LOGIN_MISSING_PARAMS_CD);
            }
            
            $id = "";
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
            }
            
            $kpi_id = "";
            if (isset($_POST['kpi_id'])) {
                $kpi_id = $_POST['kpi_id'];
            }
            
            $month = "";
            if (isset($_POST['month'])) {
                $month = $_POST['month'];
            }
            
            $day = "";
            if (isset($_POST['day'])) {
                $day = $_POST['day'];
            }
            
            $result = "";
            if (isset($_POST['result'])) {
                $result = $_POST['result'];
            }
            
            $mark = "";
            if (isset($_POST['mark'])) {
                $mark = $_POST['mark'];
            }
            
            //if (User::logIn($_POST['login'],$_POST['password'],$_POST['company_id'])){
			// Data to check change
			$dataKpiResult = Data::getKpiOneDay($id,$kpi_id,$month);
			
            // Update data Kpi One Day
			$updated = Data::UpdateKpiOneDay($id,$kpi_id,$month,$day,$result,$mark);
            if ($updated == 0) {
                throw new Exception("UpdateKpiOneDay Error", ERR_UPDATE_KPI_DAY);
            }
			
			// Insert table History
			$code = "KPI実績変更";
			$changeFlag = 0; // 0: no change	1: change main content	
			$day_withzero = "";
			if($_POST['day']>9)
				$day_withzero = $_POST['day'];
			else 
				$day_withzero = "0". $_POST['day'];
			$user_id = Data::getUserIdByKpiId($_POST['kpi_id']);
			$username = Data::getUserNameByUserId(User::getCurrentUser() -> company_id, $user_id);
			$kpi_name = Data::getKpiNameByKpiId($_POST['kpi_id']);
			
			if($user_id == $user->user_id) {
				$content = "ID：".$username."、KPI：".$kpi_name."の".$_POST['month'].$day_withzero."の実績が変更されました。\n（";
			} else {
				$action_username = Data::getUserNameByUserId(User::getCurrentUser()->company_id, User::getCurrentUser()->user_id);
				$content = "ID：".$action_username."、".$username."の"."KPI：".$kpi_name."の".$_POST['month'].$day_withzero."の実績が変更されました。\n（";
			}
			// $content = "ID：".$username."、KPI：".$kpi_name."の".$_POST['month'].$day_withzero."の実績が変更されました。\n（";
			
			if($day != $dataKpiResult['day']){
				$content = $content."日:".$dataKpiResult['day']."⇒".$day."; ";
				$changeFlag = 1;
			}
			if($result != $dataKpiResult['result']){
				$content = $content."詳細実績:".$dataKpiResult['result']."⇒".$result."; ";
				$changeFlag = 1;
			}
			if($mark != $dataKpiResult['mark']){
				$content = $content."値:".$dataKpiResult['mark']."⇒".$mark."; ";
				$changeFlag = 1;
			}
			if($changeFlag == 0){
				$content = $content."なし";
			}
			$content = $content."）";
			Data::insertHistory(User::getCurrentUser() -> company_id, $user_id, $_POST['kpi_id'], $code, $content, User::getCurrentUser()->user_id);
            
            $res = array(
                'success' => true,
                'updated' => $updated, // 0 or 1
                'data_json' => Data::getKpiOneMonth_jSon(User::getCurrentUser() -> user_id, $_POST['month'])
            );
            /*  
            }
            else {
                throw new Exception(__('UPDATE できませんでした'), ERR_LOGIN_USER_NOT_EXIST_CD);
            }
            */
        } catch (Exception $e) {
            $res['success'] = false;
            $res['code'] = $e->getCode();
            $res['message'] = $e->getMessage();
        }
        
        
        echo json_encode($res);
    }
}
?>
