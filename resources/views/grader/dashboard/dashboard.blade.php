@extends('template')

@section('content')
<div class="container-fluid">
      <div class="yoo-uikits-heading">
        <h2 class="yoo-uikits-title">Dashboard</h2> | @include('grader.dashboard.welcome_modal')
      </div>
    </div>
    <div class="container-fluid">
      <div class="row">
      <div class="col-lg-6">
          <div class="yoo-card yoo-style1">
            <div class="yoo-card-heading">
              <div class="yoo-card-heading-left">
                <h2 class="yoo-card-title"><span class="yoo-card-title-icon yoo-purple-bg"><ion-icon name="flask" role="img" class="md hydrated" aria-label="flask"></ion-icon></span>User Information</h2>
              </div>
            </div>
            <div class="yoo-card-body">
              <div class="yoo-padd-lr-20">
                <div class="yoo-height-b20 yoo-height-lg-b20"></div>
                <ul class="yoo-list-group yoo-style1 yoo-type1 yoo-mp0">
                  <li>
                    <div class="yoo-medias yoo-style1 yoo-type1">
                      <div class="yoo-media-img">
                        <div class="yoo-box-md yoo-radious10  yoo-purple-box"><img src="assets/img/profile-pic.png" width="100px;"></div>
                      </div>
                      <div class="yoo-media-meta">
                        <div class="yoo-height-b5 yoo-height-lg-b5"></div>
                        <h2 class="yoo-media-title yoo-margin-bottom-0 yoo-margin-top-5"><?=getLoginUser('full_name')?></h2>
                        <h2 class="yoo-media-title yoo-margin-bottom-0 yoo-margin-top-10">Faculty</h2>
                        <h2 class="yoo-media-title yoo-margin-bottom-0 yoo-margin-top-10"><?=getLoginUser('school')?></h2>
                        <h2 class="yoo-media-title yoo-margin-bottom-0 yoo-margin-top-10"> <?=getLoginUser('department')?></h2>
                        <h2 class="yoo-media-title yoo-margin-bottom-0 yoo-margin-top-10"> <?=getLoginUser('personal')?></h2>
                        <h2 class="yoo-media-title yoo-margin-bottom-0 yoo-margin-top-10"><?=getLoginUser('mobile')?></h2>
                        <div class="yoo-media-meta">
                        <div class="yoo-height-b5 yoo-height-lg-b5"></div>
                    
              
                      </div>    
                
                   
                   
                      </div>
                
                    </div>
                  </li>
                
               
                 
            
                
                </ul>
                <div class="yoo-height-b20 yoo-height-lg-b20"></div>
              </div>
            </div>
           
          </div>
          
          <!-- .yoo-card -->
          <div class="yoo-height-b30 yoo-height-lg-b30"></div>
        </div>
        <div class="col-lg-6">
          <div class="yoo-card yoo-style1">
            <div class="yoo-card-heading">
              <div class="yoo-card-heading-left">
                <h2 class="yoo-card-title"><span class="yoo-card-title-icon yoo-purple-bg"><ion-icon name="flask" role="img" class="md hydrated" aria-label="flask"></ion-icon></span>Notification</h2>
              </div>
            </div>
            <div class="yoo-card-body">
              <div class="yoo-padd-lr-20">
                <div class="yoo-height-b20 yoo-height-lg-b20"></div>
                <ul class="yoo-list-group yoo-style1 yoo-type1 yoo-mp0">
                  <li>
                    <div class="yoo-medias yoo-style1 yoo-type1">
                      <div class="yoo-media-img">
                        <div class="yoo-box-md yoo-radious10 yoo-img-box yoo-purple-box"><img src="assets/img/user/9.png" alt=""></div>
                      </div>
                      <div class="yoo-media-meta">
                        <div class="yoo-height-b5 yoo-height-lg-b5"></div>
                        <h2 class="yoo-media-title yoo-margin-bottom-0 yoo-margin-top-2">Assignment Submitted</h2>
                        <div class="yoo-media-subtitle">F2020-422 </div>
                      </div>
                      <span class="badge badge-success">Student</span>
                    </div>
                  </li>
                  <li>
                    <div class="yoo-medias yoo-style1 yoo-type1">
                     
                      <div class="yoo-media-meta">
                        <div class="yoo-height-b5 yoo-height-lg-b5"></div>
                        <h2 class="yoo-media-title yoo-margin-bottom-0 yoo-margin-top-15 "></h2>
                        <div class="yoo-media-subtitle"></div>
                      </div>
                      <span class="badge badge-success"></span>
                    </div>
                  </li>
                  <div class="yoo-media-meta">
                        <div class="yoo-height-b5 yoo-height-lg-b5"></div>
                    
              
                      </div>  
                      <div class="yoo-media-meta">
                        <div class="yoo-height-b5 yoo-height-lg-b5"></div>
                    
              
                      </div>  
                      <div class="yoo-media-meta">
                        <div class="yoo-height-b5 yoo-height-lg-b5"></div>
                    
              
                      </div>  
                      <div class="yoo-media-meta">
                        <div class="yoo-height-b5 yoo-height-lg-b5"></div>
                    
              
                      </div>  
                      <div class="yoo-media-meta">
                        <div class="yoo-height-b5 yoo-height-lg-b5"></div>
                    
              
                      </div>  
         
               
             
              
                </ul>
                <div class="yoo-height-b20 yoo-height-lg-b20"></div>
              </div>
            </div>
           
          </div>
          
          <!-- .yoo-card -->
          <div class="yoo-height-b30 yoo-height-lg-b30"></div>
        </div>
        <div class="col-xl-3 col-sm-6">
          <div class="yoo-iconbox yoo-style1 yoo-color1 yoo-blue-bg yoo-blue-shadow">
            <div class="yoo-iconbox-in">
              <div class="yoo-iconbox-title">Grading Status</div>
              <div class="yoo-iconbox-number">100%</div>
              <div class="yoo-iconbox-footer"><span class="yoo-iconbox-percentage"><ion-icon name="caret-up"></ion-icon>17%</span>Since last week</div>
              <div class="yoo-iconbox-icon"><ion-icon name="albums"></ion-icon></div>
            </div>
          </div>
          <div class="yoo-height-b30 yoo-height-lg-b30"></div>
        </div><!-- .col -->
        <div class="col-xl-3 col-sm-6">
          <div class="yoo-iconbox yoo-style1 yoo-color1 yoo-green-bg yoo-green-shadow">
            <div class="yoo-iconbox-in">
              <div class="yoo-iconbox-title">Courses</div>
              <div class="yoo-iconbox-number">3</div>
              <div class="yoo-iconbox-footer"><span class="yoo-iconbox-percentage"><ion-icon name="caret-up"></ion-icon>5%</span>Since last week</div>
              <div class="yoo-iconbox-icon"><ion-icon name="people"></ion-icon></div>
            </div>
          </div>
          <div class="yoo-height-b30 yoo-height-lg-b30"></div>
        </div><!-- .col -->
        <div class="col-xl-3 col-sm-6">
          <div class="yoo-iconbox yoo-style1 yoo-color1 yoo-gray-bg yoo-gray-shadow">
            <div class="yoo-iconbox-in">
              <div class="yoo-iconbox-title">Lecture Schedule</div>
              <div class="yoo-iconbox-number">100</div>
              <div class="yoo-iconbox-footer"><span class="yoo-iconbox-percentage"><ion-icon name="caret-down"></ion-icon>8%</span>Since last week</div>
              <div class="yoo-iconbox-icon"><ion-icon name="layers"></ion-icon></div>
            </div>
          </div>
          <div class="yoo-height-b30 yoo-height-lg-b30"></div>
        </div><!-- .col -->
        <div class="col-xl-3 col-sm-6">
          <div class="yoo-iconbox yoo-style1 yoo-color1 yoo-light-blue-bg yoo-light-blue-shadow">
            <div class="yoo-iconbox-in">
              <div class="yoo-iconbox-title">Assignments</div>
              <div class="yoo-iconbox-number">50</div>
              <div class="yoo-iconbox-footer"><span class="yoo-iconbox-percentage"><ion-icon name="caret-up"></ion-icon>12%</span>Since last week</div>
              <div class="yoo-iconbox-icon"><ion-icon name="file-tray"></ion-icon></div>
            </div>
          </div>
          <div class="yoo-height-b30 yoo-height-lg-b30"></div>
        </div><!-- .col -->
      </div>
      </div>
      
      
      <div class="row">
       <!-- .col -->
         <!-- .col -->
        <div class="col-lg-12">
          <div class="yoo-card yoo-style1">
            <div class="yoo-card-body">
              <div class="card">
                <div>
            
                  <div class="yoo-table yoo-style1 yoo-type2">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                      
                          <th colspan="6"> <p align="left">CSC-112 : Intro to Info. & Comm. Technologies 3 : A</p></th>
                    
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>
                          <span class="yoo-base-color1"> Course Criteria</span>
                          </td>
                          <td>
                          <span class="yoo-base-color1"><p align="left">Course Outline</p></span>
                          </td>
                          <td>
                          <span class="yoo-base-color1"><p align="left">Lecture Schedule</p></span>
                          </td>
                          <td>
                              <span class="yoo-base-color1"><p align="left">Grading</p></span>
                          </td>
                          <td>
                              <span class="yoo-base-color1">Faculty Notifications</span>
                          </td>
                          <td>
                          <span class="badge badge-success">Online Exam</span>
                          </td>
                        </tr>
                   
                      </tbody>
                      
                    </table>
                  </div>
                  <div class="yoo-table yoo-style1 yoo-type2">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                      
                          <th colspan="6"><p align="left">  PRJ-403 : Project Part I 3 : A</p></th>
                    
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td >
                          <span class="yoo-base-color1">Course Criteria</span>
                          </td>
                          <td>
                          <span class="yoo-base-color1">Course Outline</span>
                          </td>
                          <td>
                          <span class="yoo-base-color1">Lecture Schedule</span>
                          </td>
                          <td>
                              <span class="yoo-base-color1">Grading</span>
                          </td>
                          <td>
                              <span class="yoo-base-color1">Faculty Notifications</span>
                          </td>
                          <td>
                          <span class="badge badge-success">Online Exam</span>
                          </td>
                        </tr>
                   
                      </tbody>
                      
                    </table>
                  </div>
                  <div class="yoo-table yoo-style1 yoo-type2">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                      
                          <th colspan="6"><p align="left">  CSC-217 : Operating Systems 4 : A</p></th>
                    
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>
                          <span class="yoo-base-color1">Course Criteria</span>
                          </td>
                          <td>
                          <span class="yoo-base-color1">Course Outline</span>
                          </td>
                          <td>
                          <span class="yoo-base-color1">Lecture Schedule</span>
                          </td>
                          <td>
                              <span class="yoo-base-color1">Grading</span>
                          </td>
                          <td>
                              <span class="yoo-base-color1">Faculty Notifications</span>
                          </td>
                          <td>
                          <span class="badge badge-success">Online Exam</span>
                          </td>
                        </tr>
                   
                      </tbody>
                      
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div><!-- .yoo-card -->
          <div class="yoo-height-b30 yoo-height-lg-b30"></div>
        </div>
        <!-- .col -->
       <!-- .col -->
        <!-- .col -->
      </div>
    </div>
    @endsection