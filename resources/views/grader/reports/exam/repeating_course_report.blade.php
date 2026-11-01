<?php
    include('fpdf/fpdf.php');
	?>
	<style type="text/css">
    .headingtd {
    border: 2px solid #000;
    background-color: #999;
    font-weight: bold;
    }
    .cntr {
    text-align: center;
    }
    </style>
    <?php

       header("Content-Type:  application/vnd.ms-excel");
       header("Expires: 0");
       header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
       $file_time=date('D d-m-Y H:i:s');
       header("Content-Disposition: attachment; filename=RepeatCourse_Report_$file_time.xls"); 
        
        ?>
    <table border='1' align='center' cellspacing = 0 >
        <tr>
        	<td colspan="17" style="border:none;"><font style="font-size:36px;">Beaconhouse National University</font></td>
        </tr>
        <tr>
        	<td colspan="17" style="border:none;" height="20"></td>
        </tr>
        <tr>
        	<td colspan="17" style="border:none;"><font style="font-size:24px;">Repeating Students Courses</font></td>
        </tr>
        <tr>
        	<td colspan="17" style="border:none;" height="40"></td>
        </tr>
        <tr>
                    <th scope="col">Sr.No</th>
                    <th style="text-align:center;" scope="col">Course Code</th>
                    <th scope="col">Course Title</th>
                    <th style="text-align:center;" scope="col">Course Cr.Hrs</th>
                    <th style="text-align:center;" scope="col">Course Grade</th>
                    <th style="text-align:center;" scope="col">Session</th>
                    <!-- <th scope="col">Re Chance</th> -->
                    <!-- <th scope="col">Student's Contribution</th> -->
                </tr>
	<?php   
    //print_r($rowsSQL);exit;
    $countRow=count($rowsSQL);
    if($countRow){
  
        $index = 1;
   
        foreach($rowsSQL as $row) {
                if($row->rec > 1){


              
           // print_r ($row->sess_code);exit;
				?>
              
             
			 
      <tr>
      <td align="center" height="28"><?=$index?></td>
      <td style="color:#A30000"  align="center"><?=$row->gh_course_code;?></td>
      <td style="color:#A30000;font-size:bold" align="center"><?=$row->course_cd_name;?>*</td>
      <td align="center"><?=$row->gh_co_course_cr;?></td>
      <td align="center"><?=$row->gh_grade;?></td>
      <td style="color:#A30000"  align="center"><?=$row->sess_code;?></td>
        
       
      </tr>
      <?php				
               
                }else{ ?>
                    <tr>
                    <td align="center" height="28"><?=$index?></td>
                    <td align="center"><?=$row->gh_course_code;?></td>
                    <td align="center"><?=$row->course_cd_name;?></td>
                    <td align="center"><?=$row->gh_co_course_cr;?></td>
                    <td align="center"><?=$row->gh_grade;?></td>
                    <td align="center"><?=$row->sess_code;?></td>
                      
                     
                    </tr>
           <?php     }	
            $index++;			
            }//foreach
        ?>
        <tr></tr>
      <tr style="background-color:#CCC;">
        <td colspan="5" align="left">Report generated on:
          <?=date('D d-m-Y H:i:s')?></td>
      </tr>
    </table>
    <?php			
        }
        else
        {//if dataset
        ?>
    
    <tr style="background-color:#FFA8A8">
        <td colspan="3" align="center" height="28" style="font-size:18px;"> No Record Available</td>
        <td colspan="3" align="center" height="28" style="font-size:18px;"> No Record Available</td>
        <td colspan="3" align="center" height="28" style="font-size:18px;"> No Record Available</td>
        <td colspan="3" align="center" height="28" style="font-size:18px;"> No Record Available</td>
        <td colspan="3" align="center" height="28" style="font-size:18px;"> No Record Available</td>
        <td colspan="3" align="center" height="28" style="font-size:18px;"> No Record Available</td>
    </tr>
    <?php
        }
	

?> 