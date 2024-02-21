<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

include_once plugin_dir_path(__DIR__) . '../includes/models/PricePointModel.php';
include_once plugin_dir_path(__DIR__) . '../includes/models/CategoryModel.php';

$pricePointModel = new PricePointModel();
$categoryModel = new CategoryModel();

$categories = $categoryModel->get_all_categories();
$sorted_categories = $categoryModel->get_all_categories();
$sorted_timeframes = $pricePointModel->get_sorted_timeframes();
$edit = isset($_GET['edit']) ? $_GET['edit'] : false;

?>

<div class="wrap">
    <h1>Manage Price Points</h1>
    <div id="messageContainer"></div>
    <form id="price-points-form" method="post">
        <table class="wp-list-table widefat fixed">
            <thead>
                <tr>
                    <th><b>Price Group</b></th>
                    <?php 
                        foreach($sorted_timeframes as $timeframe) {
                            if ($edit) {
                                echo "<th><b>" . $timeframe . "</b><a data-timeframe-id=' . esc_attr($timeframe) . '><i class='delete-timeframe dashicons dashicons-no'></i></a></th>";
                            } else {
                                echo "<th><b>" . $timeframe . "</b></th>";
                            }
                        }
                    ?>
                    <?php if(!$sorted_timeframes && !$edit): ?>
                        <th>No timeframes found.</th>
                    <?php endif; ?>
                    <?php if ($edit): ?>
                    <th><button type="button" id="add-timeframe" class="button button-primary">+ Add Timeframe</button></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <tr>
                <?php 
                    foreach($sorted_categories as $category) 
                    {
                        echo "<tr data-category-id=" . esc_attr($category->category_id) . ">";
                        echo "<td><b>" . $categoryModel->get_category_by_id($category->category_id)->category_name . "</b></td>";
                        foreach($sorted_timeframes as $timeframe) 
                        {
                            $result = $pricePointModel->get_amount_by_timeframe_and_category($timeframe, $category->category_id);
                            // Check if the result is not null
                            if ($edit) {
                                $input_value = $result !== null ? ' value="' . esc_attr($result->amount) . '"' : '';
                                echo '<td><input type="number" name="price[' . esc_attr($category->category_id) . '][' . esc_attr($timeframe) . ']" class="price-point-number"' . $input_value . '></td>';
                            } else {
                                echo "<td>" . ($result !== null ? $result->amount : "") . "</td>";
                            }
                        }
                        echo "</tr>";
                    }
                    if(!$sorted_categories) {
                        echo '<th>No categories found.</th>';
                    }
                ?>
            </tbody>
        </table>
    <br>
    <?php if($edit): ?>
        <input type="submit" value="Save Changes" class="button button-primary">
        <a href="<?php echo esc_url(admin_url('admin.php?page=ibh_price_points')); ?>" class="button button-secondary">Back</a>
    <?php elseif ($sorted_categories): ?>
        <a href="?page=ibh_price_points&edit=true" class="button button-primary">Edit table</a>
    <?php endif; ?>
</div>

<!-- Modal for adding new timeframe -->
<div id="add-timeframe-modal" style="display:none;">
    <p>Add a new timeframe:</p>
    <form>
        <select id="new-timeframe-unit" name="new-timeframe-unit" class="widefat">
            <option value="hours">Hours</option>
            <option value="days">Days</option>
        </select>
        <input type="number" id="new-timeframe-value" name="new-timeframe-value" class="widefat" min="1" max="31" placeholder="Value">
    </form>
</div>


<script type="text/javascript">
jQuery(document).ready(function($) {

    $('.delete-timeframe').on('click', function() {

        // Get the index of the column to delete
        var columnIndex = $(this).closest('th').index();

        // Remove the corresponding th element from the table header
        $('table thead tr').find('th').eq(columnIndex).remove();

        // Remove the corresponding td element from each row in the table body
        $('table tbody tr').each(function() {
            $(this).find('td').eq(columnIndex).remove();
        });
    });

    $('#add-timeframe').on('click', function() {
        $('#add-timeframe-modal').dialog({
            title: 'Add New Timeframe',
            modal: true,
            buttons: {
                "Add": function() {
                    var unit = $('#new-timeframe-unit').val();
                    var value = $('#new-timeframe-value').val();
                    var newTimeframe = value + ' ' + unit; // e.g., "2 hours", "1 day"

                    if (value && !isTimeframePresent(newTimeframe)) {
                        insertTimeframeInOrder(newTimeframe);
                        $(this).dialog("close");
                    } else {
                        alert("Please enter a valid, unique timeframe.");
                    }
                },
                Cancel: function() {
                    $(this).dialog("close");
                }
            }
        });
    });

    function isTimeframePresent(timeframe) {
        var isPresent = false;
        $('table thead th').each(function() {
            if ($(this).text() === timeframe) {
                isPresent = true;
            }
        });
        return isPresent;
    }

    function insertTimeframeInOrder(newTimeframe) {
        var added = false;
        var newTimeframeValue = convertTimeframeToValue(newTimeframe);

        $('table thead th').each(function(index) {
            if (index === 0) return true; // Skip category column
            var current = convertTimeframeToValue($(this).text());
            if (newTimeframeValue < current && !added) {
                $('<th><b>' + newTimeframe + '</b><a><i class="delete-timeframe dashicons dashicons-no"></i></a></th>').insertBefore($(this));
                $('table tbody tr').each(function() {
                    var categoryId = $(this).data('category-id');
                    $('<td><input type="number" step="0.01" class="price-point-number" name="price[' + categoryId + '][' + newTimeframe + ']" value=""></td>').insertBefore($(this).find('td').eq(index));
                });
                added = true;
                return false;
            }
        });

        if (!added) { // If the timeframe is the latest, add it to the end
            $('<th><b>' + newTimeframe + '</b><a><i class="delete-timeframe dashicons dashicons-no"></i></a></th>').insertBefore('table thead tr th:last');
            $('table tbody tr').each(function() {
                var categoryId = $(this).data('category-id');
                $('<td><input type="number" step="0.01" class="price-point-number" name="price[' + categoryId + '][' + newTimeframe + ']" value=""></td>').insertAfter($(this).find('td:last'));
            });
        }
    }

    function convertTimeframeToValue(timeframe) {
        var parts = timeframe.split(' ');
        var value = parseInt(parts[0]);
        var unit = parts[1];
        return unit.startsWith('hour') ? value : value * 24; // Assuming 'day' timeframes are converted to equivalent hours for sorting
    }
});

</script>

