<?php
// $course_name = $rsMarksDetails[0]->course_cd_name;
// $grading_type = $rsMarksDetails[0]->gh_grading_type;
// if($grading_type == ''){
//     $grading_type = "Absolute";
// }else{
//     $grading_type = $rsMarksDetails[0]->gh_grading_type;
// }

?>

<div class="table-responsive">
   
</div>

<div class="card-block table-border-style">
    <div class="table-responsive">
        <div  class="modal-header" style="background-color:#D2D6F1 !important;color:#4454C3 !important;font-weight: bold;font-family: 'Roboto', sans-serif;font-size: 1.1rem">
            {{-- {{$rsMarksDetails[0]->sai_enrollment_no}} --}}<?php echo (count($rsMarksDetails)>0)?$rsMarksDetails[0]->sai_enrollment_no:'Student Details'?>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <table class="table table-bordered">
            <thead>
            <tr class="table-primary" >           
                <th style="text-align: center;font-weight: bold;width:60px">Sr #</th>
                <th style="text-align: center;font-weight: bold">Absent Dates</th>
                <th style="text-align: center;font-weight: bold">Attendance Description</th>
            </tr>
            </thead>
          
            <tbody>
              
            <?php          
            
            $sr=1;
            if(count($attendance) > 0){
              foreach($attendance as $rowApi){?>
                <tr>
                    <td align="center">{{$sr}}</td>
                    <td align="center">{{$rowApi->ca_attendance_date}}</td>
                    <td> <?=($rowApi->ca_desc == '')?'No Description':$rowApi->ca_desc?></td>
                </tr>
              <?php
              $sr++;
              }         
            }else{
              echo "<td colspan='3' align='center'>No Absent Found</td>";
            }
          ?>
           
            </tbody>
      
        </table>

        <table class="table table-bordered">
            <thead>         
            <tr class="table-primary">
                <th style="text-align: center;font-weight: bold">Assessments</th>
                <th style="text-align: center;font-weight: bold">Total Marks</th>
                <th style="text-align: center;font-weight: bold">Obtained Marks</th>
            </tr>
            </thead>
            <?php
           // print_r($rsMarksDetails);
            if(count($rsMarksDetails) > 0){
              foreach($rsMarksDetails as $rec){         
              if($rec->cam_grading_status.$rec->cam_grading_status!='EE'){
                  $bgcolor = "989898";
              }else{
                  $bgcolor = "FFF";
              }
              ?>
              <tr>
                  <td align="left"><?=$rec->assessment_name?>#<?=$rec->cam_number?>&nbsp;&nbsp;<?=( $rec->cam_desc != '') ? '('.$rec->cam_desc.')' : '(CMS Assignment)';?></td>
                  <td align="center"><?=$rec->total_marks?></td>
                  <td align="center"><?=$rec->obtained_marks?></td>
              </tr>
              <?php
              }
            }else{?>
             <tr>
              <td colspan="3" align="center">No Assessment taken yet</td>             
             </tr>
             <?php } ?>
        </table>

        <table class="table table-bordered">
            <thead>
            <tr class="table-primary" >
                <?php         
                foreach($ww as $rec){
                    $assess_name = $rec->assessment_name;
                    echo "<th align='center'>".$assess_name."</th>";
                }
                ?>
                <th style="text-align: center;font-weight: bold">Obtained</th>
                <th style="text-align: center;font-weight: bold">Grade</th>
            </tr>
            </thead>
          
            <tbody>
            <?php
           
            $myobtained=0;
            $mytotal=0;
            if(count($ww) > 0){
              foreach($ww as $rowApi){
                  $total_marks = $rowApi->total_marks;
                  $obtained_marks = $rowApi->obtained_marks;
                  $gh_grade = $rowApi->gh_grade;
                  echo "<td align='center'>".$obtained_marks."/".$total_marks."</td>";
                  $myobtained=$myobtained+$obtained_marks;
                  $mytotal=$mytotal+$total_marks;
                  ?>
              <?php
              }
              echo "<td align='center'>".$myobtained."/".$mytotal."</td>";
              // echo "<td align='center'>".$ww[0]->gh_grade."</td>";
            }else{
              echo "<td colspan='2' align='center'>Marks not Graded Yet</td>";
            }
          ?>
            </tbody>
         
        </table>
    </div>




</div>
