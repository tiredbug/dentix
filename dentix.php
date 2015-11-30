<?php
/*
 * Plugin Name: Dentix 
 * Plugin URI: http://basoro.org/dentix/
 * Description: A wordpress plugin containing Simple Dental Records.
 * Version: 0.1
 * Author: drg. F. Basoro
 * Author URI: http://basoro.org/
 * Text Domain: dentix 
 * Domain Path: /languages/
 * License: GNU 
 */

if( ! class_exists( 'Dentix_Updater' ) ){
	include_once( plugin_dir_path( __FILE__ ) . 'updater.php' );
}

$updater = new Dentix_Updater( __FILE__ );
$updater->set_username( 'basoro' );
$updater->set_repository( 'dentix' );
//$updater->authorize( 'abcdefghijk1234567890' ); // Your auth code goes here for private repos
$updater->initialize();

define( 'DENTIX_VERSION', '0.1' );
define( 'DENTIX_DB_VERSION', 1 );
define( 'DENTIX_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'DENTIX_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

include_once( DENTIX_DIR . 'widgets.php' );
include_once( DENTIX_DIR . 'settings.php' );
include_once( DENTIX_DIR . 'post-type.php' );
include_once( DENTIX_DIR . 'metabox.php' );
include_once( DENTIX_DIR . 'treatments.php' );

function load_dentix_textdomain() { 
load_plugin_textdomain( 'dentix', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' ); } 
add_action( 'plugins_loaded', 'load_dentix_textdomain' );

add_action('admin_print_scripts', 'dentix_scripts' );
function dentix_scripts() {
    if ( 'dentix' === get_current_screen()->id ) {

		wp_enqueue_style( 'jquery-ui-style', plugins_url( 'css/jquery-ui.min.css', __FILE__ ));
		wp_enqueue_style( 'jquery-datepicker-css', plugins_url( 'css/jquery.datePicker.css', __FILE__ ));
		wp_enqueue_style( 'dentix-css', plugins_url( 'css/dentix.css', __FILE__ ));

		wp_enqueue_media();
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );

		wp_enqueue_script( 'jquery-colorpicker-js', plugins_url( 'js/jquery.colorPicker.js', __FILE__ ), array('jquery'), time(), false );
		wp_enqueue_script( 'dentix-js', plugins_url( 'js/dentix.js', __FILE__ ), array('jquery'), time(), false );

    }
}

add_action('edit_form_after_title', 'dentix_move_metabox');
function dentix_move_metabox() {
    global $post, $wp_meta_boxes;
    do_meta_boxes(get_current_screen(), 'advanced', $post);
    unset($wp_meta_boxes['dentix']['advanced']);
}

add_filter( 'gettext', 'dentix_change_publish_button', 10, 2 );
function dentix_change_publish_button( $translation, $text ) {
    if ( 'dentix' == get_post_type())
        if ( $text == 'Draft' )
		return 'Antrian';
		if ( $text == 'Draft (%s)' )
		return 'Antrian (%s)';
		if ( $text == 'Publish' || $text == 'Update' )
		return 'Update Data';
		if ( $text == 'Published on: <b>%1$s</b>' )
		return 'Registered on: <b>%1$s</b>';
	return $translation;
}

add_filter( 'enter_title_here', 'dentix_default_title' );
function dentix_default_title( $title ){
     $screen = get_current_screen();
     if  ( $screen->post_type == 'dentix' ) {
          return 'Enter Registration Number';
     }
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

//add_filter( 'manage_edit-dentix_columns', 'edit_dentix_columns' ) ;
function edit_dentix_columns($columns) {
    $new = array ();
    foreach($columns as $key => $title) {
        if($key =='date')
            $new['address'] = 'Address';
        $new [$key] = $title;
    }
    return $new;
}

add_action( 'manage_dentix_posts_custom_column', 'manage_dentix_columns', 10, 2 );
function manage_dentix_columns( $column, $post_id ) {

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

add_filter( 'manage_edit-dentix_sortable_columns', 'dentix_sortable_columns' );
function dentix_sortable_columns( $columns ) {
    $columns['address'] = 'address';
    $columns['phone_number'] = 'phone_number';
    return $columns;
}

add_filter('the_title', 'dentix_full_name',10, 2);
function dentix_full_name($title, $id) {
  if('dentix' == get_post_type($id)) {
      return get_post_meta( $id, 'full_name', true );
   }
  else {
      return $title;
  }
}

function set_dentix_columns($columns) {
    return array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Full Name'),
        'address' => __('Address'),
        'phone_number' => __('Phone Number'),
        'date' => __('Date')
    );
}
add_filter('manage_dentix_posts_columns' , 'set_dentix_columns');

function dentix_glance_items( $items = array() ) {
    $post_types = array( 'dentix' );
    foreach( $post_types as $type ) {
        if( ! post_type_exists( $type ) ) continue;
        $num_posts = wp_count_posts( $type );
        if( $num_posts ) {
            $published = intval( $num_posts->publish + $num_posts->draft + $num_posts->pending );
            $post_type = get_post_type_object( $type );
            $text = _n( '%s ' . $post_type->labels->singular_name, '%s ' . $post_type->labels->name, $published );
            $text = sprintf( $text, number_format_i18n( $published ) );
            if ( current_user_can( $post_type->cap->edit_posts ) ) {
            $output = '<a href="edit.php?post_type=' . $post_type->name . '">' . $text . '</a>';
                echo '<li class="post-count ' . $post_type->name . '-count">' . $output . '</li>';
            } else {
            $output = '<span>' . $text . '</span>';
                echo '<li class="post-count ' . $post_type->name . '-count">' . $output . '</li>';
            }
        }
    }
    return $items;
}

add_filter( 'dashboard_glance_items', 'dentix_glance_items', 10, 1 );

/**
 * WordPress function for redirecting users on login based on user role
 */
function my_login_redirect( $url, $request, $user ){
    if( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
        if( $user->has_cap( 'edit_dentixs' ) ) {
            $url = admin_url('index.php');
        } else {
            $url = admin_url();
        }
    }
    return $url;
}

add_filter('login_redirect', 'my_login_redirect', 10, 3 );

/*------------------------------------------------------------------------------------
	remove quick edit for custom post type videos just to check if less mem consumption
------------------------------------------------------------------------------------*/
add_filter( 'post_row_actions', 'remove_row_actions', 10, 2 );
function remove_row_actions( $actions, $post )
{
  global $current_screen;
	if( $current_screen->post_type != 'dentix' ) return $actions;
	//unset( $actions['edit'] );
	//unset( $actions['view'] );
	unset( $actions['trash'] );
	unset( $actions['inline hide-if-no-js'] );
	//$actions['inline hide-if-no-js'] .= __( '' );

	return $actions;
}

?>