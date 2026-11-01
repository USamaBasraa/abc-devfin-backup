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
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"> --}}
<style>
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
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        .custom-td {
            padding: 8px;
            border: 1px solid #ddd;
            word-wrap: break-word;
            white-space: normal;
        }
</style>
</head>

@section('content')
<div id="success-flag" class="success-flag"></div>
<div class="container-fluid">
    <div class="card p-4 pt-4">
        <div class="row"><h3>Student Ledger:</h3></div>
        <form id="searchForm">
            <div class="row" style="display: flex; justify-content: center;">
                <div class="col-6 col-sm-12 col-md-6">
                    <div class="form-group row">
                        <label for="challan" class="col-sm-4 col-form-label text-right">Student ID/Enrollment No.</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input placeholder="" class="form-control" type="text" name="studentEnrollment" id="studentEnrollment" onkeypress="handleEnterPress(event)">
                            </div>
                        </div>
                    </div>
                    <small class="searchChallan-disclaimer"></small>
                    <div id="error_message" class="alert alert-danger" style="display: none;"></div>

                    <!-- Additional fields below -->

                    <div class="form-group row">
                        <label for="field1" class="col-sm-4 col-form-label text-right">Academic Session:</label>
                        <div class="col-sm-8">
                            <select name="session" id="session_sel" class="form-control">
                                <option value="">Select Session</option>
                                @foreach ($sessions as $key => $value)
                                    <option value="{{ $value->sess_code }}">{{ $value->sess_code }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label  class="col-sm-4 col-form-label text-right" for="from">From</label>
                        <div class="col-sm-8">
                            <input type="date" name="effective_from" id="eff-from" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label  class="col-sm-4 col-form-label text-right" for="to">To</label>
                        <div class="col-sm-8">
                            <input type="date" name="effective_to" id="eff-to" class="form-control">
                        </div>
                    </div>
                    <!-- Radio buttons for Challan Type -->
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label text-right">Challan Type</label>
                        <div class="col-sm-8 d-flex align-items-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input challanType" type="radio" name="challanType" id="unpaid" value="unpaid">
                                <label class="form-check-label" for="challanType1">Unpaid</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input challanType" type="radio" name="challanType" id="paid" value="paid">
                                <label class="form-check-label" for="challanType2">Paid</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input challanType" type="radio" name="challanType" id="all" checked value="all">
                                <label class="form-check-label" for="challanType3">All</label>
                            </div>

                            <div class="form-check form-check-inline" style='display:none;'>
                            <input class="form-check-input beforeF2024" type="checkbox" id="beforeF2024" name="beforeF2024" value="beforeF2024">
                            <label class="form-check-label" for="beforeF2024">Before F2024</label>
                        </div>


                        </div>
                    </div>
                    <div class="input-group-append" style="display: flex; justify-content: center;">
                        <button onclick='search("enrollment")' type="button" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
                        
                        <button class="btn btn-primary ml-2" onclick="exportToPDF()">
                            <i class="fa fa-file-pdf-o"></i>Student Scholarship Detail 
                        </button>
                        <button class="btn btn-success ml-2" onclick="exportToExcel()">
                            <i class="fa fa-file-excel-o"></i> Export All Scholarships Details
                        </button>

                    </div>

                </div>
            </div>
        </form>

        {{-- <div class="row" style="display: flex; justify-content: center;">
            <div class="col-md-4">
                <div class="form-group row">
                    <label for="challan" >Student Name:</label>
                    Ali
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group row">
                    <label for="challan" >Student Name:</label>
                    Ali
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group row">
                    <label for="challan">Student Name:</label>
                    Ali
                </div>
            </div>
        </div> --}}
        <!-- <table class="custom-table d-none"> -->
        <table class="custom-table d-none">

        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 10px; text-align: center;">
            <div style="flex: 1;"></div> <!-- First column (empty) -->
            <div style="flex: 1; font-weight: bold;">Student Profile</div> <!-- Second column -->
            <div style="flex: 1;" id='studentImage'>
                <!-- <img src="{{ asset('unpaid.png') }}" alt="Profile" style="width:50px; height:50px;"> -->
                <!-- <img src="https://cms.bnu.edu.pk/students_pics/F2022-368.jpg" alt="Student Image" class="img-fluid rounded-circle" style="width:70px; height:70px;"> -->
            </div> <!-- Third column -->
        </div>


            <tr>
                <td class="custom-td"><b>Student ID:</b></td>
                <td class="custom-td td-studentId">NA</td>
                <td class="custom-td"><b>Enrollment No:</b></td>
                <td class="custom-td td-enrollemt">NA</td>
            </tr>
            <tr>
                <td class="custom-td"><b>Student Name:</b></td>
                <td class="custom-td td-name">NA</td>
                <td class="custom-td"><b>Parent/Guardian:</b></td>
                <td class="custom-td td-guardian">NA</td>
            </tr>
            <tr>
                <td class="custom-td"><b>Gaurdian CNIC:</b></td>
                <td class="custom-td td-cnic">NA</td>
                <td class="custom-td"><b>School:</b></td>
                <td class="custom-td td-school">NA</td>
            </tr>
            <tr>
                <td class="custom-td"><b>Department:</b></td>
                <td class="custom-td td-dept">NA</td>
                <td class="custom-td"><b>Program:</b></td>
                <td class="custom-td td-prog">NA</td>
            </tr>
            <tr>
                <td class="custom-td"><b>Student Type:</b></td>
                <td class="custom-td td-studenttype" >NA</td>
                <td class="custom-td"><b>Semester:</b></td>
                <td class="custom-td td-sem">NA</td>

            </tr>
            <tr>

                <td class="custom-td"><b>Student Status:</b></td>
                <td class="custom-td td-status">NA</td>
            </tr>
        </table>


        <div id="dataDiv"   style="margin-top:20px;">
        </div>
        <div class="row" style="margin-top:10px;">
            <div class="col-lg-12 col-md-12">
                <div id='ledgerData'  class="card d-none">
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table id="data-table" class="table display" style="width:100%">
                            <thead>
                              <tr>
                                <th scope="col">Session</th>

                                <th scope="col">Pinnacle Challan</th>
                                <th scope="col">CMS challan</th>
                                <th scope="col">Amount Payable</th>
                                <th scope="col">Amount Paid</th>
                                <th scope="col">Issue Date</th>
                                <th scope="col">Due Date</th>
                                <th scope="col">Paid Date</th>
                                <th scope="col">Installment No</th>
                                <th scope="col">Fee Status</th>
                                <th scope="col">Action</th>
                                {{-- <th scope="col">Created At</th> --}}
                              </tr>
                            </thead>
                          </table>
                    </div>
                </div>
            </div>
    </div>









<hr>

<div id="showDataInTable" class="table-responsive"></div>

    </div>
</div>

 <!-- Details Modal -->
 <div class="modal fade" id="detail-Modal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header pb-0">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h4 class="modal-title" id="detailsModalLabel"><b>Fine Detail</b></h4>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -45px !important;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Content dynamically updated via JavaScript -->
                    <div id="modal-details-content">
                        <div class="details-container">
                            <div class="row">
                                <div class="col label">Challan No</div>
                                <div id="challan_num" class="col value"></div>
                            </div>
                            <div class="row">
                                <div class="col label">1Bill Invoice No</div>
                                <div id="1bill_challan_num" class="col value"></div>
                            </div>
                            <div class="row">
                                <div class="col label">Fine Category</div>
                                <div id="fine_category" class="col value"></div>
                            </div>
                            <div class="row">
                                <div class="col label">Amount Payable</div>
                                <div id="amount" class="col value"></div>
                            </div>
                            <div class="row">
                                <div class="col label">Due Date</div>
                                <div id="dueDate" class="col value"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

@section('javascript')
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> --}}

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script> --}}
<!-- Include html2canvas library -->
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script> --}}

<script>










function handleEnterPress(event) {
    console.log(event);
    if (event.key === 'Enter') {
        event.preventDefault(); // Prevent form submission if it's inside a form
        search("enrollment");
    }
}
//  $('#modal-1').on('hidden.modal', function (e) {
//     $('#form-1')[0].reset();
//  });
$(document).ready(function(){

});
function exportToPDF() {
        let studentEnrollment = $('#studentEnrollment').val();
    if (studentEnrollment) {
        let url = "{{ config('app.app_url') }}";
        var link = `${url}/assets/cms/reports/scholarshipdetail_report.php?enrollment=${studentEnrollment}`;
        window.open(link, '_blank');
        event.preventDefault();
    } else {
        alert('Please enter a valid Student Student ID / Enrollment ID.');
        event.preventDefault();
    }
}
function exportToExcel(){
    // let studentEnrollment = $('#studentEnrollment').val();
    // if (studentEnrollment) {
        let url = "{{ config('app.app_url') }}";
        // var link = `${url}assets/cms/reports/scholarship_detail_list_excel.php?enrollment=${studentEnrollment}`;
        var link = `${url}/assets/cms/reports/scholarship_detail_list_excel.php`;
        window.open(link, '_blank');
        event.preventDefault();
    // } else {
    //     alert('Please enter a valid Student Student ID / Enrollment ID.');
    //     event.preventDefault();
    // }
    
       
}
function search(searchBy) {
    var valid = true;
    // valid = validateSearchBar(searchBy, valid);//this function will make valid variable false if fields are empty
    if(valid){
        let form = document.getElementById('searchForm');

        let beforeF2024 = document.getElementById('beforeF2024');
        let data = new FormData(form);


        // if (beforeF2024.checked) {
        //     // Append additional data to the FormData object
        //     data.append('beforeF2024', beforeF2024);
        // }

        // let challNo = $('#studentChallan').val();
        let enrollmentNo = $('#studentEnrollment').val();
        if(searchBy == 'challan'){
            fetch('/api/feeadjustment/get-challan-rec', {
                method: 'POST', // Using POST method
                body: data,
            })
            .then(response => response.json())
            .then(({ success, challanData, adjustmentData }) => { // Correct destructuring syntax
                if (success) {
                    console.log(success);
                    makeChallanDataHtml(challanData, adjustmentData);
                    $("#error_message").text('').hide();
                }
                else{
                    console.log(success);
                    // $('#success_message').text('').hide();
                        $('#error_message').html('No Data found against challan no. '+challNo);
                        $('#error_message').fadeIn('slow');
                        $('#error_message').fadeOut(5000);
                // const notificationType = 'error'; // or 'success', 'info', 'warning', etc.
                // const message = usernameError;
                // const alertHTML = createAlert(notificationType, message);
                // dataDiv.innerHTML = alertHTML;
                    // return false;
                }
            })
        }else if(searchBy == 'enrollment'){
            studentEnrollment = $('#studentEnrollment').val();
            loadLedger(studentEnrollment);
            // session_sel = $('#session_sel').val();
            // effective_from = $('#eff-from').val();
            // effective_to = $('#eff-to').val();
            // challanType = $('.challanType').val();
            // $.ajax({
            //     type: 'GET',
            //     url: '/get-legder',
            //     data: {
            //         studentEnrollment : studentEnrollment,
            //         session_sel : session_sel,
            //         effective_from : effective_from,
            //         effective_to : effective_to,
            //         challanType : challanType
            //     },
            //     success: function(res) {
            //         // refreshFields();
            //         // $("#error_message_enrollment").text('').hide();
            //         // $('#success_message_enrollment').html('Saved Successfully');
            //         // $('#success_message_enrollment').fadeIn('slow');
            //         // $('#successLedger').fadeOut(5000);
            //         loadLedger();

            //     },
            //     error: function(res, xhr, status, error) {
            //         var data = res.responseJSON;
            //         var message = "";
            //         if ('msg' in data) {
            //             message = data.msg;
            //         } else if ('errors' in data) {
            //             var errors = data.errors;
            //             $.each(errors, function (i, obj) {
            //                 message += "<p>" + obj[0] + "</p>";
            //             });
            //         }
            //         $('#success_message_enrollment').text('').hide();
            //         $('#error_message_enrollment').html(message);
            //         $('#error_message_enrollment').fadeIn('slow');
            //         $('#error_message_enrollment').fadeOut(5000);
            //     }
            // });
        }

    }

}

function loadLedger(searchBy){
    $('#ledgerData').removeClass('d-none');
    enrollment = searchBy
    session_sel = $('#session_sel').val();
    effective_from = $('#eff-from').val();
    effective_to = $('#eff-to').val();
    challanType = $('input.challanType:checked').val();
    let beforeF2024 = document.getElementById('beforeF2024');


    let studentImageDiv = document.getElementById('studentImage');
    let htmlToAppend = `<img src="https://cms.bnu.edu.pk/students_pics/${enrollment.toUpperCase()}.jpg" alt="Student Image" class="img-fluid rounded-circle" style="width:70px; height:70px;">`;
    studentImageDiv.innerHTML = htmlToAppend;


    $('#data-table').DataTable({
        scrollX: true, // Enable horizontal scrolling
        responsive:true,
        fixedHeader: false,
        scrollCollapse: false,
        processing: true,
        serverSide: false,//donwnload all data
        select: false,
        searching: true,
        destroy: true,
        lengthChange: true,
        paging: true,
        pageLength: 10,
        order: [],
        ajax:{
        dataType: 'json',
            method: 'get',
            url:  "/get-legder",
            data: function(d) {
                    let beforeF2024Checked = document.getElementById('beforeF2024').checked;
                    beforeF2024Checked = beforeF2024Checked ? 1 : 0;
                    d.enrollment = enrollment;
                    d.session_sel = session_sel;
                    d.effective_from = effective_from;
                    d.effective_to = effective_to;
                    d.challanType = challanType;
                    d.beforeF2024Checked = beforeF2024Checked;
                },
            dataSrc: function(json) {
                populateAdditionalTable(json.data, enrollment); // Populate additional table with data
                return json.data; // Return the data for DataTable
            },
        },
        columns: [
            
            {data: "session_name"},
            {
                data: "buyercodechallanno",
                render: function(data, type, row) {
                    return data && data.trim() !== '' ? data : 'NA';
                }
            },
            {
                data: "cms_challan_id",
                render: function(data, type, row) {
                    return data && data.trim() !== '' ? data : 'NA';
                }
            },
            {data: "totalamountpayable"},
            {data: "paidamt"},
            {
            data: "issuedate",
                render: function(data, type, row) {
                    return data ? data.split(' ')[0] : 'NA';
                }
            },
            {
            data: "duedate",
                render: function(data, type, row) {
                    return data ? data.split(' ')[0] : 'NA';
                }
            },
          {
    data: "paidon",
    render: function(data, type, row) {
        let paidDate = (data && data.trim() !== '')
            ? data
            : (row.callback_paid_on && row.callback_paid_on.trim() !== '')
                ? row.callback_paid_on
                : null;
        if (!paidDate) return 'NA';
        // Handle Oracle timestamp format: "04-JUL-25 05.00.35.000000 PM"
        return paidDate.split(' ')[0];
    }
    },
            {data: "installments"},
            // {data: "creationdate"},
            {
                data: "challanstatus",
                render: function(data, type, row) {
                if (data === "PAID") {
                    return `<img src="{{ asset('paid.png') }}" alt="Paid" style="width:80px; height:50px;"> `;
                } else if (data === "UNPAID") {
                    return `<img src="{{ asset('unpaid.png') }}" alt="Unpaid" style="width:60px; height:50px;"> `;
                } else if (data === "CANCELLED") {
                    return `<img src="{{ asset('cancelled.png') }}" alt="Cancelled" style="width:60px; height:50px;"> `;
                } else {
                    return `<img src="{{ asset('unknown.png') }}" alt="Unknown" style="width:60px; height:50px;"> `;
                }
            }
            }


        ],

        columnDefs: [
            {
                render: function (data, type, row) {
                     console.log(` ledger=  ${row.buyercodechallanno}`);
                    // buyercodechallanno
                    // let beforeF2024Checked = document.getElementById('beforeF2024').checked;
                    // Convert the boolean value to 'old' or 'new'
                    let beforeF2024Value = !(Number(row.session_id)) ? 'old' : 'new';
                    let challan = !(Number(row.session_id)) ? row.buyercodechallanno : row.cms_challan_id;
                    // ${row["buyercodechallanno"]}


                    
                    let button;
                    if (row.sc_challan_type === 'Fine') {
                        button = `
                            <button type="button" class="btn btn-primary btn-sm preview-btn" onclick="showDetailModal('${row.cms_challan_id}','${row.totalamountpayable}','${row.duedate}','${row.finetype}')">
                                Show Detail Fine
                            </button>
                        `;
                    } else {
                        button = `
                            <button class="btn btn-outline-primary btn-sm preview-btn" id="${challan}" onclick="previewRow('${challan}', '${beforeF2024Value}')">
                                <i class="fa fa-eye"></i> Preview
                            </button>
                        `;
                    }



                    return button;

                },
                // className: 'dt-body-center',
                // className: 'dt-head-center',
                targets: 10,
            }
        //     {
        //         render: function (data, type, row) {
        //                 var   selectbtn = '<div><input name="fee_ids[]" class="select-data" idData='+row["id"]+' value='+row["id"]+' type="checkbox" ></div>';

        //             return selectbtn;

        //         },
        //         // className: 'dt-body-center',
        //         // className: 'dt-head-center',
        //         targets: 13,
        //         searchable: false,
        //         orderable: false
        //     },

        //     {
        //         "width": "100px", "targets": 1
        //     }
        ],
        autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Bfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Student Ledger',
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
                    title: 'Student Ledger',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                },
                {
                    extend: 'print',
                    title: 'Student Ledger',
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
function showDetailModal(challanId,amount,dueDate,fc_category_name){
    // alert(fc_category_name);
    $('#challan_num').text(challanId);
    $('#1bill_challan_num').text('11101103'+challanId);
    $('#fine_category').text(fc_category_name);
    // $('#fine_type').text(fcd_violation_type);
    $('#amount').text('Rs:'+amount+'/-');
    $('#dueDate').text(dueDate);

    $('#detail-Modal').modal('show');
}
function populateAdditionalTable(data, enrollment) {
    // console.log(enrollment);
    // console.log(data[0]);
    // console.log(data[0].regno);
    // console.log(data.length);//data == '' &&
    if(enrollment != ''){
        if(data.length != 0){
        // alert('there is data');
       $('.custom-table').removeClass('d-none')
        $('.td-enrollemt').text(data[0].regno ? data[0].regno : 'NA');
        $('.td-studentId').text(data[0].pvnoslipnostudentid ? data[0].pvnoslipnostudentid : 'NA');
        $('.td-name').text(data[0].studentname ? data[0].studentname : 'NA');
        $('.td-guardian').text(data[0].parentguardianname ? data[0].parentguardianname : 'NA');
        $('.td-cnic').text(data[0].parentguardiancnic ? data[0].parentguardiancnic : 'NA');
        $('.td-school').text(data[0].school ? data[0].school : 'NA');
        $('.td-dept').text(data[0].department ? data[0].department : 'NA');
        $('.td-prog').text(data[0].program ? data[0].program : 'NA');
        $('.td-studenttype').text(data[0].studenttypenew ? data[0].studenttypenew : 'NA');
        $('.td-feetype').text(data[0].feetypesummary ? data[0].feetypesummary : 'NA');
        $('.td-sem').text(data[0].semester ? data[0].semester : 'NA');
        $('.td-status').text(data[0].sai_student_status ? data[0].sai_student_status : 'NA');

        }else{
            $('.custom-table').addClass('d-none');
            // alert('there is no data');
        }
    }else{
        $('.custom-table').addClass('d-none');
        // alert('no enrollment no selected');
    }

    // var additionalTable = $('#additional-table tbody');
    // additionalTable.empty(); // Clear existing data

    // data.forEach(function(row) {
    //     var newRow = '<tr>';
    //     newRow += '<td>' + row.session_id + '</td>';
    //     newRow += '<td>' + row.buyercodechallanno + '</td>';
    //     // Add more cells as needed based on the columns in your additional table
    //     newRow += '</tr>';
    //     additionalTable.append(newRow);
    // });
}
function previewRow2(challan_id,beforeF2024Checked) {
    // var url = 'grader.financial.ledger_challan_preview.php?challan_no='+challan_id;
    // window.open(url);
// alert(beforeF2024Checked);
// return false;
    var url = '/ledger_challan_preview?challanNo='+challan_id+'&stp='+beforeF2024Checked;
    window.open(url, '_blank');
}
function previewRow(challan_id,beforeF2024Checked){

    let staffUrl = "{{ config('app.staff_url') }}";
    var link = `${staffUrl}/chalanfin.php?challan_id=${challan_id}&stp=${beforeF2024Checked}`;


    setTimeout(function(){
            $('.hidegif').hide();
            window.open(
                link,
                '_blank' // <- This is what makes it open in a new window.
                );
            // $('#pdfIcon').show();
        }, 1000);
    // var user_id = $("#staff_for_filter").val()
    //url = '/assets/cms/reports/online_payment_list.php?studentID='+stdId+'&challanNO='+challanNo+'&fromDate='+fromDate+'&toDate='+toDate,
    // url = '/assets/cms/reports/online_payment_list.php',
    // window.open(url, '_blank');

}
function showModal(mainId){
    $('#main_id').val(mainId);
    $('#status-dropdown').val('');
    $('#status_remarks').val('');

    $('#modal-1').modal('show');
}
function refreshFields(){
    $('#CategoryOption').val('');
    $('#surplus_amount').val('');
    $('#remarks').val('');
}

</script>
@stop
