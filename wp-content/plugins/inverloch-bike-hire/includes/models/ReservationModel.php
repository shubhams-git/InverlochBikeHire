<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class ReservationModel {
    private $wpdb;
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'ibk_reservation';
    }

    public function insert($data) {
        $data = array_map("sanitize_text_field", $data);
        $customer_model = new CustomerModel();

        // Check if the provided customer id is valid
        if (!$customer_model->is_valid_customer_id($data['customer_id'])) {
            return new WP_Error('invalid_customer_id', 'Invalid customer_id provided.');
        }

        $result = $this->wpdb->insert(
            $this->table_name,
            $data,
            ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        if ($result) {
            return $this->wpdb->insert_id;
        } else {
            return new WP_Error('db_insert_error', 'Failed to insert reservation into the database.');
        }
    }

    public function get_all_reservations() {
        return $this->wpdb->get_results("SELECT * FROM {$this->table_name}");
    }

    public function is_valid_reservation_id($reservation_id) {
        $reservation_id = sanitize_text_field($reservation_id);

        $result = $this->wpdb->get_var($this->wpdb->prepare("SELECT COUNT(*) FROM $this->table_name WHERE reservation_id = %d", $reservation_id));

        return $result > 0;
    }

    // Return reservation ids by date and time range
    public function get_reservation_ids_by_date_time_range($startDate, $endDate, $startTime, $endTime) {
        // Sanitize input values to prevent SQL injection
        $startDate = sanitize_text_field($startDate);
        $endDate = sanitize_text_field($endDate);
        $startTime = sanitize_text_field($startTime);
        $endTime = sanitize_text_field($endTime);
    
        // Prepare a SQL query to fetch reservation IDs within the specified date and time range
        $query = $this->wpdb->prepare(
            "SELECT reservation_id FROM {$this->table_name} WHERE 
            (to_date > %s OR (to_date = %s AND to_time >= %s)) AND
            (from_date < %s OR (from_date = %s AND from_time <= %s))",
            $startDate, $startDate, $startTime, // Checks if the reservation ends after or at the start of the range
            $endDate, $endDate, $endTime       // Checks if the reservation starts before or at the end of the range
        );

        // Execute the query and fetch results
        $results = $this->wpdb->get_results($query);
    
        // Extract and return reservation IDs
        return array_map(function($result) { return $result->reservation_id; }, $results);
    }
    

    public function get_reservations_by_reservation_ids($reservation_ids) {
        $reservation_ids = array_map("intval", $reservation_ids);
        $placeholders = implode(', ', array_fill(0, count($reservation_ids), '%d'));
        $placeholders = '(' . $placeholders . ')';

        $query = $this->wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE reservation_id IN {$placeholders} ORDER BY from_date",
            $reservation_ids
        );

        return $this->wpdb->get_results($query, OBJECT);
    }

    // Get all the reservation data, along with the customer's details
    public function get_detailed_reservations() {
        $query = "SELECT r.*, c.fname, c.lname, c.mobile_phone
                FROM {$this->table_name} r
                INNER JOIN {$this->wpdb->prefix}ibk_customer c ON r.customer_id = c.customer_id
                ORDER BY r.from_date DESC";
        return $this->wpdb->get_results($query);
    }

    
    public function update($reservation_id, $data) {
        $reservation_id = sanitize_text_field($reservation_id);
        $data = array_map('sanitize_text_field', $data);

        $result = $this->wpdb->update(
            $this->table_name,
            $data,  // data to update
            ['reservation_id' => $reservation_id],  // where clause
            ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s'], // data format
            ['%s']  // where format
        );

        if ($result !== false) {
            return true;
        } else {
            return new WP_Error('db_update_error', 'Failed to update reservation in the database.');
        }
    }

    public function delete($reservation_id) {
        $reservation_id = sanitize_text_field($reservation_id);

        $result = $this->wpdb->delete(
            $this->table_name,
            ['reservation_id' => $reservation_id],
            ['%s']
        );

        if ($result !== false) {
            return true;
        } else {
            return new WP_Error('db_delete_error', 'Failed to delete reservation in the database.');
        }
    }

    // Start a transaction
    public function start_transaction() {
        $this->wpdb->query('START TRANSACTION');
    }

    // Commit a transaction
    public function commit_transaction() {
        $this->wpdb->query('COMMIT');
    }

    // Rollback a transaction
    public function rollback_transaction() {
        $this->wpdb->query('ROLLBACK');
    }
}