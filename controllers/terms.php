<?php

require_once "controllers/kpi.php";

class TermsController extends KPIController {

    protected function index() {
    	$this->setData('success', true);
		
		// Load Release Info
		$path = "alarm". DIRECTORY_SEPARATOR ."TermsOfService.txt";
		$content_terms = "";
		try{
			if (!file_exists($path) ) {
		    	throw new Exception('File not found.');
		    }
			$myfile = fopen($path, "r") or die("Unable to open file!");
			while(!feof($myfile)) {
				$content_terms = $content_terms.fgets($myfile);
			}
			fclose($myfile);
			
		}catch  ( Exception $e ){ }
		
		if($content_terms == ""){
			$content_terms = '利用規約なし！';
			$this->setData('content_terms', $content_terms);
		}else{
			$this->setData('content_terms', $content_terms);
		}
        
        $this -> render('blank');
    }
	
	protected function getTerms_json()
    {
        // Load Terms Info
		$path = "alarm". DIRECTORY_SEPARATOR ."TermsOfService.txt";
		$content_terms = "";
		try{
			if (!file_exists($path) ) {
		    	throw new Exception('File not found.');
		    }
			$myfile = fopen($path, "r") or die("Unable to open file!");
			while(!feof($myfile)) {
				$content_terms = $content_terms.fgets($myfile);
			}
			fclose($myfile);
			
		}catch  ( Exception $e ){ }
		
		if($content_terms == ""){
			$content_terms = '利用規約なし！';
		}
		
        $res = array(
                'success' => true,
                'terms_info' => $content_terms,
            );

        echo json_encode($res);
    }

}
?>
