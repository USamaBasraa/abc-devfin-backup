<?php

//print_r(auth()->user());

?>

<aside class="app-sidebar app-sidebar3">
	<div class="app-sidebar__user">
		<div class="dropdown user-pro-body text-center">
			<div class="user-pic">
				<img src="{{asset('/staffimages/'.auth()->user()->employee_id.'.jpg')}}" alt="user-img" class="avatar-xl rounded-circle mb-1">
			</div>
			<div class="user-info">
				<h5 class=" mb-1 font-weight-bold"><?= auth()->user()->full_name ?></h5>
				<span class="text-muted app-sidebar__user-name text-sm"><?= auth()->user()->designation ?></span>
			</div>
		</div>
	</div>
	<ul class="side-menu">
		<li class="slide">
			<a class="side-menu__item home_btn" data-bm="home" href="javascript:void(0);">
				<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
					<polyline points="9 22 9 12 15 12 15 22"></polyline>
				</svg>
				<span class="side-menu__label">Home</span></a>
		</li>
		<!-- <li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="#">
							<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
							<span class="side-menu__label">Faculty Report Generator</span></a>
							<ul class="slide-menu">
								<li class="sub-slide">
									<a class="sub-side-menu__item" data-toggle="sub-slide" href="{{url('/reportgenerator/reportgenerator')}}"><span class="sub-side-menu__label">Report Generator</span></a>
									<a class="sub-side-menu__item" data-toggle="sub-slide" href="{{url('teacher/courses/approval')}}"><span class="sub-side-menu__label">Faculty Course Attendance</span></a>
									<a class="sub-side-menu__item" data-toggle="sub-slide" href="{{url('teacher/courses/approval')}}"><span class="sub-side-menu__label">Attendance Summary Department Wise</span></a>
								</li>
							</ul>
						</li> -->
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
		<?php if (auth()->user()->group_id == 222 || auth()->user()->group_id == 2 || auth()->user()->group_id == 344 || auth()->user()->group_id == 644) { ?>
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
					<?php if(auth()->user()->employee_id == 2876 || auth()->user()->group_id == 644){?>}
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
			{{-- <li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="#">
							<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
							<span class="side-menu__label">Exam</span><i class="angle fa fa-angle-right"></i></a>
							<ul class="slide-menu">
								
								<li><a class="slide-item"  href="{{url('teacher/course_exam_bank')}}/<?= auth()->user()->employee_id ?>"><span>Question Bank</span></a></li>
	</ul>
	</li> --}}



<?php } else if (auth()->user()->group_id == 223 || auth()->user()->group_id == 364 || auth()->user()->group_id == 284 || auth()->user()->group_id == 824) { ?>

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
			<li><a class="slide-item" href="{{url('teacher/grading_status')}}"><span>Grading Status</span></a></li>
			<li><a class="slide-item" href="{{url('teacher/enrollment_status')}}"><span>Enrollment Status</span></a></li>
			<li><a class="slide-item" href="{{url('teacher/applicant_academic_profile')}}"><span>Applicant Academic Profile</span></a></li>
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
<!-- GoToStaff -->
<?php if (auth()->user()->group_id != 222 || auth()->user()->group_id != 223 ) { ?>
	<li class="slide">
		<a class="side-menu__item" data-toggle="slide" href="#">
			<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
				<polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
				<line x1="12" y1="22.08" x2="12" y2="12"></line>
			</svg>
			<span class="side-menu__label">QA </span><i class="angle fa fa-angle-right"></i></a>
		<ul class="slide-menu">
			<li><a class="slide-item" href="{{url('reports/faculty-evaluation')}}"><span>Faculty Evaluation Report</span></a></li>
		</ul>
	</li>

<?php } ?>
<!-- //// exam report  -->
<?php
if (in_array(auth()->user()->group_id, array(201,424,644,2))) {
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
		
			<li><a class="slide-item" href="{{url('reports/exam/cgpa-summary')}}"><span>CGPA Summary Report</span></a></li>
			
			<li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course Report</span></a></li>
		</ul>
	</li>
<?php } ?>
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
		<a class="side-menu__item" target="_blank" href="<?= config('app.staff_url') ?>/index.php?key=<?= auth()->user()->api_token ?>&time=<?= rand(999, 999999) ?>">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
				<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
				<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
			</svg>
			<span class="side-menu__label">Staff</span>
		</a>
	</li>

<?php } ?>


<?php if (auth()->user()->group_id == 644 || auth()->user()->group_id == 2 || auth()->user()->group_id == 284) { ?>

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




<?php if (auth()->user()->group_id == 284){?>
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

<?php if (auth()->user()->group_id == 644 || auth()->user()->employee_id == 1294 ||auth()->user()->group_id == 624 ||auth()->user()->group_id == 744 ||auth()->user()->group_id == 365 || auth()->user()->group_id==284){?>
<li class="slide">
		<a class="side-menu__item" target="_blank" href="{{url('staff/student-financial')}}">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
				<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
				<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
			</svg>
			<span class="side-menu__label">Student Financial</span>
		</a>
	</li>


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