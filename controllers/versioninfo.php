<?php

require_once "controllers/kpi.php";

class VersionInfoController extends KPIController {

    protected function index() {
    	$this->setData('success', true);
		
		// Load Release Info Web
		$path = "alarm". DIRECTORY_SEPARATOR ."ReleaseInfo_Web.txt";
		$content_release = "";
		try{
			if (!file_exists($path) ) {
		    	throw new Exception('File not found.');
		    }
			$myfile = fopen($path, "r") or die("Unable to open file!");
			
			// while(!feof($myfile)) {
			// 		$content_release = $content_release.fgets($myfile);
			// }
			
			$firstline = fgets($myfile);
			$content_release = $firstline;

			fclose($myfile);
		}catch  ( Exception $e ){ }
		
		if($content_release == ""){
			$content_release = 'リリース情報なし！';
			$this->setData('content_release', $content_release);
		}else{
			$this->setData('content_release', $content_release);
		}
        
        $this -> render('blank');
    }
	
	protected function getInfo_json()
    {
        // Load Release Info Android
		$path = "alarm". DIRECTORY_SEPARATOR ."ReleaseInfo_Android.txt";
		$content_release_android = "";
		try{
			if (!file_exists($path) ) {
		    	throw new Exception('File not found.');
		    }
			$myfile = fopen($path, "r") or die("Unable to open file!");

			while(!feof($myfile)) {
			 		$content_release_android = $content_release_android.fgets($myfile);
			}

			fclose($myfile);
		}catch  ( Exception $e ){ }
		
		if($content_release_android == ""){
			$content_release_android = 'リリース情報なし！';
		}
		
		// Load Release Info iOS
		$path = "alarm". DIRECTORY_SEPARATOR ."ReleaseInfo_iOS.txt";
		$content_release_ios = "";
		try{
			if (!file_exists($path) ) {
		    	throw new Exception('File not found.');
		    }
			$myfile = fopen($path, "r") or die("Unable to open file!");

			while(!feof($myfile)) {
			 		$content_release_ios = $content_release_ios.fgets($myfile);
			}

			fclose($myfile);
		}catch  ( Exception $e ){ }
		
		if($content_release_ios == ""){
			$content_release_ios = 'リリース情報なし！';
		}
		
        $res = array(
                'success' => true,
                'version_info_android' => $content_release_android,
                'version_info_ios' => $content_release_ios,
            );

        echo json_encode($res);
    }

}
?>
