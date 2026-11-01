
<style type="text/css">
  .scroller {
    width: 300px;
    height: 100px;
    overflow-y: scroll; 
    scrollbar-color: rebeccapurple white; 
    scrollbar-width: thin;
  }
  .lecture_icons{
      text-align: center !important;
      width: 100% !important;
      border: solid 0px black;
  }
  .tar_div{
      border: solid 1px green;
  }
</style>
<?php
$count = count($teacher_courses);
$height = 115;
$tableheight = $count * $height . 'px';
 
?>
@include('grader.teacher.course_criteria_popup')

@include('grader.teacher.check_lectures')

<?php
$sr_no = 1;
$card_col=2;
$show='';
$count=0;
if(count($teacher_courses)>0) {
foreach ($teacher_courses as $rec_teacher_courses):
    $image_width = "35px";
    $image_width2 = "47px";
    $fc_co_id = $rec_teacher_courses->fc_co_id;

    $fc_co_course_id = $rec_teacher_courses->fc_co_course_id;
    $fc_co_idd = my_simple_crypt($fc_co_id, 'e');
    $co_course_id = my_simple_crypt($fc_co_course_id, 'e');
    if($rec_teacher_courses->fc_co_school_id==5 ||$rec_teacher_courses->fc_co_school_id==12 ){
        $show='';
    }else{
        $show='not-active';
    }
?>
<div class="card e-panel">
        <div class="card-header" style="padding: 7px 20px;">
            <div class="row" style="width: 100% !important">
                <div class="col-12  col-md-10 col-xl-10">
                    <strong style="font-size:16px">
                        <i class="fa fa-book"></i>
                        <?= $rec_teacher_courses->course_code . ' : ' . $rec_teacher_courses->course_cd_name; ?>
                        <?= $rec_teacher_courses->co_course_cr . ' : ' . $rec_teacher_courses->co_section; ?>
                    </strong>
                </div>
                <div class="col-12  col-md-2 col-xl-2">
                    <strong style="font-size:16px">
                        <a   data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="bottom" data-trigger="hover" data-title="CMS Guidline " class="guidline-alert-<?=($rec_teacher_courses->course_grading_type=='Relative')?'relative':'absolute';?>" data-content="Loading... "  ><i class="fa fa-cogs"></i>
                        <?= $rec_teacher_courses->course_grading_type;?><b><i  class="pe-7s-help1" data-toggle="tooltip" title="pe-7s-info"></i></b></a>
                    </strong>
                </div>
            </div>
        </div>
        <div class="card-body" style="padding:1px !important;">
            <?php
            $fc_co_id = $rec_teacher_courses->fc_co_id;
            $fc_co_course_id=$rec_teacher_courses->fc_co_course_id;
            $fc_co_idd=my_simple_crypt($fc_co_id, 'e' );
            ?>
        </div>
        <div class="card-footer">
            
            @if($rec_teacher_courses->check_final>0)
                <div class="row">
                    <div class="col-md-12 col-12 col-xl-12 lecture_icons">
                                <strong> Final Grade Submitted. </strong>
                    </div>
                </div>
            @else
                @if($rec_teacher_courses->course_criteria<=0)
                <div class="row">
                    <div class="col-md-12 col-12 col-xl-<?=$card_col;?> lecture_icons">
                        <div data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="right" data-trigger="hover" data-title="Course Criteria"  data-content="This is an evaluation criteria defined by the instructor of course. " class="card-block" style="padding-top: 0px !important;">
                        <img  src="{{asset('images/grade.png')}}" width="45px" onclick="openModalCourseCriteria('<?=$rec_teacher_courses->course_cd_name;?>',<?=$rec_teacher_courses->fc_co_id;?>,<?=$rec_teacher_courses->fc_co_course_id;?>)" style="cursor: pointer;">
                        <br> <span style="font-weight: bold;">Define </span> <br> Course Criteria
                        </div>
                    </div>
                    <div class="col-md-12 col-12 col-xl-<?=$card_col;?> lecture_icons">
                        <div data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="right" data-trigger="hover" data-title="Course Time Table"  data-content="Timetable daily, weekly, & monthly wise. " class="card-block" style="padding-top: 0px !important;">
                        <a href="{{route('Timetable',my_simple_crypt($fc_co_id, 'e' ))}}" >
                        <img src="{{asset('images/time.png')}}" width="45px"  style="cursor: pointer;">
                        </a>
                        <br> <span style="font-weight: bold;">Time Table </span> <br> Course Time Table
                        </div>
                    </div>
                    <div class="col-md-12 col-12 col-xl-6 lecture_icons">
                        <div data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="right" data-trigger="hover" data-title="Course Criteria"  data-content="This is an evaluation criteria defined by the instructor of course. " class="card-block" style="cursor:pointer;padding-top: 0px !important;">
                        <img src="{{asset('images/courses.png')}}" width="45px"  style="cursor: pointer;">

                        <br> <span style="font-weight: bold;">Course Details </span> <br> Please Define Course Criteria..
                        </div>
                    </div>
                </div>
                @else
        
                <div class="row">
            <?php 
                $first_row_count = 0;
                $next_block_id_start =  0; 
                $rowCount=0;
                $get_rolls = App\Http\Controllers\Grader\FacultyCoursesController::ta_rolls($fc_co_id,auth()->user()->employee_id);
                
                $totalRow=count($get_rolls);
                foreach ($get_rolls as $get_rolls_rec):
                $rowCount++;
                if(auth()->user()->group_id==825){
                    $show_category = ($get_rolls_rec->selected_status>0?"yes":"no");
                }else{
                    $count++;
                    $show_category = "yes";                
                }   
                if($get_rolls_rec->route_name == "route"){
                    $api_token = auth()->user()->api_token;
                    $parems = '?co_id='.$fc_co_idd.'&token='.$api_token;
                    $url = URL::route(trim($get_rolls_rec->route_href)).$parems; 
					//$url='';
                }else{
                    $parems = '';
                    $url    = '';
                }
                // echo($get_rolls_rec->route_name);exit;   
            ?>
              
            @if($show_category == "yes" && $first_row_count<=4)
                @php
                    $first_row_count++;
                    $next_block_id_start = $get_rolls_rec->id;                      
                @endphp   
             
                @if($get_rolls_rec->route_name == "route")     
                              
                    <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                        <div class="card-block" style="padding-top: 0px !important;">
                            <a  href="{{$url}}"   data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="bottom" data-trigger="hover" data-title="CMS Guidline"  data-content=" " class="guidline-alert-exam" >
                                <img src="{{$get_rolls_rec->category_icon}}" width="<?=$image_width;?>">
                                <br>{{$get_rolls_rec->category_title}}<br>
                            </a>
                        </div>
                    </div>
                @elseif($get_rolls_rec->route_name == "reports")                
                    @php
                        $report_url = $get_rolls_rec->route_href.'?action=grader&ca_course_id_name='.$fc_co_id.'&cwa_report_type=1';
                        $final_url  = URL::to($report_url);
                    @endphp
                    <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                        <div  class="card-block" style="padding-top: 0px !important;">
                            <a target="_blank" href="{{$final_url}}"  ><img src="{{$get_rolls_rec->category_icon}}" width="<?=$image_width;?>">
                                <br>Attendance Details</br>
                            </a>                       
                        </div>
                    </div>  
                @else
                    <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                        <div data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="right" data-trigger="hover" data-title="Course Criteria"  data-content="This is an evaluation criteria defined by the instructor of course. " class="card-block"  style="padding-top: 0px !important;   display: inline-block;">
                            <img src="{{asset('images/grade.png')}}" width="<?=$image_width;?>" onclick="openModalCourseCriteria('<?=$rec_teacher_courses->course_cd_name;?>',<?=$rec_teacher_courses->fc_co_id;?>,<?=$rec_teacher_courses->fc_co_course_id;?>)" style="cursor: pointer;">
                            <br> <p onclick="openModalCourseCriteria('<?=$rec_teacher_courses->course_cd_name;?>',<?=$rec_teacher_courses->fc_co_id;?>,<?=$rec_teacher_courses->fc_co_course_id;?>)" style="cursor: pointer; color:#424e79;"> {{$get_rolls_rec->category_title}}</img>
                        </div>
                    </div> 
                @endif
                @if($first_row_count==5)
                    <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                        <div class="card-block" style="padding-top: 0px !important;">
                            <a   data-toggle="collapse" href="#multiCollapseExample1<?=$fc_co_idd ;?>" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">
                                <img src="{{asset('images/pdf.png')}}" width="<?=$image_width;?>"><br>Details
                            </a>
                        </div>
                    </div>
                @endif                                
            @endif
            <?php
                endforeach;    
            ?>
        </div>
        <div class="row collapse multi-collapse" id="multiCollapseExample1<?=$fc_co_idd;?>" border="0" style="padding-top: 10px;	box-shadow: 0px 10px 10px 20px rgba(176, 184, 214, 0.09), 10px 10px 15px -5px rgb(176, 184, 214); border-radius: 20px!important; margin-top: 20px;background-color: lavender ">
                @foreach($get_rolls as $get_rolls_rec)  
                @php
                    if(auth()->user()->group_id==825){
                        $show_category = ($get_rolls_rec->selected_status>0?"yes":"no");
                    }else{
                        $count++;
                        $show_category = "yes";
                    }  
                    if($get_rolls_rec->route_name == "route"){
                    $api_token = auth()->user()->api_token;
                    $parems = '?co_id='.$fc_co_idd.'&token='.$api_token;
                    
                    $url = URL::route(trim($get_rolls_rec->route_href)).$parems; 
					//$url='';
                }else{
                    $parems = '';
                    $url    = '';
                }
                @endphp                                    
                @if($get_rolls_rec->id > $next_block_id_start && $show_category=='yes') 
                    @if($get_rolls_rec->route_name == "route")
                    <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                        <div class="card-block" style="padding-top: 0px !important;">
                            <a href="{{$url}}" data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="bottom" data-trigger="hover" data-title="CMS Guidline"  data-content=" " class="guidline-alert-exam" >
                                <img src="{{$get_rolls_rec->category_icon}}" width="<?=$image_width;?>">
                                <br>{{$get_rolls_rec->category_title}}<br>    
                            </a>
                        </div>
                    </div> 
                    @elseif($get_rolls_rec->route_name == "reports")
                        @php
                            $report_url = $get_rolls_rec->route_href.'?action=grader&ca_course_id_name='.$fc_co_id.'&cwa_report_type=1';
                            $final_url  = URL::to($report_url);
                        @endphp
                        <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                            <div  class="card-block" style="padding-top: 0px !important;">
                                <a target="_blank" href="{{$final_url}}"  ><img src="{{$get_rolls_rec->category_icon}}" width="<?=$image_width;?>">
                                    <br>Attendance Details</br>
                                </a>                       
                            </div>
                        </div>  
                    @else                                            
                    <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                        <div data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="right" data-trigger="hover" data-title="Course Criteria"  data-content="This is an evaluation criteria defined by the instructor of course. " class="card-block"  style="padding-top: 0px !important;   display: inline-block;">
                            <img src="{{asset('images/grade.png')}}" width="<?=$image_width;?>" onclick="openModalCourseCriteria('<?=$rec_teacher_courses->course_cd_name;?>',<?=$rec_teacher_courses->fc_co_id;?>,<?=$rec_teacher_courses->fc_co_course_id;?>)" style="cursor: pointer;">
                            <br> <p onclick="openModalCourseCriteria('<?=$rec_teacher_courses->course_cd_name;?>',<?=$rec_teacher_courses->fc_co_id;?>,<?=$rec_teacher_courses->fc_co_course_id;?>)" style="cursor: pointer; color:#424e79;"> {{$get_rolls_rec->category_title}}</img>
                        </div>
                    </div> 
                    @endif
                @endif   
                @endforeach
                @if(auth()->user()->group_id != 825) 
                    <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                        <div  class="card-block" style="padding-top: 0px !important;">
                            <a href="/tarightsmanagement?co_id=<?=$fc_co_id?>&token=<?=auth()->user()->api_token;?>" target='blank'> <img src="{{asset('images/ta.gif')}}" width="47px"><br>Assign TA</a>
                        </div>
                    </div>
                @endif
        </div>

                @endif
            @endif
        </div>

        
</div>
<?php
    $count=0;
    endforeach;
    }else{
?>
    <div class="alert alert-warning" style="margin-top:10px;border:none;">
        <strong>Info!</strong> Honorable Faculty.You have not yet assigned any course(s) in this session
    </div>
<?php
    }
?>

<?php
if(isset($checkApp[0]->count)){
	$checkApp = $checkApp[0]->count;
}else{
	$checkApp = '';
}
?>
<input type="hidden" id="checkAppStatus" value="<?=$checkApp?>">
		
<div class="modal fade" id="welcome-modal2">
        <div class="modal-dialog"  style="max-width:800px !important">
            <div class="modal-content">            
                <div class="modal-body"  style="height: 620px; !important "  >
                    <a target="_blank" href="https://www.youtube.com/watch?v=jYc33viQNqA"><img src="assets/mobile-img2.jpeg" width="770px" height="450px"></a>
                    <p style="font-size: 16px; font-family:Futura; text-align: justify;" >
                        Dear Faculty, <br>
                        We have just released a new CMS Mobile App and we are sure you’re going to love it. 
                        You can learn more about its features after installing it from <b>Play Store (Android) / App Store (IOS)</b>. <br>
                        If you have any questions please click on <a href="https://staff.bnu.edu.pk/mobile.php?user_name=<?=auth()->user()->username?>">Ask Questions</a>.<br>
                       
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="close_model" onclick="confirmWelcomeNote2();" class="btn btn-primary" data-dismiss="modal">Acknowledged</button>
                </div>
            </div>
        </div>
    </div>
@section('javascript')
<script type="text/javascript"> 
    $(document).ready(function() {  
        <?php
        $activeNote2 = 'popup';
        $welcomeNotes2 = (array)	json_decode(auth()->user()->welcome_notes);
        if (!(count($welcomeNotes2) > 0   && in_array($activeNote2, $welcomeNotes2))) {
        ?>
            $('#welcome-modal2').modal('show');
        <?php
        }
        ?>
        load_today_classes();
        get_guidline();
    }); 

    function confirmWelcomeNote2() {
		$('#welcome-modal2').modal('hide');
		var checkStatus = $('#checkAppStatus').val();
		if(checkStatus != 0){
			var formData = new FormData();
			formData.append("note_key", "<?=$activeNote2?>");
			sendPostRequest('', '', 'welcome/save-welcome-note-confirmation', function(res) {

			}, formData, null);
		}
	}

    function get_guidline(){
        var key='absolute,relative,schedule,exam,grading,notification,outline';
        sendGetRequest('.hello_btn','.message_div','cms-guidline/get-data?key='+key,function(res){
            if(res){
                $.each(res, function( index, value ) {

                    $('.guidline-alert-'+value.key).attr('data-content',value.description)
                    $('.guidline-alert-'+value.key).attr('data-original-title',value.title);              
                });
            }
        });
    }
    function load_today_classes(){
        const CheckReload = (() => {
                            let counter = - 1;
                            return () => {
                                counter++;
                                return counter;
                            };
                            })();
        {
            const refreshId = setInterval(() => {
                const properID = CheckReload();
                    //  console.log(properID);
                    //   alert(properID);
                    if(properID == '0'){
                        $("#prog1").hide();
                        $("#prog3").show();               
                    }else if(properID == '2'){
                    $("#prog3").hide();
                    $("#prog5").hide();
                    $("#prog6").show();
                    }
                    if (properID > 1) {
                        clearInterval(refreshId);
                        $.ajax({url: "todaylectures",
                            success: function(result){
                                //  alert(result);
                            $("#today_lectures").html(result);
                            }
                        });
                    }
            },1000);
        }
    }

    function openModalCourseCriteria(get_course_name, co_id, co_course_id) {
        $('#courseCriteriaModal').modal('show');
        $("#show_heading_criteria").html(get_course_name);
        $("#show_heading_criteria").show();
        $("#show_heading_criteria").parent().css('background-color','#6a6a6a');
        $.ajax({
        url: '{{url('teacher/load_criteria')}}/' + co_id + '/' + co_course_id,
        type: "GET",
        success: function(data) {
            //console.log(data);
            $("#load_date").html(data);
        }
        });
    }
</script>
@stop
