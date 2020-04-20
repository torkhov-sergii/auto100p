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
 * Model class.
 *
 * Simple class to return compatible attachments. WordPress DB interaction.
 *
 * @category   Class
 * @package    Model
 * @see        Plugin
 **/
class Model {

	/**
	 * Returned objects.
	 *
	 * @var \WP_Posts
	 */
	public $attachments;

	/**
	 * Attachments filter.
	 *
	 * @var Array
	 */
	protected $accepted_mime_types;

	/**
	 * Construct requires pass through array.
	 *
	 * @param array $mime_types Passed plugin mime_types option array.
	 */
	final public function __construct( array $mime_types ) {
		$this->accepted_mime_types = $mime_types;
	}

	/**
	 * Get model
	 *
	 * @return this
	 */
	public function get_image_attachments() : Model {
		$args              = [
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'post_mime_type' => $this->accepted_mime_types,
		];
		$attachments       = get_posts( $args );
		$this->attachments = $attachments;
		return $this;
	}

	/**
	 * Get currently set accepted image types.
	 *
	 * @return Array
	 */
	public function get_accepted_types() : array {
		return $this->accepted_mime_types;
	}
}
