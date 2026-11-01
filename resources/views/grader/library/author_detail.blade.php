@extends('layouts.app',['pageTitle' => 'Spine and Barcode'])

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
   
//    print_r($author_books);
//    exit;
    $bookStatus = array("Active","Pending","Update");
    ?>
<div class="row">
    <div class="col-12 col-sm-12 col-lg-12">
        <div class="card">
                <div class="card-header" style="background-color:#D2D6F1; color:#6774CF;display:inline" >
                    <div style="float:left;">
                        <h3 style="font-weight: bold">Author Details</h3>
                    </div>
                    <form enctype="multipart/form-data" method="post" name="search_form" style="display:none"  id="search_form">
                        {{ csrf_field() }}
                        <div style="float:left;padding-left:95px">
                            <input style="width:130px" type="text" class="form-control" id="search" name="search" placeholder="Search with Keyword">
                        </div>
                        <div style="float:left;padding-left:95px">
                            <input style="width:130px" type="text" class="form-control" id="searchTitle" name="searchTitle" placeholder="Search with Title">
                        </div>
                        <div style="float:left;padding-left:95px">
                            <input style="width:130px" type="text" class="form-control" id="searchAuthorName" name="searchAuthorName" placeholder="Search with Author Name">
                        </div>
                    
                        <div style="float:left;padding-left:95px"> 
                            <button type="button" class="btn btn-primary" onclick="searchByTitle()" >Search</button>            
                            <button type="button" class="btn btn-primary" onclick="searchByAll()" >View All</button>                                       
                        </div>
                    </form>
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
                    <!-- <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                        Entry Username
                    </div>  -->
                      <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                        Status
                    </div>                      
                                         
                </div>   
                {{-- Row Data from Database --}}
                <div id="hideView">
                    
                    @foreach ($author_books as $user)   
                    <div class="card" >
                        <div class="card-body">                          
                            <div class="row" >                            
                                <div class="col-4 col-sm-4 col-lg-4 form-group">                                   
                                    <div style="float:left"><img id="blah" src="<?=url('/library_images').'/'.$user->library_attachment?>" width="70" height="70" /></div>
                                    <div style="padding-left:80px;<?php echo (strlen($user->library_title)>=32) ? 'height:55px' : 'height:20px';?>"><b>Title:</b> <a style="color:black;cursor: pointer;text-decoration: none;" href="/library/add-book-item/?id=<?=$user->id?>">{{$user->library_title}}</a></div>
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
                                    {{$user->accession_no}}
                                </div>                                    
                                <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                                    {{$user->library_ddc_number}}
                                </div>                                 
                                <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">                               
                                    {{$user->library_author_mark}}
                                </div>    
                               
                                <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">                               
                                    @if ($user->lc_book_status == 'Issued')
                                        Issued
                                    @else
                                        Available
                                    @endif
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
                $.each(data,function( key, value ) {   
                    //alert(value.book_status)               
                    var item_type = '<div>'+value.library_item_type+'</div>'
                    var strlength = value.library_title.length   ;                 
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
                    html += '<div style="padding-left:80px;'+( (strlength >= 32)?'height:55px':'height:20px' )+' "><b>Title:</b><a style="color:black" href="{{route('add-book-item')}}/?id='+value.id+'" >'+value.library_title+'</a></div>' 
                    html += '<div style="padding-left:80px;'+( (strlength >= 32)?'height:23px':'height:53px' )+'"><b>Author:</b>'+value.library_author_name+'</div>'
                    html += item_type              
                    html += '</div>';
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.accession_no+'</p></div>';         
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.library_ddc_number+'</p></div>';                            
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.library_author_mark+'</p></div>';
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.emp_f_name+'</p></div>';             
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
    // function searchByTitle(){
    //     var keyword = $('#search').val();
    //     if(keyword == ''){
    //         keyword = 'undefined';
    //     }else{
    //         keyword = $('#search').val();
    //     }
    //     var searchTitle = $('#searchTitle').val();
    //     if(searchTitle == ''){
    //         searchTitle = 'undefined';
    //     }else{
    //         searchTitle = $('#searchTitle').val();
    //     }
    //     var authorname = $('#searchAuthorName').val();
    //     if(authorname == ''){
    //         authorname = 'undefined';
    //     }else{
    //         authorname = $('#searchAuthorName').val();
    //     }       
    //     var url = '<?=url("library/search-by-title")?>/'+keyword+'/'+searchTitle+'/'+authorname+'/'+$('#check_status').val();
    //     $.ajax({
    //     type:'GET',        
    //     url: url,               
    //     success:function(data){
    //         console.log(data.length)
    //         if(data.length > 0){
    //             $('#hideView').hide();
    //             var html = '';  
    //             $.each(data,function( key, value ) {   
    //                 //alert(value.book_status)               
    //                 var item_type = '<div>'+value.library_item_type+'</div>'
    //                 var strlength = value.library_title.length   ;                 
    //                 //alert(strlength)          
    //                 var imgValue = value.library_attachment;      
                      
    //                 var imgsrc = 'src="<?=url("/library_images")."/"."'+imgValue+'"?>"';                   
    //                 var img =  '<img id="blah" '+imgsrc+'  width="70" height="70" />';
    //                 var update = '<a href="{{route('add-book-item')}}/?id='+value.id+'" ><button type="button" class="btn btn-primary" >Update</button></a>'
    //                 html += '<div class="card">';
    //                 html += '<div class="card-body">';
    //                 html += '<div class="row" id="searchedItem">';
    //                 html += '<div style="text-align: left" class="col-4 col-sm-4 col-lg-4 form-group">'
    //                 html += '<div style="float:left">'+img+'</div>'
    //                 html += '<div style="padding-left:80px;'+( (strlength > 32)?'height:55px':'height:20px' )+' "><b>Title:</b>'+value.library_title+'</div>' 
    //                 html += '<div style="padding-left:80px;'+( (strlength > 32)?'height:23px':'height:53px' )+'"><b>Author:</b>'+value.library_author_name+'</div>'
    //                 html += item_type              
    //                 html += '</div>';
    //                 html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.accession_no+'</p></div>';         
    //                 html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.library_ddc_number+'</p></div>';                            
    //                 html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.library_author_mark+'</p></div>';
    //                 html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.emp_f_name+'</p></div>';
             
    //                 html += ' <div id="statusmghtml'+(value.id)+'" style="text-align: center;color:green;display:none"><span style="font-size: 12px;font-weight:bold">Status Updated</span></div>'   
    //                 html += '</div>'                  
    //                 html += '</div>';
    //                 html += '</div>';
    //                 html += '</div>';
    //             });
              
    //             if(html != ''){
    //                 $('#result-body').html(html);
    //             }else{
    //                 $('#result-body').html('No Result Found!');
    //             }
    //         }else{
    //             $('#hideView').hide();
    //             $('#result-body').html('No Result Found!');
    //         }
    //     },
    //     error: function (jqXHR, textStatus, errorThrown) {
    //               if (jqXHR.status == 500) {
    //                 $('#searchedItem').hide();
    //                 $('#hideView').show();                    
    //               }
    //           }
    // });

    // }

</script>
@stop