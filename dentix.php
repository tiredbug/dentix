<?php
/*
Plugin Name: Dentix
Plugin URI: http://basoro.org/dentix 
Description: A simple wordpress plugin for electronic dental records 
Version: 3.3
Author: Faisol Basoro 
Author URI: http://basoro.org 
Text Domain: dentix 
Domain Path: /languages/
License: GPL2
*/

/*
Copyright 2015  Faisol Basoro  (email : drg.faisol@basoro.org)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(!class_exists('Dentix'))
{
	class Dentix
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// Initialize Settings
			require_once(sprintf("%s/updater.php", dirname(__FILE__)));
			$Dentix_Updater = new Dentix_Updater(__FILE__);
			$Dentix_Updater->set_username( 'basoro' );
			$Dentix_Updater->set_repository( 'dentix' );
			$Dentix_Updater->initialize();

			// Initialize Settings
			require_once(sprintf('%s/settings.php', dirname(__FILE__)));
			$Dentix_Settings = new Dentix_Settings();

			// Register custom post types
			require_once(sprintf('%s/admin/post-type.php', dirname(__FILE__)));
			$Dentix_Patient = new Dentix_Patient();

                        // Register treatments metabox
                        require_once(sprintf('%s/treatments.php', dirname(__FILE__)));

                        // Register widgets metabox
                        require_once(sprintf('%s/widgets.php', dirname(__FILE__)));

			$plugin = plugin_basename(__FILE__);
			add_filter('plugin_action_links_$plugin', array( $this, 'plugin_settings_link' ));
			add_action('admin_print_scripts', array( $this, 'dentix_scripts' ));

			add_action('edit_form_after_title', array( $this,  'dentix_move_metabox'));

		} // END public function __construct

		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
			// Do nothing
		} // END public static function activate

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
			// Do nothing
		} // END public static function deactivate

		// Add the settings link to the plugins page
		function plugin_settings_link($links)
		{
			$settings_link = '<a href="options-general.php?page=dentix_settings">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}

		function dentix_scripts() 
		{
			if ( 'patient' === get_current_screen()->id ) 
			{

				wp_enqueue_style( 'jquery-ui-style', plugins_url( 'assets/css/jquery-ui.min.css', __FILE__ ));
				wp_enqueue_style( 'jquery-datepicker-css', plugins_url( 'assets/css/jquery.datePicker.css', __FILE__ ));
				wp_enqueue_style( 'dentix-css', plugins_url( 'assets/css/dentix.css', __FILE__ ));

				wp_enqueue_media();
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-datepicker' );

				wp_enqueue_script( 'jquery-colorpicker-js', plugins_url( 'assets/js/jquery.colorPicker.js', __FILE__ ), array('jquery'), time(), false );
				wp_enqueue_script( 'dentix-js', plugins_url( 'assets/js/dentix.js', __FILE__ ), array('jquery'), time(), false );

			}
		}

		function dentix_move_metabox() 
		{
		global $post, $wp_meta_boxes;
			if ( 'patient' === get_current_screen()->id ) 
			{
				do_meta_boxes(get_current_screen(), 'advanced', $post);
				unset($wp_meta_boxes['patient']['advanced']);
			}
		}
		
	} // END class Dentix
} // END if(!class_exists('Dentix'))

if(class_exists('Dentix'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('Dentix', 'activate'));
	register_deactivation_hook(__FILE__, array('Dentix', 'deactivate'));

	// instantiate the plugin class
	$dentix = new Dentix();

}

add_action( 'posts_where_request', 'dentix_fields_search' );
function dentix_fields_search($where) {
	if ( is_search() ) {
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
function dentix_search_join( $join ) {
	global $wpdb;
	return $join .= " LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
}
function dentix_search_distinct( $distinct ) {
	$distinct = "DISTINCT";
	return $distinct;
}
add_filter( 'manage_edit-patient_columns', 'edit_patient_columns' ) ;
function edit_patient_columns($columns) {
	$new = array ();
	foreach($columns as $key => $title) {
	if($key =='date')
		$new['address'] = 'Address';
		$new [$key] = $title;
	}
	return $new;
}
add_action( 'manage_patient_posts_custom_column', 'manage_patient_columns', 10, 2 );
function manage_patient_columns( $column, $post_id ) {
	// Get meta if exists
	$address = get_post_meta( $post_id, 'address', true );
	$phone_number = get_post_meta( $post_id, 'phone_number', true );
	switch( $column ) {
	case 'address' :
		if ( empty( $address ) ) {
			echo __( 'Unknown' );
		} else {
			echo $address;
		}
	break;
	case 'phone_number' :
		if ( empty( $phone_number ) ) {
			echo __( 'Unknown' );
		} else {
			echo $phone_number;
		}
	break;
	}
}
add_filter( 'manage_edit-patient_sortable_columns', 'patient_sortable_columns' );
function patient_sortable_columns( $columns ) {
	$columns['address'] = 'address'; 
	$columns['phone_number'] = 'phone_number'; 
	return $columns;
}
add_filter('the_title', 'patient_full_name',10, 2);
function patient_full_name($title, $id) {
	if('patient' == get_post_type($id)) {
		return get_post_meta( $id, 'full_name', true );
	} else {
	return $title;
	}
}
function set_patient_columns($columns) {
	return array(
		'cb' => '<input type="checkbox" />',
		'title' => __('Full Name'),
		'address' => __('Address'),
		'phone_number' => __('Phone Number'),
		'date' => __('Date')
	);
}
add_filter('manage_patient_posts_columns' , 'set_patient_columns');
function my_login_redirect( $url, $request, $user ){
	if( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
		if( $user->has_cap( 'edit_patients' ) ) {
		$url = admin_url('index.php');
		} else {
			$url = admin_url();
		}
	}
	return $url;
}
add_filter('login_redirect', 'my_login_redirect', 10, 3 );
add_filter( 'post_row_actions', 'remove_row_actions', 10, 2 );
function remove_row_actions( $actions, $post ) {
	global $current_screen;
	if( $current_screen->post_type != 'patient' ) return $actions;
		//unset( $actions['edit'] );
		//unset( $actions['view'] );
		unset( $actions['trash'] );
		unset( $actions['inline hide-if-no-js'] );
		//$actions['inline hide-if-no-js'] .= __( '' );
	return $actions;
}
function admin_tool_bar ($wp_admin_bar) {
	global $wp_admin_bar;
	$post_type = 'patient';
	$count = wp_count_posts ($post_type);
	$args = array (
		'id' => 'mbe_testimonials_pending',
		'href' => admin_url('/edit.php?post_status=pending&post_type='.$post_type, 'http'), 
		'parent' => 'top-secondary' 
	); 
	if ($count->pending == 1) {
		$title = ' Patient Quewe';
	} else {
		$title = ' Patients Quewe';
	}
	$args['meta']['title'] = $title; 
	if($count->pending == 0) {
		$display = '<span>'.$count->pending.' '.$title.'</span>'; 
	} else {
        	$display = '<span>'.$count->pending.'</span><span>'.$title.'</span>'; 
	}
	$args['title'] = $display;
	$wp_admin_bar->add_node($args);
}
add_action('wp_before_admin_bar_render', 'admin_tool_bar', 999);
add_filter( 'add_menu_classes', 'show_pending_number');
function show_pending_number( $menu ) {
	$type = "patient";
	$status = "pending";
	$num_posts = wp_count_posts( $type, 'readable' );
	$pending_count = 0;
	if ( !empty($num_posts->$status) )
	$pending_count = $num_posts->$status;
	// build string to match in $menu array
	if ($type == 'post') {
		$menu_str = 'edit.php'; 
	// support custom post types
	} else {
		$menu_str = 'edit.php?post_type=' . $type;
	}
	// loop through $menu items, find match, add indicator
	foreach( $menu as $menu_key => $menu_data ) {
		if( $menu_str != $menu_data[2] )
		continue;
		$menu[$menu_key][0] .= " <span class='update-plugins count-$pending_count'><span class='plugin-count'>" . number_format_i18n($pending_count) . '</span></span>';
	}
	return $menu;
}
function get_custom_post_type_template($single_template) {
     global $post;
     if ($post->post_type == 'patient') {
          $single_template = dirname( __FILE__ ) . '/patient.php';
     }
     return $single_template;
}
//add_filter( 'single_template', 'get_custom_post_type_template' );

add_filter( 'template_include', 'dentix_template', 1 );
// Load Template from themes
function dentix_template( $template_path ) {
    if ( get_post_type() == 'patient' ) {
        if ( is_single() ) {
            $template_path = plugin_dir_path( __FILE__ ) . '/template/single-patient.php';
        }
        if ( is_archive() ) {
            $template_path = plugin_dir_path( __FILE__ ) . '/template/archive-patient.php';
        }
    }
    return $template_path;
}

// func that is going to set our title of our customer magically
function patient_set_title( $data , $postarr ) {

    // We only care if it's our customer
    if( $data[ 'post_type' ] === 'patient' ) {

        // get the customer name from _POST or from post_meta
        $registration_number = ( ! empty( $_POST[ 'registration_number' ] ) ) ? $_POST[ 'registration_number' ] : get_post_meta( $postarr[ 'ID' ], 'registration_number', true );

        // if the name is not empty, we want to set the title
        if( $registration_number !== '' ) {

            // sanitize name for title
            $data[ 'post_title' ] = $registration_number;
            // sanitize the name for the slug
            $data[ 'post_name' ]  = sanitize_title( sanitize_title_with_dashes( $registration_number, '', 'save' ) );
        }
    }
    return $data;
}
add_filter( 'wp_insert_post_data' , 'patient_set_title' , '99', 2 );
