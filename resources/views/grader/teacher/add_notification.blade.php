
@extends('layouts.app',['pageTitle' => ' '])

@section('content')


<!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
  Send Notification
</button> -->
                    <div class="container">
                      <div class="card" >
                        <div class="card-header nav-justified hemi-intro-selected">
                            <h3 class="card-title ">Notification Panel </h3>
                        </div>
                        <div class="card-body" id="con">
                          <form enctype="multipart/form-data" method="post" name="cformdes" id="cformdes">
                            <div class="form-group" >
                              <label for="course_name">Description</label>
                              <textarea class="form-control " name="description" id="description" rows="3" placeholder="Please enter Description of Notification "></textarea>
                              </div>
                              <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                                  <button id="btn" type="button" name="btn" onclick="Add_desc()" class="btn btn-success btn-submit-desc">
                                      <a href="#" class="btn btn-squared-default-plain btn-success">
                                          <i class="fa fa-plus fa-2x"></i>
                                          <br />
                                          ADD
                                      </a>
                                  </button>
                               </div>
                            </form>
                        </div>
                      </div>
                     </div>       


<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
@stop
@section('javascript')
<script>
     function Add_desc() {

var bi_form = document.querySelector('#cformdes');
var fd = new FormData(bi_form);
$('#error_msg1').html('');
$.ajax({
   method: "POST",
   url: '{{'/api/teacher-notification'}}',
   data: fd,
   processData: false,
   contentType: false,
   headers: {
       Authorization: 'Bearer <?= auth()->user()->api_token ?>',
       Accept: 'application/json'
   }
}).done(function(res) {
   if (res.status) {
       //alert(res.message);

      

   } else {
      
   }
}).fail(function(res) {
   //alert(res.message);
   $('#error_msg').html(res);

   console.log(res);
   //location.reload();
});
}

</script>