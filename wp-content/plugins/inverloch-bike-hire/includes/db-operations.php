<?php
// Protection against direct access
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $ibk_table_prefix;
$ibk_table_prefix = 'wp_ibk_';

function ibh_create_db_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Category Table
    $sql_category = "CREATE TABLE {$wpdb->prefix}ibk_category (
        category_id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
        category_name VARCHAR(255) NOT NULL,
        PRIMARY KEY  (category_id)
    ) $charset_collate;";

    // Item Table
    $sql_item = "CREATE TABLE {$wpdb->prefix}ibk_item (
        item_id VARCHAR(255) NOT NULL,
        category_id MEDIUMINT(9) NOT NULL,
        size VARCHAR(10) NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        created_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
        image_url VARCHAR(255) NOT NULL,
        status ENUM('available', 'unavailable') NOT NULL,
        PRIMARY KEY  (item_id),
        FOREIGN KEY (category_id) REFERENCES {$wpdb->prefix}ibk_category(category_id) ON DELETE CASCADE
    ) $charset_collate;";

    // Customer Table
    $sql_customer = "CREATE TABLE {$wpdb->prefix}ibk_customer (
        customer_id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
        fname VARCHAR(50) NOT NULL,
        lname VARCHAR(50) NOT NULL,
        email VARCHAR(255),
        mobile_phone VARCHAR(50) NOT NULL,
        address TEXT,
        PRIMARY KEY  (customer_id)
    ) $charset_collate;";

    // Price Point Table
    $sql_price_point = "CREATE TABLE {$wpdb->prefix}ibk_price_point (
        price_point_id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
        category_id MEDIUMINT(9) NOT NULL,
        timeframe VARCHAR(255) NOT NULL,
        amount FLOAT NOT NULL,
        PRIMARY KEY  (price_point_id),
        FOREIGN KEY (category_id) REFERENCES {$wpdb->prefix}ibk_category(category_id) ON DELETE CASCADE
    ) $charset_collate;";

    // Invoice Table
    $sql_invoice = "CREATE TABLE {$wpdb->prefix}ibk_invoice (
        invoice_id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
        method ENUM('cash', 'card') NOT NULL,
        amount FLOAT NOT NULL,
        PRIMARY KEY  (invoice_id)
    ) $charset_collate;";

    // Reservation Table
    $sql_reservation = "CREATE TABLE {$wpdb->prefix}ibk_reservation (
        reservation_id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
        customer_id MEDIUMINT(9) NOT NULL,
        invoice_id MEDIUMINT(9) NOT NULL,
        from_date DATETIME NOT NULL,
        to_date DATETIME NOT NULL,
        reservation_stage ENUM('provisional', 'confirmed', 'checked-out', 'checked-in') NOT NULL,
        created_date DATETIME NOT NULL,
        delivery_notes TEXT,
        PRIMARY KEY  (reservation_id),
        FOREIGN KEY (customer_id) REFERENCES {$wpdb->prefix}ibk_customer(customer_id) ON DELETE CASCADE,
        FOREIGN KEY (invoice_id) REFERENCES {$wpdb->prefix}ibk_invoice(invoice_id) ON DELETE CASCADE
    ) $charset_collate;";

    // Blocked Date Table
    $sql_blocked_date = "CREATE TABLE {$wpdb->prefix}ibk_blocked_date (
        date DATE NOT NULL,
        is_blocked BOOLEAN NOT NULL,
        PRIMARY KEY  (date)
    ) $charset_collate;";

    // Item Booking Table
    $sql_item_booking = "CREATE TABLE {$wpdb->prefix}ibk_item_booking (
        reservation_id MEDIUMINT(9) NOT NULL,
        item_id VARCHAR(255) NOT NULL,
        PRIMARY KEY (reservation_id, item_id),
        FOREIGN KEY (reservation_id) REFERENCES {$wpdb->prefix}ibk_reservation(reservation_id) ON DELETE CASCADE,
        FOREIGN KEY (item_id) REFERENCES {$wpdb->prefix}ibk_item(item_id) ON DELETE CASCADE
    ) $charset_collate;";

    // Email Table
    $sql_email = "CREATE TABLE {$wpdb->prefix}ibk_email (
        email_id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
        subject VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        PRIMARY KEY (email_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_category);
    dbDelta($sql_item);
    dbDelta($sql_customer);
    dbDelta($sql_price_point);
    dbDelta($sql_invoice);
    dbDelta($sql_reservation);
    dbDelta($sql_blocked_date);
    dbDelta($sql_item_booking);
    dbDelta($sql_email);
}
