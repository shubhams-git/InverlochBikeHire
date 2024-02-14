<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load necessary models
include_once plugin_dir_path(__DIR__) . '../includes/models/CustomerModel.php';

$customer_model = new CustomerModel();

// Check if the form is submitted and process the data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'Add') {

    check_admin_referer('add_new_customer', 'add_new_customer_nonce');

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
            <p>Customer added successfully!</p>
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

if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['customers'])) {
    // Verify nonce for security
    if (wp_verify_nonce($_GET['_wpnonce'], 'delete-customers-action')) 
    {
        $deletion = $customer_model->delete($_GET['customers']);
        if (is_wp_error($deletion)) {
            echo '<div class="error"><p>Error deleting customer: ' . $deletion->get_error_message() . '</p></div>';
        } else {
            echo '<div class="updated"><p>Customer deleted successfully!</p></div>';
        }
        ?>
        <script>
            // Modify url to the page only after deleting
            window.history.replaceState({}, document.title, window.location.href.split('&')[0]);
        </script>
        <?php
    }
}
 
// Retrieve customer data from your custom table
$customers_data = $customer_model->get_all_customers();

// Display the form and table
?>
<div class="wrap">
    <h2>Add New Customer</h2>

    <form method="post" action="">
        <?php wp_nonce_field('add_new_customer', 'add_new_customer_nonce'); ?>
        <input type="hidden" name="action" value="Add">
        <input type="hidden" name="customer_id" id="customer_id" value="<?php echo esc_html($customer_info['id']); ?>">

        <table class="form-table">
            <tr>
                <th scope="row"><label for="customer_fname">First Name*</label></th>
                <td><input type="text" id="customer_fname" name="customer_fname" required class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="customer_lname">Last Name*</label></th>
                <td><input type="text" id="customer_lname" name="customer_lname" required class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="customer_email">Email Address</label></th>
                <td><input type="email" id="customer_email" name="customer_email" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="customer_phone_number">Phone Number*</label></th>
                <td><input type="text" id="customer_phone_number" name="customer_phone_number" required class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="customer_address">Address</label></th>
                <td><input type="text" id="customer_address" name="customer_address" class="regular-text"></td>
            </tr>
            <tr>
                <td><input type="submit" name="submit_customer" class="button button-primary" value="Add Customer"></td>
            </tr>
        </table>
    </form>
</div>

<div class="wrap">
    <h2>Customer Data</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers_data as $customer) : ?>
                <tr>
                    <td><?php echo esc_html($customer->fname); ?></td>
                    <td><?php echo esc_html($customer->lname); ?></td>
                    <td><?php echo esc_html($customer->email); ?></td>
                    <td><?php echo esc_html($customer->mobile_phone); ?></td>
                    <td><?php echo esc_html($customer->address); ?></td>
                    <td>
                        <a href="" class="edit-customers button button-primary">Edit</a>
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=ibh_customers&action=delete&customers=' . $customer->customer_id), 'delete-customers-action')); ?>" class="delete-customers button button-secondary">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($customers_data)): ?>
                <tr><td colspan="6">No customers found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    jQuery(document).ready(function($) {
        $('.delete-customers').on('click', function(e) {
            if (!confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
</script>