@extends('layouts.app',['pageTitle' => 'Circulation'])

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
    $bookStatus = array("Active","Pending","Update");
    ?>
<div class="row">
    <div class="col-12 col-sm-12 col-lg-12">
        <div class="card">
            <div class="card-header" style="background-color: #2F3B4D;color:white" >
                <div style="float:left;">
                    <h3 style="font-weight: bold">Circulation</h3>
                </div>
                <form enctype="multipart/form-data" method="post" name="search_form"  id="search_form">
                    {{ csrf_field() }}
                {{-- <div style="float:left;padding-left:95px">
                    <input style="width:160px" type="text" class="form-control" id="search" name="search" placeholder="Search with Keyword">
                </div> --}}
                <div style="float:left;padding-left:95px">
                    <input style="width:160px" type="text" class="form-control" id="searchAccession" name="searchAccession" onkeyup="bilal()" placeholder="Search with Accession NO">
                </div>
                {{-- <div style="float:left;padding-left:95px">
                    <input style="width:160px" type="text" class="form-control" id="searchTitle" name="searchTitle" placeholder="Search with Title">
                </div>
                <div style="float:left;padding-left:95px">
                    <input style="width:160px" type="text" class="form-control" id="searchAuthorName" name="searchAuthorName" placeholder="Search with Author Name">
                </div> --}}

                <div style="float:right;">
                    <button type="button" class="btn btn-primary" onclick="searchByTitle()" >Search</button>
                    <button type="button" class="btn btn-primary" onclick="searchByAll()" >View All</button>
                </div>
            </form>
            </div>
                <div class="row" style="font-weight: bold;margin-top:10px;margin-bottom:10px">
                    <div style="text-align: center" class="col-4 col-sm-4 col-lg-4 form-group">
                        Title/Author
                    </div>
                    <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                        Accession No
                    </div>
                    <div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group">
                        DDC
                    </div>
                    <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                        Author Mark
                    </div>
                    <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                        Issued / Total Books
                    </div>
                    <div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group">
                       Action
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
                                    <div style="padding-left:80px;<?php echo (strlen($user->library_title)>=32) ? 'height:55px' : 'height:20px';?>"><b>Title:</b> <a style="color:black;cursor: pointer;text-decoration: none;" href="/library/add-book-item/?id=<?=$user->id?>">{{$user->library_title}}</a></div>
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
                                <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                                    {{$user->accession_no}}
                                </div>
                                <div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group">
                                    {{$user->library_ddc_number}}
                                </div>
                                <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                                    {{$user->library_author_mark}}
                                </div>
                                <div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group">
                                    {{$user->issued.' / '.$user->total}}
                                </div>
                                <div style="text-align: center" class="col-1 col-sm-1 col-lg-1 form-group">
                                    <button type="button" <?php echo ($user->issued == $user->total) ? 'disabled' : '';?> class="btn btn-primary" onclick="allocation('<?=$user->id?>','<?=$user->accession_no?>')" >Allocation</button>
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
function bilal(){
   var str =  $('#searchAccession').val();
//   alert  (str.length);
  if(str.length == 3 || str.length == 4 || str.length == 5){
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

                 //   alert(value.library_item_type);

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
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.accession_no+'</p></div>';
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.library_ddc_number+'</p></div>';
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.library_author_mark+'</p></div>';
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>  <button type="button" class="btn btn-primary" onclick="allocation('+value.id+','+value.accession_no+')" >Allocation</button> </p></div>';
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
}
// function allocation(id,access_no)
// {
//    window.location.href = "{{URL::to('library/book-allocation-status')}}/"+id+'/'+access_no;
//  //   window.location.href = "{{URL::to('library/book-allocation-status')}}";



// }
function allocation(id,access_no)
{
   window.location.href = "{{URL::to('library/book-allocation-status')}}";
 //   window.location.href = "{{URL::to('library/book-allocation-status')}}";



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
                    html += '<div style="padding-left:80px;'+( (strlength >= 32)?'height:55px':'height:20px' )+' "><b>Title:</b>'+value.library_title+'</div>'
                    html += '<div style="padding-left:80px;'+( (strlength >= 32)?'height:23px':'height:53px' )+'"><b>Author:</b>'+value.library_author_name+'</div>'
                    html += item_type
                    html += '</div>';
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.accession_no+'</p></div>';
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.library_ddc_number+'</p></div>';
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>'+value.library_author_mark+'</p></div>';
                    html += '<div style="text-align: center" class="col-2 col-sm-2 col-lg-2 form-group"><p>  <button type="button" class="btn btn-primary" onclick="allocation('+value.id+','+value.accession_no+')" >Allocation</button> </p></div>';
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
