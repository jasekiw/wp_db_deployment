<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/11/2016
 * Time: 7:06 PM
 */

namespace wp_db_deployment\app\db;

class VirtualRelationManager
{
    /** @var VirtualTable [] */
    private $tables;

    /**
     * VirtualRelationManager constructor.
     *
     * @param VirtualTable[] $tables
     */
    function __construct($tables)
    {
        $this->tables = $tables;
    }
}