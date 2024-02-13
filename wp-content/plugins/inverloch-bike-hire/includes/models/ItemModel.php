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
        if (isset($data['id_number']) && !$this->is_id_number_unique($data['id_number'])) {
            return new WP_Error('duplicate_id_number', 'The ID number must be unique.');
        }

        $data = $this->sanitize_data($data);
        $format = $this->get_format($data);

        $result = $this->wpdb->insert($this->table_name, $data, $format);

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
    
    // Retrieve all items with category name
    public function get_all_items_with_category_name() {
        $sql = "SELECT items.*, categories.category_name
                FROM {$this->table_name} as items
                INNER JOIN {$this->wpdb->prefix}ibk_category as categories
                ON items.category_id = categories.category_id";
        return $this->wpdb->get_results($sql, OBJECT);
    }
    

    // Retrieve a single item by ID
    public function get_item_by_id($item_id) {
        $item_id = intval($item_id);
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table_name} WHERE item_id = %d", $item_id), OBJECT);
    }

    // Update an existing item
    public function update($item_id, $data) {
        $item_id = intval($item_id);

        if (isset($data['id_number']) && !$this->is_id_number_unique($data['id_number'], $item_id)) {
            return new WP_Error('duplicate_id_number', 'The ID number must be unique.');
        }

        $data = $this->sanitize_data($data);
        $format = $this->get_format($data);

        $result = $this->wpdb->update($this->table_name, $data, ['item_id' => $item_id], $format, ['%d']);

        if ($result !== false) {
            return true;
        } else {
            return new WP_Error('db_update_error', 'Failed to update item in the database. ' . $this->wpdb->last_error);
        }
    }

    // Delete an item
    public function delete($item_id) {
        $item_id = intval($item_id);
        $result = $this->wpdb->delete($this->table_name, ['item_id' => $item_id], ['%d']);

        if ($result) {
            return true;
        } else {
            return new WP_Error('db_delete_error', 'Failed to delete item from the database.');
        }
    }

    // Helper methods
    private function is_id_number_unique($id_number, $exclude_id = 0) {
        $exclude_id = intval($exclude_id);
        $query = $this->wpdb->prepare("SELECT COUNT(*) FROM {$this->table_name} WHERE id_number = %s AND item_id != %d", $id_number, $exclude_id);
        return ($this->wpdb->get_var($query) == 0);
    }

    private function sanitize_data($data) {
        $sanitized = [];
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'category_id':
                case 'item_id':
                    $sanitized[$key] = intval($value);
                    break;
                case 'image_url':
                    $sanitized[$key] = esc_url_raw($value);
                    break;
                default:
                    $sanitized[$key] = sanitize_text_field($value);
            }
        }
        return $sanitized;
    }

    private function get_format($data) {
        $format = [];
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'category_id':
                case 'item_id':
                    $format[] = '%d';
                    break;
                case 'image_url':
                    $format[] = '%s';
                    break;
                default:
                    $format[] = '%s';
            }
        }
        return $format;
    }
}
