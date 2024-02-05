<?php

// Database Table Creation
function plugin_activator() {
    // Include the file with database operations
    include_once('db-operations.php');

    // Call the functions for table creation and data insertion
    create_plugin_tables();    
}
