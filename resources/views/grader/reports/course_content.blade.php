
<?php



include('fpdf/fpdf.php');
class PDF extends FPDF   // Inheriting the parent Class
{
    public $course_name;
    public $course_code;
    public $co_course_cr;
    public $sess_name;
    public $teachername;
    public $TimeDuration;
   

    function Header()
    {
        $this->Image('fpdf/logoimages/bnulogo1.jpg', 10, 5, 15, 20);
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
        $this->SetFont('helvetica', 'B', 30);
        $this->SetXY(150,40);
        $this->Cell(100, 16, 'Course Content Report ', 0, 1, 'C');
        $this->SetFont('helvetica', 'B', 15);
        $this->SetXY(30,70);
        $this->Cell(20, 10, 'Faculty: '.$this->teachername, 0, 0, 'L');
        $this->SetXY(270,70);
        $this->Cell(50, 10, 'Course Name: '.$this->course_name, 0, 1, 'L');
        $this->SetXY(30,80);
        $this->Cell(20, 10, 'Course Code: '.$this->course_code, 0, 1, 'L');
        $this->SetXY(270,80);
        $this->Cell(50, 10, 'Course Cr Hr: '.$this->co_course_cr, 0, 1, 'L');
        $this->SetXY(30,80);
        $this->Cell(60, 30, 'Session: '.$this->sess_name, 0, 1, 'L');
       
        //$this->SetFont('helvetica', '', 18);
        //$this->SetXY(70,50);
        //$this->Cell(100, 16, 'Course Name ', 0, 1, 'C');

    }   

    

    function showHeadings()
    {
    
        $this->Ln(18);
        $this->SetFillColor(210, 210, 210);
        $this->SetFont('Arial', 'B', 11);	
        $this->SetXY(20, 130);
        $this->Cell(30, 20, 'Lectures No', '1', '0', 'C', 1);
        $this->Cell(60, 20, 'Class Date', '1', '0', 'C', 1);
        $this->Cell(30, 20, 'Duration', '1', '0', 'C', 1);
        $this->Cell(240, 20, 'Course Content', '1', '1', 'C', 1);
    }

    

    
  


}

    $pdf = new PDF('L','mm',array(400,400));   // creating Object of FPDF class to use its function
   


    $course = isset($course_content[0]->course_name) ? $course_content[0]->course_name : '';
    $code = isset($course_content[0]->course_code) ? $course_content[0]->course_code : '';
    $session = isset($course_content[0]->sess_name) ? $course_content[0]->sess_name : '';
    $coursecr = isset($course_content[0]->co_course_cr) ? $course_content[0]->co_course_cr : '';
    $teachername = isset($course_content[0]->teachername) ? $course_content[0]->teachername : '';


    $pdf->course_name=$course;
    $pdf->teachername=$teachername;
    $pdf->course_code=$code;
    $pdf->sess_name=$session;
    $pdf->co_course_cr=$coursecr;


          //  $pdf->AliasNbPages();
    $pdf->AddPage();    
    $pdf->SetFont('times', 'B', 16);
   
    $lecture_no = 1;
   
   


    function createContentStr($contentArr){
        $str = '';
        foreach($contentArr as $row):
            $str .= substr($row->coc_details,0,35)."... \t ";
        endforeach;
        return $str;
    }

    $pdf->showHeadings();
    foreach($course_content as $content){
                   
                    if (isset($content->contents))
                    {
                        $sub_record = (count($content->contents)>1?count($content->contents):1);
                    }else{
                        $sub_record = 1;
                    }
                   // $pdf->course_name=$content->coursename;

                    $cellHeight = 20*$sub_record; // ( count($content->contents) * 20 );
                    $pdf->SetX(20);
                    $pdf->Cell(30,$cellHeight,$lecture_no, '1', '0', 'C', 0);

                  $pdf->Cell(60,$cellHeight,substr($content->tt_class_date,0,11), '1', '0', 'C', 0);
                  $pdf->Cell(30,$cellHeight,substr($content->timeduration.'Hr',0,11), '1', '0', 'C', 0);

                   // $pdf->Cell(30,$cellHeight,$sub_record.'Hr',1,0);
                    
                    if (isset($content->contents))
                    {                     
                        foreach ($content->contents as $sub_content) {
                            $pdf->SetX(140);
                            //$pdf->Cell(30,20,$sub_content->coc_details, '1', '1', 'C', 0);
                            if(strlen($sub_content->coc_details)>90){
                                $pdf->MultiCell(240,20,$sub_content->coc_details,1);
                                $pdf->Ln(20);
                            }else{
                                $pdf->Cell(240,20,$sub_content->coc_details,1,1);
                            }

                            // $pdf->Cell(30,20,$sub_content->coc_details,1,1);
                            // $pdf->MultiCell(110,20,$sub_content->coc_details,1);
                            // $pdf->Ln();
                        }
                    }else{
                        $pdf->SetX(140);
                        $pdf->Cell(240,20,'No Contents',1,1);
                    }



                    //$pdf->SetX(300);
                  
                    // echo  $content->tt_id."___".$content->tt_class_date;
                    // echo "______";
                    // if (isset($content->contents))
                    //  {
                    //    // $sub_rec = $content->contents;
                    //     echo count($content->contents);
                    // }
                  //  echo "<br>";

        
        // if(isset($content->contents) ){
        //   //   $pdf->MultiCell( 200, 40, $reportSubtitle, 1);

      
        //     $pdf->Cell(30,$cellHeight,$lecture_no, '1', '0', 'C', 0);
        //     $pdf->Cell(60,$cellHeight,$content->tt_id."___".$content->tt_class_date, '1', '0', 'C', 0);
        //    // $pdf->Ln(0.20);
        //    // $pdf->MultiCell(110,$cellHeight, createContentStr($content->contents),1);
        //    //  $pdf->SetX(70);
        //    // $pdf->Cell(30,$cellHeight,$content->co_course_cr.'Hr',1,1);
        // }else{
        //     $pdf->SetX(70);
        //     $pdf->Cell(30,20,$lecture_no, '1', '0', 'C', 0);
        //     $pdf->Cell(60,20,$content->tt_id."___".$content->tt_class_date, '1', '0', 'C', 0);
        //     $pdf->Cell(110,20, 'No Content Found!','1', '0', 'C', 0);
        //     $pdf->Cell(30,20,$content->co_course_cr.'Hr',1,1);
        // }
       
       
        

        $lecture_no++;
       
    }     

    //$pdf->Cell(20,20,$sessions['151'],1,0);
         // Cell(240, 10, ' ' , 0,0, 'R',0);
    $pdf->Output('','I');
    $pdf->Close(); // Close PDF Object
    exit();
   
?>