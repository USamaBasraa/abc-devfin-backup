




  <style>

    .normal_link{
  
    margin-right: 70px;
    margin-left: 60px;


    }
 
    a:active {
      filter: brightness(50%);
}

  </style>



  <div class="card-body p-6">
    <div class="panel panel-primary">
      <div class="tab-menu-heading">
        <div class="tabs-menu ">
          <!-- Tabs -->
         
            <a href="#lectures" class="normal_link " onclick="window.location.href ='<?=url('/Lectures/'.$get_co_id)?>' "  >
            <img src="http://grader9.bnu.edu.pk/assets/images/lec.png" width="11%" style=" border-radius:22px;" class="active"> 
          </a>
          <a href="#material" class="normal_link" onclick="window.location.href ='<?=url('/teacher/course_material/'.$get_co_id)?>' " class="<?=($active_tab_name=='Study_Material'?'active':'')?> " data-toggle="tab">
          <img src="http://grader9.bnu.edu.pk/assets/images/stu.png" width="11%" style=" border-radius:22px;" > 
          </a>
          <a href="#assignment" class="normal_link" onclick="window.location.href ='<?=url('/teacher/course_assignment/'.$get_co_id)?>' " class="<?=($active_tab_name=='Assignments'?'active':'')?>" data-toggle="tab">
          <img src="http://grader9.bnu.edu.pk/assets/images/Assignments.png" width="11%" style=" border-radius:22px;"> 
          </a>
          <a href="#evaluation" class="normal_link" onclick="window.location.href ='/teacher/course_evaluation/<?=$get_co_id ;?>' " class="<?=($active_tab_name=='Evaluation'?'active':'')?>" data-toggle="tab">
          <img src="http://grader9.bnu.edu.pk/assets/images/eva.png" width="11%" style=" border-radius:22px;"> 
          </a>
          
        </div>
      </div>
    </div>
  


  <!--

  <div class="card-header">



      <ul class="nav nav-tabs">
        <li class="nav-item" style="width:24%; color:#000 !important;text-align:center;">
            <a href="#home" onclick="window.open('/Lectures/<?=$get_co_id ;?>');" class="nav-link <?=($active_tab_name=='Lecture'?'active':'')?>" data-toggle="tab" style="color:#000 !important;text-align:center;">
                <img src="{{asset('images/scheduling.png')}}" width="40px"/><br />
                Lectures
            </a>
        </li>
        <li class="nav-item"  id="Study" style="width:24%;">
            <a onclick="window.open('/teacher/course_material/<?=$get_co_id ;?>');" href="#profile" class="nav-link <?=($active_tab_name=='Study_Material'?'active':'')?>" data-toggle="tab" style="color:#000 !important;text-align:center;">
                <img src="{{asset('images/ebook.png')}}" width="40px"/><br />
                Study Material 
            </a>
        </li>
        <li class="nav-item"  id="Assignments" style="width:24%;">
            <a href="#messages" onclick="window.open('/teacher/course_assignment/<?=$get_co_id ;?>');" class="nav-link <?=($active_tab_name=='Assignments'?'active':'')?>" data-toggle="tab" style="color:#000 !important;text-align:center;">
                <img src="{{asset('images/test.png')}}" width="40px"/><br />
                Assignments
            </a>
        </li>
        <li class="nav-item"  id="grade" style="width:24%;">
            <a href="#grading" onclick="window.open('/teacher/course_evaluation/<?=$get_co_id ;?>');"  class="nav-link <?=($active_tab_name=='Evaluation'?'active':'')?>" data-toggle="tab" style="color:#000 !important;text-align:center;">
                <img src="{{asset('images/grad.png')}}" width="40px"/><br />
                Evaluation
            </a>
        </li>
    </ul>
</div>


-->


</div>
