<?php


// echo public_path().'/fpdf/fpdf.php'; exit;

include(public_path().'/fpdf/fpdf.php');

class PDF extends FPDF {


    public $results;
    public $sess_name;
    public $school_name;
    public $teacher_name;
    public $course_name;
    public $course_code;
    public $co_course_cr;

    //

//Page header
function Header()
{    
    // Variables  Declarations , these will be assigned values in main cursor 
# these varibles will be used in header functions 

    //FOR HEADING DATA From databse     
                
    // Letter Head
    
    $this->SetTextColor(10,10,10);
    $this->SetXY(28, 16);
    $this->SetFont('times','B',20);
 //   $this->Image(public_path().'/fpdf/logoimages/bnulogo1.jpg',13,5.5,14,24);
   // $this->SetFont('times','B',18);
    $this->Cell(0,0,'Beaconhouse National University',0,0,'C');
    $this->SetFont('times','B',16);
    $this->SetXY(28, 23);
    $this->Cell(0,0,'Faculty Course Detail Report',0,0,'C');
    $this->SetXY(28, 29);
    $this->SetFont('times','U',16);
    $this->Cell(0,0,$this->school_name,0,0,'C');
    $this->SetXY(28, 37);
    $this->SetFont('times','B',18);   
    $this->Cell(0,0,'Session:'.$this->sess_name,0,0,'C');  
    $this->SetXY(10, 50);
    $this->SetFont('times','B',12);   
    $this->Cell(0,0,'Teacher Name:   '.$this->teacher_name,0,0,'L');  
    $this->SetXY(10, 55);
    $this->SetFont('times','B',12);   
    $this->Cell(0,0,'Course Name:     '.$this->course_name,0,0,'L');    
    $this->SetXY(10, 60);
    $this->SetFont('times','B',12);   
    $this->Cell(0,0,'Credit Hour:       '.$this->co_course_cr,0,0,'L');  
    
    $this->SetXY(90, 70);
    $this->SetFont('Arial','B',15);  
    if($this->PageNo() == 1){ 
    $this->Cell(0,0,'Course Criteria',0,0,'L');  
    }
    $this->SetY(80);
    $this->ln();
    $this->SetFillColor(210,210,210); 
    $this->SetFont('Arial','B',8);   
    $this->criteria_heading();  

}

function criteria_heading(){
    if($this->PageNo() == 1){
        $this->Cell(8 ,8,'Sr.'				,'1','0','C',1);
        $this->Cell(60,8,'Assessment Name','1','0','C',1);
        $this->Cell(60,8,'Assessment Short Name','1','0','C',1);
        $this->Cell(60,8,'Weightage','1','1','C',1);
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
//Page footer   
    
} // class end 

if(count($teacher_evaluation) > 0)
{
//$pdf = new PDF('L','mm',array(240,400));
$pdf = new PDF('P','mm','A4');
//$pdf->SetLeftMargin(1);
//$pdf->SetrightMargin(1);
$pdf->SetLineWidth(0.4);
$pdf->SetAutoPageBreak(false);


$pdf->results = $teacher_evaluation;


foreach($teacher_evaluation as $public_teacher_evaluation) {
    
    $pdf->sess_name        = $public_teacher_evaluation->sess_name;
    $pdf->school_name      = $public_teacher_evaluation->school_name;
    $pdf->teacher_name     = $public_teacher_evaluation->teacher_name;
    $pdf->course_name      = $public_teacher_evaluation->course_cd_name;
    // $pdf->course_code      = $public_teacher_evaluation->course_code;
    $pdf->co_course_cr     = $public_teacher_evaluation->co_course_cr;
}


$a=1;
$sr=1;
$cc_sr=1;

    $pdf->SetTextColor(10,10,10);
    $pdf->SetX(40);
    $pdf->SetFont('times','B',10);
    $pdf->AddPage();
    //$pdf->ssess_code      = 'pak';  
    //$pdf->course_name = "welcome";  

  
    foreach($teacher_evaluation as $rec_teacher_evaluation) {
        //$pdf->feeObj = $row;
        $oldY = $pdf->getY();
        if($oldY>200){
            $pdf->AddPage(); 
        }
       
        $pdf->Cell(8,8,$sr,'1','0','C');
        $pdf->Cell(60,8,@$rec_teacher_evaluation->assessment_name,'1','0','L',0);
        $pdf->Cell(60,8,@$rec_teacher_evaluation->assessment_short_name,'1','0','L',0);
        $pdf->Cell(60,8,@$rec_teacher_evaluation->cc_weightage,'1','1','C',0);
     
    
        
        $sr++;

      
     }
     $pdf->SetXY(90, 140);
     $pdf->SetFont('Arial','B',15);   
     $pdf->Cell(0,0,'Course Contents',0,0,'L');  
     $pdf->SetFillColor(210,210,210); 
     $pdf->SetFont('Arial','B',8);
     $pdf->SetY(150);
     $pdf->Cell(8 ,8,'Sr.'				,'1','0','C',1);
     $pdf->Cell(180,8,'Course Content','1','1','L',1);
   
     foreach($contents as $rec_teacher_evaluation) {
        //$pdf->feeObj = $row;
        $oldY = $pdf->getY();
       // echo $pdf->getY();exit;
        if($oldY>280){
            $pdf->AddPage(); 
        }
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(8,5,$cc_sr,'1','0','C');
        $pdf->SetFillColor(255, 255, 255);
        $pdf->MultiCell(180,5,@$rec_teacher_evaluation->coc_details, '1', 1, 'L', 0);
        // $pdf->MultiCell(180,5,@$rec_teacher_evaluation->coc_details,'1','1','L',1);            
        $cc_sr++;      
     }

$pdf->output();
exit;
}else{
    echo "<center><font color=red size=2><b>No Record Found </b></font></center>";
    exit();

}

?>