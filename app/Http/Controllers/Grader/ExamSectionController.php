<?php

namespace App\Http\Controllers\Grader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExamSectionController extends Controller
{
    public function index()
    {
        try {
            $examSection = DB::table('exam_sections')
                ->orderBy('id', 'desc')
                ->get();
                
            return view('grader.exam.exam_sections', compact('examSection'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading exam sections: ' . $e->getMessage());
        }
    }

    public function manageExamSection(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'section_name' => 'required|string|max:3900',
                'section_description' => 'nullable|string|max:3900',
                'status' => 'required|in:0,1',
            ], [
                'section_name.required' => 'Section name is required',
                'section_name.max' => 'Section name cannot exceed 3900 characters',
                'section_description.max' => 'Section description cannot exceed 3900 characters',
                'status.required' => 'Status is required',
                'status.in' => 'Invalid status value'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $data = [
                'section_name' => trim($request->section_name),
                'secction_description' => $request->section_description ?? '', // Default to empty string if null
                'status' => $request->status ?? 1, // Default to 1 if null
                'entery_datetime' => now(),
                'entery_user_id' => auth()->id() ?? 1 // Fallback to 1 if not authenticated
            ];

            if ($request->filled('id')) {
                // Update
                DB::table('exam_sections')
                    ->where('id', $request->id)
                    ->update($data);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Section updated successfully'
                ]);
            } else {
                // Get max id and increment
                $maxId = DB::table('exam_sections')->max('id');
                $newId = ($maxId ?? 0) + 1;
                // Add id to data array
                $data['id'] = $newId;
                // Insert with auto-incremented id
                DB::table('exam_sections')->insert($data);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Section added successfully'
                ]);
            }
        } catch (\Exception $e) {
            // Log the full error for debugging
          
            // Check for specific Oracle error
            if (strpos($e->getMessage(), 'ORA-01400') !== false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please ensure all required fields are filled out properly.'
                ], 422);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request. ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteExamSection(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:exam_sections,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            DB::table('exam_sections')
                ->where('id', $request->id)
                ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Section deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateQuestionSection($id, $quizid)
    {
        try {
            DB::table('exam_question_paper')
                ->where('id', $quizid)
                ->update(['exam_section_id' => $id]);

            return response()->json([
                'status' => 'success',
                'message' => 'Question section updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}

