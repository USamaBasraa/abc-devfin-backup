<style type="text/css">
    .student_courses{
        cursor: pointer;
    }
    .grade_display{
        background-color: palegreen;
        text-align: center;
    }
</style>


<input type="hidden" id="show_hide_column" value="0">
<div id="post_grades" style="min-height: 200px;">
    <table width="100%" border="0">
        <tr>

            <?php

            if($grading_formula_type=="Absolute"){
            ?>
            <td width="70%" align="left"  style="margin: 10px 1px;">
                Based on Absolute Grading
            </td>
            <?php
            }else  if($grading_formula_type=="Relative"){
            ?>
            <td width="70%" align="left" style="margin: 10px 1px;">
                Based on Relative Grading
            </td>
            <?php
            }
            ?>

        </tr>
    </table>
    <form id="grading_from" method="post" action="#" >
        <input type="hidden" name="grading_formula_type" value="{{$grading_formula_type}}" >
        <table id="post_grades_view"  width="100%" border="1" cellspacing="0" cellpadding="4">
            <thead>
            <tr STYLE=" color: white; background-color:#2F3B4D;">
                <td width="10%">Sr No</td>
                <td width="15%">Enroll No. </td>
                <td width="25%" style="text-align: left">Name</td>
                <td width="25%"  align='left'>Cumulative </td>
                <td width="25%"  align="left">Grade</td>
            </tr>
            </thead>
            <tbody>
            @foreach($courseMarksSummaries as $courseMarksSummaryRec)
                <tr>
                    <td align="center"><b>{{ $loop->iteration }}</b></td>
                    <td>{{$courseMarksSummaryRec->sai_enrollment_no}}</td>
                    <td style="text-align: left">{{$courseMarksSummaryRec->spi_name}}</td>
                    @if(trim($courseMarksSummaryRec->mh_marks) === "")
                        <td style="text-align:left; color: red; font-weight: bold;">NA</td>
                    @else
                        <td style="text-align:left;">{{$courseMarksSummaryRec->mh_marks}}</td>
                    @endif
                    @if(trim($courseMarksSummaryRec->gh_grade) === "I")
                        <td style="text-align:left; color: red; font-weight: bold;">{{$courseMarksSummaryRec->gh_grade}}</td>
                    @else
                        <td style="text-align:left;">{{$courseMarksSummaryRec->gh_grade}}</td>
                    @endif

                </tr>
            @endforeach
            </tbody>
        </table>
        <div>
            <input type="hidden" name="var_co_id" id="var_co_id" value="<?=$var_co_id;?>" >
            <table style="float: right;">
                <tr>
                    {{--<td><label style="float: right; font-weight: bold;"> Are you sure you want to Post grades :</label></td>--}}
                    <td>
                        <input type="button" id="button_post_grades" class="btn btn-primary lg" style="float: right; margin: 5px;" value="Post Grades">
                        <button id="" class="btn btn-primary" style="margin: 5px 5px 8px 0px;width: 100px;" data-dismiss="modal">Cancel</button>
                    </td>
                </tr>
            </table>

        </div>
    </form>
</div>



<script type="text/javascript">

    $(document).ready(function() {
       // alert('123')
        $('#post_grades_view').DataTable({
            'responsive': true,
            "searching": false,
            "paging": false,
            "bInfo": false,
            "orderable":false,
            "fixedHeader": true,
            "scrollY": 300,
            "scrollX": true
        });
    });

    $('#button_grade_view2').click(function () {

        $("#post_grades").html('<div style="width:100%;text-align: center;"><img src="{{asset('images/loading-gears-animation.gif')}}" height="200px"></img><h1 style="margin-top: 30px;">Grader is Posting Grades, Please Wait!</h1></div>');
        setTimeout(function(){
            $('#button_grade_view2').hide();
            $('#button_post_grades').hide();
            $("#post_grades").html('<div style="width:100%;text-align: center;"><h1 style="margin-top: 60px;">Posting Process Completed Sucessfully</h1></div>');
            $("#example").show();
            setTimeout(function(){
                //var modal1 = $('.close', window.parent.document); modal1.click();
            },3000);
        },5000);

        /*var show_hide_column = $("#show_hide_column").val();
        if(show_hide_column==0) {
            var table = $('#post_grades_view').DataTable();
            var column = table.column('.grade_display')[0];  //id='forschore'
            table.columns([column]).visible(true);
            $("#show_hide_column").val(1);
        }else{
            var table = $('#post_grades_view').DataTable();
            var column = table.column('.grade_display')[0];  //id='forschore'
            table.columns([column]).visible(false);
            $("#show_hide_column").val(0);
        }*/
    });
    $('#button_post_grades').click(function () {

        var var_co_id = '<?=$courseMarksSummaries[0]->co_id;?>';
        $.ajax({
            type:'POST',
            url:"{{ URL::route('post_grade') }}/"+var_co_id,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: $("#grading_from").serializeArray(),
            success:function(data){
               // alert(data);
                $("#showGradePosting").html('<div style="width:100%;text-align: center;"><img src="{{asset('images/loading-gears-animation.gif')}}" height="200px"></img><h1 style="margin-top: 30px;">Grader is Posting Grades, Please Wait!</h1></div>');
                 setTimeout(function(){
                 $('#button_grade_view2').hide();
                 $('#button_post_grades').hide();
                 $("#showGradePosting").html('<div style="width:100%;text-align: center;"><h1 style="margin-top: 60px;">Posting Process Completed Sucessfully</h1></div>');
                 $("#example").show();
                 setTimeout(function(){

                    $('#postingModal').modal('hide');
                    //location.reload();
                // var modal1 = $('.close', window.parent.document); modal1.click();
                 },3000);

                 },5000);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {


            }
        });
    });
    $('#button_post_grades22').click(function () {
        var var_co_id = $("#var_co_id").val();
        $.ajax({
            type:'GET',
            url:"{{ URL::route('post_grade') }}/"+var_co_id,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                $("#example").hide();
                $("#post_grades").html('<div style="width:100%;text-align: center;"><img src="{{asset('images/loading-gears-animation.gif')}}" height="200px"></img><h1 style="margin-top: 30px;">Grader is Posting Grades, Please Wait!</h1></div>');
                setTimeout(function(){
                    $('#button_grade_view2').hide();
                    $('#button_post_grades').hide();
                    $("#post_grades").html('<div style="width:100%;text-align: center;"><h1 style="margin-top: 60px;">Posting Process Completed Sucessfully</h1></div>');
                    $("#example").show();
                    setTimeout(function(){
                        var modal1 = $('.close', window.parent.document); modal1.click();
                    },3000);
                },5000);
            }
        });
    });

</script>
