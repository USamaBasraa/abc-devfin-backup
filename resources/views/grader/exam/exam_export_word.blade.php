<?php
header("Content-type: application/vnd.ms-word");  
header("Content-Disposition: attachment;Filename=".rand().".doc");  
header("Pragma: no-cache");  
header("Expires: 0");  
echo $content = '<html>
<head>
<meta charset="utf-8">
</head>
<body>';
$count=count($row);
?>
<h1 align="center">Exam Name : <?=$row[0]->title;?></h1>
<br>
<table border="1" align="center" width="100%">
<?php
$enrollment_no = "";
$sr_no = 1;

foreach($row as $rec){
$get_sai_enrollment_no=$rec->sai_enrollment_no;
if($get_sai_enrollment_no!=$enrollment_no){

echo '<tr><td style="width:100%;background-color:#09F; font-size:36px; color:#FFF;">'.$get_sai_enrollment_no.'</td></td>'; 
    $enrollment_no=$get_sai_enrollment_no;
    $sr_no = 1;
    $count++;
} 
if($rec->exam_ques_type==1){  
    echo   '<tr style=" font-weight:bold"><td>'.$sr_no.' :'.$rec->question.'</td></tr>';
   echo   '<tr><td>'.'option a'.' )'.$rec->oa.'</td></tr>';
   echo   '<tr><td>'.'option b )'.$rec->ob.'</td></tr>';
   echo   '<tr><td>'.'option c )'.$rec->oc.'</td></tr>';
   echo   '<tr><td>'.'option d )'.$rec->od.'</td></tr>';
   echo   '<tr><td>'.'Given answer :'.$rec->answer_given.'</td></tr>';
   echo   '<tr><td>'.'Answer Status :'.$rec->answer_status.'</td></tr>';
   echo   '<tr ><td>'.'Correct answer :'. $rec->correct.'</td></tr>';
}else if($rec->exam_ques_type==3){
 echo   '<tr style=" font-weight:bold"><td>'.$sr_no.': '.$rec->question.'</td></tr>';
  echo   '<tr><td>'.'Given answer'.' '.$rec->answer_given.'</td></tr>';
     echo   '<tr><td>'.'Answer Status'.' :'.$rec->answer_status.'</td></tr>';
      echo   '<tr ><td>'.'Correct answer : '.$rec->correct.'</td></tr>';


}else if($rec->exam_ques_type==2){
 echo   '<tr style=" font-weight:bold"><td>'.$sr_no.' :'.$rec->question.'</td></tr>';
  echo   '<tr><td>'.$rec->answer_given.'</td></tr>';

}else if($rec->exam_ques_type==4){
 echo   '<tr style=" font-weight:bold"><td>'.$sr_no.' :'.$rec->question.'</td></tr>';
  echo   '<tr><td>'.$rec->answer_given.'</td></tr>';

}
$sr_no++;
}
?>
</table>
<?php
$content .='</body></html>';  
//echo $_POST["description"];
?>