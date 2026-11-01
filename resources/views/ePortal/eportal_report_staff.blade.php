
@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<style>
.invisible { display: none; }
</style>
<style type="text/css">

    .chat_div {

        display: flex;
        justify-content: flex-start;
        align-items: center;
        border-radius: 20px;
        border: 1px solid green;
        padding: 10px;
        background-color: #DCF8C6;
        /* text-align: left; */
        margin-bottom: 10px;
        margin-right:5px;
        margin-left:5px;
        color: #000;
        font-size: 18px;
        padding-left: 15px;
        /* word-break: break-all; */


    }

    .chat_div_other {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        border-radius: 20px;
        border: 1px solid green;
        padding: 10px;
        background-color: #FFF;
        margin-right:5px;
        margin-left:5px;
        /* text-align: left; */
        margin-bottom: 10px;
        color: #000;
        font-size: 18px;
        padding-left: 15px;
        /* word-break: break-all; */
    }

    .msg_card_body {
        height: calc(100vh - 200px);
        overflow-y: auto;
    }

    /* New styles for mobile responsiveness */
    @media only screen and (max-width: 767px) {
        /* .float-left.hidden-xs {
            display: none !important;
        } */

        .action-header {
            padding: 10px;
        }

        .widget-chat-box {
            margin-bottom: 15px;
        }
        .tm-title-mobile {
        display: block;
    }
    }
    .user_outer_div{
        border:2px solid #3D4BB7;
        float:left;
        border-radius: 10px;
        margin-right:5px;
        padding:5px;
        margin: 1px;
        position: relative;
        background-color:#ccffee !important;
    }
    .user_inner_div{
            float:left;
    }
    .user_outer_div_strong {
            position: absolute;
            top: 1px; /* Adjust the top distance as needed */
            right: 5px; /* Adjust the right distance as needed */
            color: red;
            cursor: pointer; 
    
    }
    div{
        border:0px solid black;
    }
</style>
<div class="card e-panel" style="margin:10px;width:98%;">
    <div class="card-header">
        <h3 align ="center"> E-Portal Report Generator</h3>   <br>
    </div>
    <div class="row" style="margin-top:10px">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12" align ="center">
            <input type="hidden" id="selectedOption" value="0">            
            <select class="form-control" style="width:277px" id="dept_id" onchange="setDept(this)" name="dept_id">
                <option value="0">Select Request Type</option>
                @foreach($dept as $user)
                    <option value="{{$user->id}}">{{$user->ec_title}}</option>
                @endforeach
            </select>
        </div>        
    </div>
    <div class="row" style="margin-top:10px">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12" align="center">
            <input type="hidden" id="selectedOption" value="0">            
            <select class="form-control" style="width:277px" id="request_status" onchange="setDept(this)"  name="request_status">
                <option value="0">Select Request Status</option>
                <option value="Completed">Completed</option>
                <option value="Pending">Pending</option>
                <option value="Declined">Declined</option>             
            </select>
        </div>        
    </div>

    <div class="row" style="margin-top:10px;margin-bottom:50px" id="setCateg">
            
    </div>
    
   
</div>
<div class="card e-panel" style="margin:10px;width:98%;">  
    <div class="card-body">
        <table id="result-table" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Student Name</th>
            <th>School Code</th>
            <th>Title</th>
            <th>Entry Datetime</th>
            <th>Resolving Time</th>
            <th>Escalation Date/Time</th>
            <th>Pending From Resource</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="result-body">
        <!-- Filled by jQuery -->
    </tbody>
</table>
    </div>
</div>
<div class="modal" id="application_tracking">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1150px !important">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#4454c3;color: white !important;">
                <span style="text-color:white" id="application_status"></span>
                <button type="button" class="close" style="color:white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" style="text-align: left;">
                <div class="card-body">
                <div align="left" id="escalate-tr"> </div>

                    <table class="table table-sm  table-bordered">
                        <thead>
                        <tr>
                            {{-- <th scope="col">Sr.No</th> --}}
                            <th scope="col"><b>Department</b></th>
                            <th style="text-align:center;" scope="col"><b>Status</b></th>
                            <th style="text-align:center;" scope="col"><b>Remarks</b></th>
                            <th style="text-align:center;" scope="col"><b>Request Time</b></th>
                            <th style="text-align:center;" scope="col"><b>Approval/Completion Time</b></th>
                            <!-- <th style="text-align:center;" id="comptime" scope="col"><b>Completion Time</b></th> -->
                            <th style="text-align:center;"  scope="col"><b>Resolution Time</b></th>
                            <!-- <th style="text-align:center;display:none" id="ecstime" scope="col"><b>Esclation Time</b></th> -->
                        </tr>
                        </thead>
                        <tbody id="result-body2" ><tr><td colspan="12" style="text-align:center;">No Filter Apply Yet!</td></tr></tbody>
                    </table>
                </div>
               
            </div>
        </div>
    </div>
</div>
@stop
@section('javascript')



<script src="<?=asset('js/bootstrap-autocomplete.js')?>"></script>
<script type="text/javascript">
 $(document).ready(function () {
    //alert('2')
    // getRequest(0);
    //getCateg();
});

function setDept(sel){
    //alert(sel.value)
    $('#selectedOption').val(sel.value); 

    getRequest(sel.value);
}

function getRequest(sel){
    
    var reqStatus = $('#request_status').val();
    $.ajax({
        type: 'get',
        url: "{{route('fetch_report2')}}?sel="+sel+"&reqStatus="+reqStatus,
        //url: '/fetch_report?sel='+sel,
        processData: false,
        contentType: false,        
        success: function(response) {
    var html = '';
    var sr_no = 1;

    // Destroy DataTable before reinitializing
    if ($.fn.DataTable.isDataTable('#result-table')) {
        $('#result-table').DataTable().clear().destroy();
    }

    $.each(response, function(key, value) {
        var formattedResult = '';
        var color = '';
        var daysDifference = '';
        var hoursDifference = '';

        if (value.er_escalation_time !== null && value.er_escalation_time !== '') {
            const dateStringWithOffset = value.er_escalation_time;
            const dateStringWithoutOffset = dateStringWithOffset.split(' ')[0] + ' ' + dateStringWithOffset.split(' ')[1];
            var timestamp2 = value.er_resolving_datetime;
            var entryDatetime = value.er_entry_datetime;

            var date = new Date(timestamp2);

            if (entryDatetime > value.er_resolving_datetime) {
                date.setHours(date.getHours() + 5);
            }

            formattedResult = date.getFullYear() + '-' +
                ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                ('0' + date.getDate()).slice(-2) + ' ' +
                ('0' + date.getHours()).slice(-2) + ':' +
                ('0' + date.getMinutes()).slice(-2) + ':' +
                ('0' + date.getSeconds()).slice(-2);

            var date1 = new Date(dateStringWithoutOffset);
            var date2 = new Date(formattedResult);

            var timeDifference = date2 - date1;

            if (timeDifference > 0) {
                color = 'tomato';
            }

            var days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
            var hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

            if (days < 0) {
                daysDifference = 'Within time';
                hoursDifference = '';
            } else {
                daysDifference = days + ' Days and ' + hours + ' hours';
            }
        }

        html += '<tr>';
        html += '<td style="text-align:center;">' + (sr_no++) + '</td>';
        html += '<td>' + value.student_name + '</td>';
        html += '<td>' + value.school_code + '</td>';
        html += '<td>' + value.ec_title + '</td>';
        html += '<td style="text-align:center;">' + value.er_entry_datetime + '</td>';

        if (value.er_resolving_datetime) {
            if (value.er_escalation_time !== null && value.er_escalation_time !== '') {
                html += '<td style="text-align:center;">' + formattedResult + '</td>';
                html += '<td style="color:' + color + '">' + daysDifference + '</td>';
            } else {
                html += '<td style="text-align:center;">' + value.er_resolving_datetime + '</td>';
                html += '<td style="color:' + color + '">-</td>';
            }
        } else {
            html += '<td style="text-align:center;">N / A</td>';
            if (!value.er_escalation_time) {
                html += '<td style="text-align:center;">N / A</td>';
            } else {
                html += '<td style="text-align:center;">' + value.er_escalation_time + '</td>';
            }
        }
        if (value.er_request_status == "Pending") {
            html += '<td style="text-align:center;">' + value.pending + '</td>';
        } else {
            html += '<td style="text-align:center;">Completed</td>';
        }
        html += '<td><button type="button" class="btn btn-success" onclick="openModalStatus('+ value.er_id +',\''+value.er_sai_enrollment_no+'\')" data-toggle="modal" data-target="#application_tracking"><i class="fa fa-search mr-1"></i>Details</button></td>';
        html += '</tr>';
    });

    $('#result-body').html(html);

    // Reinitialize DataTable
    $('#result-table').DataTable({
    responsive: true,
    pageLength: 10,
    lengthMenu: [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "All"]
    ],
    ordering: true,
    dom: 'Blfrtip', // ✅ includes the "l" for page length dropdown
    buttons: ['copy', 'excel', 'pdf', 'print']
});
}

    })
}
function openModalStatus(id,username){
    // alert(id);
    // alert(username);
    $('#msg_cotainer_send').html('');
    $('#RequestID').val(id);
    $('#sendToUsername').val(username);
    // $('#application_status').html('asdasd');
    $("#application_status").css("font-weight", "bold");
    //   Use jQuery to show the modal
    $("#applicationStatus").modal("show");
    $.ajax({
        type: 'get',
        url: '/vc/eportal-approval-authority-report?id='+id,
        processData: false,
            contentType: false,
            data: {
                'id':id
            },
            success: function(res) {
                $('#application_status').html(res.data[0].title+' ('+username+')');
                var dateTime=new Date();
                var taskstatus = $('#taskStatus').val();
                //alert(dateTime);
                html='';
                $.each(res.data,function( key, value ) {
                    console.log(value);                   
                    var name = '';
                    var dept = '';
                    var status = '';
                    var remarks = '';
                    var restime = '';
                    var chkminus = 0;
                    if(value.full_name == null){
                        name = ' ('+value.res_full_name+') (Resolving Authority)';
                        status = value.er_request_status;
                        remarks = value.er_remarks;
                        
                        var timestamp1 = value.er_entry_datetime;
                        var timestamp2 = value.er_resolving_datetime;

                        //alert(timestamp1+'==='+timestamp2);
                        if(timestamp2 != null){                   
                  

                            // Parse the timestamps into Date objects
                            var date1 = new Date(timestamp1);
                            var date2 = new Date(timestamp2);

                            // Calculate the time difference in milliseconds
                            var timeDifference = date2 - date1;
                            if(timeDifference < 0){
                                var date = new Date(timestamp2);
                                // Add 5 hours
                                formattedResult = date.setHours(date.getHours() + 5);
                                // Format the result in a desired format (e.g., YYYY-MM-DD HH:mm:ss)
                                //formattedResult = date.toISOString().slice(0, 19).replace('T', ' ');
                                timeDifference = formattedResult - date1;
                                    formattedResult = date.getFullYear() + '-' +
                                    ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                                    ('0' + date.getDate()).slice(-2) + ' ' +
                                    ('0' + date.getHours()).slice(-2) + ':' +
                                    ('0' + date.getMinutes()).slice(-2) + ':' +
                                    ('0' + date.getSeconds()).slice(-2);

                                // Output the formatted date and time
                                console.log('Formatted Timestamp: ' + formattedResult);
                                // formattedResult = new Date(formattedResult);
                                //chkminus = 1;

                            }else{
                                formattedResult=timestamp2;
                            }
                            // Calculate the difference in days and hours
                            restime = formattedResult;
                            var daysDifference = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
                            var hoursDifference = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        }else{
                            restime = 'N/A';
                            daysDifference = '0';
                            hoursDifference = '0';
                        }
                    }else if(value.res_full_name == null){
                        name = ' ('+value.full_name+') (Approving Authority)';
                        status = value.erd_status;
                        remarks = value.er_ecd_remarks;                        
                        var timestamp1 = value.er_entry_datetime;
                        var timestamp2 = value.er_ecd_appr_datetime;              

                        // Parse the timestamps into Date objects
                        var date1 = new Date(timestamp1);
                        var date2 = new Date(timestamp2);

                        // Calculate the time difference in milliseconds
                        var timeDifference = date2 - date1;

                        if(timeDifference < 0){
                            //alert('bilal')
                            var date = new Date(timestamp2);
                            // Add 5 hours
                            formattedResult = date.setHours(date.getHours() + 5);
                            // Format the result in a desired format (e.g., YYYY-MM-DD HH:mm:ss)
                            //formattedResult = date.toISOString().slice(0, 19).replace('T', ' ');
                            timeDifference = formattedResult - date1;
                                formattedResult = date.getFullYear() + '-' +
                                ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                                ('0' + date.getDate()).slice(-2) + ' ' +
                                ('0' + date.getHours()).slice(-2) + ':' +
                                ('0' + date.getMinutes()).slice(-2) + ':' +
                                ('0' + date.getSeconds()).slice(-2);

                            // Output the formatted date and time
                            console.log('Formatted Timestamp: ' + formattedResult);
                            // formattedResult = new Date(formattedResult);
                            //alert(timeDifference);
                            chkminus = 1;


                        }else{
                            formattedResult=timestamp2;
                        }
                        // Calculate the difference in days and hours
                        var daysDifference = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
                        var hoursDifference = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        restime = value.er_ecd_appr_datetime;
                    }
                    if(value.department == null){
                        dept = value.res_dept;
                    }else if(value.res_dept == null){
                        dept = value.department;
                    }
                    var srno = parseInt(key+1);
                    html += '<tr>';
                    // html += '<td>'+srno+'</td>';
                    html += '<td >'+dept+name+'</td>';
                    html += '<td><span class="badge ' + (status === 'Pending' ? 'badge-info' : 'badge-primary') + '">' + status + '</span></td>';                   
                    html += '<td>'+(remarks!= null?remarks:'N/A')+'</td>';              
                    // html += '</tr>';
                    // html += '<tr>';
                    html += '<td>'+value.er_entry_datetime+'</td>';
                    html += '<td>'+restime+'</td>';
                    //alert(chkminus)
                    if(chkminus == 1){
                        html += '<td>N / A</td>';
                    }else{
                        html += '<td>'+daysDifference+' Days and '+hoursDifference+' hours</td>';
                    }
                    html += '</tr>';
                   $('#escalate-tr').html('<strong>Request Description: </strong>:<span class="blink_me">'+value.er_description+'</span>');

                });
                if(html != ''){
                    $('#result-body2').html(html);

                   // html += '<td><span class="blink_me">'+daysDifference+' Days and '+hoursDifference+' hours ago</span></td>';

                    $('#canvas').show();
                }else{
                    $('#teacher_remarks').hide();
                    $('#pdf').hide();
                    $('#result-body2').html('<tr><td colspan="12" style="text-align:center;font-size:24px">No Result Found!</td></tr>');
                    $('#canvas').hide();
                    $('#course_name').html('Course Name');
                }
            },
            error: function(xhr) {

            }
    });
    //getMsgs(id);
}
</script>
@stop

