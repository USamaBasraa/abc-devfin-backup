<?php


include('fpdf/fpdf.php');

class PDF extends FPDF   // Inheriting the parent Class
{

    public $course_name;
    public $teacher_name;


    function Header()
    {
        $this->Image('fpdf/logoimages/bnulogo1.jpg',10,5,15,20);
        // Letter Head
        $this->SetXY(30, 10);
        $this->SetFont('times', 'B', 12);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 0, 'Beaconhouse National University', 0, 0, 'L');
        $this->SetXY(30, 17);
        $this->SetFont('times', '', 10);
        $this->Cell(0, 0, '13-km Raiwind Road, Lahore 54400, Pakistan', 0, 0, 'L');
        $this->SetXY(30, 22);
        $this->Cell(0, 5, 'Tel: 92-42-38100156 Fax:(92) 42-5760254 Email: info@bnu.edu.pk', 0, 1, 'L');
        $this->Cell(18,2);
        $this->ln(4);
        $this->Cell(0, 0, '', 1, 1, 'C');
        // $this->ln(5);
       
        $this->SetFont('helvetica', 'B', 12);
        $this->SetXY(80,30);
        $this->Cell(50, 12, 'Enrolled Course Student List', 0, 0, 'C');       
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(9,40);      
        $this->Cell(50, 12,$this->course_name, 0, 0, 'L'); 
        $this->SetXY(140,40); 
        if(strlen($this->teacher_name) >= 30){
            $this->SetFont('helvetica', 'B', 8);
        }else{
            $this->SetFont('helvetica', 'B', 10);
        }            
        $this->Cell(50, 12,$this->teacher_name, 0, 0, 'R'); 
        $this->SetFont('helvetica', 'BU', 10);
        $this->SetFont('Arial','B',17);     
        $this->showHeadings();     
        $this->SetFont('helvetica', 'B', 10);
        $this->SetTextColor(0, 0, 0);
        $this->ln(0);
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
        // $this->Ln(18);
        $this->SetFillColor(210,210,210);
        $this->SetFont('Arial','B',10);		//$this->SetLineWidth(.002);
        $this->SetXY(10,50);
        $this->Cell(10,10,'Sr'     ,'1','0','C',1);
        $this->Cell(60,10,'Name'      ,'1','0','C',1);
        $this->Cell(30,10,'Reg'       ,'1','0','C',1);
        $this->Cell(80,10,'Email' 	        ,'1','1','C',1);       
     
    }
  
    function Footer()
    {
        //Position at 1.5 cm from bottom
        $this->SetY(-10);
        $this->SetDrawColor(210,210,210);
        $this->SetLineWidth(.001);
        $this->Cell(410,1,'','T',1);
        $this->SetFont('Arial','I',8);
        // $this->Cell(10, 5,"Confidential ", 0,0, 'L');
        $this->Cell(10,5);
        // $this->Cell(10, 5,"Employee", 0,0, 'L');
        $this->Cell(130,5);
        $date= date('D d-m-Y H:i:s');
        $this->Cell(10, 5, $date, 0,0, 'L');
        $this->Cell(150,5);
        $this->Cell(10,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');

    }
}
if($rsAppacdemicprof!='')
{
    $pdf = new PDF('P','mm',array(200,300));   // creating Object of FPDF class to use its function
    $whileCount = 0;
    $sr = 1;
    
    foreach($rsAppacdemicprof as $rec){
      
        $pdf->course_name  =   $rec->course_name;
        $pdf->teacher_name  =   $rec->teacher_name;
        if($whileCount ==0){              
            $pdf->AliasNbPages();
            $pdf->AddPage();
        }  
            
        $sr_no =  $sr++;
        $student_name	 =  $rec->spi_name;
        $reg	         =  $rec->sai_enrollment_no;
        $email  		 =  $rec->sai_enrollment_no.'@bnu.edu.pk';      

        $pdf->SetFont('Arial','',10); 
        $pdf->Cell(10,6,$sr_no  ,'1','0','C',0);
        $pdf->Cell(60,6,ucfirst($student_name)	    ,'1','0','L',0);
        $pdf->Cell(30,6,$reg	    ,'1','0','C',0);
        $pdf->Cell(80,6,strtolower($email)         ,'1','1','C',0);            
        $whileCount ++;
    }
 
    $pdf->Output('','I');
    $pdf->Close(); // Close PDF Object
    exit();
}else{
    echo "<center><font color=red size=2><b>No Record Found </b></font></center>";
    exit();
}
?>