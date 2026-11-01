@extends('layouts.app',['pageTitle' => ''])

@section('content')
<style>
        .delete-button {
        background-color: transparent;
        border: none;
        }
        .row {
    display: flex;
    flex-wrap: wrap;
    }
    .card_header{
        border: 1px solid black;
        border-radius:10px 10px 0px 0px;
        padding:1%; 
        font-size:18px;
        margin:2% 5% 0% 5%;
        background-color:#D2D6F1;
    }
    .bottom_tr{
        border: 1px solid black;
        border-radius:0px 0px 10px 10px;
        /* padding:5%; */
        font-size:18px;
        margin:0% 5% 5% 5%;
    }
    .resources_div{
        border-bottom:solid 1px black;
        padding:2px;
        font-size:18px;
    }
    .for_li{
         /* border:solid 1px green;  */
         margin-left:1%;
         font-size: 20px !important;
    }

    .description-wrapper {
        float: left;
        width: 70%;
        margin-left:3%;
        /* border:solid 1px red; */
    }
    .button-wrapper {
    float: left;
    width: 22%;
    /* padding-top:2%; */
    text-align: center;
    /* border:solid 1px green; */
    }
    label {
        font-weight: bold;
        font-size: 18px;
        color:#4454C3 !important;
    }
    .btn_custome{
        height:80% !important;
        width:90% !important;
    }
    @media (min-width: 768px) {
    .description-wrapper {
        width: 80%;
        margin-left:1.5%;
        float: left;
        /* border:solid 1px black; */
    }

    .button-wrapper {
        width: 16%;
        text-align: left;
        float: left;
        /* border:solid 1px black; */
        padding-top:3%;
    }
    .check_uncheck_all_tr{
         border-bottom:solid 2px black;
         font-size:20px;
         color:green;
    }
    }
  </style>


<div style="background-color:white;margin-left:auto;margin-right:auto;width:100%;">
    <div style="border:solid 0px black;margin-left:auto;margin-right:auto;width:90%;">

    <table border="0" width="100%" cellpadding="0px">
                <tr>
                    <td width="40%">
                        @if(isset($_REQUEST['category_name']) && $_REQUEST['category_name'] != "")
                                <label for="task_title"><?=$_REQUEST['category_name'];?></label>      
                        @endif 
                    </td>
                    <td width="50%"> <h4>Task Management</h4> </td>
                    <td>
                    </td>
                    <td>
                    @if(isset($_REQUEST['id']) && $_REQUEST['id'] != "")
                            
                            <label for="task_title"></label>      
                            <button type="button"  class="btn btn-primary" id="toggleToDoForm" style="display:none">
                                <i id="iconToggleToDoForm" class="fa fa-plus"></i>
                            </button>
                    @endif        
                    </td>        
                </tr>
            </table>

           
        <!-- <div class="jumbotron text-center" style="padding: 0rem;margin-bottom: 0rem;">
            <h1>Task Management</h1> 
        </div> -->
        <!--  tab bar start--------------------- -->
            <div class="div_web">
                <div class="container">
                    <div class="panel panel-primary tabs-style-3">
                            <div class="tabs-menu">
                                <!-- Tabs -->
                                <ul class="nav panel-tabs" id="ul_show_hide">
                                <li style="widht:50% !important;">
                                        <a href="#taball" id="aidall" value="all" class="active" data-toggle="tab" onclick="fetchData('All','Latest')">
                                        <i class="fa fa-cube"></i>
                                            All
                                        </a>
                                    </li>
                                    @if(auth()->user()->employee_id!="8996") 
                                    <li style="widht:50% !important;">
                                        <a href="#tabcomplete" id="aidcomplete" value="complete" data-toggle="tab" onclick="fetchData('AssignedMe','Latest')">
                                        <i class="fa fa-cube"></i>
                                            Received Task
                                        </a>
                                    </li> 
                                    @endif
                                    <li style="widht:50% !important;">
                                        <a href="#tabcomplete" id="aidcomplete" value="complete" data-toggle="tab" onclick="fetchData('AssignedOthers','Latest')">
                                        <i class="fa fa-cube"></i>
                                            Assigned Task
                                        </a>
                                    </li> 
                                @php
                                $taskController = new \App\Http\Controllers\Grader\TaskManagement\TaskManagementController();
                                $taskCategory= $taskController->getTaskCategory();
                                $li_width = 100/(count($taskCategory)+1);
                                @endphp
                                @foreach ($taskCategory as $rec_category)
                                <?php
                                if($rec_category->category_title=='BNU'):
                                $checked="active";
                                else:
                                    $checked=" ";
                                endif;
                                ?>
                                <!-- <li style="widht:50% !important;">
                                    <a href="#tab{{$rec_category->id}}"   id="aid{{$rec_category->id}}" value="{{$rec_category->id}}" data-toggle="tab" onclick="fetchData({{$rec_category->id}},'Latest')">
                                        <i class="fa fa-cube"></i>{{$rec_category->category_title}}
                                    </a>
                                    </li> -->
                                @endforeach 
                                    <li style="widht:50% !important;">
                                        <a href="#tabcomplete" id="aidcomplete" value="complete" data-toggle="tab" onclick="fetchData('Completed','Latest')">
                                        <i class="fa fa-cube"></i>
                                            Completed
                                        </a>
                                    </li>  

                                    <li style="widht:55% !important;padding-left:10px">
                                        <button type="button"  class="btn btn-primary" id="toggleToDoForm" style="display:1none">
                                            <i id="iconToggleToDoForm" class="fa fa-plus"></i>
                                        </button>
                                    </li>  
                                    
                               
                                </ul>
                            </div>
                            <div class="col-sm-12" style="padding-top: 15px;">
                                <div class="row">
                                    
                                </div>
                            </div> 
                            <div class="col-md-12" style="padding-top: 15px;">
                                <div class="row">
                                   <div class="col-md-2">
                                        <div class="form-check">
                                        <label for="sortDropdown">Filter by:</label> 
                                        </div> 
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            Urgent: <input type="checkbox" name="selection[]" checked value="Urgent" onchange="handleCheckboxChange(this)">
                                        </div> 
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            Long Term: <input type="checkbox" name="selection[]" checked value="LongTerm" onchange="handleCheckboxChange(this)">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-check">  
                                            <label for="sortDropdown">Sort by:</label>
                                            <select  id="sortDropdown">
                                                <option value="Latest">Latest to Oldest</option>
                                                <option value="OldestFirst">Oldest to Latest</option>
                                            </select>                      
                                        </div> 

                                     </div>
                                </div>
                            </div>    



                            <!-- <div class="row" style="border:solid 1px green">
                                <div class="col-4 col-sm-4 col-md-4  col-lg-4" style="text-align:right">
                                    Selection By:
                                </div>
                                <div class="col-2 col-sm-2 col-md-2  col-lg-2">
                                    Urgent: <input type="checkbox" name="selection[]" checked value="Urgent" onchange="handleCheckboxChange(this)">
                                </div>
                                <div class="col-2 col-sm-2 col-md-2  col-lg-2">
                                    Long Term: <input type="checkbox" name="selection[]" checked value="LongTerm" onchange="handleCheckboxChange(this)">
                                </div>
                                <div class="col-4 col-sm-4 col-md-4  col-lg-4">
                                    &nbsp;  
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div> 
        <!-- -------------tab end  -->
        <div class="page-body" id="toDoDivForm" style="display:none">
            <div class="col-sm-12">
                <form id="taskForm" action="{{url('dboard_submit') }}" method="POST">
                    @csrf
                    <div class="container">
                    <!-- ----------- -->
                        <div class="row"  id="show_staff_pannel" >
                            <div class="col-sm-12">
                                <label for="staff">School / Department :</label>
                                <select name="staff" id="staff" class="form-control select2" style="width:100% !important;">
                                    <option value="0|<?=auth()->user()->employee_id;?>|3" style="background-color: yellow;font-weight: bold;">Select School / Department / Staff:</option>
                                    @foreach ($staffMembers as $staff)
                                        <option value="{{$staff->parent_id . '|' . $staff->id. '|' . $staff->ordername }}">
                                            {{ $staff->name }} 
                                            @if(strlen(trim($staff->code))>0)
                                                ({{$staff->code}})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                    </div>    
                            
                    <div class="row" id="date_time_desc_group"  style="display:none">
                        <div class="col-sm-12"> 
                            <div class="row" >
                                <div class="col-sm-6">
                                    <label for="task_title">Task / Project :</label>
                                    <input type="textbox" name="description" class="form-control" id="description" >
                                    <input type="hidden" name="task_category_id" id="task_category_id" value="13">
                                    <input type="hidden" name="task_category_id1" id="task_category_id1" value="13">
                                    <input type="hidden" name="task_status" id="task_status" value="approval">

                                    <input type="hidden" id="edit_tm_id" name="edit_tm_id" >
                                </div>
                            <div class="col-sm-6">
                                <label for="completion_date">Reporting Date:</label>
                                <input type="date" name="completion_date" class="form-control" id="completion_date" onchange="handleDateSelection()">
                            </div>
                            </div>
                    </div> 
                    <div class="col-sm-12">  
                        <div class="row" id="type_desc_button">
                               <div class="col-sm-6">
                                    <label for="description">Description:</label>
                                     <textarea name="task_title" id="task_title" class="form-control" placeholder="Please enter something "  rows="2" cols="40"></textarea>
                                </div>
                            <div class="col-sm-6">
                                <label for="completion_time">Reporting Time:</label>
                                <select name="completion_time" class="form-control" id="completion_time">
                                    <?php
                                        $start_time = strtotime('00:00');
                                        $end_time = strtotime('23:00');
                                        $interval = 60 * 30; // 1 hour interval

                                        for ($time = $start_time; $time <= $end_time; $time += $interval) {
                                            $display_time = date('h:i A', $time);
                                            $option_value = date('H:i', $time);
                                            $selected = ($option_value === '09:00') ? 'selected' : '';

                                            echo '<option value="' . $option_value . '" ' . $selected . '>' . $display_time . '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>        
                </div>  
                <div class="col-sm-12" style="padding-top: 15px;">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tasktype"  id="radio1" checked="checked" value="Longterm">
                            <label class="form-check-label" for="radio1">
                          Long Term
                            </label>
                        </div> 
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tasktype"  id="radio1"  value="Urgent">
                            <label class="form-check-label" for="radio1">
                           Immediate
                            </label>
                        </div>
                    </div>
                </div>
                    <!-- <div class="form-check">
                        <input class="form-check-input" name="tasktype" type="checkbox" id="longTermCheckbox">
                        <label class="form-check-label" for="longTermCheckbox"><span id="chkboxTxt">Mark Task as Long Term</span></label>
                    </div> -->
                </div>
                <div class="row" id="only_button" style="display:non1e">
                    <div class="col-sm-12">
                        <br>
                        <!-- <button type="submit" class="btn-block btn btn-success" ><i class="fe fe-check mr-2"></i>Submit Task</button> -->
                        <button type="submit" class="btn-block btn btn-success">
                            <i class="fe fe-check mr-2"></i>Submit Task
                        </button>
                        
                        <br>    
                    </div>
                </div>    
                </form>
            </div>
        </div>
    </div> 
</div>


<div class="row">
        <div id="tab_innerdivabc" style="width:100%; margin-left:auto; margin-right:auto;border:1px solid white;"></div> 
</div>
@endsection
@section('javascript')
<script>
$('#staff').select2({
  allowClear: true,
  minimumResultsForSearch: Infinity
});
function rollBack(id){
    //alert('Rollback Successfully Done');
    $.ajax({
        url: '{{ route("rollBack") }}?id='+id,
        type: 'GET',
        
        success: function(response) {
            // Set a timeout to close the loader after two seconds
            fetchData($("#task_category_id1").val(),'Latest','All');
            
        },
        error: function(xhr, status, error) {
            // Request failed, handle the error
            console.log("Error: " + error);
        }
    });
}
function confirmDelete(id){
    var confirmed = confirm("Are you sure you want to Delete this task?");

if (confirmed) {
    $.ajax({
        url: '{{ route("deleteTask") }}?id='+id,
        type: 'GET',
        
        success: function(response) {
            // Set a timeout to close the loader after two seconds
            fetchData($("#task_category_id").val(),'Latest','All');
            
        },
        error: function(xhr, status, error) {
            // Request failed, handle the error
            console.log("Error: " + error);
        }
    });
}
   
}
function editTodo(id){
    
 
    if ($("#toDoDivForm").is(':visible')) {
      console.log("The div is currently visible.");
    } else {
        $("#toDoDivForm").toggle();
    }
    $("#iconToggleToDoForm").toggleClass("fa-plus fa-minus");
    var btn=$('#edit_tm_task_priority'+id).val();
    $("input[name='tasktype'][value='"+btn+"']").prop("checked", true);

    $('#description').val($('#edit_tm_task_title'+id).val());
    $('#edit_tm_id').val(id);
    $('#staff').val($('#edit_task_client'+id).val()+'|'+$('#edit_task_client'+id).val()+'|3').trigger('change');
    $('#task_title').val($('#edit_tm_description'+id).val());
    $('#completion_date').val($('#edit_tm_completion_date'+id).val());
}


    function handleCheckboxChange(checkbox) {
    var checked = $('input[name="selection[]"]:checked').map(function() {
        return $(this).val();
    }).get();

    //alert(checkbox);

    fetchData($("#task_category_id1").val(),'Latest',checked); 
}
    $('#sortDropdown').on('change', function() {
    var selectedValue = $(this).val();
    // Perform actions based on the selected value
    console.log('Selected value: ' + selectedValue); 
    fetchData('All',selectedValue);  
    
});
    function submitTask() {

    var submitButton = document.querySelector(".btn-success");
    // Disable the submit button
    submitButton.disabled = true;
    // Display the "Data is processing" message
    submitButton.innerHTML = '<i class="fe fe-loader mr-2"></i>Data is processing...';
    // Simulate a delay of 2 seconds (2000 milliseconds)
    setTimeout(function() {
        // Enable the submit button
        submitButton.disabled = false;
        // Display the "Data saved successfully" message
        submitButton.innerHTML = '<i class="fe fe-check mr-2"></i>Data saved successfully';
        // Reset the message after 2 seconds
        setTimeout(function(event) {
            //event.preventDefault();
            submitButton.innerHTML = '<i class="fe fe-check mr-2"></i>Submit Task';  
           // document.getElementById('taskForm').submit();
        }, 2000);
    }, 2000);
}



// Add event listener for Enter key press
document.getElementById("description").addEventListener("keydown", function(event) {
  if (event.keyCode === 13) { // Check if Enter key was pressed
    event.preventDefault(); // Prevent the default Enter key behavior (new line)
    addValue(); // Call the addValue() function
  }
});



 // AJAX function to submit the form
 $(document).ready(function() {

      $("#toggleToDoForm").click(function() {
        $("#toDoDivForm").toggle();
        // alert($(this).is(':visible'));
        // $("#ul_show_hide").hide();        
        $("#iconToggleToDoForm").toggleClass("fa-plus fa-minus");
        //  $("#tab_innerdivabc").hide();        
    });

   // alert($("#task_category_id").val());
    fetchData('All','Latest'); 

    $("#longTermCheckbox").change(function() {
            var badgeText = $("#badgeText");
           
            if ($(this).is(":checked")) {              
                badgeText.html("<strong>Long term</strong>");
                $('#chkboxTxt').html('');
                $('#chkboxTxt').html('<strong>Mark as Long term</strong>');               
            } else {
                badgeText.html("<strong>Urgent</strong>");
                $('#chkboxTxt').html('');
                $('#chkboxTxt').html("<strong>Mark as Urgent</strong>");
            }
        });
        $('.select2').select2();
    
        $('#user_and_me_div').hide();
        $('#date_time_desc_group').show();





// Handle change event of the staff select dropdown
//  $('#staff').change(function() {
//     var selectedStaffId = $(this).val();
//     fetchData(selectedStaffId);
//     $('#date_time_desc_group').show();
// });
$('#task_type').change(function() {
            $('#userDataBody').empty();
           
            $('#type_desc_button').show();
            $('#report_date_time').show();

           // $('#show_staff_pannel').show();
           // $('#user_and_me_div').show();
           // $('#only_button').show();
            
            
          
                checkbox.checked = false;
                $('#show_staff_pannel').show();
                $('#user_and_me_div').show();
           
});



$('#taskForm').submit(function(event) {
   
    event.preventDefault();           
    var form = $(this);
    var url = form.attr('action');
    var method = form.attr('method');
    var formData = new FormData(form[0]);
    //console.log(formData);
    //alert('ok');
   
    // Get the selected staff members
    var selectedStaffMembers = [];
    $('#selectedStaffMembers').children('div').each(function() {
        var staffId = $(this).attr('data-staff-id');
        alert(staffId);
        selectedStaffMembers.push(staffId);

    });
    console.log(selectedStaffMembers);
    // Add the selected staff members to the form data
    formData.append('selectedStaffMembers', selectedStaffMembers);
  
   


    // Get the values from the value list
    var listItems = $('#staff').val();//document.querySelectorAll("#valueList li");
    var values = [];
    for (var i = 0; i < listItems.length; i++) {
        var listItem = listItems[i];
        var value = listItem;
        values.push(value);
    }

    // Add the values from the value list to the form data
    var hasDescription = [];
    for (var j = 0; j < 1; j++) {
        // alert(values[0]);
        hasDescription.push(values[j]);
    }
    
    //alert($('#staff').val());
    if (hasDescription==0) {
        event.preventDefault();
        alert('Please select School/Department or Person in field.');
        return false;
      }
      if ($('#description').val()=='') {
        event.preventDefault();
        alert('Please enter task field.');
        return false;
      }
      if ($('#completion_date').val()=='') {
        event.preventDefault();
        alert('Please enter reporting date field.');
        return false;
      }
    // Send the AJAX request
    $.ajax({
        url: url,
        type: method,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {  
            submitTask();
                  $("#edit_tm_id").val('');
                   $("#task_title").val('');
                   $('#completion_date').val('');
                   $('#completion_date').val('');
                   $('#staff').val(0+'|'+'<?=auth()->user()->employee_id;?>'+'|3').trigger('change');
           
                   if ($("#toDoDivForm").is(':visible')) {
                    $("#toDoDivForm").toggle();
                    $("#iconToggleToDoForm").toggleClass("fa-minus fa-plus");
                    } 
                   setTimeout(function() {
                    if (response === "NOUSER") {
                        alert("Please select at least one user.");
                    } else if (response === "NOTASK") {
                        alert("Please enter at least one task.");
                    } else {
                        $("#description").val('');
                        fetchData($("#task_category_id1").val(),'Latest');  
                        var valueList = document.getElementById('valueList');
                        valueList.innerHTML = '';

                        var checkboxes = document.querySelectorAll('input[name="user_id[]"]');
                        checkboxes.forEach(function(checkbox) {
                            //checkbox.checked = false;
                        });
                    }
                    // alert("Task Saved Successfully");
                   //$("#taskForm")[0].reset();
                 



                   
                }, 2000);

        },
        error: function(xhr, status, error) {
            // Display error message or perform any other actions
            alert('An error occurred while saving the task.');
        }
    });
});
});

// -------------------
function fetchData(categoryId,SortID,chk) {

           var a_href = $("#aid"+categoryId).attr("value");      

           
            $("#task_category_id1").val(categoryId);
            const tabContent = $('#tab_innerdivabc');
            tabContent.empty(); // Empty the div before appending new data
            $("#tab_innerdivabc").show();
         //   $("#toDoDivForm").hide();
            $.ajax({
                url: '/staff/showTaskList?id=' + categoryId+'&type=Task&sortId='+SortID+'&chkbox='+chk,
                method: 'GET',
                success: function(data) {
                tabContent.html(data); // Append the fetched data to the tab content element
                // initializeDataTable(); // Re-initialize DataTable after content is appended

                    $("#tab_innerdivabc").html(data);
                    
                },
                error: function(error) {
                console.error('Error:', error);
                }
            });
        }

        function confirmMarkCompleted(taskId) {
                // Ask for confirmation using the built-in confirm() function
                var confirmed = confirm("Are you sure you want to complete this task?");

                if (confirmed) {
                    // User confirmed, proceed with marking the task as completed
                    markCompleted(taskId);
                }
        }
        function markCompleted(taskId) {
                // Show the loader
               

                // Set the data to send in the request body
                var data = {
                    taskId: taskId,
                    _token: '{{ csrf_token() }}'
                };

                // Make the AJAX request
                $.ajax({
                    url: '{{ route("updateTask") }}',
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        // Set a timeout to close the loader after two seconds
                        fetchData($("#task_category_id").val(),'Latest');  
                        showLoader(taskId);
                        setTimeout(function() {
                            // Hide the loader
                            hideLoader(taskId);

                            // Handle the response if needed
                            console.log(response);
                            hide_row(taskId);
                        }, 2000);
                    },
                    error: function(xhr, status, error) {
                        // Request failed, handle the error
                        console.log("Error: " + error);
                    }
                });
            }
</script>
@stop
