<?php
/**

 * User: Arsalan Ahmed Sulahri
 * Date: 26/07/2024
 * Time: 9:52 AM
 */

namespace App\Http\Controllers\Grader\Hostel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Table;
use DB;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
Class HostelController{
    Public function showHostelApplicants(){
        // $session = DB::table('academic_sessions')
        // ->select('SESS_ID', 'SESS_NAME')
        // ->max('SESS_ID');
        $session = DB::table('academic_sessions')
        ->select('SESS_ID', 'SESS_NAME')
        ->orderBy('SESS_ID', 'desc')
        ->first();
        // dd($session->sess_id);

        $male_vacant_seats = DB::table('hostel_beds')
        ->join('hostel_rooms', 'hostel_beds.hb_room_id', '=', 'hostel_rooms.hr_id')
        ->where('hostel_rooms.hr_ht_id', 1)
        ->where('hostel_beds.hb_is_occupied', 'N')
        ->where('hostel_rooms.hr_status', 'E')
        ->count('hostel_beds.hb_bed_number');
        $female_vacant_seats = DB::table('hostel_beds')
        ->join('hostel_rooms', 'hostel_beds.hb_room_id', '=', 'hostel_rooms.hr_id')
        ->where('hostel_rooms.hr_ht_id', 2)
        ->where('hostel_beds.hb_is_occupied', 'N')
        ->where('hostel_rooms.hr_status', 'E')
        ->count('hostel_beds.hb_bed_number');



        //sorted list of aplicants which are early apply and less proratize of lahore
        $applicants = DB::table('admission_apply_info')
        ->select([
            'api_allow_hostel',
            'aai_id',
            'CMS_SAI_ENROLLMENT_NO',
            'admission_personal_info.API_NAME',
            'admission_personal_info.API_GENDER',
            'admission_personal_info.API_PERMANENTADDRESS',
            'admission_personal_info.api_permanentcity',
            'admission_apply_info.aai_apply_date',
            'admission_personal_info.API_gender',
            'admission_personal_info.API_ishostel',
            'admission_apply_info.aai_sdpo_sess_id',
        ])
        ->join('admission_personal_info', 'admission_apply_info.AAI_API_FORM_ID', '=', 'admission_personal_info.API_FORMID')
        // ->where('admission_apply_info.AAI_STATUS', 'Selected')
        ->where('admission_personal_info.API_ISHOSTEL', 'Yes')
        ->where('admission_apply_info.AAI_SDPO_SESS_ID', $session->sess_id)
        ->orderByRaw("CASE WHEN LOWER(admission_personal_info.API_PERMANENTCITY) = 'lahore' THEN 1 ELSE 0 END")
        ->orderBy('admission_apply_info.aai_apply_date')
        ->get();

        // $applicants = DB::table('sisusers')
        //     ->select([
        //         'spi_allow_hostel',
        //         'sisusers.username',
        //         'students_personal_info.SPI_NAME',
        //         'students_personal_info.SPI_GENDER',
        //         'students_personal_info.SPI_PERMANENTADDRESS',
        //         'students_personal_info.SPI_PRESENTCITY',
        //         'students_apply_info.sai_apply_date',
        //         'students_personal_info.spi_gender',
        //         'students_personal_info.spi_ishostel',
        //         'students_apply_info.sai_sdpo_sess_id',
        //         DB::raw("'new' as student_status"),
        //     ])
        //     ->leftJoin('students_apply_info', DB::raw('LOWER(sisusers.username)'), '=', DB::raw('LOWER(students_apply_info.sai_enrollment_no)'))
        //     ->leftJoin('students_personal_info', 'students_personal_info.spi_id', '=', 'students_apply_info.sai_spi_id')
        //     ->where('students_apply_info.sai_sdpo_sess_id', $session->sess_id)
        //     ->where('students_personal_info.spi_ishostel', 'Yes')
        //     ->get();
           // dd($applicants);

           $maleApplicants = $applicants->filter(function ($applicant) {
            return $applicant->api_gender == 'Male';
        });

        $femaleApplicants = $applicants->filter(function ($applicant) {
            return $applicant->api_gender == 'Female';
        });



// dd($new_applicants);
// for old students

$old_applicants = DB::table('hostel_applicants')
                        ->select([
                            'happ_allowed_status',
                            'happ_enrollment_no',
                            'students_personal_info.SPI_NAME',
                            'students_personal_info.SPI_GENDER',
                            'students_personal_info.SPI_PERMANENTADDRESS',
                            'students_personal_info.spi_permanentcity',
                            // 'students_apply_info.sai_apply_date',
                            'happ_entry_date as sai_apply_date',
                            'students_personal_info.spi_gender',
                            'students_personal_info.spi_ishostel',
                            'students_apply_info.sai_sdpo_sess_id',
                            DB::raw("'old' as student_status"),
                        ])
                        ->leftJoin('students_apply_info', DB::raw('LOWER(happ_enrollment_no)'), '=', DB::raw('LOWER(students_apply_info.sai_enrollment_no)'))
                        ->leftJoin('students_personal_info', 'students_personal_info.spi_id', '=', 'students_apply_info.sai_spi_id')
                        // ->where('students_apply_info.sai_sdpo_sess_id', $session->sess_id)
                        // ->where('students_personal_info.spi_ishostel', 'Yes')
                        ->get();



// dd($old_applicants);

            $old_maleApplicants = $old_applicants->filter(function ($old_applicant) {
                return $old_applicant->spi_gender == 'Male';
            });

            $old_femaleApplicants = $old_applicants->filter(function ($old_applicant) {
                return $old_applicant->spi_gender == 'Female';
            });




        return view('Hostel.hostel_applicants', [
            'maleApplicants' => $maleApplicants,
            'femaleApplicants' => $femaleApplicants,
            'maleCount' => $maleApplicants->count(),
            'femaleCount' => $femaleApplicants->count(),
            'session'=>$session->sess_name,
            'male_vacant_seats'=> $male_vacant_seats,
            'female_vacant_seats'=>$female_vacant_seats,
            //for old aplicants
            'old_maleApplicants' => $old_maleApplicants,
            'old_femaleApplicants' => $old_femaleApplicants,
            'old_maleCount' => $old_maleApplicants->count(),
            'old_femaleCount' => $old_femaleApplicants->count(),
            'new_count'=>  $applicants->count(),
            'old_count'=>  $old_applicants->count()

        ]);
    }
    public function allowOldApplicant($enrollmentNo)
    {


        DB::beginTransaction();

        try {
            // Define the query with parameter binding
            $query = "UPDATE hostel_applicants
                SET happ_allowed_status = 'Allowed'
                WHERE LOWER(happ_enrollment_no) = LOWER('$enrollmentNo')
            ";
            // dd($query,$enrollmentNo);
            // Execute the query with parameter binding
            DB::update($query );

            // Commit the transaction if successful
            DB::commit();

            $session = DB::table('academic_sessions')
            ->select('SESS_ID', 'SESS_NAME')
            ->orderBy('SESS_ID', 'desc')
            ->first();
            // dd(auth()->user());
           $userId = strtolower(auth()->user()->id);


             DB::table('hostel_assign')->insert([
                'ha_enrolment_no' => $enrollmentNo,
                'ha_session' => $session->sess_id,
                'ha_status' => 'E',
                'ha_entry_user_id' => $userId
                // You can remove 'happ_entry_user_id' and 'happ_entry_date' if they are not needed or manage them accordingly.
            ]);






            return redirect()->back()->with('success', 'Hostel applicant status updated successfully');
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();

            // Log the error message (optional)
            Log::error('Failed to update hostel applicant status: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to update hostel applicant status');
        }

    }
    public function allowNewApplicant($enrollmentNo)
    {
        // dd(auth()->user());
        DB::beginTransaction(); // Start the transaction
        $aaiApiId = DB::table('admission_apply_info')
                ->where('aai_id', $enrollmentNo)
                ->value('aai_api_id');



        try {
            // Define the query with parameter binding
            $query = "
            UPDATE admission_personal_info
            SET api_allow_hostel = 'Allowed'
            WHERE api_id = :aaiApiId
            ";
    // dd($query,$enrollmentNo);
            // Execute the query with parameter binding
            DB::update($query, ['aaiApiId' => $aaiApiId]);
            $session = DB::table('academic_sessions')
            ->select('SESS_ID', 'SESS_NAME')
            ->orderBy('SESS_ID', 'desc')
            ->first();

            $userId = strtolower(auth()->user()->id);
             DB::table('hostel_assign')->insert([
                'ha_aai_id' => $enrollmentNo,
                'ha_session' => $session->sess_id,
                'ha_status' => 'E',
                'ha_entry_user_id' => $userId
                // You can remove 'happ_entry_user_id' and 'happ_entry_date' if they are not needed or manage them accordingly.
            ]);
            DB::commit(); // Commit the transaction if successful

            return redirect()->back()->with('success', 'Applicant allowed successfully');
        } catch (Exception $e) {
            DB::rollBack(); // Rollback the transaction on error

            // Log the error message (optional)
            Log::error('Failed to allow applicant: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to allow applicant');
        }

}


public function vacateHostel(){
    $username = strtolower(auth()->user()->username);
    $hostel_type = DB::table('HOSTEL_TYPE')
    ->where('HT_STATUS', 'E')
    ->get();
    $floors_de = DB::table('HOSTEL_FLOOR')
            ->where('HF_STATUS', 'E')
            ->get();
          // Fetch all rooms
          $data = DB::table('hostel_rooms')
        //   ->where('HR_HT_ID',$condition)
          ->get();
        //   dd($data);
          // Fetch all beds
          $beds = DB::table('hostel_beds')->get();
        //   dd($beds);
          // Add beds to the corresponding rooms
          foreach ($data as $room) {
            $room->beds = $beds->where('hb_room_id', $room->hr_id)->values();
        }

        $rooms = json_decode($data , true);
        $floors =   json_decode($floors_de , true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $rooms ;
    } else {
        // Handle JSON decode error
        return response()->json(['error' => 'Invalid JSON data'], 400);
    }
    // dd($beds);
    //dd( $room);
    return view('Hostel.vacate_bed',
    compact('floors_de',
     'rooms',
     'hostel_type',


    ));
}


public function addRoom(Request $request)
{
    $request->validate([
        'room_number' => 'required|string|max:255',
        'room_capacity' => 'required|integer|min:1',
        'hostel_type' => 'required|integer|exists:hostel_type,ht_id',
        'floor' => 'required|integer|exists:hostel_floor,hf_id'
    ]);

    try {
        DB::beginTransaction();

        if ($request->filled('room_id')) {
            // UPDATE existing room
            $roomId = $request->input('room_id');

            DB::table('HOSTEL_ROOMS')
                ->where('HR_ID', $roomId)
                ->update([
                    'HR_HT_ID' => $request->input('hostel_type'),
                    'HR_ROOM_NO' => $request->input('room_number'),
                    'HR_CAPACITY' => $request->input('room_capacity'),
                    'HR_HF_ID' => $request->input('floor'),
                    'HR_AC_AVAILABILITY' => strtoupper($request->input('ac_availability')),
                    'HR_HEATER_AVAILABILITY' => strtoupper($request->input('heater_availability')),
                    'HR_REFRIGERATOR_AVAILABILITY' => strtoupper($request->input('refrigerator_availability')),
                ]);

            // Adjust Beds
            $currentBedCount = DB::table('HOSTEL_BEDS')
                ->where('HB_ROOM_ID', $roomId)
                ->count();

            $newCapacity = (int) $request->input('room_capacity');

            if ($newCapacity > $currentBedCount) {
                // Add extra beds
                for ($i = $currentBedCount + 1; $i <= $newCapacity; $i++) {
                    DB::table('HOSTEL_BEDS')->insert([
                        'HB_BED_NUMBER' => $i,
                        'HB_IS_OCCUPIED' => 'N',
                        'HB_ROOM_ID' => $roomId,
                    ]);
                }
            } elseif ($newCapacity < $currentBedCount) {
                // Check if any of the extra beds are occupied
                $occupiedCount = DB::table('HOSTEL_BEDS')
                    ->where('HB_ROOM_ID', $roomId)
                    ->where('HB_BED_NUMBER', '>', $newCapacity)
                    ->where('HB_IS_OCCUPIED', 'Y')
                    ->count();

                if ($occupiedCount > 0) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Room capacity cannot be reduced. Some of the extra beds are already occupied.'
                    ]);
                }

                // Delete unoccupied extra beds
                DB::table('HOSTEL_BEDS')
                    ->where('HB_ROOM_ID', $roomId)
                    ->where('HB_BED_NUMBER', '>', $newCapacity)
                    ->where('HB_IS_OCCUPIED', 'N')
                    ->delete();
            }
        } else {
            // INSERT new room
            $roomId = DB::table('HOSTEL_ROOMS')->insertGetId([
                'HR_HT_ID' => $request->input('hostel_type'),
                'HR_ROOM_NO' => $request->input('room_number'),
                'HR_CAPACITY' => $request->input('room_capacity'),
                'HR_HF_ID' => $request->input('floor'),
                'HR_AC_AVAILABILITY' => strtoupper($request->input('ac_availability')),
                'HR_HEATER_AVAILABILITY' => strtoupper($request->input('heater_availability')),
                'HR_REFRIGERATOR_AVAILABILITY' => strtoupper($request->input('refrigerator_availability')),
                'HR_STATUS' => 'E',
                'HR_ENTRY_USER_ID' => auth()->id(),
                'HR_ENTRY_DATE_TIME' => now(),
            ], 'HR_ID');

           
            // Add beds
            for ($i = 1; $i <= $request->input('room_capacity'); $i++) {
                DB::table('HOSTEL_BEDS')->insert([
                    'HB_BED_NUMBER' => $i,
                    'HB_IS_OCCUPIED' => 'N',
                    'HB_ROOM_ID' => $roomId,
                ]);
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => $request->filled('room_id')
                ? 'Room updated successfully!'
                : 'Room and beds added successfully!'
        ]);
    } catch (Exception $e) {
        DB::rollBack();
      
        return response()->json([
            'success' => false,
            'message' => 'An error occurred. Please try again.',
            'error' => $e->getMessage()
        ], 500);
    }
}






public function vacateBedStatus(Request $request)
{
    $bedId = $request->input('id');
    $isOccupied = $request->input('is_occupied'); // Should be 'N' for vacating

    // Update the bed status in the database
    DB::table('HOSTEL_BEDS')
        ->where('hb_id', $bedId)
        ->update(['hb_is_occupied' => $isOccupied]);

        $updated1 = DB::table('hostel_assign')
        ->where('ha_hb_id', $bedId)
        ->update(['ha_hb_id' => null]);

    return response()->json(['message' => 'Bed status updated successfully']);
}
    // Function to enable the room
    public function enableRoom($id)
    {
        $status= DB::table('hostel_rooms')
        ->where('hr_id',$id)
        ->update(['hr_status' => 'E']);
        // dd($status);
        if($status){
            return response()->json(['success' => 'Room enabled successfully']);
        }else
        return response()->json(['error' => 'not updated'], 404);
    }

    // Function to disable the room
    public function disableRoom($id)
    {

       $status= DB::table('hostel_rooms')
        ->where('hr_id',$id)
        ->update(['hr_status' => 'D']);
        // dd($status);
        if($status){
            return response()->json(['success' => 'Room enabled successfully']);
        }else
        return response()->json(['error' => 'not updated'], 404);
    }


    // Function to enable the room
    public function enableAC($id)
    {
        $status= DB::table('hostel_rooms')
        ->where('hr_id',$id)
        ->update(['hr_ac_availability' => 'E']);
        // dd($status);
        if($status){
            return response()->json(['success' => 'Room enabled successfully']);
        }else
        return response()->json(['error' => 'not updated'], 404);
    }

    // Function to disable the room
    public function disableAC($id)
    {

       $status= DB::table('hostel_rooms')
        ->where('hr_id',$id)
        ->update(['hr_ac_availability' => 'D']);
        // dd($status);
        if($status){
            return response()->json(['success' => 'Room enabled successfully']);
        }else
        return response()->json(['error' => 'not updated'], 404);
    }





public function showFacilities(){

     $sessions = DB::table('academic_sessions')
     ->select('SESS_ID', 'SESS_NAME')
     ->orderBy('SESS_ID', 'desc')
     ->get();
     $hostelNames = DB::table('hostel_facilities')
     ->select('HF_NAME')
     ->distinct()
     ->get();



    $facilities = DB::table('HOSTEL_FACILITIES')->get();

    return view('Hostel.hostel_facilities',compact('sessions', 'facilities','hostelNames'));
}

public function submitFacilities(Request $request)
{
    $validated = $request->validate([
        'facility_name' => 'required|string|max:255',
        'facility_dues' => 'required|numeric',
        'session' => 'required|string|max:100',
        'status' => 'required|string|in:enabled,disabled',
    ]);

    // Insert the facility into the database
    DB::table('HOSTEL_FACILITIES')->insert([
        'HF_NAME' => $validated['facility_name'],
        'HF_COST' => $validated['facility_dues'],
        'HF_SESSION' => $validated['session'],
        'HF_STATUS' => $validated['status'],
        'HF_ENTRYDATETIME' => now(),
        'HF_ENTRY_USER_ID' => auth()->id(), // Assuming you have user authentication
    ]);

    return redirect()->back()->with('success', 'Facility added successfully.');
}
public function editFacility(Request $request)
{
    $validated = $request->validate([
        'facility_name' => 'required|string|max:255',
        'facility_dues' => 'required|numeric',
        'session' => 'required|string|max:100',
        'status' => 'required|string|max:50',
    ]);

    DB::table('HOSTEL_FACILITIES')
        ->where('HF_ID', $request->facility_id)
        ->update([
            'HF_NAME' => $validated['facility_name'],
            'HF_COST' => $validated['facility_dues'],
            'HF_SESSION' => $validated['session'],
            'HF_STATUS' => $validated['status'],
        ]);

    return redirect()->back()->with('success', 'Facility updated successfully.');
}

public function enableFacility($id)
{
    DB::table('HOSTEL_FACILITIES')
        ->where('HF_ID', $id)
        ->update(['HF_STATUS' => 'enabled']);

    return redirect()->back()->with('success', 'Facility enabled successfully.');
}

public function disableFacility($id)
{
    DB::table('HOSTEL_FACILITIES')
        ->where('HF_ID', $id)
        ->update(['HF_STATUS' => 'disabled']);

    return redirect()->back()->with('success', 'Facility disabled successfully.');
}

public function hostel_form_visibility()
{
    $session = DB::table('academic_sessions')
        ->select('sess_id', 'sess_name')
        ->orderBy('sess_id', 'desc')
        ->get();

    $data = DB::table('hostel_form_visibility_periods')
        ->select(
            'hostel_form_visibility_periods.id',
            'hostel_form_visibility_periods.start_date',
            'hostel_form_visibility_periods.end_date',
            'hostel_form_visibility_periods.sess_id',
            'hostel_form_visibility_periods.due_date',
            'hostel_form_visibility_periods.valid_date',
            'hostel_form_visibility_periods.entry_username',
            'hostel_form_visibility_periods.entry_datetime',
            'hostel_form_visibility_periods.updated_by',
            'hostel_form_visibility_periods.updated_at',
            'hostel_form_visibility_periods.remarks',
            'academic_sessions.sess_name'
        )
        ->leftJoin('academic_sessions', 'hostel_form_visibility_periods.sess_id', '=', 'academic_sessions.sess_id')
        ->orderBy('sess_id', 'desc')
        ->get();

    return view('Hostel.hostel_form_visibility', compact('session', 'data'));
}

public function submit_hostel_form_visibility(Request $request)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'session' => 'required|numeric',
        'due_date' => 'required|date|after_or_equal:start_date',
        'valid_date' => 'required|date|after_or_equal:start_date',
        'remarks' => 'nullable|string|max:500',
    ]);

    try {
        DB::beginTransaction();

        $data = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'sess_id' => $request->session,
            'due_date' => $request->due_date,
            'valid_date' => $request->valid_date,
            'remarks' => $request->remarks,
        ];

        if ($request->filled('id')) {
            // Update existing record
            $data['updated_by'] = strtolower(auth()->user()->username);
            $data['updated_at'] = now();

            DB::table('hostel_form_visibility_periods')
                ->where('id', $request->id)
                ->update($data);

            $message = 'Hostel form visibility period updated successfully.';
        } else {
            // Insert new record
            $data['entry_username'] = strtolower(auth()->user()->username);
            $data['entry_datetime'] = now();

            DB::table('hostel_form_visibility_periods')->insert($data);
            $message = 'Hostel form visibility period added successfully.';
        }

        DB::commit();
        return redirect()->route('hostel-form-visibility')->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error in submit_hostel_form_visibility: ' . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->with('error', 'An error occurred. Please try again.');
    }
}

public function delete_hostel_form_visibility(Request $request)
{
    try {
        DB::beginTransaction();

        $deleted = DB::table('hostel_form_visibility_periods')
            ->where('id', $request->id)
            ->delete();

        DB::commit();

        if ($deleted) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Record not found.']);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error in delete_hostel_form_visibility: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'An error occurred while deleting the record.']);
    }
}

}
