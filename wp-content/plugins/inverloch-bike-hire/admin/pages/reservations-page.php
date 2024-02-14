<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

include_once plugin_dir_path(__DIR__) . '../includes/models/ReservationModel.php';
include_once plugin_dir_path(__DIR__) . '../includes/models/CustomerModel.php';
include_once plugin_dir_path(__DIR__) . '../includes/models/BlockedDateModel.php';
include_once plugin_dir_path(__DIR__) . '../includes/models/ItemBookingModel.php';
include_once plugin_dir_path(__DIR__) . '../includes/models/ItemModel.php';
include_once plugin_dir_path(__DIR__) . '../includes/models/CategoryModel.php';

// Initialize all the models
$reservation_model = new ReservationModel();
$customer_model = new CustomerModel();
$blockeddate_model = new BlockedDateModel();
$item_booking_model = new ItemBookingModel();
$item_model = new ItemModel();
$category_model = new CategoryModel();

// to show different section
$show_section = 1;
$available_bikes = [];    // store all the available bikes during the date time range
$reservation_fromdate = $reservation_todate = $reservation_fromtime = $reservation_totime = '';

// button clicked after confirming the date and time range
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_reservation_date') {

    // Get the submitted data
    $reservation_fromdate = sanitize_text_field($_POST['reservation_fromdate']);
    $reservation_todate = sanitize_text_field($_POST['reservation_todate']);
    $reservation_fromtime = sanitize_text_field($_POST['reservation_fromtime']);
    $reservation_totime = sanitize_text_field($_POST['reservation_totime']);

    // get all the booked reservation ids
    $booked_reservation_ids = $reservation_model->get_reservation_ids_by_date_time_range($reservation_fromdate, $reservation_todate, $reservation_fromtime, $reservation_totime);
    // get all the booked bike ids
    $booked_bike_ids = $item_booking_model->get_item_ids_by_reservation_ids($booked_reservation_ids);
    // get all the available bikes 
    $available_bikes = $item_model->get_items_except_specified_ids($booked_bike_ids);

    $show_section = 2; // show bike bookings and additional info
}

// button clicked after filling up all the fields in the form to add new reservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_reservation') {

    // Get the submitted data
    $reservation_fromdate = sanitize_text_field($_POST['reservation_fromdate']);
    $reservation_todate = sanitize_text_field($_POST['reservation_todate']);
    $reservation_fromtime = sanitize_text_field($_POST['reservation_fromtime']);
    $reservation_totime = sanitize_text_field($_POST['reservation_totime']);
    $reservation_customer = sanitize_text_field($_POST['reservation_customer']);
    $reservation_notes = sanitize_text_field($_POST['reservation_notes']);
    $reservation_stage = sanitize_text_field($_POST['reservation_stage']);
    $created_date = current_time('Y-m-d H:i:s');
    $selected_bike_ids = $_POST['selected_bikes']; 

    // add a new reservation data
    $reservation_id = $reservation_model->insert(array(
        'customer_id' => $reservation_customer,
        'from_date' => $reservation_fromdate,
        'to_date' => $reservation_todate,
        'from_time' => $reservation_fromtime,
        'to_time' => $reservation_totime,
        'reservation_stage' => $reservation_stage,
        'created_date' => $created_date,
        'delivery_notes' => $reservation_notes
    ));

    // If INSERT succeed
    if (!is_wp_error($reservation_id)) {
        // INSERT all the bike bookings into the itembooking table
        foreach ($selected_bike_ids as $selected_bike_id) {
            $item_booking_model->insert(array(
                'reservation_id' => $reservation_id,
                'item_id' => $selected_bike_id
            ));
        }
        $reservation_fromdate = $reservation_todate = $reservation_fromtime = $reservation_totime = '';
        // Display a success message
        ?>
        <div class="updated notice">
            <p>Reservation added successfully!</p>
        </div>
        <?php
    } else {
        // Display an error message
        ?>
        <div class="error notice">
            <p><?php echo esc_html($reservation_id->get_error_message()); ?></p>
        </div>
        <?php
    }
}

$reservation_data = $reservation_model->get_all_reservations();
$customer_data = $customer_model->get_all_customers();
$blockeddate_date = $blockeddate_model->get_all_blocked_date();
$encountered_categories = array();  // store categories

?>

<!-- Include jQuery UI Datepicker -->
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<!-- Include jQuery Timepicker -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

<div class="wrap">
    <h2>Add New Reservation</h2>
    <div class="postbox">
        <div class="inside">
            <form method="post" action="">
                <h3 style="text-align: center;">Reservation Dates</h3>
                <input type="hidden" name="action" value="add_reservation_date">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="reservation_fromdate">From Date</label></th>
                        <td colspan="2">
                            <input type="text" id="reservation_fromdate" name="reservation_fromdate" required class="medium-text" placeholder="2024-02-01" autocomplete="off" value="<?php echo $reservation_fromdate; ?>">
                        </td>
                        <th scope="row"><label for="reservation_fromtime">From Time</label></th>
                        <td colspan="2">
                            <input type="text" id="reservation_fromtime" name="reservation_fromtime" required class="medium-text" placeholder="09:00" autocomplete="off" value="<?php echo $reservation_fromtime; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="reservation_todate">To Date</label></th>
                        <td colspan="2">
                            <input type="text" id="reservation_todate" name="reservation_todate" required class="medium-text" placeholder="2024-02-05" autocomplete="off" value="<?php echo $reservation_todate; ?>">
                        </td>
                        <th scope="row"><label for="reservation_totime">To Time</label></th>
                        <td colspan="2">
                            <input type="text" id="reservation_totime" name="reservation_totime" required class="medium-text" placeholder="18:00" autocomplete="off" value="<?php echo $reservation_totime; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>        
                            <input type="submit" name="update_booking_dates" class="button button-primary" value="Update booking dates">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <?php if ($show_section == 2): ?>
        <form method="post" action="">
            <div class="postbox">
                <div class="inside">
                        <h3 style="text-align: center;">Bike Bookings</h3>
                        <input type="hidden" name="reservation_fromdate" value="<?php echo $reservation_fromdate; ?>"> 
                        <input type="hidden" name="reservation_fromtime" value="<?php echo $reservation_fromtime; ?>"> 
                        <input type="hidden" name="reservation_todate" value="<?php echo $reservation_todate; ?>"> 
                        <input type="hidden" name="reservation_totime" value="<?php echo $reservation_totime; ?>"> 
                        <input type="hidden" name="action" value="add_reservation"> 
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><b>Image</b></th>
                                    <th><b>ID Number</b></th>
                                    <th><b>Name</b></th>
                                    <th><b>Size</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($available_bikes as $bike): 
                                    // LOGIC: the available_bikes has been sorted by the category and we will create new category row in the table if not found.
                                    // Check if the category of the bike has been encountered before
                                    if (!in_array($bike->category_id, $encountered_categories)) {
                                        $category = $category_model->get_category_by_id($bike->category_id);
                                        ?>
                                        <tbody class="labels">
                                            <tr>
                                                <td colspan="5">
                                                    <label for="<?php echo esc_attr($category->category_name); ?>"><?php echo esc_attr($category->category_name); ?></label>
                                                    <input type="checkbox" name="<?php echo esc_attr($category->category_name); ?>" id="<?php echo esc_attr($category->category_name); ?>" data-toggle="toggle">
                                                </td>
                                            </tr>
                                        </tbody>
                                    <?php
                                    // Add the category to the encountered categories array
                                    $encountered_categories[] = $bike->category_id;
                                    }
                                    ?>
                                        <tbody class="hide">
                                            <tr>
                                                <td><input type="checkbox" name="selected_bikes[]" value="<?php echo esc_attr($bike->item_id); ?>"></td>
                                                <td><img width="60" height="60" src="<?php echo esc_attr($bike->image_url); ?>" alt="Bike Image"></td>
                                                <td><?php echo esc_attr($bike->id_number); ?></td>
                                                <td><?php echo esc_attr($bike->name); ?></td>
                                                <td><?php echo esc_attr($bike->size); ?></td>
                                            </tr>
                                        </tbody>
                                <?php endforeach;
                                 if (empty($available_bikes)): ?>
                                    <tr><td colspan="6">No Bikes available during the selected dates and times.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                </div>
            </div>
            <div class="postbox">
                <div class="inside">
                    <h3 style="text-align: center;">Additional information</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="reservation_customer">Customer</label></th>
                            <td colspan="3">
                                <select name="reservation_customer" id="reservation_customer" required placeholder="Please select a customer">
                                    <option value="" disabled selected>Please select a customer</option>
                                    <?php if (empty($customer_data)): ?>
                                        <option value="" disabled>No customers found.</option>
                                    <?php else: ?>
                                        <?php foreach ($customer_data as $customer): ?>
                                            <option value="<?php echo esc_attr($customer->customer_id); ?>"><?php echo esc_html($customer->fname) . " " . esc_html($customer->lname) . " (" . esc_html($customer->mobile_phone) .")";?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="reservation_notes">Notes</label></th>
                            <td colspan="3"><textarea id="reservation_notes" name="reservation_notes" rows="4" cols="50" class="large-text"></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="reservation_stage">Stage</label></th>
                            <td colspan="3">
                                <select name="reservation_stage" id="reservation_stage">
                                    <option value="provisional">Provisional</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="checked-in">Checked-in</option>
                                    <option value="checked-out">Checked-out</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <input type="submit" name="submit_reservation" id="submit_reservation" class="button button-primary" value="Add Reservation">
        </form>
    <?php endif ?>
</div>

<?php if ($show_section == 1): ?>
<div class="wrap">
    <h2>Reservation Data</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Created Date</th>
                <th>Customer</th>
                <th>From</th>
                <th>To</th>
                <th>Bikes</th>
                <th>Notes</th>
                <th>Stage</th>
                <th>Action</th> <!-- New column for Edit/Save button -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservation_data as $reservation) : ?>
                <tr>
                    <td><?php echo esc_attr($reservation->created_date); ?></td>
                    <td><?php echo esc_attr($customer_model->get_customer_by_id($reservation->customer_id)->fname); ?></td>
                    <td><?php echo esc_attr($reservation->from_date . ' ' . $reservation->from_time); ?></td>
                    <td><?php echo esc_attr($reservation->to_date. ' ' . $reservation->to_time); ?></td>
                    <td><?php echo esc_attr($item_booking_model->get_bike_counts_by_reservation_id($reservation->reservation_id)); ?></td>
                    <td><?php echo esc_attr($reservation->delivery_notes); ?></td>
                    <td><?php echo esc_attr($reservation->reservation_stage); ?></td>
                    <td>
                        <a href="" class="delete-blockeddate button button-primary">Edit</a>
                        <a href="" class="delete-blockeddate button button-secondary">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($reservation_data)): ?>
                <tr><td colspan="8">No reservations found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php endif ?>

<script>
    jQuery(document).ready(function($){

        var blockedDates = <?php echo json_encode($blockeddate_date); ?>;

        function disableBlockedDates(date) {
            // Convert date to string in yyyy-mm-dd format
            var dateString = $.datepicker.formatDate('yy-mm-dd', date);
            
            // Check if the date is in the blockedDates array
            return [blockedDates.findIndex(function(reservation) {
                return reservation.date === dateString && reservation.is_blocked === '1'; // Check both date and is_blocked status
            }) === -1];
        }
        
        // Initialize the fromdate_picker
        $("#reservation_fromdate").datepicker({
            beforeShowDay: disableBlockedDates,
            minDate: 0,
            dateFormat: "yy-mm-dd",
            onSelect: function(selectedDate) {
                // Update the minDate of the todate_picker to be after the selected date
                $("#reservation_todate").datepicker("option", "minDate", selectedDate);
            }
        });

        // Initialize the todate_picker
        $("#reservation_todate").datepicker({
            beforeShowDay: disableBlockedDates,
            minDate: 0,
            dateFormat: "yy-mm-dd"
        });

        // Initialize the fromtime_picker and totime_picker
        $('#reservation_fromtime, #reservation_totime').timepicker({
            'minTime': '08:00',
            'maxTime': '19:00',
            'timeFormat': 'HH:mm' // Specify 24-hour format
        });

        // Hide/show the bikes when the category is clicked
        $('[data-toggle="toggle"]').change(function() {
            var $labelsRow = $(this).closest('tbody').nextUntil('tbody.labels');
            $labelsRow.filter('.hide').toggle();
        });

        // Ensure one of the checkbox is clicked
        $('#submit_reservation').click(function() {
            checked = $("input[type=checkbox][name='selected_bikes[]']:checked").length;

            if(!checked) {
                alert("You must check at least one bike.");
                return false;
            }
        });
    });
</script>
