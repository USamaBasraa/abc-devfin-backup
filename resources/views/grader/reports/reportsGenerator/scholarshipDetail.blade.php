<!-- Warning Letter Modal -->

<style>
    .form-check-label {
        font-size: 20px; /* Adjust the font size as needed */
        margin-left: 10px;
    }

    .form-check-input {
        width: 20px; /* Adjust the width of the checkbox */
        height: 20px; /* Adjust the height of the checkbox */
    }
    .table-hover tbody tr:hover {
    background-color: #f5f5f5; /* Choose your desired hover color */
}
</style>
<div class="modal fade" id="scholarshipModal" tabindex="-1" role="dialog" aria-labelledby="scholarshipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scholarshipModalLabel"> <i class="fa fa-exclamation-circle"></i>    Scholarship Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
      
                <div id="showScholarshipDetail" class="table-responsive"></div>
             
            <h5  id='successMessages' class='text-success'></h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Done</button>
                <!-- <button type="button"  onclick="sendWarningLetter()"   class="btn btn-warning">Send Warning</button> -->
            </div>
        </div>
    </div>
</div>

<script>


function scholarshipDetails(enrollmentNo) {
    // Set the value of enrollmentNo in the hidden input field

    getScholarShipDetails(enrollmentNo);
   
}





function getScholarShipDetails(enrollmentNo) {
    fetch(`/reports/get-student-scholarship-details/${enrollmentNo}`, {
        method: 'GET',
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json(); // Corrected this line
    })
    .then(data => {
        makeLettersHtml2(data)
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function makeLettersHtml2(data) {
   
    let showScholarshipDetail = document.getElementById('showScholarshipDetail');
    showScholarshipDetail.innerHTML = ''; // Clear previous content
    
    if (data.length>0) {
        let lettersHtml = `
        <div style="max-height: 200px; overflow-y: auto;">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th scope="col">Registration</th>
                    <th scope="col">Session</th>
                    <th scope="col">Scholarship</th>
                    <th scope="col">Scholarship %</th>
                </tr>
            </thead>
            <tbody>
                ${data.map(element => `
                    <tr>
                        <td>${element.ssd_sai_enrollment_no}</td>
                        <td>${element.sess_name}</td>
                        <td>${element.scholarship_name}</td>
                        <td>${element.ssd_percentage}</td>
                    </tr>
                `).join('')}
            </tbody>
        </div>
        </table>
    `;

    showScholarshipDetail.innerHTML = lettersHtml;
    }
   
}





</script>