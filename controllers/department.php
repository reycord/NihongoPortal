<?php 

class DepartmentController extends BaseController
{
     //add to the parent constructor
    public function __construct($route, $urlValues) {
        parent::__construct($route, $urlValues);
        
    }

    //bad URL request error
    protected function index()
    {
        $this->setData('success', true);
        $this->setData('save_success', false);
        $this->setData('update_success', false); 
        $this->setData('delete_success', false);
        $this->setData('admin_flag', User::getCurrentUser()->admin_flag);
        $this->setData('maintenance_flag', User::getCurrentUser()->maintenance_flag);
        $this->setData('company_list', DataMaintenance::getCompanyList());
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submit'] =='save_department' || $_POST['submit'] =='update_department'
            || $_POST['submit'] =='delete_department') {
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

            if ($_POST['department_id'] == "") {
                $this->setData('success', false);
                $this->setData('department_id_error', true);
                $error_list[] = ' 所属コード';
            }
            
            $this->setData('department_id', $_POST['department_id']);
            
            if ($_POST['department_name'] == "") {
                $this->setData('success', false);
                $this->setData('department_name_error', true);
                $error_list[] = ' 所属名';
            }
            
            $this->setData('department_name', $_POST['department_name']);
            
            
          
            if ($this->data['success'] == false) {
                       
                $this->data['message'] =   getMessageById("102", implode(",", $error_list));        
            }
            else{
                
                if ($_POST['submit'] =='save_department') {
                      
                    try{
                        DataMaintenance::insertDepartment($this->data['company_id'] ,$this->data['department_id'], $this->data['department_name']);
                        $this->setData('message', getMessageById("001"));
                        $this->setData('save_success', true);
                    } 
                    catch(Exception $e){
                        $this->setData('update_success', false);
                        $this->setData('save_success', false);
                        $this->setData('message', getMessageById("106"));
                    }
                }
                elseif ($_POST['submit'] =='update_department') {
                    try{
                        DataMaintenance::updateDepartment($this->data['company_id'] ,$this->data['department_id'], $this->data['department_name']);
                        $this->setData('message', getMessageById("002"));
                        $this->setData('update_success', true);
                    }    
                    catch(Exception $e){
                        $this->setData('update_success', false);
                        $this->setData('save_success', false);
                        $this->setData('message', getMessageById("107"));
                    }     
                }   
            }
            if ($_POST['submit'] =='delete_department') {
                      
                try{
                    DataMaintenance::deleteDepartment($this->data['company_id'] ,$this->data['department_id']);
                    $this->setData('message', getMessageById("003"));
                    $this->setData('success', true);
                    $this->setData('company_id_error', false);
                    $this->setData('department_id_error', false);
                    $this->setData('department_name_error', false);
                    unset($this->data['company_id']);
                    unset($this->data['department_id']);
                    unset($this->data['department_name']);
                }
                catch(Exception $e){
                    $this->setData('success', false);
                    $this->setData('message', getMessageById("108"));
                }
            }                                                   
        }
         
        if ($this->data['maintenance_flag'] === true){
            $this->setData('department_list', DataMaintenance::getDepartmentList(""));
        }else{
            $this->setData('department_list', DataMaintenance::getDepartmentList(User::getCurrentUser()->company_id));
        }
        $this->render('main');
    }
    protected function checkNewDepartment_json(){
        if(isset($_POST['new_department_id'])){
            if(isset($_POST['old_department_id']) && $_POST['old_department_id'] == $_POST['new_department_id']){
                echo json_encode(true);
                exit();
            }
            
            $check = DataMaintenance::checkNewDepartmentId($_POST['company_id'], $_POST['new_department_id']);
            if(!$check){
                echo json_encode(getMessageById("113", "所属"));
                exit();
            }
        }
        
        echo json_encode(true);
    }
}

 ?>