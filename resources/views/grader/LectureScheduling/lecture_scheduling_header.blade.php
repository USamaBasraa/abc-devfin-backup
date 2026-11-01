<style>
  .normal_link {
    background: #dceae2;
    color: #101010 !important;
    border-radius: 25px;
  }

  .panel-tabs li {
    margin: 3px;
  }


  /* ==========================
   Production Tabs UI
========================== */

.tabs-menu{
    margin-bottom: 24px;
}

.panel-tabs{
    display:flex;
    align-items:center;
    gap:14px;
    padding:0 0 12px;
    margin:0;
    list-style:none;
    border-bottom:2px solid rgba(23,103,95,.10);
}

.panel-tabs li{
    margin:0;
}

.panel-tabs li a{
    display:flex;
    align-items:center;
    justify-content:center;
    /* min-height:54px; */
    padding:0 32px;
    border:none !important;
    border-radius:999px;
    background:#17675F1A;
    color:#17675F80 !important;
    font-size:18px;
    font-weight:500;
    line-height:1;
    text-decoration:none !important;
    transition:all .25s ease;
    position:relative;
    white-space:nowrap;
}

.panel-tabs li a:hover{
    background:#17675F26;
    color:#17675F !important;
}

.panel-tabs li a.active,
.panel-tabs li a.active:hover,
.panel-tabs li.active a{
    background:#1F776E !important;
    color:#fff !important;
    box-shadow:0 8px 18px rgba(31,119,110,.18);
}

.panel-tabs li a.active::after,
.panel-tabs li.active a::after{
    content:"";
    position:absolute;
    left:0;
    right:0;
    bottom:-14px;
    width:100%;
    height:4px;
    border-radius:999px;
    background:#1F776E;
}

.normal_link{
    background:#17675F1A !important;
    color:#17675F80 !important;
}



.panel-tabs li a{
    /* height:54px; */
    /* min-height:54px; */
    padding:12px 28px !important;
    display:flex !important;
    align-items:center !important;
    justify-content:center !important;

    line-height:1 !important;
    vertical-align:middle;
}
</style>

<?php 
      if($action == 'TA'){  
        $co_id = $co_idd;
      }else{
        $co_id = $get_co_id;
      }
    
    
    ?>



    <div class="tabs-menu " style="
    width: 100%;
">
      <!-- Tabs -->
      <ul class="nav panel-tabs">
        <li class=""><a href="#lectures" onclick="window.location.href ='<?= url('/Lectures/' . $co_id) ?>' " class="<?= ($active_tab_name == 'Lecture' ? 'active' : 'normal_link') ?>" data-toggle="tab">
             Lectures
          </a></li>
        <li><a href="#material" onclick="window.location.href ='<?= url('/teacher/course_material/' . $co_id) ?>' " class="<?= ($active_tab_name == 'Study_Material' ? 'active' : 'normal_link') ?>" data-toggle="tab">
             All Study Material
          </a></li>
        <li><a href="#assignment" onclick="window.location.href ='<?= url('/teacher/course_assignment/' . $co_id) ?>' " class="<?= ($active_tab_name == 'Assignments' ? 'active' : 'normal_link') ?>" data-toggle="tab">
            All Assignments
          </a></li>
        <!-- <li><a href="#evaluation" onclick="window.location.href ='/teacher/course_evaluation/<?= $co_id; ?>' " class="<?= ($active_tab_name == 'Evaluation' ? 'active' : 'normal_link') ?>" data-toggle="tab">
            <i class="fa fa-calculator"></i> Evaluation
          </a></li> -->
      </ul>
    </div>