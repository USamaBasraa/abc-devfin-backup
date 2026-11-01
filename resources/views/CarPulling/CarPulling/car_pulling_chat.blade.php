<?php

// print_r($car_pulling);
// exit();
?>


@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

<style>
     

    @media (min-width: 576px) {
        div.dataTables_wrapper div.dataTables_filter input {
            width: 100%;
}
    div.dataTables_wrapper div.dataTables_filter input {
        width: 450px;
        height: 40px;
    }
}
    tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>
    <div class="card">
        <div class="card-header bg-primary"
            style="color:white !important;border-top-right-radius:10px;border-top-left-radius:10px">
            <div class="card-title text-white">
                Car Pooling Search
            </div>
        </div>
        <div class="card-block">
            <div class="tab-menu-heading">
                <div class="tabs-menu ">
                    <!-- Tabs -->
                    <ul class="nav panel-tabs">
                        <li class="mr-2 p-2 rounded"><a href="{{ url('/car-pooling-details') }}" ><i
                                    class="fa fa-car fa-lg mr-2"></i>Ride Search</a>
                        </li>
                        <li class="mr-2 p-2 rounded"> <a href="{{ url('/car-pooling-inbox') }}" ><i
                                    class="fa fa-inbox fa-lg mr-1"></i>Ride Request </a>
                        </li>
                        <li class="mr-2 p-2 rounded"><a href="{{ url('/car-pooling-chat') }}" class="active text-white"><i
                            class="fa fa-commenting-o fa-lg mr-2"></i>ChatBox
                            <span
                            class="badge badge-danger">{{ count($car_pulling) }}</span>
                     </a>
                </li>
                    </ul>
                </div>

                
                <hr>
                <h4>Details</h4>
                <div class="dt-responsive ">
                    <table id="simpletable" class="table table-striped table-bordered dt-responsive">
                        <thead>
                            <tr>
                                   <th class="wd-15p border-bottom-0">Username</th>
                                       <th class="wd-25p border-bottom-0">Conversation</th>
                                       <!-- <th class="wd-15p border-bottom-0">Ride Type</th> -->
                                       <!-- <th class="wd-15p border-bottom-0">Vehicle Capacity</th> -->
                                       <!-- <th class="wd-15p border-bottom-0">Preferred Locations</th> -->
                                    
                               </tr>
                        </thead>
                        <!-- <tbody> -->
                            @foreach ($car_pulling as $item)
                            <tr>
                                <td>{{ $item->entry_user_id }}</td>
                                <input type="hidden" id="userId"
                                    value="{{ $item->entry_user_id }}">
                                <td>

                                    <a class="option-dots new-list2" href="#"
                                        data-toggle="modal"
                                        onclick="getUsername('<?= $item->entry_user_id ?>','<?= $item->cp_id ?>')"
                                        data-target="#large-Modal_chat"><svg
                                            class="svg-icon" style="scale:1.3;"
                                            xmlns="http://www.w3.org/2000/svg"
                                            height="24" viewBox="0 0 24 24"
                                            width="24">
                                            <path d="M0 0h24v24H0V0z" fill="none" />
                                            <path
                                                d="M20 4H4v13.17L5.17 16H20V4zm-6 10H6v-2h8v2zm4-3H6V9h12v2zm0-3H6V6h12v2z"
                                                opacity=".3" />
                                            <path
                                                d="M20 18c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14zm-16-.83V4h16v12H5.17L4 17.17zM6 12h8v2H6zm0-3h12v2H6zm0-3h12v2H6z" />
                                        </svg></a>

                                </td>
   
                            </tr>
                        @endforeach
                        <!-- </tbody> -->
                    </table>
                </div>

            </div>
        </div>

        <!-- Modal large-->
        <div class="modal fade" id="large-Modal_chat" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-right chatbox" role="document">
                <div class="modal-content chat border-0">
                    <div class="card overflow-hidden mb-0 border-0">
                        <!-- action-header -->
                        <div class="action-header">
                            <div class="float-left hidden-xs d-flex ml-2">
                                <div class="img_cont mr-3">
                                    <img src="../../assets/images/users/16.jpg"
                                        class="rounded-circle user_img avatar avatar-md" alt="img">
                                </div>
                                <div class="align-items-center mt-2 text-white">
                                    <h5 class="mb-0"><span id="title"></span></h5>
                                    <span class="dot-label bg-success"></span><span class="mr-3 fs-12">online</span>
                                </div>
                            </div>
                            <ul class="ah-actions actions align-items-center">
                                <li>
                                    <a href="" class="" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg"
                                                height="24" viewBox="0 0 24 24" width="24">
                                                <path d="M0 0h24v24H0V0z" fill="none" />
                                                <path
                                                    d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" />
                                            </svg></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- action-header end -->

                        <!-- msg_card_body -->
                        <div class="card-body msg_card_body">
                            <div class="col-lg-12">
                                <div class="card widget-chat-box" style="border-top:0px solid">
                                    <div class="card-block">
                                        <form enctype="multipart/form-data"  method="post" name="sendMessageCarPulling_form"
                                            id="sendMessageCarPulling_form">
                                            @csrf
                                            <div class="input-group">
                                                <input type="text" id="message" name='message' class="form-control"
                                                    placeholder="Please Enter Message..."
                                                    style="    border: 1px solid #424e79 !important;" />
                                                <input type="hidden" id="sendToUsername" name="sendToUsername">
                                                <input type="hidden" id="cp_id" name="cp_id">
                                                <input type="hidden" id="login_username"
                                                    value="<?= auth()->user()->username ?>" name="login_username">
                                                <button type="button" id="messageBtn"
                                                    onclick="btn_sendMessageCarPulling()" class="btn btn-primary ">
                                                    <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg"
                                                        height="24" viewBox="0 0 24 24" width="24">
                                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                                        <path d="M4 8.25l7.51 1-7.5-3.22zm.01 9.72l7.5-3.22-7.51 1z"
                                                            opacity=".3" />
                                                        <path
                                                            d="M2.01 3L2 10l15 2-15 2 .01 7L23 12 2.01 3zM4 8.25V6.03l7.51 3.22-7.51-1zm.01 9.72v-2.22l7.51-1-7.51 3.22z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </form>
                                        <hr style="border-top:2px solid tomato">
                                        <div id="msg_cotainer_send" class="text-left"></div>
                                    </div>
                                </div>
                                <p>
                                <div id="msg_cotainer"></div>
                                <div id="msg_cotainer_send"></div>
                                </p>
                            </div>
                        </div>
                        <!-- msg_card_body end -->
                        <!-- card-footer -->

                    </div>
                </div>
            </div>
        </div>

        <table id="itrc"></table>

    @stop

    @section('javascript')
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                // disable enter key on form
                document.addEventListener('keydown', function(event) {
                    if (event.keyCode === 13 && event.target.nodeName === 'INPUT') {
                        event.preventDefault();
                        return false;
                    }
                });
            });


            $(document).ready(function() {
                $('#simpletable').DataTable();

            });


            function acceptRide(id, cap, userID, AuthUserNamee,textNocap,parent_cpID,cp_partner) {
                // sendMail();
                // var bi_form = document.querySelector('#car_pulling');
               
                var data = new FormData();
               
                data.append("user_ID", userID);
                data.append("AuthUserNamee", AuthUserNamee);
                data.append("textNocap", textNocap);
                data.append("parent_cpID", parent_cpID);
                data.append("cpPartner", cp_partner);
                sendPostRequest('update-Capacity?cp_id=' + id + '&cap=' + cap, function(res) {
                    //console.log('Hello Baby', res);
                    //alert(res.status);
                    if (res.status) {
                        $('#successMsg' + userID).css('color', 'green');
                        $('#successMsg' + userID).html('Request Accepted Successfully');
                        // getRequest();     
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {

                    }
                }, data);
            }

            // var flag=0;
            $(document).ready(function() {
                $('#res-config').DataTable();
                setInterval(function() {
                    var username = $('#sendToUsername').val();
                    var id = $('#cp_id').val();
                    getUsername(username, id);

                }, 5000);
            });
            function getUsername(username, id) {
                var loginUsername = $('#login_username').val();
                $('#title').html(username);
                $('#sendToUsername').val(username);
                $('#cp_id').val(id);
                if (!id) {
                    return false;
                }
                var authUsername = '<?= auth()->user()->username ?>';
                $.ajax({
                    type: 'get',
                    url: 'api/get-car-pulling-message?cp_id=' + id + '&username=all' + '&authUsername=' + authUsername,
                    headers: {
                        Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                        Accept: 'application/json'
                    },
                    // data:formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            var rightSide = '';
                            var leftSide = '';
                            $('#msg_cotainer').html('');
                            $('#msg_cotainer_send').html('');

                            $(res.data).each(function(index, row) {
                                if (loginUsername != row.entry_user_id) {
                                    var divone = '<div class="d-flex justify-content-start">';
                                    var divtwo =
                                        '<div class="img_cont_msg"><img src="../../assets/images/users/16.jpg" class="rounded-circle user_img_msg" alt="img"></div>';
                                    var divthree =
                                        '<div class="msg_cotainer bg-primary text-white"><span>' + row
                                        .message + '</span></div>';
                                } else {
                                    var divone = '<div class="d-flex justify-content-end">';
                                    var divtwo =
                                        '<div class="img_cont_msg"><img src="../../assets/images/users/16.jpg" class="rounded-circle user_img_msg" alt="img"></div>';
                                    var divthree =
                                        '<div class="msg_cotainer bg-success text-white"><span>' + row
                                        .message + '</span></div>';

                                }
                                var row_add = divone + '' + divtwo + ' ' + divthree + '</div';
                                $('#msg_cotainer_send').append(row_add);
                            });

                            if (rightSide == '' && leftSide == '') {
                                rightSide = '<tr><td colspan="4"> No Message FOund Yet! </td></tr>';
                            }

                        } else {

                        }
                    },
                    error: function(jqXHR, exception) {},
                });
            }

            function btn_sendMessageCarPulling() {

                event.preventDefault()
                let message = document.getElementById("message").value.trim();
                if (message === '') {
                    return false;
                }
                //         // $('#messageBtn').attr('disabled', true);
                var bi_form = document.querySelector('#sendMessageCarPulling_form');
                var formData = new FormData(bi_form);
                var authUsername = '<?= auth()->user()->username ?>';
                console.log(formData);
                $.ajax({
                    type: 'POST',
                    url: 'api/car-pulling-message?authUsername=' + authUsername,
                    headers: {
                        Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                        Accept: 'application/json'
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.status) {
                            $('#message').val('');
                            var username = $('#sendToUsername').val();
                            var id = $('#cp_id').val();
                            getUsername(username, id)
                            // getRequest();     
                            setTimeout(function() {
                                $('#successMsg').html('');
                                $('#messageBtn').removeAttr("disabled");
                            }, 2000);

                        }
                    }

                });


            }
        </script>
    @stop
