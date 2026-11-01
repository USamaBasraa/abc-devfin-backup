<!-- Logs View Modal -->
<div class="modal fade" id="logsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fa fa-history mr-2"></i>Hostel Vacate Logs
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Date Range Filter -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="font-weight-bold">Date From</label>
                        <input type="date" class="form-control" id="log_date_from">
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold">Date To</label>
                        <input type="date" class="form-control" id="log_date_to">
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold">&nbsp;</label>
                        <button type="button" class="btn btn-primary btn-block" onclick="loadLogs()">
                            <i class="fa fa-search mr-2"></i>Filter Logs
                        </button>
                    </div>
                </div>

                <!-- Loading Indicator -->
                <div id="logs_loading" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading logs...</p>
                </div>

                <!-- Logs Table -->
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="bg-light" style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th>#</th>
                                <th>Date & Time</th>
                                <th>Hostel</th>
                                <th>Vacated By</th>
                                <th>Total Vacated</th>
                                <th>Paid Kept</th>
                                <th>Students List</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="logs_table_body">
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="fa fa-info-circle mr-2"></i>Click "Filter Logs" to load data
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function loadLogs() {
    const dateFrom = $('#log_date_from').val();
    const dateTo = $('#log_date_to').val();

    $('#logs_loading').show();
    $('#logs_table_body').html('<tr><td colspan="7" class="text-center">Loading...</td></tr>');

    $.ajax({
        url: '/api/hostel-summary/get-logs',
        type: 'GET',
        data: {
            date_from: dateFrom,
            date_to: dateTo
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
            'Accept': 'application/json'
        },
        dataType: 'json',
        success: function(response) {
            $('#logs_loading').hide();
            
            if (response.success) {
                renderLogsTable(response.logs);
            } else {
                $('#logs_table_body').html(`
                    <tr>
                        <td colspan="7" class="text-center text-danger">
                            Error: ${response.message || 'Unknown error'}
                        </td>
                    </tr>
                `);
            }
        },
        error: function(xhr) {
            $('#logs_loading').hide();
            const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error loading logs. Please try again.';
            $('#logs_table_body').html(`
                <tr>
                    <td colspan="7" class="text-center text-danger">
                        ${errorMsg}
                    </td>
                </tr>
            `);
        }
    });
}

function renderLogsTable(logs) {
    const tbody = $('#logs_table_body');
    tbody.empty();

    if (logs.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="8" class="text-center text-muted">
                    <i class="fa fa-info-circle mr-2"></i>No logs found
                </td>
            </tr>
        `);
        return;
    }

    logs.forEach(function(log, index) {
        const vacatedAt = log.HVL_VACATED_AT || log.hvl_vacated_at ? 
            new Date(log.HVL_VACATED_AT || log.hvl_vacated_at).toLocaleString() : 'N/A';
        
        const studentsList = log.HVL_STUDENTS_LIST || log.hvl_students_list || '-';
        const studentsListDisplay = studentsList && studentsList !== '-' ? 
            '<small class="text-muted">' + studentsList + '</small>' : '-';
        
        const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${vacatedAt}</td>
                <td>${log.HVL_HOSTEL_NAME || log.hvl_hostel_name || 'All Hostels'}</td>
                <td>${log.HVL_VACATED_BY || log.hvl_vacated_by || 'N/A'}</td>
                <td class="text-center">
                    <span class="badge badge-danger">${log.HVL_TOTAL_VACATED || log.hvl_total_vacated || 0}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-success">${log.HVL_PAID_KEPT || log.hvl_paid_kept || 0}</span>
                </td>
                <td>${studentsListDisplay}</td>
                <td>${log.HVL_REMARKS || log.hvl_remarks || '-'}</td>
            </tr>
        `;
        tbody.append(row);
    });
}

$(document).on('logsDataLoaded', function(event, logs) {
    renderLogsTable(logs);
});
</script>

