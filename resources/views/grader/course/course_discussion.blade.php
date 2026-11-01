<?php 
use App\sessions;
$sess = new sessions();
$sess->startSession();


?>
@extends('layouts.master')
@section('title')
    BNU CMS
@stop
@section('mainBody')
    <div class="card"  style="width: 95%; margin-left:10px!important;" >
        <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >Course Discussion </h3>
            <a href="{{url('teacher/group_chat',$student_data[0]->co_id)}}"  style="float:right; text-decoration:none; color:#FFF; font-size:18px;"> Class Discussion
                               <img src="{{asset('images/class_discussion.png')}}" width="30px">
             </a>
        </div>
        <div class="container" id="reload" style="height: 80%;overflow-y: scroll;scrollbar-color: #2F3B4D white; scrollbar-width: thin;">
               <form enctype="multipart/form-data" method="post" name="class_discussion"  id="class_discussion">
                   @csrf
                   <div class="form-group" style="margin-top: 20px!important">
                       <label for="exampleFormControlInput1">Comments</label>
<!--                       <input type="text" id="discussion" name="discussion" cols="40" rows="5" style="width:650px; height:50px;" >
-->
<textarea rows="3" cols="5"  placeholder="Type Message" id="discussion" name="discussion" class="form-control" maxlength="1500" ></textarea>
                      <div id="show_limit">
                      </div>
                                                               </div>
               </form>
            <div style="float: right">
                <button type="button" id="class_dis_btn" onclick="insertbtn()" class="btn btn-success" style="float:left; margin-left:4px;">Submit</button>
           </div>
        </div>
         <div class="container"  id="comment_div"   style="height: 80%;overflow-y: scroll;scrollbar-color: #2F3B4D white; scrollbar-width: thin;">
            <table class="table table-bordered table-striped" style="width:100%!important;border-left: none!important;border-right: none!important">
                <?php foreach ($getnotification as $row) { ?>
                <tr class="row">
                    <td class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-10"><?=$row->un_description?> </td>
                    <td class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2"><img src="{{asset('staffimages/'.$row->emp_imagefile.'')}}" width="30px"></td>
                </tr>

                <?php } ?>
            </table>
        </div>
        <div class="container" id="reload" style="height: 80%;overflow-y: scroll;scrollbar-color: #2F3B4D white; scrollbar-width: thin;">
            <table class="table table-bordered table-striped" style="width:100%!important;border-left: none!important;border-right: none!important">
                <tr class="row" >
                    <th class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Students</th>
                </tr>
                <style>.student_name{ color:black; font-weight:bold; }</style>
                <?php
                foreach($student_data as $rec){
                ?>
                <tr class="row">
                    <td class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-10"><a href="javascript:void(0);" onclick="open_message_popup('<?=$rec->sc_sai_enrollment_no?>');" class="student_name" >
                    {{$rec->spi_name}} <br>{{$rec->sai_enrollment_no}}
                     </a></td>
                    <td class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2"><img src="{{asset('studentpics/'.$rec->sai_enrollment_no.'.jpg')}}" width="30px" ></td>
                </tr>
                <?php
                }
                ?>
                <input type="hidden" id="sc_co_id" value="{{$co_id}}">
            </table>
        </div>
    </div>


      <!-- The Modal -->
      <div class="modal fade" id="studentInboxThread">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Message<span id="marks-name"></span></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>



<!-- Modal body -->
<div class="modal-body" style="width: 100%; padding: 0px; margin: 0px;">


<form enctype="multipart/form-data" method="post" name="class_discussion" id="student_msg_form">
<input type="hidden" name="co_id" id="student_co_id">
<input type="hidden" name="username" id="student_id">
@csrf

<div class="input-group">
  <div class="input-group-prepend">
    <span class="input-group-text" style="font-size:17px;" >Message</span>
  </div>
  <textarea style="font-size:17px;" name="message" class="form-control" aria-label="Message"></textarea>
</div>



<div >
  
  <button style="margin:10px 0px;" onclick="send_message_to_student();" type="button" class="btn btn-success" >Send</button>
</div>


</form>


<div id="student-message_listing">
                <table class="table table-bordered table-striped" >
                <tbody class="container" id="t-messsages">
               
                </tbody>
                </table>
</div>



</div>
</div></div></div>


@stop

@section('pageScripts')
 <script type="text/javascript">


        function get_messages_by_teacher(studentID){
            $.ajax({
             type: 'GET',
             url: '{{url('get_messages_by_teacher')}}?student-id='+studentID,
             success: function (data) {
                //  $("#comment_div").load(window.location + " #comment_div");
                
                var pic = 'http://cmstest.bnu.edu.pk/studentpics/'+studentID+'.jpg';
                var html = '';
                $(data.data).each(function(index,row){
                    html += '<tr class="row" style="margin:0px;" >';
                    html += '<td class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-10">'+row.un_description+'</td>';
                    html += '<td class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2"><img src="'+pic+'" width="30px"></td></tr>';
                });
                $('#t-messsages').html(html);
             },
             complete: function () {
             }
            });
        }



       function open_message_popup(sid){
            $('#studentInboxThread').modal('show');
            $('#student_co_id').val('{{$co_id}}');
            $('#student_id').val(sid);
            get_messages_by_teacher(sid);
       }   


       function send_message_to_student(){

        var co_id = $("#sc_co_id").val();
           var formData = $('#student_msg_form').serialize();
           console.log(formData);
           $.ajax({
             type: 'POST',
             data: formData,
             url: '{{url('send-message-to-student-by-teacher')}}',
             success: function (data) {
                //  $("#comment_div").load(window.location + " #comment_div");
                setTimeout(() => {
                    $('#studentInboxThread').modal('hide');    
                },2000);

                sendNote(co_id);
             },
             complete: function () {
             }
         });
       }      

       function sendNote(coID) {
            $.ajax({
                type: 'GET',
                processData: false,
                contentType: false,
                data: {co_id:coID},
                url: 'https://admission.bnu.edu.pk/index2.php?co_id='+coID+'&action=email&from=soft',
                complete: function () {
                }
            });
        }




     function insertbtn() {
         var co_id = $("#sc_co_id").val();
         var bi_form = document.querySelector('#class_discussion');
         var fd = new FormData(bi_form);
         $.ajax({
             type: 'post',
             processData: false,
             contentType: false,
             data: fd,
             url: '{{url('classdiscussion')}}/'+co_id,
             success: function (data) {
                 $("#comment_div").load(window.location + " #comment_div");
                 sendNote(co_id);
             },
             complete: function () {
             }
         });
     }
	 $('#discussion').keypress(function(e) {
           var tval = $('textarea').val(),
               tlength = tval.length,
               set = 1500,
               remain = parseInt(set - tlength);
           $('#show_limit').text(remain);
           if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
               $('textarea').val((tval).substring(0, tlength - 1))
           }
       });

 </script>
@stop 