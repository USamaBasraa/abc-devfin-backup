<?php

namespace App\Http\Controllers\survey;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class surveyController extends Controller
{

    public function librarySurvey(Request $request)
    {

        // dd($request->all());
        $user_id = auth()->user()->username;
        
        // Handle POST request - submit survey
        if ($request->isMethod('post')) {
            $request->validate([
                'role' => 'required|string',
                'visitFrequency' => 'required|string',
                'reasons' => 'required|array',
                'satisfaction' => 'required|string',
                'examWeekExtend' => 'required|string',
                'regularExtension' => 'required|string',
                'weekendExtension' => 'required|string',
                'enrollment_no' => 'required|string',
            ]);
    
            // Define the questions array for mapping with full question text
            $questions = [
                'role' => 'Please select the category that best describes your role at BNU:',
                'visitFrequency' => 'How often have you visited the Library?',
                'reasons' => 'What are the reasons for your visit to the library? (Select all that apply)',
                'satisfaction' => 'Are you satisfied with the current library opening hours?',
                'examWeekExtend' => ' Do you want an extension in the library timing a week before the exam?',
                'regularExtension' => 'In case of regular extension, what times do you prefer?',
                'weekendExtension' => 'In case of extension on weekend hours, what time do you prefer?',
            ];
    
        
            try {
                // Insert new responses
                foreach ($questions as $field => $question) {
                    $value = $request->input($field);
                    // Handle array values (like reasons)
                    if ($field === 'reasons' && is_array($value)) {
                        $value = implode(', ', $value);
                    }
                    
                  

                    DB::table('student_feedbacks')->insert([
                        'sfd_title' => $question,
                        'sfd_desc' => $value,
                        'SAI_ENROLLMENT_NO' => $user_id,
                        'sf_type' => 'librarysurvey'
                    ]);
                }
                
                return response()->json(['message' => 'Library survey submitted successfully!']);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error submitting survey: ' . $e->getMessage()], 500);
            }
        }
    }
    
    public function librarysurveyPage()
    {
        $user_id = auth()->user()->username;
        // Check if the survey exists
        $exists = DB::table('student_feedbacks')
            ->where('SAI_ENROLLMENT_NO', $user_id)
            ->where('sf_type', 'librarysurvey')
            ->exists();
        
        // If survey exists, return message and EXIT
        if ($exists) {
            return response()->json(['exists' => true, 'message' => 'You have already submitted the library survey.']);
        }
    
        // Otherwise, allow loading the view
        return view('surveys.library-survey-page');
    }
    
    public function jobDescriptionForm()
    {
        
        $user_id = auth()->user()->username;
    
        // Check if the survey exists and is completed
        try {
            // First check if any record exists in main table
            $mainRecord = DB::table('hr_job_description_form')
                ->where('entry_username', auth()->user()->username)
                ->first();
                
            if ($mainRecord) {
                // Check if the form is completely finished (final submit done)
                $isFullyCompleted = !empty($mainRecord->is_completed) && $mainRecord->is_completed == 1;
                
                // If form is fully completed, user has already submitted
                if ($isFullyCompleted) {
                    return response()->json(['exists' => true, 'message' => 'You have already submitted the job description survey.']);
                }
            }
        } catch (\Exception $e) {
            // If there's any database error, log it and continue
        }

        // Get existing partial data if any
        $existingData = null;
        $sectionStatus = [
            'section_a' => false,
            'section_b' => false,
            'section_c' => false,
            'section_d' => false,
            'section_e' => false
        ];

        try {
            $existingData = DB::table('hr_job_description_form')
                ->where('entry_username', auth()->user()->username)
                ->first();
             

            if ($existingData) {
                // Check completion status based on data existence, not just is_completed flag
                $sectionStatus['section_a'] = !empty($existingData->job_title); // Section A is complete if basic data exists
             
                // Check Section B completion from main table
                $sectionStatus['section_b'] = !empty($existingData->role_description);
                
                // Check related tables - look for any data, not just completed status
                $sectionStatus['section_c'] = DB::table('hr_job_description_responsibilities')
                    ->where('job_description_id', $existingData->id)
                    // ->where('responsibility_type', '!=', 'role_description') // Exclude role description
                    ->exists(); // Check if any responsibilities exist
                // dd($sectionStatus);
                $sectionStatus['section_d'] = DB::table('hr_job_description_qualification_experience')
                    ->where('job_description_id', $existingData->id)
                    ->exists(); // Check if any qualifications exist
                
                $sectionStatus['section_e'] = DB::table('hr_job_description_interests')
                    ->where('job_description_id', $existingData->id)
                    ->exists(); // Check if any interests exist
            }
        } catch (\Exception $e) {
            // If there's any database error, log it and continue with empty data
            Log::error('Error getting existing data: ' . $e->getMessage());
            $existingData = null;
        }
    
        // Otherwise, allow loading the view with existing data
        return view('surveys.job-description-page', compact('existingData', 'sectionStatus'));
    }
    
    public function setSurveyFlag(Request $request)
    {
        // Set the session flag to indicate survey was closed
        Session::put('survey_closed', true);
        return response()->json([
            'success' => true,
            'message' => 'Survey flag set successfully'
        ]);
    }

    public function setJobDescriptionSurveyFlag(Request $request)
    {
        // Set the session flag to indicate survey was closed
        Session::put('job_description_survey_closed', true);
        return response()->json([
            'success' => true,
            'message' => 'Survey flag set successfully'
        ]);
    }

    /**
     * Save Section A: Basic Information
     */
    public function saveSectionA(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'job_title' => 'required|string|max:150',
                'hris_id' => 'required|string|regex:/^[0-9]+$/|max:150',
                'incumbent_name' => 'required|string|min:3|max:150',
                'supervisor_name' => 'required|string|min:3|max:150',
                'school' => 'nullable|string|max:150',
                'department' => 'nullable|string|max:150',
                'job_status' => 'required|in:Full-time,Part-time',
            ], [
                'job_title.required' => 'The job title is required.',
                'job_title.max' => 'The job title may not be greater than 150 characters.',
                'hris_id.required' => 'The HRIS ID is required.',
                'hris_id.regex' => 'The HRIS ID must contain only numbers.',
                'hris_id.max' => 'The HRIS ID may not be greater than 150 characters.',
                'incumbent_name.required' => 'The incumbent name is required.',
                'incumbent_name.min' => 'The incumbent name must be at least 3 characters.',
                'incumbent_name.max' => 'The incumbent name may not be greater than 150 characters.',
                'incumbent_name.regex' => 'The incumbent name may contain letters, spaces, dots, and common punctuation marks.',
                'supervisor_name.required' => 'The supervisor name is required.',
                'supervisor_name.min' => 'The supervisor name must be at least 3 characters.',
                'supervisor_name.max' => 'The supervisor name may not be greater than 150 characters.',
                'supervisor_name.regex' => 'The supervisor name may contain letters, spaces, dots, and common punctuation marks.',
                'school.max' => 'The school may not be greater than 150 characters.',
                'department.max' => 'The department may not be greater than 150 characters.',
                'job_status.required' => 'The job status is required.',
                'job_status.in' => 'The job status must be either Full-time or Part-time.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $username = strtolower(auth()->user()->username);

            DB::beginTransaction();
            try {
                // Check if record exists
                $existingRecord = DB::table('hr_job_description_form')
                    ->where('entry_username', $username)
                    ->first();

                if ($existingRecord) {
                    // Update existing record - only update Section A fields
                    DB::table('hr_job_description_form')
                        ->where('entry_username', $username)
                        ->update([
                            'job_title' => $request->job_title,
                            'hris_id' => $request->hris_id,
                            'incumbent_name' => $request->incumbent_name,
                            'supervisor_name' => $request->supervisor_name,
                            'school' => $request->school,
                            'department' => $request->department,
                            'job_status' => $request->job_status,
                            'is_completed' => 0, // Keep as incomplete since only Section A is done
                            'updated_at' => now(),
                        ]);
                    
                    $jobDescriptionId = $existingRecord->id;
                } else {
                    // Create new record with only Section A data
                    $jobDescriptionId = DB::table('hr_job_description_form')->insertGetId([
                        'entry_username' => $username,
                        'job_title' => $request->job_title,
                        'hris_id' => $request->hris_id,
                        'incumbent_name' => $request->incumbent_name,
                        'supervisor_name' => $request->supervisor_name,
                        'school' => $request->school,
                        'department' => $request->department,
                        'job_status' => $request->job_status,
                        'role_description' => '', // Set empty string since it can be NULL now
                        'is_completed' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Section A saved successfully!',
                    'job_description_id' => $jobDescriptionId
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving Section A: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save Section B: Role Description
     */
    public function saveSectionB(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'role_description' => 'required|string|max:3000',
            ], [
                'role_description.required' => 'The role description is required.',
                'role_description.max' => 'The role description may not be greater than 3000 characters.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $username = strtolower(auth()->user()->username);
            
            // Check if Section A exists (required before Section B)
            $mainRecord = DB::table('hr_job_description_form')
                ->where('entry_username', $username)
                ->first();

            if (!$mainRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please complete Section A first.'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Update the role_description in the main table
                DB::table('hr_job_description_form')
                    ->where('entry_username', $username)
                    ->update([
                        'role_description' => $request->role_description,
                        'updated_at' => now()
                    ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Section B saved successfully!'
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving Section B: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save Section C: Major Responsibilities
     */
    public function saveSectionC(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'responsibilities' => 'required|json',
                'responsibility_percentage' => 'required|json',
            ], [
                'responsibilities.required' => 'Responsibilities are required.',
                'responsibility_percentage.required' => 'Responsibility percentages are required.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $responsibilities = json_decode($request->responsibilities, true);
            $percentages = json_decode($request->responsibility_percentage, true);

            // Validate JSON data
            if (!is_array($responsibilities) || !is_array($percentages)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data format for responsibilities or percentages.'
                ], 422);
            }

            if (count($responsibilities) !== count($percentages)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Number of responsibilities and percentages must match.'
                ], 422);
            }

            // Validate minimum 7 responsibilities
            if (count($responsibilities) < 7) {
                return response()->json([
                    'success' => false,
                    'message' => 'At least 7 responsibilities are required.'
                ], 422);
            }

            // Validate each responsibility and percentage
            $totalPercentage = 0;
            $validResponsibilities = [];
            $validPercentages = [];

            foreach ($responsibilities as $index => $responsibility) {
                $percentage = $percentages[$index] ?? 0;

                // Validate responsibility
                if (empty(trim($responsibility))) {
                    return response()->json([
                        'success' => false,
                        'message' => "Responsibility #" . ($index + 1) . " cannot be empty."
                    ], 422);
                }

                if (strlen($responsibility) > 3000) {
                    return response()->json([
                        'success' => false,
                        'message' => "Responsibility #" . ($index + 1) . " cannot exceed 3000 characters."
                    ], 422);
                }

                // Validate percentage
                if (!is_numeric($percentage) || $percentage < 1 || $percentage > 100) {
                    return response()->json([
                        'success' => false,
                        'message' => "Percentage for responsibility #" . ($index + 1) . " must be between 1 and 100."
                    ], 422);
                }

                $totalPercentage += (int)$percentage;
                $validResponsibilities[] = trim($responsibility);
                $validPercentages[] = (int)$percentage;
            }

            // Validate total percentage equals 100
            if ($totalPercentage !== 100) {
                return response()->json([
                    'success' => false,
                    'message' => "Total percentage must equal 100%. Current total: {$totalPercentage}%"
                ], 422);
            }

            $username = strtolower(auth()->user()->username);
            
            // Check if Section A exists (required before Section C)
            $existingRecord = DB::table('hr_job_description_form')
                ->where('entry_username', $username)
                ->first();

            if (!$existingRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please complete Section A first.'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Delete existing responsibilities
                DB::table('hr_job_description_responsibilities')
                    ->where('job_description_id', $existingRecord->id)
                    ->delete();

                // Insert new responsibilities
                foreach ($validResponsibilities as $index => $responsibility) {
                    DB::table('hr_job_description_responsibilities')->insert([
                        'job_description_id' => $existingRecord->id,
                        'responsibility' => $responsibility,
                        'percentage' => $validPercentages[$index],
                        'is_completed' => 0, // Section C is saved but not final completed
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Section C saved successfully!',
                    'job_description_id' => $existingRecord->id
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Section C Save Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving Section C: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save Section D: Job Specifications
     */
    public function saveSectionD(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'qualification' => 'required|json',
                'experience' => 'required|json',
            ], [
                'qualification.required' => 'Qualifications are required.',
                'experience.required' => 'Experience details are required.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $qualifications = json_decode($request->qualification, true);
            $experiences = json_decode($request->experience, true);

            // Validate JSON data
            if (!is_array($qualifications) || !is_array($experiences)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data format for qualifications or experience.'
                ], 422);
            }

            if (count($qualifications) !== count($experiences)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Number of qualifications and experiences must match.'
                ], 422);
            }

            // Validate at least one qualification/experience pair
            if (count($qualifications) < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'At least one qualification and experience entry is required.'
                ], 422);
            }

            // Validate each qualification and experience
            $validQualifications = [];
            $validExperiences = [];

            foreach ($qualifications as $index => $qualification) {
                $experience = $experiences[$index] ?? 0;

                // Validate qualification
                if (empty(trim($qualification))) {
                    return response()->json([
                        'success' => false,
                        'message' => "Qualification #" . ($index + 1) . " cannot be empty."
                    ], 422);
                }

                if (strlen($qualification) > 3000) {
                    return response()->json([
                        'success' => false,
                        'message' => "Qualification #" . ($index + 1) . " cannot exceed 3000 characters."
                    ], 422);
                }

                // Validate experience
                if (!is_numeric($experience) || $experience < 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Experience for qualification #" . ($index + 1) . " must be a positive number."
                    ], 422);
                }

                $validQualifications[] = trim($qualification);
                $validExperiences[] = (int)$experience;
            }

            $username = strtolower(auth()->user()->username);
            
            // Check if Section A exists (required before Section D)
            $existingRecord = DB::table('hr_job_description_form')
                ->where('entry_username', $username)
                ->first();

            if (!$existingRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please complete Section A first.'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Delete existing qualifications/experience
                DB::table('hr_job_description_qualification_experience')
                    ->where('job_description_id', $existingRecord->id)
                    ->delete();

                // Insert new qualifications/experience
                foreach ($validQualifications as $index => $qualification) {
                    if (!empty($qualification) && !empty($validExperiences[$index])) {
                        DB::table('hr_job_description_qualification_experience')->insert([
                            'job_description_id' => $existingRecord->id,
                            'qualification' => $qualification,
                            'experience' => $validExperiences[$index],
                            'is_completed' => 0, // Section D is saved but not final completed
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Section D saved successfully!',
                    'job_description_id' => $existingRecord->id
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Section D Save Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving Section D: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save Section E: Multidisciplinary Interests (Final Submission)
     */
    public function saveSectionE(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'multi_interest' => 'required|json',
                'required_skills' => 'required|json',
            ], [
                'multi_interest.required' => 'Multidisciplinary interests are required.',
                'required_skills.required' => 'Required skills are required.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $interests = json_decode($request->multi_interest, true);
            $skills = json_decode($request->required_skills, true);

            // Validate JSON data
            if (!is_array($interests) || !is_array($skills)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data format for interests or skills.'
                ], 422);
            }

            if (count($interests) !== count($skills)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Number of interests and skills must match.'
                ], 422);
            }

            // Validate at least one interest/skills pair
            if (count($interests) < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'At least one multidisciplinary interest and skills entry is required.'
                ], 422);
            }

            // Validate each interest and skills
            $validInterests = [];
            $validSkills = [];

            foreach ($interests as $index => $interest) {
                $skill = $skills[$index] ?? '';

                // Validate interest
                if (empty(trim($interest))) {
                    return response()->json([
                        'success' => false,
                        'message' => "Multidisciplinary interest #" . ($index + 1) . " cannot be empty."
                    ], 422);
                }

                if (strlen($interest) > 3000) {
                    return response()->json([
                        'success' => false,
                        'message' => "Multidisciplinary interest #" . ($index + 1) . " cannot exceed 3000 characters."
                    ], 422);
                }

                // Validate skills
                if (empty(trim($skill))) {
                    return response()->json([
                        'success' => false,
                        'message' => "Required skills for interest #" . ($index + 1) . " cannot be empty."
                    ], 422);
                }

                if (strlen($skill) > 3000) {
                    return response()->json([
                        'success' => false,
                        'message' => "Required skills for interest #" . ($index + 1) . " cannot exceed 3000 characters."
                    ], 422);
                }

                $validInterests[] = trim($interest);
                $validSkills[] = trim($skill);
            }

            $username = strtolower(auth()->user()->username);
            
            // Check if Section A exists (required before Section E)
            $existingRecord = DB::table('hr_job_description_form')
                ->where('entry_username', $username)
                ->first();

            if (!$existingRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please complete Section A first.'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Delete existing interests
                DB::table('hr_job_description_interests')
                    ->where('job_description_id', $existingRecord->id)
                    ->delete();

                // Insert new interests
                foreach ($validInterests as $index => $interest) {
                    if (!empty($interest) && !empty($validSkills[$index])) {
                        DB::table('hr_job_description_interests')->insert([
                            'job_description_id' => $existingRecord->id,
                            'interest_area' => $interest, // Changed from 'multi_interest' to 'interest_area'
                            'required_skills' => $validSkills[$index],
                            'is_completed' => 1, // Section E is final completed
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }

                // Mark all sections as completed in main table
                DB::table('hr_job_description_form')
                    ->where('id', $existingRecord->id)
                    ->update([
                        'is_completed' => 1, // Final completion
                        'updated_at' => now()
                    ]);

                // Mark all other sections as completed
                DB::table('hr_job_description_responsibilities')
                    ->where('job_description_id', $existingRecord->id)
                    ->update(['is_completed' => 1]);

                DB::table('hr_job_description_qualification_experience')
                    ->where('job_description_id', $existingRecord->id)
                    ->update(['is_completed' => 1]);

                DB::commit();

            
                return response()->json([
                    'success' => true,
                    'message' => 'Job Description Form completed successfully!',
                    'job_description_id' => $existingRecord->id,
                    'redirect_url' => url('/') // Use url() helper for the redirect URL
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Section E Save Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving Section E: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get saved data for a specific section
     */
    public function getSectionData(Request $request, $section)
    {
        try {
            $username = strtolower(auth()->user()->username);
            
            $mainRecord = DB::table('hr_job_description_form')
                ->where('entry_username', $username)
                ->first();

            if (!$mainRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'No saved data found.'
                ]);
            }

            $data = [];

            switch ($section) {
                case 'a':
                    // Get only Section A data from main table
                    $data = [
                        'job_title' => $mainRecord->job_title,
                        'hris_id' => $mainRecord->hris_id,
                        'incumbent_name' => $mainRecord->incumbent_name,
                        'supervisor_name' => $mainRecord->supervisor_name,
                        'school' => $mainRecord->school,
                        'department' => $mainRecord->department,
                        'job_status' => $mainRecord->job_status,
                    ];
                    break;
                case 'b':
                    // Get Section B data from main table
                    $data = [
                        'role_description' => $mainRecord->role_description,
                    ];
                    break;
                case 'c':
                    $responsibilities = DB::table('hr_job_description_responsibilities')
                        ->where('job_description_id', $mainRecord->id)
                        ->get();
                    $data = [
                        'responsibilities' => $responsibilities->pluck('responsibility')->toArray(),
                        'responsibility_percentage' => $responsibilities->pluck('percentage')->toArray(),
                    ];
                    break;

                case 'd':
                    // Get data from hr_job_description_qualification_experience table
                    $qualifications = DB::table('hr_job_description_qualification_experience')
                        ->where('job_description_id', $mainRecord->id)
                        ->get();
                    
                    // Debug: Log what we found
                    \Log::info('Section D data retrieval for user: ' . $username . ', job_description_id: ' . $mainRecord->id);
                    \Log::info('Qualifications found: ' . $qualifications->count());
                    
                    $data = [
                        'qualification' => $qualifications->pluck('qualification')->toArray(),
                        'experience' => $qualifications->pluck('experience')->toArray(),
                    ];
                    break;

                case 'e':
                    $interests = DB::table('hr_job_description_interests')
                        ->where('job_description_id', $mainRecord->id)
                        ->get();
                    $data = [
                        'multi_interest' => $interests->pluck('interest_area')->toArray(), // Changed from 'multi_interest' to 'interest_area'
                        'required_skills' => $interests->pluck('required_skills')->toArray(),
                    ];
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid section specified.'
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Get Section Data Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving section data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store job description survey data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeJobDescription(Request $request)
    {
       
        // Remove dd for production
        // dd($request->all());
        try {
            $validator = Validator::make($request->all(), [
                'job_title' => 'required|string|max:200|regex:/^[A-Za-z0-9\s\-]+$/',
                'employee_code' => 'required|string|max:20|regex:/^[A-Za-z0-9\-]+$/',
                'incumbent_name' => 'required|string|max:200|regex:/^[A-Za-z\s\.]+$/',
                'supervisor_name' => 'required|string|max:200|regex:/^[A-Za-z\s\.]+$/',
                'role_description' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        // Use character count instead of word count
                        $charCount = strlen($value);
                        if ($charCount > 3000) {
                            $fail('The role description must not exceed 3000 characters.');
                        }
                    },
                ],
                'responsibilities' => 'required|array|min:7',
                'responsibilities.*' => [
                    'required',
                    'string',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        $charCount = strlen($value);
                        if ($charCount > 3000) {
                            $fail('Each responsibility must not exceed 3000 characters.');
                        }
                    },
                ],
                'responsibility_percentage' => [
                    'required',
                    'array',
                    'min:7',
                    function ($attribute, $value, $fail) {
                        $total = array_sum($value);
                        if ($total !== 100) {
                            $fail('The total percentage must equal 100%.');
                        }
                    },
                ],
                'responsibility_percentage.*' => 'required|integer|min:1|max:100',
                'qualification' => 'required|array|min:1',
                'qualification.*' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        $charCount = strlen($value);
                        if ($charCount > 3000) {
                            $fail('Each qualification must not exceed 3000 characters.');
                        }
                    },
                ],
                'hris_id' => 'required|integer',
                'experience' => 'required|array|min:1',
                'experience.*' => 'required|integer|min:0',
                'multi_interest' => 'required|array|min:1',
                'multi_interest.*' => [
                    'required',
                    'string',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        $charCount = strlen($value);
                        if ($charCount > 3000) {
                            $fail('Each area of interest must not exceed 3000 characters.');
                        }
                    },
                ],
                'required_skills' => 'required|array|min:1',
                'required_skills.*' => [
                    'required',
                    'string',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        $charCount = strlen($value);
                        if ($charCount > 3000) {
                            $fail('Each required skill must not exceed 3000 characters.');
                        }
                    },
                ],
            ], [
                'job_title.required' => 'The job title is required.',
                'hris_id.required' => 'The HRIS ID is required.',
                'job_title.regex' => 'The job title may only contain letters, numbers, spaces and hyphens.',
                'employee_code.required' => 'The employee code is required.',
                'employee_code.regex' => 'The employee code may only contain letters, numbers and hyphens.',
                'incumbent_name.required' => 'The incumbent name is required.',
                'incumbent_name.regex' => 'The incumbent name may only contain letters, spaces and dots.',
                'supervisor_name.required' => 'The supervisor name is required.',
                'supervisor_name.regex' => 'The supervisor name may only contain letters, spaces and dots.',
                'role_description.required' => 'The role description is required.',
                'responsibilities.required' => 'Please provide at least 7 responsibilities.',
                'responsibilities.min' => 'Please provide at least 7 responsibilities.',
                'responsibilities.*.required' => 'Each responsibility field is required.',
                'responsibilities.*.min' => 'Each responsibility must be at least 10 characters.',
                'responsibility_percentage.required' => 'Please provide percentages for each responsibility.',
                'responsibility_percentage.min' => 'Please provide percentages for at least 7 responsibilities.',
                'responsibility_percentage.*.required' => 'Each responsibility percentage is required.',
                'responsibility_percentage.*.integer' => 'Each responsibility percentage must be a whole number.',
                'responsibility_percentage.*.min' => 'Each responsibility percentage must be at least 1%.',
                'qualification.required' => 'Please provide at least one qualification.',
                'qualification.*.required' => 'Each qualification field is required.',
                'experience.required' => 'Please provide at least one experience entry.',
                'experience.*.required' => 'Each experience field is required.',
                'experience.*.integer' => 'Experience must be a whole number.',
                'experience.*.min' => 'Experience cannot be negative.',
                'multi_interest.required' => 'Please provide at least one area of interest.',
                'multi_interest.*.required' => 'Each area of interest is required.',
                'multi_interest.*.min' => 'Each area of interest must be at least 1 characters.',
                'required_skills.required' => 'Please provide required skills for each area of interest.',
                'required_skills.*.required' => 'Required skills field is required.',
                'required_skills.*.min' => 'Required skills must be at least 1 characters.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction(); // 🔐 Begin Transaction

            // Save main form
            $jobDescriptionId = DB::table('hr_job_description_form')->insertGetId([
                'job_title' => $request->job_title,
                'employee_code' => $request->employee_code,
                'hris_id' => $request->hris_id,
                'school' => $request->school,
                'incumbent_name' => $request->incumbent_name,
                'department' => $request->department,
                'supervisor_name' => $request->supervisor_name,
                'job_status' => $request->job_status,
                'role_description' => $request->role_description,
                'entry_username' => strtolower(auth()->user()->username),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            // Store responsibilities
            foreach ($request->responsibilities as $i => $responsibility) {
                if (!empty($responsibility)) {
                    DB::table('hr_job_description_responsibilities')->insert([
                        'job_description_id' => $jobDescriptionId,
                        'responsibility' => $responsibility,
                        'percentage' => $request->responsibility_percentage[$i] ?? 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            // Store qualifications and experience
            foreach ($request->qualification as $i => $qualification) {
                if (!empty($qualification)) {
                    DB::table('hr_job_description_qualification_experience')->insert([
                        'job_description_id' => $jobDescriptionId,
                        'qualification' => $qualification,
                        'experience' => $request->experience[$i],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            // Store multidisciplinary interests
            foreach ($request->multi_interest as $i => $interest) {
                if (!empty($interest)) {
                    DB::table('hr_job_description_interests')->insert([
                        'job_description_id' => $jobDescriptionId,
                        'multi_interest' => $interest,
                        'required_skills' => $request->required_skills[$i],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit(); // 🔐 Commit Transaction

            return response()->json([
                'success' => true,
                'message' => 'Job description submitted successfully!',
                'redirect_url' => '/'
            ]);

        } catch (\Exception $e) {
            DB::rollBack(); // 🔐 Rollback Transaction
            
            // Create more detailed error message
            $errorDetails = "Error: " . $e->getMessage() . "\n" . 
                "File: " . $e->getFile() . "\n" .
                "Line: " . $e->getLine() . "\n" .
                "IP Address: " . request()->ip() . "\n" .
                "User: " . auth()->user()->username . "\n" .
                "Request Data: " . json_encode(array_map(function($item) {
                    // Truncate long string values to prevent massive logs
                    if (is_string($item) && strlen($item) > 200) {
                        return substr($item, 0, 200) . '... [truncated]';
                    }
                    return $item;
                }, $request->except(['_token'])));
            
            // Log to database using makeErrorLog function
            $logStatus = $this->makeErrorLog('JOB_DESCRIPTION_ERROR', $errorDetails);
        
            // Generate reference ID for support
            $refId = date('YmdHis') . '_' . auth()->user()->username;
            
            // Log to Laravel's error log too
            Log::error('Job Description Form Error: ' . $e->getMessage() . ' | Ref: ' . $refId);
            
            return response()->json([
                'success' => false,
                'message' => 'Form submit mein error aaya hai. Technical team ko notice bhej di gayi hai.',
                'reference_id' => $refId,
                'log_status' => $logStatus ? 'Logged to DB' : 'Logged to file only',
                'error_details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
 /**
     * Store error logs in database
     *
     * @param  string  $errorType    Type/category of error
     * @param  string  $errorMessage Detailed error message
     * @return boolean               Success status of logging operation
     */
    public function makeErrorLog($errorType, $errorMessage)
    {
        // Create a simple formatted error log with basic info
        $completeLog = "ERROR TYPE: " . $errorType . "\n" .
            "DATE: " . now()->format('Y-m-d H:i:s') . "\n" .
            "USER: " . (auth()->check() ? auth()->user()->username : 'guest') . "\n" .
            "DETAILS: " . $errorMessage;
        
        // Insert using a direct query to avoid any ORM issues
        try {
            DB::insert('insert into testing (description) values (?)', [$completeLog]);
            // Also log to Laravel's error log for backup
  
            return true;
        } catch (\Exception $e) {
            // If database logging fails, at least log to Laravel's error log

            return false;
        }
    }
}
