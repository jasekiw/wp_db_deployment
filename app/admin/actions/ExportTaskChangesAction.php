<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/11/2016
 * Time: 10:18 PM
 */

namespace wp_db_deployment\app\admin\actions;

use PHPSQLParser\PHPSQLParser;
use wp_db_deployment\app\DB;
use wp_db_deployment\app\Options;

class ExportTaskChangesAction extends Action
{
    public function handle()
    {
        parent::handle();
        $histories = DB::getAllSqlHistoryAsArrays();
        $export = json_encode($histories);
        $file = "export.xprt";
        $quoted = sprintf('"%s"', addcslashes(basename($file), '"\\'));
        $size   = strlen($export);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $quoted);
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $size);
        echo $export;
        exit;
    }
}