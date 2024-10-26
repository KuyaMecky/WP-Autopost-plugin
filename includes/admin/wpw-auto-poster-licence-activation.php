<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * @package Social Auto Poster
 * @since 2.3.4
 */
class Wpw_Auto_Posting_license {

	public function wpw_auto_poster_plugin_activation_form() {		
		if ( ! wpw_auto_poster_is_lincense_activated() ) {
			add_menu_page(
				esc_html__( 'Social Auto Poster', 'wpwautoposter' ),
				esc_html__( 'Social Auto Poster', 'wpwautoposter' ),
				'manage_options',
				'wpw-auto-poster-settings',
				array( $this, 'wpw_auto_poster_page_callback' ),
				WPW_AUTO_POSTER_IMG_URL . '/wpweb-menu-icon-white.png',
				99
			);
			// We don't need to have a link for the parent in the submenu, so this overwrites it
			// However, this will leave an empty link item that's still visible due to padding
			add_submenu_page(
				'wpw-auto-poster-settings',
				'',
				'',
				'manage_options',
				'wpw-auto-poster-settings',
				array( $this, 'wpw_auto_poster_page_callback' ),
			);

			// This gets rid of the submenu item that overwrites the parent
			// This effectively removes the parent link in the submenu
			remove_submenu_page( 'wpw-auto-poster-settings', 'wpw-auto-poster-settings' );
		}
		add_submenu_page( 'wpw-auto-poster-settings', 'License', 'License','manage_options','wpw-auto-poster-license', array($this, 'wpw_auto_poster_page_callback'));
	}

	public function wpw_auto_poster_page_callback() {
		require WPW_AUTO_POSTER_ADMIN . '/forms/wpw-auto-poster-licence-form.php';
	}

	public function wpw_auto_poster_activate_license_callback() {

		// Verify nonce
        if ( !isset( $_POST['wpw_license_nonce'] ) || !wp_verify_nonce( $_POST['wpw_license_nonce'], 'wpw_auto_poster_verify_license_nonce' ) ) {
            $response = array(
                'type' => 'error',
                'message' => 'Invalid nonce'
                );
            wp_send_json($response);
            exit;
        }

        // Check user permissions
        $allowed_roles = apply_filters('wpw_auto_poster_allowed_roles', array('administrator'));

        foreach ($allowed_roles as $role) {
            if (!current_user_can($role)) {
                $response = array(
                    'type' => 'error',
                    'message' => 'Invalid user'
                    );
                wp_send_json($response);
                exit;
            }
        }

		$license_key    = $_POST['license_key'];
		$email          = $_POST['email'];
		$license_action = $_POST['license_action'];

		if ( $license_action == 'Activate License' ) {
			$data = $this->wpw_auto_poster_render_activation_settings( $license_key, $email, $license_action );
			if ( isset( $data['status'] ) && true == $data['status'] ) {				
				update_option( 'wpw_auto_poster_activation_code', $license_key, false );
				update_option( 'wpw_auto_poster_email_address', $email, false );
				$final_activation_code =  base64_encode( $license_key. '%' . $email );
				update_option( 'wpw_auto_poster_activated', $final_activation_code, false );
				delete_option( 'wpw_auto_poster_verification_fail' );
			}
			wp_send_json( $data );
		}

		if ( $license_action == 'Deactivate License' ) {
			$license_key = get_option( 'wpw_auto_poster_activation_code' );
			$data = $this->wpw_auto_poster_render_activation_settings( $license_key, $email, $license_action );
			if ( true == $data['status'] ) {
				delete_option( 'wpw_auto_poster_activated' );
				delete_option( 'wpw_auto_poster_activation_code' );
				delete_option( 'wpw_auto_poster_email_address' );
				delete_option( 'wpw_auto_poster_verification_fail' );
			}
			wp_send_json( $data );
		}
	}

	public function wpw_auto_poster_verify_license() {

        $license_key = get_option( 'wpw_auto_poster_activation_code' );
        $email 		 = get_option( 'wpw_auto_poster_email_address' );
        $data 		 = $this->wpw_auto_poster_render_activation_settings( $license_key, $email, 'Verify License' ); 
        if( isset($data['status']) && $data['status'] != 1 ){
            delete_option( 'wpw_auto_poster_activated' ); 
            update_option( 'wpw_auto_poster_verification_fail', $data['msg'], false );
        }

    }

	public function wpw_auto_poster_render_activation_settings( $license_key, $email, $license_action ) {
		$activation_code = $license_key;
		$email_address   = $email;
		$url             = WPW_AUTO_POSTER_LICENSE_VALIDATOR;
		$curl            = curl_init();
		$fields          = array(
			'email'           => $email_address,
			'site_url'        => get_site_url(),
			'activation_code' => $activation_code,
			'activation'      => $license_action,
			'version'      	  => WPW_AUTO_POSTER_VERSION,
			'item_id'     	  => 5754169,
		);
		$fields_string   = http_build_query( $fields );
		curl_setopt( $curl, CURLOPT_URL, $url );	
		curl_setopt( $curl, CURLOPT_HEADER, false );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $fields_string );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		$data = json_decode( curl_exec( $curl ), true );
		return( $data );
	}

	public function wpw_auto_poster_enqueue_license_script( $hook ) {
		if( 'social-auto-poster_page_wpw-auto-poster-license' === $hook || 'toplevel_page_wpw-auto-poster-settings' === $hook ) {		
			wp_enqueue_script( 'wpw-auto-poster-admin-license-script', WPW_AUTO_POSTER_URL . 'includes/js/wpw-auto-poster-admin-license.js', array( 'jquery' ), WPW_AUTO_POSTER_VERSION );
			// Localize script
            wp_localize_script('wpw-auto-poster-admin-license-script', 'WpwAutoPosterLicenseScript', array(
                'wpw_license_nonce' => wp_create_nonce( 'wpw_auto_poster_verify_license_nonce' ),
            ));

			wp_enqueue_script( 'sweetalert-script', WPW_AUTO_POSTER_URL . 'includes/js/sweetalert2.all.min.js', array( 'jquery' ), WPW_AUTO_POSTER_VERSION );
			wp_enqueue_style( 'wpw-auto-poster-admin-license', WPW_AUTO_POSTER_URL . 'includes/css/wpw-auto-poster-admin-license.css', array(), WPW_AUTO_POSTER_VERSION );
		}
	}

	public function wpw_auto_poster_show_license_notice() {
		
		if( ! wpw_auto_poster_is_lincense_activated() ) { 			
			if( function_exists('get_current_screen') ) {
				//get the current screen
				$screen = get_current_screen();
				$wpw_auto_poster_verification_fail = get_option( 'wpw_auto_poster_verification_fail' );
				if( $screen->id !== 'social-auto-poster_page_wpw-auto-poster-license' ) { ?>
					<div class="notice notice-error is-dismissible">
						<p><?php 
						$license_page_url = add_query_arg(array('page'=> 'wpw-auto-poster-license'), admin_url( 'admin.php' ) );
						printf( esc_html__( '%sSocial Auto Poster%s: Please %sactivate%s your license in order to use the plugin.', 'wpwautoposter' ), '<b>', '</b>', '<a href="' . $license_page_url . '">', '</a>' ); ?></p>
					</div>
					<?php

					if( !empty( $wpw_auto_poster_verification_fail ) ){ ?>
						<div class="notice notice-error is-dismissible">
							<p><?php echo $wpw_auto_poster_verification_fail; ?></p>
						</div>
					<?php }
				}
			}
		}
	}

	public function wpw_auto_poster_plugin_update_action( $upgrader_object, $options ) {
	    if ( $options['action'] == 'update' && $options['type'] == 'plugin' ) {
	        $updated_plugins = $options['plugins'];
	        if ( in_array('social-auto-poster/social-auto-poster.php', $updated_plugins ) ) {
				if( wpw_auto_poster_is_license_activated() ){
		            $wpw_auto_poster_license = new Wpw_Auto_Posting_license();
					$wpw_auto_poster_license->wpw_auto_poster_verify_license();
				}
	        }
	    }
	}

	public function add_hooks() {
		add_action( 'admin_notices', array( $this, 'wpw_auto_poster_show_license_notice' ) );
		add_action( 'admin_menu', array( $this, 'wpw_auto_poster_plugin_activation_form' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wpw_auto_poster_enqueue_license_script' ) );
		add_action( 'wp_ajax_wpw_auto_poster_activate_license', array( $this, 'wpw_auto_poster_activate_license_callback' ) );

		add_action('upgrader_process_complete', array( $this, 'wpw_auto_poster_plugin_update_action' ), 10, 2);
	}
}
