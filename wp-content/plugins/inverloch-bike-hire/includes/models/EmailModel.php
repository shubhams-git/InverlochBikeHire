<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class EmailModel {
    private $wpdb;
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'ibk_email';
    }

    public function get_all_emails() {
        return $this->wpdb->get_results("SELECT * FROM {$this->table_name}", OBJECT);
    }

    public function get_email_by_id($email_id) {
        $email_id = intval($email_id);
        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table_name} WHERE email_id = %d", $email_id), OBJECT);
    }

    public function update($email_id, $data) {
        $email_id = sanitize_text_field($email_id);

        $result = $this->wpdb->update(
            $this->table_name,
            $data, // Data to update
            ['email_id' => $email_id], // Where clause
            ['%s', '%s', '%s'], // Data format
            ['%d'] // Where format
        );

        if ($result !== false) {
            return true;
        } else {
            return new WP_Error('db_update_error', 'Failed to update email in the database.');
        }
    }
}