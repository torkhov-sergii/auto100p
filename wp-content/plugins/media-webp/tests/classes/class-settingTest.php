<?php
/**
 * SettingsTest
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
use WPST\Media_Webp\Setting;

class SettingTest extends TestCase {
	
	/**
	 * Test form render.
	 */

	public function setup() {
		parent::setUp();
		\WP_Mock::setUp();
	}
	public function test_show() {
		$options = [
			'mode'                    => 'on',
			'gallery_auto_conversion' => 'on',
			'gallery_auto_delete'     => 'on',
			'theme_auto_conversion'   => 'on',
			'images'                  => [ 'image/jpg', 'image/jpeg', 'image/png' ],
			'show_icon'               => 'on',

		];
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
		ob_start();
		$class  = new Setting( $options );
		$output = ob_get_clean();

		$count = substr_count( $output, 'id="media-webp"' );
		$this->assertEquals( $count, 1 );
		$count = substr_count( $output, 'id="_mode_enable"' );
		$this->assertEquals( $count, 1 );
		$count = substr_count( $output, 'class="infopanel"' );
		$this->assertEquals( $count, 1 );
		$count = substr_count( $output, 'name="gallery_auto_conversion"' );
		$this->assertEquals( $count, 1 );
		$count = substr_count( $output, 'name="gallery_auto_delete"' );
		$this->assertEquals( $count, 1 );
		$count = substr_count( $output, 'name="theme_auto_conversion"' );
		$this->assertEquals( $count, 1 );
		$count = substr_count( $output, 'name="images"' );
		$this->assertEquals( $count, 3 );
		$count = substr_count( $output, 'class="button-primary" type="submit"' );
		$this->assertEquals( $count, 1 );
	}
	public function tearDown() {
		\WP_Mock::tearDown();
		parent::tearDown();
	}
}
