<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/10/2016
 * Time: 6:49 PM
 */

namespace wp_db_deployment\app;

class DB
{

    protected static $historyTableName;
    protected static $taskTableName;

    /**
     * Gets the history table name
     * @return string
     */
    public static function getHistoryTable()
    {
        if (isset(self::$historyTableName))
            return self::$historyTableName;
        /** @var \wpdb $wpdb */
        global $wpdb;
        self::$historyTableName = $wpdb->prefix . "db_deployment_history";
        return self::$historyTableName;
    }

    /**
     * Gets the development tasks table name
     * @return string
     */
    public static function getDevelopmentTaskTable()
    {
        if (isset(self::$taskTableName))
            return self::$taskTableName;
        /** @var \wpdb $wpdb */
        global $wpdb;
        self::$taskTableName = $wpdb->prefix . "db_deployment_task";
        return self::$taskTableName;
    }

    public static function createHistoryTable()
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table = self::getHistoryTable();
        $sql = "
       CREATE TABLE IF NOT EXISTS {$table}
       (    
        id INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
        development_task_id INT NOT NULL,
        sql_value TEXT NOT NULL,
        type VARCHAR(20) NOT NULL,
        inserted_ID INT
       ) $charset_collate;
       ";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function createDevelopmentTaskTable()
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table = self::getDevelopmentTaskTable();
        $sql = "
       CREATE TABLE IF NOT EXISTS {$table}
       (    
        id INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
        task_name VARCHAR(60) NOT NULL
       ) $charset_collate;
       ";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function insertSqlHistory($taskId, $sql, $type = null, $insertedId = null)
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        if(isset($insertedId) && isset($type))
        {
            $wpdb->insert(self::getHistoryTable(), [
                "development_task_id" => $taskId,
                "sql_value"           => $sql,
                "type"                => $type,
                "inserted_ID"         => $insertedId
            ]);
        }
        else if (isset($type)) {
            $wpdb->insert(self::getHistoryTable(), [
                "development_task_id" => $taskId,
                "sql_value"           => $sql,
                "type"                => $type
            ]);
        } else
            $wpdb->insert(self::getHistoryTable(), [
                "development_task_id" => $taskId,
                "sql_value"           => $sql,
                "type"                => "OTHER"
            ]);
    }

    public static function insertNewTask($name)
    {
        if ($name == "")
            return false;
        /** @var \wpdb $wpdb */
        global $wpdb;
        $result = $wpdb->insert(self::getDevelopmentTaskTable(), ["task_name" => $name]);
        if ($result == false)
            return false;
        return true;
    }

    public static function deleteDevTask($id)
    {
        if ($id = "" || !is_numeric($id))
            return false;
        /** @var \wpdb $wpdb */
        global $wpdb;
        $result = $wpdb->delete(self::getDevelopmentTaskTable(), ["id" => $id]);
        if ($result == false)
            return false;
        return true;
    }

    public static function getAllSqlHistory()
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $deploymentHistoryTable = self::getHistoryTable();
        $developmentTaskId = Options::getCurentDevTask()->id;
        $rows = $wpdb->get_results("SELECT * FROM {$deploymentHistoryTable} WHERE development_task_id = {$developmentTaskId}");
        return $rows;
    }
    public static function getAllSqlHistoryAsArrays()
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $deploymentHistoryTable = self::getHistoryTable();
        $developmentTaskId = Options::getCurentDevTask()->id;
        $rows = $wpdb->get_results("SELECT * FROM {$deploymentHistoryTable} WHERE development_task_id = {$developmentTaskId}", "ARRAY_A");
        return $rows;
    }

    public static function getAllSqlHistoryValues()
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $deploymentHistoryTable = self::getHistoryTable();
        $developmentTaskId = Options::getCurentDevTask()->id;
        $rows = $wpdb->get_results("SELECT sql_value as value FROM {$deploymentHistoryTable} WHERE development_task_id = {$developmentTaskId}");
        return $rows;
    }
}