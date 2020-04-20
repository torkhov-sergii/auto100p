<?php

// Media_Webp Plugin Test suite bootstrap

$wp_version;
$plugin_page;
$protocol = 'https';
define( 'MEDIA_WEBP_URL', '//tmp//' );
define( 'MEDIA_WEBP_BASENAME', '//tmp//' );
define( 'MEDIA_WEBP_DIR', '//tmp//' );
WP_Mock::setUsePatchwork( false );
WP_Mock::bootstrap();