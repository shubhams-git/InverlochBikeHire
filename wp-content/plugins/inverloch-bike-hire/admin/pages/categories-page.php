<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

include_once plugin_dir_path(__DIR__) . '../includes/models/CategoryModel.php';

$categoryModel = new CategoryModel();
$categories = $categoryModel->get_all_categories();

// Determine if we're editing a category
$edit_category_id = isset($_GET['edit']) ? intval($_GET['edit']) : null;
$category_to_edit = $edit_category_id ? $categoryModel->get_category_by_id($edit_category_id) : null;
?>

<div class="wrap">
    <h1><?php echo $edit_category_id ? 'Edit Category' : 'Add New Category'; ?></h1>
    <form method="post" id="categories" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="handle_category_form">
        <input type="hidden" name="entity" value="category">
        <input type="hidden" name="action_type" value="<?php echo $edit_category_id ? 'edit' : 'add'; ?>">
        <?php if ($edit_category_id): ?>
            <input type="hidden" name="category_id" value="<?php echo esc_attr($edit_category_id); ?>">
        <?php endif; ?>
        <div id="messageContainer"></div>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="category_name">Category Name</label></th>
                <td><input type="text" id="category_name" name="category_name" value="<?php echo esc_attr($edit_category_id ? $category_to_edit->category_name : ''); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <td>
                    <?php submit_button($edit_category_id ? 'Update Category' : 'Add Category', 'primary', 'submit_category', false); ?>
                    <?php if ($edit_category_id): ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=ibh_categories')); ?>" class="button button-secondary">Back</a>
                    <?php endif; ?>                
                </td>
            </tr>
        </table>
    </form>
    <hr>
    <?php if (!$edit_category_id): ?>
    <h2>Existing Categories</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo esc_html($category->category_name); ?></td>
                    <td>
                        <a href="?page=ibh_categories&edit=<?php echo $category->category_id; ?>" class="button button-primary">Edit</a>
                        <a href="#" class="button button-secondary delete-category" data-category-id="<?php echo $category->category_id; ?>">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
                <tr><td colspan="6">No categories found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
