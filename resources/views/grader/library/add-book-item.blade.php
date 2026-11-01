
@extends('layouts.app')

@section('title')
    BNU CMS
@stop
@section('content')

<style>
.hidden
{
display:none;
}
.col-75 {
  border-radius: 4px;
}

</style>
<?php

$count_library_accession_number = count($library_accession_number);
$item_typ_array =array("Book", "Reference Book", "Art Catalog", "Report", "Journal");
$library_typ_array =array("Main Library", "SVAD Resource Center","SMMC","DGHC");
$language_array = array("Arabic","English","Pubjabi","Urdu");
$book_status = array("Active","Pending","Lost","Lost and Paid","Hold");
$accession_number = array("1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20");

?>

<div class="row">
    <div class="col-12 col-sm-12 col-lg-12">
        <div class="card"> 
            <div class="card-header" style="background-color:#D2D6F1; color:#6774CF;display:inline">
                <div style="float:left;">
                    <h3><?=isset($library_id)?"Update Book":'Add Book';?></h3>
                    </div>
                    <div style="float:right">
                    <button type="button" class="btn btn-primary" onclick="searchByAll()" >View New Arrival</button>  
                    <button type="button" class="btn btn-primary" onclick="searchByCatalog()" >View Catalog</button>  
                    </div>
            </div>
            <div class="card-body">       
                    <form enctype="multipart/form-data" method="post" name="library_items"  id="library_items">

                    {{ csrf_field() }}                   
                        {{-- First Row --}}
                        <div class="row">
                            <div class="col-3 col-sm-3 col-lg-3 form-group col-75">
                                <label for="school_sel">Purchased By School</label>
                                <select name="school" id="school" class=" form-control">
                                    <option value="">--Select School--</option>
                                    @foreach ($schools as $row) 
                                     <option <?=((isset($view_library[0]->library_purchased_by)?$view_library[0]->library_purchased_by:'') == $row->school_id )? 'selected="selected"' : '' ?> value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                                    @endforeach
                                    <option <?=((isset($view_library[0]->library_purchased_by)?$view_library[0]->library_purchased_by:'') == 13 )? 'selected="selected"' : '' ?> value="13">General Collection</option>
                                </select>
                            </div>
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="items">Item Type</label>
                                <select name="item_type" id="item_type" class=" form-control">
                                    <option value="">--Select Type--</option>    
                                    @foreach ($item_typ_array as $row) 
                                    <option <?=((isset($view_library[0]->library_item_type)?$view_library[0]->library_item_type:'') == $row )? 'selected="selected"' : '' ?> value="{{ $row}}">{{ $row }}</option>
                                    @endforeach                                        
                                </select>
                            </div>
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="library_main">Library</label>
                                <select name="library_main" id="library_main" class=" form-control">
                                    <option value="">--Select Library--</option>
                                    @foreach ($library_typ_array as $row) 
                                    <option <?=((isset($view_library[0]->library_name)?$view_library[0]->library_name:'') == $row )? 'selected="selected"' : '' ?> value="{{ $row}}">{{ $row }}</option>
                                    @endforeach         
                                                           
                                </select>
                            </div>
                            <?php if(isset($library_id)){?>
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="items">Book Status</label>
                                <select name="book_status" id="book_status" class=" form-control">
                                    <option value="">--Select Type--</option>    
                                    @foreach ($book_status as $row) 
                                    <option <?=((isset($view_library[0]->book_status)?$view_library[0]->book_status:'') == $row )? 'selected="selected"' : '' ?> value="{{ $row}}">{{ $row }}</option>
                                    @endforeach                                        
                                </select>
                            </div>
                            <?php } ?>
                       
                        </div>
                        {{-- Second Row --}}
                        <div class="row">
                            <div class="col-6 col-sm-6 col-lg-6 form-group ">
                                <label for="item_title">Item Title<span style="color:red">*</span></label>
                                <input type="text" id="item_title" name="item_title" class="form-control box-shadow required" value ="<?php echo (isset($view_library[0]->library_title))?$view_library[0]->library_title:'';?>"  aria-describedby="itemTitle" placeholder="Item Title">
                            </div>  
                            <div class="col-6 col-sm-6 col-lg-6 form-group">
                                <label for="item_subtitle">Item Sub Title</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_subtitle))?$view_library[0]->library_subtitle:'';?>" id="item_subtitle" name="item_subtitle" aria-describedby="itemSubTitle" placeholder="Item Sub Title">
                            </div>  
                        </div>
                        {{-- Third Row --}}
                        <div class="row">
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="isbn_issn">ISBN / ISSN Number</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_number_issn))?$view_library[0]->library_number_issn:'';?>" id="isbn_issn" name="isbn_issn" aria-describedby="isbnIssn" placeholder="ISBN / ISSN Number">
                            </div>  
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="edition">Edition</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_edition))?$view_library[0]->library_edition:'';?>" id="edition" name="edition" aria-describedby="edition" placeholder="Edition">
                            </div>  
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="ddc">DDC Number<span style="color:red">*</span></label>
                                <input type="text" class="form-control required" value ="<?php echo (isset($view_library[0]->library_ddc_number))?$view_library[0]->library_ddc_number:'';?>" id="ddc" name="ddc" aria-describedby="ddc" placeholder="DDC Number">
                            </div>  
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="language">Language</label>
                                <select name="language" id="language" class=" form-control">
                                    <option value="">--Select Language--</option>
                                    @foreach ($language_array as $row) 
                                    <option <?=((isset($view_library[0]->library_language)?$view_library[0]->library_language:'') == $row )? 'selected="selected"' : '' ?> value="{{ $row}}">{{ $row }}</option>
                                    @endforeach   
                                                                
                                </select>
                            </div>
                        </div>
                        {{-- Fourth Row --}}
                        <div class="row">
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="author-mark">Author Mark<span style="color:red">*</span></label>
                                <input type="text" class="form-control required" value ="<?php echo (isset($view_library[0]->library_author_mark))?$view_library[0]->library_author_mark:'';?>" id="author_mark" name="author_mark" aria-describedby="authorMark" placeholder="Author Mark">
                            </div>  
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="author-name">Author Name</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_author_name))?$view_library[0]->library_author_name:'';?>" id="author_name" name="author_name" aria-describedby="AuthorName" placeholder="Author Name">
                            </div>  
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="sub-author-name">Sub Author Name</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_sub_author_name))?$view_library[0]->library_sub_author_name:'';?>" id="sub_author_name" name="sub_author_name" aria-describedby="subAuthorName" placeholder="Sub Author Name">
                            </div> 
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="volume">Volume</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_volume))?$view_library[0]->library_volume:'';?>" id="volume" name="volume" aria-describedby="volume" placeholder="Volume">
                            </div> 
                        </div>
                         {{-- Fifth Row --}}
                         <div class="row">
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="price-of-item">Price Of Item</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_price))?$view_library[0]->library_price:'';?>" id="price_item" name="price_item" aria-describedby="priceItem" placeholder="Price Of Item">
                            </div>  
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="Publisher">Publisher<span style="color:red">*</span></label>
                                <input type="text" class="form-control required" value ="<?php echo (isset($view_library[0]->library_publisher))?$view_library[0]->library_publisher:'';?>" id="publisher" name="publisher" aria-describedby="publisher" placeholder="Publisher">
                            </div>  
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="place-of-publication">Place of Publication</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_place_publication))?$view_library[0]->library_place_publication:'';?>" id="place_of_publication" name="place_of_publication" aria-describedby="PlaceOfPublication" placeholder="Place of Publication">
                            </div> 
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="Date-of-publication">Year of Publication</label>
                                <input type="text" class="form-control required" value ="<?php echo (isset($view_library[0]->library_date_publication))?$view_library[0]->library_date_publication:'';?>" id="Date_of_publication" name="Date_of_publication" aria-describedby="DateOfPublication" placeholder="Year of Publication">
                            </div>  
                        </div>
                        {{-- Sixth Row --}}
                        <div class="row">
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="Pages">Pages</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_pages))?$view_library[0]->library_pages:'';?>" id="pages" name="pages" aria-describedby="Pages" placeholder="Pages">
                            </div>  
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="Source">Source<span style="color:red">*</span></label>
                                <input type="text" class="form-control required" value ="<?php echo (isset($view_library[0]->library_sources))?$view_library[0]->library_sources:'';?>" id="source" name="source" aria-describedby="Source" placeholder="Source">
                            </div>  
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="Series-Statement">Series Statement</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_series_statement))?$view_library[0]->library_series_statement:'';?>" id="series_statement" name="series_statement" aria-describedby="SeriesStatement" placeholder="Series Statement">
                            </div> 
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="Keyword">Keyword</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_keyword))?$view_library[0]->library_keyword:'';?>" id="keyword" name="keyword" aria-describedby="Keyword" placeholder="Keyword">
                            </div>  
                        </div>
                        {{-- Sixth Row --}}
                        <div class="row">
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="Subject">Subject<span style="color:red">*</span></label>
                                <input type="text" class="form-control required" value ="<?php echo (isset($view_library[0]->library_subject))?$view_library[0]->library_subject:'';?>" id="subject" name="subject" aria-describedby="Subject" placeholder="Subject">
                            </div>  
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="Binding">Binding</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_binding))?$view_library[0]->library_binding:'';?>" id="binding" name="binding" aria-describedby="Binding" placeholder="Binding">
                            </div>  

                            <div class="col-6 col-sm-6 col-lg-6 form-group">
                                <img src="" id="remoteIamgePre" style="width: 75px; height:100px">
                                <input type="hidden" name="remoteIamge" id="remoteIamge">
                            </div> 

                           <div class="col-6 col-sm-6 col-lg-6 form-group">
                                <label for="Attachment">Attachment</label>
                                <?php
                                     if(isset($library_id)) {                                     
                                ?>                                   
                                         <img id="blah" src="<?=url('/library_images').'/'.$view_library[0]->library_attachment?>" width="90" height="70" />
                                <?php
                                      }
                                ?>
                                <input type="file" class="form-control" id="attachment" name="attachment" aria-describedby="Attachment" placeholder="Attachment">
                            </div>       
                         
                            <div class="col-6 col-sm-6 col-lg-6 form-group">
                                <label for="E-Book">E-Book</label> 
                                <?php
                                if(isset($library_id)) {    
                                    if($view_library[0]->libray_ebook != '' && $view_library[0]->libray_ebook != 'No File'){             
                                ?> 
                                 <a style="color:blue" href="/library_images/<?=$view_library[0]->libray_ebook?>" download>
                                    DOWNLOAD PDF
                                  </a>
                                <?php
                                    }
                                }
                                ?>                              
                                <input type="file" class="form-control" id="e_book" name="e_book" aria-describedby="E-Book" placeholder="E-Book">
                            </div>    
                            <div class="col-6 col-sm-6 col-lg-6 form-group">
                                <label for="items">Accession No</label>
                                <select name="accession_num" id="accession_num" onchange="getVal()" class=" form-control">
                                    <option value="">--Select Type--</option>    
                                    @foreach ($accession_number as $row) 
                                    <option <?=((isset($view_library[0]->library_accession_number)?$view_library[0]->library_accession_number:'') == $row )? 'selected="selected"' : '' ?> value="{{ $row}}">{{ $row }}</option>
                                    @endforeach                                        
                                </select>
                            </div>                     
                        </div>
                        <div class="row">
                            <div class="form-group" style="width:1300px;float:left;margin-left:5px">
                                <div  id="abc" style="float:left;margin-left:5px"></div>
                            </div>  
                        </div>
                        
                        <button type="button" onclick="<?=isset($library_id)?"Update_items($library_id)":'insert_items()';?>" class="btn btn-info">{{isset($library_id)?'Update':'Submit'}}</button>
                        <span id="loadingMsg" style="display:none">Please Wait...</span>
                        <span style="color:green;" id="msgSuccess"></span>
                        <span style="color:red;" id="errorSuccess"></span>
                        <?php if(isset($library_id)){?>
                            <button type="button" class="btn btn-primary"  data-toggle="modal" onclick="modeldata('<?=$library_id?>')" data-target="#exampleModalCenter">Accession NO</button>

                            <input type="hidden" value="{{$view_library[0]->library_attachment}}" id="libraryImg" name="libraryImg">
                        <input type="hidden" value="{{$view_library[0]->libray_ebook}}" id="libraryEbook" name="libraryEbook">
                        <?php } ?>
                    </form>
                </div>
            </div>
			
            <input type="hidden" value="<?=$library_id?>" id= "library_ids">
            <input type="hidden" value="<?=$count_library_accession_number?>" id= "count_accession_num">
		
        </div>

 <!--- Remote search -->
 <div class="col-12 col-sm-12 col-lg-12" style="margin-top: 20px;">
<?php if(!isset($library_id)){?>
<div class="card">
    <div class="card-header" style="background-color:#D2D6F1; color:#6774CF;display:inline"> <h3>Remote Search</h3> </div>

    <div class="card-body">
        <div class="row">
          
            <div class="col-4 col-sm-4 col-lg-4">
               <div class="form-group">
                    <label for="searchBookTitle">Book Title</label>
                    <input type="email" class="form-control" id="searchBookTitle" placeholder="Book Title">
                </div> 
            </div>

            <div class="col-4 col-sm-4 col-lg-4">
               <div class="form-group">
                    <label for="searchBookIsbn">Book ISBN Number</label>
                    <input type="email" class="form-control" id="searchBookIsbn" placeholder="Book ISBN">
                </div> 
            </div>

            <div class="col-4 col-sm-4 col-lg-4">
               <div class="form-group">
                    <label for="searchBookAuthor">Book Author</label>
                    <input type="email" class="form-control" id="searchBookAuthor" placeholder="Book Author">
                </div> 
            </div>

            <div class="col-12 col-sm-12 col-lg-12">
                <div class="form-group">
                    <label for="searchBookAuthor">Remote Source</label>
                    <div class="form-group">
                        <label style="margin-left: 10px;" class="form-check-label" for="remote_source_google"> &nbsp <input class="form-check-input" type="radio" name="remote_source" id="remote_source_google" value="google">  &nbsp Google  </label>
                        <label style="margin-left: 20px;" class="form-check-label" for="remote_source_loc">  &nbsp <input class="form-check-input" type="radio" name="remote_source" id="remote_source_loc" value="loc">  &nbsp Library Of Congress </label>
                    </div>
                </div>
            </div>      

            <div class="col-12 col-sm-12 col-lg-12">
                <div class="form-group">
                    <button type="submit" id="search-btn" class="btn btn-primary" style="width: 100%;"  onclick="getBookData(0)">Search Book</button>
                    <small class="form-text text-muted"></small>
                </div>
            </div>


        </div>
    </div>

    <div class="card-header"> <h3>Results</h3> </div>
    <div class="card-body"> <table id="remote-data-table">  </table> </div>
    <div class="card-body" id="result_pagination"></div>
</div>  </div>
<!-- REMOTE SEARCH END  -->
    </div>

</div>
<?php } ?>

 <!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Update Accession No</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                  <input type="hidden" class="form-control" name="library_books_id" id="library_books_id">     
               
                  <form enctype="multipart/form-data" method="post"  name="update_acc_no" id="update_acc_no">
                @csrf       
                <div class="form-group row ">   
                    <div class="col-10">
                    <input type="text" class="form-control" name="acc_no[]" class="acc" placeholder="accession#1">
                </div>        
                
                </div>   
                <div class="form-group row ">   
                    <div class="col-10">
                    <input type="text" class="form-control" name="acc_no[]" class="acc" placeholder="accession#2">
                </div>        
              
                </div>   
                <div class="form-group row ">   
                    <div class="col-10">
                    <input type="text" class="form-control" name="acc_no[]" class="acc" placeholder="accession#3">
                </div>        
              
                </div>  
                <div class="form-group row ">   
                    <div class="col-10">
                    <input type="text" class="form-control" name="acc_no[]" class="acc" placeholder="accession#4">
                </div>        
                
                </div>  
                <div class="form-group row ">   
                    <div class="col-10">
                    <input type="text" class="form-control" name="acc_no[]" class="acc" placeholder="accession#5">
                </div>        
               
                </div>    
                </form>
            </div>
            <div class="modal-footer">     
                <div id="msg-span"></div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="UpdateBtn()">Update</button>         
            </div>
        </div>
    </div>
</div>
<!-- Modal Ends Here -->


@stop
@section('javascript')
<script type="text/javascript">
   function modeldata(id){
     $('#library_books_id').val(id);
    }
    function UpdateBtn(){
        var id =  $('#library_books_id').val();
       var bi_form = document.querySelector('#update_acc_no');
        var fd = new FormData(bi_form);
        var url = "{{URL::to('library/insert-accession-no')}}/"+id;
        
        $.ajax({
                type:'POST',
                url: url,
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){
                
                 console.log(data.message);
                 if(!data.status){
                    $('#msg-span').html(data.message);

                 }
                if(data == 1){
                    $("#update_acc_no")[0].reset();
                    $('#msg-span').html('Data insert Successfully');
                   
                    setInterval(function() {
                        $('#exampleModalCenter').modal('hide');
                        location.reload();
                    
                    }, 3000);
                }
           
                },
                error: function (jqXHR, exception) {
      
     console.log(jqXHR);
                },               
            });

        }


    function searchByAll(){
         window.location = '<?=url('library/view-book-item')?>';
    }
    function searchByCatalog(){
        window.location = '<?=url('library/catalog')?>';
    }
    $( document ).ready(function() {
        if($('#library_ids').val()){               
                var array = JSON.parse('<?=$library_accession_number?>');                          
                $.each(array, function( index, value ) {   
                              console.log(value)         
                 
                    var input  = '<input type="text" class="form-control"  id="access_no" readonly name="access_no[]" value='+value.accession_no+' aria-describedby="accessionNumber" >';
                    input     += '<input type="hidden" readonly class="form-control"  id="access_id" name="access_id[]" value='+ value.lan_id+'>'
                    
                    $('#abc').append('<div style="float:left; margin-left:5px;" class="form-group">'+input+'</div');              
                });                  
        }
    });
    function getVal() {

        $('#abc').html('');
        var access = $('#accession_num').val();
        var i;
        for (let i = 1; i <= access; i++) {
            console.log(i);
            var placeholder = 'Accession#'
            var res = placeholder.concat(i);
            var value = 'value ="<?php echo (isset($view_library[0]->library_binding))?$view_library[0]->library_binding:'';?>';
            var input  = '<input type="text" class="form-control"  id="access_no" name="access_no[]" aria-describedby="accessionNumber" placeholder='+res+'>';//<input type="text" class="form-control"  id="abcd" name="abcd" aria-describedby="accessionNumber" placeholder="Accession Number :'+i+'>';
            $('#abc').append('<div style="float:left; margin-left:5px;" class="form-group">'+input+'</div');
           // $('#abc').append("<div style='float:left; margin-left:5px;' class='form-group'> <input type='text' class='form-control'  id='abcd' name='abcd' aria-describedby='accessionNumber' placeholder='Accession Number :"+i+"></div>  ")	
        }
      
    }  

    function insert_items() {

        $('#loadingMsg').show();
        $('.required').removeClass('border-danger');
        $('.required').each(function(index,ele){
            var text = $(ele).val();
            if(text == ''){
                $(ele).addClass('border-danger');
            }
        });

     
        var bi_form = document.querySelector('#library_items');
        var fd = new FormData(bi_form);
        var url = '<?=url("library/insert-library-items")?>';
        $.ajax({
                type:'POST',
                url: url,
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){
                 
                    if(data.status){
                        $('#loadingMsg').hide();
                        $('#errorSuccess').hide();
                        $('#msgSuccess').html(data.message);
                       // $("#library_items")[0].reset();
                     
                    }else{    
                        //console.log(data)                   
                       $('#errorSuccess').html(data.message).focus();
                    }
                },
                error: function (jqXHR, exception) {
                  alert(jqXHR+' ======  '+exception)
                   $('#msgSuccess').hide();
        var msg = '';
        if (jqXHR.status === 0) {
            $('#loadingMsg').hide();
            msg = 'Check ur Internet Connection';
        }else if (exception === 'error') {
            $('#loadingMsg').hide();
            msg = 'Accession no already added.';
        } else if (jqXHR.status == 404) {
            $('#loadingMsg').hide();
            msg = 'Requested page not found. [404]';
        } else if (jqXHR.status == 500) {
            $('#loadingMsg').hide();
            msg = 'Data Not Updated, Please Contact ITRC';
        } else if (exception === 'parsererror') {
            $('#loadingMsg').hide();
            msg = 'Requested JSON parse failed.';
        } else if (exception === 'timeout') {
            $('#loadingMsg').hide();
            msg = 'Time out error.';
        } else if (exception === 'abort') {
            $('#loadingMsg').hide();
            msg = 'Ajax request aborted.';
        } else {
            $('#loadingMsg').hide();
            msg = 'Uncaught Error.\n' + jqXHR.responseText;
        }
        $('#errorSuccess').html(msg);
    },
               
            });
    }
    function Update_items(id) {
        // alert(id);
        // return false;
        $('#loadingMsg').show();
var bi_form = document.querySelector('#library_items');
var fd = new FormData(bi_form);
var url = '<?=url("library/update-library-items")?>/'+id;
$.ajax({
        type:'POST',
        url: url,
        data:fd,
        processData:false,
        contentType:false,
        success:function(data){
           // console.log(data)
            if(data.status){
                $('#loadingMsg').hide();
                $('#msgSuccess').html(data.message);
                $("#library_items")[0].reset();
                location.reload();
            }else{
                $('#msgSuccess').html('Data Not Updated');
            }
        },
       
      error: function (jqXHR, exception) {
      
        var msg = '';
        if (jqXHR.status === 0) {
            $('#loadingMsg').hide();
            msg = 'Check ur Internet Connection';
        } else if (jqXHR.status == 404) {
            $('#loadingMsg').hide();
            msg = 'Requested page not found. [404]';
        } else if (jqXHR.status == 500) {
            $('#loadingMsg').hide();
            msg = 'Data Not Updated, Please Contact ITRC';
        } else if (exception === 'parsererror') {
            $('#loadingMsg').hide();
            msg = 'Requested JSON parse failed.';
        } else if (exception === 'timeout') {
            $('#loadingMsg').hide();
            msg = 'Time out error.';
        } else if (exception === 'abort') {
            $('#loadingMsg').hide();
            msg = 'Ajax request aborted.';
        } else {
            $('#loadingMsg').hide();
            msg = 'Uncaught Error.\n' + jqXHR.responseText;
        }
        $('#errorSuccess').html(msg);
    },
 
    });
}


///-----------------------///-----------------------///-----------------------///-----------------------///-----------------------///-----------------------///-----------------------///-----------------------///
var dataArr = [];

    function selectBook(index) {
        var book = dataArr[index];
        console.log('select book ', book);
        $('#item_title').val(book.title);
        $('#item_subtitle').val(book.subTitle);
        $('#author_name').val(book.author);
        $('#publisher').val(book.publisher);

        $('#remoteIamge').val(book.image);
        $('#remoteIamgePre').attr('src',book.image);

        $('#bookDescription').val(book.description);
        // isbn_issn
        $('#isbn_issn').val(book.isbn);
    }

    function createRecordRowByObject(obj) {
        var html = '<tr>';
            html += '<td><img width="75px" heigth="100px" src="'+obj.image+'" ' + obj.title + ' /></td>';
            html += '<td>' + obj.title + '</td>';
            html += '<td>' + obj.author + '</td>';
            html += '<td>' + obj.publisher + '</td>';
            html += '<td><button class="btn btn-primary" style="width: 100%" onclick="selectBook('+obj.index+')">Select Book</button></td>';
            html += '</tr>';
        return html;
    }

    function bookObjFromGoogleItem(obj,index) {

        var author = '';
        if(obj.volumeInfo.authors){
            author = obj.volumeInfo.authors.join();
        }

        var isbn = '';
        if( (obj.volumeInfo.industryIdentifiers) && obj.volumeInfo.industryIdentifiers.length > 0){
            isbn = obj.volumeInfo.industryIdentifiers[0].identifier;
        }
        
        var iamge = (obj.volumeInfo.imageLinks.thumbnail)?obj.volumeInfo.imageLinks.thumbnail:'';
        // (typeof obj.volumeInfo.imageLinks.smallThumbnail !== 'undefined')?obj.volumeInfo.imageLinks.smallThumbnail: ''; 
        //  { alert('please select the remote source.'); return false; }
       //  if (typeof source === 'undefined'){ alert('please select the remote source.'); return false; }

        return {
            'index':index,
            'title': obj.volumeInfo.title,
            'subTitle': obj.volumeInfo.subtitle,
            'description': obj.volumeInfo.description,
            'author': author,
            'image': iamge,
            'publisher': obj.volumeInfo.publisher,
            'publishedDate': obj.volumeInfo.publishedDate,
            'isbn' : isbn,
            'pages' : '',
        }
    }



// https://libraryofcongress.github.io/data-exploration/requests.html
    function bookObjFromLOC(obj,index) {
        return {
            'index':index,
            'title': obj.title,
        //    'subTitle': (obj.subject.length > 0)?obj.subject[0]:'',
            'description': obj.description,
            'author': '', //obj.volumeInfo.authors.join(),
        //    'image': (obj.image_url.length > 0)? obj.image_url[0] : '' ,
            'publisher': '', // obj.volumeInfo.publisher,
            'publishedDate': obj.date,
        }
    }


    function createPagination(paginationObj){
        var pagination = '<nav aria-label="Page navigation example"><ul class="pagination justify-content-center">'; 
        $(paginationObj).each(function(index, pageObj) {
            pagination += '<li class="page-item"><a href="javascript:void(0);" class="page-link" onclick="getBookData(\''+pageObj.url+'\')" class="btn btn-primary" >'+pageObj.number+'</a></li>';
        });
        pagination += '</ul></nav>';
        $('#result_pagination').html(pagination);
    }

    function getBookData(paginationURL) {

        $('#result_pagination').html('');

        var url     = '';
        var source  = $('input[name="remote_source"]:checked').val();
        var isbn    = $('#searchBookIsbn').val();
        var title   = $('#searchBookTitle').val();
        var author  = $('#searchBookAuthor').val();

       
       //  if (title == ''){ alert('please provide title.'); return false; }
        if (typeof source === 'undefined'){ alert('please select the remote source.'); return false; }

        $('#search-btn').html('Sending....');
        if(source == 'google'){
            if(isbn != ''){
                url = "https://www.googleapis.com/books/v1/volumes?q=isbn:" + isbn + "";
            }else if(title != ''){
                url = "https://www.googleapis.com/books/v1/volumes?q=title:" + title + "";
            }else if(author != ''){
                url = "https://www.googleapis.com/books/v1/volumes?q=author:" + author + "";
            }
        }else if(source == 'loc'){
                url = "https://www.loc.gov/books/?q="+title+"&fa=original-format:book&fo=json&c=50";
        }

        if(paginationURL != 0){
            url = paginationURL;
        }

        console.log('----',paginationURL);

        var query = $("#queryInput").val();
        $.ajax({
            type: 'GET',
            url:url,
            success: function(data) {
                dataArr = [];
                $('#search-btn').html('Search Book');
                var tableData = '';
                if(source == 'google'){
                    if (data.items.length > 0) {
                        
                        $(data.items).each(function(index, dataSet) {
                            var dd = bookObjFromGoogleItem(dataSet,index);
                            dataArr.push(dd);
                            tableData += createRecordRowByObject(dd);
                        });
                      //  $('#remote-data-table').html(tableData);
                    }else{
                        tableData = '<tr><td>No Data Found!</td></tr>';
                    }
                }else if(source == 'loc'){
                    if (data.results.length > 0) {

                        var tableData = '';
                        $(data.results).each(function(index, dataSet) {
                            var dd = bookObjFromLOC(dataSet,index);
                            dataArr.push(dd);
                            tableData += createRecordRowByObject(dd);
                        });

                        
                        createPagination(data.pagination.page_list);
                        

                    }else{
                        tableData = '<tr><td>No Data Found!</td></tr>';
                    }
                }

                $('#remote-data-table').html(tableData);               
                
            }
        }).fail(function( jqXHR, textStatus ) {
            $('#search-btn').html('Search Book');
            $('#remote-data-table').html('<tr><td>Request Fail. With response '+textStatus+'</td></tr>');
        });
    }
</script>
@stop