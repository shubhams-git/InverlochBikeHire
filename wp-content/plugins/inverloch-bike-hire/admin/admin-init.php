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
        wp_enqueue_style(
            'ibh-admin-css',
            plugins_url('/assets/css/admin.css', dirname(__FILE__)),
            [],
            filemtime(plugin_dir_path(dirname(__FILE__)) . '/assets/css/admin.css')
        );

        wp_enqueue_script('jquery-ui-dialog');
        
        wp_enqueue_style('wp-jquery-ui-dialog');


        wp_enqueue_script(
            'ibh-admin-js',
            plugins_url('/assets/js/admin.js', dirname(__FILE__)),
            ['jquery'],
            filemtime(plugin_dir_path(dirname(__FILE__)) . '/assets/js/admin.js'),
            true
        );

        // Localize the script with new data
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
