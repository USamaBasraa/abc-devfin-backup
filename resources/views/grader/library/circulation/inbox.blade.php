@extends('layouts.app',['pageTitle' => 'Circulation Reminder Log '])

@section('title')
    BNU CMS
@stop

@section('content')

<div class="card">
    <div class="card-header">
        <h3>Circulation Reminder Log</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="inboxTable" class="table table-striped table-bordered">
                                 <thead class="thead-dark">
                     <tr>
                         <th>#</th>
                         <th>User ID</th>
                         <th>Book Name</th>
                         <th>Issue Date</th>
                         <th>Due Date</th>
                         <th>Reminder Dates</th>
                         <th>Action</th>
                     </tr>
                 </thead>
                <tbody>
                    @foreach($inbox as $rec)
                        <tr>
                            @php $reminderArr = json_decode($rec->reminder2) @endphp
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $rec->sai_enrollment_no }}</td>
                            <td>{{ $rec->library_title }}</td>
                            <td>{{ $rec->lc_issue_date }}</td>
                            <td>{{ $rec->lc_due_date }}</td>
                                                         <td>
                                 @if($reminderArr)
                                     @foreach($reminderArr as $row)
                                         <span class="badge badge-info">{{ $row }}</span><br>
                                     @endforeach
                                 @else
                                     <span class="text-muted">No reminders</span>
                                 @endif
                             </td>
                             <td>
                                 <button class="btn btn-danger btn-sm" onclick="generateUserPDF('{{ $rec->sai_enrollment_no }}', '{{ $rec->library_title }}', '{{ $rec->lc_issue_date }}', '{{ $rec->lc_due_date }}', '{{ $rec->reminder2 }}')">
                                     <i class="fa fa-file-pdf-o"></i> PDF
                                 </button>
                             </td>
                         </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('javascript')


<script type="text/javascript">
$(document).ready(function() {
    $('#inboxTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                className: 'btn btn-primary btn-sm mr-2',
                title: 'Circulation Reminder Log - ' + new Date().toLocaleDateString(),
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf-o"></i> PDF',
                className: 'btn btn-primary btn-sm mr-2',
                title: 'Circulation Reminder Log - ' + new Date().toLocaleDateString(),
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5]
                },
                customize: function(doc) {
                    doc.pageOrientation = 'landscape';
                    doc.content[1].table.widths = ['8%', '18%', '30%', '18%', '18%', '8%'];
                    doc.styles.tableHeader = {
                        fillColor: '#343a40',
                        color: 'white',
                        fontSize: 12,
                        bold: true
                    };
                }
            }
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[0, 'asc']],
        responsive: true,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries per page",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            zeroRecords: "No matching records found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
});

// Function to generate individual user PDF
function generateUserPDF(userId, bookName, issueDate, dueDate, reminderData) {
    // Parse reminder data
    let reminders = [];
    if (reminderData && reminderData !== 'null') {
        try {
            reminders = JSON.parse(reminderData);
        } catch (e) {
            reminders = [];
        }
    }
    
    // Create PDF document
    const docDefinition = {
        pageSize: 'A4',
        pageMargins: [40, 80, 40, 80],
        
                 header: {
             stack: [
                 {
                     text: 'BEACONHOUSE NATIONAL UNIVERSITY Lahore',
                     style: 'universityName',
                     alignment: 'center',
                     margin: [0, 20, 0, 5]
                 },
              
                 {
                     canvas: [
                         {
                             type: 'line',
                             x1: 40, y1: 0, x2: 550, y2: 0,
                             lineWidth: 1,
                             lineColor: '#1a237e'
                         }
                     ],
                     margin: [0, 0, 0, 15]
                 },
                 {
                     text: 'CIRCULATION REMINDER REPORT',
                     style: 'reportTitle',
                     alignment: 'center',
                     margin: [0, 0, 0, 20]
                 }
             ]
         },
                 footer: {
             stack: [
                 {
                     canvas: [
                         {
                             type: 'line',
                             x1: 40, y1: 0, x2: 550, y2: 0,
                             lineWidth: 1,
                             lineColor: '#1a237e'
                         }
                     ],
                     margin: [0, 0, 0, 10]
                 },
                 {
                     text: 'Generated by: ' + '{{auth()->user()->full_name ?? "N/A" }}',
                     style: 'footerText',
                     alignment: 'center',
                     margin: [0, 0, 0, 5]
                 },
                {
                    text: 'Generated at: ' + new Date().toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    }) + ' at ' + new Date().toLocaleTimeString('en-US', { 
                        hour: '2-digit', 
                        minute: '2-digit', 
                        second: '2-digit',
                        hour12: true 
                    }),
                    style: 'footerText',
                    alignment: 'center',
                    margin: [0, 0, 0, 5]
                },
                {
                    text: 'Beaconhouse National University, Lahore, Pakistan',
                    style: 'footerText',
                    alignment: 'center',
                    margin: [0, 0, 0, 5]
                },
                {
                    text: 'Page ',
                    style: 'pageNumber',
                    alignment: 'center',
                    margin: [0, 5, 0, 0]
                }
            ]
        },
                 content: [
             {
                 text: 'BOOK CIRCULATION DETAILS',
                 style: 'subheader',
                 margin: [0, 20, 0, 20]
             },
                         {
                 table: {
                     widths: ['35%', '65%'],
                     body: [
                         [
                             { text: 'User ID:', style: 'label' },
                             { text: userId, style: 'value' }
                         ],
                         [
                             { text: 'Book Title:', style: 'label' },
                             { text: bookName, style: 'value' }
                         ],
                         [
                             { text: 'Issue Date:', style: 'label' },
                             { text: formatDate(issueDate), style: 'value' }
                         ],
                         [
                             { text: 'Due Date:', style: 'label' },
                             { text: formatDate(dueDate), style: 'value' }
                         ]
                     ]
                 },
                 layout: {
                     hLineWidth: function(i, node) { return 0.5; },
                     vLineWidth: function(i, node) { return 0.5; },
                     hLineColor: function(i, node) { return '#1a237e'; },
                     vLineColor: function(i, node) { return '#1a237e'; },
                     fillColor: function(rowIndex, node, columnIndex) {
                         return (rowIndex % 2 === 0) ? '#f8f9fa' : '#ffffff';
                     }
                 },
                 margin: [0, 0, 0, 25]
             },
                         {
                 text: 'REMINDER HISTORY',
                 style: 'subheader',
                 margin: [0, 20, 0, 10]
             },
                         {
                 table: {
                     widths: ['100%'],
                     body: reminders.length > 0 ? 
                         reminders.map((reminder, index) => [{ 
                             text: `${index + 1}. ${reminder}`, 
                             style: 'reminder' 
                         }]) :
                         [{ text: 'No reminders sent', style: 'noReminder' }]
                 },
                 layout: {
                     hLineWidth: function(i, node) { return 0.5; },
                     vLineWidth: function(i, node) { return 0.5; },
                     hLineColor: function(i, node) { return '#1a237e'; },
                     vLineColor: function(i, node) { return '#1a237e'; },
                     fillColor: function(rowIndex, node, columnIndex) {
                         return (rowIndex % 2 === 0) ? '#f8f9fa' : '#ffffff';
                     }
                 }
             }
        ],
                 styles: {
             universityName: {
                 fontSize: 18,
                 bold: true,
                 color: '#1a237e',
                 alignment: 'center'
             },
             universityLocation: {
                 fontSize: 14,
                 bold: true,
                 color: '#3949ab',
                 alignment: 'center'
             },
             universityAddress: {
                 fontSize: 11,
                 color: '#666666',
                 alignment: 'center'
             },
             contactInfo: {
                 fontSize: 9,
                 color: '#666666',
                 alignment: 'center'
             },
             reportTitle: {
                 fontSize: 16,
                 bold: true,
                 color: '#1a237e',
                 alignment: 'center',
                 decoration: 'underline'
             },
             footerText: {
                 fontSize: 9,
                 color: '#666666',
                 alignment: 'center'
             },
             pageNumber: {
                 fontSize: 9,
                 color: '#666666',
                 alignment: 'center'
             },
             subheader: {
                 fontSize: 14,
                 bold: true,
                 color: '#1a237e',
                 margin: [0, 10, 0, 5],
                 decoration: 'underline'
             },
             label: {
                 fontSize: 11,
                 bold: true,
                 color: '#1a237e',
                 fillColor: '#e3f2fd'
             },
             value: {
                 fontSize: 11,
                 color: '#2c3e50'
             },
             overdueValue: {
                 fontSize: 11,
                 color: '#d32f2f',
                 bold: true
             },
             reminder: {
                 fontSize: 10,
                 color: '#2e7d32',
                 fillColor: '#e8f5e8'
             },
             noReminder: {
                 fontSize: 10,
                 color: '#95a5a6',
                 fontStyle: 'italic'
             }
         }
    };
    
         // Generate and download PDF
     pdfMake.createPdf(docDefinition).download('Circulation_Report_' + userId + '_' + new Date().toISOString().split('T')[0] + '.pdf');
 }
 
 // Helper function to format dates
 function formatDate(dateString) {
     if (!dateString) return 'N/A';
     const date = new Date(dateString);
     return date.toLocaleDateString('en-US', { 
         year: 'numeric', 
         month: 'long', 
         day: 'numeric' 
     });
 }
 
 // Helper function to calculate overdue days
 function calculateOverdueDays(dueDate) {
     if (!dueDate) return 'N/A';
     const due = new Date(dueDate);
     const today = new Date();
     const diffTime = today - due;
     const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
     
     if (diffDays > 0) {
         return `${diffDays} days overdue`;
     } else if (diffDays === 0) {
         return 'Due today';
     } else {
         return `${Math.abs(diffDays)} days remaining`;
     }
 }
</script>
@stop