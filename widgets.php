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

 /**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function dentix_add_dashboard_widgets() {

	wp_add_dashboard_widget(
                 'dentix_dashboard_widget',         // Widget slug.
                 'Dentix Dashboard Widget',         // Title.
                 'dentix_dashboard_widget_function' // Display function.
        );	
}
add_action( 'wp_dashboard_setup', 'dentix_add_dashboard_widgets' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function dentix_dashboard_widget_function() {

	// Display whatever it is you want to show.
	echo '<h2>Dentix Statistics</h2>';
	echo "Hello World, I'm a great Dashboard Widget";
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
function my_dashboard_widget_function() {
	echo '<h2>Dentix Metabox Statistics</h2>';
	echo "Hello World, I'm a great Dashboard Widget";
}
