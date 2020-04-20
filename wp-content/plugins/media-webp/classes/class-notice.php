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
 * Class Notice
 *
 * Processes notice display within the admin area.
 *
 * @category   Class
 * @package    Notice
 * @see        Singleton
 * @see        Plugin
 **/
class Notice {

	use Singleton;

	/**
	 * Admin editor meta box details flag.
	 *
	 * @var $_edit_meta flag for: meta box update required.
	 * **/
	private static $edit_meta;

	/**
	 * Image size for attachment details overlay.
	 *
	 * @var String
	 * */
	private static $_image_filesize;

	/**
	 * Plugin notice when plugin active but not enabled. Show in plugin list.
	 *
	 * @param string $plugin Current Plugin row being processed.
	 */
	public function plugin_notice( string $plugin ) : void {
		if ( 'mediawebp/mediawebp.php' === $plugin && 'off' === Plugin::$options['mode'] ) {
			echo '<td colspan="5" class="plugin-update colspanchange"><div class="notice inline notice-info notice-alt"><p>' .
			esc_html( _x( 'The plugin is active, but has not been enabled.  Please click on Settings', 'admin notice', 'media-webp' ) ) . '</p></div></td>';
		}
	}

	/**
	 * Plugin admin errors notice settings not updated. Hooked by admin_notices.
	 */
	public function admin_notice_error_settings() : void {
		$class   = esc_html( 'notice notice-error' );
		$message = _x( 'Your settings have NOT been updated.', 'admin notice', 'media-webp' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}

	/**
	 * Plugin admin error, wrong WordPress version. Hooked by admin_notices and network_admin_notices
	 */
	public function admin_notices_incompatible() : void {
		echo '<div class="error notice is-dismissible"><p>' .
		esc_html( _x( 'Please upgrade to the latest version of WordPress', 'admin notice', 'media-webp' ) ) . ' <strong>' .
		esc_html( _x( 'Media Webp', 'plugin title', 'media-webp' ) ) . '</strong>.</p></div>';
	}

	/**
	 * GD Library missing notice
	 */
	public function admin_notices_gd_lib() {
		echo '<div class="error notice is-dismissible"><p>' .
		esc_html( _x( 'The GD php module needs to be enabled for this plugin to function.', 'admin notice', 'media-webp' ) ) . ' <strong>' .
		esc_html( _x( 'Media Webp', 'plugin title', 'media-webp' ) ) . '</strong>.</p></div>';
	}

	/**
	 * Fired by WordPress action 'add_meta_boxes', adding the callback to the meta box function.
	 *
	 * @param string   $content passed content.
	 * @param \WP_Post $post passed post object.
	 * @param Admin    $admin Admin instance.
	 */
	public function wp_editor_meta_box_action( string $content, \WP_Post $post, Admin $admin = null ) : bool {
		$response = [
			'id'        => $post->ID,
			'webp'      => '',
			'webp_size' => '',
		];

		is_null( $admin ) ? $admin = Admin::get_instance() : null;

		$response              = $admin->attachment_details_webp_exists( $response );
		self::$_image_filesize = $response['webp_size'];
		if ( '' !== $response['webp'] ) {
			self::$edit_meta    = $response['webp'];
			Plugin::$current_id = $post->ID;
			add_meta_box( 'webp_image', 'Webp Image', array( $this, 'wp_editor_meta_box' ), $content, 'side', 'core', 'high' );
			return true;
		}
		return false;
	}

	/**
	 * Callback for wp_editor_meta_box_action.  Adds Webp info meta box on the post.php admin page'.
	 *
	 * @param  \WP_Post $post post object.
	 */
	public function wp_editor_meta_box( \WP_Post $post ) : void {
		if ( 'true' === self::$edit_meta ) {
			echo '<div class="webp-meta-box"><p><span class="webp"></span></p><p style="margin-left:20px">' . esc_html( _x( 'This media has linked webp images.', 'post.php webp meta box', 'media-webp' ) );
			echo '<br/>' . esc_html( _x( 'Main Image File Size', 'post.php webp meta box', 'media-webp' ) ) . ' : ' . esc_html( self::$_image_filesize ) . '</p></div>';
		} else {
			echo '<div class="webp-meta-box"><p>' . esc_html( _x( 'Media compatible with the webp format.', 'post.php webp meta box', 'media-webp' ) ) . '<span class="spinner"></span></p>';
			echo '<button class="button create-webp button-large" style="margin-left:10px" type="button" id="webp_button">';
			echo esc_html( _x( 'Create webp', 'post.php webp meta box', 'media-webp' ) ) . '</button><span class="webp"></span></div>';
			echo '<script type="text/javascript">jQuery("#webp_button").bind("click", function(){convert_to_webp(' . esc_html( Plugin::$current_id ) . ');});</script>';
		}
	}

	/**
	 * Clear switch for unit testing.
	 */
	public function clear_edit_meta_flag() : void {
		self::$edit_meta = false;
	}
}
