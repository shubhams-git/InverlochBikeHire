<?php
// Prevent direct access
if (!defined('ABSPATH')) exit;

// Include necessary models
include_once plugin_dir_path(__FILE__) . 'models/CategoryModel.php';
include_once plugin_dir_path(__FILE__) . 'models/ItemModel.php';
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
    $categoryModel = new CategoryModel();
    switch ($action_type) {
        case 'add':
            // Extract and sanitize form data
            // Perform addition logic
            // Set success or error message
            break;
        case 'edit':
            // Extract and sanitize form data
            // Perform edit logic
            // Set success or error message
            break;
        case 'delete':
            // Extract category ID
            // Perform deletion logic
            // Set success or error message
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



// Additional functions for other entities as needed
