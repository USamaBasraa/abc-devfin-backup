
@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
.invisible { display: none; }
</style>
<style>
    .column-link {
        cursor: pointer; /* Set cursor style to pointer */
    }
    div{
        border: 0px solid black;
    }
    .badge {
        display: inline-block;
        padding: 5px 10px;
        background-color: red;
        color: white;
        border-radius: 50%;
    }
    div{
        border: 0px solid black;
    }
    span{
        border: 0px solid black;
    }
    .accordion {
  background-color: #eee;
  color: #444;
  cursor: pointer;
  padding: 18px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
  transition: 0.4s;
}

.active, .accordion:hover {
  background-color: #ccc; 
}

.panel {
  padding: 0 18px;
  display: none;
  background-color: white;
  overflow: hidden;
}
.badge {
        display: inline-block;
        padding: 2px 8px;
        background-color: red;
        color: white;
        border-radius: 30%;
    }
    .status-btn {
      width: 120px; /* Adjust the width as needed */
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>


    $(document).ready(function () {
        // Initialize the accordion behavior
        $(".accordion").click(function () {
            // Get the id from the data-id attribute
            var id = $(this).data("id");
            // Increase the count in the badge
            var badge = $(this).find(".badge");
            badge.text(parseInt(badge.text()) );

            // Perform any other actions you need here
            // For example, you can open a modal or make an AJAX request
            // to load additional content related to the clicked item.
        });
    });
</script>
    <div class="card e-panel" style="margin:10px;width:98%">
        <div class="card-header">    
                <h3 align ="center"> E-Portal Requests</h3><br>
            </div>     
            <div class="row">   
                <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                    <div class="tabs-menu ">
                        <!-- Tabs -->
                        <ul class="nav panel-tabs">
                            <li class=""><a href="#lectures" onclick="pending()"     data-toggle="tab">
                                <i class="fa fa-laptop"></i> Pending
                            </a></li>
                            <li><a href="#material" onclick="completed()" class="active"  data-toggle="tab">
                                <i class="fa fa-book"></i> Completed
                            </a></li>
                        
                        </ul>
                    </div>
                </div>
            </div>  
        </div>
        <div class="card-block" style="margin:10px;width:98%">
            <?php
            // Initialize an associative array to store rows grouped by id and username
            $groupedRows = [];
            $penStatus = '';
            if(count($res) > 0){
            foreach ($res as $row) {
                $id = $row->id;
                $username = $row->username;
                $penStatus = $row->er_request_status;
                // Add the row to the groupedRows array, grouped by id and username
                if (!isset($groupedRows[$id][$username])) {
                    $groupedRows[$id][$username] = [];
                }
        
                $groupedRows[$id][$username][] = $row;
            }
        
            foreach ($groupedRows as $id => $usernameRows) { ?>
                <button class="accordion" data-id="<?= $id ?>"><?php echo reset($usernameRows)[0]->title ?> <span class="badge">1</span></button>
                <div class="panel">
                    <?php foreach ($usernameRows as $rows) { ?>
                        <div class="row">
                            <?php foreach ($rows as $row) { ?>
                                <div class="col-md-12">
                                    <div class="p-4" style="background-color:#f0f0f0;color:black !important">
                                        <b>Name:</b> <?= $row->full_name ?>   <b>Roll #:</b> <?= $row->username ?>  <b>School:</b> <?= $row->school ?> <b>Dept:</b> <?= $row->programs ?>
        
                                        <div style="float:right;padding-bottom:10px;">

                                            <button class="btn btn-success" onclick="openModal('<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>')" type="button">Approval Status</button>
                                        </div>
        
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php }
            }else{ ?>

                <div style="font-size: 18px; text-align: center; padding: 20px; background-color: #f0f0f0; border: 1px solid #ddd; margin: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);">
                    No Students Requests Completed Yet
                </div>

            <?php } ?>
                
        </div>       
        <div class="modal-footer">
                <!-- <button type="button" class="btn btn-success"  >Mark as Complete</button>
                <button type="button" class="btn btn-secondary"  >Recommned to Head</button> -->
        </div>
        </div>
    </div>
    
<!-- model start  -->
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#4454c3;color: white !important;">
                <span style="text-color:white" id="header_modal"></span>
                <button type="button" style="color:white" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="text-align: left;">
             
            </div>
            <form enctype="multipart/form-data" method="post" name="cformdes" id="cformdes">
            <div  class="col-12">
            <div class="form-group">
                    <label for="remarks">Remarks:</label>
                    <input type="text" class="form-control" name="remarks" id="req_remarks">
                </div>
            </div>
                    <div class="modal-footer">
                    <span id="saved_msg_des"></span>
                        <!-- Dropdown button and options -->
                        <div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Mark as 
                            </button>
                            <input type="hidden" id="ecd_id">
                            <div class="dropdown-menu">
                               
                                <a class="dropdown-item" name ="markRollback" id="markRollback">Incomplete</a>
                                <a class="dropdown-item" href="#" id="markRejected" name = "markRejected">Rejected</a>
                            </div>
                        </div>
                    
                      
                    </div>
                    </form>
        </div>
    </div>
</div>
</div>
@stop
@section('javascript')
<script type="text/javascript">


              
function pending(){
    var routeUrl = "{{ route('eportal-dashboard') }}"; // Replace 'my.route' with your route name
    window.location.href = routeUrl;
}

var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}


$('#markRollback').click(function () {

        var id = $('#ecd_id').val();
    
        var bi_form = document.querySelector('#cformdes');
            var fd = new FormData(bi_form);
            fd.append( 'id', id);
        $.ajax({
            method: "POST",
            url: '{{'/api/eportal/rollback'}}',
            data: fd,
            processData: false,
            contentType: false,
            headers: {
                Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                Accept: 'application/json'
            }
            }).done(function(res) {
                $('#saved_msg_des').html(res.message);
                setTimeout(function() {
                   $('#myModal').modal('hide');
                   location.reload();
                    }, 1500);

                

           
            }).fail(function(res) {
            
          
            });
});

function openModalStatus(title){
    $('#application_status').html(title);
    $("#application_status").css("font-weight", "bold");
    // Use jQuery to show the modal
    $("#applicationStatus").modal("show");

}

    
function openModal(jsonString){
    var data = JSON.parse(jsonString); // Parse the JSON string back to an object
    // alert(data.cate_id)
    $('#ecd_id').val(data.cate_id)
// Access the modal element
var modal = document.getElementById("myModal");

// Access the modal's content area
var modalContent = modal.querySelector(".modal-body");
    // Access and set the content of an element with the ID "header_modal"
    $('#header_modal').html(data.full_name + ' ' + data.username);
    // modalContent.innerHTML = "<p>Name: " + data.full_name + "</p><p>Username: " + data.username + "</p>";

// Populate the modal content with data from the JSON object
modalContent.innerHTML = "<p>Name: " + data.full_name + "</p><p>School: " + data.school + "</p><p>Program: " + data.programs + "</p><p>Contact: " + data.mobile + "</p><p>Decription: " + data.description + "</p>";
// Show the modal
$(modal).modal("show");
    console.log(row);
    // var username = row.username;
    // alert(row.name);
      $('#header_modal').html(data.full_name + data.username);
      $("#header_modal").css("font-weight", "bold");
      // Use jQuery to show the modal
    //   $("#myModal").modal("show");

  



   
}





</script>
@stop

