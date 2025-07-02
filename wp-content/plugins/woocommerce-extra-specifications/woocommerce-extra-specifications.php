<?php
/**
 * Plugin Name: WooCommerce Extra Specifications
 * Description: Adds an extra specifications tab to WooCommerce products and a metabox to manage them.
 * Version: 1.0.0
 * Author: Jules
 * Author URI: https://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: woocommerce-extra-specifications
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin path and URL constants.
define( 'WC_EXTRA_SPECS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WC_EXTRA_SPECS_URL', plugin_dir_url( __FILE__ ) );

// Include the main class.
require_once WC_EXTRA_SPECS_PATH . 'includes/class-wc-extra-specifications.php';

// Initialize the plugin.
function wc_extra_specifications_init() {
	new WC_Extra_Specifications();
}
add_action( 'plugins_loaded', 'wc_extra_specifications_init' );
