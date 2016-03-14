<?php
	global $wpdb;
	$today="P".date("Ym");
	$recentRegNumber = $wpdb->get_var("SELECT meta_value from {$wpdb->postmeta} where meta_key = 'registration_number' order by post_id desc limit 1");
	$lastNumber = substr($recentRegNumber, 8, 4);
	$nextNumber = $lastNumber + 1;
	$nextRegNumber = $today.sprintf('%04s', $nextNumber);

?>

<table class="form-table">
	<tr>
		<th><label for="registration_number"><?php _e("Registration Number", "dentix"); ?></label></th>
		<td><input type="text" name="registration_number" id="registration_number" value="<?php if (get_post_meta($post->ID, 'registration_number', true) == "") { echo $nextRegNumber; } else { echo @get_post_meta($post->ID, 'registration_number', true);} ?>" class="regular-text" readonly ></td>
	</tr>
	<tr>
		<th><label for="full_name"><?php _e("Full Name", "dentix"); ?></label></th>
		<td><input type="text" name="full_name" id="full_name" value="<?php echo @get_post_meta($post->ID, 'full_name', true); ?>" class="regular-text"></td>
	</tr>
	<tr>
		<th><label for="sex"><?php _e("Sex", "dentix"); ?></label></th>
		<td><label for="male">
        		<input type="radio" name="sex" id="male" value="Male" <?php checked( @get_post_meta($post->ID, 'sex', true), 'Male' ); ?>> Male
    		</label>
    		<label for="female">
        		<input type="radio" name="sex" id="female" value="Female" <?php checked( @get_post_meta($post->ID, 'sex', true), 'Female' ); ?>> Female
    		</label></td>
	</tr>
	<tr>
		<th><label for="birthdate"><?php _e("Birthdate", "dentix"); ?></label></th>
		<td><input type="text" name="birthdate" class="birthdate" id="birthdate" value="<?php echo @get_post_meta($post->ID, 'birthdate', true); ?>" class="regular-text"> (format: <i>dd-mm-yyyy</i>)</td>
	</tr>
	<tr>
		<th><label for="address"><?php _e("Address", "dentix"); ?></label></th>
		<td><textarea name="address" id="address" class="large-text"><?php echo @get_post_meta($post->ID, 'address', true); ?></textarea></td>
	</tr>
	<tr>
		<th><label for="phone_number"><?php _e("Phone Number", "dentix"); ?></label></th>
		<td><input type="text" name="phone_number" id="phone_number" value="<?php echo @get_post_meta($post->ID, 'phone_number', true); ?>" class="regular-text"></td>
	</tr>
	<tr>
		<th><label for="occupation"><?php _e("Occupation", "dentix"); ?></label></th>
		<td><input type="text" name="occupation" id="occupation" value="<?php echo @get_post_meta($post->ID, 'occupation', true); ?>" class="regular-text"></td>
	</tr>
	<tr>
		<th><label for="marriage"><?php _e("Marriage", "dentix"); ?></label></th>
		<td>
		<label for="single">
        		<input type="radio" name="marriage" id="single" value="Single" <?php checked( @get_post_meta($post->ID, 'marriage', true), 'Single' ); ?>> Single
    		</label>
    		<label for="married">
        		<input type="radio" name="marriage" id="married" value="Married" <?php checked( @get_post_meta($post->ID, 'marriage', true), 'Married' ); ?>> Married
    		</label>
		</td>
	</tr>
</table>
