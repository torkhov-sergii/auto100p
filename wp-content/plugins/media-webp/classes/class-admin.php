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
 * Class Admin
 *
 * This class contains all the heavy lifting.
 *
 * @category   Class
 * @package    Admin
 * @see        Singleton
 * @see        Plugin
 **/
class Admin {

	use Singleton;
	/**
	 * Switch used to indicate that a webp image has been created from the uploaded attachment.
	 *
	 * @var Boolean
	 * */
	private static $_webp = false;

	/**
	 * Object that contains the standardised jpg or png image.
	 *
	 * @var Object
	 * */
	private static $_img;

	/**
	 * Image size for attachment details overlay.
	 *
	 * @var String
	 * */
	private static $_image_filesize;

	/**
	 * Image size for attachment details overlay.
	 *
	 * @var String
	 * */
	private $_current_path;
	/**
	 * Image id array for test conformations
	 *
	 * @var array
	 * */
	public $id_array = [];

	/**
	 * Array used to collect stats shown on the tools page.
	 *
	 * @var Array
	 * */
	public $image_info = [
		'message'     => '',
		'storage'     => 0,
		'images'      => 0,
		'webps'       => 0,
		'images_size' => 0,
		'webps_size'  => 0,
	];

	/**
	 * Image size array used when generating thumbnails.
	 *
	 * @var Array
	 * */
	private static $_image_size = [
		'width'  => 0,
		'height' => 0,
	];

	/**
	 * Cast variable default.
	 * */
	public function init() {
		self::$_webp = false;
	}

	/**
	 * Return instance of self.
	 *
	 * @return $this
	 * */
	public function __construct() {
		return $this;
	}

	/**
	 * Query if webp exists by attachment ID.
	 *
	 * @param int $id Attachment ID.
	 * @return Boolean
	 */
	public function attachment_webp_exists( int $id ) : bool {
		$image_meta_data = wp_get_attachment_metadata( $id );
		if ( is_array( $image_meta_data ) && isset( $image_meta_data['file'] ) ) {
			if ( file_exists( Plugin::$base_path . '/' . $image_meta_data['file'] . '.webp' ) ) {
				self::$_image_filesize = filesize( Plugin::$base_path . '/' . $image_meta_data['file'] . '.webp' );
				return true;
			}
		}
		return false;
	}

	/**
	 * Used to construct the detailed reply.
	 *
	 * @param array $response Json object construct.
	 * @return array
	 */
	public function attachment_details_webp_exists( array $response ) : array {
		$type = get_post_mime_type( $response['id'] );
		if ( self::attachment_webp_exists( $response['id'] ) ) {
			$response['webp']      = 'true';
			$response['webp_size'] = size_format( self::$_image_filesize );
		} elseif ( in_array( $type, Plugin::$images, true ) ) {
			$response['webp'] = 'convert';
		}
		return $response;
	}

	/**
	 * Theme convert method.
	 * Only useful for themes as attachment images are created
	 * by iterating through the WordPress database to avoid
	 * converting unlinked images.
	 *
	 * @param bool    $action true = create, false = delete.
	 * @param string  $path file path.
	 * @param boolean $switch true:Cast path to class variable.
	 * @return object $this
	 */
	public function manage_theme_jpeg_png( bool $action, string $path, bool $switch = false ) : Admin {
		if ( $switch ) {
			unset( $this->id_array );
			$this->id_array      = [];
			$this->_current_path = $path;
		}

		foreach ( new \DirectoryIterator( $path ) as $file ) {
			if ( $file->isDot() ) {
				continue;
			}
			if ( $file->isDir() ) {
				self::manage_theme_jpeg_png( $action, "$path/$file" );
			} else {
				$filename  = $file->getFilename();
				$extension = pathinfo( $filename, PATHINFO_EXTENSION );
				if ( 'jpg' === $extension || 'jpeg' === $extension ) {
					$extension = 'image/jpg';
				} elseif ( 'png' === $extension ) {
					$extension = 'image/png';
				}
				if ( true === $action && in_array( $extension, Plugin::$images, true ) ) {
						$file_param = [
							'file' => $path . '/' . $filename,
							'mime' => $extension,
						];
						self::create_webp( $file_param, '' );
						array_push( $this->id_array, "$filename" );
				} elseif ( false === $action && 'webp' === $extension ) {
						wp_delete_file( $path . '/' . $filename );
						array_push( $this->id_array, "$filename" );
				}
			}
		}
		return $this;
	}

	/**
	 * Get private static array. Really not worth overloading the class.
	 *
	 * @return array
	 */
	public function get_image_info() : array {
		return $this->image_info;
	}

	/**
	 * Delete webp by attachment ID.
	 *
	 * @param int $id Attachment ID.
	 * @return bool
	 */
	public function delete_action( int $id ) : bool {
		$type = get_post_mime_type( $id );
		if ( ! in_array( $type, Plugin::$images, true ) ) {
			return false;
		} elseif ( 'off' !== Plugin::$options['mode'] ) {
			$found           = false;
			$image_meta_data = wp_get_attachment_metadata( $id );
			$file['file']    = Plugin::$base_path . '/' . $image_meta_data['file'];

			if ( file_exists( $file['file'] . '.webp' ) ) {
				$image_details = getimagesize( $file['file'] );
				$image_path    = dirname( $file['file'] );
				wp_delete_file( $file['file'] . '.webp' );
				$found = true;

				foreach ( $image_meta_data['sizes'] as $size ) {
					if ( file_exists( $image_path . '/' . $size['file'] . '.webp' ) ) {
						wp_delete_file( $image_path . '/' . $size['file'] . '.webp' );
					}
				}

				$backup_sizes = get_post_meta( $id, '_wp_attachment_backup_sizes', true );

				if ( is_array( $backup_sizes ) ) {
					foreach ( $backup_sizes as $size ) {
						if ( file_exists( $image_path . '/' . $size['file'] . '.webp' ) ) {
							wp_delete_file( $image_path . '/' . $size['file'] . '.webp' );
						}
					}
				}
				if ( true === $found ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Create Webp Image.
	 *
	 * @param array   $filearray Local filename.
	 * @param string  $overrides Passed by the action.
	 * @param boolean $ignore Create image or just store in buffer.
	 * @return array
	 */
	public function create_webp( array $filearray, string $overrides, bool $ignore = false ) : array {
		self::$_webp = false;

		if ( 'off' !== Plugin::$options['mode'] ) {
			$image_details = getimagesize( $filearray['file'] );
			if ( ! in_array( $image_details['mime'], Plugin::$images, true ) ) {
				return $filearray;
			}
			self::$_webp = true;
			if ( 'image/png' === $image_details['mime'] ) {
				self::$_img = imagecreatefrompng( $filearray['file'] );
			} elseif ( 'image/jpeg' === $image_details['mime'] ) {
				self::$_img = imagecreatefromjpeg( $filearray['file'] );
			}

			imagepalettetotruecolor( self::$_img );
			imagealphablending( self::$_img, true );
			imagesavealpha( self::$_img, true );

			if ( ! $ignore ) {
				imagewebp( self::$_img, $filearray['file'] . '.webp' );
			}

			self::$_image_size['width']  = $image_details[0];
			self::$_image_size['height'] = $image_details[1];
		}
		return $filearray;
	}

	/**
	 * Make thumbnail webp images.
	 *
	 * @param string $filename Filename to use.
	 * @return string
	 */
	public function make_thumbnails( string $filename ) : string {
		if ( true !== self::$_webp ) {
			return $filename;
		}
		$image_file = rtrim( $filename, '.jpg/.jpeg/.png' );
		$image_file = explode( '-', $image_file );
		$sizes      = explode( 'x', $image_file[ count( $image_file ) - 1 ] );
		$thumb      = imagecreatetruecolor( $sizes[0], $sizes[1] );

		imagecopyresampled( $thumb, self::$_img, 0, 0, 0, 0, $sizes[0], $sizes[1], self::$_image_size['width'], self::$_image_size['height'] );
		imagewebp( $thumb, "$filename.webp" );
		return $filename;
	}

	/**
	 * Clear image stats.
	 *
	 * @return boolean.
	 */
	public function clear_image_info() : bool {
		$this->image_info['images']      = 0;
		$this->image_info['webps']       = 0;
		$this->image_info['images_size'] = 0;
		$this->image_info['webps_size']  = 0;
		return true;
	}

	/**
	 * Image stats.
	 *
	 * @param string $path base folder to search.
	 */
	public function webp_jpeg_png_totals( string $path = null ) : array {
		$path = is_null( $path ) ? $this->_current_path : $path;

		$images      = 0;
		$webps       = 0;
		$images_size = 0;
		$webps_size  = 0;
		foreach ( new \DirectoryIterator( $path ) as $file ) {
			if ( $file->isDot() ) {
				continue;
			}

			if ( $file->isDir() ) {
				$this->webp_jpeg_png_totals( "$path/$file" );
			} else {
				$filename  = $file->getFilename();
				$extension = pathinfo( $filename, PATHINFO_EXTENSION );
				if ( 'jpg' === $extension || 'jpeg' === $extension ) {
					$extension = 'image/jpg';
				} elseif ( 'png' === $extension ) {
					$extension = 'image/png';
				}
				if ( in_array( $extension, Plugin::$images, true ) ) {
					$images_size += $file->getSize();
					$images++;
				} elseif ( 'webp' === $extension ) {
					$webps_size += $file->getSize();
					$webps++;
				}
			}
		}

		$this->image_info['images']      += $images;
		$this->image_info['webps']       += $webps;
		$this->image_info['images_size'] += $images_size;
		$this->image_info['webps_size']  += $webps_size;
		return $this->image_info;
	}

	/**
	 * Media Editor updates.
	 *
	 * @param mixed                    $saved State.
	 * @param string                   $filename Filename.
	 * @param \WP_Image_Editor_Imagick $image Image object.
	 * @param string                   $mime_type MIME Type.
	 * @param int                      $post_id Post ID.
	 * @return bool|null
	 */
	public function editor_update( $saved, string $filename, \WP_Image_Editor_Imagick $image, string $mime_type, int $post_id ) {
		if ( 'off' !== Plugin::$options['mode'] && self::attachment_webp_exists( $post_id ) ) {
			if ( in_array( $mime_type, Plugin::$images, true ) ) {
				$image->save( $filename, $mime_type );
				$saved  = true;
				$ignore = 'thumbnail' === Plugin::$_post['target'];
				self::create_webp(
					array(
						'file' => $filename,
						'mime' => $mime_type,
					),
					'',
					$ignore
				);
			}
		}
		return $saved;
	}

	/**
	 * Set variable for unit testing.
	 *
	 * @param string $path To set private variable to.
	 *
	 * @return bool
	 */
	public function set_current_path( string $path ) : bool {
		$this->_current_path = $path;
		return true;
	}
}
