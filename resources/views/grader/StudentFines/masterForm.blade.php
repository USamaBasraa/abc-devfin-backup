@extends('layouts.app',['pageTitle' => 'BNU Fines'])
@section('content')

<div class="container">
    <div class="card p-4">


        <form id="submitForm">
            @csrf
            <input type="hidden"  name="verifyEdit" id="verifyEdit" value="" >
            <input type="hidden"  name="id" id="id" value="" >
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="fineCategoryOption">Select Fine Category:</label>
                    <select name="fineCategoryOption" id="fineCategoryOption" class="form-control">
                        <option value="" selected disabled>--- Select Category ---</option>
                        @foreach ($fine_categories as $category)
                            <option value="{{ $category->id }}">{{ $category->fc_category_name }}</option>
                        @endforeach
                    </select>
                    <h6 data-error-for="fineCategoryOption" class="validation-error pt-4"></h6>
                </div>
                <div class="form-group col-md-6">
                    <label for="description">Violation Type:</label>
                    <input type="text" name="violationType" id="violationType" class="form-control" placeholder="Enter description" style="width: 100%;">
                    <h6 data-error-for="violationType" class="validation-error pt-4"></h6>
                </div>

            </div>

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="fineAmount">Enter Fine Amount:</label>
                    <input type="number" name="fineAmount" id="fineAmount" class="form-control" placeholder="Enter fine amount">
                    <h6 data-error-for="fineAmount" class="validation-error pt-4"></h6>
                </div>

                <!-- Add other form fields as needed -->
            </div>
            <div id="successMessage" class="text-success mt-2 mb-4"></div>
            {{-- <h2 id="successMessage" class="text-success"></h2> --}}
            <div class="d-flex justify-content-center">
                <button type="button" onclick='submitForm()' class="btn btn-primary">Submit</button>
                <button type="button" onclick="clearForm()" style="margin-left:10px;" class="btn btn-info">Clear</button>
            </div>

        </form>


        <div id="dataDiv"   style="margin-top:20px;">

        </div>



    </div>








</div>



@stop
@section('javascript')
<script>

$(document).on('click', '.edit-category', function(event) {
    const buttonElement = this; // or event.currentTarget
    const categorypDataString = buttonElement.dataset.category;

    try {
        const DataString = JSON.parse(categorypDataString);
        console.log(DataString);

        $('#fineCategoryOption').val(DataString.id);
        $('#violationType').val(DataString.fcd_violation_type);
        $('#fineAmount').val(DataString.fcd_fine_amount);
        $('#verifyEdit').val('1');
        $('#id').val(DataString.fcd_id);

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

    } catch (error) {
        console.error("Parsing Error:", error);
    }
});



function submitForm() {
    let form = document.getElementById('submitForm');
    let data = new FormData(form);
    let successMsg = document.getElementById('successMessage');

    fetch(`/submit-student-fine`, {
        method: 'POST', // Using POST method
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            form.reset(); // Reset the form
            document.getElementById('verifyEdit').value = '';
            document.getElementById('id').value = '';

            const alertHTML = `
                <div class="alert alert-success alert-dismissible fade show mb-2" role="alert">
                    <i class="fa fa-check-circle"></i> <strong>Success!</strong> ${data.message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            successMsg.innerHTML = alertHTML;


            // Automatically hide the alert after a few seconds
            setTimeout(() => {
                successMsg.innerHTML = '';
            }, 3000);

            makeDataHtml(data.challanRec);
        } else {
            displayValidationErrors(data.errors);
            console.error('Error:', data.errors);
        }
    })
    .catch((error) => {
        displayValidationErrors(error);
        console.error('Error:', error);
    });
}





function makeDataHtml(data) {
    let showWarningLetter = document.getElementById('dataDiv');
    showWarningLetter.innerHTML = ''; // Clear previous content

    if (data.length > 0) {
        let lettersHtml = `
        <div class="table-responsive table-striped table-bordered" style="max-height: 800px; overflow-y: auto;">
            <table id="dataTable" class="table">
                <thead class="thead">
                    <tr style="font-size: 20px; font-weight: bold;">
                        <th scope="col">Sr#</th>
                        <th scope="col">Category</th>
                        <th scope="col">Fine Type</th>
                        <th scope="col">Fine Amount</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody style="font-size: 18px;">
                    ${data.map((element, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${element.fc_category_name}</td>
                            <td>${element.fcd_violation_type}</td>
                            <td>${element.fcd_fine_amount}</td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-outline-primary mx-2 edit-category" data-category='${JSON.stringify(element)}'>
                                        <i class="fa fa-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-outline-danger mx-2 delete-category" data-category='${JSON.stringify(element)}' onclick="deleteRows('${element.fcd_id}')">
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
        // dom: 'Blfrtip',
        dom:
        // First row: Buttons + Length menu + Filter
       '<"row mb-2"' +
            '<"col-md-6 d-flex"<"me-3"B><"ml-3"l>>' +   // � spacing between Buttons and Length menu
            '<"col-md-6 d-flex justify-content-end"f>' +
        '>' +
        // Second row: Table
        '<"row"<"col-12"tr>>' +
        // Third row: Info + Pagination
        '<"row mt-2"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o mr-1"></i>Excel',
                title: 'Excel Export ',
                exportOptions: {
                    columns: ':not(:last-child)'
                },
                className: 'ml-2 btn btn-primary'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf-o mr-1"></i>PDF',
                title: 'PDF Export',
                exportOptions: {
                    columns: ':not(:last-child)'
                },
                 className: 'ml-2 btn btn-primary'
            }
        ]
    });


    } else {
        // Optionally display a message when there is no data
        showWarningLetter.innerHTML = '<p>No data available.</p>';
    }
}

function deleteRows(mainId){
    if (confirm("Are you sure you want to delete this record?")) {
        $.ajax({
            type: 'POST',
            url: '/delete-student-fine',
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
    fetch(`/get-fine-data`, {
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


    function clearForm() {
    // Reset the form with the ID "submitForm"
    document.getElementById('submitForm').reset();
}


</script>
@stop
