<?php

$countRow = count($all_files);


?>
<style type="text/css">
	.headingtd {
		border:2px solid #000;
		background-color:#999;
		font-weight:bold;
	}
	.cntr {
		text-align:center;	
	}
</style>

    <table border='1' align='center' cellspacing="0" >
	<?php
	if($countRow){
	?>
		
		  <tr><td colspan="12" height="34" style="border:none;"  bgcolor="#2F3B4D" align="center"><font style="font-size:12px; color:#FFF;">Faculty Course Attendance</font></td></tr>
		   
		  
	<?php   
		$index = 1;
		$tchContactHour = 0;
		$tchName = '';
		$deliver_status = "";
		foreach($all_files as $row) {
         
			if(trim($row->class_status)=="DELIVERED")
			{
				$row_colour = "#00CC66";
			}
			else
			{
				$row_colour = "#FF0000";
				
			}
			
			
			if(trim($row->class_status)!=trim($deliver_status))
			{
				$deliver_status = $row->class_status;
				?>
                <tr><td colspan="12" align="right">
           <div style="float:left; font-size:18px; font-weight:bold; margin-left:20px;"> <?php echo ucwords(strtolower($row->class_status));?> </div>
           <b>Report Day / Date : </b>
           <?php 
		    echo $all_files[0]->tt_subject_day." / ".date("d-M-Y", strtotime($tac_end_date));
		   ?></td>
           </tr>
            <tr style="background-color:#AFAFAF;">
			<th class="headingtd" align="center" valign="middle" width="40">Sr #</th>
            <th class="headingtd" width="200">Name of Instructor</th>
            <th class="headingtd" width="340">Courses</th>
             <th class="headingtd" width="340">Description</th>
           <th class="headingtd" width="130">From</th>
            <th class="headingtd" width="130">To</th>
            <th class="headingtd" width="150">Attendance Marked</th>
            <th class="headingtd" width="150" style="display:none;">Status</th>
            
		  </tr>    
                
                <?php
				
				$deliver_status = $row->class_status;
			}
			
	?>
    		
		  <tr style="background-color:<?php echo $row_colour;?>">
			<td align="center" height="28"><?=$index?></td>
			<td align="left">&nbsp;&nbsp; <?=$row->faculty_name?></td>
           <td align="left">&nbsp;&nbsp;<?php echo $row->course_cd_name." (".$row->co_section.")"; ?></td>
            <td align="center"><?=$row->makeup_desc?></td>
             <td align="center"><?=$row->tt_subject_time_from?></td>
            <td align="center"><?=$row->tt_subject_time_to?></td>
             <td align="center"><?=str_replace(".",":",substr($row->attendance_marked,0,15));?><?php //echo str_replace(".",":",substr($row['C'],10,8))." ".substr($row['ATTENDANCE_MARKED'],-2,2);?></td>
            <td align="center" style="display:none"><?php 
								if($row->class_status!="0")
								{
									echo "Delivered";
								}
								else if($row->class_status=="0")
								{
									echo "Not Delivered";
								}
								?></td>
          
		</tr>
	<?php				
			$index++;				
		}//foreach
	?>
		  <tr>
          		<td colspan="7" align="right">Report generated on: <?=date('D d-m-Y H:i:s')?></td>
		  </tr>
		</table>
	<?php			
	}
	else
	{
		?>
        	<tr>
          		<td colspan="6" align="left">No Class </td>
		  </tr>
        	<tr>
          		<td colspan="6" align="left">Report generated on: <?=date('D d-m-Y H:i:s')?></td>
		  </tr>
		</table>
        
        <?php
		
	}//if dataset
	?>