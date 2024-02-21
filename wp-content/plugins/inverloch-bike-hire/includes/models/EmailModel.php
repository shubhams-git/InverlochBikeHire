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

    public function insert_initial_email() {
        $provisional_content = '<p>Thank you {{first name}} for your provisional reservation with Inverloch Bike Hire!</p>';
        $provisional_content .= '<p>We have just received your email, which means that someone from our office will be in touch with you soon to confirm the details of your booking, including your delivery address, times, dates and of course...your wheels!</p>';
        $provisional_content .= '<p>In the meantime, please take a few moments to check out our <a href="#">FAQs</a>. We have included a heap of useful information, hints, tips and handy local knowledge to help make your ride one to remember.</p>';
        $provisional_content .= '<p>If you have any questions or there is anything else we can assist you with, please get in touch via email or give us a buzz on <strong>0455-896-240</strong>.</p>';
        $provisional_content .= '<p>Again, thank you for choosing Inverloch Bike Hire...see you in the saddle soon.</p>';
        $provisional_content .= '<p>Meika</p>';
        $provisional_content .= '<p>Inverloch Bike Hire</p>';

        $confirmed_content = '<p>Congratulations {{first name}} ...your reservation with Inverloch Bike Hire is now confirmed!</p>';
        $confirmed_content .= '<p>Next step, someone from our office will give you a call to introduce ourselves and fine tune the details of your hire. If you have any questions or wish to make any changes to your hire, give us a call.  In the meantime, wait for ours...see you soon!</p>';
        $confirmed_content .= '<p>Regards</p>';
        $confirmed_content .= '<p>Meika</p>';
        $confirmed_content .= '<p>0455 896 240</p>';

        $data = [
            [
                'email_id' => 1,
                'email_type' => 'provisional',
                'subject' => 'Provisional Email',
                'content' => $provisional_content
            ],
            [
                'email_id' => 2,
                'email_type' => "confirmation",
                'subject' => 'Confirmed Booking',
                'content' => $confirmed_content
            ]
        ];

        foreach ($data as $email) {
            $result = $this->wpdb->insert(
                $this->table_name,
                $email,
                ['%s', '%s', '%s'] // Data format
            );

            if ($result === false) {
                return new WP_Error('db_insert_error', 'Failed to insert email into the database.');
            }
        }

        return true;
    }
}