<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

include_once plugin_dir_path(__DIR__) . '../includes/models/CategoryModel.php';
include_once plugin_dir_path(__DIR__) . '../includes/models/ItemModel.php';
include_once plugin_dir_path(__DIR__) . '../includes/models/ItemBookingModel.php';
include_once plugin_dir_path(__DIR__) . '../includes/models/ReservationModel.php';

$categoryModel = new CategoryModel();
$itemModel = new ItemModel();
$message = '';

// Fetching items for display
$categories = $categoryModel->get_all_categories();
$items = $itemModel->get_all_items_with_category_name();

$reservations = [];
// Determine if we're checking the item's schedule
$check_schedule_item_id = isset($_GET['check']) ? intval($_GET['check']) : null;
if ($check_schedule_item_id) {
    $reservations = get_item_schedule($check_schedule_item_id);
}

// Determine if we're editing an item
$edit_item_id = isset($_GET['edit']) ? intval($_GET['edit']) : null;
$item_to_edit = $edit_item_id ? $itemModel->get_item_by_id($edit_item_id) : null;
if ($edit_item_id) {
    $reservations = get_item_schedule($edit_item_id);
}

function get_item_schedule($item_id) {
    $itemBookingModel = new ItemBookingModel();
    $reservationModel = new ReservationModel();
    $reservation_ids = $itemBookingModel->get_reservation_ids_by_item_id($item_id);
    if ($reservation_ids) {
        return $reservationModel->get_reservations_by_reservation_ids($reservation_ids);
    }
}

?>

<div class="wrap">
    <h1><?php 
        if ($check_schedule_item_id) {
            echo 'Check Item Schedule';
        } elseif ($edit_item_id) {
            echo 'Edit Item';
        } else {
            echo 'Add New Item';
        }
    ?></h1>
    <?php if(!$check_schedule_item_id): ?>
        <form method="post" id="item" enctype="multipart/form-data">
            <input type="hidden" name="entity" value="item">
            <input type="hidden" id="action_type" name="action_type" value="<?php echo $edit_item_id ? 'edit' : 'add'; ?>">
            <?php if ($edit_item_id): ?>
                <input type="hidden" name="item_id" value="<?php echo esc_attr($edit_item_id); ?>">
            <?php endif; ?>
            <?php if (count($reservations) > 0): ?>
                <input type="hidden" id="check_reservations" name="check_reservations" value="<?php echo count($reservations); ?>">
            <?php endif; ?>
            <div id="messageContainer"></div>
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
    <?php endif; ?>

    <?php if (!$edit_item_id && !$check_schedule_item_id): ?>
    <h2>Inventory List</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Size</th>
                <th>Status</th>
                <th>Image</th>
                <th>Check Schedule</th>
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
                    <a href="?page=ibh_inventory&check=<?php echo $item->item_id; ?>" class="button button-primary">Check</a>
                </td>
                <td>
                    <a href="?page=ibh_inventory&edit=<?php echo $item->item_id; ?>" class="button button-primary">Edit</a>
                    <a href="?page=ibh_inventory" class="button button-secondary delete-item" data-item-id="<?php echo $item->item_id; ?>">Delete</a>
                </td>

            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
                <tr><td colspan="6">No items found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php elseif($edit_item_id || $check_schedule_item_id): ?>
        <?php if ($edit_item_id): ?>
        <h2>Check Reservations</h2>
        <?php endif; ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><b>From Date</b></th>
                    <th><b>To Date</b></th>
                    <th><b>From Time</b></th>
                    <th><b>To Time</b></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td><?php echo esc_html($reservation->from_date); ?></td>
                    <td><?php echo esc_html($reservation->to_date); ?></td>
                    <td><?php echo esc_html($reservation->from_time); ?></td>
                    <td><?php echo esc_html($reservation->to_time); ?></td> 
                </tr>
                <?php endforeach; ?>
                <?php if (empty($reservation)): ?>
                    <tr><td colspan="4">No reservations found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <?php if ($check_schedule_item_id): ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=ibh_inventory')); ?>" class="button button-secondary">Back</a>
        <?php endif; ?>   
    <?php endif; ?>
</div>