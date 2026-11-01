<?php

namespace App\Http\Controllers\survey;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class StaffSurveyController extends Controller
{
    private const GRIEVANCE_MAX_FILES = 10;

    private const GRIEVANCE_FILE_MIMES = 'pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,webp,mp4,mov,csv';

    private const GRIEVANCE_FILE_DELIMITER = '|';

    private const GRIEVANCE_ENCODED_FILE_NAMES_MAX = 200;

    private const GRIEVANCE_ADMIN_REMARKS_MAX = 3900;

    /* =========================
       STAFF SATISFACTION SURVEY
    ==========================*/

    public function staffSatisfaction()
    {
        $username = Auth::user()->username;

        $sql = "
            SELECT count(*) as total
            FROM student_feedbacks
            WHERE lower(SAI_ENROLLMENT_NO) = lower(?)
            AND sfd_title = 'Staff Satisfaction Survey 2026'
        ";

        $responses = DB::select($sql, [$username]);

        return view('surveys.staff-satisfaction-survey', [
            'record' => $responses[0]
        ]);
    }

    public function staffHealthInsuranceSurvey()
    {
        return view('surveys.staff-health-insurance-survey');
    }

    public function setStaffHealthInsuranceSurveyFlag(Request $request)
    {
        Session::put('staff_health_insurance_survey_closed', true);

        return response()->json([
            'success' => true,
            'message' => 'Survey flag set successfully'
        ]);
    }

    /* =========================
       STAFF HEALTH INSURANCE
    ==========================*/

    public function submitStaffHealthInsuranceSurvey(Request $request)
    {
        $username = auth()->user()->username;

        if ($request->isMethod('post')) {

            $input = $request->all();

            $rules = [
                'dob_emp' => 'required|date_format:d/m/Y',
                'is_interested' => 'required',
            ];

            $is_interested = $input['is_interested'];
            if ($is_interested == '1') $is_interested = 'Yes';
            if ($is_interested == '0') $is_interested = 'No';

            if ($is_interested === 'Yes') {
                $rules['contribution_amount'] = 'required';
                $rules['payment_frequency'] = 'required';

                for ($i = 1; $i <= 9; $i++) {
                    $rules["dob_child_$i"] = 'nullable|date_format:d/m/Y';
                    $rules["gender_child_$i"] = 'nullable|string|in:Male,Female';
                }

                $rules['dob_spouse'] = 'nullable|date_format:d/m/Y';
            }

            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            try {
                /* Prevent duplicate submission */
                $alreadySubmitted = DB::table('student_feedbacks')
                    ->where('SAI_ENROLLMENT_NO', $username)
                    ->where('sf_type', 'staff_health_insurance_survey')
                    ->exists();

                if ($alreadySubmitted) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You have already submitted this survey.'
                    ], 400);
                }

                /* -----------------------------
                   DATA FOR staff_health_insurance
                ------------------------------*/
                $data = [
                    'entry_username' => $username,
                    'dob_emp' => Carbon::createFromFormat('d/m/Y', $input['dob_emp'])->format('Y-m-d'),
                    'is_interested' => $is_interested,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                if ($is_interested === 'Yes') {
                    if (isset($input['dob_spouse'])) {
                        $data['dob_spouse'] = Carbon::createFromFormat('d/m/Y', $input['dob_spouse'])->format('Y-m-d');
                    } else {
                        $data['dob_spouse'] = null;
                    }

                    $data['no_of_children'] = $input['no_of_children'] ?? 0;

                    for ($i = 1; $i <= 9; $i++) {
                        if (isset($input["dob_child_$i"])) {
                            $data["dob_child_$i"] = Carbon::createFromFormat('d/m/Y', $input["dob_child_$i"])->format('Y-m-d');
                        } else {
                            $data["dob_child_$i"] = null;
                        }
                    }

                    $data['contribution_amount'] = $input['contribution_amount'] ?? null;
                    if ($data['contribution_amount'] === 'Other') {
                        $data['contribution_amount'] = $input['other_contribution_amount'] ?? null;
                    }
                    $data['payment_frequency'] = $input['payment_frequency'] ?? null;
                }

                // DB::table('staff_health_insurance')->insert($data);

                /* -----------------------------
                   INSERT INTO student_feedbacks
                ------------------------------*/
                $feedbackPayload = [
                    'dob_emp' => Carbon::createFromFormat('d/m/Y', $input['dob_emp'])->format('Y-m-d'),
                    'is_interested' => $is_interested,
                    'dob_spouse' => $data['dob_spouse'] ?? null,
                    'no_of_children' => $data['no_of_children'] ?? 0,
                    'contribution_amount' => $data['contribution_amount'] ?? null,
                    'payment_frequency' => $data['payment_frequency'] ?? null,
                ];

                for ($i = 1; $i <= 9; $i++) {
                    $feedbackPayload["dob_child_$i"] = $data["dob_child_$i"] ?? null;
                    $feedbackPayload["gender_child_$i"] = $input["gender_child_$i"] ?? null;
                }

                DB::table('student_feedbacks')->insert([
                'sfd_title' => 'Staff Health Insurance Survey 2026',
                'sfd_desc' => json_encode($feedbackPayload),
                'SAI_ENROLLMENT_NO' => $username,
                'sf_type' => 'staff_health_insurance_survey'
]);


                return response()->json([
                    'status' => 'success',
                    'message' => 'Staff Health Insurance Survey submitted successfully!'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error submitting survey: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid request'
        ], 400);
    }

    /* =========================
       STAFF SATISFACTION SURVEY
    ==========================*/

    public function setStaffSatisfactionSurveyFlag(Request $request)
    {
        Session::put('staff_satisfaction_survey_closed', true);

        return response()->json([
            'success' => true,
            'message' => 'Survey flag set successfully'
        ]);
    }

    public function submitStaffSatisfactionSurvey(Request $request)
    {
        $user_id = strtoupper(auth()->user()->username);

        $request->validate([
            'culture_satisfaction' => 'required|integer|min:1|max:5',
            'innovation_support' => 'required|integer|min:1|max:5',
            'professional_autonomy' => 'required|integer|min:1|max:5',
            'decision_transparency' => 'required|integer|min:1|max:5',
            'leadership_communication' => 'required|integer|min:1|max:5',
            'academic_input' => 'required|integer|min:1|max:5',
            'workload_satisfaction' => 'required|integer|min:1|max:5',
            'additional_workload' => 'required|integer|min:1|max:5',
            'professional_development' => 'required|integer|min:1|max:5',
            'salary_structure' => 'required|integer|min:1|max:5',
            'compensation_workload' => 'required|integer|min:1|max:5',
            'workplace_quality' => 'required|integer|min:1|max:5',
            'it_support' => 'required|integer|min:1|max:5',
            'office_responsiveness' => 'required|integer|min:1|max:5',
            'campus_environment' => 'required|integer|min:1|max:5',
            'overall_satisfaction' => 'required|integer|min:1|max:5',
            'belonging_sense' => 'required|integer|min:1|max:5',
            'recommend_likelihood' => 'required|integer|min:1|max:5',
            'key_strengths' => 'required|string|max:1500',
            'improvement_areas' => 'required|string|max:1500',
        ]);

        $exists = DB::table('student_feedbacks')
            ->where('SAI_ENROLLMENT_NO', $user_id)
            ->where('sf_type', 'staff_satisfaction_survey')
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already submitted this survey.'
            ], 400);
        }

        DB::table('student_feedbacks')->insert([
            'sfd_title' => 'Staff Satisfaction Survey 2026',
            'sfd_desc' => json_encode($request->except(['_token'])),
            'SAI_ENROLLMENT_NO' => $user_id,
            'sf_type' => 'staff_satisfaction_survey'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Staff Satisfaction Survey submitted successfully!'
        ]);
    }
    /**
     * Display list of user's grievance / harassment reports.
     */
    public function index(Request $request)
    {
        $username = strtoupper(auth()->user()->username);

        $requests = $this->withGrievanceAdminRemarksHistory(
            DB::table('laptop_maintenance_requests')
                ->where('enrollment_no', $username)
                ->whereIn('request_type', ['harassment', 'grievance'])
                ->orderBy('created_at', 'DESC')
                ->get()
        );

        return view('surveys.staff-harassment-list', ['requests' => $requests]);
    }

    /**
     * Show grievance form (new or revise after admin marks "revised").
     */
    public function create(Request $request)
    {
        $data = [];

        if ($request->filled('id')) {
            $username = strtoupper(auth()->user()->username);
            $requestData = DB::table('laptop_maintenance_requests')
                ->where('id', $request->id)
                ->where('enrollment_no', $username)
                ->whereIn('request_type', ['harassment', 'grievance'])
                ->first();

            if (!$requestData || strtolower($requestData->status ?? '') !== 'revised') {
                return $this->redirectToStaffGrievanceList()
                    ->with('error', 'Request not found or cannot be edited at this time.');
            }

            $data = [
                'id' => $requestData->id,
                'request_type' => $requestData->request_type,
                'problem' => $requestData->problem,
                'description' => $requestData->description,
                'file_names' => $this->grievanceFileNamesFromRecord($requestData),
                'admin_remarks_history' => $this->parseGrievanceAdminRemarksHistory($requestData),
            ];
        }

        return view('surveys.staff-harassment-form', compact('data'));
    }

    /**
     * Store new grievance or resubmit after admin revision request.
     */
    public function store(Request $request)
    {
        $newUploadedFiles = [];

        try {
            if ($request->input('id') === '' || $request->input('id') === null) {
                $request->merge(['id' => null]);
            }

            $validator = Validator::make($request->all(), [
                'id' => 'nullable|integer',
                'category' => 'required|string|in:harassment,grievance',
                'subject' => 'required|string|max:255',
                'description' => 'required|string|max:3000',
                'remove_all_files' => 'nullable|in:0,1',
                'remove_files' => 'nullable|array',
                'remove_files.*' => 'string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $fileValidationError = $this->validateGrievanceUploadedFiles($request);
            if ($fileValidationError !== null) {
                return response()->json([
                    'success' => false,
                    'message' => $fileValidationError,
                ], 422);
            }

            $description = trim((string) $request->description);
            $subject = trim((string) $request->subject);

            if ($description === '' || $subject === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Subject and description are required.',
                ], 422);
            }

            $username = strtoupper(auth()->user()->username);
            $displayName = $username;
            $isResubmit = $request->filled('id');
            $removeAllFiles = $request->input('remove_all_files') === '1' || $request->boolean('remove_all_files');
            $newUploadCount = $this->countValidGrievanceUploadFiles($request);
            $newUploadedFiles = [];

            $payload = [
                'problem' => $subject,
                'description' => $description,
                'request_type' => $request->get('category'),
                'student_name' => $displayName,
                'enrollment_no' => $username,
                'submitted_by' => $displayName,
                'status' => 'Pending',
                'updated_at' => now(),
            ];

            $resubmitRequestId = null;

            if (!$isResubmit) {
                if ($newUploadCount > self::GRIEVANCE_MAX_FILES) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You can upload a maximum of ' . self::GRIEVANCE_MAX_FILES . ' files.',
                    ], 422);
                }

                $newUploadedFiles = $this->uploadGrievanceFiles($request, $newUploadCount);

                if (count($newUploadedFiles) > self::GRIEVANCE_MAX_FILES) {
                    $this->deleteGrievanceAttachmentFiles($newUploadedFiles);

                    return response()->json([
                        'success' => false,
                        'message' => 'You can upload a maximum of ' . self::GRIEVANCE_MAX_FILES . ' files.',
                    ], 422);
                }

                $encodedFiles = $this->encodeGrievanceFileNames($newUploadedFiles);
                if ($encodedFiles !== null && strlen($encodedFiles) > self::GRIEVANCE_ENCODED_FILE_NAMES_MAX) {
                    $this->deleteGrievanceAttachmentFiles($newUploadedFiles);

                    return response()->json([
                        'success' => false,
                        'message' => 'Attached file names exceed the maximum storage length. Upload fewer files.',
                    ], 422);
                }

                if ($encodedFiles !== null) {
                    $payload['FILE_NAME'] = $encodedFiles;
                }

                $payload['created_at'] = now();
            } else {
                $existingRequest = DB::table('laptop_maintenance_requests')
                    ->where('id', $request->id)
                    ->where('enrollment_no', $username)
                    ->whereIn('request_type', ['harassment', 'grievance'])
                    ->first();

                if (!$existingRequest) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Request not found or you do not have permission to update it.',
                    ], 404);
                }

                if (strtolower($existingRequest->status ?? '') !== 'revised') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Only reports marked for revision can be resubmitted.',
                    ], 403);
                }

                $keptFileCount = $this->countGrievanceFilesAfterRemovals(
                    $existingRequest,
                    $removeAllFiles,
                    $request->input('remove_files', [])
                );
                $totalFileCountAfterSave = min(self::GRIEVANCE_MAX_FILES, $keptFileCount + $newUploadCount);

                if ($totalFileCountAfterSave > self::GRIEVANCE_MAX_FILES) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maximum ' . self::GRIEVANCE_MAX_FILES . ' evidence files allowed per report.',
                    ], 422);
                }

                $newUploadedFiles = $this->uploadGrievanceFiles($request, $totalFileCountAfterSave > 0 ? $totalFileCountAfterSave : $newUploadCount);

                $payload['resolved_by'] = null;
                $payload['completed_at'] = null;
                $payload['status'] = 'Pending';

                $finalFiles = $this->resolveGrievanceFilesForResubmit(
                    $existingRequest,
                    $newUploadedFiles,
                    $removeAllFiles,
                    $request->input('remove_files', [])
                );

                if (count($finalFiles) > self::GRIEVANCE_MAX_FILES) {
                    $this->deleteGrievanceAttachmentFiles($newUploadedFiles);

                    return response()->json([
                        'success' => false,
                        'message' => 'Maximum ' . self::GRIEVANCE_MAX_FILES . ' evidence files allowed per report.',
                    ], 422);
                }

                $encodedFiles = $this->encodeGrievanceFileNames($finalFiles);
                if ($encodedFiles !== null && strlen($encodedFiles) > self::GRIEVANCE_ENCODED_FILE_NAMES_MAX) {
                    $this->deleteGrievanceAttachmentFiles($newUploadedFiles);

                    return response()->json([
                        'success' => false,
                        'message' => 'Attached file names exceed the maximum storage length. Remove some files and try again.',
                    ], 422);
                }

                if ($encodedFiles !== null) {
                    // dd($encodedFiles);
                    $payload['FILE_NAME'] = $encodedFiles;
                } else {
                    $payload['FILE_NAME'] = null;
                }

                $resubmitRequestId = $request->id;
            }

            DB::transaction(function () use ($isResubmit, $payload, $resubmitRequestId, $request) {
                if (!$isResubmit) {
                    DB::table('laptop_maintenance_requests')->insert($payload);
                } else {
                    DB::table('laptop_maintenance_requests')
                        ->where('id', $resubmitRequestId)
                        ->update($payload);
                }

                $to = 'irfan.bajwa@bnu.edu.pk';
                $emailSubject = 'Grievance Complaint Report';
                $emailDescription = 'Grievance/Harassment Complaint Report:' . $request->get('description');
                $this->send_email_to_one($to, $emailSubject, $emailDescription);
            });

            return response()->json([
                'success' => true,
                'message' => $isResubmit
                    ? 'Your revised report has been resubmitted successfully. Status is now Pending.'
                    : 'Your grievance report has been submitted successfully. Our team will review it soon.',
                'redirect' => route('staff-harassment-form'),
            ]);
        } catch (\Exception $e) {
            if (!empty($newUploadedFiles)) {
                $this->deleteGrievanceAttachmentFiles($newUploadedFiles);
            }

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your request. Please try again later.',
            ], 500);
        }
    }

     public function send_email_to_one($to, $subject, $message)
    {
        $procedurenotification = 'send_mail_one_by_one';
        $bindings_notification = [
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
            'p_sender_display_name' => 'Grievance Complaint Report',
        ];
        // dd($bindings_notification);
        try {
            DB::executeProcedure($procedurenotification, $bindings_notification);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Failed to send notification email.', 0, $e);
        }
    }


    /**
     * Display the specified laptop maintenance request details
     */
    public function show($id)
    {
        try {
            $username = strtoupper(auth()->user()->username);

            // Fetch request details - ensure it belongs to current user
            $request = DB::table('student_feedbacks')
                ->where('id', $id)
                ->where('SAI_ENROLLMENT_NO', $username)
                ->first();

            if(!$request) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found or you do not have permission to view it.'
                ], 404);
            }

            // Format dates
            $request->created_at = \Carbon\Carbon::parse($request->created_at)->format('d M Y, h:i A');
            $request->updated_at = \Carbon\Carbon::parse($request->updated_at)->format('d M Y, h:i A');

            return response()->json([
                'success' => true,
                'data' => $request
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching request details.'
            ], 500);
        }
    }
    public function harassmentIndex()
    {
        $requests = $this->withGrievanceAdminRemarksHistory(
            DB::table('laptop_maintenance_requests')
                ->whereIn('request_type', ['harassment', 'grievance'])
                ->orderBy('id', 'DESC')
                ->get()
        );

        return view('laptop_maintenance.harassment_index', compact('requests'));
    }

    /**
     * Mark harassment request as complete (Admin).
     */
    public function harassmentMarkComplete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|integer',
            'remarks' => 'required|string|max:' . self::GRIEVANCE_ADMIN_REMARKS_MAX,
            'status_action' => 'required|in:completed,revised',
        ]);

        if ($validator->fails()) {
            return $this->redirectToHarassmentAdmin()
                ->withInput()
                ->withErrors($validator);
        }

        $remarks = trim((string) $request->remarks);
        if ($remarks === '') {
            return $this->redirectToHarassmentAdmin()
                ->withInput()
                ->withErrors(['remarks' => 'Admin remarks are required.']);
        }

        $statusAction = strtolower($request->status_action);
        $requestData = DB::table('laptop_maintenance_requests')
            ->where('id', $request->request_id)
            ->whereIn('request_type', ['harassment', 'grievance'])
            ->first();

        if (!$requestData) {
            return $this->redirectToHarassmentAdmin()->with('error', 'Request not found.');
        }

        $currentStatus = strtolower($requestData->status ?? '');

        if ($currentStatus === 'completed') {
            return $this->redirectToHarassmentAdmin()->with('error', 'This request is already completed.');
        }

        if ($currentStatus === 'revised' && $statusAction !== 'completed') {
            return $this->redirectToHarassmentAdmin()->with('error', 'Revised requests can only be marked as completed.');
        }

        $resolvedBy = strtoupper(Auth::user()->username ?? Auth::user()->name ?? 'SYSTEM');

        $updatePayload = [
            'status' => $statusAction,
            'resolver_remarks' => $this->appendGrievanceAdminRemark($requestData, $remarks, $statusAction, $resolvedBy),
            'resolved_by' => $resolvedBy,
            'updated_at' => now(),
        ];

        if ($statusAction === 'completed') {
            $updatePayload['completed_at'] = now();
        }
        // dd($updatePayload);
        DB::table('laptop_maintenance_requests')
            ->where('id', $request->request_id)
            ->update($updatePayload);
        
        $to = strtolower($requestData->enrollment_no).'@bnu.edu.pk';
        $subject = 'Grievance Complaint Report';
        $description = 'Please check your grievance report status by logging in to your CMS portal.';
        $this->send_email_to_one($to, $subject, $description);

        $message = $statusAction === 'revised'
            ? 'Request marked as revised successfully.'
            : 'Request marked as completed successfully.';

        return $this->redirectToHarassmentAdmin()->with('success', $message);
    }

    /**
     * Attachment names from DB (single / JSON / pipe-separated).
     */
    private function grievanceFileNamesFromRecord($record): array
    {
        if (!$record) {
            return [];
        }

        $name = $record->file_name ?? $record->FILE_NAME ?? null;
        if ($name === null && is_object($record)) {
            foreach ((array) $record as $key => $value) {
                if (strtolower((string) $key) === 'file_name' && $value !== null && $value !== '') {
                    $name = $value;
                    break;
                }
            }
        }

        $raw = trim((string) ($name ?? ''));
        if ($raw === '') {
            return [];
        }

        if ($raw[0] === '[') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $files = [];
                foreach ($decoded as $item) {
                    $normalized = $this->normalizeGrievanceFileName((string) $item);
                    if ($normalized !== '') {
                        $files[] = $normalized;
                    }
                }

                return array_values(array_unique($files));
            }
        }

        if (strpos($raw, self::GRIEVANCE_FILE_DELIMITER) !== false) {
            $files = [];
            foreach (explode(self::GRIEVANCE_FILE_DELIMITER, $raw) as $item) {
                $normalized = $this->normalizeGrievanceFileName((string) $item);
                if ($normalized !== '') {
                    $files[] = $normalized;
                }
            }

            return array_values(array_unique($files));
        }

        $single = $this->normalizeGrievanceFileName($raw);

        return $single !== '' ? [$single] : [];
    }

    /**
     * Store file list in FILE_NAME column (max 200 characters total).
     */
    private function encodeGrievanceFileNames(array $files): ?string
    {
        $maxLength = self::GRIEVANCE_ENCODED_FILE_NAMES_MAX;
        $delimiter = self::GRIEVANCE_FILE_DELIMITER;

        $files = array_values(array_unique(array_filter(array_map(function ($file) {
            $normalized = $this->normalizeGrievanceFileName((string) $file);

            return $normalized !== '' ? $normalized : null;
        }, $files))));

        if ($files === []) {
            return null;
        }

        if (count($files) === 1) {
            return $this->truncateGrievanceFileName($files[0], $maxLength);
        }

        return implode($delimiter, $files);
    }

    /**
     * Max characters allowed per stored file name for a given attachment count.
     */
    private function grievancePerFileNameMaxLength(int $totalFileCount): int
    {
        if ($totalFileCount < 1) {
            return self::GRIEVANCE_ENCODED_FILE_NAMES_MAX;
        }

        $delimiterLength = strlen(self::GRIEVANCE_FILE_DELIMITER);

        return max(
            20,
            (int) floor((self::GRIEVANCE_ENCODED_FILE_NAMES_MAX - ($delimiterLength * max(0, $totalFileCount - 1))) / $totalFileCount)
        );
    }

    /**
     * Count grievance files that remain after user-selected removals (resubmit).
     */
    private function countGrievanceFilesAfterRemovals($existingRequest, bool $removeAll, $removeFilesInput): int
    {
        if ($removeAll) {
            return 0;
        }

        $existingFiles = $this->grievanceFileNamesFromRecord($existingRequest);
        $removeFiles = [];
        if (is_array($removeFilesInput)) {
            foreach ($removeFilesInput as $file) {
                $normalized = $this->normalizeGrievanceFileName((string) $file);
                if ($normalized !== '') {
                    $removeFiles[] = $normalized;
                }
            }
        }
        $removeFiles = array_unique($removeFiles);

        $keptCount = 0;
        foreach ($existingFiles as $file) {
            if (!in_array($file, $removeFiles, true)) {
                $keptCount++;
            }
        }

        return $keptCount;
    }

    /**
     * Count valid grievance files in the current request upload.
     */
    private function countValidGrievanceUploadFiles(Request $request): int
    {
        if (!$request->hasFile('file')) {
            return 0;
        }

        $files = $request->file('file');
        if (!is_array($files)) {
            $files = [$files];
        }

        $count = 0;
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Shorten a stored grievance file name while keeping the extension when possible.
     */
    private function truncateGrievanceFileName(string $fileName, int $maxLength = self::GRIEVANCE_ENCODED_FILE_NAMES_MAX): string
    {
        $fileName = $this->normalizeGrievanceFileName($fileName);
        if ($fileName === '' || strlen($fileName) <= $maxLength) {
            return $fileName;
        }

        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        if ($extension !== '') {
            $extensionWithDot = '.' . $extension;
            $baseMaxLength = $maxLength - strlen($extensionWithDot);
            if ($baseMaxLength < 1) {
                return substr($fileName, 0, $maxLength);
            }

            $baseName = pathinfo($fileName, PATHINFO_FILENAME);

            return substr($baseName, 0, $baseMaxLength) . $extensionWithDot;
        }

        return substr($fileName, 0, $maxLength);
    }

    /**
     * Validate uploaded grievance files (supports file[] multiple uploads).
     */
    private function validateGrievanceUploadedFiles(Request $request): ?string
    {
        if (!$request->hasFile('file')) {
            return null;
        }

        $files = $request->file('file');
        if (!is_array($files)) {
            $files = [$files];
        }

        $validFiles = [];
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $validFiles[] = $file;
            }
        }

        if ($validFiles === []) {
            return null;
        }

        if (count($validFiles) > self::GRIEVANCE_MAX_FILES) {
            return 'You can upload a maximum of ' . self::GRIEVANCE_MAX_FILES . ' files.';
        }

        $allowedMimes = explode(',', self::GRIEVANCE_FILE_MIMES);
        foreach ($validFiles as $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, $allowedMimes, true)) {
                return 'Invalid file type. Allowed: ' . str_replace(',', ', ', self::GRIEVANCE_FILE_MIMES) . '.';
            }

            if ($file->getSize() > 30720 * 1024) {
                return 'Each file must be 30 MB or smaller.';
            }
        }

        return null;
    }

    /**
     * Upload multiple grievance files from request.
     *
     * @param  int|null  $totalFileCountAfterSave  Total attachments after save (for short names on resubmit).
     */
    private function uploadGrievanceFiles(Request $request, ?int $totalFileCountAfterSave = null): array
    {
        if (!$request->hasFile('file')) {
            return [];
        }

        $uploaded = [];
        $files = $request->file('file');
        if (!is_array($files)) {
            $files = [$files];
        }

        $destination = public_path('assets' . DIRECTORY_SEPARATOR . 'docs');
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $validFiles = [];
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $validFiles[] = $file;
            }
        }

        $fileCount = $totalFileCountAfterSave ?? count($validFiles);
        $perFileMax = $this->grievancePerFileNameMaxLength($fileCount);

        foreach ($validFiles as $file) {
            $originalBase = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = strtolower($file->getClientOriginalExtension());
            $slug = Str::slug($originalBase, '_') ?: 'document';
            if (strlen($slug) > 40) {
                $slug = substr($slug, 0, 40);
            }
            $storedName = $slug . '_' . time() . '_' . Str::random(6) . ($extension ? '.' . $extension : '');
            $storedName = $this->truncateGrievanceFileName($storedName, $perFileMax);

            $file->move($destination, $storedName);
            $uploaded[] = $storedName;
        }

        return $uploaded;
    }

    /**
     * Merge kept, removed, and newly uploaded files on resubmit.
     */
    private function resolveGrievanceFilesForResubmit($existingRequest, array $newUploadedFiles, bool $removeAll, $removeFilesInput): array
    {
        $existingFiles = $this->grievanceFileNamesFromRecord($existingRequest);

        if ($removeAll) {
            $this->deleteGrievanceAttachmentFiles($existingFiles);

            return $newUploadedFiles;
        }

        $removeFiles = [];
        if (is_array($removeFilesInput)) {
            foreach ($removeFilesInput as $file) {
                $normalized = $this->normalizeGrievanceFileName((string) $file);
                if ($normalized !== '') {
                    $removeFiles[] = $normalized;
                }
            }
        }

        $removeFiles = array_unique($removeFiles);

        $keptFiles = [];
        foreach ($existingFiles as $file) {
            if (in_array($file, $removeFiles, true)) {
                $this->deleteGrievanceAttachmentFile($file);
                continue;
            }
            $keptFiles[] = $file;
        }

        return array_values(array_unique(array_merge($keptFiles, $newUploadedFiles)));
    }

    /**
     * Strip duplicated assets/docs prefix from stored file name.
     */
    private function normalizeGrievanceFileName(string $fileName): string
    {
        $fileName = trim(str_replace('\\', '/', $fileName));
        if ($fileName === '') {
            return '';
        }

        return ltrim(preg_replace('#^/?assets/docs/#i', '', $fileName), '/');
    }

    /**
     * Delete physical grievance attachment from public/assets/docs.
     */
    private function deleteGrievanceAttachmentFile(string $fileName): void
    {
        $fileName = $this->normalizeGrievanceFileName($fileName);
        if ($fileName === '') {
            return;
        }

        $fullPath = public_path('assets/docs/' . $fileName);
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    /**
     * Delete multiple grievance attachments.
     */
    private function deleteGrievanceAttachmentFiles(array $fileNames): void
    {
        foreach ($fileNames as $fileName) {
            $this->deleteGrievanceAttachmentFile((string) $fileName);
        }
    }

    /**
     * Parse admin remarks history from resolver_remarks (JSON or legacy plain text).
     */
    private function parseGrievanceAdminRemarksHistory($record): array
    {
        if (!$record) {
            return [];
        }

        $raw = trim((string) ($record->resolver_remarks ?? ''));
        if ($raw === '') {
            return [];
        }

        if ($raw[0] === '{') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && isset($decoded['history']) && is_array($decoded['history'])) {
                $history = [];
                foreach ($decoded['history'] as $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    $remarkText = trim((string) ($item['remarks'] ?? ''));
                    if ($remarkText === '') {
                        continue;
                    }
                    $history[] = [
                        'remarks' => $remarkText,
                        'action' => !empty($item['action']) ? strtolower((string) $item['action']) : null,
                        'by' => $item['by'] ?? null,
                        'at' => $item['at'] ?? null,
                    ];
                }

                return $history;
            }
        }

        return [[
            'remarks' => $raw,
            'action' => null,
            'by' => $record->resolved_by ?? null,
            'at' => $record->updated_at ?? ($record->created_at ?? null),
        ]];
    }

    /**
     * Encode remarks history for storage in resolver_remarks.
     */
    private function encodeGrievanceAdminRemarksHistory(array $history): string
    {
        return json_encode(['history' => array_values($history)], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Append a new admin remark entry to existing history.
     */
    private function appendGrievanceAdminRemark($record, string $remarks, string $action, string $by): string
    {
        $history = $this->parseGrievanceAdminRemarksHistory($record);
        $history[] = [
            'remarks' => $remarks,
            'action' => strtolower($action),
            'by' => $by,
            'at' => now()->format('Y-m-d H:i:s'),
        ];

        return $this->encodeGrievanceAdminRemarksHistory($history);
    }

    /**
     * Attach parsed remarks history to each request row for views.
     */
    private function withGrievanceAdminRemarksHistory($requests)
    {
        return $requests->map(function ($request) {
            $request->admin_remarks_history = $this->parseGrievanceAdminRemarksHistory($request);

            return $request;
        });
    }

    /**
     * Admin grievance list (never redirect()->back() — avoids file-download URL).
     */
    private function redirectToHarassmentAdmin()
    {
        return redirect()->route('staff-harassment.admin');
    }

    /**
     * Staff grievance list.
     */
    private function redirectToStaffGrievanceList()
    {
        return redirect()->route('staff-harassment-form');
    }


    public function downloadFile($id, Request $request)
    {
        $requestData = DB::table('laptop_maintenance_requests')
            ->where('id', $id)
            ->whereIn('request_type', ['harassment', 'grievance'])
            ->first();

        if (!$requestData) {
            abort(404);
        }

        $files = $this->grievanceFileNamesFromRecord($requestData);
        if ($files === []) {
            abort(404);
        }

        $requestedFile = $this->normalizeGrievanceFileName((string) $request->query('file', ''));
        $fileName = $requestedFile !== '' && in_array($requestedFile, $files, true)
            ? $requestedFile
            : $files[0];

        $filePath = public_path('assets/docs/' . $fileName);
        if (!file_exists($filePath)) {
            abort(404);
        }

        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $downloadName = 'request_' . $requestData->id . '_' . $requestData->created_at . '.' . $extension;

        return response()->download($filePath, $downloadName);
    }
}
