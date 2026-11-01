@extends('layouts.app',['pageTitle' => 'Course Content'])
@section('content')
{{-- @dd("123"); --}}
<!-- Tabs css-->
<link href="{{ asset('plugins/tabs/style.css') }}" rel="stylesheet" />
<!-- <script src="{{ asset('plugins/tabs/jquery.multipurpose_tabcontent.js') }}"></script> -->
<!-- <script src="{{ asset('s/tabs.js') }}"></script> -->

@foreach($teacher_courses as $teacher_course)
@if($teacher_course->fc_co_id==$co_id)
<?php
$courses_idd = $teacher_course->fc_co_course_id;
?>
@endif
@endforeach


<style>
    li .active {
        border-bottom: #f72d66 !important;
        border-width: 2px !important;
        background-color: darkgray;
    }

    #btn:focus {
        color: green;
        border: 2px solid steelblue;
    }

    /* .first-letter {
  text-transform: capitalize;
} */
    .demo_file_link {
        text-decoration: underline;
        curser: pointer;
    }

    .capitalize-first {
        /* text-transform: lowercase; */
    }

    .capitalize-first::first-letter {
        text-transform: uppercase;
    }
</style>

<?php

$show = 0;
foreach ($courses_content as $courses_content_rec) {
    if ($courses_content_rec->cc_type == 'description') {
        $show++;
    }
}
?>

<div class="container-flauid" style="padding-left: 0 !important;padding-right:0 !important;">
    <div>
        <div class=" nav-justified hemi-intro-selected">
            <h5>Course Outline</h5>
            <h3 class="card-title ">{{$course_name}} </h3>
        </div>
        <div class="card-body" style="padding: 0px;">
            <div class="tab_wrapper first_tab">

                <!-- start of tab menu ----->
                <ul class="nav  tab_list" id="pills-tab" role="tablist" style="
    border-bottom: 2px solid #17675F1A !important;
    padding-bottom: 2px;
">
                    <li>
                        <a class="nav-link active" style="color: #0c0c0c" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true"> Description </a>
                    </li>
                    <li>
                        <a class="nav-link" style="color: #0c0c0c" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">
                            Course Content</a>
                    </li>
                 
                    <li>
                    <a class="nav-link" style="color: #0c0c0c" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">
                    Upload Course Outline </a>
                   </li>
                

                    <!-- <div  style="margin-left:40%" class="form-group">
                    <button id="btn" name="btn" onclick="final_submit()" class="btn btn-success btn-submit-final">Final Submit</button>
                </div> -->
                </ul>
                <!-- end of tab menu ----->
                <!-- start of tab contents ----->




                <div class="tab-content  " id="pills-tabContent">


                    <div class="tab_content tab-pane  show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <!--- First Tab Start ----->

                        <div id="description_form_container" class="container-fluid" style=" padding-top: 2%;padding-bottom: 2%;    border-radius: 5px; background-color:white;margin-top:21px; display: {{ $show > 0 ? 'none' : 'block' }};">
                            <form enctype="multipart/form-data" method="post" name="cformdes" id="cformdes">

                                <div class="form-group" style="display: none;">
                                    <label for="course_name">Course Name</label>
                                    <select class="form-control" name="fc_co_id" id="fc_co_id">
                                        @foreach($teacher_courses as $teacher_course)
                                        @if($teacher_course->fc_co_id==$co_id)
                                        <option value="{{$teacher_course->fc_co_id}}">{{$teacher_course->course_cd_name}}</option>
                                        <input type="text" class="form-control" name="course_id" id="course_id" value="<?= $teacher_course->fc_co_course_id; ?>">
                                        <input type="text" class="form-control" name="fc_co_sess" id="fc_co_sess" value="<?= $teacher_course->fc_co_sess_id; ?>">
                                        @endif
                                        @endforeach

                                    </select>
                                </div>



                                <div class="row" id="desc_row">
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"> <strong>Description</strong></div>

                                    <div class="col-12 col-sm-10 col-md-10 col-lg-10 col-xl-10">
                                        <input type="hidden" class="form-control" name="id" id="id" rows="3"></input>
                                        <!-- <textarea class="form-control " name="description" id="description" rows="3" placeholder="Please enter Description of course {{$course_name}}"></textarea> -->
                                        <textarea class="form-control " name="description" id="description" rows="3" placeholder="Please enter the description for the course {{$course_name}}."></textarea>
                                    </div>
                                    <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                                        <button id="btn" type="button" name="btn" onclick="Add_desc()" class="btn btn-success btn-submit-desc">
                                            <a href="#" class="btn btn-squared-default-plain btn-success">
                                               
                                            
                                                Add
                                            </a>
                                        </button>
                                    </div>

                                    <div id="saved_msg_des" style="color: green; padding-left:12px;"></div>
                                    <div id="error_msg" style="display: none;color: red;padding-left:12px;"><span>Request Failed,Server Error</span></div>
                                    <div id="error_msg1" style="color: red;padding-left:12px;"><span></span></div>


                                </div>
                            </form>
                        </div>

                        <!---- First Tabl End ---->
@if($show > 0)
                        <div class="yoo-card" id="simpledescription" style="
    margin-bottom: 25px;
">
                            <!-- <div class="yoo-card-heading">
                            <h3>Uploaded Content </h3>
                        </div> -->
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                    <br>


                                    <?php foreach ($courses_content as $courses_content_rec) {
                                        if ($courses_content_rec->cc_type == 'description') {
                                    ?>
                                            <label for="des" class="col-sm-2 control-label"><strong>Description</strong></label>
                                            <p class="capitalize-first" style="padding-left: 15px;">{{$courses_content_rec->cc_lecture_content}}</p>
                                            <textarea type="hidden" style="display:none" id="desc">{{$courses_content_rec->cc_lecture_content}}</textarea>
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="get_des(<?= $courses_content_rec->id; ?>)"><i class="fa fa-edit"></i>Edit</button>
                                    <?php }
                                    } ?>
                                </div>
                                <br>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="tab_content tab-pane " id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <!--- SECOND Tab Start ----->

                        <div class="yoo-card course-content-card">
                            <div class="card-body" id="con">
                                <form enctype="multipart/form-data" method="post" name="cform" id="cform">
                                    <input type="hidden" name="form_name" value="cform14">
                                    <input style="display:none" class="form-control" type="text" name="con_id" id="con_id" />
                                    <input type="hidden" class="form-control" type="text" name="order_id" id="order_id" />

                                    <div class="form-group" style="display: none;">
                                        <label for="course_name">Course Name</label>
                                        <select class="form-control" name="fc_co_id" id="fc_co_id">
                                            @foreach($teacher_courses as $teacher_course)
                                            <?= $teacher_course->fc_co_id; ?>
                                            @if($teacher_course->fc_co_id==$co_id)
                                            <option value="{{$teacher_course->fc_co_id}}">{{$teacher_course->course_cd_name}}</option>
                                            <input type="text" class="form-control" name="course_id" id="course_id" value="<?= $teacher_course->fc_co_course_id; ?>">
                                            <input type="text" class="form-control" name="fc_co_sess" id="fc_co_sess" value="<?= $teacher_course->fc_co_sess_id; ?>">
                                            @endif
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="row course-content-row">
                                        <div class="col-12 col-sm-12 col-md-10 col-lg-10 col-xl-10 course-content-left">
                                            <div class="form-group ">

                                            <div class="popover fade show bs-popover-right got-it" role="tooltip" style=" margin-left: 131px; top: -16px; z-index:700;max-width: 285px;" x-placement="right">
                                                <div class="arrow" style="top: 16px;"></div>
                                                <!-- <h3 class="popover-header">Info</h3> -->
                                    <div class="popover-body">
                                        <!-- <h6> Press enter <strong style="font-size: 18px;">&#8626;</strong> after putting your content </h6> -->
                                        <h6> Press Enter  after entering your content </h6>
                                        <button  class="btn btn-primary" onclick="hideGotIt();" type="button" style="float: right; margin-bottom: 10px;background-color:#17675F;border:none !important;">Got It</button>
                                     </div>
                                </div>

                                            <!-- <label for="lec_content" class="intro_1"><strong>Lecture Content </strong></label> -->
                                            <label for="lec_content" class="intro_1"><strong>Course Content </strong></label>

                                                <!-- <textarea class="form-control" name="lec_content" id="lec_content" rows="3" placeholder="Please enter Content of course {{$course_name}}"></textarea> -->
                                                <textarea class="form-control" name="lec_content" id="lec_content" rows="3" placeholder="Please enter the content for the course {{$course_name}}."></textarea>
                                            </div>
                                            <div class="form-group" id="contentOrder" style="display:none1">
                                                <label for="lec_content"><strong>Content Order</strong></label>

                                                <!-- data-limit='120' class='short' style="width: 250px; overflow: hidden;text-overflow: ellipsis;"
                                    -->
                                                <select class="form-control" name="new_order" id="con_order">
                                                    <!-- <option value="0">--Select Content Order --</option> -->
                                                    <?php
                                                    $count_dropdown = count($sqlcontentresult2);
                                                    ?>
                                                    @if($count_dropdown>0)
                                                    @foreach($sqlcontentresult2 as $courses_rec)
                                                    <!-- <option data-limit='120' class='short' style="width: 250px; overflow: hidden;text-overflow: ellipsis;" value="">Select One </option> -->
                                                    <option value="{{$courses_rec->cc_order}}">
                                                        Move Content at Position {{$courses_rec->cc_order}}
                                                    </option>
                                                    <?php
                                                    $count_dropdown--;
                                                    ?>
                                                    @endforeach
                                                    @endif
                                                </select>


                                            </div>
                                            <div class="form-group">
                                                <!-- accept=".csv,.docx,.doc" course_content -->
                                                <label for="lec_content">
                                                    <strong>Download Sample:</strong>
                                                    <a href="{{ url('docs/Book.csv') }}" class="demo_file_link">CSV Extension</a> and <a href="{{ url('docs/demo_content_word_file.docx') }}" class="demo_file_link">MS Word Extension</a>  
                                                </label>
                                                <input type="file" class="form-control" accept=".csv,.docx,.doc" name="content_file" id="uploadfile">
                                                <!-- <label style="color:blue;font-weight:bold">After Selecting Content File Please Press "Add" Button </label> -->
                                                <label style="color:#FFBA49;"><strong>Note: </strong> After selecting the content file, please press the "Upload File" button. </label>
                                            </div>
                                                <span id="lblError" style="color: red;"></span>

                                            <div class="upload-content-msg form-group" style="color: #f72d66;"></div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-2 col-lg-2 col-xl-2 course-content-right" style="padding-top: 3%;">
                                            <div align="center" class="form-group course-content-btn-group">
                                                <button id="btn" name="btn" class="btn btn-success btn-submit" type="button">
                                                    <a href="#" class="btn btn-squared-default-plain btn-success" style="padding-left: 0 !important;">
                                                        <i id="addbtn" class=""></i>
                                                        <i id="edbtn" class=""></i>
                                                        
                                                        <span class="edit-btn">Add</span>
                                                    </a>
                                                </button>
                                                <!-- <button id="btn" name="btn" class="btn btn-success btn-submit upload-content-btn">Submit</button> -->
                                            </div>
                                        </div>
                                        <div id="saved_msg1" style="color: green"></div><br />
                                    </div>
                                    <div id="record"><br></div>
                                </form>

                            </div>
                        </div>

                        <!---- SECOND Tabl End ---->
                        <div class="yoo-card" style="margin-top: 0%;">
                            <div class="page-body">
                                <div class="card-block">
                                    <div class="dt-responsive table-responsive course-content-table-wrap" class="container" id="reload" style="height: 400px;overflow-y: scroll;scrollbar-color: #2F3B4D white; scrollbar-width: thin;">
                                        <table id="simpletable" class="table  table-bordered dt-responsive course-content-table" style="width:100% ">
                                            <thead>
                                                <tr>
                                                    <!-- <td width="70" style="font-size: 12px;font-weight:bold" align="center"><b>Sr #</b></td> -->
                                                    <td width="70" style="font-size: 12px;font-weight:bold" align="left"><b>Sr. No.</b></td>
                                                    <td width="700" style="font-size: 12px;font-weight:bold" align="left"><b>Lecture Content</b></td>
                                                    <!-- <td  style="font-size: 12px;font-weight:bold" align="center"><b>Content <br> Order </b></td> -->
                                                    <td style="font-size: 12px;font-weight:bold" align="left"><b>Action</b></td>
                                                </tr>
                                            </thead>
                                            <?php
                                            $count = 1;
                                            $order = count($sqlcontentresult);

                                            ?>
                                            @if($order>0)
                                            <tbody>

                                                @foreach($sqlcontentresult as $courses_rec)

                                                <tr>
                                                    <?php
                                                    if ($courses_rec->cc_lecture_description == '' && $courses_rec->cc_type == 'content') { ?>
                                                        <td align="left" class="course-content-actions">{{$count}} </td>
                                                        <td style="font-size: 12px;width:70%">
                                                            <p class="capitalize-first">{{$courses_rec->cc_lecture_content}} </p>
                                                            <!-- <td align="center">{{$courses_rec->cc_order}}</td> -->
                                                        <td align="left" class="course-content-actions">
                                                            <a href="javascript:void(0)" id="as" onclick="edit_con('<?= $courses_rec->id; ?>','<?= $courses_rec->cc_lecture_no; ?>','<?= $courses_rec->cc_lecture_content; ?>','<?= $courses_rec->cc_order; ?>')"><button class="btn btn-sm"><i class="fa fa-edit"></i> Edit</button></a>
                                                            <a href="javascript:void(0)" id="as" onclick="delete_con('<?= $courses_rec->id; ?>','<?= $courses_rec->cc_co_id; ?>')"><button class="btn btn-sm"><i class="fa fa-trash"></i> Delete</button></a>
                                                        </td>
                                                    <?php
                                                        $count++;
                                                        $order--;
                                                    } else if (count($sqlcontentresult) == 1) { ?>
                                                <tr>
                                                    <td colspan="4" align="center"><b>No Content add currently</b></td>
                                                </tr>
                                            <?php }

                                            ?>

                                            @endforeach

                                            </tbody>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab_content tab-pane " id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <!--- THIRD Tab Start ----->
  <br/>
								<form enctype="multipart/form-data" method="post" name="form_file" id="form_file" class="card">
                                    <div class="form-group" style="display: none;">
                                        <label for="course_name">Course Name</label>
                                        <select class="form-control" name="fc_co_id" id="fc_co_id">
                                            @foreach($teacher_courses as $teacher_course)
                                            @if($teacher_course->fc_co_id==$co_id)
                                            <option value="{{$teacher_course->fc_co_id}}">{{$teacher_course->course_cd_name}}</option>

                                            <input type="text" style="display: none;" class="form-control" name="course_id" id="course_id" value="<?= $teacher_course->fc_co_course_id; ?>">
                                            <input type="text" style="display: none;" class="form-control" name="fc_co_sess" id="fc_co_sess" value="<?= $teacher_course->fc_co_sess_id; ?>">
                                            @endif
                                            @endforeach

                                        </select>

                                    </div>
                                
									<div class=" card-body">
										<div class="row ">
											 <div class="form-group col-12 col-sm-12 col-md-8 col-lg-8 col-xl-8 upload-file-field">
															<label for="lec_content">
																<!-- <strong>Upload File </strong> -->
																<!-- ( Docx,Doc And PDF Extension Are Allowed ) -->
                                                                <strong>Download Sample:</strong>
																Only DOCX, DOC and PDF formats are allowed.
															</label><span id="error"></span>                                                
															<input   type="file" class="form-control" accept=".pdf,.docx,.doc" name="fileCon" id="fileCon" >
															
                                                            <!-- <label style="color:blue;font-weight:bold">After Selecting Content File Please Press "Add" Button </label> -->
                                                            <label style="color:#FFBA49;margin-bottom:0px;"> <strong>Note:</strong>After selecting the file, please press the "Upload File" button. </label>
														</div>
													
														<div class="col-12 col-sm-12 col-md-2 col-lg-2 col-xl-2 upload-file-btn-wrap" style="padding-top: 0%;">
														<div align="center" class="form-group">
															<button id="btn" name="btn" class="btn btn-success btn-submit-file" type="button" style="background-color: #1F776E !important;">
																<a href="#" class="btn btn-squared-default-plain btn-success" style="
    background: #1F776E !important;
    border: 1px solid #1F776E;
">
																	{{-- <i id="addbtn" class="fa fa-plus fa-2x"></i> --}}
																	{{-- <i id="spin" class="fa fa-spinner1 fa-spin p-101"></i> --}}
																	{{-- <br /> --}}
																	<span class="edit-btn">Upload File </span>
																</a>
															</button>
														</div>
													</div>
													</div>
												</div>
												<div class="file_msg"> </div>
                                              
								</form>


                               
                                <div id="fileUpload"> 
                                @if(isset($resfile->id) && ($resfile->id !=''))
                                    <button type="button" onclick="Remove('<?=(isset($resfile->id)!='')?$resfile->id:'';?>')" class="btn btn-outline-secondary btn-lg btn-block"><i id="spremove" class="fa fa-spinner1 fa-spin p-101"></i>Remove File</button>
                                @endif
                                <br/>
                                <?php if(isset($resfile->cc_lecture_description) && ($resfile->cc_lecture_description!='')){
                                        if($resfile->cc_lecture_description=='pdf') {  
                
                                            ?>

                                    <!-- <label style="color:blue;font-weight:bold">Please <a href="<?=(isset($resfile->cc_file_name)!='')?$resfile->cc_file_name:'';?> target='_blank'"> Download File</a> From here and Disable 
                                Internet Download Manager For Preview the Course Outline File  </label> -->
                                <label style="color:blue;font-weight:bold">Please <a href="<?=(isset($resfile->cc_file_name)!='')?$resfile->cc_file_name:'';?> target='_blank'"> download the file</a> from here and disable 
                                Internet Download Manager to preview the course outline file.  </label>
                                <iframe src="<?=(isset($resfile->cc_file_name)!='')?$resfile->cc_file_name:'';?>" width='100%' height='500px' frameborder='0'></iframe>

                                    <?php   
                                } else{ ?>
                                
                                <label style="color:blue;font-weight:bold">Please <a href="<?=(isset($resfile->cc_file_name)!='')?$resfile->cc_file_name:'';?> target='_blank'"> download the file</a> from here and disable 
                                Internet Download Manager to preview the course outline file.  </label>
                                    <!-- <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?=(isset($resfile->cc_file_name)!='')?$resfile->cc_file_name:'';?>" width='100%' height='500px' frameborder='0'>This is an embedded <a target='_blank' href='<?=url('/')?>/docs/'>Microsoft Office</a> document, powered by <a target='_blank' href='http://office.com/webapps'>Office Online</a>.</iframe> -->
                                    <iframe src="https://drive.google.com/file/d/<?=isset($resfile->cc_file_original_name) ? urlencode($resfile->cc_file_original_name) : '';?>/preview" width='100%' height='500px' frameborder='0'>This is an embedded <a target='_blank' href='<?=url('/')?>/docs/'>Microsoft Office</a> document, powered by <a target='_blank' href='http://office.com/webapps'>Office Online</a>.</iframe>

                                  <?php } }  ?>
                                  </div>
                        <!--<div class="yoo-card">
                            <div class="card-body" id="con1" style="">
                                <form enctype="multipart/form-data" method="post" name="cform_cri" id="cform_cri">
                                    <div class="form-group" style="display: none;">
                                        <label for="course_name">Course Name</label>
                                        <select class="form-control" name="fc_co_id" id="fc_co_id">
                                            @foreach($teacher_courses as $teacher_course)
                                            @if($teacher_course->fc_co_id==$co_id)
                                            <option value="{{$teacher_course->fc_co_id}}">{{$teacher_course->course_cd_name}}</option>

                                            <input type="text" style="display: none;" class="form-control" name="course_id" id="course_id" value="<?= $teacher_course->fc_co_course_id; ?>">
                                            <input type="text" style="display: none;" class="form-control" name="fc_co_sess" id="fc_co_sess" value="151">
                                            @endif
                                            @endforeach

                                        </select>

                                    </div>
                                    <div class="form-group">
                                        <label for="LectureNo">Assessments</label>
                                        <select class="form-control" name="assessment" id="assessment">
                                            @foreach($assessments as $assessments_course)
                                            <option value="{{$assessments_course->id}}">{{$assessments_course->assessment_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="weightage">weightage</label>
                                        <input class="form-control" name="weightage" id="weightage"></input>
                                    </div>



                                    <div align="center" class="form-group">
                                        <button id="btn" name="btn" class="btn btn-success btn-submit-cri ">Submit</button>
                                    </div>

                                    <div id="saved_msg2" style="display: none;color: green"><span>Data Saved Successfully</span></div>
                                </form>

                            </div>
                        </div> --->
                        <!---- THIRD Tabl End ---->
                       <!-- <div class="yoo-card" style="margin-top: 0%;">
                            <div class="yoo-card-heading" style="background-color:#2F3B4D !important; color:#FFF; ">
                                <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left">Uploaded Criteria </h3>
                            </div>
                            <div class="page-body">
                                <div class="card-block">
                                    <div class="container" id="reload" style="height: 200px;overflow-y: scroll;scrollbar-color: #2F3B4D white; scrollbar-width: thin;">


                                        <table id="simpleweightage" class="table table-striped table-bordered dt-responsive nowrap" style="width:100% ">
                                            <thead>
                                                <tr>
                                                    <th style="font-size: 14px;font-weight:bold;">ASSESSMENTS</th>
                                                    <th style="font-size: 14px;font-weight:bold" align="center">WEIGHTAGE</th>
                                                    <th style="font-size: 14px;font-weight:bold">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($criteria as $courses_content_rec)
                                                <tr>


                                                    <td>{{$courses_content_rec->assessment_name}} </td>
                                                    <td style="font-size: 12px">
                                                        {{$courses_content_rec->cc_weightage.'%'}}
                                                    </td>
                                                    <td><a href="javascript:void(0)" id="as" onclick="delete_con('<?= $courses_content_rec->id; ?>')"><button class="btn btn-sm"><i class="fa fa-trash"></i> Delete</button></a> </td>


                                                </tr>

                                                @endforeach
                                            </tbody>

                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div> -->

                    </div>
                </div>

            </div>
        </div>
    </div>




<div class="hemi-intro-backdrop got-it"  ></div>
<style>
.hemi-intro-backdrop{
    position: fixed;
    left: 0;
    bottom: 0;
    top: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 500;
    display: none
}
/* ==========================
   Course Tabs UI
========================== */

.tab_list {
    display: flex;
    gap: 10px;
    border-bottom: none !important;
    padding-bottom: 10px;
}

.tab_list li {
    margin: 0;
}

.tab_list .nav-link {
    background: #17675F1A;
    color: #17675F80 !important;
    border: none !important;
    border-radius: 999px;
    padding: 10px 28px;
    font-size: 18px;
    font-weight: 500;
    transition: all .25s ease;
}

.tab_list .nav-link i {
    color: inherit;
}

.tab_list .nav-link:hover {
    background: #17675F26;
    color: #17675F !important;
}

.tab_list .nav-link.active,
.tab_list .nav-link.active:hover {
    background: #1F776E !important;
    color: #fff !important;
    position: relative;
}

/* Bottom active indicator */
.tab_list .nav-link.active::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: -12px;
    height: 4px;
    background: #1F776E;
    border-radius: 4px;
}

.tab_list .nav-link.active i {
    color: #fff;
}
.tab_wrapper > ul li {
    background:none !important;
    padding-left: 0px;
}
.tab_wrapper>ul li {
    border: none !important;
}













/* Description Section */
#desc_row strong{
    display: block;
    margin-bottom: 8px;
    font-size: 16px;
    font-weight: 500;
    color: #2d2d2d;
}

/* Textarea */
#description{
    width: 100%;
    min-height: 118px;
    padding: 14px 16px;
    border: 1px solid #D9D9D9;
    border-radius: 4px;
    background: #fff;
    color: #2d2d2d;
    font-size: 15px;
    line-height: 1.6;
    resize: vertical;
    box-shadow: none;
    transition: all .2s ease;
}

#description::placeholder{
    color: #B6B6B6;
    font-size: 15px;
}

#description:focus{
    border-color: #17675F;
    box-shadow: 0 0 0 3px rgba(23,103,95,.08);
    outline: none;
}

/* Make button align like screenshot */
.btn-submit-desc{
    margin-top: 26px;
    border: none !important;
    background: transparent !important;
    padding: 0;
}

.btn-submit-desc .btn-squared-default-plain{
    background: #17675F !important;
    border-radius: 4px;
    width: 90px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 0;
    font-size: 16px;
    font-weight: 500;
    text-decoration: none;
}

.btn-submit-desc .fa{
    font-size: 18px !important;
    margin: 0;
}
/* Make textarea column full width */
#desc_row .col-sm-10,
#desc_row .col-md-10,
#desc_row .col-lg-10,
#desc_row .col-xl-10{
    flex: 0 0 100%;
    max-width: 100%;
}

/* Move button below textarea */
#desc_row .col-sm-2,
#desc_row .col-md-2,
#desc_row .col-lg-2,
#desc_row .col-xl-2{
    flex: 0 0 100%;
    max-width: 100%;
    margin-top: 16px;
    text-align: left;
}

/* Full width textarea */
#description{
    width:100%;
    min-height:130px;
    resize:vertical;
}

/* Button styling */
.btn-submit-desc{
    margin-top:0;
    padding:0;
    border:none !important;
    background:transparent !important;
}

.btn-submit-desc .btn-squared-default-plain{
    width:90px;
    height:40px;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    background:#17675F !important;
    border-radius:4px;
    text-decoration:none;
}
/* ==========================================
   Uploaded Description Card
========================================== */

#simpledescription{
    background: #ffffff;
    border: 1px solid #E8ECEF;
    border-radius: 10px;
    padding: 24px;
    margin-top: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,.03);
}

#simpledescription label{
    display: block;
    margin-bottom: 12px;
    font-size: 22px;
    font-weight: 700;
    color: #2D2D2D;
}

#simpledescription p{
    margin: 0 0 24px;
    padding: 0;
    font-size: 16px;
    line-height: 1.8;
    color: #4B5563;
    word-break: break-word;
}

/* Edit Button */
#simpledescription .btn-outline-info{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    min-width: 95px;
    height: 42px;

    border: 1px solid #0D99FF;
    border-radius: 6px;

    background: #fff;
    color: #0D99FF;

    font-size: 15px;
    font-weight: 600;

    transition: .25s;
}

#simpledescription .btn-outline-info:hover{
    background: #0D99FF;
    color: #fff;
}

#simpledescription .btn-outline-info i{
    font-size: 15px;
}


/* Make file input section full width */
.upload-file-field{
    flex: 0 0 100% !important;
    max-width: 100% !important;
}

/* Button in new line */
.upload-file-btn-wrap{
    flex: 0 0 100% !important;
    max-width: 100% !important;
    margin-top: 16px;
    text-align: left !important;
}

/* File input full width */
.upload-file-field #fileCon{
    width: 100%;
}

/* Align button to left */
.upload-file-btn-wrap .form-group{
    margin-bottom: 0;
    text-align: left !important;
}

/* Button styling */
.upload-file-btn-wrap .btn-submit-file{
    margin: 0;
    padding: 0;
    border: none !important;
    background: transparent !important;
}

.upload-file-btn-wrap .btn-squared-default-plain{
    width: 150px;
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #34C985 !important;
    border-radius: 4px;
    text-decoration: none;
}

.upload-file-btn-wrap .fa-plus{
    font-size: 18px !important;
}

.upload-file-btn-wrap br{
    display: none;
}



/* =====================================================
   Course Content (New CSS Only)
===================================================== */

.course-content-row{
    display:flex;
    flex-wrap:wrap;
}

/* Left side becomes full width */
.course-content-left{
    flex:0 0 100% !important;
    max-width:100% !important;
}

/* Labels */
.course-content-left label{
    margin-bottom:8px;
    font-size:16px;
    font-weight:600;
    color:#2D2D2D;
}

/* Textarea */
.course-content-left #lec_content{
    width:100%;
    min-height:120px;
    padding:14px 16px;
    border:1px solid #D9D9D9;
    border-radius:5px;
    background:#fff;
    font-size:15px;
    color:#2D2D2D;
    resize:vertical;
    box-shadow:none;
}

.course-content-left #lec_content:focus{
    border-color:#17675F;
    box-shadow:0 0 0 3px rgba(23,103,95,.08);
    outline:none;
}

/* Select */
.course-content-left #con_order{
    width:100%;
    height:46px;
    border:1px solid #D9D9D9;
    border-radius:5px;
    box-shadow:none;
}

/* File input */
.course-content-left #uploadfile{
    width:100%;
    border:1px solid #D9D9D9;
    border-radius:5px;
}

/* Right column moves below */
.course-content-right{
    flex:0 0 100% !important;
    max-width:100% !important;
    margin-top:18px;
    padding-top:0 !important;
}

/* Left align button */
.course-content-btn-group{
    text-align:left !important;
    margin-bottom:0;
}

/* Remove old wrapper styling */
.course-content-right .btn-submit{
    padding:0;
    margin:0;
    border:none !important;
    background:transparent !important;
}

/* Green button */
.course-content-right .btn-squared-default-plain{
    width:100px;
    height:42px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    background:#17675F !important;
    border:none !important;
    border-radius:4px;
    text-decoration:none;
}

/* Hide old line break */
.course-content-right br{
    display:none;
}

.course-content-right #addbtn,
.course-content-right #edbtn{
    font-size:17px !important;
}




/* ==========================================
   Course Content Table
========================================== */

.course-content-table-wrap{
    height:auto !important;
    max-height:400px;
    overflow-y:auto;
    background:#ffffff;
    border:1px solid #E7ECEF;
    border-radius:8px;
}

/* Table base */
.course-content-table{
    width:100%;
    margin-bottom:0;
    border-collapse:separate;
    border-spacing:0;
    background:#ffffff;
}

/* Header */
.course-content-table thead td,
.course-content-table thead th{
    padding:16px 14px;
    background:#17675F1A;
    color:#2F4A47;
    font-size:14px !important;
    font-weight:700 !important;
    vertical-align:middle;
    border-top:none !important;
    border-bottom:1px solid #DDE7E5 !important;
}

/* Body cells */
.course-content-table tbody td{
    padding:16px 14px;
    color:#3F4854;
    font-size:14px !important;
    vertical-align:middle;
    border-color:#EDF1F3 !important;
    background:#ffffff;
}

/* Row hover */
.course-content-table tbody tr:hover td{
    background:#17675F0A;
}

/* Serial number column */
.course-content-table thead td:first-child,
.course-content-table tbody td:first-child{
    width:85px;
    text-align:center;
}

/* Content text */
.course-content-table tbody p{
    margin:0;
    color:#3F4854;
    font-size:14px;
    line-height:1.5;
}

/* Action column */
.course-content-actions{
    white-space:nowrap;
}

/* Edit and Delete links */
.course-content-actions a{
    display:inline-block;
    margin:0 4px;
    text-decoration:none !important;
}

/* Action buttons */
.course-content-actions .btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    min-width:82px;
    height:36px;
    padding:0 12px;
    border:1px solid #D7E1E0;
    border-radius:5px;
    background:#ffffff;
    color:#3F4854;
    font-size:14px;
    transition:.2s ease;
}

/* Edit hover */
.course-content-actions a:first-child .btn:hover{
    border-color:#17675F;
    background:#17675F;
    color:#ffffff;
}

/* Delete hover */
.course-content-actions a:last-child .btn:hover{
    border-color:#DC3545;
    background:#DC3545;
    color:#ffffff;
}

.course-content-actions .btn i{
    font-size:14px;
}

/* Scrollbar */
.course-content-table-wrap::-webkit-scrollbar{
    width:7px;
}

.course-content-table-wrap::-webkit-scrollbar-track{
    background:#F4F6F7;
}

.course-content-table-wrap::-webkit-scrollbar-thumb{
    background:#B7C7C4;
    border-radius:10px;
}


/* ==========================================
   Course Content White Card
========================================== */

.course-content-card{
    background:#ffffff;
    border:1px solid #E7ECEF;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,.03);
    padding:12px;
    margin-top:20px;
    margin-bottom: 16px !important;
}

.course-content-card > .card-body{
    padding:20px;
}





































/* =========================================================
   COURSE CONTENT / COURSE OUTLINE
   FIGMA STYLE OVERRIDE ONLY

   Figma:
   Description          -> 2:79
   Course Content       -> 14:411
   Upload Course Outline-> 14:754
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');


/* =========================================================
   1. PAGE TYPOGRAPHY
   ========================================================= */

.container-flauid {
    font-family: 'Montserrat', sans-serif !important;
    color: #252525;
}

/* Existing page heading:
   Course Outline */
.hemi-intro-selected h5 {
    margin: 0 0 7px 0 !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 20px !important;
    font-weight: 600 !important;
    line-height: 1.2 !important;

    color: #252525 !important;
}

/* Course name */
.hemi-intro-selected .card-title {
    margin: 0 0 20px 0 !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 18px !important;
    font-weight: 400 !important;
    line-height: 1.25 !important;

    color: #252525 !important;
}


/* =========================================================
   2. FIGMA TABS
   ========================================================= */

.tab_wrapper > .tab_list,
.tab_list {
    display: flex !important;
    align-items: center !important;

    gap: 8px !important;

    margin: 0 !important;
    padding: 0 0 15px 0 !important;

    border: 0 !important;
    border-bottom: 2px solid rgba(23, 103, 95, 0.10) !important;

    overflow: visible !important;
}

.tab_list li {
    position: relative;
    margin: 0 !important;
    padding: 0 !important;

    background: transparent !important;
    border: 0 !important;
}


/* All pills */
.tab_list .nav-link {
    position: relative !important;

    height: 50px !important;

    padding: 0 26px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: rgba(23, 103, 95, 0.10) !important;

    border: 0 !important;
    border-radius: 50px !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 18px !important;
    font-weight: 500 !important;
    line-height: 1 !important;

    color: rgba(23, 103, 95, 0.50) !important;

    text-decoration: none !important;

    box-shadow: none !important;

    transition:
        background-color .2s ease,
        color .2s ease !important;
}


/* Exact Figma desktop pill widths */
#pills-home-tab {
    min-width: 190px;
}

#pills-profile-tab {
    min-width: 208px;
}

#pills-contact-tab {
    min-width: 260px;
}


/* Active */
.tab_list .nav-link.active {
    color: #ffffff !important;
}


/* Description active */
#pills-home-tab.active {
    background: #1F776E !important;
}


/* Course Content + Upload active */
#pills-profile-tab.active,
#pills-contact-tab.active {
    background: #17675F !important;
}


/* Remove old hover treatment */
.tab_list .nav-link:hover:not(.active) {
    background: rgba(23, 103, 95, 0.13) !important;
    color: #17675F !important;
}


/* Active green bottom indicator */
.tab_list .nav-link.active::after {
    content: "" !important;

    position: absolute !important;

    left: 0 !important;
    right: 0 !important;

    bottom: -17px !important;

    height: 4px !important;

    background: #17675F !important;

    border-radius: 4px !important;
}


/* =========================================================
   3. TAB CONTENT SPACING
   ========================================================= */

#pills-tabContent {
    margin-top: 20px !important;
}


/* =========================================================
   4. DESCRIPTION WHITE FORM CARD
   ========================================================= */

#pills-home > .container-fluid {
    margin-top: 0 !important;

    padding: 20px !important;

    background: #ffffff !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;
}


/* Description label */
#desc_row strong {
    margin-bottom: 11px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 500 !important;
    line-height: 1.2 !important;

    color: #000000 !important;
}


/* Description textarea */
#description {
    width: 100% !important;

    min-height: 151px !important;
    height: 151px;

    padding: 14px !important;

    background: #ffffff !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;

    color: #252525 !important;

    resize: vertical;
}


#description::placeholder {
    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;

    color: #AAAAAA !important;

    opacity: 1;
}


#description:focus {
    border-color: #17675F !important;

    outline: 0 !important;
    box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.08) !important;
}


/* =========================================================
   5. FIGMA ADD BUTTON
   ========================================================= */

.btn-submit-desc,
.course-content-right .btn-submit {
    margin: 0 !important;

    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;

    box-shadow: none !important;
}


/* Description Add */
.btn-submit-desc .btn-squared-default-plain,

/* Course Content Add */
.course-content-right .btn-squared-default-plain {
    width: 114px !important;
    min-width: 114px !important;

    height: 50px !important;

    padding: 0 14px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    gap: 9px !important;

    background: #1F776E !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 500 !important;
    line-height: 1 !important;

    color: #ffffff !important;

    text-decoration: none !important;
}


.btn-submit-desc .fa-plus,
.course-content-right #addbtn,
.course-content-right #edbtn {
    margin: 0 !important;

    font-size: 18px !important;

    color: #ffffff !important;
}


.btn-submit-desc br,
.course-content-right br {
    display: none !important;
}


.btn-submit-desc:focus,
.course-content-right .btn-submit:focus {
    outline: 0 !important;
    border: 0 !important;
}


/* Description button spacing */
#desc_row > .col-sm-2,
#desc_row > .col-md-2,
#desc_row > .col-lg-2,
#desc_row > .col-xl-2 {
    margin-top: 20px !important;
}


/* =========================================================
   6. COURSE CONTENT FORM CARD
   ========================================================= */

.course-content-card {
    margin-top: 0 !important;
    margin-bottom: 16px !important;

    padding: 0 !important;

    background: #ffffff !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;
}


.course-content-card > .card-body {
    padding: 20px !important;
}


/* Prevent bootstrap columns from reducing Figma width */
.course-content-left {
    flex: 0 0 100% !important;
    max-width: 100% !important;
}


.course-content-right {
    flex: 0 0 100% !important;
    max-width: 100% !important;

    margin-top: 20px !important;

    padding-top: 0 !important;
}


/* =========================================================
   7. COURSE CONTENT LABELS
   ========================================================= */

.course-content-left label,
.course-content-left label strong {
    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 500 !important;
    line-height: 1.25 !important;

    color: #000000 !important;
}


.course-content-left label {
    margin-bottom: 11px !important;
}


/* Form vertical spacing */
.course-content-left .form-group {
    margin-bottom: 20px !important;
}


/* =========================================================
   8. COURSE CONTENT TEXTAREA
   Figma = 100px
   ========================================================= */

.course-content-left #lec_content {
    width: 100% !important;

    height: 100px !important;
    min-height: 100px !important;

    padding: 14px !important;

    background: #ffffff !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;
    line-height: 1.4 !important;

    color: #252525 !important;

    resize: vertical;
}


.course-content-left #lec_content::placeholder {
    color: #AAAAAA !important;
    opacity: 1;

    font-size: 16px !important;
    font-weight: 400 !important;
}


.course-content-left #lec_content:focus {
    border-color: #17675F !important;

    outline: none !important;

    box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.08) !important;
}


/* =========================================================
   9. CONTENT ORDER
   ========================================================= */

.course-content-left #con_order {
    width: 100% !important;

    height: 40px !important;

    padding: 0 12px !important;

    background-color: #ffffff !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}


.course-content-left #con_order:focus {
    border-color: #17675F !important;

    outline: none !important;

    box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.08) !important;
}


/* =========================================================
   10. DOWNLOAD SAMPLE
   ========================================================= */

.course-content-left label .demo_file_link,
.demo_file_link {
    color: #252525 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;

    text-decoration: underline !important;

    cursor: pointer;
}


/* =========================================================
   11. FILE INPUT
   ========================================================= */

#uploadfile,
#fileCon {
    width: 100% !important;

    height: 40px !important;

    padding: 5px !important;

    background: #ffffff !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;

    color: #252525 !important;
}


#uploadfile::file-selector-button,
#fileCon::file-selector-button {
    height: 29px;

    min-width: 110px;

    margin-right: 10px;

    padding: 0 10px;

    background: #F1F1F1;

    border: 1px solid #F0F0F0;
    border-radius: 5px;

    font-family: 'Montserrat', sans-serif;

    font-size: 14px;
    font-weight: 400;

    color: #000000;

    cursor: pointer;
}


/* Chrome / older WebKit */
#uploadfile::-webkit-file-upload-button,
#fileCon::-webkit-file-upload-button {
    height: 29px;

    min-width: 110px;

    margin-right: 10px;

    padding: 0 10px;

    background: #F1F1F1;

    border: 1px solid #F0F0F0;
    border-radius: 5px;

    font-family: 'Montserrat', sans-serif;

    font-size: 14px;
    font-weight: 400;

    color: #000000;

    cursor: pointer;
}


/* =========================================================
   12. FIGMA NOTE
   ========================================================= */

.course-content-left label[style*="#FFBA49"],
.upload-file-field label[style*="#FFBA49"] {
    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;
    line-height: 1.3 !important;

    color: #FFBA49 !important;
}


/* Broader fallback for existing note labels */
.course-content-left .form-group > label:last-of-type {
    line-height: 1.3;
}


.course-content-left label strong,
.upload-file-field label strong {
    font-weight: 600;
}


/* =========================================================
   13. UPLOAD COURSE OUTLINE CARD
   ========================================================= */

#form_file.card {
    margin-top: 0 !important;
    margin-bottom: 0 !important;

    background: #ffffff !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;
}


#form_file > .card-body {
    padding: 20px !important;
}


/* File field full width like Figma */
.upload-file-field {
    flex: 0 0 100% !important;

    width: 100% !important;
    max-width: 100% !important;
}


.upload-file-field > label:first-of-type {
    margin-bottom: 11px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 500 !important;
    line-height: 1.25 !important;

    color: #000000 !important;
}


/* Upload button goes below */
.upload-file-btn-wrap {
    flex: 0 0 100% !important;

    width: 100% !important;
    max-width: 100% !important;

    margin-top: 20px !important;

    padding-top: 0 !important;

    text-align: left !important;
}


.upload-file-btn-wrap .form-group {
    margin: 0 !important;

    text-align: left !important;
}


.upload-file-btn-wrap .btn-submit-file {
    margin: 0 !important;

    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;

    box-shadow: none !important;
}


/* Figma Upload File = 140 x 50 */
.upload-file-btn-wrap .btn-squared-default-plain {
    width: 140px !important;
    min-width: 140px !important;

    height: 50px !important;

    padding: 0 14px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    gap: 8px !important;

    background: #1F776E !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 500 !important;
    line-height: 1 !important;

    color: #ffffff !important;

    text-decoration: none !important;
}


.upload-file-btn-wrap br,
.upload-file-btn-wrap .fa-plus {
    display: none !important;
}


/* =========================================================
   14. SAVED DESCRIPTION
   Keep functionality, visually align with Figma
   ========================================================= */

#simpledescription {
    margin-top: 20px !important;

    padding: 20px !important;

    background: #ffffff !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;
}


#simpledescription label {
    margin-bottom: 11px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 500 !important;

    color: #000000 !important;
}


#simpledescription p {
    margin: 0 0 20px 0 !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;
    line-height: 1.5 !important;

    color: #252525 !important;
}


/* Keep Edit function, align visual language */
#simpledescription .btn-outline-info {
    min-width: 90px !important;

    height: 40px !important;

    padding: 0 14px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    gap: 7px !important;

    background: #ffffff !important;

    border: 1px solid #17675F !important;
    border-radius: 5px !important;

    color: #17675F !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 500 !important;

    box-shadow: none !important;
}


#simpledescription .btn-outline-info:hover {
    background: #17675F !important;
    color: #ffffff !important;
}


/* =========================================================
   15. COURSE CONTENT TABLE
   Do not change table functionality
   ========================================================= */

.course-content-table-wrap {
    margin-top: 0 !important;

    background: #ffffff !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;
}


.course-content-table {
    font-family: 'Montserrat', sans-serif !important;
}


/* =========================================================
   16. REMOVE OLD CARD DECORATIONS
   ========================================================= */

#pills-home .yoo-card,
#pills-profile .yoo-card,
#pills-contact .card {
    box-shadow: none !important;
}


/* =========================================================
   17. RESPONSIVE
   Desktop values above are exact Figma sizing.
   ========================================================= */

@media (max-width: 900px) {

    .tab_list {
        gap: 6px !important;
    }

    #pills-home-tab,
    #pills-profile-tab,
    #pills-contact-tab {
        min-width: 0 !important;
        width: auto !important;

        padding-left: 18px !important;
        padding-right: 18px !important;
    }

}


@media (max-width: 767px) {

    .tab_list {
        display: grid !important;
        grid-template-columns: 1fr !important;

        gap: 8px !important;

        padding-bottom: 12px !important;
    }

    .tab_list li,
    .tab_list .nav-link {
        width: 100% !important;
    }

    .tab_list .nav-link.active::after {
        display: none !important;
    }

    .hemi-intro-selected h5 {
        font-size: 20px !important;
    }

    .hemi-intro-selected .card-title {
        font-size: 16px !important;
    }

    #pills-home > .container-fluid,
    .course-content-card > .card-body,
    #form_file > .card-body {
        padding: 16px !important;
    }

}
</style>


    @stop

    @section('javascript')
    <!-- //////////////////// desc work ////////////////////// -->
    <script>
   
   function ValidateExtension() {
        var allowedFiles = [".csv", ".docx"];
        var fileUpload = document.getElementById("uploadfile");
        var lblError = document.getElementById("lblError");
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:()])+(" + allowedFiles.join('|') + ")$");
        if (!regex.test(fileUpload.value.toLowerCase())) {
            lblError.innerHTML = "Please upload files: <b>" + allowedFiles.join(', ') + "</b> only.";
            $('#uploadfile').val('');
            return false;
        }
        lblError.innerHTML = "";
        return true;
    }




        var process = false;
        $(document).ready(function() {
            //alert('ok');
            shortString('.short');
            $("#description").focus();
        });

        function hideGotIt(){
            $('.got-it').hide();
            $("#lec_content").focus();
            $('#lec_content').css('border-color', '#808080');
            $('#lec_content').css('border-width', '2px');
   // alert('ok');
}


$('#pills-profile-tab').click(function(){
        $('.got-it').show();
});

        function getCourseContent(co_id) {
            // sendPostRequest('.upload-content-btn','.upload-content-msg','teacher-coursecontent',function(res){
            // },fd,null);

            sendGetRequest(null, null, '/get-lecture-content?co_id=' + co_id, function() {

            }, null);
        }
		
		///////remove file course outline 
        function Remove(id){
            $("#spremove").addClass("fa fa-spinner fa-spin p-101 fa-2x");

            $.ajax({
            method: "GET",
            url: "<?php echo url('api/teacher-removeFile/'); ?>/" + id,

        }).done(function(res) {
            // console.log(res);
            if (res.status) {
                alert(res.message);
                //location.reload();
                $("#fileUpload").load(window.location + " #fileUpload");
            }

        }).fail(function(res) {
            console.log(res);
        });
        }
        ////////////file upload /////////////////////////
        $(".btn-submit-file").click(function(e) {
            e.preventDefault();
            $('.file_msg').html('');
            $('#errorfile_msg').html('');
            var error = 0;
            var file        = $("#fileCon").val();
            var typeArr     = ["docx","pdf","doc"];
            
            if(file.trim() != ''){
                var fileInfoArr = file.split(".");
                var len         = fileInfoArr.length;
                var type        = fileInfoArr[len-1];
                var index       = typeArr.indexOf(type);

                if(index < 0){
                    error++;
                    $('#fileCon').addClass('is-invalid');
                    
                    $('#error').html('<span style="color:red;">Only  docx,and pdf are allowed </span>');
                    $("#spin").addClass("fa fa-spinner1 fa-spin p-101 fa-2x");
                }
                var fileInput =  document.getElementById("fileCon");
               // var file_size = 0;
                
                   var  file_size =  $('#fileCon')[0].files[0].size;
                
                file_size = (file_size/1024)/1024;

                if(file_size > 20){
                    error++;
                    $('#errorfile_msg').html('<span style="color:red;">File size must be less then 20MB! </span>');
                    $("#spin").addClass("fa fa-spinner1 fa-spin p-101 fa-2x");
                    return false;
                }
            }
            if( error > 0 ){
                
                return false;
            }
            var bi_form = document.querySelector('#form_file');
            var fd = new FormData(bi_form);
            $("#spin").addClass("fa fa-spinner fa-spin p-101 fa-2x");
            $("#addbtn").removeClass("fa fa-plus fa-2x");
            //$('.btn-submit-file').prop('disabled', true);
            sendPostRequest('.upload-file-btn', '.file_msg', 'teacher-uploadFile',
                function(res) {
                  
                    $('.upload-file-msg').html(' ');
                    if (res.status) {
                        $('#spin').removeClass('fa fa-spinner fa-spin p-101 fa-2x');
                        document.getElementById("form_file").reset();
                        $("#fileCon").val('');
                        //$('.file_msg').html('<p style="color:green;">File upload Successfully'+res.message+'.</p>');
                        $("#fileUpload").load(window.location + " #fileUpload");
                        //location.reload();
                        setTimeout(function() {
                            $("#saved_msg1").html('');
							$(".file_msg").html(' ');
                            $("#record").html('');
                        }, 3000);
                    } else {
                        //$('#file_msg').html('<p style="color:red;">'+res.message+'</p>');
                        $("#spin").removeClass("fa fa-spinner fa-spin p-101 fa-2x");
                        $('#spin').removeClass('is-invalid');
                       
                    }
                },fd, function(jqXHR, textStatus, errorThrown) {
                    defaultFailBlock('.file_msg', jqXHR, textStatus, errorThrown)
                        $("#spin").removeClass("fa fa-spinner fa-spin p-101 fa-2x");
						 setTimeout(function() {
                            $(".file_msg").html(' ');
                        }, 3000);
						document.getElementById("form_file").reset();

                    });
        });



        function shortString(selector) {
            const elements = document.querySelectorAll(selector);
            const tail = '...';
            if (elements && elements.length) {
                for (const element of elements) {
                    let text = element.innerText;
                    if (element.hasAttribute('data-limit')) {
                        if (text.length > element.dataset.limit) {
                            element.innerText = `${text.substring(0, element.dataset.limit - tail.length).trim()}${tail}`;
                        }
                    } else {
                        throw Error('Cannot find attribute \'data-limit\'');
                    }
                }
            }
        }

        window.onload = function() {
            shortString('.short');
        };

        function get_des11(id) {
            //alert(id);
            $('#desc_row').show();
            var lecture_desc = $("#desc").val();
            $('#description').val(lecture_desc);
            $('#id').val(id);
            $('#description').focus();
            $('#description').css('border-color', 'red');
            $('#description').css('border-width', '2px');
        }
        function get_des(id) {

    $('#description_form_container').show();
    $('#desc_row').show();

    var lecture_desc = $("#desc").val();

    $('#description').val(lecture_desc);
    $('#id').val(id);

    $('#simpledescription').hide();

    $('#description').focus();
    $('#description').css('border-color', '#17675F');
}
        document.getElementById('description').addEventListener('keypress', function(event) {
            if (event.keyCode == 13) {
                Add_desc();
                event.preventDefault();
            }
        });

        document.getElementById('lec_content').addEventListener('keypress', function(event) {
            var input = document.getElementById("lec_content");
            var x = document.getElementById("lec_content");
            var string = input.value;
            var abc = string.charAt(0).toUpperCase() +
                string.slice(1);

            if (event.keyCode == 13) {
                // Add_desc();

                $('.btn-submit').click();
                event.preventDefault();
            }
        });

        function Add_desc() {

             var bi_form = document.querySelector('#cformdes');
            var fd = new FormData(bi_form);
            $('#error_msg1').html('');
            $.ajax({
                method: "POST",
                url: '{{'/api/teacher-coursecontent/desc'}}',
                data: fd,
                processData: false,
                contentType: false,
                headers: {
                    Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                    Accept: 'application/json'
                }
            }).done(function(res) {
                if (res.status) {
                    //alert(res.message);

                    $('#saved_msg_des').html(res.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);

                } else {
                    $('#description').focus();

                    $('#description').css('border-color', 'red');
                    $('#error_msg1').html(res.message);
                }
            }).fail(function(res) {
                //alert(res.message);
                $('#error_msg').html(res);

                console.log(res);
                //location.reload();
            });
        }

        ///////////////////// Content work //////////////////////
        $.ajaxSetup({
            headers: {
                Authorization: 'Bearer <?= auth()->user()->api_token ?>',
                Accept: 'application/json'
            }
        });


        $(".btn-submit").click(function(e) {
            e.preventDefault();

            if (process) {
                console.log('Process');
                return false;
            }
        

            $("#saved_msg1").html('');
            $(".edit-btn").html('Add');
            $("#addbtn").addClass("fa fa-plus fa-2x");
            $("#edbtn").removeClass("fa fa-edit fa-2x");
            var file        = $("#uploadfile").val();
            
            if(file.trim() != ''){
              var data=ValidateExtension();
              if(!data){
              return false
             
            }
            }

            var bi_form = document.querySelector('#cform');
            var fd = new FormData(bi_form);

            process = true;
            sendPostRequest('.upload-content-btn', '.upload-content-msg', 'teacher-coursecontent',
                function(res) {
                    process = false;

                    $('.upload-content-msg').html(' ');
                    if (res.status) {

                        $("#saved_msg1").html(res.message);
                        if (res.data) {
                            var html = '<div id="msg" style="width:100%" class="alert alert-danger" role="alert">';
                            // html += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                            html += '<i class="fa fa-frown-o mr-2" aria-hidden="true"></i>';
                            html += 'Record Already exist <br>' + res.data;
                            html += '</div>';
                        }

                        $("#record").html(html);
                        $("#uploadfile").val('');
                        $("#lec_content").val('');
                        $("#Lectno").val('');
                        $("#con_id").val('');
                        $('#lec_content').css('border-color', 'gray');
                        $("#simpletable").load(window.location + " #simpletable");
                        $("#contentOrder").load(window.location + " #contentOrder");
                        // location.reload();
                        setTimeout(function() {
                            $("#saved_msg1").html('');
                            $("#record").html('');
                            // $("cform")[0].reset();
                            // $("#cform").load(window.location + " #cform");

                        }, 4000);
                    } else {
                        //      alert(res.message);
                 
                        if (res.message == 'The selected extension is invalid.') {
                            $('.upload-content-msg').html(res.message + '& only csv format allowed');
                            $('#uploadfile').css('border-color', 'red');
                        } else {
                            $('.upload-content-msg').html(res.message);
                            $('#lec_content').css('border-color', 'red');
                        }

                    }


                }, fd,
                function(jqXHR, textStatus, errorThrown) {

                    process = false;
                    $("#uploadfile").val('');
                    defaultFailBlock('.upload-content-msg', jqXHR, textStatus, errorThrown);
                    // process = false;
                    // var code = jqXHR.status;


                });

            return false;

        });

        function edit_con(id, lec, desc, orderId) {

            // alert(desc);
            // alert(orderId);
            $("#contentOrder").show();
            $("#order_id").val(orderId);
            $("#con_order").val(orderId);
            $('.upload-content-msg').html('');
            $("#addbtn").removeClass("fa fa-plus fa-2x");
            $("#edbtn").addClass("fa fa-edit fa-2x");
            $(".edit-btn").html('Edit');
            $("#con").show();
            $("#Lectno").val(lec);
            $("#con_id").val(id);
            // $("#id").val(id);
            $("#lec_content").val(desc);
            $("#lec_content").focus();
            $('#lec_content').css('border-color', 'red');


        }

        function delete_con(id, co_id) {
            $.ajax({
                method: "GET",
                url: "<?php echo url('teacher/delete_faculity_course_content/'); ?>/" + id + '/' + co_id,

            }).done(function(res) {
                // console.log(res);
                $("#simpletable").load(window.location + " #simpletable");
                $("#simpleweightage").load(window.location + " #simpleweightage");
                $("#contentOrder").load(window.location + " #contentOrder");

                //location.reload();
                if (res.status) {
                    $("#saved_msg1").html(res.message)
                    //alert(res.message);
                    $("#contentOrder").load(window.location + " #contentOrder");

                    //location.reload();
                    setTimeout(function() {
                        $("#saved_msg1").html('');

                    }, 2000);
                }

            }).fail(function(res) {
                console.log(res);
            });
        }


        ////////////////////////// Criteria add///////////////////////////////////////////////
        $(".btn-submit-cri").click(function(e) {
            e.preventDefault();

            var bi_form = document.querySelector('#cform_cri');
            var fd = new FormData(bi_form);
            $.ajax({
                type: "post",
                url: '<?= url('api/teacher-coursecontent'); ?>',
                data: fd,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.status) {
                        alert(data.message);
                    }

                    $("#saved_msg2").show()
                    $("#simpleweightage").load(window.location + " #simpleweightage");
                    setTimeout(function() {
                        $("#saved_msg2").hide()
                        $("#weightage").val('')
                        // location.reload();
                        $("cform")[0].reset();
                    }, 1000);
                },
                complete: function() {


                }
            });
        });
    </script>
    @stop
