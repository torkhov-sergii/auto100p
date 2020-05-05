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

        <link rel="stylesheet" href="{{ \Helpers\General::asset_hash('/wp-content/themes/classy/dist/main.css') }}">
    </head>

    <body {{ body_class($body_additional) }}>

        <div class="wrapper" id="top">
            {{ get_header() }}

            <div class="content">
                @yield('content')
            </div>

            {{ get_footer() }}
        </div>

        {{ wp_footer() }}

        <script src="{{ \Helpers\General::asset_hash('/wp-content/themes/classy/dist/index.js') }}"></script>
    </body>
</html>
