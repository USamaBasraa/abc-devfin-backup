
@extends('layouts.app')
@section('title')
    BNU CMS
@stop
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
{{-- <script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
 <!-- DataTables CSS -->
 <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css">
 <!-- DataTables Responsive CSS -->
 <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">

 <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
 <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"> --}}

<style>
.invisible { display: none; }
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
    .error-flag {
        position: fixed;
        top: 170px;
        right: -300px;
        background-color: #f44336; /* Red color for error */
        color: white;
        padding: 15px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1050;
        transition: right 0.5s;
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
    }
    .error-flag.show {
        right: 0px;
    }
    .filter-bubble {
        background-color: #45abf4; /* Background color of the bubble */
        color: white;            /* Text color inside the bubble */
        padding: 0px 14px;       /* Padding to make it look like an oval */
        border-radius: 15px;
        font-size: 12px;     /* Border radius to make it oval-shaped */
        /* font-weight: bold;       Optional: Make the text bold */
        display: inline-block;   /* Ensures the span behaves like a block for styling */
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
</style>
<?php
// print_r($Scholarship_Type);exit;
// $sch_type = array("Need Base","Merit Base","BSS Emp","BSS Emp Ward","BNU Emp","BNU Emp Ward","Nest","UMISAA","SHEHLA Siagol");
?>
<div id="success-flag" class="success-flag"></div>
<div id="error-flag" class="error-flag"></div>
<div class="card">
  <div class="card-header" style="display:inline">
    <div style="float:left">
        <h3 class="card-title">Offered Course Status Update</h3>
    </div>
  </div>
  <div class="card-body">
    <div class='row'>
        <div class='col-md-6'>
            <form id="studentRecord" method="post" onsubmit="event.preventDefault(); autoSearch(this);">
                @csrf
                <div class="form-group row" style="margin-bottom:0px;">
                    <div class="col-lg-6" style="text-align: right">
                        <label class="col-form-label" >Session</label>
                    </div>
                    <div class="form-group col-lg-6">
                        <select  class="custom-select form-control select2-show-search" style="width:412 !important"  name="academic_session" id="academic_session" data-placeholder="Select Session">
                        <option value=""></option>
                            @foreach ($academic_session as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>                    
                <div class="form-group row" style="margin-bottom:0px;">
                    <div class="col-lg-6" style="text-align: right">
                        <label class="col-form-label" >School</label>
                    </div>
                    <div class="form-group col-lg-6">
                        <select  class="custom-select form-control select2-show-search" onchange="getDepartments();" style="width:412 !important" name="schools" id="schools" data-placeholder="Select School">
                            <option value=""></option>
                            @foreach ($schools as $row)                                                            
                                <option value="{{$row->school_id}}">{{ $row->school_name }}</option>                               
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            Please select a Type.
                        </div>
                    </div>
                </div>   
                <div class="form-group row" style="margin-bottom:0px;">
                    <div class="col-lg-6" style="text-align: right">
                        <label class="col-form-label" >Department</label>
                    </div>
                     <div class="form-group col-lg-6">                      
                        <select name="department" id="department" class="custom-select form-control select2-show-search" style="width:412 !important" placeholder="Select Department">
                        </select>
                    </div>  
                </div>   
                <div class="form-group row" style="margin-bottom:0px;">
                    <div class="col-lg-6" style="text-align: right">
                        <label class="col-form-label">No of Enrolled Students</label>
                    </div>
                    <div class="form-group col-lg-6">                      
                        <select name="no_of_enrolled" id="no_of_enrolled" class="custom-select form-control select2-show-search" style="width:412 !important" placeholder="Select Department">
                            <option value="">Select No of Enrolled Students</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                        </select>
                    </div>  
                </div>          
                 
              

                <style>
                    #hostel_scholarship:hover {
                        background-color: #e8f0fe;
                        border-color: #6c757d;
                        transform: scale(1.1);
                    }
                    #hostel_scholarship:checked {
                        background-color: #e8f0fe;
                        border-color: #6c757d;
                        transform: scale(1.1);
                    }
                </style>


            </form>


        </div>
        <div class='col-md-6'>
            <table class="table" style="width: 100%;">
                <tbody>
                    <tr>
                        <td style='font-size: 17px; font-weight: 600;' id="student_label"></td>
                        <td style='font-size: 15px;' id="student_name"></td>
                    </tr>
                    <tr>
                        <td style='font-size: 17px; font-weight: 600;' id="school_label"></td>
                        <td style='font-size: 15px;' id="school_name"></td>
                    </tr>
                    <tr>
                        <td style='font-size: 17px; font-weight: 600;' id="dept_label"></td>
                        <td style='font-size: 15px;' id="dept_name"></td>
                    </tr>
                    <tr>
                        <td style='font-size: 17px; font-weight: 600;' id="prog_label"></td>
                        <td style='font-size: 15px;' id="prog_name"></td>
                    </tr>
                    <tr>
                        <td style='font-size: 17px; font-weight: 600;' id="status_label"></td>
                        <td style='font-size: 15px;' id="st_status"></td>
                    </tr>
                    <tr>
                        <td style='font-size: 17px; font-weight: 600;' id="cgpa_label"></td>
                        <td style='font-size: 15px;' id="st_cgpa"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="form-group" style="margin-top:20px;">
        <div style="text-align:center;">
            <button type="button" class="btn btn-primary" onclick="submitForm()"> <i class="fa fa-paper-plane"></i>   Filter</button>
        </div>
        <span id="successMsg"></span>
    </div>
    <div id="processing_message" class="alert" style="display: none;background-color:#4454c36e;">Processing, please wait...</div>
    <div id="error_message" class="alert alert-danger" style="display: none;"></div>
    <div id="success_message" class="alert alert-success" style="display: none;"></div>
    <hr style="border: 1px solid #ccc; margin: 20px 0;">
    <h4 class="text-left">Action</h4>
    <div class='row'>
       
       
        <div class="form-group row" style="margin-bottom:0px;">
            <div class="col-lg-2" style="text-align: right">
                <label class="col-form-label">Remarks</label>
            </div>
            <div class="form-group col-lg-10">
                <input type="text" class="form-control" name="sc_remarks" id="sc_remarks">                  
            </div>
        </div>
        <div class="text-center col-lg-4">
            <button type="button" class="btn btn-primary" onclick="DisableCourse()">DisApprove</button>
           
        </div>
        
    </div>
    <div class="row" style="margin-top:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="data-table" class="table display" style="width:100%">
                        <thead>
                          <tr>                         
                            
                            <th scope="col">Sr No.</th>
                            <th scope="col">Course Offered</th>
                            <th scope="col">No of Students</th>     
                            <th scope="col">Course Type</th>                    
                            <th><input type="checkbox" class="checkbox approve-select-data" id="approve-select-all" value=""/></th>
                                                  
                          </tr>
                        </thead>
                      </table>
                </div>
            </div>
        </div>
    </div>

          <?php $count = 1; ?>


  </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmDisapproveModal" tabindex="-1" role="dialog" aria-labelledby="confirmDisapproveLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title" id="confirmDisapproveLabel">Confirm Disapproval</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to disapprove this page?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button id="confirmDisapproveBtn" type="button" class="btn btn-danger">Yes, Disapprove</button>
      </div>
    </div>
  </div>
</div>

@stop

@section('javascript')


<script src="<?=asset('js/bootstrap-autocomplete.js')?>"></script>
<script>
    var totalAwardedAmount_hostel = 0;
    var totalAwardedPercent_hostel = 0;
    var permitted_amount = 0;
    $(document).ready(function(){
        // DisableCourse();
        // $('#hostel_scholarship').change(function () {
        //     session_enrollment_change();

        // });
        // LoadOfferedCourses('', '', '', '');
    });
</script>
<script type="text/javascript">
    
    // $(document).ready(function() {
    //     session_enrollment_change(); // Trigger on page load in case a value is preselected
    // });
$(document).on('change', '#approve-select-all', function() {
    if ($(this).prop("checked") == true) {
        $('.approve-select-data').prop('checked', true);
        console.log($('.approve-select-data').val());

    } else {
        $('.approve-select-data').prop('checked', false);
        console.log($('.approve-select-data').val());
    }
});
 function getRequest(endPoint, successBlock, failBlock) {
            $.ajax({
                url: "{{ url('/') }}" + endPoint,
                type: "GET",
                dataType: "json",
                success: successBlock
            }).fail(failBlock);
        }
function getDepartments() {
    var school_id = $('#schools').val();
    getRequest('/department/get?school_id=' + school_id, function(res) {
        if (res.status) {
            var html = '<option value="">All Dept</option>';
            $.each(res.data, function(key, value) {
                html += '<option value="' + value.dept_id + '" >' + value.dept_name + '</option>';
            });
            $('select[name="department"]').html(html);
            getTeacher();
        }
    }, function(res) {
        alert('Some Error found!');
    });
}


function submitForm() {

          
    var academic_session = $('#academic_session').val();
    var schools = $('#schools').val();  
    var department = $('#department').val();  
    var no_of_enrolled = $('#no_of_enrolled').val(); 
   
   
   
    LoadOfferedCourses(academic_session, schools,department,no_of_enrolled);
}


function LoadOfferedCourses(academic_session, schools,department,no_of_enrolled){
        // let filterParams = studentType;
        // console.log(filterParams);
        // $('#studentType').val();
        $('#data-table').DataTable({
            scrollX: true, // Enable horizontal scrolling
            responsive: true,
            fixedHeader: false,
            scrollCollapse: false,
            processing: true,
            serverSide: false,
            select: false,
            searching: true,
            destroy: true,
            lengthChange: true,
            paging: true,
            pageLength: 10,
         
            lengthMenu: [10, 25, 50, 100, 500, 1000, 5000, 10000],
            order: [1, 'desc'],
            ajax:{
                dataType: 'json',
                method: 'get',
                url:  "/api/get-courses-offered",
                data: function(d) {
                    d.academic_session = academic_session;
                    d.schools = schools;
                    d.department = department;     
                    d.no_of_enrolled = no_of_enrolled;                 
                },
            },
            columns: [       
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1; // Sr. No. (starts from 1)
                    },
                
                },            
                {data: "course_cd_name"},
                {data: "no_of_enrolled"},   
                {
                    data: "directed_course",
                    render: function(data, type, row) {
                        if (data === "Single") {
                            return "Single Directed";
                        } else if (data === "Group") {
                            return "Group Directed";
                        } else {
                            return "Not Defined by School";
                        }
                }
                },              
                {
                    render: function (data, type, row) {
                        // If no_of_enrolled is 10 or less, return empty string (no checkbox)
                        if (parseInt(row.no_of_enrolled) >= 10) {
                            return '';
                        }
                        // Otherwise, render the checkbox
                        var checked = row.status === 'E' ? 'checked' : '';
                        var selectbtn = `<div>
                                            <input name="approved_honorList[]" class="approve-select-data" value="${row.co_id}" type="checkbox" ${checked} data-id="${row.co_id}" data-co_id="${row.co_id}">
                                        </div>`;
                        return selectbtn;
                    }
                },
            ],
            columnDefs: [
              
            ],
            autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Courses Offered Status',
                    exportOptions: {
                            columns: [0, 1, 2] // Include all columns except the delete button (index 9)
                        }
                  
                },
                // {
                //     text: 'Export All Data (Excel)', // Custom button text
                //     action: function(e, dt, button, config) {
                //         // Call your custom function
                //         excelExportFunction(dt, config);
                //     }
                // },
                {
                    extend: 'pdfHtml5',
                    title: 'Courses Offered Status',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                            columns: [0, 1, 2] // Include all columns except the delete button (index 9)
                        }
                 

                },
                // {
                //     text: 'Export All Data (PDF)', // Custom button text
                //     action: function(e, dt, button, config) {
                //         // Call your custom function
                //         pdfExportFunction(dt, config);
                //     }
                // },
                {
                    extend: 'print',
                    title: 'Courses Offered Status',
                    exportOptions: {
                            columns: [0, 1, 2] // Include all columns except the delete button (index 9)
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





function downloadExcel() {
    // Select the table element
    const table = document.querySelector('.table');

    // Clone the table to avoid modifying the original table
    const clonedTable = table.cloneNode(true);

    // Remove the rightmost column (ACTION)
    const rows = clonedTable.querySelectorAll('tr');
    rows.forEach(row => {
        const lastCellIndex = row.cells.length - 1; // Index of the last cell
        row.deleteCell(lastCellIndex); // Remove the last cell (rightmost column)
    });

    // Convert the modified table to an Excel workbook
    const wb = XLSX.utils.table_to_book(clonedTable);

    // Download the Excel file
    XLSX.writeFile(wb, 'student-record.xlsx');
}
function verifyCgpa(element){
 

}


function cancelChallanBulk(){

    const selectedIds = [];
    const selectedRecords = [];

    $('.approve-select-data:checked').each(function () {
        selectedRecords.push({ 
            id: $(this).data('id'),
            co_id: $(this).data('co_id'),
         
        });
    });

    const sc_remarks = $('#sc_remarks').val();


    if (!sc_remarks) {
        alert('Please enter Remarks.');
        return;
    }

   

        // Send AJAX request to backend
        $.ajax({
            url: '/student-record/student-cancel-challan-bulk',
            method: 'POST',
            data: {
                records: selectedRecords,
                sc_remarks: sc_remarks,
                // due_date: due_date,
                // valid_date: valid_date,
                // sc_remarks: sc_remarks,
                _token: $('meta[name="csrf-token"]').attr('content') // if using blade
            },
            success: function (response) {
                alert('Records updated successfully!');
                $('#data-table').DataTable().ajax.reload(); // Reload table
            },
            error: function (xhr) {
                alert('Error updating records');
            }
        });
    

}
function DisableCourse() {
    // Get selected IDs
    const selectedIds = [];
    $('.approve-select-data:checked').each(function () {
        selectedIds.push($(this).val());
    });

    // Get form values
   
    const sc_remarks = $('#sc_remarks').val();

    if (selectedIds.length === 0) {
        alert('Please select at least one record to update.');
        return;
    }

    if (!sc_remarks) {
        alert('Please enter Remarks.');
        return;
    }

      // Show confirmation modal
    $('#confirmDisapproveModal').modal('show');

    // When user confirms
    $('#confirmDisapproveBtn').off('click').on('click', function () {
        // Close modal
        $('#confirmDisapproveModal').modal('hide');


        // Send AJAX request to backend
        $.ajax({
            url: '/student-record/update-course_status',
            method: 'POST',
            data: {
                ids: selectedIds,            
                sc_remarks: sc_remarks,
                _token: $('meta[name="csrf-token"]').attr('content') // if using blade
            },
            success: function (response) {
                alert('Records updated successfully!');
                $('#data-table').DataTable().ajax.reload(); // Reload table
            },
            error: function (xhr) {
                alert('Error updating records');
            }
        });
    });
}

</script>
@stop

