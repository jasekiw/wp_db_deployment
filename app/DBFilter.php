<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/10/2016
 * Time: 6:25 PM
 */

namespace wp_db_deployment\app;

use PHPSQLParser\PHPSQLParser;

class DBFilter
{

    public static $queryCount = 0;
    public static $getLastInsertId = false;
    public static $lastHistoryId = 0;

    function __construct()
    {
    }

    /**
     * Query hook just before Query
     *
     * @param string $sql
     *
     * @return string
     */
    public function filterQuery($sql)
    {
        self::checkLastInsertId();
        if (strpos($sql, "mushrooms") !== false) {
            $test = "";
        }
        $parser = new PHPSQLParser();
        $result = $parser->parse($sql, true);
        self::$queryCount++;
        if ($this->shouldIgnore($sql))
            return $sql;
        $devTask = Options::getCurentDevTask();
        $isInsert = $this->isInsert($sql);
        if ($isInsert) {
            DB::insertSqlHistory($devTask->id, $sql, "INSERT");
            /** @var \wpdb $wpdb */
            global $wpdb;
            self::$lastHistoryId = $wpdb->insert_id;
            self::$getLastInsertId = true;
        } else
            DB::insertSqlHistory($devTask->id, $sql);

        return $sql;
    }

    public static function checkLastInsertId()
    {
        if (!self::$getLastInsertId)
            return;

        /** @var \wpdb $wpdb */
        global $wpdb;
        $new_insert_id = $wpdb->insert_id;
        self::$getLastInsertId = false;
        $wpdb->update(DB::getHistoryTable(), ["inserted_ID" => $new_insert_id], ['id' => self::$lastHistoryId]);
    }

    public function isInsert($sql)
    {
        if (strpos(strtoupper($sql), "INSERT ") !== false)
            return true;
        return false;
    }

    /**
     * determins if the sql should be ignored by the plugin
     *
     * @param string $sql
     *
     * @return bool
     */
    protected function shouldIgnore($sql)
    {
        if (strpos(strtoupper($sql), "SELECT ") !== false)
            return true;
        if (strpos(strtoupper($sql), "SHOW FULL COLUMNS FROM") !== false)
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
    public function getTables($sql)
    {
        $matches = [];
        preg_match_all('/(?<=from|join|into|update)\s+`?(\w+\b)`?/i', $sql, $matches);
        $tableNames = [];
        foreach ($matches[1] as $tableName)
            $tableNames[] = $this->getUnprefixedTableName(trim($tableName));
        return $tableNames;
    }
    
    /**
     * @param string $sql
     */
    public function getColumns($sql)
    {
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