@extends('layouts.app')
{{-- @dd("123"); --}}
@section('content')
    <style type="text/css">
        .grid_center {
            text-align: center;
        }

        .button_div {
            margin-left: auto;
            margin-right: auto;
            border: solid 2px #6f42c1;
        }

        .button_size {
            /* height: 30px; */
            width: 32%;
            font-size: 14px;
            margin: 2px;
            float: left;
        }

        .criteria_sub_div {
            float: left;
            padding: 0px;
        }
        h3 {
            color: #252525;
font-size: 18px;
font-style: normal;
font-weight: 500;
        }
        /* =========================================================
   CMS VIDEO TUTORIALS — FIGMA STYLE OVERRIDE
   Figma frame: Video Tutorials (203:1035)

   STYLE ONLY:
   - No element hidden
   - No element added
   - No HTML / Blade / video functionality changed
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');


/* =========================================================
   1. SCREEN TOKENS
   ========================================================= */

.e-panel {
    --cvt-green: #17675F;
    --cvt-green-dark: #1F776E;

    --cvt-text: #252525;
    --cvt-muted: #45556C;

    --cvt-white: #FFFFFF;
    --cvt-border: rgba(23, 103, 95, 0.08);

    font-family: 'Montserrat', sans-serif !important;
}


.e-panel,
.e-panel * {
    font-family: 'Montserrat', sans-serif;
}


/* =========================================================
   2. OUTER STRUCTURE
   Existing Blade structure remains intact
   ========================================================= */

.e-panel {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;
}


/* Current page outer row */
.row.flex-lg-nowrap[style*="margin-left: 30px"] {
    margin-left: 30px !important;
    margin-right: 30px !important;
}


/* =========================================================
   3. MAIN HEADING
   Figma:
   CMS Video Tutorials
   20px / SemiBold
   ========================================================= */

.e-panel h1 {
    margin-top: 0 !important;
    margin-right: 0 !important;
    margin-bottom: 28px !important;
    margin-left: 12px !important;

    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 20px !important;
    font-style: normal !important;
    font-weight: 600 !important;
    line-height: 1.25 !important;

    color: #252525 !important;
}


/* =========================================================
   4. VIDEO GRID
   Figma = three 350px cards across content area
   ========================================================= */

.e-panel > div > .row:nth-of-type(2) {
    display: flex !important;
    flex-wrap: wrap !important;

    align-items: stretch !important;

    margin-left: -6px !important;
    margin-right: -6px !important;

    row-gap: 19px !important;
}


/* Existing Bootstrap col-lg-4 stays present */
.e-panel > div > .row:nth-of-type(2) > .col-lg-4 {
    padding-left: 6px !important;
    padding-right: 6px !important;

    margin-bottom: 0 !important;
}


/* =========================================================
   5. EACH VIDEO CARD
   Figma:
   white
   10px radius
   350px wide
   no heavy border/shadow
   ========================================================= */

.e-panel .col-lg-4 > .card {
    width: 100% !important;
    height: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    overflow: hidden !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 10px !important;

    box-shadow: none !important;

    transition:
        transform .18s ease,
        box-shadow .18s ease !important;
}


/* Keep a subtle interaction without changing structure */
.e-panel .col-lg-4 > .card:hover {
    transform: translateY(-2px) !important;

    box-shadow:
        0 6px 18px rgba(15, 23, 42, 0.05) !important;
}


/* Existing nested divs */
.e-panel .col-lg-4 > .card > div,
.e-panel .col-lg-4 .wrapper {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    background: transparent !important;
}


/* =========================================================
   6. VIDEO AREA
   Exact Figma media height = 200px
   Existing controls stay visible/functioning
   ========================================================= */

.e-panel .wrapper video {
    display: block !important;

    width: 100% !important;
    height: 200px !important;

    margin: 0 !important;
    padding: 0 !important;

    object-fit: cover !important;

    background: #000000 !important;

    border: 0 !important;
    border-radius: 10px 10px 0 0 !important;

    box-shadow: none !important;
}


/* Video keyboard focus */
.e-panel .wrapper video:focus {
    outline: 2px solid rgba(23, 103, 95, 0.30) !important;
    outline-offset: -2px !important;
}


/* =========================================================
   7. VIDEO TITLE AREA
   Figma bottom panel = 115px
   ========================================================= */

.e-panel .wrapper h3 {
    width: 100% !important;

    min-height: 115px !important;

    margin: 0 !important;

    padding-top: 30px !important;
    padding-right: 15px !important;
    padding-bottom: 24px !important;
    padding-left: 15px !important;

    background: #FFFFFF !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-style: normal !important;
    font-weight: 500 !important;
    line-height: 28px !important;

    color: #252525 !important;

    text-align: left !important;

    border: 0 !important;
    border-radius: 0 0 10px 10px !important;

    box-shadow: none !important;
}


/* =========================================================
   8. LONG TITLES
   Existing text remains complete
   ========================================================= */

.e-panel .wrapper h3 {
    overflow-wrap: anywhere !important;
    word-break: normal !important;
}


/* =========================================================
   9. REMOVE BOOTSTRAP VISUAL DECORATION ONLY
   Structure remains present
   ========================================================= */

.e-panel .card.border {
    border: 0 !important;
}


.e-panel .shadow-none {
    box-shadow: none !important;
}


/* =========================================================
   10. DESKTOP
   Figma = 3 equal columns
   ========================================================= */

@media (min-width: 992px) {

    .e-panel
    > div
    > .row:nth-of-type(2)
    > .col-lg-4 {

        flex: 0 0 33.333333% !important;

        width: 33.333333% !important;
        max-width: 33.333333% !important;
    }

}


/* =========================================================
   11. LARGE DESKTOP
   Keep cards visually close to Figma 350px proportion
   ========================================================= */

@media (min-width: 1200px) {

    .e-panel .wrapper video {
        height: 200px !important;
    }

}


/* =========================================================
   12. TABLET
   All existing videos remain visible
   ========================================================= */

@media (min-width: 768px) and (max-width: 991.98px) {

    .e-panel
    > div
    > .row:nth-of-type(2)
    > .col-lg-4 {

        flex: 0 0 50% !important;

        width: 50% !important;
        max-width: 50% !important;
    }


    .e-panel .wrapper video {
        height: 210px !important;
    }

}


/* =========================================================
   13. MOBILE
   Nothing hidden — cards simply stack
   ========================================================= */

@media (max-width: 767.98px) {

    .row.flex-lg-nowrap[style*="margin-left: 30px"] {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }


    .e-panel h1 {
        margin-left: 0 !important;
        margin-bottom: 20px !important;

        font-size: 20px !important;
    }


    .e-panel > div > .row:nth-of-type(2) {
        margin-left: 0 !important;
        margin-right: 0 !important;

        row-gap: 16px !important;
    }


    .e-panel
    > div
    > .row:nth-of-type(2)
    > .col-lg-4 {

        flex: 0 0 100% !important;

        width: 100% !important;
        max-width: 100% !important;

        padding-left: 0 !important;
        padding-right: 0 !important;
    }


    .e-panel .wrapper video {
        width: 100% !important;

        height: auto !important;

        aspect-ratio: 16 / 9;
    }


    .e-panel .wrapper h3 {
        min-height: auto !important;

        padding: 20px 15px !important;

        font-size: 17px !important;
        line-height: 26px !important;
    }

}
    </style>
    <!-- Grid row -->
    <div class="row flex-lg-nowrap" style="
    margin-left: 30px;
    margin-right: 30px;
">
        <div class="col-12">
            <div class="row flex-lg-nowrap">
                <div class="col-12 mb-3">
                    <div class="e-panel">
                        <div class="">
                            <div class="row">
                                <h1 style="margin-bottom: 15px;margin-left:12px;
                                color: #252525;
font-size: 20px;
font-style: normal;
font-weight: 600;">CMS Video Tutorials</h1>

                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="card border p-0 shadow-none">
                                        <div class="">

                                            <div class="wrapper">
                                                <video style="width:100%;" poster="../../assets/staffimages/poster.jpg" controls>
                                                    <source src="../../assets/staffimages/Login.mp4" type="video/mp4">
                                                    </video>
                                                    <h3 style="text-align: left;
    padding: 30px 15px;"> Login and Dashboard </h3>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border p-0 shadow-none">
                                        <div class="">

                                            <div class="wrapper">
                                                <video style="width:100%;" poster="../../assets/staffimages/poster.jpg" controls>
                                                    <source src="../../assets/staffimages/outline.mp4" type="video/mp4">
                                                        
                                                        
                                                    </video>
                                                    <h3 style="text-align: left;
    padding: 30px 15px;">Course Outline </h3>

                                            </div>

                                        </div>



                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border p-0 shadow-none">
                                        <div class="">

                                            <div class="wrapper">
                                                <video style="width:100%;" poster="../../assets/staffimages/poster.jpg" controls>
                                                    <source src="../../assets/staffimages/schedule.mp4" type="video/mp4">
                                                        
                                                        
                                                    </video>
                                                    <h3 style="text-align: left;
    padding: 30px 15px;">Lecture Schedule </h3>

                                            </div>

                                        </div>



                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border p-0 shadow-none">
                                        <div class="">

                                            <div class="wrapper">
                                                <video style="width:100%;" poster="../../assets/staffimages/poster.jpg" controls>
                                                    <source src="../../assets/staffimages/notification.mp4"
                                                    type="video/mp4">
                                                    
                                                    
                                                </video>
                                                <h3 style="text-align: left;
    padding: 30px 15px;">Faculty Notification </h3>

                                            </div>

                                        </div>



                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border p-0 shadow-none">
                                        <div class="">

                                            <div class="wrapper">
                                                <video style="width:100%;" poster="../../assets/staffimages/poster.jpg" controls>
                                                    <source src="../../assets/staffimages/details.mp4" type="video/mp4">
                                                        
                                                        
                                                    </video>
                                                    <h3 style="text-align: left;
    padding: 30px 15px;">Course Details </h3>

                                            </div>

                                        </div>



                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border p-0 shadow-none">
                                        <div class="">

                                            <div class="wrapper">
                                                <video style="width:100%;" poster="../../assets/staffimages/poster.jpg" controls>
                                                    <source src="../../assets/staffimages/grading process.mp4"
                                                    type="video/mp4">
                                                    
                                                </video>
                                                <h3 style="text-align: left;
    padding: 30px 15px;">Grading Process Details </h3>

                                            </div>

                                        </div>



                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border p-0 shadow-none">
                                        <div class="">
                                            <div class="wrapper">
                                                <video style="width:100%;" poster="../../assets/staffimages/poster.jpg" controls>
                                                    <source src="../../assets/staffimages/CMS-BNU.mp4" type="video/mp4">
                                                    </video>
                                                    <h3 style="text-align: left;
    padding: 30px 15px;">BNU CMS </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border p-0 shadow-none">
                                        <div class="">
                                            <div class="wrapper">
                                                <video style="width:100%;" poster="../../assets/staffimages/poster.jpg" controls>
                                                    <source src="../../assets/staffimages/details icon.mp4"
                                                    type="video/mp4">
                                                </video>
                                                <h3 style="text-align: left;
    padding: 30px 15px;">Details Icon</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border p-0 shadow-none">
                                        <div class="">
                                            <div class="wrapper">
                                                <video style="width:100%;" poster="../../assets/staffimages/poster.jpg" controls>
                                                    <source src="../../assets/staffimages/Exam Marking.mp4"
                                                    type="video/mp4">
                                                </video>
                                                <h3 style="text-align: left;
    padding: 30px 15px;">Exam Marking </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border p-0 shadow-none">
                                        <div class="">
                                            <div class="wrapper">
                                                <video style="width:100%;" poster="../../assets/staffimages/poster.jpg" controls>
                                                    <source src="../../assets/staffimages/Today's lecture panel.mp4"
                                                    type="video/mp4">
                                                </video>
                                                <h3 style="text-align: left;
    padding: 30px 15px;">Today's lecture panel</h3>
                                              </br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border p-0 shadow-none">
                                        <div class="">
                                            <div class="wrapper">
                                                <video style="width:100%;" poster="../../assets/staffimages/poster.jpg" controls>
                                                    <source src="../../assets/staffimages/Combine Grading.mp4"
                                                    type="video/mp4">
                                                </video>
                                                <h3 style="text-align: left;
    padding: 30px 15px;">Combine Grading For Same Course with different Section</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Form Modal -->

        </div>
    </div>
@stop
