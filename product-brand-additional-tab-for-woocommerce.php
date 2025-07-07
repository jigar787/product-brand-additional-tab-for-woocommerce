<?php
/**
 * Plugin Name:       Product Brand Additional Tab for WooCommerce
 * Plugin URI:
 * Description:       Displays WooCommerce native product brand in the Additional Information tab and adds schema.org brand markup(description, logo, link) for better SEO.
 * Author:            Jigar Bhanushali
 * Author URI:        https://jigarbhanushali.com
 * Text Domain:       product-brand-additional-tab-for-woocommerce
 * Domain Path:       /languages
 * Version:           1.0.0
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires Plugins:  woocommerce
 * Requires at least: 6.0
 * Requires PHP:      7.2
 *
 * @package         WC_Product_Brand_Additional_Tab
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WC_PBAT_BASEFILE', __FILE__ );
define( 'WC_PBAT_ABSURL', plugins_url( '/', WC_PBAT_BASEFILE ) );
define( 'WC_PBAT_BASENAME', plugin_basename( WC_PBAT_BASEFILE ) );
define( 'WC_PBAT_ABSPATH', plugin_dir_path( WC_PBAT_BASEFILE ) );
define( 'WC_PBAT_DIRNAME', basename( WC_PBAT_ABSPATH ) );

/**
 * The function thast will handle the queue for autoloader.
 *
 * @param string $class_name Class Name.
 * @since    1.0.0
 */
function wc_pbat_autoload( $class_name ) {
	$namespaces = array( 'WC_Product_Brand_Additional_Tab' );
	foreach ( $namespaces as $namespace ) {
		if ( substr( $class_name, 0, strlen( $namespace ) ) === $namespace ) {
			$filename = WC_PBAT_ABSPATH . 'includes/class-' . str_replace( '_', '-', strtolower( $class_name ) ) . '.php';
			if ( is_readable( $filename ) ) {
				require_once $filename;

				return true;
			}
		}
	}
	return false;
}
spl_autoload_register( 'wc_pbat_autoload' );

/**
 * The code that runs during plugin activation.
 */
function wc_pbat_activate() {
	// Code here.
}

/**
 * The code that runs during plugin deactivation.
 */
function wc_pbat_deactivate() {
	// Code here.
}

register_activation_hook( WC_PBAT_BASEFILE, 'wc_pbat_activate' );
register_deactivation_hook( WC_PBAT_BASEFILE, 'wc_pbat_deactivate' );

/**
 * Run plugin core hooks.
 */
function wc_pbat_woo_init() {
	WC_Product_Brand_Additional_Tab::instance();
}
add_action( 'woocommerce_init', 'wc_pbat_woo_init' );
