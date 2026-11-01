<?php


include('fpdf/fpdf.php');

class PDF extends FPDF   // Inheriting the parent Class
{
    // public $grading;
    // public $total_assessments;
    // public $teacher_name;
    // public $dept_name;
    // public $course_name;
    // public $assess;
    // public $graded;

    public $grading = [];
    public $total_assessments = 0;
    public $teacher_name;
    public $dept_name;
    public $course_name;
    public $assess = [];
    public $graded = 0;

    function Header()
    {
        $this->Image('fpdf/bnulogo1.jpg',10,5,15,20);
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
        // if($this->school_name!=''){
        //     $this->cell(140,10, $this->school_name, 0,0, 'L',0);
        // }else{
        //     $this->Cell(140,10, ' ' , 0,0,'L',0);
        // }
        // if($this->sess_name!=''){
        //     $this->Cell(240,10, $this->sess_name, 0,1, 'R',0);
        // }else{
        //     $this->Cell(240,10, ' ' , 0,0,'R',0);
        // }
        // if($this->teacher_name!=''){
        //     $this->Cell(140,10, $this->teacher_name, 0,0, 'L',0);
        // }else{
        //     $this->Cell(140,10, ' ' , 0,0,'L',0);
        // }
        // if($this->course_name!=''){
        //  $this->Cell(240, 10,'Grading Type'.' '.$this->grader, 0,0, 'R',0);
        // }else{
        //     $this->Cell(240, 10, ' ' , 0,0, 'R',0);
        // }
        $this->SetFont('helvetica', 'B', 18);

        $this->SetXY(160,60);
        $this->Cell(100, 12, 'Course Assessments Original Marks ', 0, 0, 'C');
        $this->SetFont('helvetica', '', 18);
        $this->SetXY(160,72);
        $this->Cell(100, 12, $this->course_name, 0, 0, 'C');
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
        $this->SetXY(20,55);
        $this->Ln(5);
        $this->SetFillColor(210,210,210);
        $this->SetFont('Arial','B',11);		//$this->SetLineWidth(.002);
        $this->Cell(30,30,'Reg No'          ,'1','0','C',1);
        $this->Cell(70,30,'Name' 	        ,'1','0','C',1);
        $count_width = 1;
        $srno=1;
        if($this->assess!=''){
            $assess = $this->assess;
           // echo "===123";exit;
            $actual_columns = count($assess);
            if($assess!=''){
                foreach($assess as $rec1){
                 $assessment =  $rec1->assessment_name;
                    $totalmarks =  $rec1->total_marks;
                    $cell_width = 280/$this->total_assessments;
                    $this->SetFont('Arial','B',11);

                  //  print_r($assess); exit;


                //     $hello = $assess[$this->catch_null($srno,$actual_columns)]->assessment_name;
                  //  echo $hello;exit;

                   //  if($assess[$this->catch_null($srno,$actual_columns)]->assessment_name != $assessment){

                  //  print_r($assess[0]->assessment_name); exit;

                    if( isset($assess[$srno]->assessment_name) && $assess[$srno]->assessment_name != $assessment){

                    //    echo "as"; exit;

                        if($count_width==1){
                            $assessment= $rec1->assessment_short_name;
                        }
                        if($assessment!=''){

                            $this->Cell($cell_width*$count_width,30,$assessment        ,'T,R',0, 'C',1);
                        }else{
                            $this->Cell($cell_width*$count_width,30,  ' '                    ,'T,R',0, 'C',1);
                        }
                        $count_width=0;
                    }else{

                        if($count_width==1){
                            $assessment= $rec1->assessment_short_name;
                        }
                        if($assessment!=''){

                            $this->Cell($cell_width*$count_width,30,$assessment        ,'T,R',0, 'C',1);
                        }else{
                            $this->Cell($cell_width*$count_width,30,  ' '                    ,'T,R',0, 'C',1);
                        }
                        $count_width=0;






                    }
                    $count_width++;
                    $srno++;
                }
            }
        }
        $this->SetFillColor(230,230,230);
        $this->Ln(30);
        $this->Cell(100,10,'Assessment #'       ,'1','0','C',0);
        if($this->assess!=''){
            $total_camnos = count($this->assess);
            foreach($this->assess as $recNoWise){
                $cam_number =  $recNoWise->cam_number;
                $total_camnos--;
                $cam_line_break = 0;
                if($total_camnos==0){
                    $cam_line_break = 1;
                }
                $this->SetFont('Arial','B',11);
                $this->Cell($cell_width,10, $cam_number  ,'1',$cam_line_break,'C',0);
            }
        }
        $this->SetTextColor	(0, 0, 255);
        $this->Cell(100,10,'Total Marks','1','0','C',0);
        // $total_marks_nos = count($this->assess);
        $total_marks_nos = is_array($this->assess)
    ? count($this->assess)
    : 0;
        if($this->assess!=''){
            foreach($this->assess as $recNoWise){
                $total_marks =  $recNoWise->total_marks;
                $total_marks_nos--;
                $total_line_break = 0;
                if($total_marks_nos==0){
                    $total_line_break = 1;
                }
                $this->SetFont('Arial','B',11);
                $this->SetTextColor	(0, 0, 255);
                $this->Cell($cell_width,10,$total_marks,'1',$total_line_break,'C',0);
            }
        }
    }
    function showHeadings2()
    {
        $this->SetXY(230,218);
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
        $this->SetXY(100,90);
        $this->Cell(50,10,'Assessment Name'     ,'1','0','C',1);
        if ($this->graded > 0) {     
            $this->Cell(40,10,'Grading Method','1','0','C',1);        
        }
        $this->Cell(40,10,'Total Entered'       ,'1','0','C',1);
        if ($this->graded > 0) {     
        $this->Cell(30,10,'Selected' 	        ,'1','0','C',1);
        }
        $this->Cell(40,10,'Weightage' 	        ,'1','1','C',1);

     // print_r($this->grading);exit;
        if ($this->grading!=''){
       
        foreach($this->grading as $method){
                $assessment_name =  $method->assessment_name;
                $method_name	 =  $method->method_name;
                $total_enter	 =  $method->total_enter;
                $best_of_no		 =  $method->best_of_no;
                $weightage		 =  $method->cc_weightage;
                

                $this->SetX(100);
                $this->Cell(50,12,$assessment_name  ,'1','0','L',0);
               // echo $this->graded; exit;
                if ($this->graded > 0) {
                    $this->Cell(40,12,$method_name	    ,'1','0','L',0);
                }
                $this->Cell(40,12,$total_enter	    ,'1','0','C',0);
                if ($this->graded > 0) {     
                    if($assessment_name == 'Attendance'){
                        $this->Cell(30,12,'1'       ,'1','0','C',0);
                    }else{
                        $this->Cell(30,12,$best_of_no       ,'1','0','C',0);
                    }
                }

                $this->Cell(40,12,$weightage."%"	,'1','1','C',0);
            }

        }
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
    $pdf = new PDF('L','mm',array(240,400));   // creating Object of FPDF class to use its function
    $whileCount = 0;
    $oldEnroll  = '';
    $obtain_marks = 0;
    // echo count($res1);exit;
    foreach($rsAppacdemicprof as $rec){
        $pdf->graded = $rec->graded;
        $pdf->grader      =  $rec->grader;
        if($whileCount ==0){
           
            if($res_grading!=''){
                $pdf->grading       =  $res_grading;

            }
            if($res1!=''){
                $total_assessments = count($res1);
                $pdf->total_assessments = $total_assessments;
                $pdf->assess = $res1;
                foreach($res1 as $rec1){
                    $pdf->teacher_name   =  $rec1->teacher_name;
                    $pdf->dept_name      =  $rec1->dept_name;
                    $pdf->school_name    =  $rec1->school_name;
                    $pdf->sess_name      =  $rec1->sess_name;
                }
            }
           $pdf->course_name = $rec->course_name;
            $pdf->AliasNbPages();
            $pdf->AddPage();
        }
        $height = 10;
        $enroll = $rec->sai_enrollment_no;
        $assessment_name = $rec->assessment_name;
        $pdf->SetFont('Arial','',12);
        if($assessment_name == ''){
            echo "<center><font color=red size=2><b>No Assessment Found </b></font></center>";
            exit;
        }else{
            if($oldEnroll != $enroll){
                $obtain_marks   =   0;
                $cell_width     =   280/$total_assessments;
                $total_print    =   $total_assessments;
                $pdf->Cell(30,$height,$enroll               ,'1','0','C',false);
                $pdf->Cell(70,$height,$rec->spi_name        ,'1','0','L',false);
                if($rec->ccd_grading_status == 'D' or $rec->cam_grading_status == 'D'){
                    $pdf->SetFillColor(255,255,255);
                    $pdf->Cell($cell_width,$height,$rec->obtained_marks     ,'1',0,'C',0);
                }else{
                    $pdf->SetFillColor(128,128,128);
                    $pdf->Cell($cell_width,$height,$rec->obtained_marks     ,'1',0,'C',1);
                }
                $obtained_marks_total= number_format($obtain_marks,2);
                if($total_print==1){
                    $pdf->Cell(15,$height,  ''        ,'0','1','C',false);
                }
                $oldEnroll = $enroll;
                $total_print--;
            }else{
                if($rec->ccd_grading_status == 'D' or $rec->cam_grading_status == 'D'){
                    $pdf->SetFillColor(255,255,255);
                    $pdf->Cell($cell_width,$height,$rec->obtained_marks,'1',0,'C',0);
                }else{
                    $pdf->SetFillColor(128,128,128);
                    $pdf->Cell($cell_width,$height,$rec->obtained_marks,'1',0,'C',1);
                }
                $total_print--;
                if($total_print==0){
                    $pdf->Cell(15,$height,'','0','1','C',false);
                }
            }
        }
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