<?php
$framework = get_theme_framework();

$news = $framework::get_posts([
    'post_type' => 'news',
    'orderby' => ['post_date' => 'ASC'],
    'posts_per_page' => 8,
]);

$query = new WP_Query(array(
    'post_type' => 'news',
    'orderby' => ['post_date' => 'ASC'],
    'posts_per_page' => 8,
));

$tags = get_terms('tag');

$big = 9999999999;

$args = array(
    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
    'format' => '?paged=%#%',
    'type' => 'list',
    'current' => max( 1, get_query_var('paged') ),
    'total' => $query->max_num_pages
);

$pagination = upg_paginate_links($args);

$data = compact(
    'news','tags', 'pagination'
);
