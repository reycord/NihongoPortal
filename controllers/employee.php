<?php

require_once "controllers/kpi.php";

class EmployeeController extends BaseController {

    protected function changepassword() {
        if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submit'] =='editPassword' ){
            if ($_POST['password1'] == "") {
                $this->setData('success', false);
                $this->setData('password_error', true);
                $error_list[] = ' 新パスワード';
            }
              
            $this->setData('password1', $_POST['password1']);
            
            if ($_POST['password2'] == "") {
                $this->setData('success', false);
                $this->setData('password_error', true);
                $error_list[] = ' 確認パスワード';
            }
              
            $this->setData('password2', $_POST['password2']);
            
            if ($_POST['password1'] != "" && $_POST['password2'] != ""){
                if($_POST['password1'] != $_POST['password2']){
                    $this->setData('success', false);
                    $this->setData('password_error', true);
                    $error_list[] = getMessageById("111");
                }    
            }
            if ($this->data['success'] == false) {   
                $this->data['message'] = getMessageById("102", implode(",", $error_list)) ;         
            }
            else{
                $this->setData('password', $_POST['password1']);
                if($_POST['submit'] == 'editPassword'){
                    try{
                        User::editPassword(User::getCurrentUser()->company_id,User::getCurrentUser()->user_id, $this->data['password']);
                        $this->setData('message', getMessageById("006"));  
                        $this->setData('password1', "");
                        $this->setData('password2', "");
                    }
                    catch(Exception $e){
                        $this->setData('success', false);
                        $this->setData('message', getMessageById("104")); 
                    }
                }
                
            }  
        }
        if(User::getCurrentUser()->maintenance_flag){
            $this -> render('main');
        }else{
            $this -> render();
        }
        
    } 
}
?>
