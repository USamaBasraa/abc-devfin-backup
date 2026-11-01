<?php
namespace App\Http\Controllers\Grader\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Table;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\sessions;
use App\Models\Schools;
use App\Models\BookItems;
use App\LibraryCirculation;
use App\LibraryCriteria;
use App\Models\BookCirculation;
use App\Models\Departments;
use App\Models\FacultyCourses;
use App\Models\EmpEmployees;
use Symfony\Component\HttpFoundation\Response;
use \Milon\Barcode\DNS1D;
use App\Models\User;

class LibraryRoomsController extends Controller{

    function index(){
        $res = [];
        
        // Get all rooms with availability counts for today
        $today = date('d-M-Y');
        $rooms = DB::table('LIBRARY_MEETING_ROOMS')
            ->select('LIBRARY_MEETING_ROOMS.*')
            ->get();
        
        // Calculate availability for each room
        $roomsWithAvailability = [];
        foreach ($rooms as $room) {
            // Get unique reserved time slots for today (not individual reservations)
            $reservedTimeSlots = DB::table('library_room_reservations')
                ->where('room_id', $room->id)
                ->whereRaw("TRUNC(RESERVED_FOR) = TO_DATE(?, 'DD-MON-YY')", [$today])
                ->distinct()
                ->pluck('start_time')
                ->count();
            
            // Total slots available (9 AM to 6 PM = 9 slots)
            $totalSlots = 9;
            $availableSlots = $totalSlots - $reservedTimeSlots;
            
            $roomsWithAvailability[] = [
                'id' => $room->id,
                'title' => $room->title,
                'capacity' => $room->number_of_persons,
                'description' => $room->description ?? '',
                'total_slots' => $totalSlots,
                'reserved_slots' => $reservedTimeSlots,
                'available_slots' => $availableSlots
            ];
        }
        
        return view("grader.library.room_allocation.index", compact("res", "roomsWithAvailability"));
    }
    public function show(Request $request){

        $sql = DB::table("library_room_reservations")
            ->select('library_room_reservations.*')
            ->where('reservation_group_id',$request->group_id)
            ->get();
        return $sql;

    }
    public function updateStatus(Request $request){

        // Validation Rules
        $rules = array(
            'group_id'     => "required",
            'is_approved'  => "required|in:0,1",
            'remarks'      => "required|string|max:1500"
        );

        $messages = [
            'group_id.required'      => 'Group ID is required',
            'is_approved.required'   => 'Approval status is required',
            'is_approved.in'         => 'Invalid approval status',
            'remarks.required'       => 'Remarks are required for approval/rejection',
            'remarks.max'            => 'Remarks cannot exceed 1500 characters'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        
        if($validator->fails()){
            return response()->json([
                "error" => true,
                "message" => $validator->errors()->first(),
                "errors" => $validator->errors()
            ], 422);
        }

        try{
            // Determine status: 1 = Approved, 2 = Rejected
            if($request->is_approved == 1){
                $status = 1;
                $action = "approved";
                $statusMessage = "Reservation has been approved successfully.";
            } else {
                $status = 2;
                $action = "rejected";
                $statusMessage = "Reservation has been rejected successfully.";
            }

            // Check if reservation exists before updating
            $exists = DB::table('library_room_reservations')
                ->where('reservation_group_id', $request->group_id)
                ->exists();

            if(!$exists) {
                return response()->json([
                    "error" => true,
                    "message" => "Reservation not found with the provided Group ID."
                ], 404);
            }

            // Get current logged-in user's username
            $username = auth()->user()->username ?? auth()->user()->name ?? auth()->user()->email ?? 'system';
            
            // Update the reservation status and remarks
            $res = DB::table('library_room_reservations')
                ->where('reservation_group_id', $request->group_id)
                ->update([
                    'is_approved' => $status,
                    'remarks' => trim($request->remarks),
                    'updated_by' => $username,
                    'updated_at' => now()
                ]);

            if($res > 0) {
                // Clear cache for this data
                \Illuminate\Support\Facades\Cache::flush();
                
                return response()->json([
                    "error" => false,
                    "message" => $statusMessage,
                    "status" => $status,
                    "action" => $action,
                    "group_id" => $request->group_id
                ], 200);
            } else {
                return response()->json([
                    "error" => true,
                    "message" => "No changes were made. The reservation may already have this status."
                ], 400);
            }

        } catch(\Exception $e) {
            return response()->json([
                "error" => true,
                "message" => "An error occurred while updating the reservation status.",
                "details" => $e->getMessage()
            ], 500);
        }
    }

    public function getRoomAllocationRequests(Request $request){
        // Professional Pagination Parameters
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 100); // Default 100 records per page
        $offset = ($page - 1) * $perPage;
        
        $forceRefresh = $request->input('force_refresh');
        $status = $request->input('status', 0);
        
        // Create cache key based on all parameters
        $cacheKey = 'library_room_allocation_' . md5(serialize([
            'status' => $status,
            'page' => $page,
            'per_page' => $perPage
        ]));
        
        // Try to get from cache first (5 minutes cache) - Skip if force refresh
        if (!$forceRefresh) {
            $cachedResult = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if ($cachedResult) {
                return response()->json([
                    'data' => $cachedResult['data'],
                    'pagination' => $cachedResult['pagination'],
                    'status' => true,
                    'cached' => true
                ]);
            }
        } else {
            // If force refresh, clear the cache for this key
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
        }

        $sql = "
        WITH getentrytime AS (
            SELECT
                COUNT(*)                                       AS count,
                library_room_reservations.reservation_group_id AS group_id,
                library_meeting_rooms.title,
                library_room_reservations.reserved_for,
                library_room_reservations.start_time,
                library_room_reservations.end_time,
                library_room_reservations.is_approved,
                library_room_reservations.status,
                library_room_reservations.remarks
            FROM
                library_room_reservations
                LEFT JOIN library_meeting_rooms ON library_room_reservations.room_id = library_meeting_rooms.id
            WHERE ";
    
        if ($status == 0) {
            $sql .= "library_room_reservations.is_approved = '$status'";
        } else {
            $sql .= "library_room_reservations.is_approved IN (1,2)";
        }
        
        $sql .= " GROUP BY
                    library_room_reservations.reservation_group_id,
                    library_meeting_rooms.title,
                    library_room_reservations.reserved_for,
                    library_room_reservations.start_time,
                    library_room_reservations.is_approved,
                    library_room_reservations.end_time,
                    library_room_reservations.status,
                    library_room_reservations.remarks
                ORDER BY
                    library_room_reservations.reservation_group_id DESC
            )
        SELECT
            getentrytime.*,
            (
                SELECT
                    created_at
                FROM
                    library_room_reservations
                WHERE
                    reservation_group_id = group_id
                AND ROWNUM = 1
            ) AS entry_date_time
        FROM
            getentrytime
        ";
        
        // Build separate optimized COUNT query (without ORDER BY and complex fields)
        $countSql = "SELECT COUNT(*) as total_count FROM (" . $sql . ")";
        
        // Execute count query first
        $totalResult = DB::select($countSql);
        $totalRecords = $totalResult[0]->total_count ?? 0;
        
        // Add Oracle 12c+ pagination to main query
        $sql .= " OFFSET $offset ROWS FETCH FIRST $perPage ROWS ONLY";
        
        $res = DB::select($sql);
        
        // Prepare response data
        $responseData = [
            'data' => $res,
            'pagination' => [
                'current_page' => (int)$page,
                'per_page' => (int)$perPage,
                'total' => (int)$totalRecords,
                'last_page' => ceil($totalRecords / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $totalRecords),
                'has_more_pages' => ($page * $perPage) < $totalRecords
            ],
            'status' => true
        ];
        
        // Store in cache for 5 minutes (300 seconds)
        \Illuminate\Support\Facades\Cache::put($cacheKey, [
            'data' => $res,
            'pagination' => $responseData['pagination']
        ], 300);
        
        // Return paginated response
        return response()->json($responseData);
    }
    public function countCompletedRequest(){
        $sql = "SELECT
        count(*) as count,
        library_room_reservations.reservation_group_id as group_id,
        library_meeting_rooms.title,
        library_room_reservations.reserved_for,
        library_room_reservations.start_time,
        library_room_reservations.end_time,
        library_room_reservations.is_approved
    FROM
        library_room_reservations
    LEFT JOIN library_meeting_rooms ON library_room_reservations.room_id = library_meeting_rooms.id
    WHERE
            library_room_reservations.status = 1
    GROUP BY library_room_reservations.reservation_group_id,
            library_meeting_rooms.title,
            library_room_reservations.reserved_for,
            library_room_reservations.start_time,
            library_room_reservations.is_approved,
            library_room_reservations.end_time

    order by
            library_room_reservations.reservation_group_id
    desc";
        $res = DB::select($sql);
       return $res;

    }

    public function getRoomAvailability(Request $request){
        try {
            $roomId = $request->input('room_id');
            $date = $request->input('date', date('d-M-Y')); // Default to current date
            
            // Convert date format if needed
            if (strpos($date, '-') !== false && strlen($date) === 10) {
                // If date is in YYYY-MM-DD format, convert to DD-MON-YY
                $date = date('d-M-Y', strtotime($date));
            }
            
            if (!$roomId) {
                return response()->json([
                    'error' => true,
                    'message' => 'Room ID is required'
                ], 400);
            }

            // Get room details
            $room = DB::table('LIBRARY_MEETING_ROOMS')
                ->where('id', $roomId)
                ->first();

            if (!$room) {
                return response()->json([
                    'error' => true,
                    'message' => 'Room not found'
                ], 404);
            }

            // Get reserved time slots for the specified date (unique time slots only)
            $reservedSlots = DB::table('library_room_reservations')
                ->where('room_id', $roomId)
                ->whereRaw("TRUNC(RESERVED_FOR) = TO_DATE(?, 'DD-MON-YY')", [$date])
                ->select('start_time', 'end_time', 'enrollment', 'reserved_by', 'reservation_group_id')
                ->orderBy('start_time')
                ->get();

            // Group by time slot to get unique reserved slots
            $uniqueReservedSlots = $reservedSlots->groupBy('start_time');

            // Generate all possible time slots (9 AM to 6 PM)
            $allTimeSlots = [];
            for ($hour = 9; $hour <= 17; $hour++) {
                $slot = [
                    'start_time' => $hour,
                    'end_time' => $hour + 1,
                    'time_display' => sprintf('%02d:00 - %02d:00', $hour, $hour + 1),
                    'is_reserved' => false,
                    'reservation_details' => null
                ];
                
                // Check if this slot is reserved (using grouped data)
                if ($uniqueReservedSlots->has($hour)) {
                    $slotReservations = $uniqueReservedSlots[$hour];
                    $firstReservation = $slotReservations->first();
                    
                    $slot['is_reserved'] = true;
                    $slot['reservation_details'] = [
                        'enrollment' => $slotReservations->pluck('enrollment')->implode(', '),
                        'reserved_by' => $firstReservation->reserved_by,
                        'group_id' => $firstReservation->reservation_group_id,
                        'group_members_count' => $slotReservations->count()
                    ];
                }
                
                $allTimeSlots[] = $slot;
            }

            // Count available and reserved slots
            $availableCount = collect($allTimeSlots)->where('is_reserved', false)->count();
            $reservedCount = collect($allTimeSlots)->where('is_reserved', true)->count();

            return response()->json([
                'error' => false,
                'room' => [
                    'id' => $room->id,
                    'title' => $room->title,
                    'capacity' => $room->number_of_persons,
                    'description' => $room->description ?? ''
                ],
                'date' => $date,
                'time_slots' => $allTimeSlots,
                'summary' => [
                    'total_slots' => count($allTimeSlots),
                    'available_slots' => $availableCount,
                    'reserved_slots' => $reservedCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error fetching room availability: ' . $e->getMessage()
            ], 500);
        }
    }


}