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

});
 