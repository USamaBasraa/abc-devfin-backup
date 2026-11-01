@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentors List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            font-size: 16px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        button {
            padding: 8px 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        #mentor-details {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60%;
            padding: 20px;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            display: none;
        }

        #mentor-details h2 {
            margin-top: 0;
            color: #333;
        }

        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .mentor-detail-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .mentor-detail-table th,
        .mentor-detail-table td {
            padding: 10px;
            text-align: center;
        }

        .mentor-detail-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
<div class="card">
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-family:'Times New Roman', Times, serif;"> Blocked Students</h3>
        <button id="export-btn">Export to Excel</button>
       
    </div>
    <div class="card-body">   
        <!-- Mentor Details Popup -->
        
          
            <table class="mentor-detail-table">
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Student Name</th>
                        <th>Status</th>
                       
                    </tr>
                </thead>
                <tbody id="details-table">
                    <!-- Mentor details rows will be dynamically added here -->
                </tbody>
            </table>
            
            <!-- <button onclick="closeDetails()">Close</button> -->
        
    </div>
</div>
</body>

</html>
@endsection
@section('javascript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        document.getElementById("export-btn").addEventListener("click", function () {
            // Get the table
            const table = document.querySelector(".mentor-detail-table");

            // Convert table to workbook
            const workbook = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });

            // Export workbook to an Excel file
            XLSX.writeFile(workbook, "BlockedStudents.xlsx");
        });
        $(document).ready(function() {
            // Send an AJAX request to fetch the data
            getRequest();
        });
        // Show mentor details
        function getRequest() {
            //alert('123');
            //return false;
            fetch(`/get-blocked-student-list`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const detailsTable = document.getElementById('details-table'); // Corrected variable declaration
                    detailsTable.innerHTML = ''; // Clear previous rows if any
                    data.forEach(detail => {
                        // alert(detail.sc_sai_enrollment_no);
                        const row = `
                            <tr>
                                <td>${detail.sc_sai_enrollment_no}</td>
                                <td style="text-align:left">${detail.spi_name}</td>
                                <td>${detail.sc_status}</td>
                               
                            </tr>
                        `;
                        detailsTable.innerHTML += row;
                    });
                    
                    document.getElementById('mentor-details').style.display = 'block';
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                    //alert('Failed to load mentor details. Please try again later.');
                });
        }

    </script>

@endsection
