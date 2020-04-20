<?php
/**
 * Plugin
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage WPST\Media_Webp
 * @author     Steven Turner <steveturner23@hotmail.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 */

namespace WPST\Media_Webp;

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Class Plugin
 *
 * Implements all actions and filters.
 *
 * @category   Class
 * @package    WordPress
 * @see        Singleton
 * @see        Admin
 **/
class Plugin {

	use Singleton;

	/**
	 * $_POST data after sanitized. Not to name convention. never mind.
	 *
	 * @var $_post
	 * **/
	public static $_post;

	/**
	 * Loaded options.
	 *
	 * @var Array
	 * **/
	public static $options = [];

	/**
	 * Accepted image types.
	 *
	 * @var Array
	 * **/
	public static $images = [];

	/**
	 * Upload file path.
	 *
	 * @var String
	 * **/
	public static $base_path;

	/**
	 * Page requested.
	 *
	 * @var $pagenow $globals['pagenow']
	 * **/
	public static $pagenow;

	/**
	 * Plugin settings notices response.
	 *
	 * @var int
	 * **/
	public static $settings_notice;

	/**
	 * Current post ID.
	 *
	 * @var $current_id used to pass post id between filters. That's one of the reasons why its a singleton.
	 * **/
	public static $current_id;

	/**
	 * Multi site flag.
	 *
	 * @var boolean
	 * **/
	private static $_networkactive;

	/**
	 * Fired once by getInstance().
	 *
	 * @see    Singleton
	 * @see    Admin
	 *
	 * Main constructor routine used to provision and hook the plugin.
	 * This class is triggered by mediawebp.php
	 * @return void
	 */
	protected function init() : void {
		global $wp_version;

		// set base_path to current upload directory path and case post.
		self::$base_path       = wp_upload_dir()['basedir'];
		self::$settings_notice = 0;

		// GD library missing.
		if ( ! function_exists( 'imagewebp' ) ) {
			$notice = Notice::get_instance();
			add_action( 'admin_notices', array( $notice, 'admin_notices_gd_lib' ) );
			add_action( 'network_admin_notices', array( $notice, 'admin_notices_gd_lib' ) );
			return;
		}

		// WordPress version.
		if ( version_compare( $wp_version, '4.7-RC1-src', '<' ) ) {
			$notice = Notice::get_instance();
			add_action( 'admin_notices', array( $notice, 'admin_notices_incompatible' ) );
			add_action( 'network_admin_notices', array( $notice, 'admin_notices_incompatible' ) );
			return;
		}

		// running on a multi site instance.
		self::$_networkactive = ( is_multisite() &&
		array_key_exists( plugin_basename( __FILE__ ), get_site_option( 'active_sitewide_plugins' ) ) );

		if ( self::$_networkactive ) {
			self::set_options( get_site_option( 'media_webp_options', array() ) );
		} else {
			self::set_options( get_option( 'media_webp_options', array() ) );
		}

		if ( 'both' === self::$options['images'] ) {
			self::$images = [ 'image/jpg', 'image/jpeg', 'image/png' ];
		} elseif ( 'jpeg' === self::$options['images'] ) {
			self::$images = [ 'image/jpg', 'image/jpeg' ];
		} else {
			self::$images = [ 'image/png' ];
		}
		// active.
		'off' !== self::$options['mode'] ? self::active_plugin() : null;
		is_admin() ? self::admin_filters_actions() : null;
	}

	/**
	 * On condition of admin area.
	 *
	 * Adds admin filters and actions.
	 *
	 * @return void
	 */
	public function admin_filters_actions() : void {
		$links  = Links::get_instance();
		$notice = Notice::get_instance();
		$admin  = Admin::get_instance();
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		// base plugin actions.
		add_action( 'wp_ajax_media_webp_callback', array( $this, 'media_webp_callback' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'updated_scripts' ) );
		add_action( 'after_plugin_row', array( $notice, 'plugin_notice' ) );
		// base plugin filters.
		add_filter( 'wp_save_image_editor_file', array( $admin, 'editor_update' ), 10, 5 );
		add_filter( 'wp_prepare_attachment_for_js', array( $admin, 'attachment_details_webp_exists' ), 10, 1 );
		add_filter( 'plugin_action_links_' . plugin_basename( MEDIA_WEBP_DIR . 'mediawebp.php' ), array( $links, 'settings_links' ) );
		add_filter( 'the_title', array( $this, 'add_webp_list' ), 10, 2 );
		add_filter( 'add_meta_boxes', array( $notice, 'wp_editor_meta_box_action' ), 10, 2 );

		// add multi site specific hooks.
		if ( ! self::$_networkactive ) {
			add_action( 'admin_menu', array( $links, 'settings_menu' ) );
			add_action( 'admin_menu', array( $links, 'tools_menu' ) );
		} else {
			add_action( 'network_admin_menu', array( $links, 'settings_menu' ) );
			add_action( 'network_admin_menu', array( $links, 'tools_menu' ) );
			// granular activation settings on a multi site platform.
		}
		if ( ! is_network_admin() && ! empty( $GLOBALS['_POST'] ) ) {
			$form = Forms::get_instance();
			add_action( 'admin_head', array( $form, 'check_submission' ) );
		}
	}

	/**
	 * Active plugin hooks.
	 *
	 * @see    self::init
	 *
	 * Adds filters and actions if the plugin is active.
	 */
	public function active_plugin() : void {
		$admin = Admin::get_instance();
		if ( 'on' === self::$options['gallery_auto_conversion'] ) {
			// all media upload hooks.
			add_filter( 'wp_handle_upload', array( $admin, 'create_webp' ), 10, 2 );
			add_filter( 'image_make_intermediate_size', array( $admin, 'make_thumbnails' ), 10, 1 );
		}
		// media delete webp when original is deleted.
		'on' === self::$options['gallery_auto_delete'] ? add_filter( 'delete_attachment', array( $admin, 'delete_action' ), 10, 1 ) : null;
		// theme conversion on selection.
		'on' === self::$options['theme_auto_conversion'] ? add_action( 'after_switch_theme', array( $this, 'theme_switch' ) ) : null;
	}

	/**
	 * Fired by WordPress action 'admin_enqueue_scripts'.
	 *
	 * @see    self::init
	 *
	 * Adds css and javascript to :
	 * Admin tools.php, Plugin Tools interface.
	 * Admin upload.php, wp.media object event hooks
	 * and custom attribute.
	 * Admin post.php, displays if the image has a corresponding
	 * webp and adds convert functions if available.
	 */
	public function updated_scripts() : bool {

		$script_pages = [
			'upload.php',
			'post.php',
			'post-new.php',
		];
		$plugin_pages = [
			'media-webp_tools',
			'media-webp_settings',
		];

		global $plugin_page;
		global $protocol;

		if ( in_array( self::$pagenow, $script_pages, true ) ) {
			if ( 'off' !== self::$options['mode'] ) {
				$params = [
					'media_webp_alert_1'  => _x( 'Create webp', 'jQuery Alert', 'media-webp' ),
					'media_webp_alert_2'  => _x( 'Sorry there has been an error creating the webp files.', 'jQuery Alert', 'media-webp' ),
					'media_webp_alert_15' => _x( 'Media webp images have now been created.', 'jQuery Alert', 'media-webp' ),
					'ajaxurl'             => admin_url( 'admin-ajax.php', $protocol ),
					'ajax_nonce'          => wp_create_nonce( 'media-webp-ajax' ),
				];
				wp_enqueue_script( 'media-webp-ajax', MEDIA_WEBP_URL . 'assets/js/admin.js', array( 'jquery' ), '1.0.0', true );
				wp_localize_script( 'media-webp-ajax', 'media_webp_object', $params );
			}
			wp_enqueue_style( 'media-webp', MEDIA_WEBP_URL . 'assets/css/library.css', array(), '1.0.0' );
			'on' === self::$options['show_icon'] ? wp_enqueue_script( 'media-webp-ajax-icons', MEDIA_WEBP_URL . 'assets/js/icons.js', array( 'jquery' ), '1.0.0', true ) : null;
			return true;
		} elseif ( in_array( $plugin_page, $plugin_pages, true ) ) {
			wp_enqueue_script( 'media-webp-settings', MEDIA_WEBP_URL . 'assets/js/settings.js', array( 'jquery' ), '1.0.0', true );
			wp_enqueue_style( 'media-webp', MEDIA_WEBP_URL . 'assets/css/plugin.css', array(), '1.0.0' );
			return true;
		}
		return false;
	}

	/**
	 * Fired by WordPress action 'wp_ajax_media_webp_callback'.
	 *
	 * @see    Routes Ajax request to the correct method.
	 * @return Boolean
	 */
	public function media_webp_callback() : bool {
		$valid_nonce = check_ajax_referer( 'media-webp-ajax', 'security' );
		if ( false === $valid_nonce ) {
			return false;
		}
		$post   = sanitize_post( wp_unslash( $_POST ) );
		$class  = Ajax::get_instance();
		$method = rtrim( $post['callback_action'], '_' );
		if ( method_exists( $class, $method ) ) {
			return Ajax::$method( $post );
		}
		return false;
	}

	/**
	 * Create theme images when theme selected.
	 *
	 * @see Plugin::theme_switch()
	 */
	public function theme_switch() : Plugin {
		Ajax::theme_generate_webps( false );
		return $this;
	}

	/**
	 * Fired by WordPress action 'the_title'.
	 *
	 * Adds tags to admin media lists to signify that webp image(s) exist.
	 * jQuery is used to refresh the page based on the presents of these tags
	 * and displays the icon.
	 *
	 * @self::init
	 * @param  string $title Original title.
	 * @param  int    $id Attachment ID.
	 * @param  Admin  $admin Admin instance.
	 *
	 * @return String
	 */
	public function add_webp_list( string $title, int $id, Admin $admin = null ) : string {
		if ( 'upload.php' === self::$pagenow && self::$current_id !== $id ) {
			is_null( $admin ) ? $admin = Admin::get_instance() : null;
			if ( $admin->attachment_webp_exists( $id ) ) {
				echo "<span class='webp_found'></span>";
			}
			self::$current_id = $id;
		}
		return $title;
	}

	/**
	 * Setter for options.
	 *
	 * @param array $options set options class variable.
	 */
	public function set_options( array $options ) : void {
		null !== $options ? self::$options = $options : null;
	}

	/**
	 * Save class options (all plugin options) to WordPress db.
	 *
	 * @return int
	 */
	public function update_options() : int {

		if ( self::$_networkactive ) {
			update_site_option( 'media_webp_options', self::$options );
		} else {
			update_option( 'media_webp_options', self::$options );
		}
		return 1;
	}

	/**
	 * Plugin activation. Hooked automatically.
	 */
	public static function activate() : bool {

		if ( count( self::$options ) === 0 ) {
			self::$options = [
				'mode'                    => 'on',
				'images'                  => 'both',
				'gallery_auto_conversion' => 'on',
				'gallery_auto_delete'     => 'on',
				'theme_auto_conversion'   => 'on',
				'show_icon'               => 'on',
			];
			self::update_options();
		}

		do_action( 'media_webp_activate' );
		return true;
	}

	/**
	 * Plugin admin initialise.
	 */
	public static function admin_init() : void {
		global $pagenow;
		self::$pagenow = $pagenow;
	}

	/**
	 * Multi site switch.
	 *
	 * @return array
	 */
	public function networkactive() {
		return self::$_networkactive;
	}
}
