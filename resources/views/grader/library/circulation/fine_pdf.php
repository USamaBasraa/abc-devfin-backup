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

    public $school_name = 'ITRC';
    public $sess_name   = 'Garmi 21';

    public $x = 3;
    public $y = 3;

    function challanHeader($num=0)
    {


        $dateX = 0;
        $dateY = 0;
        $dateX = 160;
        if($num == 1){
            $this->x = 10;
            $this->y = 3;
            
            $dateY = 10;
        }elseif($num == 2){
            $this->x = 10;
            $this->y = 90;
           
            $dateY = 90;
        }elseif($num == 3){
            $this->x = 10;
            $this->y = 180;
           
            $dateY = 180;
        }



        $this->Image('fpdf/logoimages/bnulogo1.jpg',$this->x,$this->y+3,10,13);

        
        


        // Letter Head
        $this->SetXY($this->x+10, $this->y);
        $this->SetFont('times', 'B', 12);
        $this->SetTextColor(0, 0, 0);

        $this->Cell(100, 7, 'Beaconhouse National University', 0, 1, 'L');

        $this->SetXY($this->x+10, $this->y);
        $this->SetFont('times', '', 10);
        $this->Cell(100, 5, '13-km Raiwind Road, Lahore 54400, Pakistan', 0, 1, 'L');
        
        $this->SetXY($this->x+10, $this->y);
        $this->Cell(100, 5, 'Tel: 92-42-38100156 Fax:(92) 42-5760254 Email: info@bnu.edu.pk', 0, 1, 'L');


        $this->SetXY($dateX, $dateY);
        $this->Cell(50, 5, 'S. No : '.$this->data[0]->id, 1, 1, 'L');

        $this->SetXY($dateX, $dateY+7);
        $this->Cell(50, 5, 'Date : '.date('d M Y') , 1, 0, 'L');
    }


    function challanBody($num){


        $dateX = 0;
        $dateY = 0;
        $copyType = '';
        if($num == 1){
            $this->x = 10;
            $this->y = 24;
            $dateX = 125;
            $dateY = 3;
            $copyType = 'Bank Copy';
        }elseif($num == 2){
            $this->x = 10;
            $this->y = 110;
            $dateX = 125;
            $dateY = 100;
            $copyType = 'Library Copy';
        }elseif($num == 3){
            $this->x = 10;
            $this->y = 200;
            $dateX = 125;
            $dateY = 210;
            $copyType = 'Student Copy';
        }



        $this->SetXY($this->x, $this->y);
        $this->Cell(50, 5, 'Received with thanks from : ', 0, 0, 'L');
        $this->Cell(150, 5, $this->data[0]->sai_enrollment_no, 'B', 1, 'L');

        $this->SetXY(10, $this->y+3);
        $this->Cell(20, 5, 'ID No : ', 0, 0, 'L');
        $this->Cell(180, 5, $this->data[0]->library_title, 'B', 0, 'L');


        
        $this->SetXY(10, $this->y+8);
        $this->Cell(50, 5, 'the sum of Rupees(in words)', 0, 0, 'L');
        $this->Cell(150, 5, 'Rs:'.$this->data[0]->lc_fine_total.'/-', 'B', 0, 'L');

        
        $this->SetXY(10, $this->y+8);
        $this->Cell(30, 5, 'on account of', 0, 0, 'L');
        $this->Cell(170, 5, '', 'B', 0, 'L');


        
        $this->SetXY(10, $this->y+8);
        $this->Cell(30, 5, 'Rs. (in figures) ', 0, 0, 'L');
        $this->Cell(170, 5, 'Rs:'.$this->data[0]->lc_fine_total.'/-', 'B', 0, 'L');


        $this->SetXY(10, $this->y+15);
        $this->Cell(100, 5, 'Acceptable only at UBL Cash Collection Booth at BNU', 0, 0, 'L');
        $this->Cell(20, 5, $copyType, 0, 0, 'C');
        $this->SetXY(140, $this->y);
        $this->Cell(55, 5, 'For Beaconhouse National University', 'T', 0, 'L');


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
  
  
    
    // function Footer()
    // {
    //     //Position at 1.5 cm from bottom
    //     $this->SetY(-10);
    //     $this->SetDrawColor(210,210,210);
    //     $this->SetLineWidth(.001);
    //     $this->Cell(410,1,'','T',1);
    //     $this->SetFont('Arial','I',8);
    //     $this->Cell(30, 5,"Confidential ", 0,0, 'L');
    //     $this->Cell(45,5);
    //     $this->Cell(30, 5,"Employee", 0,0, 'L');
    //     $this->Cell(180,5);
    //     $date= date('D d-m-Y H:i:s');
    //     $this->Cell(30, 5, $date, 0,0, 'L');
    //     $this->Cell(65,5);
    //     $this->Cell(30,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');

    // }
}

// $pdf = new PDF('L','mm',array(240,440));   // creating Object of FPDF class to use its function

$pdf = new PDF('P','mm',array(216,279)); 

$pdf->AddPage();

$pdf->data = $data;

$pdf->challanHeader(1);
$pdf->challanBody(1);
// $pdf->y = 50;
$pdf->challanHeader(2);
$pdf->challanBody(2);
// $pdf->y = 100;
$pdf->challanHeader(3);
$pdf->challanBody(3);



$pdf->Output('','I');
    $pdf->Close(); // Close PDF Object
    
    exit();

?>