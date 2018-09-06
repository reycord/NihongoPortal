<?php 


/**
* Data
*/
class DataMaintenance
{
    // insert company
    public static function insertCompany($company_id, $company_name)
    {
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING); 
        $company_name = DbAgent::queryEncode($company_name,DbAgent::$DB_STRING); 
                 
        $query = "INSERT INTO company_t(company_id, company_name, del_flag)
                VALUES ($company_id, $company_name, 0);";      
        Database::currentDb()->execute($query);
    }
	
	// insert company to config_t
	public static function insertCompanyToConfig($company_id)
	{
		// START_MONTH was defined in 'excel.php'
		$start_month = 4;
		$company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
		
		$query = "INSERT INTO config_t(company_id, start_month)
				VALUES ($company_id, $start_month);";
		Database::currentDb()->execute($query);
	}
	
    // update company
    public static function updateCompany($company_id, $company_name)
    {
         
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING); 
        $company_name = DbAgent::queryEncode($company_name,DbAgent::$DB_STRING);
                 
        $query = "UPDATE company_t
                SET company_name = $company_name                    
                WHERE company_id= $company_id
                      and del_flag = 0;";      
        Database::currentDb()->execute($query);
    }
    // get data from company_t  
    public static function getCompanyList()
    {
        
        $query = "SELECT *
                FROM   company_t
                WhERE  del_flag = 0
                ORDER BY company_id;";
                                              
        $records = Database::currentDb()->getMultiRecord($query, $t);       
        return $records;
    }
    
    // Lay danh sach message
    public static function getMessageList()
    {
        
        $query = "SELECT id, send_time, company_id, user_id, code, terminal, function, content
                FROM   message_t
                WhERE  del_flag = 0
                ORDER BY id DESC;";
                                              
        $records = Database::currentDb()->getMultiRecord($query, $t);       
        return $records;
    }
    
    // delete company
    public static function deleteCompany($company_id)
    {
         
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING); 
                 
        $query = "UPDATE company_t
                SET del_flag = 1                    
                WHERE company_id= $company_id;";     
        Database::currentDb()->execute($query);
    }
    // delete company
    public static function deleteMessage($message_id)
    {
         
        $message_id = DbAgent::queryEncode($message_id,DbAgent::$DB_STRING); 
                 
        $query = "UPDATE message_t
                SET del_flag = 1                    
                WHERE id= $message_id;";     
        Database::currentDb()->execute($query);
    }
     // insert department
    public static function insertDepartment($company_id,$department_id, $department_name)
    {
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
        $department_id = DbAgent::queryEncode($department_id,DbAgent::$DB_STRING); 
        $department_name = DbAgent::queryEncode($department_name,DbAgent::$DB_STRING); 
                 
        $query = "INSERT INTO department_t (company_id, dep_id, dep_name, del_flag)
                VALUES ($company_id, $department_id, $department_name, 0);";      
        Database::currentDb()->execute($query);
    }
    // update department
    public static function updateDepartment($company_id, $department_id, $department_name)
    {
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING); 
        $department_id = DbAgent::queryEncode($department_id,DbAgent::$DB_STRING); 
        $department_name = DbAgent::queryEncode($department_name,DbAgent::$DB_STRING);
                 
        $query = "UPDATE department_t
                SET dep_name = $department_name                    
                WHERE dep_id= $department_id
                      and del_flag = 0
                      and company_id = $company_id;";      
        Database::currentDb()->execute($query);
    }
    // get data from department_t  
    public static function getDepartmentList($company_id)
    {
        $company_id_encode = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
        $query = "SELECT *
                FROM   department_t
                inner join company_t
                on department_t.company_id = company_t.company_id
                and company_t.del_flag = 0
                WhERE  department_t.del_flag = 0";
        if($company_id != ""){
            $query .= " and department_t.company_id = $company_id_encode";
        }       
        $query .= " ORDER BY department_t.company_id,department_t.dep_id;";                            
        $records = Database::currentDb()->getMultiRecord($query, $t);       
        return $records;
    }
    
    // get data from department_t by Manager
    public static function getDepartmentListByManager($company_id, $dep_id)
    {
        $company_id_encode = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
        $dep_id_encode = DbAgent::queryEncode($dep_id,DbAgent::$DB_STRING);
        $query = "SELECT *
                FROM   department_t
                inner join company_t
                on department_t.company_id = company_t.company_id
                and company_t.del_flag = 0
                WhERE  department_t.del_flag = 0
                and department_t.company_id = $company_id_encode
                and department_t.dep_id = $dep_id_encode";      
        $query .= " ORDER BY department_t.company_id,department_t.dep_id;";                            
        $records = Database::currentDb()->getMultiRecord($query, $t);       
        return $records;
    }
    
    // delete department
    public static function deleteDepartment($company_id, $department_id)
    {
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING); 
        $department_id = DbAgent::queryEncode($department_id,DbAgent::$DB_STRING); 
                 
        $query = "UPDATE department_t
                SET del_flag = 1                    
                WHERE dep_id= $department_id
                and company_id = $company_id;";     
        Database::currentDb()->execute($query);
    }
      // insert user
    public static function insertUser($name, $leader_id, $level, $password, $department_id, 
                                    $admin_flag, $company_id, $user_id)
    {
        $name = DbAgent::queryEncode($name,DbAgent::$DB_STRING);
        $leader_id = DbAgent::queryEncode($leader_id,DbAgent::$DB_STRING);
        $level = DbAgent::queryEncode($level,DbAgent::$DB_NUMBER);
        $password = DbAgent::queryEncode($password,DbAgent::$DB_STRING);
        $department_id = DbAgent::queryEncode($department_id,DbAgent::$DB_STRING);
        $admin_flag = DbAgent::queryEncode($admin_flag,DbAgent::$DB_NUMBER);
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
        $user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
                 
        $query = "INSERT INTO user_t (name, leader_id, level, password, dep_id, 
                                    admin_flag, del_flag, company_id, user_id, maintenance_flag)
                VALUES ($name, $leader_id, $level, $password, $department_id, 
                                    $admin_flag, 0, $company_id, $user_id, 0);";      
        Database::currentDb()->execute($query);
    }
    // update user
    public static function updateUser($name, $leader_id, $level, $password, $department_id, 
                                    $admin_flag, $company_id, $user_id)
    {
        $name = DbAgent::queryEncode($name,DbAgent::$DB_STRING);
        $leader_id = DbAgent::queryEncode($leader_id,DbAgent::$DB_STRING);
        $level = DbAgent::queryEncode($level,DbAgent::$DB_NUMBER);
        $password = DbAgent::queryEncode($password,DbAgent::$DB_STRING);
        $department_id = DbAgent::queryEncode($department_id,DbAgent::$DB_STRING);
        $admin_flag = DbAgent::queryEncode($admin_flag,DbAgent::$DB_NUMBER);
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
        $user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
                 
        $query = "UPDATE user_t
                SET name = $name,
                    leader_id = $leader_id,
                    level = $level,
                    password = $password,
                    dep_id = $department_id, 
                    admin_flag = $admin_flag                
                WHERE company_id = $company_id
                      and user_id = $user_id
                      and del_flag = 0;";     
        Database::currentDb()->execute($query);
    }
    // get data from user_t  
    public static function getUserList($company_id)
    {
        $company_id_encode = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
        
        $query = "SELECT user_t.*, department_t.dep_name
                FROM   user_t
                inner join company_t
                on user_t.company_id = company_t.company_id
                and company_t.del_flag = 0
                inner join department_t
                on user_t.company_id = department_t.company_id
                and department_t.del_flag = 0
                and user_t.dep_id = department_t.dep_id
                WhERE  user_t.del_flag = 0";
        if($company_id != ""){
            $query .= " and user_t.company_id = $company_id_encode";
        }       
        $query .= " ORDER BY user_t.company_id,user_t.user_id;";
                                       
        $records = Database::currentDb()->getMultiRecord($query, $t);       
        return $records;
    }
	
	// get data from user_t by Manager
    public static function getUserListByManager($company_id, $dep_id)
    {
        $company_id_encode = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
        $dep_id_encode = DbAgent::queryEncode($dep_id,DbAgent::$DB_STRING);
        
        $query = "SELECT user_t.*, department_t.dep_name
                FROM   user_t
                inner join company_t
                on user_t.company_id = company_t.company_id
                and company_t.del_flag = 0
                inner join department_t
                on user_t.company_id = department_t.company_id
                and department_t.del_flag = 0
                and user_t.dep_id = department_t.dep_id
                WhERE  user_t.del_flag = 0
				and user_t.company_id = $company_id_encode
				and user_t.dep_id = $dep_id_encode";
             
        $query .= " ORDER BY user_t.company_id,user_t.user_id;";
                                       
        $records = Database::currentDb()->getMultiRecord($query, $t);       
        return $records;
    }
	
	// get data from user_t  
    public static function getUserInfo($company_id, $dep_id, $user_id)
    {
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
        $dep_id = DbAgent::queryEncode($dep_id,DbAgent::$DB_STRING);
        $user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
        
        $query = "SELECT user_t.*
                FROM   user_t
                inner join company_t
                on user_t.company_id = company_t.company_id
                and company_t.del_flag = 0
                inner join department_t
                on user_t.company_id = department_t.company_id
                and department_t.del_flag = 0
                and user_t.dep_id = department_t.dep_id
                WhERE  user_t.del_flag = 0
                and user_t.company_id = $company_id
                and user_t.dep_id = $dep_id
                and user_t.user_id = $user_id";
                                       
		$record = Database::currentDb()->getRecord($query);
        return $record;
    }
    
    // delete user
    public static function deleteUser($company_id, $user_id)
    {
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING); 
        $user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING); 
                 
        $query = "UPDATE user_t
                SET del_flag = 1                    
                WHERE user_id= $user_id
                and company_id = $company_id;";     
        Database::currentDb()->execute($query);
    }
    
    // insert category
    public static function insertCategory($category_id, $category_name)
    {
        
        $category_id = DbAgent::queryEncode($category_id,DbAgent::$DB_STRING); 
        $category_name = DbAgent::queryEncode($category_name,DbAgent::$DB_STRING); 
                 
        $query = "INSERT INTO category_t (category_id, category_name, del_flag)
                VALUES ($category_id, $category_name, 0);";      
        Database::currentDb()->execute($query);
    }
    // update category
    public static function updateCategory($category_id, $category_name)
    {
       
        $category_id = DbAgent::queryEncode($category_id,DbAgent::$DB_STRING); 
        $category_name = DbAgent::queryEncode($category_name,DbAgent::$DB_STRING);
                 
        $query = "UPDATE category_t
                SET category_name = $category_name                    
                WHERE category_id= $category_id
                      and del_flag = 0;";      
        Database::currentDb()->execute($query);
    }
    // get data from category_t  
    public static function getCategoryList()
    {
        // $company_id_encode = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
        $query = "SELECT *
                FROM   category_t
                WhERE  category_t.del_flag = 0";
        // if($company_id != ""){
            // $query .= " and category_t.company_id = $company_id_encode";
        // }       
        $query .= " ORDER BY category_t.company_id,category_t.category_id;";
                                                      
        $records = Database::currentDb()->getMultiRecord($query, $t);       
        return $records;
    }
    
    // delete category
    public static function deleteCategory($category_id)
    {
        // $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING); 
        $category_id = DbAgent::queryEncode($category_id,DbAgent::$DB_STRING); 
                 
        $query = "UPDATE category_t
                SET del_flag = 1                    
                WHERE category_id= $category_id;";     
        Database::currentDb()->execute($query);
    }
    // check company_id
    public static function checkNewCompanyId($company_id){
        
        $company_id = DbAgent::queryEncode($company_id, DbAgent::$DB_STRING);
               
        $sql = "select count(*) from company_t where company_id=$company_id;";
        
        $count = Database::currentDb()->get($sql, 0);
        
        return $count == 0;
    }
    // check department_id
    
    public static function checkNewDepartmentId($company_id, $department_id){
        
        $company_id = DbAgent::queryEncode($company_id, DbAgent::$DB_STRING);
        $department_id = DbAgent::queryEncode($department_id, DbAgent::$DB_STRING);       
        $sql = "select count(*) from department_t where company_id = $company_id and dep_id = $department_id;";
        
        $count = Database::currentDb()->get($sql, 0);
        
        return $count == 0;
    }
     // check user_id
    
    public static function checkNewUserId($company_id, $user_id){
        
        $company_id = DbAgent::queryEncode($company_id, DbAgent::$DB_STRING);
        $user_id = DbAgent::queryEncode($user_id, DbAgent::$DB_STRING);       
        $sql = "select count(*) from user_t where company_id = $company_id and user_id = $user_id;";
        
        $count = Database::currentDb()->get($sql, 0);
        
        return $count == 0;
    }
     // check category
    
    public static function checkNewCategoryId($category_id){
        
        $category_id = DbAgent::queryEncode($category_id, DbAgent::$DB_STRING);       
        $sql = "select count(*) from category_t where category_id = $category_id;";
        
        $count = Database::currentDb()->get($sql, 0);
        
        return $count == 0;
    }
    
}


 ?>