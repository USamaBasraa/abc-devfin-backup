@extends('layouts.app',['pageTitle' => 'Data Correction'])

@section('title')
    BNU CMS
@stop
@section('content')
<style type="text/css">
    div {
        border: 0px solid black;
    }

    .content-div {
        width: 100%;
        padding-left: 15px !important;
    }

    #system-side-bar {
        display: none;
    }

    .nav-tabs .nav-link {
        color: indigo;
    }

    .userlink:link {
        color: darkgray;
    }
</style>
<?php
$bookStatus = array("Active", "Pending", "Update");
?>
<div class="row">
    <div class="col-4 col-sm-4 col-lg-4">
        <div class="card">

            <div class="card-header">


                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Users</a>
                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Books</a>
                        <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Contact</a>
                    </div>
                </nav>


            </div>

            <div class="card-body">

                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

                    <form method="post" id="searchUserForm" action="#">
                            <div class="form-group">
                                <label for="searchStudent">Search User</label>
                                <input type="text" name="query" class="form-control" id="searchStudent" placeholder="Search by User Name & ID">
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn" value="Search">
                            </div>
                        </form>
                        <table class="table" id="user_search_listing">
                            <tr>
                                <td>No Search Submit</td>
                            </tr>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">...</div>

                    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                   ...
                    </div>
                </div>




            </div>

        </div>
    </div><!-- col-6 col-sm-6 col-lg-6 -->

    <div class="col-8 col-sm-8 col-lg-8">
        <div class="card">

            <div class="card-header">
                <nav>
                    <div class="nav nav-tabs" id="detail-nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-user-detail-tab" data-toggle="tab" href="#nav-user-detail" role="tab" aria-controls="nav-user-detail" aria-selected="true">Users</a>
                        <a class="nav-item nav-link" id="nav-book-detail-tab" data-toggle="tab" href="#nav-book-detail" role="tab" aria-controls="nav-book-detail" aria-selected="false">Books</a>
                        <!-- <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Contact</a> -->
                    </div>
                </nav>
            </div>

            <div class="card-body">




            <div class="tab-content" id="nav-tabContent_Detail">
                    <div class="tab-pane fade show active" id="nav-user-detail" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="alert alert-dark" role="alert">
                        <b>Profile</b>
                        </div>
                        <table class="table" id="user_profile_detail">
                            <tr>
                                <td>No User Select</td>
                            </tr>
                        </table>
                        <!--  Issued Book  -->
                        <div class="alert alert-dark" role="alert">
                            <b>User Issued Book</b>
                        </div>
                        <table class="table" id="user_issued_book_history_table">
                            <tr>
                                <td>No User Select</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Book Detail Tab -->
                    <div class="tab-pane fade" id="nav-book-detail" role="tabpanel" aria-labelledby="nav-profile-tab">
                        
                        <div class="alert alert-dark" role="alert">
                        <b id="detail_book_title" >...</b>
                        </div>
                    </div>


                    <!-- <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">...</div> -->
                </div>







                




            </div>

        </div>
    </div><!-- col-6 col-sm-6 col-lg-6 -->




</div>



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
                        <label for="inputPassword6">User Name : </label>
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
                            <label for="inputEmail4">Return Date</label>
                            <input type="date" class="form-control date" readonly value="2021-04-28" onchange="finecal()" name="return-date" id="dt" placeholder="Return Date">
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
                                <option value="Issued">Reissued</option>
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
                <span id="bookissued" style="color:green"></span>
                <span id="booklimit" style="color:red"></span>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" onclick="saveChanges()" id="btn" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!----   Return Book Modal End   ----->


@stop


@section('javascript')
<script type="text/javascript">
    // function loadDetail(formID){
    //     $('#').modal('show');
    //     $.ajax();

    // }


    var url = "<?= url('/') ?>";

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


    function sendPostRequest(endPoint, successBlock, postData, failBlock) {
        $.ajax({
            method: "POST",
            url: url + '/api/' + endPoint,
            data: postData,
            processData: false,
            contentType: false,
            headers: reqHeaders
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


    $(document).ready(function() {
        $('#system-side-bar').hide();


        $("#searchUserForm").submit(function(e) {
            $('#user_search_listing').html('<tr><td>Searching....</td></tr>');
            var query = $('#searchStudent').val();
            var endPoint = 'library/search-user?query=' + query;
            sendGetRequest(endPoint, function(res) {
                var html = '<tr><td>Users</td></tr>';
                $(res.data).each(function(index, row) {
                    html += '<tr><td><a href="javascript:void(0);" class="userlink" onclick="loadUser(\'' + row.username + '\')">' + row.username + '</a></td></tr>';
                });
                $('#user_search_listing').html(html);
            });
            return false;
        });

    });



    function loadUser(username) {
        // get-user-personal-info

        var endPoint = 'library/get-user-personal-info?username=' + username;
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
        var endPoint = 'library/user-issued-book-history?username=' + username;
        sendGetRequest(endPoint, function(res) {
            var html = '<tr><th>Title & Author / (Mark) </th><th>Accession # </th><th>DDC</th><th>Issue Date</th><th>Due Date</th><th>Late Date</th><th>Status</th></tr>';
            $(res).each(function(index, row) {
                html += '<tr><th><a href="javascript:void(0);" onclick="showBookDetail('+index+');" >' + row.library_title + ' </a> <br> ' + row.library_author_name + ' / (' + row.library_author_mark + ') </th><td>' + row.lc_accession_no + '</td>';
                html += '<td>' + row.library_ddc_number + '</td><td>' + row.lc_issue_date.replace('00:00:00', '') + '</td><td>' + row.lc_due_date.replace('00:00:00', '') + '</td><td>' + row.lc_return_date + '</td>';
                html += '<td><button class="btn primary" onclick="openReturnBook(' + index + ');"  >Return Book</button></td></tr>';
            });
            bookHistoryArr = res;
            $('#user_issued_book_history_table').html(html);
        });
    }

    // data-toggle="modal" data-target="#exampleModalCenter"
    function openReturnBook(index) {
        var obj = bookHistoryArr[index];
        console.log(obj);
        $('#returnBookModal').modal('show');
    }


    function showBookDetail(index){
        var obj = bookHistoryArr[index];
        

        var tab = 'nav-book-detail';
        $('.nav-tabs a[href="#' + tab + '"]').tab('show');
        $('#detail_book_title').html(obj.library_title);
    }






</script>
@stop