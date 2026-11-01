<!DOCTYPE html>
<html lang="en">
<?php
// echo Illuminate\Support\Facades\Session::get('user_full_name'); exit;
 // echo Session::get('enrollment_no'); exit;
?>
<head>
    <title> @yield('keywords', 'Campus Management System | BNU' ) </title>
    <!-- HTML5 Shim and Respond.js IE9 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
      <!-- Meta -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
      <meta name="description" content="@yield('author', 'BNU')">
      <meta name="keywords" content="@yield('keywords', 'Campus Management System | BNU' )">
      <meta name="author" content="@yield('author', 'BNU')">
      <!-- CSRF Token -->
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <!-- Favicon icon -->
      <link rel="icon" href="{{asset('assets/icon/favicon.ico')}}" type="image/x-icon">
      <!-- Google font-->
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
      <!-- Required Fremwork -->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/bower_components/bootstrap/css/bootstrap.min.css')}}">
      <!-- themify icon -->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/icon/themify-icons/themify-icons.css')}}">
      <!-- ico font -->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/icon/icofont/css/icofont.css')}}">
      <!-- flag icon framework css -->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/pages/flag-icon/flag-icon.min.css')}}">
      <!-- Style.css -->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">
      <!--color css-->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('assets/pages/data-table/css/buttons.dataTables.min.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('assets/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}">

    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/icon/font-awesome/css/font-awesome.min.css')}}">
 <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/pages/data-table/css/buttons.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}">

      <link rel="stylesheet" type="text/css" href="{{asset('assets/css/simple-line-icons.css')}}">
    <script>
        var loading     = '<p style="text-align: center; line-height:60px; font-weight:bold">Loading...</p>';
        var no_record   = '<p style="text-align: center; line-height:60px; font-weight:bold">No records found!</p>';
        var message     = '<p style="text-align: center; line-height:60px; font-weight:bold">message</p>';
        var url         = '{{ url('/') }}';
        function loadDefaultImage(ele){
            ele.src = '{{config('cms.noimage')}}';
        }
    </script>

  </head>

  <body>

    <!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div></div>
        </div>
    </div>
    <!-- Pre-loader end -->
    <!-- Menu header start -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">


        <!-- topbar start -->
        @include('theme.topbar')
           <!-- topbar end -->







            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">





                    <!-- sidebar start -->
                    @include('theme.sidebar')
                    <!-- sidebar end -->



                    <div class="pcoded-content">
                        <div class="pcoded-inner-content"style="padding: 0px !important;">

                            <div class="main-body">
                                <div class="page-wrapper" style="padding: 10px !important;">


                                   <!-- content start -->
                                   @yield('content')

                                   @yield('mainBody')
                                    <!-- content end -->


                                </div>
                            </div>

                            <div id="styleSelector"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Older IE warning message -->
    <!--[if lt IE 9]>
<div class="ie-warning">
    <h1>Warning!!</h1>
    <p>You are using an outdated version of Internet Explorer, please upgrade <br/>to any of the following web browsers to access this website.</p>
    <div class="iew-container">
        <ul class="iew-download">
            <li>
                <a href="http://www.google.com/chrome/">
                    <img src="assets/images/browser/chrome.png" alt="Chrome">
                    <div>Chrome</div>
                </a>
            </li>
            <li>
                <a href="https://www.mozilla.org/en-US/firefox/new/">
                    <img src="assets/images/browser/firefox.png" alt="Firefox">
                    <div>Firefox</div>
                </a>
            </li>
            <li>
                <a href="http://www.opera.com">
                    <img src="assets/images/browser/opera.png" alt="Opera">
                    <div>Opera</div>
                </a>
            </li>
            <li>
                <a href="https://www.apple.com/safari/">
                    <img src="assets/images/browser/safari.png" alt="Safari">
                    <div>Safari</div>
                </a>
            </li>
            <li>
                <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                    <img src="assets/images/browser/ie.png" alt="">
                    <div>IE (9 & above)</div>
                </a>
            </li>
        </ul>
    </div>
    <p>Sorry for the inconvenience!</p>
</div>
<![endif]-->
<!-- Warning Section Ends -->
<!-- Required Jqurey -->

<script type="text/javascript" src="{{asset('assets/bower_components/jquery/js/jquery.min.js')}}"></script>
<script src="{{asset('assets/bower_components/jquery-ui/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/bower_components/popper.js/js/popper.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/bower_components/bootstrap/js/bootstrap.min.js')}}"></script>
<!-- jquery slimscroll js -->
<script type="text/javascript" src="{{asset('assets/bower_components/jquery-slimscroll/js/jquery.slimscroll.js')}}"></script>
<!-- Horizontal-Timeline js -->
<script type="text/javascript" src="{{asset('assets/pages/dashboard/horizontal-timeline/js/main.js')}}"></script>
<!-- Custom js -->
<script type="text/javascript" src="{{asset('assets/pages/dashboard/custom-dashboard.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/js/script.js')}}"></script>
<!-- Datatable -->
<script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>


<!-- pcmenu js -->
<script src="{{asset('assets/js/pcoded.min.js')}}"></script>
<script src="{{asset('assets/js/demo-12.js')}}"></script>
<script src="{{asset('assets/js/jquery.mCustomScrollbar.concat.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.mousewheel.min.js')}}"></script>
<script src="assets/js/menu/menu-header-fixed.js"></script>

    <!-- easy ui -->
    <script type="text/javascript" src="{{asset('assets/easyui/jquery.easyui.min.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/easyui/easyui.css')}}">
    <!-- easy ui end -->

</body>

<script type="text/javascript">
var nav_postion='collapsed_';
function sidebar_toggle_custom(){
    var canSee = $(".pcoded-navbar").is(":visible");
   // alert(canSee);
}
    function sendGetRequest(endPoint,successBlock) {
        $.ajax({
            method: "GET",
            url:   endPoint,// url+'/api/'+endPoint,
            headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
                Accept: 'application/json'
            }
        }).done(successBlock).fail(function( res ) {
           // console.log(res);
        });
    }


    function sendPostRequest(endPoint,successBlock,postData) {
        $.ajax({
            method: "POST",
            url:    url+'/api/'+endPoint,
            data : postData,
            processData: false,
            contentType: false,
            headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
                Accept: 'application/json'
            }
        }).done(successBlock).fail(function( res ) {
            console.log(res);
        });
    }

    $(document).ready(function(){
        $('#styleSelector').hide();

        
    getNotification();
   
        
        sendGetRequest(endPoint+'/u-notification/get',function(res){

            $(res).each(function(index,obj){
            //$('#hot-notification-modal').modal('show');
           console.log(obj.viewed_notification);
           if(obj.viewed_notification !=0){
           // $('#hot-notification-modal').modal('show');

           }else{
            $('#hot-notification-modal').modal('hide');
            <?php 
            if((!session('modal_show'))){
                session(['modal_show' => 1]);
            ?>
            //$('#hot-notification-modal').modal('show');
        <?php } ?>
           }
        });
           });


    });




</script>
<script src="{{asset('assets/js/remote_call.js')}}"></script>



@yield('javascript')
<style>
#styleSelector{display:none;}
</style>
</html>
