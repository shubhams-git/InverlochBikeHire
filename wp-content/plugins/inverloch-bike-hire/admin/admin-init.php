<?php
/**
 * Admin Initialization File for Inverloch Bike Hire Plugin
 *
 * Handles the initialization of admin-specific functionalities including
 * enqueuing scripts and styles, setting up admin menus, and registering AJAX actions.
 *
 * @package InverlochBikeHire
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the admin menu setup and form handlers.
require_once plugin_dir_path(__FILE__) . 'admin-menus.php';
require_once plugin_dir_path(__DIR__) . 'includes/form-handlers.php';

/**
 * Enqueues admin-specific scripts and styles.
 */
function ibh_enqueue_admin_scripts_styles() {
    // Enqueue the admin CSS
    wp_enqueue_style(
        'ibh-admin-css',
        plugins_url('/assets/css/admin.css', dirname(__FILE__)),
        [],
        filemtime(plugin_dir_path(dirname(__FILE__)) . '/assets/css/admin.css')
    );

    // Enqueue jQuery UI styles directly from Google's CDN for consistent UI across the admin
    wp_enqueue_style('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css');

    // Enqueue the jQuery UI Dialog script as it's a dependency for our scripts
    wp_enqueue_script('jquery-ui-dialog');
    
    // Enqueue the jQuery UI Datepicker script, which we may need for date inputs
    wp_enqueue_script('jquery-ui-datepicker');

    // Enqueue Timepicker addon if you need time selection functionality
    wp_enqueue_style('jquery-timepicker', '//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css');
    wp_enqueue_script('jquery-timepicker', '//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js', array('jquery'), '1.3.5', true);

    // Enqueue custom admin JavaScript
    wp_enqueue_script(
        'ibh-admin-js',
        plugins_url('/assets/js/admin.js', dirname(__FILE__)),
        ['jquery', 'jquery-ui-dialog', 'jquery-ui-datepicker', 'jquery-timepicker'], // Ensure jQuery and jQuery UI components are loaded as dependencies
        filemtime(plugin_dir_path(dirname(__FILE__)) . '/assets/js/admin.js'),
        true
    );

    // Localize the script with new data for AJAX requests
    wp_localize_script('ibh-admin-js', 'myAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'adminUrl' => admin_url('admin.php'),
        'nonce' => wp_create_nonce('ibh_form_nonce')
    ]);
}
add_action('admin_enqueue_scripts', 'ibh_enqueue_admin_scripts_styles');

/**
 * Initializes the plugin's admin functionalities.
 */
function ibh_admin_init() {
    // Admin scripts and styles are enqueued via the hook, so no need to call directly here.
}

ibh_admin_init();
