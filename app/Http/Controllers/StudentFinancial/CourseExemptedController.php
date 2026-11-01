<?php

namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Response;
use Validator;
use DataTables;
use Carbon\Carbon;

class CourseExemptedController extends Controller
{
    public function index()
    {
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');

        return view('StudentFinancial.course_exempted', compact('sessions'));
    }

    public function getCoursesBySession(Request $request)
    {
        try {
            $courses = DB::select("
                SELECT
                    crs.COURSE_ID,
                    crs.COURSE_CD_NAME AS course_name,
                    crs.COURSE_CODE
                FROM COURSES_TWO crs
                WHERE NVL(crs.COURSE_STATUS, 'E') = 'E'
                ORDER BY crs.COURSE_CODE NULLS LAST, crs.COURSE_CD_NAME NULLS LAST
            ");

            $data = array_map(function ($c) {
                return [
                    'course_id' => (int) $c->course_id,
                    'course_name' => $c->course_name ?? '',
                    'course_code' => $c->course_code ?? '',
                ];
            }, $courses);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|numeric',
            'course_id' => 'required|numeric',
            'course_name' => 'nullable|string|max:255',
            'course_code' => 'nullable|string|max:100',
            'co_id' => 'nullable|numeric',
            'enrollment_no' => 'nullable|string|max:255',
            'status' => 'nullable|in:E,D',
        ], [
            'session_id.required' => 'Please select a session.',
            'course_id.required' => 'Please select a course.',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ], 400);
        }

        $username = auth()->user()->username;
        $now = Carbon::now();
        $enrollmentNo = strtoupper(trim((string) ($request->enrollment_no ?? '')));
        $status = $request->status ?: 'E';

        // Ensure CE_COURSE_NAME stores "COURSE_CODE - COURSE_NAME" when code is available.
        $courseName = trim((string) $request->course_name);
        $courseCode = trim((string) ($request->course_code ?? ''));
        if ($courseCode === '') {
            $courseRow = DB::table('COURSES_TWO')
                ->select('COURSE_CODE', 'COURSE_CD_NAME')
                ->where('COURSE_ID', $request->course_id)
                ->first();
            if ($courseRow) {
                $courseCode = trim((string) ($courseRow->course_code ?? ''));
                if ($courseName === '') {
                    $courseName = trim((string) ($courseRow->course_cd_name ?? ''));
                }
            }
        }
        if ($courseCode !== '' && stripos($courseName, $courseCode) !== 0) {
            $courseName = $courseCode.' - '.$courseName;
        }
        if ($courseName === '') {
            return response()->json([
                'success' => false,
                'msg' => 'Course name is required.',
            ]);
        }

        $data = [
            'CE_SESSION_ID' => $request->session_id,
            'CE_CO_ID' => $request->co_id ?: null,
            'CE_COURSE_ID' => $request->course_id,
            'CE_COURSE_NAME' => $courseName,
            'CE_ENROLLMENT_NO' => $enrollmentNo !== '' ? $enrollmentNo : null,
            'CE_STATUS' => $status,
            'CE_ENTRY_USERNAME' => $username,
            'CE_UPDATED_AT' => $now,
        ];

        DB::beginTransaction();
        try {
            if (empty($request->mainId)) {
                $dup = DB::table('COURSE_EXEMPTED')
                    ->where('CE_SESSION_ID', $request->session_id)
                    ->where('CE_COURSE_ID', $request->course_id)
                    ->where('CE_STATUS', 'E');

                if ($enrollmentNo !== '') {
                    $dup->whereRaw('LOWER(TRIM(CE_ENROLLMENT_NO)) = ?', [strtolower($enrollmentNo)]);
                } else {
                    $dup->where(function ($q) {
                        $q->whereNull('CE_ENROLLMENT_NO')->orWhere('CE_ENROLLMENT_NO', '');
                    });
                }

                if ($dup->exists()) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'msg' => 'This course exemption already exists for the selected session/enrollment.',
                    ]);
                }

                $data['CE_CREATED_AT'] = $now;
                DB::table('COURSE_EXEMPTED')->insert($data);
                $mesg = 'Saved Successfully';
            } else {
                $affectedRows = DB::table('COURSE_EXEMPTED')
                    ->where('CE_ID', $request->mainId)
                    ->update($data);

                $mesg = $affectedRows > 0 ? 'Updated Successfully' : 'No changes made';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => $mesg,
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'msg' => 'Something went wrong: '.$e->getMessage(),
            ]);
        }
    }

    public function list(Request $request)
    {
        $data = DB::table('COURSE_EXEMPTED as ce')
            ->leftJoin('ACADEMIC_SESSIONS as s', 's.SESS_ID', '=', 'ce.CE_SESSION_ID')
            ->select(
                'ce.CE_ID',
                'ce.CE_SESSION_ID',
                's.SESS_NAME',
                'ce.CE_CO_ID',
                'ce.CE_COURSE_ID',
                'ce.CE_COURSE_NAME',
                'ce.CE_ENROLLMENT_NO',
                'ce.CE_STATUS',
                'ce.CE_ENTRY_USERNAME',
                'ce.CE_CREATED_AT',
                'ce.CE_UPDATED_AT'
            )
            ->where('ce.CE_STATUS', 'E')
            ->orderBy('ce.CE_ID', 'desc')
            ->get();

        return DataTables::of($data)->toJson();
    }

    public function destroy(Request $request)
    {
        $ids = $request->mainId;
        if (! is_array($ids) || count($ids) === 0) {
            return response()->json([
                'success' => false,
                'data' => 'Please select atleast one row to delete.',
            ]);
        }

        $username = auth()->user()->username;
        $affectedRows = DB::table('COURSE_EXEMPTED')
            ->whereIn('CE_ID', $ids)
            ->update([
                'CE_STATUS' => 'D',
                'CE_ENTRY_USERNAME' => $username,
                'CE_UPDATED_AT' => Carbon::now(),
            ]);

        if ($affectedRows > 0) {
            return response()->json([
                'success' => true,
                'data' => 'Deleted Successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'data' => 'Some problem occured',
        ]);
    }

    /**
     * Copy active COURSE_EXEMPTED rows from one session to another.
     * Skips duplicates (same course + enrollment already active in target).
     */
    public function importFromSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_session_id' => 'required|numeric',
            'to_session_id' => 'required|numeric|different:from_session_id',
        ], [
            'from_session_id.required' => 'Please select From Session.',
            'to_session_id.required' => 'Please select To Session.',
            'to_session_id.different' => 'From and To sessions must be different.',
        ]);

        if ($validator->fails()) {
            return Response::json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ], 400);
        }

        $fromSessionId = $request->from_session_id;
        $toSessionId = $request->to_session_id;
        $username = auth()->user()->username;
        $now = Carbon::now();

        $sourceRows = DB::table('COURSE_EXEMPTED')
            ->where('CE_SESSION_ID', $fromSessionId)
            ->where('CE_STATUS', 'E')
            ->orderBy('CE_ID')
            ->get();

        if ($sourceRows->isEmpty()) {
            return response()->json([
                'success' => false,
                'msg' => 'No active course exemptions found in the From Session.',
            ]);
        }

        $inserted = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($sourceRows as $row) {
                $courseId = $row->ce_course_id;
                $enrollmentNo = trim((string) ($row->ce_enrollment_no ?? ''));

                $dup = DB::table('COURSE_EXEMPTED')
                    ->where('CE_SESSION_ID', $toSessionId)
                    ->where('CE_COURSE_ID', $courseId)
                    ->where('CE_STATUS', 'E');

                if ($enrollmentNo !== '') {
                    $dup->whereRaw('LOWER(TRIM(CE_ENROLLMENT_NO)) = ?', [strtolower($enrollmentNo)]);
                } else {
                    $dup->where(function ($q) {
                        $q->whereNull('CE_ENROLLMENT_NO')->orWhere('CE_ENROLLMENT_NO', '');
                    });
                }

                if ($dup->exists()) {
                    $skipped++;
                    continue;
                }

                DB::table('COURSE_EXEMPTED')->insert([
                    'CE_SESSION_ID' => $toSessionId,
                    'CE_CO_ID' => null,
                    'CE_COURSE_ID' => $courseId,
                    'CE_COURSE_NAME' => $row->ce_course_name,
                    'CE_ENROLLMENT_NO' => $enrollmentNo !== '' ? $enrollmentNo : null,
                    'CE_STATUS' => 'E',
                    'CE_ENTRY_USERNAME' => $username,
                    'CE_CREATED_AT' => $now,
                    'CE_UPDATED_AT' => $now,
                ]);
                $inserted++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => "Import completed. Transferred: {$inserted}, Skipped (already exists): {$skipped}.",
                'inserted' => $inserted,
                'skipped' => $skipped,
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'msg' => 'Import failed: '.$e->getMessage(),
            ]);
        }
    }
}
