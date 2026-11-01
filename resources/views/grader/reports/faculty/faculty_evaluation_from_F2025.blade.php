<?php


// echo public_path().'/fpdf/fpdf.php'; exit;

include('fpdf/fpdf.php');

class PDF extends FPDF {


    public $results;
    public $sess_name;
    public $school_name;

    //

//Page header
function Header()
{


    $teacherNames = [];
        foreach ($this->results as $rec_teacher_evaluation) {
            if (!empty($rec_teacher_evaluation->new_feedback_submitted) && $rec_teacher_evaluation->new_feedback_submitted > 0) {
                if (!in_array($rec_teacher_evaluation->teacher_name, $teacherNames)) {
                    $teacherNames[] = $rec_teacher_evaluation->teacher_name;
                }
            }
        }
        $teacherNamesString = implode(', ', $teacherNames);



    // Variables  Declarations , these will be assigned values in main cursor
# these varibles will be used in header functions



    //FOR HEADING DATA From databse


    // Letter Head

    $this->SetTextColor(10,10,10);
    $this->SetXY(28, 10);
    $this->SetFont('times','B',24);
    $this->Image(public_path().'/fpdf/logoimages/bnulogo1.jpg',10,5.5,17,22);
    //$this->Image(public_path().'/fpdf/logoimages/bnulogo1.jpg',13,5.5,14,24);
    $this->SetFont('times','B',14);
    $this->Cell(0,0,'Beaconhouse National University',0,0,'L');
    $this->SetXY(28, 17);
    $this->Cell(0,0,$this->school_name,0,0,'L');
    $this->SetXY(14, 35);
    $this->Cell(0,0,'Session:'.$this->sess_name,0,0,'L');
    // $this->SetXY(14, 35);
    //$this->Cell(0,0,'Department'.$this->school_name,0,0,'L');

    $this->SetXY(14, 45);
    $this->Cell(0,0,'Department:' .$this->dept_name,0,0,'L');

    $this->SetXY(14, 48);
    $this->Cell(0,15,'Total No of Records: '.count($this->results),0,0,'L');


  
        if (!empty($teacherNamesString)) {
            $this->SetXY(14, 65); // Set initial Y position for the first line
            $this->Cell(0, 0, 'Faculty selected for both modules:', 0, 1, 'L'); // '1' moves to the next line

        // Set font to bold for both lines
            $this->SetFont('Arial', '', 12);

        // Combine the faculty names and feedback line into one line
            $this->SetXY(14, 70);
            // $this->MultiCell(0, 5, 'Dr. Shafaat Ahmed Bazaz, Ms. Shazia Rizwan, Ms. Huda Sarfraz, and Dr. Usman Nazir. 50% completed the evaluation as per the old feedback survey.', 0, 'L');

            $this->MultiCell(
                0,
                5,
                $teacherNamesString . '. Completed the evaluation as per the F2025 feedback system.',
                0,
                'L'
            );
}



        // Reset the font to normal if needed
        $this->SetFont('Arial','B',14);




    $this->ln();
    $this->SetFillColor(210,210,210);
    $this->SetFont('Arial','B',8);
    $height=16;
    $this->Cell(10 ,$height,'Sr.'				,'1','0','C',1);
    $this->Cell(50,$height,'Name of Faculty'  				,'1','0','C',1);
    $this->Cell(30,$height,'Course Code'  				,'1','0','C',1);
    $this->Cell(90,$height,'Course Title'  				,'1','0','C',1);
    $this->Cell(25,$height,'Course Cr.Hrs'  				,'1','0','C',1);
    $this->Cell(30,$height,'Total No of Students'		,'1','0','C',1);
    $this->Cell(30,$height,'No of Evaluators' ,'1','0','C',1);
    $this->Cell(40,$height,'Academic Core Functions','1','0','C',1);
    $this->Cell(45,$height,'Academic Support Functions'	,'1','0','C',1);
    $this->Cell(30,$height,'Overall Rating' ,'1','0','C',1);
    $this->Cell(30,$height,'Students Attendance','1','1','C',1);

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


//Page footer










} // class end


$pdf = new PDF('L','mm',array(300,440));
//$pdf = new PDF('L','mm','LEGAL');
//$pdf->SetLeftMargin(1);
//$pdf->SetrightMargin(1);
$pdf->SetLineWidth(0.4);
$pdf->SetAutoPageBreak(false);

$pdf->results = $teacher_evaluation;

// Get session and school name for filename
$sessionName = '';
$schoolName = '';
$deptName = '';

foreach($teacher_evaluation as $public_teacher_evaluation) {

    $pdf->sess_name      = $public_teacher_evaluation->sess_name;
    $pdf->school_name      = $public_teacher_evaluation->school_name;
    $pdf->dept_name      = $public_teacher_evaluation->dept_name;
    
    // Store for filename
    if (empty($sessionName)) {
        $sessionName = $public_teacher_evaluation->sess_name ?? '';
        $schoolName = $public_teacher_evaluation->school_name ?? '';
        $deptName = $public_teacher_evaluation->dept_name ?? '';
    }

}


$a=1;
$sr=1;

    $pdf->SetTextColor(10,10,10);
    $pdf->SetX(40);
    $pdf->SetFont('times','B',10);
    $pdf->AddPage();
    $pdf->ssess_code      = 'pak';
    $pdf->course_name = "welcome";
    foreach($teacher_evaluation as $rec_teacher_evaluation) {
        //$pdf->feeObj = $row;
        $oldY = $pdf->getY();
        if($oldY>200){
            $pdf->AddPage();
        }

        $pdf->Cell(10,10,$sr,'1','0','C');
        $pdf->Cell(50,10, \Illuminate\Support\Str::limit($rec_teacher_evaluation->teacher_name, 28, $end='...'),1,0,'L');
        $pdf->Cell(30,10,@$rec_teacher_evaluation->course_code ?? 'N/A',1,0,'C');
        $pdf->Cell(90,10, \Illuminate\Support\Str::limit($rec_teacher_evaluation->course_name, 50, $end='...') ,1,0,'L');
        $pdf->Cell(25,10,@$rec_teacher_evaluation->co_course_cr,1,0,'C');
        $pdf->Cell(30,10,@$rec_teacher_evaluation->total_students,1,0,'C');
        
        // Display No of Evaluators
        $new_feedback_submitted = @$rec_teacher_evaluation->new_feedback_submitted ?? 0;
        
        // $evaluators_count = $new_feedback_submitted > 0 ? $new_feedback_submitted : (@$rec_teacher_evaluation->feedback_submitted ?? 0);
        $evaluators_count = @$rec_teacher_evaluation->feedback_submitted ?? 0;
        
        $pdf->Cell(30,10,$evaluators_count,1,0,'C');
        
        // Display Academic Core Functions
        $pdf->Cell(40,10,@$rec_teacher_evaluation->core_summary ?? 'N/A',1,0,'C');
        
        // Display Academic Support Functions
        $pdf->Cell(45,10,@$rec_teacher_evaluation->support_summary ?? 'N/A',1,0,'C');
        
        // Display Overall Rating
        $pdf->Cell(30,10,@$rec_teacher_evaluation->overall_summary ?? 'N/A',1,0,'C');
        
        // Display Students Attendance
        $pdf->Cell(30,10,@$rec_teacher_evaluation->co_attendance_percentage ?? 'N/A',1,1,'C');


        $sr++;


     }

// Generate simple and professional filename
$dateStr = date('Y-m-d');
$timeStr = date('His'); // Add time to avoid duplicates

// Simple format: Faculty_Evaluation_Report_YYYY-MM-DD_HHMMSS.pdf
$filename = 'Faculty_Evaluation_Report_' . $dateStr . '_' . $timeStr . '.pdf';

$pdf->output('I', $filename);
exit;

?>
