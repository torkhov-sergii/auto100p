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
 * Settings
 *
 * Settings template.
 *
 * @category   Class
 * @package    WordPress
 * @see        Forms
 * @see        Admin
 **/
class Setting {

	/**
	 * Form object info.
	 *
	 * @var Array
	 * */
	private $_options = [];

	/**
	 * Construct, populate form variables, render.
	 *
	 * @param array $options form and program options.
	 */
	public function __construct( $options ) {
		$this->_options = $options;
		$this->show();
	}

	/**
	 * Show method, html template with placeholders. No logic.
	 */
	public function show() : bool {
		?>
		<div class="wrap">
		<h2></h2>
		<form action="" method="post" id="media-webp">
				<h1><?php echo esc_html( _x( 'Media Webp Settings', 'settings page', 'media-webp' ) ); ?></h1>
				<ul>
					<li><label for="_mode_enable"><input type="radio" id="_mode_enable" name="mode" value="on" <?php checked( $this->_options['mode'], 'on' ); ?> /> <strong>
						<?php echo esc_html( _x( 'Enabled', 'settings page', 'media-webp' ) ); ?></strong>: 
						<?php echo esc_html( _x( 'webp images generated.', 'settings page', 'media-webp' ) ); ?></label>
						<p class="indent"><span class="webp_notice_info"><?php echo esc_html( _x( 'Notice', 'settings page', 'media-webp' ) ); ?>:</span>
						<span><?php echo esc_html( _x( 'You may also need to update your server settings to allow the new files to be available.', 'settings page', 'media-webp' ) ); ?><span onclick="javascript:jQuery('#info').toggle();document.getSelection().removeAllRanges();" class="infowebp"></span>
						</p>
					</li>
					<li><label for="_mode_disabled"><input type="radio" id="_mode_disabled" name="mode" value="off" <?php checked( $this->_options['mode'], 'off' ); ?> /> <strong>
						<?php echo esc_html( _x( 'Disabled', 'settings page', 'media-webp' ) ); ?></strong>: 
						<?php echo esc_html( _x( 'Plugin features are disabled.', 'settings page', 'media-webp' ) ); ?>&nbsp;<?php echo esc_html( _x( 'No webp images generated.', 'settings page', 'media-webp' ) ); ?></label>
					</li>
				</ul>  
				<div id="info" class="infopanel" style="display:none">
					<h4><?php echo esc_html( _x( 'How it works', 'settings page', 'media-webp' ) ); ?></h4>
					<p>
					<?php echo esc_html( _x( 'The webp format is currently the best option when sending images over the internet, first launched by Google in 2010', 'settings page', 'media-webp' ) ); ?>
					<?php echo esc_html( _x( 'Nearly 60% of all internet users chosen browser now supports the image format.', 'settings page', 'media-webp' ) ); ?>
					<?php echo esc_html( _x( 'Its main advantage is it can halve your servers band width and greatly improves their experience.', 'settings page', 'media-webp' ) ); ?>
					<?php echo esc_html( _x( 'Without compromising on image quality.', 'settings page', 'media-webp' ) ); ?>
					<?php echo esc_html( _x( 'This plugin uses the Google recommendations on how to implement and support the webp format.', 'settings page', 'media-webp' ) ); ?>
					</p>
					<p>
					<i><?php echo esc_html( _x( 'The way it works could not be simpler.', 'settings page', 'media-webp' ) ); ?></i><br/>
					<?php echo esc_html( _x( 'Your visitors browser requests the jpg or png version of the image, included is information telling your server if they can accept the webp format.', 'settings page', 'media-webp' ) ); ?>
					<?php echo esc_html( _x( 'If the browser can the server sends the webp binary information.  The image link will stay the same, so this does not affect page caching.', 'settings page', 'media-webp' ) ); ?>
					</p>
					<p>
					<?php echo esc_html( _x( 'The sections below show how to set up two of the main types of web server.', 'settings page', 'media-webp' ) ); ?>
					<?php echo esc_html( _x( 'If your not sure on you web server, just ask your provider.  But if your site has a .htaccess file there is a good chance its an Apache server.', 'settings page', 'media-webp' ) ); ?>
					<?php echo esc_html( _x( 'For more information on how to check that the webp images are being sent successfully please search Google. Its really easy to check using Google Chrome', 'settings page', 'media-webp' ) ); ?>
					<?php echo esc_html( _x( 'But please remember to clear your browsers cache data as the image names will stay the same.', 'settings page', 'media-webp' ) ); ?>
					</p>
					<h4>Apache <?php echo esc_html( _x( 'configuration', 'settings page', 'media-webp' ) ); ?></h4>
					<p><?php echo esc_html( _x( 'Too enable webp images on a Apache server please add the following section to your sites .htaccess file in the root folder', 'settings page', 'media-webp' ) ); ?><br/>
					<?php echo esc_html( _x( 'You will need to add it before the WordPress section.', 'settings page', 'media-webp' ) ); ?></p>
					<pre>
&lt;ifModule mod_rewrite.c&gt;
	RewriteEngine On 
	RewriteCond %{HTTP_ACCEPT} image/webp
	RewriteCond %{REQUEST_URI}  (?i)(.*)(\.jpe?g|\.png)$ 
	RewriteCond %{DOCUMENT_ROOT}/$1.$2.webp -f
	RewriteRule ^(wp-content/.+)\.(jpe?g|png)$ $1.$2.webp [T=image/webp,E=accept:1]
&lt;/IfModule&gt;

&lt;IfModule mod_headers.c&gt;
	Header append Vary Accept env=REDIRECT_accept
&lt;/IfModule&gt;

AddType image/webp .webp
					</pre>
					<br/>
					<h4>Nginx <?php echo esc_html( _x( 'configuration', 'settings page', 'media-webp' ) ); ?></h4>
					<p><?php echo esc_html( _x( 'Enabling the webp extension in Nginx requires two files to be modified.', 'settings page', 'media-webp' ) ); ?> </p>
					<p><?php echo esc_html( _x( 'You can add the required map command to /etc/nginx/nginx.conf.  I have it just before the Virtual Host Configs section, within the http brackets.', 'settings page', 'media-webp' ) ); ?> </p>
					<p><?php echo esc_html( _x( 'Or create a new file /etc/nginx/conf.d/webp.conf and enter the section shown below.', 'settings page', 'media-webp' ) ); ?> 
					<pre>
map $http_accept $webp_extension {
	default "";
	"~*webp" ".webp";
}
					</pre>
					<p><?php echo esc_html( _x( 'Open your sites .conf file.  It is usual found here', 'settings page', 'media-webp' ) ); ?>  : /etc/nginx/sites-available/"YOUR SITE FILENAME".conf <br/>
					<?php echo esc_html( _x( 'And add the following section', 'settings page', 'media-webp' ) ); ?></p>
					<pre>
location ~* ^(/wp-content/.+)\.(png:jpe?g)$ {
	expires max;
	log_not_found off;
	add_header Vary Accept;
	try_files $uri$webp_ext $uri =404;
}
					</pre>
					<p><?php echo esc_html( _x( 'Afterwards open mime_types.conf, and make sure there is an entry for the webp format.', 'settings page', 'media-webp' ) ); ?></p>
					<p><?php echo esc_html( _x( "You will need to reload the server after adding the above information. Usual 'systemctl reload nginx'", 'settings page', 'media-webp' ) ); ?></p>
					<p><?php echo esc_html( _x( 'There are a lot of guides on the internet explaining how to set up and test Nginx servers.', 'settings page', 'media-webp' ) ); ?></p>
					</div>
				<hr/>
				<h2><?php echo esc_html( _x( 'Options', 'settings page', 'media-webp' ) ); ?></h2>
				<p>
					<input type="checkbox" id="gallery_auto_conversion" name="gallery_auto_conversion" value="on" <?php checked( $this->_options['gallery_auto_conversion'], 'on' ); ?>>
					<label for="gallery_auto_conversion"><span><?php echo esc_html( _x( 'Automatically generate webp images when compatible media is uploaded.', 'settings page', 'media-webp' ) ); ?></span></label>
				</p>
				<p> 
					<input type="checkbox" id="gallery_auto_delete" name="gallery_auto_delete" value="on" <?php checked( $this->_options['gallery_auto_delete'], 'on' ); ?>>
					<label for="gallery_auto_delete"><span><?php echo esc_html( _x( 'Delete webp images when the corresponding original media is deleted.', 'settings page', 'media-webp' ) ); ?></span></label>
				</p>
				<p> 
					<input type="checkbox" id="theme_auto_conversion" name="theme_auto_conversion" value="on" <?php checked( $this->_options['theme_auto_conversion'], 'on' ); ?>>
					<label for="theme_auto_conversion"><span><?php echo esc_html( _x( 'Generate webp images when a theme is activated.', 'settings page', 'media-webp' ) ); ?></span></label>
				</p>
				<p><?php echo esc_html( _x( 'Generate webp images for the following media and theme images', 'settings page', 'media-webp' ) ); ?></p>
				<ul>
					<li><label for="images_all">
						<input type="radio" id="images_all" name="images" value="both" <?php checked( $this->_options['images'], 'both' ); ?> /> <strong> 
						<?php echo esc_html( _x( 'Generate .webp from all types of compatible image.', 'settings page', 'media-webp' ) ); ?></strong>
						<?php echo esc_html( _x( '.png, .jpg, .jpeg', 'settings page', 'media-webp' ) ); ?>
						</label>
					</li>
					<li><label for="images_jpg">
						<input type="radio" id="images_jpg" name="images" value="jpeg" <?php checked( $this->_options['images'], 'jpeg' ); ?> /> <strong> 
						<?php echo esc_html( _x( 'Joint Photographic Experts Group', 'settings page', 'media-webp' ) ); ?></strong> 
						<?php echo esc_html( _x( '.jpg, .jpeg', 'settings page', 'media-webp' ) ); ?>
					</label>
					</li>
					<li><label for="images_png">
						<input type="radio" id="images_png" name="images" value="png" <?php checked( $this->_options['images'], 'png' ); ?> /> <strong> 
						<?php echo esc_html( _x( 'Portable Network Graphics', 'settings page', 'media-webp' ) ); ?></strong> <?php echo esc_html( _x( '.png', 'settings page', 'media-webp' ) ); ?>
						</label>
					</li>
				</ul> 
				<p> 
					<input type="checkbox" id="show_icon" name="show_icon" value="on" <?php checked( $this->_options['show_icon'], 'on' ); ?>>
					<label for="show_icon"><span><?php echo esc_html( _x( 'Show webp icon on media library images.', 'settings page', 'media-webp' ) ); ?></span></label>
				</p>
				<?php wp_nonce_field( 'media-webp-admin' ); ?>
				<p class="submit"><input class="button-primary" type="submit" name="submit" value="<?php echo esc_html( _x( 'Save Changes', 'settings page', 'media-webp' ) ); ?>"></p>
		</form>
		</div>
		<?php
		return true;
	}
}
