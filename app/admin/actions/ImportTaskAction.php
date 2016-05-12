<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/11/2016
 * Time: 10:38 PM
 */

namespace wp_db_deployment\app\admin\actions;

use PHPSQLParser\PHPSQLParser;

class ImportTaskAction extends Action
{
    public function handle()
    {
        parent::handle();
        $files =$this->requiresFiles(['import_file']);
        $filelocation = $files['import_file']['tmp_name'];
        /** @var string[] $importData */
        $importQueries = json_decode(file_get_contents($filelocation), true);
        $parser = new PHPSQLParser();
        $inserts = [];
        $querires = [];
        foreach($importQueries as $query)
        {
            $parsed = $parser->parse($query['sql_value']);
            if(isset($parsed['INSERT']) && $query['type'] == "INSERT")
            {
                $insertedId = $query['inserted_ID'];
                $inserts[$insertedId] = $query;
            }
            $querires[] = $query;
        }
        $this->redirectToOptionWithMessage(true, "Successfully Imported");

    }
}