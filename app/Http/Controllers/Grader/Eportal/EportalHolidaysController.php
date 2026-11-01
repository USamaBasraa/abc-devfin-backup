<?php

namespace App\Http\Controllers\Grader\Eportal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EportalHolidaysController extends Controller
{
    public function index(Request $request){


        $holidays = DB::table('holidays')->orderBy('id', 'desc')->get();
        
        return view('ePortal.ePortalRequest.ePortalHolidays',['holidays' => $holidays]);
    }


    public function submitHoliday(Request $request)
{
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'holidayDate' => 'required|date|after_or_equal:today',
        'holidayDescription' => 'required|string|max:1000',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()->toArray()], 422);
    }

    // Start the transaction
    DB::beginTransaction();

    try {
        // Insert the holiday into the database
        $inserted = DB::table('holidays')->insert([
            'holiday_date' => $request->input('holidayDate'),
            'holiday_description' => $request->input('holidayDescription'),
            'entry_user_id' => auth()->user()->id
        ]);

        if (!$inserted) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to insert holiday.'
            ], 500);
        }

        // Commit the transaction
        DB::commit();

        // Check if the holidayDate exists in the holidays table
        $holidayExists = DB::table('holidays')
            ->where('holiday_date', $request->input('holidayDate'))
            ->exists();

            if ($holidayExists) {
                // Fetch all holiday dates
                $holidaysData = DB::table('holidays')->pluck('holiday_date')->toArray();
                
                
                
                // Calculate the new date
                $newDate = $this->removeWeekHolidays($request->input('holidayDate'), $holidaysData);
                
                // dd($newDate);
            // Format the new date correctly
            $formattedNewDate = date('Y-m-d', strtotime($newDate)); // Ensure it's in YYYY-MM-DD format

            // Start a new transaction for updating `er_escalation_time`
            DB::beginTransaction();

            // Update `er_escalation_time` for records with the original date
            $updatedCount = DB::table('eportal_request')
                ->whereRaw('TRUNC(er_escalation_time) = ?', [$request->input('holidayDate')])
                ->update([
                    'er_escalation_time' => DB::raw("TO_TIMESTAMP('$formattedNewDate ' || TO_CHAR(er_escalation_time, 'HH24:MI:SS.FF'), 'YYYY-MM-DD HH24:MI:SS.FF')")
                ]);

            if ($updatedCount === 0) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'No records updated in eportal_request.'
                ], 500);
            }

            // Commit the transaction for the update
            DB::commit();

            // Fetch updated records to verify (optional)
            $updatedRequests = DB::table('eportal_request')
                ->select('er_escalation_time', 'eportal_request.*')
                ->whereRaw('TRUNC(er_escalation_time) = TRUNC(SYSDATE)')
                ->get();

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Holiday added and escalation time updated successfully!',
                'updatedRequests' => $updatedRequests // Optionally include this if needed
            ]);
        } else {
            // If holidayDate does not exist, return a generic success response
            return response()->json([
                'status' => true,
                'message' => 'Holiday added successfully!'
            ]);
        }

    } catch (\Exception $e) {
        DB::rollBack();

        // Handle any errors during the process
        return response()->json([
            'status' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

    

public function removeWeekHolidays($date, $holidays) {
    $dateTime = new \DateTime($date);
    
    // Get the day of the week (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
    $dayOfWeek = (int)$dateTime->format('w');
    
    // Define an array of weekend days (Saturday and Sunday)
    $weekendDays = [0, 6];
    
    // Convert holidays array to a simpler format for easier comparison
    $holidayDates = array_map(function($holiday) {
        return (new \DateTime($holiday))->format('Y-m-d');
    }, $holidays);

    // Check if the date is a weekend or holiday and increment if necessary
    while (in_array($dateTime->format('Y-m-d'), $holidayDates) || in_array($dayOfWeek, $weekendDays)) {
        $dateTime->add(new \DateInterval('P1D'));
        $dayOfWeek = (int)$dateTime->format('w'); // Update the day of the week after adding days
    }

    // Return the new date as a string
    return $dateTime->format('Y-m-d H:i:s');
}


    
}
