<style type="text/css">
  .both_div_header {
    width: 100%;
    border: solid 2px #2F3B4D;
    border-top-right-radius: 4px;
    border-top-left-radius: 4px;
    float: left;
    text-align: center;
    font-size: 16px;
    margin-left: 4px;
    /* color: white;
        background-color: #2F3B4D; */
    height: 36px;
    vertical-align: middle;
    line-height: 30px;
  }

  .both_div {
    width: 49%;
    border: solid 1px #7DA0B1;
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
    float: left;
    margin-left: 4px;
    min-height: 230px;

  }

  .btn_wizard {
    width: 150px;
    height: 30px;
    font-size: 14px;
    margin-right: 1px;

  }

  .for_tr {
    cursor: -webkit-grab;
    cursor: grab;
  }

  .sidenav {
    height: 100%;
    width: 0;
    position: fixed;
    z-index: 1;
    top: 0;
    left: 0;
    background-color: #FFF color:#000;
    overflow-x: hidden;
    transition: 0.5s;
    padding-top: 60px;
  }

  .sidenav a {
    padding: 8px 8px 8px 32px;
    text-decoration: none;
    font-size: 25px;
    color: #818181;
    display: block;
    transition: 0.3s;
  }

  .sidenav a:hover {
    color: #f1f1f1;
  }

  .sidenav .closebtn {
    position: absolute;
    top: 0;
    right: 25px;
    font-size: 16px;
    margin-left: 0px;
    width: 100%;
  }

  @media screen and (max-height: 450px) {
    .sidenav {
      padding-top: 15px;
    }

    .sidenav a {
      font-size: 18px;
    }
  }
</style>
<!-- Button trigger modal -->
<?php /*?><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
  Launch demo modal
</button>
<?php */ ?>
<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="0" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: red;">
        <h5 class="modal-title" id="exampleModalCenterTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>


<div id="mySidenav" class="sidenav">

  <div class="closebtn" style="background-color: #2F3B4D; color:#FFF; text-align:center;">Assessment Components </div>
  <table class="table-striped" width="100%">
    <thead>
      <tr>
        <th scope="col">Sr #</th>
        <th scope="col">Assessment</th>
        <th scope="col">Weightage</th>
      </tr>
    </thead>
    <tbody>

      @foreach($assesment as $obj_assessment)
      @if ($obj_assessment->course_content_weightage!="")
      <tr>
        <th scope="row">{{ $loop->iteration }}</th>
        <td>{{$obj_assessment->assessment_name}}</td>
        <td>{{$obj_assessment->course_criteria_weightage}}</td>
      </tr>

      </tr>
      @endif
      @endforeach
    </tbody>
  </table>
</div>
<?php /*?>
<span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776; open</span><?php */ ?>
<div id="for_criteria_entry">
<table class="table"  style="border: solid 0px #2F3B4D; width:98%; float:right;  padding:0px !important;">
  <tr>
      <td colspan="2">
        <!-- <span style="color: red;"> <small style="font-weight: bold">Disclaimer : </small>  Accumulative weightage must be equal to 100 </span> -->
        <span style="color: red;"> <small style="font-weight: bold">Disclaimer: </small> The accumulated weightage must equal 100. </span>
      </td>
  </tr>
    <tr>
    <td width="50%;" style="padding:0px !important;">
        <div style="overflow-y: scroll; height:450px !important;">
        <table class="table table-bordered table-striped mb-0" id="list_assessment_table">
            <thead>
                <tr>
                <td colspan="3" style="background-color: #2F3B4D; color:#FFF; text-align:center;">Assessment Components </td>
                </tr>
            </thead>
            <tbody>
            <?php /*?> {{$obj_assessment->course_criteria_id === "" ? "1":$obj_assessment->course_criteria_id}})   <?php */ ?>
            @foreach($assesment as $obj_assessment)
            @if (($obj_assessment->course_content_weightage=="" || $obj_assessment->course_criteria_status>0) && $obj_assessment->course_criteria_weightage=="")
            <tr id="list{{$obj_assessment->id}}" onclick="add_new_assessment('{{$obj_assessment->id}}','{{$obj_assessment->assessment_name}}','0')">
            <td colspan="2">{{$obj_assessment->assessment_name}} </td>
            <td align="center">
                <i class="fa fa-plus" aria-hidden="true" style="color: green; float: right; font-size:18px;"></i>
            </td>
            </tr>
            @endif
            @endforeach
            </tbody>
        </table>
       </div>
    </td>
    <td width="50%;" style="padding:0px !important;">
      <form method="post" id="course_criteria_form">
        <div style="overflow-y: scroll; height:450px !important;">
        <table class="table table-bordered table-striped mb-0" width="100%" id="submit_assessment_table">
          <tr>
            <td colspan="3" style="background-color: #2F3B4D; color:#FFF; text-align:center;">Selected Components </td>
          </tr>
          {{csrf_field()}}
          <input type="hidden" name="co_id" value="<?= $co_id; ?>">
          @foreach($assesment as $obj_assessment)
          @if (($obj_assessment->course_content_weightage!="" && $obj_assessment->course_criteria_status<=0) || $obj_assessment->course_criteria_weightage!="")
            <tr id="added{{$obj_assessment->id}}">
              <td>{{$obj_assessment->assessment_name}}
                <input type="hidden" name="assessment_id[]" value="{{$obj_assessment->id}}" />
              </td>
              <td>
                <?php
                if ($obj_assessment->course_criteria_weightage != "") {
                  $weightage     = $obj_assessment->course_criteria_weightage;
                  $criteria_id  = $obj_assessment->course_criteria_id;
                } else {
                  $weightage   = $obj_assessment->course_content_weightage;
                  $criteria_id  = 0;
                }
                ?>
                <input type="text" class="form-control weightage_class" name="weightage[]" style="width:60px !important;" value="{{$weightage}}" autocomplete="off" />
                <input type="hidden" name="course_criteria_id[]" value="{{$criteria_id}}">
                <?php /*?>{{$obj_assessment->course_criteria_weightage}}--{{$obj_assessment->course_criteria_id}}<?php */ ?>
              </td>
              <td>
                <i class="fa fa-minus" aria-hidden="true" style="color:red;float:right; width:60px; font-size: 18px;" onclick=""></i>
              </td>
            </tr>
            @endif
            @endforeach
        </table>
        </div>
      </form>
    </td>
  </tr>
  <tr>
    <td>
      <div style="color:red; font-weight:bold;" id="showMsg"></div>
    </td>
    <td align="right" style="border:solid 0px black; padding-right:16px;">

      <input type="button" id="final_submit" class="btn btn-primary" style="display: none;" value="Final Submit">
      <input type="button" id="close_submit_btn" class="btn btn-danger" style="width:140px;" data-dismiss="modal" value="Close">

    </td>
  </tr>
</table>
</div>
<div class="container">
  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="background-color: yellow;">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div>
        <div class="modal-body">
          <p>Some text in the modal.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>

</div>


@section('javascript')
<script type="text/javascript">
  function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
  }

  document.addEventListener('keydown', function(e) {
    switch (e.keyCode) {
        case 37:
            alert('left');
            break;
        case 38:
           alert('up');
            break;
        case 39:
            alert('right');
            break;
        case 40:
            alert('down');
            break;
    }
});



    $(document).on('keypress', 'input,select', function (e) {
       // alert(e.which);
    if (e.which == 13) {
        e.preventDefault();
        // Get all focusable elements on the page
        var $canfocus = $(':focusable');
        var index = $canfocus.index(this) + 1;
        if (index >= $canfocus.length) index = 0;
        $canfocus.eq(index).focus();
    }
    // $('input').keyup(function (e) {
    //     if (e.which == 39)
    //         $(this).closest('td').next().find('input').focus();
    //     else if (e.which == 37)
    //         $(this).closest('td').prev().find('input').focus();
    //     else if (e.which == 40)
    //         $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
    //     else if (e.which == 38)
    //         $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus();
    // });
});


  function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
  }
</script>
<script type="text/javascript">
  function delete_assessment(co_id, assessment_id) {
    $.ajax({
      type: 'POST',
      url: '{{url('delete_criteria_assessment ')}}/' + co_id + '/' + assessment_id,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: 'fd',
      processData: false,
      contentType: false,
      success: function(data) {

      },
      complete: function() {}
    });

  }

  function add_new_assessment(assessment_id, assessment_name, course_criteria_id) {
    //alert(course_criteria_id);
    $("#list" + assessment_id).hide();
    var content = "";
    content += '<tr id="added' + assessment_id + '"><td>' + assessment_name +
      '<input type="hidden" name="assessment_id[]" value="' + assessment_id + '"></td>' +
      '<td><input type="text" name="weightage[]" class="form-control weightage_class" autocomplete="off" style="width:60px !important;"><input type="hidden" name="course_criteria_id[]" value="' + course_criteria_id + '"></td>' +
      '<td style="padding-left:12px !important;"><i onclick="remove_added_assessment(' + assessment_id + ',\'' + assessment_name + '\')" class="fa fa-minus" aria-hidden="true" style="color:red;width:30px; font-size: 18px;"></i></td>' +
      '</tr>';
    $("#submit_assessment_table").append(content);
  }

  function remove_added_assessment(assessment_id, assessment_name, co_id) {

    //alert(co_id+'ok'+assessment_name);
    delete_assessment(co_id, assessment_id)
    //$( "#confirm_modal" ).dialog();
    //return false;

    $("#added" + assessment_id).remove();
    $("#list" + assessment_id).show();
    var assessment_row_id = "list" + assessment_id;
    var content = "";
    content += '<tr id="' + assessment_row_id + '" onclick="add_new_assessment(' + assessment_id + ',\'' + assessment_name + '\',0)"><td colspan="2">' + assessment_name + '</td><td><i class="fa fa-plus" aria-hidden="true" style="color: green; float: right; font-size:18px;"></td></tr>';
  //  $("#list_assessment_table").append(content);
  }
  $('#final_submit').click(function() {




    $("#showMsg").css('display', 'block');
    $.ajax({
      type: 'POST',
      url: '{{ URL::route('teacher/courses_criteria_save')}}',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: $("#course_criteria_form").serializeArray(),
      success: function(data) {

        if (!(data.status)) {
          $('#showMsg').html(data.message);
          return false;
        }

        location.reload();
        setTimeout(function() {
          var modal1 = $('.close', window.parent.document);
          modal1.click();
        }, 2000);
        $('#ResultModal').modal('toggle');
        $("#ResultModal").html(data);
        $('#courseCriteriaModal').modal('close');
      }
    });
  });


  /**
   * Given an input field, this function will only allow numbers with up to two decimal places to be input.
   * @param {object} element
   * @return {number}
   */
  function forceNumber(element) {
    element
      .data("oldValue", '')
      .bind("paste", function(e) {
        var validNumber = /^[-]?\d+(\.\d{1,2})?$/;
        element.data('oldValue', element.val())
        setTimeout(function() {
          if (!validNumber.test(element.val()))
            element.val(element.data('oldValue'));
        }, 0);
      });
    element
      .keypress(function(event) {
        var text = $(this).val();
        if ((event.which != 46 || text.indexOf('.') != -1) && //if the keypress is not a . or there is already a decimal point
          ((event.which < 48 || event.which > 57) && //and you try to enter something that isn't a number
            (event.which != 45 || (element[0].selectionStart != 0 || text.indexOf('-') != -1)) && //and the keypress is not a -, or the cursor is not at the beginning, or there is already a -
            (event.which != 0 && event.which != 8))) { //and the keypress is not a backspace or arrow key (in FF)
          event.preventDefault(); //cancel the keypress
        }

        if ((text.indexOf('.') != -1) && (text.substring(text.indexOf('.')).length > 2) && //if there is a decimal point, and there are more than two digits after the decimal point
          ((element[0].selectionStart - element[0].selectionEnd) == 0) && //and no part of the input is selected
          (element[0].selectionStart >= element.val().length - 2) && //and the cursor is to the right of the decimal point
          (event.which != 45 || (element[0].selectionStart != 0 || text.indexOf('-') != -1)) && //and the keypress is not a -, or the cursor is not at the beginning, or there is already a -
          (event.which != 0 && event.which != 8)) { //and the keypress is not a backspace or arrow key (in FF)
          event.preventDefault(); //cancel the keypress
        }
      });
  }

  function number_validation(full_no, max_num) {
    try {
      var before_decimal_number = full_no.toString().split(".")[0];
      var after_decimal_number = full_no.toString().split(".")[1];
      var after_decimal_length = full_no.toString().split(".")[1].length;

      if (full_no > max_num) {
        var before_decimal_number = before_decimal_number.slice(0, -1)
      } else {
        before_decimal_number = full_no.toString().split(".")[0];
      }


      if (after_decimal_length > 2) {
        full_no = before_decimal_number.concat(".").concat(after_decimal_number.slice(0, 2 - after_decimal_length));
      } else {
        full_no = before_decimal_number.concat(".").concat(after_decimal_number);
      }
    } catch (err) {

      if (full_no > max_num) {
        var full_no = full_no.slice(0, -1)
      }
      // else{
      //     var after_decimal_number = full_no;
      //     var after_decimal_length = 40;
      // }
    }
    // var final_string = full_no+"_____"+after_decimal_number;
    return full_no; //+'_____'+after_decimal_number+'______'+max_num;

  }

  $(document).on("keyup", ".weightage_class", function(evt) {
    var sum = 0;
    $(".weightage_class").each(function() {
      sum += +$(this).val();
    });

    if (sum > 100) {
      $(this).css({
        "border-color": "#bd2130",
        "border-width": "2px",
        "border-style": "solid"
      });
    } else {
      $(this).css({
        "border-color": "#80BDFF",
        "border-width": "2px",
        "border-style": "solid"
      });
    }
    if (sum == 100) {
      $("#final_submit").show();
      $("#final_submit").focus();
      $("#close_submit_btn").hide();
    } else {
      $("#close_submit_btn").show();
      $("#final_submit").hide();
    }
    $("#weightage_sum").html(sum);
    //alert(sum);
  });


  $(document).on("keyup", ".weightage_classhhhh", function() {

    var index = $('.inputs').index(this) + 1;
    $('.inputs').eq(index).focus();

    // alert(index);
    // $("input").not($(":button")).keypress(function (evt) {
    if (evt.keyCode == 13) {
      var iname = $(this).val();
      alert(iname);
      if (iname !== 'Submit') {
        var fields = $(this).parents('form:eq(0),body').find('button, input, textarea, select');
        var index = fields.index(this);
        if (index > -1 && (index + 1) < fields.length) {
          fields.eq(index + 1).focus();
        }
        return false;
      }
    }

  });
  $(document).ready(function() {
    $("#showMsg").hide();
  });
</script>
