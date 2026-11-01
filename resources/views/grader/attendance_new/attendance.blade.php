<?php
use App\sessions;

$sess = new sessions();
$sess->startSession();

?>

@extends('layouts.master')
@section('title')
BNU CMS
@stop
@section('mainBody')

<style>
.card_div{
    padding: 0px 0px;
}
.row_div{
    margin: 0px 0px;
}
</style>

<div class="row row_div">

    <div class="card col-8 card_div" >
        <div class="card-header">
            <h3>Attendance </h3>
        </div>
        <div class="card-body">
            
        </div>
    </div>

    <div class="card col-4 card_div" >
        <div class="card-header">
            <h3>Student with same courses </h3>
        </div>
        <div class="card-body">
            
        </div>
    </div>


</div>









@stop
@section('pageScripts')
<script type="text/javascript">

    var apiURL = url('/api/');

    function loadClassDates(coID,ttID){
       //  attendance/loadClassDates/{get_co_id?}/{tt_id?}
       // loadClassSlotDetail

       $.ajax({
            type:'GET',
            url:apiURL+'attendance/load-class-dates',
            success:function(data){
                console.log('data-->',data);
            }
        });



    }


    $(document).ready(function(){
        // alert('--');
        loadClassDates(,ttID);
    });



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


        alert('yes yes');


        $('#close_button_id').click(function (){
            $('#attendance_submit_summary').modal('hide');
        });
         
        
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
                //  alert(get_tt_id+"___"+get_tt_class_status);

                // alert(get_tt_id);
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
                        load_course_attendance_summary(class_date);
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
@stop