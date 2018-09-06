<?php 
class TermsManagerController extends BaseController
{
     //add to the parent constructor
    public function __construct($route, $urlValues) {
        parent::__construct($route, $urlValues);
        
    }

    //bad URL request error
    protected function index()
    {
        $this->setData('success', true);
		
		// Start import Release Info
        if ($_POST['submit'] =='import') {
            if($_FILES['file_import']['tmp_name'] == ""){
                $this->setData('success', false);
                $this->setData('warning_message', getMessageById("104"));
            }
            else{
                $path = "alarm". DIRECTORY_SEPARATOR ."TermsOfService.txt";
                if (!copy($_FILES['file_import']['tmp_name'], $path)) {
                    $this->setData('success', false);
                }
                else{
                    $this->setData('success', true);
					
					// Read file and insert Notification
					$content = "";
					$myfile = fopen($path, "r") or die("Unable to open file!");
					// Output one line until end-of-file
					while(!feof($myfile)) {
						$content = $content.fgets($myfile);
					}
					fclose($myfile);
					$this->setData('content', $content);
                }
            }
        }
		// End import Release Info
		
        // Start Load Release Info
		$path = "alarm". DIRECTORY_SEPARATOR ."TermsOfService.txt";
		$content_terms = "";
		$myfile = fopen($path, "r") or die("Unable to open file!");
		while(!feof($myfile)) {
			$content_terms = $content_terms.fgets($myfile);
		}
		fclose($myfile);
		$this->setData('content_terms', $content_terms);
		// End Load Release Info
		
        $this->render('main');
    }

}

 ?>