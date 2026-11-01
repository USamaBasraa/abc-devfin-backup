@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<style>
.invisible { display: none; }
.dropbtn {
  background-color: #04AA6D;
  color: white;
  padding: 10px;
  font-size: 16px;
  border: none;
}
.dropdown {
  position: relative;
  display: inline-block;
}
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f1f1f1;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}
.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown-content a:hover {background-color: #ddd;}

.dropdown:hover .dropdown-content {display: block;}

.dropdown:hover .dropbtn {background-color: #3e8e41;}
ul {
    margin: 20px;
}

.input-color {
    position: relative;
}
.input-color input {
    padding-left: 25px;
      border:0px solid;
}
.input-color .color-box {
    width: 15px;
    height: 15px;
    display: inline-block;
    background-color: #ccc;
    position: absolute;
    left: 5px;
    top: 5px;
}
.challanDIV {
    height: 350px;
    display: inline-block;
    width: 100%;
    overflow: auto;
}
</style>
<?php

$getChallan = App\Models\Grader\StudentFinancial::fetchChallan('F2018','F2019-102');

//print_r($getChallan );

?>

<div class="card">
	<div class="card-header" style="display:inline">
  <form action="{{url('staff/export-to-excel')}}" method="post" enctype="multipart/form-data" id="filterFormOne"  >
    @csrf
    <div class="row">
        <h3 class="col-sm-8">Filter By Student Financial Details: </h3>
         <div class="col-sm-4">
            <button type="button" class="btn btn-primary" onclick="getPDF()" >
              <i  class="fa fa-file-excel-o" aria-hidden="true"> Semester Challan</i>
            </button>
             <button type="submit" class="btn btn-secondary" onclick="getHostelDetails()" >
                <i  class="fa fa-file-excel-o" aria-hidden="true"> Hostel Challan</i>
            </button>
          </div>
      </div>
            <div class="row">
                <div class="form-group col-sm-3">
                    <label for="session_sel">Session</label>
                    <select class="form-control select2-show-search" name="session" id="session" data-placeholder="Choose one (with searchbox)">
                    @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}"><?=strtolower($value);?></option>
                        @endforeach

											</select>
                </div>
                <div class="form-group col-sm-3">
                    <label for="session_sel">For Session</label>
                    <select class="form-control select2-show-search" name="session-for" id="session-for" data-placeholder="Choose one (with searchbox)">
                        <option value=""></option>
                    @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}"><?=strtolower($value);?></option>
                        @endforeach

											</select>
                </div>
                <div class="form-group col-sm-3">
                    <label for="school_sel">School</label>
                    <select class="form-control select2-show-search"  name="school" id="school_sel" data-placeholder="Choose one (with searchbox)" onchange="getDepartments();">
                        <option value=""></option>
                        @foreach ($schools as $row)
                            <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label for="department">Department</label>
                    <select class="form-control select2-show-search"  data-placeholder="Choose one (with searchbox)"  name="department" id="department" class="form-control"  >
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label for="department">Scholarships</label>
                    <select class="form-control select2-show-search"  data-placeholder="Choose one (with searchbox)"  name="schID" id="schID" class="form-control"  >
                       <option value=""></option>
                         @foreach ($Scholarship_Type as $row)
                        <option value="{{$row->scholarship_id}}">{{ $row->scholarship_name }}</option>
                        @endforeach
                  </select>
                </div>
                <div class="form-group col-sm-3">
                    <label for="department">Fees Type</label>
                    <select class="form-control select2-show-search"  data-placeholder="Choose one (with searchbox)"  name="fee_type" id="fee_type" class="form-control"  >
                       <option value=""></option>
                       <option value="full_semester">Full Semester</option>
                       <option value="per_credit">Per Credit</option>

                  </select>
                </div>
                <div class="form-group col-sm-3">
                    <label for="department">Enrollment No</label>
                      <input type="text" name="enrollment" id="enrollment" class="form-control"/>
                </div>
                <div class="form-group col-sm-3">
                  <label for="department">Students Status</label>
                    <select class="form-control select2-show-search"  data-placeholder="Choose one (with searchbox)"  name="student_status" id="student_status" class="form-control"  >
                    <option value="all_date">All Data</option>
                    <option value="requested_and_pending" selected>Requested and Pending</option>
                    <!-- <option value="Generated">Challan Generated Students</option>
                    <option value="Allowed">Allowed Students</option>
                    <option value="NotAllowed">DisApprove Students</option> -->
                    </select>
                  </div>
                <div class="form-group col-sm-3">
                    <label for="department">Record Type</label>
                    <select class="form-control select2-show-search"  data-placeholder="Choose one (with searchbox)"  name="record_type" id="record_type" class="form-control"  >
                       <option value="challan_wise">All</option>
                       <option value="ready_for_challan">Ready For Challan</option>
                       <option value="challan_generated">Challan Generated</option>
                       <option value="challan_not_generated">Challan Not Generated</option>
                  </select>
                </div>

                <div class="form-group col-sm-2">
                        <label for="getResultsByProgDept">&nbsp;&nbsp;&nbsp;&nbsp;</label>
                        <button type="button" class="btn btn-primary"  style="margin-top: 25px;"  onclick="getResults();" >Get Result</button>
                </div>
            </div>
<!-- From Here Advance Report Options -->

<p>
  <button class="btn btn-info" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" style="float:right;">
    Advance Options
  </button>
</p>
<div class="collapse" id="collapseExample">
  <div class="card card-body">
        <div class="row">
                <div class="form-group col-sm-5">
                    <label for="department">Report Type</label>
                    <select class="form-control select2-show-search" style="width:300px"  data-placeholder="Choose one (with searchbox)"  name="report_type" id="report_type" class="form-control"  >
                       <option value="all_challan_list">Challan List</option>
                       <option value="challan_by_student">Challan Summary by Student</option>
                       <option value="challan_by_school">Challan Summary by School</option>
                       <!-- <option value="challan_by_schools">Challan Summary by Schools</option>
                       <option value="challan_by_all">Challan Summary by Schools</option> -->
                    </select>
                </div>
                <div class="form-group col-sm-5">
                        <label for="getResultsByProgDept">&nbsp;&nbsp;&nbsp;&nbsp;</label>
                        <button type="button" class="btn btn-primary"  onclick="getChallanStatus()" >Challan Status</button>
                </div>
        </div>
  </div>
</div>


		</form>
<p>

    <!-- color list start------------------------------- -->
    <ul>
    <li>
        <div class="input-color">
            <input style="width: 100%;" type="text" value="This Color represents the request has to RO" readonly/>
            <div class="color-box" style="background-color: #FF850A;"></div>
            <!-- Replace "#FF850A" to change the color -->
        </div>
    </li>
    <li>
        <div class="input-color">
            <input style="width: 100%;" type="text" value="This Color represents the request has forward to accounts" readonly />
            <div class="color-box" style="background-color: #6495ED;"></div>
            <!-- Replace "navy" to change the color -->
        </div>
    </li>
    <li>
        <div class="input-color">
            <input style="width: 100%;" type="text" value="Request has been Approved" readonly />
            <div class="color-box" style="background-color: #2A683E;"></div>
            <!-- Replace "navy" to change the color -->
        </div>
    </li>
</ul>

<!-- color list ended ------------------------------------------- -->
    <div style="display:inline">
      <div style="float:left">
       <!-- <button class="btn btn-primary" id="updRecordBtn" onclick="updRecord();" >Update Record</button>  -->
        <span id="udpateRecordMsg"> </span>
      </div>
      <div style="float:right">
        <!-- <span id="total-record"> </span> -->
        <h1 id="total_records"></h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#myModalCommunication"  onclick="genChallan()" >Generate Challan</button>
        <i class="fa fa-bell fa-2x" aria-hidden="true"></i><span id="getChallanRequestCount" class="badge sec badge-danger"></span>
      </div>
    </div>
	</div>
</p>
    <div class="card-body table-responsive">
      <table class="table table-bordered data-table" id="tbl_exporttable_to_xls">
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Student Name</th>
                <th>Status</th>
                <th>Total Outstanding</th>
                <th>CGPA</th>
                <!-- <th>Fee Type</th>
                <th>Challan#</th> -->
                <th>Total Challans</th>
                <th>Enrolled Students</th>
                <th>Total.Cr</th>
                <!-- <th><input  type="checkbox" value="" style="margin-left: 10px;" id="topcheckbtn"></th></th> -->
                <th colspan="2" class="excludeExport" width="200px">Action</th>
                <!-- <th class="excludeExport" width="100px">Details</th> -->
            </tr>
        </thead>
        <tbody id="result-body" ></tbody>
    </table>
    </div>
</div>

 <!-- The details Modal -->

 <div class="modal fade bd-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="max-width:1100px" >
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Student Fees Details : <span id="std-data"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <table class="table table-bordered">
        <thead>
          <tr>
                  <th  style="font-weight:bold !important">Sr.</th>
                  <th  style="font-weight:bold !important">Scholarship Name</th>
                  <th  style="font-weight:bold !important">Scholarship %</th>
          </tr>
        </thead>
        <tbody id="result-data1" ><tr><td colspan="12" style="text-align:center;">No Filter Apply Yet!</td></tr></tbody>
      </table>
    <table class="table table-bordered table-responsive"  >
        <thead>
            <tr>
                <th  style="font-weight:bold !important">Sr.</th>
                <!-- <th  style="font-weight:bold !important">Student Name</th>
                <th  style="font-weight:bold !important">Roll No</th> -->
                <th style="font-weight:bold !important">Program</th>
                <th  style="font-weight:bold !important">Session</th>
                <th  style="font-weight:bold !important">Challan #</th>
                <th  style="font-weight:bold !important">Duedate</th>
                <th  style="font-weight:bold !important">Paidon</th>
                <th  style="font-weight:bold !important">Amount</th>
                <th  style="font-weight:bold !important">Action</th>
              </tr>
        </thead>
        <tbody id="result-data" ><tr><td colspan="12" style="text-align:center;">No Filter Apply Yet!</td></tr></tbody>
      </table>
      <table class="table table-bordered table-responsive"  >
        <thead>
                <th colspan="7"  style="font-weight:bold !important"><h1>Request Log</h1></th>
        </thead>
        <thead>
            <tr>
                <th  style="font-weight:bold !important">Sr.</th>
                <th  style="font-weight:bold !important">Student Id</th>
                <th  style="font-weight:bold !important">Request Type</th>
                <th  style="font-weight:bold !important">Entry User</th>
                <th  style="font-weight:bold !important">Entry Date Time</th>
                <th  style="font-weight:bold !important">Forward Type</th>
                <th  style="font-weight:bold !important">Log Type</th>
              </tr>
        </thead>
        <tbody id="result-request-data" ><tr><td colspan="12" style="text-align:center;">No Filter Apply Yet!</td></tr></tbody>
      </table>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>

  </div>
</div>


 <!-- The Generate Modal -->
 <div class="modal fade" id="myModalGenerate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Challan Regenrate Panel: <span style="font-weight:bold" id="std-detail"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div class="modal-body">
        <form id="cform" method="post"  enctype="multipart/form-data" name="cform"  id="cform">
        @csrf
        <div class="row col-md-12">
          <div class="form-group col-sm-6">
            <label for="recipient-name" class="col-form-label">No of Challan:</label>
            <input type="hidden" id="scd_id" name="scd_id" class="form-control"/>
            <input type="hidden" id="regNo" name="regNo" class="form-control"/>
            <input type="hidden" id="sesID" name="sesID" class="form-control"/>

            <select  name="challanNo" id="challanNo" class="form-control"  >
                <option value="1">--One--</option>
                <option value="2">--Two--</option>
                <option value="3">--Three--</option>
              </select>
          </div>
          <div class="form-group col-sm-6">
                    <label for="challan-type" class="col-form-label">Challan Type</label>
                    <select class="form-control"   name="challan_type" id="challan_type"  >
                       <option value=""></option>
                       <option value="TUTIONFEE">Semester Challan</option>
                       <option value="cancel_challan">Cancell Challan</option>
                       <option value="adjust_challan">Adjust Challan</option>
                       <option value="HOSTELFEE">Hostel Challan</option>
                       <option value="MISCELLANEOUS">MISCELLANEOUS Challan</option>
                       <option value="DISCIPLINE">Discipline Challan</option>
                       <option value="GAT">GAT Challan</option>
                       <option value="Retake_Exam">Retake Exam</option>
                       <option value="Revise_Challan">Revise Challan</option>
                       <option value="Grade_Review">Grade Review</option>
                       <option value="late_enrollment">Late Enrollment</option>
                       <option value="late_fee_submission">Late Fee Submission</option>
                      <option value="discipline _challan">Discipline </option>
                    </select>
          </div>
        </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Select Challan :</label>
            <select  name="upChallan" class="form-control" id="challan-body"></select>
          </div>

          <div class="form-group">
            <label for="message-text" class="col-form-label">Description:</label>
            <textarea class="form-control" name="desc-text" id="desc-text"></textarea>
          </div>
          <div class="row col-md-12">
              <div class="form-group col-sm-4">
                    <label for="department">Due Date</label>
                      <input type="date" name="due_date" id="due_date" class="form-control"/>
                </div>
                <div class="form-group col-sm-4">
                    <label for="department">Valid Date</label>
                      <input type="date" name="valid_date" id="valid_date" class="form-control"/>
                </div>

            </div>

        </form>
      </div>
      <div class="modal-footer">
        <span id="UpdateMsg"></span>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" onclick="RequestFun()" class="btn btn-primary">Request</button>
      </div>
    </div>
  </div>
</div>
<!-- The Generate communication Modal -->
<div class="modal fade bd-example-modal-lg" id="myModalCommunication" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="max-width:1100px" >
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Generate Challan Details : <span id="std-data"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="comform" method="post"  enctype="multipart/form-data" name="comform" >
        @csrf
          <div class=" row form-group">
            <div class="col-md-2">
              <input type="hidden" id="sessionID" name="sessionID" class="form-control"/>
            </div>
             <div class="col-md-8">
               <label for="message-text" class="col-form-label">Description:</label>

              <textarea class="form-control" name="description" id="description" rows="4"></textarea>
            </div>
          </div>

          <div class="row col-md-12">
              <div class="form-group col-sm-4">
                    <label for="department">Due Date</label>
                      <input type="date" name="due_date" id="due_date" class="form-control"/>
                </div>
                <div class="form-group col-sm-4">
                    <label for="department">Valid Date</label>
                      <input type="date" name="valid_date" id="valid_date" class="form-control"/>
                </div>
                <div class="form-group col-sm-4">
                    <label for="department">Challan Type</label>
                    <select class="form-control"   name="challan_type" id="challan_type" >
                       <option value=""></option>
                       <option value="TUTIONFEE">Semester Challan</option>
                       <option value="HOSTELFEE">Hostel Challan</option>
                       <option value="MISCELLANEOUS">MISCELLANEOUS Challan</option>
                  </select>
                </div>
            </div>
        </form>
        <div class="modal-footer">
            <span style="color:green" id="insertMsg"></span>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" onclick="genChallanComm();" class="btn btn-primary">Generate</button>
      </div>
      <div class="challanDIV">
        <table class="table table-bordered "  >
            <thead>
                <tr>
                    <th  style="font-weight:bold !important">Sr.</th>
                    <th  style="font-weight:bold !important">Description</th>
                    <th style="font-weight:bold !important">User Name</th>
                    <th style="font-weight:bold !important">Date</th>
                  </tr>
            </thead>
            <tbody id="communication-body" ><tr><td colspan="4" style="text-align:center;">No Filter Apply Yet!</td></tr></tbody>
          </table>
        </div>
    </div>

  </div>
</div>

@stop
@section('javascript')
<script type="text/javascript">

  $(function() {

    function getChallanCount() {
      sendGetRequest('','.asdas','get-student-req-count',function(res){
        // console.log(res.data);
          if(res.data > 0){
          $('#getChallanRequestCount').html(res.data);
          }
      });
    }
    setInterval(getChallanCount, 30000);
      getChallanCount();
  })
 var lastURL = '';
 function updRecord(){
  $('#updRecordBtn').attr('disabled', true);
  var sess_id = $('#session').val();
  sendPostRequest('', '.asdasdas', 'update-record-procedure/'+sess_id, function(res) {
        if(res.status == true) {
          $("#udpateRecordMsg").css("color", "green");
          $('#udpateRecordMsg').html(res.message);
          setTimeout(function(){
            $('#udpateRecordMsg').html('');
            $('#updRecordBtn').attr('disabled', false);
            },2000);
      }
  }, '');

}
function ExportToExcel() {
    var elt = document.getElementById('tbl_exporttable_to_xls');
    let tableClone = $('#tbl_exporttable_to_xls').clone();
      tableClone
      	.find(".excludeExport")
        .each(function(){
        	$(this).remove();
        });
        $("tbl_exporttable_to_xls").html(tableClone);
    var wb = XLSX.utils.table_to_book(elt, { sheet: "sheet1" });
    return dl ?
      XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }):
      XLSX.writeFile(wb, fn || ('accounts.' + (type || 'xlsx')));
 }

    function getPDF(){

        window.open(lastURL+'&result-for=print');

    }
    function getChallanStatus(){
        var school = $('#school_sel').val();
        var department = $('#department').val();
        var session = $('#session').val();
        var report_type = $('#report_type').val();

        window.open('/staff/challan-status-excel?school_id='+school+'&dept_id='+department+'&report_type='+$('#report_type').val()+'&session='+session);


    }
  function RequestInstallment(sessid,regno,challanNo,spiName,scd_id){


    $('#sesID').val(sessid);
    $('#scd_id').val(scd_id);
    $('#regNo').val(regno);
    $('#std-detail').html(' '+regno);


    getRequest('/staff/get-challan-user?userID='+regno+'&sessID='+sessid,function(res){
      console.log(res);
      $('#challan-body').html('');
       $.each(res, function(key, value) {
        $('#challan-body').append($("<option></option>")
                          .attr("value", value.buyercodechallanno)
                          .text(value.buyercodechallanno +':'+ value.session_id));
        });
        $('#challan-body').append($("<option></option>")
                          .attr("value", '0')
                          .text('Add New'));
          // if(htmlGen != ''){
          //     $('#challan-body').html(htmlGen);
          // }else{
          //    // $('#challan-body').html('<select><option>No Result Found!</option></select>');
          // }

    });

  }
  function RequestFun(){
    var bi_form = document.querySelector('#cform');
    var fd = new FormData(bi_form);
    //challan-request
    $('#challan_type').removeClass('is-invalid');
    var type       = $("#challan_type").val();
    if(type.trim() == ''){
      $('#challan_type').addClass('is-invalid');
        return false;
    }

    $.ajax({
      method: "post",
      url: '{{url('staff/challan-request')}}',
      data:fd,
      processData:false,
      contentType:false,

    }).done(function(res) {

      if (res.status == true) {
          $('#UpdateMsg').html('Data Updated Succesfully');
          getResults();
          setTimeout(function(){
            $('#challan_type').val('');
            $('#myModalGenerate').modal('hide');
            $("#cform")[0].reset();

            $('#UpdateMsg').html('');
            // location.reload();
            },1000);
      }

    }).fail(function(res) {
      console.log(res);
    });

  }
  function genChallanComm(){

      var url = '/staff/get-communication?session='+$('#session').val();

    var bi_form = document.querySelector('#comform');
     var fd = new FormData(bi_form);

    $.ajax({
      method: "post",
      url: '{{url('staff/get-communication')}}',
      data:fd,
      processData:false,
      contentType:false,

    }).done(function(res) {
      if (res.status == true) {
        $('#description').val('');
        genChallan();
        $('#insertMsg').html('Challan Generate Successfully');
          setTimeout(function(){
            $('#insertMsg').html('');
            },1000);
      }

    }).fail(function(res) {
      console.log(res);
    });
}
  function genChallan(){

    $('#sessionID').val($('#session').val());

    var url = '/staff/generate-challan?schoolID='+$('#school_sel').val()+'&depID='+$('#department').val()+'&session='+$('#session').val()+'&session-for='+$('#session-for').val()+'&schID='+$('#schID').val()+'&enrollment='+$('#enrollment').val()+'&get_type='+$('#record_type').val()+'&due_date='+$('#due_date').val()+'&valid_date='+$('#valid_date').val();

    getRequest(url,function(res){
      console.log(res.data);
      var htmlGen='';
          $.each(res.data,function( key, val ) {
            var srno = parseInt(key+1);
             htmlGen += '<tr>';
              htmlGen += '<td>'+srno+'</td>';
              htmlGen += '<td>'+val.afc_description+'</td>';
              htmlGen += '<td>'+val.afc_entry_username+'</td>';
              htmlGen += '<td>'+val.entry_datetime+'</td>';
              htmlGen += '</tr>';
          });
          if(htmlGen != ''){
              $('#communication-body').html(htmlGen);
          }else{
              $('#communication-body').html('<tr><td colspan="12" style="text-align:center;">No Result Found!</td></tr>');
          }

    });
  }

  // function RequestInstallment(regno,challanNo,spiName,scd_id){
  //   $('#scd_id').val(scd_id);
  //   $('#regNo').val(regno);

  //   $('#std-detail').html(' '+regno);
  // }


  function fun(userID,sessID){

     getRequest('/staff/get-challan?userID='+userID,function(res){
      console.log(res);
      var html = '';
      var html1='';
      var requests_html='';
      var flag=0;
        $.each(res,function( key, value ) {
            var srno = parseInt(key+1);
                if(flag==0){
                  $.each(value.scholarship,function( key, val) {
                      var srnumber = parseInt(key+1);
                      html1 += '<tr>';
                      html1 += '<td>'+srnumber+'</td>';
                      html1 += '<td>'+val.scholarship_name+'</td>';
                      html1 += '<td>'+val.ssd_percentage+'%'+'</td>';
                      html1 += '</tr>';

                  });
                  $.each(value.requests,function( key, val) {

                    console.log(val.student_id);
                      var srnumber_req = parseInt(key+1);
                      requests_html += '<tr>';
                      requests_html += '<td>'+srnumber_req+'</td>';
                      requests_html += '<td>'+val.student_id+'</td>';
                      requests_html += '<td>'+val.request_status+'</td>';
                      requests_html += '<td>'+val.entry_username+'</td>';
                      requests_html += '<td>'+val.entry_datetime+'</td>';
                      requests_html += '<td>'+val.forward_type+'</td>';
                      requests_html += '<td>'+val.log_type+'</td>';
                      requests_html += '</tr>';

                  });

                flag=1;
              }




                      if(srno <=3){
                      html += '<tr>';
                      html += '<td>'+srno+'</td>';
                      // html += '<td>'+value.studentname+'</td>';
                      // html += '<td>'+value.regno+'</td>';
                      html += '<td>'+value.program+'</td>';
                      html += '<td>'+value.session_id+'</td>';
                      html += '<td>'+value.buyercodechallanno+'</td>';
                      html += '<td>'+value.duedate+'</td>';
                      html += '<td>'+value.paidon+'</td>';
                      html += '<td>'+value.totalamountpayable+'</td>';
                      html += '<td><a target="_blank" href="'+url+'/staff/print-challan?id='+value.buyercodechallanno+'">Download</a></td>';
                      html += '</tr>';
                    }else{
                      html += '<tr class="invisible check" id="myDiv">';
                      html += '<td>'+srno+'</td>';
                      // html += '<td>'+value.studentname+'</td>';
                      // html += '<td>'+value.regno+'</td>';
                      html += '<td>'+value.program+'</td>';
                      html += '<td>'+value.session_id+'</td>';
                      html += '<td>'+value.buyercodechallanno+'</td>';
                      html += '<td>'+value.duedate+'</td>';
                      html += '<td>'+value.paidon+'</td>';
                      html += '<td>'+value.totalamountpayable+'</td>';
                      html += '<td><a target="_blank" href="'+url+'/staff/print-challan?id='+value.buyercodechallanno+'">Download</a></td>';
                      html += '</tr>';
                    }

                    $('#std-data').html(value.studentname+' , Reg.No: '+value.regno);

                  });
                  if(html != ''){
                    html+='<tr><td colspan="12" style="text-align:right;" ><i onClick="setDiv()" class="fa fa-plus fa-2x"  id="btn-plus" aria-hidden="true"></i></td></tr>';
                    if(html1 !=''){
                      $('#result-data1').html(html1);
                    }else{
                      $('#result-data1').html('<tr><td colspan="8" style="text-align:center;">No Result Found!</td></tr>');
                    }
                    if(requests_html !=''){
                      $('#result-request-data').html(requests_html);
                    }else{
                      $('#result-request-data').html('<tr><td colspan="8" style="text-align:center;">No Result Found!</td></tr>');
                    }



                      $('#result-data').html(html);
                  }else{
                      $('#result-data').html('<tr><td colspan="12" style="text-align:center;">No Result Found!</td></tr>');
                  }
    });
  }

  function setDiv(){

    var x = document.getElementById("myDiv");
   // alert(x);
    if(x.classList.contains('invisible')){
      $('#btn-plus').addClass('fa fa-minus fa-2x');
      $('.check').removeClass('invisible');
    }else{
      $('#btn-plus').removeClass('fa fa-minus fa-2x');
      $('#btn-plus').addClass('fa fa-plus fa-2x');
      $('.check').addClass('invisible');
    }

  }



  function getRequest(endPoint,successBlock,failBlock){
        $.ajax({
            url: "{{url('/')}}"+endPoint,
            type: "GET",
            dataType: "json",
            success:successBlock
        }).fail(failBlock);
    }
function getDepartments(){
        var school_id = $('#school_sel').val();
        getRequest('/department/get?school_id='+school_id,function(res){
            if(res.status){
                var html = '<option value="">All Dept</option>';
                $.each(res.data,function( key, value ) {
                    html += '<option value="'+value.dept_id+'" >'+value.dept_name+'</option>';
                });
                $('select[name="department"]').html(html);
            }
        },function(res){
            alert('Some Error found!');
        });
    }

    function getResults(){
     /// alert($('#record_type').val());
      var url = '/staff/get-Data?schoolID='+$('#school_sel').val()+'&depID='+$('#department').val()+'&session='+$('#session').val()+'&session-for='+$('#session-for').val()+'&schID='+$('#schID').val()+'&enrollmentNO='+$('#enrollmentNO').val()+'&get_type='+$('#record_type').val()+'&due_date='+$('#due_date').val()+'&valid_date='+$('#valid_date').val()+'&student_status='+$('#student_status').val()+'&record_type='+$('#record_type').val()+'&enrollment='+$('#enrollment').val();
      lastURL = url;
      var disapprove='disapprove';
      var approve='approve';
      var apprbtn='';
        $('#result-body').html('<tr><td colspan="12" style="text-align:center;">Loading...</td></tr>');

        getRequest(url,function(res){
            if(res.status){
              $('#total-record').html(res.message);
                var html = ''
                var disapprove='disapprove';
				        var approve='approve';
                var new_sai_enrollment_no='';
                var count_total_records = 0 ;
                var count_student_records = 0 ;


                $.each(res.data,function( key, value ) {

                    var srno = parseInt(key+1);
                    //var chk_status = ( (value.scd_status == 'Requested')?'background-color:yellow':'background-color:white' )
                    var chk_status = '';
                    // if(value.scd_status == 'Requested'){
                    //   chk_status = 'background-color:#FF850A';
                    // }else if(value.scd_status == 'Forwarded'){
                    //   chk_status = 'background-color:#6495ED';
                    // }else if(value.scd_status == 'Approved'){
                    //   chk_status = 'background-color:#2A683E';
                    // }

                   // if(value.display_type=='challan_wise'){

                      if(parseInt(value.buyercodechallanno)>0){
                        chk_status = 'background-color:#c7f9eb';
                      }else{
                        chk_status = 'background-color:#fcfac1';
                      }


                      // if(value.scd_status=='installment_request' && value.challan_generate=='Allowed'){
                      //   chk_status = 'background-color:#00BFFF';
                      // }else if(value.scd_status=='installment_request' && value.challan_generate=='Requested'){
                      //   chk_status = 'background-color:#2E8B57';
                      // }


                      if(value.challan_generate=='Requested'){
                        chk_status = 'background-color:#25D366';
                      }
                      else if(value.challan_generate=='Allowed'){
                         chk_status = 'background-color:#00BFFF';
                      }


                   // }
                     if(value.sai_enrollment_no!=new_sai_enrollment_no)
                    {
                      new_sai_enrollment_no=value.sai_enrollment_no;
                      var status_request = (value.challan_generate == 'Allowed')?'Sent to FO':value.challan_generate;
                      html += '<tr style="'+chk_status+'">';

                        html += '<td>'+srno+'</td>';
                        html += '<td>'+value.spi_name+' ('+value.sai_enrollment_no+' )</td>';
                        html += '<td>'+status_request+'</td>';
                        html += '<td>'+value.total_pending_challans+'</td>';
                        html += '<td>'+value.cgpa+'</td>';

                       // html += '<td>'+value.fee_type+'</td>';

                        // if(value.display_type== 'challan_wise'){
                        //   html += '<td>'+value.buyercodechallanno+'</td>';
                        // }

                       html += '<td><span class="doit'+value.sai_enrollment_no+'">"doit'+srno+'"</span></td>';
                       html += '<td>'+value.enrolled_students+'</td>';
                       html += '<td>'+value.total_cr+'</td>';
                       // html += '<td style="padding-left: 40px;"><input class="form-check-input" type="checkbox" value="" id="flexCheckIndeterminate"></td>';
					    if('<?=auth()->user()->group_id;?>' !=324){
                        html +=  '<td> <div class="dropdown"><button class="dropbtn">Action</button><div class="dropdown-content"><a data-toggle="modal" data-target="#myModalGenerate"  onclick="RequestInstallment(\''+value.sess_id+'\',\''+value.sai_enrollment_no+'\',\''+value.cd_challan_no+'\' ,\''+value.spi_name+'\',\''+value.scd_id+'\')"  >GENERATE</a><a  onclick="approvedBtn(\''+value.id+'\',\''+disapprove+'\' )"  >DISAPPROVE</a><a  onclick="approvedBtn(\''+value.id+'\',\''+approve+'\' )"  >APPROVE</a></div></div> </td>'; //<button type="button" data-toggle="modal" data-target="#myModalGenerate"  onclick="RequestInstallment(\''+value.sai_enrollment_no+'\',\''+value.cd_challan_no+'\' ,\''+value.spi_name+'\',\''+value.scd_id+'\')"  class="btn btn-primary">Generate</button>
						}
                        html +=  '<td> <button type="button" data-toggle="modal" data-target="#myModal"  onclick="fun(\''+value.sai_enrollment_no+'\',\''+value.sess_code+'\' )" class="btn btn-secondary">Detail</button></td>';
                      //  html +=  '<td> <button type="button"   onclick="approvedBtn(\''+value.scd_id+'\',\''+apprbtn+'\')" class="btn btn-success">Approved</button></td>';
                        html += '</tr>';

                        count_total_records++;


                    }else{
                        console.log('ok');
                    }
                       $("#total_records").html(count_total_records);
                });

                if(html != ''){
                    $('#result-body').html(html);
                }else{
                    $('#result-body').html('<tr><td colspan="12" style="text-align:center;">No Result Found!</td></tr>');
                }

               //count_student_records
               new_sai_enrollment_no='';
                $.each(res.data,function( key, value ) {
                 // console.log(value.sai_enrollment_no);
                //  var doit = 'doit';

                    if(value.sai_enrollment_no!=new_sai_enrollment_no)
                    {
                      $(".doit"+new_sai_enrollment_no).html(count_student_records);
                      new_sai_enrollment_no=value.sai_enrollment_no;

                    if(value.buyercodechallanno>0)
                    {
                      count_student_records=1;
                    }else{
                      count_student_records=0;
                    }


                    }else{
                      count_student_records++;
                    }
                 });




            }
        },function(res){
            alert('Some Error found!');
        });
    }
$(function() {
    $('#topcheckbtn').click(function() {
        $('.form-check-input').prop('checked', this.checked);
    });
});
function ExportToExcel(type, fn, dl) {
    var elt = document.getElementById('tbl_exporttable_to_xls');
    var wb = XLSX.utils.table_to_book(elt, { sheet: "sheet1" });
    return dl ?
      XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }):
      XLSX.writeFile(wb, fn || ('MySheetName.' + (type || 'xlsx')));
 }

function approvedBtn(scd_id,status) {

  sendPostRequest('', '.asdasdas', 'approved-challan-req/'+scd_id+'?status='+status, function(res) {
      if(res.status == true) {
          getResults();
      }
    }, '');

}



</script>

@stop
