@extends('layouts.app', ['pageTitle' => 'Car pooling'])
@section('content')
    <style>
        div.dataTables_wrapper div.dataTables_filter input {
            width: 450px;
            height: 40px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>


<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmModalLabel">Confirm Request Submission</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <!-- <input type="text" name="txt_campus_id" id="txt_campus_id">
        <input type="text" name="txt_car_pooling_id" id="txt_car_pooling_id"> -->
        <span style="display:none" id="username"></span> <span style="display:none" id="action"></span> <span style="display:none" id="id"></span> 
        <div class="modal-body" id="body_message">
  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="submitRequestBtn">Submit Request</button>
        </div>
      </div>
    </div>
  </div>

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
                        <li class="mr-2 p-2 rounded"><a href="{{ url('/car-pooling-details') }}" class="active text-white"><i
                                    class="fa fa-car fa-lg mr-2"></i>Ride Search</a>
                        </li>
                        <li class="mr-2 p-2 rounded"> <a href="{{ url('/car-pooling-inbox') }}"><i
                                    class="fa fa-inbox fa-lg mr-1 text-primary"></i>Ride Request </a>
                        </li>
                        <li class="mr-2 p-2 rounded"><a href="{{ url('/car-pooling-chat') }}"><i
                            class="fa fa-commenting-o fa-lg mr-2"></i>ChatBox
                            <span
                            class="badge badge-danger">{{ count($car_pulling_count) }}</span>
                        </a>
                </li>
                    </ul>
                </div>
            </div>
            <hr>
            <h4>Details</h4>
            <div class="dt-responsive ">
                <table id="simpletable" class="table table-striped table-bordered dt-responsive">
                    <thead>
                        <tr>
                            <th class="tab-title">Username</th>
                            <th class="tab-title">Conversation</th>
                            <th class="tab-title">Route</th>
                            <th class="tab-title">Ride</th>
                            <th class="tab-title"> Contact</th>
                            <th class="tab-title">Preferred Location</th>
                            <th class="tab-title">Route Option</th>
                            <th class="tab-title">Request</th>
                            <th class="tab-title">Vehicle</th>
                            <th class="tab-title">Engine Size</th>
                            <th class="tab-title">Capacity</th>
                        </tr>
                    </thead>
                    <!-- <tbody> -->
                    <?php foreach ($car_pulling as $item){
                                    $color = '';
                                    $bgcolor = '';
                                    $reqMsg = '';
                                    if(strpos($item->cp_partner, auth()->user()->username) !== false){                                                              
                                        $bgcolor = '#2C3E50';    
                                        $color = '#fff';   
                                        $reqMsg = 'Ride Accepted' ;
                                    }else{
                                        $color = '#black';  
                                        $bgcolor = '#fff'; 
                                    }
                                    ?>
                    <tr style="background-color:<?= $bgcolor ?>;color:<?= $color ?>">
                        <td style="background-color:;height:100px" class="card-header">
                            {{ $item->cp_entry_username }}
                             &nbsp &nbsp &nbsp &nbsp &nbsp 
                                <span style="color:#28E1BD"><b><?= $reqMsg ?></b></span>
                         
                        </td>
                        <td>
                            <a class="option-dots new-list2" href="#" data-toggle="modal"
                                onclick="getUsername('<?= $item->cp_entry_username ?>','<?= $item->cp_id ?>')"
                                data-target="#large-Modal_chat"><svg class="svg-icon" style="scale:1.3;"
                                    xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                    <path d="M20 4H4v13.17L5.17 16H20V4zm-6 10H6v-2h8v2zm4-3H6V9h12v2zm0-3H6V6h12v2z"
                                        opacity=".3" />
                                    <path
                                        d="M20 18c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14zm-16-.83V4h16v12H5.17L4 17.17zM6 12h8v2H6zm0-3h12v2H6zm0-3h12v2H6z" />
                                </svg>
                            </a>
                        </td>
                        @if ($item->cp_route_link != '')
                            <td><a href="{{ $item->cp_route_link }}" class="btn btn-primary mb-2" target="_blank">
                                    <i class="fa fa-map-marker fa-xl" aria-hidden="true" class="font-size: 1.2rem;"></i>
                                    Route
                                </a>
                                <span style="display:none">{{ $item->cp_route_link }}</span>
                            </td>
                        @else
                            <td>Not Mentioned</td>
                        @endif
                        <td>{{ $item->cp_want_ride }}</td>
                        <td>{{ $item->cp_contact_det }}</td>
                        <td>{{ $item->cp_preferred_location }}</td>
                        <td>
                            <h6 class="mb-4">
                                {{ $item->cp_route_option }}
                                </h6>
                      
                                
                                
                                <a class="bnt btn-primary text-white p-3 mb-3 rounded" href="#" data-toggle="modal"
                                onclick="getTimings('<?= $item->cp_entry_username ?>','<?= $item->cp_id ?>')"
                                data-target="#default-Modal">
                                Timings
                            </a>
                            
                            
                        </td>
                        <td>
                            <!-- onclick="getUsername_request('<?= $item->cp_entry_username ?>','<?= $item->cp_id ?>')" -->
           
@if ($item->offer_status == '' or $item->cp_want_ride=='PENDING')
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal" data-username="<?= $item->cp_entry_username ?>" data-id="<?= $item->cp_id ?>" data-action="insert">Send Request</button>

@else

<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmModal" data-username="<?= $item->cp_entry_username ?>" data-id="<?= $item->offer_status ?>"  data-action="delete">Cancel Request</button>

@endif
                               
                            </td>
                        <td>{{ $item->cp_vehical_model }}</td>
                        <td>{{ $item->cp_vehical_cc }}</td>
                        <td>{{ $item->cp_capacity }}</td>
                    </tr>
                    <?php } ?>
                    <!-- </tbody> -->
                </table>
            </div>

        </div>
    </div>

    <!-- Message Modal -->
    <div class="modal fade" id="large-Modal_chat" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-right chatbox" role="document">
            <div class="modal-content chat border-0">
                <div class="card overflow-hidden mb-0 border-0">
                    <!-- action-header -->
                    <div class="action-header">
                        <div class="float-left hidden-xs d-flex ml-2">
                            <div class="img_cont mr-3">
                                <img src="../../assets/images/users/16.jpg" class="rounded-circle user_img avatar avatar-md"
                                    alt="img">
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
                                    <form enctype="multipart/form-data" method="post" name="sendMessageCarPulling_form"
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
                                            <button type="button" id="messageBtn" onclick="btn_sendMessageCarPulling()"
                                                class="btn btn-primary ">
                                                <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24"
                                                    viewBox="0 0 24 24" width="24">
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




    <!-- {{-- Model for days timing --}} -->
    <div class="modal fade" id="default-Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div>
                        <div>
                            <h4 class="modal-title text-left">Ride Timings</h4>
                        </div>
                        <div class="card-block dt-responsive">
                            <table class="table table-striped table-bordered dt-responsive">
                                <thead>
                                    <tr>

                                        <th>Monday</th>
                                        <th>Tuesday</th>
                                        <th>Wednesday</th>
                                        <th>Thursday</th>
                                        <th>Friday</th>
                                        <th>Saturday</th>
                                        <th>Sunday</th>
                                    </tr>
                                </thead>
                                <tbody id="dailyTimings">

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    </div>
@stop
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#simpletable').DataTable();
            $('#confirmModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var username = button.data('username'); // Extract info from data-* attributes
            var id = button.data('id');
            var action = button.data('action');

            if(action=='delete'){
                $('#body_message').text('Are you Sure you want to delete Request');
            }else{
                $('#body_message').text('Are you Sure you want to Send Request');
            }        

            // Update the modal content with the extracted information
            var modal = $(this);
            modal.find('#username').text(username);
            modal.find('#id').text(id);
            modal.find('#action').text(action);


         });
        });

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
            $('#res-config').DataTable();
            // var username=$('#sendToUsername').val();
            // var id= $('#cp_id').val();
            setInterval(function() {
                console.log('love');
                // btn_sendMessageCarPulling();
                var username = $('#sendToUsername').val();
                var id = $('#cp_id').val();
                getUsername(username, id);
            }, 5000);

        });
        $('#submitRequestBtn').click(function() {
                var username    = $('#confirmModal').find('#username').text();
                var id          = $('#confirmModal').find('#id').text();
                var action      = $('#confirmModal').find('#action').text();
                var login_user_id = '<?=auth()->user()->username;?>';

               // alert(id);
                // Add your request code here, passing in the username and id variables
              //  console.log("Submitting request for user " + username + " with ID " + id);
                
                var data = new FormData();
                // alert(AuthUserNamee)
                data.append("user_ID", username);
                data.append("cp_id", id);
                data.append("requested_user", login_user_id);
                data.append("action", action);

        

              sendPostRequest('pooling_request_aceept_cancel?rno='+Math.random(), function(res) {
                    //console.log('Hello Baby', res);
                    //alert(res.status);
                    if (res.status) {
                        // $('#successMsg' + userID).css('color', 'green');
                        // $('#successMsg' + userID).html('Request Accepted Successfully');
                        // // getRequest();     
                        // setTimeout(function() {
                        //     location.reload();
                        // }, 1000);

                        location.reload();
                    } else {

                    }
                }, data);



                // Close the modal
                $('#confirmModal').modal('hide');
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
                url: 'api/get-car-pulling-message?cp_id=' + id + '&username=all' + '&authUsername=' + username,
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
            let message = document.getElementById("message").value;
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
                        var username = $('#sendToUsername').val();
                        var id = $('#cp_id').val();
                        $('#message').val('');
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
        function getTimings(username, cp_id) {
            //    alert(cp_id);
            $('#dailyTimings').html('');
            // sendPostRequest('car-pulling-timings?cp_id=' + cp_id, function(res) {
                $.ajax({
                    type: 'POST',
                    url: 'api/car-pulling-timings?cp_id=' + cp_id,
                    headers: {
                        Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                        Accept: 'application/json'
                    },
                    processData: false,
                    contentType: false,
                    success: function(res) {
                    if (res.status) {
                    let data = res.data[0]
                    console.log(data);
                    var tr = `
                        <tr>
                            <td>${data.monday}</td>
                            <td>${data.tuesday}</td>
                            <td>${data.wednesday}</td>
                            <td>${data.thursday}</td>
                            <td>${data.friday}</td>
                            <td>${data.saturday}</td>
                            <td>${data.sunday}</td>
                            </tr>
                        `;
                    $('#dailyTimings').append(tr);
                }
                    }

                });





              
            }
    </script>
@stop
