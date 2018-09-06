<?php 
class UserController extends BaseController
{
     //add to the parent constructor
    public function __construct($route, $urlValues) {
        parent::__construct($route, $urlValues);
        
    }

    //bad URL request error
    protected function index()
    {
        $this->setData('success', true);
        //$this->setData('admin_flag', User::getCurrentUser()->admin_flag);
        $this->setData('maintenance_flag', User::getCurrentUser()->maintenance_flag);
        $this->setData('company_list', DataMaintenance::getCompanyList());
        $this->setData('save_success', false);
        $this->setData('update_success', false); 
        $this->setData('delete_success', false); 
        if ($this->data['maintenance_flag'] === true){
            $this->setData('department_arr', DataMaintenance::getDepartmentList(""));
        }elseif (User::getCurrentUser()->admin_flag == 1){
            $this->setData('department_arr', DataMaintenance::getDepartmentList(User::getCurrentUser()->company_id)); 
        }else{
        	$this->setData('department_arr', DataMaintenance::getDepartmentListByManager(User::getCurrentUser()->company_id,User::getCurrentUser()->dep_id)); 
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submit'] =='save_user' || $_POST['submit'] =='update_user'
            || $_POST['submit'] =='delete_user') {
            // kiem tra field  category_kpi bang rong => tra ve loi 
            
            if ($this->data['maintenance_flag'] === true && $_POST['company_id'] == "") {
                $this->setData('success', false);
                $this->setData('company_id_error', true);
                $error_list[] = ' 会社コード';
            }  
            if ($this->data['maintenance_flag'] === true){
                $this->setData('company_id', $_POST['company_id']);
            }else{
                $this->setData('company_id', User::getCurrentUser()->company_id);
            }
            if ($_POST['user_id'] == "") {
                $this->setData('success', false);
                $this->setData('user_id_error', true);
                $error_list[] = ' ユーザーコード';
            }
            $this->setData('user_id', $_POST['user_id']);
            
            if ($_POST['name'] == "") {
                $this->setData('success', false);
                $this->setData('name_error', true);
                $error_list[] = ' ユーザー名';
            }  
            $this->setData('name', $_POST['name']);
            
            if ($_POST['leader_id'] == "") {
                if ($this->data['maintenance_flag'] != true){
                    $this->setData('success', false);
                    $this->setData('leader_id_error', true);
                    $error_list[] = ' リーダーコード';
                    $this->setData('leader_id', $_POST['leader_id']);  
                }
                else{
                    $user_list = array();
                    $user_list = DataMaintenance::getUserList($_POST['company_id']);
                    if (count($user_list) == 0){
                        $this->setData('leader_id', $_POST['user_id']);
                    }
                    else {
                        $this->setData('success', false);
                        $this->setData('leader_id_error', true);
                        $error_list[] = ' リーダーコード';
                        $this->setData('leader_id', $_POST['leader_id']);
                    }
                }
            }
            else {
                $this->setData('leader_id', $_POST['leader_id']);
            }  

            // if ($_POST['level'] == "") {
                // $this->setData('success', false);
                // $this->setData('level_error', true);
                // $error_list[] = ' レベル';
            // }     
            // $this->setData('level', $_POST['level']);
			$this->setData('level', '1');
            
            if ($_POST['password'] == "") {
                $this->setData('success', false);
                $this->setData('password_error', true);
                $error_list[] = ' パスワード';
            }
            
            $this->setData('password', $_POST['password']);
            
            if ($_POST['department_id'] == "") {
                $this->setData('success', false);
                $this->setData('department_id_error', true);
                $error_list[] = ' 所属コード';
            } 
            $this->setData('department_id', $_POST['department_id']);
            
			$admin_flag = 0;
			if ($_POST['manager_flag'] == "on") {
                $admin_flag = 2;
            }
            if ($_POST['admin_flag'] == "on") {
                $admin_flag = 1;
            }
            $this->setData('admin_flag', $admin_flag);
			
			$post_user_info = DataMaintenance::getUserInfo($_POST['company_id'], User::getCurrentUser()->dep_id, $_POST['user_id']);
			$post_user_admin_flag = $post_user_info['admin_flag'];
			if ($this->data['maintenance_flag'] === false and User::getCurrentUser()->admin_flag == 2 and $post_user_admin_flag == 1){
	            $this->setData('success', false);
                $this->setData('admin_flag_error', true);
                $error_list[] = ' 管理者';
	        }
  
            if ($this->data['success'] == false) { 
                if ($_POST['submit'] =='save_user') {
                    $this->setData('save_success', false);
                    $this->setData('update_success', false); 
                }
                elseif ($_POST['submit'] =='update_user') {
                    $this->setData('save_success', true);
                    $this->setData('update_success', false);
                }
				if ($this->data['maintenance_flag'] === false and User::getCurrentUser()->admin_flag == 2 and $post_user_admin_flag == 1){
		            $this->data['message'] = getMessageById("121");  
		        }else{
		        	$this->data['message'] = getMessageById("102", implode(",", $error_list)); 
		        }
            }
            else{
                
                if ($_POST['submit'] =='save_user') {
                    try{
                        DataMaintenance::insertUser($this->data['name'], $this->data['leader_id'], $this->data['level'], $this->data['password'], $this->data['department_id'], 
                                $this->data['admin_flag'], $this->data['company_id'], $this->data['user_id']);
                        $this->setData('message', getMessageById("001"));
                        $this->setData('save_success', true);
                    }
                    catch(Exception $e){
                        $this->setData('save_success', false);
                        $this->setData('update_success', false);
                        $this->setData('message', getMessageById("106"));
                    }
                }
                elseif ($_POST['submit'] =='update_user') {
                    
                    try{
                        DataMaintenance::updateUser($this->data['name'], $this->data['leader_id'], $this->data['level'], $this->data['password'], $this->data['department_id'], 
                                    $this->data['admin_flag'], $this->data['company_id'], $this->data['user_id']);
                        $this->setData('message', getMessageById("002"));
                        $this->setData('update_success', true);
                    }  
                    catch(Exception $e){
                        $this->setData('save_success', false);
                        $this->setData('update_success', false);
                        $this->setData('message', getMessageById("107"));
                    }      
                }   
            }
            if ($_POST['submit'] =='delete_user') {
                              
                try{
                	if (User::getCurrentUser()->maintenance_flag === false and User::getCurrentUser()->admin_flag == 2 and $post_user_admin_flag == 1){
			            $this->data['message'] = getMessageById("121");  
			        }else{
			        	DataMaintenance::deleteUser($this->data['company_id'] ,$_POST['user_id']);
	                    $this->setData('success', true);
	                    $this->setData('message', getMessageById("003"));
	                    unset($this->data['company_id']);
	                    unset($this->data['user_id']);
	                    $this->setData('company_id_error', false);
	                    $this->setData('department_id_error', false);
	                    $this->setData('leader_id_error', false);
	                    $this->setData('user_id_error', false);
	                    $this->setData('name_error', false);
	                    $this->setData('level_error', false);
	                    $this->setData('password_error', false);
					}
                }
                catch(Exception $e){
                    $this->setData('success', false);
                    $this->setData('message', getMessageById("108"));
                }
            }                                                    
        }
        
        if ($this->data['maintenance_flag'] === true){
            $this->setData('user_list', DataMaintenance::getUserList(""));
			$this->setData('user_list_leader', DataMaintenance::getUserList($this->data['company_id']));
        }elseif(User::getCurrentUser()->admin_flag == 1){
            $this->setData('user_list', DataMaintenance::getUserList(User::getCurrentUser()->company_id));
			$this->setData('user_list_leader', DataMaintenance::getUserList(User::getCurrentUser()->company_id));
        }else{
        	$this->setData('user_list', DataMaintenance::getUserListByManager(User::getCurrentUser()->company_id,User::getCurrentUser()->dep_id));
			$this->setData('user_list_leader', DataMaintenance::getUserListByManager(User::getCurrentUser()->company_id,User::getCurrentUser()->dep_id));
        }
        $this->render('main');
    }
    protected function checkNewUser_json(){
        if(isset($_POST['new_user_id'])){
            if(isset($_POST['old_user_id']) && $_POST['old_user_id'] == $_POST['new_user_id']){
                echo json_encode(true);
                exit();
            }
            
            $check = DataMaintenance::checkNewUserId($_POST['company_id'], $_POST['new_user_id']);
            if(!$check){
                echo json_encode(getMessageById("113","ユーザー"));
                exit();
            }
        }
        
        echo json_encode(true);
    }
}

 ?>