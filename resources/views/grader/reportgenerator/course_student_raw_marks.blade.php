<style type="text/css">
    .table_style{
        width: 100%;
        padding: 4px;
        cellspacing:4px;
        text-align: center;

    }
    .profile_td_title{
        text-align: right;
        font-weight: bold;

    }
    .profile_td{
        text-align: left;

    }
    .title_td{
        text-align: left;
        color: white;
        background-color:#2F3B4D;
        font-size: 14px;
        padding: 10px;


    }
    .sub_title_td{

        background-color: #6f42c1;

    }




</style>
<?php error_reporting(0);  ?>
<table style="width: 100%;" class='table table-striped' >
    <td align="center">
        @include('student.student_profile')
    </td>
    <tr>
        <td class="title_td">
            Attendance
            <div style="float: right;">
                <?=$fetch_offered_course_details[0]->course_cd_name;?>
                ( <?=$fetch_offered_course_details[0]->course_code;?> )
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <?php
                    $prsent_exempted = $load_student_attendance[0]['total_student_present']+$load_student_attendance[0]['total_exmp'];
                    $percentage = round(($prsent_exempted*100/$load_student_attendance[0]['total_classes_held']),2);
             ?>
            <table width="100%" class='table table-striped table-bordered'>

               <?php
                        if($load_student_attendance[0]['co_total_classes']>0){
                 ?>
                <tr>
                    <td>Total Classes</td>
                    <td>Delivered</td>
                    <td>Attended</td>
                    <td>Absents</td>
                    <td>Exempted</td>
                    <td>Status</td>
                </tr>
                <tr>
                    <td><?=$load_student_attendance[0]['co_total_classes']?></td>
                    <td><?=$load_student_attendance[0]['total_classes_held']?></td>
                    <td><?=$load_student_attendance[0]['total_student_present']?></td>
                    <td><?=$load_student_attendance[0]['total_absent']?></td>
                    <td><?=$load_student_attendance[0]['total_exmp']?></td>
                    <td><?=$percentage>0 ? $percentage : '0';?></td>
                </tr>
                <?php
                    }else{
                  echo "<tr><td colspan='6' align='center'>Not submitted yet</td>";
                }
                ?>
            </table>
        </td>
    </tr>
    <tr>
        <td class="title_td">
            Academic Status
        </td>
    </tr>
    <?php
    if(count($student_actual_marks)==0){
        echo "<td>Not submitted yet</td>";
    }else{
    ?>
    <tr>
        <td >
            {{--table-bordered--}}
            <table id="assessment_marks_table" border="1"  style="max-width:100%;"  class='table table-striped '>
                 <?php
                    $a=1;
                    $count=1;
                    $new_sai_enrollment_no="";
                    $assessment_name_show = "";
               echo" <tr style='background-color:#2F3B4D; color:white;'>";
                echo "<th>"."Assessment Type "."</th>";
                echo "<th>"." Sr # "."</th>";
                echo "<th>" ."Obtained Marks"."</th>";
                echo "<th >". "Total Marks "."</th>";
                "</tr>";
                    foreach($student_actual_marks  as $obj_student_marks_heads){
                        $assessment_name = $obj_student_marks_heads->assessment_short_name;
                        //if($assessment_name!=$assessment_name_show){
                        if(1==1){
                            echo "<tr  >";
               // if($student_actual_marks[$a]->assessment_short_name!=$obj_student_marks_heads->assessment_short_name)
                if($assessment_name_show!=$obj_student_marks_heads->assessment_short_name){
                    
                    echo "<td rowspan='".$obj_student_marks_heads->total_no."' align='center' valign='middle' ><div><strong>".$obj_student_marks_heads->assessment_short_name." (".$obj_student_marks_heads->cc_weightage."% )"." </strong></div>
                   <div><strong>"." Total: </strong>".$obj_student_marks_heads->total_no."</div>
                    <div><strong>"." Method: </strong>".$obj_student_marks_heads->method_name."</div>
                     <div><strong>"." No of Selected: </strong>".$obj_student_marks_heads->best_of_no."</div></td>";
                    $assessment_name_show = $obj_student_marks_heads->assessment_short_name;
                    $count=1;

                }

 //echo "<td><div>".."</div></td>";
                            $marks_status=$obj_student_marks_heads->marks_status;
                          // print_r($obj_student_marks_heads);
                            if($marks_status!='EE'){

                            echo "<td bgcolor = '989898'  align='center'><div>".$count."</div></td>";
                            echo "<td  bgcolor = '989898' align='center'><div>".$obj_student_marks_heads->obtained_marks."</div></td>";
                            echo "<td  bgcolor = '989898' align='center'><div>".$obj_student_marks_heads->total_marks."</div></td>";
                            echo "</tr>";
                               // $count++;
                                $count++;
                           // $assessment_name_show=$assessment_name;
                             } else{
                                echo "<td   align='center'><div>".$count."</div></td>";
                                echo "<td  align='center'><div>".$obj_student_marks_heads->obtained_marks."</div></td>";
                                echo "<td   align='center'><div>".$obj_student_marks_heads->total_marks."</div></td>";
                                echo "</tr>";
                                $assessment_name_show=$assessment_name;

                                $count++;
                            }





}
                   }

                    ?>


            </table>
        </td>
    </tr>
    <?php
            } // end of bracket for if statement(raw marks) to check if record exist
     ?>
     <tr>
        <td class="title_td">
            Weighted Evaluation
        </td>
    </tr>
    <?php
   // print_r($student_percentage_marks);
    if(count($student_percentage_marks)==0){
        echo "<td>Not submitted yet</td>";
    }else{
    ?>
    <tr>
        <td>
            <?php //print_r($student_percentage_marks);exit();?>
            <table id="assessment_marks_table" border="1"  class='table table-striped table-bordered'>
                <tr>
                    <?php
                    $a=1;
                    $new_sai_enrollment_no="";
                    foreach($student_percentage_marks  as $obj_student_percentage_heads){
                        echo "<td align='center'><div>".
                            $obj_student_percentage_heads->assessment_name.
                            "</div>".
                            "</td>";
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    $a=1;
                    $grand_of_total = 0;
                    $grand_of_obtained = 0;
                    $new_sai_enrollment_no="";
                    foreach($student_percentage_marks  as $obj_student_percentage_heads){
                        echo "<td align='center'>".
                            $obj_student_percentage_heads->obtained_marks.
                            " / ".
                            $obj_student_percentage_heads->total_marks.
                            "</td>";
                        $grand_of_obtained   = $grand_of_obtained+$obj_student_percentage_heads->obtained_marks;
                        $grand_of_total      = $grand_of_total+$obj_student_percentage_heads->total_marks;
                    }
                    ?>
                </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table class='table table-striped table-bordered'>
                <td> <h3>Total Marks :  <?=$grand_of_obtained;?> / <?=$grand_of_total;?> </h3></td>
                <td> <h3> Grade :  <?=$student_percentage_marks[0]->gh_grade?> </h3></td>
            </table>
        </td>

    </tr>
    <?php
        } // end of bracket for if statement(precentage marks) to check if record exist
     ?>
</table>