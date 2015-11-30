<?php

$_profile = array (
    'picture',
    'registration_number',
    'full_name',
    'sex',
    'date_of_birth',
    'address',
    'phone_number',
    'occupation',
    'marriage'
);

$_adult_odontogram_upper = array (
    'gg_18',
    'gg_17',
    'gg_16',
    'gg_15',
    'gg_14',
    'gg_13',
    'gg_12',
    'gg_11',
    'gg_21',
    'gg_22',
    'gg_23',
    'gg_24',
    'gg_25',
    'gg_26',
    'gg_27',
    'gg_28'
);

$_adult_odontogram_lower = array (
    'gg_48',
    'gg_47',
    'gg_46',
    'gg_45',
    'gg_44',
    'gg_43',
    'gg_42',
    'gg_41',
    'gg_31',
    'gg_32',
    'gg_33',
    'gg_34',
    'gg_35',
    'gg_36',
    'gg_37',
    'gg_38'
);

$_child_odontogram_upper = array (
    'gg_55',
    'gg_54',
    'gg_53',
    'gg_52',
    'gg_51',
    'gg_61',
    'gg_62',
    'gg_63',
    'gg_64',
    'gg_65'
);

$_child_odontogram_lower = array (
    'gg_75',
    'gg_74',
    'gg_73',
    'gg_72',
    'gg_71',
    'gg_81',
    'gg_82',
    'gg_83',
    'gg_84',
    'gg_85'
);
add_action( 'add_meta_boxes', 'dentix_metabox');

function dentix_metabox( $post ) {
    add_meta_box('dentix-metabox', 'Dentix Metabox', 'render_dentix_metabox', 'dentix', 'advanced', 'high');
} 

function render_dentix_metabox( $post) {
    global $wpdb, $_profile, $_adult_odontogram_upper, $_adult_odontogram_lower, $_child_odontogram_upper, $_child_odontogram_lower;

$today="P".date("Ym");
$recentRegNumber = $wpdb->get_var("SELECT meta_value from {$wpdb->postmeta} where meta_key = 'registration_number' order by post_id desc limit 1");
$lastNumber = substr($recentRegNumber, 8, 4);
$nextNumber = $lastNumber + 1;
$nextRegNumber = $today.sprintf('%04s', $nextNumber);

    $dentix_galleries = get_post_meta($post->ID, 'dentix_gallery', true);

	wp_nonce_field( 'dentix_metabox_nonce', 'dentix_metabox_nonce' );

	echo '<div id="dentix-navigation">
	<h2 class="nav-tab-wrapper current">
		<a class="nav-tab nav-tab-active" href="javascript:;">Profile</a>
		<a class="nav-tab" href="javascript:;">Odontogram</a>
		<a class="nav-tab" href="javascript:;">Photos</a>
	</h2>';

	echo '<div class="inside">';

	echo '<table class="form-table">';
	    foreach ($_profile as $field) {
			$meta = get_post_meta($post->ID, $field, true);
			echo '<tr>';
			echo '<th><label for="' . $field . '"><strong>' . __(ucwords(str_replace("_", " ", $field))) . '</strong></label></th>';
            if ( $field == 'picture' ) {
			echo '<td><img src="', $meta ? $meta : stripslashes(htmlspecialchars(( ""), ENT_QUOTES)), '" id="picture_preview" /><br/> <input type="hidden" name="' . $field . '" id="' . $field . '" value="', $meta ? $meta : stripslashes(htmlspecialchars(( ""), ENT_QUOTES)), '" class="regular-text"  /><input type="button" name="upload-picture" id="upload-picture" value="Upload Picture" class="button-secondary"  /></td>';
            } elseif ( $field == 'registration_number' ) {
                if($meta == "") {
			echo '<td><input type="text" name="' . $field . '" id="' . $field . '" value="' . $nextRegNumber . '" class="medium-text" /></td>';
			    } else {
			echo '<td><input type="text" name="' . $field . '" id="' . $field . '" value="', $meta ? $meta : stripslashes(htmlspecialchars(( ""), ENT_QUOTES)), '" class="medium-text" /></td>';
			    }
            } elseif ( $field == 'sex' ) { 
            echo '<td><input type="radio" name="' . $field . '" id="' . $field . '_male"  value="Male"  ' . checked( $meta, 'Male', false) . ' /> Male ';
            echo '<input type="radio" name="' . $field . '" id="' . $field . '_female"  value="Female" ' . checked( $meta, 'Female', false) . ' /> Female</td>';
            } elseif ( $field == 'address' ) { 
            echo '<td><textarea rows="3" name="' . $field . '" id="' . $field . '" class="large-text">', $meta ? $meta : stripslashes(htmlspecialchars(( ""), ENT_QUOTES)), '</textarea></td>';
            } elseif ( $field == 'marriage' ) { 
            echo '<td><input type="radio" name="' . $field . '" id="' . $field . '_single"  value="Single"  ' . checked( $meta, 'Single', false) . ' /> Single ';
            echo '<input type="radio" name="' . $field . '" id="' . $field . '_married"  value="Married" ' . checked( $meta, 'Married', false) . ' /> Married</td>';
            } elseif ( $field == 'date_of_birth' ) {
			echo '<td><input type="text" name="' . $field . '" id="' . $field . '" value="', $meta ? $meta : stripslashes(htmlspecialchars(( ""), ENT_QUOTES)), '" class="medium-text"  placeholder="dd/mm/yyyy"/></td>';
            } else {
			echo '<td><input type="text" name="' . $field . '" id="' . $field . '" value="', $meta ? $meta : stripslashes(htmlspecialchars(( ""), ENT_QUOTES)), '" class="regular-text" /></td>';
            }
			echo '<tr>';
		}
	echo '</table>';
	echo '</div>';

	echo '<div class="inside hidden">';
  	echo '<div class="table-odontogram">';
	echo '<table style="margin: 20px auto; width: 450px; text-align: center;">';
			echo '<tr>';
	        foreach ($_adult_odontogram_upper as $aou_num) {
			echo '<td><strong>' . str_replace("gg_", "", $aou_num) . '</strong></td>';
		    }
			echo '</tr>';
			echo '<tr>';
	        foreach ($_adult_odontogram_upper as $aou) {
			$meta = get_post_meta($post->ID, $aou, true);
			echo '<td><input type="text" name="' . $aou . '" id="' . $aou . '" value="' . $meta . '" class="odont_input color" /></td>';
		    }
			echo '</tr>';
			echo '<tr>';
	        foreach ($_adult_odontogram_lower as $aol) {
			$meta = get_post_meta($post->ID, $aol, true);
			echo '<td><input type="text" name="' . $aol . '" id="' . $aol . '" value="' . $meta . '" class="odont_input color" /></td>';
		    }
			echo '</tr>';
			echo '<tr>';
	        foreach ($_adult_odontogram_lower as $aol_num) {
			echo '<td><strong>' . str_replace("gg_", "", $aol_num) . '</strong></td>';
		    }
			echo '</tr>';
	echo '</table>';
	echo '<table style="margin: 20px auto; width: 300px; text-align: center;">';
			echo '<tr>';
	        foreach ($_child_odontogram_upper as $cou_num) {
			echo '<td><strong>' . str_replace("gg_", "", $cou_num) . '</strong></td>';
		    }
			echo '</tr>';
			echo '<tr>';
	        foreach ($_child_odontogram_upper as $cou) {
			$meta = get_post_meta($post->ID, $cou, true);
			echo '<td><input type="text" name="' . $cou . '" id="' . $cou . '" value="' . $meta . '" class="odont_input color" /></td>';
		    }
			echo '</tr>';
			echo '<tr>';
	        foreach ($_child_odontogram_lower as $col) {
			$meta = get_post_meta($post->ID, $col, true);
			echo '<td><input type="text" name="' . $col . '" id="' . $col . '" value="' . $meta . '" class="odont_input color" /></td>';
		    }
			echo '</tr>';
			echo '<tr>';
	        foreach ($_child_odontogram_lower as $col_num) {
			echo '<td><strong>' . str_replace("gg_", "", $col_num) . '</strong></td>';
		    }
			echo '</tr>';
	echo '</table>';
?>
	<table style="margin: 30px auto 0 auto; width: 450px;">
		<tr>
			<td style="height: 20px; width: 20px; background-color: #ffffff; border: 1px solid #000000; "></td>
			<td> = Normal</td>
			<td style="height: 20px; width: 20px; background-color: #FF0000; border: 1px solid #000000; "></td>
			<td> = Dicabut</td>
			<td style="height: 20px; width: 20px; background-color: #000000; border: 1px solid #000000; "></td>
			<td> = Hilang</td>
			<td style="height: 20px; width: 20px; background-color: #FFFF00; border: 1px solid #000000; "></td>
			<td> = Karies</td>
		</tr>
		<tr>
			<td style="height: 20px; width: 20px; background-color: #FF6600; border: 1px solid #000000; "></td>
			<td> = Sisa Akar</td>
			<td style="height: 20px; width: 20px; background-color: #0000FF; border: 1px solid #000000; "></td>
			<td> = Tumpatan</td>
			<td style="height: 20px; width: 20px; background-color: #FF00FF; border: 1px solid #000000; "></td>
			<td> = Gigi Tiruan</td>
			<td style="height: 20px; width: 20px; background-color: #339966; border: 1px solid #000000; "></td>
			<td> = Goyang</td>
		</tr>
	</table>
<?php
	echo '</div>';
	echo '</div>';

	echo '<div class="inside hidden">';
?>
    <table class="form-table">
      <tr><td>
        <a class="gallery-add button" href="#" data-uploader-title="Add image(s) to gallery" data-uploader-button-text="Add image(s)">Add image(s)</a>
        <ul id="gallery-metabox-list">
        <?php if ($dentix_galleries) : foreach ($dentix_galleries as $key => $value) : $image = wp_get_attachment_image_src($value); ?>
          <li>
            <input type="hidden" name="dentix_gallery[<?php echo $key; ?>]" value="<?php echo $value; ?>">
            <img class="image-preview" src="<?php echo $image[0]; ?>">
            <small><a class="remove-image" href="#">Remove image</a></small>
          </li>
        <?php endforeach; endif; ?>
        </ul>
      </td></tr>
    </table>
<?php
	echo '</div>';
	echo '</div>';
} 

add_action('save_post', 'save_dentix_metabox');

function save_dentix_metabox($post_id) {
	global $_profile, $_adult_odontogram_upper, $_adult_odontogram_lower, $_child_odontogram_upper, $_child_odontogram_lower;
	
	if ( ! isset( $_POST['dentix_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['dentix_metabox_nonce'], 'dentix_metabox_nonce' ) ) {
		return;
		}

	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
            
    if(isset($_POST['post_type']) && $_POST['post_type'] == 'dentix' && current_user_can('edit_post', $post_id)) {
    	foreach($_profile as $field) {
    		update_post_meta($post_id, $field, $_POST[$field]);
        }

		foreach ($_adult_odontogram_upper as $aou) {
			$old = get_post_meta($post_id, $aou, true);
			$new = $_POST[$aou];
	 
			if ($new && $new != $old) {
				update_post_meta($post_id, $aou, $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $aou, $old);
			}
		} 

		foreach ($_adult_odontogram_lower as $aol) {
			$old = get_post_meta($post_id, $aol, true);
			$new = $_POST[$aol];
	 
			if ($new && $new != $old) {
				update_post_meta($post_id, $aol, $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $aol, $old);
			}
		}

		foreach ($_child_odontogram_upper as $cou) {
			$old = get_post_meta($post_id, $cou, true);
			$new = $_POST[$cou];
	 
			if ($new && $new != $old) {
				update_post_meta($post_id, $cou, $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $cou, $old);
			}
		} 

		foreach ($_child_odontogram_lower as $col) {
			$old = get_post_meta($post_id, $col, true);
			$new = $_POST[$col];
	 
			if ($new && $new != $old) {
				update_post_meta($post_id, $col, $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $col, $old);
			}
		}

		update_post_meta($post_id, 'dentix_gallery', $_POST['dentix_gallery']);
  
	} else {
		return;
    } 
} 


?>
