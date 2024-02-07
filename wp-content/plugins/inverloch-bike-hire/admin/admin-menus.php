<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registers the main menu and its submenus for the plugin.
 */
function ibh_register_admin_menus() {
    // Main plugin menu
    add_menu_page(
        'Bike Hire Management',           // Page title
        'Rental Dashboard',                      // Menu title
        'manage_options',                 // Capability required to see this menu
        'ibh_management',                 // Menu slug, used to uniquely identify the page
        'ibh_admin_dashboard',            // Function to output the content of the page
        'dashicons-admin-site-alt3',      // Icon URL, using a dashicon
        6                                 // Position in the menu order
    );

    // Submenu: Manage Categories
    add_submenu_page(
        'ibh_management',                 // Parent menu slug
        'Manage Categories',              // Page title
        'Categories',                     // Menu title
        'manage_options',                 // Capability required
        'ibh_categories',                 // Menu slug
        'ibh_manage_categories_page'      // Function to output the content of this page
    );

    // Submenu: Manage Inventory
    add_submenu_page(
        'ibh_management',
        'Manage Inventory',
        'Inventory',
        'manage_options',
        'ibh_inventory',
        'ibh_manage_inventory_page'
    );

    // Submenu: Manage Customers
    add_submenu_page(
        'ibh_management',
        'Manage Customers',
        'Customers',
        'manage_options',
        'ibh_customers',
        'ibh_manage_customers_page'
    );

    // Submenu: Manage Reservations
    add_submenu_page(
        'ibh_management',
        'Manage Reservations',
        'Reservations',
        'manage_options',
        'ibh_reservations',
        'ibh_manage_reservations_page'
    );

    // Submenu: Manage Blocked Dates
    add_submenu_page(
        'ibh_management',
        'Blocked Dates',
        'Blocked Dates',
        'manage_options',
        'ibh_blocked_dates',
        'ibh_manage_blocked_dates_page'
    );

    // Submenu: Manage Emails
    add_submenu_page(
        'ibh_management',
        'Email Templates',
        'Emails',
        'manage_options',
        'ibh_emails',
        'ibh_manage_emails_page'
    );

    // Submenu: Manage Invoices
   add_submenu_page(
        'ibh_management',
        'Invoices',
        'Invoices',
        'manage_options',
        'ibh_invoices',
        'ibh_manage_invoices_page'
    );

    // Additional submenus can be added here as needed.
}

/**
 * Placeholder functions for each admin page.
 * Actual content and functionality will be defined in separate files within the /pages directory.
 */
function ibh_admin_dashboard() {
    include plugin_dir_path(__FILE__) . 'pages/dashboard-page.php';
}

function ibh_manage_categories_page() {
    include plugin_dir_path(__FILE__) . 'pages/categories-page.php';
}

function ibh_manage_inventory_page() {
    include plugin_dir_path(__FILE__) . 'pages/inventory-page.php';
}

function ibh_manage_customers_page() {
    include plugin_dir_path(__FILE__) . 'pages/customers-page.php';
}

function ibh_manage_reservations_page() {
    include plugin_dir_path(__FILE__) . 'pages/reservations-page.php';
}

function ibh_manage_blocked_dates_page() {
    include plugin_dir_path(__FILE__) . 'pages/blocked-dates-page.php';
}

function ibh_manage_emails_page() {
    include plugin_dir_path(__FILE__) . 'pages/emails-page.php';
}

function ibh_manage_invoices_page() {
    include plugin_dir_path(__FILE__) . 'pages/invoices-page.php';
}

// Hook the above function into the 'admin_menu' action
add_action('admin_menu', 'ibh_register_admin_menus');
