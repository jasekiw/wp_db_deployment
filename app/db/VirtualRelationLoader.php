<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/11/2016
 * Time: 7:22 PM
 */

namespace wp_db_deployment\app\db;

use wp_db_deployment\app\DB;

class VirtualRelationLoader
{
    function __construct()
    {
        
        $virtalManager = null;
        
        $sqlHIstories = DB::getAllSqlHistory();

        foreach ($sqlHIstories as $SqlHistory)
        {
            $id = $SqlHistory->id;
            $development_task_id = $SqlHistory->development_task_id;
            $sql = $SqlHistory->sql_value;
            $columns = [];
            
            
        }
    }
}