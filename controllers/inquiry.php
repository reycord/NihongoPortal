<?php

require_once "controllers/kpi.php";

class InquiryController extends KPIController {

    protected function index() {
    			
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['submit'] == 'send_message') ) {
			$this->setData('success', true);
			// nhan button dang ki thanh tich 
			if ($_POST['submit'] == 'send_message') {
				if ($_POST['message_content'] == "") {
					$this->setData('success', false);
					$this->setData('message_id_error', true);
					$error_list[] = '内容';
					$this->setData('message_code', $_POST['message_code']);
					$this->setData('message_terminal', $_POST['message_terminal']);
					$this->setData('message_function', $_POST['message_function']);
				}
				if($this->data['success'] == false){
					$this->data['message'] = getMessageById("112",implode(",", $error_list));
				}else{
					Data::insertMessage(User::getCurrentUser()->company_id, User::getCurrentUser()->user_id, $_POST['message_code'], $_POST['message_terminal'], $_POST['message_function'], $_POST['message_content']);
					$string = getMessageById("014");
					echo '<script type="text/javascript">alert("' . $string . '");</script>';  
					$this->setData('message_content', "");
					
					// Check captcha
					/*
					session_start();
						
					if(isset($_POST['submit']))
					{
						if($_POST['txtCaptcha'] == NULL)
						{
							//echo "Please enter your code";
							$this->setData('success', false);
							$this->setData('message_code', $_POST['message_code']);
							$this->setData('message_terminal', $_POST['message_terminal']);
							$this->setData('message_function', $_POST['message_function']);
							$this->setData('message_content', $_POST['message_content']);
							$this->setData('message', getMessageById("119"));
						}
						else
						{
							if($_POST['txtCaptcha'] == $_SESSION['security_code'])
							{
								//echo "Ma captcha hop le";
								Data::insertMessage(User::getCurrentUser()->company_id, User::getCurrentUser()->user_id, $_POST['message_code'], $_POST['message_terminal'], $_POST['message_function'], $_POST['message_content']);
								$string = getMessageById("014");
								echo '<script type="text/javascript">alert("' . $string . '");</script>';  
								$this->setData('message_content', "");
							}
							else
							{
								//echo "Ma captcha khong hop le";
								$this->setData('success', false);
								$this->setData('message_code', $_POST['message_code']);
								$this->setData('message_terminal', $_POST['message_terminal']);
								$this->setData('message_function', $_POST['message_function']);
								$this->setData('message_content', $_POST['message_content']);
								$this->setData('message', getMessageById("120"));
							}
						}
					}
					*/
				}
			}
		 }

		$this->render();
    }
	
	protected function insertMessage_json() {
        try {

            if (empty($_POST['inquiry'])) {
                $this -> setData('success', false);
                $this -> setData('result_error', true);
                $error_list[] = '内容';
            }

            $this -> setData('create_code', $_POST['code']);
            $this -> setData('create_terminal', $_POST['terminal']);
            $this -> setData('create_function', $_POST['function']);
            $this -> setData('create_content', $_POST['content']);

            if (!empty($_POST['content'])) {
	            $this -> setData('success', true);
			}else {
                $this -> setData('success', false);
                $error_list[] = '内容';
            }            

            if ($this -> data['success'] == false) {
                $this -> data['message'] = getMessageById("102", implode(",", $error_list));
            } else {
                Data::insertMessage(User::getCurrentUser()->company_id, User::getCurrentUser()->user_id, $_POST['code'], $_POST['terminal'], $_POST['function'], $_POST['content']);
                $this -> data['message'] = getMessageById("014");
            }
        } catch (Exception $e) {
            $this -> setData('success', false);
            $this -> setData('code', $e -> getCode());
            $this -> setData('message', getMessageById("106"));
        }
        
        $this -> data['data_json'] = Data::getMessageList();
        
        echo json_encode($this -> data);
    }

}
?>
