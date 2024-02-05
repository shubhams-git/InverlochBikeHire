<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class ItemModel {
    private $wpdb;
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'ibk_item';
    }

    // Create a new item
    public function insert($data) {
        // Ensure id_number is unique
        if (!$this->is_id_number_unique($data['id_number'])) {
            return new WP_Error('duplicate_id_number', 'The ID number must be unique.');
        }

        // Data sanitization
        $data = $this->sanitize_data($data);

        $format = $this->get_format($data);

        $result = $this->wpdb->insert(
            $this->table_name,
            $data,
            $format
        );

        if ($result) {
            return $this->wpdb->insert_id;
        } else {
            return new WP_Error('db_insert_error', 'Failed to insert item into the database. ' . $this->wpdb->last_error);
        }
    }

    // Retrieve all items
    public function get_all_items() {
        return $this->wpdb->get_results("SELECT * FROM {$this->table_name}", OBJECT);
    }

    // Retrieve a single item by ID
    public function get_item_by_id($item_id) {
        $item_id = intval($item_id);

        return $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE item_id = %d",
            $item_id
        ), OBJECT);
    }

    // Update an existing item
    public function update($item_id, $data) {
        $item_id = intval($item_id);

        if (isset($data['id_number']) && !$this->is_id_number_unique($data['id_number'], $item_id)) {
            return new WP_Error('duplicate_id_number', 'The ID number must be unique.');
        }

        $data = $this->sanitize_data($data);
        $format = $this->get_format($data);

        $result = $this->wpdb->update(
            $this->table_name,
            $data,
            ['item_id' => $item_id],
            $format,
            ['%d']
        );

        if ($result !== false) {
            return true;
        } else {
            return new WP_Error('db_update_error', 'Failed to update item in the database. ' . $this->wpdb->last_error);
        }
    }

    // Delete an item
    public function delete($item_id) {
        $item_id = intval($item_id);

        $result = $this->wpdb->delete(
            $this->table_name,
            ['item_id' => $item_id],
            ['%d']
        );

        if ($result) {
            return true;
        } else {
            return new WP_Error('db_delete_error', 'Failed to delete item from the database.');
        }
    }

    // Helper Methods
    private function is_id_number_unique($id_number, $exclude_id = 0) {
        $query = $this->wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name} WHERE id_number = %s AND item_id != %d",
            $id_number, $exclude_id
        );
        return ($this->wpdb->get_var($query) == 0);
    }

    private function sanitize_data($data) {
        foreach ($data as $key => &$value) {
            if ('item_id' !== $key) { // Do not sanitize the primary key
                $value = ('category_id' === $key) ? intval($value) : sanitize_text_field($value);
            }
        }
        return $data;
    }

    private function get_format($data) {
        return array_map(function($key, $value) {
            return ('category_id' === $key) ? '%d' : '%s';
        }, array_keys($data), $data);
    }
    
}
