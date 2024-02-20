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
        // Retrieve all price points from the database
        return $this->wpdb->get_results("SELECT * FROM {$this->table_name}", OBJECT);
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
    public function delete_all() {
        $result = $this->wpdb->query("DELETE FROM {$this->table_name}");

        if ($result !== false) {
            return true;
        } else {
            return new WP_Error('db_delete_error', 'Failed to delete all price points from the database.');
        }
    }

    private function convert_to_minutes($timeframe) {
        // Extract the numeric value and unit from the timeframe
        preg_match('/(\d+)\s*(\w+)/', $timeframe, $matches);
        $value = isset($matches[1]) ? (int)$matches[1] : 0;
        $unit = isset($matches[2]) ? strtolower($matches[2]) : '';

        // Convert the value to minutes based on the unit
        switch ($unit) {
            case 'hours':
                return $value * 60; // Convert hours to minutes
            case 'days':
                return $value * 24 * 60; // Convert days to minutes
            default:
                return 0; // Unknown unit
        }
    }

    private function sort_timeframes($timeframes) {
        usort($timeframes, function($a, $b) {
            // Convert timeframes to minutes for comparison
            $a_minutes = $this->convert_to_minutes($a);
            $b_minutes = $this->convert_to_minutes($b);
    
            // Compare the converted values
            return $a_minutes - $b_minutes;
        });

        return $timeframes;
    }

    public function get_sorted_timeframes() {
        // Retrieve all price points from the database
        $timeframes = $this->wpdb->get_col("SELECT DISTINCT timeframe FROM {$this->table_name}");

        return $this->sort_timeframes($timeframes);
    }

    public function get_amount_by_timeframe_and_category($timeframe, $category) {
        $query = $this->wpdb->prepare(
            "SELECT amount FROM {$this->table_name} WHERE 
            (timeframe = %s) AND (category_id = %d) LIMIT 1",
            $timeframe, $category
        );

        return $this->wpdb->get_row($query);
    }

}
