@extends('layouts.app',['pageTitle' => 'Task Board'])

@section('content')
<style>
    html, body {
      height: 100%;
    }

    .tab-content {
      height: 100%;
      overflow: auto;
    }

    @media (min-width: 576px) {
      .tab-pane {
        height: calc(100% - 56px);
      }
    }
    .cus{
        background-color: gainsboro;
        border-radius:8px !important;
    }
  </style>

  <!-- Send Message Modal -->
  <div class="modal fade" id="sendMessageModal" tabindex="-1" role="dialog" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sendMessageModalLabel">Send Message</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
         <form id="messageForm"  method="POST">
            @csrf
          <textarea class="form-control" rows="4" id="messageTextarea" name="messageTextarea"></textarea>
          <input type="text" name="taskIdInput" id="taskIdInput" value="">
        </form>
      </div>
      <div class="modal-footer">
        <span id="successMsg"></span>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button"   class="btn btn-primary  assignToMessagesBtn">Send</button>

    </div>
    </div>
  </div>
</div>
<!-- ------------ -->


    <div class="container-fluid">
    <div class="col-sm-12">

      <!-- <div class="row">
          <div class="col-sm-12">
            <label for="staff">Select School / Department / Staff:</label>
            <select name="staff" id="staff" class="form-control select2">
                <option value="" style="background-color: yellow;font-weight: bold;">Select School / Department / Staff:</option>
                @foreach ($staffMembers as $staff)
                    <option value="{{$staff->parent_id . '|' . $staff->id. '|' . $staff->id }}">
                        {{ $staff->name }}
                        @if ($staff->code!="")
                            ({{$staff->code}})
                        @endif
                    </option>
                @endforeach
            </select>
          </div>
      </div>   -->
      <div class="card">
            <div class="card-header row" style=" color:white; background-color: #2C3E50; padding:0px;padding-left: 20px;margin-right:0px;margin-left:0px; ">
                    <div class="col-2 col-sm-2 col-md-9 col-lg-9 col-xl-9" style="border: solid 0px green;">
                    <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left; line-height: 20px;" >Task Board</h3>
                    </div>
                    <div style="display: inline-block; float:right"  >
                    <div class="row">
                    <div class="col-sm-10"></div>
                        <div class="col-sm-2">
                        <button type="button" class="btn btn-danger"><i class="fe fe-link mr-2"></i><a style="color: white;" href="/staff/task-list" >Add Task</a></button>
                    </div>
                </div>
            </div>
    </div>
    <div class="card-body">
      <!-- <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Home</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Profile</a>
  </li>

</ul> -->
        <ul class="nav nav-tabs" id="pills-tab" role="tablist">
            <li class="nav-item" style="width:10%; text-align:center;">
                <a class="nav-link active" style="color: #0c0c0c" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Todo/Task <b></b></a>
            </li>
            <li class="nav-item" style="width:10%; text-align:center;">
                <a class="nav-link" style="color: #0c0c0c" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Completed <b></b></a>
            </li>
        </ul>


        <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            ..1.

            <?php
            //print_r($tasks);
            $schoolName='';
            foreach($tasks as $key){
            if($schoolName!=$key->tm_title){
            // echo $schoolName=$rec->tm_title;
            ?>
            <div class="container mt-4 ">
                <ul>
                    <li class="list-item" style="background-color1:lightgray">
                    <div style="font-weight:bold;font-size:20px">
                    <?php echo $schoolName=$key->tm_title;
                ?>
                    </div>
                    </li>
                </ul>
            </div>

  <div class="container mt-5">
    <div class="row">
      <div class="col-sm-12 col-md-6">
        <div class="card task-card">
          <div class="card-header cus">
            <h5>To Do</h5>
          </div>
          <ul class="list-group list-group-flush">
          <?php
         // echo sizeof($tasks)." ___";
          $a=1;

          foreach($tasks as $rec){
          $a++;

          if($rec->task_type=='ToDo' and $rec->tm_title==$key->tm_title){

          ?>
          <li class="list-group-item">
              <div class="accordion" id="accordionToDo">
                <div class="card">
                  <div class="card-header" id="headingToDo1<?=$a;?>">
                    <h6 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#collapseToDo1<?=$a;?>" aria-expanded="true" aria-controls="collapseToDo1">
                        <?=$rec->tm_title."--".$rec->id;?> <span style="padding-left: 100%;"><?=$rec->completion_date;?></span>
                      </button>
                      {{-- <input type="text" name='tmId' id="tmId" value="<?=$rec->id;?>"> --}}

                    </h6>
                  </div>
                  <div id="collapseToDo1<?=$a;?>" class="collapse" aria-labelledby="headingToDo1<?=$a;?>" data-parent="#accordionToDo">
                    <div class="card-body">
                        <ul>
                        <li class="list-item">
                            <div>
                            <h6>Assign to: </h6>
                              <?php


                                 foreach($rec->resources as $emp_rec){
                                 ?>

                              {{$emp_rec->employee_name}}
                                <img src="http://cmstest.bnu.edu.pk/assets/staffimages/<?=$emp_rec->imagename;?>" width="20px" height="20px" style="border-radius:16px">

                             <?php
                                 }
                                ?>
                            </div>
                            </li>
                        </ul>
                       <ul>
                        <?php if(count($rec->task_list)>0){;?>
                       </br>
                        <h6> Task list </h6>
                       <?php
                        }
                         foreach($rec->task_list as $task_list){

                            ?>
                            <li class="list-item">
                            <div>

                              {{$task_list->tm_description}}</br></br>

                            </div>
                            </li>

                            <?php
                           }
                        ?>
                            <!-- <li class="list-item">
                            <div>Item 2</div>
                            </li>
                            <li class="list-item">
                            <div>Item 3</div>
                            </li> -->
                        </ul>
                    </div>
                  </div>
                <!-- ------------ -->



                <!-- ------------- -->
                </div>
                <div class="card-footer" style="width:100%; height:100%; border:solid 1px darkgrey">
                      <!-- Example single danger button -->
                     <!-- //--------------------- -->
                     <div class="btn-group mt-2 mb-2">
                          <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                            Action <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu" role="menu">
                            <!-- <li class="dropdown-plus-title">
                              Dropdown
                              <b class="fa fa-angle-down" aria-hidden="true"></b>
                            </li> -->
                            <li><a href="#">Completed</a></li>
                            <li><a href="#">Pending</a></li>
                            <li><a href="#">Deleted</a></li>
                            <!-- <li class="divider"></li>
                            <li><a href="#">Separated link</a></li> -->
                          </ul>
                        </div>
                     <!-- //-------------------------- -->
                      <button type="button" class="btn btn-primary sendMessageButton" data-tmID="<?=$rec->id;?>" data-toggle="modal" data-target="#sendMessageModal">Send Message <?=$rec->id;?></button>
                    </div>
              </div>
            </li>
            <?php
             }
                  }
            ?>
          </ul>
        </div>
      </div>
      <div class="col-sm-12 col-md-6">
        <div class="card task-card">
          <div class="card-header cus">
            <h5>Task / Issue  </h5>
          </div>
          <ul class="list-group list-group-flush">
            <!-- <li class="list-group-item">
              <div class="accordion" id="accordionPending">
                <div class="card">
                  <div class="card-header" id="headingPending1">
                    <h6 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#collapsePending1" aria-expanded="true" aria-controls="collapsePending1">
                        Task 3
                      </button>
                    </h6>
                  </div>
                  <div id="collapsePending1" class="collapse" aria-labelledby="headingPending1" data-parent="#accordionPending">
                    <div class="card-body">
                      Task 3 description goes here.
                    </div>
                  </div>
                </div>
              </div>
            </li> -->
        <?php
          $pending=1;
          foreach($tasks as $rec){
          $pending++;
          if(($rec->task_type=='Issue' or $rec->task_type=='Task' ) and $rec->tm_title==$key->tm_title){

          ?>
          <li class="list-group-item">
              <div class="accordion" id="accordionPending">
                <div class="card">
                  <div class="card-header" id="headingPending1<?=$pending;?>">
                    <h6 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#collapsePending1<?=$pending;?>" aria-expanded="true" aria-controls="collapsePending1">
                        <?=$rec->tm_title;?> <span style="padding-left: 100%;"><?=$rec->completion_date;?></span>
                      </button>

                    </h6>
                  </div>
                  <div id="collapsePending1<?=$pending;?>" class="collapse" aria-labelledby="headingPending1<?=$pending;?>" data-parent="#accordionPending">
                    <div class="card-body">
                        <ul>
                        <li class="list-item">
                            <div>
                            <h6>Assign to: </h6>
                              <?php
                                 foreach($rec->resources as $emp_rec){
                                 ?>

                              {{$emp_rec->employee_name}}
                                <img src="http://cmstest.bnu.edu.pk/assets/staffimages/<?=$emp_rec->imagename;?>" width="20px" height="20px" style="border-radius:16px">

                             <?php
                                 }
                                ?>
                            </div>
                            </li>
                        </ul>
                       <ul>
                       <?php if(count($rec->task_list)>0){;?>
                       </br>
                        <h6> Task list </h6>
                       <?php
                        }
                         foreach($rec->task_list as $task_list){

                            ?>
                            <li class="list-item">
                            <div>
                              {{$task_list->tm_description}}</br></br>

                            </div>
                            </li>

                            <?php
                           }
                        ?>
                            <!-- <li class="list-item">
                            <div>Item 2</div>
                            </li>
                            <li class="list-item">
                            <div>Item 3</div>
                            </li> -->
                        </ul>
                    </div>
                  </div>
                </div>
              </div>
            </li>
            <?php
             }
                  }
            ?>


          </ul>
        </div>
      </div>

    </div>
  </div>


<?php }
} ?>




















        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
            .22..


            <div class="col-sm-12 col-md-4">
        <div class="card task-card">
          <div class="card-header">
            <h5>Completed</h5>
          </div>
          <ul class="list-group list-group-flush">
            <!-- <li class="list-group-item">
              <div class="accordion" id="accordionCompleted">
                <div class="card">
                  <div class="card-header" id="headingCompleted1">
                    <h6 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#collapseCompleted1" aria-expanded="true" aria-controls="collapseCompleted1">
                        Task 4
                      </button>
                    </h6>
                  </div>
                  <div id="collapseCompleted1" class="collapse" aria-labelledby="headingCompleted1" data-parent="#accordionCompleted">
                    <div class="card-body">
                      Task 4 description goes here.
                    </div>
                  </div>
                </div>
              </div>
            </li> -->
            <?php
          $completed=1;
          foreach($tasks as $rec){
          $completed++;
          if($rec->task_type=='Completed'){

          ?>
          <li class="list-group-item">
              <div class="accordion" id="accordionCompleted">
                <div class="card">
                  <div class="card-header" id="headingCompleted1<?=$completed;?>">
                    <h6 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#collapseCompleted1<?=$completed;?>" aria-expanded="true" aria-controls="collapseCompleted1">
                        <?=$rec->tm_title;?> <span style="padding-left: 100%;"><?=$rec->completion_date;?></span>
                      </button>

                    </h6>
                  </div>
                  <div id="collapseCompleted1<?=$completed;?>" class="collapse" aria-labelledby="headingCompleted1<?=$completed;?>" data-parent="#accordionCompleted">
                    <div class="card-body">
                        <ul>
                        <li class="list-item">
                            <div>
                            <h6>Assign to: </h6>
                              <?php
                                 foreach($rec->resources as $emp_rec){
                                 ?>

                              {{$emp_rec->employee_name}}
                                <img src="http://cmstest.bnu.edu.pk/assets/staffimages/<?=$emp_rec->imagename;?>" width="20px" height="20px" style="border-radius:16px">

                             <?php
                                 }
                                ?>
                            </div>
                            </li>
                        </ul>
                       <ul>
                       <?php
                         foreach($rec->task_list as $task_list){

                            ?>
                            <li class="list-item">
                            <div>
                              {{$task_list->tm_description}}</br></br>

                            </div>
                            </li>

                            <?php
                           }
                        ?>
                        </ul>
                    </div>
                  </div>
                </div>
              </div>
            </li>
            <?php
             }
                  }
            ?>
          </ul>
        </div>
      </div>

                </div>
             </div>
        </div>
    </div>
      <!-- /////////////////////////////// -->
      </div>
    <div>

    <div class="div_row" id="taskList_data" > </div>

@endsection


@section('javascript')
<script>
    $(document).ready(function() {
        $('.select2').select2();

    });

    $("#staff").change(function(){

          load_taskGrid();
        });

    function load_taskGrid() {

      $.ajax({
            type:'GET',
            url:'{{ URL::route('staff/gettaskboard') }}',
            success:function(data){

                $("#taskList_data").html(data);
                // $("#primary").html(data).fadeIn('slow');
            }
        });
    };

    $(document).ready(function() {
  $(".assignToMessagesBtn").on("click", function() {
    // let data = $("#messageForm").serializeArray();
            var bi_form = document.querySelector('#messageForm');
            var fd      = new FormData(bi_form);
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
            type: 'POST',
            url: '{{URL::route('staff/assign-to-message')}}',
            data: fd,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Authorization": 'Bearer {{ auth()->user()->api_token }}',
                "Accept": "application/json"
                            },
                success: function(data) {
                    if (data.message == 1) {
                        $('#successMsg').html('Message Sent Successfully');
                        $('#successMsg').css('color','green');
                        setTimeout(function() {
                            $('#successMsg').html('');
                        }, 2000);
                    // alert('Status Updated');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
  });

  $(".sendMessageButton").on("click", function() {
    var tmId = $(this).data("tmid");
    // Set the tmId value in the hidden input element
    $("#taskIdInput").val(tmId);
  });

});


</script>
@stop
