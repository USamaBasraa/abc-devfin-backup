<?php

include(public_path().'/fpdf/fpdf.php');
// echo "213";exit;
class PDF extends FPDF   // Inheriting the parent Class
{



    function Header()
    {

        global $rollNo;

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
    function showHeadings()
    {
        global $full_name, $school, $dept, $prog, $rollNo, $cnic, $design;
        $pageNumber = $this->PageNo();
        $this->SetFillColor(210, 210, 210);
        $this->SetFont('Arial', 'B', 18);
        // if ($pageNumber == '1') {
        //     $this->Cell(23, 12, 'Name: ', 0, 1, 'R');
        //     $this->Cell(50, 12, 'Department: ', 0, 1, 'L');
        //     $this->Cell(50, 12, 'CNIC: ', 0, 1, 'L');
        //     $this->Cell(50, 12, 'Username: ', 0, 1, 'L');
        //     $this->Cell(50, 12, 'Designation: ', 0, 1, 'L');
        //     $this->SetFont('Arial', '', 18);
        //     $this->SetXY(50, 52);
        //     $this->Cell(50, 12, $full_name, 0, 1, 'L');
        //     $this->SetX(50);
        //     $this->Cell(50, 12, $dept, 0, 1, 'L');
        //     $this->SetX(50);
        //     $this->Cell(50, 12, $cnic, 0, 1, 'L');
        //     $this->SetX(50);
        //     $this->Cell(50, 12, $rollNo, 0, 1, 'L');
        //     $this->SetX(50);
        //     $this->Cell(50, 12, $design, 0, 1, 'L');
          
        // }

        $this->Ln(8);
        $this->SetFillColor(210, 210, 210);
        $this->SetFont('Arial', 'B', 18);

        // $this->Cell(10, 8, 'Sr ', 1, 0, 'C', 1);
        // $this->Cell(250, 8, 'Questions', 1, 0, 'C', 1);

        // Calculate the maximum width required for the answers
        $maxAnswerLength = 0;
        // foreach ($answers as $answer) {
        //     $answerLength = $this->GetStringWidth($answer);
        //     if ($answerLength > $maxAnswerLength) {
        //         $maxAnswerLength = $answerLength;
        //     }
        // }

        $answerCellWidth = max(225, $maxAnswerLength + 10); // Adding some padding

        // $this->Cell($answerCellWidth, 8, 'Answers', 1, 1, 'C', 1);
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


    public $from_insq_date;
    public $to_insq_date;
}





if (count($resEmployee) >= 1) {
    $pdf = new PDF('P', 'mm', array(550, 505));   // creating Object of FPDF class to use its function
    $whileCount = 0;
    $a = 1;


    // $full_name = $resEmployee[0]['FULL_NAME'];

    // $dept = $resEmployee[0]['DEPARTMENT'];

$currentRollNo = '';
$questionCount = 0;
$pageAdded = false;
// dd($resEmployee);
foreach ($resEmployee as $rec) {
    $rollNo = $rec->fullname;
    $department = $rec->department;
    $created_at = $rec->created_at;
    $er_id = $rec->er_id;
    $username = $rec->ulr_enrollment_no;
    // $cnic = $rec->emp_nic;

    // if ($rollNo == $cnic) {
    //     $rollNo = 'N/A';
    // } else {
    //     $design = 'N/A';
    // }

    // Add page once at the start
    if (!$pageAdded) {
        $pdf->AddPage();
        $pageAdded = true;
    }

    // When a new Roll No starts

    // if ($currentRollNo != $rollNo) {
    //     // Reset question counter for new roll number
    //     $pdf->Ln(8);
    //     $questionCount = 0;
    //     $currentRollNo = $rollNo;

    //     // Print Roll No heading
    //     $pdf->SetFillColor(210, 210, 210);
    //     $pdf->SetFont('Arial', 'B', 18);
    //     $pdf->Cell(0, 12, $rollNo . ' (' . $department . ') (' . $created_at . ')', 1, 1, 'L', true);

         
    // }
    if ($currentRollNo != $rollNo) {
        // Reset question counter for new roll number
        $pdf->Ln(8);
        $questionCount = 0;
        $currentRollNo = $rollNo;
        $rollNoText = $rollNo . ' (' . $department . ') (' . $created_at . ')';
        $linkText = 'View Full Form';
        $link = "https://cms.bnu.edu.pk/staff-exit-form-details?id=".$er_id."&username=".$username;

        // Cell dimensions
        $cellHeight = 14;
        $leftMargin = 10;
        $pageWidth = $pdf->GetPageWidth();
        $cellWidth = $pageWidth - ($leftMargin * 2);

        // Background and font
        $pdf->SetFillColor(210, 210, 210);
        $pdf->SetFont('Arial', 'B', 20);

        // Draw full cell (background + border)
        $pdf->SetX($leftMargin);
        $pdf->Cell($cellWidth, $cellHeight, '', 1, 0, 'L', true);

        // Write left-aligned text
        $pdf->SetXY($leftMargin + 2, $pdf->GetY()+3);
        $pdf->SetTextColor(0);
        $pdf->Write(8, $rollNoText);

        // Write right-aligned clickable link
        $pdf->SetTextColor(0, 0, 255);
        $pdf->SetFont('Arial', 'U', 12);
        $linkWidth = $pdf->GetStringWidth($linkText);
        $linkX = $leftMargin + $cellWidth - $linkWidth - 3;
        $pdf->SetXY($linkX, $pdf->GetY());
        $pdf->Write(8, $linkText, $link);

        // Move cursor below the cell
        $pdf->Ln($cellHeight);
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', '', 12);



         
    }

    // Only show first 2 questions for each roll number
    if ($questionCount < 2) {
        $height = 18;
        $chr = strlen($rec->answer);

       

        $pdf->SetFont('Arial', '', 12);

        // Create a side-by-side layout: Question (left), Answer (right)
        // $pdf->Cell(10, $height, $questionCount + 1, 1, 0, 'C');
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(485, 20, $rec->question, 1, 1, 'L'); // Question column
        $pdf->SetFont('Arial', '', 14);
        $pdf->MultiCell(485, $height,$rec->answer, 1, 1, 'L');   // Answer column (same row)

        $questionCount++;
    }
}


 $pdf->output();
exit;
} else {
    echo "<center><font color=red size=2><b>No Record Found </b></font></center>";
}
