<?php
/**
 * Admin Initialization File for Inverloch Bike Hire Plugin
 *
 * Handles the initialization of admin-specific functionalities including
 * enqueuing scripts and styles and setting up admin menus.
 *
 * @package InverlochBikeHire
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Include the admin menu setup.
require_once plugin_dir_path( __FILE__ ) . 'admin-menus.php';

/**
 * Enqueues admin-specific scripts and styles.
 *
 * This function is designed to only enqueue scripts and styles for the plugin's
 * own admin pages, to avoid conflicts and unnecessary loading on other admin pages.
 */
function ibh_enqueue_admin_scripts_styles() {
    add_action( 'admin_enqueue_scripts', function ( $hook_suffix ) {
        $hook_suffixes = [
            'toplevel_page_ibh_management',
            'bike-hire_page_ibh_categories',
            'bike-hire_page_ibh_inventory',
            'bike-hire_page_ibh_customers',
            'bike-hire_page_ibh_reservations',
            'bike-hire_page_ibh_blocked_dates',
            'bike-hire_page_ibh_emails',
            'bike-hire_page_ibh_invoices',
        ];

        if ( in_array( $hook_suffix, $hook_suffixes, true ) ) {
            wp_enqueue_style(
                'ibh-admin-css',
                plugins_url( '/assets/css/admin.css', dirname( __FILE__ ) ),
                [],
                filemtime( plugin_dir_path( dirname( __FILE__ ) ) . '/assets/css/admin.css' )
            );

            wp_enqueue_script(
                'ibh-admin-js',
                plugins_url( '/assets/js/admin.js', dirname( __FILE__ ) ),
                [ 'jquery' ],
                filemtime( plugin_dir_path( dirname( __FILE__ ) ) . '/assets/js/admin.js' ),
                true
            );
        }
    } );
}

/**
 * Initializes the plugin's admin functionalities.
 *
 * Calls functions to enqueue styles and scripts for admin pages and could be
 * extended to include other admin initializations.
 */
function ibh_admin_init() {
    ibh_enqueue_admin_scripts_styles();
}

ibh_admin_init();
