<?php 
require_once "classes/basecontroller.php";

class KPIController extends BaseController{
	//add to the parent constructor
    public function __construct($route, $urlValues) {
        parent::__construct($route, $urlValues);
		// set year hien tai hoac tu combo box
		$year = date('Y');
		$month = date('m');
		$start_month = User::getCurrentUser()->start_month;
		if($month < $start_month) {
			$year -= 1;
		}
		if (isset($_GET['year'])) {
			$year=$_GET['year'];
		}
		$this->setData('year', $year);
		
		$search = '';
		$user_id = '';
		if (isset($_GET['search'])) {
			$user_id =$_GET['search'];
		}
		$this->setData('search', $user_id);
		
		
		
		//$this->setData('yearList', Data::getYearList());

		$this->setData('yearList', Company::getYearList());
		//$this->setData('idList', User::getUserIDList(User::getCurrentUser()->company_id));
		
    }
    protected function kpi_category_json(){
        $res = array(
                'success' => true,
                'kpi_list' => CategoryKPI::getListCategoryJson(), 
            );

        echo json_encode($res);
    }
}

 ?>