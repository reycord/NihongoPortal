<?php 

/**
* User
*/
class User implements JsonSerializable
{
    const SESSION_USER_ID_KEY ="user_id";
	const SESSION_COMPANY_ID_KEY ="company_id";

    /**
     * varchar $user_id
     * @var int $user_id
     */
    public $user_id;
	/**
     * int
     * @var varchar $company_id
     */
    public $company_id;	/**
     * int
     * @var varchar $company_name
     */
    public $company_name;
    /**
     * int
     * @var int $dep_id
     */
    public $dep_id;
	    /**
     * varchar
     * @var varchar $dep_name
     */
    public $dep_name;
    /**
     * name varchar(50)
     * @var string $name
     */
    public $name;
    
    /**
     * leader_name varchar(50)
     * @var string $leader_name
     */
    public $leader_name;
    /**
     * int
     * @var int $leader_id
     */
    public $leader_id;
    /**
     * admin_flag int
     * @var int
     */
    public $admin_flag ;
    /**
     * maintenance_flag
     * @var boolean
     */
    public $maintenance_flag = false;

	  /**
     * int
     * @var int $level
     */
    public $level;
	  /**
     * password varchar(50)
     * @var string $password
     */
    public $password;
	 /**
     * del_flag boolean
     * @var boolean
     */
    public $del_flag = 1;
	 /**
     * int
     * @var int $year
     */
    public $year;
	/**
     * int
     * @var int $start_month
     */
    public $start_month;
	
    
    function __construct($data) {
        if (isset($data['user_id'])) {
            $this->user_id = $data['user_id'];
        }
        
		if (isset($data['company_id'])) {
            $this->company_id = $data['company_id'];
        }
		if (isset($data['company_name'])) {
            $this->company_name = $data['company_name'];
        }
        if (isset($data['dep_id'])) {
            $this->dep_id = $data['dep_id'];
        }
		if (isset($data['dep_name'])) {
            $this->dep_name = $data['dep_name'];
        }
        
        if (isset($data['name'])) {
            $this->name = $data['name'];
        }
		if (isset($data['leader_id'])) {
            $this->leader_id = $data['leader_id'];
        }
        if (isset($data['admin_flag'])) {
            //$this->admin_flag = $data['admin_flag'] == 1 ? true : false;
			$this->admin_flag = $data['admin_flag'];
        }
        if (isset($data['maintenance_flag'])) {
            $this->maintenance_flag = $data['maintenance_flag'] == 1 ? true : false;
        }
        if (isset($data['level'])) {
            $this->level = $data['level'];
        }
		if (isset($data['password'])) {
            $this->password = $data['password'];
        }
		if (isset($data['del_flag'])) {
            $this->del_flag = $data['del_flag'] == 't' ? true : false;
        }
		if (isset($data['year'])) {
            $this->year = $data['year'];
        }	
        if (isset($data['start_month'])) {
            $this->start_month = $data['start_month'];
        }	
    }
    public function isLeader(){
        return count($this->getListUserByLeader()) > 1;
    }
    
    public function checkIsLeader($user_leader, $user_member, $company_id){
        $res = array();
        
        User::getAllCapduoi($res, $user_leader, $company_id);

        if(in_array($user_member, $res)){
           return true;
        }
        return false;
    }

    public function JsonSerialize(){
        
        $leader = User::getByID($this->leader_id, $this->company_id);
    
        return array( 
                    "user_id" => $this->user_id,
                    "company_id"=> $this->company_id,
                    "company_name"=> $this->company_name,
                    "dep_id" => $this->dep_id,
                    "dep_name"=> $this->dep_name,
                    "name"=> $this->name,
                    "leader_id"=> $this->leader_id,
                    "admin_flag"=> $this->admin_flag,
                    "maintenance_flag"=> $this->maintenance_flag,
                    "level"=> $this->level,
                    "password"=> $this->password,
                    "del_flag"=> $this->del_flag,
                    "isLeader" => $this->isLeader(),
                    "leader" => array(
                                "user_id" => $leader->user_id,
                                "name"=> $leader->name),
                    "year"=> $this->year,
					"start_month"=> $this->start_month);  
    }

    // lay danh sach user cua leader
    public function getListUserByLeader(){
        
        $res = array();
        
        User::getAllCapduoi($res, $this->user_id, $this->company_id);

        if(!in_array($this->user_id, $res)){
           $res[] = $this->user_id; 
        }
        
        $user_list = array();
        
                
        sort($res);
        
        foreach ($res as $key => $u) {
            $user_list[] = User::getByID($u, $this->company_id);
        }
    
        return $user_list;
    }

    /**
     * select from database where id=$id
     * @param  string $user_id user id
     * @return User     return null if not found
     */
	public static function getByID($user_id, $company_id){
	    $user_id = DbAgent::queryEncode($user_id, DbAgent::$DB_STRING);
        $company_id = DbAgent::queryEncode($company_id, DbAgent::$DB_STRING);

        $sql = 
            "SELECT 
            user_t.name, user_t.leader_id, user_t.level, user_t.password, user_t.maintenance_flag,
            user_t.dep_id, user_t.admin_flag, user_t.del_flag, user_t.dep_id,
            user_t.company_id, user_t.user_id, company_t.company_name, config_t.start_month
            FROM config_t, user_t
            left join company_t
            on  company_t.company_id = user_t.company_id
            and company_t.del_flag = 0
            WHERE user_id= $user_id
            and user_t.company_id= $company_id
            and config_t.company_id= $company_id
            and user_t.del_flag = 0";
        $record = Database::currentDb()->getRecord($sql);

        if ($record != null) {
            return new User($record);
        }

        return null;
    }
	
	
     public static function getCategory($company_id){
        
        $company_id = DbAgent::queryEncode($company_id, DbAgent::$DB_STRING);
        //company_id=$company_id
        $sql ="SELECT  *
            FROM category_t 
            WHERE del_flag = 0";
        $record = Database::currentDb()->getMultiRecord($sql, $t);

        return $record;
    }
  
    
    public static function getByIDAndPassword($user_id, $password, $company_id){
        $user_id = DbAgent::queryEncode($user_id, DbAgent::$DB_STRING);
		$password= DbAgent::queryEncode($password, DbAgent::$DB_STRING);
		$company_id = DbAgent::queryEncode($company_id, DbAgent::$DB_STRING);
        $sql = 
            "SELECT 
                 user_t.company_id
                ,user_t.user_id
                ,user_t.name
                ,user_t.leader_id
                ,user_t.level
                ,user_t.dep_id
                ,user_t.password
                ,user_t.dep_id
                ,user_t.admin_flag
                ,user_t.del_flag
                ,user_t.maintenance_flag
                ,department_t.dep_name
                ,department_t.del_flag
                ,company_t.company_name
                ,company_t.del_flag
				,config_t.start_month
            FROM user_t
			LEFT JOIN config_t
                on  config_t.company_id = user_t.company_id
            LEFT JOIN company_t
                on  company_t.company_id = user_t.company_id
            LEFT JOIN department_t
                on  department_t.company_id = user_t.company_id
                and department_t.dep_id     = user_t.dep_id
            WHERE user_t.user_id= $user_id
                and user_t.password = $password
                and user_t.company_id = $company_id
                and company_t.del_flag = 0
                and department_t.del_flag = 0
                and user_t.del_flag = 0;";

        $record = Database::currentDb()->getRecord($sql);
		
        if ($record != null) {
            /*select them Company Name tai day de Android su dung
            Update date: 2015/12/29*/
            $record['year'] = Company::getYearList();
            
            return new User($record);
        }

        return null;
        
    }
    // lay danh sach user theo Admin_Flag = 1
    public static function getUserIDListByAdmin($company_id)
	{
		$company_id = DbAgent::queryEncode($company_id, DbAgent::$DB_STRING);
		$query = "SELECT user_t.user_id, user_t.name, user_t.leader_id, user_t.dep_id, user_t.company_id
		FROM user_t, company_t, department_t
		where user_t.company_id = $company_id
        and company_t.del_flag = 0
        and department_t.del_flag = 0
        and user_t.del_flag = 0
		and company_t.company_id = user_t.company_id 
        and department_t.company_id = user_t.company_id
        and department_t.dep_id = user_t.dep_id
		order by user_id;";	  
		$record = Database::currentDb()->getMultiRecord($query, $t);		
		return $record;
	}
	
	// lay danh sach user theo Admin_Flag = 2
    public static function getUserIDListByManager($company_id, $dep_id)
	{
		$company_id = DbAgent::queryEncode($company_id, DbAgent::$DB_STRING);
		$dep_id = DbAgent::queryEncode($dep_id, DbAgent::$DB_STRING);
		$query = "SELECT user_t.user_id, user_t.name, user_t.leader_id, user_t.dep_id, user_t.company_id
		FROM user_t, company_t, department_t
		where user_t.company_id = $company_id
		and user_t.dep_id = $dep_id
        and company_t.del_flag = 0
        and department_t.del_flag = 0
        and user_t.del_flag = 0
		and company_t.company_id = user_t.company_id 
        and department_t.company_id = user_t.company_id
        and department_t.dep_id = user_t.dep_id
		order by user_id;";	  
		$record = Database::currentDb()->getMultiRecord($query, $t);		
		return $record;
	}

    // edit Password
    public static function editPassword($company_id, $user_id, $password)
    {
        $company_id = DbAgent::queryEncode($company_id, DbAgent::$DB_STRING);
        $user_id = DbAgent::queryEncode($user_id, DbAgent::$DB_STRING);
        $password = DbAgent::queryEncode($password, DbAgent::$DB_STRING);
        $query = "UPDATE user_t
                SET password = $password                     
                WHERE user_t.company_id = $company_id
                      and user_t.user_id = $user_id;";     
        Database::currentDb()->execute($query); 
    }
    /**
     * store current User
     * @var User
     */
    private static $_currentUser = null;

    /**
     * get current user.
     * @return User return null if not yet login
     */
    public static function getCurrentUser(){

        if (self::$_currentUser == null 
        	&& isset($_SESSION[self::SESSION_USER_ID_KEY]) 
        	&& isset($_SESSION[self::SESSION_COMPANY_ID_KEY])) {
        		
            self::$_currentUser = self::getByID($_SESSION[self::SESSION_USER_ID_KEY], $_SESSION[self::SESSION_COMPANY_ID_KEY]);
            
        }
        
        return self::$_currentUser;
    }

    /**
     * login with $login
     * @param string $user_id
	 * @param string $password
     * @return boolean        true if success. false if error
     */
    public static function logIn($user_id, $password, $company_id){

        unset($_SESSION[self::SESSION_USER_ID_KEY]);
		unset($_SESSION[self::SESSION_COMPANY_ID_KEY]);

        self::$_currentUser = self::getByIDAndPassword($user_id, $password, $company_id);


        if (self::$_currentUser != null) {

            $_SESSION[self::SESSION_USER_ID_KEY] = self::$_currentUser->user_id;
			$_SESSION[self::SESSION_COMPANY_ID_KEY] = self::$_currentUser->company_id;
            return true;
        }
		
        return false;
    }

    /**
     * logout : unset session
     * @return [type] [description]
     */
    public static function logOut(){
        self::$_currentUser = null;
        unset($_SESSION[self::SESSION_USER_ID_KEY]);
		unset($_SESSION[self::SESSION_COMPANY_ID_KEY]);
    }

    /**
     * count all user
     * @return int [description]
     */
    public static function countUser(){
        $sql = "SELECT count(*) FROM user_t";
        $record = Database::currentDb()->getRecord($sql);

        if ($record != null) {
            return $record["count"];
        }

        return 0;
    }

    /**
     * count all user
     * @return int [description]
     */
    public static function countNotAdminUser(){
        $sql = "SELECT count(*) FROM user_t WHERE admin_flag = 0";
        $record = Database::currentDb()->getRecord($sql);

        if ($record != null) {
            return $record["count"];
        }

        return 0;
    }
    
    
    public static function getAllCapduoi(&$res, $user_id, $company_id){
        $company_id_encode = DbAgent::queryEncode($company_id, DbAgent::$DB_STRING);
        
        if (!is_array($user_id)) {
              $user_id = array($user_id);
        }
        
        if(count($user_id) == 0){
            return;
        }
        
        $user_id_map = array_map(function($ele){return DbAgent::queryEncode($ele, DbAgent::$DB_STRING);}, $user_id);
        $res_map = array_map(function($ele){return DbAgent::queryEncode($ele, DbAgent::$DB_STRING);}, $res);
        
        $values = implode(",", $user_id_map);
        $ress = implode(",", $res_map);
        
        $sql = "SELECT user_id 
                from user_t 
                left join company_t
                on user_t.company_id = company_t.company_id
                and company_t.del_flag = 0
                left join department_t
                on user_t.company_id = department_t.company_id
                and user_t.dep_id = department_t.dep_id
                and department_t.del_flag = 0
                where user_t.company_id=$company_id_encode
                and user_t.del_flag = 0 ";
        if ($ress != "") {
            $sql .= " and user_t.user_id not in($ress)";
        }
        if ($values != "") {
            $sql .= " and user_t.leader_id in($values)";
        }

        $records = Database::currentDb()->getMultiRecord($sql, $t);
        $user_arr = array_map(function($ele){return $ele['user_id'];}, $records);
        
        foreach ($user_arr as $key => $value) {
            $res[] = $value;
        }
        
        self::getAllCapduoi($res, $user_arr, $company_id);
    }

}


 ?>