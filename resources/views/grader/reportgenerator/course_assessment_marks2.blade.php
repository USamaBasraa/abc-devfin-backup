<style type="text/css">
    .student_courses{
        cursor: pointer;
    }
    td{
        text-align: center;
    }
    #course_raw_summary.dataTable tfoot th {
        border-bottom:none;

    }
    #course_raw_summary.dataTable tbody th {
        border-bottom:none;
    }
    #course_raw_summary.dataTable thead th {
        color:white;
    }

</style>

<div class="modal fade" tabindex="-1" role="dialog" id="raw_marks_Modal_list">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="model_label"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="raw_marks_detail"> </div>
                <div id="student_course_attendance" style="display: none;"> @include('grader.reportgenerator.student_course_attendance') </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<style>
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
th {
  border: 1px solid black;
}
</style>
</head>
<body>

<div class="scrollmenu" style="height:400px !important; background-color:#FFF !important;">

    <?php
        if(count($student_actual_marks)<=0){ echo "Not submitted yet";exit();};
     ?>
<table id="course_raw_summary11" class="table table-bordered"  width="100%">
    <thead>
    <tr style="background-color:#2F3B4D; color: white">
        <td>Enroll. No.</td>
        <td>Name</td>
        <?php
      //  print_r(getLoginUser());exit;
        if(getLoginUser('school_id')==1)
        {
            echo "<td>Mark I Grade</td>";
        }

        $print = "Yes";

        $a=1;
        $new_sai_enrollment_no="";
        $var_assessment = "";
		$count_width=1;
		$srno=1;
		$student_count = 1;
		$stop_student_count = 0;
		$new_student="";
		$break="no";
		//print_r($student_actual_marks
		foreach($student_actual_marks_header as $obj_header_names){
			try{
				$assessment_name_next = $student_actual_marks_header[$srno]->assessment_short_name;
			}
			catch(Exception $e) {
				$assessment_name_next = 'Different';
			}

			if($assessment_name_next!=$obj_header_names->assessment_short_name){
						echo "<th style='text-align: center; color:white' colspan='$count_width'>".
						$obj_header_names->assessment_name."</th>";
						$count_width=0;
			}

		$count_width++;
	 	$srno++;
		}
		?>
        </tr>
        </thead>
        <thead>
		<tr style="background-color:#9db5db;">
		<?php
            if($school_id == 1){
                echo '<th colspan="3" style="text-align: center"> &nbsp; </th>';
            }else{
                echo '<th colspan="2"  style="text-align: center"> &nbsp; </th>';
            }

		foreach($student_actual_marks_header as $obj_student_actual_marks_header){

						echo "<th style='text-align: center'>".
						$obj_student_actual_marks_header->cam_number
						."</th>";
		}
		?>
        </tr>
        </thead>
        <thead>
		<tr style="background-color:#9db5db;">
		<?php
        if($school_id == 1){
		    echo '<th colspan="3"  style="text-align:right; color:#F00;"> All selected checkbox will marked
			 <label style=" font-weight:bold; font-size:20px;"> I </label>
			 grade </th>';
        }else{
            echo '<th colspan="2"  style="text-align: center"> &nbsp; </th>';
        }
		foreach($student_actual_marks_header as $obj_header_total_marks){

						echo "<th style='text-align: center'>".
						$obj_header_total_marks->total_marks
						."</th>";
		}

        ?>
    </tr>
    </thead>
    <tbody>
    <tr>
        <form name="test_form" id="test_form">
            <?php
            $new_sai_enrollment_no="";
            $sr_no=1;
            foreach($student_actual_marks as $obj_student_actual_marks){
            if($obj_student_actual_marks->sai_enrollment_no!=$new_sai_enrollment_no){

                if(trim($obj_student_actual_marks->obtained_marks) == "")
                {
                    $checkFlag = 1;
                }else{
                    $checkFlag = 0;
                }
                if($sr_no>1){
                    echo "</tr><tr>";
                }
                // echo "<td>".$sr_no."</td>";
                echo "<td class='student_courses'>".$obj_student_actual_marks->sai_enrollment_no."</td>";
                echo "<td  align='left'><div style='text-align:left;'>".$obj_student_actual_marks->spi_name."</div></td>";

                if($obj_student_actual_marks->sai_enrollment_no!=$new_sai_enrollment_no){
                    if(getLoginUser('school_id')==1)
                    {
                        echo '<td><input type="checkbox" name="i_grade[]" id="i_grade[]" class="chk" value="'.$obj_student_actual_marks->sc_id.'"';  echo ($obj_student_actual_marks->missing_flag) == 0 ? "" : "checked"; echo '/></td>';
                    }
                    $new_sai_enrollment_no=$obj_student_actual_marks->sai_enrollment_no;
                    $sr_no++;
                }

            }
            ?>
            @if(trim($obj_student_actual_marks->obtained_marks) === "" && trim($obj_student_actual_marks->assessment_id) === "4")
                <td style="text-align:center; background-color: red; color: white;">Empty</td>
            @elseif(trim($obj_student_actual_marks->obtained_marks) === "" && trim($obj_student_actual_marks->assessment_id) !== "4")
                <td style="text-align:center; background-color: yellow; color: black;">Empty</td>
            @elseif(($obj_student_actual_marks->ccd_status) =='D' or ($obj_student_actual_marks->cam_status) =='D')
                <td class="student_courses">{{$obj_student_actual_marks->obtained_marks}}</td>
            @else
                <td class="student_courses">{{$obj_student_actual_marks->obtained_marks}}</td>
            @endif
            <?php

            }

            ?>
        </form>
    </tr>
    </tbody>
</table>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        var table = $('#course_raw_summary').DataTable({
            'responsive': true,
            "searching": false,
            "paging": false,
            "bInfo": false,
            "orderable":false,
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
    function student_coursesaa(get_enrollment_no) {
        $('#raw_marks_Modal_list').modal('show');
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
    function student_courses(get_enrollment_no,parent_co_id,sc_co_id,session_name) {
        $('#raw_marks_Modal_list').modal('show');
        $.ajax({
            type:'GET',
            url:"{{ URL::route('load_student_marks') }}/"+get_enrollment_no+"/"+parent_co_id+"/"+sc_co_id,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                $("#raw_marks_detail").html(data);
                $("#model_label").html('Academic Summary '+session_name);
                $("#student_course_attendance").hide();
            }
        });
    }

    function checkStudent()
    {

    }
</script>
