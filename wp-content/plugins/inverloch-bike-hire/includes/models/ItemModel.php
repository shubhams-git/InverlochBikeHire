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
        // Data sanitization
        $data = array_map('sanitize_text_field', $data);

        $result = $this->wpdb->insert(
            $this->table_name,
            $data,
            ['%s', '%d', '%s', '%s', '%s', '%s']
        );

        if ($result) {
            return $this->wpdb->insert_id;
        } else {
            return new WP_Error('db_insert_error', 'Failed to insert item into the database.');
        }
    }

    // Retrieve all items
    public function get_all_items() {
        return $this->wpdb->get_results("SELECT * FROM {$this->table_name}");
    }

    // Retrieve a single item by ID
    public function get_item_by_id($item_id) {
        $item_id = sanitize_text_field($item_id);

        return $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE item_id = %s",
            $item_id
        ));
    }

    // Update an existing item
    public function update($item_id, $data) {
        // Data sanitization
        $item_id = sanitize_text_field($item_id);
        $data = array_map('sanitize_text_field', $data);

        $result = $this->wpdb->update(
            $this->table_name,
            $data, // Data to update
            ['item_id' => $item_id], // Where clause
            ['%s', '%d', '%s', '%s', '%s', '%s'], // Data format
            ['%s'] // Where format
        );

        if ($result !== false) {
            return true;
        } else {
            return new WP_Error('db_update_error', 'Failed to update item in the database.');
        }
    }

    // Delete an item
    public function delete($item_id) {
        $item_id = sanitize_text_field($item_id);

        $result = $this->wpdb->delete(
            $this->table_name,
            ['item_id' => $item_id],
            ['%s']
        );

        if ($result) {
            return true;
        } else {
            return new WP_Error('db_delete_error', 'Failed to delete item from the database.');
        }
    }
}
