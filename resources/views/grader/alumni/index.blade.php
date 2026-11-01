
@extends('layouts.app')
@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">

<div class="row" style="margin:5px;">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <form method="post" id="alumni_form"  target="_blank" >
                <div class="card-body pb-2">
                    <div class="row">
                    <div class="form-group col-3">
                        <label for="inputName" class="form-label mt-2 d-flex flex-row">Session:</label>
                            <select class="form-select select2" required onchange="selectSession(this)"  name="session" id="session" style="width:100%;height: 40px;border-radius: 5px;border: 1px solid gray;">
                                <option value="" default>Select Session</option>
                                @foreach ($resSess as $ses)
                                <option value="{{$ses->sess_id}}">{{$ses->sess_name}}</option>
                                @endforeach
                            </select>
                    </div>
                    <div class="form-group col-3">
                        <label for="inputName" class="form-label mt-2 d-flex flex-row">School:</label>
                        <select  class="form-select select2"  onchange="selectSchool()" required name="school" id="school" style="width:100%;height: 40px;border-radius: 5px;border: 1px solid gray;">
                            <option value="" default>Select School</option>

                            @foreach ($resSchools as $ses)
                            <option value="{{$ses->school_id}}">{{$ses->school_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-3">
                        <label for="inputName" class="form-label mt-2 d-flex flex-row">Department:</label>

                        <select  class="form-select select2"  onchange="selectDept()" required name="department" id="department" style="width:100%;height: 40px;border-radius: 5px;border: 1px solid gray;">
                            <option value="" default>Select Department</option>
                            @foreach ($resDeprtments as $ses)
                            <option value="{{$ses->dept_id}}">{{$ses->dept_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-3">
                        <label for="inputName" class="form-label mt-2 d-flex flex-row">Program:</label>
                        <select  class="form-select select2" onchange="loadStudents(this)" required name="program" id="program" style="width:100%;height: 40px;border-radius: 5px;border: 1px solid gray;">

                        </select>
                    </div>
                </div>

                    {{-- <div class="form-group">
                        <div class="col-md-12 d-flex flex-row justify-content-end">
                            <button type="button" onclick="loadStudents()" class="btn btn-primary mr-2"><i class="fa fa-refresh mr-2" aria-hidden="true"></i></i>
                                Search</button>
                             <button type="button" class="btn btn-primary"><i class="fa fa-refresh mr-2" aria-hidden="true"></i>
                                Clear</button>
                        </div>
                    </div> --}}
                </div>

                </form>
        </div>
    </div>
</div>
<div class="row" style="margin:5px;">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"> Alumni List</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-responssive table-striped table-bordered "  id="index">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Enrollment#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Program</th>

                        </tr>
                    </thead>
                    <tbody id="alumni_body">

                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
@stop
@section('javascript')
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>


<script>

$(document).ready(function(){
    $(".select2").select2({
        // allowClear: true
    });
//     $("#index").DataTable({
//         "bInfo": false,
//     aLengthMenu: [
//         [25, 50, 100, 200, -1],
//         [25, 50, 100, 200, "All"]
//     ],
//     iDisplayLength: -1
// });
// var dataTable = $("#index").DataTable({
//     iDisplayLength: -1,
//     binfo:false

// });

var dataTable = $('#index').DataTable();


});
function selectSession(){

    // $("#school").prop("selectedIndex", -1).trigger("change");
    // $("#department").prop("selectedIndex", -1).trigger("change");
    // $("#program").prop("selectedIndex", -1).trigger("change");

    // $('#school option:eq(0)').prop('selected',true);
    // $("#school").val('').trigger('change');
    $('#school').val('').select2();
    $("#department").empty();
    $("#program").empty();

    // $("#school").val('');
    // $("#department").val('');
    // $("#program").val('');
    loadStudents();
}


function selectSchool(){


    $("#department").empty();
    $("#program").empty();
    loadDepartments();
}



function selectDept(){
    $("#program").empty();
    loadProgram();
}
function loadProgram(){


        data = {
            session: $("#session").val(),
            school: $("#school").val(),
            department:  $("#department").val()
        }

        fetch('api/alumni/get_programs', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
                body: JSON.stringify(data)
            })
            .then(response => response.json())


            .then(dat => {
                $('#program').empty();
                $('#program').append('<option value="" selected> Select Program</option>');
                dat.forEach(item => {
                    $('#program').append('<option value="' + item.prog_id + '">' + item.prog_name + '</option>');

                    });

            })
            .catch(error => {
                // Handle errors here
                // console.error('Error:', error);
            });

            loadStudents();


}
function loadStudents(){
    // var newData = ['John Doe', 'Software Engineer', '$80,000', '$80,000'];
    //                     $('#index').DataTable().row.add(newData);

    //                     $('#index').DataTable().draw();
    // if($(ele).id=='session'){
        // alert("ddd");/

    //}
    data = {
            session: $("#session").val(),
            school: $("#school").val(),
            department:  $("#department").val(),
            program :  $("#program").val()
        }

        $.ajax({
            url: 'api/alumni/get_alumnis',
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: JSON.stringify(data),
            dataType: 'json',
            success: function(dat) {
                // Clear DataTable
                $('#index').DataTable().clear().draw();
                // Add new rows to the DataTable
                dat.forEach((item, index) => {
                    var newData = [index+1, item.sai_enrollment_no, item.spi_name,item.sai_api_email, item.sai_degreecode];
                    $('#index').DataTable().row.add(newData);
                });

                // Draw the updated table
                $('#index').DataTable().draw();

            },
            error: function(error) {
                // Handle errors here
                // console.error('Error:', error);
            }
        });

        // fetch('api/alumni/get_alumnis', {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json',
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //     },
        //         body: JSON.stringify(data)
        //     })
        //     .then(response => response.json())


        //     .then(dat => {

        //         // $('#alumni_body').empty();
        //         // console.log(dat.length());
        //         // alert(dat.length());
        //         // if(dat.length>0){
        //             dataTable.clear().draw();

        //             dat.forEach((item,index) => {

        //             });
        //         // Add new rows to the DataTable




        //             // dataTable.draw();

        //             // dat.forEach((item,index) => {


        //             // $('#alumni_body').append('<tr><td class="text-center">'+(index+1)+'</td><td class="text-center">'+item.sai_enrollment_no+'</td><td>'+item.spi_name+'</td><td>'+item.sai_degreecode+'</td></tr>');

        //             // });

        //         // }else{
        //         //     $('#alumni_body').append('<tr><td colspan="4" class="text-center">No data available in table</td></tr>');
        //         // }

        //     })
        //     .catch(error => {
        //         // Handle errors here
        //         // console.error('Error:', error);
        //     });
            // setTimeout(() => {
            //     $("#index").DataTable();


            // },2100);

//             $("#index").DataTable({
//                 "bInfo": true,
//                 aLengthMenu: [
//                     [25, 50, 100, 200, -1],
//                     [25, 50, 100, 200, "All"]
//                 ],
//                 iDisplayLength: -1
// });

}
function loadDepartments(){


    data = {
            dept_school_id: $("#school").val()
        }

        fetch('api/alumni/get_dept', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
                body: JSON.stringify(data)
            })
            .then(response => response.json())


            .then(dat => {
                console.log(dat);
                $('#department').empty();
                $('#department').append('<option value="" selected> Select Department</option>');
                dat.forEach(item => {
                    $('#department').append('<option value="' + item.dept_id + '" data-id="'+item.dept_id+'">' + item.dept_name + '</option>');

                    });

            })
            .catch(error => {
                // Handle errors here
                // console.error('Error:', error);
            });
            loadProgram();
}

</script>
@stop
