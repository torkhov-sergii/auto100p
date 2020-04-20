<?php

/*News tags ajax function*/
//add_action( 'wp_ajax_blog_filter', 'ajax_blog_filter_callback' );
//add_action( 'wp_ajax_nopriv_blog_filter', 'ajax_blog_filter_callback' );

function ajax_blog_filter_callback() {

    $terms = $_POST['tags'];

    if($terms !== 'all'){
        $args = array(
            'post_type' => 'news',
            'posts_per_page' => 8,
            'orderby' => ['post_date' => 'ASC'],
            'tax_query' => array(
                'relation' => "OR",
                array(
                    'taxonomy' => 'tag',
                    'field'    => 'term_id',
                    'terms'    => $terms,
                    'operator' => 'IN'
                ),
            ),
        );
    }
    else{
        $args = array(
            'post_type' => 'news',
            'posts_per_page' => 8,
            'orderby' => ['post_date' => 'ASC']
        );
    }

    $get_posts = new WP_Query($args);
    $posts = $get_posts->query($args);

    $posts_arr = array();

    foreach ($posts as $post) {
        $posts_arr[] = array (
            'link'  => get_permalink($post->ID),
            'title' => $post->post_title,
            'image' => get_the_post_thumbnail_url($post->ID),
            'time'  => date('d.m.Y', strtotime($post->post_date))
        );
    }

    $big = 9999999999;

    $args = array(
        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => '?paged=%#%',
        'type' => 'list',
        'current' => max( 1, get_query_var('paged') ),
        'total' => $get_posts->max_num_pages
    );

    $pagination = upg_paginate_links($args);

    $response = array(
        'posts' => $posts_arr,
        'pagination' => $pagination
    );

    echo json_encode($response);
    wp_die();
}

//add_action( 'wp_ajax_blog_pagination', 'ajax_blog_pagination_callback' );
//add_action( 'wp_ajax_nopriv_blog_pagination', 'ajax_blog_pagination_callback' );

function ajax_blog_pagination_callback() {
    $terms = $_POST['tags'];

    if($terms !== 'all'){
        $args = array(
            'post_type' => 'news',
            'posts_per_page' => 8,
            'paged' => $_POST['page'],
            'orderby' => ['post_date' => 'ASC'],
            'tax_query' => array(
                'relation' => "OR",
                array(
                    'taxonomy' => 'tag',
                    'field'    => 'term_id',
                    'terms'    => $terms,
                    'operator' => 'IN'
                ),
            ),
        );
    }
    else{
        $args = array(
            'post_type' => 'news',
            'paged' => $_POST['page'],
            'posts_per_page' => 8,
            'orderby' => ['post_date' => 'ASC']
        );
    }

    $get_posts = new WP_Query($args);
    $posts = $get_posts->query($args);

    $posts_arr = array();

    foreach ($posts as $post) {
        $posts_arr[] = array (
            'link'  => get_permalink($post->ID),
            'title' => $post->post_title,
            'image' => get_the_post_thumbnail_url($post->ID),
            'time'  => date('d.m.Y', strtotime($post->post_date))
        );
    }

    $big = 9999999999;

    $args = array(
        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => '?paged=%#%',
        'type' => 'list',
        'current' => max( 1, $_POST['page'] ),
        'total' => $get_posts->max_num_pages
    );

    $pagination = upg_paginate_links($args);

    $response = array(
        'posts' => $posts_arr,
        'pagination' => $pagination
    );

    echo json_encode($response);
    wp_die();
}

//add_action( 'wp_ajax_blog_search', 'ajax_blog_search_callback' );
//add_action( 'wp_ajax_nopriv_blog_search', 'ajax_blog_search_callback' );

function ajax_blog_search_callback() {

    $search_str = $_POST['search'];

    $args = array(
        'post_type' => 'news',
        's' => $search_str,
        'sentence' => true,
        'paged' => $_POST['page'],
        'posts_per_page' => 8,
        'orderby' => ['post_date' => 'ASC']
    );

    $get_posts = new WP_Query($args);
    $posts = $get_posts->query($args);

    $posts_arr = array();

    foreach ($posts as $post) {
        $posts_arr[] = array (
            'link'  => get_permalink($post->ID),
            'title' => $post->post_title,
            'image' => get_the_post_thumbnail_url($post->ID),
            'time'  => date('d.m.Y', strtotime($post->post_date))
        );
    }

    $big = 9999999999;

    $args = array(
        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => '?paged=%#%',
        'type' => 'list',
        'current' => max( 1, $_POST['page'] ),
        'total' => $get_posts->max_num_pages
    );

    $pagination = upg_paginate_links($args);

    $response = array(
        'posts' => $posts_arr,
        'pagination' => $pagination
    );

    echo json_encode($response);
    wp_die();
}
