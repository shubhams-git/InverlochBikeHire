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
$pricePoints = $pricePointModel->get_all_price_points();

function map_price_points_by_category($pricePoints) {
    $mapped = [];
    foreach ($pricePoints as $point) {
        $mapped[$point->category_id][] = $point;
    }
    return $mapped;
}

$pricePointsByCategory = map_price_points_by_category($pricePoints);
?>

<div class="wrap">
    <h1>Manage Price Points</h1>
    <div id="messageContainer"></div>
    <form id="price-points-form" method="post" action="">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Category</th>
                    <?php
                    // Collect unique timeframes
                    $uniqueTimeframes = [];
                    foreach ($pricePoints as $point) {
                        $uniqueTimeframes[$point->timeframe] = true; // Use keys to enforce uniqueness
                    }

                    // Sort timeframes naturally
                    uksort($uniqueTimeframes, 'strnatcmp');

                    // Generate table headings
                    foreach (array_keys($uniqueTimeframes) as $timeframe) {
                        echo '<th>' . esc_html($timeframe) . '</th>';
                    }
                    ?>
                    <th><button type="button" id="add-timeframe" class="button button-primary">+ Add Timeframe</button></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr data-category-id="<?php echo esc_attr($category->category_id); ?>">
                        <td><?php echo esc_html($category->category_name); ?></td>
                        <?php
                        if (isset($pricePointsByCategory[$category->category_id])) {
                            foreach ($pricePointsByCategory[$category->category_id] as $point) {
                                echo '<td><input type="number" name="price[' . esc_attr($category->category_id) . '][' . esc_attr($point->timeframe) . ']" value="' . esc_attr($point->amount) . '"></td>';
                            }
                        }
                        ?>
                        <td></td> <!-- Placeholder for dynamic timeframe input -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <input type="submit" value="Save Changes" class="button button-primary">
    </form>
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
                $('<th>' + newTimeframe + '</th>').insertBefore($(this));
                $('table tbody tr').each(function() {
                    var categoryId = $(this).data('category-id');
                    $('<td><input type="number" step="0.01" name="price[' + categoryId + '][' + newTimeframe + ']" value=""></td>').insertBefore($(this).find('td').eq(index));
                });
                added = true;
                return false;
            }
        });

        if (!added) { // If the timeframe is the latest, add it to the end
            $('<th>' + newTimeframe + '</th>').insertBefore('table thead tr th:last');
            $('table tbody tr').each(function() {
                var categoryId = $(this).data('category-id');
                $('<td><input type="number" step="0.01" name="price[' + categoryId + '][' + newTimeframe + ']" value=""></td>').insertBefore($(this).find('td:last'));
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

