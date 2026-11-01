<?php
use Illuminate\Support\Facades\DB;

// Handle large data - increase limits for user-friendly experience
set_time_limit(300); // 5 minutes max execution time
ini_set('memory_limit', '512M'); // Increase memory limit
ini_set('max_execution_time', '300');

// Clean output buffer to prevent corruption
while (ob_get_level()) {
    ob_end_clean();
}

// Include FPDF from public folder
require_once(public_path('fpdf/fpdf.php'));

// Get request parameters
$fromDate = request()->get('from_date');
$toDate = request()->get('to_date');

// PDF Class extending FPDF
class PDF extends FPDF
{
    // Page header
    function Header()
    {
        $this->Image(public_path('fpdf/logoimages/bnulogo1.jpg'), 15, 5, 15, 20);
        // Letter Head
        $this->SetXY(35, 10);
        $this->SetFont('times', 'B', 18);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 0, 'Beaconhouse National University', 0, 0, 'L');
        $this->SetXY(35, 17);
        $this->SetFont('times', '', 12);
        $this->Cell(0, 0, '13-km Raiwind Road, Lahore 54400, Pakistan', 0, 0, 'L');
        $this->SetXY(35, 22);
        $this->Cell(0, 5, 'Tel: 92-42-38100156 Email: info@bnu.edu.pk', 0, 1, 'L');
        $this->ln(1);
        $this->Ln();
        $this->SetFont('times', 'BU', 18);
        $this->Cell(0, 12, 'Student University Leaving Exit Form Report', 0, 1, 'C');

        $this->showHeadings();
    }

    // Shows Heading Of The Group
    function showGroupHeadings($show_level)
    {
        $this->SetFont('Arial', '', 11);
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
        $this->SetFillColor(210, 210, 210);
        $this->SetFont('Arial', 'B', 8); // Increased font size

        $this->Cell(8, 8, 'Sr #', '1', '0', 'C', 1);
        $this->Cell(18, 8, 'Student Id', '1', '0', 'C', 1);
        $this->Cell(18, 8, 'Roll #', '1', '0', 'C', 1);
        $this->Cell(30, 8, 'Student Name', '1', '0', 'C', 1);
        $this->Cell(15, 8, 'Status', '1', '0', 'C', 1);
        $this->Cell(20, 8, 'Request Date', '1', '0', 'C', 1);
        $this->Cell(35, 8, 'Category', '1', '0', 'C', 1);
        $this->Cell(20, 8, 'Account', '1', '0', 'C', 1);

        // Dynamic department columns - show all departments
        if (isset($this->dept_names) && is_array($this->dept_names)) {
            foreach ($this->dept_names as $key => $deptName) {
                $this->Cell(20, 8, substr($deptName, 0, 9), '1', '0', 'C', 1);
            }
        }

        // Add Department HOD column header if there are other departments
        if (isset($this->has_other_depts) && $this->has_other_depts) {
            $this->Cell(20, 8, 'Dept HOD', '1', '1', 'C', 1);
        } else {
            // If no other departments, add line break to last school-11 dept
            $this->Ln();
        }
    }

    // Initializes Public variables of the class
    function initVariables($rec, $from_insq_date, $to_insq_date, $dept_names = array(), $has_other_depts = false)
    {
        $this->from_insq_date    = $from_insq_date;
        $this->to_insq_date      = $to_insq_date;
        $this->dept_names        = $dept_names;
        $this->has_other_depts   = $has_other_depts;
    }

    // shows totals
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

    // Give page break
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

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
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

    // Custom function to wrap text without causing vertical expansion
    function wrapText($text, $maxLength) {
        if (strlen($text) <= $maxLength) {
            return $text;
        }
        
        // Split by spaces and build lines
        $words = explode(' ', $text);
        $lines = array();
        $currentLine = '';
        
        foreach ($words as $word) {
            if (strlen($currentLine . ' ' . $word) <= $maxLength) {
                $currentLine .= ($currentLine ? ' ' : '') . $word;
            } else {
                if ($currentLine) {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
            }
        }
        
        if ($currentLine) {
            $lines[] = $currentLine;
        }
        
        // Return first line only to prevent vertical expansion
        return isset($lines[0]) ? $lines[0] : substr($text, 0, $maxLength);
    }

    // Custom function for Category column with word wrapping
    function wrapCategoryText($text, $maxLength) {
        if (strlen($text) <= $maxLength) {
            return $text;
        }
        
        // Split by spaces and build lines
        $words = explode(' ', $text);
        $lines = array();
        $currentLine = '';
        
        foreach ($words as $word) {
            if (strlen($currentLine . ' ' . $word) <= $maxLength) {
                $currentLine .= ($currentLine ? ' ' : '') . $word;
            } else {
                if ($currentLine) {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
            }
        }
        
        if ($currentLine) {
            $lines[] = $currentLine;
        }
        
        // For Category, return first line with "..." if truncated
        if (count($lines) > 1) {
            return $lines[0] . '...';
        }
        
        return $lines[0];
    }


    // Variables Declarations
    public $from_insq_date;
    public $to_insq_date;
    public $dept_names;
    public $has_other_depts;
}

// Build query with date filters
$additionalConditions = array();

if (!empty($fromDate) && !empty($toDate)) {
    $fromDateTime = $fromDate . ' 00:00:00';
    $toDateTime   = $toDate . ' 23:59:59';

    $additionalConditions[] = "TRIM(er_entry_datetime) BETWEEN '$fromDateTime' AND '$toDateTime'";
}
$studentQuery = "SELECT er_sai_enrollment_no,
                er_entry_datetime,
                ec_title,
                ec_id,
                er_request_status,
                er_resolving_datetime,
                CASE 
                    WHEN ec_id = 120 AND er_sai_enrollment_no IS NOT NULL THEN 
                        (SELECT api_name
                            FROM admission_apply_info aai
                            LEFT JOIN admission_personal_info api 
                                ON to_char(aai.aai_api_id) = to_char(api.api_id)
                            WHERE 
                            to_char(aai.aai_id) = to_char(er_sai_enrollment_no)
                            OR
                            to_char(aai.cms_sai_enrollment_no) = to_char(er_sai_enrollment_no)
                            FETCH FIRST 1 ROWS ONLY
                        )
                    ELSE spi_name
                END AS spi_name,
                (SELECT aai_id FROM admission_apply_info WHERE cms_sai_enrollment_no = sai_enrollment_no) AS aai_id,
                er_applicant_request_type,
                er_request_status,
                CASE 
                    WHEN ec_id = 120 THEN er_applicant_request_type
                    ELSE sai_student_status
                END AS applicant_status
                FROM eportal_request 
                LEFT JOIN eportal_categories ON eportal_request.ec_id = eportal_categories.id
                LEFT JOIN students_apply_info ON to_char(sai_enrollment_no) = to_char(er_sai_enrollment_no)
                LEFT JOIN students_personal_info ON sai_spi_id = spi_id                         
                WHERE ec_id IN (86,120,23,121)";

// If there are additional conditions, append them to the query
if (!empty($additionalConditions)) {
    $studentQuery .= " AND " . implode(" AND ", $additionalConditions);
}
$studentQuery .= " ORDER BY er_entry_datetime DESC";

// Execute query using Laravel DB
$rsStdFeeStatus = DB::select($studentQuery);

// Convert stdClass objects to array (Oracle returns column names in lowercase when aliased)
$rsStdFeeStatus = array_map(function($row) {
    return (array) $row;
}, $rsStdFeeStatus);

// Get unique departments from the actual data being displayed
$uniqueDeptNames = array();
$hasOtherDepts = false;

foreach ($rsStdFeeStatus as $studentRow) {
    // Skip if enrollment number is not set
    if (!isset($studentRow['er_sai_enrollment_no'])) {
        continue;
    }
    
    // Get department details for this student (only for school ID 11)
    $tempSqlDetail = "SELECT DISTINCT d.dept_name 
                     FROM eportal_request_details erd
                     LEFT JOIN departments d ON d.dept_id = erd.er_ecd_dep
                     LEFT JOIN eportal_request er ON erd.er_id = er.id
                     WHERE erd.erd_sai_enrollment_no = '" . $studentRow['er_sai_enrollment_no'] . "'
                     AND erd.er_ecd_instruction_categories IS NULL
                     AND erd.er_ecd_completion_days IS NULL
                     AND erd.er_ecd_viewer_user IS NULL
                     AND erd.er_ecd_dep IS NOT NULL
                     AND er.ec_id IN (86,120,121)
                     AND d.DEPT_SCHOOL_ID = 11";
    
    $tempDeptResult = DB::select($tempSqlDetail);
    $tempDeptResult = array_map(function($row) {
        return (array) $row;
    }, $tempDeptResult);

    foreach ($tempDeptResult as $deptRow) {
        if (!empty($deptRow['dept_name'])) {
            $uniqueDeptNames[] = $deptRow['dept_name'];
        }
    }

    // Check if there are any departments other than school ID 11
    $tempSqlOtherDepts = "SELECT DISTINCT d.dept_name 
                         FROM eportal_request_details erd
                         LEFT JOIN departments d ON d.dept_id = erd.er_ecd_dep
                         LEFT JOIN eportal_request er ON erd.er_id = er.id
                         WHERE erd.erd_sai_enrollment_no = '" . $studentRow['er_sai_enrollment_no'] . "'
                         AND erd.er_ecd_instruction_categories IS NULL
                         AND erd.er_ecd_completion_days IS NULL
                         AND erd.er_ecd_viewer_user IS NULL
                         AND erd.er_ecd_dep IS NOT NULL
                         AND er.ec_id IN (86,120,121)
                         AND (d.DEPT_SCHOOL_ID != 11 OR d.DEPT_SCHOOL_ID IS NULL)";
    
    $tempOtherResult = DB::select($tempSqlOtherDepts);

    if (count($tempOtherResult) > 0) {
        $hasOtherDepts = true;
    }
}

// Get unique department names and sort them
$uniqueDeptNames = array_unique($uniqueDeptNames);
sort($uniqueDeptNames);

// Generate PDF
if (count($rsStdFeeStatus) >= 1) {
    try {
        $pdf = new PDF('L', 'mm', 'A4'); // A4 landscape for proper printing
        $whileCount = 0;
        $a = 1;

        foreach ($rsStdFeeStatus as $rec) {
        if ($whileCount == 0) {
            // Initialization of Inherited Class Variables
            $pdf->initVariables($rec, $fromDate, $toDate, $uniqueDeptNames, $hasOtherDepts);

            // Defines an alias for the total number of pages
            $pdf->AliasNbPages();

            // Adds a new page to the document
            $pdf->AddPage();
        }

        // Get detail records
        $sqlDetail = "SELECT
            erd_sai_enrollment_no,
            emp_name,
            dept_name,
            dept_school_id,
            CASE
                WHEN erd_status IS NULL THEN er_request_status
                ELSE erd_status
            END AS erd_status,
            CASE
                WHEN er_ecd_appr_datetime IS NULL THEN er_resolving_datetime
                ELSE er_ecd_appr_datetime
            END AS er_ecd_appr_datetime
        FROM (
            SELECT
                erd_sai_enrollment_no,
                CASE
                    WHEN er_ecd_approval_user IS NULL THEN empres.emp_f_name || ' ' || empres.emp_l_name
                    ELSE empapr.emp_f_name || ' ' || empapr.emp_l_name
                END AS emp_name,
                CASE
                    WHEN dept_name IS NULL THEN 'A/F'
                    ELSE dept_name
                END AS dept_name,
                deptapr.dept_school_id,
                CASE
                    WHEN eportal_request_details.erd_status = 'Approved' THEN 'Approved'
                    WHEN eportal_request_details.erd_status = 'Declined' THEN 'Declined'
                END AS erd_status,
                er_ecd_appr_datetime,
                er_request_status,
                er_resolving_datetime
            FROM eportal_request_details
            LEFT JOIN emp_employees empapr ON empapr.employee_id = er_ecd_approval_user
            LEFT JOIN emp_employees empres ON empres.employee_id = er_ecd_resolving_user
            LEFT JOIN departments deptapr ON deptapr.dept_id = er_ecd_dep
            LEFT JOIN eportal_request ON er_id = eportal_request.id
            WHERE er_id = (
                SELECT id
                FROM eportal_request
                WHERE ec_id IN (86,120,23,121)
                AND er_sai_enrollment_no = '" . $rec['er_sai_enrollment_no'] . "'
                AND er_entry_datetime = '" . $rec['er_entry_datetime'] . "'
                FETCH FIRST 1 ROWS ONLY
            )
            AND erd_sai_enrollment_no = '" . $rec['er_sai_enrollment_no'] . "'
            AND er_ecd_instruction_categories IS NULL
            AND er_ecd_completion_days IS NULL
            AND ER_ECD_APPROVAL_USER IS NOT NULL
            AND er_ecd_viewer_user IS NULL
            ORDER BY dept_name ASC
        )";

        $rowssqlDetail = DB::select($sqlDetail);
        $rowssqlDetail = array_map(function($row) {
            return (array) $row;
        }, $rowssqlDetail);

        $height = 18; // Same height as original
        $pdf->SetFont('Arial', '', 8); // Font size

        // Displaying data
        $pdf->Cell(8, $height, $a++, '1', '0', 'C');

        if ($rec['ec_id'] == 120) {
            $pdf->Cell(18, $height, $rec['er_sai_enrollment_no'], '1', '0', 'C');
        } else {
            $pdf->Cell(18, $height, $rec['aai_id'], '1', '0', 'C');
        }

        if ($rec['ec_id'] == 120) {
            $pdf->Cell(18, $height, '', '1', '0', 'C');
        } else {
            $pdf->Cell(18, $height, $rec['er_sai_enrollment_no'], '1', '0', 'C');
        }

        // Student Name column with MultiCell
        $currentX = $pdf->GetX();
        $currentY = $pdf->GetY();
        $studentName = $rec['spi_name'];
        $nbLines = $pdf->GetStringWidth($studentName) > 28 ? 2 : 1;
        $textHeight = $nbLines * 4.5;
        $yOffset = ($height - $textHeight) / 2;
        $pdf->SetXY($currentX, $currentY + $yOffset);
        $pdf->MultiCell(30, 4.5, $studentName, 0, 'L', false);
        $pdf->Rect($currentX, $currentY, 30, $height);
        $pdf->SetXY($currentX + 30, $currentY);

        // Status column with MultiCell
        $currentX = $pdf->GetX();
        $currentY = $pdf->GetY();
        if ($rec['ec_id'] == 120 && (empty($rec['applicant_status']) || $rec['applicant_status'] === null)) {
            $statusValue = ucfirst($rec['er_applicant_request_type']);
        } else {
            $statusValue = $rec['applicant_status'];
        }
        $nbLines = $pdf->GetStringWidth($statusValue) > 13 ? 2 : 1;
        $textHeight = $nbLines * 4.5;
        $yOffset = ($height - $textHeight) / 2;
        $pdf->SetXY($currentX, $currentY + $yOffset);
        $pdf->MultiCell(15, 4.5, $statusValue, 0, 'C', false);
        $pdf->Rect($currentX, $currentY, 15, $height);
        $pdf->SetXY($currentX + 15, $currentY);

        // Request Date column with MultiCell - show date and time
        $currentX = $pdf->GetX();
        $currentY = $pdf->GetY();
        $datePart = date('Y-m-d', strtotime($rec['er_entry_datetime']));
        $timePart = date('H:i', strtotime($rec['er_entry_datetime']));
        $requestDate = $datePart . "\n" . $timePart;
        $nbLines = substr_count($requestDate, "\n") + 1; // Count line breaks
        $textHeight = $nbLines * 4.5;
        $yOffset = ($height - $textHeight) / 2;
        $pdf->SetXY($currentX, $currentY + $yOffset);
        $pdf->MultiCell(20, 4.5, $requestDate, 0, 'C', false);
        $pdf->Rect($currentX, $currentY, 20, $height);
        $pdf->SetXY($currentX + 20, $currentY);
        
        // Category column with MultiCell
        $currentX = $pdf->GetX();
        $currentY = $pdf->GetY();
        $categoryValue = $rec['ec_title'];
        $nbLines = $pdf->GetStringWidth($categoryValue) > 33 ? 2 : 1;
        $textHeight = $nbLines * 4.5;
        $yOffset = ($height - $textHeight) / 2;
        $pdf->SetXY($currentX, $currentY + $yOffset);
        $pdf->MultiCell(35, 4.5, $categoryValue, 0, 'C', false);
        $pdf->Rect($currentX, $currentY, 35, $height);
        $pdf->SetXY($currentX + 35, $currentY);

        // Show er_resolving_datetime if not null and status is not Pending
        $accountValue = $rec['er_request_status'];
        if (!empty($rec['er_resolving_datetime']) && strtoupper($rec['er_request_status']) != 'PENDING') {
            // Extract date and time from timestamp
            $datePart = date('Y-m-d', strtotime($rec['er_resolving_datetime']));
            $timePart = date('H:i', strtotime($rec['er_resolving_datetime']));
            $accountValue = $rec['er_request_status'] . "\n" . $datePart . "\n" . $timePart;
        }
        
        // Account column with MultiCell
        $currentX = $pdf->GetX();
        $currentY = $pdf->GetY();
        $nbLines = substr_count($accountValue, "\n") + 1; // Count line breaks
        $textHeight = $nbLines * 4.5;
        $yOffset = ($height - $textHeight) / 2;
        $pdf->SetXY($currentX, $currentY + $yOffset);
        $pdf->MultiCell(20, 4.5, $accountValue, 0, 'C', false);
        $pdf->Rect($currentX, $currentY, 20, $height);
        $pdf->SetXY($currentX + 20, $currentY);

        // Organize department data by department name and school id
        $deptData = array();
        $otherDeptData = array();

        foreach ($rowssqlDetail as $deptRow) {
            $deptName = $deptRow['dept_name'];
            $deptSchoolId = $deptRow['dept_school_id'];

            if ($deptSchoolId == 11) {
                // School 11 departments - separate columns
                $deptData[$deptName] = array(
                    'status' => $deptRow['erd_status'],
                    'datetime' => $deptRow['er_ecd_appr_datetime']
                );
            } else {
                // Other departments - combine in one column
                $otherDeptData[] = array(
                    'dept_name' => $deptName,
                    'status' => $deptRow['erd_status'],
                    'datetime' => $deptRow['er_ecd_appr_datetime']
                );
            }
        }

        // Display each school-11 department column in the correct order - show all departments
        foreach ($uniqueDeptNames as $key => $deptName) {
            $deptValue = '';
            if (isset($deptData[$deptName])) {
                $deptValue = $deptData[$deptName]['status'];
                if (!empty($deptData[$deptName]['datetime']) && strtoupper($deptData[$deptName]['status']) != 'PENDING') {
                    // Extract date and time from timestamp
                    $datePart = date('Y-m-d', strtotime($deptData[$deptName]['datetime']));
                    $timePart = date('H:i', strtotime($deptData[$deptName]['datetime']));
                    $deptValue = $deptData[$deptName]['status'] . "\n" . $datePart . "\n" . $timePart;
                }
            }
            // Save current position
            $currentX = $pdf->GetX();
            $currentY = $pdf->GetY();
            
            // Calculate number of lines for vertical centering
            $textValue = trim($deptValue);
            $nbLines = substr_count($textValue, "\n") + 1; // Count line breaks
            $textHeight = $nbLines * 4.5;
            $yOffset = ($height - $textHeight) / 2;
            
            // Adjust Y position for vertical centering
            $pdf->SetXY($currentX, $currentY + $yOffset);
            
            // Use MultiCell for department column with text wrapping (no border to prevent extra lines)
            $pdf->MultiCell(20, 4.5, $textValue, 0, 'C', false);
            
            // Draw border manually to avoid extra horizontal lines
            $pdf->Rect($currentX, $currentY, 20, $height);
            
            // Restore Y position and move X to next column
            $pdf->SetXY($currentX + 20, $currentY);
        }

        // Display Department HOD column with all non-school-11 departments
        if ($hasOtherDepts) {
            if (count($otherDeptData) > 0) {
                $hodValue = '';
                foreach ($otherDeptData as $otherDept) {
                    if (strtoupper($otherDept['status']) != 'PENDING') {
                        // Extract date and time from timestamp
                        $datePart = date('Y-m-d', strtotime($otherDept['datetime']));
                        $timePart = date('H:i', strtotime($otherDept['datetime']));
                        $hodValue .= $otherDept['status'] . "\n" . $datePart . ' ' . $timePart . "\n";
                    } else {
                        $hodValue .= $otherDept['status'] . "\n";
                    }
                }
                $hodValue = rtrim($hodValue, "\n");
                
                // Save current position
                $currentX = $pdf->GetX();
                $currentY = $pdf->GetY();
                
                // Calculate number of lines for vertical centering
                $nbLines = substr_count($hodValue, "\n") + 1; // Count line breaks
                $textHeight = $nbLines * 4.5;
                $yOffset = ($height - $textHeight) / 2;
                
                // Adjust Y position for vertical centering
                $pdf->SetXY($currentX, $currentY + $yOffset);
                
                // Use MultiCell for Dept HOD column with text wrapping (no border to prevent extra lines)
                $pdf->MultiCell(20, 4.5, $hodValue, 0, 'C', false);
                
                // Draw border manually to avoid extra horizontal lines
                $pdf->Rect($currentX, $currentY, 20, $height);
                
                // Move to next line
                $pdf->SetXY($currentX, $currentY + $height);
                $pdf->Ln(0);
            } else {
                $pdf->Cell(20, $height, '', '1', '1', 'C');
            }
        } else {
            // If no other departments, add line break after last school-11 dept
            $pdf->Ln();
        }

        $whileCount++;
    }

        // Output PDF to browser with proper headers
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="Student_Exit_Form_Report.pdf"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        echo $pdf->Output('Student_Exit_Form_Report.pdf', 'S');
        exit;
        
    } catch (\Exception $e) {
        // Clear any output and show error
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: text/html; charset=utf-8');
        echo "<center><font color=red size=4><b>Error generating PDF</b></font></center>";
        echo "<br><pre>";
        echo "Error: " . $e->getMessage() . "\n";
        echo "Line: " . $e->getLine();
        echo "</pre>";
        exit;
    }
} else {
    // Clear any output and show error
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: text/html; charset=utf-8');
    echo "<center><font color=red size=2><b>No Record Found</b></font></center>";
    exit;
}