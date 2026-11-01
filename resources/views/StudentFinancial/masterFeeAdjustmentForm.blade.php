@extends('layouts.app')
@section('title')
    BNU CMS
@stop
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- DataTables CSS -->
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css"> --}}

<style>

    input[type=checkbox], input[type=radio] {
        box-sizing: border-box;
        margin-right: 15px;
    }
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
    .edit-btn, .delete-btn{
            width:76px;
        }
        .dt-center {
    text-align: center;
    vertical-align: middle; /* Optional, for vertical centering */
}
</style>
</head>
@section('content')
{{-- ---------------------- --}}
<div id="success-flag" class="success-flag"></div>
<div class="card">
<div class="card-header" style="width:100%;">
<form id="feeAdjForm" method="POST" action="">
    @csrf
        <!-- <div class="row"><h3>Master Fee Adjustment Form:</h3></div> -->
        <div class="row"><h3>Adjustment for Private Scholarship:</h3></div>
        <div class="row">
            <input type="text" name="mainId" id="main-Id" value='' class="form-control" hidden>
            <div class="form-group col-sm-6">
                <label for="title">Title</label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    class="form-control">
            </div>
            <div class="form-group col-sm-6">
                <label for="school_sel">Type</label>
                <select name="fee_category" id="fee_category_type" class="form-control">
                    <option value="">Select Type</option>
                    @foreach ($feeFineCategory as $row)
                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-sm-12" style="width:100%;">
                <label for="descp">Description</label>
                <input
                    type="text"
                    name="description"
                    id="descp"
                    class="form-control">
            </div>

        </div>
        <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
            <button type="button" style="width: 150px;" id="saveButton" class="btn btn-primary">Save</button>
        </div>
        <div id="processing_message" class="alert" style="display: none;background-color:#4454c36e;">Processing, please wait...</div>
        <div id="error_message" class="alert alert-danger" style="display: none;"></div>
        <div id="success_message" class="alert alert-success" style="display: none;"></div>
</form>
</div>
<div class="row" style="margin-top:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="data-table" class="table display" style="width:100%">
                        <thead>
                          <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Description</th>
                            <th scope="col">Fee Category</th>
                            <th scope="col">Action</th>
                            <th><input type="checkbox" class="checkbox select-data" id="select-all" value="" /></th>
                          </tr>
                        </thead>
                      </table>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-2">
                <div class="form-group col-sm-3 d-flex justify-content-end mt-2">
                  <button type="button" id="bulk-Del" class="btn btn-danger">Bulk Delete</button>
                </div>
            </div>
        </div>
</div>




{{-- --------------------------- --}}




<div class="table-responsive">
    <div id="faculty_evaluation_report">

    </div>


  </div>


{{-- --------------------------- --}}

<div class="card" style="margin-top:10px;">

</div>
<div style="clear:both;"></div>
@stop
@section('javascript')
<script>
function escapeHtmlAttr(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

$(document).on('click', '.edit-btn', function () {
    var $btn = $(this);
    editRow($btn.data('id'), $btn.data('title'), $btn.data('desc'), $btn.data('feeCategory'));
});

    document.getElementById('saveButton').addEventListener('click', function() {
        var form = document.getElementById("feeAdjForm");
        var formData = new FormData(form);
        formData.append('title', $('#title').val());
        formData.append('description', $('#descp').val());
        formData.append('fee_category', $('#fee_category_type').val());
        console.log(formData);
        $("#processing_message").show();
        fetch('addMasterFeeAdjustment', {
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
                LoadAdjustmentsCategories();
            } else {
                console.log(data);
            var message = "";
            if ('msg' in data) {
                message = data.msg;
            } else if ('errors' in data) {
                var errors = data.errors;
                $.each(errors, function (i, obj) {
                    message += "<p>" + obj[0] + "</p>";
                });
            }
            $("#processing_message").hide();
            $('#success_message').text('').hide();
            $('#error_message').html(message);
            $('#error_message').fadeIn('slow');
            $('#error_message').fadeOut(5000);
            }
        })
        .catch(error => {
            $("#processing_message").hide();
            console.error('There was an error!', error);
        });
    });


    $(document).ready(function(){
        LoadAdjustmentsCategories();
        $(document).on('change', '#select-all', function() {
        if ($(this).prop("checked") == true) {
            $('.select-data').prop('checked', true);
            console.log($('.select-data').val());
            // $('#bulk-Del').prop('disabled', false);

        } else {
            $('.select-data').prop('checked', false);
            console.log($('.select-data').val());
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
    });

</script>

{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin/tableExport.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.2/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

<script src="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap4.min.js"></script> --}}

    <!-- Bootstrap CSS -->
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css"> --}}

<!-- Bootstrap Select CSS -->
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css"> --}}

<!-- Bootstrap Select JS -->
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"></script> --}}

<!-- Bootstrap Select LiveSearch JS -->
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/i18n/defaults-*.min.js"></script> --}}

<script>
function LoadAdjustmentsCategories(){
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
            ajax:{
                dataType: 'json',
                method: 'post',
                url:  "/api/master-feeadjustment-form/getMasterFeeAdjustmentList",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token
                }
            },
            columns: [
                {data: "ffac_title", name: "ffac_title",  className: "dt-center"},
                {
                    data: "ffac_description",
                    name: "ffac_description",
                    className: "dt-center",
                    render: function(data, type, row) {
                        return data ? data : 'NA';
                    }
                },
                // Add other column definitions here
                {data: "name", name: "name", className: "dt-center"},

            ],
            columnDefs: [
                {
                    render: function (data, type, row) {
                        console.log(row);
                        var button = '<div class="d-flex align-items-center justify-content-center">' +
                                        '<button type="button" class="btn btn-primary btn-sm edit-btn" id="'+row["ffac_id"]+'" data-id="'+escapeHtmlAttr(row["ffac_id"])+'" data-title="'+escapeHtmlAttr(row["ffac_title"])+'" data-desc="'+escapeHtmlAttr(row["ffac_description"])+'" data-fee-category="'+escapeHtmlAttr(row["feecategoryid"])+'">Edit</button>' +
                                        '<span class="mx-2"></span>' +
                                        '<button class="btn btn-danger btn-sm delete-btn" onclick="deleteRows(['+row["ffac_id"]+'])">Delete</button>' +
                                    '</div>';
                        return button;

                    },
                    // className: 'dt-body-center',
                    // className: 'dt-head-center',
                    targets: 3,
                },
                {
                    render: function (data, type, row) {
                            var   selectbtn = '<div><input name="fee_ids[]" class="select-data" idData='+row["ffac_id"]+' value='+row["ffac_id"]+' type="checkbox" ></div>';

                        return selectbtn;

                    },
                    // className: 'dt-body-center',
                    // className: 'dt-head-center',
                    targets: 4,
                    searchable: false,
                    orderable: false
                },

                // {
                //     "width": "100px", "targets": 1
                // }
            ],
            headerCallback: function(thead, data, start, end, display) {
                $(thead).find('th').css('text-align', 'center');
            },
            autoWidth: false, // Ensures DataTable does not set its own width
        });
    }
function editRow(mainId,title,desc,feeCategoryId){

    $('#main-Id').val(mainId);
    $('#title').val(title);
    $('#fee_category_type').val(feeCategoryId);
    if (!desc || desc === 'null') {
        $('#descp').val('');
    } else {
        $('#descp').val(desc);
    }
    $('html, body').animate({scrollTop: 0}, 'slow');
}
function deleteRows(mainId){
    var confirmation = window.confirm("Are you sure you want to delete?");
    if (confirmation) {
        $.ajax({
            type: "POST",
            url: "/api/master-feeadjustment-form/delete_masterFeeAdjustment",
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
                LoadAdjustmentsCategories();
            },
            error: function(error) {
                // Handle the error
                console.error("Error:", error);
            }
        });
    } else {

    }

}
function refreshFields(){
    $('#main-Id').val('');
    $('#title').val('');
    $('#descp').val('');
    $('#fee_category_type').val('');
}
</script>

@stop

