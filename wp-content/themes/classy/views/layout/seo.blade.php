<?php
global $post;
$post = new \Classy\Models\BasisPost($post);
$yoast_image = $post->getAcfByKey('_yoast_wpseo_opengraph-image');

$title = wp_title(false, false, 'right');
if(!$title) $title = get_bloginfo( 'name' );

$description = get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
if(!$description && $post->post_content != '') $description = esc_attr(substr(strip_tags($post->post_content), 0, 200));
if(!$description) $description = get_bloginfo( 'description' );
?>

@if(!$yoast_image)
    <meta property="og:image" content="{{ $post->getAcfImage()->src('large') }}"/>
    <meta property="og:image:width" content="500">
    <meta property="og:image:height" content="400">
@endif

<title>{!! $title !!}</title>
<meta property="og:title" content="{!! $title !!}"/>

@if($description)
    <meta name="description" content="{{ $description }}">
    <meta property="og:description" content="{{ $description }}">
@endif

<meta property="og:type" content="website">
<meta property="og:url" content="{{ $post->get_permalink() }}">
