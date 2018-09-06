<?php 
class CategoryController extends BaseController
{
     //add to the parent constructor
    public function __construct($route, $urlValues) {
        parent::__construct($route, $urlValues);
        
    }

    //bad URL request error
    protected function index()
    {
        $this->setData('success', true);
        $this->setData('admin_flag', User::getCurrentUser()->admin_flag);
        $this->setData('maintenance_flag', User::getCurrentUser()->maintenance_flag);
        $this->setData('company_list', DataMaintenance::getCompanyList());
        // if ($this->data['maintenance_flag'] === true){
            // $this->setData('company_id', $_POST['company_id']);
        // }else{
            $this->setData('company_id', User::getCurrentUser()->company_id);
        //}

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submit'] =='save_category' || $_POST['submit'] =='update_category'
            || $_POST['submit'] =='delete_category') {
            // kiem tra field  category_kpi bang rong => tra ve loi   
            
            if ($_POST['category_id'] == "") {
                $this->setData('success', false);
                $this->setData('category_id_error', true);
                $error_list[] = ' カテゴリコード';
            }
            
            $this->setData('category_id', $_POST['category_id']);
            
            if ($_POST['category_name'] == "") {
                $this->setData('success', false);
                $this->setData('category_name_error', true);
                $error_list[] = ' カテゴリ名';
            }
            
            $this->setData('category_name', $_POST['category_name']);
            
            
          
            if ($this->data['success'] == false) {
                       
                $this->data['message'] =  getMessageById("102", implode(",", $error_list));        
            }
            else{
                
                if ($_POST['submit'] =='save_category') {
                      
                    try{
                        DataMaintenance::insertCategory($this->data['category_id'], $this->data['category_name']);
                        $this->setData('message', getMessageById("001"));
                        $this->setData('save_success', true);
                    }
                    catch(Exception $e){
                        $this->setData('message', getMessageById("106"));
                        $this->setData('update_success', false);
                        $this->setData('save_success', false);
                    }
                }
                elseif ($_POST['submit'] =='update_category') {
                    try{
                        DataMaintenance::updateCategory($this->data['category_id'], $this->data['category_name']);
                        $this->setData('message', getMessageById("002"));   
                        $this->setData('update_success', true);
                    }    
                    catch(Exception $e){
                        $this->setData('message', getMessageById("107"));   
                        $this->setData('update_success', false);
                        $this->setData('save_success', false);
                    } 
                }   
            }
            if ($_POST['submit'] =='delete_category') {
                  
                try{
                    DataMaintenance::deleteCategory($_POST['category_id']);
                    $this->setData('message', getMessageById("003"));
                    $this->setData('success', true);
                    unset($this->data['category_id']);
                    unset($this->data['category_name']);
                    $this->setData('category_id_error', false);
                    $this->setData('category_name_error', false);
                }
                catch(Exception $e){
                    $this->setData('message', getMessageById("107"));
                    $this->setData('success', false);
                }
            }                                                   
        }
         
        // if ($this->data['maintenance_flag'] === true){
            $this->setData('category_list', DataMaintenance::getCategoryList(""));
        // }else{
            // $this->setData('category_list', DataMaintenance::getCategoryList($this->data['company_id']));
        // }
        $this->render('main');
    }
    protected function checkNewCategory_json(){
        if(isset($_POST['new_category_id'])){
            if(isset($_POST['old_category_id']) && $_POST['old_category_id'] == $_POST['new_category_id']){
                echo json_encode(true);
                exit();
            }
            
            $check = DataMaintenance::checkNewCategoryId($_POST['new_category_id']);
            if(!$check){
                echo json_encode(getMessageById("113", "カテゴリ"));
                exit();
            }
        }
        
        echo json_encode(true);
    }
}

 ?>