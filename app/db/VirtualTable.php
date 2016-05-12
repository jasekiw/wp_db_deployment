<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/11/2016
 * Time: 7:07 PM
 */

namespace wp_db_deployment\app\db;

class VirtualTable
{

    private $table = "";
    private $columns;
    /** @var mixed[][] $rows */
    private $rows;

    /**
     * VirtualTable constructor.
     *
     * @param string   $table
     * @param string[] $columns
     * @param int [][] | string [][] | bool[][] | float [][] $rows
     *
     */
    function __construct($table, $columns, $rows)
    {
        $this->table = $table;
        $this->columns = $columns;
        $this->rows = $rows;
    }
}