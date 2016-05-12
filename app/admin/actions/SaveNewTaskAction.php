<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/11/2016
 * Time: 5:54 PM
 */

namespace wp_db_deployment\app\admin\actions;

use wp_db_deployment\app\DB;

class SaveNewTaskAction extends Action
{

    public function handle()
    {
        parent::handle();
        $params = $this->requiresAll(['new_task']);
        $newTask = $params["new_task"];
        $this->redirectToOptionWithMessage( DB::insertNewTask($newTask));
    }
}