<?php //print_r($courseMarksSummaries);exit();  ?>

<style type="text/css">
    .student_courses{
        cursor: pointer;
    }
    td{
        text-align: center;
    }
    #courseMarksSummary.dataTable thead th {
        border-bottom: 0;
    }
    #courseMarksSummary.dataTable tfoot th {
        border-top: 0;
    }
    #courseMarksSummary border-bottom{
       border: none;
    }
div.scrollmenu {
  overflow: auto;
  white-space: nowrap;
}

div.scrollmenu a {
  display: inline-block;
  color: white;
  text-align: center;
  padding: 14px;
  text-decoration: none;
}
</style>

<div class="datatable_div2">
<div class="scrollmenu" style="height:408px !important; background-color:#FFF !important; overflow:scroll">
<table id="courseMarksSummary" class="table table-bordered" cellspacing="0" width="100%">
    <thead STYLE=" color: white; background-color:#2F3B4D;">
    <tr STYLE=" color: white; background-color:#2F3B4D;">
        <td width="20%">Enroll No. </td>
        <td>Name</td>
        <?php
        $print = "Yes";
        $a=1;
        $new_sai_enrollment_no="";
        $var_assessment = "";
        $count_assessments=0;
        foreach($courseMarksSummaries as $obj_student_marks_heads){

            if($obj_student_marks_heads->sc_sai_enrollment_no!=$new_sai_enrollment_no){
                $new_sai_enrollment_no=$obj_student_marks_heads->sc_sai_enrollment_no;
                $a=$a+1;
            }
            if($a==2){
                echo "<td style='text-align: center' width='15%'>".
                    $obj_student_marks_heads->assessment_short_name.
                   // " ".
                    //$obj_student_marks_heads->cam_number.
                    "<br>".
                    round($obj_student_marks_heads->total_marks)
                    ."</td>";
                $count_assessments++;
            }
            $var_assessment=$obj_student_marks_heads->assessment_name;

        }
        ?>
        <td width="25%" style='text-align: left'>Score <br>100</td>

    </tr>
    </thead>
    <tbody>
    <tr>
        <?php
        $new_sai_enrollment_no="";
        $sr_no=1;
        $obtained_total_marks = 0;
        $print_assessments_count = 0;
        foreach($courseMarksSummaries as $obj_student_actual_marks){
        if($obj_student_actual_marks->sc_sai_enrollment_no!=$new_sai_enrollment_no){
            if($sr_no>1){
                $obtained_total_marks = 0;
                $print_assessments_count = 0;
                echo "</tr><tr>";
            }
           // echo "<td>".$sr_no."</td>";
            echo "<td class='student_courses'>".$obj_student_actual_marks->sc_sai_enrollment_no."</td>";
           	echo "<td class='student_courses' align='left'><div style='text-align:left;'>".$obj_student_actual_marks->spi_name."</div></td>";
            $new_sai_enrollment_no=$obj_student_actual_marks->sc_sai_enrollment_no;
            $sr_no++;
        }
        ?>
       <?php
               if(trim($obj_student_actual_marks->obtained_marks) == "" && trim($obj_student_actual_marks->assessment_id) == "4"){
                    echo '<td style="text-align:center; background-color: red; color: white;">Empty</td>';
                }elseif(trim($obj_student_actual_marks->obtained_marks) == "" && trim($obj_student_actual_marks->assessment_id) != "4"){
                    echo '<td style="text-align:center;background-color: yellow; color: black;">Empty</td>';
                }else{
                    echo "<td style='text-align: center'>".$obj_student_actual_marks->obtained_marks."</td>";
                }
            $obtained_total_marks=$obtained_total_marks+$obj_student_actual_marks->obtained_marks;
            $print_assessments_count++;
              if($print_assessments_count==$count_assessments){
                  echo "<td style='text-align: center'>".number_format($obtained_total_marks,2)."</td>";
              }
        }

        ?>
    </tr>
    </tbody>
</table>
</div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#courseMarksSummaryno').DataTable({
            'responsive': true,
            "searching": false,
            "paging": false,
            "bInfo": false,
            "orderable":true,
            "fixedHeader": true,
            "scrollY": 250,
            "scrollX": true,
            "columnDefs": [{
                "targets": [ 0 ],
                "className": "text-left"
            },{
                "targets": [ 1 ],
                "className": "text-left"
            }]
        });
        // table.ajax.reload();
    });
    function student_courses(get_enrollment_no) {
        $.ajax({
            type:'GET',
            url:"{{ URL::route('student_courses') }}/"+get_enrollment_no,
            //  headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},  /'"/"+sai_id;
            success:function(data){
                // alert(data);
                $("#assessment_marks").html(data);
            }
        });
    };
</script>
