<?php
/**
 * Plugin Name: Inverloch Bike Hire
 * Plugin URI: http://example.com/inverloch-bike-hire
 * Description: This is a bike hire management system.
 * Version: 1.0
 * Author: Joe and Shubham
 * Author URI: http://example.com
 * Text Domain: inverloch-bike-hire
 * License: GPL-2.0+
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin version
define('INVERLOCH_BIKE_HIRE_VERSION', '1.0');

// Define plugin directory path and URL for easy access
define('INVERLOCH_BIKE_HIRE_DIR', plugin_dir_path(__FILE__));
define('INVERLOCH_BIKE_HIRE_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_inverloch_bike_hire() {
    require_once INVERLOCH_BIKE_HIRE_DIR . 'includes/db-operations.php';
    require_once INVERLOCH_BIKE_HIRE_DIR . 'includes/plugin-activator.php';
    ibh_activate_plugin(); // Function from plugin-activator.php
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_inverloch_bike_hire() {
    require_once INVERLOCH_BIKE_HIRE_DIR . 'includes/plugin-deactivator.php';
    ibh_deactivate_plugin(); // Function from plugin-deactivator.php
}

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'activate_inverloch_bike_hire');
register_deactivation_hook(__FILE__, 'deactivate_inverloch_bike_hire');

/**
 * Core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once INVERLOCH_BIKE_HIRE_DIR . 'admin/admin-init.php';
require INVERLOCH_BIKE_HIRE_DIR . 'public/public-init.php';

/**
 * Begin execution of the plugin.
 */
function run_inverloch_bike_hire() {
    // Place code here to run your plugin, such as adding hooks and filters,
    // initializing classes, or other setup tasks.
    // Set the timezone to Melbourne
    date_default_timezone_set('Australia/Melbourne');
}

run_inverloch_bike_hire();


?>