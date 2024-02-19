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

// Initialize models
$reservation_model = new ReservationModel();
$customer_model = new CustomerModel();
$blockeddate_model = new BlockedDateModel();
$item_booking_model = new ItemBookingModel();
$item_model = new ItemModel();
$category_model = new CategoryModel();

// Load necessary data
$reservation_data = $reservation_model->get_all_reservations();
$customer_data = $customer_model->get_all_customers();
$blockeddate_date = $blockeddate_model->get_all_blocked_date();


?>
<div class="wrap">
    <h2>Add New Reservation</h2>
    <div id="messageContainer"></div>
    <!-- Initial Form for Fetching Available Bikes Based on Date/Time Range -->
    <div class="postbox">
        <div class="inside">
            <form id="fetch_reservations">
                <h3 style="text-align: center;">Reservation Dates</h3>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="reservation_fromdate">From Date</label></th>
                        <td colspan="2">
                            <input type="text" id="reservation_fromdate" name="reservation_fromdate" required class="medium-text" placeholder="YYYY-MM-DD" autocomplete="off">
                        </td>
                        <th scope="row"><label for="reservation_fromtime">From Time</label></th>
                        <td colspan="2">
                            <input type="text" id="reservation_fromtime" name="reservation_fromtime" required class="medium-text" placeholder="HH:MM" autocomplete="off">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="reservation_todate">To Date</label></th>
                        <td colspan="2">
                            <input type="text" id="reservation_todate" name="reservation_todate" required class="medium-text" placeholder="YYYY-MM-DD" autocomplete="off">
                        </td>
                        <th scope="row"><label for="reservation_totime">To Time</label></th>
                        <td colspan="2">
                            <input type="text" id="reservation_totime" name="reservation_totime" required class="medium-text" placeholder="HH:MM" autocomplete="off">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <button type="submit" class="button button-primary">Fetch Available Bikes</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

    <!-- Container for Dynamically Injected Bike Booking Form -->
    <div id="dynamicFormContainer"></div>
</div>

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
