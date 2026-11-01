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

class SurveyAdminController extends Controller
{
    private const GRIEVANCE_MAX_FILES = 10;

    private const GRIEVANCE_FILE_MIMES = 'pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,webp,mp4,mov,csv';

    private const GRIEVANCE_FILE_DELIMITER = '|';

    private const GRIEVANCE_ENCODED_FILE_NAMES_MAX = 200;

    private const GRIEVANCE_ADMIN_REMARKS_MAX = 3900;

    /**
     * Maps sf_type -> announcement_details.ad_a_id, so that deleting a
     * student_feedbacks submission can also clean up its corresponding
     * announcement_details row. Add more sf_type => ad_a_id pairs here
     * as they're confirmed; types not listed are simply skipped.
     */
    private const SURVEY_ANNOUNCEMENT_IDS = [
        'hostel-consent-form' => 21,
    ];

    /**
     * Page shell: dropdown of survey types + (empty until a type is
     * chosen) DataTable container. View: surveys.survey-admin
     */
    public function allSurveysAdmin()
    {
        $types = DB::table('student_feedbacks')
            ->select('sf_type', 'sfd_title')
            ->whereNotNull('sf_type')
            ->where('sf_type', '!=', '')
            ->groupBy('sf_type', 'sfd_title')
            ->orderBy('sfd_title')
            ->get();

        return view('surveys.survey-admin', compact('types'));
    }

    /**
     * AJAX: returns submissions for the selected sf_type, for the DataTable.
     * Now also includes each submission's decoded sfd_desc fields (as
     * "fields") so the table/export (Copy, Excel, Print) can include the
     * full submission content, not just the summary columns.
     * GET /student-surveys/admin/data?sf_type=...
     */
    public function manageSurveysData(Request $request)
    {
        $sfType = trim((string) $request->query('sf_type', ''));

        if ($sfType === '') {
            return response()->json([
                'success' => false,
                'message' => 'Survey type is required.',
            ], 422);
        }

        $records = DB::table('student_feedbacks')
            ->where('sf_type', $sfType)
            ->orderBy('id', 'DESC')
            ->get();

        $data = $records->map(function ($record) {
            $fields = json_decode((string) $record->sfd_desc, true);
            if (!is_array($fields)) {
                $fields = [];
            }

            return [
                'id' => $record->id,
                'enrollment_no' => $record->sai_enrollment_no,
                'title' => $record->sfd_title,
                'created_at' => $record->entry_date_time
                    ? Carbon::parse($record->entry_date_time)->format('d M Y, h:i A')
                    : null,
                'fields' => $fields,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * AJAX: full details of a single submission (used by View and Edit modals).
     * GET /student-surveys/admin/{id}
     */
    public function manageSurveysShow($id)
    {
        $record = DB::table('student_feedbacks')->where('id', $id)->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found.',
            ], 404);
        }

        $fields = json_decode((string) $record->sfd_desc, true);
        if (!is_array($fields)) {
            $fields = [];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $record->id,
                'enrollment_no' => $record->sai_enrollment_no,
                'sf_type' => $record->sf_type,
                'title' => $record->sfd_title,
                'created_at' => $record->entry_date_time,
                'fields' => $fields,
            ],
        ]);
    }

    /**
     * AJAX: updates the editable fields (stored as JSON in sfd_desc).
     * PUT /student-surveys/admin/{id}
     *
     * The edit modal is generic/dynamic — one text input per key found in
     * sfd_desc — so this works for every survey type without per-survey
     * edit forms. Branch on $record->sf_type here if a specific survey
     * type needs stricter validation.
     */
    public function manageSurveysUpdate(Request $request, $id)
    {
        $record = DB::table('student_feedbacks')->where('id', $id)->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found.',
            ], 404);
        }

        $fields = $request->input('fields', []);

        if (!is_array($fields)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data format.',
            ], 422);
        }

        foreach ($fields as $key => $value) {
            if (is_string($value)) {
                $fields[$key] = trim($value);
            }
        }

        DB::table('student_feedbacks')
            ->where('id', $id)
            ->update([
                'sfd_desc' => json_encode($fields, JSON_UNESCAPED_UNICODE),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Submission updated successfully.',
        ]);
    }

    /**
     * AJAX: deletes a single submission permanently. Also removes the
     * matching announcement_details row (by enrollment no + the survey's
     * announcement id), when one is configured for the submission's
     * sf_type in SURVEY_ANNOUNCEMENT_IDS.
     * DELETE /student-surveys/admin/{id}
     */
    public function manageSurveysDestroy($id)
    {
        $record = DB::table('student_feedbacks')->where('id', $id)->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found.',
            ], 404);
        }

        DB::transaction(function () use ($record, $id) {
            $adAId = self::SURVEY_ANNOUNCEMENT_IDS[$record->sf_type] ?? null;

            if ($adAId !== null && !empty($record->sai_enrollment_no)) {
                DB::table('announcement_details')
                    ->where('ad_enrolment', $record->sai_enrollment_no)
                    ->where('ad_a_id', $adAId)
                    ->delete();
            }

            DB::table('student_feedbacks')->where('id', $id)->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Submission deleted successfully.',
        ]);
    }
}