<?php

use App\sessions;

$sess = new sessions();
$sess->startSession();

?>
@extends('layouts.app',['pageTitle' => 'Exam'])
{{-- @dd('123'); --}}
@section('content')
<style>
  /* Chrome, Safari, Edge, Opera */
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  /* Firefox */
  input[type=number] {
    -moz-appearance: textfield;
  }


  
</style>

<style>

/* =========================================================
   EXAM MARKS MANAGEMENT SCREEN
   SAME STYLE AS PREVIOUS SCREEN
========================================================= */

/* Card */
.card {
    border: 1px solid #e5e5e5 !important;
    border-radius: 5px !important;
    box-shadow: none !important;
    background: #fff !important;
}

.card-header {
    background: #ffffff !important;
    border-bottom: 1px solid #eeeeee !important;
    padding: 14px 16px !important;
}

.card-header h3 {
    margin: 0 !important;
    color: #2d2d2d !important;
    font-family: inherit !important;
    font-size: 20px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;
}

.card-body {
    padding: 18px !important;
}


/* =========================================================
   TABLE
========================================================= */

#simpletable {
    width: 100% !important;
    margin: 0 !important;

    border-collapse: separate !important;
    border-spacing: 0 !important;

    border: 1px solid #e5e5e5 !important;
    border-radius: 4px !important;

    background: #ffffff !important;

    font-size: 12px !important;
}


/* =========================================================
   TABLE HEADER
========================================================= */

#simpletable thead,
#simpletable thead tr {
    background: #247d72 !important;
}

#simpletable thead th {
    padding: 11px 12px !important;

    background: #17675F !important;
    color: #ffffff !important;

    border-top: 0 !important;
    border-bottom: 0 !important;
    border-right: 1px solid rgba(255, 255, 255, 0.15) !important;

    font-size: 12px !important;
    font-weight: 500 !important;

    line-height: 1.4 !important;
    vertical-align: middle !important;

    white-space: nowrap;
}

#simpletable thead th:last-child {
    border-right: 0 !important;
}


/* =========================================================
   TABLE BODY
========================================================= */

#simpletable tbody th,
#simpletable tbody td {
    padding: 10px 12px !important;

    background: #ffffff !important;
    color: #444444 !important;

    border-top: 0 !important;
    border-bottom: 1px solid #eeeeee !important;

    font-size: 12px !important;
    line-height: 1.5 !important;

    vertical-align: middle !important;
}


/* Remove Bootstrap striped effect */
#simpletable.table-striped tbody tr:nth-of-type(odd),
#simpletable.table-striped tbody tr:nth-of-type(even) {
    background: #ffffff !important;
}

#simpletable.table-striped tbody tr:nth-of-type(odd) td,
#simpletable.table-striped tbody tr:nth-of-type(even) td,
#simpletable.table-striped tbody tr:nth-of-type(odd) th,
#simpletable.table-striped tbody tr:nth-of-type(even) th {
    background: #ffffff !important;
}


/* Hover */
#simpletable tbody tr:hover td,
#simpletable tbody tr:hover th {
    background: #f7faf9 !important;
}


/* Last row */
#simpletable tbody tr:last-child td,
#simpletable tbody tr:last-child th {
    border-bottom: 0 !important;
}


/* =========================================================
   COLUMN WIDTHS
========================================================= */

#simpletable th:nth-child(1),
#simpletable td:nth-child(1) {
    width: 55px;
    text-align: center;
}

#simpletable th:nth-child(2),
#simpletable td:nth-child(2) {
    min-width: 270px;
}

#simpletable th:nth-child(3),
#simpletable td:nth-child(3) {
    min-width: 150px;
}

#simpletable th:nth-child(4),
#simpletable td:nth-child(4) {
    width: 100px;
    text-align: center;
}

#simpletable th:nth-child(5),
#simpletable td:nth-child(5) {
    width: 155px;
    text-align: center;
}

#simpletable th:nth-child(6),
#simpletable td:nth-child(6) {
    width: 110px;
    text-align: center;
}


/* =========================================================
   INPUTS
========================================================= */

#simpletable input.form-control {
    width: 72px !important;
    height: 32px !important;

    padding: 5px 8px !important;
    margin: 0 auto !important;

    border: 1px solid #dddddd !important;
    border-radius: 4px !important;

    background: #ffffff !important;
    color: #333333 !important;

    font-size: 12px !important;
    text-align: center !important;

    box-shadow: none !important;
    outline: none !important;
}

#simpletable input.form-control:focus {
    border-color: #247d72 !important;
    box-shadow: 0 0 0 2px rgba(36, 125, 114, 0.08) !important;
}


/* Hide number arrows */
#simpletable input[type="number"]::-webkit-outer-spin-button,
#simpletable input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

#simpletable input[type="number"] {
    -moz-appearance: textfield;
}


/* =========================================================
   TOTAL MARKS READONLY
========================================================= */

#simpletable input[readonly] {
    background: #f4f6f6 !important;
    color: #555555 !important;

    border-color: #e1e5e4 !important;

    font-weight: 500 !important;

    cursor: default;
}


/* =========================================================
   NEGATIVE MARKS SELECT
========================================================= */

#simpletable select.form-control {
    width: 72px !important;
    height: 32px !important;

    padding: 4px 7px !important;

    border: 1px solid #dddddd !important;
    border-radius: 4px !important;

    background-color: #ffffff !important;
    color: #333333 !important;

    font-size: 12px !important;

    box-shadow: none !important;
    outline: none !important;

    cursor: pointer;
}

#simpletable select.form-control:focus {
    border-color: #247d72 !important;
    box-shadow: 0 0 0 2px rgba(36, 125, 114, 0.08) !important;
}


/* =========================================================
   NEGATIVE MARKS CELL
========================================================= */

#simpletable td > div[style*="float:left"],
#simpletable td > div[style*="float: left"] {
    float: none !important;
    display: inline-flex !important;
    align-items: center !important;
    vertical-align: middle !important;
}


/* =========================================================
   SAVED MESSAGE
========================================================= */

#simpletable div[id$="saved_msg_div_file"] {
    margin-left: 7px !important;
    padding: 3px 7px !important;

    background: #e8f4f1 !important;

    border: 1px solid #c8e3de !important;
    border-radius: 3px !important;

    color: #247d72 !important;

    font-size: 10px !important;
    font-weight: 500 !important;

    line-height: 1.3 !important;
}


/* =========================================================
   QUESTION CONTENT
========================================================= */

#simpletable td:nth-child(2) {
    color: #333333 !important;
    overflow-wrap: anywhere;
}

#simpletable td:nth-child(2) p {
    margin-top: 0 !important;
    margin-bottom: 5px !important;
}

#simpletable td:nth-child(2) p:last-child {
    margin-bottom: 0 !important;
}

#simpletable td:nth-child(2) img {
    max-width: 100% !important;
    height: auto !important;
}


/* =========================================================
   WARNING / EMPTY MESSAGES
========================================================= */

.card-body > h1 {
    margin: 0 0 15px !important;
    padding: 12px 14px !important;

    background: #fff7e8 !important;

    border: 1px solid #f1d8a5 !important;
    border-radius: 4px !important;

    color: #8a651d !important;

    font-size: 13px !important;
    font-weight: 500 !important;

    line-height: 1.5 !important;
}

.card-body > h3 {
    margin: 0 0 15px !important;
    padding: 12px 14px !important;

    background: #f7faf9 !important;

    border: 1px solid #dfe9e7 !important;
    border-radius: 4px !important;

    color: #555555 !important;

    font-size: 13px !important;
    font-weight: 500 !important;

    line-height: 1.5 !important;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 991.98px) {

    .card-body {
        overflow-x: auto;
    }

    #simpletable {
        min-width: 850px !important;
    }
}


@media (max-width: 767.98px) {

    .card-header {
        padding: 12px 14px !important;
    }

    .card-header h3 {
        font-size: 17px !important;
    }

    .card-body {
        padding: 14px !important;
    }

    #simpletable thead th,
    #simpletable tbody th,
    #simpletable tbody td {
        padding: 8px 10px !important;
    }
}

.profile-overview-title {
    margin: 0 0 14px;
    font-size: 24px;
    font-weight: 700;
    color: #2d2d2d;
    line-height: 1.2;
}

</style>



<!-- //// print record /////////////// -->
  <div class="" style="display:inline ">
    {{-- <div style="float:left">
      <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left;color:black">Marks Management </h3>

    </div> --}}
    <div>
      <h4 class="profile-overview-title">{{$course_name}} 
        {{-- <button type="button" onclick="go_back('<?=$co_id?>','<?=$course_id?>')" class="btn btn-primary">
          Go Back
        </button> --}}
      </h4>

    </div>

  </div>
<div class="card" style="width: 100%;margin-top: 20px">


  <div class="card-body" style="padding: 0 !important;">
    <div style="float:right">
     
    </div>
    <?php


    $count = count($exam_questinbank);

    if ($count == 0) {


      echo '<h3> Kindly Enter Question First In Exam </h3>';
      //exit;
    } else {
      // echo $exam_total_ques.'__'.$total_question;
      if ($exam_total_ques > $total_question) {

        echo '<h1>IF You Want to Change Marks Please Add Exam Questions Equal to Total Questions </h1>';
        //exit();
      } else {



    ?>
        <table id="simpletable" class="table table-striped">
          <thead>
            <tr>
              <th scope="col ">#</th>
              <th scope="col">Question</th>
              <th scope="col">Exam Name</th>
              <th scope="col">Marks</th>
              <th scope="col">Negative Marks %</th>
              <th scope="col">Total Marks</th>
              <!-- <th scope="col">Action </th> -->
            </tr>
          </thead>
          <tbody>
            <tr>
              <?php
              $count = 1;

              if( count($exam_questinbank) > 0 ){

              ?>
              @foreach($exam_questinbank as $exam_bank_rec)
              <?php
              //print_r($exam_questinbank);
              //       $exam_ques = (1 == $question_bank_rec->exam_status) ? 'Yes' : 'No'; // $r is set to 'Yes'

              ?>
              <th scope="row">{{$count}}</th>
              <td>
                  @if($exam_bank_rec->question !== strip_tags($exam_bank_rec->question))
        {!! $exam_bank_rec->question !!}
    @else
        {{ $exam_bank_rec->question }}
    @endif
              </td>
              <td>{{$exam_bank_rec->title}}</td>
              <td><input type="number" min="0" name="marks" onkeyup="update_question_marks(<?=$exam_bank_rec->id;?>,'<?=$exam_bank_rec->exam_papers_id;?>')" id="marks<?= $exam_bank_rec->id; ?>" class="form-control" style="width:70px;" value="{{$exam_bank_rec->question_marks}}"></td>
              <!-- <td><input type="number" min="0" name="neg" id="neg<?= $exam_bank_rec->id; ?>"class="form-control"  style="width:70px;" value="<?= $exam_bank_rec->neg_marks ?>" ></td> -->
              <td  >
                <div style="float:left; ">
                  <select name="neg" class="form-control" onchange="update_question_marks(<?=$exam_bank_rec->id;?>,'<?=$exam_bank_rec->exam_papers_id;?>')" id="neg<?=$exam_bank_rec->id;?>" style="width:70px;">
                    <option <?= (0 == $exam_bank_rec->neg_marks) ? 'selected="selected"' : '' ?> value="0"><?= (0 == $exam_bank_rec->neg_marks) ? '0' : '0' ?></option>
                    <option <?= (25 == $exam_bank_rec->neg_marks) ? 'selected="selected"' : '' ?> value="25"><?= (25 == $exam_bank_rec->neg_marks) ? '25' : '25' ?></option>
                    <option <?= (33 == $exam_bank_rec->neg_marks) ? 'selected="selected"' : '' ?> value="33"><?= (33 == $exam_bank_rec->neg_marks) ? '33' : '33' ?></option>
                    <option <?= (50 == $exam_bank_rec->neg_marks) ? 'selected="selected"' : '' ?> value="50"><?= (50 == $exam_bank_rec->neg_marks) ? '50' : '50' ?></option>
                    <option <?= (75 == $exam_bank_rec->neg_marks) ? 'selected="selected"' : '' ?> value="75"><?= (75 == $exam_bank_rec->neg_marks) ? '75' : '75' ?></option>
                    <option <?= (100 == $exam_bank_rec->neg_marks) ? 'selected="selected"' : '' ?> value="100"><?= (100 == $exam_bank_rec->neg_marks) ? '100' : '100' ?></option>
                  </select>
                </div>
                <div id="{{$exam_bank_rec->id}}saved_msg_div_file" style="float:left;color: green;display:none">Saved</div>
              </td>
              <!-- <td> <button type="button"s class="btn btn-lg" onclick="update_question_marks('<?= $exam_bank_rec->id; ?>','<?= $exam_bank_rec->exam_papers_id; ?>')"><i class="fa fa-edit"></i> Update </button></td> -->
              <td><input readonly type="text" name="total_with_neg" class="form-control" style="width:70px;" value="{{$exam_bank_rec->total_with_neg}}"></td>
            </tr>
            <?php
            $count++;
            ?>
            @endforeach

            <?php }else{ echo '<tr><td colspan="6" style="text-align:center;" >No Record</td></tr>'; } ?>

          </tbody>
        </table>

    <?php }
    } ?>
  </div>

  <!-- <div id="saved_msg_div_file" style="display: none;color: green"><span>Marks Update Successfully In Exam</span></div>  -->
  <!-- <div id="saved_msg_div_file_error" style="display: none;color: red"><span>You already enetered negative marks all in one</span></div>  -->
</div>
@stop
@section('javascript')
<script>
  function go_back(co_id,course_id){
    
  
    window.location = '<?=url('/teacher/online_exam/')?>/'+co_id+'/'+course_id;
  }

  function update_question_marks(id, paper_id) {

    var neg = $("#neg"+id).val();
    //alert	(neg);
    //return false;
    var update_marks = $("#marks"+id).val();
    //var neg = $("#neg"+id).val();
    //alert(tt_marks);
    // var bi_form = document.querySelector('#question_form');
    var fd = new FormData();


    $.ajax({
      type: 'get',
	  url: '{{url('teacher/update_marks')}}/'+id+'/'+update_marks+'/'+neg+'/'+paper_id,  
      data: fd,
      processData: false,
      contentType: false,
      success: function(data) {
        //alert(data)
        if (data == 2) {
          //  $("#saved_msg_div_file").show();
          //   $("#saved_msg_div_file_error").show();
          $("#"+ id + "saved_msg_div_file").show();

        }
        $("#" + id + "saved_msg_div_file").show();
        console.log(data.message);
        // $("#neg"+id).val(neg);
        // 	$("#saved_msg_div_file").show();
        $("#simpletable").load(window.location + " #simpletable");
        setTimeout(function() {
          $("#saved_msg_div_file").hide();

        }, 3000);
      },

      complete: function() {

      }
    });
  }
</script>
@stop