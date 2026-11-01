@extends('layouts.app',['pageTitle' => 'Circulation'])

@section('title')
    BNU CMS
@stop
@section('content')









<style type="text/css">
    div{
        border:0px solid black;
    }

    .headingtd {
		border:0px solid #000;
		background-color:#999;
		font-weight:bold;
	}
	.cntr {
		text-align:center;
	}
    .fined{
        color:red;
        animation:blinkingText 1.2s infinite;
    }
    @keyframes blinkingText{
        0%{     color: red;    }
        49%{    color: red; }
        60%{    color: transparent; }
        99%{    color:transparent;  }
        100%{   color: red;    }
    }
    

    .dropdown-item.active, .dropdown-item:active {
    text-decoration: none;
    background-color: #c0c7f5;
}

.col-sm-3 a:hover {
    /* border: 1px solid #ff4647; */
    /* border-radius: 100% 60% / 30% 10%; */
    /* background: linear-gradient(rgba(56, 123, 131, 0.7), rgba(56, 123, 131, 0.7)); */
    background-color: #c0c7f5;
    color: #fffff;
}



</style>
<?php
$bookStatus = array("Active","Pending","Update");  
?>
   
<div class="row">
    <div class="col-12 col-sm-12 col-lg-12">
        <div class="card" style="margin-top:15px; padding:0px;" >
            <div class="card-header" style="background-color: #D2D6F1;color:#6774CF;display:inline !important" >
                <div style="float:left;"> 
                  <h3>  Circulation </h3>
                </div>
           
            <div style="float:right;"> 
                <button type="button" class="btn btn-primary" onclick="catalogView()" >Catalog</button>            
                {{-- <button type="button" class="btn btn-primary" onclick="homeView()" >Home</button>  --}}
            </div>
        </div>

        <div class="card-body">
            <form enctype="multipart/form-data" action="#" onsubmit="event.preventDefault(); autoSearch(this);" method="post" id="filterForm" >
                <div class="row">
                    <div class="col-2 col-sm-2 col-lg-2 form-group" style="text-align: right">
                        <label>Filters</label>
                    </div>
                    <div class="col-3 col-sm-3 col-lg-3 form-group">
                        <input type="hidden" value="<?php echo (isset($roll_no)) ? $roll_no : '';?>" id="roll_no" >
                        <input type="hidden" id="chkFine">
                        <input class=" form-control" type="text"  id="sai_enrollment_no"  autocomplete="off" name="username" placeholder="Search By Username" value="<?php echo (isset($roll_no)) ? $roll_no : '';?>" style="border: 1px solid blue;">
                    </div>

                    <div class="col-3 col-sm-3 col-lg-3 form-group"> 
                        <input class=" form-control" type="text" id="accesstion_user" name="accesstion_user" placeholder="Search By Book Accession #" style="border: 1px solid blue;" >
                    </div>

                    <div class="col-3 col-sm-3 col-lg-3 form-group">
                        <button type="submit" class="btn btn-primary" id="user-search-btn" >Load Details</button>
                    </div>
                </div>
            </form>
        </div> <!-- card body end  -->

        <div id="filter_message" class="card-footer alert alert-danger" role="alert" style="margin-bottom:0px; display:none;" >
            
        </div>

        </div> 
    </div> <!-- Column End  -->
</div>
<!-- Profile Info Start -->
<div class="card" style="margin-top:15px; padding:0px;" >
    <div class="card-header" style="background-color:#D2D6F1; color:#6774CF"> 
        <h3>User Profile</h3>
    </div> 
        <div class="card-body" style="padding:0px;">
            <div class="tab-pane fade show active" id="nav-user-detail" role="tabpanel" aria-labelledby="nav-home-tab">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align: center;font-weight:bold" width="200">User ID</th>
                            <th style="text-align: center;font-weight:bold" width="340">User Name</th>
                            <th style="text-align: center;font-weight:bold" width="340">Student Status</th>
                            <th style="text-align: center;font-weight:bold" width="340">Picture</th>
                        </tr>
                    </thead>
                    <tbody id="user_profile_detail"></tbody>
                </table>
            </div>
        </div>
</div>
<!-- Profile Info End -->
<!-- User Issue Books Start -->
<div class="card"  style="margin-top:15px"  >
<div class="card-header" style="background-color:#D2D6F1; color:#6774CF">
    <h3>User Issued Book</h3>
</div>
<div class="card-body" style="padding:0px;" >
    <!--  Issued Book  -->
    <!-- <div class="alert alert-dark" role="alert"> <b>User Issued Book</b> </div> -->
    <table class="table table-bordered" >
    <thead>
    <tr>
        <th style="text-align: center;font-weight:bold" width="400">Book Title</th>
        <th style="text-align: center;font-weight:bold" width="340">Book Auhtor</th>
        <th style="text-align: center;font-weight:bold" width="200">Accession #</th>
        <th style="text-align: center;font-weight:bold" width="250">Author Mark</th>
        <th style="text-align: center;font-weight:bold" width="150">DDC</th>
        <th style="text-align: center;font-weight:bold" width="200">Issue Date</th>
        <th style="text-align: center;font-weight:bold" width="200">Due Date</th>
        <th style="text-align: center;font-weight:bold" width="200">Late Days</th>
        <th style="text-align: center;font-weight:bold" width="200">Return date</th>
        <th style="text-align: center;font-weight:bold" width="100">Fine</th>
        <th style="text-align: center;font-weight:bold" width="340">Status</th>
    </tr>
    </thead>
    <tbody id="user_issued_book_history_table">

    </tbody>
    </table>
</div>
</div>
<!-- User Issue Books End -->
<div class="card" style="margin:20px 0px;">

    <div class="card-header" style="background-color:#D2D6F1; color:#6774CF;display:inline"> 
        <form  onsubmit="event.preventDefault(); searchByAccession();" method="post" name="search_form"  id="search_form">
            {{ csrf_field() }}
        <div style="float:left;">
            <h3>Book To Be Issue</h3>
        </div>
        <div style="float:right;font-weight: bold;color:white">
        <!-- onkeyup="searchByAccession()" -->
                <input style="width:160px" type="text" class="form-control scanner_search" id="searchAccession" name="searchAccession"  placeholder="Search with Accession NO">
        </div>
        </form>
    </div>
    <div class="card-footer" id="circulation-msg" style="display: none; color:red;" ></div>
    <div class="card-body" style="padding:0px">
        <form action="#" method="post" id="bookCartForm" onsubmit="return submitBookCartItme();"  >
        <input type="hidden" name="username" value="" id="cart_username">
            <table class="table table-bordered">
                <thead>
                    <tr>
                    <th style="text-align: center;font-weight:bold" width="340">Book Title</th>
                    <th style="text-align: center;font-weight:bold" width="340">Book Auhtor</th>
                    <th style="text-align: center;font-weight:bold" width="200">Accession #</th>
                    <th style="text-align: center;font-weight:bold" width="200">DDC #</th>
                    <th style="text-align: center;font-weight:bold" width="200">Author Mark</th>
                    <th style="text-align: center;font-weight:bold" width="340">Status</th>
                    </tr>
                </thead>
                <tbody id="cartItems_view"></tbody>
            </table>
        </form>
    </div>

    <div class="card-footer" style="display: none;" id="cart-error-msg"  >
        
    </div>
    
    <div class="card-footer"  >
        <button style="display:none; width:100%;" id="issueBtn" type="button" class="btn btn-primary" onclick="submitBookCartItme();" >Issue Book</button>
    </div>
</div>
</div>
<!--- column end --> 
<div id="showbooks" style="margin-top:20px"></div>
<span id="bookissuedmain" style="color:green"></span>
<span id="booklimitmain" style="color:red"></span>
<!-- Profile Status Modal -->
   <div class="modal fade" id="profile-status-popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">
                 User Full Name 
            </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered"  align='center' cellspacing="0" >
                <tr>
                    <input type="hidden" id="sisuserUsername">
                    <th style="text-align: center" width="340">User Profile</th>
                    <th style="text-align: center" width="340">Library Status</th>                    
                </tr>
                   <?php
                       //  $index = 1;
                    // foreach($view_profile as $row) {
                    ?>
                    <tr>
                       
                        <td align="left"><a style="color:black;cursor:pointer" onclick="reportPDF()" >Click Here to Generate PDF</a></td>
                        <td align="left">
                            <select name="library_status" id="library_status" onchange="updateStatus()" class=" form-control">
                                <option value="">--Select Library--</option>
                                <option value="Active" >Active</option>
                                <option value="Block" >Block</option>
          
                            </select>
                        </td>
                    </tr>
                    <?php
                 //   $index++;
                //    }//foreach
                    ?>
                </table>
        </div>

        <div class="modal-footer">
        <span id="bookissued" style="color:green"></span>
         <span id="booklimit" style="color:red"></span>
         <span id="libraryStatusMsg" style="color:green"></span>
        <!-- <div style="float:left;color:red" id="result-body" ></div>  -->
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          {{-- <button type="button" onclick="saveChanges()"  id="btn"class="btn btn-primary">Save changes</button> --}}
        </div>
      </div>
    </div>
  </div>
<!-- Profile Status Modal End -->
<!----   Return Book Modal Start   ----->
<div class="modal fade" id="returnBookModal" tabindex="-1" role="dialog" aria-labelledby="returnBookModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Return Book</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data" method="post" name="return-book-form" id="return-book-form">
                    @csrf
                    <div class="form-inline form-group">
                        <label for="inputPassword6" id="return-username">User Name : </label>
                        <input type="hidden" id="book-id" name="book-id" class="form-control mx-sm-3">
                        <input type="hidden" id="user-idd" name="user-id" class="form-control mx-sm-3">
                        <input type="hidden" id="issue-date" name="issue-date" class="form-control mx-sm-3">
                        <input type="hidden" id="id" name="id" class="form-control mx-sm-3">
                        <input type="hidden" id="issue-days" name="issue-days" class="form-control mx-sm-3">
                        <input type="hidden" id="accession-no" name="accession-no" class="form-control mx-sm-3">

                        <small style="font-weight:bold;font-size:16px;" id="user-id" class="text-muted" style="padding-left:20px;">
                        </small>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <input type="hidden" id="r_date" name="r_date" value="">
                            <label for="inputEmail4">Return Date</label>
                            <input type="hidden" id="lc_due_date">

                            <input type="date" class="form-control date" value="<?php echo date('Y-m-d'); ?>" onchange="fineCalOnChange(this)" name="return-date" id="dt" placeholder="Return Date">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputPassword4">Fine</label>
                            <input type="number" class="form-control" name="fine" id="fine" readonly placeholder="Fine">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputAddress">Remarks</label>
                        <input type="text" class="form-control" name="remarks" id="inputAddress" placeholder="Book Remarks">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="inputCity">Book Name</label>
                            <input type="text" class="form-control" name="book-name" readonly id="book-name">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputState">Book Status</label>
                            <select onchange="status(this)" name="book-status" id="bookstatus" class="form-control">
                                <option value="" selected>Choose...</option>
                                <option value="Received">Received</option>
                                <option value="Reissued">Reissued</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4" id="issue-div" style="display:none">
                            <label for="inputEmail4">Issue Date</label>
                            <input type="date" class="form-control " name="issue-date" id="issue" placeholder="Issue Date">
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <span id="bookissuedpop" style="color:green"></span>
                <span id="booklimitpop" style="color:red"></span>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" onclick="saveChanges()" id="btn" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!----   Return Book Modal End   ----->
@stop
@section('javascript')

<script src="<?=asset('js/bootstrap-autocomplete.js')?>"></script>



<script type="text/javascript">
$( document ).ready(function() {
    if($('#roll_no').val() != ''){
        autoSearch();
    }
   
});
var url = "<?= url('/') ?>";
var BooksToBeIssuedArr =  [];

function reportPDF(){
    var username =   $('#sisuserUsername').val();
    window.open( "{{URL::to('library/get-user-detail-pdf?username=')}}"+username, '_blank');
//window.location.href = "{{URL::to('library/get-user-detail-pdf?username=')}}"+username;

}

function sendGetRequest(endPoint, successBlock) {
    $('#exam_time_alert').hide();

    $.ajax({
        method: "GET",
        url: url + '/api/' + endPoint,
        headers: {
            Authorization: 'Bearer {{ auth()->user()->api_token }}',
            Accept: 'application/json'
        }
    }).done(successBlock).fail(function(res) {
        console.log(res);
        var errorMsg = '';
        if (res.status == 0) {
            errorMsg = 'Internet not found!';
        } else if (res.status == 500) {
            errorMsg = '500 : Server error. Please contact with ITRC Team!';
        } else if (res.status == 401) {
            errorMsg = '401 : Your session is out. Please login again!';
        }
        if (errorMsg != '') {
            $('#exam_time_alert').show();
            $('#exam_time_alert').html(errorMsg);
            setTimeout(function() {
                $('#exam_time_alert').hide();
            }, 200000);
        }
    });
}
function catalogView(){


window.location.href = "{{URL::to('library/catalog')}}";

}
function homeView(){


window.location.href = "{{URL::to('/teacher/courses')}}";

}

function sendPostRequest(endPoint, successBlock, postData, failBlock) {
    $.ajax({
        method: "POST",
        url: url + '/api/' + endPoint,
        data: postData,
        processData: false,
        contentType: false,
        headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
                Accept: 'application/json'
            }
    }).done(successBlock).fail(function(res) {


        if (failBlock != null) {
            failBlock();
        } else {
            console.log(res);
            var errorMsg = '';
            if (res.status == 0) {
                errorMsg = 'Internet not found!';
            } else if (res.status == 500) {
                errorMsg = '500 : Server error. Please contact with ITRC Team!';
            } else if (res.status == 401) {
                errorMsg = '401 : Your session is out. Please login again!';
            }
            console.log(errorMsg);
        }
    });
}

var currentUser = false;

    function loadUser(username) {
        var endPoint = 'library/get-user-personal-info?username=' + username;
        sendGetRequest(endPoint, function(res) {
            if(res){
                createProfileView(res);
            }
        });
        loadUserBookIssuedHistory(username);
    }

    function createProfileView(userObj){
        $('#cart_username').val(userObj.username);     
        currentUser = userObj;
        $('#issueBtn').show();
        BooksToBeIssuedArr = [];
        printCartItem();
        var statusImageURL = '';

        if(userObj.library_status == 'Active'){
            statusImageURL = "<?=url('/images/ok.png')?>";
        }else{
            statusImageURL =  "<?=url('/images/del.png')?>";
        }
        var statusImage = '<img id="blah" src="'+statusImageURL+'" >';
        var html = '<tr>';
        html += '<td>  '+statusImage + userObj.username + '</td>';
        html += '<td>  <a href="javascript:void(0);" onclick="showStatusModal()" style="color:black;" >' + userObj.user_full_name + ' </a> </td>';
        html += '<td>' + userObj.library_status + '</td>';
        html += '<td><img src="' + userObj.image + '" width="75" height="75"></td>';
        html += '</tr>';
        $('#user_profile_detail').html(html);            
    }

    function autoSearch(ele){       
   
        $('#filter_message').hide();
        var bi_form = document.querySelector('#filterForm');
        var fd      = new FormData(bi_form);


        if( (fd.get('username') == '') && (fd.get('accesstion_user') == '') ){
            $('#filter_message').show();
            $('#filter_message').html('No Record Found!');
            return false;
        }

        $('#user-search-btn').html('Searching...');

        sendPostRequest('library/search-by-filter',function(res){
            $('#user-search-btn').html('Load Detail');
            if(res.username){
                var username = res.username;
                $('#sai_enrollment_no').val(username);
                $('#sisuserUsername').val(username);
                createProfileView(res);
                loadUserBookIssuedHistory(username);
            }else{
               
                $('#filter_message').show();
                $('#filter_message').html('No Record Found!');
            }
        },fd,function(error){
            $('#user-search-btn').html('Load Detail');
            console.log('error : ',error);
        });
        return false;
    }

    function BookTobeIssued(username) {
        var endPoint = 'library/book-to-be-issued?username=' + username;
        sendGetRequest(endPoint, function(res) {
            if (res) {
                var html = '<tr><th>Name</th><td>' + res.user_full_name + '</td></tr>';
                if (res.group_id == "4") {
                    html += '<tr><th>Program</th><td>' + res.prog + '</td></tr>';
                }
                html += '<tr><th>Department</th><td>' + res.dept + '</td></tr>';
                html += '<tr><th>School</th><td>' + res.school + '</td></tr>';


                html += '<tr><th>Username</th><td>' + res.username + '</td></tr>';
                html += '<tr><th>Mobile</th><td>' + res.mobile + '</td></tr>';
                html += '<tr><th>Status</th><td>' + res.student_status + '</td></tr>';

                $('#user_profile_detail').html(html);
            }
        });

        loadUserBookIssuedHistory(username);
    }

    var bookHistoryArr = [];
    var selectedBookTitle = '';

    function loadUserBookIssuedHistory(username) {
        var endPoint = 'library/user-issued-book-history?username='+username;
        sendGetRequest(endPoint, function(res) {
            // var html = '<tr><th>Title & Author / (Mark) </th><th>Accession # </th><th>DDC</th><th>Issue Date</th><th>Due Date</th><th>Late Date</th><th>Status</th></tr>';
            var html = '';
            var foundReceive = 0;
            var totalFine = 0;
            $(res).each(function(index, row) {
            //     alert(row.library_author_name );
                var receivedClass = ''; // (row.lc_book_status == 'Received')?'received':'';
                var lateDays = 0;
                var fine = 0;
                if(row.lc_fine_total != '' && row.lc_fine_status == 'Paid'){
                    // alert(row.lc_fine_total+'____'+row.lc_fine_status);
                    lateDays = (finecal(row.lc_due_date) >0)?finecal(row.lc_due_date):0;
                    fine ='<b class="fined" style="color:green !important;cursor:pointer">'+row.lc_fine_total+' (Paid)</b>';  
                   //alert('if first');
                }else{
                    if(row.lc_book_status == 'Received'){
                        receivedClass = 'received';
                        foundReceive  = 1;               
                        // fine = row.lc_fine_total;
                        fine ='<b class="fined" style="color:green !important;cursor:pointer" onclick="finePDF('+row.id+')" target="_blank">'+row.lc_fine_total+'</b>';     
                    }else{
                        lateDays = (finecal(row.lc_due_date) >0)?finecal(row.lc_due_date):0;
                        var amount  = lateDays*100;
                        if( amount > 0 ){
                            //alert('bilalsaleem');
                            fine = '<b class="fined">'+amount+'</b>';
                            if(row.lc_due_date.replace('00:00:00', '') > '2023-10-04'){
                                //alert(row.lc_due_date.replace('00:00:00', '')+'---'+'2023-10-04');
                                totalFine += amount;
                               
                            }else{
                                //alert('bilal');
                                totalFine = 0;
                            }
                        }else{      
                            //alert('bilal');                  
                            fine = amount;
                            if(row.lc_due_date.replace('00:00:00', '') > '2023-10-04'){
                                totalFine += amount;
                            }else{
                                
                                totalFine = 0;
                            }
                        } 
                        
                    }    
                }                  
                html += '<tr class="'+receivedClass+'">';
                html += '<td align="left">' + row.library_title + '</td>';
                html += '<td align="left">' + row.library_author_name + '</td>';
                html += '<td align="center">' + row.lc_accession_no + '</td>';
                html += '<td align="left">' + row.library_author_mark + '</td>';
                html += '<td align="center">' + row.library_ddc_number + '</td>';
                html += '<td align="center">' + row.lc_issue_date.replace('00:00:00', '') + '</td>';
                html += '<td align="center">' + row.lc_due_date.replace('00:00:00', '') + '</td>';
                if(row.lc_due_date.replace('00:00:00', '') > '2023-10-04'){
                    html += '<td align="center">' + ((row.lc_total_late_days)?row.lc_total_late_days:lateDays) + '</td>';
                    html += '<td align="center" id="fine_'+index+'">'+fine+'</td>';
                }else{
                    html += '<td align="center">0</td>';
                    html += '<td align="center">0</td>';
                }
                if(row.lc_return_date){
                    html += '<td align="center">' + row.lc_return_date.replace('00:00:00', '') + '</td>';
                }else{
                    html += '<td align="center">N/A</td>';
                }
                if(row.lc_book_status == 'Issued'){
                    html += '<td align="center"><button class="btn primary" onclick="openReturnBook(' + index + ',\'' + row.lc_due_date.substring(0, 10)+ '\');"  >Return Book</button></td>';
                }else{
                    html += '<td align="center">'+row.lc_book_status+'</td>';
                }
                
                html += '</tr>';

                // html += '<th>' + row.library_title + '<br> ' + row.library_author_name + ' / (' + row.library_author_mark + ') </th><td>' + row.lc_accession_no + '</td>';
                // html += '<td>' + row.library_ddc_number + '</td><td>' + row.lc_issue_date.replace('00:00:00', '') + '</td><td>' + row.lc_due_date.replace('00:00:00', '') + '</td><td>' + row.lc_return_date + '</td>';
                // html += '<td><button class="btn primary" onclick="openReturnBook(' + index + ');"  >Return Book</button></td></tr>';
            });
            $('#chkFine').val(totalFine);
            if(foundReceive == 1){
                html += '<tr><td colspan="10" ><button class="btn primary" style="width:100%" onclick="toggleReceiveBook();">Show Receive Books</button></td></tr>';
               
            }           

            bookHistoryArr = res;
            $('#user_issued_book_history_table').html(html);
            $('.received').hide();
        });
    }
    function finePDF(id){
        //alert(id);
        window.open( "{{URL::to('library/library_fine_slip?id=')}}"+id, '_blank');
      //  window.location.href = "{{URL::to('library/library_fine_slip?id=')}}"+id;
    }

 //    toggleReceiveBook received

    function toggleReceiveBook(){
        $('.received').toggle();
    }

    function openReturnBook(index,duedate) {
        $("#lc_due_date").val(duedate);
        var row = bookHistoryArr[index];
        console.log(row);
        $('#book-id').val(row.library_title);
        $('#book-name').val(row.library_title);
        $('#user-id').html(row.sai_enrollment_no);
        $('#fine').val(row.lc_fine_total);
        $('#id').val(row.id);
        $('#user-idd').val(row.sai_enrollment_no);
        $('#issue-date').val(row.lc_issue_date);
        $('#issue-days').val(row.lc_issue_no_of_days);
        $('#accession-no').val(row.lc_accession_no);
        $('#fine').val($("#fine_"+index+" b.fined").text());
        $('#returnBookModal').modal('show');
        loadUserBookIssuedHistory(row.sai_enrollment_no);
    }

    var working = 0;
    function searchByAccession(){

        var username = $('#sai_enrollment_no').val();
    
        // alert(username);
        // return false;
        $('#circulation-msg').hide();
        var str =  $('#searchAccession').val();

    //  alert(str); return false;

    var alreadyFound = 0;
    $(BooksToBeIssuedArr).each(function(index,row){
            if(str == row.accession_no){
                alreadyFound++;
            }
    });
    if(alreadyFound){
            $('#circulation-msg').show();
            $('#circulation-msg').html('Book already added.');
            return false;
    }


    if(str.length == 3 || str.length == 4 || str.length == 5){
        var bi_form = document.querySelector('#search_form');
            var fd = new FormData(bi_form);
            var url = '<?=url("library/search-from-allocation")?>/'+str+'/'+username;
            // alert(url);
            // return false;
            $.ajax({
                type:'POST',
                url: url,
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){
                    
                    if(data.message == 'Book Cannot be issued due to Overdue Date'){
                        $('#circulation-msg').show();
                        $('#circulation-msg').html(data.message);            
                        $('#issueBtn').hide();
                    // return false;
                    }else if(data.message == 'Book Already Issued'){

                        $('#circulation-msg').show();
                        $('#circulation-msg').html(data.message);            
                        $('#issueBtn').hide();

                    }
                    if(data.length > 0){
                        $('#issueBtn').show();
                        var lostFound = 0;

                        $(data).each(function(index,row){
                            if(row.book_status == 'Lost'){
                                lostFound = 1;
                                $('#circulation-msg').show();
                                $('#circulation-msg').html(row.library_title+' Book Lost Cannot be issue');
                            }
                        });
                        working = 0;
                        if(lostFound == 1){
                            return false;
                        }
                        // circulation-msg
                        BooksToBeIssuedArr.push(data[0]);
                        printCartItem();
                        $('#searchAccession').val('');
                    
                    }else{
                        working = 0;
                    }
                    console.log('this == > ',BooksToBeIssuedArr)

                
                },
                error: function (jqXHR, exception) {
                    working = 0;
                    alert('Error');
                },
            });
    }

    return false;
    }

    function printCartItem(){
        $('#circulation-msg').hide();
        var data = BooksToBeIssuedArr;
        var html = '';
        $.each(data,function( key, value ) {
            //console.log('hello ',value)
            var refBook = (value.library_item_type == 'Reference Book')?'Reference Book':'';
            html +=  '<tr>';
            html +=  '<td align="left" width="200"><input type="hidden" name="titles[]" value="'+value.library_title+'">';
            html +=  '<input type="hidden" name="accession_nos[]" value="'+value.accession_no+'">';
            html +=  '<input type="hidden" name="book_ids[]" value="'+value.id+'">';
            html +=  value.library_title+'</td>';
            html +=  '<td align="left"  width="200">'+value.library_author_name+'</td>'
            html +=  '<td align="center" width="200">'+value.accession_no+'</td>';          
            html +=  '<td align="center" width="200">'+value.library_ddc_number+'</td>';
            html +=  '<td align="left" width="200">'+value.library_author_mark+'   <input type="hidden" id= "issue_date" value="<?php echo date('Y-m-d');?>"></td>';
            html +=  '<td align="center"><button class="btn" onclick="removeBookItem('+key+');" >Remove</button>  '+refBook+' </td></tr>';
        });
        $('#cartItems_view').html(html);
    }

    function removeBookItem(index){
        BooksToBeIssuedArr.splice(index, 1);
        printCartItem();
    }

    function status(sel){
        var value = sel.value;
        if(value == 'Reissued'){
            $('#issue-div').show();
        }else{
            $('#issue-div').hide();
        }
    }

    function finecal(dueDate){
        // $('#dt').val()
        var date    = new Date();
        var day     = date.getDate();
        var month   = date.getMonth() + 1;
        var year    = date.getFullYear();
        var arr     = [year, month, day].join('/');
       //  var issueDate=$('#issue-date').val();

        var date1 = new Date(dueDate);
        var date2 = new Date(arr);

        const diffTime = (date2 - date1);
        const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
        //console.log('diffDays ---> ', diffDays);
        const fine=diffDays*100;       

        //     $('#fine').val(fine);

        // if(diffDays>0){
        //     const fine=diffDays*50;
        //     $('#fine').val(fine);
        //     $('#dt').removeClass('is-invalid');
        // }else{
        //     $('#fine').val(0);
        //     $('#dt').removeClass('is-invalid');

        // }
        return diffDays;
    }

    function checkval(user_id,book_name,book_id,fine,id,date,issueDays,accession_no){
        $('#book-id').val(book_id);
        $('#book-name').val(book_name);
        $('#user-id').html(user_id);
        $('#fine').val(fine);
        $('#id').val(id);
        $('#user-idd').val(user_id);
        $('#issue-date').val(date);
        $('#issue-days').val(issueDays);
        $('#accession-no').val(accession_no);
    }

    function saveChanges(){

        var currentdate = new Date(); 
        var datetime = currentdate.getDate() + "/"
                        + (currentdate.getMonth()+1)  + "/" 
                        + currentdate.getFullYear() + " "  
                        + currentdate.getHours() + ":"  
                        + currentdate.getMinutes() + ":" 
                        + currentdate.getSeconds();
        $('#r_date').val(datetime);

        var username = $('#cart_username').val();        
        var book    =   $('select[name=book-status] option').filter(':selected').val();
        var date    =   new Date($('#dt').val());
        var day     =   date.getDate();
        var month   =   date.getMonth() + 1;
        var year    =   date.getFullYear();
        var arr     =   [year, month, day].join('/');

        if($('#issue').val() == '' && book =='Issued'){
            $("#issue").focus();
            $("#issue").css("border","1px solid red");
            return false;
        }
        // alert(book);
        // return flase;
        if(book==''){
            $('#bookstatus').addClass('is-invalid');
            return false;
        // }else if(book=='reissue'){
        //     var issuedate = new Date($('#issue').val());
        //     var day = issuedate.getDate();
        //     var month = issuedate.getMonth() + 1;
        //     var year = issuedate.getFullYear();
        //     var arr=[year, month, day].join('/');
        // }
        }else if(book=='Reissued'){
            var issuedate = new Date($('#issue').val());
            // alert(issuedate);
            if(issuedate == 'Invalid Date'){
                alert('Please Select Reissue Date');
                return false;
            }else{              
                var day = issuedate.getDate();
                var month = issuedate.getMonth() + 1;
                var year = issuedate.getFullYear();
                var arr=[year, month, day].join('/');
            }
        }

        var bi_form = document.querySelector('#return-book-form');
        var fd = new FormData(bi_form);
        $.ajax({
            type:'POST',
            url: "{{url('library/user-return-book')}}",
            data:fd,
            processData:false,
            contentType:false,
            success:function(data){
                loadUserBookIssuedHistory(username);
                if(data.status){
                    $('#booklimitpop').html('');
                    $('#bookissuedpop').html(data.message);
                    setTimeout(function(){
                        $('#bookissuedpop').html('');
                        $('#exampleModalCenter').modal('hide');
                        $("#return-book-form")[0].reset();
                        $('#returnBookModal').modal('hide');
                    }, 2000);

                }else{
                        $('#bookissuedpop').html('');
                        $('#booklimitpop').html(data.message);
                        setTimeout(function(){
                        $('#booklimitpop').html('');
                        $('#exampleModalCenter').modal('hide');
                        }, 2000);
                        loadDetails();
                }
            }
        });

    }
// Reference Book will not issue
function fineCalOnChange(ele){


selectDate = $(ele).val();
dueDate = $("#lc_due_date").val();
selectDate = new Date(selectDate);
dueDate = new Date(dueDate);

if(selectDate > dueDate){
    var timeDiff = selectDate - dueDate;
    var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));

    $("#fine").val(daysDiff * 100);
    // $('#fine').val($("#fine_"+(daysDiff * 100)+" b.fined").text());
}
else{
    $("#fine").val(0);
}
}
    function submitBookCartItme(){

        var chkFine = $('#chkFine').val();
        if(chkFine > 0){
            $('#cart-error-msg').show();
            $('#cart-error-msg').html('Book cannot Issue due Unpaid Fine');
            $('#cart-error-msg').css('color','red');
            setTimeout(function(){
                $('#cart-error-msg').hide();
            }, 3000);     
            return false;
        }

        if(BooksToBeIssuedArr.length == 0){
            return false;
        }

        $('#cart-error-msg').hide();
        var bi_form = document.querySelector('#bookCartForm');
        var fd      = new FormData(bi_form);
        sendPostRequest('library/post-issue-books', function(res){
            var username = $('#cart_username').val();
            loadUserBookIssuedHistory(username);
            $('#cart-error-msg').show();
            $('#cart-error-msg').html(res.message);
            if(res.message == 'Book cannot Issue due Unpaid Fine'){
                $('#cart-error-msg').css('color','red');
            }else{
                $('#cart-error-msg').css('color','green');
            }
            setTimeout(function(){
                $('#cart-error-msg').hide();
            }, 3000);            
            if(res.message == 'Book Cannot be issued due to Overdue Date'){
              $('#issueBtn').hide();
            }
          //  alert(res.message);
        }, fd, function(error){
            console.log('Hi I am error ! ', error);
            // This Book already has been Issued to this User
            $('#cart-error-msg').show();
            $('#cart-error-msg').html('<p style="color:red;">This Book already has been Issued to this User</p>');
        });
    }

    function showStatusModal(id){
 
        $('#profile-status-popup').modal('show');
        $('#sisuserID').val(id);
        $('#library_status').val(currentUser.library_status);
    }

    function updateStatus(){    
        var status      = $('#library_status').val();
        var data        = {'username' : currentUser.username, 'status' : status};
        $.ajax({
            type:'POST',
            url: "{{url('api/library/update-library-status')}}",
            data:JSON.stringify(data),
            processData:false,
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            success:function(data){
                var username = $('#sai_enrollment_no').val();
                loadUser(username);
                $('#profile-status-popup').modal('close');
            }
        });
    }

    $('document').ready(function(e){
        $(".scanner_search").on('keyup', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();

            }
        });        



        /****************************************************************/



        $('#sai_enrollment_no').autoComplete({
    resolver: 'custom',
    autoSelect: false,
    formatResult: function (item) {
						return {
							value: 'hello ap',
							text: item.text + "[" + item.value + "] ",
							// html: [ 
							// 		$('<img>').attr('src', item.icon).css("height", 18), ' ',
							// 		item.text 
							// 	] 
						};
					},
                    select : function (evt, item){
            console.log('Hello Ahmed',item);
        },
    events: {
        search: function (qry, callback) {
            // let's do a custom ajax call
            $.ajax(
                '<?=url('api/library/search-user-for-suggestion_library')?>',
                {
                    data: { 'qry': qry}
                }
            ).done(function (res) {
                console.log(res);
                callback(res)
            });
        }
      
    }
});

$('#sai_enrollment_no').on('autocomplete.select', function (evt, item) {
					// $('.basicAutoCompleteSelected').html(JSON.stringify(item));
					// $('.basicAutoCompleteCustom').html('');
                    $('#sai_enrollment_no').val(item.value);
});



        /****************************************************************/







    });









</script>
@stop
