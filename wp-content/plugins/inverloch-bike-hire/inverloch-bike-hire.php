<?php
/*
Plugin Name: Inverloch Bike Hire Management System
Description: This is a bike hire management system. 
Version: 1.0
Author: 
*/

if (!defined('ABSPATH')) {
    die('You cannot be here');
}

global $ibk_table_prefix;
$ibk_table_prefix = 'wp_ibk_';

require_once(plugin_dir_path(__FILE__) . 'includes/plugin-activator.php');
register_activation_hook(__FILE__, 'plugin_activator');

require_once(plugin_dir_path(__FILE__) . 'includes/plugin-deactivator.php');
register_deactivation_hook(__FILE__, 'plugin_deactivator');

if (!class_exists('Inverloch_Bike_Hire')) 
{
    class Inverloch_Bike_Hire 
    {
        public function __construct() 
        {
            define('MY_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
        }

        public function initalize() 
        {
        }
    }

    $plugin = new Inverloch_Bike_Hire;
    $plugin->initalize();
}

