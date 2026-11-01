<?php

//print_r(auth()->user());


?>

@push('styles')
<style>
	.app-sidebar3 {
		width: 270px;
		background: linear-gradient(185deg, #1b2141 0%, #0f1326 65%, #0a0d1a 100%);
		color: #e8ecff;
		box-shadow: 6px 0 24px rgba(10, 14, 40, 0.25);
		border-right: 1px solid rgba(255, 255, 255, 0.04);
	}

	.app-sidebar3 .side-menu {
		list-style: none;
		padding: 20px 18px 32px;
		margin: 0;
		overflow-y: auto;
		height: calc(100vh - 140px);
		scrollbar-width: thin;
		scrollbar-color: rgba(114, 135, 255, 0.35) transparent;
	}

	.app-sidebar3 .side-menu::-webkit-scrollbar {
		width: 6px;
	}

	.app-sidebar3 .side-menu::-webkit-scrollbar-track {
		background: transparent;
	}

	.app-sidebar3 .side-menu::-webkit-scrollbar-thumb {
		background: rgba(114, 135, 255, 0.35);
		border-radius: 999px;
	}

	.app-sidebar__user {
		padding: 32px 24px 18px;
		background: linear-gradient(180deg, rgba(255, 255, 255, 0.06) 0%, rgba(255, 255, 255, 0.02) 100%);
		border-bottom: 1px solid rgba(255, 255, 255, 0.05);
		text-align: center;
	}

	.app-sidebar__user .user-pic .avatar-xl {
		box-shadow: 0 12px 30px rgba(11, 15, 31, 0.35);
		border: 3px solid rgba(255, 255, 255, 0.35);
		width: 96px;
		height: 96px;
		object-fit: cover;
	}

	.app-sidebar__user .user-info h5 {
		color: #ffffff;
		font-size: 16px;
		margin-bottom: 4px;
	}

	.app-sidebar__user .user-info span {
		color: rgba(226, 232, 255, 0.7);
		font-size: 13px;
	}

	.side-menu__item {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 11px 20px 11px 16px;
		font-size: 14px;
		color: rgba(214, 219, 255, 0.92);
		border-radius: 14px;
		transition: all 0.25s ease;
		position: relative;
		text-decoration: none;
		font-weight: 500;
	}

	.side-menu__item .side-menu__label {
		flex: 1;
		line-height: 1.35;
		letter-spacing: 0.01em;
	}

	.side-menu__item .angle,
	.side-menu__item .fa-angle-right,
	.side-menu__item .fe-chevron-right {
		display: none !important;
	}

	.side-menu__item::after {
		content: "\f105";
		font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome";
		font-weight: 900;
		font-size: 12px;
		color: rgba(214, 219, 255, 0.55);
		margin-left: auto;
		transition: transform 0.25s ease, color 0.25s ease;
	}

	.side-menu__item:hover,
	.side-menu__item:focus {
		background: rgba(89, 106, 255, 0.22);
		color: #ffffff;
		box-shadow: inset 0 0 0 1px rgba(126, 142, 255, 0.38);
		text-decoration: none;
	}

	.side-menu__item:focus-visible {
		outline: 3px solid rgba(143, 161, 255, 0.55);
		outline-offset: 2px;
	}

	.side-menu__item:hover::after,
	.side-menu__item:focus::after {
		color: #ffffff;
		transform: translateX(2px);
	}

	.side-menu__icon,
	img.side-menu__icon {
		width: 22px;
		height: 22px;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		color: inherit;
		opacity: 0.95;
	}

	.side-menu__icon svg,
	.side-menu__icon img,
	.side-menu__icon i,
	img.side-menu__icon {
		max-width: 100%;
		max-height: 100%;
		object-fit: contain;
	}

	.side-menu__icon i {
		font-size: 16px;
		line-height: 1;
	}

	.side-menu__item[data-state="open"]::after {
		transform: rotate(90deg);
		color: #ffffff;
	}

	.slide-menu {
		list-style: none;
		padding-left: 18px;
		margin: 8px 0 12px;
		border-left: 1px dashed rgba(154, 167, 255, 0.35);
	}

	.slide-menu .slide-item {
		display: flex;
		align-items: center;
		padding: 9px 18px 9px 14px;
		color: rgba(214, 219, 255, 0.85);
		border-radius: 12px;
		transition: all 0.2s ease;
		font-size: 13px;
		text-decoration: none;
	}

	.slide-menu .slide-item span {
		flex: 1;
	}

	.slide-menu .slide-item::after {
		content: "\f105";
		font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome";
		font-weight: 900;
		font-size: 11px;
		color: rgba(214, 219, 255, 0.45);
		margin-left: auto;
		transition: transform 0.25s ease, color 0.25s ease;
	}

	.slide-menu .slide-item:hover,
	.slide-menu .slide-item:focus {
		background: rgba(97, 115, 255, 0.18);
		color: #ffffff;
		text-decoration: none;
		box-shadow: inset 0 0 0 1px rgba(126, 142, 255, 0.25);
	}

	.slide-menu .slide-item:hover::after,
	.slide-menu .slide-item:focus::after {
		color: #ffffff;
		transform: translateX(2px);
	}

	.slide-menu .sub-slide {
		padding: 0;
	}

	.slide-menu .sub-slide .slide-item {
		padding-left: 22px;
	}

	@media (max-height: 768px) {
		.app-sidebar__user {
			padding: 24px 18px 14px;
		}

		.app-sidebar3 .side-menu {
			height: calc(100vh - 118px);
		}
	}

	@media (max-width: 991px) {
		.app-sidebar3 {
			width: 100%;
		}

		.app-sidebar__user {
			display: flex;
			align-items: center;
			gap: 16px;
			text-align: left;
		}

		.app-sidebar__user .user-pic .avatar-xl {
			width: 72px;
			height: 72px;
		}
	}
</style>
@endpush

<aside class="app-sidebar app-sidebar3" role="navigation" aria-label="Primary Sidebar">
	<div class="app-sidebar__user">
		<div class="dropdown user-pro-body text-center">
			<div class="user-pic">
				<img src="{{ asset('/staffimages/' . auth()->user()->employee_id . '.jpg') }}" alt="{{ auth()->user()->full_name }} Avatar" class="avatar-xl rounded-circle mb-1" loading="lazy" decoding="async">
			</div>
			<div class="user-info">
				<h5 class=" mb-1 font-weight-bold"><?= auth()->user()->full_name ?></h5>
				<span class="text-muted app-sidebar__user-name text-sm"><?= auth()->user()->designation ?></span>
			</div>
		</div>
	</div>
	<ul class="side-menu" role="list">
		<li class="slide">
			<a class="side-menu__item home_btn" data-bm="home" href="javascript:void(0);">
				<img src="{{ asset('images/icons/home.png') }}"
				alt="Home Icon"
				class="side-menu__icon"
				width="20" height="20">
				<span class="side-menu__label">Home</span></a>
		</li>
         <?php if(auth()->user()->group_id == 644  ||  1==1  ){?>		

							<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon" aria-hidden="true" focusable="false">
										<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
										<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
									</svg>
                                <span class="side-menu__label">Store Procurement</span>
                                <i class="angle fa fa-angle-right" aria-hidden="true"></i>
                            </a>
						<ul class="slide-menu" role="list">
                             <li><a class="slide-item" href="{{url('/vendor-category-create')}}"><span>Budgetary Head Categories</span></a></li>
                            <li><a class="slide-item" href="{{url('/vendor-create')}}"><span>Add Vendor</span></a></li>
                            <li><a class="slide-item" href="{{url('/approval-master/create')}}"><span>Master Approval Form</span></a></li>

							    <li><a class="slide-item" href="{{url('/budget')}}"><span> Create Department Budget </span></a></li>
							    <li><a class="slide-item" href="{{url('/budgets/list')}}"><span>Department Budgets</span></a></li>
                                <li><a class="slide-item" href="{{url('/quotation-requests')}}"><span>Department Head Approval</span></a></li>
                                <li><a class="slide-item" href="{{url('/parent-dept-review')}}"><span>Supervising Dept Review</span></a></li>

                                <li><a class="slide-item" href="{{url('/sp/quotations')}}"><span>Submit Vendor Quotation</span></a></li>


								<li><a class="slide-item" href="{{url('/quotation-request-create')}}"><span>Quotation Requests</span></a></li>
								{{-- <li><a class="slide-item" href="{{url('/quotation-request-purchase')}}"><span>Select Quotation Request</span></a></li> --}}


								<li><a class="slide-item" href="{{url('/recommended-quotations')}}"><span>Purchase Order Approval Requests</span></a></li>
								<li><a class="slide-item" href="{{url('/reappropriation/approvals')}}"><span>Special VC Approval </span></a></li>

								{{-- <li><a class="slide-item" href="{{url('/vendor/quotations')}}"><span>Vendor Quotations</span></a></li> --}}
								 {{-- <li><a class="slide-item" href="{{url('purchase-order-approval')}}"><span>Purchase Order Approval</span></a></li> --}}
								<li><a class="slide-item" href="{{url('gatepass')}}"><span>Purchase Order Gate Pass</span></a></li>
								<li><a class="slide-item" href="{{url('gatepass/list')}}"><span>Gate Pass List</span></a></li>
								<li><a class="slide-item" href="{{url('/sp_assets')}}"><span>Assets List</span></a></li>

                                {{-- Incorrect	Correct
                                Budgetry	Budgetary
                                Vendor Add	Add Vendor
                                Approvel	Approval
                                Approvels	Approvals
                                Recmondation	Recommendation
                                Approvel bodies	Approval Bodies --}}

								{{-- <li><a class="slide-item" target="__blank" href="{{url('/purchase-order-create')}}"><span>Purchase Order</span></a></li> --}}
								{{-- <li><a class="slide-item" target="__blank" href="{{url('/vendor-profiling')}}"><span>Vendor Profiling</span></a></li> --}}
						</ul>
					</li>

					<?php   }?>
	@if(auth()->user()->group_id == 825)
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
                <span class="side-menu__label">TA</span><i class="angle fa fa-angle-right" aria-hidden="true"></i></a>
            <ul class="slide-menu" role="list">
                <!-- <li><a class="slide-item"  href="{{url('reports/faculty/list')}}"><span>Faculty Assurance Report</span></a></li> https://devstudent.bnu.edu.pk/login-user-by-users-->
                <li><a class="slide-item" href="{{url('move-to-student?token='.auth()->user()->api_token)}}"><span>Students</span></a></li>
            </ul>
        </li>
    @else
				<!-- vc if condition start -->
				<?php
				if(auth()->user()->group_id=="8"){ ?>
					<?php if(auth()->user()->username == 'orip' || auth()->user()->username == 'mansoor.ahmad' || auth()->user()->username == 'babar.jamil' || auth()->user()->username == 'ayeda.hamed'){?>

							<li class="slide">
								<a class="side-menu__item"  href="{{url('staff/task-list')}}">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
										<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
										<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
									</svg>
									<span class="side-menu__label">Task Management</span>
								</a>
							</li>
							<?php if(auth()->user()->username == 'ayeda.hamed'){?>
								<li class="slide">
									<a class="side-menu__item" href="{{url('UniversityEvent/register')}}">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
											<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
											<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
										</svg>
										<span class="side-menu__label">University Event</span>
									</a>
								</li>
							<?php }?>
							@if(auth()->user()->username == 'babar.jamil')
								<li class="slide">
									<a class="side-menu__item" href="{{url('eportal-dashboard')}}">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
											<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
											<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
										</svg>
										<span class="side-menu__label">  E-Portal Requests</span>
									</a>
								</li>
							@endif

					<?php   }?>
					<?php if(auth()->user()->username == 'moeedyusuf' || auth()->user()->username == 'sumair.shafique'){?>
					<li class="slide">
						<a class="side-menu__item " data-toggle="slide" href="#" role="button" aria-haspopup="true" aria-expanded="false">
							<span class="side-menu__icon" aria-hidden="true"><i class="fa fa-book mt-1"></i></span>
							<span class="side-menu__label">Reports</span><i class="angle fa fa-angle-right"></i>
						</a>
						<ul class="slide-menu" role="list">
							<li>
								<a class="slide-item" href="{{url('admission_applicant_status')}}"><span>Admission Status</span></a>
							</li>
							<li>
								<a class="slide-item" href="{{url('studentCheckNBS')}}"><span>Student NBS List</span></a>
							</li>
							<li>
								<a class="slide-item" href="{{url('studentHealthReport')}}"><span>Student Health Report</span></a>
							</li>
							<li>
								<a class="slide-item" href="{{url('studentZeroHealthReport')}}"><span>Student Zero Week Report</span></a>
							</li>
							<li>
								<a class="slide-item" href="{{url('gymSubscriptionReport')}}"><span>Gym Report</span></a>
							</li>
							<li>
								<a class="slide-item" href="{{url('hostel-survey-summary')}}"><span>Hostel Survey Report</span></a>
							</li>
						</ul>
					</li>

					<li class="slide link_for_web">
						<a class="side-menu__item" href="{{url('staff/todo-list')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
								<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
								<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
							</svg>
							<span class="side-menu__label">To-Do List</span>
						</a>
					</li>
					<li class="slide link_for_mobile">
						<a class="side-menu__item"  href="{{url('staff/ProjectBoard')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
								<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
								<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
							</svg>
							<span class="side-menu__label">To-Do List</span>
						</a>
					</li>
					<li class="slide link_for_mobile">
						<a class="side-menu__item"  href="{{url('staff/personal_todo-list')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
								<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
								<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
							</svg>
							<span class="side-menu__label">Personal To-Do List</span>
						</a>
					</li>
					<li class="slide link_for_mobile">
						<a class="side-menu__item"  href="{{url('staff/immediate_todo-list')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
								<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
								<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
							</svg>
							<span class="side-menu__label">Immediate To-Do List</span>
						</a>
					</li>
					<li class="slide">
						<a class="side-menu__item"  href="{{url('staff/task-list')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
								<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
								<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
							</svg>
							<span class="side-menu__label">Task Management</span>
						</a>
					</li>
					<li class="slide ">
						<a class="side-menu__item" data-toggle="slide" href="#">

							<span class="side-menu__label"><i class="fa fa-list side-menu__icon mt-1"></i>Subscriptions</span><i class="angle fe fe-chevron-right"></i>
						</a>
						<ul class="slide-menu">
							<li class="sub-slide">
								<a class="slide-item" href="{{url('staff-subscription-form')}}"><span>Gym Subscription</span></a>
							</li>
						</ul>

					</li>
					{{-- VC reports generator --}}
					<li class="slide">
						<a class="side-menu__item" data-toggle="slide" href="#">
							<span><i class="fa fa-file-text-o side-menu__icon mt-1"></i></span> <!-- Using 'file-text-o' for reports -->
							<span class="side-menu__label">Report Generator</span>
							<i class="angle fa fa-angle-right"></i>
						</a>
						<ul class="slide-menu">
							<li>
								<a class="slide-item" href="/reports/bnu-admission-reports-generator">
									<span>BNU Reports Generator</span> <!-- Updated label for clarity -->
								</a>
							</li>


							<li>
							<a class="slide-item" href="/student-profile-rec">
								<span>Student Data Connection</span> <!-- Updated label for clarity -->
								</a>
							</li>



						</ul>
					</li>
					<li class="slide">
						<a class="side-menu__item"  href="{{url('car-sticker-report')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
								<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
								<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
							</svg>
							<span class="side-menu__label">Car Sticker Report</span>
						</a>
					</li>
					<li class="slide">
						<a class="side-menu__item" href="{{url('grading_status_report')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
								<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
								<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
							</svg>
							<span class="side-menu__label">Grading Status</span>
						</a>
					</li>
					<?php } ?>
					<?php if(auth()->user()->username != 'mansoor.ahmad' && auth()->user()->username != 'babar.jamil' && auth()->user()->username != 'ayeda.hamed'){?>
					<li class="slide">
						<a class="side-menu__item"  href="{{url('vc/eportal-tracking')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
								<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
								<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
							</svg>
							<span class="side-menu__label">E-Portal Tracking</span>
						</a>
					</li>
					<li class="slide">
						<a class="side-menu__item" href="{{url('vc/eportal-dashboard')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
								<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
								<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
							</svg>
							<span class="side-menu__label">E-Portal Requests</span>
						</a>
					</li>
					<li class="slide">
						<a class="side-menu__item" href="{{url('vc/eportal-report')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
								<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
								<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
							</svg>
							<span class="side-menu__label">E-Portal Report</span>
						</a>
					</li>
					<li class="slide">
						<a class="side-menu__item" href="{{url('vc/eportal-summary')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
								<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
								<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
							</svg>
							<span class="side-menu__label">E-Portal Summary</span>
						</a>
					</li>

					<li class="slide">
						<a class="side-menu__item " data-toggle="slide" href="#">
						<span><i class="fa fa-share-alt side-menu__icon mt-1"></i></span>
						<span class="side-menu__label">SharePoint</span><i class="angle fa fa-angle-right"></i></a>
					<ul class="slide-menu">
						<li><a class="slide-item" target="__blank" href="/share-point-portal"><span>Faculty SharePoint</span></a></li>
						<li><a class="slide-item" target="__blank" rel="noopener" href="https://share.bnu.edu.pk/sites/students/">Student SharePoint</a></li>
					</ul>
				</li>


					<?php } ?>
					<?php if(auth()->user()->username == 'aizaz.hussain' && auth()->user()->group_id == '8'){?>
						<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="#">
								<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
									<polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
									<line x1="12" y1="22.08" x2="12" y2="12"></line>
								</svg>
								<span class="side-menu__label">QA</span><i class="angle fa fa-angle-right"></i></a>
							<ul class="slide-menu">
								<!-- <li><a class="slide-item"  href="{{url('reports/faculty/list')}}"><span>Faculty Assurance Report</span></a></li> -->
								<li><a class="slide-item" href="{{url('reports/faculty-evaluation')}}"><span>Faculty Evaluation Report</span></a></li>
							</ul>
						</li>
						<li class="slide">
							<a class="side-menu__item " data-toggle="slide" href="#">
							<span><i class="fa fa-book  side-menu__icon mt-1"></i></span>
							<span class="side-menu__label"> Short Courses</span><i class="angle fa fa-angle-right"></i></a>
							<ul class="slide-menu">
								<li>
									<a class="slide-item" href="{{url('short_courses')}}"><span>Add Course</span></a>
								</li>
								<li>
									<a class="slide-item" href="{{url('shortCourse_applicants')}}"><span>Applicants List</span></a>
								</li>
							</ul>
						</li>
						<li class="slide">
							<a class="side-menu__item" href="{{url('UniversityEvent/register')}}">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
									<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
									<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
								</svg>
								<span class="side-menu__label">University Event</span>
							</a>
						</li>

					<?php } ?>

				<?php

				}else{

				?>
					<?php if(auth()->user()->group_id == 644 || auth()->user()->group_id == 121){?>



								<li class="slide">
										<a class="side-menu__item" target="_blank" href="{{url('university-leaving-report')}}">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
												<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
												<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
											</svg>
											<span class="side-menu__label">Staff Exit Report</span>
										</a>
									</li>



							<li class="slide">
									<a class="side-menu__item" target="_blank" href="{{url('staff-exit-request-list')}}">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
											<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
											<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
										</svg>
										<span class="side-menu__label">Staff Exit Request List</span>
									</a>
								</li>




					<?php   }?>
					<?php if(auth()->user()->group_id == 644 ){?>

						<li class="slide">
								<a class="side-menu__item" target="_blank" href="{{url('announcements')}}">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
										<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
										<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
									</svg>
									<span class="side-menu__label">Mobile Announcements</span>
								</a>
							</li>

					<?php   }?>


		<?php if(auth()->user()->group_id == 644 || auth()->user()->group_id == 964){?>
			<li class="slide">
				<a class="side-menu__item"  href="{{url('leave-requests')}}">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
						<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
						<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
					</svg>
					<span class="side-menu__label">Hostel Night leave</span>
				</a>
			</li>
		<?php   }?>

		<?php if(auth()->user()->group_id == 644 || auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'asbah.rehman' || auth()->user()->username == 'zainabsanik' || auth()->user()->username == 'saqib.jamil' || auth()->user()->username == 'esha.abid' || auth()->user()->username =='ahmad.arfeen' || auth()->user()->username == 'asif_nadeem' || auth()->user()->username == 'muhammad.nadeem' ||auth()->user()->username == 'farzana' || auth()->user()->username == 'hassan.farooq'  || auth()->user()->username =='abdul.waheed' || auth()->user()->username =='talha.sarfraz' || auth()->user()->username == 'haider.raza' || auth()->user()->username == 'qasim.ali'){?>

			<li class="slide">
				<a class="side-menu__item" data-toggle="slide" href="#">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
						<polyline points="9 22 9 12 15 12 15 22"></polyline>
					</svg>
					<span class="side-menu__label">Student Financial</span><i class="angle fa fa-angle-right"></i>
				</a>
				<ul class="slide-menu">

					<?php if(auth()->user()->group_id == 644 || auth()->user()->username == 'farzana' || auth()->user()->username == 'hassan.farooq' || auth()->user()->username =='talha.sarfraz' || auth()->user()->username =='abdul.waheed' || auth()->user()->username == 'haider.raza' || auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'sumaira.arshad' || auth()->user()->username == 'asbah.rehman' || auth()->user()->username == 'zainabsanik'){?>
					<li><a class="slide-item" href="{{url('finance/fee-policy')}}"><span>Enrollment Policy</span></a></li>
					<li><a class="slide-item" href="{{url('master-scholarship-criteria')}}"><span>Scholarship Criteria</span></a></li>
					<li><a class="slide-item" href="{{url('finance/assign-scholarship-index')}}"><span>Assign Scholarship</span></a></li>
					<li><a class="slide-item" href="{{url('finance/verify-scholarship-index')}}"><span>Scholarship Approval</span></a></li>
					<li><a class="slide-item" href="{{url('finance/other-scholarship-index')}}"><span>Other Scholarships</span></a></li>
					<li><a class="slide-item" href="{{url('studentLedger')}}"><span>Student Ledger</span></a></li>
					<li><a class="slide-item" href="{{url('challan-report')}}"><span>Challan Report</span></a></li>


					<?php   } if(auth()->user()->group_id == 644 || auth()->user()->username == 'saqib.jamil' || auth()->user()->username == 'esha.abid' || auth()->user()->username =='ahmad.arfeen' || auth()->user()->username == 'asif_nadeem' || auth()->user()->username == 'muhammad.nadeem' || auth()->user()->username == 'qasim.ali'){?>


					<li><a class="slide-item" href="{{url('master-fee-structure')}}"><span>Master Fee Structure</span></a></li>
					<li><a class="slide-item" href="{{url('master-scholarship-form')}}"><span>Add Scholarship</span></a></li>
					<li><a class="slide-item" href="{{url('master-hostelfee-structure')}}"><span>Hostel Fee Structure</span></a></li>
					<li><a class="slide-item" href="{{url('master-feeadjustment-form')}}"><span>Master Fee Adjustment</span></a></li>
					<li><a class="slide-item" href="{{url('feeadjustment')}}"><span>Fee Adjustment</span></a></li>
					<li><a class="slide-item" href="{{url('generate-fee-challan')}}"><span>Generate Bulk Challan</span></a></li>
					<li><a class="slide-item" href="{{url('finance/generate-single-challan')}}"><span>Generate Single Challan</span></a></li>
					<li><a class="slide-item" href="{{url('challan-report')}}"><span>Challan Report</span></a></li>
					<li><a class="slide-item" href="{{url('master-installment-form')}}"><span>Master Installment Form</span></a></li>
					<li><a class="slide-item" href="{{url('student-manual-paid-mark')}}"><span>Manual Paid Mark</span></a></li>
					<li><a class="slide-item" href="{{url('studentLedger')}}"><span>Student Ledger</span></a></li>
					<li><a class="slide-item" href="{{url('finance/assign-scholarship-index')}}"><span>Assign Scholarship</span></a></li>
					<li><a class="slide-item" href="{{url('finance/verify-scholarship-index')}}"><span>Scholarship Approval</span></a></li>

					<?php   }if(auth()->user()->group_id == 644 || auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'asbah.rehman' || auth()->user()->username == 'zainabsanik'){?>
						<li><a class="slide-item" href="{{url('studentLedger')}}"><span>Student Ledger</span></a></li>
						<li><a class="slide-item" href="{{url('admission-scholarship-criteria')}}"><span>Admission Scholarship Criteria</span></a></li>
						<li><a class="slide-item" href="{{url('admission-individual-scholarship-criteria')}}"><span>Individual Scholarship</span></a></li>
						<li><a class="slide-item" href="{{url('finance/verify-admissionScholarship')}}"><span>Admission Scholarship Approval</span></a></li>


						<?php } ?>

				</ul>
			</li>

			<?php } ?>





				<?php if(auth()->user()->username == 'farzana' ){?>
					<li class="slide">
						<a class="side-menu__item"  href="{{url('car-sticker-report')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
								<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
								<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
							</svg>
							<span class="side-menu__label">Car Sticker Report</span>
						</a>
					</li>

				<?php   }?>
				<?php if(auth()->user()->username == 'bilal.saleem' || auth()->user()->username == 'farzana'){?>
						<li class="slide">
							<a class="side-menu__item"  href="{{url('admission_applicant_data')}}">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
									<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
									<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
								</svg>
								<span class="side-menu__label">Applicant Data Report</span>
							</a>
						</li>

				<?php   }?>
				<li class="slide link_for_web">
					<a class="side-menu__item" href="{{url('dean-honor')}}">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
							<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
							<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
						</svg>
						<span class="side-menu__label">Dean Honor List</span>
					</a>
				</li>
				<li class="slide link_for_web">
					<a class="side-menu__item" href="{{url('staff/todo-list')}}">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
							<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
							<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
						</svg>
						<span class="side-menu__label">To-do List</span>
					</a>
				</li>
				<li class="slide">
					<a class="side-menu__item"  href="{{url('staff/task-list')}}">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
							<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
							<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
						</svg>
						<span class="side-menu__label">Task Management</span>
					</a>
				</li>

				<?php if (auth()->user()->group_id == 825) { ?>
					<li class="slide">
						<a class="side-menu__item" data-toggle="slide" href="#">
							<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
								<polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
								<line x1="12" y1="22.08" x2="12" y2="12"></line>
							</svg>
							<span class="side-menu__label">TA</span><i class="angle fa fa-angle-right"></i></a>
						<ul class="slide-menu">
							<!-- <li><a class="slide-item"  href="{{url('reports/faculty/list')}}"><span>Faculty Assurance Report</span></a></li> https://devstudent.bnu.edu.pk/login-user-by-users-->
							<li><a class="slide-item" href="{{url('move-to-student?token='.auth()->user()->api_token)}}"><span>Students</span></a></li>
						</ul>
					</li>
				<?php } ?>
				<?php
				if(auth()->user()->group_id != 884){
					if (auth()->user()->group_id == 222 || auth()->user()->group_id == 2 || auth()->user()->group_id == 344 || auth()->user()->group_id == 644) { ?>
						<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="#">
								<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
									<path d="M22 12A10 10 0 0 0 12 2v10z"></path>
								</svg>
								<span class="side-menu__label">Reports</span><i class="angle fa fa-angle-right"></i></a>
							<ul class="slide-menu">
								<li><a class="slide-item" href="{{url('/reportgenerator/reportgenerator')}}"><span>Report Generator</span></a></li>
								<!--<li><a class="slide-item" href="{{url('teacher/grading_status')}}"><span>Grading Status</span></a></li>
											<li><a class="slide-item" href="{{url('teacher/enrollment_status')}}"><span>Enrollment Status</span></a></li>
											<li><a class="slide-item" href="{{url('teacher/applicant_academic_profile')}}"><span>Applicant Academic Profile</span></a></li>-->
								<li><a class="slide-item" href="{{url('teacher/course_attendance_detail')}}"><span>Course Attendance Detail</span></a></li>
								<li><a class="slide-item" href="{{url('teacher/hr_attendance_report')}}"><span>HR Attendance Report</span></a></li>
								<?php if(auth()->user()->username == 'mehmil.ishtiaq'){?>
									<li><a class="slide-item" href="{{url('teacher/applicant_academic_profile_status')}}"><span>Applicant Status</span></a></li>
								<?php } ?>
								<?php if(auth()->user()->employee_id == 2876 || auth()->user()->group_id == 644){?>
									<li><a class="slide-item" href="{{url('teacher/academic_history2')}}"><span>Academic History</span></a></li>
								<?php } ?>

								<!-- <li><a class="slide-item" href="{{url('teacher/attendance_summary_dept')}}"><span>Attendance Summary Department Wise</span></a></li>
											<li><a class="slide-item" href="{{url('teacher/student_semester_performance')}}"><span>Student Semester Performance</span></a></li>
											<li><a class="slide-item" href="{{url('teacher/faculty_course_attendance_report')}}"><span>Faculty Course Attendance</span></a></li>
											<li><a class="slide-item" href="{{url('teacher/acadmic_history')}}"><span>Acadmic History</span></a></li>
										-->
							</ul>
						</li>
						<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="#">
								<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
									<polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
									<line x1="12" y1="22.08" x2="12" y2="12"></line>
								</svg>
								<span class="side-menu__label">QA</span><i class="angle fa fa-angle-right"></i></a>
							<ul class="slide-menu">
								<!-- <li><a class="slide-item"  href="{{url('reports/faculty/list')}}"><span>Faculty Assurance Report</span></a></li> -->
								<li><a class="slide-item" href="{{url('reports/faculty-evaluation')}}"><span>Faculty Evaluation Report</span></a></li>
							</ul>
						</li>
					<?php } else if (auth()->user()->group_id == 223 || auth()->user()->group_id == 364 || auth()->user()->group_id == 284 || auth()->user()->group_id == 824 || auth()->user()->group_id == 122) { ?>
						<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="#">
								<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
									<path d="M22 12A10 10 0 0 0 12 2v10z"></path>
								</svg>
								<span class="side-menu__label">Reports</span><i class="angle fa fa-angle-right"></i></a>
							<ul class="slide-menu">
								<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
								<li><a class="slide-item" href="{{url('/reportgenerator/reportgenerator')}}"><span>Report Generator</span></a></li>
								<li><a class="slide-item" href="{{url('/faculty/course_offering')}}"><span>Course Offering List</span></a></li>
								<li><a class="slide-item" href="{{url('teacher/grading_status')}}"><span>Grading Status</span></a></li>
								<li><a class="slide-item" href="{{url('teacher/enrollment_status')}}"><span>Enrollment Status</span></a></li>
								<li><a class="slide-item" href="{{url('teacher/applicant_academic_profile')}}"><span>Applicant Academic Profile</span></a></li>
								<li><a class="slide-item" href="{{url('teacher/applicant_academic_profile_status')}}"><span>Applicant Status</span></a></li>
								<li><a class="slide-item" href="{{url('teacher/student_assessment_report')}}"><span>Student Assessment Report</span></a></li>
								<li><a class="slide-item" href="{{url('teacher/attendance_summary_dept')}}"><span>Attendance Summary Department Wise</span></a></li>
								<li><a class="slide-item" href="{{url('teacher/course_attendance_detail')}}"><span>Course Attendance Detail</span></a></li>
								<li><a class="slide-item" href="{{url('teacher/student_semester_performance')}}"><span>Student Semester Performance</span></a></li>
								<li><a class="slide-item" href="{{url('teacher/faculty_course_attendance_report')}}"><span>Faculty Course Attendance</span></a></li>
								<li><a class="slide-item" href="{{url('teacher/acadmic_history')}}"><span>Acadmic History</span></a></li>
								<li><a class="slide-item" href="{{url('teacher/faculty_course_detail')}}"><span>Faculty Course Content</span></a></li>
								<?php if(auth()->user()->employee_id == 1294 or auth()->user()->employee_id == 6455){ ?>
								<li><a class="slide-item" href="{{url('teacher/hr_attendance_report')}}"><span>HR Attendance Report</span></a></li>
								<?php } ?>
								<?php if(auth()->user()->username == 'hifsa.farooq' or auth()->user()->username == 'bilal.saleem'){ ?>
								<li><a class="slide-item" href="{{url('teacher/admission_enrollment_status')}}"><span>Department wise Report</span></a></li>
								<?php } ?>
							</ul>
						</li>
						<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="#">
								<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
									<polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
									<line x1="12" y1="22.08" x2="12" y2="12"></line>
								</svg>
								<span class="side-menu__label">QA </span><i class="angle fa fa-angle-right"></i></a>
							<ul class="slide-menu">
								<li><a class="slide-item" href="{{url('reports/faculty/list')}}"><span>Faculty Assurance Report</span></a></li>
								<li><a class="slide-item" href="{{url('reports/faculty-evaluation')}}"><span>Faculty Evaluation Report</span></a></li>
							</ul>
						</li>
						<?php if(auth()->user()->username == 'ruhi.khalid'){?>
							<li class="slide">
								<a class="side-menu__item" data-toggle="slide" href="#">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
										<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
										<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
									</svg>
									<span class="side-menu__label">Dean</span></a>
								<ul class="slide-menu">
									<li><a href="{{url('teacher/courses/approval')}}" class="slide-item">Result Approval</a></li>
								</ul>
							</li>
						<?php }else{ ?>
							<li class="slide">
								<a class="side-menu__item" data-toggle="slide" href="#">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
										<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
										<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
									</svg>
									<span class="side-menu__label">HOD</span></a>
								<ul class="slide-menu">
									<li><a href="{{url('teacher/courses/approval')}}" class="slide-item">Result Approval</a></li>
								</ul>
							</li>
						<?php } ?>
						<?php if(auth()->user()->group_id != 284 || auth()->user()->group_id == 122) { ?>
							<li class="slide">
								<a class="side-menu__item" target="_blank" href="{{url('/approve_ta')}}">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
										<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
										<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
									</svg>
									<span class="side-menu__label">Approve TA</span>
								</a>
							</li>
						<?php } ?>

					<?php }?>







							<?php


					$groupId = auth()->user()->group_id;
					// Group IDs: 644 - Software Team, 223 - Dean, 364 - Deans SCIT, 445 - HOD Ijaz
					// if (in_array($groupId, [644, 223, 364, 445]) || auth()->user()->username == 'hifsa.farooq') {
						if (in_array($groupId, [644, 223, 364, 445]) || auth()->user()->username == 'hifsa.farooq'  || auth()->user()->username == 'zara.amjad' || auth()->user()->username == 'arez.ali'|| auth()->user()->username == 'ahsan.fazal' || auth()->user()->username == 'sara.assad' || auth()->user()->username == 'ghammaz.husnain' || auth()->user()->username == 'zeeshan.sarwar' || auth()->user()->username == 'nouman.ali' || auth()->user()->username == 'amna.humayun' || auth()->user()->username == 'nabeel.ahsan' || auth()->user()->username == 'osama.tariq' || auth()->user()->username == 'haider.ali1' || auth()->user()->username == 'huda.sarfraz' || auth()->user()->username == 'farhat_nadeem' || auth()->user()->username == 'asim.irshad' || auth()->user()->username == 'noreen.khalid' || auth()->user()->username == 'ameer.hamza' || auth()->user()->username == 'farhan.ali' || auth()->user()->username == 'talia.noreen' || auth()->user()->username == 'muhammad.ali1') {
				?>
					<li class="slide">
						<a class="side-menu__item" data-toggle="slide" href="#">
							<!-- SVG Icon -->
							<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
								<polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
								<line x1="12" y1="22.08" x2="12" y2="12"></line>
							</svg>
							<span class="side-menu__label">Student Progress</span>
							<i class="angle fa fa-angle-right"></i>
						</a>
						<ul class="slide-menu">
							<li>
								<a class="slide-item" href="<?php echo url('/reports/report-generator'); ?>">
									<span>Student Progress Monitoring Portal</span>
								</a>
							</li>
						</ul>
					</li>
				<?php
					}
				?>
					<!-- GoToStaff -->
					<?php if (auth()->user()->group_id != 222 || auth()->user()->group_id != 223 ) {
						if (in_array(auth()->user()->group_id, array(141, 584,201))) {?>
						<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="#">
								<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
									<polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
									<line x1="12" y1="22.08" x2="12" y2="12"></line>
								</svg>
								<span class="side-menu__label">QA </span><i class="angle fa fa-angle-right"></i></a>
							<ul class="slide-menu">
								<li><a class="slide-item" href="{{url('reports/faculty/list')}}"><span>Faculty Assurance Report</span></a></li>
								<li><a class="slide-item" href="{{url('reports/faculty-evaluation')}}"><span>Faculty Evaluation Report</span></a></li>
							</ul>
						</li>
						<?php }?>
					<?php }
				}
					if(auth()->user()->group_id == 884 || auth()->user()->group_id == 644 || auth()->user()->group_id == 2){?>
				<li class="slide">
						<a class="side-menu__item " data-toggle="slide" href="#">
						<span><i class="fa fa-car side-menu__icon mt-1"></i></span>
						<span class="side-menu__label">Car Pooling</span><i class="angle fa fa-angle-right"></i></a>
					<ul class="slide-menu">
						<li><a class="slide-item" href="{{url('/car-pooling')}}"><span>Ride Registration</span></a></li>
						<li><a class="slide-item" target="__blank" href="{{url('/car-pooling-pdf')}}"><span>Generate Report</span></a></li>
					</ul>
				</li>
				<?php if(auth()->user()->username != 'ashfaque.hussain'){?>
					<li class="slide">
						<a class="side-menu__item" target="_blank" href="{{url('/car-registration')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
								<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
								<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
							</svg>
							<span class="side-menu__label">Car Registration</span>
						</a>
					</li>
				<?php } ?>
				<li class="slide">
					<a class="side-menu__item" target="_blank" href="{{url('/search-registered-car')}}">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
							<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
							<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
						</svg>
						<span class="side-menu__label">Search Registered Car</span>
					</a>
				</li>
				<?php }else{ ?>
					<li class="slide">
						<a class="side-menu__item " data-toggle="slide" href="#">
						<span><i class="fa fa-car side-menu__icon mt-1"></i></span>
						<span class="side-menu__label">Car Pooling</span><i class="angle fa fa-angle-right"></i></a>
					<ul class="slide-menu">
						<li><a class="slide-item" href="{{url('/car-pooling')}}"><span>Ride Registration</span></a></li>
					</ul>
				</li>
				<?php } ?>
		<!-- //// exam report  -->
		<?php
		if (in_array(auth()->user()->group_id, array(201, 424, 644, 2,1064))) {
		?>
			<li class="slide">
				<a class="side-menu__item" data-toggle="slide" href="#">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
						<rect x="3" y="3" width="7" height="7"></rect>
						<rect x="14" y="3" width="7" height="7"></rect>
						<rect x="14" y="14" width="7" height="7"></rect>
						<rect x="3" y="14" width="7" height="7"></rect>
					</svg>
					<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
					<polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
					<line x1="12" y1="22.08" x2="12" y2="12"></line>
					</svg>
					<span class="side-menu__label">EX. Reports </span><i class="angle fa fa-angle-right"></i>
				</a>
				<ul class="slide-menu">
					<?php if(auth()->user()->username != 'maham.nadeem'){?>
						<li><a class="slide-item" href="{{url('reports/exam/cgpa-summary')}}"><span>CGPA Summary Report</span></a></li>
					<?php } ?>
					<li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course Report</span></a></li>
					<li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary Report</span></a></li>
				</ul>
			</li>
		<?php } ?>
		<!-- peer mentorship-->
		<?php
		if (in_array(auth()->user()->group_id, array(644)) || auth()->user()->username == 'neha.quasim') {
		?>
			<li class="slide">
				<a class="side-menu__item" data-toggle="slide" href="#">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
						<circle cx="12" cy="7" r="4" stroke-width="2"/>
						<path d="M4 21v-2a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v2"  stroke-width="2"/>
					</svg>
					<span class="side-menu__label">Peer Mentorship</span>
					<i class="angle fa fa-angle-right"></i>
				</a>
				<ul class="slide-menu">
					<?php {?>
						<li><a class="slide-item" href="{{url('pm-scholarship-students')}}"><span>Scholarship students</span></a></li>
					<?php } ?>
					{{-- <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course Report</span></a></li>
					<li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary Report</span></a></li> --}}
				</ul>
				<ul class="slide-menu">
					<?php {?>
						<li><a class="slide-item" href="{{url('mentors_list')}}"><span>Mentor List</span></a></li>
					<?php } ?>
					{{-- <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course Report</span></a></li>
					<li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary Report</span></a></li> --}}
				</ul>
				<ul class="slide-menu">
					<?php {?>
						<li><a class="slide-item" href="{{url('admin-mentors-list')}}"><span>Admin Mentor List</span></a></li>
					<?php } ?>
					{{-- <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course Report</span></a></li>
					<li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary Report</span></a></li> --}}
				</ul>
				<ul class="slide-menu">
					<?php {?>
						<li><a class="slide-item" href="{{url('be_mentors_list')}}"><span>Be Mentor List</span></a></li>
					<?php } ?>
					{{-- <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course Report</span></a></li>
					<li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary Report</span></a></li> --}}
				</ul>

				<ul class="slide-menu">
					<?php {?>
						<li><a class="slide-item" href="{{url('pm-students-requests')}}"><span>Student Request List</span></a></li>
					<?php } ?>
					{{-- <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course Report</span></a></li>
					<li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary Report</span></a></li> --}}
				</ul>

				<ul class="slide-menu">
					<?php {?>
						<li><a class="slide-item" href="{{url('intro-session')}}"><span>Sessions</span></a></li>
					<?php } ?>
					{{-- <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course Report</span></a></li>
					<li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary Report</span></a></li> --}}
				</ul>




			</li>
		<?php } ?>
		<!-- peer mentorship ends here-->
		<?php
		if (in_array(auth()->user()->group_id, array(141, 584,305))) {
		?>
			<li class="slide">
				<a class="side-menu__item" data-toggle="slide" href="#">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
						<rect x="3" y="3" width="7" height="7"></rect>
						<rect x="14" y="3" width="7" height="7"></rect>
						<rect x="14" y="14" width="7" height="7"></rect>
						<rect x="3" y="14" width="7" height="7"></rect>
					</svg>
					<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
					<polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
					<line x1="12" y1="22.08" x2="12" y2="12"></line>
					</svg>
					<span class="side-menu__label">EX. Reports </span><i class="angle fa fa-angle-right"></i>
				</a>
				<ul class="slide-menu">
					<li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course Report</span></a></li>

				</ul>
			</li>
		<?php } ?>
		<li class="slide ">
			<a class="side-menu__item" data-toggle="slide" href="#">

				<span class="side-menu__label"><i class="fa fa-list side-menu__icon mt-1"></i>Subscriptions</span><i class="angle fe fe-chevron-right"></i>
			</a>
			@if (auth()->user()->group_id == 644 || auth()->user()->id == 23858 || auth()->user()->username == 'somia.maqsood' || auth()->user()->username == 'abdul.waheed' || auth()->user()->username == 'talha.sarfraz') {{-- 644 is group id of ITRC and 23858 is user id of Rabia Shabir--}}
				<ul class="slide-menu">
					<li class="sub-slide">
						<a class="slide-item" href="{{url('sports-applicant-form')}}"><span>Add Sports/Scholarship Students</span></a>
					</li>
				</ul>
			@endif
			@if (auth()->user()->group_id == 644 || auth()->user()->group_id == 324) {{-- 644 is group id of ITRC and 324 is group id of accounts --}}
				<ul class="slide-menu">
					<li class="sub-slide">
						<a class="slide-item" href="{{url('subscription-master-form')}}"><span>Subscription Master Form</span></a>
					</li>
				</ul>
				<ul class="slide-menu">
					<li class="sub-slide">
						<a class="slide-item" href="{{url('user-subscriptions')}}"><span>Student Subscriptions</span></a>
					</li>
				</ul>
			@endif
			@if (auth()->user()->group_id == 644 || auth()->user()->group_id == 324 || auth()->user()->username == 'saqib.zia' || auth()->user()->username == 'ahmer.sayeed'  || auth()->user()->username == 'asif.khan'){{-- 644 is group id of ITRC and 324 is group id of accounts --}}
				<ul class="slide-menu">
					<li class="sub-slide">
						<a class="slide-item" href="{{url('staff-subscriptions')}}"><span>Staff Subscriptions</span></a>
					</li>
				</ul>
			@endif
			@if (auth()->user()->group_id == 644)
				<ul class="slide-menu">
					<li class="sub-slide">
						<a class="slide-item" href="{{url('rfid-assignment-form')}}"><span>RFID Setup</span></a>
					</li>
				</ul>
			@endif
			<ul class="slide-menu">
				<li class="sub-slide">
					<a class="slide-item" href="{{url('staff-subscription-form')}}"><span>Gym Subscription</span></a>
				</li>
			</ul>

		</li>




		<?php
			$allowedGroupIds = [644,]; // 644 - Software Team
			$allowedUsernames = [
				'haider.raza', 'aiman.adnan','somia.maqsood','noreen','zainabsanik', 'asbah.rehman', 'sidra.tanveer','zonia.akbar','abdul.waheed','hassan.farooq'
			];
			if (in_array(auth()->user()->group_id, $allowedGroupIds) || in_array(auth()->user()->username, $allowedUsernames)) {
		?>
				<li class="slide">
					<a class="side-menu__item" data-toggle="slide" href="#">
						<!-- Better Icon for Documents -->
						<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M8 2H16C17.1 2 18 2.9 18 4V20C18 21.1 17.1 22 16 22H8C6.9 22 6 21.1 6 20V4C6 2.9 6.9 2 8 2Z"></path>
							<polyline points="14 2 14 6 10 6 10 2"></polyline>
						</svg>
						<span class="side-menu__label">Missing Documents</span>
						<i class="angle fa fa-angle-right"></i>
					</a>
					<ul class="slide-menu">
						<li>
							<a class="slide-item" href="<?php echo url('/admission_qualification'); ?>">
								<span>Missing Documents Verification</span>
							</a>
						</li>
					</ul>
				</li>
		<?php
			}
		?>




		{{-- Library Side Bars --}}
		<?php if (auth()->user()->group_id == 764 || auth()->user()->group_id == 644 || auth()->user()->group_id == 2) { ?>
			<li class="slide ">
				<a class="side-menu__item" data-toggle="slide" href="#">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
						<line x1="4" y1="21" x2="4" y2="14"></line>
						<line x1="4" y1="10" x2="4" y2="3"></line>
						<line x1="12" y1="21" x2="12" y2="12"></line>
						<line x1="12" y1="8" x2="12" y2="3"></line>
						<line x1="20" y1="21" x2="20" y2="16"></line>
						<line x1="20" y1="12" x2="20" y2="3"></line>
						<line x1="1" y1="14" x2="7" y2="14"></line>
						<line x1="9" y1="8" x2="15" y2="8"></line>
						<line x1="17" y1="16" x2="23" y2="16"></line>
					</svg>
					<span class="side-menu__label">Library</span><i class="angle fe fe-chevron-down"></i>
				</a>
				<ul class="slide-menu">
					<li class="sub-slide">
						<a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label" style="font-weight:bold;">Acquisition</span></a>
					</li>
				</ul>
				<ul class="slide-menu">

					<li class="sub-slide">
						<a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label " style="font-weight:bold;">Cataloging</span><i class="sub-angle fe fe-chevron-down"></i></a>
						<ul class="sub-slide-menu">
							<li class="slide">
								<ul class="slide-menu">
									<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
									<li><a class="slide-item" href="{{url('/library/catalog')}}"><span>Catalog</span></a></li>

								</ul>
								<ul class="slide-menu">
									<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
									<li><a class="slide-item" href="{{url('/library/add-book-item')}}"><span>New Item Entry</span></a></li>

								</ul>
								<ul class="slide-menu">
									<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
									<li><a class="slide-item" href="{{url('/library/view-book-item')}}"><span>Update / View Book Entry</span></a></li>

								</ul>
								<ul class="slide-menu">
									<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
									<li><a class="slide-item" href="{{url('/library/spine-barcode')}}"><span>Spine and Barcode</span></a></li>

								</ul>
								<ul class="slide-menu">
									<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
									<li><a class="slide-item" href="{{url('/library/daily-library-visitor')}}"><span>Daily Library Visitor</span></a></li>

								</ul>
							</li>
						</ul>
					</li>
					<li class="sub-slide">
						<a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label " style="font-weight:bold;">Circulation</span><i class="sub-angle fe fe-chevron-down"></i></a>
						<ul class="sub-slide-menu">
							<ul class="slide-menu">
								<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
								<li><a class="slide-item" href="{{url('/library/librarycirculation')}}"><span>Circulation</span></a></li>

							</ul>
							<ul class="slide-menu">
								<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
								<li><a class="slide-item" href="{{url('/library/library-criteria')}}"><span>Books issuing Criteria</span></a></li>

							</ul>
							<ul class="slide-menu">
								<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
								<li><a class="slide-item" href="{{url('/library/circulation-book-history')}}"><span> Circulation History</span></a></li>

							</ul>
							<ul class="slide-menu">
								<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
								<li><a class="slide-item" href="{{url('/library/overdue-book')}}"><span> Over Due Books</span></a></li>

							</ul>
							<ul class="slide-menu">
								<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
								<!-- <li><a class="slide-item" href="{{url('/library/datacorrection')}}"><span>Data Correction</span></a></li> -->

							</ul>
							<ul class="slide-menu">
								<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
								<li><a class="slide-item" href="{{url('/library/return-book')}}"><span>Fine Management</span></a></li>

							</ul>


					</li>

				</ul>
			<li class="sub-slide">
				<a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label " style="font-weight:bold;">Reminders</span><i class="sub-angle fe fe-chevron-down"></i></a>
				<ul class="sub-slide-menu">
					<li class="slide">
						<ul class="slide-menu">
							<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
							<li><a class="slide-item" href="{{url('/library/inbox')}}"><span>Inbox</span></a></li>

						</ul>



					</li>
				</ul>
			</li>

			<li class="sub-slide">
				<a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label " style="font-weight:bold;">Reports</span><i class="sub-angle fe fe-chevron-down"></i></a>
				<ul class="sub-slide-menu">
					<li class="slide">
						<ul class="slide-menu">
							<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
							<li><a class="slide-item" href="{{url('/library/report_cataloging')}}"><span>Cataloging Reports</span></a></li>
							{{-- <li><a class="slide-item" href="{{url('/library/inbox')}}"><span>Circulation Reports</span></a></li> --}}

						</ul>



					</li>
				</ul>
			</li>
			<ul class="slide-menu">
				<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
				<li><a class="slide-item" href="{{url('/library/room-allocation')}}"><span>Library Room Allocation</span></a></li>

				</ul>



			</li>
			</ul>
			</li>
		<?php } ?>
		{{-- Library Side Bar Ends here --}}





		<!-- GoToStaff -->
		<?php if (!(in_array(2, [222,825])) && auth()->user()->group_id != 825) {
		//if (auth()->user()->group_id !=222 &&auth()->user()->group_id !=223  && auth()->user()->group_id != 825) {
		?>
			<li class="slide">
				<a class="side-menu__item" target="_blank" href="<?= config('app.staff_url') ?>/index.php?key=<?= auth()->user()->api_token ?>&username=<?= auth()->user()->username ?>&time=<?= rand(999, 999999) ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
						<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
						<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
					</svg>
					<span class="side-menu__label">Staff</span>
				</a>
			</li>

		<?php } ?>


		<?php if (auth()->user()->group_id == 644 || auth()->user()->group_id == 2 || auth()->user()->group_id == 284 || auth()->user()->group_id == 122) { ?>

			<li class="slide">
				<a class="side-menu__item" target="_blank" href="{{url('/enrollment-start-stop')}}">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
						<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
						<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
					</svg>
					<span class="side-menu__label">Enrollment Start Stop</span>
				</a>
			</li>

		<?php } ?>




		<?php if (auth()->user()->group_id == 284 || auth()->user()->group_id == 122){?>
			<li class="slide">
				<a class="side-menu__item" target="_blank" href="{{url('/attendance_update_ex')}}">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
						<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
						<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
					</svg>
					<span class="side-menu__label">Attendance Exemption</span>
				</a>
			</li>
		<?php } ?>

		<?php if (auth()->user()->group_id == 644 || auth()->user()->employee_id == 1294 ||auth()->user()->group_id == 624 ||auth()->user()->group_id == 744 ||auth()->user()->group_id == 365 || auth()->user()->group_id==284 || auth()->user()->group_id == 122){?>
		<li class="slide">
				<a class="side-menu__item" target="_blank" href="{{url('staff/student-financial')}}">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
						<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
						<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
					</svg>
					<span class="side-menu__label">Student Financial</span>
				</a>
			</li>
			<?php } ?>


			<?php
		$allowed_usernames = [
			'somia.maqsood',
			'abdul.waheed',
			'talha.sarfraz',
			'sidra.tanveer',
			'asbah.rehman',
			'zainabsanik',
			'saqib.jamil',
			'esha.abid',
			'asif_nadeem',
			'ahmad.arfeen'
		];

		if (in_array(auth()->user()->username, $allowed_usernames) || auth()->user()->group_id == 644) {
		?>
				<li class="slide">
					<a class="side-menu__item" target="_blank" href="{{url('student-record/student-record')}}">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
							<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
							<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
						</svg>
						<span class="side-menu__label">Student Record Mgt</span>
					</a>
				</li>
			<?php } ?>

			<?php if(auth()->user()->group_id == '404' || auth()->user()->group_id == '644'){?>
				<li class="slide">
					<a class="side-menu__item" href="{{url('/softwarelist')}}">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
							<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
							<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
						</svg>
						<span class="side-menu__label">Software Entry </span>
					</a>
				</li>
			<?php } ?>

			<?php if(auth()->user()->group_id == '181' || auth()->user()->group_id == '644'){?>
				<li class="slide">
					<a class="side-menu__item" href="{{url('/emp-employee-details')}}">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
							<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
							<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
						</svg>
						<span class="side-menu__label">Faculty Profile </span>
					</a>
				</li>
			<?php } ?>

		<?php if (auth()->user()->group_id == 644 || auth()->user()->employee_id == 1294 || auth()->user()->group_id == 324){?>
			<li class="slide">
				<a class="side-menu__item" target="_blank" href="{{url('accounts/student-financial')}}">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
						<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
						<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
					</svg>
					<span class="side-menu__label">Student Financial Accounts</span>
				</a>
			</li>
			<li class="slide">
				<a class="side-menu__item" target="_blank" href="{{url('staff/student-financial')}}">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
						<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
						<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
					</svg>
					<span class="side-menu__label">Student Financial</span>
				</a>
			</li>
			<?php } ?>






				<li class="slide">
					<a class="side-menu__item " data-toggle="slide" href="#">
					<span><i class="fa fa-desktop  side-menu__icon mt-1"></i></span>
					<span class="side-menu__label">E-Portal</span><i class="angle fa fa-angle-right"></i></a>
				<ul class="slide-menu">


				@if(auth()->user()->username == 'haider.raza' || auth()->user()->group_id == 644)
					<li><a class="slide-item" href="{{ url('university-holidays-management') }}"><span>University Holidays</span></a></li>
				@endif



				<?php
						$userDeptId = auth()->user()->dept_id;
						$userGroupId = auth()->user()->group_id;
						$allowedDeptIds = [41, 23, 24, 141, 61];
						$allowedGroupId = '644';
						if (in_array($userDeptId, $allowedDeptIds) || $userGroupId == $allowedGroupId) {?>
					{{-- <li><a class="slide-item" href="{{url('eportal-request')}}"><span>Admin to School</span></a></li> --}}

					<?php  } ?>

						<!-- <?php if(auth()->user()->username == 'zonia.akbar'){?>
			<?php }elseif(auth()->user()->username == 'farzana'){?>
				<?php } ?> -->
				<li><a class="slide-item" href="{{url('eportal-approval')}}"><span>E-Portal Approval</span></a></li>
				<li><a class="slide-item" href="{{url('eportal-dashboard')}}"><span>E-Portal Requests</span></a></li>
				<?php if(auth()->user()->username == 'saqib.jamil' || auth()->user()->username == 'ahmad.arfeen' || auth()->user()->username == 'bilal.saleem' || auth()->user()->username == 'esha.abid' || auth()->user()->username == 'qasim.ali'){?>
					<li><a class="slide-item" href="{{url('eportal-report-student')}}"><span>Student Exit Report</span></a></li>
				<?php } ?>

				<?php if(auth()->user()->username == 'farzana' || auth()->user()->username == 'noreen'){ ?>

					<li><a class="slide-item" href="{{url('eportal-report-staff')}}"><span>E-Portal Report</span></a></li>
				<?php } ?>
				</ul>
			</li>

			<?php if (auth()->user()->username == 'ahmad.arfeen' || auth()->user()->username == 'asif_nadeem' || auth()->user()->group_id == 644 || auth()->user()->group_id == 324 || auth()->user()->username == 'farhan.iftikhar' || auth()->user()->username == 'qasim.ali'){?>
						<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="#">
								<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
									<path d="M22 12A10 10 0 0 0 12 2v10z"></path>
								</svg>
								<span class="side-menu__label">Account Reports</span><i class="angle fa fa-angle-right"></i></a>
							<ul class="slide-menu">
								<!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
								<li><a class="slide-item" target="_blank" href="{{url('fine-challan-student-list')}}">
									<span class="side-menu__label">Online Payment List</span></a>
								</li>
								<li><a class="slide-item" target="_blank" href="{{url('blocked-student-list')}}">
									<span class="side-menu__label">Blocked Students</span></a>
								</li>
								<li><a class="slide-item" target="_blank" href="{{url('students_fee_challan')}}">
									<span class="side-menu__label">Students Fee Challan</span></a>
								</li>
								<?php if (auth()->user()->username == 'ahmad.arfeen' || auth()->user()->username == 'asif_nadeem' || auth()->user()->group_id == 644 || auth()->user()->username == 'farhan.iftikhar'){?>
									<li><a class="slide-item"  href="{{url('car-sticker-report')}}">
										<span class="side-menu__label">Car Sticker Report</span>
									</a></li>
									<li><a  class="slide-item" href="{{url('admission_applicant_data')}}">
										<span class="side-menu__label">Applicant Data Report</span>
									</a></li>
								<?php } ?>

								<?php if(auth()->user()->employee_id == 1294 or auth()->user()->employee_id == 6455){ ?>
								<li><a class="slide-item" href="{{url('teacher/hr_attendance_report')}}"><span>HR Attendance Report</span></a></li>
								<?php } ?>
							</ul>
						</li>
			<?php } ?>


			<?php if (auth()->user()->group_id == 644 || auth()->user()->group_id == 142){?>
				<li class="slide">
					<a class="side-menu__item" target="_blank" href="{{url('exam/allow-exam-ips')}}">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
							<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
							<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
						</svg>
						<span class="side-menu__label">Allow Exams IPs</span>
					</a>
				</li>
			<?php } ?>



			<li class="slide">
				<a class="side-menu__item " data-toggle="slide" href="#">
				<span><i class="fa fa-share-alt side-menu__icon mt-1"></i></span>
				<span class="side-menu__label">SharePoint</span><i class="angle fa fa-angle-right"></i></a>
			<ul class="slide-menu">


				<li><a class="slide-item" target="__blank" href="/share-point-portal"><span>Faculty SharePoint</span></a></li>
				<li><a class="slide-item" target="__blank" rel="noopener" href="https://share.bnu.edu.pk/sites/students/">Student SharePoint</a></li>


				<?php
					// Define an array of allowed employee IDs these all are cordincators id's
					define('ALLOWED_EMPLOYEE_IDS', [
						553, 1313, 1273, 276, 226, 259, 512, 2815,
						2855, 7335, 5375, 8157, 9194, 9096, 9258,
						9241, 9325, 9328, 9385, 16
					]);

					// Get the authenticated user's details
					$user = auth()->user();
					$userEmployeeId = $user->employee_id;
					$userGroupId = $user->group_id;

					// Check if the user has the right employee ID or group ID is 644
					if (in_array($userEmployeeId, ALLOWED_EMPLOYEE_IDS) || $userGroupId == 644) { ?>
						<li>
							<a class="slide-item" target="_blank" href="http://share.bnu.edu.pk/sites/Careers">
								<span>Job Portal</span>
							</a>
						</li>
					<?php } ?>





			</ul>
		</li>



		<li class="slide">
			<a class="side-menu__item" data-toggle="slide" href="#">
				<span><i class="fa fa-money side-menu__icon mt-1"></i></span>
				<span class="side-menu__label">Student Fine</span><i class="angle fa fa-angle-right"></i>
			</a>
			<ul class="slide-menu">
				<li><a class="slide-item" href="/students-fine"><span>Apply Student Fine</span></a></li>

				<?php if (auth()->user()->group_id == 644 || auth()->user()->username == 'asif_nadeem') { ?>
					<li><a class="slide-item" href="/student-fine-categories"><span>Add New Category</span></a></li>
					<li><a class="slide-item" href="/student-fine-sub-categories"><span>Add New Fine</span></a></li>
					<li><a class="slide-item" href="/fine-challan-student-list"><span>Student List</span></a></li>
				<?php } ?>

				<?php
				$usernamesFines = ['asif_nadeem', 'esha.abid', 'saqib.jamil', 'ahmad.arfeen','amir.sheikh'];
				$roUsernamesFines = ['somia.maqsood', 'talha.sarfraz', 'zonia.akbar', 'haider.raza','abdul.waheed','qasim.ali'];
				if (auth()->user()->group_id == 644 || in_array(auth()->user()->username, $usernamesFines) || in_array(auth()->user()->username, $roUsernamesFines)) { ?>
					<li><a class="slide-item" href="/fine-challan-generated-list"><span>Students Fine Details</span></a></li>
				<?php } ?>
			</ul>
		</li>



		<?php if (auth()->check() && (auth()->user()->group_id == 644 ||
			in_array(auth()->user()->username, ['asif_nadeem', 'noreen', 'farzana', 'sidra.tanveer', 'zainabsanik']))) { ?>
			<li class="slide">
				<a class="side-menu__item" data-toggle="slide" href="#">
					<span><i class="fa fa-file-text-o side-menu__icon mt-1"></i></span>
					<span class="side-menu__label">Report Generator</span>
					<i class="angle fa fa-angle-right"></i>
				</a>
				<ul class="slide-menu">
					<li>
						<a class="slide-item" href="/reports/bnu-admission-reports-generator">
							<span>BNU Report Generator</span>
						</a>
					</li>
					<!--<li>
						<a class="slide-item" href="/student-profile-rec">
						<span>Student Data Connection</span>
						</a>
					</li>-->
				</ul>
			</li>
		<?php } ?>






			<?php if(auth()->user()->group_id == 644){?>
				<li class="slide">
					<a class="side-menu__item" data-toggle="slide" href="#">
						<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
							<polyline points="9 22 9 12 15 12 15 22"></polyline>
						</svg>
						<span class="side-menu__label">University Events</span><i class="angle fa fa-angle-right"></i></a>
					<ul class="slide-menu">
						<li><a class="slide-item" href="{{url('UniversityEvent/register')}}"><span>Add University Event</span></a></li>

						<li><a class="slide-item" href="{{url('UniversityEvent/event-approval')}}"><span> University Event List</span></a></li>

					</ul>
				</li>
			<?php } ?>


		<?php if(auth()->user()->username == 'haider.raza' || auth()->user()->username == 'zonia.akbar' || auth()->user()->username == 'rabia.shabbir' || auth()->user()->username == 'bilal.saleem' || auth()->user()->username == 'zaeem' || auth()->user()->username == 'umar.imam' || auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'sheeraz' || auth()->user()->username == 'hina.razzaq' || auth()->user()->username == 'fariha.nadeem' || auth()->user()->username == 'ali.kashif'){?>
			<li class="slide">
				<a class="side-menu__item" data-toggle="slide" href="#">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
						<polyline points="9 22 9 12 15 12 15 22"></polyline>
					</svg>
					<span class="side-menu__label">University Events</span><i class="angle fa fa-angle-right"></i></a>
				<ul class="slide-menu">
					<li><a class="slide-item" href="{{url('UniversityEvent/register')}}"><span>Add University Event</span></a></li>
					<?php if(auth()->user()->username == 'haider.raza'  || auth()->user()->username == 'bilal.saleem' || auth()->user()->username == 'zonia.akbar' ){?>
						<li><a class="slide-item" href="{{url('UniversityEvent/event-approval')}}"><span> University Event List</span></a></li>
					<?php } ?>
				</ul>
			</li>
			<?php if(auth()->user()->username == 'haider.raza' || auth()->user()->username == 'bilal.saleem'){?>
				<li class="slide">
					<a class="side-menu__item" target="_blank" href="{{url('admission_qualification')}}">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
							<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
							<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
						</svg>
						<span class="side-menu__label">Applicant Data Uploads</span>
					</a>
				</li>
			<?php } ?>
		<?php } ?>




		<?php if(auth()->user()->username == 'umar.imam' || auth()->user()->username == 'bilal.saleem'){?>
			<li class="slide">
				<a class="side-menu__item" href="{{url('alumni')}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
						<polyline points="9 22 9 12 15 12 15 22"></polyline>
					</svg>
					<span class="side-menu__label">Alumni</span>
				</a>
			</li>
		<?php } ?>



					<?php if(auth()->user()->username == 'saqib.zia' || auth()->user()->group_id == 644 || auth()->user()->username == 'ali.sher'){?>
						<li class="slide">
								<a class="side-menu__item" target="_blank" href="{{url('lowerstaff_exist_form')}}">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
										<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
										<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
									</svg>
									<span class="side-menu__label">Lower Staff Exist Form</span>
								</a>
							</li>
					<?php } ?>


		<?php
		}

		//vc end else part--------------------------------------
		?>

		<li class="slide">
			<a class="side-menu__item" target="_blank" href="{{url('staff-exit-interview-form')}}">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
					<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
					<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
				</svg>
				<span class="side-menu__label">Staff Exit Interview Form</span>
			</a>
		</li>


		<?php if(auth()->user()->username != 'mansoor.ahmad'){?>
				<li class="slide">
					<a class="side-menu__item " data-toggle="slide" href="#">
					<span><i class="fa fa-desktop  side-menu__icon mt-1"></i></span>
					<span class="side-menu__label"> MCM Portal</span><i class="angle fa fa-angle-right"></i></a>
					<ul class="slide-menu">

						<li><a class="slide-item" href="{{url('admin/eportal-dashboard')}}"><span>MCM Portal Dashboard</span></a></li>
						<?php

						$userEmpType = auth()->user()->sis_emp_type;
						$userGroupId = auth()->user()->group_id;
						$allowedEmpType = [4,5];
						$allowedGroupId = '644';


						if (in_array($userEmpType, $allowedEmpType) || $userGroupId == $allowedGroupId  )  {?>
							<li><a class="slide-item" href="{{url('eportal-request')}}"><span>MCM Portal Request</span></a></li>

							<?php  } ?>

					</ul>
					</li>
					<li class="slide">
						<a class="side-menu__item" target="_blank" href="{{url('visiting_faculty')}}">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
								<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
								<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
							</svg>
							<span class="side-menu__label">Visiting Faculty</span>
						</a>
					</li>
			<li class="slide">
				<a class="side-menu__item" target="_blank" href="{{url('staff-exit-interview-form')}}">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
						<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
						<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
					</svg>
					<span class="side-menu__label">Staff Exit Interview Form</span>
				</a>
			</li>
			<?php } ?>
	@endif
{{-- TA IF ENDED --}}
</ul>
<div class="app-sidebar-help">
	<div class="dropdown text-center">
		<div class="help d-flex">
			<a href="#" class="nav-link p-0 help-dropdown" data-toggle="dropdown">
				<span class="font-weight-bold">Help Info</span> <i class="fa fa-angle-down ml-2"></i>
			</a>
			<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow p-4">
				<div class="border-bottom pb-3">
					<h4 class="font-weight-bold">Help</h4>
					<a class="text-primary d-block" href="#">Contact us</a>
					<a class="text-primary d-block" href="#">cmshelpdesk@bnu.edu.pk</a>
					<a class="text-primary d-block" href="#">Tel: 04238100156 Ext:300, 102, 101</a>
				</div>
			</div>
			<div class="ml-auto">
				<a class="nav-link icon p-0" href="#">
					<svg class="header-icon" x="1008" y="1248" viewBox="0 0 24 24" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
						<path opacity=".3" d="M12 6.5c-2.49 0-4 2.02-4 4.5v6h8v-6c0-2.48-1.51-4.5-4-4.5z"></path>
						<path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-11c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2v-5zm-2 6H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6zM7.58 4.08L6.15 2.65C3.75 4.48 2.17 7.3 2.03 10.5h2a8.445 8.445 0 013.55-6.42zm12.39 6.42h2c-.15-3.2-1.73-6.02-4.12-7.85l-1.42 1.43a8.495 8.495 0 013.54 6.42z"></path>
					</svg>
					<span class="pulse "></span>
				</a>
			</div>
		</div>
	</div>
</div>
</aside>
<script>
const isMobile = window.matchMedia('(max-width: 768px)').matches;

const slideForWeb = document.querySelector('.link_for_web');
const slideForMobile = document.querySelector('.link_for_mobile');

if (isMobile) {
  slideForWeb.style.display = 'none';
  slideForMobile.style.display = 'block';
} else {
  slideForWeb.style.display = 'block';
  slideForMobile.style.display = 'none';
}
</script>
