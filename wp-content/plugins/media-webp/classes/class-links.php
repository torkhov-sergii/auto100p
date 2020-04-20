<?php
/**
 * Plugin
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage WPST\Media_Webp
 * @author     Steven Turner <steveturner23@hotmail.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 */

namespace WPST\Media_Webp;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Class Links
 *
 * Admin link login and render
 *
 * @category   Class
 * @package    Links
 * @see        Singleton
 * @see        Plugin
 **/
class Links {

	use Singleton;

	/**
	 * Plugin add settings links plugin screen.
	 *
	 * @param array $links admin links array filter.
	 * @return Array
	 */
	public function settings_links( array $links ) : array {
		$links = array_merge(
			array(
				'<a href="' . esc_url( admin_url( '/options-general.php?page=media-webp_settings' ) ) . '">' .
				_x( 'Settings', 'plugin action notice', 'media-webp' ) . '</a>',
				'<a href="' . esc_url( admin_url( '/tools.php?page=media-webp_tools' ) ) . '">' .
				_x( 'Tools', 'plugin action notice', 'media-webp' ) . '</a>',
			),
			$links
		);
		return $links;
	}

	/**
	 * Add tool links to admin pages. add_action network_admin_menu or admin_menu
	 */
	public function tools_menu() : bool {
		$title  = esc_html( _x( 'Media Webp', 'admin side menu title', 'media-webp' ) );
		$form   = Forms::get_instance();
		$plugin = Plugin::get_instance();
		if ( $plugin->networkactive() ) {
			add_submenu_page( 'tools.php', $title, $title, 'manage_network_plugins', 'media-webp_tools', array( $form, 'tools_page' ) );
		} else {
			add_submenu_page( 'tools.php', $title, $title, 'manage_options', 'media-webp_tools', array( $form, 'tools_page' ) );
		}
		return true;
	}

	/**
	 * Add settings links to admin pages. add_action network_admin_menu or admin_menu
	 */
	public function settings_menu() : bool {
		$title  = esc_html( _x( 'Media Webp', 'admin side menu title', 'media-webp' ) );
		$form   = Forms::get_instance();
		$plugin = Plugin::get_instance();
		if ( $plugin->networkactive() ) {
			add_submenu_page( 'settings.php', $title, $title, 'manage_network_plugins', 'media-webp_settings', array( $form, 'settings_page' ) );
		} else {
			add_submenu_page( 'options-general.php', $title, $title, 'manage_options', 'media-webp_settings', array( $form, 'settings_page' ) );
		}
		return true;
	}

	/**
	 * Plugin add settings links to $links array. Triggered by plugin_action_links_{plugin name}
	 *
	 * @param array $links Test.
	 * @return array
	 */
	public static function plugin_action_links( array $links ) : array {
		if ( is_network_admin() && is_plugin_active_for_network( MEDIA_WEBP_BASENAME )
		) {
			array_unshift( $links, '<a href="' . network_admin_url( 'settings.php#whl_settings' ) . '">' . _x( 'Settings', 'plugin action notice', 'media-webp' ) . '</a>' );
		} elseif ( ! is_network_admin() ) {
			array_unshift( $links, '<a href="' . admin_url( 'options-general.php#whl_settings' ) . '">' . _x( 'Settings', 'plugin action notice', 'media-webp' ) . '</a>' );
		}
		return $links;
	}
}
