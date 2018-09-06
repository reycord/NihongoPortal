<?php

require_once "controllers/kpi.php";
require_once "config/lang_config.php";

class ListController extends KPIController
{

    protected function index()
	{
    	// nhan button xoa kpi
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submit'] =='delete' ) {                
            try{
                Data::deleteKpiManager($_POST['kpi_id']);
                //$this->setData('message', 'Xoa thanh cong');
                $this->setData('message', getMessageById("003"));
				// Insert table History
				$code = "KPI削除";
				$action_username = Data::getUserNameByUserId(User::getCurrentUser()->company_id, User::getCurrentUser()->user_id);
				$kpi_name = Data::getKpiNameByKpiId($_POST['kpi_id']);
				$user_id = Data::getUserIdByKpiId($_POST['kpi_id']);
				
				if($user_id == User::getCurrentUser()->user_id) {
					$content = "ID：".$action_username."、KPI：".$kpi_name."が削除されました。";
				} else {
					$username = Data::getUserNameByUserId(User::getCurrentUser()->company_id, $user_id);
					$content = "ID：".$action_username."、".$username."の"."KPI：".$kpi_name."が削除されました。";
				}
				// $content = "ID：".$action_username."、KPI：".$kpi_name."が削除されました。";
				Data::insertHistory(User::getCurrentUser()->company_id, $user_id, $_POST['kpi_id'], $code, $content, User::getCurrentUser()->user_id);
            }
            catch(Exception $e){
                $this->setData('success', false);
                $this->setData('message', getMessageById("108"));
            }
        }
        $user = User::getByID(User::getCurrentUser()->user_id, User::getCurrentUser()->company_id);
        $this->setData('category_arr', CategoryKPI::getListCategoryById());
        
        $data_arr = $this->getListKPI();

        $this->setData('data', $data_arr);

        $this->setData('user_arr', array_count_values(array_map(function($e){return $e['user_id'];}, $this->data['data'])));
        
        $this->render();
		
    }
	
		
    // Get danh sach len json
	protected function index_json(){
	    $data = array();
	    $year = date("Y");
        if (isset($_POST['year'])) {
            $year = $_POST['year'];
        }
        $search = "";
        if (isset($_POST['search'])) {
            $search = $_POST['search'];
        }
		
		$user = User::getByID(User::getCurrentUser()->user_id, User::getCurrentUser()->company_id);
		
        // linh sua
         $data_arr = $this->getListKPI();

        foreach ($data_arr as $key => $u) {
            foreach ($u as $key => $row) {
                $data[] = $row;
            }
            
        }

        //$this->setData('list', $data_arr);
        $res = array(
                'success' => true,
                //'data_json' => Data::getHomeData($year,User::getCurrentUser()->company_id, $search, User::getCurrentUser()->start_month),
                'data_json' => $data
            );

        echo json_encode($res);
        //echo json_encode($this -> data);
    }
    
    protected function getUserList_json(){
        $year = date("Y");
        if (isset($_POST['year'])) {
            $year = $_POST['year'];
        }
        $search = "";
        if (isset($_POST['search'])) {
            $search = $_POST['search'];
        }
        
         $data_arr = $this->getListKPI();
		 
        foreach ($data_arr as $key => $u) {
            foreach ($u as $key => $row) {
                $data[] = $row;
            }
            
        }
        
        try {
            $user_id = User::getCurrentUser()->user_id;
            $checkUser = User::getCurrentUser()->isLeader();
          
            if ($checkUser['admin_flag'] == true) {
                $list = Data::exportLeader(User::getCurrentUser()->company_id, $this->getData('year'), $this ->getData('search'), User::getCurrentUser()->start_month);
                $this->setData('list', $list);
                $this->setData('user', $user_id);
            } else {
                $this -> setData('success', false);
                $this -> data['message'] = "User nay khong phai admin";
            }

        } catch (Exception $e) {
            $this -> setData('success', false);
            $this -> setData('code', $e -> getCode());
            $this -> setData('message', $e -> getMessage());
        }
        echo json_encode($this -> data);
   
    }
	
	protected function listUserKpiRegis_json(){
        // lay user_id cuar user hien tai
        $user_id = User::getCurrentUser()->user_id;

        // lay danh sach nhan vien cap duoi theo admin va leader
        if (User::getCurrentUser()->admin_flag == 1){
            $users = User::getUserIDListByAdmin(User::getCurrentUser()->company_id);
        }
		elseif(User::getCurrentUser()->admin_flag == 2){
			//lay admin_flag cua leader cua user hien tai de kiem tra co phai la truong phong hay khong
			$leaderId = User::getCurrentUser()->leader_id; 
			$tmpUser = User::getById($leaderId,User::getCurrentUser()->company_id);
			$flagleader = $tmpUser->admin_flag;
			if (($flagleader==1)||($leaderId==User::getCurrentUser()->user_id)||($leaderId==null)) {
				$users = User::getUserIDListByManager(User::getCurrentUser()->company_id, User::getCurrentUser()->dep_id);
			} else {
				$users = User::getCurrentUser()->getListUserByLeader();
			}
        }
        else {
            //$users = User::getListUserByLeader(User::getCurrentUser()->company_id,User::getCurrentUser()->user_id);
            $users = User::getCurrentUser()->getListUserByLeader();
        }
    
        $this->setData('list', $users);
        echo json_encode($this -> data);
   
    } 
    
    protected function kpiDelete_json(){      
        $kpiDel = Data::deleteKpiManager($_POST['kpi_id']);
        //$this->setData('message', 'Xoa thanh cong');
        $this->setData('message', getMessageById("003"));
        $this->setData('list', $kpiDel);
		// Insert table History
		$code = "KPI削除";
		$user_id = Data::getUserIdByKpiId($_POST['kpi_id']);
		$username = Data::getUserNameByUserId(User::getCurrentUser()->company_id, $user_id);
		$kpi_name = Data::getKpiNameByKpiId($_POST['kpi_id']);
					
		if($user_id == User::getCurrentUser()->user_id) {
			$content = "ID：".$username."、KPI：".$kpi_name."が削除されました。";
		} else {
			$action_username = Data::getUserNameByUserId(User::getCurrentUser()->company_id, User::getCurrentUser()->user_id);
			$content = "ID：".$action_username."、".$username."の"."KPI：".$kpi_name."が削除されました。";
		}
		// $content = "ID：".$username."、KPI：".$kpi_name."が削除されました。";
		Data::insertHistory(User::getCurrentUser()->company_id, $user_id, $_POST['kpi_id'], $code, $content, User::getCurrentUser()->user_id);
        echo json_encode($this -> data);
    } 
    
    protected function message_category_json(){
        $message_id = $_POST['message_id'];
        $message_parameter = $_POST['message_parameter'];
        $res = array(
                'success' => true,
                //'message_list' => Company::getMessage()
                'message_content' => getMessageById($message_id,$message_parameter)
            );

        echo json_encode($res);
    }
	function getListKPI(){
		/* $users = User::getUserIDListByManager(User::getCurrentUser()->company_id, User::getCurrentUser()->dep_id);
            $data_arr = array();
            foreach ($users as $key => $u) {
                $data_arr[] = Data::getAllKpiOfUserInYear($u['user_id'], User::getCurrentUser()->company_id, $this->getData('year'), $this ->getData('search'));          
            }*/
		if(User::getCurrentUser()->admin_flag == 1){
            $users = User::getUserIDListByAdmin(User::getCurrentUser()->company_id);
            $data_arr = array();
            foreach ($users as $key => $u) {
                $data_arr[] = Data::getAllKpiOfUserInYear($u['user_id'], User::getCurrentUser()->company_id, $this->getData('year'), $this ->getData('search'), User::getCurrentUser()->start_month);          
            }
        }
		elseif(User::getCurrentUser()->admin_flag == 2){
			//lay admin_flag cua leader cua user hien tai de kiem tra co phai la truong phong hay khong
			$leaderId = User::getCurrentUser()->leader_id; 
			$tmpUser = User::getById($leaderId,User::getCurrentUser()->company_id);
			$flagleader = $tmpUser->admin_flag;
			if (($flagleader==1)||($leaderId==User::getCurrentUser()->user_id)||($leaderId==null))
			{
				$users = User::getUserIDListByManager(User::getCurrentUser()->company_id, User::getCurrentUser()->dep_id);
				$data_arr = array();
				foreach ($users as $key => $u) {
					$data_arr[] = Data::getAllKpiOfUserInYear($u['user_id'], User::getCurrentUser()->company_id, $this->getData('year'), $this ->getData('search'), User::getCurrentUser()->start_month);          
				}
			} else {
				$users = User::getCurrentUser()->getListUserByLeader();
				$data_arr = array();
				foreach ($users as $key => $u) {
					$data_arr[] = Data::getAllKpiOfUserInYear($u->user_id, User::getCurrentUser()->company_id, $this->getData('year'), $this ->getData('search'), User::getCurrentUser()->start_month);          
            }
			}
        }
        elseif (User::getCurrentUser()->isLeader() == true) {
            $users = User::getCurrentUser()->getListUserByLeader();
            $data_arr = array();
            foreach ($users as $key => $u) {
                $data_arr[] = Data::getAllKpiOfUserInYear($u->user_id, User::getCurrentUser()->company_id, $this->getData('year'), $this ->getData('search'), User::getCurrentUser()->start_month);          
            }
        }
        else {
            $data_arr[] = Data::getAllKpiOfUserInYear(User::getCurrentUser()->user_id, User::getCurrentUser()->company_id, $this->getData('year'), $this ->getData('search'), User::getCurrentUser()->start_month);
            
        }
		
		return $data_arr;
	}
    
}

?>
