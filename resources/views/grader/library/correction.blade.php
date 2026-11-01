
@extends('layouts.app',['pageTitle' => 'Correction'])

@section('title')
    BNU CMS
@stop
@section('content')
<style type="text/css">
    div{
        border:0px solid black;
    }
    </style>
    <?php
    $bookStatus = array("E","D");
    ?>
<div class="row">
    <div class="col-12 col-sm-12 col-lg-12">
        <div class="card">
            <div class="card-header" style="background-color: #2F3B4D;color:white" >
                <div style="float:left;">
                    <h3 style="font-weight: bold">New Arrival</h3>
                </div>
                <div style="float:left;padding-left:100px">
                    <input style="width:150px" type="text" class="form-control" id="search" placeholder="Search with Keyword">
                </div>
                <div style="float:left;padding-left:100px">
                    <input style="width:150px" type="text" class="form-control" id="searchTitle" placeholder="Search with Title">
                </div>
                <div style="float:left;padding-left:100px">
                    <input style="width:150px" type="text" class="form-control" id="searchAuthorName" placeholder="Search with Author Name">
                </div>
                {{-- <div style="float:left;padding-left:70px">
                    <select style="width:130px"  name="check_status" id="check_status" class=" form-control">
                        <option value="undefined">--Select Status--</option>                                       
                        <option value="Active">Active</option>   
                        <option value="Pending">Pending</option>                                                                                             
                    </select>
                </div> --}}
                <div style="float:left;padding-left:70px"> 
                    <button type="button" class="btn btn-primary" onclick="searchByTitle()" >Search</button>            
                    <button type="button" class="btn btn-primary" onclick="searchByAll()" >View All</button>                                       
                </div>
            </div>                                                             
                <div class="row" style="font-weight: bold;margin-top:10px;margin-bottom:10px">                            
                    <div style="text-align: center" class="col-3 col-sm-3 col-lg-3 form-group">
                        Title/Author
                    </div>   
                    <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                        Author Name
                    </div>                    
                    <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                        ISBN
                    </div> 
                    <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                        Accession No(s)
                    </div>     
                    <div style="text-align: center" class="col-3 col-sm-3 col-lg-3 form-group">
                        Active / Pending
                    </div>                           
                </div>   
                {{-- Row Data from Database --}}
                <?php 
                    use App\Http\Controllers\Grader\Library\BookController;                                  
                ?>
                <div id="hideView">
                    @foreach ($viewBooks as $user)   
                    <div class="card">
                        <div class="card-body">                          
                            <div class="row" >     
                                <div class="col-3 col-sm-3 col-lg-3 form-group">                                   
                                  
                                    <div style="<?php echo (strlen($user->library_title)>=32) ? 'height:60px' : 'height:20px';?>"><b>Title:</b> {{$user->library_title}}</div>
                                    <div style="<?php echo (strlen($user->library_title)>=32) ? 'height:23px' : 'height:53px';?>"><b>Volume:</b> {{$user->library_volume}}</div>
                                                                    
                                </div>                          
                                 
                                <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">                               
                                    {{$user->library_author_name}}
                                </div>                                  
                                <div class="col-2 col-sm-2 col-lg-2 form-group">
                                    {{$user->library_number_issn}}
                                </div>  
                                <div class="col-2 col-sm-2 col-lg-2 form-group"> 
                                   <ul>
                                   
                                       @foreach (BookController::accession_no($user->id) as $item)
                                           <li>{{$item->accession_no}}</li>
                                       @endforeach
                                   </ul>                                
                                </div> 
                                                     
                                <div style="text-align: center"  class="col-3 col-sm-3 col-lg-3 form-group">
                                    <div >
                                    <select name="book_status" id="book_status" class=" form-control" onchange="getval(this,<?=$user->id?>)">
                                        <option value="">--Select Status--</option>                                       
                                        @foreach ($bookStatus as $row) 
                                        <option   <?=((isset($user->status)?$user->status:'') == $row )? 'selected="selected"' : '' ?>   value="{{$row}}">{{($row==='E'?'Enable':'Disable')}}</option>
                                        @endforeach                                                                                                   
                                    </select>
                                    </div>
                                    <div id="statusmg<?=$user->id?>" style="text-align: center;color:green;display:none">
                                        <span style="font-size: 12px;font-weight:bold">Status Updated</span>
                                    </div>  
                                </div>       
                            </div>
                        </div>
                    </div>
                    @endforeach    
                                    
                </div>
            <div id="result-body" ></div>                  
        </div>
    </div>
</div>
@stop


@section('javascript')
<script type="text/javascript">
function getval(sel,id)
{   
    //alert(id);return false;
    if(sel.value == 'Update'){
        window.location.href = "{{URL::to('library/add-book-item')}}/?id="+id;        
    }else{
        var bookstatus = sel.value;
        var url = "{{URL::to('library/insert-bookcorrection-status')}}/"+bookstatus+'/'+id;
       
        $.ajax({
                type:'get',
                url: url,
              //  data:fd,
                processData:false,
                contentType:false,
                success:function(data){
                
                 if(data == 1){
                     $("#statusmg"+id).show();
                     $("#statusmghtml"+id).show();
                   //  window.location = '<?=url('library/correction')?>';
                    //  location.reload();
                 }
                  
                },
                complete: function(){
                }
            });
    }
}


</script>
@stop