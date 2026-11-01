<?php


include('fpdf/fpdf.php');

class PDF extends FPDF   // Inheriting the parent Class
{

    public $grading;
    public $total_assessments;
    public $teacher_name;
    public $dept_name;
    public $course_name_studentwise;
    public $assess;
    public $res_grading;
    public $res1;
    public $grader;
    public $assessment_short_name;
    public $cc_weightage;
    public $assess_weightage;
    public $assess_weightage_count;

    function Header()
    {
        $this->Image('fpdf/logoimages/bnulogo1.jpg',10,5,15,20);
        // Letter Head
        $this->SetXY(35, 10);
        $this->SetFont('times', 'B', 23);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 0, 'Beaconhouse National University', 0, 0, 'L');
        $this->SetXY(35, 17);
        $this->SetFont('times', '', 16);
        $this->Cell(0, 0, '13-km Raiwind Road, Lahore 54400, Pakistan', 0, 0, 'L');
        $this->SetXY(35, 22);
        $this->Cell(0, 5, 'Tel: 92-42-38100156 Fax:(92) 42-5760254 Email: info@bnu.edu.pk', 0, 1, 'L');
        $this->Cell(18,2);
        $this->ln(8);
        $this->Cell(0, 0, '', 1, 1, 'C');
        $this->ln(5);
        $this->SetFont('helvetica', '', 15);
        if($this->school_name!=''){
            $this->Cell(140,10, $this->school_name, 0,0, 'L',0);
        }else{
            $this->Cell(140,10, ' ' , 0,0,'L',0);
        }
        if($this->sess_name!=''){
            $this->Cell(280,10, $this->sess_name, 0,1, 'R',0);
        }else{
            $this->Cell(280,10, ' ' , 0,0,'R',0);
        }
        if($this->teacher_name!=''){
            $this->Cell(140,10, $this->teacher_name, 0,0, 'L',0);
        }else{
            $this->Cell(140,10, ' ' , 0,0,'L',0);
        }
        if($this->grader!=''){
            $this->Cell(280, 10,'Grading Type'.' '.$this->grader, 0,0, 'R',0);
        }else{
            $this->Cell(280, 10, ' ' , 0,0, 'R',0);
        }
        $this->SetFont('helvetica', 'B', 18);

        $this->SetXY(170,60);
        $this->Cell(100, 12, 'Summary Percentage Marks ', 0, 0, 'C');
        $this->SetFont('helvetica', '', 18);
        $this->SetXY(170,72);
        $this->Cell(100, 12, $this->course_name_studentwise, 0, 0, 'C');
        $this->SetFont('helvetica', 'BU', 15);

        $this->SetFont('Arial','B',17);
        $this->SetXY(227,55);
        $this->SetTextColor	(250,0,0);
        $this->showHeadings2();
        $this->SetTextColor(0, 0, 0);

        if($this->PageNo() == 1){

            $this->showHeadings1();
            $this->AddPage();
        } else {
            $this->showHeadings();
            // Showing Detail Headings
        }
        $this->SetFont('helvetica', 'B', 17);
        $this->SetTextColor(0, 0, 0);
        $this->ln(0);
      //  $this->showHeadings();
    }
    function catch_null($var1,$var2){
        if($var1<$var2){
           return $var1;
        }else{
            return 1;
        }
        try{
            return $var;
        }
        catch (Exception $e) {
            return  $var='No';
        }
    }
    // shows Headings For Detail Records
    function showHeadings()
    {
        $this->SetXY(20,80);
        $this->Ln(5);
        $this->SetFillColor(210,210,210);
        $this->SetFont('Arial','B',11);		//$this->SetLineWidth(.002);
        $this->Cell(10,22,'Sr #','1','0','C',1);
        $this->Cell(30,22,'Reg No'          ,'1','0','C',1);
        $this->Cell(70,22,'Name' 	        ,'1','0','C',1);
        foreach($this->assess_weightage as $weightage){
            $assessment =  $weightage->assessment_short_name;
            $totalmarks = $weightage->cc_weightage;
            $count = $this->assess_weightage_count;
            $cell_width = 280/$count;
            $this->SetFont('Arial','B',11);
            $this->Cell($cell_width, 22, $assessment."/".$totalmarks, 'T,R', '0', 'C',1);
        }
        $this->Cell(15,22,'Total' 	 ,'1','0','C',1);
        $this->Cell(15,22,'Grade' 	 ,'1','1','C',1);

    }
    function showHeadings2()
    {
        $this->SetXY(280,218);
        $this->SetFont('Arial','B',14);
        $this->Cell(30,10,'Disclaimer:' ,'0','0','L',0);
        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial','BU',14);
        $this->Cell(20,10,'The Grey box indicates marks are considered' ,'0','0','L',0);


    }
    function showHeadings1()
    {
        $this->Ln(18);
        $this->SetFillColor(210,210,210);
        $this->SetFont('Arial','B',11);		//$this->SetLineWidth(.002);

        $this->SetXY(120,90);
        $this->Cell(50,10,'Assessment Name'     ,'1','0','C',1);
        $this->Cell(40,10,'Grading Method'      ,'1','0','C',1);
        $this->Cell(40,10,'Total Entered'       ,'1','0','C',1);
        $this->Cell(30,10,'Selected' 	        ,'1','0','C',1);
        $this->Cell(40,10,'Weightage' 	        ,'1','1','C',1);
        if ($this->res_grading!=''){
            foreach($this->res_grading as $method){
                $assessment_name =  $method->assessment_name;
                $method_name	 =  $method->method_name;
                $total_enter	 =  $method->total_enter;
                $best_of_no		 =  $method->best_of_no;
                $weightage		 =  $method->cc_weightage;

                $this->SetX(120);
                $this->Cell(50,12,$assessment_name  ,'1','0','L',0);
                $this->Cell(40,12,$method_name	    ,'1','0','L',0);
                $this->Cell(40,12,$total_enter	    ,'1','0','C',0);
                $this->Cell(30,12,$best_of_no       ,'1','0','C',0);
                $this->Cell(40,12,$weightage."%"	,'1','1','C',0);
            }
        }
    }
    function ledger()
    {
        $this->SetX(20);
        $this->SetFillColor(210,210,210);
        $this->SetFont('Arial','B',11);
        $this->SetX(160);	//$this->SetLineWidth(.002);
        $this->Cell(10,10,'A'  ,'1','0','C',1);
        $this->Cell(10,10,'A-' ,'1','0','C',1);
        $this->Cell(10,10,'B+' ,'1','0','C',1);
        $this->Cell(10,10,'B'  ,'1','0','C',1);
        $this->Cell(10,10,'B-' ,'1','0','C',1);
        $this->Cell(10,10,'C+' ,'1','0','C',1);
        $this->Cell(10,10,'C'  ,'1','0','C',1);
        $this->Cell(10,10,'C-' ,'1','0','C',1);
        $this->Cell(10,10,'D+' ,'1','0','C',1);
        $this->Cell(10,10,'D'  ,'1','0','C',1);
        $this->Cell(10,10,'F'  ,'1','0','C',1);
        $this->Cell(10,10,'I'  ,'1','0','C',1);
        $this->Cell(10,10,'W'  ,'1','0','C',1);
        $this->Cell(20,10,'No Grade'  ,'1','1','C',1);
    }
    function Footer()
    {
        //Position at 1.5 cm from bottom
        $this->SetY(-10);
        $this->SetDrawColor(210,210,210);
        $this->SetLineWidth(.001);
        $this->Cell(410,1,'','T',1);
        $this->SetFont('Arial','I',8);
        $this->Cell(30, 5,"Confidential ", 0,0, 'L');
        $this->Cell(45,5);
        $this->Cell(30, 5,"Employee", 0,0, 'L');
        $this->Cell(180,5);
        $date= date('D d-m-Y H:i:s');
        $this->Cell(30, 5, $date, 0,0, 'L');
        $this->Cell(65,5);
        $this->Cell(30,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');

    }
}
if($rsAppacdemicprof!='')
{
    $pdf = new PDF('L','mm',array(240,440));   // creating Object of FPDF class to use its function
    $whileCount = 0;
    $oldEnroll  = '';
    $obtain_marks = 0;
    $sc_co_id_count = 0;
    $old_sc_co_id = '';
    $old_co_id = 0;
    $sr_no=1;
    $a_show = 0;
    $am_show = 0;
    $bp_show = 0;
    $b_show = 0;
    $bm_show = 0;
    $cp_show = 0;
    $c_show = 0;
    $cm_show = 0;
    $dp_show = 0;
    $d_show = 0;
    $f_show = 0;
    $i_show = 0;
    $w_show = 0;
    $no_grading_show = 0;
    foreach($rsAppacdemicprof as $rec){
        $pdf->course_name_studentwise  =   $rec->course_name_studentwise;
        $sc_co_id = $rec->sc_co_id;
        if($old_sc_co_id != $sc_co_id){
            if($sc_co_id_count>0){
                $pdf->AddPage();
            }
            $old_sc_co_id= $sc_co_id;
            $sc_co_id_count++;
        }
        $pdf->grader      =  $rec->grader;
        if($whileCount ==0){
            if($res_grading!=''){
                $pdf->res_grading       =  $res_grading;
            }
            if($res1!=''){
                $total_assessments = count($res1);
                $pdf->total_assessments = $total_assessments;
                $pdf->res1 = $res1;
                foreach($res1 as $rec1){
                    $pdf->teacher_name   =  $rec1->teacher_name;
                    $pdf->dept_name      =  $rec1->dept_name;
                    $pdf->school_name    =  $rec1->school_name;
                    $pdf->sess_name      =  $rec1->sess_name;
                    $pdf->full_course_name    =  $rec1->full_course_name;
                }
            }
            if($assess_weightage!=''){

                $total_assessments = count($assess_weightage);

                $pdf->assess_weightage = $assess_weightage;
                $pdf->assess_weightage_count = $total_assessments;
                foreach($assess_weightage as $row){
                    $pdf->assessment_short_name   =  $row->assessment_short_name;
                    $pdf->cc_weightage      =  $row->cc_weightage;
                }
            }
            $pdf->AliasNbPages();
            $pdf->AddPage();
        }
        $height = 10;
        $enorll = $rec->sai_enrollment_no;
        $assessment_name = $rec->assessment_name;
        $pdf->SetFont('Arial','',12);
        if(count($rsAppacdemicprof) == 0){
            echo "<center><font color=red size=2><b>No Assessment Found</b></font></center>";
            exit;
        }else{
            if($oldEnroll != $enorll){
                if($rec->student_grade=="A"){
                    $a_show = $a_show+1;
                }else if ($rec->student_grade=="A-"){
                    $am_show = $am_show+1;
                }else if ($rec->student_grade=="B+"){
                    $bp_show = $bp_show+1;
                }else if ($rec->student_grade=="B"){
                    $b_show = $b_show+1;
                }else if ($rec->student_grade=="B-"){
                    $bm_show = $bm_show+1;
                }else if ($rec->student_grade=="C+"){
                    $cp_show = $cp_show+1;
                }else if ($rec->student_grade=="C"){
                    $c_show = $c_show+1;
                }else if ($rec->student_grade=="C-"){
                    $cm_show = $cm_show+1;
                }else if ($rec->student_grade=="D+"){
                    $dp_show = $dp_show+1;
                }else if ($rec->student_grade=="D"){
                    $d_show = $d_show+1;
                }else if ($rec->student_grade=="F"){
                    $f_show = $f_show+1;
                }else if ($rec->student_grade=="I"){
                    $i_show = $i_show+1;
                }else if ($rec->student_grade=="W"){
                    $w_show = $w_show+1;
                }else {
                    $no_grading_show = $no_grading_show+1;
                }
                $obtain_marks   =   0;
                $print_more="YES";
                $total_assessments = count($assess_weightage);
                $cell_width     =   280/$total_assessments;
                $total_print    =   $total_assessments;
                $pdf->Cell(10,$height,$sr_no                 ,'1','0','C',false);
                $sr_no = $sr_no+1;
                $pdf->Cell(30,$height,$enorll               ,'1','0','C',false);
                $pdf->Cell(70,$height,$rec->spi_name        ,'1','0','L',false);

                if($rec->student_grade=="W" or $rec->student_grade=="I" or $rec->student_grade==""){
                    if($rec->student_grade=="W"){
                        $show_message = "Course Withdraw by Student";
                    }else if($rec->student_grade=="I"){
                        $show_message= " I grade awarded by Teacher";
                    }else if($rec->student_grade==""){
                        if($rec->sc_status=='A'){
                            $course_status='Enrolled';
                        }else if($rec->sc_status=='Withdrawal'){
                            $course_status='Withdrawa';
                        }else{
                            $course_status=$rec->sc_status;
                        }
                        $show_message = "Not Graded yet due to course status or student status at the time of grading";
                    }
                    $pdf->SetFillColor(245,245,245);
                    $pdf->Cell(($cell_width*$total_assessments)+15,$height,$show_message,'1',0,'L',true);
                    $print_more="NO";
                }else{
                    $pdf->Cell($cell_width,$height,$rec->obtained_marks,'1',0,'C',false);
                }
                $obtain_marks=$obtain_marks+$rec->obtained_marks;
                $obtained_marks_total= number_format($obtain_marks,2);

                if($total_print==1){
                    if($rec->student_grade=="W" or $rec->student_grade=="I" or $rec->student_grade==""){
                        // do something
                    }else{
                        $pdf->Cell(15,$height,$obtained_marks_total,'1','0','C',false);
                    }
                    $pdf->Cell(15,$height,$rec->student_grade,'1','1','C',false);
                }
                $oldEnroll = $enorll;
                $total_print--;
            }else{
                if($print_more=="YES"){
                    $pdf->Cell($cell_width,$height,$rec->obtained_marks,'1','0','C',false);
                }
                $obtain_marks=$obtain_marks+$rec->obtained_marks;
                $total_print--;
                if($total_print==0){
                    if($rec->student_grade=="W" or $rec->student_grade=="I" or $rec->student_grade==""){
                        //DO SOMETHING
                    }else{
                        $pdf->Cell(15,$height,$obtain_marks,'1','0','C',false);
                    }
                    $pdf->Cell(15,$height,$rec->student_grade,'1','1','C',false);
                }
             }
        }
        $whileCount ++;
    }
    if($pdf->GetY()>180){
        $pdf->AddPage();
    }
    $pdf->Ln(10);
    $pdf->SetX(160);
    $pdf->SetFillColor(210,210,210);
    $pdf->Cell(150,10,'Grade Summary','1','1','C',true);
    $pdf->ledger();
    $pdf->SetX(160);
    $pdf->Cell(10,10,$a_show,'1','0','C',false);
    $pdf->Cell(10,10,$am_show,'1','0','C',false);
    $pdf->Cell(10,10,$bp_show,'1','0','C',false);
    $pdf->Cell(10,10,$b_show,'1','0','C',false);
    $pdf->Cell(10,10,$bm_show,'1','0','C',false);
    $pdf->Cell(10,10,$cp_show,'1','0','C',false);
    $pdf->Cell(10,10,$c_show,'1','0','C',false);
    $pdf->Cell(10,10,$cm_show,'1','0','C',false);
    $pdf->Cell(10,10,$dp_show,'1','0','C',false);
    $pdf->Cell(10,10,$d_show,'1','0','C',false);
    $pdf->Cell(10,10,$f_show,'1','0','C',false);
    $pdf->Cell(10,10,$i_show,'1','0','C',false);
    $pdf->Cell(10,10,$w_show,'1','0','C',false);
    $pdf->Cell(20,10,$no_grading_show,'1','1','C',false);
    $pdf->Output('','I');
    $pdf->Close(); // Close PDF Object
    exit();
}else{
    echo "<center><font color=red size=2><b>No Record Found </b></font></center>";
    exit();
}
?>