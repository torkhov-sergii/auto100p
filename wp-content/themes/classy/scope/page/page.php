<?php
$framework = get_theme_framework();

$page = $framework::get_post();

$data = compact(
    'page'
);
