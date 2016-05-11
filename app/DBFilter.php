<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/10/2016
 * Time: 6:25 PM
 */

namespace wp_db_deployment\app;

class DBFilter
{

    function __construct()
    {
    }

    public function filterQuery($sql)
    {
        if($this->shouldIgnore($sql))
            return $sql;
        $devTask = Options::getCurentDevTask();
        DB::insertSqlHistory($devTask->id, $sql);
        return $sql;
    }

    /**
     * determins if the sql should be ignored by the plugin
     * @param string $sql
     *
     * @return bool
     */
    protected function shouldIgnore($sql)
    {
        if(strpos(strtoupper($sql),"SELECT ") !== false )
            return true;
        if(strpos(strtoupper($sql),"SHOW FULL COLUMNS FROM") !== false )
            return true;
        $tables = $this->getTables($sql);
        if (in_array('options', $tables) && strpos($sql, Options::$optionsName) !== false)
            return true;
        if (in_array($this->getUnprefixedTableName(DB::getDevelopmentTaskTable()), $tables) ||
            in_array($this->getUnprefixedTableName(DB::getHistoryTable()), $tables)
        )
            return true;

        $devTask = Options::getCurentDevTask();
        if (!isset($devTask))
            return true;
        return false;
    }

    protected function getRelationships()
    {
        /** @var \wpdb $wpdb */
        global $wpdb;

        $relationships = [
            "postmeta"    => [
                "post_id" => [
                    "posts" => "ID"
                ]
            ],
            "comments"    => [
                "comment_post_ID" => [
                    "posts" => "ID"
                ]
            ],
            "commentmeta" => [
                "comment_id" => [
                    "comments" => "comment_ID"
                ]
            ]
        ];
        return apply_filters('wp_db_deployment_relationships', $relationships);
    }

    /**
     * @param string $sql
     *
     * @return string[]
     */
    protected function getTables($sql)
    {
        $matches = [];
        preg_match_all('/(?<=from|join|into|update)\s+`?(\w+\b)`?/i', $sql, $matches);
        $tableNames = [];
        foreach ($matches[1] as $tableName)
            $tableNames[] = $this->getUnprefixedTableName(trim($tableName));
        return $tableNames;
    }

    /**
     * Gets the unprefixed version of a table name
     *
     * @param string $tableName
     *
     * @return string
     */
    protected function getUnprefixedTableName($tableName)
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $prefixPos = strpos($tableName, $wpdb->prefix);
        if ($prefixPos === 0)
            return substr($tableName, strlen($wpdb->prefix));
        return $tableName;
    }
}