<?php
// Protection against direct access
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include the database operations file to use its functions
include_once plugin_dir_path(__FILE__) . 'db-operations.php';

function ibh_activate_plugin() {
    // Call the function to create database tables
    ibh_create_db_tables();

    // Optionally, here you can also insert any initial data needed for your plugin
    // For example, default categories, initial settings, etc.
    // This is a good place to set default options or other initial settings for your plugin.
}

// Register the activation function
register_activation_hook(plugin_dir_path(__DIR__) . 'inverloch-bike-hire.php', 'ibh_activate_plugin');

?>