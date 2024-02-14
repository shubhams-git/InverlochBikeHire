<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class PricePointModel {
    private $wpdb;
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'ibk_price_point';
    }

    // Retrieve all price points
    public function get_all_price_points() {
        return $this->wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY timeframe ASC", OBJECT);
    }

    // Insert a new price point
    public function insert($category_id, $timeframe, $amount) {
       
        $category_id =intval($category_id); 
        $amount = floatval($amount);
         // Validate inputs
         if ($category_id == 0 || empty($timeframe) || $amount ==0) {
            return new WP_Error('invalid_input', 'Invalid input for price point insertion.');
        }
        $data = [
            'category_id' => $category_id,
            'timeframe' => $timeframe,
            'amount' => $amount
        ];
        $format = ['%d', '%s', '%f'];
        
        $result = $this->wpdb->insert($this->table_name, $data, $format);
        if ($result === false) {
            return new WP_Error('db_insert_error', 'Failed to insert price point into the database. Error: ' . $this->wpdb->last_error);
        }
        return $this->wpdb->insert_id;
    }
    
    

    // Update an existing price point
    public function update($price_point_id, $category_id, $timeframe, $amount) {
        // Validate inputs
        if (empty($price_point_id) || empty($category_id) || empty($timeframe) || !is_numeric($amount)) {
            return new WP_Error('invalid_input', 'Invalid input for price point update.');
        }
        $amount = floatval($amount);

        $data = [
            'category_id' => $category_id,
            'timeframe' => $timeframe,
            'amount' => $amount
        ];
        $where = ['price_point_id' => $price_point_id];
        $format = ['%d', '%s', '%f'];
        $where_format = ['%d'];
    
        $result = $this->wpdb->update($this->table_name, $data, $where, $format, $where_format);
        if ($result === false) {
            return new WP_Error('db_update_error', 'Failed to update price point in the database. Error: ' . $this->wpdb->last_error);
        }
        return true;
    }
    
    // Delete a price point
    public function delete($price_point_id) {
        $where = ['price_point_id' => intval($price_point_id)];
        $where_format = ['%d'];

        $result = $this->wpdb->delete($this->table_name, $where, $where_format);
        if ($result) {
            return true;
        } else {
            return new WP_Error('db_delete_error', 'Failed to delete price point from the database.');
        }
    }

    // Retrieve price points for a specific category
    public function get_price_points_by_category($category_id) {
        $category_id = intval($category_id);
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE category_id = %d ORDER BY timeframe ASC", 
            $category_id
        ), OBJECT);
    }

    public function get_price_point_by_category_and_timeframe($category_id, $timeframe) {
        $query = $this->wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE category_id = %d AND timeframe = %s LIMIT 1",
            intval($category_id),
            $timeframe
        );
        return $this->wpdb->get_row($query);
    }
    
}
