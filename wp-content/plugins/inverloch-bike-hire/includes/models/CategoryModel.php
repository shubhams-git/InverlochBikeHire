<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class CategoryModel {
    private $wpdb;
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'ibk_category';
    }

    // Create a new category
    public function insert($category_name) {
        // Data validation
        if (empty($category_name)) {
            return new WP_Error('invalid_data', 'Category name cannot be empty.');
        }

        // Data sanitization
        $category_name = sanitize_text_field($category_name);

        $result = $this->wpdb->insert(
            $this->table_name,
            ['category_name' => $category_name],
            ['%s']
        );

        if ($result) {
            return $this->wpdb->insert_id;
        } else {
            return new WP_Error('db_insert_error', 'Failed to insert category into the database.');
        }
    }

    // Retrieve all categories
    public function get_all_categories() {
        return $this->wpdb->get_results("SELECT * FROM {$this->table_name}");
    }

    // Retrieve a single category by ID
    public function get_category_by_id($category_id) {
        $category_id = intval($category_id);

        return $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE category_id = %d",
            $category_id
        ));
    }

    // Update an existing category
    public function update($category_id, $new_category_name) {
        // Data validation
        if (empty($new_category_name)) {
            return new WP_Error('invalid_data', 'Category name cannot be empty.');
        }

        // Data sanitization
        $new_category_name = sanitize_text_field($new_category_name);
        $category_id = intval($category_id);

        $result = $this->wpdb->update(
            $this->table_name,
            ['category_name' => $new_category_name], // data
            ['category_id' => $category_id], // where
            ['%s'], // data format
            ['%d']  // where format
        );

        if ($result !== false) {
            return true;
        } else {
            return new WP_Error('db_update_error', 'Failed to update category in the database.');
        }
    }

    // Delete a category
    public function delete($category_id) {
        $category_id = intval($category_id);

        $result = $this->wpdb->delete(
            $this->table_name,
            ['category_id' => $category_id],
            ['%d']
        );

        if ($result) {
            return true;
        } else {
            return new WP_Error('db_delete_error', 'Failed to delete category from the database.');
        }
    }
}
