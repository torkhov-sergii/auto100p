<?php
namespace WPST\Media_Webp\Tests;

use WP_Mock\Tools\TestCase;
use WPST\Media_Webp\Plugin;

class Stubs extends TestCase {

	// Media_Webp Plugin Test suite Stubs

	public function plugin_stub( bool $_networkactive, array $_functions_call_pattern, $wp_version_ = '4.1-RC1-src' ) {

		global $wp_version;

        $wp_version = $wp_version_;

        $images = 'both';
        if ( array_key_exists( 'images', $_functions_call_pattern ) ) {
			$images = $_functions_call_pattern['images'];
		}

        $n = 1;
		if ( array_key_exists( 'wp_upload_dir', $_functions_call_pattern ) ) {
			$n = $_functions_call_pattern['wp_upload_dir'];
		}
		\WP_Mock::userFunction(
			'wp_upload_dir',
			array(
				'times'  => $n,
				'return' => [ 'basedir' => __DIR__ ],
			)
		);
		$n = 1;
		if ( array_key_exists( 'is_admin', $_functions_call_pattern ) ) {
			$n = $_functions_call_pattern['is_admin'];
		}
		\WP_Mock::userFunction(
			'is_admin',
			array(
				'times'  => $n,
				'return' => true,
			)
		);

        $n = 1;
		if ( array_key_exists( 'is_multisite', $_functions_call_pattern ) ) {
			$n = $_functions_call_pattern['is_multisite'];
		}

		\WP_Mock::userFunction(
			'is_multisite',
			array(
				'times'  => $n,
				'return' => $_networkactive,
			)
		);
		$n = 1;
		if ( array_key_exists( 'get_option', $_functions_call_pattern ) ) {
			$n = $_functions_call_pattern['get_option'];
		}

		\WP_Mock::userFunction(
			'get_option',
			array(
				'times'  => $n,
				'return' => [
					'mode'                    => 'on',
					'images'                  => $images,
					'gallery_auto_conversion' => 'on',
					'gallery_auto_delete'     => 'on',
					'theme_auto_conversion'   => 'on',
					'show_icon'               => 'on',
				],
			)
        );
        $n = 0;
		if ( array_key_exists( 'get_site_option', $_functions_call_pattern ) ) {
			$n = $_functions_call_pattern['get_site_option'];
		}

		\WP_Mock::userFunction(
			'get_site_option',
            array(
				'times'  => $n,
				'return' => [
                    'Test_Plugin'             => 1,
					'mode'                    => 'on',
					'images'                  => 'both',
					'gallery_auto_conversion' => 'on',
					'gallery_auto_delete'     => 'on',
					'theme_auto_conversion'   => 'on',
					'show_icon'               => 'on',
				],
			)
		);
		$n = 1;
		if ( array_key_exists( 'plugin_basename', $_functions_call_pattern ) ) {
			$n = $_functions_call_pattern['plugin_basename'];
		}

		\WP_Mock::userFunction(
			'plugin_basename',
			array(
				'times'  => $n,
				'return' => 'Test_Plugin',
			)
		);
		$n = 1;
		if ( array_key_exists( 'is_network_admin', $_functions_call_pattern ) ) {
			$n = $_functions_call_pattern['is_network_admin'];
		}

		\WP_Mock::userFunction(
			'is_network_admin',
			array(
				'times'  => $n,
				'return' => $_networkactive,
			)
        );
        
		$mock = Plugin::get_instance();

		return $mock;

    }

    public function admin_delete( $exclude = [] ) {

		\WP_Mock::userFunction(
			'wp_delete_file',
			array(
				'id'     => [ 2 ],
				'return' => 'true',
			)
		);

		\WP_Mock::userFunction(
			'get_post_mime_type',
			array(
				'id'     => [ 2 ],
				'times'  => 1,
				'return' => 'image/jpg',
			)
		);
		if ( ! in_array( 'wp_get_attachment_metadata', $exclude ) ) {
			\WP_Mock::userFunction(
				'wp_get_attachment_metadata',
				array(
					'id'     => [ 2 ],
					'times'  => 1,
					'return' => [
						'file'  => 'tests/tmp/test.jpg',
						'sizes' => [ [ 'file' => 'tests/tmp/test-100x200.jpg' ], [ 'file' => 'tests/tmp/test-300x500.jpg' ] ],
					],
				)
			);
		}
		
		if ( ! in_array( 'wp_get_attachment_metadata', $exclude ) ) {
			\WP_Mock::userFunction(
				'get_post_meta',
				array(
					'args'   => array( 2, '_wp_attachment_backup_sizes', true ),
					'id'     => [ 2 ],
					'times'  => 1,
					'return' => [
						[ 'file' => 'tests/tmp/test-100x200.jpg' ],
						[ 'file' => 'tests/tmp/test-300x500.jpg' ],
					],
				)
			);
		}
	}

}