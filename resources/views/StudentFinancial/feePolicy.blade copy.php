@extends('layouts.app')

@section('title')
  BNU CMS
@stop

@section('content')

<div class="card p-4">

    <form  id="feePolicyFrom">
    @csrf

      <div class="row">
        <h3>Fee Policy:</h3>
      </div>

      <div class="row filter-row">
        <div class="form-group col-sm-6">
          <label for="session_sel">Session</label>
          <select  id="session_sel"  name="session_sel" class="form-control" >
            <option value="">All Session</option>
            @foreach ($sessions as $key => $value)
              <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
          </select>
          <span id="session_sel-error" class="error text-danger"></span>
        </div>
        <input type="hidden"  name="verifyEdit" id="verifyEdit" value="" >
        <input type="hidden"  name="edit_id" id="edit_id" value="" >

        <div class="form-group col-sm-6">
          <label for="school_sel">School</label>
          <select  id="school_sel" name="school_sel" class="form-control" onchange="getDepartments();">
            <option value="">All School</option>
            @foreach ($schools as $row)
              <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
            @endforeach
          </select>
          <span id="school_sel-error" class="error text-danger"></span>
        </div>

        <div class="form-group col-sm-6">
          <label for="department">Department</label>
          <select name="department" id="department"  class="form-control" onchange="getPrograms();">
            </select>
            <span id="department-error" class="error text-danger"></span>
        </div>

        <div class="form-group col-sm-6">
          <label for="course">Program</label>
          <select name="course" id="course" class="form-control">
            </select>
            <span id="course-error" class="error text-danger"></span>
        </div>
      </div>
      <div class="row filter-row">
        <div class="form-group col-sm-6">
          <label for="type">Type</label>
          <div class="form-check form-check-inline" style="margin-left: 40px;">
            <input class="form-check-input" type="radio" name="fee_Type" id="fee_Type" value="cr_wise">
            <label class="form-check-label" for="fee_Type">Cr. Wise</label>
          </div>
          <div class="form-check form-check-inline" style="margin-left: 40px;">
            <input class="form-check-input" type="radio" name="fee_Type" id="fee_Type" value="full_semester">
            <label class="form-check-label" for="fee_Type">Full Semester</label>
          </div>
          <span id="fee_Type-error" class="error text-danger"></span>
        </div>

        <div class="form-group col-sm-6 d-flex align-items-center">
          <label for="range">Range:</label>
          <div class="input-group ms-2">
            <input type="number" min="0" class="form-control" id="min_range" name="min_range" placeholder="Min">
            <div class="input-group-text">-</div>
            <input type="number" min="0" class="form-control" id="max_range" name="max_range" placeholder="Max">

        </div>
<br>
        <span id="min_range-error" class="error text-danger float-end"></span>
        <span id="max_range-error" class="error text-danger float-end"></span>
        <div>

    </div>


</div>


<button type="submit" class="btn btn-primary">Submit</button>
</form> <br>
<h5 id="successMsg"  class"mt-3" style="color:green; margin-left:10px;"></h5>



<div id="showDataInTable" class="table-responsive"></div>
</div>

@stop

@section('javascript')
<script>


$(document).ready(function() {
    getFeePolicy();







    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});







    $('#feePolicyFrom').submit(function(event) {
        // Prevent default form submission
        event.preventDefault();

        // Serialize form data
        var formData = $(this).serialize();

        // Send AJAX request
        $.ajax({
            url: '/finance/submit-fee-policy',
            type: 'POST',
            data: formData,
            success: function(response) {
                // Handle success response
                if (response.status) {
                    // Reset the form
                    document.getElementById("feePolicyFrom").reset();
                    $('#verifyEdit').val('');
                    $('#edit_id').val('');
                    // Display success message
                    $('#successMsg').html('<i class="fa fa-check-circle mr-1"></i>' + response.message);
                    
                    // Update table with new data
                    makeTableHtml(response.data);
                    
                    // Clear success message after 3 seconds
                    setTimeout(function() {
                        $('#successMsg').html('');
                    }, 3000);
                }
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.error(xhr.responseText);
                displayValidationErrors(xhr);
            }
        });
    });




//    // Delete button click event
//    $(document).on('click', '.delete-rec', function() {
//         var recId = $(this).data('rec');

//         // Send AJAX request to delete record
//         $.ajax({
//             url: '/finance/delete-fee-policy/' + recId,
//             type: 'DELETE',
//             success: function(response) {
//                 // Handle success response
//                 if (response.status) {
//                     $('#successMsg').html('<i class="fa fa-check-circle mr-1"></i>' + response.message);
                    
//                     // Refresh the list of fee policies
//                     makeTableHtml(response.data);
                    
//                     // Clear success message after 3 seconds
//                     setTimeout(function() {
//                         $('#successMsg').html('');
//                     }, 3000);
//                 }
//             },
//             error: function(xhr, status, error) {
//                 // Handle error response
//                 console.error(xhr.responseText);
//                 $('#errorMsg').html('<i class="fa fa-exclamation-circle mr-1"></i>' + xhr.responseText);
                
//                 // Clear error message after 3 seconds
//                 setTimeout(function() {
//                     $('#errorMsg').html('');
//                 }, 3000);
//             }
//         });
//     });

});


function getDepartments() {
  const school_id = $('#school_sel').val();

  $.ajax({
    url: '/department/get?school_id=' + school_id,
    method: 'GET',
    success: function(response) {
      if (response.status) {
        let html = '<option value="">All Dept</option>';
        $.each(response.data, function(key, value) {
          html += `<option value="${value.dept_id}">${value.dept_name}</option>`; // Template literal for option string
        });
        $('select[name="department"]').html(html);
      } else {
        console.error('Error fetching departments:', response.message); // Log error message
        // Optionally display a user-friendly error message
      }
    },
    error: function(response) {
      console.error('Error fetching departments:', response.statusText); // Log error details
      // Optionally display a generic error message to the user
    }
  });
}





    function getPrograms() {
  const session_id = $('#session_sel').val(); // Assuming session_id is still used (remove if not)
  const dept_id = $('#department').val();
  const url = `/program/get?dept_id=${dept_id}&session_sel=${session_id}`; // Template literals for URL

  $.ajax({
    url: url,
    method: 'GET',
    success: function(response) {
      if (response.status) {
        let html = '<option value="">All Program</option>';
        $.each(response.data, function(key, value) {
          html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
        });
        $('select[name="course"]').html(html); // Update "course" select
      } else {
        console.error('Error fetching programs:', response.message); // Log error message
        // Optionally display an error message to the user
      }
    },
    error: function(response) {
      console.error('Error fetching programs:', response.statusText); // Log error details
      // Optionally display a generic error message to the user
    }
  });
}

function displayValidationErrors(xhr){
    if (xhr.status == 422) {
        var errors = xhr.responseJSON.errors;
        $.each(errors, function(key, value) {
            $("#" + key + "-error").html('<i class="fa fa-info-circle"></i> ' + value[0]);
            $("#" + key + "-error").addClass('text-danger');
            setTimeout(function(){
                $("#" + key + "-error").text('');
            }, 5000);
        });
    }
}


function makeTableHtml(data) {
    let showDataInTable = document.getElementById('showDataInTable');
    showDataInTable.innerHTML = ''; // Clear previous content
    if (data.length>0) {
        let createHtml = `
        <div style="max-height: 8000px; overflow-y: auto; margin-top:10px;">
        <table class="table table-bordered  table-hover" id='tableData'>
            <thead class="thead-light">
                <tr>
                    <th scope="col">Session Name</th>
                    <th scope="col">Type</th>
                    <th scope="col">School Name</th>
                    <th scope="col">Dept Name</th>
                    <th scope="col">Prog Name</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                ${data.map(element => `
                    <tr>
                        <td>${element.sess_name}</td>
                        <td>${element.ffs_type}</td>
                        <td>${element.school_name}</td>
                        <td>${element.dept_name}</td>
                        <td>${element.prog_name}</td>
                          <td>
                                <button type='button'  class="btn btn-primary edit-rec" data-rec='${JSON.stringify(element)}'>Edit</button>

                            </td>
                          <td>
                                <button type='button'  class="btn btn-danger "  onclick="delRec(${element.ffp_id})" >Delete</button>

                            </td>

                    </tr>
                `).join('')}
            </tbody>
        </div>
        </table>
    `;

    showDataInTable.innerHTML = createHtml;

     $('#tableData').DataTable();
        // Add event listeners to dynamically created buttons
        let editButtons = document.querySelectorAll('.edit-rec');
        editButtons.forEach(button => {
            button.addEventListener('click', editRec);
        });


    }

}




function getFeePolicy(){
  $.ajax({
        url: '/finance/get-fee-policy',
        type: 'GET',
        success: function(data) {
          makeTableHtml(data)
          // Handle success response
        },
        error: function(xhr, status, error) {
          console.error(xhr.responseText);
        }
      });
    }

// Function to handle editing scholarship data
function editRec(event) {
    const buttonElement = event.target;
    const dataString = buttonElement.dataset.rec;



    try {
        const Data = JSON.parse(dataString);
        window.scrollTo({ top: 0, behavior: 'smooth' });
        $('#verifyEdit').val('1');
        $('#edit_id').val(Data.ffp_id).trigger('change');
        $('#session_sel').val(Data.ffp_sess_id).trigger('change');


        $('#school_sel').val(Data.ffp_school_id).trigger('change');

        setTimeout(()=>{

        $('#department').val(Data.ffp_dept_id).trigger('change');
        },1000)
        setTimeout(()=>{

            $('#course').val(Data.ffp_prog_id).trigger('change');

        },2000)



        // Assuming 'fee_Type' is the ID of a radio button group
        $('input[name="fee_Type"][value="' + Data.ffs_type + '"]').prop('checked', true).trigger('change');

        // Adjusting min_range and max_range based on ffp_criteria
        var ffp_criteria = Data.ffp_criteria.split('-');
        $('#min_range').val(ffp_criteria[0]).trigger('change');
        $('#max_range').val(ffp_criteria[1]).trigger('change');



    } catch (error) {
        console.error("Parsing Error:", error);
    }
}

// Function to delete a record
function delRec(recId) {
    // Send AJAX request to delete record
    $.ajax({
        url: '/finance/delete-fee-policy/' + recId,
        type: 'DELETE',
        success: function(response) {
            // Handle success response
            if (response.status) {
                $('#successMsg').html('<i class="fa fa-check-circle mr-1"></i>' + response.message);
                
                // Refresh the list of fee policies
                getFeePolicy();
                
                // Clear success message after 3 seconds
                setTimeout(function() {
                    $('#successMsg').html('');
                }, 3000);
            }
        },
        error: function(xhr, status, error) {
            // Handle error response
            console.error(xhr.responseText);
            $('#errorMsg').html('<i class="fa fa-exclamation-circle mr-1"></i>' + xhr.responseText);
            
            // Clear error message after 3 seconds
            setTimeout(function() {
                $('#errorMsg').html('');
            }, 3000);
        }
    });
}

</script>
@stop
