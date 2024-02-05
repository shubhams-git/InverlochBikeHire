<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class CustomerModel {
    private $wpdb;
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'ibk_customer';
    }

    public function insert($data) {
        // Data sanitization
        $data = array_map('sanitize_text_field', $data);

        $result = $this->wpdb->insert(
            $this->table_name,
            $data,
            ['%s', '%s', '%s', '%s', '%s']
        );

        if ($result) {
            return $this->wpdb->insert_id;
        } else {
            return new WP_Error('db_insert_error', 'Failed to insert item into the database.');
        }
    }

    public function get_all_customers() {
        return $this->wpdb->get_results("SELECT * FROM {$this->table_name}");
    }

    public function update($item_id, $data) {
        // Data sanitization
        $item_id = sanitize_text_field($item_id);
        $data = array_map('sanitize_text_field', $data);

        $result = $this->wpdb->update(
            $this->table_name,
            $data, // Data to update
            ['item_id' => $item_id], // Where clause
            ['%s', '%s', '%s', '%s', '%s'], // Data format
            ['%s'] // Where format
        );

        if ($result !== false) {
            return true;
        } else {
            return new WP_Error('db_update_error', 'Failed to update item in the database.');
        }
    }

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