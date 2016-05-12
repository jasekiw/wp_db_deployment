<?php
namespace wp_db_deployment\app;

use wp_db_deployment\app\admin\AdminPage;

/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 5/10/2016
 * Time: 6:12 PM
 */
class Main
{

    private $dbFilter;
    protected static $singelton;
    protected $adminPage;
    public static $textDomain = "wp-db-deployment";
    function __construct()
    {
        if(!class_exists('PHPSQLParser\PHPSQLParser',false))
        {
            $ds = DIRECTORY_SEPARATOR;
            require_once(dirname(dirname(__FILE__)) . "{$ds}dependencies{$ds}greenlion{$ds}vendor{$ds}autoload.php" );
        }
        $this->dbFilter = new DBFilter();
        $this->adminPage = new AdminPage();

        $this->setupActions();
        $this->setUpFilters();
        /** @var \wpdb  */
        global $wpdb;
        $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_options WHERE option_id = %d AND option_value = %s",[ 5, "mushrooms"] ));

    }

    public static function init()
    {
        if(!isset(self::$singelton))
            self::$singelton = new Main();
    }

    protected function setUpFilters()
    {
        add_filter('query', [$this->dbFilter, 'filterQuery']);
    }
    public function QueryCount()
    {
        echo "\r\n" . DBFilter::$queryCount . "\r\n";

    }

    protected function setupActions()
    {
        add_action('admin_menu', [$this->adminPage, 'hookMenu']);
        add_action('admin_init', [$this->adminPage, 'registerSettings'] );
        add_action( 'admin_post_wp_db_deployment_add_dev_task', [$this->adminPage->saveNewTask, 'handle'] );
        add_action( 'admin_post_wp_db_deployment_save_current_dev_task', [$this->adminPage->saveCurrentTask, 'handle'] );
        add_action( 'admin_post_wp_db_deployment_delete_dev_task', [$this->adminPage->deleteTask, 'handle'] );
        if(isset($_GET['page']) && $_GET['page'] == AdminPage::$optionsPageUri && isset($_GET['settings-updated']) )
            add_action( 'admin_notices', [$this->adminPage, 'notice']);
//        add_action('shutdown', [$this, 'QueryCount']);

        add_action( 'admin_post_wp_db_deployment_export_dev_task', [$this->adminPage->exportTaskChanges, 'handle']);
        add_action( 'admin_post_wp_db_deployment_import_dev_task', [$this->adminPage->importTaskChanges, 'handle']);
        add_action('shutdown', [DBFilter::class, 'checkLastInsertId']);
    }

    public static function plugin_activation()
    {
        DB::createDevelopmentTaskTable();
        DB::createHistoryTable();
    }
    
    public static function plugin_deactivation()
    {
    }

}