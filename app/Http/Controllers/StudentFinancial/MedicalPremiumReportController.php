<?php

namespace App\Http\Controllers\StudentFinancial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use DataTables;

class MedicalPremiumReportController extends Controller
{
    public function index()
    {
        // Retrieve sessions to display in the dropdown filter
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
        $user = Auth::user()->username ?? 'admin';

        return view('StudentFinancial.medical_premium_report', compact('sessions', 'user'));
    }

    public function getReportData(Request $request)
    {
        if (empty($request->session_id)) {
            return DataTables::of([])->toJson();
        }

        $sessionCondition = "";
        $admissionTypeCondition = "";
        $studentStatusCondition = "";
        $challanTypeCondition = "";

        if (!empty($request->session_id)) {
            $sessionCondition = " AND student_challan.sc_session_id = '{$request->session_id}' AND students_semester_record.SESS_ID = '{$request->session_id}'";
        }

        if (!empty($request->admission_type)) {
            $admissionTypeCondition = " AND student_challan.SC_STUDENT_STATUS = '{$request->admission_type}'";
        }

        if (!empty($request->student_status)) {
            if ($request->student_status === 'Struck off') {
                $studentStatusCondition = " AND students_apply_info.SAI_STUDENT_STATUS IN ('Struck off', 'Stuck-Off')";
            } else {
                $studentStatusCondition = " AND students_apply_info.SAI_STUDENT_STATUS = '{$request->student_status}'";
            }
        }

        if (!empty($request->challan_type)) {
            $challanTypeCondition = " AND student_challan.sc_challan_status = '{$request->challan_type}'";
            if ($request->challan_type === 'PAID') {
                if (!empty($request->from_date)) {
                    $fromDate = date('Y-m-d', strtotime($request->from_date));
                    $challanTypeCondition .= " AND student_challan.callback_paid_on >= TO_DATE('{$fromDate}', 'YYYY-MM-DD')";
                }
                if (!empty($request->to_date)) {
                    $toDate = date('Y-m-d', strtotime($request->to_date . ' +1 day'));
                    $challanTypeCondition .= " AND student_challan.callback_paid_on < TO_DATE('{$toDate}', 'YYYY-MM-DD')";
                }
            }
        }

        $query = "
            SELECT 
                sc_enrollment_no as REGNO,
                SPI_NAME as Name,
                SPI_GENDER as Gender,
                SPI_NIC as CNIC,
                SPI_DOB as DOB,
                TRUNC(MONTHS_BETWEEN(SYSDATE, SPI_DOB) / 12) AS Age,
                sai_student_status as student_status,
                SSR_SEMESTER_STATUS as semester_status,
                School_code,
                DEPT_NAME,
                PROG_NAME,
                sc_app_id as app_id,
                SC_CHALLAN_STATUS as challan_Status,
                FFS_MEDICAL_AMT as MeDiCaL_amount,
                SC_STUDENT_STATUS as std_status 
            FROM student_challan
            JOIN students_apply_info ON sai_enrollment_no = sc_enrollment_no
            JOIN students_personal_info ON SAI_SPI_ID = spi_id
            JOIN students_semester_record ON students_semester_record.sai_enrollment_no = students_apply_info.SAI_ENROLLMENT_NO
            JOIN schools ON sai_sdpo_school_id = school_id
            JOIN departments ON sai_sdpo_dept_id = dept_id
            JOIN programs ON sai_sdpo_prog_id = prog_id
            WHERE FFS_MEDICAL_AMT > 0
            $sessionCondition
            $admissionTypeCondition
            $studentStatusCondition
            $challanTypeCondition
        ";

        $data = DB::select($query);

        return DataTables::of($data)->toJson();
    }
}
