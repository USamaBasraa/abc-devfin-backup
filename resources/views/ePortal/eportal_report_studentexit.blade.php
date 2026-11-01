
@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

@include('components.toast-notifications')

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
<div class="container-fluid px-4 py-4">
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
        <!-- Modern Header -->
        <div class="card-header text-white border-0" style="background-color: #3e5871; padding: 25px 30px;">
            <div class="d-flex align-items-center">
                <div class="mr-3">
                    <i class="fa fa-graduation-cap" style="font-size: 36px;"></i>
                </div>
                <div>
                    <h3 class="mb-1 font-weight-bold">E-Portal Student Exit Form Report</h3>
                    <p class="mb-0" style="opacity: 0.9; font-size: 14px;">Student University Leaving Exit Form</p>
                </div>
            </div>
        </div>
        
        <!-- Form Section -->
        <div class="card-body" style="padding: 30px;">
            <div class="row" id="setCateg">
                <!-- From Date -->
                <div class="col-md-4 mb-4">
                    <label for="fromDate" class="font-weight-bold text-dark mb-2">
                        <i class="fa fa-calendar text-primary mr-2"></i>From Date
                    </label>
                    <input type="date" name="fromDate" id="fromDate" class="form-control form-control-lg" style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 12px;" placeholder="Select From Date">
                    <h6 data-error-for="fromDate" class="validation-error pt-2 text-danger"></h6>
                </div>
                
                <!-- To Date -->
                <div class="col-md-4 mb-4">
                    <label for="toDate" class="font-weight-bold text-dark mb-2">
                        <i class="fa fa-calendar text-primary mr-2"></i>To Date
                    </label>
                    <input type="date" name="toDate" id="toDate" class="form-control form-control-lg" style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 12px;" placeholder="Select To Date">
                    <h6 data-error-for="toDate" class="validation-error pt-2 text-danger"></h6>
                </div>
                
                <!-- Action Buttons -->
                <div class="col-md-4 mb-4">
                    <label class="font-weight-bold text-dark mb-2">
                        <i class="fa fa-file-text text-primary mr-2"></i>Download Reports
                    </label>
                    <div class="d-flex flex-column gap-2">
                        <button id="downloadExcel" class="btn btn-outline-success btn-lg shadow-sm mb-2" onclick="downloadExcel2()" style="border-radius: 8px; font-weight: 600; border-width: 2px;">
                            <i class="fa fa-file-excel-o mr-2"></i>Download Excel
                        </button>
                        <button id="downloadPDFBtn" class="btn btn-outline-danger btn-lg shadow-sm mb-2" onclick="downloadPDF()" style="border-radius: 8px; font-weight: 600; border-width: 2px;">
                            <i class="fa fa-file-pdf-o mr-2"></i>Download PDF
                        </button>
                        <button id="downloadExcel3" class="btn btn-outline-info btn-lg shadow-sm" onclick="downloadExcel3()" style="border-radius: 8px; font-weight: 600; border-width: 2px;">
                            <i class="fa fa-file-excel-o mr-2"></i>Download Excel Exam
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-lg {
    transition: all 0.3s ease;
}
.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2) !important;
}
.form-control:focus {
    border-color: #3e5871;
    box-shadow: 0 0 0 0.2rem rgba(62, 88, 113, 0.25);
}
</style>

<div style="margin-top:10px;margin-bottom:50px">
    
   
</div>

<!-- Loading Overlay for Report Generation -->
<div id="pdfLoadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.75); z-index: 9999; justify-content: center; align-items: center;">
    <div class="card shadow-lg border-0" style="max-width: 400px; border-radius: 15px; overflow: hidden;">
        <div class="card-body text-center p-5">
            <!-- Animated Icon -->
            <div class="mb-4">
                <i id="loaderIcon" class="fa fa-file-text-o text-primary" style="font-size: 48px; animation: pulse 1.5s ease-in-out infinite;"></i>
            </div>
            
            <!-- Spinner -->
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem; border-width: 4px;">
                <span class="sr-only">Loading...</span>
            </div>
            
            <!-- Title -->
            <h4 id="loaderTitle" class="mb-2 font-weight-bold" style="color: #3e5871;">Generating...</h4>
            
            <!-- Subtitle -->
            <p id="loaderSubtext" class="text-muted mb-0" style="font-size: 14px;">Please wait, it may take some time to generate</p>
            
            <!-- Progress Bar Animation -->
            <div class="progress mt-4" style="height: 4px; background: #e9ecef;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 100%"></div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.1); }
}
</style>

@stop
@section('javascript')
<script src="<?=asset('js/bootstrap-autocomplete.js')?>"></script>
<script type="text/javascript">

// General loader function for report generation
function showReportLoader(fileType) {
    const loaderConfig = {
        'excel': {
            title: 'Generating Excel...',
            icon: 'fa fa-file-excel-o text-success'
        },
        'pdf': {
            title: 'Generating PDF...',
            icon: 'fa fa-file-pdf-o text-danger'
        }
    };
    
    const config = loaderConfig[fileType] || {
        title: 'Generating...',
        icon: 'fa fa-file-text-o text-primary'
    };
    
    $('#pdfLoadingOverlay').css('display', 'flex');
    $('#loaderTitle').text(config.title);
    $('#loaderIcon').removeClass().addClass(config.icon);
}

function hideReportLoader() {
    $('#pdfLoadingOverlay').css('display', 'none');
    // Reset to default
    $('#loaderTitle').text('Generating...');
    $('#loaderIcon').removeClass().addClass('fa fa-file-text-o text-primary');
}

function downloadExcel2(){ 
    fromDate = $("#fromDate").val();
    toDate = $("#toDate").val();
    
    // Validate date fields
    if (!fromDate || !toDate) {
        showToast('warning', 'Please select both From Date and To Date to generate the report.', 'Required Fields');
        return;
    }
    
    // Show loader
    showReportLoader('excel');
    
    url = "{{route('eportal-report-student-excel')}}?from_date="+fromDate+"&to_date="+toDate;
    
    // Fetch Excel and trigger download
    fetch(url)
        .then(response => response.blob())
        .then(blob => {
            // Create download link
            const downloadUrl = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.download = 'Student_Exit_Form_Report_' + new Date().toISOString().slice(0,19).replace(/:/g,'-') + '.xls';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(downloadUrl);
            
            hideReportLoader();
            showToast('success', 'Excel report generated successfully!');
        })
        .catch(error => {
            console.error('Error generating Excel:', error);
            hideReportLoader();
            showToast('error', 'Failed to generate Excel report. Please try again.');
        });
}
function downloadExcel3(){ 
    fromDate = $("#fromDate").val();
    toDate = $("#toDate").val();
    url = 'https://staff.bnu.edu.pk/HOD/degreeClearanceReport.php';
    window.open(url, '_blank');
}
function downloadPDF(){
    fromDate = $("#fromDate").val();
    toDate = $("#toDate").val();
    
    // Validate date fields
    if (!fromDate || !toDate) {
        showToast('warning', 'Please select both From Date and To Date to generate the report.', 'Required Fields');
        return;
    }
    
    // Show loader
    showReportLoader('pdf');
    
    url = "{{route('eportal-report-student-pdf')}}?from_date="+fromDate+"&to_date="+toDate;
    
    // Fetch PDF to ensure it's generated, then open in new tab
    fetch(url)
        .then(response => response.blob())
        .then(blob => {
            const blobUrl = URL.createObjectURL(blob);
            window.open(blobUrl, '_blank');
            hideReportLoader();
        })
        .catch(error => {
            console.error('Error generating PDF:', error);
            hideReportLoader();
            showToast('error', 'Failed to generate PDF report. Please try again.');
        });
}
 $(document).ready(function () {
    //alert('2')
    getRequest(0);
    //getCateg();
});

function setDept(sel){
    //alert(sel.value)
    $('#selectedOption').val(sel.value); 

    getRequest(sel.value);
}

function getRequest(sel){
    
  
    $.ajax({
        type: 'get',
        url: "{{route('fetch_report2')}}?sel="+sel,
        //url: '/fetch_report?sel='+sel,
        processData: false,
        contentType: false,        
        success: function(response) {
            var html = '';
            var sr_no = 1;
            $.each(response,function(key,value) {

                // var timestamp1 = value.er_escalation_time;
                const dateStringWithOffset = value.er_escalation_time;
                const dateStringWithoutOffset = dateStringWithOffset.split(' ')[0] + ' ' + dateStringWithOffset.split(' ')[1];
                var timestamp2 = value.er_resolving_datetime;
                var entryDatetime = value.er_entry_datetime;

                console.log(dateStringWithoutOffset+'==='+timestamp2+'==='+entryDatetime);
                var date = new Date(timestamp2);


                // Add 5 hours
                if(entryDatetime > value.er_resolving_datetime){
                    formattedResult = date.setHours(date.getHours() + 5);
                }else{
                    formattedResult =timestamp2;
                }
                
                // Format the result in a desired format (e.g., YYYY-MM-DD HH:mm:ss)
                //formattedResult = date.toISOString().slice(0, 19).replace('T', ' ');
                //timeDifference = formattedResult - date1;
                formattedResult = date.getFullYear() + '-' +
                ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                ('0' + date.getDate()).slice(-2) + ' ' +
                ('0' + date.getHours()).slice(-2) + ':' +
                ('0' + date.getMinutes()).slice(-2) + ':' +
                ('0' + date.getSeconds()).slice(-2);
                

                //console.log(dateStringWithoutOffset+'==='+timestamp2);

                // Parse the timestamps into Date objects
                var date1 = new Date(dateStringWithoutOffset);
                var date2 = new Date(formattedResult);

                // Calculate the time difference in milliseconds
                var timeDifference = date2 - date1;
                //console.log(timeDifference);
                var color = '';
                if(timeDifference > 0){
                    color = 'tomato';
                }
               
                
                //console.log(timeDifference,'zain');
                // Calculate the difference in days and hours
                var daysDifference = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
                var hoursDifference = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

                // Output the difference in days and hours
                //console.log('Difference in days: ' + daysDifference);
               // console.log('Difference in hours: ' + hoursDifference);        

               if(daysDifference < 0){
                    daysDifference='Within time';
                    hoursDifference='';
                }else{
                    daysDifference=daysDifference+' Days and '+hoursDifference+' hours';
                }

                // Output the formatted date and time
               // console.log('Formatted Timestamp: ' + formattedResult);
                //console.log(value);
                html += '<tr>';
                html += '<td style="text-align:center;">'+ (sr_no++) +'</td>';
                html += '<td>'+value.student_name+'</td>';
                html += '<td>'+value.school_code+'</td>';
                html += '<td>'+value.ec_title+'</td>';
                html += '<td style="text-align:center;">'+value.er_entry_datetime+'</td>';
                if(value.er_resolving_datetime){
                    html += '<td style="text-align:center;">'+formattedResult+'</td>';
                    html += '<td style="color:'+color+'">'+daysDifference+'</td>';  
                }else{
                    html += '<td style="text-align:center;">N / A</td>';
                    html += '<td style="text-align:center;">N / A</td>';
                }  
                          
                // html += '<td style="text-align:center;">'+value.er_escalation_time+'</td>';
                html += '<td scope="col"><button type="button" class="btn btn-success" onclick="openModalStatus('+value.er_id+',\'' + value.er_sai_enrollment_no + '\')" data-toggle="modal" data-target="#application_tracking"><i class="fa fa-search mr-1"></i>Details</button></td>';
                // html += '<td style="text-align:center;"><button type="button" class="btn btn-primary" onclick="chkDetail()">Details</button></td>';
                html += '</tr>';               
            });
            if(html != ''){
                $('#result-body').html(html);
            }else{
                $('#result-body').html('No Result Found!');
            }

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
                        
                    // if(value.er_resolving_datetime){
                    //     html += '<td>'+(value.er_ecd_appr_datetime==null?formattedResult:value.er_ecd_appr_datetime)+'</td>';
                    //     html += '<td>'+daysDifference+' Days and '+hoursDifference+' hours</td>';
                    // }else{
                    //     html += '<td style="text-align:center;">N / A</td>';
                    //     html += '<td style="text-align:center;">N / A</td>';
                    // }
                    
                  
                    //alert(taskstatus);
                    // if(taskstatus == 'Pending'){                        
                    //     $('#comptime').show();
                    //     $('#ecstime').show();
                    //     html += '<td>'+(value.er_resolving_datetime==null?'N/A':value.er_resolving_datetime)+'</td>';
                    // }else{
                    //     $('#comptime').hide();
                    //     $('#ecstime').hide();
                        
                    // }
                  
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

