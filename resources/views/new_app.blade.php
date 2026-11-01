<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}  dir=" ltr">

<head>
	<script>
		var url = '<?= url('/') ?>';
		var token = '<?= (isset(auth()->user()->api_token) ? auth()->user()->api_token : '') ?>';
	</script>
	<!-- Meta data -->
	<meta charset="UTF-8">
	<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
	<meta content="CMS | BNU" name="description">
	<meta content="ITRC BNU | <?= env('VERSION') ?>" name="author">
	<meta name="keywords" content="CMS, BNU, Grader, ITRC, School, University, Teacher" />
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />

	<?php if (auth()->user()->id) { /* if user login */ ?>
		<!-- CSRF Token -->
		<meta name="csrf-token" content="{{ csrf_token() }}">
	<?php } ?>

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- Title -->
	<title><?= @$pageTitle ?> | CMS | {{ config('app.name', 'BNU') }}</title>

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
	<style>
		.for_li{
			/* background-color: #D2D6F1; */
			color: #4454C3 !important;
			border-radius: 5px 5px 5px 5px !important;
			/* border: solid 1px #4454C3; */
			padding-left: 36px !important;  
			padding-top:2px !important;
			font-weight: bold !important;
			
		}
		.for_ul{
			background-color: white !important;
			color: #4454C3 !important;
			border-radius: 10px 10px 10px 10px !important;
			border: solid 0px #4454C3;
			padding: 3px;  */
		}
		.card-header {

			/* background-color: #D2D6F1;
			color: #4454C3 !important;
			border-radius: 10px 10px 0px 0px !important;
			border: solid 0px #4454C3;
			padding: 20px;  */

			/* font-family: Georgia, 'Times New Roman', Times, serif; */


			/* background: rgb(68,176,63); */

			/*14 Auguest 
			
			color:#FFFFFF;
			background: linear-gradient(90deg, rgba(68,176,63,1) 8%, rgba(135,215,41,1) 100%, rgba(161,191,80,0.9136029411764706) 100%);
			*/
		}

		.card-header22 {
			background-color: #484F77;
			color: #FFFFFF !important;
			border-radius: 10px 10px 0px 0px !important;
			padding: 20px;
			/* font-family: Georgia, 'Times New Roman', Times, serif; */

		}

		body {
			font-family: 'M PLUS Rounded 1c', sans-serif;
		}

		.header.top-header .header-option,
		.header.top-header .header-setting {
			display: initial !important;
		}

		.modal-backdrop.show {
			opacity: 0.8
		}
		</style>
</head>
<?php if (auth()->user()->id) { ?>

	<body class="app sidebar-mini light-mode default-sidebar">

		<!---Global-loader-->
		<!-- <div id="global-loader">
		<img src="{{ asset('images/svgs/loader.svg') }}" alt="loader">
	</div> -->

		<div class="page">
			<div class="page-main">
				<!--aside open-->
				<div class="app-sidebar app-sidebar2">
					<div class="app-sidebar__logo">
						<a class="header-brand  home_btn" data-bm="home" href="javascript:void(0);">
							<img src="{{ asset('images/brand/logo.png') }}" class="header-brand-img desktop-lgo" alt="Covido logo">
							<img src="{{ asset('images/brand/logo1.png') }}" class="header-brand-img dark-logo" alt="Covido logo">
							<img src="{{ asset('images/brand/favicon.png') }}" class="header-brand-img mobile-logo" alt="Covido logo">
							<img src="{{ asset('images/brand/favicon1.png') }}" class="header-brand-img darkmobile-logo" alt="Covido logo">
						</a>
					</div>
				</div>


				<!-- SideBar Start-->
				@include('layouts.new_sidebar')
				<!-- SideBar End-->


				<!--aside closed-->

				<div class="app-content main-content">
					<div class="side-app">

						<!--app header-->
						<div class="app-header header top-header">
							<div class="container-fluid">
								<div class="d-flex">
									<a class="header-brand  home_btn" data-bm="home" href="javascript:void(0);">
										<img src="{{ asset('images/brand/logo.png') }}" class="header-brand-img desktop-lgo" alt=" logo">
										<img src="{{ asset('images/brand/logo1.png') }}" class="header-brand-img dark-logo" alt=" logo">
										<img src="{{ asset('images/brand/favicon.png') }}" class="header-brand-img mobile-logo" alt=" logo">
										<img src="{{ asset('images/brand/favicon1.png') }}" class="header-brand-img darkmobile-logo" alt=" logo">
									</a>
									<div class="dropdown side-nav">
										<div class="app-sidebar__toggle" data-toggle="sidebar">
											<a class="open-toggle" href="#">
											<svg class="header-icon mt-1" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
													<path d="M0 0h24v24H0V0z" fill="none" />
													<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" />
												</svg>
											
											</a>
											<a class="close-toggle" href="#">
											<svg class="header-icon mt-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
													<line x1="3" y1="12" x2="21" y2="12"></line>
													<line x1="3" y1="6" x2="21" y2="6"></line>
													<line x1="3" y1="18" x2="21" y2="18"></line>
												</svg>
											</a>
										</div>
									</div>
									<div class="dropdown  header-option">
										<a class="nav-link icon p-0" data-toggle="dropdown">
											<svg class="header-icon" x="1008" y="1248" viewBox="0 0 24 24" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
												<path opacity=".3" d="M19.28,8.6 L18.58,7.39 L17.31,7.9 L16.25,8.33 L15.34,7.63 C14.95,7.33 14.54,7.09 14.11,6.92 L13.05,6.49 L12.89,5.36 L12.7,4 L11.3,4 L11.11,5.35 L10.95,6.48 L9.89,6.92 C9.48,7.09 9.07,7.33 8.64,7.65 L7.74,8.33 L6.69,7.91 L5.42,7.39 L4.72,8.6 L5.8,9.44 L6.69,10.14 L6.55,11.27 C6.52,11.57 6.5,11.8 6.5,12 C6.5,12.2 6.52,12.43 6.55,12.73 L6.69,13.86 L5.8,14.56 L4.72,15.4 L5.42,16.61 L6.69,16.1 L7.75,15.67 L8.66,16.37 C9.05,16.67 9.46,16.91 9.89,17.08 L10.95,17.51 L11.11,18.64 L11.3,20 L12.69,20 L12.88,18.65 L13.04,17.52 L14.1,17.09 C14.51,16.92 14.92,16.68 15.35,16.36 L16.25,15.68 L17.29,16.1 L18.56,16.61 L19.26,15.4 L18.18,14.56 L17.29,13.86 L17.43,12.73 C17.47,12.42 17.48,12.21 17.48,12 C17.48,11.79 17.46,11.57 17.43,11.27 L17.29,10.14 L18.18,9.44 L19.28,8.6 Z M12,16 C9.79,16 8,14.21 8,12 C8,9.79 9.79,8 12,8 C14.21,8 16,9.79 16,12 C16,14.21 14.21,16 12,16 Z"></path>
												<path d="M19.43,12.98 C19.47,12.66 19.5,12.34 19.5,12 C19.5,11.66 19.47,11.34 19.43,11.02 L21.54,9.37 C21.73,9.22 21.78,8.95 21.66,8.73 L19.66,5.27 C19.57,5.11 19.4,5.02 19.22,5.02 C19.16,5.02 19.1,5.03 19.05,5.05 L16.56,6.05 C16.04,5.65 15.48,5.32 14.87,5.07 L14.49,2.42 C14.46,2.18 14.25,2 14,2 L10,2 C9.75,2 9.54,2.18 9.51,2.42 L9.13,5.07 C8.52,5.32 7.96,5.66 7.44,6.05 L4.95,5.05 C4.89,5.03 4.83,5.02 4.77,5.02 C4.6,5.02 4.43,5.11 4.34,5.27 L2.34,8.73 C2.21,8.95 2.27,9.22 2.46,9.37 L4.57,11.02 C4.53,11.34 4.5,11.67 4.5,12 C4.5,12.33 4.53,12.66 4.57,12.98 L2.46,14.63 C2.27,14.78 2.22,15.05 2.34,15.27 L4.34,18.73 C4.43,18.89 4.6,18.98 4.78,18.98 C4.84,18.98 4.9,18.97 4.95,18.95 L7.44,17.95 C7.96,18.35 8.52,18.68 9.13,18.93 L9.51,21.58 C9.54,21.82 9.75,22 10,22 L14,22 C14.25,22 14.46,21.82 14.49,21.58 L14.87,18.93 C15.48,18.68 16.04,18.34 16.56,17.95 L19.05,18.95 C19.11,18.97 19.17,18.98 19.23,18.98 C19.4,18.98 19.57,18.89 19.66,18.73 L21.66,15.27 C21.78,15.05 21.73,14.78 21.54,14.63 L19.43,12.98 Z M17.45,11.27 C17.49,11.58 17.5,11.79 17.5,12 C17.5,12.21 17.48,12.43 17.45,12.73 L17.31,13.86 L18.2,14.56 L19.28,15.4 L18.58,16.61 L17.31,16.1 L16.27,15.68 L15.37,16.36 C14.94,16.68 14.53,16.92 14.12,17.09 L13.06,17.52 L12.9,18.65 L12.7,20 L11.3,20 L11.11,18.65 L10.95,17.52 L9.89,17.09 C9.46,16.91 9.06,16.68 8.66,16.38 L7.75,15.68 L6.69,16.11 L5.42,16.62 L4.72,15.41 L5.8,14.57 L6.69,13.87 L6.55,12.74 C6.52,12.43 6.5,12.2 6.5,12 C6.5,11.8 6.52,11.57 6.55,11.27 L6.69,10.14 L5.8,9.44 L4.72,8.6 L5.42,7.39 L6.69,7.9 L7.73,8.32 L8.63,7.64 C9.06,7.32 9.47,7.08 9.88,6.91 L10.94,6.48 L11.1,5.35 L11.3,4 L12.69,4 L12.88,5.35 L13.04,6.48 L14.1,6.91 C14.53,7.09 14.93,7.32 15.33,7.62 L16.24,8.32 L17.3,7.89 L18.57,7.38 L19.27,8.59 L18.2,9.44 L17.31,10.14 L17.45,11.27 Z M12,8 C9.79,8 8,9.79 8,12 C8,14.21 9.79,16 12,16 C14.21,16 16,14.21 16,12 C16,9.79 14.21,8 12,8 Z M12,14 C10.9,14 10,13.1 10,12 C10,10.9 10.9,10 12,10 C13.1,10 14,10.9 14,12 C14,13.1 13.1,14 12,14 Z"></path>
											</svg>
											<span class="nav-span"> <i class="fa fa-angle-down ml-1 fs-18"></i></span>
										</a>



										<div class="dropdown-menu dropdown-menu-left dropdown-menu-arrow animated custom">

											<span id="db-session"></span>
											<!-- <a class="dropdown-item" href="#">
											Fall 2018
										</a>--->

										</div>
									</div>
									<div class="d-flex order-lg-2 ml-auto">
										<!-- <a href="#" data-toggle="search" class="nav-link nav-link-lg d-md-none navsearch">
											<svg class="header-icon search-icon" x="1008" y="1248" viewBox="0 0 24 24"  height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
												<path d="M0 0h24v24H0V0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
											</svg>
										</a> -->
										<!-- <div class="mt-1">
											<form class="form-inline">
												<div class="search-element">
													<input type="search" class="form-control header-search" placeholder="Search…" aria-label="Search" tabindex="1">
													<button class="btn btn-primary-color" type="submit">
													<svg class="header-icon search-icon" x="1008" y="1248" viewBox="0 0 24 24"  height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
														<path d="M0 0h24v24H0V0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
													 </svg>
													</button>
												</div>
											</form>
										</div> -->
										<!-- SEARCH -->
										<a href="/contacts" class="nav-link pr-0 leading-none">
											<span>

												<!--img src="{{asset('/staffimages/cmshelp.png')}}" alt="img" width="55px"-->
											</span>
										</a>
										<a href="/cvt" class="nav-link pr-0 leading-none">
											<span>
												<img src="{{asset('/staffimages/helpcms1.png')}}" alt="img" width="44px">
											</span>
										</a>
										<div class="dropdown   header-fullscreen pl-5">
											<a class="nav-link icon full-screen-link p-0" id="fullscreen-button">
												<svg class="header-icon" x="1008" y="1248" viewBox="0 0 24 24" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
													<path d="M7,14 L5,14 L5,19 L10,19 L10,17 L7,17 L7,14 Z M5,10 L7,10 L7,7 L10,7 L10,5 L5,5 L5,10 Z M17,17 L14,17 L14,19 L19,19 L19,14 L17,14 L17,17 Z M14,5 L14,7 L17,7 L17,10 L19,10 L19,5 L14,5 Z"></path>
												</svg>
											</a>
										</div>
										{{-- <div class="dropdown header-notify pl-4">
										<a class="nav-link icon p-0" data-toggle="dropdown">
											<svg class="header-icon" x="1008" y="1248" viewBox="0 0 24 24" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
												<path opacity=".3" d="M12 6.5c-2.49 0-4 2.02-4 4.5v6h8v-6c0-2.48-1.51-4.5-4-4.5z"></path>
												<path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-11c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2v-5zm-2 6H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6zM7.58 4.08L6.15 2.65C3.75 4.48 2.17 7.3 2.03 10.5h2a8.445 8.445 0 013.55-6.42zm12.39 6.42h2c-.15-3.2-1.73-6.02-4.12-7.85l-1.42 1.43a8.495 8.495 0 013.54 6.42z"></path>
											</svg>
											<span class="pulse "></span>
										</a>
										<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow  animated">
											<a href="#" class="dropdown-item d-flex pb-3">
												<svg class="header-icon mr-4" x="1008" y="1248" viewBox="0 0 24 24" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
													<path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3" />
													<path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z" />
												</svg>
												<div>
													<div class="font-weight-bold">Message Sent.</div>
													<div class="small text-muted">3 hours ago</div>
												</div>
											</a>
											<!-- <a href="#" class="dropdown-item d-flex pb-3">
													<svg class="header-icon mr-4" x="1008" y="1248" viewBox="0 0 24 24"  height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
														<path d="M0 0h24v24H0V0z" fill="none"/><path d="M15.55 11l2.76-5H6.16l2.37 5z" opacity=".3"/><path d="M15.55 13c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.37-.66-.11-1.48-.87-1.48H5.21l-.94-2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.48 17 7 17h12v-2H7l1.1-2h7.45zM6.16 6h12.15l-2.76 5H8.53L6.16 6zM7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
													<div>
														<div class="font-weight-bold"> Order Placed</div>
														<div class="small text-muted">5  hour ago</div>
													</div>
												</a>
												<a href="#" class="dropdown-item d-flex pb-3">
													<svg class="header-icon mr-4" x="1008" y="1248" viewBox="0 0 24 24"  height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
														<path d="M5 8h14V6H5z" opacity=".3"/><path d="M7 11h2v2H7zm12-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zm-4 3h2v2h-2zm-4 0h2v2h-2z"/>
													</svg>
													<div>
														<div class="font-weight-bold"> Event Started</div>
														<div class="small text-muted">45 mintues ago</div>
													</div>
												</a>
												<a href="#" class="dropdown-item d-flex pb-3">
													<svg class="header-icon mr-4" x="1008" y="1248" viewBox="0 0 24 24"  height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
														<path d="M0 0h24v24H0V0z" fill="none"/><path d="M18 13h5v7h-5z" opacity=".3"/><path d="M23 11.01L18 11c-.55 0-1 .45-1 1v9c0 .55.45 1 1 1h5c.55 0 1-.45 1-1v-9c0-.55-.45-.99-1-.99zM23 20h-5v-7h5v7zM2 4h18v5h2V4c0-1.11-.9-2-2-2H2C.89 2 0 2.89 0 4v12c0 1.1.89 2 2 2h7v2H7v2h8v-2h-2v-2h2v-2H2V4zm9 2l-.97 3H7l2.47 1.76-.94 2.91 2.47-1.8 2.47 1.8-.94-2.91L15 9h-3.03z"/></svg>
													<div>
														<div class="font-weight-bold">Your Admin lanuched</div>
														<div class="small text-muted">1 daya ago</div>
													</div>
												</a> -->
											<div class=" text-center p-2 border-top">
												<a href="#" class="">View All Notifications</a>
											</div>
										</div>
									</div> --}}
										<div class="dropdown profile-dropdown">
											<a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
												<span>
													<img src="{{asset('/staffimages/'.auth()->user()->employee_id.'.jpg')}}" alt="img" class="avatar avatar-md brround">
												</span>
											</a>
											<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow animated">
												<div class="text-center">
													<a href="#" class="dropdown-item text-center user pb-0 font-weight-bold"><?= auth()->user()->username ?></a>
													<!-- <span class="text-center user-semi-title">HOD SB</span> -->
													<div class="dropdown-divider"></div>
												</div>
												<!-- <a class="dropdown-item d-flex" href="#">
													<svg class="header-icon mr-3" x="1008" y="1248" viewBox="0 0 24 24"  height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
														<path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 16c-2.69 0-5.77 1.28-6 2h12c-.2-.71-3.3-2-6-2z" opacity=".3"/><circle cx="12" cy="8" opacity=".3" r="2"/><path d="M12 14c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm-6 4c.22-.72 3.31-2 6-2 2.7 0 5.8 1.29 6 2H6zm6-6c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z"/>
													</svg>
													<div class="mt-1">Profile</div>
												</a> -->
												<!-- <a class="dropdown-item d-flex" href="#">
													<svg class="header-icon mr-3" x="1008" y="1248" viewBox="0 0 24 24"  height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
														<path opacity=".3" d="M19.28,8.6 L18.58,7.39 L17.31,7.9 L16.25,8.33 L15.34,7.63 C14.95,7.33 14.54,7.09 14.11,6.92 L13.05,6.49 L12.89,5.36 L12.7,4 L11.3,4 L11.11,5.35 L10.95,6.48 L9.89,6.92 C9.48,7.09 9.07,7.33 8.64,7.65 L7.74,8.33 L6.69,7.91 L5.42,7.39 L4.72,8.6 L5.8,9.44 L6.69,10.14 L6.55,11.27 C6.52,11.57 6.5,11.8 6.5,12 C6.5,12.2 6.52,12.43 6.55,12.73 L6.69,13.86 L5.8,14.56 L4.72,15.4 L5.42,16.61 L6.69,16.1 L7.75,15.67 L8.66,16.37 C9.05,16.67 9.46,16.91 9.89,17.08 L10.95,17.51 L11.11,18.64 L11.3,20 L12.69,20 L12.88,18.65 L13.04,17.52 L14.1,17.09 C14.51,16.92 14.92,16.68 15.35,16.36 L16.25,15.68 L17.29,16.1 L18.56,16.61 L19.26,15.4 L18.18,14.56 L17.29,13.86 L17.43,12.73 C17.47,12.42 17.48,12.21 17.48,12 C17.48,11.79 17.46,11.57 17.43,11.27 L17.29,10.14 L18.18,9.44 L19.28,8.6 Z M12,16 C9.79,16 8,14.21 8,12 C8,9.79 9.79,8 12,8 C14.21,8 16,9.79 16,12 C16,14.21 14.21,16 12,16 Z"></path>
														<path d="M19.43,12.98 C19.47,12.66 19.5,12.34 19.5,12 C19.5,11.66 19.47,11.34 19.43,11.02 L21.54,9.37 C21.73,9.22 21.78,8.95 21.66,8.73 L19.66,5.27 C19.57,5.11 19.4,5.02 19.22,5.02 C19.16,5.02 19.1,5.03 19.05,5.05 L16.56,6.05 C16.04,5.65 15.48,5.32 14.87,5.07 L14.49,2.42 C14.46,2.18 14.25,2 14,2 L10,2 C9.75,2 9.54,2.18 9.51,2.42 L9.13,5.07 C8.52,5.32 7.96,5.66 7.44,6.05 L4.95,5.05 C4.89,5.03 4.83,5.02 4.77,5.02 C4.6,5.02 4.43,5.11 4.34,5.27 L2.34,8.73 C2.21,8.95 2.27,9.22 2.46,9.37 L4.57,11.02 C4.53,11.34 4.5,11.67 4.5,12 C4.5,12.33 4.53,12.66 4.57,12.98 L2.46,14.63 C2.27,14.78 2.22,15.05 2.34,15.27 L4.34,18.73 C4.43,18.89 4.6,18.98 4.78,18.98 C4.84,18.98 4.9,18.97 4.95,18.95 L7.44,17.95 C7.96,18.35 8.52,18.68 9.13,18.93 L9.51,21.58 C9.54,21.82 9.75,22 10,22 L14,22 C14.25,22 14.46,21.82 14.49,21.58 L14.87,18.93 C15.48,18.68 16.04,18.34 16.56,17.95 L19.05,18.95 C19.11,18.97 19.17,18.98 19.23,18.98 C19.4,18.98 19.57,18.89 19.66,18.73 L21.66,15.27 C21.78,15.05 21.73,14.78 21.54,14.63 L19.43,12.98 Z M17.45,11.27 C17.49,11.58 17.5,11.79 17.5,12 C17.5,12.21 17.48,12.43 17.45,12.73 L17.31,13.86 L18.2,14.56 L19.28,15.4 L18.58,16.61 L17.31,16.1 L16.27,15.68 L15.37,16.36 C14.94,16.68 14.53,16.92 14.12,17.09 L13.06,17.52 L12.9,18.65 L12.7,20 L11.3,20 L11.11,18.65 L10.95,17.52 L9.89,17.09 C9.46,16.91 9.06,16.68 8.66,16.38 L7.75,15.68 L6.69,16.11 L5.42,16.62 L4.72,15.41 L5.8,14.57 L6.69,13.87 L6.55,12.74 C6.52,12.43 6.5,12.2 6.5,12 C6.5,11.8 6.52,11.57 6.55,11.27 L6.69,10.14 L5.8,9.44 L4.72,8.6 L5.42,7.39 L6.69,7.9 L7.73,8.32 L8.63,7.64 C9.06,7.32 9.47,7.08 9.88,6.91 L10.94,6.48 L11.1,5.35 L11.3,4 L12.69,4 L12.88,5.35 L13.04,6.48 L14.1,6.91 C14.53,7.09 14.93,7.32 15.33,7.62 L16.24,8.32 L17.3,7.89 L18.57,7.38 L19.27,8.59 L18.2,9.44 L17.31,10.14 L17.45,11.27 Z M12,8 C9.79,8 8,9.79 8,12 C8,14.21 9.79,16 12,16 C14.21,16 16,14.21 16,12 C16,9.79 14.21,8 12,8 Z M12,14 C10.9,14 10,13.1 10,12 C10,10.9 10.9,10 12,10 C13.1,10 14,10.9 14,12 C14,13.1 13.1,14 12,14 Z"	></path>
													</svg>
													<div class="mt-1">Settings</div>
												</a>
												<a class="dropdown-item d-flex" href="#">
													<svg class="header-icon mr-3" x="1008" y="1248" viewBox="0 0 24 24"  height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
														<path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3"/><path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z"/>
													</svg>
													<div class="mt-1">Messages</div>
												</a> -->


												<!-- <a class="dropdown-item d-flex" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"> -->

													 <a class="dropdown-item d-flex" href="/settings">
													<svg class="header-icon mr-3" x="1008" y="1248" viewBox="0 0 24 24"  height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
														<path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 16c-2.69 0-5.77 1.28-6 2h12c-.2-.71-3.3-2-6-2z" opacity=".3"/><circle cx="12" cy="8" opacity=".3" r="2"/><path d="M12 14c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm-6 4c.22-.72 3.31-2 6-2 2.7 0 5.8 1.29 6 2H6zm6-6c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z"/>
													</svg>
													<div class="mt-1">Profile</div>
												</a>
												<a class="dropdown-item d-flex" href="{{ url('new_logout') }}">


													<svg class="header-icon mr-4" x="1008" y="1248" viewBox="0 0 24 24" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
														<path d="M0 0h24v24H0V0zm0 0h24v24H0V0z" fill="none" />
														<path d="M6 20h12V10H6v10zm2-6h3v-3h2v3h3v2h-3v3h-2v-3H8v-2z" opacity=".3" />
														<path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM8.9 6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2H8.9V6zM18 20H6V10h12v10zm-7-1h2v-3h3v-2h-3v-3h-2v3H8v2h3z" />
													</svg>
													<div class="mt-1">{{ __('Logout') }}</div>
													<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
														@csrf
													</form>
												</a>
											

											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--/app header-->

						<!--Page header-->
						<div class="page-header" style="min-height:0px; margin : 0.3rem 0 0.3rem">
							<div class="page-leftheader" style="color:#6c757d">
								<b><?= @$pageTitle ?></b>
								<!-- <h1>	<button class="btn btn-info hello_btn" onclick="hello();">Hello</button> 	</h1>  -->
							</div>
							<!-- <div class="page-rightheader ml-auto d-lg-flex d-none">
								<ol class="breadcrumb">
									<!-- <li class="breadcrumb-item"><a href="#" class="d-flex"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3zm5 15h-2v-6H9v6H7v-7.81l5-4.5 5 4.5V18z"/><path d="M7 10.19V18h2v-6h6v6h2v-7.81l-5-4.5z" opacity=".3"/></svg><span class="breadcrumb-icon"> Home</span></a></li>
									<li class="breadcrumb-item"><a href="#">Pages</a></li> -->
									<li class="breadcrumb-item" style="display:none;" aria-current="page"><a href="<?= url('/'); ?>"> Home </a></li>
								</ol>
							</div>
						</div>
						<!--End Page header-->
	<?php
		if (session()->has('errors')) {
		?>
			<div class="alert alert-danger" role="alert">
				<?= session('errors')->first() ?>
			</div>
		<?php
		} ?>
		<div class="page-body message_div" style="padding:0px 15px"></div>
						<!-- Row -->
						@yield('content')
						<!-- End Row -->
					</div>
				</div><!-- end app-content-->
			</div>

			<!--Footer-->
			<!-- <footer class="footer">
				<div class="container">
					<div class="row align-items-center flex-row-reverse">
						<div class="col-md-12 col-sm-12 mt-3 mt-lg-0 text-center">
							Copyright © 2020 <a href="#">Dashtic</a>. Designed by <a href="#">Spruko Technologies Pvt.Ltd</a> All rights reserved.
						</div>
					</div>
				</div>
			</footer> -->
			<!-- End Footer-->




		</div>

		<!-- Back to top -->
		<a href="#top" id="back-to-top">
			<svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
				<path d="M0 0h24v24H0V0z" fill="none" />
				<path d="M4 12l1.41 1.41L11 7.83V20h2V7.83l5.58 5.59L20 12l-8-8-8 8z" />
			</svg>
		</a>

		<!-- Jquery js-->
		<script src="{{asset('js/vendors/jquery-3.5.1.min.js') }}"></script>

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

		<!-- Data tables -->
		<script src="{{ asset('plugins/datatable/js/jquery.dataTables.js') }}"></script>
		<script src="{{ asset('plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
		<script src="{{ asset('plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
		<script src="{{ asset('plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
		<script src="{{ asset('plugins/datatable/js/jszip.min.js') }}"></script>
		<script src="{{ asset('plugins/datatable/js/pdfmake.min.js') }}"></script>
		<script src="{{ asset('plugins/datatable/js/vfs_fonts.js') }}"></script>
		<script src="{{ asset('plugins/datatable/js/buttons.html5.min.js') }}"></script>
		<script src="{{ asset('plugins/datatable/js/buttons.print.min.js') }}"></script>
		<script src="{{ asset('plugins/datatable/js/buttons.colVis.min.js') }}"></script>
		<script src="{{ asset('plugins/datatable/dataTables.responsive.min.js') }}"></script>
		<script src="{{ asset('plugins/datatable/responsive.bootstrap4.min.js') }}"></script>
		<script src="{{ asset('js/datatables.js') }}"></script>
		<script src="{{ asset('pages/dashboard/ecommerce-dashboard.js') }}"></script>
		<script src="{{ asset('pages/chart/echarts/js/echarts-all.js') }}"></script>
		
		<!-- <script  src="{{ asset('plugins/datatable/datatable/jquery.dataTables.min.js') }}"></script>
        <script  src="{{ asset('plugins/datatable/datatable/dataTables.keyTable.min.js') }}"></script>
         <script  src="{{ asset('plugins/datatable/datatable/dataTables.buttons.min.js') }}"></script> -->

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


<div class="modal" id="welcome-modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #eec0c6;background-image: linear-gradient(315deg, #eec0c6 0%, #7ee8fa 74%);">
				<h5 class="modal-title">CMS updates</h5>

			</div>
			<div class="modal-body">
				<h4 style="text-align: center;color:green;">Dear Faculty Member</h4>
				<hr style="padding:0px;">
				<p class="lead" style="font-weight:bold;">Please Note<p>
			<div style="margin:15px;">
			<ul class="list-style">
				<li class="U+02192">Please note that as per the decision of the Management    Committee of BNU all those students whose attendance in any course fell short of the prescribed attendance percentage shall automatically receive a W grade. You are requested to please inform your students accordingly.</li>
			</ul>	
		</div>
	</div>
		<div class="modal-footer" style="background-color: #eec0c6;background-image: linear-gradient(315deg, #eec0c6 0%, #7ee8fa 74%);">
			<button type="button" class="btn btn-secondary" onclick="confirmWelcomeNote();">Close</button>
		</div>
	</div>
	</div>
</div>

<!-- ///////////////////////// -->

<div class="modal" id="mobile-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header" style="background-color: #eec0c6;background-image: linear-gradient(315deg, #eec0c6 0%, #7ee8fa 74%);">
						<h5 class="modal-title">Personal Details</h5>
					</div>
                    <div class="modal-body">
                        <h4 style="text-align: left;color:green;">Dear Faculty Member Please verify Your Contact Details </h4>
                        <div class="container">
                        <div class="row justify-content-md-center">
                            <div class="col-md-12 text-center">
                                <div id="detail">

									<div class="form-group row" style="margin-bottom:0px">
										<label for="staticEmail" class="col-sm-4 col-form-label" style="text-align: right;">Mobile#</label>
											<div class="col-sm-6">
											<input type="text" readonly class="form-control-plaintext" id="staticEmail" value="<?=auth()->user()->mobile;?>">
											
											</div>
									</div>
									<div class="form-group row" style="margin-bottom:0px">
										<label for="staticEmail" class="col-sm-4 col-form-label" style="text-align:right;">Email#</label>
											<div class="col-sm-6" style="text-align: left;color:green;">
											<input type="text" readonly class="form-control-plaintext" id="staticEmail" value=" <?=auth()->user()->email;?>">
											
											</div>
									</div>
								</div>
									
									<div class="col-sm-12 mt-5" id="getdetail" style="display:none">
										<form  enctype="multipart/form-data" method="post" id="detailUpdate" name="detailUpdate">
										@csrf
											<div class="form-group row">
												<label for="staticEmail" class="col-sm-2 col-form-label">Email</label>
												<div class="col-sm-10">
												<input type="text" class="form-control" id="email" name="email"  >
												</div>
											</div>
											<div class="form-group row">
												<label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
												<div class="col-sm-10">
												<input type="number" class="form-control" id="mobile"  name="mobile" placeholder="Mobile No">
												</div>
											</div>
										</form>
									</div>
                                
                            </div>
                            </div>
                        </div>
                    </div>
					<div class="modal-footer" id="footer1" style="background-color: #eec0c6;background-image: linear-gradient(315deg, #eec0c6 0%, #7ee8fa 74%);">
						<button type="button" class="btn btn-success" onclick="confirmDetails();">Agree</button>
						<button type="button" class="btn btn-secondary" onclick="changeDetails();">Decline</button>
				    </div>
					<div class="modal-footer" id="footer2" style="background-color: #eec0c6;background-image: linear-gradient(315deg, #eec0c6 0%, #7ee8fa 74%); display:none" >
						<button type="button" class="btn btn-success" onclick="confirmDetails();">Submit</button>
						<button type="button" class="btn btn-secondary" onclick="changeDetails();">Close</button>
				    </div>
				</div>
		</div>
    </div>

		<script src="{{asset('easyui/jquery-easyui-1.5/jquery.easyui.min.js')}}"></script>

		@yield('javascript')

		<script>
			$(document).ready(function() {
				get_session();
				checkAccessToken();
				loadMenuTree();
				//alert('All elements');  
				
					var mobileNumber ='<?=auth()->user()->mobile;?>';
						var regex = /^92[1-9]\d{9}$/; // Regex pattern for mobile number with "92" prefix and exactly 10 digits
						//localStorage.setItem("OtpRed",'true');
						//alert(localStorage.getItem("OtpRed"));
						if (regex.test(mobileNumber)) {
						//alert("Mobile number is valid.");
						} else {
						
						if(localStorage.getItem("OtpRed")==null){
							localStorage.setItem("OtpRed",'true');
							window.location.href = "/settings";
						}
						
						}
				
			});

	function ajaxdata(){
			$.ajax({
				type: 'GET',
				
				url: '/sidebardata?token=<?=auth()->user()->group_id; ?>',
				// url: '/getDataajax?token=<?=auth()->user()->group_id; ?>',
				success: function(data) {
					localStorage.setItem('myarray', data);
				},
				error: function(xhr, status, error) {
					// Handle any errors that occurred during the AJAX request
				}
			});
    }
    function loadMenuTree() {
       // $("all-data").html('');
		//alert('Please select');
        const myArrayJson = localStorage.getItem("myarray");
        // Parse the JSON-encoded array into a JavaScript array
        const myArray = JSON.parse(myArrayJson);
        if(!myArray){
            ajaxdata();
            setTimeout(loadMenuTree, 1000);
        }
        // Get the HTML div that will contain the data
        const allDataDiv = document.getElementById("all-data");
        // Loop through the elements in the myArray and create HTML elements to represent them
        var set_module_id = '';
		var sr_no 		  = 1;

			var home_icon = '<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>';
				var sub_menu_start_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-text-indent-right" viewBox="0 0 16 16"><path d="M2 3.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm10.646 2.146a.5.5 0 0 1 .708.708L11.707 8l1.647 1.646a.5.5 0 0 1-.708.708l-2-2a.5.5 0 0 1 0-.708l2-2zM2 6.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg>';
   			$(".itrctest").append('<a href="http://cmstest.bnu.edu.pk/" class="side-menu__item home_btn list-group-item">'+home_icon+'<span style="padding:4px;" class="side-menu__label">Home</span><i style="float:right;line-height:14px; margin-top:7px;" class="angle fa fa-angle-right"></i></a>');


		//const navbar = document.getElementById('navbartest');

		for (let i = 0; i < myArray.length; i++) {

			var main_menu_icon = '<span style="font-size: 24px;"><i class="fa fa-'+myArray[i]['module_icon']+' side-menu__icon mt-1"></i></span>';
		
			//alert(myArray[i]['module_icon']);
			 // $("#newtest").append('<span>'+myArray[i]['gmp_module_id']+'</span>');
						 //'as <i class="bi bi-list"></i>';// '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/></svg>';
             
			 if(set_module_id!=myArray[i]['gmp_module_id']){
				
				var a_start = '<a href="#desktop'+myArray[i]['gmp_module_id']+'"  data-parent="#accordion" data-toggle="collapse" class="side-menu__item home_btn list-group-item">'+myArray[i]['gmp_page_name']+'</a>';
				$(".itrctest").append('<a href="#desktop'+myArray[i]['gmp_module_id']+'" data-parent="#accordion" data-toggle="collapse" class="side-menu__item home_btn list-group-item">'+main_menu_icon+'<span style="padding:4px;" class="side-menu__label">'+myArray[i]['gmp_module_name']+'</span><i style="float:right;line-height:14px; margin-top:7px;" class="angle fa fa-angle-right"></i></a> <div class="collapse" id="desktop'+myArray[i]['gmp_module_id']+'"><ul id="ul'+myArray[i]['gmp_module_id']+'" class="side-menu list-group-item-text for_ul"><a onclick="openUrl(\''+myArray[i]['gmp_page_url']+'\')" style="cursor: pointer;"><li  class="for_li slide-item">'+sub_menu_start_icon+' '+myArray[i]['gmp_page_name']+'</li></a></ul></div>');
				set_module_id = myArray[i]['gmp_module_id'];
			}else{
				$("#ul"+myArray[i]['gmp_module_id']).append('<a onclick="openUrl(\''+myArray[i]['gmp_page_url']+'\')" style="cursor: pointer;"><li class="for_li slide-item">'+sub_menu_start_icon+' '+myArray[i]['gmp_page_name']+'</a></li>');
			}
			sr_no++;
        }
  		
    }

			function openUrl(url) {
				var base_url = window.location.origin;
				var full_url = base_url+"/"+url;
				window.location.href = full_url;
			}

			function check_validation(validation_array){
					var count_errors = 0;
					$.each(validation_array, function( index, value ) {
						//alert(value);
						if ($('#'+value).val()==="" || $('#'+value).val()===null || $('#'+value).tokenfield('getTokensList')==="" || $('#'+value).tokenfield('getTokensList')===null){  
							$('#'+value).removeClass('is-valid').addClass('is-invalid');
							count_errors++;
						}
						else{
							$('#'+value).removeClass('is-invalid').addClass('is-valid');
							setTimeout(function(){
							$('#'+value).removeClass('is-valid');
							},2000);
						} 
					});
					if(count_errors>0){
					return 0;
					}else{
					return 1;
					}
	}

	
			function confirmDetails11(){
				var bi_form = document.querySelector('#detailUpdate');
                 var fd      = new FormData(bi_form);


				$.ajax({
					method: "POST",
					url: "{{url('api/update-details')}}",
                    data: fd,
                    processData: false,
                    contentType: false,
					headers: {
						Authorization: 'Bearer <?= auth()->user()->api_token ?>',
						Accept: 'application/json'
					}
				}).done(function(res) {
				
				}).fail(function(res) {
					//console.log(res);
				});
			}
		
			function checkAccessToken() {
				var token='<?=auth()->user()->access_token;?>';
				if(token!=''){
					$.ajax({
						method: "GET",
						url: '<?= url('api/faculty/access-token') ?>',
						headers: {
							Authorization: 'Bearer <?= auth()->user()->api_token ?>',
							Accept: 'application/json'
						}
						
					}).done(function(res) {
				
						console.log(res);
						
					}).fail(function(res) {
						console.log(res);
					});

				}

			}
			function get_session() {
				var html = '';
				$.ajax({
					method: "get",
					url: '<?= url('api/get-sessions') ?>',
					headers: {
						Authorization: 'Bearer <?= auth()->user()->api_token ?>',
						Accept: 'application/json'
					}
				}).done(function(res) {
					if (res.length > 0) {
						$(res).each(function(index, value) {
							if (index < 12) {
								if (value.sess_id == '<?= auth()->user()->profile_settings->active_session ?>') {
									html += '<div class="dropdown-item" ><input type="radio" name="activeSessionCog" class="activeSessionCog" value="' + value.sess_id + '-' + value.sess_name + '" onclick="selectActiveSession(this.value)" checked/>&nbsp;&nbsp;&nbsp;&nbsp;' + value.sess_name + '( <span style="color:green ">Courses: </span> ' + value.total + ')' + '</div>';

									$('.nav-span').html(value.sess_name + '<i class="fa fa-angle-down ml-1 fs-18"></i>');
								} else {
									html += '<div class="dropdown-item"><input type="radio" name="activeSessionCog" class="activeSessionCog" value="' + value.sess_id + '-' + value.sess_name + '" onclick="selectActiveSession(this.value)"  >&nbsp;&nbsp;&nbsp;&nbsp;' + value.sess_name + '( <span style="color:steelblue ">Courses: </span> ' + value.total + ')' + '</input></div>';

								}
							}

						});
					} else {
						//console.log('dd');
						html += '<div class="dropdown-item">&nbsp;&nbsp;&nbsp;&nbsp; <span style="color:steelblue ">No Course Assigned in any Session </span></div>';

					}
					$('#db-session').html(html);
				}).fail(function(res) {

					//console.log(res);
				});
			}
			$(".activeSessionCog").change(function() {
				$(".activeSessionCog").prop('checked', false);
				$(this).prop('checked', true);
			});
			function selectActiveSession(sel_sess) {
				$.ajax({
					type: 'POST',
					url: '<?=url('set_sesion')?>',
					data: {
						'_token': '{{csrf_token()}}',
						'sel_active_session': sel_sess
					},
					success: function(data) {
						if (data == 1) {
							window.location.replace(url);
							//location.reload();

						}
					}
				});
			}
			function hello() {
				sendGetRequest('.hello_btn', '.message_div', 'hello', function(res) {
					console.log('kk', res);
				});

				sendGetRequest('.hello_btn', '.message_div', 'user', function(res) {
					console.log('kk', res);
				});
			}
			

	<?php /************				WELCOME MESSAGE WORK			***************/ ?>
	$(document).ready(function() {

	//	$('#welcome-modal').modal({backdrop: 'static', keyboard: false});  
	<?php 
	$activeNote = 'new-cms23';
	$welcomeNotes = (array)	json_decode(auth()->user()->welcome_notes);
	if ((!count($welcomeNotes) > 0   && in_array($activeNote, $welcomeNotes))) {
	?>
		$('#welcome-modal').modal('show');
	<?php
	}
	?>	
	});		
	function confirmWelcomeNote() {
		//$('#welcome-modal').modal('hide');
		var formData = new FormData();
		formData.append("note_key", "<?=$activeNote?>");
		
		sendPostRequest('', '', 'welcome/save-welcome-note-confirmation', function(res) {

		}, formData, null);
	}
	<?php /************				WELCOME MESSAGE WORK END 			***************/ ?>


				// 		$.showConfirm({
				//     title: "Please confirm", body: "Do you like cats?", textTrue: "Yes", textFalse: "No",
				//     onSubmit: function (result) { // callback on confirm
				//         if (result) {
				//             $.showAlert({title: "Result: " + result, body: "You like cats."})
				//         } else {
				//             $.showAlert({title: "Result: " + result, body: "You don't like cats."})
				//         }
				//     },
				//     onDispose: function() {
				//         console.log("The confirm dialog vanished")
				//     }
				// })

				//  $.showModal({title: "Hello World!", body: "A very simple modal dialog without buttons."});
				// 	$.showAlert({title: "Hi", body: "Please press ok, if you like or dislike cookies."});
				<?php if (isset($_GET['model'])) { ?>
					$.remoteModal({
						url: "<?= url('/demo-modal') ?>"
					});
				<?php } ?>

		
			function show_Assignment(coID, mtt_ID) {
				console.log('Yes yes');

				$.uploadAssignmentModal({});
			}
		</script>

		<!-- 
<a href="javascript:void(0);" onclick="show_Assignment(0);">assignment demo</a> -->
	</body>

<?php } else { ?>

	<body>
		<div> Please Login First ! </div>
	</body>
<?php } ?>

</html>