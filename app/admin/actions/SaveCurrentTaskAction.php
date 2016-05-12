<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/11/2016
 * Time: 5:34 PM
 */

namespace wp_db_deployment\app\admin\actions;

use wp_db_deployment\app\Options;

class SaveCurrentTaskAction extends Action
{
    public function handle()
    {
        parent::handle();
        $params = $this->requiresAll(["current_task"]);
        $currentTask = $params["current_task"];
        Options::setCurrentDevTask($currentTask);
        $this->redirectToOptionWithMessage(true);
    }

}