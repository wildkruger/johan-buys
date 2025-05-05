<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ meta(Route::current()->uri(), 'description') }}">
    <meta name="keywords" content="{{ meta(Route::current()->uri(), 'keywords') }}">
    <title>{{ meta(Route::current()->uri(), 'title') }}<?= isset($additionalTitle) ? ' | '.$additionalTitle : '' ?></title>

    @include('cryptoexchange::frontend.layouts.style')

    <!---title logo icon-->
    <link rel="javascript" href="{{theme_asset('public/js/respond.min.js')}}">
    <!---favicon-->
    <link rel="shortcut icon" href="{{ faviconPath() }}" />

    <script type="text/javascript">
        var SITE_URL = "{{url('/')}}";
    </script>

</head>

<body>

    <!--main section-->

    <div class="container-page">

        @include('frontend.layouts.common.header')

        @yield('content')

        @include('frontend.layouts.common.footer_menu')

    </div>

    @include('cryptoexchange::frontend.layouts.script')

    @yield('js')

</body>

</html>