<?php

require_once "controllers/kpi.php";

class HomeController extends KPIController
{

    // Get danh sach len web
    protected function index()
    {
        $this->setData('success', true);
		// import notification for admin
        if ($_POST['submit'] =='import') {
            if($_FILES['file_import']['tmp_name'] == ""){
                $this->setData('success', false);
                $this->setData('warning_message', getMessageById("104"));
            }
            else{
                $path = $_FILES['file_import']['tmp_name'];
                $this->setData('success', true);
				
				// Read file and insert Notification
				$content = "";
				try{
					if (!file_exists($path) ) {
				    	throw new Exception('File not found.');
				    }
					$myfile = fopen($path, "r") or die("Unable to open file!");
					// Output one line until end-of-file
					while(!feof($myfile)) {
						$content = $content.fgets($myfile);
					}
					fclose($myfile);
				}catch  ( Exception $e ){ }
				Data::insertNotification(User::getCurrentUser()->company_id, User::getCurrentUser()->user_id, $content);
				$this->setData('content', $content);
            }
        }
		// Load Release Info
		$path = "alarm". DIRECTORY_SEPARATOR ."ReleaseInfo_Web.txt";
		$content_release = "";
		try{
			if (!file_exists($path) ) {
		    	throw new Exception('File not found.');
		    }
			$myfile = fopen($path, "r") or die("Unable to open file!");
			while(!feof($myfile)) {
				$content_release = $content_release.fgets($myfile);
			}
			fclose($myfile);
		}catch  ( Exception $e ){ }
		
		if($content_release == ""){
			$content_release = 'リリース情報なし！';
			$this->setData('content_release', $content_release);
		}else{
			$content_release = "【 リリース情報 】\n".$content_release;
			$this->setData('content_release', $content_release);
		}

		// Load notification
		$content_notification = "";
		$companyNotification = Data::getNotificationByCompany(User::getCurrentUser()->company_id);
		//$companyNotification = Data::getNotificationByCompany("1001");
		if($companyNotification['content'] != ""){
			$content_notification = $content_notification."【 お知らせ情報 】\n";
			$content_notification = $content_notification."登録者：".$companyNotification['user_id']."（".$companyNotification['name']."）\n";
			$content_notification = $content_notification."登録日：".substr($companyNotification['times'],0,10)."\n";
			$content_notification = $content_notification."内容：\n".$companyNotification['content'];
			$this->setData('content_notification', $content_notification);
		}else{
			$content_notification = 'お知らせなし！';
			$this->setData('content_notification', $content_notification);
		}
		
		// Load History
		$content_history = "";
		$check_history = 0;
		$len = count($array);
		$companyHistory = Data::getHistoryByCompany(User::getCurrentUser()->company_id);
		//$companyHistory = Data::getHistoryByCompany("1010");
		$content_history = $content_history."【 履歴情報 】";
		foreach ($companyHistory as $key => $row) {
			$content_history = $content_history."\n";
            $content_history = $content_history."KPI ID：".$row['kpi_id']."\n";
			$content_history = $content_history."更新者：".$row['action_user_id']."（".$row['action_user_name']."）\n";
			// $content_history = $content_history."更新者：".$row['user_id']."（".$row['name']."）\n";
			$content_history = $content_history."更新内容：".$row['code']."\n";
			$content_history = $content_history."　　".$row['content']."\n";
			$content_history = $content_history."--------------------------------------------------";
			$check_history = 1;
        }
		if($check_history == 0){
			$content_history = '2日分の履歴なし！';
		}
		$this->setData('content_history', $content_history);
		
		// GET CONTENT TO SHOW IN HOME
		$content_view = "";
		$content_view = $content_view . $content_release;
		//$content_view = $content_view . "\n==============================\n";
		$content_view = $content_view . "\n\n";
		$content_view = $content_view . $content_notification;
		$content_view = $content_view . "\n\n";
		$content_view = $content_view . $content_history;
		$this->setData('content_view', $content_view);

		$startMonth = User::getCurrentUser()->start_month;
		
        // load data cua tat ca user
    	$this->setData('data', Data::getHomeData($this->getData('year'),User::getCurrentUser()->company_id, User::getCurrentUser()->dep_id, User::getCurrentUser()->user_id, $this ->getData('search'), User::getCurrentUser()->admin_flag, $startMonth));
        $this->setData('year', $this->getData('year'));
        $goal_company_arr = array();
        $result_company_arr = array();
        // set goal & result cua cong ty trong nam hien tai
        for ($i= $startMonth; $i <= 12 ; $i++) {
            $goal_company_arr[$i-1] = Data::getGoalCompany($this->getData('year').sprintf("%02d",$i),User::getCurrentUser()->company_id,User::getCurrentUser()->dep_id,User::getCurrentUser()->admin_flag);
            $result_company_arr[$i-1] = Data::getResultCompany($this->getData('year').sprintf("%02d",$i),User::getCurrentUser()->company_id,User::getCurrentUser()->dep_id,User::getCurrentUser()->admin_flag);
        }
		for ($i=1; $i < $startMonth; $i++) { 
            $goal_company_arr[$i-1] = Data::getGoalCompany(((int)$this->getData('year')+1).sprintf("%02d",$i),User::getCurrentUser()->company_id,User::getCurrentUser()->dep_id,User::getCurrentUser()->admin_flag);
            $result_company_arr[$i-1] = Data::getResultCompany(((int)$this->getData('year')+1).sprintf("%02d",$i),User::getCurrentUser()->company_id,User::getCurrentUser()->dep_id,User::getCurrentUser()->admin_flag);
        }
        foreach ($goal_company_arr as $k1 => &$v1) {
            if($v1['goal_month']== ""){
                $v1['goal_month'] = 0;
            }
        }
        foreach ($result_company_arr as $k1 => &$v1) {
            if($v1['mark_month']== ""){
                $v1['mark_month'] = 0;
            }
        }
        $this->setData('goal_company',  $goal_company_arr);
        $this->setData('mark_company', array_map(function($e){return $e['mark_month'];}, $result_company_arr));
        $this->render();
    }
	
    // Get danh sach len json
	protected function index_json(){
	    $year = date("Y");
        if (isset($_POST['year'])) {
            $year = $_POST['year'];
        }
        $search = "";
        if (isset($_POST['search'])) {
            $search = $_POST['search'];
        }
        $data = Data::getHomeData($year,User::getCurrentUser()->company_id, User::getCurrentUser()->dep_id, User::getCurrentUser()->user_id, $search, User::getCurrentUser()->admin_flag, User::getCurrentUser()->start_month);
        foreach ($data as $key => &$row) {
            $percent = 0;
            if($row['sokpi'] != 0){
                $percent=($row['thuctich'])*100/($row['sokpi']*100);
            }  
            $row['percent'] = $percent;
        }
        $res = array(
                'success' => true,
                'data_json' => $data,
            );

        echo json_encode($res);
    }
    
    //lay ra data cua user bat ki theo thang
    protected function getKpiMonthByUser_json() {
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
    protected function news_json()
    {
        $message = "";
		/*
        $path = "alarm". DIRECTORY_SEPARATOR .User::getCurrentUser()->company_id.".txt";
        $fp = @fopen($path, "r");
    
        // Kiem tr mo file
        if (!$fp) {
            $message = "お知らせなし！";
        }
        else
        {
            // Doc file va tra ve noi dung
           // $data = fread($fp, filesize($path));
            //echo $data;
            
             // L?p qua t?ng dong ?? ??c
             $i=0;
            while(!feof($fp))
            {
                $message .= fgets($fp);
                $i++;
            }
        } */
		
		// Load notification
		$content_notification = "";
		$companyNotification = Data::getNotificationByCompany(User::getCurrentUser()->company_id);
		if($companyNotification['content'] != ""){
			$content_notification = $content_notification."【 お知らせ情報 】\n";
			$content_notification = $content_notification."登録者：".$companyNotification['user_id']."（".$companyNotification['name']."）\n";
			$content_notification = $content_notification."登録日：".substr($companyNotification['times'],0,10)."\n";
			$content_notification = $content_notification."内容：\n".$companyNotification['content'];
			$message = $content_notification;
		}else{
			$message = 'お知らせなし！';
		}
		$message = $message."\n==============================\n";
		
		// Load History
		$content_history = "";
		$check_history = 0;
		$companyHistory = Data::getHistoryByCompany(User::getCurrentUser()->company_id);
		$content_history = $content_history."【 履歴情報 】";
		foreach ($companyHistory as $key => $row) {
			$content_history = $content_history."\n";
            $content_history = $content_history."KPI ID：".$row['kpi_id']."\n";
			$content_history = $content_history."更新者：".$row['action_user_id']."（".$row['action_user_name']."）\n";
			$content_history = $content_history."更新内容：".$row['code']."\n";
			$content_history = $content_history."　　".$row['content']."\n";
			$content_history = $content_history."--------------------------------------------------";
			$check_history = 1;
        }
		if($check_history == 0){
			$content_history = '2日分の履歴なし！';
		}
		$message = $message.$content_history;
		
        $res = array(
                'success' => true,
                'alarm_message' => $message,
            );

        echo json_encode($res);
    }
	
	
    
}

?>
