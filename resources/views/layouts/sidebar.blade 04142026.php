<?php

// print_r(auth()->user());
?>
<style>
    @media (min-width: 768px) {
        .app-sidebar.app-sidebar3 {
            top: 60px !important;
        }
    }

    /* Base sidebar link styling - Consistent padding and spacing */
    .app-sidebar3 .side-menu__item {
        padding: 8px 19px !important;
        margin: 2px 0 !important;
        display: flex !important;
        align-items: center !important;
        border-radius: 999px !important;
        transition: all 0.2s ease !important;
        line-height: 1.4 !important;
    }

    /* Consistent spacing between menu items */
    .app-sidebar3 .side-menu>.slide {
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Remove extra spacing when slide contains submenu */
    .app-sidebar3 .slide>.slide-menu {
        margin-top: 0 !important;
    }

    /* Active sidebar link styling - Purple pill shape */
    .app-sidebar3 .side-menu__item.active {
        background-color: #CD9A8F !important;
        color: #ffffff !important;
        border-radius: 999px !important;
        padding: 8px 19px !important;
        margin: 2px 0 !important;
    }

    /* Hover state for main menu items */
    .app-sidebar3 .side-menu__item:hover:not(.active) {
        background-color: #B8877A !important;
        color: #ffffff !important;
        border-radius: 999px !important;
        padding: 8px 19px !important;
        margin: 2px 0 !important;
    }

    .app-sidebar3 .side-menu__item:hover:not(.active) .side-menu__icon {
        filter: brightness(0) saturate(100%) invert(100%) !important;
        color: #ffffff !important;
    }

    .app-sidebar3 .side-menu__item:hover:not(.active) .side-menu__label {
        color: #ffffff !important;
    }

    .app-sidebar3 .side-menu__item:hover:not(.active) .angle {
        color: #ffffff !important;
    }

    .app-sidebar3 .side-menu__item.active .side-menu__icon {
        color: #ffffff !important;
    }

    .app-sidebar3 .side-menu__item.active .side-menu__label {
        color: #ffffff !important;
    }

    .app-sidebar3 .side-menu__item.active .angle {
        color: #ffffff !important;
    }

    /* Home icon image styling - Dark (#252525) when not active */
    .app-sidebar3 .home_btn .side-menu__icon {
        filter: grayscale(100%) brightness(0.145);
    }

    /* Home icon image styling - White (#FFFFFF) when active */
    .app-sidebar3 .home_btn.active .side-menu__icon {
        filter: brightness(0) saturate(100%) invert(100%) !important;
    }

    /* All sidebar menu item images - Dark (#252525) when not active */
    .app-sidebar3 .side-menu__item .side-menu__icon {
        filter: grayscale(100%) brightness(0.145);
    }

    /* All sidebar menu item images - White (#FFFFFF) when active */
    .app-sidebar3 .side-menu__item.active .side-menu__icon {
        filter: brightness(0) saturate(100%) invert(100%) !important;
    }

    /* Prevent image stretching/compression - maintain aspect ratio */
    .app-sidebar3 .side-menu__icon[src] {
        object-fit: contain !important;
        flex-shrink: 0 !important;
        display: inline-block !important;
    }

    /* When sidebar is collapsed/closed, ensure images don't stretch or compress */
    .app.sidenav-toggled .app-sidebar3 .side-menu__icon[src],
    .app.sidenav-toggled1 .app-sidebar3 .side-menu__icon[src] {
        object-fit: contain !important;
        flex-shrink: 0 !important;
    }

    /* Dropdown parent item styling - Purple background when expanded */
    .app-sidebar3 .slide.is-expanded>.side-menu__item {
        background-color: #CD9A8F !important;
        color: #ffffff !important;
        border-radius: 999px !important;
        padding: 8px 19px !important;
        margin: 2px 0 !important;
    }

    .app-sidebar3 .slide.is-expanded>.side-menu__item:hover {
        background-color: #B8877A !important;
        color: #ffffff !important;
        border-radius: 999px !important;
        padding: 8px 19px !important;
        margin: 2px 0 !important;
    }

    .app-sidebar3 .slide.is-expanded>.side-menu__item .side-menu__icon {
        /* filter: brightness(0) saturate(100%) invert(100%) !important; */
        /* color: #ffffff !important; */
    }

    .app-sidebar3 .slide.is-expanded>.side-menu__item .side-menu__label {
        color: #ffffff !important;
    }

    .app-sidebar3 .slide.is-expanded>.side-menu__item .angle {
        color: #ffffff !important;
    }

    /* Submenu items styling - Medium grey for inactive */
    .app-sidebar3 .slide-menu .slide-item {
        color: #666666 !important;
        padding: 6px 0 6px 20px !important;
        margin: 2px 0 !important;
        position: relative;
        background-color: transparent !important;
        border-left: 1px solid #E6E6E6;
        display: block !important;
        transition: all 0.2s ease !important;
    }

    /* Hide default chevron icons in submenu items */
    .app-sidebar3 .slide-menu .slide-item::before {
        display: none !important;
        content: none !important;
    }

    /* Active submenu item - Purple with left vertical bar */
    .app-sidebar3 .slide-menu .slide-item.active {
        color: #CD9A8F !important;
        background-color: transparent !important;
        padding: 6px 0 6px 20px !important;
        margin: 2px 0 !important;
        border-left: 3px solid #CD9A8F !important;
    }

    /* Purple bar on left side for active submenu items */
    .app-sidebar3 .slide-menu .slide-item.active::before {
        content: '' !important;
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background-color: #CD9A8F;
        border-right: 1px solid #5A4ED1;
        display: block !important;
    }

    /* Hover state for submenu items - Purple color */
    .app-sidebar3 .slide-menu .slide-item:hover {
        color: #B8877A !important;
        background-color: transparent !important;
        border-left: 3px solid #B8877A !important;
        padding: 6px 0 6px 20px !important;
        margin: 2px 0 !important;
    }

    /* Ensure no other icons show in submenu */
    .app-sidebar3 .slide-menu .slide-item i.fa,
    .app-sidebar3 .slide-menu .slide-item i.fe,
    .app-sidebar3 .slide-menu .slide-item svg {
        display: none !important;
    }

    /* Slide menu padding - Reduced top padding to minimize spacing */
    .app-sidebar3 .slide-menu {
        padding-left: 15px !important;
        padding-top: 4px !important;
        padding-bottom: 0 !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }

    /* Consistent spacing for submenu list items */
    .app-sidebar3 .slide-menu>li {
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Ensure slides with submenus have same spacing as regular items */
    .app-sidebar3 .slide {
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Ensure parent item with submenu has consistent spacing - no extra margin */
    .app-sidebar3 .slide>.side-menu__item {
        margin: 2px 0 !important;
    }
    .app-sidebar3 .side-menu__item .side-menu__icon {
    filter: none !important;
}
.app-sidebar3 img.side-menu__icon {
    filter: grayscale(100%) brightness(0.3);
}

.app-sidebar3 svg.side-menu__icon {
    filter: none !important;
}
.side-menu__icon {
    filter: none !important;
}
</style>
<aside class="app-sidebar app-sidebar3" style="box-shadow: none !important; border-right: none !important;">
    <div class="app-sidebar__user" style="padding: 0px !important">
        {{-- <div class="dropdown user-pro-body text-center">
            <div class="user-pic">
                <img src="{{ asset('/staffimages/' . auth()->user()->employee_id . '.jpg') }}" alt="user-img"
                    class="avatar-xl rounded-circle mb-1">
            </div>
            <div class="user-info">
                <h5 class=" mb-1 font-weight-bold">
                    <?= auth()->user()->full_name ?>
                </h5>
                <span class="text-muted app-sidebar__user-name text-sm">
                    <?= auth()->user()->designation ?>
                </span>
            </div>
        </div> --}}
    </div>
    <ul class="side-menu">
        <h6 style="color: #949494;
			leading-trim: both;
			text-edge: cap;
			font-family: Montserrat;
			font-size: 12px;
			font-style: normal;
			padding-left:5px;
			font-weight: 500;
			line-height: normal;
			text-transform: uppercase;">
            MAIN MENU</h6>
        <li class="slide">
            <a class="side-menu__item home_btn" data-bm="home" href="{{ url('/') }}">
                <img class="side-menu__icon" src="{{ asset('images/home.png') }}" width="19" height="22" alt="Home">
                <span class="side-menu__label">Home</span></a>
        </li>
        @if (auth()->user()->group_id == 825)
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                    <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                        </path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    <span class="side-menu__label">TA</span><i class="angle fa fa-angle-right"></i></a>
                <ul class="slide-menu">






                    <!-- <li><a class="slide-item"  href="{{ url('reports/faculty/list') }}"><span>Faculty Assurance Report</span></a></li> https://devstudent.bnu.edu.pk/login-user-by-users-->
                    <li><a class="slide-item"
                            href="{{ url('move-to-student?token=' . auth()->user()->api_token) }}"><span>Students</span></a>
                    </li>
                </ul>
            </li>
        @else
            @if (auth()->user()->group_id == 1024)



                <li class="slide">
                    <a class="side-menu__item " data-toggle="slide" href="#">
                        <span><i class="fa fa-share-alt side-menu__icon mt-1"></i></span>
                        <span class="side-menu__label">SharePoint</span><i class="angle fa fa-angle-right"></i></a>
                    <ul class="slide-menu">


                        <li><a class="slide-item" target="__blank"
                                href="https://share.bnu.edu.pk/sites/facultynstaff"><span>Faculty SharePoint</span></a>
                        </li>
                        <li><a class="slide-item" target="__blank"
                                href="https://share.bnu.edu.pk/sites/students/"><span></span>Student SharePoint</a></li>
                        <li><a class="slide-item" target="__blank"
                                href="https://share.bnu.edu.pk/sites/Careers"><span></span>Job Portal</a></li>


                        @php
                            $allowedSharePointEmployeeIds = [
                                553,
                                1313,
                                1273,
                                276,
                                226,
                                259,
                                512,
                                2815,
                                2855,
                                7335,
                                5375,
                                8157,
                                9194,
                                9096,
                                9258,
                                9241,
                                9325,
                                9328,
                                9385,
                                16,
                            ];

                            $user = auth()->user();
                            $hasSharePointAccess =
                                in_array($user->employee_id, $allowedSharePointEmployeeIds) || $user->group_id == 644;
                        @endphp

                        @if ($hasSharePointAccess)
                            <li>
                                <a class="slide-item" target="_blank" href="http://share.bnu.edu.pk/sites/Careers">
                                    <span>Job Portal</span>
                                </a>
                            </li>
                        @endif



                    </ul>

                <li class="slide">
                    <a class="side-menu__item" target="_blank" href="{{ url('staff-exit-interview-form') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="side-menu__icon">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <span class="side-menu__label">Staff Exit Interview Form</span>
                    </a>
                </li>




                <!---- -->


                <?php


                // $groupId = auth()->user()->group_id;
                // Group IDs: 644 - Software Team, 223 - Dean, 364 - Deans SCIT, 445 - HOD Ijaz
                // if (in_array($groupId, [644, 223, 364, 445]) || auth()->user()->username == 'hifsa.farooq') {

                $groupId = auth()->user()->group_id;
                $username = auth()->user()->username;
                // Group IDs allowed
                $allowedGroups = [644, 223, 364, 445];
                // Usernames allowed
                $allowedUsers = [
                    'hifsa.farooq',
                    'zara.amjad',
                    'arez.ali',
                    'ahsan.fazal',
                    'sara.assad',
                    'ghammaz.husnain',
                    'zeeshan.sarwar',
                    'nouman.ali',
                    'amna.humayun',
                    'nabeel.ahsan',
                    'osama.tariq',
                    'haider.ali1',
                    'huda.sarfraz',
                    'farhat_nadeem',
                    'asim.irshad',
                    'noreen.khalid',
                    'ameer.hamza',
                    'farhan.ali',
                    'talia.noreen',
                    'muhammad.ali1',
                    'ammar.faiz',
                    'umaima.mohsin',
                    'sana.aziz',
                    'zoona.kundi',
                    'neha.qasim',


                    'werdah.munib',
                    'tabina.sirhindi',
                    'harris.badar',
                    'zeeshan.zaighum',
                    'nasir.ali',
                    'usman.rana',
                    'sheharyar.zahid',
                    'warda.shah',
                    'nosharwhan.adil',
                    'naveed.aasim',
                    'farahat.ali',
                    'rabia.noor',
                    'sarmed.cheema'

                ];

                if (in_array($groupId, $allowedGroups) || in_array($username, $allowedUsers)) {


                                                                ?>
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <!-- SVG Icon -->
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path
                                d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                            </path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                        <span class="side-menu__label">Student Progress</span>
                        <i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li>
                            <a class="slide-item" href="<?php            echo url('/reports/report-generator'); ?>">
                                <span>Student Progress Monitoring Portal</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php
                }
                                                                ?>


                <!-- peer mentorship-->
                <?php
                if (in_array(auth()->user()->group_id, array(644)) || auth()->user()->username == 'neha.qasim') {
                                                                ?>


                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="side-menu__icon">
                            <circle cx="12" cy="7" r="4" stroke-width="2" />
                            <path d="M4 21v-2a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v2" stroke-width="2" />
                        </svg>
                        <span class="side-menu__label">Peer Mentorship</span>
                        <i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <?php { ?>
                        <li><a class="slide-item" href="{{ url('pm-scholarship-students') }}"><span>Scholarship
                                    students</span></a></li>
                        <?php            } ?>
                    </ul>

                    <ul class="slide-menu">
                        <?php { ?>
                        <li><a class="slide-item" href="{{ url('interview_schedules') }}"><span>Interview
                                    Schedules</span></a></li>
                        <?php            } ?>
                    </ul>

                    <ul class="slide-menu">
                        <?php { ?>
                        <li><a class="slide-item" href="{{ url('mentors_list') }}"><span>Mentor List</span></a></li>
                        <?php            } ?>
                    </ul>
                    <ul class="slide-menu">
                        <?php { ?>
                        <li><a class="slide-item" href="{{ url('admin-mentors-list') }}"><span>Admin Mentor
                                    List</span></a></li>
                        <?php            } ?>
                        {{-- <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course
                                    Report</span></a>
                        </li>
                        <li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary
                                    Report</span></a></li> --}}
                    </ul>
                    <ul class="slide-menu">
                        <?php { ?>
                        <li><a class="slide-item" href="{{ url('be_mentors_list') }}"><span>Be Mentor List</span></a>
                        </li>
                        <?php            } ?>
                        {{-- <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course
                                    Report</span></a></li>
                        <li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary
                                    Report</span></a></li> --}}
                    </ul>

                    <ul class="slide-menu">
                        <?php { ?>
                        <li><a class="slide-item" href="{{ url('pm-students-requests') }}"><span>Student Request
                                    List</span></a></li>
                        <?php            } ?>
                        {{-- <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course
                                    Report</span></a></li>
                        <li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary
                                    Report</span></a></li> --}}
                    </ul>

                    <ul class="slide-menu">
                        <?php { ?>
                        <li><a class="slide-item" href="{{ url('intro-session') }}"><span>Sessions</span></a></li>
                        <?php            } ?>
                        {{-- <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course
                                    Report</span></a></li>
                        <li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary
                                    Report</span></a></li> --}}
                    </ul>




                </li>
                <?php        } ?>
                <!-- peer mentorship ends here-->

                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="26"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <span class="side-menu__label">University Events</span><i class="angle fa fa-angle-right"></i></a>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="{{ url('UniversityEvent/register') }}"><span>Add University
                                    Event</span></a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ url('eportal-dashboard') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="side-menu__icon">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <span class="side-menu__label"> E-Portal Requests</span>
                    </a>
                </li>
                <li class="slide">
                    <a class="side-menu__item" href="{{ url('eportal-approval') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="side-menu__icon">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <span class="side-menu__label"> E-Portal Approval</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <img class="side-menu__icon" src="{{ asset('images/studentFine.png') }}" width="20" height="18"
                            alt="Dean Honor List">
                        <span class="side-menu__label">Student Fine</span><i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="/students-fine"><span>Apply Student Fine</span></a></li>
                    </ul>
                </li>
            @else
                    <!-- vc if condition start -->
                    <?php
                    if (auth()->user()->group_id == "8") { ?>
                    <?php            if (auth()->user()->username == 'orip' || auth()->user()->username == 'mansoor.ahmad' || auth()->user()->username == 'babar.jamil' || auth()->user()->username == 'ayeda.hamed') { ?>

                    <li class="slide">
                        <a class="side-menu__item" href="{{ url('staff/task-list') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span class="side-menu__label">Task Management</span>
                        </a>
                    </li>
                    <?php                if (auth()->user()->username == 'ayeda.hamed') { ?>
                    <li class="slide">
                        <a class="side-menu__item" href="{{ url('UniversityEvent/register') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span class="side-menu__label">University Event</span>
                        </a>
                    </li>
                    <?php                } ?>
                    @if (auth()->user()->username == 'babar.jamil')
                        <li class="slide">
                            <a class="side-menu__item" href="{{ url('eportal-dashboard') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="side-menu__icon">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                <span class="side-menu__label"> E-Portal Requests</span>
                            </a>
                        </li>
                    @endif

                    <?php            } ?>
                    <?php            if (auth()->user()->username == 'moeedyusuf' || auth()->user()->username == 'sumair.shafique') { ?>
                    {{-- VC reports generator --}}
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <span><i class="fa fa-file-text-o side-menu__icon mt-1"></i></span>
                            <!-- Using 'file-text-o' for reports -->
                            <span class="side-menu__label">Report Generator</span>
                            <i class="angle fa fa-angle-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li>
                                <a class="slide-item" href="/reports/bnu-admission-reports-generator"><span>Attendance
                                        Analysis Portal </span> <!-- Updated label for clarity --></a>
                            </li>
                            <li>
                                <a class="slide-item" href="/reports/bnu-admission-reports-generator-detail"><span>Admission
                                        Analysis Portal
                                    </span> <!-- Updated label for clarity --></a>
                            </li>
                            <li>
                                <a class="slide-item" href="/student-profile-rec"><span>Student Profile Data</span>
                                    <!-- Updated label for clarity --></a>
                            </li>
                            <li>
                                <a class="slide-item" href="{{ url('gymSubscriptionReport') }}"><span>Gym
                                        Report</span></a>
                            </li>
                            <li>
                                <a class="slide-item" href="{{ url('challan-report') }}"><span>Challan Report</span></a>
                            </li>
                            <li>
                                <a class="slide-item" href="{{ url('student-staff-exit-form-report') }}"><span>Student /
                                        Faculty / Staff Exit Comments</span></a>
                            </li>
                        </ul>
                    </li>

                    <li class="slide link_for_web">
                        <a class="side-menu__item" href="{{ url('staff/todo-list') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span class="side-menu__label">To-Do List</span>
                        </a>
                    </li>
                    <li class="slide link_for_mobile">
                        <a class="side-menu__item" href="{{ url('staff/ProjectBoard') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span class="side-menu__label">To-Do List</span>
                        </a>
                    </li>
                    <li class="slide link_for_mobile">
                        <a class="side-menu__item" href="{{ url('staff/personal_todo-list') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span class="side-menu__label">Personal To-Do List</span>
                        </a>
                    </li>
                    <li class="slide link_for_mobile">
                        <a class="side-menu__item" href="{{ url('staff/immediate_todo-list') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span class="side-menu__label">Immediate To-Do List</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a class="side-menu__item" href="{{ url('staff/task-list') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span class="side-menu__label">Task Management</span>
                        </a>
                    </li>
                    <li class="slide ">
                        <a class="side-menu__item" data-toggle="slide" href="#">

                            <span class="side-menu__label"><i class="fa fa-list side-menu__icon mt-1"></i>Subscriptions</span><i
                                class="angle fe fe-chevron-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li class="sub-slide">
                                <a class="slide-item" href="{{ url('staff-subscription-form') }}"><span>Gym
                                        Subscription</span></a>
                            </li>
                        </ul>

                    </li>


                    <?php            } ?>
                    <?php            if (auth()->user()->username != 'mansoor.ahmad' && auth()->user()->username != 'babar.jamil' && auth()->user()->username != 'ayeda.hamed') { ?>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span class="side-menu__label">E-Portal</span>
                            <i class="angle fa fa-angle-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li>
                                <a class="slide-item" href="{{ url('vc/eportal-tracking') }}">
                                    <span>E-Portal Tracking</span>
                                </a>
                            </li>
                            <li>
                                <a class="slide-item" href="{{ url('vc/eportal-dashboard') }}">
                                    <span>E-Portal Requests</span>
                                </a>
                            </li>
                            <li>
                                <a class="slide-item" href="{{ url('vc/eportal-report') }}">
                                    <span>E-Portal Report</span>
                                </a>
                            </li>
                            <li>
                                <a class="slide-item" href="{{ url('vc/eportal-summary') }}">
                                    <span>E-Portal Summary</span>
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li class="slide">
                        <a class="side-menu__item " data-toggle="slide" href="#">
                            <span><i class="fa fa-share-alt side-menu__icon mt-1"></i></span>
                            <span class="side-menu__label">SharePoint</span><i class="angle fa fa-angle-right"></i></a>
                        <ul class="slide-menu">
                            <li><a class="slide-item" target="__blank"
                                    href="https://share.bnu.edu.pk/sites/facultynstaff"><span>Faculty SharePoint</span></a>
                            </li>
                            <li><a class="slide-item" target="__blank"
                                    href="https://share.bnu.edu.pk/sites/students/"><span></span>Student SharePoint</a>
                            </li>
                        </ul>
                    </li>


                    <?php            } ?>
                    <?php            if (auth()->user()->username == 'aizaz.hussain' && auth()->user()->group_id == '8') { ?>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                                </path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                            <span class="side-menu__label">QA</span><i class="angle fa fa-angle-right"></i></a>
                        <ul class="slide-menu">
                            <!-- <li><a class="slide-item"  href="{{ url('reports/faculty/list') }}"><span>Faculty Assurance Report</span></a></li> -->
                            <li><a class="slide-item" href="{{ url('reports/faculty-evaluation') }}"><span>Faculty
                                        Evaluation Report</span></a></li>
                        </ul>
                    </li>
                    <li class="slide">
                        <a class="side-menu__item " data-toggle="slide" href="#">
                            <span><i class="fa fa-book  side-menu__icon mt-1"></i></span>
                            <span class="side-menu__label"> Short Courses</span><i class="angle fa fa-angle-right"></i></a>
                        <ul class="slide-menu">
                            <li>
                                <a class="slide-item" href="{{ url('short_courses') }}"><span>Add Course</span></a>
                            </li>
                            <li>
                                <a class="slide-item" href="{{ url('shortCourse_applicants') }}"><span>Applicants
                                        List</span></a>
                            </li>
                        </ul>
                    </li>
                    <li class="slide">
                        <a class="side-menu__item" href="{{ url('UniversityEvent/register') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span class="side-menu__label">University Event</span>
                        </a>
                    </li>

                    <?php            } ?>

                    <?php

                    } else {

                                                                                        ?>
                    <?php            if (auth()->user()->group_id == 644 || auth()->user()->group_id == 121) { ?>



                    <li class="slide">
                        <a class="side-menu__item" target="_blank" href="{{ url('university-leaving-report') }}">
                            <i class="fa fa-pencil-square-o side-menu__icon"></i>
                            <span class="side-menu__label">Staff Exit Report</span>
                        </a>
                    </li>



                    <li class="slide">
                        <a class="side-menu__item" target="_blank" href="{{ url('staff-exit-request-list') }}">
                            <i class="fa fa-list-alt side-menu__icon"></i>
                            <span class="side-menu__label">Staff Exit Request List</span>
                        </a>
                    </li>




                    <?php            } ?>
                    <?php            if (auth()->user()->group_id == 644) { ?>

                    <li class="slide">
                        <a class="side-menu__item" target="_blank" href="{{ url('announcements') }}">
                            <i class="fa fa-bullhorn side-menu__icon"></i>
                            <span class="side-menu__label">Mobile Announcements</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a class="side-menu__item" target="_blank" href="{{ url('update-test-slip-date') }}">
                            <i class="fa fa-calendar side-menu__icon"></i>
                            <span class="side-menu__label">Admission Test Dates</span>
                        </a>
                    </li>

                    <?php            } ?>


                    <?php            if (auth()->user()->group_id == 644 || auth()->user()->username == 'haider.raza') { ?>

                    <li class="slide">
                        <a class="side-menu__item" target="_blank" href="{{ url('academic-session') }}">
                            <i class="fa fa-calendar side-menu__icon"></i>
                            <span class="side-menu__label">Academic calender</span>
                        </a>
                    </li>

                    <?php            } ?>





                    <?php            if (auth()->user()->group_id == 644 || auth()->user()->username == 'noreen') { ?>
                    <li class="slide">
                        <a class="side-menu__item" target="_blank" href="{{ url('exit-form-report') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14,2 14,8 20,8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10,9 9,9 8,9"></polyline>
                            </svg>
                            <span class="side-menu__label">Exit Form Report</span>
                        </a>
                    </li>
                    <?php            } ?>






                    <?php            if (auth()->user()->group_id == 644 || auth()->user()->group_id == 964 || auth()->user()->group_id == 1024 || auth()->user()->group_id == 844 || auth()->user()->group_id == 324 || auth()->user()->group_id == 744 || auth()->user()->username == 'ahmer.sayeed' || auth()->user()->username == 'ali.sher' || auth()->user()->username == 'hassan.farooq') { ?>

                    <li class="slide">
                        <a class="side-menu__item " data-toggle="slide" href="#">
                            <span><i class="fa fa-bed side-menu__icon"></i></span>
                            <span class="side-menu__label">Hostel</span><i class="angle fa fa-angle-right"></i></a>
                        <ul class="slide-menu">



                            <!-- <li><a class="slide-item" href="{{ url('/hostel-update-checkin-and-checkout-date') }}"><span>Hostel Allocation History</span></a></li> -->



                            <?php                if (auth()->user()->group_id == 644 || auth()->user()->group_id == 324) { ?>
                            <li><a class="slide-item" href="{{ url('/hostel-form-visibility') }}"><span>Hostel Form
                                        Visibility</span></a></li>
                            <?php                } ?>


                            <?php                if (auth()->user()->group_id == 644 || auth()->user()->group_id == 284 || auth()->user()->group_id == 844 || auth()->user()->group_id == 744 || auth()->user()->username == 'ahmer.sayeed' || auth()->user()->username == 'ali.sher') { ?>
                            <li><a class="slide-item" href="{{ url('/hostel-applicants') }}"><span>Hostel
                                        Applicants</span></a></li>
                            <?php                } ?>

                            <?php                if (auth()->user()->group_id == 644 || auth()->user()->group_id == 284 || auth()->user()->group_id == 844 || auth()->user()->group_id == 744 || auth()->user()->username == 'ahmer.sayeed' || auth()->user()->username == 'ali.sher' || auth()->user()->username == 'hassan.farooq') { ?>
                            <li><a class="slide-item" href="{{ url('/hostel-students') }}"><span>Hostel
                                        Students</span></a></li>
                            <?php                } ?>

                            <?php                if (auth()->user()->group_id == 644 || auth()->user()->group_id == 964 || auth()->user()->username == 'ahmer.sayeed' || auth()->user()->username == 'ali.sher') { ?>
                            <li><a class="slide-item" href="{{ url('/hostel-summary') }}"><span>Hostel Summary</span></a>
                            </li>
                            <li><a class="slide-item" target="__blank" href="{{ url('/hostel-vacate') }}"><span>Hostel
                                        Vacate</span></a></li>
                            <li><a class="slide-item" target="__blank" href="{{ url('/leave-requests') }}"><span>Hostel
                                        Night leave</span></a></li>
                            <li><a class="slide-item" href="{{ url('/facilities') }}"><span>Hostel Facilities</span></a>
                            </li>
                            <li><a class="slide-item" href="{{ url('/hostel-management') }}"><span>Hostel Type & Floor
                                        Management</span></a></li>

                            <?php                } ?>
                        </ul>
                    </li>

                    <?php            } ?>





                    <?php            if (auth()->user()->group_id == 644 || auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'asbah.rehman' || auth()->user()->username == 'maheen.james' || auth()->user()->username == 'zainabsanik' || auth()->user()->username == 'saqib.jamil' || auth()->user()->username == 'esha.abid' || auth()->user()->username == 'ahmad.arfeen' || auth()->user()->username == 'asif_nadeem' || auth()->user()->username == 'muhammad.nadeem' || auth()->user()->username == 'farzana' || auth()->user()->username == 'hassan.farooq' || auth()->user()->username == 'abdul.waheed' || auth()->user()->username == 'tayyaba.nawaz' || auth()->user()->username == 'talha.sarfraz' || auth()->user()->username == 'haider.raza' || auth()->user()->username == 'qasim.ali') { ?>

                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <i class="fa fa-money side-menu__icon"></i>
                            <span class="side-menu__label">Student Financial</span><i class="angle fa fa-angle-right"></i>
                        </a>
                        <ul class="slide-menu">

                            <?php                if (auth()->user()->group_id == 644 || auth()->user()->username == 'farzana' || auth()->user()->username == 'hassan.farooq' || auth()->user()->username == 'talha.sarfraz' || auth()->user()->username == 'abdul.waheed' || auth()->user()->username == 'tayyaba.nawaz' || auth()->user()->username == 'haider.raza' || auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'sumaira.arshad' || auth()->user()->username == 'asbah.rehman' || auth()->user()->username == 'zainabsanik' || auth()->user()->username == 'maheen.james') { ?>
                            <li><a class="slide-item" href="{{ url('finance/fee-policy') }}"><span>Enrollment
                                        Policy</span></a></li>
                            {{-- <li><a class="slide-item" href="{{url('master-scholarship-criteria')}}"><span>Scholarship
                                        Criteria</span></a>
                            </li> --}}
                            <!-- Scholarship Criteria Parent -->
                            {{-- <li class="sub-slide">
                                <a class="sub-side-menu__item" data-toggle="sub-slide" href="#">
                                    <span>Scholarship Criteria</span>
                                </a>

                                <ul class="sub-slide-menu">
                                    <li>
                                        <a class="slide-item" href="{{ url('master-scholarship-criteria') }}">
                                            Scholarship Criteria
                                        </a>
                                    </li>
                                    <li>
                                        <a class="slide-item" href="{{ url('master-scholarship-policy') }}">
                                            Scholarship Policy
                                        </a>
                                    </li>
                                </ul>
                            </li> --}}
                            <!-- End Scholarship Criteria -->
                            <li><a class="slide-item" href="{{ url('master-scholarship-criteria') }}"><span>Scholarship
                                        Criteria</span></a></li>
                            <li><a class="slide-item" href="{{ url('master-scholarship-policy') }}"><span>Scholarship
                                        Policy</span></a></li>
                            <li><a class="slide-item" href="{{ url('finance/assign-scholarship-index') }}"><span>Assign
                                        Scholarship</span></a></li>
                            <li><a class="slide-item" href="{{ url('finance/verify-scholarship-index') }}"><span>Scholarship
                                        Approval</span></a></li>
                            <li><a class="slide-item" href="{{ url('finance/other-scholarship-index') }}"><span>Other
                                        Scholarships</span></a></li>
                            <li><a class="slide-item" href="{{ url('studentLedger') }}"><span>Student Ledger</span></a>
                            </li>
                            <li><a class="slide-item" href="{{ url('challan-report') }}"><span>Challan Report</span></a>
                            </li>
                            <li><a class="slide-item" href="{{ url('merit-scholarship-entry') }}"><span>Merit Scholarship
                                        Entry</span></a></li>


                            <?php                }
                            if (auth()->user()->group_id == 644 || auth()->user()->username == 'saqib.jamil' || auth()->user()->username == 'esha.abid' || auth()->user()->username == 'ahmad.arfeen' || auth()->user()->username == 'asif_nadeem' || auth()->user()->username == 'muhammad.nadeem' || auth()->user()->username == 'qasim.ali') { ?>


                            <li><a class="slide-item" href="{{ url('master-fee-structure') }}"><span>Master Fee
                                        Structure</span></a></li>
                            <li><a class="slide-item" href="{{ url('master-scholarship-form') }}"><span>Add
                                        Scholarship</span></a></li>
                            <li><a class="slide-item" href="{{ url('master-hostelfee-structure') }}"><span>Hostel Fee
                                        Structure</span></a></li>
                            <li><a class="slide-item" href="{{ url('master-feeadjustment-form') }}"><span>Master Fee
                                        Adjustment</span></a></li>
                            <li><a class="slide-item" href="{{ url('feeadjustment') }}"><span>Fee Adjustment</span></a>
                            </li>
                            <li><a class="slide-item" href="{{ url('generate-fee-challan') }}"><span>Generate Bulk
                                        Challan</span></a></li>
                            <li><a class="slide-item" href="{{ url('generate-credithrfee-challan') }}"><span>Generate
                                        Bulk Credit Hour Challan</span></a></li>
                            <li><a class="slide-item" href="{{ url('generate-convocation-challan') }}"><span>Generate
                                        Convocation Challan</span></a></li>
                            <li><a class="slide-item" href="{{ url('finance/generate-single-challan') }}"><span>Generate
                                        Single Challan</span></a></li>
                            <li><a class="slide-item" href="{{ url('challan-report') }}"><span>Challan Report</span></a>
                            </li>
                            <li><a class="slide-item" href="{{ url('challan-generated-details') }}"><span>Student Challan
                                        Detail</span></a></li>
                            {{-- <li><a class="slide-item" href="{{url('middleware-challan_rectification-form')}}"><span>Challan
                                        Rectification</span></a></li> --}}
                            <li><a class="slide-item" href="{{ url('installment-form') }}"><span>Master Installment Form
                                        (CMS)</span></a></li>
                            <li><a class="slide-item" href="{{ url('student-manual-paid-mark') }}"><span>Manual Paid
                                        Mark</span></a></li>
                            <li><a class="slide-item" href="{{ url('studentLedger') }}"><span>Student Ledger</span></a>
                            </li>
                            <li><a class="slide-item" href="{{ url('enrollment-summary-report') }}"><span>Enrollment
                                        Summary</span></a></li>
                            <li><a class="slide-item" href="{{ url('challan-detail-report') }}"><span>Challan Detail
                                        Report</span></a></li>
                            <li><a class="slide-item" href="{{ url('donation-record-form') }}"><span>Donations</span></a>
                            </li>
                            <li><a class="slide-item" href="{{ url('finance/assign-scholarship-index') }}"><span>Assign
                                        Scholarship</span></a></li>
                            <li><a class="slide-item" href="{{ url('finance/verify-scholarship-index') }}"><span>Scholarship
                                        Approval</span></a></li>
                            <li><a class="slide-item" href="{{ url('student-record/student-cancel-challan') }}"><span>Bulk Challan
                                        Update &
                                        Cancellation</span></a></li>

                            <li><a class="slide-item" href="{{ url('merit-scholarship-challan-handle') }}"><span>Generate/Approve
                                        Scholarship Challan</span></a></li>
                            <?php                }
                            if (auth()->user()->group_id == 644 || auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'asbah.rehman' || auth()->user()->username == 'zainabsanik' || auth()->user()->username == 'maheen.james') { ?>
                            <li><a class="slide-item" href="{{ url('studentLedger') }}"><span>Student Ledger</span></a>
                            </li>
                            <li><a class="slide-item" href="{{ url('installment-form-admission') }}"><span>Master
                                        Installment Form (Admission)</span></a></li>
                            <li><a class="slide-item" href="{{ url('admission-scholarship-criteria') }}"><span>Admission
                                        Scholarship Criteria</span></a></li>
                            <li><a class="slide-item" href="{{ url('admission-individual-scholarship-criteria') }}"><span>Individual
                                        Scholarship</span></a></li>
                            <li><a class="slide-item" href="{{ url('finance/verify-admissionScholarship') }}"><span>Admission
                                        Scholarship
                                        Approval</span></a></li>


                            <?php                } ?>

                        </ul>
                    </li>

                    <?php            } ?>


                    <?php            if (auth()->user()->group_id == 644 || auth()->user()->username == 'salman.zafar')  { ?>

                    <ul class="side-menu" role="list">


                        {{-- Store Procurement --}}
                        @if (auth()->user()->group_id == 644 || auth()->user()->username == 'salman.zafar')
                            @include('layouts.modules.store_procurement')
                        @endif

                        {{-- Add more modules here --}}

                    </ul>

                    <?php            } ?>






                    <?php            if (auth()->user()->username == 'farzana') { ?>
                    <li class="slide">
                        <a class="side-menu__item" href="{{ url('car-sticker-report') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span class="side-menu__label">Car Sticker Report</span>
                        </a>
                    </li>

                    <?php            } ?>
                    <?php            if (auth()->user()->username == 'bilal.saleem' || auth()->user()->username == 'farzana') { ?>
                    <li class="slide">
                        <a class="side-menu__item" href="{{ url('admission_applicant_data') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span class="side-menu__label">Applicant Data Report</span>
                        </a>
                    </li>

                    <?php            } ?>
                    <li class="slide link_for_web">
                        <a class="side-menu__item" href="{{ url('dean-honor') }}">
                            <img class="side-menu__icon" src="{{ asset('images/deanlist.png') }}" width="24" height="22"
                                alt="Dean Honor List">
                            <span class="side-menu__label">Dean Honor List</span>
                        </a>
                    </li>
                    <li class="slide link_for_web">
                        <a class="side-menu__item" href="{{ url('staff/todo-list') }}">
                            <img class="side-menu__icon" src="{{ asset('images/todo1.png') }}" width="22" height="21"
                                alt="Dean Honor List">
                            <span class="side-menu__label">To-do List</span>
                        </a>
                    </li>
                    <li class="slide">
                        <a class="side-menu__item" href="{{ url('staff/task-list') }}">
                            <img class="side-menu__icon" src="{{ asset('images/task.png') }}" width="18" height="23"
                                alt="Dean Honor List">
                            <span class="side-menu__label">Task Management</span>
                        </a>
                    </li>
                    <?php            if (auth()->user()->group_id == 644 || auth()->user()->username == 'shabana' || auth()->user()->username == 'ghulam.ghouse' || auth()->user()->username == 'zainab.ahmed') { ?>
                    <li class="slide">
                        <a class="side-menu__item" href="{{ url('event-registrations') }}">
                            <i class="fa fa-calendar-plus-o side-menu__icon"></i>
                            <span class="side-menu__label">Event Registrations</span>
                        </a>
                    </li>
                    <?php            } ?>

                    <?php            if (auth()->user()->group_id == 825) { ?>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                                </path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                            <span class="side-menu__label">TA</span><i class="angle fa fa-angle-right"></i></a>
                        <ul class="slide-menu">
                            <!-- <li><a class="slide-item"  href="{{ url('reports/faculty/list') }}"><span>Faculty Assurance Report</span></a></li> https://devstudent.bnu.edu.pk/login-user-by-users-->
                            <li><a class="slide-item"
                                    href="{{ url('move-to-student?token=' . auth()->user()->api_token) }}"><span>Students</span></a>
                            </li>
                        </ul>
                    </li>
                    <?php            } ?>
                    <?php
                        if (auth()->user()->group_id != 884) {
                            if (auth()->user()->group_id == 222 || auth()->user()->group_id == 2 || auth()->user()->group_id == 344 || auth()->user()->group_id == 644) { ?>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <i class="fa fa-line-chart side-menu__icon"></i>
                            <span class="side-menu__label">Reports</span><i class="angle fa fa-angle-right"></i></a>
                        <ul class="slide-menu">
                            <li><a class="slide-item" href="{{ url('/reportgenerator/reportgenerator') }}"><span>Report
                                        Generator</span></a></li>
                            <!--<li><a class="slide-item" href="{{ url('teacher/grading_status') }}"><span>Grading Status</span></a></li>
                                                                                                   <li><a class="slide-item" href="{{ url('teacher/enrollment_status') }}"><span>Enrollment Status</span></a></li>
                                                                                                   <li><a class="slide-item" href="{{ url('teacher/applicant_academic_profile') }}"><span>Applicant Academic Profile</span></a></li>-->
                            <li><a class="slide-item" href="{{ url('teacher/course_attendance_detail') }}"><span>Course
                                        Attendance Detail</span></a></li>
                            <li><a class="slide-item" href="{{ url('teacher/hr_attendance_report') }}"><span>HR
                                        Attendance Report</span></a></li>
                            <?php                    if (auth()->user()->username == 'mehmil.ishtiaq') { ?>
                            <li><a class="slide-item" href="{{ url('teacher/applicant_academic_profile_status') }}"><span>Applicant
                                        Status</span></a></li>
                            <?php                    } ?>
                            <?php                    if (auth()->user()->employee_id == 2876 || auth()->user()->group_id == 644) { ?>
                            <li><a class="slide-item" href="{{ url('teacher/academic_history2') }}"><span>Academic
                                        History</span></a></li>
                            <?php                    } ?>

                            <!-- <li><a class="slide-item" href="{{ url('teacher/attendance_summary_dept') }}"><span>Attendance Summary Department Wise</span></a></li>
                                                                                                   <li><a class="slide-item" href="{{ url('teacher/student_semester_performance') }}"><span>Student Semester Performance</span></a></li>
                                                                                                   <li><a class="slide-item" href="{{ url('teacher/faculty_course_attendance_report') }}"><span>Faculty Course Attendance</span></a></li>
                                                                                                   <li><a class="slide-item" href="{{ url('teacher/acadmic_history') }}"><span>Acadmic History</span></a></li>
                                                                                                  -->
                        </ul>
                    </li>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <i class="fa fa-check-square-o side-menu__icon"></i>
                            <span class="side-menu__label">QA</span><i class="angle fa fa-angle-right"></i></a>
                        <ul class="slide-menu">
                            <!-- <li><a class="slide-item"  href="{{ url('reports/faculty/list') }}"><span>Faculty Assurance Report</span></a></li> -->
                            <li><a class="slide-item" href="{{ url('reports/faculty-evaluation') }}"><span>Faculty
                                        Evaluation Report</span></a></li>
                        </ul>
                    </li>
                    <?php                } else if (auth()->user()->group_id == 223 || auth()->user()->group_id == 364 || auth()->user()->group_id == 284 || auth()->user()->group_id == 824 || auth()->user()->group_id == 122) { ?>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <img class="side-menu__icon" src="{{ asset('images/reports.png') }}" width="21" height="25"
                                alt="Dean Honor List">
                            <span class="side-menu__label">Reports</span><i class="angle fa fa-angle-right"></i></a>
                        <ul class="slide-menu">
                            <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                            <li><a class="slide-item" href="{{ url('/reportgenerator/reportgenerator') }}"><span>Report
                                        Generator</span></a></li>
                            <li><a class="slide-item" href="{{ url('/faculty/course_offering') }}"><span>Course Offering
                                        List</span></a></li>
                            <li><a class="slide-item" href="{{ url('teacher/grading_status') }}"><span>Grading
                                        Status</span></a></li>
                            <li><a class="slide-item" href="{{ url('teacher/enrollment_status') }}"><span>Enrollment
                                        Status</span></a></li>
                            <li><a class="slide-item" href="{{ url('teacher/applicant_academic_profile') }}"><span>Applicant
                                        Academic
                                        Profile</span></a></li>
                            <li><a class="slide-item" href="{{ url('teacher/applicant_academic_profile_status') }}"><span>Applicant
                                        Status</span></a></li>
                            <li><a class="slide-item" href="{{ url('teacher/student_assessment_report') }}"><span>Student Assessment
                                        Report</span></a></li>
                            <li><a class="slide-item" href="{{ url('teacher/attendance_summary_dept') }}"><span>Attendance Summary
                                        Department Wise</span></a></li>
                            <li><a class="slide-item" href="{{ url('teacher/course_attendance_detail') }}"><span>Course
                                        Attendance Detail</span></a></li>
                            <li><a class="slide-item" href="{{ url('teacher/student_semester_performance') }}"><span>Student
                                        Semester
                                        Performance</span></a></li>
                            <li><a class="slide-item" href="{{ url('teacher/faculty_course_attendance_report') }}"><span>Faculty
                                        Course
                                        Attendance</span></a></li>
                            <li><a class="slide-item" href="{{ url('teacher/acadmic_history') }}"><span>Acadmic
                                        History</span></a></li>
                            <li><a class="slide-item" href="{{ url('teacher/nbs_report') }}"><span>NBS Summary
                                        Report</span></a></li>
                            <li><a class="slide-item" href="{{ url('teacher/faculty_course_detail') }}"><span>Faculty
                                        Course Content</span></a></li>
                            <?php                        if (auth()->user()->employee_id == 1294 or auth()->user()->employee_id == 6455) { ?>
                            <li><a class="slide-item" href="{{ url('teacher/hr_attendance_report') }}"><span>HR
                                        Attendance Report</span></a></li>
                            <?php                        } ?>
                            <?php                        if (auth()->user()->username == 'hifsa.farooq' or auth()->user()->username == 'bilal.saleem') { ?>
                            <li><a class="slide-item" href="{{ url('teacher/admission_enrollment_status') }}"><span>Department wise
                                        Report</span></a></li>
                            <?php                        } ?>
                        </ul>
                    </li>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <img class="side-menu__icon" src="{{ asset('images/qr.png') }}" width="20" height="20"
                                alt="Dean Honor List">
                            <span class="side-menu__label">QA </span><i class="angle fa fa-angle-right"></i></a>
                        <ul class="slide-menu">
                            <li><a class="slide-item" href="{{ url('reports/faculty/list') }}"><span>Faculty Assurance
                                        Report</span></a></li>
                            <li><a class="slide-item" href="{{ url('reports/faculty-evaluation') }}"><span>Faculty
                                        Evaluation Report</span></a></li>
                        </ul>
                    </li>
                    <?php                        if (auth()->user()->username == 'ruhi.khalid') { ?>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span class="side-menu__label">Dean</span></a>
                        <ul class="slide-menu">
                            <li><a href="{{ url('teacher/courses/approval') }}" class="slide-item">Result Approval</a>
                            </li>
                        </ul>
                    </li>
                    <?php                        } else { ?>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <img class="side-menu__icon" src="{{ asset('images/hod.png') }}" width="20" height="24"
                                alt="Dean Honor List">
                            <span class="side-menu__label">HOD</span></a>
                        <ul class="slide-menu">
                            <li><a href="{{ url('teacher/courses/approval') }}" class="slide-item">Result Approval</a>
                            </li>
                        </ul>
                    </li>
                    <?php                        } ?>
                    <?php                        if (auth()->user()->group_id != 284 || auth()->user()->group_id == 122) { ?>
                    <li class="slide">
                        <a class="side-menu__item" target="_blank" href="{{ url('/approve_ta') }}">
                            <img class="side-menu__icon" src="{{ asset('images/approve.png') }}" width="23" height="22"
                                alt="Dean Honor List">
                            <span class="side-menu__label">Approve TA</span>
                        </a>
                    </li>
                    <?php                        } ?>

                    <?php                    } ?>
                    <?php


                            // $groupId = auth()->user()->group_id;
                            // Group IDs: 644 - Software Team, 223 - Dean, 364 - Deans SCIT, 445 - HOD Ijaz
                            // if (in_array($groupId, [644, 223, 364, 445]) || auth()->user()->username == 'hifsa.farooq') {

                            $groupId = auth()->user()->group_id;
                            $username = auth()->user()->username;
                            // Group IDs allowed
                            $allowedGroups = [644, 223, 364, 445];
                            // Usernames allowed
                            $allowedUsers = [
                                'hifsa.farooq',
                                'zara.amjad',
                                'arez.ali',
                                'ahsan.fazal',
                                'sara.assad',
                                'ghammaz.husnain',
                                'zeeshan.sarwar',
                                'nouman.ali',
                                'amna.humayun',
                                'nabeel.ahsan',
                                'osama.tariq',
                                'haider.ali1',
                                'huda.sarfraz',
                                'farhat_nadeem',
                                'asim.irshad',
                                'noreen.khalid',
                                'ameer.hamza',
                                'farhan.ali',
                                'talia.noreen',
                                'muhammad.ali1',
                                'ammar.faiz',
                                'umaima.mohsin',
                                'sana.aziz',
                                'zoona.kundi',
                                'neha.qasim',
                                'noreen',
                                'reema.qadeer',

                                'werdah.munib',
                                'tabina.sirhindi',
                                'harris.badar',
                                'zeeshan.zaighum',
                                'nasir.ali',
                                'usman.rana',
                                'sheharyar.zahid',
                                'warda.shah',
                                'nosharwhan.adil',
                                'naveed.aasim',
                                'farahat.ali',
                                'rabia.noor',
                                'sarmed.cheema',
                                'hina.razzaq',
                                'bnu_hr'


                            ];

                            if (in_array($groupId, $allowedGroups) || in_array($username, $allowedUsers)) {


                                                                                            ?>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <!-- SVG Icon -->
                            <img class="side-menu__icon" src="{{ asset('images/studentProgress.png') }}" width="20" height="20"
                                alt="Dean Honor List">
                            <span class="side-menu__label">Student Progress</span>
                            <i class="angle fa fa-angle-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li>
                                <a class="slide-item" href="<?php                    echo url('/reports/report-generator'); ?>">
                                    <span>Student Progress Monitoring Portal</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php
                            }
                                                                                            ?>
                    <!-- GoToStaff -->
                    <?php                if (auth()->user()->group_id != 222 || auth()->user()->group_id != 223) {
                                if (in_array(auth()->user()->group_id, array(141, 584, 201, 1206))) { ?>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                                </path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                            <span class="side-menu__label">QA </span><i class="angle fa fa-angle-right"></i></a>
                        <ul class="slide-menu">
                            <li><a class="slide-item" href="{{ url('reports/faculty/list') }}"><span>Faculty Assurance
                                        Report</span></a></li>
                            <li><a class="slide-item" href="{{ url('reports/faculty-evaluation') }}"><span>Faculty
                                        Evaluation Report</span></a></li>
                            <li><a class="slide-item" href="{{ url('teacher/graduate_survey_report') }}"><span>Graduate
                                        Survey
                                        Report</span></a></li>
                            <li><a class="slide-item" href="{{ url('student-record/student-course-disable') }}"><span>Update Course
                                        Status</span></a></li>

                            <?php                        if (auth()->user()->username == 'noreen') { ?>
                            <li><a class="slide-item" href="{{ url('/faculty/course_offering_report') }}"><span>Course
                                        Offering Report</span></a></li>
                            <?php                        } ?>

                        </ul>
                    </li>
                    <?php                    } ?>


                    @if (auth()->user()->group_id == '644')
                        <li class="slide">
                            <a class="side-menu__item" data-toggle="slide" href="#">
                                <i class="fa fa-lock side-menu__icon"></i>
                                <span class="side-menu__label">Security Settings</span><i class="angle fa fa-angle-right"></i>
                            </a>
                            <ul class="slide-menu">
                                <li>
                                    <a class="slide-item" href="{{ url('allowed-inspect-users') }}">
                                        <span>Inspection Access Management</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="slide-item" href="{{ url('error-logs') }}">
                                        <span>System Logs</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="slide-item" href="{{ url('exam-sections') }}">
                                        <span>Exam Sections</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="slide-item" href="{{ url('page-permissions') }}">
                                        <span>Page Permissions</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="slide-item" href="{{ url('eportal-hod-dean-list') }}">
                                        <span>ePortal HOD/Dean List</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    <?php                }
                        }
                        if (auth()->user()->group_id == 884 || auth()->user()->group_id == 644 || auth()->user()->group_id == 2) { ?>
                    <li class="slide">
                        <a class="side-menu__item " data-toggle="slide" href="#">
                            <span><i class="fa fa-car side-menu__icon mt-1"></i></span>
                            <span class="side-menu__label">Car Pooling</span><i class="angle fa fa-angle-right"></i></a>
                        <ul class="slide-menu">
                            <li><a class="slide-item" href="{{ url('/car-pooling') }}"><span>Ride
                                        Registration</span></a></li>
                            <li><a class="slide-item" target="__blank" href="{{ url('/car-pooling-pdf') }}"><span>Generate
                                        Report</span></a></li>
                        </ul>
                    </li>
                    <?php                if (auth()->user()->username != 'ashfaque.hussain') { ?>
                    <li class="slide">
                        <a class="side-menu__item" target="_blank" href="{{ url('/car-registration') }}">
                            <i class="fa fa-car side-menu__icon"></i>
                            <span class="side-menu__label">Car Registration</span>
                        </a>
                    </li>
                    <?php                } ?>
                    <li class="slide">
                        <a class="side-menu__item" target="_blank" href="{{ url('/search-registered-car') }}">
                            <i class="fa fa-search side-menu__icon"></i>
                            <span class="side-menu__label">Search Registered Car</span>
                        </a>
                    </li>
                    <?php            } else { ?>
                    <li class="slide">
                        <a class="side-menu__item " data-toggle="slide" href="#">
                            <img class="side-menu__icon" src="{{ asset('images/carPooling.png') }}" width="20" height="19"
                                alt="Dean Honor List">
                            <span class="side-menu__label">Car Pooling</span><i class="angle fa fa-angle-right"></i></a>
                        <ul class="slide-menu">
                            <li><a class="slide-item" href="{{ url('/car-pooling') }}"><span>Ride
                                        Registration</span></a></li>
                        </ul>
                    </li>
                    <?php            } ?>
                    <!-- //// exam report  -->
                    <?php
                        if (in_array(auth()->user()->group_id, array(201, 424, 644, 2, 1064))) {
                                                                                        ?>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <path
                                d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                            </path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                            <span class="side-menu__label">EX. Reports </span><i class="angle fa fa-angle-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <?php                if (auth()->user()->username != 'maham.nadeem') { ?>
                            <li><a class="slide-item" href="{{ url('reports/exam/cgpa-summary') }}"><span>CGPA Summary
                                        Report</span></a></li>
                            <?php                } ?>
                            <li><a class="slide-item" href="{{ url('repeating-course') }}"><span>Repeating Course
                                        Report</span></a></li>
                            <li><a class="slide-item" href="{{ url('graduate-summary-report') }}"><span>Graduate
                                        Summary Report</span></a></li>
                            <li><a class="slide-item" href="{{ url('exam-report-student') }}"><span>Missing Doc
                                        Summary Report</span></a></li>
                        </ul>
                    </li>
                    <?php            } ?>

                    <?php
                        if (in_array(auth()->user()->group_id, array(141, 584, 305))) {
                                                                                        ?>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <path
                                d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                            </path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                            <span class="side-menu__label">EX. Reports </span><i class="angle fa fa-angle-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li><a class="slide-item" href="{{ url('repeating-course') }}"><span>Repeating Course
                                        Report</span></a></li>

                        </ul>
                    </li>
                    <?php            } ?>
                    <li class="slide ">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <img class="side-menu__icon" src="{{ asset('images/subs.png') }}" width="21" height="20"
                                alt="Dean Honor List">
                            <span class="side-menu__label">Subscriptions</span><i class="angle fe fe-chevron-right"></i>
                        </a>
                        @if (
                                auth()->user()->group_id == 644 ||
                                auth()->user()->id == 23858 ||
                                auth()->user()->username == 'somia.maqsood' ||
                                auth()->user()->username == 'abdul.waheed' ||
                                auth()->user()->username == 'tayyaba.nawaz' ||
                                auth()->user()->username == 'hassan.farooq' ||
                                auth()->user()->username == 'talha.sarfraz'
                            )
                            {{-- 644 is group id of ITRC and 23858 is user id of Rabia Shabir --}}
                            <ul class="slide-menu">
                                <li class="sub-slide">
                                    <a class="slide-item" href="{{ url('sports-applicant-form') }}"><span>Add
                                            Sports/Scholarship Students</span></a>
                                </li>
                            </ul>
                        @endif
                        @if (auth()->user()->group_id == 644 || auth()->user()->group_id == 324)
                            {{-- 644 is group id of ITRC and 324 is group id of accounts --}}
                            <ul class="slide-menu">
                                <li class="sub-slide">
                                    <a class="slide-item" href="{{ url('subscription-master-form') }}"><span>Subscription Master
                                            Form</span></a>
                                </li>
                            </ul>
                            <ul class="slide-menu">
                                <li class="sub-slide">
                                    <a class="slide-item" href="{{ url('user-subscriptions') }}"><span>Student
                                            Subscriptions</span></a>
                                </li>
                            </ul>
                        @endif
                        @if (
                                auth()->user()->group_id == 644 ||
                                auth()->user()->group_id == 324 ||
                                auth()->user()->username == 'saqib.zia' ||
                                auth()->user()->username == 'ahmer.sayeed' ||
                                auth()->user()->username == 'asif.khan'
                            )
                            {{-- 644 is group id of ITRC and 324 is group id of accounts --}}
                            <ul class="slide-menu">
                                <li class="sub-slide">
                                    <a class="slide-item" href="{{ url('staff-subscriptions') }}"><span>Staff
                                            Subscriptions</span></a>
                                </li>
                            </ul>
                        @endif
                        @if (auth()->user()->group_id == 644)
                            <ul class="slide-menu">
                                <li class="sub-slide">
                                    <a class="slide-item" href="{{ url('rfid-assignment-form') }}"><span>RFID
                                            Setup</span></a>
                                </li>
                            </ul>
                        @endif
                        <ul class="slide-menu">
                            <li class="sub-slide">
                                <a class="slide-item" href="{{ url('staff-subscription-form') }}"><span>Gym
                                        Subscription</span></a>
                            </li>
                        </ul>

                    </li>




                    <?php
                        $allowedGroupIds = [644,]; // 644 - Software Team
                        $allowedUsernames = [
                            'haider.raza',
                            'aiman.adnan',
                            'somia.maqsood',
                            'noreen',
                            'sheeraz',
                            'zainabsanik',
                            'asbah.rehman',
                            'sidra.tanveer',
                            'zonia.akbar',
                            'abdul.waheed',
                            'tayyaba.nawaz',
                            'hassan.farooq',
                            'maheen.james',
                            'saleem',
                            'arshad.khurshid'
                        ];
                        if (in_array(auth()->user()->group_id, $allowedGroupIds) || in_array(auth()->user()->username, $allowedUsernames)) {
                                                                                        ?>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <!-- Better Icon for Documents -->
                            <i class="fa fa-file-text-o side-menu__icon"></i>
                            <span class="side-menu__label">Missing Documents</span>
                            <i class="angle fa fa-angle-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li>
                                <a class="slide-item" href="<?php                echo url('/admission_qualification'); ?>">
                                    <span>Missing Documents Verification</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php
                        }
                                                                                        ?>




                    <?php
                        $allowedGroupIds = [644]; // 644 - Software Team

                        // Users for HOD Approval
                        $hodApprovalUsers = [
                            'farzana',
                            'zaeem',
                            'asif_nadeem',
                            'noreen',
                            'zeeshan.zaighum',
                            'maheen.abid',
                            'nouman.ali',
                            'waqas.sajjad',
                            'shabana',
                            'ijaz.hussain',
                            'faizan.naveed',
                            'arez.ali'
                        ];

                        // Users for NBS Approval
                        $nbsApprovalUsers = [
                            'hassan.farooq',
                            'talha.sarfraz',
                            'asbah.rehman',
                            'sidra.tanveer',
                            'maheen.james'
                        ];

                        // Check user
                        $user = auth()->user();
                        if (
                            in_array($user->group_id, $allowedGroupIds) ||
                            in_array($user->username, array_merge($hodApprovalUsers, $nbsApprovalUsers))
                        ) {
                                                                                        ?>
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <img class="side-menu__icon" src="{{ asset('images/nbs.png') }}" width="21" height="20"
                                alt="Dean Honor List">
                            <span class="side-menu__label">NBS Verification</span>
                            <i class="angle fa fa-angle-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <?php                if (in_array($user->username, $hodApprovalUsers)) { ?>
                            <li>
                                <a class="slide-item" href="<?php                    echo url('/nbs-hod-approval'); ?>">
                                    <span>NBS HOD Approval</span>
                                </a>
                            </li>
                            <?php                } ?>

                            <?php                if (in_array($user->username, $nbsApprovalUsers)) { ?>
                            <li>
                                <a class="slide-item" href="<?php                    echo url('/nbs-ro-approval'); ?>">
                                    <span>NBS Approval</span>
                                </a>
                            </li>
                            <?php                } ?>
                        </ul>
                    </li>
                    <?php            } ?>











                    {{-- Library Side Bars --}}
                    <?php            if (auth()->user()->group_id == 764 || auth()->user()->group_id == 644 || auth()->user()->group_id == 2) { ?>
                    <li class="slide ">
                        <a class="side-menu__item" data-toggle="slide" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
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
                                <a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label"
                                        style="font-weight:bold;">Acquisition</span></a>
                            </li>
                        </ul>
                        <ul class="slide-menu">

                            <li class="sub-slide">
                                <a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label "
                                        style="font-weight:bold;">Cataloging</span><i class="sub-angle fe fe-chevron-down"></i></a>
                                <ul class="sub-slide-menu">
                                    <li class="slide">
                                        <ul class="slide-menu">
                                            <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                                            <li><a class="slide-item" href="{{ url('/library/catalog') }}"><span>Catalog</span></a>
                                            </li>

                                        </ul>
                                        <ul class="slide-menu">
                                            <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                                            <li><a class="slide-item" href="{{ url('/library/add-book-item') }}"><span>New Item
                                                        Entry</span></a></li>

                                        </ul>
                                        <ul class="slide-menu">
                                            <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                                            <li><a class="slide-item" href="{{ url('/library/view-book-item') }}"><span>Update /
                                                        View Book
                                                        Entry</span></a></li>

                                        </ul>
                                        <ul class="slide-menu">
                                            <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                                            <li><a class="slide-item" href="{{ url('/library/spine-barcode') }}"><span>Spine and
                                                        Barcode</span></a></li>

                                        </ul>
                                        <ul class="slide-menu">
                                            <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                                            <li><a class="slide-item" href="{{ url('/library/daily-library-visitor') }}"><span>Daily
                                                        Library Visitor</span></a></li>

                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="sub-slide">
                                <a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label "
                                        style="font-weight:bold;">Circulation</span><i class="sub-angle fe fe-chevron-down"></i></a>
                                <ul class="sub-slide-menu">
                                    <ul class="slide-menu">
                                        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                                        <li><a class="slide-item"
                                                href="{{ url('/library/librarycirculation') }}"><span>Circulation</span></a>
                                        </li>

                                    </ul>
                                    <ul class="slide-menu">
                                        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                                        <li><a class="slide-item" href="{{ url('/library/library-criteria') }}"><span>Books issuing
                                                    Criteria</span></a></li>

                                    </ul>
                                    <ul class="slide-menu">
                                        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                                        <li><a class="slide-item" href="{{ url('/library/circulation-book-history') }}"><span>
                                                    Circulation
                                                    History</span></a></li>

                                    </ul>
                                    <ul class="slide-menu">
                                        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                                        <li><a class="slide-item" href="{{ url('/library/overdue-book') }}"><span>
                                                    Over Due Books</span></a></li>

                                    </ul>
                                    <ul class="slide-menu">
                                        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                                        <!-- <li><a class="slide-item" href="{{ url('/library/datacorrection') }}"><span>Data Correction</span></a></li> -->

                                    </ul>
                                    <ul class="slide-menu">
                                        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                                        <li><a class="slide-item" href="{{ url('/library/return-book') }}"><span>Fine
                                                    Management</span></a></li>

                                    </ul>


                            </li>

                        </ul>
                    <li class="sub-slide">
                        <a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label "
                                style="font-weight:bold;">Reminders</span><i class="sub-angle fe fe-chevron-down"></i></a>
                        <ul class="sub-slide-menu">
                            <li class="slide">
                                <ul class="slide-menu">
                                    <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                                    <li><a class="slide-item" href="{{ url('/library/inbox') }}"><span>Inbox</span></a></li>

                                </ul>



                            </li>
                        </ul>
                    </li>

                    <li class="sub-slide">
                        <a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label "
                                style="font-weight:bold;">Reports</span><i class="sub-angle fe fe-chevron-down"></i></a>
                        <ul class="sub-slide-menu">
                            <li class="slide">
                                <ul class="slide-menu">
                                    <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                                    <li><a class="slide-item" href="{{ url('/library/report_cataloging') }}"><span>Cataloging
                                                Reports</span></a></li>
                                    {{-- <li><a class="slide-item" href="{{url('/library/inbox')}}"><span>Circulation
                                                Reports</span></a>
                                    </li> --}}

                                </ul>



                            </li>
                        </ul>
                    </li>
                    <ul class="slide-menu">
                        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                        <li><a class="slide-item" href="{{ url('/library/room-allocation') }}"><span>Library Room
                                    Allocation</span></a></li>

                    </ul>



                    </li>
                </ul>
                </li>
                <?php            } ?>
                {{-- Library Side Bar Ends here --}}





                <!-- GoToStaff -->
                <?php            if (!(in_array(2, [222, 825])) && auth()->user()->group_id != 825) {
                            //if (auth()->user()->group_id !=222 &&auth()->user()->group_id !=223  && auth()->user()->group_id != 825) {
                                                                                        ?>
                <li class="slide">
                    <a class="side-menu__item" target="_blank"
                        href="<?= config('app.staff_url') ?>/index.php?key=<?= auth()->user()->api_token ?>&username=<?= auth()->user()->username ?>&time=<?= rand(999, 999999) ?>">
                        <img class="side-menu__icon" src="{{ asset('images/staff.png') }}" width="22" height="20"
                            alt="Dean Honor List">
                        <span class="side-menu__label">Staff</span>
                    </a>
                </li>

                <?php            } ?>


                <?php            if (auth()->user()->group_id == 644 || auth()->user()->group_id == 2 || auth()->user()->group_id == 122) { ?>

                <li class="slide">
                    <a class="side-menu__item" target="_blank" href="{{ url('/enrollment-start-stop') }}">
                        <i class="fa fa-power-off side-menu__icon"></i>
                        <span class="side-menu__label">Enrollment Start Stop</span>
                    </a>
                </li>

                <?php            } ?>

                <?php            if (auth()->user()->group_id == 644 || auth()->user()->group_id == 284) { ?>


                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fa fa-user-plus side-menu__icon"></i>
                        <span class="side-menu__label">Enrollments</span><i class="angle fa fa-angle-right"></i></a>
                    <ul class="slide-menu">

                        <li><a class="slide-item" href="{{ url('/road-map-wise-offering') }}"><span> Roadmap Wise
                                    Offering</span></a></li>
                        <li><a class="slide-item" href="{{ url('/road-map-wise-verification') }}"><span> Verify
                                    Offering</span></a></li>
                    </ul>
                </li>

                <?php            } ?>




                <?php            if (auth()->user()->group_id == 284 || auth()->user()->group_id == 122) { ?>
                <li class="slide">
                    <a class="side-menu__item" target="_blank" href="{{ url('/attendance_update_ex') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="side-menu__icon">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <span class="side-menu__label">Attendance Exemption</span>
                    </a>
                </li>
                <?php            } ?>

                <?php            if (auth()->user()->group_id == 644 || auth()->user()->employee_id == 1294 || auth()->user()->group_id == 624 || auth()->user()->group_id == 744 || auth()->user()->group_id == 365 || auth()->user()->group_id == 284 || auth()->user()->group_id == 122) { ?>
                <li class="slide">
                    <a class="side-menu__item" target="_blank" href="{{ url('staff/student-financial') }}">
                        <i class="fa fa-money side-menu__icon"></i>
                        <span class="side-menu__label">Student Financial</span>
                    </a>
                </li>
                <?php            } ?>


                <?php
                        $allowed_usernames = [
                            'somia.maqsood',
                            'abdul.waheed',
                            'tayyaba.nawaz',
                            'talha.sarfraz',
                            'hassan.farooq',
                            'sidra.tanveer',
                            'asbah.rehman',
                            'zainabsanik',
                            'saqib.jamil',
                            'esha.abid',
                            'asif_nadeem',
                            'ahmad.arfeen',
                            'maheen.james'
                        ];

                        if (in_array(auth()->user()->username, $allowed_usernames) || auth()->user()->group_id == 644) {
                                                                                        ?>
                <li class="slide">
                    <a class="side-menu__item" target="_blank" href="{{ url('student-record/student-record') }}">
                        <i class="fa fa-users side-menu__icon"></i>
                        <span class="side-menu__label">Student Record Mgt</span>
                    </a>
                </li>
                <?php            } ?>

                <?php            if (auth()->user()->group_id == '404' || auth()->user()->group_id == '644') { ?>
                <li class="slide">
                    <a class="side-menu__item" href="{{ url('/softwarelist') }}">
                        <i class="fa fa-code side-menu__icon"></i>
                        <span class="side-menu__label">Software Entry </span>
                    </a>
                </li>
                <?php            } ?>

                <?php            if (auth()->user()->group_id == '181' || auth()->user()->group_id == '644') { ?>
                <li class="slide">
                    <a class="side-menu__item" href="{{ url('/emp-employee-details') }}">
                        <i class="fa fa-user side-menu__icon"></i>
                        <span class="side-menu__label">Faculty Profile </span>
                    </a>
                </li>
                <?php            } ?>

                <?php            if (auth()->user()->group_id == 644 || auth()->user()->employee_id == 1294 || auth()->user()->group_id == 324) { ?>
                <li class="slide">
                    <a class="side-menu__item" target="_blank" href="{{ url('accounts/student-financial') }}">
                        <i class="fa fa-credit-card side-menu__icon"></i>
                        <span class="side-menu__label">Student Financial Accounts</span>
                    </a>
                </li>
                <li class="slide">
                    <a class="side-menu__item" target="_blank" href="{{ url('staff/student-financial') }}">
                        <i class="fa fa-credit-card side-menu__icon"></i>
                        <span class="side-menu__label">Student Financial</span>
                    </a>
                </li>
                <?php            } ?>






                <li class="slide">
                    <a class="side-menu__item " data-toggle="slide" href="#">
                        <img class="side-menu__icon" src="{{ asset('images/eportal.png') }}" width="22" height="22"
                            alt="Dean Honor List">
                        <span class="side-menu__label">E-Portal</span><i class="angle fa fa-angle-right"></i></a>
                    <ul class="slide-menu">


                        @if (auth()->user()->username == 'haider.raza' || auth()->user()->group_id == 644)
                            <li><a class="slide-item" href="{{ url('university-holidays-management') }}"><span>University
                                        Holidays</span></a></li>
                        @endif



                        <?php
                        $userDeptId = auth()->user()->dept_id;
                        $userGroupId = auth()->user()->group_id;
                        $allowedDeptIds = [41, 23, 24, 141, 61];
                        $allowedGroupId = '644';
                        if (in_array($userDeptId, $allowedDeptIds) || $userGroupId == $allowedGroupId) { ?>
                        {{-- <li><a class="slide-item" href="{{url('eportal-request')}}"><span>Admin to School</span></a>
                        </li> --}}

                        <?php            } ?>

                        <!-- <?php            if (auth()->user()->username == 'zonia.akbar') { ?>
                                                                                           <?php            } elseif (auth()->user()->username == 'farzana') { ?>
                                                                                            <?php            } ?> -->
                        <li><a class="slide-item" href="{{ url('eportal-approval') }}"><span>E-Portal Approval</span></a></li>
                        <li><a class="slide-item" href="{{ url('eportal-dashboard') }}"><span>E-Portal Requests</span></a>
                        </li>
                        <?php            if (auth()->user()->username == 'saqib.jamil' || auth()->user()->username == 'ahmad.arfeen' || auth()->user()->username == 'bilal.saleem' || auth()->user()->username == 'esha.abid' || auth()->user()->username == 'qasim.ali') { ?>
                        <li><a class="slide-item" href="{{ url('eportal-report-student') }}"><span>Student Exit
                                    Report</span></a></li>
                        <?php            } ?>

                        <?php            if (auth()->user()->username == 'farzana' || auth()->user()->username == 'noreen' || auth()->user()->username == 'asif_nadeem' || auth()->user()->username == 'zaeem' || auth()->user()->username == 'ahmer.sayeed') { ?>

                        <li><a class="slide-item" href="{{ url('eportal-report-staff') }}"><span>E-Portal Report</span></a>
                        </li>
                        <?php            } ?>
                    </ul>
                </li>

                <?php            if (auth()->user()->username == 'ahmad.arfeen' || auth()->user()->username == 'asif_nadeem' || auth()->user()->group_id == 644 || auth()->user()->group_id == 324 || auth()->user()->username == 'farhan.iftikhar' || auth()->user()->username == 'qasim.ali') { ?>
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fa fa-line-chart side-menu__icon"></i>
                        <span class="side-menu__label">Account Reports</span><i class="angle fa fa-angle-right"></i></a>
                    <ul class="slide-menu">
                        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                        <li><a class="slide-item" target="_blank" href="{{ url('fine-challan-student-list') }}">
                                <span class="side-menu__label">Online Payment List</span></a>
                        </li>
                        <li><a class="slide-item" target="_blank" href="{{ url('blocked-student-list') }}">
                                <span class="side-menu__label">Blocked Students</span></a>
                        </li>
                        <li><a class="slide-item" target="_blank" href="{{ url('students_fee_challan') }}">
                                <span class="side-menu__label">Students Fee Challan</span></a>
                        </li>
                        <?php                if (auth()->user()->username == 'ahmad.arfeen' || auth()->user()->username == 'asif_nadeem' || auth()->user()->group_id == 644 || auth()->user()->username == 'farhan.iftikhar') { ?>
                        <li><a class="slide-item" href="{{ url('car-sticker-report') }}">
                                <span class="side-menu__label">Car Sticker Report</span>
                            </a></li>
                        <li><a class="slide-item" href="{{ url('admission_applicant_data') }}">
                                <span class="side-menu__label">Applicant Data Report</span>
                            </a></li>
                        <?php                } ?>

                        <?php                if (auth()->user()->employee_id == 1294 or auth()->user()->employee_id == 6455) { ?>
                        <li><a class="slide-item" href="{{ url('teacher/hr_attendance_report') }}"><span>HR Attendance
                                    Report</span></a></li>
                        <?php                } ?>
                    </ul>
                </li>
                <?php            } ?>


                <?php            if (auth()->user()->group_id == 644 || auth()->user()->group_id == 142) { ?>
                <li class="slide">
                    <a class="side-menu__item" target="_blank" href="{{ url('exam/allow-exam-ips') }}">
                        <i class="fa fa-shield side-menu__icon"></i>
                        <span class="side-menu__label">Allow Exams IPs</span>
                    </a>
                </li>
                <?php            } ?>



                <li class="slide">
                    <a class="side-menu__item " data-toggle="slide" href="#">
                        <img class="side-menu__icon" src="{{ asset('images/sharePoitn.png') }}" width="22" height="22"
                            alt="Dean Honor List">
                        <span class="side-menu__label">SharePoint</span><i class="angle fa fa-angle-right"></i></a>
                    <ul class="slide-menu">


                        <li><a class="slide-item" target="__blank" href="https://share.bnu.edu.pk/sites/facultynstaff"><span>Faculty
                                    SharePoint</span></a></li>
                        <li><a class="slide-item" target="__blank"
                                href="https://share.bnu.edu.pk/sites/students/"><span></span>Student SharePoint</a></li>


                        @php
                            $allowedSharePointEmployeeIds = [
                                553,
                                1313,
                                1273,
                                276,
                                226,
                                259,
                                512,
                                2815,
                                2855,
                                7335,
                                5375,
                                8157,
                                9194,
                                9096,
                                9258,
                                9241,
                                9325,
                                9328,
                                9385,
                                16,
                            ];

                            $user = auth()->user();
                            $hasSharePointAccess =
                                in_array($user->employee_id, $allowedSharePointEmployeeIds) || $user->group_id == 644;
                        @endphp

                        @if ($hasSharePointAccess)
                            <li>
                                <a class="slide-item" target="_blank" href="http://share.bnu.edu.pk/sites/Careers">
                                    <span>Job Portal</span>
                                </a>
                            </li>
                        @endif

                    </ul>
                </li>



                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <img class="side-menu__icon" src="{{ asset('images/studentFine.png') }}" width="20" height="18"
                            alt="Dean Honor List">
                        <span class="side-menu__label">Student Fine</span><i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="/students-fine"><span>Apply Student Fine</span></a></li>

                        <?php            if (auth()->user()->group_id == 644 || auth()->user()->username == 'asif_nadeem') { ?>
                        <li><a class="slide-item" href="/student-fine-categories"><span>Add New Category</span></a></li>
                        <li><a class="slide-item" href="/student-fine-sub-categories"><span>Add New Fine</span></a></li>
                        <li><a class="slide-item" href="/fine-challan-student-list"><span>Student List</span></a></li>
                        <?php            } ?>

                        <?php
                        $usernamesFines = ['asif_nadeem', 'esha.abid', 'saqib.jamil', 'ahmad.arfeen', 'amir.sheikh', 'hassan.farooq'];
                        $roUsernamesFines = ['zonia.akbar', 'haider.raza', 'tayyaba.nawaz', 'qasim.ali'];
                        if (auth()->user()->group_id == 644 || in_array(auth()->user()->username, $usernamesFines) || in_array(auth()->user()->username, $roUsernamesFines)) { ?>
                        <li><a class="slide-item" href="/fine-challan-generated-list"><span>Students Fine Details</span></a>
                        </li>
                        <?php            } ?>
                    </ul>
                </li>


                <?php            if (auth()->user()->username == 'naveed') { ?>
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <span class="side-menu__label">Report Generator</span>
                        <i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li>
                            <a class="slide-item" href="/student-profile-rec"><span>Student Profile Data</span>
                                <!-- Updated label for clarity --></a>
                        </li>
                    </ul>
                </li>
                <?php            } ?>

                <?php            if (
                            auth()->check() && (auth()->user()->group_id == 644 || auth()->user()->group_id == '223' ||
                                in_array(auth()->user()->username, ['asif_nadeem', 'noreen', 'farzana', 'sidra.tanveer', 'zainabsanik']) ||
                                in_array(auth()->user()->username, ['rashid.rana', 'amra.raza', 'bushra.rahman', 'ruhi.khalid', 'shafaat.ahmed']) ||
                                in_array(auth()->user()->username, ['sana.aziz', 'ammar.faiz', 'reema.qadeer', 'arshad.khurshid']))
                        ) { ?>
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <img class="side-menu__icon" src="{{ asset('images/reportG.png') }}" width="20" height="24"
                            alt="Dean Honor List">
                        <span class="side-menu__label">Report Generator</span>
                        <i class="angle fa fa-angle-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li>
                            <a class="slide-item" href="/reports/bnu-admission-reports-generator">
                                <span>BNU Report Generator</span>
                            </a>
                        </li>
                        <li>
                            <a class="slide-item" href="/reports/bnu-admission-reports-generator-detail"><span>Admission
                                    Analysis Portal
                                </span> <!-- Updated label for clarity --></a>
                        </li>

                        <?php                if (auth()->user()->group_id == 644) { ?>
                        <li>
                            <a class="slide-item" href="/student-profile-rec"><span>Student Profile Data</span>
                                <!-- Updated label for clarity --></a>
                        </li>
                        <?php                } ?>

                    </ul>
                </li>
                <?php            } ?>
                <?php            if (auth()->user()->group_id == 644) { ?>
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fa fa-calendar side-menu__icon"></i>
                        <span class="side-menu__label">University Events</span><i class="angle fa fa-angle-right"></i></a>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="{{ url('UniversityEvent/register') }}"><span>Add University
                                    Event</span></a></li>

                        <li><a class="slide-item" href="{{ url('UniversityEvent/event-approval') }}"><span> University Event
                                    List</span></a></li>

                    </ul>
                </li>
                <?php            } ?>
                <?php            if (auth()->user()->username == 'haider.raza' || auth()->user()->username == 'zonia.akbar' || auth()->user()->username == 'muddassar.javed' || auth()->user()->username == 'quratul.ain' || auth()->user()->username == 'abdur.rehman' || auth()->user()->username == 'mahreen.zaib' || auth()->user()->username == 'rabia.shabbir' || auth()->user()->username == 'bilal.saleem' || auth()->user()->username == 'zaeem' || auth()->user()->username == 'umar.imam' || auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'sheeraz' || auth()->user()->username == 'hina.razzaq' || auth()->user()->username == 'fariha.nadeem' || auth()->user()->username == 'ali.kashif' || auth()->user()->username == 'saqib.zia' || auth()->user()->username == 'naveed') { ?>
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fa fa-calendar side-menu__icon"></i>
                        <span class="side-menu__label">University Events</span><i class="angle fa fa-angle-right"></i></a>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="{{ url('UniversityEvent/register') }}"><span>Add University
                                    Event</span></a></li>
                        <?php                if (auth()->user()->username == 'haider.raza' || auth()->user()->username == 'bilal.saleem' || auth()->user()->username == 'zonia.akbar') { ?>
                        <li><a class="slide-item" href="{{ url('UniversityEvent/event-approval') }}"><span> University Event
                                    List</span></a></li>
                        <?php                } ?>
                    </ul>
                </li>
                <?php if (auth()->user()->username == 'haider.raza' || auth()->user()->username == 'bilal.saleem') { ?>
                <li class="slide">
                    <a class="side-menu__item" target="_blank" href="{{ url('admission_qualification') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="side-menu__icon">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <span class="side-menu__label">Applicant Data Uploads</span>
                    </a>
                </li>
                <?php                } ?>
                <?php            } ?>

                <?php if ( auth()->user()->username == 'ijaz.hussain') { ?>
                    <li class="slide">
                        <a class="side-menu__item" target="_blank" href="{{ url('admission_qualification') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="side-menu__icon">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span class="side-menu__label">Applicant Data Uploads</span>
                        </a>
                    </li>
                <?php } ?>




                <?php            if (auth()->user()->username == 'umar.imam' || auth()->user()->username == 'bilal.saleem') { ?>
                <li class="slide">
                    <a class="side-menu__item" href="{{ url('alumni') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <span class="side-menu__label">Alumni</span>
                    </a>
                </li>
                <?php            } ?>



                <?php            if (auth()->user()->username == 'saqib.zia' || auth()->user()->group_id == 644 || auth()->user()->username == 'ali.sher') { ?>
                <li class="slide">
                    <a class="side-menu__item" target="_blank" href="{{ url('lowerstaff_exist_form') }}">
                        <i class="fa fa-sign-out side-menu__icon"></i>
                        <span class="side-menu__label">Lower Staff Exist Form</span>
                    </a>
                </li>
                <?php            } ?>


                <?php
                    }

                    //vc end else part--------------------------------------
                                                                                        ?>
                <?php        if (auth()->user()->username != 'moeedyusuf') { ?>
                <li class="slide">
                    <a class="side-menu__item" target="_blank" href="{{ url('staff-exit-interview-form') }}">
                        <img class="side-menu__icon" src="{{ asset('images/staffExit.png') }}" width="22" height="22"
                            alt="Dean Honor List">
                        <span class="side-menu__label">Staff Exit Interview Form</span>
                    </a>
                </li>
                <?php        } ?>


                <?php        if (auth()->user()->username != 'mansoor.ahmad') { ?>
                <li class="slide">
                    <a class="side-menu__item " data-toggle="slide" href="#">
                        <img class="side-menu__icon" src="{{ asset('images/mcmPortal.png') }}" width="22" height="22"
                            alt="Dean Honor List">
                        <span class="side-menu__label"> MCM Portal</span><i class="angle fa fa-angle-right"></i></a>
                    <ul class="slide-menu">

                        <li><a class="slide-item" href="{{ url('admin/eportal-dashboard') }}"><span>MCM Portal
                                    Dashboard</span></a></li>
                        <?php

                        $userEmpType = auth()->user()->sis_emp_type;
                        $userGroupId = auth()->user()->group_id;
                        $allowedEmpType = [4, 5];
                        $allowedGroupId = '644';


                        if (in_array($userEmpType, $allowedEmpType) || $userGroupId == $allowedGroupId) { ?>
                        <li><a class="slide-item" href="{{ url('eportal-request') }}"><span>MCM Portal Request</span></a></li>

                        <?php            } ?>

                    </ul>
                </li>
                <?php            if (auth()->user()->username != 'moeedyusuf') { ?>
                <li class="slide">
                    <a class="side-menu__item" target="_blank" href="{{ url('visiting_faculty') }}">
                        <img class="side-menu__icon" src="{{ asset('images/VF.png') }}" width="21" height="18"
                            alt="Dean Honor List">
                        <span class="side-menu__label">Visiting Faculty</span>
                    </a>
                </li>
                <li class="slide">
                    <a class="side-menu__item" target="_blank" href="{{ url('staff-exit-interview-form') }}">
                        <img class="side-menu__icon" src="{{ asset('images/staffExit.png') }}" width="22" height="22"
                            alt="Dean Honor List">
                        <span class="side-menu__label">Staff Exit Interview Form</span>
                    </a>
                </li>
                <?php            } ?>

                <?php        } ?>
            @endif
        @endif
    {{-- TA IF ENDED --}}
    <?php
$allowedUsers = [
    'ijaz.hussain',
    'bushra.rahman',
    'shafaat.ahmed',
    'tahir.kamran',
    'omer.hassan',
    'rashid.rana',
    'ruhi.khalid',
    'amra.raza',
    'shabana',
    'zainabsanik'
];

if (auth()->user()->group_id == 644 || in_array(auth()->user()->username, $allowedUsers)) {
?>
    <li class="slide">
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{ url('challan-report') }}">
            <img class="side-menu__icon" src="{{ asset('images/cp.png') }}" width="20" height="20"
                alt="Dean Honor List">
            <span class="side-menu__label">Challan Report</span>
        </a>
    </li>

    <?php } ?>
    <?php
$allowedUsers = [
    'muddassar.javed'
];

if (in_array(auth()->user()->username, $allowedUsers)) {
?>

    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{ url('reports/faculty/list') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Faculty Assurance Report</span>
        </a>
    </li>

    <?php } ?>
    <?php
$allowedUsers = [
    'omar.naeem',
    'fatima.khalid',
    'muhammad.zafran',
    'mohaira.ahmad',
    'sana.iqbal',
    'ali.abbas',
    'naeem.khan',
    'ali.murtaza',
    'ahsan.ahmad',
    'imran.sharif',
    'fareed.qureshi',
    'ijaz.hussain',
    'umar.farooq',
    'anwar.haq',
    'quratul.ain',
    'shazil.ali',
    'rameen.shahram',
    'ali.hafeez',
    'sumaiya.naumaan',
    'muhammad.abubakar',
    'hasnain.ahmad',
    'rabia.malik',
    'nayab.tariq',
    'umar.farooq',
    'amber.razzaq',
    'shazia'

];
if (in_array(auth()->user()->username, $allowedUsers)) {
?>

    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{ url('road-map-wise-offering') }}">
            <img class="side-menu__icon" src="{{ asset('images/RS.png') }}" width="21" height="13"
                alt="Dean Honor List">
            <span class="side-menu__label">Roadmap Session</span>
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
                        <svg class="header-icon" x="1008" y="1248" viewBox="0 0 24 24" height="100%" width="100%"
                            preserveAspectRatio="xMidYMid meet" focusable="false">
                            <path opacity=".3" d="M12 6.5c-2.49 0-4 2.02-4 4.5v6h8v-6c0-2.48-1.51-4.5-4-4.5z">
                            </path>
                            <path
                                d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-11c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2v-5zm-2 6H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6zM7.58 4.08L6.15 2.65C3.75 4.48 2.17 7.3 2.03 10.5h2a8.445 8.445 0 013.55-6.42zm12.39 6.42h2c-.15-3.2-1.73-6.02-4.12-7.85l-1.42 1.43a8.495 8.495 0 013.54 6.42z">
                            </path>
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

    // Add active class to home button when on home page
    // This runs after sidemenu scripts to ensure home button gets active class
    (function () {
        function checkAndSetHomeActive() {
            if (typeof jQuery === 'undefined') {
                setTimeout(checkAndSetHomeActive, 50);
                return;
            }

            var pathname = window.location.pathname;
            var pageUrl = window.location.href.split(/[?#]/)[0];
            var origin = window.location.origin;

            // Normalize pathname - remove trailing slash except for root
            var normalizedPath = pathname === '/' ? '/' : pathname.replace(/\/$/, '');

            // Check if we're on the home page (root URL)
            var isHomePage = normalizedPath === '/' || normalizedPath === '' ||
                pageUrl === origin || pageUrl === origin + '/';

            if (isHomePage) {
                jQuery('.home_btn').addClass('active');
            }
        }

        // Run immediately and also on document ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(checkAndSetHomeActive, 150);
            });
        } else {
            setTimeout(checkAndSetHomeActive, 150);
        }

        // Also run on jQuery ready as additional backup
        if (typeof jQuery !== 'undefined') {
            jQuery(document).ready(function () {
                setTimeout(checkAndSetHomeActive, 100);
            });
        }
    })();
</script>