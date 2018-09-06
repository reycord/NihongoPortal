<?php 

require_once 'config/categoryKPI_config.php';

class CategoryKPI{  
    public static function getCategoryKPList()
    {
        // lay danh sach ten category
        $categorys = User::getCategory(User::getCurrentUser()->company_id);
        $g_categoryKPIList = array_map(function($e){return $e['category_name'];}, $categorys);

        return $g_categoryKPIList;
    }
    public static function getListCategoryById()
    {
        // lay danh sach category theo key, ten 
       $categorys = User::getCategory(User::getCurrentUser()->company_id);
            
        foreach ($categorys as $k => $category) {
            $category_arr[$category['category_id']] = $category ['category_name'];
        }
        return $category_arr;
    }
	
	public static function getListCategoryJson()
    {
        $categorys = User::getCategory(User::getCurrentUser()->company_id);
        $category_arr = array();
        foreach ($categorys as $k => $category) {
            $category_arr[] = array("id" => $category['category_id'], "name" => $category ['category_name']);
        }
        return $category_arr;
    }
    
    public static function getPointList()
    {
        global $g_PointList;    
        return $g_PointList;    
    }
    

}
 ?>