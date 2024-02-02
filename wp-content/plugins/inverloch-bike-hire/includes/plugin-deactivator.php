<?php
// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

function ibh_deactivate_plugin() {
    global $wpdb;
    $ibk_table_prefix = 'wp_ibk_';

    // List of table names
    $tables = [
        'item_booking',
        'reservation',
        'price_point',
        'item',
        'category',
        'customer',
        'invoice',
        'blocked_date',
        'email',
    ];

    foreach ($tables as $table) {
        $table_name = $ibk_table_prefix . $table;
        $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
    }
}
