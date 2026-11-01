

      <!----------  MODEL 4 upload file START---------------------->
      <div class="modal fade" id="fileUploadModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #D2D6F1">
                    <h5 class="modal-title"  id="exampleModalLongTitle">Upload File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" method="post" name="file_form"  id="file_form">
                        @csrf
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Title</label>
                            <input type="hidden" class="form-control" id="file_course_id" name="file_course_id">
                            <input type="hidden" class="form-control" id="file_lec_no" name="file_lec_no">
                            <input type="hidden" class="form-control" id="file_mtt_id" name="file_mtt_id">
                            <input type="text" class="form-control" id="file_title" name="file_title" placeholder="Title Here . . ." maxlength="100">
                            <div id="error_msg_div_file" style="display: none;"class="text-danger"><span> </span></div>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Upload File</label>
                            <input type="file" class="form-control" name="uploadfile" id="uploadfile" >
                            <div id="error_msg_div_file1" style="display: none;"class="text-danger"><span> </span></div>

                        </div>
                    </form>
                </div>
                <div id="saved_msg_div_file" style="display: none;color: green"><span>Data Upload Successfully and notified to Students</span></div>

                <div id="error_msg" class="msg-div" style="display: none;color: red"><span>The File must be a required: xlsx, docx,pdf</span></div>
                    <div id="wrapper" style="color: red" >
                        <p id='post_error'></p>
                    </div>

                <div class="modal-footer">

                    <div id="loading" class="preloader3 loader-block" style="display: none;">
                        <h4>Please wait processing.....</h4>
                    </div>
                    <button type="button" onclick="cancell_ass()" class="btn btn-info " data-dismiss="modal">Close</button>
                    <button id="file_up" type="button" onclick="uploadfile()" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <!----------  MODEL 4 END  --------------------->