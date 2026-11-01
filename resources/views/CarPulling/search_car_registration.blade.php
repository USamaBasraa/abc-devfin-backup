
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
<div class="testif"></div>
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group"> 
        <div class="card">
            <div class="card-header" style="display:inline;background-color:#4454c3;color: white;">
                <div style="float:left">
                    <h3 class="card-title" style="padding-top:10px">Search User by ID or Vehicle No</h3>  
                </div> 
            </div>
            <div class="card-body">
                <form id="carRegistration" method="post" onsubmit="event.preventDefault(); autoSearch(this);"> 
                    <div class="row">                           
                        <div class="col-12 col-sm-12 col-md-11 col-lg-11 form-group"> 
                            <label class="col-form-label" >ID / Vehicle No</label>                  
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
                                                 
                        </div>        
                    </div>         
                   
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row" style="display:none">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group"> 
        <div class="card">
            <div class="card-header">
                <div class="card-title">Registered User</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-nowrap" id="example2">
                        <thead>
                            <tr>
                                <th class="wd-15p border-bottom-0" style="background-color:#4454c3;color: white;">Sr #</th>
                                <th class="wd-15p border-bottom-0" style="background-color:#4454c3;color: white;">Session</th>
                                <th class="wd-20p border-bottom-0" style="background-color:#4454c3;color: white;">User</th>
                                <th class="wd-15p border-bottom-0" style="background-color:#4454c3;color: white;">Vehicle No</th>
                                <th class="wd-10p border-bottom-0" style="background-color:#4454c3;color: white;">Vehicle Token</th>
                                <th class="wd-25p border-bottom-0" style="background-color:#4454c3;color: white;">Entry DateTime</th>
                                <th class="wd-25p border-bottom-0" style="background-color:#4454c3;color: white;">Token Status</th>
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
                                <td><?=($row->vr_status == 'E')?'Active':'Cancelled';?></td>
                                <td><button type="button" onclick="setVrID('<?=$row->vr_id?>')" class="btn btn-danger" data-toggle="modal" data-target="#default-Modal" >Cancel</button></td>
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


@stop
@section('javascript')
<script src="<?=asset('js/bootstrap-autocomplete.js')?>"></script>
<script type="text/javascript">

function setVrID(id){
    $('#vr_id').val(id);
}    

function getProfile(){   
    $('#showProfile').show(); 
    var rol_no = $('#sai_enrollment_no').val().toUpperCase();        
    // if you want to show alert msg at the top write this (.message_div) as second parem in sendGetRequest
    sendGetRequest('#noti-msg-tbody', '.ghgfhfghdfh','get-student-profile/'+rol_no, function(res) {       
        if(res.message == 1){           
            var tbody = '';
            $(res.data).each(function(index,row){    
                var group = '';
                if(row.group_id == '4'){
                    var group = 'Student';
                }else if(row.group_id == '222' || row.group_id == '222'){
                    var group = 'Faculty';
                }else{
                    var group = 'Staff';
                }
                var shareWith = (row.cp_share_with === 'Nobody') ? 'Nobody' :'Yes';
                if(row.cp_partner == null){
                    var shareWith = 'No';
                }
                $('#group_id').html('<b>Group: </b>'+group); 
                $('#name').html('<b>Name: </b>'+row.spi_name);  
                $('#school_name').html('<b>School: </b>'+row.school_name); 
                $('#dept_name').html('<b>Department: </b>'+row.dept_name); 
                $('#mobile').html('<b>Mobile: </b>'+row.spi_mobile); 
                $('#vehicle').html('<b>Vehicle: </b>'+row.cp_vehical_no); 
                $('#share_with').html('<b>Share With: </b>'+shareWith);                 
                // alert(row.vr_status);
                if(row.vr_status != null){
                    //alert(row.vr_status)
                    if(row.vr_status == 'E'){
                        $('#vehicle_reg_no').html('<b>Vehicle Token: </b>'+row.vr_vehicle_token); 
                        $('#token_status').html('<b>Vehicle Status: </b> Active'); 
                    }
                }else{
                    $('#vehicle_reg_no').html('<b>Vehicle Token: </b> No Sticker Issued');   
                    $('#token_status').html('');
                }
                $('#vehicle_no').val(row.cp_vehical_no);
                // $('#partner').html('<b>Partners: </b>'+row.cp_partner); 
                       
                // myArray = row.cp_partner.split(" ");
                // $(myArray).each(function(index,row){ 
                //     alert(row.length);
                // });
                if(row.cp_vehical_no == null && row.vr_vehicle_token == null){ 
                    // alert('456');    
                    $('#showVehicleToken').hide();
                    $('#btnShowHide').hide();
                }else if(row.cp_vehical_no == null || row.vr_status == 'D'){ 
                    // alert('123');                  
                    $('#showVehicleToken').show();
                    $('#btnShowHide').show();
                }else{
                    // alert('789');    
                    $('#showVehicleToken').hide();
                    $('#btnShowHide').hide();
                    if(row.vr_vehicle_token == null){
                        $('#showVehicleToken').show();
                        $('#btnShowHide').show();    
                    }
                }
              
            });
            tbody += '<img src="https://cms.bnu.edu.pk/students_pics/'+rol_no+'.jpg" alt="No Image Found" width="120" height="168">';    
            $('#image').html(tbody);
            $('#partner').html(''); 
            // alert(parseInt(res.sub_data.length));           
            if(parseInt(res.sub_data.length)>0){
              
                $('#partner').append('<div style="background-color:powderblue;padding:4px"><h3> Sharing With </h3></div>'); 
                $(res.sub_data).each(function(index,row){    
                    $('#partner').append('<div><b> '+row[0].sai_enrollment_no+' </b> : '+row[0].spi_name+'</div>'); 
                });
                // $('#showVehicleToken').show();
                var totalCarMember = res.sub_data.length+1;
                $('#partner').append('<div><b> Total Members: </b>'+totalCarMember+'</div>');       
            }else{
               
                $('#partner').append('<div style="background-color:tomato;padding:4px"><h3> Sharing With </h3></div>'); 
                $(res.resCarPooling).each(function(index,row){   
                    
                var part = row.cp_partner.toUpperCase();   
                    // alert(part);
                var abc = part.replace(rol_no, "").replace('~', "");
                var text = abc.replace('~', "");
                // alert(text);
                $('#partner').append('<div><b> '+row.cp_entry_username.toUpperCase()+' (Owner)</div>'); 
                $('#partner').append('<div><b> '+text+'</div>'); 
               
                });
                var totalCarMember = res.sub_data2.length+1;
                 //alert(res.resCarPooling.length);
                if(totalCarMember == '1'){
                    $('#partner').append('<div><b>No Partner</b></div>');  
                }else{
                    $('#partner').append('<div><b> Total Members: </b>'+totalCarMember+'</div>');      
                }                    
                //alert("456");                
                // $('#showVehicleToken').hide();
            }    
        }          
    });
}  

function deleteBtn(){    
    //var reason   = $('#cancel_reason').val();
    var getdata = check_validation(['cancel_reason']);  
    if(getdata==0){       
        return false;
    }  
    var id  = $('#vr_id').val();
    var cancel_reason  = $('#cancel_reason').val();
    
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
            $('#successMsg').css('color', 'green');
            $('#successMsg').html('User Deleted Successfully');       
            setTimeout(function(){
                $('#successMsg').html('');
                $("#carRegistration")[0].reset();
                // location.reload();                
            },1000);
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
    // var getdata = check_validation(['academic_session','vehicle_no','vehicle_token','sai_enrollment_no']);  
    // if(getdata==0){       
    //     return false;
    // }       
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
                    setTimeout(function(){
                        $('#successMsg').html('');
                        $("#carRegistration")[0].reset();
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

function check_validation(validation_array){
    var count_errors = 0;
    $.each(validation_array, function( index, value ) {
        //alert(value);
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
$(document).ready(function(){
    // $('#tbl_exporttable_to_xls').DataTable();
    // getRequest();
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
            '<?=url('api/library/search-user-for-suggestion')?>',
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
                    $('#sai_enrollment_no').val(item.value);
});
</script>
@stop

