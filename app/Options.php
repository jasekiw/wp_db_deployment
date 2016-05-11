<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/10/2016
 * Time: 8:29 PM
 */

namespace wp_db_deployment\app;

class Options
{
    protected static $options;
    public static $optionsName = "wp_db_deployment_options";
    protected static $currentDevTask;

    protected static function initTheOptions()
    {
        if(isset(self::$options))
            return;
        $options = get_option(self::$optionsName);
        if($options === false)
        {
            self::$options = self::addOptions();
        }
        else
        {
            self::$options = unserialize($options);
        }

    }

    /**
     * adds the options to the database
     * @return array
     */
    protected static function addOptions()
    {
        $options = [
            "current_dev_task" => null,
        ];
        add_option(self::$optionsName,serialize($options));
        return $options;
    }
    protected static function save()
    {
        if(!isset(self::$options))
            return;
        update_option(self::$optionsName, serialize(self::$options));
    }
    public static function getCurentDevTask()
    {
        if(isset(self::$currentDevTask))
            return self::$currentDevTask;

        self::initTheOptions();
        $currentDevTaskId = self::$options['current_dev_task'];
        if($currentDevTaskId == null)
            return null;
        /** @var \wpdb $wpdb */
        global $wpdb;
        $table = DB::getDevelopmentTaskTable();
        $row = $wpdb->get_row(" SELECT * FROM {$table} WHERE id = {$currentDevTaskId};");
        return $row;
    }


    public static function getDevTasks()
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $table = DB::getDevelopmentTaskTable();
        $rows = $wpdb->get_results(" SELECT * FROM {$table}");
        return $rows;
    }
    public static function setCurrentDevTask($id)
    {
        self::initTheOptions();
        if(empty($id))
            self::$options['current_dev_task'] = null;
        else
            self::$options['current_dev_task'] = $id;
        self::save();
    }

}