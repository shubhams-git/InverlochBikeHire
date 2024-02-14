<?php
// Prevent direct access
if (!defined('ABSPATH')) exit;

// Include necessary models
include_once plugin_dir_path(__FILE__) . 'models/CategoryModel.php';
include_once plugin_dir_path(__FILE__) . 'models/ItemModel.php';
include_once plugin_dir_path(__FILE__) . 'models/PricePointModel.php';
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

function handle_price_point_actions($action_type) {
    global $wpdb; 
    switch ($action_type) {
        case 'update':
            update_price_points_action();
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

    foreach ($_POST['price'] as $category_id_str => $timeframes) {
        $category_id = intval($category_id_str);
        
        foreach ($timeframes as $timeframe => $amount_str) {
            $amount = floatval($amount_str);
            $timeframe_sanitized = sanitize_text_field($timeframe);

            // Validate data
            if ($category_id <= 0 || empty($timeframe_sanitized) || !is_numeric($amount)) {
                // Skip invalid entries
                continue;
            }

            // Check if a price point already exists for the given category_id and timeframe
            $existing = $pricePointModel->get_price_point_by_category_and_timeframe($category_id, $timeframe_sanitized);

            if ($existing) {
                // Update existing price point
                $update_result = $pricePointModel->update($existing->price_point_id, $category_id, $timeframe_sanitized, $amount);
                if (is_wp_error($update_result)) {
                    wp_send_json_error(['message' => 'Failed to update price point.', 'error' => $update_result->get_error_message()]);
                    return;
                }
            } else {
                // Insert new price point
                $insert_result = $pricePointModel->insert($category_id, $timeframe_sanitized, $amount);
                if (is_wp_error($insert_result)) {
                    wp_send_json_error(['message' => 'Failed to insert new price point.', 'error' => $insert_result->get_error_message()]);
                    return;
                }
            }
        }
    }

    wp_send_json_success(['message' => 'Price points updated successfully.']);
}








// Additional functions for other entities as needed
