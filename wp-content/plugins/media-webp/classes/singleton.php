<?php
/**
 * Singleton
 *
 * Trait for inclusion in singleton plugin classes.
 *
 * @category   Class
 * @package    WordPress
 * @subpackage WPST\Media_Webp
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @see        WPST\Media_Webp\Plugin
 * @see        WPST\Media_Webp\Admin
 * @since      1.0.0
 **/

namespace WPST\Media_Webp;

/**
 * This allows you to have only one instance of the needed object
 * You can get the instance with
 *     $class = My_Class::get_instance();
 *
 * /!\ The get_instance method have to be implemented !
 *
 * Class Singleton
 *
 * @package WPST\Media_Webp
 */
trait Singleton {

	/**
	 * Static instance container.
	 *
	 * @var self
	 */
	protected static $instance;
	/**
	 * Singleton returned, created in first get_instance
	 *
	 * @return self
	 */
	final public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Constructor protected from the outside
	 */
	final private function __construct() {
		$this->init();
	}

	/**
	 * Add init function by default
	 * Implement this method in your child class
	 * If you want to have actions send at construct
	 */
	protected function init() {}

	/**
	 * Prevent the instance from being cloned
	 *
	 * @return void
	 */
	final private function __clone() {}

	/**
	 * Deserialize prevention
	 *
	 * @return void
	 */
	final private function __wakeup() {}

	/**
	 * Destruction method only used by test units.
	 *
	 * @return void
	 */
	public function destroy() {
		self::$instance = null;
	}
}
