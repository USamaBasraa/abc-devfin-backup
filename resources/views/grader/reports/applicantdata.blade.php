@extends('layouts.app')
@section('content')
<style type="text/css">
.canvasjs-chart-canvas{
    color:white !important;
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

<div class="card">
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >Applicant Data Report</h3>
    </div>

<div class="card-header" style="width:100%;">
<div class="row" style="margin-left:auto; margin-right:auto; padding:10px;">
    <div class="col-12 col-md-12 text-md-left text-center" style="min-height:150px;">
        <form  id="reportForm">
            @csrf
            <div class="row">
                <div class="form-group col-sm-6">
                    <label for="school_sel">Schools</label>
                    <select name="session" id="school_sel" class="form-control" >
                        <option value=""  selected>--- Select School ---</option>
                        @foreach ($schools as $row)
                            <option value="{{ $row->sess_id }}">{{ $row->sess_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" >
                        <option value=""  selected>--- Select Status ---</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Absent">Absent</option>
                        <option value="Not Joined">Not Joined</option>
                        <option value="Selected">Selected</option>
                        <option value="Waiting">Waiting</option>
                        <option value="Schedule Changed">Schedule Changed</option>
                        <option value="Left">Left</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="Qualification Status">Qualification Status</label>
                    <select name="q_status" id="q_status" class="form-control" >
                        <option value=""  selected>--- Select Status ---</option>
                        <option value="A_Level">A_Level</option>
                        <option value="Intermediate">Intermediate</option>
                      
                    </select>
                </div>
            </div>


              




              
        </div>
        <div class="row">
        <div class="form-group col-sm-12">
                <button type="button"   onclick="submitForm()" class="btn btn-info btn-lg btn-block"> GET Result</button>
        </div>
        </div>
    </form>
    <div id="dataDiv"   style="margin-top:20px;">

    </div>
</div>
</div>
</div>
</div>

@stop
@section('javascript')
<script>
function getPDF(){
         var  lastURL;//="reports/exam/cgpa-summary-report";
       // alert(lastURL+'&result-for=print');
       var data = $('#filterFormOne').serialize();
           window.open('/reports/exam/cgpa-summary-report?'+data);
          // /reports/exam/cgpa-summary-report

    }
function on_session_change(session){
        //console.log(session.value);
        getCourses();

    }


    function getRequest(endPoint,successBlock,failBlock){
        $.ajax({
            url: "{{url('/')}}/"+endPoint,
            type: "GET",
            dataType: "json",
            headers: {
                    Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                    Accept: 'application/json'
                },
            success:successBlock
        }).fail(failBlock);
    }
    function getDepartments(){
       // alert('dd');
        var school_id = $('#school_sel').val();
        getRequest('api/department/get?school_id='+school_id,function(res){
            if(res.status){
                var html = '<option value="">All Dept</option>';
                $.each(res.data,function( key, value ) {
                    html += '<option value="'+value.dept_id+'" >'+value.dept_name+'</option>';
                });
                $('select[name="department"]').html(html);
            }
        },function(res){
            alert('Some Error found!');
        });
    }

    function submitForm() {
    let form = document.getElementById('reportForm');
    let data = new FormData(form);
    if($('#report_type_sel').val()== 'summary'){
        fetch(`/applicant-report`, {
        method: 'POST',
        body: data
    }).then(response => response.json())
        .then(data => {
            makeDataHtml(data);
        })
        .catch(error => {
            // Uncomment the next line if you want to display fetch errors
            // displayValidationErrors(error);
            console.error('Error:', error);
        });
    }else{

        // if($('#school_sel').val() == null){
        //     alert('Please Select School');
        //     return false;
        // }
        fetch(`/applicant-report`, {
        method: 'POST',
        body: data
        }).then(response => response.json())
        .then(data => {
            makeDataHtml2(data);
        })
        .catch(error => {
            // Uncomment the next line if you want to display fetch errors
            // displayValidationErrors(error);
            console.error('Error:', error);
        });
    }

}

function makeDataHtml(data) {
    let showWarningLetter = document.getElementById('dataDiv');
    showWarningLetter.innerHTML = ''; // Clear previous content
    let sr = 1;
    if (data.length > 0) {
        let lettersHtml = `
        <button id="downloadExcel" class="btn btn-primary" onclick="downloadExcel()">
            <i class="fa fa-download"></i> Download Excel
        </button>

            <div class="table-responsive table-striped table-bordered" style="max-height: 500px; overflow-y: auto;">
                <table class="table">
                    <thead class="thead">
                        <tr style="font-size: 20px; font-weight: bold;">
                            <th scope="col">Sr#</th>
                            <th scope="col">School</th>
                            <th scope="col">Active Students</th>
                            <th scope="col">University Parking</th>
                            <th scope="col">Paid Parking</th>
                            <th scope="col">University Parking %Age</th>
                            <th scope="col">Paid Parking %Age</th>
                            <th scope="col">Scholarship Base</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 18px;">
                        ${data.map((element) => `
                            <tr>
                                <td>${sr++}</td>
                                <td>${element.school_name}</td>
                                <td>${element.active}</td>
                                <td>${element.university_parking_count}</td>
                                <td>${element.paid_parking_count}</td>
                                <td>${element.university_parking_percentage}</td>
                                <td>${element.paid_parking_percentage}</td>
                                <td>${element.nbs_count}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>

        `;
        showWarningLetter.innerHTML = lettersHtml;
    }
}

function makeDataHtml2(data) {
    let showWarningLetter = document.getElementById('dataDiv');
    showWarningLetter.innerHTML = ''; // Clear previous content
    let sr = 1;
    if (data.length > 0) {
        let lettersHtml = `
        <button id="downloadExcel" class="btn btn-primary" onclick="downloadExcel()">
            <i class="fa fa-download"></i> Download Excel
        </button>

            <div class="table-responsive table-striped table-bordered" style="max-height: 500px; overflow-y: auto;">
                <table class="table">
                    <thead class="thead">
                        <tr style="font-size: 20px; font-weight: bold;">
                            <th scope="col">Sr#</th>
                            <th scope="col">Applicant ID</th>
                            <th scope="col">Session</th>
                            <th scope="col">Name</th>
                            <th scope="col">Program</th>
                            <th scope="col">School</th>
                            <th scope="col">Last Qualification</th>
                            <th scope="col">Obtained Marks</th>
                            <th scope="col">Total Marks</th>
                            <th scope="col">Status</th>

                        </tr>
                    </thead>
                    <tbody style="font-size: 18px;">
                        ${data.map((element) => `
                            <tr>
                                <td>${sr++}</td>
                                <td>${element.aai_id}</td>
                                <td>${element.sess_name}</td>
                                <td>${element.api_name}</td>
                                <td>${element.prog_name}</td>
                                <td>${element.school_name}</td>
                                <td>${element.last_qualification}</td>
                                <td>${element.obtained_marks}</td>
                                <td>${element.total_marks}</td>
                                <td>${element.aai_status}</td>
                            

                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>

        `;
        showWarningLetter.innerHTML = lettersHtml;
    }
}

function downloadExcel() {
    const wb = XLSX.utils.table_to_book(document.querySelector('.table'));
    XLSX.writeFile(wb, 'ApplicantDataReport.xlsx');
}




</script>
@stop
