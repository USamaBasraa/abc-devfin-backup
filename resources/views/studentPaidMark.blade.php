@extends('layouts.app')
@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">


</head>
<style>
    .custom-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }
    .custom-table th, .custom-table td {
        padding: 10px; /* Add padding to all table cells */
        text-align: center; /* Center align the text */
        border: 1px solid #ddd; /* Optional: Add border to cells */
    }
    .custom-table th {
        background-color: #f2f2f2; /* Optional: Background color for header */
        font-weight: bold; /* Make header text bold */
    }
    .custom-table thead th {
        letter-spacing: 2px; /* Increase space between letters in headers */
    }

    .custom-td {
        padding: 8px;
        border: 1px solid #ddd;
        word-wrap: break-word;
        white-space: normal;
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

    </style>
<div id="error-flag" class="error-flag"></div>
<div id="success-flag" class="success-flag"></div>
<div class="container-fluid">
    <div class="card p-4 pt-4">
        <table class="custom-table" id="challanTableBody">
            <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 10px; text-align: center;">
                {{-- <div style="flex: 1;"></div> <!-- First column (empty) --> --}}
                <div style="flex: 1; font-weight: bold;"><h3>Manual Paid Mark</h3></div> <!-- Second column -->
            </div>
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th style="width: 100px;">Challan No.</th>
                    <th>Student Name</th>
                    <th style="width: 90px;">Challan Amount</th>
                    <th style="width: 90px;">Amount Payable</th>
                    <th>Due Date</th>
                    <th>Valid Date</th>
                    <th style="width: 120px;">Amount Paid</th>
                    <th>Fine</th>
                    <th>Mode of Payment</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="custom-td srNo">1</td>
                    <td class="custom-td challanNo">
                        <input class="form-control input_challanNo" type="text" value="" name="challanNO" onblur="searchStudent(this);" onkeypress="handleEnterPress(event, this)">
                    </td>
                    <td class="custom-td student_name">-</td>
                    <td class="custom-td challan_amount">-</td>
                    <td class="custom-td amount_payable">-</td>
                    <td class="custom-td due_date">-</td>
                    <td class="custom-td valid_date">-</td>
                    <td class="custom-td amount_paid">
                        <input type="number" min='0' max='9999999' name="amount" maxlength="7" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 7);" onblur="sumTotalAndFineCal(this);" onkeypress="enterPressTotal(event, this)"class="form-control input_amount_paid">
                    </td>
                    <td class="custom-td fine" actualFine="0">0</td>
                    <td class="custom-td mode">
                        <select name="payment_mode" class="form-control payment_mode_select">
                            <option value="Cash payment at Bank">Cash payment at Bank</option>
                            <option value="PO\DD">PO\DD</option>
                            <option value="Credit Card">Credit Card</option>
                        </select>
                    </td> <!-- Select for Mode of Payment -->
                </tr>
                <tr id="addRow"> <!-- Green background -->
                    <td colspan="1" style="text-align: center; background-color: #d4edda;">
                        <button type="button" onclick="addNewRow()" style="border: none; background: none; font-size: 17px; color: #28a745; cursor: pointer;">
                            <i class="fa fa-plus"></i> <!-- Font Awesome plus icon -->
                        </button>
                    </td>
                    <td colspan="6"></td>
                    <td class="custom-td amount_paid_total" style="text-align: center;">
                        0
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
            {{-- <tfoot>
                <tr>
                    <td colspan="7"></td> <!-- Empty cells to align with the "Amount Paid" column -->
                    <td class="custom-td amount_paid_text" style="text-align: center;">
                        Your Text Here
                    </td>
                    <td colspan="2"></td> <!-- Empty cells after the text -->
                </tr>
            </tfoot> --}}
        </table>
        <div class="form-group col-md-12" style="margin-top:10px; display: flex; justify-content: center;">
            <button type="button" style="width:150px;" onclick="submitFineFormBtn()" class="btn btn-primary">Save</button>
        </div>
        <hr style="border-top: 1px solid #ccc; margin-top: 20px;">

        <div id="filterdiv" class="row" style="margin-right:10px;margin-top:20px;margin-bottom:20px;">
            <div class="form-group col-sm-3">
                <label for="studentType">From Date</label>
            <input type="date" name="due_date" id="from-date"  class="form-control">
            </div>
            <div class="form-group col-sm-3">
                <label for="studentType">To Date</label>
            <input type="date" name="valid_date" id="to-date"  class="form-control">
            </div>
            <div class="form-group col-sm-3">
                <label for="modeOFPayment"> Mode of payment:</label>
                <select name="mode" id="mode" class="form-control">
                    <option value="">All</option>
                    <option value="Cash payment at Bank">Cash payment at Bank</option>
                    <option value="PO\DD">PO\DD</option>
                    <option value="Credit Card">Credit Card</option>
                </select>
            </div>
            <div class="form-group col-sm-3 d-flex justify-content-center align-items-center" style="margin-top:24px;">
                <button onclick='filterPaidMark()' type="button" class="btn btn-info"><i class="fa fa-filter"></i>Apply Filter</button>
                {{-- <button class="btn btn-primary ml-2" onclick="exportToPDF('exportTable')">
                    <i class="fa fa-file-pdf-o"></i> Export to PDF
                </button> --}}
                <button class="btn btn-primary ml-2" onclick="exportToExcel('exportTable')">
                    <i class="fa fa-file-excel-o"></i> Export to Excel
                </button>
            </div>
        </div>
        <hr style="border-top: 1px solid #ccc; margin-top: 20px;">
        <div class="row" style="margin:10px;">
            <div class="col-lg-12 col-md-12">
                {{-- <div class="card"> --}}
                    <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                        <table id="data-table" class="table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Challan No.</th>
                                    <th>Enrollment No.</th>
                                    <th>Name</th>
                                    <th>Challan Type</th>
                                    <th>Paid Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Paid On</th>
                                    <th>Challan Status</th>
                                    <th>Valid Date</th>
                                </tr>
                            </thead>
                          </table>
                    </div>
                {{-- </div> --}}
            </div>
        </div>


        <div id="dataDiv"   style="margin-top:20px;">
        </div>



            </div>
            {{-- <h2 id="successMessage" class="text-success"></h2> --}}
            {{-- <button type="button" onclick='submitForm()' class="btn btn-primary">Submit</button> --}}

            </div>
    </div>
</div>


@stop
@section('javascript')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<!-- Include html2canvas library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>




<script>
$(document).ready(function() {
    $('.custom-table').on('click', '.delete-row', function() {
        $(this).closest('tr').remove();

        // Reordering
        $('#challanTableBody tbody tr').each(function(index) {
            $(this).find('.srNo').text(index + 1);
            if ($('#challanTableBody tbody tr').length > 2) {//restricting placing cross on last remaing tr
                $(this).find('.srNo').append('<span class="delete-row" style="cursor: pointer; color: red; float: left; margin-right: 5px;">&times;</span>');
            }
        });
        sumTotal();
    });
});
function handleEnterPress(event, element) {
    console.log(event);
    if (event.key === 'Enter') {
        event.preventDefault(); // Prevent form submission if it's inside a form
        searchStudent(element);
    }
}
function enterPressTotal(event, element) {
    if (event.key === 'Enter') {
        event.preventDefault(); // Prevent form submission if it's inside a form
        sumTotal();
    }
}
function searchStudent(element){
    // console.log($(element).val());
    let challanNo = $(element).val()
    if(challanNo != ''){
        $.ajax({
            url: '/get-student-fee-rec',
            type: 'POST',
            data: {
                    _token: '{{ csrf_token() }}',
                    challanNo: challanNo
                },
            success: function(response) {
                if (response.success) {
                    // console.log(response.student_challan);
                    if(response.student_challan == ''){
                        $('#error-flag').text('No Data Found!');
                        $('#error-flag').addClass('show');
                        setTimeout(function() {
                            $('#error-flag').removeClass('show');
                        }, 3000); // Hide after 3 seconds
                    }else{
                        populateData(response.student_challan, element);
                        // makeDataHtml(response.student_challan);
                    }

                }
            },
            error: function(xhr, status, error) {
                console.error('An error occurred:', error);
                // Handle the error here, e.g., display an error message
            }
        });
    }
}
async function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p', 'pt', 'a4');

    const facultyReport = document.getElementById('studentChallanStatusTable');
    const clone = facultyReport.cloneNode(true);

    // Remove the button from the clone
    const button = clone.querySelector('button');
    if (button) {
        button.remove();
    }

    // Hide the cloned element (will be used for rendering only)
    clone.style.position = 'absolute';
    clone.style.left = '-9999px';
    document.body.appendChild(clone);

    await html2canvas(clone, { scale: 2 }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const imgWidth = pdf.internal.pageSize.getWidth();
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
    });

    document.body.removeChild(clone);

    pdf.save('student-challan-status.pdf');
}
function exportToExcel() {
    const table = document.getElementById('studentChallanStatusTable');
    const worksheet = XLSX.utils.table_to_sheet(table);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1');

    XLSX.writeFile(workbook, 'student-challan-status.xlsx');
}
function submitFineFormBtn(){
    // let form = document.getElementById('submitFineForm');
    let data = getChallansArray();
    console.log(data);
    let dataDiv = document.getElementById('dataDiv');
    // let data = new FormData(form);
    let successMsg=document.getElementById('successMessage');
    $.ajax({
        url: '/paid-student-fee-challan', // URL to send the request to
        type: 'POST', // Use POST method
        data: data, // Data to send (this should be the JSON stringified data)
        contentType: 'application/json', // Set content type to JSON
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}' // CSRF token for security
        },
        success: function(response) {
            if (response.success) {
                // form.reset(); // Reset the form on success
                console.log('Success:', response.message);
                // successMsg.innerHTML = response.message;
                $('#success-flag').text(response.message);
                $('#success-flag').addClass('show');
                setTimeout(function() {
                    $('#success-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds
                refreshTable();
                filterPaidMark();
            } else {
                $('#error-flag').text(response.message);
                $('#error-flag').addClass('show');
                setTimeout(function() {
                    $('#error-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds
                console.error('Error:', response.message);
            }
        },
        error: function(xhr, status, error) {
            displayValidationErrors(xhr.responseJSON.errors); // Handle AJAX errors
            console.error('Error:', error);
        }
    });

}
function generateFeeChallanHTML(challan) {

    // console.log(typeof challan);
    if (challan) {

                const tableRows = `
                    <tr>
                        <td>${challan.buyercodechallanno}</td>
                        <td>${challan.regno}</td>
                        <td>${challan.spi_name}</td>
                        <td>${challan.feetypesummary?challan.feetypesummary:'N/A'}</td>
                        <td>${challan.paidamt?challan.paidamt:'N/A'}</td>
                        <td>${challan.paymentmode?challan.paymentmode:'N/A'}</td>
                        <td>${challan.paidon?challan.paidon:'N/A'}</td>

                        <td>
                            <span class="badge ${challan.challanstatus === 'UNPAID' ? 'badge-danger' : 'badge-success'}">
                                ${challan.challanstatus === 'UNPAID' ? 'Unpaid' : 'Paid'}
                            </span>
                        </td>
                        <td>${challan.validdate?new Date(challan.validdate).toLocaleString():'N/A'}</td>
                    </tr>
                `;

                return `
                    <div id='studentChallanStatusTable' class='table-responsive mt-3'>
                        <table class='table'>
                            <thead>
                                <tr>
                                    <th>Challan No.</th>
                                    <th>Enrollment No.</th>
                                    <th>Name</th>
                                    <th>Challan Type</th>
                                    <th>Paid Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Paid On</th>
                                    <th>Challan Status</th>
                                    <th>Valid Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                `;
    } else {
        return '';
    }
}
function loadFilteredFeeChallanHTML(challan) {
    if (challan) {
        let tableRows = '';
        challan.forEach((item, index) => {
            tableRows += `
                <tr>
                    <td>${item.buyercodechallanno}</td>
                    <td>${item.regno}</td>
                    <td>${item.spi_name}</td>
                    <td>${item.feetypesummary?item.feetypesummary:'N/A'}</td>
                    <td>${item.paidamt?item.paidamt:'N/A'}</td>
                    <td>${item.paymentmode?item.paymentmode:'N/A'}</td>
                    <td>${item.paidon?item.paidon:'N/A'}</td>

                    <td>
                        <span class="badge ${item.challanstatus === 'UNPAID' ? 'badge-danger' : 'badge-success'}">
                            ${item.challanstatus === 'UNPAID' ? 'Unpaid' : 'Paid'}
                        </span>
                    </td>
                    <td>${item.validdate?new Date(item.validdate).toLocaleString():'N/A'}</td>
                </tr>
            `;
        });
                return `
                    <div id='studentChallanStatusTable' class='table-responsive mt-3'>
                        <table class='table'>
                            <thead>
                                <tr>
                                    <th>Challan No.</th>
                                    <th>Enrollment No.</th>
                                    <th>Name</th>
                                    <th>Challan Type</th>
                                    <th>Paid Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Paid On</th>
                                    <th>Challan Status</th>
                                    <th>Valid Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                `;
    } else {
        return '';
    }
}
function filterPaidMark() {
    let fromData = $('#from-date').val();
    let toData = $('#to-date').val();
    let modeOfPayment = $('#mode').val();
    // $.ajax({
    //         type: "GET",
    //         url: "/filter-paid-mark",
    //         data: {
    //             fromData: fromData,
    //             toData: toData,
    //             modeOfPayment: modeOfPayment
    //         },
    //         success: function(response) {
    //             // console.log(response.student_challan);
    //             let filteredResultHtml = loadFilteredFeeChallanHTML(response.student_challan);
    //             let tablDiv = document.getElementById('dataDiv');
    //             tablDiv.innerHTML = ''; // Clear previous content

    //             if(response.student_challan){
    //                 tablDiv.innerHTML = filteredResultHtml;
    //             } else {
    //                 // Optionally display a message when there is no data
    //                 tablDiv.innerHTML = '<p style="color:red;">No data available.</p>';
    //             }
    //         },
    //         error: function(error) {
    //             // Handle the error
    //             console.error("Error:", error);
    //         }
    // });
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
                method: 'get',
                url:  "/filter-paid-mark",
                data: function(d) {
                    d.fromData = fromData;
                    d.toData = toData;
                    d.modeOfPayment = modeOfPayment;
                }
            },
            columns: [
                { data: "buyercodechallanno", name: "buyercodechallanno" },
                {
                    data: "regno", name: "regno",
                    render: function(data, type, row) {
                        return data ? data : 'NA'; // Check if data is empty or null, if so, display 'NA'
                    }
                },
                { data: "spi_name", name: "spi_name" },
                {
                    data: "feetypesummary", name: "feetypesummary",
                    render: function(data, type, row) {
                        return data ? data : 'NA';
                    }
                },
                {
                    data: "paidamt", name: "paidamt",
                    render: function(data, type, row) {
                        return data ? data : 'NA';
                    }
                },
                {
                    data: "paymentmode", name: "paymentmode",
                    render: function(data, type, row) {
                        return data ? data : 'NA';
                    }
                },
                {
                    data: "paidon", name: "paidon",
                    render: function(data, type, row) {
                        return data ? data : 'NA';
                    }
                },
                {
                    data: "challanstatus", name: "challanstatus",
                    render: function(data, type, row) {
                        if (data === 'UNPAID') {
                            return `<span class="badge badge-danger">${data}</span>`;
                        }else {
                            return `<span class="badge badge-success">PAID</span>`;
                        }
                    }

                },
                {
                    data: "validdate", name: "validdate",
                    render: function(data, type, row) {
                        if (data) {
                             // Convert to Date object
                            const date = new Date(data);
                            const datePart = data.split(' ')[0]; // Extract the 'YYYY-MM-DD' part
                            // Format the date to MM/DD/YYYY


                            return datePart;
                        } else {
                            // Return 'NA' if data is null or empty
                            return 'NA';
                        }
                    }
                }

            ],
            // columns: [
            //     { data: "buyercodechallanno", name: "buyercodechallanno" },
            //     {
            //         data: "regno", name: "regno",
            //         render: function(data, type, row) {
            //             return data ? data : 'NA'; // Check if data is empty or null, if so, display 'NA'
            //         }
            //     },
            //     { data: "spi_name", name: "spi_name" },
            //     {
            //         data: "feetypesummary", name: "feetypesummary",
            //         render: function(data, type, row) {
            //             return data ? data : 'NA';
            //         }
            //     },
            //     {
            //         data: "paidamt", name: "paidamt",
            //         render: function(data, type, row) {
            //             return data ? data : 'NA';
            //         }
            //     },
            //     {
            //         data: "paymentmode", name: "paymentmode",
            //         render: function(data, type, row) {
            //             return data ? data : 'NA';
            //         }
            //     },
            //     {
            //         data: "paidon", name: "paidon",
            //         render: function(data, type, row) {
            //             return data ? data : 'NA';
            //         }
            //     },
            //     {
            //         data: "challanstatus", name: "challanstatus",
            //         render: function(data, type, row) {
            //             if (data === 'UNPAID') {
            //                 return `<span class="badge badge-danger">${data}</span>`;
            //             }else {
            //                 return `<span class="badge badge-success">PAID</span>`;
            //             }
            //         }

            //     },
            //     {
            //         data: "validdate", name: "validdate",
            //         render: function(data, type, row) {
            //             if (data) {
            //                 // Convert to Date object and format as a localized string
            //                 return new Date(data).toLocaleString();
            //             } else {
            //                 // Return 'NA' if data is null or empty
            //                 return 'NA';
            //             }
            //         }
            //     }
            // ]
            columnDefs: [
                // {
                //     render: function (data, type, row) {
                //         console.log(row);
                //         var button = '<div><button class="btn btn-primary btn-sm edit-btn" style="margin-top:16px;" id="'+row["ffs_id"]+'" onclick="editRow(`'+row["ffs_id"]+'`,`'+row["ffs_sess_id"]+'`,`'+row["ffs_school_id"]+'`,`'+row["ffs_dept_id"]+'`,`'+row["ffs_prog_id"]+'`,`'+row["ffs_student_type"]+'`,`'+row["ffs_fee_type"]+'`,`'+row["ffs_eff_from"]+'`,`'+row["ffs_eff_to"]+'`,`'+row["ffs_tutionfee_amt"]+'`,`'+row["ffs_admission_amt"]+'`,`'+row["ffs_security_amt"]+'`,`'+row["ffs_medical_amt"]+'`,`'+row["ffs_credhr_amt"]+'`,`'+row["ffs_activity_amt"]+'`,`'+row["ffs_degree_level"]+'`)"><i class="fa fa-edit"></i> Edit</button>'+
                //                                     '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
                //                                 '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows(['+row["ffs_id"]+'])"><i class="fa fa-trash"></i> Delete</button></div>';
                //         return button;

                //     },
                //     // className: 'dt-body-center',
                //     // className: 'dt-head-center',
                //     targets: 12,
                // },
                // {
                //     render: function (data, type, row) {
                //             var   selectbtn = '<div style="margin-top:40px;"><input name="fee_ids[]" class="select-data" idData='+row["ffs_id"]+' value='+row["ffs_id"]+' type="checkbox" ></div>';

                //         return selectbtn;

                //     },
                //     // className: 'dt-body-center',
                //     // className: 'dt-head-center',
                //     targets: 13,
                //     searchable: false,
                //     orderable: false
                // },

                // {
                //     "width": "100px", "targets": 1
                // },
                // {
                //     "width": "5px", "targets": 13
                // },
            ],
            autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Paid Mark',
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
                    title: 'Paid Mark',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }

                },
                // {
                // text: 'Export All Data (PDF)', // Custom button text
                //     action: function(e, dt, button, config) {
                //         // Call your custom function
                //         pdfExportFunction(dt, config);
                //     }
                // },


                {
                    extend: 'print',
                    title: 'Paid Mark',
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
function populateData(challanData, element){
    console.log(challanData);
    const $td = $(element).closest('td');//getting the current td element
    const $tr = $td.parent('tr'); // getting the parent <tr> element of the current <td>
    // console.log($tr.attr('id'));
    $tr.attr('id', challanData.buyercodechallanno); // Set challannumber as id to the <tr> element for uniquness
    $tr.attr('enrollment_no', challanData.regno);
    $tr.attr('aai_id', challanData.applicant_id);
    $tr.attr('sess_id', challanData.sess_id);
    $td.siblings('.student_name').text(challanData.spi_name ? challanData.spi_name : 'NA');
    $td.siblings('.challan_amount').text(challanData.payableamount ? challanData.payableamount : 'NA');

    let fineAmount = parseFloat(challanData.fine) || 0;
    let payableAmount = parseFloat(challanData.payableamount) || 0;
    let totalAmountPayable = fineAmount + payableAmount;

    $td.siblings('.amount_payable').text(totalAmountPayable ? totalAmountPayable : 'NA');
    $td.siblings('.due_date').text(challanData.duedate ? challanData.duedate.split(' ')[0] : 'NA');
    $td.siblings('.valid_date').text(challanData.validdate ? challanData.validdate.split(' ')[0] : 'NA');
    $td.siblings('.fine').text(challanData.fine ? challanData.fine : 0);
    $td.siblings('.fine').attr('actualFine',challanData.fine ? challanData.fine : 0);
    sumTotal();
}
function addNewRow() {
    // Clone the last row in the table (excluding the add row button)
    let $newRow = $('#challanTableBody tr').eq(1).clone();
    console.log($newRow);
    // Update the serial number
    let rowCount = $('#challanTableBody tr').length - 1; // excluding the add row button
    $newRow.find('.srNo').html('<span class="delete-row" style="cursor: pointer; color: red; float: left;">&times;</span>'+rowCount);
    // Clear input values
    $newRow.attr('id', '');
    $newRow.attr('enrollment_no', '');
    $newRow.attr('aai_id', '');
    $newRow.attr('sess_id', '');
    $newRow.find('.input_challanNo').val('');
    $newRow.find('.payment_mode_select').val('Cash payment at Bank');
    $newRow.find('.student_name').text('-');
    $newRow.find('.challan_amount').text('-');
    $newRow.find('.amount_payable').text('-');
    $newRow.find('.due_date').text('-');
    $newRow.find('.valid_date').text('-');
    $newRow.find('.input_amount_paid').val('');
    $newRow.find('.fine').text(0);
    $newRow.find('.fine').attr('actualFine', 0);
    // Append the new row before the addRow button row
    $newRow.insertBefore('#addRow');
}
function getChallansArray(){
    var data = [];
    var invalid = false;
    $('#challanTableBody tr').each(function() {
        // Get the attributes of the 'tr'
        if($(this).attr('id') != 'addRow' && $(this).attr('id') != '' && $(this).attr('id') != undefined){
            var challanNo = $(this).attr('id');
            var enrollmentNo = $(this).attr('enrollment_no');
            var aaiId = $(this).attr('aai_id');
            var sessId = $(this).attr('sess_id');
            var srNo = $(this).find('td').find('.srNo').text();
            var amountPaid = $(this).find('td').find('.input_amount_paid').val();
            var paymentMode = $(this).find('td').find('.payment_mode_select').val();
            invalid = validateReuired(amountPaid, 'Amount Paid', challanNo);
            if(invalid){
                return false;
            }
            invalid = validateReuired(paymentMode, 'Mode of Payment', challanNo);
            if(invalid){
                return false;
            }
            // Create an object for each row and push it to the data array
            data.push({
                    challanNo: challanNo,
                    enrollmentNo: enrollmentNo,
                    aaiId: aaiId,
                    sessId: sessId,
                    amountPaid: amountPaid,
                    paymentMode: paymentMode
                });
            // console.log("TR Attributes - ID:", challanNo, "Enrollment No:", enrollmentNo, "AAI ID:", aaiId, "Session ID:", sessId, 'amountPaid: ',amountPaid,  'paymentMode: ',paymentMode);
        }
    });
    // console.log(data);
    if (invalid) {
        return false; // In case invalid is true, function returns false
    }else{
        return jsonData = JSON.stringify(data);
    }

}
function validateReuired(fieldData, fieldName, challanNo){
    if (fieldData == "" || fieldData == undefined || fieldData == null) {
        alert(fieldName+" is required for Challan No: " + challanNo);
        // $('#error-flag').text(fieldName+" is required for Challan No: " + challanNo);
        //         $('#error-flag').addClass('show');
        //         setTimeout(function() {
        //             $('#error-flag').removeClass('show');
        //         }, 3000); // Hide after 3 seconds
        return true; // Stops the iteration if fieldData is empty
    }else{
        return false;
    }
}
function refreshTable(){
    let table_body=`<tr>
                    <td class="custom-td srNo">1</td>
                    <td class="custom-td challanNo">
                        <input class="form-control input_challanNo" type="text" value="" name="challanNO" onblur="searchStudent(this);" onkeypress="handleEnterPress(event, this)">
                    </td>
                    <td class="custom-td student_name">-</td>
                    <td class="custom-td challan_amount">-</td>
                    <td class="custom-td amount_payable">-</td>
                    <td class="custom-td due_date">-</td>
                    <td class="custom-td valid_date">-</td>
                    <td class="custom-td amount_paid">
                         <input type="number" min='0' max='9999999' name="amount" maxlength="7" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 7);" onblur="sumTotalAndFineCal(this);" onkeypress="enterPressTotal(event, this)"class="form-control input_amount_paid">
                    </td>
                    <td class="custom-td fine" actualFine="0">0</td>
                    <td class="custom-td mode">
                        <select name="payment_mode" class="form-control payment_mode_select">
                            <option value="Cash payment at Bank">Cash payment at Bank</option>
                            <option value="PO\\DD">PO\\DD</option>
                            <option value="Credit Card">Credit Card</option>
                        </select>
                    </td> <!-- Select for Mode of Payment -->
                </tr>
                <tr id="addRow" style="background-color: #d4edda;"> <!-- Green background -->
                    <td colspan="1" style="text-align: center;">
                        <button type="button" onclick="addNewRow()" style="border: none; background: none; font-size: 17px; color: #28a745; cursor: pointer;">
                            <i class="fa fa-plus"></i> <!-- Font Awesome plus icon -->
                        </button>
                    </td>
                </tr>`;
    $('#challanTableBody tbody').html(table_body);
}
function sumTotal(){
    var grandtotal = 0;
    $('#challanTableBody tr').each(function() {
        // Get the attributes of the 'tr'
        if($(this).attr('id') != 'addRow' && $(this).attr('id') != '' && $(this).attr('id') != undefined){
            total = $(this).find('td').find('.input_amount_paid').val();
            if(total != '' && total != undefined){
                grandtotal = grandtotal + parseInt(total);
                // console.log(total);
            }
        }
    });
    $('.amount_paid_total').text('Total: '+grandtotal);
}
function sumTotalAndFineCal(element){
    //this below code is same as sumTotal (we have this function call onblur of the amountPaid field because we need the total to update and also to calculate the fine amopunt and the sumTotal function is only adding the total of amount payable and not checking the fine amount)
    var grandtotal = 0;
    $('#challanTableBody tr').each(function() {
        // Get the attributes of the 'tr'
        if($(this).attr('id') != 'addRow' && $(this).attr('id') != '' && $(this).attr('id') != undefined){
            total = $(this).find('td').find('.input_amount_paid').val();
            if(total != '' && total != undefined){
                grandtotal = grandtotal + parseInt(total);
                // console.log(total);
            }
        }
    });
    $('.amount_paid_total').text('Total: '+grandtotal);
    ////until this
   let amountPaid = parseInt($(element).val());
   let amountPayableTD = $(element).closest('tr').find('.amount_payable');
   let amountPayable = parseInt(amountPayableTD.text().trim());
   let fineTD = $(element).closest('tr').find('.fine');
   let fine = parseInt(fineTD.attr('actualFine').trim());
   let difference = 0;
   console.log('paid->'+amountPaid);
   console.log('payable->'+amountPayable);
   if(amountPaid > amountPayable){
        difference = amountPaid - amountPayable;
        // console.log('The paid amount exceeds the payable amount by: ' + difference);
   }else{
        difference = 0;
    }
        fine = fine + difference;
        fineTD.text(fine);
}

</script>
@stop
