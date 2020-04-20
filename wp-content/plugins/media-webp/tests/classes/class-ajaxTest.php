<?php
/**
 * AjaxTest
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
use WPST\Media_Webp\Ajax;
use WPST\Media_Webp\Tests\Mocks;
use WPST\Media_Webp\Tests\Stubs;

/**
 * All AJAX methods are routed via the Plugin class, media_webp_callback method.
 * These tests are to run concurrently. Each function is independent within the
 * class.
 */
class AjaxTest extends TestCase {

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
	 * Ajax called : confirm webp exists
	 * method webp_ids()
	 * Used on the tools form to calculate delete requests (flag = false)
	 * Expected result: matching arrays
	 */
	public function test_webp_ids_exist() {
		\WP_Mock::userFunction(
			'wp_upload_dir',
			array(
				'times'  => 1,
				'return' => [ 'basedir' => dirname( __DIR__ ) ],
			)
		);

		\WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => [
					'file'  => 'tmp/test.jpg',
					'mime'  => 'image/jpg',
					'sizes' => [ [ 'file' => '/tmp/test-100x200.jpg' ], [ 'file' => '/tmp/test-300x500.jpg' ] ],
				],
			)
		);

		\WP_Mock::userFunction(
			'wp_send_json_success',
			array(
				'times'  => 1,
				'return' => [ true ],
			)
		);

		$model = Mocks::model_mock();
		$stub  = Stubs::plugin_stub( false, [] );
		$class = Ajax::get_instance();
		$json  = (object) [
			'result' => 1,
			'total'  => 1,
			'id_s'   => [ 0 => '101' ],
		];
		$this->assertEquals( $class->set_model_( $model )->webp_ids( [ 'flag' => false ] ), $json );
		$stub->destroy();
	}

	/**
	 * Ajax called : confirm webp exists
	 * method webp_ids()
	 * Used on the tools form to calculate delete requests (flag = false)
	 * Expected result: matching arrays
	 */
	public function test_webp_ids_missing() {

		\WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => [
					'file'  => 'tmp/test.png',
					'mime'  => 'image/png',
					'sizes' => [],
				],
			)
		);

		\WP_Mock::userFunction(
			'wp_send_json_success',
			array(
				'times'  => 1,
				'return' => [ true ],
			)
		);

		$model = Mocks::model_mock();
		$mock  = Mocks::plugin_mock();

		$class = Ajax::get_instance();
		$json  = (object) [
			'result' => 1,
			'total'  => 0,
			'id_s'   => [],
		];
		$this->assertEquals( $class->set_model_( $model )->webp_ids( [ 'flag' => false ] ), $json );
	}

	/**
	 * Ajax called : confirm webp exists
	 * method webp_ids()
	 * Use real model class
	 * Used on the tools form to calculate delete requests (flag = false)
	 * Expected result: matching arrays
	 */
	public function test_webp_ids_real_model() {

		\WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => [
					'file'  => 'tmp/test.jpg',
					'mime'  => 'image/jpg',
					'sizes' => [ [ 'file' => '/tmp/test-100x200.jpg' ], [ 'file' => '/tmp/test-300x500.jpg' ] ],
				],
			)
		);

		\WP_Mock::userFunction(
			'wp_send_json_success',
			array(
				'times'  => 1,
				'return' => [ true ],
			)
		);

		$args = [
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'post_mime_type' => $this->mime_types[0],
		];

		$attachments    = [];
		$attachment     = new \WP_Post();
		$attachment->ID = '101';
		array_push( $attachments, $attachment );

		\WP_Mock::userFunction(
			'get_posts',
			array(
				'args'   => [ $args ],
				'times'  => 1,
				'return' => $attachments,
			)
		);

		$mock  = Mocks::plugin_mock();
		$class = Ajax::get_instance();
		$class->clear_model_();
		$json = (object) [
			'result' => 1,
			'total'  => 0,
			'id_s'   => [],
		];
		$this->assertEquals( $class->webp_ids( [ 'flag' => false ] ), $json );
	}

	/**
	 * Ajax called : confirm webp exists
	 * method webp_ids()
	 * No matched returned.
	 * Used on tools form to calculate delete requests (flag = false)
	 * Expected result: matching arrays
	 */
	public function test_webp_ids_no_matches() {

		\WP_Mock::userFunction(
			'wp_send_json_success',
			array(
				'times'  => 1,
				'return' => [],
			)
		);

		$model              = Mocks::model_mock();
		$model->attachments = null;
		$mock               = Mocks::plugin_mock();

		$class = Ajax::get_instance();
		$json  = (object) [
			'result' => 0,
			'total'  => 0,
			'id_s'   => [],
		];

		$this->assertEquals( $class->set_model_( $model )->webp_ids( [ 'flag' => false ] ), $json );
	}

	/**
	 * Ajax called : confirm webp exists
	 * method webp_ids()
	 * No matched returned.
	 * Used on the tools form to calculate delete requests (flag = false)
	 * Expected result: matching arrays
	 */
	public function test_webp_manage_delete() {

		\WP_Mock::userFunction(
			'wp_delete_file',
			array(
				'return' => 'true',
			)
		);

		\WP_Mock::userFunction(
			'get_post_mime_type',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => 'image/jpg',
			)
		);

		\WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => [
					'file'  => 'tmp/test.jpg',
					'mime'  => 'image/jpg',
					'sizes' => [ [ 'file' => '/tmp/test-100x200.jpg' ], [ 'file' => '/tmp/test-300x500.jpg' ] ],
				],
			)
		);

		$json = (object) [
			'success' => true,
		];

		\WP_Mock::userFunction(
			'wp_send_json_success',
			array(
				'times'  => 1,
				'return' => [ $json ],
			)
		);

		$attachments = null;

		$model              = \Mockery::mock( '\WPST\Media_Webp\Model' );
		$model->attachments = $attachments;
		$mock               = Mocks::plugin_mock();

		$class = Ajax::get_instance();
		$json  = (object) [
			'result' => 0,
			'total'  => 0,
			'id_s'   => [],
		];
		$post  = [
			'flag'    => false,
			'details' => false,
			'id'      => 101,
		];
		$this->assertTrue( $class->set_model_( $model )->webp_manage( $post ), $json );
	}

	/**
	 * Ajax called : confirm webp exists
	 * method webp_ids()
	 * No matched returned.
	 * Used on the tools form to calculate delete requests (flag = false)
	 * Expected result: matching arrays
	 */
	public function test_webp_manage_create() {
		\WP_Mock::userFunction(
			'get_post_meta',
			array(
				'args'   => array( 101, '_wp_attachment_backup_sizes', true ),
				'id'     => [ 101 ],
				'times'  => 3,
				'return' => [
					[ 'file' => 'test-100x200.jpg' ],
					[ 'file' => 'test-300x500.jpg' ],
				],
			)
		);

		\WP_Mock::userFunction(
			'wp_delete_file',
			array(
				'return' => 'true',
			)
		);

		\WP_Mock::userFunction(
			'get_post_mime_type',
			array(
				'id'     => [ 101 ],
				'times'  => 0,
				'return' => 'image/jpg',
			)
		);

		\WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 101 ],
				'times'  => 4,
				'return' => [
					'file'  => 'tmp/test.jpg',
					'mime'  => 'image/jpg',
					'sizes' => [ [ 'file' => 'test-100x200.jpg' ], [ 'file' => 'test-300x500.jpg' ] ],
				],
			)
		);

		$json = (object) [
			'success' => true,
		];

		\WP_Mock::userFunction(
			'wp_send_json_success',
			array(
				'times'  => 3,
				'return' => [ $json ],
			)
		);

		\WP_Mock::userFunction(
			'size_format',
			array(
				'times'  => 3,
				'return' => '100KB',
			)
		);
		$mock = Mocks::plugin_mock();

		$class = Ajax::get_instance();
		$json  = (object) [
			'result' => 0,
			'total'  => 0,
			'id_s'   => [],
		];
		$post  = [
			'flag'    => true,
			'details' => false,
			'id'      => 101,
		];
		$this->assertTrue( $class->webp_manage( $post ), $json );
		$model = Mocks::model_mock();

		$post = [
			'flag'    => true,
			'details' => 'post',
			'id'      => 101,
		];
		$this->assertTrue( $class->set_model_( $model )->webp_manage( $post ), $json );

		$post = [
			'flag'    => true,
			'details' => 'tool',
			'id'      => 101,
		];
		$this->assertTrue( $class->set_model_( $model )->webp_manage( $post ), $json );
		$id_array = [ 'test-300x500.jpg', 'test-100x200.jpg' ];
		foreach ( $id_array as $id ) {
			unlink( dirname( __DIR__ ) . "\\tmp\\" . $id . '.webp' );
		}

	}

	/**
	 * Ajax called : confirm webp exists
	 * method webp_ids()
	 * No matched returned.
	 * Used on the tools form to calculate delete requests (flag = false)
	 * Expected result: matching arrays
	 */
	public function test_webp_manage_create_no_model() {

		\WP_Mock::userFunction(
			'wp_delete_file',
			array(
				'return' => 'true',
			)
		);

		\WP_Mock::userFunction(
			'get_post_mime_type',
			array(
				'id'     => [ 101 ],
				'times'  => 0,
				'return' => 'image/jpg',
			)
		);

		\WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => [
					'file'  => 'tmp/test.jpg',
					'mime'  => 'image/jpg',
					'sizes' => [ [ 'file' => 'test-100x200.jpg' ], [ 'file' => 'test-300x500.jpg' ] ],
				],
			)
		);

		$args = [
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'post_mime_type' => $this->mime_types[0],
		];

		$attachments    = [];
		$attachment     = new \WP_Post();
		$attachment->ID = '101';
		array_push( $attachments, $attachment );

		$json = (object) [
			'success' => true,
		];

		\WP_Mock::userFunction(
			'wp_send_json_success',
			array(
				'times'  => 1,
				'return' => [ $json ],
			)
		);

		\WP_Mock::userFunction(
			'size_format',
			array(
				'times'  => 0,
				'return' => [],
			)
		);

		$mock = Mocks::plugin_mock();

		$class = Ajax::get_instance();
		$class->clear_model_();
		$json = (object) [
			'result' => 0,
			'total'  => 0,
			'id_s'   => [],
		];
		$post = [
			'flag'    => true,
			'details' => true,
			'id'      => 101,
		];

		$this->assertTrue( $class->webp_manage( $post ), $json );
		$id_array = [ 'test-300x500.jpg', 'test-100x200.jpg' ];
		foreach ( $id_array as $id ) {
			unlink( dirname( __DIR__ ) . "\\tmp\\" . $id . '.webp' );
		}

	}

	/**
	 * Ajax called : confirm webp exists
	 * method webp_ids()
	 * No matched returned.
	 * Used on the tools form to calculate delete requests (flag = false)
	 * Expected result: matching arrays
	 */
	public function test_webp_manage_create_png() {
		\WP_Mock::userFunction(
			'get_post_meta',
			array(
				'args'   => array( 101, '_wp_attachment_backup_sizes', true ),
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => [],
			)
		);

		\WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => [
					'file'  => 'tmp/test.png',
					'mime'  => 'image/png',
					'sizes' => [],
				],
			)
		);

		$json = (object) [
			'success' => true,
		];

		\WP_Mock::userFunction(
			'wp_send_json_success',
			array(
				'times'  => 1,
				'return' => [ $json ],
			)
		);

		$mock = Mocks::plugin_mock();

		$class = Ajax::get_instance();
		$json  = (object) [
			'result' => 0,
			'total'  => 0,
			'id_s'   => [],
		];
		$post  = [
			'flag'    => true,
			'details' => false,
			'id'      => 101,
		];
		$this->assertTrue( $class->webp_manage( $post ), $json );
		unlink( dirname( __DIR__ ) . "\\tmp\\test.png.webp" );
	}

	/**
	 * Ajax called : confirm webp exists
	 * method webp_ids()
	 * No matched returned.
	 * Used on the tools form to calculate delete requests (flag = false)
	 * Expected result: matching arrays
	 */
	public function test_webp_theme() {
		$json = (object) [
			'success' => true,
		];

		\WP_Mock::userFunction(
			'wp_send_json',
			array(
				'times'  => 1,
				'return' => [ $json ],
			)
		);

		\WP_Mock::userFunction(
			'get_template_directory',
			array(
				'times'  => 1,
				'return' => dirname( __DIR__ ) . '//tmp//',
			)
		);

		\WP_Mock::userFunction(
			'size_format',
			array(
				'times'  => 1,
				'return' => [],
			)
		);

		$mock  = Mocks::plugin_mock();
		$model = Mocks::model_mock();

		$class = Ajax::get_instance();
		$post  = [
			'flag'    => true,
			'details' => true,
			'id'      => 101,
		];
		$this->assertTrue( $class->set_model_( $model )->webp_theme( $post ) );
		$id_array = [ 'test-300x500.jpg', 'test-100x200.jpg', 'test.png' ];
		foreach ( $id_array as $id ) {
			unlink( dirname( __DIR__ ) . "\\tmp\\" . $id . '.webp' );
		}
	}

	/**
	 * Ajax called : confirm webp exists
	 * method webp_ids()
	 * No matched returned.
	 * Used on the tools form to calculate delete requests (flag = false)
	 * Expected result: matching arrays
	 */
	public function test_webp_theme_disabled() {
		$json = (object) [
			'success' => true,
		];

		\WP_Mock::userFunction(
			'get_template_directory',
			array(
				'times'  => 1,
				'return' => dirname( __DIR__ ) . '//tmp//',
			)
		);

		\WP_Mock::userFunction(
			'wp_send_json',
			array(
				'times'  => 1,
				'return' => [ $json ],
			)
		);
		$class = Ajax::get_instance();
		$post  = [
			'flag'    => true,
			'details' => true,
			'id'      => 101,
		];
		$this->assertTrue( $class->webp_theme( $post ) );
	}

	/**
	 * Ajax called : confirm webp exists
	 * method webp_ids()
	 * No matched returned.
	 * Used on the tools form to calculate delete requests (flag = false)
	 * Expected result: matching arrays
	 */
	public function test_webp_theme_switch_fail() {
		$json = (object) [
			'success' => true,
		];

		$mock  = Mocks::plugin_mock( 0, 'off' );
		$class = Ajax::get_instance();
		$post  = [
			'flag'    => true,
			'details' => true,
			'id'      => 101,
		];
		$this->assertFalse( $class->theme_switch() );
	}

	/**
	 * Ajax called : confirm webp exists
	 * method webp_ids()
	 * No matched returned.
	 * Used on the tools form to calculate delete requests (flag = false)
	 * Expected result: matching arrays
	 */
	public function test_webp_theme_switch_pass() {
		$json = (object) [
			'success' => true,
		];

		\WP_Mock::userFunction(
			'get_template_directory',
			array(
				'times'  => 1,
				'return' => dirname( __DIR__ ) . '//tmp//',
			)
		);

		\WP_Mock::userFunction(
			'size_format',
			array(
				'times'  => 1,
				'return' => [],
			)
		);

		$mock = Mocks::plugin_mock();

		$class = Ajax::get_instance();
		$post  = [
			'flag'    => true,
			'details' => true,
			'id'      => 101,
		];
		$this->assertTrue( $class->theme_switch() );
		$id_array = [ 'test-300x500.jpg', 'test-100x200.jpg', 'test.png' ];
		foreach ( $id_array as $id ) {
			unlink( dirname( __DIR__ ) . "\\tmp\\" . $id . '.webp' );
		}
	}

	public function tearDown() {
		\WP_Mock::tearDown();
		parent::tearDown();
	}
}
