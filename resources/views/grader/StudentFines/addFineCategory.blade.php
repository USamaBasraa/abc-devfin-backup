@extends('layouts.app')
@section('content')




<div class="container">
    <div class="card p-4">

          @php
            $validUsernames = ['asif_nadeem'];
            $validGroupIds = [644]; 
        @endphp


        @if(in_array(auth()->user()->username, $validUsernames) || in_array(auth()->user()->group_id, $validGroupIds))

            <div id="addUserPermission" class="my-4">
                <h4 class="text-left mb-4">Add User Permission</h4>
                <form id="submitUserForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="form-group col-md-6">
                            <label for="category" class="font-weight-bold">Select Category:</label>
                            <select name="category" id="category" class="form-control select2">
                                <option value="" selected disabled>--- Select Category ---</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->fc_category_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="user" class="font-weight-bold">Select User:</label>
                            <select name="user" id="user" class="form-control select2">
                                <option value="" selected disabled>--- Select User ---</option>
                                @foreach($getUsers as $user)
                                    <option value="{{ $user->employee_id }}">{{ $user->prefix }} {{ $user->emp_f_name }} {{ $user->emp_l_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="form-group col-md-6">
                            <label for="effectiveFrom" class="font-weight-bold">Effective From:</label>
                            <input type="date" name="effective_from" id="effectiveFrom" class="form-control" required>
                        </div>

                        <div class="form-group col-md-6 d-flex align-items-end">
                            <button type="button" onclick="submitUserFormBtn()" class="btn btn-outline-primary" style="padding: 0.375rem 0.75rem;">
                                <i class="fa fa-check"></i> Submit
                            </button>
                        </div>
                    </div>
                </form>

                <div id="successMsg" class="text-success mt-2"></div>
            </div>

            <hr>

            <div id="accordion">
          

            <a href="assets/cms/reports/fine-category-permissions.php?report_type=1" class="btn btn-outline-success mx-2 mb-4" target="_blank">
                <i class="fa fa-file-pdf-o"></i> PDF
            </a>

            <a href="assets/cms/reports/fine-category-permissions.php?report_type=2" class="btn btn-outline-success mx-2 mb-4" target="_blank">
                <i class="fa fa-file-excel-o"></i> Excel
            </a>





                @foreach($categories as $category)
                    <div class="card">
                        <div class="card-header" id="heading{{ $category->id }}" data-toggle="collapse" data-target="#collapse{{ $category->id }}" aria-expanded="false" aria-controls="collapse{{ $category->id }}">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" style="text-decoration: none; color: inherit;">
                                        {{ $category->fc_category_name }}
                                    </button>
                                </h5>
                                <button class="btn btn-link p-0" style="color: inherit;">
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                            </div>
                        </div>

                        <div id="collapse{{ $category->id }}" class="collapse" aria-labelledby="heading{{ $category->id }}" data-parent="#accordion">
                            <div class="card-body">
                                @if($fineCategoryUser->isEmpty())
                                    <div class="alert alert-warning" role="alert">
                                        No data available.
                                    </div>
                                @else
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Effective From</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($fineCategoryUser as $rec)
                                                @if($rec->fc_id == $category->id)
                                                    <tr>
                                                        <td class="employee-name">{{ $rec->emp_f_name }} {{ $rec->emp_l_name }}</td>
                                                        <td class="effective-from">{{ $rec->fcp_effective_from  }}</td>
                                                        <td>
                                                            <button onclick="deleteRecord({{ $rec->id }})" class="btn btn-danger btn-sm">
                                                                <i class="fa fa-trash"></i> Delete
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @endif

        <hr>

        <h4 id='categoryAdd'>Add New Category</h4>
        <form id="submitForm">
            @csrf
            <input type="hidden" name="verifyEdit" id="verifyEdit" value="">
            <input type="hidden" name="id" id="id" value="">

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="categoryName">Category Name:</label>
                    <input type="text" name="categoryName" id="categoryName" class="form-control" placeholder="Enter category name" style="width: 100%;">
                    <h6 data-error-for="categoryName" class="validation-error pt-4"></h6>
                </div>

                <div class="form-group col-md-6">
                    <label for="description">Description:</label>
                    <input type="text" name="description" id="description" class="form-control" placeholder="Enter description" style="width: 100%;">
                </div>
            </div>

            <div id="successMessage" class="text-success mt-2"></div>

            <div class="d-flex justify-content-center">
                <button type="button" onclick='submitForm()' class="btn btn-outline-primary mx-2">
                    <i class="fa fa-check"></i> Submit
                </button>
                <button type="button" onclick="clearForm()" class="btn btn-outline-info mx-2">
                    <i class="fa fa-eraser"></i> Clear
                </button>
            </div>
        </form>

        <div id="dataDiv" style="margin-top:20px;"></div>
    </div>
</div>



@stop
@section('javascript')

<script src="{{ asset('weekStudentsReport/xlsx.full.min.js') }}"></script>


<script>
$(document).ready(function() {
        $('.select2').select2();
    });
   function clearForm() {
    // Reset the form with the ID "submitForm"
    document.getElementById('submitForm').reset();
}

function submitForm(){
    let form = document.getElementById('submitForm');
        let data = new FormData(form);
        let successMsg=document.getElementById('successMessage');

        fetch(`/add-student-fine-categories`, {
            method: 'POST', // Using POST method
            body: data
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // getNotifications(co_id);
                form.reset();
                // challanRec
                // makeCommentsHtml(data.getComments)
                console.log('Success:', data.message);
                successMsg.innerHTML =data.message;
                makeDataHtml(data.challanRec);
                setTimeout(function(){
                    successMsg.innerHTML = '';
                    window.location.reload();
                },2000)
                // Handle success response here
            } else {

                  displayValidationErrors(data.errors);
                console.error('Error:', data.errors);
                // Handle error here
            }
        })
        .catch((error) => {
            displayValidationErrors(error);
            console.error('Error:', error);
            // Handle fetch error here
        });
}

function makeDataHtml(data) {
    let showWarningLetter = document.getElementById('dataDiv');
    showWarningLetter.innerHTML = ''; // Clear previous content
    let sr = 1;

    if (data.length > 0) {
        let lettersHtml = `
        <div class="table-responsive" style="max-height: 800px; overflow-y: auto;">
            <table id="dataTable" class="table table-striped table-bordered">
                <thead class="thead">
                    <tr style="font-size: 20px; font-weight: bold;">
                        <th scope="col">Sr#</th>
                        <th scope="col">Category</th>
                        <th scope="col">Fine Description</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody style="font-size: 18px;">
                    ${data.map(element => `
                        <tr>
                            <td>${sr++}</td>
                            <td>${element.fc_category_name || 'N/A'}</td>
                            <td>${element.fc_description || 'N/A'}</td>
                            <td>
                             <div class="d-flex justify-content-center">
                            <button class="btn btn-outline-primary mx-2 edit-category" data-category='${JSON.stringify(element)}'>
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-outline-danger mx-2 delete-category" data-category='${JSON.stringify(element)}' onclick="deleteRows('${element.id}')">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </div>

                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        `;

        showWarningLetter.innerHTML = lettersHtml;

        // Initialize DataTable
        $('#dataTable').DataTable({
        dom: 'Bfrtip', 
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o mr-1"></i>Excel', 
                title: 'Data Export',
                exportOptions: {
                    columns: ':not(:last-child)' 
                },
                className: 'ml-2 btn btn-primary'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf-o mr-1"></i>PDF', 
                title: 'Data Export',
                exportOptions: {
                    columns: ':not(:last-child)' 
                },
                 className: 'ml-2 btn btn-primary'
            }
        ]
    });

        // Add event listeners to dynamically created buttons
        let editButtons = document.querySelectorAll('.edit-category');
        editButtons.forEach(button => {
            button.addEventListener('click', editCategoryRec);
        });

    } else {
        // Optionally display a message when there is no data
        showWarningLetter.innerHTML = '<p>No data available.</p>';
    }
}


function editCategoryRec(event) {
    const buttonElement = event.target;
    const categorypDataString = buttonElement.dataset.category;
    try {
        const DataString = JSON.parse(categorypDataString);
    console.log(DataString);

    $('#categoryName').val(DataString.fc_category_name);
    $('#description').val(DataString.fc_description);
    $('#verifyEdit').val('1');
    $('#id').val(DataString.id);


    // document.getElementById('categoryAddDiv').scrollIntoView({
    //     behavior: 'smooth'
    // });

    
    $('html, body').animate({
    scrollTop: $('#categoryAdd').offset().top 
}, 600); 



    } catch (error) {
        console.error("Parsing Error:", error);
    }
}
function deleteRows(mainId){
    if (confirm("Are you sure you want to delete this record?")) {
        $.ajax({
            type: 'POST',
            url: '/delete-fine-category',
            data: {
                _token: '{{ csrf_token() }}',
                mainId: mainId
            },
            success: function(response) {
                // Display success message
                console.log(response);
                  $('#successMsg').html(response.message);
                // Reload the page after 3 seconds
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error(xhr.responseText);
            }
        });
    }

}






(() => {
    fetch(`/get-student-fine-categories`, {
        method: 'GET', // Using GET method
    })
    .then(response => response.json())
    .then(data => {
        makeDataHtml(data);
    })
    .catch((error) => {
        console.error('Error:', error);
        // Handle fetch error here
    });
})();


function displayValidationErrors(errors) {
        Object.entries(errors).forEach(([key, value]) => {
            var trimmedKey = key.trim();
            var errorElement = document.querySelector("[data-error-for='" + trimmedKey + "']");

            if (errorElement) {
                // Check if the value is an array, and get the first element
                var errorMessage = Array.isArray(value) ? value[0] : value;

                // Create a span element for the error message and icon
                var errorSpan = document.createElement('span');
                errorSpan.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + errorMessage;

                // Clear the existing content and add the new span
                errorElement.innerHTML = '';
                errorElement.appendChild(errorSpan);

                errorElement.classList.add('text-danger');

                setTimeout(function () {
                    errorElement.innerHTML = ''; // Clear both icon and text
                    errorElement.classList.remove('text-danger');
                }, 5000);
            }
        });
    }


function submitUserFormBtn() {
    // Serialize form data
    var formData = $('#submitUserForm').serialize();

    // Send AJAX request
    $.ajax({
        url: '/fine-category-permission', // Replace with your actual endpoint URL
        method: 'POST',
        data: formData,
        success: function(response) {
            // Display success message
            $('#successMsg').html(response.message);

            // Reload the page after 3 seconds
            setTimeout(function() {
                window.location.reload();
            }, 2000);
        },
        error: function(xhr, status, error) {
            // Handle error response
            console.error('AJAX request failed');
            console.error(status);
            console.error(error);
        }
    });
}

function deleteRecord(id) {
    if (confirm("Are you sure you want to delete this record?")) {
        $.ajax({
            type: 'POST',
            url: '/delete-fine-category-permission',
            data: {
                _token: '{{ csrf_token() }}',
                id: id
            },
            success: function(response) {
                // Display success message
                console.log(response);
                  $('#successMsg').html(response.message);
                // Reload the page after 3 seconds
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error(xhr.responseText);
            }
        });
    }
}


function downloadPermissionsReport() {
    const workbook = XLSX.utils.book_new();
    const worksheetData = [];
    
    // Iterate through each category card
    const accordionItems = document.querySelectorAll('.card');

    accordionItems.forEach(item => {
        const categoryButton = item.querySelector('.card-header button');
        const categoryName = categoryButton ? categoryButton.innerText.trim() : '';

        // Push the category name as a header row
        worksheetData.push([categoryName, "", ""]); // Category Name in first column, leave others empty
        worksheetData.push(["Employee Name", "Effective From", ""]); // Column headers
        
        // Collect employee names and effective dates from the table rows
        const rows = item.querySelectorAll('.card-body table tbody tr');
        
        if (rows.length > 0) {
            rows.forEach(row => {
                const employeeNameCell = row.querySelector('.employee-name');
                const effectiveDateCell = row.querySelector('.effective-from');
                
                const employeeName = employeeNameCell ? employeeNameCell.innerText.trim() : '';
                const effectiveDate = effectiveDateCell ? effectiveDateCell.innerText.trim() : '';

                // Push employee data
                worksheetData.push([employeeName, effectiveDate, ""]); // Employee details
            });
        } else {
            // If no employees, indicate that with a message
            worksheetData.push(['No Employees', '', '']); // Message for no employees
        }

        // Add a blank row to separate categories
        worksheetData.push(["", "", ""]); // Blank row for spacing
    });

    // Create a worksheet
    const worksheet = XLSX.utils.aoa_to_sheet(worksheetData);
    XLSX.utils.book_append_sheet(workbook, worksheet, "Permissions Report");

    // Set column widths based on the maximum length of the content
    const columnWidths = [
        { wch: Math.max(...worksheetData.map(row => row[0].length)) + 2 }, // Category Name
        { wch: Math.max(...worksheetData.map(row => row[1].length)) + 2 }, // Employee Name
        { wch: Math.max(...worksheetData.map(row => row[2].length)) + 2 }  // Effective From
    ];
    worksheet['!cols'] = columnWidths;

    // Save the workbook
    XLSX.writeFile(workbook, "permissions_report.xlsx");
}






function downloadPermissionsReportAsPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('landscape');
            const margin = 10;
            const pageWidth = doc.internal.pageSize.getWidth();
            const startY = 20; // Starting Y position for the table

            // Set header title
            doc.setFontSize(16);
            doc.text("Permissions Report", margin, startY);

            const categories = document.querySelectorAll('.card');
            let currentY = startY + 10; // Update Y position for the table rows

            // Set column widths
            const categoryWidth = (pageWidth / 2) - margin; // Half of page width for category
            const employeeWidth = (pageWidth / 2) - margin; // Half of page width for employee names

            // Draw table header (skip this if you want to completely remove it)
            // doc.setFontSize(12);
            // doc.setFont("bold");
            // doc.text("Category Name", margin, currentY);
            // doc.text("Employee Names", margin + categoryWidth, currentY);
            // currentY += 10; // Space after header

            // Draw a horizontal line for the header
            // doc.line(margin, currentY, pageWidth - margin, currentY);
            // currentY += 5; // Space after line

            // Prepare data and draw rows, skipping the first row
            categories.forEach((item, index) => {
                // Skip the first index (0) row
                if (index === 0) return; 

                const categoryButton = item.querySelector('.card-header button');
                const categoryName = categoryButton ? categoryButton.innerText.trim() : '';

                // Collect employee names
                const employeeNames = Array.from(item.querySelectorAll('.card-body .employee-name'))
                                           .map(span => span.innerText.trim())
                                           .filter(name => name);
                
                const employeeNamesString = employeeNames.length > 0 ? employeeNames.join(', ') : 'No Employees';

                // Only process if there's data to display
                if (employeeNames.length > 0) {
                    // Draw category name
                    doc.setFont("normal");
                    doc.text(categoryName, margin, currentY);

                    // Split employee names into multiple lines if too long
                    const lines = doc.splitTextToSize(employeeNamesString, employeeWidth);
                    lines.forEach(line => {
                        doc.text(line, margin + categoryWidth, currentY);
                        currentY += 6; // Increment Y position for each line
                    });

                    currentY += 5; // Space between categories
                }
            });

            // Save the PDF
            doc.save('permissions_report.pdf');
        }


</script>
@stop
