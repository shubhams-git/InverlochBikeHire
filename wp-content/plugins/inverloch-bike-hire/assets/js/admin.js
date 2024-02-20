jQuery(document).ready(function($) {
    console.log("Hi Hi! This is a test!");

    $('form#item').submit(function(e) {
        e.preventDefault();
        console.log("Form submission prevented");
    
        var action_type = $('#action_type').val();
        var status = $('#status').val();
        var check_reservations = $('#check_reservations').val();
        
        // If the action type is edit and status is set to unavailable and more than one reservations for the item
        if (action_type == "edit" && status == "Unavailable" && check_reservations > 0) {
            var confirmUpdate = prompt("Future reservations found for this item. Type 'update' to confirm changing the item to unavailable.");
        } else {
            var confirmUpdate = "update";
        }

        if (confirmUpdate === "update") {
            var formData = new FormData(this);
    
            formData.append('action', 'ibh_handle_form');
            formData.append('_wpnonce', myAjax.nonce);
        
            $.ajax({
                type: 'POST',
                url: myAjax.ajaxurl,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        $('#messageContainer').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                        setTimeout(function() {
                            window.location.href = myAjax.adminUrl + '?page=ibh_inventory'; 
                        }, 200);
                    } else {
                        $('#messageContainer').html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                    }
                    
                },
                error: function(response) {
                    $('#messageContainer').html('<div class="notice notice-error"><p>There was an error processing the request.</p></div>');

  
                }
             });
        } else {
            alert('Update cancelled or incorrect confirmation. No action taken.');
        }
    });

    $('.delete-item').on('click', function(e) {
        e.preventDefault(); 
        
        var itemId = $(this).data('item-id');
        var deleteConfirmation = prompt("Type 'delete' to confirm deletion of this item.");

        if (deleteConfirmation === 'delete') {
            var formData = new FormData();
            formData.append('action', 'ibh_handle_form'); 
            formData.append('item_id', itemId);
            formData.append('action_type', "delete");
            formData.append('entity', 'item');
            formData.append('_wpnonce', myAjax.nonce); 

            $.ajax({
                type: 'POST',
                url: myAjax.ajaxurl,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        $('#messageContainer').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                        setTimeout(function() {
                            window.location.href = myAjax.adminUrl + '?page=ibh_inventory'; 
                        }, 200);
                    } else {
                        $('#messageContainer').html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                    }
                },
                error: function() {
                    $('#messageContainer').html('<div class="notice notice-error"><p>There was an error processing the request.</p></div>');
                }
            });
        } else {
            alert('Deletion cancelled or incorrect confirmation. No action taken.');
        }
    });

    $('form#categories').submit(function(e) {
        e.preventDefault();
        console.log("Category form submission prevented");

        var formData = new FormData(this);
        formData.append('action', 'ibh_handle_form'); 
        formData.append('_wpnonce', myAjax.nonce); 

        $.ajax({
            type: 'POST',
            url: myAjax.ajaxurl,
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    $('#messageContainer').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                    setTimeout(function() {
                        window.location.href = myAjax.adminUrl + '?page=ibh_categories'; 
                    }, 200);
                } else {
                    $('#messageContainer').html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                }
            },
            error: function() {
                $('#messageContainer').html('<div class="notice notice-error"><p>There was an error processing the category request.</p></div>');
            }
        });
    });

    $('.delete-category').on('click', function(e) {
        e.preventDefault();
    
        var categoryId = $(this).data('category-id');
        var deleteConfirmation = prompt("Type 'delete' to confirm deletion of this item.");
    
        if (deleteConfirmation === 'delete') {
            var formData = new FormData();
            formData.append('action', 'ibh_handle_form');
            formData.append('category_id', categoryId);
            formData.append('action_type', "delete");
            formData.append('entity', 'category');
            formData.append('_wpnonce', myAjax.nonce);
    
            $.ajax({
                type: 'POST',
                url: myAjax.ajaxurl,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        $('#messageContainer').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                        setTimeout(function() {
                            window.location.reload(); // Refresh the page to reflect the deletion
                        }, 200);
                    } else {
                        $('#messageContainer').html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                    }
                },
                error: function(response) {
                    $('#messageContainer').html('<div class="notice notice-error"><p>' + response.responseJSON.data.message + '</p></div>');
                    console.log(response);
                }
            });
        } else {
            alert('Deletion cancelled. No action taken.');
        }
    });

    $('#price-points-form').submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'ibh_handle_form');
        formData.append('entity', 'price_point');
        formData.append('action_type', 'update');
        formData.append('_wpnonce', myAjax.nonce);

        $.ajax({
            url: myAjax.ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    // Display success message
                    $('#messageContainer').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                    setTimeout(function() {
                        window.location.href = myAjax.adminUrl + '?page=ibh_price_points'; 
                    }, 200);
                } else {
                    // Display error message
                    $('#messageContainer').html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                }
            },
            error: function(xhr, status, error) {
                // Fallback error message
                var errorMessage = xhr.status + ': ' + xhr.statusText;
                $('#messageContainer').html('<div class="notice notice-error"><p>AJAX error: ' + errorMessage + '</p></div>');
            }
        });
    });

    $('form#email').submit(function(e) {
        e.preventDefault();
        console.log("Form submission prevented");

        var formData = new FormData(this);

        formData.append('action', 'ibh_handle_form');
        formData.append('_wpnonce', myAjax.nonce);
    
        $.ajax({
            type: 'POST',
            url: myAjax.ajaxurl,
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    $('#messageContainer').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                    setTimeout(function() {
                        window.location.href = myAjax.adminUrl + '?page=ibh_emails'; 
                    }, 200);
                } else {
                    $('#messageContainer').html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                }
                     
            },
            error: function(response) {
                $('#messageContainer').html('<div class="notice notice-error"><p>There was an error processing the request.</p></div>');
            }
        });
    });

    $('form#customer').submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        formData.append('action', 'ibh_handle_form');
        formData.append('_wpnonce', myAjax.nonce);
    
        $.ajax({
            type: 'POST',
            url: myAjax.ajaxurl,
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    $('#messageContainer').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                    setTimeout(function() {
                        window.location.href = myAjax.adminUrl + '?page=ibh_customers'; 
                    }, 200);
                } else {
                    $('#messageContainer').html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                }
            },
            error: function(response) {
                console.log(response);
                $('#messageContainer').html('<div class="notice notice-error"><p>There was an error processing the request.</p></div>');
            }
        });
    });

    $('.delete-customer').on('click', function(e) {
        e.preventDefault();
    
        var customerId = $(this).data('customer-id');
        var deleteConfirmation = prompt("Type 'delete' to confirm deletion of this item.");
    
        if (deleteConfirmation === 'delete') {
            var formData = new FormData();
            formData.append('action', 'ibh_handle_form');
            formData.append('customer_id', customerId);
            formData.append('action_type', "delete");
            formData.append('entity', 'customer');
            formData.append('_wpnonce', myAjax.nonce);
    
            $.ajax({
                type: 'POST',
                url: myAjax.ajaxurl,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        $('#messageContainer').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                        setTimeout(function() {
                            window.location.reload(); // Refresh the page to reflect the deletion
                        }, 200);
                    } else {
                        $('#messageContainer').html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                    }
                },
                error: function(response) {
                    $('#messageContainer').html('<div class="notice notice-error"><p>' + response.responseJSON.data.message + '</p></div>');
                }
            });
        } else {
            alert('Deletion cancelled. No action taken.');
        }
    });
    

    // AJAX Submission for Fetching Reservations with updated functionality
    $('#fetch_reservations').submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'ibh_handle_form');
        formData.append('entity', 'reservation');
        formData.append('action_type', 'fetch_reservations');
        formData.append('_wpnonce', myAjax.nonce);

        $.ajax({
            url: myAjax.ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Clear any previous messages
                $('#messageContainer').empty();

                if (response.success) {
                    // Dynamically update the UI with the received HTML for available bikes
                    // This assumes response.data contains the HTML needed for available bikes
                    updateAvailableBikesUI(response.data);

                    // Optionally, show a success message if needed
                    $('#messageContainer').html('<div class="notice notice-success is-dismissible"><p>Available bikes updated successfully.</p></div>');
                    setTimeout(function() {
                        $('#messageContainer').html('');
                    }, 3000);
                } else {
                    // Handle failure: Show error message from response or a default message
                    var errorMessage = response.data && response.data.message ? response.data.message : 'Failed to fetch available bikes.';
                    $('#messageContainer').html('<div class="notice notice-error"><p>' + errorMessage + '</p></div>');
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                var errorMsg = error || 'Unknown AJAX error.';
                $('#messageContainer').html('<div class="notice notice-error"><p>AJAX error: ' + errorMsg + '</p></div>');
            }
        });
    });

    $(document).on('submit', '#reservation-form', function(e) {
        e.preventDefault(); // Prevent the form from submitting traditionally
        console.log("Testing submit_reservation");

        var formData = new FormData();
        formData.append('action', 'ibh_handle_form');
        formData.append('entity', 'reservation');
        formData.append('action_type', 'add_new_reservation');
        formData.append('_wpnonce', myAjax.nonce);
        formData.append('customer_id', $('#reservation_customer').val());
        formData.append('reservation_notes', $('#reservation_notes').val());
        formData.append('reservation_stage', $('#reservation_stage').val());
        formData.append('from_date', $('#reservation_fromdate').val());
        formData.append('to_date', $('#reservation_todate').val());
        formData.append('from_time', $('#reservation_fromtime').val());
        formData.append('to_time', $('#reservation_totime').val());
        // Append selected bikes to formData
        $('input[name="selected_bikes[]"]:checked').each(function() {
            formData.append('selected_bikes[]', $(this).val());
        });

        $.ajax({
            url: myAjax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#messageContainer').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                    setTimeout(function() {
                        $('#messageContainer').html('');
                        window.location.reload(); 
                    }, 1000);
                } else {
                    $('#messageContainer').html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                }
            },
            error: function() {
                $('#messageContainer').html('<div class="notice notice-error"><p>AJAX error: ' + error + '</p></div>');
            }
        });
    });

    $('.edit-reservation-button').on('click', function(e) {
        e.preventDefault(); 
        var reservationId = $(this).data('reservation-id'); 
        var formData = new FormData();
        formData.append('action', 'ibh_handle_form');
        formData.append('entity', 'reservation');
        formData.append('action_type', 'fetch_reservations');
        formData.append('_wpnonce', myAjax.nonce);
        formData.append('reservation_id', reservationId);
        formData.append('is_edit', true);
    
        $.ajax({
            url: myAjax.ajaxurl, 
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    console.log(response.data);
                    // Directly inject the received HTML into the dynamic form container
                    $('#dynamicFormContainer').html(response.data.html);
                    // Hide or adjust visibility of other elements as necessary
                    $('#reservation-form-container').hide();
                    $('#reservation-list-view').hide();
                    $('#dynamicFormContainer').show(); // Ensure the container is visible
                    // Re-initialize any dynamic elements within the form
                    var testBlockedDates = [{date: '2024-02-21', is_blocked: '1'}]
                    initializeDatepickersWithBlocked(response.data.blockedDates);
                    DropDownForReservation();
                } else {
                    alert('Could not load reservation details.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    });
    
    $(document).on('submit', '#update-reservation', function(e) {
        e.preventDefault(); 
        var formData = new FormData(this); 
        formData.append('action', 'ibh_handle_form');
        formData.append('entity', 'reservation');
        formData.append('action_type', 'update_reservation');
        formData.append('_wpnonce', myAjax.nonce);
            
        $.ajax({
            url: myAjax.ajaxurl, 
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    console.log(response.data);
                    // Directly inject the received HTML into the dynamic form container
                    $('#messageContainer').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                    $('#reservation-form-container').hide();
                    $('#reservation-list-view').show();
                    $('#dynamicFormContainer').hide(); 
                    setTimeout(()=>{
                        window.location.reload();
                        $('#messageContainer').hide();
                    }, 1000)
                } else {
                    alert('Could not load reservation details.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    });

    $('.delete-reservation-button').on('click', function(e) {
        e.preventDefault(); 

        var deleteConfirmation = prompt("Type 'delete' to confirm deletion of this item.");

        if (deleteConfirmation === 'delete') {
            var reservationId = $(this).data('reservation-id'); 
            var formData = new FormData();
            formData.append('action', 'ibh_handle_form');
            formData.append('entity', 'reservation');
            formData.append('action_type', 'delete_reservation');
            formData.append('_wpnonce', myAjax.nonce);
            formData.append('reservation_id', reservationId);

            // Print each field of the FormData object individually
            console.log("FormData Details:");
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }    
            $.ajax({
                url: myAjax.ajaxurl, 
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.success) {
                        console.log(response.data);
                        // Directly inject the received HTML into the dynamic form container
                        $('#messageContainer').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                        $('#reservation-form-container').hide();
                        $('#reservation-list-view').show();
                        $('#dynamicFormContainer').hide(); 
                        setTimeout(()=>{
                            window.location.reload();
                            $('#messageContainer').hide();
                        }, 1000)
                    } else {
                        alert('Could not load reservation details.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                }
            });
        }else{
            alert('Deletion cancelled. No action taken.');
        }
    });
    
    $('form#blocked-date').submit(function(e) {
        e.preventDefault(); 

        var formData = new FormData(this);
        // Append additional data required for the form processing
        formData.append('action', 'ibh_handle_form'); 
        formData.append('entity', 'blocked_date'); 
        formData.append('action_type', 'add'); 
        formData.append('_wpnonce', myAjax.nonce); 

        $.ajax({
            type: 'POST',
            url: myAjax.ajaxurl, 
            data: formData,
            contentType: false, 
            processData: false, 
            success: function(response) {
                $('#messageContainer').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                window.location.reload(); 
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.status + ': ' + xhr.statusText;
                $('#messageContainer').html('<div class="notice notice-error"><p>AJAX error: ' + errorMessage + '</p></div>');
            }
        });
    });

    $('.delete-blockeddate').on('click', function(e) {
        e.preventDefault(); 


        var deleteConfirmation = prompt("Type 'delete' to confirm deletion of this item.");
    
        if (deleteConfirmation === 'delete') {
            var formData = new FormData();
            var dateToDelete = $(this).data('date'); 

            // Append additional data required for the form processing
            formData.append('action', 'ibh_handle_form'); 
            formData.append('entity', 'blocked_date'); 
            formData.append('action_type', 'delete'); 
            formData.append('_wpnonce', myAjax.nonce);
            formData.append('blocked_date', dateToDelete);

            formData.forEach(function(value, key) {
                console.log(key + ': ' + value);
            });
            $.ajax({
                type: 'POST',
                url: myAjax.ajaxurl, 
                data: formData,
                contentType: false, 
                processData: false, 
                success: function(response) {
                    $('#messageContainer').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                    window.location.reload(); 
                },
                error: function(xhr, status, error) {
                    var errorMessage = xhr.status + ': ' + xhr.statusText;
                    $('#messageContainer').html('<div class="notice notice-error"><p>AJAX error: ' + errorMessage + '</p></div>');
                }
            });

        }else{
            alert('Deletion cancelled. No action taken.');
        }
        
    });

});

function updateAvailableBikesUI(data) {
    if (data && data.html) {
        jQuery('#dynamicFormContainer').html(data.html);
        DropDownForReservation();
    } else {
        var message = '<div class="notice notice-warning is-dismissible"><p><strong>No available bikes for the selected dates. Please adjust your selection or check your Inventory.</strong></p></div>';
        jQuery('#dynamicFormContainer').html(message);
    }
}


function DropDownForReservation() {
    // Delegate the click event from the #dynamicFormContainer static parent
    jQuery('#dynamicFormContainer').on('click', '.toggle-category-label', function() {
        // The associated bikes are within the next .bikes tbody relative to the clicked label
        jQuery(this).closest('.labels').next('.bikes').toggle();
    });

    // Other event listeners as needed
}

function populateEditReservations(data) {
    const { reservation_details } = data;

    // Populate static fields
    jQuery('#reservation_from_date').val(reservation_details.from_date);
    jQuery('#reservation_from_time').val(reservation_details.from_time);
    jQuery('#reservation_to_date').val(reservation_details.to_date);
    jQuery('#reservation_to_time').val(reservation_details.to_time);
    jQuery('#reservation_customer').val(reservation_details.fname);
    jQuery('#reservation_notes').val(reservation_details.delivery_notes);
    jQuery('#reservation_stage').val(reservation_details.reservation_stage);

}

function loadEditReservationContent(data) {
    
    const {bookedItems} = data;

    // Clear previous dynamic content
    jQuery('#dynamicFormContainer').empty();

    // If there are booked items, dynamically generate HTML for each
    if (bookedItems.length > 0) {
        let itemsHtml = bookedItems.map(item => `
            <div class="booked-item">
                <label>
                    <input type="checkbox" name="selected_bikes[]" value="${item.item_id}" checked>
                    ${item.id_number} - ${item.name}
                </label>
            </div>
        `).join('');

        // Append the generated HTML to your dynamic form container
        jQuery('#dynamicFormContainer').html(itemsHtml);
    } else {
        // If no items were booked, you might want to show a message or handle accordingly
        jQuery('#dynamicFormContainer').html('<p>No bikes/items were booked for this reservation.</p>');
    }
}

function initializeDatepickersWithBlockedDates(blockedDates) {

    function disableBlockedDates(date) {
        // Convert date to string in yyyy-mm-dd format
        var dateString = $.datepicker.formatDate('yy-mm-dd', date);
        
        // Check if the date is in the blockedDates array
        return [blockedDates.findIndex(function(reservation) {
            return reservation.date === dateString && reservation.is_blocked === '1'; // Check both date and is_blocked status
        }) === -1];
    }
    

    // Initialize the fromdate_picker
    jQuery("#reservation_fromdate").datepicker({
       // beforeShowDay: disableBlockedDates,
        minDate: 0,
        dateFormat: "yy-mm-dd",
        onSelect: function(selectedDate) {
            // Update the minDate of the todate_picker to be after the selected date
            $("#reservation_todate").datepicker("option", "minDate", selectedDate);
        }
    });

    // Initialize the todate_picker
    jQuery("#reservation_todate").datepicker({
        beforeShowDay: disableBlockedDates,
        minDate: 0,
        dateFormat: "yy-mm-dd"
    });

    
}

function initializeDatepickersWithBlocked(blockedDates) {
    jQuery("#reservation_fromdate, #reservation_todate").datepicker({
        beforeShowDay: function(date) {
            // Check for weekends
            var noWeekend = jQuery.datepicker.noWeekends(date);
            if (!noWeekend[0]) {
                return noWeekend; // If it's weekend, disable it
            }

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
}


