<?php


namespace App\Http\Controllers\Grader;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Log; 
use App\Http\Requests;
use Illuminate\Http\Request;
use App\sessions;
use Illuminate\Support\Facades\File;
use Storage;
use Illuminate\Support\Facades\Http;
use ZipArchive;
use Chumper\Zipper\Zipper;



class StudentDocumentsController extends Controller
{


function getAuthorizedUsersForDocumentsVerification(){
    return 
        ['haider.raza', 'aiman.adnan','somia.maqsood', 'asbah.rehman','zainabsanik', 'sidra.tanveer','zonia.akbar'
        ,'hassan.farooq','bilal.saleem', 'muhammad.hamid', 'noreen','sheeraz', 'saleem', 'arshad.khurshid','anwar.haq'];
    }


  public function documentVerification(Request $request){


    $api_id = $request->get('api_id');
    $prog_id = $request->get('prog_id');
    $sess_id = $request->get('sess_id');

    if($sess_id>163){
        return $this->greaterThan163SessionDocuments($api_id,$prog_id,$sess_id);
    }
    else{
        return $this->lessThan163SessionDocuments($api_id,$prog_id,$sess_id);
    }


  }


  public function lessThan163SessionDocuments($api_id,$prog_id,$sess_id){
    
    $sql = "SELECT
    (
      select SQ_RESULT_STATUS from STUDENTS_QUALIFICATION
        where sq_degree_name = sd_ad_type
      and sq_api_id = sd_ad_api_id

    ) AS result_status,
    (
        SELECT
            MAX(aai_api_email)
        FROM
            admission_apply_info
        WHERE
            aai_api_id = sd_ad_api_id
    ) AS student_email,
    (
        SELECT
            api_name
        FROM
            admission_personal_info
        WHERE
            api_id = sd_ad_api_id
    ) AS applicant_name,
    student_documents.*
FROM
    student_documents
WHERE
    student_documents.sd_ad_type NOT IN ( 'uploadFile' )
      and sd_ad_api_id = $api_id and sd_prog_id=$prog_id
      AND sd_session_id = $sess_id
      ";
    $admissionDocument = DB::select($sql);
    //  echo $sql;exit();
    // dd($admissionDocument);
    // $user = new User();
    // $appliedCriteria = $user->getAppliedCriteria(74620);
    $record = DB::table('admission_personal_info')
    ->where('api_id', $api_id)
    ->first();
    $studentInfo = DB::table('students_apply_info')
    ->where('sai_api_id', $api_id)
    ->first();
  
    return view('grader/studentDocuments/docVerificationOld',['admissionDocument'=>$admissionDocument,
    'record'=>$record,
    'studentInfo'=>$studentInfo,
]);
  }

  public function getSchool(){
    $schools = DB::table('schools')
    ->orderBy('school_id', 'asc')
    ->get();
    return response()->json($schools);
  }

  public function greaterThan163SessionDocuments($api_id,$prog_id,$sess_id){
    
    $username = auth()->user()->username;
    $sql = "SELECT
    (
      select SQ_RESULT_STATUS from STUDENTS_QUALIFICATION
        where sq_degree_name = ad_type
      and sq_api_id = ad_api_id

    ) AS result_status,
    (
        SELECT
            MAX(aai_api_email)
        FROM
            admission_apply_info
        WHERE
            aai_api_id = ad_api_id
    ) AS student_email,
    (
        SELECT
            api_name
        FROM
            admission_personal_info
        WHERE
            api_id = ad_api_id
    ) AS applicant_name,
    admission_document.*
FROM
    admission_document
WHERE
    admission_document.ad_type NOT IN ( 'uploadFile' )
    
      and ad_api_id = $api_id";

    $allowedUsers = $this->getAuthorizedUsersForDocumentsVerification();

    if (in_array($username, $allowedUsers)) {       
        $sql .=       "and cms_prog_id=$prog_id
        and CMS_ENROLLMENT_NO is not null
        AND cms_sess_id = $sess_id ";
    }else{
         $sql .=       "and ad_sdpo_prog_id=$prog_id";
    }
    //    echo $sql;exit();
    $admissionDocument = DB::select($sql);
    //  echo $sql;exit();
    // dd($admissionDocument);
    // $user = new User();
    // $appliedCriteria = $user->getAppliedCriteria(74620);
    $record = DB::table('admission_personal_info')
    ->where('api_id', $api_id)
    ->first();
    $studentInfo = DB::table('students_apply_info')
    ->where('sai_api_id', $api_id)
    ->first();

  

    return view('grader/studentDocuments/docVerificationNew',['admissionDocument'=>$admissionDocument,
    'record'=>$record,
    'studentInfo'=>$studentInfo,
]);

  }


  public function admissionQualification()
  {

      return view('grader/studentDocuments/admissionQualification');

  }


  public function updateDocStatus(Request $request)
  {
      // Log request data for debugging
    //   dd($request->all());

      $validationRules = [
        //   'sd_ad_reason' => 'required',
        'sd_ad_reason' => 'nullable|string', // Update validation rule
      ];

      $validationMessages = [
          'sd_ad_reason.required' => 'The Reason field is required.',
      ];

      // Validate the request data
      $validatedData = $request->validate($validationRules, $validationMessages);

      // Uncomment to start transaction
      // DB::beginTransaction();

      $document = DB::table('student_documents')
    ->where('sd_id', $request->get('sd_ad_id'))
    ->first();

if (!$document) {
    return response()->json(['status' => false, 'message' => 'Document not found.']);
}

// dd(1111111111111);

      try {
          DB::table('student_documents')
              ->where('sd_id', $request->get('sd_ad_id'))
              ->update([
                  'sd_ad_verification_status' => $request->verification_status,
                  'sd_ad_reason' => $request->get('sd_ad_reason'),

              ]);

          // If verification_status is '2', update the document status as well
          if ($request->get('verification_status') == '2') {
              DB::table('student_documents')
                  ->where('sd_id', $request->get('sd_ad_id'))
                  ->update([
                      'sd_ad_document_status' => '1',

                  ]);
          }

          // Uncomment if using transactions
          // DB::commit();

          return response()->json(['status' => true, 'message' => 'Document status has been updated successfully.']);
      } catch (\Exception $e) {
          // Uncomment if using transactions
          // DB::rollback();
          return response()->json(['status' => false, 'message' => 'Failed to verify the document.']);
      }
  }

  public function sendDocumentNotification(Request $request)
  {


      // Validate incoming data
      $validated = $request->validate([
          'remarkInput' => 'required|string|max:255',
          'sai_enrollment_no' => 'required|string|max:255',
      ]);

      // Use the correct enrollment number to find the student
      $updated = DB::table('students_apply_info')
          ->where('sai_enrollment_no', $validated['sai_enrollment_no'])
          ->update([
              'SAI_PF_REMARKS' => $validated['remarkInput'], // Update with the remark input
              'sai_entry_user_name' =>auth()->user()->username,  // Use authenticated user's name
              'sai_entry_datetime' => now(),  // Current date and time
          ]);

      // Return success or error response
      if ($updated) {
          return response()->json([
              'status' => 'success',
              'message' => 'Notification successfully sent to enrollment number ' . $validated['sai_enrollment_no'],
          ], 200);
      } else {
          return response()->json([
              'status' => 'error',
              'message' => 'No records were updated. Please check the enrollment number.',
          ], 404);
      }
  }


  public function getApplicantInformation(Request $request)
  {

      $username = auth()->user()->username;
      $session = $request->input('session');
      $school = $request->input('school');
      $dept = $request->input('department');
      $prog = $request->input('program');
  
      if ($session>163) {
        return $this->greaterThan163SessionStudentInfo($username,$session,$school,$dept,$prog);
    }
    else{
        return $this->lessThan163SessionStudentInfo($username,$session,$school,$dept,$prog);
    }
  }


  public function greaterThan163SessionStudentInfo($username,$session,$school,$dept,$prog){
 
    $allowedUsers = $this->getAuthorizedUsersForDocumentsVerification();

    if (in_array($username, $allowedUsers)) {       
        $sql = "SELECT
                admission_apply_info.aai_sdpo_prog_id           AS aai_sdpo_prog_id,
                admission_apply_info.aai_sdpo_sess_id           AS aai_sdpo_sess_id,
                admission_apply_info.aai_api_id                 AS api_id,
                admission_personal_info.api_mobile              AS api_mobile,
                admission_personal_info.api_name                AS api_name,
                admission_personal_info.api_formid              AS api_formid,
                admission_personal_info.api_nic                 AS api_nic,
                admission_personal_info.api_guardian_cnic       AS api_guardian_cnic,
                admission_personal_info.api_fathername          AS api_fathername,
                admission_personal_info.api_imagename           AS api_imagename,
                admission_apply_info.aai_id                     AS aai_id,
                admission_apply_info.aai_api_form_id            AS aai_api_form_id,
                programs.prog_title                            AS aai_programid,
                programs.prog_title                            AS aai_degreecode,
                admission_apply_info.aai_interview_marks        AS aai_interview_marks,
                admission_apply_info.cms_sai_enrollment_no          AS CMS_sai_enrollment_no,
                admission_apply_info.aai_interview_totalmarks   AS aai_interview_totalmarks,
                admission_personal_info.api_email               AS uemail,
                admission_apply_info.aai_pf_remarks             AS aai_pf_remarks,
                admission_apply_info.aai_qualification_status   AS aai_qualification_status,
                admission_apply_info.aai_qualification_remarks  AS aai_qualification_remarks,

            CASE
                WHEN EXISTS (
                    SELECT
                        1
                    FROM
                        admission_document
                    WHERE
                            admission_document.cms_enrollment_no = admission_apply_info.cms_sai_enrollment_no
                        AND admission_document.ad_type != 'uploadFile'
                        AND admission_document.ad_document_status = 2
                        AND admission_document.ad_verification_status = 2
                ) THEN
                    1
                ELSE
                    0
            END                                            AS upload_missing_document,
            to_char(trim(admission_apply_info.aai_remarks)) AS aai_remarks,

            (   select MAX(cms_prog_id) from admission_document
                    where cms_enrollment_no = admission_apply_info.cms_sai_enrollment_no
            ) as cms_prog_id,




            (
                SELECT
                    admission_schedules.admission_sch_status
                FROM
                    admission_schedules
                WHERE
                    admission_schedules.admission_sch_id = admission_apply_info.aai_admission_sch_id
            )                                              AS sch_status
            FROM
                admission_apply_info
                LEFT JOIN admission_personal_info ON admission_apply_info.aai_api_id = admission_personal_info.api_id
                LEFT JOIN programs ON admission_apply_info.aai_sdpo_prog_id = programs.prog_id
                LEFT JOIN schools ON admission_apply_info.aai_sdpo_school_id = schools.school_id
                LEFT JOIN departments ON admission_apply_info.aai_sdpo_dept_id = departments.dept_id
                LEFT JOIN admission_schedules ON admission_schedules.admission_sch_id = admission_apply_info.aai_admission_sch_id
            WHERE
                admission_apply_info.aai_sdpo_sess_id IS NOT NULL
                and CMS_sai_enrollment_no is not null
                ";
            // Add dynamic conditions based on request inputs
            if (!empty($session)) {
                $sql .= " AND admission_apply_info.aai_sdpo_sess_id = $session";
            }

            if (!empty($school)) {
                $sql .= " AND admission_apply_info.aai_sdpo_school_id = $school";
            }

            if (!empty($dept)) {
                $sql .= " AND admission_apply_info.aai_sdpo_dept_id = $dept";
            }

            if (!empty($prog)) {
                $sql .= " AND admission_apply_info.aai_sdpo_prog_id = $prog";
            }

            // Restrict data access for specific users
            if (!in_array($username,$allowedUsers)) {
                $sql .= " AND admission_apply_info.aai_sdpo_school_id IN (
                SELECT emp_school_id
                FROM emp_employees
                WHERE emp_ad_username = '$username'
            )";
            }
            // echo $sql;exit;
            $res = DB::select($sql);
            return response()->json($res);
    }else{
        $sql = "SELECT
                admission_apply_info.aai_sdpo_prog_id           AS aai_sdpo_prog_id,
                admission_apply_info.aai_sdpo_sess_id           AS aai_sdpo_sess_id,
                admission_apply_info.aai_api_id                 AS api_id,
                admission_personal_info.api_mobile              AS api_mobile,
                admission_personal_info.api_name                AS api_name,
                admission_personal_info.api_formid              AS api_formid,
                admission_personal_info.api_nic                 AS api_nic,
                admission_personal_info.api_guardian_cnic       AS api_guardian_cnic,
                admission_personal_info.api_fathername          AS api_fathername,
                admission_personal_info.api_imagename           AS api_imagename,
                admission_apply_info.aai_id                     AS aai_id,
                admission_apply_info.aai_api_form_id            AS aai_api_form_id,
                programs.prog_title                            AS aai_programid,
                programs.prog_title                            AS aai_degreecode,
                admission_apply_info.aai_interview_marks        AS aai_interview_marks,
                admission_apply_info.cms_sai_enrollment_no          AS CMS_sai_enrollment_no,
                admission_apply_info.aai_interview_totalmarks   AS aai_interview_totalmarks,
                admission_personal_info.api_email               AS uemail,
                admission_apply_info.aai_pf_remarks             AS aai_pf_remarks,
                admission_apply_info.aai_qualification_status   AS aai_qualification_status,
                admission_apply_info.aai_qualification_remarks  AS aai_qualification_remarks,
            CASE
                WHEN EXISTS (
                    SELECT
                        1
                    FROM
                        admission_document
                    WHERE
                            admission_document.cms_enrollment_no = admission_apply_info.cms_sai_enrollment_no
                        AND admission_document.ad_type != 'uploadFile'
                        AND admission_document.ad_document_status = 2
                        AND admission_document.ad_verification_status = 2
                ) THEN
                    1
                ELSE
                    0
            END                                            AS upload_missing_document,
            to_char(trim(admission_apply_info.aai_remarks)) AS aai_remarks,
            (
                SELECT
                    admission_schedules.admission_sch_status
                FROM
                    admission_schedules
                WHERE
                    admission_schedules.admission_sch_id = admission_apply_info.aai_admission_sch_id
            )                                              AS sch_status
            FROM
                admission_apply_info
                LEFT JOIN admission_personal_info ON admission_apply_info.aai_api_id = admission_personal_info.api_id
                LEFT JOIN programs ON admission_apply_info.aai_sdpo_prog_id = programs.prog_id
                LEFT JOIN schools ON admission_apply_info.aai_sdpo_school_id = schools.school_id
                LEFT JOIN departments ON admission_apply_info.aai_sdpo_dept_id = departments.dept_id
                LEFT JOIN admission_schedules ON admission_schedules.admission_sch_id = admission_apply_info.aai_admission_sch_id
            WHERE
               1=1   
            and aai_pf_status = 'Fee Verified'
            and admission_apply_info.aai_qualification_status = 'Accepted'
            and aai_finalsubmit = 1";
            // Add dynamic conditions based on request inputs
            if (!empty($session)) {
                $sql .= " AND admission_apply_info.aai_sdpo_sess_id = $session";
            }

            if (!empty($school)) {
                $sql .= " AND admission_apply_info.aai_sdpo_school_id = $school";
            }

            if (!empty($dept)) {
                $sql .= " AND admission_apply_info.aai_sdpo_dept_id = $dept";
            }

            if (!empty($prog)) {
                $sql .= " AND admission_apply_info.aai_sdpo_prog_id = $prog";
            }

            
            //echo $sql;exit;
            $res = DB::select($sql);
            return response()->json($res);


    }
  }

  public function lessThan163SessionStudentInfo($username,$session,$school,$dept,$prog){

    $sql = "SELECT
    students_apply_info.sai_sdpo_prog_id AS aai_sdpo_prog_id,
    students_apply_info.sai_sdpo_sess_id AS aai_sdpo_sess_id,
    students_apply_info.sai_api_id AS api_id,
    students_personal_info.spi_mobile AS api_mobile,
    students_personal_info.spi_name AS api_name,
    students_personal_info.spi_formid AS api_formid,
    students_personal_info.spi_nic AS api_nic,
    students_personal_info.spi_guardian_cnic AS api_guardian_cnic,
    students_personal_info.spi_fathername AS api_fathername,
    students_personal_info.spi_imagename AS api_imagename,
    students_apply_info.sai_id AS aai_id,
    students_apply_info.sai_api_form_id AS aai_api_form_id,
    programs.prog_title AS aai_programid,
    programs.prog_title AS aai_degreecode,
    students_apply_info.sai_interview_marks AS aai_interview_marks,
    students_apply_info.sai_enrollment_no AS CMS_sai_enrollment_no,
    students_apply_info.sai_interview_totalmarks AS aai_interview_totalmarks,
    students_personal_info.spi_email AS uemail,
    students_apply_info.sai_pf_remarks AS aai_pf_remarks,
    students_apply_info.sai_qualification_status AS aai_qualification_status,
    students_apply_info.sai_qualification_remarks AS aai_qualification_remarks,
    students_apply_info.sai_student_status AS sai_student_status,
    CASE
        WHEN EXISTS (
            SELECT 1
            FROM student_documents
            WHERE student_documents.sd_enrollment_no = students_apply_info.sai_enrollment_no
            AND student_documents.sd_ad_type != 'uploadFile'
            AND student_documents.sd_ad_document_status = 2
            AND student_documents.sd_ad_verification_status = 2
        ) THEN 1
        ELSE 0
    END AS upload_missing_document,
    TO_CHAR(TRIM(students_apply_info.sai_remarks)) AS sai_remarks,
    (
        SELECT
            admission_schedules.admission_sch_status
        FROM
            admission_schedules
        WHERE
            admission_schedules.admission_sch_id = students_apply_info.sai_admission_sch_id
    ) AS sch_status
FROM
    students_apply_info
LEFT JOIN students_personal_info ON students_apply_info.sai_spi_id = students_personal_info.spi_id
LEFT JOIN programs ON students_apply_info.sai_sdpo_prog_id = programs.prog_id
LEFT JOIN schools ON students_apply_info.sai_sdpo_school_id = schools.school_id
LEFT JOIN departments ON students_apply_info.sai_sdpo_dept_id = departments.dept_id
LEFT JOIN admission_schedules ON admission_schedules.admission_sch_id = students_apply_info.sai_admission_sch_id
WHERE
    students_apply_info.sai_sdpo_sess_id IS NOT NULL";

// Add dynamic conditions based on request inputs
if (!empty($session)) {
    $sql .= " AND students_apply_info.sai_sdpo_sess_id = $session";
}

if (!empty($school)) {
    $sql .= " AND students_apply_info.sai_sdpo_school_id = $school";
}

if (!empty($dept)) {
    $sql .= " AND students_apply_info.sai_sdpo_dept_id = $dept";
}

if (!empty($prog)) {
    $sql .= " AND students_apply_info.sai_sdpo_prog_id = $prog";
}

$allowedUsers = $this->getAuthorizedUsersForDocumentsVerification();
// Restrict data access for specific users
if (!in_array($username,$allowedUsers)) {
  $sql .= " AND students_apply_info.sai_sdpo_school_id IN (
      SELECT emp_school_id
      FROM emp_employees
      WHERE emp_ad_username = '$username'
  )";
}
// Execute the query
$res = DB::select($sql);
// Return the results as JSON
return response()->json($res);

  }
  

   public function fetchPreviousUploads(Request $request)
   {
       // Validate incoming request for the required parameter
       $validated = $request->validate([
           'id' => 'required|integer',
           'sess_id' => 'required|integer',
       ]);

       if($request->input('sess_id')>163){
        return $this->fetchPreviousUploadsAfter163($request->input('id'));
       }
       else{
        return $this->fetchPreviousUploadsBefore163($request->input('id'));
       }
   }

   protected function fetchPreviousUploadsBefore163($id){
      // Fetch data from the database
      $documents = DB::table('AUD_BNUCMS.STUDENT_DOCUMENTS_AUD')
          ->where('sd_ad_id', $id)
          ->get();
      if ($documents->isEmpty()) {
          return response()->json(['status' => false, 'message' => 'No previous uploads found.']);
      }
      // Return the data in JSON format for frontend
      return response()->json(['status' => true, 'data' => $documents]);

   }
   protected function fetchPreviousUploadsAfter163($id){

    $documents = DB::table('AUD_BNUCMS.admission_document_aud')
          ->where('ad_id', $id)
          ->get();
      if ($documents->isEmpty()) {
          return response()->json(['status' => false, 'message' => 'No previous uploads found.']);
      }
      // Return the data in JSON format for frontend
   }



   public function deleteUploadedStudentDoc(Request $request)
   {
       try {
           $deleted = DB::table('student_documents')->where('sd_ad_id', $request->id)->delete();

           if (!$deleted) {
               return response()->json(['success' => false, 'message' => 'Document not found.']);
           }

           return response()->json(['success' => true, 'message' => 'Document deleted successfully.']);
       } catch (\Exception $e) {
           return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
       }
   }


   
   protected function DownloadDocumentBundle(Request $request)
   {
       $api_id = $request->input('api_id');
       $prog_id = $request->input('prog_id');
       $sess_id = $request->input('sess_id');

       if($sess_id>163){
        return $this->after163SessionDownloadDocumentBundle($api_id,$prog_id,$sess_id);
       }
       else{
        return $this->before163SessionDownloadDocumentBundle($api_id,$prog_id,$sess_id);
       }
   
   }
   


protected function before163SessionDownloadDocumentBundle($api_id,$prog_id,$sess_id){
    try {
        // Get base URL dynamically
        $protocol = request()->isSecure() ? 'https' : 'http';
        $baseUrl = request()->root();
        $rootUrl = "{$baseUrl}/docs/adm_docs/up_docs/{$api_id}";

        // Fetch student documents
        $sql = "SELECT
            (SELECT SQ_RESULT_STATUS FROM STUDENTS_QUALIFICATION 
             WHERE sq_degree_name = sd_ad_type AND sq_api_id = sd_ad_api_id) AS result_status,
            (SELECT MAX(aai_api_email) FROM admission_apply_info 
             WHERE aai_api_id = sd_ad_api_id) AS student_email,
            (SELECT api_name FROM admission_personal_info 
             WHERE api_id = sd_ad_api_id) AS applicant_name,
            student_documents.sd_ad_filename,
            student_documents.sd_ad_type,
            student_documents.sd_ad_filelink
        FROM student_documents
        WHERE student_documents.sd_ad_type NOT IN ('uploadFile')
          AND sd_ad_api_id = ?
          AND sd_prog_id = ?
          AND sd_session_id = ?";

        $admissionDocuments = DB::select($sql, [$api_id, $prog_id, $sess_id]);

        if (empty($admissionDocuments)) {
            return response()->json([
                'message' => 'No documents found for this student',
                'status' => false
            ], 404);
        }

        // Get the applicant's name and format it
        $applicantName = $admissionDocuments[0]->applicant_name ?? 'unknown';
        $formattedName = strtolower(str_replace(' ', '_', trim($applicantName)));
        
        // Create a user-specific folder with formatted name
        $userId = auth()->user()->id;
        $folderName = "{$api_id}";
        $userFolderPath = public_path("docs/adm_docs/up_docs/{$api_id}");
        $courseFolderPath = "{$userFolderPath}/{$folderName}";

        // Ensure directories exist with proper permissions
        if (!File::exists($courseFolderPath)) {
            File::makeDirectory($courseFolderPath, 0777, true);
        }

        // Download files
        foreach ($admissionDocuments as $doc) {
            // Skip if no filename or type
            if (empty($doc->sd_ad_type)) {
                $this->insertIntoTesting("Skipping document with no type for API ID: {$api_id}");
                continue;
            }

            // Check if file exists in docs/adm_docs/up_docs
            $existingFilePath = public_path("docs/adm_docs/up_docs/" . $doc->sd_ad_filename);
            if (!empty($doc->sd_ad_filename) && File::exists($existingFilePath)) {
                // Copy existing file to course folder
                $newFileName = $doc->sd_ad_type . '_' . time() . '.' . pathinfo($doc->sd_ad_filename, PATHINFO_EXTENSION);
                $finalPath = "{$courseFolderPath}/{$newFileName}";
                File::copy($existingFilePath, $finalPath);
                chmod($finalPath, 0777);
                $this->insertIntoTesting("Copied existing file: {$doc->sd_ad_filename} to {$newFileName}");
                continue;
            }

            // If file doesn't exist locally, try downloading
            $tempFile = tempnam(sys_get_temp_dir(), 'download_');
            
            try {
                // Handle Google Drive or regular URLs
                if (!empty($doc->sd_ad_filelink) && str_contains($doc->sd_ad_filelink, 'drive.google.com')) {
                    $fileUrl = $this->convertGoogleDriveLink($doc->sd_ad_filelink);
                } else {
                    $fileUrl = $rootUrl . '/' . $doc->sd_ad_filename;
                }

                // Skip if URL is empty
                if (empty($fileUrl)) {
                    $this->insertIntoTesting("Skipping empty file URL for {$doc->sd_ad_filename}");
                    continue;
                }

                // Download to temp file
                $response = Http::timeout(60)->get($fileUrl);
                if (!$response->successful()) {
                    $this->insertIntoTesting("Failed to download: {$fileUrl}");
                    continue;
                }

                // Save to temp file
                File::put($tempFile, $response->body());

                // Get real file extension from content
                $realExtension = $this->getFileExtensionFromContent($tempFile);
                
                // Create final filename with correct extension
                $newFileName = $doc->sd_ad_type . '_' . time() . '.' . $realExtension;
                $finalPath = "{$courseFolderPath}/{$newFileName}";

                // Move temp file to final destination
                File::move($tempFile, $finalPath);
                chmod($finalPath, 0777);

                $this->insertIntoTesting("Successfully downloaded and processed: {$newFileName}");

            } catch (\Exception $e) {
                $this->insertIntoTesting("Error processing file {$doc->sd_ad_filename}: " . $e->getMessage());
            } finally {
                // Clean up temp file
                if (File::exists($tempFile)) {
                    File::delete($tempFile);
                }
            }
        }

        // Create ZIP Archive
        $zipName = "{$folderName}.zip";
        $zipPath = "{$userFolderPath}/{$zipName}";

        // Remove old zip if exists
        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        // Create new zip
        $command = "cd " . escapeshellarg($userFolderPath) . " && zip -r " . escapeshellarg($zipName) . " " . escapeshellarg($folderName);
        exec($command, $output, $status);

        if ($status !== 0) {
            return response()->json([
                'message' => 'Failed to create ZIP file',
                'status' => false,
                'error' => implode("\n", $output)
            ], 500);
        }

        // Set proper permissions for zip
        chmod($zipPath, 0777);

        // Generate download URL
        $zipUrl = "{$protocol}://" . $_SERVER['HTTP_HOST'] . "/downloadBundle/{$userId}/{$zipName}";

        return response()->json([
            'message' => 'Download URL generated successfully',
            'status' => true,
            'url' => $zipUrl
        ]);

    } catch (\Exception $e) {
        $this->insertIntoTesting("Error in before163SessionDownloadDocumentBundle: " . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred while processing the documents',
            'error' => $e->getMessage(),
            'status' => false
        ], 500);
    }
}








  
   /**
    * Convert Google Drive links to direct download links and get file info
    */
   protected function convertGoogleDriveLink($url)
   {
       $fileId = '';
       if (preg_match('/drive\.google\.com\/file\/d\/([^\/]+)/', $url, $matches)) {
           $fileId = $matches[1];
       } elseif (preg_match('/drive\.google\.com\/uc\?id=([^&]+)/', $url, $matches)) {
           $fileId = $matches[1];
       }

       // First get the file metadata to get real filename
       $metaUrl = "https://www.googleapis.com/drive/v3/files/{$fileId}?fields=name,mimeType&key=YOUR_API_KEY";
       try {
           $response = Http::get($metaUrl);
           if ($response->successful()) {
               $metadata = $response->json();
               return [
                   'download_url' => "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&key=YOUR_API_KEY",
                   'filename' => $metadata['name'],
                   'mime_type' => $metadata['mimeType']
               ];
           }
       } catch (\Exception $e) {
           $this->insertIntoTesting("Failed to get file metadata: " . $e->getMessage());
       }

       // Fallback to direct download link if metadata fetch fails
       return "https://drive.google.com/uc?export=download&id={$fileId}";
   }
   
   /**
    * Get file extension from file content
    */
   protected function getFileExtensionFromContent($filePath) {
       $finfo = finfo_open(FILEINFO_MIME_TYPE);
       $mimeType = finfo_file($finfo, $filePath);
       finfo_close($finfo);

       $this->insertIntoTesting("Detected MIME type: " . $mimeType . " for file: " . $filePath);

       $mimeToExt = [
           // PDF
           'application/pdf' => 'pdf',
           
           // Images
           'image/jpeg' => 'jpg',
           'image/jpg' => 'jpg',
           'image/png' => 'png',
           'image/gif' => 'gif',
           'image/bmp' => 'bmp',
           'image/tiff' => 'tiff',
           'image/webp' => 'webp',
           
           // Archives
           'application/x-rar' => 'rar',
           'application/x-rar-compressed' => 'rar',
           'application/zip' => 'zip',
           'application/x-zip-compressed' => 'zip',
           'application/x-7z-compressed' => '7z',
           'application/x-tar' => 'tar',
           'application/x-gzip' => 'gz',
           
           // Microsoft Office
           'application/msword' => 'doc',
           'application/vnd.ms-word' => 'doc',
           'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
           'application/vnd.ms-excel' => 'xls',
           'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
           'application/vnd.ms-powerpoint' => 'ppt',
           'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
           
           // Text
           'text/plain' => 'txt',
           'text/html' => 'html',
           'text/css' => 'css',
           'text/javascript' => 'js',
           'text/xml' => 'xml',
           'text/csv' => 'csv',
           
           // Other common types
           'application/json' => 'json',
           'application/xml' => 'xml',
           'application/sql' => 'sql',
           'application/rtf' => 'rtf'
       ];

       // If we have a direct match in our array
       if (isset($mimeToExt[$mimeType])) {
           return $mimeToExt[$mimeType];
       }

       // Try to get extension from original file path
       $originalExt = pathinfo($filePath, PATHINFO_EXTENSION);
       if (!empty($originalExt)) {
           return strtolower($originalExt);
       }

       // For octet-stream, try to detect common file signatures
       if ($mimeType === 'application/octet-stream') {
           $fileContent = file_get_contents($filePath);
           $signatures = [
               "\x25\x50\x44\x46" => 'pdf',    // PDF
               "\x50\x4B\x03\x04" => 'zip',    // ZIP
               "\x52\x61\x72\x21" => 'rar',    // RAR
               "\xFF\xD8\xFF" => 'jpg',        // JPEG
               "\x89\x50\x4E\x47" => 'png',    // PNG
               "\x47\x49\x46\x38" => 'gif',    // GIF
               "\x42\x4D" => 'bmp',            // BMP
               "\x37\x7A\xBC\xAF" => '7z',     // 7Z
           ];

           foreach ($signatures as $signature => $ext) {
               if (strpos($fileContent, $signature) === 0) {
                   $this->insertIntoTesting("Detected file signature for: " . $ext);
                   return $ext;
               }
           }
       }

       // If still no match, try to extract extension from MIME type string
       if (preg_match('/application\/|image\/|text\/(.+)$/', $mimeType, $matches)) {
           $possibleExt = $matches[1];
           if (!empty($possibleExt) && $possibleExt !== 'octet-stream') {
               return strtolower($possibleExt);
           }
       }

       // Default to pdf if all else fails (since most documents are PDFs)
       $this->insertIntoTesting("Could not detect file type, defaulting to pdf for MIME type: " . $mimeType);
       return 'pdf';
   }

   /**
    * Download file using Laravel's HTTP Client with temp file
    */
   protected function downloadFileHttp($fileUrl, $savePath)
   {
       try {
           // First download to a temp file
           $tempFile = tempnam(sys_get_temp_dir(), 'download_');
           $response = Http::timeout(60)->get($fileUrl);
   
           if ($response->successful()) {
               File::put($tempFile, $response->body());
               
               // Get real file extension from content
               $realExtension = $this->getFileExtensionFromContent($tempFile);
               
               // Modify save path to use real extension
               $pathInfo = pathinfo($savePath);
               $newSavePath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $realExtension;
               
               // Move temp file to final destination with correct extension
               File::move($tempFile, $newSavePath);
               
               $this->insertIntoTesting("File downloaded and saved as: {$newSavePath} with detected type: {$realExtension}");
               return true;
           } else {
               $this->insertIntoTesting("HTTP request failed: {$fileUrl} (Status: " . $response->status() . ")");
               return false;
           }
       } catch (\Exception $e) {
           $this->insertIntoTesting("Exception in downloadFileHttp(): " . $e->getMessage());
           return false;
       } finally {
           // Clean up temp file if it exists
           if (isset($tempFile) && File::exists($tempFile)) {
               File::delete($tempFile);
           }
       }
   }

   function after163SessionDownloadDocumentBundle($api_id, $prog_id, $sess_id)
   {
       try {
           // 1. First check database for Google Drive files
           $documents = DB::table('admission_document')
               ->where('ad_api_id', $api_id)
               ->where('cms_prog_id', $prog_id)
               ->where('cms_sess_id', $sess_id)
               ->whereNotNull('ad_filelink')
               ->get();

           // 2. Folder path where documents will be stored
           $folderPath = public_path("docs/adm_docs/up_docs/{$api_id}");
   
           // Create directory if it doesn't exist
           if (!File::exists($folderPath)) {
               File::makeDirectory($folderPath, 0777, true);
           }

           // 3. Download files
           foreach ($documents as $doc) {
               // Check if file exists in docs/adm_docs/up_docs
               $existingFilePath = public_path("docs/adm_docs/up_docs/" . $doc->ad_filename);
               if (!empty($doc->ad_filename) && File::exists($existingFilePath)) {
                   // File already exists, no need to download
                   $this->insertIntoTesting("File already exists: {$doc->ad_filename}");
                   continue;
               }

               if (strpos($doc->ad_filelink, 'drive.google.com') !== false) {
                   $fileUrl = $this->convertGoogleDriveLink($doc->ad_filelink);
                   
                   // Create temp file for download
                   $tempFile = tempnam(sys_get_temp_dir(), 'download_');
                   
                   try {
                       // Download to temp file
                       $response = Http::timeout(60)->get($fileUrl);
                       if (!$response->successful()) {
                           $this->insertIntoTesting("Failed to download: {$fileUrl}");
                           continue;
                       }

                       // Save to temp file
                       File::put($tempFile, $response->body());

                       // Get real file extension from content
                       $realExtension = $this->getFileExtensionFromContent($tempFile);
                       
                       // Create final filename with correct extension
                       $newFileName = $doc->ad_type . '_' . time() . '.' . $realExtension;
                       $finalPath = "{$folderPath}/{$newFileName}";

                       // Move temp file to final destination
                       File::move($tempFile, $finalPath);
                       chmod($finalPath, 0777);

                       $this->insertIntoTesting("Successfully downloaded and processed: {$newFileName}");

                   } catch (\Exception $e) {
                       $this->insertIntoTesting("Error processing file: " . $e->getMessage());
                   } finally {
                       // Clean up temp file
                       if (File::exists($tempFile)) {
                           File::delete($tempFile);
                       }
                   }
               }
           }
   
           // 4. Make folder writeable
           @chmod($folderPath, 0777);
   
           // 5. ZIP file name and path
           $zipName = "Student_Documents_{$api_id}.zip";
           $zipPath = "{$folderPath}/{$zipName}";
   
           // 6. Delete old ZIP if exists
           if (File::exists($zipPath)) {
               File::delete($zipPath);
           }
   
           // 7. Create ZIP
           $command = "cd " . escapeshellarg($folderPath) . " && zip -r " . escapeshellarg($zipName) . " .";
           exec($command, $output, $status);
   
           if ($status !== 0) {
               return response()->json([
                   'message' => 'ZIP creation failed.',
                   'error' => implode("\n", $output),
                   'status' => false,
               ], 500);
           }
   
           // 8. Make ZIP writeable
           chmod($zipPath, 0777);
   
           // 9. Generate download URL
           $protocol = request()->isSecure() ? 'https' : 'http';
           $host = request()->getHost();
           $zipUrl = "{$protocol}://{$host}/docs/adm_docs/up_docs/{$api_id}/{$zipName}";
   
           return response()->json([
               'message' => 'Download bundle created successfully.',
               'status' => true,
               'url' => $zipUrl
           ]);
       } catch (\Exception $e) {
           $this->insertIntoTesting("Error in after163SessionDownloadDocumentBundle: " . $e->getMessage());
           return response()->json([
               'message' => 'An error occurred.',
               'error' => $e->getMessage(),
               'status' => false
           ], 500);
       }
   }
   
   /**
    * Insert logs into `testing` table for debugging
    */
   protected function insertIntoTesting($message)
   {
       DB::table('testing')->insert([
           'description' => $message,
           //'created_at' => now()
       ]);
   }
   
   /**
    * Get file extension from MIME type
    */
   protected function getExtensionFromMimeType($mimeType) {
       $mimeTypes = [
           'application/pdf' => '.pdf',
           'image/jpeg' => '.jpg',
           'image/png' => '.png',
           'image/gif' => '.gif',
           'application/msword' => '.doc',
           'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
           'application/vnd.ms-excel' => '.xls',
           'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '.xlsx',
           'text/plain' => '.txt'
       ];

       return $mimeTypes[$mimeType] ?? '.pdf';
   }
   
   
   
}
