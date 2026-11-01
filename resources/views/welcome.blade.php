
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>BNU</title>


        {{-- <link href="{{assets('css/app.css')}}" rel="stylesheet"> --}}
        <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
      
	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- Title -->	

	<!--Favicon -->
	<link rel="icon" href="{{ asset('images/brand/favicon.ico') }}" type="image/x-icon" />

	<!-- Bootstrap css -->
	<link href="{{ asset('plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet" />

	<!-- Style css -->
	<link href="{{ asset('css/style.css') }}" rel="stylesheet" />

	<!-- Dark css -->
	<link href="{{ asset('css/dark.css') }}" rel="stylesheet" />
	<!-- <link href="{{ asset('css/font-awesome-4.7.0/css/font-awesome.css') }}" rel="stylesheet" > -->

	<!-- Skins css -->
	<link href="{{ asset('css/skins.css') }}" rel="stylesheet" />

	<!-- Animate css -->
	<link href="{{ asset('css/animated.css') }}" rel="stylesheet" />

	<link href="{{ asset('plugins/accordion/accordion.css') }}" rel="stylesheet" />

	<!--Sidemenu css -->
	<link id="theme" href="{{ asset('css/sidemenu2.css') }}" rel="stylesheet">

	<!-- P-scroll bar css-->
	<link href="{{ asset('plugins/p-scrollbar/p-scrollbar.css') }}" rel="stylesheet" />
	<!-- Data table css -->
	<link href="{{ asset('plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
	<link href="{{ asset('plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
	<link href="{{ asset('plugins/datatable/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
	<!-- Select2 css -->
	<link href="{{ asset('plugins/select2/select2.min.css') }}" rel="stylesheet" />
	<!-- <link rel="stylesheet" type="text/css" href="{{asset('css/font-awesome.min.css')}}"> -->

	<!-- Charts -->
	<!-- <link href="{{ asset('plugins/charts-c3/c3-chart.css') }}" rel="stylesheet" /> -->
	<!-- c3.js Charts js-->
	<!-- <script src="{{ asset('plugins/charts-c3/d3.v5.min.js') }}"></script>
		<script src="{{ asset('plugins/charts-c3/c3-chart.js') }}"></script>
		<script src="{{ asset('js/charts.js') }}"></script> -->

	<!-- Fullcalendar css-->
	<link href="{{ asset('plugins/fullcalendar/fullcalendar.css') }}" rel='stylesheet' />
	<link href="{{ asset('plugins/fullcalendar/fullcalendar.print.min.css') }}" rel='stylesheet' media='print' />

	<!---Icons css-->
	<link href="{{ asset('plugins/web-fonts/icons.css') }}" rel="stylesheet" />
	<link href="{{ asset('plugins/web-fonts/font-awesome/font-awesome.min.css') }}" rel="stylesheet">
	<link href="{{ asset('plugins/web-fonts/plugin.css') }}" rel="stylesheet" />


	<link href="{{ asset('https://unpkg.com/intro.js/minified/introjs.min.css') }}" rel="stylesheet" />



	<!-- Fonts -->
	<link rel="dns-prefetch" href="//fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
	


    </head>
    <body>
        <noscript>You need to enable JavaScript to run this app.</noscript>
        <div id="example">
        </div>

        <script src="js/app.js" ></script>

   	<!-- Jquery js-->
		<script src="{{ asset('js/vendors/jquery-3.5.1.min.js') }}"></script>

		<script src="{{asset('js/jquery-ui/js/jquery-ui.min.js')}}"></script>

		<!-- Full-calendar js-->
		<!-- <script src="{{ asset('plugins/fullcalendar/moment.min.js') }}"></script>
		<script src="{{ asset('plugins/fullcalendar/fullcalendar.min.js') }}"></script>
		<script src="{{ asset('js/app-calendar-events.js') }}"></script>
		<script src="{{ asset('js/app-calendar.js') }}"></script> -->

		<!-- Bootstrap4 js-->
		<script src="{{ asset('plugins/bootstrap/popper.min.js') }}"></script>
		<script src="{{ asset('plugins/bootstrap/js/bootstrap.min.js') }}"></script>

		<script src="{{asset('https://unpkg.com/intro.js/minified/intro.min.js') }}"></script>

		<!--Othercharts js-->
		<script src="{{ asset('plugins/othercharts/jquery.sparkline.min.js') }}"></script>

		<!-- Circle-progress js-->
		<script src="{{ asset('js/vendors/circle-progress.min.js') }}"></script>

		<!-- Jquery-rating js-->
		<script src="{{ asset('plugins/rating/jquery.rating-stars.js') }}"></script>

		<!--Sidemenu js-->
		<script src="{{ asset('plugins/sidemenu/sidemenu2.js') }}"></script>

		<!-- P-scroll js-->
		<script src="{{ asset('plugins/p-scrollbar/p-scrollbar.js') }}"></script>
		<script src="{{ asset('plugins/p-scrollbar/p-scroll1.js') }}"></script>

		<script src="{{ asset('plugins/accordion/accordion.min.js') }}"></script>
		<script src="{{ asset('js/accordion.js') }}"></script>

		<script src="{{ asset('js/bootstrap-show-modal.js') }}"></script>

	
		<script src="{{ asset('plugins/fullcalendar/moment.min.js') }}"></script>

		<!--Select2 js -->
		<script src="{{ asset('plugins/select2/select2.full.min.js') }}"></script>
		<script src="{{ asset('js/select2.js') }}"></script>

		<!-- Custom js-->
		<script src="{{asset('js/custom.js') }}"></script>

		<!-- CMS js-->
		<script src="{{asset('js/cms/http.js') }}?ver=<?= time() ?>"></script>

		<!-- CMS js-->
		<script src="{{asset('js/grader/requests.js') }}"></script>

		<script src="{{asset('plugins/echarts/echarts.js') }}"></script>
		<!-- CMS js-->
		<script src="{{asset('js/grader/course_content.js') }}?ver=<?= time() ?>"></script>
		<!-- easy ui -->
		<link rel="stylesheet" type="text/css" href="{{asset('easyui/jquery-easyui-1.5/themes/gray/easyui.css')}}">
		<link rel="stylesheet" type="text/css" href="{{asset('easyui/jquery-easyui-1.5/themes/icon.css')}}">


    </body>
</html>

