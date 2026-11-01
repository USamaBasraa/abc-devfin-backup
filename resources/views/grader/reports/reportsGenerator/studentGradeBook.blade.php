<?php

include('fpdf/fpdf.php');
	foreach($_POST as $name => $val)
	$$name=$val;	
 
	function get_semester_name($semester_number) { 
				   if($semester_number==1)
				   {
					   $semester_name = "I"; 
				   }
				   else if($semester_number==2)
				   {
					   $semester_name = "II";
				   }
				   else if($semester_number==3)
				   {
					   $semester_name = "III"; 
				   }
				   else if($semester_number==4)
				   {
					   $semester_name = "IV"; 
				   }
				   else if($semester_number==5)
				   {
					   $semester_name = "V"; 
				   }
				   else if($semester_number==6)
				   {
					   $semester_name = "VI"; 
				   }
				   else if($semester_number==7)
				   {
					   $semester_name = "VII"; 
				   }
				   else if($semester_number==8)
				   {
					   $semester_name = "VIII"; 
				   }
				   else if($semester_number==9)
				   {
					   $semester_name = "IX"; 
				   }
				   else if($semester_number==10)
				   {
					   $semester_name = "X"; 
				   }
				   else if($semester_number==11)
				   {
					   $semester_name = "XI"; 
				   }
				   else if($semester_number==12)
				   {
					   $semester_name = "XII"; 
				   }
				   else if($semester_number==13)
				   {
					   $semester_name = "XIII"; 
				   }
				   else if($semester_number==14)
				   {
					   $semester_name = "XIV";  
				   }
				   else if($semester_number==15)
				   {
					   $semester_name = "XV"; 
				   }
				   else if($semester_number==16)
				   {
				   $semester_name = "XVI";  
				   }
				   else if($semester_number==17)
				   {
				   $semester_name = "XVII";  
				   }
				   else if($semester_number==18)
				   {
				   $semester_name = "XVIII";  
				   }
				   else if($semester_number==19)
				   {
				   $semester_name = "S-C";  
				   }
				        

					return $semester_name; 
} 
	$roll_no	= strtoupper($pt_sai_enroll_no );

$sqlApi="SELECT gh_sai_id,
prog_degree_level,
				  gh_id,
				  gh_sai_enrollment_no,
				  school_name,
				  dept_name,
				  prog_name,
				  spi_name,
				  spi_fathername,
				  spi_presentaddress,
				  spi_mobile,
				  sai_id,
				  sai_status,
				  students_apply_info.sai_student_status,
				  gh_sai_semester,
				  gh_co_sess_id,
				  sess_name,
				  sess_code,
				  sess_order,
				  ay_name,
				  gh_course_code,
				  gh_cd_name||' '||specialization as gh_cd_name,
				  gh_co_course_cr,
				  trim(gh_grade) AS gh_grade,
				  is_star,
				  gh_sai_semester,
				  gh_sai_sdpo_sess_id,
				  gh_co_course_id,
				  gh_grade_point,
				  (
					SELECT DISTINCT
						gh_exemption_status
					FROM
						grade_history gh2
					WHERE
						gh2.gh_sai_enrollment_no=grade_history.gh_sai_enrollment_no
						and gh_exemption_status IN ('Exchange','U-Grad')
						
					)AS gh_exemption_status,
				  (
					SELECT DISTINCT
						gh_exemption_institute
					FROM
						grade_history gh2
					WHERE
						gh2.gh_sai_enrollment_no=grade_history.gh_sai_enrollment_no
						and gh_exemption_status IN ('Exchange','U-Grad')
						and rownum=1
					)AS gh_exemption_institute,
					(
						SELECT DISTINCT
							sess_name
						FROM
							grade_history gh4
							LEFT JOIN academic_sessions ON gh_co_sess_id = sess_id
						WHERE
							gh4.gh_sai_enrollment_no=grade_history.gh_sai_enrollment_no
							and gh_exemption_status IN ('Exchange','U-Grad')
							
					) AS exchange_session,
				 -- sdpo_credits,
				  SAI_PROG_TOTAL_CREDITS,
				  (SELECT SEMESTER_NAME FROM semesters WHERE semester_id=gh_sai_semester
				  ) AS semester_name,
				  (SELECT SUM(gh3.gh_co_course_cr)
					FROM grade_history gh3
					WHERE gh3.gh_sai_enrollment_no=grade_history.gh_sai_enrollment_no
					AND gh3.gh_exemption_status='Yes' and gh_status = 'E'
				  ) AS sai_transfer_cr,
				  gh_sai_sdpo_prog_id,
				  sai_sdpo_prog_id
				FROM grade_history
				LEFT JOIN students_apply_info
				ON sai_id = gh_sai_id
				LEFT JOIN students_personal_info
				ON students_apply_info.sai_spi_id=students_personal_info.spi_id
				LEFT JOIN academic_sessions
				ON sess_id =grade_history.gh_co_sess_id
				LEFT JOIN schools
				ON school_id = students_apply_info.sai_sdpo_school_id
				LEFT JOIN departments
				ON dept_id = students_apply_info.sai_sdpo_dept_id
				LEFT JOIN programs
				ON prog_id = sai_sdpo_prog_id
				LEFT JOIN academic_years
				ON sess_ay_id =ay_id
				LEFT JOIN session_degree_program_offerin
				ON sdpo_sess_id           =students_apply_info.sai_sdpo_sess_id
				AND sdpo_school_id        =sai_sdpo_school_id
				AND sdpo_dept_id          =sai_sdpo_dept_id
				AND sdpo_prog_id          =sai_sdpo_prog_id
				AND sdpo_shift_id         =sai_sdpo_shift_id
				AND sdpo_status='E'
				WHERE gh_status  ='E' AND (gh_exemption_status!='Yes' OR gh_exemption_status IS NULL)  
				AND gh_exam_fsubmit	=1";


$sqlApi .=" AND gh_sai_enrollment_no IN
			  ( SELECT DISTINCT sai_enrollment_no
			  FROM students_apply_info
			  WHERE 1 = 1  " ;
  	if(isset($_REQUEST['pt_school_id']) and $_REQUEST['pt_school_id']!=''){
		$sqlApi .= "AND sai_sdpo_school_id = '".$_REQUEST['pt_school_id']."' ";	
	}
    if(isset($_REQUEST['pt_dept_id']) and  $_REQUEST['pt_dept_id']!=''){
		$sqlApi .="AND sai_sdpo_dept_id = '".$_REQUEST['pt_dept_id']."' ";
	}
 	if(isset($_REQUEST['pt_prog_id']) and $_REQUEST['pt_prog_id']!=''){
		$pt_prog_id_name   = explode("~",$_REQUEST['pt_prog_id']);
		$sqlApi .= "AND sai_sdpo_prog_id = '".$pt_prog_id_name[0]."' ";	
	}
	if(isset($_REQUEST['pt_shift_id']) and $_REQUEST['pt_shift_id']!=''){
		$pt_shift_id_name   = explode("~",$_REQUEST['pt_shift_id']);
		$sqlApi .= "AND sai_sdpo_shift_id = '".$pt_shift_id_name[0]."' ";	
	}
	if(isset($_REQUEST['sel_student_status']) and $_REQUEST['sel_student_status']!=''){
		$sqlApi .= "AND sai_student_status = '".$_REQUEST['sel_student_status']."' ";	
	}else{
		// $sqlApi .= "AND sai_student_status !='Stuck-Off' ";
		if(trim($checks)==''){
			$sqlApi .= " AND sai_student_status !='Stuck-Off' ";	
	   	} 
	}
$sqlApi .=" AND sai_enrollment_no  IN
				( SELECT DISTINCT gh_sai_enrollment_no
				FROM grade_history
				WHERE 1 =1 " ;

	if(isset($_REQUEST['pt_sess_from_id']) and $_REQUEST['pt_sess_from_id']!='' and isset($_REQUEST['pt_sess_to_id']) and $_REQUEST['pt_sess_to_id']!=''){

		$sqlApi .= "  and  gh_co_sess_id  >='".$_REQUEST['pt_sess_from_id']."' and gh_co_sess_id  <='".$_REQUEST['pt_sess_to_id']."' ";	
	}
$sqlApi .=" ))";

	if(isset($_REQUEST['pt_sai_id']) and $_REQUEST['pt_sai_id']!=''){
		$sqlApi .= " AND gh_sai_id = '".$_REQUEST['pt_sai_id']."' ";	
	}
	if(isset($pt_sai_enroll_no ) and $pt_sai_enroll_no !=''){
		$sqlApi .= " AND gh_sai_enrollment_no = '".$roll_no."' ";	
	}
	//....... in case from student login the botton part would work.............
	if(isset($_REQUEST['url']) and $_REQUEST['url']=='student'){
		$sqlApi .= " AND gh_sai_enrollment_no = '".$mysess->spi_enrollment_no."' ";	
	}
	// ................. ..............................................
	if(isset($_REQUEST['sel_enrollment_no']) and $_REQUEST['sel_enrollment_no']!=''){
		$sqlApi .= " AND gh_sai_enrollment_no = '".$_REQUEST['sel_enrollment_no']."' ";	
	}
	if(isset($_REQUEST['pt_sess_from_id']) and $_REQUEST['pt_sess_from_id']!='' and isset($_REQUEST['pt_sess_to_id']) and $_REQUEST['pt_sess_to_id']!=''){

		$sqlApi .= "  and  gh_co_sess_id  >='".$_REQUEST['pt_sess_from_id']."' and gh_co_sess_id  <='".$_REQUEST['pt_sess_to_id']."' ";	
	}
	
	$sqlApi.="ORDER BY gh_sai_id,grade_history.gh_co_sess_id,gh_id";
	// echo $sqlApi;exit();

	
	$resApi= $mydb->query_select($sqlApi);
	$countStudent = count($resApi);
	if($countStudent==0){
		
		$pdf = new FPDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',36);
		$pdf->SetTextColor(255,0,0);
		$pdf->SetXY(50,50);
		$pdf->Cell(40,10,'1st Semester / Stuck-Off');
		$pdf->Output();
		exit();
	}
class PDF extends FPDF   // Inheriting the parent Class
{
	function initVariables($rec,$from_insq_date,$to_insq_date)
	{
		//$this->from_insq_date   = $GET_STUDENT_NAME;
		//$this->to_insq_date 	= $SPI_FATHERNAME;
	}
	//Page header
	function Header()
	{
		global $school_name;
		global $dept_name;
		global $prog_name;
		global $spi_name;
		global $spi_fathername;
		global $gh_enrollment_no;
		global $spi_present_address;
		global $spi_mobile;
		global $sai_id;
		global $sdpo_credits;
		
		if(isset($_REQUEST['url']) and $_REQUEST['url']=='student'){	
			$this->SetTextColor(200,200,200);
			$this->Rotate(-35, 5, 80);
			$this->SetXY(50, 1);
			$this->Rotate(25,250,150);
			$this->SetFont('times','B',85);
			$this->Cell(200, 150, "Student Copy", 0, false, 'C');
			$this->Rotate(0);
			$this->Ln(25);
		}
		if($_REQUEST['url']!='student'){	
		//FOR HEADING DATA From databse 
		$this->Image('../lib/fpdf/logoimages/bnulogo1.jpg',10,5,15,20);
					
		// Letter Head
		$this->SetTextColor(10,10,10);
		$this->SetXY(28, 10);
		$this->SetFont('times','B',14);
		$this->Cell(0,0,'Beaconhouse National University',0,0,'L');
		$this->SetXY(28, 17);
		$this->SetFont('times','',10);
		$this->Cell(0,0,'13-km Raiwind Road, Lahore 54400, Pakistan',0,0,'L');
		$this->SetXY(28, 22);
		$this->Cell(0,0,'Tel: 92-42-38100156  Email: info@bnu.edu.pk',0,0,'L');
		//serial no simple
		$this->SetFont('helvetica','',8);
		$this->SetXY(160, 25);
		$this->Cell(0,0,$_POST['prov_serial_no']==''? 'Serial no.: Undefined':'Serial no.: '.$_POST['prov_serial_no'],0,0,'L');
		
		if($this->header_Set['isAcademic']!='yes'){
			$this->SetFont('helvetica','',8);
			$this->SetXY(160, 25);
			$this->Cell(0,0,/*$_POST['serial_no']==''? 'Serial no.: Undefined':'Serial no.: '.$_POST['serial_no']*/'',0,0,'L');
		}

		//horinozental line
		$style = array('width' => 0.4, 'color' => array(205,201,201));
		$this->Line(10, 29, 202, 29, $style);					
		
		} // end of if condition for student 
		$this->SetTextColor(10,10,10);
		//transcript head
		$this->SetXY(10, 32);
		$this->SetFont('helvetica','BU',14);
		$this->Cell(192,5,'Provisional Transcript',0,0,'C');
		
		$this->SetXY(10, 40);
		$this->SetFont('helvetica','B',16);
		$this->Cell(192,5,$school_name,0,false,'C');
		
		$this->SetXY(10, 50);
		$this->SetFont('helvetica','B',9);
		$this->Cell(105,5,$dept_name,0,false,'L');
		$this->SetXY(104, 50);
		$this->Cell(98,5,$prog_name,0,false,'R');

		//main border
		$this->SetXY(10, 56);
		$this->SetFont('times','',10);
		$this->SetFillColor(255,255,255);
		$this->SetLineWidth(0.5);
		$this->Cell(192,21,'',1,false,'C',1,1);	
		
							// Set font
		$this->SetXY(15, 58);
		$this->SetFont('times','',11);
		$this->Cell(30,5,'Student I.D #:','0',false,'L');
		$this->SetXY(15, 64);
		$this->Cell(30,5,'Student\'s Name:','0',false,'L');
		$this->SetXY(15, 70);
		$this->Cell(30,5,'Parent Name:','0',false,'L');
		
		//data
		$this->SetXY(45, 58);
		$this->SetFont('helvetica','B',10);
		$this->Cell(40,5,$sai_id,'0',false,'L');
		$this->SetXY(45, 64);
		$this->Cell(40,5,$spi_name,'0',false,'L');
		$this->SetXY(45, 70);
		$this->Cell(40,5,$spi_fathername,'0',false,'L');
		
		$this->SetXY(130, 58);
		$this->SetFont('times','',11);
		$this->Cell(30,5,'Registration No:','0',false,'L');
		$this->SetXY(130, 64);
		$this->Cell(30,5,'Date of Issuance:','0',false,'L');
		$this->SetXY(130, 70);
		$this->Cell(30,5,'Required Cr. Hrs.','0',false,'L');
			
		//data
		$this->SetXY(165, 58);
		$this->SetFont('helvetica','B',10);
		$this->Cell(40,5,$gh_enrollment_no,'0',false,'L');
		$this->SetXY(165, 64);
		$this->Cell(40,5,date('d M, Y'),'0',false,'L');
		$this->SetXY(165, 70);
		$this->Cell(40,5,$sdpo_credits,'0',true,'L');
		$this->Ln();
		
		$this->SetFont('times','B',10);
		$this->Cell(20,5,'Address:',false,false,'L',0);
		
		$this->SetFont('times','',10);
		$this->SetFillColor(255,255,255);
		$this->Cell(75,5,$spi_present_address,false,false,'L',1);
		$this->Ln();
		$this->SetFont('times','B',10);
		$this->Cell(20,5,'Contact #:',false,false,'L',1);
		
		$this->SetFont('times','',10);
		$this->SetFillColor(255,255,255);
		$this->Cell(75,5,$spi_mobile,false,false,'L',1);	
		$this->Ln(6);
		//$this->Ln();
		
		//	watermark	for each page			
			
		
		
		
		
	}
	// with this function you can give page break whenever a group is changed 
	// pass this function  'Y'  to give page break 
	function givePageBreak($pageBreak,$show_level)
	{
		if($pageBreak == 'Y')
		{
			$this->addPage();
		}
		else
		{
			/*************************************************************************/
			// here 200 is a constant and is used for making page break,  
			// it is a variable and page size is A4. 
			// You will have to choose when to give break in the page, depending upon your page size and displaying data  
			if($this->GetY() > 215)  { 
				$this->addPage();
			} else {
				//$this->showGroupHeadings($show_level);
			}
		}
	}

	//Page footer
	function Footer()
	{
		
				//footer info
			//....... in case from student login the botton part would work.............
		if($_REQUEST['url']!='student'){
				$this->SetXY(10, -14);
				$this->SetFont('times','BI',11);
				
				$style = array('width' => 0.3, 'color' => array(0,0,0));
				$this->Line(10, $this->GetY(), 52, $this->GetY(), $style);
				$this->Cell(42,5,'Controller Examinations'.$_REQUEST['url'],0,false,'C');
		
			
				$this->SetXY(85, -14);
				$this->Line(85, $this->GetY(), 129, $this->GetY(), $style);
				$this->Cell(40,5,'Dean',0,false,'C');
				
				$this->SetXY(160, -14);
				$this->Line(157, $this->GetY(), 201, $this->GetY(), $style);
				$this->Cell(40,5,'Registrar',0,true,'C');
				
		
					}
	// ................. ..............................................		
		//if($this->header_Set['isAcademic']!='yes'){
//					$this->SetXY(175, -9);
//					$this->SetFont('helvetica','',8);
//					$this->Cell(30,5,'Page '.$this->PageNo().'of {nb}',0,0,'R');
//				}
				$this->SetFont('Arial','I',6);
				//$this->Cell(5,5);
				$date= date('D d-m-Y H:i:s');
				//$this->Cell(30, 5, '  ', 0,0, 'L');
				$this->Cell(170,5);
				$this->Cell(30,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');
		
	}

	

}
//echo mssql_num_rows($rsAdmission_Inq1) ." ". $sqlAdmission_Inq1;
if($countStudent>0)
	{
	$pdf=new PDF('P','mm','letter');   // creating Object of FPDF class to use its function 
	//$pdf->AliasNbPages();
	$enrollment_no = "";
	$com_semester_id="";
	$com_sess_id="";
	$com_gpa_cgpa="";
	$srno=0;
	$semester_credit_hourse=0;
	$semester_credit_hourse_er=0;
	$sem_change_calculation = "";
	$countKey = 0;//for loop count
	$start_enrollment_no = "";
	$start_sem_earned_cr = "";//this variable is used to run untill the same semster contines & as semster change it agains reset
	$enrollment_no_footer = "";
	$semester_num = 0;
	foreach($resApi as $rowApi){
		// for array calculation
		$gh_enrollment_no=$rowApi['GH_SAI_ENROLLMENT_NO'];
		
		if($start_enrollment_no!= $gh_enrollment_no){
			$semester_credit_hourse=0;
			$start_enrollment_no =  $gh_enrollment_no;
			$semester_credit_hourse=0;
			$cgpa_credits_points = 0;
			$ctotal_credit_points =  0;
			$cgpa= 0;
			$er_credit_hours=0;
			$cgpa_credit_hourse=0;
			
			$e_cr = 0;
			$sgpa_cr = 0;
			$sgrade_point = 0;
			$cgpa_cr = 0;
			$cgrade_point = 0;
			$sem_cr = 0;
		}
		if (($rowApi['GH_CO_SESS_ID']!=$resApi[$countKey-1]['GH_CO_SESS_ID']) or ($rowApi['GH_SAI_ENROLLMENT_NO']!=$resApi[$countKey-1]['GH_SAI_ENROLLMENT_NO'])){
			$sgpa_cr = 0;
			$sgrade_point = 0;
		}
		
		if ($rowApi['GH_SAI_ENROLLMENT_NO']!=$resApi[$countKey+1]['GH_SAI_ENROLLMENT_NO']){
			$std_n_change = 'Y';
			$std_n_sem_change = 'Y';

		} else {
			$std_n_change = 'N';
			if ($rowApi['GH_CO_SESS_ID']!=$resApi[$countKey+1]['GH_CO_SESS_ID']){
				$std_n_sem_change = 'Y';
			} else {
				$std_n_sem_change = 'N';
			}
		}
		if ($rowApi['GH_SAI_ENROLLMENT_NO']!=$resApi[$countKey-1]['GH_SAI_ENROLLMENT_NO']){
			$std_p_change = 'Y';
			$std_p_sem_change = 'Y';

		} else {
			$std_p_change = 'N';
			if ($rowApi['GH_CO_SESS_ID']!=$resApi[$countKey-1]['GH_CO_SESS_ID']){
				$std_p_sem_change = 'Y';
			} else {
				$std_p_sem_change = 'N';
			}
		}
		$keys = array_keys(array_column($resApi, 'GH_CO_COURSE_ID'), $rowApi['GH_CO_COURSE_ID']);
		$allGradePoints = array();
		$allGradeCodes = array();
		$allGradePoints_sem = array();
		
		$allRepRows = array();
		foreach($keys as $key){
			if($resApi[$key]['GH_SAI_ENROLLMENT_NO']==$rowApi['GH_SAI_ENROLLMENT_NO']){
				$allGradePoints[] = $resApi[$key]['GH_GRADE_POINT'];
				$allGradeCodes[] = $resApi[$key]['GH_ID'];
			}
			//for semseter cgpa count
			if($resApi[$key]['GH_SAI_ENROLLMENT_NO']==$rowApi['GH_SAI_ENROLLMENT_NO'] && $resApi[$key]['GH_SAI_SEMESTER']==$rowApi['GH_SAI_SEMESTER']){
				$allGradePoints_sem[] = $resApi[$key]['GH_GRADE_POINT'];
				
			}	
			if($resApi[$key]['GH_SAI_ENROLLMENT_NO']==$rowApi['GH_SAI_ENROLLMENT_NO'] && $resApi[$key]['GH_CO_COURSE_ID']==$rowApi['GH_CO_COURSE_ID']){
				$allRepRows[] = array('sess_id'=>$resApi[$key]['GH_CO_SESS_ID'], 'course_id'=>$resApi[$key]['GH_CO_COURSE_ID'], 'grade'=>trim($resApi[$key]['GH_GRADE']), 'course_cr'=>$resApi[$key]['GH_CO_COURSE_CR'], 'grade_point'=>trim($resApi[$key]['GH_GRADE_POINT']));
			}	
		}
		$tot_rep_count1 = 0;
		foreach($allRepRows as $cntRep){
			$tot_rep_count1++;
		}
		$tot_rep_count = 0;
		$tot_rep_count = count($allRepRows);
		//print_r($allRepRows);
		//echo count($allRepRows)." - ".$tot_rep_count1.'<br/>' ;
		// ............................................
		if(($tot_rep_count1)>1){
		//if($allRepCount > 0){
			$repStar = '*';//.$tot_rep_count1.' ';
		} else {
			$repStar = ' ';//.$tot_rep_count1.' ';
		}
		$repeat_num = count($allGradePoints);
		$all_points_sum = array_sum($allGradePoints_sem);	
		
		
		// if (trim($rowApi['GH_GRADE'])!='W' and trim($rowApi['GH_GRADE'])!='I' ){
		if (trim($rowApi['GH_GRADE'])!='P' and trim($rowApi['GH_GRADE'])!='W' and trim($rowApi['GH_GRADE'])!='I' ){
			$allRepCount = 0;
			$noSemCount = 'Y';
			$cMaxCr = $rowApi['GH_CO_COURSE_CR'];
			$pMaxGp = 0;
			$cMaxGp = $rowApi['GH_GRADE_POINT']*$rowApi['GH_CO_COURSE_CR'];
			$crDiff = 0;
			if($tot_rep_count){
				foreach($allRepRows as $allRepRow){
					if($rowApi['GH_CO_COURSE_ID'] == $allRepRow['course_id']){
						if($rowApi['GH_CO_SESS_ID'] == $allRepRow['sess_id'] and trim($rowApi['GH_GRADE']) == $allRepRow['grade']){
							$sgpa_cr += $rowApi['GH_CO_COURSE_CR'];
							$sgrade_point += $rowApi['GH_GRADE_POINT']*$rowApi['GH_CO_COURSE_CR'];
							//if($allRepCount>0){
								for($i=$allRepCount-1; $i>=0; $i--){
									if($allRepRows[$i]['grade']!='I' and $allRepRows[$i]['grade']!='W'){	
										if ($rowApi['GH_CO_COURSE_CR'] != $allRepRows[$i]['course_cr'] ) {
											$crDiff = $rowApi['GH_CO_COURSE_CR'] - $allRepRows[$i]['course_cr'];
											break;
										}
									}
								}
								
							//}
							if(trim($rowApi['GH_GRADE'])!='F'){
								
								for($i=$allRepCount-1; $i>=0; $i--){
									if( $allRepRows[$i]['grade']=='A'
										or $allRepRows[$i]['grade']=='A-'
										or $allRepRows[$i]['grade']=='B+'
										or $allRepRows[$i]['grade']=='B'
										or $allRepRows[$i]['grade']=='B-'
										or $allRepRows[$i]['grade']=='C+'
										or $allRepRows[$i]['grade']=='C'
										or $allRepRows[$i]['grade']=='C-'
										or $allRepRows[$i]['grade']=='D+'
										or $allRepRows[$i]['grade']=='D'){
											$noSemCount = 'N';
									}
									if( $allRepRows[$i]['grade']=='F'
										or $allRepRows[$i]['grade']=='A'
										or $allRepRows[$i]['grade']=='A-'
										or $allRepRows[$i]['grade']=='B+'
										or $allRepRows[$i]['grade']=='B'
										or $allRepRows[$i]['grade']=='B-'
										or $allRepRows[$i]['grade']=='C+'
										or $allRepRows[$i]['grade']=='C'
										or $allRepRows[$i]['grade']=='C-'
										or $allRepRows[$i]['grade']=='D+'
										or $allRepRows[$i]['grade']=='D'){
											$cMaxCr = 0;
									}
									if($cMaxGp <= $allRepRows[$i]['grade_point']*$allRepRows[$i]['course_cr']){
										$cMaxGp = 0;
									}
									
								}
							} else if (trim($rowApi['GH_GRADE'])=='F'){
								$noSemCount = 'N';
								
								for($i=$allRepCount-1; $i>=0; $i--){
									if($allRepRows[$i]['grade']=='F'
									or $allRepRows[$i]['grade']=='A'
									or $allRepRows[$i]['grade']=='A-'
									or $allRepRows[$i]['grade']=='B+'
									or $allRepRows[$i]['grade']=='B'
									or $allRepRows[$i]['grade']=='B-'
									or $allRepRows[$i]['grade']=='C+'
									or $allRepRows[$i]['grade']=='C'
									or $allRepRows[$i]['grade']=='C-'
									or $allRepRows[$i]['grade']=='D+'
									or $allRepRows[$i]['grade']=='D'
									){
										$cMaxCr = 0;
									}
									if($cMaxGp <= $allRepRows[$i]['grade_point']*$allRepRows[$i]['course_cr']){
										$cMaxGp = 0;
									}
								}
								
							}
							for($i=$allRepCount-1; $i>=0; $i--){	
								if($allRepRows[$i]['grade_point'] > $pMaxGp) 
									$pMaxGp = ($allRepRows[$i]['grade_point']*$allRepRows[$i]['course_cr']);
							}
							if($pMaxGp == $rowApi['GH_GRADE_POINT']*$rowApi['GH_CO_COURSE_CR']){
								$pMaxGp = 0;
							}	
							break;
						}
						
					}
					$allRepCount++;
				}
			}//if before loop
			else {
				if(trim($rowApi['GH_GRADE'])!='P'){
					//$cgpa_cr      += $rowApi['GH_CO_COURSE_CR'];
					//$cgrade_point += $rowApi['GH_GRADE_POINT']*$rowApi['GH_CO_COURSE_CR'];
				
					$sgpa_cr      += $rowApi['GH_CO_COURSE_CR'];
					$sgrade_point += $rowApi['GH_GRADE_POINT']*$rowApi['GH_CO_COURSE_CR'];
				}
				if(trim($rowApi['GH_GRADE'])=='F'){
					$noSemCount = 'N';
				}
			}
			
			if($noSemCount == 'Y'){
				if(isset($_REQUEST['pt_sess_from_id']) and $_REQUEST['pt_sess_from_id']!='' and isset($_REQUEST['pt_sess_to_id']) and $_REQUEST['pt_sess_to_id']!=''){
					if (($rowApi['GH_CO_SESS_ID']>=$_REQUEST['pt_sess_from_id'] ) and ($rowApi['GH_CO_SESS_ID']<=$_REQUEST['pt_sess_to_id']) ){
						$sem_cr += $rowApi['GH_CO_COURSE_CR'];
					} 
				} else {
					$sem_cr += $rowApi['GH_CO_COURSE_CR'];	
				}
				$e_cr += $rowApi['GH_CO_COURSE_CR'];
				
			}
			$cgpa_cr += $cMaxCr;
			if(!$cMaxGp){ $pMaxGp=0; }
			$cgrade_point = $cgrade_point + $cMaxGp - $pMaxGp;
		}
		if (trim($rowApi['GH_GRADE'])=='P'){
			$sem_cr += $rowApi['GH_CO_COURSE_CR'];
			$e_cr += $rowApi['GH_CO_COURSE_CR'];
		}
		$cgpa_cr += $crDiff;
		//$crDiff = 0;
		$school_name         = $rowApi['SCHOOL_NAME'];
		$dept_name           = $rowApi['DEPT_NAME'];
		$prog_name           = $rowApi['PROG_NAME'];
		$spi_name            = $rowApi['SPI_NAME'];
		$spi_fathername      = $rowApi['SPI_FATHERNAME'];
		$gh_enrollment_no    = $rowApi['GH_SAI_ENROLLMENT_NO'];
		$spi_present_address = $rowApi['SPI_PRESENTADDRESS'];
		$spi_mobile          = $rowApi['SPI_MOBILE'];
		$sai_id              = $rowApi['SAI_ID'];
		
		$get_semster         = $rowApi['GH_SAI_SEMESTER'];
		$get_co_sess_id      = $rowApi['GH_CO_SESS_ID'];
		$show_semester       = $rowApi['SEMESTER_NAME']; 
		$spi_mobile          = $rowApi['SPI_MOBILE'];
		$sai_id              = $rowApi['SAI_ID'];
		$sdpo_credits        = $rowApi['SAI_PROG_TOTAL_CREDITS'];
		$sai_transfer_cr     = $rowApi['SAI_TRANSFER_CR'];
		
		$get_course_code    = $print_start."".$rowApi['GH_COURSE_CODE'];
		$get_course_credits = $rowApi['GH_CO_COURSE_CR'];
		$get_course_grade   = $rowApi['GH_GRADE'];
		$is_star			= $rowApi['IS_STAR'];	
		$gh_grade_points    = $rowApi['GH_GRADE_POINT'];	
		$semp               = $resApi[$srno]['GH_SAI_SEMESTER']; // current semeter in array
		$semp_second        = $resApi[$srno+1]['GH_SAI_SEMESTER']; // next semester in array
		
		$current_sess       = $resApi[$srno]['GH_CO_SESS_ID']; // current semeter in array
		$second_sess        = $resApi[$srno+1]['GH_CO_SESS_ID']; // next semester in array
		$institute 			= $rowApi['GH_EXEMPTION_INSTITUTE'];
		$exchange_sess		= $rowApi['EXCHANGE_SESSION'];
		$exemption_status	= $rowApi['GH_EXEMPTION_STATUS'];
		$prog_degree_level	= $rowapi['PROG_DEGREE_LEVEL'];
		
		
		$doprint = 'Y';
		if(isset($_REQUEST['pt_sess_from_id']) and $_REQUEST['pt_sess_from_id']!='' and isset($_REQUEST['pt_sess_to_id']) and $_REQUEST['pt_sess_to_id']!=''){
			if (($rowApi['GH_CO_SESS_ID']>=$_REQUEST['pt_sess_from_id'] ) and ($rowApi['GH_CO_SESS_ID']<=$_REQUEST['pt_sess_to_id']) ){
				$doprint = 'Y';
			} else {
				$semester_credit_hourse_er=0;
				$doprint = 'N';
			}
		} 
		// here it check if enrollment no change then it add page 
		if($enrollment_no!=$gh_enrollment_no){
			$pdf->AddPage();
			$pdf->AliasNbPages();
			$enrollment_no=$gh_enrollment_no;
		}
			
		// AFter Add page first time headings
		if ($doprint == 'Y'){	
			if($std_p_sem_change == 'Y'){
				if(trim($rowApi['SAI_SDPO_PROG_ID'])=='725'){
					$semester_num++;
					$show_semester_name = get_semester_name($semester_num);
				}else{
					if(substr(trim($rowApi['SESS_NAME']),0,4)=="Summ"){
						$show_semester_name = get_semester_name(19);
					}else{
						$semester_num++;
						$show_semester_name = get_semester_name($semester_num);
					}
				}
				
				$pdf->SetFont('helvetica','B',9);
				$pdf->Cell(20,6,'Semester:',0,false,'L');
				//$pdf->Cell(40,6,$show_semester_name."_".trim($show_semester),0,false,'L');
				$pdf->Cell(40,6,$show_semester_name,0,false,'L');
				$pdf->Cell(25,6,'Exam Session:',0,false,'R');
				$pdf->Cell(60,6,trim($rowApi['SESS_NAME']),0,false,'L');
				$pdf->Cell(25,6,'Academic Year:',0,false,'R');
				$pdf->Cell(22,6,trim($rowApi['AY_NAME']),0,false,'R');
				$pdf->Ln();
				//table headers
				$style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0);
				$pdf->Line(10, $pdf->GetY(), 202, $pdf->GetY(),$style);
				$pdf->Ln(0.5);
				$pdf->Line(10, $pdf->GetY(), 202, $pdf->GetY(),$style);
				$pdf->Ln(0.5);
				
				$pdf->SetFillColor(139,137,137);
				
				$pdf->Cell(25,5,'Course Code',1,false,'C',1);
				$pdf->Cell(121,5,'Course Title',1,false,'C',1);				
				$pdf->Cell(23,5,'Credits',1,false,'C',1);
				$pdf->Cell(23,5,'Grade',1,1,'C',1);
				$pdf->SetFont('helvetica','',9);
				$pdf->Ln(0.5);
				$pdf->Line(10, $pdf->GetY(), 202, $pdf->GetY(),$style);
				$pdf->Ln(1);
			}
		}
			
		
		//course Printing start ________  ..........................
		if ($doprint == 'Y' ){

			// bottom code just mention software engineers by adding record in db which semester no is wrong
			if($show_semester_name!=trim($show_semester)){
				$details_of_testing = 'Provisional Transcript change semester '.$show_semester.' to '.$show_semester_name.' of student '.$rowApi['GH_SAI_ENROLLMENT_NO'];
				$sql_add="insert into testing(description,record_type,value_one,value_two) values ('".trim($details_of_testing)."','wrong_semester_no','".$rowApi['GH_ID']."','".$show_semester_name."')";
				$resTransferStudents = $mydb->query_insert($sql_add);	
			}
			// remove the upper record if report seems to be slow

			$pdf->SetX(10);
			$pdf->Cell(25,5,$repStar.trim($get_course_code),1,false,'C',0);
			$pdf->Cell(121,5,trim($rowApi['GH_CD_NAME']),1,false,'L',0);
			$pdf->Cell(23,5,trim($get_course_credits),1,false,'C',0);
			$pdf->Cell(23,5,trim($get_course_grade).$is_star,1,false,'C',0);
			
			//$pdf->Cell(25,5,$repStar.trim($get_course_code),1,false,'C',0);
//			//$crs_dtl = ' - '.$rowApi['GH_GRADE_POINT'].' SemCr='.$sem_cr.' CgpaCr='.$cgpa_cr.' $crDiff='.$crDiff.' rep='.$allRepCount.' pMaxGp='.$pMaxGp.' repCount = '.(count($allRepRows));//.' sem cng = '.($std_n_sem_change);
//			//$crs_dtl = ' - '.$rowApi['GH_GRADE_POINT'].' SemCr='.$sem_cr.' CgpaCr='.$cgpa_cr.' $crDiff='.$crDiff.' rep='.$allRepCount.' pMaxGp='.$pMaxGp.' repCount = '.(count($allRepRows));//.' sem cng = '.($std_n_sem_change);
//			
//			$crs_dtl= "_______________".$cgrade_point."__".$cgpa_cr.' pMaxGp='.$pMaxGp.' cMaxGp='.$cMaxGp;
//			
//			$pdf->Cell(121,5,trim($rowApi['GH_CD_NAME']).$crs_dtl,1,false,'L',0);				
//			$pdf->Cell(23,5,trim($get_course_credits),1,false,'C',0);
//			$crs_gp_dtl = ' '.($rowApi['GH_GRADE_POINT']*$rowApi['GH_CO_COURSE_CR']);
//			$pdf->Cell(23,5,trim($get_course_grade).$crs_gp_dtl,1,false,'C',0);
			//$pdf->Cell(23,5,$crs_gp_dtl,1,false,'C',0);
			$pdf->Ln();
		}
		// semester totals
		if($std_n_sem_change=='Y'){
			$com_gpa_cgpa=$get_semster;// assign value to semeter		
			$pdf->Ln(1);
			if ($doprint == 'Y' ){
				$pdf->SetFont('helvetica','B',9);
				$pdf->SetFillColor(190,190,190);
				//$pdf->Cell(20,5,'SGPA '.$sgpa_cr,1,false,'C',1);
				$pdf->Cell(20,5,'SGPA ',1,false,'C',1);          
				$pdf->Cell(12,5,((sprintf('%.2F',round(($sgrade_point/$sgpa_cr),2))>0)?sprintf('%.2F',round(($sgrade_point/$sgpa_cr),2)):'0.00')  ,1,0,'C');
				$pdf->Cell(40,5,'',0,false,'C');
				$pdf->Cell(30,5,'Credit Hrs. Earned',1,false,'C',1);
				$pdf->Cell(12,5,$sem_cr,1,false,'C');
				$pdf->Cell(41,5,'',0,false,'C');
				//$crs_cgpa_cal = $cgrade_point.'-'.$cgpa_cr;
				$pdf->Cell(25,5,'CGPA'.$crs_cgpa_cal,1,false,'C',1);
				$pdf->Cell(12,5,((sprintf('%.2F',round(($cgrade_point/$cgpa_cr),2))>0)?sprintf('%.2F',round(($cgrade_point/$cgpa_cr),2)):'0.00'),1,1,'C');
				$pdf->SetFont('helvetica','',9);
				$pdf->Ln();
			}
		}
		$final_cgpa = sprintf('%.2F',round(($cgrade_point/$cgpa_cr),2));
		$chk_final_cgpa = $final_cgpa*100;
		if($prog_degree_level == 'Undergraduate'){
			if($sdpo_credits<=$e_cr and $chk_final_cgpa>=200){
				$show_degree_status="Completed ";
			}
			else{
				$show_degree_status="In Complete ";
			}
		}else{
			if($sdpo_credits<=$e_cr and $chk_final_cgpa>=250){
				$show_degree_status="Completed ";
			}
			else{
				$show_degree_status="In Complete ";
			}			
		}
		if($std_n_change == 'Y'){	
			$pdf->Ln(2);
			$pdf->Cell(30,5,'Transfer Cr Hrs',1,false,'C',1);
			$pdf->Cell(12,5,$sai_transfer_cr==0 ? '0' : $sai_transfer_cr,1,false,'C');
			//////-------------------
			if(isset($_REQUEST['htxtSessOrder'])and ($_REQUEST['htxtSessOrder']=='')){
					if($_REQUEST['is_academic']!='yes'){
						$pdf->Cell(21,5,'',0,false,'C');
						$pdf->Cell(60,5,'Degree Requirements: '.$show_degree_status,1,false,'C',1);
					}else{
						$pdf->Cell(21,5,'',false,false,'C');
						$pdf->Cell(60,5,'',false,false,'C');
					}
			}
		
	
			$pdf->SetX(133);
			$pdf->Cell(20,5,'',0,false,'C');
			$pdf->Cell(37,5,'Total Credit Hrs. Earned',1,false,'C',1);
			//$pdf->Cell(12,5,($test),1,1,'C');
			//$pdf->Cell(12,5,($e_cr+intval($sai_transfer_cr)),1,1,'C');
			//$pdf->Cell(12,5,($e_cr+intval($sai_transfer_cr)),1,1,'C');
            $pdf->Cell(12,5,($e_cr+($sai_transfer_cr)),1,1,'C');
			$pdf->Ln(1.5);
			if(($institute!='')or($exchange_sess!='')){
				$pdf->SetFont('helvetica','',8);
				if($exemption_status != 'U-Grad'){
				$pdf->MultiCell(50,10,'In'.'('.$exchange_sess.')'.'Cr.Hrs.earned from: '.$institute . chr(10).'under Student Exchange Program','1',0,'L',false);
				}else{
					$pdf->MultiCell(50,10,'In'.'('.$exchange_sess.')'.'Cr.Hrs.earned from: '.$institute . chr(10).'under U-Grad Program','1',0,'L',false);
				}
			}
			$pdf->SetX(147);
			$pdf->SetFont('helvetica','B',13);
			$pdf->Cell(5,6,'*','LTB',false,'R');
			$pdf->SetFont('helvetica','',9);
			$pdf->Cell(50,6,'Passed in more than one attempt','RTB',1,'L');
			$pdf->Ln(3.5);
	
			$pdf->Ln(1.5);
			//________________________________________________________
			$pdf->SetFont('helvetica','B',9);
			$pdf->Cell(30,6,'Note:',0,false,'C');
			$pdf->SetFont('times','BI',9);
			$pdf->MultiCell(162,6,'Any entry appearing in Provisional Transcript does not itself confer any right or privilege for the grant of degree which will be issued under the rules',0,1,'L',true);
			if($_POST['is_academic']!='yes'){
				
						
				//$pdf->Cell(162,6,'asdfasdf','RTB',1,'M');
				//$pdf->Ln();
				//$pdf->Cell(162,6,'333333333','RTB',1,'M');
				
				//$pdf->Cell(162,5,'Any entry appearing in Provisional Transcript does not itself confer.' , 1, '', 0, 0, '', '', true, 0, false, true, 5, 'M', false);
				//$pdf->Cell(162,5,'any right or privilege for the grant of degree which will be issued under the rules.' , 1, '', 0, 0, '', '', true, 0, false, true, 5, 'M', false);
			}else{
				//$pdf->MultiCell(162,5,"This grade report is issued as a notice only. Errors and omissions are expected. \nAny entry appearing in Provisional Transcript does not itself confer any right or privilege for the grant of degree which will be issued under the rules." , 1, '', 0, 0, '', '', true, 0, false, true,5, 'M', false);
			}
						
			//$pdf->y=$pdf->y+1;
			$pdf->Ln();
			
			$pdf->SetFont('helvetica','B',9);
			$pdf->Cell(30,5,'Grading Legend:',0,false,'C');
			$pdf->Cell(10,5,'A',1,false,'C');
			$pdf->Cell(10,5,'4.00',1,false,'C');
			$pdf->Cell(10,5,'B+',1,false,'C');
			$pdf->Cell(10,5,'3.33',1,false,'C');
			$pdf->Cell(10,5,'B-',1,false,'C');
			$pdf->Cell(10,5,'2.67',1,false,'C');
			$pdf->Cell(10,5,'C',1,false,'C');
			$pdf->Cell(10,5,'2.00',1,false,'C');
			$pdf->Cell(10,5,'D+',1,false,'C');
			$pdf->Cell(10,5,'1.33',1,false,'C');
			$pdf->Cell(10,5,'F/I/W',1,false,'C');
			$pdf->Cell(10,5,'0.00',1,1,'C');
			
			$pdf->Cell(30,5,'',0,false,'C');
			$pdf->Cell(10,5,'A-',1,false,'C');
			$pdf->Cell(10,5,'3.67',1,false,'C');
			$pdf->Cell(10,5,'B',1,false,'C');
			$pdf->Cell(10,5,'3.00',1,false,'C');
			$pdf->Cell(10,5,'C+',1,false,'C');
			$pdf->Cell(10,5,'2.33',1,false,'C');
			$pdf->Cell(10,5,'C-',1,false,'C');
			$pdf->Cell(10,5,'1.67',1,false,'C');
			$pdf->Cell(10,5,'D',1,false,'C');
			$pdf->Cell(10,5,'1.00',1,false,'C');
			$pdf->Cell(10,5,'EX',1,false,'C');
			$pdf->SetFont('helvetica','',5);
			$pdf->Cell(10,5,'Exemption',1,1,'C');
			
			//....... in case from student login the botton part would work.............
	if(isset($_REQUEST['url']) and $_REQUEST['url']=='student'){
		$pdf->Ln();
		$pdf->SetX(40);
		$pdf->SetFont('helvetica','B',12);
		$pdf->Cell(15,5,'Note:',0,0,'L');
		$pdf->SetFont('helvetica','',10);	
		$pdf->Cell(150,5,'This Transcript cannot be used for official purpose.',0,1,'L');
	}
	// ................. ..............................................
					
		}

		//$pdf->Cell(10,5,'Exemption',1,1,'C');			
		//if($pdf->GetY()>=260) $pdf->AddPage();
		$pdf->givePageBreak('N',2);			
		$srno++;	
		$countKey++;	
	}
	
	//exit();
	ob_end_clean();
	$pdf->Output('','I'); 
	$pdf->Close(); // Close PDF Object
} else {
	echo "<center><font color=red size=2><b>No Record Found </b></font></center>";
	//dbClose($db); //Close DB Obbject 
}

ob_end_flush(); 
?>

