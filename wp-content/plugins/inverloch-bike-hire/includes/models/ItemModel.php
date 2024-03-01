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

    // Retrieve a list of items by IDs
    public function get_items_by_ids($item_ids) {
        if (!is_array($item_ids)) {
            $item_ids = [$item_ids];
        }
        
        // Filter out any non-integer values to maintain query integrity
        $item_ids = array_filter($item_ids, function($id) {
            return is_int($id) || ctype_digit($id);
        });

        // If after filtering there are no valid IDs, return an empty array to avoid SQL errors
        if (empty($item_ids)) {
            return [];
        }

        // Convert item IDs to integers
        $item_ids = array_map('intval', $item_ids);

        // Construct placeholders string for the query
        $placeholders = implode(',', array_fill(0, count($item_ids), '%d'));

        // Prepare and execute the SQL query
        $query = $this->wpdb->prepare("SELECT * FROM {$this->table_name} WHERE item_id IN ($placeholders)", $item_ids);
        
        // Fetch and return the results
        return $this->wpdb->get_results($query, OBJECT);
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

// Delete an item and its associated image
public function delete($item_id) {
    $item_id = intval($item_id);
    
    // First, retrieve the image URL for the item
    $item = $this->get_item_by_id($item_id);
    if (!$item) {
        return new WP_Error('item_not_found', 'Item not found.');
    }
    
    $image_url = $item->image_url;
    
    // Convert the image URL to a file path
    if ($image_url) {
        $upload_dir = wp_upload_dir();
        $image_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $image_url);
        
        // Check if the file exists and then delete
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Proceed to delete the item from the database
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

    // Return all the items except for the item ids(parameter)
    public function get_items_except_specified_ids($item_ids) {
        if (empty($item_ids)) {
            return $this->wpdb->get_results("SELECT * FROM {$this->table_name} WHERE status = 'available' ORDER BY category_id", OBJECT);
        }
    
        $item_ids = array_map('intval', $item_ids);
        $placeholders = implode(', ', array_fill(0, count($item_ids), '%d'));
        $placeholders = '(' . $placeholders . ')'; // Wrap placeholders in parentheses
    
        $query = $this->wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE item_id NOT IN {$placeholders} AND status = 'available' ORDER BY category_id",
            $item_ids
        );
    
        return $this->wpdb->get_results($query, OBJECT);
    }
}
