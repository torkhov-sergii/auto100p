<?php
/**
 * PluginTest
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
use WPST\Media_Webp\Plugin;
use WPST\Media_Webp\Ajax;
use WPST\Media_Webp\Tests\Stubs;
use WPST\Media_Webp\Tests\Mocks;

/*
*  Basic Unit tests to ensure normal plugin operations
*  These class tests are designed to be ran as one test
*  pattern as there is presidency between some of the
*  methods due to the class being a Singleton.  I have
*  added a destroy method to the trait to allow for
*  easier testing.
*/


class PluginTest extends TestCase {

	protected $mime_types = [
		[ 'image/jpg', 'image/jpeg', 'image/png' ],
		[ 'image/jpg', 'image/jpeg' ],
		[ 'image/png' ],
	];

	public function setup() {
		parent::setUp();
		\WP_Mock::setUp();
	}

	public function tearDown() {
		\WP_Mock::tearDown();
		parent::tearDown();
	}

	/**
	 * Plugin initialised, wrong WordPress version.
	 * Expected results: Instance of plugin returned.
	 */
	public function test_init_wp_version_fail() {
		Plugin::destroy();
		$stub = Stubs::plugin_stub(
			false,
			array(
				'wp_upload_dir'    => 1,
				'is_admin'         => 0,
				'get_option'       => 0,
				'is_multisite'     => 0,
				'plugin_basename'  => 0,
				'is_network_admin' => 0,
			),
			'3.5-RC1-src'
		);
		$this->assertInstanceOf( 'WPST\Media_Webp\Plugin', $stub );
		$stub->destroy();
	}

	/**
	 * Plugin initialised, all images in options.
	 * update options triggered.
	 * Expected results: True & Instance of plugin returned.
	 */
	public function test_init_all_images() {
		$stub = Stubs::plugin_stub( false, array() );
		$this->assertInstanceOf( 'WPST\Media_Webp\Plugin', $stub );

		\WP_Mock::userFunction(
			'update_option',
			array(
				'times'  => 1,
				'return' => true,
			)
		);

		$this->assertEquals( $stub->update_options(), 1 );
		$this->assertFalse( $stub->networkactive() );

		$stub->destroy();
	}

	/**
	 * Plugin initialised, with post data to trigger check submission method
	 * Expected results: Instance of plugin returned.
	 */
	public function test_init_submission() {
		$GLOBALS['_POST'] = [ 'test' => 1 ];
		$stub             = Stubs::plugin_stub( false, array() );
		$this->assertInstanceOf( 'WPST\Media_Webp\Plugin', $stub );
		$stub->destroy();
	}

	/**
	 * Plugin initialised, just jpg
	 * Expected results: Instance of plugin returned.
	 */
	public function test_init_jpg() {
		$params = [
			'images' => 'jpeg',
		];
		$stub   = Stubs::plugin_stub( false, $params );
		$this->assertInstanceOf( 'WPST\Media_Webp\Plugin', $stub );
		$stub->destroy();
	}

	/**
	 * Plugin initialised, just png
	 * Expected results: Instance of plugin returned.
	 */
	public function test_init_png() {
		$params = [
			'images' => 'png',
		];
		$stub   = Stubs::plugin_stub( false, $params );
		$this->assertInstanceOf( 'WPST\Media_Webp\Plugin', $stub );
		$stub->destroy();
	}

	/**
	 * Plugin initialised, multi site
	 * update method also triggered
	 * Expected results: True, Instance of plugin returned.
	 */
	public function test_init_networkactive() {
		$params = [
			'get_site_option'            => 2,
			'get_option'                 => 0,
			'plugin_basename'            => 2,
			'register_deactivation_hook' => 1,
			'is_multisite'               => 1,
		];
		$stub   = Stubs::plugin_stub( true, $params );
		$this->assertInstanceOf( 'WPST\Media_Webp\Plugin', $stub );
		\WP_Mock::userFunction(
			'update_site_option',
			array(
				'times'  => 1,
				'return' => true,
			)
		);
		// possible returned results from the update_options function are 0 = fail, 1 = success, 2 = no change.
		$this->assertEquals( $stub->update_options(), 1 );
		$stub->destroy();
	}

	/**
	 * Plugin initialised, run update_scripts method, no plugin page given
	 * Expected results: True, Instance of plugin returned.
	 */
	public function test_updated_scripts_fail() {
		$stub = Stubs::plugin_stub( false, array() );
		$this->assertFalse( $stub->updated_scripts() );
		$this->assertInstanceOf( 'WPST\Media_Webp\Plugin', $stub );
		$stub->destroy();
	}

	/**
	 * Plugin initialised, update scripts, correct page.
	 * Expected results: True, Instance of plugin returned.
	 */
	public function test_updated_scripts_pass_just_style() {
		global $plugin_page;
		$stub = Stubs::plugin_stub( false, array() );
		\WP_Mock::userFunction(
			'wp_enqueue_style',
			array(
				'times'  => 1,
				'return' => true,
			)
		);

		$plugin_page = 'media-webp_tools';
		$this->assertTrue( $stub->updated_scripts() );
		$this->assertInstanceOf( 'WPST\Media_Webp\Plugin', $stub );
		$stub->destroy();
	}

	/**
	 * Plugin initialised, update scripts, media options
	 * Expected results: True, Instance of plugin returned.
	 */
	public function test_updated_scripts_pass_js_and_style() {
		global $plugin_page;

		\WP_Mock::userFunction(
			'wp_enqueue_style',
			array(
				'times'  => 1,
				'return' => true,
			)
		);

		\WP_Mock::userFunction(
			'wp_localize_script',
			array(
				'times'  => 1,
				'return' => true,
			)
		);

		\WP_Mock::userFunction(
			'wp_enqueue_script',
			array(
				'times'  => 2,
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'admin_url',
			array(
				'times'  => 1,
				'return' => true,
			)
		);

		\WP_Mock::userFunction(
			'wp_create_nonce',
			array(
				'times'  => 1,
				'return' => true,
			)
		);
		$plugin_page    = 'media-webp_tools';
		$stub           = Stubs::plugin_stub( false, array() );
		$stub::$pagenow = 'post.php';
		$this->assertTrue( $stub->updated_scripts() );
		$this->assertInstanceOf( 'WPST\Media_Webp\Plugin', $stub );
		$stub->destroy();
	}

	/**
	 * Plugin initialised, trigger switch theme method
	 * Expected results: Instance of plugin returned.
	 */
	public function test_theme_switch() {
		$stub = Stubs::plugin_stub( false, array() );
		\WP_Mock::userFunction(
			'get_template_directory',
			array(
				'times'  => 1,
				'return' => dirname( __DIR__ ) . '//tmp//',
			)
		);

		\WP_Mock::userFunction(
			'wp_delete_file',
			array(
				'return' => 'true',
			)
		);

		\WP_Mock::userFunction(
			'size_format',
			array(
				'times'  => 1,
				'return' => [],
			)
		);
		$stub->theme_switch();
		$this->assertInstanceOf( 'WPST\Media_Webp\Plugin', $stub );
		$stub->destroy();
	}

	/**
	 * Plugin initialised, add webp icon to images
	 * within the media gallery. Grid view.
	 * Expected results: match.
	 */
	public function test_add_webp_list_exists() {
		$response = [
			'id'   => 101,
			'webp' => 'true',
		];

		$stub  = Stubs::plugin_stub( false, array() );
		$admin = Mocks::admin_mock();
		$admin->shouldReceive( 'attachment_details_webp_exists' )
		->andReturn( $response );
		$title = 'test title';
		ob_start();
		$stub::$pagenow = 'upload.php';
		$stub->add_webp_list( $title, 101, $admin );
		$output = ob_get_clean();
		$count  = substr_count( $output, 'webp_found' );
		$this->assertEquals( $count, 1 );
		$stub->destroy();
	}

	/**
	 * Plugin initialised, add webp icon to images (none found)
	 * within the media gallery. Grid view.
	 * Expected results: no matches.
	 */
	public function test_add_webp_list_no_images() {
		$stub = Stubs::plugin_stub( false, array() );
		\WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 102 ],
				'times'  => 1,
				'return' => [
					'file'  => '',
					'sizes' => [],
				],
			)
		);

		$title             = 'test title';
		$stub::$current_id = 0;
		ob_start();
		$stub::$pagenow = 'upload.php';
		$stub->add_webp_list( $title, 101 );
		$output = ob_get_clean();
		$count  = substr_count( $output, 'webp_found' );
		$this->assertEquals( $count, 0 );
		$stub->destroy();
	}

	/**
	 * Add webp meta box to post.php form test
	 * Expected results: True.
	 */
	public function test_media_webp_callback_found() {
		Ajax::destroy();
		$stub  = Stubs::plugin_stub( false, array( 'wp_upload_dir' => 2 ) );
		$_POST = [
			'flag'            => true,
			'details'         => true,
			'id'              => 101,
			'callback_action' => 'webp_theme',
		];

		\WP_Mock::userFunction(
			'wp_unslash',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => true,
			)
		);

		\WP_Mock::userFunction(
			'sanitize_post',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => $_POST,
			)
		);

		\WP_Mock::userFunction(
			'wp_send_json',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => true,
			)
		);

		\WP_Mock::userFunction(
			'check_ajax_referer',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => true,
			)
		);
		$stub::$options['mode'] = 'off';
		$this->assertTrue( $stub->media_webp_callback() );
		$stub->destroy();
	}

	/**
	 * Add webp meta box to post.php form test
	 * Expected results: False.
	 */
	public function test_media_webp_callback_missing() {
		$stub  = Stubs::plugin_stub( false, array() );
		$_POST = [
			'flag'            => true,
			'details'         => true,
			'id'              => 101,
			'callback_action' => 'theme_switch_fake',
		];

		\WP_Mock::userFunction(
			'wp_unslash',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => true,
			)
		);

		\WP_Mock::userFunction(
			'sanitize_post',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => $_POST,
			)
		);

		\WP_Mock::userFunction(
			'check_ajax_referer',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => true,
			)
		);
		$this->assertFalse( $stub->media_webp_callback() );
		$stub->destroy();
	}

	/**
	 * Check AJAX routing method for failed wp_nonce.
	 * Expected results: False.
	 */
	public function test_media_webp_callback_failed_nonce() {
		$stub = Stubs::plugin_stub( false, array() );
		\WP_Mock::userFunction(
			'check_ajax_referer',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => false,
			)
		);
		$this->assertFalse( $stub->media_webp_callback() );
		$stub->destroy();
	}

	/**
	 * Test WordPress default hook admin_init().
	 * Expected: match
	 */
	public function test_admin_init() {
		global $pagenow;
		Plugin::destroy();
		$stub         = Stubs::plugin_stub( false, array() );
		$pagenow      = 'test_page';
		$pagenow_test = $pagenow;
		$stub::admin_init();
		$this->assertEquals( $stub::$pagenow, $pagenow_test );
		$stub->destroy();
	}

	/**
	 * Test WordPress default hook activate.
	 * Expected: True, True
	 */
	public function test_activate() {
		$stub = Stubs::plugin_stub( false, array() );
		$test = $stub::activate();
		$this->assertTrue( $test );
		$stub::$options = [];
		$test           = $stub::activate();
		$this->assertTrue( $test );
		$stub->destroy();
	}
}
