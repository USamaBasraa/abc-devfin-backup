<?php
/**
 * Created by PhpStorm.
 * User: Maher Asad
 * Date: 5/14/2019
 * Time: 9:52 AM
 */
namespace App\Http\Controllers\StudentFinancial;

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

class HostelFeeStructureController extends Controller
{

    function masterHostelFeeForm()
    {
        $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');

        return view('StudentFinancial.master_hostelFee_structure', compact('sessions'));

    }
    public function add(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [

            'session' => 'required',
            'session_type' => 'required',
            'student_type' => 'required',
            'effective_from' => 'required',
            'effective_to' => 'required',
            'amount_per_day' => 'required',
            'amount_per_month' => 'required',
            'amount_per_semester' => 'required',
            'refrigerator_fee' => 'required',
            'hostel_security_fee' => 'required',
            'heater_fee' => 'required',
            'ac_fee' => 'required',

        ], [
            'session.required' => 'The session field is required.',
            'session_type.required' => 'The session type field is required.',
            'student_type.required' => 'The student type field is required.',
            'effective_from.required' => 'The effective from date is required.',
            'effective_to.required' => 'The effective to date is required.',
            'amount_per_day.required' => 'The amount per day is required.',
            'amount_per_month.required' => 'The amount per month is required.',
            'amount_per_semester.required' => 'The amount per semester is required.',
            'refrigerator_fee.required' => 'The refrigerator fee is required.',
            'hostel_security_fee.required' => 'The Hostel Security Fee is required.',
            'heater_fee.required' => 'The heater fee is required.',
            'ac_fee.required' => 'The air conditioner fee is required.',
        ]);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'error' => $validator->getMessageBag()->toArray()
            ), 400);
        }
        $userId = auth()->user()->id;
        $resp = [];
        $data = [
            'FHFS_SESSION' => $request->get('session'),
            'fhfs_session_type' => $request->get('session_type'),
            'FHFS_EFFECTIVE_FROM' => $request->get('effective_from'),
            'FHFS_EFFECTIVE_TO' => $request->get('effective_to'),
            'FHFS_AMOUNT_PER_DAY' => $request->get('amount_per_day'),
            'FHFS_AMOUNT_PER_MONTH' => $request->get('amount_per_month'),
            'FHFS_AMOUNT_PER_SEMESTER' => $request->get('amount_per_semester'),
            'FHFS_REFRIGERATOR_FEE' => $request->get('refrigerator_fee'),
            'FHFS_HEATER_FEE' => $request->get('heater_fee'),
            'FHFS_AC_FEE' => $request->get('ac_fee'),
            'FHFS_hostel_security_fee' => $request->get('hostel_security_fee'),
            'FHFS_ENTRY_USER_ID' => $userId,
            'FHFS_ENTRY_DATE' => now(),
            'FHFS_STATUS' => 'E',
            'FHFS_STUDENT_TYPE' => $request->get('student_type'),
        ];

        if (empty($request->mainId)) {
            $duplicateCheck = DB::table('fin_hostel_fee_structure')
                ->select(
                    'FHFS_ID',
                    'FHFS_SESSION',
                    'fhfs_session_type',
                    'FHFS_EFFECTIVE_FROM',
                    'FHFS_EFFECTIVE_TO'
                )
                ->where('FHFS_SESSION', $request->get('session'))
                ->where('fhfs_session_type', $request->get('session_type'))
                // ->where('FHFS_EFFECTIVE_FROM', $request->get('effective_from'))
                // ->where('FHFS_EFFECTIVE_TO', $request->get('effective_to'))
                ->where('FHFS_STUDENT_TYPE', $request->get('student_type'))
                ->where('FHFS_STATUS', 'E')
                ->get();
            //  dd($duplicateCheck);
            if (sizeof($duplicateCheck) == 0) {//insert
                DB::table('fin_hostel_fee_structure')->insert($data);
                $resp[0] = true;
                $resp[1] = 'Saved Successfully';
            } else {//update / overwrite
                $existingId = $duplicateCheck[0]->fhfs_id;
                $challanSummary = $this->getHostelFeeStructureChallanSummary($existingId);
                if (!empty($challanSummary['has_challan'])) {
                    return response()->json([
                        'success' => false,
                        'msg' => 'Overwrite not allowed. Challan(s) already exist against this hostel fee structure.',
                        'has_challan' => true,
                        'paid_count' => $challanSummary['paid_count'],
                        'unpaid_count' => $challanSummary['unpaid_count'],
                        'total_count' => $challanSummary['paid_count'] + $challanSummary['unpaid_count'],
                        'locked_ids' => [$existingId],
                    ]);
                }
                DB::table('fin_hostel_fee_structure')
                    ->where('FHFS_ID', $existingId)
                    ->update($data);
                $resp[0] = true;
                $resp[1] = 'Updated Successfully';
            }
        } else {//edit
            // dd($request->mainId);
            if ($this->hostelFeeStructureHasChallanById($request->mainId)) {
                $challanSummary = $this->getHostelFeeStructureChallanSummary($request->mainId);
                return response()->json([
                    'success' => false,
                    'msg' => 'Cannot edit: challan(s) already exist against this hostel fee structure.',
                    'has_challan' => true,
                    'paid_count' => $challanSummary['paid_count'],
                    'unpaid_count' => $challanSummary['unpaid_count'],
                    'total_count' => $challanSummary['paid_count'] + $challanSummary['unpaid_count'],
                    'locked_ids' => [$request->mainId],
                ]);
            }
            $duplicateCheck = DB::table('fin_hostel_fee_structure')
                ->select(
                    'FHFS_ID',
                    'FHFS_SESSION',
                    'fhfs_session_type',
                    'FHFS_EFFECTIVE_FROM',
                    'FHFS_EFFECTIVE_TO'
                )
                ->where('FHFS_SESSION', $request->get('session'))
                ->where('fhfs_session_type', $request->get('session_type'))
                // ->where('FHFS_EFFECTIVE_FROM', $request->get('effective_from'))
                // ->where('FHFS_EFFECTIVE_TO', $request->get('effective_to'))
                ->where('FHFS_STUDENT_TYPE', $request->get('student_type'))
                ->where('FHFS_STATUS', 'E')
                ->where('FHFS_ID', '!=', $request->mainId)
                ->get();
            //  dd($duplicateCheck);
            if (sizeof($duplicateCheck) == 0) {
                DB::table('fin_hostel_fee_structure')
                    ->where('FHFS_ID', $request->mainId)
                    ->update($data);
                $resp[0] = true;
                $resp[1] = 'Edited Successfully';
            } else {
                $resp[0] = false;
                $resp[1] = 'Data already exists';
            }
        }
        return response()->json([
            'success' => $resp[0],
            'msg' => $resp[1],
        ]);

    }

    protected function getMasterFeeList(Request $request)
    {
        $data = DB::table('fin_hostel_fee_structure as fhfs')
            ->select(
                'fhfs.FHFS_ID',
                'fhfs.FHFS_SESSION',
                'fhfs.FHFS_EFFECTIVE_FROM',
                'fhfs.FHFS_EFFECTIVE_TO',
                'fhfs.FHFS_AMOUNT_PER_DAY',
                'fhfs.FHFS_AMOUNT_PER_MONTH',
                'fhfs.FHFS_AMOUNT_PER_SEMESTER',
                'fhfs.FHFS_REFRIGERATOR_FEE',
                'fhfs.FHFS_HEATER_FEE',
                'fhfs.FHFS_AC_FEE',
                'fhfs.FHFS_hostel_security_fee',
                'acd1.SESS_NAME as SESS_NAME',
                'fhfs.fhfs_session_type',
                'fhfs.fhfs_student_type'
            )
            ->join('ACADEMIC_SESSIONS as acd1', 'acd1.SESS_ID', '=', 'fhfs.FHFS_SESSION')
            ->where('fhfs.FHFS_STATUS', '=', 'E');
        if (!empty($request->session_sel)) {
            $data = $data->where('fhfs.FHFS_SESSION', $request->session_sel);
        }
        if (!empty($request->session_type)) {
            $data = $data->where('fhfs.fhfs_session_type', $request->session_type);
        }
        if (!empty($request->student_type)) {
            $data = $data->where('fhfs.fhfs_student_type', $request->student_type);
        }
        if (!empty($request->eff_from)) {
            $data = $data->where('fhfs.FHFS_EFFECTIVE_FROM', $request->eff_from);
        }
        if (!empty($request->eff_to)) {
            $data = $data->where('fhfs.FHFS_EFFECTIVE_TO', $request->eff_to);
        }
        if (!empty($request->amount_per_day)) {
            $data = $data->where('fhfs.FHFS_AMOUNT_PER_DAY', $request->amount_per_day);
        }
        if (!empty($request->amount_per_month)) {
            $data = $data->where('fhfs.FHFS_AMOUNT_PER_MONTH', $request->amount_per_month);
        }
        if (!empty($request->amount_per_semester)) {
            $data = $data->where('fhfs.FHFS_AMOUNT_PER_SEMESTER', $request->amount_per_semester);
        }
        if (!empty($request->refrigerator_fee)) {
            $data = $data->where('fhfs.FHFS_REFRIGERATOR_FEE', $request->refrigerator_fee);
        }
        if (!empty($request->heater_fee)) {
            $data = $data->where('fhfs.FHFS_HEATER_FEE', $request->heater_fee);
        }
        if (!empty($request->ac_fee)) {
            $data = $data->where('fhfs.FHFS_AC_FEE', $request->ac_fee);
        }
        $data = $data->orderBy('fhfs.FHFS_ID', 'desc')->get();

        // dd($data);
        return DataTables::of($data)->toJson();
    }
    public function delete_feeData(Request $request)
    {
        $userId = auth()->user()->id;
        $ids = is_array($request->mainId) ? $request->mainId : [$request->mainId];
        $ids = array_values(array_filter($ids, function ($id) {
            return $id !== null && $id !== '';
        }));
        $lockedIds = [];
        foreach ($ids as $id) {
            if ($this->hostelFeeStructureHasChallanById($id)) {
                $lockedIds[] = $id;
            }
        }
        if (count($lockedIds) === count($ids)) {
            return response()->json([
                'success' => false,
                'data' => 'Cannot delete: challan(s) already exist against selected hostel fee structure(s)',
                'has_challan' => true,
                'locked_ids' => $lockedIds,
            ]);
        }
        $deletableIds = array_values(array_diff($ids, $lockedIds));
        $affectedRows = DB::table('fin_hostel_fee_structure')
            ->whereIn('FHFS_ID', $deletableIds)
            ->update([
                'FHFS_STATUS' => 'D',
                'FHFS_ENTRY_USER_ID' => $userId,
            ]);
        if ($affectedRows > 0) {
            $msg = 'Deleted Successfully';
            if (count($lockedIds) > 0) {
                $msg .= '. ' . count($lockedIds) . ' record(s) skipped because challan(s) exist';
            }
            return response()->json([
                'success' => true,
                'data' => $msg,
                'locked_ids' => $lockedIds,
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Some problem occured'
        ]);
    }

    public function checkHostelFeeStructureChallan(Request $request)
    {
        $ids = is_array($request->mainId) ? $request->mainId : [$request->mainId];
        $ids = array_values(array_filter($ids, function ($id) {
            return $id !== null && $id !== '';
        }));
        $lockedIds = [];
        $details = [];
        $totalPaid = 0;
        $totalUnpaid = 0;
        foreach ($ids as $id) {
            $summary = $this->getHostelFeeStructureChallanSummary($id);
            if ($summary['has_challan']) {
                $lockedIds[] = $id;
                $totalPaid += $summary['paid_count'];
                $totalUnpaid += $summary['unpaid_count'];
                $details[] = [
                    'fhfs_id' => $id,
                    'paid_count' => $summary['paid_count'],
                    'unpaid_count' => $summary['unpaid_count'],
                    'total_count' => $summary['paid_count'] + $summary['unpaid_count'],
                ];
            }
        }
        return response()->json([
            'success' => true,
            'has_challan' => count($lockedIds) > 0,
            'locked_ids' => $lockedIds,
            'paid_count' => $totalPaid,
            'unpaid_count' => $totalUnpaid,
            'total_count' => $totalPaid + $totalUnpaid,
            'details' => $details,
        ]);
    }

    private function hostelFeeStructureHasChallanById($fhfsId)
    {
        $summary = $this->getHostelFeeStructureChallanSummary($fhfsId);
        return $summary['has_challan'];
    }

    /**
     * Hostel challans are matched on session + session type (case-insensitive).
     * Student type is not stored on STUDENT_CHALLAN, so Existing/New_Admission
     * rows for the same session+type share the same lock.
     */
    private function getHostelFeeStructureChallanSummary($fhfsId)
    {
        $row = DB::table('fin_hostel_fee_structure')
            ->where('FHFS_ID', $fhfsId)
            ->where('FHFS_STATUS', 'E')
            ->first();
        if (!$row) {
            return [
                'has_challan' => false,
                'paid_count' => 0,
                'unpaid_count' => 0,
            ];
        }

        $sessionId = $row->fhfs_session ?? $row->FHFS_SESSION ?? null;
        $sessionType = $row->fhfs_session_type ?? $row->FHFS_SESSION_TYPE ?? null;
        $studentType = $row->fhfs_student_type ?? $row->FHFS_STUDENT_TYPE ?? null;
        
        $studentType = $studentType == 'New_Admission' ? 'New Admission' : 'Existing';

        $query = DB::table('STUDENT_CHALLAN')
            ->where('SC_CHALLAN_TYPE', 'Hostel_Fee')
            ->where('SC_SESSION_ID', $sessionId)
            ->whereRaw("UPPER(TRIM(SC_CHALLAN_STATUS)) IN ('PAID', 'UNPAID')");

        if($studentType == 'New Admission'){
            $query->where('SC_STUDENT_STATUS', 'LIKE', '%New Admission%');
        } else {
            $query->where('SC_STUDENT_STATUS', 'LIKE', '%Existing%');
        }

        if ($sessionType === null || $sessionType === '') {
            $query->where(function ($q) {
                $q->whereNull('SC_SESSION_TYPE')
                    ->orWhereRaw("TRIM(SC_SESSION_TYPE) = ''");
            });
        } else {
            $query->whereRaw(
                'UPPER(TRIM(SC_SESSION_TYPE)) = ?',
                [strtoupper(trim($sessionType))]
            );
        }
        
        $counts = $query->selectRaw("
                SUM(CASE WHEN UPPER(TRIM(SC_CHALLAN_STATUS)) = 'PAID' THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN UPPER(TRIM(SC_CHALLAN_STATUS)) = 'UNPAID' THEN 1 ELSE 0 END) as unpaid_count
            ")
            ->first();

        $paid = (int) ($counts->paid_count ?? $counts->PAID_COUNT ?? 0);
        $unpaid = (int) ($counts->unpaid_count ?? $counts->UNPAID_COUNT ?? 0);

        return [
            'has_challan' => ($paid + $unpaid) > 0,
            'paid_count' => $paid,
            'unpaid_count' => $unpaid,
        ];
    }

}
