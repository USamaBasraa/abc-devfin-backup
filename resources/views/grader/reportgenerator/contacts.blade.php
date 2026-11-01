@extends('layouts.app')
{{-- @dd("123"); --}}
@section('content')
    <style type="text/css">
        .grid_center{
            text-align: center;
        }
     .button_div{
         margin-left: auto;
         margin-right: auto;
         border: solid 2px #6f42c1;
     }
     .button_size{
    /* height: 30px; */
        width: 32%;
        font-size: 14px;
        margin:2px;
        float: left;
       }
    .criteria_sub_div{
        float: left;
        padding: 0px;
    }

	.nameStyle {
		color: #252525;
font-size: 18px;
font-style: normal;
font-weight: 500;
	}
	.desiginationStyle {
		color: #62748E;
font-size: 14px;
font-style: normal;
font-weight: 400;
	}
	.label {
		color: #62748E;
font-size: 12px;
font-style: normal;
font-weight: 400;
margin-bottom:0px;
	}
	.labelValue {
		color: #3C3B3B;
font-size: 14px;
font-style: normal;
font-weight: 400;
	}


	/* =========================================================
   CONTACT US — FIGMA STYLE OVERRIDE
   Figma frame: Contact (202:661)

   IMPORTANT:
   - NOTHING HIDDEN
   - NOTHING ADDED
   - Existing 5 contact blocks remain exactly the same
   - Commented Blade content remains commented
   - No JS / HTML / PHP changes
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');


/* =========================================================
   1. SCREEN DESIGN TOKENS
   ========================================================= */

.e-panel {
    --contact-text: #252525;
    --contact-muted: #62748E;
    --contact-value: #3C3B3B;
    --contact-white: #FFFFFF;
    --contact-line: rgba(98, 116, 142, 0.20);

    font-family: 'Montserrat', sans-serif !important;
}


/* Apply Figma font only to this contact section */
.e-panel,
.e-panel * {
    font-family: 'Montserrat', sans-serif;
}


/* =========================================================
   2. OUTER CONTACT PANEL
   Figma does not use a visible outer Bootstrap card.
   Structure remains unchanged.
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


/* Existing card-body stays present */
.e-panel > .card-body {
    width: 100% !important;

    padding-top: 0 !important;

    background: transparent !important;

    border: 0 !important;

    box-shadow: none !important;
}


/* =========================================================
   3. PAGE TITLE
   Figma:
   Contact Us
   Montserrat SemiBold 20px
   ========================================================= */

.e-panel > .card-body > .row:first-child h1 {
    margin-top: 0 !important;
    margin-right: 0 !important;
    margin-bottom: 16px !important;
    margin-left: 14px !important;

    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 20px !important;
    font-style: normal !important;
    font-weight: 600 !important;
    line-height: 1.25 !important;

    color: #252525 !important;

    text-transform: capitalize !important;
}


/* =========================================================
   4. MAIN WHITE CONTACT CARD
   Figma:
   white
   10px radius
   inner padding approx 30px
   ========================================================= */

.e-panel > .card-body > .row:nth-child(2) {
    margin-left: 22px !important;
    margin-right: 22px !important;

    padding: 30px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 10px !important;

    box-shadow: none !important;

    align-items: flex-start !important;
}


/* =========================================================
   5. CONTACT COLUMNS
   Existing Bootstrap columns stay intact.
   ========================================================= */

.e-panel > .card-body > .row:nth-child(2)
> .col-xl-4 {

    margin-bottom: 46px !important;

    padding-left: 15px !important;
    padding-right: 15px !important;
}


/* Bottom row does not need excessive trailing whitespace */
.e-panel > .card-body > .row:nth-child(2)
> .col-xl-4:nth-last-child(-n+2) {
    margin-bottom: 0 !important;
}


/* =========================================================
   6. INDIVIDUAL EXISTING CONTACT WRAPPER
   No card/background is added because Figma uses
   clean columns inside one large white panel.
   ========================================================= */

.e-panel > .card-body > .row:nth-child(2)
> .col-xl-4
> div {

    background: transparent !important;

    border: 0 !important;

    box-shadow: none !important;
}


/* =========================================================
   7. NAME + DESIGNATION AREA

   Figma:
   Name top
   Designation under it
   thin 270px separator
   ========================================================= */

.e-panel
.col-xl-4
> div
> .d-flex.align-items-center {

    width: 100% !important;
    max-width: 270px !important;

    min-height: 55px !important;

    margin: 0 !important;
    padding: 0 0 11px 0 !important;

    align-items: flex-start !important;

    background: transparent !important;

    border-top: 0 !important;
    border-left: 0 !important;
    border-right: 0 !important;

    border-bottom:
        1px solid rgba(98, 116, 142, 0.20) !important;

    box-shadow: none !important;
}


/* Existing wrapper remains */
.e-panel
.col-xl-4
.d-flex
.wrapper {

    width: 100% !important;

    margin: 0 !important;
    padding: 0 !important;
}


/* =========================================================
   8. CONTACT NAME
   Exact Figma:
   18px Medium
   #252525
   28px line
   ========================================================= */

.e-panel .nameStyle {
    margin: 0 !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 18px !important;
    font-style: normal !important;
    font-weight: 500 !important;
    line-height: 28px !important;

    color: #252525 !important;
}


/* Override Bootstrap text-dark etc. */
.e-panel .nameStyle.text-dark {
    color: #252525 !important;
}


/* =========================================================
   9. DESIGNATION
   Exact Figma:
   14px Regular
   #62748E
   20px line
   ========================================================= */

.e-panel .desiginationStyle {
    display: block !important;

    margin: 0 !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-style: normal !important;
    font-weight: 400 !important;
    line-height: 20px !important;

    color: #62748E !important;
}


/* Override Bootstrap text-muted */
.e-panel .desiginationStyle.text-muted {
    color: #62748E !important;
}


/* =========================================================
   10. DETAILS BODY
   Existing email / phone sections remain unchanged.
   ========================================================= */

.e-panel
.col-xl-4
> div
> .card-body {

    width: 100% !important;
    max-width: 270px !important;

    margin: 0 !important;

    padding-top: 24px !important;
    padding-right: 0 !important;
    padding-bottom: 0 !important;
    padding-left: 0 !important;

    background: transparent !important;

    border: 0 !important;

    box-shadow: none !important;
}


/* =========================================================
   11. EMAIL BLOCK
   Figma spacing between Email and Phone
   ========================================================= */

.e-panel
.col-xl-4
> div
> .card-body
> div:first-child {

    margin-bottom: 20px !important;
}


/* =========================================================
   12. DETAIL LABEL
   Exact Figma:
   12px Regular
   #62748E
   20px line
   ========================================================= */

.e-panel .label {
    margin: 0 !important;
    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 12px !important;
    font-style: normal !important;
    font-weight: 400 !important;
    line-height: 20px !important;

    color: #62748E !important;
}


/* =========================================================
   13. DETAIL VALUE
   Exact Figma:
   14px Regular
   ========================================================= */

.e-panel .labelValue {
    margin-top: 0 !important;
    margin-bottom: 0 !important;

    padding: 0 !important;

    font-family: 'Montserrat', sans-serif !important;

    font-size: 14px !important;
    font-style: normal !important;
    font-weight: 400 !important;
    line-height: 20px !important;

    color: #3C3B3B !important;

    word-break: normal !important;
    overflow-wrap: anywhere !important;
}


/* =========================================================
   14. KEEP BOOTSTRAP H6 FROM CHANGING FIGMA VALUES
   ========================================================= */

.e-panel h6.labelValue,
.e-panel .h6.labelValue {
    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 20px !important;

    color: #3C3B3B !important;
}


/* =========================================================
   15. REMOVE VISUAL BOOTSTRAP CARD TREATMENT ONLY
   Structure itself remains exactly present.
   ========================================================= */

.e-panel .shadow-none {
    box-shadow: none !important;
}


/* =========================================================
   16. DESKTOP — FIGMA 3 COLUMN LAYOUT
   Existing col-xl-4 already creates this layout.
   Only spacing is refined.
   ========================================================= */

@media (min-width: 1200px) {

    .e-panel > .card-body > .row:nth-child(2) {
        row-gap: 0 !important;
    }


    .e-panel > .card-body > .row:nth-child(2)
    > .col-xl-4 {

        flex: 0 0 33.333333% !important;

        width: 33.333333% !important;
        max-width: 33.333333% !important;
    }

}


/* =========================================================
   17. TABLET
   Existing content remains visible.
   ========================================================= */

@media (min-width: 768px) and (max-width: 1199.98px) {

    .e-panel > .card-body > .row:nth-child(2)
    > .col-lg-6 {

        flex: 0 0 50% !important;

        width: 50% !important;
        max-width: 50% !important;

        margin-bottom: 34px !important;
    }


    .e-panel
    .col-xl-4
    > div
    > .d-flex.align-items-center,

    .e-panel
    .col-xl-4
    > div
    > .card-body {

        max-width: 100% !important;
    }

}


/* =========================================================
   18. MOBILE
   No contact is hidden.
   Existing blocks simply stack vertically.
   ========================================================= */

@media (max-width: 767.98px) {

    .e-panel > .card-body > .row:first-child h1 {
        margin-left: 0 !important;
        margin-bottom: 16px !important;

        font-size: 20px !important;
    }


    .e-panel > .card-body > .row:nth-child(2) {
        margin-left: 0 !important;
        margin-right: 0 !important;

        padding: 20px !important;

        border-radius: 10px !important;
    }


    .e-panel > .card-body > .row:nth-child(2)
    > .col-xl-4 {

        width: 100% !important;
        max-width: 100% !important;

        flex: 0 0 100% !important;

        margin-bottom: 30px !important;

        padding-left: 0 !important;
        padding-right: 0 !important;
    }


    .e-panel > .card-body > .row:nth-child(2)
    > .col-xl-4:last-child {

        margin-bottom: 0 !important;
    }


    .e-panel
    .col-xl-4
    > div
    > .d-flex.align-items-center,

    .e-panel
    .col-xl-4
    > div
    > .card-body {

        width: 100% !important;
        max-width: 100% !important;
    }

}
    </style>
    <!-- Grid row -->
	{{-- old contact section start --}}
	
	<div class="row flex-lg-nowrap">
							<div class="col-12">
								<div class="row flex-lg-nowrap">
									<div class="col-12 mb-3">
										<div class="e-panel">
											<div class="card-body pb-2" style="padding-left:0 !important; padding-right: 0 !important; padding-top: 0px;margin-bottom: 24px;">
												<div class="row">
											<h1 style="color: #252525;
font-size: 20px;
font-style: normal;
margin-left:34px;
font-weight: 600;">Contact us  </h1>
										
												</div>
												<div class="row" style="background: white;margin-left:0 !important; margin-right:0 !important;">
                                       


														<div class="col-xl-4 col-lg-6">
														<div class=" p-0 shadow-none">
															<div class="d-flex align-items-center p-4" style="border-bottom: 1px solid #62748E33;padding-left:0px !important;">
																{{-- <div class="avatar avatar-lg brround d-block cover-image" data-image-src="../../assets/staffimages/843.jpg" >
																</div> --}}
																<div class="wrapper">
																	<p class="mb-0 mt-1 text-dark font-weight-semibold nameStyle">Salman Zafar</p>
																	<small class="text-muted desiginationStyle">Director IT</small>
																	
																</div>
														
															</div>
															<div class="card-body pt-2" style="padding-left: 0px;">
																<div class="" style="margin-bottom:25px;">
																	<div class="label">
																		Email Address
																	</div>
																	<div class="h6 mb-0 mt-1 labelValue">salman@bnu.edu.pk</div>
																</div>
																<div class="">
																<div class="label">
																	Phone Number
																</div>
																	<div class="h6 mb-0 mt-1 labelValue">38100156 (Ext: 110)</div>
																	<div class="h6 mb-0 mt-1 labelValue">0323-5014842</div>
																</div>
															</div>
													
														</div>
													</div>
														<div class="col-xl-4 col-lg-6">
														<div class=" p-0 shadow-none">
															<div class="d-flex align-items-center p-4" style="border-bottom: 1px solid #62748E33;padding-left:0px !important;">
																{{-- <div class="avatar avatar-lg brround d-block cover-image" data-image-src="../../assets/staffimages/843.jpg" >
																</div> --}}
																<div class="wrapper">
																	<p class="mb-0 mt-1 text-dark font-weight-semibold nameStyle">Naveed Idrees</p>
																	<small class="text-muted desiginationStyle">Assistant Manager Support</small>
																	
																</div>
														
															</div>
															<div class="card-body pt-2" style="padding-left: 0px;">
																<div class="" style="margin-bottom:25px;">
																	<div class="label">
																		Email Address
																	</div>
																	<div class="h6 mb-0 mt-1 labelValue">naveed@bnu.edu.pk</div>
																</div>
																<div class="">
																<div class="label">
																	Phone Number
																</div>
																	<div class="h6 mb-0 mt-1 labelValue">38100156 (Ext: 123)</div>
																	<div class="h6 mb-0 mt-1 labelValue">0300-4169621</div>
																</div>
															</div>
													
														</div>
													</div>
														<div class="col-xl-4 col-lg-6">
														<div class=" p-0 shadow-none">
															<div class="d-flex align-items-center p-4" style="border-bottom: 1px solid #62748E33;padding-left:0px !important;">
																{{-- <div class="avatar avatar-lg brround d-block cover-image" data-image-src="../../assets/staffimages/843.jpg" >
																</div> --}}
																<div class="wrapper">
																	<p class="mb-0 mt-1 text-dark font-weight-semibold nameStyle">Zain Ali</p>
																	<small class="text-muted desiginationStyle">Team Lead Software Projects</small>
																	
																</div>
														
															</div>
															<div class="card-body pt-2" style="padding-left: 0px;">
																<div class="" style="margin-bottom:25px;">
																	<div class="label">
																		Email Address
																	</div>
																	<div class="h6 mb-0 mt-1 labelValue">zain.ali@bnu.edu.pk</div>
																</div>
																{{-- <div class="">
																<div class="label">
																	Phone Number
																</div>
																	<div class="h6 mb-0 mt-1 labelValue">38100156 (Ext: 115)</div>
																	<div class="h6 mb-0 mt-1 labelValue">0300-4169621</div>
																</div> --}}
															</div>
													
														</div>
													</div>
														<div class="col-xl-4 col-lg-6">
														<div class=" p-0 shadow-none">
															<div class="d-flex align-items-center p-4" style="border-bottom: 1px solid #62748E33;padding-left:0px !important;">
																{{-- <div class="avatar avatar-lg brround d-block cover-image" data-image-src="../../assets/staffimages/843.jpg" >
																</div> --}}
																<div class="wrapper">
																	<p class="mb-0 mt-1 text-dark font-weight-semibold nameStyle">Bilal Ilyas</p>
																	<small class="text-muted desiginationStyle">Assistant Manager NOC</small>
																	
																</div>
														
															</div>
															<div class="card-body pt-2" style="padding-left: 0px;">
																<div class="" style="margin-bottom:25px;">
																	<div class="label">
																		Email Address
																	</div>
																	<div class="h6 mb-0 mt-1 labelValue">bilal.ilyas@bnu.edu.pk</div>
																</div>
																{{-- <div class=""> --}}
																{{-- <div class="label">
																	Phone Number
																</div>
																	<div class="h6 mb-0 mt-1 labelValue">38100156 (Ext: 107)</div>
																	<div class="h6 mb-0 mt-1 labelValue">0300-4169621</div>
																</div> --}}
															</div>
													
														</div>
													</div>
														<div class="col-xl-4 col-lg-6">
														<div class=" p-0 shadow-none">
															<div class="d-flex align-items-center p-4" style="border-bottom: 1px solid #62748E33;padding-left:0px !important;">
																{{-- <div class="avatar avatar-lg brround d-block cover-image" data-image-src="../../assets/staffimages/843.jpg" >
																</div> --}}
																<div class="wrapper">
																	<p class="mb-0 mt-1 text-dark font-weight-semibold nameStyle">CMS Help Desk</p>
																	<small class="text-muted desiginationStyle">Front Desk Support</small>
																	
																</div>
														
															</div>
															<div class="card-body pt-2" style="padding-left: 0px;">
																<div class="" style="margin-bottom:25px;">
																	<div class="label">
																		Email Address
																	</div>
																	<div class="h6 mb-0 mt-1 labelValue">cmshelpdesk@bnu.edu.pk</div>
																</div>
																<div class="">
																<div class="label">
																	Phone Number
																</div>
																	<div class="h6 mb-0 mt-1 labelValue">04238100156 Ext: 101, 102, 103 (SVAD)</div>
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
	{{-- old contact section end --}}
@stop

