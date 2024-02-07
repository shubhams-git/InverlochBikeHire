<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class BlockedDateModel {
    private $wpdb;
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'ibk_blocked_date';
    }

    public function insert($data) 
    {
        if (!$this->is_valid_blocked_date($data['date'])) {
            $result = $this->wpdb->insert(
                $this->table_name,
                $data,
                ['%s', '%d']
            );

            if ($result) {
                return $this->wpdb->insert_id;
            } else {
                return new WP_Error('db_insert_error', 'Failed to insert blocked date into the database.');
            }
        } else {
            return new WP_Error('db_insert_error', 'This blocked date exists in the table.');
        }
    }

    public function get_all_blocked_date() 
    {
        return $this->wpdb->get_results("SELECT * FROM {$this->table_name}");
    }

    public function is_valid_blocked_date($date) {
        $date = sanitize_text_field($date);

        $query = $this->wpdb->prepare(
            "SELECT COUNT(*) FROM $this->table_name WHERE date = %s",
            $date
        );

        $count = $this->wpdb->get_var($query);

        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($date) {
        $date = sanitize_text_field($date);

        $result = $this->wpdb->delete(
            $this->table_name,
            ['date' => $date],
            ['%s']
        );

        if ($result) {
            return true;
        } else {
            return new WP_Error('db_delete_error', 'Failed to delete blocked date from the database.');
        }
    }
}