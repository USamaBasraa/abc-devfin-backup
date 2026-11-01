<?php
//$class_status           = "YES";//$slot_details[0]->today_class_status;
//$attendance_mark_status = "1";//$slot_details[0]->attendance_mark_status;
if(count($student_data)>0){
?>


						<div class="row">
							<div class="col-lg-4">
								<div class="card" style="margin-bottom:0 !important;">
									<div class="card-body">
										<div class="row">
											<div class="col mb-lg-4 mb-xl-0">
												<div class="mb-2 fs-18 text-muted">
													Total Classes
												</div>
												<h1 class="font-weight-bold mb-1"><?=$student_data[0]->co_total_classes;?></h1>
												{{-- <span class="text-primary"><i class="fa fa-arrow-up mr-1"></i> +1.4%</span> --}}
											</div>
											<div class="col col-auto">
												<div class="mx-auto chart-circle chart-circle-md chart-circle-primary mt-sm-0 mb-0" data-value="1" data-thickness="12" data-color="#4454c3">
													<div class="mx-auto chart-circle-value text-center fs-20">100%</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="card" style="margin-bottom:0 !important;">
									<div class="card-body">
										<div class="row">
											<div class="col mb-lg-4 mb-xl-0">
												<div class="mb-2 fs-18 text-muted">
													Delivered
                                                    <?php
                                                        $delivered = round(($student_data[0]->delivered_classess*100)/$student_data[0]->co_total_classes,2);
                                                    ?>
												</div>
												<h1 class="font-weight-bold mb-1"><?=$student_data[0]->delivered_classess;?></h1>
												{{-- <span class="text-success"><i class="fa fa-arrow-up mr-1"></i> +1.8%</span> --}}
											</div>
											<div class="col col-auto">
												<div class="mx-auto chart-circle chart-circle-md chart-circle-success mt-sm-0 mb-0" data-value="<?=($delivered/100);?>" data-thickness="12" data-color="#2dce89">
													<div class="mx-auto chart-circle-value text-center fs-20"><?=$delivered;?>%</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="card" style="margin-bottom:0 !important;">
									<div class="card-body">
										<div class="row">
											<div class="col mb-lg-4 mb-xl-0">
												<div class="mb-2 fs-18 text-muted">
													Remaining
                                                    <?php
                                                        $remaining = round((($student_data[0]->co_total_classes-$student_data[0]->delivered_classess)*100)/$student_data[0]->co_total_classes,2);
                                                    ?>
												</div>
												<h1 class="font-weight-bold mb-1"><?=$student_data[0]->co_total_classes-$student_data[0]->delivered_classess;?></h1>
												{{-- <span class="text-danger"><i class="fa fa-arrow-down mr-1"></i> -2.4%</span> --}}
											</div>
											<div class="col col-auto">
												<div class="mx-auto chart-circle chart-circle-md chart-circle-secondary mt-sm-0 mb-0" data-value="<?=($remaining/100);?>" data-thickness="12" data-color="#f7346b">
													<div class="mx-auto chart-circle-value text-center fs-20"><?=$remaining;?>%</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
                        <!-- Row-->
						<div class="row">
							<div class="col-md-12">
								<div class="card" style="margin-bottom: 0 !important;">
									<div class="card-body" style="padding: 0 !important;">
										<div class="table-responsive">
											<table class="table table-vcenter text-nowrap mb-0 border table-striped table-hover">
												<thead>
													<tr style="background-color:#D2D6F1; color: lavender !important; border:solid 2px #dde0f4; padding: 0%;">
														<th class="border-bottom-0 text-left">Student</th>
														<th>Absents/Present  </th>
                                                        <th class="text-center">Percentage</th>
                                                        <th class="text-center">Presents</th>
														<th class="text-center">Absents</th>
													</tr>
												</thead>
												<tbody>
                                                    @php
                                                    $sr_no=1;
                                                    $show_hide_main='';
                                                    $show_hide_sub='none';
                                                    $count_marks_attendance = 0;
                                                    $attendance_mark=0;
                                                    @endphp
                                                    @foreach($student_data as $row_student_data)
                                                    @php
                                                        if($row_student_data->attendance_percentage>80){
                                                                $graph_type_name    = "success";
                                                                $graph_color        = "2dce89";

                                                        }elseif($row_student_data->attendance_percentage>=75){
                                                                $graph_type_name    = "primary";
                                                                $graph_color        = "4454c3";
                                                        }else{
                                                                $graph_type_name    = "secondary";
                                                                $graph_color        = "f72d66";
                                                        }

                                                    $co_total_classes  =    $row_student_data->co_total_classes;
                                                    $co_req_classes    =    $row_student_data->co_req_classes;
                                                    $req_classes       =    ($co_total_classes*$co_req_classes/100);
                                                    $allowed_absents   =    $co_total_classes-$req_classes;
                                                    $student_absents   =    $row_student_data->absents-$row_student_data->exempted;
                                                    $display_row       =    ($sr_no==6?'style="display:none;"':'');
                                                    $file_path = '/students_pics/'.$row_student_data->sai_enrollment_no.'.jpg';
                                                    $total_student_absents = $row_student_data->total_classes-$row_student_data->presents;
                                                    if(!file_exists(public_path($file_path))){
                                                        $file_path = '/students_pics/noimage.png';
                                                    }
                                                    $color = 'white';
                                                    $row_status_class = 'attendance-status-safe';
                                                    $withdraw_expected_students = array();
                                                    $withdraw_expected[]='';
                                                    $withdraw_expected[]='';

                                                    if($total_student_absents >= $student_data[0]->allowed_absents){
                                                        $color = 'tomato';  //tomato
                                                        $row_status_class = 'attendance-status-danger';
                                                        //$withdraw_expected_students=[$row_student_data->spi_name];
                                                        array_push($withdraw_expected_students,$row_student_data->spi_name);
                                                       
                                                        foreach($withdraw_expected_students as $row){
                                                        //echo $row.'===Red Zone<br>';
                                                        $withdraw_expected[]=$row_student_data->sai_enrollment_no.' '.$row.' '.'<b>Red Zone</b>';
                                                        }

                                                        }elseif(($total_student_absents) ==($student_data[0]->allowed_absents-1)){
                                                        $color = 'yellow';
                                                        $row_status_class = 'attendance-status-warning';
                                                        $withdraw_expected_students[]=$row_student_data->spi_name;

                                                       // array_push($withdraw_expected_students,$row_student_data->spi_name);
                                                        foreach($withdraw_expected_students as $row){
                                                            $withdraw_expected[]=$row_student_data->sai_enrollment_no.' '.$row.' '.'<b >Warning (Yellow)</b>';

                                                       // echo $row.'===warning';
                                                        }

                                                    }   
                                                    if($row_student_data->sc_status == 'Block'){
                                                            $color = 'darkgrey';
                                                            $row_status_class = 'attendance-status-blocked';
                                                    }
                                                    @endphp
                                                   <tr class="{{$row_status_class}}" style="border-bottom:solid 2px #dde0f4;background-color:<?=$color?>">
                                                        <td class="d-flex">
                                                            <img class="avatar-lg rounded-circle mr-6" src="{{$file_path}}" alt="NA" style="width:70px; height:70px;">
                                                            <div class="ml-3 mt-2" style="margin-right:60px!important;">
                                                                <h3 class="mb-0 text-dark">{{ucwords($row_student_data->spi_name)}}</h3>
                                                                <p class="mb-0  fs-13 text-muted"  style="text-align: left; font-weight: bold;">{{$row_student_data->sai_enrollment_no}}</p>
                                                            </div>
                                                        </td>
                                                        <td class="text-right" style="display: none;">
                                                            {{-- <a class="btn btn-light" href="#"> View Details</a> --}}
                                                            <input type="radio" class="form-control" value="1" name="attendance" >Present
                                                            <input type="radio" class="form-control" value="1" name="attendance" >Absent
                                                        </td>
                                                            @php
                                                                $chek_present='';
                                                                $chek_absent='';
                                                                $disabled = '';
                                                                $tooltip='';
                                                            @endphp
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
                                                    @if($var_class_status=="ACTIVE")
                                                             {{-- @if($row_student_data->ca_status=="E")
                                                                 @php
                                                                     $disabled = 'disabled="disabled"';
                                                                 @endphp
                                                             @else
                                                                 @php
                                                                     $disabled = '';
                                                                     $chek_present = 'checked="checked"';
                                                                 @endphp
                                                             @endif --}}
                                                                 @if(($row_student_data->sai_student_status=='Active' or $row_student_data->sai_student_status=='Expected-Graduate' ) && $row_student_data->ssr_semester_status =='Active')
                                                                     @if($row_student_data->sc_status!='A')
                                                                         @php
                                                                             $show_hide_main='none';
                                                                             $show_hide_sub='';
                                                                             $chek_present = '';
                                                                             $chek_absent  = 'checked="checked"';
                                                                             $tooltip = 'Attendance is currently blocked due to an outstanding payment. Please clear the dues to proceed.';
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
                                                    <td align="center">
                                                        @if($var_class_status === "DELIVERED" && $row_student_data->ca_status=="")
                                                            <i class="fa fa-minus-circle" aria-hidden="true" style="color: blue; font-size: 16px; font-weight: bold;"> Not Marked </i>
                                                        @elseif($var_class_status === "SCHEDULED" && $row_student_data->ca_status=="")
                                                            <i class="fa fa-minus-circle" aria-hidden="true" style="color:darkcyan; font-size: 16px; font-weight: bold;">SCHEDULED</i>
                                                        @else
                                                            @if($row_student_data->ca_status=="E")
                                                                @php
                                                                    $count_marks_attendance++;
                                                                @endphp
                                                                    @if($row_student_data->attendance_status==='A')
                                                                    {{-- <button type="button" class="btn btn-danger">Absent</button> --}}
                                                                    <i class="fa fa-minus-circle" aria-hidden="true" style="color: red; font-size: 16px; font-weight: bold;"> Absent </i>

                                                                    @else
                                                                    {{-- <button type="button" class="btn btn-success">Present</button> --}}
                                                                    <i class="fa fa-hand-paper-o" aria-hidden="true" style="color: green; font-size: 16px; font-weight: bold;"> Present </i>

                                                                    @endif

                                                            @else
                                                           <table>
                                                                <tr>
                                                                    <td>
                                                            <input type="radio" class="ca_attendance form-control"   name="ca_attendance[{{$sr_no-1}}]" value="A"   {{$chek_absent}} {{$disabled}} style="display:{{$show_hide_main}};" onclick="update_attendance(<?=$row_student_data->ca_id;?>,'A')">
                                                            <input type="radio" class="ca_attendance form-control"   name="not_active_case[{{$sr_no-1}}]" value="A"  {{$chek_absent}} disabled="disabled" style="display:{{$show_hide_sub}};"  onclick="update_attendance(<?=$row_student_data->ca_id;?>,'A')" data-toggle="tooltip"  data-placement="top"  title="{{$tooltip}}">
                                                            Absent
                                                                    </td>
                                                                    <td>
                                                            <input type="radio" class="ca_attendance form-control"  name="ca_attendance[{{$sr_no-1}}]" value="P" {{$chek_present}} {{$disabled}}   style="display:{{$show_hide_main}};"  onclick="update_attendance(<?=$row_student_data->ca_id;?>,'P')">
                                                            <input type="radio" class="ca_attendance form-control"  name="not_active_case[{{$sr_no-1}}]" value="P" {{$chek_present}} disabled="disabled"  style="display: {{$show_hide_sub}};"  onclick="update_attendance(<?=$row_student_data->ca_id;?>,'P')" data-toggle="tooltip"  data-placement="top"  title="{{$tooltip}}">
                                                            Present
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            @endif
                                                            @php
                                                                $show_hide_main    ='';
                                                                $show_hide_sub     ='none';
                                                            @endphp
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="mx-auto chart-circle chart-circle-xs chart-circle-<?=$graph_type_name;?> mt-sm-0 mb-0 icon-dropshadow-<?=$graph_type_name;?>" data-value="{{$row_student_data->attendance_percentage/100}}" data-thickness="5" data-color="#<?=$graph_color;?>">
                                                            <div class="mx-auto chart-circle-value text-center">{{round($row_student_data->attendance_percentage)}}%</div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">{{$row_student_data->presents}}</td>
                                                    <td class="text-center">{{($row_student_data->delivered_classess > 1 || $row_student_data->ca_status=="E"?$row_student_data->total_classes-$row_student_data->presents:0)}}</td>
                                                </tr>
                                                    @php
                                                        $sr_no++;
                                                    @endphp
                                                    @endforeach

												</tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="5">
                                                            <div class="message_div" style="padding:0px 15px">  </div>
                                                            @if($var_class_status === "ACTIVE" && $count_marks_attendance==0)
                                                                <button type="button" class="btn btn-primary btn-block attendance_btn" onclick="submit_with_check({{$row_student_data->slot_id}})">Submit Attendance</button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tfoot>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>                        
						<!-- End Row -->
                        <?php }else{ ?>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="card">
                                        <div class="card-body">
                                            No Student Enrolled
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php 
                        $getData=count($withdraw_expected_students); 
                        if( $getData>0){
                        ?>

                        <div class="modal" id="AddNewContentModal1" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <p class="h5 modal-title" id="ModalTitle">Students WithDraw Details</p>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body" style="text-align: left">
                                        <?php
                                        $sr=1;
                                            foreach($withdraw_expected as $rec ){
                                                if($rec != ''){
                                                echo $sr++.'.  '.$rec.'<br>';
                                                }
                                            }
                                        ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" onclick="Acknowledge()" >Yes I Read it</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
<script>

    $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
        // ______________ Chart-circle
	if ($('.chart-circle-primary').length) {
		$('.chart-circle-primary').each(function() {
			let $this = $(this);

			$this.circleProgress({
			  fill: {
				color: $this.attr('data-color')
			  },
			  size: $this.height(),
			  startAngle: -Math.PI / 4 * 2,
			  emptyFill: 'rgba(68, 84, 195, 0.4)',
			  lineCap: 'round'
			});
		});
	}
        // ______________ Chart-circle
        if ($('.chart-circle-secondary').length) {

            $('.chart-circle-secondary').each(function() {
                let $this = $(this);

                $this.circleProgress({
                  fill: {
                    color: $this.attr('data-color')
                  },
                  size: $this.height(),
                  startAngle: -Math.PI / 4 * 2,
                  emptyFill: 'rgba(247, 45, 102, 0.4)',
                  lineCap: 'round'
                });
            });
        }

        // ______________ Chart-circle
        if ($('.chart-circle-success').length) {
            console.log('ok success');
            $('.chart-circle-success').each(function() {
                let $this = $(this);

                $this.circleProgress({
                  fill: {
                    color: $this.attr('data-color')
                  },
                  size: $this.height(),
                  startAngle: -Math.PI / 4 * 2,
                  emptyFill: 'rgba(45, 206, 137, 0.5)',
                  lineCap: 'round'
                });
            });
        }

        // ______________ Chart-circle
        if ($('.chart-circle-warning').length) {
            $('.chart-circle-warning').each(function() {
                let $this = $(this);

                $this.circleProgress({
                  fill: {
                    color: $this.attr('data-color')
                  },
                  size: $this.height(),
                  startAngle: -Math.PI / 4 * 2,
                  emptyFill: '#e5e9f2',
                  lineCap: 'round'
                });
            });
        }
    });


    </script>
