<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/11/2016
 * Time: 7:48 PM
 */

namespace wp_db_deployment\app\sqlParser;

use PHPSQLParser\PHPSQLParser;

class SqlLexer
{

    protected $currentIndex = 0;
    protected $sql = "";
    protected $currentNextValue = "";
    protected $tokens = [];
    function __construct()
    {
        
    }
    
    public function parse($sql)
    {
//        $this->sql = $sql;
//        for($index = 0; $index < strlen($sql); $index++)
//        {
//            $this->currentIndex = $index;
//
//            if($this->check("select", false))
//            {
//                $tokens[] = new SqlToken($this->get(), "command", "SELECT");
//            }
////            else if($this->check('update'), false))
////            {
////
////            }
//        }

       
        
    }


    public function check($for, $caseSensitive = true)
    {
        $nextValue = substr($this->sql,$this->currentIndex, strlen($for));
        $checker = $nextValue;
        if(!$caseSensitive)
        {
            $checker = strtoupper($checker);
            $for = strtoupper($nextValue);
        }

        if($checker == $for)
        {
            $this->currentNextValue = $nextValue;
            return true;
        }
        else
            return false;
    }
    public function get()
    {
        return $this->currentNextValue;
    }
}