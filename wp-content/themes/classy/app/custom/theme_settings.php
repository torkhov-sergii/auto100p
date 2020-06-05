<?php

/*-- Init Theme Settings --*/
function al_theme_setup()
{
    if (function_exists('acf_add_options_page')) {

        acf_add_options_page(array(
            'page_title' => __('Theme Settings', preg_replace('/^www\./','',$_SERVER['SERVER_NAME'])),
            //'menu_title' => __('Theme Settings', preg_replace('/^www\./','',$_SERVER['SERVER_NAME'])),
            'menu_slug' => 'theme-general-settings',
            'capability' => 'edit_posts',
            'redirect' => false
        ));
        acf_add_options_page(array(
            'page_title' => __('Services', preg_replace('/^www\./','',$_SERVER['SERVER_NAME'])),
            'menu_title' => __('Services', preg_replace('/^www\./','',$_SERVER['SERVER_NAME'])),
            'parent_slug' => 'theme-general-settings',
        ));
//        acf_add_options_sub_page(array(
//            'page_title' => __('Header', preg_replace('/^www\./','',$_SERVER['SERVER_NAME'])),
//            'menu_title' => __('Header', preg_replace('/^www\./','',$_SERVER['SERVER_NAME'])),
//            'parent_slug' => 'theme-general-settings',
//        ));

    }
}
add_action('after_setup_theme', 'al_theme_setup');


//отключить типы
function my_unregister_post_type(){
    unregister_taxonomy_for_object_type('post_tag', 'post');
    unregister_taxonomy_for_object_type('category', 'post');
}
add_action('init', 'my_unregister_post_type');


//отключить обновление
//add_filter( 'pre_site_transient_update_core', 'remove_core_updates' );
wp_clear_scheduled_hook('wp_version_check');


//отключить обновление плагинов
remove_action( 'load-update-core.php', 'wp_update_plugins' );
//add_filter( 'pre_site_transient_update_plugins', function ( $a ) { return null; } );
wp_clear_scheduled_hook( 'wp_update_plugins' );
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'DISALLOW_FILE_MODS', true );


//убрать иконку обновления из хедера
function wph_new_toolbar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('updates');
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-content');
    $wp_admin_bar->remove_menu('wp-logo');
}
add_action('wp_before_admin_bar_render', 'wph_new_toolbar');


//убрать текст из футера
function wph_admin_footer_text () {
    echo '';
}
add_filter('admin_footer_text', 'wph_admin_footer_text');


//убрать из меню не нужные пункты
function remove_menus(){
    //remove_menu_page( 'index.php' );                  //Консоль
    remove_menu_page( 'edit.php' );                   //Записи
    //remove_menu_page( 'upload.php' );                 //Медиафайлы
    //remove_menu_page( 'edit.php?post_type=page' );    //Страницы
    remove_menu_page( 'edit.php?post_type=discussion' ); //Discussion
    remove_menu_page( 'edit-comments.php' );          //Комментарии

    if (!current_user_can('super-administrator')):
        remove_menu_page( 'themes.php' );                 //Внешний вид
        remove_menu_page( 'plugins.php' );                //Плагины
        remove_menu_page( 'options-general.php' );        //Настройки
        remove_menu_page( 'users.php' );                  //Пользователи
        remove_menu_page( 'tools.php' );                  //Инструменты
        remove_menu_page( 'edit.php?post_type=acf-field-group' );                  //Группы полей
        //remove_menu_page( 'edit.php?post_type=popup' );                  //popup
        remove_menu_page( 'wpcf7' );                  //wpcf7
        remove_menu_page( 'ultimatemember' );                  //ultimatemember
    endif;

    //убрать из консоли (главная страница) блоки
    remove_meta_box('dashboard_activity', 'dashboard', 'core');
    remove_meta_box('dashboard_primary', 'dashboard', 'core');
    remove_meta_box('wp-admin-bar-customize', 'dashboard', 'core');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'core');

    //Tags reorganise - to one chapter
    /*
    remove_submenu_page( 'edit.php?post_type=tag', 'post-new.php?post_type=tag' );
    add_submenu_page( 'edit.php?post_type=tag', 'Markets', 'Markets', 'manage_options', 'edit.php?post_type=market');
    add_submenu_page( 'edit.php?post_type=tag', 'Topics', 'Topics', 'manage_options', 'edit.php?post_type=topic');
    add_submenu_page( 'edit.php?post_type=tag', 'Regions', 'Regions', 'manage_options', 'edit.php?post_type=region');
    add_submenu_page( 'edit.php?post_type=tag', 'Resources', 'Resources', 'manage_options', 'edit.php?post_type=resource');
    remove_menu_page( 'edit.php?post_type=market' );
    remove_menu_page( 'edit.php?post_type=topic' );
    remove_menu_page( 'edit.php?post_type=region' );
    remove_menu_page( 'edit.php?post_type=resource' );
    */
}
add_action( 'admin_menu', 'remove_menus' );


//custom styles
function my_custom_styles() {
echo '<style>
</style>';
}
add_action('admin_head', 'my_custom_styles');


// Custom CSS for admin panel
function load_admin_style() {
    //wp_enqueue_style( 'admin_css', get_template_directory_uri() . '/admin.css', false, '1.0.0' );
    wp_enqueue_style( 'admin_css', '/wp-content/themes/classy/dist/admin.css', false, '1.0.0' );
}
add_action( 'admin_enqueue_scripts', 'load_admin_style' );


//убрать customize
function remove_some_nodes_from_admin_top_bar_menu( $wp_admin_bar ) {
    $wp_admin_bar->remove_menu( 'customize' );
}
add_action( 'admin_bar_menu', 'remove_some_nodes_from_admin_top_bar_menu', 999 );


//убираем emoji
function disable_wp_emojicons() {

    // all actions related to emojis
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

    // filter to remove TinyMCE emojis
    //add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
}
add_action( 'init', 'disable_wp_emojicons' );


// switch the theme to "classy"
switch_theme( 'classy' );


// enable plugins by default
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
activate_plugin( 'advanced-custom-fields-pro-master/acf.php' );
activate_plugin( 'cyr3lat/cyr-to-lat.php' );
activate_plugin( 'duplicate-page/duplicatepage.php' );
activate_plugin( 'intuitive-custom-post-order/duplicatepage.php' );
