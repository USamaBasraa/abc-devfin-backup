@extends('layouts.app')
@section('title')
    BNU CMS
@stop

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- DataTables CSS -->
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css"> --}}
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
        content: '\2713';
        /* Unicode checkmark symbol */
        display: block;
        color: white;
        text-align: center;
        font-size: 16px;
        line-height: 20px;
    }

    .disclaimer {
        color: #4c62ff;
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
    table#data-table tbody tr td {
    padding: 8px;
    border-top: 1px solid #ddd; /* Add top border */
    border-bottom: 1px solid #ddd; /* Add bottom border */
    border-left: none; /* Remove left border */
    border-right: none; /* Remove right border */
    }
    .edit-btn{
            width:76px;
        }
</style>
@section('content')
    {{-- ---------------------- --}}
    <div id="success-flag" class="success-flag"></div>
    <div class="card" style="margin-left:20px; margin-right:20px; margin-top:20px; padding:20px;">
        <form id="filterApplicant" method="POST" action="">
            @csrf
            <div class="row">
                <h3>Admission Scholarship Approval:</h3>
            </div>
            <div class="row filter-row formDiv" style="margin-right:10px;">
                <div class="form-group col-sm-6 session_sel_div">
                    <label for="session_sel">Session</label>
                    <select name="session" id="session_sel" class="form-control" onchange="on_session_change(this);">
                        <option value="">Select Session</option>
                        @foreach ($sessions as $key => $value)
                            <option value="{{ $value->sess_id }}">{{ $value->sess_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="school_sel">School</label>
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
                </div>

                <div class="form-group col-sm-6">
                    <label for="department">Department</label>
                    <select name="department" id="department" class="form-control" onchange="getPrograms();">
                    </select>
                </div>

                <div class="form-group col-sm-6">
                    <label for="course">Program</label>
                    <select name="program" id="program" class="form-control">
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="scholarship_sel">Scholarship</label>
                    <select name="scholarship" id="scholarship_id" class="form-control">
                        <option value="">Select Scholarships</option>
                        @foreach ($scholarships as $row)
                            <option value="{{ $row->scholarship_id }}">{{ $row->scholarship_name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                <button type="button" style="width: 150px;" id="filterButton" class="btn btn-primary"
                    onclick="LoadScholarshipApplicants()">Filter</button>
            </div>

        </form>
        <div class="row" style="margin-top:10px;">
            <div class="col-lg-12 col-md-12">
                <div class="card dtableDiv">

                </div>

            </div>
        </div>
    </div>




        {{-- --------------------------- --}}






        {{-- --------------------------- --}}


        {{-- <div style="clear:both;"></div> --}}
    @stop
    @section('javascript')
        <script>
            // document.getElementById('saveButton').addEventListener('click', function() {
            //     var form = document.getElementById("admission_schlrship");
            //     var formData = new FormData(form);
            //     formData.append('session', $('#session_sel_field').val());
            //     formData.append('school', $('#school_sel_field').val());
            //     formData.append('department', $('#department_field').val());
            //     formData.append('program', $('#program_field').val());

            //     $("#processing_message").show();
            //     fetch('add-admissionsScholarship-criteria', {
            //             method: 'POST',
            //             body: formData,
            //             headers: {
            //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //             }
            //         })
            //         .then(response => response.json())

            //         .then(data => {
            //             if (data.success) {
            //                 // refreshFields();
            //                 $("#processing_message").hide();
            //                 $("#error_message").text('').hide();
            //                 $('#success_message').html(data.msg);
            //                 $('#success_message').fadeIn('slow');
            //                 $('#success_message').fadeOut(5000);
            //                 // LoadScholarshipApplicants();
            //             } else {
            //                 console.log(data);
            //                 var message = "";
            //                 if ('msg' in data) {
            //                     message = data.msg;
            //                 } else if ('errors' in data) {
            //                     var errors = data.errors;
            //                     $.each(errors, function(i, obj) {
            //                         message += "<p>" + obj[0] + "</p>";
            //                     });
            //                 }
            //                 $("#processing_message").hide();
            //                 $('#success_message').text('').hide();
            //                 $('#error_message').html(message);
            //                 $('#error_message').fadeIn('slow');
            //                 $('#error_message').fadeOut(5000);
            //             }
            //         })
            //         .catch(error => {
            //             $("#processing_message").hide();
            //             console.error('There was an error!', error);
            //         });
            // });

            var lastURL = '';
            $(document).ready(function() {
                LoadScholarshipApplicants();
                // $('#feeType').change(function() {
                //     if ($(this).val() == 'Credit_hours') {
                //         $('.credFeeDiv').removeClass('d-none');
                //     } else {
                //         $('.credFeeDiv').addClass('d-none');
                //         $('#credFee').val('');

                //     }
                // });
                // $(document).on('change', '#select-all', function() {
                //     if ($(this).prop("checked") == true) {
                //         $('.select-data').prop('checked', true);
                //         console.log($('.select-data').val());
                //         // $('#bulk-Del').prop('disabled', false);

                //     } else {
                //         $('.select-data').prop('checked', false);
                //         console.log($('.select-data').val());
                //         // $('#bulk-Del').prop('disabled', true);
                //     }
                // });
                // $(document).on('click', '#bulk-Del', function() {
                //     var fee_ids = new Array();
                //     $.each($("input[name='fee_ids[]']:checked"), function() {
                //         fee_ids.push($(this).val());
                //     });
                //     if (fee_ids.length == 0) {
                //         alert("Please select atleast one row to delete.");
                //     } else {
                //         deleteRows(fee_ids);
                //     }
                // });
                // $(document).on('click', '#changeFilter', function() {
                //     $('#filterApplicant').removeClass('d-none');
                //     $('#admission_schlrship').addClass('d-none');
                // });
            });
        </script>

        {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin/tableExport.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.2/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>


        <script src="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap4.min.js"></script> --}}



        <script>
            function LoadScholarshipApplicants() {
                var session = $('#session_sel').val();
                var school = $('#school_sel').val();
                var department = $('#department').val();
                var program = $('#program').val();
                var scholarship = $('#scholarship_id').val();
                // console.log(session);
                // console.log(school);
                // console.log(department);
                // console.log(program);




                let table = `<div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="data-table" class="table display">
                        <thead>
                          <tr>
                            <th scope="col">Applicant ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">School</th>
                            <th scope="col">Program</th>
                            <th scope="col">Scholarship</th>
                            <th scope="col">Percentage Given</th>
                            <th scope="col">Scholarship Status</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                      </table>
                    </div>`;
                $('.dtableDiv').html(table);
                $('#data-table').DataTable({
                    scrollX: true, // Enable horizontal scrolling
                    responsive: true,
                    fixedHeader: false,
                    scrollCollapse: false,
                    processing: true,
                    serverSide: true,
                    select: false,
                    searching: true,
                    destroy: true,
                    lengthChange: true,
                    paging: true,
                    pageLength: 10,
                    order: [1, 'desc'],
                    ajax: {
                        dataType: 'json',
                        method: 'post',
                        url: "/api/admission-scholarship-approval/filter_applicants",
                        data: function(d) {
                            d.session_id = session;
                            d.school_id = school;
                            d.department_id = department;
                            d.program_id = program;
                            d.scholarship_id = scholarship;
                        }
                    },
                    columns: [{
                            data: "aai_id"
                        },
                        {
                            data: "api_name"
                        },
                        {
                            data: "school_code"
                        },
                        {
                            data: "aai_programid"
                        },
                        {
                            data: "scholarship_name"
                        },
                        {
                            data: "fasc_schlrshp_percentage",
                            render: function(data, type, row) {
                                // Check if data is present and not null or undefined
                                if (data) {
                                    return data + " %";
                                } else {
                                    return "NA";
                                }
                            }
                        },
                        {
                            data: "fasc_scholarship_status"
                        },
                    ],
                    columnDefs: [
                        {

                            render: function(data, type, row) {
                                // Determine the button text, function, and class based on the scholarship status
                                // var buttonText = (row["fasc_scholarship_status"] === 'Approved') ? '' : 'Approve';
                                // var buttonClass = (row["fasc_scholarship_status"] === 'Approved') ? '' : 'btn-success';
                                var button = '';
                                    if(row["fasc_scholarship_status"] === 'Disapproved'){
                                        button = '<div class="btn-group" style="margin-top: 10px;">' +
                                                    '<button class="btn btn-success btn-sm edit-btn" id="' + row["fasc_id"] + '" onclick="approve(`' + row["fasc_id"] + '`,`' + row["fasc_scholarship_status"] +'`,`' + row["aai_id"] +'`,`' + row["aai_sdpo_sess_id"] +'`,`' + row["fasc_scholarship_id"] +'`)">Approve</button>' +
                                                '</div>';
                                    }

                                        return button;

                            },
                            // className: 'dt-body-center',
                            // className: 'dt-head-center',
                            targets: 7,
                        },
                        // {
                        //     render: function (data, type, row) {
                        //             var   selectbtn = '<div style="margin-top:40px;"><input name="fee_ids[]" class="select-data" idData='+row["fsc_id"]+' value='+row["fsc_id"]+' type="checkbox" ></div>';

                        //         return selectbtn;

                        //     },
                        //     // className: 'dt-body-center',
                        //     // className: 'dt-head-center',
                        //     targets: 10,
                        //     searchable: false,
                        //     orderable: false
                        // },

                        {
                            "width": "250px",
                            "targets": 3
                        },
                        // {
                        //     "width": "50px", "targets": 6
                        // },
                        // {
                        //     "width": "10px", "targets": 10
                        // },
                    ],
                });
            }
            function verifyDocs(apiId, progId, sessId) {
                var baseUrl = window.location.origin; // Dynamically gets the base URL
                var newLink = baseUrl + "/doc-verifications?api_id=" + apiId + "&prog_id=" + progId + "&sess_id=" + sessId;

                // Open the new link in a new tab or window
                window.open(newLink, "_blank");
            }
            function getRequest(endPoint, successBlock, failBlock) {
                $.ajax({
                    url: "{{ url('/') }}" + endPoint,
                    type: "GET",
                    dataType: "json",
                    success: successBlock
                }).fail(failBlock);
            }

            function getPrograms() {
                const session_id = $('#session_sel').val(); // Assuming session_id is still used (remove if not)
                const dept_id = $('#department').val();
                // console.log(session_id, dept_id);
                const url =`/api/getProgramFeeStructureForm/get?dept_id=${dept_id}&session_sel=${session_id}`; // Template literals for URL
                getRequest(url, function(res) {
                    if (res.status) {
                        console.log(res.data);
                        // let allProg = [];
                        // $.each(res.data, function(key, value) {
                        //     allProg.push(value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id);
                        //     // allProg.push(value.prog_id);
                        // });
                        let html = `<option value=''>Select Program</option>`;
                        // html += `<option value='${JSON.stringify(allProg)}'>All Program</option>`;
                        $.each(res.data, function(key, value) {
                            // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                            html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                        });
                        $('select[name="program"]').html(html); // Update "course" select
                        ////////
                        // action_onAllDepartmentSelection(dept_id);
                        ///////

                    } else {
                        console.error('Error fetching programs:', res.message); // Log error message
                        // Optionally display an error message to the user
                    }

                }, function(res) {
                    alert('Some Error found!');
                });
                console.log(url);
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

            function getDepartments() {
                var school_id = $('#school_sel').val();
                var session_id = $('#session_sel').val();
                // if (school_id != '' || session_id != '') {
                //     $('#filterButton').removeAttr('disabled');
                // } else {
                //     $('#filterButton').attr('disabled', true);
                // }
                getRequest('/api/getDepartmentsFeeStructureForm/get?school_id=' + school_id, function(res) {
                    console.log(res);
                    if (res.status) {
                        console.log(res.data);
                        // let allDep = [];
                        // $.each(res.data, function(key, value) {
                        //     allDep.push(value.dept_school_id+'~~'+value.dept_id);
                        // });
                        let html = `<option value=''>Select Department</option>`;
                        // html += `<option value='${JSON.stringify(allDep)}'>All Departments</option>`;
                        $.each(res.data, function(key, value) {
                            html += '<option value="' + value.dept_id + '" >' + value.dept_name + '</option>';
                        });
                        $('select[name="department"]').html(html);
                        /////////
                         htmlProg = `<option value=''>Select Program</option>`;
                        $('select[name="program"]').html(htmlProg);
                        // action_onAllSchoolSelection(school_id);
                        ////
                    }
                }, function(res) {
                    alert('Some Error found!');
                });
            }

            // function on_session_change(session) {
            //     var session_id = session.value;
            //     var school_id = $('#school_sel').val();
            //     if (school_id != '' || session_id != '') {
            //         $('#filterButton').removeAttr('disabled');
            //     } else {
            //         $('#filterButton').attr('disabled', true);
            //     }

            // }

            function exportToPDF(tableId) {
                try {
                    const doc = new jsPDF();
                    doc.autoTable({
                        html: `#${tableId}`
                    });
                    doc.save('table.pdf');
                } catch (error) {
                    console.error('Error exporting to PDF:', error);
                    alert('An error occurred while exporting to PDF. Please try again.');
                }
            }
            function approve(fasc_id,status,aai_id, sess_id, schlrship_id) {
                console.log('mianId->' + fasc_id);
                $.ajax({
                        type: "GET",
                        url: "/api/admission-scholarship-approval/updateApprovalStatus",
                        data: {
                            fasc_id: fasc_id,
                            status: status,
                            aai_id: aai_id,
                            sess_id: sess_id,
                            schlrship_id: schlrship_id
                        },
                        success: function(response) {
                            console.log(response);
                            $('#success-flag').text(response.data);
                            $('#success-flag').addClass('show');
                            setTimeout(function() {
                                $('#success-flag').removeClass('show');
                            }, 3000); // Hide after 3 seconds
                            LoadScholarshipApplicants();

                        },
                        error: function(error) {
                            // Handle the error
                            console.error("Error:", error);
                        }
                    });


            }

            function deleteRows(mainId) {
                var confirmation = window.confirm("Are you sure you want to delete?");
                if (confirmation) {
                    $.ajax({
                        type: "POST",
                        url: "/api/master-scholarship-criteria/delete_scholarshipCriteria",
                        data: {
                            mainId: mainId
                        },
                        success: function(response) {
                            $('.select-data').prop('checked', false);
                            $('#success-flag').text('Deleted Successfully');
                            $('#success-flag').addClass('show');
                            setTimeout(function() {
                                $('#success-flag').removeClass('show');
                            }, 3000); // Hide after 3 seconds
                            LoadScholarshipApplicants();

                        },
                        error: function(error) {
                            // Handle the error
                            console.error("Error:", error);
                        }
                    });
                } else {

                }

            }

            function refreshFields() {
                $('#main-Id').val('');
                $('#session-wise').prop('checked', true);
                $('.session_sel_div').removeClass('d-none'); //show session selection div
                $('.validity-type').val('session'); //setting validityType value
                $('.year_sel_div').addClass('d-none'); //hiding thd year fields
                $('#year_sel').val(''); //emptying the from date
                $('#session_sel').val('');
                $('#school_sel').val('');
                $('#scholarship_id').val('');
                $('#percent').val('');
                $('#department').val('');
                $('#program').val('');
                $('#cgpaFrom').val('');
                $('#cgpaTo').val('');
                $('#eff-from').val('');
                $('#eff-to').val('');
            }

            function getPDF() {
                //var lastURL = lastURL.replace("/reports/faculty", " ");
                //var lastURL='/reports/faculty';
                //alert(lastURL+'&result-for=print');
                //return false;
                window.open(lastURL + '&result-for=print');

            }

            function validatePercentageInput(event) {
                const input = event.target;
                if (input.value !== '') {
                    const value = parseInt(input.value, 10);
                    if (value < 1 || value > 100) {
                        $('.percent-disclaimer').text('Enter a value between 1 and 100');
                        $('.percent-disclaimer').css('color', 'red');
                        // input.setCustomValidity('Please enter a value between 1 and 100');
                    } else {
                        $('.percent-disclaimer').text('');
                    }
                }
            }
            function getStudentAppliedSchlrshps(needbase_scholarship, transgenderbase, deansbase, sportsbase, bnuinstitutionalpatronage, vcscholarship){
                let studentSchlrshp = {};
               // Add key-value pairs to the associative array
                studentSchlrshp['0'] = ['1', 'Need based', needbase_scholarship];
                studentSchlrshp['1'] = ['86', 'Transgender Base', transgenderbase];
                studentSchlrshp['2'] = ['87', 'Deans Base', deansbase];
                studentSchlrshp['3'] = ['88', 'Sports Base', sportsbase];
                studentSchlrshp['4'] = ['89', 'BNU Institutional Patronage', bnuinstitutionalpatronage];
                studentSchlrshp['5'] = ['90', 'VC Scholarship', vcscholarship];
                // console.log(studentSchlrshp);
                let filteredSchlrshp = [];
                // Iterate over each key-value pair in the studentSchlrshp object
                Object.keys(studentSchlrshp).forEach(key => {
                    if (studentSchlrshp[key][2] && studentSchlrshp[key][2].toLowerCase() === 'yes') {
                        filteredSchlrshp.push(studentSchlrshp[key]);
                    }
                });
                // Output the filtered scholarships
                console.log(filteredSchlrshp);
                return filteredSchlrshp;
            }
            // function validateCGPAInput(event) {
            //     const input = event.target;
            //     if (input.value !== '') {
            //         const value = parseFloat(input.value);
            //         console.log(value);
            //         if (value < 1 || value > 4) {
            //             $('.cgpaRange-disclaimer').text('Enter CGPA between 1 and 4');
            //             $('.cgpaRange-disclaimer').css('color', 'red');
            //             // input.setCustomValidity('Please enter a value between 1 and 100');
            //         } else {
            //             $('.cgpaRange-disclaimer').text('');
            //         }
            //     }
            // }
            // function action_onAllSchoolSelection(school_id){
            //     if(school_id != ''){
            //         school_ids_array = JSON.parse(school_id);
            //         if(Array.isArray(school_ids_array)){
            //             $('#department').prop('disabled', true);
            //             $('#program').prop('disabled', true);
            //             setTimeout(function() {
            //                 $('#department').val($('#department option:nth-child(2)').val());
            //                 getPrograms();
            //                 setTimeout(function() {
            //                     $('#program').val($('#program option:nth-child(2)').val());
            //                 }, 500);  // Adjust the delay as needed
            //             }, 500);  // Adjust the delay as needed
            //         }else{
            //             $('#department').prop('disabled', false);
            //             $('#program').prop('disabled', false);
            //         }
            //     }else{
            //         $('#department').prop('disabled', false);
            //         $('#program').prop('disabled', false);
            //     }

            // }
            // function action_onAllDepartmentSelection(dept_id){
            //     if(dept_id != ''){
            //         dept_ids_array = JSON.parse(dept_id);
            //         if(Array.isArray(dept_ids_array)){
            //             $('#program').prop('disabled', true);
            //             $('#program').val($('#program option:nth-child(2)').val());
            //         }else{
            //             $('#program').prop('disabled', false);
            //         }
            //     }else{
            //         $('#program').prop('disabled', false);
            //     }
            //     console.log($('#program').val());

            // }
            ////lower code is of no use
            // function setCgpaValue(cgpa, relation){
            //     console.log(cgpa, relation)
            //     cgpa = parseFloat(cgpa);
            //     if(relation == 'greater_than' || relation == 'less_than'){
            //         return cgpa;
            //     }else if(relation == 'greater_or_equal'){
            //         cgpa = parseFloat((cgpa - 0.01).toFixed(2));
            //         return cgpa;
            //     }else if(relation == 'less_or_equal'){
            //         cgpa = parseFloat((cgpa + 0.01).toFixed(2));
            //         return cgpa;
            //     }
            // }

            // function setCgpaFromValue(cgpa,relation){
            //     cgpa = parseFloat(cgpa);
            //     cgpa = parseFloat((cgpa - 0.01).toFixed(2));
            //     return cgpa;
            // }
            // function setCgpaToValue(cgpa,relation){
            //     cgpa = parseFloat(cgpa);
            //     cgpa = parseFloat((cgpa + 0.01).toFixed(2));
            //     return cgpa;
            // }
        </script>

    @stop
