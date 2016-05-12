<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/11/2016
 * Time: 6:02 PM
 */

namespace wp_db_deployment\app\admin\actions;

use wp_db_deployment\app\DB;

class DeleteTaskAction extends Action
{
    public function handle()
    {
        parent::handle();
        $params = $this->requiresAll(['task_id']);
        $successful = DB::deleteDevTask($params['task_id']);
        $this->redirectToOptionWithMessage($successful);
    }
}