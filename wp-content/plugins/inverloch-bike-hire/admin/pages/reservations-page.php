<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

include_once plugin_dir_path(__DIR__) . '../includes/models/ReservationModel.php';
include_once plugin_dir_path(__DIR__) . '../includes/models/CustomerModel.php';

$reservation_model = new ReservationModel();
$customer_model = new CustomerModel();

$reservation_data = $reservation_model->get_all_reservations();
$customer_data = $customer_model->get_all_customers();

?>

<div class="wrap">
    <h2>Add New Reservation</h2>
    <form method="post" action="">
        <?php wp_nonce_field('add_new_reservation', 'add_new_reservation_nonce'); ?>
        <input type="hidden" name="action" value="add_item">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="reservation_fromdate">From Date</label></th>
                <td><input type="datetime-local" id="reservation_fromdate" name="reservation_fromdate" required class="large-text" onchange="checkAvailability()"></td>
                <th scope="row"><label for="reservation_todate">To Date</label></th>
                <td><input type="datetime-local" id="reservation_todate" name="reservation_todate" required class="large-text" onchange="checkAvailability()"></td>
            </tr>
            <tr>
                <th scope="row"><label for="reservation_bike">Bikes ID</label></th>
                <td colspan="3"><input type="search" id="reservation_bike" name="reservation_bike" required class="large-text" oninput="search()" autocomplete="off"></td>
            </tr>
            <tr>
                <th scope="row"><label for="reservation_customer">Customer</label></th>
                <td colspan="3"><select name="reservation_customer" id="reservation_customer" required>
                    <?php foreach ($customer_data as $customer): ?>
                        <option value="<?php echo esc_attr($customer->fname); ?>"><?php echo esc_html($customer->fname) . " " . esc_html($customer->lname) . " " . esc_html($customer->mobile_phone);?></option>
                    <?php endforeach; ?>
                </select></td>
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
            <tr>
                <td>        
                    <input type="submit" name="submit_reservation" class="button button-primary" value="Add Reservation">
                </td>
            </tr>
        </table>
    </form>
</div>

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
                    <td><?php echo $reservation->created_date; ?></td>
                    <td><?php echo $customer_model->get_customer_by_id($reservation->customer_id)->fname; ?></td>
                    <td><?php echo $reservation->from_date; ?></td>
                    <td><?php echo $reservation->to_date; ?></td>
                    <td>null</td>
                    <td><?php echo $reservation->delivery_notes; ?></td>
                    <td><?php echo $reservation->reservation_stage; ?></td>
                    <td>
                        <button class="edit-btn">Edit</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
