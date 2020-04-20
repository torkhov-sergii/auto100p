<?php

function re_order_menu () {
    global $menu;

//    $menu[19] = $menu[10]; unset($menu[10]); //media

//    print_r($menu);
//    exit;

//    // ------- Put away items
//    $dashboard = $menu[2];
//    $separator1 = $menu[4];
//    $posts = $menu[5];
//    $media = $menu[10];
//    $links = $menu[15];
//    $pages = $menu[20];
//    $comments = $menu[25];
//    $separator2 = $menu[59];
//    $appearance = $menu[60];
//    $plugins = $menu[65];
//    $users = $menu[70];
//    $tools = $menu[75];
//    $settings = $menu[80];
//    $separator3 = $menu[99];

}
add_action('admin_menu', 're_order_menu');
