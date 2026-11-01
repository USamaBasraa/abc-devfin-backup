<?php
// echo "abc";exit();
?>
        <style>
             .description {
        text-align: justify;
    }
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
		         #readMoreBtn {
        display: inline-block;
        padding: 0;
        background: none;
        border: none;
        color: blue; /* Change color as needed */
        text-decoration: underline; /* Add underline to make it look like a link */
        cursor: pointer;
    }















    /* =========================================================
   NEW TASK LIST CARD
   Only presentation changes - existing logic stays same
   ========================================================= */

#tab_innerdivabc .task-list-card {
    background: #ffffff !important;
    border: 1px solid #edf2f1 !important;
    border-radius: 12px !important;
    margin: 0 0 14px 0 !important;
    overflow: hidden !important;
    box-shadow: 0 2px 10px rgba(27, 94, 84, 0.035) !important;
}


/* ---------------------------------------------------------
   HEADER
   --------------------------------------------------------- */

#tab_innerdivabc .task-list-card__header,
#tab_innerdivabc .task-list-card div[id^="row"] {
    display: flex !important;
    align-items: flex-start !important;
    justify-content: space-between !important;

    width: 100% !important;
    min-height: auto !important;

    margin: 0 !important;
    padding: 16px 18px !important;

    background: #ffffff !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-sizing: border-box !important;

    transform: none !important;
}


#tab_innerdivabc .task-list-card__main {
    display: flex;
    align-items: flex-start;

    flex: 1 1 auto;
    min-width: 0;

    padding: 0 !important;
}


#tab_innerdivabc .task-list-card__checkbox {
    flex: 0 0 auto;

    width: 14px !important;
    height: 14px !important;

    margin: 3px 12px 0 0 !important;

    accent-color: #17675F;

    cursor: pointer;
}


#tab_innerdivabc .task-list-card__content {
    flex: 1 1 auto;
    min-width: 0;
}


/* title + priority */
#tab_innerdivabc .task-list-card__title-line {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: 8px;

    margin: 0 0 4px 0;
}


#tab_innerdivabc .task-list-card__title {
    margin: 0 !important;

    color: #1d2939 !important;

    font-size: 13px !important;
    font-weight: 650 !important;
    line-height: 1.35 !important;
}


/* priority pill */
#tab_innerdivabc .task-list-card__priority {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;

    padding: 4px 9px !important;

    border: 0 !important;
    border-radius: 20px !important;

    font-size: 8px !important;
    font-weight: 500 !important;
    line-height: 1 !important;
}


#tab_innerdivabc .task-list-card__priority--longterm {
    background: #fff2c9 !important;
    color: #d69c00 !important;
}


#tab_innerdivabc .task-list-card__priority--urgent {
    background: #ffe4e6 !important;
    color: #ef5d68 !important;
}


/* age / overdue */
#tab_innerdivabc .task-list-card__meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: 5px;

    margin-bottom: 3px;

    color: #84919a;

    font-size: 9px;
    line-height: 1.3;
}


#tab_innerdivabc .task-list-card__meta .task-created-age {
    color: #788995 !important;
}


#tab_innerdivabc .task-list-card__meta .task-created-age i {
    margin-right: 3px;
}


#tab_innerdivabc .task-list-card__meta .badge {
    padding: 4px 8px !important;
    font-size: 8px !important;
}


/* Assigned To */
#tab_innerdivabc .task-list-card__assigned {
    color: #87949d !important;

    font-size: 9px !important;
    font-weight: 400 !important;
    line-height: 1.35 !important;
}


#tab_innerdivabc .task-list-card__assigned strong {
    color: #87949d !important;

    font-size: 9px !important;
    font-weight: 400 !important;
}


/* ---------------------------------------------------------
   RIGHT ACTIONS
   --------------------------------------------------------- */

#tab_innerdivabc .task-list-card__actions {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    flex-wrap: wrap;

    flex: 0 0 auto;

    gap: 22px;

    padding: 1px 0 0 18px !important;
}


#tab_innerdivabc .task-list-card__action {
    display: inline-flex;
    align-items: center;

    gap: 5px;

    margin: 0 !important;
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;
    outline: none !important;

    color: #17675F !important;

    font-size: 9px !important;
    font-weight: 600 !important;
    line-height: 1.2 !important;

    text-decoration: none !important;

    cursor: pointer;

    white-space: nowrap;
}


#tab_innerdivabc .task-list-card__action i {
    margin: 0 !important;

    color: #17675F !important;

    font-size: 10px !important;
}


#tab_innerdivabc .task-list-card__action:hover {
    color: #10564f !important;
    text-decoration: none !important;
}


/* ---------------------------------------------------------
   EXPANDED DETAIL
   --------------------------------------------------------- */

#tab_innerdivabc .task-list-card div[id^="demo"].collapse {
    width: auto !important;

    margin: 0 18px !important;
    padding: 14px 0 16px !important;

    background: #ffffff !important;

    border: 0 !important;
    border-top: 1px solid #edf1f1 !important;

    border-radius: 0 !important;
    outline: none !important;

    box-shadow: none !important;

    box-sizing: border-box !important;
}


#tab_innerdivabc .task-detail-grid {
    display: grid;

    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);

    column-gap: 34px;
    row-gap: 12px;

    width: 100%;
}


/* Detail field */
#tab_innerdivabc .task-detail-item {
    position: relative;

    min-height: 32px;

    padding: 0 0 0 9px;
}


/* colored left line */
#tab_innerdivabc .task-detail-item::before {
    content: "";

    position: absolute;

    top: 1px;
    bottom: 1px;
    left: 0;

    width: 2px;

    border-radius: 3px;

    background: #28b99a;
}


#tab_innerdivabc .task-detail-item--description::before {
    background: #ff9f43;
}


#tab_innerdivabc .task-detail-item--reminder::before {
    background: #38b99a;
}


#tab_innerdivabc .task-detail-item--school::before {
    background: #4f8df7;
}


#tab_innerdivabc .task-detail-item--department::before {
    background: #8f67e8;
}


/* Detail label */
#tab_innerdivabc .task-detail-label {
    display: block;

    margin: 0 0 2px 0;

    color: #849199 !important;

    font-size: 8px !important;
    font-weight: 500 !important;
    line-height: 1.25;
}


/* Detail value */
#tab_innerdivabc .task-detail-value,
#tab_innerdivabc .task-detail-value.description {
    margin: 0 !important;

    color: #66727d !important;

    font-size: 9px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;
}


/* read more */
#tab_innerdivabc .task-detail-value .read-more,
#tab_innerdivabc .task-detail-value .read-less {
    font-size: 9px !important;
}


/* ---------------------------------------------------------
   DETAIL FOOTER
   --------------------------------------------------------- */

#tab_innerdivabc .task-detail-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 15px;

    margin-top: 14px;
    padding-top: 13px;

    border-top: 1px solid #edf1f1;
}


#tab_innerdivabc .task-detail-footer__buttons {
    display: flex;
    align-items: center;

    gap: 8px;
}


/* common buttons */
#tab_innerdivabc .task-detail-footer .btn {
    min-width: 50px !important;
    height: 27px !important;

    margin: 0 !important;
    padding: 4px 12px !important;

    border-radius: 5px !important;

    box-shadow: none !important;

    font-size: 9px !important;
    font-weight: 500 !important;
    line-height: 17px !important;
}


/* Edit */
#tab_innerdivabc .task-detail-footer .btn-success {
    background: #17675F !important;

    border: 1px solid #17675F !important;

    color: #ffffff !important;
}


/* Delete */
#tab_innerdivabc .task-detail-footer .btn-primary {
    background: #ffffff !important;

    border: 1px solid #17675F !important;

    color: #17675F !important;
}


/* rollback */
#tab_innerdivabc .task-detail-footer .btn-secondary {
    background: #ffffff !important;

    border: 1px solid #88979d !important;

    color: #66757d !important;
}


/* ---------------------------------------------------------
   SORTING
   --------------------------------------------------------- */

#tab_innerdivabc .task-detail-footer__sorting {
    display: flex;
    align-items: center;
    justify-content: flex-end;

    min-width: 160px;
}


#tab_innerdivabc .task-detail-footer__sorting select {
    width: 150px !important;
    max-width: 150px !important;
    height: 30px !important;

    margin: 0 !important;
    padding: 4px 25px 4px 9px !important;

    background-color: #ffffff !important;

    border: 1px solid #dfe7e7 !important;
    border-radius: 5px !important;

    color: #77848d !important;

    font-size: 8px !important;

    box-shadow: none !important;
}


#tab_innerdivabc .task-detail-footer__sorting select:focus {
    border-color: #17675F !important;
    outline: none !important;
    box-shadow: none !important;
}


#tab_innerdivabc .task-detail-footer__tick {
    margin-left: 7px;

    font-size: 10px;
}


/* remove old hamburger */
#tab_innerdivabc .fa-align-justify {
    display: none !important;
}


/* ---------------------------------------------------------
   MOBILE
   --------------------------------------------------------- */

@media (max-width: 767px) {

    #tab_innerdivabc .task-list-card__header {
        padding: 14px !important;
    }

    #tab_innerdivabc .task-list-card__header {
        flex-direction: column !important;
    }

    #tab_innerdivabc .task-list-card__actions {
        justify-content: flex-start !important;

        gap: 12px;

        padding: 10px 0 0 26px !important;
    }

    #tab_innerdivabc .task-detail-grid {
        grid-template-columns: 1fr;
    }

    #tab_innerdivabc .task-list-card div[id^="demo"].collapse {
        margin: 0 14px !important;
    }

    #tab_innerdivabc .task-detail-footer {
        align-items: flex-start;
        flex-direction: column;
    }

    #tab_innerdivabc .task-detail-footer__sorting {
        justify-content: flex-start;

        width: 100%;
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
        <div class="">
            <!-- <div class="row">
                <div class="col-md-12">
                    <input type="text" id="searchInput" placeholder="Search" class="form-control mb-2" oninput="filterTimeline()">
                </div>
            </div> -->
            <?php //print_r($rec);?>
            @if($rec[0]->task_type!='ToDo')
                @if($rec[0]->tm_status != 'Completed')
                    @if($rec[0]->tm_status != 'Draft')
                        <div class="row hidecheckbox align-items-center" style="background-color: #f2f6f9;padding-bottom: 30px;">
                            <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                                <input type="checkbox"  id="markCompletedAllCheckBox"> Check All Task
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4" style="text-align:right">
                                <div onclick="markCompletedAll()" style="border:solid 0px green;margin:1px;color:#D04545;">
                                    <i class="fa fa-check-square-o" style="background-color: #ffffff; color: #10B981; padding: 10px 18px; border-radius: 12px; display: inline-flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.05);"> Mark Completed All</i>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @endif
            @if($rec[0]->task_type=='ToDo')
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
                                <i class="fa fa-check-square-o" style="background-color: #ffffff; color: #10B981; padding: 10px 18px; border-radius: 12px; display: inline-flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.05);"> Mark Completed All</i>
                            </div>
                        </div>
                    </div>
                    <hr style="margin-top:1rem !important;margin-bottom:1rem !important">
                @endif
            @endif
            @forelse($rec as $subtask)
            <div  style=" padding-top:4px; padding-bottom:4px;">
                {{-- <div class="row" style=" margin-top:2px;" id="row<?=$subtask->id?>">
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
                        <b>  {{$subtask->tm_title}} {{ $subtask->task_type === 'ToDo' ? ('('.$subtask->category_title.')') : '' }}</b>
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
                                    @if($subtask->tm_group_task == 1)
                                        <strong>Assigned To : Group Task</strong>
                                        <button id="readMoreBtn" onclick="getEmp({{$subtask->id}})"> (Employees in Group)</button>
                                        <div id="employeeNamesContainer<?php echo $subtask->id?>" style="display: none;"></div>

                                    @else
                                        <strong>Assigned To : {{$subtask->task_to_name}}</strong>
                                    @endif
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
                                        if($subtask->tm_group_task != 1){
                                            $user_id = $subtask->task_client_id;
                                        }else{
                                            $user_id = 'groupMessage';
                                        }
                                    else
                                        if($subtask->tm_group_task != 1){
                                            $user_id = $subtask->tm_entry_user;
                                        }else{
                                            $user_id = 'groupMessage';
                                        }

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
                            @if($subtask->tm_group_task == 1)
                                <i class="fa fa-search-plus" style="font-size:18px;color:green;cursor:pointer" onclick="getGroupUser({{$subtask->id}})" data-toggle="collapse" data-target="#demo{{$subtask->id}}"> Detail</i>
                            @else
                                <i class="fa fa-search-plus" style="font-size:18px;color:green;cursor:pointer"  data-toggle="collapse" data-target="#demo{{$subtask->id}}"> Detail</i>
                            @endif
                        </div>
                </div> --}}

<div class="task-list-card">

    <div class="task-list-card__header" id="row<?=$subtask->id?>">

        <div class="task-list-card__main">

            {{-- ========================================
                 EXISTING CHECKBOX - LOGIC UNCHANGED
                 ======================================== --}}

            @if($subtask->task_type!='ToDo')
                @if ($subtask->tm_status != 'Completed')
                    @if ($subtask->tm_status != 'Draft')
                        @if($subtask->tm_entry_user==auth()->user()->employee_id)

                            <input
                                type="checkbox"
                                class="subtask-checkbox hidecheckbox task-list-card__checkbox"
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
                            class="subtask-checkbox hidecheckbox task-list-card__checkbox"
                            id="markCompletedAllTodo"
                            name="markCompletedAllTodo[<?=$subtask->id;?>]"
                            value="<?=$subtask->id;?>"
                        >

                    @endif
                @endif
            @endif


            <div class="task-list-card__content">

                {{-- TITLE + PRIORITY --}}
                <div class="task-list-card__title-line">

                    <strong class="task-list-card__title">
                        {{$subtask->tm_title}}

                        {{ $subtask->task_type === 'ToDo'
                            ? ('('.$subtask->category_title.')')
                            : ''
                        }}
                    </strong>


                    @if($subtask->tm_entry_user==auth()->user()->employee_id)

                        @if($subtask->tm_task_priority=='Urgent')

                            <span
                                class="badge badge-success badge-pill
                                       task-list-card__priority
                                       task-list-card__priority--urgent">
                                {{$subtask->tm_task_priority}}
                            </span>

                        @else

                            <span
                                class="badge badge-warning badge-pill
                                       task-list-card__priority
                                       task-list-card__priority--longterm">
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


                {{-- CREATED AGE / DEADLINE --}}
                <div class="task-list-card__meta">

                    <span class="task-created-age">
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
                            $getdays = getdays(
                                $subtask->convert_tm_completion_date
                            );
                        ?>

                        @if($getdays=="1")

                            <span class="blink_me badge rounded-pill bg-warning">
                                {{$getdays}} Day Left
                            </span>

                        @elseif($getdays<=0 && $getdays!="")

                            <span class="blink_me badge rounded-pill bg-danger">
                                {{str_replace("-","",$getdays)}} Days Passed
                            </span>

                        @endif

                    @endif

                </div>


                {{-- LOADER --}}
                <div id="loader{{$subtask->id}}" style="display:none">

                    <div class="spinner4">
                        <div class="bounce1"></div>
                        <div class="bounce2"></div>
                        <div class="bounce3"></div>
                    </div>

                </div>


                {{-- ASSIGNED --}}
                @if($subtask->task_type!='ToDo')

                    <div class="task-list-card__assigned">

                        @if($subtask->tm_entry_user==auth()->user()->employee_id)

                            @if($subtask->tm_group_task == 1)

                                <strong>
                                    Assigned To : Group Task
                                </strong>

                                <button
                                    id="readMoreBtn"
                                    onclick="getEmp({{$subtask->id}})">
                                    (Employees in Group)
                                </button>

                                <div
                                    id="employeeNamesContainer<?php echo $subtask->id?>"
                                    style="display:none;">
                                </div>

                            @else

                                <strong>
                                    Assigned To : {{$subtask->task_to_name}}
                                </strong>

                            @endif

                        @else

                            <strong>
                                Assigned By : {{$subtask->task_sender_name}}
                            </strong>

                        @endif

                    </div>

                @endif

            </div>

        </div>


        {{-- =============================================
             RIGHT ACTIONS
             ============================================= --}}

        <div class="task-list-card__actions">


            {{-- MARK COMPLETED --}}
            @if($subtask->tm_entry_user==auth()->user()->employee_id)

                @if($subtask->tm_status != 'Draft')

                    @if ($subtask->tm_status != 'Completed')

                        <button
                            type="button"
                            class="task-list-card__action"
                            onclick="confirmMarkCompleted(<?=$subtask->id;?>)"
                        >
                            <i class="fa fa-check-circle-o"></i>
                            Mark Completed
                        </button>

                    @endif

                @endif

            @endif


            {{-- SEND MESSAGE --}}
            @if($subtask->task_type!='ToDo')

                <?php

                    $user_id = '';

                    if(
                        auth()->user()->employee_id ==
                        $subtask->tm_entry_user
                    ){

                        if($subtask->tm_group_task != 1){

                            $user_id =
                                $subtask->task_client_id;

                        }else{

                            $user_id = 'groupMessage';
                        }

                    }else{

                        if($subtask->tm_group_task != 1){

                            $user_id =
                                $subtask->tm_entry_user;

                        }else{

                            $user_id = 'groupMessage';
                        }
                    }

                ?>


                <a
                    class="task-list-card__action"
                    href="https://cms.bnu.edu.pk/user-messages?task_id=<?=$subtask->id;?>&to_user_id=<?=$user_id?>"
                >

                    <i class="fa fa-comment-o"></i>

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

                <button
                    type="button"
                    class="task-list-card__action"
                    onclick="draftStatus({{$subtask->id}})"
                >
                    <i class="fa fa-tasks"></i>
                    Assign Task
                </button>

            @endif


            {{-- DETAIL --}}
            @if($subtask->tm_group_task == 1)

                <button
                    type="button"
                    class="task-list-card__action"
                    onclick="getGroupUser({{$subtask->id}})"
                    data-toggle="collapse"
                    data-target="#demo{{$subtask->id}}"
                >
                    <i class="fa fa-chevron-right"></i>
                    Detail
                </button>

            @else

                <button
                    type="button"
                    class="task-list-card__action"
                    data-toggle="collapse"
                    data-target="#demo{{$subtask->id}}"
                >
                    <i class="fa fa-chevron-right"></i>
                    Detail
                </button>

            @endif

        </div>

    </div>
                {{-- <div id="demo{{$subtask->id}}" class="collapse" style="border:solid 2px green;padding:8px">
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
                        <input type="hidden" id="edit_group_task<?=$subtask->id;?>" value="{{$subtask->tm_group_task}}" >
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
                </div> --}}
                <div
    id="demo{{$subtask->id}}"
    class="collapse task-list-card__details"
>

    <div class="task-detail-grid">


        {{-- DESCRIPTION --}}
        <div class="task-detail-item task-detail-item--description">

            <span class="task-detail-label">
                Description
            </span>


            @php
                $description =
                    $subtask->tm_description
                    ? $subtask->tm_description
                    : "Description Not Available";

                $words = str_word_count($description);
            @endphp


            <div class="description task-detail-value">

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
        <div class="task-detail-item task-detail-item--reminder">

            <span class="task-detail-label">
                Reminder Date / Time
            </span>

            <div class="task-detail-value">

                {{
                    $subtask->tm_completion_date
                    ? substr($subtask->tm_completion_date, 0, 10)
                    : "Not Defined"
                }}

                /

                @if(strlen($subtask->task_report_time) > 10)

                    {{ substr($subtask->task_report_time, 11) }}

                @else

                    {{
                        $subtask->task_report_time
                        ? $subtask->task_report_time
                        : "Not Defined"
                    }}

                @endif

            </div>

        </div>


        {{-- SCHOOL --}}
        @if($subtask->task_type!='ToDo')

            <div class="task-detail-item task-detail-item--school">

                <span class="task-detail-label">
                    School
                </span>

                <div class="task-detail-value">
                    {{$subtask->school_code}}
                </div>

            </div>


            {{-- DEPARTMENT --}}
            <div class="task-detail-item task-detail-item--department">

                <span class="task-detail-label">
                    Department
                </span>

                <div class="task-detail-value">
                    {{$subtask->dept_name}}
                </div>

            </div>

        @endif

    </div>


    {{-- ================================================
         ALL HIDDEN EDIT FIELDS - EXACT LOGIC PRESERVED
         ================================================ --}}

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
        id="edit_group_task<?=$subtask->id;?>"
        value="{{$subtask->tm_group_task}}"
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


    {{-- ================================================
         DETAIL FOOTER
         ================================================ --}}

    <div class="task-detail-footer">

        <div class="task-detail-footer__buttons">


            {{-- TO DO COMPLETED ROLLBACK --}}
            @if(
                $subtask->tm_entry_user ==
                    auth()->user()->employee_id
                and
                $subtask->task_type == 'ToDo'
                and
                $subtask->tm_status == 'Completed'
            )

                <button
                    type="button"
                    onclick="rollBack({{$subtask->id}})"
                    class="btn btn-secondary"
                >
                    Roll Back
                </button>

            @endif


            {{-- TO DO EDIT DELETE --}}
            @if(
                $subtask->tm_entry_user ==
                    auth()->user()->employee_id
                and
                $subtask->task_type == 'ToDo'
            )

                <button
                    type="button"
                    onclick="editTodo({{$subtask->id}})"
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


            {{-- TASK COMPLETED ROLLBACK --}}
            @if(
                $subtask->tm_entry_user ==
                    auth()->user()->employee_id
                and
                $subtask->task_type == 'Task'
                and
                $subtask->tm_status == 'Completed'
            )

                <button
                    type="button"
                    id="rollbackBtn"
                    onclick="rollBack({{$subtask->id}})"
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
                $subtask->task_type == 'Task'
            )

                <button
                    type="button"
                    onclick="editTodo({{$subtask->id}})"
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


        {{-- SORT --}}
        @if(
            $subtask->tm_entry_user ==
            auth()->user()->employee_id
        )

            <div class="task-detail-footer__sorting">

                <select
                    id="myDropdown{{$subtask->id}}"
                    class="form-control"
                    onchange="handleDropdownChange(
                        {{$subtask->id}}
                    )"
                >

                    <option value="">
                        Task Sorting Order
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


                <span
                    id="tickIcon{{$subtask->id}}"
                    class="task-detail-footer__tick"
                    style="display:none;"
                >
                    <i
                        class="fa fa-check"
                        style="color:#17675F;"
                    ></i>
                </span>

            </div>

        @endif

    </div>

</div>

</div>
            </div>
            {{-- @endforeach --}}
            @empty
    <div class="alert alert-info text-center mt-3">
        No Record Found
    </div>
@endforelse
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
        // $('#readMoreBtn').click(function() {
            function getEmp(id){
        $.ajax({
            url: 'getGroupTaskEmployee/'+id,
            method: 'GET',
            success: function(response) {
                var employeeIds = response.map(function(obj) {
                    return obj.tmr_employee_id;
                });
                var idsText = employeeIds.join(', ');
                $('#employeeNamesContainer'+id).text(idsText);
                $('#employeeNamesContainer'+id).toggle();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    }
    // });
function getGroupUser(id){
    $.ajax({
            url: '/staff/getGroupUser?taskid='+id,
            method: 'GET',
            success: function(data) {

                console.log(data)
                var employeeIds = [];
                $.each(data,function( key, value ) {
                    console.log(value.tmr_employee_id)
                    //$('#edit_task_client'+id).val(value.tmr_employee_id)
                    // var employeeIdsString = value.tmr_employee_id.join(',');

                    // $('#edit_task_client'+id).val(employeeIdsString);
                    employeeIds.push(value.tmr_employee_id);
                });
                //var employeeIdsString = employeeIds.join(',');

                // Set the resulting string as the value of $('#edit_task_client' + id)
                $('#edit_task_client' + id).val(employeeIds);

            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
}

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
