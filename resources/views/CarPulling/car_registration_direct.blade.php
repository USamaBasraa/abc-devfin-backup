
@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<style>
div.dataTables_wrapper div.dataTables_filter input {
    width: 450px;
    height: 40px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}
/* div{
    border:solid 1px black;
} */

.invisible { display: none; }
</style>



<input type="hidden" name="var_reg_no" id="var_reg_no">
<div class="testif"></div>
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group"> 
        <div class="card">
            <div class="card-header" style="display:inline;background-color:#4454c3;color: white;">
                <div style="float:left">
                    <h3 class="card-title" style="padding-top:10px">University Pool /  Visitor Car Registration</h3>  
                </div>               
            </div>
            <div class="card-body">
                <form id="DirectcarRegistration" method="post" onsubmit="event.preventDefault(); autoSearch(this);">    
                    <input type="hidden" class="form-control" name ="vr_id" id ="vr_id">  
                    <div class="row">                        
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group"> 
                            <label class="col-form-label" ><b>Username</b></label>                                
                            <input type="text" class="form-control" name ="vr_username" id ="vr_username" placeholder="Username">                                         
                        </div>     
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group"> 
                            <label class="col-form-label"  ><b>Vehicle No</b></label>       
                            <input type="text" class="form-control" style="text-transform: uppercase;" name ="vr_vehicle_no" id ="vr_vehicle_no" placeholder="ABC-00-0000">                                         
                        </div>              
                    </div>        
                    <div class="row">                        
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group"> 
                            <label class="col-form-label" ><b>Mobile No</b></label>       
                            <input type="text" class="form-control" name ="vr_mobile" id ="vr_mobile" placeholder="923100000000">                                         
                        </div>     
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group"> 
                            <label class="col-form-label" ><b>Sticker No</b><span style="color:red">&nbsp</span></label> 
                            
                            <select name ="vr_sticker" id ="vr_sticker" namespace="vr_mobile" class="form-control">
                                <option value="F<?=$rec[0]->faculty_count;?>">Faculty Token : <?=$rec[0]->faculty_count;?></option>
                                <option value="S<?=$rec[0]->student_count;?>">Student Token : <?=$rec[0]->student_count;?></option>
                            </select>

<!--                        

                            <input type="text" class="form-control" name ="vr_sticker" id ="vr_sticker" placeholder="Sticker #" value="F<?=(count($rec)+1);?>">                                          -->
                        </div>              
                    </div>         
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6  col-lg-6 form-group ">
                            <label class="col-form-label" ><b>Parking Status</b></label>           
                            <select  class="form-control"  name="vr_parking_status" id="vr_parking_status" >
                                <option value="">--Please Select Parking Options--</option>                
                                <option value="University Parking">University Parking</option>
                                <option value="Chilli Factory">Chilli Factory</option>                
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
                    <!-- <div class="row">
                        
                        <div class="col-12 col-sm-12 col-md-11 col-lg-11">                
                            <input class=" form-control" type="text" name="sch_enrollment_no"  id="sai_enrollment_no"  autocomplete="off" name="username" placeholder="Search By Username" value="<?php echo (isset($roll_no)) ? $roll_no : '';?>" >  
                        </div>
                        <div class="col-12 col-sm-12 col-md-1 col-lg-1">   
                            <button type="button" class="btn btn-success" onclick="getProfile()"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                    </div>           -->
                </form>
                <table class="table table-striped" width="100%" id="tbl_exporttable_to_xls">
                    <thead>        
                        <tr>
                            <th colspan="9"></th>          
                        <tr>
                            <th style="text-align:center;" scope="col"><b>Sr.No</b></th>
                            <th style="text-align:center;" scope="col"><b>Username</b></th>
                            <th style="text-align:center;" scope="col"><b>Vehicle</b></th>
                            <th style="text-align:center;" scope="col"><b>Mobile</b></th>
                            <th style="text-align:center;" scope="col"><b>Sticker</b></th>
                            <th style="text-align:center;" scope="col"><b>Parking Status</b></th>
                            <th style="text-align:center;" scope="col"><b>Date Time</b> </th>
                            <th style="text-align:center;" scope="col"><b>Action</b> </th>
                        </tr>
                    </thead>
                    <tbody id="result-body" >
                       
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop
@section('javascript')
<script src="<?=asset('js/bootstrap-autocomplete.js')?>"></script>
<script type="text/javascript">
$(document).ready(function() {
    getData();
});

function submitForm() {   
    
    // var getdata = check_validation(['vr_username','vr_vehicle_no','vr_mobile','vr_parking_status']);   
    // if(getdata==0){      
    //     return false;
    // } 
    var bi_form = document.querySelector('#DirectcarRegistration');
    var fd = new FormData(bi_form);   
    $.ajax({
        method: "POST",
        url: '{{'/api/save-direct-car_registration'}}',
        data: fd,
        processData: false,
        contentType: false,
        headers: {
            Authorization: 'Bearer <?= auth()->user()->api_token ?>',
            Accept: 'application/json'
        }
    }).done(function(res) {
        if(res.message == 1){
            $('#successMsg').css('color', 'green');
            $('#successMsg').html('Car Registered Successfully'); 
            // $("#vehicle_token").hide();   
            getData();   
            setTimeout(function(){
                $('#successMsg').html('');
                //  $("#DirectcarRegistration")[0].reset();
                 $('#vr_username').val('');
                 $('#vr_vehicle_no').val('');
                 
                 location.reload();                
            },1000);
        }
    }).fail(function(res) {
        $('#errorMsg').css('color', 'red');
        $('#errorMsg').html('User Car has already Registered');
        setTimeout(function(){
            $('#errorMsg').html('');   
            //$('#btn').prop('disabled', false);                                        
        },5000);   
    });                

} 
function getData(){
    $.ajax({
        type:'GET',
        url: "{{ url('/api/get-direct-car-registration') }}",
        headers: {"Authorization": 'Bearer {{ auth()->user()->api_token }}'},     
        //headers:reqHeaders,
        success:function(res){
            if (res.data) {
                var tbody = '';
                var count = 1;                                 
                $(res.data).each(function(index,row){    
                    var sr = count++;
                    // alert(row.vr_vehicle_token);
                    // $('#show_sticker_data').append('<div style="text-align:center">'+row.vr_vehicle_token+'</div>');  
                    tbody += '<tr>';
                    tbody += '<td style="text-align:center">'+sr+'</td>';
                    tbody += '<td style="text-align:center">'+row.vr_username+'</td>';
                    tbody += '<td style="text-align:center">'+row.vr_vehicle_no+'</td>';
                    tbody += '<td style="text-align:center">'+row.vr_mobile+'</td>';
                    tbody += '<td style="text-align:center">'+row.vr_vehicle_token+'</td>';
                    tbody += '<td style="text-align:center">'+row.vr_parking+'</td>';
                    tbody += '<td style="text-align:center">'+row.vr_entry_datetime+'</td>';   
                    tbody += '<td style="text-align:center"><button type="button" onclick="editDirectRegister('+row.vr_id+')" class="btn btn-primary">Edit</button></td>';                       
                    tbody += '</tr>';                      
                });
                if(tbody == ''){
                    tbody = '<tr><td colspan="4"> No Car Registered Yet! </td></tr>';
                }  
                $('#result-body').html(tbody);   
                
            }   
        },
        error: function (jqXHR, exception) {
            // alert('bilal saleem')   
        },               
    });  

}
function editDirectRegister(id){
    
    $.ajax({
        type:'GET',
        url: "{{ url('/api/edit-direct-car-registration?vr_id=')}}"+id,
        headers: {"Authorization": 'Bearer {{ auth()->user()->api_token }}'},     
        //headers:reqHeaders,
        success:function(res){
            if (res.data) {
                $('#vr_username').val(res.data[0]['vr_username']);
                $('#vr_vehicle_no').val(res.data[0]['vr_vehicle_no']);
                $('#vr_mobile').val(res.data[0]['vr_mobile']);
                $('#vr_sticker').val(res.data[0]['vr_vehicle_token']);
                $('#vr_parking_status').val(res.data[0]['vr_parking']);
                $('#vr_id').val(res.data[0]['vr_id']);                  
                $('#submitBtn').hide();
                $('#updtBtn').show();
            }   
        },
        error: function (jqXHR, exception) {
            // alert('bilal saleem')   
        },               
    });  
}
function AddNewForm(){
    $('#submitBtn').show();
    $('#updtBtn').hide();
    $("#DirectcarRegistration")[0].reset();
}
function UpdateForm() {  
    // var getdata = check_validation(['vr_username','vr_vehicle_no','vr_mobile','vr_parking_status']);   
    // if(getdata==0){      
    //     return false;
    // }    
    var bi_form = document.querySelector('#DirectcarRegistration');
    var data = new FormData(bi_form);    
    var fd = new FormData(bi_form);   
    var id = $('#vr_id').val();
    $.ajax({
        method: "POST",
        url: '{{'/api/update-direct-car_registration?vr_id='}}'+id,
        data: fd,
        processData: false,
        contentType: false,
        headers: {
            Authorization: 'Bearer <?= auth()->user()->api_token ?>',
            Accept: 'application/json'
        }
    }).done(function(res) {
        if(res.message == 1){
            $('#successMsg').css('color', 'green');
            $('#successMsg').html('Car Registered Updated Successfully'); 
            // $("#vehicle_token").hide();   
            getData();   
            setTimeout(function(){
                $('#successMsg').html('');
                //  $("#DirectcarRegistration")[0].reset();
                // location.reload();                
            },1000);
        }
    }).fail(function(res) {
      
    }); 
} 
</script>
@stop

