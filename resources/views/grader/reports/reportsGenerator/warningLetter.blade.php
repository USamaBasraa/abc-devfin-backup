@php
    $canShowWarningLetter = auth()->user()->school_id == 2 || auth()->user()->group_id == 644;
    $display = $canShowWarningLetter ? 'block' : 'none';
@endphp<!-- Warning Letter Modal -->

<style>
    .form-check-label {
        font-size: 20px; /* Adjust the font size as needed */
        margin-left: 10px;
    }

    .form-check-input {
        width: 20px; /* Adjust the width of the checkbox */
        height: 20px; /* Adjust the height of the checkbox */
    }
    .table-hover tbody tr:hover {
    background-color: #f5f5f5; /* Choose your desired hover color */
}
</style>


<div class="modal fade" id="warningLetterModal" tabindex="-1" role="dialog" aria-labelledby="warningLetterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="warningLetterModalLabel"> <i class="fa fa-exclamation-circle"></i>    Warning Notices</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div id="showWarningLetter" class="table-responsive"></div>
                <form id="warningLetterForm">
                    @csrf

                <!-- Add your warning letter form or content here -->
                <div class="form-check m-3" style="display: <?php echo $display; ?>;">
                    <input type="checkbox" id="checkboxOption1" name="warningOption[]" value="1" class="form-check-input">
                    <label for="checkboxOption1" class="form-check-label d-flex align-items-center">
                        <span class="flex-grow-1">Unsatisfactory Academic Performance</span>
                    </label>
                </div>


                <input type="hidden" id="sendWarningLetterTo" name="sendWarningLetterTo" value="">

                <label for="file"><h5>Upload File:</h5></label>
            <input type="file" class="form-control-file" id="file" name='file' accept=".pdf, .jpg, .jpeg, .png, .ppt, .docx, .xlsx">
            <span class="text-muted ">Only <span class='text-danger'>PDF, JPG, JPEG, PNG, PPT, DOC and XLS</span> files are allowed.</span>

            </form>
            <h5  id='successMessage' class='text-success'></h5>
            <div id='errorMsg'>

            </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button"  onclick="sendWarningLetter()"      class="btn btn-warning">Send Warning</button>
            </div>
        </div>
    </div>
</div>








<!-- Multiple Warning Letters Modal -->
<div class="modal fade" id="multipleWarningLettersModal" tabindex="-1" role="dialog" aria-labelledby="multipleWarningLettersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="multipleWarningLettersModalLabel">Send Warning Letters to Multiple Students</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="multipleWarningLetterForm">
                    @csrf

                    <div class="form-check m-3">
                    <input type="checkbox" id="multipleCheckboxOption" name="warningOption[]" value="1" class="form-check-input">
                    <label for="multipleCheckboxOption" class="form-check-label d-flex align-items-center">
                        <span class="flex-grow-1">Unsatisfactory Academic Performance</span>
                    </label>
                </div>


                    <input type="hidden" id="multipleWarningLetterData" name="multipleWarningLetterData" value="">
                    <label for="file"><h5>Upload File:</h5></label>
                    <input type="file" class="form-control-file" id="fileForMultiple" name='file' accept=".pdf, .jpg, .jpeg, .png, .ppt, .docx, .xlsx">
                    <span class="text-muted">Only <span class='text-danger'>PDF, JPG, JPEG, PNG, PPT, DOC and XLS</span> files are allowed.</span>
                </form>
                <h5 id='multipleSuccessMessage' class='text-success'></h5>
                <div id='multipleErrorMsg'></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="sendMultipleWarningLetters()" class="btn btn-warning">Send Warnings</button>
            </div>
        </div>
    </div>
</div>













<script>

function checkInputs(type) {
    if (type === 'multiple') {
        // Logic for multiple warning letters
        let checkboxOption = document.getElementById('multipleCheckboxOption');
        let fileInput = document.getElementById('fileForMultiple');

        // Check if either checkbox is checked or a file is selected
        if (!checkboxOption.checked && fileInput.files.length === 0) {
            displayErrorMessage("Select a Warning Letter Type or Upload a File.", 'multipleErrorMsg');
            return false; // Prevent further action
        }
    } else if (type === 'single') {
        // Logic for single warning letter
        let checkboxOption1 = document.getElementById('checkboxOption1');
        let fileInput = document.getElementById('file');

        // Check if either checkbox is checked or a file is selected
        if (!checkboxOption1.checked && fileInput.files.length === 0) {
            displayErrorMessage("Select Warning Letter Type or Upload a File.", 'errorMsg');
            return false; // Prevent further action
        }
    } else {
        // Handle unknown type or provide a default case
        displayErrorMessage("Invalid warning letter type.", 'errorMsg');
        return false; // Prevent further action
    }
    return true; // Allow further action
}

function displayErrorMessage(message, elementId) {
    let errorMsg = document.getElementById(elementId);
    if (errorMsg) {
        errorMsg.innerHTML = `<div class="alert alert-danger" role="alert">${message}</div>`;
        
        // Hide the error message after 5 seconds
        setTimeout(function() {
            errorMsg.innerHTML = '';
        }, 5000);
    }
}




function warningLetterBtn(enrollmentNo) {
    // Set the value of enrollmentNo in the hidden input field

    

    document.querySelector('#warningLetterModalLabel').innerHTML = enrollmentNo;


    document.getElementById('sendCommentTo').value = enrollmentNo;
    document.getElementById('sendWarningLetterTo').value = enrollmentNo;
    getWarningLetters(enrollmentNo);

}



function sendWarningLetter() {
    
    let form = document.getElementById('warningLetterForm');
    let data = new FormData(form);
    let successMsg = document.getElementById('successMessage');
    let button = document.querySelector('.btn-warning');

    if (!checkInputs('single')) {
        // If inputs are empty, prevent form submission
        return false;
    }

 

   

    // Add spinner loading animation to the button
    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
    button.disabled = true; // Disable the button while sending the request

    fetch(`/reports/send-warning-letter-to-student`, {
        method: 'POST', // Using POST method
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            form.reset();
            successMsg.innerHTML = data.message;
            submitForm();
            makeLettersHtml(data.warningLetters);
            // Remove spinner loading animation from the button on success
            button.innerHTML = 'Send Warning';
            button.disabled = false; // Enable the button again
            setTimeout(function () {
                successMsg.innerHTML = '';
                $('#warningLetterModal').modal('hide');

            }, 3000);
        } else {
            console.error('Error:', data.errors);
            // Handle error here if needed

            // Remove spinner loading animation from the button on error
            button.innerHTML = 'Send Warning';
            button.disabled = false; // Enable the button again
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        // Handle fetch error here if needed

        // Remove spinner loading animation from the button on error
        button.innerHTML = 'Send Warning';
        button.disabled = false; // Enable the button again
    });
}

function getWarningLetters(enrollmentNo) {
    fetch(`/reports/get-student-warning-letter/${enrollmentNo}`, {
        method: 'GET',
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json(); // Corrected this line
    })
    .then(data => {
        makeLettersHtml(data)
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function makeLettersHtml(data) {
    let showWarningLetter = document.getElementById('showWarningLetter');
    showWarningLetter.innerHTML = ''; // Clear previous content

    if (data.length > 0) {
        let lettersHtml = `
        <div style="max-height: 200px; overflow-y: auto;">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th scope="col">Warning Type</th>
                    <th scope="col">Time</th>
                    <th scope="col">Warning Given By</th>
                    <th scope="col">Actions</th> <!-- New column for actions -->
                </tr>
            </thead>
          <tbody>
            ${data.map(element => {
                // Determine if the row should be marked as deleted
                const isDeleted = element.status === 'D';
                
                // Create the file link once and reuse it
                const fileLink = element.warning_letter_type!=1 
                    ? `<a href="${element.swl_filelink}" target="_blank">${element.warning_letter_name ? element.warning_letter_name : element.swl_filename}</a>`
                    : `<a href="https://student.bnu.edu.pk/unsatisfactory-academic-performance" target="_blank">${element.warning_letter_name}</a>`;

                return `
                <tr data-id="${element.id}" data-enrollment="${element.swl_enrollment_no}" style="${isDeleted ? 'text-decoration: line-through; color: gray;' : ''}">
                    <td>${fileLink}</td>
                    <td>${new Date(element.entry_timestamp).toLocaleString()}</td>
                    <td>${element.full_name}</td>
                    <td>
                        ${isDeleted ? '<span class="badge badge-danger"><i class="fa fa-trash-alt"></i> Deleted</span>' : 
                        `<button class="btn btn-danger btn-sm" onclick="deleteWarningLetter(${element.id}, '${element.swl_enrollment_no}')"><i class="fa fa-trash"></i> Delete</button>`}
                    </td>
                </tr>
                `;
            }).join('')}
        </tbody>

        </table>
        </div>
    `;

        showWarningLetter.innerHTML = lettersHtml;
    } else {
        showWarningLetter.innerHTML = '<p>No warning letters found.</p>'; // Handle empty state
    }
}







function sendMultipleWarningLetters() {
    let form = document.getElementById('multipleWarningLetterForm');
    let data = new FormData(form);

    if (!checkInputs('multiple')) {
        // If inputs are empty, prevent form submission
        return false;
    }


    // Collect selected student IDs
    const selectedStudents = Array.from(document.querySelectorAll('input[name="selectedStudents[]"]:checked'))
        .map(checkbox => checkbox.value);


if (selectedStudents.length > 0) {
    // Add selected students array to form data
    data.append('multipleWarningLetterData', JSON.stringify(selectedStudents));
} else {
    // Display error message if no students are selected
    displayErrorMessage("Select at least one student before proceeding.", 'multipleErrorMsg');
}




    let successMsg = document.getElementById('multipleSuccessMessage');
    let button = document.querySelector('.btn-warning');

    // Add spinner loading animation to the button
    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
    button.disabled = true; // Disable the button while sending the request

    fetch(`/reports/send-warning-letters-to-multiple-students`, {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
         
            submitForm();
            form.reset();



            successMsg.innerHTML = data.message;
            // Remove spinner loading animation from the button on success
            button.innerHTML = 'Send Warnings';
            button.disabled = false; // Enable the button again
            setTimeout(function () {
                successMsg.innerHTML = '';
                $('#multipleWarningLettersModal').modal('hide');
            }, 2000);
          


            

        } else {
            console.error('Error:', data.errors);
            // Handle error here if needed
            button.innerHTML = 'Send Warnings';
            button.disabled = false; // Enable the button again
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        // Handle fetch error here if needed
        button.innerHTML = 'Send Warnings';
        button.disabled = false; // Enable the button again
    });
}

function deleteWarningLetter(id, enrollmentNo) {
    if (confirm("Are you sure you want to delete this warning letter?")) {
        $.ajax({
            url: '{{ route("reports/delete-warning-letter") }}', 
            type: 'DELETE',
            data: {
                id: id,
                enrollment_no: enrollmentNo,
                _token: '{{ csrf_token() }}' 
            },
            success: function(response) {
                if (response.success) {
                    // Update the HTML table with the new records
                    makeLettersHtml(response.updatedRecords); // Refresh the table with updated records
                    $('#successMessage').text('Warning letter marked as deleted successfully.');
                    submitForm();
                } else {
                    $('#errorMsg').html(`<div class='alert alert-danger'>${response.message}</div>`);
                }
            },
            error: function(xhr, status, error) {
                $('#errorMsg').html(`<div class='alert alert-danger'>Error: ${error}</div>`);
            }
        });
    }
}



</script>
