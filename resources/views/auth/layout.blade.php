<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>

    <!-- Meta data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta content="Grader | BNU" name="description">
    <meta content="Grader" name="author">
    <meta name="keywords" content="CMS, Grader, BNU" />

    <!-- Title -->
    <title>CMS | BNU</title>

    <!--Favicon -->
    <link rel="icon" href="{{ asset('images/brand/favicon.ico') }} " type="image/x-icon" />

    <!-- Preload Background Image -->
    <link rel="preload" as="image" href="{{ asset('assets/images/bg/loginBg.png') }}">

    <!-- Bootstrap css -->
    <link href="{{ asset('plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet" />

    <!-- Colors css -->
    <link href="{{ asset('css/colors.css') }}?v={{ time() }}" rel="stylesheet" />

    <!-- Style css -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />

    <!-- Dark css -->
    <link href="{{ asset('css/dark.css') }}" rel="stylesheet" />

    <!-- Skins css -->
    <link href="{{ asset('css/skins.css') }}" rel="stylesheet" />

    <!-- Animate css -->
    <link href="{{ asset('css/animated.css') }}" rel="stylesheet" />

    <!---Icons css-->
    <link href="{{ asset('plugins/web-fonts/icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('plugins/web-fonts/font-awesome/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/web-fonts/plugin.css') }}" rel="stylesheet" />
    <link href="{{ asset('') }}">
</head>


<style>
    .page-style1-custom {
        background-size: 100% 100%;
        height: 100%;
        width: 100%;
    }

    .background {
        background: url('../assets/images/bg/loginBg.png') no-repeat center center fixed;
        background-size: 100% 100%;
        width: 100%;
        height: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: -1;
    }

    body#background {
        background: url('../assets/images/bg/loginBg.png') no-repeat center center fixed;
        background-size: 100% 100%;
    }

    /*
#background {
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: -1;
  transition: background-color 1s ease;
  background-color:yellow !important;
} */
</style>


<body id="background" class="h-100vh page-style1-custom light-mode default-sidebar"
    style="background: url('{{ asset('assets/images/bg/loginBg.png') }}') no-repeat center center fixed; background-size: 100% 100%; min-height: 100vh;">

    <div class="background"></div>

    <div class="page">
        <div class="page-single">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Jquery js-->
    <script src="{{ asset('js/vendors/jquery-3.5.1.min.js') }}"></script>

    <!-- Bootstrap4 js-->
    <script src="{{ asset('plugins/bootstrap/popper.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.min.js') }}"></script>

    <!--Othercharts js-->
    <script src="{{ asset('plugins/othercharts/jquery.sparkline.min.js') }}"></script>

    <!-- Circle-progress js-->
    <script src="{{ asset('js/vendors/circle-progress.min.js') }}"></script>

    <!-- Jquery-rating js-->
    <script src="{{ asset('plugins/rating/jquery.rating-stars.js') }}"></script>
    @yield('javascript')
</body>

</html>
