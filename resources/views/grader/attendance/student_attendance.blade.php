<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
?>
@extends('layouts.app',['pageTitle' => 'Dashboard'])
@section('content')



<div class="page-body">
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700|Open+Sans:300,400,600,700,800" rel="stylesheet">

<script>
	var url 	= '<?=url('/')?>';
	var token 	= '<?=auth()->user()->api_token?>';
</script>

<?php
 $date = date('M d,  Y');


?>

<script>
// Set the date we're counting down to
var cdate_time ="<?=$date?> <?=$ts_time_stamp?>";
//alert(cdate_time);
var countDownDate = new Date(cdate_time).getTime();

// Update the count down every 1 second
var x = setInterval(function() {

  // Get today's date and time
  var now = new Date().getTime();

  // Find the distance between now and the count down date
  var distance = countDownDate - now;

  // Time calculations for days, hours, minutes and seconds
  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

  // Output the result in an element with id="demo"
//  document.getElementById("demo").innerHTML = days + "d " + hours + "h "
  document.getElementById("demo").innerHTML = "Time Left : "+ hours + "H "
  + minutes + "M " + seconds + "S ";

  // If the count down is over, write some text
  if (distance < 0) {
    clearInterval(x);
    document.getElementById("demo").innerHTML = "Lecture Finished";
  }
}, 1000);
</script>





        <!-- Bootstrap css -->
        <link href="{{ asset('plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet" />
        <link href="{{ asset('plugins/p-scrollbar/p-scrollbar.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
		<script src="{{ asset('js/vendors/jquery-3.5.1.min.js') }}"></script>
		<script src="{{ asset('plugins/bootstrap/popper.min.js') }}"></script>
		<!-- <script src="{{ asset('plugins/bootstrap/js/bootstrap.min.js') }}"></script>  -->



        <!-- Circle-progress js-->
        <script src="{{ asset('js/vendors/circle-progress.min.js') }}"></script>

        <!-- Jquery-rating js-->
        <script src="{{ asset('plugins/rating/jquery.rating-stars.js') }}"></script>




        <!--Select2 js -->
        <script src="{{ asset('plugins/select2/select2.full.min.js') }}"></script>
		<script src="{{ asset('js/select2.js') }}"></script>
<!--
        <script src="{{ asset('js/custom.js') }}"></script>
 -->

 <script src="{{ asset('js/cms/http.js') }}"></script>
<script src="{{ asset('plugins/select2/select2.full.min.js') }}"></script>




<script>
   var loading     = '<p style="text-align: center; line-height:60px; font-weight:bold">Loading...</p>';
   var no_record   = '<p style="text-align: center; line-height:60px; font-weight:bold">No records found!</p>';
   var message     = '<p style="text-align: center; line-height:60px; font-weight:bold">message</p>';
   var url         = '{{ url('/') }}';
   function loadDefaultImage(ele){
       ele.src = '{{config('cms.noimage')}}';
   }
</script>


<style type="text/css">
    /* ==========================================================
       FIGMA: Class Attendance (node 2766:2320)
       IMPORTANT: visual layer only. Existing Blade/PHP/JS logic,
       IDs, routes, AJAX calls and attendance flow remain unchanged.
       ========================================================== */

    .page-body {
        font-family: 'Montserrat', 'Open Sans', sans-serif;
    }

    /* Existing functional wrapper, visually adapted to Figma */
    .timetable_layout {
        width: 100%;
        background: rgba(15, 118, 110, 0.05);
        border-radius: 10px !important;
        padding: 20px 28px 48px !important;
        box-sizing: border-box;
    }

    .timetable_layout > .row:first-child {
        margin: 0 auto 28px !important;
        width: 100% !important;
    }

    .timetable_layout > .row:first-child > .default-grid-item {
        padding: 0 !important;
    }

    /* Course header */
    .page-header.course_title {
        width: 100%;
        margin: 0 !important;
        padding: 0 !important;
        background: transparent !important;
        color: #334155 !important;
        border: 0 !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        text-align: left !important;
        font-family: 'Montserrat', 'Open Sans', sans-serif !important;
    }

    .page-header.course_title > .card-header.course_title {
        flex: 1 1 320px;
        min-width: 240px;
        padding: 0 !important;
        margin: 0 !important;
        border: 0 !important;
        background: transparent !important;
        color: #334155 !important;
        text-align: left !important;
        font-family: 'Montserrat', 'Open Sans', sans-serif !important;
    }

    .page-header.course_title > .card-header.course_title h5 {
        margin: 0 !important;
        color: #334155 !important;
        font-family: 'Montserrat', 'Open Sans', sans-serif !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        line-height: 1.35;
    }

    /* Preserve existing select ID and options; only restyle */
    .page-header.course_title > .col-3,
    .page-header.course_title > .col-sm-3,
    .page-header.course_title > .col-xl-3 {
        flex: 0 1 390px;
        max-width: 390px !important;
        width: 390px !important;
        padding: 0 !important;
    }

    #slot_date_id {
        width: 100% !important;
        height: 40px !important;
        padding: 8px 36px 8px 14px !important;
        border: 1px solid transparent !important;
        border-radius: 8px !important;
        background-color: #ffffff !important;
        color: #64748b !important;
        font-family: 'Montserrat', 'Open Sans', sans-serif !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        line-height: 1.2;
        box-shadow: none !important;
        outline: none !important;
        cursor: pointer;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    #slot_date_id:hover,
    #slot_date_id:focus {
        border-color: rgba(23, 103, 95, .35) !important;
        box-shadow: 0 0 0 3px rgba(23, 103, 95, .08) !important;
    }

    /* Existing countdown target remains #demo */
    #demo {
        flex: 0 0 auto;
        width: auto !important;
        min-width: 110px;
        height: 40px !important;
        margin: 0 !important;
        padding: 0 16px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        border: 0 !important;
        border-radius: 8px !important;
        background: #17675f !important;
        color: #ffffff !important;
        font-family: 'Montserrat', 'Open Sans', sans-serif !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        text-transform: uppercase;
        white-space: nowrap;
        box-shadow: none !important;
    }

    /* Attendance card */
    .timetable_layout > .row:not(:first-child) {
        width: 100% !important;
        margin: 0 auto !important;
    }

    .timetable_layout > .row:not(:first-child) > .card {
        width: 100% !important;
        margin: 0 auto !important;
        background: #ffffff !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 20px !important;
        box-shadow: 0 10px 32px rgba(0, 0, 0, 0.02) !important;
        overflow: hidden;
    }

    .timetable_layout .card-block.table-border-style {
        padding: 0 !important;
        background: #ffffff;
    }

    .attendance-card-toolbar {
        min-height: 78px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        border-bottom: 1px solid #e5e7eb;
        background: #ffffff;
    }

    .attendance-card-toolbar h4 {
        margin: 0 0 4px;
        color: #111827;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.25;
    }

    .attendance-card-toolbar p {
        margin: 0;
        color: #4b5563;
        font-size: 12px;
        font-weight: 400;
        line-height: 1.4;
    }

    .attendance-legend {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 18px;
        flex-wrap: wrap;
    }

    .attendance-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #4b5563;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
    }

    .attendance-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    .attendance-legend-dot.present { background: #10b981; }
    .attendance-legend-dot.absent { background: #ef4444; }
    .attendance-legend-dot.pending { background: #f59e0b; }

    .timetable_layout .table-responsive {
        margin: 0 !important;
        border: 0 !important;
        overflow-x: auto;
        background: #ffffff;
    }

    #student_list {
        width: 100%;
        min-height: 100px;
        text-align: left !important;
        color: #111827;
        font-family: 'Montserrat', 'Open Sans', sans-serif !important;
        background: #ffffff;
    }

    #student_list > img,
    #student_list img.photo {
        display: block;
        margin: 34px auto;
        max-width: 46px;
        height: auto;
    }

    /* Generic styling for the HTML returned by load_studentlist_attendance.
       No returned markup/logic is changed here. */
    #student_list table {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background: #ffffff !important;
        color: #111827;
        font-family: 'Montserrat', 'Open Sans', sans-serif !important;
    }

    #student_list table thead th,
    #student_list table thead td {
        padding: 14px 16px !important;
        border: 0 !important;
        background: #17675f !important;
        color: #ffffff !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        line-height: 1.25;
        text-transform: uppercase;
        vertical-align: middle !important;
        white-space: nowrap;
    }

    #student_list table tbody tr {
        background: #ffffff !important;
        transition: background-color .15s ease;
    }

    #student_list table tbody tr:hover {
        background: #fbfdfd !important;
    }

    #student_list table tbody td {
        padding: 7px 16px !important;
        border-top: 0 !important;
        border-right: 0 !important;
        border-left: 0 !important;
        border-bottom: 1px solid #e5e7eb !important;
        color: #4b5563 !important;
        font-size: 12px !important;
        font-weight: 500;
        vertical-align: middle !important;
    }

    #student_list table tbody tr:last-child td {
        border-bottom: 0 !important;
    }

    /* Figma Class Attendance: left status strip.
       The row class is assigned in students_attendance_list.blade.php
       from the EXISTING attendance/absence/block logic. */
    #student_list table tbody tr > td:first-child {
        border-left-width: 3px !important;
        border-left-style: solid !important;
        border-left-color: #10b981 !important; 
    }

    #student_list table tbody tr.attendance-status-warning > td:first-child {
        border-left-color: #f59e0b !important;
    }

    #student_list table tbody tr.attendance-status-danger > td:first-child {
        border-left-color: #ef4444 !important;
    }

    #student_list table tbody tr.attendance-status-blocked > td:first-child {
        border-left-color: #6b7280 !important;
    }

    #student_list .btn,
    #student_list button,
    #student_list input[type="button"],
    #student_list input[type="submit"] {
        min-height: 34px;
        padding: 7px 14px !important;
        border-radius: 8px !important;
        border-width: 1px !important;
        font-family: 'Montserrat', 'Open Sans', sans-serif !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        box-shadow: none !important;
        transition: all .15s ease;
    }

    #student_list .btn-success,
    #student_list .btn-outline-success.active,
    #student_list .btn-outline-success:hover {
        background: #ecfdf5 !important;
        border-color: #ecfdf5 !important;
        color: #10b981 !important;
    }

    #student_list .btn-danger,
    #student_list .btn-outline-danger.active,
    #student_list .btn-outline-danger:hover {
        background: #fef2f2 !important;
        border-color: #fef2f2 !important;
        color: #ef4444 !important;
    }

    #student_list .btn-secondary,
    #student_list .btn-light,
    #student_list .btn-outline-secondary {
        background: rgba(243, 244, 246, .75) !important;
        border-color: rgba(243, 244, 246, .75) !important;
        color: #4b5563 !important;
    }

    #student_list .btn-primary,
    #student_list .attendance_btn {
        background: #17675f !important;
        border-color: #17675f !important;
        color: #ffffff !important;
    }

    #student_list .form-control,
    #student_list select,
    #student_list input[type="text"],
    #student_list input[type="search"] {
        min-height: 38px;
        border: 1px solid #e5e7eb !important;
        border-radius: 8px !important;
        background: #f9fafb !important;
        color: #334155 !important;
        font-family: 'Montserrat', 'Open Sans', sans-serif !important;
        font-size: 12px !important;
        box-shadow: none !important;
    }

    /* Existing room validation condition remains unchanged */
    .timetable_layout > .alert.alert-danger {
        margin: 0 !important;
        padding: 16px 18px !important;
        border: 1px solid #fecaca !important;
        border-radius: 12px !important;
        background: #fef2f2 !important;
        color: #b91c1c !important;
        font-family: 'Montserrat', 'Open Sans', sans-serif !important;
        font-size: 13px !important;
        font-weight: 600 !important;
    }

    .mobile { display: none; }
    .desktop { display: block; }

    @media only screen and (max-width: 991px) {
        .timetable_layout {
            padding: 18px 18px 36px !important;
        }

        .page-header.course_title {
            align-items: stretch;
        }

        .page-header.course_title > .card-header.course_title {
            flex-basis: 100%;
        }

        .page-header.course_title > .col-3,
        .page-header.course_title > .col-sm-3,
        .page-header.course_title > .col-xl-3 {
            flex: 1 1 360px;
            max-width: none !important;
            width: auto !important;
        }

        #demo {
            flex: 0 0 auto;
        }
    }

    /* Extra small devices (phones, 600px and down) */
    @media only screen and (max-width: 600px) {
        .timetable_layout {
            width: 100%;
            padding: 14px 12px 28px !important;
            border-radius: 8px !important;
        }

        .page-header.course_title {
            gap: 10px;
            text-align: left !important;
        }

        .page-header.course_title > .card-header.course_title h5 {
            font-size: 15px !important;
        }

        .page-header.course_title > .col-3,
        .page-header.course_title > .col-sm-3,
        .page-header.course_title > .col-xl-3 {
            flex: 1 1 100%;
            width: 100% !important;
            max-width: 100% !important;
        }

        #slot_date_id {
            height: 42px !important;
            font-size: 11px !important;
        }

        #demo {
            width: 100% !important;
            height: 38px !important;
        }

        .attendance-card-toolbar {
            padding: 16px;
            align-items: flex-start;
            flex-direction: column;
        }

        .attendance-legend {
            justify-content: flex-start;
            gap: 12px;
        }

        #student_list table {
            min-width: 700px;
        }

        #student_list table thead th,
        #student_list table thead td,
        #student_list table tbody td {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }

        .mobile { display: block !important; }
        .desktop { display: none !important; }
    }
    .main-content {
        padding: 0 !important;
    }
    /* Left status border — ONLY main student row first cell */
#student_list table.table > tbody > tr.attendance-status-safe > td:first-child {
    border-left: 4px solid #10b981 !important;
}

#student_list table.table > tbody > tr.attendance-status-warning > td:first-child {
    border-left: 4px solid #f59e0b !important;
}

#student_list table.table > tbody > tr.attendance-status-danger > td:first-child {
    border-left: 4px solid #ef4444 !important;
}

#student_list table.table > tbody > tr.attendance-status-blocked > td:first-child {
    border-left: 4px solid #9ca3af !important;
}

/* Remove border from nested Absent/Present table */
#student_list table.table > tbody > tr > td table td {
    border-left: 0 !important;
}
#AddNewContentModal1 .modal-header .close {
    font-size: 32px !important;
    line-height: 1 !important;
}



</style>
<?php
$room_and_status = explode("~",$slot_ip_yes);
//echo $room_and_status[1];
//exit();
?>
    <div class="page-body">
<div class="default-grid timetable_layout" style="margin-left: auto; margin-right: auto;  padding:4px; border-radius: 4px;">
    <div class="row" style="border: solid 0px red; padding-bottom: 0px !important; width:100%; margin-left: auto; margin-right: auto;">
        <div class="col-sm-12 default-grid-item"  style="border: solid 0px green; padding: 0px !important;">
            {{-- <div class="card"> --}}
                   <div class="page-header course_title" style="border: solid 0px blue; margin-top: 0px !important;">
                        <div class="card-header course_title">
                            <h5 style="">{{$course_name}}
                                <div style="float:right;">&nbsp;</div>
                            </h5>

                        </div>

                        <div class="col-3 col-sm-3 col-xl-3" style="border:solid 0px yellow;">
            <select id="slot_date_id"  name="slot_date_id"  class="form-control"  style="width:100%; align-items:center;">
                <option>--Select Attendance Slot --</option>
                @foreach($get_slot_list as $rec_master_timetable)
                    @if($rec_master_timetable->today_class_status=='YES')
                        @php
                            $radio_button_show = 1;
                            $show_room_name = $rec_master_timetable->room_name;
                            $slot_selected= 'selected="selected"';
                            $current_time_slot_id = $rec_master_timetable->tt_id;
                        @endphp
                    @else
                        @php
                            $radio_button_show = 0;
                            $slot_selected= '';
                        @endphp
                    @endif
                    <option value="{{$rec_master_timetable->mtt_co_id."~".$rec_master_timetable->tt_id."~".$rec_master_timetable->today_class_status}}" {{$slot_selected}}>{{date('d-M-Y',strtotime($rec_master_timetable->tt_class_date))}} {{$rec_master_timetable->start_time." To ".$rec_master_timetable->end_time}}</option>
                @endforeach
            </select>
        </div>

       <label class="form-control alert" style="width:300px; margin-right:5px; color:red;" id="demo"></label>
                    </div>
        </div>
    </div>
    @if($room_and_status[0]=="YES")
    <div class="row" style=" margin-left: auto; margin-right: auto; width:100%;">
        <div class="card" style="width: 100% !important; margin-left: auto; margin-right: auto; border: solid 0px #101010;">
            <div class="card-block table-border-style">
                <div class="attendance-card-toolbar">
                    <div>
                        <h4>Student Attendance</h4>
                        <p>Mark and review daily attendance logs</p>
                    </div>
                    <div class="attendance-legend" aria-label="Attendance status legend">
                        <span class="attendance-legend-item"><span class="attendance-legend-dot present"></span>Present</span>
                        <span class="attendance-legend-item"><span class="attendance-legend-dot absent"></span>Absent</span>
                        <span class="attendance-legend-item"><span class="attendance-legend-dot pending"></span>Not Marked</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="student_list" style="text-align: center;"><img src="/images/loader.gif"></div>
                </div>
            </div>
        </div>
     </div>
     @else
     <div class="alert alert-danger" role="alert">
     Please go to Room <?=$room_and_status[1];?> for attendance
     </div>

     @endif
</div>
</div>
    </div>
    @stop
@section('javascript')
<script type="text/javascript">

$(document).ready(function() {
         load_attendance_student(<?=$co_id;?>,<?=$slot_id;?>,'YES');

         
         setTimeout(function(){
     localStorage.clear(); 
    },300000);
});
function load_attendance_student(co_id,slot_id,status){

    // alert(co_id+"___"+slot_id+"***"+status);
    // return false;
    //  setInterval(() => {
        $.ajax({
            type:'GET',
            url:'{{ URL::route('load_studentlist_attendance')}}/'+co_id+'/'+slot_id,
            success:function(data){
                $("#student_list").html(data);
                var token = localStorage.getItem("token");            
                if(token ==null){
                    $('#AddNewContentModal1').modal('show');

                }
            }
        });
   // }, 2000);
}
function Acknowledge(){
    var token = localStorage.setItem("token",1);
    $('#AddNewContentModal1').modal('hide');

}
function checkit(co_id,slot_id){
    load_attendance_student(co_id,slot_id);
}

$("#slot_date_id").change(function(){
            var result=$(this).val().split('~');
            var get_co_id           = result[0];
            var get_tt_id           = result[1];
            var get_tt_status       = result[2];
         //   $("#student_list").attr("src", "/images/loader.gif");

         //   alert(result);
            var image = document.createElement("IMG");
            image.alt = "Alt information for image";
            image.setAttribute('class', 'photo');
            image.src="/images/loader.gif";
            $("#student_list").html(image);

           // setInterval(() => {
                load_attendance_student(get_co_id,get_tt_id,get_tt_status);
          //  }, 2000);

       //     alert(get_tt_id);
         // if((get_co_id>0 && get_tt_id>0){

         // }

})
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
        function update_attendance(attendance_id,status){
          //  alert('ok');
            $.ajax({
                type:'GET',
                url:'{{ URL::route('update_student_attendance') }}/'+attendance_id+'/'+status,
                success:function(data){
                    //alert(data);
                    console.log(data);
                }
            });
        }
        function submit_with_check(slot_id){

            $.ajax({
                url: '{{ url('/attendance/check_all_absent') }}/' + slot_id,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(res){
                    console.log(res.all_absent);
                    // return false;
                    // 🔴 If all absent → ask confirmation
                    if (res.all_absent) {

                        let confirmSubmit = confirm("All students are marked ABSENT. Are you sure you want to continue?");

                        if (!confirmSubmit) {
                            return; // ❌ stop
                        }
                    }

                    // 🟢 If confirmed OR not all absent → proceed
                    final_submit(slot_id);

                },
                error: function(xhr){
                    console.error(xhr.responseText);
                    alert('Error checking attendance status');
                }
            });

        }
        function final_submit(slot_id){
             sendPostRequest('.attendance_btn','.message_div','attendance/final-submit-attendance/'+slot_id,function(res){
                location.reload();


                   //  setInterval(() => {
                      //  location.reload();
                    // }, 3000);
             },[]);

        }
</script>


@stop

