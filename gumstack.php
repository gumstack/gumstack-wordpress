<?php
	/**
	 * Plugin Name: Gumstack
	 * Plugin URI: https://www.gumstack.com/
	 * Description: Live audio/video calls with customers on your websites or apps. This plugin allows admins to install Gumstack on any WordPress site.
	 * Version: 0.1.0
	 * Author: Gumstack
	 * Author URI: https://www.gumstack.com
	 *
	 * Copyright: © 2021 Gumstack.
	 */
	
	/**
	* Insert Headers and Footers Class
	*/
	class Gumstack {
	        /**
	        * Constructor
	        */
	        public function __construct() {
	                $file_data = get_file_data( __FILE__, array( 'Version' => 'Version' ) );
	
	                // Plugin Details
	                $this->plugin                           = new stdClass;
	                $this->plugin->name                     = 'gumstack'; // Plugin Folder
	                $this->plugin->displayName              = 'Gumstack'; // Plugin Name
	                $this->plugin->version                  = $file_data['Version'];
	                $this->plugin->folder                   = plugin_dir_path( __FILE__ );
	                $this->plugin->url                      = plugin_dir_url( __FILE__ );
	                $this->plugin->db_welcome_dismissed_key = $this->plugin->name . '_welcome_dismissed_key';
	                $this->body_open_supported              = function_exists( 'wp_body_open' ) && version_compare( get_bloginfo( 'version' ), '5.2', '>=' );
	
	                // Hooks
	                add_action( 'admin_init', array( &$this, 'registerSettings' ) );
	                add_action( 'admin_menu', array( &$this, 'adminPanelsAndMetaBoxes' ) );
	                add_action( 'admin_notices', array( &$this, 'dashboardNotices' ) );
	                add_action( 'wp_ajax_' . $this->plugin->name . '_dismiss_dashboard_notices', array( &$this, 'dismissDashboardNotices' ) );
	
	                // Frontend Hooks
	                add_action( 'wp_head', array( &$this, 'insertGumstackScript' ) );
	        }
	
	        /**
	         * Show relevant notices for the plugin
	         */
	        function dashboardNotices() {
	                global $pagenow;
	
	                if (
	                        ! get_option( $this->plugin->db_welcome_dismissed_key )
	                        && current_user_can( 'manage_options' )
	                ) {
	                        if ( ! ( 'options-general.php' === $pagenow && isset( $_GET['page'] ) && 'gumstack' === $_GET['page'] ) ) {
	                                $setting_page = admin_url( 'options-general.php?page=' . $this->plugin->name );
	                                // load the notices view
	                                include_once( $this->plugin->folder . 'views/dashboard-notices.php' );
	                        }
	                }
	        }
	
	        /**
	         * Dismiss the welcome notice for the plugin
	         */
	        function dismissDashboardNotices() {
	                check_ajax_referer( $this->plugin->name . '-nonce', 'nonce' );
	                // user has dismissed the welcome notice
	                update_option( $this->plugin->db_welcome_dismissed_key, 1 );
	                exit;
	        }
	
	        /**
	        * Register Settings
	        */
	        function registerSettings() {
	                register_setting( $this->plugin->name, 'gumstack_api_token', 'trim' );
	        }
	
	        /**
	        * Register the plugin settings panel
	        */
	        function adminPanelsAndMetaBoxes() {
	                add_submenu_page( 'options-general.php', $this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array( &$this, 'adminPanel' ) );
	        }
	
	        /**
	        * Output the Administration Panel
	        * Save POSTed data from the Administration Panel into a WordPress option
	        */
	        function adminPanel() {
	                /*
	                 * Only users with manage_options can access this page.
	                 *
	                 * The capability included in add_settings_page() means WP should deal
	                 * with this automatically but it never hurts to double check.
	                 */
	                if ( ! current_user_can( 'manage_options' ) ) {
	                        wp_die( __( 'Sorry, you are not allowed to access this page.', 'gumstack' ) );
	                }
	
	                // only users with `unfiltered_html` can edit scripts.
	                if ( ! current_user_can( 'unfiltered_html' ) ) {
	                        $this->errorMessage = '<p>' . __( 'Sorry, only have read-only access to this page. Ask your administrator for assistance editing.', 'gumstack' ) . '</p>';
	                }
	
	                // Save Settings
	                if ( isset( $_REQUEST['submit'] ) ) {
	                        // Check permissions and nonce.
	                        if ( ! current_user_can( 'unfiltered_html' ) ) {
	                                // Can not edit scripts.
	                                wp_die( __( 'Sorry, you are not allowed to edit this page.', 'gumstack' ) );
	                        } elseif ( ! isset( $_REQUEST[ $this->plugin->name . '_nonce' ] ) ) {
	                                // Missing nonce
	                                $this->errorMessage = __( 'nonce field is missing. Settings NOT saved.', 'gumstack' );
	                        } elseif ( ! wp_verify_nonce( $_REQUEST[ $this->plugin->name . '_nonce' ], $this->plugin->name ) ) {
	                                // Invalid nonce
	                                $this->errorMessage = __( 'Invalid nonce specified. Settings NOT saved.', 'gumstack' );
	                        } else {
	                                // Save
	                                // $_REQUEST has already been slashed by wp_magic_quotes in wp-settings
	                                // so do nothing before saving
	                                update_option( 'gumstack_api_token', $_REQUEST['gumstack_api_token'] );
	                                update_option( $this->plugin->db_welcome_dismissed_key, 1 );
	                                $this->message = __( 'Settings Saved.', 'gumstack' );
	                        }
	                }
	
	                // Get latest settings
	                $this->settings = array(
	                        'gumstack_api_token' => esc_html( wp_unslash( get_option( 'gumstack_api_token' ) ) )
	                );
	
	                // Load Settings Form
	                include_once( $this->plugin->folder . 'views/settings.php' );
	        }
	
	        /**
	        * Outputs script / CSS to the frontend header
	        */
	        function insertGumstackScript() {
	                $this->outputScript();
	        }
	
	        function outputScript() {
                // Ignore admin, feed, robots or trackbacks
                if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
                        return;
                }
                $token = get_option( 'gumstack_api_token' );
                if ( empty( $token ) ) {
                        return;
                }
                if ( trim( $token ) === '' ) {
                        return;
                }

                // Output
                wp_register_script( 'Gumstack', 'https://w.gumstack.com/apiembed/' . $token . '.js', null, null, true );
                wp_enqueue_script('Gumstack');

	        }
	}
	
	$gumstack = new Gumstack();