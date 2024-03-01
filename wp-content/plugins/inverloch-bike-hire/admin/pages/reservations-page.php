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
$detailed_reservations = $reservation_model->get_detailed_reservations();
$customer_data = $customer_model->get_all_customers();
$blockeddate_date = $blockeddate_model->get_all_blocked_date();

?>

<div class="wrap">
    <h1 class="wp-heading-inline">Manage Reservations</h1>
    <button id="show-add-reservation-form" class="page-title-action">+ Add New Reservation</button>
    <hr class="wp-header-end">

    <div id="reservation-form-container" class="postbox" style="display:none; margin-top:20px;">
        <div class="inside">
            <form id="fetch_reservations" class="form-table">
                <h2>Add New Reservation</h2>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="reservation_fromdate">From Date</label></th>
                            <td><input type="text" id="reservation_fromdate" name="reservation_fromdate" class="regular-text" placeholder="YYYY-MM-DD" autocomplete="off" required></td>
                            <th scope="row"><label for="reservation_fromtime">From Time</label></th>
                            <td><input type="text" id="reservation_fromtime" name="reservation_fromtime" class="regular-text" placeholder="HH:MM" autocomplete="off" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="reservation_todate">To Date</label></th>
                            <td><input type="text" id="reservation_todate" name="reservation_todate" class="regular-text" placeholder="YYYY-MM-DD" autocomplete="off" required></td>
                            <th scope="row"><label for="reservation_totime">To Time</label></th>
                            <td><input type="text" id="reservation_totime" name="reservation_totime" class="regular-text" placeholder="HH:MM" autocomplete="off" required></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button type="submit" class="button button-primary">Fetch Available Bikes</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <div id="messageContainer"></div>
    <!-- Placeholder for dynamically injected form -->
    <div id="dynamicFormContainer"></div>
    <button id="go-back-to-reservation-list" class="button" style="display: none; margin-top: 20px;">Go Back</button>
        <div id="reservation-list-view" style="margin-top: 20px;" class="widefat fixed striped">
            <h2>Existing Reservations</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Reference ID</th>
                        <th>Customer</th>
                        <th>Reservation Time Slots</th>
                        <th>Items Booked</th>
                        <th>Booking Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($detailed_reservations)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No reservations found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($detailed_reservations as $reservation): 
                            $customer_display = "{$reservation->fname} {$reservation->lname} ({$reservation->mobile_phone})";
                            // Convert and reformat the time and date
                            $formatted_from_time = date("g A", strtotime($reservation->from_time)); // Convert to 12-hour AM/PM format
                            $formatted_to_time = date("g A", strtotime($reservation->to_time)); // Convert to 12-hour AM/PM format
                            $formatted_from_date = date("d/m/Y", strtotime($reservation->from_date)); // Convert to DD/MM/YYYY format
                            $formatted_to_date = date("d/m/Y", strtotime($reservation->to_date)); // Convert to DD/MM/YYYY format

                            // Concatenate the formatted time and date strings
                            $time_slots = "From: {$formatted_from_time} ({$formatted_from_date}) <br>To: {$formatted_to_time} ({$formatted_to_date})";                            
                            // Fetch booked items for this reservation
                            $booked_items_ids = $item_booking_model->get_item_ids_by_reservation_ids([$reservation->reservation_id]);
                            $items_booked = [];
                            foreach ($booked_items_ids as $item_id) {
                                $item = $item_model->get_item_by_id($item_id);
                                $items_booked[] = "{$item->id_number} - {$item->name}";
                            }
                            $items_display = implode('<br>', $items_booked);
                            ?>
                            <tr>
                                <td><?= esc_html($reservation->reference_id); ?></td>
                                <td><?= htmlspecialchars($customer_display); ?></td>
                                <td><?= $time_slots; // Directly echo because it contains HTML ?></td>
                                <td><?= $items_display; // Directly echo because it contains HTML ?></td>
                                <td><?= esc_html($reservation->reservation_stage); ?></td>
                                <td>
                                    <a href="#" class="button button-primary action edit-reservation-button" data-reservation-id="<?= esc_attr($reservation->reservation_id); ?>">Edit</a>
                                    <a href="#" class="button button-secondary action delete-reservation-button" data-reservation-id="<?= esc_attr($reservation->reservation_id); ?>">Delete</a>
                                    <a href="#" class="button button-secondary action generate-invoice-button" data-reservation-id="<?= esc_attr($reservation->reservation_id); ?>">Generate Invoice</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
</div>

<script>
    jQuery(document).ready(function($){

        var blockedDates = <?php echo json_encode($blockeddate_date); ?>;

        jQuery("#reservation_fromdate, #reservation_todate").datepicker({
            beforeShowDay: function(date) {
                // Format date to yyyy-mm-dd
                var dateString = jQuery.datepicker.formatDate('yy-mm-dd', date);

                // Check if the date is in the blockedDates array
                var isBlocked = blockedDates.some(function(blockedDate) {
                    return dateString === blockedDate.date && blockedDate.is_blocked === "1";
                });

                // Disable date if it's in the blockedDates array
                return [!isBlocked];
            },
            minDate: 0,
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true
        });

        // Initialize the fromtime_picker and totime_picker
        jQuery('#reservation_fromtime, #reservation_totime').timepicker({
            'minTime': '08:00',
            'maxTime': '19:00',
            'timeFormat': 'HH:mm' // Specify 24-hour format
        });
        
        // Ensure one of the checkbox is clicked
        $('#submit_reservation').click(function() {
            checked = $("input[type=checkbox][name='selected_bikes[]']:checked").length;

            if(!checked) {
                alert("You must check at least one bike.");
                return false;
            }
        });

        $('#show-add-reservation-form').click(function() {
            $('#reservation-form-container').show();
            $('#dynamicFormContainer').show();
            $('#reservation-list-view').hide();
            $('#go-back-to-reservation-list').show();
        });

        $('#go-back-to-reservation-list').click(function() {
            $('#reservation-form-container').hide();
            $('#dynamicFormContainer').empty();
            $('#reservation-list-view').show();            
            $(this).hide();
        });
    });
</script>
