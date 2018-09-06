<?php

require_once "controllers/kpi.php";


class ImportExportController extends KPIController
{

    protected function index()
    {
        // set data de import, export
    	$this->setData('data', Data::getHomeData($this->getData('year'),User::getCurrentUser()->company_id, User::getCurrentUser()->dep_id, User::getCurrentUser()->user_id, $this ->getData('search'), User::getCurrentUser()->admin_flag, User::getCurrentUser()->start_month));
        
        // leader nhan button export
		if ($_POST['submit'] =='export_leader') {
			$excel = new Excel(TemplateFileName_leader);
			$excel->export_leader(User::getCurrentUser()->company_id, User::getCurrentUser()->user_id, $this->getData('year'), $this ->getData('search'), User::getCurrentUser()->start_month);
			
			$outFileName = User::getCurrentUser()->user_id ."_leader". ".xlsx";
				
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.$outFileName.'"');
			$excel->writeFile();	
			exit();	
		}
        // user nhan button export
        elseif ($_POST['submit'] =='export_user') {
            $excel = new Excel(TemplateFileName_user);
            $excel->export_user(User::getCurrentUser()->company_id, User::getCurrentUser()->user_id, $this->getData('year'), $this ->getData('search'), User::getCurrentUser()->start_month);
            
            
            $outFileName = User::getCurrentUser()->user_id ."_user". ".xlsx";
                
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$outFileName.'"');
            $excel->writeFile();    
            exit(); 
        }
        // user nhan button import
        elseif ($_POST['submit'] =='import') {
            if($_FILES['file_import']['tmp_name'] == ""){
                $this->setData('success', false);
                $this->setData('warning_message', getMessageById("104"));
            }
            else{
                $excel = new Excel($_FILES['file_import']['tmp_name']);
                try{
                    $result_import = $excel->import();
                    $this->setData('result_import', $result_import);
                    if ($this->data['result_import'][success_kpi] == 0){
                        $this->setData('success', false);
                        $this->setData('message', $this->data['result_import'][message]);
                    }else{
                        
                        $this->setData('message', getMessageById("005",$this->data['result_import'][success_kpi]
                                                            ,$this->data['result_import'][error_kpi]
                                                            ,$this->data['result_import'][error_user]));
                    
                    
                    }
                }
                catch(Exception $e){
                    $this->setData('success', false);
                    $this->setData('message', getMessageById("203"));
                }
                
                
            }
            
        }
        
		$this->render();
	}
}
?>
