
@extends('layouts.app')

@section('title')
    BNU CMS
@stop
@section('content')
<style>


.modal-body1{
   position: relative;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    padding: 1rem;
    height: 520px;
    overflow-y: auto;

}

</style>


    <div class="card">
        <div class="card-header bnu-header"><span style="color:white;">Attendance Management</span></div>
        <div class="card-body">
            <form id="filterForm">
            <div class="form-group row">
                <div class="col-md-3 col-sm-4 col-6">

                    <label class="control-label" for="session">Session :</label>

                    <select name="session" id="session" class=" form-control"  >
                        <option id="nullVal" value=""></option>
                        @foreach ($sessions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-4 col-6">
                    <label class="control-label" for="school">School :</label>
                    <select name="school" id="school" class=" form-control" >
                        <option value=""></option>
                        @foreach ($schools as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-4 col-6">
                    <label  class="control-label" for="department" >Dept. :</label>
                    <select name="department" id="department" class="form-control">
                    </select>
                </div>
                <div class="col-md-3 col-sm-4 col-6">
                    <label  class="control-label" for="teacher" >Teacher :</label>
                    <select name="teacher" id="teacher" class="form-control">
                        <option value=""></option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-5 col-6">
                    <label class="control-label" for="teacher" >Course :</label>
                    <select name="courses" id="courses" class="form-control" >
                        <option value=""></option>
                    </select>
                </div>
                <div class="col-md-3 col-sm-5 col-6">
                    <label class="control-label" for="teacher" >Student Id :</label>
                    <input type="text" class="form-control" name="student_id" id="student_id">
                </div>
            </div>
        </form>
        <div style="margin-bottom: 20px;"></div>
        <table id="attendanceManagement" class="table table-bordered display responsive nowrap">
            <thead style="background-color: #2F3B4D;">
            <tr>
                <th class="tab-title">Enrol. No.</th>
                <th class="tab-title">Student Name</th>
                <th class="tab-title-center">Picture</th>
                <th class="tab-title-center">Presents</th>
                <th class="tab-title-center">Absents </th>
                <th class="tab-title-center">Action </th>
            </tr>
        </table>
        </div>
    </div>

    <div class="modal" id="exemptionMainModal" tabindex="-1" role="dialog" aria-labelledby="exemptionMainModal" aria-hidden="true" style="">
        <div class="modal-dialog" style="max-width: 640px!important;" >
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Attendance Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                {{-- <div class="modal-header">
                    <h4 class="modal-title">Attendance Description</h4>
                    <textarea class="form-control" name="attendance_description" id="attendance_description"> </textarea>
                </div> --}}
                <div class="modal-body1">
                    <h2 id="showStudentDetails"></h2>
                    <table id="attendanceManagementDetails" class="table table-bordered display responsive nowrap">
                        <thead class="bnu-header">
                        <tr>
                            <th class="tab-title-center">ID </th>
                            <th class="tab-title-center">Status </th>
                            <th class="tab-title-center">Date </th>
                            <th class="tab-title-center">HOD Approval Status</th>
                            <th class="tab-title">Remarks</th>
                            <th class="tab-title">Action</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="exemptionModal" tabindex="-1" role="dialog" aria-labelledby="exemptionModal" aria-hidden="true" style="">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Attendance Exemption</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form role="form" class="f1" id="exemptionForm" name="exemptionForm" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="id" name="id">
                        <input type="hidden" id="sel_sai_id" name="sel_sai_id">
                        <input type="hidden" id="sc_co_id" name="sc_co_id">
                        <div class="form-group">
                            <label for="ca_hod_attendance">HOD Approval Status</label>
                            <select name="ca_hod_attendance" id="ca_hod_attendance" class="form-control">
                                <option value="EX">Exempted</option>
                                <option value="P">Present</option>
                                <option value="A">Absent</option>
                            </select>
                            <small id="ca_hod_attendance_error" class="text-danger" style="display:none;"></small>
                        </div>
                        <div class="form-group">
                            <label for="ca_hod_remarks">Remarks</label>
                            <input type="text" class="form-control" id="ca_hod_remarks" name="ca_hod_remarks" placeholder="Exemption Details">
                            <small id="ca_hod_remarks_error" class="text-danger" style="display:none;"></small>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <div id="form-errors"></div>
                    <div class="alert alert-danger print-error-msg" style="display:none">
                        <ul></ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @endsection
    @section('javascript')
    <script>
        $(document).ready(function() {
            $('select[name="session"]').on('change', function() {
                $('select[name="school"]').val("");
                $('select[name="department"]').empty();
                $('select[name="teacher"]').empty();
                $('select[name="courses"]').empty();
            });
            $("#student_id").keyup(function() {
              //  alert('ok');
                var student_id = $('input:text[name=student_id]').val();
                if(student_id.length>8){
                    openExemptionPopup(student_id);
                      console.log(student_id);
                }

            });
            $('select[name="school"]').on('change', function() {
                var school_id = $(this).val();
                if(school_id) {
                    $.ajax({
                        url: '{{url('departments/get/')}}/'+school_id,
                        type: "GET",
                        dataType: "json",
                        success:function(data) {
                            $('select[name="department"]').empty();
                            $('select[name="department"]').append('<option value=""> </option>');
                            $.each(data, function(key, value) {
                                $('select[name="department"]').append('<option value="'+ key +'">'+ value +'</option>');
                            });
                            $('select[name="teacher"]').empty();
                            $('select[name="courses"]').empty();
                        }
                    });
                }else{
                    $('select[name="department"]').empty();
                    $('select[name="teacher"]').empty();
                    $('select[name="courses"]').empty();
                }
            });

            $('select[name="department"]').on('change', function() {
                var school_id = $('#school').val();
                var dept_id = $('#department').val();
                var sess_id = $('#session').val();
                if(sess_id) {
                    $.ajax({
                        url: '{{url('CoursesOffered/getFaculty')}}/'+sess_id+'/'+school_id+'/'+dept_id,
                        type: "GET",
                        dataType: "json",
                        success:function(data) {
                            $('select[name="teacher"]').empty();
                            $('select[name="teacher"]').append('<option value=""> </option>');
                            $.each(data, function (key, value) {
                                $('select[name="teacher"]').append('<option value="'+ value.emp_email +'">'+ value.emp_full_name +'</option>');
                            });
                            $('select[name="courses"]').empty();
                        }
                    });
                }else{
                    $('select[name="teacher"]').empty();
                    $('select[name="courses"]').empty();
                }
            });

            $('select[name="teacher"]').on('change', function() {
                var dept_id = $('#department').val();
                var sess_id = $('#session').val();
                var email = $(this).val();
                if(sess_id) {
                    $.ajax({
                        url: '{{url('facultyCourses/get')}}/'+sess_id+'/'+dept_id+'/'+email,
                        type: "GET",
                        dataType: "json",
                        success:function(data) {
                            $('select[name="courses"]').empty();
                            $('select[name="courses"]').append('<option value=""> </option>');
                            $.each(data, function (key, value) {
                                $('select[name="courses"]').append('<option value="'+ value.co_id +'">'+value.course_code+' '+value.course_cd_name +'</option>');
                            });
                        }
                    });
                }else{
                    $('select[name="courses"]').empty();
                }
            });

            $('select[name="courses"]').on('change', function() {
                var table = $('#attendanceManagement').DataTable();
                table.destroy();
                var co_id = $(this).val();
                if(co_id) {
                    $('#attendanceManagement').DataTable( {
                        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                        "ajax": {
                            "url": '{{url('attendance/management/grid')}}/'+co_id,
                            "dataSrc": ""
                        },
                        "columnDefs": [ {
                            "targets": 0,
                            "className": "text-center",
                            "width": "4%"
                        },{
                            "targets": 2,
                            "className": "text-center",
                        },{
                            "targets": 3,
                            "className": "text-center",
                        },{
                            "targets": 4,
                            "className": "text-center",
                        },{
                            "targets": 5,
                            "className": "text-center",
                        }],
                        "columns": [
                            { "data": "sai_enrollment_no" },
                            { "data": "spi_name" },
                            {
                                'data': 'sai_enrollment_no',
                                'render': function (data, type, row) {
                                    var imgPath = "{{asset("studentpics/")}}";
                                    var finalPath = imgPath+'/'+data+'.jpg';
                                    return '<object data="'+finalPath+'" type="image/png" height="30px"><img src="{{asset("images/dummy.jpg")}}" title="No Image Found!" height="30px" /></object>';
                                }
                            },
                            { "data": "presents" },
                            { "data": "absents" },
                            {
                                'data': 'sai_enrollment_no',
                                'render': function (data, type, row) {
                                    return '<button class="btn btn-primary" onclick="openExemptionPopup(\''+data+'\')">Details</button>';
                                }
                            }
                        ],
                        "createdRow": function( row, data, dataIndex){
                            var allowed_classes = (data.co_req_classes/100)*data.co_total_classes;
                            var allowed_abs = data.co_total_classes-allowed_classes;

                            if(data.co_total_classes>0){
                                if (data.total_abs==allowed_abs)
                                {
                                    $(row).addClass('yellow-row');
                                }else if(data.total_abs>allowed_abs)
                                {
                                    $(row).addClass('red-row');
                                }else{
                                }
                            }
                        }
                    } );
                }
            });
        });

        $('#exemptionMainModal').on('hidden.bs.modal', function () {
            $('#attendanceManagement').DataTable().ajax.reload();
        });

        function openExemptionPopup(sai_id)
        {
            var sel_co_id = $("#courses").val();
            var url = '{{url('attendance/management/details/')}}/'+sel_co_id+"/"+sai_id;
            var table = $('#attendanceManagementDetails').DataTable();
            table.destroy();
            $('#attendanceManagementDetails').DataTable( {
                lengthMenu: [[35, 10, 35, -1], [35, 10, 35, "All"]],
                "searching": false,
                "ajax": {
                    "url": url,
                    "dataSrc": ""
                },
                "columnDefs": [{
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                },{
                    "targets": 1,
                    "className": "text-center",
                    "width": "4%"
                },{
                    "targets": 2,
                    "className": "text-center",
                },{
                    "targets": 3,
                    "className": "text-center",
                },{
                    "targets": 4,
                    "className": "text-left",
                }],
                "order": [[ 2, 'desc' ]],
                "columns": [
                    { data: 'id'},
                    { data: 'ca_attendance'},
                    { data: 'ca_attendance_date'},
                    { data: 'ca_hod_attendance'},
                    { mRender: function (data, type, row) {
                            var remarks = ''+row.course_name;
                            if(remarks.length>16){
                                if(remarks!='null'){
                                    var res = remarks.substring(0, 16);
                                    return res = res+' . .';
                                }else{
                                    return '';
                                }
                            }else{
                                if(remarks!='null'){
                                    return remarks;
                                }else{
                                    return '';
                                }
                            }
                        }
                    },
                    { mRender: function (data, type, row) {
                            return '<button class="btn btn-primary" onclick="openExemptionDetails('+row.id+',\''+row.ca_hod_attendance+'\',\''+row.ca_hod_remarks+'\',\''+sai_id+'\',\''+row.co_id+'\')">Exempt</button>'
                        }
                    }
                ]
            } );
            $("#exemptionMainModal").modal("show");
        };


        function openExemptionDetails(id,ca_hod_attendance,ca_hod_remarks,sai_id,co_id){

            var exempted;
            var remarks;
            $('#id').val(id);
            if(ca_hod_attendance=="Exempted"){
                exempted = "EX"
            }else{
                exempted = "0"
            }
            if(ca_hod_remarks!="null"){
                remarks = ca_hod_remarks
            }else{
                remarks = ""
            }
            $('select[id="ca_hod_attendance"]').val(exempted);
            $('#ca_hod_remarks').val(remarks);
            $('#sel_sai_id').val(sai_id);
            $('#sc_co_id').val(co_id);
            $("#exemptionModal").modal("show");
        }
        $("#exemptionForm").submit(function(e)
        {
            e.preventDefault();
            var url = "{{url('attendance/management/update')}}";
            var formData = new FormData($(this)[0]);
            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: url,
                data: new FormData(this),
                dataType: "json",
                processData: false,
                contentType: false,
                success : function(json){
                    if(json.success){
                        $('#errMsg').html('');
                        $('#exemptionModal').modal('hide');
                        $("#exemptionMainModal").removeClass("fade");
                        $('#attendanceManagementDetails').DataTable().ajax.reload();
                    }else{
                        $('#errMsg').html(json.error_details)
                        setTimeout(function () {
                            $('#errMsg').html('')
                        }, 5000);
                       
                    }
                },
                error: function(data,status, response) {
                    var errorData = jQuery.parseJSON(data.responseText);
                    var error = errorData.errors;
                    $("#ca_hod_remarks_error").css('display','none');
                    $("#ca_hod_attendance_error").css('display','none');

                    if(error.ca_hod_attendance){
                        $("#ca_hod_attendance_error").css('display','block');
                        $("#ca_hod_attendance_error").html(error.ca_hod_attendance);
                    }
                    if(error.ca_hod_remarks){
                        $("#ca_hod_remarks_error").css('display','block');
                        var hod_remarks = error.ca_hod_remarks;
                        $("#ca_hod_remarks_error").html(hod_remarks);
                    }
                }
            });

        });

    </script>
@stop
