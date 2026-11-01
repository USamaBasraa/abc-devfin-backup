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
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >Car Sticker Report</h3>
    </div>

<div class="card-header" style="width:100%;">
<div class="row" style="margin-left:auto; margin-right:auto; padding:10px;">
    <div class="col-12 col-md-12 text-md-left text-center" style="min-height:150px;">
        <form  id="reportForm">
            @csrf
            <div class="row">
                {{-- <div class="form-group col-sm-3">
                    <label for="session_sel">From Session</label>
                    <select name="from_session" id="session_from" class=" form-control"   >
                        <option value="">All Session</option>
                        @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div> --}}
                {{-- <div class="form-group col-sm-3">
                    <label for="session_sel">To Session</label>
                    <select name="to_session" id="session_to" class=" form-control"   >
                        <option value="">All Session</option>
                        @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div> --}}
                {{-- <input type="hidden" name="cwa_report_type" id="teacher" class="form-control" readonly value="1" > --}}
                <div class="form-group col-sm-3">
                    <label for="report_type_sel">Report Type</label>
                    <select name="report_type" id="report_type_sel" class="form-control" >
                        <option value="" disabled selected>--- Select Report Type ---</option>
                        <option value="summary">School Wise Summary Report</option>
                        <option value="studentlist" >Student List</option>
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label for="school_sel">School</label>
                    <select name="school" id="school_sel" class="form-control" >
                        <option value="" disabled selected>--- Select School ---</option>
                        @foreach ($schools as $row)
                            <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                
                
                <div class="form-group col-sm-3">
                    <label for="from">From</label>
                    <input type="date" name="from" id="from" class="form-control" required>
                </div>
                
                <div class="form-group col-sm-3">
                    <label for="to">To</label>
                    <input type="date" name="to" id="to" class="form-control" required>
                </div>
                
                
                

                {{-- <div class="form-group col-sm-3">
                    <label for="department">Department</label>
                    <select name="department" id="department" class="form-control" onchange="getTeacher();" >
                    </select>
                </div> --}}
                <!-- <div class="form-group col-sm-3">
                        <button type="submit" class="btn btn-info btn-lg" style="margin-top: 25px;" type="button"  ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> GET PDF REPORT</button>
                </div> -->
        </div>
        <div class="row">
        <div class="form-group col-sm-12">
                <button type="button"   onclick="submitForm()" class="btn btn-info btn-lg btn-block"> Get Result</button>
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
        fetch(`/vc-car-pooling-report`, {
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
        fetch(`/vc-car-pooling-report-studentwise`, {
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
                            <th scope="col">Username</th>
                            <th scope="col">Applicant ID</th>
                            <th scope="col">Vehicle No</th>
                            <th scope="col">School</th>
                            <th scope="col">Parking Type</th>
                            <th scope="col">NBS Status</th>
                       
                        </tr>
                    </thead>
                    <tbody style="font-size: 18px;">
                        ${data.map((element) => `
                            <tr>
                                <td>${sr++}</td>
                                <td>${element.vr_username}</td>
                                <td>${element.aai_id}</td>
                                <td>${element.vr_vehicle_no}</td>
                                <td>${element.school_name}</td>
                                <td>${element.vr_parking}</td>
                                <td>${element.check_nbs}</td>
                       
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
    XLSX.writeFile(wb, 'RepeatCourse_Report.xlsx');
}




</script>
@stop
