@extends('layouts.app',['pageTitle' => 'Stream'])
@section('content')



<div class="row row-sm">
							<div class="col-lg-8">
								<div class="card">
									<div class="tab-menu-heading p-0 border-0">
										<div class="tabs-menu1 px-3">
											<ul class="nav">
												<li><a href="#tab-7" class="active" data-toggle="tab">Post</a></li>
												<li><a href="#tab-8" data-toggle="tab" class="">Albumbs</a></li>
												<li><a href="#tab-9" data-toggle="tab" class="">Videos</a></li>
											</ul>
										</div>
									</div>
									<div class="card-body d-flex border-top">
										<div class="mr-4"><img alt="" class="rounded-circle avatar avatar-md" src="../../assets/images/users/16.jpg"></div>
										<form class="profile-edit w-100">

<!-- border-top-0 -->

                    <div class="profile-share border border-light2 "  style="border-radius:0"  >

                    <p>For</p>


    										<select><option>All Class</option></select>
											
											</div>


											<textarea class="form-control" placeholder="What are you doing right now?" rows="5" style="border-radius:0" ></textarea>
											

                     
                      
                      
                      
                      <div class="profile-share border border-light2">
												<a href="#" class="mr-2" title="" data-toggle="tooltip" data-placement="bottom" data-original-title="Audio"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 13.3c.66 0 1.19-.54 1.19-1.2l.01-6.2c0-.66-.54-1.2-1.2-1.2s-1.2.54-1.2 1.2v6.2c0 .66.54 1.2 1.2 1.2z" opacity=".3"/><path d="M12 15c1.66 0 2.99-1.34 2.99-3L15 6c0-1.66-1.34-3-3-3S9 4.34 9 6v6c0 1.66 1.34 3 3 3zm-1.2-9.1c0-.66.54-1.2 1.2-1.2s1.2.54 1.2 1.2l-.01 6.2c0 .66-.53 1.2-1.19 1.2s-1.2-.54-1.2-1.2V5.9zm6.5 6.1c0 3-2.54 5.1-5.3 5.1S6.7 15 6.7 12H5c0 3.41 2.72 6.23 6 6.72V22h2v-3.28c3.28-.48 6-3.3 6-6.72h-1.7z"/></svg></a>
												<a href="#" class="mr-2" title="" data-toggle="tooltip" data-placement="bottom" data-original-title="Video"><svg  class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 8h10v8H5z" opacity=".3"/><path d="M17 7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4V7zm-2 9H5V8h10v8z"/></svg></a>
												<a href="#" class="mr-2" title="" data-toggle="tooltip" data-placement="bottom" data-original-title="Picture"><svg  class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 19h14V5H5v14zm4-5.86l2.14 2.58 3-3.87L18 17H6l3-3.86z" opacity=".3"/><path d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2zm16 14H5V5h14v14zm-4.86-7.14l-3 3.86L9 13.14 6 17h12z"/></svg></a>
												<button type="submit" class="btn btn-sm btn-success pull-right"><i class="fa fa-share ml-1"></i> Share</button>
											</div>



										</form>
									</div>
								</div>
								<div class="card overflow-hidden">
									<div class="card-body pb-0">
										<div class="d-flex">
											<div class="media mt-0">
												<div class="media-user mr-2">
													<div class=""><img alt="" class="rounded-circle avatar avatar-md" src="../../assets/images/users/16.jpg"></div>
												</div>
												<div class="media-body">
													<h6 class="mb-0 mt-1 font-weight-bold">Peter Hill</h6>
													<small class="text-primary">just now</small>
												</div>
											</div>
											<div class="ml-auto">
												<div class="dropdown show">
													<a class="new option-dots" href="JavaScript:void(0);" data-toggle="dropdown"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg></a>
													<div class="dropdown-menu dropdown-menu-right">
														<a class="dropdown-item" href="#">Edit Post</a>
														<a class="dropdown-item" href="#">Delete Post</a>
														<a class="dropdown-item" href="#">Personal Settings</a>
													</div>
												</div>
											</div>
										</div>
										<div class="mt-4">
											<p class="mb-0">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable.</p>
										</div>
										<div class="media mg-t-15 profile-footer">
											<div class="media-user mr-2">
												<div class="avatar-list avatar-list-stacked">
													<span class="avatar brround" style="background-image: url(../../assets/images/users/12.jpg)"></span>
													<span class="avatar brround" style="background-image: url(../../assets/images/users/2.jpg)"></span>
													<span class="avatar brround" style="background-image: url(../../assets/images/users/9.jpg)"></span>
													<span class="avatar brround" style="background-image: url(../../assets/images/users/2.jpg)"></span>
													<span class="avatar brround" style="background-image: url(../../assets/images/users/4.jpg)"></span>
													<span class="avatar brround">+28</span>
												</div>
											</div>
											<div class="media-body">
												<h6 class="mb-0 mt-2 ml-2">28 people like your photo</h6>
											</div>
											<div class="ml-auto">
												<div class="d-flex">
													<a class="new" href="JavaScript:void(0);"><svg class="svg-icon mr-3 mt-2" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M16.5 5c-1.54 0-3.04.99-3.56 2.36h-1.87C10.54 5.99 9.04 5 7.5 5 5.5 5 4 6.5 4 8.5c0 2.89 3.14 5.74 7.9 10.05l.1.1.1-.1C16.86 14.24 20 11.39 20 8.5c0-2-1.5-3.5-3.5-3.5z" opacity=".3"/><path d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z"/></svg></a>
													<a class="new" href="JavaScript:void(0);"><svg class="svg-icon mr-3 mt-2" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 17.17V4H4v12h14.83L20 17.17zM18 14H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z" opacity=".3"/><path d="M4 18h14l4 4-.01-18c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2zM4 4h16v13.17L18.83 16H4V4zm2 8h12v2H6zm0-3h12v2H6zm0-3h12v2H6z"/></svg></a>
													<a class="new" href="JavaScript:void(0);"><svg class="svg-icon mt-2" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><circle cx="18" cy="5" opacity=".3" r="1"/><circle cx="6" cy="12" opacity=".3" r="1"/><circle cx="18" cy="19.02" opacity=".3" r="1"/><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92c0-1.61-1.31-2.92-2.92-2.92zM18 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM6 13c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm12 7.02c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/></svg></a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card overflow-hidden">
									<div class="card-body pb-0">
										<div class="d-flex">
											<div class="media mt-0">
												<div class="media-user mr-2">
													<div class=""><img alt="" class="rounded-circle avatar avatar-md" src="../../assets/images/users/16.jpg"></div>
												</div>
												<div class="media-body">
													<h6 class="mb-0 mt-1 font-weight-bold">Peter Hill</h6>
													<small class="text-muted">Sep 26 2019, 10:14am</small>
												</div>
											</div>
											<div class="ml-auto">
												<div class="dropdown show">
													<a class="new option-dots" href="JavaScript:void(0);" data-toggle="dropdown"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg></a>
													<div class="dropdown-menu dropdown-menu-right">
														<a class="dropdown-item" href="#">Edit Post</a>
														<a class="dropdown-item" href="#">Delete Post</a>
														<a class="dropdown-item" href="#">Personal Settings</a>
													</div>
												</div>
											</div>
										</div>
										<div class="mt-4">
											<p class="mb-0">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable.</p>
											<div class="d-flex">
												<img src="../../assets/images/photos/2.jpg" alt="img" class="w-40 m-1">
												<img src="../../assets/images/photos/3.jpg" alt="img" class="w-40 m-1">
											</div>
										</div>
										<div class="media mg-t-15 profile-footer">
											<div class="media-user mr-2">
												<div class="avatar-list avatar-list-stacked">
													<span class="avatar brround" style="background-image: url(../../assets/images/users/12.jpg)"></span>
													<span class="avatar brround" style="background-image: url(../../assets/images/users/2.jpg)"></span>
													<span class="avatar brround" style="background-image: url(../../assets/images/users/9.jpg)"></span>
													<span class="avatar brround" style="background-image: url(../../assets/images/users/2.jpg)"></span>
													<span class="avatar brround" style="background-image: url(../../assets/images/users/4.jpg)"></span>
													<span class="avatar brround">+28</span>
												</div>
											</div>
											<div class="media-body">
												<h6 class="mb-0 mt-2 ml-2">28 people like your photo</h6>
											</div>
											<div class="ml-auto">
												<div class="d-flex">
													<a class="new" href="JavaScript:void(0);"><svg class="svg-icon mr-3 mt-2" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M16.5 5c-1.54 0-3.04.99-3.56 2.36h-1.87C10.54 5.99 9.04 5 7.5 5 5.5 5 4 6.5 4 8.5c0 2.89 3.14 5.74 7.9 10.05l.1.1.1-.1C16.86 14.24 20 11.39 20 8.5c0-2-1.5-3.5-3.5-3.5z" opacity=".3"/><path d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z"/></svg></a>
													<a class="new" href="JavaScript:void(0);"><svg class="svg-icon mr-3 mt-2" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 17.17V4H4v12h14.83L20 17.17zM18 14H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z" opacity=".3"/><path d="M4 18h14l4 4-.01-18c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2zM4 4h16v13.17L18.83 16H4V4zm2 8h12v2H6zm0-3h12v2H6zm0-3h12v2H6z"/></svg></a>
													<a class="new" href="JavaScript:void(0);"><svg class="svg-icon mt-2" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><circle cx="18" cy="5" opacity=".3" r="1"/><circle cx="6" cy="12" opacity=".3" r="1"/><circle cx="18" cy="19.02" opacity=".3" r="1"/><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92c0-1.61-1.31-2.92-2.92-2.92zM18 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM6 13c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm12 7.02c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/></svg></a>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card overflow-hidden">
									<div class="card-body pb-0">
										<div class="d-flex">
											<div class="media mt-0">
												<div class="media-user mr-2">
													<div class=""><img alt="" class="rounded-circle avatar avatar-md" src="../../assets/images/users/16.jpg"></div>
												</div>
												<div class="media-body">
													<h6 class="mb-0 mt-1 font-weight-bold">Peter Hill</h6>
													<small class="text-muted">Sep 24 2019, 09:14am</small>
												</div>
											</div>
											<div class="ml-auto">
												<div class="dropdown show">
													<a class="new option-dots" href="JavaScript:void(0);" data-toggle="dropdown"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg></a>
													<div class="dropdown-menu dropdown-menu-right">
														<a class="dropdown-item" href="#">Edit Post</a>
														<a class="dropdown-item" href="#">Delete Post</a>
														<a class="dropdown-item" href="#">Personal Settings</a>
													</div>
												</div>
											</div>
										</div>
										<div class="mt-4">
											<p class="mb-0">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable.</p>
											<div class="d-flex">
												<img src="../../assets/images/photos/4.jpg" alt="img" class="w-30 m-1">
												<img src="../../assets/images/photos/5.jpg" alt="img" class="w-30 m-1">
												<img src="../../assets/images/photos/6.jpg" alt="img" class="w-30 m-1">
											</div>
										</div>
										<div class="media mg-t-15 profile-footer">
											<div class="media-user mr-2">
												<div class="avatar-list avatar-list-stacked">
													<span class="avatar brround" style="background-image: url(../../assets/images/users/12.jpg)"></span>
													<span class="avatar brround" style="background-image: url(../../assets/images/users/2.jpg)"></span>
													<span class="avatar brround" style="background-image: url(../../assets/images/users/9.jpg)"></span>
													<span class="avatar brround" style="background-image: url(../../assets/images/users/2.jpg)"></span>
													<span class="avatar brround" style="background-image: url(../../assets/images/users/4.jpg)"></span>
													<span class="avatar brround">+28</span>
												</div>
											</div>
											<div class="media-body">
												<h6 class="mb-0 mt-2 ml-2">28 people like your photo</h6>
											</div>
											<div class="ml-auto">
												<div class="d-flex">
													<a class="new" href="JavaScript:void(0);"><svg class="svg-icon mr-3 mt-2" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M16.5 5c-1.54 0-3.04.99-3.56 2.36h-1.87C10.54 5.99 9.04 5 7.5 5 5.5 5 4 6.5 4 8.5c0 2.89 3.14 5.74 7.9 10.05l.1.1.1-.1C16.86 14.24 20 11.39 20 8.5c0-2-1.5-3.5-3.5-3.5z" opacity=".3"/><path d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z"/></svg></a>
													<a class="new" href="JavaScript:void(0);"><svg class="svg-icon mr-3 mt-2" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 17.17V4H4v12h14.83L20 17.17zM18 14H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z" opacity=".3"/><path d="M4 18h14l4 4-.01-18c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2zM4 4h16v13.17L18.83 16H4V4zm2 8h12v2H6zm0-3h12v2H6zm0-3h12v2H6z"/></svg></a>
													<a class="new" href="JavaScript:void(0);"><svg class="svg-icon mt-2" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><circle cx="18" cy="5" opacity=".3" r="1"/><circle cx="6" cy="12" opacity=".3" r="1"/><circle cx="18" cy="19.02" opacity=".3" r="1"/><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92c0-1.61-1.31-2.92-2.92-2.92zM18 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM6 13c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm12 7.02c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/></svg></a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="card mg-b-20 card--events">
									<div class="card-header">
										<div class="card-title">
											Trending Posts
										</div>
									</div>
									<div class="card-body p-0">
										<div class="">
											<ul class="list-group mb-0">
												<li class="list-group-item d-flex border-top-0 border-left-0 border-right-0">
													<div class="w-3 h-3 bg-primary mr-3 mt-1 brround"></div>
													<div>
														<label class="fs-12">Oct 20 <span class="font-weight-semibold">Sunday</span></label>
														<h5>New Fests Blog</h5>
														<p class="mb-0 text-muted fs-12"><strong>8AM - 4PM</strong> Rew City, USA</p>
													</div>
												</li>
												<li class="list-group-item d-flex border-left-0 border-right-0">
													<div class="w-3 h-3 bg-secondary mr-3 mt-1 brround"></div>
													<div>
														<label class="fs-12">Oct 18 <span class="font-weight-semibold">Friday</span></label>
														<h6>New Location Visited</h6>
														<p class="mb-0 text-muted fs-12"><strong>8AM - 5PM</strong> Newyork</p>
													</div>
												</li>
												<li class="list-group-item d-flex border-left-0 border-right-0">
													<div class="w-3 h-3 bg-success mr-3 mt-1 brround"></div>
													<div>
														<label class="fs-12">Oct 12 <span class="font-weight-semibold">Saturday</span></label>
														<h6>Weding Event Dance Show</h6>
														<p class="mb-0 text-muted fs-12"><strong>8AM - 5PM</strong> Japan</p>
													</div>
												</li>
												<li class="list-group-item d-flex border-left-0 border-right-0">
													<div class="w-3 h-3 bg-warning mr-3 mt-1 brround"></div>
													<div>
														<label class="fs-12">Sep 20 <span class="font-weight-semibold">Friday</span></label>
														<h6>Field day Festival Events Orders</h6>
														<p class="mb-0 text-muted fs-12"><strong>8AM - 4PM</strong> Rew City, USA</p>
													</div>
												</li>
												<li class="list-group-item d-flex border-left-0 border-right-0">
													<div class="w-3 h-3 bg-info mr-3 mt-1 brround"></div>
													<div>
														<label class="fs-12">Sep 25 <span class="font-weight-semibold">Wednesday</span></label>
														<h6>Christamas Party Event Orders</h6>
														<p class="mb-0 text-muted fs-12"><strong>8AM - 5PM</strong> Newyork</p>
													</div>
												</li>
												<li class="list-group-item d-flex border-left-0 border-right-0">
													<div class="w-3 h-3 bg-danger mr-3 mt-1 brround"></div>
													<div>
														<label class="fs-12">Sep 30 <span class="font-weight-semibold">Monday</span></label>
														<h6>New Weding Event Dance Show Orders</h6>
														<p class="mb-0 text-muted fs-12"><strong>8AM - 5PM</strong> Japan</p>
													</div>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>

						</div>



@stop

@section('javascript')
<script type="text/javascript">
 


</script>
@stop