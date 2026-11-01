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
.div_web {
    display: block;
    background-color:white;
}
.div_mobile {
    display: none;
    /* background-color:green; */
}
.add_more_sign {
  /* Default padding for web */
  padding: 38px;
}
/* Media query for mobile view */
@media (max-width: 767px) {
    .div_web {
        display: none;
    }
    #task_title_lable{
        display: none;
    }
    .div_mobile {
        display: block;
    }
    .add_more_sign {
  /* Default padding for web */
  padding: 50px;
}
}
.left_div{
    vertical-align:center;
    text-align:center;
    float:left;
    width:30%;
    height:100px;
    margin-left:14%;
    margin-top:2%;
    background-color: #fff;
    border: solid 2px black;
    border-radius: 10px;
}
.right_div{
    vertical-align:center;
    text-align:center;
    float:left;
    width:30%;
    height:100px;
    margin-left:8%;
    margin-top:2%;
    background-color: #fff;
    border: solid 2px black;
    border-radius: 10px;
}
    /* Mobile-specific styles */
    @media (max-width: 600px) {
        table {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            font-size: 14px;
        }

        td {
            padding: 5px;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }

        select {
            width: 100%;
        }
    }

    /* Fancy styles */
    table {
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    td:first-child {
        font-weight: bold;
        padding-right: 10px;
    }

    input[type="checkbox"] {
        transform: scale(1.3);
    }

    select {
        width: 180px;
        border-radius: 4px;
        padding: 5px;
    }
</style>
<div style="background-color:white;margin-left:auto;margin-right:auto;width:100%;border:solid 0px red;">
    <!-- <div style="border-bottom:dotted 2px blue;margin-left:auto;margin-right:auto;width:90%;"> -->
            <table border="0" width="100%" cellpadding="0px" style='margin-bottom: -1px;'>
                <tr>
                    <td width="33%" align="center">
                        @if(isset($_REQUEST['category_name']) && $_REQUEST['category_name'] != "")
                        <span class="badge badge-success"><?=$_REQUEST['category_name'];?></span>
                        @endif 
                    </td>
                    <td width="33%" align="center"> <h4>Todo</h4> </td>
                    <td  width="33%"align="center">
                    @if(isset($_REQUEST['id']) && $_REQUEST['category_name'] !== 'All' && $_REQUEST['category_name'] !== 'Completed')
                            <button type="button"  class="btn btn-outline-success" id="toggleToDoForm">
                                   <i id="iconToggleToDoForm" class="fa fa-plus"></i>
                            </button>
                    @endif
                    </td>        
                </tr>
            </table>
<table border="0" cellspacing="0" cellpadding="0" id="sort_filter_table">
    <tr>
        <td>Urgent:<input type="checkbox" name="selection[]" checked value="Urgent" onchange="handleCheckboxChange(this)"></td>
        <td>Long Term:<input type="checkbox" name="selection[]" checked value="LongTerm" onchange="handleCheckboxChange(this)"></td>
        <td>
            <select id="sortDropdown" class="form-control">
                <option value="">-- Sort By --</option>
                <option value="Latest">Latest to Oldest</option>
                <option value="OldestFirst">Oldest to Latest</option>
            </select>
        </td>
    </tr>
</table>
    <!-- --------------tab bar end----------------- -->
    <div class="page-body p-2 m-2" id="toDoDivForm" style="display:none; border:solid 2px green;">
        <div class="col-sm-12">
            <form id="taskForm" action="{{url('tododboard_submit') }}" method="POST">
                @csrf
                <div class="container" style="border:solid 0px black">     
                    <div class="row" style="display:none">
                        <div class="col-sm-12">
                            <label for="Task Type">Task Type:</label>
                            <select class="form-control" name="task_type" id="task_type">
                                <!-- {{-- <option selected="selected">--Select Activity --</option> --}} -->
                                <option>Task</option>
                                <option selected="selected">ToDo</option>
                            </select>
                            <input type="hidden" id="entry_user_id" name="entry_user_id" value="<?=auth()->user()->employee_id;?>">
                        </div>
                    </div>   
                                      
                    <div class="row" id="date_time_desc_group">    
                        <div class="col-12 col-sm-12 col-md-12  col-lg-12"> 
                                    <table border="0" width="100%">
                                         <tr>
                                            <td>
                                                <input type="text" placeholder="Enter something to do" name="description" class="form-control" id="description" >
                                                <input type="hidden" name="task_category_id" id="task_category_id">
                                            </td> 
                                            <td> 
                                                <button type="button"  class="btn btn-primary" id="toggleButton">
                                                    <i id="iconToggle" class="fa fa-plus"></i>
                                                </button> 
                                            </td>    
                                        </tr>
                                    </table>
                         </div>                           
                    </div> 
                        <div class="col-sm-12" style="display:none" id="advanceOption">
                            <div class="col-sm-12">             
                                <div class="row" >
                                    <div class="col-sm-6">
                                        <label for="completion_time">Reporting Time:</label>
                                            <select name="completion_time" class="form-control" id="completion_time">
                                        <?php
                                            $start_time = strtotime('00:00');
                                            $end_time = strtotime('23:00');
                                            $interval = 60 * 60; // 1 hour interval
                                            for ($time = $start_time; $time <= $end_time; $time += $interval) {
                                                $display_time = date('h:i A', $time);
                                                $option_value = date('H:i', $time);
                                                $selected = ($option_value === '09:00') ? 'selected' : '';

                                                echo '<option value="' . $option_value . '" ' . $selected . '>' . $display_time . '</option>';
                                            }
                                        ?>
                                        </select>
                                    </div>            
                                    <div class="col-sm-6">
                                        <label for="completion_date">Reporting Date:</label>
                                        <input type="date" name="completion_date" class="form-control" id="completion_date" onchange="handleDateSelection()">
                                    </div>
                                </div>   
                            </div> 
                            <div class="col-sm-12">   
                                <div class="row" >
                                    <div class="col-sm-12">
                                        <label for="description">Description:</label>
                                        <textarea name="task_title" id="task_title" class="form-control" placeholder="Please enter something "  rows="2" cols="40"></textarea>
                                    </div>    
                                </div>    
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12  col-lg-12"  style="padding-left:34px !important">
                               <table widht="100%" border="0">
                                    <tr>
                                        <td> <input class="form-check-input" type="radio" name="tasktype" id="urgentRadio" value="Urgent" checked> </td>
                                        <td> <label class="form-check-label" for="urgentRadio">Urgent</label></td>
                                        <td> <input class="form-check-input" type="radio" name="tasktype" id="longTermRadio" value="Longterm"></td>
                                        <td> <label class="form-check-label" for="longTermRadio">Long Term</label></td>
                                    </tr>
                                </table>        
                        </div>
                    </div>
                    <div class="row" id="points_heading" style="display:none">
                        <div class="col-sm-12" id="show_list">
                            <div class="row" style="background-color:gray;padding: 4px;" >
                                <div class="col-sm-3 for_task_show">
                                        Title
                                        <inpu type="text" name="task_point_desc[]" value="taskname">
                                </div>
                                <div class="col-sm-3 for_task_date">
                                        Date
                                        <inpu type="text" name="task_point_date[]" value="taskname">
                                </div>
                                <div class="col-sm-3 for_task_time">
                                        Time
                                        <inpu type="text" name="task_point_time[]" value="taskname">
                                </div>
                                <div class="col-sm-3 for_task_remove">
                                        Remove
                                </div>
                            </div> 
                        </div>
                    </div>   
                    <div id="append_desc_date_time"></div>
                    <!-- <div class="row">
                        <div class="col-sm-12">
                                <ul id="valueList"></ul> 
                        </div>
                    </div>                   -->
                    <div class="row" id="only_button">
                        <div class="col-sm-12">
                            <!-- <button type="submit" class="btn-block btn btn-success" ><i class="fe fe-check mr-2"></i>Submit Task</button> -->
                            <center><button type="submit" class="btn btn-success">
                                <i class="fe fe-check mr-2"></i>Submit Task
                            </button>
                                        </center>
                            <span id="errorMsg"></span>
                        </div>
                    </div>    
            </form>
            
        </div>
    </div>
</div>
<div class="row">
        <div id="tab_innerdivabc" style="width:100%; margin-left:auto; margin-right:auto;border:1px solid white;"></div> 
</div>
@endsection
@section('javascript')
<script type="text/javascript"> 
function del(id){
        //alert(id);
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
function handleCheckboxChange(checkbox) {
    var checked = $('input[name="selection[]"]:checked').map(function() {
        return $(this).val();
    }).get();

    //alert(checkbox);

    fetchData('All','Latest',checked); 
}
$('#sortDropdown').on('change', function() {
    var selectedValue = $(this).val();
    // Perform actions based on the selected value
    console.log('Selected value: ' + selectedValue); 
    fetchData('All',selectedValue);  
    
});

$(document).ready(function() {   
   
   // fetchData('All','Latest');  
   //alert('<?=(isset($_REQUEST['id']) && $_REQUEST['id'] != "") ? $_REQUEST['id'] : "";?>','Latest'); 
    fetchData('<?=(isset($_REQUEST['id']) && $_REQUEST['id'] != "") ? $_REQUEST['id'] : "";?>','Latest');
    $("#toggleButton").click(function() {
        $("#advanceOption").toggle();
        $("#iconToggle").toggleClass("fa-plus fa-minus");
    });
    $("#toggleToDoForm").click(function() {
        $("#toDoDivForm").toggle(); 
        $("#iconToggleToDoForm").toggleClass("fa-plus fa-minus");
        var icon = $('#iconToggleToDoForm');
        var isMinus = icon.hasClass('fa-minus');
        $("#description").focus();
        if (isMinus) {
            $("#sort_filter_table").hide();
        } else {
            $("#sort_filter_table").show();
        }
    });
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
   // $('#toggleToDoForm').hide(); 
    //--------submit function--------------------------------
    $('#taskForm').submit(function(event) {   
        event.preventDefault();           
        var form = $(this);
        var url = form.attr('action');
        var method = form.attr('method');
        var formData = new FormData(form[0]);    
        // var category_id = $("#aid"+categoryId).attr("value"); 
        // formData.append('category_id', values[j]);     
        // alert(a_href);                
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
                //alert($("#task_category_id").val());
                fetchData($("#task_category_id").val(),'Latest');  
                $("#description").attr('readonly', 'readonly');
                submitTask();
                setTimeout(function() {
                    if (response === "NOUSER") {
                        alert("Please select at least one user.");
                    } else if (response === "NOTASK") {
                        alert("Please enter at least one task.");
                    } else {                        
                        $("#description").val('');
                        var valueList = document.getElementById('valueList');
                        valueList.innerHTML = '';
                        var checkboxes = document.querySelectorAll('input[name="user_id[]"]');
                        checkboxes.forEach(function(checkbox) {
                        //checkbox.checked = false;
                        });
                    }                    
                    $("#description").val('');
                    $("#task_title").val('');  
                    $("#description").focus();                                  
                }, 2000);
            },
            error: function(xhr, status, error) {
                // Display error message or perform any other actions
                //alert('An error occurred while saving the task.');
                console.log(xhr.status)
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $('#errorMsg').html('Description Required');
                    $('#errorMsg').css('color','red');
                    // Handle validation errors
                    setTimeout(function() {
                        $('#errorMsg').html('');
                    }, 2000);                    
                }else{
                    // $('#errorMsg').html('');
                    // Handle other error cases
                }
            }
        });
    });
    // end of submit function --------------------------------
});
function submitTask() {  
    $("#description").val('');   
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
        //$("#description").show();
        $("#description").removeAttr('readonly');  
        }, 2000);
    }, 1000);
}
function fetchData(categoryId,SortID,chk) {
    
    //var str=$("#aid"+categoryId).text(); 
    if(categoryId == 'All' || categoryId == 'Completed'){
       // $('#toggleToDoForm').hide(); 
    }else{
        $('#toggleToDoForm').show(); 
    }    
    var a_href = $("#aid"+categoryId).attr("value");      
    //  alert(a_href);
    //return false;           
    $("#task_category_id").val(categoryId);
    const tabContent = $('#tab_innerdivabc');
    tabContent.empty(); // Empty the div before appending new data
    $("#tab_innerdivabc").show();
    //   $("#toDoDivForm").hide();
    $.ajax({
        url: '/staff/showTaskList?id='+categoryId+'&type=ToDo&sortId='+SortID+'&chkbox='+chk,
        method: 'GET',
        success: function(data) {
            // console.log(data)
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
            showLoader(taskId);
            setTimeout(function() {
                // Hide the loader
                hideLoader(taskId);
                // Handle the response if needed
                console.log(response);
                hide_row(taskId);
            }, 1000);
        },
        error: function(xhr, status, error) {
            // Request failed, handle the error
            console.log("Error: " + error);
        }
    });
}
function showLoader(rowid) {
    // Show the loader by modifying the display style
    var loader = document.getElementById("loader"+rowid);
    loader.style.display = "block";
}

function hideLoader(rowid) {
    // Hide the loader by modifying the display style
    var loader = document.getElementById("loader"+rowid);
    loader.style.display = "none";
}
function hide_row(taskId){
    // Hide the corresponding div with ID "row" + taskId
    var rowId = "row" + taskId;
    var rowElement = document.getElementById(rowId);
    if (rowElement) {
        rowElement.style.display = "none";
    }
}
// Add event listener for Enter key press
document.getElementById("description").addEventListener("keydown", function(event) {
    if (event.keyCode === 13) { // Check if Enter key was pressed
        event.preventDefault();
            // Prevent the default Enter key behavior (new line)
        $('#taskForm').submit();
        fetchData($("#task_category_id").val(),'Latest');       
        //addValue(); // Call the addValue() function
    }
});
</script>
@stop
