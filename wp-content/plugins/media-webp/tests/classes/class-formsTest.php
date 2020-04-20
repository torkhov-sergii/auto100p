<?php
/**
 * FormTest
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
use WPST\Media_Webp\Forms;
use WPST\Media_Webp\Plugin;
use WPST\Media_Webp\Tests\Stubs;
use WPST\Media_Webp\Tests\Mocks;

/**
 * Test form submission and parsing.
 */
class FormTest extends TestCase {


	public function setup() {

		parent::setUp();
		\WP_Mock::setUp();
	}

	/**
	* Check form submission with update.
	* Expected result: True, output buffer contains matching text. 
	*/
	public function test_check_submission() {


		global $wp_version;
		global $pagenow;
		global $plugin_page;

		$pagenow     = 'options-general.php';
		$plugin_page = 'media-webp_settings';
		$_POST       = [
			'flag'            => true,
			'details'         => true,
			'id'              => 101,
			'unit'            => 1,
			'_wpnonce'        => 'test_test_test',
			'callback_action' => 'webp_ids',
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
			'check_admin_referer',
			array(
				'return' => true,
			)
		);

		\WP_Mock::userFunction(
			'update_option',
			array(
				'return' => true,
			)
		);
		$stub = Stubs::plugin_stub(false,array());
		$class = Forms::get_instance();

		$this->assertTrue( $class->check_submission() );
		ob_start();
		$class->admin_notice_info();
		$output = ob_get_clean();
		$count  = substr_count( $output, 'Settings have been updated' );
		$this->assertEquals( $count, 1 );

	}

	/**
	* Check form submission no update.
	* Expected result: True, output buffer contains matching text. 
	*/
	public function test_check_submission_no_change() {

		global $pagenow;
		global $plugin_page;

		$pagenow     = 'options-general.php';
		$plugin_page = 'media-webp_settings';
		$_POST       = [
			'flag'            => true,
			'details'         => true,
			'id'              => 101,
			'unit'            => 1,
			'_wpnonce'        => 'test_test_test',
			'callback_action' => 'webp_ids',
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
			'check_admin_referer',
			array(
				'return' => true,
			)
		);

		$class = Forms::get_instance();

		$this->assertTrue( $class->check_submission() );
		ob_start();
		$class->admin_notice_info();
		$output = ob_get_clean();
		$count  = substr_count( $output, 'No changes have been made to the plugin settings' );
		$this->assertEquals( $count, 1 );
		$class->destroy();
	}
		
	/**
	* Check form submission multi site.
	* Expected result: return True. 
	*/
	public function test_check_submission_networkactive() {
		global $wp_version;
		global $pagenow;
		global $plugin_page;

		$class = Forms::get_instance();

		$pagenow     = 'options-general.php';
		$plugin_page = 'media-webp_settings';
		$_POST       = [
			'flag'            => true,
			'details'         => true,
			'id'              => 101,
			'unit'            => 1,
			'_wpnonce'        => 'test_test_test',
			'callback_action' => 'webp_ids',
		];

		\WP_Mock::userFunction(
			'sanitize_post',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => $_POST,
			)
		);

		\WP_Mock::userFunction(
			'check_admin_referer',
			array(
				'return' => true,
			)
		);

		\WP_Mock::userFunction(
			'update_site_option',
			array(
				'return' => true,
			)
		);

		$params = [
			'get_site_option'            => 2,
			'get_option'                 => 0,
			'plugin_basename'            => 2,
			'register_deactivation_hook' => 1,
			'wp_unslash'                 => 2,
		];
		Plugin::destroy();
		$stub = Stubs::plugin_stub( true, $params );
		$this->assertTrue( $class->check_submission() );
	}

	/**
	* Check form submission no update.
	* Expected result: False, form submission with wrong url. 
	*/
	public function test_check_submission_fail() {
		global $pagenow;
		global $plugin_page;

		$pagenow     = 'options-general-wrong.php';
		$plugin_page = 'media-webp_settings_wrong';

		$class = Forms::get_instance();

		$this->assertFalse( $class->check_submission() );

	}

	/**
	* Check settings form render from endpoint method.
	* Expected result: True. 
	*/
	public function test_settings_page() {

		\WP_Mock::userFunction(
			'checked',
			array(
				'return' => ' checked',
			)
		);

		\WP_Mock::userFunction(
			'wp_nonce_field',
			array(
				'return' => '123fake123',
			)
		);
		Plugin::destroy();
		$stub  = Stubs::plugin_stub( false, array() );
		$form = Forms::get_instance();
		ob_start();
		$this->assertTrue( $form->settings_page() );
		$output = ob_get_clean();
	}

	/**
	* Check form admin notice, failed update.
	* This just tests the notice render directly, no conditions.
	* Expected result: output buffer contains required text. 
	*/
	public function test_plugin_notice_info_failed_update() {
		Plugin::destroy();
		$stub  = Stubs::plugin_stub( false, array() );
		$class = Forms::get_instance();
		ob_start();
		$class->admin_notice_info();
		$output = ob_get_clean();
		$count  = substr_count( $output, 'Failed to update settings' );
		$this->assertEquals( $count, 1 );

	}

	/**
	* Check tools form render from endpoint method.
	* Expected result: True. 
	*/
	public function test_tools_page() {
		global $wp_version;
		global $protocol;

		\WP_Mock::userFunction(
			'add_query_arg',
			array(
				'id'     => [ 101 ],
				'times'  => 2,
				'return' => 'http://test-admin-url/',
			)
		);
		\WP_Mock::userFunction(
			'admin_url',
			array(
				'times'  => 4,
				'return' => dirname( __DIR__ ) . '//tmp//',
			)
		);

		\WP_Mock::userFunction(
			'get_template_directory',
			array(
				'times'  => 2,
				'return' => dirname( __DIR__ ) . '//tmp//',
			)
		);

		\WP_Mock::userFunction(
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => [
					'file'  => 'test.jpg',
					'sizes' => [ [ 'file' => 'test-100x200.jpg' ], [ 'file' => 'test-300x500.jpg' ] ],
				],
			)
		);
		\WP_Mock::userFunction(
			'size_format',
			array(
				'times'  => 10,
				'return' => '100',
			)
		);

		\WP_Mock::userFunction(
			'wp_create_nonce',
			array(
				'times'  => 2,
				'return' => true,
			)
		);

		\WP_Mock::userFunction(
			'wp_localize_script',
			array(
				'times'  => 2,
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
		$plugin                   = Plugin::get_instance();
		$form                     = Forms::get_instance();
		$model                    = Mocks::model_mock();
		$admin                    = Mocks::admin_mock();
		$plugin::$base_path       = dirname( __DIR__ );
		$plugin::$options['mode'] = 'off';

		ob_start();
		$form->tools_page( '', $model, $admin );
		$output = ob_get_clean();
		$count  = substr_count( $output, 'Plugin disabled, you are unable to create .webp images. Please enable the plugin in' );
		$this->assertEquals( $count, 1 );

		$plugin::$options['mode'] = 'on';
		ob_start();
		$form->tools_page( '', $model );
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
		$this->assertEquals( $count, 12 );
	}

	/**
	* Check actions links render with real model.
	* Expected result: output buffer contains expected text. 
	*/
	public function test_plugin_action_links_plugin_page_real_model() {
		\WP_Mock::userFunction(
			'add_query_arg',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => 'http://test-admin-url/',
			)
		);
		\WP_Mock::userFunction(
			'admin_url',
			array(
				'times'  => 2,
				'return' => dirname( __DIR__ ) . '//tmp//',
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
			'wp_get_attachment_metadata',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => [
					'file'  => 'tmp/test.jpg',
					'sizes' => [ [ 'file' => '/tmp/test-100x200.jpg' ], [ 'file' => '/tmp/test-300x500.jpg' ] ],
				],
			)
		);
		$args = [
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'post_mime_type' => [ 'image/jpg', 'image/jpeg', 'image/png' ],
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

		$plugin             = Plugin::get_instance();
		$form               = Forms::get_instance();
		$plugin::$base_path = dirname( __DIR__ );
		ob_start();
		$form->tools_page();
		$output = ob_get_clean();
		$count  = substr_count( $output, 'class="media_wts"' );
		$this->assertEquals( $count, 12 );

	}
	public function tearDown() {
		\WP_Mock::tearDown();
		parent::tearDown();
	}
}
