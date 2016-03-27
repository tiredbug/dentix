<?php
if(!class_exists('Dentix_Appointment'))
{
	/**
	 * A PostType class that provides additional meta fields
	 */
	class Dentix_Appointment
	{
		const POST_TYPE	= "appointment";
		private $_meta	= array(
			'full_name',
			'main_complaint',
			'appointment_date',
		);
		
    	/**
    	 * The Constructor
    	 */
    	public function __construct()
    	{
    		// register actions
    		add_action('init', array(&$this, 'init'));
    		add_action('admin_init', array(&$this, 'admin_init'));
            add_action('edit_form_after_title', array(&$this, 'appointment_move_metabox' ));
    	} // END public function __construct()

    	/**
    	 * hook into WP's init action hook
    	 */
    	public function init()
    	{
    		// Initialize Post Type
    		$this->create_post_type();
    		add_action('save_post', array(&$this, 'save_post'));
    	} // END public function init()

    	/**
    	 * Create the post type
    	 */
    	public function create_post_type()
    	{
    		register_post_type(self::POST_TYPE,
    			array(
    				'labels' => array(
    					'name'               => _x( 'Appointments', 'post type general name', 'medical-records' ),
					    'singular_name'      => _x( 'Appointment', 'post type singular name', 'medical-records' ),
					    'menu_name'          => _x( 'Appointments', 'admin menu', 'medical-records' ),
					    'name_admin_bar'     => _x( 'Appointment', 'add new on admin bar', 'medical-records' ),
					    'add_new'            => _x( 'Add New', 'medical-records', 'medical-records' ),
					    'add_new_item'       => __( 'Add New Appointment', 'medical-records' ),
					    'new_item'           => __( 'New Appointment', 'medical-records' ),
					    'edit_item'          => __( 'Edit Appointment', 'medical-records' ),
					    'view_item'          => __( 'View Appointment', 'medical-records' ),
					    'all_items'          => __( 'All Appointments', 'medical-records' ),
					    'search_items'       => __( 'Search Appointments', 'medical-records' ),
					    'parent_item_colon'  => __( 'Parent Appointments:', 'medical-records' ),
					    'not_found'          => __( 'No appointments found.', 'medical-records' ),
					    'not_found_in_trash' => __( 'No appointments found in Trash.', 'medical-records' )
    				),
		        	'description'        => __( 'Description.', 'medical-records' ),
				    'public'             => true,
				    'publicly_queryable' => true,
				    'show_ui'            => true,
				    'show_in_menu'       => 'edit.php?post_type=patient',
				    'query_var'          => true,

'capability_type'    => array ('patient', 'patients'),       
        			'map_meta_cap' 	     => true,
				    'has_archive'        => true,
				    'hierarchical'       => false,
				    'menu_position'      => 5,
				    'menu_icon' 	     => 'dashicons-event',
    				'supports' => array(''),
    			)
    		);
    	}
	
    	/**
    	 * Save the metaboxes for this custom post type
    	 */
    	public function save_post($post_id)
    	{
            // verify if this is an auto save routine. 
            // If it is our form has not been submitted, so we dont want to do anything
            if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            {
                return;
            }
            
    		if(isset($_POST['post_type']) && $_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id))
    		{
    			foreach($this->_meta as $field_name)
    			{
    				// Update the post's meta field
    				update_post_meta($post_id, $field_name, $_POST[$field_name]);
    			}
    		}
    		else
    		{
    			return;
    		} // if($_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id))
    	} // END public function save_post($post_id)

    	/**
    	 * hook into WP's admin_init action hook
    	 */
    	public function admin_init()
    	{			
    		// Add metaboxes
    		add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
    	} // END public function admin_init()
			
    	/**
    	 * hook into WP's add_meta_boxes action hook
    	 */
    	public function add_meta_boxes()
    	{
    		// Add this metabox to every selected post
    		add_meta_box( 
    			sprintf('%s-metabox', ucwords(str_replace("_", " ", self::POST_TYPE))),
    			sprintf('%s Information', ucwords(str_replace("_", " ", self::POST_TYPE))),
    			array(&$this, 'add_inner_meta_boxes'),
    			self::POST_TYPE, 'advanced', 'high'
    	    );					
    	} // END public function add_meta_boxes()

		/**
		 * called off of the add meta box
		 */		
		public function add_inner_meta_boxes($post)
		{	
			// Render the job order metabox
			include(sprintf("%s/views/appointment.php", dirname(__FILE__)));			
		} // END public function add_inner_meta_boxes($post)


		public function appointment_move_metabox() 
		{
			global $post, $wp_meta_boxes;
			if ( 'appointment' === get_current_screen()->id ) 
			{
			    do_meta_boxes(get_current_screen(), 'advanced', $post);
			    unset($wp_meta_boxes['appointment']['advanced']);
			}
		}

	} // END class Dentix_Appointment
} // END if(!class_exists('Dentix_Appointment'))
