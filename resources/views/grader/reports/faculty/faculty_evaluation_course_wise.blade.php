@extends('layouts.app')

@section('content')





<div class="container my-4"   id="export_to_excel">
    <!-- Summary Card -->
    <div class="card">


        <div class="card-header text-center">
            <h4 class="mb-0">SUMMARY OF STUDENT FEEDBACK EVALUATION</h4>

          <div class="mt-1">Session: {{ request()->get('sessionName') ? request()->get('sessionName') : 'N/A' }}</div>
          {{-- sessionName=Spring%202025      get value from utrl parms  --}}

        </div>



        <div class="card-body">

            <button id="exportToExcel" class="btn btn-outline-success mb-4">
                <i class="fa fa-file-excel-o"></i> Export to Excel
            </button>


            <!-- Information Table -->
            <table class="table table-bordered">
                <tr>
                    <td><strong>School:</strong></td>
                    <td>{{ $courseDetails->school_name ?? 'N/A' }}</td>
                    <td><strong>Department:</strong></td>
                    <td>{{ $courseDetails->department_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Degree Programs:</strong></td>
                    <td>{{ $courseDetails->degree_program ?? 'N/A' }}</td>
                    <td><strong>Faculty Name:</strong></td>
                    <td>{{ $courseDetails->faculty_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Total No. of Students:</strong></td>
                    <td>{{ $courseDetails->total_students ?? 0 }}</td>
                    <td><strong>No. of Evaluators:</strong></td>
                    <td>{{ $courseDetails->no_of_evaluators ?? 0 }}</td>
                </tr>
                <tr>
                    <td><strong>Course Code:</strong></td>
                    <td>{{ $courseDetails->course_code ?? 'N/A' }}</td>
                    <td><strong>Course Title:</strong></td>
                    <td>{{ $courseDetails->course_title ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Course Cr. Hrs.:</strong></td>
                    <td>{{ $courseDetails->course_credit_hours ?? 'N/A' }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>


         <!-- Evaluation Criteria Table -->
<table class="table table-bordered">
    <thead class="thead-light">
        <tr>
            <th>Sr No#</th>
            <th>Evaluation Criteria</th>
            <th>Weightage</th>
            <th>Average Score</th>
            <th>Weighted Score</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalWeightedScore = 0; // Initialize total score variable
        @endphp

        @foreach($courseEvaluations as $index => $evaluation)
        <tr>
            <td>{{ $index + 1 }}</td> <!-- Serial number -->
            <td>{{ $evaluation->evaluation_criteria }}</td>
            <td>{{ $evaluation->weightage }}</td>
            <td>{{ $evaluation->average_score }}</td>
            <td>{{ $evaluation->weighted_score }}</td>
            @php
                $totalWeightedScore += $evaluation->weighted_score; // Add weighted score to total
            @endphp
        </tr>
        @endforeach

       <!-- Additional Summary Row -->
<tr>
    <td>15</td> <!-- Static Sr No for this row -->
    <td>Would you like to take another course with the same faculty?</td>
    <td colspan="2">Yes: {{ $evaluationCounts->yes_count ?? 0 }}</td>
    <td>No: {{ $evaluationCounts->no_count ?? 0 }}</td>
</tr>

    </tbody>
</table>


            <!-- Total Weighted Score -->
            <div class="text-right">
                <strong>Total Weighted Score:</strong> {{ number_format($totalWeightedScore, 2) }}/100
            </div>
        </div>
    </div>
</div>

<div class="container my-4">
  <!-- Comments Card -->
<div class="card">
    <div class="card-header text-center font-weight-bold">
        COMMENTS SHEET
    </div>
    <div class="card-body">
        <div class="text-center font-weight-bold mb-4">
            School/Department <br>
            Session: {{ request()->get('sessionName') ? request()->get('sessionName') : 'N/A' }}
        </div>

        <!-- Information Table -->
        <table class="table table-borderless mb-4">
            <tbody>
                <tr>
                    <td class="font-weight-bold">Teacher Name:</td>
                    <td>{{ $courseDetails->faculty_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Course Name:</td>
                    <td>{{ $courseDetails->course_title ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Course Code:</td>
                    <td>{{ $courseDetails->course_code ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Credit Hour:</td>
                    <td>{{ $courseDetails->course_credit_hours ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Comments Table -->
        <h5 class="font-weight-bold">Comments:</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sr No#</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comments as $index => $comment)
                    <tr>
                        <td>{{ $index + 1 }}</td> <!-- Serial number -->
                        <td>{{ ucfirst($comment) }}</td> <!-- Capitalize first letter of comment -->
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">No comments available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>


@endsection




<script src="{{ asset('weekStudentsReport/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('weekStudentsReport/xlsx.full.min.js') }}"></script>


@section('javascript')

<script>




$('#exportToExcel').on('click', function () {
    // Initialize a new workbook
    var wb = XLSX.utils.book_new();

    // Create a new worksheet for the main content
    var mainSheet = {};
 
    // Add the card-header content (title and session)
    mainSheet['A1'] = { t: 's', v: 'SUMMARY OF STUDENT FEEDBACK EVALUATION' }; // Title
    mainSheet['A2'] = { t: 's', v: 'Session: {{ request()->get('sessionName') ? request()->get('sessionName') : 'N/A' }}' }; // Session

    // Define the number of columns to merge
    const maxColumns = 5; // Adjust this to match the actual number of visible columns in your sheet

    // Merge cells for the header and session across all visible columns
    mainSheet['!merges'] = [
        { s: { r: 0, c: 0 }, e: { r: 0, c: maxColumns - 1 } }, // Merge title across columns (A1 to E1)
        { s: { r: 1, c: 0 }, e: { r: 1, c: maxColumns - 1 } }  // Merge session across columns (A2 to E2)
    ];

    // Apply center alignment and styling to header cells
    mainSheet['A1'].s = {
        alignment: { horizontal: 'center', vertical: 'center' },
        font: { bold: true, color: { rgb: "FFFFFF" } },
        fill: { fgColor: { rgb: "4CAF50" } } // Header background color
    };

    mainSheet['A2'].s = {
        alignment: { horizontal: 'center', vertical: 'center' },
        font: { bold: true, color: { rgb: "FFFFFF" } },
        fill: { fgColor: { rgb: "4CAF50" } } // Header background color
    };

    // Leave a blank row after the header
    let contentStartRow = 5; // Content starts after the header

    // Get the entire div content by its ID
    const exportDiv = document.getElementById('export_to_excel');
    const tableSheet = XLSX.utils.table_to_sheet(exportDiv);

    // Adjust table rows to start after the header
    const tableRange = XLSX.utils.decode_range(tableSheet['!ref']);
    Object.keys(tableSheet).forEach((key) => {
        if (key[0] !== '!') {
            const cell = XLSX.utils.decode_cell(key);
            const newKey = XLSX.utils.encode_cell({ r: cell.r + contentStartRow, c: cell.c });
            mainSheet[newKey] = tableSheet[key];

            // Apply left alignment for all non-header content
            if (!mainSheet[newKey].s) {
                mainSheet[newKey].s = {}; // Initialize style object if undefined
            }
            mainSheet[newKey].s.alignment = { horizontal: 'left' };
        }
    });

    // Update the range to include the header and table rows
    mainSheet['!ref'] = XLSX.utils.encode_range({
        s: { r: 0, c: 0 }, // Start from A1
        e: { r: tableRange.e.r + contentStartRow, c: tableRange.e.c }
    });

    // Add Total Weighted Score at the appropriate position
    const range = XLSX.utils.decode_range(mainSheet['!ref']);
    const totalScoreRow = range.e.r + 2; // Leave a blank row after the table
    const totalScoreLabelCell = XLSX.utils.encode_cell({ r: totalScoreRow, c: range.s.c + 3 }); // Fourth column
    const totalScoreValueCell = XLSX.utils.encode_cell({ r: totalScoreRow, c: range.s.c + 4 }); // Fifth column

    mainSheet[totalScoreLabelCell] = { t: 's', v: 'Total Weighted Score:' };
    mainSheet[totalScoreValueCell] = { t: 's', v: '{{ number_format($totalWeightedScore, 2) }}/100' }; // Use dynamic value from Blade

    // Append the Details Section (Teacher Name, Course Name, etc.) below Total Weighted Score
    const detailsSection = document.querySelectorAll('.table.table-borderless.mb-4 tbody tr'); // Select rows in the details table
    if (detailsSection) {
        let startRow = totalScoreRow + 2; // Leave a blank row after Total Weighted Score

        detailsSection.forEach((row) => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, colIndex) => {
                const cellAddress = XLSX.utils.encode_cell({ r: startRow, c: colIndex });
                mainSheet[cellAddress] = { t: 's', v: cell.innerText }; // Add cell content

                // Apply left alignment for details section
                if (!mainSheet[cellAddress].s) {
                    mainSheet[cellAddress].s = {}; // Initialize style object if undefined
                }
                mainSheet[cellAddress].s.alignment = { horizontal: 'left' };
            });
            startRow++; // Move to the next row
        });

        // Update the range to include the new rows
        mainSheet['!ref'] = XLSX.utils.encode_range({
            s: range.s,
            e: { r: startRow - 1, c: range.e.c },
        });
    }

    // Append Comments Section (if it exists) after Details Section
    var commentsTable = document.querySelector('.table.table-bordered:last-child'); // Comments table
    if (commentsTable) {
        const commentsSheet = XLSX.utils.table_to_sheet(commentsTable);

        // Find the row after the Details Section
        const range = XLSX.utils.decode_range(mainSheet['!ref']);
        const startRow = range.e.r + 2; // Leave a blank row

        // Append comments data to the main sheet
        Object.keys(commentsSheet).forEach(cell => {
            if (cell[0] === '!') return; // Skip metadata
            const col = cell.replace(/[0-9]/g, ''); // Extract column
            const row = parseInt(cell.replace(/[A-Z]/g, ''), 10); // Extract row
            const newRow = startRow + row - 1; // Calculate new row position
            const newCell = `${col}${newRow}`; // New cell address
            mainSheet[newCell] = commentsSheet[cell];

            // Apply left alignment for comments section
            if (!mainSheet[newCell].s) {
                mainSheet[newCell].s = {}; // Initialize style object if undefined
            }
            mainSheet[newCell].s.alignment = { horizontal: 'left' };
        });

        // Update the sheet range to include the new rows
        mainSheet['!ref'] = XLSX.utils.encode_range({
            s: range.s,
            e: { r: startRow + commentsTable.rows.length - 2, c: range.e.c },
        });
    }

    // Auto-adjust column widths based on content
    const cols = [];
    const finalRange = XLSX.utils.decode_range(mainSheet['!ref']);
    for (let col = finalRange.s.c; col <= finalRange.e.c; col++) {
        let maxWidth = 10; // Default minimum width
        for (let row = finalRange.s.r; row <= finalRange.e.r; row++) {
            const cell = mainSheet[XLSX.utils.encode_cell({ r: row, c: col })];
            if (cell && cell.v) {
                const cellLength = String(cell.v).length;
                maxWidth = Math.max(maxWidth, cellLength);
            }
        }
        cols.push({ wch: maxWidth });
    }
    mainSheet['!cols'] = cols;

    // Append the sheet to the workbook
    XLSX.utils.book_append_sheet(wb, mainSheet, 'Student Feedback Evaluation');

    // Export the workbook to Excel
    XLSX.writeFile(wb, 'Student_Feedback_Evaluation.xlsx');
});







</script>


@stop
