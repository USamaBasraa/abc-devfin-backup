@extends('layouts.app', ['pageTitle' => 'Car pooling'])
@section('content')
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<style>
.invisible { display: none; }
.dropbtn {
  background-color: #04AA6D;
  color: white;
  padding: 10px;
  font-size: 16px;
  border: none;
}
.dropdown {
  position: relative;
  display: inline-block;
}
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f1f1f1;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba((142, 152, 219, 1.0));
  z-index: 1;
}
.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}
.dropdown-content a:hover {background-color: #ddd;}

.dropdown:hover .dropdown-content {display: block;}

.dropdown:hover .dropbtn {background-color: #3e8e41;}
ul {
    margin: 20px;
}
.input-color {
    position: relative;
}
.input-color input {
    padding-left: 25px;
      border:0px solid;
}
.input-color .color-box {
    width: 15px;
    height: 15px;
    display: inline-block;
    background-color: #ccc;
    position: absolute;
    left: 5px;
    top: 5px;
}
.challanDIV {
    height: 350px;
    display: inline-block;
    width: 100%;
    overflow: auto;
}
.weekDays-selector input {
  display: none!important;
}
.weekDays-selector input[type=checkbox] + label {
  display: inline-block;
  border-radius: 6px;
  background: #dddddd;
  height: 35px;
  width: 80px;
  margin-right: 3px;
  line-height: 40px;
  text-align: center;
  cursor: pointer;
}
.weekDays-selector input[type=checkbox]:checked + label {
  background: blue;
  color: #ffffff;
}
</style>
<?php
$sharing_with = array("Male","Female","Any");
?>
<div class="card" style="margin-top:10px;margin-right: 20px !important;width:99%;border-radius:10px">
    <div class="card-header bg-primary" style="color:white !important;border-top-right-radius:10px;border-top-left-radius:10px">
        <div style="float:left">
            <h3 class="card-title">Rider Profile</h3>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group ">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12  col-lg-12 form-group " style="text-align:right;"> 
                        @if(auth()->user()->group_id =='825')         
                        <img src="https://cms.bnu.edu.pk/students_pics/<?=auth()->user()->imagename?>" alt="No Image Found" width="150" height="190">    
                        @else
                        <img src="https://cms.bnu.edu.pk/students_pics/<?=auth()->user()->imagename?>" alt="No Image Found" width="150" height="190"> 
                        @endif                                 
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-md-6  col-lg-6 form-group ">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12  col-lg-12 form-group "> 
                        <label class="col-form-label" ><b>Name:</b></label>           
                        <span><?=auth()->user()->full_name?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12  col-lg-12 form-group "> 
                        <label class="col-form-label" ><b>Mobile:</b></label>           
                        <span><?=auth()->user()->mobile?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12  col-lg-12 form-group "> 
                        <label class="col-form-label" ><b>Email:</b></label>           
                        <span><?=auth()->user()->email?></span>
                    </div>
                </div>               
            </div>
        </div>  
    </div>
</div>
<div class="card" style="margin-top:10px;margin-right: 20px !important;width:99%;border-radius:10px">
    <div class="card-header  bg-primary" style="color:white !important;border-top-right-radius:10px;border-top-left-radius:10px">
        <div style="float:left">
            <h3 class="card-title">Car Pooling</h3>
        </div>
    </div>
    <div class="card-body">
        <form id="car_pulling" method="post" onsubmit="event.preventDefault(); autoSearch(this);">    
            <input type="hidden" id="cp_id" name="cp_id">  
            <div class="row">
                <div class="col-12 col-sm-12 col-md-6  col-lg-6 form-group " id="show_hide_ride">
                    <label class="col-form-label" ><b>Ride Status</b></label>           
                    <select  class="custom-select form-control" onchange="rideClicked(this)" name="want_ride" id="want_ride" >
                        <option value="">--Please Select Ride Options--</option>                
                        <option value="Offer Ride">Offer Ride</option>
                        <option value="Get Ride">Get Ride</option>                
                    </select>   
                </div>  
                <div class="col-12 col-sm-12 col-md-6  col-lg-6 form-group ">
                    <label class="col-form-label" ><b>Contact Options</b></label>           
                    <select  class="custom-select form-control " onchange="contactDetail(this)" name="contact_me" id="contact_me">
                        <option value="">--How to Contact Me--</option>                
                        <option value="On Mobile">On Mobile</option>                         
                        <option value="On CMS Email">On CMS Chat</option>             
                    </select>   
                </div>  
                <div id="contDetail" class="col-12 col-sm-12 col-md-3 col-lg-3 form-group" style="display:none">
                    <label class="col-form-label" ><b>Contact Detail</b></label>           
                    <input type="text" class="form-control" readonly id="contact_detail" name="contact_detail" placeholder="Contact Detail">    
                </div>     
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-3  col-lg-3 form-group ">
                    <label class="col-form-label" ><b>Vehicle Type</b></label>           
                    <select  class="custom-select form-control" name="vehical_type" id="vehical_type"  onchange="vehicalType(this)" data-placeholder="--Please Select Vehical--">
                        <option value="">--Please Select Vehical--</option>                
                        <option value="Car">Car</option>
                        <option value="Bike">Bike</option>                
                    </select>   
                </div>  
                <div class="col-12 col-sm-12 col-md-3 col-lg-3 form-group vehicle_cap">
                    <label class="col-form-label" ><b>Sharing Capacity</b></label>           
                    <input type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');" maxlength="1" class="form-control" id="vehicle_capacity" name="vehicle_capacity" placeholder="Sharing Capacity">    
                </div>  
                
                <div class="col-12 col-sm-12 col-md-3 col-lg-3 form-group vehicle_cap">
                        <label class="col-form-label" ><b>Vehicle No</b></label>           
                        <input type="text" class="form-control" style="text-transform: uppercase;" id="vehicle_no" name="vehicle_no" placeholder="ABC-00-0000">    
                    </div>  
                <div class="col-12 col-sm-12 col-md-3 col-lg-3 form-group "> 
                    <label class="col-form-label"><b>Share With</b></label>
                    <select  class="custom-select form-control" name="share_with" id="share_with">
                        <option value="">--Select Share With--</option>                
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>   
                        <option value="Any">Any</option>                               
                    </select>                 
                </div>
            </div>
            </hr>               
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group "> 
                    <label class="form-label"><b>Preferred Location</b><span style="color:red;font-size:12px"> (You can add multiple locations by comma separated or entering space)</span></label>
                    <!-- <input type="text" name="search_data" id="search_data" placeholder="" autocomplete="off" class="form-control input-lg" />  -->
                
                    <input  type="text" class="form-control" name="search_data" id="search_data" placeholder="Preferred Location">        
                </div>
            </div>              
            <div  style="padding:3px;background-color:#1ABC0C  !important;color:black !important; color:white !important;border-radius:5px;height:40px;">
                <div>
                    <h3 class="card-title">Optional Fields</h3>
                </div>
            </div>   
                <div class="row">                                                 
                    <div id="routePlan" class="col-12 col-sm-12 col-md-12 col-lg-12 form-group">
                        <label class="col-form-label" ><b>Route Map</b></label>&nbsp &nbsp&nbsp<a target="_blank" style="color:blue" href="https://www.google.com/maps/dir/BNU+CITY+CAMPUS,+Shahrah-e-Quaid-e-Azam,+Upper+Mall+Scheme,+Lahore/Beaconhouse+National+University%D8%8C+13+Km+Off+Thokar+Niazbeg,+Raiwand+Rd,+Beacon+House+Society,+Lahore,+Punjab+53700%E2%80%AD/@31.4539637,74.2551051,12z/data=!3m1!4b1!4m14!4m13!1m5!1m1!1s0x3919052e2e32fc41:0xf431b17a26ac6528!2m2!1d74.3529845!2d31.5406309!1m5!1m1!1s0x391855549698f6f7:0x4ffb44644f967144!2m2!1d74.2160936!2d31.36463!3e0">(Copy Route From here)</a>           
                        <input  type="text" class="form-control" id="route_link" name="route_link" placeholder="Route Map">    
                    </div>   
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group ">
                        <label class="col-form-label" ><b>Route Option</b></label>                              
                        <select  class="custom-select form-control " onchange="chkWay(this)"   name="pay_for_one_side" id="pay_for_one_side" data-placeholder="--Please Select--">
                            <option value="">--Please Select--</option>                
                            <option value="One Way">One Way</option>
                            <option value="Two Way">Two Way</option>                              
                        </select>    
                    </div>    
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group" style="display:none" id="time_div">
                        <label class="col-form-label" ><b>Time</b></label>           
                        <input type="text" class="form-control" id="time" name="time" placeholder="One Way">    
                    </div>          
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group" style="display:none" id="specific_day">
                        <label class="col-form-label" ><b>Enter Time for Today <span style="color:tomato">( Format 09:30 ) </span></b></label>           
                        <input type="text" class="form-control" id="specific_date_time" name="specific_date_time" placeholder="09:30">    
                    </div>                        
                </div>  
                <div id="showDays" class="row" style="display:none">
                    <?php
                    $days_array = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                    foreach($days_array as $day_name)
                    {                            
                    ?>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group " >  
                            <div class="row">                                   
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4">   
                                    <label class="form-control">{{$day_name}}</label>  
                                </div> 
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4">
                                    <select name="<?=$day_name?>_morning_time" class="custom-select form-control" id="<?=$day_name?>_morning_time">
                                    <?php
                                    $start_time = strtotime('00:00');
                                    $end_time = strtotime('23:59');
                                    $interval = 30 * 60; // 30 minutes in seconds
                                    echo '<option value="">Not Available</option>';
                                    for ($i = $start_time; $i <= $end_time; $i += $interval) {
                                        $time = date('H:i', $i);
                                        echo '<option value="' . $time . '">' . $time . '</option>';
                                    }
                                    ?>
                                    </select>    
                                </div>
                                <div class="col-4 col-sm-4 col-md-4 col-lg-4">
                                    <select name="<?=$day_name?>_back_time" class="custom-select form-control" id="<?=$day_name?>_back_time">
                                    <?php
                                    $start_time = strtotime('00:00');
                                    $end_time = strtotime('23:59');
                                    $interval = 30 * 60; // 30 minutes in seconds
                                    echo '<option value="">Not Available</option>';
                                    for ($i = $start_time; $i <= $end_time; $i += $interval) {
                                    $time = date('H:i', $i);
                                    echo '<option value="' . $time . '">' . $time . '</option>';
                                    }
                                    ?>
                                    </select>
                                </div> 
                            </div>  
                        </div>   
                    <?php
                    }
                    ?>              
                </div>      
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12  col-lg-12 form-group ">
                        <label class="col-form-label" ><b>Vehicle Information</b></label>           
                        <select  class="custom-select form-control" onchange="shareInfo(this,1)" name="share_info" id="share_info">
                            <option value="">--Select Information--</option>     
                            <option value="Vehical Info">Vehical Info</option>                               
                        </select>   
                    </div>  
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group show-vehicle" style="display:none">
                        <label class="col-form-label" ><b>Engine Capacity</b></label>           
                        <input type="text" class="form-control" id="vehicle" name="vehicle" placeholder="Engine Capacity">    
                    </div>   
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group show-vehicle" style="display:none">
                        <label class="col-form-label" ><b>Vehicle Model</b></label>           
                        <input type="text" class="form-control" id="vehicle_model" name="vehicle_model" placeholder="Vehicle Model">    
                    </div>  
                </div>
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12  col-lg-12 form-group ">
                        <label class="col-form-label" ><b>Do you Smoke while Driving</b></label>           
                        <select  class="custom-select form-control" name="smoking_info" id="smoking_info">
                            <option value="">--Please Select--</option>         
                        
                            <option value="Yes">Yes</option>   
                            <option value="No">No</option>                               
                        </select>   
                    </div>                         
                </div>                
                <div class="row" style="margin-top:20px;" >  
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;margin-bottom:20px"  id="submitBtn">  
                        <button type="button" id="btn" class="btn btn-primary" onclick="submitForm()">Submit</button>                
                        <span id="successMsg"></span>   
                        <span id="errorMsg"></span>                          
                    </div>   
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;margin-bottom:20px;display:none"  id="updtBtn">                
                        <button type="button" class="btn btn-primary" onclick="UpdateForm()">Update</button> 
                        <button type="button" class="btn btn-secondary" onclick="AddNewForm()">Reset</button> 
                        <span id="UpdSuccessMsg"></span>       
                        <span id="errorUpdMsg"></span>                       
                    </div>         
                </div>               
        </form>
    </div>
</div>
<div  id="cardLoop">

</div>
<div class="table-responsive" style="display:none">
    <table class="table table-striped">
        <thead class="bg-primary text-white">
            <tr>
                <th class="text-white" style="text-align:center;">Sr#</th>
                <th class="text-white" style="text-align:left;">I Want To</th>
                <th class="text-white" style="text-align:left;">Contact Me Via</th>
                <th class="text-white" style="text-align:left;">Start Location</th>   
                <th class="text-white" style="text-align:left;">Vehicle Type</th>
                <th class="text-white" style="text-align:left;">Preffered Location</th>
                <th class="text-white" style="text-align:left;">Vehicle CC</th>    
                <th class="text-white" style="text-align:left;">Vehicle Model</th>
                <th class="text-white" style="text-align:left;">Vehicle Capacity</th>                              
                <th class="text-white" style="text-align:left;">Mon</th>
                <th class="text-white" style="text-align:left;">Tue</th>
                <th class="text-white" style="text-align:left;">Wed</th>
                <th class="text-white" style="text-align:left;">Thu</th>
                <th class="text-white" style="text-align:left;">Fri</th>
                <th class="text-white" style="text-align:left;">Sat</th>
                <th class="text-white" style="text-align:left;">Sun</th>       
                <th class="text-white" style="text-align:left;">Can Pay For</th>                           
                <th class="text-white" style="text-align:left;">Route Link</th>
                <th class="text-white" style="text-align:left;">Rider Name</th>             
            </tr>
        </thead>
        <tbody id="tbl_exporttable_to_xls">
        </tbody>
    </table>
</div>
</div>
@endsection
@section('javascript')     
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/bootstrap-tokenfield.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/bootstrap-tokenfield.js"></script>  
<script type="text/javascript">

function rideClicked(sel){     
    if(sel.value == 'Get Ride'){
        $('.vehicle_cap').hide();    
    }else{
        $('.vehicle_cap').show();
    }
}

function AddNewForm(){
    window.scrollTo(0, 370);
    $('#search_data').tokenfield('setTokens', " ");
    $('#updtBtn').hide();
    $('#submitBtn').show();
    $("#car_pulling")[0].reset();
    $('#share_info').val('');
    $('#uni_timings').val('will share on contact');
    $('#showDays').hide();
    $('.show-vehicle').hide();   
    $('#startLocation').hide();
}
$(document).ready(function(){
    getRequest();
    // $('#search_data').tokenfield({
    // autocomplete :{
    //     source: function(request, response)
    //     {
    //     console.log('tokenfield console');             
    //     },
    //     delay: 100
    // }
    // });

});
function vehicalType(sel){
    if(sel.value == 'Car'){
        $('#vehicle_capacity').val(3);
    }else if(sel.value == 'Bike'){
        $('#vehicle_capacity').val(1);
    }
}
function contactDetail(sel){    
    var mobile = '<?=auth()->user()->mobile?>';
    var email = '<?=auth()->user()->email?>';
    if(sel.value == 'On Mobile'){            
        $('#contact_detail').val(mobile);
    }else if(sel.value == 'On CMS Email'){            
        $('#contact_detail').val(email);
    }
}
function inOutTime(sel){        
    if(sel.value == 'let me mention days wise'){
        $('#showDays').show();
    }else{
        $('#showDays').hide();
    }
}
function routeshareInfo(sel,id){
    if(sel.value == 'Route Map'){
        $('#area_pickup').val('');
        $('#route_link').val('');          
        $('#routePlan').show();
    }else if(sel.value == ''){          
        $('#routePlan').hide();
    }
}
function shareInfo(sel,id){    
    if(sel.value == 'Just My Start Location'){   
        $('#area_pickup').val('');
        $('#route_link').val('');
        $('#vehicle').val('');
        $('#vehicle_model').val('');           
        $('#startLocation').show();
    }else{
        $('#startLocation').hide();
    }    
    if(sel.value == 'Vehical Info'){ 
        $('#area_pickup').val('');            
        $('#vehicle').val('');
        $('#vehicle_model').val('');         
        $('.show-vehicle').show();
    }else if(sel.value == ''){          
        $('.show-vehicle').hide();
    }
}
function chkWay(sel){      
    $('#showDays').hide();
    if(sel.value == 'One Way'){          
        $('#showDays').show(); 
    }       
    if(sel.value == 'Two Way'){                
        $('#showDays').show(); 
    }
}
function resetForm(){    
    $("#car_pulling")[0].reset();
}    
function submitForm() {  
    if($('#want_ride').val() == 'Get Ride'){
        var getdata = check_validation(['want_ride','contact_me','vehical_type','share_with','search_data']);   
    }else{
        var getdata = check_validation(['want_ride','contact_me','vehical_type','vehicle_capacity','vehicle_no','share_with','search_data']);   
    }    
         
    // if($('#search_data').tokenfield('getTokensList') == '' )
    // {     
    // $('.tokenfield').css('border-color', 'red');
    // var getdata = 0;
    // }else{
    // $('.tokenfield').css('border-color', 'green');
    // }
    if(getdata==0){
        //window.scrollTo(0, 370);
        $('#errorMsg').css('color', 'red');
        $('#errorMsg').html('Compulsory Fields Missing');
        return false;
    }
    var username = '<?=auth()->user()->username?>';   
    var bi_form = document.querySelector('#car_pulling');
    var postData = new FormData(bi_form);     
    $.ajax({
            type:'POST',
            url: "{{ url('/api/insert-car-pulling?username=') }}"+username,           
            data : postData,
            processData: false,
            contentType: false,
            headers: {
                Authorization: 'Bearer {{ auth()->user()->api_token }}',
                Accept: 'application/json'
            },            
            success:function(res){
                if (res.status) {  
                    AddNewForm(); 
                    $('#btn').prop('disabled', true);                    
                    $('#successMsg').css('color', 'green');
                    $('#successMsg').html('Ride Generated Successfully');
                    $(".tokenfield").removeAttr("style");                
                    getRequest();     
                    setTimeout(function(){
                        $('#successMsg').html('');   
                        $('#btn').prop('disabled', false);                                        
                    },6000);
                }else{
                   
                    $('#errorMsg').css('color', 'red');
                    $('#errorMsg').html(res.message);
                    setTimeout(function(){
                        $('#errorMsg').html('');   
                        $('#btn').prop('disabled', false);                                        
                    },3000);
                }
            },
            error: function (jqXHR, exception) {
                $('#errorMsg').css('color', 'red');
                $('#errorMsg').html('You Have Already Offered A Ride');
                setTimeout(function(){
                    $('#errorMsg').html('');   
                    $('#btn').prop('disabled', false);                                        
                },3000);   
            },               
    });   
 
}    
function UpdateForm() {  
    if($('#want_ride').val() == 'Get Ride'){
        var getdata = check_validation(['want_ride','contact_me','vehical_type','share_with','search_data']);   
    }else{
        var getdata = check_validation(['want_ride','contact_me','vehical_type','vehicle_capacity','vehicle_no','share_with','search_data']);   
    }               
   // var getdata = check_validation(['want_ride','contact_me','vehical_type','vehicle_capacity','vehicle_no','share_with','search_data']);      
    // if($('#search_data').tokenfield('getTokensList') == '' )
    // {     
    //     $('.tokenfield').css('border-color', 'red');
    //     var getdata = 0;
    // }else{
    //     $('.tokenfield').css('border-color', 'green');
    // }
    if(getdata==0){
        window.scrollTo(0, 370);  
        return false;
    } 
    var username = '<?=auth()->user()->username?>';   
    var bi_form = document.querySelector('#car_pulling');
    var data = new FormData(bi_form);    
    sendPostRequest('update-car-pulling2?username='+username,function (res){   
        if (res.status) {     
            $('#UpdSuccessMsg').css('color', 'green');
            $('#UpdSuccessMsg').html('Ride Updated Successfully');
            $(".tokenfield").removeAttr("style");
            getRequest();     
            setTimeout(function(){
                $('#UpdSuccessMsg').html('');                            
            },3000);
        }else{
            $('#errorUpdMsg').css('color', 'red');
            $('#errorUpdMsg').html(res.message);
            setTimeout(function(){
                $('#errorUpdMsg').html('');   
              //  $('#btn').prop('disabled', false);                                        
            },3000);
        }
    }, data);   
} 
function getRequest(){
    var username = '<?=auth()->user()->username?>';   
    // sendGetRequest('get-car-pulling2?username='+username,function (res){       
    //     console.log('Hello Baby', res.data);            
   
    // }, '');    
    $.ajax({
            type:'GET',
            url: "{{ url('/api/get-car-pulling2?username=') }}"+username,
            headers: {"Authorization": 'Bearer {{ auth()->user()->api_token }}'},     
            //headers:reqHeaders,
            success:function(res){
                if (res.data) {
                    var tbody = '';
                    var div = '';
                    var count = 1;                    
                    $(res.data).each(function(index,row){
                        //     if(row.cp_want_ride=='Offer Ride'){
                        //     $("#show_hide_ride").hide();//('readonly',true);
                        // }    
                        var sr = count++;
                        var link = row.cp_route_link;                                    
                        tbody += '<tr>';
                        tbody += '<td align="center">'+sr+'</td>';                   
                        tbody += '<td>'+row.cp_want_ride+'</td>';
                        tbody += '<td>'+row.cp_contact_me+'</td>';
                        tbody += '<td>'+row.cp_area_pickup+'</td>';
                        tbody += '<td>'+row.cp_vehical_type+'</td>';
                        tbody += '<td>'+row.cp_preferred_location+'</td>';
                        tbody += '<td>'+row.cp_vehical_cc+'</td>';
                        tbody += '<td>'+row.cp_vehical_model+'</td>';
                        tbody += '<td>'+row.cp_capacity+'</td>';    
                        tbody += '<td>'+row.cp_monday+'</td>';
                        tbody += '<td>'+row.cp_tuesday+'</td>';
                        tbody += '<td>'+row.cp_wednesday+'</td>';
                        tbody += '<td>'+row.cp_thursday+'</td>';
                        tbody += '<td>'+row.cp_friday+'</td>';
                        tbody += '<td>'+row.cp_saturday+'</td>';
                        tbody += '<td>'+row.cp_sunday+'</td>';    
                        tbody += '<td>'+row.cp_pay_for_oneside+'</td>';                      
                        tbody += '<td><a target="_blank"  '+((row.cp_route_link == null)?'':'style="color:blue;cursor:pointer" href="'+link+'"')+' >'+((row.cp_route_link == null)?'Route Not Defined':'Route Link')+'</a></td>';
                        tbody += '<td>'+row.cp_entry_username+'</td>';    
                        tbody += '</tr>';                    
                    
                        var monday              = (row.cp_monday == '-')?'Not Available':'<b>'+row.cp_monday+'</b>';                    
                        var tuesday             = (row.cp_tuesday == '-')?'Not Available':'<b>'+row.cp_tuesday+'</b>';
                        var wednesday           = (row.cp_wednesday == '-')?'Not Available':'<b>'+row.cp_wednesday+'</b>'; 
                        var thursday            = (row.cp_thursday == '-')?'Not Available':'<b>'+row.cp_thursday+'</b>'; 
                        var friday              = (row.cp_friday == '-')?'Not Available':'<b>'+row.cp_friday+'</b>'; 
                        var saturday            = (row.cp_saturday == '-')?'Not Available':'<b>'+row.cp_saturday+'</b>'; 
                        var sunday              = (row.cp_sunday == '-')?'Not Available':'<b>'+row.cp_sunday+'</b>'; 
                        var smoking             = (row.cp_smoking == null)?'Not Available':row.cp_smoking;                    
                        var specific_day        = (row.cp_specific_day == null)?'Not Available':row.cp_specific_day;
                        var vehicleCC           = (row.cp_vehical_cc == null)?'Not Available':row.cp_vehical_cc;
                        var vehicleModel        = (row.cp_vehical_model == null)?'Not Available':row.cp_vehical_model;
                        var vehicleCap          = (row.cp_capacity == null)?'Not Available':row.cp_capacity;
                        var routeOption         = (row.cp_route_option == null)?'Not Available':row.cp_route_option;
                        if(row.cp_route_option == 'One Way'){
                            var routeOption = row.cp_route_option;
                        }                                      
                        div += '<div class="card" style="margin-top:10px;margin-right: 20px !important;width:99%;border-radius:10px">';
                        div += '<div class="card-header" style="background-color:#2C3E50 !important;color:white !important;border-top-right-radius:10px;border-top-left-radius:10px;display:inline">';
                        div += '<div style="float:left">';
                        div += '<h3 class="card-title">Ride Details <span style="">('+row.cp_entry_datetime+')</span></h3>';
                        div += '</div>';
                        div += '<div style="float:right">';
                        div += '<button type="button" onclick="edit_carPull('+row.cp_id+')"><i class="fa fa-edit"></i></button>';
                        div += '</div>';
                        div += '</div>';
                        div += '<div class="card-body">';                   
                        div += '<div class="row">';
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>To Ride </b></label><br>';
                        div +=  row.cp_want_ride;                    
                        div += '</div>';
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Contact Me </b></label><br>';
                        div +=  row.cp_contact_det;                    
                        div += '</div>';
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Share With </b></label><br>';
                        div +=  row.cp_share_with;                    
                        div += '</div>'; 
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Vehicle Type </b></label><br>';
                        div +=  row.cp_vehical_type;                    
                        div += '</div>';                       
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Model </b></label><br>';
                        div +=  vehicleModel;                    
                        div += '</div>';     
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Engine Capacity </b></label><br>';
                        div +=  vehicleCC;                    
                        div += '</div>';
                        div += '</div>';
                        div += '<div class="row">';   
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Vehicle No </b></label><br>';
                        div +=  row.cp_vehical_no;                    
                        div += '</div>';                      
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Capacity </b></label><br>';
                        div +=  vehicleCap;                    
                        div += '</div>';   
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Route Option </b></label><br>';
                        div +=  routeOption;                    
                        div += '</div>';  
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Route Map </b></label><br>';
                        div +=  '<a target="_blank"  '+((row.cp_route_link == null)?'':'style="color:blue;cursor:pointer" href="'+link+'"')+' >'+((row.cp_route_link == null)?'Route Not Defined':'Route Link')+'</a>';                    
                        div += '</div>';  
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Preferred Location </b></label><br>';
                        div +=  row.cp_preferred_location;                    
                        div += '</div>';
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:cornflowerblue" ><b>Smoking while Driving </b></label><br>';
                        div +=  smoking;                    
                        div += '</div>';                                
                        div += '</div>';   
                        div += '<div class="row">';    
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:brown" ><b>Monday </b></label><br>';
                        div +=  monday;                    
                        div += '</div>';       
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:yellowgreen" ><b>Tuesday </b></label><br>';
                        div +=  tuesday;                    
                        div += '</div>';  
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:tomato" ><b>Wednesday </b></label><br>';
                        div +=  wednesday;                    
                        div += '</div>';  
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:teal" ><b>Thursday </b></label><br>';
                        div +=  thursday;                    
                        div += '</div>';  
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:sienna" ><b>Friday </b></label><br>';
                        div +=  friday;                    
                        div += '</div>';  
                        div += '<div class="col-6 col-sm-6 col-md-2  col-lg-2 form-group "> ';
                        div += '<label class="col-form-label" style="color:seagreen" ><b>Saturday </b></label><br>';
                        div +=  saturday;                    
                        div += '</div>';                          
                        div += '</div>';
                        div += '</div>';
                        div += '</div>';
                    });
                    if(tbody == ''){
                        tbody = '<tr><td colspan="4"> No Car Pulling Request Found Yet! </td></tr>';
                    }  
                    $('#cardLoop').html(div);                    
                }   
            },
            error: function (jqXHR, exception) {
                // alert('bilal saleem')   
            },               
    });
  
}
function edit_carPull(id){  
    $('#updtBtn').show();
    $('#submitBtn').hide();
    $('#cp_id').val(id);
    window.scrollTo(0, 370);
    sendGetRequest('edit-car-pulling?cp_id='+id,function (res){             
        if (res.data){                   
            if(res.data[0].cp_monday.charAt(0) == '-'){
                var mondayMorningTime  = res.data[0].cp_monday.substring(0, 5);
                var mondayBackTime     = res.data[0].cp_monday.substring(1,6);
            }else{
                var mondayMorningTime  = res.data[0].cp_monday.substring(0, 5);
                var mondayBackTime     = res.data[0].cp_monday.substring(6,11);
            }
            if(res.data[0].cp_tuesday.charAt(0) == '-'){
                var tuesdayMorningTime = res.data[0].cp_tuesday.substring(0, 5);
                var tuesdayBackTime    = res.data[0].cp_tuesday.substring(1,6);
            }else{
                var tuesdayMorningTime = res.data[0].cp_tuesday.substring(0, 5);
                var tuesdayBackTime    = res.data[0].cp_tuesday.substring(6,11);
            }
            if(res.data[0].cp_wednesday.charAt(0) == '-'){
                var wednesdayMorningTime = res.data[0].cp_wednesday.substring(0, 5);
                var wednesdayBackTime    = res.data[0].cp_wednesday.substring(1,6);
            }else{
                var wednesdayMorningTime = res.data[0].cp_wednesday.substring(0, 5);
                var wednesdayBackTime    = res.data[0].cp_wednesday.substring(6,11);
            }             
            if(res.data[0].cp_thursday.charAt(0) == '-'){
                var thursdayMorningTime  = res.data[0].cp_thursday.substring(0, 5);
                var thursdayBackTime     = res.data[0].cp_thursday.substring(1,6);
            }else{
                var thursdayMorningTime  = res.data[0].cp_thursday.substring(0, 5);
                var thursdayBackTime     = res.data[0].cp_thursday.substring(6,11);
            }
            if(res.data[0].cp_friday.charAt(0) == '-'){
                var fridayMorningTime = res.data[0].cp_friday.substring(0, 5);
                var fridayBackTime    = res.data[0].cp_friday.substring(1,6);
            }else{
                var fridayMorningTime = res.data[0].cp_friday.substring(0, 5);
                var fridayBackTime    = res.data[0].cp_friday.substring(6,11);
            }
            if(res.data[0].cp_saturday.charAt(0) == '-'){
                var saturdayMorningTime = res.data[0].cp_saturday.substring(0, 5);
                var saturdayBackTime    = res.data[0].cp_saturday.substring(1,6);
            }else{
                var saturdayMorningTime = res.data[0].cp_saturday.substring(0, 5);
                var saturdayBackTime    = res.data[0].cp_saturday.substring(6,11);
            }
            if(res.data[0].cp_sunday.charAt(0) == '-'){
                var sundayMorningTime = res.data[0].cp_sunday.substring(0, 5);
                var sundayBackTime    = res.data[0].cp_sunday.substring(1,6);
            }else{
                var sundayMorningTime = res.data[0].cp_sunday.substring(0, 5);
                var sundayBackTime    = res.data[0].cp_sunday.substring(6,11);
            }                
            $('#want_ride').val(res.data[0].cp_want_ride);
            $('#contact_me').val(res.data[0].cp_contact_me);
            $('#contact_detail').val(res.data[0].cp_contact_det);
            $('#vehical_type').val(res.data[0].cp_vehical_type);
            $('#share_with').val(res.data[0].cp_share_with);
            $('#search_data').val(res.data[0].cp_preferred_location);
            $('#share_info').val(res.data[0].cp_further_information);
            $('#route_share_info').val(res.data[0].route_information);
            $('#pay_for_one_side').val(res.data[0].cp_route_option);    
            $('#uni_timings').val(res.data[0].cp_timings);    
            $('#vehicle_capacity').val(res.data[0].cp_capacity);       
            $('#route_link').val(res.data[0].cp_route_link);   
            $('#smoking_info').val(res.data[0].cp_smoking); 
            $('#vehicle_no').val(res.data[0].cp_vehical_no);
            $('#search_data').tokenfield('setTokens', res.data[0].cp_preferred_location);    
            if(res.data[0].cp_want_ride == 'Get Ride'){                
                $('.vehicle_cap').hide();
            }else{
                $('.vehicle_cap').show();
            }
            if(res.data[0].cp_route_option == 'One Way'){
                $('#specific_day').hide();
                // $('#time_div').show();
                $('#time').val(res.data[0].cp_pay_for_oneside);                   
            } 
            if(res.data[0].cp_further_information == 'Just My Start Location'){
                $('#startLocation').show();
                $('#area_pickup').val(res.data[0].cp_area_pickup);
                $('#routePlan').hide();              
                $('.show-vehicle').hide();
                $('#vehicle').val('');
                $('#vehicle_model').val('');                 
            }
            if(res.data[0].cp_further_information == 'Route Map'){
                $('#routePlan').show();                 
                $('#startLocation').hide();
                $('#area_pickup').val('');                
                $('.show-vehicle').hide();
                $('#vehicle').val('');
                $('#vehicle_model').val('');              
            }
            if(res.data[0].cp_further_information == 'Vehical Info'){
                $('.show-vehicle').show();
                $('#vehicle').val(res.data[0].cp_vehical_cc);
                $('#vehicle_model').val(res.data[0].cp_vehical_model);                    
                $('#routePlan').show();                 
                $('#startLocation').hide();
                $('#area_pickup').val('');
            }
            if(res.data[0].cp_route_option == 'One Way' || res.data[0].cp_route_option == 'Two Way'){    
                $('#showDays').show();               
                $('#Monday_morning_time').val(mondayMorningTime);
                $('#Monday_back_time').val(mondayBackTime);
                $('#Tuesday_morning_time').val(tuesdayMorningTime);
                $('#Tuesday_back_time').val(tuesdayBackTime);
                $('#Wednesday_morning_time').val(wednesdayMorningTime);
                $('#Wednesday_back_time').val(wednesdayBackTime);
                $('#Thursday_morning_time').val(thursdayMorningTime);
                $('#Thursday_back_time').val(thursdayBackTime);
                $('#Friday_morning_time').val(fridayMorningTime);
                $('#Friday_back_time').val(fridayBackTime);
                $('#Saturday_morning_time').val(saturdayMorningTime);
                $('#Saturday_back_time').val(saturdayBackTime);
                $('#Sunday_morning_time').val(sundayMorningTime);
                $('#Sunday_back_time').val(sundayBackTime);
            }else{
                $('#showDays').hide();  
                $('#Monday_morning_time').val('');
                $('#Monday_back_time').val('');
                $('#Tuesday_morning_time').val('');
                $('#Tuesday_back_time').val('');
                $('#Wednesday_morning_time').val('');
                $('#Wednesday_back_time').val('');
                $('#Thursday_morning_time').val('');
                $('#Thursday_back_time').val('');
                $('#Friday_morning_time').val('');
                $('#Friday_back_time').val('');
                $('#Saturday_morning_time').val('');
                $('#Saturday_back_time').val('');
                $('#Sunday_morning_time').val('');
                $('#Sunday_back_time').val('');
            }     
        }else{
        
        }
    }, '');
}
</script>
@endsection

