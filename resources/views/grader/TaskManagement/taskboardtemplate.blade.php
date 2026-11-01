<style type="text/css">
div{
  border:solid 0px green;
}
</style>

<div style="border:solid 2px black;margin-left:auto;margin-right:auto;width:96%;">

                                              <div class="row"  style=" color:white; background-color: #2C3E50; padding:4px;padding-left: 20px;margin-right:0px;margin-left:0px; ">
                                                    <div class="col-sm-10">
                                                          <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left; line-height: 20px;" >Task Board</h3>
                                                    </div>
                                                    <div class="col-sm-2" style="float:right">
                                                      <button type="button" class="btn btn-danger"><i class="fe fe-link mr-2"></i><a style="color: white;" href="/staff/task-list" >New Task</a></button>
                                                    </div>
                                              </div>

    <div class="panel panel-primary tabs-style-3">
        <div class="tab-menu-heading">
          <div class="tabs-menu ">
            <!-- Tabs -->
            <ul class="nav panel-tabs">
              <li class=""><a href="#tab11" class="active" data-toggle="tab"><i class="fa fa-laptop"></i> Pending</a></li>
              <li><a href="#tab12" data-toggle="tab"><i class="fa fa-tasks"></i> Completed</a></li>
            </ul>
          </div>
        </div>
        <div class="panel-body tabs-menu-body">
          <div class="tab-content">
            <div class="tab-pane active" id="tab11">
              <!-- Tab One start  -->
                    @include('grader.TaskManagement.pendingtask')
              <!-- Tab one End -->
            </div>
            <div class="tab-pane" id="tab12">
               <!-- Tab Two start  -->
                    @include('grader.TaskManagement.completedtask')
              <!-- Tab Two End -->
          </div>
        </div>
        </div>
</div>



<script>



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
                            document.getElementById("messageTextarea").value = '';
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
