@extends('template')
@section('content')
<div class="container-fluid">
      <div class="yoo-uikits-heading">
        <h2 class="yoo-uikits-title">Dashboard</h2>
      </div>
    </div>
    <div class="container-fluid">





    <div class="modal-dialog modal-lg">
        <div class="modal-header">
            <div style="float: left; width: 100%;">
                <div style="float: left; width: 76%; "><h4 class="modal-title" id="attendance_model">&nbsp;</h4></div>
                <div style="float: right; width: 4%;"><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                <div style="float: right; width: 10%; text-align: right; color: white; !important;">&nbsp;</div>
            </div>
        </div>
        <div class="modal-content" id="attendance_contents">
        </div>
    </div>













      
    
    <div class="yoo-card yoo-style1">
            <div class="yoo-card-heading">
              <div class="yoo-card-heading-left">
                <h2 class="yoo-card-title">Striped rows</h2>
              </div>
              <div class="yoo-card-heading-right">
                <button type="button" class="yoo-card-btn" data-toggle="modal" data-target="#stripedRows"><ion-icon name="code" role="img" class="md hydrated" aria-label="code"></ion-icon></button>
              </div>
            </div>
            <div class="yoo-card-body">
              <div class="yoo-padd-lr-20">
                <div class="yoo-height-b20 yoo-height-lg-b20"></div>
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th scope="col">ID</th>
                      <th scope="col">First</th>
                      <th scope="col">Last</th>
                      <th scope="col">Handle</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row">156</th>
                      <td>Mark</td>
                      <td>Otto</td>
                      <td>@mdo</td>
                    </tr>
                    <tr>
                      <th scope="row">257</th>
                      <td>Jacob</td>
                      <td>Thornton</td>
                      <td>@fat</td>
                    </tr>
                    <tr>
                      <th scope="row">358</th>
                      <td>Larry</td>
                      <td>the Bird</td>
                      <td>@twitter</td>
                    </tr>
                  </tbody>
                </table>
                <div class="yoo-height-b20 yoo-height-lg-b20"></div>
              </div>
            </div>
          </div>



      </div>
      
      
      
@endsection
@section('javascript')
<script>

  var co_id = '<?=$coID?>';

  $(document).ready(function(){
    // openModalCourseAttendance(get_course_name,co_id)
    $("#attendance_contents").html('');
            //$('#courseAttendanceModal').modal('show');///attendance_popup.blade.php
            $("#attendance_model").html('get_course_name');
            // $("#show_heading_criteria").show();
            $.ajax({
                url:'{{url('api/attendance/attendance')}}/'+co_id,
                type: "GET",
                success: function (data) {
                    $("#attendance_contents").html(data);
                }
            });





  });


</script>
@stop