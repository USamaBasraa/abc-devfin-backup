@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<style>
.invisible { display: none; }
</style>
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
  <form action="{{url('staff/export-to-excel?print=accounts')}}" method="post" enctype="multipart/form-data" id="filterFormOne"  >
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
                      <!-- <option value="need_base">Need Base</option>
                       <option value="all">All Scholarships</option>
                        <option value="merit_base">Merit Base</option>
                        <option value="bss_emp">BSS EMPLOYEE</option>
                        <option value="bss_emp_ward">BSS EMP WARD</option>
                        <option value="bnu_emp">BNU EMP</option>
                        <option value="bnu_emp_ward">BNU EMP WARD</option>
                        <option value="nest">NEST</option>
                        <option value="umisaa">UMISAA</option>
                        <option value="shehla_siagol">SHEHLA SIAGOL</option>-->
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
                    <label for="department">Record Type</label>
                    <select class="form-control select2-show-search"  data-placeholder="Choose one (with searchbox)"  name="record_type" id="record_type" class="form-control"  >
                       <option value="Pending">Pending</option>
                       <option value="student_wise">Student Wise</option>
                       <option value="challan_wise">Challan Wise</option>
                       <option value="Completed">Request Log</option>
                  </select>
                </div>
                <div class="form-group col-sm-3">
                        <label for="getResultsByProgDept">&nbsp;&nbsp;&nbsp;&nbsp;</label>
                        <button type="button" class="btn btn-primary"  style="margin-top: 25px;"  onclick="getResults();" >Get Result</button>
                </div>

            </div>
		</form>
         <!-- color list start------------------------------- -->
    <ul>
    <li>
        <div class="input-color">
            <input style="width: 100%;" type="text" value="The request has to RO" readonly/>
            <div class="color-box" style="background-color: #FF850A;"></div>
            <!-- Replace "#FF850A" to change the color -->
        </div>
    </li>
    <li>
        <div class="input-color">
            <input style="width: 100%;" type="text" value="The request has forward to accounts" readonly />
            <div class="color-box" style="background-color: #6495ED;"></div>
            <!-- Replace "navy" to change the color -->
        </div>
    </li>
    <li>
        <div class="input-color">
            <input style="width: 100%;" type="text" value="The Request has been Approved" readonly />
            <div class="color-box" style="background-color: #2A683E;"></div>
            <!-- Replace "navy" to change the color -->
        </div>
    </li>
</ul>

<!-- color list ended ------------------------------------------- -->
    <div style="display:inline">
      <!-- <div style="float:left">
            <button type="button" class="btn btn-danger" onclick="getPDF()" style="float:left">
                  <i  class="fa fa-file-excel-o" aria-hidden="true">Challan Log</i>
            </button>
      </div> -->
      <div style="float:right">
        <!-- <span id="total-record"> </span> -->
        
      <button class="btn btn-primary" data-toggle="modal" data-target="#myModalCommunication"  onclick="genChallan()" >Generate Challan</button>
      <i class="fa fa-bell fa-2x" aria-hidden="true"></i><span id="getChallanRequestCount" class="badge sec badge-danger"></span>
      </div>
    </div>
	</div>
    <div class="card-body table-responsive">
		<!-- <table class="table table-bordered text-nowrap" id="example2">
			<thead>
			<tr>
				<th class="wd-15p border-bottom-0">Sr.</th>
				<th class="wd-15p border-bottom-0">Student Name</th>
				<th class="wd-15p border-bottom-0">Roll No</th>
				<th class="wd-15p border-bottom-0">School</th>
				<th class="wd-15p border-bottom-0">Challan #</th>
				<th class="wd-15p border-bottom-0">Action</th>
				<th class="wd-15p border-bottom-0">dd</th>
				<th class="wd-15p border-bottom-0">Detail</th>
			  </tr>
			</thead>
			  <tbody id="result-body" ></tbody>
		  </table> -->
     <form  method="post" enctype="multipart/form-data" class="m-form m-form--fit m-form--label-align-right"  id="update_form">
        @csrf
      <table class="table table-bordered data-table" id="tbl_exporttable_to_xls">
        <thead>
            <tr>
                <th>Sr.</th>
				<th>Req.ID</th>
                <th>Student Name</th>
                <th>Reg No</th>
                <th>Status</th>
                <th>Fee Type</th>
                <th><input  type="checkbox" value="" style="margin-left: 10px;" id="topcheckbtn"></th></th>
                <th class="excludeExport" width="100px">Details</th>
                <!-- <th class="excludeExport" width="100px">Action</th> -->
            </tr>
        </thead>

        <tbody id="result-body" ></tbody>

    </table>
</form>
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
      <table class="table table-bordered"  >
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
                <th  style="font-weight:bold !important">Due date</th>
                <th  style="font-weight:bold !important">Paid on</th>
                <th  style="font-weight:bold !important">Amount</th>
                <th  style="font-weight:bold !important">Action</th>
              </tr>
        </thead>
        <tbody id="result-data" ><tr><td colspan="12" style="text-align:center;">No Filter Apply Yet!</td></tr></tbody>
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
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">No of Challan:</label>
            <input type="hidden" id="scd_id" name="scd_id" class="form-control"/>
            <input type="hidden" id="regNo" name="regNo" class="form-control"/>
            <select  name="challanNo" id="challanNo" class="form-control"  >
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
              </select>
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Description:</label>
            <textarea class="form-control" name="desc-text" id="desc-text"></textarea>
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
             <div class="col-md-4">
               <label for="message-text" class="col-form-label">Request Ids:</label>
              <textarea class="form-control" name="request_ids" id="request_ids" rows="4"></textarea>
            </div>
            <div class="col-md-4">
               <label for="message-text" class="col-form-label">Description:</label>
              <textarea class="form-control" name="description" id="description" rows="4"></textarea>
            </div>
          </div>
        </form>
        <div class="modal-footer">
            <span style="color:green" id="insertMsg"></span>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" onclick="genChallanComm();" class="btn btn-primary">Submit</button>
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
  function genChallanComm(){

      var url = '/staff/get-communication?session='+$('#session').val();

    var bi_form = document.querySelector('#comform');
     var fd = new FormData(bi_form);
     updateChallan();
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
                location.reload(true);
            },2000);
      }

    }).fail(function(res) {
      console.log(res);
    });
}

function updateChallan(){



var bi_form = document.querySelector('#update_form');
 var fd = new FormData(bi_form);

$.ajax({
  method: "post",
  url: '{{url('accounts/update-record')}}',
  data:fd,
  processData:false,
  contentType:false,

}).done(function(res) {
  if (res.status == true) {
    $('#description').val('');
    getResults();
    $('#insertMsg').html('Challan Generate Successfully');
      setTimeout(function(){
        $('#insertMsg').html('');
        },2000);
  }

}).fail(function(res) {
  console.log(res);
});

}


  function genChallan(){

    $('#sessionID').val($('#session').val());

    var url = '/staff/generate-challan?schoolID='+$('#school_sel').val()+'&depID='+$('#department').val()+'&session='+$('#session').val()+'&session-for='+$('#session-for').val()+'&schID='+$('#schID').val()+'&enrollment='+$('#enrollment').val()+'&get_type='+$('#record_type').val()+'&due_date='+$('#due_date').val()+'&valid_date='+$('#valid_date').val();

    getRequest(url,function(res){

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

        function getPDF(){
          //alert(lastURL+'&result-for-account=print');
        window.open(lastURL+'&result-for=print');

    }


  function RequestInstallment(regno,challanNo,spiName,scd_id){
    $('#scd_id').val(scd_id);
    $('#regNo').val(regno);

    $('#std-detail').html(' '+regno);
  }
  function RequestFun(){
    var bi_form = document.querySelector('#cform');
    var fd = new FormData(bi_form);
    //challan-request

    $.ajax({
      method: "post",
      url: '{{url('staff/challan-request')}}',
      data:fd,
      processData:false,
      contentType:false,

    }).done(function(res) {
      // alert(res.status);
      // return false;
      // console.log(res);
      if (res.status == true) {

          $('#UpdateMsg').html('Data Updated Succesfully');
          setTimeout(function(){
            $('#myModalGenerate').modal('hide');
            $("#cform")[0].reset();
            getResults();
            $('#UpdateMsg').html('');
            // location.reload();
            },1000);
      }

    }).fail(function(res) {
      console.log(res);
    });

  }

  function fun(userID,sessID){

     getRequest('/staff/get-challan?userID='+userID,function(res){
      console.log(res);
      var html = '';
      var html1='';
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
              $('#result-data').html(html);
          }else{
              $('#result-data').html('<tr><td colspan="12" style="text-align:center;">No Result Found!</td></tr>');
          }
    });
  }

  function setDiv(){

    var x = document.getElementById("myDiv");
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

	 var url = '/accounts/get-Data?schoolID='+$('#school_sel').val()+'&depID='+$('#department').val()+'&session='+$('#session').val()+'&session-for='+$('#session-for').val()+'&schID='+$('#schID').val()+'&enrollment='+$('#enrollment').val()+'&get_type='+$('#record_type').val()+'&due_date='+$('#due_date').val()+'&valid_date='+$('#valid_date').val()+'&page-from=accounts';

      lastURL = url;
        $('#result-body').html('<tr><td colspan="12" style="text-align:center;">Loading...</td></tr>');
        getRequest(url,function(res){
            if(res.status){
              $('#total-record').html(res.message);
                var html = '';
				 var disapprove='disapprove';
				  var approve='approve';
                   var apprbtn='';
                $.each(res.data,function( key, value ) {
                    var srno = parseInt(key+1);
                    //var chk_status = ( (value.scd_status == 'Requested')?'background-color:yellow':'background-color:white' )
                    var chk_status = '';
                    if(value.scd_status == 'Requested'){
                      chk_status = 'background-color:yellow';
                    }else if(value.scd_status == 'Forwarded'){
                      chk_status = 'background-color:#6495ED';
                    }else if(value.scd_status == 'Approved'){
                      chk_status = 'background-color:#2A683E';
                    }
                    html += '<tr style="'+chk_status+'">';
                    html += '<td>'+srno+'</td>';
					 html += '<td>'+value.scd_id+'</td>';
                    html += '<td>'+value.spi_name+'</td>';
                    html += '<td>'+value.sai_enrollment_no+'</td>';
                    html += '<td>'+value.challan_type+'</td>';
                    html += '<td>'+value.fee_type+'</td>';
                    html += '<td style="padding-left: 40px;"><input class="form-check-input" id="['+value.scd_id+']" type="checkbox" name="checkval[]" value="'+value.scd_id+'"  id="flexCheckIndeterminate"></td>';
                    // html += '<td><button type="button" data-toggle="modal" data-target="#myModal" onclick="fun()"class="btn btn-secondary">Details</button></td>';

                    // html += '<td><button type="button" data-toggle="modal" data-target="#myModal" onclick="fun()"class="btn btn-secondary">Details</button></td>';
                   // html +=  '<td> <div class="dropdown"><button class="dropbtn">Action</button><div class="dropdown-content"><a data-toggle="modal" data-target="#myModalGenerate"  onclick="RequestInstallment(\''+value.sai_enrollment_no+'\',\''+value.cd_challan_no+'\' ,\''+value.spi_name+'\',\''+value.scd_id+'\')"   >GENERATE</a><a  onclick="approvedBtn(\''+value.id+'\',\''+disapprove+'\' )" >DISAPPROVE</a></div></div> </td>'; //<button type="button" data-toggle="modal" data-target="#myModalGenerate"  onclick="RequestInstallment(\''+value.sai_enrollment_no+'\',\''+value.cd_challan_no+'\' ,\''+value.spi_name+'\',\''+value.scd_id+'\')"  class="btn btn-primary">Generate</button>
                    html +=  '<td> <button type="button" data-toggle="modal" data-target="#myModal"  onclick="fun(\''+value.sai_enrollment_no+'\',\''+value.sess_code+'\' )" class="btn btn-secondary">Detail</button></td>';
                    // html +=  '<td> <button type="button"   onclick="approvedBtn(\''+value.scd_id+'\',\''+apprbtn+'\' )" class="btn btn-success">Approved</button></td>';
                    html += '</tr>';
                });
                if(html != ''){
                    $('#result-body').html(html);
                }else{
                    $('#result-body').html('<tr><td colspan="12" style="text-align:center;">No Result Found!</td></tr>');
                }
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

function approvedBtn(scd_id) {

  sendPostRequest('', '.asdasdas', 'approved-challan-req/'+scd_id, function(res) {
      if(res.status == true) {
          getResults();
      }
    }, '');

}



</script>

@stop
