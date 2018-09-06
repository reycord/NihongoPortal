<?php

require_once "controllers/kpi.php";



class AgreeController extends KPIController
{

	protected function index()
    {
    	$this->setData('success', true);
		$this->data['message']  = "";
        $check_flag  = FALSE;
		
		$user_id = User::getCurrentUser()->user_id;
		$company_id = User::getCurrentUser()->company_id;
		$this->setData('data_info', Data::getInformationUser($company_id, $user_id));
		$this->setData('admin_flag', $this->data['data_info']['admin_flag']);
    	
    	//xu ly event khi nhan nut 登録	
    	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['btnregis'] == 'regis') {
			//$this->addError("Event button　登録");
			
			
			if (isset($_POST['selectrow'])) {
				$i =0;
				$selectrow = $_POST['selectrow'];
				foreach ($selectrow as $value) {
			        $chk_select = $value['chk_select'];
					$val_leader_comment = trim($value['val_leader_comment']);
					$chk_priority_flag = $value['chk_priority_flag'];
					$chk_accept_flag = $value['chk_accept_flag'];

					//set gia tri  cho field leader_comment de luu xuong datatbase
					if (empty($val_leader_comment)) {
						$val_leader_comment = "";
					}
			
					//set gia tri 0,1 cho field priority_flag de luu xuong datatbase
					if (empty($chk_priority_flag)) {
						$val_priority_flag = 0;
					}
					else {
						$val_priority_flag = 1;
					}
					
					//set gia tri 0,1 cho field accept_flag de luu xuong datatbase
					if (empty($chk_accept_flag)) {
						$val_accept_flag = 0;
					}
					else {
						$val_accept_flag =1;
					}
						//update data table kpi_base_t
					if (!empty($chk_select) ){
						$check_flag = true;
						// ne co chon dong thi kiem tra do lai cua　上司コメント	
						if (strlen($val_leader_comment)>250){
							$this->setData('success', false);
							$this->data['message']  = "上司コメントの項目250桁を超えました";
					   	}else{
							Data::UpdateLeaderComment($chk_select,$val_leader_comment,$val_priority_flag,$val_accept_flag);
						}
					}
					//}
					$i++;
				}
				
				if ($check_flag == false) {
					$this->setData('success', false);
					$this->data['message']  = "まだ選択しませんので、選択してください";
				} else {
					$this->setData('message', 'データを更新しました');	
				}
				
				
				/* xuat gia tri ra man hinh
				?>
				<pre>
					<?php print_r($selectrow) ?>
				</pre>
				<?php
				*/
			}   	

		}
		//Load data len Table select sql.

		$this->setData('data', Data::getListDataAccept($this->getData('year'),User::getCurrentUser()->company_id, User::getCurrentUser()->user_id, $this ->getData('search')));
		$this->render();
    }
    
}

?>