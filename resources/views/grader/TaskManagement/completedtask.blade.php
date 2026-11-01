<div class="container">
   <div id="accordion">
   <?php
    $var_tm_client_name_pending = '';
    $pending_sr_no = 1;
    $find_completed = 0 ;
    foreach($tasks as $key_p):
        if($var_tm_client_name_pending!=$key_p->tm_client_name && $key_p->tm_status=='Completed'){
           $find_completed++;
    ?>
      <!-- -- Card Start Here   && $key_p->task_type=='Pending'  -->
        <div class="card">
            <div class="card-header" style="background-color:#D2D6F1;">
                <a class="card-link" data-toggle="collapse" href="#collapseOne<?=$pending_sr_no;?>">
                    <h4> <i class="fe fe-chevron-down"></i>  
                    <?php echo $var_tm_client_name_pending=$key_p->tm_client_name;?> </h4>
                </a>
            </div>
            <div id="collapseOne<?=$pending_sr_no;?>" class="collapse" data-parent="#accordion">
                <div class="card-body">
                  <!-- accordion body start here   -->
                        @php 
                            $taskController = new \App\Http\Controllers\Grader\TaskManagement\TaskManagementController();
                            $taskDetails = $taskController->getTaskDetailsbyid($key_p->task_client_id,'Completed');
                        @endphp
                        <div class="row">
                            <?php 
                                $sub_count = 1;   
                                foreach ($taskDetails as $detail):
                            ?>     
                                    <div class="col-sm-6">
                                        <!-- within working div -->
                                                <div class="card">
                                                    <div class="card-header" style="background-color:#9EECFF">
                                                        <h3 class="card-title"> <?php echo $detail->tm_title; ?> </h3>
                                                        <div class="card-options">
                                                            <div style="float: right; color:black; margin-right:6px;">
                                                                <strong>Reporting Date / Time : </strong>
                                                                <?php echo $detail->completion_date." : ".$detail->task_report_time; ?>
                                                            </div>
                                                            <a href="#" class="card-options-collapse mr-2" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                            <p> <?php echo $detail->description; ?> </p>
                                                    </div>
                                                    <!-- ---------------footer -->
                                                    <div class="card-footer" style="width:100%; height:100%; padding: 0.5rem 8rem; float:right;">
                                                        <div class="btn-group mt-2 mb-2">
                                                            <button type="button" class="btn btn-success dropdown-toggle"  data-toggle="dropdown">
                                                            Action <span class="caret"></span>
                                                            </button>
                                                            <ul  class="dropdown-menu" role="menu">
                                                            <li><a class="otherCatches" data-todoID=<?=$detail->id;?> data-todoID=<?=$detail->id;?>>Completed</a></li>
                                                            <li><a class="otherCatches" data-todoID=<?=$detail->id;?>>Pending</a></li>
                                                            <!-- <li><a class="otherCatches" data-todoID=<?=$detail->id;?>>Deleted</a></li> -->
                                                            </ul>
                                                        </div>
                                                        <button type="button" class="btn btn-primary sendMessageButton" data-tmID="<?=$detail->id;?>" data-toggle="modal" data-target="#sendMessageModal">Send Message <?=$detail->id;?></button>
                                                    </div>
                                                    <!-- //-------------------------- -->
                                                </div>
                                         <!-- end of within working div -->  
                                     </div>      
                                <?php
                                    if($sub_count%2==0){
                                ?>
                                    </div><div class="row">
                                <?php
                                    }
                                ?>    
                            <?php
                                $sub_count++;
                                endforeach;
                            ?>     
                        </div> 
                  <!-- accordion body end here                 -->
                </div>
            </div>
            </div>
      <!-- -- Card End Here   -->
      <?php
             $pending_sr_no++;
            }
          endforeach;
        ?>
         @if($find_completed == 0)
            <p class="text-center" style="font-size: 18px;">No Record Found</p>
        @endif 
</div>
</div>


            <!-- <div class="expanel expanel-default mt-4">
                <div class="expanel-body">
                <?php echo $var_tm_client_name_pending=$key_p->tm_client_name;?>
                </div>
                <div class="expanel-footer">Panel footer</div>
            </div> -->

