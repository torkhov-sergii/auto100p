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
 * Class Tools.
 *
 * Tools Template.
 *
 * @category   Class
 * @package    WordPress
 * @see        Forms
 * @see        Admin
 **/
class Tool {

	/**
	 * Image object info.
	 *
	 * @var Array
	 * */
	private $_image_info;

	/**
	 * Construct method, populates form data.
	 *
	 * @param array $image_info object passed through.
	 */
	public function __construct( $image_info ) {
		$this->_image_info = $image_info;
		$this->show();
	}

	/**
	 * Show method, html template with placeholders no logic.
	 */
	public function show() : bool {
		echo esc_html( $this->_image_info['message'] );
		?>
		<div class="wrap">
		<h2></h2>
		<h1>
		<?php echo esc_html( _x( 'Media Webp Tools', 'tools page', 'media-webp' ) ); ?>
		</h1>
		<div id="free_block"><span class="media_wts"><?php echo esc_html( _x( 'Upload folder free space', 'tools page', 'media-webp' ) ); ?>&#58;</span>
		<span id="storage">
		<?php echo esc_html( $this->_image_info['storage'] ); ?>
		</div>
		</p>
		<hr/>
		<h2><?php echo esc_html( _x( 'Uploads Information', 'tools page', 'media-webp' ) ); ?><span onclick="javascript:jQuery('#info_u').toggle();document.getSelection().removeAllRanges();" class='infowebp'></span></h2>
		<div id="info_u" class="infopanel">
		<p><?php echo esc_html( _x( 'Only images that are currently in your WordPress database will be generated or deleted.', 'tools page', 'media-webp' ) ); ?>
		<?php echo esc_html( _x( 'Any orphaned images in the upload folder will not be affected.', 'tools page', 'media-webp' ) ); ?>
		</p>
		</div>
		<form action="" method="post" id="m_webp_form">
		<input type="hidden" name="mark" id="mark" value="0"/>
		<ul>
		<li><span class="media_wts"><?php echo esc_html( _x( 'Media with a webp compatible image', 'tools page', 'media-webp' ) ); ?>&#58;</span>
		<span id="attachment" class="media_wiv"><?php echo esc_html( $this->_image_info['attachments'] ); ?></span></li>
		<li><span class="media_wts"><?php echo esc_html( _x( 'Compatible images in your upload folder', 'tools page', 'media-webp' ) ); ?></span>
		<span>&#58;</span>
		<span id="attachment_images" class="media_wiv"><?php echo esc_html( $this->_image_info['attachment_images'] ); ?></span>
		<span class="media_wi">( <?php echo esc_html( _x( 'including thumbnails', 'tools page', 'media-webp' ) ); ?> )</span></li>
		<li><span class="media_wts"><?php echo esc_html( _x( 'Total size of compatible images in your upload folder', 'tools page', 'media-webp' ) ); ?>&#58;</span>
		<span id="attachments_size" class="media_wiv"><?php echo esc_html( $this->_image_info['attachment_images_size'] ); ?></span></li>
		</ul>
		<ul>
		<li><span class="media_wts"><?php echo esc_html( _x( 'Media with webp images', 'tools page', 'media-webp' ) ); ?>&#58;</span>
		<span id="attachments_with_webp" class="media_wiv"><?php echo esc_html( $this->_image_info['attachments_with_webps'] ); ?></span></li>
		<li><span class="media_wts"><?php echo esc_html( _x( 'webp images in your upload folder', 'tools page', 'media-webp' ) ); ?></span>
		<span>&#58;</span>
		<span id="attachment_webps" class="media_wiv"><?php echo esc_html( $this->_image_info['attachment_webps'] ); ?></span>
		<span class="media_wi">( <?php echo esc_html( _x( 'including thumbnails', 'tools page', 'media-webp' ) ); ?> )</span></li>
		<li><span class="media_wts"><?php echo esc_html( _x( 'Total size of webp images in your upload folder', 'tools page', 'media-webp' ) ); ?>&#58;</span>
		<span id="attachment_webps_size" class="media_wiv"><?php echo esc_html( $this->_image_info['attachment_webps_size'] ); ?></span></li>
		</ul>
		<p class="submit">
		<?php if ( 'off' !== Plugin::$options['mode'] ) { ?>
		<input id="button_convert" class="button-primary" type="button" name="submit" value="<?php echo esc_html( _x( 'Create webp Images', 'tools page', 'media-webp' ) ); ?>">
		<?php } ?>
		<input id="button_delete" class="button-primary" type="button" name="submit" value="<?php echo esc_html( _x( 'Delete webp Images', 'tools page', 'media-webp' ) ); ?>"><br/>
		<span class="info_block_attachments"><span class="spinner info_spinner" sytle="float:right" id="convert_spinner"></span><span id="info"></span>&nbsp;&nbsp;<span id="total"></span><span id="progress"></span></span>
		</p>
		<hr/>
		<h2><?php echo esc_html( _x( 'Theme Information', 'tools page', 'media-webp' ) ); ?><span onclick="javascript:jQuery('#info_t').toggle();document.getSelection().removeAllRanges();" class="infowebp"></h2>
		<div id="info_t" class="infopanel">
		<h4><?php echo esc_html( _x( 'Creating', 'tools page', 'media-webp' ) ); ?></h4>
		<p><?php echo esc_html( _x( 'This will only create wepb images for your active theme folder.', 'tools page', 'media-webp' ) ); ?>
		<h4><?php echo esc_html( _x( 'Deleting', 'tools page', 'media-webp' ) ); ?></h4>
		<p><?php echo esc_html( _x( 'Deleting all webp images in the current active theme folder, this includes any that have not been generated using this plugin.', 'tools page', 'media-webp' ) ); ?></p>
		</div>
		<ul>
		<li><span class="media_wts"><?php echo esc_html( _x( 'Compatible images in your active themes folder', 'tools page', 'media-webp' ) ); ?>&#58;</span>
		<span class="media_wiv"><?php echo esc_html( $this->_image_info['theme_images'] ); ?></span></li>
		<li><span class="media_wts"><?php echo esc_html( _x( 'Size of compatible images in your active themes folder', 'tools page', 'media-webp' ) ); ?>&#58;</span>
		<span class="media_wiv"><?php echo esc_html( $this->_image_info['theme_images_size'] ); ?></span></li>
		<li><span class="media_wts"><?php echo esc_html( _x( 'webp images in your active themes folder', 'tools page', 'media-webp' ) ); ?>&#58;</span>
		<span id="theme_webp" class="media_wiv"><?php echo esc_html( $this->_image_info['theme_webps'] ); ?></span></li>
		<li><span class="media_wts"><?php echo esc_html( _x( 'Size of webp images in your active themes folder', 'tools page', 'media-webp' ) ); ?>&#58;</span>
		<span id="theme_webps_size" class="media_wiv"><?php echo esc_html( $this->_image_info['theme_webps_size'] ); ?></span></li>
		</ul>
		<p class="submit">
		<?php if ( 'off' !== Plugin::$options['mode'] ) { ?>
		<input id="button_convert_theme" class="button-primary" type="button" name="submit" value="<?php echo esc_html( _x( 'Create webp Theme Images', 'tools page', 'media-webp' ) ); ?>">
		<?php } ?>
		<input id="button_delete_theme" class="button-primary" type="button" name="submit" value="<?php echo esc_html( _x( 'Delete webp Theme Images', 'tools page', 'media-webp' ) ); ?>"><br/>
		<span class="info_block_theme"><span class="spinner info_spinner" id="convert_spinner_t"></span><span class="info_theme"></span></span>
		</p>        
		<?php wp_nonce_field( 'media-webp-admin' ); ?>
		</form>
		</div>
		<?php
		return true;
	}
}
