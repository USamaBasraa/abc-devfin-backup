<!-- @if(count($teacher_courses)>0) -->
<?php
// if(count($timetable) >0 ){


  ?>
  <style>
    .section-view-all{
    font-size:15px;
    font-weight:500;
    color:#2d2d2d;
    text-decoration:underline;
    transition:.2s;
}

.section-view-all:hover{
    color:#17675F;
    text-decoration:underline;
}
.container-fluid {
  padding-right: 0px !important;
  padding-left: 0px !important;
}
.timetable-table > thead > tr > th{
      background-color: #17675F !important;
    color: #ffffff !important;
}
.timetable-table thead th:not(:last-child) {
    border-right: 1px solid #d9e4e3 !important;
}
  </style>
  

{{-- @dd("123"); --}}

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="profile-overview-title mb-0" style="margin-bottom: 0px !important;">
        Time Table
    </h4>

    <a href="{{url('/timetable/faculty')}}" class="text-decoration-underline font-weight-bold" style="color: #2d2d2d;
    font-size: 14px;
    font-weight: 500 !important;
    text-decoration: underline;
    cursor: pointer;
    white-space: nowrap;">
        View Timetable
    </a>
</div>
<div class="card timetable-card" style="margin-bottom: 25px !important;border-radius: 12px;">
    {{-- <div class="card-header row" style="margin:0px;background-color:steelblue;color:white !important;border-radius: 8px;">
        <div class="col-lg-10 ">
            <i class="mdi mdi-clock fs-20 text-white icon-dropshadow-success mr-3"><strong>&nbsp Timetable</strong></i>
        </div>
        <!-- <label>Today Classes</label> -->
        <div class="col-lg-2">
            <a href="{{url('/timetable/faculty')}}" class="btn btn btn-info btn-mini ">View Timetable</a>
        </div>
    </div> --}}

    <div class="card-body" style="padding:18px !important;">
        <div class="col-lg-12" style="padding-left:0px !important;padding-right:0px !important;">
            <div class="card-block table-border-style">
                <div class="table-responsive">
                   <table class="table table-bordered table-striped timetable-table" style="margin-bottom:0px !important;">
                        <thead>
                            <th align="center"><b> Day</b></th>
                            <th align="center"><b>Subject Name</th>
                            <th align="center"><b>Class Date</th>
                            <th align="center"><b>Class Room</th>
                            <th align="center"><b>Class Time</th>
                        </thead>
                        <tr>
                            <?php
                              $count = 0;
                              $background_colors = array('success','secondary','primary','info');

                              if( count($timetable) > 0 ){

                              foreach ($timetable as $rec) {
                                $rand_background = $background_colors[array_rand($background_colors)];
                                $curr_date = date("d-M-Y", strtotime(substr($rec->tt_class_date, 0, 11)));
                              ?>
                            <td class="p-3 d-flex" align="left" ;>
                                <div class=""></div><?= $rec->tt_subject_day; ?>
                            </td>
                            <td align="left" ;>
                                <?= $rec->course_name ?>
                            </td>
                            <td align="left" ;>
                                <?php if ($count == 0) { ?>
                                <font> <?= $curr_date; ?></font>

                                <?php $count = 1;
                                } else { ?>
                                                <font> <?= $curr_date; ?></font>
                                                <?php
                                } ?>
                            </td>
                            <td align="left" color="pink" ;>
                                <?= $rec->room_name ?>
                            </td>
                            <td align="left" color="pink" ;>
                                <?= $rec->tt_subject_time_from . ' - ' . $rec->tt_subject_time_to; ?>
                            </td>
                        </tr>

                        <?php } }else{ ?>
                        <!-- <tr><td colspan="4" style="text-align: center;" > <b>No Lecture Today!</b> </td></tr>   -->
                        <tr>
                            <td colspan="5" style="text-align: center;"> <b>There is no lecture scheduled for today.</b> </td>
                        </tr>
                        <?php } ?>

                    </table>
                </div>
            </div>


        </div>

    </div>
</div>



<!-- <div class="alert alert-info" style="margin-top:10px;border:none;">
        <strong>Info ! </strong>You have No Class Today
    </div> -->

</div>
</div>
</div>
<!-- @endif -->
