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
    public function get_reservation_ids_by_date_time_range($from_date, $to_date, $from_time, $to_time) {

        $from_date = sanitize_text_field($from_date);
        $to_date = sanitize_text_field($to_date);
        $from_time = sanitize_text_field($from_time);
        $to_time = sanitize_text_field($to_time);

        // Prepare the SQL query
        $query = $this->wpdb->prepare(
            "SELECT reservation_id FROM {$this->table_name} WHERE 
            (
                (from_date <= %s AND to_date >= %s) OR
                (from_date <= %s AND to_date >= %s) OR
                (from_date <= %s AND to_date >= %s AND from_time <= %s AND to_time >= %s) OR
                (to_date >= %s AND from_date <= %s AND to_time >= %s AND from_time <= %s) OR
                (from_date >= %s AND from_date <= %s AND from_time >= %s AND from_time <= %s) OR
                (to_date >= %s AND to_date <= %s AND to_time >= %s AND to_time <= %s)
            )",
            $from_date, $from_date,
            $to_date, $to_date,
            $from_date, $to_date, $from_time, $to_time,
            $from_date, $to_date, $to_time, $from_time,
            $from_date, $to_date, $from_time, $to_time,
            $from_date, $to_date, $from_time, $to_time
        );
    
        // Execute the query
        $results = $this->wpdb->get_results($query);
    
        // Extract reservation IDs from the results
        $reservation_ids = array();
        foreach ($results as $result) {
            $reservation_ids[] = $result->reservation_id;
        }
    
        return $reservation_ids;
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
}