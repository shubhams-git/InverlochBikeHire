<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Load necessary models
include_once plugin_dir_path(__DIR__) . '../includes/models/CategoryModel.php';
include_once plugin_dir_path(__DIR__) . '../includes/models/ItemModel.php';

$categoryModel = new CategoryModel();
$itemModel = new ItemModel();

$message = '';

// Handle form submission for adding a new item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_item') {
    check_admin_referer('add-item-nonce', '_wpnonce_add_item');

    $data = [
        'id_number' => $_POST['id_number'],
        'category_id' => $_POST['category_id'],
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'size' => $_POST['size'],
        'status' => $_POST['status'],
    ];

    $insertResult = $itemModel->insert($data);
    if (is_wp_error($insertResult)) {
        $message = '<div class="notice notice-error"><p>Error: ' . $insertResult->get_error_message() . '</p></div>';
    } else {
        $message = '<div class="notice notice-success is-dismissible"><p>Item added successfully!</p></div>';
    }
}

// Fetch all categories and items for listing
$categories = $categoryModel->get_all_categories();
$items = $itemModel->get_all_items();
?>

<div class="wrap">
    <h1>Add New Item</h1>
    <?php echo $message; ?>
    <form method="post" action="">
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
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo esc_html($item->name); ?></td>
                    <td><?php echo esc_html($item->id_number); ?></td>
                    <td><?php echo esc_html($item->description); ?></td>
                    <td><?php echo esc_html($item->size); ?></td>
                    <td><?php echo esc_html($item->status); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
                <tr><td colspan="4">No items found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
