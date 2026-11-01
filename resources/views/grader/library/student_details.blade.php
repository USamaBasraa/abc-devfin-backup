<?php

include('fpdf/fpdf.php');
//print_r($data);exit;
class PDF extends FPDF   // Inheriting the parent Class
{

    public $userProfile;

    function Header()
    {
        $this->Image('fpdf/logoimages/bnulogo1.jpg', 10, 5, 20, 25);
        // Letter Head
        $this->SetXY(35, 10);
        $this->SetFont('times', 'B', 16);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 0, 'Beaconhouse National University', 0, 0, 'L');
        $this->SetXY(35, 17);
        $this->SetFont('times', '', 11);
        $this->Cell(0, 0, '13-km Raiwind Road, Lahore 54400, Pakistan', 0, 0, 'L');
        $this->SetXY(35, 22);
        $this->Cell(0, 5, 'Tel: 92-42-38100156 Fax:(92) 42-5760254 Email: info@bnu.edu.pk', 0, 0, 'L');
        $this->Cell(0, 5, 'Serial No:', 0, 1, 'R');
        $this->Line(1, 33, 400, 33, 1);
    }   


    function userPersonalInfo(){

        $this->SetFont('helvetica', 'B', 15);
        $this->Cell(90, 30, 'Name : '.$this->userProfile->user_full_name, 0, 1, 'L');
        $this->Cell(90, 0, ''.$this->userProfile->school, 0, 0, 'L'); 
        // echo file_exists($this->userProfile->image);      
       // $this->Image($this->userProfile->image, 210, 40, 20, 25);
        $this->SetFont('helvetica', 'B', 11);
        $this->SetXY(10, 65);
        $this->Cell(100, 15, ''.$this->userProfile->dept, 0, 0, 'L');
        $this->Cell(120, 15, ''.$this->userProfile->prog, 0, 1, 'R'); 
		$this->SetFont('helvetica','',11);
		$this->SetFillColor(255,255,255);
		$this->SetLineWidth(0.5);
        $this->SetXY(10, 75);
		$this->Cell(220,25,'',1,false,'C',1,0);	
        $this->SetXY(10, 78);
        if($this->userProfile->group_id == 4){
            $this->Cell(55, 5, 'Student id : '.$this->userProfile->id,'0',false,'L');
        }else{
            $this->Cell(55, 5, 'Id : '.$this->userProfile->id,'0',false,'L'); 
        }
        $this->Cell(160, 7, ' Registration No: '.$this->userProfile->username, 0, 1, 'R');
        if($this->userProfile->group_id == 4){
            $this->Cell(90, 7, 'Student Name : '.$this->userProfile->user_full_name, 0, 0, 'L');
        }else{
            $this->Cell(90, 7, 'Name : '.$this->userProfile->user_full_name, 0, 0, 'L');   
        }
        if($this->userProfile->group_id == 4){
            $this->Cell(115, 7, 'Required Cr.Hrs :'.$this->userProfile->sai_prog_total_credits, 0, 1, 'R');
        }else{
            $this->Cell(125, 7, 'Email :'.$this->userProfile->email, 0, 1, 'R');
        }
       
        if($this->userProfile->group_id == 4){
            $this->Cell(90, 7, 'Parent Name : '.$this->userProfile->user_father_name, 0, 0, 'L');
        }else{
            $this->Cell(90, 7, 'Mobile : '.$this->userProfile->mobile_staff, 0, 0, 'L');
        }
       
    }
}

   //  print_r($data); exit;


    $pdf = new PDF('P','mm',array(240,279));   // creating Object of FPDF class to use its function
    $pdf->AddPage();    
    $pdf->SetFont('times', 'B', 16);
    $lecture_no = 1;
    $pdf->userProfile = $data;
    $pdf->userPersonalInfo();

    $pdf->Output('','I');
    $pdf->Close(); // Close PDF Object
    exit();
   
?>