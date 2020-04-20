<?php
/**
 * LinkTest
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
use WPST\Media_Webp\Links;
use WPST\Media_Webp\Plugin;
use WPST\Media_Webp\Tests\Stubs;

	/**
	 * Basic model tests.
	 */

class LinkTest extends TestCase {


	public function setup() {
		parent::setUp();
		\WP_Mock::setUp();
	}

	public function tearDown() {
		\WP_Mock::tearDown();
		parent::tearDown();
	}

	/**
	 * Successfully construct the setting menu options.
	 * Expected: True
	 */
	public function test_settings_menu() {
		$links = Links::get_instance();
		Plugin::destroy();
		$stub             = Stubs::plugin_stub( false, array() );
		\WP_Mock::userFunction(
			'add_submenu_page',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => '',
			)
		);
		$this->assertTrue( $links->settings_menu() );
		$stub->destroy();
	}

	/**
	 * Successfully construct the setting menu options, multi site.
	 * Expected: True
	 */
	public function test_settings_menu_networkactive() {
		$params           = [
			'get_site_option'            => 2,
			'get_option'                 => 0,
			'plugin_basename'            => 2,
			'register_deactivation_hook' => 1,
			'is_multisite'               => 1,
			'wp_upload_dir'              => 1,
		];
		$GLOBALS['_POST'] = [ 'test' => 1 ];
		$stub             = Stubs::plugin_stub( true, $params );
		$links 			  = Links::get_instance();

		\WP_Mock::userFunction(
			'add_submenu_page',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => '',
			)
		);

		$this->assertTrue( $links->settings_menu() );
		$stub->destroy();
	}


	/**
	 * Successfully construct the tools menu options.
	 * Expected: True
	 */
	public function test_tools_menu() {
		$stub = Stubs::plugin_stub( false, array() );
		$links = Links::get_instance();
		$this->assertTrue( $links->tools_menu() );
		$stub->destroy();
	}

	/**
	 * Successfully construct the tools menu options, multi site.
	 * Expected: True
	 */
	public function test_tools_menu_networkactive() {
		$params = [
			'get_site_option'            => 2,
			'get_option'                 => 0,
			'plugin_basename'            => 2,
			'register_deactivation_hook' => 1,
			'is_multisite'               => 1,
		];

		\WP_Mock::userFunction(
			'add_submenu_page',
			array(
				'id'     => [ 101 ],
				'times'  => 1,
				'return' => '',
			)
		);

		$stub   = Stubs::plugin_stub( true, $params );
		$links = Links::get_instance();
		$this->assertTrue( $links->tools_menu() );
		$stub->destroy();
	}
	/**
	 * Test WordPress default hook plugin action links.
	 * Expected: match
	 */
	public function test_plugin_action_links_plugin_page() {
		\WP_Mock::userFunction(
			'admin_url',
			array(
				'times'  => 1,
				'return' => true,
			)
		);
		$params        = [
			'is_network_admin' => 3,
		];
		$stub          = Stubs::plugin_stub( false, $params );
		$link         = [ 'link' ];
		$links_correct = [
			'<a href="1">Settings</a>',
			'link',
		];
		$links = Links::get_instance();
		$links_new = $links->plugin_action_links( $link );
		$this->assertEquals( $links_new, $links_correct );
		$stub->destroy();
	}

		/**
	 * Test WordPress default hook plugin settings links, plugin page.
	 * Expected: match
	 */
	public function test_settings_links() {
		$stub = Stubs::plugin_stub( false, array() );
		\WP_Mock::userFunction(
			'admin_url',
			array(
				'times'  => 2,
				'return' => true,
			)
		);
		$link         = [ 'link' ];
		$links_correct = [
			'<a href="1">Settings</a>',
			'<a href="1">Tools</a>',
			'link',
		];
		$links = Links::get_instance();
		$links_new     = $links->settings_links( $link );
		$this->assertEquals( $links_new, $links_correct );
		$stub->destroy();
	}
	




	/**
	 * Test WordPress default hook plugin action links, multi site.
	 * Expected: match
	 */
	public function test_plugin_action_links_plugin_page_networkactive() {
		$params = [
			'get_site_option'            => 2,
			'get_option'                 => 0,
			'plugin_basename'            => 2,
			'is_network_admin'           => 2,
			'register_deactivation_hook' => 1,
			'is_multisite'               => 1,
		];
		$stub   = Stubs::plugin_stub( true, $params );
		\WP_Mock::userFunction(
			'network_admin_url',
			array(
				'times'  => 1,
				'return' => true,
			)
		);
		\WP_Mock::userFunction(
			'is_plugin_active_for_network',
			array(
				'times'  => 1,
				'return' => true,
			)
		);

		$link         = [ 'link' ];
		$links_correct = [
			'<a href="1">Settings</a>',
			'link',
		];
		$links = Links::get_instance();
		$links_new     = $links->plugin_action_links( $link );
		$this->assertEquals( $links_new, $links_correct );
		$stub->destroy();
	}

}