@extends('layouts.app')

@section('content')

    <div class="container-fluid p-5 my-1">
        <div class="card p-2 m-2">
            <div class="card-body pb-2">
                <form id = 'staffExitfeedbackForm'>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="col-form-label">Name:</label>
                            <input type="text" class="form-control" name="full_name" id="fullName"
                                placeholder="Enter full name" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="col-form-label">CNIC:</label>
                            <input type="text" class="form-control" name="cnic" id="cnic"
                                placeholder="Enter CNIC without dash" pattern="\d{13}" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="col-form-label">School:</label>
                            <input type="text" class="form-control" name="school" id="school"
                                placeholder="Enter school name" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="col-form-label">Department:</label>
                            <input type="text" class="form-control" name="department" id="department"
                                placeholder="Enter department name" required>
                        </div>
                        <div class="col-sm-3">
                            <label class="col-form-label">Employee ID:</label>
                            <input type="text" class="form-control" name="employee_id" id="employeeId"
                                placeholder="Enter Employee ID" required>
                        </div>
                        <div class="col-sm-3">
                            <label class="col-form-label">Mobile No:</label>
                            <input type="text" class="form-control" name="mobile_no" id="mobileNo"
                                placeholder="Enter Mobile Number" pattern="\d{11}" required>
                        </div>
                        <div class="col-sm-3">
                            <label class="col-form-label">Email:</label>
                            <input type="email" class="form-control" name="email" id="email"
                                placeholder="Enter Email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}"  required>
                        </div>
                        <div class="col-sm-3">
                            <label class="col-form-label">Designation:</label>
                            <input type="text" class="form-control" name="designation" id="designation"
                                placeholder="Enter Designation" required>
                        </div>
                    </div>
                    <div class="form-container">
                        <h5>Principal reason for leaving:</h5>
                        <div class="mb-3">
                            <textarea id="q0" name="q0" class="form-control" maxlength="3500" placeholder="Enter your reason here..." required></textarea>
                        </div>
                    </div>
                    <div class="Container">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <h3>Questions on the position you held while employed at BNU:</h3>
                                <h5>What is your assessment of your supervisory chain in regard to the following?</h5>
                                <thead>
                                    <tr>
                                        <th style="width: 30%">Question</th>
                                        <th style="width: 10%">Excellent</th>
                                        <th style="width: 10%">Good</th>
                                        <th style="width: 10%">Fair</th>
                                        <th style="width: 10%">Poor</th>
                                        <th style="width: 10%">N/A</th>
                                        <th style="width: 20%">Comments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Fair and equal treatment</td>
                                        <td><input type="radio" name="q1" value="Excellent" required></td>
                                        <td><input type="radio" name="q1" value="Good" required></td>
                                        <td><input type="radio" name="q1" value="Fair" required></td>
                                        <td><input type="radio" name="q1" value="Poor" required></td>
                                        <td><input type="radio" name="q1" value="N/A" required></td>
                                        <td>
                                            <textarea name="q1_comments" class="form-control" maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Recognition</td>
                                        <td><input type="radio" name="q2" value="Excellent" required></td>
                                        <td><input type="radio" name="q2" value="Good" required></td>
                                        <td><input type="radio" name="q2" value="Fair" required></td>
                                        <td><input type="radio" name="q2" value="Poor" required></td>
                                        <td><input type="radio" name="q2" value="N/A" required></td>
                                        <td>
                                            <textarea name="q2_comments" class="form-control" maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Cooperation and teamwork</td>
                                        <td><input type="radio" name="q3" value="Excellent" required></td>
                                        <td><input type="radio" name="q3" value="Good" required></td>
                                        <td><input type="radio" name="q3" value="Fair" required></td>
                                        <td><input type="radio" name="q3" value="Poor" required></td>
                                        <td><input type="radio" name="q3" value="N/A" required></td>
                                        <td>
                                            <textarea name="q3_comments" class="form-control" maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Training/Development Opportunities</td>
                                        <td><input type="radio" name="q4" value="Excellent" required></td>
                                        <td><input type="radio" name="q4" value="Good" required></td>
                                        <td><input type="radio" name="q4" value="Fair" required></td>
                                        <td><input type="radio" name="q4" value="Poor" required></td>
                                        <td><input type="radio" name="q4" value="N/A" required></td>
                                        <td>
                                            <textarea name="q4_comments" class="form-control" maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Policies and procedures</td>
                                        <td><input type="radio" name="q5" value="Excellent" required></td>
                                        <td><input type="radio" name="q5" value="Good" required></td>
                                        <td><input type="radio" name="q5" value="Fair" required></td>
                                        <td><input type="radio" name="q5" value="Poor" required></td>
                                        <td><input type="radio" name="q5" value="N/A" required></td>
                                        <td>
                                            <textarea name="q5_comments" class="form-control" maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Communication</td>
                                        <td><input type="radio" name="q6" value="Excellent" required></td>
                                        <td><input type="radio" name="q6" value="Good" required></td>
                                        <td><input type="radio" name="q6" value="Fair" required></td>
                                        <td><input type="radio" name="q6" value="Poor" required></td>
                                        <td><input type="radio" name="q6" value="N/A" required></td>
                                        <td>
                                            <textarea name="q6_comments" class="form-control" maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <!-- Repeat for other questions -->
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <h5>How relevant were the below factors in your decision to leave BNU?</h5>
                                <tbody>
                                    <thead>
                                        <tr>
                                            <th style="width: 30%">Question</th>
                                            <th style="width: 10%">Excellent</th>
                                            <th style="width: 10%">Good</th>
                                            <th style="width: 10%">Fair</th>
                                            <th style="width: 10%">Poor</th>
                                            <th style="width: 10%">N/A</th>
                                            <th style="width: 20%">Comments</th>
                                        </tr>
                                    </thead>
                                    <tr>
                                        <td>Tenure/ Promotion opportunities</td>
                                        <td><input type="radio" name="q7" value="Excellent" required></td>
                                        <td><input type="radio" name="q7" value="Good" required></td>
                                        <td><input type="radio" name="q7" value="Fair" required></td>
                                        <td><input type="radio" name="q7" value="Poor" required></td>
                                        <td><input type="radio" name="q7" value="N/A" required></td>
                                        <td>
                                            <textarea name="q7_comments" class="form-control" maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Decision making autonomy</td>
                                        <td><input type="radio" name="q8" value="Excellent" required></td>
                                        <td><input type="radio" name="q8" value="Good" required></td>
                                        <td><input type="radio" name="q8" value="Fair" required></td>
                                        <td><input type="radio" name="q8" value="Poor" required></td>
                                        <td><input type="radio" name="q8" value="N/A" required></td>
                                        <td>
                                            <textarea name="q8_comments" class="form-control" maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pay, benefits and incentives</td>
                                        <td><input type="radio" name="q9" value="Excellent" required></td>
                                        <td><input type="radio" name="q9" value="Good" required></td>
                                        <td><input type="radio" name="q9" value="Fair" required></td>
                                        <td><input type="radio" name="q9" value="Poor" required></td>
                                        <td><input type="radio" name="q9" value="N/A" required></td>
                                        <td>
                                            <textarea name="q9_comments" class="form-control" maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Job Satisfaction</td>
                                        <td><input type="radio" name="q10" value="Excellent" required></td>
                                        <td><input type="radio" name="q10" value="Good" required></td>
                                        <td><input type="radio" name="q10" value="Fair" required></td>
                                        <td><input type="radio" name="q10" value="Poor" required></td>
                                        <td><input type="radio" name="q10" value="N/A" required></td>
                                        <td>
                                            <textarea name="q10_comments" class="form-control" maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Salary</td>
                                        <td><input type="radio" name="q11" value="Excellent" required></td>
                                        <td><input type="radio" name="q11" value="Good" required></td>
                                        <td><input type="radio" name="q11" value="Fair" required></td>
                                        <td><input type="radio" name="q11" value="Poor" required></td>
                                        <td><input type="radio" name="q11" value="N/A" required></td>
                                        <td>
                                            <textarea name="q11_comments" class="form-control" maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>HOD</td>
                                        <td><input type="radio" name="q12" value="Excellent" required></td>
                                        <td><input type="radio" name="q12" value="Good" required></td>
                                        <td><input type="radio" name="q12" value="Fair" required></td>
                                        <td><input type="radio" name="q12" value="Poor" required></td>
                                        <td><input type="radio" name="q12" value="N/A" required></td>
                                        <td>
                                            <textarea name="q12_comments" class="form-control" maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dean</td>
                                        <td><input type="radio" name="q13" value="Excellent" required></td>
                                        <td><input type="radio" name="q13" value="Good" required></td>
                                        <td><input type="radio" name="q13" value="Fair" required></td>
                                        <td><input type="radio" name="q13" value="Poor" required></td>
                                        <td><input type="radio" name="q13" value="N/A" required></td>
                                        <td>
                                            <textarea name="q13_comments" class="form-control" maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Overall rating of BNU</td>
                                        <td><input type="radio" name="q14" value="Excellent" required></td>
                                        <td><input type="radio" name="q14" value="Good" required></td>
                                        <td><input type="radio" name="q14" value="Fair" required></td>
                                        <td><input type="radio" name="q14" value="Poor" required></td>
                                        <td><input type="radio" name="q14" value="N/A" required></td>
                                        <td>
                                            <textarea name="q14_comments" class="form-control" maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table table- striped">
                                <h5> Workload </h5>
                                <thead>
                                    <tr>
                                        <th style="width: 25%">Question</th>
                                        <th style="width: 10%">Overburdened</th>
                                        <th style="width: 14%">Varied, but manageable</th>
                                        <th style="width: 10%">Just right</th>
                                        <th style="width: 10%">Too light</th>
                                        <th style="width: 15%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>What was your workload usually?</td>
                                        <td><input type="radio" name="q15" value="Overburdened" required></td>
                                        <td><input type="radio" name="q15" value="Varied, but manageable" required></td>
                                        <td><input type="radio" name="q15" value="Just right" required></td>
                                        <td><input type="radio" name="q15" value="Too light" required></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <th></th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <label for="q16">Did any specific event trigger your decision to
                                                leave?</label>
                                            <input type="text" id="q16" name="q16" class="form-control" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q17">When did you begin searching for another job?</label>
                                            <input type="text" id="q17" name="q17" class="form-control"  required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q18">What makes the new job more attractive than your current
                                                position?</label>
                                            <input type="text" id="q18" name="q18" class="form-control" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q19">Have you spoken with anyone, either your director or any
                                                of your
                                                colleagues
                                                about your career goals?</label>
                                            <input type="text" id="q19" name="q19" class="form-control" required>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>
                                            <label for="q20">Did you receive timely, useful, and adequate feedback on
                                                your
                                                performance?</label>
                                        </td>
                                        <td>
                                            <input type="radio" id="q20_yes" name="q20" value="Yes"  required>
                                            <label for="q20_yes">Yes</label>
                                            <input type="radio" id="q20_no" name="q20" value="No" required>
                                            <label for="q20_no">No</label>
                                            <textarea id="q20_explanation" name="q20_explanation" class="form-control" placeholder="Please explain"
                                                maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q21">Did you have clear goals and know what was expected of
                                                you in your
                                                job?</label>
                                        </td>
                                        <td>
                                            <input type="radio" id="q21_yes" name="q21" value="Yes" required>
                                            <label for="q21_yes">Yes</label>
                                            <input type="radio" id="q21_no" name="q21" value="No" required>
                                            <label for="q21_no">No</label>
                                            <textarea id="q21_explanation" name="q21_explanation" class="form-control" placeholder="Please explain"
                                                maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q22">Did you have the resources and support necessary to
                                                accomplish your
                                                job?</label>
                                        </td>
                                        <td>
                                            <input type="radio" id="q22_yes" name="q22" value="Yes" required>
                                            <label for="q22_yes">Yes</label>
                                            <input type="radio" id="q22_no" name="q22" value="No" required>
                                            <label for="q22_no">No</label>
                                            <textarea id="q22_explanation" name="q22_explanation" class="form-control" placeholder="Please explain"
                                                maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q23">Did you understand and feel a part of the accomplishment
                                                of the BNU
                                                mission?</label>
                                        </td>
                                        <td>
                                            <input type="radio" id="q23_yes" name="q23" value="Yes" required>
                                            <label for="q23_yes">Yes</label>
                                            <input type="radio" id="q23_no" name="q23" value="No" required>
                                            <label for="q23_no">No</label>
                                            <textarea id="q23_explanation" name="q23_explanation" class="form-control" placeholder="Please explain"
                                                maxlength="3500"></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>

                                    <tr>
                                        <td>
                                            <label for="q24">What improvements (to BNU, your department, or to your
                                                job) would you
                                                suggest?</label>
                                            <textarea id="q24" name="q24" class="form-control"  required></textarea >
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="q25">What recommendation would you have for making the BNU as a whole a better place to work?</label>
                                                        <textarea  id="q25" name="q25" class="form-control"  maxlength="3500"  required></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q26">What did you like most about your job?</label>
                                            <textarea id="q26" name="q26" class="form-control" maxlength="3500" required></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q27">What was the least exciting part of your job?</label>
                                            <textarea id="q27" name="q27" class="form-control" maxlength="3500" required></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q28">What makes BNU a good place to work?</label>
                                            <textarea id="q28" name="q28" class="form-control" maxlength="3500" required></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q29">What makes BNU a poor place to work?</label>
                                            <textarea id="q29" name="q29" class="form-control" maxlength="3500" required></textarea>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="q30">In your opinion have there been adequate career
                                                progression opportunities
                                                available within BNU?</label>
                                            <input type="text" id="q30" name="q30" class="form-control"  required>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="form-group">
                                <label for="opportunities">What types of career opportunities are important to you? (Select
                                    all that
                                    apply)</label><br>
                                <input type="checkbox" id="promotional_opportunities" name="q31[]"
                                    value="Promotional Opportunities">
                                <label for="promotional_opportunities">Promotional Opportunities</label><br>
                                <input type="checkbox" id="position_rotations" name="q31[]"
                                    value="Position Rotations">
                                <label for="position_rotations">Position Rotations</label><br>
                                <input type="checkbox" id="special_projects" name="q31[]" value="Special Projects">
                                <label for="special_projects">Special Projects</label><br>
                                <input type="checkbox" id="other" name="q31[]" value="Other">
                                <label for="other">Other</label><br>
                                <textarea id="other_description" name="other_description" class="form-control"
                                    placeholder="If you selected other, please elaborate" maxlength="3500"></textarea>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: center;">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Your form has been submitted successfully.
                </div>
                <div class="modal-footer">
                    <button type="click" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@stop
@section('javascript')

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {
            $("#address").focus();
            $('#staffExitfeedbackForm').on('submit', function(event) {
                event.preventDefault();
                // Show confirmation dialog
                if (confirm('Are you sure you want to submit this form?')) {
                    $.ajax({
                        url: '{{ route('lower-staff-exist-interview-submit') }}',
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            if (response.message ===
                                'You cannot request another one until the first one is completed.'
                            ) {
                                $('#errorModal').modal('show');
                            } else {
                                $('#successModal').modal('show');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            // Handle AJAX errors (optional)
                            console.error('AJAX Error:', textStatus, errorThrown);
                        }
                    });
                }
            });
            $('#successModal').on('click', function() {
                location.reload(); // Reload the page
            });

        });
    </script>


@endsection
