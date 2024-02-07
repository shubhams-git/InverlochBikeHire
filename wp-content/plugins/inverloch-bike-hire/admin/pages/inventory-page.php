<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

include_once plugin_dir_path(__DIR__) . '../includes/models/CategoryModel.php';
include_once plugin_dir_path(__DIR__) . '../includes/models/ItemModel.php';

$categoryModel = new CategoryModel();
$itemModel = new ItemModel();
$message = '';

$edit_item_id = isset($_GET['edit']) ? intval($_GET['edit']) : null;

$item_to_edit = null;
if ($edit_item_id) {
    $item_to_edit = $itemModel->get_item_by_id($edit_item_id);
    // Check if item exists and if not, reset $edit_item_id and $item_to_edit
    if (!$item_to_edit) {
        $edit_item_id = null;
        $message = '<div class="notice notice-error"><p>Item not found.</p></div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_referer('add-item-nonce', '_wpnonce_add_item');
    if (!function_exists('media_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
    }
    // Additional checks for nonce, etc.
    if ($_POST['action'] === 'edit_item') {
        $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : null;
        if ($item_id) {
            // Prepare your data array from POST data
            $data = [
                // 'id_number' => sanitize_text_field($_POST['id_number']),
                // Similar for other fields
                'id_number' => sanitize_text_field($_POST['id_number']),
                'category_id' => intval($_POST['category_id']),
                'name' => sanitize_text_field($_POST['name']),
                'description' => sanitize_textarea_field($_POST['description']),
                'size' => sanitize_text_field($_POST['size']),
                'status' => sanitize_text_field($_POST['status']),
            ];
            // Call your update method instead of insert
            $updateResult = $itemModel->update($item_id, $data);
            if ($updateResult) {
                $message = '<div class="notice notice-success"><p>Item updated successfully.</p></div>';
                echo '<script>window.location.href="' . admin_url('admin.php?page=ibh_inventory') . '";</script>';
                exit;
            } else {
                $message = '<div class="notice notice-error"><p>Error updating item.</p></div>';
            }
        }
    }
    // Handle add_item action
    if ($_POST['action'] === 'add_item') {
        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
            $attachment_id = media_handle_upload('item_image', 0);
    
            if (is_wp_error($attachment_id)) {
                $message = '<div class="notice notice-error"><p>Error uploading image: ' . $attachment_id->get_error_message() . '</p></div>';
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
                $message = is_wp_error($insertResult) ? 
                    '<div class="notice notice-error"><p>Error: ' . $insertResult->get_error_message() . '</p></div>' : 
                    '<div class="notice notice-success is-dismissible"><p>Item added successfully!</p></div>';
            }
        } else {
            $message = '<div class="notice notice-error"><p>Error: No image selected.</p></div>';
        }
    }
}

$categories = $categoryModel->get_all_categories();
$items = $itemModel->get_all_items_with_category_name();
?>

<div class="wrap">
    <h1><?php echo $edit_item_id ? 'Edit Item' : 'Add New Item'; ?></h1>
    <?php echo $message; ?>
    <form method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('add-item-nonce', '_wpnonce_add_item'); ?>
        <input type="hidden" name="action" value="<?php echo $edit_item_id ? 'edit_item' : 'add_item'; ?>">
        <input type="hidden" name="item_id" value="<?php echo esc_attr($edit_item_id); ?>">

        <table class="form-table">
            <tr>
                <th scope="row"><label for="id_number">Identification Number</label></th>
                <td><input type="text" id="id_number" name="id_number" value="<?php echo esc_attr($edit_item_id ? $item_to_edit->id_number : ''); ?>" required class="large-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="category_id">Category</label></th>
                <td>
                    <select name="category_id" id="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo esc_attr($category->category_id); ?>" <?php echo $edit_item_id && $item_to_edit->category_id == $category->category_id ? 'selected' : ''; ?>><?php echo esc_html($category->category_name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="name">Name</label></th>
                <td><input type="text" id="name" name="name" value="<?php echo esc_attr($edit_item_id ? $item_to_edit->name : ''); ?>" required class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="description">Description</label></th>
                <td><textarea id="description" name="description" rows="5" class="large-text"><?php echo esc_textarea($edit_item_id ? $item_to_edit->description : ''); ?></textarea></td>
            </tr>
            <tr>
                <th scope="row"><label for="size">Size</label></th>
                <td><input type="text" id="size" name="size" value="<?php echo esc_attr($edit_item_id ? $item_to_edit->size : ''); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="item_image">Item Image</label></th>
                <td><input type="file" id="item_image" name="item_image" accept="image/*"></td>
            </tr>
            <tr>
                <th scope="row"><label for="status">Status</label></th>
                <td>
                    <select id="status" name="status" required>
                        <option value="Available" <?php echo $edit_item_id && $item_to_edit->status == 'Available' ? 'selected' : ''; ?>>Available</option>
                        <option value="Unavailable" <?php echo $edit_item_id && $item_to_edit->status == 'Unavailable' ? 'selected' : ''; ?>>Unavailable</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php submit_button($edit_item_id ? 'Update Item' : 'Add Item', 'primary', 'submit_item', false); ?></td>
            </tr>
        </table>
    </form>

    <?php if (!$edit_item_id): ?>
    <h2>Inventory List</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Identification Number</th>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Size</th>
                <th>Status</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo esc_html($item->id_number); ?></td>
                <td><?php echo esc_html($item->name); ?></td>
                <td><?php echo esc_html($item->category_name); ?></td>
                <td><?php echo ucfirst(esc_html($item->description)); ?></td> 
                <td><?php echo esc_html($item->size); ?></td>
                <td><?php echo ucfirst(esc_html($item->status)); ?></td> 
                <td><img src="<?php echo esc_url($item->image_url); ?>" alt="" style="width: 100px; height: auto;"></td>
                <td>
                    <a href="?page=ibh_inventory&edit=<?php echo $item->item_id; ?>">Edit</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
                <tr><td colspan="4">No items found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
