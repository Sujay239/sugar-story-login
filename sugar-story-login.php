<?php
/**
 * Plugin Name:       Login Form by Sujay
 * Plugin URI:        https://example.com
 * Description:       A custom WooCommerce login plugin by Sujay.
 * Version:           1.0.0
 * Author:            Sujay Kumar Kotal
 * Author URI:        https://example.com
 * Text Domain:       site-title-login
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin constants.
define( 'SUGAR_STORY_LOGIN_VERSION', '1.0.0' );
define( 'SUGAR_STORY_LOGIN_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SUGAR_STORY_LOGIN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Check if WooCommerce is active
 */
function sugar_story_login_check_woocommerce() {
    if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        add_action( 'admin_notices', 'sugar_story_login_woocommerce_missing_notice' );
        return false;
    }
    return true;
}

/**
 * Admin notice if WooCommerce is not active.
 */
function sugar_story_login_woocommerce_missing_notice() {
    ?>
    <div class="error notice">
        <p><?php printf( esc_html__( '%s Login requires WooCommerce to be installed and active.', 'sugar-story-login' ), esc_html( get_bloginfo( 'name' ) ) ); ?></p>
    </div>
    <?php
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sugar-story-login.php';

/**
 * Begins execution of the plugin.
 */
function run_sugar_story_login() {
    if ( sugar_story_login_check_woocommerce() ) {
        $plugin = new Sugar_Story_Login();
        $plugin->run();
    }
}
run_sugar_story_login();
