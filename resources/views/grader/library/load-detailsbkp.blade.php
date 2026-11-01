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
<?php
$countRow = count($viewBooks);
$countProfileRow = count($view_profile);
$countLibraryBooks = count($view_sqlLibraryBooks);
$library_status_array  = array("Active","Block");
?>
<style type="text/css">
	.headingtd {
		border:0px solid #000;
		background-color:#999;
		font-weight:bold;
	}
	.cntr {
		text-align:center;
	}
</style>
<div>
    <table class="table table-bordered"  align='center' cellspacing="0" >
	<?php
	if($countProfileRow){
	?>
	  <tr
    <td colspan="12" height="34" style="border:none;"  bgcolor="#2F3B4D" align="left"><h3 style="font-weight: bold;color:white">User Profile</h3></td></tr>
      <tr style="background-color:#AFAFAF;">

        <th class="headingtd" style="text-align: center" width="200">User ID</th>
        <th class="headingtd" style="text-align: center" width="340">User Name</th>

        <?php if($view_profile[0]->group_id == '4'){?>

        <th class="headingtd" style="text-align: center" width="340">Student Status</th>
        <th class="headingtd" style="text-align: center" width="340">Picture</th>
        <?php } ?>


    </tr>
       <?php
            $index = 1;
        foreach($view_profile as $row) {
             $Activeurl = url('/images').'/ok.png';
             $BlockUrl = url('/images').'/del.png';

        ?>

        <tr>
            <td><img id="blah" <?php echo ($row->library_status == 'Active')?"src='$Activeurl'":"src='$BlockUrl'"?> ><?php echo ($row->group_id == '4')?$row->sai_enrollment_no:$row->id?></td>

            <td align="left" style="cursor:pointer" data-toggle="modal" data-target="#popup"><?php echo ($row->group_id == '4')?$row->spi_name:$row->emp_f_name.' '.$row->emp_l_name?></td>

            <?php if($row->group_id == '4'){?>

            <td style="text-align: left"><?php echo$row->sai_student_status?></td>
            <td style="text-align: center"><img id="blah" src="<?=url('/studentpics').'/'.$row->sai_enrollment_no.'.jpg'?>" width="70" height="70" /></td>
            <?php } ?>
        </tr>
	    <?php
		$index++;
		}//foreach
	    ?>

		</table>
	<?php
	}

	?>

</div>

<div style="margin-top:20px">
    <table class="table table-bordered"  align='center' cellspacing="0" >
	<?php
	if($countRow){
	?>
	  <tr><td colspan="12" height="34" style="border:none;"  bgcolor="#2F3B4D" align="left"><h3 style="font-weight: bold;color:white">Issued Books</h3></td></tr>
      <tr style="background-color:#AFAFAF;">
        {{-- <th class="headingtd" style="text-align: center" valign="middle" width="40">Sr #</th> --}}
        <th class="headingtd" style="text-align: center" width="200">Book Title</th>
        <th class="headingtd" style="text-align: center" width="340">Book Auhtor</th>
        <th class="headingtd" style="text-align: center" width="340">Accession #</th>
        <th class="headingtd" style="text-align: center" width="340">Author Mark</th>
        <th class="headingtd" style="text-align: center" width="340">DDC</th>
        <th class="headingtd" style="text-align: center" width="340">Issue Date</th>
        <th class="headingtd" style="text-align: center" width="340">Due Date</th>
        <th class="headingtd" style="text-align: center" width="340">Late Days</th>
        <th class="headingtd" style="text-align: center" width="340">Fine</th>
        <th class="headingtd" style="text-align: center" width="340">Status</th>
    </tr>
       <?php
            $index = 1;
          
        foreach($viewBooks as $row) {

                    $interval='';
                    // $is_admin = ($row->lc_issue_no_of_days == '') ? 0 : $row->lc_issue_no_of_days;
                    // $due_date=date('Y-m-d', strtotime($row->lc_issue_date. ' +'.$is_admin.' days'));
                    date_default_timezone_set('Asia/karachi');
                    $curr_time =date("His");
                    $curr_date=date("Y-m-d", strtotime(substr($curr_time,0,11)));
                    $due_date=date("Y-m-d", strtotime(substr($row->lc_due_date,0,11)));


                    $origin = date_create($curr_date);
                    $target = date_create($due_date);
                    $interval = date_diff( $target,$origin);
                    $blink='';
                    $color='';
                    // or $row->lc_book_status=='Issued'
                   // $fine=($interval->format('%R%a') > 0 and $row->lc_book_status=='Issued') ? $interval->format('%R%a')*10 : 0;
                    if($interval->format('%R%a') > 0 ){
                      $fine=$interval->format('%R%a')*10;
                    }else{
                      $fine=$row->lc_fine_total;
                    }
                    if($row->lc_book_status=='Issued'){
                        $color='#F95700FF';
                    }
        ?>

        <tr>
            <td align="left"><?=$row->library_title?></td>
            <td align="left"><?=$row->library_author_name?></td>
            <td align="center"><?=$row->lc_accession_no?></td>
            <td align="left"><?=$row->library_author_mark?></td>
            <td align="center"><?=$row->library_ddc_number?></td>
            <td style="text-align: center"><?php echo substr($row->lc_issue_date,0,10);?></td>
            <td style="text-align: center;" ><?php echo substr($row->lc_due_date,0,10);?></td>
         
            <?php if($interval->format('%R%a') >0 ){
                      $blink="blink_me";

                      }else{
                          $blink="";
                          $interval->format('%R%a');
                      }
        ?>
            <td class="<?=$blink;?>">
            <?php echo ($interval->format('%R%a') > 0) ? $interval->format('%R%a days') :0;?></td>
         
            <td align="center">  <a href="{{url('library/library_fine_slip')}}?id=<?=$row->id?>" target="_blank" style="color:red;font-weight: bold">
            <?=$fine;?> </a></td>
            <td  style="text-align: center">
            <?php if($row->lc_book_status=='Issued') { ?>
            <button type="button" class="btn btn-primary" onclick="checkval('<?=$row->sai_enrollment_no;?>','<?=$row->library_title;?>','<?=$row->library_books_id;?>','<?=$fine;?>','<?=$row->id;?>','<?=$row->lc_due_date;?>','<?=$row->lc_issue_no_of_days;?>','<?=$row->lc_accession_no;?>')" data-toggle="modal" data-target="#exampleModalCenter">Return Book</button>
              <?php } else{ ?>
              <h5 style="color:green;font-weight:bold">Received</h5>
         <?php     } ?></td>
        </tr>
	    <?php
		$index++;
		}//foreach
	    ?>
		  <tr>
          		<td colspan="10" align="right">Report generated on: <?=date('D d-m-Y H:i:s')?></td>
		  </tr>
		</table>
	<?php
	}
	else
	{
	?>
        <tr>
            <tr><td colspan="12" height="34" style="border:none;"  bgcolor="#2F3B4D" align="left"><h3 style="font-weight: bold;color:white">Students Issued Books</h3></td></tr>
        <tr><td colspan="12" height="34" style="border:none;"  bgcolor="#AFAFAF" align="center"><font style="font-size:12px; color:#black;font-weight:bold">No Book Found</font></td></tr>
        </tr>
        </table>
    <?php
    }//if dataset
    ?>
</div>

<div style="margin-top:20px">
    <form enctype="multipart/form-data" method="post" name="search_form"  id="search_form">
        {{ csrf_field() }}
    <table class="table table-bordered"  align='center' cellspacing="0" >

	  <tr><td colspan="12" height="34" style="border:none;"  bgcolor="#2F3B4D" align="left">
        <div style="float:left;"><h3 style="font-weight: bold;color:white">Book To Be Issue</h3></div>
        <div style="float:right;font-weight: bold;color:white">
              <input style="width:160px" type="text" class="form-control" id="searchAccession" name="searchAccession" onkeyup="searchByAccession()" placeholder="Search with Accession NO">
        </div></td></tr>
      <tr style="background-color:#AFAFAF;">

        <th class="headingtd" style="text-align: center" width="200">Book Title</th>
        <th class="headingtd" style="text-align: center" width="340">Book Auhtor</th>
        <th class="headingtd" style="text-align: center" width="340">Accession #</th>
        <th class="headingtd" style="text-align: center" width="340">DDC #</th>
        <th class="headingtd" style="text-align: center" width="340">Author Mark</th>
        <th class="headingtd" style="text-align: center" width="340">Issue Date</th>

      </tr>


        <tr id="result-body">

        </tr>


		</table>
    </form>


        <tr>
        {{-- <tr><td colspan="12" height="34" style="border:none;"  bgcolor="#2F3B4D" align="center"><font style="font-size:12px; color:#FFF;">No Book Found</font></td></tr> --}}
        </tr>
        </table>

</div>


 <!-- Modal -->
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
        <form enctype="multipart/form-data" method="post" name="return-book-form"  readonly id="return-book-form">
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
      <div class="form-group col-md-6">
        <label for="inputEmail4">Return Date</label>
        <input type="date" class="form-control date" onchange="finecal()" name="return-date" id="dt" value="<?php echo date('Y-m-d');?>" placeholder="Return Date">
      </div>
      <div class="form-group col-md-6">
        <label for="inputPassword4">Fine</label>
        <input type="number" class="form-control" name="fine" id="fine" readonly placeholder="Fine">
      </div>
    </div>
    <div class="form-group">
      <label for="inputAddress">Remarks</label>
      <input type="text" class="form-control" name="remarks" id="inputAddress" placeholder="Book Remarks">
    </div>
    <div class="form-row">
      <div class="form-group col-md-4">
        <label for="inputCity">Book Name</label>
        <input type="text" class="form-control" name="book-name" readonly id="book-name">
      </div>
      <div class="form-group col-md-4">
        <label for="inputState">Book Status</label>
        <select onchange="status(this)" name="book-status" id="bookstatus" class="form-control">
          <option value="" selected>Choose...</option>
          <option value="Received">Received</option>
          <option value="Issued">Reissued</option>
        </select>
      </div>
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
          <button type="button" onclick="saveChanges()"  id="btn"class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>




   <!-- Modal Profile-->
 <div class="modal fade" id="popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle"><?php echo ($view_profile[0]->group_id == '4')?$view_profile[0]->spi_name:$view_profile[0]->emp_f_name.' '.$view_profile[0]->emp_l_name?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered"  align='center' cellspacing="0" >


                <tr style="background-color:#AFAFAF;">

                    {{-- <th class="headingtd" style="text-align: center" width="200">Contact #</th>
                    <th class="headingtd" style="text-align: center" width="340">Address</th> --}}
                    <th class="headingtd" style="text-align: center" width="340">User Profile</th>
                    <th class="headingtd" style="text-align: center" width="340">Library Status</th>
                </tr>
                   <?php
                        $index = 1;
                        // dd($view_profile);
                    foreach($view_profile as $row) {

                    ?>

                    <tr>

            
                        <td align="left"><a href="{{url('library/get-user-detail-pdf')}}" style="color:black" href="">Click Here to Generate PDF</a></td>
                        <td align="left">
                            <select name="library_status" id="library_status" onchange="updateStatus(<?=$row->id?>)" class=" form-control">
                                <option value="">--Select Library--</option>
                                <option value="Active" <?= ('Active' == $row->library_status) ? 'selected' : ''; ?>>Active</option>
                                <option value="Block" <?= ('Block' == $row->library_status) ? 'selected' : ''; ?>>Block</option>
          
                            </select>
                        </td>

                    </tr>
                    <?php
                    $index++;
                    }//foreach
                    ?>

                    </table>
        </div>

        <div class="modal-footer">
        <span id="bookissued" style="color:green"></span>
         <span id="booklimit" style="color:red"></span>
         <span id="libraryStatusMsg" style="color:green"></span>
        <!-- <div style="float:left;color:red" id="result-body" ></div>  -->
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          {{-- <button type="button" onclick="saveChanges()"  id="btn"class="btn btn-primary">Save changes</button> --}}
        </div>
      </div>
    </div>
  </div>



