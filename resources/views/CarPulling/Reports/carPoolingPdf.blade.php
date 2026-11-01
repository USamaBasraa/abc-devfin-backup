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
   
   
       $this->Image(public_path().'/fpdf/logoimages/bnulogo1.jpg',13,5.5,14,24);
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
        $this->SetXY(28, 40);
        $this->SetFillColor(210,210,210);       
        $this->SetFont('helvetica','B',16);      
        $this->Cell(0,6,'Car Pooling Report',0,1,'C');
  

}
function Footer()
    {
        //Position at 1.5 cm from bottom
        $this->SetY(-10);
        $this->SetDrawColor(210,210,210);
        $this->SetLineWidth(.001);
        $this->Cell(190,1,'','T',1);
        $this->SetFont('Arial','I',8);
        date_default_timezone_set("Asia/Karachi");
        $date= date('d-m-Y H:i:s');
        $this->Cell(30, 5,'Generated :'.$date, 0,0, 'L');
        $this->Cell(65,5);
        $this->Cell(95,5,'Page '.$this->PageNo(),0,0,'R');

    }


function showHeadings()
    {
    
        $this->SetFillColor(210, 210, 210);
        $this->SetFont('Arial', 'B', 11);	
        $this->SetXY(10, 50);
        $this->Cell(20, 10, 'Sr#', '1', '0', 'C', 1);
        $this->Cell(90, 10, 'Ride Status', '1', '0', 'C', 1);
        $this->Cell(80, 10, 'Total', '1', '1', 'C', 1);
        
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


    //$pdf = new PDF('L','mm',array(240,400));
    $pdf = new PDF('P','mm','A4');
    //$pdf->SetLeftMargin(1);
    //$pdf->SetrightMargin(1);
    $pdf->SetLineWidth(0.4);
    $pdf->SetAutoPageBreak(false);
    $a=1;
    $sr=1;
    $srTick=1;
    $pdf->SetTextColor(10,10,10);
    $pdf->SetX(40);
    $pdf->SetFont('times','B',10);
    $pdf->AddPage();
    $pdf->showHeadings();
    $abc='';
    foreach($statisticData as $rec) {
        $oldY = $pdf->getY();
        if($oldY>200){
            $pdf->AddPage(); 
        }       
        $abc=$rec->cp_want_ride;
        $pdf->Cell(20,8,$sr,'1','0','C');
        $pdf->Cell(90,8,$rec->cp_want_ride,'1','0','L',0);
        $pdf->Cell(80,8,$rec->total,'1','1','C',0);
        $sr++;      
    }   
    // $pdf->Cell(90,8,'Bial SLaeem','1','1','L',0);
    $pdf->SetFillColor(210,210,210);       
    $pdf->SetFont('helvetica','B',16);      
    $pdf->SetXY(28, 100);
    $pdf->Cell(0,6,'Vehicle Ticket Status',0,1,'C');
    $pdf->SetY(110);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(20, 10, 'Sr#', '1', '0', 'C', 1);
    $pdf->Cell(90, 10, 'Parking Status', '1', '0', 'C', 1);
    $pdf->Cell(80, 10, 'Total', '1', '1', 'C', 1);
    
    foreach($statisticParking as $rec) {
        // $oldY = $pdf->getY();
        // if($oldY>200){
        //     $pdf->AddPage(); 
        // }       
        $pdf->Cell(20,8,$srTick,'1','0','C');
        $pdf->Cell(90,8,$rec->vr_parking." Token",'1','0','L',0);
        $pdf->Cell(80,8,$rec->total_parking,'1','1','C',0);
        $srTick++;      
    }
    $pdf->Ln(10);
    $pdf->SetTextColor(10,10,10);
    $pdf->SetFont('times','B',12);
    $pdf->Cell(190,10,"Disclaimer!.",'1','1','C',1);
    $pdf->Ln(5);
    $pdf->SetFont('times','B',10);
    $pdf->Cell(34,10,"Ride Requested        : ",'0','0','L',0);
    $pdf->SetFont('times','',10);
    $pdf->Cell(20,10,"Request(s) of students who want convience.",'0','1','L',0);
    $pdf->SetFont('times','B',10);
    $pdf->Cell(34,10,"Ride Offered             : ",'0','0','L',0);
    $pdf->SetFont('times','',10);
    $pdf->Cell(20,10,"Request(s) of students who want to share his/her vehicle.",'0','1','L',0);
    $pdf->Ln(5);
    $pdf->SetFont('times','B',10);
    $pdf->Cell(34,5,"Pending Requests     : ",'0','0','L',0);
    $pdf->SetFont('times','',10);
    $pdf->Cell(20,5,"Requests are sent by either the vehicle owner or the ride requester. These requests can ",'0','1','L',0);
    $pdf->Cell(34,5,"",'0','0','L',0);
    $pdf->Cell(120,5,"vary in the total number of rides requested or offered.",'0','1','L',0);
    $pdf->Ln(5);
    $pdf->SetFont('times','B',10);

    $pdf->Cell(34,10,"Accepted Requests   : ",'0','0','L',0);
    $pdf->SetFont('times','',10);
    $pdf->Cell(20,5,"Requests are accepted by either the vehicle owner or the ride requester. The number of rides ",'0','1','L',0);
    $pdf->Cell(34,5,"",'0','0','L',0);
    $pdf->Cell(120,5,"accepted can vary from the total number of ride requests or offerings because a ride owner",'0','1','L',0);
    $pdf->Cell(34,5,"",'0','0','L',0);
    $pdf->Cell(120,5,"may share more than one seat in their vehicle.",'0','1','L',0);

    $pdf->Output('','I');
    $pdf->Close(); // Close PDF Object
    exit();

?>









