<?php

use App\sessions;

$sess = new sessions();
$sess->startSession();
$color = '#ff6c43';
$login_id = $sess->emp_user_id;
//print_r($course_data->id);
//exit();
?>
@extends('layouts.app',['pageTitle' => 'Lecture Video Upload'])

@section('content')
{{-- @dd("!23d"); --}}
<style>
    .profile-overview-title {
    margin: 0 0 14px;
    font-size: 24px;
    font-weight: 700;
    color: #2d2d2d;
    line-height: 1.2;
}



/* ==========================
   Video Upload Card
========================== */

.card-body{
    background:#ffffff;
    padding:24px !important;
}

/* Form layout */
#video_form{
    width:100%;
}

/* Top toggle buttons */
#addhome,
#addlink{
    float:none !important;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:42px;
    padding:0 18px;
    margin:0 0 20px 10px;
    border:1px solid #17675F !important;
    border-radius:8px;
    background:#ffffff !important;
    color:#17675F !important;
    font-size:14px;
    font-weight:600;
    box-shadow:none !important;
    transition:.2s ease;
}

#addhome{
    margin-left:0;
}

#addhome:hover,
#addlink:hover{
    background:#17675F !important;
    color:#ffffff !important;
}

/* Bootstrap row cleanup */
#video_form .row{
    align-items:flex-end;
    margin-left:-8px;
    margin-right:-8px;
}

#video_form .row > [class*="col-"]{
    padding-left:8px;
    padding-right:8px;
    margin-bottom:18px;
}

/* Labels */
#video_form label{
    display:flex;
    align-items:center;
    gap:8px;
    margin-bottom:8px;
    color:#374151;
    font-size:14px;
    font-weight:600;
}

/* YouTube icon */
#video_form label .fa-youtube{
    font-size:24px !important;
    color:#17675F !important;
    vertical-align:middle;
}

/* Inputs */
#video_form .form-control{
    width:100%;
    min-height:46px;
    padding:10px 14px;
    border:1px solid #D7E0E5;
    border-radius:8px;
    background:#ffffff;
    color:#374151;
    font-size:14px;
    box-shadow:none;
    transition:border-color .2s ease, box-shadow .2s ease;
}

#video_form .form-control:focus{
    border-color:#17675F;
    box-shadow:0 0 0 3px rgba(23,103,95,.10);
    outline:none;
}

#video_form .form-control::placeholder{
    color:#9CA3AF;
}

/* File input */
#home_dir input[type="file"]{
    min-height:46px;
    padding:8px 10px;
    cursor:pointer;
}

/* Upload limitation text */
#home_dir label span{
    color:#DC3545 !important;
    font-size:12px;
    font-weight:500;
    line-height:1.5;
}

/* Upload button column */
#video_form .col-xl-2[align="right"]{
    display:flex;
    flex-direction:column;
    align-items:flex-end;
    justify-content:flex-end;
    margin-top:0 !important;
}

/* Upload button */
#video_form #btn{
    min-width:120px;
    min-height:46px;
    padding:0 22px;
    border:none;
    border-radius:8px;
    background:#17675F;
    color:#ffffff;
    font-size:14px;
    font-weight:600;
    box-shadow:none;
    transition:.2s ease;
}

#video_form #btn:hover{
    background:#12564F;
    transform:translateY(-1px);
}

#video_form #btn:disabled{
    opacity:.6;
    cursor:not-allowed;
    transform:none;
}

/* Success message */
#saved_msg_div_video{
    margin-bottom:8px;
    color:#198754 !important;
    font-size:13px;
    font-weight:600;
}

/* Divider */
.card-body > hr{
    margin:10px 0 22px;
    border:0;
    border-top:1px solid #E5E7EB;
}

/* Progress section */
.card-body > .form-group{
    margin:0;
}

.progress{
    height:10px;
    overflow:hidden;
    border-radius:999px;
    background:#EAF0F2;
    box-shadow:none;
}

.progress-bar{
    height:100%;
    border-radius:999px;
    background:#17675F !important;
    transition:width .3s ease;
}

#uploadStatus{
    margin-top:12px;
    font-size:14px;
}

/* Responsive */
@media(max-width:767px){

    .card-body{
        padding:18px !important;
    }

    #addhome,
    #addlink{
        width:100%;
        margin:0 0 10px;
    }

    #video_form .col-xl-2[align="right"]{
        align-items:stretch;
    }

    #video_form #btn{
        width:100%;
    }
}

.bg-info {
    background-color: #17675f !important;
}

.border-info {
    border-color: #17675f !important;
}

.progress {
    height: 22px;
    overflow: hidden;
    border-radius: 20px;
    background-color: #E5EFED;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.08);
}

.progress-bar {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 20px;
    background: linear-gradient(
        90deg,
        #17675F,
        #24998D
    ) !important;
    color: #ffffff;
    font-size: 12px;
    font-weight: 600;
    transition: width 0.3s ease;
    position: relative;
    overflow: hidden;
}

.progress-bar::after {
    content: "";
    position: absolute;
    top: 0;
    left: -40%;
    width: 40%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.4),
        transparent
    );
    animation: progress-shine 1.2s infinite;
}

@keyframes progress-shine {
    from {
        left: -40%;
    }

    to {
        left: 110%;
    }
}

#uploadStatus {
    margin-top: 10px;
    font-size: 13px;
    font-weight: 500;
    color: #6B7280;
}

.btn-labeled{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:10px 18px;
    border:none;
    border-radius:10px;
    background:#17675F !important;
    color:#fff !important;
    font-size:14px;
    font-weight:600;
    box-shadow:0 4px 12px rgba(23,103,95,.18);
    transition:all .25s ease;
}

.btn-labeled:hover{
    background:#12564F !important;
    color:#fff !important;
    transform:translateY(-2px);
    box-shadow:0 8px 18px rgba(23,103,95,.28);
}

.btn-labeled:focus{
    outline:none;
    box-shadow:0 0 0 4px rgba(23,103,95,.18);
}

.btn-labeled .btn-label{
    display:flex;
    align-items:center;
    justify-content:center;
    width:22px;
    height:22px;
    background:rgba(255,255,255,.15);
    border-radius:50%;
}

.btn-labeled .fa{
    font-size:13px;
}



/* =========================================================
   LECTURE VIDEO UPLOAD — FIGMA STYLE OVERRIDE
   Figma frame: Upload Video (28:1546)

   STYLE ONLY
   No PHP / HTML / JavaScript functionality changed
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');


/* =========================================================
   1. DESIGN TOKENS
   ========================================================= */

:root {
    --video-green: #17675F;
    --video-green-dark: #1F776E;

    --video-text: #252525;
    --video-black: #000000;
    --video-muted: #7A7A7A;

    --video-danger: #E24D4F;

    --video-border: #D9D9D9;
    --video-file-bg: #F1F1F1;
    --video-file-border: #F0F0F0;

    --video-white: #FFFFFF;
}


/* =========================================================
   2. PAGE TYPOGRAPHY
   ========================================================= */

#video_form,
.profile-overview-title {
    font-family: 'Montserrat', sans-serif !important;
}


/* Existing Blade has:
   Lecture Video | Subject Name

   Keep same content/functionality, update visual style.
*/
.profile-overview-title {
    margin: 0 0 16px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 20px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;

    color: #252525 !important;
}


/* =========================================================
   3. MAIN FIGMA WHITE CARD
   Figma:
   width 1087
   height 697
   radius 5
   ========================================================= */

.row > .col-12 > .container-fluid > .card {
    width: 100% !important;

    min-height: 697px !important;

    margin: 0 !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    overflow: hidden !important;
}


.row > .col-12 > .container-fluid > .card > .card-body {
    min-height: 697px !important;

    padding: 25px 20px !important;

    background: #FFFFFF !important;
}


/* =========================================================
   4. FORM
   Figma inner width leaves 20px each side
   ========================================================= */

#video_form {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;
}


/* Stack all form fields full-width like Figma */
#video_form .row {
    width: 100% !important;

    margin: 0 !important;

    display: flex !important;
    flex-direction: column !important;

    align-items: stretch !important;
}


#video_form .row > [class*="col-"] {
    width: 100% !important;
    max-width: 100% !important;

    flex: 0 0 100% !important;

    padding-left: 0 !important;
    padding-right: 0 !important;

    margin-bottom: 20px !important;
}


/* =========================================================
   5. YOUTUBE / COMPUTER SWITCH
   Keep existing JS toggle behavior
   ========================================================= */

#addhome,
#addlink {
    min-width: 0 !important;
    min-height: auto !important;
    height: auto !important;

    margin: 0 28px 25px 0 !important;
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;
    line-height: 1.2 !important;

    color: #252525 !important;

    text-align: left !important;

    transform: none !important;
}


/* Radio-style circle like Figma */
#addhome::before,
#addlink::before {
    content: "";

    display: inline-block;

    width: 11px;
    height: 11px;

    margin-right: 8px;

    vertical-align: -1px;

    background: #FFFFFF;

    border: 1px solid #AFAFAF;
    border-radius: 50%;
}


#addhome:hover,
#addlink:hover {
    background: transparent !important;

    color: #17675F !important;

    box-shadow: none !important;

    transform: none !important;
}


/* =========================================================
   6. LABELS
   Figma = 16px Medium
   ========================================================= */

#video_form label {
    display: block !important;

    margin: 0 0 10px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 500 !important;
    line-height: 1.35 !important;

    color: #000000 !important;
}


/* File limitation text
   Figma red regular 16px
*/
#home_dir label span {
    font-family: 'Montserrat', sans-serif !important;

    font-size: 16px !important;
    font-weight: 400 !important;
    line-height: 1.35 !important;

    color: #E24D4F !important;
}


/* YouTube icon in existing markup */
#video_form label .fa-youtube {
    margin-left: 5px;

    font-size: 18px !important;

    color: #E24D4F !important;
}


/* =========================================================
   7. TEXT INPUTS
   Figma = 40px height / 5px radius
   ========================================================= */

#videolink,
#video_title {
    width: 100% !important;

    height: 40px !important;
    min-height: 40px !important;

    padding: 0 12px !important;

    background: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}


#videolink::placeholder,
#video_title::placeholder {
    color: #A7A7A7 !important;

    opacity: 1;
}


#videolink:focus,
#video_title:focus {
    outline: none !important;

    border-color: #17675F !important;

    box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.08) !important;
}


/* =========================================================
   8. FILE INPUT
   Figma:
   input = 40px
   Choose File = 110 x 29
   ========================================================= */

#file1 {
    width: 100% !important;

    height: 40px !important;
    min-height: 40px !important;

    padding: 5px !important;

    background: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;

    cursor: pointer;
}


#file1::file-selector-button {
    width: 110px;
    height: 29px;

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


/* WebKit fallback */
#file1::-webkit-file-upload-button {
    width: 110px;
    height: 29px;

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
   9. FIELD SPACING
   ========================================================= */

#utube_link,
#home_dir {
    margin-bottom: 20px !important;
}


/* Comment group */
#video_form .row > div:nth-last-of-type(2) {
    margin-bottom: 20px !important;
}


/* Error */
#error {
    margin-top: 6px;

    font-family: 'Montserrat', sans-serif;

    font-size: 12px;
    line-height: 1.4;
}


/* =========================================================
   10. UPLOAD BUTTON
   Exact Figma:
   178 x 50
   bg #1F776E
   radius 5
   18px Medium
   ========================================================= */

#video_form .col-xl-2[align="right"] {
    width: 100% !important;
    max-width: 100% !important;

    flex: 0 0 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    display: flex !important;
    flex-direction: column !important;

    align-items: flex-start !important;
    justify-content: flex-start !important;

    text-align: left !important;
}


#video_form #btn {
    width: 178px !important;
    min-width: 178px !important;

    height: 50px !important;
    min-height: 50px !important;

    margin: 0 !important;
    padding: 0 18px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    background: #1F776E !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 500 !important;
    line-height: 1 !important;

    color: #FFFFFF !important;

    text-align: center !important;

    transition: background-color .2s ease !important;
}


#video_form #btn:hover {
    background: #17675F !important;

    box-shadow: none !important;

    transform: none !important;
}


#video_form #btn:focus {
    outline: none !important;

    box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.12) !important;
}


#video_form #btn:disabled {
    opacity: .6 !important;

    cursor: not-allowed !important;

    transform: none !important;
}


/* =========================================================
   11. SUCCESS MESSAGE
   ========================================================= */

#saved_msg_div_video {
    margin: 0 0 8px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 500 !important;

    color: #17675F !important;
}


/* =========================================================
   12. DIVIDER
   Figma does not show a visible divider
   ========================================================= */

.row > .col-12 > .container-fluid > .card > .card-body > hr {
    margin: 24px 0 16px !important;

    border: 0 !important;

    border-top: 1px solid #EEEEEE !important;
}


/* =========================================================
   13. UPLOAD PROGRESS
   Not visible in initial Figma state.
   JS .show() can still display it while uploading.
   ========================================================= */

.row > .col-12 > .container-fluid > .card .progress {
    display: none;

    width: 100% !important;

    height: 8px !important;

    margin: 0 !important;

    overflow: hidden !important;

    background: rgba(23, 103, 95, 0.10) !important;

    border-radius: 50px !important;

    box-shadow: none !important;
}


.row > .col-12 > .container-fluid > .card .progress-bar {
    height: 100% !important;

    background: #17675F !important;

    border-radius: 50px !important;

    box-shadow: none !important;

    color: transparent !important;

    animation: none !important;
}


/* Remove old shine animation */
.row > .col-12 > .container-fluid > .card .progress-bar::after {
    display: none !important;
}


#uploadStatus {
    margin-top: 8px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-weight: 400 !important;

    color: #7A7A7A !important;
}


/* =========================================================
   14. EXISTING UPLOADED VIDEOS SECTION

   Figma frame 28:1546 shows upload form state only.
   Keep current uploaded-video functionality intact,
   just align it with same visual language.
   ========================================================= */

.row > .col-12 > .container-fluid > .card-footer.bg-info {
    margin-top: 22px !important;

    padding: 0 0 14px !important;

    background: transparent !important;

    border: 0 !important;

    box-shadow: none !important;
}


.row > .col-12 > .container-fluid > .card-footer.bg-info .text-white {
    font-family: 'Montserrat', sans-serif !important;

    font-size: 20px !important;
    font-weight: 600 !important;

    color: #252525 !important;
}


/* Uploaded videos wrapper */
.row > .col-12 > .container-fluid > .card-body.border.bg-light2 {
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;

    box-shadow: none !important;
}


/* =========================================================
   15. EXISTING VIDEO CARDS
   ========================================================= */

.row > .col-12 > .container-fluid
> .card-body.border.bg-light2
.col-xl-6 > .card {

    overflow: hidden !important;

    background: #FFFFFF !important;

    border: 1px solid #EEEEEE !important;
    border-radius: 5px !important;

    box-shadow: none !important;
}


/* Card header */
.row > .col-12 > .container-fluid
> .card-body.border.bg-light2
.col-xl-6 > .card > .card-header {

    min-height: 55px !important;

    padding: 10px 14px !important;

    display: flex !important;

    align-items: center !important;
    justify-content: space-between !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-bottom: 1px solid #EEEEEE !important;
}


/* =========================================================
   16. POST / POSTED / REMOVE BUTTONS
   ========================================================= */

.row > .col-12 > .container-fluid .btn-labeled {
    min-width: 100px !important;

    height: 38px !important;

    padding: 0 14px !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    gap: 7px !important;

    background: #17675F !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 500 !important;

    color: #FFFFFF !important;

    transform: none !important;
}


.row > .col-12 > .container-fluid .btn-labeled:hover {
    background: #125A53 !important;

    box-shadow: none !important;

    transform: none !important;
}


.row > .col-12 > .container-fluid .btn-labeled .btn-label {
    width: auto !important;
    height: auto !important;

    background: transparent !important;

    border-radius: 0 !important;
}


.row > .col-12 > .container-fluid .btn-labeled .fa {
    font-size: 13px !important;
}


/* =========================================================
   17. VIDEO / YOUTUBE PREVIEW
   ========================================================= */

.row > .col-12 > .container-fluid
> .card-body.border.bg-light2
.col-xl-6 > .card > .card-body {

    height: auto !important;
    min-height: 200px !important;

    padding: 12px !important;

    background: #FFFFFF !important;
}


.row > .col-12 > .container-fluid
> .card-body.border.bg-light2
video,

.row > .col-12 > .container-fluid
> .card-body.border.bg-light2
iframe {

    display: block !important;

    width: 100% !important;
    height: 220px !important;

    border: 0 !important;
    border-radius: 5px !important;

    object-fit: cover;
}


/* =========================================================
   18. VIDEO CARD FOOTER
   ========================================================= */

.row > .col-12 > .container-fluid
> .card-body.border.bg-light2
.col-xl-6 > .card > .card-footer {

    padding: 12px 14px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-top: 1px solid #EEEEEE !important;

    font-family: 'Montserrat', sans-serif !important;
}


.row > .col-12 > .container-fluid
> .card-body.border.bg-light2
h6 {

    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;
}


.row > .col-12 > .container-fluid
> .card-body.border.bg-light2
h6 strong {

    font-weight: 500 !important;
}


/* Video links */
.row > .col-12 > .container-fluid
> .card-body.border.bg-light2
.card-footer a {

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;
    font-weight: 500 !important;

    color: #17675F !important;

    text-decoration: underline !important;
}


/* =========================================================
   19. DELETE VIDEO BUTTON
   ========================================================= */

.row > .col-12 > .container-fluid
> .card-body.border.bg-light2
button#del {

    width: 32px !important;
    height: 32px !important;

    margin-bottom: 7px !important;
    padding: 0 !important;

    display: inline-flex !important;

    align-items: center !important;
    justify-content: center !important;

    background: #FEF2F2 !important;

    border: 1px solid #FEE2E2 !important;
    border-radius: 5px !important;

    box-shadow: none !important;
}


.row > .col-12 > .container-fluid
> .card-body.border.bg-light2
button#del img {

    width: 15px !important;
    height: 15px !important;

    object-fit: contain !important;
}


/* =========================================================
   20. RESPONSIVE
   ========================================================= */

@media (max-width: 767px) {

    .profile-overview-title {
        font-size: 18px !important;
    }

    .row > .col-12 > .container-fluid > .card,
    .row > .col-12 > .container-fluid > .card > .card-body {
        min-height: auto !important;
    }

    .row > .col-12 > .container-fluid > .card > .card-body {
        padding: 18px 16px !important;
    }

    #home_dir label span {
        display: block;

        margin-top: 5px;

        font-size: 13px !important;
    }

    #video_form #btn {
        width: 100% !important;
    }

    .row > .col-12 > .container-fluid
    > .card-body.border.bg-light2
    video,

    .row > .col-12 > .container-fluid
    > .card-body.border.bg-light2
    iframe {
        height: 190px !important;
    }

}
    </style>

<div class="row">



    <div class="col-12">
        <div class=" container-fluid" style="padding-left: 0px; padding-right: 0px;">
            <div class="">
                <h4 class="profile-overview-title">Lecture Video | <?=$subjectTitle?></h4>
            </div>
            <div class="card">
            <div class="card-body">



                <form enctype="multipart/form-data" method="post" name="video_form" id="video_form">
                    @csrf

                    @foreach($teacher_courses as $teacher_course)
                    @if($teacher_course->fc_co_id==$fc_co_id)
                    <input type="text" style="display: none;" class="form-control" name="video_mtt_id" id="video_mtt_id" value="<?= $tt_id; ?>">
                    <input type="text" style="display: none;" class="form-control" name="video_lec_no" id="video_lec_no" value="<?= $lec_id; ?>">

                    <input type="text" style="display: none;" class="form-control" name="co_id" id="co_id" value="<?= $fc_co_id; ?>">
                    <input type="text" style="display: none;" class="form-control" name="course_id" id="course_id" value="<?= $teacher_course->fc_co_course_id; ?>">
                    @endif
                    @endforeach
                    <button id="addhome" class="btn btn-info" type="button" onclick="add_home()" style="border: 1px solid black;float: right;">From Work Station</button>
                    <button id="addlink" class="btn btn-info" type="button" onclick="add_utube()" style="display:none;border: 1px solid black;float: right;">Add Youtube</button>
                    <div class="row">

                        <div id="utube_link" class="col-12 col-sm-10 col-md-10 col-lg-10 col-xl-10">
                            <label for="exampleFormControlInput1">
                                Video Link <a href="https://www.youtube.com/"  > <i class="fa fab fa-youtube" style="color:<?= $color; ?>; font-size:30px;"></i></a></label>
                            <input type="text" class="form-control" id="videolink" name="videolink" placeholder="Video Link Here . . .">
                        </div>

                        <div id="home_dir" style="display: none" class="col-12 col-sm-10 col-md-10 col-lg-10 col-xl-10">
                            <label for="exampleFormControlInput1">Upload File<span style="color:red">&nbsp;&nbsp;(File Limit 200 MB) Only 'mkv', 'mp4', 'flv', 'mp2', 'mpv', 'webm', 'm4p','mp3' formats allowed</span></label>
                            <input type="file" class="form-control" name="file1" id="file1">
                            <div id="error"></div>
                        </div>
                      
                        <div class="col-12 col-sm-10 col-md-10 col-lg-10 col-xl-10">
                            <label for="exampleFormControlInput1">Comment</label>
                            <input type="text" class="form-control" id="video_title" name="video_title" placeholder="Title Here . . ." maxlength="40">
                        </div>
                        <br>
                        <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2" align="right" style="margin-top: 2%">
                            <div id="saved_msg_div_video" style="display: none;color: green"><span>Data Upload Successfully</span></div>
                            <button type="button" id="btn" onclick="uploadvideo()" name="btn" class="btn btn-primary">Upload</button>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="form-group">
                            <div class="progress">
                                 <div style="background-color: steelblue" class="progress-bar"></div>
                            </div>     
                            <div id="uploadStatus"></div>
                </div>









            </div> <!-- card body end  -->
</div>


            <div class="card-footer bg-info">
                <div class="text-white"> Upload Videos </div>
            </div>




            

            <div class="card-body  border bg-light2 border-info border-wd-2" style="background-color: #eeeeee;">

            <div class="row">


                <?php

                if (isset($course_data) != '') {
                    foreach ($course_data as $course_mat) {
                        // echo $course_mat->type;
                        if ($course_mat->type == 444) { ?>
                            <?php if ($course_mat->video_status == 0) {
                                $text = 'Post video';
                                $color = 'btn-info';
                            } else {
                                $text = 'Posted';
                                $color = 'btn-success';
                                $rem_color = 'btn-info';
                            } ?>
                            {{--////////////////////////start/////////////////////////////--}}
                            <div class="col-xl-6 col-md-6 col-sm-6">
                                <div><button id="del" type="button" style="border: 0px solid " onclick="dElete(<?= $course_mat->id ?>)"><img id="del" src="{{asset('images/delete.png')}}" width="20" height="20" ; ></button>
                                </div>
                                <div class="card border-success mb-3">

                                    <div class="card-header bg-transparent border-info bg-success" style="background-color:white !important;">

                                        <div style="float:left;">
                                            <div style="font-size:12px; font-weight:bold;"> <button type="button" onclick="add('<?= $fc_co_id ?>','<?= $course_mat->id ?>')" class="btn btn-labeled <?= $color; ?>"><span class="btn-label"> <i class="fa fa-paper-plane"></i></span>
                                                    <?= $text; ?></button></div>
                                        </div>
                                        <?php if ($course_mat->video_status == 1) { ?>
                                            <div style="float:right; font-size:10px;">
                                                <button type="button" onclick="remove('<?= $fc_co_id ?>','<?= $course_mat->id ?>')" class="btn btn-labeled <?= $rem_color; ?>">
                                                    Remove</button>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="card-body" style="height: 200px;">
                                        <video style="width: 328px; height:175px" controls src="<?=url('docs/'.$course_mat->file_name)?>" type='video/x-matroska;mkv,webm, codecs="theora, vorbis"'></video>
                                    </div>

                                    <div class="card-footer bg-transparent border-success">
                                        <div class="row justify-content-left">
                                            <h6 style="padding-left: 12px;"><strong>Title :</strong>{{$course_mat->title}}</h6>
                                        </div>
                                        <div class="card-footer bg-transparent row justify-content-right">
                                            <a href="<?=url('docs/'.$course_mat->file_name)?>"  style="color: black;" align="right">Video Link</a> 
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{--/////////////////////////end//////////////////////////////////////--}}
                        <?php } else if ($course_mat->type == 443) {

                            $is_video = $course_mat->file_original_name;
                            //  $its_link=explode("=",$is_video);
                            $its_link = array_pad(explode("=", $is_video), 2, null);
                            $its_link[1];
                            $its_link2 = array_pad(explode("&", $its_link[1]), 1, null);
                            if ($course_mat->video_status == 0) {
                                $text = 'Post video';
                                $color = 'btn-info';
                            } else {
                                $text = 'Posted';
                                $color = 'btn-success';
                                $rem_color = 'btn-info';
                            }
                        ?>



                            <div class="col-xl-6 col-md-6 col-sm-6">
                                <div><button id="del" type="button" style="border: 0px solid " onclick="dElete(<?= $course_mat->id ?>)"><img  src="{{asset('images/delete.png')}}" width="20" height="20" ; ></button></div>
                                <div class="card border-success mb-3">
                                    <div class="card-header bg-transparent border-info bg-success" style="background-color:white !important;">
                                        <div style="float:left;">
                                            <div style="font-size:12px; font-weight:bold;"><button type="button" onclick="add('<?= $fc_co_id ?>','<?= $course_mat->id ?>')" class="btn btn-labeled <?= $color; ?>"><span class="btn-label"><i class="fa fa-paper-plane"></i></span>
                                                    <?= $text; ?></button></div>
                                        </div>

                                        <?php if ($course_mat->video_status == 1) { ?>
                                            <div style="float:right; font-size:10px;">
                                                <button type="button" onclick="remove('<?= $fc_co_id ?>','<?= $course_mat->id ?>')" class="btn btn-labeled <?= $rem_color; ?>"><span class="btn-label"><i class="fa fa-paper-plane"></i></span>
                                                    Remove</button>
                                            </div>
                                        <?php } ?>

                                    </div>
                                    <div class="card-body " style="height: 200px;">
                                        <iframe id="myCode" gesture="media" allow="autoplay; encrypted-media" class="embed-responsive-item" src="https://www.youtube.com/embed/<?= $its_link2[0]; ?>" allowfullscreen>
                                            <!----//style="display: block;height: 200px;width: 330px;"-->
                                        </iframe>
                                    </div>
                                    <div class="card-footer bg-transparent border-success">
                                        <div class="row justify-content-left">
                                            <h6 style="padding-left: 12px;"><strong>Title :</strong>{{$course_mat->title}}</h6>
                                        </div>
                                        <div class="card-footer bg-transparent row justify-content-right">
                                            <a href="{{$is_video}}" target="_blank" style="color: black;" align="right">Youtube Video Link</a>
                                        </div>
                                    </div>
                                </div>
                            </div>







                <?php  }
                    }
                } ?>

            </div>

            </div> <!-- card body end  -->


        </div> <!-- card end -->
    </div>


</div>


@stop
@section('javascript')
<script type="text/javascript">
    function dElete(id) {
        $("#del").addClass("disabled");
        $('#del').prop('disabled', true);
       // $('#del').prop('disabled', true);
       // alert('ddd');
       /// return false;
        $.ajax({
            method: "GET",
            url: "<?php echo url('uploads/delete_faculity_course_material/'); ?>/" + id,

        }).done(function(res) {
            // console.log(res);
            if (res.status) {
                alert(res.message);
                location.reload();
            }

        }).fail(function(res) {
            console.log(res);
        });
    }






    function add(co_id, id) {

        $.ajax({
            type: 'POST',
            url: '{{url('api/uploads-video/add_faculity_course_material/')}}/' + co_id + "/" + id,
            headers: {
                Authorization: 'Bearer <?=auth()->user()->api_token?>',
                Accept: 'application/json'
            },
            data: 'fd',
            processData: false,
            contentType: false,
            success: function(res) {
                if(res.status){
                    $("#saved_msg2").show();
                }
                setTimeout(function() {
                    $("#saved_msg2").hide()
                    location.reload();

                }, 3000);
            },
            complete: function() {

            }
        });
    }


    function remove(co_id, id) {
        $.ajax({
            type: 'POST',
            url: '<?=url('api/uploads-video/remove_faculity_course_material')?>/'+co_id+'/'+id,
            headers: {
                Authorization: 'Bearer <?=auth()->user()->api_token?>',
                Accept: 'application/json'
                },
           // data: 'fd',
            processData: false,
            contentType: false,
            success: function(res) {
                if(res.status){
                    $("#saved_msg2").show();
                }
        
                setTimeout(function() {
                    $("#saved_msg2").hide();
                     location.reload();

                }, 3000);
            },
            complete: function() {

            }
        });
    }



    function addb(co_id, id) {


        $.ajax({
            method: "post",

            url: '<?=url('/add_faculity_course_material/')?>' + co_id + "/" + id,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                alert(data);
            },
            error: function(xhr, errorThrown, statusText) {

                var err = JSON.parse(xhr.responseText);
                alert(err.message);

            }

        });



    }





    function uploadvideo() {

        $('#post_error').html('');
        var title = $("#video_title").val();
        var file = $("#file1").val();
        var videolink = $("#videolink").val();
        $('#error').html('');
        $('#file1').removeClass('is-invalid');
        $('.msg-div').hide();
        $('.form-control').removeClass('is-invalid');
        var error = 0;

        if (title.trim() == '') {

            error++;
            $('#video_title').addClass('is-invalid');
            $('#error_msg span').html('Please provide some title!');
        }

        var typeArr = ["mkv", "mp4", "flv", "mp2", "mpv", "webm", "m4p","mp3"];

        if (file.trim() != '') {
            var file_size = $('#file1')[0].files[0].size;
               // console.log(file_size+'hhh');
               // return false;524288000 /// 1048576000
                if(file_size > 209715200 ) {

                    console.log(file_size+'hhh');
                    $('#error').html('<span class="text-danger"><b>File size is greater than 200MB</b></span>');

                    $("#error_msg span").html("File size is greater than 200MB");
                    $("#file1").css("border-color","#FF0000");
                    return false;
                } 
            var fileInfoArr = file.split(".");
            var len = fileInfoArr.length;
            var type = fileInfoArr[len - 1];
            var index = typeArr.indexOf(type);
            if (index < 0) {
                error++;
                $('#file1').addClass('is-invalid');
                $('#error').html('<span class="text-danger"><b>Only mkv, mp4 ,flv,mp2,mpv,webm,are allowed !</b></span>');
            }
        }
        if (file.trim() == '' && videolink == '') {
            error++;
            $('#videolink').addClass('is-invalid');
            $('#file1').addClass('is-valid');
            $('#error_msg span').html('Please provide one field!');

        }
     
        if (error > 0) {
            $('#error_msg').show();
            return false;
        }
            var bi_form = document.querySelector('#video_form');
            var fd = new FormData(bi_form);
            // $("#btn").attr("disabled", true);
            $('.progress').show();
            $('#gif').show();
            $('#btn').prop('disabled', true);

            $.ajax({
                beforeSend: function(){
                $(".progress-bar").width('0%');
                //$('#uploadStatus').html('<img src="images/loading.gif"/>');
                },
                xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        console.log(evt.loaded+'=='+evt.total);
                        var percentComplete = ((evt.loaded / evt.total) * 100);
                        var percent = percentComplete.toFixed(2);
                        $(".progress-bar").width(percent + '%');
                        $(".progress-bar").html(percent+'%');
                    }
                }, false);
                return xhr;
                },
                error:function(){
                    $('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');
                },
                type: 'POST',
                // url: '<?=url('/videoupload')?>',
                url: "{{ url('teacher/videoupload') }}",
                data: fd,
                processData: false,
                contentType: false,
                success: function(data) {
                    $('.progress-bar').text('Uploaded');
                    $('.progress-bar').css('width', '100%');
                    $('#success').html('<span class="text-success"><b>' + data.success + '</b></span><br /><br />');
                    $('#success').append(data.image);

                    $("#video_title").val('')
                    $("#videolink").val('')
                    $("#file1").val('')

                    setTimeout(function() {
                        location.reload();
                        // $('.progress').hide();
                        $('#gif').hide();
                        // $('#success').hide();
                    }, 3000);

                },
                error: function(xhr, errorThrown, statusText) {
                    $('.progress-bar').text('0%');
                    $('.progress-bar').css('width', '0%');
                    $('#success').html('<span class="text-danger"><b>' + data.errors + '</b></span>');
                    $("#video_title").val('')
                    $("#videolink").val('')
                    $("#file1").val('')
                },
                complete: function(xhr) {
                    // status.html(xhr.responseText);
                    // alert('Uploaded Successfully');

                }
            });
        
    }

    function add_home() {
        $("#home_dir").show();
        $("#utube_link").hide();
        $("#addlink").show()
        $("#addhome").hide();
    }

    function add_utube() {

        $("#home_dir").hide();
        $("#utube_link").show();
        $("#addlink").hide()
        $("#addhome").show()


    }
</script>
@stop