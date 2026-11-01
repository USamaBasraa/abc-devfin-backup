<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();


?>

<style>

/* Pulse class and keyframe animation */
.pulseit{
	-webkit-animation: pulse linear .5s infinite;
	animation: pulse linear 1s infinite;
    margin-top: 6px;
    
}
@-webkit-keyframes pulse {
	0% { width:40px; } 
	50% { width:48px; } 
	100% { width:40px; }
}
@keyframes pulse {
	0% { width:40px; } 
	50% { width:48px; } 
	100% { width:40px; }
}


    </style>





<nav class="navbar header-navbar pcoded-header" header-theme="theme4" pcoded-header-position="fixed">
    <div class="navbar-wrapper fixed;">
        <div class="navbar-logo">
            <a class="mobile-menu" id="mobile-collapse" href="javascript:void(0)">
                <i class="ti-menu"></i>
            </a>
            <!-- search div is commented
                        <a class="mobile-search morphsearch-search" href="#">
                            <i class="ti-search"></i>
                        </a>-->
            <a href="{{url('/')}}">
                <img class="img-fluid" src="/images/bnuLogo.png" alt="BNU" width="22%" />
            </a>
            <a href="#!" class="mobile-options">
                <i class="ti-more"></i>
            </a>
    
        </div>
        <div class="navbar-container container-fluid">
            <div>
                <ul class="nav-left">
                    <li>
                        <div id="collapse" class="sidebar_toggle"><a><i class="ti-menu"></i></a></div>
                    </li>
                
                 <div class="nav-left">   
                     <a href="http://grader8.bnu.edu.pk/support/support.html">
                <img class="img-fluid pulseit " src="/images/help.png" alt="BNU" width="5%" />
</a>
                </div>
                </ul>
             
                <ul class="nav-right">
              
                <li class="header-notification">



                                    <a href="#!">
                                        <i class="ti-bell"></i>
                                        <span class="badge">3</span>
                                    </a>
                                    <ul class="show-notification">
                                        <li>
                                            <h6>Notifications</h6>
                                            <label class="label label-danger">New</label>
                                        </li>
                                        <li>
                                            <div class="media">
                                                <img class="d-flex align-self-center" src="assets/images/user.png" alt="Generic placeholder image">
                                                <div class="media-body">
                                                    <h5 class="notification-user">Ijaz Hussain</h5>
                                                    <p class="notification-msg"> 	S2020-012 Upload Assginment by student.</p>
                                                    <span class="notification-time">30 minutes ago</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="media">
                                                <img class="d-flex align-self-center" src="assets/images/user.png" alt="Generic placeholder image">
                                                <div class="media-body">
                                                    <h5 class="notification-user">Ijaz Hussain</h5>
                                                    <p class="notification-msg"> 	S2020-012 Upload Assginment by student.</p>
                                                    <span class="notification-time">30 minutes ago</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="media">
                                                <img class="d-flex align-self-center" src="assets/images/user.png" alt="Generic placeholder image">
                                                <div class="media-body">
                                                    <h5 class="notification-user">Ijaz Hussain</h5>
                                                    <p class="notification-msg">3.84 / 10 Updated by teacher in Assignment no 5.</p>
                                                    <span class="notification-time">30 minutes ago</span>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <li class="header-notification">
                                    <a onclick="getSessions()" class="displayChatbox">
                                        <i class="ti-comments"></i>
                                        {{-- <span class="badge">9</span> --}}
                                    </a>
                                </li>  

       <!-- Sidebar chat start -->
       <div id="sidebar" class="users p-chat-user showChat">
            <div class="had-container">
                <div class="card card_main p-fixed users-main">
                    <div class="user-box">
                        <div class="card-block">
                            <div class="right-icon-control">
                                {{-- <input type="text" class="form-control  search-text" placeholder="Search Friend" id="search-friends"> --}}
                                <div class="form-icon">
                                    {{-- <i class="icofont icofont-search"></i> --}}
                                </div>
                            </div>
                        </div>
                        <div class="main-friend-list">
                            <div class="media userlist-box" data-id="1" data-status="online" data-username="Josephin Doe" data-toggle="tooltip" data-placement="left" title="Josephin Doe">
                                <a class="media-left" href="#!">
                                    {{-- <img class="media-object img-circle" src="assets/images/avatar-1.png" alt="Generic placeholder image"> --}}
                                    <div class="live-status bg-success"></div>
                                </a>
                                <div class="media-body">
                                    <div class="f-13 chat-header" id="sessions"></div>
                                </div>
                            </div>                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sidebar inner chat start-->
                                <li class="user-profile header-notification">
              
                        <a href="#!">
                            <img  src="{{asset('staffimages/350.jpg')}}" class=" img-radius img-responsive media-object img-circle" style="width:40px; height:40px;" alt="">
                            <span>{{ getLoginUser('full_name') }}</span>
                            <i class="ti-angle-down"></i>
                        </a>
                        <ul class="show-notification profile-notification">
                            <li>
                                <a href="<?= url('/new_logout') ?>">
                                    <i class="ti-layout-sidebar-left"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                    </ul>             
            </div>
        </div>
    </div>
</nav>


<script type="text/javascript">
function getSessions(){
    $('#sessions').html('');
   
   // var selected_sess = '<?php echo getLoginUser('active_session_id') ?>';
   var selected_sess =  '<?php if(isset($sess->active_session_id)){echo $sess->active_session_id;}?>';

   
   $.ajax({
                url:'{{url('/get_top_sessions')}}',
                type: "GET",
                success: function (data) {
                    $.each(data,function(key,value){
                        console.log(value)
                   //     $('#sessions').append('<span>'+value.sess_name+'</span><br>')
                     
                        if(value.fc_co_sess_id==selected_sess)
                        {
                            $('#sessions').append('<ul  class="list-group"><li style="line-height:2 !important" class="list-group-item"><span><input type="radio" name="activeSessionCog" class="activeSessionCog" value="'+value.fc_co_sess_id+'-'+value.sess_name+'"  onclick="selectActiveSession(this.value)" checked/> '+value.sess_name+'<br>'+value.allocated_courses+'</span><span style="font-weight: normal;"> Courses</span></li></ul>')
                        }else{
                            $('#sessions').append('<ul class="list-group"><li style="line-height:2 !important"  class="list-group-item"><span><input type="radio" name="activeSessionCog" class="activeSessionCog" value="'+value.fc_co_sess_id+'-'+value.sess_name+'" onclick="selectActiveSession(this.value)" /> '+value.sess_name+'<br>'+value.allocated_courses+'</span><span style="font-weight: normal;"> Courses</span></li></ul>')
                        }
                    });
               
                }
            });
}
      

        function selectActiveSession(sel_sess)
        {
            // alert(sel_sess);
            // return false;
            $.ajax({
                type:'POST',
                url:'{{url('set_session')}}',
                data: {'_token':'{{csrf_token()}}'
                ,'sel_active_session':sel_sess},
                success:function(data){
                    if(data==1)
                    {
                        location.reload();
                    }
                }
            });
        }
      

    


</script>
