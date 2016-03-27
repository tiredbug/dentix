jQuery(document).ready(function($) {

	// Tabs metabox Make sure the script fires only when editing post:
	if( jQuery('form#post').length != 0 ) {
		
		// The menu html source code:
		menu_html = '<h2 class="nav-tab-wrapper current">\n';
		menu_html = menu_html + '\n<a class="nav-tab profile-metabox" href="#">Profile</a>';
		menu_html = menu_html + '\n<a class="nav-tab odontogram-metabox" href="#">Odontogram</a>';
		menu_html = menu_html + '\n<a class="nav-tab gallery-metabox" href="#">Gallery</a>';
		menu_html = menu_html + '\n</h2>';
		// Attach menu
		jQuery('#advanced-sortables').before(menu_html);
		jQuery('a.profile-metabox').addClass('nav-tab-active');
		
		// Initialize tab interface / first tab active
		jQuery('#profile-metabox').show();
		jQuery('#odontogram-metabox').hide();
		jQuery('#gallery-metabox').hide();
		
		// Switching tabs
		jQuery('a.profile-metabox').click(function(){
			// Set active menu item
			jQuery('a.profile-metabox').addClass( 'nav-tab-active' );
			jQuery('a.odontogram-metabox').removeClass('nav-tab-active');
			jQuery('a.gallery-metabox').removeClass('nav-tab-active');
			// Show current tab metaboxes
		    jQuery('#profile-metabox').show();
		    jQuery('#odontogram-metabox').hide();
		    jQuery('#gallery-metabox').hide();
		}); 

		jQuery('a.odontogram-metabox').click(function(){
			// Set active menu item
			jQuery('a.profile-metabox').removeClass('nav-tab-active');
			jQuery('a.odontogram-metabox').addClass( 'nav-tab-active' );
			jQuery('a.gallery-metabox').removeClass('nav-tab-active');
			// Show current tab metaboxes
		    jQuery('#profile-metabox').hide();
		    jQuery('#odontogram-metabox').show();
		    jQuery('#gallery-metabox').hide();
		}); 

		jQuery('a.gallery-metabox').click(function(){
			// Set active menu item
			jQuery('a.profile-metabox').removeClass('nav-tab-active');
			jQuery('a.odontogram-metabox').removeClass('nav-tab-active');
			jQuery('a.gallery-metabox').addClass( 'nav-tab-active' );
			// Show current tab metaboxes
		    jQuery('#profile-metabox').hide();
		    jQuery('#odontogram-metabox').hide();
		    jQuery('#gallery-metabox').show();
		}); 

		jQuery('a.treatments-metabox').click(function(){
			// Set active menu item
			jQuery('a.profile-metabox').removeClass('nav-tab-active');
			jQuery('a.odontogram-metabox').removeClass('nav-tab-active');
			jQuery('a.gallery-metabox').removeClass('nav-tab-active');
			// Show current tab metaboxes
		    jQuery('#profile-metabox').hide();
		    jQuery('#odontogram-metabox').hide();
		    jQuery('#gallery-metabox').hide();
		}); 

	} 


//Picture uploader
    var custom_uploader;
    $('#upload-picture').click(function(e) {
        e.preventDefault();
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Picture',
        library: {
            type: 'image'
        },
            button: {
                text: 'Choose Picture'
            },
            multiple: true
        });
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#picture').val(attachment.sizes.thumbnail.url);
            $('#picture_preview').attr('src', attachment.sizes.thumbnail.url );
        });
        //Open the uploader dialog
        custom_uploader.open();
    });

//Date Picker
	$('#birthdate').datepicker({
		dateFormat : 'yy-mm-dd',
      		changeMonth: true,
      		changeYear: true,
      		yearRange: "1950:2015"
	});

//Color picker
	$('.color').colorPicker();

// Gallery

	var file_frame;

	$(document).on('click', '#gallery-metabox a.image-add', function(e) {

		e.preventDefault();

		if (file_frame) file_frame.close();

		file_frame = wp.media.frames.file_frame = wp.media({
			title: $(this).data('uploader-title'),
			button: {
				text: $(this).data('uploader-button-text'),
			},
			multiple: true
		});

		file_frame.on('select', function() {
			var listIndex = $('#image-list li').index($('#image-list li:last')),
				selection = file_frame.state().get('selection');

			selection.map(function(attachment, i) {
				attachment = attachment.toJSON(),
				index      = listIndex + (i + 1);

				$('#image-list').append('<li><input type="hidden" name="images[' + index + ']" value="' + attachment.id + '"><img class="image-preview" src="' + attachment.sizes.thumbnail.url + '"><small><a class="remove-image" href="#">Remove image</a></small></li>');
			});
		});

		makeSortable();
    
		file_frame.open();

	});

	function resetIndex() {
		$('#image-list li').each(function(i) {
			$(this).find('input:hidden').attr('name', 'images[' + i + ']');
		});
	}

	function makeSortable() {
		$('#image-list').sortable({
			opacity: 0.6,
			stop: function() {
				resetIndex();
			}
		});
	}

	$(document).on('click', '#gallery-metabox a.remove-image', function(e) {
		e.preventDefault();

		$(this).parents('li').animate({ opacity: 0 }, 200, function() {
			$(this).remove();
			resetIndex();
		});
	});

	makeSortable();

});
