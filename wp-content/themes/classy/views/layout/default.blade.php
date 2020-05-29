<!DOCTYPE html>
<html {{ language_attributes() }} class="no-js">
    <head>

        <meta charset="{{ bloginfo( 'charset' ) }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <script>
            var hostname = "<?php echo constant('HOSTNAME') ?>";
            var ajaxurl = "<?php echo constant('HOSTNAME') ?>/wp-admin/admin-ajax.php";
        </script>

        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,600i,700,700i|Poppins:300,400,500,600,700&subset=latin" />

        {{ wp_head() }}

        @include('layout.seo')

        @include('layout.services')

        <link rel="apple-touch-icon" sizes="180x180" href="/wp-content/themes/classy/dist/img/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/wp-content/themes/classy/dist/img/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/wp-content/themes/classy/dist/img/favicon/favicon-16x16.png">
        <link rel="manifest" href="/wp-content/themes/classy/dist/img/favicon/site.webmanifest">
        <link rel="mask-icon" href="/wp-content/themes/classy/dist/img/favicon/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <link rel="stylesheet" href="{{ \Helpers\General::asset_hash('/wp-content/themes/classy/dist/main.css') }}">
    </head>

    <body {{ body_class($body_additional) }}>

        <div class="wrapper" id="top">
            {{ get_header() }}

            {!! kama_breadcrumbs('/', [], [
                'on_front_page' => false,
                'markup' => [
                    'wrappatt'  => '<div class="breadcrumbs"><div class="container">%s</div></div>',
                    'linkpatt'  => '<div class="breadcrumbs__item"><a class="breadcrumbs__link" href="%s">%s</a></div>',
                ],
            ]) !!}

            <div class="content">
                @yield('content')
            </div>

            {{ get_footer() }}
        </div>

        {{ wp_footer() }}

        <script src="{{ \Helpers\General::asset_hash('/wp-content/themes/classy/dist/index.js') }}"></script>
    </body>
</html>
