<table class="form-table">
	<tr>
		<th><label for="full_name"><?php _e("Full Name", "dentix"); ?></label></th>
		<td><input type="text" name="full_name" id="full_name" value="<?php echo @get_post_meta($post->ID, 'full_name', true); ?>" class="regular-text"></td>
	</tr>
	<tr>
		<th><label for="appointment_date"><?php _e("Appointment Date", "dentix"); ?></label></th>
		<td><input type="text" name="appointment_date" class="appointment_date" id="appointment_date" value="<?php echo @get_post_meta($post->ID, 'appointment_date', true); ?>" class="regular-text"> (format: <i>dd-mm-yyyy</i>)</td>
	</tr>
	<tr>
		<th><label for="main_complaint"><?php _e("Main Complaint", "dentix"); ?></label></th>
		<td><textarea name="main_complaint" id="main_complaint" class="large-text"><?php echo @get_post_meta($post->ID, 'main_complaint', true); ?></textarea></td>
	</tr>
</table>
 
