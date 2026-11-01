@extends('layouts.app',['pageTitle' => 'Over Due Books'])

@section('title')
    BNU CMS
@stop
@section('content')

<style>
    
    .blink_me {
  animation: blinker 1s linear infinite;
  color:red;
   font-weight: bold;
}

@keyframes blinker {
  50% {
    opacity: 0;
  }
}
     
</style>



        <div class="card" >
            {{-- <div class="card-header">
                <h3>Circulation History </h3>
            </div> --}}
            <div class="card-body">              
            <table class="table">
                <thead class="thead-light">
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">User Id</th>
                    <th scope="col">Book Name</th>
                    <th scope="col">Issue Date</th>
                    <th scope="col">Due Date</th>
                    <th scope="col">Return Status</th>
                    <th scope="col">Late Days</th>
                    <th scope="col">Fine</th>
                    <th scope="col">Status</th>
                    <!-- <th scope="col">Action</th> -->
                    </tr>
                </thead>
                <tbody>
                    
                    <?php $count=1;?>
                    @foreach($res_books as $rec)
                    <?php 
                    // $reminderArr = json_decode($rec->reminder2);
                    // count($reminderArr);exit;
                        $interval='';
                    $is_admin = ($rec->lc_issue_no_of_days == '') ? 0 : $rec->lc_issue_no_of_days;
                    $due_date=date('Y-m-d', strtotime($rec->lc_issue_date. ' +'.$is_admin.' days'));
                    date_default_timezone_set('Asia/karachi');
                    $curr_time =date("His");
                    $curr_date=date("Y-m-d", strtotime(substr($curr_time,0,11)));
                    
                    
                    $expiry=date('d-m-Y', strtotime($rec->lc_issue_date));
                                                        
                        //$expiry."__".$curr_date;
                    $origin = date_create($curr_date);
                    $target = date_create($due_date);
                    $interval = date_diff( $target,$origin);
                    $blink='';
                    $color='';
                   // $fine=($interval->format('%R%a') > 0 and $rec->lc_book_status=='Issued') ? $interval->format('%R%a')*10 : 0;
                    if($interval->format('%R%a') > 0 and $rec->lc_book_status=='Issued'){
                      if($due_date > '2023-10-04'){
                        // $fine = $due_date;
                        $fine=$interval->format('%R%a')*100;
                      }else{
                        $fine=0;
                      }
                    }else{
                      $fine=$rec->lc_fine_total;
                    }
                    if($rec->lc_book_status=='Issued'){
                        $color='#F95700FF';
                    }

                    ?>
                    <tr >
             
                    <th scope="row">{{$count}}</th>
                    <td><a style="color:black;text-decoration: none;cursor: pointer;" href="/library/allocation-new/?roll_no=<?=$rec->sai_enrollment_no?>">{{$rec->sai_enrollment_no}}</a></td>
                    <td>{{$rec->library_title}}</td>
                    <td><?=date('Y-m-d', strtotime($rec->lc_issue_date));?></td>
                    <td>{{$due_date}}</td>
                    <td style="color:<?=$color;?>">{{$rec->lc_book_status}}</td>
                    <!-- <td style="color:<?=$color;?>">{{$rec->lc_return_date}}</td> -->
                    <?php if($interval->format('%R%a') >0 and $rec->lc_book_status=='Issued'){
                              if($due_date > '2023-10-04'){
                                $blink="blink_me";
                              }
                              
                              }else{
                                  $blink="";
                                  $interval->format('%R%a');
                              }
                ?>
                    <td class="<?=$blink;?>">
                  
                  <?php   if($due_date > '2023-10-04'){
                     echo ($interval->format('%R%a') > 0 and $rec->lc_book_status=='Issued') ? $interval->format('%R%a days') :0;
                  }else{
                    echo '0';
                  }
                     ?> 
                    </td>
                    <td><?=$fine;?></td>
                    <!-- <td>{{$rec->lc_fine_status}}</td> -->
                    <?PHP
                    $lateDays=$interval->format('%R%a');
                    ?>
                    <td><button type="button" class="btn btn-primary" onclick="checkval('<?=$rec->sai_enrollment_no;?>','<?=$rec->library_title;?>','<?=$rec->library_books_id;?>','<?=$fine;?>','<?=$rec->id;?>','<?=$rec->lc_due_date;?>','<?=$rec->lc_issue_no_of_days;?>','<?=$rec->lc_accession_no;?>','<?=$lateDays;?>','<?=$rec->lc_reminder;?>','<?=$rec->sai_sdpo_sess_id;?>','<?=$rec->spi_email;?>')" data-toggle="modal" data-target="#exampleModalCenter1">Send</button></td>

                   
                    <?php $count++; ?>
                    @endforeach
                    </tr>
                </tbody>
        </table>               
    </div>
</div>
  <!-- --------- Model for send Reminder  -->
  <!-- Modal -->
  <div class="modal fade" id="exampleModalCenter1" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Reminder Book</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form enctype="multipart/form-data" method="post" name="return-book-form"  id="return-book-form">
        @csrf
      <div  class="form-inline form-group">
        <label for="inputPassword6" >User Name : </label>
        <input type="hidden" id="book-id" name="book-id" class="form-control mx-sm-3" >
        <input type="hidden" id="user-idd" name="user-id" class="form-control mx-sm-3" >
        <input type="hidden" id="act_sess_id" name="act_sess_id" class="form-control mx-sm-3" >
        <input type="hidden" id="spiEmail" name="spiEmail" class="form-control mx-sm-3" >
        <input type="hidden" id="issue-date" name="issue-date" class="form-control mx-sm-3" >
        <input type="hidden" id="id" name="id" class="form-control mx-sm-3" >
        <input type="hidden" id="issue-days" name="issue-days" class="form-control mx-sm-3" >
        <input type="hidden" id="accession-no" name="accession-no" class="form-control mx-sm-3" >
      
        <small style="font-weight:bold;font-size:16px;" id="user-id" class="text-muted" style="padding-left:20px;">
        </small>
        
  </div>
  <div class="form-inline form-group"> <strong>Reminder already Send:</strong><input type="text" id="reminder-no" name="reminder-no" readonly class="form-control mx-sm-3" ></div>
  <div class="form-group">
    <label for="inputAddress">Remarks</label>
    <input type="text" class="form-control" name="reminder"  id="reminder" vplaceholder="Book Remarks">
  </div>
  <div class="form-row">
    <div class="form-group col-md-12">
      <label for="inputCity">Book Name</label>
      <input type="text" class="form-control" name="book-name" readonly id="book-name">
    </div>
    <!-- <div class="form-group col-md-4" id="issue-div" style="display:none">
      <label for="inputEmail4">Issue Date</label>
      <input type="date" class="form-control "  name="issue-date" id="issue" placeholder="Issue Date">
    </div> -->
  </div>
</form>
      </div>

      <div class="modal-footer">
      <span id="bookissued" style="color:green"></span>  
       <span id="booklimit" style="color:red"></span>  
      <!-- <div style="float:left;color:red" id="result-body" ></div>  -->
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" onclick="SendMail()" id="btn"class="btn btn-primary">Send Mail</button>
      </div>
    </div>
  </div>
</div>




<!-- --------------end ----------------------- -->


  <!-- Modal 1-->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Return Book</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form enctype="multipart/form-data" method="post" name="return-book-form"  id="return-book-form">
        @csrf
      <div  class="form-inline form-group">
        <label for="inputPassword6" >User Name : </label>
        <input type="hidden" id="book-id" name="book-id" class="form-control mx-sm-3" >
        <input type="hidden" id="user-idd" name="user-id" class="form-control mx-sm-3" >
        <input type="hidden" id="issue-date" name="issue-date" class="form-control mx-sm-3" >
        <input type="hidden" id="id" name="id" class="form-control mx-sm-3" >
        <input type="hidden" id="issue-days" name="issue-days" class="form-control mx-sm-3" >
        <input type="hidden" id="accession-no" name="accession-no" class="form-control mx-sm-3" >

        <small style="font-weight:bold;font-size:16px;" id="user-id" class="text-muted" style="padding-left:20px;">
        </small>
  </div>
  <div class="form-row">
    <!-- <div class="form-group col-md-6">
      <label for="inputEmail4">Return Date</label>
      <input type="date" class="form-control date" onchange="finecal()" name="return-date" id="dt" placeholder="Return Date">
    </div> -->
    <div class="form-group col-md-6">
      <label for="inputState">Fine Status</label>
      <select onchange="status(this)" name="fine-status" id="finestatus" class="form-control">
        <option value="" selected>Choose...</option>
        <option value="Paid">Paid</option>
        <option value="UnPaid">Un Paid</option>
      </select>
    </div> 
    <div class="form-group col-md-6">
      <label for="inputPassword4">Fine</label>
      <input type="number" class="form-control" name="fine" id="fine"  placeholder="Fine">
    </div>
  </div>
  <div class="form-group">
    <label for="inputAddress">Remarks</label>
    <input type="text" class="form-control" name="remarks" id="inputAddress" placeholder="Book Remarks">
  </div>
  <div class="form-row">
    <div class="form-group col-md-12">
      <label for="inputCity">Book Name</label>
      <input type="text" class="form-control" name="book-name" readonly id="book-name">
    </div>
    <!-- <div class="form-group col-md-4">
      <label for="inputState">Book Status</label>
      <select onchange="status(this)" name="book-status" id="bookstatus" class="form-control">
        <option value="" selected>Choose...</option>
        <option value="Received">Received</option>
        <option value="Issued">Reissued</option>
      </select>
    </div> -->
    <div class="form-group col-md-4" id="issue-div" style="display:none">
      <label for="inputEmail4">Issue Date</label>
      <input type="date" class="form-control "  name="issue-date" id="issue" placeholder="Issue Date">
    </div>
  </div>
  <!-- <div class="form-group">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" id="gridCheck">
      <label class="form-check-label" for="gridCheck">
        Check me out
      </label>
    </div>
  </div> -->
  <!-- <button type="button" class="btn btn-primary">Fine Calculate</button> -->
 
</form>
      </div>

      <div class="modal-footer">
      <span id="bookissued" style="color:green"></span>  
       <span id="booklimit" style="color:red"></span>  
      <!-- <div style="float:left;color:red" id="result-body" ></div>  -->
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" onclick="fineupdate()" id="btn"class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
@stop


@section('javascript')
<script type="text/javascript">

function SendMail(){



  var bi_form = document.querySelector('#return-book-form');
            var fd = new FormData(bi_form);
            $.ajax({
                type:'POST',
                url: '{{url('library/user-reminder')}}',
                data:fd,
                processData:false,
                contentType:false,
                success:function(data){


        if(data.status){
            $('#booklimit').html('');
            $('#bookissued').html(data.message); 
            setTimeout(function(){  
                $('#bookissued').html(''); 
                $('#exampleModalCenter1').modal('hide');
                $("#return-book-form")[0].reset();
                location.reload();
            }, 4000);
     
           }else{
            $('#bookissued').html('');
            $('#booklimit').html(data.message);
            setTimeout(function(){ 
                $('#booklimit').html('');
                $('#exampleModalCenter1').modal('hide');
            }, 4000);
            
            
            loadDetails();
            
           }
                },
                complete: function(){
                }
            });
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



  function checkval(user_id,book_name,book_id,fine,id,date,issueDays,accession_no,days,reminderCount,ActiveSess,spiEmail){


    $('#book-id').val(book_id);
    $('#book-name').val(book_name);
    $('#act_sess_id').val(ActiveSess);
    $('#spiEmail').val(spiEmail);
    $('#user-id').html(user_id);
    $('#fine').val(fine);
    $('#id').val(id);
    $('#user-idd').val(user_id);
    $('#issue-date').val(date);
    $('#issue-days').val(issueDays);
    $('#accession-no').val(accession_no);
    $('#reminder').val('This Book '+book_name+' is Overdue for '+days+' days');
    $('#reminder-no').val(reminderCount);

    
  }
  $("#btn1").click(function(e){
            e.preventDefault();
            
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

        });

      
        
</script>
@stop
