@extends('layouts.app',['pageTitle' => 'Library Criteria'])

@section('title')
    BNU CMS
@stop
@section('content')



        <div class="card" >
            {{-- <div class="card-header">
                <h3>Circulation Criteria </h3>
            </div> --}}
            <div class="card-body">              
                <form enctype="multipart/form-data" method="post" name="library_criteria"  id="library_criteria">
                {{ csrf_field() }}  
                <input class=" form-control" type="hidden" id="lbc_id" name="lbc_id">                      
                    <div class="row">
                        <div class="col-6 col-sm-6 col-lg-6 form-group">
                            <label for="school_sel">Categories</label>
                            <select name="categories" id="categories" class=" form-control">
                                <option value="">--Select Categories</option>
                                <option value="Graduation 2/4 Years">Graduation 2/4 Years</option>
                                <option value="Post Graduate">Post Graduate</option>
                                <option value="Student TA">Student TA</option>
                                <option>Visiting Faculty</option>
                                <option>Permanent Faculty</option>
                                <option>Staff</option>
                                <option>Dean / HOD</option>
                                <option>Vice Chancellor</option>                            
                            </select>
                        </div>                
                        
                        <div class="col-6 col-sm-6 col-lg-6 form-group">
                            <label for="school_sel">Limit</label>
                            <select name="limit" id="limit" class=" form-control">
                                <option value="">--Select Limit</option>
                                <option value="2">2</option>
                                <option value="4">4</option>
                                <option value="6">6</option>
                                <option value="10">10</option>   
                                                                     
                            </select>
                        </div>       
                    </div>  
                    <div class="row">     
                        <div class="col-6 col-sm-6 col-lg-6 form-group">
                            <label for="school_sel">Per Day Fine</label>
                            <select name="fine" id="fine" class=" form-control">
                            <option value="">--Select Per Day Fine</option>
                                <option value="10">10</option>
                                <option value="Exempted">Exempted</option>                                                                                
                            </select>
                        </div>   
                        <div class="col-6 col-sm-6 col-lg-6 form-group">
                            <label for="school_sel">Book Lost Case</label>
                            <select name="book_lost" id="book_lost" class=" form-control">
                            <option value="">--Select Book Lost Case</option>
                                <option value="two times">Two times current price</option>                                                                                                  
                            </select>
                        </div>                     
                    </div>  
                    <div class="row">
                        <div class="col-6 col-sm-6 col-lg-6 form-group">
                            <label for="school_sel">Duration</label>
                            <select name="duration" id="duration" class=" form-control">
                            <option value="">--Select Duration</option>
                                <option value="14">14 Days</option>
                                <option value="30">1 Month</option>      
                                <option value="120">4 Month</option>                                                                                 
                            </select>
                        </div>   
                        <div class="col-6 col-sm-6 col-lg-6 form-group">
                            <label for="school_sel">Reissuance Policy</label>
                            <select name="reissuance_poilicy" id="reissuance_poilicy" class=" form-control">
                            <option value="">--Select Reissuance Policy</option>
                                <option value="42">42 Days</option>
                                <option value="90">3 Months</option>     
                                <option value="120">4 Months</option>                                                                           
                            </select>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-12 col-lg-12 form-group" style="text-align: left">
                            <button id="submitbtn"  type="button" class="btn btn-primary" onclick="Submit()" >Submit</button>    
                            <button id="updatebtn" style="display:none"  type="button" class="btn btn-primary" onclick="Update()" >Update</button>  
                            <span id="successMsg"></span>  
                        </div> 
                    </div>                                        
                </form>  
                        
            </div>
        </div>
  
<div id="showcriteria" style="margin-top:20px"></div> 
@stop


@section('javascript')
<script type="text/javascript">
  
    function viewCriteria(){
        var url = '<?=url("library/show-library-criteria")?>';
        $.ajax({
            type:'get',
            url: url,
           // data:fd,
            processData:false,
            contentType:false,
            success:function(data){
              
                $("#showcriteria").html(data);	
                
            },
            error: function (jqXHR, exception) {
            },
        });


    }
    $( document ).ready(function() {
   viewCriteria();
});
    function Submit(){ 
        var bi_form = document.querySelector('#library_criteria');
        var fd = new FormData(bi_form);
        var url = '<?=url("library/insert-criteria")?>';
        $.ajax({
            type:'POST',
            url: url,
            data:fd,
            processData:false,
            contentType:false,
            success:function(data){
              //  console.log(data)
                $('#successMsg').html(data.message);      
                
            },
            error: function (jqXHR, exception) {
            },
        });

        viewCriteria();
       
    }
    function Update(){ 
        var bi_form = document.querySelector('#library_criteria');
        var fd = new FormData(bi_form);
        var url = '<?=url("library/update-criteria")?>/'+$('#lbc_id').val();
        $.ajax({
            type:'POST',
            url: url,
            data:fd,
            processData:false,
            contentType:false,
            success:function(data){
              //  console.log(data)
              $('#successMsg').html(data.message); 
              location.reload();
                
            },
            error: function (jqXHR, exception) {
            },
        });

     
       
    }
    
</script>
@stop