
{{-- @dd([
    'profile' => $profile,
    'auth_user' => auth()->user()
]) --}}
<style>
    .profile-overview-section {
        margin-bottom: 20px;
    }

    .profile-overview-title {
        margin: 0 0 14px; 
        font-size: 24px;
        font-weight: 700; 
        color: #2d2d2d;
        line-height: 1.2;
    }

    .employee-profile-card {
        background: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 18px;
        margin-bottom:0 !important;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    .employee-profile-wrapper {
        display: flex;
        align-items: stretch;
        gap: 24px;
    }

    .employee-profile-image {
        flex: 0 0 230px;
    }
/* 
    .employee-profile-image img {
        width: 230px;
        height: 245px;
        object-fit: cover;
        object-position: center;
        display: block;
        border-radius: 7px;
        border: 1px solid #ececec;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    } */

    .employee-profile-image {
    flex: 0 0 230px;
    display: flex;
}

.employee-profile-image img {
    width: 230px;
    height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
    border-radius: 7px;
    border: 1px solid #ececec;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

    .employee-profile-content {
        flex: 1;
        min-width: 0;
    }

    .employee-name {
        margin: 0 0 22px;
        font-size: 20px;
        font-weight: 700;
        color: #292929;
        line-height: 1.2;
    }

    .employee-details-grid {
        /* display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        column-gap: 34px;
        row-gap: 16px; */
           display: grid;
    grid-template-columns: 1fr;
    row-gap: 16px;
    }

    .employee-detail-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        min-width: 0;
    }

    .employee-detail-item.full-width {
        /* grid-column: 1 / -1; */
            grid-column: auto;
    }

    .employee-detail-icon {
        width: 30px;
        height: 30px;
        min-width: 30px;
        border-radius: 50%;
        background: #eef7f6;
        color: #268f88;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        margin-top: 1px;
    }

    .employee-detail-content {
        flex: 1;
        min-width: 0;
    }

    .employee-detail-label {
        display: block;
        margin-bottom: 2px;
        color: #777777;
        font-size: 13px;
        line-height: 1.2;
    }

    .employee-detail-value {
        color: #292929;
        font-size: 16px;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .employee-contact {
        cursor: pointer;
    }

    @media (max-width: 991px) {
        .employee-profile-wrapper {
            align-items: flex-start;
        }

        .employee-details-grid {
            grid-template-columns: 1fr;
        }

        .employee-detail-item.full-width {
            grid-column: auto;
        }
    }

    @media (max-width: 767px) {
        .employee-profile-wrapper {
            flex-direction: column;
            align-items: stretch;
        }

        .employee-profile-image {
            flex: none;
            text-align: center;
        }

        .employee-profile-image img {
            width: 100%;
            max-width: 230px;
            height: 245px;
            margin: 0 auto;
        }

        .employee-name {
            margin-top: 4px;
            text-align: center;
        }
    }
    .employee-detail-icon {
    width: 40px;
    height: 40px;
    min-width: 36px;
    border-radius: 50%;
    background: #eef7f6;
    color: #268f88;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 1px;
}

.employee-detail-icon i {
    font-size: 16px;   /* Change to 18px if you want larger icons */
}
.notification-wrapper > .d-flex {
    flex: 0 0 auto;
    margin-bottom: 14px !important;
}

.notification-wrapper > .d-flex .section-title {
    margin-bottom: 0 !important;
}
h3 {
    font-family: 'Montserrat', sans-serif !important;
}
</style>

<div class="profile-overview-section" style="margin-bottom: 0px;">

    <h4 class="profile-overview-title">
        Profile Overview
    </h4>

    <div class="card employee-profile-card">
        <div class="employee-profile-wrapper"> 

            <div class="employee-profile-image">
                {{-- <img
                    src="{{ asset('/staffimages/' . auth()->user()->employee_id . '.jpg') }}"
                    alt="{{ $profile['full_name'] }}"
                > --}}

                {{-- ********************************************************************** --}}
             <img
    src="{{ asset('staffimages/' . auth()->user()->employee_id . '.jpg') }}"
    alt="{{ $profile['full_name'] }}"
    onerror="this.onerror=null;this.src='{{ asset('/images/male.png') }}';">
                {{-- ********************************************************************** --}}
            </div>

            <div class="employee-profile-content">

                <h3 class="employee-name">
                    {{ $profile['full_name'] }}
                </h3>

                <div class="employee-details-grid">

                    {{-- Designation --}}
                    <div class="employee-detail-item">
                        <div class="employee-detail-icon">
                                   <img src="{{asset('images/pp.png')}}" width="22">
                        </div>

                        <div class="employee-detail-content">
                            <span class="employee-detail-label">
                                Designation
                            </span>

                            <div class="employee-detail-value">
                                {{ auth()->user()->designation }}
                            </div>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="employee-detail-item">
                        <div class="employee-detail-icon">
                            <img src="{{asset('images/ee.png')}}" width="22">
                        </div>

                        <div class="employee-detail-content">
                            <span class="employee-detail-label">
                                Email
                            </span>

                            <div class="employee-detail-value">
                                {{ $profile['email'] }}
                            </div>
                        </div>
                    </div>

                    {{-- Department --}}
                    <div class="employee-detail-item">
                        <div class="employee-detail-icon">
                            <img src="{{asset('images/dd.png')}}" width="22">
                        </div>

                        <div class="employee-detail-content">
                            <span class="employee-detail-label">
                                Department
                            </span>

                            <div class="employee-detail-value">
                                {{ $profile['department'] }}
                            </div>
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div
                        class="employee-detail-item employee-contact"
                        onclick="ShowAndHide()"
                    >
                        <div class="employee-detail-icon">
                           <img src="{{asset('images/cc.png')}}" width="22">
                        </div>

                        <div class="employee-detail-content">
                            <span class="employee-detail-label">
                                Contact
                            </span>

                            <div class="employee-detail-value">
                                <span id="ContactPlaceholder">
                                    **************
                                </span>

                                <span id="SectionName" style="display: none;">
                                    {{ $profile['mobile'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- School --}}
                    <div class="employee-detail-item full-width">
                        <div class="employee-detail-icon">
                          <img src="{{asset('images/ss.png')}}" width="22">
                        </div>

                        <div class="employee-detail-content">
                            <span class="employee-detail-label">
                                School
                            </span>

                            <div class="employee-detail-value">
                                {{ $profile['school'] }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>


<script>
    function ShowAndHide() {
        var number = document.getElementById('SectionName');
        var placeholder = document.getElementById('ContactPlaceholder');

        if (number.style.display === 'none') {
            number.style.display = 'inline';
            placeholder.style.display = 'none';
        } else {
            number.style.display = 'none';
            placeholder.style.display = 'inline';
        }
    }
</script>


<h1 hidden id="em_email">
    {{$profile['email'] }}
</h1>

<h1 hidden id="em_token">
    {{auth()->user()->api_token}}
</h1>

{{-- {{$profile['email'] }} --}}


<script type="text/javascript">
    (function() {
        var loginRemember = {
            rememberUser: localStorage.getItem('rememberUser')
            , rememberPass: localStorage.getItem('rememberPass')
            , rememberMe: localStorage.getItem('rememberMe')
        };
        localStorage.clear();
        if (loginRemember.rememberUser !== null) {
            localStorage.setItem('rememberUser', loginRemember.rememberUser);
        }
        if (loginRemember.rememberPass !== null) {
            localStorage.setItem('rememberPass', loginRemember.rememberPass);
        }
        if (loginRemember.rememberMe !== null) {
            localStorage.setItem('rememberMe', loginRemember.rememberMe);
        }
        var email = document.getElementById('em_email').innerText;
        var em_token = document.getElementById('em_token').innerText;
        localStorage.setItem('emp_email', email);
        localStorage.setItem('em_token', em_token);
    })();

</script>
