@extends('layouts.master')
@section('title')
BNU CMS
@stop
@section('mainBody')
<!-- Grid row -->

<!-- 


<div class="card-header">
    <p> 45 Records Founds </p>
    <p><button class="btn btn-primary">Show Records</button></p>
</div>
<div class="card-body record-container">
    <div class="row">
        <div class="col-12 col-sm-12 col-lg-12">
        
            <table id="remote-data-table">
            
            <tr>
                <td>
                    <img src="http://books.google.com/books/content?id=qn1mDwAAQBAJ&printsec=frontcover&img=1&zoom=5&edge=curl&source=gbs_api">
                    
                </td>
                <td>
                    <p><strong>Steave Jobs</strong></p>
                    <p><b>Computer Visionary</b></p>
                    <p>Steve Jobs is a controversial figure who is regarded with awe by some and disgust by others, but he inarguably changed the faces of technology and entertainment with his involvement in Apple and Pixar. This volume presents both sides of the debate, citing Jobs’s proponents and detractors, allowing readers to come to their own conclusions about his actions. Detailed sidebars, a fact-filled timeline, and full-color photographs augment the engaging discussion</p>
                    <p><button class="btn btn-primary" onclick="" >Select Book</button></p>
                </td>
                <td width="150px">
                    <b>Author</b>
                    <p>Ahmed Shabbir</p>
                    <hr>
                    <b>publisher</b>
                    <p>Greenhaven Publishing LLC</p>
                    <hr>
                    <b>published Date</b>
                    <p>2016-12-15</p>
                    <hr>
                </td>
            </tr>

            </table>
        </div>
    </div> 
</div>

-->

<div class="row">
    <div class="col-7 col-sm-7 col-lg-7">
        <div class="card">
            <div class="card-header">
                <h3>Add Book</h3>
            </div>

            <div class="card-body">
                <div class="col-lg-12">
                    <form>
                        <div class="form-group">
                            <label for="bookTitle">Book Title</label>
                            <input type="input" class="form-control" id="bookTitle"  placeholder="book Title">
                            <small id="emailHelp" class="form-text text-muted"></small>
                        </div>

                        <div class="form-group">
                            <label for="bookSubTitle">Sub Title</label>
                            <input type="input" class="form-control" id="bookSubTitle"  placeholder="Sub Title">
                            <small id="emailHelp" class="form-text text-muted"></small>
                        </div>

                        <div class="form-group">
                            <label for="bookDescription">Description</label>
                            <textarea class="form-control" id="bookDescription" rows="3"></textarea>
                            <small id="bookDescription" class="form-text text-muted"></small>
                        </div>

                        <div class="form-group">
                            <label for="bookAuthor">Author Name</label>
                            <input type="input" class="form-control" id="bookAuthor"  placeholder="book Author">
                            <small id="emailHelp" class="form-text text-muted"></small>
                        </div>

                        <div class="form-group">
                            <label for="bookAuthor">Publisher</label>
                            <input type="input" class="form-control" id="bookPublisher"  placeholder="book Publisher">
                            <small id="bookPublisher" class="form-text text-muted"></small>
                        </div>

                       

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>


        </div>
    </div>

    <!--- Remote search -->
    <div class="col-5 col-sm-5 col-lg-5">

        <div class="card">
            <div class="card-header">
                <h3>Remote Search</h3>
            </div>

            <div class="card-body">
                <div class="row">
                  



                    <div class="col-12 col-sm-12 col-lg-12">
                       <div class="form-group">
                            <label for="searchBookTitle">Book Title</label>
                            <input type="email" class="form-control" id="searchBookTitle" placeholder="Book Title">
                        </div> 
                    </div>

                    <div class="col-12 col-sm-12 col-lg-12">
                       <div class="form-group">
                            <label for="searchBookIsbn">Book ISBN Number</label>
                            <input type="email" class="form-control" id="searchBookIsbn" placeholder="Book ISBN">
                        </div> 
                    </div>

                    <div class="col-12 col-sm-12 col-lg-12">
                       <div class="form-group">
                            <label for="searchBookAuthor">Book Author</label>
                            <input type="email" class="form-control" id="searchBookAuthor" placeholder="Book Author">
                        </div> 
                    </div>


                
                    <div class="col-12 col-sm-12 col-lg-12">
                        <div class="form-group">
                            <label for="searchBookAuthor">Remote Source</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="remote_source" id="remote_source_google" value="google">
                                <label class="form-check-label" for="remote_source_google"> Google  </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="remote_source" id="remote_source_loc" value="loc">
                                <label class="form-check-label" for="remote_source_loc"> Library Of Congress </label>
                            </div>
                        </div>
                    </div>
              

                    <div class="col-12 col-sm-12 col-lg-12">
                        <div class="form-group">
                            <button type="submit" id="search-btn" class="btn btn-primary" style="width: 100%;"  onclick="getBookData()">Search Book</button>
                            <small class="form-text text-muted"></small>
                        </div>
                    </div>


                </div>
            </div>


            <div class="card-header">
                <h3>Results</h3>
            </div>

            <div class="card-body">
                <table id="remote-data-table">  </table>
            </div>


        </div>

    </div>

    <!-- REMOTE SEARCH END  -->






</div>

@stop
@section('pageScripts')
<script>
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
            html += '<td>' + obj.title + '</td>';
            html += '<td>' + obj.author + '</td>';
            html += '<td>' + obj.publisher + '</td>';
            html += '<td><button class="btn btn-primary" style="width: 100%" onclick="selectBook('+obj.index+')">Select Book</button></td>';
            html += '</tr>';

        return html;

    }

    function bookObjFromGoogleItem(obj,index) {
        return {
            'index':index,
            'title': obj.volumeInfo.title,
            'subTitle': obj.volumeInfo.subtitle,
            'description': obj.volumeInfo.description,
            'author': obj.volumeInfo.authors.join(),
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




    function getBookData() {

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
                url = "https://www.loc.gov/search/?q="+title+"&fo=json&c=50";
                // books

                url = "https://www.loc.gov/books/?q="+title+"&fa=original-format:book&fo=json&c=50";


        }

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
                        // $('#remote-data-table').html(tableData);
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