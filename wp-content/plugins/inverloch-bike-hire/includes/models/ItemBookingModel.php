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

    // Return all item ids by reservations ids
    public function get_item_ids_by_reservation_ids($reservation_ids) {
        $reservation_ids = array_map('intval', $reservation_ids);
        
        // Check if the array is empty to avoid SQL syntax error
        if (empty($reservation_ids)) {
            // Return an empty array or handle this scenario as needed
            return [];
        }
    
        // Now $reservation_ids is guaranteed to not be empty
        $query = $this->wpdb->prepare(
            "SELECT item_id FROM {$this->table_name} WHERE reservation_id IN (" . implode(',', array_fill(0, count($reservation_ids), '%d')) . ")",
            $reservation_ids
        );
    
        $results = $this->wpdb->get_results($query);
    
        $item_ids = array();
        foreach ($results as $result) {
            $item_ids[] = $result->item_id;
        }
    
        return $item_ids;
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
    
}