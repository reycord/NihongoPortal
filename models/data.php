<?php 

/**
* Data
*/
class Data
{
    // lay muc tieu tung thang cua cong ty
	public static function getGoalCompany($month,$company_id,$dep_id,$admin_flag)
    {
        $month = DbAgent::queryEncode($month,DbAgent::$DB_NUMBER);
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
        $dep_id = DbAgent::queryEncode($dep_id,DbAgent::$DB_STRING);
        $admin_flag = DbAgent::queryEncode($admin_flag,DbAgent::$DB_NUMBER);
               
        $query = "SELECT SUM(kpi_manager_t.mark_month) as goal_month
            FROM kpi_manager_t, kpi_base_t,
                 company_t, department_t, user_t
            WHERE 
            company_t.company_id = $company_id
            ";
        if($admin_flag != 1){
        	$query = $query . "and department_t.dep_id = $dep_id";
        }  
        $query = $query . "
            and kpi_manager_t.month = $month
            and kpi_manager_t.del_flag = 0
            and kpi_base_t.del_flag = 0
            and company_t.del_flag = 0
            and department_t.del_flag = 0
            and user_t.del_flag = 0
            and kpi_manager_t.kpi_id = kpi_base_t.kpi_id
            and user_t.user_id = kpi_base_t.user_id
            and user_t.dep_id = kpi_base_t.dep_id
            and user_t.company_id = kpi_base_t.company_id
            and company_t.company_id = user_t.company_id
            and department_t.company_id = user_t.company_id
            and department_t.dep_id = user_t.dep_id";     
        $record = Database::currentDb()->getRecord($query);
        return $record;
    } 
    // lay ket qua tung thang cua cong ty
    public static function getResultCompany($month,$company_id,$dep_id,$admin_flag)
    {
        $month = DbAgent::queryEncode($month,DbAgent::$DB_NUMBER);
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
        $dep_id = DbAgent::queryEncode($dep_id,DbAgent::$DB_STRING);
        $admin_flag = DbAgent::queryEncode($admin_flag,DbAgent::$DB_NUMBER);
        
        $query = "SELECT SUM(kpi_result_t.mark) as mark_month
            FROM kpi_result_t, kpi_manager_t, kpi_base_t, 
                 company_t, department_t, user_t
            WHERE 
            company_t.company_id = $company_id
            ";
        if($admin_flag != 1){
        	$query = $query . " and department_t.dep_id = $dep_id ";
        }  
        $query = $query . "
            and kpi_result_t.month = $month
            and kpi_manager_t.del_flag = 0
            and kpi_result_t.del_flag = 0
            and kpi_base_t.del_flag = 0
            and company_t.del_flag = 0
            and department_t.del_flag = 0
            and user_t.del_flag = 0
            and kpi_manager_t.kpi_id = kpi_base_t.kpi_id
            and kpi_result_t.month = kpi_manager_t.month
            and kpi_result_t.kpi_id = kpi_base_t.kpi_id
            and user_t.user_id = kpi_base_t.user_id
            and user_t.dep_id = kpi_base_t.dep_id
            and user_t.company_id = kpi_base_t.company_id
            and company_t.company_id = user_t.company_id
            and department_t.company_id = user_t.company_id
            and department_t.dep_id = user_t.dep_id";     
        $record = Database::currentDb()->getRecord($query);
        return $record;
    } 
    
    // lay user id, user name, level, so kpi da dang ky, so kpi dang thuc hien
	public static function getHomeData($year, $company_id, $dep_id, $user_id, $search, $admin_flag, $startMonth)
	{
		$year = DbAgent::queryEncode($year,DbAgent::$DB_NUMBER);
		$startMonth = DbAgent::queryEncode($startMonth,DbAgent::$DB_NUMBER);
		$company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
		$dep_id = DbAgent::queryEncode($dep_id,DbAgent::$DB_STRING);
		$user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
		$user_name = DbAgent::queryEncode($search,DbAgent::$DB_STRING);
		$today = DbAgent::queryEncode(date("Y-m-d"),DbAgent::$DB_STRING);
	    $search = DbAgent::queryEncode($search,DbAgent::$DB_STRING);
		$admin_flag = DbAgent::queryEncode($admin_flag,DbAgent::$DB_NUMBER);
		
		$query = "select user_t.user_id, user_t.name, user_t.level, user_t.admin_flag 
                       ,sum(case when kpi_base_t.kpi_id is not null then 1 else 0 end) as sokpi
                       ,sum(case when tmp.sum_mark > 0 then 1 else 0 end) as kpidangthuchien
                       ,sum(case when kpi_base_t.goal > 0 then 
                               case when tmp.sum_mark*100/kpi_base_t.goal >= 100 
                               then 100 
                               else tmp.sum_mark*100/kpi_base_t.goal end
                            else 0 end) as thuctich
                from user_t
                left join kpi_base_t
                on user_t.company_id = kpi_base_t.company_id
                and user_t.user_id = kpi_base_t.user_id
                and user_t.dep_id = kpi_base_t.dep_id
                and kpi_base_t.del_flag = 0
                and ((date_part('year', kpi_base_t.start_date) = $year 
                    		and date_part('month', kpi_base_t.start_date) >= $startMonth) 
	                    or (date_part('year', kpi_base_t.start_date) = $year + 1
							and date_part('month', kpi_base_t.start_date) < $startMonth))
                left join (select kpi_id, 
                              sum (kpi_result_t.mark) as sum_mark
                       from kpi_result_t
                       where kpi_result_t.del_flag = 0
                       group by kpi_id) tmp
                on kpi_base_t.kpi_id = tmp.kpi_id
                where user_t.del_flag = 0 
                and user_t.company_id = $company_id
                ";
        if($admin_flag != 1){
        	$query = $query . "and user_t.dep_id = $dep_id";
        }  
        $query = $query . "and ( $search is null or user_t.user_id LIKE $search or user_t.name LIKE $search)
                group by user_t.user_id, user_t.name, user_t.level, user_t.admin_flag
                order by user_t.user_id, user_t.name;";	  
		$record = Database::currentDb()->getMultiRecord($query, $t);
		return $record;
	}  
	
	public static function getYearList()
	{
		
		$query = "SELECT DISTINCT date_part('year', kpi_base_t.start_date) as year
		FROM kpi_base_t;";	  
		$record = Database::currentDb()->getMultiRecord($query, $t);
		return $record;
	} 
	
	// lay thong tin cua 1 user 
	public static function getInformationUser($company_id,$user_id)
	{
		$company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
		$user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
		$query = 
				"SELECT 
					user_t.user_id
					,user_t.dep_id
					, user_t.name
					, user_t.company_id
					, user_t.leader_id
					, department_t.dep_name
					, user_t.level
					,company_t.company_name
					, (select admin_flag from user_t where user_t.company_id = $company_id and user_t.user_id = $user_id LIMIT  1)
				FROM user_t
				JOIN department_t
				ON user_t.dep_id = department_t.dep_id
				   AND department_t.company_id = department_t.company_id
				   AND department_t.del_flag = 0
			    JOIN company_t
                ON company_t.company_id = user_t.company_id
                   AND company_t.del_flag = 0
				WHERE
					user_t.company_id = $company_id
					AND user_t.del_flag = 0 
					AND user_t.user_id = $user_id;";	  
		$record = Database::currentDb()->getRecord($query);
		
		return $record;
	}
	
	// get data from table kpi_base_t
	public static function getAllKpiOfUserInYear($user_id, $company_id, $year, $search, $start_month)
	{	
		$user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
		$search = DbAgent::queryEncode($search,DbAgent::$DB_STRING);
		$year = DbAgent::queryEncode($year,DbAgent::$DB_NUMBER);
		$company_id	= DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);	
		$today = DbAgent::queryEncode(date("Y-m-d"),DbAgent::$DB_STRING);
		$start_month = DbAgent::queryEncode($start_month,DbAgent::$DB_NUMBER);
		
		$query = "SELECT 
                    kpi_base_t.company_id
                    ,kpi_base_t.kpi_id
                    ,kpi_base_t.user_id
                    ,user_t.name
                    ,kpi_base_t.category_kpi
                    ,kpi_base_t.kpi_name
                    ,kpi_base_t.goal
                    ,kpi_base_t.start_date
                    ,kpi_base_t.end_date
                    ,kpi_base_t.comment_user
                    ,kpi_base_t.comment_leader
                    ,kpi_base_t.accept_flag
                    ,sum(CASE WHEN kpi_result_t.del_flag = 0 THEN kpi_result_t.mark END) as mark_kpi
                    ,count(CASE WHEN 
                                ((date_part('year', kpi_base_t.start_date) = $year 
	                    		and date_part('month', kpi_base_t.start_date) >= $start_month) 
		                    	or (date_part('year', kpi_base_t.start_date) = $year + 1
								and date_part('month', kpi_base_t.start_date) < $start_month))
                                and kpi_base_t.end_date >= date($today)  
                                and kpi_base_t.del_flag = 0 
                          THEN 1 END) as kpidangthuchien
                
                    ,user_t.leader_id
                FROM kpi_base_t
                LEFT JOIN kpi_result_t
                    ON kpi_result_t.kpi_id = kpi_base_t.kpi_id
                    and kpi_result_t.del_flag = 0
                LEFT JOIN user_t
                    ON kpi_base_t.user_id = user_t.user_id
                    and kpi_base_t.company_id = user_t.company_id
                    and kpi_base_t.dep_id = user_t.dep_id
                    and user_t.del_flag = 0
                LEFT JOIN company_t
                    ON user_t.company_id = company_t.company_id
                    and company_t.del_flag = 0
                LEFT JOIN department_t
                    ON user_t.company_id = department_t.company_id
                    and user_t.dep_id = department_t.dep_id
                    and department_t.del_flag = 0
                WHERE 
                    kpi_base_t.company_id = $company_id
                    and ((date_part('year', kpi_base_t.start_date) = $year 
                    		and date_part('month', kpi_base_t.start_date) >= $start_month) 
	                    or (date_part('year', kpi_base_t.start_date) = $year + 1
							and date_part('month', kpi_base_t.start_date) < $start_month))
                    and kpi_base_t.del_flag = 0
                    and kpi_base_t.user_id = $user_id
                    and ( $search is null 
                        or kpi_base_t.user_id LIKE $search 
                        or user_t.name LIKE $search
                        )
                GROUP BY 
                    kpi_base_t.kpi_id
                    ,kpi_base_t.user_id
                    ,user_t.name
                    ,kpi_base_t.category_kpi
                    ,kpi_base_t.kpi_name
                    ,kpi_base_t.goal
                    ,kpi_base_t.start_date
                    ,kpi_base_t.end_date
                    ,kpi_base_t.comment_user
                    ,kpi_base_t.accept_flag
                    ,kpi_base_t.comment_leader
                    ,user_t.leader_id
                ORDER BY
                     kpi_base_t.user_id,kpi_base_t.category_kpi,kpi_base_t.kpi_id;";	
		
		$records = Database::currentDb()->getMultiRecord($query, $t);
		
		foreach ($records as $key1 => &$kpi) {
			$kpi['months'] = Data::getKpiManagerByKpiId($year,$kpi['kpi_id'], $start_month);
			foreach ($kpi['months'] as $key2 => &$data_month) {
				$data_month['days'] = Data::getMarkDay($kpi['kpi_id'], $data_month['month']);
			}
		}
		return $records;
	}
	
	// get data from table kpi_base_t
    public static function getAllKpiOfUserInMonthAndDay($user_id, $company_id, $month)
    {
        $user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
        $month = DbAgent::queryEncode($month,DbAgent::$DB_NUMBER);   
        $query = "SELECT 
                    kpi_base_t.kpi_id
                    ,kpi_base_t.user_id
                    ,kpi_base_t.category_kpi
                    ,kpi_base_t.kpi_name
                    ,kpi_manager_t.month
                    ,category_t.category_name
                    ,sum(kpi_result_t.mark) as mark_kpi
                    ,kpi_manager_t.mark_month
                FROM kpi_base_t
                INNER JOIN kpi_manager_t
                    ON kpi_manager_t.kpi_id   = kpi_base_t.kpi_id
                    AND kpi_manager_t.del_flag = 0
                INNER JOIN user_t
                    ON kpi_base_t.user_id = user_t.user_id
                    AND kpi_base_t.company_id = user_t.company_id
                    AND user_t.del_flag = 0
                INNER JOIN company_t
                    ON user_t.company_id = company_t.company_id
                    AND company_t.del_flag = 0
                INNER JOIN category_t
                    ON kpi_base_t.category_kpi = category_t.category_id 
                    AND category_t.del_flag = 0
                LEFT JOIN kpi_result_t
                    ON kpi_base_t.kpi_id = kpi_result_t.kpi_id 
                    AND kpi_result_t.del_flag = 0
            
                WHERE 
                    kpi_base_t.company_id = $company_id
                    and kpi_base_t.user_id = $user_id
                    and kpi_manager_t.month = $month
                    and kpi_base_t.del_flag = 0
            
                GROUP BY 
                    kpi_base_t.kpi_id
                    ,kpi_base_t.user_id
                    ,kpi_base_t.category_kpi
                    ,kpi_base_t.kpi_name
                    ,kpi_manager_t.month
                    ,category_t.category_name
                    ,kpi_manager_t.mark_month
                ORDER BY
                    kpi_base_t.user_id
                    ,kpi_manager_t.month
                    ,kpi_base_t.category_kpi
                    ,kpi_base_t.kpi_id;";    
                    
        
        $records = Database::currentDb()->getMultiRecord($query, $t);
        
         foreach ($records as $key => &$record) {
             $kpi_id = $record['kpi_id'];
             $query = "SELECT
                        kpi_result_t.day
                        ,kpi_result_t.result
                        ,kpi_result_t.mark
                        ,kpi_result_t.id
                    FROM kpi_result_t
                    WHERE kpi_result_t.kpi_id   = $kpi_id
                    AND kpi_result_t.del_flag = 0
                    AND kpi_result_t.month = $month
                    ORDER BY kpi_result_t.day
                        ,kpi_result_t.id";
             $record['day_list'] = Database::currentDb()->getMultiRecord($query, $t);
         }  
        
        return $records;
    }
    
    // get data from table kpi_base_t
    public static function getAllKpiOfUserInMonthAndDayByKpiId($user_id, $company_id, $month, $kpiId)
    {
        $user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
        $month = DbAgent::queryEncode($month,DbAgent::$DB_NUMBER);   
        $query = "SELECT 
                    kpi_base_t.kpi_id
                    ,kpi_base_t.user_id
                    ,kpi_base_t.category_kpi
                    ,kpi_base_t.kpi_name
                    ,kpi_manager_t.month
                    ,category_t.category_name
                    ,sum(kpi_result_t.mark) as mark_kpi
                    ,kpi_manager_t.mark_month
                FROM kpi_base_t
                INNER JOIN kpi_manager_t
                    ON kpi_manager_t.kpi_id   = kpi_base_t.kpi_id
                    AND kpi_manager_t.del_flag = 0
                INNER JOIN user_t
                    ON kpi_base_t.user_id = user_t.user_id
                    AND kpi_base_t.company_id = user_t.company_id
                    AND user_t.del_flag = 0
                INNER JOIN company_t
                    ON user_t.company_id = company_t.company_id
                    AND company_t.del_flag = 0
                INNER JOIN category_t
                    ON kpi_base_t.category_kpi = category_t.category_id 
                    AND category_t.del_flag = 0
                LEFT JOIN kpi_result_t
                    ON kpi_base_t.kpi_id = kpi_result_t.kpi_id 
                    AND kpi_result_t.del_flag = 0
            
                WHERE 
                    kpi_base_t.company_id = $company_id
                    and kpi_manager_t.month = $month
                    and kpi_base_t.kpi_id = $kpiId
                    and kpi_base_t.del_flag = 0
            
                GROUP BY 
                    kpi_base_t.kpi_id
                    ,kpi_base_t.user_id
                    ,kpi_base_t.category_kpi
                    ,kpi_base_t.kpi_name
                    ,kpi_manager_t.month
                    ,category_t.category_name
                    ,kpi_manager_t.mark_month
                ORDER BY
                    kpi_base_t.user_id
                    ,kpi_manager_t.month
                    ,kpi_base_t.category_kpi
                    ,kpi_base_t.kpi_id;";    
                    
        
        $records = Database::currentDb()->getMultiRecord($query, $t);
        
         foreach ($records as $key => &$record) {
             $kpi_id = $record['kpi_id'];
             $query = "SELECT
                        kpi_result_t.day
                        ,kpi_result_t.result
                        ,kpi_result_t.mark
                        ,kpi_result_t.id
                    FROM kpi_result_t
                    WHERE kpi_result_t.kpi_id   = $kpi_id
                    AND kpi_result_t.del_flag = 0
                    AND kpi_result_t.month = $month
                    ORDER BY kpi_result_t.day
                        ,kpi_result_t.id";
             $record['day_list'] = Database::currentDb()->getMultiRecord($query, $t);
         }  
        
        return $records;
    }
    
	// get data from table kpi_base_t
	public static function getDataKpiByKpiId($company_id, $year, $kpi_id, $start_month)
	{
		//$user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
		$company_id	= DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
		$year = DbAgent::queryEncode($year,DbAgent::$DB_NUMBER);
		$kpi_id= DbAgent::queryEncode($kpi_id,DbAgent::$DB_NUMBER);	
			$query = "SELECT 
                    user_t.user_id
                    ,user_t.name
                    ,kpi_base_t.kpi_id
                    ,kpi_base_t.category_kpi
                    ,kpi_base_t.kpi_name
                    ,kpi_base_t.goal
                    ,kpi_base_t.rate as point_kpi
                    ,kpi_base_t.start_date
                    ,kpi_base_t.end_date
                    ,kpi_base_t.comment_user
                    ,kpi_base_t.accept_flag
                    ,sum(CASE WHEN kpi_result_t.del_flag = 0 THEN kpi_result_t.mark END) as mark_kpi
                    FROM kpi_base_t
                    LEFT JOIN kpi_result_t
                    ON kpi_result_t.kpi_id = kpi_base_t.kpi_id
                    and kpi_result_t.del_flag = 0
                    LEFT JOIN user_t
                    ON kpi_base_t.user_id = user_t.user_id
                    and kpi_base_t.company_id = user_t.company_id
                    and kpi_base_t.dep_id = user_t.dep_id
                    and user_t.del_flag = 0          
                    WHERE 
                    kpi_base_t.company_id = $company_id
                    and  ((date_part('year', kpi_base_t.start_date) = $year 
                    		and date_part('month', kpi_base_t.start_date) >= $start_month) 
	                    or (date_part('year', kpi_base_t.start_date) = $year + 1
							and date_part('month', kpi_base_t.start_date) < $start_month))
                    and kpi_base_t.del_flag = 0
                    and kpi_base_t.kpi_id = $kpi_id
                    GROUP BY 
                    user_t.user_id
                    ,user_t.name
                    ,kpi_base_t.kpi_id
                    ,kpi_base_t.category_kpi
                    ,kpi_base_t.kpi_name
                    ,kpi_base_t.goal
                    ,kpi_base_t.rate
                    ,kpi_base_t.start_date
                    ,kpi_base_t.end_date
                    ,kpi_base_t.comment_user
                    ,kpi_base_t.accept_flag
                    ORDER BY
                    kpi_base_t.category_kpi
                    ,kpi_base_t.kpi_name;";	  
		$record = Database::currentDb()->getRecord($query);		
		return $record;
	}
	
	// get data from table kpi_manager_t
	public static function getKpiManagerByKpiId($year,$kpi_id, $start_month)
	{
		$kpi_id = DbAgent::queryEncode($kpi_id,DbAgent::$DB_NUMBER);
		$start_month = DbAgent::queryEncode($start_month,DbAgent::$DB_NUMBER);
		$year = DbAgent::queryEncode($year,DbAgent::$DB_NUMBER);
		
		$query = "SELECT kpi_manager_t.kpi_id,
                       kpi_manager_t.month,
                       kpi_manager_t.goal_month,
                       kpi_manager_t.mark_month,
                       kpi_manager_t.company_id,
                       kpi_manager_t.del_flag
                       ,SUM(CASE WHEN kpi_result_t.del_flag = 0 THEN kpi_result_t.mark END) as mark
               FROM kpi_manager_t
                LEFT JOIN kpi_result_t
                ON kpi_result_t.kpi_id = kpi_manager_t.kpi_id
                AND kpi_result_t.month = kpi_manager_t.month
                and kpi_result_t.del_flag = 0
                WHERE
                        kpi_manager_t.kpi_id = $kpi_id
                        and (kpi_manager_t.month >= ($year*100 + $start_month) and kpi_manager_t.month < (($year+1)*100 + $start_month))
                        and kpi_manager_t.del_flag = 0
                GROUP BY kpi_manager_t.kpi_id,
                       kpi_manager_t.month,
                       kpi_manager_t.goal_month,
                       kpi_manager_t.mark_month,
                       kpi_manager_t.company_id,
                       kpi_manager_t.del_flag
              order by kpi_id, month;";	  
		$records = Database::currentDb()->getMultiRecord($query, $t);
		return $records;
	}
	// get data from table kpi_base_t by kpi_id
	
	public static function getKpiBaseByKpiId($kpi_id, $month)
	{
		$kpi_id = DbAgent::queryEncode($kpi_id,DbAgent::$DB_NUMBER);
		$month = DbAgent::queryEncode($month,DbAgent::$DB_NUMBER);
		$query = "SELECT kpi_base_t.user_id, kpi_base_t.category_kpi, kpi_base_t.kpi_name, kpi_manager_t.month, kpi_manager_t.goal_month,kpi_base_t.accept_flag 
				  FROM kpi_base_t
				  INNER JOIN kpi_manager_t
				  ON kpi_base_t.kpi_id = kpi_manager_t.kpi_id
				  and kpi_manager_t.del_flag = 0
				  WHERE kpi_base_t.kpi_id = $kpi_id
				  and kpi_manager_t.month = $month
				  and kpi_base_t.del_flag = 0;";	
				    
		$record = Database::currentDb()->getRecord($query);	
		return $record;	
	}
	// get data from table kpi_result_t by month
	
	public static function getResultKpi($kpi_id, $month)
	{
		$kpi_id = DbAgent::queryEncode($kpi_id,DbAgent::$DB_NUMBER);
		$month = DbAgent::queryEncode($month,DbAgent::$DB_NUMBER);
		
		$query = "select  kpi_result_t.id, kpi_result_t.kpi_id, kpi_result_t.month, 
					kpi_result_t.result, kpi_result_t.mark, kpi_result_t.day, kpi_result_t.del_flag,
					kpi_base_t.accept_flag
					from kpi_result_t
					left join kpi_base_t
					on kpi_result_t.kpi_id = kpi_base_t.kpi_id
					and kpi_base_t.del_flag = 0
					where kpi_result_t.kpi_id = $kpi_id
					and kpi_result_t.month = $month
					and kpi_result_t.del_flag = 0				    
					order by kpi_id, month, day;";
						  
		$records = Database::currentDb()->getMultiRecord($query, $t);	
		return $records;	
	}
	
	
	
	public static function exportLeader($company_id, $year, $search, $start_month)
    {
        $year = DbAgent::queryEncode($year,DbAgent::$DB_NUMBER);
        $company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
        $search = DbAgent::queryEncode($search,DbAgent::$DB_STRING);    
        $today = DbAgent::queryEncode(date("Y-m-d"),DbAgent::$DB_STRING);
		$start_month = DbAgent::queryEncode($start_month,DbAgent::$DB_NUMBER);
		
        $query = "SELECT 
                      kpi_base_t.kpi_id
                    ,kpi_base_t.user_id
                    ,kpi_base_t.category_kpi
                    ,kpi_base_t.kpi_name
                    ,kpi_base_t.goal
                    ,kpi_base_t.rate as point_kpi
                    ,kpi_base_t.start_date
                    ,kpi_base_t.end_date
                    ,kpi_base_t.comment_user
                    ,kpi_base_t.comment_leader
                    ,kpi_base_t.accept_flag
                    ,sum(CASE WHEN kpi_result_t.del_flag = 0 THEN kpi_result_t.mark END) as mark_kpi
                    ,count(CASE WHEN 
                                ((date_part('year', kpi_base_t.start_date) = $year 
		                    		and date_part('month', kpi_base_t.start_date) >= $start_month) 
			                    or (date_part('year', kpi_base_t.start_date) = $year + 1
									and date_part('month', kpi_base_t.start_date) < $start_month)) 
                                and kpi_base_t.del_flag = 0 
                          THEN 1 END) as kpidangthuchien
                    FROM kpi_base_t
                LEFT JOIN kpi_result_t
                    ON kpi_result_t.kpi_id = kpi_base_t.kpi_id
                    and kpi_result_t.del_flag = 0
                WHERE kpi_base_t.del_flag = 0
                    and ((date_part('year', kpi_base_t.start_date) = $year 
                    		and date_part('month', kpi_base_t.start_date) >= $start_month) 
	                    or (date_part('year', kpi_base_t.start_date) = $year + 1
							and date_part('month', kpi_base_t.start_date) < $start_month))
                    and kpi_base_t.company_id = $company_id
                    and ( $search is null or kpi_base_t.category_kpi LIKE $search)
                GROUP BY 
                    kpi_base_t.kpi_id
                    ,kpi_base_t.user_id
                    ,kpi_base_t.category_kpi
                    ,kpi_base_t.kpi_name
                    ,kpi_base_t.goal
                    ,kpi_base_t.rate
                    ,kpi_base_t.start_date
                    ,kpi_base_t.end_date
                    ,kpi_base_t.comment_user
                    ,kpi_base_t.accept_flag
                    ,kpi_base_t.comment_leader
                ORDER BY
                    kpi_base_t.user_id,kpi_base_t.category_kpi,kpi_base_t.kpi_id;";    
        
        $records = Database::currentDb()->getMultiRecord($query, $t);
        
        foreach ($records as $key1 => &$kpi) {
            $kpi['months'] = Data::getKpiManagerByKpiId($year,$kpi['kpi_id'], $start_month);
            foreach ($kpi['months'] as $key2 => &$data_month) {
                $data_month['days'] = Data::getMarkDay($kpi['kpi_id'], $data_month['month']);
            }
        }
        return $records;
    }
	
	// get Mark Kpi
	 
	 public static function getMarkKpi($kpi_id)
	{
		$kpi_id = DbAgent::queryEncode($kpi_id,DbAgent::$DB_NUMBER);
		
		$query = "SELECT
                kpi_base_t.kpi_id,
                kpi_base_t.goal,
                SUM(kpi_result_t.mark ) as mark_kpi          
                FROM kpi_base_t
                
                left join kpi_result_t
                On kpi_result_t.kpi_id = kpi_base_t.kpi_id
                and kpi_result_t.del_flag = 0 
                
                where 
                kpi_base_t.kpi_id = $kpi_id
                group by kpi_base_t.goal,kpi_base_t.kpi_id;";
											  
		$record = Database::currentDb()->getRecord($query); 
		return $record;
	}
	
	// get mark month
	
	 public static function getMarkMonth($kpi_id, $month)
    {
        $kpi_id = DbAgent::queryEncode($kpi_id,DbAgent::$DB_NUMBER);
        $month = DbAgent::queryEncode($month,DbAgent::$DB_NUMBER);
        
        $query = "SELECT kpi_manager_t.kpi_id,
                         kpi_manager_t.month,
                         kpi_manager_t.goal_month,
                         kpi_manager_t.mark_month,
                         SUM(CASE WHEN kpi_result_t.del_flag = 0 and kpi_result_t.month = $month THEN kpi_result_t.mark END) as mark 
                  FROM kpi_manager_t
                  LEFT JOIN kpi_result_t
                  ON kpi_result_t.kpi_id = kpi_manager_t.kpi_id
                  and kpi_result_t.del_flag = 0
                  WHERE  kpi_manager_t.month = $month
                         and kpi_manager_t.kpi_id = $kpi_id
                         and kpi_manager_t.month/100 = $month/100
                         and kpi_manager_t.del_flag = 0
                  GROUP BY kpi_manager_t.kpi_id,
                           kpi_manager_t.month,
                           kpi_manager_t.goal_month,
                           kpi_manager_t.mark_month
                  order by kpi_id, month;";
                                              
        $record = Database::currentDb()->getRecord($query); 
        
        return $record;
    }
	
	// get Mark month     
     public static function getMarkDay($kpi_id, $month)
    {
        $kpi_id = DbAgent::queryEncode($kpi_id,DbAgent::$DB_NUMBER);
        $month = DbAgent::queryEncode($month,DbAgent::$DB_NUMBER);
        
        $query = "SELECT kpi_manager_t.kpi_id,
                         kpi_manager_t.month,
                         kpi_result_t.day,
                         kpi_result_t.result,
                         kpi_manager_t.del_flag,
                         kpi_result_t.mark as mark_day
                  FROM kpi_manager_t
                  LEFT JOIN kpi_result_t
                  ON kpi_result_t.kpi_id = kpi_manager_t.kpi_id
                  AND kpi_result_t.month = kpi_manager_t.month
                  and kpi_result_t.del_flag = 0
                  WHERE kpi_manager_t.month = $month
                        and kpi_manager_t.kpi_id = $kpi_id
                        and kpi_manager_t.month/100 = $month/100
                        and kpi_manager_t.del_flag = 0
                  order by kpi_id, month, day;";
                                              
        $record = Database::currentDb()->getMultiRecord($query, $t);    
        return $record;
    }
	// get month of kpi_id
	
	public static function getMonthOfKpiId($kpi_id)
	{
		$kpi_id = DbAgent::queryEncode($kpi_id,DbAgent::$DB_NUMBER);
		
		$query = "SELECT month
				FROM kpi_manager_t
				WhERE 
				kpi_id = $kpi_id
				and del_flag = 0
				ORDER BY month;";
											  
		$records = Database::currentDb()->getMultiRecord($query, $t);		
		return $records;
	}
		
	//insert to table kpi_manager_t
	public static function insertKpiManager($company_id, $user_id, $goal, $start_date, $end_date, $category_kpi, $kpi_name,
	 $comment_user, $month_array, $goal_month_array, $goal_markMonth_array, $dep_id)
	{
		$company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
		$dep_id = DbAgent::queryEncode($dep_id,DbAgent::$DB_STRING);
		$user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
		$goal = DbAgent::queryEncode($goal,DbAgent::$DB_NUMBER);
		$start_date = DbAgent::queryEncode($start_date,DbAgent::$DB_DATE);
		$end_date = DbAgent::queryEncode($end_date,DbAgent::$DB_DATE);
		$category_kpi = DbAgent::queryEncode($category_kpi,DbAgent::$DB_STRING);
		$kpi_name = DbAgent::queryEncode($kpi_name,DbAgent::$DB_STRING);
		$comment_user = DbAgent::queryEncode($comment_user,DbAgent::$DB_STRING);
		// $point_kpi = DbAgent::queryEncode($point_kpi,DbAgent::$DB_STRING);		
		$query = "INSERT INTO kpi_base_t(company_id, user_id, goal, start_date, end_date, priority_flag, del_flag, category_kpi, kpi_name, comment_leader, comment_user, accept_flag, dep_id )
				VALUES ($company_id,$user_id, $goal, $start_date, $end_date, 0, 0, $category_kpi, $kpi_name, null, $comment_user, 0, $dep_id );";	  
		Database::currentDb()->execute($query);	
		
		$record = Database::currentDb()->getRecord("SELECT last_value FROM kpi_base_t_kpi_id_seq;");
		$last_kpi_id = $record['last_value'];
		//foreach month insert to ... ($next_kpi_id, month)
		foreach ($month_array as $key => $month) {
		    
            $k = (int)substr($month, -2);
		    $goal_month = $goal_month_array[$k -1];
            $goal_month = DbAgent::queryEncode($goal_month,DbAgent::$DB_STRING);
            $goal_markMonth = $goal_markMonth_array[$k -1];
            $goal_markMonth = DbAgent::queryEncode($goal_markMonth,DbAgent::$DB_NUMBER);

			$query_month = "INSERT INTO kpi_manager_t(kpi_id, month, goal_month, week2, week3, week4, mark_month, company_id, del_flag, priority_flag)
				VALUES ($last_kpi_id, $month, $goal_month , null , null, null, $goal_markMonth , $company_id, 0, 0 )";	  
			Database::currentDb()->execute($query_month);
		}
		
		return $last_kpi_id;
	}
	// insert result Kpi to kpi_result_t
	public static function insertResultKpi($kpi_id, $month, $day, $result, $mark)
	{
		$kpi_id = DbAgent::queryEncode($kpi_id,DbAgent::$DB_NUMBER);
		$month = DbAgent::queryEncode($month,DbAgent::$DB_NUMBER);
		$day = DbAgent::queryEncode($day,DbAgent::$DB_NUMBER);
		$result = DbAgent::queryEncode($result,DbAgent::$DB_STRING);
		$mark = DbAgent::queryEncode($mark,DbAgent::$DB_NUMBER);
				
		$query = "INSERT INTO kpi_result_t(kpi_id, month, day, result, mark, del_flag)
				VALUES ($kpi_id, $month, $day, $result, $mark,0);";	  
		$record = Database::currentDb()->execute($query);	
	}
		
	// delete kpi 
	public static function deleteKpiManager($kpi_id)
	{
		$kpi_id = DbAgent::queryEncode($kpi_id,DbAgent::$DB_NUMBER);		
			$query_kpi = "UPDATE kpi_base_t
				SET del_flag = 1
				WHERE kpi_base_t.kpi_id = $kpi_id
						and del_flag = 0;";	  
			Database::currentDb()->execute($query_kpi);	
			$query_kpi_month = "UPDATE kpi_manager_t
					SET del_flag = 1
					WHERE kpi_manager_t.kpi_id = $kpi_id
						and del_flag = 0;";	  
			Database::currentDb()->execute($query_kpi_month);	  
				$query_kpi_result = "UPDATE kpi_result_t
					SET del_flag = 1
					WHERE kpi_result_t.kpi_id = $kpi_id
						and del_flag = 0;";	  
			Database::currentDb()->execute($query_kpi_result);
	}
	
	//edit kpi
	public static function editKpiManager($company_id, $user_id, $goal, $start_date, $end_date, 
	$category_kpi, $kpi_name, $comment_user, $month_array_new, $goal_month_array,$goal_markMonth_array, 
	$kpi_id,$year, $dep_id, $start_month)
	{
		$company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
		$user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
		$goal = DbAgent::queryEncode($goal,DbAgent::$DB_NUMBER);
		$start_date = DbAgent::queryEncode($start_date,DbAgent::$DB_DATE);
		$end_date = DbAgent::queryEncode($end_date,DbAgent::$DB_DATE);
		$category_kpi = DbAgent::queryEncode($category_kpi,DbAgent::$DB_STRING);
		$kpi_name = DbAgent::queryEncode($kpi_name,DbAgent::$DB_STRING);
		$comment_user = DbAgent::queryEncode($comment_user,DbAgent::$DB_STRING);
		$kpi_id = DbAgent::queryEncode($kpi_id,DbAgent::$DB_NUMBER);
		$year= DbAgent::queryEncode($year,DbAgent::$DB_NUMBER);
		$start_month = DbAgent::queryEncode($start_month,DbAgent::$DB_NUMBER);
		
		// $point_kpi = DbAgent::queryEncode($point_kpi,DbAgent::$DB_STRING);
		// update table kpi_base_t		
		$query_kpi = "UPDATE kpi_base_t
				SET goal = $goal,
					start_date = $start_date, 
					end_date = $end_date,   
					category_kpi = $category_kpi, 
					kpi_name = $kpi_name,				
					comment_user = $comment_user					
				WHERE kpi_base_t.kpi_id = $kpi_id
					  and del_flag = 0;";	  
		Database::currentDb()->execute($query_kpi);	
        
        
		
		$month_array = array_map(function($n) {return $n['month'];}, Data::getKpiManagerByKpiId($year,$kpi_id, $start_month));
		
		// update table kpi_manager_t
		foreach ($month_array_new as $key => $month) {
            $goal_month = $goal_month_array[(int)substr($month, -2) -1];
            $goal_month = DbAgent::queryEncode($goal_month,DbAgent::$DB_STRING);
            $goal_markMonth = $goal_markMonth_array[(int)substr($month, -2) -1];
            $goal_markMonth = DbAgent::queryEncode($goal_markMonth,DbAgent::$DB_NUMBER);
				if (!in_array($month, $month_array )){
     
					$query_month = "INSERT INTO kpi_manager_t(kpi_id, month, goal_month, week2, week3, week4, mark_month, company_id, del_flag, priority_flag)
									VALUES ($kpi_id, $month, $goal_month , null , null, null, $goal_markMonth , $company_id, 0, 0 );";	  
					Database::currentDb()->execute($query_month);
				}
                else{
                    $query_kpi = "UPDATE kpi_manager_t
                            SET goal_month = $goal_month, 
                            mark_month = $goal_markMonth                
                            WHERE kpi_manager_t.kpi_id = $kpi_id
                                  and del_flag = 0
                                  and month = $month;";     
                    Database::currentDb()->execute($query_kpi);
                }	
			//}
		}
		foreach ($month_array as $key => $month) {				
				if (!in_array($month, $month_array_new )){
					$query_month = "DELETE FROM kpi_manager_t
                                    WHERE month = $month
                                    and del_flag = 0
                                    and kpi_id = $kpi_id;";	  
					Database::currentDb()->execute($query_month);
					$query_result = "DELETE FROM kpi_result_t
									WHERE month = $month
										and del_flag = 0
										and kpi_id = $kpi_id;";	  
					Database::currentDb()->execute($query_result);
				}	
		}				
	}

	// delete result Kpi
		
	public static function deleteResultKpi($id_resultKpi)
	
	{
			$id_resultKpi= DbAgent::queryEncode($id_resultKpi,DbAgent::$DB_NUMBER);
			$query_kpi = "UPDATE kpi_result_t
				SET del_flag = 1
				WHERE kpi_result_t.id = $id_resultKpi
					  and (select accept_flag from kpi_base_t where kpi_id=
					     (select kpi_id from kpi_result_t where id =$id_resultKpi)) = 0;";	  
			Database::currentDb()->execute($query_kpi);	
		
	}


	
	//一覧画面のチE�E�Eタ取征E
	public static function getListData($year,$company_id,$user_id, $start_month)
	//public static function getListData($year,$company_id,$search)
	{
		$year = DbAgent::queryEncode($year,DbAgent::$DB_NUMBER);
		$company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
		$user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING); 
		$start_month = DbAgent::queryEncode($start_month,DbAgent::$DB_NUMBER);

		$query = "select 
					kpi_base_t.category_kpi
					,kpi_base_t.kpi_id
					,kpi_base_t.kpi_name
					,user_t.name
					,kpi_base_t.goal
					,kpi_base_t.comment_leader
					,kpi_base_t.priority_flag
				from user_t
				inner join kpi_base_t
				        on kpi_base_t.user_id =user_t.user_id
				
				where user_t.company_id = $company_id
					and user_t.del_flag = 0 
					and user_t.user_id = $user_id
					and kpi_base_t.del_flag = 0
					order by kpi_base_t.category_kpi,kpi_base_t.kpi_name;";

		$record = Database::currentDb()->getMultiRecord($query, $t);
		
		foreach ($record as $khoa => &$thang) {
			$thang['month'] = Data::getKpiManagerByKpiId($year,$thang['kpi_id'], $start_month);
		}
		
		
		return $record;
	} 

	//承認画面のチE�E�Eタ取征E
	public static function getListDataAccept($year,$company_id,$user_id,$search, $start_month)
	{
		$user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
		$year = DbAgent::queryEncode($year,DbAgent::$DB_NUMBER);
		$company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
		$search = DbAgent::queryEncode($search,DbAgent::$DB_STRING);
		$start_month = DbAgent::queryEncode($start_month,DbAgent::$DB_NUMBER);
		//$user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING); 

		$query = "select 
					kpi_base_t.category_kpi
					,kpi_base_t.kpi_name
					,user_t.name
					,kpi_base_t.goal
					,kpi_base_t.comment_leader
					,kpi_base_t.priority_flag
					,kpi_base_t.accept_flag
					
					,kpi_base_t.kpi_id
					,kpi_base_t.company_id
					,user_t.user_id					
				from user_t
				inner join kpi_base_t
				        on kpi_base_t.user_id     = user_t.user_id
				        and kpi_base_t.company_id = $company_id
						and kpi_base_t.del_flag   = 0
			
				where user_t.company_id = $company_id
					and user_t.del_flag = 0 
					and  ((date_part('year', kpi_base_t.start_date) = $year 
                    		and date_part('month', kpi_base_t.start_date) >= $start_month) 
	                    or (date_part('year', kpi_base_t.start_date) = $year + 1
							and date_part('month', kpi_base_t.start_date) < $start_month))
					and ( $search is null or kpi_base_t.category_kpi LIKE $search)
				order by user_t.user_id
						,kpi_base_t.category_kpi;					
				";
 
		$record = Database::currentDb()->getMultiRecord($query, $t);
		
		foreach ($record as $khoa => &$thang) {
			$thang['month'] = Data::getKpiManagerByKpiId($year,$thang['kpi_id'], $start_month);
		}
		
		return $record;
	} 
	
	//承認画面のチE�E�Eタの更新
	public static function UpdateLeaderComment($kpi,$val_leader_comment,$chk_priority_flag,$chk_accept_flag)
	{
		$kpi = DbAgent::queryEncode($kpi,DbAgent::$DB_NUMBER);
		$val_leader_comment = DbAgent::queryEncode($val_leader_comment,DbAgent::$DB_STRING);
		$chk_priority_flag = DbAgent::queryEncode($chk_priority_flag,DbAgent::$DB_NUMBER);
		$chk_accept_flag = DbAgent::queryEncode($chk_accept_flag,DbAgent::$DB_NUMBER);

		$query = "update  kpi_base_t
				  set 	 comment_leader = $val_leader_comment
						,priority_flag  = $chk_priority_flag
						,accept_flag    = $chk_accept_flag
				  where
						kpi_base_t.kpi_id =   $kpi ;";

		Database::currentDb()->execute($query);	
	}
		// get data from table kpi_result_t by month
	
	// get KPI trong vong 1 ngay hoac 1 thang
	public static function getKpiOneMonthDay($user_id, $month, $day)
	{
		$month = DbAgent::queryEncode($month,DbAgent::$DB_NUMBER);
		$day = DbAgent::queryEncode($day,DbAgent::$DB_NUMBER);
		$user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
				
			$query = "SELECT
							user_t.user_id
							,user_t.name
							,kpi_manager_t.month
							,kpi_base_t.kpi_id
							,kpi_base_t.category_kpi
							,kpi_base_t.kpi_name
							,SUM(kpi_result_t.mark) as mark_kpi
							,category_t.category_name
						FROM user_t
						INNER JOIN kpi_base_t
								ON kpi_base_t.user_id    = user_t.user_id
							    AND kpi_base_t.del_flag   = 0
						INNER JOIN category_t
                                ON kpi_base_t.category_kpi = category_t.category_id 
                                AND category_t.del_flag = 0	       
						INNER JOIN kpi_manager_t
								ON kpi_manager_t.kpi_id = kpi_base_t.kpi_id
							    AND kpi_manager_t.del_flag = 0
						LEFT JOIN kpi_result_t
								ON kpi_manager_t.kpi_id = kpi_result_t.kpi_id
								AND kpi_result_t.month = $month
								AND kpi_result_t.del_flag = 0
						WHERE
							user_t.user_id     = $user_id
							and kpi_manager_t.month = $month
							and user_t.del_flag   = 0
						GROUP BY
							user_t.user_id
							,user_t.name
							,kpi_manager_t.month
							,kpi_base_t.kpi_id
							,kpi_base_t.category_kpi
							,kpi_base_t.kpi_name
							,category_t.category_name
						ORDER BY 
                            user_t.user_id
                            ,kpi_manager_t.month
                            ,kpi_base_t.category_kpi
                            ,kpi_base_t.kpi_id;";	  
		$records = Database::currentDb()->getMultiRecord($query, $t);	
		foreach ($records as $key => &$record) {
             $kpi_id = $record['kpi_id'];
             $query = "SELECT
             			kpi_result_t.id
                        ,kpi_result_t.day
                        ,kpi_result_t.result
                        ,kpi_result_t.mark
                    FROM kpi_result_t
                    WHERE kpi_result_t.kpi_id   = $kpi_id
                    AND kpi_result_t.del_flag = 0
                    AND kpi_result_t.month = $month";
             $record['day_list'] = Database::currentDb()->getMultiRecord($query, $t);
         }  
		return $records;	
	} 

    // Lay danh sach tung ngay trong 1 thang
    public static function getKpiOneMonth($user_id, $month){
        $month = DbAgent::queryEncode($month,DbAgent::$DB_NUMBER);
        $user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
        $query = "SELECT Distinct
                        kpi_base_t.kpi_id
                        ,kpi_base_t.category_kpi
                        ,kpi_base_t.kpi_name
                        ,kpi_result_t.month
                       
                    FROM user_t
                        INNER JOIN kpi_base_t
                        ON kpi_base_t.user_id    = user_t.user_id
                    AND kpi_base_t.del_flag   = 0
                        INNER JOIN kpi_result_t
                        ON kpi_result_t.kpi_id   = kpi_base_t.kpi_id
                    AND kpi_result_t.del_flag = 0
                    WHERE
                        user_t.user_id     = $user_id
                    AND user_t.del_flag    = 0 
                    AND kpi_result_t.month = $month";
         $records = Database::currentDb()->getMultiRecord($query, $t);
         foreach ($records as $key => &$record) {
             $kpi_id = $record['kpi_id'];
             $query = "SELECT
                        kpi_result_t.day
                        ,kpi_result_t.result
                        ,kpi_result_t.mark
                        ,kpi_result_t.id
                    FROM kpi_result_t
                    WHERE kpi_result_t.kpi_id   = $kpi_id
                    AND kpi_result_t.del_flag = 0
                    AND kpi_result_t.month = $month";
             $record['day_list'] = Database::currentDb()->getMultiRecord($query, $t);
         }  
         return $records;
    }
    
    //Update cac KPI trong vong 1 ngay
	public static function UpdateKpiOneDay($id,$kpi_id,$month,$day,$result,$mark)
	{
		$kpi_id     = DbAgent::queryEncode($kpi_id,DbAgent::$DB_NUMBER);
		$month      = DbAgent::queryEncode($month,DbAgent::$DB_NUMBER);
		$day        = DbAgent::queryEncode($day,DbAgent::$DB_NUMBER);
		$mark       = DbAgent::queryEncode($mark,DbAgent::$DB_NUMBER);
		$result     = DbAgent::queryEncode($result,DbAgent::$DB_STRING);

		$query = "UPDATE kpi_result_t
				  SET result = $result
				  	  ,mark  = $mark
				  	  ,day   = $day
				  WHERE
					  id     = $id 
				  AND kpi_id = $kpi_id
				  AND month  = $month;";

		$record = Database::currentDb()->execute($query);
		return $record;	
	}
	
	
	// Lay danh sach tung ngay trong 1 thang
    public static function getKpiOneMonth_jSon($user_id, $month){
        $month = DbAgent::queryEncode($month,DbAgent::$DB_NUMBER);
        $user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
        $query = "SELECT Distinct
                        kpi_base_t.kpi_id
                        ,kpi_base_t.category_kpi
                        ,kpi_base_t.kpi_name
                        ,kpi_manager_t.month
                        ,category_t.category_name
                    FROM user_t
                    INNER JOIN kpi_base_t
                        ON kpi_base_t.user_id    = user_t.user_id
                        AND kpi_base_t.del_flag   = 0
                    INNER JOIN category_t
                        ON kpi_base_t.category_kpi = category_t.category_id 
                        AND category_t.del_flag = 0
                    INNER JOIN kpi_manager_t
                        ON kpi_manager_t.kpi_id   = kpi_base_t.kpi_id
                        AND kpi_manager_t.del_flag = 0
                    WHERE
                        user_t.user_id     = $user_id
                    AND user_t.del_flag    = 0 
                    AND kpi_manager_t.month = $month
                    ORDER BY kpi_base_t.category_kpi, 
                    kpi_base_t.kpi_id";
         $records = Database::currentDb()->getMultiRecord($query, $t);
         foreach ($records as $key => &$record) {
             $kpi_id = $record['kpi_id'];
             $query = "SELECT
                        kpi_result_t.day
                        ,kpi_result_t.result
                        ,kpi_result_t.mark
                        ,kpi_result_t.id
                    FROM kpi_result_t
                    WHERE kpi_result_t.kpi_id   = $kpi_id
                    AND kpi_result_t.del_flag = 0
                    AND kpi_result_t.month = $month
                    ORDER BY kpi_result_t.day";
             $record['day_list'] = Database::currentDb()->getMultiRecord($query, $t);
         }  
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
    
    // insert Message to message_t
	public static function insertMessage($company_id, $user_id, $code, $terminal, $function, $content)
	{
		$company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
		$user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
		$code = DbAgent::queryEncode($code,DbAgent::$DB_STRING);
		$terminal = DbAgent::queryEncode($terminal,DbAgent::$DB_STRING);
		$function = DbAgent::queryEncode($function,DbAgent::$DB_STRING);
		$content = DbAgent::queryEncode($content,DbAgent::$DB_STRING);
				
		$query = "INSERT INTO message_t(send_time, company_id, user_id, code, terminal, function, content, del_flag)
				VALUES (CURRENT_TIMESTAMP, $company_id, $user_id, $code, $terminal, $function, $content, 0);";	  
		$record = Database::currentDb()->execute($query);	
	}
	
    
    // insert history_t
	public static function insertHistory($company_id, $user_id, $kpi_id, $code, $content, $action_user_id)
	{
		$company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
		$user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
		$kpi_id = DbAgent::queryEncode($kpi_id,DbAgent::$DB_STRING);
		$code = DbAgent::queryEncode($code,DbAgent::$DB_STRING);
		$content = DbAgent::queryEncode($content,DbAgent::$DB_STRING);
		$action_user_id = DbAgent::queryEncode($action_user_id,DbAgent::$DB_STRING);
		
		$query = "INSERT INTO history_t(times, company_id, user_id, kpi_id, code, content, action_user_id)
				VALUES (CURRENT_TIMESTAMP, $company_id, $user_id, $kpi_id, $code, $content, $action_user_id);";
				
		// $query = "INSERT INTO history_t(times, company_id, user_id, kpi_id, code, content)
				// VALUES (CURRENT_TIMESTAMP, $company_id, $user_id, $kpi_id, $code, $content);";	  
		$record = Database::currentDb()->execute($query);	
	}
	
	// Get KPI Base info from KPI_ID 
	public static function getKpiBaseInfoFromKpiId($kpi_id)
	{	  
		$query = "SELECT kpi_id, company_id, dep_id, user_id, goal, start_date, end_date, category_kpi, kpi_name, comment_user
                FROM   kpi_base_t
                WhERE  del_flag = 0
                AND kpi_id = $kpi_id ;";
                                              
		$record = Database::currentDb()->getRecord($query);
        return $record;
	}
	
	// Get KPI Manager info from KPI_ID 
	public static function getKpiManagerInfoFromKpiId($kpi_id)
	{	  
		$query = "SELECT kpi_id, month, goal_month, mark_month, company_id
                FROM   kpi_manager_t
                WhERE  del_flag = 0
                AND kpi_id = $kpi_id ;";
                                              
		$records = Database::currentDb()->getMultiRecord($query, $t);       
        return $records;
	}
	
	// get MaxId Form History_t
	public static function getMaxIdFormHistory()
	{	  
		$record = Database::currentDb()->getRecord("SELECT max(id) as maxid FROM history_t");
		$max_id = $record['maxid'];
		return $max_id;
	}
	
	// get UserName By Company_id, User_id
	public static function getUserNameByUserId($company_id, $user_id)
	{
		$company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
		$user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
			  
		$record = Database::currentDb()->getRecord("SELECT name FROM user_t WHERE company_id = $company_id AND user_id = $user_id");
		$name = $record['name'];
		return $name;
	}
	
	// get KPI Name By KPI ID
	public static function getKpiNameByKpiId($kpi_id)
	{
		$kpi_id = DbAgent::queryEncode($kpi_id,DbAgent::$DB_STRING);
			  
		$record = Database::currentDb()->getRecord("SELECT kpi_name FROM kpi_base_t WHERE kpi_id = $kpi_id");
		$kpi_name = $record['kpi_name'];
		return $kpi_name;
	}
	
	// get KPI Name By KPI ID
	public static function getResultFromId($id)
	{
		$id = DbAgent::queryEncode($id,DbAgent::$DB_STRING);

		$query = "SELECT kpi_id, month, day FROM kpi_result_t WHERE id = $id;";
                                              
		$record = Database::currentDb()->getRecord($query);
        return $record;
	}
	
	// insert notification_t
	public static function insertNotification($company_id, $user_id, $content)
	{
		$company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
		$user_id = DbAgent::queryEncode($user_id,DbAgent::$DB_STRING);
		$content = DbAgent::queryEncode($content,DbAgent::$DB_STRING);
				
		$query = "INSERT INTO notification_t(times, company_id, user_id, content)
				VALUES (CURRENT_TIMESTAMP, $company_id, $user_id, $content);";	  
		$record = Database::currentDb()->execute($query);	
	}
	
	// Load notification_t by company
	public static function getNotificationByCompany($company_id)
	{
		$company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
				
		$query = "SELECT n.id, n.times, n.company_id, n.user_id, n.content, u.name
                FROM   notification_t n, user_t u
                WhERE  n.company_id = $company_id
				AND n.company_id = u.company_id
				AND n.user_id = u.user_id
                ORDER BY id DESC
                LIMIT 1;";
                                              
        $record = Database::currentDb()->getRecord($query);
        return $record;
	}
	
	// Load history_t by company
	public static function getHistoryByCompany($company_id)
	{
		$company_id = DbAgent::queryEncode($company_id,DbAgent::$DB_STRING);
				
		// $query = "SELECT h.id, h.times, h.company_id, h.user_id, h.kpi_id, h.code, h.content, u.name
                // FROM   history_t h, user_t u
                // WhERE  h.company_id = $company_id
				// AND h.company_id = u.company_id
				// AND h.user_id = u.user_id
				// AND h.times > CURRENT_TIMESTAMP - INTERVAL '2' DAY
                // ORDER BY id DESC;";
                
		$query = "WITH result as (SELECT h.id, h.times, h.company_id, h.user_id, h.kpi_id, h.code, h.content, u.name, h.action_user_id
                FROM   history_t h, user_t u
                WHERE  h.company_id = $company_id
				AND h.company_id = u.company_id
				AND h.user_id = u.user_id
				AND h.times > CURRENT_TIMESTAMP - INTERVAL '2' DAY
                ORDER BY id DESC)
                
                SELECT a.id, a.times, a.company_id, a.user_id, a.kpi_id, a.code, a.content, a.name, a.action_user_id, u.name as action_user_name
                FROM result a
                LEFT JOIN user_t u
                ON a.action_user_id = u.user_id 
				WHERE a.company_id = u.company_id
				ORDER BY id DESC;";
                                              
        $records = Database::currentDb()->getMultiRecord($query, $t);
        return $records;
	}
	
	//　Load kpi_result_t by kpi, month
	public static function getKpiOneDay($id,$kpi_id,$month)
	{
		$id     = DbAgent::queryEncode($id,DbAgent::$DB_NUMBER);
		$kpi_id = DbAgent::queryEncode($kpi_id,DbAgent::$DB_NUMBER);
		$month  = DbAgent::queryEncode($month,DbAgent::$DB_NUMBER);

		$query = "SELECT id, kpi_id, month, day, result, mark
                FROM   kpi_result_t
                WhERE  id = $id
				AND kpi_id = $kpi_id
				AND month = $month
				ORDER BY id DESC;";

		$record = Database::currentDb()->getRecord($query);
        return $record;
	}
	
	// get user_id by kpi_id
	public static function getUserIdByKpiId($kpi_id) {
		$kpi_id = DbAgent::queryEncode($kpi_id, DbAgent::$DB_NUMBER);
		$query = "SELECT user_id
				FROM kpi_base_t
				WHERE kpi_id = $kpi_id;";
		$record = Database::currentDb()->getRecord($query);
		return $record['user_id'];
	}
}


 ?>