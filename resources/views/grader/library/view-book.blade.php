@extends('layouts.app',['pageTitle' => 'View New Arrivals'])

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
    $bookStatus = array("Active","Pending","Update","Delete");
    ?>
<div class="row">
    <div class="col-12 col-sm-12 col-lg-12">
        <div class="card">
            <div class="card-header" style="background-color:#D2D6F1; color:#6774CF;display:inline" >
                <div style="float:left;">
                    <h3 style="font-weight: bold">New Arrival</h3>
                </div>
                <div style="float:left;padding-left:100px">
                    <input style="width:170px" type="text" class="form-control" id="search" placeholder="Search with Keyword">
                </div>
                <div style="float:left;padding-left:100px">
                    <input style="width:170px" type="text" class="form-control" id="searchTitle" placeholder="Search with Title">
                </div>
                <div style="float:left;padding-left:100px">
                    <input style="width:170px" type="text" class="form-control" id="searchAuthorName" placeholder="Search with Author Name">
                </div>
           
                <div style="float:right"> 
                    <button type="button" class="btn btn-primary" onclick="searchByTitle()" >Search</button>            
                    <button type="button" class="btn btn-primary" onclick="searchByAll()" >View All</button>                                       
                </div>
            </div>                                                             
                <div class="row" style="font-weight: bold;margin-top:10px;margin-bottom:10px">                            
                    <div style="text-align: center" class="col-4 col-sm-4 col-lg-4 form-group">
                        Title/Author
                    </div>   
                    <div style="text-align: center:display:inline" class="col-1 col-sm-1 col-lg-1 form-group">
                        Accession #
                    </div>                    
                    <div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group">
                        DDC
                    </div>  
                    
                    <div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group">
                        Author Mark
                    </div>       
                    <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                        Entry Username
                    </div>                    
                    <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                        Active / Pending
                    </div>   
                                           
                </div>   
                {{-- Row Data from Database --}}
                <div id="hideView">
                    @foreach ($viewBooks as $user)   
                    <div class="card"  <?php echo ($user->book_status == 'Pending') ? 'style="background-color: #95FFC0"' : '';?>>
                        <div class="card-body">                          
                            <div class="row" >                            
                                <div class="col-4 col-sm-4 col-lg-4 form-group">                                   
                                    <div style="float:left"><img id="blah" src="<?=url('/library_images').'/'.$user->library_attachment?>" width="70" height="70" /></div>
                                    <div style="padding-left:80px;<?php echo (strlen($user->library_title)>=32) ? 'height:55px' : 'height:20px';?>"><b>Title:</b> {{$user->library_title}}</div>
                                    <div style="padding-left:80px;<?php echo (strlen($user->library_title)>=32) ? 'height:23px' : 'height:53px';?>"><b>Author:</b> {{$user->library_author_name}}</div>
                                    <div style="text-align:center;width:70px" >
                                        <?php 
                                            if($user->library_item_type == 'Reference Book'){
                                                echo 'Reference';
                                            }elseif($user->library_item_type == 'Art Catalog'){
                                                echo 'Art';
                                            }else{
                                                echo $user->library_item_type;
                                            }
                                        ?>
                                    </div>                                      
                                </div>    
                                <div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group">                               
                                    {{$user->accession_no}}
                                </div>                                  
                                <div class="col-1 col-sm-1 col-lg-1 form-group">
                                    {{$user->library_ddc_number}}
                                </div>   
                                
                                <div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group">                               
                                    {{$user->library_author_mark}}
                                </div>    
                                <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">                               
                                    {{$user->emp_f_name}}
                                </div>                            
                                <div style="text-align: center"  class="col-2 col-sm-2 col-lg-2 form-group">
                                    <div >
                                    <select name="book_status" id="book_status" class=" form-control" onchange="getval(this,<?=$user->id?>)">
                                        <option value="0">--Select Status--</option>                                       
                                        @foreach ($bookStatus as $row) 
                                        <option   <?=((isset($user->book_status)?$user->book_status:'') == $row )? 'selected="selected"' : '' ?>   value="{{ $row}}">{{ $row }}</option>
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
                    <span>
                        {{$viewBooks->links()}}
                    </span>                       
                </div>
            <div id="result-body" ></div>                  
        </div>
    </div>
</div>

@stop


@section('javascript')
<script type="text/javascript">

/*

library/circulation/search
library/circulation/add



library/catelog/search
library/catelog/add

*/

function getval(sel,id)
{   
    //alert(id);return false;
    if(sel.value == 'Update'){
        window.location.href = "{{URL::to('library/add-book-item')}}/?id="+id;        
    }else if(sel.value == 'Delete'){

            sendPostRequest('#btn','#kihhh','library/delete-newarrival-rows/'+id,function(data){
            if(data == 1){
            $('#book_status').val(0);
            location.reload();
            }
            //alert(data)
            });
    }else{
        var bookstatus = sel.value;
        var url = "{{URL::to('library/insert-book-status')}}/"+bookstatus+'/'+id;
       
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
                     window.location = '<?=url('library/view-book-item')?>';
                    //  location.reload();
                 }
                  
                },
                complete: function(){
                }
            });
    }
}
    function searchByAll(){

        var url = "{{URL::to('library/view-book-item')}}";
        location.reload(url)
        
    }
    function searchByTitle(){
        var keyword = $('#search').val();
        if(keyword == ''){
            keyword = 'undefined';
        }else{
            keyword = $('#search').val();
        }
        var searchTitle = $('#searchTitle').val();
        if(searchTitle == ''){
            searchTitle = 'undefined';
        }else{
            searchTitle = $('#searchTitle').val();
        }
        var authorname = $('#searchAuthorName').val();
        if(authorname == ''){
            authorname = 'undefined';
        }else{
            authorname = $('#searchAuthorName').val();
        }       
        var url = '<?=url("library/search-by-title")?>/'+keyword+'/'+searchTitle+'/'+authorname+'/'+$('#check_status').val();
        $.ajax({
        type:'GET',
        
        url: url,               
        success:function(data){
            console.log(data.length)
            if(data.length > 0){
                $('#hideView').hide();
                var html = '';  
                $.each(data,function( key, value ) {   
                    //alert(value.book_status)               
                    var item_type = '<div>'+value.library_item_type+'</div>'
                    var strlength = value.library_title.length    
                    var strlength2 = value.library_title.length 
                    //alert(strlength)          
                    var imgValue = value.library_attachment;     
                   
                      
                    var imgsrc = 'src="<?=url("/library_images")."/"."'+imgValue+'"?>"';                   
                    var img =  '<img id="blah" '+imgsrc+'  width="70" height="70" />';
                    var update = '<a href="{{route('add-book-item')}}/?id='+value.id+'" ><button type="button" class="btn btn-primary" >Update</button></a>'
                    html += '<div class="card">';
                    html += '<div class="card-body">';
                    html += '<div class="row" id="searchedItem">';
                    html += '<div style="text-align: left" class="col-4 col-sm-4 col-lg-4 form-group">'
                    html += '<div style="float:left">'+img+'</div>'
                    html += '<div style="padding-left:80px;'+( (strlength >= 32)?'height:55px':'height:20px' )+' "><b>Title:</b>'+value.library_title+'</div>' 
                    html += '<div style="padding-left:80px;'+( (strlength >= 32)?'height:23px':'height:53px' )+'"><b>Author:</b>'+value.library_author_name+'</div>'
                    html += item_type              
                    html += '</div>';
                    html += '<div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group"><p>'+value.accession_no+'</p></div>';   
                    html += '<div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group"><p>'+value.library_ddc_number+'</p></div>';                                 
                    html += '<div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group"><p>'+value.library_author_mark+'</p></div>';
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.emp_f_name+'</p></div>';
                    html += '<div style="text-align: center"  class="col-2 col-sm-2 col-lg-2 form-group">'
                    html += '<div><select name="book_status" id="book_status" class=" form-control" onchange="getval(this,'+value.id+')">' 
                    html += '<option value="">--Select Status--</option>'
                    html += '<option value="Active" '+( (value.book_status == "Active")?'selected':'' )+' >Active</option>'
                    html += '<option value="Pending" '+( (value.book_status == "Pending")?'selected':'' )+' >Pending</option>'
                    html += '<option value="Update">Update</option>'
                    html += '</select>'
                    html += '</div>' 
                    html += ' <div id="statusmghtml'+(value.id)+'" style="text-align: center;color:green;display:none"><span style="font-size: 12px;font-weight:bold">Status Updated</span></div>'   
                    html += '</div>'                  
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                });
              
                if(html != ''){
                    $('#result-body').html(html);
                }else{
                    $('#result-body').html('No Result Found!');
                }
            }else{
                $('#hideView').hide();
                $('#result-body').html('No Result Found!');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
                  if (jqXHR.status == 500) {
                    $('#searchedItem').hide();
                    $('#hideView').show();                    
                  }
              }
    });

    }

</script>
@stop