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
        action_sql TEXT NOT NULL
       ) $charset_collate;
       ";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
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
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }
    public static function insertSqlHistory($taskId, $sql)
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $wpdb->insert(self::getHistoryTable(), [ "development_task_id" => $taskId, "action_sql" => $sql]);
    }
    public static function insertNewTask($name)
    {
        if($name == "")
            return false;
        /** @var \wpdb $wpdb */
        global $wpdb;
        $result = $wpdb->insert(self::getDevelopmentTaskTable(), ["task_name" => $name]);
        if($result == false)
            return false;
        return true;
    }
}