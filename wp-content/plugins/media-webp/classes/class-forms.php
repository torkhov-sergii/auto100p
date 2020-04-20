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
 * Class Foms
 *
 * Form display and processing.
 *
 * @category   Class
 * @package    Form
 * @see        Singleton
 * @see        Plugin
 **/
class Forms {

	use Singleton;

	/**
	 * Form content array.
	 *
	 * @var Array
	 * */
	private static $form = [
		'storage',
		'attachments',
		'attachments_with_webps',
		'attachment_images',
		'attachment_webps',
		'attachment_images_size',
		'attachment_webps_size',
		'theme_images',
		'theme_webps',
		'theme_images_size',
		'theme_webps_size',
	];

	/**
	 * Populates and tools options to use when rendering setting page view. Render triggered by Tools::show() at the bottom of this function.
	 *
	 * @param  null  $string Passed by callback.
	 * @param  Model $model Instance of $model class.
	 * @param  Admin $admin Instance of admin class.
	 *
	 * @return void
	 */
	public function tools_page( $string = null, Model $model = null, Admin $admin = null ) : void {
		global $protocol;

		is_null( $admin ) ? $admin = Admin::get_instance() : null;
		is_null( $model ) ? $model = new Model( Plugin::$images ) : null;

		$base        = Plugin::networkactive() ? network_admin_url( 'settings.php' ) : admin_url( 'options-general.php' );
		$url         = add_query_arg( 'page', 'media_webp_settings', $base );
		$attachments = $model->get_image_attachments()->attachments;

		self::$form['attachments']            = 0;
		self::$form['attachments_with_webps'] = 0;

		foreach ( $attachments as $attachment ) {
			$admin->attachment_webp_exists( $attachment->ID ) ? self::$form['attachments_with_webps']++ : null;
			self::$form['attachments']++;
		}
		$admin->clear_image_info();
		$admin->webp_jpeg_png_totals( Plugin::$base_path );

		self::$form['storage']                = size_format( disk_free_space( Plugin::$base_path ), 2 );
		self::$form['attachment_images_size'] = size_format( $admin->image_info['images_size'], 2 );
		self::$form['attachment_webps_size']  = size_format( $admin->image_info['webps_size'], 2 );
		self::$form['attachment_images']      = $admin->image_info['images'];
		self::$form['attachment_webps']       = $admin->image_info['webps'];

		$admin->clear_image_info();

		$admin->webp_jpeg_png_totals( get_template_directory() );
		self::$form['theme_images_size'] = size_format( $admin->image_info['images_size'], 2 );
		self::$form['theme_webps_size']  = size_format( $admin->image_info['webps_size'], 2 );
		self::$form['theme_images']      = $admin->image_info['images'];
		self::$form['theme_webps']       = $admin->image_info['webps'];

		// sentences are stored in the complete format to allow for accurate translations. This does mean there is repartition.
		$translation_array = [
			'media_webp_alert_1'  => _x( 'All .webp images have been successfully created.', 'jQuery Alert', 'media-webp' ),
			'media_webp_alert_2'  => _x( 'All .webp images have been successfully deleted.', 'jQuery Alert', 'media-webp' ),
			'media_webp_alert_3'  => _x( 'Checking', 'jQuery Alert', 'media-webp' ),
			'media_webp_alert_4'  => _x( 'Creating webp images, please wait.', 'jQuery Alert', 'media-webp' ),
			'media_webp_alert_5'  => _x( 'Deleting webp images, please wait.', 'jQuery Alert', 'media-webp' ),
			'media_webp_alert_6'  => _x( 'No .wepb images to delete', 'jQuery Alert', 'media-webp' ),
			'media_webp_alert_7'  => _x( 'No .wepb images to create', 'jQuery Alert', 'media-webp' ),
			'media_webp_alert_8'  => _x( '.webp images created', 'jQuery Alert', 'media-webp' ),
			'media_webp_alert_9'  => _x( '.webp images deleted', 'jQuery Alert', 'media-webp' ),
			'media_webp_alert_10' => _x( 'There has been a problem deleting the theme .webp images', 'jQuery Alert', 'media-webp' ),
			'media_webp_alert_11' => _x( 'Are you sure you would like to create all of the theme .webp images', 'jQuery Alert', 'media-webp' ),
			'media_webp_alert_12' => _x( 'Are you sure you would like to delete all of the theme .webp images', 'jQuery Alert', 'media-webp' ),
			'media_webp_alert_13' => _x( 'Are you sure you would like to create all of the media .webp images', 'jQuery Alert', 'media-webp' ),
			'media_webp_alert_14' => _x( 'Are you sure you would like to delete all of the media .webp images', 'jQuery Alert', 'media-webp' ),
			'media_webp_alert_17' => _x( 'Unable to generate images, the plugin is disabled', 'jQuery Alert', 'media-webp' ),
			'ajaxurl'             => admin_url( 'admin-ajax.php', $protocol ),
			'ajax_nonce'          => wp_create_nonce( 'media-webp-ajax' ),
		];
		wp_enqueue_script( 'media-webp-ajax', MEDIA_WEBP_URL . 'assets/js/tools.js', array(), '1.0.0', true );
		wp_localize_script( 'media-webp-ajax', 'media_webp_object', $translation_array );

		if ( 'off' === Plugin::$options['mode'] ) {
			self::$form['message'] = '<div class="notice-warning notice is-dismissible"><p><span class="media_wts">' .
			esc_html( _x( 'Plugin disabled, you are unable to create .webp images. Please enable the plugin in ', 'admin notice', 'media-webp' ) ) . '</span><span>' .
			esc_html( $admin->image_info['message'] ) . '</span></p></div>';
		}
		// render trigger.
		$tools = new Tool( self::$form );
	}

	/**
	 * Render trigger.
	 *
	 * @return bool
	 */
	public function settings_page() : bool {
		// render trigger.
		$plugin   = Plugin::get_instance();
		$settings = new Setting( $plugin::$options );
		return true;
	}

	/**
	 * Return context-aware tools page URL.
	 *
	 * @return bool
	 */
	public function check_submission() : bool {
		global $pagenow;
		global $plugin_page;
		$plugin = Plugin::get_instance();
		if ( 'options-general.php' === $pagenow && 'media-webp_settings' === $plugin_page ) {
			check_admin_referer( 'media-webp-admin', '_wpnonce' );
			$post = sanitize_post( wp_unslash( $_POST ) );
			unset( $post['_wpnonce'] );

			if ( ! $plugin::networkactive() ) {
				add_action( 'admin_notices', array( $this, 'admin_notice_info' ) );
			} else {
				add_action( 'network_admin_notices', array( $this, 'admin_notice_info' ) );
			}
			if ( $plugin::$options !== $post ) {
				$plugin::$options         = $post;
				$plugin::$settings_notice = $plugin->update_options();
			} else {
				$plugin::$settings_notice = -1;
			}
			return true;
		}
		return false;
	}

	/**
	 * Settings page, updated notice. Hooked by 'admin_notices','network_admin_notices','after_plugin_row','network_admin_notices'
	 */
	public function admin_notice_info() : void {
		if ( 1 === Plugin::$settings_notice ) {
			echo '<div class="updated notice is-dismissible"><p>' . esc_html( _x( 'Settings have been updated', 'admin notice', 'media-webp' ) ) . '</p></div>';
		} elseif ( -1 === Plugin::$settings_notice ) {
			echo '<div class="notice notice-info is-dismissible"><p>' . esc_html( _x( 'No changes have been made to the plugin settings', 'admin notice', 'media-webp' ) ) . '</p></div>';
		} else {
			echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( _x( 'Failed to update settings', 'admin notice', 'media-webp' ) ) . '</p></div>';
		}
	}

}
