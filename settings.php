<?php

add_action('admin_init', 'register_dentix_settings' );
add_action('admin_menu', 'dentix_settings_menu');

// Init plugin options to white list our options
function register_dentix_settings(){
	register_setting( 'dentix_settings', 'dentix_options', 'dentix_validate' );
}

// Add menu page
function dentix_settings_menu() {
	add_submenu_page('edit.php?post_type=dentix', 'Dentix Settings', 'Settings', 'manage_options', 'dentix_settings', 'dentix_settings_page');
}

// Draw the menu page itself
function dentix_settings_page() {
	?>
	<div class="wrap">
		<h2><?php _e( 'Dentix Settings', 'dentix' ); ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields('dentix_settings'); ?>
			<?php $options = get_option('dentix_options'); ?>
			<table class="form-table">
				</tr>
				<tr valign="top"><th scope="row">Dentist Name</th>
					<td><input type="text" name="dentix_options[dentistname]" value="<?php echo $options['dentistname']; ?>" /></td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function dentix_validate($input) {
	
	// Say option must be safe text with no HTML tags
	$input['dentistname'] =  wp_filter_nohtml_kses($input['dentistname']);
	
	return $input;
}
?>
