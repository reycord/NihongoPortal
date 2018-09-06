<?php 
class AuthenticateController extends BaseController
{
     //add to the parent constructor
    public function __construct($route, $urlValues) {
        parent::__construct($route, $urlValues);
        
    }

    //bad URL request error
    protected function login()
    {
    	$this->setData('companyList', Company::getCompanyList());
        if ($_POST['submit'] == 'login') {
            
			$error_list = array();
			$this->setData('success', true);
            // kiem tra input bang company_id rong => tra ve loi
			if ($_POST['company_id'] == "") {
				$this->setData('success', false);
				$this->setData('company_id_error', true);
				$error_list[] = ' 会社';
            }
            // kiem tra input bang user_id rong => tra ve loi
			if (empty($_POST['user_id'])) {
				$this->setData('success', false);
				$this->setData('user_id_error', true);
				$this->setData('password_error', true);			
				$error_list[] = ' ユーザー, パスワード';
            }
			// kiem tra input bang password rong => tra ve loi
			elseif (empty($_POST['password'])) {
				$this->setData('success', false);
				$this->setData('password_error', true);
				$error_list[] = ' パスワード';
            }
            // message loi
			if($this->data['success'] == false){
                $this->data['message'] = getMessageById("112",implode(",", $error_list));
			}
            
			elseif($this->data['success'] == true){
			
				$user_id = $_POST['user_id'];
				$password = $_POST['password'];
				$company_id = $_POST['company_id'];
	         
                $loginsuccess = User::logIn($user_id, $password, $company_id);
	            if ($loginsuccess){
	                if (User::getCurrentUser()->maintenance_flag == true){
	                    // login thanh cong
                        $location = $this->route->url("company");                 
	                }
	                else{
	                    $location = $this->route->url('home');          
	                }
                    if (isset($_GET['location'])) {
                        $location = $_GET['location'];
                    }
    
                    header("Location: $location");
                    exit();
	               
	            }
	            else{
	            	$this->setData('success', false);
	                $this->data['message'] = getMessageById("202");
					
	            }	
			}
           
        }

        $this->render('login');
    }

    protected function logout(){
    	$company_id = User::getCurrentUser()->company_id;
		$user_id = User::getCurrentUser()->user_id;
        User::logOut();
        $location = $this->route->url("authenticate", "login", array("company_id" => $company_id, "user_id" => $user_id));
        header("Location: $location");
		
        exit();
		
    }
    
    protected function login_json(){
        
        
        $res = array(
            'success' => true,
        );
        try {
            if (!isset($_POST['login']) && $_POST['login'] == "" ) {
                $res['message'] = '足しているパラメータ: login';
   
            }
            
            if (User::logIn($_POST['login'],$_POST['password'],$_POST['company_id'])){
                if(User::getCurrentUser()->maintenance_flag == "true")
                {
                    $res['success'] = false;
                    $res['message'] = getMessageById("204");
                } else{
                    $res['user'] = User::getCurrentUser();
                }
            }
            else {
                $res['success'] = false;
                $res['message'] = getMessageById("202");
            }

        } catch (Exception $e) {
            $res['success'] = false;
            $res['code'] = $e->getCode();
            $res['message'] = $e->getMessage();
        }

        echo json_encode($res);
        
        
    }
    
    protected function logout_json(){
        User::logOut();
        $res = array(
                'success' => true,
            );

        echo json_encode($res);
    }
    
    protected function company_list_json(){
        $res = array(
                'success' => true,
                'company_list' => Company::getCompanyList(), 
            );

        echo json_encode($res);
    }
    
    protected function year_list_json(){
        $res = array(
                'success' => true,
                'year_list' => Company::getYearList(),
            );

        echo json_encode($res);
    }
		
	//doc noi dung file thong bao
	protected function readAlarm_json(){
		$content="";
				
		$path = 'resources\alarm.txt';
			$fp = @fopen($path, "r");
		
			// Kiem tr mo file
			if (!$fp) {
			    echo 'ファイルを開くことはエラーが発生しました！';
			}
			else
			{
			    // Doc file va tra ve noi dung
			   // $data = fread($fp, filesize($path));
			    //echo $data;
				
				 // Lặp qua từng dòng để đọc
				 $i=0;
			    while(!feof($fp))
			    {
			    	if($i==0){
			    		 //echo fgets($fp);
						$content= "<ul>";
			    	}
					else{
						$content = $content . "<li>" .fgets($fp) . "</li>";
					}
			        $i++;
			    }
				$content =$content . "</ul>";
			}
			
			$res = array(
                'success' => true,
                'contentAlarm' => $content,
            );

        echo json_encode($res);
    }

}

 ?>