<div class="modal fade" id="mentorshipModal" tabindex="-1" role="dialog" aria-labelledby="mentorshipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- Dynamic content will be injected here -->
        </div>
    </div>
</div>

<script>



function openMentorshipModal(enrollmentNo, cgpa) {
    // AJAX request to fetch student profile details
    $.ajax({
        url: '/reports/get-student-profile-for-mentorship',
        type: 'GET',
        data: {
            enrollment_no: enrollmentNo,
        },
        success: function (response) {
            if (response.success) {
                const studentProfile = response.data;

                // Generate Modal Content Dynamically
                const profileHTML = `
                <div class="modal-header bg-primary text-white text-center font-weight-bold">
                    <h5 class="modal-title" id="mentorshipModalLabel">
                        <i class="fa fa-user-circle mr-2"></i> Student Profile
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center">
                        <!-- Profile Image and Status -->
                        <div class="col-md-3 text-center">
                            <img src="https://cms.bnu.edu.pk/students_pics/${studentProfile.enrollment_no.toUpperCase()}.jpg" 
                                 alt="Student Image" 
                                 class="img-fluid rounded-circle mb-3" 
                                 style="width: 120px; height: 120px; object-fit: cover;">
                            <h5 class="text-uppercase font-weight-bold text-primary mb-0" style="font-size: 1.1rem;">
                                ${studentProfile.student_status}
                            </h5>
                        </div>
                        <!-- Profile Details -->
                        <div class="col-md-9">
                            <div class="row">
                                ${generateInfoBox('Enrollment No', studentProfile.enrollment_no, 'fa-id-card')}
                                ${generateInfoBox('School', studentProfile.school_name, 'fa-building')}
                                ${generateInfoBox('Department', studentProfile.department_name, 'fa-university')}
                                ${generateInfoBox('Program', studentProfile.program_name, 'fa-book')}
                                ${generateInfoBox('Degree Level', studentProfile.degree_level, 'fa-graduation-cap')}
                                ${generateInfoBox('CGPA', cgpa, 'fa-star')}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>`;

                // Update the modal content
                $('#mentorshipModal .modal-content').html(profileHTML);
            } else {
                alert('Student profile not found.');
            }
        },
        error: function (error) {
            console.error('Error fetching student profile:', error);
            alert('Failed to fetch student profile. Please try again.');
        },
    });
}

// Helper function to generate info box
function generateInfoBox(label, value, iconClass) {
    return `
        <div class="col-sm-6 mb-3 d-flex align-items-center">
            <i class="fa ${iconClass} mr-2 text-muted"></i>
            <div>
                <span class="text-muted">${label}:</span>
                <strong>${value}</strong>
            </div>
        </div>
    `;
}


function submitMentorshipForm() {
    // Get form data
    const enrollmentNo = document.getElementById('mentorshipEnrollmentNo').value;
    const cgpa = document.getElementById('mentorshipCgpa').value;
    const notes = document.getElementById('mentorshipNotes').value;

    // Validate input (optional)
    if (!notes.trim()) {
        alert("Please provide mentorship notes.");
        return;
    }

    // AJAX request to submit mentorship details
    $.ajax({
        url: '/submit-mentorship',
        type: 'POST',
        data: {
            _token: $('input[name="_token"]').val(),
            enrollment_no: enrollmentNo,
            cgpa: cgpa,
            notes: notes,
        },
        success: function (response) {
            alert(response.message || 'Mentorship details submitted successfully!');
            $('#mentorshipModal').modal('hide');
        },
        error: function (error) {
            console.error('Error submitting mentorship details:', error);
            alert('Failed to submit mentorship details. Please try again.');
        },
    });
}






</script>
