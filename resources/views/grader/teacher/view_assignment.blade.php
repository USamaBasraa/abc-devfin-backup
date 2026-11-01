    <?php

echo "I am Bot and finding this file";


if($chkassess){?>
<div class="card"  style="width:100%;margin-top: 20px" >
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
        <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >{{$criteria[0]->course_code.' '.$criteria[0]->course_cd_name}} </h3>
    </div>
    <div class="container" id="reload" style="height: 80%;overflow-y: scroll;scrollbar-color: #2F3B4D white; scrollbar-width: thin;">
        <table class="table table-bordered table-striped" style="width:100%!important;border-left: none!important;border-right: none!important">
            <tr class="row" >
                <th style="display:none;" class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">ID</th>
                <th style="text-align: center" class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">Assessments</th>
                <th style="text-align: center" class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">Title</th>
                <th style="text-align: center" class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">Marks</th>
                <th style="text-align: center;display: none" class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">Due Date</th>
                <th style="text-align: center" class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">Update</th>
                <th style="text-align: center" class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">Check</th>
            </tr>
            <?php
             //   print_r($all_uploads);
            foreach($all_uploads as $rec){
              $timestamp = strtotime( $rec->due_date);
              $newDate = date('d-M-y', $timestamp);
            ?>
            <tr class="row" id="{{$rec->id}}">
            <td style="display: none" class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">{{$rec->id}}</td>
            <td class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">{{$rec->assessment_name.' # '.$rec->ccd_number}}</td>
            <td class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">{{$rec->ccd_details}}</td>
            <td style="text-align: center" class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">{{$rec->abc}}</td>
            <td style="text-align: center;display: none" class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">{{$newDate}}</td>
            <td style="text-align: center;"  class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                <a href="#" data-toggle="modal" data-target="#updatemodal"  onclick="btn('<?=$rec->id;?>')"><i class="fa fa-pencil-square-o fa-2x" style="color: blue"></i></a>
            </td>

                <td style="text-align: center" class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                    <a href="{{url('teacher/check_assignment',['title'=>$rec->id,'id'=> $rec->co_id])}}"  ><i class="fa fa-file-text-o fa-2x" style="color: green"></i></a>
                </td>
            </tr>
            <tr class="row">
                <?php if($rec->description == ''){?>
                    <td class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6"><b>Description: </b>Not Entered</td>
                    <?php }else {?>
                <td class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6"><b>Description: </b>{{$rec->description}}</td>
                    <?php } ?>
                <td class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2"><b>Late Submission: </b>{{$rec->late_submission_allowed}}</td>
                <td class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2"><b>Re Submission: </b>{{$rec->re_submission_allowed}}</td>
                    <?php if($rec->file_original_name == ''){?>
                    <td class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2"><b>No Uploads</b></td>
                    <?php }else {?>
                    <td class="col-2 col-sm-2 col-md-2 col-lg-2 col-xl-2"><b><a href="{{asset('docs/'.$rec->file_name)}}" style="color: blue;cursor: pointer;">Download {{$rec->file_original_name}}</a></b></td>
                    <?php } ?>

            </tr>
            <?php
            }
            ?>
        </table>
    </div>
</div>
<div class="card" style="width: 50%; margin-left: 210px!important;margin-top: 20px!important;">
    <div class="container">
        <table class="table table-bordered table-striped" style="border-left: none!important;border-right: none!important">
            <tr class="row">
                <th style="text-align: center" class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Assessments</th>
                <th style="text-align: center" class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">Total</th>
            </tr>
            <tr class="row">
                <?php
                foreach($count as $row){
                ?>
                <td class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">{{$row->assessment_name}}</td>
                <td style="text-align: center" class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">{{$row->count}}</td>
                <?php
                }
                ?>
            </tr>
        </table>
    </div>
</div>
    



<!-- MODAL START -->
<div class="modal fade" id="updatemodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit Assessment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
            <form enctype="multipart/form-data" method="post" name="update_form"  id="update_form">
            <input type="hidden" id="edit_assessment_id" value="" name="assessment_id" >
                @csrf
                        <div class="form-group">
                            <div class="row">
                                <div style="display: none" class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                    <label for="assessment_id" style="font-weight: bold">Assessments</label>
                                    <select class="form-control"  id="assessment_idd" name="assessment_id_edit">
                                        <option value="">--Select Assessment--</option>
                                        @foreach($criteria as $rec_course_criteria)
                                            <option value="{{$rec_course_criteria->assessment_id}}">{{ $rec_course_criteria->assessment_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                    <label for="marked" style="font-weight: bold">Marks <b class="text-danger">*</b> </label>
                                    <input type="number" class="form-control assess-edit-input" id="marked" name="marks" min="0" max="500" maxlength="5"  placeholder="Marks">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="is_file_edit" name="is_file" value="">
                        <div class="form-group">
                            <input type="hidden" id="fcu_id">
                            <label for="exampleFormControlInput1" style="font-weight: bold">Title <b class="text-danger">*</b> </label>
                            <input type="text" class="form-control assess-edit-input"  id="title_models" name="title2"  placeholder="Title Here . . ." maxlength="200">
                            
                        </div>
                        <div style="margin-top: 20px"></div>
                        <!--
                        <div data-toggle="collapse" data-target="#detailed">For Details &nbsp;<i class="fa fa-arrow-circle-o-down fa-lg"></i></div>
                        -->
                        <div class="form-group">
                            <label for="is_assignment_edit">
                                <input id="is_assignment_edit" name="is_assignment" data-toggle="collapse"  data-target="#detailed" type="checkbox"   value="yes" > &nbsp;Is Assignment</label>
                        </div>

                        <div style="margin-top: 20px"></div>
                        <div id="detailed" class="collapse">
                             <div class="form-group">
                                <label for="exampleFormControlInput1" style="font-weight: bold"></label>

                                <a id="href"  class="file-remove-btn" style="color:black;cursor: pointer;" >
                                        Uploaded File(Download) 
                                        <!-- <label  id="lastuploadfile" style="cursor:pointer"></label> -->
                                </a>
                                <a href="javascript:void(0);"  class="file-remove-btn" onclick="remove_edit_file();" id="remove_file_btn"  >Remove File</a>


                                <input type="file" name="uploadfile2" id="uploadfile2_edit"  accept=".xlsx,.docx,.pptx,.jpg,.jpeg,.pdf,.png,.zip" class="form-control assess-edit-input optional-inputs" >
                                <small class="form-text text-danger">Only xlsx, docx, pptx, jpg, jpeg, pdf, png and zip file type allow </small>
                            <small class="form-text text-danger">Maximum file size limit 200 MB!</small>
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1" style="font-weight: bold">Description <b class="text-danger">*</b> </label>
                                <textarea class="form-control assess-edit-input optional-inputs" id="descriptioned" name="description" rows="3"  maxlength="800"></textarea>
                                <p><span id="descriptioned-limit"></span> Description limit 500.</p>
                            </div>
                            <div class="form-group">
                                <div class="row"  style="border:solid 0px #FF0000; width:100%; margin-left:auto; margin-right:auto;font-weight: bold">
                                    <div class="col-10 col-sm-6" style="padding:0px !important;">
                                        <label for="exampleFormControlInput1">Due Date <b class="text-danger">*</b></label>
                                        <input type="date" class="form-control assess-edit-input optional-inputs" id="duedated"  class="form-control" name="duedate" placeholder="Due Date">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group"> 
                                <div class="row"  style="border:solid 0px #FF0000; width:100%; margin-left:auto; margin-right:auto;">
                                    <div class="col-10 col-sm-6" style="padding:0px !important;font-weight: bold">
                                        <label for="latesubmission">Late Submission Allowed</label><br />
                                        <input type="radio" id="latesub-yes-edit"   name="latesub" value="Yes"> &nbsp;Yes
                                        <input type="radio" id="latesub-no-edit"  name="latesub" value="No"> &nbsp;No
                                    </div>
                                    <div class="col-10 col-sm-6" style="padding:0px !important;font-weight: bold">
                                        <label for="resubmission">Re Submission Allowed</label><br />
                                        <input type="radio"  name="resub" id="resub-yes-edit" value="Yes"> &nbsp;Yes
                                        <input type="radio" name="resub" id="resub-no-edit" value="No"> &nbsp;No
                                        <input type="hidden" name="co_id"  value="{{$all_uploads[0]->co_id}}">
                                    </div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">

                <div id="edit-success-msg"  class="msg-div" style="display: none;color: green"><span></span></div>
                <div id="edit-fail-msg"     class="msg-div" style="display: none;color: red"><span></span></div>

<!--
                    <div id="saved_msgg" class="msg-div" style="display: none;color: green"><span>Data Update Successfully</span></div>
                    <div id="obtained_marks_msg" class="msg-div" style="display: none;color: red"><span>Total Marks should be greater than Obtained Marks</span></div>
-->

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                   <!-- <button  name="btn" type="button" class="btn btn-primary" onclick="updtbtn()">Update</button> -->


                    <button  name="btn" type="button" class="btn btn-primary upload_btn" onclick="updtbtn()" >
                        <span class="loading" ><i class="fa fa-spinner fa-spin p-10"></i> &nbsp; Uploading</span>
                        <span class="save" >Save changes</span>
                    </button>

                </div>
            </div>
        </div>
    </div>
<!-- MODAL START -->



    <?php }else{ ?>
   <span style="text-align: center;font-weight: bold"><h1>No Assessment Found</h1></span>
    <?php } ?>

    <script type="text/javascript">


     $('#is_assignment_edit').change(function(e){
            if( !($(this).is(':checked')) ){

                $('.optional-inputs').val('');
                $("#latesub-no-edit").prop("checked", true);
                $("#resub-yes-edit").prop("checked", true);

                $('#error_msg').show();
                $('#error_msg span').html('Detail data is remove!');

                setTimeout(function () {
                    $('#error_msg').hide();
                    $('#error_msg span').html('');
                }, 1800);

            }
        });


     function remove_edit_file(){
        $("#href").attr('href','');
     }

        var downloadPath = '{{asset('docs/')}}';
        var inputQuantity = [];
        $(function() {
            $("#marks").on("keyup", function (e) {
                var $field = $(this),
                    val=this.value,
                    $thisIndex=parseInt($field.data("idx"),10);
                if (this.validity && this.validity.badInput || isNaN(val) || $field.is(":invalid") ) {
                    this.value = inputQuantity[$thisIndex];
                    return;
                }
                if (val.length > Number($field.attr("maxlength"))) {
                    val=val.slice(0, 5);
                    $field.val(val);
                }
                inputQuantity[$thisIndex]=val;
            });
        });
	function dell_file(file_id){
        var values = { 'file_id': file_id };
         $("#"+file_id).remove();
	}
   


  


    $('#descriptioned').keypress(function(e) {
            var tval = $(this).val(),
                tlength = tval.length,
                set = 500,
                remain = parseInt(set - tlength);
            $('#descriptioned-limit').text(remain);
            if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
                $(this).val((tval).substring(0, tlength - 1))
            }
        });


    

    </script>
