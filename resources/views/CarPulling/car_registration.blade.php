
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
                    <h3 class="card-title" style="padding-top:10px">Car Registration (<?=$academic_session[0]->sess_name?>)</h3>  
                </div> 
                <div style="float:right">
                    <button type="button"  class="btn btn-success" onclick="window.location.href='{{ url('/direct-registered-car') }}'">Direct Registration</button>
                    <button type="button"  class="btn btn-warning" onclick="window.location.href='{{ url('/car_pooling_report') }}'">Report</button>
                </div> 
            </div>
            <div class="card-body">
                <form id="carRegistration" method="post" onsubmit="event.preventDefault(); autoSearch(this);"> 
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12  col-lg-12 form-group " id="vr_parking">
                            <label class="col-form-label" ><b>Parking Status</b></label>           
                            <select  class="form-control"  name="parking_status" id="parking_status" >                                             
                                <option value="University Parking">University Parking</option>
                                <option value="Paid Parking">Paid Parking</option>                
                            </select>   
                        </div> 
                    </div>
                    <div class="row">                           
                        <div class="col-12 col-sm-12 col-md-11 col-lg-11 form-group"> 
                            <label class="col-form-label" ><b>Campus ID / Vehicle No</b></label>                  
                           
                            <input class=" form-control" type="text" name="sch_enrollment_no"   id="sai_enrollment_no"  autocomplete="off" name="username" placeholder="Search By Username" value="<?php echo (isset($roll_no)) ? $roll_no : '';?>" >  
                        </div>
                        <div class="col-12 col-sm-12 col-md-1 col-lg-1 form-group" style="padding-top:35px">                            
                            <button type="submit"  class="btn btn-success" onclick="getProfile()"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <div class="row" style="display:none" id="showProfile">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group" style="display:inline"> 
                            <div class="col-6 col-sm-6 col-md-6 col-lg-6 form-group" style="float:left;text-align:left">
                                <label class="col-form-label" ><span id="group_id"></span></label>     <br>  
                                <label class="col-form-label" ><span id="name"></span></label>     <br>  
                                <label class="col-form-label" ><span id="school_name"></span></label>       <br>
                                <label class="col-form-label" ><span id="dept_name"></span></label>         <br>    
                                <label class="col-form-label" ><span id="mobile"></span></label>     <br>
                                <label class="col-form-label" ><span id="vehicle"></span></label>   <br>
                                <label class="col-form-label" ><span id="share_with"></span></label>   <br>
                                <label class="col-form-label" ><span id="vehicle_reg_no"></span></label><br>
                                <label class="col-form-label" ><span id="token_status"></span></label>                                 
                            </div>
                             <div class="col-6 col-sm-6 col-md-6 col-lg-6 form-group" style="float:left">  
                                <span id="image"></span>
                            </div>                            
                        </div>                            
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group" style="display:inline"> 
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group" style="float:left;text-align:left">                                
                                <div text-align="center" id="partner"></div>                               
                            </div> 
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group" style="float:left;text-align:left" id="token_record_div" style="display:none">
                                    <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                <tr style="background-color:gray;">
                                                    <th scope="col" colspan="4"><b>Sticker Details<b></th>
                                                </tr>
                                                </thead>
                                                <thead>
                                                <tr>
                                                    <th scope="col" style="text-align:center"><b>#</b></th>
                                                    <th scope="col"><b>Sticker</b></th>
                                                    <th scope="col"><b>Vehicle #</b></th>
                                                    <th scope="col"><b>Parking</b></th>
                                                    <th scope="col"><b>Action</b></th>
                                                </tr>
                                                </thead>
                                                <tbody id="token_record">
                                                </tbody>
                                            </table>   
                                    </div>
                            </div>    
                        </div> 
                        <div id="show_sticker_data">
                        </div>       
                    </div>                   
                    @if ($page_action == 'issue_token')          
                            <div class="row" style="display:none" id="showVehicleToken">                        
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 form-group"> 
                                    <label class="col-form-label" ><b>Vehicle Sticker : </b></label>       
                                    <input type="text" class="form-control" name ="vehicle_token" id ="vehicle_token">
                                    <label class="col-form-label" ><b>Vehicle No : </b></label>  
                                    <input type="text" class="form-control" name ="vehicle_no" id ="vehicle_no">
                                    <input type="hidden" class="form-control" value="<?=$academic_session[0]->sess_name?>" name ="academic_session" id ="academic_session">
                                                           
                                </div>                
                            </div>              
                            <div class="row" style="margin-top:20px;display:none" id="btnShowHide" >                                   
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group">                                    
                                </div>
                                <div style="text-align:center;" class="col-12 col-sm-12 col-md-12 col-lg-12 form-group">
                                    <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>                            
                                </div>
                                <span id="successMsg"></span>
                                <span id="errorMsg"></span>
                            </div>
                    @endif     
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row" style="display:none">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group"> 
        <div class="card">
            <div class="card-header">
                <div class="card-title">Registered Campus ID</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-nowrap" id="example2">
                        <thead>
                            <tr>
                                <th class="wd-15p border-bottom-0" style="background-color:#4454c3;color: white;">Sr #</th>
                                <th class="wd-15p border-bottom-0" style="background-color:#4454c3;color: white;">Session</th>
                                <th class="wd-20p border-bottom-0" style="background-color:#4454c3;color: white;">Campus ID</th>
                                <th class="wd-15p border-bottom-0" style="background-color:#4454c3;color: white;">Vehicle</th>
                                <th class="wd-10p border-bottom-0" style="background-color:#4454c3;color: white;">Sticker</th>
                                <th class="wd-25p border-bottom-0" style="background-color:#4454c3;color: white;">Issued</th>
                                <th class="wd-25p border-bottom-0" style="background-color:#4454c3;color: white;">Status</th>
                                <th class="wd-10p border-bottom-0" style="background-color:#4454c3;color: white;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $count = 1;
                            foreach($car_registration as $row){?>
                            <tr>
                                <td><?=$count++?></td>
                                <td><?=$row->vr_sess_id?></td>
                                <td><?=$row->vr_username?></td>
                                <td><?=$row->vr_vehicle_no?></td>
                                <td><?=$row->vr_vehicle_token?></td>
                                <td><?=$row->vr_entry_datetime?></td>
                                <td><?=($row->vr_status == 'E')?'Issued':'Cancelled';?></td>
                                <td><?=$row->vr_id?><button type="button" onclick="setVrID('<?=$row->vr_id?>')" class="btn btn-danger" data-toggle="modal" data-target="#default-Modal" >Cancel</button></td>
                            </tr>													
                            <?php } ?>	
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="default-Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Car Token Cancel Reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <input type="hidden" class="form-control" name ="vr_id" id ="vr_id">  
                        <div class="card-block dt-responsive">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group"> 
                                    <label class="col-form-label" ><b>Reason</b></label>  
                                </div>
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group">                                    
                                    <input type="text" class="form-control" name ="cancel_reason" id ="cancel_reason">                                  
                                </div>
                            </div>                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="deleteBtn()">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="default-Modal2" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Car Parking Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <input type="hidden" class="form-control" name ="vr_parking_id" id ="vr_parking_id">  
                        <div class="card-block dt-responsive">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12  col-lg-12 form-group">
                                    <label class="col-form-label" ><b>Parking Status</b></label>           
                                    <select  class="form-control"  name="parking_status_edit" id="parking_status_edit" >
                                        <option value="">--Please Select Parking Options--</option>                
                                        <option value="University Parking">University Parking</option>
                                        <option value="Paid Parking">Paid Parking</option>                
                                    </select>   
                                </div> 
                            </div>                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="editParkingBtn()">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


@stop
@section('javascript')
<script src="<?=asset('js/bootstrap-autocomplete.js')?>"></script>
<script type="text/javascript">

function setVrID(id){
    $('#vr_id').val(id);
}
function editParkingStatus(id){
    $('#vr_parking_id').val(id);
}
function display_token(token_data){
    $('#token_record').html('');
   
    //alert(token_data.length)
    let count_enable_token = 0;
    $.each(token_data, function(index, value) {
        var button           = (value.vr_status == 'E')?'<button type="button" onclick="editParkingStatus('+value.vr_id+')" class="btn btn-info" data-toggle="modal" data-target="#default-Modal2" ><i class="fa fa-edit"></i></button>':''; 
        $('#token_record_div').show();
        $('#token_record').append('<tr>');
        $('#token_record').append('<td scope="row" style="padding:4px;" align="center">'+(index+1)+'</td>');
        $('#token_record').append('<td scope="row" style="padding:4px;">'+value.vr_vehicle_token+'</td>');
        $('#token_record').append('<td scope="row" style="padding:4px;">'+value.vr_vehicle_no+'</td>');
        $('#token_record').append('<td scope="row" style="padding:4px;">'+value.vr_parking+' &nbsp &nbsp '+button+'</td>');
        var vr_status = value.vr_status;
        if (vr_status === 'E') {
            count_enable_token++;
            $('#token_record').append('<td scope="row" style="padding:4px;"><button type="button" onclick="setVrID('+value.vr_id+')" class="btn btn-danger" data-toggle="modal" data-target="#default-Modal" >Cancel</button></td>');
        } else {
            $('#token_record').append('<td scope="row" style="padding:4px;">Cancelled</td>');
        }
       // alert(count_enable_token);
        if(count_enable_token>1){
            $('#showVehicleToken').hide();
            $('#btnShowHide').hide();      
        }else{
            $('#showVehicleToken').show();
            $('#btnShowHide').show();
        }
        $('#token_record').append('</tr>');
    });
   // $('#token_record').append('</table>');
}
let lastPartner = '';
let lastEntryUser = '';
function getProfile(){   
    $('#showProfile').show(); 
    $('#vr_parking').show(); 
    $('#showVehicleToken').hide();
    $('#btnShowHide').hide();

    if($('#sai_enrollment_no').val().toUpperCase()!=''){
        var rol_no = $('#sai_enrollment_no').val().toUpperCase(); 
        $("#var_reg_no").val(rol_no);  
        $("#vehicle_token").show();        
    }else{
        var rol_no = $('#var_reg_no').val().toUpperCase();
    }  
    // alert($('#var_reg_no').val().toUpperCase()+"__"+$('#sai_enrollment_no').val().toUpperCase());

    // if you want to show alert msg at the top write this (.message_div) as second parem in sendGetRequest
    let apiUrl        = 'api/get-student-profile_cr';

    let data={
        rol_no:rol_no,
    }   
    $.ajax({
        url: apiUrl,
        type: "POST",
        data: data,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
        },
        success: function (res) {
            if(res.message == 1){           
            var tbody = '';
            var ccount = 1;
            $(res.data).each(function(index,row){                 
                // alert('ok');
                var group = '';
                // var image_id = res.data.sai_enrollment_no;
                if(row.group_id == '4'){
                    var group = 'Student';
                }else if(row.group_id == '222' || row.group_id == '222'){
                    var group = 'Faculty';
                }else{
                    var group = 'Staff';
                }
                // var shareWith = (row.cp_share_with === 'Nobody') ? 'Nobody' :'Yes';
                // var shareWith = (row.cp_share_with === 'Nobody') ? 'Nobody' :'Yes';
                if(row.cp_partner == null  && res.sub_data2.length>0){
                    var shareWith = 'Shared';
                }else if(row.cp_partner != null){
                    var shareWith = 'Shared';
                }else if(row.cp_partner == null && res.sub_data2.length==0){
                    var shareWith = 'Sharing Offered';
                }else{
                    var shareWith = 'Yes';
                }
                var vehicleNO  = (row.cp_vehical_no == null)?'N/A':row.cp_vehical_no;
                $('#group_id').html('<b>Campus Status: </b>'+group);  
                $('#name').html('<b>Name: </b>'+row.spi_name);  
                $('#school_name').html('<b>School: </b>'+row.school_name); 
                $('#dept_name').html('<b>Department: </b>'+row.dept_name); 
                $('#mobile').html('<b>Mobile: </b>'+row.spi_mobile);
                $('#parking_status').val(row.vr_parking);
                if(vehicleNO!='N/A'){
                    $('#vehicle').html('<b>Vehicle: </b>'+vehicleNO); 
                }
                $('#share_with').html('<b>Share With: </b>'+shareWith);                 
                if(row.vr_status != null){                   
                    if(row.vr_status == 'E'){
                        $('#vehicle_reg_no').html('<b>Sticker: </b>'+row.vr_vehicle_token); 
                        $('#token_status').html('<b>Status: </b> Issued'); 
                    }
                }else{
                    $('#vehicle_reg_no').html('');     
                    $('#token_status').html(''); 
                }
                
                if(row.cp_vehical_no!="null"){
                    $('#vehicle_no').val(row.cp_vehical_no);  
                   // alert("a"+row.cp_vehical_no);
                }
                if(row.cp_vehical_no != null){ 
                    // alert('456');    
                    // $('#showVehicleToken').show();
                    //$('#btnShowHide').show();
                }
                if(row.vr_status == 'E'){                    
                    //$('#showVehicleToken').hide();
                    //$('#btnShowHide').hide();            
                }  

                
                if(ccount==1){                    
                    if(row.group_id == 4){
                        tbody += '<img src="https://cms.bnu.edu.pk/students_pics/'+row.sai_enrollment_no+'.jpg" alt="No Image Found" width="120" height="168">';  
                    }else{
                        tbody += '<img src="https://cms.bnu.edu.pk/assets/staffimages/'+row.rn+'.jpg" alt="No Image Found" width="120" height="168">';  
                    }                    
                    ccount = ccount + 1;
                }                                            
            });  
            //alert(res.rec_car_token);   
             $('#showVehicleToken').show();
             $('#btnShowHide').show();       
            display_token(res.rec_car_token);
            $('#image').html(tbody);
            $('#partner').html('');  
                $('#partner').append('<div style="background-color:#FF9F83;padding:4px"><h3> Sharing With </h3></div>'); 
                var print_turn = 1;
                $(res.resCarPooling).each(function(index,row){   

                    const partners = row.cp_partner.split('~').map(p => p.trim().toLowerCase());
                    const entryUser = row.cp_entry_username.trim().toLowerCase();

                    // find the one that is NOT equal to entryUser
                    const otherPartner = partners.find(p => p !== entryUser);
                    // get original casing (optional, if needed)
                    const originalPartner = row.cp_partner
                    .split('~')
                    .map(p => p.trim())
                    .find(p => p.toLowerCase() === otherPartner);

                    
                    //if(print_turn==1)
                    //{                        
                    var part = row.cp_partner.toUpperCase();   
                    // alert(part);
                    var abc = part.replace(rol_no, "").replace('~', "");
                    var text = abc.replace('~', "");
                    // alert(text);
                    $('#partner').append('<div><b> '+row.cp_partner.toUpperCase()+'</div>'); 
                    // $('#partner').append('<div><b> '+text+'</div>'); 

                    lastPartner = originalPartner;
                    lastEntryUser = row.cp_entry_username;
                    print_turn = print_turn +1; 
                        
                    // }               
                });
                $('#partner').append(`
                    <div class="text-center mt-2">
                        <button id="deleteCarPoolingBtn" class="btn btn-danger btn-sm">
                            Delete Car Pooling
                        </button>
                    </div>
                `);
                var totalCarMember = res.sub_data2.length+1;
                    //alert(res.resCarPooling.length);
                if(totalCarMember == '1'){
                    $('#partner').append('<div><b>No Partner</b></div>');  
                }else{
                    $('#partner').append('<div><b> Total Members: </b>'+totalCarMember+'</div>');      
                }    
        }      
        },
        error: function (xhr, status, error) {
            if (typeof errorCallback === 'function') {
                errorCallback(xhr, status, error);
            }
        }
    });

//     $.ajax({
//     url: "https://example.com/api/data",
//     type: "GET",
//     dataType: "json",
//     success: function(response) {
//         $.each(response, function(index, data) {
//         var record = "<div class='record'><p>" + data.name + "</p><button class='cancel'>Cancel</button></div>";
//         $("div#records").append(record);
//         });
//     }
//   });
   
}  

$(document).on('click', '#deleteCarPoolingBtn', function () {
    if (!confirm('Are you sure you want to delete this car pooling record?')) {
        return;
    }

    $.ajax({
        url: "{{ route('deleteCarPooling') }}",
        type: "POST",
        data: {
            cp_partner: lastPartner,
            cp_entry_username: lastEntryUser,
            _token: "{{ csrf_token() }}"
        },
        beforeSend: function () {
            $('#deleteCarPoolingBtn').prop('disabled', true).text('Deleting...');
        },
        success: function (res) {
            alert('Car pooling deleted successfully.');
            $('#partner').html('<div><b>Deleted Successfully</b></div>');
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert('Error deleting car pooling.');
            $('#deleteCarPoolingBtn').prop('disabled', false).text('Delete Car Pooling');
        }
    });
});



function editParkingBtn(){ 
    
   var id  = $('#vr_parking_id').val();
   var parking_status  = $('#parking_status_edit').val();
   //var cancel_reason  = $('#cancel_reason').val();
   //alert(id);alert('s');   
   $.ajax({
       method: "POST",
       url: '{{'/api/upd-car-registration-parking?vr_id='}}'+id+'&parking_status='+parking_status,       
       processData: false,
       contentType: false,
       headers: {
           Authorization: 'Bearer <?= auth()->user()->api_token ?>',
           Accept: 'application/json'
       }
   }).done(function(res) {
       if(res.message == 1){
           getProfile();
           $("#default-Modal2").modal('hide');          
       }
   }).fail(function(res) {
   
   });

}
function deleteBtn(){ 
   
    var id  = $('#vr_id').val();
    var cancel_reason  = $('#cancel_reason').val();
    //alert(id);alert('s');   
    $.ajax({
        method: "POST",
        url: '{{'/api/del-car-registration?vr_id='}}'+id+'&reason='+cancel_reason,
        // data: fd,
        processData: false,
        contentType: false,
        headers: {
            Authorization: 'Bearer <?= auth()->user()->api_token ?>',
            Accept: 'application/json'
        }
    }).done(function(res) {
        if(res.message == 1){
            getProfile();
            $("#default-Modal").modal('hide');
           // location.reload();  
            // $('#successMsg').css('color', 'green');
            // $('#successMsg').html('User Deleted Successfully');       
            // setTimeout(function(){
            //     $('#successMsg').html('');
            //     $("#carRegistration")[0].reset();
                // location.reload();                
           // },1000);
        }
    }).fail(function(res) {
    
    });

}

function autoSearch(ele){       
   
   $('#filter_message').hide();
   var bi_form = document.querySelector('#filterForm');
   var fd      = new FormData(bi_form);

   if( (fd.get('username') == '') && (fd.get('accesstion_user') == '') ){
       $('#filter_message').show();
       $('#filter_message').html('No Record Found!');
       return false;
   }

   $('#user-search-btn').html('Searching...');

   sendPostRequest('library/search-by-filter',function(res){
       $('#user-search-btn').html('Load Detail');
       if(res.username){
           var username = res.username;
           $('#sai_enrollment_no').val(username);
           $('#sisuserUsername').val(username);
           createProfileView(res);
           loadUserBookIssuedHistory(username);
       }else{
          
           $('#filter_message').show();
           $('#filter_message').html('No Record Found!');
       }
   },fd,function(error){
       $('#user-search-btn').html('Load Detail');
       console.log('error : ',error);
   });
   return false;
}   
function submitForm() {   
   var bi_form = document.querySelector('#carRegistration');
    var fd = new FormData(bi_form);   
    $.ajax({
        method: "POST",
        url: '{{'/api/save-car_registration'}}',
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
                    $("#vehicle_token").hide();   
                    getProfile();   
                    setTimeout(function(){
                        $('#successMsg').html('');
                      //  $("#carRegistration")[0].reset();
                       // location.reload();                
                    },1000);
                }
    }).fail(function(res) {
        $('#errorMsg').css('color', 'red');
        $('#errorMsg').html('Sticker Not Issued');
        setTimeout(function(){
            $('#errorMsg').html('');   
            //$('#btn').prop('disabled', false);                                        
        },5000);   
    });
                 

} 

function check_validation(validation_array){
    var count_errors = 0;
    //alert('ddd');
    $.each(validation_array, function( index, value ) {
        alert(value);
        if ($('#'+value).val()==="" || $('#'+value).val()===null){  
            $('#'+value).removeClass('is-valid').addClass('is-invalid');
            count_errors++;
        }
        else{
            $('#'+value).removeClass('is-invalid').addClass('is-valid');
            setTimeout(function(){
            $('#'+value).removeClass('is-valid');
            },2000);
        } 
    });
    if(count_errors>0){
    return 0;
    }else{
    return 1;
    }
}
$(document).ready(function() {
  // Call API and append data to div
  
  
  // Cancel button click event
  $("div#records").on("click", "button.cancel", function() {
    var record = $(this).closest("div.record");
    var name = record.find("p").text();
    
    // Call API again and refresh data
    $.ajax({
      url: "https://example.com/api/cancel",
      type: "POST",
      data: { name: name },
      success: function(response) {
        record.remove();
        // Append refreshed data to div
        $.each(response, function(index, data) {
          var newRecord = "<div class='record'><p>" + data.name + "</p><button class='cancel'>Cancel</button></div>";
          $("div#records").append(newRecord);
        });
      }
    });
  });
});

function getRequest(){  
    $.ajax({
            type:'GET',
            url: "{{ url('/api/get-car-registration') }}",
            headers: {"Authorization": 'Bearer {{ auth()->user()->api_token }}'},     
            //headers:reqHeaders,
            success:function(res){
                if (res.data) {
                    var tbody = '';
                    var count = 1;                                 
                    $(res.data).each(function(index,row){    
                        var sr = count++;
                        alert(row.vr_vehicle_token);
                        $('#show_sticker_data').append('<div style="text-align:center">'+row.vr_vehicle_token+'</div>');   

                        tbody += '<tr>';
                        tbody += '<td style="text-align:center">'+sr+'</td>';
                        tbody += '<td style="text-align:center">'+row.vr_sess_id+'</td>';
                        tbody += '<td>'+row.vr_username+'</td>';
                        tbody += '<td>'+row.vr_vehicle_no+'</td>';
                        tbody += '<td>'+row.vr_vehicle_token+'</td>';
                        tbody += '<td style="text-align:center">'+row.vr_entry_datetime+'</td>';                       
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

// function deleteRow(id){

//     sendPostRequest('', '', 'delete-student-record/'+id, function(res) {
//         //console.log('Hello Baby', res);
//         //alert(res.status);
//         if(res == 1){           
//             location.reload();            
//         }
//     },'');
// }

$('#sai_enrollment_no').autoComplete({
resolver: 'custom',
autoSelect: false,
formatResult: function (item) {
                return {
                    value: 'hello ap',
                    text: item.text + "[" + item.value + "] ",
                    // html: [ 
                    // 		$('<img>').attr('src', item.icon).css("height", 18), ' ',
                    // 		item.text 
                    // 	] 
                };
            },
            select : function (evt, item){
    console.log('Hello Ahmed',item);
},
events: {
    search: function (qry, callback) {
        // let's do a custom ajax call
        $.ajax(
            '<?=url('api/library/search-campusID-for-suggestion')?>',
            {
                data: { 'qry': qry}
            }
        ).done(function (res) {
            console.log(res);
            callback(res)
        });
    }
    
}
});

$('#sai_enrollment_no').on('autocomplete.select', function (evt, item) {
					// $('.basicAutoCompleteSelected').html(JSON.stringify(item));
					// $('.basicAutoCompleteCustom').html('');
                    // alert('ok')
                    var val = item.value+' || '+item.text
                    $('#sai_enrollment_no').val(val);
});
</script>
@stop

