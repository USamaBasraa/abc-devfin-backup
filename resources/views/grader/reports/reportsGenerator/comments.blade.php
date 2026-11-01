<!-- Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel">Add Comment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="list-group" style="max-height: 200px; overflow-y: auto;" id="CommentsDiv">
                    {{-- <!-- Dummy Comments -->
                    <div class="list-group-item">
                        <strong>User1:</strong> This is the first comment. <small class="text-muted">2 minutes ago</small>
                    </div>
               --}}

                    <!-- Add more dummy comments as needed -->
                </div>

                <form class="mt-3" id="sendCommentForm" style="border: 10px;">
                    @csrf
                <!-- Add your comment form or content here -->
                <input type="hidden" id="sendCommentTo" name="sendCommentTo" value="">
                <input type="hidden" id="messageType" name="messageType" value="FacultyComment">
                <textarea class="form-control" id="sendComment" name="sendComment"  placeholder="Enter comment here..."  rows="3"></textarea>
                <h6 data-error-for="sendComment" class="validation-error"></h6>


            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button"  onclick="sendNotification()"  class="btn btn-primary">Submit Comment</button>
            </div>
        </div>
    </div>
</div>




<script>



function setEnrollmentNo(enrollmentNo) {
    // Set the value of enrollmentNo in the hidden input field

    
    document.querySelector('#commentModalLabel').innerHTML = enrollmentNo;

    document.getElementById('sendCommentTo').value = enrollmentNo;
    document.getElementById('sendWarningLetterTo').value = enrollmentNo;
    getComments(enrollmentNo)
}




    function sendNotification() {
        let form = document.getElementById('sendCommentForm');
        let data = new FormData(form);

        fetch(`/reports/send-faculty-student-comment`, {
            method: 'POST', // Using POST method
            body: data
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                form.reset();
                makeCommentsHtml(data.getComments)
                console.log('Success:', data.message);
                // Handle success response here
            } else {

                  displayValidationErrors(data.errors);
                console.error('Error:', data.errors);
                // Handle error here
            }
        })
        .catch((error) => {
            displayValidationErrors(error);
            console.error('Error:', error);
            // Handle fetch error here
        });
    }
    function getComments(enrollmentNo) {
    let messageType = document.getElementById('messageType').value; // Assuming 'messageType' is the ID of the element
    fetch(`/reports/get-faculty-student-comment/${enrollmentNo}?messageType=${messageType}`, {
        method: 'GET',
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        makeCommentsHtml(data);
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

    function makeCommentsHtml(data) {
    let CommentsDiv = document.getElementById('CommentsDiv');
    CommentsDiv.innerHTML = ''; // Corrected method name

    console.log(data);

    const commentsHtml = data.map((element) => {
        return `

        <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${element.full_name}</h6>
                    <small class="text-muted">${new Date(element.created_at).toLocaleString()} </small>
                </div>
                <p class="mb-1">${element.message}</p>
            </div>
        `;
    });

    CommentsDiv.innerHTML = commentsHtml.join(''); // Corrected method name
}

  






    </script>
