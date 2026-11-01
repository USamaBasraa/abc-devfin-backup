<!-- Psychiatrist Modal -->
<div class="modal fade" id="psychiatristModal" tabindex="-1" role="dialog" aria-labelledby="psychiatristModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="psychiatristModalLabel">Add Psychiatrist Comment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="list-group" style="max-height: 400px; overflow-y: auto;" id="psychiatristsCommentsDiv">
                </div>
                <input type="hidden" id="sendPsychiatristTo" name="sendPsychiatristTo" value="">
                <!-- <form class="mt-3" id="sendPsychiatristForm">
                    @csrf
                    <input type="hidden" id="sendPsychiatristTo" name="sendPsychiatristTo" value="">
                    <textarea class="form-control" id="sendPsychiatrist" name="sendPsychiatrist" placeholder="Enter remarks here..." rows="3"></textarea>
                    <h6 data-error-for="sendPsychiatrist" class="validation-error"></h6>
                </form> -->
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" onclick="sendPsychiatristNotification()" class="btn btn-primary">Submit Psychiatrist Comment</button>
            </div> -->
        </div>
    </div>
</div>


<script>


function getPsychiatristComments(enrollmentNo) {
    const formData = new FormData();
    let getAllRec='allRec';
    fetch(`/reports/get-psychiatrist-student-comment/${enrollmentNo}/${getAllRec}`, {
        method: 'GET',

    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        renderPsychiatristComments(data);
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function renderPsychiatristComments(data) {
    let psychiatristsCommentsDiv = document.getElementById('psychiatristsCommentsDiv');
    psychiatristsCommentsDiv.innerHTML = '';

    if (data && data.length > 0) {
        const commentsHtml = data.map((element) => {
            // Check if emp_ad_username is 'sadia.nawaz' and set dept_name accordingly
            let deptName = element.dept_name;
            if (element.emp_ad_username === 'sadia.nawaz') {
                deptName = 'Department of Counseling';
            }

            return `
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title"><strong>${deptName}</strong></h6>
                            <small class="text-muted">
                                <i class="fa fa-clock-o"></i> ${new Date(element.created_at).toLocaleString()}
                            </small>
                        </div>
                        <h6 class="card-text mt-2">Treatment:</h6>
                        <p>${element.message}</p>
                    </div>
                </div>
            `;
        });

        psychiatristsCommentsDiv.innerHTML = commentsHtml.join('');
    } else {
        psychiatristsCommentsDiv.innerHTML = `
            <div class="alert alert-warning text-center" role="alert">
                No Record Found
            </div>
        `;
    }
}


function setEnrollmentNoPsychiatrist(enrollmentNo) {
    document.querySelector('#psychiatristModalLabel').innerHTML = enrollmentNo;
    document.getElementById('sendPsychiatristTo').value = enrollmentNo;
    getPsychiatristComments(enrollmentNo);
}

function sendPsychiatristNotification() {
    let form = document.getElementById('sendPsychiatristForm');
    let data = new FormData(form);

    fetch(`/reports/send-psychiatrist-student-comment`, {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            form.reset();
            renderPsychiatristComments(data.getComments);
            console.log('Success:', data.message);
        } else {
            displayValidationErrors(data.errors);
            console.error('Error:', data.errors);
        }
    })
    .catch((error) => {
        displayValidationErrors(error);
        console.error('Error:', error);
    });
}






    </script>
