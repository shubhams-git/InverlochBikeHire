<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class ItemBookingModel {
    private $wpdb;
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'ibk_item_booking';
    }

    public function insert($data) {
        // Data sanitization
        $data = array_map('sanitize_text_field', $data);

        $result = $this->wpdb->insert(
            $this->table_name,
            $data,
            ['%d', '%d']
        );

        if ($result) {
            return $this->wpdb->insert_id;
        } else {
            return new WP_Error('db_insert_error', 'Failed to insert item booking into the database.');
        }
    }

    // Return all reservation ids by item id
    public function get_reservation_ids_by_item_id($item_id) {
        $item_id = sanitize_text_field($item_id);

        if (!$this->is_valid_reservation_id($item_id)) {
            return null;
        }

        $query = $this->wpdb->prepare(
            "SELECT reservation_id FROM {$this->table_name} WHERE item_id = %s",
            $item_id
        );

        $results = $this->wpdb->get_results($query);

        $reservation_ids = array();
        foreach ($results as $result) {
            $reservation_ids[] = $result->reservation_id;
        }
        return $reservation_ids;
    }

    public function get_item_ids_by_reservation_ids($reservation_ids) {
        $reservation_ids = array_map('intval', $reservation_ids); // Sanitize input
        
        // Check if the array is empty to avoid SQL syntax error
        if (empty($reservation_ids)) {
            return []; // Return an empty array if no reservation IDs are provided
        }
    
        // Create a placeholders string with the correct number of placeholders
        $placeholders = implode(',', array_fill(0, count($reservation_ids), '%d'));
    
        // Prepare the SQL query, injecting the placeholders and then using call_user_func_array to apply the reservation IDs
        $query = "SELECT item_id FROM {$this->table_name} WHERE reservation_id IN ($placeholders)";
        $prepared_query = $this->wpdb->prepare($query, $reservation_ids);
        
        // Execute the query and fetch results
        $results = $this->wpdb->get_results($prepared_query);
    
        // Assuming you want to return an array of item_ids directly
        $item_ids = array_map(function($result) { return $result->item_id; }, $results);
    
        return $item_ids;
    }

    public function clear_bookings_for_reservation($reservation_id) {
        return $this->wpdb->delete($this->table_name, ['reservation_id' => $reservation_id], ['%d']);
    }
    

    // Return the bike counts by the reservation id
    public function get_bike_counts_by_reservation_id($reservation_id) {
        $reservation_id = sanitize_text_field($reservation_id);

        $query = $this->wpdb->prepare(
            "SELECT COUNT(*) AS count FROM {$this->table_name} WHERE reservation_id = %s",
            $reservation_id
        );

        $count_row = $this->wpdb->get_row($query);

        if ($count_row) {
            return $count_row->count;
        } else {
            return 0;
        }
    }

    public function is_valid_reservation_id($item_id) {
        $item_id = sanitize_text_field($item_id);

        $result = $this->wpdb->get_var($this->wpdb->prepare("SELECT COUNT(*) FROM $this->table_name WHERE item_id = %d", $item_id));

        return $result > 0;
    }
    
}