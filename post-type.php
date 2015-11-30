<?php

function dentix_post_type_rewrite_flush() {
    // First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry, 
    // when you add a post of this CPT.
    dentix_post_type_init();

    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'dentix_post_type_rewrite_flush' );

add_action( 'init', 'dentix_post_type_init' );
/**
 * Register a book post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function dentix_post_type_init() {
	$labels = array(
		'name'               => _x( 'Dentixs', 'post type general name', 'dentix' ),
		'singular_name'      => _x( 'Dentix', 'post type singular name', 'dentix' ),
		'menu_name'          => _x( 'Dentixs', 'admin menu', 'dentix' ),
		'name_admin_bar'     => _x( 'Dentix', 'add new on admin bar', 'dentix' ),
		'add_new'            => _x( 'Add New', 'dentix', 'dentix' ),
		'add_new_item'       => __( 'Add New Dentix', 'dentix' ),
		'new_item'           => __( 'New Dentix', 'dentix' ),
		'edit_item'          => __( 'Edit Dentix', 'dentix' ),
		'view_item'          => __( 'View Dentix', 'dentix' ),
		'all_items'          => __( 'All Dentixs', 'dentix' ),
		'search_items'       => __( 'Search Dentixs', 'dentix' ),
		'parent_item_colon'  => __( 'Parent Dentixs:', 'dentix' ),
		'not_found'          => __( 'No dentixs found.', 'dentix' ),
		'not_found_in_trash' => __( 'No dentixs found in Trash.', 'dentix' )
	);

	$args = array(
		'labels'             => $labels,
        	'description'        => __( 'Description.', 'dentix' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'dentix' ),
        	'capability_type'    => array ('dentix', 'dentixs'),       
        	'map_meta_cap' 	     => true,
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'menu_icon' 	     => 'dashicons-clipboard',
		'supports'           => array( ''
			//'title', 
			//'editor', 
			//'author', 
			//'thumbnail', 
			//'excerpt', 
			//'comments' 
		)
	);

	register_post_type( 'dentix', $args );
}

function add_dentix_caps_to_admin() {
	global $wp_roles;
		if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
        //create a new role, based on the subscriber role 
        $subscriber = $wp_roles->get_role('subscriber');
        $wp_roles->add_role('dentist', __( 'Dentist', 'dentix' ), $subscriber->capabilities);

	$caps = array(
		'read',
		'read_dentix',
		'read_private_dentixs',
		'edit_dentixs',
		'edit_private_dentixs',
		'edit_published_dentixs',
		'edit_others_dentixs',
		'publish_dentixs',
		//'delete_dentixs',
		//'delete_private_dentixs',
		//'delete_published_dentixs',
		//'delete_others_dentixs',
		'upload_files',
	);

	$roles = array(
		get_role( 'administrator' ),
		get_role( 'dentist' ),
	);

	foreach ($roles as $role) {
		foreach ($caps as $cap) {
			$role->add_cap( $cap );
		}
	}
}
add_action( 'admin_init', 'add_dentix_caps_to_admin' );

add_filter( 'post_updated_messages', 'dentix_post_type_updated_messages' );
/**
 * Book update messages.
 *
 * See /wp-admin/edit-form-advanced.php
 *
 * @param array $messages Existing post update messages.
 *
 * @return array Amended post update messages with new CPT update messages.
 */
function dentix_post_type_updated_messages( $messages ) {
	$post             = get_post();
	$post_type        = get_post_type( $post );
	$post_type_object = get_post_type_object( $post_type );

	$messages['dentix'] = array(
		0  => '', // Unused. Messages start at index 1.
		1  => __( 'Dentix updated.', 'dentix' ),
		2  => __( 'Custom field updated.', 'dentix' ),
		3  => __( 'Custom field deleted.', 'dentix' ),
		4  => __( 'Dentix updated.', 'dentix' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Dentix restored to revision from %s', 'dentix' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6  => __( 'Dentix published.', 'dentix' ),
		7  => __( 'Dentix saved.', 'dentix' ),
		8  => __( 'Dentix submitted.', 'dentix' ),
		9  => sprintf(
			__( 'Dentix scheduled for: <strong>%1$s</strong>.', 'dentix' ),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i', 'dentix' ), strtotime( $post->post_date ) )
		),
		10 => __( 'Dentix draft updated.', 'dentix' )
	);

	if ( $post_type_object->publicly_queryable ) {
		$permalink = get_permalink( $post->ID );

		$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View dentix', 'dentix' ) );
		$messages[ $post_type ][1] .= $view_link;
		$messages[ $post_type ][6] .= $view_link;
		$messages[ $post_type ][9] .= $view_link;

		$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
		$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview dentix', 'dentix' ) );
		$messages[ $post_type ][8]  .= $preview_link;
		$messages[ $post_type ][10] .= $preview_link;
	}

	return $messages;
}

add_action( 'contextual_help', 'dentix_add_help_text', 10, 3 );
//display contextual help for Dentixs
function dentix_add_help_text( $contextual_help, $screen_id, $screen ) {
  //$contextual_help .= var_dump( $screen ); // use this to help determine $screen->id
  if ( 'dentix' == $screen->id ) {
    $contextual_help =
      '<p>' . __('Things to remember when adding or editing a Dentix:', 'dentix') . '</p>' .
      '<ul>' .
      '<li>' . __('Specify the correct field such as Name, or Address.', 'dentix') . '</li>' .
      '<li>' . __('Specify the correct phone number of the book.  Remember that the patient refers to you, the patient of this dentix.', 'dentix') . '</li>' .
      '</ul>' .
      '<p>' . __('If you want to schedule the dentix to be published in the future:', 'dentix') . '</p>' .
      '<ul>' .
      '<li>' . __('Under the Publish module, click on the Edit link next to Publish.', 'dentix') . '</li>' .
      '<li>' . __('Change the date to the date to actual publish this article, then click on Ok.', 'dentix') . '</li>' .
      '</ul>' .
      '<p><strong>' . __('For more information:', 'dentix') . '</strong></p>' .
      '<p>' . __('<a href="http://codex.wordpress.org/Posts_Edit_SubPanel" target="_blank">Edit Posts Documentation</a>', 'dentix') . '</p>' .
      '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>', 'dentix') . '</p>' ;
  } elseif ( 'edit-book' == $screen->id ) {
    $contextual_help =
      '<p>' . __('This is the help screen displaying the table of Dentixs blah blah blah.', 'dentix') . '</p>' ;
  }
  return $contextual_help;
}

add_action('admin_head', 'dentix_custom_help_tab');
function dentix_custom_help_tab() {

  $screen = get_current_screen();

  // Return early if we're not on the book post type.
  if ( 'dentix' != $screen->post_type )
    return;

  // Setup help tab args.
  $args = array(
    'id'      => 'dentix_help_tab_id', //unique id for the tab
    'title'   => 'Dentix Help', //unique visible title for the tab
    'content' => '<h3>Help Title</h3><p>Help content</p>',  //actual help text
  );
  
  // Add the help tab.
  $screen->add_help_tab( $args );

}
