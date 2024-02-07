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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_item') {
    check_admin_referer('add-item-nonce', '_wpnonce_add_item');

    if (!function_exists('media_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
    }

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

$categories = $categoryModel->get_all_categories();
$items = $itemModel->get_all_items();
?>

<div class="wrap">
    <h1>Add New Item</h1>
    <?php echo $message; ?>
    <form method="post" action="" enctype="multipart/form-data">
        <?php wp_nonce_field('add-item-nonce', '_wpnonce_add_item'); ?>
        <input type="hidden" name="action" value="add_item">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="id_number">Identification Number</label></th>
                <td><input type="text" id="id_number" name="id_number" required class="large-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="category_id">Category</label></th>
                <td>
                    <select name="category_id" id="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo esc_attr($category->category_id); ?>"><?php echo esc_html($category->category_name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="name">Name</label></th>
                <td><input type="text" id="name" name="name" required class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="description">Description</label></th>
                <td><textarea id="description" name="description" rows="5" class="large-text"></textarea></td>
            </tr>
            <tr>
                <th scope="row"><label for="size">Size</label></th>
                <td><input type="text" id="size" name="size" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="item_image">Item Image</label></th>
                <td><input type="file" id="item_image" name="item_image" accept="image/*"></td>
            </tr>
            <tr>
                <th scope="row"><label for="status">Status</label></th>
                <td>
                    <select id="status" name="status" required>
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php submit_button('Add Item', 'primary', 'submit_item', false); ?></td>
            </tr>
        </table>
    </form>

    <h2>Inventory List</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Identification Number</th>
                <th>Description</th>
                <th>Size</th>
                <th>Status</th>
                <th>Image</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo esc_html($item->name); ?></td>
                <td><?php echo esc_html($item->id_number); ?></td>
                <td><?php echo ucfirst(esc_html($item->description)); ?></td> 
                <td><?php echo esc_html($item->size); ?></td>
                <td><?php echo ucfirst(esc_html($item->status)); ?></td> 
                <td><img src="<?php echo esc_url($item->image_url); ?>" alt="" style="width: 100px; height: auto;"></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
                <tr><td colspan="4">No items found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>