<?php
if(!class_exists('Dentix_Patient'))
{
	/**
	 * A PostType class that provides additional meta fields
	 */
	class Dentix_Patient
	{
		const POST_TYPE	= "patient";
		private $_meta	= array(
			'registration_number', 'full_name', 'sex', 'birthdate', 'address', 'phone_number', 'occupation', 'marriage', 'images', 
			'gg_18', 'gg_17', 'gg_16', 'gg_15', 'gg_14', 'gg_13', 'gg_12', 'gg_11', 'gg_21', 'gg_22', 'gg_23', 'gg_24', 'gg_25', 'gg_26', 'gg_27', 'gg_28',
			'gg_48', 'gg_47', 'gg_46', 'gg_45', 'gg_44', 'gg_43', 'gg_42', 'gg_41', 'gg_31', 'gg_32', 'gg_33', 'gg_34', 'gg_35', 'gg_36', 'gg_37', 'gg_38',
			'gg_55', 'gg_54', 'gg_53', 'gg_52', 'gg_51', 'gg_61', 'gg_62', 'gg_63', 'gg_64', 'gg_65', 
			'gg_75', 'gg_74', 'gg_73', 'gg_72', 'gg_71', 'gg_81', 'gg_82', 'gg_83', 'gg_84', 'gg_85',
		);
		
    	/**
    	 * The Constructor
    	 */
    	public function __construct()
    	{
    		// register actions
    		add_action('init', array(&$this, 'init'));
    		add_action('admin_init', array(&$this, 'admin_init'));
			add_action('admin_init', array(&$this, 'add_dentix_caps_to_admin'));
			add_filter('post_updated_messages', array(&$this, 'dentix_post_type_updated_messages'));
			add_action('contextual_help', array(&$this, 'dentix_add_help_text'), 10, 3);
			add_action('admin_head', array(&$this, 'dentix_custom_help_tab'));

    	} // END public function __construct()

    	/**
    	 * hook into WP's init action hook
    	 */
    	public function init()
    	{
    		// Initialize Post Type
    		$this->create_post_type();
    		add_action('save_post', array(&$this, 'save_post'));
                add_action('save_post', array(&$this, 'treatments_metabox_save'));
    	} // END public function init()

    	/**
    	 * Create the post type
    	 */
    	public function create_post_type()
    	{
    		register_post_type(self::POST_TYPE,
    			array(
    				'labels' => array(
    					'name'               => _x( 'Patients', 'post type general name', 'dentix' ),
						'singular_name'      => _x( 'Patient', 'post type singular name', 'dentix' ),
						'menu_name'          => _x( 'Patients', 'admin menu', 'dentix' ),
						'name_admin_bar'     => _x( 'Patient', 'add new on admin bar', 'dentix' ),
						'add_new'            => _x( 'Add New', 'dentix', 'dentix' ),
						'add_new_item'       => __( 'Add New Patient', 'dentix' ),
						'new_item'           => __( 'New Patient', 'dentix' ),
						'edit_item'          => __( 'Edit Patient', 'dentix' ),
						'view_item'          => __( 'View Patient', 'dentix' ),
						'all_items'          => __( 'All Patients', 'dentix' ),
						'search_items'       => __( 'Search Patients', 'dentix' ),
						'parent_item_colon'  => __( 'Parent Patients:', 'dentix' ),
						'not_found'          => __( 'No patients found.', 'dentix' ),
						'not_found_in_trash' => __( 'No patients found in Trash.', 'dentix' )
    				),
		        	'description'        => __( 'Description.', 'dentix' ),
					'public'             => true,
					'publicly_queryable' => true,
					'show_ui'            => true,
					'show_in_menu'       => true,
					'query_var'          => true,
					'rewrite'            => array( 'slug' => 'dentix' ),
        			'capability_type'    => array ('patient', 'patients'),       
        			'map_meta_cap' 	     => true,
					'has_archive'        => true,
					'hierarchical'       => false,
					'menu_position'      => 5,
					'menu_icon' 	     => 'dashicons-clipboard',
    				'supports' => array(''),
    			)
    		);
    	}


		public function add_dentix_caps_to_admin() 
		{
			global $wp_roles;
			if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
				//create a new role, based on the subscriber role 
				$subscriber = $wp_roles->get_role('subscriber');
				$wp_roles->add_role('dentist', __( 'Dentist', 'dentix' ), $subscriber->capabilities);

			$caps = array(
				'read',
				'read_patient',
				'read_private_patients',
				'edit_patients',
				'edit_private_patients',
				'edit_published_patients',
				'edit_others_patients',
				'publish_patients',
				'delete_patients',
				'delete_private_patients',
				'delete_published_patients',
				'delete_others_patients',
				'upload_files',
			);

			$roles = array(
				get_role( 'administrator' ),
				get_role( 'dentist' ),
			);

			foreach ($roles as $role) 
			{
				foreach ($caps as $cap) 
				{
					$role->add_cap( $cap );
				}
			}
		}

		public function dentix_post_type_updated_messages( $messages ) 
		{
			$post             = get_post();
			$post_type        = get_post_type( $post );
			$post_type_object = get_post_type_object( $post_type );

			$messages['patient'] = array(
				0  => '', // Unused. Messages start at index 1.
				1  => __( 'Patient updated.', 'dentix' ),
				2  => __( 'Custom field updated.', 'dentix' ),
				3  => __( 'Custom field deleted.', 'dentix' ),
				4  => __( 'Patient updated.', 'dentix' ),
				/* translators: %s: date and time of the revision */
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'Patient restored to revision from %s', 'dentix' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				6  => __( 'Patient published.', 'dentix' ),
				7  => __( 'Patient saved.', 'dentix' ),
				8  => __( 'Patient submitted.', 'dentix' ),
				9  => sprintf(__( 'Patient scheduled for: <strong>%1$s</strong>.', 'dentix' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'dentix' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Patient draft updated.', 'dentix' )
			);

			if ( $post_type_object->publicly_queryable ) 
			{
				$permalink = get_permalink( $post->ID );

				$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View patient', 'dentix' ) );
				$messages[ $post_type ][1] .= $view_link;
				$messages[ $post_type ][6] .= $view_link;
				$messages[ $post_type ][9] .= $view_link;

				$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
				$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview patient', 'dentix' ) );
				$messages[ $post_type ][8]  .= $preview_link;
				$messages[ $post_type ][10] .= $preview_link;
			}

			return $messages;
		}

		public function dentix_add_help_text( $contextual_help, $screen_id, $screen ) 
		{
			if ( 'patient' == $screen->id ) 
			{
    			$contextual_help =
      			'<p>' . __('Things to remember when adding or editing a patient:', 'dentix') . '</p>' .
      			'<ul>' .
      			'<li>' . __('Specify the correct field such as Name, or Address.', 'dentix') . '</li>' .
      			'<li>' . __('Specify the correct phone number of the book.  Remember that the patient refers to you, the patient of this record.', 'dentix') . '</li>' .
      			'</ul>' .
      			'<p>' . __('If you want to schedule the patient to be published in the future:', 'dentix') . '</p>' .
      			'<ul>' .
      			'<li>' . __('Under the Publish module, click on the Edit link next to Publish.', 'dentix') . '</li>' .
      			'<li>' . __('Change the date to the date to actual publish this article, then click on Ok.', 'dentix') . '</li>' .
      			'</ul>' .
      			'<p><strong>' . __('For more information:', 'dentix') . '</strong></p>' .
      			'<p>' . __('<a href="http://codex.wordpress.org/Posts_Edit_SubPanel" target="_blank">Edit Posts Documentation</a>', 'dentix') . '</p>' .
      			'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>', 'dentix') . '</p>' ;
			} 
			elseif ( 'patient' == $screen->id ) 
			{
    			$contextual_help =
      			'<p>' . __('This is the help screen displaying the table of Dentixs blah blah blah.', 'dentix') . '</p>' ;
			}
			return $contextual_help;
		}

		public function dentix_custom_help_tab() 
		{

			$screen = get_current_screen();

			if ( 'patient' != $screen->post_type )
				return;

			$args = array(
    			'id'      => 'dentix_help_tab_id', //unique id for the tab
    			'title'   => 'Dentix Help', //unique visible title for the tab
    			'content' => '<h3>Help Title</h3><p>Help content</p>',  //actual help text
			);
  
			$screen->add_help_tab( $args );

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
    	
        public function treatments_metabox_save($post_id) {
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
    			sprintf('profile-metabox'),
    			sprintf('%s Profile Information', ucwords(self::POST_TYPE)),
    			array(&$this, 'profile_inner_meta_boxes'),
    			self::POST_TYPE, 'advanced', 'high'
    	    );					
    		add_meta_box( 
    			sprintf('odontogram-metabox'),
    			sprintf('%s Odontogram Information', ucwords(self::POST_TYPE)),
    			array(&$this, 'odontogram_inner_meta_boxes'),
    			self::POST_TYPE, 'advanced', 'high'
    	    );					

    		add_meta_box( 
    			sprintf('gallery-metabox'),
    			sprintf('%s Gallery Information', ucwords(self::POST_TYPE)),
    			array(&$this, 'gallery_inner_meta_boxes'),
    			self::POST_TYPE, 'advanced', 'high'
    	    );					

                add_meta_box(
                        sprintf('treatments-metabox'),
                        sprintf('%s Treatments Information', ucwords(self::POST_TYPE)),
                        array(&$this, 'treatments_inner_meta_boxes'),
                        self::POST_TYPE, 'advanced', 'high'
            );


    	} // END public function add_meta_boxes()

		/**
		 * called off of the add meta box
		 */		
		public function profile_inner_meta_boxes($post)
		{		
			// Render the job order metabox
			include(sprintf("%s/../views/profile.php", dirname(__FILE__)));			
		} // END public function add_inner_meta_boxes($post)

		public function odontogram_inner_meta_boxes($post)
		{		
			// Render the job order metabox
			include(sprintf("%s/../views/odontogram.php", dirname(__FILE__)));			
		} // END public function add_inner_meta_boxes($post)

		public function gallery_inner_meta_boxes($post)
		{		
			// Render the job order metabox
			include(sprintf("%s/../views/gallery.php", dirname(__FILE__)));			
		} // END public function add_inner_meta_boxes($post)

                public function treatments_inner_meta_boxes($post)
                {
                        // Render the job order metabox
                        include(sprintf("%s/../views/treatments.php", dirname(__FILE__)));
                } // END public function add_inner_meta_boxes($post)

	} // END class Dentix_Patient
} // END if(!class_exists('Dentix_Patient'))
