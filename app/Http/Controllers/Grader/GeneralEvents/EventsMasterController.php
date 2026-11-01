<?php

namespace  App\Http\Controllers\Grader\GeneralEvents;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Table;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Models\Grader\Schools;
use App\Models\Grader\Departments;
use App\Models\Grader\FacultyCourses;
use App\Models\Grader\EmpEmployees;
use Response;
use Validator;
use DataTables;
use DateTime;
use Carbon\Carbon;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\Writer\PngWriter;

class EventsMasterController extends Controller{

        function eventMasterForm(){
            $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
            $schools =  Schools::all();
            $departments = array(); // $this->school->fetch();
            $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
            $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
            $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
            $query = 'select * from fin_fee_category';
            $feeCategories = DB::select($query);
            return view('general_events.master_event_form',compact('sessions','schools','departments','facultyCourses','offeredFaculty','feeCategories','programs'));

        }
        public function addEvent(Request $request){
            // dd($request->all());
            $validator = Validator::make($request->all(), [
                'E_EVENT_NAME'         => 'required|string|max:255',
                'E_EVENT_TYPE'         => 'required',
                'E_EVENT_DESCRIPTION'  => 'required|string|max:1000',
                'E_REGISTRATION_START'  => 'required|date',
                'E_REGISTRATION_END'    => 'required|date|after_or_equal:E_REGISTRATION_START',
                'E_FROM_DATE'           => 'required|date|after:E_REGISTRATION_START',
                'E_TO_DATE'             => 'required|date|after:E_REGISTRATION_START|after_or_equal:E_FROM_DATE',
                'E_EVENT_VENUE'         => 'required|string|max:255',
                'school'                => 'required',
                'department'            => 'required',
                'e_organizer'           => 'nullable|max:255',
                'E_REGISTRATION_FEE'    => 'required|numeric|min:100',
                'E_DISCOUNT_GENERAL'    => 'nullable|numeric|min:0|lte:E_REGISTRATION_FEE',
                'E_DISCOUNT_BNU_STUDENT' => 'nullable|numeric|min:0|lte:E_REGISTRATION_FEE',
                'E_DISCOUNT_BNU_ALUMNI' => 'nullable|numeric|min:0|lte:E_REGISTRATION_FEE',
                'E_DISCOUNT_BNU_FACULTY' => 'nullable|numeric|min:0|lte:E_REGISTRATION_FEE',
                'E_DISCOUNT_BNU_STAFF' => 'nullable|numeric|min:0|lte:E_REGISTRATION_FEE',
                'E_ARCHIVE_DATE'       => 'nullable|date',
                'E_SPONSOR_REGISTRATION_ENABLED' => 'nullable|string|in:Y,N',
                'E_URL'                 => 'nullable|max:255',
                'E_STATUS'              => 'required|string|in:E,D', // Example: E=Enabled, D=Disabled
                'E_EVENT_BANNER'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'E_EVENT_NAME.required'         => 'Please enter the event name.',
                'E_EVENT_TYPE.required'         => 'Please specify the event type.',
                'E_EVENT_DESCRIPTION.required'  => 'Please provide a description for the event.',
                'E_REGISTRATION_START.date'     => 'The registration start date must be a valid date.',
                'E_REGISTRATION_START.required' => 'Please select the registration start date.',
                'E_REGISTRATION_END.date'       => 'The registration end date must be a valid date.',
                'E_REGISTRATION_END.required'   => 'Please select the registration end date.',
                'E_REGISTRATION_END.after_or_equal' => 'The registration end date must be the same as or after the registration start date.',
                'E_FROM_DATE.required'          => 'Please select the event start date.',
                'E_FROM_DATE.date'              => 'The event start date must be a valid date.',
                'E_FROM_DATE.after'             => 'The event start date must be after the registration start date.',
                'E_TO_DATE.required'            => 'Please select the event end date.',
                'E_TO_DATE.date'                => 'The event end date must be a valid date.',
                'E_TO_DATE.after'               => 'The event end date must be after the registration start date.',
                'E_TO_DATE.after_or_equal'      => 'The event end date must be the same as or after the event start date.',
                'E_EVENT_VENUE.required'        => 'Please enter the venue for the event.',
                'school.required'               => 'Please select a school.',
                'department.required'           => 'Please select a department.',
                'E_REGISTRATION_FEE.required'   => 'Please enter the general registration fee.',
                'E_REGISTRATION_FEE.numeric'    => 'The registration fee must be a valid number.',
                'E_REGISTRATION_FEE.min'        => 'The registration fee cannot be less than 100.',
                'E_DISCOUNT_GENERAL.numeric'    => 'General discount must be a valid number.',
                'E_DISCOUNT_GENERAL.min'        => 'General discount cannot be negative.',
                'E_DISCOUNT_GENERAL.lte'        => 'General discount cannot exceed the general registration fee.',
                'E_DISCOUNT_BNU_STUDENT.numeric' => 'BNU student discount must be a valid number.',
                'E_DISCOUNT_BNU_STUDENT.min'   => 'BNU student discount cannot be negative.',
                'E_DISCOUNT_BNU_STUDENT.lte'    => 'BNU student discount cannot exceed the general registration fee.',
                'E_DISCOUNT_BNU_ALUMNI.numeric' => 'BNU alumni discount must be a valid number.',
                'E_DISCOUNT_BNU_ALUMNI.min'    => 'BNU alumni discount cannot be negative.',
                'E_DISCOUNT_BNU_ALUMNI.lte'    => 'BNU alumni discount cannot exceed the general registration fee.',
                'E_DISCOUNT_BNU_FACULTY.numeric' => 'BNU faculty discount must be a valid number.',
                'E_DISCOUNT_BNU_FACULTY.min'   => 'BNU faculty discount cannot be negative.',
                'E_DISCOUNT_BNU_FACULTY.lte'   => 'BNU faculty discount cannot exceed the general registration fee.',
                'E_DISCOUNT_BNU_STAFF.numeric' => 'BNU staff discount must be a valid number.',
                'E_DISCOUNT_BNU_STAFF.min'     => 'BNU staff discount cannot be negative.',
                'E_DISCOUNT_BNU_STAFF.lte'     => 'BNU staff discount cannot exceed the general registration fee.',
                'E_ARCHIVE_DATE.date'           => 'Archive date must be a valid date.',
                'E_SPONSOR_REGISTRATION_ENABLED.in' => 'Invalid sponsor registration option.',
                'E_URL.url'                     => 'Please enter a valid URL.',
                // 'E_URL.required'                 => 'URL is required.',
                'E_STATUS.required'             => 'Please select the event status.',
                'E_STATUS.in'                   => 'Invalid event status selected.',
                'E_EVENT_BANNER.image'          => 'Event banner must be an image file.',
                'E_EVENT_BANNER.mimes'          => 'Event banner must be a JPG, JPEG, PNG or WEBP file.',
                'E_EVENT_BANNER.max'            => 'Event banner must not be larger than 2MB.',
                ]);

            if ($validator->fails()) {
                return Response::json([
                    'success' => false,
                    'error'   => $validator->getMessageBag()->toArray()
                ], 400);
            }

            $sponsorEnabled = $request->input('E_SPONSOR_REGISTRATION_ENABLED', 'N');
            if (!in_array($sponsorEnabled, ['Y', 'N'], true)) {
                $sponsorEnabled = 'N';
            }

            $normalizedSponsors = [];
            if ($sponsorEnabled === 'Y') {
                $sponsorJsonRaw = $request->input('E_SPONSOR_DETAILS', '[]');
                if (is_array($sponsorJsonRaw)) {
                    $sponsorList = $sponsorJsonRaw;
                } else {
                    $sponsorList = json_decode((string) $sponsorJsonRaw, true);
                }
                if (!is_array($sponsorList)) {
                    return Response::json([
                        'success' => false,
                        'error'   => ['E_SPONSOR_DETAILS' => ['Sponsor details must be valid JSON.']],
                    ], 400);
                }
                foreach ($sponsorList as $idx => $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    $title = isset($item['title']) ? trim((string) $item['title']) : '';
                    $feeVal = $item['fee'] ?? null;
                    if ($title === '' && ($feeVal === null || $feeVal === '')) {
                        continue;
                    }
                    if ($title === '') {
                        return Response::json([
                            'success' => false,
                            'error'   => ['E_SPONSOR_DETAILS' => ['Each sponsor row must have a title (label).']],
                        ], 400);
                    }
                    if (!is_numeric($feeVal) || (float) $feeVal < 0) {
                        return Response::json([
                            'success' => false,
                            'error'   => ['E_SPONSOR_DETAILS' => ['Fee must be a non-negative number for: '.$title]],
                        ], 400);
                    }
                    $normalizedSponsors[] = [
                        'title' => $title,
                        'fee'   => round((float) $feeVal, 2),
                    ];
                }
                if (count($normalizedSponsors) === 0) {
                    return Response::json([
                        'success' => false,
                        'error'   => ['E_SPONSOR_DETAILS' => ['Add at least one sponsor type with fee when sponsor registration is enabled.']],
                    ], 400);
                }
            }

            $username = auth()->user()->username;

            $archiveDate = null;
            $archiveRaw = $request->input('E_ARCHIVE_DATE');
            if ($archiveRaw !== null && trim((string) $archiveRaw) !== '') {
                $archiveDate = Carbon::parse($archiveRaw)->format('Y-m-d');
            }

           $data = [
                    'E_EVENT_NAME'         => $request->E_EVENT_NAME,
                    'E_EVENT_TYPE'         => $request->E_EVENT_TYPE,
                    'E_EVENT_DESCRIPTION'  => $request->E_EVENT_DESCRIPTION,
                    'E_EVENT_VENUE'        => $request->E_EVENT_VENUE,
                    'E_ORGANIZER'          => $request->e_organizer,
                    'E_FROM_DATE'          => Carbon::parse($request->E_FROM_DATE)->format('Y-m-d H:i:s'),
                    'E_TO_DATE'            => Carbon::parse($request->E_TO_DATE)->format('Y-m-d H:i:s'),
                    'E_SCHOOL_ID'          => $request->school,
                    'E_REGISTRATION_FEE'   => $request->E_REGISTRATION_FEE,
                    'E_DISCOUNT_GENERAL'   => $this->nullableMoneyAmount($request, 'E_DISCOUNT_GENERAL'),
                    'E_DISCOUNT_BNU_STUDENT' => $this->nullableMoneyAmount($request, 'E_DISCOUNT_BNU_STUDENT'),
                    'E_DISCOUNT_BNU_ALUMNI' => $this->nullableMoneyAmount($request, 'E_DISCOUNT_BNU_ALUMNI'),
                    'E_DISCOUNT_BNU_FACULTY' => $this->nullableMoneyAmount($request, 'E_DISCOUNT_BNU_FACULTY'),
                    'E_DISCOUNT_BNU_STAFF' => $this->nullableMoneyAmount($request, 'E_DISCOUNT_BNU_STAFF'),
                    'E_ARCHIVE_DATE'       => $archiveDate,
                    'E_SPONSOR_DETAILS'  => json_encode($normalizedSponsors, JSON_UNESCAPED_UNICODE),
                    'E_STATUS'             => $request->E_STATUS,
                    'E_URL'                => $request->E_URL,
                    'E_REGISTRATION_START' => Carbon::parse($request->E_REGISTRATION_START)->format('Y-m-d H:i:s'),
                    'E_REGISTRATION_END'   => Carbon::parse($request->E_REGISTRATION_END)->format('Y-m-d H:i:s'),
                    'E_DEPT_ID'            => $request->department,
                    'E_ENTRY_USER'         => $username,
                    'E_ENTRY_DATETIME'     => now(),
                ];
                /* Remove image */
                if ($request->boolean('remove_image') && !empty($request->mainId)) {

                    $event = DB::table('EVENTS')->where('E_ID', $request->mainId)->first();

                    if ($event && $event->e_image && file_exists(public_path('docs/'.$event->e_image))) {
                        unlink(public_path('docs/'.$event->e_image));
                    }

                    $data['E_IMAGE'] = null;
                }

                /* Upload new image */
                elseif ($request->hasFile('E_EVENT_BANNER')) {

                    $file = $request->file('E_EVENT_BANNER');
                    $imageName = time().'_'.$file->getClientOriginalName();

                    $file->move(public_path('docs'), $imageName);

                    $data['E_IMAGE'] = $imageName;
                }
                // dd($data);

            $urlForDuplicateCheck = trim((string) ($request->E_URL ?? ''));
            $duplicateExistsMsg = 'Events with this data already exist. Use a different event name, event type';

            if(empty($request->mainId)){//insertion case
                $duplicateQuery = DB::table('EVENTS')
                                    ->select(
                                        'E_ID',
                                        'E_EVENT_NAME',
                                        )
                                    ->where('E_EVENT_NAME', $request->E_EVENT_NAME)
                                    ->where('E_EVENT_TYPE', $request->E_EVENT_TYPE)
                                    ;
                if ($urlForDuplicateCheck !== '') {
                    $duplicateQuery->where('E_URL', $urlForDuplicateCheck);
                }
                $duplicateCheck = $duplicateQuery->get();
                                    //  dd($duplicateCheck);
                if(sizeof($duplicateCheck) == 0 ){
                    $newId = DB::table('EVENTS')->insertGetId($data, 'E_ID');
                    DB::table('EVENTS')->where('E_ID', $newId)
                        ->update(['E_QR_CODE' => $this->generateEventQrBase64((int) $newId)]);
                    $resp[0] = true;
                    $resp[1] = 'Saved Successfully';
                }else{
                    $resp[0] = false;
                    $resp[1] = $duplicateExistsMsg;
                }
            }else{ //edit case
                $duplicateQuery = DB::table('EVENTS')
                                    ->select(
                                        'E_ID',
                                        'E_EVENT_NAME',
                                        )
                                    ->where('E_EVENT_NAME', $request->E_EVENT_NAME)
                                    ->where('E_EVENT_TYPE', $request->E_EVENT_TYPE)
                                    ->where('E_ID', '!=', $request->mainId);
                if ($urlForDuplicateCheck !== '') {
                    $duplicateQuery->where('E_URL', $urlForDuplicateCheck);
                }
                $duplicateCheck = $duplicateQuery->get();
                    // dd($duplicateCheck);
                    if(sizeof($duplicateCheck) == 0 ){
                        $existingQr = DB::table('EVENTS')->where('E_ID', $request->mainId)->value('E_QR_CODE');
                        if (empty($existingQr)) {
                            $data['E_QR_CODE'] = $this->generateEventQrBase64((int) $request->mainId);
                        }
                        DB::table('EVENTS')
                                ->where('E_ID', $request->mainId)
                                ->update($data);
                        $resp[0] = true;
                        $resp[1] = 'Updated Successfully';

                    }else{
                        $resp[0]=false;
                        $resp[1]=$duplicateExistsMsg;
                    }


            }
            return response()->json([
                'success' => $resp[0],
                'msg' => $resp[1],
            ]);

        }
        protected function getEventList(Request $request){
            // dd($request->all());
            $data = DB::table('EVENTS as ev')
                ->select(
                'E_ID',
                'E_EVENT_NAME',
                'E_EVENT_TYPE',
                'E_EVENT_DESCRIPTION',
                'E_IMAGE',
                'E_EVENT_VENUE',
                DB::raw("TO_CHAR(E_FROM_DATE, 'YYYY-MM-DD\"T\"HH24:MI') AS E_FROM_DATE"),
                DB::raw("TO_CHAR(E_TO_DATE, 'YYYY-MM-DD\"T\"HH24:MI') AS E_TO_DATE"),
                'E_SCHOOL_ID',
                'E_REGISTRATION_FEE',
                'E_DISCOUNT_GENERAL',
                'E_DISCOUNT_BNU_STUDENT',
                'E_DISCOUNT_BNU_ALUMNI',
                'E_DISCOUNT_BNU_FACULTY',
                'E_DISCOUNT_BNU_STAFF',
                DB::raw("TO_CHAR(ev.E_ARCHIVE_DATE, 'YYYY-MM-DD') AS E_ARCHIVE_DATE"),
                DB::raw("CASE WHEN ev.E_QR_CODE IS NULL THEN 'N' ELSE 'Y' END AS E_QR_CODE_PRESENT"),
                'E_SPONSOR_DETAILS',
                'E_STATUS',
                'E_ENTRY_DATETIME',
                'E_UPDATE_DATETIME',
                'E_URL',
                DB::raw("TO_CHAR(E_REGISTRATION_START, 'YYYY-MM-DD\"T\"HH24:MI') AS E_REGISTRATION_START"),
                DB::raw("TO_CHAR(E_REGISTRATION_END, 'YYYY-MM-DD\"T\"HH24:MI') AS E_REGISTRATION_END"),
                'E_DEPT_ID',
                'E_ENTRY_USER',
                'school.SCHOOL_NAME',
                'dept.DEPT_NAME',
                'e_organizer'
                    )
                    ->join('SCHOOLS as school', 'school.SCHOOL_ID', 'ev.E_SCHOOL_ID')
                    ->join('DEPARTMENTS as dept', 'dept.DEPT_ID', 'ev.E_DEPT_ID');
                    // ->join('PROGRAMS as prog', 'prog.PROG_ID', 'ffs.FFS_PROG_ID')
                if(!empty($request->school_sel) ){
                    $data =  $data->where('ev.E_SCHOOL_ID', $request->school_sel);
                }
                if(!empty($request->department)){
                    $data =  $data->where('ev.E_DEPT_ID', $request->department);
                }
                if(!empty($request->url)){
                    $data =  $data->where('ev.E_URL', $request->url);
                }
                // if(!empty($request->status)){
                //     $data =  $data->where('ev.E_STATUS', $request->status);
                // }
                if(!empty($request->reg_fee)){
                    $data =  $data->where('ev.E_REGISTRATION_FEE', $request->reg_fee);
                }
                if(!empty($request->event_type)){
                    $data =  $data->where('ev.E_EVENT_TYPE', $request->event_type);
                }
                if(auth()->user()->group_id != 644){
                    $data =  $data->where('ev.E_ENTRY_USER', '=', auth()->user()->username);
                }
                $data =  $data->orderBy('ev.E_ID', 'desc')->get();

            //    dd($data);
            return DataTables::of($data)->toJson();
        }
        public function deleteEvent(Request $request){
            $userId = auth()->user()->username;
            $affectedRows =  DB::table('EVENTS')
                                ->where('E_ID', $request->mainId)
                                ->update([
                                    'E_STATUS' => 'D',
                                    'E_ENTRY_USER' => $userId,
                                ]);
            if ($affectedRows > 0) {
                return response()->json([
                    'success' => true,
                    'data' => 'Inactive Successfully'
            ]);
            } else {
                return response()->json([
                    'success' => false,
                    'data' => 'Some problem occured'
                ]);
            }

        }
        protected function getProgramFeeStructureForm(Request $request){
            $dept_id = json_decode($request->get('dept_id'));
            // dd($request->get('degreeLevel'));
            $programs=[];
            if(!empty($dept_id)){
                if(is_array($dept_id)){//in case of all departments selected
                    foreach($dept_id as $val){
                        $dept_idsss[] = explode('~~', $val)[1];
                    }
                    $programs = DB::table('programs')
                                ->where('PROG_STATUS','=', 'E')
                                ->whereIn('prog_dept_id', $dept_idsss);
                                if(!empty($request->get('degreeLevel'))){
                                    $programs = $programs->where('PROG_DEGREE_LEVEL','=', $request->get('degreeLevel'));
                                }
                    $programs =  $programs->orderBy('prog_name', 'asc')
                                ->get();
                }else{
                    $programs = DB::table('programs')
                        ->where('prog_dept_id', $dept_id)
                        ->where('PROG_STATUS', '=', 'E');
                        if(!empty($request->get('degreeLevel'))){
                            $programs = $programs->where('PROG_DEGREE_LEVEL','=', $request->get('degreeLevel'));
                        }
                    $programs =  $programs->orderBy('prog_name', 'asc')
                                ->get();
                }
                // dd($programs);

            }
            return response()->json(['data' => $programs, 'status'=> true, 'message' => count($programs).' record(s) found!']);
        }
        protected function getDepartmentsFeeStructureForm(Request $request){
            // dd($request->get('school_id'));
            $school_id = json_decode($request->get('school_id'));
            $departments=[];
            if(!empty($school_id)){
                if(is_array($school_id)){
                    $departments = DB::table('departments')
                        ->where('dept_status', '=', 'E')
                        ->whereIn('dept_school_id', $school_id)
                        ->orderBy('DEPT_NAME', 'asc')
                        ->get();
                }else{
                    $departments = DB::table('departments')
                        ->where('dept_status', '=', 'E')
                        ->where('dept_school_id', $school_id)
                        ->orderBy('DEPT_NAME', 'asc')
                        ->get();
                }
                // dd($departments);

            }
            return response()->json(['data' => $departments, 'status'=> true, 'message' => count($departments).' record(s) found!']);
        }

        /**
         * Optional money fields: empty input stored as NULL (no discount / default fee applies).
         */
        protected function nullableMoneyAmount(Request $request, string $key)
        {
            $v = $request->input($key);
            if ($v === null || $v === '') {
                return null;
            }

            return round((float) $v, 2);
        }

        /**
         * Generate a base64-encoded PNG QR code for the given event ID.
         * Encodes the public detail URL configured via config('app.events_public_url').
         */
        protected function generateEventQrBase64(int $eventId): string
        {
            $base = (string) config('app.events_url');
            if (trim($base) === '') {
                $base = (string) env('EVENTS_URL', 'https://events.bnu.edu.pk');
            }
            $url = rtrim($base, '/') . '/events/detail/' . $eventId;
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($url)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(new ErrorCorrectionLevelMedium())
                ->size(300)
                ->margin(10)
                ->build();

            return base64_encode($result->getString());
        }

        /**
         * Public PNG endpoint so external systems (posters, emails, microsites) can
         * hot-link a stable URL like /api/events/{id}/qr.png. Self-heals by generating
         * and persisting the QR on first hit when E_QR_CODE is NULL.
         */
        public function eventQrImage(Request $request, $id)
        {
            $eventId = (int) $id;
            if ($eventId <= 0) {
                abort(404);
            }

            $row = DB::table('EVENTS')->where('E_ID', $eventId)->first(['E_QR_CODE']);
            if (!$row) {
                abort(404);
            }

            $force  = $request->boolean('force') || $request->boolean('regen');
            $base64 = $force ? null : ($row->e_qr_code ?? null);

            if (empty($base64)) {
                $base64 = $this->generateEventQrBase64($eventId);
                DB::table('EVENTS')->where('E_ID', $eventId)->update(['E_QR_CODE' => $base64]);
            }

            $binary = base64_decode($base64, true);
            if ($binary === false) {
                abort(500);
            }

            return response($binary, 200, [
                'Content-Type'  => 'image/png',
                'Cache-Control' => $force ? 'no-store, max-age=0' : 'public, max-age=86400',
            ]);
        }

}
