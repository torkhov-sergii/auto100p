<?php
$framework = get_theme_framework();

$post = $framework::get_post(false);

$news = $framework::get_posts([
    'post_type' => 'news',
    'orderby' => ['post_date' => 'ASC'],
    'posts_per_page' => 4,
]);

foreach ($news as &$one_news) {
    $one_news->excerpt = upg_excerpt(['text'=>$one_news->post_content, 'maxchar' => 80, 'post' => $one_news]);
}

$fields = array(
    'single_new_main_image' => $post->getAcfByKey('single_new_main_image')
);

$data = compact(
    'post', 'news', 'fields'
);
