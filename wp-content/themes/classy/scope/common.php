<?php
/**
 * Data that will be accessible in every view.
 */

$body_additional = '';

if( is_single()) {
    $post_type = get_query_var( 'post_type' );
    $body_additional = 'p-'.$post_type;
}
elseif(is_post_type_archive()) {
    $post_type = get_query_var( 'post_type' );
    $body_additional = 'archive-'.$post_type;
}
else {
    $_post = get_queried_object();
    if($_post) $body_additional = 'p-'.$_post->post_name;
}

$data = array(
    //'menu' => new Classy\Menu(),
    'body_additional' => $body_additional
);
