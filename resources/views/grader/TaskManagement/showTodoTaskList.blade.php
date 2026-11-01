<?php
// echo "abc";exit();
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


        /* =====================================================
   TODO LIST - NEW TASK MANAGEMENT STYLE
   ===================================================== */

.todo-list-card {
    background: #ffffff;
    border: 1px solid #edf2f1;
    border-radius: 12px;
    margin: 0 0 14px 0;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(27, 94, 84, 0.04);
}


/* HEADER */
.todo-list-card__header {
    display: flex !important;
    align-items: flex-start !important;
    justify-content: space-between !important;

    width: 100%;
    margin: 0 !important;
    padding: 16px 18px;

    background: #ffffff;
}

.todo-list-card__header > [class*="col-"] {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

.todo-list-card__content {
    flex: 1 1 auto !important;
    width: auto !important;
    max-width: none !important;
    min-width: 0;
}


/* TITLE LINE */
.todo-title-line {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: 8px;

    margin-bottom: 4px;
}

.todo-list-card .subtask-checkbox {
    width: 14px !important;
    height: 14px !important;

    margin: 0 4px 0 0 !important;

    accent-color: #17675F;
    cursor: pointer;
}

.todo-title {
    color: #1d2939;

    font-size: 13px;
    font-weight: 650;
    line-height: 1.35;
}


/* PRIORITY */
.todo-priority {
    display: inline-flex !important;
    align-items: center;

    padding: 4px 9px !important;

    border: 0 !important;
    border-radius: 20px !important;

    font-size: 8px !important;
    font-weight: 500 !important;
    line-height: 1 !important;
}

.todo-priority--urgent {
    background: #ffe4e6 !important;
    color: #ef5d68 !important;
}

.todo-priority--longterm {
    background: #fff2c9 !important;
    color: #d69c00 !important;
}


/* TIME / DAYS */
.todo-list-card__meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: 5px;

    margin: 0 0 3px 22px;

    color: #87949d;

    font-size: 9px;
}

.todo-list-card__meta .todo-created-age {
    color: #87949d !important;
}

.todo-list-card__meta .badge {
    padding: 4px 8px !important;
    font-size: 8px !important;
}


/* ASSIGNED */
.todo-list-card__assigned {
    margin-left: 22px;

    color: #87949d;

    font-size: 9px;
    font-weight: 400;
}

.todo-list-card__assigned strong {
    color: inherit;
    font-size: inherit;
    font-weight: 400;
}


/* RIGHT ACTIONS */
.todo-list-card__actions {
    display: flex !important;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;

    gap: 20px;

    flex: 0 0 auto !important;

    width: auto !important;
    max-width: none !important;

    padding-left: 20px !important;
}

.todo-action {
    display: inline-flex !important;
    align-items: center;

    gap: 5px;

    margin: 0 !important;
    padding: 0 !important;

    background: transparent !important;
    border: 0 !important;

    color: #17675F !important;

    font-size: 9px !important;
    font-weight: 600 !important;

    text-decoration: none !important;

    cursor: pointer;
    white-space: nowrap;
}

.todo-action i,
i.todo-action {
    color: #17675F !important;
    font-size: 10px !important;
}

.todo-action:hover {
    color: #10564f !important;
    text-decoration: none !important;
}


/* OLD HAMBURGER REMOVE */
.todo-list-card .fa-align-justify {
    display: none !important;
}


/* =====================================================
   DETAIL
   ===================================================== */

.todo-list-card__details {
    width: auto !important;

    margin: 0 18px !important;
    padding: 14px 0 16px !important;

    background: #ffffff !important;

    border: 0 !important;
    border-top: 1px solid #edf1f1 !important;

    outline: 0 !important;
    box-shadow: none !important;
}


/* DETAIL GRID */
.todo-detail-grid {
    display: grid;

    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);

    column-gap: 34px;
    row-gap: 12px;
}

.todo-detail-item {
    position: relative;

    min-height: 32px;

    padding-left: 9px;
}

.todo-detail-item::before {
    content: "";

    position: absolute;
    top: 1px;
    bottom: 1px;
    left: 0;

    width: 2px;

    border-radius: 3px;

    background: #28b99a;
}

.todo-detail-item--description::before {
    background: #ff9f43;
}

.todo-detail-item--reminder::before {
    background: #28b99a;
}

.todo-detail-item--school::before {
    background: #4f8df7;
}

.todo-detail-item--department::before {
    background: #8f67e8;
}

.todo-detail-label {
    display: block;

    margin-bottom: 2px;

    color: #849199;

    font-size: 8px;
    font-weight: 500;
}

.todo-detail-value,
.todo-detail-value.description {
    margin: 0;

    color: #66727d;

    font-size: 9px;
    font-weight: 400;
    line-height: 1.4;
}


/* FOOTER */
.todo-detail-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 15px;

    margin-top: 14px;
    padding-top: 13px;

    border-top: 1px solid #edf1f1;
}

.todo-detail-footer__buttons {
    display: flex;
    align-items: center;

    gap: 8px;
}

.todo-detail-footer .btn {
    min-width: 50px !important;
    height: 27px !important;

    margin: 0 !important;
    padding: 4px 12px !important;

    border-radius: 5px !important;

    font-size: 9px !important;
    font-weight: 500 !important;
    line-height: 17px !important;

    box-shadow: none !important;
}

.todo-detail-footer .btn-success {
    background: #17675F !important;
    border: 1px solid #17675F !important;
    color: #ffffff !important;
}

.todo-detail-footer .btn-primary {
    background: #ffffff !important;
    border: 1px solid #17675F !important;
    color: #17675F !important;
}

.todo-detail-footer .btn-secondary {
    background: #ffffff !important;
    border: 1px solid #87949d !important;
    color: #66727d !important;
}


/* SORTING */
.todo-detail-footer__sorting {
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.todo-detail-footer__sorting select {
    width: 150px !important;
    height: 30px !important;

    margin: 0 !important;
    padding: 4px 25px 4px 9px !important;

    background: #ffffff !important;

    border: 1px solid #dfe7e7 !important;
    border-radius: 5px !important;

    color: #77848d !important;

    font-size: 8px !important;

    box-shadow: none !important;
}


/* MOBILE */
@media (max-width: 767px) {

    .todo-list-card {
        margin-left: 5px;
        margin-right: 5px;
    }

    .todo-list-card__header {
        flex-direction: column;
        padding: 14px;
    }

    .todo-list-card__actions {
        justify-content: flex-start;

        width: 100% !important;

        gap: 12px;

        padding: 10px 0 0 22px !important;
    }

    .todo-detail-grid {
        grid-template-columns: 1fr;
    }

    .todo-list-card__details {
        margin: 0 14px !important;
    }

    .todo-detail-footer {
        align-items: flex-start;
        flex-direction: column;
    }
}
.todo-bulk-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 15px;

    margin: 0 18px 14px;
    padding: 11px 0;

    /* background: #ffffff; */

    /* border: 1px solid #edf2f1; */
    border-radius: 10px;
    margin-left: 0 !important;
    margin-right: 0 !important;
}


.todo-bulk-check {
    display: inline-flex;
    align-items: center;

    gap: 7px;

    margin: 0 !important;

    color: #667085 !important;

    font-size: 11px !important;
    font-weight: 500 !important;
}


.todo-bulk-check input {
    width: 14px;
    height: 14px;

    margin: 0;

    accent-color: #17675F;
}


.todo-bulk-actions {
    display: flex;
    align-items: center;

    gap: 18px;
}


.todo-bulk-action {
    display: inline-flex;
    align-items: center;

    gap: 5px;

    color: #17675F;

    font-size: 10px;
    font-weight: 600;

    cursor: pointer;
}


.todo-bulk-delete {
    color: #d64545;
}


@media(max-width:767px) {

    .todo-bulk-bar {
        align-items: flex-start;
        flex-direction: column;
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

    <div class="container-fluid">
        <span id="TaskAssigned" style="color:green;font-weight:bold"></span>
    </div>
    <div class="container-fluid">
        <div class="timeline" style="padding-left: 0 !important; padding-right: 0 !important;background: #f2f6f6;">
            <!-- <div class="row">
                <div class="col-md-12">
                    <input type="text" id="searchInput" placeholder="Search" class="form-control mb-2" oninput="filterTimeline()">
                </div>
            </div> -->
            <?php //print_r($rec);?>
            @if($rec[0]->task_type!='ToDo')
                @if($rec[0]->tm_status != 'Completed')
                    @if($rec[0]->tm_status != 'Draft')
                        <div class="row hidecheckbox">
                            <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                                <input type="checkbox"  id="markCompletedAllCheckBox"> Check All Task
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4" style="text-align:right">
                                <div onclick="markCompletedAll()" style="border:solid 0px green;margin:1px;color:#D04545;">
                                    <i class="fa fa-check-square-o" style="font-size:18px;cursor:pointer"> Mark Completed All</i>
                                </div>
                            </div>
                        </div>
                        <hr style="margin-top:1rem !important;margin-bottom:1rem !important">
                    @endif
                @endif
            @endif
            @if($rec[0]->task_type=='ToDo')
    @if($rec[0]->tm_status != 'Completed')

        <div class="todo-bulk-bar hidecheckbox">

            <label class="todo-bulk-check">

                <input
                    type="checkbox"
                    id="markCompletedAllCheckBoxTodo"
                >

                <span>Check All Todo List</span>

            </label>


            <div class="todo-bulk-actions">

                <div
                    class="todo-bulk-action todo-bulk-delete"
                    onclick="deleteSelected()"
                >
                    <i class="fa fa-trash"></i>
                    Delete All
                </div>


                <div
                    class="todo-bulk-action"
                    onclick="markCompletedAllTodo()"
                >
                    <i class="fa fa-check-square-o"></i>
                    Mark Completed All
                </div>

            </div>

        </div>

    @endif
@endif
            {{-- @if($rec[0]->task_type=='ToDo')
                @if($rec[0]->tm_status != 'Completed')
                    <div class="row hidecheckbox">

                        <div class="col-12 col-sm-7 col-md-7 col-lg-7">
                            <input type="checkbox"  id="markCompletedAllCheckBoxTodo"> Check All Todo List
                        </div>
                        <div class="col-12 col-sm-2 col-md-2 col-lg-2" style="text-align:right;">
                            <div onclick="deleteSelected()" style="border:solid 0px green;margin:1px;color:#481de5;">
                                <i class="fa fa-trash" style="font-size:18px;cursor:pointer"> Delete All</i>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3" style="text-align:right">
                            <div onclick="markCompletedAllTodo()" style="border:solid 0px green;margin:1px;color:#D04545;width: 172px;">
                                <i class="fa fa-check-square-o" style="font-size:18px;cursor:pointer"> Mark Completed All</i>
                            </div>
                        </div>
                    </div>
                    <hr style="margin-top:1rem !important;margin-bottom:1rem !important">
                @endif
            @endif --}}
            {{-- @foreach($rec as $subtask)
            <div  style="border-bottom:2px solid #D2D6F1; padding-top:4px; padding-bottom:4px;">
                <div class="row" style="border-bottom:0px dotted blue; margin-top:2px;" id="row<?=$subtask->id?>">
                    <div class="col-md-10">
                        @if($subtask->task_type!='ToDo')
                            @if ($subtask->tm_status != 'Completed')
                                @if ($subtask->tm_status != 'Draft')
                                    @if($subtask->tm_entry_user==auth()->user()->employee_id)
                                        <input type="checkbox" class="subtask-checkbox hidecheckbox" id="markCompletedAll" name="markCompletedAll[<?=$subtask->id;?>]" value="<?=$subtask->id;?>">
                                    @endif
                                    <!-- <input type="checkbox" class="subtask-checkbox hidecheckbox" id="markCompletedAll" name="markCompletedAll[<?=$subtask->id;?>]" value="<?=$subtask->id;?>"> -->
                                @endif
                            @endif
                        @endif
                        @if($subtask->task_type=='ToDo')
                            @if($rec[0]->tm_status != 'Completed')
                                @if($subtask->tm_entry_user==auth()->user()->employee_id)
                                    <input type="checkbox" class="subtask-checkbox hidecheckbox" id="markCompletedAllTodo" name="markCompletedAllTodo[<?=$subtask->id;?>]" value="<?=$subtask->id;?>">
                                @endif
                            @endif
                        @endif
                        <i class="fa fa-align-justify"></i>
                         <b>{{$subtask->tm_title}} {{ $subtask->task_type === 'ToDo' ? ('('.$subtask->category_title.')') : '' }}</b>
                        <br>
                        @if($subtask->tm_entry_user==auth()->user()->employee_id)
                            @if($subtask->tm_task_priority=='Urgent')
                            <span class="badge badge-success badge-pill">{{$subtask->tm_task_priority}}</span>
                            @else
                            <span class="badge badge-warning badge-pill">{{$subtask->tm_task_priority}}</span>
                            @endif
                            <input type="hidden" id="category_id_{{$subtask->id}}" value="{{$subtask->task_category_id}}">

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
                            @endif

                            @if($subtask->task_type!='ToDo')

                                <?php
                                $user_id = '';
                                if(auth()->user()->employee_id == $subtask->tm_entry_user)
                                    $user_id = $subtask->task_client_id;
                                else
                                    $user_id = $subtask->tm_entry_user;

                                ?>
                                <a href="https://cms.bnu.edu.pk/user-messages?task_id=<?=$subtask->id;?>&to_user_id=<?=$user_id?>">
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

                                    @if($subtask->tm_entry_user==auth()->user()->employee_id and $subtask->task_type=='Task' and $subtask->tm_status=='Completed')
                                        <button type="button" id="rollbackBtn"  onclick="rollBack({{$subtask->id}})" class="btn btn-secondary">Roll Back</button>
                                    @endif

                                    @if($subtask->tm_entry_user==auth()->user()->employee_id and $subtask->task_type=='Task')
                                        <button type="button"  onclick="editTodo({{$subtask->id}})" class="btn btn-success">Edit</button>
                                        <button type="button" onclick="confirmDelete({{$subtask->id}},'{{$subtask->tm_status}}')" class="btn btn-primary">Delete</button>
                                    @endif
                                </div>
                                @if($subtask->tm_entry_user==auth()->user()->employee_id)
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
                                @endif
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
            @endforeach --}}
            @foreach($rec as $subtask)

<div class="todo-list-card" id="row<?=$subtask->id?>">

    <div class="row todo-list-card__header">

        <div class="col-md-10 todo-list-card__content">

            <div class="todo-title-line">

                {{-- CHECKBOX - SAME LOGIC --}}
                @if($subtask->task_type!='ToDo')
                    @if ($subtask->tm_status != 'Completed')
                        @if ($subtask->tm_status != 'Draft')
                            @if($subtask->tm_entry_user==auth()->user()->employee_id)

                                <input
                                    type="checkbox"
                                    class="subtask-checkbox hidecheckbox"
                                    id="markCompletedAll"
                                    name="markCompletedAll[<?=$subtask->id;?>]"
                                    value="<?=$subtask->id;?>"
                                >

                            @endif
                        @endif
                    @endif
                @endif


                @if($subtask->task_type=='ToDo')
                    @if($rec[0]->tm_status != 'Completed')
                        @if($subtask->tm_entry_user==auth()->user()->employee_id)

                            <input
                                type="checkbox"
                                class="subtask-checkbox hidecheckbox"
                                id="markCompletedAllTodo"
                                name="markCompletedAllTodo[<?=$subtask->id;?>]"
                                value="<?=$subtask->id;?>"
                            >

                        @endif
                    @endif
                @endif


                <strong class="todo-title">

                    {{$subtask->tm_title}}

                    {{
                        $subtask->task_type === 'ToDo'
                        ? ('('.$subtask->category_title.')')
                        : ''
                    }}

                </strong>


                {{-- PRIORITY - SAME CONDITION --}}
                @if($subtask->tm_entry_user==auth()->user()->employee_id)

                    @if($subtask->tm_task_priority=='Urgent')

                        <span
                            class="badge badge-success badge-pill
                                   todo-priority
                                   todo-priority--urgent"
                        >
                            {{$subtask->tm_task_priority}}
                        </span>

                    @else

                        <span
                            class="badge badge-warning badge-pill
                                   todo-priority
                                   todo-priority--longterm"
                        >
                            {{$subtask->tm_task_priority}}
                        </span>

                    @endif


                    <input
                        type="hidden"
                        id="category_id_{{$subtask->id}}"
                        value="{{$subtask->task_category_id}}"
                    >

                @endif

            </div>


            {{-- TIME --}}
            <div class="todo-list-card__meta">

                <span class="todo-created-age">

                    <i class="fa fa-clock-o"></i>

                    <?php
                        $givenTime = $subtask->created_at;
                        $duration = calculateDuration($givenTime);
                        echo $duration;
                    ?>

                    Ago

                </span>


                @if($subtask->task_type!='ToDo')

                    <?php
                        $getdays =
                            getdays(
                                $subtask->convert_tm_completion_date
                            );
                    ?>

                    @if($getdays=="1")

                        <span class="blink_me badge rounded-pill bg-warning">
                            {{$getdays}} Day Left
                        </span>

                    @elseif($getdays<=0 && $getdays!="")

                        <span class="blink_me badge rounded-pill bg-danger">

                            {{str_replace("-","",$getdays)}}
                            Days Passed

                        </span>

                    @endif

                @endif

            </div>


            {{-- LOADER - SAME ID --}}
            <div
                id="loader{{$subtask->id}}"
                style="display:none"
            >

                <div class="spinner4">
                    <div class="bounce1"></div>
                    <div class="bounce2"></div>
                    <div class="bounce3"></div>
                </div>

            </div>


            {{-- ASSIGNED - SAME LOGIC --}}
            @if($subtask->task_type!='ToDo')

                <div class="todo-list-card__assigned">

                    @if(
                        $subtask->tm_entry_user ==
                        auth()->user()->employee_id
                    )

                        <strong>
                            Assigned To :
                            {{$subtask->task_to_name}}
                        </strong>

                    @else

                        <strong>
                            Assigned By :
                            {{$subtask->task_sender_name}}
                        </strong>

                    @endif

                </div>

            @endif

        </div>


        {{-- ==========================================
             ACTIONS
             ========================================== --}}

        <div class="col-md-2 todo-list-card__actions">


            {{-- MARK COMPLETED - SAME ONCLICK --}}
            @if(
                $subtask->tm_entry_user ==
                auth()->user()->employee_id
            )

                @if($subtask->tm_status != 'Draft')

                    @if ($subtask->tm_status != 'Completed')

                        <div
                            class="todo-action"
                            onclick="confirmMarkCompleted(
                                <?=$subtask->id;?>
                            )"
                        >

                            <i class="fa fa-check-square-o"></i>

                            Mark Completed

                        </div>

                    @endif

                @endif

            @endif


            {{-- SEND MESSAGE - SAME URL LOGIC --}}
            @if($subtask->task_type!='ToDo')

                <?php

                    $user_id = '';

                    if(
                        auth()->user()->employee_id ==
                        $subtask->tm_entry_user
                    )

                        $user_id =
                            $subtask->task_client_id;

                    else

                        $user_id =
                            $subtask->tm_entry_user;

                ?>


                <a
                    class="todo-action"
                    href="https://cms.bnu.edu.pk/user-messages?task_id=<?=$subtask->id;?>&to_user_id=<?=$user_id?>"
                >

                    <i class="fa fa-commenting-o"></i>

                    Send Message


                    @if($subtask->total_message!='0')

                        <span class="badge rounded-pill bg-danger">
                            {{$subtask->total_message}}
                        </span>

                    @endif

                </a>

            @endif


            {{-- ASSIGN DRAFT --}}
            @if($subtask->tm_status == 'Draft')

                <div
                    class="todo-action"
                    onclick="draftStatus(
                        {{$subtask->id}}
                    )"
                >

                    <i class="fa fa-tasks"></i>

                    Assign Task

                </div>

            @endif


            {{-- DETAILS - SAME COLLAPSE TARGET --}}
            <div
                class="todo-action"
                data-toggle="collapse"
                data-target="#demo{{$subtask->id}}"
            >

                <i class="fa fa-search-plus"></i>

                Details

            </div>

        </div>

    </div>



    {{-- ==========================================
         DETAIL COLLAPSE
         ========================================== --}}

    <div
        id="demo{{$subtask->id}}"
        class="collapse todo-list-card__details"
    >

        <div class="todo-detail-grid">


            {{-- DESCRIPTION --}}
            <div class="todo-detail-item todo-detail-item--description">

                <span class="todo-detail-label">
                    Description
                </span>

                @php

                    $description =
                        $subtask->tm_description
                        ? $subtask->tm_description
                        : "Description Not Available";

                    $words = str_word_count($description);

                @endphp


                <div class="description todo-detail-value">

                    @if ($words > 50)

                        <span class="short-description">
                            {{ substr($description, 0, 200) }}...
                        </span>

                        <span
                            class="read-more text-info"
                            style="cursor:pointer;"
                        >

                            <i class="fa fa-chevron-circle-down"></i>
                            Read more

                        </span>


                        <span
                            class="full-description"
                            style="display:none;"
                        >
                            {{ $description }}
                        </span>


                        <span
                            class="read-less text-info"
                            style="display:none; cursor:pointer;"
                        >

                            <i class="fa fa-chevron-circle-up"></i>
                            Show less

                        </span>

                    @else

                        {{ $description }}

                    @endif

                </div>

            </div>


            {{-- REMINDER --}}
            <div class="todo-detail-item todo-detail-item--reminder">

                <span class="todo-detail-label">
                    Reminder Date / Time
                </span>

                <div class="todo-detail-value">

                    {{
                        $subtask->tm_completion_date
                        ? substr(
                            $subtask->tm_completion_date,
                            0,
                            10
                        )
                        : "Not Defined"
                    }}

                    /

                    @if(strlen($subtask->task_report_time) > 10)

                        {{
                            substr(
                                $subtask->task_report_time,
                                11
                            )
                        }}

                    @else

                        {{
                            $subtask->task_report_time
                            ? $subtask->task_report_time
                            : "Not Defined"
                        }}

                    @endif

                </div>

            </div>


            {{-- SAME EXISTING NON TODO INFORMATION --}}
            @if($subtask->task_type!='ToDo')

                <div class="todo-detail-item todo-detail-item--school">

                    <span class="todo-detail-label">
                        School
                    </span>

                    <div class="todo-detail-value">
                        {{$subtask->school_code}}
                    </div>

                </div>


                <div class="todo-detail-item todo-detail-item--department">

                    <span class="todo-detail-label">
                        Department
                    </span>

                    <div class="todo-detail-value">
                        {{$subtask->dept_name}}
                    </div>

                </div>

            @endif

        </div>



        {{-- ==========================================
             HIDDEN FIELDS - DO NOT CHANGE
             ========================================== --}}

        <input
            type="hidden"
            id="edit_tm_task_priority<?=$subtask->id;?>"
            value="{{$subtask->tm_task_priority}}"
        >

        <input
            type="hidden"
            id="edit_tm_task_title<?=$subtask->id;?>"
            value="{{$subtask->tm_title}}"
        >

        <input
            type="hidden"
            id="edit_tm_description<?=$subtask->id;?>"
            value="{{$subtask->tm_description}}"
        >

        <input
            type="hidden"
            id="edit_task_client<?=$subtask->id;?>"
            value="{{$subtask->task_client_id}}"
        >

        <input
            type="hidden"
            id="edit_tm_completion_time<?=$subtask->id;?>"
            value="{{$subtask->task_report_time}}"
        >

        <input
            type="hidden"
            id="edit_tm_project_category<?=$subtask->id;?>"
            value="{{$subtask->tm_project_category}}"
        >

        <input
            type="hidden"
            id="edit_tm_completion_date<?=$subtask->id;?>"
            value="{{substr($subtask->tm_completion_date,0,10)}}"
        >

        <input
            type="hidden"
            id="taskSortOrder<?=$subtask->id;?>"
            value="{{$subtask->tm_sorting_order}}"
        >

        <input
            type="hidden"
            id="tasknoTime<?=$subtask->id;?>"
            value="{{$subtask->tm_no_date}}"
        >



        {{-- ==========================================
             FOOTER
             ========================================== --}}

        <div class="todo-detail-footer">

            <div class="todo-detail-footer__buttons">


                {{-- TODO COMPLETED ROLLBACK --}}
                @if(
                    $subtask->tm_entry_user ==
                    auth()->user()->employee_id
                    and
                    $subtask->task_type=='ToDo'
                    and
                    $subtask->tm_status == 'Completed'
                )

                    <button
                        type="button"
                        onclick="rollBack(
                            {{$subtask->id}}
                        )"
                        class="btn btn-secondary"
                    >
                        Roll Back
                    </button>

                @endif


                {{-- TODO EDIT DELETE --}}
                @if(
                    $subtask->tm_entry_user ==
                    auth()->user()->employee_id
                    and
                    $subtask->task_type=='ToDo'
                )

                    <button
                        type="button"
                        onclick="editTodo(
                            {{$subtask->id}}
                        )"
                        class="btn btn-success"
                    >
                        Edit
                    </button>


                    <button
                        type="button"
                        onclick="confirmDelete(
                            {{$subtask->id}},
                            '{{$subtask->tm_status}}'
                        )"
                        class="btn btn-primary"
                    >
                        Delete
                    </button>

                @endif


                {{-- TASK COMPLETED --}}
                @if(
                    $subtask->tm_entry_user ==
                    auth()->user()->employee_id
                    and
                    $subtask->task_type=='Task'
                    and
                    $subtask->tm_status=='Completed'
                )

                    <button
                        type="button"
                        id="rollbackBtn"
                        onclick="rollBack(
                            {{$subtask->id}}
                        )"
                        class="btn btn-secondary"
                    >
                        Roll Back
                    </button>

                @endif


                {{-- TASK EDIT DELETE --}}
                @if(
                    $subtask->tm_entry_user ==
                    auth()->user()->employee_id
                    and
                    $subtask->task_type=='Task'
                )

                    <button
                        type="button"
                        onclick="editTodo(
                            {{$subtask->id}}
                        )"
                        class="btn btn-success"
                    >
                        Edit
                    </button>


                    <button
                        type="button"
                        onclick="confirmDelete(
                            {{$subtask->id}},
                            '{{$subtask->tm_status}}'
                        )"
                        class="btn btn-primary"
                    >
                        Delete
                    </button>

                @endif

            </div>



            {{-- SORTING - SAME ID / FUNCTION --}}
            @if(
                $subtask->tm_entry_user ==
                auth()->user()->employee_id
            )

                <div class="todo-detail-footer__sorting">

                    <select
                        id="myDropdown{{$subtask->id}}"
                        class="form-control"
                        onchange="handleDropdownChange(
                            {{$subtask->id}}
                        )"
                    >

                        <option value="">
                            --Task Sorting Order--
                        </option>


                        @for(
                            $i = 1;
                            $i <= $subtask->total_task;
                            $i++
                        )

                            <option value="{{$i}}">
                                Set on {{$i}}
                            </option>

                        @endfor

                    </select>


                    <div
                        id="tickIcon{{$subtask->id}}"
                        style="
                            padding-left:8px;
                            display:none;
                        "
                    >

                        <i
                            class="fa fa-check"
                            style="color:#17675F"
                        ></i>

                    </div>

                </div>

            @endif

        </div>

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

        const fragment = window.location.hash;
        // Remove the '#' character from the fragment
        const fragmentWithoutHash = fragment.substring(1);
        // Split the fragment by '?' to separate the location from the query parameter
        const parts = fragmentWithoutHash.split('?');
        // Get the specific-location value
        const getspecificLocation = parts[0];
        if(getspecificLocation){
            // Set the cursor position
            const specificLocation = document.getElementById(getspecificLocation);
            specificLocation.scrollIntoView();
            specificLocation.classList.add(getspecificLocation);
            // Change the color
            specificLocation.style.backgroundColor =  'lightblue';
        }
        //alert($('#task_category_id').val());
        // $('#editBtn').hide();
        if($('#task_category_id1').val() == 'AssignedMe'){
            //alert('ok');
            $('.hidecheckbox').attr('style', 'display: none;');

        }
        $("#markCompletedAllCheckBoxTodo").click(function() {
            // Get its current checked state
            var isChecked = $(this).prop("checked");
            // Set the same state for all subtask checkboxes
            $(".subtask-checkbox").prop("checked", isChecked);
        });
        $("#markCompletedAllCheckBox").click(function() {
            // Get its current checked state
            var isChecked = $(this).prop("checked");
            // Set the same state for all subtask checkboxes
            $(".subtask-checkbox").prop("checked", isChecked);
        });
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
    function openModal(subtaskId) {
        // Pass the subtask ID to the modal or perform any other action
        document.getElementById("myModal").style.display = "block";
        document.getElementById("taskid").value = subtaskId;
    }

 </script>
