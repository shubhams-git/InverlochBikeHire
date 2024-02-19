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

        // Check if a customer with the same first name, last name, and phone number exists
        $existing_customer = $this->get_customer_by_fname_lname_phone($data['fname'], $data['lname'], $data['mobile_phone']);
        if ($existing_customer) {
            return new WP_Error('duplicate_customer', 'Customer with the same first name, last name, and phone number already exists.');
        }

        $result = $this->wpdb->insert(
            $this->table_name,
            $data,
            ['%s', '%s', '%s', '%s', '%s']
        );

        if ($result) {
            return $this->wpdb->insert_id;
        } else {
            return new WP_Error('db_insert_error', 'Failed to insert customer into the database.');
        }
    }

    private function get_customer_by_fname_lname_phone($fname, $lname, $phone) {
        return $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE fname = %s AND lname = %s AND mobile_phone = %s",
            $fname,
            $lname,
            $phone
        ));
    }

    public function get_all_customers() {
        return $this->wpdb->get_results("SELECT * FROM {$this->table_name}");
    }

    public function get_customer_by_id($customer_id) {
        $customer_id = sanitize_text_field($customer_id);

        return $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE customer_id = %s",
            $customer_id
        ));
    }

    // Retrieve a single customer by ID
    public function is_valid_customer_id($customer_id) {
        $customer_id = sanitize_text_field($customer_id);

        $result = $this->wpdb->get_var($this->wpdb->prepare("SELECT COUNT(*) FROM $this->table_name WHERE customer_id = %d", $customer_id));

        return $result > 0;
    }

    public function update($customer_id, $data) {
        // Data sanitization
        $customer_id = sanitize_text_field($customer_id);
        $data = array_map('sanitize_text_field', $data);

        $result = $this->wpdb->update(
            $this->table_name,
            $data, // Data to update
            ['customer_id' => $customer_id], // Where clause
            ['%s', '%s', '%s', '%s', '%s'], // Data format
            ['%s'] // Where format
        );

        if ($result !== false) {
            return true;
        } else {
            return new WP_Error('db_update_error', 'Failed to update customer in the database.');
        }
    }

    public function delete($customer_id) {
        $customer_id = sanitize_text_field($customer_id);

        $result = $this->wpdb->delete(
            $this->table_name,
            ['customer_id' => $customer_id],
            ['%s']
        );

        if ($result) {
            return true;
        } else {
            return new WP_Error('db_delete_error', 'Failed to delete customer from the database.');
        }
    }

    // Method to check if the customer is being referenced by reservations
    public function is_customer_referenced($customer_id) {
        $customer_id = intval($customer_id);
        $customer_count = $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->wpdb->prefix}ibk_reservation WHERE customer_id = %d",
            $customer_id
        ));

        return ($customer_count > 0) ? true : false;
    }

}