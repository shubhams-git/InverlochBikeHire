<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add a custom menu page under the "Customers" menu
add_action('admin_menu', 'customers_page');

function customers_page() {
    add_menu_page(
        'Customers', 
        'Customers', 
        'manage_options', 
        'customer_data_page', 
        'display_customer_data_page', // Callback function
        'dashicons-businessman',
        36
    );

    // Add a submenu page for "Add New Customer"
    add_submenu_page(
        'customer_data_page', 
        'Add New Customer', 
        'Add New Customer',
        'manage_options',
        'add_new_customer_page', // Callback function
        'display_add_new_customer_page'
    );
}

// Callback function to display the "Add New Customer" page
function display_add_new_customer_page() {

    include_once(MY_PLUGIN_PATH .'includes/models/CustomerModel.php');

    // Instantiate the CustomerModel
    $customer_model = new CustomerModel();

    // Check if the form is submitted and process the data
    if (
        isset($_POST['submit_customer']) &&
        isset($_POST['add_new_customer_nonce']) &&
        wp_verify_nonce($_POST['add_new_customer_nonce'], 'add_new_customer')
    ) {
        // Get the submitted data
        $fname = sanitize_text_field($_POST['customer_fname']);
        $lname = sanitize_text_field($_POST['customer_lname']);
        $email = sanitize_email($_POST['customer_email']);
        $pnumber = sanitize_text_field($_POST['customer_phone_number']);
        $address = sanitize_text_field($_POST['customer_address']);

        // Insert the data using the CustomerModel
        $customer_id = $customer_model->insert(array(
            'fname' => $fname,
            'lname' => $lname,
            'email' => $email,
            'mobile_phone' => $pnumber,
            'address' => $address 
        ));

        // If INSERT succeed
        if (!is_wp_error($customer_id)) {
            // Display a success message
            ?>
            <div class="updated notice">
                <p>Customer added successfully! Customer ID: <?php echo $customer_id; ?></p>
            </div>
            <?php
        } else {
            // Display an error message
            ?>
            <div class="error notice">
                <p><?php echo esc_html($customer_id->get_error_message()); ?></p>
            </div>
            <?php
        }
    }

    // Display the form
    ?>
    <div class="wrap">
        <h2>Add New Customer</h2>

        <form method="post" action="admin.php?page=add_new_customer_page">
            <?php wp_nonce_field('add_new_customer', 'add_new_customer_nonce'); ?>

            <label for="customer_fname">First Name:</label>
            <input type="text" name="customer_fname" id="customer_fname" placeholder="eg. John" required><br><br>

            <label for="customer_lname">Last Name:</label>
            <input type="text" name="customer_lname" id="customer_lname" placeholder="eg. Doe" required><br><br>

            <label for="customer_email">Email address:</label>
            <input type="email" name="customer_email" id="customer_email" placeholder="eg. johndoe@gmail.com" required><br><br>

            <label for="customer_phone_number">Phone Number:</label>
            <input type="tel" name="customer_phone_number" id="customer_phone_number" placeholder="eg. 6123445565" required><br><br>

            <label for="customer_address">Address:</label>
            <input type="text" name="customer_address" id="customer_address" placeholder="eg. 24 Wakefield St, Hawthorn VIC 3122, Australia"><br><br>

            <input type="submit" name="submit_customer" class="button button-primary" value="Add Customer">
        </form>
    </div>
    <?php
}


// Callback function to display all the customers
function display_customer_data_page() {
    // Retrieve customer data from your custom table
    $customers_data = display_customers();

    // Display the data in a custom table
    ?>
    <div class="wrap">
        <h2>Customer Data</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers_data as $customer) : ?>
                    <tr>
                        <td><?php echo $customer->fname; ?></td>
                        <td><?php echo $customer->lname; ?></td>
                        <td><?php echo $customer->email; ?></td>
                        <td><?php echo $customer->mobile_phone; ?></td>
                        <td><?php echo $customer->address; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

function display_customers() {
    include_once(MY_PLUGIN_PATH .'includes/models/CustomerModel.php');

    // Instantiate the CustomerModel
    $customer_model = new CustomerModel();

    return $customer_model->get_all_customers();
}

?>