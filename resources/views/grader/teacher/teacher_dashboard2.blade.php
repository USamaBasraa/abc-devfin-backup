<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
?>
<?php
  $file_name= "app";
  if (auth()->user()->cms_sidebar=='new')
  {
  $file_name= "updated_app";
  }
?> 
{{-- @dd('working'); --}}
@extends('layouts.' . $file_name, ['pageTitle' => 'Dashboard'])  
@section('content')

 @php
       // $staffHealthInsuranceExists = DB::table('student_feedbacks')
          //  ->where('sf_type', 'staff_health_insurance_survey')
          //  ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = LOWER(?)', [auth()->user()->username])
           // ->exists();
    @endphp
 @php
 //if (!$staffHealthInsuranceExists) {
   // if (!session('staff_health_insurance_survey_closed')){
    //    @include('surveys.staff-health-insurance-modal')
//}}
 @endphp
     
<div class="page-body">


  <div class="container-fluid"> 
    <div class="row equal-height">
      <div class="col-12 col-sm-6">
      <!-- <input type="button" value="local storage button" onclick="checkCookie()">  
      <input type="button" value="idb btn" onclick="idb()">  
      <input type="button" value="GET get_idb" onclick="get_idb()">   -->
      
      @include('grader.teacher.teacher_profile')</div>
      <div class="col-12 col-sm-6 notification-wrapper">@include('grader.teacher.notifications')</div>
    </div>
  </div>
  <style>
    html, body {
      height: 100%;
    }

    .tab-content {
      height: 100%;
      overflow: auto;
    }

    @media (min-width: 576px) {
      .tab-pane {
        height: calc(100% - 56px);
      }
    }
    .cus{
        background-color: gainsboro;
        border-radius:8px !important;
    }
    






    .row.equal-height {
    display: flex;
    align-items: stretch;
}

.row.equal-height > [class*="col-"] {
    display: flex;
    flex-direction: column;
}

/* Profile section */
.profile-overview-section {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.employee-profile-card {
    flex: 1;
    height: auto !important;
}

/* Notification section */
.notification-wrapper {
    display: flex;
    flex-direction: column;
}

.notification-wrapper > .d-flex {
    flex: 0 0 auto;
}

.notification-wrapper > .card {
    flex: 1;
    height: auto !important;
    display: flex;
    flex-direction: column;
    margin-bottom: 0 !important;
}

.notification-wrapper .card-body {
    flex: 1;
    height: auto !important;
    min-height: 0;
    overflow-y: auto;
}













































/* =========================================================
   FIGMA MAIN DASHBOARD
   Content Area Styling Only
   Figma: Faculty CMS Design / Main Dashboard (1:38)
   ========================================================= */

@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap');


/* =========================================================
   1. MAIN CONTENT
   ========================================================= */

.app-content.main-content {
    --figma-green: #17675F;
    --figma-green-soft: rgba(23, 103, 95, 0.10);
    --figma-text: #252525;
    --figma-muted: #949494;
    --figma-orange: #FFBA49;
    --figma-white: #FFFFFF;
    --figma-shadow: 0 0 10px rgba(0, 0, 0, 0.05);

    font-family: 'Montserrat', sans-serif !important;
    color: var(--figma-text);

    /* Keep existing content layout/padding */
    padding: 25px !important;
    padding-bottom: 50px !important;

    /* Figma dashboard background */
    /* background:
        linear-gradient(
            to left,
            rgba(23, 103, 95, 0.15),
            rgba(255, 255, 255, 0.15)
        ) !important; */
}


/* Apply Montserrat only inside main content */
.app-content.main-content,
.app-content.main-content button,
.app-content.main-content input,
.app-content.main-content select,
.app-content.main-content textarea {
    font-family: 'Montserrat', sans-serif;
}

.app-content.main-content .side-app,
.app-content.main-content .page-body,
.app-content.main-content .container-fluid {
    background: transparent !important;
}


/* =========================================================
   2. COMMON SECTION HEADINGS
   Figma: 20px / SemiBold
   ========================================================= */

.main-content .profile-overview-title,
.main-content .section-title {
    margin-top: 0 !important;
    margin-bottom: 14px !important;

    font-family: 'Montserrat', sans-serif !important;
    font-size: 20px !important;
    font-weight: 600 !important;
    line-height: 1.2 !important;

    color: #252525 !important;
}


/* View All / View Timetable etc. */
.main-content .section-view-all {
    font-family: 'Montserrat', sans-serif !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    line-height: 1.2;

    color: #252525 !important;
    text-decoration: underline !important;
}

.main-content .section-view-all:hover {
    color: #17675F !important;
}


/* =========================================================
   3. PROFILE + NOTIFICATIONS COLUMN RATIO
   Figma: approx 737px + 20px + 330px
   ========================================================= */

/* Profile and Notifications: Bootstrap 6 + 6 */
@media (min-width: 768px) {
    .main-content .row.equal-height {
        display: flex !important;
        flex-wrap: wrap !important;
        margin-left: -10px !important;
        margin-right: -10px !important;
    }

    .main-content .row.equal-height > .col-md-6 {
        width: 50% !important;
        max-width: 50% !important;
        flex: 0 0 50% !important;
        padding-left: 10px !important;
        padding-right: 10px !important;
    }
}




/* =========================================================
   6. INFO / MESSAGE BANNER
   ========================================================= */

.main-content .message_div .alert {
    min-height: 50px;

    display: flex;
    align-items: center;

    padding: 10px 20px !important;

    background: rgba(56, 128, 222, 0.05) !important;

    border: 1px solid rgba(56, 128, 222, 0.25) !important;
    border-radius: 10px !important;

    box-shadow: none !important;

    color: #252525 !important;

    font-size: 16px !important;
    font-weight: 400 !important;
}


/* =========================================================
   7. WELCOME + TIMETABLE PROGRESS
   ========================================================= */

.main-content #today_lectures {
    margin-top: 20px !important;
    margin-bottom: 20px !important;
}


/* Remove old blue alert look */
.main-content #today_lectures > .alert {
    padding: 0 !important;
    margin: 0 !important;

    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;

    box-shadow: none !important;

    color: #252525 !important;

    font-size: 16px !important;
    font-weight: 400 !important;
}


/* Existing floated layout -> Figma aligned row */
.main-content #today_lectures > .alert > div {
    display: flex !important;
    align-items: center !important;

    gap: 20px;
}

.main-content #today_lectures > .alert > div > div:first-child {
    float: none !important;

    width: 46% !important;

    font-size: 16px !important;
    font-weight: 400 !important;

    color: #252525;
}

.main-content #today_lectures > .alert > div > div:nth-child(2) {
    float: none !important;

    width: auto !important;
    flex: 1;
}


/* Figma progress outer container = 30px */
.main-content #today_lectures .progress {
    width: 100%;

    height: 30px !important;

    padding: 2px !important;
    margin: 0 !important;

    border: 1px solid #17675F !important;
    border-radius: 50px !important;

    background: transparent !important;

    box-shadow: none !important;

    overflow: hidden;
}


/* Figma inner progress */
.main-content #today_lectures .progress-bar {
    height: 24px !important;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50px !important;

    background: linear-gradient(
        90deg,
        #17675F 0%,
        #FFFFFF 38.942%,
        #17675F 100%
    ) !important;

    color: #FFFFFF !important;

    font-size: 12px !important;
    font-weight: 400 !important;
    line-height: 24px !important;

    box-shadow: none !important;
}


/* =========================================================
   8. TODAY LECTURES - OUTER CARD
   ========================================================= */

.main-content .today_lec_card {
    padding: 20px !important;

    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 10px !important;

    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05) !important;

    overflow: visible;
}


/* Individual course panel */
.main-content .today_lec_card .card.e-panel {
    background: rgba(23, 103, 95, 0.10) !important;

    border: 0 !important;
    border-radius: 5px !important;

    box-shadow: none !important;

    margin-bottom: 14px !important;

    overflow: hidden !important;
}

.main-content .today_lec_card > .card.e-panel:last-of-type {
    margin-bottom: 0 !important;
}


/* Course header */
.main-content .today_lec_card .card.e-panel > .card-header {
    min-height: 42px !important;

    padding: 14px 12px 5px !important;

    background: transparent !important;

    border: 0 !important;
}


/* Figma course title = 16px */
.main-content .today_lec_card .card-header strong {
    font-size: 16px !important;
    font-weight: 500 !important;
    line-height: 1.3 !important;

    color: #252525 !important;
}


/* Hide old book icon */
.main-content .today_lec_card .card-header strong > .fa-book {
    display: none !important;
}


/* =========================================================
   9. ABSOLUTE / RELATIVE BADGE
   ========================================================= */

.main-content
.today_lec_card
.card.e-panel
> .card-header
.col-md-2 a {

    width: auto !important;
    min-width: 44px !important;

    height: 12px !important;
    min-height: 12px !important;

    padding: 0 5px !important;

    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: #FFBA49 !important;

    border-radius: 50px !important;

    color: #FFFFFF !important;

    font-size: 8px !important;
    font-weight: 400 !important;
    line-height: 12px !important;

    text-decoration: none !important;
}


/* Expanded course badge as shown in Figma */
.main-content
.today_lec_card
.card.e-panel.lecture-open
> .card-header
.col-md-2 a {

    min-width: 60px !important;
    height: 20px !important;
    min-height: 20px !important;

    padding: 0 8px !important;

    font-size: 12px !important;
    line-height: 20px !important;
}


/* =========================================================
   10. TODAY LECTURE ACTION ICONS
   ========================================================= */

.main-content .today_lec_card .card.e-panel > .card-footer {
    padding: 8px 12px 12px !important;

    background: transparent !important;

    border: 0 !important;
}


/* Each action */
.main-content .today_lec_card .lecture_icons .card-block {
    min-height: 64px !important;

    padding: 4px 2px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background: transparent !important;

    text-align: center !important;
}


/* Figma action icons approximately 20-24px */
.main-content .today_lec_card .lecture_icons img {
    width: 24px !important;
    height: 24px !important;

    max-width: 24px !important;
    max-height: 24px !important;

    object-fit: contain !important;

    margin-bottom: 5px;
}


/* Action labels = 14px */
.main-content .today_lec_card .lecture_icons a,
.main-content .today_lec_card .lecture_icons p {
    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.2 !important;

    color: #252525 !important;

    text-decoration: none !important;
}


/* Expanded details */
.main-content .today_lec_card .multi-collapse.show {
    background: transparent !important;

    border-top: 1px solid rgba(23, 103, 95, 0.12) !important;

    box-shadow: none !important;
}


/* =========================================================
   11. TIME TABLE
   ========================================================= */

.main-content .timetable-card {
    background: #FFFFFF !important;

    border: 0 !important;
    border-radius: 10px !important;

    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05) !important;

    overflow: hidden !important;
}

.main-content .timetable-card .card-body {
    padding: 18px 20px !important;
}


/* Keep current table markup but give Figma visual treatment */
.main-content .timetable-table {
    margin-bottom: 0 !important;

    color: #252525 !important;

    font-size: 14px !important;
    font-weight: 400 !important;
}

.main-content .timetable-table th,
.main-content .timetable-table td {
    vertical-align: middle !important;

    border-color: rgba(23, 103, 95, 0.12) !important;
}

.main-content .timetable-table > thead > tr > th {
   background-color: #17675F !important;
    color: #ffffff !important;

    font-size: 14px !important;
    font-weight: 500 !important;
}


/* =========================================================
   12. COMMON CARDS
   ========================================================= */

.main-content .employee-profile-card,
.main-content .notification-wrapper > .card,
.main-content .timetable-card {
    transition: none !important;
}


/* =========================================================
   13. RESPONSIVE
   ========================================================= */

@media (max-width: 767px) {

    .app-content.main-content {
        padding: 16px !important;
        padding-bottom: 40px !important;
    }

    .main-content .row.equal-height {
        display: flex !important;
        flex-direction: column !important;
    }

    .main-content .employee-profile-wrapper {
        flex-direction: column !important;
        align-items: stretch !important;
    }

    .main-content .employee-profile-image {
        flex: none !important;

        text-align: center;
    }

    .main-content .employee-profile-image img {
        width: 100% !important;
        max-width: 234px !important;
        height: 259px !important;

        margin: 0 auto;
    }

    .main-content .employee-name {
        margin-top: 5px !important;
        text-align: center;
    }

    .main-content .employee-details-grid {
        grid-template-columns: 1fr !important;
    }

    .main-content .employee-detail-item.full-width {
        grid-column: auto;
    }

    .main-content #today_lectures > .alert > div {
        flex-direction: column !important;
        align-items: stretch !important;

        gap: 12px;
    }

    .main-content #today_lectures > .alert > div > div:first-child,
    .main-content #today_lectures > .alert > div > div:nth-child(2) {
        width: 100% !important;
    }
}
h1, h2, h3, h4, h5, h6 {
    font-family: 'Montserrat', sans-serif !important;
}









.row.equal-height {
    display: flex;
    align-items: stretch;
}

.row.equal-height > [class*="col-"] {
    display: flex;
    flex-direction: column;
}

/* Profile apni natural height rakhega */
.profile-overview-section {
    display: flex;
    flex-direction: column;
    height: auto !important;
    min-height: 0 !important;
}

.employee-profile-card {
    height: auto !important;
    min-height: 0 !important;
    overflow: visible;
}

.employee-profile-wrapper {
    height: auto !important;
}

/*
 Notification content row ki height increase nahi karega.
 Row ki height profile ki natural height se calculate hogi.
*/
.notification-wrapper {
    position: relative;
    min-height: 0;
}

.notification-wrapper > .figma-notifications-wrap {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    width: auto;
    height: 100% !important;
    min-height: 0 !important;
    box-sizing: border-box;
}

.notification-wrapper .figma-notifications {
    display: flex;
    flex: 1;
    flex-direction: column;
    height: auto !important;
    min-height: 0 !important;
    margin-bottom: 0 !important;
}

.notification-wrapper .figma-notifications__body {
    flex: 1;
    height: auto !important;
    min-height: 0 !important;
    overflow-y: auto;
}

/* Mobile par sections one-by-one natural height mein */
@media (max-width: 767.98px) {
    .row.equal-height {
        display: block;
    }

    .notification-wrapper {
        position: static;
    }

    .notification-wrapper > .figma-notifications-wrap {
        position: static;
        width: 100%;
        height: auto !important;
        min-height: 0 !important;
    }

    .notification-wrapper .figma-notifications {
        min-height: 332px !important;
    }
}



@media (min-width: 768px) {
    .notification-wrapper > .figma-notifications-wrap {
        position: absolute !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: auto !important;
        min-height: 0 !important;
        /* padding: 4px 0 0 !important; */
        padding-top: 4px !important;
        padding-bottom: 0 !important;
        box-sizing: border-box;
    }

    .notification-wrapper .figma-notifications {
        display: flex !important;
        flex: 1 !important;
        flex-direction: column;
        /* width: 100% !important; */
        height: auto !important;
        min-height: 0 !important;
        margin: 0 !important;
    }

    .notification-wrapper .figma-notifications__body {
        flex: 1 !important;
        height: auto !important;
        min-height: 0 !important;
        overflow-y: auto;
    }
}
@media (min-width: 576px) {
    .row.equal-height > .notification-wrapper {
        display: flex !important;
        flex-direction: column !important;
        min-height: 0 !important;
        overflow: hidden !important;
    }

    .notification-wrapper > .d-flex {
        flex: 0 0 auto !important;
    }

    .notification-wrapper > .card {
        display: flex !important; 
        flex: 1 1 0 !important;
        flex-direction: column !important;
        height: 0 !important;
        min-height: 0 !important;
        margin-bottom: 0 !important;
        overflow: hidden !important;
    }

    .notification-wrapper > .card > .card-body {
        flex: 1 1 0 !important;
        height: 0 !important;
        min-height: 0 !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
    }
}
  </style>

  <?php
if(auth()->user()->group_id=="8"){



  $completed = 0 ;
  $pending = 0 ;
  if(count($obj_summary)>0){

  }else{
    //$completed=1;
    $pending=1;
  }
    foreach($obj_summary as $rec_sum):
        if($rec_sum->tm_status=="Completed"){
          $completed = $rec_sum->tr;
        }
        if($rec_sum->tm_status=="Pending"){
          $pending = $rec_sum->tr;
        }
    endforeach;   
  ?>
<!--Row-->
<div class="col-xl-12 col-lg-6">
	<!--Row-->
  <div class="row">
							<div class="col-lg-4">
								<div class="card">
									<div class="card-body">
										<div class="row">
											<div class="col mb-lg-4 mb-xl-0">
												<div class="mb-2 fs-18 text-muted">
													Total Task
												</div>
												<h1 class="font-weight-bold mb-1"><?php echo $completed+$pending;?></h1>
												<!-- <span class="text-primary"><i class="fa fa-arrow-up mr-1"></i> +1.4%</span> -->
											</div>
											<div class="col col-auto">
												<div class="mx-auto chart-circle chart-circle-md chart-circle-primary mt-sm-0 mb-0" data-value="100" data-thickness="12" data-color="#4454c3">
													<div class="mx-auto chart-circle-value text-center fs-20">100%</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="card">
									<div class="card-body">
										<div class="row">
											<div class="col mb-lg-4 mb-xl-0">
												<div class="mb-2 fs-18 text-muted">
                        Pending 
												</div>
												<h1 class="font-weight-bold mb-1"><?php echo $pending;?></h1>
												<span class="text-success"><i class="fa fa-arrow-up mr-1"></i> <?php echo $completed;?></span>
											</div>
											<div class="col col-auto">
												<div class="mx-auto chart-circle chart-circle-md chart-circle-success mt-sm-0 mb-0" data-value="<?php echo ($pending>0?(($pending)/($completed+$pending)):0);?>" data-thickness="12" data-color="#2dce89">
													<div class="mx-auto chart-circle-value text-center fs-20"><?php echo round(($pending>0?(($pending)/($completed+$pending)):0)*100);?>%</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="card">
									<div class="card-body">
										<div class="row">
											<div class="col mb-lg-4 mb-xl-0">
												<div class="mb-2 fs-18 text-muted">
                        Completed
												</div>
												<h1 class="font-weight-bold mb-1"><?php echo $completed;?></h1>
												<span class="text-danger"><i class="fa fa-arrow-down mr-1"></i><?php echo $pending;?></span>
											</div>
											<div class="col col-auto">
												<div class="mx-auto chart-circle chart-circle-md chart-circle-secondary mt-sm-0 mb-0" data-value="<?php echo ($completed>0?(($completed)/($completed+$pending)):0);?>" data-thickness="12" data-color="#f7346b">
													<div class="mx-auto chart-circle-value text-center fs-20"><?php echo round(($completed>0?(($completed)/($completed+$pending)):0)*100);?>%</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
				
            <div class="col-xl-12 col-lg-6">
								<div class="row">
									<div class="col-xl-6 col-md-12 col-lg-12">
										<div class="card overflow-hidden">
											<div class="card-body">
												<div class="row">
                          <a href="{{url('staff/todo-list')}}"  class="btn btn-success  btn-block">
                            <button type="button" class="btn btn-success  btn-block">ToDo List</button>
                          </a>
													<div class="col-6 col-auto">
														<div id="spark1"></div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-xl-6 col-md-12 col-lg-12">
										<div class="card overflow-hidden">
											<div class="card-body">
                      <a href="{{url('staff/task-list')}}" class="btn btn-primary btn-block">
												<div class="row">
                      
													<button type="button" class="btn btn-primary btn-block">Task Management</button>
                        
                          <div class="col-6 col-auto">
														<div id="spark2"></div>
													</div>
												</div>
                        </a>
											</div>
										</div>
									</div>
			
								</div>
							</div>
            
  <?php
  if(auth()->user()->username=='aizaz.hussain'){ ?>
        @include('grader.teacher.faculty_courses')

 <?php }
  }else{
   if(auth()->user()->group_id != 825){ ?>
  <div class="container-fluid" >
    <div class="row e-panel">
      <div class="col-12 col-sm-12 col-lg-12">
        @include('grader.teacher.faculty_courses')
      </div>
    </div>
  </div>
  <?php }else{ ?>
    <div class="container-fluid" >
      <div class="row e-panel">
        <div class="col-12 col-sm-12 col-lg-12">
           @include('grader.teacher.ta_courses')
        </div>
      </div>
    </div>
  <?php } ?>

  <?php 
   if(auth()->user()->group_id != 825){ ?>
   <div class="container-fluid" >
    <div class="row">
      <div class="col-12 col-sm-12 col-lg-12">
        @include('grader.teacher.timetable')
      </div>
    </div>
  </div> 
  <?php } } ?>

</html>
  
</script>


</div>
<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script>
 function get_idb(){
        var request = indexedDB.open('myDatabase', 1);

      request.onsuccess = function(event) {
        var db = event.target.result;
        var transaction = db.transaction(['myObjectStore'], 'readonly');
        var objectStore = transaction.objectStore('myObjectStore');
        var getAllRequest = objectStore.getAll();

        getAllRequest.onsuccess = function(event) {
          var data = event.target.result;
          data.forEach(function(item) {
            console.log(item.name);
          });
        };
      };
 }
 function idb(){
  // Open a connection to the database
var request = indexedDB.open('myDatabase', 1);

// Create the object store if it doesn't already exist
request.onupgradeneeded = function(event) {
  var db = event.target.result;
  var objectStore = db.createObjectStore('myObjectStore', { keyPath: 'id' });
  objectStore.createIndex('name', 'name', { unique: false });
};

// Add some data to the object store
request.onsuccess = function(event) {
  var db = event.target.result;
  var transaction = db.transaction(['myObjectStore'], 'readwrite');
  var objectStore = transaction.objectStore('myObjectStore');
  var data = [
    { id: 1, name: 'John', age: 30 },
    { id: 2, name: 'Jane', age: 25 },
    { id: 3, name: 'Bob', age: 40 }
  ];
  data.forEach(function(item) {
    objectStore.add(item);
  });
};

 }     

function setCookie(cname,cvalue,exdays) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  let expires = "expires=" + d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(';');
  for(let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function checkCookie() {


  let user = getCookie("username");
  alert(user);
  if (user != "") {
    alert("Welcome again " + user);
  } else {
     user = prompt("Please enter your name:","");
     if (user != "" && user != null) {
       setCookie("username", user, 30);
     }
  }
}

    function generateUUID() {
  let d = new Date().getTime();
  if (window.performance && typeof window.performance.now === "function") {
    d += performance.now(); // use high-precision timer if available
  }
  const uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
    const r = (d + Math.random() * 16) % 16 | 0;
    d = Math.floor(d / 16);
    return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
  });
  return uuid;
}

$(document).ready(function() {
  //alert('Please wait');

  //localStorage.setItem("lastname", "Smith");
  // Retrieve
  var getid = localStorage.getItem("lastname");
  
 // alert(getid);

  // const deviceId = localStorage.getItem('device_id');// || generateUUID();
  // alert(deviceId);
  // localStorage.setItem('device_id', deviceId);
  // $.cookie('device_id', deviceId, { expires: 30 }); // expires in 30 days
});

function localStore(){
  localStorage.setItem("lastname", "Smith");
}
    </script>

@stop

@section('javascript')
<script>
  


</script>
@stop
