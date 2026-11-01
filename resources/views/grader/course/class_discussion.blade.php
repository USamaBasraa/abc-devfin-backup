<?php 
use App\sessions;
$sess = new sessions();
$sess->startSession();
//exit();

?>
@extends('layouts.master')
@section('title')
    BNU CMS
@stop
@section('mainBody')
<!-- width: 80%; margin-left:10px!important; -->


<style>
.msg_btn{
    height: 9px;
    width: 9px;
    position: absolute;
    bottom: 0;
    right: 17px;
    border-radius: 100%;
    border: 1px solid;
}
.center{
    margin: auto;
  
  
  padding: 10px;
}
</style>


    <div class="card"  style="" >
        <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
            <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >Course Discussion </h3>
        </div>
    </div>
<?php /*?> <table class="table table-bordered" width="80%" style="width:70% !important;">   
    <tr>
    	<th> Student </th>
        <th> Message </th>
        <th> Entry Time </th>
    </tr>
	<?php
	foreach($data as $rec){
	?>
  <tr>
		<td><?php echo $rec->entry_user_id;?></td>
        <td><?php echo $rec->message;?></td>
        <td><?php echo $rec->created_at;?></td>
  </tr>
    <?php
	}
	?>
</table><?php */?>

         <div class="container"  id="comment_div"   style="height:70%; width:70%; overflow-y: scroll;scrollbar-color: #2F3B4D white; scrollbar-width: thin;">
           
        <div class="container" id="reload" style="height: 80%;overflow-y: scroll;scrollbar-color: #2F3B4D white; scrollbar-width: thin;">

        <style>.student_name{ color:black; font-weight:bold; }</style>
            <table class="table table-bordered table-striped" style="width:100%!important;border-left: none!important;border-right: none!important">
                <tr class="" >
                    <th class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">Students</th>
                    <th class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-8">Message</th>
                </tr>
            
                <?php
                foreach($data as $rec){
                ?>


<tr >
    <td class=" media" style="margin-left:0px;"  >
        <div class="media-left" onclick="open_message_popup('<?=$rec->sai_enrollment_no?>');" >
            <img src="http://staff.bnu.edu.pk/studentpics/<?=$rec->sai_enrollment_no.'.jpg'?>" class="rounded-circle media-left" height="40" width="40" alt="{{$rec->spi_name}}">
        </div>
        <div class="media-body" style="padding-left:15px;">
            <div class="f-13 chat-header" style="font-size: 14px; font-weight: 600; " >{{$rec->spi_name}} <br>{{$rec->sai_enrollment_no}}</div>
            
        </div>
    </td>
    <td>
    <b><?=$rec->message?></b><br>
    <?=$rec->created_at?>
    </td>
    </td>
</tr>


               
                <?php
                }
                ?>
                <input type="hidden" id="sc_co_id" value="{{$co_id}}">
            </table>
        </div>
    </div>
       

@stop

@section('pageScripts')

@stop 