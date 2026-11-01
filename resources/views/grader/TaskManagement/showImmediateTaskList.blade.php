<?php
//echo "abc";exit();
?>
        <style>
        .timeline {
            padding: 20px;
            /* margin-top: 50px; */
        }

        .timeline-event {
            position: relative;
            padding: 20px;
            margin-bottom: 40px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .event-date {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .event-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .event-description {
            font-size: 16px;
            line-height: 1.5;
        }

        @media (max-width: 767px) {
            .timeline-event {
                padding: 10px;
                margin-bottom: 20px;
            }
        }


        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .loader {
            /* Your loader styles here */
            /* For example, a spinning loader animation */
            border: 16px solid #f3f3f3;
            /* Light grey */
            border-top: 16px solid #3498db;
            /* Blue */
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            margin: auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <?php
    function getdays($input_date_time){
        if($input_date_time!=""){
            $due_date=date('Y-m-d', strtotime($input_date_time));
            date_default_timezone_set('Asia/karachi');
            $curr_time =date("His");
            $curr_date=date("Y-m-d", strtotime(substr($curr_time,0,11)));

            $expiry=date('d-m-Y', strtotime($input_date_time));

            $origin = date_create($curr_date);
            $target = date_create($due_date);
            $interval = date_diff($origin, $target);
            return str_replace("+","",$interval->format('%R%a'));
         }else{
            return '';
         }
    }
    function calculateDuration($givenTime)
    {
        date_default_timezone_set('Asia/karachi');
        $currentDateTime = new DateTime(); // Current date and time

        // Convert the given time to a DateTime object
        $givenDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $givenTime);

        // Calculate the duration between the given time and the current time
        $duration = $currentDateTime->diff($givenDateTime);

        // Access the duration components (years, months, days, hours, minutes, seconds)
        $years = $duration->y;
        $months = $duration->m;
        $days = $duration->d;
        $hours = $duration->h;
        $minutes = $duration->i;
        $seconds = $duration->s;

        // Build the duration string
        $durationString = "";
        if ($years > 0) {
            $durationString .= $years . " Year, ";
            return $durationString;
        }
        if ($months > 0) {
            $durationString .= $months . " Month, ";
            return $durationString;
        }
        if ($days > 0) {
            $durationString .= $days . " Days, ";
            return $durationString;
        }
        if ($hours > 0) {
            $durationString .= $hours . " Hours, ";
            return $durationString;
        }
        if ($minutes > 0) {
            $durationString .= $minutes . " Minutes, ";
            return $durationString;
        }
        $durationString .= $seconds . " Seconds";
        return $durationString;
    }
    ?>

    <div class="container">
        <span id="TaskAssigned" style="color:green;font-weight:bold"></span>
    </div>
    <div class="container">
        <div class="timeline">
            <!-- <div class="row">
                <div class="col-md-12">
                    <input type="text" id="searchInput" placeholder="Search" class="form-control mb-2" oninput="filterTimeline()">
                </div>
            </div> -->
            <?php //print_r($rec);?>
            @if($rec[0]->task_type=='ToDo')
                @if($rec[0]->tm_status != 'Completed')
                    <div class="row hidecheckbox">
                        <div class="col-12 col-sm-7 col-md-7 col-lg-7">
                            <input type="checkbox"  id="markCompletedAllCheckBoxImmediateTodo"> Check All Todo List
                        </div> <div class="col-12 col-sm-2 col-md-2 col-lg-2" style="text-align:right;">
                            <div onclick="deleteSelectedImmediateTodo()" style="border:solid 0px green;margin:1px;color:#481de5;">
                                <i class="fa fa-trash" style="font-size:18px;cursor:pointer"> Delete All</i>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3" style="text-align:right">
                            <div onclick="markCompletedAllImmediateTodo()" style="border:solid 0px green;margin:1px;color:#D04545;;width: 172px;">
                                <i class="fa fa-check-square-o" style="font-size:18px;cursor:pointer"> Mark Completed All</i>
                            </div>
                        </div>
                    </div>
                    <hr style="margin-top:1rem !important;margin-bottom:1rem !important">
                @endif
            @endif

            @foreach($rec as $subtask)
            <div  style="border-bottom:2px solid #D2D6F1; padding-top:4px; padding-bottom:4px;">
                <div class="row" style="border-bottom:0px dotted blue; margin-top:2px;" id="row<?=$subtask->id?>">
                    <div class="col-md-10">
                        @if($subtask->task_type=='ToDo')
                            @if($rec[0]->tm_status != 'Completed')
                                @if($subtask->tm_entry_user==auth()->user()->employee_id)
                                    <input type="checkbox" class="subtask-checkbox hidecheckbox" id="markCompletedAllImmediateTodo" name="markCompletedAllImmediateTodo[<?=$subtask->id;?>]" value="<?=$subtask->id;?>">
                                @endif
                            @endif
                        @endif
                        <i class="fa fa-align-justify"></i>
                        <b> {{$subtask->tm_title}} {{ $subtask->task_type === 'ToDo' ? ('('.$subtask->category_title.')') : '' }}</b>
                        <br>
                        @if($subtask->tm_entry_user==auth()->user()->employee_id)
                            @if($subtask->tm_task_priority=='Urgent')
                            <span class="badge badge-success badge-pill">{{$subtask->tm_task_priority}}</span>
                            @else
                            <span class="badge badge-warning badge-pill">{{$subtask->tm_task_priority}}</span>
                            @endif
                        @endif

                        <span style="color:blue;font-size:12px;">
                            <i class="fa fa-clock-o"></i>
                            <?php
                                // Example usage:
                                $givenTime = $subtask->created_at; // Replace with your actual given time value
                                $duration = calculateDuration($givenTime);
                                echo $duration;
                            ?> Ago
                        </span>
                        @if($subtask->task_type!='ToDo')
                                <?php
                                $getdays = getdays($subtask->convert_tm_completion_date);
                                ?>
                                @if($getdays=="1")
                                    <span class="blink_me position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                        {{$getdays}} Day Left
                                    </span>
                                @elseif($getdays<=0 && $getdays!="")
                                    <span class="blink_me position-absolute top-0 start-150 translate-middle badge rounded-pill bg-danger">
                                        {{str_replace("-","",$getdays)}}  Days Passed
                                    </span>
                                @endif
                        @endif
                        <div id="loader{{$subtask->id}}" style="display:none">
                                <div class="spinner4">
                                        <div class="bounce1"></div>
                                        <div class="bounce2"></div>
                                        <div class="bounce3"></div>
                                </div>
                        </div><br>
                        @if($subtask->task_type!='ToDo')
                                @if($subtask->tm_entry_user==auth()->user()->employee_id)
                                    <strong>Assigned To : {{$subtask->task_to_name}}</strong>
                                @else
                                    <strong>Assigned By : {{$subtask->task_sender_name}}</strong>
                                @endif
                        @endif
                        </div>
                        <div class="col-md-2">

                            @if($subtask->tm_entry_user==auth()->user()->employee_id)
                                <!-- <div onclick="confirmMarkCompleted(<?=$subtask->id;?>)" style="border:solid 0px green;margin:1px;color:blue;">
                                    <i class="fa fa-check-square-o"> Mark Completed</i>
                                </div> -->
                                @if($subtask->tm_status != 'Draft')
                                    @if ($subtask->tm_status != 'Completed')
                                        <div onclick="confirmMarkCompleted(<?=$subtask->id;?>)" style="border:solid 0px green;margin:1px;color:#8A2BE2;">
                                            <i class="fa fa-check-square-o" style="font-size:18px;cursor:pointer"> Mark Completed</i>
                                        </div>
                                    @endif
                                @endif
                                @elseif ($subtask->task_type === 'ToDo' && $subtask->tm_status != 'Completed')
                                <div onclick="confirmMarkCompleted(<?=$subtask->id;?>)" style="border:solid 0px green;margin:1px;color:#8A2BE2;">
                                    <i class="fa fa-check-square-o" style="font-size:18px;cursor:pointer"> Mark Completed</i>
                                </div>
                            @endif

                            @if($subtask->task_type!='ToDo')

                                <?php
                                $user_id = '';
                                if(auth()->user()->employee_id == $subtask->tm_entry_user)
                                    $user_id = $subtask->task_client_id;
                                else
                                    $user_id = $subtask->tm_entry_user;

                                ?>
                                <a href="/user-messages?task_id=<?=$subtask->id;?>&to_user_id=<?=$user_id?>">
                                    <!-- <button type="button" class="btn btn-outline-info position-relative"> -->
                                    <i class="fa fa-commenting-o" style="font-size:18px;color:blue">
                                    </i> Send Message
                                           @if($subtask->total_message!='0')
                                                <span class="badge rounded-pill bg-danger">
                                                    {{$subtask->total_message}}
                                                </span>
                                            @endif
                                </a>
                            @endif
                            <!-- <div onclick="openModal('{{ $subtask->id }}')" style="border:solid 2px green;margin:1px;color:green;">
                                <i class="fa fa-edit"> Details</i>
                            </div> -->


                            @if($subtask->tm_status == 'Draft')
                                <i class="fa fa-tasks" style="font-size:18px;color:#4454C3;cursor:pointer" onclick="draftStatus({{$subtask->id}})"> Assign Task</i><br>
                            @endif
                            <i class="fa fa-search-plus" style="font-size:18px;color:green;cursor:pointer" data-toggle="collapse" data-target="#demo{{$subtask->id}}"> Details</i>
                        </div>
                </div>
                <div id="demo{{$subtask->id}}" class="collapse" style="border:solid 2px green;padding:8px">
                    <div class="row">
                            <div class="col-md-6"><span class="badge badge-success">Description</span></div>
                            <div class="col-md-6"><span class="badge badge-warning">Reminder Date / Time</span></div>
                    </div>
                    <div class="row">
                    <div class="col-md-6">
                            @php
                                $description = $subtask->tm_description ? $subtask->tm_description : "Description Not Available";
                                $words = str_word_count($description);
                            @endphp

                            <div class="description">
                                @if ($words > 50)
                                    <span class="short-description">{{ substr($description, 0, 200) }}...</span>
                                    <span class="read-more text-info " style="cursor: pointer;">
                                        <i class="fa fa-chevron-circle-down"></i> Read more
                                    </span>
                                    <span class="full-description" style="display: none;">{{ $description }}</span>
                                    <span class="read-less text-info" style="display: none; cursor: pointer;">
                                        <i class="fa fa-chevron-circle-up"></i> Show less
                                    </span>
                                @else
                                    {{ $description }}
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{ $subtask->tm_completion_date ? substr($subtask->tm_completion_date, 0, 10) : "Not Defined" }}
                            /
                            @if(strlen($subtask->task_report_time) > 10)
                                {{ substr($subtask->task_report_time, 11) }}
                            @else
                                {{ $subtask->task_report_time ? $subtask->task_report_time : "Not Defined" }}
                            @endif
                        </div>
                        <input type="hidden" id="edit_tm_task_priority<?=$subtask->id;?>" value="{{$subtask->tm_task_priority}}" >
                        <input type="hidden" id="edit_tm_task_title<?=$subtask->id;?>" value="{{$subtask->tm_title}}" >
                        <input type="hidden" id="edit_tm_description<?=$subtask->id;?>" value="{{$subtask->tm_description}}" >
                        <input type="hidden" id="edit_task_client<?=$subtask->id;?>" value="{{$subtask->task_client_id}}" >
                        <input type="hidden" id="edit_tm_completion_time<?=$subtask->id;?>" value="{{$subtask->task_report_time}}" >
                        <input type="hidden" id="edit_tm_project_category<?=$subtask->id;?>" value="{{$subtask->tm_project_category}}" >
                        <input type="hidden" id="edit_tm_completion_date<?=$subtask->id;?>" value="{{substr($subtask->tm_completion_date,0,10)}}" >
                        <input type="hidden" id="taskSortOrder<?=$subtask->id;?>" value="{{$subtask->tm_sorting_order}}" >
                        <input type="hidden" id="tasknoTime<?=$subtask->id;?>" value="{{$subtask->tm_no_date}}" >
                    </div>
                    @if($subtask->task_type!='ToDo')
                        <div class="row">
                            <div class="col-md-6"><span class="badge badge-info">School</span></div>
                            <div class="col-md-6"><span class="badge badge-info">Department</span></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">{{$subtask->school_code}}</div>
                            <div class="col-md-6">{{$subtask->dept_name}}</div>
                        </div>
                    @endif
                    <br>
                    <div class="row" style="border:0px solid black" >
                        <div class="col-md-12">
                            <div style="display:inline;">
                                <div class="col-md-6" style="float:left;border:0px solid black">
                                    @if($subtask->tm_entry_user==auth()->user()->employee_id and $subtask->task_type=='ToDo' and $subtask->tm_status == 'Completed')
                                        <button type="button"   onclick="rollBack({{$subtask->id}})" class="btn btn-secondary">Roll Back</button>
                                    @endif
                                    @if($subtask->tm_entry_user==auth()->user()->employee_id and $subtask->task_type=='ToDo')
                                        <button type="button"  onclick="editTodo({{$subtask->id}})" class="btn btn-success">Edit</button>
                                        <button type="button" onclick="confirmDelete({{$subtask->id}},'{{$subtask->tm_status}}')" class="btn btn-primary">Delete</button>
                                    @endif
                                </div>
                                <!-- @if($subtask->tm_entry_user==auth()->user()->employee_id)
                                <div class="col-md-6" style="float:right;">
                                    <div class="col-md-8" style="float:left;" >

                                        <select id="myDropdown{{$subtask->id}}" class="form-control" onchange="handleDropdownChange({{$subtask->id}})">
                                            <option value="">--Task Sorting Order--</option>
                                            @for($i = 1; $i <= $subtask->total_task; $i++)
                                                <option value="{{$i}}">Set on {{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-4" id="tickIcon{{$subtask->id}}" style="float:right;padding-top:8px;display:none">
                                        <i class="fa fa-check" style="color:green"></i>
                                    </div>
                                </div>
                                @endif  -->
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <!-- <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script> -->

    <script>
        $(document).ready(function() {
            $('.read-more').click(function() {
                var descriptionContainer = $(this).closest('.description');
                var shortDescription = descriptionContainer.find('.short-description');
                var fullDescription = descriptionContainer.find('.full-description');
                var readLess = descriptionContainer.find('.read-less');

                shortDescription.slideUp();
                fullDescription.slideDown();
                $(this).hide(); // Hide the "Read more" link
                readLess.show(); // Show the "Show less" link
            });

            $('.read-less').click(function() {
                var descriptionContainer = $(this).closest('.description');
                var shortDescription = descriptionContainer.find('.short-description');
                var fullDescription = descriptionContainer.find('.full-description');
                var readMore = descriptionContainer.find('.read-more');

                shortDescription.slideDown();
                fullDescription.slideUp();
                $(this).hide(); // Hide the "Show less" link
                readMore.show(); // Show the "Read more" link
            });
        });
    </script>



    <script>
        $(document).ready(function() {
            $('.read-more').click(function() {
                var descriptionContainer = $(this).closest('.description');
                var shortDescription = descriptionContainer.find('.short-description');
                var fullDescription = descriptionContainer.find('.full-description');
                var readLess = descriptionContainer.find('.read-less');

                shortDescription.slideUp();
                fullDescription.slideDown();
                $(this).hide(); // Hide the "Read more" link
                readLess.show(); // Show the "Show less" link
            });

            $('.read-less').click(function() {
                var descriptionContainer = $(this).closest('.description');
                var shortDescription = descriptionContainer.find('.short-description');
                var fullDescription = descriptionContainer.find('.full-description');
                var readMore = descriptionContainer.find('.read-more');

                shortDescription.slideDown();
                fullDescription.slideUp();
                $(this).hide(); // Hide the "Show less" link
                readMore.show(); // Show the "Read more" link
            });
        });
    </script>

 <script>
    $(document).ready(function() {
        //alert($('#task_category_id').val());
        // $('#editBtn').hide();
        if($('#task_category_id').val() == 'Completed'){
            $('#rollbackBtn').show();
            $('#editBtn').hide();
           // alert($('#task_category_id').val());
        }else if($('#task_category_id').val() == ''){
            $('#editBtn').hide();
        }else if($('#task_category_id').val() == 'All'){
            $('#editBtn').show();
        }

        (function blink() {
            $('.blink_me').fadeOut(500).fadeIn(500, blink);
        })();
    });
    $("#markCompletedAllCheckBoxImmediateTodo").click(function() {
        // Get its current checked state
        var isChecked = $(this).prop("checked");
        // Set the same state for all subtask checkboxes
        $(".subtask-checkbox").prop("checked", isChecked);
    });
    function openModal(subtaskId) {
        // Pass the subtask ID to the modal or perform any other action
        document.getElementById("myModal").style.display = "block";
        document.getElementById("taskid").value = subtaskId;
    }

 </script>
