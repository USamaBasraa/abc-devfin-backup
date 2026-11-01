<style type="text/css">
.btn{
    font-size:10px !important;
}
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
<div class="scrollmenu" style="height:347px !important; background-color:#FFF !important; overflow:scroll">
<table id="courseMarksSummary" width="100%" border="1" cellpadding="4" cellspacning="0" cellspacing="0" width="100%">
    <thead>
    <tr STYLE=" color: white; background-color:#2F3B4D;">
        <th width="20%">Enroll No. </th>
        <th>Name</th>
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
                echo "<th style='text-align: center' width='15%'>".
                    $obj_student_marks_heads->assessment_short_name.
                   // " ".
                    //$obj_student_marks_heads->cam_number.
                    "<br>".
                    round($obj_student_marks_heads->total_marks)
                    ."</th>";
                $count_assessments++;
            }
            $var_assessment=$obj_student_marks_heads->assessment_name;

        }
        ?>
        <th width="25%" style='text-align: left'>Score <br>100</th>

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
                    echo "<td style='text-align:center'>".number_format($obj_student_actual_marks->obtained_marks,2)."</td>";
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

<style type="text/css">
div{
    border: solid 0px green;
}
</style>
    <div class="modal-footer">
        <input type="hidden" name="post_co_id" id="post_co_id" value="{{$courseMarksSummaries[0]->co_id}}">
        <div class="row" style="padding: 0px !important; width:100% !important;" >
            <div class="col-md-2 col-sm-2 col-2" >
                <?php
                if($grade_history_entry[0]['gh_count']==0)
                {?>
                <button id="sum_delete_button" class="btn btn-primary" style="margin: 5px 5px 8px 0px;width: 112px;" onclick="deleteMarkSummaries({{$courseMarksSummaries[0]->co_id}})">Rollback Process</button>
                <?php }
                ?>
            </div>
            <div class="col-md-5 col-sm-5 col-5" >
                <label style="float: right; font-weight: bold;"> Are you sure you want to submit grades :</label>
            </div>
            <div class="col-md-5 col-sm-5 col-5" >
                <input type="button" name="student_name" class="btn btn-primary lg" style="float: right; margin: 5px;" value="Submit Grades" onclick="openModalGradeSummary({{$courseMarksSummaries[0]->co_id}})">
                <button id="" class="btn btn-primary" style="margin: 5px 5px 8px 0px; float:right;width: 100px;" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>



</div>
@section('javascript')
<script>
    window.onscroll = function() {myFunction()};

var header = document.getElementById("myHeader");
var sticky = header.offsetTop;

function myFunction() {
  if (window.pageYOffset > sticky) {
    header.classList.add("sticky");
  } else {
    header.classList.remove("sticky");
  }
}
    $(document).ready(function() {
        var table = $('#gradingMarksSummaryNO').DataTable({
            'responsive': true,
            "searching": false,
            "paging": false,
            "bInfo": false,
            "orderable":true,
            "fixedHeader": true,
            "scrollY": 260,
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
@stop
