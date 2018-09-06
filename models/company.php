<?php 


require_once 'config/lang_config.php';
class Company{
    // lay danh sach ten cong ty	
	public static function getCompanyList()
	{
		global $g_companyList;
		return $g_companyList;	
        
	}
    // lay danh sach nam
	public static function getYearList()
	{
		$year = (int)date("Y");	
		return array($year-2, $year-1, $year, $year +1, $year +2);	
	}
    public static function getMonthList()
    {
        global $g_monthList; 
        return $g_monthList; 
    }
    
    public static function getMessage() {
        global $g_lang; 
        return $g_lang; 
    }
}
 ?>