
  <div class="container-fluid">
  			  <div class="row" style="background-color:#066; color:#FFF; padding:6px;">
          <input type="text" name="material_id" id="material_id" hidden="true"/>
          		<div class="col-12 col-sm-1 col-md-1 col-lg-1 col-xl-1">
               Sr 
                </div>
                  <div class="col-12 col-sm-1 col-md-1 col-lg-1 col-xl-2">
               Lecture No
                  </div>
                <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-2">
               Title
                </div>
                <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-3">
               Entry Date / Time
                </div>
                <div class="col-12 col-sm-5 col-md-4 col-lg-4 col-xl-2">
              Action
                </div> 
              
          </div> 
      <?php	
      
      $file_sr = 1;
      $allowed ="";
      $color="";
      $is_video="";
            foreach($course_handouts as $rec_course_handouts){
              $file_name= $rec_course_handouts->file_original_name;
              $its_link=explode("=",$is_video);
                if($rec_course_handouts->type=='442'){

  
             $get_ext = pathinfo($file_name, PATHINFO_EXTENSION);
              if ($get_ext=='docx' or $get_ext=='doc') {         
                                    $allowed ="fa fa-file-word-o";
                                    $color='blue';
                              }else if ($get_ext=='csv' or $get_ext=='xlsx' or $get_ext=='xlsm' or $get_ext=='xls' ){
                                    $allowed ="fa fa-file-excel-o";
                                    $color='green';
                              }else if ($get_ext=='pptx' or $get_ext=='PPT' or $get_ext=='XPS' ){
                                  $allowed ="fa fa-file-powerpoint-o";
                                  $color='#ff6c43';
                             }else if ($get_ext=='pdf'){
                                  $allowed ="fa fa-file-pdf-o";
                                  $color='#ff6c43';
                              }else if($get_ext=='mp4' or $get_ext=='mkv' or $get_ext=='3gp'  ){
                                  $allowed ="fa fa-youtube-play";
                                  $color='#ff0000';
                                }
                              ?>
                               <div class="row" style="padding:4px; border:4px;">
                            <div class="col-12 col-sm-1 col-md-1 col-lg-1 col-xl-1">
                             {{$file_sr}} 
                             </div>

                             <div class="col-12 col-sm-1 col-md-1 col-lg-1 col-xl-2">
                               {{$rec_course_handouts->lecture_no}}
                             </div>
                             <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-2">
                             {{$rec_course_handouts->title}} 
                             </div>
                             <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-3">
                             {{$rec_course_handouts->entry_date_time}} 
                             </div>
                             <div>
                             &nbsp; &nbsp;&nbsp;    <a  href="{{asset('docs/'.$file_name)}}" > <i class="<?=$allowed;?>" style="color:<?=$color;?>;  font-size:65px;"></i></a>
                            <br/>
                            </div>
                            <div><?=$rec_course_handouts->id?><img data-toggle="modal" data-target="#myModal" onclick="setval(<?=$rec_course_handouts->id?>)" src="{{asset('images/delete.png')}}"   width="20" height="20"; >
                           </div>
                          </div>  
         
                         <?php     }else{

                          $is_video= $rec_course_handouts->file_original_name;
                            //$its_link=explode("=",$is_video);
                          $its_link= array_pad(explode("=", $is_video), 2, null);
                          // print_r($its_link);
                            // exit();
                            // $its_link[0];
                            $its_link[1];
                           
                           ?>
                           <div class="row" style="padding:4px;">
                           <div class="col-12 col-sm-1 col-md-1 col-lg-1 col-xl-1">
                             {{$file_sr}} 
                             </div>
                             <div class="col-12 col-sm-1 col-md-1 col-lg-1 col-xl-2">
                               {{$rec_course_handouts->lecture_no}}
                             </div>
                             <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-2">
                             {{$rec_course_handouts->title}} 
                             </div>
                             <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-3">
                             {{$rec_course_handouts->entry_date_time}} 
                             </div>
                            <div>
                            <iframe  id="myCode"  gesture="media" allow="autoplay; encrypted-media" class="embed-responsive-item" src="https://www.youtube.com/embed/<?=$its_link[1];?>" style=" height:60px;width:100px;display: block;" allowfullscreen >
                            </iframe>
                          </div>
                          <div><?=$rec_course_handouts->id?><img src="{{asset('images/delete.png')}}" data-toggle="modal" data-target="#myModal" width="20" onclick="setval(<?=$rec_course_handouts->id?>)" height="20"; >
                          </div>
                            </div>






                        <?php }?>
     
                              

            <?php
			$file_sr++;
            }
            ?>



 <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
         
          <h4 align="left" class="modal-title">Delete Material</h4>
           <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p>Are you sure want to Delete Material !.</p>
          <input type="hidden" id="dele" name="dele"/>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            <button type="button"  onclick="dElete()" class="btn btn-default" data-dismiss="modal">Yes</button>
        </div>
      </div>
      
    </div>
  </div>
<script>

  function setval(id){
$('#dele').val(id);
  }
function dElete() {
  var id=$('#dele').val();
 // alert(id);
 // return false;
 // exit();
  
  // uploads/delete_faculity_course_material/{id}

  $.ajax({
            method: "GET",
            url:    "<?php echo url('uploads/delete_faculity_course_material/'); ?>/"+id,
            
        }).done(function(res){
          // console.log(res);
          if(res.status){
              alert(res.message);
			  location.reload();
          }

        }).fail(function( res ) {
            console.log(res);
        });



}

</script>
	</div> 

