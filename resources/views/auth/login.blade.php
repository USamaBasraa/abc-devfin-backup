@extends('auth.layout')
@section('content')


    {{-- old form start --}}
    {{-- <div class="row">
	<div class="col mx-auto">
		<div class="row justify-content-center">
			<div class="col-md-7 col-lg-5">
				<div class="card card-group mb-0" style="box-shadow:none !important;">
					<div class="card p-4" style="box-shadow:none !important;">
						<div class="card-header row">
							<!-- <div class="row"> -->
							<div class="col-3">
								<div class="media-user mr-1">
									<div class="">
										<img src="<?= asset('/images/bg/bnu_logo.png') ?>" alt="Logo" style="no-repeat center center fixed; background-size: 100% 100%; min-height: 100vh;">
									</div>
								</div>
							</div>
							<div class="col-9">
								<h1 class="mb-2">Login</h1>
							</div>
						</div>
						<div class="card-body" style="box-shadow:none !important; padding: 2.5rem 1.5rem">
							<!-- <div class="text-center title-style mb-6">Logo</div> -->
							<!-- <div class="d-sm-flex" style="justify-content: center;">


								<a href="<?= url('google/auth/redirect') ?>" class="btn btn-social " style="border:2px; border-style: groove; border-color: white; ">
									<img src="/assets/images/bg/google_icon.png" width="17px" height="17px">
									Login with BNU Email</a>

							</div> -->

							<hr class="divider my-6" style="display:none;">


							<form id="login-form" method="POST" class="md-float-material" onsubmit="login_btn_click(this); return false;" action="#">
								@csrf


								@if ($errors->any())
								<div class="alert alert-danger">
									<ul>
										@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
										@endforeach
									</ul>
								</div>
								@endif


								@if (session()->has('success'))
								<div class="alert alert-success">
									@if (is_array(session('success')))
									<ul>
										@foreach (session('success') as $message)
										<li>{{ $message }}</li>
										@endforeach
									</ul>
									@else
									{{ session('success') }}
									@endif
								</div>
								@endif


								<div class="input-group mb-3">
									<span class="input-group-addon" style="padding: .3rem .75rem;"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
											<path d="M0 0h24v24H0V0z" fill="none" />
											<path d="M12 16c-2.69 0-5.77 1.28-6 2h12c-.2-.71-3.3-2-6-2z" opacity=".3" />
											<circle cx="12" cy="8" opacity=".3" r="2" />
											<path d="M12 14c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm-6 4c.22-.72 3.31-2 6-2 2.7 0 5.8 1.29 6 2H6zm6-6c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z" />
										</svg></span>
									<input placeholder="Login ID" id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="email" autofocus>
								</div>
								<div class="input-group mb-41">
									<span class="input-group-addon" style="padding: .3rem .75rem;"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
											<g fill="none">
												<path d="M0 0h24v24H0V0z" />
												<path d="M0 0h24v24H0V0z" opacity=".87" />
											</g>
											<path d="M6 20h12V10H6v10zm6-7c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z" opacity=".3" />
											<path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z" />
										</svg></span>
									<input id="password" type="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password">

                                </div>
                               <div class="input-group mb-4">
								<a href="https://secure.bnu.edu.pk/" ><small>Reset Password.</small></a>
								</div>

								<div class="row">


									<div class="col-12" id="notifications"></div>


									<div class="col-12">
										<button type="submit" id="login-btn" class="btn btn-lg btn-primary btn-block"><i class="fe fe-arrow-right"></i> Login</button>
									</div>
									<div class="col-12">
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="text-center pt-4">
					<!-- <div class="font-weight-normal fs-16">You Don't have an account <a class="btn-link font-weight-normal" href="#">Register Here</a></div> -->
				</div>
			</div>
		</div>
		<!-------->
	</div>
</div> --}}
    {{-- old form end --}}


    {{-- new form start --}}
    <div class="row justify-content-center align-items-center login-wrapper-4k mx-0">
        <div class="col-12 col-md-8 col-lg-5 col-xl-4 px-0 px-md-3">
            <div class="card shadow-lg w-100" style="border-radius: 20px; overflow: hidden; border: none;">
                <div class="row g-0">
                    <!-- Login Form -->
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="card-body p-4 p-md-5 w-100" style="background: white;">
                            <!-- Logo -->
                            <div class="text-center mb-4">
                                <img src="{{ asset('images/bg/bnu_logo.png') }}" alt="BNU Logo"
                                    style="max-width: 80px; margin-bottom: 20px;">
                            </div>

                            <!-- Welcome Text -->
                            <h3 class="text-center mb-2" style="font-weight: bold; color: #252525; font-size: 20px;">Welcome
                                Back</h3>
                            <p class="text-center mb-4" style="color: #777777; font-size: 14px;">Enter your
                                username & password
                                to access your account</p>

                            <!-- Form -->
                            <form id="new-login-form" method="POST" action="#">
                                @csrf

                                @if ($errors->any())
                                    <div class="alert alert-danger mb-3">
                                        <ul class="mb-0" style="font-size: 13px;">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (session()->has('success'))
                                    <div class="alert alert-success mb-3">
                                        @if (is_array(session('success')))
                                            <ul class="mb-0" style="font-size: 13px;">
                                                @foreach (session('success') as $message)
                                                    <li>{{ $message }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span style="font-size: 13px;">{{ session('success') }}</span>
                                        @endif
                                    </div>
                                @endif

                                <!-- Username Field -->
                                <div class="mb-3">
                                    <label for="new-username" class="form-label"
                                        style="font-size: 14px; font-weight: 500; color: #252525; margin-bottom: 5px;">Username</label>
                                    <input type="text" class="form-control" id="new-username" name="username"
                                        value="{{ old('username') }}" placeholder="Enter your username"
                                        style="padding: 12px 15px; border-radius: 50px; font-size: 14px; height: 40px;"
                                        required autofocus>
                                </div>

                                <!-- Password Field -->
                                <div class="mb-3">
                                    <label for="new-password" class="form-label"
                                        style="font-size: 14px; font-weight: 500; color: #252525; margin-bottom: 5px;">Password</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control" id="new-password" name="password"
                                            placeholder="Enter your password"
                                            style="padding: 12px 45px 12px 15px; border-radius: 50px; font-size: 14px; height: 40px;"
                                            required>
                                        <button type="button" class="btn btn-link position-absolute"
                                            onclick="togglePassword()"
                                            style="right: 5px; top: 50%; transform: translateY(-50%); padding: 5px; border: none; background: none; color: var(--color-text-light);">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" id="eye-icon">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Remember Me & Forgot Password -->
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check remember-me-inline">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember-me"
                                            style="cursor: pointer;height:12px;">
                                        <label class="form-check-label" for="remember-me"
                                            style="font-size: 12px; color: #252525; cursor: pointer;font-weight: 500;">Remember
                                            me</label>
                                    </div>
                                    <a href="https://secure.bnu.edu.pk/" class="forgot-password-link"
                                        style="font-size: 12px; color:#17675F !important; text-decoration: underline;">Forgot
                                        Password?</a>
                                </div>

                                <!-- Error Notifications -->
                                <div class="row mb-3">
                                    <div class="col-12" id="new-notifications"></div>
                                </div>

                                <!-- Login Button -->
                                <button type="submit" id="new-login-btn" class="btn btn-block w-100"
                                    style="background-color: #17675F !important; background:#17675F; color: #ffffff; padding: 12px; border-radius: 50px; font-size: 16px; font-weight: 500; border: none; transition: all 0.3s;">
                                    Login
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Override Roboto font with Montserrat for login page */
        body#background {
            font-family: Montserrat !important;
        }

        #new-login-form,
        #new-login-form *,
        .card-body,
        .card-body * {
            font-family: Montserrat !important;
        }

        #new-login-form .form-control {
            border-color: #ced4da;
        }

        #new-login-form .form-control:focus,
        #new-login-form .form-control:focus-visible,
        #new-login-form .form-control:active {
            border-color: #ced4da !important;
            box-shadow: none !important;
            outline: none !important;
        }

        #new-username:focus,
        #new-password:focus {
            border-color: #ced4da !important;
            box-shadow: none !important;
            outline: none !important;
        }

        #new-login-btn {
            background-color: #CD9A8F !important;
            color: #ffffff !important;
        }

        #new-login-btn:hover {
            background-color: #B8877A !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        #new-login-btn:active {
            transform: translateY(0);
        }

        .forgot-password-link {
            color: #CD9A8F !important;
            text-decoration: underline !important;
            transition: color 0.3s ease;
        }

        .forgot-password-link:hover {
            color: #B8877A !important;
            text-decoration: underline !important;
        }

        .remember-me-inline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .remember-me-inline .form-check-input {
            margin-top: 0;
        }

        /* Base responsive typography for login card (scale with viewport) */
        .login-wrapper-4k h3 {
            /* bigger heading on large screens, still readable on small */
            font-size: clamp(1.6rem, 2vw, 2.6rem);
        }

        .login-wrapper-4k p,
        .login-wrapper-4k label,
        .login-wrapper-4k .form-check-label,
        .login-wrapper-4k a {
            /* body text scales with viewport width */
            font-size: clamp(0.95rem, 1.05vw, 1.2rem);
        }

        .login-wrapper-4k input.form-control {
            font-size: clamp(0.95rem, 1.05vw, 1.15rem);
        }

        .login-wrapper-4k #new-login-btn {
            font-size: clamp(1rem, 1.1vw, 1.25rem);
        }

        /* Force login wrapper to fill full viewport */
        .login-wrapper-4k {
            min-height: 100vh;
            margin: 0;
        }

        .login-wrapper-4k>.col-12 {
            width: 100%;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .login-wrapper-4k .card.shadow-lg {
            border-radius: 20px;
            max-width: 100%;
        }

        .login-wrapper-4k .card.shadow-lg>.row.g-0 {
            height: 100%;
        }

        /* Laptop and larger screens (>= 1366px) */
        @media (min-width: 1366px) {
            .login-wrapper-4k {
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
            }

            .login-wrapper-4k .card.shadow-lg>.row.g-0 {
                height: 100%;
            }
        }

        /* Very large screens (>= 1650px) */
        @media (min-width: 1650px) {
            .login-wrapper-4k>.col-12 {
                max-width: 500px;
            }
        }
    </style>

    {{-- new form end --}}


    {{-- updated form start --}}
    {{-- updated form end --}}


    <form method="post" action="" id="kkk">

    </form>



@endsection

@section('javascript')
    <script type="text/javascript">
        var isSubmitting = false;

        $(document).ready(function() {
                       let savedUser = localStorage.getItem('rememberUser');
               let savedPass = localStorage.getItem('rememberPass');
               let rememberMeChecked = localStorage.getItem('rememberMe');

  if (savedUser && savedPass && rememberMeChecked) {
    $('#new-username').val(savedUser);
    $('#new-password').val(savedPass);
    $('#remember-me').prop('checked', true);
}

            $('#login-form').on('submit', function(e) {
                e.preventDefault();
                if (!isSubmitting) {
                    isSubmitting = true;
                    login_btn_click(this);
                }
                return false;
            });
        });

        function login_btn_click(ele) {
            if (isSubmitting) {
                $('#login-btn').attr('disabled', 'disabled').css('opacity', '0.5');
                $('.loading-icon').show();
                $('#notifications').html('');
                var msg_div =
                    '<div class="col-md-12 alert alert-danger border-danger" style="text-align: left; position: relative; padding-right: 40px;"  ><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="position: absolute; right: 10px; top: 10px; opacity: 1; color: var(--color-danger-text); font-size: 20px; font-weight: bold; background: none; border: none; cursor: pointer; line-height: 1;">&times;</button>'
                var username = $('#email').val();
                var password = $('#password').val();

                if (username.trim() == '') {
                    msg_div += 'Login ID is required !';
                    msg_div += '</div>';
                    $('#notifications').html(msg_div);
                    enableLoginButton();
                    return false;
                } else if (password.trim() == '') {
                    msg_div += 'Password is required !';
                    msg_div += '</div>';
                    $('#notifications').html(msg_div);
                    enableLoginButton();
                    return false;
                }

                var form_data = $('#login-form').serialize();
                $.ajax({
                    method: "POST",
                    url: "{{ url('/do-login') }}",
                    data: form_data,
                    beforeSend: function() {
                        $('#login-btn').attr('disabled', 'disabled').css('opacity', '0.5');
                    }
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
                        if (res.action == 'change-password') {
                            setTimeout(function() {
                                var url = "https://secure.bnu.edu.pk/";
                                var win = window.open(url);
                                return false
                            }, 3000);
                        }
                        msg_div += res.message;
                        msg_div += '</div>';
                        $('#notifications').html(msg_div);
                        enableLoginButton();
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
                    enableLoginButton();
                });
            }
        }

        function enableLoginButton() {
            isSubmitting = false;
            $('#login-btn').removeAttr('disabled').css('opacity', '1');
        }

        function confirmLogin(data) {
            var html = '';
            console.log(data);
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
            }, 300);
            return false;
        }

        // New Form Functions
        function togglePassword() {
            var passwordField = document.getElementById('new-password');
            var eyeIcon = document.getElementById('eye-icon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML =
                    '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML =
                    '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }

        $(document).ready(function() {
            $('#new-login-form').on('submit', function(e) {
                e.preventDefault();
                if (!isSubmitting) {
                    isSubmitting = true;
                    newLoginSubmit(this);
                }
                return false;
            });
        });

        function newLoginSubmit(ele) {
            if (isSubmitting) {
                $('#new-login-btn').attr('disabled', 'disabled').css('opacity', '0.5');
                $('.loading-icon').show();
                $('#new-notifications').html('');
                var msg_div =
                    '<div class="col-md-12 alert alert-danger border-danger" style="text-align: left; position: relative; padding-right: 40px;"  ><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="position: absolute; right: 10px; top: 10px; opacity: 1; color: var(--color-danger-text); font-size: 20px; font-weight: bold; background: none; border: none; cursor: pointer; line-height: 1;">&times;</button>'
                var username = $('#new-username').val();
                var password = $('#new-password').val();

                if (username.trim() == '') {
                    msg_div += 'Login ID is required !';
                    msg_div += '</div>';
                    $('#new-notifications').html(msg_div);
                    enableNewLoginButton();
                    return false;
                } else if (password.trim() == '') {
                    msg_div += 'Password is required !';
                    msg_div += '</div>';
                    $('#new-notifications').html(msg_div);
                    enableNewLoginButton();
                    return false;
                }

                var form_data = $('#new-login-form').serialize();
                $.ajax({
                    method: "POST",
                    url: "{{ url('/do-login') }}",
                    data: form_data,
                    beforeSend: function() {
                        $('#new-login-btn').attr('disabled', 'disabled').css('opacity', '0.5');
                    }
                }).done(function(res) {
                    $('.loading-icon').hide();
                    if (res.status) {
                           let remember = $('#remember-me').is(':checked');
        let username = $('#new-username').val();
        let password = $('#new-password').val(); 

           if (remember) {
            localStorage.setItem('rememberUser', username);
            localStorage.setItem('rememberPass', password);
            localStorage.setItem('rememberMe', 'true');
        }

                        if (res.redirect) {
                            $('.loading-icon').hide();
                            window.location = res.redirect;
                        } else {
                            confirmLogin(res.confirmLogin);
                        }
                    } else {
                        if (res.action == 'change-password') {
                            setTimeout(function() {
                                var url = "https://secure.bnu.edu.pk/";
                                var win = window.open(url);
                                return false
                            }, 3000);
                        }
                        msg_div += (res.message !== undefined && res.message !== null) ? res.message :
                            'Invalid username or password';
                        msg_div += '</div>';
                        $('#new-notifications').html(msg_div);
                        enableNewLoginButton();
                    }
                }).fail(function(res) {
                    console.log(res.statusText);
                    $('.loading-icon').hide();
                    if (res.status == 419) {
                        msg_div += (res.responseJSON && res.responseJSON.message) ? res.responseJSON.message :
                            'Session expired. Please refresh the page.';
                        msg_div += '</div>';
                        $('#new-notifications').html(msg_div);
                        window.location = "{{ url('/') }}";
                    } else {
                        // Check if response has JSON with message, otherwise use statusText
                        var errorMsg = (res.responseJSON && res.responseJSON.message) ? res.responseJSON.message : (
                            res.statusText || 'An error occurred. Please try again.');
                        msg_div += errorMsg;
                        msg_div += '</div>';
                        $('#new-notifications').html(msg_div);
                    }
                    enableNewLoginButton();
                });
            }
        }

        function enableNewLoginButton() {
            isSubmitting = false;
            $('#new-login-btn').removeAttr('disabled').css('opacity', '1');
        }
    </script>
@stop
