
(function( $ ) {
    $(function() {
        $(document).ready(function(){
            $(".wtddb-add-new").click(function(e){
                e.preventDefault();
                var $this = $(this); 
                // Add New Popup 
                var $popup = $(".wtddb-add-new-popup");
                // add class 
                $popup.fadeIn();
                $popup.addClass("show");
            });
            $(".wtddb-add-new-popup-close").click(function(e){
                e.preventDefault();
                var $this = $(this); 
                // Add New Popup 
                var $popup = $(".wtddb-add-new-popup");
                // add class 
                $popup.fadeOut();
                $popup.removeClass("show");
            });
            $(".wtddb-edit-popup-close").click(function(e){
                e.preventDefault();
                var $this = $(this); 
                // Add New Popup 
                var $popup = $(".wtddb-edit-popup");
                // add class 
                $popup.fadeOut();
                $popup.removeClass("show");
            });


            $('.wtddb-addnew-form').submit(function(e){
                e.preventDefault();  
                // add loader intu submit button
                var $this = $(this);
                $(".wtddb-addnew-form-submit").html('<i class="fa fa-spinner fa-spin"></i> Please wait...');
                // form data get as post method
                var formData = $(this).serialize();
                // pass data to ajax as wordpress methood
                jQuery.ajax({
                    url: wtddb_ajax_object.ajax_url,
                    type: 'post',
                    data: {
                        action: 'wtddb_add_new_data', 
                        formData: formData, 
                        ajax_nonce: wtddb_ajax_object.nonce
                    },
                    success: function (data) { 
                        // add loader intu submit button
                        $this.find(".wtddb-addnew-form-submit").html('Submit');
                        // Add New Popup 
                        var $popup = $(".wtddb-add-new-popup");
                        // add class 
                        $popup.fadeOut();
                        $popup.removeClass("show");
                        // reload page
                        location.reload();

                    }
                }); 
                 
            });

            $(".wtddb-edit-btn").click(function(e){
                e.preventDefault();
                var $this = $(this);
                var $data = $this.attr('edit-data');
                var $data = JSON.parse($data); 
                // Edit Popup
                var $popup = $(".wtddb-edit-popup");
                // set data to form
                $popup.find("input[name='id']").val($data.id);
                $popup.find("input[name='name']").val($data.name);
                $popup.find("input[name='email']").val($data.email);
                
                // add class
                $popup.fadeIn();
                $popup.addClass("show");

            }); 

            $('.wtddb-edit-form').submit(function(e){
                e.preventDefault();  
                // add loader intu submit button
                var $this = $(this);
                $(".wtddb-edit-form-submit").html('<i class="fa fa-spinner fa-spin"></i> Please wait...');
                // form data get as post method
                var formData = $(this).serialize();
                // pass data to ajax as wordpress methood
                jQuery.ajax({
                    url: wtddb_ajax_object.ajax_url,
                    type: 'post',
                    data: {
                        action: 'wtddb_edit_data', 
                        formData: formData, 
                        ajax_nonce: wtddb_ajax_object.nonce
                    },
                    success: function (data) { 
                        // add loader intu submit button
                        $this.find(".wtddb-edit-form-submit").html('Submit');
                        // Edit Popup 
                        var $popup = $(".wtddb-edit-popup");
                        // add class 
                        $popup.fadeOut();
                        $popup.removeClass("show");
                        // reload page
                        location.reload();

                    }
                }); 
                 
            });

            $(".wtddb-delete-btn").click(function(e){
                e.preventDefault();
                // Confirmation message
                var $confirm = confirm("Are you sure you want to delete this?");
                if(!$confirm){
                    return false;
                }
                var $this = $(this);
                var $id = $this.attr('data-id'); 

                // Loader
                $this.html('<i class="fa fa-spinner fa-spin"></i> Please wait...');

                // pass data to ajax as wordpress methood
                jQuery.ajax({
                    url: wtddb_ajax_object.ajax_url,
                    type: 'post',
                    data: {
                        action: 'wtddb_delete_data', 
                        id: $id, 
                        ajax_nonce: wtddb_ajax_object.nonce
                    },
                    success: function (data) { 
                        $this.html('Deleted');
                        // reload page
                        location.reload();

                    }
                });
            });

        });
         
    });
})( jQuery );