jQuery(document).ready(function($) {
    console.log("Hi Hi! This is a test!");

    $('form#item').submit(function(e) {
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
                        window.location.reload(); // Refresh the page to reflect the deletion
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
    
    
});
 