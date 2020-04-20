<?php
/**
 * AdminTest
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
use WPST\Media_Webp\Admin;
use WPST\Media_Webp\Tests\Stubs;
use WPST\Media_Webp\Tests\Mocks;

class AdminTest extends TestCase {

	/**
	 * @todo Create work flow based tests.
	 *
	 * Tests to evaluate coverage, complexity and behaviour.
	 * These tests can be ran individually.
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

	/**
	 * Try and delete .webp from post
	 * Scenario : webp image removed, covers, i, by user, ii, by event
	 * Expected result: no change (wp_delete_file Mocked), return True
	 */
	public function test_delete_action() {
		Stubs::admin_delete();
		$mock  = Mocks::plugin_mock();
		$class = new Admin();
		$this->assertTrue( $class->delete_action( 2 ) );
		Admin::destroy();
	}

	/**
	 * Try and delete .webp from post that does not have an associated webp image
	 * Scenario : webp image removed between active user operations
	 * Expected result: no change, return False
	 */
	public function test_delete_action_fail_jpg_no_png() {
		Stubs::admin_delete( [ 'wp_get_attachment_metadata', 'get_post_meta' ] );
		$mock  = Mocks::plugin_mock( 2 ); // 2 sets stub images too png only.
		$class = new Admin();
		$this->assertFalse( $class->delete_action( 101 ) );
		Admin::destroy();
	}

	/**
	 * Try and delete .webp from post
	 * Scenario : webp image removed, plugin not enabled, no action
	 * Expected result: no change , return False
	 */
	public function test_delete_action_fail_not_enabled() {
		Stubs::admin_delete( [ 'wp_get_attachment_metadata', 'get_post_meta' ] );
		$mock  = Mocks::plugin_mock( 1, 'off' );
		$class = new Admin();
		$this->assertFalse( $class->delete_action( 101 ) );
	}

	/**
	 * Try and delete .webp from post
	 * Scenario : webp image removed, Image not present, no action
	 * Expected result: no change , return False
	 */
	public function test_delete_action_fail_not_found() {
		Stubs::admin_delete( [ 'wp_get_attachment_metadata', 'get_post_meta' ] );
		$mock  = Mocks::plugin_mock( 1 );
		$class = new Admin();
		$this->assertFalse( $class->delete_action( 2 ) );
	}

	/**
	 * Query webp exists for post attachment
	 * Scenario : Query file exists
	 * Expected response: json object with queried ID present
	 * Expected result: file exists, webp key present, value 'true'
	 */
	public function test_attachment_details_webp_exists() {
		\WP_Mock::userFunction(
			'get_post_mime_type',
			array(
				'id'     => [ 2 ],
				'times'  => 1,
				'return' => 'image/jpg',
			)
		);
		\WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 2 ],
				'times'  => 1,
				'return' => [
					'file'  => 'tmp/test.jpg',
					'sizes' => [ [ 'file' => 'tmp/test-100x200.jpg' ], [ 'file' => 'tmp/test-300x500.jpg' ] ],
				],
			)
		);
		\WP_Mock::userFunction(
			'size_format',
			array(
				'times'  => 1,
				'return' => "100KB",
			)
		);

		$mock     = Mocks::plugin_mock();
		$mock::$base_path = dirname( __DIR__ );
		$expected = [
			'id'   => 2,
			'webp' => 'true',
			'webp_size' => '100KB',
		];

		$class    = new Admin();
		$response = [ 'id' => $expected['id'] ];
		$this->assertEquals( $class->attachment_details_webp_exists( $response ), $expected );
	}

	/**
	 * Query webp exists for post attachment
	 * Scenario : Query file exists
	 * Expected response: json object with queried ID present
	 * Expected result: file exists, webp key present, value 'true'
	 */
	public function test_attachment_details_webp_exists_convert() {
		\WP_Mock::userFunction(
			'get_post_mime_type',
			array(
				'id'     => [ 3 ],
				'times'  => 1,
				'return' => 'image/png',
			)
		);
		\WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 3 ],
				'times'  => 1,
				'return' => [
					'file'  => 'tmp/test.png',
					'sizes' => [],
				],
			)
		);
/* 		\WP_Mock::userFunction(
			'wp_upload_dir',
			array(
				'times'  => 1,
				'return' => [ 'basedir' => dirname( __DIR__ ) ],
			)
		); */
		$mock     = Mocks::plugin_mock();
		$expected = [
			'id'   => 3,
			'webp' => 'convert',
		];
		$class    = new Admin();
		$response = [ 'id' => $expected['id'] ];
		$this->assertEquals( $class->attachment_details_webp_exists( $response ), $expected );
	}

	/**
	 * Try and retrieve details for attachment that does not exist
	 * Expected result: no change
	 */
	public function test_attachment_details_webp_exists_false() {
		\WP_Mock::userFunction(
			'get_post_mime_type',
			array(
				'id'     => [ 4 ],
				'times'  => 1,
				'return' => 'images/bmp',
			)
		);
		\WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 4 ],
				'times'  => 1,
				'return' => [
					'file'  => 'tmp/test2.bmp',
					'sizes' => [ [ 'file' => '/tmp/test2-100x200.bmp' ], [ 'file' => '/tmp/test2-300x500.bmp' ] ],
				],
			)
		);
/* 		\WP_Mock::userFunction(
			'wp_upload_dir',
			array(
				'times'  => 1,
				'return' => [ 'basedir' => dirname( __DIR__ ) ],
			)
		); */
		$mock     = Mocks::plugin_mock();
		$expected = [ 'id' => 4 ];
		$class    = new Admin();
		$this->assertEquals( $class->attachment_details_webp_exists( $expected ), $expected );

	}

	/**
	 * Calculate files sizes
	 * Expected result: matching arrays
	 */
	public function test_webp_jpeg_png_totals() {
		$mock     = Mocks::plugin_mock();
		$class    = new Admin();
		$expected = [
			'message'               => '',
			'storage'               => 0,
			'images_size'           => 2288,
			'webps_size'            => 44,
			'images'                => 4,
			'webps'                 => 1,
		];
		$this->assertEquals( $class->webp_jpeg_png_totals( dirname( __DIR__ ) ), $expected );
		$class->set_current_path( dirname( __DIR__ ) );
		$class->clear_image_info();
		$this->assertEquals( $class->webp_jpeg_png_totals(), $expected );
		$image_info = $class->get_image_info();
		$this->assertEquals( $image_info, $expected );
	}

	/**
	 * Method triggered when theme switched
	 * Remove old theme webp images
	 * Expected result: matching arrays
	 */
	public function test_manage_theme_jpeg_png_delete() {
		$mock = Mocks::plugin_mock();
		\WP_Mock::userFunction(
			'wp_delete_file',
			array(
				'return' => 'true',
			)
		);
		$class = new Admin();
		$this->assertEquals( $class->manage_theme_jpeg_png( false, dirname( __DIR__ ), true ), $class );
		$id_array = [ 'test.jpg.webp' ];
		$this->assertEquals( $class->id_array, $id_array );
	}

	/**
	 * Method triggered when theme switched
	 * Create new theme webp images
	 * Expected result: matching arrays
	 */
	public function test_manage_theme_jpeg_png_create() {
		$mock = Mocks::plugin_mock();
		\WP_Mock::userFunction(
			'wp_delete_file',
			array(
				'return' => 'true',
			)
		);
		$class = new Admin();
		$this->assertEquals( $class->manage_theme_jpeg_png( true, dirname( __DIR__ ), true ), $class );
		$id_array = [ 'test-100x200.jpg', 'test-300x500.jpg', 'test.jpg', 'test.png' ];
		$this->assertEquals( $class->id_array, $id_array );
	}

	/**
	 * Method triggered when theme switched
	 * Create new theme webp images
	 * Expected result: matching arrays
	 */
	public function test_create_webp_wrong_type() {
		$mock                   = \Mockery::mock( 'WPST\Media_Webp\Plugin' );
		$mock::$images          = $this->mime_types[1];
		$mock::$options['mode'] = 'on';
		$mock::$base_path       = dirname( __DIR__ );
		\WP_Mock::userFunction(
			'wp_delete_file',
			array(
				'return' => 'true',
			)
		);
		$filearray = [
			'file' => dirname( __DIR__ ) . '\\tmp\\test.png',
			'type' => 'image/png',
		];
		$class     = new Admin();

		$this->assertEquals( $class->create_webp( $filearray, '' ), $filearray );

	}

	/**
	 * Test core plugin method
	 * Create new webp image, just png images
	 * Expected result: True
	 */
	public function test_create_webp_jpg() {
		$mock = Mocks::plugin_mock();
		\WP_Mock::userFunction(
			'wp_delete_file',
			array(
				'return' => 'true',
			)
		);
		$filearray = [
			'file' => dirname( __DIR__ ) . '\\tmp\\test.jpg',
			'type' => 'image/jpg',
		];
		$class     = new Admin();

		$this->assertEquals( $class->create_webp( $filearray, '' ), $filearray );
	}

	/**
	 * Test core plugin method
	 * Create new webp image, just png images
	 * Expected result: True
	 */
	public function test_create_webp_png() {
		$mock = Mocks::plugin_mock( 2 );
		\WP_Mock::userFunction(
			'wp_delete_file',
			array(
				'return' => 'true',
			)
		);
		$filearray   = [
			'file' => dirname( __DIR__ ) . '\\tmp\\test.png',
			'type' => 'image/png',
		];
		$this->class = new Admin();
		$this->assertEquals( $this->class->create_webp( $filearray, '' ), $filearray );

		// remove test image from tmp folder.
		unlink( dirname( __DIR__ ) . "\\tmp\\test.png.webp" );

	}

	/**
	 * Test core plugin method
	 * Create new webp image thumbnails, all images
	 * Expected result: True
	 */
	public function test_make_thumbnails() {
		$this->class = new Admin();
		$this->assertEquals( $this->class->make_thumbnails( dirname( __DIR__ ) . "\\tmp\\test-100x200.png" ), dirname( __DIR__ ) . "\\tmp\\test-100x200.png" );

		// remove test images from tmp folder.
		$id_array = [ 'test-100x200.png', 'test-300x500.jpg', 'test-100x200.jpg' ];
		foreach ( $id_array as $id ) {
			unlink( dirname( __DIR__ ) . "\\tmp\\" . $id . '.webp' );
		}
	}
	/**
	 * Test core plugin method
	 *
	 * Create new webp image thumbnails, image does not exist
	 * Expected result: False
	 */
	public function test_make_thumbnails_false() {
		Admin::init();
		$this->class = new Admin();
		$this->assertEquals( $this->class->make_thumbnails( dirname( __DIR__ ) . "\\tmp\\test-100x200.png" ), dirname( __DIR__ ) . "\\tmp\\test-100x200.png" );
	}

	/**
	 * Test core plugin method
	 *
	 * Triggered when the user edits an image via the admin interface
	 * Expected result: True
	 */
	public function test_editor_update() {
		$mock                   = Mocks::plugin_mock();
		$mock::$_post['target'] = 'thumbnail';
		$mock::$base_path       = dirname( __DIR__ );
		$class                  = Admin::get_instance();
/* 		\WP_Mock::userFunction(
			'update_post_meta',
			array(
				'times' => 1,
			)
		); */

		\WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 2 ],
				'times'  => 1,
				'return' => [
					'file'  => 'tmp/test.jpg',
					'sizes' => [ [ 'file' => 'tmp/test-100x200.jpg' ], [ 'file' => 'tmp/test-300x500.jpg' ] ],
				],
			)
		);

		$filename  = dirname( __DIR__ ) . "\\tmp\\test.jpg";
		$mime_type = 'image/jpg';
		$image     = \Mockery::mock( '\WP_Image_Editor_Imagick' );
		$image->shouldReceive( 'save' )
		->times( 1 )
		->andReturn( true );
		$this->assertTrue( $class->editor_update( '', $filename, $image, $mime_type, 2 ) );

	}

	public function tearDown() {
		\WP_Mock::tearDown();
		parent::tearDown();
	}

}
