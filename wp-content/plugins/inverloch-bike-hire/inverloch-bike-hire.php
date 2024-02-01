<?php
/*
Plugin Name: Inverloch Bike Hire Management System
Description: This is a bike hire management system. 
Version: 1.0
Author: 
*/

if (!defined('ABSPATH')) {
    die('You cannot be here');
}

global $ibk_table_prefix;
$ibk_table_prefix = 'wp_ibk_';

register_activation_hook(__FILE__, 'your_plugin_activate');
register_deactivation_hook(__FILE__, 'your_plugin_deactivate');

// Database Table Creation
function your_plugin_activate() {
    global $wpdb, $ibk_table_prefix;
    $charset_collate = $wpdb->get_charset_collate();

    // CREATE `category` table
    $table_name_category = $ibk_table_prefix . 'category';
    $sql_category = "CREATE TABLE $table_name_category (
        category_id mediumint(9) NOT NULL AUTO_INCREMENT,
        category_name varchar(255) NOT NULL,
        PRIMARY KEY  (category_id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_category);

    // CREATE `item` table
    $table_name_item = $ibk_table_prefix . 'item';
    $sql_item = "CREATE TABLE $table_name_item (
        item_id VARCHAR(255) NOT NULL,
        category_id mediumint(9) NOT NULL,
        size varchar(10) NOT NULL,
        name varchar(255) NOT NULL,
        description text NOT NULL,
        created_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        image_url varchar(255) NOT NULL,
        status ENUM('available', 'unavailable') NOT NULL,
        PRIMARY KEY  (item_id),
        FOREIGN KEY (category_id) REFERENCES $table_name_category(category_id) ON DELETE CASCADE
    ) $charset_collate;";
    dbDelta($sql_item);

    // CREATE `customer` table
    $table_name_customer = $ibk_table_prefix . 'customer';
    $sql_customer = "CREATE TABLE $table_name_customer (
        customer_id mediumint(9) NOT NULL AUTO_INCREMENT,
        fname VARCHAR(50) NOT NULL,
        lname varchar(50) NOT NULL,
        email varchar(255),
        mobile_phone varchar(50) NOT NULL,
        address text,
        PRIMARY KEY  (customer_id)
    ) $charset_collate;";
    dbDelta($sql_customer);

    // CREATE `price_point` table
    $table_name_price_point = $ibk_table_prefix . 'price_point';
    $sql_price_point = "CREATE TABLE $table_name_price_point (
        price_point_id mediumint(9) NOT NULL AUTO_INCREMENT,
        category_id mediumint(9) NOT NULL,
        timeframe varchar(255) NOT NULL,
        amount FLOAT NOT NULL,
        PRIMARY KEY  (price_point_id),
        FOREIGN KEY (category_id) REFERENCES $table_name_category(category_id) ON DELETE CASCADE
    ) $charset_collate;";
    dbDelta($sql_price_point);

    // CREATE `invoice` table
    $table_name_invoice = $ibk_table_prefix . 'invoice';
    $sql_invoice = "CREATE TABLE $table_name_invoice (
        invoice_id mediumint(9) NOT NULL AUTO_INCREMENT,
        method ENUM('cash', 'card') NOT NULL,
        amount FLOAT NOT NULL,
        PRIMARY KEY  (invoice_id)
    ) $charset_collate;";
    dbDelta($sql_invoice);

    // CREATE `reservation` table
    $table_name_reservation = $ibk_table_prefix . 'reservation';
    $sql_reservation = "CREATE TABLE $table_name_reservation (
        reservation_id mediumint(9) NOT NULL AUTO_INCREMENT,
        invoice_id mediumint(9) NOT NULL,
        customer_id mediumint(9) NOT NULL,
        from_date DATETIME NOT NULL,
        to_date DATETIME NOT NULL,
        reservation_stage ENUM('provisional', 'confirmed', 'checked-out', 'checked-in') NOT NULL,
        created_date DATETIME NOT NULL,
        delivery_notes TEXT,
        PRIMARY KEY  (reservation_id),
        FOREIGN KEY (invoice_id) REFERENCES $table_name_invoice(invoice_id) ON DELETE CASCADE,
        FOREIGN KEY (customer_id) REFERENCES $table_name_customer(customer_id) ON DELETE CASCADE
    ) $charset_collate;";
    dbDelta($sql_reservation);

    // CREATE `blocked_date` table
    $table_name_blocked_date = $ibk_table_prefix . 'blocked_date';
    $sql_blocked_date = "CREATE TABLE $table_name_blocked_date (
        blocked_date DATE NOT NULL,
        is_blocked BOOL NOT NULL,
        PRIMARY KEY  (blocked_date)
    ) $charset_collate;";
    dbDelta($sql_blocked_date);

    // CREATE `item_booking` table
    $table_name_item_booking = $ibk_table_prefix . 'item_booking';
    $sql_item_booking = "CREATE TABLE $table_name_item_booking (
        reservation_id mediumint(9) NOT NULL,
        item_id VARCHAR(255),
        PRIMARY KEY (reservation_id, item_id),
        FOREIGN KEY (reservation_id) REFERENCES $table_name_reservation(reservation_id) ON DELETE CASCADE,
        FOREIGN KEY (item_id) REFERENCES $table_name_item(item_id) ON DELETE CASCADE
    ) $charset_collate;";
    dbDelta($sql_item_booking);

    // CREATE `email` table
    $table_name_email = $ibk_table_prefix . 'email';
    $sql_email = "CREATE TABLE $table_name_email (
        email_id mediumint(9) NOT NULL,
        subject varchar(255) NOT NULL,
        content text NOT NULL,
        PRIMARY KEY (email_id)
    ) $charset_collate;";
    dbDelta($sql_email);

    insert_tables_test_data($table_name_item_booking, $table_name_reservation, $table_name_price_point, $table_name_item, $table_name_category, $table_name_customer, $table_name_invoice, $table_name_blocked_date, $table_name_email);
}

function your_plugin_deactivate() {
    global $wpdb, $ibk_table_prefix;

    $table_name_item_booking = $ibk_table_prefix . 'item_booking';
    $table_name_reservation = $ibk_table_prefix . 'reservation';
    $table_name_price_point = $ibk_table_prefix . 'price_point';
    $table_name_item = $ibk_table_prefix . 'item';
    $table_name_category = $ibk_table_prefix . 'category';
    $table_name_customer = $ibk_table_prefix . 'customer';
    $table_name_invoice = $ibk_table_prefix . 'invoice';
    $table_name_blocked_date = $ibk_table_prefix . 'blocked_date';
    $table_name_email = $ibk_table_prefix . 'email';

    // Drop `item_booking` table
    $wpdb->query("DROP TABLE IF EXISTS $table_name_item_booking");

    // Drop `reservation` table
    $wpdb->query("DROP TABLE IF EXISTS $table_name_reservation");

    // Drop `price_point` table
    $wpdb->query("DROP TABLE IF EXISTS $table_name_price_point");

    // DROP table if the plugin is deactivated
    $wpdb->query("DROP TABLE IF EXISTS $table_name_item");

    // Drop `category` table
    $wpdb->query("DROP TABLE IF EXISTS $table_name_category");

    // Drop `customer` table
    $wpdb->query("DROP TABLE IF EXISTS $table_name_customer");

    // Drop `invoice` table
    $wpdb->query("DROP TABLE IF EXISTS $table_name_invoice");

    // Drop `blocked_date` table
    $wpdb->query("DROP TABLE IF EXISTS $table_name_blocked_date");

    // Drop `email` table
    $wpdb->query("DROP TABLE IF EXISTS $table_name_email");
}

function insert_tables_test_data(
    $table_name_item_booking, 
    $table_name_reservation,
    $table_name_price_point,
    $table_name_item,
    $table_name_category,
    $table_name_customer,
    $table_name_invoice,
    $table_name_blocked_date,
    $table_name_email
) {
    global $wpdb;

    // INSERT test data into `category` table
    $category_data = array(
        array('category_name' => 'E-bike step-thru'),
        array('category_name' => 'E-bike step over'),
        array('category_name' => 'Kid trailer')
    );
    foreach ($category_data as $data) {
        $wpdb->insert($table_name_category, $data);
    }

    // INSERT test data into `item` table
    $item_data = array(
        array( 
            'item_id' => "TSO-1/22", 
            'category_id' => 1,
            'size' => 'M',
            'name' => 'E-BIKE: TOWNIE GO 7D! STEP-THRU',
            'description' => 'Must be 18 plus.Suitable for riders 4\'11" - 6\'1".Fits our kids tag-alongs & trailers.Check out the INFO tab above for more about our Townies.',
            'image_url' => 'http://127.0.0.1/InverlochBikeHire/wp-content/uploads/2022/09/image.png',
            'status' => 'available'
        ),
        array(
            'item_id' => "TSO-2/23", 
            'category_id' => 2,
            'size' => 'M',
            'name' => 'E-BIKE: TOWNIE GO 7D! STEP-OVER',
            'description' => 'Must be 18 plus.Suitable for riders 4\'11" - 6\'1".Fits our kids tag-alongs & trailers.Check out the INFO tab above for more about our Townies.',
            'image_url' => 'http://127.0.0.1/InverlochBikeHire/wp-content/uploads/2022/09/image-1.png',
            'status' => 'available'
        ),
        array(
            'item_id' => "KT-1/23", 
            'category_id' => 3,
            'size' => 'S',
            'name' => 'Kid Trailer',
            'description' => 'Designed for children 18 months and older.Adjustable harnesses to safely cart up to 2 children.Total capacity 45kg.One available - booking essential.',
            'image_url' => 'http://127.0.0.1/InverlochBikeHire/wp-content/uploads/2022/05/2020-ktm-factory-replica-stacyc-electric-balance-bikes.webp',
            'status' => 'available'
        )
    );
    foreach ($item_data as $data) {
        $wpdb->insert($table_name_item, $data);
    }

    // INSERT test data into `customer` table
    $customer_data = array(
        array(
            'fname' => "John", 
            'lname' => 'Doe', 
            'email' => 'johndoe@gmail.com',
            'mobile_phone' => "6172344552",
            'address' => null
        ),
        array(
            'fname' => "Amy", 
            'lname' => 'Doe', 
            'email' => 'amydoe@gmail.com',
            'mobile_phone' => "617324912",
            'address' => "2 Flinders street, VIC"
        )
    );
    foreach ($customer_data as $data) {
        $wpdb->insert($table_name_customer, $data);
    }

    // INSERT test data into `price_point` table
    $price_price_data = array(
        array('category_id' => 1, 'timeframe' => '2h', 'amount' => 40.00),
        array('category_id' => 1, 'timeframe' => '4h', 'amount' => 75.00),
        array('category_id' => 1, 'timeframe' => '1d', 'amount' => 90.00),
        array('category_id' => 1, 'timeframe' => '2d', 'amount' => 100.00),
        array('category_id' => 1, 'timeframe' => '3d', 'amount' => 130.00),
        array('category_id' => 2, 'timeframe' => '2h', 'amount' => 50.00),
        array('category_id' => 2, 'timeframe' => '4h', 'amount' => 75.00),
    );
    foreach ($price_price_data as $data) {
        $wpdb->insert($table_name_price_point, $data);
    }

    // INSERT test data into `invoice` table
    $invoice_data = array(
        array(
            'method' => 'cash', 
            'amount' => 50.00
        ),
        array(
            'method' => 'card', 
            'amount' => 210.00
        )
    );
    foreach ($invoice_data as $data) {
        $wpdb->insert($table_name_invoice, $data);
    }

    // INSERT test data into `reservation` table
    $reservation_data = array(
        array(
            'invoice_id' => 1,
            'customer_id' => 1,
            'from_date' => '2024-02-01 08:00:00',
            'to_date' => '2024-02-05 18:00:00',
            'reservation_stage' => 'confirmed',
            'created_date' => '2024-01-31 12:30:00',
            'delivery_notes' => 'Please deliver to the specified address.'
        ),
        array(
            'invoice_id' => 2,
            'customer_id' => 2,
            'from_date' => '2024-02-10 10:00:00',
            'to_date' => '2024-02-15 20:00:00',
            'reservation_stage' => 'provisional',
            'created_date' => '2024-02-01 15:45:00',
            'delivery_notes' => 'Customer will pick up the items.'
        )
    );
    foreach ($reservation_data as $data) {
        $wpdb->insert($table_name_reservation, $data);
    }

    // INSERT test data into `blocked_date` table
    $blocked_date_data = array(
        array(
            'blocked_date' => '2024-02-29', 
            'is_blocked' => true
        ),
        array(
            'blocked_date' => '2024-01-10', 
            'is_blocked' => false
        )
    );
    foreach ($blocked_date_data as $data) {
        $wpdb->insert($table_name_blocked_date, $data);
    }

    // INSERT test data into `item_booking` table
    $item_booking_data = array(
        array(
            'reservation_id' => 1, 
            'item_id' => 'TSO-1/22'
        ),
        array(
            'reservation_id' => 2, 
            'item_id' => 'TSO-2/23'
        )
    );
    foreach ($item_booking_data as $data) {
        $wpdb->insert($table_name_item_booking, $data);
    }

    // INSERT test data into `email` table
    $email_data = array(
        array(
            'email_id' => 1,
            'subject' => 'Provisional email',
            'content' => 'Thank you {{first name}} for your provisional reservation with Inverloch Bike Hire!

            We have just received your email, which means that someone from our office will be in touch with you soon to confirm the details of your booking, including your delivery address, times, dates and of course...your wheels!
            
            In the meantime, please take a few moments to check out our FAQ\'s. We have included a heap of useful information, hints, tips and handy local knowledge to help make your ride one to remember.
            
            Finally, if you could read, sign and return the attached T&C\'s and waiver, we can then get this two-wheeled show on the road. By making your booking with us, you are confirming that you have read, acknowledged and are agreeing to our T&C\'s.
            
            If you have any questions or there is anything else we can assist you with, please get in touch via email or give us a buzz on 0455-896-240.
            
            Again, thank you for choosing Inverloch Bike Hire...see you in the saddle soon.
            
            Meika
            
            Inverloch Bike Hire'
        )
    );
    foreach ($email_data as $data) {
        $wpdb->insert($table_name_email, $data);
    }
}
