=== Media Webp ===
Contributors: steveturner2018
Tags: images, media, webp, optimise, optimization, seo, latest
Requires at least: 4.7
Tested up to: 4.9
Requires PHP: 7.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically creates webp images when you upload compatible media.  This plugin also manages any updates and changes to the linked attachment images.

== Description ==
Media Webp has been designed to help you fully automate the management of webp images on your WordPress website. 
When a compatible image is uploaded to your sites media library corresponding webp images will be generated.  Giving you the advantages of webp images without having to maintain matching images yourself.
This plugin also updates any existing webp images if the associated attachment image is altered through the admin interface.
Included is the option to allow this plugin to generate webp images for your theme when it is made active or switched too.  
The plugin has built in tools to generate webp files from existing images both within your upload media and the active theme folders.
Too enable your server to send webp images you will need to include a small block of code at the top of your .htaccess file.  This will allow your server to check if the requesting browser accepts webp images and if there is a corresponding webp image. Full instructions are available on the settings page.

What this plugin does NOT do.

Alter any existing images that do not have the webp extension.
Delete any images that do not have the webp extension.
Alter any .htaccess file(s) for you.

== Installation ==
1. Upload the contents of the zip file to the "/wp-content/plugins/mediawebp" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.  You will require the GD php module to have been enabled or you will recieve a warning.
3. Add the included code to your .htaccess file.  This can be found on the settings page, by clicking on the blue circle with a white i in the centre.
4. Then generate webp images for any exiting media including any compatible active theme images from within the plugins tool page.  This can be done before or after updating your .htaccess file.

== Frequently Asked Questions ==
= How do I know if it is working =
You can check if its working by using Google Chrome to view one of your sites pages that contain webp images.  You can open the \'web developers\' options to check the received media type next to the image.  It will still have the original file extension on the filename so you will need to clear any caching on your browser.

= Will this affect WordPress caching =
No, all of the work is done by your server so any caching / optimization plugin will not be affected.

= Will it work with CDN's and or static content providers=
You will have to configure your CDN to serve the webp image type, if the webp images are saved directly to your CDN folder will depend on how it is being maintained in WordPress.  You may be able to just include the .htaccess snippet to make it work.  I will update this question as soon as I know more.

== Screenshots ==

1. Once the plugin is activated you can see if webp images are being sent using Google Chrome.
2. Any attachments that have corresponding webp images will have this logo in the top left corner in both grid and list views.  You can turn this off.
3. If a image does not have webp images but it is possible to, you will see this button when viewing the image.
4. On the tools page you can generate images from existing media for both your upload and theme folders.

== Changelog ==
= 1.0.0 =
* Media Webp, first release.

= 1.0.1 =
* bugfix: minor fixes for strict mode.

= 1.0.2 =
* improvement: GD library missing notice added.

= 1.0.3 =
* bugfix (js): new post, add media, media library, icon / button duplicates fix.

== Upgrade Notice ==
= 1.0 =
First release