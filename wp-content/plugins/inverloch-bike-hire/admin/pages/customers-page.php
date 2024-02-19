<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load necessary models
include_once plugin_dir_path(__DIR__) . '../includes/models/CustomerModel.php';

$customer_model = new CustomerModel();
 
// Retrieve customer data from your custom table
$edit_customer_id = isset($_GET['edit']) ? intval($_GET['edit']) : null;
$customer_to_edit = $edit_customer_id ? $customer_model->get_customer_by_id($edit_customer_id) : null;
$customers_data = $customer_model->get_all_customers();

?>
<div class="wrap">
    <h1><?php echo $edit_customer_id ? 'Edit Customer' : 'Add New Customer' ?></h1>
    <form method="post" id="customer">
        <input type="hidden" id="entity" name="entity" value="customer">
        <input type="hidden" id="action_type" name="action_type" value="<?php echo $edit_customer_id ? 'edit' : 'add'; ?>">
        <?php if ($edit_customer_id): ?>
            <input type="hidden" name="customer_id" value="<?php echo esc_attr($edit_customer_id); ?>">
        <?php endif; ?>
        <div id="messageContainer"></div>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="customer_fname">First Name*</label></th>
                <td><input type="text" id="customer_fname" name="customer_fname" required class="regular-text" value="<?php echo esc_attr($edit_customer_id ? $customer_to_edit->fname : ''); ?>"></td>
            </tr>
            <tr>
                <th scope="row"><label for="customer_lname">Last Name*</label></th>
                <td><input type="text" id="customer_lname" name="customer_lname" required class="regular-text" value="<?php echo esc_attr($edit_customer_id ? $customer_to_edit->lname : ''); ?>"></td>
            </tr>
            <tr>
                <th scope="row"><label for="customer_mobile_phone">Mobile Phone*</label></th>
                <td><input type="text" id="customer_mobile_phone" name="customer_mobile_phone" required class="regular-text" value="<?php echo esc_attr($edit_customer_id ? $customer_to_edit->mobile_phone : ''); ?>"></td>
            </tr>
            <tr>
                <th scope="row"><label for="customer_email">Email Address</label></th>
                <td><input type="email" id="customer_email" name="customer_email" class="regular-text" value="<?php echo esc_attr($edit_customer_id ? $customer_to_edit->email : ''); ?>"></td>
            </tr>
            <tr>
                <th scope="row"><label for="customer_address">Address</label></th>
                <td><input type="text" id="customer_address" name="customer_address" class="regular-text" value="<?php echo esc_attr($edit_customer_id ? $customer_to_edit->address : ''); ?>"></td>
            </tr>
            <tr>
                <td>
                    <?php submit_button($edit_customer_id ? 'Update Customer' : 'Add Customer', 'primary', 'submit_customer', false); ?>
                    <?php if ($edit_customer_id): ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=ibh_customers')); ?>" class="button button-secondary">Back</a>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </form>
</div>

<?php if (!$edit_customer_id): ?>
<div class="wrap">
    <h2>Customer Data</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email Address</th>
                <th>Mobile Phone</th>
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
                        <a href="?page=ibh_customers&edit=<?php echo $customer->customer_id; ?>" class="button button-primary">Edit</a>
                        <a href="?page=ibh_customers" class="button button-secondary delete-customer" data-customer-id="<?php echo $customer->customer_id; ?>">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($customers_data)): ?>
                <tr><td colspan="6">No customers found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>