<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

include_once plugin_dir_path(__DIR__) . '../includes/models/BlockedDateModel.php';

$blocked_date_model = new BlockedDateModel();

// Retrieve all blocked dates
$blocked_dates = $blocked_date_model->get_all_blocked_date();

// Encode blocked dates to JSON for JavaScript
$blocked_dates_json = json_encode($blocked_dates);
?>


<div id="messageContainer"></div>
<div class="wrap">
    <h2>Add New Blocked Dates</h2>
    <form id= "blocked-date">
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
                        <button type="button" class="delete-blockeddate button button-secondary" data-date="<?php echo esc_attr($blocked_date->date); ?>">Delete</button>
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
            beforeShowDay: disableBlockedDates, 
            minDate: 0, // Correct: Prevents selection of dates before today
            onSelect: function(dateText, inst) {

                var selectedDate = new Date(dateText);
                // Format the selected Date object as 'yyyy-mm-dd'
                var formattedDate = $.datepicker.formatDate('yy-mm-dd', selectedDate);
                
                // Log the formatted date for debugging
                console.log(`Formatted Date: ${formattedDate}`);
                
                // Update the input's value to the formatted date
                $(this).val(formattedDate);
            }
        });

    });
</script>


