<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/11/2016
 * Time: 7:49 PM
 */

namespace wp_db_deployment\app\sqlParser;

class SqlToken
{

    public $type = "";
    public $subtype = "";
    public $value = "";

    /**
     * SqlToken constructor.
     *
     * @param int|float|double|string $value
     * @param string $type
     * @param string $subtype
     */
    function __construct($value, $type, $subtype)
    {
        $this->value = $value;
        $this->type = $type;
        $this->subtype = $subtype;
    }
}