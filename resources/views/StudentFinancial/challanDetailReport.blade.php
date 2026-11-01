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
        <style>
/* Prominent, clean profile heading: applies only when .d-none is NOT present */
.custom-table:not(.d-none).custom-table-heading {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 18px;
    text-align: center;
    background: linear-gradient(90deg, #144272 0%, #205295 100%);
    border-radius: 14px;
    box-shadow: 0 2px 14px rgba(32, 82, 149, 0.09);
    padding: 16px 0;
}

.custom-table:not(.d-none).custom-table-heading > div:nth-child(2) {
    font-size: 1.6rem;
    font-weight: 800;
    color: #45aaf2;
    letter-spacing: 1px;
    text-shadow: 0 2px 12px rgba(0,0,0,0.13);
}

#studentImage {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Student info table styling: applies only when .d-none is NOT present */
.custom-table:not(.d-none):not(.custom-table-heading) {
    width: 65%;
    margin: 0 auto 16px auto;
    border-collapse: separate;
    border-spacing: 0;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 14px rgba(44, 62, 80, 0.08);
    overflow: hidden;
    font-size: 1.06rem;
    letter-spacing: .01em;
}

.custom-table:not(.d-none):not(.custom-table-heading) tr {
    border-bottom: 1px solid #ecf0f1;
}

.custom-table:not(.d-none):not(.custom-table-heading) tr:last-child {
    border-bottom: none;
}

.custom-td {
    padding: 13px 15px;
    text-align: left;
    vertical-align: middle;
    font-weight: 500;
    color: #2d3e50;
    background: #f7faff;
    border-right: 1px solid #f0f4f8;
}

.custom-table:not(.d-none):not(.custom-table-heading) td:nth-child(odd) {
    background: #e8f0fe;
    color: #134074;
    font-weight: 600;
    border-right: 2px solid #fff;
    width: 23%;
}

.custom-table:not(.d-none):not(.custom-table-heading) td:nth-child(even) {
    background: #f7faff;
    color: #1a2930;
    width: 27%;
}

.custom-table:not(.d-none):not(.custom-table-heading) td b {
    font-size: 1.07em;
    font-weight: 700;
    letter-spacing: 0.5px;
}

@media (max-width: 900px) {
    .custom-table:not(.d-none):not(.custom-table-heading) {
        width: 99%;
        font-size: 0.98rem;
    }
    .custom-table:not(.d-none).custom-table-heading {
        font-size: 1.1rem;
        padding: 11px 0;
    }
}
</style>


</style>
</head>

@section('content')
<div id="success-flag" class="success-flag"></div>
<div class="container-fluid">
    <div class="card p-4 pt-4">
        <div class="row" style="margin-left:11px;"><h3>Challan Detail Report:</h3></div>
        <form id="searchForm">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <!-- Student ID / Enrollment No. -->
                        <div class="form-group row align-items-center">
                            <div class="col-sm-6">
                                <label for="studentEnrollment" class="col-form-label">Student ID/Enrollment No.</label>
                                <input type="text" class="form-control" name="studentEnrollment" id="studentEnrollment" onkeypress="handleEnterPress(event)">
                                <small class="searchChallan-disclaimer"></small>
                                <div id="error_message" class="alert alert-danger" style="display: none;"></div>
                            </div>
                        {{-- </div> --}}

                        <!-- Academic Session -->
                        <div class="col-sm-6">
                                <div class="form-group align-items-center" style="margin-bottom: 0;">
                                <label for="session_sel" class=" col-form-label">Academic Session:</label>
                                <select name="session" id="session_sel" class="form-control">
                                    <option value="">Select Session</option>
                                    @foreach ($sessions as $key => $value)
                                        <option value="{{ $value->sess_id }}">{{ $value->sess_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        </div>
                       <!-- From and To Challan No. in One Row -->
                        <div class="form-group row align-items-center">
                            <!-- From Challan No. -->
                            <div class="col-sm-6">
                                <label for="fromChallanNo" class="col-form-label">From Challan No.</label>
                                <input type="number" class="form-control" name="fromChallanNo" id="fromChallanNo">
                            </div>
                            <!-- To Challan No. -->
                            <div class="col-sm-6">
                                <label for="toChallanNo" class="col-form-label">To Challan No.</label>
                                <input type="number" class="form-control" name="toChallanNo" id="toChallanNo">
                            </div>
                        </div>

                        {{-- from date, to date section start --}}
                        <div class="form-group row align-items-center">

    <div class="col-sm-6">
        <label for="from_date" class="col-form-label">
            From Date
        </label>

        <input type="date"
               class="form-control"
               id="from_date"
               name="from_date"
               max="{{ date('Y-m-d') }}">
    </div>
    <div class="col-sm-6">
        <label for="to_date" class="col-form-label">
            To Date
        </label>

        <input type="date"
               class="form-control"
               id="to_date"
               name="to_date"
               max="{{ date('Y-m-d') }}">
    </div>

</div>
                        {{-- from date, to date section end --}}


                         <!-- Challan Type -->
                        <div class="form-group row align-items-center">
                            <div class="col-sm-6">
                                <label for="challan_type" class="col-form-label">Challan Type:</label>
                                <select name="challan_type" id="challan_type" class="form-control">
                                    <option value="">Select Challan Type</option>
                                    @foreach ($challanTypesArray as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                              <div class="col-sm-6 align-items-center">
                                <h3 style="visibility: hidden;">Hello</h3>
                                <label class="col-form-label">Challan Status: </label>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input challanStatus" type="radio" name="challanStatus" id="unpaid" value="unpaid">
                                    <label class="form-check-label" for="unpaid">Unpaid</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input challanStatus" type="radio" name="challanStatus" id="paid" value="paid">
                                    <label class="form-check-label" for="paid">Paid</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input challanStatus" type="radio" name="challanStatus" id="all" checked value="all">
                                    <label class="form-check-label" for="all">All</label>
                                </div>
                                <div class="form-check form-check-inline" style='display:none;'>
                                    <input class="form-check-input beforeF2024" type="checkbox" id="beforeF2024" name="beforeF2024" value="beforeF2024">
                                    <label class="form-check-label" for="beforeF2024">Before F2024</label>
                                </div>
                            </div>
                        </div>
                        <!-- Challan Status -->


                        <!-- Buttons -->
                        <div class="form-group row">
                            <div class="col-12 d-flex justify-content-center flex-wrap">
                                <button onclick='search("enrollment")' type="button" class="btn btn-info ml-2"> Search</button>
                                {{-- <button class="btn btn-primary ml-2" onclick="exportToPDF()">
                                    <i class="fa fa-file-pdf-o"></i> Student Scholarship Detail
                                </button>
                                <button class="btn btn-success ml-2" onclick="exportToExcel()">
                                    <i class="fa fa-file-excel-o"></i> Export All Scholarships Details
                                </button> --}}
                            </div>
                        </div>
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


        <div class='custom-table custom-table-heading d-none' style="justify-content: center; align-items: center; margin-bottom: 10px; text-align: center;">
            <div style="flex: 1;"></div>
            <div style="flex: 1; font-weight: bold;">Student Profile</div>
            <div style="flex: 1;" id='studentImage'></div>
        </div>

        <table class="custom-table d-none" style="width: 100%;">
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
                <td class="custom-td"><b></b></td>
                <td class="custom-td"></td>
            </tr>
        </table>

        <div id="dataDiv"   style="margin-top:20px;">
        </div>
        <div class="row" style="margin-top:10px;">
            <div class="col-lg-12 col-md-12">
                <div id='ledgerData'  class="card d-none">
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <div id="challan-bulk-toolbar" class="d-none mb-2 px-2 d-flex flex-wrap align-items-center">
                            <button type="button" class="btn btn-primary btn-sm" onclick="openBulkPdfFromDataTable()">
                                <i class="fa fa-file-pdf-o"></i> Bulk PDF (selected rows)
                            </button>

                        </div>
                        <table id="data-table" class="table display" style="width:100%">
                            <thead>
                              <tr>
                                <th scope="col" class="text-center" style="width:2.5rem;" title="Select for bulk PDF">
                                    <input type="checkbox" id="challan-select-all" aria-label="Select all rows for bulk PDF">
                                </th>
                                <th scope="col">Session</th>
                                <th scope="col">Pinnacle Challan</th>
                                <th scope="col">CMS challan</th>
                                <th scope="col">Challan Type</th>
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

{{-- <hr> --}}

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
@section('javascript')
<script>
function handleEnterPress(event) {
    //console.log(event);
    if (event.key === 'Enter') {
        event.preventDefault(); // Prevent form submission if it's inside a form
        search("enrollment");
    }
}
$(document).ready(function () {
    $(document).on('change', '#challan-select-all', function () {
        if (!$.fn.DataTable.isDataTable('#data-table')) {
            return;
        }
        var table = $('#data-table').DataTable();
        var on = $(this).prop('checked');
        table.rows({ search: 'applied' }).every(function () {
            var $cb = $(this.node()).find('.challan-row-select');
            if ($cb.length) {
                $cb.prop('checked', on);
            }
        });
    });
    $(document).on('change', '#data-table tbody .challan-row-select', function () {
        syncChallanSelectAllHeader();
    });
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
function normalizeChallanTypeKey(challanType) {
    return String(challanType || '').toLowerCase().trim().replace(/[\s-]+/g, '_');
}

function isPreviewDisabledChallanType(challanType) {
    var key = normalizeChallanTypeKey(challanType);
    return ['fine', 'gym', 'misc', 'misc_fee', 'hostel_fines'].indexOf(key) !== -1;
}

function isShortcourseChallanType(challanType) {
    return normalizeChallanTypeKey(challanType) === 'shortcourse';
}

function search(searchBy) {
    var valid = true;
    // valid = validateSearchBar(searchBy, valid);//this function will make valid variable false if fields are empty
    if(valid){
        let form = document.getElementById('searchForm');

        let beforeF2024 = document.getElementById('beforeF2024');
        let data = new FormData(form);
        let enrollmentNo = $('#studentEnrollment').val();
        if(searchBy == 'enrollment'){
            studentEnrollment = $('#studentEnrollment').val();
            loadLedger(studentEnrollment);
        }
    }
}
function postBulkPdfToServer(formData) {
    return fetch('{{ url('/challan-detail-bulk-pdf') }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(function (r) {
        if (!r.ok) {
            return r.text().then(function (t) { throw new Error(t && t.trim() ? t.trim() : r.statusText); });
        }
        return r.text();
    }).then(function (html) {
        var w = window.open('', '_blank');
        if (w) {
            w.document.open();
            w.document.write(html);
            w.document.close();
        } else {
            alert('Your browser blocked the new tab. Allow pop-ups for this site to open the bulk PDF preview.');
        }
    });
}

function openBulkPdfFromDataTable() {
    if (!$.fn.DataTable.isDataTable('#data-table')) {
        alert('Run Search first to load the challan grid.');
        return;
    }
    var payload = [];
    var $checked = $('#data-table tbody .challan-row-select:checked');
    if (!$checked.length) {
        alert('Check at least one row to download. Fine-type rows cannot be included in bulk PDF.');
        return;
    }
    $checked.each(function () {
        var stp = $(this).data('stp');
        var cid = $(this).data('challan');
        if (cid === undefined || cid === null || String(cid) === '') {
            return;
        }
        payload.push({ challan_id: cid, stp: stp === 'old' ? 'old' : 'new' });
    });
    if (!payload.length) {
        alert('No valid challans in your selection.');
        return;
    }
    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('selection_json', JSON.stringify(payload));

    postBulkPdfToServer(formData).catch(function (e) {
        alert(e.message || 'Could not start bulk PDF preview.');
    });
}

function syncChallanSelectAllHeader() {
    if (!$.fn.DataTable.isDataTable('#data-table')) {
        return;
    }
    var table = $('#data-table').DataTable();
    var $h = $('#challan-select-all');
    if (!$h.length) {
        return;
    }
    var selectable = 0;
    var checked = 0;
    table.rows({ search: 'applied' }).every(function () {
        var $c = $(this.node()).find('.challan-row-select');
        if (!$c.length) {
            return;
        }
        selectable++;
        if ($c.prop('checked')) {
            checked++;
        }
    });
    if (selectable === 0) {
        $h.prop('checked', false).prop('indeterminate', false);
        return;
    }
    if (checked === 0) {
        $h.prop('checked', false).prop('indeterminate', false);
    } else if (checked === selectable) {
        $h.prop('checked', true).prop('indeterminate', false);
    } else {
        $h.prop('checked', false).prop('indeterminate', true);
    }
}

function loadLedger(searchBy){
    $('#ledgerData').removeClass('d-none');
    $('#challan-bulk-toolbar').removeClass('d-none');
    enrollment = searchBy
    session_sel = $('#session_sel').val();
    challan_type = $('#challan_type').val();
    from_challan = $('#fromChallanNo').val();
    to_challan = $('#toChallanNo').val();
    from_date = $('#from_date').val();
to_date = $('#to_date').val();
    challanStatus = $('input.challanStatus:checked').val();
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
        serverSide: true,//donwnload all data
        deferRender: true,
searchDelay: 500,
        select: false,
        searching: true,
        destroy: true,
        lengthChange: true,
        paging: true,
        lengthMenu: [[10, 50, 100, 500, -1], [10, 50, 100, 500, "All"]],
        pageLength: 10,
        order: [],
        drawCallback: function () {
            syncChallanSelectAllHeader();
        },
        ajax:{
        dataType: 'json',
            method: 'post',
            url:  "/get-challan-detail",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                    let beforeF2024Checked = document.getElementById('beforeF2024').checked;
                    beforeF2024Checked = beforeF2024Checked ? 1 : 0;
                    d.enrollment = enrollment;
                    d.session_sel = session_sel;
                    d.challan_type = challan_type;
                    d.from_challan = from_challan;
                    d.to_challan = to_challan;
                    d.from_date = from_date;
d.to_date = to_date;
                    d.challanStatus = challanStatus;
                    d._token = $('meta[name="csrf-token"]').attr('content');
                    // d.beforeF2024Checked = beforeF2024Checked;
                },
            dataSrc: function(json) {
                populateAdditionalTable(json.data, enrollment); // Populate additional table with data
                return json.data; // Return the data for DataTable
            },
        },
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'dt-body-center text-center',
                render: function (data, type, row) {
                    if (type !== 'display') {
                        return '';
                    }
                    if (row.sc_challan_type === 'Fine') {
                        return '<span class="text-muted" title="Not available for bulk PDF">—</span>';
                    }
                    var stp = !(Number(row.session_id)) ? 'old' : 'new';
                    var challanForPdf = stp === 'old' ? row.buyercodechallanno : row.cms_challan_id;
                    if (challanForPdf === null || challanForPdf === undefined || String(challanForPdf).trim() === '') {
                        return '<span class="text-muted">—</span>';
                    }
                    var esc = String(challanForPdf).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                    return '<input type="checkbox" class="challan-row-select" data-stp="' + (stp === 'old' ? 'old' : 'new') + '" data-challan="' + esc + '" />';
                }
            },
            {data: "session_name"},
            {
                data: "buyercodechallanno",
                render: function(data, type, row) {
                    return data != null && String(data).trim() !== '' ? data : 'NA';
                }
            },
            {
                data: "cms_challan_id",
                render: function(data, type, row) {
                    return data != null && String(data).trim() !== '' ? data : 'NA';
                }
            },
            {
                data: "sc_challan_type",
                render: function(data, type, row) {
                    if (!data) {
                        return 'NA';
                    }
                    return String(data)
                        .replace(/[_-]/g, ' ')
                        .replace(/\b\w/g, c => c.toUpperCase());
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
                    return data ? data.split(' ')[0] : 'NA';
                }
            },
            {data: "installments"},
            // {data: "creationdate"},
            {
                data: "challanstatus",
                render: function(data, type, row) {
                //console.log(row);
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
                     //console.log(` ledger=  ${row.buyercodechallanno}`);
                    // buyercodechallanno
                    // let beforeF2024Checked = document.getElementById('beforeF2024').checked;
                    // Convert the boolean value to 'old' or 'new'
                    let beforeF2024Value = !(Number(row.session_id)) ? 'old' : 'new';
                    let challan = !(Number(row.session_id)) ? row.buyercodechallanno : row.cms_challan_id;

                     if (challan === null || challan === undefined || String(challan).trim() === '') {
                            return '<span class="text-muted">No Challan ID</span>';
                        }

                    if (isShortcourseChallanType(row.sc_challan_type)) {
                        return `
                            <button class="btn btn-outline-primary btn-sm preview-btn" onclick="previewRow('${challan}', '${beforeF2024Value}', 'shortcourse')">
                                <i class="fa fa-eye"></i> Preview
                            </button>
                        `;
                    }

                    // if (isPreviewDisabledChallanType(row.sc_challan_type)) {
                    //     if (normalizeChallanTypeKey(row.sc_challan_type) === 'fine') {
                    //         return `
                    //             <button type="button" class="btn btn-primary btn-sm preview-btn" onclick="showDetailModal('${row.cms_challan_id}','${row.totalamountpayable}','${row.duedate}','${row.finetype}')">
                    //                 Show Detail Fine
                    //             </button>
                    //         `;
                    //     }
                    //     return '<span class="text-muted" title="Preview not available for this challan type">—</span>';
                    // }

                     if (isPreviewDisabledChallanType(row.sc_challan_type)) {
                        
        return `
            <button class="btn btn-outline-primary btn-sm preview-btn" onclick="previewRow('${challan}', '${beforeF2024Value}', 'blocked')">
                <i class="fa fa-eye"></i> Preview
            </button>
        `;
    }

    if (normalizeChallanTypeKey(row.sc_challan_type) === 'fee') {
    return `
        <button class="btn btn-outline-primary btn-sm preview-btn" onclick="previewRow('${challan}', '${beforeF2024Value}', 'fee')">
            <i class="fa fa-eye"></i> Preview
        </button>
    `;
}

                    return `
                        <button class="btn btn-outline-primary btn-sm preview-btn" id="${challan}" onclick="previewRow('${challan}', '${beforeF2024Value}')">
                            <i class="fa fa-eye"></i> Preview
                        </button>
                    `;

                },
                // className: 'dt-body-center',
                // className: 'dt-head-center',
                targets: 12,
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
            dom: 'Blfrtip', // l = length menu, B = buttons, f = search, r/t/i/p = table + info + pagination
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Challan Detail Report',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Challan Detail Report',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                    }
                },
                {
                    extend: 'print',
                    title: 'Challan Detail Report',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                    }
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
function previewRowOrignal(challan_id, beforeF2024Checked, previewType){

    let staffUrl = "{{ config('app.staff_url') }}";
    var previewScript = (previewType === 'shortcourse') ? 'scchalanfin.php' : 'chalanfin.php';
    var link = `${staffUrl}/${previewScript}?challan_id=${challan_id}&stp=${beforeF2024Checked}`;

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

function previewRow(challan_id, beforeF2024Checked, previewType){

    let staffUrl = "{{ config('app.staff_url') }}";
    var previewScript = 'chalanfin.php';

    if (previewType === 'shortcourse') {
    previewScript = 'scchalanfin.php';
    } else if (previewType === 'blocked') {
        previewScript = 'scchalanfin.php';
    } else if (previewType === 'fee') {
        previewScript = 'scchalanfin.php';
    }

    var link = `${staffUrl}/${previewScript}?challan_id=${challan_id}&stp=${beforeF2024Checked}`;

    setTimeout(function(){
        $('.hidegif').hide();
        window.open(link, '_blank');
    }, 1000);
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
