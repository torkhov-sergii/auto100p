<?php
namespace WPST\Media_Webp\Tests;

use WP_Mock\Tools\TestCase;
use WPST\Media_Webp\Plugin;

class Mocks extends TestCase {

	// Media_Webp plugin test suite mocks.

	private static $mime_types = [
		[ 'image/jpg', 'image/jpeg', 'image/png' ],
		[ 'image/jpg', 'image/jpeg' ],
		[ 'image/png' ],
	];

	public static function plugin_mock($mime_key = 0, $state = 'on'){
		$mock                   = \Mockery::mock( '\WPST\Media_Webp\Plugin' );
		$mock::$images          = self::$mime_types[$mime_key];
		$mock::$options['mode'] = $state;
		$mock::$base_path       = dirname( __DIR__ );
		return $mock;
	}

	public static function model_mock($attachments = null){
		$attachments    = [];
		$attachment     = \Mockery::mock( '\WP_Post' );
		$attachment->ID = '101';
		array_push( $attachments, $attachment );

		$mock = \Mockery::mock( '\WPST\Media_Webp\Model' );
		$mock->shouldReceive( 'get_image_attachments' )
		->andReturn( $mock );
		$mock->attachments     = $attachments;
		return $mock;
	}

	public static function admin_mock(){
		$mock = \Mockery::mock( 'WPST\Media_Webp\Admin' );
		$mock->shouldReceive( 'attachment_webp_exists' )
		->andReturn( true );
		$mock->shouldReceive( 'webp_jpeg_png_totals' )
		->andReturn( [] );
		$mock->shouldReceive( 'clear_image_info' )
		->andReturn( true );
		return $mock;
	}
}

