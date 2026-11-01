@extends('layouts.app')
@section('content')
<style type="text/css">
.canvasjs-chart-canvas{
    color:white !important;
}
</style>
<div class="card">     
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-family:'Times New Roman', Times, serif;">Gym Summary Report</h3>
       
        <!-- <button type="button" class="btn btn-info" onclick="viewPDF()">View Summary</button> -->
    </div>
</div>    

    <div class="card" style="margin-top:10px;">    
        <!-- <button class="btn primary" onclick="downloadExcel2();" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> GET Excel</button> -->
        <div class="card-body">

            <table class="table table-sm  table-bordered">
                <thead>
                    <tr>
                        <th style="text-align:center;font-weight:bold;" scope="col">User Type</th>                      
                        <th style="text-align:center;font-weight:bold;" scope="col">Total</th>
                        <th style="text-align:center;font-weight:bold;" scope="col">Paid</th>
                        <th style="text-align:center;font-weight:bold;" scope="col">Unpaid</th>
                          
                    </tr>
                </thead>
                <tbody id="reportBody"></tbody>
            </table>
        </div>
    </div>  
    
     <div class="modal fade" id="scholarshipModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Scholarship Applicants Detail</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <table class="table table-bordered" id="scholarshipTable">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Enrollment No</th>
                                <th>AAI ID</th>
                                <th>Scholarship List</th>
                                <th>Subscription Status</th>
                                <th>Challan Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="sportsModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Sports Applicants Detail</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <table class="table table-bordered" id="sportsTable">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Enrollment No</th>
                                <th>Sports</th>
                                <th>Cahlan Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@stop
@section('javascript')
<script>

    $(document).ready(function () {
        // alert('123');
       
        // AJAX request runs automatically on page load
        $.ajax({
            url: '/update-status-gym', // your backend route
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'  // required in Laravel
            },
            success: function (response) {
                 getReport();
                console.log('Update successfully executed:', response);
            },
            error: function (xhr, status, error) {
                console.error('Error during update:', error);
            }
        });

    });

    function getReport(){

        $.ajax({
            url: '/get-report-gym',
            type: 'GET',
            success: function(res) {
                // paste logic here
                let lastMonth = null;
                let sumTotal = 0;
                let sumPaid = 0;
                let sumUnpaid = 0;

                $('#reportBody').empty(); // Clear existing rows

                res.data.forEach(function(row, index) {
                    // Check if month changed
                    if(row.subscription_month !== lastMonth) {

                        // Print totals for previous month
                        if(lastMonth !== null) {
                            $('#reportBody').append(`
                                <tr>
                                    <td style="text-align:center; font-weight:bold;">Total</td>
                                    <td style="text-align:center; font-weight:bold;">${sumTotal}</td>
                                    <td style="text-align:center; font-weight:bold;">${sumPaid}</td>
                                    <td style="text-align:center; font-weight:bold;">${sumUnpaid}</td>
                                </tr>
                            `);
                        }

                        // New Month Header
                        $('#reportBody').append(`
                            <tr style="background-color:bisque">
                                <td colspan="4" style="text-align:center; font-weight:bold;">
                                    ${row.subscription_month}
                                </td>
                            </tr>
                        `);

                        // Reset month totals
                        sumTotal = 0;
                        sumPaid = 0;
                        sumUnpaid = 0;
                        lastMonth = row.subscription_month;
                    }

                    // Add totals
                    sumTotal += parseInt(row.total_count);
                    sumPaid += parseInt(row.paid_count);
                    sumUnpaid += parseInt(row.unpaid_count);

                    // Append row
                    $('#reportBody').append(`
                        <tr>
                            <td>${row.label}</td>
                            <td 
                                style="text-align:center; 
                                    ${(row.label === 'Total Scholarship Applicants' || row.label === 'Total Sports Applicants') 
                                        ? 'cursor:pointer;color:blue;text-decoration:underline;' 
                                        : ''}"

                                ${(row.label === 'Total Scholarship Applicants') 
                                    ? `onclick="openDetails('scholarship', '${row.subscription_month}')"`
                                    : (row.label === 'Total Sports Applicants'
                                        ? `onclick="openDetails('sports', '${row.subscription_month}')"`
                                        : ''
                                    )}>

                                ${row.total_count}
                            </td>
                            <td style="text-align:center;">${row.paid_count}</td>
                            <td style="text-align:center;">${row.unpaid_count}</td>
                        </tr>
                    `);
                });

                // Final total row
                $('#reportBody').append(`
                    <tr>
                        <td style="text-align:center; font-weight:bold;">Total</td>
                        <td style="text-align:center; font-weight:bold;">${sumTotal}</td>
                        <td style="text-align:center; font-weight:bold;">${sumPaid}</td>
                        <td style="text-align:center; font-weight:bold;">${sumUnpaid}</td>
                    </tr>
                `);
            }
        });


    }

    function openDetails(type, month) {

        // Clear previous data
        $("#scholarshipTable tbody").empty();
        $("#sportsTable tbody").empty();

        // Open modal according to type
        if (type === "scholarship") { 
            $("#scholarshipModal").modal('show');
            $("#scholarshipTable tbody").html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
        } 
        else if (type === "sports") {
            $("#sportsModal").modal('show');
            $("#sportsTable tbody").html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');
        }

        $.ajax({
            url: "/get-applicants-details",
            type: "GET",
            data: {
                type: type,
                month: month
            },
            success: function(res) {

                let rows = "";
                let sr = 1;
                if (type === "scholarship") {

                    res.forEach(item => {
                        rows += `
                            <tr>
                                <td>${sr++}</td>      
                                <td>${item.sa_enrolment_no}</td>
                                <td>${item.aai_id}</td>
                                <td>${item.scholarship_list}</td>
                                <td>${item.subscription_status}</td>
                                <td>${item.challan_status}</td>
                            </tr>`;
                    });

                    $("#scholarshipTable tbody").html(rows);
                }

                if (type === "sports") {

                    res.forEach(item => {
                        rows += `
                            <tr>
                                <td>${sr++}</td>        
                                <td>${item.user_id}</td>
                                <td>${item.sa_sports_category}</td>
                                <td>${item.challan_status}</td>
                            </tr>`;
                    });

                    $("#sportsTable tbody").html(rows);
                }
            }
        });
    }


    function viewPDF(){
       
        url = 'http://devstaff.bnu.edu.pk/HOD/gym_report.php?';
        window.open(url, '_blank');
    }

   


</script>
@stop