<?php
// Prevent direct access
if (!defined('ABSPATH')) exit;

// Include necessary models
include_once plugin_dir_path(__FILE__) . 'models/ReservationModel.php';
include_once plugin_dir_path(__FILE__) . 'models/CustomerModel.php';
include_once plugin_dir_path(__FILE__) . 'models/BlockedDateModel.php';
include_once plugin_dir_path(__FILE__) . 'models/ItemBookingModel.php';
include_once plugin_dir_path(__FILE__) . 'models/ItemModel.php';
include_once plugin_dir_path(__FILE__) . 'models/CategoryModel.php';
include_once plugin_dir_path(__FILE__) . 'models/PricePointModel.php';
include_once plugin_dir_path(__FILE__) . 'models/EmailModel.php';

// Include other models as needed

// Register AJAX actions for logged-in and non-logged-in users
add_action('wp_ajax_ibh_handle_form', 'ibh_handle_form_submission');
add_action('wp_ajax_nopriv_ibh_handle_form', 'ibh_handle_form_submission');

function ibh_handle_form_submission() {
    
    // Check for nonce for security
    if (!check_ajax_referer('ibh_form_nonce', '_wpnonce', false)) {
        wp_send_json_error(['message' => 'Nonce verification failed.']);
        return; 
    }

    $action_type = sanitize_text_field($_POST['action_type'] ?? '');
    $entity = sanitize_text_field($_POST['entity'] ?? '');


    switch ($entity) {
        case 'item':
            handle_item_actions($action_type);
            break;
        case 'category':
            handle_category_actions($action_type);
            break;
        case 'price_point': 
            handle_price_point_actions($action_type);
        case 'email':
            handle_email_actions($action_type);
            break;
        case 'customer':
            handle_customer_actions($action_type);
            break;
        case 'reservation': 
            handle_reservation_actions($action_type);
            break;
        case 'blocked_date': 
            handle_blocked_date_actions($action_type);
            break;
        // Add more entities as needed
    }
}

function handle_item_actions($action_type) {
    switch ($action_type) {
        case 'add':
            add_item_action();
            break;
        case 'edit':
            edit_item_action();
            break;
        case 'delete':
            delete_item_action();
            break;
    }
}

function handle_category_actions($action_type) {
    switch ($action_type) {
        case 'add':
            add_category_action();
            break;
        case 'edit':
            edit_category_action();
            break;
        case 'delete':
            delete_category_action();
            break;
    }
}

function handle_blocked_date_actions($action_type) {
    switch ($action_type) {
        case 'add':
            add_blocked_date_action();
            break;
        case 'delete':
            delete_blocked_date_action();
            break;
    }
}

function handle_price_point_actions($action_type) {
    switch ($action_type) {
        case 'update':
            update_price_points_action();
    }
}

function handle_email_actions($action_type) {
    switch ($action_type) {
        case 'edit':
            edit_email_action();
            break;
    }
}

function handle_customer_actions($action_type) {
    switch ($action_type) {
        case 'add':
            add_customer_action();
            break;
        case 'edit':
            edit_customer_action();
            break;
        case 'delete':
            delete_customer_action();
            break;
    }
}

function handle_reservation_actions($action_type){
    switch ($action_type) {
        case 'fetch_reservations':
            fetch_reservations_action();
            break;

        case 'add_new_reservation':
            handle_add_new_reservation();
            break;
    
        case 'update_reservation':
            handle_edit_reservation_update();
            break;

        case 'delete_reservation':
            handle_delete_reservation();
            break;
    }
}

function add_item_action() {
    $itemModel = new ItemModel();
    // Check if required fields are set and not empty
    if (isset($_POST['name'], $_FILES['item_image']) && !empty($_POST['name'])) {
        // Handle file upload
        if (!function_exists('media_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }
        
        $attachment_id = media_handle_upload('item_image', 0); // 0 means no parent post

        if (is_wp_error($attachment_id)) {
            wp_send_json_error(['message' => 'Error uploading image: ' . $attachment_id->get_error_message()]);
        } else {
            $image_url = wp_get_attachment_url($attachment_id);
            $data = [
                'id_number' => sanitize_text_field($_POST['id_number']),
                'category_id' => intval($_POST['category_id']),
                'name' => sanitize_text_field($_POST['name']),
                'description' => sanitize_textarea_field($_POST['description']),
                'size' => sanitize_text_field($_POST['size']),
                'status' => sanitize_text_field($_POST['status']),
                'image_url' => $image_url,
            ];

            $insertResult = $itemModel->insert($data);
            if (is_wp_error($insertResult)) {
                wp_send_json_error(['message' => 'Error adding item.']);
            } else {
                wp_send_json_success(['message' => 'Item added successfully!']);
            }
            exit;   
        }
    }
}

function edit_item_action() {
    $itemModel = new ItemModel();
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : null;
    
    if (!$item_id || !($existing_item = $itemModel->get_item_by_id($item_id))) {
        wp_send_json_error(['message' => 'Invalid or non-existent item ID.']);
        return;
    }

    $image_url = $existing_item->image_url; // Use existing image URL by default

    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $attachment_id = media_handle_upload('item_image', 0);
        if (is_wp_error($attachment_id)) {
            wp_send_json_error(['message' => 'Error uploading image: ' . $attachment_id->get_error_message()]);
            return;
        }
        
        $image_url = wp_get_attachment_url($attachment_id); // Update with new image URL
    }

    $data = [
        'id_number' => sanitize_text_field($_POST['id_number']),
        'category_id' => intval($_POST['category_id']),
        'name' => sanitize_text_field($_POST['name']),
        'description' => sanitize_textarea_field($_POST['description']),
        'size' => sanitize_text_field($_POST['size']),
        'status' => sanitize_text_field($_POST['status']),
        'image_url' => $image_url,
    ];

    if (!$itemModel->update($item_id, $data)) {
        wp_send_json_error(['message' => 'Failed to update item.']);
        return;
    }

    wp_send_json_success(['message' => 'Item updated successfully.']);
}


function delete_item_action() {
    $itemModel = new ItemModel();
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : null;
    
    if (!$item_id || !($existing_item = $itemModel->get_item_by_id($item_id))) {
        wp_send_json_error(['message' => 'Invalid or non-existent item ID.']);
        return;
    }

    if (!$itemModel->delete($item_id)) {
        wp_send_json_error(['message' => 'Failed to delete item.']);
        return;
    }

    wp_send_json_success(['message' => 'Item deleted successfully.']);
}

function add_category_action() {
    $categoryModel = new CategoryModel();
    $category_name = isset($_POST['category_name']) ? sanitize_text_field($_POST['category_name']) : '';

    if (empty($category_name)) {
        wp_send_json_error(['message' => 'Category name is required.']);
        return;
    }

    $insertResult = $categoryModel->insert($category_name);
    if (is_wp_error($insertResult)) {
        wp_send_json_error(['message' => 'Error adding category.']);
    } else {
        wp_send_json_success(['message' => 'Category added successfully!']);
    }
    exit;
}


function edit_category_action() {
    $categoryModel = new CategoryModel();
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $category_name = isset($_POST['category_name']) ? sanitize_text_field($_POST['category_name']) : '';

    if (!$category_id || empty($category_name)) {
        wp_send_json_error(['message' => 'Invalid request. Category ID and name are required.']);
        return;
    }

    $updateResult = $categoryModel->update($category_id, $category_name);
    if (is_wp_error($updateResult)) {
        wp_send_json_error(['message' => 'Failed to update category.']);
    } else {
        wp_send_json_success(['message' => 'Category updated successfully.']);
    }
    exit;
}


function delete_category_action() {
    $categoryModel = new CategoryModel();
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : null;

    if (!$category_id) {
        wp_send_json_error(['message' => 'Invalid or non-existent category ID.']);
        return;
    }

    // Check if the category is referenced by items
    if ($categoryModel->is_category_referenced($category_id)) {
        wp_send_json_error(['message' => 'Cannot delete the category because it is being linked with one or more items. Please review the Inventory.']);
        return;
    }

    $deleteResult = $categoryModel->delete($category_id);
    if (is_wp_error($deleteResult)) {
        wp_send_json_error(['message' => 'Failed to delete category.']);
    } else {
        wp_send_json_success(['message' => 'Category deleted successfully.']);
    }
    exit;
}

function update_price_points_action() {
    $pricePointModel = new PricePointModel();

    // Check if price data exists and is an array
    if (!isset($_POST['price']) || !is_array($_POST['price'])) {
        wp_send_json_error(['message' => 'Invalid price data.']);
        return;
    }

    $pricePointModel->delete_all();

    foreach ($_POST['price'] as $category_id_str => $timeframes) {
        $category_id = intval($category_id_str);
        
        foreach ($timeframes as $timeframe => $amount_str) {
            if($amount_str == null) {
                $amount = null;
            } else {
                $amount = floatval($amount_str);
            }
            $timeframe_sanitized = sanitize_text_field($timeframe);

            // Validate data
            if ($category_id <= 0 || empty($timeframe_sanitized) || !is_numeric($amount)) {
                // Skip invalid entries
                continue;
            }

            // Insert new price point
            $insert_result = $pricePointModel->insert($category_id, $timeframe_sanitized, $amount);
            if (is_wp_error($insert_result)) {
                wp_send_json_error(['message' => 'Failed to insert new price point.', 'error' => $insert_result->get_error_message()]);
                return;
            }
        }
    }

    wp_send_json_success(['message' => 'Price points updated successfully.']);
}

function edit_email_action() {
    $emailModel = new EmailModel();
    $email_id = isset($_POST['email_id']) ? intval($_POST['email_id']) : null;
    $check_id = $emailModel->get_email_by_id($email_id);

    if (!$email_id || $check_id->email_id != $email_id) {
        wp_send_json_error(['message' => 'Invalid or non-existent email ID.']);
        return;
    }

    $data = [
        'email_type' => sanitize_text_field($_POST['email_type']),
        'subject' => sanitize_text_field($_POST['email_subject']),
        'content' => sanitize_text_field($_POST['email_content'])
    ];

    if (!$emailModel->update($email_id, $data)) {
        wp_send_json_error(['message' => 'Failed to update email.']);
        return;
    }

    wp_send_json_success(['message' => 'Email updated successfully.']);
}

function add_customer_action() {
    $customerModel = new CustomerModel();
    // Check if required fields are set and not empty
    $data = [
        'fname' => sanitize_text_field($_POST['customer_fname']),
        'lname' => sanitize_text_field($_POST['customer_lname']),
        'email' => sanitize_text_field($_POST['customer_email']),
        'mobile_phone' => sanitize_text_field($_POST['customer_mobile_phone']),
        'address' => sanitize_text_field($_POST['customer_address'])
    ];

    $insertResult = $customerModel->insert($data);
    if (is_wp_error($insertResult)) {
        wp_send_json_error(['message' => 'Error adding customer.']);
    } else {
        wp_send_json_success(['message' => 'Customer added successfully!']);
    }
    exit;
}

function edit_customer_action() {
    $customerModel = new CustomerModel();

    $customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : null;
    $check_id = $customerModel->get_customer_by_id($customer_id);

    if (!$customer_id || $check_id->customer_id != $customer_id) {
        wp_send_json_error(['message' => 'Invalid or non-existent customer ID.']);
        return;
    }

    $data = [
        'fname' => sanitize_text_field($_POST['customer_fname']),
        'lname' => sanitize_text_field($_POST['customer_lname']),
        'email' => sanitize_text_field($_POST['customer_email']),
        'mobile_phone' => sanitize_text_field($_POST['customer_mobile_phone']),
        'address' => sanitize_text_field($_POST['customer_address'])
    ];

    if (!$customerModel->update($customer_id, $data)) {
        wp_send_json_error(['message' => 'Failed to update customer.']);
        return;
    }

    wp_send_json_success(['message' => 'Customer updated successfully.']);

}

function delete_customer_action() {
    $customerModel = new CustomerModel();
    $customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : null;

    if (!$customer_id) {
        wp_send_json_error(['message' => 'Invalid or non-existent category ID.']);
        return;
    }

    // Check if the customer is referenced by reservation(s)
    if ($customerModel->is_customer_referenced($customer_id)) {
        wp_send_json_error(['message' => 'Cannot delete the customer because it is being linked with one or more reservations. Please review the Reservation(s).']);
        return;
    }

    $deleteResult = $customerModel->delete($customer_id);
    if (is_wp_error($deleteResult)) {
        wp_send_json_error(['message' => 'Failed to delete customer.']);
    } else {
        wp_send_json_success(['message' => 'Customer deleted successfully.']);
    }
    exit;
}

function fetch_reservations_action() {

    $is_edit = isset($_POST['is_edit']) ? (bool)$_POST['is_edit'] : false;
    $reservation_id = isset($_POST['reservation_id']) ? intval($_POST['reservation_id']) : 0;

    // If it's an edit request, fetch existing reservation details and booked items
    if ($is_edit && $reservation_id > 0) {
        handle_edit_reservation_fetch($reservation_id);
        return; // Stop execution to avoid fetching available bikes as for a new reservation
    }

    // Assuming $_POST data includes 'from_date', 'to_date', 'from_time', and 'to_time'
    $from_date = sanitize_text_field($_POST['reservation_fromdate'] ?? '');
    $to_date = sanitize_text_field($_POST['reservation_todate'] ?? '');
    $from_time = sanitize_text_field($_POST['reservation_fromtime'] ?? '');
    $to_time = sanitize_text_field($_POST['reservation_totime'] ?? '');

    if (empty($from_date) || empty($to_date) || empty($from_time) || empty($to_time)) {
        wp_send_json_error(['message' => 'Missing required fields for fetching reservations.']);
        return;
    }

    $reservation_model = new ReservationModel();
    $booked_reservation_ids = $reservation_model->get_reservation_ids_by_date_time_range($from_date, $to_date, $from_time, $to_time);

    // Fetch available bikes excluding those booked in the given time range
    $item_booking_model = new ItemBookingModel();
    $booked_bike_ids = $item_booking_model->get_item_ids_by_reservation_ids($booked_reservation_ids);

    $item_model = new ItemModel();
    $available_bikes = $item_model->get_items_except_specified_ids($booked_bike_ids);
    
    if (empty($available_bikes)) {
        wp_send_json_success(['message' => 'No available bikes found for the specified time range.', 'html' => '']);
        return;
    }
    
    // Fetch categories for available bikes
    $categoryModel = new CategoryModel();
    $categories = $categoryModel->get_all_categories();
    
    // Fetch customer data
    $customerModel = new CustomerModel();
    $customer_data = $customerModel->get_all_customers();

    // Generate HTML markup for available bikes form
    $html = generate_available_bikes_form_html($available_bikes, $categories, $customer_data);

    wp_send_json_success(['message' => 'Available bikes fetched successfully.', 'html' => $html]);
}

function generate_available_bikes_form_html($available_bikes, $categories, $customer_data) {
    $encountered_categories = array();  // store categories
    ob_start(); ?>
    <form id="reservation-form" method="post" action="">
        <div class="postbox">
            <div class="inside">
                <h3 style="text-align: center;">Bike Bookings</h3>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Image</th>
                            <th>ID Number</th>
                            <th>Name</th>
                            <th>Size</th>
                        </tr>
                    </thead>
                   <!-- Categories and Bikes -->
                    <?php foreach ($categories as $category): ?>
                        <tbody class="labels">
                            <tr>
                                <td colspan="5">
                                    <!-- Toggle label for category -->
                                    <div class="toggle-category-label" id="label-category-<?php echo esc_attr($category->category_id); ?>">
                                        <?php echo esc_html($category->category_name); ?>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody class="bikes hide" id="bikes-category-<?php echo esc_attr($category->category_id); ?>">
                            <?php 
                            $hasBikes = false;
                            foreach ($available_bikes as $bike): 
                                if ($bike->category_id === $category->category_id): 
                                    $hasBikes = true; ?>
                                    <tr class="bike">
                                        <td>
                                            <input type="checkbox" id="bike-<?php echo esc_attr($bike->item_id); ?>" name="selected_bikes[]" value="<?php echo esc_attr($bike->item_id); ?>">
                                        </td>
                                        <td>
                                            <img width="60" height="60" src="<?php echo esc_url($bike->image_url); ?>" alt="Bike Image">
                                        </td>
                                        <td><?php echo esc_html($bike->id_number); ?></td>
                                        <td><?php echo esc_html($bike->name); ?></td>
                                        <td><?php echo esc_html($bike->size); ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if (!$hasBikes): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center;">
                                        Currently, no bikes are available in the "<?php echo esc_html($category->category_name); ?>" category for the selected timeframes.  
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        <!-- Additional Information -->
        <div class="postbox">
            <div class="inside">
                <h3 style="text-align: center;">Additional information</h3>
                <table class="form-table">
                    <!-- Customer Selection -->
                    <tr>
                        <th scope="row"><label for="reservation_customer">Customer</label></th>
                        <td colspan="3">
                            <select name="reservation_customer" id="reservation_customer" required>
                                <option value="" disabled selected>Please select a customer</option>
                                <?php if (!empty($customer_data)): ?>
                                    <?php foreach ($customer_data as $customer): ?>
                                        <option value="<?php echo esc_attr($customer->customer_id); ?>"><?php echo esc_html($customer->fname . " " . $customer->lname . " (" . $customer->mobile_phone .")"); ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No customers found.</option>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                    <!-- Notes -->
                    <tr>
                        <th scope="row"><label for="reservation_notes">Notes</label></th>
                        <td colspan="3"><textarea id="reservation_notes" name="reservation_notes" rows="4" cols="50" class="large-text"></textarea></td>
                    </tr>
                    <!-- Stage Selection -->
                    <tr>
                        <th scope="row"><label for="reservation_stage">Stage</label></th>
                        <td colspan="3">
                            <select name="reservation_stage" id="reservation_stage">
                                <option value="provisional">Provisional</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="checked-in">Checked-in</option>
                                <option value="checked-out">Checked-out</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <input type="submit" id="submit_reservation" class="button button-primary" value="Add Reservation">
    </form>

    <?php
    $html_content = ob_get_clean();
    return $html_content;
}

function generate_edit_reservation_form_html($reservation_details, $bookedItems){
    
    $reservation_model = new ReservationModel();
    $booked_reservation_ids = $reservation_model->get_reservation_ids_by_date_time_range($from_date, $to_date, $from_time, $to_time);

    // Fetch available bikes excluding those booked in the given time range
    $item_booking_model = new ItemBookingModel();
    $booked_bike_ids = $item_booking_model->get_item_ids_by_reservation_ids($booked_reservation_ids);

    $item_model = new ItemModel();
    $available_bikes = $item_model->get_items_except_specified_ids($booked_bike_ids);
    
    if (empty($available_bikes)) {
        wp_send_json_success(['message' => 'No available bikes found for the specified time range.', 'html' => '']);
        return;
    }
    
    // Fetch categories for available bikes
    $categoryModel = new CategoryModel();
    $categories = $categoryModel->get_all_categories();
    
    // Fetch customer data
    $customerModel = new CustomerModel();
    $customer_data = $customerModel->get_all_customers();

    $selected_customer_id = $reservation_details->customer_id;
    $bookedItemIds = array_map(function($item) { return $item['item_id']; }, $bookedItems);
    
    ob_start(); ?>
        <form id="update-reservation" >
        <h2>Edit Reservation</h2>
        <input type="hidden" name="reservation_id" value="<?php echo esc_attr($reservation_details->reservation_id); ?>">

        <!-- Reservation Dates and Times -->
        <div class="inside">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="reservation_fromdate">From Date</label></th>
                        <td><input type="text" id="reservation_fromdate" name="reservation_fromdate" class="regular-text" value="<?php echo esc_attr($reservation_details->from_date); ?>"></td>
                        <th scope="row"><label for="reservation_fromtime">From Time</label></th>
                        <td><input type="text" id="reservation_fromtime" name="reservation_fromtime" class="regular-text" value="<?php echo esc_attr($reservation_details->from_time); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="reservation_todate">To Date</label></th>
                        <td><input type="text" id="reservation_todate" name="reservation_todate" class="regular-text" value="<?php echo esc_attr($reservation_details->to_date); ?>"></td>
                        <th scope="row"><label for="reservation_totime">To Time</label></th>
                        <td><input type="text" id="reservation_totime" name="reservation_totime" class="regular-text" value="<?php echo esc_attr($reservation_details->to_time); ?>"></td>
                    </tr>
                </tbody>
            </table>
        </div>
            <div class="postbox">
                <div class="inside">
                    <h3 style="text-align: center;">Bike Bookings</h3>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Image</th>
                                <th>ID Number</th>
                                <th>Name</th>
                                <th>Size</th>
                            </tr>
                        </thead>
                    <!-- Categories and Bikes -->
                    <?php foreach ($categories as $category): ?>
                        <tbody class="labels">
                            <tr>
                                <td colspan="5">
                                    <div class="toggle-category-label" id="label-category-<?php echo esc_attr($category->category_id); ?>">
                                        <?php echo esc_html($category->category_name); ?>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody class="bikes hide" id="bikes-category-<?php echo esc_attr($category->category_id); ?>">
                            <?php 
                            foreach ($available_bikes as $bike): 
                                if ($bike->category_id === $category->category_id): 
                                    $hasBikes = true;
                                    // Check if this bike is in the booked items
                                    $isChecked = in_array($bike->item_id, $bookedItemIds) ? 'checked' : ''; ?>
                                    <tr class="bike">
                                        <td>
                                            <input type="checkbox" id="bike-<?php echo esc_attr($bike->item_id); ?>" name="selected_bikes[]" value="<?php echo esc_attr($bike->item_id); ?>" <?php echo $isChecked; ?>>
                                        </td>
                                        <td>
                                            <img width="60" height="60" src="<?php echo esc_url($bike->image_url); ?>" alt="Bike Image">
                                        </td>
                                        <td><?php echo esc_html($bike->id_number); ?></td>
                                        <td><?php echo esc_html($bike->name); ?></td>
                                        <td><?php echo esc_html($bike->size); ?></td>
                                    </tr>

                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <!-- Additional Information -->
        <div class="postbox">
            <div class="inside">
                <h3 style="text-align: center;">Additional Information</h3>
                <table class="form-table">
                    <!-- Customer Selection -->
                    <tr>
                        <th scope="row"><label for="reservation_customer">Customer</label></th>
                        <td colspan="3">
                            <select name="reservation_customer" id="reservation_customer" required>
                                <option value="" disabled>Please select a customer</option>
                                <?php foreach ($customer_data as $customer): ?>
                                    <option value="<?php echo esc_attr($customer->customer_id); ?>" <?php echo ($customer->customer_id == $selected_customer_id) ? 'selected' : ''; ?>>
                                        <?php echo esc_html($customer->fname . " " . $customer->lname . " (" . $customer->mobile_phone .")"); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <!-- Notes -->
                    <tr>
                        <th scope="row"><label for="reservation_notes">Notes</label></th>
                        <td colspan="3">
                            <textarea id="reservation_notes" name="reservation_notes" rows="4" class="large-text"><?php echo esc_textarea($reservation_details->delivery_notes); ?></textarea>
                        </td>
                    </tr>
                    <!-- Stage Selection -->
                    <tr>
                        <th scope="row"><label for="reservation_stage">Stage</label></th>
                        <td colspan="3">
                            <select name="reservation_stage" id="reservation_stage">
                                <!-- Populate options dynamically based on your system's stages -->
                                <option value="provisional" <?php echo ($reservation_details->reservation_stage == 'provisional') ? 'selected' : ''; ?>>Provisional</option>
                                <option value="confirmed" <?php echo ($reservation_details->reservation_stage == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                <!-- Add more stages as needed -->
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <input type="submit" class="button button-primary"  value="Save Changes">
        <button id= "go-back-button-edit-reservation" class="button button-secondary"> Go Back </button>

    </form>
    <?php
    $html_content = ob_get_clean();
    return $html_content;
}

function handle_add_new_reservation() {

    // Instantiate necessary models
    $reservationModel = new ReservationModel();
    $itemBookingModel = new ItemBookingModel();
    $customerModel = new CustomerModel(); 

    // Collect and sanitize input data
    $customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : null;
    $selected_bikes = isset($_POST['selected_bikes']) ? array_map('intval', $_POST['selected_bikes']) : [];
    $reservation_data = [
        'customer_id' => $customer_id,
        'from_date' => sanitize_text_field($_POST['from_date']),
        'to_date' => sanitize_text_field($_POST['to_date']),
        'from_time' => sanitize_text_field($_POST['from_time']),
        'to_time' => sanitize_text_field($_POST['to_time']),
        'reservation_stage' => sanitize_text_field($_POST['reservation_stage']),
        'created_date' => current_time('mysql'),
        'delivery_notes' => sanitize_textarea_field($_POST['reservation_notes'])
    ];

    // Validate customer ID
    if (!$customerModel->is_valid_customer_id($customer_id)) {
        wp_send_json_error(['message' => 'Invalid customer ID.']);
        return;
    }

    // Begin transaction if supported
    $reservationModel->start_transaction();

    try {
        // Insert reservation
        $reservation_id = $reservationModel->insert($reservation_data);
        if (is_wp_error($reservation_id)) {
            throw new Exception('Failed to create reservation.');
        }

        // Insert item bookings
        foreach ($selected_bikes as $item_id) {
            $booking_result = $itemBookingModel->insert([
                'reservation_id' => $reservation_id,
                'item_id' => $item_id,
            ]);
            if (is_wp_error($booking_result)) {
                throw new Exception('Failed to book selected bikes.');
            }
        }

        // Commit transaction
        $reservationModel->commit_transaction();

        // Success response
        wp_send_json_success(['message' => 'Reservation successfully created.', 'reservation_id' => $reservation_id]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $reservationModel->rollback_transaction();
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}
function handle_edit_reservation_fetch($reservation_id) {


    $reservationModel = new ReservationModel();
    $itemBookingModel = new ItemBookingModel(); 
    $itemModel = new ItemModel();

    // Retrieve the reservation details
    $reservation_details = $reservationModel->get_reservation_detail_by_id($reservation_id);

    if (!$reservation_details) {
        wp_send_json_error(['message' => 'Reservation not found.']);
        return; // Make sure to return to stop the execution here
    }


    $blockeddate_model = new BlockedDateModel();
    $blockeddate_data = $blockeddate_model->get_all_blocked_date();
    // Fetch booked items
    $bookedItems = get_dynamic_content_for_reservation($reservation_id);

    // Generate HTML for edit form
    $html = generate_edit_reservation_form_html($reservation_details, $bookedItems);

    wp_send_json_success(['html' => $html, 'blockedDates' => $blockeddate_data]);
}

function get_dynamic_content_for_reservation($reservation_id) {
    $itemBookingModel = new ItemBookingModel(); 
    $itemModel = new ItemModel();

    // Retrieve item IDs booked under this reservation
    $booked_items_ids = $itemBookingModel->get_item_ids_by_reservation_ids([$reservation_id]);
    $booked_items_details = [];

    // Retrieve details for each booked item
    foreach ($booked_items_ids as $item_id) {
        $item = $itemModel->get_item_by_id($item_id);
        if ($item) {
            $booked_items_details[] = [
                'item_id' => $item_id,
                'id_number' => $item->id_number,
                'name' => $item->name,
                'image_url' => $item->image_url // Assuming you have an image URL for each item
            ];
        }
    }
    return $booked_items_details;
}

function handle_edit_reservation_update() {
    $reservationModel = new ReservationModel();
    $itemBookingModel = new ItemBookingModel(); 
    $reservation_id = intval($_POST['reservation_id']);
    $updatedDetails = [
        // Populate with sanitized $_POST data
        //'reference_id' => sanitize_text_field($_POST['reference_id']),
        'customer_id' => sanitize_text_field($_POST['reservation_customer']),
        'from_date' => sanitize_text_field($_POST['reservation_fromdate']),
        'to_date' => sanitize_text_field($_POST['reservation_todate']),
        'from_time' => sanitize_text_field($_POST['reservation_fromtime']),
        'to_time' => sanitize_text_field($_POST['reservation_totime']),
        'reservation_stage' => sanitize_text_field($_POST['reservation_stage']),
        'created_date' => sanitize_text_field($_POST['created_date']),
        'delivery_notes' => sanitize_text_field($_POST['reservation_notes'])
        // Add other fields as needed
    ];
    $newItemIds = array_map('intval', $_POST['selected_bikes']); // Assuming 'selected_bikes' is an array of item IDs

    // Begin transaction
    $reservationModel->start_transaction();

    try {
        // Update reservation details
        $reservationModel->update($reservation_id, $updatedDetails);

        // Clear existing item bookings
        $itemBookingModel->clear_bookings_for_reservation($reservation_id);

        // Add new item bookings
        foreach ($newItemIds as $itemId) {
            $itemBookingModel->insert(['reservation_id' => $reservation_id, 'item_id' => $itemId]);
        }

        // Commit transaction
        $reservationModel->commit_transaction();

        wp_send_json_success(['message' => 'Reservation updated successfully.']);
    } catch (Exception $e) {
        // Rollback transaction on error
        $reservationModel->rollback_transaction();
        wp_send_json_error(['message' => 'Failed to update reservation. Error: ' . $e->getMessage()]);
    }
}

function handle_delete_reservation() {
    $reservationModel = new ReservationModel();
    // Assuming you're passing the reservation ID via POST request
    $reservation_id = isset($_POST['reservation_id']) ? intval($_POST['reservation_id']) : null;

    if (!$reservation_id) {
        wp_send_json_error(['message' => 'Invalid reservation ID.']);
        return;
    }

    // Attempt to delete the reservation
    $delete_result = $reservationModel->delete($reservation_id);

    if (is_wp_error($delete_result)) {
        // If there was an error during deletion, return the error message
        wp_send_json_error(['message' => $delete_result->get_error_message()]);
    } else {
        // On successful deletion, return a success message
        wp_send_json_success(['message' => 'Reservation deleted successfully.']);
    }
}
    


function add_blocked_date_action() {
    // Instantiate the BlockedDateModel
    $blockedDateModel = new BlockedDateModel();

    // Collect and sanitize input data
    $date = isset($_POST['blockeddate_date']) ? sanitize_text_field($_POST['blockeddate_date']) : null;
    $is_blocked = true;
    
    // Validate the date
    if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        wp_send_json_error(['message' => 'Invalid date format. Please use YYYY-MM-DD.']);
        return;
    }

    // Prepare data for insertion
    $data = [
        'date' => $date,
        'is_blocked' => $is_blocked
    ];

    // Insert the new blocked date
    $insert_result = $blockedDateModel->insert($data);

    // Check if the insertion was successful
    if ($insert_result === false) {
        wp_send_json_error(['message' => 'Failed to add the blocked date.']);
    } else {
        wp_send_json_success(['message' => 'Blocked date added successfully.']);
    }
}

function delete_blocked_date_action() {
    // Sanitize and validate the input
    $date = isset($_POST['blocked_date']) ? sanitize_text_field($_POST['blocked_date']) : '';
    if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        wp_send_json_error(['message' => 'Invalid or missing date.']);
        return;
    }
    
    // Instantiate the BlockedDateModel and attempt to delete the date
    $blockedDateModel = new BlockedDateModel();
    $result = $blockedDateModel->delete($date);
    
    if (is_wp_error($result)) {
        wp_send_json_error(['message' => 'Failed to delete blocked date.']);
    } else {
        wp_send_json_success(['message' => 'Blocked date deleted successfully.']);
    }
}


// Additional functions for other entities as needed
