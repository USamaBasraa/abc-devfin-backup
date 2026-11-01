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

  <?php
  $completed = 0 ;
  $pending = 0 ;

    foreach($obj_summary as $rec_sum):
        if($rec_sum->tm_status=="Completed"){
          $completed = $rec_sum->tr;
        }
        if($rec_sum->tm_status=="Pending"){
          $pending = $rec_sum->tr;
        }
    endforeach;   
  ?>
<!--Row-->
<div class="row">
							<div class="col-xl-12 col-md-12 col-lg-12">
								<div class="row">
									<div class="col-xl-4 col-lg-4 col-md-12">
										<div class="card">
											<div class="card-body">
												<svg class="card-custom-icon text-success icon-dropshadow-success" x="1008" y="1248" viewBox="0 0 24 24"  height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
													<path opacity=".0" d="M3.31,11 L5.51,19.01 L18.5,19 L20.7,11 L3.31,11 Z M12,17 C10.9,17 10,16.1 10,15 C10,13.9 10.9,13 12,13 C13.1,13 14,13.9 14,15 C14,16.1 13.1,17 12,17 Z"></path>
													<path d="M22,9 L17.21,9 L12.83,2.44 C12.64,2.16 12.32,2.02 12,2.02 C11.68,2.02 11.36,2.16 11.17,2.45 L6.79,9 L2,9 C1.45,9 1,9.45 1,10 C1,10.09 1.01,10.18 1.04,10.27 L3.58,19.54 C3.81,20.38 4.58,21 5.5,21 L18.5,21 C19.42,21 20.19,20.38 20.43,19.54 L22.97,10.27 L23,10 C23,9.45 22.55,9 22,9 Z M12,4.8 L14.8,9 L9.2,9 L12,4.8 Z M18.5,19 L5.51,19.01 L3.31,11 L20.7,11 L18.5,19 Z M12,13 C10.9,13 10,13.9 10,15 C10,16.1 10.9,17 12,17 C13.1,17 14,16.1 14,15 C14,13.9 13.1,13 12,13 Z"></path>
												</svg>
												<p class=" mb-1 ">All Taks</p>
												<h2 class="mb-1 font-weight-bold"><?php echo $completed+$pending;?></h2>
											
												<div class="progress progress-sm mt-3 bg-success-transparent">
													<div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 78%"></div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-xl-4 col-lg-4 col-md-12">
										<div class="card">
											<div class="card-body">
												<svg class="card-custom-icon text-secondary icon-dropshadow-secondary" x="1008" y="1248" viewBox="0 0 24 24"  height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
													<path opacity=".0" d="M12.07,6.01 C8.2,6.01 5.07,9.14 5.07,13.01 C5.07,16.88 8.2,20.01 12.07,20.01 C15.94,20.01 19.07,16.88 19.07,13.01 C19.07,9.14 15.94,6.01 12.07,6.01 Z M13.07,14.01 L11.07,14.01 L11.07,8.01 L13.07,8.01 L13.07,14.01 Z"></path>
													<path d="M9.07,1.01 L15.07,1.01 L15.07,3.01 L9.07,3.01 L9.07,1.01 Z M11.07,8.01 L13.07,8.01 L13.07,14.01 L11.07,14.01 L11.07,8.01 Z M19.1,7.39 L20.52,5.97 C20.09,5.46 19.62,4.98 19.11,4.56 L17.69,5.98 C16.14,4.74 14.19,4 12.07,4 C7.1,4 3.07,8.03 3.07,13 C3.07,17.97 7.09,22 12.07,22 C17.05,22 21.07,17.97 21.07,13 C21.07,10.89 20.33,8.93 19.1,7.39 Z M12.07,20.01 C8.2,20.01 5.07,16.88 5.07,13.01 C5.07,9.14 8.2,6.01 12.07,6.01 C15.94,6.01 19.07,9.14 19.07,13.01 C19.07,16.88 15.94,20.01 12.07,20.01 Z"></path></svg>
												<p class=" mb-1 ">Pending </p>
												<h2 class="mb-1 font-weight-bold"><?php echo $pending;?></h2>
											
												<div class="progress progress-sm mt-3 bg-secondary-transparent">
													<div class="progress-bar progress-bar-striped progress-bar-animated bg-secondary" style="width: 58%"></div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-xl-4 col-lg-4 col-md-12">
										<div class="card">
											<div class="card-body">
												<svg class="card-custom-icon text-primary icon-dropshadow-primary" x="1008" y="1248" viewBox="0 0 24 24"  height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
													 <path d="M17.65,6.35 C16.2,4.9 14.21,4 12,4 C7.58,4 4.01,7.58 4.01,12 C4.01,16.42 7.58,20 12,20 C15.73,20 18.84,17.45 19.73,14 L17.65,14 C16.83,16.33 14.61,18 12,18 C8.69,18 6,15.31 6,12 C6,8.69 8.69,6 12,6 C13.66,6 15.14,6.69 16.22,7.78 L13,11 L20,11 L20,4 L17.65,6.35 Z"></path></svg>
												<p class=" mb-1 ">Completd</p>
												<h2 class="mb-1 font-weight-bold"><?php echo $completed;?></h2>
												
												<div class="progress progress-sm mt-3 bg-primary-transparent">
													<div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 58%"></div>
												</div>
											</div>
										</div>
									</div>
                </div> 
              </div>
</div>                 
						
						
								
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
         <div class="row">
            <div class="col-sm-12">
                <label for="Task Type">Task Type:</label>
                <select name="staff1" id="staff1" onchange="fun(this)" class="form-control" name="task_type" id="task_type">
                    <option selected="selected">--Select--</option>
                    <option >ToDo</option>
                    <option>Task</option>
                </select>
                <input type="hidden" id="entry_user_id" name="entry_user_id" value="<?=auth()->user()->employee_id;?>">
            </div>
        </div>
    </div>
  </div>


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




    <div class="div_row" id="taskList_data" > </div>

@endsection
@section('javascript')
<script>
  $(document.body).on('click','.otherCatches',function(event){

    var selectedValue = $(event.currentTarget).text();
    var id = $(this).attr("data-todoID");
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var formdata = new FormData();
    formdata.append('selValue',selectedValue);
    formdata.append('id',id);

    $.ajax({
  type: 'POST',
  url: '{{URL::route('staff/update-task-status')}}',
  data: formdata,
  processData: false,
  contentType: false,
  headers: {
    "X-CSRF-TOKEN": csrfToken,
    "Authorization": 'Bearer {{ auth()->user()->api_token }}',
    "Accept": "application/json"
  },
  success: function(data) {
    if (data == true) {
      //alert($("#staff1").val());
      fun($("#staff1").val());
      alert('Status Updated');
    }
  },
  error: function(xhr, status, error) {
    console.error(error);
  }
});

});

    $(document).ready(function() {
        $('.select2').select2();
    });

    // $("#staff1").change(function(event){

    //       load_taskGrid();
    //     });

    function fun(sel) {
      $("#taskList_data").html('');
      //alert(sel.value);
      var getSelctedValue = $("#staff1").val();
      $.ajax({
            type:'GET',
            url:'{{ URL::route('staff/gettaskboard') }}/?id='+getSelctedValue,
            success:function(data){

                $("#taskList_data").html(data);
                // $("#primary").html(data).fadeIn('slow');
            },
            error: function(xhr, status, error) {
                // Display error message or perform any other actions
               // alert('An error occurred while saving the task.');
                $("#taskList_data").html('<p>No Record Found</p>');
            }
        });
    };




</script>
@stop
