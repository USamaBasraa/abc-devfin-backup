@extends('layouts.master')
@section('title')
BNU CMS
@stop
@section('mainBody')
<?php
$item_typ_array =array("Book", "Reference Book", "Art Catalog", "Report", "Journal");
$library_typ_array =array("Main Library", "SVAD Resource Center");
$language_array = array("Arabic","English","Pubjabi","Urdu");

?>

<div class="row">
    <div class="col-12 col-sm-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3>Add Book</h3>
            </div>
            <div class="card-body">       
                    <form enctype="multipart/form-data" method="post" name="library_items"  id="library_items">
                    {{ csrf_field() }}
                        {{-- First Row --}}
                        <div class="row">
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="school_sel">Purchased By School</label>
                                <select name="school" id="school" class=" form-control">
                                    <option value="">--Select School--</option>
                                    @foreach ($schools as $row) 
                                     <option <?=((isset($view_library[0]->library_purchased_by)?$view_library[0]->library_purchased_by:'') == $row->school_id )? 'selected="selected"' : '' ?> value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                                    @endforeach
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
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="accession_number">Accession Number<span style="color:red">*</span></label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_accession_number))?$view_library[0]->library_accession_number:'';?>" id="accession_number" name="accession_number" aria-describedby="accessionNumber" placeholder="Accession Number" >
                            </div>          
                        </div>
                        {{-- Second Row --}}
                        <div class="row">
                            <div class="col-6 col-sm-6 col-lg-6 form-group">
                                <label for="item_title">Item Title<span style="color:red">*</span></label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_title))?$view_library[0]->library_title:'';?>" id="item_title" name="item_title" aria-describedby="itemTitle" placeholder="Item Title">
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
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_ddc_number))?$view_library[0]->library_ddc_number:'';?>" id="ddc" name="ddc" aria-describedby="ddc" placeholder="DDC Number">
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
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_author_mark))?$view_library[0]->library_author_mark:'';?>" id="author_mark" name="author_mark" aria-describedby="authorMark" placeholder="Author Mark">
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
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_publisher))?$view_library[0]->library_publisher:'';?>" id="publisher" name="publisher" aria-describedby="publisher" placeholder="Publisher">
                            </div>  
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="place-of-publication">Place of Publication</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_place_publication))?$view_library[0]->library_place_publication:'';?>" id="place_of_publication" name="place_of_publication" aria-describedby="PlaceOfPublication" placeholder="Place of Publication">
                            </div> 
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="Date-of-publication">Year of Publication</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_date_publication))?$view_library[0]->library_date_publication:'';?>" id="Date_of_publication" name="Date_of_publication" aria-describedby="DateOfPublication" placeholder="Year of Publication">
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
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_sources))?$view_library[0]->library_sources:'';?>" id="source" name="source" aria-describedby="Source" placeholder="Source">
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
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_subject))?$view_library[0]->library_subject:'';?>" id="subject" name="subject" aria-describedby="Subject" placeholder="Subject">
                            </div>  
                            <div class="col-3 col-sm-3 col-lg-3 form-group">
                                <label for="Binding">Binding</label>
                                <input type="text" class="form-control" value ="<?php echo (isset($view_library[0]->library_binding))?$view_library[0]->library_binding:'';?>" id="binding" name="binding" aria-describedby="Binding" placeholder="Binding">
                            </div>  
                           <div class="col-6 col-sm-6 col-lg-6 form-group">
                                <label for="Attachment">Attachment</label>
                                <?php
                                     if(isset($library_id)) {                                     
                                ?>                                   
                                         <img id="blah" src="<?=url('/images').'/'.$view_library[0]->library_attachment?>" width="90" height="70" />
                                <?php
                                      }
                                ?>
                                <input type="file" class="form-control" id="attachment" name="attachment" aria-describedby="Attachment" placeholder="Attachment">
                            </div>   
                            <div class="col-6 col-sm-6 col-lg-6 form-group">
                                <label for="E-Book">E-Book</label>                               
                                <input type="file" class="form-control" id="e_book" name="e_book" aria-describedby="E-Book" placeholder="E-Book">
                            </div>                         
                        </div>
                        <button type="button" onclick="<?=isset($library_id)?"Update_items($library_id)":'insert_items()';?>" class="btn btn-info">{{isset($library_id)?'Update':'Submit'}}</button>
                        <span style="color:green;" id="msgSuccess"></span>
                        <span style="color:red;" id="errorSuccess"></span>
                    </form>
                </div>
            </div>


        </div>





    <!--- Remote search -->
    <div class="col-12 col-sm-12 col-lg-12" style="margin-top: 20px;">

        <div class="card">
            <div class="card-header"> <h3>Remote Search</h3> </div>

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

@stop

@section('pageScripts')
<script>
    function insert_items() {
     
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
                        $('#msgSuccess').html(data.message);
                        $("#library_items")[0].reset();
                     
                    }else{    
                        //console.log(data)                   
                       $('#errorSuccess').html(data.message).focus();
                    }
                },
                complete: function(){
                }
            });
    }
    function Update_items(id) {
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
                $('#msgSuccess').html(data.message);
                $("#library_items")[0].reset();
                location.reload();
            }else{
                $('#msgSuccess').html('Data Not Updated');
            }
        },
        complete: function(){
        }
    });
}


///-----------------------///-----------------------///-----------------------///-----------------------///-----------------------///-----------------------///-----------------------///-----------------------///
var dataArr = [];

    function selectBook(index) {
        var book = dataArr[index];
        $('#bookTitle').val(book.title);
        $('#bookSubTitle').val(book.subTitle);
        $('#bookAuthor').val(book.author);
        $('#bookPublisher').val(book.publisher);
        $('#bookDescription').val(book.description);
    }

    function createRecordRowByObject(obj) {
        // var html = '<tr><td>';
        //     html += '<p><img src="' + obj.image + '" > </p>';
        //     html += '<p>Title & Sub Title</p>';
        //     html += '<b>' + obj.title + '</b>';
        //     html += '<b>' + obj.subTitle + '</b>';
        //     html += '<hr>';
        //     html += '<p>Author & publisher</p>';
        //     html += '<b>' + obj.author + '</b>';
        //     html += '<b>' + obj.publisher + '</b>';
        //     html += '<hr>';
        //     html += '<p>published Date</p>';
        //     html += '<b>2016-12-15</b>';
        //     html += '<hr>';
        //     html += '<button class="btn btn-primary" style="width: 100%" onclick="selectBook('+obj.index+')">Select Book</button>';
        //     html += '</td></tr>';

        // return html;

        // --------------------------------------------------------------------------------------------

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

        return {
            'index':index,
            'title': obj.volumeInfo.title,
            'subTitle': obj.volumeInfo.subtitle,
            'description': obj.volumeInfo.description,
            'author': author,
            'image': obj.volumeInfo.imageLinks.smallThumbnail,
            'publisher': obj.volumeInfo.publisher,
            'publishedDate': obj.volumeInfo.publishedDate,
        }
    }
// https://libraryofcongress.github.io/data-exploration/requests.html
    function bookObjFromLOC(obj,index) {
        console.log('------',obj);
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