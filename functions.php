<?php

add_action('edit_form_after_title', 'dentix_move_metabox');
function dentix_move_metabox() 
{
	global $post, $wp_meta_boxes;
	if ( 'patient' === get_current_screen()->id ) 
	{
		do_meta_boxes(get_current_screen(), 'advanced', $post);
		unset($wp_meta_boxes['patient']['advanced']);
	}
}

add_action( 'posts_where_request', 'dentix_fields_search' );
function dentix_fields_search($where) 
{
	if ( is_search() ) 
	{
		global $wpdb, $wp;
		// Dump value of $where to check preg_replace pattern
		// print_r( $where );
		$where = preg_replace(
			"/($wpdb->posts.post_title LIKE '%{$wp->query_vars['s']}%')/i", 
			"$0 OR ($wpdb->postmeta.meta_value LIKE '%{$wp->query_vars['s']}%')", 
			$where 
		); 
	add_filter( 'posts_distinct_request', 'dentix_search_distinct' );
	add_filter( 'posts_join_request', 'dentix_search_join' );
	}
	return $where;
}

function dentix_search_join( $join ) 
{
	global $wpdb;
	return $join .= " LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
}

function dentix_search_distinct( $distinct ) 
{
	$distinct = "DISTINCT";
	return $distinct;
}

add_filter( 'manage_edit-patient_columns', 'edit_patient_columns' ) ;
function edit_patient_columns($columns) 
{
	$new = array ();
	foreach($columns as $key => $title) 
	{
	if($key =='date')
		$new['address'] = 'Address';
		$new [$key] = $title;
	}
	return $new;
}

add_action( 'manage_patient_posts_custom_column', 'manage_patient_columns', 10, 2 );
function manage_patient_columns( $column, $post_id ) 
{
	// Get meta if exists
	$address = get_post_meta( $post_id, 'address', true );
	$phone_number = get_post_meta( $post_id, 'phone_number', true );
	switch( $column ) 
	{
		case 'address' :
		if ( empty( $address ) ) 
		{
			echo __( 'Unknown' );
		} else {
			echo $address;
		}
		break;
		case 'phone_number' :
		if ( empty( $phone_number ) ) 
		{
			echo __( 'Unknown' );
		} else {
			echo $phone_number;
		}
		break;
	}
}

add_filter( 'manage_edit-patient_sortable_columns', 'patient_sortable_columns' );
function patient_sortable_columns( $columns ) 
{
	$columns['address'] = 'address'; 
	$columns['phone_number'] = 'phone_number'; 
	return $columns;
}

add_filter('the_title', 'patient_full_name',10, 2);
function patient_full_name($title, $id) 
{
	if('patient' == get_post_type($id)) 
	{
		return get_post_meta( $id, 'full_name', true );
	} else {
		return $title;
	}
}

add_filter('manage_patient_posts_columns' , 'set_patient_columns');
function set_patient_columns($columns) 
{
	return array(
		'cb' => '<input type="checkbox" />',
		'title' => __('Full Name'),
		'address' => __('Address'),
		'phone_number' => __('Phone Number'),
		'date' => __('Date')
	);
}

add_filter('login_redirect', 'dentix_login_redirect', 10, 3 );
function dentix_login_redirect( $url, $request, $user )
{
	if( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) 
	{
		if( $user->has_cap( 'edit_patients' ) ) 
		{
			$url = admin_url('index.php');
		} else {
			$url = admin_url();
		}
	}
	return $url;
}

add_filter( 'post_row_actions', 'remove_row_actions', 10, 2 );
function remove_row_actions( $actions, $post ) 
{
	global $current_screen;
	if( $current_screen->post_type != 'patient' ) return $actions;
		//unset( $actions['edit'] );
		//unset( $actions['view'] );
		unset( $actions['trash'] );
		unset( $actions['inline hide-if-no-js'] );
		//$actions['inline hide-if-no-js'] .= __( '' );
	return $actions;
}

add_action('wp_before_admin_bar_render', 'dentix_admin_tool_bar', 999);
function dentix_admin_tool_bar ($wp_admin_bar) 
{
	global $wp_admin_bar;
	$post_type = 'patient';
	$count = wp_count_posts ($post_type);
	$args = array (
		'id' => 'mbe_testimonials_pending',
		'href' => admin_url('/edit.php?post_status=pending&post_type='.$post_type, 'http'), 
		'parent' => 'top-secondary' 
	); 
	if ($count->pending == 1) 
	{
		$title = ' Patient Quewe';
	} else {
		$title = ' Patients Quewe';
	}
	$args['meta']['title'] = $title; 
	if($count->pending == 0) 
	{
		$display = '<span>'.$count->pending.' '.$title.'</span>'; 
	} else {
        	$display = '<span>'.$count->pending.'</span><span>'.$title.'</span>'; 
	}
	$args['title'] = $display;
	$wp_admin_bar->add_node($args);
}

add_filter( 'add_menu_classes', 'dentix_show_pending_number');
function dentix_show_pending_number( $menu ) 
{
	$type = "patient";
	$status = "pending";
	$num_posts = wp_count_posts( $type, 'readable' );
	$pending_count = 0;
	if ( !empty($num_posts->$status) )
	$pending_count = $num_posts->$status;
	// build string to match in $menu array
	if ($type == 'post') 
	{
		$menu_str = 'edit.php'; 
		// support custom post types
	} else {
		$menu_str = 'edit.php?post_type=' . $type;
	}
	// loop through $menu items, find match, add indicator
	foreach( $menu as $menu_key => $menu_data ) 
	{
		if( $menu_str != $menu_data[2] )
		continue;
		$menu[$menu_key][0] .= " <span class='update-plugins count-$pending_count'><span class='plugin-count'>" . number_format_i18n($pending_count) . '</span></span>';
	}
	return $menu;
}

add_filter( 'template_include', 'dentix_template', 1 );
function dentix_template( $template_path ) 
{
	if ( get_post_type() == 'patient' ) 
	{
        	if ( is_single() ) 
        	{
        		$template_path = plugin_dir_path( __FILE__ ) . '/template/single-patient.php';
        	}
        	if ( is_archive() ) 
        	{
        		$template_path = plugin_dir_path( __FILE__ ) . '/template/archive-patient.php';
        	}
	}
	return $template_path;
}

add_filter( 'wp_insert_post_data' , 'patient_set_title' , '99', 2 );
function patient_set_title( $data , $postarr ) 
{
	if( $data[ 'post_type' ] === 'patient' ) 
	{
        	$registration_number = ( ! empty( $_POST[ 'registration_number' ] ) ) ? $_POST[ 'registration_number' ] : get_post_meta( $postarr[ 'ID' ], 'registration_number', true );
        	if( $registration_number !== '' ) 
        	{
            		$data[ 'post_title' ] = $registration_number;
        		$data[ 'post_name' ]  = sanitize_title( sanitize_title_with_dashes( $registration_number, '', 'save' ) );
        	}
    	}
    	return $data;
}

add_filter( 'wp_insert_post_data' , 'appointment_set_title' , '99', 2 );
function appointment_set_title( $data , $postarr ) 
{
	if( $data[ 'post_type' ] === 'appointment' ) 
	{
        	$full_name = ( ! empty( $_POST[ 'full_name' ] ) ) ? $_POST[ 'full_name' ] : get_post_meta( $postarr[ 'ID' ], 'full_name', true );
        	if( $full_name !== '' ) 
        	{
            		$data[ 'post_title' ] = $full_name;
        		$data[ 'post_name' ]  = sanitize_title( sanitize_title_with_dashes( $full_name, '', 'save' ) );
        	}
    	}
    	return $data;
}

add_filter('manage_edit-appointment_columns', 'p2p2_add_appointment_columns');

function p2p2_add_appointment_columns($columns){
    $new_columns['cb'] = '<input type="checkbox" />';

    $new_columns['title'] = _x('Date', 'column name', 'dentix');

    $new_columns['p2p2_patient'] = __('Patient', 'dentix');
        
    $new_columns['p2p2_complaint'] = __('Complaint', 'dentix');

    return $new_columns;
}

add_action('manage_appointment_posts_custom_column', 'p2p2_fill_appointment_columns', 10, 2);

function p2p2_fill_appointment_columns($column_name, $id) {
    global $wpdb;
    switch ($column_name) {
        case 'p2p2_patient':
            $patient_id = get_post_meta($id, 'full_name', true);
            $patients = get_post($patient_id);
            $permalink = get_permalink($patient_id);
	        $get_the_title = get_the_title($patient_id);
            echo "<a href='" . $permalink . "'>" . $get_the_title . "</a>";
           break;
        case 'p2p2_date':
            $appointment_date = get_post_meta($id, 'appointment_date', true);
            echo $appointment_date;
           break;
        case 'p2p2_complaint':
            $main_complaint = get_post_meta($id, 'main_complaint', true);
            echo $main_complaint;
           break;
        default:
            break;
    } // end switch
}

add_filter('the_title', 'appointment_date',10, 2);
function appointment_date($title, $id) 
{
	if('appointment' == get_post_type($id)) 
	{
		return get_post_meta( $id, 'appointment_date', true );
	} else {
		return $title;
	}
}

add_filter( 'post_row_actions', 'appointment_remove_row_actions', 10, 2 );
function appointment_remove_row_actions( $actions, $post ) 
{
	global $current_screen;
	if( $current_screen->post_type != 'appointment' ) return $actions;
		unset( $actions['edit'] );
		unset( $actions['view'] );
		//unset( $actions['trash'] );
		unset( $actions['inline hide-if-no-js'] );
		//$actions['inline hide-if-no-js'] .= __( '' );
	return $actions;
}