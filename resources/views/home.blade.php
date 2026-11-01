@extends('layouts.app')

@section('content')
<!-- Body Page Start -->
<div class="row">
<div class="col-xl-12 col-lg-12">
				<div class="card text-center user-contact-list">
					<div class="p-5">
						<div class="avatar avatar-xxl brround d-block cover-image mx-auto" data-image-src="../../assets/images/users/7.jpg" style="background: url(&quot;../../assets/images/users/7.jpg&quot;) center center;">	</div>
						<div class="wrapper mt-3">
							<p class="mb-0 mt-1 text-dark font-weight-semibold"><?=auth()->user()->username?></p>
							<small class="text-muted"><?=auth()->user()->email?></small>
						</div>
						<div class="">
							<a class="btn btn-light btn-svgs mt-5" href="<?=url('/home2')?>">
								<svg class="side-svg-icon mr-3" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								
								<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg> 
								Dashboard
							</a>
							<a class="btn btn-light btn-svgs mt-5" href="<?=url('/home2')?>">
							<svg class="svg-icon mr-3" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
							<path d="M0 0h24v24H0V0z" fill="none"></path><path d="M12 16c-2.69 0-5.77 1.28-6 2h12c-.2-.71-3.3-2-6-2z" opacity=".3"></path><circle cx="12" cy="8" opacity=".3" r="2"></circle><path d="M12 14c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm-6 4c.22-.72 3.31-2 6-2 2.7 0 5.8 1.29 6 2H6zm6-6c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z"></path></svg>Profile</a>
						</div>
					</div>
				</div>
			</div>
						</div>

<!-- Body Page End -->
@endsection


@section('javascript')
<script>
	
function letUserExit() {
    // Add code here to redirect user where she expects to go 
    // when pressing exit button or to submit a form or whatever.
}

var exitConfirmDialog = new BootstrapDialog({
    title: 'Data unsaved',
    message: 'Are you sure you want to continue?',
    closable: false,
    buttons: [
        {
            label: 'Cancel',
            action: function(dialog) {
                dialog.close();
            }
        },
        {
            label: 'Continue',
            cssClass: 'btn-primary',
            action: function(dialog) {
                letUserExit();
            }
        }
    ]
});

// Add event to invoke dialog to your exit button here
$('button.exit').on('click', function() {
    // Show or open? Not quite sure what difference is. Use the one
    // that's most appropriate.
    exitConfirmDialog.show();

    // Stop any events (like a form being submitted or user being
    // redirected.) Need to wait until user responds to modal and
    // have event take place then.
    return false;
});



	$(document).ready(function(){
		exitConfirmDialog.show();
	});

</script>
@endsection
