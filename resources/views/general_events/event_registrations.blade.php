@extends('layouts.app')
@section('title')
    BNU CMS
@stop
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<style>
    /* Custom checkbox style */
    .select-data {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
        outline: none;
    }

    /* Checked state */
    .select-data:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Checkmark icon */
    .select-data:checked::after {
        content: '\2713'; /* Unicode checkmark symbol */
        display: block;
        color: white;
        text-align: center;
        font-size: 16px;
        line-height: 20px;
    }
    .success-flag {
        position: fixed;
        top: 170px;
        right: -300px;
        background-color: #4CAF50;
        color: white;
        padding: 15px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1050;
        transition: right 0.5s;
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
    }
    .success-flag.show {
        right: 0px;
    }
    .dt-button {
    background-color: #4454c3;
    color: #4454c3;
    border: none;
    padding: 5px 10px;
    margin: 5px;
    cursor: pointer;
    font-size: 14px;
    border-radius: 4px;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dt-buttons {
        display: inline-block;
        align-items: center;
    }

    .dataTables_wrapper .dataTables_length label, .dataTables_wrapper .dataTables_filter label{
        margin-top:5px;
    }

    .dataTables_wrapper .dt-buttons {
        margin-right: 15px;
    }
    #clear{
        background-color: #25a4fe;
        border-color: #25a4fe;

    }
    #clear:hover {
    background-color: #1b82cb; /* New color on hover */
    border-color: #1b82cb;     /* Matching border color on hover */
    }
    .edit-btn, .delete-btn{
        width:76px;
    }
    .container{
        background: #ffffff;
        border-radius: 10px;
        padding: 20px 50px !important; /* top-bottom = 20px, left-right = 50px */
        max-width: 80%;
    }
    .form-label {
        color: #333; /* text color */
        padding: 6px 12px;
        font-weight: 700;
        /* border-left: 5px solid #007bff; accent strip on the left */
        border-radius: 4px;
        margin-bottom: 6px;
    }
    .section-heading {
        background-color: #4658c0; /* main strip color */
        color: #fff; /* white text */
        padding: 10px 15px;
        font-weight: 700;
        border-radius: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 15px;
    }


</style>
@section('content')
{{-- ---------------------- --}}
<div id="success-flag" class="success-flag"></div>
<div class="card" style="background:aliceblue;">
    <div class="container d-none">
        <form id="eventForm" method="POST" action="">
            @csrf
                <div class="row"><h3>Create Event:</h3></div>

               <!-- 1. Event Details -->
        <h5 class="section-heading mt-3">Event Details</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="E_EVENT_NAME" class="form-label">Event Name</label>
                <input type="text" class="form-control" name="E_EVENT_NAME" id="E_EVENT_NAME">
                <span class="text-danger error-text E_EVENT_NAME_error"></span>
            </div>
            <div class="col-md-6">
                <label for="E_EVENT_TYPE" class="form-label">Event Type</label>
                <select class="form-control" name="E_EVENT_TYPE" id="E_EVENT_TYPE">
                    <option value="">-- Select Type --</option>
                    <option value="workshop">Workshop</option>
                    <option value="conference">Conference</option>
                    <option value="tour">Tour</option>
                </select>
                <span class="text-danger error-text E_EVENT_TYPE_error"></span>
            </div>
            <div class="col-12">
                <label for="E_EVENT_DESCRIPTION" class="form-label">Description</label>
                <textarea class="form-control" name="E_EVENT_DESCRIPTION" id="E_EVENT_DESCRIPTION" rows="3" required></textarea>
                <span class="text-danger error-text E_EVENT_DESCRIPTION_error"></span>
            </div>
            {{-- <div class="col-md-6">
                <label for="E_EVENT_BANNER" class="form-label">Event Banner</label>
                <input type="file" class="form-control" name="E_EVENT_BANNER" id="E_EVENT_BANNER" accept="image/*">
            </div> --}}
        </div>

        <!-- 2. Schedule -->
        <h5 class="section-heading mt-4">Schedule</h5>
        <div class="row g-4">
            <div class="col-md-3">
                <label for="E_FROM_DATE" class="form-label">Start Date</label>
                <input type="date" class="form-control" name="E_FROM_DATE" id="E_FROM_DATE" required>
                <span class="text-danger error-text E_FROM_DATE_error"></span>
            </div>
            <div class="col-md-3">
                <label for="E_TO_DATE" class="form-label">End Date</label>
                <input type="date" class="form-control" name="E_TO_DATE" id="E_TO_DATE" required>
                <span class="text-danger error-text E_TO_DATE_error"></span>
            </div>
            <div class="col-md-3">
                <label for="E_REGISTRATION_START" class="form-label">Registration Start</label>
                <input type="date" class="form-control" name="E_REGISTRATION_START" id="E_REGISTRATION_START">
                <span class="text-danger error-text E_REGISTRATION_START_error"></span>
            </div>
            <div class="col-md-3">
                <label for="E_REGISTRATION_END" class="form-label">Registration End</label>
                <input type="date" class="form-control" name="E_REGISTRATION_END" id="E_REGISTRATION_END">
                <span class="text-danger error-text E_REGISTRATION_END_error"></span>

            </div>
        </div>

        <!-- 3. Location -->
        <h5 class="section-heading mt-4">Location</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="E_EVENT_VENUE" class="form-label">Venue</label>
                <input type="text" class="form-control" name="E_EVENT_VENUE" id="E_EVENT_VENUE" required>
                <span class="text-danger error-text E_EVENT_VENUE_error"></span>
            </div>
            {{-- <div class="col-md-6">
                <label for="E_EVENT_MAP_URL" class="form-label">Google Map URL</label>
                <input type="url" class="form-control" name="E_EVENT_MAP_URL" id="E_EVENT_MAP_URL">
            </div> --}}
        </div>

        <!-- 4. Organization -->
        <h5 class="section-heading mt-4">Organizing Body</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="school_sel">School</label>
                <select name="school" id="school_sel" class="form-control" onchange="getDepartments();">
                    @php
                        $allSchools = [];
                        foreach ($schools as $value) {
                            $allSchools[] = $value['school_id'];
                        }
                    @endphp
                    <option value="">Select School</option>
                    {{-- <option value="[{{implode(',', $allSchools) }}]">All School</option> --}}
                    @foreach ($schools as $row)
                    <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                    @endforeach
                </select>
                <span class="text-danger error-text school_error"></span>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="department">Department</label>
                <select name="department" disabled id="department" class="form-control" onchange="getPrograms();">
                </select>
                <span class="text-danger error-text department_error"></span>
            </div>
            {{-- <div class="col-md-4">
                <label for="E_CONTACT_EMAIL" class="form-label">Contact Email</label>
                <input type="email" class="form-control" name="E_CONTACT_EMAIL" id="E_CONTACT_EMAIL">
                <span class="text-danger error-text E_CONTACT_EMAIL_error"></span>

            </div> --}}
            <div class="col-md-4">
                <label for="e_organizer" class="form-label">Organizer</label>
                <input type="text" class="form-control" name="e_organizer" id="e_organizer">
            </div>

           {{--  <div class="col-md-6">
                <label for="E_CONTACT_PHONE" class="form-label">Contact Phone</label>
                <input type="text" class="form-control" name="E_CONTACT_PHONE" id="E_CONTACT_PHONE">
            </div> --}}
        </div>

        <!-- 5. Registration & Status -->
        <h5 class="section-heading mt-4">Registration & Status</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label for="E_REGISTRATION_FEE" class="form-label">Registration Fee</label>
                <input type="number" class="form-control" name="E_REGISTRATION_FEE" id="E_REGISTRATION_FEE" min="0" step="0.01">
                 <span class="text-danger error-text E_REGISTRATION_FEE_error"></span>
            </div>
            <div class="col-md-4">
                <label for="E_URL" class="form-label">Event Website / Registration Link</label>
                <input type="url" class="form-control" name="E_URL" id="E_URL">
                <span class="text-danger error-text E_URL_error"></span>
            </div>
            <div class="col-md-4">
                <label for="E_STATUS" class="form-label">Status</label>
                <select class="form-control" name="E_STATUS" id="E_STATUS">
                    <option value="E">Active</option>
                    <option value="D">Inactive</option>
                </select>
                <span class="text-danger error-text E_STATUS_error"></span>
            </div>
        </div>

        <!-- Hidden System Fields -->
        <input type="hidden" id='main-Id' name="mainId" value="">
                <div class="form-group col-sm-12 mt-5" style="display: flex;justify-content: center;">
                    <button type="button" style="width: 150px;" id="saveButton" class="btn btn-primary">Save</button>
                    <button type="button" style="width: 150px;margin-left:50px;" id="filterButton" class="btn btn-primary">Apply Filter</button>
                    <button type="button" style="width: 100px;margin-left:50px;" id="clear" class="btn btn-secondary" onclick="refreshFields()">Clear</button>
                </div>
                <div id="processing_message" class="alert" style="display: none;background-color:#4454c36e;">Processing, please wait...</div>
                <div id="error_message" class="alert alert-danger" style="display: none;"></div>
                <div id="success_message" class="alert alert-success" style="display: none;"></div>
        </form>
    </div>
    <div class="row" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card" style="padding:20px;">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h3>Event Registrations</h3>
                    </div>
                </div>
               <div class="row g-4 d-flex justify-content-center">
                    <div class="col-md-4">
                        <select class="form-control" name="event" id="event" onchange="LoadRegistrations()">
                            {{-- <option value="">-- Select Event --</option> --}}
                             @foreach ($events as $row)
                                <option value="{{ $row->e_id }}">{{ $row->e_event_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                     <div class="row">
                        <div class="col-md-12 text-center">
                            <h3 class="event_heading"></h3>
                        </div>
                    </div>
                    <table id="data-table" class="table display" style="width:100%">
                        <thead>
                          <tr>
                            {{-- <th scope="col">Sr. No.</th> --}}
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Mobile</th>
                            <th scope="col">Organization/Institute</th>
                            <th scope="col">Department</th>
                            <th scope="col">Participation Type</th>
                            <th scope="col">Challan Status</th>
                            {{-- <th scope="col">Action</th> --}}
                          </tr>
                        </thead>

                      </table>

                </div>
            </div>
            {{-- <div class="d-flex justify-content-end mt-2">
                <div class="form-group col-sm-3 d-flex justify-content-end mt-2">
                  <button type="button" id="bulk-Del" class="btn btn-danger"><i class="fa fa-trash"></i> Bulk Delete</button>
                </div>
            </div> --}}
        </div>
    </div>
</div>




{{-- --------------------------- --}}






{{-- --------------------------- --}}

<div class="card" style="margin-top:10px;">
</div>
<div style="clear:both;"></div>
@stop
@section('javascript')






<script>

document.getElementById('saveButton').addEventListener('click', function() {
    var form = document.getElementById("eventForm");
    var formData = new FormData(form);
    // formData.append('school', $('#school_sel').val());
    // formData.append('department', $('#department').val());
    // formData.append('program', $('#program').val());
    // formData.append('session', $('#session_sel').val());
    // formData.append('roadmap_session', $('#roadmap_session_sel').val());
    // formData.append('student_type', $('#studentTypeOption').val());
    // formData.append('fee_type', $('#feeType').val());
    // formData.append('effective_from', $('#eff-from').val());
    // formData.append('effective_to', $('#eff-to').val());
    // formData.append('tution_amount', $('#tutionAmt').val());
    // formData.append('admission_amount', $('#admissionAmt').val());
    // formData.append('security_amount', $('#securityAmt').val());
    // formData.append('medical_amount', $('#medicalAmt').val());
    // formData.append('activity_charges_amount', $('#activitychargesAmt').val());
    // formData.append('per_credit_fee', $('#credFee').val());
    // formData.append('hostel_amount', $('#hostelAmt').val());
    // console.log(formData);
    // console.log($('#school_sel').val());
    // console.log($('#department').val());
    // console.log($('#program').val());
    // console.log($('#session_sel').val());
    // console.log($('#studentTypeOption').val());
    // console.log($('#feeType').val());
    // console.log($('#eff-from').val());
    // console.log($('#eff-to').val());
    // console.log($('#tutionAmt').val());
    // console.log($('#admissionAmt').val());
    // console.log($('#securityAmt').val());
    // console.log($('#medicalAmt').val());
    // console.log($('#credFee').val());
    $("#processing_message").show();
    fetch('add-event', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(response => response.json())

    .then(data => {
        if (data.success) {
            refreshFields();
            $("#processing_message").hide();
            $("#error_message").text('').hide();
            $('#success_message').html(data.msg);
            $('#success_message').fadeIn('slow');
            $('#success_message').fadeOut(5000);
            LoadRegistrations();
        } else {
            // Clear old error messages
            $('span.error-text').text('');

            if ('error' in data) {
                var errors = data.error;
                $.each(errors, function (field, messages) {
                    // field is like "E_EVENT_NAME"
                    // messages is an array of error messages
                    $('span.' + field + '_error').text(messages[0]);
                });
            }

            $("#processing_message").hide();
            $('#success_message').text('').hide();
        }
    })
    .catch(error => {
        $("#processing_message").hide();
        // console.error('There was an error!', error);
    });
});

    var lastURL = '';
$(document).ready(function(){
    LoadRegistrations();
    $(document).on('change', '#select-all', function() {
        if ($(this).prop("checked") == true) {
            $('.select-data').prop('checked', true);
            // console.log($('.select-data').val());
            // $('#bulk-Del').prop('disabled', false);

        } else {
            $('.select-data').prop('checked', false);
            // console.log($('.select-data').val());
            // $('#bulk-Del').prop('disabled', true);
        }
    });
    $(document).on('click', '#bulk-Del', function() {
        var fee_ids = new Array();
        $.each($("input[name='fee_ids[]']:checked"), function () {
            fee_ids.push($(this).val());
        });
        if (fee_ids.length == 0) {
            alert("Please select atleast one row to delete.");
        } else {
            // console.log(fee_ids);
            deleteRows(fee_ids);
        }
    });
    $(document).on('click', '#filterButton', function() {
        LoadRegistrations();
    });
});


</script>

{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>



<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script> -->


<script>

function LoadRegistrations(){
        let filterParams = getFilterParams();
        let eventName = $('#event').val()
                        ? $('#event option:selected').text()
                        : 'Events List';

        $('.event_heading').text(eventName);
        // console.log(filterParams);
        $('#data-table').DataTable({
            scrollX: true, // Enable horizontal scrolling
            responsive: true,
            fixedHeader: false,
            scrollCollapse: false,
            processing: true,
            serverSide: false, //to download all data once
            select: false,
            searching: true,
            destroy: true,
            lengthChange: true,
            paging: true,
            pageLength: 100,
            order: [1, 'desc'],
            ajax:{
                dataType: 'json',
                method: 'post',
                url:  "/api/get_reg_list",
                data: function(d) {
                    return $.extend({}, d, filterParams);
                }
            },
            columns: [

                {data: "er_first_name", name: "er_first_name"},
                {data: "er_last_name", name: "er_last_name"},
                {data: "er_email", name: "er_email"},
                {data: "er_mobile", name: "er_mobile"},
                {data: "er_organization", name: "er_organization"},
                {data: "er_department", name: "er_department"},
                {
                    data: "er_participation_type",
                    name: "er_participation_type",
                   render: function (data, type, row) {
                        if (!data) {
                            return 'NA'; // If null or empty
                        }

                        // Map values to display text
                        switch (data.toLowerCase()) {
                            case 'non_bnu':
                                return 'Non BNU';
                            case 'bnu_student':
                                return 'BNU Student';
                            case 'bnu_faculty':
                                return 'BNU Faculty';
                            case 'bnu_staff':
                                return 'BNU Staff';
                            default:
                                return data; // fallback to the original value if unmatched
                        }
                    }

                },
                {data: "challan_status", name: "challan_status"},




            ],
            // columnDefs: [
            //     {
            //         render: function (data, type, row) {
            //             // console.log(row);
            //             var button = '<div><button class="btn btn-primary btn-sm edit-btn" style="margin-top:16px;" id="'+row["e_id"]+'" onclick="editRow(`'+row["e_id"]+'`,`'+row["e_event_name"]+'`,`'+row["e_event_type"]+'`,`'+row["e_event_description"]+'`,`'+row["e_event_venue"]+'`,`'+row["e_from_date"]+'`,`'+row["e_to_date"]+'`,`'+row["e_school_id"]+'`,`'+row["e_registration_fee"]+'`,`'+row["e_status"]+'`,`'+row["e_url"]+'`,`'+row["e_registration_start"]+'`,`'+row["e_registration_end"]+'`,`'+row["e_dept_id"]+'`,`'+row["e_organizer"]+'`)"><i class="fa fa-edit"></i> Edit</button>'+
            //                                         '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
            //                                     '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows('+row["e_id"]+')"><i class="fa fa-trash"></i> Delete</button></div>';
            //             return button;

            //         },
            //         // className: 'dt-body-center',
            //         // className: 'dt-head-center',
            //         targets: 8,
            //     },
            //     // {
            //     //     render: function (data, type, row) {
            //     //             var   selectbtn = '<div style="margin-top:40px;"><input name="fee_ids[]" class="select-data" idData='+row["ffs_id"]+' value='+row["ffs_id"]+' type="checkbox" ></div>';

            //     //         return selectbtn;

            //     //     },
            //     //     // className: 'dt-body-center',
            //     //     // className: 'dt-head-center',
            //     //     targets: 14,
            //     //     searchable: false,
            //     //     orderable: false
            //     // },

            //     // {
            //     //     "width": "100px", "targets": 1
            //     // },
            // ],
            autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: function () {
                        return $('.event_heading').text() || 'Events List';
                    }
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         exclude columns 12 and 13 (0-based index)
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: '',
                    customize: function (doc) {
                        let dynamicTitle = $('.event_heading').text() || 'Events List';
                        doc.info.title = dynamicTitle;
                        doc.content[0].text = dynamicTitle;
                        doc.content[0].alignment = 'center';
                        doc.content[0].fontSize = 16;
                    }

                },


                {
                    extend: 'print',
                   title: function () {
                        return $('.event_heading').text() || 'Events List';
                    }
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                }
            ]

        });

}
function getRequest(endPoint,successBlock,failBlock){
    $.ajax({
        url: "{{url('/')}}"+endPoint,
        type: "GET",
        dataType: "json",
        success:successBlock
    }).fail(failBlock);
}
function getPrograms() {
    const session_id = $('#session_sel').val(); // Assuming session_id is still used (remove if not)
    const dept_id = $('#department').val();
    // console.log(session_id, dept_id);
    const url = `/api/getProgramFeeStructureForm/get?dept_id=${dept_id}&session_sel=${session_id}`; // Template literals for URL
    getRequest(url,function(res){
                if(res.status){
                    // console.log(res.data);
                    let allProg = [];
                    let degreeLevels = [];
                    $.each(res.data, function(key, value) {
                        allProg.push(value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id);
                        degreeLevels.push(value.prog_degree_level);
                    });
                    populateDegreeLevel(degreeLevels);
                    if(dept_id == ''){
                        let html = `<option value=''>Select Program</option>`;
                        $.each(res.data, function(key, value) {
                        // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                        html += `<option deg-level="${value.prog_degree_level}" value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                        });
                        $('select[name="program"]').html(html); // Update "program" select
                    }else{
                        let html = `<option value=''>Select Program</option>`;
                        html += `<option value='${JSON.stringify(allProg)}'>All Program</option>`;
                        $.each(res.data, function(key, value) {
                        // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                        html += `<option deg-level="${value.prog_degree_level}" value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name}  &#x2192; ${value.prog_degree_level}</option>`;
                        });
                        $('select[name="program"]').html(html); // Update "course" select
                    }
                    ////////
                    action_onAllDepartmentSelection(dept_id);
                    ///////

                } else {
                    // console.error('Error fetching programs:', res.message); // Log error message
                    // Optionally display an error message to the user
                }

            },function(res){
                alert('Some Error found!');
    });
    // console.log(url);
    //   $.ajax({
    //     url: url,
    //     method: 'GET',
    //     success: function(response) {
    //       if (response.status) {
    //         let html = '<option value="">All Program</option>';
    //         $.each(response.data, function(key, value) {
    //           html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
    //         });
    //         $('select[name="course"]').html(html); // Update "course" select
    //       } else {
    //         console.error('Error fetching programs:', response.message); // Log error message
    //         // Optionally display an error message to the user
    //       }
    //     },
    //     error: function(response) {
    //       console.error('Error fetching programs:', response.statusText); // Log error details
    //       // Optionally display a generic error message to the user
    //     }
    //   });
}
function getDepartments(){
    var school_id = $('#school_sel').val();
    getRequest('/api/getDepartmentsFeeStructureForm/get?school_id='+school_id,function(res){
        // console.log(res);
        if(res.status){
            let allDep = [];
            $.each(res.data, function(key, value) {
                allDep.push(value.dept_school_id+'~~'+value.dept_id);
            });
            if(school_id == ''){
                    let html = `<option value=''>Select Department</option>`;
                    $.each(res.data,function( key, value ) {
                        html += '<option value="'+value.dept_id+'" >'+value.dept_name+'</option>';
                    });
                    $('select[name="department"]').html(html);
            }else{
                let html = `<option value=''>Select Department</option>`;
                // html += `<option value='${JSON.stringify(allDep)}'>All Departments</option>`;
                $.each(res.data,function( key, value ) {
                    html += '<option value="'+value.dept_id+'" >'+value.dept_name+'</option>';
                });
                $('select[name="department"]').html(html);
            }
            /////////
            let htmlProg = `<option value=''>Select Program</option>`;
                $('select[name="program"]').html(htmlProg);
                htmlProg = `<option value=''>Select Degree Level</option>`;
                $('select[name="degree_level"]').html(htmlProg);
            action_onAllSchoolSelection(school_id);
            ////
        }
    },function(res){
        alert('Some Error found!');
    });
}
function on_session_change(session){
    // console.log(session.value);
}
function exportToPDF(tableId){
  try {
    const doc = new jsPDF();
    doc.autoTable({ html: `#${tableId}` });
    doc.save('table.pdf');
  } catch (error) {
    // console.error('Error exporting to PDF:', error);
    alert('An error occurred while exporting to PDF. Please try again.');
  }
}
function editRow(
    mainId,
    e_event_name,
    e_event_type,
    e_event_description,
    e_event_venue,
    e_from_date,
    e_to_date,
    e_school_id,
    e_registration_fee,
    e_status,
    e_url,
    e_registration_start,
    e_registration_end,
    e_dept_id,
    e_organizer
) {
    // console.log('Event ID -> ' + mainId);
    // console.log('Event Name -> ' + e_event_name);
    // console.log('Event Type -> ' + e_event_type);
    // console.log('Event Description -> ' + e_event_description);
    // console.log('Venue -> ' + e_event_venue);
    // console.log('From Date -> ' + e_from_date);
    // console.log('To Date -> ' + e_to_date);
    // console.log('School ID -> ' + e_school_id);
    // console.log('Registration Fee -> ' + e_registration_fee);
    // console.log('Status -> ' + e_status);
    // console.log('URL -> ' + e_url);
    // console.log('Registration Start -> ' + e_registration_start);
    // console.log('Registration End -> ' + e_registration_end);
    // console.log('Department ID -> ' + e_dept_id);
    // console.log('Organizer -> ' + e_organizer);

    $('#main-Id').val(mainId);
    $('#E_EVENT_NAME').val(e_event_name);
    $('#E_EVENT_TYPE').val(e_event_type);
    $('#E_EVENT_DESCRIPTION').val(!e_event_description || e_event_description.toLowerCase() === 'null' ? '' : e_event_description);
    $('#E_EVENT_VENUE').val(e_event_venue);
    $('#E_FROM_DATE').val(e_from_date);
    $('#E_TO_DATE').val(e_to_date);
    $('#E_REGISTRATION_START').val(e_registration_start);
    $('#E_REGISTRATION_END').val(e_registration_end);
    $('#e_organizer').val(!e_organizer || e_organizer.toLowerCase() === 'null' ? '' : e_organizer);
    $('#E_REGISTRATION_FEE').val(e_registration_fee ?? 0);
    $('#E_URL').val(e_url);
    $('#E_STATUS').val(e_status);

    $('#school_sel').val(e_school_id);
    getDepartments();//this will set department dropdown menu
    setTimeout(function() {
        $('#department').val(e_dept_id);
    }, 500);  // Adjust the delay as needed


    $('html, body').animate({scrollTop: 0}, 'slow');

}
function deleteRows(mainId){
    var confirmation = window.confirm("Are you sure you want to delete?");
    if (confirmation) {
        $.ajax({
            type: "POST",
            url: "/api/delete_event",
            data: {
                mainId: mainId
            },
            success: function(response) {
                refreshFields();
                $('.select-data').prop('checked', false);
                $('#success-flag').text('Deleted Successfully');
                $('#success-flag').addClass('show');
                setTimeout(function() {
                    $('#success-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds
                LoadRegistrations();
            },
            error: function(error) {
                // Handle the error
                // console.error("Error:", error);
            }
        });
    } else {

    }

}
function refreshFields() {
    $('#main-Id').val('');
    $('#E_EVENT_NAME').val('');
    $('#E_EVENT_TYPE').val('');
    $('#E_EVENT_DESCRIPTION').val('');
    $('#E_EVENT_VENUE').val('');
    $('#E_FROM_DATE').val('');
    $('#E_TO_DATE').val('');
    $('#E_REGISTRATION_START').val('');
    $('#E_REGISTRATION_END').val('');
    $('#e_organizer').val('');
    $('#E_REGISTRATION_FEE').val(0);
    $('#E_URL').val('');
    $('#E_STATUS').val('');
    $('#school_sel').val('');
    $('#department').val('');
}

function action_onAllSchoolSelection(school_id){
    if(school_id != ''){
        school_ids_array = JSON.parse(school_id);
        if(Array.isArray(school_ids_array)){
            $('#department').prop('disabled', true);
            $('#program').prop('disabled', true);
            setTimeout(function() {
                $('#department').val($('#department option:nth-child(2)').val());
                getPrograms();
                setTimeout(function() {
                    $('#program').val($('#program option:nth-child(2)').val());
                }, 500);  // Adjust the delay as needed
            }, 500);  // Adjust the delay as needed
        }else{
            $('#department').prop('disabled', false);
            $('#program').prop('disabled', false);
        }
    }else{
        $('#department').prop('disabled', false);
        $('#program').prop('disabled', false);
    }

}
function action_onAllDepartmentSelection(dept_id){
    if(dept_id != ''){
        dept_ids_array = JSON.parse(dept_id);
        if(Array.isArray(dept_ids_array)){
            $('#program').prop('disabled', true);
            $('#program').val($('#program option:nth-child(2)').val());
        }else{
            $('#program').prop('disabled', false);
        }
    }else{
        $('#program').prop('disabled', false);
    }
    // console.log($('#program').val());

}
function populateDegreeLevel(degreeLevels){
    // console.log(degreeLevels);
    let filterDegreeLevels = degreeLevels.filter(item => item !== "Short_Course");
    let distinctDegreeLevels = [...new Set(filterDegreeLevels)];
    // console.log(distinctDegreeLevels);
    html='<option value="" >Select Degree Level</option>';
    $.each(distinctDegreeLevels,function( key, value ) {
                html += '<option value="'+value+'" >'+value+'</option>';
            });
    $('select[name="degree_level"]').html(html);
}
function setProgramsAccordingly(){
    let session_id = $('#session_sel').val(); // Assuming session_id is still used (remove if not)
    let dept_id = $('#department').val();
    let degreeLevel = $('#degree_level').val();
    let degreeLevelwithArrow = ((degreeLevel != '')? '&#x2192;'+degreeLevel: '');
    // console.log(session_id, dept_id);
    const url = `/api/getProgramFeeStructureForm/get?dept_id=${dept_id}&degreeLevel=${degreeLevel}`; // Template literals for URL
    getRequest(url,function(res){
                if(res.status){
                    // console.log(res.data);
                    let allProg = [];
                    $.each(res.data, function(key, value) {
                        allProg.push(value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id);
                    });
                    // let html = `<option value=''>Select Program</option>`;
                    html = `<option value='${JSON.stringify(allProg)}'>All Program `+degreeLevelwithArrow+`</option>`;
                    $.each(res.data, function(key, value) {
                    // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                    html += `<option deg-level="${value.prog_degree_level}"  value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                    });
                    $('select[name="program"]').html(html); // Update "course" select

                    $('#program option').filter(function() {
                        return $(this).text() === 'All Program';
                    }).prop('selected', true);
                    ////////
                    // action_onAllDepartmentSelection(dept_id);
                    ///////
                    // console.log($('#program').val());

                } else {
                    // console.error('Error fetching programs:', res.message); // Log error message
                    // Optionally display an error message to the user
                }

            },function(res){
                alert('Some Error found!');
    });
}
function getFilterParams() {
    const event = $('#event').val();
    // const school_sel = $('#school_sel').val();
    // const department = $('#department').val();
    // const url = $('#E_URL').val();
    // const status = $('#E_STATUS').val();
    // const reg_fee = $('#E_REGISTRATION_FEE').val();
    // const event_type = $('#E_EVENT_TYPE').val();
    let filterParams = {
        event: event,
        // school_sel: school_sel,
        // department: department,
        // url: url,
        // status: status,
        // reg_fee: reg_fee,
        // event_type: event_type
    };
    return filterParams;
}

</script>

@stop

