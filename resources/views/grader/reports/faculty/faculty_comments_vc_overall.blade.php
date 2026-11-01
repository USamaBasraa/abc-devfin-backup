<?php

include(public_path().'/fpdf/fpdf.php');
// echo "213";exit;
class PDF extends FPDF   // Inheriting the parent Class
{
   

    function Header()
    {

        global $rollNo;

        // $this->Image('../lib/fpdf/logoimages/bnulogo1.jpg',15,5,22,27);
        $this->Image(public_path().'/fpdf/logoimages/bnulogo1.jpg',13,5.5,14,24);


        $this->SetXY(40, 10);

        $this->SetFont('times', 'B', 24);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 0, 'Beaconhouse National University', 0, 0, 'L');
        $this->SetXY(40, 17);
        $this->SetFont('times', '', 24);
        $this->Cell(0, 0, '13-km Raiwind Road, Lahore 54400, Pakistan', 0, 0, 'L');
        $this->SetXY(40, 24);
        $this->Cell(0, 5, 'Tel: 92-42-38100156 Email: info@bnu.edu.pk', 0, 1, 'L');
        $this->ln(1);
        $this->Ln();
        $this->SetFont('times', 'BU', 30);
        $this->Cell(0, 12, 'Staff Faculty Exit Form', 0, 1, 'C');
        $pageNumber = $this->PageNo();

        $this->ln(5);
        // $this->Image('../studentpics/'.$rollNo.'.jpg',450,5,20,30);
        $this->showHeadings();
    }

    // Shows Heding Of The Group ( depending upon the level )
    //  first time shows all Group Headings
    function showGroupHeadings($show_level)
    {
        $this->SetFont('Arial', '', 11);  // Setting Font ---   font name, fontstyle ('B','I','U','') , font size
        if ($show_level >= 3) {
            $this->Cell(1, 7, '', '0', 0);

            $this->Ln(5);
        }
        if ($show_level >= 2) {
            $this->Cell(1, 7, '', '0', 0);
            $this->Cell(20, 7, "Branch : ", '0', 0, 'R');
            $this->Cell(10, 7, $this->branch_id, '0', 0, 'R');
            $this->Cell(5, 7, "-", '0', 0, 'C');
            $this->Cell(50, 7, $this->br_name, '0');
            $this->Ln(5);
        }

        $this->Ln(5);
    }

    // shows Headings For Detail Records

     public $full_name;
    public $dept;
    public $rollNo;
    public $cnic;
    public $design;
    public $from_insq_date;
    public $to_insq_date;

    
    function showHeadings()
    {
         $full_name = $this->full_name;
        $dept      = $this->dept;
        $rollNo    = $this->rollNo;
        $cnic      = $this->cnic;
        $design    = $this->design;
        $pageNumber = $this->PageNo();
        $this->SetFillColor(210, 210, 210);
        $this->SetFont('Arial', 'B', 18);
        if ($pageNumber == '1') {
            $this->Cell(23, 12, 'Name: ', 0, 1, 'R');
            $this->Cell(50, 12, 'Department: ', 0, 1, 'L');
            $this->Cell(50, 12, 'CNIC: ', 0, 1, 'L');
            $this->Cell(50, 12, 'Username: ', 0, 1, 'L');
            $this->Cell(50, 12, 'Designation: ', 0, 1, 'L');
            $this->SetFont('Arial', '', 18);
            $this->SetXY(50, 52);
            $this->Cell(50, 12, $full_name, 0, 1, 'L');
            $this->SetX(50);
            $this->Cell(50, 12, $dept, 0, 1, 'L');
            $this->SetX(50);
            $this->Cell(50, 12, $cnic, 0, 1, 'L');
            $this->SetX(50);
            $this->Cell(50, 12, $rollNo, 0, 1, 'L');
            $this->SetX(50);
            $this->Cell(50, 12, $design, 0, 1, 'L');
          
        }

        $this->Ln(8);
        $this->SetFillColor(210, 210, 210);
        $this->SetFont('Arial', 'B', 18);

        $this->Cell(10, 8, 'Sr ', 1, 0, 'C', 1);
        $this->Cell(260, 8, 'Questions', 1, 0, 'C', 1);

        // Calculate the maximum width required for the answers
        $maxAnswerLength = 0;
        // foreach ($answers as $answer) {
        //     $answerLength = $this->GetStringWidth($answer);
        //     if ($answerLength > $maxAnswerLength) {
        //         $maxAnswerLength = $answerLength;
        //     }
        // }

        // $answerCellWidth = max(225, $maxAnswerLength + 10); // Adding some padding

        $this->Cell(260, 8, 'Answers', 1, 1, 'C', 1);
    }
    function initVariables($rec, $from_insq_date, $to_insq_date)
    {
        $this->from_insq_date    = $from_insq_date;
        $this->to_insq_date         = $to_insq_date;
    }

    function showTotals($title, $abcrray_values)
    {

        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(220, 220, 220);
        $this->SetLineWidth(.005);
        $this->Cell(80, 6, $title, '1', '', 'L');
        $this->Cell(55, 6, number_format($abcrray_values[0]), '1', '', 'R');
        $this->Cell(200, 6, '', '1', '', 'R');
        $this->Cell(20, 6, number_format($abcrray_values[1]), '1', '0', 'R');
        $this->Cell(20, 6, number_format($abcrray_values[2]), '1', '0', 'R');
        $this->Cell(20, 6, number_format($abcrray_values[3]), '1', '0', 'R');
        $this->Cell(20, 6, number_format($abcrray_values[4]), '1', '1', 'R');

        $this->ln(1);
    }


    function givePageBreak($pageBreak, $show_level)
    {
        if ($pageBreak == 'Y') {
            $this->addPage();
        } else {

            if ($this->GetY() > 200)
                $this->addPage();
            else
                $this->showGroupHeadings($show_level);
        }
    }


    //Page footer
    function Footer()
    {

        $this->SetY(-10);
        $this->SetDrawColor(210, 210, 210);
        $this->SetLineWidth(.001);
        $this->Cell(320, 1, '', 'T', 1);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(30, 5, "Confidential ", 0, 0, 'L');
        $this->Cell(45, 5);
        $this->Cell(30, 5, "Employee", 0, 0, 'L');
        $this->Cell(90, 5);
        $date = date('D d-m-Y H:i:s');
        $this->Cell(30, 5, $date, 0, 0, 'L');
        $this->Cell(65, 5);
        $this->Cell(30, 5, 'Page ' . $this->PageNo() . ' of {nb}', 0, 0, 'R');
    }


    // public $from_insq_date;
    // public $to_insq_date;
}


    
if (count($resEmployee) >= 1) {
    $pdf = new PDF('P', 'mm', array(550, 550));   // creating Object of FPDF class to use its function
    $whileCount = 0;
    $a = 1;


   $pdf->full_name = $resEmployee[0]->full_name;
    $pdf->dept      = $resEmployee[0]->department;
    $pdf->rollNo    = $resEmployee[0]->ulr_enrollment_no;
    $pdf->cnic      = $resEmployee[0]->emp_nic;
    $pdf->design    = $resEmployee[0]->ulr_designation;


    // if ($rollNo == $cnic) {
    //     $rollNo = 'N/A';
    // }
    // else
    // {
    //     $design = 'N/A';
    // }

    foreach ($resEmployee as $rec) {

        if ($whileCount == 0) {

            // $pdf->initVariables($rec, $from_insq_date, $to_insq_date);
            $pdf->AliasNbPages();

            $pdf->AddPage();
        }
        $pdf->SetFont('Arial', '', 12);

        $questionWidth = 260;
        $answerWidth   = 260;
        $lineHeight    = 8; // base line height per wrapped line

        // --- Calculate number of lines the answer will take ---
        $answerLines = ceil($pdf->GetStringWidth($rec->answer) / ($answerWidth - 2));
        $answerLines = max(1, $answerLines); // minimum 1 line
        $ans_cell_height = $answerLines * $lineHeight;

        // --- Same height for both question and answer ---
        $rowHeight = $ans_cell_height;

        // --- Draw Serial Number ---
        $pdf->Cell(10, $rowHeight, $a++, 1, 0, 'C');

        // --- Save positions ---
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        // --- Draw Question ---
        $pdf->Cell($questionWidth, $rowHeight, $rec->question, 1, 0, 'L');
        // $pdf->MultiCell($questionWidth, $lineHeight, $rec->question, 1, 'L', false);

        // --- Reset X,Y to draw answer next to question ---
        $pdf->SetXY($startX + $questionWidth, $startY);
        $pdf->SetFont('Arial', '', 11);
        // --- Draw Answer ---
        $pdf->MultiCell($answerWidth, $lineHeight, $rec->answer, 1, 'L', false);

        // --- Move cursor below both cells (next row) ---
        $pdf->SetY($startY + $rowHeight);


        // --- Move to next line after both cells ---
        // $pdf->SetY($startY + max(
        //     $pdf->GetY() - $startY, // height used by question
        //     $height
        // ));
        //  $pdf->MultiCell(260, $height,$rec->question, '1', 0, 'L', 0);
        //   $pdf->MultiCell(260, $height,$rec->answer, '1', 1, 'L', 0);
      
        

        $whileCount++;
    }


   $pdf->output();
exit;
} else {
    echo "<center><font color=red size=2><b>No Record Found </b></font></center>";
}
