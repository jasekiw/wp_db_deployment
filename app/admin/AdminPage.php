<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/10/2016
 * Time: 9:13 PM
 */

namespace wp_db_deployment\app\admin;

use wp_db_deployment\app\admin\actions\DeleteTaskAction;
use wp_db_deployment\app\admin\actions\ExportTaskChangesAction;
use wp_db_deployment\app\admin\actions\ImportTaskAction;
use wp_db_deployment\app\admin\actions\SaveCurrentTaskAction;
use wp_db_deployment\app\admin\actions\SaveNewTaskAction;
use wp_db_deployment\app\DB;
use wp_db_deployment\app\Main;
use wp_db_deployment\app\Options;

class AdminPage
{

    public $saveCurrentTask;
    public $saveNewTask;
    public $deleteTask;
    public $exportTaskChanges;
    public $importTaskChanges;
    public static $optionsPageUri = "wp-db-deployment-options";

    function __construct()
    {
        $this->saveCurrentTask = new SaveCurrentTaskAction();
        $this->saveNewTask = new SaveNewTaskAction();
        $this->deleteTask = new DeleteTaskAction();
        $this->exportTaskChanges = new ExportTaskChangesAction();
        $this->importTaskChanges = new ImportTaskAction();
//        $this->exportTaskChanges
    }

    public function hookMenu()
    {
        add_options_page('DB Deployment Options', 'DB Deployment Options', 'manage_options', self::$optionsPageUri, [
            $this,
            'settingsPage'
        ]);
    }

    function notice()
    {
        $successful = boolval($_GET["settings-updated"]);
        $class = "";
        if ($successful)
            $class = 'notice notice-success';
        else
            $class = 'notice notice-error';

        $message = __($_GET['message'], Main::$textDomain);

        printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
    }

    public function registerSettings()
    {
        //register our settings

    }
    
    public function settingsPage()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        $devTasks = Options::getDevTasks();
        $currentTask = Options::getCurentDevTask()
        ?>
        <div class="wrap">
            <h2>WP DB Deploymenet Settings</h2>

            <form id="wp_db_deplyment_save_current_dev_Task" method="post" action="admin-post.php">
                <input type="hidden" name="action" value="wp_db_deployment_save_current_dev_task"/>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Current Dev Task</th>
                        <td>
                            <select name="current_task">
                                <option value="">No Developer task</option>
                                <?php
                                foreach ($devTasks as $task) {
                                    if (isset($currentTask) && $task->id == $currentTask->id) {
                                        ?>
                                        <option selected="selected"
                                                value="<?php echo $task->id ?>"><?php echo $task->task_name ?></option><?php
                                    } else {
                                        ?>
                                        <option
                                        value="<?php echo $task->id ?>"><?php echo $task->task_name ?></option><?php
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <?php submit_button("Save Current Dev Task"); ?>

            </form>

            <h2>Create New Developer Task</h2>
            <form method="post" action="admin-post.php">
                <input type="hidden" name="action" value="wp_db_deployment_add_dev_task"/>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">New Task Name</th>
                        <td><input type="text" name="new_task"/></td>
                    </tr>
                </table>

                <?php submit_button('Add Developer Task'); ?>

            </form>

            <h2>Export changes from current task</h2>
            <form method="post" action="admin-post.php">
                <input type="hidden" name="action" value="wp_db_deployment_export_dev_task"/>
                <?php submit_button('Export'); ?>

            </form>

            <h2>Import Changes</h2>
            <form method="post" action="admin-post.php" enctype="multipart/form-data" >
                <input type="hidden" name="action" value="wp_db_deployment_import_dev_task"/>
                <input type="file" name="import_file" />
                <?php submit_button('Import'); ?>
            </form>
        </div>
    <?php }
}