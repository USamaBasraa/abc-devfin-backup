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
@extends('layouts.' . $file_name, ['pageTitle' => 'Notification Detail'])  
@section('content')

{{-- @dd($notification) --}}
<div class="page-body">


  <div class="container-fluid">
    <div class="row">
      <div class="col-12 col-sm-6">
      {{-- notification detail screen start here --}}

<style>
.notification-detail-wrapper{
    padding:20px;
    background:#f5f7fb;
    border-radius:18px;
}

.notification-detail-card{
    background:#fff;
    border-radius:18px;
    padding:25px;
    box-shadow:0 4px 18px rgba(0,0,0,0.06);
    border:none;
}

.notification-detail-top{
    display:flex;
    align-items:center;
    margin-bottom:25px;
}

.notification-detail-icon{
    width:65px;
    height:65px;
    border-radius:50%;
    /* background:#EAF6F4; */
    display:flex;
    align-items:center;
    justify-content:center;
    margin-right:18px;
    flex-shrink:0;
}

.notification-detail-icon img{
    width:30px;
    height:30px;
}

.notification-detail-heading{
    font-size:26px;
    font-weight:700;
    color:#24324B;
    margin-bottom:4px;
}

.notification-detail-time{
    color:#94A3B8;
    font-size:14px;
}

.notification-detail-body{
    background:#fcfcfc;
    border:1px solid #edf1f5;
    border-radius:14px;
    padding:22px;
    margin-top:10px;
}

.notification-label{
    font-size:13px;
    color:#94A3B8;
    font-weight:600;
    margin-bottom:6px;
    text-transform:uppercase;
    letter-spacing:0.5px;
}

.notification-message{
    font-size:20px;
    font-weight:600;
    color:#334155;
    line-height:1.7;
    margin-bottom:0;
}

.notification-footer{
    margin-top:30px;
    display:flex;
    justify-content:flex-end;
}

.theme-btn{
    background:#17675F !important;
    border:none !important;
    border-radius:30px !important;
    color:#fff !important;
    font-size:14px !important;
    font-weight:600 !important;
    padding:10px 24px !important;
    transition:all 0.3s ease;
    box-shadow:0 3px 10px rgba(23,103,95,0.18);
}

.theme-btn:hover{
    color:#fff !important;
    background:#14564f !important;
    transform:translateY(-1px);
    text-decoration:none;
}

@media(max-width:768px){

    .notification-detail-top{
        flex-direction:column;
        text-align:center;
    }

    .notification-detail-icon{
        margin-right:0;
        margin-bottom:15px;
    }

    .notification-detail-heading{
        font-size:22px;
    }

    .notification-message{
        font-size:17px;
    }

    .notification-footer{
        justify-content:center;
    }
}
.side-app {
  margin-bottom: 0px !important;
}
</style>


<div class="notification-detail-wrapper">

    <div class="notification-detail-card">

        <div class="notification-detail-top">

            <div class="notification-detail-icon">
                <img src="{{asset('/staffimages/do.png')}}" alt="">
            </div>

            <div>

                <div class="notification-detail-heading">
                    Notification Detail
                </div>

                <div class="notification-detail-time">
                  {{ isset($notification->displayDate) ? $notification->displayDate : '' }}
                </div>

            </div>

        </div>


        <div class="notification-detail-body">

            <div class="notification-label">
                Message
            </div>

            <p class="notification-message">
              {{ isset($notification->un_description) ? $notification->un_description : '' }}
            </p>

        </div>


        <div class="notification-footer">

            <a href="{{ route('home') }}" class="btn btn btn-info btn-mini">
                Back
            </a>

        </div>

    </div>

</div>

{{-- notification detail screen start end --}}
    </div>
  </div>
  </div>
  </div>

@endsection