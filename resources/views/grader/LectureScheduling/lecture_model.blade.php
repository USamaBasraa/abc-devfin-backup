
<!-- //////////delete content model start //////////////// -->
 <!----------  MODEL 1 START---------------------->
    <div class="modal" id="AddNewContentModal1" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="h5 modal-title" id="ModalTitle">Add Lecture Content Description</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" method="post" name="content_form"  id="add_new_content_form">
                        @csrf
                        <input type="hidden" id="tt_new_content_id" name="tt_new_content_id">
                        <input type="hidden" name="co_id"  value="{{$co_id}}">
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Description</label>
                            <textarea class="form-control" id="add_new_desc" name="desc" rows="3"></textarea>
                        </div>

                        {{--<textarea id="getcontent" name="getcontent" rows="4" cols="72"></textarea>--}}
                    </form>
                </div>
                <div class="modal-footer">
                    <div id="saved_msg_div_new_content" style="display: none;color: green"><span>Data Update Successfully</span></div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="updtnewcontent()">Update</button>
                </div>
            </div>
        </div>
    </div>
    {{--Add New Content Modal End Here--}}
    {{--Content Modal--}}
    <div class="modal" id="ContentModal" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="h5 modal-title" id="ModalTitle">Add Lecture Content</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" method="post" name="content_form"  id="content_form">
                        @csrf
                        <input type="hidden" id="cc">
                        <input type="hidden" id="cc_id" name="cc_id">
                        <input type="hidden" id="coc_content_type" name="coc_content_type">


                        <input type="hidden" id="mtt_co_id" class="mtt_co_id" name="mtt_co_id">

                        <div class="form-group" id="content_div">
                            <label for="exampleFormControlInput1">Content</label>
                            <input type="text" class="form-control" id="getcontent" name="getcontent" readonly="true"  placeholder="Title Here . . .">
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlInput1">Description</label>
                            <textarea class="form-control" id="desc" name="desc" rows="3"></textarea>
                        </div>

                        {{--<textarea id="getcontent" name="getcontent" rows="4" cols="72"></textarea>--}}
                    </form>
                </div>
                <div class="modal-footer">
                    <div id="saved_msg_div" style="display: none;color: green"><span>Data Update Successfully</span></div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="ContentUpdate()">Update</button>
                </div>
            </div>
        </div>
    </div>
  
 <!----------  MODEL 4 upload file START---------------------->
 <div class="modal fade bd-example-modal-lg" id="exampleModalCenter5" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:">
                    <h5 class="modal-title"  id="exampleModalLongTitle">Upload File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" method="post"  name="file_form_upload"  action="{{ url('fileupload_google') }}" id="file_form_upload">
                        @csrf
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Title</label>
                            <input type="hidden" class="form-control" id="file_course_id" name="file_course_id">
                            <input type="hidden" class="form-control" id="file_lec_no" name="file_lec_no">
                            <input type="hidden" class="form-control" id="file_mtt_id" name="file_mtt_id">
                            <input type="text" class="form-control" id="file_title" name="file_title" placeholder="Title Here . . ." maxlength="100">
                        </div>
     
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Upload File</label>
                            <input type="file" class="form-control" name="uploadfile" id="uploadfile" >
                            <div id="error_msg_div" style="display: none1;"class="text-danger"><span> </span></div>

                        </div>
                        <div id="error_msg"  style="color: red"><span></span></div>
                        <br/>
                        <div class="form-group">
                            <div class="progress">
                                 <div style="background-color: steelblue" class="progress-bar"></div>
                            </div><br/>     
                            <div  class="alert " id="uploadStatus"></div>
                        </div>
                        <!-- <button id="file_up" type="submit"  class="btn btn-primary">Save changes</button> -->
                    </form>
                </div>
                <div id="saved_msg_div_file" style="color: green"><span></span></div>
            <!-- File preview --> 
            <div id="filepreview" style="display:none" > 
                        <img src="" class="displaynone1"  with="100px" height="100px"><br>
                        <a href="#" style="font-weight:bold" >Click Here Download..</a>
                </div>
                <div class="modal-footer">
                    <div id="loading" class="preloader3 loader-block" style="display: none;">
                        <h4>Please wait uploading.....</h4>
                    </div>
                    <button type="button" onclick="cancell_ass()" class="btn btn-info " data-dismiss="modal">Close</button>
                    <button id="file_up" type="submit" onclick="uploadfile_google()" class="btn btn-primary"><i id="spin" class="fa fa-spinner1 fa-spin p-101"></i>Upload</button>
                    <button id="file_upload" style="display:none" onclick="cancell_ass()" type="button" data-dismiss="modal" class="btn btn-primary">Save Changes</button>

                    <!-- <button id="file_up" onclick="uploadfile()" type="button" class="btn btn-primary upload_btn">
                        <i class="fa fa-spinner fa-spin p-101"></i> &nbsp; Uploading
                    </button> -->
                </div>
            </div>
        </div>
    </div>
    <!----------  MODEL 4 END  --------------------->

  
    <!---------- Assignment MODEL 3 START---------------------->

    <div class="modal fade" id="exampleModalCenter3" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:">

                    <h5 class="modal-title" style="color: #5260C7" id="exampleModalLongTitle">Upload Assignment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" >
                    <form enctype="multipart/form-data" method="post" name="request_form"  id="request_form">
                    @csrf
                        <div class="row" >
                            <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                <label for="exampleFormControlInput1">Assessments <b class="text-danger">*</b> </label>
                                <select class="form-control assess-form-input"  id="assessment_id" name="assessment_id">
                                    <option value="0">--Select Assessment--</option>
                                    @foreach($course_criteria as $rec_course_criteria)
                                    @php 
                                       $if_selected =  $rec_course_criteria->assessment_id;
                                    @endphp
                                        <option  value="{{$rec_course_criteria->assessment_id.'`'.$rec_course_criteria->id }}" {{$if_selected == '2'?'selected="selected"':''}}>{{ $rec_course_criteria->assessment_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                                <label for="marks"> Marks <b class="text-danger">*</b> </label>
                                <input type="text" onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46"  maxlength="3" class="form-control assess-form-input" min="1"  id="marks" name="marks" placeholder="Marks">
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                    
                                <input type="hidden" class="form-control assess-form-input" id="fcu_id" value="insert">
                                <input type="hidden" class="mtt_co_id" name="co_id"  value="{{$co_id}}">
                                
                                <label for="exampleFormControlInput1">Title <b class="text-danger">*</b> </label>
                                <input type="text"  class="form-control assess-form-input" id="title2" name="title2"  placeholder="Title Here . . ." maxlength="200">

                            </div>
                            <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-6"> 
                                <label for="exampleFormControlInput1">Due Date <b class="text-danger">*</b></label>
                                <input type="date" class="form-control  assess-form-input" id="duedate" name="duedate" placeholder="Due Date">
                            </div>

                        </div>       
                                             
                     
                        <div style="display:none" class="form-group">
                            <label for="is_assignment"><input id="is_assignment" name="is_assignment" data-toggle="collapse"  data-target="#detail" type="checkbox"   value="yes" > &nbsp;Is Assignment</label>
                        </div>
                        <div class="row"  style="border:solid 0px #FF0000; width:100%; margin-left:auto; margin-right:auto;">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" style="padding:0px !important;">
                        <label for="exampleFormControlTextarea1">Description <b class="text-danger">*</b></label>
                        <textarea class="form-control assess-form-input optional-inputs" style="margin-bottom: 0.2rem !important" id="description" name="description" rows="2"></textarea>
                            </div>
                        </div>
                      
                        <input type="hidden" id="is_file" value="" name="is_file" >
                        
                        <div class="row"  style="border:solid 0px #FF0000; width:100%; margin-left:auto; margin-right:auto;">
                            <div class="col-10 col-sm-6" style="padding:0px !important;">
                                <label for="latesubmission">Late Submission Allowed</label><br />
                                <label for="latesub-yes"><input type="radio" id="latesub-yes"   name="latesub" value="Yes"> &nbsp;Yes</label>
                                <label for="latesub-no"><input type="radio" id="latesub-no"  name="latesub" value="No" checked> &nbsp;No</label>
                            </div>
                            <div class="col-10 col-sm-6" style="padding:0px !important;">
                                <label for="resubmission">Re Submission Allowed</label><br />
                                <label for="resub-yes"><input type="radio"  name="resub" id="resub-yes" value="Yes" checked> &nbsp;Yes</label>
                                <label for="resub-no"><input type="radio" name="resub" id="resub-no" value="No"> &nbsp;No</label>


                            </div>
                        </div>    

                        <label for="exampleFormControlInput1">Upload File  </label>
                        <input type="file" class="form-control assess-form-input optional-inputs"  name="uploadfile2" id="uploadfile2" accept=".xlsx,.docx,.pptx,.jpg,.jpeg,.pdf,.png,.zip" >
                        
                        <small class="form-text text-danger">Only xlsx, docx, pptx, jpg, jpeg, pdf, png and zip file type allow. </small>
                        <small class="form-text text-danger">Maximum file size limit 200 MB!</small>
                        <button type="button" class="btn btn-primary"  onclick="resetupload()" >Reset</button>

                       
                  
                      
                    </form>
                </div>
                <div class="assignment_info"></div>
                <div class="modal-footer">
                     <div id="error_assignment" style="color:#EA4335;"><span></span></div>
                    <div id="saved_msg" class="msg-div" style="display: none;color: green"><span>Data Saved Successfully</span></div>
                    <div id="error_msg" class="msg-div" style="display: none;color: red"><span>The File must be a file of type: xlsx, docx</span></div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button id="btn2" name="btn" type="button" class="btn btn-primary upload_btn">Save changes</button>
                </div>
            </div>
        </div>
    </div>
