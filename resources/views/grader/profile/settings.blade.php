@extends('template')

@section('content')
<div class="page-header">
  <div class="page-header-title">
    <h4>Profile Settings</h4>
    <span>...</span>
  </div>

</div>


<div class="page-body">


  <div class="card">
    <div class="card-header">
      <h5>Login & Auth</h5>


    </div>
    <form method="POST" action="<?=url('/profile/setting-save')?>">

    @csrf


    <div class="card-block">


    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif



      <div class="form-group row">
        <div class="col-sm-2">
          <label class="col-form-label" for="inputSuccess1">Enable Login OTP</label>
        </div>
        <div class="col-sm-10 form-radio">


          <div class="radio radio-inline">
            <label>
              <input type="radio" name="otp_verify" value="1" <?=($user->otp_verify)?'checked':''?> >
              <i class="helper"></i>Yes
            </label>
          </div>


          <div class="radio radio-inline">
            <label>
              <input type="radio" name="otp_verify" value="0" <?=($user->otp_verify)?'':'checked'?> >
              <i class="helper"></i>No
            </label>
          </div>





        </div>
      </div>

     
    </div> <!-- end card black -->

    <div class="card-footer">
      <input type="submit" class="btn" value="Save Change" >
    </div>
    </form>
  </div>





</div>


@endsection