import React from "react";
import MainDashboard from "./MainDasboard";
import http from "../components/http";
import { useEffect, useState, useContext } from "react";
const Sidebar=()=>{
	const [emp_details, setemp_details] = useState([]);
	var emp_login_email = window.localStorage.getItem('emp_email').trim().toLowerCase();
	useEffect(() => {
        http.get("/get_current_login_employee?email="+emp_login_email)
            .then(res => {
				setemp_details(res.data[0]);
				window.localStorage.setItem('user_id', res.data[0].id);
            
            })
    }, []);
    return(<>


	<style>{`
    /* Figma Sidebar + Topbar CSS */
    .app.default-sidebar {
        --cms-primary: #17675f;
        --cms-primary-soft: rgba(23, 103, 95, 0.10);
        --cms-text: #252525;
        --cms-muted: #7b7b7b;
        --cms-sidebar-width: 293px;

        min-height: 100vh;
        color: var(--cms-text);
        font-family: "Montserrat", sans-serif;
       
    }

    .app.default-sidebar .page,
    .app.default-sidebar .page-main {
        min-height: 100vh;
        background: transparent;
    }

    /* Sidebar */
    .app.default-sidebar .app-sidebar2,
    .app.default-sidebar .app-sidebar3 {
        width: var(--cms-sidebar-width);
     
        border-right: 0;
        box-shadow: none;
    }

    .app.default-sidebar .app-sidebar2 {
        height: 104px;
        padding: 22px 30px 12px;
    }

    .app.default-sidebar .app-sidebar__logo {
        height: 70px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .app.default-sidebar .app-sidebar__logo .header-brand-img {
        max-width: 46px;
        max-height: 69px;
        object-fit: contain;
    }

    .app.default-sidebar .app-sidebar3 {
        top: 104px;
        padding: 14px 30px 28px;
    }

    /* Sidebar profile */
    .app.default-sidebar .app-sidebar__user {
        margin: 0 0 22px;
        padding: 14px;
        border: 0;
        border-radius: 12px;
    }

    .app.default-sidebar .app-sidebar__user .avatar-xl {
        width: 58px;
        height: 58px;
        object-fit: cover;
        border: 3px solid #fff;
    }

    .app.default-sidebar .app-sidebar__user h5 {
        margin-top: 7px;
        color: var(--cms-text);
        font-size: 14px;
        font-weight: 600;
    }

    .app.default-sidebar .app-sidebar__user-name {
        color: var(--cms-muted) !important;
        font-size: 11px;
    }

    /* Sidebar menu */
    .app.default-sidebar .side-menu {
        margin: 0;
        padding: 0;
    }

    .app.default-sidebar .side-menu .slide {
        margin-bottom: 5px;
    }

    .app.default-sidebar .side-menu__item {
        min-height: 40px;
        padding: 0 13px;
        display: flex;
        align-items: center;
        color: var(--cms-text) !important;
        border-radius: 20px;
        transition: background-color .2s ease, color .2s ease;
    }

    .app.default-sidebar .side-menu__item:hover,
    .app.default-sidebar .side-menu__item.active,
    .app.default-sidebar .slide.is-expanded > .side-menu__item {
        color: #fff !important;
    }

    .app.default-sidebar .side-menu__icon {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        color: currentColor;
        stroke: currentColor;
    }

    .app.default-sidebar .side-menu__label {
        font-size: 13px;
        font-weight: 400;
    }

    .app.default-sidebar .side-menu__item .angle {
        margin-left: auto;
        color: currentColor;
        font-size: 13px;
    }

    .app.default-sidebar .slide-menu {
        margin: 5px 0 8px;
        padding: 4px 0 4px 41px;
        border: 0;
    }

    .app.default-sidebar .slide-item {
        padding: 8px 10px;
        color: #5f6565;
        font-size: 12px;
        border-radius: 8px;
    }

    .app.default-sidebar .slide-item:hover {
        color: var(--cms-primary);
    }

    /* Main content */
    .app.default-sidebar .app-content.main-content {
        margin-left: var(--cms-sidebar-width);
    background:#f2f6f9 !important;
    }

 

    /* Topbar */
    .app.default-sidebar .app-header.top-header {
        left: var(--cms-sidebar-width);
        right: 0;
        top: 0;
        height: 104px;
        padding: 20px 30px;
        background: #f2f6f9;
        border: 0;
        box-shadow: none;
    }

    .app.default-sidebar .app-header .container-fluid,
    .app.default-sidebar .app-header .container-fluid > .d-flex {
        height: 64px;
        padding: 0;
        align-items: center;
    }

    .app.default-sidebar .app-header .header-brand {
        display: none;
    }

    /* Session dropdown */
    .app.default-sidebar .app-header .header-option {
	border: 1px solid #516172;
        min-width: 330px;
        height: 64px;
        margin-left: 0 !important;
        padding: 0 22px;
        display: flex;
        align-items: center;
        border-radius: 32px;
    }

    .app.default-sidebar .app-header .header-option > .nav-link {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: var(--cms-text);
    }

    .app.default-sidebar .app-header .header-option .header-icon {
        width: 20px;
        height: 20px;
        fill: var(--cms-primary);
    }

    /* Right-side topbar icons */
    .app.default-sidebar .app-header .order-lg-2 {
        height: 64px;
        gap: 8px;
        align-items: center;
    }

    .app.default-sidebar .app-header .order-lg-2 > .nav-link,
    .app.default-sidebar .app-header .header-fullscreen,
    .app.default-sidebar .app-header .profile-dropdown > .nav-link {
        width: 48px;
        height: 48px;
        padding: 0 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .app.default-sidebar .app-header .order-lg-2 > .nav-link img {
        max-width: 28px;
        max-height: 28px;
        object-fit: contain;
    }

    .app.default-sidebar .app-header .header-fullscreen .nav-link,
    .app.default-sidebar .app-header .header-fullscreen .header-icon {
        width: 20px;
        height: 20px;
        padding: 0;
        fill: var(--cms-primary);
    }

    .app.default-sidebar .app-header .profile-dropdown > .nav-link {
        width: 64px;
        height: 64px;
        overflow: hidden;
    }

    .app.default-sidebar .app-header .profile-dropdown .avatar {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border: 0;
    }

    .app.default-sidebar .dropdown-menu {
        margin-top: 10px;
        padding: 8px;
        border: 0;
        border-radius: 12px;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .app.default-sidebar {
            --cms-sidebar-width: 0px;
        }

        .app.default-sidebar .app-sidebar2,
        .app.default-sidebar .app-sidebar3 {
            width: 270px;
        }

        .app.default-sidebar .app-header.top-header {
            height: 84px;
            padding: 10px 16px;
        }

        .app.default-sidebar .side-app {
            padding-top: 96px;
        }

        .app.default-sidebar .app-header .header-option {
            min-width: 210px;
        }
    }

    @media (max-width: 575.98px) {
        .app.default-sidebar .app-header .header-option {
            min-width: 150px;
            height: 48px;
        }

        .app.default-sidebar .app-header .order-lg-2 {
            gap: 4px;
        }

        .app.default-sidebar .app-header .order-lg-2 > .nav-link,
        .app.default-sidebar .app-header .header-fullscreen {
            width: 40px;
            height: 40px;
        }

        .app.default-sidebar .app-header .profile-dropdown > .nav-link,
        .app.default-sidebar .app-header .profile-dropdown .avatar {
            width: 46px;
            height: 46px;
        }
    }
/* Topbar visibility and alignment fix */
.app.default-sidebar .app-header.top-header {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;

    position: fixed !important;
    top: 0 !important;
    left: var(--cms-sidebar-width) !important;

    width: calc(100% - var(--cms-sidebar-width)) !important;
    max-width: calc(100% - var(--cms-sidebar-width)) !important;

    margin: 0 !important;
    box-sizing: border-box !important;
    overflow: visible !important;
    z-index: 1000 !important;
}

.app.default-sidebar .app-header .container-fluid {
    width: 100% !important;
    max-width: 100% !important;
}

.app.default-sidebar .app-header .container-fluid > .d-flex {
    width: 100% !important;
    max-width: 100% !important;
    display: flex !important;
    align-items: center !important;
    flex-wrap: nowrap !important;
}

/* Right-side icons ko screen ke andar rakhega */
.app.default-sidebar .app-header .order-lg-2 {
    display: flex !important;
    margin-left: auto !important;
    flex-shrink: 0 !important;
    align-items: center !important;
}

/* Session dropdown */
.app.default-sidebar .app-header .header-option {
    flex-shrink: 0 !important;
}

/* Mobile fix */
@media (max-width: 991.98px) {
    .app.default-sidebar .app-header.top-header {
        left: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
}
.app.default-sidebar .app-sidebar2,
.app.default-sidebar .app-sidebar3,
.app.default-sidebar .app-header.top-header {
    background:#f2f6f9 !important;

    box-shadow: none !important;
    border: none !important;
}/* Sidebar item hover */
.app.default-sidebar .side-menu__item:hover {
    color: #ffffff !important;
    background-color: #17675f !important;
    border-radius: 22px !important;
}

.app.default-sidebar .side-menu__item:hover .side-menu__icon,
.app.default-sidebar .side-menu__item:hover .side-menu__label,
.app.default-sidebar .side-menu__item:hover .angle {
    color: #ffffff !important;
    stroke: #ffffff !important;
}

/* Dropdown open ho to parent item selected rahe */
.app.default-sidebar .slide.is-expanded > .side-menu__item,
.app.default-sidebar .slide:has(.slide-menu.show) > .side-menu__item,
.app.default-sidebar
    .slide:has(.slide-menu[style*="display: block"])
    > .side-menu__item {
    color: #ffffff !important;
    background-color: #17675f !important;
    border-radius: 22px !important;
}

.app.default-sidebar
    .slide.is-expanded
    > .side-menu__item
    .side-menu__icon,
.app.default-sidebar
    .slide.is-expanded
    > .side-menu__item
    .side-menu__label,
.app.default-sidebar
    .slide.is-expanded
    > .side-menu__item
    .angle {
    color: #ffffff !important;
    stroke: #ffffff !important;
}

/* Open dropdown ka child link */
.app.default-sidebar .slide-menu {
    padding: 8px 0 4px 30px !important;
}

.app.default-sidebar .slide-item {
    display: block !important;
    padding: 11px 14px !important;
    color: #252525 !important;
    border-left: 3px solid transparent !important;
    border-radius: 10px !important;
    background: transparent !important;
}

/* Child link hover/selected */
.app.default-sidebar .slide-item:hover,
.app.default-sidebar .slide-item.active {
    color: #17675f !important;
    font-weight: 600 !important;
    background: rgba(23, 103, 95, 0.10) !important;
    border-left-color: #17675f !important;
}/* Closed sidebar item */
.app.default-sidebar .slide:not(.is-expanded) > .side-menu__item {
    color: #252525 !important;
    background-color: transparent !important;
    border-radius: 22px !important;
}

.app.default-sidebar
    .slide:not(.is-expanded)
    > .side-menu__item
    .side-menu__icon {
    color: #17675f !important;
    stroke: #17675f !important;
}

.app.default-sidebar
    .slide:not(.is-expanded)
    > .side-menu__item
    .side-menu__label,
.app.default-sidebar
    .slide:not(.is-expanded)
    > .side-menu__item
    .angle {
    color: #252525 !important;
}

/* Dropdown open state */
.app.default-sidebar .slide.is-expanded > .side-menu__item {
    color: #ffffff !important;
    background-color: #17675f !important;
    border-radius: 22px !important;
}

.app.default-sidebar
    .slide.is-expanded
    > .side-menu__item
    .side-menu__icon,
.app.default-sidebar
    .slide.is-expanded
    > .side-menu__item
    .side-menu__label,
.app.default-sidebar
    .slide.is-expanded
    > .side-menu__item
    .angle {
    color: #ffffff !important;
    stroke: #ffffff !important;
}

/* Closed dropdown ki visible line remove karega */
.app.default-sidebar .slide:not(.is-expanded) > .slide-menu {
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    background: transparent !important;
    overflow: hidden !important;
}

/* Open dropdown */
.app.default-sidebar .slide.is-expanded > .slide-menu {
    height: auto !important;
    margin: 5px 0 8px !important;
    padding: 8px 0 4px 30px !important;
    overflow: visible !important;
}

/* Hover */
.app.default-sidebar
    .slide:not(.is-expanded)
    > .side-menu__item:hover {
    color: #ffffff !important;
    background-color: #17675f !important;
}

.app.default-sidebar
    .slide:not(.is-expanded)
    > .side-menu__item:hover
    .side-menu__icon,
.app.default-sidebar
    .slide:not(.is-expanded)
    > .side-menu__item:hover
    .side-menu__label,
.app.default-sidebar
    .slide:not(.is-expanded)
    > .side-menu__item:hover
    .angle {
    color: #ffffff !important;
    stroke: #ffffff !important;
}`}

</style>


    <div className="app sidebar-mini light-mode default-sidebar">
	<div className="page">
			<div className="page-main">

            <div className="app-sidebar app-sidebar2">
					<div className="app-sidebar__logo">
						<a className="header-brand" href="https://cms.bnu.edu.pk">
							<img src="../../assets/images/brand/logo.png" className="header-brand-img desktop-lgo" alt="Covido logo"/>
							<img src="../../assets/images/brand/logo1.png" className="header-brand-img dark-logo" alt="Covido logo"/>
							<img src="../../assets/images/brand/favicon.png" className="header-brand-img mobile-logo" alt="Covido logo"/>
							<img src="../../assets/images/brand/favicon1.png" className="header-brand-img darkmobile-logo" alt="Covido logo"/>
						</a>
					</div>
				</div>

                <aside className="app-sidebar app-sidebar3">
					
					<ul className="side-menu">
					{emp_login_email === 'haider.raza@bnu.edu.pk' && (
						<li className="slide">
							<a className="side-menu__item text-decoration-none"  data-toggle="slide" href="#">
							<svg
							 className="side-menu__icon"
							 xmlns="http://www.w3.org/2000/svg"
							 width="24" height="26" viewBox="0 0 24 24"
							 fill="none" stroke="currentColor"
							 strokeWidth={"2"} strokeLinecap={"round"}
							 strokeLinejoin={"round"}
							 >
							<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
							<span className="side-menu__label">Attendance</span><i className="angle fa fa-angle-right"></i></a>
							
								<ul className="slide-menu">
									<li><a className="slide-item" href="attendance_update_ex"><span>Mark Attendance</span></a></li>
								</ul>
							
						</li>
					)}
					{emp_login_email !== 'haider.raza@bnu.edu.pk' && (
						<li className="slide">
							<a className="side-menu__item text-decoration-none" data-toggle="slide" href="#">
								<svg className="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
									<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
									<circle cx="9" cy="7" r="4"></circle>
									<path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
									<path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
								</svg>
								<span className="side-menu__label">TA</span>
								<i className="angle fa fa-angle-right"></i>
							</a>
							<ul className="slide-menu">
								<li><a className="slide-item" href="approve_ta"><span>TA Management</span></a></li>
							</ul>
						</li>
					)}
					</ul>
				
				</aside>
                <div className="app-content main-content">
					<div className="side-app">
					{/* <!--app header--> */}
                    <div className="app-header header top-header">
							<div className="container-fluid">
								<div className="d-flex">
									<a className="header-brand" href="#">
										<img src="../../assets/images/brand/logo.png" className="header-brand-img desktop-lgo" alt="Dashtic logo"/>
										<img src="../../assets/images/brand/logo1.png" className="header-brand-img dark-logo" alt="Dashtic logo"/>
										<img src="../../assets/images/brand/favicon.png" className="header-brand-img mobile-logo" alt="Dashtic logo"/>
										<img src="../../assets/images/brand/favicon1.png" className="header-brand-img darkmobile-logo" alt="Dashtic logo"/>
									</a>
									{/* <div className="dropdown side-nav">
									<div className="app-sidebar__toggle" data-toggle="sidebar">
											<a className="open-toggle" href="#">
											<svg className="header-icon mt-1" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
													<path d="M0 0h24v24H0V0z" fill="none" />
													<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" />
												</svg>
											</a>
											<a className="close-toggle" href="#">
											<svg className="header-icon mt-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
										 viewBox="0 0 24 24" fill="none"
										 stroke="currentColor"
										 strokeWidth={"2"}
										 strokeLinecap={"round"} 
										strokeLinejoin={"round"}
										>
													<line x1="3" y1="12" x2="21" y2="12"></line>
													<line x1="3" y1="6" x2="21" y2="6"></line>
													<line x1="3" y1="18" x2="21" y2="18"></line>
												</svg>
											</a>
										</div>
									</div> */}
									<div className="dropdown  ml-5 header-option">
										<a className="nav-link icon p-0" data-toggle="dropdown">
											<svg className="header-icon" x="1008" y="1248" viewBox="0 0 24 24"  height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
												<path opacity=".3" d="M19.28,8.6 L18.58,7.39 L17.31,7.9 L16.25,8.33 L15.34,7.63 C14.95,7.33 14.54,7.09 14.11,6.92 L13.05,6.49 L12.89,5.36 L12.7,4 L11.3,4 L11.11,5.35 L10.95,6.48 L9.89,6.92 C9.48,7.09 9.07,7.33 8.64,7.65 L7.74,8.33 L6.69,7.91 L5.42,7.39 L4.72,8.6 L5.8,9.44 L6.69,10.14 L6.55,11.27 C6.52,11.57 6.5,11.8 6.5,12 C6.5,12.2 6.52,12.43 6.55,12.73 L6.69,13.86 L5.8,14.56 L4.72,15.4 L5.42,16.61 L6.69,16.1 L7.75,15.67 L8.66,16.37 C9.05,16.67 9.46,16.91 9.89,17.08 L10.95,17.51 L11.11,18.64 L11.3,20 L12.69,20 L12.88,18.65 L13.04,17.52 L14.1,17.09 C14.51,16.92 14.92,16.68 15.35,16.36 L16.25,15.68 L17.29,16.1 L18.56,16.61 L19.26,15.4 L18.18,14.56 L17.29,13.86 L17.43,12.73 C17.47,12.42 17.48,12.21 17.48,12 C17.48,11.79 17.46,11.57 17.43,11.27 L17.29,10.14 L18.18,9.44 L19.28,8.6 Z M12,16 C9.79,16 8,14.21 8,12 C8,9.79 9.79,8 12,8 C14.21,8 16,9.79 16,12 C16,14.21 14.21,16 12,16 Z"></path>
												<path d="M19.43,12.98 C19.47,12.66 19.5,12.34 19.5,12 C19.5,11.66 19.47,11.34 19.43,11.02 L21.54,9.37 C21.73,9.22 21.78,8.95 21.66,8.73 L19.66,5.27 C19.57,5.11 19.4,5.02 19.22,5.02 C19.16,5.02 19.1,5.03 19.05,5.05 L16.56,6.05 C16.04,5.65 15.48,5.32 14.87,5.07 L14.49,2.42 C14.46,2.18 14.25,2 14,2 L10,2 C9.75,2 9.54,2.18 9.51,2.42 L9.13,5.07 C8.52,5.32 7.96,5.66 7.44,6.05 L4.95,5.05 C4.89,5.03 4.83,5.02 4.77,5.02 C4.6,5.02 4.43,5.11 4.34,5.27 L2.34,8.73 C2.21,8.95 2.27,9.22 2.46,9.37 L4.57,11.02 C4.53,11.34 4.5,11.67 4.5,12 C4.5,12.33 4.53,12.66 4.57,12.98 L2.46,14.63 C2.27,14.78 2.22,15.05 2.34,15.27 L4.34,18.73 C4.43,18.89 4.6,18.98 4.78,18.98 C4.84,18.98 4.9,18.97 4.95,18.95 L7.44,17.95 C7.96,18.35 8.52,18.68 9.13,18.93 L9.51,21.58 C9.54,21.82 9.75,22 10,22 L14,22 C14.25,22 14.46,21.82 14.49,21.58 L14.87,18.93 C15.48,18.68 16.04,18.34 16.56,17.95 L19.05,18.95 C19.11,18.97 19.17,18.98 19.23,18.98 C19.4,18.98 19.57,18.89 19.66,18.73 L21.66,15.27 C21.78,15.05 21.73,14.78 21.54,14.63 L19.43,12.98 Z M17.45,11.27 C17.49,11.58 17.5,11.79 17.5,12 C17.5,12.21 17.48,12.43 17.45,12.73 L17.31,13.86 L18.2,14.56 L19.28,15.4 L18.58,16.61 L17.31,16.1 L16.27,15.68 L15.37,16.36 C14.94,16.68 14.53,16.92 14.12,17.09 L13.06,17.52 L12.9,18.65 L12.7,20 L11.3,20 L11.11,18.65 L10.95,17.52 L9.89,17.09 C9.46,16.91 9.06,16.68 8.66,16.38 L7.75,15.68 L6.69,16.11 L5.42,16.62 L4.72,15.41 L5.8,14.57 L6.69,13.87 L6.55,12.74 C6.52,12.43 6.5,12.2 6.5,12 C6.5,11.8 6.52,11.57 6.55,11.27 L6.69,10.14 L5.8,9.44 L4.72,8.6 L5.42,7.39 L6.69,7.9 L7.73,8.32 L8.63,7.64 C9.06,7.32 9.47,7.08 9.88,6.91 L10.94,6.48 L11.1,5.35 L11.3,4 L12.69,4 L12.88,5.35 L13.04,6.48 L14.1,6.91 C14.53,7.09 14.93,7.32 15.33,7.62 L16.24,8.32 L17.3,7.89 L18.57,7.38 L19.27,8.59 L18.2,9.44 L17.31,10.14 L17.45,11.27 Z M12,8 C9.79,8 8,9.79 8,12 C8,14.21 9.79,16 12,16 C14.21,16 16,14.21 16,12 C16,9.79 14.21,8 12,8 Z M12,14 C10.9,14 10,13.1 10,12 C10,10.9 10.9,10 12,10 C13.1,10 14,10.9 14,12 C14,13.1 13.1,14 12,14 Z"	></path>
											</svg>
											 <span className="nav-span"><i className="fa fa-angle-down ml-1 fs-18"></i></span>
										</a>
										<div className="dropdown-menu dropdown-menu-left dropdown-menu-arrow animated">
											<a className="dropdown-item" href="#">
												Fall 2022
											</a>
							
										</div>
									</div>
									<div className="d-flex order-lg-2 ml-auto">
									<a href="/contacts" className="nav-link pr-2 pt-2 leading-none">
											<span>

												<img src="https://cms.bnu.edu.pk/assets/staffimages/cmshelp.png" alt="img" width="55px"/>
											</span>
										</a>
										<a href="/cvt" className="nav-link pr-0 leading-none">
											<span>
												<img src="https://cms.bnu.edu.pk/assets/staffimages/helpcms1.png" alt="img" width="44px"/>
											</span>
										</a>
										<div className="dropdown   header-fullscreen pl-0">
											<a className="nav-link icon full-screen-link p-0" id="fullscreen-button">
												<svg className="header-icon" x="1008" y="1248" viewBox="0 0 24 24" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
													<path d="M7,14 L5,14 L5,19 L10,19 L10,17 L7,17 L7,14 Z M5,10 L7,10 L7,7 L10,7 L10,5 L5,5 L5,10 Z M17,17 L14,17 L14,19 L19,19 L19,14 L17,14 L17,17 Z M14,5 L14,7 L17,7 L17,10 L19,10 L19,5 L14,5 Z"></path>
												</svg>
											</a>
										</div>
	
										<div className="dropdown profile-dropdown">
										<a href="#" className="nav-link pr-0 leading-none" data-toggle="dropdown">
												<span>
													<img src={"https://cms.bnu.edu.pk/assets/staffimages/"+emp_details.imagename} alt="img" className="avatar avatar-md brround" />
												</span>
											</a>
											
											<div className="dropdown-menu dropdown-menu-right dropdown-menu-arrow animated">
												<div className="text-center">
													<a href="#" className="dropdown-item text-center user pb-0 font-weight-bold">{emp_details.username}</a>
													{/* <!-- <span className
													="text-center user-semi-title">HOD SB</span> --> */}
													<div className="dropdown-divider"></div>
												</div>
												<a className="dropdown-item d-flex" href="new_logout">


													<svg className="header-icon mr-4" x="1008" y="1248" viewBox="0 0 24 24" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
														<path d="M0 0h24v24H0V0zm0 0h24v24H0V0z" fill="none" />
														<path d="M6 20h12V10H6v10zm2-6h3v-3h2v3h3v2h-3v3h-2v-3H8v-2z" opacity=".3" />
														<path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM8.9 6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2H8.9V6zM18 20H6V10h12v10zm-7-1h2v-3h3v-2h-3v-3h-2v3H8v2h3z" />
													</svg>
													<div className="mt-1">Logout</div>
													<form id="logout-form" action="{{ route('logout') }}" method="POST" className="d-none">
														@csrf
													</form>
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
                        {/* <!--/app header--> */}

						{/* Content dashboard */}

				<MainDashboard/>

					{/* end content  dashboard*/}







                        </div></div>
				


                </div></div>





                {/* <!-- Jquery js--> */}
		<script src="../../assets/js/vendors/jquery-3.5.1.min.js"></script>

		{/* <!-- Bootstrap4 js--> */}
		<script src="../../assets/plugins/bootstrap/popper.min.js"></script>
		<script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>

		{/* <!--Othercharts js--> */}
		<script src="../../assets/plugins/othercharts/jquery.sparkline.min.js"></script>

		{/* <!-- Circle-progress js--> */}
		<script src="../../assets/js/vendors/circle-progress.min.js"></script>



           

    </div>
    </>);
}

export default Sidebar;