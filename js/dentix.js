jQuery(document).ready(function($) {

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
	$('#date_of_birth').datepicker({
	  dateFormat : 'dd-mm-yy',
      changeMonth: true,
      changeYear: true,
      yearRange: "1950:2015"
	});

//Color picker
	$('.color').colorPicker();

//Tabs Navigation
	// Grab the wrapper for the Navigation Tabs
	var navTabs = $( '#dentix-navigation').children( '.nav-tab-wrapper' ),
		tabIndex = null;

	navTabs.children().each(function() {

		$( this ).on( 'click', function( evt ) {

			evt.preventDefault();

			// If this tab is not active...
			if ( ! $( this ).hasClass( 'nav-tab-active' ) ) {

				// Unmark the current tab and mark the new one as active
				$( this ).siblings( '.nav-tab-active' ).removeClass( 'nav-tab-active' );
				$( this ).addClass( 'nav-tab-active' );

				// Save the index of the tab that's just been marked as active. It will be 0 - 3.
				tabIndex = $( this ).index();

				// Hide the old active content
				$( '#dentix-navigation' )
					.children( 'div:not( .inside.hidden )' )
					.addClass( 'hidden' );

				$( '#dentix-navigation' )
					.children( 'div:nth-child(' + ( tabIndex ) + ')' )
					.addClass( 'hidden' );

				// And display the new content
				$( '#dentix-navigation' )
					.children( 'div:nth-child( ' + ( tabIndex + 2 ) + ')' )
					.removeClass( 'hidden' );

			}

		});
	});

// Gallery

	var file_frame;

	$(document).on('click', '#dentix-metabox a.gallery-add', function(e) {

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
			var listIndex = $('#gallery-metabox-list li').index($('#gallery-metabox-list li:last')),
				selection = file_frame.state().get('selection');

			selection.map(function(attachment, i) {
				attachment = attachment.toJSON(),
				index      = listIndex + (i + 1);

				$('#gallery-metabox-list').append('<li><input type="hidden" name="dentix_gallery[' + index + ']" value="' + attachment.id + '"><img class="image-preview" src="' + attachment.sizes.thumbnail.url + '"><small><a class="remove-image" href="#">Remove image</a></small></li>');
			});
		});

		makeSortable();
    
		file_frame.open();

	});

	function resetIndex() {
		$('#gallery-metabox-list li').each(function(i) {
			$(this).find('input:hidden').attr('name', 'dentix_gallery[' + i + ']');
		});
	}

	function makeSortable() {
		$('#gallery-metabox-list').sortable({
			opacity: 0.6,
			stop: function() {
				resetIndex();
			}
		});
	}

	$(document).on('click', '#dentix-metabox a.remove-image', function(e) {
		e.preventDefault();

		$(this).parents('li').animate({ opacity: 0 }, 200, function() {
			$(this).remove();
			resetIndex();
		});
	});

	makeSortable();

//Disable postbox close togle
    $( '.postbox' ).removeClass( 'closed' );
    $( '.postbox .hndle' ).css( 'cursor', 'default' );
    $( document ).delegate( '.postbox h3, .postbox .handlediv', 'click', function() {
        $( this )
        .unbind( 'click.postboxes' )
        .parent().removeClass( 'closed' );
    } );

});
