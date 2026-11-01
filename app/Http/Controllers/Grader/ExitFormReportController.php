<?php

namespace App\Http\Controllers\Grader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExitFormReportController extends Controller
{
    public function index(Request $request)
    {
        // Get all exit form submissions with student details and feedback answers
        $query = DB::table('EPORTAL_REQUEST as er')
            ->join('university_leaving_request as ulr', 'er.id', '=', 'ulr.ER_ID')
            ->join('students_apply_info as sai', 'er.ER_SAI_ENROLLMENT_NO', '=', 'sai.sai_enrollment_no')
            ->leftJoin('students_personal_info as spi', 'sai.sai_spi_id', '=', 'spi.spi_id')
            ->leftJoin('departments as dept', 'sai.sai_sdpo_dept_id', '=', 'dept.dept_id')
            ->leftJoin('schools as sch', 'sai.sai_sdpo_school_id', '=', 'sch.school_id')
            ->leftJoin('programs as prog', 'sai.sai_sdpo_prog_id', '=', 'prog.prog_id')
            ->select([
                'er.id as request_id',
                'er.ER_SAI_ENROLLMENT_NO as enrollment_no',
                'er.ER_ENTRY_DATETIME as submission_date',
                'er.ER_REQUEST_STATUS as status',
                'er.ER_BANK_DETAILS as bank_details',
                'er.ER_LIBRARY_STATUS as library_status',
                'er.ER_APPLY_ANYOTHER_REASON as any_other_reason',
                'er.ER_APPLY_REASON as apply_reason',
                'spi.spi_name as student_name',
                'spi.spi_fathername as father_name',
                'dept.dept_name as department_name',
                'sch.school_name as school_name',
                'prog.prog_name as program_name'
            ])
            ->where('er.EC_ID', 86); // Exit form category ID

        // Apply filters
        if ($request->filled('school')) {
            $query->where('sai.sai_sdpo_school_id', $request->school);
        }
        
        if ($request->filled('department')) {
            $query->where('sai.sai_sdpo_dept_id', $request->department);
        }
        
        if ($request->filled('program')) {
            $query->where('sai.sai_sdpo_prog_id', $request->program);
        }
        
        if ($request->filled('date_from')) {
            $query->where('er.ER_ENTRY_DATETIME', '>=', $request->date_from . ' 00:00:00');
        }
        
        if ($request->filled('date_to')) {
            $query->where('er.ER_ENTRY_DATETIME', '<=', $request->date_to . ' 23:59:59');
        }

        $exitFormReports = $query->orderBy('er.ER_ENTRY_DATETIME', 'desc')
            ->get()
            ->groupBy('request_id');

        // If it's an AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'exitFormReports' => $exitFormReports,
                'total' => count($exitFormReports)
            ]);
        }

        // Get all feedback questions
        $questions = [
            'q1' => 'Personal Email',
            'q2' => 'Reason for Leaving',
            'q3' => 'Any Other Reason for Leaving',
            'q4' => 'Present Postal Address',
            'q5' => 'Overall satisfaction level with academic experience (1-10)',
            'q6' => 'Quality of instruction from faculty members (1-10)',
            'q7' => '3 measures to improve instruction',
            'q8' => 'Career guidance and counselling availability',
            'q9' => 'Ways to improve career counselling',
            'q10' => 'Courses aligned with academic and career goals',
            'q11' => 'Suggested courses to be introduced',
            'q12' => 'Academic mentor support adequacy',
            'q13' => 'Satisfaction with library, labs, study spaces',
            'q14' => 'Satisfaction with campus facilities and amenities',
            'q15' => 'Effectiveness of support services (1-10)',
            'q16' => 'Sense of belonging and inclusivity',
            'q17' => 'Challenges/barriers encountered',
            'q18' => 'Diversity, equity, and inclusion efforts (1-10)',
            'q19' => 'Research, internships, experiential learning opportunities',
            'q20' => 'Suggestions for improving campus environment',
            'q21' => 'Would recommend BNU to prospective students',
            'q22' => 'Additional feedback or experience',
            'q23' => 'Applicant or parent CNIC for security cheque',
            'q24' => 'Applicant or parent name on CNIC'
        ];

        // Get answers for each request
        foreach ($exitFormReports as $requestId => $request) {
            $answers = DB::table('university_leaving_request')
                ->where('ER_ID', $requestId)
                ->orderBy('id')
                ->get();

            $exitFormReports[$requestId]->answers = $answers;
            
            // Parse bank details JSON - handle the collection structure properly
            if (is_object($request) && property_exists($request, 'bank_details') && $request->bank_details) {
                $exitFormReports[$requestId]->bank_details_parsed = json_decode($request->bank_details, true);
            } elseif (is_array($request) && isset($request[0]->bank_details) && $request[0]->bank_details) {
                $exitFormReports[$requestId]->bank_details_parsed = json_decode($request[0]->bank_details, true);
            }
        }

        return view('grader.exit_form_report.index', compact('exitFormReports', 'questions'));
    }

    public function studentStaffExitReport(){
        return view('grader.exit_form_report.student_staff_exit_report');
    }


    public function show($id)
    {
        // Get specific exit form submission
        $exitForm = DB::table('EPORTAL_REQUEST as er')
            ->join('university_leaving_request as ulr', 'er.id', '=', 'ulr.ER_ID')
            ->join('students_apply_info as sai', 'er.ER_SAI_ENROLLMENT_NO', '=', 'sai.sai_enrollment_no')
            ->leftJoin('students_personal_info as spi', 'sai.sai_spi_id', '=', 'spi.spi_id')
            ->leftJoin('departments as dept', 'sai.sai_sdpo_dept_id', '=', 'dept.dept_id')
            ->leftJoin('schools as sch', 'sai.sai_sdpo_school_id', '=', 'sch.school_id')
            ->select([
                'er.id as request_id',
                'er.ER_SAI_ENROLLMENT_NO as enrollment_no',
                'er.ER_ENTRY_DATETIME as submission_date',
                'er.ER_REQUEST_STATUS as status',
                'er.ER_BANK_DETAILS as bank_details',
                'er.ER_LIBRARY_STATUS as library_status',
                'er.ER_APPLY_ANYOTHER_REASON as any_other_reason',
                'er.ER_APPLY_REASON as apply_reason',
                'er.ER_REQUEST_FILE as request_file',
                'spi.spi_name as student_name',
                'spi.spi_fathername as father_name',
                'dept.dept_name as department_name',
                'sch.school_name as school_name'
            ])
            ->where('er.id', $id)
            ->where('er.EC_ID', 86)
            ->first();

        if (!$exitForm) {
            abort(404, 'Exit form not found');
        }

        // Get answers
        $answers = DB::table('university_leaving_request')
            ->where('ER_ID', $id)
            ->orderBy('id')
            ->get();

        // Get questions
        $questions = [
            'q1' => 'Personal Email',
            'q2' => 'Reason for Leaving',
            'q3' => 'Any Other Reason for Leaving',
            'q4' => 'Present Postal Address',
            'q5' => 'Overall satisfaction level with academic experience (1-10)',
            'q6' => 'Quality of instruction from faculty members (1-10)',
            'q7' => '3 measures to improve instruction',
            'q8' => 'Career guidance and counselling availability',
            'q9' => 'Ways to improve career counselling',
            'q10' => 'Courses aligned with academic and career goals',
            'q11' => 'Suggested courses to be introduced',
            'q12' => 'Academic mentor support adequacy',
            'q13' => 'Satisfaction with library, labs, study spaces',
            'q14' => 'Satisfaction with campus facilities and amenities',
            'q15' => 'Effectiveness of support services (1-10)',
            'q16' => 'Sense of belonging and inclusivity',
            'q17' => 'Challenges/barriers encountered',
            'q18' => 'Diversity, equity, and inclusion efforts (1-10)',
            'q19' => 'Research, internships, experiential learning opportunities',
            'q20' => 'Suggestions for improving campus environment',
            'q21' => 'Would recommend BNU to prospective students',
            'q22' => 'Additional feedback or experience',
            'q23' => 'Applicant or parent CNIC for security cheque',
            'q24' => 'Applicant or parent name on CNIC'
        ];

        // Parse bank details
        $bankDetails = null;
        if ($exitForm->bank_details) {
            $bankDetails = json_decode($exitForm->bank_details, true);
        }

        return view('grader.exit_form_report.show', compact('exitForm', 'answers', 'questions', 'bankDetails'));
    }

    public function completeReport($id)
    {
        // Get specific exit form submission
        $exitForm = DB::table('EPORTAL_REQUEST as er')
            ->join('university_leaving_request as ulr', 'er.id', '=', 'ulr.ER_ID')
            ->join('students_apply_info as sai', 'er.ER_SAI_ENROLLMENT_NO', '=', 'sai.sai_enrollment_no')
            ->leftJoin('students_personal_info as spi', 'sai.sai_spi_id', '=', 'spi.spi_id')
            ->leftJoin('departments as dept', 'sai.sai_sdpo_dept_id', '=', 'dept.dept_id')
            ->leftJoin('schools as sch', 'sai.sai_sdpo_school_id', '=', 'sch.school_id')
            ->select([
                'er.id as request_id',
                'er.ER_SAI_ENROLLMENT_NO as enrollment_no',
                'er.ER_ENTRY_DATETIME as submission_date',
                'er.ER_REQUEST_STATUS as status',
                'er.ER_BANK_DETAILS as bank_details',
                'er.ER_LIBRARY_STATUS as library_status',
                'er.ER_APPLY_ANYOTHER_REASON as any_other_reason',
                'er.ER_APPLY_REASON as apply_reason',
                'spi.spi_name as student_name',
                'spi.spi_fathername as father_name',
                'dept.dept_name as department_name',
                'sch.school_name as school_name',
                'er.ER_REQUEST_FILE as request_file'
            ])
            ->where('er.EC_ID', 86)
            ->where('er.id', $id)
            ->first();

        if (!$exitForm) {
            abort(404);
        }

        // Parse bank details
        $bankDetails = null;
        if ($exitForm->bank_details) {
            $bankDetails = json_decode($exitForm->bank_details, true);
        }

        // Get questions
        $questions = [
            'q1' => 'Personal Email',
            'q2' => 'Reason for Leaving',
            'q3' => 'Any Other Reason for Leaving',
            'q4' => 'Present Postal Address',
            'q5' => 'Overall satisfaction level with academic experience (1-10)',
            'q6' => 'Quality of instruction from faculty members (1-10)',
            'q7' => '3 measures to improve instruction',
            'q8' => 'Career guidance and counselling availability',
            'q9' => 'Ways to improve career counselling',
            'q10' => 'Courses aligned with academic and career goals',
            'q11' => 'Suggested courses to be introduced',
            'q12' => 'Academic mentor support adequacy',
            'q13' => 'Satisfaction with library, labs, study spaces',
            'q14' => 'Satisfaction with campus facilities and amenities',
            'q15' => 'Effectiveness of support services (1-10)',
            'q16' => 'Sense of belonging and inclusivity',
            'q17' => 'Challenges/barriers encountered',
            'q18' => 'Diversity, equity, and inclusion efforts (1-10)',
            'q19' => 'Research, internships, experiential learning opportunities',
            'q20' => 'Suggestions for improving campus environment',
            'q21' => 'Would recommend BNU to prospective students',
            'q22' => 'Additional feedback or experience',
            'q23' => 'Applicant or parent CNIC for security cheque',
            'q24' => 'Applicant or parent name on CNIC'
        ];

        // Get answers
        $answers = DB::table('university_leaving_request')
            ->where('ER_ID', $id)
            ->orderBy('id')
            ->get();

        return view('grader.exit_form_report.complete_report', compact('exitForm', 'bankDetails', 'questions', 'answers'));
    }

    public function overallReport(Request $request)
    {
        // Get filter parameters
        $schoolId = $request->get('school');
        $deptId = $request->get('dept');
        $progId = $request->get('prog');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        // Get both "REASON FOR LEAVING" and "ANY OTHER REASON FOR LEAVING" questions
        $question1Text = 'Reason for Leaving';
        $question2Text = 'Any Other Reason for Leaving';

        // Build the base query
        $query = DB::table('university_leaving_request as ulr')
            ->join('EPORTAL_REQUEST as er', 'ulr.ER_ID', '=', 'er.id')
            ->join('students_apply_info as sai', 'er.ER_SAI_ENROLLMENT_NO', '=', 'sai.sai_enrollment_no')
            ->leftJoin('students_personal_info as spi', 'sai.sai_spi_id', '=', 'spi.spi_id')
            ->leftJoin('schools', 'sai.sai_sdpo_school_id', '=', 'school_id')
            ->leftJoin('programs', 'sai.sai_sdpo_prog_id', '=', 'prog_id')
            ->select([
                'ulr.answer',
                'ulr.question',
                'spi.spi_name as student_name',
                'spi.spi_fathername as father_name',
                'sai.sai_enrollment_no as enrollment_no',
                'er.ER_ENTRY_DATETIME as submission_date',
                'er.ER_REQUEST_STATUS as status',
                'sai.sai_sdpo_school_id',
                'sai.sai_sdpo_dept_id',
                'sai.sai_sdpo_prog_id',
                'school_code',
                'prog_name'
            ])
            ->where('er.EC_ID', 86)
            ->whereNotNull('ulr.answer');

        // Apply filters if provided
        if ($schoolId) {
            $query->where('sai.sai_sdpo_school_id', $schoolId);
        }

        if ($deptId) {
            $query->where('sai.sai_sdpo_dept_id', $deptId);
        }

        if ($progId) {
            $query->where('sai.sai_sdpo_prog_id', $progId);
        }

        // Apply date filters if provided
        if ($fromDate) {
            // Convert from_date to start of day (00:00:00)
            $fromDateTime = $fromDate . ' 00:00:00';
            $query->where('er.ER_ENTRY_DATETIME', '>=', $fromDateTime);
        }

        if ($toDate) {
            // Convert to_date to end of day (23:59:59)
            $toDateTime = $toDate . ' 23:59:59';
            $query->where('er.ER_ENTRY_DATETIME', '<=', $toDateTime);
        }

        // Execute the query
        $allStudentAnswers = $query->orderBy('spi.spi_name')
            ->orderBy('ulr.question')
            ->get();

        // Separate answers by question using case-sensitive matching
        $studentAnswers1 = $allStudentAnswers->filter(function($item) {
            return strtolower($item->question) == strtolower('Reason for Leaving');
        });
        
        $studentAnswers2 = $allStudentAnswers->filter(function($item) {
            return strtolower($item->question) == strtolower('Any Other Reason for Leaving');
        });

        $studentAnswers3 = $allStudentAnswers->filter(function($item) {
            return strtolower($item->school_code) == strtolower('School');
        });

        $studentAnswers4 = $allStudentAnswers->filter(function($item) {
            return strtolower($item->prog_name) == strtolower('Program');
        });

        // Generate PDF using FPDF
        $pdfContent = $this->generateReasonForLeavingReport($question1Text, $question2Text, $studentAnswers1, $studentAnswers2, $allStudentAnswers, $fromDate, $toDate,$studentAnswers3,$studentAnswers4);
        
        // Return PDF for inline viewing in browser
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="reason_for_leaving_report.pdf"');
    }

    private function generateReasonForLeavingReport2($question1Text, $question2Text, $studentAnswers1, $studentAnswers2, $allStudentAnswers, $fromDate, $toDate)
    {
        // Include FPDF library
        require_once(public_path('fpdf/fpdf.php'));
        
        // Create PDF object with HORIZONTAL (Landscape) orientation
        $pdf = new \FPDF('L', 'mm', 'A4');
        
        // Set auto page break
        $pdf->SetAutoPageBreak(true, 20);
        
        // Enable page numbering
        $pdf->AliasNbPages();
        
        // Add first page
        $pdf->AddPage();
        
        // Add header to first page
        $this->addProfessionalHeader($pdf, $fromDate, $toDate);
        
        // Single table with all four columns - added Sr No column
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(10, 8, 'Sr No', '1', 0, 'C', true);
        $pdf->Cell(30, 8, 'Enrollment No', '1', 0, 'C', true);
        $pdf->Cell(110, 8, 'Reason for Leaving', '1', 0, 'C', true);
        $pdf->Cell(110, 8, 'Any Other Reason', '1', 1, 'C', true);
        
        // Get all unique enrollment numbers
        $allEnrollments = $allStudentAnswers->pluck('enrollment_no')->unique()->sort();
        
        // Student data - one row per student with page break checks
        $pdf->SetFont('Arial', '', 8);
        $serialNumber = 1; // Initialize serial number counter
        foreach ($allEnrollments as $enrollmentNo) {
            // Check if we need a new page
            if ($pdf->GetY() > 160) { // Leave space for footer and copyright
                $pdf->AddPage();
                
                // Add header to new page
                $this->addProfessionalHeader($pdf, $fromDate, $toDate);
                
                // Re-add table headers on new page
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetFillColor(240, 240, 240);
                $pdf->Cell(10, 8, 'Sr No', '1', 0, 'C', true);
                $pdf->Cell(30, 8, 'Enrollment No', '1', 0, 'C', true);
                $pdf->Cell(110, 8, 'Reason for Leaving', '1', 0, 'C', true);
                $pdf->Cell(110, 8, 'Any Other Reason', '1', 1, 'C', true);
                $pdf->SetFont('Arial', '', 8);
            }
            
            // Get reason for leaving
            $reasonForLeaving = $studentAnswers1->where('enrollment_no', $enrollmentNo)->first();
            $reasonForLeavingAnswer = $reasonForLeaving ? $reasonForLeaving->answer : 'No answer';
            
            // Get any other reason
            $anyOtherReason = $studentAnswers2->where('enrollment_no', $enrollmentNo)->first();
            $anyOtherReasonAnswer = $anyOtherReason ? $anyOtherReason->answer : 'No answer';
            
            // Truncate long text to prevent page overflow
            $maxChars = 150; // Limit characters per cell
            $reasonForLeavingAnswer = strlen($reasonForLeavingAnswer) > $maxChars ? 
                substr($reasonForLeavingAnswer, 0, $maxChars) . '...' : $reasonForLeavingAnswer;
            $anyOtherReasonAnswer = strlen($anyOtherReasonAnswer) > $maxChars ? 
                substr($anyOtherReasonAnswer, 0, $maxChars) . '...' : $anyOtherReasonAnswer;
            
            // Start position for this row
            $startY = $pdf->GetY();
            $maxHeight = 6; // Minimum height for each cell
            
            // Sr No (fixed height)
            $pdf->Cell(10, 6, $serialNumber, '1', 0, 'C');
            
            // Enrollment No (fixed height)
            $pdf->Cell(30, 6, $enrollmentNo ?? 'N/A', '1', 0, 'L');
            
            // Reason for Leaving (MultiCell with text wrapping)
            $pdf->MultiCell(110, 6, $reasonForLeavingAnswer, '1', 'L');
            $currentY = $pdf->GetY();
            $height1 = $currentY - $startY;
            $maxHeight = max($maxHeight, $height1);
            
            // Reset position for next column
            $pdf->SetXY($pdf->GetX() + 10 + 30 + 110, $startY);
            
            // Any Other Reason (MultiCell with text wrapping)
            $pdf->MultiCell(110, 6, $anyOtherReasonAnswer, '1', 'L');
            $currentY = $pdf->GetY();
            $height2 = $currentY - $startY;
            $maxHeight = max($maxHeight, $height2);
            
            // Move to next row
            $pdf->SetY($startY + $maxHeight);
            
            // Increment serial number
            $serialNumber++;
        }
        
        // Output PDF as string
        return $pdf->Output('', 'S');
    }

    private function generateReasonForLeavingReport($question1Text, $question2Text, $studentAnswers1, $studentAnswers2, $allStudentAnswers, $fromDate, $toDate,$studentAnswers3,$studentAnswers4)
    {
        // Include FPDF library
        require_once(public_path('fpdf/fpdf.php'));
        
        // Create PDF object with HORIZONTAL (Landscape) orientation
        // $pdf = new \FPDF('L', 'mm', 'A4');
        $pdf = new \FPDF('L', 'mm', array(200, 350));
        
        // Set auto page break
        $pdf->SetAutoPageBreak(true, 20);
        
        // Enable page numbering
        $pdf->AliasNbPages();
        
        // Add first page
        $pdf->AddPage();
        
        // Add header to first page
        $this->addProfessionalHeader($pdf, $fromDate, $toDate);
        
        // Single table with all four columns - added Sr No column
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(10, 8, 'Sr No', '1', 0, 'C', true);
        $pdf->Cell(30, 8, 'Enrollment No', '1', 0, 'C', true);
        $pdf->Cell(30, 8, 'School', '1', 0, 'C', true);
        $pdf->Cell(60, 8, 'Program', '1', 0, 'C', true);
        $pdf->Cell(110, 8, 'Reason for Leaving', '1', 0, 'C', true);
        $pdf->Cell(90, 8, 'Any Other Reason', '1', 1, 'C', true);
        
        // Get all unique enrollment numbers
        $allEnrollments = $allStudentAnswers->pluck('enrollment_no')->unique()->sort();
        
        // Student data - one row per student with page break checks
        $pdf->SetFont('Arial', '', 10);
        $serialNumber = 1; // Initialize serial number counter
        foreach ($allEnrollments as $enrollmentNo) {
            // Check if we need a new page
            if ($pdf->GetY() > 160) { // Leave space for footer and copyright
                $pdf->AddPage();
                
                // Add header to new page
                $this->addProfessionalHeader($pdf, $fromDate, $toDate);
                
                // Re-add table headers on new page
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetFillColor(240, 240, 240);
                $pdf->Cell(10, 8, 'Sr No', '1', 0, 'C', true);
                $pdf->Cell(30, 8, 'Enrollment No', '1', 0, 'C', true);
                $pdf->Cell(30, 8, 'School', '1', 0, 'C', true);
                $pdf->Cell(60, 8, 'Program', '1', 0, 'C', true);
                $pdf->Cell(110, 8, 'Reason for Leaving', '1', 0, 'C', true);
                $pdf->Cell(90, 8, 'Any Other Reason', '1', 1, 'C', true);
                $pdf->SetFont('Arial', '', 10);
            }
            
            // Get reason for leaving
            $reasonForLeaving = $studentAnswers1->where('enrollment_no', $enrollmentNo)->first();
            $reasonForLeavingAnswer = $reasonForLeaving ? $reasonForLeaving->answer : 'No answer';
            
            // Get any other reason
            $anyOtherReason = $studentAnswers2->where('enrollment_no', $enrollmentNo)->first();
            $anyOtherReasonAnswer = $anyOtherReason ? $anyOtherReason->answer : 'No answer';

            // Fetch SChool data for this student
            $studentData = $allStudentAnswers->where('enrollment_no', $enrollmentNo)->first();
            $schoolCode = $studentData && !empty($studentData->school_code) ? $studentData->school_code : 'N/A';

             // Fetch Program data for this student
            $studentDataProgram = $allStudentAnswers->where('enrollment_no', $enrollmentNo)->first();
            $programCode = $studentDataProgram && !empty($studentDataProgram->prog_name) ? $studentDataProgram->prog_name : 'N/A';

            
            // Truncate long text to prevent page overflow
            $maxChars = 150; // Limit characters per cell
            $reasonForLeavingAnswer = strlen($reasonForLeavingAnswer) > $maxChars ? 
            substr($reasonForLeavingAnswer, 0, $maxChars) . '...' : $reasonForLeavingAnswer;
            $anyOtherReasonAnswer = strlen($anyOtherReasonAnswer) > $maxChars ? 
            substr($anyOtherReasonAnswer, 0, $maxChars) . '...' : $anyOtherReasonAnswer;
            
            // Start position for this row
            $startY = $pdf->GetY();
            $startX = $pdf->GetX();

            // Define column widths
            $widths = [
                'sr' => 10,
                'enroll' => 30,
                'school' => 30,
                'program' => 60,
                'reason' => 110,
                'other' => 90
            ];

            // ---- STEP 1: Measure MultiCell Heights (without borders) ----

            // Measure Reason height
            $pdf->SetXY($startX + $widths['sr'] + $widths['enroll'] + $widths['school'] + $widths['program'], $startY);
            // $pdf->MultiCell($widths['reason'], 6, $reasonForLeavingAnswer, 0, 'L');
            $reasonHeight = $pdf->GetY() - $startY;

            // Measure Other Reason height
            $pdf->SetXY(
                $startX + $widths['sr'] + $widths['enroll'] + $widths['school'] + $widths['program'] + $widths['reason'],
                $startY
            );
            $pdf->MultiCell($widths['other'], 6, $anyOtherReasonAnswer, 0, 'L');
            $otherHeight = $pdf->GetY() - $startY;

            // Find the tallest cell height
            $maxHeight = max(6, $reasonHeight, $otherHeight);

            // ---- STEP 2: Draw All Cells with Same Height ----
            $pdf->SetXY($startX, $startY);

            $pdf->Cell($widths['sr'], $maxHeight, $serialNumber, 1, 0, 'C');
            $pdf->Cell($widths['enroll'], $maxHeight, $enrollmentNo ?? 'N/A', 1, 0, 'L');
            $pdf->Cell($widths['school'], $maxHeight, $schoolCode ?? 'N/A', 1, 0, 'L');

            $pdf->SetFont('Arial', 'B', 6);
            $pdf->Cell($widths['program'], $maxHeight, $programCode ?? 'N/A', 1, 0, 'L');
            $pdf->SetFont('Arial', '', 10);

            // Draw ReasonForLeaving (MultiCell aligned properly)
            $xReason = $pdf->GetX();
            $yReason = $pdf->GetY();
            $pdf->Cell($widths['reason'], $maxHeight, $reasonForLeavingAnswer ?? 'N/A', 1, 0, 'L');
            // $pdf->MultiCell($widths['reason'], 6, $reasonForLeavingAnswer, 1, 'L');

            // Move to next cell position (on same row)
            $pdf->SetXY($xReason + $widths['reason'], $yReason);

            // Draw AnyOtherReason
            $xOther = $pdf->GetX();
            $pdf->MultiCell($widths['other'], 6, $anyOtherReasonAnswer, 1, 'L');

            // ---- STEP 3: Move to next row ----
            $pdf->SetY($startY + $maxHeight);

            // Increment serial
            $serialNumber++;

        }
        
        // Output PDF as string
        return $pdf->Output('', 'S');
    }

    private function addProfessionalHeader($pdf, $fromDate, $toDate)
    {
        // BNU Logo - same as eportal_summary.php
        $pdf->Image(public_path('fpdf/logoimages/bnulogo1.jpg'), 15, 5, 15, 20);
        
        // Letter Head - same as eportal_summary.php
        $pdf->SetXY(35, 10);
        $pdf->SetFont('times', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 0, 'Beaconhouse National University', 0, 0, 'L');
        
        $pdf->SetXY(35, 17);
        $pdf->SetFont('times', '', 12);
        $pdf->Cell(0, 0, '13-km Raiwind Road, Lahore 54400, Pakistan', 0, 0, 'L');
        
        $pdf->SetXY(35, 22);
        $pdf->Cell(0, 5, 'Tel: 92-42-38100156 Email: info@bnu.edu.pk', 0, 1, 'L');
        
        $pdf->ln(1);
        $pdf->Ln();
        
        // Report Title - same style as eportal_summary.php
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(0, 12, 'Exit Form Report - Reasons for Leaving', 0, 1, 'C');
        
        // Display date range if dates are selected
        if ($fromDate || $toDate) {
            $pdf->SetFont('Arial', 'B', 10); // Smaller font size
            $pdf->SetTextColor(0, 0, 0); // Black color for better visibility
            
            $dateText = '';
            if ($fromDate && $toDate) {
                $dateText = 'Date Range: ' . date('d M Y', strtotime($fromDate)) . ' to ' . date('d M Y', strtotime($toDate));
            } elseif ($fromDate) {
                $dateText = 'From Date: ' . date('d M Y', strtotime($fromDate));
            } elseif ($toDate) {
                $dateText = 'To Date: ' . date('d M Y', strtotime($toDate));
            }
            
            $pdf->Cell(0, 8, $dateText, 0, 1, 'C');
        }
        
        // Set position below header - adjust based on whether dates are shown
        if ($fromDate || $toDate) {
            $pdf->SetY(65); // More space when dates are shown
        } else {
            $pdf->SetY(50); // Original position when no dates
        }
    }

    function faculty_comments(Request $request){

        $fromDate = $request->get('from_date') ?? '';
        $toDate = $request->get('to_date') ?? '';
        $id = strtolower($request->get('id') ?? '');

        $dateFilter = "";
        if (!empty($fromDate) && !empty($toDate)) {
            $dateFilter = "AND TRUNC(created_at) BETWEEN TO_DATE('$fromDate', 'YYYY-MM-DD') 
                                            AND TO_DATE('$toDate', 'YYYY-MM-DD')";
        }

        $studentQuery = "SELECT 
                        ULR_ENROLLMENT_NO,er_id,
                        CASE 
                            WHEN fullname IS NULL THEN 
                                (SELECT emp_f_name || ' ' || emp_l_name 
                                FROM emp_employees 
                                WHERE lower(emp_ad_username) = lower(ulr_enrollment_no))
                            ELSE fullname 
                        END AS fullname,
                        CASE 
                            WHEN department IS NULL THEN 
                                (SELECT dept_name
                                FROM emp_employees 
                                LEFT JOIN departments ON dept_id = emp_dept_id
                                WHERE lower(emp_ad_username) = lower(ulr_enrollment_no))
                            ELSE department 
                        END AS department,
                        question,
                        answer,
                        created_at
                    FROM (
                        SELECT 
                            ulr.*,
                            ROW_NUMBER() OVER (
                                PARTITION BY ULR_ENROLLMENT_NO, question 
                                ORDER BY created_at DESC
                            ) AS rn
                        FROM university_leaving_request ulr
                        WHERE question IN (
                            'Principal reason for leaving', 
                            'Did any specific event trigger your decision to leave?'
                        )
                        $dateFilter
                    ) t
                    WHERE rn = 1
                    ORDER BY ULR_ENROLLMENT_NO, question";
                    //  echo $studentQuery;exit;
                    $resEmployee = DB::select($studentQuery);
             //print_r($rec_ora); exit;
             return view('grader.reports.faculty.faculty_comments_vc',['resEmployee'=>$resEmployee]);
    }

     function faculty_comments_overall(Request $request){


        $id = $request->get('id') ?? '';
        $username = $request->get('username') ?? '';
        


        $whereClause = '';
        if ($id !== '' && is_numeric($id)) {
            $whereClause = "WHERE er_id = $id";
        }
        if ($username !== '') {
            $whereClause .= ($whereClause ? " OR " : "WHERE ") . "LOWER(ulr_enrollment_no) = '$username'";
        }
        $studentQuery = "SELECT DISTINCT
        university_leaving_request.question,
        answer,
        university_leaving_request.id,
        ulr_enrollment_no,
        CASE
            WHEN university_leaving_request.fullname IS NULL THEN
                full_name
            ELSE
                university_leaving_request.fullname
        END AS full_name,
        CASE
            WHEN to_char(university_leaving_request.cnic) IS NULL THEN
                to_char(emp_nic)
            ELSE
                to_char(university_leaving_request.cnic)
        END AS emp_nic,
        CASE
            WHEN university_leaving_request.department IS NULL THEN
                sisusers.department
            ELSE
                university_leaving_request.department
        END AS department,
        CASE
            WHEN ulr_enrollment_no = to_char(university_leaving_request.cnic) THEN
                university_leaving_request.designation
        END AS ulr_designation

        FROM
        university_leaving_request
        LEFT JOIN emp_employees ON emp_ad_username = ulr_enrollment_no
        LEFT JOIN sisusers ON sisusers.username = ulr_enrollment_no
        $whereClause
        ORDER BY
        university_leaving_request.id ASC
        FETCH FIRST 31 ROWS ONLY";
        // echo $studentQuery;exit;

                        //  echo $studentQuery;exit;
                        $resEmployee = DB::select($studentQuery);
                //print_r($rec_ora); exit;
            return view('grader.reports.faculty.faculty_comments_vc_overall',['resEmployee'=>$resEmployee]);
       
        // }else{
        //     abort(404, 'Not Authorized');
        // }
    }


    public function export(Request $request)
    {
        // Export functionality for reports
        $format = $request->get('format', 'pdf');
        
        // Get the same data as index method
        $exitFormReports = DB::table('EPORTAL_REQUEST as er')
            ->join('university_leaving_request as ulr', 'er.id', '=', 'ulr.ER_ID')
            ->join('students_apply_info as sai', 'er.ER_SAI_ENROLLMENT_NO', '=', 'sai.sai_enrollment_no')
            ->leftJoin('students_personal_info as spi', 'sai.sai_spi_id', '=', 'spi.spi_id')
            ->leftJoin('departments as dept', 'sai.sai_sdpo_dept_id', '=', 'dept.dept_id')
            ->leftJoin('schools as sch', 'sai.sai_sdpo_school_id', '=', 'sch.school_id')
            ->select([
                'er.id as request_id',
                'er.ER_SAI_ENROLLMENT_NO as enrollment_no',
                'er.ER_ENTRY_DATETIME as submission_date',
                'er.ER_REQUEST_STATUS as status',
                'er.ER_BANK_DETAILS as bank_details',
                'er.ER_LIBRARY_STATUS as library_status',
                'er.ER_APPLY_ANYOTHER_REASON as any_other_reason',
                'er.ER_APPLY_REASON as apply_reason',
                'spi.spi_name as student_name',
                'spi.spi_fathername as father_name',
                'dept.dept_name as department_name',
                'sch.school_name as school_name'
            ])
            ->where('er.EC_ID', 86)
            ->orderBy('er.ER_ENTRY_DATETIME', 'desc')
            ->get()
            ->groupBy('request_id');

        // Get questions
        $questions = [
            'q1' => 'Personal Email',
            'q2' => 'Reason for Leaving',
            'q3' => 'Any Other Reason for Leaving',
            'q4' => 'Present Postal Address',
            'q5' => 'Overall satisfaction level with academic experience (1-10)',
            'q6' => 'Quality of instruction from faculty members (1-10)',
            'q7' => '3 measures to improve instruction',
            'q8' => 'Career guidance and counselling availability',
            'q9' => 'Ways to improve career counselling',
            'q10' => 'Courses aligned with academic and career goals',
            'q11' => 'Suggested courses to be introduced',
            'q12' => 'Academic mentor support adequacy',
            'q13' => 'Satisfaction with library, labs, study spaces',
            'q14' => 'Satisfaction with campus facilities and amenities',
            'q15' => 'Effectiveness of support services (1-10)',
            'q16' => 'Sense of belonging and inclusivity',
            'q17' => 'Challenges/barriers encountered',
            'q18' => 'Diversity, equity, and inclusion efforts (1-10)',
            'q19' => 'Research, internships, experiential learning opportunities',
            'q20' => 'Suggestions for improving campus environment',
            'q21' => 'Would recommend BNU to prospective students',
            'q22' => 'Additional feedback or experience',
            'q23' => 'Applicant or parent CNIC for security cheque',
            'q24' => 'Applicant or parent name on CNIC'
        ];

        // Get answers for each request
        foreach ($exitFormReports as $requestId => $request) {
            $answers = DB::table('university_leaving_request')
                ->where('ER_ID', $requestId)
                ->orderBy('id')
                ->get();

            $exitFormReports[$requestId]->answers = $answers;
            
            // Parse bank details JSON - handle the collection structure properly
            if (is_object($request) && property_exists($request, 'bank_details') && $request->bank_details) {
                $exitFormReports[$requestId]->bank_details_parsed = json_decode($request->bank_details, true);
            } elseif (is_array($request) && isset($request[0]->bank_details) && $request[0]->bank_details) {
                $exitFormReports[$requestId]->bank_details_parsed = json_decode($request[0]->bank_details, true);
            }
        }

        if ($format === 'excel') {
            return $this->exportToExcel($exitFormReports, $questions);
        } else {
            return $this->exportToPdf($exitFormReports, $questions);
        }
    }

    private function exportToExcel($exitFormReports, $questions)
    {
        // Excel export logic would go here
        // For now, return a simple response
        return response()->json(['message' => 'Excel export functionality to be implemented']);
    }

    private function exportToPdf($exitFormReports, $questions)
    {
        // PDF export logic would go here
        // For now, return a simple response
        return response()->json(['message' => 'PDF export functionality to be implemented']);
    }
}

