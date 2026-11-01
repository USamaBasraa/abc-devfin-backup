<div class="row mx-auto justify-content-center align-items-center flex-column ">
   @include('teacher.lecture_scheduling_header', ['active_tab_name' => 'Lecture']);
   <div class="tab-content">
     <div class="tab-pane fade show active" id="home">
        <!-------------- tab content 1 --------------->
        <div class="container" style="border:solid 0px #0C0;padding-left:2%; margin-left:auto; margin-right:auto; width:100%;">
            <div class="row">
                <?php
                $date_sr_no = 0;
                foreach($all_classes as $rec_all_classes):
                        $no_content_print = 0;
                        $class_status="";
                        if($rec_all_classes->attendance_marked>0 && $rec_all_classes->checkclass=="past"){
                        $class_status = "";
                        $bg_color = '#00CC99';
                        }elseif($rec_all_classes->attendance_marked==0 && $rec_all_classes->checkclass=="past"){
                        $bg_color = '#FF0';
                        $class_status = '<label style="color:red">Missed</label>';
                        }elseif($rec_all_classes->attendance_marked==0 && $rec_all_classes->checkclass=="coming"){
                        $bg_color = '#337AB7';
                        $class_status = "";
                        }
                        //------------------
                        $today_date=date("d-M-Y", strtotime(substr($rec_all_classes->tt_class_date,0,11)));
                        date_default_timezone_set('Asia/karachi');
                        $curr_time =date("His");
                        $curr_date=date("d-M-Y", strtotime(substr($curr_time,0,11)));
                        $bod='custom_boffrder';
                        $card_col=3;
                        $bdr='2';
                        if($curr_date==$today_date){
                        $bod='custom_border';
                        $bg_color='#00CCFF';
                        $bdr='4';
                        }

                    ?>
<!-- Card Contents Start -->
<div class="col-md-12 col-xl-12" style="padding: 0px !important;margin:0px;">
    <div class="card client-blocks dark-primary-border">
        <div class="card-block">
            <div class="card-header" style="height:70px;">
                <div style="float:left;">
                    <div style="font-size:12px; font-weight:bold;">
                        <h5>Lecture <?php echo ($date_sr_no=$date_sr_no+1)." ".$class_status; ?></h5>
                        <?php
                        $today_date=date("d-M-Y", strtotime(substr($rec_all_classes->tt_class_date,0,11)));
                        date_default_timezone_set('Asia/karachi');
                        $curr_time =date("His");
                        $curr_date=date("d-M-Y", strtotime(substr($curr_time,0,11)));
                        if($curr_date==$today_date){
                        ?>
                        <span class="badge badge-danger blink align-text-top">Today Class</span>
                        {{-- In Case Today Class --}}
                        <div class="col-md-12 col-xl-<?=$card_col;?>">
                            <div class="card-block" style="padding-top: 0px !important;">
                                <a   onclick="openModalCourseAttendance('{{$all_classes[0]->course_name}}','{{$all_classes[0]->mtt_co_id}}')"  >
                                    <img   title="Attendance" src="{{asset('images/attendance.png')}}" width="<?=$image_width;?>"  style="cursor: pointer;" >
                                </a  >
                            </div>
                        </div>
                        {{-- In Case today class code end --}}
                        <?php } ?>
                    </div>
                    <div style="font-size:10px; line-height:4px;">{{$rec_all_classes->start_time}} To {{$rec_all_classes->end_time}}</div>
                </div>
                <div style="float:right; font-size:10px;">
                    <?php echo date("d-M-Y", strtotime(substr($rec_all_classes->tt_class_date,0,11)));?><br />
                    {{$rec_all_classes->class_day_name}}
                </div>
            </div>
            <div class="card-body" style="height:100px !important; "> 
               <p class="card-text overflow-scroll" id="class_content_p{{$rec_all_classes->tt_id}}" style="color:#000;">
                     <?php
                    $content_count = 0;
                    foreach($rec_all_classes->courseofferingcontent  as $row):
                    ?>
                    <a href="#" data-toggle="modal" data-target="#deletepopup" onclick="deletecontentpopup('<?=$row->coc_id?>','<?=$row->coc_tt_id?>','<?=$row->coc_details?>')" style="cursor: pointer">
                    <i class="fa fa-edit" style="color: red"></i>
                    <?=$row->coc_details?><br>
                    </a>    
                    <?php
                    $content_count++;
                    endforeach;
                    $image_width="32px";
                    if($content_count==0){
                     ?>
                     <h3 id="no_content_here" class="text-success" > No Content Here . . . </h3>
                     <?php
                     }
                     ?>
                 </p>
            </div>
            <style type="text/css">
             .lecture_icons{
                 bordered:solid 2px black;
                 text-align:center;
             }
            </style>
            <div class="card-footer">
                <div class="row">
                        <div class="col-md-12 col-xl-<?=$card_col;?> lecture_icons">
                            <div class="card-block" style="padding-top: 0px !important;">
                                <a href="#"  data-toggle="modal" data-target="#exampleModalCenter"  onclick="get_tt_id('<?php echo $rec_all_classes->tt_id;?>','<?php echo $rec_all_classes->mtt_co_id;?>')">
                                    <img src="{{asset('images/content.png')}}" width="28px" data-toggle="tooltip" data-placement="top" title="Lecture Content"><br>Lecture Content
                                </a>
                            </div>
                        </div>
                        <div class="col-md-12 col-xl-<?=$card_col;?> lecture_icons">
                            <div class="card-block" style="padding-top: 0px !important;">
                                <a  <?php  if($content_count>0){ ?> class="disabled-icon"    <?php }else{  ?>  data-toggle="modal" data-target="#exampleModalCenter4"<?php }?> >
                                    <img src="{{asset('images/upload.png')}}" width="<?=$image_width;?>" onclick="get_co_id('<?php echo $all_classes[0]->mtt_co_id;?>','<?=$date_sr_no?>','<?php echo $rec_all_classes->tt_id;?>')" data-toggle="tooltip" data-placement="top" title="Upload File"><br>Upload File
                                </a>
                            </div>
                        </div>
                        <div class="col-md-12 col-xl-<?=$card_col;?> lecture_icons">
                            <div class="card-block" style="padding-top: 0px !important;">
                                <a  <?php if($content_count>0){ ?> class="disabled-icon" <?php }else{  ?>  href='/teacher/videouploaddrive/<?php echo $all_classes[0]->mtt_co_id;?>/<?=$date_sr_no?>/<?php echo $rec_all_classes->tt_id;?>' data-target="#exampleModalCenter22" <?php }?> onclick="get_co_id('<?php echo $all_classes[0]->mtt_co_id;?>','','<?php echo $rec_all_classes->tt_id;?>')">
                                    <img src="{{asset('images/video_upload.png')}}" width="<?=$image_width;?>" data-toggle="tooltip" data-placement="top" title="Upload Video"><br>Upload Video
                                </a>
                            </div>
                        </div>
                        <div class="col-md-12 col-xl-<?=$card_col;?> lecture_icons">
                            <div class="card-block" style="padding-top: 0px !important;">
                                <a <?php if($content_count>0){ ?> class="disabled-icon" <?php }else{  ?> data-toggle="modal" data-target="#exampleModalCenter3"  <?php }?> />
                                    <img src="{{asset('images/assignment.png')}}" width="<?=$image_width;?>" data-toggle="tooltip" data-placement="top" title="Upload Assignment"><br>Upload Assignment
                                </a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Card Contents End -->
                    <?php  endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
