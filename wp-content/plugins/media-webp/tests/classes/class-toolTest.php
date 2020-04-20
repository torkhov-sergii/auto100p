<?php
/**
 * ToolsTest
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
use WPST\Media_Webp\Tool;

class ToolTest extends TestCase {

	/**
	 * Test form render.
	 */

	protected $mime_types = [
		[ 'image/jpg', 'image/jpeg', 'image/png' ],
		[ 'image/jpg', 'image/jpeg' ],
		[ 'image/png' ],
	];

	public function setup() {
		parent::setUp();
		\WP_Mock::setUp();
	}
	public function test_show() {

		\WP_Mock::userFunction(
			'wp_nonce_field',
			array(
				'return' => '123fake123',
			)
		);

		$image_info             = [
			'message'                      => 'Unit Test',
			'storage'                      => 0,
			'attachments'                  => 0,
			'attachments_with_webps'       => 0,
			'attachment_images_size' => 0,
			'attachment_webps_size'  => 0,
			'attachment_images'      => 0,
			'attachment_webps'       => 0,
			'theme_images_size'      => 0,
			'theme_webps_size'       => 0,
			'theme_images'           => 0,
			'theme_webps'            => 0,
		];
		$mock                   = \Mockery::mock( '\WPST\Media_Webp\Plugin' );
		$mock::$images          = $this->mime_types[0];
		$mock::$options['mode'] = 'on';
		$mock::$base_path       = __DIR__;
		ob_start();
		$class  = new Tool( $image_info );
		$output = ob_get_clean();

		$count = substr_count( $output, 'id="storage"' );
		$this->assertEquals( $count, 1 );
		$count = substr_count( $output, 'id="attachment_webps"' );
		$this->assertEquals( $count, 1 );
		$count = substr_count( $output, 'id="attachment_webps_size"' );
		$this->assertEquals( $count, 1 );
		$count = substr_count( $output, 'id="button_convert"' );
		$this->assertEquals( $count, 1 );
		$count = substr_count( $output, 'id="button_delete"' );
		$this->assertEquals( $count, 1 );
		$count = substr_count( $output, 'id="button_convert_theme"' );
		$this->assertEquals( $count, 1 );
		$count = substr_count( $output, 'id="button_delete_theme"' );
		$this->assertEquals( $count, 1 );
		$count = substr_count( $output, 'class="media_wts"' );
		$this->assertEquals( $count, 11 );
	}
	public function tearDown() {
		\WP_Mock::tearDown();
		parent::tearDown();
	}
}
