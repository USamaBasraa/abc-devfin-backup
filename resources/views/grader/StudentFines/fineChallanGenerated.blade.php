@extends('layouts.app')
<style>
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
</style>
@section('content')
<div class="container-fluid" style="">
    <div class="card p-4">
    <div class="card-header">
    <h2 class="mb-4 text-left">Students Fine Details</h2>
  </div>
  <div class="card-body">
    <div class="row">
        <div class="form-group col-md-3">
            <label for="session">Select Session:</label>
            <select name="session" id="session" class="form-control">
                <option value="" selected>--- Select Session ---</option>
                @foreach ($sessions as $session)
                    <option value="{{ $session->sc_session_id }}">{{ $session->sess_name }}</option>
                @endforeach
            </select>
            <h6 data-error-for="sessionOption" class="validation-error pt-4"></h6>
        </div>
        <div class="form-group col-md-3">
            <label for="fineCategoryOption">Select Category:</label>
            <select name="fineCategoryOption" id="fineCategoryOption" onchange="getOffenceDetails(this.value)" class="form-control">
                <option value="" selected>--- Select Category ---</option>
                @foreach ($fine_categories as $category)
                    <option value="{{ $category->id }}">{{ $category->fc_category_name }}</option>
                @endforeach
            </select>
            <h6 data-error-for="fineCategoryOption" class="validation-error pt-4"></h6>
        </div>
        <div class="form-group col-md-3">
            <label for="violationType">Select Offence Type:</label>
            <select name="violationType" id="violationType" class="form-control"></select>
            <h6 data-error-for="violationType" class="validation-error pt-4"></h6>
        </div>
        <div class="form-group col-md-3">
            <div class="form-group" id="user_dropdown">
                <label for="issuedBy">Issued By:</label>
                <select style="width: 100%;" class="form-control  select2-show-search" id="issuedBy" name="issuedBy">
                    <option value="">-- Select User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->employee_id }}">{{ $user->emp_ad_username }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-4 my-2">
            <label for="fromDate">From Date:</label>
            <input type="date" name="fromDate" id="fromDate" class="form-control" placeholder="From Date">
            <h6 data-error-for="fromDate" class="validation-error pt-4"></h6>
        </div>
        <div class="form-group col-md-4 my-2">
            <label for="toDate">To Date:</label>
            <input type="date" name="toDate" id="toDate" class="form-control" placeholder="To Date">
            <h6 data-error-for="toDate" class="validation-error pt-4"></h6>
        </div>
        <div class="form-group col-md-4 d-flex justify-content-center align-items-center my-2">
            <button class="btn btn-success" onclick="exportToExcel()">
                <i class="fa fa-file-excel-o"></i> Download Summary
            </button>
        </div>
    </div>
    <table id="challanTable" class="table table-striped table-bordered table-hover">
    <thead class="thead-light">
                <tr>
                    <th>Ticket No</th>
                    <th>Enrollment No</th>
                    <th>Session Name</th>
                    <th>Category Name</th>
                    <th>Violation Type</th>
                    <th>Amount</th>
                    <th>Invoice Amount</th>
                    <th>Challan Type</th>
                    <th>Status</th> 
                    <th>Issue Date</th> 
                    <th>Issued By</th>
                    <th>Student Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($challansRec as $challan)
                <tr>
                    <td>{{ $challan->challan_id }}</td>
                    <td>{{ $challan->sc_enrollment_no }}</td>
                    <td>{{ $challan->sess_name }}</td>
                    <td>{{ $challan->fc_category_name }}</td>
                    <td>{{ $challan->fcd_violation_type }}</td>
                    <td>{{ $challan->sc_amount }}</td>
                    <td>{{ $challan->invoiceamount }}</td>
                    <td>{{ $challan->sc_challan_type }}</td>
                    <td>
                        <!-- Badge for sc_challan_status with different styles -->
                        @if($challan->sc_challan_status === 'PAID')
                            <span class="badge bg-success text-white">PAID</span>
                        @elseif($challan->sc_challan_status === 'UNPAID')
                            <span class="badge bg-danger text-white">UNPAID</span>
                        @elseif($challan->sc_challan_status === 'PENDING')
                            <span class="badge bg-warning text-white">PENDING</span>
                        @elseif($challan->sc_challan_status === 'OVERDUE')
                            <span class="badge bg-danger">OVERDUE</span>
                        @elseif($challan->sc_challan_status === 'CANCELLED')
                            <span class="badge bg-danger text-white">Cancelled</span>
                        @endif
                    </td>
                    <td>{{ $challan->sc_issu_date }}</td>
                    <td>{{ $challan->fined_by }}</td>
                    <td>{{ $challan->student_status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        </div>
    </div>
</div>
@stop

@section('javascript')
<script>
    $(document).ready(function() {
        var table = $('#challanTable').DataTable({
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o mr-1"></i>Excel',
                    title: 'Generated Challans Excel',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6,7,8,9,10,11] // Adjust based on actual number of columns
                    },
                    className: 'ml-2 btn btn-primary'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o mr-1"></i>PDF',
                    title: 'Generated Challans PDF',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    },
                    className: 'ml-2 btn btn-primary'
                }
            ],
            responsive: true,
            ordering: true, // Enable sorting
            scrollX: true, // Enable horizontal scroll
            pageLength: 25, // Default rows per page
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, 'All']] // Custom menu
            // "language": {
            //     "lengthMenu": "Display _MENU_ records per page",
            //     "zeroRecords": "Nothing found - sorry",
            //     "info": "Showing page _PAGE_ of _PAGES_",
            //     "infoEmpty": "No records available",
            //     "infoFiltered": "(filtered from _MAX_ total records)"
            // }
        });

        // Filter table based on selected session
        $('#session').on('change', function() {
            var selectedSessionText = $(this).find('option:selected').text();

            if (selectedSessionText === '--- Select Session ---') {
                // If no session selected, show all data
                table.column(2).search('').draw();
            } else {
                // Filter by selected session (column 2 is Session Name)
                table.column(2).search('^' + selectedSessionText + '$', true, false).draw();
            }
        });

        // Filter table based on selected category
        $('#fineCategoryOption').on('change', function() {
            var selectedCategoryText = $(this).find('option:selected').text();

            if (selectedCategoryText === '--- Select Category ---') {
                // If no category selected, show all data
                table.column(3).search('').draw();
            } else {
                // Filter by selected category (column 3 is Category Name)
                table.column(3).search('^' + selectedCategoryText + '$', true, false).draw();
            }
        });

        // Filter table based on selected violation type
        $('#violationType').on('change', function() {
            var selectedViolationText = $(this).find('option:selected').text();

            if (selectedViolationText === '--- Select Violation Type ---' || selectedViolationText === '') {
                // If no violation type selected, show all data
                table.column(4).search('').draw();
            } else {
                // Filter by selected violation type (column 4 is Violation Type)
                table.column(4).search('^' + selectedViolationText + '$', true, false).draw();
            }
        });

        // Filter table based on selected issued by user
        $('#issuedBy').on('change', function() {
            var selectedIssuedByText = $(this).find('option:selected').text();

            if (selectedIssuedByText === '-- Select User --' || selectedIssuedByText === '') {
                // If no user selected, show all data
                table.column(10).search('').draw();
            } else {
                // Filter by selected issued by user (column 10 is Issued By)
                table.column(10).search('^' + selectedIssuedByText + '$', true, false).draw();
            }
        });
    });
    function getOffenceDetails(selectedValue) {
        var violationType = document.getElementById("violationType");
        violationType.value='';


        // Adjust the URL by appending the selectedValue to it
        let url = `/check-violation-types?fineCategoryOption=${selectedValue}`;

        fetch(url)
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    violationType.innerHTML = '<option value="" selected disabled>--- Select Violation Type ---</option>';
                     response.data.forEach(type => {
                    let option = document.createElement("option");
                    option.value = type.id;
                    option.textContent = type.fcd_violation_type;
                    violationType.appendChild(option);
                });
                }

            });
    }
    function exportToExcel(){
        const sessionElement = document.getElementById('session');
        const session = sessionElement.value; // Get the selected value
        const sessionText = sessionElement.options[sessionElement.selectedIndex].text; // Get the selected text
        const category = document.getElementById('fineCategoryOption').value;
        const offence = document.getElementById('violationType').value;
        const issuedBy = document.getElementById('issuedBy').value;
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;
        let url = "{{ config('app.app_url') }}";
        let link = `${url}/assets/cms/reports/fine_summary_excel.php?session=${encodeURIComponent(session)}&category=${encodeURIComponent(category)}&offence=${encodeURIComponent(offence)}&issuedBy=${encodeURIComponent(issuedBy)}&fromDate=${encodeURIComponent(fromDate)}&toDate=${encodeURIComponent(toDate)}&sessionText=${encodeURIComponent(sessionText)}`;
        window.open(link, '_blank');
        event.preventDefault();
    }
</script>
@stop
