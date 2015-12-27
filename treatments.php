<?php
 
add_action('admin_init', 'treatments_metabox', 1);
function treatments_metabox() {
	add_meta_box( 'treatments-metabox', 'Treatments', 'render_treatments_metabox', 'patient', 'advanced', 'low');
}

function render_treatments_metabox() {
	global $post;

	$treatments_metabox_fields = get_post_meta($post->ID, 'treatments_metabox_fields', true);

	wp_nonce_field( 'treatments_metabox_nonce', 'treatments_metabox_nonce' );
	?>
	<script type="text/javascript">
	jQuery(document).ready(function( $ ){
		$( '#add-row' ).on('click', function() {
			var row = $( '.empty-row.screen-reader-text' ).clone(true);
			row.removeClass( 'empty-row screen-reader-text' );
			row.insertBefore( '#treatments-fieldset-one tbody>tr:last' );
			return false;
		});
  	
		$( '.remove-row' ).on('click', function() {
			$(this).parents('tr').remove();
			return false;
		});
	});
	</script>
  
	<table class="wp-list-table widefat fixed striped" id="treatments-fieldset-one" >
	<thead>
		<tr>
			<th scope="col" id='date' class='manage-column column-date column-primary'>Date</th>
			<th scope="col" id='anamnesis' class='manage-column column-anamnesis'>Anamnesis</th>
			<th scope="col" id='diagnosis' class='manage-column column-diagnosis'>Diagnosis</th>
			<th scope="col" id='treatment' class='manage-column column-treatment'>Treatment</th>
			<th scope="col" id='bill' class='manage-column column-bill'>Bill</th>
			<th width="25px"></th>
		</tr>
	</thead>
	<tbody id="the-list">
	<?php
	
	if ( $treatments_metabox_fields ) :
	
	foreach ( $treatments_metabox_fields as $field ) {
	?>
	<tr>
		<td class="date column-date has-row-actions column-primary page-date " data-colname="Date"><input type="text" class="widefat" name="date[]" value="<?php if($field['date'] != '') echo esc_attr( $field['date'] ); ?>" readonly /><button type="button" class="toggle-row"><span class="screen-reader-text">Tampilkan rincian</span></button></td>

		<td class='anamnesis column-anamnesis ' data-colname="Anamnesis "><input type="text" class="widefat" name="anamnesis[]" value="<?php if($field['anamnesis'] != '') echo esc_attr( $field['anamnesis'] ); ?>" readonly /></td>
	
		<td class='diagnosis column-diagnosis ' data-colname="Diagnosis "><input type="text" class="widefat" name="diagnosis[]" value="<?php if($field['diagnosis'] != '') echo esc_attr( $field['diagnosis'] ); ?>" readonly /></td>
	
		<td class='treatment column-treatment ' data-colname="Treatment "><input type="text" class="widefat" name="treatment[]" value="<?php if($field['treatment'] != '') echo esc_attr( $field['treatment'] ); ?>" readonly /></td>

		<td class='bill column-bill ' data-colname="Bill "><input type="text" class="widefat" name="bill[]" value="<?php if($field['bill'] != '') echo esc_attr( $field['bill'] ); ?>" readonly /></td>

		<td><a class="button remove-row" href="#">-</a></td>
	</tr>
	<?php
	}
	else :
	// show a blank one
	?>
	<tr>
		<td class="date column-date has-row-actions column-primary page-date " data-colname="Date" ><input type="text" class="widefat" name="date[]" value="<?php echo date("d-m-Y"); ?>" readonly /> <button type="button" class="toggle-row"><span class="screen-reader-text">Tampilkan rincian</span></button> </td>

		<td class='anamnesis column-anamnesis ' data-colname="Anamnesis "><input type="text" class="widefat" name="anamnesis[]" /></td>
	
		<td class='diagnosis column-diagnosis ' data-colname="Diagnosis "><input type="text" class="widefat" name="diagnosis[]" /></td>

		<td class='treatment column-treatment ' data-colname="Treatment "><input type="text" class="widefat" name="treatment[]" /></td>
	
		<td class='bill column-bill ' data-colname="Bill "><input type="text" class="widefat" name="bill[]" /></td>

		<td><a class="button remove-row" href="#">-</a></td>
	</tr>
	<?php endif; ?>
	
	<!-- empty hidden one for jQuery -->
	<tr class="empty-row screen-reader-text">
		<td class="date column-date has-row-actions column-primary page-date " data-colname="Date"><input type="text" class="widefat" name="date[]" value="<?php echo date("d-m-Y"); ?>" readonly /><button type="button" class="toggle-row"><span class="screen-reader-text">Tampilkan rincian</span></button></td>

		<td class='anamnesis column-anamnesis ' data-colname="Anamnesis " ><input type="text" class="widefat" name="anamnesis[]" /></td>
	
		<td class='diagnosis column-diagnosis ' data-colname="Diagnosis " ><input type="text" class="widefat" name="diagnosis[]" /></td>

		<td class='treatment column-treatment ' data-colname="Treatment " ><input type="text" class="widefat" name="treatment[]" /></td>
		  
		<td class='bill column-bill ' data-colname="Bill " ><input type="text" class="widefat" name="bill[]" /></td>

		<td><a class="button remove-row" href="#">-</a></td>
	</tr>
	</tbody>
	</table>
	
	<p><a id="add-row" class="button" href="#">Add Treatment</a></p>

	<?php
}

add_action('save_post', 'treatments_metabox_save');
function treatments_metabox_save($post_id) {
	if ( ! isset( $_POST['treatments_metabox_nonce'] ) ||
	! wp_verify_nonce( $_POST['treatments_metabox_nonce'], 'treatments_metabox_nonce' ) )
		return;
	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;
	
	if (!current_user_can('edit_post', $post_id))
		return;
	
	$old = get_post_meta($post_id, 'treatments_metabox_fields', true);
	$new = array();
	
	$dates = $_POST['date'];
	$anamnesises = $_POST['anamnesis'];
	$diagnosises = $_POST['diagnosis'];
	$treatmentes = $_POST['treatment'];
	$billes = $_POST['bill'];
	
	$count = count( $anamnesises );
	
	for ( $i = 0; $i < $count; $i++ ) {
		if ( $anamnesises[$i] != '' ) :
			$new[$i]['date'] = stripslashes( strip_tags( $dates[$i] ) );

			$new[$i]['anamnesis'] = stripslashes( strip_tags( $anamnesises[$i] ) );
			
			$new[$i]['diagnosis'] = stripslashes( strip_tags( $diagnosises[$i] ) );

			$new[$i]['treatment'] = stripslashes( strip_tags( $treatmentes[$i] ) );

			$new[$i]['bill'] = stripslashes( strip_tags( $billes[$i] ) );
		endif;
	}

	if ( !empty( $new ) && $new != $old )
		update_post_meta( $post_id, 'treatments_metabox_fields', $new );
	elseif ( empty($new) && $old )
		delete_post_meta( $post_id, 'treatments_metabox_fields', $old );
}
?>
