@extends('layouts.app',['pageTitle' => 'Graduating Student Summary'])
@section('content')
<style type="text/css">
.canvasjs-chart-canvas{
    color:white !important;
}
</style>
<div class="card" style="display:none">     
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
        <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >Student Repeating Report</h3>
    </div>
    <div class="card-header" style="width:100%;">
        <div class="row" style=" width:98% ;height: 110px;!important; margin-left:auto; margin-right:auto; padding:10px;"> 
            <div class="col-12 col-md-12 text-md-left text-center" style="min-height:150px;">
                <form method="get"  id="filterFormOne" enctype="multipart/form-data">
                <!-- {{ csrf_field() }}        -->
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label for="session_sel">Enrollment No: </label>
                            <input type="text" name="sai_enrollment" id="sai_enrollment" class="form-control" >
                        </div>
                        <div class="form-group col-sm-3">
                            <button type="button" class="btn btn-info btn-lg" style="margin-top: 25px;"  onclick="getresult()" >GET Result</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>    

    <div class="card" style="margin-top:10px;">    
        <button class="btn primary" onclick="downloadExcel2();" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> GET Excel</button>
        <div class="card-body">
            <table class="table table-sm  table-bordered">
                <thead>
                    <tr>
                        <th style="text-align:center;font-weight:bold;" scope="col">Sr.No</th>
                        <th style="text-align:center;font-weight:bold;" scope="col">Roll #</th>
                        <th style="text-align:center;font-weight:bold;" scope="col">Student Name</th>
                        <th style="text-align:center;font-weight:bold;" scope="col">School</th>
                        <th style="text-align:center;font-weight:bold;" scope="col">Program</th>    
                        <th style="text-align:center;font-weight:bold;" scope="col">Submit Yes / No</th>             
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sr = 1;
                    foreach($res as $row){?>
                        <tr>
                            <td style="text-align:center;"><?=$sr++?></td>
                            <td style="text-align:left;"><?=$row->sai_enrollment_no?></td>
                            <td style="text-align:left;"><?=$row->spi_name?></td>
                            <td style="text-align:left;"><?=$row->school_name?></td>
                            <td style="text-align:left;"><?=$row->prog_name?></td>
                            <?php if($row->submit == 1){?>
                                <td style="text-align:center;">Yes</td>
                            <?php }else{?>
                                <td style="text-align:center;color:red">No</td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>    
@stop
@section('javascript')
<script>
    function downloadExcel2(){
       
        url = 'http://devstaff.bnu.edu.pk/HOD/graduate_survey_form.php';
        window.open(url, '_blank');
    }
    $(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
        getresult()
      event.preventDefault();
      return false;
    }
  });
});
var lastURL = '';

    function getRequest(endPoint,successBlock,failBlock){
        $.ajax({
            url: "{{url('/')}}/"+endPoint,
            type: "GET",
            dataType: "json",
            headers: {
                    Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                    Accept: 'application/json'
                },
            success:successBlock
        }).fail(failBlock);
    }
    function getresult(){
        var sai_enrollment = $('#sai_enrollment').val();
        var url = 'student/get-repeat-course?enrollment='+sai_enrollment;
        lastURL = url;
        getRequest('student/get-repeat-course?enrollment='+sai_enrollment,function(res){
            if(res.status){
                html='';
                std='';
            
                std += '<div><span style="font-weight:bold;padding-right: 90px;">Reg No:   </span> '+res.std[0].sai_enrollment_no+'</div>';
                std += '<div><span style="font-weight:bold;padding-right: 70px;">Student ID:    </span>'+res.std[0].sai_id+'</div>';
                std += '<div><span style="font-weight:bold;padding-right: 45px;">Student Name:   </span> '+res.std[0].spi_name+'</div>';
                std += '<div><span style="font-weight:bold;padding-right: 50px;">School Name:    </span>'+res.std[0].school_code+'</div>';
                std += '<div><span style="font-weight:bold;padding-right: 80px;">Program:    </span>'+res.std[0].sai_programid+'</div>';
                console.log(res.std);
                $.each(res.data,function( key, value ) {
                    var srno = parseInt(key+1);
                    
                    if(value.rec > 1 ){
                        
                    html += '<tr>';
                    html += '<td>'+srno+'</td>';
                    html += '<td style="text-align:center;" style="color:#0B7A75" >'+value.gh_course_code+'</td>';
                    html += '<td  style="color:#A30000;font-size:bold" >*'+value.course_cd_name+'</td>';
                    html += '<td align="center" style="color:#582C4D">'+value.gh_co_course_cr+'</td>';
                    html += '<td align="center" style="color:#6B5E62" >'+value.gh_grade+'</td>';
                    html += '<td align="center" style="color:#F95700FF" >'+value.sess_code+'</td>';
                    // html += '<td><a href="#" target="_blank">Comments</a></td>';
                    //html += '<td>'+value.want_course_again+'</td>';
                    // html += '<td>N/A</td>';
                    html += '</tr>';
                    
                }else {
                    html += '<tr>';
                    html += '<td>'+srno+'</td>';
                    html += '<td style="text-align:center;" style="color:#0B7A75" >'+value.gh_course_code+'</td>';
                    html += '<td  style="color:#" >'+value.course_cd_name+'</td>';
                    html += '<td align="center" style="color:#582C4D">'+value.gh_co_course_cr+'</td>';
                    html += '<td align="center" style="color:#6B5E62" >'+value.gh_grade+'</td>';
                    html += '<td align="center" style="color:#19535F" >'+value.sess_code+'</td>';
                    // html += '<td><a href="#" target="_blank">Comments</a></td>';
                    //html += '<td>'+value.want_course_again+'</td>';
                    // html += '<td>N/A</td>';
                    html += '</tr>';
                    //var co_id = value.gh_co_course_id;
                }
              
                });
                $('#result-body').html(html);
                $('#std-detail').html(std);
            }
        },function(res){
            alert('Some Error found!');
        });
    }
    function getPDF(){
     
    // alert(lastURL+'&result-for=print');
     window.open(lastURL+'&result-for=print');

 }


</script>
@stop