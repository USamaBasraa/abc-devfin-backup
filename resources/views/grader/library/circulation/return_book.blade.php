@extends('layouts.app',['pageTitle' => 'Library Fine Management'])

@section('title')
    BNU CMS
@stop
@section('content')
<style>
    
    .blink_me {
  animation: blinker 1s linear infinite;
  color:red;
   font-weight: bold;
}

@keyframes blinker {
  50% {
    opacity: 0;
  }
}
    
</style>

        <div class="card" >
            {{-- <div class="card-header">
                <h3>LIBRARY FINE MANAGEMENT </h3>
            </div> --}}
            <div class="card-header" style="background-color:#D2D6F1; color:#6774CF;display:inline" >
                <!-- <div style="float:left;">
                    <h3 style="font-weight: bold">Circulation</h3>
                </div> -->
                <form enctype="multipart/form-data" method="post" name="search_form"  id="search_form">
                    {{ csrf_field() }}
                 <div style="float:left;padding-left:45px">
                    <input style="width:160px" type="text" class="form-control" id="slip-id" onkeyup="searchBySlip(this)" name="slip-id" placeholder="Search with Slip ID">
                </div> 
                <div style="float:left;padding-left:95px">
                    <input style="width:160px" type="text" class="form-control" id="student-id" onkeyup="searchByUser(this)" name="student-id" onkeyup="bilal()" placeholder="Search with Student ID">
                </div>
                <!-- <div style="float:right;">
                    <button type="button" class="btn btn-primary" onclick="searchByTitle()" >Search</button>
                    <button type="button" class="btn btn-primary" onclick="searchByAll()" >View All</button>
                </div> -->
            </form>
            </div>
            <div class="card-body">  

            <table class="table table-sm  table-bordered">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">User Id</th>
                    <th scope="col">Book Name</th>
                    <th scope="col">Due Date</th>
                    <th scope="col">Return Status</th>
                    <th scope="col">Remarks</th>
                    <th scope="col">Fine</th>
                    <th scope="col">Status</th>
                    <th colspan="2" scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="result-body" ><tr><td colspan="12" style="text-align:center;">No Filter Apply Yet!</td></tr></tbody>
            </table>
  
              
    </div>
</div>
  



  <!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Return Book</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form enctype="multipart/form-data" method="post" name="return-book-form"  id="return-book-form">
        @csrf
      <div  class="form-inline form-group">
        <label for="inputPassword6" >User Name : </label>
        <input type="hidden" id="book-id" name="book-id" class="form-control mx-sm-3" >
        <input type="hidden" id="user-idd" name="user-id" class="form-control mx-sm-3" >
        <input type="hidden" id="issue-date" name="issue-date" class="form-control mx-sm-3" >
        <input type="hidden" id="id" name="id" class="form-control mx-sm-3" >
        <input type="hidden" id="issue-days" name="issue-days" class="form-control mx-sm-3" >
        <input type="hidden" id="accession-no" name="accession-no" class="form-control mx-sm-3" >

        <small style="font-weight:bold;font-size:16px;" id="user-id" class="text-muted" style="padding-left:20px;">
        </small>
  </div>
  <div class="form-row">
    <!-- <div class="form-group col-md-6">
      <label for="inputEmail4">Return Date</label>
      <input type="date" class="form-control date" onchange="finecal()" name="return-date" id="dt" placeholder="Return Date">
    </div> -->
    <div class="form-group col-md-6">
      <label for="inputState">Fine Status</label>
      <select onchange="status(this)" name="fine-status" id="finestatus" class="form-control">
        <option value="" selected>Choose...</option>
        <option value="Paid">Paid</option>
        <option value="UnPaid">UnPaid</option>
        <option value="Partial">Partial Paid</option>
      </select>
    </div> 
    <div class="form-group col-md-6">
      <label for="inputPassword4">Fine</label>
      <input type="number" class="form-control" name="fine" id="fine"  placeholder="Fine">
    </div>
  </div>
  <div class="form-group">
    <label for="inputAddress">Remarks</label>
    <input type="text" class="form-control" name="remarks" id="inputAddress" placeholder="Book Remarks">
  </div>
  <div class="form-row">
    <div class="form-group col-md-12">
      <label for="inputCity">Book Name</label>
      <input type="text" class="form-control" name="book-name" readonly id="book-name">
    </div>
    <!-- <div class="form-group col-md-4">
      <label for="inputState">Book Status</label>
      <select onchange="status(this)" name="book-status" id="bookstatus" class="form-control">
        <option value="" selected>Choose...</option>
        <option value="Received">Received</option>
        <option value="Issued">Reissued</option>
      </select>
    </div> -->
    <div class="form-group col-md-4" id="issue-div" style="display:none">
      <label for="inputEmail4">Issue Date</label>
      <input type="date" class="form-control "  name="issue-date" id="issue" placeholder="Issue Date">
    </div>
  </div>
  <!-- <div class="form-group">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" id="gridCheck">
      <label class="form-check-label" for="gridCheck">
        Check me out
      </label>
    </div>
  </div> -->
  <!-- <button type="button" class="btn btn-primary">Fine Calculate</button> -->
 
</form>
      </div>

      <div class="modal-footer">
      <span id="bookissued" style="color:green"></span>  
       <span id="booklimit" style="color:red"></span>  
      <!-- <div style="float:left;color:red" id="result-body" ></div>  -->
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" onclick="fineupdate()" id="btn"class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
@stop


@section('javascript')
<script type="text/javascript">

var data='';
function searchBySlip(val){
  var text=val.value;
  //alert(text);

        var formData = new FormData();

        formData.append("slipID", text);
            $.ajax({
                type:'GET',
                url: '{{url('library/get-user')}}/'+text,
                data:formData,
                processData:false,
                contentType:false,
                success:function(res){
                  var html='';
                  var fine_status='';
                  data=res.data;
                  var date = new Date();
                  var day = date.getDate();
                  var month = date.getMonth() + 1;
                  var year = date.getFullYear();
                  var arr=[year, month, day].join('-');
                  $.each(res.data,function( key, value ) {
                    var srno = parseInt(key+1);
               if(value.lc_fine_status=='undefined'){
                fine_status=0;
               }else{
                fine_status=value.lc_fine_status;
               }

               if(value.lc_desc == undefined){
                
                dsc_status=' ';
               }else{
                dsc_status=value.lc_fine_status;
               }
                  //const diffTime = Math.abs(arr - value.lc_due_date);
                  //alert(diffTime);
                    html += '<tr>';
                    html += '<td>'+srno+'</td>';
                    html += '<td style="color:#0B7A75" >'+value.sai_enrollment_no+'</td>';
                    html += '<td align="left" style="color:#A30000" >'+value.library_title+'</td>';
                    html += '<td align="center" style="color:#582C4D">'+value.lc_issue_date+'</td>';
                    html += '<td align="center" style="color:#6B5E62" >'+value.lc_book_status+'</td>';
                    html += '<td align="center" style="color:#19535F" >'+dsc_status+'</td>';
                    html += '<td align="center" style="color:#7B2D26" >'+value.lc_fine_total+'</td>';
                    html += '<td align="center" style="color:#0B7A75">'+ value.lc_fine_status+'</td>';                    
                    html += '<td style="text-align:center;" > <button  data-toggle="modal" data-target="#exampleModalCenter" style="text-align:center;color:#F95700FF;font-weight:bold" class="blink_me"  onclick="checkval('+value.id+')"><img   height="30" src="{{asset('images/comments.png')}}" /></button></td>';
                    html += '</tr>';
                });
                $('#result-body').html(html);
                },
                complete: function(){
                }
            });

}

function searchByUser(val){
  var text=val.value;
  //alert(text);

        var formData = new FormData();

        formData.append("slipID", text);
            $.ajax({
                type:'GET',
                url: '{{url('library/get-user-id')}}/'+text,
                data:formData,
                processData:false,
                contentType:false,
                success:function(res){
                  var html='';
                  var fine_status='';
                  var sum=0
                  data=res.data;
                  var date = new Date();
                  var day = date.getDate();
                  var month = date.getMonth() + 1;
                  var year = date.getFullYear();
                  var arr=[year, month, day].join('-');
                  $.each(res.data,function( key, value ) {
                    var srno = parseInt(key+1);
                    console.log(value.lc_fine_status);
               if(value.lc_fine_status == undefined){
                
                fine_status=' ';
               }else{
                fine_status=value.lc_fine_status;
               }
               if(value.lc_desc == undefined){
                
                dsc_status=' ';
               }else{
                dsc_status=value.lc_fine_status;
               }
                sum=parseInt(value.lc_fine_total)+sum;
                  //const diffTime = Math.abs(arr - value.lc_due_date);
                  //alert(diffTime);
                    html += '<tr>';
                    html += '<td>'+srno+'</td>';
                    html += '<td style="color:#0B7A75" >'+value.sai_enrollment_no+'</td>';
                    html += '<td align="left" style="color:#A30000" >'+value.library_title+'</td>';
                    html += '<td align="center" style="color:#582C4D">'+value.lc_issue_date+'</td>';
                    html += '<td align="center" style="color:#6B5E62" >'+value.lc_book_status+'</td>';
                    html += '<td align="center" style="color:#19535F" >'+dsc_status+'</td>';
                    html += '<td align="center" style="color:#7B2D26" >'+value.lc_fine_total+'</td>';
                    html += '<td align="center" style="color:#0B7A75">'+ fine_status+'</td>';                    
                    html += '<td style="text-align:center;" > <button  data-toggle="modal" data-target="#exampleModalCenter" style="text-align:center;color:#F95700FF;font-weight:bold" class="blink_me"  onclick="checkval('+value.id+')"><img   height="30" src="{{asset('images/comments.png')}}" /></button></td>';
                    html += '</tr>';
                });
                html += '<tr>';
                html += '<td colspan="6" align="right"  style="color:#582C4D;font-weight:bold">Total Fine </td>';
                html += '<td colspan="6">'+sum+'</td>';
                html += '</tr>';
                $('#result-body').html(html);
                },
                complete: function(){
                }
            });

}


function fineupdate(){

  var bi_form = document.querySelector('#return-book-form');
            var fd = new FormData(bi_form);
            $.ajax({
                type:'POST',
                url: '{{url('library/user-fine-update')}}',
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){

        if(data.status){
            $('#booklimit').html('');
            $('#bookissued').html(data.message); 
            setTimeout(function(){  
                $('#bookissued').html(''); 
                $('#exampleModalCenter').modal('hide');
                $("#return-book-form")[0].reset();
                location.reload();
            }, 4000);
     
           }else{
            $('#bookissued').html('');
            $('#booklimit').html(data.message);
            setTimeout(function(){ 
                $('#booklimit').html('');
                $('#exampleModalCenter').modal('hide');
            }, 4000);
            
            
  
            
           }
                },
                complete: function(){
                }
            });
}


function status(sel){
    var value = sel.value;
  
    if(value == 'Issued'){
        $('#issue-div').show();
    }else{
        $('#issue-div').hide();
    }if(value=='Partial'){
      document.getElementById('fine').readOnly
                        = false;
    }
}
function finecal(){
 
    var date = new Date($('#dt').val());
    var day = date.getDate();
  var month = date.getMonth() + 1;
  var year = date.getFullYear();
  var arr=[year, month, day].join('/');
  var issueDate=$('#issue-date').val();

var date1 = new Date(issueDate);
var date2 = new Date(arr);

const diffTime = (date2 - date1);
const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24)); 
    if(diffDays>0){
    const fine=diffDays*10;
    $('#fine').val(fine);
    $('#dt').removeClass('is-invalid');
    }else{
        $('#fine').val(0);
        $('#dt').removeClass('is-invalid');
        
    }


    // if(diffDays < 0){
    //     alert('You Cannot enter less then Issue Date');
    //     $('#dt').addClass('is-invalid');
    //     $('#fine').val(0);
    // }

console.log(diffDays + " days");

}


  function checkval(id){

    $.each(data,function( key, value ) {
      var srno = parseInt(key+1);
      if(id==value.id){
          console.log(data);
          $('#book-id').val(value.library_books_id);
          $('#book-name').val(value.library_title);
          $('#user-id').html(value.sai_enrollment_no);
          $('#fine').val(value.lc_fine_total);
          $('#id').val(value.id);
          $('#user-idd').val(value.sai_enrollment_no);
          $('#issue-date').val(value.lc_due_date);
          $('#issue-days').val(value.lc_issue_no_of_days);
          $('#accession-no').val(value.lc_accession_no);
      }
    });
  }
  $("#btn1").click(function(e){
            e.preventDefault();
            var book=$('select[name=book-status] option').filter(':selected').val()
                
                    var date = new Date($('#dt').val());
                    var day = date.getDate();
                    var month = date.getMonth() + 1;
                    var year = date.getFullYear();
                    var arr=[year, month, day].join('/');

                    // if(isNaN(arr)){
                    //     alert(arr);
                    //     $('#dt').addClass('is-invalid');
                    //     $('#result-body').html('Please enter Return Date');
                    //     return false;
                    // }else{
                    //     alert('dd11');
                    //     $('#dt').removeClass('is-invalid');
                    //     $('#result-body').html('');
                    // }
                    if($('#issue').val() == '' && book =='Issued'){
                      $("#issue").focus();
                      $("#issue").css("border","1px solid red");
                      return false;
                    }
                   
                if(book==''){
                    $('#bookstatus').addClass('is-invalid');
                    return false;
                }else if(book=='reissue'){
                    var issuedate = new Date($('#issue').val());
                    var day = issuedate.getDate();
                    var month = issuedate.getMonth() + 1;
                    var year = issuedate.getFullYear();
                    var arr=[year, month, day].join('/');
                    // if(isNaN(arr)){
                    //     $('#issue').addClass('is-invalid');
                    //     $('#result-body').html('Please enter Issue Date');
                    //     return false;
                    // }
                }
            

            var bi_form = document.querySelector('#return-book-form');
            var fd = new FormData(bi_form);
            $.ajax({
                type:'POST',
                url: '{{url('library/user-return-book')}}',
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){

        if(data.status){
            $('#booklimit').html('');
            $('#bookissued').html(data.message); 
            setTimeout(function(){  
                $('#bookissued').html(''); 
                $('#exampleModalCenter').modal('hide');
                $("#return-book-form")[0].reset();
                location.reload();
            }, 4000);
     
           }else{
            $('#bookissued').html('');
            $('#booklimit').html(data.message);
            setTimeout(function(){ 
                $('#booklimit').html('');
                $('#exampleModalCenter').modal('hide');
            }, 4000);
            
            
            loadDetails();
            
           }
                },
                complete: function(){
                }
            });

        });

      
        
</script>
@stop