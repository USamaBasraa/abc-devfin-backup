      <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
      <!-- Required Fremwork -->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/bower_components/bootstrap/css/bootstrap.min.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('assets/pages/dashboard/horizontal-timeline/css/style.css')}}">
      {{-- <!-- themify icon -->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/icon/themify-icons/themify-icons.css')}}">
      <!-- ico font -->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/icon/icofont/css/icofont.css')}}">
      <!-- flag icon framework css -->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/pages/flag-icon/flag-icon.min.css')}}">
      <!-- Menu-Search css -->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/pages/menu-search/css/component.css')}}">
      <!-- Horizontal-Timeline css -->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/pages/dashboard/horizontal-timeline/css/style.css')}}">
      <!-- amchart css -->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/pages/dashboard/amchart/css/amchart.css')}}">
      <!-- flag icon framework css -->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/pages/flag-icon/flag-icon.min.css')}}">
      <!-- Style.css -->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">
      <!--color css-->
      <link rel="stylesheet" type="text/css" href="{{asset('assets/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('assets/pages/data-table/css/buttons.dataTables.min.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('assets/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}">



      <link rel="stylesheet" type="text/css" href="{{asset('assets/css/linearicons.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('assets/css/simple-line-icons.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('assets/css/ionicons.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('assets/css/jquery.mCustomScrollbar.css')}}">

    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/icon/font-awesome/css/font-awesome.min.css')}}">
 <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/pages/data-table/css/buttons.dataTables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}">

      <link rel="stylesheet" type="text/css" href="{{asset('assets/css/linearicons.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('assets/css/simple-line-icons.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('assets/css/ionicons.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('assets/css/jquery.mCustomScrollbar.css')}}"> --}}
    {{-- <script>
        var loading     = '<p style="text-align: center; line-height:60px; font-weight:bold">Loading...</p>';
        var no_record   = '<p style="text-align: center; line-height:60px; font-weight:bold">No records found!</p>';
        var message     = '<p style="text-align: center; line-height:60px; font-weight:bold">message</p>';
        var url         = '{{ url('/') }}';
        function loadDefaultImage(ele){
            ele.src = '{{config('cms.noimage')}}';
        }
    </script> --}}

<script type="text/javascript" src="{{asset('assets/bower_components/jquery/js/jquery.min.js')}}"></script>
{{-- <script src="{{asset('assets/bower_components/jquery-ui/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/bower_components/popper.js/js/popper.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/bower_components/bootstrap/js/bootstrap.min.js')}}"></script>
<!-- jquery slimscroll js -->
<script type="text/javascript" src="{{asset('assets/bower_components/jquery-slimscroll/js/jquery.slimscroll.js')}}"></script>
<!-- modernizr js -->
<script type="text/javascript" src="{{asset('assets/bower_components/modernizr/js/modernizr.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/bower_components/modernizr/js/css-scrollbars.js')}}"></script>
<!-- classie js -->
<script type="text/javascript" src="{{asset('assets/bower_components/classie/js/classie.js')}}"></script>
<!-- Rickshow Chart js -->
<script src="{{asset('assets/bower_components/d3/js/d3.js')}}" ></script>
<script src="{{asset('assets/bower_components/rickshaw/js/rickshaw.js')}}"></script>
<!-- Morris Chart js -->
<script src="{{asset('assets/bower_components/raphael/js/raphael.min.js')}}"></script>
<script src="{{asset('assets/bower_components/morris.js/js/morris.js')}}"></script>
<!-- Horizontal-Timeline js -->
<script type="text/javascript" src="{{asset('assets/pages/dashboard/horizontal-timeline/js/main.js')}}"></script>
<!-- amchart js -->
<script type="text/javascript" src="{{asset('assets/pages/dashboard/amchart/js/amcharts.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/pages/dashboard/amchart/js/serial.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/pages/dashboard/amchart/js/light.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/pages/dashboard/amchart/js/custom-amchart.js')}}"></script>
<!-- i18next.min.js -->
<script type="text/javascript" src="{{asset('assets/bower_components/i18next/js/i18next.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/bower_components/i18next-xhr-backend/js/i18nextXHRBackend.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/bower_components/i18next-browser-languagedetector/js/i18nextBrowserLanguageDetector.min.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/bower_components/jquery-i18next/js/jquery-i18next.min.js')}}"></script>
<!-- Custom js -->
<script type="text/javascript" src="{{asset('assets/pages/dashboard/custom-dashboard.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/js/script.js')}}"></script>
<!-- Datatable -->
<script src="{{asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('assets/pages/data-table/js/jszip.min.js')}}"></script>
<script src="{{asset('assets/pages/data-table/js/pdfmake.min.js')}}"></script>
<script src="{{asset('assets/pages/data-table/js/vfs_fonts.js')}}"></script>
<script src="{{asset('assets/bower_components/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('assets/bower_components/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('assets/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('assets/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js')}}"></script>


<!-- pcmenu js -->
<script src="{{asset('assets/js/pcoded.min.js')}}"></script>
<script src="{{asset('assets/js/demo-12.js')}}"></script>
<script src="{{asset('assets/js/jquery.mCustomScrollbar.concat.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.mousewheel.min.js')}}"></script> --}}

  </head>



<style type="text/css">
    .grid_center{
        text-align: center;
    }
    .row_div{
        padding: 4px;
    }
    .bnu_headera{
        background-color: #2F3B4D;
        color: white;
        height: 40px;
        padding-top: 6px;
        padding-left: 6px;
    }
    .div_row{
        float: left;
        padding: 6px;
        width: 100%;
    }
    .page_container{
        border: solid 1px #2F3B4D;
        margin-left: auto;
        margin-right: auto;
        width:100%;
    }
    .combo_box_div{
        width: 60%;
        margin-left: auto;
        margin-right: auto;
        border: solid 0px;
        text-align: center;
    }
    .combo_box{
        width: 100%;
        float: left;
    }
    .combo_label_div{
        float: left;
        padding-right: 4px;
        padding-left: 4px;
    }
    .page_container{
        border: none;!important;
    }
    .dataTables_scrollBody{
        border-bottom : none;!important;
    }
</style>
<div class="modal" tabindex="-1" role="dialog"  id="attendance_submit_summary">
    <div class="modal-dialog" role="document" style="width:250px; margin-top:150px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attendance Summary</h5>
            </div>
            <div class="modal-body" id="attendance_submit_summary_txt" style="text-align: center;!important;">
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary btn-lg" id="close_button_id" >Close</button>
            </div>
        </div>
    </div>
</div>
<div class="page-header">
        <div class="card-header" style="background-color:#2F3B4D !important; color:#639; width:100%;margin-top: 2px;">
            <h5 style="font-family:'Times New Roman', Times, serif; color:white;">{{$course_name}}<div style="float:right;"></div></h5>
        </div>
</div>
<?php error_reporting(0);  ?>
<form id="attendance_from" method="post" action="#" >
    <input type="hidden" name="slot_co_id" id="slot_co_id" value="<?=$class_date[0]->mtt_co_id;?>">
    <input type="hidden" name="ip_allowed" id="ip_allowed" value="<?=$slot_ip_yes;?>">
    <input type="hidden" name="slot_id_now" id="slot_id_now" value="<?=$slot_id_now;?>">
    <div class="page_container">
        {{csrf_field()}}
        <div class="div_row">
            <div class="combo_box_div">
                <select id="slot_date_id"  name="slot_date_id"  class="form-control">
                    <option>--Select Attendance Slot --</option>
                    @foreach($class_date as $rec_master_timetable)
                        @if($rec_master_timetable->today_class_status=='YES')
                            @php
                                $show_room_name = $rec_master_timetable->room_name;
                                $slot_selected= 'selected="selected"';
                                $current_time_slot_id = $rec_master_timetable->tt_id;
                            @endphp
                        @else
                            @php
                                $slot_selected= '';
                            @endphp
                        @endif
                        <option value="{{$rec_master_timetable->tt_id."~".$rec_master_timetable->today_class_status}}" {{$slot_selected}}>{{date('d-M-Y',strtotime($rec_master_timetable->tt_class_date))}} {{$rec_master_timetable->start_time." To ".$rec_master_timetable->end_time}}</option>
                    @endforeach
                </select>

            </div>
        </div>
        <div class="div_row">
            <div style="float:left; width:28%; border: solid 0px; text-align: left;  ">
                Presents : <label id="slot_presents">&nbsp;</label> Absents : <label id="slot_absents">&nbsp;</label>
            </div>
            <div id="id_course_attendance_summary" style="float:right; margin-right:28px; width:68%; border: solid 0px; text-align: right;  ">
                Total Classes :    <label id="lbl_total_classes">&nbsp;</label>
                Delivered :        <label id="lbl_delivered_classes">&nbsp;</label>
                Remaining:         <label id="lbl_remaining_classes">&nbsp;</label>
                Allowed Absents:     <label id="lbl_absents_classes">&nbsp;</label>
            </div>
        </div>
        @php
            $show_ip_message = 'none';
            $show_data_div = '';
            $show_no_class_schedule='';
            $final_submit_preview='none';
        @endphp
        @if($slot_ip_yes=="NO")
            @php
                $show_ip_message = '';
                $show_data_div = 'none';
                $show_no_class_schedule='none';

            @endphp
        @elseif($slot_ip_yes=="YES")
            @php
                $show_ip_message = 'none';
                $show_data_div = '';
                $show_no_class_schedule='none';
                $final_submit_preview='';
            @endphp
        @else
            @php
                $show_ip_message = 'none';
                $show_data_div = 'none';
                $show_no_class_schedule = '';
            @endphp
        @endif
        <div class="div_row" id="ip_div" style="display: <?=$show_ip_message;?>">
            <div class="alert alert-danger" role="alert" style="text-align: center; !important;">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                Attendance in this room is not allowed. Please go to Room
                <label style="color: green; font-size: 14px; font-weight: bold;">
                    {{@$show_room_name}}
                </label>
                {{--There is no class scheduled in this time / slot.--}}
            </div>
        </div>
        <div class="div_row" id="show_no_class_schedule" style="display:<?=$show_no_class_schedule;?>;">
            <div class="alert alert-warning" role="alert" style="text-align: center; !important;">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                There is no class scheduled in this time / slot.
            </div>
        </div>
        <div class="div_row" id="attendance_data_id" style="display:<?=$show_data_div;?>"> </div>
        <div class="div_row"  >

            <div id="attendance_save" style="display: none; font-size: 14px; width: 50%; float: left;"> </div>
            <div style="float: right; width: 40%;">
                <button type="button" class="btn btn-primary btn-lg" style="margin-top:2px; float: right;" id="final_submit_att">Save Attendance </button>
            </div>


        </div>


        <div style="clear:both"></div>
    </div> <div id="mykam">&nbsp;</div>
</form>
<script type="text/javascript">
    function load_presents_absents(get_tt_id) {
        $.ajax({
            type:'GET',
            url:'{{ URL::route("attendance_slot_summary") }}/'+get_tt_id,
            success:function(data){
                $("#slot_presents").html(data[0].total_presents);
                $("#slot_absents").html(data[0].total_absents);
                var total_prsents = parseInt(data[0].total_presents)+parseInt(data[0].total_absents);
                if(total_prsents>0){
                    $("#final_submit_att").hide();
                    $( "#id_course_attendance_summary" ).css( "float", "right" );
                }else{
                    // $("#final_submit").show();
                }
            }
        });
    };
    function get_slot_details(tt_id,co_id) {
        // var function_date;
        $.ajax({
            url:"{{URL::route('loadClassDates') }}/"+tt_id+"/"+co_id,
            type: "GET",
            success:function(data) {
                if(data.trim()=="IP_NO"){
                    //  $("#ip_div").show();
                    //  $("#final_submit").hide();
                    //  $("#attendance_data_id").hide();
                }
                //alert(data);
                //$("#mykam").html(data);
                // return data;
                //var function_date = data;
            }
        });
        //return function_date;
    };
    function load_course_attendance_summary(courses) {
        $.ajax({
            url:"{{ URL::route('course_attendance_summary') }}/"+courses,
            type: "GET",
            dataType: "json",
            success:function(data) {
                $("#lbl_total_classes").html(data[0].co_total_classes);
                $("#lbl_delivered_classes").html(data[0].delived_classes);
                $("#lbl_remaining_classes").html(parseInt(data[0].co_total_classes)-parseInt(data[0].delived_classes));
                $("#lbl_absents_classes").html(data[0].allowed_absents);
            }
        });
    };
    $(document).ready(function() {
        $('#close_button_id').click(function (){
            $('#attendance_submit_summary').modal('hide');
        });
            <?php
            if($current_time_slot_id>0){
            ?>
        var co_id = $("#slot_co_id").val();
        <?php  if($slot_ip_yes=='NO'){ ?>
            $("#final_submit_att").hide();
        <?php   }else{  ?>
            load_grid('<?php echo $current_time_slot_id;?>',co_id,'YES');
        <?php    } ?>

        load_presents_absents('<?php echo $current_time_slot_id;?>');
        <?php
         }
        ?>
        load_course_attendance_summary('<?=$class_date[0]->mtt_co_id;?>');
        $("#slot_date_id").change(function(){
            $("#attendance_save").html('');
            var result=$(this).val().split('~');
            var get_tt_id           = result[0];
            var get_tt_class_status = result[1];

            var ip_allowed = $("#ip_allowed").val();
            var slot_id_now = $("#slot_id_now").val();

            var co_id = $("#slot_co_id").val();

            var slot_all_date = get_slot_details(co_id,get_tt_id);

            //  alert(slot_all_date);

            // if(get_tt_id==slot_id_now && ip_allowed=="NO"){
			 if(get_tt_class_status=="YES" && ip_allowed=="NO"){
                $("#ip_div").show();
                $("#final_submit_att").hide();
                $("#attendance_data_id").hide();
            }else {
                $("#show_no_class_schedule").hide();
                $("#attendance_data_id").show();
                $("#ip_div").hide();
                $("#show_no_class_schedule").hide();


                if (get_tt_class_status == "YES") {
                    $("#final_submit_att").show();
                } else {
                    $("#final_submit_att").hide();
                }
                $("#final_submit_att").show();

                load_course_attendance_summary(co_id);
                load_grid(get_tt_id, co_id, get_tt_class_status);
                load_presents_absents(get_tt_id);
                if (get_tt_class_status == "NO") {
                    $("#final_submit_att").hide();
                } else {
                    $("#final_submit_att").show();
                }
            }

        });
        $('#final_submit_att').click(function () {
            var get_tt_id = $('#slot_date_id').val();
            var co_id = $("#slot_co_id").val();
            $('#attendance_submit_summary').modal('hide');

            $.ajax({
                type:'POST',
                url:'{{ URL::route('attendance/attendance_post') }}',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: $("#attendance_from").serializeArray(),
                success:function(data){
                    $("#attendance_save").show();
                    load_grid(get_tt_id,co_id);
                    if(data){
                        $("#attendance_save").css("color", "green");
                        $("#attendance_save").html('Data Saved Successfully');
                        $("#final_submit_att").hide();
                        load_grid(get_tt_id,co_id);
                        load_course_attendance_summary('<?=$class_date[0]->mtt_co_id;?>');
                    }else{
                        $("#attendance_save").css("color", "red");
                        $("#attendance_save").html('Attendance not saved please try again');

                    }


                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#attendance_save").css("color", "red");
                    $("#attendance_save").html('Attendance not saved please try again');
                }
            });
        });
        //-----------------------  LOAD GRID FUNCTION --------------------------------------
        function load_grid(get_tt_id,co_id,get_tt_class_status) {
            load_presents_absents(get_tt_id);
            $.ajax({
                type:'GET',
                url:'{{ URL::route('attendance/AttendanceLoad') }}/'+get_tt_id+'/'+co_id+'/'+get_tt_class_status,
                success:function(data){

                    $("#attendance_data_id").html(data);
                    // $("#primary").html(data).fadeIn('slow');
                }
            });
        };

    });
</script>
