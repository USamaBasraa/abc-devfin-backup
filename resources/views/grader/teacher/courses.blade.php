<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
?>
@extends('layouts.app',['pageTitle' => 'Courses'])
@section('content')
{{-- @dd("123"); --}}
<style type="text/css">
.model_header{
    background-color: #484F77 ;
    color: white;"
}






/* =========================================================
   COURSES SCREEN — FIGMA STYLE OVERRIDE
   Figma reference:
   Main Dashboard -> Today Lectures / Course Action Cards

   STRICT PRESERVATION
   ---------------------------------------------------------
   - HTML / Blade unchanged
   - PHP conditions unchanged
   - AJAX unchanged
   - JS unchanged
   - Dynamic icons unchanged
   - View More logic unchanged
   - Dropdown logic unchanged
   - Result Finalization logic unchanged
   - Grade Posting logic unchanged
   - Modal logic unchanged

   IMPORTANT:
   No display property
   No visibility property
   No opacity: 0
   No content / pseudo element
   No element hidden or force-shown
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');


/* =========================================================
   1. DESIGN TOKENS
   ========================================================= */

.app-content.main-content {
    --courses-green: #17675F;
    --courses-green-dark: #125D56;
    --courses-green-soft: rgba(23, 103, 95, 0.10);

    --courses-text: #252525;
    --courses-muted: #7A7A7A;

    --courses-white: #FFFFFF;
    --courses-border: #E6E6E6;

    --courses-warning: #FFBA49;

    font-family: 'Montserrat', sans-serif !important;
}


/* Keep font scoped to this screen */
.app-content.main-content .side-app,
.app-content.main-content .side-app * {
    font-family: 'Montserrat', sans-serif;
}


/* =========================================================
   2. SCREEN BACKGROUND
   Figma background system
   ========================================================= */

.app-content.main-content {
 
}


/* =========================================================
   3. MAIN COURSES WRAPPER
   Existing .side-app remains exactly the same element.
   ========================================================= */

.app-content.main-content .side-app {
    width: 100% !important;

    padding: 20px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 10px !important;

    box-shadow:
        0 0 10px rgba(0, 0, 0, 0.05) !important;
}


/* =========================================================
   4. EACH EXISTING COURSE ROW
   ========================================================= */

.app-content.main-content
.side-app > .row {

    margin-left: 0 !important;
    margin-right: 0 !important;
    margin-bottom: 16px !important;
}


.app-content.main-content
.side-app > .row:last-child {

    margin-bottom: 0 !important;
}


.app-content.main-content
.side-app > .row > .col-md-12 {

    padding-left: 0 !important;
    padding-right: 0 !important;
}


/* =========================================================
   5. COURSE CARD
   Figma course-action block:
   soft green
   radius 5px
   ========================================================= */

.app-content.main-content
.side-app > .row
> .col-md-12
> .card {

    width: 100% !important;

    margin: 0 !important;

    background: rgba(23, 103, 95, 0.10) !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    /*
       Important for existing dropdown / View More menu.
       Nothing gets clipped.
    */
    overflow: visible !important;
}


/* =========================================================
   6. COURSE CARD HEADER
   ========================================================= */

.app-content.main-content
.side-app > .row
> .col-md-12
> .card
> .card-header {

    min-height: auto !important;

    margin: 0 !important;

    padding:
        18px
        20px
        8px !important;

    background: transparent !important;

    border: 0 !important;

    border-radius:
        5px
        5px
        0
        0 !important;

    box-shadow: none !important;
}


/* =========================================================
   7. COURSE TITLE
   Figma = 16px
   Course code slightly stronger
   ========================================================= */

.app-content.main-content
.side-app
.card-header
.card-title {

    margin: 0 !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 500 !important;
    line-height: 1.4 !important;

    color: #252525 !important;

    letter-spacing: 0 !important;
}


/* =========================================================
   8. EXISTING GRADING TYPE
   Example:
   Absolute Grading

   Existing <sub> remains exactly present.
   Only visual treatment changes.
   ========================================================= */

.app-content.main-content
.side-app
.card-title
sub {

    margin-left: 10px !important;

    padding: 3px 8px !important;

    vertical-align: middle !important;

    background: #FFBA49 !important;

    border: 0 !important;
    border-radius: 50px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 9px !important;
    font-weight: 500 !important;
    line-height: 1.2 !important;

    color: #FFFFFF !important;
}


/* =========================================================
   9. EXISTING INNER HEADER WRAPPER
   ========================================================= */

.app-content.main-content
.side-app
.card
.header.py-4 {

    width: 100% !important;

    margin: 0 !important;

    padding:
        8px
        20px
        18px !important;

    background: transparent !important;

    border: 0 !important;
}


/* =========================================================
   10. EXISTING BOOTSTRAP CONTAINER
   ========================================================= */

.app-content.main-content
.side-app
.card
.header
.container {

    width: 100% !important;
    max-width: none !important;

    margin: 0 !important;
    padding: 0 !important;
}


/* =========================================================
   11. DYNAMIC ACTION AREA
   .teacher-courses

   CRITICAL:
   Display is NOT touched.
   JS-created children remain exactly as generated.
   ========================================================= */

.app-content.main-content
.teacher-courses {

    width: 100% !important;

    margin: 0 !important;

    padding-left: 0 !important;
    padding-right: 0 !important;

    align-items: flex-start !important;

    flex-wrap: wrap !important;

    gap: 14px 24px !important;

    background: transparent !important;

    border: 0 !important;
}


/* =========================================================
   12. EXISTING ACTION ITEM
   .dsb-icon-div

   No visibility/display rules.
   ========================================================= */

.app-content.main-content
.teacher-courses
.dsb-icon-div {

    flex: 0 1 125px !important;

    min-width: 100px !important;
    max-width: 145px !important;

    margin: 0 !important;
    padding: 5px 6px !important;

    background: transparent !important;

    border: 0 !important;

    text-align: center !important;

    box-shadow: none !important;
}


/* =========================================================
   13. EXISTING IMAGE ACTION ICONS
   Figma approx 22–24px
   ========================================================= */

.app-content.main-content
.teacher-courses
.dsb-icon-div
img {

    width: 24px !important;
    height: 24px !important;

    margin:
        0
        auto
        7px !important;

    object-fit: contain !important;

    border: 0 !important;

    box-shadow: none !important;
}


/*
   Some generated states use FontAwesome instead of images,
   e.g. Result Finalization / Grade Posting.

   Preserve their existing semantic color.
*/
.app-content.main-content
.teacher-courses
.dsb-icon-div
> i.fa,

.app-content.main-content
.teacher-courses
.dsb-icon-div
.dropdown
> i.fa {

    font-size: 22px !important;

    margin-bottom: 7px !important;
}


/* =========================================================
   14. ACTION LABELS
   Figma = 14px Regular
   ========================================================= */

.app-content.main-content
.teacher-courses
.txtBlue,

.app-content.main-content
.teacher-courses
.txtBlue-margin {

    margin-top: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.35 !important;

    color: #252525 !important;

    text-align: center !important;

    text-decoration: none !important;
}


/* =========================================================
   15. EXISTING CLICKABLE ICONS
   ========================================================= */

.app-content.main-content
.teacher-courses
.dsb-icon-div
img[onclick],

.app-content.main-content
.teacher-courses
.dsb-icon-div
i[onclick] {

    cursor: pointer !important;
}


/* =========================================================
   16. EXISTING DROPDOWN WRAPPER
   Structure/functionality unchanged
   ========================================================= */

.app-content.main-content
.teacher-courses
.dropdown {

    position: relative !important;

    margin: 0 !important;
}


/* =========================================================
   17. EXISTING ACTION DROPDOWN CONTENT
   IMPORTANT:
   We do NOT change display / visibility.
   Existing hover/JS CSS stays responsible for that.
   ========================================================= */

.app-content.main-content
.teacher-courses
.dropdown-content {

    min-width: 150px !important;

    padding: 5px !important;

    background: #FFFFFF !important;

    border: 1px solid #E6E6E6 !important;
    border-radius: 5px !important;

    box-shadow:
        0 8px 20px
        rgba(0, 0, 0, 0.08) !important;

    z-index: 1050 !important;
}


.app-content.main-content
.teacher-courses
.dropdown-content
a {

    margin: 0 !important;
    padding: 8px 10px !important;

    background: #FFFFFF !important;

    border-radius: 4px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 400 !important;
    line-height: 1.35 !important;

    color: #252525 !important;

    text-decoration: none !important;
}


.app-content.main-content
.teacher-courses
.dropdown-content
a:hover {

    background: rgba(23, 103, 95, 0.10) !important;

    color: #17675F !important;
}


/* =========================================================
   18. EXISTING "VIEW MORE" CONTROL
   Dynamically generated by current JS.

   NO display rule.
   NO visibility rule.
   ========================================================= */

.app-content.main-content
.teacher-courses
div[id^="view-more-"] {

    min-width: 80px !important;

    margin-left: 0 !important;
    margin-top: 0 !important;

    padding: 4px 6px !important;

    text-align: center !important;
}


/* Existing ellipsis button */
.app-content.main-content
.teacher-courses
div[id^="view-more-"]
.btn-link {

    width: 36px !important;
    height: 36px !important;

    margin: 0 !important;
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 50% !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    color: #17675F !important;

    text-decoration: none !important;
}


.app-content.main-content
.teacher-courses
div[id^="view-more-"]
.btn-link:hover {

    background: rgba(23, 103, 95, 0.08) !important;

    color: #17675F !important;
}


/* Existing dots */
.app-content.main-content
.teacher-courses
div[id^="view-more-"]
.btn-link
span {

    font-family: 'Montserrat', sans-serif !important;

    font-size: 15px !important;

    color: #17675F !important;
}


/* =========================================================
   19. VIEW MORE DROPDOWN MENU
   Existing Bootstrap dropdown behavior retained.
   ========================================================= */

.app-content.main-content
.teacher-courses
.dropdown-menu {

    min-width: 160px !important;

    padding: 6px !important;

    background: #FFFFFF !important;

    border: 1px solid #E6E6E6 !important;
    border-radius: 5px !important;

    box-shadow:
        0 8px 20px
        rgba(0, 0, 0, 0.08) !important;

    z-index: 1050 !important;
}


/* Dynamically injected list entries */
.app-content.main-content
.teacher-courses
.dropdown-menu
li {

    margin-left: 0 !important;
    margin-right: 0 !important;
}


/* =========================================================
   20. VIEW-MORE ITEMS
   ========================================================= */

.app-content.main-content
.teacher-courses
.dropdown-menu
.dsb-icon-div {

    width: 100% !important;
    max-width: 100% !important;

    margin: 0 !important;

    padding: 9px 8px !important;

    text-align: left !important;

    border-radius: 4px !important;
}


.app-content.main-content
.teacher-courses
.dropdown-menu
.dsb-icon-div:hover {

    background: rgba(23, 103, 95, 0.08) !important;
}


/* Icons inside dropdown */
.app-content.main-content
.teacher-courses
.dropdown-menu
.dsb-icon-div
img {

    width: 20px !important;
    height: 20px !important;

    margin-right: 7px !important;
    margin-bottom: 4px !important;
}


/* Dropdown label */
.app-content.main-content
.teacher-courses
.dropdown-menu
.txtBlue {

    font-size: 12px !important;

    color: #252525 !important;
}


/* =========================================================
   21. LOADING STATE
   Existing loading structure completely preserved.
   ========================================================= */

.app-content.main-content
.teacher-courses
div[id^="loading-"] {

    margin: 6px 0 !important;
}


.app-content.main-content
.teacher-courses
div[id^="loading-"]
img {

    max-width: 120px !important;
    height: auto !important;
}


/* =========================================================
   22. LINKS INSIDE COURSE ACTIONS
   ========================================================= */

.app-content.main-content
.teacher-courses
a {

    font-family: 'Montserrat', sans-serif !important;

    text-decoration: none !important;
}


/* =========================================================
   23. CARD INTERACTION
   Very subtle Figma treatment
   ========================================================= */

.app-content.main-content
.side-app > .row
> .col-md-12
> .card:hover {

    box-shadow:
        0 4px 14px
        rgba(23, 103, 95, 0.04) !important;
}


/* =========================================================
   24. TABLET
   No content hidden or removed.
   ========================================================= */

@media (max-width: 991.98px) {

    .app-content.main-content
    .side-app {

        padding: 16px !important;
    }


    .app-content.main-content
    .teacher-courses {

        gap: 14px 16px !important;
    }


    .app-content.main-content
    .teacher-courses
    .dsb-icon-div {

        flex-basis: 110px !important;

        min-width: 95px !important;
    }

}


/* =========================================================
   25. MOBILE
   Existing items remain present and accessible.
   ========================================================= */

@media (max-width: 767.98px) {

    .app-content.main-content
    .side-app {

        padding: 12px !important;

        border-radius: 8px !important;
    }


    .app-content.main-content
    .side-app
    .card-header {

        padding:
            15px
            15px
            8px !important;
    }


    .app-content.main-content
    .side-app
    .card
    .header.py-4 {

        padding:
            8px
            15px
            15px !important;
    }


    .app-content.main-content
    .side-app
    .card-title {

        font-size: 15px !important;
    }


    .app-content.main-content
    .teacher-courses {

        gap: 14px 8px !important;
    }


    .app-content.main-content
    .teacher-courses
    .dsb-icon-div {

        flex:
            0
            1
            calc(33.333% - 8px) !important;

        min-width: 90px !important;
        max-width: none !important;

        padding-left: 3px !important;
        padding-right: 3px !important;
    }


    .app-content.main-content
    .teacher-courses
    .txtBlue,

    .app-content.main-content
    .teacher-courses
    .txtBlue-margin {

        font-size: 12px !important;
    }

}
</style>
<pre>
<?php
//print_r($courseList);
?>
</pre>
<div class="page">
    <div class="page-main">
        <div class="app-content main-content">
            <div class="side-app">
                @if(isset($courseList))
                    @foreach($courseList as $course)
                    <?php
                        $fc_co_id = $course->fc_co_id;
                        $fc_co_id=my_simple_crypt($fc_co_id, 'e' );
                    ?>
                    <!-- Row-->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        {{$course->course_code}} - {{$course->course_cd_name}}  {{$course->co_course_cr}} ({{$course->co_section}})<sub style="color: blue; font-size: 10px;margin-left: 10px;">{{$course->course_grading_type}} Grading</sub>
                                    </h3>
                                </div>
                                <div class="">
                                    <div class="header py-4">
                                        <div class="container">
                                            <div class="form-group row teacher-courses" id="{{$course->fc_co_id}}" style="padding-left:40px !important">
                                                <div class="" id="loading-{{$course->fc_co_id}}">
                                                     <img src="{{asset('images/2.gif')}}" width="120px;">
                                                 </div>
                                             </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Row-->
                    @endforeach
                 @endif
            </div>
        </div><!-- end app-content-->
    </div>
</div>





    <!-- todo 0-->
    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="ResultFinalConfirmModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header model_header">
                    <h4 class="modal-title"> Result Finalization </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" style="height: 420px;">

                    <!-- todo method start -->
                    <div class="grader-intro grader-views">
                        <div id="finalization-header-confirm"><h3><span id="finalization-course-confirm"></span></h3><span id="finalization-details-confirm"></span></div>
                        <div style="margin-top: 50px;margin-bottom: 50px;">
                            <div style="float: left; border: 2px solid #A0A0A0;width:38%;height: 300px;;padding:100px 5px 5px 5px;">
                                <h1 style="text-shadow: 1px 1px 2px #000000;">Note</h1>
                            </div>
                            <div style="float: right; border: 2px solid #A0A0A0;width:60%;height: 300px;padding:50px 5px 5px 5px;">
                                <br>
                                <br>
                                <h2>Please make sure that you have completed all the academic criterias to evaluate this course.</h2>
                                <br>
                                <h3 style="color: red;"><i>Are you sure to proceed?</i></h3>
                            </div>
                        </div>
                    </div>
                    <!-- todo method end -->

                    <!-- todo method start -->
                    <div class="grader-cretria-method grader-views" style="display: none;">
                        <form id="criteria_method_form">
                            <table id="SubjectAssesmentTypeTable" width="100%" border="1" cellpadding="4" cellspacning="0" style="margin-top: 10px;">
                                <thead class="bnu-header">
                                <tr>
                                    <th class="tab-title-center">Assessment Types </th>
                                    <th class="tab-title-center" style="padding:10px 0px;" >Weightage </th>
                                    <!--  <th class="tab-title-center">Missing Marks 1 </th> -->

                                    <th class="tab-title-center" style="padding:10px 0px;" >Assessments</th>
                                    <th class="tab-title-center">Grading Method </th>
                                    <th class="tab-title-center center" style="padding:10px 0px;" >Selections</th>
                                </tr>
                                </thead>
                            </table>
                        </form>


                    </div>
                    <!-- todo method end -->
                    <style>.error-field{color:red;}
                        .center{display: block;
                            margin-left: auto;
                            margin-right: auto;}
                    </style>

                    <!-- todo best of start -->
                    <div class="grader-best-of-option grader-views" style="display: none;">
                        <div style="margin-top: 10px;margin-bottom: 10px;">
                            <h3 id="best-of-title" ></h3>
                            <div id="pick_drop_div" style="min-height: 350px; overflow-y: auto; max-width: 762px; overflow-x: hidden">
                                <form id="assessment_best_of_form">
                                    <input type="hidden" value="" id="abf-co_id" name="co_id">
                                    <input type="hidden" value="" id="abf-assessment_id" name="assessment_id">
                                    <input type="hidden" value="" id="abf-total-assesment" >
                                    <input type="hidden" value="" id="abf-method_id" name="method_id" >
                                    <table id="BestCretriaTable1" class="table table-bordered display responsive nowrap ed-table" style="margin-top: 10px;">
                                        <thead class="bnu-header">
                                        <tr>
                                            <th class="tab-title-center">Assessment Types</th>
                                            <th class="tab-title-center t-center">No of Assessments</th>
                                            <th class="tab-title-center">Enter Best Number Of Assessment</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td id="as-assessment-name">Assessment</td>
                                            <td id="asses_total_taken" class="t-center">Loading...</td>
                                            <td class="t-center"><input type="number" id="best_of_val" name="best_of" placeholder="Enter No"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- todo best of end -->



                    <!-- todo pick select start  -->
                    <div class="grader-pick-select-option grader-views" style="display: none;" >
                        <h5>Course Criteria Details</h5>
                        <div id="pick_drop_div" style="max-height: 350px; overflow-y: scroll; max-width: 762px; overflow-x: hidden">
                            <form method="post" id="pick-select-form">
                                <table id="CourseCriteriaDetails" name="CourseCriteriaDetails"  class="table table-bordered display responsive nowrap ed-table" style="margin-top: 10px;">
                                    <thead class="bnu-header">
                                    <tr>
                                        <th  style="width: 100px;" id="final_assessment" class="tab-title-center">**Assessment Types</th>
                                        <th style="width: 100px;" class="tab-title-center"><span id="final_weightage">Weightage</span></th>
                                        <!-- <th  style="width: 100px;"class="tab-title-center">Missing Marks</th> -->
                                        <th  style="width: 100px;"class="tab-title-center">Grading Method</th>
                                    </tr>
                                    </thead>
                                    <tbody id="CourseCriteriaDetailsBody"></tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                    <div class="grader-pick-select-option grader-views" style="display: none;"  >
                        <span>Total : <b id="pick-select-total">0</b> </span>
                        <br>
                        <span>Require : <b id="pick-require-total">0</b> </span>
                        <br>
                        <span style="display: none; color: red" id="pick-select-error-msg">...</span>
                    </div>
                    <!-- todo pick select end -->



                    <!-- todo confirmation methods window start  -->
                    <!-- todo zain course detail -->
                    <div class="grader-criteria-selected grader-views" style="display: none;" >
                        <h3>Course Criteria Selected Detail</h3>
                        <div id="pick_drop_div" style="max-height: 350px; overflow-y: scroll; max-width: 762px; overflow-x: hidden">

                            <table id="CourseCriteriaselected" name="CourseCriteriaselected"  width="100%" border="1" cellpadding="4" cellspacning="0" style="margin-top: 10px;">
                                <thead class="bnu-header">
                                <tr>
                                    <th   id="final_assessment" class="tab-title-center" style=" width: 20px " >Assessment</th>
                                    <th   class="tab-title-center" >Grading Type</th>
                                    <th   class="tab-title-center t-center"  width="20px"  >Total</th>
                                    <th   class="tab-title-center t-center" style=" width: 20px ">Selected</th>
                                    <th   class="tab-title-center t-center">MissingMarks</th>
                                    <th   class="tab-title-center t-center">Weightage</th>
                                </tr>
                                </thead>
                                <tbody id="CourseCriteriaselected"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- todo zain course detail of end -->
                    <!-- todo confirmation methods window end -->




                    <!--  todo show raw marks start -->

                    <div id="showRawMarks" class="grader-views"></div>
                    <div id="showSummaryMarks"></div>

                    <!-- todo show raw marks start -->














                </div><!-- model body end -->
                <div class="modal-footer">



                    <p style="font-weight: bold; float: left; display: none;" class="grader-cretria-method grader-views">Please make sure criteria you have selected is satisfactory to you  </p>



                    <button type="button" class="btn btn-default grader-intro grader-views" id="rfcm-btn-no">Cancel</button>
                    <button type="button" class="btn btn-primary grader-intro grader-views" id="rfcm-btn-yes">Proceed</button>


                    <button type="button" class="btn btn-default grader-cretria-method grader-views" id="criteria-btn-no">Back</button>

                    <!-- <button type="button" class="btn btn-primary grader-cretria-method grader-views" onclick="showRawMarksList()" id="criteria-btn-yes-">Proceed</button> -->

                    <button type="button" class="btn btn-primary grader-cretria-method grader-views" id="goto-criteria-confirmation-btn">Proceed</button>



                    <!-- grader-criteria-selected start -->
                    <button type="button" class="btn btn-primary grader-criteria-selected grader-views" id="grader-criteria-selected-back-btn"  >Back</button>
                    <button type="button" class="btn btn-primary grader-criteria-selected grader-views" onclick="showRawMarksList();" >Confirm</button>
                    <!-- grader-criteria-selected end -->





                    {{--<button id="rf_close_next" class="btn btn-primary" style="display: none;margin: 5px 5px 8px 0px;width: 100px;" onclick="showRawMarksList()">Next btn</button>--}}
                    {{--<!-- todo best of start -->--}}
                    <button type="button" class="btn btn-default grader-best-of-option grader-views" id="grader-best-of-cancel-btn">Cancel</button>
                    <button type="button" class="btn btn-primary grader-best-of-option grader-views" id="grader-best-of-save-btn">Proceed</button>
                    <!-- todo best of end -->



                    <!-- todo pick select start  -->
                    <button type="button" class="btn btn-default grader-pick-select-option grader-views" id="grader-pick-select-cancel-btn">Cancel</button>
                    <button type="button" class="btn btn-primary grader-pick-select-option grader-views" id="grader-pick-select-save-btn">Proceed</button>
                    <!-- todo pick select end  -->






                    <div class="float-left raw_mark_views grader-views">
                        <button id="raw_marks_back_btn" class="btn btn-primary raw_mark_views" style="display: none;margin: 5px 5px 8px 10px;width: 100px;float:left"  >Back</button>
                    </div>
                    <div class="float-right raw_mark_views grader-views">
                        <form id="rf_process_form">
                            @csrf
                            <input type="hidden" id="rf_co_id" name="rf_co_id">
                            <input type="submit" value="Process" id="rf_process_button" class="btn btn-primary" style="margin: 5px 5px 8px 0px;width: 100px;display:none;">
                        </form>
                        {{--<button id="rf_close_next" class="btn btn-primary" style="display: none;margin: 5px 5px 8px 0px;width: 100px;" onclick="showRawMarksList()">Next btn</button>--}}
                        <button id="rf_delete_button" class="btn btn-primary" style="display: none;margin: 5px 5px 8px 0px;width: 110px;" onclick="deleteMarkSummaries()">Cancel</button>
                        <button id="rf_close_button" class="btn btn-primary" style="display: none;margin: 5px 5px 8px 0px;width: 110px;" data-dismiss="modal">Finish</button>
                    </div>






                </div>
            </div>
        </div>
    </div>
    <!-- todo -->
    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="CretriaMethodPopUp_old">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Result Finalization</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" style="height: 420px;">
                    <div id="finalization-header-confirm"><h3><span id="finalization-course-confirm"></span></h3><span id="finalization-details-confirm"></span></div>


                    {{--<h3 class="cc_subject_name" >Subject Name</h3>--}}
                    {{--<h5>Course Criteria Details</h5>--}}
                    <div style="margin-top: 50px;margin-bottom: 50px;">

                        <!-- todo  -->

                    </div>
                    <div id="showRawMarks"></div>
                    <div id="showSummaryMarks"></div>
                </div>
                <div class="modal-footer">
                    {{--<button type="button" class="btn btn-default" id="cmpu-btn-no">Cancel</button>--}}
                    {{--<button type="button" class="btn btn-primary" id="cmpu-btn-yes">Proceed</button>--}}

                    <div class="float-left">
                        <button id="rf_close_back" class="btn btn-primary" style="display: none;margin: 5px 5px 8px 10px;width: 100px;float:left" onclick="showRawMarksList()">Back</button>

                    </div>
                    <div class="float-right">
                        <form id="rf_process_form">
                            @csrf
                            <input type="hidden" id="rf_co_id" name="rf_co_id">
                            <input type="submit" value="Process" id="rf_process_button" class="btn btn-primary" style="margin: 5px 5px 8px 0px;width: 100px;display:none;">
                        </form>
                        <button id="rf_close_next" class="btn btn-primary" style="display: none;margin: 5px 5px 8px 0px;width: 100px;" onclick="showRawMarksList()">Next</button>
                        <button id="rf_delete_button" class="btn btn-primary" style="display: none;margin: 5px 5px 8px 0px;width: 110px;" onclick="deleteMarkSummaries()">Cancel</button>
                        <button id="rf_close_button" class="btn btn-primary" style="display: none;margin: 5px 5px 8px 0px;width: 110px;" data-dismiss="modal">Proceed</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- todo -->
    <div class="modal fade" tabindex="-2" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="BestCretriaDetailPopup">
        <div class="modal-dialog modal-md"  style="width:100%; margin-left: auto; margin-right: auto; margin-top: 10%;">
            <div class="modal-content">
                <div class="modal-header"><?php print_r($sess); ?>
                    <h4 class="modal-title">Result Finalization </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" style="height: 240px; overflow: scroll; overflow-x: hidden">
                    {{--                 <div id="finalization-header-confirm"><h3><span id="finalization-course-confirm"></span></h3><span id="finalization-details-confirm"></span></div>
                                     --}}



                </div>
                <div class="modal-footer">
                    {{--<button type="button" class="btn btn-default" id="rfcm-btn-no">Cancel</button>--}}
                    <button type="button" class="btn btn-primary" id="abof-btn-yes">Save</button>
                </div>
            </div>
        </div>
    </div>
    <!-- todo zain 1 -->
    <div class="modal fade" tabindex="-2" role="dialog"  id="okit">
        <div id="okit-dialog" class="modal-dialog modal-lg" style="background-color: greenyellow; width:50%; margin-left: auto; margin-right: auto; margin-top:2%;">

            <div class="modal-header">
                <h4 class="modal-title">Result Finalization</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="height: 420px;overflow-y: auto;">
                <div id="finalization-header"><h3><span id="finalization-course"></span></h3><span id="finalization-details"></span></div>
                <div style="max-height: 350px; overflow-y: auto; max-width: 775px; overflow-x: hidden">

                    <table id="CourseCriteriaDetails" name="CourseCriteriaDetails"  class="table table-bordered display responsive nowrap ed-table" style="margin-top: 10px;">
                        <thead class="bnu-header">
                        <tr>
                            <th id="final_assessment" class="tab-title-center">Assessment Types</th>
                            <th class="tab-title-center"><span id="final_weightage">Weightage</span></th>
                            <th class="tab-title-center">Missing Marks 1</th>
                            <th class="tab-title-center">Grading Method</th>
                        </tr>
                        </thead>
                    </table>

                </div>

                <div id="showRawMarks"></div>
                <div id="showSummaryMarks"></div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="postingModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Grade Posting</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
               <!-- <div class="modal-body" style="height: 420px;overflow-y: auto;"> -->
                    <div id="showGradePosting"></div>
            <!-- </div>  -->
            </div>
        </div>
    </div>
@stop
@section('javascript')
    <script >

        var view_more_lenght = 0;
        var my_school   = '<?php echo getLoginUser('school_id'); ?>';
        var base_url    = '{{url('/')}}';


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $(document).ready(function() {
       //   alert('asd');

            $('.datepicker').datepicker({
       format: 'yyyy-mm-dd'
     });


            var width = $(window).width();
            var height = $(window).height();

            if (width >= 1280)
            {
                //alert('Screen 1');
                view_more_lenght = 5;
            }
            else if (width >= 1024)
            {
                view_more_lenght = 4;
                //alert('Screen 2');
            } else if (width >= 800)
            {
                //alert('Screen 3');
                view_more_lenght = 3;
            }else
            {
                view_more_lenght = 1;
                //alert('Screen 4');
            }

            getCourseCriteria();
            $("#courseMarksModal").modal({
                show: false,
                backdrop: 'static'
            });
        });

        $(document).on("wheel", "input[type=number]", function (e) {
            $(this).blur();
        });

        $(document).keyup(function(e) {

            if (e.currentTarget.activeElement != undefined) {
                var id = $(e.currentTarget.activeElement).attr('id');
                if (e.keyCode == 40) {
                    var inc = 1;
                    var final_id = Number(id) + Number(inc);
                    var om =  parseInt($('#'+id).val());
                    var tm =  parseInt($('#total_marks').val());
                    if((om<=tm)||(isNaN(om))){
                        $('#'+id).css({
                            "border": ""
                        });
                        $('#'+final_id).focus();
                    }else{
                        $('#'+id).css({
                            "border": "1px solid red"
                        });
                        return 0;
                    }
                }
            }
        });

        $(document).keyup(function(e) {
            if (e.currentTarget.activeElement != undefined) {
                var id = $(e.currentTarget.activeElement).attr('id');
//                            alert(id);
                if (e.keyCode == 38) {
                    var inc = 1;
                    var final_id = Number(id) - Number(inc);
                    var om =  parseInt($('#'+id).val());
                    var tm =  parseInt($('#total_marks').val());
                    if((om<=tm)||(isNaN(om))){
                        $('#'+id).css({
                            "border": ""
                        });
                        $('#'+final_id).focus();
                    }else{
                        $('#'+id).css({
                            "border": "1px solid red"
                        });
                        return 0;
                    }
                }
            }
        });

        function openModalCourseAttendance(get_course_name,co_id) {
            $("#attendance_contents").html('');
            $('#courseAttendanceModal').modal('show');///attendance_popup.blade.php
            $("#attendance_model").html(get_course_name);
            // $("#show_heading_criteria").show();
            $.ajax({
                url:'{{URL::route('attendance/attendance')}}/'+co_id,
                type: "GET",
                success: function (data) {
                    $("#attendance_contents").html(data);
                }
            });
        }
        function openModalCourseCriteria(get_course_name,co_id) {
           // alert(get_course_name + co_id)
            $('#courseCriteriaModal').modal('show');
            $("#show_heading_criteria").html(get_course_name);
            $("#show_heading_criteria").show();
            $.ajax({
                url:'{{url('teacher/load_criteria')}}/'+co_id,
                type: "GET",
                success: function (data) {
                    $("#load_date").html(data);
                }
            });
        }

        $('#courseCriteriaModal').on('hidden.bs.modal', function () {
            getCourseCriteria();
        });

        $('#finalizationModal').on('hidden.bs.modal', function () {
            getCourseCriteria();
        });

        $('#postingModal').on('hidden.bs.modal', function () {
            getCourseCriteria();
        });

        function openMarksModal(assess_type,num,co_id,assess_name,course,cc_id)
        {
            if(num==0)
            {
                var ass_number = "New";
            }else{
                var ass_number = num;
            }
            $('#marks-name').html(' '+course+' ('+assess_name+' '+ass_number+')');
            $("#courseMarksModal").modal();
            openMarksEntryPopup(co_id,assess_type,num,cc_id);
        }

        function getCourseCriteria()
        {
            var co_id_list = document.getElementsByClassName('teacher-courses');
            $.each(co_id_list, function(i, value){
                var co_id= co_id_list[i].id;
                $('#loading').css({'display':'block'});
                if(co_id)
                {
                    $.ajax({
                        url: '{{url('teacher/courses/checkCriteria')}}/'+co_id,
                        type: "GET",
                        dataType: "HTML",
                        success:function(data) {
                            showIcons(data,co_id);
                        }
                    });
                }else{
                    alert('Session Information Missing!');
                }
            });
        }

        function showIcons(data,co_id)
        {
            if(data!='Zero')
            {
                $("#define-"+co_id).css({'display':'none'});
                var obj = JSON.parse(data);
                var lCount = 1;
                var gp=0;
                //alert(obj.length);
                $.each(obj, function(key, value) {
                    var assessment_id           = obj[key]['assessment_id'];
                    var assessment_name         = obj[key]['assessment_name'];
                    var assessment_short_name   = obj[key]['assessment_short_name'];
                    var assessment_icon         = obj[key]['assessment_icon'];
                    var course_code             = obj[key]['course_code'];
                    var course_name             = obj[key]['course_cd_name'];
                    var co_course_cr            = obj[key]['co_course_cr'];
                    var co_section              = obj[key]['co_section'];
                    var mid_approval_status     = obj[key]['mid_approval_status'];
                    var final_approval_status   = obj[key]['final_approval_status'];
                    var conversion_status       = obj[key]['marks_conversion_status'];
                    var grading_status          = obj[key]['grading_status'];
                    var assessment_type         = "new";
                    var icon_path               = "../images/" + assessment_icon;
                    var icon_path2              = "../images/list.png";


                    if(mid_approval_status==1 && final_approval_status==1)
                    {
                        var approval_status = 1;
                    }else{
                        var approval_status = 0;
                    }

                    //alert(co_id+'--'+approval_status);

// babag
                    if(conversion_status==0)
                    {
                        if ($('#' + assessment_id + '-' + co_id).length == 0)
                        {
                            if(key==view_more_lenght)
                            {
                                $("#"+co_id).append('<div id="view-more-'+co_id+'" title="View More" style="margin-left: 40px;margin-top: 20px;"><button class="btn btn-link" type="button" data-toggle="dropdown"> <span style="font-weight: 900;"> . &ensp; . &ensp; .</span> </button><ul class="dropdown-menu scrollable-menu" style="width: 150px;" id="#dropdown-submenu"></ul></div>');
                            }

                            if(assessment_id==4)
                            {
                                if($("#view-more-"+co_id+":visible").length>0)
                                {
                                    $("#"+co_id+" ul").append('<li style="margin-bottom: 50px;"><div class="text-align-center dsb-icon-div" id="' + assessment_id + '-' + co_id + '"><div class="dropdown"><img title="' + assessment_name + '" src="' + icon_path + '"  onclick="openMarksModal('+assessment_id+',\'' + assessment_type + '\',' + co_id + ',\'' + assessment_name + '\',\'' + course_name + '\')"><br><span class="txtBlue txtBlue-margin">  ' + assessment_name + '</span> </div></div></li>');

                                }else{

                                    $("#"+co_id).append('<div class="text-align-center dsb-icon-div" id="' + assessment_id + '-' + co_id + '"><div class="dropdown"><img title="' + assessment_name + '" src="' + icon_path + '"  onclick="openMarksModal('+assessment_id+',\'' + assessment_type + '\',' + co_id + ',\'' + assessment_name + '\',\'' + course_name + '\')"><br><span class="txtBlue txtBlue-margin">  ' + assessment_name + '</span> </div></div>');
                                }

                            }else{
                                if($("#view-more-"+co_id+":visible").length>0)
                                {
                                    //$("#"+co_id+" ul").append('<li style="margin-bottom: 50px;margin-left: -30px;"><div class="text-align-center dsb-icon-div" id="' + assessment_id + '-' + co_id + '"><div class="dropdown"><img title="' + assessment_name + '" src="' + icon_path + '"><div class="dropdown-content" id="'+assessment_id+'-list-'+co_id+'"><a href="#" onclick="openMarksModal('+assessment_id+',\'' + assessment_type + '\',' + co_id + ',\'' + assessment_name + '\',\'' + course_name + '\')">Add New</a></div><br><span class="txtBlue txtBlue-margin">  ' + assessment_name + '</span> </div></div></li>');

                                }else{

                                   // $("#"+co_id).append('<div class="text-align-center dsb-icon-div" id="' + assessment_id + '-' + co_id + '"><div class="dropdown"><img title="' + assessment_name + '" src="' + icon_path + '"><div class="dropdown-content" id="'+assessment_id+'-list-'+co_id+'"><a href="#" onclick="openMarksModal('+assessment_id+',\'' + assessment_type + '\',' + co_id + ',\'' + assessment_name + '\',\'' + course_name + '\')">Add New</a></div><br><span class="txtBlue txtBlue-margin">  ' + assessment_name + '</span> </div></div>');
                                }
                            }
                        }else{

                            finalProcessingIcons(co_id, approval_status, conversion_status, grading_status,course_code,course_name,co_course_cr,co_section);
                        }
                        getAssessmentDetails(co_id,assessment_id,course_name);

                        $("#attendance-"+co_id).css({'display':'block'});
                        if (lCount>= obj.length) {
                            finalProcessingIcons(co_id, approval_status, conversion_status, grading_status,course_code,course_name,co_course_cr,co_section);
                        }
                        $("#exam-"+co_id).css({'display':'block'});
                        if (lCount>= obj.length) {
                            finalProcessingIcons(co_id, approval_status, conversion_status, grading_status,course_code,course_name,co_course_cr,co_section);
                        }
                    }else{
                        if (lCount>= obj.length) {
                            finalProcessingIcons(co_id, approval_status, conversion_status, grading_status,course_code,course_name,co_course_cr,co_section);
                        }
                    }
                   // alert(conversion_status);
                    lCount ++;
                });
                $('#loading-'+co_id).css({'display':'none'});
                // todo ahmed
                $(".dropdown").hover(function() {
                    var bonus = 100;
                    var elePoint = $(this).position().top + 95 + bonus;
                    var winPoint =  $(window).height();
                    if(elePoint > winPoint){
                        $(this).find('div').css('bottom','60px');
                    }else{
                        $(this).find('div').css('bottom','');
                    }
                });



            }else{
                $('#loading-'+co_id).css({'display':'none'});
                $("#define-"+co_id).css({'display':'block'});
            }

        }

        $('#more-1').click(function(){
            $('.overflow').slideToggle();
        });

        function finalProcessingIcons(co_id, approval_status, conversion_status,grading_status,course_code,course_name,co_course_cr,co_section)
        {
           // alert(approval_status+'--'+grading_status);
            //alert(co_id+'--'+approval_status+'--'+conversion_status+'--'+grading_status+'--'+course_code+'--'+course_name+'--'+co_course_cr+'--'+co_section);

            if (approval_status == 1)
            {
                if((conversion_status==0) && (grading_status==0)){
                    //alert(co_id+'-1st');
                    if ($('#finalize-'+co_id).length == 0) {
                        $("#" + co_id).append('<div class="text-align-center dsb-icon-div" id="finalize-' + co_id + '"><i class="fa fa-cogs fa-2x" aria-hidden="true" style="color: #EA8B24;" onclick="openModalResultFinalizationConfirm(' + co_id + ',\'' + course_code + ' ' + course_name + '\',' + co_course_cr + ',\'' + co_section + '\')"></i><br><span class="txtBlue txtBlue-margin">Result Finalization</span></div>');
                        $("#finalize-" + co_id).css({'display': 'block'});
                    }else{
                        $("#finalize-" + co_id).css({'display': 'block'});
                    }

                }else if((conversion_status>0) && (grading_status==0)){
//                    alert(co_id+'-2nd');
                    $("div[id*=-"+co_id+"]").css({'display':'none'});
                    $("#website-"+co_id).css({'display':'none'});
                    $("#attendance-"+co_id).css({'display':'none'});
                     $("#exam-"+co_id).css({'display':'none'});
                    $("#"+ co_id).css("width","500px");
                    $("#" + co_id).append('<div class="text-align-center dsb-icon-div" id="grade-posting-' + co_id + '"><i class="fa fa-cogs fa-2x" aria-hidden="true" style="color: #EA8B24;" onclick="openModalGradePosting('+co_id+')"></i><br><span class="txtBlue txtBlue-margin">Grade Posting</span></div>');
                }else{
//                    alert(co_id+'-3rd');
                    $("div[id*=-"+co_id+"]").css({'display':'none'});
                    $("#website-"+co_id).css({'display':'block'});
                    $("#attendance-"+co_id).css({'display':'block'});
                      $("#exam-"+co_id).css({'display':'block'});
                    $("#"+ co_id).css("width","500px");
                    $("#grade-posting-"+co_id).css({'display':'none'});
                    if ($('#green_cog_img_'+co_id).length == 0){
                        $("#" + co_id).append('<div class="text-align-center dsb-icon-div"><img id="green_cog_img_'+co_id+'" src="{{asset('images/green_cog.png')}}" width="40"><br><span class="txtBlue txtBlue-margin" style="margin-bottom: 500px;">Grade Posted</span></div>');
                    }
                }
            }else{
                var srno = 0;
                if(srno==0){
                $("#" + co_id).html('<div class="text-align-center dsb-icon-div" id="finalize-' + co_id + '"><span style="color: #EA8B24"><h2>Some Assessments are not yet entered or Not Approved.</h2></span></div>');
                srno++;
                }
            }
        }

        function getAssessmentDetails(co_id,assessment_id,course_name)
        {

            var old_co_ids = [];
            old_co_ids.push(co_id);
            if(co_id && assessment_id)
            {
                $.ajax({
                    url: '{{url('teacher/courses/assessment/list')}}/'+co_id+'/'+assessment_id,
                    type: "GET",
                    dataType: "HTML",
                    success:function(data) {
                        var obj = JSON.parse(data);
//                        alert(co_id+'----'+JSON.stringify(data));
                        $.each(obj, function (key,value) {
                            var num         = obj[key]['cam_number'];
                            var type        = obj[key]['assessment_id'];
                            var ass_name    = obj[key]['assessment_name'];
                            var cc_id       = obj[key]['course_criteria_id'];


                            if($('#'+type+'-'+num+'-'+co_id).length==0)
                            {
                                if(type!=4)
                                    $('#'+type+'-list-'+co_id).append('<a href="#" onclick="openMarksModal('+type+','+num+','+co_id+',\''+ass_name+'\',\''+course_name+'\',\''+cc_id+'\')" id="'+type+'-'+num+'-'+co_id+'">'+ass_name+' '+num+'</a>');
                            }
                            if(type==4)
                            {
                                
                                $('#4-'+co_id).html('');
                            }
                        });
                    }
                });
            }else{
                alert('CO_ID & Assessment ID Information Missing!');
            }
        }

        function openMarksEntryPopup(co_id,assess_type,num,cc_id)
        {
            var errorCheck = $.fn.dataTable.ext.errMode = 'throw';
            $("#marks_submit_btn").removeAttr("disabled");
            $("#error-msg").css('display','none');
            $("#success-msg").css('display','none');
            $('#form_change_flag').val('0');
            $('#finalize-'+co_id).hide();
            var get_total_marks=0;
            var get_cam_details=0;
            var get_cam_date=0;
            if(co_id && assess_type && num)
            {
                $("#sel_co_id").val(co_id);
                $("#sel_assess_type").val(assess_type);
                var url = '{{url('teacher/courses/students/list/')}}/'+co_id+"/"+assess_type+"/"+num;
                var table = $('#MarksEntryMainTable').DataTable();
                table.destroy();
                var t = $('#MarksEntryMainTable').DataTable( {
                    "keys" : {columns:[4]},
                    "order": [[ 3, "asc" ]],
                    "searching": false,
                    "paging": false,
                    "bInfo": false,
                    "ajax": {
                        "url": url,
                        "dataSrc": ""
                    },
                    "columnDefs": [{
                        "targets": [ 0 ],
                        "visible": false,
                        "searchable": false
                    },{
                        "targets": [ 1 ],
                        "searchable": false,
                        "orderable": false,
                        "className": "text-center",
                        "responsivePriority": 3
                    },{
                        "targets": 2,
                        "className": "text-center",
                        "width": "2%",
                        "responsivePriority": 1,
                        "orderable": false,
                    },{
                        "targets": 3,
                        "className": "text-left",
                        "orderable": false,
                    },{
                        "targets": 4,
                        "className": "text-center",
                        "searching": false,
                        "orderable": false,
                        "responsivePriority": 2
                    }],
                    "columns": [
                        { data: 'cam_id'},
                        { data: 'cam_id'},
                        { data: 'sai_enrollment_no'},
                        { mRender: function (data, type, row) {
                            var std_name = row.spi_name;
                            if(std_name.length<=18){
                                return std_name;
                            }else{
                                return std_name.substr(0, 18) + "...";
                            }
                        }
                        },
                        { mRender: function (data, type, row, meta) {
                            if(row.total_marks!=null)
                            {
                                get_total_marks = row.total_marks;
                            }
                            get_cam_details = row.cam_details;
                            get_cam_date    = row.cam_date;

                               // alert(get_cam_date);
                            if(row.obtained_marks==undefined){
                                var obtMarks = '';
                            }
                            else{
                                var obtMarks = row.obtained_marks;
                            }
                            $("#total_marks").val(get_total_marks);
                            $("#old_total_marks").val(get_total_marks);
                            $(".obt-marks").attr("max", get_total_marks);

                            $("#cam_details").val(get_cam_details);
                            $("#cam_date").val(get_cam_date);

                            $("#co_id").val(co_id);
                            $("#assessment_id").val(assess_type);
                            $("#cam_number").val(num);
                            $("#course_criteria_id").val(cc_id);
                            $("#ccd_id").val(row.ccd_id);


                            return '<input type="number" id="'+meta.row+'" name="obt_marks[]"  class="form-control obt-marks obt-marks-number" min="0" step="0.01" value="'+obtMarks+'"/>' +
                                '<input type="hidden" id="old_obt_marks[]" name="old_obt_marks[]" value="'+obtMarks+'" />' +
                                '<input type="hidden" id="cam_id[]" name="cam_id[]" value="'+row.cam_id+'"/>' +
                                '<input type="hidden" id="sai_enrollment_no[]" name="sai_enrollment_no[]" value="'+row.sai_enrollment_no+'"/>' +
                                '<input type="hidden" id="sai_id[]" name="sai_id[]" value="'+row.sai_id+'"/>' +
                                '<input type="hidden" id="sc_co_id[]" name="sc_co_id[]" value="'+row.sc_co_id+'">'
                        }
                        }
                    ]
                });

                t.on( 'order.dt search.dt', function () {
                    t.column(1, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                        cell.innerHTML = i+1;
                    } );
                } ).draw();
            }
        }

        $('#courseMarksModal').on('shown.bs.modal', function () {
            $('#cam_details').focus();
        })
        $('#courseMarksModal').on('hidden.bs.modal', function () {
            getCourseCriteria();
        })

        function setMaxLimit()
        {
            $(".obt-marks").attr("max", $("#total_marks").val());
            $("#res_total_marks").css('display','none');
        }

        $('#marksEntryForm').change(function() {
            $('#form_change_flag').val('1');
        });

        $("#marksEntryForm").submit(function(e)
        {

           // alert(123);
          //  return false;
            $("#marks_submit_btn").attr("disabled", true);
            $("#error-msg").css('display','none');
            $("#success-msg").css('display','none');
            //$("#marks_submit_btn").attr('disabled','true');
            e.preventDefault();
            $.ajax({
                type:'POST',
                url:'{{url('teacher/courses/store/marks')}}',
                data: $("#marksEntryForm").serializeArray(),
                success:function(data){

                   // alert(data);
                    //return false;
                    var result          = data.split('-');
                    var res             = result[0];
                    var missing_marks   = result[1];

                    if(res=='A'){
                        $("#success-msg").css('display','block');
                        var show_msg = '<strong>Success!</strong> Marks Saved Successfully';
                        if(missing_marks>0)
                        {
                            show_msg += '<br>'+missing_marks+' Students Marks Are Missing';
                        }
                        $("#success-msg").html(show_msg);
                        setTimeout(function(){
                            $('#courseMarksModal').modal('hide');
                            getCourseCriteria();
                        },2000);

                    }else if(res=='B'){
                        $("#error-msg").css('display','block');
                        $("#error-msg").html('<strong>Alert!</strong> No Change in the Marks');
                        $('#error-msg').focus();
                        $("#marks_submit_btn").removeAttr("disabled");
                    }else if(res=='C'){
                        $("#error-msg").css('display','block');
                        $("#error-msg").html('<strong>Alert!</strong> Obtained Marks Must Be Less Than Total Marks');
                        $('#error-msg').focus();
                        $("#marks_submit_btn").removeAttr("disabled");
                    }else if(res=='D'){
                        $("#success-msg").css('display','block');
                        $("#success-msg").html('<strong>Success!</strong> Title & Date Saved Successfully');
                        setTimeout(function(){
                            $('#courseMarksModal').modal('hide');
                            getCourseCriteria();
                        },2000);
                    }else if(res==0){
                        $("#error-msg").css('display','block');
                        $("#error-msg").html('<strong>Error!</strong> Unchanged Marks, Please Correct & Try Again');
                        $('#error-msg').focus();
                        $("#marks_submit_btn").removeAttr("disabled");
                    }else{
                        $("#error-msg").css('display','block');
                        $("#error-msg").html('<strong>Error!</strong>'+data);
                        $('#error-msg').focus();
                        $("#marks_submit_btn").removeAttr("disabled");
                    }
                },
                error: function(data,status, response) {


                }
            });
        });


        function showRawMarksList(){


            //   btn btn-primary grader-cretria-method grader-views


            $('.grader-cretria-method').hide();
            $('.grader-criteria-selected').hide();

            $('.raw_mark_views').fadeIn(1000);

            $('#raw_marks_back_btn').hide();




            $("#course_raw_summary").css("background-color", "yellow");
            $( "#finalizationModal-dialog" ).addClass( "modal-lg" );
            $("#finalization-details").append("" +
                "<br><div style='float: left;'><label for='name' style='color: black; font-weight: bold;'>Assessment Marks</label></div>" +
                "<div style='float: right; text-align: right;'><label for='name' style='color: red; font-weight: bold; float: right;'>Empty Scores Students will be considered I grade</label></div>" +
                "");

            //   rf_close_next


            // $('#SubjectAssesmentTypeTable').hide();



            $("#CourseCriteriaDetails").hide();
            $("#showRawMarks").empty();
            $("#showRawMarks").show();
            $("#showSummaryMarks").empty();
            $('#rf_process_button').show();
            $('#rf_close_button').hide();
            $('#rf_delete_button').hide();
            $('#rf_close_next').show();
            $('#rf_close_back').show();

            // todo course_critiria request



            $.ajax({
                url:'{{url('reportgenerator/load_marks2')}}/'+$('#rf_co_id').val(),
                type: "GET",
                beforeSend: function(){
                    $('#showRawMarks').html("<div style='width:100%;text-align: center;'><img src='{{asset('images/2.gif')}}' /></div>");
                },
                success: function (data) {
                    $("#showRawMarks").html(data);
                }
            });



        }






        $('#finalizationModal').on('hidden.bs.modal', function () {
            getCourseCriteria();
        });

        $("#rf_process_form").submit(function(e)
        {
            $("#rf_process_button").hide();


            var array_i_values = [];
            $('input[type=checkbox]:checked').each(function() {
		if($(this).attr('id')=='i_grade[]'){
                	if( $(this).is(':checked') ) {
                    		array_i_values.push( $(this).val() );
                	}
		}
            });
            var arrayIValues = array_i_values.join(',');

            $.ajax({
                type:'POST',
                url:'{{url('teacher/courses/mark_i_grade/')}}',
                data: {
                    'sc_id_list': arrayIValues,
                    'sel_co_id': $('#rf_co_id').val()
                },
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success:function(data){
//                    alert(data);
                },
                error: function(data,status, response) {

                }
            });

            e.preventDefault();
            $('#rf_close_back').hide();

            setTimeout(function(){
                $.ajax({
                    type:'POST',
                    url:'{{url('teacher/courses/finalize_result/')}}',
                    data: $("#rf_process_form").serializeArray(),
                    success:function(data){
                        if(data==true)
                        {
                            $("#showRawMarks").hide();
                            //   $('#rf_process_button').hide();
                            $.ajax({
                                url:'{{url('reportgenerator/courseMarksSummaries2')}}/'+$('#rf_co_id').val(),
                                type: "GET",
                                success: function (data) {
//                                $("#showSummaryMarks").html('<div style="width:100%;text-align: center;"><i id="cogCourses" class="fa fa-cog fa-5x fa-spin" style="color:#EA8B24;margin-top: 80px;"></i><h1 style="margin-top: 30px;">CMS Grader is working, Please wait</h1></div>');
                                    $("#showSummaryMarks").html('<div style="width:100%;text-align: center;"><img src="{{asset('images/loading-gears-animation.gif')}}" height="200px" style="margin-left:30px;"></img><h1 style="margin-top: 30px;">Grader is Generating Percentage Marks, Please Wait!</h1></div>');
                                    setTimeout(function(){
                                        $("#showSummaryMarks").html(data);
                                        $('#rf_close_button').show();
                                        $('#rf_delete_button').show();
                                    },6000);

                                }
                            });
                        }

                    },
                    error: function(data,status, response) {

                    }
                });
            }, 2000);
        });


        function openModalSummaryModal(co_id) {
            $("#showRawMarks").hide();
            $('#rf_process_button').hide();
            $('#rf_close_button').show();
            $('#rf_delete_button').show();
            $.ajax({
                url:'{{url('reportgenerator/courseMarksSummaries')}}/'+$('#rf_co_id').val(),
                type: "GET",
                success: function (data) {
                    $("#showSummaryMarks").html(data);
                }
            });
        }


        function openModalGradePosting(var_co_id)
        {
            $("#showGradePosting").css({height:'405px'});
            $("#showGradePosting").html('<div style="width:100%;text-align: center;"><img src="{{asset('images/2.gif')}}" height="200px"></img></div>');
            $("#post_co_id").val(var_co_id);
            $('#postingModal').modal('show');
            $.ajax({
                type:'GET',
                url:"{{ URL::route('GradingMarksSummaries') }}/"+var_co_id,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success:function(data){
                    $("#showGradePosting").html(data);
                }
            });

        }

        function openModalGradeSummary(var_co_id)
        {
            $("#showGradePosting").html('<div style="width:100%;text-align: center;"><img src="{{asset('images/loading-gears-animation.gif')}}" height="200px"></img><h1 style="margin-top: 30px;">Grader is submitting  Grades, Please Wait!</h1></div>');

            $('#postingModal').modal('show');
            $.ajax({
                type:'GET',
                url:"{{ URL::route('grading') }}/"+var_co_id,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success:function(data){
                    $("#showGradePosting").html(data);
                }
            });
        }

        function deleteMarkSummaries(co_id)
        {
            if(co_id==undefined)
            {
                $.ajax({
                    url:'{{url('teacher/courses/delete_marks_summaries/')}}/'+$('#rf_co_id').val(),
                    type: "GET",
                    success: function (data) {
                        $("#showSummaryMarks").html('<div style="width:100%;text-align: center;"><img src="{{asset('images/loading-gears-animation.gif')}}" height="200px" style="margin-left:30px;"></img><h1 style="margin-top: 30px;">Grader is Deleting Percentage Marks, Please Wait!</h1></div>');
                        setTimeout(function(){
                            $('#finalizationModal').modal('toggle');
                        },3000);
                    }
                });
            }else{
                $.ajax({
                    url:'{{url('teacher/courses/delete_marks_summaries/')}}/'+co_id,
                    type: "GET",
                    success: function (data) {
                        $("#gradingMarksSummary").empty();
                        $("#showGradePosting").html('<div style="width:100%;text-align: center;"><img src="{{asset('images/loading-gears-animation.gif')}}" height="200px" style="margin-left:30px;"></img><h1 style="margin-top: 30px;">Grader is Deleting Percentage Marks, Please Wait!</h1></div>');
                        setTimeout(function(){
                            location.reload(false);
                            //$('#postingModal').modal('toggle');
                        },3000);
                    }
                });
            }

        }

        $('#main-parent-div').bind('heightChange', function(){
            console.log('changed');
        });

        $('#main-parent-div').resize(function() {
            console.log('changed');
        });



    </script>
    <script src="https://www.jqueryscript.net/demo/Easy-Top-Loading-Bar-Plugin-For-jQuery-loadBar/jquery.loadBar.js"></script>
    <script src="{{asset("js/grader/teacher_grading.js")}}"></script>
@stop
