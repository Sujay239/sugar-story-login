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
        add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'restrict_add_to_cart' ), 10, 3 );
        add_filter( 'woocommerce_locate_template', array( $this, 'override_login_template' ), 10, 3 );
        add_action( 'woocommerce_register_post', array( $this, 'validate_custom_registration_fields' ), 10, 3 );
        add_action( 'woocommerce_created_customer', array( $this, 'save_custom_registration_fields' ) );
        add_action( 'init', array( $this, 'handle_google_oauth_flow' ) );
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
        if ( is_checkout() && ! is_user_logged_in() && ! is_wc_endpoint_url( 'order-pay' ) && ! is_wc_endpoint_url( 'order-received' ) ) {
            wc_add_notice( __( 'You must be logged in to checkout. Please log in or register below.', 'sugar-story-login' ), 'error' );
            wp_redirect( wc_get_page_permalink( 'myaccount' ) );
            exit;
        }
    }

    /**
     * Restrict adding items to cart for non-logged-in users.
     */
    public function restrict_add_to_cart( $passed, $product_id, $quantity ) {
        if ( ! is_user_logged_in() ) {
            wc_add_notice( __( 'You must be logged in to add items to your cart.', 'sugar-story-login' ), 'error' );
            return false;
        }
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
        if ( isset( $_POST['full_name'] ) ) {
            $full_name = sanitize_text_field( wp_unslash( $_POST['full_name'] ) );
            if ( empty( $full_name ) ) {
                $validation_errors->add( 'full_name_error', __( '<strong>Error</strong>: Full Name is required.', 'sugar-story-login' ) );
            } elseif ( ! preg_match( '/^[a-zA-Z\s]+$/', $full_name ) ) {
                $validation_errors->add( 'full_name_invalid', __( '<strong>Error</strong>: Full Name can only contain letters and spaces.', 'sugar-story-login' ) );
            }
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
     * Save custom registration fields.
     */
    public function save_custom_registration_fields( $customer_id ) {
        if ( isset( $_POST['full_name'] ) ) {
            $full_name = sanitize_text_field( wp_unslash( $_POST['full_name'] ) );
            $name_parts = explode( ' ', $full_name, 2 );
            $first_name = strval( $name_parts[0] );
            $last_name  = isset( $name_parts[1] ) ? strval( $name_parts[1] ) : '';
            
            update_user_meta( $customer_id, 'first_name', $first_name );
            update_user_meta( $customer_id, 'last_name', $last_name );
            update_user_meta( $customer_id, 'billing_first_name', $first_name );
            update_user_meta( $customer_id, 'billing_last_name', $last_name );
        }
        
        if ( isset( $_POST['billing_phone'] ) && ! empty( $_POST['billing_phone'] ) ) {
            $phone = sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) );
            update_user_meta( $customer_id, 'billing_phone', strval( $phone ) );
        }
    }

	/**
	 * Handle Google OAuth 2.0 Flow.
	 */
	public function handle_google_oauth_flow() {
		if ( ! isset( $_GET['sugar_story_google_auth'] ) ) {
			return;
		}

		$client_id     = get_option( 'sugar_story_google_client_id' );
		$client_secret = get_option( 'sugar_story_google_client_secret' );
		$redirect_uri  = site_url( '?sugar_story_google_auth=callback' );

		if ( empty( $client_id ) || empty( $client_secret ) ) {
			return;
		}

		$action = sanitize_text_field( $_GET['sugar_story_google_auth'] );

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
			$auth_url = add_query_arg( $params, $google_oauth_url );
			wp_redirect( $auth_url );
			exit;
		}

		// 2. Handle Google Callback
		if ( 'callback' === $action && isset( $_GET['code'] ) ) {
			$code = sanitize_text_field( $_GET['code'] );

			// Exchange code for access token
			$token_url = 'https://oauth2.googleapis.com/token';
			$response = wp_remote_post( $token_url, array(
				'body' => array(
					'client_id'     => $client_id,
					'client_secret' => $client_secret,
					'redirect_uri'  => $redirect_uri,
					'grant_type'    => 'authorization_code',
					'code'          => $code,
				),
			) );

			if ( is_wp_error( $response ) ) {
				wc_add_notice( __( 'Google Login Failed: Could not connect to Google.', 'sugar-story-login' ), 'error' );
				wp_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			
			if ( isset( $body['error'] ) ) {
				wc_add_notice( __( 'Google Login Failed: ' . esc_html( $body['error_description'] ?? $body['error'] ), 'sugar-story-login' ), 'error' );
				wp_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			$access_token = $body['access_token'];

			// Get user profile data
			$user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo';
			$user_response = wp_remote_get( $user_info_url, array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token,
				),
			) );

			if ( is_wp_error( $user_response ) ) {
				wc_add_notice( __( 'Google Login Failed: Could not retrieve user profile.', 'sugar-story-login' ), 'error' );
				wp_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			$user_data = json_decode( wp_remote_retrieve_body( $user_response ), true );

			if ( ! isset( $user_data['email'] ) ) {
				wc_add_notice( __( 'Google Login Failed: No email address provided by Google.', 'sugar-story-login' ), 'error' );
				wp_redirect( wc_get_page_permalink( 'myaccount' ) );
				exit;
			}

			$email      = sanitize_email( $user_data['email'] );
			$first_name = isset( $user_data['given_name'] ) ? sanitize_text_field( $user_data['given_name'] ) : '';
			$last_name  = isset( $user_data['family_name'] ) ? sanitize_text_field( $user_data['family_name'] ) : '';

			$user = get_user_by( 'email', $email );

			if ( ! $user ) {
				$password = wp_generate_password( 24 );
				$user_id  = wc_create_new_customer( $email, '', $password );

				if ( is_wp_error( $user_id ) ) {
					wc_add_notice( $user_id->get_error_message(), 'error' );
					wp_redirect( wc_get_page_permalink( 'myaccount' ) );
					exit;
				}

				update_user_meta( $user_id, 'first_name', $first_name );
				update_user_meta( $user_id, 'last_name', $last_name );
				update_user_meta( $user_id, 'billing_first_name', $first_name );
				update_user_meta( $user_id, 'billing_last_name', $last_name );
				
				$user = get_user_by( 'id', $user_id );
			}

			wp_set_current_user( $user->ID );
			wp_set_auth_cookie( $user->ID );
			do_action( 'wp_login', $user->user_login, $user );

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
