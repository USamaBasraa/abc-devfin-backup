<style type="text/css">

    :root {
        --lecture-primary: #17675F;
        --lecture-background: #17675F1A; 
        --lecture-text: #292929; 
        --lecture-badge: #FFB938; 
        --lecture-border: rgba(23, 103, 95, 0.12);
    }

    /* Existing utility classes */
    .scroller {
        width: 300px;
        height: 100px;
        overflow-y: scroll;
        scrollbar-color: var(--lecture-primary) #ffffff;
        scrollbar-width: thin;
    }

    .tar_div {
        border: solid 1px var(--lecture-primary);
    }

    /* =====================================================
       TODAY LECTURE COURSE CARD
    ===================================================== */

    .card.e-panel {
        background: var(--lecture-background) !important;
        border: 0 !important;
        border-radius: 7px !important;
        box-shadow: none !important;
        margin: 0 0 14px 0 !important;
        overflow: visible !important;
    }

    /* Remove Bootstrap default spacing */
    .card.e-panel > .card-body {
        display: none !important;
        padding: 0 !important;
        border: 0 !important;
    }

    /* =====================================================
       COURSE HEADER
    ===================================================== */

    .card.e-panel > .card-header {
        position: relative;
        min-height: 42px;
        padding: 14px 16px 4px !important;
        background: transparent !important;
        border: 0 !important;
        border-radius: 7px 7px 0 0 !important;
    }

    .card.e-panel > .card-header .row {
        width: 100% !important;
        margin: 0 !important;
        align-items: center;
    }

    .card.e-panel > .card-header [class*="col-"] {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    /* Course title */
    .card.e-panel > .card-header strong {
        color: var(--lecture-text) !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        line-height: 1.4;
    }

    .card.e-panel > .card-header strong > i.fa-book {
        display: none !important;
    }

    /* Grading type badge */
    .card.e-panel > .card-header .col-md-2 {
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .card.e-panel > .card-header .col-md-2 a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 18px;
        padding: 2px 7px;
        background: var(--lecture-badge) !important;
        border-radius: 10px;
        color: #ffffff !important;
        font-size: 9px !important;
        font-weight: 500;
        line-height: 1;
        text-decoration: none !important;
        white-space: nowrap;
    }

    /* Hide old cog/help icons inside grading badge */
    .card.e-panel > .card-header .col-md-2 a > i,
    .card.e-panel > .card-header .col-md-2 a b {
        display: none !important;
    }

    /* =====================================================
       ICONS AREA
    ===================================================== */

    .card.e-panel > .card-footer {
        padding: 5px 12px 10px !important;
        background: transparent !important;
        border: 0 !important;
        border-radius: 0 0 7px 7px !important;
    }

    .card.e-panel > .card-footer > .row {
        margin-left: -4px !important;
        margin-right: -4px !important;
        align-items: stretch;
    }

    .lecture_icons {
        display: flex;
        align-items: stretch;
        justify-content: center;
        width: 100% !important;
        padding-left: 4px !important;
        padding-right: 4px !important;
        margin-bottom: 0 !important;
        text-align: center !important;
        border: 0 !important;
    }

    .lecture_icons .card-block {
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 64px;
        padding: 4px 2px !important;
        margin: 0 !important;
        background: transparent !important;
        border: 0 !important;
    }

    .lecture_icons a,
    .lecture_icons p {
        display: flex !important;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin: 0 !important;
        color: var(--lecture-text) !important;
        font-size: 12px !important;
        font-weight: 400;
        line-height: 1.25;
        text-align: center;
        text-decoration: none !important;
        transition: color 0.2s ease, transform 0.2s ease;
    }

    .lecture_icons a:hover,
    .lecture_icons p:hover {
        color: var(--lecture-primary) !important;
        text-decoration: none !important;
        transform: translateY(-1px);
    }

    /* Existing icon images */
    .lecture_icons img {
        display: block;
        width: 27px !important;
        height: 27px !important;
        margin: 0 auto 6px !important;
        object-fit: contain;
    }

    /*
       Converts existing multicolour icons toward green.
       Remove this filter if original image colours are required.
    */
    .lecture_icons img {
        filter:
            grayscale(1)
            sepia(1)
            saturate(4)
            hue-rotate(118deg)
            brightness(0.62);
    }

    /* Disabled course-outline state */
    .lecture_icons a.not-active {
        opacity: 0.45;
        pointer-events: none;
    }

    /* =====================================================
       DETAILS / EXPANDED ROW
    ===================================================== */

    .card.e-panel .multi-collapse {
        position: relative;
        /* margin: 0 !important; */
            margin: 21px 0 !important;
        padding: 21px 0 !important;
        background: transparent !important;
        border: 0 !important;
        border-top: 1px dotted rgba(23, 103, 95, 0.24) !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .card.e-panel .multi-collapse.show {
        display: flex;
    }

    .card.e-panel .multi-collapse .lecture_icons .card-block {
        min-height: 64px;
    }

    /* Arrow shown below opened details row */
    /* .card.e-panel .multi-collapse.show::after {
        content: "\f077";
        position: absolute;
        left: 50%;
        bottom: -15px;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        transform: translateX(-50%);
        background: #4E9A90;
        border: 2px solid #ffffff;
        border-radius: 50%;
        color: #ffffff;
        font-family: FontAwesome;
        font-size: 8px;
        line-height: 1;
        box-shadow: 0 2px 5px rgba(23, 103, 95, 0.15);
    } */

    /* =====================================================
       DETAILS / MORE ICON
    ===================================================== */

    .card.e-panel a[data-toggle="collapse"] img {
        transition: transform 0.25s ease;
    }

    .card.e-panel a[data-toggle="collapse"][aria-expanded="true"] img {
        transform: rotate(180deg);
    }

    /*
       Current image is PDF icon.
       It is visually softened so it fits the new design.
    */
    .card.e-panel a[data-toggle="collapse"] img {
        opacity: 0.9;
    }

    /* =====================================================
       FINAL GRADE / DOWNLOAD AREA
    ===================================================== */

    .card.e-panel .fileDownload,
    .card.e-panel .downloadZip {
        border-color: var(--lecture-primary) !important;
        color: var(--lecture-primary) !important;
        border-radius: 6px;
    }

    .card.e-panel .fileDownload:hover,
    .card.e-panel .downloadZip:hover {
        background: var(--lecture-primary) !important;
        color: #ffffff !important;
    }

    /* =====================================================
       HOVER
    ===================================================== */

    .card.e-panel {
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }

    .card.e-panel:hover {
        box-shadow: 0 4px 14px rgba(23, 103, 95, 0.08) !important;
    }

    /* =====================================================
       TABLET
    ===================================================== */

    @media (max-width: 991.98px) {

        .card.e-panel > .card-header {
            padding: 12px 13px 5px !important;
        }

        .card.e-panel > .card-header strong {
            font-size: 13px !important;
        }

        .lecture_icons .card-block {
            min-height: 72px;
        }

        .lecture_icons a,
        .lecture_icons p {
            font-size: 11px !important;
        }

        .lecture_icons img {
            width: 25px !important;
            height: 25px !important;
        }
    }

    /* =====================================================
       MOBILE
    ===================================================== */

    @media (max-width: 767.98px) {

        .card.e-panel {
            margin-bottom: 12px !important;
        }

        .card.e-panel > .card-header .row {
            display: flex;
            flex-wrap: nowrap;
        }

        .card.e-panel > .card-header .col-md-10 {
            flex: 1 1 auto;
            width: auto;
        }

        .card.e-panel > .card-header .col-md-2 {
            flex: 0 0 auto;
            width: auto;
            margin-left: 8px;
        }

        .card.e-panel > .card-header strong {
            font-size: 12px !important;
        }

        .card.e-panel > .card-footer {
            padding-left: 7px !important;
            padding-right: 7px !important;
        }

        .lecture_icons {
            margin-bottom: 5px !important;
        }

        .lecture_icons .card-block {
            min-height: 68px;
        }

        .lecture_icons a,
        .lecture_icons p {
            font-size: 10px !important;
        }

        .lecture_icons img {
            width: 23px !important;
            height: 23px !important;
            margin-bottom: 5px !important;
        }
    }
    .today_lec_card  {
        background-color:white;
        padding: 18px;
        padding-bottom: 6px;
        border-radius: 12px;
    }
    /*==================================================
    TODAY LECTURE - FINAL UI OVERRIDE
==================================================*/

.card.e-panel{
    background:#17675F1A !important;
    border:none !important;
    border-radius:8px !important;
    box-shadow:none !important;
    overflow:hidden !important;
    margin:0 0 12px 0 !important;
}

/* Header */

.card.e-panel>.card-header{
    background:transparent !important;
    border:none !important;
    padding:14px 18px 4px !important;
    min-height:auto !important;
}

/* Course Title */

.card.e-panel>.card-header strong{
    color:#2E2E2E !important;
    font-size:14px !important;
    font-weight:500 !important;
    line-height:1.4 !important;
}

/* Remove Book Icon */

.card.e-panel>.card-header .fa-book{
    display:none !important;
}

/* Badge */

.card.e-panel>.card-header .col-md-2{
    display:flex;
    justify-content:flex-end;
    align-items:flex-start;
}

.card.e-panel>.card-header .col-md-2 a{

    background:#FDB73A !important;

    color:#fff !important;

    font-size:9px !important;

    font-weight:500 !important;

    /* border-radius:20px !important; */

    padding:2px 8px !important;

    line-height:1.2 !important;

    text-decoration:none !important;

}

/* Remove icons inside badge */

.card.e-panel>.card-header .col-md-2 i,
.card.e-panel>.card-header .col-md-2 b{
    display:none !important;
}

/* Footer */

.card.e-panel>.card-footer{

    background:transparent !important;

    border:none !important;

    padding:6px 12px 10px !important;

}

/* Icon Grid */

.lecture_icons{

    text-align:center !important;

    margin-bottom:0 !important;

}

.lecture_icons .card-block{

    min-height:58px !important;

    display:flex !important;

    flex-direction:column;

    justify-content:center;

    align-items:center;

    padding:0 !important;

    background:none !important;

    border:none !important;

}

/* Icon */

/* .lecture_icons img{

    width:24px !important;

    height:24px !important;

    object-fit:contain;

    margin-bottom:6px !important;

} */

/* Label */

.lecture_icons a,
.lecture_icons p{

    color:#2E2E2E !important;

    font-size:12px !important;

    font-weight:400 !important;

    line-height:1.25 !important;

    text-decoration:none !important;

    margin:0 !important;

}

/* Hover */

.lecture_icons a:hover{

    color:#17675F !important;

    text-decoration:none !important;

}

/* Expanded Area */

.multi-collapse{

    background:transparent !important;

    border:none !important;

    border-top:1px dashed rgba(23,103,95,.18) !important;

    border-radius:0 !important;

    box-shadow:none !important;

    margin-top:6px !important;

    padding-top:12px !important;

}

/* Remove Existing Lavender Shadow */

.multi-collapse.show{

    background:transparent !important;

    box-shadow:none !important;

}

/* Bottom Arrow */

/* .multi-collapse.show::after{

    content:"▲";

    position:absolute;

    left:50%;

    bottom:-11px;

    transform:translateX(-50%);

    width:18px;

    height:18px;

    border-radius:50%;

    background:#4E988E;

    color:#fff;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:8px;

    line-height:1;

    border:2px solid #fff;

} */

/* Remove Extra Row Gap */

.card.e-panel .row{

    margin-left:-5px !important;

    margin-right:-5px !important;

}

.card.e-panel .row>[class*="col"]{

    padding-left:5px !important;

    padding-right:5px !important;

}

/* Mobile */

@media(max-width:768px){

    .lecture_icons img{

        width:22px !important;

        height:22px !important;

    }

    .lecture_icons a,
    .lecture_icons p{

        font-size:10px !important;

    }

}
/* ==================================================
   GRADING STATUS POSITION — CLOSED / OPEN LECTURE
================================================== */

/* Header row */
.card.e-panel > .card-header .row {
    display: flex !important;
    align-items: center !important;
    flex-wrap: nowrap;
}

/* Course name */
.card.e-panel > .card-header .col-md-10 {
    flex: 1 1 auto;
    max-width: none !important;
}

/* Absolute / Relative badge — closed state */
.card.e-panel > .card-header .col-md-2 {
    flex: 0 0 auto;
    width: auto !important;
    max-width: none !important;
    margin-left: auto;
    display: flex !important;
    justify-content: flex-end !important;
}

/* Open lecture header */
.card.e-panel.lecture-open > .card-header .row {
    flex-wrap: wrap !important;
    align-items: flex-start !important;
}

/* Badge above course name when open */
.card.e-panel.lecture-open > .card-header .col-md-2 {
    order: 1;
    flex: 0 0 100%;
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 0 6px 0 !important;
    justify-content: flex-start !important;
}

/* Course name below badge when open */
.card.e-panel.lecture-open > .card-header .col-md-10 {
    order: 2;
    flex: 0 0 100%;
    width: 100% !important;
    max-width: 100% !important;
}

/* Open-state header spacing */
.card.e-panel.lecture-open > .card-header {
    padding-top: 14px !important;
    padding-bottom: 4px !important;
}

/* Keep badge compact */
.card.e-panel > .card-header .col-md-2 a {
    display: inline-flex !important;
    width: auto !important;
    min-height: auto !important;
    padding: 2px 7px !important;
    background: #FDB73A !important;
    border-radius: 12px !important;
    color: #ffffff !important;
    font-size: 9px !important;
    line-height: 1.2 !important;
}
/* Final Grade Submitted - Center Course Material */
.today_lec_card > .card.e-panel:last-of-type{
    margin-bottom:0 !important;
}
/* Closed lecture details */
.card.e-panel .multi-collapse:not(.show){
    display:none !important;
}

/* Open lecture details */
.card.e-panel .multi-collapse.show{
    display:flex !important;
}
.card.e-panel .fileDownload {
    /* color: white !important; */
    background: none !important;
}
#courseCriteriaModal table thead th,
#courseCriteriaModal table thead td {
    background-color: #17675F !important;
    color: #FFFFFF !important;
}
#courseCriteriaModal #submit_assessment_table tbody tr:first-child td[colspan="3"] {
    background-color: #17675F !important;
    color: #FFFFFF !important;
}


















/* arslan issue 2 style starts from below */

/* Course Criteria Required Section — Figma Style */
.card.e-panel .card-footer:has(.alert.alert-info) {
    padding: 16px !important;
}

/* Warning message */
.card.e-panel .card-footer:has(.alert.alert-info) .alert.alert-info {
    display: flex !important;
    align-items: center;
    gap: 10px;
    width: 100%;
    margin: 0 0 16px !important;
    padding: 12px 16px !important;
    background: #fff8e8 !important;
    border: 1px solid #ffb938 !important;
    border-radius: 8px !important;
    color: #2d3748 !important;
}

.card.e-panel .card-footer:has(.alert.alert-info) .alert.alert-info > div {
    float: none !important;
    color: #2d3748 !important;
    border: 0 !important;
    font-family: Montserrat, sans-serif;
    font-size: 13px;
    font-weight: 500 !important;
    line-height: 1.5;
}

.card.e-panel .card-footer:has(.alert.alert-info) .alert.alert-info i {
    margin: 0 !important;
    color: #d98b00 !important;
    font-size: 18px;
}

/* Three-column cards container */
.card.e-panel .card-footer:has(.alert.alert-info)
.alert.alert-info + .row {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin: 0 !important;
}

/* Remove old Bootstrap column sizing */
.card.e-panel .card-footer:has(.alert.alert-info)
.alert.alert-info + .row > .lecture_icons {
    display: block !important;
    width: 100% !important;
    max-width: none !important;
    padding: 0 !important;
}

/* Individual option card */
.card.e-panel .card-footer:has(.alert.alert-info)
.alert.alert-info + .row .card-block {
    display: flex !important;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100% !important;
    min-height: 110px !important;
    padding: 16px !important;
    background: rgba(15, 118, 110, 0.05) !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    box-sizing: border-box;
    color: #64748b !important;
    font-family: Montserrat, sans-serif;
    font-size: 11px !important;
    font-weight: 400;
    line-height: 1.35;
    text-align: center;
    transition:
        background-color 0.2s ease,
        border-color 0.2s ease,
        transform 0.2s ease;
}

.card.e-panel .card-footer:has(.alert.alert-info)
.alert.alert-info + .row .card-block:hover {
    background: rgba(15, 118, 110, 0.09) !important;
    border-color: #17675f !important;
    transform: translateY(-1px);
}

/* Figma-style icon container */
.card.e-panel .card-footer:has(.alert.alert-info)
.alert.alert-info + .row .card-block img {
    display: block !important;
    width: 40px !important;
    height: 40px !important;
    margin: 0 auto 12px !important;
    padding: 9px;
    box-sizing: border-box;
    object-fit: contain;
    background: #17675f !important;
    border-radius: 8px !important;
    filter: brightness(0) invert(1) !important;
}

/* Title */
.card.e-panel .card-footer:has(.alert.alert-info)
.alert.alert-info + .row .card-block span {
    color: #1e293b !important;
    font-family: Montserrat, sans-serif;
    font-size: 12px !important;
    font-weight: 500 !important;
    line-height: 1.25;
}

/* Timetable link */
.card.e-panel .card-footer:has(.alert.alert-info)
.alert.alert-info + .row .card-block a {
    width: 100%;
    color: #1e293b !important;
    font-family: Montserrat, sans-serif;
    text-decoration: none !important;
}

/* Mobile */
@media (max-width: 767.98px) {
    .card.e-panel .card-footer:has(.alert.alert-info)
    .alert.alert-info + .row {
        grid-template-columns: 1fr;
    }
}
</style>
<?php
$count = count($teacher_courses);
$height = 115;
$tableheight = $count * $height . 'px';
?>
@php
    $surveyExists = DB::table('student_feedbacks')
        ->where('sf_type', 'librarysurvey')
        ->where('SAI_ENROLLMENT_NO', auth()->user()->username)
        ->exists();
@endphp

@include('grader.teacher.course_criteria_popup')
@include('grader.teacher.check_lectures')

{{-- @dd($teacher_courses) --}}
{{-- @include('grader.teacher.check_lectures') --}}


                <!-- Today Lecture Code -->
                        @if(count($teacher_courses)>0)
                        {{-- @include('grader.teacher.check_lectures') --}}

                            {{-- <div class="row mt-4">
                                <div class="col-12">
                                    <div class="expanel expanel-primary">
                                        <div class="expanel-heading" style="text-align: center">
                                            <h3 class="expanel-title">Today Lecstures(s)</h3>
                                        </div>
                                        <div class="expanel-body">
                                            @include('grader.teacher.check_lectures')
                                        </div>
                                    </div>
                                </div>

                            </div> --}}

                        @endif

   <h4 class="profile-overview-title" style="margin-bottom:16px !important;">
        Assigned Courses
    </h4>
                 <!-- Today Lecture Code End-->
                 <div class="today_lec_card" style="margin-bottom:16px !important;">
                   <?php
                    if(auth()->user()->group_id != 825){
                    $sr_no = 1;
                    $card_col=2;
                    $show='';
                    if(count($teacher_courses)>0) {
                    foreach ($teacher_courses as $rec_teacher_courses):
                        $image_width = "35px";
                        $fc_co_id = $rec_teacher_courses->fc_co_id;

                        $fc_co_course_id = $rec_teacher_courses->fc_co_course_id;
                        $fc_co_idd = my_simple_crypt($fc_co_id, 'e');
                        $co_course_id = my_simple_crypt($fc_co_course_id, 'e');
                        if($rec_teacher_courses->fc_co_school_id==5 ||$rec_teacher_courses->fc_co_school_id==12 ){
                          $show='';
                        }else{
                        //   $show='not-active';
                          $show='';
                        }
                    ?>
                    <div class="card e-panel">
                        <div class="card-header" style="padding: 7px 20px;background-color:red;">
                            <div class="row" style="width: 100% !important">
                                <div class="col-12  col-md-10 col-xl-10">
                                    <strong style="font-size:16px">
                                        <i class="fa fa-book"></i>
                                        <?= $rec_teacher_courses->course_code . ' | ' . $rec_teacher_courses->course_cd_name; ?>
                                        <?= $rec_teacher_courses->co_course_cr . ' | ' . $rec_teacher_courses->co_section; ?>
                                    </strong>
                                </div>
                                <div class="col-12  col-md-2 col-xl-2">
                                    <strong style="font-size:16px">
                                        <a   data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="bottom" data-trigger="hover" data-title="CMS Guidline " class="guidline-alert-<?=($rec_teacher_courses->course_grading_type=='Relative')?'relative':'absolute';?>" data-content="Loading... "  ><i class="fa fa-cogs"></i>
                                        <?= $rec_teacher_courses->course_grading_type;?><b><i  class="pe-7s-help1" data-toggle="tooltip" title="pe-7s-info"></i></b></a>
                                    </strong>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="padding:1px !important;">
                            <?php
                            $fc_co_id = $rec_teacher_courses->fc_co_id;
                            $fc_co_course_id=$rec_teacher_courses->fc_co_course_id;
                            $fc_co_idd=my_simple_crypt($fc_co_id, 'e' );
                            ?>
                        </div>
                        <div class="card-footer padding-footer" style="padding-bottom: 14px !important;">
                            @if($rec_teacher_courses->check_final>0)
                            <div class="row">
                                <div class="col-md-12 col-12 col-xl-12 lecture_icons">
                                            <strong> Final Grade Submitted. </strong>
                                </div>
                                <div class="col-12 col-md-12 col-xl-12 text-center pt-3 pb-3">
                                    <button class="btn btn-outline-primary fileDownload col-2 col-md-3 col-xl-3"
                                            type="button"
                                            data-course_criteria_detail_id="<?php echo $fc_co_course_id;?>"
                                            data-co_id="<?php echo $fc_co_id;?>"
                                            data-co_name="<?=
                                           preg_replace('/[^A-Za-z]/', '', $rec_teacher_courses->course_code . ' : ' . $rec_teacher_courses->course_cd_name.$rec_teacher_courses->co_section);
                                            // $rec_teacher_courses->course_code;
                                            ?>">
                                      Course Material
                                    </button>
                                    <a class="btn btn-outline-primary invisible downloadZip col-2 col-md-3 col-xl-3">
                                   
                                      Start Download
                                    </a>
                                  </div>
                            </div>
                            @else
                                    @if($rec_teacher_courses->course_criteria<=0)
									    <div class="row">
											<div class="alert alert-info col-md-12 col-12 col-xl-12" role="alert" style="background-color: #ffff">
												<div style="float:left;">
													<i class="fa fa-bell-o mr-2" aria-hidden="true"></i>
												</div>
												<div style="float:left;font-weight:bold;color:tomato;border: solid 0px black;">
													Welcome! First, you need to enter the course criteria, then you will be able to mark the attendance.
												</div>
											</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 col-12 col-xl-<?=$card_col;?> lecture_icons">
                                                <div data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="top" data-trigger="hover" data-title="Course Criteria"  data-content="This is an evaluation criteria defined by the instructor of course. " class="card-block" style="padding-top: 0px !important;">
                                                <img  src="{{asset('images/crateria.png')}}" width="45px" onclick="openModalCourseCriteria('<?=$rec_teacher_courses->course_cd_name;?>',<?=$rec_teacher_courses->fc_co_id;?>,<?=$rec_teacher_courses->fc_co_course_id;?>)" style="cursor: pointer;">
                                                <br> <span style="font-weight: bold;">Define </span> <br> Course Criteria
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-12 col-xl-<?=$card_col;?> lecture_icons">
                                                <div data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="top" data-trigger="hover" data-title="Course Timetable"  data-content="Timetable structured for daily, weekly, and monthly schedules. " class="card-block" style="padding-top: 0px !important;">
                                                <a href="{{route('Timetable',my_simple_crypt($fc_co_id, 'e' ))}}" >
                                                <img src="{{asset('images/lecture_schedule1.png')}}" width="45px"  style="cursor: pointer;">
                                                </a>
                                                <br> <span style="font-weight: bold;">Time Table </span> <br> Course Timetable
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-12 col-xl-6 lecture_icons">
                                                <div data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="top" data-trigger="hover" data-title="Course Criteria"  data-content="This is an evaluation criteria defined by the instructor of course. " class="card-block" style="cursor:pointer;padding-top: 0px !important;">
                                                <img src="{{asset('images/courseoutline.png')}}" width="45px"  style="cursor: pointer;">

                                                <br> <span style="font-weight: bold;">Course Details </span> <br> Please define course criteria..
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                    <div class="row">
                                        <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                            <div  class="card-block" style="padding-top: 0px !important; display: inline-block;">
                                            <a  class="<?=$show;?> guidline-alert-outline" href="{{route('teacher/course_content',$fc_co_id)}}" data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="right" data-trigger="hover" data-title="CMS Guidline"  data-content=" "  >
                                                <img src="{{asset('images/courseoutline.png')}}" width="<?=$image_width;?>"></br> Course Outline </a>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                            <div class="card-block" style="padding-top: 0px !important;">
                                            <a  data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="bottom" data-trigger="hover" data-title="CMS Guidline "  data-content=" " class="guidline-alert-schedule" href="{{route('Lectures',my_simple_crypt($fc_co_id, 'e' ))}}" >
                                                <!-- <img src="{{asset('images/coursematerial.png')}}" width="<?=$image_width;?>"><br>Lecture Schedule -->
                                                <img src="{{asset('images/lecture_schedule1.png')}}" width="<?=$image_width;?>"><br>Lecture Schedule
                                            </a>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                            <div class="card-block" style="padding-top: 0px !important;">
                                                <a href="{{route('teacher/online_exam')}}/<?=$fc_co_idd?>/<?=$co_course_id?>" data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="bottom" data-trigger="hover" data-title="CMS Guidline"  data-content=" " class="guidline-alert-exam" >
                                                    <img src="{{asset('images/onlineexam.png')}}" width="<?=$image_width;?>">
                                                    <br>Online Exam
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                            <div class="card-block" style="padding-top: 0px !important;">
                                            
                                                <a href="<?=url('course/notification/'.$fc_co_id)?>" data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="bottom" data-trigger="hover" data-title="CMS Guidline"  data-content=" " class="guidline-alert-notification" >
                                                    <!-- <img src="{{asset('images/class_discussion.png')}}" width="<?=$image_width;?>"><br>Faculty Notifications new -->
                                                    <img src="{{asset('images/fNotification.png')}}" width="<?=$image_width;?>"><br>Faculty Notifications
                                                </a>
                                            </div>
                                        </div>
                                     <!--   <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                            <div class="card-block" style="padding-top: 0px !important;">
                                            
                                                <a href="<?=url('course/chat/'.$fc_co_id)?>" data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="bottom" data-trigger="hover" data-title="CMS Guidline"  data-content=" " class="guidline-alert-notification" >
                                                    <img src="{{asset('images/class_discussion.png')}}" width="<?=$image_width;?>"><br>Faculty Notifications
                                                </a>

                                            </div>
                                        </div> --> 
                                        <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                            <div class="card-block" style="padding-top: 0px !important;">
                                            <a     href="{{route('teacher/course_evaluation',$fc_co_idd)}}" data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="bottom" data-trigger="hover" data-title="CMS Guidline"  data-content=" " class="guidline-alert-grading" style="cursor:pointer"  >
                                                <img src="{{asset('images/gradings.png')}}" width="<?=$image_width;?>"><br>Grading
                                            </a>
                                            </div>  
                                        </div>
                                        <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                            <div class="card-block" style="padding-top: 0px !important;">
                                            <a   data-toggle="collapse" href="#multiCollapseExample1<?=$fc_co_idd ;?>" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">
                                                <img src="{{asset('images/pdf.png')}}" width="<?=$image_width;?>"><br>More
                                            </a>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="row"> --}}
                                        <div class="row collapse multi-collapse" id="multiCollapseExample1<?=$fc_co_idd ;?>" border="0" style="padding-top: 10px;	box-shadow: 0px 10px 10px 20px rgba(176, 184, 214, 0.09), 10px 10px 15px -5px rgb(176, 184, 214); margin-top: 20px;background-color: lavender; margin-bottom:0 !important;padding-bottom:0 !important;">

                                        <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                            <div data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="right" data-trigger="hover" data-title="Course Criteria"  data-content="This is an evaluation criteria defined by the instructor of course. " class="card-block"  style="padding-top: 0px !important;   display: inline-block;">
                                            <img src="{{asset('images/crateria.png')}}" width="45px" onclick="openModalCourseCriteria('<?=$rec_teacher_courses->course_cd_name;?>',<?=$rec_teacher_courses->fc_co_id;?>,<?=$rec_teacher_courses->fc_co_course_id;?>)" style="cursor: pointer;">
                                                <br> <p onclick="openModalCourseCriteria('<?=$rec_teacher_courses->course_cd_name;?>',<?=$rec_teacher_courses->fc_co_id;?>,<?=$rec_teacher_courses->fc_co_course_id;?>)" style="cursor: pointer; color:#424e79;">Criteria </img>

                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                            <div  data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="bottom" data-trigger="hover" data-title="Course Timetable"  data-content="Timetable structured for daily, weekly, and monthly schedules. " class="card-block" style="padding-top: 0px !important;">
                                            <!-- <a href="{{route('Timetable',my_simple_crypt($fc_co_id, 'e' ))}}" ><img src="{{asset('images/time.png')}}" width="47px" ><br>Course Time Table </a> -->
                                            <a href="{{route('Timetable',my_simple_crypt($fc_co_id, 'e' ))}}" ><img src="{{asset('images/lecture_schedule1.png')}}" width="47px" ><br>Course Timetable </a>

                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                            <div data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="bottom" data-trigger="hover" data-title="Enrolled Students"  data-content="List of the students enrolled in this course. " class="card-block" style="padding-top: 0px !important;">
                                            <a href="{{route('StudentCourseDetail',my_simple_crypt($fc_co_id, 'e' ))}}" ><img src="{{asset('images/estudents.png')}}" width="60px"><br>Enrolled Students</a>

                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                            <div data-container="body"
     data-toggle="popover"
     data-popover-color="popsuccess"
     data-placement="bottom"
     data-trigger="hover"
     data-title="Assessment Marks"
     data-content="View the assessment marks report for this course." class="card-block" style="padding-top: 0px !important;">
                                            <a target="_blank" href="/assessment_marks_report/<?=$fc_co_id?>"  ><img src="{{asset('images/assesmentMarks.png')}}" width="58px"><br>Assessment Marks</a>

                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                            <div data-container="body"
     data-toggle="popover"
     data-popover-color="popsuccess"
     data-placement="bottom"
     data-trigger="hover"
     data-title="Attendance Details"
     data-content="View course-wise attendance details." class="card-block" style="padding-top: 0px !important;">
                                            <a target="_blank" href="https://cms.bnu.edu.pk/assets/cms/reports/course_wise_attendance_report.php?action=grader&ca_course_id_name='+<?=$fc_co_id?>+'&cwa_report_type=1" >
                                                            <img src="{{asset('images/adetails.png')}}" width="47px">
                                                            <br>Attendance Details
                                                        </a>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6 col-xl-<?=$card_col;?> lecture_icons">
                                            <div data-container="body"
     data-toggle="popover"
     data-popover-color="popsuccess"
     data-placement="bottom"
     data-trigger="hover"
     data-title="Assign TA"
     data-content="Assign or manage the Teaching Assistant for this course." class="card-block" style="padding-top: 0px !important;">
                                                <a href="/tarightsmanagement?co_id=<?=$fc_co_id?>&token=<?=auth()->user()->api_token;?>" target='blank'> <img src="{{asset('images/examTA.png')}}" width="47px"><br>Assign TA</a>
                                            </div>
                                        </div>
                                       
                                        </div>
                                    {{-- </div> --}}
                                @endif
                            @endif
                        </div>
                    </div>
                  <?php
                    endforeach;
                }

        }else{
            ?>
            </div>
 <div class="alert alert-warning" style="margin-top:10px;border:none;">
    <strong>Info!</strong> Honorable Faculty.You have not yet assigned any course(s) in this session
</div>
            <?php
        }
                    ?>

<?php
if(isset($checkApp[0]->count)){
	$checkApp = $checkApp[0]->count;
}else{
	$checkApp = '';
}
?>
<input type="hidden" id="checkAppStatus" value="<?=$checkApp?>">
		
<div class="modal fade" id="welcome-modal2">
        <div class="modal-dialog"  style="max-width:800px !important">
            <div class="modal-content">
                <!-- <div class="modal-header">
                    <h4 class="modal-title">Marks Entry for <span id="assessment_name"></span></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div> -->
                <div class="modal-body"  style="height: 620px; !important "  >
                    <a target="_blank" href="https://www.youtube.com/watch?v=jYc33viQNqA"><img src="assets/mobile-img2.jpeg" width="770px" height="450px"></a>
                    <p style="font-size: 16px; font-family:Futura; text-align: justify;" >
                        Dear Faculty, <br>
                        We have just released a new CMS Mobile App and we are sure you're going to love it. 
                        You can learn more about its features after installing it from <b>Play Store (Android) / App Store (IOS)</b>. <br>
                        If you have any questions please click on <a href="https://staff.bnu.edu.pk/mobile.php?user_name=<?=auth()->user()->username?>">Ask Questions</a>.<br>
                        <!-- For More Guideline watch our video on this link <a href="https://www.youtube.com/watch?v=jYc33viQNqA">Video Link</a> -->
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="close_model" onclick="confirmWelcomeNote2();" class="btn btn-primary" data-dismiss="modal">Acknowledged</button>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="welcome-modal2">
        <div class="modal-dialog" style="max-width:600px !important">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><b>BNU Ramzan Timings Survey<b></h4>
                </div>
                <div class="modal-body" style="height: auto;">
                    <p style="font-size: 16px; font-family:Futura; text-align: justify;">
                        During the Holy month of Ramzan, BNU is considering three options for daily timings:<br><b>Which timing option do you prefer?<b><br>
                    </p>
    
                    <form>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="timing" value="8:30 AM to 4:00 PM" required>
                            <label class="form-check-label">8:30 AM to 4:00 PM</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="timing" value="8:00 AM to 3:30 PM">
                            <label class="form-check-label">8:00 AM to 3:30 PM</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="timing" value="7:30 AM to 3:00 PM">
                            <label class="form-check-label">7:30 AM to 3:00 PM</label>
                        </div>
    
                        <input type="hidden" id="csrf_token" name="_token" value="{{ csrf_token() }}">
                       
                    </form>
                    
                    <p class="text-center text-danger mt-3">Please respond within 48 hours.</p>
                    <div class="text-center">
                        <button type="button" id="ramzan_submit" onclick="submitRamzanResponse();" class="btn btn-success">Submit Response</button>
                    </div>
                </div>
                <div class="modal-footer text-center">
                </div>
            </div>
        </div>
    </div> --}}
    


@section('javascript')
<script type="text/javascript">
   var ramzanSurveyRecord = "<?php echo $ramzan_survey; ?>";
   //RAMZAN TIMIMNG SURVEY POPUP
        // $(document).ready(function() {
        //     if (ramzanSurveyRecord === '') {
        //         $('#welcome-modal2').modal({
        //             backdrop: 'static', // Prevent closing when clicking outside
        //             keyboard: false      // Prevent closing with Esc key
        //         });
        //     }
        
        
        //      load_today_classes();
        //      get_guidline();
        // });
    //END RAMZAN TIMIMNG SURVEY POPUP
    $(document).ready(function() {
        // Modal disabled - uncomment below lines to re-enable
        // if (ramzanSurveyRecord === '') {
        //     $('#welcome-modal2').modal('show');
        // }
        <?php
        $activeNote2 = 'popup';
        $welcomeNotes2 = (array)	json_decode(auth()->user()->welcome_notes);
        if (!(count($welcomeNotes2) > 0   && in_array($activeNote2, $welcomeNotes2))) {
        ?>
            // $('#welcome-modal2').modal('show');
        <?php
        }
        ?>
    
        load_today_classes();
        get_guidline();
    });
function submitRamzanResponse() {
    // Get the selected timing option
    let selectedOption = document.querySelector('input[name="timing"]:checked');
    let csrfToken = document.getElementById('csrf_token').value; // Get CSRF token from hidden input
    // Check if any option is selected
    if (!selectedOption) {
        alert("Please select a timing option before submitting.");
        return;
    }

    // Get the selected value
    let timingValue = selectedOption.value;

    // Send AJAX request
    $.ajax({
        url: "save-ramzan-response",
        type: "POST",
        data: {
            timing: timingValue
        },
        headers: {
            "X-CSRF-TOKEN": csrfToken, // Include CSRF token in headers
           
        },
        success: function(response) {
            $('#welcome-modal2').modal('hide'); // Close modal on success
        },
        error: function() {
            alert("An error occurred. Please try again.");
        }
    });
}



function get_guidline(){
			var key='absolute,relative,schedule,exam,grading,notification,outline';
			sendGetRequest('.hello_btn','.message_div','cms-guidline/get-data?key='+key,function(res){
				if(res){
				$.each(res, function( index, value ) {

					$('.guidline-alert-'+value.key).attr('data-content',value.description)
					$('.guidline-alert-'+value.key).attr('data-original-title',value.title);
                    //$('.guidline-alert-'+value.title).popover();


                    // $( '.guidline-alert-'+value.key ).hover(
				    //         function() {
				    //             $(this).css('cursor','pointer');
				    //             $( this ).append($( '<a data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="bottom" data-trigger="hover" data-title="CMS Guidlineghfhfgh"  data-content=" fghfgh" class="guidline-alert-exam"></a>' ) );
				    //         }, function() {
				    //             $(this).css('cursor','auto');
				    //             $( this ).find( "a" ).last().remove();
				    //         }
				    //         );
				});

			}

	
		});

}
    function load_today_classes(){




                const CheckReload = (() => {
                                    let counter = - 1;
                                    return () => {
                                        counter++;
                                        return counter;
                                    };
                                    })();

                {
                        const refreshId = setInterval(() => {
                            const properID = CheckReload();
                          //  console.log(properID);
                             //   alert(properID);
                              if(properID == '0'){
                                  $("#prog1").hide();
                                  $("#prog3").show();
                            //   }else if(properID == '1'){
                            //     $("#prog2").hide();
                            //     $("#prog3").show();
                            //   }else if(properID == '2'){
                            //     $("#prog3").hide();
                            //     $("#prog4").show();
                            //   }else if(properID == '22'){
                            //     $("#prog4").hide();
                            //     $("#prog5").show();
                              }else if(properID == '2'){
                                $("#prog3").hide();
                                $("#prog5").hide();
                                $("#prog6").show();
                              }
                              if (properID > 1) {
                                clearInterval(refreshId);
                                $.ajax({url: "todaylectures",
                                success: function(result){
                                //    alert(result);
                                $("#today_lectures").html(result);
                                }});
                              }
                              // Start of Ajax code to load Lectures

                               //  End of Ajax code for lectures loading
                               // }
                        },1000
                        );
                }
    }



  function openModalCourseCriteria(get_course_name, co_id, co_course_id) {

    $('#courseCriteriaModal').modal('show');
    $("#show_heading_criteria").html(get_course_name);
    $("#show_heading_criteria").show();
    $("#show_heading_criteria").parent().css('background-color','#6a6a6a');
    $.ajax({
      url: '{{url('teacher/load_criteria')}}/' + co_id + '/' + co_course_id,
      type: "GET",
      success: function(data) {
        //console.log(data);
        $("#load_date").html(data);
      }
    });
  }
  


  
  $('.fileDownload').click(function(e) {
  e.preventDefault();
  const button = $(this);
  const row = button.closest('.col-6, .col-12, .col-10'); // Find the common parent element for the row
  console.log(row);
  const courseCriteriaDetailId = button.data('course_criteria_detail_id');
  const coId = button.data('co_id');
  const coName = button.data('co_name');

  // Disable button and show spinner
  button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...');

  const progressBar = $('<div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="50">Please wait...</div></div>');

  button.after(progressBar);

  const downloadButton = row.find('.downloadZip');
  downloadButton.addClass('invisible');

  const formData = new FormData();
  formData.append('course_criteria_detail_id', courseCriteriaDetailId);
  formData.append('co_id', coId);
  formData.append('co_name', coName);

  $('.fileDownload').prop('disabled', true);

  $.ajax({
    method: "POST",
    url: "/api/download-assignment-file",
    data: formData,
    processData: false,
    contentType: false,
    headers: {
      "Authorization": 'Bearer {{ auth()->user()->api_token }}',
      "Accept": "application/json"
    },
    xhr: function() {
      const xhr = new window.XMLHttpRequest();
      // Add progress event listener to update progress bar
      xhr.upload.addEventListener("progress", function(evt) {
        if (evt.lengthComputable) {
          const percentComplete = evt.loaded / evt.total * 100;
          progressBar.find('.progress-bar').css('width', percentComplete + '%');
        }
      }, false);
      return xhr;
    }
  })
  .done(function(res) {
    if (res.status) {
      // Remove progress bar and enable button
      progressBar.remove();
      button.removeClass('btn-outline-primary').addClass('btn-primary').prop('disabled', false).html(' Generate Again');

      // Show download button in the same row
      downloadButton.attr('href', res.url).removeClass('invisible');
    }
  })
  .fail(function(xhr) {
    if (xhr.status == 422) {
      // Remove progress bar and enable button
      progressBar.remove();
      button.prop('disabled', false).html('Generated Again').addClass('btn-danger');
    }
  })
  .always(function() {
    // Remove progress bar and enable button
    progressBar.remove();
    button.removeClass('btn-outline-primary').addClass('btn-primary').prop('disabled', false).html(' Generate Again');

    $('.fileDownload').prop('disabled', false);
  });
});
function syncLectureOpenState() {
    $('.card.e-panel').each(function () {
        var $card = $(this);
        var isOpen = $card.find('.multi-collapse').hasClass('show');

        $card.toggleClass('lecture-open', isOpen);
    });
}
$(document).ready(function () {

    $('.multi-collapse').removeClass('show');

    $('.multi-collapse').collapse('hide');

    $('.card.e-panel').removeClass('lecture-open');

    syncLectureOpenState();

});

/* When details open */
$(document).on('shown.bs.collapse', '.multi-collapse', function () {
    $(this).closest('.card.e-panel').addClass('lecture-open');
});

/* When details close */
$(document).on('hidden.bs.collapse', '.multi-collapse', function () {
    $(this).closest('.card.e-panel').removeClass('lecture-open');
});

/* Initial state */
$(document).ready(function () {
    syncLectureOpenState();
});

</script>

<script>
    /* When details open */
$(document).on('shown.bs.collapse', '.multi-collapse', function () {

    $(this).closest('.card.e-panel').addClass('lecture-open');

    var targetId = '#' + $(this).attr('id');

    $('a[data-toggle="collapse"][href="' + targetId + '"]')
        .contents()
        .filter(function () {
            return this.nodeType === 3;
        })
        .last()[0].nodeValue = ' Less';
});


/* When details close */
$(document).on('hidden.bs.collapse', '.multi-collapse', function () {

    $(this).closest('.card.e-panel').removeClass('lecture-open');

    var targetId = '#' + $(this).attr('id');

    $('a[data-toggle="collapse"][href="' + targetId + '"]')
        .contents()
        .filter(function () {
            return this.nodeType === 3;
        })
        .last()[0].nodeValue = ' More';
});
    </script>
@stop
