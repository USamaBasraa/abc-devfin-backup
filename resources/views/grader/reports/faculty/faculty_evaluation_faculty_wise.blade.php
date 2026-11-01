@extends('layouts.app')

@section('content')


<div class="card my-4"    id="facultyEvaluationReport">




    <div class="card-header">
        <button id="exportExcel" class="btn btn-primary mt-3 mr-4">

            <i class="fa fa-file-excel-o"></i> Export to Excel


        </button>
        <h5 class="mb-0">Evaluation Report</h5>


    </div>


    <div class="card-body">



<div class=" my-4">
    <!-- Header Information -->
    <div class="mb-4">
        <div id="evaluationTableContainer" class="table-responsive"></div>

    </div>

    <div class="table-responsive">
        <table class="table table-borderless table-striped align-middle text-start">
            <tbody>
                <tr>
                    <td class="fw-bold">Department:</td>
                    <td>{{ $data->dept_name }}</td>
                    <td class="fw-bold">Session:</td>
                    <td>{{ $data->sess_name }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">Programme:</td>
                    <td>N/A</td>
                    <td class="fw-bold">Total Faculty Members:</td>
                    <td>{{ $data->total_no_of_faculty }}</td>
                </tr>
            </tbody>
        </table>




        <table class="table table-bordered text-center" id="evaluationTable">
            <thead class="thead-light"> 
                <tr>
                    <th rowspan="2">Sr. No.</th>
                    <th rowspan="2">Name of Faculty</th>
                    <th rowspan="2">Course Code</th>
                    <th rowspan="2">Course Title</th>
                    <th rowspan="2">Course Cr. Hrs.</th>
                    <th rowspan="2">Total No of Students</th>
                    <th rowspan="2">No of Evaluators</th>
                    <th colspan="14" id="evaluationCriteriaHeaders">Evaluation Criteria</th>
                    <th rowspan="2">15.Would you like to take another course with the same faculty?</th>
                    <th rowspan="2" class="bg-warning">Total Weighted Score</th>
                </tr>
                <tr id="evaluationSubHeaders">
                    <!-- Evaluation criteria subheaders will be dynamically added here -->
                </tr>
            </thead>
            <tbody id="evaluationTableBody">
                <!-- Rows will be dynamically populated here -->
            </tbody>
        </table>
    </div>
    <div class="card-footer text-muted">
        Generated on: <span id="reportDate">{{ date('Y-m-d') }}</span>
    </div>
 </div>

</div>
</div>


@endsection


<script src="{{ asset('weekStudentsReport/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('weekStudentsReport/xlsx.full.min.js') }}"></script>

@section('javascript')




<script>
    $(document).ready(function () {


        function getQueryParams(url) {
        const params = {};
        const parser = new URL(url);
        const queryString = parser.search.slice(1); // Remove the "?" at the start
        const queryArray = queryString.split('&'); // Split into key=value pairs

        queryArray.forEach(pair => {
            const [key, value] = pair.split('=');
            params[decodeURIComponent(key)] = decodeURIComponent(value || '');
        });

        return params;
    }

    // Extract parameters from the current page's URL
    const currentURL = window.location.href;
    const queryParams = getQueryParams(currentURL);




    $.ajax({
        url: "{{ route('reports.facultywise.list') }}", // Replace with your route
        method: "GET",
        data: queryParams, // Pass the extracted parameters in the request body
        success: function (data) {
            // Extract unique evaluation criteria for headers
            const uniqueCriteria = [...new Set(data.map(item => item.evaluation_criteria))];

            // Populate evaluation criteria headers dynamically
            const evaluationSubHeaders = uniqueCriteria
                .map((criteria, index) => `<th>${index + 1}. ${criteria}</th>`)
                .join('');

            $('#evaluationSubHeaders').html(evaluationSubHeaders);

            // Group data by course_id
            const groupedData = {};
            data.forEach(item => {
                if (!groupedData[item.course_id]) {
                    groupedData[item.course_id] = {
                        course_id: item.course_id,
                        faculty_name: item.faculty_name,
                        course_code: item.course_code,
                        course_title: item.course_title,
                        course_credit_hours: item.course_credit_hours,
                        total_students: item.total_students,
                        no_of_evaluators: item.no_of_evaluators,
                        yes_count: item.yes_count, // Directly take from 0th index
                        no_count: item.no_count,  // Directly take from 0th index
                        evaluation_scores: {}
                    };
                }

                groupedData[item.course_id].evaluation_scores[item.evaluation_criteria] = item.weighted_score;
            });

            const groupedArray = Object.values(groupedData);

            // Populate table rows
            let tableRows = '';
            groupedArray.forEach((course, index) => {
                let evaluationCells = '';
                uniqueCriteria.forEach(criteria => {
                    const score = course.evaluation_scores[criteria] || '-';
                    evaluationCells += `<td>${score}</td>`;
                });

                const totalWeightedScore = Object.values(course.evaluation_scores).reduce(
                    (sum, val) => sum + parseFloat(val || 0),
                    0
                );

                // Populate each row with Yes and No counts
                tableRows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${course.faculty_name}</td>
                        <td>${course.course_code}</td>
                        <td>${course.course_title}</td>
                        <td>${course.course_credit_hours}</td>
                        <td>${course.total_students}</td>
                        <td>${course.no_of_evaluators}</td>
                        ${evaluationCells}
                        <td>Yes: ${course.yes_count}, No: ${course.no_count}</td>
                        <td class="bg-warning">${totalWeightedScore.toFixed(2)}</td>
                    </tr>`;
            });

            // Append rows to the table body
            $('#evaluationTableBody').html(tableRows);
        },
        error: function (error) {
            console.error('Error fetching data:', error);
        }
    });






    $('#exportExcel').on('click', function () {
            const table = document.getElementById('facultyEvaluationReport');
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(table, { raw: true });

            // Set fixed column widths to 35 characters and enable text wrapping
            ws['!cols'] = Array(20).fill({ wch: 35 }); // Adjust the number of columns if needed

            // Enable text wrapping for all cells
            const range = XLSX.utils.decode_range(ws['!ref']);
            for (let row = range.s.r; row <= range.e.r; row++) {
                for (let col = range.s.c; col <= range.e.c; col++) {
                    const cellAddress = XLSX.utils.encode_cell({ r: row, c: col });
                    if (!ws[cellAddress]) continue; // Skip empty cells

                    if (!ws[cellAddress].s) ws[cellAddress].s = {}; // Initialize styles
                    ws[cellAddress].s.alignment = { wrapText: true }; // Enable wrapText
                }
            }

            // Append worksheet to workbook
            XLSX.utils.book_append_sheet(wb, ws, "Faculty Evaluation Report");

            // Export to Excel file
            XLSX.writeFile(wb, "FacultyEvaluationReport.xlsx");
        });



    });
</script>


@stop

