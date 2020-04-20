<?php
/**
 * NoticeTest
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
use WPST\Media_Webp\Notice;
use WPST\Media_Webp\Tests\Stubs;
use WPST\Media_Webp\Tests\Mocks;

	/**
	 * Basic model tests.
	 */

class NoticeTest extends TestCase {

	public function setup() {
		parent::setUp();
		\WP_Mock::setUp();
	}

	public function tearDown() {
		\WP_Mock::tearDown();
		parent::tearDown();
	}

	/**
	 * Test WordPress default hook plugin notice.
	 * Expected: match
	 */
	public function test_plugin_notice() {
		$stub                   = Stubs::plugin_stub( false, array() );
        $stub::$options['mode'] = 'off';
        $notice = Notice::get_instance();
        $notice::clear_edit_meta_flag();
		ob_start();
		$notice->plugin_notice( 'mediawebp/mediawebp.php' );
		$output                 = ob_get_clean();
		$stub::$options['mode'] = 'on';
		$count                  = substr_count( $output, 'class="plugin-update colspanchange"' );
		$this->assertEquals( $count, 1 );
		$stub->destroy();
	}

	/**
	 * Test WordPress default hook plugin notice error with settings.
	 * Expected: match
	 */
	public function test_plugin_notice_error_settings() {
        $stub = Stubs::plugin_stub( false, array() );
        $notice = Notice::get_instance();
		ob_start();
		$notice->admin_notice_error_settings();
		$output = ob_get_clean();
		$count  = substr_count( $output, 'Your settings have NOT been updated' );
		$this->assertEquals( $count, 1 );
		$stub->destroy();
	}

	/**
	 * Test WordPress default hook plugin notice error with version.
	 * Expected: match
	 */
	public function test_plugin_notice_error_incompatible() {
        $stub = Stubs::plugin_stub( false, array() );
        $notice = Notice::get_instance();
		ob_start();
        $notice->admin_notices_incompatible();
		$output = ob_get_clean();
		$count  = substr_count( $output, 'Please upgrade to the latest version of WordPress' );
		$this->assertEquals( $count, 1 );
		$stub->destroy();
	}

	/**
	 * Check AJAX meta box action, create_webp.
	 * Expected results: match.
	 */
	public function test_wp_editor_meta_box_create_webp() {
        $stub = Stubs::plugin_stub( false, array() );
        $notice = Notice::get_instance();
		$post     = \Mockery::mock( '\WP_Post' );
		$post->ID = '101';
        $notice = Notice::get_instance();
		ob_start();
		$notice->wp_editor_meta_box( $post );
		$output = ob_get_clean();

		$count = substr_count( $output, 'class="button create-webp button-large"' );
		$this->assertEquals( $count, 1 );
		$stub->destroy();
	}

	/**
	 * Plugin initialised, add webp icon to images
	 * within the media gallery. Grid view.
	 * Expected results: Instance of plugin returned.
	 */
	public function test_wp_editor_meta_box_action_success() {
		$stub = Stubs::plugin_stub( false, array() );
		\WP_Mock::userFunction(
			'add_meta_box',
			array(
				'times'  => 1,
				'return' => true,
			)
		);

		$response = [
			'id'   => 101,
			'webp' => 'true',
			'webp_size' => '100KB',
		];

		$admin = Mocks::admin_mock();
		$admin->shouldReceive( 'attachment_details_webp_exists' )
		->andReturn( $response );
		$content  = '';
		$post     = \Mockery::mock( '\WP_Post' );
        $post->ID = '101';
        $notice = Notice::get_instance();
		$this->assertTrue( $notice->wp_editor_meta_box_action( $content, $post, $admin ) );
        ob_start();
		$notice->wp_editor_meta_box( $post );
		$output = ob_get_clean();
		$count  = substr_count( $output, 'This media has linked' );
		$this->assertEquals( $count, 1 );
		$stub->destroy();
	}

	/**
	 * Plugin initialised, add webp icon to images
	 * within the media gallery. Grid view.
	 * Expected results: Instance of plugin returned.
	 */
	public function test_wp_editor_meta_box_action_empty_response() {
		$stub = Stubs::plugin_stub( false, array() );

		$response = [
			'id'   => 101,
			'webp' => '',
			'webp_size' => '',
		];
		$admin    = Mocks::admin_mock();
		$admin->shouldReceive( 'attachment_details_webp_exists' )
		->andReturn( $response );

		$content  = '';
		$post     = \Mockery::mock( '\WP_Post' );
        $post->ID = '100';
        $notice = Notice::get_instance();
		$this->assertFalse( $notice->wp_editor_meta_box_action( $content, $post, $admin ) );
		$stub->destroy();
	}

	/**
	 * Plugin initialised, add webp icon to images
	 * within the media gallery. Grid view.
	 * Expected results: Instance of plugin returned.
	 */
	public function test_wp_editor_meta_box_action_static_class() {
		$stub = Stubs::plugin_stub( false, array() );

		\WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 10 ],
				'times'  => 1,
				'return' => [
					'file'  => '',
					'sizes' => [],
				],
			)
		);

		\WP_Mock::userFunction(
			'get_post_mime_type',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => '',
			)
		);
		$response = [
			'id'   => 101,
			'webp' => '',
			'webp_size' => '',
		];
		$admin    = Mocks::admin_mock();
		$admin->shouldReceive( 'attachment_details_webp_exists' )
		->andReturn( $response );
		
		$title             = 'test title';
		$stub::$current_id = 0;

		$content  = '';
		$post     = \Mockery::mock( '\WP_Post' );
        $post->ID = '101';
        $notice = Notice::get_instance();
		$this->assertFalse( $notice->wp_editor_meta_box_action( $content, $post ) );
		$stub->destroy();
	}
}