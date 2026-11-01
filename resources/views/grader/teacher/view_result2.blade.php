<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
// echo $co_id;
// print_r($sqlresult);
// exit;


 $studentCount = collect($test)->unique('sai_enrollment_no')->count();

?>

@extends('layouts.app',['pageTitle' => 'Online Exam'])
@section('content')
<style>
a.disabled {
            pointer-events: none;
            cursor: default;
        }
.question_counting{
    background-color: #CCC;
}
.heading_center{
  margin: auto;
  border: 0px solid green;
  padding: 10px;
  text-align: center;
}
.center {
  margin: auto;
  border: 0px solid green;
  padding: 10px;
}
div{
    border: 0px solid rgb(128, 0, 6);
}
.table thead th {
            background-color: steelblue !important;
            color: #ffffff !important;
        }
</style>
<!----------  ADVANCE MODEL upload file START---------------------->
<div class="modal fade" id="advancemodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="  width: 600px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Student Paper</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                 <div class="modal-body">
                 <form enctype="multipart/form-data" method="post" name="paper_setting"  id="paper_setting">
                 @csrf
                 <input type="hidden" id="paper_id" name="paper_id" />
                 <input type="hidden" id="roll_no" name="roll_no" />
                 <div class="row">
                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4"><strong>Exam Status </strong></div>
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                        <select id="paper_status" name="paper_status" class="form-control"  style="width:300px;" >
                        <option value="0">Exam Continue</option>
                        <option value="3">Exam Stop</option>
                        <option value="4">Exam Reload</option>
                        <option value="6">All Student Exam Continue</option>
                        <option value="7">All Student Exam Stop</option>
                        <option value="8">All Student Exam Reload</option>
                      <!--  <option value="9">All Student Exam Complete</option>
                         <option value="6">Exam Re Allowed</option> -->
                         </select>
                    </div>
                 </div>
               <div class="row" style="margin-top: 6px;">
                    <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4"><strong> Description </strong></div>
                    <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                      <input type="text" class="form-control"  style="width:300px;" id="desc" name="desc" />
                    </div>
                 </div>


                </form>
                    </div>

                <div class="modal-footer">
                <div id="saved_msg_div_file" style="display: none;color: green"><span>Data Successfully</span></div>
                <button type="button" onclick="cancell_ass()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button  onclick="savesetting()" type="button" class="btn btn-primary upload_btn">
                    <span class="save" >Save changes</span>
                </button>


            </div>

        </div>
    </div>
</div>
<!----------ADVANCE  MODEL  END  --------------------->


<?php
//print_r($test);exit();

foreach ($test as $key => $vvv) {
    $sid = str_replace("-","",$vvv->sai_enrollment_no);
   
   // $abc = $value->sai_enrollment_no;
    $$sid[] =[
                'course_exam_papers_id' =>$vvv->course_exam_papers_id,
                'sai_enrollment_no'     =>$vvv->sai_enrollment_no,
                'answer_given'          =>$vvv->answer_given,
                'answer_status'         =>$vvv->answer_status,
                'answer_given'          =>$vvv->answer_given,
                'ceqp_id'               =>$vvv->ceqp_id,
                'question_marks'        =>$vvv->question_marks,
                'obtained_marks'        =>$vvv->obtained_marks,
                'exam_ques_type'       =>$vvv->exam_ques_type,
                'marks_enter_status'   =>$vvv->marks_enter_status
            ];
}
?>
 

 <div class="container-fluid">

    <div class="row">
        <div class="col-md-6">
            <h3 style="color: #1E293B;
font-size: 20px;
font-style: normal;
font-weight: 600;
margin-bottom:0px;
line-height: normal;">Exam Results</h3>
            <p style="color: #64748B;
font-size: 14px;
font-style: normal;
font-weight: 400;
line-height: normal;margin-bottom:30px;">Showing {{ $studentCount }} {{ $studentCount == 1 ? 'student' : 'students' }} {{ $studentCount == 1 ? 'record' : 'records' }}.</p>
        </div>
        <div class="col-md-6 text-right">
  <button class="btn btn-primary btn-sm ml-2 mb-2" onclick="exportToExcel('exportTable')" style="padding: 10px 16px;border-radius: 8px;
background: #17675F;border-color:#17675F;   color: #FFF;
font-size: 16px;
font-style: normal;
font-weight: 600;
line-height: normal;">
            <i class="fa fa-download"></i> Export to Excel
        </button>
        </div>
    </div>
      

        <!-- Table structure -->
        <table id="exportTable" class="table table-bordered">
        <thead >
                <tr>
                    <th align="center" style="
    background-color: #17675F !important;
        border: none;
            text-align: center;
                border-top-left-radius: 8px !important;
                    padding-top: 13px;
    padding-bottom: 13px;
">Sr #</th>
                    <th style="
    background-color: #17675F !important;
        border: none;
            text-align: center;
                padding-top: 13px;
    padding-bottom: 13px;
">Reg#</th>
                    <th style="
    background-color: #17675F !important;
        border: none;
            text-align: center;
                padding-top: 13px;
    padding-bottom: 13px;
">Name</th>
                    <th align="center" style="
    background-color: #17675F !important;
        border: none;
            text-align: center;
                padding-top: 13px;
    padding-bottom: 13px;
">Father Name</th>
                    <th style="
    background-color: #17675F !important;
        border: none;
            text-align: center;
                padding-top: 13px;
    padding-bottom: 13px;
">Program</th>
                    {{-- <th>Grouup</th> --}}
                    <th style="
    background-color: #17675F !important;
        border: none;
            text-align: center;
                padding-top: 13px;
    padding-bottom: 13px;
">Total Questions</th>
                    <th style="
    background-color: #17675F !important;
        border: none;
            text-align: center;
                padding-top: 13px;
    padding-bottom: 13px;
">Correct</th>
                    <th style="
    background-color: #17675F !important;
        border: none;
            text-align: center;
                padding-top: 13px;
    padding-bottom: 13px;
">Wrong</th>
                    <th style="
    background-color: #17675F !important;
        border: none;
            text-align: center;
                padding-top: 13px;
    padding-bottom: 13px;
">Missed</th>
                    <th style="
    background-color: #17675F !important;
        border: none;
            text-align: center;
                padding-top: 13px;
    padding-bottom: 13px;
">Obtained / Total (<span id="totalmarks" style="font-size:20;"></span>)</th>
                    <th style="
    background-color: #17675F !important;
        border: none;
            text-align: center;
                padding-top: 13px;
    padding-bottom: 13px;
                border-top-right-radius: 8px !important;
">Action</th>
                </tr>
            </thead>
            <tbody class="text-center" style="
    background-color: white;
">
                <?php
                $new_Enrollment_no = "";
                $srno = 1;
                $total_marks = 0;
                foreach ($test as $key => $value) {
                    if ($value->sai_enrollment_no != $new_Enrollment_no) {
                        $new_Enrollment_no = $value->sai_enrollment_no;
                        $correct = 0;
                        $wrong = 0;
                        $missed = 0;
                        $obtained_marks = 0;
                        $not_checked = 0;
                        $total_written = 0;
                        $total_marks    = 0;
                        $exam_status_text = "Exam Status";
                        $exam_status_color = "#177ce9";
                        $exam_status_desc = $value->description;

                        if ($value->exam_status == 3) {
                            $exam_status_text = "Suspended";
                            $exam_status_color = "#F00";
                        }

                        $stu_id = str_replace("-", "", $value->sai_enrollment_no);

                        foreach($$stu_id as $key => $exam_result){

                            if($exam_result['answer_status']=='T'){
                                $correct=$correct+1;
                            }elseif($exam_result['answer_status']=='F'){
                                $wrong=$wrong+1;
                            }elseif($exam_result['answer_status']=='M'){
                                $missed=$missed+1;
                            }
            
            
                    if(($exam_result['exam_ques_type']==2 or $exam_result['exam_ques_type']==4) and $exam_result['marks_enter_status']==1){
                        //$not_checked =
                        $not_checked++;
                    }
                    if($exam_result['exam_ques_type']==2 or $exam_result['exam_ques_type']==4){
                        $total_written++;
                    }


                    $total_marks    =   $total_marks+$exam_result['question_marks'];
                    $obtained_marks =   $obtained_marks+$exam_result['obtained_marks'];
                        }
                        ?>
                     
                        <tr>
                            <td><?php echo $srno++; ?></td>
                            <td ><?php echo strtoupper($new_Enrollment_no); ?></td>
                            <td class="question_counting" style="background-color:white;" ><?php echo $value->api_name?? $value->spi_name; ?></td>
                            <td class="question_counting" style="background-color:white;" ><?php echo $value->api_fathername??$value->spi_fathername; ?></td>
                            <td class="question_counting" style="background-color:white;" ><?php echo $value->prog_name??$value->st_prog_name; ?></td>
                            {{-- <td class="question_counting" style="padding: 1px;font-weight:bold;" ><?php echo $value->aq_degree_type; ?></td> --}}
                            <td class="question_counting" style="background-color:white;">
                           <?php echo ($correct + $wrong + $missed); ?>
                                </td>
<td class="question_counting" style="background-color:white;">
    <?php echo $correct; ?>
</td>
<td class="question_counting" style="background-color:white;">
    <?php echo $wrong; ?>
</td>
<td class="{{ $missed === 0 ? 'question_counting' : 'bg-warning' }} " style="text-align:center;background-color:white;">
    {{ $missed }}
</td>

                            <td class="font-weight-bold text-success"><?php echo $obtained_marks; ?></td>
                            <td>
                                <?php if ($total_written > 0 && $not_checked != $total_written) { ?>
                                    <a href="<?php echo route('teacher/check_paper', ['id' => my_simple_crypt($new_Enrollment_no), 'exam_paper_id' => my_simple_crypt($exam_result['course_exam_papers_id'])]); ?>" class="btn btn-warning btn-sm">Mark Paper</a>
                                <?php } else { ?>
                                    <button  style="width: 100px;"   type="button" class="btn btn-success btn-sm mb-2">Marked</button>
                                <?php } ?>
                                <button type="button"  
        class="btn btn-sm"
        style="  width: 100px; background-color: <?php echo htmlspecialchars($exam_status_color); ?>; color: #ffffff; border: none;"
        onclick="paperstatus('<?php echo htmlspecialchars($new_Enrollment_no); ?>', '<?php echo htmlspecialchars($exam_result['course_exam_papers_id']); ?>', '0', '<?php echo htmlspecialchars($exam_status_desc); ?>')">
    <?php echo htmlspecialchars($exam_status_text); ?>
</button>

                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    


@stop
@section('javascript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>

<script>
$( document ).ready(function() {
    $("#totalmarks").html(<?=$total_marks;?>);
});




  function go_back(co_id){
//alert(co_id);
  window.location = '<?=url('/teacher/online_result/')?>/'+co_id;
//alert(co_id);
  window.location = '<?=url('/teacher/adm_online_result/')?>/'+adm_sch_id;

}


  function paperstatus(enroll,exam_id,status,desc){
    //alert(enroll+exam_id)
        $('#advancemodel').modal('show');
        $("#paper_id").val(exam_id);
        $("#roll_no").val(enroll);

        $("#paper_status").val(status) ;
        $("#desc").val(desc);

}
  function savesetting(){

          var bi_form = document.querySelector('#paper_setting');
            var fd = new FormData(bi_form);
            $.ajax({
                type:'POST',
                url: '{{url('teacher/update-exam-status')}}',
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){
                    $("#saved_msg_div_file_error").hide();
                    $("#saved_msg_div_file").show();
          $("#exampfle-1").load(window.location + " #exampfle-1");
          setTimeout(function(){
                  $("#advancemodel").modal('hide');

                  }, 2000);
                 location.reload();

                },
                complete: function(){
                }
            });



  }

  setInterval(function() {
             $("#exampfle-1").load(window.location + " #exampfle-1");
location.reload();

}, 1800000);


function exportToExcel(tableID) {
    // Clone the table to avoid modifying the original HTML
    var table = document.getElementById(tableID);
    var clonedTable = table.cloneNode(true); // Deep clone the table

    // Remove all buttons labeled "Exam Status" from the cloned table
    removeExamStatusButtons(clonedTable);

    // Export the modified cloned table to Excel
    var workbook = XLSX.utils.table_to_book(clonedTable, { sheet: "Sheet1" });
    var wbout = XLSX.write(workbook, { bookType: 'xlsx', type: 'binary' });
    var blob = new Blob([s2ab(wbout)], { type: "application/octet-stream" });
    var link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "ExportedData.xlsx";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function s2ab(s) {
    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
    return buf;
}

function removeExamStatusButtons(table) {
    // Find and remove all buttons with the text "Exam Status" from the cloned table
    var buttons = table.querySelectorAll('button');
    buttons.forEach(button => {
        if (button.textContent.trim() === 'Exam Status') {
            button.remove();
        }
    });
}

</script>
@stop
