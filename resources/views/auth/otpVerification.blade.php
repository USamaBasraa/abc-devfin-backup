@extends('auth.layout')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('OTP Login') }}</div>
  
                <div class="card-body">
                 
                    @if(!empty($success))
                    <div class="alert alert-success" role="alert"> {{$success}} </div>
                    @endif

                    @if(!empty($error))
                    <div class="alert alert-danger" role="alert"> {{$error}} </div>
                    @endif

                    <div class="row">
						<div class="col-12" id="notifications"></div>
					</div>
                </br>
                <!-- action="{{ route('otp.getlogin') }}" -->
               
                    <form  id="otp-form" method="POST" class="md-float-material" onsubmit="otp_btn(this); return false;" action="#">
                    @csrf
                        <input type="hidden" name="user_id" value="{{$userID}}" />
                        <div class="row mb-3">
                            <label for="mobile_no" class="col-md-4 col-form-label text-md-end">{{ __(' Enter OTP') }}</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control @error('otp_code') is-invalid @enderror" name="otp_code" id="otp_code" value="{{ old('otp_code') }}"  autocomplete="otp" autofocus placeholder="Enter OTP">
                                <div style="font-weight:bold">Time Remaining = <span id="timer"></span></div>
                            </div>
                        </div>
                        <!-- <div class="offset-md-4 mb-3">
								<a onclick="otp_generate(this)" ><small>Resend OTP.</small></a>
						</div> -->
                        <div class="row ">
                            <div class="offset-md-5 col-md-6 ">
                                <button onclick="otp_btn(this)" type="button" class="btn btn-primary">
                                    {{ __('Submit') }}
                                </button>
                                <a type="button" href="/login" class="btn btn-secondary">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                         
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<form method="post" action="" id="kkk">
	
</form>
@endsection

@section('javascript')
<script type="text/javascript">
    let timerOn = true;

function timer(remaining) {
    //document.getElementById('timer').innerHTML ="02" + ":" + "00";
  var m = Math.floor(remaining / 60);
  var s = remaining % 60;
  //alert(remaining);
  m = m < 10 ? '0' + m : m;
  s = s < 10 ? '0' + s : s;
  document.getElementById('timer').innerHTML = m + ':' + s;
  remaining -= 1;
  
  if(remaining >= 0 && timerOn) {
    setTimeout(function() {
        timer(remaining);
    }, 1000);
    return;
  }

  if(!timerOn) {
    // Do validate stuff here
    return;
  }else{
    window.location='/login';
     return false;
  }
  
}
$(document).ready(function() {
    timer(120);
			});

    var counter=0;
	var msg_div = '<div class="col-md-12 alert alert-success border-success" style="text-align: left;"  ><button type="button" class="close" data-dismiss="alert" aria-label="Close"><i class="fa fa-times-circle-o" style="color:#e74c3c"></i></button><strong>Error! &nbsp &nbsp</strong>'

	function otp_btn(ele) {

		$('.loading-icon').show();
		$('#notifications').html('');
		var msg_div = '<div class="col-md-12 alert alert-danger border-danger" style="text-align: left;"  ><button type="button" class="close" data-dismiss="alert" aria-label="Close"><i class="fa fa-times-circle-o" style="color:#e74c3c"></i></button><strong>Error! &nbsp &nbsp</strong>'
		var Otp = $('#otp_code').val();


		if (Otp.trim() == '') {
			msg_div += 'Otp is required !';
			msg_div += '</div>';
			$('#notifications').html(msg_div);
			return false;
		}
        counter=counter+1;
        if(counter >=3){
            window.location='/login';
            return false;
        }
		var form_data = $('#otp-form').serialize();
		$.ajax({
			method: "POST",
			url: "{{ url('/otp/login')}}",
			data: form_data
		}).done(function(res) {
			$('.loading-icon').hide();
			if (res.status) {
				
				if (res.redirect) {
					$('.loading-icon').hide();
					window.location = res.redirect; 
				} else {

					confirmLogin(res.confirmLogin);
				}
			} else {
					
				
				msg_div += res.message;
				msg_div += '</div>';
				$('#notifications').html(msg_div);
                setTimeout(function() {
                    $('#notifications').html('');
					}, 2000);
			}
		}).fail(function(res) {
			console.log(res.statusText);
			$('.loading-icon').hide();
			if (res.status == 419) {
				msg_div += res.responseJSON.message;
				msg_div += '</div>';
				$('#notifications').html(msg_div);
				window.location = "{{ url('/') }}";
			} else {
				msg_div += res.statusText;
				msg_div += '</div>';
				$('#notifications').html(msg_div);
			}
		});

    }
    function confirmLogin(data) {
		var html = '';
		//console.log(data);
		$(data).each(function(index, obj) {
			if (obj.key == 'redirect_url') {

				$('#kkk').attr('action', obj.value);
			} else {
				html += '<input type="hidden" name="' + obj.key + '"  value="' + obj.value + '" >';
			}
		});

		$('#kkk').html(html);

		setTimeout(() => {
			$('.loading-icon').hide();
			$('#kkk').submit();
		}, 3000);
		return false;

	}
</script>

@stop