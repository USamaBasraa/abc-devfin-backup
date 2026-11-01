<?php

namespace App\Http\Controllers\Grader\Hostel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Exception;

/**
 * HostelManagementController
 * 
 * This controller handles Hostel Type and Floor management operations.
 * - Add new hostel types and floors
 * - Update existing hostel types and floors
 * - Simple and clean code for easy understanding
 */
class HostelManagementController extends Controller
{
    /**
     * Show the hostel type and floor management page
     * 
     * This method displays the form where admin can add/update
     * hostel types and floors.
     */
    public function index()
    {
        $hostelTypes = DB::table('HOSTEL_TYPE')
            ->whereIn('HT_STATUS', ['E', 'D'])
            ->select('HT_ID', 'HT_NAME', 'HT_LOCATION', 'HT_HOSTEL_TYPE', 'HT_STATUS')
            ->orderBy('HT_STATUS', 'desc')
            ->orderBy('HT_NAME', 'asc')
            ->get();

        $floors = DB::table('HOSTEL_FLOOR')
            ->whereIn('HF_STATUS', ['E', 'D'])
            ->orderBy('HF_STATUS', 'desc')
            ->orderBy('HF_NAME', 'asc')
            ->get();

        return view('Hostel.hostel_type_floor_management', compact('hostelTypes', 'floors'));
    }

    /**
     * Add or Update Hostel Type
     * 
     * This method handles both adding new hostel types and updating existing ones.
     * - If ht_id is provided, it updates the existing record
     * - If ht_id is not provided, it creates a new record
     */
    public function saveHostelType(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'ht_name' => 'required|string|max:255',
                'ht_location' => 'required|in:on_campus,off_campus,out_of_campus',
                'ht_hostel_type' => 'required|in:1,2',
                'ht_status' => 'nullable|in:E,D',
            ]);

            $htId = $request->input('ht_id');
            $htName = $request->input('ht_name');
            $htLocation = $request->input('ht_location');
            $htHostelType = $request->input('ht_hostel_type');
            $htStatus = $request->input('ht_status', 'E');

            DB::beginTransaction();

            if ($htId) {
                // UPDATE existing hostel type
                DB::table('HOSTEL_TYPE')
                    ->where('HT_ID', $htId)
                    ->update([
                        'HT_NAME' => $htName,
                        'HT_LOCATION' => $htLocation,
                        'HT_HOSTEL_TYPE' => $htHostelType,
                        'HT_STATUS' => $htStatus,
                    ]);

                $message = 'Hostel Type updated successfully!';
            } else {
                // INSERT new hostel type
                DB::table('HOSTEL_TYPE')->insert([
                    'HT_NAME' => $htName,
                    'HT_LOCATION' => $htLocation,
                    'HT_HOSTEL_TYPE' => $htHostelType,
                    'HT_STATUS' => $htStatus,
                    'HT_ENTRY_DATE_TIME' => now(),
                    'HT_ENTRY_USER_ID' => auth()->id()
                ]);

                $message = 'Hostel Type added successfully!';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add or Update Floor
     * 
     * This method handles both adding new floors and updating existing ones.
     * - If hf_id is provided, it updates the existing record
     * - If hf_id is not provided, it creates a new record
     */
    public function saveFloor(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'hf_name' => 'required|string|max:255',
                'ht_id' => 'required|integer|exists:HOSTEL_TYPE,HT_ID',
                'hf_status' => 'nullable|in:E,D',
            ]);

            $hfId = $request->input('hf_id');
            $hfName = $request->input('hf_name');
            $htId = $request->input('ht_id');
            $hfNewHostelType = $htId;
            $hfStatus = $request->input('hf_status', 'E');

            // Fetch HT_HOSTEL_TYPE (gender: 1 = Boys, 2 = Girls) from selected hostel type
            $selectedHostelType = DB::table('HOSTEL_TYPE')
                ->where('ht_id', $htId)
                ->select('ht_hostel_type')
                ->first();

            if (!$selectedHostelType || is_null($selectedHostelType->ht_hostel_type)) {
                throw new Exception("Could not determine gender for the selected Hostel Type (HT_ID: {$htId}). Please ensure HT_HOSTEL_TYPE is set in HOSTEL_TYPE table.");
            }

            $hfHostelType = $selectedHostelType->ht_hostel_type; // 1 = Boys, 2 = Girls

            DB::beginTransaction();

            if ($hfId) {
                // UPDATE existing floor
                DB::table('HOSTEL_FLOOR')
                    ->where('HF_ID', $hfId)
                    ->update([
                        'HF_NAME' => $hfName,
                        'HF_HOSTEL_TYPE' => $hfHostelType,
                        'HF_NEW_HOSTEL_TYPE' => $hfNewHostelType,
                        'HF_STATUS' => $hfStatus,
                    ]);

                $message = 'Floor updated successfully!';
            } else {
                // INSERT new floor
                DB::table('HOSTEL_FLOOR')->insert([
                    'HF_NAME' => $hfName,
                    'HF_HOSTEL_TYPE' => $hfHostelType,
                    'HF_NEW_HOSTEL_TYPE' => $hfNewHostelType,
                    'HF_STATUS' => $hfStatus,
                    'HF_ENTRY_DATE_TIME' => now(),
                    'HF_ENTRY_USER_ID' => auth()->id()
                ]);

                $message = 'Floor added successfully!';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation Error: ' . json_encode($e->errors())
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Hostel Type by ID
     * 
     * This method returns hostel type data for editing
     */
    public function getHostelType($id)
    {
        try {
            $hostelType = DB::table('HOSTEL_TYPE')
                ->where('HT_ID', $id)
                ->first();

            if (!$hostelType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hostel Type not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $hostelType
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Floor by ID
     * 
     * This method returns floor data for editing
     */
    public function getFloor($id)
    {
        try {
            $floor = DB::table('HOSTEL_FLOOR')
                ->where('HF_ID', $id)
                ->first();

            if (!$floor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Floor not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $floor
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleHostelTypeStatus($id)
    {
        try {
            $hostelType = DB::table('HOSTEL_TYPE')->where('HT_ID', $id)->first();

            if (!$hostelType) {
                return response()->json(['success' => false, 'message' => 'Hostel Type not found'], 404);
            }

            $newStatus = $hostelType->ht_status === 'E' ? 'D' : 'E';

            DB::table('HOSTEL_TYPE')
                ->where('HT_ID', $id)
                ->update(['HT_STATUS' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => $newStatus === 'E' ? 'Hostel Type enabled successfully.' : 'Hostel Type disabled successfully.',
                'status' => $newStatus,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function toggleFloorStatus($id)
    {
        try {
            $floor = DB::table('HOSTEL_FLOOR')->where('HF_ID', $id)->first();

            if (!$floor) {
                return response()->json(['success' => false, 'message' => 'Floor not found'], 404);
            }

            $newStatus = $floor->hf_status === 'E' ? 'D' : 'E';

            DB::table('HOSTEL_FLOOR')
                ->where('HF_ID', $id)
                ->update(['HF_STATUS' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => $newStatus === 'E' ? 'Floor enabled successfully.' : 'Floor disabled successfully.',
                'status' => $newStatus,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function deleteHostelType($id)
    {
        try {
            $floorCount = DB::table('HOSTEL_FLOOR')
                ->where('HF_NEW_HOSTEL_TYPE', $id)
                ->whereIn('HF_STATUS', ['E', 'D'])
                ->count();

            $roomCount = DB::table('hostel_rooms')
                ->where('hr_ht_id', $id)
                ->where('hr_status', 'E')
                ->count();

            if ($floorCount > 0 || $roomCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete: this hostel type has linked floors or rooms. Disable it instead.',
                ], 422);
            }

            $deleted = DB::table('HOSTEL_TYPE')->where('HT_ID', $id)->delete();

            if (!$deleted) {
                return response()->json(['success' => false, 'message' => 'Hostel Type not found'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Hostel Type deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function deleteFloor($id)
    {
        try {
            $roomCount = DB::table('hostel_rooms')
                ->where('hr_hf_id', $id)
                ->where('hr_status', 'E')
                ->count();

            if ($roomCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete: this floor has linked rooms. Disable it instead.',
                ], 422);
            }

            $deleted = DB::table('HOSTEL_FLOOR')->where('HF_ID', $id)->delete();

            if (!$deleted) {
                return response()->json(['success' => false, 'message' => 'Floor not found'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Floor deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}

