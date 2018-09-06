<?php 
class InquirymanagerController extends BaseController
{
     //add to the parent constructor
    public function __construct($route, $urlValues) {
        parent::__construct($route, $urlValues);
        
    }

    //bad URL request error
    protected function index()
    {
        $this->setData('success', true);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['submit'] =='delete_message') {
             
            if ($_POST['message_id'] == "") {
                $this->setData('success', false);
                $this->setData('message_id_error', true);
                $error_list[] = ' 問合せコード';
            }
            
            if ($this->data['success'] == false) {
                $this->data['message'] = getMessageById("102", implode(",", $error_list)) ;        
            }
            else{
                try{
                    DataMaintenance::deleteMessage($_POST['message_id']);
                    $this->setData('success', true);
                    $this->setData('message', getMessageById("003"));
                    $this->setData('message_id', "");
                    unset($this->data['message_id']);
                    $this->setData('message_id_error', false);
                }
                catch(Exception $e){
                     $this->setData('success', false);
                    $this->setData('message', getMessageById("108"));
                }
            }                                   
        }
        
        $this->setData('message_list', DataMaintenance::getMessageList());
        $this->render('main');
    }

}

 ?>