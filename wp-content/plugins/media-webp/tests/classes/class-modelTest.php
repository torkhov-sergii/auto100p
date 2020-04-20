<?php
/**
 * ModelTest
 *
 * Unit Tests.
 *
 * @category   Unit Test
 * @package    WordPress
 * @subpackage WPST\Media_Webp
 * @author     Steven Turner <steveturner23@hotmail.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 */

use WP_Mock\Tools\TestCase;
use WPST\Media_Webp\Model;

	/**
	 * Basic model tests.
	 */

class ModelTest extends TestCase {

	protected $mime_types = [
		[ 'image/jpg', 'image/jpeg', 'image/png' ],
		[ 'image/jpg', 'image/jpeg' ],
		[ 'image/png' ],
	];

	public function setup() {
		parent::setUp();
		\WP_Mock::setUp();
	}

	public function test_construct_mime_types() {

		foreach ( $this->mime_types as $mime_type ) {
			$model = new Model( $mime_type );
			$this->assertEquals( $model->get_accepted_types(), $mime_type );
		}
	}

	public function test_get_image_attachments() {
		$model = new Model( [ 'image/jpg', 'image/jpeg', 'image/png' ] );

		$args = [
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'post_mime_type' => [ 'image/jpg', 'image/jpeg', 'image/png' ],
		];

		$attachments    = [];
		$attachment     = new stdClass();
		$attachment->ID = '101';
		array_push( $attachments, $attachment );

		// Mocked get_posts, there is not much value in this test unit.

		\WP_Mock::userFunction(
			'get_posts',
			array(
				'args'   => [ $args ],
				'times'  => 1,
				'return' => $attachments,
			)
		);

		$model->get_image_attachments();

		$this->assertEquals( $model->attachments, $attachments );
	}

	public function tearDown() {
		\WP_Mock::tearDown();
		parent::tearDown();
	}
}
