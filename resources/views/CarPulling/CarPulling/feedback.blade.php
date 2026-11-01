@extends('layouts.app', ['pageTitle' => 'Car pooling Fedback'])
@section('content')
<!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
  Send Notification
</button> -->
                    <div class="container">
                      <div class="card" >
                        <div class="card-header nav-justified hemi-intro-selected">
                            <h3 class="card-title ">Feed Back Panel </h3>
                        </div>
                        <div class="card-body" id="con">
                          <form enctype="multipart/form-data" method="post" name="cformdes" id="cformdes">
                            <div class="form-group" >
                              <label for="course_name">Description</label>
                              <textarea class="form-control " name="description"  id="description" rows="3" placeholder="Please enter Feed Back for Car Pooling"></textarea>
                              <!-- <small id="display_countddd">100 words </small> -->
                              <small>Total words: </small><span id="display_count">0</span>. Words left :<span id="word_left">150</span>
                            </div>
                              <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                                  <button id="btn" style="    margin-left: 232px;" type="button" name="btn" onclick="Add_desc()" class="btn btn-success btn-submit-desc">
                                      <a href="#" class="btn btn-squared-default-plain btn-success">
                                          <i class="fa fa-plus fa-2x"></i>
                                          <br />
                                          Submit
                                      </a>
                                  </button>
                               </div>
                            </form>
                            <br>
                            <div class="row">
						        <div class="col-12" id="notifications"></div>
				            </div>
                        </div>
                      </div>
                     </div>       
                  

                     <!-- /////////////////////////////////////  -->

<!-- 
      <div class="page-body">
         <div class="card">
        <div class="card-header">
            <h5>FeedBack Status</h5>            
        </div>
        <div class="card-block table-border-style">
        <table id="simpletable"  class="table table-striped">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="text-white" style="text-align:left;">Sr#</th>
                        <th class="text-white" style="text-align:left;">Description</th>  
                           
                    </tr>                         
                </thead>
                <tbody>
               
                  <?php  $count=1;?>
                  @foreach ($res as $rec)
                  <tr>
                    <td>{{$count}}</td>
                   <td> {{$rec->sfd_desc}}</td>
                    
                   <?php $count++;?>
                  @endforeach  
                  </tr>
                </tbody>
            </table>        
                
        </div>
    </div>
</div>               -->

@stop
@section('javascript')
<script>   
$(document).ready(function()
{

//   $('textarea').keypress(function(e) {
//     var tval = $('textarea').val(),
//         tlength = tval.length,
//         set = 500,
//         remain = parseInt(set - tlength);
//     $('p').text(remain);
//     if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
//         $('textarea').val((tval).substring(0, tlength - 1))
//     }
// })

    // var wordCounts = {};
    // $("#description").keyup(function() {
    //     var matches = this.value.match(/\b/g);
    //     wordCounts[this.id] = matches ? matches.length / 2 : 0;
    //     var finalCount = 0;
    //     $.each(wordCounts, function(k, v) {
    //         finalCount += v;
    //     });
    //     $('#display_count').html(finalCount);
    //     am_cal(finalCount);
    // }).keyup();


    $("#description").on('keyup', function() {
    var words = 0;

    if ((this.value.match(/\S+/g)) != null) {
      words = this.value.match(/\S+/g).length;
    }

    if (words > 150) {
      // Split the string on first 200 words and rejoin on spaces
      var trimmed = $(this).val().split(/\s+/, 150).join(" ");
      // Add a space at the end to make sure more typing creates new words
      $(this).val(trimmed + " ");
    }
    else {
      $('#display_count').text(words);
      $('#word_left').text(150-words);
    }
  });
 }); 






     function Add_desc() {
        var msg_div = '<div class="col-md-12 alert alert-danger border-danger" style="text-align: left;"  ><button type="button" class="close" data-dismiss="alert" aria-label="Close"><i class="fa fa-times-circle-o" style="color:black"></i></button><strong>Error! &nbsp &nbsp</strong>'
        var msg_div1 = '<div class="col-md-12 alert alert-success border-success" style="text-align: left;"  ><button type="button" class="close" data-dismiss="alert" aria-label="Close"><i class="fa fa-times-circle-o" style="color:black"></i></button><strong>Success! &nbsp &nbsp</strong>'

        var bi_form = document.querySelector('#cformdes');
        var fd = new FormData(bi_form);
        $('#btn').prop('disabled', true);
        $('#error_msg1').html('');
        $.ajax({
        method: "POST",
        url: '{{'/api/car-pooling-feedback'}}',
        data: fd,
        processData: false,
        contentType: false,
        headers: {
            Authorization: 'Bearer <?= auth()->user()->api_token ?>',
            Accept: 'application/json'
        }
        }).done(function(res) {
          
          $('#description').val('');
        if (res.status) {
            //alert(res.message);
            msg_div1 +=res.message;
            msg_div1 += '</div>';
            $('#btn').prop('disabled', false);
            $("#simpletable").load(window.location + " #simpletable");
            $('#notifications').html(msg_div1);
        } else {
           
        }
        }).fail(function(res) {
        console.log(res.statusText,"hhhh");
        msg_div += res.statusText;//'You have successfully entered your details. please contact to admin office to get vechicle sticker';
                msg_div += '</div>';
               $('#notifications').html(msg_div);
               setTimeout(function() {
                $('#notifications').html('');
                $('#notifications').html('');
                        }, 2000);
        //console.log(res);
        //location.reload();
        });
}

</script>
@endsection