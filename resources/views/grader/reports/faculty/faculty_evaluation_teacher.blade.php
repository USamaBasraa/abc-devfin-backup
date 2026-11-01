<?php


// echo public_path().'/fpdf/fpdf.php'; exit;

include('fpdf/fpdf.php');

class PDF extends FPDF {

    public $total_result;
    public $results;
    public $sess_name;
    public $school_name;
    public $all_departments;
    public $all_programs;
    public $program_total_chars;
    public $departments_total_chars;
    //

//Page header
function Header()
{

    // Letter Head
    
        // Add your stuff here
   
    $this->SetTextColor(10,10,10);
    $this->SetXY(28, 10);
    $this->SetFont('times','B',24);
    $this->Image('fpdf/logoimages/bnulogo1.jpg',10,5.5,14,24);
    $this->SetFont('times','B',24);
    $this->Cell(0,15,'Beaconhouse National University',0,0,'L');
    $this->ln();
    // $this->SetXY(28, 17);
    // $this->Cell(0,0,$this->school_name,0,0,'L');
    

    //$this->Cell(0,0,'Department'.$this->school_name,0,0,'L');
    $this->ln();
    if ( $this->PageNo() == 1 ) {

    $this->SetFont('times','B',14);
    $this->SetXY(280, 20);
    $this->Cell(0,0,'Session:'.$this->sess_name,0,0,'L');
    $this->SetXY(14, 35);

   

// $this->Cell(345,10 ,$show_all_programs,'1','1','C',0);'__'.strlen($this->all_departments).'_'.strlen($this->all_programs).'__'.

   // $this->Cell(0,0,'Programme          BA (Hons) Media Studies, MA Mass Communication, MS PR & Advertising',0,0,'L');

    //$this->SetXY(14, 80);
    //$this->Cell(0,20,'Total No of Faculty Members: '.count($this->results),0,0,'L');





   // $this->Multicell(225,20,$all_programs ,'1','0','L' );

    $this->ln();
    $this->SetFillColor(210,210,210);
    $this->SetFont('Arial','B',8);
    $height=16;
    $this->Cell(8 ,$height,'Sr.'				,'1','0','C',1);
    $this->Cell(77,$height,'Name of Faculty '  				,'1','0','C',1);
    $this->Cell(30,$height,'Course Code'  				,'1','0','C',1);
    $this->Cell(90,$height,'Course Title'  				,'1','0','C',1);
    $this->Cell(20,$height,'Course Cr.Hrs'  				,'1','0','C',1);
    $this->Cell(20,$height,'Total No Of Students'		,'1','0','C',1);
    $this->Cell(20,$height,'No of Evaluators' ,'1','0','C',1);
    $this->Cell(20,$height,'Quality of Teaching','1','0','C',1);
    $this->Cell(20,$height,'Course     Evaluation'	,'1','0','C',1);
    $this->Cell(20,$height,'Exam &     Assessment' ,'1','0','C',1);
    $this->Cell(20,$height,'Overall Rating%','1','0','C',1);
    $this->Cell(20,$height,'Attendance' ,'1','1','C',1);
   // $this->MultiCell(20,$height,'Re Chance','1','1','C',1);
    // $this->MultiCell(20,$height,'Student Attendance','1','1','C',1);
}
}



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
        if($this->GetY() > 100)
            $this->addPage();
        else
            $this->mainHeadings();
    }
}


} // class end


$pdf = new PDF('L','mm',array(240,400));
//$pdf = new PDF('L','mm','LEGAL');
//$pdf->SetLeftMargin(1);
//$pdf->SetrightMargin(1);
$pdf->SetLineWidth(0.4);
$pdf->SetAutoPageBreak(false);

//$pdf->results = $teacher_evaluation;

foreach($teacher_evaluation as $public_teacher_evaluation) {

     $pdf->sess_name      = $public_teacher_evaluation->sess_name;
    $pdf->school_name      = $public_teacher_evaluation->school_name;

   

}

   

$a=1;
$sr=0;

    $pdf->SetTextColor(10,10,10);
    $pdf->SetX(40);
    $pdf->SetFont('times','B',10);
    $pdf->AddPage();
    $pdf->ssess_code      = 'pak';
    $pdf->course_name = "welcome";

    $grand_total_students        = 0 ;
    $grand_quality_of_teaching   = 0 ;
    $grand_course_evaluation     = 0 ;
    $grand_exam_and_assessment   = 0 ;
    $grand_overall_rating        = 0 ;
    $grand_feedback_submitted    = 0 ;
    $grand_co_attendance_percentage=0;
    $avg_per=0;


  //  $pdf->Cell(225,10,$all_programs,1,1,'C');

    foreach($teacher_evaluation as $rec_teacher_evaluation) {
        //$pdf->feeObj = $row;
        $oldY = $pdf->getY();
        if($oldY>200){
            $pdf->AddPage();
        }
        if($rec_teacher_evaluation->total_students !=0){

        
        $pdf->Cell(8,10,$sr,'1','0','C');
        $pdf->Cell(77,10, \Illuminate\Support\Str::limit($rec_teacher_evaluation->teacher_name, 38, $end='...'),1,0,'L');
        $pdf->Cell(30,10,@$rec_teacher_evaluation->course_code,1,0,'C');
        $pdf->Cell(90,10, \Illuminate\Support\Str::limit($rec_teacher_evaluation->course_cd_name, 50, $end='...') ,1,0,'L');
        $pdf->Cell(20,10,@$rec_teacher_evaluation->co_course_cr,1,0,'C');
        $pdf->Cell(20,10,@$rec_teacher_evaluation->total_students,1,0,'C');
        $pdf->Cell(20,10,@$rec_teacher_evaluation->feedback_submitted,1,0,'C');
        $pdf->Cell(20,10,@$rec_teacher_evaluation->quality_of_teaching,1,0,'C');
        $pdf->Cell(20,10,@$rec_teacher_evaluation->course_evaluation,1,0,'C');
        $pdf->Cell(20,10,@$rec_teacher_evaluation->exam_and_assessment,1,0,'C');
        $pdf->Cell(20,10,@$rec_teacher_evaluation->overall_rating,1,0,'C');
        $pdf->Cell(20,10,@$rec_teacher_evaluation->co_attendance_percentage,1,1,'C');

        $grand_total_students        = $grand_total_students + $rec_teacher_evaluation->total_students;
        $grand_feedback_submitted    = $grand_feedback_submitted + $rec_teacher_evaluation->feedback_submitted;
        $grand_quality_of_teaching   = $grand_quality_of_teaching + $rec_teacher_evaluation->quality_of_teaching;
        $grand_course_evaluation     = $grand_course_evaluation + $rec_teacher_evaluation->course_evaluation;
        $grand_exam_and_assessment   = $grand_exam_and_assessment + $rec_teacher_evaluation->exam_and_assessment ;
        $grand_overall_rating        = $grand_overall_rating + $rec_teacher_evaluation->overall_rating;
        $grand_co_attendance_percentage        = $grand_co_attendance_percentage + $rec_teacher_evaluation->co_attendance_percentage;



        $sr++;
    }


     }
        $sr=($sr ? $sr : '1');
        $pdf->results=$sr;
        $avg_per= round($grand_co_attendance_percentage/$sr,2);
        $grand_quality_of_teaching= round($grand_quality_of_teaching/$sr,2);
        $grand_course_evaluation = round($grand_course_evaluation/$sr,2);
        $grand_overall_rating=round($grand_overall_rating/$sr,2);
        $grand_exam_and_assessment=round($grand_exam_and_assessment/$sr,2);
        $pdf->Cell(225,10,'Overall Rating',1,0,'C');
        $pdf->Cell(20,10,@$grand_total_students,1,0,'C');
        $pdf->Cell(20,10,@$grand_feedback_submitted,1,0,'C');
        $pdf->Cell(20,10,@$grand_quality_of_teaching,1,0,'C');
        $pdf->Cell(20,10,@$grand_course_evaluation,1,0,'C');
        $pdf->Cell(20,10,@$grand_exam_and_assessment,1,0,'C');
        $pdf->Cell(20,10,@$grand_overall_rating,1,0,'C');
        $pdf->Cell(20,10,@$avg_per,1,1,'C');



echo $pdf->Output('S');
?>
