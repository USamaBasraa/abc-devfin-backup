
star
@php
     $attendance_mark = $attendance_present+$attendance_absent;
@endphp
<div class="modal fade" tabindex="-1" role="dialog" id="attendance_Modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="model_label"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="id_attendance_model"> </div>
            </div>
            <div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php
$desktop_col = 12 ;
$tablet_col  = 12 ;
$mobile_col  = 12
?>

<div class="container" style="border:solid 0px #0C0;padding-left:2%; margin-left:auto; margin-right:auto; width:100%;">
    @php
    $sr_no=1;
    $show_hide_main='';
    $show_hide_sub='none';
    @endphp
    @foreach($student_data as $row_student_data)
    @php
            $co_total_classes  =    $row_student_data->co_total_classes;
            $co_req_classes    =    $row_student_data->co_req_classes;
            $req_classes       =    ($co_total_classes*$co_req_classes/100);
            $allowed_absents   =    $co_total_classes-$req_classes;
            $student_absents   =    $row_student_data->absents-$row_student_data->exempted;
            $display_row       =    ($sr_no==6?'style="display:none;"':'');

        @endphp
    <div class="row">
        <div class="col-12 col-md-2 col-xl-2">{{$sr_no}}</div>
        <div class="col-12 col-md-2 col-xl-2">
            {{$row_student_data->sai_enrollment_no}}
           <input type="hidden" name="sai_enrollment_no[{{$sr_no-1}}]"  id="sai_enrollment_no" value="{{$row_student_data->sai_enrollment_no}}" >
            <input type="hidden" name="sc_co_id[{{$sr_no-1}}]"  id="sc_co_id" value="{{$row_student_data->sc_co_id}}" >
        </div>
        <div class="col-12 col-md-2 col-xl-2">
            <img src='/studentpics/{{$row_student_data->sai_enrollment_no}}.jpg' class='rounded-circle' width='30px' height="30px">
        </div>
        <div class="col-12 col-md-4 col-xl-4" style="text-align: left;">
            <table width="100%" border="1">
                <tr height="10px">
                    <td align="left">
                        {{$row_student_data->spi_name}}
                    </td>
                    <td align="right">
                                @if(($row_student_data->sai_student_status=='Active' or $row_student_data->sai_student_status=='Expected-Graduate' ) && $row_student_data->ssr_semester_status =='Active' )
                                    @if($row_student_data->sc_status!='A')
                                        <i class="fa fa-book" aria-hidden="true" style="font-size:15px; color: red;">  </i>
                                    @else
                                        <i class="fa fa-male" aria-hidden="true"  style="font-size:15px; color: green;">  </i>
                                    @endif
                                @else
                                    <i class="fa fa-male" aria-hidden="true" style="font-size:15px; color:red;">  </i>
                                @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-12 col-md-2 col-xl-2">
            @if($row_student_data->attendance_status==='A')
            @php
                $chek_present = '';
                $chek_absent  = 'checked="checked"';
            @endphp
        @elseif($row_student_data->attendance_status==='P')
            @php
                $chek_present = 'checked="checked"';
                $chek_absent  = '';
            @endphp
        @else
            @php
                    $chek_present='';
                    $chek_absent='';
            @endphp
        @endif
        @if($row_student_data->class_status=="YES")
                 @if($attendance_mark>0)
                     @php
                         $disabled = 'disabled="disabled"';
                     @endphp
                 @else
                     @php
                         $disabled = '';
                         $chek_present = 'checked="checked"';
                     @endphp
                 @endif
                     @if(($row_student_data->sai_student_status=='Active' or $row_student_data->sai_student_status=='Expected-Graduate' ) && $row_student_data->ssr_semester_status =='Active')
                         @if($row_student_data->sc_status!='A')
                             @php
                                 $show_hide_main='none';
                                 $show_hide_sub='';
                                 $chek_present = '';
                                 $chek_absent  = 'checked="checked"';
                             @endphp
                         @endif
                     @else
                         @php
                            $show_hide_main='none';
                            $show_hide_sub='';
                            $chek_present = '';
                            $chek_absent  = 'checked="checked"';
                         @endphp
                    @endif
         @else
             @php
                 $disabled = 'disabled="disabled"';
             @endphp
         @endif
    <input type="radio" class="ca_attendance"   name="ca_attendance[{{$sr_no-1}}]" value="A"  {{$chek_absent}} {{$disabled}} style="display:{{$show_hide_main}};">
    <input type="radio" class="ca_attendance"   name="not_active_case[{{$sr_no-1}}]" value="A"  {{$chek_absent}} disabled="disabled" style="display:{{$show_hide_sub}};">
     Absent
    <input type="radio" class="ca_attendance"  name="ca_attendance[{{$sr_no-1}}]" value="P" {{$chek_present}} {{$disabled}}   style="display:{{$show_hide_main}};">
    <input type="radio" class="ca_attendance"  name="not_active_case[{{$sr_no-1}}]" value="P" {{$chek_present}} disabled="disabled"  style="display: {{$show_hide_sub}};">
    Present
     @php
         $show_hide_main    ='';
         $show_hide_sub     ='none';
     @endphp
        </div>
    </div>


     @endforeach

<script type="text/javascript">
            $(document).ready( function () {
                        //$('#table_id').DataTable();
                        var table = $('#table_id').DataTable({
                        'responsive': true,
                        "searching": false,
                        "paging": false,
                        "bInfo": false,
                        "orderable":true,
                        "fixedHeader": true,
                        "scrollY": 350,
                        "scrollX": true,
                         "height":10,

                        });
            } );
            function load_attendance_model(get_enrollment_no) {
                $('#attendance_Modal').modal('show');
                $.ajax({
                    type:'GET',
                    url:"{{ URL::route('student_profile') }}/"+get_enrollment_no,
                    //  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},  /'"/"+sai_id;
                    success:function(data){
                        // alert(data);
                        $("#id_attendance_model").html(data);
                    }
                });
            };
            function load_attendance_record(sc_co_id,get_enrollment_no,attendance_status,get_course_name) {
                $('#attendance_Modal').modal('show');
                $("#model_label").html(get_course_name);
                //var get_course_text = $( "#myselect option:selected" ).text();;
                $.ajax({
                    type:'GET',
                    url:"{{ URL::route('student_course_attendance') }}/"+sc_co_id+"/"+get_enrollment_no+"/"+attendance_status,
                    //  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},  /'"/"+sai_id;
                    success:function(data){
                        // alert(data);
                        $("#id_attendance_model").html(data);
                    }
                });
            };

</script>
