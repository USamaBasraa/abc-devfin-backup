<!-- Vacate Confirmation Modal -->
<div class="modal fade" id="vacateConfirmationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fa fa-exclamation-triangle mr-2"></i>Confirm Hostel Vacate
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fa fa-warning mr-2"></i>
                    <strong>Warning:</strong> This action will vacate all non-paid students. Paid students will be kept.
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h5>Students to Vacate</h5>
                                <h2 id="preview_total_vacate">0</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5>Paid Students (Kept)</h5>
                                <h2 id="preview_paid_kept">0</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Remarks (Optional)</label>
                    <textarea class="form-control" id="vacate_remarks" rows="3" placeholder="Enter remarks for this vacate operation..."></textarea>
                </div>

                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Hostel</th>
                                <th>Floor, Room, Bed</th>
                            </tr>
                        </thead>
                        <tbody id="preview_students_list">
                            <!-- Students list will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmVacate()">
                    <i class="fa fa-trash mr-2"></i>Confirm Vacate
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var currentVacateData = null;
var currentHostelId = null;
window.currentHostelId = null;

$(document).on('showVacateModal', function(event, previewData, hostelId) {
    currentVacateData = previewData;
    currentHostelId = hostelId;
    window.currentHostelId = hostelId;
    console.log('Preview modal opened with hostel_id:', hostelId);

    $('#preview_total_vacate').text(previewData.total_to_vacate || 0);
    $('#preview_paid_kept').text(previewData.paid_kept || 0);

    var tbody = $('#preview_students_list');
    tbody.empty();

    if (previewData.students_to_vacate && previewData.students_to_vacate.length > 0) {
        $.each(previewData.students_to_vacate, function(index, student) {
            var floorRoomBed = 'N/A';
            if (student.floor_name && student.room_no && student.bed_no) {
                floorRoomBed = student.floor_name + ', Room No# ' + student.room_no + ', Bed No# ' + student.bed_no;
            }

            var studentName = student.student_name || 'N/A';
            var hostelName = student.hostel_name || 'N/A';

            var row = '<tr>';
            row += '<td>' + (index + 1) + '</td>';
            row += '<td>' + studentName + '</td>';
            row += '<td>' + hostelName + '</td>';
            row += '<td>' + floorRoomBed + '</td>';
            row += '</tr>';
            
            tbody.append(row);
        });
    } else {
        var noDataRow = '<tr><td colspan="4" class="text-center text-muted">No students to vacate</td></tr>';
        tbody.append(noDataRow);
    }

    $('#vacate_remarks').val('');

    $('#vacateConfirmationModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: true
    });
});

window.confirmVacate = function() {
    console.log('confirmVacate function called');
    var sessionId = $('#session_id').val();
    var remarks = $('#vacate_remarks').val();
    var hostelId = currentHostelId || window.currentHostelId;

    console.log('Session ID:', sessionId);
    console.log('Hostel ID:', hostelId);
    console.log('Remarks:', remarks);

    if (!sessionId) {
        showToast('error', 'Session ID is missing');
        return;
    }

    if (!hostelId) {
        showToast('error', 'Hostel ID is missing');
        return;
    }

    if (!confirm('Are you sure you want to proceed with vacate operation?')) {
        return;
    }

    var confirmBtn = $('#vacateConfirmationModal .btn-danger');
    var originalText = confirmBtn.html();
    confirmBtn.html('<i class="fa fa-spinner fa-spin mr-2"></i>Processing...').prop('disabled', true);

    console.log('Calling vacate API - POST /api/hostel-summary/vacate');

    $.ajax({
        url: '/api/hostel-summary/vacate',
        type: 'POST',
        data: {
            session_id: sessionId,
            hostel_id: hostelId,
            remarks: remarks
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Authorization': 'Bearer {{ auth()->user()->api_token ?? "" }}',
            'Accept': 'application/json'
        },
        dataType: 'json',
        success: function(response) {
            confirmBtn.html(originalText).prop('disabled', false);
            
            if (response.success) {
                showToast('success', 'Hostel vacated successfully! Total Vacated: ' + response.total_vacated + ', Paid Kept: ' + response.paid_kept);
                $('#vacateConfirmationModal').modal('hide');
                loadSummary();
            } else {
                var errorMsg = 'Unknown error';
                if (response.message) {
                    errorMsg = response.message;
                }
                showToast('error', errorMsg);
            }
        },
        error: function(xhr) {
            confirmBtn.html(originalText).prop('disabled', false);
            console.error('Vacate API Error:', xhr);
            var errorMsg = 'Error performing vacate operation. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showToast('error', errorMsg);
        }
    });
}
</script>

