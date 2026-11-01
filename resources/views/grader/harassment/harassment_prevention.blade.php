@extends('layouts.app')
@section('content')

{{-- @dd("123"); --}}
    <style>
        .video-container {
            margin-bottom: 30px;
        }
        .video-title {
            color: #4454C3;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .video-card {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .video-card:hover {
            transform: translateY(-5px);
        }
        .section-title {
            color: #4454C3;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 2px solid #4454C3;
        }
        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
        }
        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }


        /* =========================================================
   HARASSMENT PREVENTION VIDEOS
   FIGMA STYLE OVERRIDE ONLY

   Figma visual reference:
   Video Tutorials - 203:1035

   IMPORTANT:
   - Nothing is hidden
   - Nothing is removed
   - Video controls remain visible
   - Existing HTML / Blade structure remains unchanged
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');


/* =========================================================
   1. DESIGN TOKENS
   ========================================================= */

:root {
    --hp-green: #17675F;
    --hp-green-dark: #1F776E;

    --hp-text: #252525;
    --hp-muted: #45556C;

    --hp-white: #FFFFFF;
    --hp-border: #E5E7EB;

    --hp-page-green: rgba(23, 103, 95, 0.15);
    --hp-page-white: rgba(255, 255, 255, 0.15);
}


/* =========================================================
   2. PAGE TYPOGRAPHY
   ========================================================= */

.e-panel,
.e-panel *,
.section-title,
.video-card,
.video-card * {
    font-family: 'Montserrat', sans-serif !important;
}


/* =========================================================
   3. OUTER PAGE WRAPPER
   Keep existing structure, just remove old visual treatment
   ========================================================= */

.e-panel.card {
    width: 100% !important;

    margin: 0 !important;

    background: transparent !important;

    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;
}


.e-panel.card > .card-body {
    width: 100% !important;

    padding: 0 !important;

    background: transparent !important;
}


/* =========================================================
   4. PAGE HEADING
   Figma:
   Montserrat SemiBold
   20px
   left aligned
   ========================================================= */

.section-title {
    margin: 0 0 26px 0 !important;

    padding: 0 !important;

    border: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 20px !important;
    font-weight: 600 !important;
    line-height: 1.25 !important;

    color: #252525 !important;

    text-align: left !important;
}


/* =========================================================
   5. VIDEO GRID
   Existing Bootstrap row remains.
   Figma desktop shows 3 cards in one row.
   ========================================================= */

.e-panel .card-body > .row {
    display: flex !important;

    flex-wrap: wrap !important;

    align-items: stretch !important;

    margin-left: -10px !important;
    margin-right: -10px !important;
}


/* Existing col-xl-6 / col-lg-6 remain in HTML.
   CSS only updates their visual width on large screens. */
.video-container {
    margin-bottom: 20px !important;

    padding-left: 10px !important;
    padding-right: 10px !important;
}


@media (min-width: 1200px) {

    .video-container {
        flex: 0 0 33.333333% !important;

        width: 33.333333% !important;
        max-width: 33.333333% !important;
    }

}


/* =========================================================
   6. VIDEO CARD
   Figma card:
   white
   radius 10px
   no heavy shadow
   ========================================================= */

.video-card {
    width: 100% !important;
    height: 100% !important;

    margin: 0 !important;

    overflow: hidden !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 10px !important;

    box-shadow: none !important;

    transition:
        transform .2s ease,
        box-shadow .2s ease !important;
}


/* Keep existing hover functionality,
   just make it more subtle like Figma */
.video-card:hover {
    transform: translateY(-2px) !important;

    box-shadow:
        0 6px 18px rgba(15, 23, 42, 0.06) !important;
}


/* =========================================================
   7. CARD BODY
   Current Bootstrap padding removed so media can reach edges
   ========================================================= */

.video-card > .card-body {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    background: #FFFFFF !important;

    border-radius: 10px !important;

    overflow: hidden !important;
}


/* =========================================================
   8. EXISTING .wrapper
   Keep it fully visible.
   ========================================================= */

.video-card .wrapper {
    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;

    background: #FFFFFF !important;

    border-radius: 10px !important;

    overflow: hidden !important;
}


/* =========================================================
   9. VIDEO
   Figma media area = approx 200px
   Current controls remain completely intact.
   ========================================================= */

.video-card .wrapper video {
    display: block !important;

    width: 100% !important;
    height: 200px !important;

    margin: 0 !important;

    object-fit: cover !important;

    background: #000000 !important;

    border: 0 !important;
    border-radius: 10px 10px 0 0 !important;
}


/* =========================================================
   10. VIDEO TITLE
   Existing h3 stays visible.
   Override current inline 30px 15px padding.
   Figma title = 18px Medium
   ========================================================= */

.video-card .wrapper h3 {
    min-height: 94px !important;

    margin: 0 !important;

    padding: 24px 15px !important;

    display: flex !important;

    align-items: flex-start !important;

    background: #FFFFFF !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 500 !important;
    line-height: 1.55 !important;

    color: #252525 !important;

    text-align: left !important;

    border-radius: 0 0 10px 10px !important;
}


/* =========================================================
   11. VIDEO FOCUS
   Preserve browser controls/accessibility
   ========================================================= */

.video-card video:focus {
    outline: 2px solid rgba(23, 103, 95, 0.35) !important;

    outline-offset: -2px !important;
}


/* =========================================================
   12. CURRENT VIDEO-CONTAINER SPACING
   ========================================================= */

.video-container:last-child {
    margin-bottom: 20px !important;
}


/* =========================================================
   13. TABLET
   Keep all content visible.
   Two cards per row.
   ========================================================= */

@media (min-width: 768px) and (max-width: 1199.98px) {

    .video-container {
        flex: 0 0 50% !important;

        width: 50% !important;
        max-width: 50% !important;
    }


    .video-card .wrapper video {
        height: 210px !important;
    }

}


/* =========================================================
   14. MOBILE
   Keep all three videos visible in one-column layout.
   ========================================================= */

@media (max-width: 767.98px) {

    .section-title {
        margin-bottom: 20px !important;

        font-size: 20px !important;
        line-height: 1.35 !important;
    }


    .video-container {
        flex: 0 0 100% !important;

        width: 100% !important;
        max-width: 100% !important;

        margin-bottom: 16px !important;

        padding-left: 0 !important;
        padding-right: 0 !important;
    }


    .e-panel .card-body > .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }


    .video-card .wrapper video {
        height: auto !important;

        aspect-ratio: 16 / 9;
    }


    .video-card .wrapper h3 {
        min-height: auto !important;

        padding: 18px 15px !important;

        font-size: 16px !important;
    }

}


/* =========================================================
   15. SMALL MOBILE
   ========================================================= */

@media (max-width: 480px) {

    .section-title {
        font-size: 18px !important;
    }


    .video-card .wrapper h3 {
        padding: 16px 14px !important;

        font-size: 15px !important;
    }

}
    </style>

    <div class="row flex-lg-nowrap">
        <div class="col-12">
            <div class="row flex-lg-nowrap" style="
   
">
                <div class="col-12 mb-3">
                    <div class="e-panel card">
                        <div class="card-body pb-2">
                            <h1 class="section-title">Harassment Prevention Awareness Videos</h1>
                            
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 video-container">
                                    <div class="card video-card">
                                        <div class="card-body">
                                            
                                            {{-- <div class="video-wrapper">
                                                <iframe src="https://www.youtube.com/embed/5w2vXgzX43Y" 
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                        allowfullscreen></iframe>
                                            </div> --}}
                                                <div class="wrapper">
                                                <video style="width:100%;" poster="../../assets/staffimages/poster.jpg" controls>
                                                    <source src="../../assets/staffimages/one.mp4" type="video/mp4">
                                                    </video>
                                                    <h3 style="text-align: left;
                                      padding: 30px 15px;"> The Social Impact of Harassment and How to Stop It </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 video-container">
                                    <div class="card video-card">
                                        <div class="card-body">
                                           
                                            {{-- <div class="video-wrapper"> --}}
                                                {{-- <iframe src="https://www.youtube.com/embed/vbMzHvj_mzo" 
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                        allowfullscreen></iframe> --}}
                                                          <div class="wrapper">
                                                <video style="width:100%;" poster="../../assets/staffimages/poster.jpg" controls>
                                                    <source src="../../assets/staffimages/two.mp4" type="video/mp4">
                                                    </video>
                                                    <h3 style="text-align: left;
    padding: 30px 15px;"> The Social Impact of Harassment and How to Stop It </h3>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                {{-- </div> --}}

                                <div class="col-xl-6 col-lg-6 video-container">
                                    <div class="card video-card">
                                        <div class="card-body">
                                           
                                            {{-- <div class="video-wrapper"> --}}
                                                {{-- <iframe src="https://www.youtube.com/embed/YdzawWYkKRc" 
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                        allowfullscreen></iframe> --}}

                                                               <div class="wrapper">
                                                <video style="width:100%;" poster="../../assets/staffimages/poster.jpg" controls>
                                                    <source src="../../assets/staffimages/three.mp4" type="video/mp4">
                                                    </video>
                                                    <h3 style="text-align: left;
    padding: 30px 15px;"> The Social Impact of Harassment and How to Stop It </h3>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop