@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')

<style>
    .checkboxOptions{
        display: flex;
        flex-direction: row;
        align-items: center;
        margin-left: 10px;
        margin-top: 30px;
    }

    input[type=checkbox], input[type=radio] {
        box-sizing: border-box;
        margin-right: 15px;
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
         /* Change background color of the dropdown toggle button */
         .btn.dropdown-toggle.bs-placeholder.btn-light,
        .btn.dropdown-toggle.btn-light,
        .btn-light {
            background-color: white !important;
            color: black !important;
            border-color: #ced4da !important; /* Matching Bootstrap's default border color */
        }

        /* Change background color of the dropdown menu */
        .dropdown-menu {
            background-color: white !important;
        }

        /* Change text color of the dropdown items */
        .dropdown-item {
            color: black !important;
        }

        /* Optional: Change the hover state */
        .dropdown-item:hover {
            background-color: #f8f9fa !important; /* Light grey background on hover */
        }
        .edit-btn, .delete-btn{
            width:76px;
        }
</style>
<div id="success-flag" class="success-flag"></div>
<div class="card">
    <div class="card" style="margin-left:20px; margin-right:20px; margin-top:20px; padding:20px;">
        <form id="schlrshipForm" method="POST" action="">
            @csrf
                <div class="row"><h3>Master Scholarship Form:</h3></div>
                <div class="row filter-row" style="margin-right:10px;">
                    <input type="text" name="mainId" id="main-Id" value='' class="form-control" hidden>
                    <div class="form-group col-sm-6">
                        <label for="school_sel">Scholarship</label>
                        <select name="scholarship" id="scholarship_id" class="form-control">
                            <option value="">Select Scholarships</option>
                            @foreach ($scholarships as $row)
                            <option value="{{ $row->scholarship_id }}">{{ $row->scholarship_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="tutionAmt">Amount</label>
                        <input type="text"  onkeypress="return /[0-9.]/i.test(event.key)" name="amount" id="amount" class="form-control">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="school_sel">Select User</label>
                        <select name="user" id="user_to" class="form-control selectpicker" data-live-search="true">
                            <option value="">Select</option>
                            @foreach ($employees as $row)
                            <option value="{{ $row->employee_id }}">{{ $row->prefix }} {{ $row->emp_f_name }} {{ $row->emp_l_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-6 d-none" style="display: flex; flex-direction: row;">
                        <label for="schlrshp-type">Scholarship Type</label>
                        <div class="checkboxOptions">
                            <input type="radio" class="validity-type" id="session-wise" name="validity-type" value="session">
                            <label for="session-wise">Session wise</label>
                        </div>
                        <div  class="checkboxOptions">
                            <input type="radio" class="validity-type" id="annual-wise" name="validity-type" value="">
                            <label for="annual-wise">Annual wise</label>
                        </div>
                    </div>
                    <div class="form-group sessions_select col-sm-6">
                        <label for="session_sel">Session</label>
                        <select name="session" id="session_sel" class="form-control">
                            <option value="">Select Sessions</option>
                            @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group validity-date col-sm-6 d-none">
                        <label for="studentType">Effective From</label>
                    <input type="date" name="effective_from" id="eff-from" class="form-control">
                    </div>
                    <div class="form-group validity-date col-sm-6 d-none">
                        <label for="studentType">Effective To</label>
                    <input type="date" name="effective_to" id="eff-to" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-12" style="display: flex;justify-content: center;">
                    <button type="button" style="width: 150px;" id="saveButton" class="btn btn-primary">Save</button>
                </div>
                <div id="error_message" class="alert alert-danger" style="display: none;"></div>
                <div id="success_message" class="alert alert-success" style="display: none;"></div>
        </form>
    </div>
    <div class="row" style="margin-top:10px;">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Form Id</th>
                                <th scope="col">Scholarship Type</th>
                                <th scope="col">User</th>
                                <th scope="col">Validity Type</th>

                                <th scope="col">Session</th>
                                {{-- <th scope="col">Effective From</th>
                                <th scope="col">Effective To</th> --}}
                                <th scope="col">Amount</th>
                                <th scope="col">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="tbody">
                            </tbody>
                        </table>
                    </div>
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
    <!-- <div class="card-header">
        <h3>Result</h3>
        <button class="btn primary" onclick="getPDF();" >GET PDF</button>

    </div> -->
    <!-- <div class="card-body" id="facultyReport">
    <table class="table table-sm">
        <thead>
          <tr>
            <th scope="col">Sr.No</th>
            <th scope="col">Name of Faculty</th>
            <th scope="col">Course Code</th> -->
            <!-- <th scope="col">Course Title</th>
            <th scope="col">Course Cr.Hrs</th>
            <th scope="col">Total No of Students</th>
            <th scope="col">No of Evaluators</th>
            <th scope="col">Quality of Teaching</th>
            <th scope="col">Course Evaluation</th>
            <th scope="col">Exams & Assessment</th>
            <th scope="col">Overall Rating%</th>
            <th scope="col">Comments</th> -->
             <!-- <th scope="col">Re Chance</th> -->
            <!-- <th scope="col">Student's Contribution</th> -->
            <!-- </tr>
            </thead>
            <tbody id="result-body" ><tr><td colspan="12" style="text-align:center;">No Filter Apply Yet!</td></tr></tbody>
        </table>
        </div> -->
    <!-- </div> -->
</div>
<div style="clear:both;"></div>
@stop
@section('javascript')
<script>

document.getElementById('saveButton').addEventListener('click', function() {
    var form = document.getElementById("schlrshipForm");
    var formData = new FormData(form);
    formData.append('scholarship', $('#scholarship_id').val());
    formData.append('session', $('#session_sel').val());
    formData.append('user', $('#user_to').val());
    formData.append('validity-type', $('.validity-type').val());
    formData.append('effective_from', $('#eff-from').val());
    formData.append('effective_to', $('#eff-to').val());
    formData.append('amount', $('#amount').val());

    console.log(formData);
    fetch('addScholarship', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#main-Id').val('');
            refreshFields();
            $("#error_message").text('').hide();
            $('#success_message').html(data.msg);
            $('#success_message').fadeIn('slow');
            $('#success_message').fadeOut(5000);
            LoadScholarships();
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
        console.error('There was an error!', error);
    });
});




    function getPDF(){
        //var lastURL = lastURL.replace("/reports/faculty", " ");
       //var lastURL='/reports/faculty';
//alert(lastURL+'&result-for=print');
//return false;
        window.open(lastURL+'&result-for=print');

    }
    $(document).ready(function(){
        $('#user_to').selectpicker('refresh');
        LoadScholarships();
    });
    $(document).on('change', '.validity-type', function() {
        selectedValue = $(this).attr('id');
        if(selectedValue == 'session-wise'){
            $('.sessions_select').removeClass('d-none');//show session selection div
            $('.validity-type').val('session');//setting validityType value
            $('.validity-date').addClass('d-none'); //hiding thd dates fields
            $('#eff-from').val('');//emptying the from date
            $('#eff-to').val('');//emptying the to date

        }else if(selectedValue == 'annual-wise'){
            $('.validity-date').removeClass('d-none');//show validity dates field
            $('.validity-type').val('annual');//setting validityType value
            $('.sessions_select').addClass('d-none');//hide session selection div
            $('#session_sel').val('');//emptying the sesstion selection
        }
    });
    function LoadScholarships(){
        $.ajax({
        type: "POST",
        url: "/api/master-scholarship-form/get_scholarship_list",
        success: function(response) {
        $("#tbody").empty();
        $('#user_to').selectpicker('refresh');
        // Check if response is an array
        if (Array.isArray(response.data)) {
        console.log(response.data);
        response.data.forEach(function(item, index) {
            var sess_name = item.sess_name != null ? item.sess_name : 'N/A';
            var fsb_session_id = item.fsb_session_id != null ? item.fsb_session_id : 'null';
            var fsb_effect_from = item.fsb_effect_from != null ? item.fsb_effect_from : 'N/A';
            var effect_from = item.fsb_effect_from != null ? item.fsb_effect_from : 'null';
            var fsb_effect_to = item.fsb_effect_to != null ? item.fsb_effect_to : 'N/A';
            var effect_to = item.fsb_effect_to != null ? item.fsb_effect_to : 'null';

            var mainID = item.fsb_id;
            $("#tbody").append(
                '<tr>' +
                    '<td>' + (index + 1) + '</td>' +
                    '<td>' + item.fsb_form_id + '</td>' +
                    '<td>' + item.scholarship_name + '</td>' +
                    '<td>' +  item.prefix+' '+item.emp_f_name+' '+item.emp_l_name + '</td>' +
                    '<td>' + item.fsb_validity_type + '</td>' +


                    '<td>' + sess_name + '</td>' +////////
                    // '<td>' + fsb_effect_from + '</td>' +//Optional fields
                    // '<td>' + fsb_effect_to + '</td>' +////

                    '<td>' + item.fsb_remainig_amount + '</td>' +
                    '<td style="display: flex; align-items: center;">' +
                        '<button class="btn btn-primary btn-sm edit-btn" onclick="editRow('+item.fsb_schlr_ship_id+','+item.fsb_amount+','+item.fsb_to_userid+',`'+item.fsb_validity_type+'`,`'+fsb_session_id+'`,`'+ effect_from+'`,`'+ effect_to+'`,`'+mainID+'`)"><i class="fa fa-edit"></i> Edit</button>' +
                        '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
                        '<button class="btn btn-danger btn-sm delete-btn" onclick="deleteRow(`'+mainID+'`)"><i class="fa fa-trash"></i> Delete</button>' +
                    '</td>' +
                '</tr>'
            );
        });

        } else {
        console.error("Error: Response is not an array");
        }

        // Handle the successful response from the server
        console.log(response);
        },
        error: function(error) {
        // Handle the error
        console.error("Error:", error);
        }
        });



    }

function exportToExcel() {
  const table = document.getElementById('htmlTable');
  const rows = table.querySelectorAll('tr');

  let csvContent = "data:text/csv;charset=utf-8,";
  rows.forEach(row => {
    const cells = row.querySelectorAll('td, th');
    const rowData = Array.from(cells).map(cell => cell.textContent.replace(',', ''));
    csvContent += rowData.join(',') + "\n";
  });

  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', 'table.csv');
  document.body.appendChild(link);
  link.click();
}

function exportToExcel(tableId) {
  $(`#${tableId}`).tableExport({type:'xlsx', escape:'false'});
}
</script>

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin/tableExport.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.2/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script> --}}

    <!-- Bootstrap CSS -->
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css"> --}}

<!-- Bootstrap Select CSS -->
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css">

<!-- Bootstrap Select JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"></script>

<!-- Bootstrap Select LiveSearch JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/i18n/defaults-*.min.js"></script> --}}

<script>

function exportToPDF(tableId) {
  try {
    const doc = new jsPDF();
    doc.autoTable({ html: `#${tableId}` });
    doc.save('table.pdf');
  } catch (error) {
    console.error('Error exporting to PDF:', error);
    alert('An error occurred while exporting to PDF. Please try again.');
  }
}
function editRow(schlshpid, amnt, userid, validityType, session_id, eff_from, eff_to, mainId){

    // console.log('mianId->'+mainId);
    // console.log('userid->'+userid);

    $('#main-Id').val(mainId);
    $('#scholarship_id').val(schlshpid);
    $('#amount').val(amnt);
    $('#user_to').val(userid);
    $('#user_to').selectpicker('refresh');
    if(validityType == 'session'){
        $('.sessions_select').removeClass('d-none');//show session selection div

        $('.validity-type').val('session');//setting validityType value
        $('#session-wise').prop('checked', true);
        $('#annual-wise').prop('checked', false);
        $('#session_sel').val(session_id);

        $('.validity-date').addClass('d-none'); //hiding thd dates fields
        $('#eff-from').val('');//emptying the from date
        $('#eff-to').val('');//emptying the to date
    }else if(validityType == 'annual'){
        $('.validity-date').removeClass('d-none');//show validity dates field

        $('.validity-type').val('annual');//setting validityType value
        $('#annual-wise').prop('checked', true);
        $('#session-wise').prop('checked', false);
        var eff_from_datePart = eff_from.split(' ')[0];
        $('#eff-from').val(eff_from_datePart);
        var eff_to_datePart = eff_to.split(' ')[0];
        $('#eff-to').val(eff_to_datePart);

        $('.sessions_select').addClass('d-none');//hide session selection div
        $('#session_sel').val('');//emptying the sesstion selection
    }

}
function deleteRow(mainId){
    var confirmation = window.confirm("Are you sure you want to delete?");
    if (confirmation) {
        $.ajax({
            type: "POST",
            url: "/api/master-scholarship-form/delete_scholarship",
            data: {
                mainId: mainId
            },
            success: function(response) {
                $('#success-flag').text('Deleted Successfully');
                $('#success-flag').addClass('show');
                setTimeout(function() {
                    $('#success-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds
                LoadScholarships();

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
    $('#scholarship_id').val('');
    $('#amount').val('');
    $('#user_to').val('');
    $('#user_to').selectpicker('refresh');
    $('.validity-type').val('session');
    $('#session-wise').prop('checked', false);
    $('#annual-wise').prop('checked', false);
    $('#session_sel').val('');
    $('#eff-from').val('');
    $('#eff-to').val('');
    $('.validity-date').addClass('d-none');
    // $('.sessions_select').addClass('d-none');
}
</script>

@stop

