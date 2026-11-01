<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
// echo $co_id;
// exit;

?>

@extends('layouts.app',['pageTitle' => 'Online Exam Result'])

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<div class="modal fade" id="move_to_cms" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="width:700px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Marks Move To GRADING</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="move_to_cms_form">
                    {{csrf_field()}}

                    <span style="color: red;">Marks will only move one time.</span>

                    <div class="form-group">
                        <label for="email">Quiz Detail</label>
                        <input type="quiz_detail" class="form-control" style="border: solid 1px black" placeholder="Quiz Detail" id="quiz_detail" name="quiz_detail">

                        <input type="hidden" name="cms_exam_id" id="cms_exam_id">
                        <input type="hidden" name="cms_co_id" id="cms_co_id">
                        <input type="hidden" name="cms_assessment_id" id="cms_assessment_id">
                    </div>

                    <div class="modal-body" id="model_message" style="display: none;"></div>

                    <input type="button" onclick="POST_move_to_cms()" class="btn btn-primary" value="Click to Move to CMS">
                </form>
            </div>
        </div>
    </div>
</div>

<?php



?>

<style>
    .exam-result-card {
        margin: 12px;
        width: calc(100% - 24px);
        border: 0;
        border-radius: 8px;
        background-color: #fff;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .exam-result-inner {
        padding: 26px;
        background-color: #fff;
    }

    .exam-result-header {
        background-color: #D2D6F1;
        color: #1d3158;
        padding: 22px 28px;
        border-radius: 6px 6px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .exam-result-title-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .exam-result-icon {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        border: 2px solid #7b86d9;
        color: #7b86d9;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    .exam-result-icon i {
        font-size: 24px;
        color: #7b86d9;
    }

    .exam-result-header h3 {
        margin: 0;
        font-family: 'Times New Roman', Times, serif;
        font-size: 24px;
        font-weight: 700;
        color: #1d3158;
    }

    .exam-result-course {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #1d3158;
        text-align: right;
    }

    .exam-result-table-wrap {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e4e7f2;
        border-top: 0;
        border-radius: 0 0 6px 6px;
        background-color: #fff;
    }

    .exam-result-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
        table-layout: fixed;
        color: #1d3158;
    }

    .exam-result-table thead tr {
        background-color: #fbfbff;
    }

    .exam-result-table thead td {
        padding: 22px 14px;
        font-size: 15px;
        font-weight: 700;
        color: #1d3158;
        text-align: center;
        border: 1px solid #e4e7f2;
        white-space: nowrap;
    }

    .exam-result-table tbody tr {
        background-color: #fff;
    }

    .exam-result-table tbody tr:nth-child(even) {
        background-color: #f8f9ff;
    }

    .exam-result-table tbody td {
        padding: 30px 14px;
        font-size: 15px;
        color: #1d3158;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #e4e7f2;
        min-height: 120px;
    }

    .assessment-text {
        font-weight: 700;
        color: #078282FF;
        font-size: 17px;
        display: block;
        line-height: 1.5;
    }

    .question-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 70px;
        height: 42px;
        padding: 0 16px;
        border-radius: 6px;
        background-color: #eef3ff;
        color: #2364d8;
        font-size: 20px;
        font-weight: 700;
    }

    .exam-data-icon {
        display: block;
        font-size: 28px;
        color: #4454C3;
        margin-bottom: 10px;
    }

    .exam-date-text,
    .exam-time-text,
    .exam-duration-text {
        display: block;
        font-size: 15px;
        font-weight: 500;
        color: #1d3158;
        line-height: 1.5;
    }

    .exam-action-cell {
        min-width: 170px;
    }

    .exam-action-cell .btn {
        width: 150px;
        min-height: 44px;
        border-radius: 5px;
        font-size: 14px;
        font-weight: 700;
        border: 0;
        margin: 0 auto;
        display: block;
    }

    .exam-action-cell .btn-warning {
        background-color: #f6b800;
        color: #fff;
    }

    .exam-action-cell .btn-primary {
        background-color: #2f55d4;
        color: #fff;
    }

    .exam-action-cell .btn-success {
        background-color: #28a745;
        color: #fff;
    }

    .exam-action-cell hr {
        margin: 14px 0;
        border-top: 0;
    }

    .exam-action-cell a {
        text-decoration: none;
    }

    @media (max-width: 991px) {
        .exam-result-inner {
            padding: 16px;
        }

        .exam-result-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .exam-result-course {
            text-align: left;
            font-size: 17px;
        }

        .exam-result-table {
            min-width: 950px;
        }
    }
</style>

<!-- Print Table Start Here  -->
<div class="card exam-result-card" style="
    margin-bottom: 50px;
">
    <div class="exam-result-inner">

        <div class="exam-result-header">
            <div class="exam-result-title-wrap">
                {{-- <div class="exam-result-icon">
                    <i class="fa fa-check"></i>
                </div> --}}
                <h3>Exam Details</h3>
            </div>

            {{-- <h3 class="exam-result-course">Course: {{$course_name}}</h3> --}}
        </div>

        <div class="exam-result-table-wrap" id="table_div">
            <table class="exam-result-table">
                <thead>
                    <tr>
                        <td><b>Assessment</b></td>
                        <td><b>Title</b></td>
                        <td><b>Total Questions</b></td>
                        <td style="display:none;"><b>Pass Percentage</b></td>
                        <td style="display:none;"><b>Exam Pattern</b></td>
                        <td><b>Exam Date</b></td>
                        <td><b>Exam Start Time</b></td>
                        <td><b>Exam Duration</b></td>
                        <td><b>Action</b></td>
                    </tr>
                </thead>

                <tbody>
                    @foreach($result as $question_bank_rec)
                    <?php
                    $id=my_simple_crypt($question_bank_rec->id,'e');
                    $criteria='';
                    ?>

                    <tr>
                        <td>
                            <?php if(isset($get_criteria)){?>
                                @foreach($get_criteria as $rec_course_criteria)
                                    @if($rec_course_criteria->id==$question_bank_rec->course_criteria_id )
                                        <span class="assessment-text">
                                            {{ $rec_course_criteria->assessment_name}} ({{ $question_bank_rec->assessment_no}})
                                        </span>
                                    @endif
                                @endforeach
                            <?php } ?>
                        </td>

                        <td>{{$question_bank_rec->title}}</td>

                        <td>
                            <span class="question-count-badge">{{$question_bank_rec->total_questions}}</span>
                        </td>

                        <td style="display:none;">{{$question_bank_rec->percentage_status}}</td>

                        <td style="display:none;">{{$question_bank_rec->exam_pattern}}</td>

                        <td>
                            <i class="fa fa-calendar-o exam-data-icon"></i>
                            <span class="exam-date-text"><?php echo substr($question_bank_rec->exam_date,0,11); ?></span>
                        </td>

                        <td>
                            <i class="fa fa-clock-o exam-data-icon"></i>
                            <span class="exam-time-text"><?php echo substr($question_bank_rec->exam_start_time,11,10);?></span>
                        </td>

                        <td>
                            <i class="fa fa-clock-o exam-data-icon"></i>
                            <span class="exam-duration-text">{{$question_bank_rec->exam_duration}}</span>
                        </td>

                        <td class="exam-action-cell">
                            <?php if($question_bank_rec->move_to_cms == 'No'){?>
                                <button type="button" class="btn btn-warning btn-lg btn-block" onclick="move_to_cms(<?=$question_bank_rec->id;?>,<?=$question_bank_rec->co_id;?>,<?=$question_bank_rec->assessment_id;?>)">Move to CMS</button>
                                <hr>
                            <?php } else {
                                $criteria='';
                                foreach($criteria_detail as $rec_criteria){
                                    if( $rec_criteria->id  == $question_bank_rec->move_to_cms){ ?>

                                        <button type="button" class="btn btn-success btn-lg btn-block">{{$rec_criteria->ccd_details}}</button>
                                        <hr>

                                    <?php }
                                } ?>
                            <?php } ?>

                            <a href="{{route('viewstudentresult')}}/<?=$id;?>">
                                <button type="button" class="btn btn-primary btn-lg btn-block">View Result</button>
                            </a>
                        </td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

@stop

@section('javascript')
<script>
function movetocms(id){
$.ajax({
            type:'get',
            url:'{{url('movetocms')}}/'+id,
            success:function(data){
              if(data == 1){

                alert('Move to Cms Successfully')
              }else{
                alert('Already Moved to Cms')
              }
            }
        });
}
function move_to_cms(exam_id,co_id,assessment_id){
    $('#move_to_cms').modal('show');
    $("#cms_exam_id").val(exam_id);
    $("#cms_co_id").val(co_id);
    $("#cms_assessment_id").val(assessment_id);
    $("#quiz_detail").val('');
}
function  POST_move_to_cms(){
       $("#model_message").show();
       if($.trim($('#quiz_detail').val()) == '')
        {
            $('#quiz_detail').css("border-color", "red");
            $('#quiz_detail').focus();
            $("#model_message").html("Enter Details please");
        }else{
        var myform = document.getElementById("move_to_cms_form");
        var fd = new FormData(myform );
        $.ajax({
            url: '/move_to_cms',
            data: fd,
            cache: false,
            processData: false,
            contentType: false,
            type: 'POST',
            success: function (data) {
             if(data.trim()==1){
                 $("#model_message").html('Marks move to CMS Successfully');
                 setTimeout(function()
                {
                    location.reload();
                },1500);
             }else{
                $("#model_message").html(data);
             }
            }
        });
    }

}

</script>
@stop