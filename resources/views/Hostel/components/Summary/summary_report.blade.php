<!-- Buttons will appear here automatically when DataTable initializes -->
<div class="table-container">
    <table class="table table-bordered table-striped table-hover" id="summary_table">
        <thead class="bg-light">
            <tr>
                <th>Hostel Name</th>
                <th>Location</th>
                <th>Total Seats</th>
                <th>Paid</th>
                <th>Reserved</th>
                <th>Vacant</th>
                @if(isset($canVacate) && $canVacate)
                <th class="text-center">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody id="summary_table_body">
            <tr>
                <td colspan="7" class="text-center text-muted">
                    <i class="fa fa-info-circle mr-2"></i>Click "Load Summary" to view data
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
// DataTable initialization is now handled directly in renderSummaryTable function
// No need for event-based approach - same pattern as male_applicants_table

function viewHostelDetails(hostelId) {
    if (!currentSummaryData) return;
    
    const hostel = currentSummaryData.find(h => h.hostel_id == hostelId);
    if (!hostel) return;

    let detailsHtml = `
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Enrollment/Applicant ID</th>
                        <th>Email</th>
                        <th>Floor, Room, Bed</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
    `;

    if (hostel.students && hostel.students.length > 0) {
        hostel.students.forEach(function(student, index) {
            const status = student.sc_challan_status === 'PAID' ? 
                '<span class="badge badge-success">Paid</span>' : 
                '<span class="badge badge-warning">Reserved</span>';
            
            const floorRoomBed = (student.floor_name && student.room_no && student.bed_no) ?
                `${student.floor_name}, Room No# ${student.room_no}, Bed No# ${student.bed_no}` : 'N/A';

            detailsHtml += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${student.student_name || 'N/A'}</td>
                    <td>${student.ha_enrolment_no || student.ha_aai_id || 'N/A'}</td>
                    <td>${student.student_email || 'N/A'}</td>
                    <td>${floorRoomBed}</td>
                    <td>${status}</td>
                </tr>
            `;
        });
    } else {
        detailsHtml += `
            <tr>
                <td colspan="6" class="text-center text-muted">No students found</td>
            </tr>
        `;
    }

    detailsHtml += `
                </tbody>
            </table>
        </div>
    `;

    // Show in modal stable way
    $('#hostelDetailsModalTitle').text(hostel.hostel_name + ' - Student Details');
    $('#hostelDetailsModalBody').html(detailsHtml);
    $('#hostelDetailsModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: true
    });
}
</script>

<!-- Hostel Details Modal -->
<div class="modal fade" id="hostelDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="hostelDetailsModalTitle">Hostel Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="hostelDetailsModalBody">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

