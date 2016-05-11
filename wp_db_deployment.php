<?php
/**
Plugin Name: WP DB Deployment
Plugin URI: example.com
Description: Database tracking and deployment
Version: 0.0.1
Author: Jason Gallavin
Author URI: http://visualrdseed.com
License: GPLv2 or later
Text Domain: wp-db-deployment
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define('WP_DB_DEPLOYMENT_ENTRY', __FILE__);
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php");
register_activation_hook( __FILE__, array( 'wp_db_deployment\app\Main', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'wp_db_deployment\app\Main', 'plugin_deactivation' ) );
add_action( 'init', array( 'wp_db_deployment\app\Main', 'init' ) );