<table class="form-table">
	<tr>
		<th><label for="full_name">Select patient name</label></th>
		<td>
		<select name="full_name" id="full_name">";
<?php
    // Query the authors here
    $patient_id = get_post_meta($post->ID, 'full_name', true);
    $query = new WP_Query( 'post_type=patient' );
    while ( $query->have_posts() ) {
        $query->the_post();
        $id = get_the_ID();
        $selected = "";

        if($id == $patient_id){
            $selected = ' selected="selected"';
        }
        echo '<option' . $selected . ' value=' . $id . '>' . get_the_title() . '</option>';
    } 
    ?>
		</select>
		<td>
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
 
