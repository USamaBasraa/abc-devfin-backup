{{-- @dd("1234"); --}}

<style type="text/css">
    .chekcdiv {
        border: solid 2px red !important;
        width: 100% !important;
    }

    .card-custom-icon {
        position: unset !important;
        right: 0px !important;
        top: 0px !important;
        width: 35px;
        height: 30px;
        color: #e5e9f2;
        fill: #2dce89 !important;
    }

    .content {
        white-space: nowrap;
        width: 80%;
        overflow: hidden;
        text-overflow: ellipsis;
        border: 1px solid #000000;
    }

    @-webkit-keyframes blinker {
        from {
            opacity: 1.0;
        }

        to {
            opacity: 0.0;
        }
    }

    .blink {
        text-decoration: blink;
        -webkit-animation-name: blinker;
        -webkit-animation-duration: 0.6s;
        -webkit-animation-iteration-count: infinite;
        -webkit-animation-timing-function: ease-in-out;
        -webkit-animation-direction: alternate;
    }

    .current-lecture-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 10px;
    }

    .current-lecture-header .profile-overview-title {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
        color: #292929;
    }

    .current-attendance-link {
        color: #2d2d2d;
        font-size: 14px;
        font-weight: 500;
        text-decoration: underline;
        cursor: pointer;
        white-space: nowrap;
    }

    .current-attendance-link:hover {
        color: #17675f;
        text-decoration: underline;
    }

    @media (max-width: 576px) {
        .current-lecture-header {
            align-items: flex-start;
        }

        .current-lecture-header .profile-overview-title {
            font-size: 19px;
        }

        .current-attendance-link {
            font-size: 13px;
        }
    }

    /* ===========================
   Current Lecture Card
=========================== */

    .card-aside {
        background: #fff !important;
        border: none !important;
        border-radius: 12px !important;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .05) !important;
        overflow: hidden;
    }

    /* Remove green strip look */
    .card-aside-column {
        background: transparent !important;
        min-width: 0 !important;
        flex: 0 0 0 !important;
        padding: 0 !important;
        display: none !important;
        /* remove left strip completely */
    }

    /* Main body */
    .card-aside>.card-body {
        background: #fff !important;
        padding: 0 !important;
        border-radius: 12px !important;
    }

    /* Inner card */
    .card-aside .card {
        border: none !important;
        border-radius: 12px !important;
        box-shadow: none !important;
    }

    /* Course title */
    .card-aside h4 {
        color: #2F3D63 !important;
        font-weight: 600 !important;
    }

    /* Statistics section */
    #content {
        /* border-top: 1px solid #ececec !important; */
    }

    #content .row {
        padding: 12px 10px;
    }

    /* Metric icons */
    .fs-40 {
        font-size: 34px !important;
    }

    .icon-dropshadow-success,
    .icon-dropshadow-primary,
    .icon-dropshadow-danger {
        text-shadow: none !important;
    }

    /* Lecture content section */
    .card1 {
        /* background: #17675F1A !important; */
        border-radius: 8px !important;
        margin: 10px 12px !important;
    }

    .card1 .card-body {
        padding: 16px 18px !important;
    }

    .card1 h4 {
        font-size: 16px !important;
        margin-bottom: 10px !important;
    }

    /* Footer actions */
    .card-footer {
        background: #17675F1A !important;
        border-top: none !important;
        padding: 18px 10px !important;
    }

    .lecture_icons {
        text-align: center;
    }

    .lecture_icons img {
        width: 32px !important;
        height: auto;
        transition: .2s;
    }

    .lecture_icons img:hover {
        transform: translateY(-2px);
    }

    .lecture_icons a {
        color: #444 !important;
        text-decoration: none;
        font-size: 14px !important;
        font-weight: 500;
    }

    /* Remove heavy borders */
    .card-body {
        border: none !important;
    }

    /* Remaining time */
    .blink1 {
        color: #444 !important;
        font-weight: 600 !important;
    }

    /* Class Attendance */
    .btn-success1 {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .btn-success1:hover {
        background: transparent !important;
    }

    .btn-success1 svg {
        display: none !important;
    }

    .btn-success1 span {
        color: #2F3D63 !important;
        font-size: 15px;
        text-decoration: underline;
    }

    /* Divider */
    .card-footer {
        border-top: 1px solid #E6E6E6 !important;
    }

    /* Lecture Content + Footer = One Section */

    .card1 {
        /* background: #17675F1A !important; */
        border-radius: 8px 8px 0 0 !important;
        margin: 12px 12px 0 12px !important;
        box-shadow: none !important;
    }

    .card1 .card-body {
        background: #17675F1A !important;
        padding: 14px 18px !important;
    }

    /* Footer same background */
    .card-footer {
        background: #17675F1A !important;
        margin: 0 12px 18px 12px !important;
        border: none !important;
        border-radius: 0 0 8px 8px !important;
        padding: 16px 10px !important;
    }

    /* Remove gap between both sections */
    .card1+.card-footer {
        margin-top: 0 !important;
    }

    /* Remove any white strip */
    .card-footer .row {
        margin: 0 !important;
    }

    /* Heading */
    .card1 h4 {
        margin-bottom: 8px !important;
        font-size: 16px;
        font-weight: 600;
    }

    /* Statistics Row */
    #content>.row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        padding: 6px 8px 18px;
        margin: 0;
    }

    /* Each Item */
    #content>.row>div {
        display: flex !important;
        align-items: center;
        flex: 1;
        min-width: 180px;
        padding: 8px 14px !important;
    }

    /* Icons */
    #content .fs-40 {
        font-size: 30px !important;
        margin-right: 16px !important;
        padding-left: 0 !important;
    }

    /* Remove icon shadows */
    #content .icon-dropshadow-success,
    #content .icon-dropshadow-primary,
    #content .icon-dropshadow-danger {
        text-shadow: none !important;
    }

    /* Remove mt-5 spacing */
    #content .mt-5 {
        margin-top: 0 !important;
    }

    /* Heading */
    #content h6 {
        margin: 0 0 6px;
        font-size: 14px;
        font-weight: 600;
        color: #2F3D63;
    }

    /* Numbers */
    #content h4 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #2F3D63;
    }

    /* Class Time */
    #content p {
        margin: 0;
        font-size: 13px !important;
        font-weight: 600;
        color: #444;
    }

    /* Remaining Time */
    #content .blink1 {
        color: #444 !important;
        font-size: 18px !important;
        font-weight: 700 !important;
    }

    /* Remaining Class Time heading */
    #content .blink1+h6 {
        margin-top: 4px;
    }

    /* Responsive */
    @media (max-width:991px) {

        #content>.row>div {
            flex: 0 0 50%;
            max-width: 50%;
        }

    }

    @media (max-width:576px) {

        #content>.row>div {
            flex: 0 0 100%;
            max-width: 100%;
        }

    }

    .card-body[id^="content"]>.row .mdi::before {
        content: "" !important;
        display: none !important;
    }



/* Statistics row: left to right with equal spacing */
.card-body[id^="content"] > .row {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: nowrap !important;
    gap: 24px;
    margin: 0 !important;
    padding: 18px 24px 22px !important;
}

/* Each statistic block */
.card-body[id^="content"] > .row > div {
    flex: 0 1 auto !important;
    width: auto !important;
    max-width: none !important;
    min-width: 150px;
    padding: 0 !important;
    display: flex !important;
    align-items: flex-start !important;
}

/* Last block needs more width */
.card-body[id^="content"] > .row > div:last-child {
    min-width: 220px;
}

/* Remove old MDI icon space */
.card-body[id^="content"] > .row > div > .mdi {
    display: none !important;
}

/* Remove Bootstrap mt-5 */
.card-body[id^="content"] > .row .mt-5 {
    margin-top: 0 !important;
}

/* Labels */
.card-body[id^="content"] > .row h6 {
    margin: 0 0 8px !important;
    font-size: 16px !important;
    font-weight: 500 !important;
    color: #292929 !important;
    white-space: nowrap;
}

/* Value line */
.card-body[id^="content"] > .row h4,
.card-body[id^="content"] > .row p {
    margin: 0 !important;
    display: flex;
    align-items: center;
    white-space: nowrap;
    color: #444 !important;
}

/* Images beside values */
.card-body[id^="content"] > .row h4 img,
.card-body[id^="content"] > .row p img {
    width: 22px !important;
    height: 22px !important;
    margin-right: 10px !important;
    object-fit: contain;
}

/* Number styling */
.card-body[id^="content"] > .row h4 {
    font-size: 16px !important;
    font-weight: 500 !important;
}

/* Class time */
.card-body[id^="content"] > .row p {
    font-size: 14px !important;
    font-weight: 500 !important;
}

/* Remaining time block */
.card-body[id^="content"] > .row .blink1 {
    font-size: 14px !important;
    color: #444 !important;
    font-weight: 500 !important;
}

/* Responsive */
@media (max-width: 1100px) {
    .card-body[id^="content"] > .row {
        flex-wrap: wrap !important;
    }

    .card-body[id^="content"] > .row > div {
        flex: 0 0 calc(50% - 12px) !important;
        min-width: 0;
    }
}

@media (max-width: 576px) {
    .card-body[id^="content"] > .row > div {
        flex: 0 0 100% !important;
    }
}

/* Statistics row */
.card-body[id^="content"] > .row{
    display:flex !important;
    flex-wrap:nowrap !important;
    justify-content:space-between !important;
    align-items:flex-start !important;
    margin:0 !important;
    padding:0 !important;
        padding:10px 16px !important;

}

/* Override Bootstrap column widths */
.card-body[id^="content"] > .row > [class*="col-"]{
    flex:1 1 0 !important;
    max-width:none !important;
    width:auto !important;
    padding:0 8px !important;
}

/* First item left aligned */
.card-body[id^="content"] > .row > [class*="col-"]:first-child{
    text-align:left;
}

/* Middle items centered */
.card-body[id^="content"] > .row > [class*="col-"]:not(:first-child):not(:last-child){
    text-align:center;
}

/* Last item fully right aligned */
.card-body[id^="content"] > .row > [class*="col-"]:last-child{
    text-align:right;
}

.card-body[id^="content"] > .row > [class*="col-"]:last-child > div{
    margin-left:auto;
}

/* Remove mt-5 */
.card-body[id^="content"] .mt-5{
    margin-top:0 !important;
}

/* Hide old mdi icons if using images */
.card-body[id^="content"] .mdi{
    display:none !important;
}

</style>
<style type='text/css'>
    /*This will work for chrome */
    .todayLectureCSS {
        margin-left: 28%;
        width: 28px !important;
        margin-top: 146%;
    }

    /*This will work for firefox*/
    @-moz-document url-prefix() {
        .todayLectureCSS {
            margin-left: 4%;
            width: 28px !important;
            margin-top: 126%;
        }
    }


/* Footer */
.card-footer{
    padding:16px !important;
}

/* Row */
.card-footer .row{
    display:flex !important;
    flex-wrap:nowrap !important;
    justify-content:space-between !important;
    align-items:center !important;
    margin:0 !important;
}

/* Override Bootstrap columns */
.card-footer .row > [class*="col-"]{
    flex:1 !important;
    max-width:none !important;
    width:auto !important;
    padding:0 !important;
}

/* Alignment */
.card-footer .lecture_icons:first-child{
    text-align:left !important;
}

.card-footer .lecture_icons:not(:first-child):not(:last-child){
    text-align:center !important;
}

.card-footer .lecture_icons:last-child{
    text-align:right !important;
}

/* Links */
.card-footer .lecture_icons a{
    display:inline-flex;
    flex-direction:column;
    align-items:center;
    text-decoration:none;
    color:#444;
}

/* Last item stays on extreme right */
.card-footer .lecture_icons:last-child a{
    float:right;
}

/* Same icon size */
.card-footer .lecture_icons img{
    width:32px !important;
    height:32px !important;
    object-fit:contain;
    margin-bottom:8px;
}
.lecture_icons img {
    filter: none !important;
}

/* Live Chat image is 65px in HTML */
.card-footer .lecture_icons:last-child img{
    width:32px !important;
}
/* All footer icons */
.card-footer .lecture_icons img{
    width: 24px !important;
    height: 24px !important;
    object-fit: contain;
    margin-bottom: 8px;
}

/* Live Chat icon (same size) */
.card-footer .lecture_icons:last-child img{
    width: 24px !important;
    height: 24px !important;
}




/* =========================================================
   CURRENT LECTURE MODALS — FIGMA STYLE OVERRIDE
   ---------------------------------------------------------
   CSS ONLY
   Structure / Logic / Functionality preserved

   IMPORTANT:
   - Modal open/close logic untouched
   - No display:none
   - No visibility:hidden
   - No force-show
   - No .show / .fade state override
   - No collapse behavior override
   - Existing form IDs / buttons / inputs untouched
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');


/* =========================================================
   1. MODAL DESIGN TOKENS
   ========================================================= */

.modal {
    --modal-green: #17675F;
    --modal-green-dark: #125D56;
    --modal-green-soft: rgba(23, 103, 95, 0.08);

    --modal-text: #252525;
    --modal-muted: #7A7A7A;

    --modal-border: #D9D9D9;
    --modal-light-border: #E8E8E8;

    --modal-white: #FFFFFF;

    --modal-danger: #DC3545;
    --modal-warning: #FFBA49;
    --modal-success: #17675F;

    font-family: 'Montserrat', sans-serif !important;
}


.modal input,
.modal select,
.modal textarea,
.modal button {
    font-family: 'Montserrat', sans-serif;
}


/* =========================================================
   2. MODAL WIDTH
   Only visual sizing — Bootstrap modal behavior unchanged
   ========================================================= */

.modal .modal-dialog {
    width: auto !important;
    max-width: 620px !important;

    margin-top: 40px !important;
    margin-right: auto !important;
    margin-bottom: 40px !important;
    margin-left: auto !important;
}


/* =========================================================
   3. MODAL CARD
   Figma white surface / rounded corners
   ========================================================= */

.modal .modal-content {
    overflow: hidden !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 10px !important;

    box-shadow:
        0 16px 40px rgba(0, 0, 0, 0.14) !important;
}


/* =========================================================
   4. MODAL HEADER
   ========================================================= */

.modal .modal-header {
    min-height: 60px !important;

    padding: 16px 20px !important;

    align-items: center !important;

    background: #17675F !important;

    border: 0 !important;
    border-radius: 10px 10px 0 0 !important;

    box-shadow: none !important;
}


/* =========================================================
   5. MODAL TITLE
   Covers Bootstrap h4 / h5 / .modal-title
   ========================================================= */

.modal .modal-title,
.modal .modal-header h3,
.modal .modal-header h4,
.modal .modal-header h5,
.modal .modal-header h6 {

    margin: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-weight: 600 !important;
    line-height: 1.3 !important;

    color: #FFFFFF !important;
}


/* =========================================================
   6. CLOSE BUTTON
   Existing X remains exactly the same control
   ========================================================= */

.modal .modal-header .close {
    width: 34px !important;
    height: 34px !important;

    margin: 0 !important;
    padding: 0 !important;

    background: rgba(255, 255, 255, 0.12) !important;

    border: 0 !important;
    border-radius: 50% !important;

    box-shadow: none !important;

    color: #FFFFFF !important;

    font-size: 22px !important;
    font-weight: 400 !important;
    line-height: 30px !important;

    text-align: center !important;
    text-shadow: none !important;
}


.modal .modal-header .close:hover,
.modal .modal-header .close:focus {
    background: rgba(255, 255, 255, 0.20) !important;

    color: #FFFFFF !important;

    outline: 0 !important;
    box-shadow: none !important;
}


/* Existing close span/icon */
.modal .modal-header .close span,
.modal .modal-header .close i {
    color: #FFFFFF !important;
}


/* =========================================================
   7. MODAL BODY
   ========================================================= */

.modal .modal-body {
    padding: 22px 20px !important;

    background: #FFFFFF !important;

    border: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.6 !important;

    color: #252525 !important;
}


/* Normal body text */
.modal .modal-body p,
.modal .modal-body span,
.modal .modal-body li {

    font-family: 'Montserrat', sans-serif;

    line-height: 1.55;
}


/* =========================================================
   8. COURSE CONTENT TEXT
   Existing content structure remains untouched
   ========================================================= */

.modal .modal-body ul {
    margin-top: 0 !important;
    margin-bottom: 15px !important;

    padding-left: 20px !important;
}


.modal .modal-body ul li {
    margin-bottom: 6px !important;

    font-size: 14px !important;

    color: #252525 !important;
}


.modal .modal-body .lecture_content,
.modal .modal-body .lecture-content {

    color: #252525 !important;

    font-size: 14px !important;
    line-height: 1.6 !important;
}


/* =========================================================
   9. FORM GROUP
   ========================================================= */

.modal .form-group {
    margin-bottom: 16px !important;
}


/* =========================================================
   10. LABELS
   ========================================================= */

.modal label,
.modal .control-label,
.modal .form-check-label {

    margin-bottom: 7px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 500 !important;
    line-height: 1.35 !important;

    color: #252525 !important;
}


/* Required markers remain visible */
.modal label .text-danger,
.modal label span.text-danger {

    color: #DC3545 !important;
}


/* =========================================================
   11. TEXT / DATE / NUMBER / EMAIL INPUTS
   ========================================================= */

.modal .form-control,
.modal input[type="text"].form-control,
.modal input[type="number"].form-control,
.modal input[type="date"].form-control,
.modal input[type="email"].form-control,
.modal input[type="url"].form-control,
.modal input[type="time"].form-control,
.modal select.form-control {

    width: 100% !important;

    min-height: 40px !important;

    padding: 8px 12px !important;

    background: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 400 !important;

    color: #252525 !important;

    transition:
        border-color .15s ease,
        box-shadow .15s ease !important;
}


.modal .form-control::placeholder {
    color: #A0A0A0 !important;

    opacity: 1 !important;
}


.modal .form-control:focus {
    outline: 0 !important;

    border-color: #17675F !important;

    box-shadow:
        0 0 0 3px rgba(23, 103, 95, 0.08) !important;
}


/* Existing validation */
.modal .form-control.is-invalid {
    border-color: #DC3545 !important;

    box-shadow:
        0 0 0 3px rgba(220, 53, 69, 0.06) !important;
}


/* =========================================================
   12. TEXTAREA
   ========================================================= */

.modal textarea.form-control {
    min-height: 100px !important;

    padding: 10px 12px !important;

    line-height: 1.5 !important;

    resize: vertical !important;
}


/* =========================================================
   13. SELECT
   ========================================================= */

.modal select.form-control {
    padding-right: 36px !important;

    cursor: pointer !important;
}


/* =========================================================
   14. FILE INPUT
   Existing upload functionality unchanged
   ========================================================= */

.modal input[type="file"] {
    width: 100% !important;

    min-height: 40px !important;

    padding: 5px !important;

    background: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;
    border-radius: 5px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 13px !important;

    color: #7A7A7A !important;
}


.modal input[type="file"]::file-selector-button {
    height: 28px;

    margin-right: 10px;
    padding: 0 12px;

    background: #F2F2F2;

    border: 1px solid #E5E5E5;
    border-radius: 5px;

    font-family: 'Montserrat', sans-serif;

    font-size: 13px;
    font-weight: 400;

    color: #252525;

    cursor: pointer;
}


.modal input[type="file"]::-webkit-file-upload-button {
    height: 28px;

    margin-right: 10px;
    padding: 0 12px;

    background: #F2F2F2;

    border: 1px solid #E5E5E5;
    border-radius: 5px;

    font-family: 'Montserrat', sans-serif;

    font-size: 13px;

    color: #252525;

    cursor: pointer;
}


/* =========================================================
   15. RADIO / CHECKBOX
   Existing values/selection logic untouched
   ========================================================= */

.modal input[type="radio"],
.modal input[type="checkbox"] {
    accent-color: #17675F;
}


/* =========================================================
   16. EXISTING COLLAPSIBLE DETAIL AREA
   Styling only — no .show/.collapse visibility changes
   ========================================================= */

.modal .collapse {
    margin-top: 10px;

    padding: 14px;

    background: rgba(23, 103, 95, 0.035);

    border: 1px solid #E4ECEA;
    border-radius: 5px;
}


/* =========================================================
   17. HELP / SMALL TEXT
   ========================================================= */

.modal small,
.modal .form-text,
.modal .text-muted {

    font-family: 'Montserrat', sans-serif !important;

    font-size: 11px !important;
    line-height: 1.45 !important;
}


/* =========================================================
   18. ALERTS
   Existing JS show/hide behavior remains untouched
   ========================================================= */

.modal .alert {
    margin: 10px 0 !important;
    padding: 10px 12px !important;

    border-radius: 5px !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    line-height: 1.45 !important;

    box-shadow: none !important;
}


.modal .alert-success {
    background: rgba(23, 103, 95, 0.08) !important;

    border: 1px solid rgba(23, 103, 95, 0.18) !important;

    color: #17675F !important;
}


.modal .alert-danger {
    background: rgba(220, 53, 69, 0.06) !important;

    border: 1px solid rgba(220, 53, 69, 0.15) !important;

    color: #C82333 !important;
}


.modal .alert-warning {
    background: rgba(255, 186, 73, 0.10) !important;

    border: 1px solid rgba(255, 186, 73, 0.25) !important;

    color: #8A6100 !important;
}


/* =========================================================
   19. MODAL FOOTER
   Existing buttons/order remain unchanged
   ========================================================= */

.modal .modal-footer {
    padding: 16px 20px 20px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-top: 1px solid #EEEEEE !important;

    gap: 8px !important;
}


/* =========================================================
   20. COMMON BUTTON STYLE
   ========================================================= */

.modal .btn {
    min-height: 40px !important;

    margin: 0 !important;
    padding: 0 18px !important;

    border-radius: 5px !important;

    box-shadow: none !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-weight: 500 !important;
    line-height: 38px !important;

    transition:
        background-color .15s ease,
        border-color .15s ease,
        color .15s ease !important;
}


/* =========================================================
   21. PRIMARY / SUCCESS BUTTON
   ========================================================= */

.modal .btn-primary,
.modal .btn-success,
.modal .upload_btn {

    background: #17675F !important;

    border: 1px solid #17675F !important;

    color: #FFFFFF !important;
}


.modal .btn-primary:hover,
.modal .btn-primary:focus,
.modal .btn-success:hover,
.modal .btn-success:focus,
.modal .upload_btn:hover,
.modal .upload_btn:focus {

    background: #125D56 !important;

    border-color: #125D56 !important;

    color: #FFFFFF !important;

    box-shadow: none !important;
}


/* =========================================================
   22. SECONDARY / CLOSE BUTTON
   ========================================================= */

.modal .btn-secondary,
.modal .btn-light {

    background: #FFFFFF !important;

    border: 1px solid #D9D9D9 !important;

    color: #252525 !important;
}


.modal .btn-secondary:hover,
.modal .btn-secondary:focus,
.modal .btn-light:hover,
.modal .btn-light:focus {

    background: #F7F9F8 !important;

    border-color: #BFCAC7 !important;

    color: #17675F !important;
}


/* =========================================================
   23. DANGER BUTTON
   Existing delete functionality unchanged
   ========================================================= */

.modal .btn-danger {
    background: #FFFFFF !important;

    border: 1px solid #DC3545 !important;

    color: #DC3545 !important;
}


.modal .btn-danger:hover,
.modal .btn-danger:focus {
    background: #DC3545 !important;

    border-color: #DC3545 !important;

    color: #FFFFFF !important;
}


/* =========================================================
   24. BUTTON ICONS
   Existing icons remain visible
   ========================================================= */

.modal .btn i,
.modal .btn .fa {
    margin-right: 5px;

    color: inherit !important;
}


/* =========================================================
   25. DISABLED BUTTON
   Existing disabled logic remains unchanged
   ========================================================= */

.modal .btn:disabled,
.modal .btn.disabled {
    cursor: not-allowed !important;

    box-shadow: none !important;
}


/* =========================================================
   26. UPLOAD PROGRESS
   Existing progress logic unchanged
   ========================================================= */

.modal .progress {
    height: 8px !important;

    margin-top: 10px !important;

    background: rgba(23, 103, 95, 0.10) !important;

    border-radius: 50px !important;

    box-shadow: none !important;
}


.modal .progress-bar {
    background: #17675F !important;

    border-radius: 50px !important;

    box-shadow: none !important;
}


/* =========================================================
   27. TABLE INSIDE MODAL
   If helper renders existing table, preserve all content
   ========================================================= */

.modal .table-responsive {
    border: 1px solid #E5E7EB !important;
    border-radius: 5px !important;
}


.modal .table {
    margin-bottom: 0 !important;

    font-family: 'Montserrat', sans-serif !important;
}


.modal .table thead th {
    padding: 10px 12px !important;

    background: rgba(23, 103, 95, 0.10) !important;

    border-color: #E5E7EB !important;

    font-size: 11px !important;
    font-weight: 600 !important;

    color: #17675F !important;
}


.modal .table tbody td {
    padding: 10px 12px !important;

    border-color: #EEEEEE !important;

    font-size: 12px !important;

    color: #252525 !important;
}


/* =========================================================
   28. EXISTING LINKS
   ========================================================= */

.modal a {
    color: #17675F;

    text-decoration: none;
}


.modal a:hover {
    color: #125D56;

    text-decoration: underline;
}


/* =========================================================
   29. MOBILE
   No modal content hidden/reordered
   ========================================================= */

@media (max-width: 767.98px) {

    .modal .modal-dialog {
        max-width: calc(100% - 24px) !important;

        margin-top: 20px !important;
        margin-right: 12px !important;
        margin-bottom: 20px !important;
        margin-left: 12px !important;
    }


    .modal .modal-header {
        min-height: 56px !important;

        padding: 14px 16px !important;
    }


    .modal .modal-title,
    .modal .modal-header h3,
    .modal .modal-header h4,
    .modal .modal-header h5,
    .modal .modal-header h6 {

        font-size: 16px !important;
    }


    .modal .modal-body {
        padding: 18px 16px !important;
    }


    .modal .modal-footer {
        padding: 14px 16px 16px !important;
    }

}
/* Content modal - Bootstrap Large size */
@media (min-width: 992px) {
    .modal .modal-dialog.modal-lg {
        max-width: 800px !important;
    }
}
/* Content modal - Action column fix */
.course-content-modal table th:last-child,
.course-content-modal table td:last-child {
    min-width: 165px !important;
    width: 165px !important;
    text-align: center !important;
}

/* Action button must remain inside its cell */
.course-content-modal table td:last-child .btn,
.course-content-modal table td:last-child button,
.course-content-modal table td:last-child a.btn {
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;

    margin-left: 0 !important;
    margin-right: 0 !important;

    box-sizing: border-box !important;
    white-space: nowrap;
}
.course-content-modal .table-responsive {
    overflow-x: auto !important;
}
</style>
<style>
/* =========================================================
   CURRENT LECTURE — FIGMA MAIN DASHBOARD-2
   Visual styling only — logic/functionality untouched
   ========================================================= */

/* Lecture Content upper section */
.card-aside .card1 {
    /* background: #17675F1A !important; */
    border-radius: 5px 5px 0 0 !important;
    box-shadow: none !important;
}

/* Because inner card-body currently has its own background */
.card-aside .card1 .card-body {
    background: #17675F1A !important;
}

/* Lecture Content heading */
.card-aside .card1 h4 {
    color: #252525 !important;
}

/* Lecture Content normal text */
.card-aside .card1 .lecture_content,
.card-aside .card1 .lecture_content li,
.card-aside .card1 .lecture_content p {
    color: #252525 !important;
}

/* Bottom Content / Material / Video / Assignment / Quiz / Live Chat area */
.card-aside .card-footer {
    background: #17675F1A !important;
    border-radius: 0 0 5px 5px !important;
    border: none !important;
}

/* Footer labels */
.card-aside .card-footer .lecture_icons a {
    color: #252525 !important;
}

/* Preserve white warning/info box as shown in Figma */
.card-aside .card1 .alert {
    background: #FFFFFF !important;
    color: #252525 !important;
}


#today_lectures .alert {
    /* background: transparent !important; */
    background: #FFF !important;
    border: none !important;
    box-shadow: none !important;
}
@media (min-width: 992px) {
    .modal .modal-dialog.modal-lg {
        max-width: 989px !important;
    }
}
tr[id^="lrow_"] > td {
    text-align: center !important;
}
</style>
@php
$new_tt_id = "";

date_default_timezone_set("Asia/Karachi");
$today = date("H:i:s");
$class_date='';
@endphp
@if(count($lecture_rec)>0)
@foreach ($lecture_rec as $item)
<?php
$new_tt_id = $item->tt_id;
$contents_count = 0;
$co_id_encrypted = my_simple_crypt($item->mtt_co_id, 'e');
$class_date = $item->tt_class_date;
$class_time = $item->end_time;
?>
<!-- <div class="default-grid"> -->
<!-- <div class="row">
            <div class="col-sm-12 default-grid-item">--}} -->
<!-- Job card start -->


<?php
$lecture_color = "#009788";
?>
<div class="row mt-4 today_lectures_div">

    <div class="col-lg-12">
        <div>
            <div class="current-lecture-header">
                <h4 class="profile-overview-title mb-0" style="margin-bottom: 0 !important;">
                    Current Lecture
                </h4>

                <a href="javascript:void(0);" class="current-attendance-link
       {{ $item->attendance_marks_status == '0' ? 'blink' : '' }}" 
        onclick="check_contents(
        {{$item->mtt_co_id }},
        {{ $item->tt_id }},
         '{{ $item->tt_class_date }}',
         '{{ $item->co_school_id }}'
         )">
                    Class Attendance
                </a>
                 {{-- <button type="button" class="btn btn-success1 <?=$item->attendance_marks_status=="0" ? "blink":"" ?> waves-effect waves-light" onclick="check_contents(<?= $item->mtt_co_id; ?>,<?= $item->tt_id; ?>,'<?= $item->tt_class_date; ?>','<?= $item->co_school_id;?>')" style="float: right; color:#ffc107!important; margin-top: -15px;">
                                            <svg class="card-custom-icon  text-secondary icon-dropshadow-secondary" style="color: #ffc107 !important;" x="1008" y="1248" viewBox="0 0 24 24" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
                                                <path opacity=".0" d="M12.07,6.01 C8.2,6.01 5.07,9.14 5.07,13.01 C5.07,16.88 8.2,20.01 12.07,20.01 C15.94,20.01 19.07,16.88 19.07,13.01 C19.07,9.14 15.94,6.01 12.07,6.01 Z M13.07,14.01 L11.07,14.01 L11.07,8.01 L13.07,8.01 L13.07,14.01 Z"></path>
                                                <path d="M9.07,1.01 L15.07,1.01 L15.07,3.01 L9.07,3.01 L9.07,1.01 Z M11.07,8.01 L13.07,8.01 L13.07,14.01 L11.07,14.01 L11.07,8.01 Z M19.1,7.39 L20.52,5.97 C20.09,5.46 19.62,4.98 19.11,4.56 L17.69,5.98 C16.14,4.74 14.19,4 12.07,4 C7.1,4 3.07,8.03 3.07,13 C3.07,17.97 7.09,22 12.07,22 C17.05,22 21.07,17.97 21.07,13 C21.07,10.89 20.33,8.93 19.1,7.39 Z M12.07,20.01 C8.2,20.01 5.07,16.88 5.07,13.01 C5.07,9.14 8.2,6.01 12.07,6.01 C15.94,6.01 19.07,9.14 19.07,13.01 C19.07,16.88 15.94,20.01 12.07,20.01 Z"></path>
                                            </svg>
                                            <span style="color: #424e79;font-weight:bold">
                                                 Class Attendance1
                                            </span>
                                        </button> --}}
            </div>
            <div>
            </div>
        </div>
        <div class="card card-aside" style="margin-bottom:0 !important;">
            <div class="card-aside-column br-tl-7 br-bl-7" style="vertical-align: middle;  min-width:3rem !important; writing-mode: sideways-lr;flex:0 0 1% !important; color: white;padding-bottom: 67px; background-color:<?=$lecture_color;?>;">
                <img src="<?=url('/')?>/assets/images/today.png" class="todayLectureCSS">
            </div>
            <div class="card-body d-flex flex-column" style="background-color:<?=$lecture_color;?>;padding: 0px !important; border-radius: 0px 10px 10px 0px;border-top: 1px solid #17675F !important;
    border-right: 1px solid #17675F !important;
    border-bottom: 1px solid #17675F !important;">
                {{-- start of right side all code    --}}
                <div class="card" style="border-left: 10px solid #17675F !important; padding: 0px !important;">
                    <div class="card-body" style="display: none; padding-bottom: 0px; background-color:white !important; border-bottom: solid 2px #D2D6F1; border-radius:0px !important;padding-top:18px !important;">
                        <div class="row" style="width: 100% !important; ">
                            <div class="col-12  col-md-8 col-xl-8">

                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding-bottom: 0px; background-color: !important; border: solid 0px #D2D6F1; border-radius:0px !important;">
                        <div class="row" style="width: 100% !important; ">
                            <div class="col-12  col-md-8 col-xl-8" style="line-height:80px;">
                                <h4 style="color: #424e79 !important;">{{$item->course_name}}</h4>
                                <!-- {{$item->tt_id;}} -- {{$item->mtt_co_id}} -- {{$item->lecture_status}} -->
                            </div>
                            {{-- <div class="col-12  col-md-4 col-xl-4">
                                        <button type="button" class="btn btn-success1 <?=$item->attendance_marks_status=="0" ? "blink":"" ?> waves-effect waves-light" onclick="check_contents(<?= $item->mtt_co_id; ?>,<?= $item->tt_id; ?>,'<?= $item->tt_class_date; ?>','<?= $item->co_school_id;?>')" style="float: right; color:#ffc107!important; margin-top: -15px;">
                                            <svg class="card-custom-icon  text-secondary icon-dropshadow-secondary" style="color: #ffc107 !important;" x="1008" y="1248" viewBox="0 0 24 24" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
                                                <path opacity=".0" d="M12.07,6.01 C8.2,6.01 5.07,9.14 5.07,13.01 C5.07,16.88 8.2,20.01 12.07,20.01 C15.94,20.01 19.07,16.88 19.07,13.01 C19.07,9.14 15.94,6.01 12.07,6.01 Z M13.07,14.01 L11.07,14.01 L11.07,8.01 L13.07,8.01 L13.07,14.01 Z"></path>
                                                <path d="M9.07,1.01 L15.07,1.01 L15.07,3.01 L9.07,3.01 L9.07,1.01 Z M11.07,8.01 L13.07,8.01 L13.07,14.01 L11.07,14.01 L11.07,8.01 Z M19.1,7.39 L20.52,5.97 C20.09,5.46 19.62,4.98 19.11,4.56 L17.69,5.98 C16.14,4.74 14.19,4 12.07,4 C7.1,4 3.07,8.03 3.07,13 C3.07,17.97 7.09,22 12.07,22 C17.05,22 21.07,17.97 21.07,13 C21.07,10.89 20.33,8.93 19.1,7.39 Z M12.07,20.01 C8.2,20.01 5.07,16.88 5.07,13.01 C5.07,9.14 8.2,6.01 12.07,6.01 C15.94,6.01 19.07,9.14 19.07,13.01 C19.07,16.88 15.94,20.01 12.07,20.01 Z"></path>
                                            </svg>
                                            <span style="color: #424e79;font-weight:bold">
                                                 Class Attendance
                                            </span>
                                        </button>
                                </div> --}}
                        </div>
                    </div>
                    <div class="card-body" style="padding: 0px; border-bottom: solid 2px;" id="content<?= $item->tt_id; ?>">
                        <?php
                            $card_col1 = 2;
                            ?>
                        <div class="row">
                            <div class="col-6 col-md-<?= $card_col1; ?> d-flex col-xl-<?= $card_col1; ?>">
                                <i class="mdi mdi-basket-fill fs-40 text-success icon-dropshadow-success mr-3" style="padding-left: 8px;"></i>
                                <div class="mt-5">
                                    <h6>Total Lectures</h6>
                                    <h4 class="mb-0 font-weight-bold"> <img src="{{ asset('images/tl.png') }}" alt="Live Chat" style="width:20px;height:20px;margin-right:8px;object-fit:contain;"> <?= $item->total_classes; ?></h4>
                                </div>
                            </div>
                            <div class="col-6 col-md-<?= $card_col1; ?> d-flex col-xl-<?= $card_col1; ?> ">
                                <i class=" mdi mdi-check-circle fs-40 text-primary icon-dropshadow-primary mr-3"></i>
                                <div class="mt-5">
                                    <h6>Delivered</h6>
                                    <h4 class="mb-0 font-weight-bold"> <img src="{{ asset('images/delivered12.png') }}" alt="Live Chat" style="width:20px;height:20px;margin-right:8px;object-fit:contain;"> <?= $item->delivered_classes; ?></h4>
                                </div>
                            </div>

                            <div class="col-6 col-md-<?= $card_col1; ?> d-flex col-xl-<?= $card_col1; ?> " style="padding-left: 20px;">
                                <i class="mdi mdi-calendar-clock fs-40 text-danger icon-dropshadow-primary mr-3"></i>
                                <div class="mt-5">
                                    <h6>Remaining</h6>
                                    <h4 class="mb-0 font-weight-bold"> <img src="{{ asset('images/remainings.png') }}" alt="Live Chat" style="width:20px;height:20px;margin-right:8px;object-fit:contain;"> <?= $item->total_classes - $item->delivered_classes; ?></h4>
                                </div>
                            </div>
                            <div class="col-6 col-md-<?= $card_col1; ?> d-flex col-xl-<?= $card_col1; ?> ">
                                <i class="mdi mdi-alarm fs-40 text-success icon-dropshadow-danger mr-3"></i>
                                <div class="mt-5">
                                    <h6 style="
    text-align: justify;
">Class Time</h6>
                                    <p class="mb-0 font-weight-bold" style="font-size: 13px;"> <img src="{{ asset('images/ctime.png') }}" alt="Live Chat" style="width:20px;height:20px;margin-right:8px;object-fit:contain;"> {{$item->start_time}}-{{$item->end_time}}</p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 d-flex col-xl-3 ">
                                <i class="mdi mdi-clock fs-40 text-danger icon-dropshadow-danger mr-3"></i>
                                <div class="mt-5">
                                    <h6>Remaining Class Time </h6>
                                    <div style="display:flex !important;"><img src="{{ asset('images/rct.png') }}" alt="Live Chat" style="width:20px;height:20px;margin-right:8px;object-fit:contain;"> <p class="mb-0 font-weight-bold blink1" style="font-size: 18px; color:red" id="rem-time<?= $item->tt_id; ?>"> Please Wait Loading...</p></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <div class="card1" style="margin-left:6px !important; margin-right:6px !important;">
                                <div class="card-body" style="padding: 0.5rem 0.5rem;">
                                    <h4 class="mb-1 font-weight-bold">Lecture Content</h4>
                                    {{-- @if(count($item->contents)>0)
                                    <ul style="list-style-type: disc !important; padding-left:1em !important; margin-left:1em;" class="lecture_content" id="check_content<?= $item->tt_id; ?>">
                                        <?php $allText = ''; ?>
                                        @foreach ($item->contents as $show_contents)
                                        <?php $allText .= ' '.str_replace(",","",$show_contents->coc_details)."," ; ?>
                                        @endforeach
                                        <li><?= Illuminate\Support\Str::limit($allText, 99, '... &nbsp &nbsp <b><a href="javascript:void();" class="highlighter-rouge" onclick="show_content('.$item->tt_id.')" > Read More </a></b>'); ?></li>
                                        <li style="display:none;" id="course_content_<?=$item->tt_id?>">
                                            <p class="text-justify font-weight-normal"><?=$allText?> </p>
                                        </li>
                                    </ul>
                                    @else
                                    <div class="alert alert-warning" role="alert">
                                        Please enter lecture contents for Class Attendance
                                    </div>
                                    @endif --}}
                                    @if(count($item->contents)>0)
                                    <ul style="list-style-type: disc !important; padding-left:1em !important; margin-left:1em;" class="lecture_content" id="check_content<?= $item->tt_id; ?>">

                                        <?php $allText = ''; ?>

                                        @foreach ($item->contents as $show_contents)
                                        <?php
            $allText .= ' ' . str_replace(",", "", $show_contents->coc_details) . ",";
        ?>
                                        @endforeach

                                        <li>
                                            <?= Illuminate\Support\Str::limit(
            $allText,
            99,
            '... &nbsp;&nbsp;<b><a href="javascript:void();" class="highlighter-rouge" onclick="show_content('.$item->tt_id.')"> Read More </a></b>'
        ); ?>
                                        </li>

                                        <li style="display:none;" id="course_content_<?=$item->tt_id?>">
                                            <p class="text-justify font-weight-normal">
                                                <?=$allText?>
                                            </p>
                                        </li>

                                    </ul>
                                    @else
                                    <div class="alert alert-warning" role="alert" style="background:#FFF !important;">
                                        Please enter lecture contents for Class Attendance
                                    </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        $card_col = 2;
                        $image_width = 35;
                        $date_sr_no = 1;
                        if(auth()->user()->group_id != 825){
                        ?>

                    <div class="card-footer" style="padding: 0.5rem 0.5rem;margin-left:18px !important; margin-right:18px !important;">
                        <div class="row">
                            <div class="col-6 col-md-<?= $card_col; ?> col-xl-<?= $card_col; ?> lecture_icons">
                                <div class="card-block" style="padding-top: 0px !important;">
                                    <!-- data-toggle="modal" data-target="#exampleModalCenter" -->
                                    <a onclick="get_ttID('<?= $item->tt_id; ?>','<?= $item->mtt_co_id; ?>')" style="cursor:pointer"><img src="{{asset('images/gContent.png')}}" width="28px" data-toggle="tooltip" data-placement="top" title="Lecture Content"><br>Content
                                    </a>
                                </div>
                            </div>
                            <!-- data-toggle="modal" data-target="#exampleModalCenter4" -->
                            <!-- onclick="get_co_id('<?= $item->mtt_co_id; ?>','1','<?= $item->tt_id; ?>')" -->

                            <div class="col-6 col-md-<?= $card_col; ?> col-xl-<?= $card_col; ?> lecture_icons">

                                <a onclick="grader_UploadFileCourseContent('<?= $item->mtt_co_id; ?>','1','<?= $item->tt_id; ?>')" style="cursor:pointer">
                                    <img src="{{asset('images/gMaterial.png')}}" width="<?= $image_width; ?>" title="Upload File"><br>Material
                                </a>

                            </div>
                            <div class="col-6 col-md-<?= $card_col; ?> col-xl-<?= $card_col; ?> lecture_icons">
                                <div class="card-block" style="padding-top: 0px !important;">
                                    <a href='/teacher/videouploaddrive/<?= $item->mtt_co_id; ?>/1/<?= $item->tt_id; ?>' data-target="#exampleModalCenter22">
                                        <img src="{{asset('images/gVideo.png')}}" width="<?= $image_width; ?>" data-toggle="tooltip" data-placement="top" title="Upload Video"><br>Video
                                    </a>

                                </div>
                            </div>
                            <div class="col-6 col-md-<?= $card_col; ?> col-xl-<?= $card_col; ?> lecture_icons">
                                <div class="card-block assignment_buttons" style="padding-top: 0px !important;">
                                    <a href="javascript:void(0);" onclick="uploadAssignment(<?=$item->tt_id?>,<?=$item->mtt_co_id?>,'<?=my_simple_crypt($item->mtt_co_id,'e')?>');">
                                        <img src="{{asset('images/gAssignment.png')}}" width="<?= $image_width; ?>" data-toggle="tooltip" data-placement="top" title="Upload Assignment"><br>Assignment
                                    </a>
                                </div>
                            </div>
                            <div class="col-6 col-md-<?= $card_col; ?> col-xl-<?= $card_col; ?> lecture_icons">
                                <div class="card-block" style="padding-top: 0px !important;">
                                    <a href="{{route('teacher/online_exam')}}/<?=$co_id_encrypted;?>/1234" data-container="body" data-toggle="popover" data-popover-color="popsuccess" data-placement="bottom" data-trigger="hover" data-title="CMS Guidline" data-content=" " class="guidline-alert-exam">
                                        <img src="{{asset('images/gQuiz.png')}}" width="35px">
                                        <br>Quiz
                                    </a>
                                </div>
                            </div>
                            <div class="col-6 col-md-<?= $card_col; ?> col-xl-<?= $card_col; ?> lecture_icons">
                                <div class="card-block" style="padding-top: 0px !important;">
                                    <a href="<?=url('course/chat/'.$item->mtt_co_id)?>">
                                        <img src="{{asset('images/gChat.png')}}" width="24px"><br>Live Chat
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                {{-- end of right side all code      --}}
            </div>
        </div>
    </div>
</div>


<!-- Job card end -->
@endforeach

@else



<div class="alert alert-info" style="margin-top:10px;border:none;">
    <!-- <strong>Info!</strong> Honourable Faculty, Right now you have no Lecture(s) -->
    Honourable Faculty, You currently have no lectures scheduled.
</div>
@endif



<div id="today_modal_container"></div>


<script type="text/javascript">
    $(document).ready(function() {
        var str = '<?= json_encode($lecture_rec); ?>';
        if (str.length) {
            $.each(JSON.parse(str), function(index, value) {
                var class_date = value.tt_class_date;
                const myArr = class_date.split(" ");
                var completeDateString = (myArr[0] + " " + value.end_time);
                var countDownDate = moment([completeDateString], "YYYY-MM-DD HH:mm a");
                var x = setInterval(function() {
                    var distance = countDownDate.diff(moment());
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    document.getElementById("rem-time" + value.tt_id).innerHTML = hours + "h " + minutes + "m " + seconds + "s ";
                    if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("rem-time" + value.tt_id).innerHTML = "EXPIRED";
                    }
                }, 1000);


            });
        }
    });


    function show_content(tt_id) {
        //   alert($('#course_content_'+tt_id).html());
        $.showAlert({
            title: "Course Content"
            , body: $('#course_content_' + tt_id).html()
        });
    }

//     window.check_contents = function(co_id, slot_id, slot_date, var_school_id) {

//     var check_content = $.trim(
//         $("#check_content" + slot_id).html() || ''
//     );

//     if (var_school_id > 1) {
//         if (check_content != '') {
//             window.location.href =
//                 "{{ route('attendance/attendance') }}/" +
//                 co_id + '/' + slot_id + '/' + slot_date;
//         } else {
//             $.courseContentRawModal(co_id, slot_id);
//         }
//     } else {
//         window.location.href =
//             "{{ route('attendance/attendance') }}/" +
//             co_id + '/' + slot_id + '/' + slot_date;
//     }
// };
    function check_contents(co_id, slot_id, slot_date, var_school_id) {

        var get_course_content = parseInt($("#check" + slot_id).val());
        var check_content = $.trim($("#check_content" + slot_id).html());

        if (var_school_id > 1) {
            if (check_content != '') {
                window.location.href = "{{ route('attendance/attendance')}}/" + co_id + '/' + slot_id + '/' + slot_date;
            } else {
                $("#paragraph" + slot_id).html('Please Update Contents');
                $.courseContentRawModal(co_id, slot_id);
            }
        } else {
            window.location.href = "{{ route('attendance/attendance')}}/" + co_id + '/' + slot_id + '/' + slot_date;
        }

    }

    function skip_and_move(co_id, slot_id, slot_date) {

        window.location.href = "{{ route('attendance/attendance')}}/" + co_id + '/' + slot_id + '/' + slot_date;
    }

    function get_co_id(co_id, lec, mtt_id) {
        $('#course_id').val(co_id);
        $('#file_course_id').val(co_id);
        $('#file_lec_no').val(lec);
        $('#file_mtt_id').val(mtt_id);
        $('#video_mtt_id').val(mtt_id);
    }


    //////////////////////////////
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function addnewcontent() {
        var tt_id = $("#tt_id").val();
    }


    function isFloat(n) {
        return n === +n && n !== (n | 0);
    }

    function isInteger(n) {
        return n === +n && n === (n | 0);
    }


    function get_tt_id(tt_id, mtt_co_id) {
        $('#tt_id').val(tt_id);
        $('#mtt_co_id').val(mtt_co_id);
    }

    function get_ttID(tt_id, mtt_co_id) {
        $("#tt_new_content_id").val(tt_id);
        $(".mtt_co_id").val(mtt_co_id);
        $("#tt_id").val(tt_id);

           // Sirf next opening Content modal ko Bootstrap Large banaye
    $(document).one('show.bs.modal', '.modal', function () {
         $(this).addClass('course-content-modal');
        $(this).find('.modal-dialog').addClass('modal-lg');
    });


        $.courseContentRawModal(mtt_co_id, tt_id, function() {
            alert('Hello How are you !');
        });
    }

    var isModalLoad = 0;

    function uploadAssignment(tt_id, mtt_co_id, e_coID) {
        $("#tt_new_content_id").val(tt_id);
        $(".mtt_co_id").val(mtt_co_id);
        $("#tt_id").val(tt_id);
        window.location = '<?=url('Lectures/')?>/' + e_coID + '?tt_id=' + tt_id;

        //window.location = '<?=url('teacher/course_assignment/')?>/'+e_coID;
    }
    ///////////////////////////////

    function uploadAssignmentRequest() {
        var latesub = $('input[name=latesub]:checked').val();
        var resub = $('input[name=resub]:checked').val();
        var bi_form = document.querySelector('#request_form');
        var fd = new FormData(bi_form);

        var marks = $("#marks").val();
        var title = $("#title2").val();
        var assessment = $("#assessment_id").val();
        var file = $("#uploadfile2").val();
        var dueDate = $("#duedate").val();
        var description = $("#description").val();
        var typeArr = ["xlsx", "docx", "pptx", "jpg", "jpeg", "pdf", "png", "zip"];

        $('.msg-div').hide();
        $('.form-control').removeClass('is-invalid');
        var error = 0;

        if (assessment.trim() == '') {
            error++;
            $('#assessment_id').addClass('is-invalid');
            $('#error_msg span').html('Please select assessment first!');
        } else if ((marks.trim() == '') || (marks < 1)) {
            if (!(isFloat() || isInteger())) {
                error++;
                $('#marks').addClass('is-invalid');
                $('#error_msg span').html('Please define valid marks between 1 to 100');
            }
        } else if (title.trim() == '') {
            error++;
            $('#title2').addClass('is-invalid');
            $('#error_msg span').html('Please provide title!');
        }
        // if(file.trim() == ''){
        //         error++;
        //         $('#uploadfile2').addClass('is-invalid');
        //         $('#error_msg span').html('Please provide description!');
        //     }
        if (file.trim() != '') {
            var fileInfoArr = file.split(".");
            var len = fileInfoArr.length;
            var type = fileInfoArr[len - 1];
            var index = typeArr.indexOf(type);

            if (index < 0) {
                error++;
                $('#uploadfile2').addClass('is-invalid');
                $('#error_msg span').html('Only xlsx, docx, jpg, jpeg, png and pdf are allow !');
            }
            var fileInput = document.getElementById("uploadfile2");
            var file_size = 0;
            try {
                file_size = fileInput.files[0].size;
            } catch (e) {
                var objFSO = new ActiveXObject("Scripting.FileSystemObject");
                var e = objFSO.getFile(fileInput.value);
                var fileSize = e.size;
                // file_size = fileSize/1024;
            }
            file_size = (file_size / 1024) / 1024;

            if (file_size > 201) {
                error++;
                $('#uploadfile2').addClass('is-invalid');
                $('#error_msg span').html('File size must be less then 200MB!');
            }
        }

        if ($('#detail').hasClass('show')) { // #rule if user click on detail then he must provide the whole detail
            if (description.trim() == '') {
                error++;
                $('#description').addClass('is-invalid');
                $('#error_msg span').html('Please provide description!');
            }

            if (dueDate.trim() == '') {
                error++;
                $('#duedate').addClass('is-invalid');
                $('#error_msg span').html('Please provide due date!');
            }
            $('#is_file').val('yes');
        } else {
            $('#is_file').val('no');
        }

        if (error > 0) {
            $('#error_msg').show();
            return false;
        }

        save_btn_action('loading');
        $.ajax({
            type: 'POST'
            , url: "{{url('uploadassessment?fcu_id=')}}" + $('#fcu_id').val()
            , data: fd
            , processData: false
            , contentType: false
            , success: function(data) {
                save_btn_action('done');
                if (data.status) {
                    $("#saved_msg").show();
                    $('#saved_msg span').html(data.message);
                    $('.assess-form-input').val('');
                    setTimeout(function() {
                        $("#saved_msg").hide();
                        $('#exampleModalCenter3').modal('hide');
                    }, 1800);
                    location.reload();
                } else {
                    $('#error_msg').show();
                    $('#error_msg span').html(data.message);
                }
            }
            , error: function() {
                // save_btn_action('done');
                console.log('error', res);
                $('#edit-fail-msg').show();
                if (res.status == 500) {
                    $('#error_msg span').html('Unknow Problem! Please infrom ITRC Team.');
                } else {
                    $('#error_msg span').html(res.statusText);
                }
            }
        });
    }


    ////////////////////////////////


    $("#btn").click(function(e) {
        e.preventDefault();

        // if( $('#detail').hasClass('show') ){
        //     $('#is_file').val('yes');
        // }else{
        //     $('#is_file').val('no');
        //     $('#uploadfile2').val('');
        //     $('#description').val('');
        //     $('#duedate').val('');
        //     $('#latesub-no').click();
        //     $('#resub').click();
        // }



    });

    function save_btn_action(action) {
        if (action == 'loading') {
            $(".upload_btn").prop('disabled', true);
            $(".upload_btn .save").hide();
            $(".upload_btn .loading").show();
        } else {
            $(".upload_btn").prop('disabled', false);
            $(".upload_btn .save").show();
            $(".upload_btn .loading").hide();
        }
    }

    function grader_UploadFileCourseContent(co_id, lec, mtt_id) {
        var hiddenFieldsArr = [{
                'key': 'co_id'
                , 'val': co_id
            }
            , {
                'key': 'course_id'
                , 'val': co_id
            }
            , {
                'key': 'file_course_id'
                , 'val': co_id
            }
            , {
                'key': 'file_lec_no'
                , 'val': lec
            }
            , {
                'key': 'file_mtt_id'
                , 'val': mtt_id
            }
            , {
                'key': 'video_mtt_id'
                , 'val': mtt_id
            }
        ];

        $.fileUploadPopup({
            hiddenFields: hiddenFieldsArr
        });
    }


    function uploadAssignmetnModal(co_ID, mtt_ID) {
        $.uploadAssignmentModal({
            coID: co_ID
            , mttID: mtt_ID
        });
    }

</script>
