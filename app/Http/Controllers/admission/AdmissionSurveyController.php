<?php

namespace App\Http\Controllers\admission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmissionSurveyController extends Controller
{
    public function index(Request $request)
    {

        // Session dropdown
        $sessions = DB::table('ACADEMIC_SESSIONS')
            ->select('SESS_ID','SESS_NAME')
            ->orderBy('SESS_ID','desc')
            ->get();

        $surveys = [];

        if ($request->sess_id) {

            $surveys = DB::table('admission_survey')
                ->where('SESS_ID', $request->sess_id)
                ->get();
        }
        // dd($sessions);

        return view('admission.survey', compact('sessions','surveys'));

    }
}