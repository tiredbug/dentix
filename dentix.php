<?php
/*
Plugin Name: Dentix
Plugin URI: http://basoro.org/dentix 
Description: A simple wordpress plugin for electronic dental records 
Version: 3.7
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
			require_once(sprintf('%s/admin/patient-post-type.php', dirname(__FILE__)));
			$Dentix_Patient = new Dentix_Patient();

                        // Register functions
                        require_once(sprintf('%s/functions.php', dirname(__FILE__)));

                        // Register widgets metabox
                        require_once(sprintf('%s/widgets.php', dirname(__FILE__)));

			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ));
			add_action('admin_print_scripts', array( $this, 'dentix_scripts' ));

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
