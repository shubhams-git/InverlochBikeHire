<?php

function plugin_deactivator() {
    // Include the file with database operations
    include_once('db-operations.php');

    // Call the function for table deletion
    delete_plugin_tables();
}
