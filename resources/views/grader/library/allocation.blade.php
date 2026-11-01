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
    $bookStatus = array("Active","Pending","Update");
    ?>
<div class="row">
    <div class="col-12 col-sm-12 col-lg-12">
        <div class="card">
            <div class="card-header" style="background-color: #2F3B4D;color:white" >
                <div style="float:left;">
                    <h3 style="font-weight: bold">Circulation</h3>
                </div>
                <div style="float:right">
                    {{-- <button type="button" class="btn btn-primary" onclick="searchByCatalog()" >View Circulation</button> --}}
                </div>
            </div>
            <div class="card-body">

                <form enctype="multipart/form-data" method="post" name="ff"  id="ff">
                    {{ csrf_field() }}
               @csrf
                    <div class="row"  >
                        <div  class="col-4 col-sm-4 col-lg-4 form-group">
                            <label class="form-control">User ID</label>
                            <input class="form-control" type="text" id="sai_enrollment_no" name="sai_enrollment_no">

                        </div>

                    </div>

                    <input class=" form-control" type="hidden" id="library_book_id" name="library_book_id" value="123">
                            <input class=" form-control" type="hidden" id="access_no" name="access_no" value="123">
                    <div class="row" >
                        <div class="col-12 col-sm-12 col-lg-12 form-group">
                            <button type="button" class="btn btn-primary" onclick="loadDetails()" >Load Details</button>
                        </div>
                    </div>
                </form>
            <div>
        </div>
    </div>
</div>

<div id="showbooks" style="margin-top:20px"></div>
<span id="bookissuedmain" style="color:green"></span>
<span id="booklimitmain" style="color:red"></span>

<button style="width:1252px;display:none" id="issueBtn" type="button" class="btn btn-primary" onclick="issueBook()" >Issue Book</button>

@stop


@section('javascript')
<script type="text/javascript">

   function searchByCatalog(){
        window.location = '<?=url('library/circulation')?>';
    }
function loadDetails(){

    if($('#sai_enrollment_no').val() == ''){
        $("#sai_enrollment_no").focus();
        $("#sai_enrollment_no").css("border","1px solid red");
    }else{
        $("#sai_enrollment_no").css("border","1px solid blue");
    $('#issueBtn').show();


    var url = '<?=url("library/allocation-load-details")?>/'+$('#sai_enrollment_no').val()+'/'+$('#library_book_id').val()+'/'+$('#access_no').val();
    $.ajax({
        type:'get',
        url: url,
        // data:fd,
        processData:false,
        contentType:false,
        success:function(data){
            $("#showbooks").html(data);

        },
        error: function (jqXHR, exception) {
        },
    });
    }
}
function issueBook(){
     //alert($('#issue_date').val());
     //return false;
    // alert($('#due_date').val());
    // return false;
    if($('#issue_date').val() == ''){
        $("#issue_date").focus();
        $("#issue_date").css("border","1px solid red");
    }else{
    var bi_form = document.querySelector('#ff');
    var fd = new FormData(bi_form);
    var url = '<?=url("library/issue-book")?>/'+$('#issue_date').val()+'/'+$('#accession_no').val()+'/'+$('#book_id').val();
    $.ajax({
        type:'POST',
        url: url,
        data:fd,
        processData:false,
        contentType:false,
        success:function(data){

           if(data.status){
            $('#booklimitmain').html('');
            $('#bookissuedmain').html(data.message);
            setTimeout(function(){
                $('#bookissuedmain').html('');
            }, 4000);


           loadDetails();
           }else{
            $('#bookissuedmain').html('');
            $('#booklimitmain').html(data.message);
            setTimeout(function(){
                $('#booklimitmain').html('');
            }, 4000);


            loadDetails();

           }

        },
        error: function (jqXHR, exception) {
        },
    });
    }
}
function searchByAccession(){


    var str =  $('#searchAccession').val();

  if(str.length == 3 || str.length == 4 || str.length == 5){
    var bi_form = document.querySelector('#search_form');
        var fd = new FormData(bi_form);
        var url = '<?=url("library/search-from-allocation")?>/'+str;
        $.ajax({
                type:'POST',
                url: url,
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){
            console.log(data.length)
            if(data.length > 0){
                //$('#hideView').hide();
                var html = '';
                $.each(data,function( key, value ) {
                    console.log(value)

                    html +=  '<td  width="200">'+value.library_title+'</td>';
                    html +=  '<td   width="200">'+value.library_author_name+'</td>'
                    html +=  '<td   width="200">'+value.accession_no+'</td>';
                    html +=  '<td   width="200"><input type="hidden" id= "issue_date" value="<?php echo date('Y-m-d');?>"><?php echo date('Y-m-d');?><input type="hidden" id= "accession_no" value="'+value.accession_no+'"><input type="hidden" id= "book_id" value="'+value.id+'"></td></tr>';



                });

                if(html != ''){
                   $('#result-body').html(html);
                }else{
                   $('#result-body').html('No Result Found!');
                }
            }else{
                alert('no data found')
                // $('#hideView').hide();
                // $('#result-body').html('No Result Found!');
            }
        },
                error: function (jqXHR, exception) {


    },

            });

  }
}

function status(sel){
    var value = sel.value;
   // alert(value);
    if(value == 'Issued'){
        $('#issue-div').show();
    }else{
        $('#issue-div').hide();
    }
}
function finecal(){

    var date = new Date($('#dt').val());
    var day = date.getDate();
  var month = date.getMonth() + 1;
  var year = date.getFullYear();
  var arr=[year, month, day].join('/');
  var issueDate=$('#issue-date').val();

var date1 = new Date(issueDate);
var date2 = new Date(arr);

const diffTime = (date2 - date1);
const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
    if(diffDays>0){
    const fine=diffDays*10;
    $('#fine').val(fine);
    $('#dt').removeClass('is-invalid');
    }else{
        $('#fine').val(0);
        $('#dt').removeClass('is-invalid');

    }


    // if(diffDays < 0){
    //     alert('You Cannot enter less then Issue Date');
    //     $('#dt').addClass('is-invalid');
    //     $('#fine').val(0);
    // }

console.log(diffDays + " days");

}



  function checkval(user_id,book_name,book_id,fine,id,date,issueDays,accession_no){
    $('#book-id').val(book_id);
    $('#book-name').val(book_name);
    $('#user-id').html(user_id);
    $('#fine').val(fine);
    $('#id').val(id);
    $('#user-idd').val(user_id);
    $('#issue-date').val(date);
    $('#issue-days').val(issueDays);
    $('#accession-no').val(accession_no);
  }
  //$("#btn").click(function(e){
      function saveChanges(){
      // alert('bilal');
       //return false;
         //   e.preventDefault();

            var book=$('select[name=book-status] option').filter(':selected').val()

                    var date = new Date($('#dt').val());
                    var day = date.getDate();
                    var month = date.getMonth() + 1;
                    var year = date.getFullYear();
                    var arr=[year, month, day].join('/');

                    // if(isNaN(arr)){
                    //     alert(arr);
                    //     $('#dt').addClass('is-invalid');
                    //     $('#result-body').html('Please enter Return Date');
                    //     return false;
                    // }else{
                    //     alert('dd11');
                    //     $('#dt').removeClass('is-invalid');
                    //     $('#result-body').html('');
                    // }
                    if($('#issue').val() == '' && book =='Issued'){
                      $("#issue").focus();
                      $("#issue").css("border","1px solid red");
                      return false;
                    }

                if(book==''){
                    $('#bookstatus').addClass('is-invalid');
                    return false;
                }else if(book=='reissue'){
                    var issuedate = new Date($('#issue').val());
                    var day = issuedate.getDate();
                    var month = issuedate.getMonth() + 1;
                    var year = issuedate.getFullYear();
                    var arr=[year, month, day].join('/');
                    // if(isNaN(arr)){
                    //     $('#issue').addClass('is-invalid');
                    //     $('#result-body').html('Please enter Issue Date');
                    //     return false;
                    // }
                }


            var bi_form = document.querySelector('#return-book-form');
            var fd = new FormData(bi_form);
            $.ajax({
                type:'POST',
                url: '{{url('library/user-return-book')}}',
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){

        if(data.status){
            $('#booklimit').html('');
            $('#bookissued').html(data.message);
            setTimeout(function(){
                $('#bookissued').html('');
                $('#exampleModalCenter').modal('hide');
                $("#return-book-form")[0].reset();
                location.reload();
            }, 4000);

           }else{
            $('#bookissued').html('');
            $('#booklimit').html(data.message);
            setTimeout(function(){
                $('#booklimit').html('');
                $('#exampleModalCenter').modal('hide');
            }, 4000);


            loadDetails();

           }
                },
                complete: function(){
                }
            });

        }




</script>
@stop
