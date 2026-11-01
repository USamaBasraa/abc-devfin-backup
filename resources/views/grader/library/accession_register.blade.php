@extends('layouts.app',['pageTitle' => 'Accession Register'])

@section('title')
    BNU CMS
@stop
@section('content')

<?php
$bookStatus = array("Active","Pending","Update");
?>

<div class="row">
    <div class="col-12 col-sm-12 col-lg-12">
        <div class="card">
            <div class="card-header" >
                <div class="row">     
                    <form enctype="multipart/form-data" method="post" onsubmit="event.preventDefault(); searchByTitle();"  name="search_form" id="search_form">
                        <div class="form-row align-items-center">
                            {{ csrf_field() }}
                            <div class="col-auto">
                                <p><strong>Catalog</strong></p>
                            </div>
                            <div class="col-auto">
                                <input style="width:600px" type="text" class="form-control" id="search-input" name="search-input" placeholder="Search">
                            </div>
                            <div class="col-auto">
                            <select class="form-control" name="search_by">
                                <option value="">All</option>
                                <option value="title">Title</option>
                                <option value="author">Author</option>
                                <option value="ddc">DDC</option>
                                <option value="isbn">ISBN</option>
                                <option value="acc_no">Acc.No</option>
                                <option value="subject">Subject</option>
                                <option value="keyword">Keyword</option>
                            </select>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-primary" onclick="searchByTitle()">Search</button>
                                <button type="button" class="btn btn-primary" onclick="searchByAll()">View All</button>
                            </div>
                        </div>
                    </form>
                    <!-- FORM END -->            
                </div>
            </div>
            <div class="row" style="font-weight: bold;margin-top:10px;margin-bottom:10px">    
                <style>
                .header_link{
                    color:black;
                }        
                </style>                        
                <div style="text-align: center" class="col-4 col-sm-4 col-lg-4 form-group">
                    <?php
                    $title_link = url('/library/catalog?title_order=asc');
                    $title_icon = 'fa-sort-alpha-asc';
                    if( request()->get('title_order') == 'asc' ){
                        
                        $title_link = url('/library/catalog?title_order=desc');
                        $title_icon = 'fa-sort-alpha-desc';
                    }elseif(  request()->get('title_order') == 'desc' ){
                        $title_link = url('/library/catalog?title_order=asc');
                        $title_icon = 'fa-sort-alpha-asc';
                    } 
                    ?>
                    <a href="<?=$title_link?>" class="header_link"> Title/Author <i class="fa <?=$title_icon?>" aria-hidden="true"></i> </a>
                </div> 
                <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                    <?php
                    $access_link = url('/library/catalog?access_order=asc');
                    $access_icon = 'fa-sort-numeric-asc';
                    if( request()->get('access_order') == 'asc' ){
                        $access_link = url('/library/catalog?access_order=desc');
                        $access_icon = 'fa-sort-numeric-desc';
                    }elseif(  request()->get('access_order') == 'desc' ){
                        $access_icon = 'fa-sort-numeric-asc';
                        $access_link = url('/library/catalog?access_order=asc');
                    } 
                    ?>
                    <a href="<?=$access_link?>" class="header_link"> Accession No <i class="fa <?=$access_icon?>" aria-hidden="true"></i> </a>
                </div> 
                <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                    <?php
                        $ddc_link = url('/library/catalog?ddc_order=asc');
                        $ddc_icon = 'fa-sort-numeric-asc';
                        if( request()->get('ddc_order') == 'asc' ){
                            $ddc_link = url('/library/catalog?ddc_order=desc');
                            $ddc_icon = 'fa-sort-numeric-desc';
                        }elseif(  request()->get('ddc_order') == 'desc' ){
                            $ddc_icon = 'fa-sort-numeric-asc';
                            $ddc_link = url('/library/catalog?ddc_order=asc');
                        } 
                    ?>
                    <a href="<?=$ddc_link?>" class="header_link"> DDC <i class="fa <?=$ddc_icon?>" aria-hidden="true"></i> </a>
                </div>                        
                <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">                            
                    <?php
                    $author_mark_link = url('/library/catalog?author_mark_order=asc');
                    $author_mark_icon  = 'fa-sort-alpha-asc';
                    if( request()->get('author_mark_order') == 'asc' ){
                        $author_mark_link = url('/library/catalog?author_mark_order=desc');
                        $author_mark_icon = 'fa-sort-alpha-desc';
                    }elseif(  request()->get('author_mark_order') == 'desc' ){
                        $author_mark_icon = 'fa-sort-alpha-asc';
                        $author_mark_link = url('/library/catalog?author_mark_order=asc');
                    } 
                    ?>
                    <a href="<?=$author_mark_link?>" class="header_link"> Author Mark <i class="fa <?=$author_mark_icon?>" aria-hidden="true"></i> </a>
                </div>  
                <div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group">
                    Edition
                </div>  
                <div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group">
                    Status
                </div>                    
                </div>   
                    {{-- Row Data from Database --}}
                <div id="hideView">
                    @foreach ($viewBooks as $user)   
                    <div class="card" >
                        <div class="card-body">                          
                            <div class="row" >                            
                                <div class="col-4 col-sm-4 col-lg-4 form-group">                                   
                                    <div style="float:left"><img id="blah" src="<?=url('/library_images').'/'.$user->library_attachment?>" width="70" height="70" /></div>
                                    <div style="padding-left:80px;<?php echo (strlen($user->library_title)>=32) ? 'height:55px' : 'height:20px';?>"><b>Title:</b> <a style="color:black;cursor: pointer;text-decoration: none;" href="/library/add-book-item/?id=<?=$user->library_id?>">{{$user->library_title}}</a></div>
                                    <div style="padding-left:80px;<?php echo (strlen($user->library_title)>=32) ? 'height:23px' : 'height:53px';?>"><b>Author:</b><a style="color:black;cursor: pointer;text-decoration: none;" href="/library/author_books_details/?author=<?=$user->library_author_name?>"> {{$user->library_author_name}}</a></div>
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
                                <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">                               
                                    <?php if($user->library_item_type == 'Report'){?>
                                        R-{{$user->accession_no}}                                        
                                    <?php }else if($user->library_item_type == 'Art Catalog'){?>
                                        A-{{$user->accession_no}}  
                                    <?php } else { ?>
                                        {{$user->accession_no}} 
                                    <?php } ?>
                                </div>                                    
                                <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                                    {{$user->library_ddc_number}}
                                </div>                                 
                                <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">                               
                                    {{$user->library_author_mark}}
                                </div>    
                                <div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group">                               
                                    @if($user->library_edition != '')
                                    {{$user->library_edition}}
                                    @else
                                    Not Entered
                                    @endif
                                </div>  
                                <div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group">                              
                                    @if($user->book_status != 'Lost')  
                                        @if ($user->issued > 0)
                                            Issued
                                        @elseif ($user->received > 0)
                                            Received
                                        @else
                                            Available
                                        @endif
                                    @else
                                        Lost
                                    @endif
                                </div>     
                            </div>
                        </div>
                    </div>
                    @endforeach    
                    <span>
                        {{$viewBooks->appends(request()->except(['page','_token']))->links()}}
                    </span>                
                </div>
                <div id="result-body" ></div>                  
            </div>
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
                     location.reload();
                 }
                  
                },
                complete: function(){
                }
            });
    }
}
    function searchByAll(){

        var url = "{{URL::to('library/catalog')}}";
        location.reload(url)
        
    }
    function searchByTitle(){

        var bi_form = document.querySelector('#search_form');
        var fd = new FormData(bi_form);
        var url = '<?=url("library/insert-library-items-catlog")?>';
        $.ajax({
                type:'POST',
                url: url,
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){
            console.log(data.length)
            if(data.length > 0){
                $('#hideView').hide();
                var html = '';  
               
                html += '<div class="card">';
                html += '<div class="card-body">';
                html += '<div style="float:left">Searched Books Result '+data.length+'</div>'
                html += '</div>';
                html += '</div>';
                $.each(data,function( key, value ) {   
                    //alert(value.library_title);
                    var library_title = value.library_title;
                    if(library_title == null){
                        title = 'No Title Entered'
                    }else{
                        title = value.library_title
                    }
                   // return false;               
                    var item_type = '<div>'+value.library_item_type+'</div>'
                    var strlength = title.length;                 
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
                    html += '<div style="padding-left:80px;'+( (strlength >= 32)?'height:55px':'height:20px' )+' "><b>Title:</b><a style="color:black;cursor: pointer;text-decoration: none;" href="{{route('add-book-item')}}/?id='+value.id+'" >'+title+'</a></div>' 
                    html += '<div style="padding-left:80px;'+( (strlength >= 32)?'height:23px':'height:53px' )+'"><b>Author:</b><a style="color:black;cursor: pointer;text-decoration: none;" href="{{route('author_books_details')}}/?author='+value.library_author_name+'" >'+value.library_author_name+'</a></div>'
                    html += item_type              
                    html += '</div>';
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.accession_no+'</p></div>';         
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.library_ddc_number+'</p></div>';                            
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.library_author_mark+'</p></div>';
                    html += '<div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group"><p>'+( (value.library_edition != null)?value.library_edition:'Not Entered' )+'</p></div>';
                    html += '<div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group"><p>'+((value.issued > 0 ? 'Issued' : (value.received > 0 ? 'Received' : 'Available')))+'</p></div>';             
                    // html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+((value.lc_book_status == 'Issued')?'Issued':'Available' )+'</p></div>';             
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
                error: function (jqXHR, exception) {
      
     
                },               
            });
    }


</script>
@stop