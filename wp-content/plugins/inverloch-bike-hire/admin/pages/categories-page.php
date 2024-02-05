<?php

include_once plugin_dir_path(__DIR__) . '../includes/models/CategoryModel.php';
$categoryModel = new CategoryModel();

// Handle form submission for adding a new category
if (isset($_POST['submit_category']) && !empty($_POST['category_name'])) {
    // Verify nonce for security
    if (check_admin_referer('add-category-action', 'add-category-nonce')) {
        $insertion = $categoryModel->insert($_POST['category_name']);
        if (is_wp_error($insertion)) {
            echo '<div class="error"><p>Error adding category: ' . $insertion->get_error_message() . '</p></div>';
        } else {
            echo '<div class="updated"><p>Category added successfully!</p></div>';
        }
    }
}

// Handle category deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['category_id'])) {
    // Verify nonce for security
    if (wp_verify_nonce($_GET['_wpnonce'], 'delete-category-action')) {
        $deletion = $categoryModel->delete(intval($_GET['category_id']));
        if (is_wp_error($deletion)) {
            echo '<div class="error"><p>Error deleting category: ' . $deletion->get_error_message() . '</p></div>';
        } else {
            echo '<div class="updated"><p>Category deleted successfully!</p></div>';
        }
    }
}


$categories = $categoryModel->get_all_categories();
?>

<div class="wrap">
    <h1>Manage Categories</h1>
    <div id="ibh-category-form" class="metabox-holder">
        <div class="postbox">
            <div class="inside">
                <form method="post" action="">
                    <?php wp_nonce_field('add-category-action', 'add-category-nonce'); ?>
                    <label for="category_name">Category Name:</label>
                    <input type="text" id="category_name" name="category_name" class="regular-text" placeholder="Enter Category Name" required>
                    <input type="submit" name="submit_category" class="button button-primary" value="Add Category">
                </form>
            </div>
        </div>
    </div>
    <hr>
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
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=ibh_categories&action=delete&category_id=' . $category->category_id), 'delete-category-action')); ?>" class="delete-category button button-secondary">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
                <tr><td colspan="2">No categories found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    jQuery(document).ready(function($) {
        $('.delete-category').on('click', function(e) {
            if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
</script>
