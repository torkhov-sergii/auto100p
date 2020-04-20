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
 * Class Ajax
 *
 * Handles all incoming Ajax requests.
 *
 * @category   Class
 * @package    WordPress
 * @see        Singleton
 **/
class Ajax {

	use Singleton;
	/**
	 * Upload folder.
	 *
	 *  @var string
	 * */
	public static $base_path;

	/**
	 * Model object in use.
	 *
	 * @var Model
	 */
	public static $_model;

	/**
	 * Cast base_path to upload folder.
	 */
	protected function init() : void {
			self::$base_path = wp_upload_dir()['basedir'];
	}

	/**
	 * Theme create / delete webps method.
	 *
	 * @param array $post sanitized $_POST object.
	 */
	public function webp_theme( array $post ) : bool {
		wp_send_json( self::theme_generate_webps( $post['flag'] ) );
		return true;
	}

	/**
	 * Starts the collection of attachment IDs that have an associated webp image.
	 *
	 * @param array $post sanitized $_POST object.
	 */
	public function webp_ids( array $post ) {
		is_null( self::$_model ) ? self::$_model = new Model( Plugin::$images ) : null;

		$json = self::get_webps_( $post['flag'], self::$_model );
		wp_send_json_success( $json );
		return $json;
	}

	/**
	 * Create or delete an existing Webp image.
	 *
	 * @param array $post sanitized $_POST object.
	 *
	 * @return bool
	 */
	public function webp_manage( array $post ) : bool {
			$id      = (int) $post['id'];
			$flag    = (bool) $post['flag'];
			$details = (string) $post['details'];
			$admin   = Admin::get_instance();

		if ( false === $flag ) {
			$admin->delete_action( $id );
		} else {

			$image_details = wp_get_attachment_metadata( $id );
			$file['mime']  = $image_details['mime'];
			$file['file']  = self::$base_path . '/' . $image_details['file'];
			$image_path    = dirname( $file['file'] );

			$admin->create_webp( $file, '' );

			foreach ( $image_details['sizes'] as $size ) {
				$file['file'] = $image_path . '/' . $size['file'];
				$admin->create_webp( $file, '' );
			}

			$backup_sizes = get_post_meta( $id, '_wp_attachment_backup_sizes', true );
			if ( is_array( $backup_sizes ) && null !== $backup_sizes ) {
				foreach ( $backup_sizes as $size ) {
					$file['file'] = $image_path . '/' . $size['file'];
					$admin->create_webp( $file, '' );
				}
			}
		}

		if ( 'tool' === $details ) {
			is_null( self::$_model ) ? self::$_model = new Model( Plugin::$images ) : null;
			$model                                   = self::$_model;
			$attachments                             = $model->get_image_attachments()->attachments;
			$attachment_webps                        = 0;
			foreach ( $attachments as $attachment ) {
				$admin->attachment_webp_exists( $attachment->ID ) ? $attachment_webps++ : null;
			}

			$admin->webp_jpeg_png_totals( self::$base_path );
			$json = [
				'attachments_with_webp' => $attachment_webps,
				'attachment_webps_size' => size_format( $admin->image_info['webps_size'], 2 ),
				'attachment_webps'      => $admin->image_info['webps'],
				'storage'               => size_format( disk_free_space( self::$base_path ), 2 ),
			];
			wp_send_json_success( $json );
			return true;
		} elseif ( 'post' === $details ) {
			$size_details = size_format( filesize( self::$base_path . '/' . $image_details['file'] . '.webp' ) );
			$json         = [
				'meta_html' => '<div class="webp-meta-box"><p><span class="webp"></span></p><p style="margin-left:20px">' .
				esc_html( _x( 'This media has linked webp images.', 'post.php webp meta box', 'media-webp' ) ) .
				'<br/>' . esc_html( _x( 'Main Image File Size', 'post.php webp meta box', 'media-webp' ) ) . ' : ' .
				$size_details . '</p></div>',
				'size'      => $size_details,
			];
			wp_send_json_success( $json );
			return true;
		} else {
			wp_send_json_success();
			return true;
		}
	}

	/**
	 * Create theme images when theme selected.
	 *
	 * @see Plugin::theme_switch()
	 */
	public function theme_switch() : bool {
		$image_info = self::theme_generate_webps( true );
		if ( 1 === $image_info['result'] ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Create theme images.
	 *
	 * @param bool $action create or delete.
	 * @return bool $result succeeded / failed.
	 */
	public function theme_generate_webps( bool $action ) : array {
		if ( 'off' !== Plugin::$options['mode'] || ! $action ) {
				$admin        = new Admin();
				$template_dir = get_template_directory();
				$image_info   = $admin
				->manage_theme_jpeg_png( $action, $template_dir, true )
				->webp_jpeg_png_totals();
				$image_info   = [
					'total_webps_size' => size_format( $image_info['webps_size'], 2 ),
					'total_images'     => $image_info['webps'],
					'result'           => 1,
				];

				return $image_info;
		} else {
			$image_info['result'] = -2;
			return $image_info;
		}
	}

	/**
	 * Collects all attachments that could have or do have webp images.
	 *
	 * @param boolean $flag Request missing or present webps.
	 * @param Model   $model Model Instance.
	 *
	 * @return object $json List of attachments with or without webp images. Depends on flag.
	 */
	public function get_webps_( bool $flag, Model $model ) : \stdClass {

		$json = (object) [
			'result' => '',
			'total'  => 0,
			'id_s'   => [],
		];

			$attachments = $model->get_image_attachments()->attachments;
			$flag        = ! $flag;
		if ( $attachments ) {
			$admin = Admin::get_instance();
			foreach ( $attachments as $attachment ) {
				if ( $admin->attachment_webp_exists( $attachment->ID ) === $flag ) {
					$json->id_s[] = $attachment->ID;
					++$json->total;
				}
			}
			$json->result = 1;
		} else {
			$json->result = 0;
		}
		return $json;
	}

	/**
	 * Set class model, for unit testing.
	 * Protected by trailing underscore.
	 *
	 * @param Model $model Bind class model.
	 *
	 * @return Ajax   $this Instance of self.
	 */
	public function set_model_( Model $model ) : Ajax {
		self::$_model = $model;
		return $this;
	}

	/**
	 * Clear class model, for unit testing.
	 * Protected by trailing underscore.
	 *
	 * @return Ajax   $this Instance of self.
	 */
	public function clear_model_() : Ajax {
		self::$_model = null;
		return $this;
	}
}
