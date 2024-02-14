<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

include_once plugin_dir_path(__DIR__) . '../includes/models/BlockedDateModel.php';

$blocked_date_model = new BlockedDateModel();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_blocked_date') {
    check_admin_referer('add_new_blocked_date', 'add_new_blocked_date_nonce');

    // Get the submitted data
    $date = sanitize_text_field($_POST['blockeddate_date']);
    $is_blocked = true;

    // Insert the data using the BlockedDateModel
    $blocked_date = $blocked_date_model->insert(array(
        'date' => $date,
        'is_blocked' => $is_blocked
    ));

    // If INSERT succeed
    if (!is_wp_error($blocked_date)) {
        // Display a success message
        ?>
        <div class="updated notice">
            <p>Blocked Date added successfully!</p>
        </div>
        <?php
    } else {
        // Display an error message
        ?>
        <div class="error notice">
            <p><?php echo esc_html($blocked_date->get_error_message()); ?></p>
        </div>
        <?php
    }
}

// Handle blocked date deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['blocked_date'])) {
    // Verify nonce for security
    if (wp_verify_nonce($_GET['_wpnonce'], 'delete-blockeddate-action')) {
        $deletion = $blocked_date_model->delete($_GET['blocked_date']);
        if (is_wp_error($deletion)) {
            echo '<div class="error"><p>Error deleting blocked date: ' . $deletion->get_error_message() . '</p></div>';
        } else {
            echo '<div class="updated"><p>Blocked date deleted successfully!</p></div>';
        }
        ?>
        <script>
            // Modify URL to the page only after deleting
            window.history.replaceState({}, document.title, window.location.href.split('&')[0]);
        </script>
        <?php
    }
}

// Retrieve all blocked dates
$blocked_dates = $blocked_date_model->get_all_blocked_date();

// Encode blocked dates to JSON for JavaScript
$blocked_dates_json = json_encode($blocked_dates);
?>

<!-- Include jQuery UI -->
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<div class="wrap">
    <h2>Add New Blocked Dates</h2>
    <form method="post" action="">
        <?php wp_nonce_field('add_new_blocked_date', 'add_new_blocked_date_nonce'); ?>
        <input type="hidden" name="action" value="add_blocked_date">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="blockeddate_date">Date</label></th>
                <td>
                    <input type="text" id="blockeddate_date" name="blockeddate_date" required class="regular-text" placeholder="Select a date" autocomplete="off">
                    <input type="submit" name="submit_blockeddate" class="button button-primary" value="Add blocked date">
                </td>
            </tr>
        </table>
    </form>
</div>

<div class="wrap">
    <h2>Blocked Dates</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($blocked_dates as $blocked_date) : ?>
                <tr>
                    <td><?php echo $blocked_date->date; ?></td>
                    <td>
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=ibh_blocked_dates&action=delete&blocked_date=' . $blocked_date->date), 'delete-blockeddate-action')); ?>" class="delete-blockeddate button button-secondary">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($blocked_dates)): ?>
                <tr><td colspan="2">No blocked date found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    jQuery(document).ready(function($) {
        $('.delete-blockeddate').on('click', function(e) {
            if (!confirm('Are you sure you want to delete this blocked date? This action cannot be undone.')) {
                e.preventDefault();
            }
        });

        var blockedDates = <?php echo $blocked_dates_json; ?>;

        function disableBlockedDates(date) {
            // Convert date to string in yyyy-mm-dd format
            var dateString = $.datepicker.formatDate('yy-mm-dd', date);
            
            // Check if the date is in the blockedDates array
            return [blockedDates.findIndex(function(item) {
                return item.date === dateString && item.is_blocked === '1'; // Check both date and is_blocked status
            }) === -1];
        }

        $("#blockeddate_date").datepicker({
            beforeShowDay: disableBlockedDates, // Call disableBlockedDates function
            minDate: 0, // Prevent selection of dates before today
            onSelect: function(dateText, inst) {
                // Parse the selected date and format it as yyyy-mm-dd
                var selectedDate = $.datepicker.parseDate('mm/dd/yy', dateText);
                var formattedDate = $.datepicker.formatDate('yy-mm-dd', selectedDate);
                
                // Update the value of datepicker to the selected date format
                $(this).val(formattedDate);
            }
        });
    });
</script>