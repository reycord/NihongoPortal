<?php 
class CompanyController extends BaseController
{
     //add to the parent constructor
    public function __construct($route, $urlValues) {
        parent::__construct($route, $urlValues);
        
    }

    //bad URL request error
    protected function index()
    {
        $this->setData('success', true);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submit'] =='save_company' || $_POST['submit'] =='update_company'
        || $_POST['submit'] =='delete_company') {
            // kiem tra field  category_kpi bang rong => tra ve loi 
              
            if ($_POST['company_id'] == "") {
                $this->setData('success', false);
                $this->setData('company_id_error', true);
                $error_list[] = ' 会社コード';
            }
            
            $this->setData('company_id', $_POST['company_id']);
            
            if ($_POST['company_name'] == "") {
                $this->setData('success', false);
                $this->setData('company_name_error', true);
                $error_list[] = ' 会社名';
            }
            
            $this->setData('company_name', $_POST['company_name']);
            
            
          
            if ($this->data['success'] == false) {
                       
                $this->data['message'] = getMessageById("102", implode(",", $error_list)) ;        
            }
            else{
                
                if ($_POST['submit'] =='save_company') {
                      
                    try{
                        DataMaintenance::insertCompany($this->data['company_id'], $this->data['company_name']);
						DataMaintenance::insertCompanyToConfig($this->data['company_id']);
                        $this->setData('message', getMessageById("001"));
                        $this->setData('save_success', true);
                    }
                    catch(Exception $e){
                        $this->setData('update_success', false);
                        $this->setData('save_success', false);
                        $this->setData('message', getMessageById("106"));
                    }
                }
                elseif ($_POST['submit'] =='update_company') {
                    try{
                        DataMaintenance::updateCompany($this->data['company_id'], $this->data['company_name']);
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
            if ($_POST['submit'] =='delete_company') {
                              
                try{
                    DataMaintenance::deleteCompany($this->data['company_id']);
                    $this->setData('success', true);
                    $this->setData('message', getMessageById("003"));
                    $this->setData('company_id', "");
                    unset($this->data['company_id']);
                    unset($this->data['company_name']);
                    $this->setData('company_id_error', false);
                    $this->setData('company_name_error', false);
                }
                catch(Exception $e){
                     $this->setData('success', false);
                    $this->setData('message', getMessageById("108"));
                }
            }                                   
        }
        
        $this->setData('company_list', DataMaintenance::getCompanyList());
        $this->render('main');
    }

    protected function checkNewCompany_json(){
        if(isset($_POST['new_company_id'])){
            if(isset($_POST['old_company_id']) && $_POST['old_company_id'] == $_POST['new_company_id']){
                echo json_encode(true);
                exit();
            }
            
            $check = DataMaintenance::checkNewCompanyId($_POST['new_company_id']);
            if(!$check){
                echo json_encode(getMessageById("113","会社"));
                exit();
            }
        }
        
        echo json_encode(true);
    }
}

 ?>