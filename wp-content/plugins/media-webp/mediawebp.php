<?php

/**
 * Plugin Name: Media Webp
 * Description: Automatically creates webp images when you upload compatible media.  This plugin also manages any updates and changes to the linked attachments, and allows you to manually convert existing images for both your upload and theme folders.
 * Author: Steven Turner
 * Version: 1.0.3
 * Requires at least: 4.7
 * Tested up to: 4.9
 * Text Domain: media-webp
 * Domain Path: /languages
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// PHP 7.1 or greater.
if ( version_compare( phpversion(), '7.1.0', '>' ) ) {
	// Constants.
	define( 'MEDIA_WEBP_VERSION', '1.0.3' );
	define( 'MEDIA_WEBP_FOLDER', 'mediawebp' );
	define( 'MEDIA_WEBP_URL', plugin_dir_url( __FILE__ ) );
	define( 'MEDIA_WEBP_DIR', plugin_dir_path( __FILE__ ) );
	define( 'MEDIA_WEBP_BASENAME', plugin_basename( __FILE__ ) );
	require_once MEDIA_WEBP_DIR . 'autoload.php';
	register_activation_hook( __FILE__, array( '\WPST\Media_Webp\Plugin', 'activate' ) );
	add_action( 'plugins_loaded', 'plugins_loaded_media_webp_plugin' );

	/**
	 * Plugin loading function
	 */
	function plugins_loaded_media_webp_plugin() {
		load_plugin_textdomain( 'media-webp', false, dirname( MEDIA_WEBP_BASENAME ) . '/languages' );
		\WPST\Media_Webp\Plugin::get_instance();
	}
} else {
	add_action( 'admin_notices', 'php_version_media_webp_notice' );
	add_action( 'network_admin_notices', 'php_version_media_webp_notice' );
}
/**
 * Php version notice
 */
function php_version_media_webp_notice() {
	echo '<div class="error notice is-dismissible"><p>' .
	esc_html( _x( 'Please upgrade your PHP to version 7.1 or greater before activating Media Webp', 'admin notice', 'media-webp' ) ) . ' <strong>' .
	esc_html( _x( 'Media Webp', 'plugin title', 'media-webp' ) ) . '</strong>.</p></div>';
}