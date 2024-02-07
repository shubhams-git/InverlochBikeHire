<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}
// Include the database operations file to use its functions
include_once plugin_dir_path(__FILE__) . 'db-operations.php';

function ibh_deactivate_plugin() {
    // Call the function from db-operations.php to drop the tables
    ibh_drop_db_tables();
}
