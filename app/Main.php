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
        $this->dbFilter = new DBFilter();
        $this->adminPage = new AdminPage();

        $this->setupActions();
        $this->setUpFilters();
    }

    public static function init()
    {
        self::$singelton = new Main();
    }

    protected function setUpFilters()
    {
        add_filter('query', [$this->dbFilter, 'filterQuery']);
    }

    protected function setupActions()
    {
        add_action('admin_menu', [$this->adminPage, 'hookMenu']);
        add_action('admin_init', [$this->adminPage, 'registerSettings'] );
        add_action( 'admin_post_wp_db_deployment_add_dev_task', [$this->adminPage, 'handleSaveNewDevTask'] );
        add_action( 'admin_post_wp_db_deployment_save_current_dev_task', [$this->adminPage, 'handleSaveCurrentDevTask'] );
        if(isset($_GET['page']) && $_GET['page'] == AdminPage::$optionsPageUri && isset($_GET['settings-updated']) )
            add_action( 'admin_notices', [$this->adminPage, 'notice']);

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