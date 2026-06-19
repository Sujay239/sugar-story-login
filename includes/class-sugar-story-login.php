<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 */
class Sugar_Story_Login {

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {
		// Require any other classes here
		// require_once SUGAR_STORY_LOGIN_PLUGIN_DIR . 'includes/class-sugar-story-login-admin.php';
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_plugin_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_public_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'template_redirect', array( $this, 'restrict_checkout' ) );
        // Guests can add items to cart. Redirection will happen at checkout instead.
        // add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'restrict_add_to_cart' ), 10, 3 );
        add_filter( 'woocommerce_locate_template', array( $this, 'override_login_template' ), 10, 3 );
        add_action( 'woocommerce_register_post', array( $this, 'validate_custom_registration_fields' ), 10, 3 );
        add_action( 'woocommerce_created_customer', array( $this, 'save_custom_registration_fields' ) );
        add_action( 'init', array( $this, 'handle_google_oauth_flow' ) );
        add_filter( 'woocommerce_login_redirect', array( $this, 'custom_login_redirect' ), 10, 2 );
        add_filter( 'woocommerce_registration_redirect', array( $this, 'custom_registration_redirect' ), 10, 2 );
	}

    /**
     * Enqueue Admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_sugar-story-login' !== $hook) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script( 'sugar-story-admin-script', SUGAR_STORY_LOGIN_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery', 'wp-color-picker'), SUGAR_STORY_LOGIN_VERSION, true );
    }

    /**
     * Register Admin Menu
     */
    public function add_plugin_admin_menu() {
        add_menu_page(
            get_bloginfo('name') . ' Login Settings',
            get_bloginfo('name') . ' Login',
            'manage_options',
            'sugar-story-login',
            array( $this, 'display_plugin_setup_page' ),
            'dashicons-admin-users',
            50
        );
    }

    /**
     * Register Settings
     */
    public function register_plugin_settings() {
        register_setting( 'sugar_story_login_options', 'sugar_story_primary_color' );
        register_setting( 'sugar_story_login_options', 'sugar_story_bg_color' );
        register_setting( 'sugar_story_login_options', 'sugar_story_image_1' );
        register_setting( 'sugar_story_login_options', 'sugar_story_image_2' );
        register_setting( 'sugar_story_login_options', 'sugar_story_image_3' );
        register_setting( 'sugar_story_login_options', 'sugar_story_image_4' );
        register_setting( 'sugar_story_login_options', 'sugar_story_google_client_id' );
        register_setting( 'sugar_story_login_options', 'sugar_story_google_client_secret' );
    }

    /**
     * Display Settings Page
     */
    public function display_plugin_setup_page() {
        require_once SUGAR_STORY_LOGIN_PLUGIN_DIR . 'includes/admin-settings-page.php';
    }

    /**
     * Enqueue custom styles for the login page.
     */
    public function enqueue_styles() {
        if ( is_account_page() && ! is_user_logged_in() ) {
            wp_enqueue_style( 'sugar-story-login-style', SUGAR_STORY_LOGIN_PLUGIN_URL . 'assets/css/login-style.css', array(), SUGAR_STORY_LOGIN_VERSION, 'all' );
            wp_enqueue_script( 'sugar-story-login-script', SUGAR_STORY_LOGIN_PLUGIN_URL . 'assets/js/login-script.js', array('jquery'), SUGAR_STORY_LOGIN_VERSION, true );
        }
    }

    /**
     * Restrict the checkout page for non-logged-in users.
     */
    public function restrict_checkout() {
        // Skip for REST API / AJAX requests (prevents 409 conflicts with WooCommerce Store API)
        if ( defined( 'REST_REQUEST' ) || wp_doing_ajax() || ( defined( 'WC_DOING_AJAX' ) && WC_DOING_AJAX ) ) {
            return;
        }

        if ( is_checkout() && ! is_user_logged_in() && ! is_wc_endpoint_url( 'order-pay' ) && ! is_wc_endpoint_url( 'order-received' ) ) {
            $redirect_url = add_query_arg( 'redirect_to', urlencode( wc_get_checkout_url() ), wc_get_page_permalink( 'myaccount' ) );
            wp_redirect( $redirect_url );
            exit;
        }
    }

    /**
     * Redirect after successful login.
     */
    public function custom_login_redirect( $redirect, $user ) {
        if ( isset( $_REQUEST['redirect_to'] ) ) {
            $redirect_to = esc_url_raw( wp_unslash( $_REQUEST['redirect_to'] ) );
            $redirect = wp_validate_redirect( $redirect_to, $redirect );
        }
        return $redirect;
    }

    /**
     * Redirect after successful registration.
     */
    public function custom_registration_redirect( $redirect, $user ) {
        if ( isset( $_REQUEST['redirect_to'] ) ) {
            $redirect_to = esc_url_raw( wp_unslash( $_REQUEST['redirect_to'] ) );
            $redirect = wp_validate_redirect( $redirect_to, $redirect );
        }
        return $redirect;
    }

    /**
     * Restrict adding items to cart for non-logged-in users.
     * (Deprecated: guest add-to-cart is now allowed)
     */
    public function restrict_add_to_cart( $passed, $product_id, $quantity ) {
        return $passed;
    }

    /**
     * Override the WooCommerce login template with our custom one.
     */
    public function override_login_template( $template, $template_name, $template_path ) {
        if ( 'myaccount/form-login.php' === $template_name ) {
            $custom_template = SUGAR_STORY_LOGIN_PLUGIN_DIR . 'templates/myaccount/form-login.php';
            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }
        return $template;
    }

    /**
     * Validate custom registration fields.
     */
    public function validate_custom_registration_fields( $username, $email, $validation_errors ) {
        // Only validate during actual form submissions, not programmatic registrations (e.g. Google OAuth)
        if ( empty( $_POST ) ) {
            return $validation_errors;
        }
        $full_name = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';
        if ( empty( $full_name ) ) {
            $validation_errors->add( 'full_name_error', __( '<strong>Error</strong>: Full Name is required.', 'sugar-story-login' ) );
        } elseif ( ! preg_match( '/^[a-zA-Z\s]+$/', $full_name ) ) {
            $validation_errors->add( 'full_name_invalid', __( '<strong>Error</strong>: Full Name can only contain letters and spaces.', 'sugar-story-login' ) );
        }
        
        if ( isset( $_POST['billing_phone'] ) && ! empty( $_POST['billing_phone'] ) ) {
            $phone = sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) );
            if ( ! preg_match( '/^\+?[0-9\s\-\(\)]+$/', $phone ) ) {
                $validation_errors->add( 'billing_phone_invalid', __( '<strong>Error</strong>: Please enter a valid mobile number.', 'sugar-story-login' ) );
            }
        }

        if ( isset( $_POST['password'] ) && isset( $_POST['password_confirm'] ) ) {
            $password = strval( wp_unslash( $_POST['password'] ) );
            $password_confirm = strval( wp_unslash( $_POST['password_confirm'] ) );
            
            if ( $password !== $password_confirm ) {
                $validation_errors->add( 'password_mismatch', __( '<strong>Error</strong>: Passwords do not match.', 'sugar-story-login' ) );
            }
        }
        
        return $validation_errors;
    }

    /**
     * Save customer name details using WooCommerce CRUD and WordPress APIs.
     */
    private function save_customer_name( $customer_id, $first_name, $last_name ) {
        if ( class_exists( 'WC_Customer' ) ) {
            try {
                $customer = new WC_Customer( $customer_id );
                $customer->set_first_name( $first_name );
                $customer->set_last_name( $last_name );
                $customer->set_billing_first_name( $first_name );
                $customer->set_billing_last_name( $last_name );
                $customer->set_shipping_first_name( $first_name );
                $customer->set_shipping_last_name( $last_name );
                $customer->save();
            } catch ( Exception $e ) {
                update_user_meta( $customer_id, 'first_name', $first_name );
                update_user_meta( $customer_id, 'last_name', $last_name );
                update_user_meta( $customer_id, 'billing_first_name', $first_name );
                update_user_meta( $customer_id, 'billing_last_name', $last_name );
                update_user_meta( $customer_id, 'shipping_first_name', $first_name );
                update_user_meta( $customer_id, 'shipping_last_name', $last_name );
            }
        } else {
            update_user_meta( $customer_id, 'first_name', $first_name );
            update_user_meta( $customer_id, 'last_name', $last_name );
            update_user_meta( $customer_id, 'billing_first_name', $first_name );
            update_user_meta( $customer_id, 'billing_last_name', $last_name );
            update_user_meta( $customer_id, 'shipping_first_name', $first_name );
            update_user_meta( $customer_id, 'shipping_last_name', $last_name );
        }

        wp_update_user( array(
            'ID'           => $customer_id,
            'first_name'   => $first_name,
            'last_name'    => $last_name,
            'display_name' => $first_name . ( $last_name ? ' ' . $last_name : '' ),
        ) );
    }

    /**
     * Save custom registration fields.
     */
    public function save_custom_registration_fields( $customer_id ) {
        if ( isset( $_POST['full_name'] ) ) {
            $full_name = sanitize_text_field( wp_unslash( $_POST['full_name'] ) );
            $name_parts = explode( ' ', $full_name, 2 );
            $first_name = strval( $name_parts[0] );
            $last_name  = isset( $name_parts[1] ) ? strval( $name_parts[1] ) : '';
            
            $this->save_customer_name( $customer_id, $first_name, $last_name );
        }
        
        if ( isset( $_POST['billing_phone'] ) && ! empty( $_POST['billing_phone'] ) ) {
            $phone = sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) );
            update_user_meta( $customer_id, 'billing_phone', strval( $phone ) );
        }
    }

	/**
	 * Get dynamic Google OAuth key based on site title.
	 */
	public static function get_google_auth_key() {
		$site_name = get_bloginfo( 'name' );
		if ( empty( $site_name ) ) {
			return 'sugar_story_google_auth';
		}
		$slug = sanitize_title( $site_name );
		$key = str_replace( '-', '_', $slug ) . '_google_auth';
		$key = preg_replace( '/[^a-z0-9_]/', '', strtolower( $key ) );
		return empty( $key ) ? 'sugar_story_google_auth' : $key;
	}

	/**
	 * Write debug log to plugin directory for OAuth troubleshooting.
	 */
	private function oauth_debug_log( $message ) {
		$log_file = SUGAR_STORY_LOGIN_PLUGIN_DIR . 'debug-oauth.log';
		$timestamp = date( 'Y-m-d H:i:s' );
		file_put_contents( $log_file, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX );
	}

	/**
	 * Handle Google OAuth 2.0 Flow.
	 */
	public function handle_google_oauth_flow() {
		$auth_key = self::get_google_auth_key();
		
		// Find which query param is present (support dynamic key + legacy fallback)
		$active_key = '';
		if ( isset( $_GET[ $auth_key ] ) ) {
			$active_key = $auth_key;
		} elseif ( isset( $_GET['sugar_story_google_auth'] ) ) {
			$active_key = 'sugar_story_google_auth';
		}

		if ( empty( $active_key ) ) {
			return;
		}

		$client_id     = get_option( 'sugar_story_google_client_id' );
		$client_secret = get_option( 'sugar_story_google_client_secret' );

		if ( empty( $client_id ) || empty( $client_secret ) ) {
			$this->oauth_debug_log( 'ABORT: client_id or client_secret is empty.' );
			return;
		}

		// IMPORTANT: Always use the SAME auth_key for redirect_uri in both login and callback.
		// This must match exactly what is registered in Google Cloud Console.
		$redirect_uri = site_url( '?' . $auth_key . '=callback' );

		$action = sanitize_text_field( $_GET[ $active_key ] );

		$this->oauth_debug_log( '--- NEW REQUEST ---' );
		$this->oauth_debug_log( 'Action: ' . $action );
		$this->oauth_debug_log( 'Active key: ' . $active_key );
		$this->oauth_debug_log( 'Auth key: ' . $auth_key );
		$this->oauth_debug_log( 'Redirect URI: ' . $redirect_uri );
		$this->oauth_debug_log( 'Full request URL: ' . ( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : 'N/A' ) );

		// 1. Redirect to Google Authorization
		if ( 'login' === $action ) {
			$google_oauth_url = 'https://accounts.google.com/o/oauth2/v2/auth';
			$params = array(
				'client_id'     => $client_id,
				'redirect_uri'  => $redirect_uri,
				'response_type' => 'code',
				'scope'         => 'email profile',
				'access_type'   => 'online',
				'prompt'        => 'select_account',
			);
			if ( isset( $_GET['redirect_to'] ) ) {
				$params['state'] = sanitize_text_field( wp_unslash( $_GET['redirect_to'] ) );
			}
			$auth_url = add_query_arg( $params, $google_oauth_url );
			$this->oauth_debug_log( 'Redirecting to Google: ' . $auth_url );
			wp_redirect( $auth_url );
			exit;
		}

		// 2. Handle Google Callback
		if ( 'callback' === $action && isset( $_GET['code'] ) ) {
			$code = sanitize_text_field( $_GET['code'] );
			
			$this->oauth_debug_log( 'Callback received. Code length: ' . strlen( $code ) );
			
			// Build failure redirect (back to login with redirect_to preserved)
			$failure_redirect = wc_get_page_permalink( 'myaccount' );
			$state_redirect = '';
			if ( isset( $_GET['state'] ) && ! empty( $_GET['state'] ) ) {
				$state_redirect = esc_url_raw( wp_unslash( $_GET['state'] ) );
				$failure_redirect = add_query_arg( 'redirect_to', urlencode( $state_redirect ), $failure_redirect );
			}

			// Exchange code for access token
			$token_url = 'https://oauth2.googleapis.com/token';
			$token_body = array(
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
				'redirect_uri'  => $redirect_uri,
				'grant_type'    => 'authorization_code',
				'code'          => $code,
			);

			$this->oauth_debug_log( 'Token exchange - redirect_uri: ' . $redirect_uri );

			$response = wp_remote_post( $token_url, array(
				'body'    => $token_body,
				'timeout' => 30,
			) );

			if ( is_wp_error( $response ) ) {
				$this->oauth_debug_log( 'TOKEN ERROR (wp_error): ' . $response->get_error_message() );
				wp_redirect( add_query_arg( 'ssl_oauth_error', urlencode( 'Connection error: ' . $response->get_error_message() ), $failure_redirect ) );
				exit;
			}

			$response_code = wp_remote_retrieve_response_code( $response );
			$body_raw = wp_remote_retrieve_body( $response );
			$body = json_decode( $body_raw, true );

			$this->oauth_debug_log( 'Token exchange HTTP status: ' . $response_code );
			$this->oauth_debug_log( 'Token exchange response: ' . substr( $body_raw, 0, 500 ) );
			
			if ( isset( $body['error'] ) ) {
				$error_msg = $body['error'] . ': ' . ( $body['error_description'] ?? 'no description' );
				$this->oauth_debug_log( 'TOKEN ERROR: ' . $error_msg );
				wp_redirect( add_query_arg( 'ssl_oauth_error', urlencode( $error_msg ), $failure_redirect ) );
				exit;
			}

			if ( ! isset( $body['access_token'] ) ) {
				$this->oauth_debug_log( 'TOKEN ERROR: No access_token in response' );
				wp_redirect( add_query_arg( 'ssl_oauth_error', urlencode( 'No access token received' ), $failure_redirect ) );
				exit;
			}

			$access_token = $body['access_token'];
			$this->oauth_debug_log( 'Access token received successfully' );

			// Get user profile data
			$user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo';
			$user_response = wp_remote_get( $user_info_url, array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token,
				),
				'timeout' => 30,
			) );

			if ( is_wp_error( $user_response ) ) {
				$this->oauth_debug_log( 'PROFILE ERROR: ' . $user_response->get_error_message() );
				wp_redirect( add_query_arg( 'ssl_oauth_error', urlencode( 'Profile error: ' . $user_response->get_error_message() ), $failure_redirect ) );
				exit;
			}

			$user_data = json_decode( wp_remote_retrieve_body( $user_response ), true );

			if ( ! isset( $user_data['email'] ) ) {
				$this->oauth_debug_log( 'PROFILE ERROR: No email. Body: ' . wp_remote_retrieve_body( $user_response ) );
				wp_redirect( add_query_arg( 'ssl_oauth_error', urlencode( 'No email from Google' ), $failure_redirect ) );
				exit;
			}

			$email      = sanitize_email( $user_data['email'] );
			$first_name = isset( $user_data['given_name'] ) ? sanitize_text_field( $user_data['given_name'] ) : '';
			$last_name  = isset( $user_data['family_name'] ) ? sanitize_text_field( $user_data['family_name'] ) : '';

			if ( empty( $first_name ) && empty( $last_name ) && ! empty( $user_data['name'] ) ) {
				$full_name = sanitize_text_field( $user_data['name'] );
				$name_parts = explode( ' ', $full_name, 2 );
				$first_name = strval( $name_parts[0] );
				$last_name  = isset( $name_parts[1] ) ? strval( $name_parts[1] ) : '';
			}

			$this->oauth_debug_log( 'Google user: ' . $email . ' (' . $first_name . ' ' . $last_name . ')' );

			$user = get_user_by( 'email', $email );

			if ( ! $user ) {
				$password = wp_generate_password( 24 );
				$user_id  = wc_create_new_customer( $email, '', $password );

				if ( is_wp_error( $user_id ) ) {
					$this->oauth_debug_log( 'CUSTOMER ERROR: ' . $user_id->get_error_message() );
					wp_redirect( add_query_arg( 'ssl_oauth_error', urlencode( $user_id->get_error_message() ), $failure_redirect ) );
					exit;
				}

				$this->save_customer_name( $user_id, $first_name, $last_name );
				$user = get_user_by( 'id', $user_id );
				$this->oauth_debug_log( 'Created new customer ID: ' . $user_id );
			} else {
				$existing_first = get_user_meta( $user->ID, 'first_name', true );
				$existing_last  = get_user_meta( $user->ID, 'last_name', true );
				if ( empty( $existing_first ) && empty( $existing_last ) ) {
					$this->save_customer_name( $user->ID, $first_name, $last_name );
				}
				$this->oauth_debug_log( 'Existing user ID: ' . $user->ID );
			}

			// Log the user in
			$this->oauth_debug_log( 'Headers already sent: ' . ( headers_sent() ? 'YES' : 'NO' ) );
			
			wp_clear_auth_cookie();
			wp_set_current_user( $user->ID );
			wp_set_auth_cookie( $user->ID, true );

			$this->oauth_debug_log( 'Auth cookie set for user ID: ' . $user->ID );
			$this->oauth_debug_log( 'is_user_logged_in: ' . ( is_user_logged_in() ? 'YES' : 'NO' ) );

			// Initialize WooCommerce session for the logged-in customer
			if ( class_exists( 'WooCommerce' ) && isset( WC()->session ) ) {
				WC()->session->init_session_cookie();
				$this->oauth_debug_log( 'WC session cookie initialized' );
			}

			do_action( 'wp_login', $user->user_login, $user );

			// Determine final redirect
			$redirect_url = wc_get_page_permalink( 'myaccount' );
			if ( ! empty( $state_redirect ) ) {
				$redirect_url = $state_redirect;
			}

			$redirect_url = wp_validate_redirect( $redirect_url, wc_get_page_permalink( 'myaccount' ) );

			$this->oauth_debug_log( 'SUCCESS! Redirecting to: ' . $redirect_url );
			wp_redirect( $redirect_url );
			exit;
		}

		// Handle callback errors from Google (e.g. user denied access)
		if ( 'callback' === $action && isset( $_GET['error'] ) ) {
			$this->oauth_debug_log( 'Google returned error: ' . sanitize_text_field( $_GET['error'] ) );
			wp_redirect( add_query_arg( 'ssl_oauth_error', urlencode( 'Google: ' . sanitize_text_field( $_GET['error'] ) ), wc_get_page_permalink( 'myaccount' ) ) );
			exit;
		}

		// Fallback: callback action but no code and no error
		if ( 'callback' === $action ) {
			$this->oauth_debug_log( 'Callback with no code and no error. GET params: ' . wp_json_encode( array_keys( $_GET ) ) );
			wp_redirect( wc_get_page_permalink( 'myaccount' ) );
			exit;
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		// Initialize the plugin
	}

}
