<?php

function remove_dashboard_meta() {
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
        //remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
}
add_action( 'admin_init', 'remove_dashboard_meta' );

function add_jsapi_Scripts() {
	global $wpdb;
	$male_sex_count = $wpdb->get_results("
		SELECT * 
    		FROM $wpdb->posts 
    		INNER JOIN $wpdb->postmeta 
    		ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
    		WHERE   $wpdb->posts.post_type = 'dentix' 
    		AND $wpdb->postmeta.meta_key = 'sex' 
    		AND $wpdb->postmeta.meta_value = 'Male' 
  	;"); 
   	$female_sex_count = $wpdb->get_results("
    		SELECT * 
    		FROM $wpdb->posts 
    		INNER JOIN $wpdb->postmeta 
    		ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
    		WHERE   $wpdb->posts.post_type = 'dentix' 
    		AND $wpdb->postmeta.meta_key = 'sex' 
    		AND $wpdb->postmeta.meta_value = 'Female' 
  	;"); 
  
	wp_enqueue_script(
		'google-jsapi',
		'//www.google.com/jsapi',
		array(),
		0,
		true
	);

	wp_enqueue_script(
		'jsapi',
		plugin_dir_url( __FILE__ ).'js/jsapi.js',
		array( 'google-jsapi', ),
		filemtime( plugin_dir_path( __FILE__ ).'js/jsapi.js' ),
		true
	);
	
   	$male_sex_count = count($male_sex_count);
      	$female_sex_count = count($female_sex_count);
   
	//$male_sex_count = print $male_sex_count;
	wp_localize_script(
		'jsapi',
		'jsapi',
		array( 'exampleData' => array(
			array( 'Sex',    'Persentase pasien berdasarkan jenis kelamin ', ),
			array( 'Male',     $male_sex_count, ),
			array( 'Female',      $female_sex_count, ),
			//array( 'Commute',  2, ),
			//array( 'Watch TV', 2, ),
			//array( 'Sleep',    7, ),
		) )
	);
}
add_action('admin_print_scripts', 'add_jsapi_Scripts' );

function dentix_add_dashboard_widgets() {

	wp_add_dashboard_widget(
                'dentix_dashboard_widget',         // Widget slug.
                'Dentix Dashboard Widget',         // Title.
                'dentix_dashboard_widget_function' // Display function.
        );	
}
add_action( 'wp_dashboard_setup', 'dentix_add_dashboard_widgets' );

function dentix_dashboard_widget_function() {

	echo '<h2>Dentix Statistics</h2>';
	global $wpdb;
	$sex_count = $wpdb->get_var("
    		SELECT COUNT(*) 
    		FROM $wpdb->posts 
    		INNER JOIN $wpdb->postmeta 
    		ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
    		WHERE   $wpdb->posts.post_type = 'dentix' 
    		AND $wpdb->postmeta.meta_key = 'sex' 
  	;"); 
	echo '<p>Sex count is ' . $sex_count . '</p>'; 
}

add_action( 'wp_dashboard_setup', 'my_dashboard_setup_function' );
function my_dashboard_setup_function() {
	add_meta_box( 'my_dashboard_widget', 'My Widget Name', 'my_dashboard_widget_function', 'dashboard', 'side', 'high' );
}
function my_dashboard_widget_function($data) {
	echo '<h2>Dentix Metabox Statistics</h2>';
	?><div id="piechart"></div><?php
}
?>
