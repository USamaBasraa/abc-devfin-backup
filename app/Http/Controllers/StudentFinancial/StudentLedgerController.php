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

class StudentLedgerController extends Controller{

    function studentLedger(){
        $sessions = DB::select("select * from ACADEMIC_SESSIONS where SESS_STATUS = 'E' order by SESS_ID desc");
        // dd($sessions);
        return view('StudentFinancial.studentLedger',compact('sessions'));

    }
    public function getLedger(Request $request)
    {
        // dd($request->all());
        if ($request->challanType == 'all') {
            // Combine data from both methods
            $data1 = $this->getFeeSummaryData($request);
            $data2 = $this->getDataBefore2024($request);
            // dd($data1, $data2);
            // Combine the results (ensuring they are arrays)
            $data = array_merge($data1, $data2);
            // dd($data);
        } else {
            // Fetch data only from getFeeSummaryData
            $data = $this->getFeeSummaryData($request);
        }
        return DataTables::of($data)->toJson();
    }

    protected function getFeeSummaryData(Request $request)
    {
        // dd($request->enrollment);
        if($this->isEnrollmentNo($request->enrollment)){
            $sql = "SELECT
                    case when challan_no <= 185088 then sc_feechallanno else
                    sc.challan_no       end         AS buyercodechallanno,
                    sc.id as cms_challan_id,
                    sc.sc_enrollment_no         AS regno,
                    sc.sc_session_id            AS session_id,
                    sess.sess_name              AS session_name,
                    sc.sc_paid_on               AS paidon,
                    sc.callback_paid_on         AS callback_paid_on,
                    sc.sc_app_id                AS pvnoslipnostudentid,
                    sc.total_amount_payable     AS totalamountpayable,
                    sc.sc_issu_date             AS issuedate,
                    sc.sc_challan_status        AS challanstatus,
                    sc.sc_challan_type          AS sc_challan_type,
                     (select fc_category_name from fine_category where sc_category_id = fine_category.id) as finetype,
                    sc.invoiceamount            AS totalamountpayable,
                    nvl(sc.callback_amount, 0)  AS paidamt,
                    sc.sc_due_date              AS duedate,
                    sc.sc_validate_date         AS validdate,
                    sc.sc_student_status        AS studenttype,
                    sc.sc_student_status        AS studenttypenew,
                    spi.spi_fathername          AS parentguardianname,
                    sc.check_fee_installments AS installments,
                    sch.school_name             AS school,
                    dept.dept_name              AS department,
                    prog.prog_name              AS program,
                    spi.spi_name                AS studentname,
                    spi.spi_guardian_cnic       AS parentguardiancnic,
                    spi.spi_permanentaddress    AS permanentaddress,
                    sai.sai_semester            AS semester,
                    sai.sai_student_status      AS sai_student_status
                FROM
                    student_challan          sc
                    LEFT JOIN students_apply_info      sai ON sai.sai_enrollment_no = sc.sc_enrollment_no
                    LEFT JOIN students_personal_info   spi ON spi.spi_id = sai.sai_spi_id
                    INNER JOIN academic_sessions        sess ON sess.sess_id = sc.sc_session_id
                    INNER JOIN schools                  sch ON sch.school_id = sai.sai_sdpo_school_id
                    INNER JOIN departments              dept ON dept.dept_id = sai.sai_sdpo_dept_id
                    INNER JOIN programs                 prog ON prog.prog_id = sai.sai_sdpo_prog_id

                    where sc.SC_CHALLAN_STATUS != 'CANCELLED' and sc.INVOICE_STATUS = 'E'
                ";
                // Add filter based on challanType
                if ($request->challanType == 'paid') {
                    $sql .= "and sc.sc_challan_status = 'PAID'";
                } elseif ($request->challanType == 'unpaid') {
                    $sql .= "and sc.sc_challan_status = 'UNPAID'";
                }

                if (!empty($request->enrollment)) {
                    $sql .= "and  TO_CHAR(sc.sc_enrollment_no) = UPPER('$request->enrollment')";
                }
            //    echo  $sql;exit;
        }else{
            $sql = "SELECT
                     case when challan_no <= 185088 then sc_feechallanno else
                    sc.challan_no              end         AS buyercodechallanno,
                    sc.id as cms_challan_id,
                    sc.sc_enrollment_no         AS regno,
                    sc.sc_session_id            AS session_id,
                    sess.sess_name              AS session_name,
                    sc.sc_paid_on               AS paidon,
                    sc.callback_paid_on         AS callback_paid_on,
                    sc.sc_app_id                AS pvnoslipnostudentid,
                    sc.total_amount_payable     AS totalamountpayable,
                    sc.sc_issu_date             AS issuedate,
                    sc.sc_challan_status        AS challanstatus,
                    sc.sc_challan_type          AS sc_challan_type,
                    (select fc_category_name from fine_category where sc_category_id = fine_category.id) as finetype,
                    sc.invoiceamount            AS totalamountpayable,
                    nvl(sc.callback_amount, 0)  AS paidamt,
                    sc.sc_due_date              AS duedate,
                    sc.sc_validate_date         AS validdate,
                    sc.sc_student_status        AS studenttype,
                    sc.sc_student_status        AS studenttypenew,
                    api.api_fathername          AS parentguardianname,
                    sc.check_fee_installments   AS installments,
                    sch.school_name             AS school,
                    dept.dept_name              AS department,
                    prog.prog_name              AS program,
                    api.api_name                AS studentname,
                    api.api_guardian_cnic       AS parentguardiancnic,
                    api.api_permanentaddress    AS permanentaddress,
                    NULL AS semester,
                    aai.aai_status      AS sai_student_status
                FROM
                    student_challan           sc
                    LEFT JOIN admission_apply_info      aai ON aai.aai_id = sc.sc_app_id
                    LEFT JOIN admission_personal_info   api ON api.api_id = aai.aai_api_id
                    INNER JOIN academic_sessions        sess ON sess.sess_id = sc.sc_session_id
                    INNER JOIN schools                  sch ON sch.school_id = aai.AAI_SDPO_SCHOOL_ID
                    INNER JOIN departments              dept ON dept.dept_id = aai.AAI_SDPO_DEPT_ID
                    INNER JOIN programs                 prog ON prog.prog_id = aai.AAI_SDPO_PROG_ID

                    where sc.SC_CHALLAN_STATUS != 'CANCELLED' and sc.INVOICE_STATUS = 'E'
                ";
                // Add filter based on challanType
                if ($request->challanType == 'paid') {
                    $sql .= "and sc.sc_challan_status = 'PAID'";
                } elseif ($request->challanType == 'unpaid') {
                    $sql .= "and sc.sc_challan_status = 'UNPAID'";
                }

                if (!empty($request->enrollment)) {
                    $sql .= "and TO_CHAR(sc.sc_app_id) = UPPER('$request->enrollment') ";
                }
        }
        //    echo  $sql;exit;
        $data = DB::select($sql);
        return $data;
    }

    protected function getDataBefore2024(Request $request)
    {
        $data = DB::table('mv_feechallanwisesummary as mf')
        ->select(
            'mf.REGNO',
            'mf.SESSION_ID',
            // DB::raw("'N/A' as cms_challan_id"),
            'sess.SESS_NAME AS SESSION_NAME',
            'mf.PAIDON',
            DB::raw("CASE WHEN mf.PAIDON IS NOT NULL THEN 'PAID' ELSE 'UNPAID' END AS CHALLANSTATUS"),

            'mf.BUYERCODECHALLANNO',
            'mf.TOTALAMOUNTPAYABLE',
            'mf.PAIDAMT',
            'mf.ISSUEDATE',
            'mf.DUEDATE',
            'mf.VALIDDATE',
            'mf.INSTALLMENTS',
            'mf.PVNOSLIPNOSTUDENTID',
            'mf.CREATIONDATE',
            'mf.STUDENTNAME',
            'mf.PARENTGUARDIANNAME',
            'mf.PROGRAM',
            'mf.SCHOOL',
            'mf.DEPARTMENT',
            'mf.STUDENTTYPE',
            'mf.STUDENTTYPENEW',
            // 'mf.FEETYPESUMMARY',
            'mf.PERMANENTADDRESS',
            'mf.SEMESTER',
            'mf.PARENTGUARDIANCNIC',
            DB::raw("123 as sai_student_status")
        )
        ->leftjoin('academic_sessions as sess', 'mf.SESSION_ID', 'sess.sess_code');

        // Add where clauses based on request parameters
        if (!empty($request->enrollment)) {
            $data = $data->where(function ($query) use ($request) {
                $query->where('mf.REGNO', strtoupper($request->enrollment))
                      ->orWhere('mf.PVNOSLIPNOSTUDENTID', strtoupper($request->enrollment));
            });
        }

        if (!empty($request->session_sel)) {
            $data = $data->where('mf.SESSION_ID', $request->session_sel);
        }

        if (!empty($request->effective_from)) {
            $data = $data->whereRaw("mf.CREATIONDATE >= TO_DATE(?, 'YYYY-MM-DD')", [$request->effective_from]);
        }

        if (!empty($request->effective_to)) {
            $effectiveTo = new DateTime($request->effective_to);
            $effectiveTo->modify('+1 day');
            $effectiveToFormatted = $effectiveTo->format('Y-m-d');
            $data = $data->whereRaw("mf.CREATIONDATE <= TO_DATE(?, 'YYYY-MM-DD')", [$effectiveToFormatted]);
        }

        if (!empty($request->challanType) && $request->challanType != 'all') {
            if ($request->challanType == 'paid') {
                $data = $data->whereNotNull('mf.PAIDON');
            } elseif ($request->challanType == 'unpaid') {
                $data = $data->whereNull('mf.PAIDON');
            }
        }
        $data =  $data->orderByRaw("
                    CASE
                        WHEN mf.PAIDON IS NULL THEN 1
                        ELSE 2
                    END
                ");
        return $data->limit(5000)->get()->toArray();
    }

    function ledger_challan_preview(Request $request){
        $challan_no = $request->challanNo;
        $challanType = $request->stp;
        $data='';
        if ($challanType=='old') {
            $data_qry='select
                        *
                    from MV_FEECHALLANWISESUMMARY
                    where BUYERCODECHALLANNO ='.$challan_no;
                            //echo $data_qry;exit;
                $data = DB::select($data_qry);

                return view('grader.financial.ledger_challan_preview',array('obj' => $data));
        }
        else{

            // $data_qry=' SELECT
            //         sc.id                          AS BUYERCODECHALLANNO,
            //         sc.sc_enrollment_no            AS REGNO,
            //         sc.sc_session_id               AS SESSION_ID,
            //         sc.sc_paid_on                  AS PAIDON,
            //         sc.sc_app_id                   AS PVNOSLIPNOSTUDENTID,
            //         sc.total_amount_payable        AS TOTALAMOUNTPAYABLE,
            //         sc.sc_issu_date                AS ISSUEDATE,

            //         sc.INVOICEAMOUNT                AS TOTALAMOUNTPAYABLE,
            //         NVL(sc.CALLBACK_AMOUNT, 0)            AS PAIDAMT,

            //         sc.sc_due_date                 AS DUEDATE,
            //         sc.sc_validate_date            AS VALIDDATE,
            //         sc.SC_STUDENT_STATUS            AS STUDENTTYPE,
            //         sc.SC_STUDENT_STATUS            AS studenttypenew,

            //         spi.spi_fathername           AS PARENTGUARDIANNAME,
            //         sc.check_fee_installments      AS INSTALLMENTS,
            //         sch.school_name                AS SCHOOL,
            //         dept.dept_name                 AS DEPARTMENT,
            //         prog.prog_name                 AS PROGRAM,
            //         spi.spi_name                   AS STUDENTNAME,
            //         spi.spi_guardian_cnic          AS PARENTGUARDIANCNIC,
            //         spi.spi_permanentaddress          AS PERMANENTADDRESS,
            //         sai.sai_semester               AS SEMESTER
            //     FROM student_challan sc
            //     LEFT JOIN schools sch ON sc.sc_school_id = sch.school_id
            //     LEFT JOIN departments dept ON sc.sc_department_id = dept.dept_id
            //     LEFT JOIN programs prog ON sc.sc_program_id = prog.prog_id
            //     LEFT JOIN students_apply_info sai ON sc.sc_enrollment_no = sai.sai_enrollment_no
            //     LEFT JOIN students_personal_info spi ON sai.sai_spi_id = spi.spi_id

            //         where id ='.$challan_no;
            // //echo $data_qry;exit;

            // $data = DB::select($data_qry);

            // return view('grader.financial.ledger_challan_preview',array('obj' => $data));
            $challan_no = $request->challanNo;
            // Get Personal Info and fees other then courses
            //$data_qry='select * from MV_feechallanwisesummary where BuyerCodeChallanNo='.$challan_no;
            $data_qry='select
            scholarship_type.scholarship_name,
            schools.school_name,
            dept_name,
            programs.prog_name,
            sess_name,
            student_challan.*,
            fin_scholarship_discounts.*,
            sc_surplus_amount,
            sc_activity_amt,
            sc_studentname,
            sc_fathername
             from student_challan
            LEFT join fin_scholarship_discounts on  sc_app_id=fin_scholarship_discounts.fsd_aai_id
            LEFT JOIN scholarship_type ON fin_scholarship_discounts.fsd_scholarship_type=scholarship_type.scholarship_id
            LEFT JOIN fin_fee_adjustment ON fin_fee_adjustment.ffa_enrollment_no = student_challan.sc_enrollment_no
            left join schools on school_id = sc_school_id
            left join departments on dept_id = sc_department_id
            left join programs on prog_id = sc_program_id
            left join academic_sessions on sess_id = sc_session_id
            where student_challan.id='.$challan_no;
            // echo $data_qry;exit;
            $data = DB::select($data_qry);
            // Get all courses with fee --fin_scholarship_discounts.fsd_enrollment_no=student_challan.sc_enrollment_no
            //$data_det_qry='select * from feechallanwisesummary_det@acc where "BuyerCodeChallanNo"='.$challan_no.' order by "srno" asc';
            $data_det_qry='select * from student_challan
            LEFT join fin_scholarship_discounts on sc_app_id=fin_scholarship_discounts.fsd_aai_id
            and fin_scholarship_discounts.fsd_sesion_id = student_challan.sc_session_id
            LEFT JOIN scholarship_type ON fin_scholarship_discounts.fsd_scholarship_type=scholarship_type.scholarship_id
            where id='.$challan_no.' order by id asc';
            $data_detail = DB::select($data_det_qry);
           // echo $data_qry;exit;
        //    dd($data, $data_detail);
            return view('grader.financial.print_challan_preview',array('obj' => $data,'details' => $data_detail));


        }

        // dd($data);


    }
    public function getChallanRec(Request $request){
        $data = DB::table('student_challan')
                ->select(
                        'ID',
                        'SC_ENROLLMENT_NO',
                        'CHALLAN_NO',
                        'SC_CHALLAN_STATUS',
                        'SC_CHALLAN_TYPE',
                        'INVOICEAMOUNT',
                        'INVOICE_STATUS',
                        'SC_SAI_STUDENT_STATUS',
                        'SC_STUDENT_STATUS',
                        'SC_SCHOLARSHIP_AMOUNT',
                        'SC_TUITION_FEE_AMNT as actual_amount',
                        'SC_ISSU_DATE',

                        'SC_SESSION_ID',
                        'SC_SCHOOL_ID',
                        'SC_DEPARTMENT_ID',
                        'SC_PROGRAM_ID',
                        )
                        ->where('ID', $request->studentChallanNo)
                        ->first();
        // dd($data);
        $adjustmentData = DB::table('FIN_FEE_ADJUSTMENT_CATEGORY')
                        ->select(
                                'FFAC_ID',
                                'FFAC_TITLE',
                                'FFAC_DESCRIPTION',
                                )
                                ->where('FFAC_STATUS', 'E')
                                ->get();
                        // dd($data, $adjustmentData);
         if (!empty($data)) {
            return response()->json(['success' => true, 'challanData' => $data, 'adjustmentData' => $adjustmentData]);
        }else{
            return response()->json(['success' => false, 'challanData' => $data, 'adjustmentData' => $adjustmentData]);
        }


    }
    public function addfeeAdjustment(Request $request){
        $validator = Validator::make($request->all(),[
            'surplus_amount' => 'required',
            'CategoryOption' => 'required'
        ]);
        if($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400);
        }
        $data = [
			'FFA_SC_CHALLAN_NO' => (!empty($request->challan_no)? $request->challan_no : NULL),//
			'FFA_ENROLLMENT_NO' =>$request->enrollment_no,
			'FFA_ADJUSTMENT_CATEGORY' =>$request->CategoryOption,
			'FFA_ACTUAL_AMOUNT' =>(!empty($request->actual_amount)? ($request->actual_amount != 'null' ? $request->actual_amount : 0) : 0),//
			'FFA_INVOICE_AMOUNT' =>(!empty($request->invoice_amount)? ($request->invoice_amount != 'null' ? $request->invoice_amount : 0) : 0),//
			'FFA_SURPLUS_AMOUNT' =>$request->surplus_amount,
			'FFA_SCHOLARSHIP_AMOUNT' =>(!empty($request->scholarship_amount)? ($request->scholarship_amount != 'null' ? $request->scholarship_amount : 0) : 0),//
			'FFA_REMARKS' =>$request->remarks,
			'FFA_CHALLAN_STATUS' =>(!empty($request->challan_status)? $request->challan_status : NULL),//
			'FFA_CHALLAN_TYPE' =>(!empty($request->challan_type)? $request->challan_type : 'fee'),//
			'FFA_STUDENT_STATUS' =>$request->student_status,
			'FFA_CREATED_AT' => now(),
        ];
        DB::beginTransaction();
        $mesg='';
        try {
            $duplicateCheck = DB::table('FIN_FEE_ADJUSTMENT')
                            ->select(
                                    'FFA_ID'
                                    )
                                    ->where('FFA_ENROLLMENT_NO', $request->enrollment_no)
                                    ->get();
            if(sizeof($duplicateCheck) == 0){
                //INSERT CASE
                $check = DB::table('FIN_FEE_ADJUSTMENT')->insert($data);
                if($check){
                    $mesg = 'Saved Successfully';
                }
            }else{
                //UPDATE CASE
                $check = DB::table('FIN_FEE_ADJUSTMENT')
                        ->where('FFA_ID', $duplicateCheck[0]->ffa_id)
                        ->update($data);
                if($check){
                    $mesg = 'Updated Successfully';
                }
            }
            // Commit the transaction
            DB::commit();
            return response()->json([
                'success' => true,
                'msg' => $mesg
            ]);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Log the error
            // \Log::error('Error: ' . $e->getMessage());

            // Return a failure response
            return response()->json([
                'success' => false,
                'msg' => 'Something went wrong',
            ]);
        }
    }
    public function getFeeAdjustmentList(){
        $data = DB::table('FIN_FEE_ADJUSTMENT as ffa')
            ->select(
                    'ffa.FFA_ID',
                    'ffa.FFA_SC_CHALLAN_NO',
                    'ffa.FFA_ENROLLMENT_NO',
                    'ffa.FFA_SURPLUS_AMOUNT',
                    'ffa.FFA_REMARKS',
                    'ffa.FFA_ACTUAL_AMOUNT',
                    'ffa.FFA_INVOICE_AMOUNT',
                    'ffa.FFA_CHALLAN_STATUS',
                    'ffa.FFA_STUDENT_STATUS',
                    'ffa.FFA_CHALLAN_TYPE',
                    'ffa.FFA_SCHOLARSHIP_AMOUNT',

                    'ffac.FFAC_ID',
                    'ffac.FFAC_TITLE',
                    'ffc.NAME'
                    )
                    ->leftjoin('FIN_FEE_ADJUSTMENT_CATEGORY as ffac', 'ffac.FFAC_ID', 'ffa.FFA_ADJUSTMENT_CATEGORY')
                    ->leftjoin('FIN_FEE_CATEGORY as ffc', 'ffc.ID', 'ffac.FFAC_FEE_CTGRY')
                    ->where('FFA_STATUS', 'E')
                    ->orderBy('ffa.FFA_CREATED_AT', 'desc')
                    ->get();
                // dd($data);
            return DataTables::of($data)->toJson();
    }
    public function changeStatusFeeAdjustment(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(),[
            'status' => 'required'
        ]);
        if($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400);
        }
        $affectedRows =  DB::table('FIN_FEE_ADJUSTMENT')
                            ->where('FFA_ID', $request->main_id)
                            ->update([
                                'FFA_STATUS' => 'D',
                                'FFA_STATUS_AMOUNT' => $request->status,
                                'FFA_STATUS_REMARKS' => $request->status_remarks
                            ]);
        if ($affectedRows > 0) {
            return response()->json([
                'success' => true,
                'data' => 'Updated Successfully'
        ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Some problem occured'
            ]);
        }

    }
    public function getScholarshipDetail(Request $request)
    {
        $data = $this->getScholarshipDetailData($request);
        return DataTables::of($data)->toJson();
    }

    protected function getScholarshipDetailData(Request $request)
    {
        $enrollment = strtoupper(trim($request->enrollment ?? ''));
        if ($enrollment === '') {
            return [];
        }

        if ($this->isEnrollmentNo($enrollment)) {
            $data = $this->queryScholarshipForEnrolledStudent($enrollment, $request);
            if (count($data) === 0) {
                $data = $this->queryScholarshipForAdmissionStudent($enrollment, $request, 'enrollment');
            }
        } else {
            $data = $this->queryScholarshipForAdmissionStudent($enrollment, $request, 'app_id');
        }

        return $data;
    }

    protected function scholarshipFilterSql(Request $request)
    {
        $filters = '';

        if (!empty($request->session_sel)) {
            $sessionSel = str_replace("'", "''", $request->session_sel);
            $filters .= " AND sess.sess_code = '{$sessionSel}'";
        }

        if (!empty($request->effective_from)) {
            $effectiveFrom = str_replace("'", "''", $request->effective_from);
            $filters .= " AND ssd.changed_on >= TO_DATE('{$effectiveFrom}', 'YYYY-MM-DD')";
        }

        if (!empty($request->effective_to)) {
            $effectiveTo = str_replace("'", "''", $request->effective_to);
            $filters .= " AND ssd.changed_on < TO_DATE('{$effectiveTo}', 'YYYY-MM-DD') + 1";
        }

        return $filters;
    }

    protected function queryScholarshipForEnrolledStudent($enrollment, Request $request)
    {
        $enrollment = str_replace("'", "''", $enrollment);
        $filters = $this->scholarshipFilterSql($request);

        $sql = "SELECT
                ssd.ssd_sai_enrollment_no,
                ssd.ssd_percentage,
                ssd.ssd_details,
                ssd.changed_on,
                aai.aai_id,
                ssd.scd_entry_username,
                st.scholarship_name,
                sess.sess_name,
                spi.spi_name,
                spi.spi_fathername,
                spi.spi_nic,
                spi.api_id,
                s.school_name,
                d.dept_name,
                p.prog_name
            FROM
                aud_bnucms.students_scholarships_details_aud ssd
                INNER JOIN scholarship_type st ON st.scholarship_id = ssd.ssd_type
                INNER JOIN academic_sessions sess ON sess.sess_id = ssd.ssd_sess_id
                INNER JOIN students_apply_info sai ON sai.sai_enrollment_no = ssd.ssd_sai_enrollment_no
                INNER JOIN students_personal_info spi ON spi.spi_id = sai.sai_spi_id
                INNER JOIN admission_personal_info api ON api.api_id = spi.api_id
                INNER JOIN admission_apply_info aai ON aai.aai_api_id = api.api_id
                INNER JOIN schools s ON s.school_id = sai.sai_sdpo_school_id
                INNER JOIN departments d ON d.dept_id = sai.sai_sdpo_dept_id
                INNER JOIN programs p ON p.prog_id = sai.sai_sdpo_prog_id
            WHERE
                ssd.ssd_sai_enrollment_no = '{$enrollment}'
                AND aai.cms_sai_enrollment_no IS NOT NULL
                {$filters}
            ORDER BY
                ssd.changed_on DESC,
                sess.sess_id DESC";

        return DB::select($sql);
    }

    protected function queryScholarshipForAdmissionStudent($identifier, Request $request, $type = 'enrollment')
    {
        $identifier = str_replace("'", "''", $identifier);
        $filters = $this->scholarshipFilterSql($request);

        if ($type === 'enrollment') {
            $idCondition = " AND aai.cms_sai_enrollment_no = '{$identifier}'";
        } else {
            $idCondition = " AND ssd.ssd_app_id = '{$identifier}'";
        }

        $sql = "SELECT
                aai.cms_sai_enrollment_no AS ssd_sai_enrollment_no,
                ssd.ssd_percentage,
                ssd.ssd_details,
                ssd.changed_on,
                aai.aai_id,
                ssd.scd_entry_username,
                ssd.ssd_type AS scholaship_id,
                st.scholarship_name,
                sess.sess_name,
                api.api_name AS spi_name,
                api.api_fathername AS spi_fathername,
                api.api_nic AS spi_nic,
                api.api_id,
                s.school_name,
                d.dept_name,
                p.prog_name
            FROM
                aud_bnucms.students_scholarships_details_aud ssd
                INNER JOIN scholarship_type st ON st.scholarship_id = ssd.ssd_type
                INNER JOIN academic_sessions sess ON sess.sess_id = ssd.ssd_sess_id
                INNER JOIN admission_apply_info aai ON aai.aai_id = ssd.ssd_app_id
                INNER JOIN admission_personal_info api ON api.api_id = aai.aai_api_id
                INNER JOIN schools s ON s.school_id = aai.aai_sdpo_school_id
                INNER JOIN departments d ON d.dept_id = aai.aai_sdpo_dept_id
                INNER JOIN programs p ON p.prog_id = aai.aai_sdpo_prog_id
            WHERE
                aai.aai_status = 'Selected'
                AND aai.cms_sai_enrollment_no IS NOT NULL
                {$idCondition}
                {$filters}
            ORDER BY
                sess.sess_id DESC,
                ssd.changed_on DESC";

        return DB::select($sql);
    }

    function isEnrollmentNo($enrollment) {
        // Check if the input is a valid string and contains a hyphen
        if (is_string($enrollment) && strpos($enrollment, '-') !== false) {
            return true;
        }
        return false;
    }

    //////////////SCHOLARSHIP REPORT ////////////////////
    function scholarshipReport(){
        $sessions = DB::select("select * from ACADEMIC_SESSIONS where SESS_STATUS = 'E' order by SESS_ID desc");
        $schools = Schools::where('school_status', 'E')
            ->whereNotIn('school_id', [10, 11])
            ->orderBy('school_name')
            ->get();
        $scholarshipTypes = DB::table('scholarship_type')
            ->select('scholarship_id', 'scholarship_name', 'scholarship_desc')
            ->where('scholarship_status', 'E')
            ->whereRaw("LOWER(scholarship_desc) IN ('academic', 'hostel')")
            ->orderBy('scholarship_name')
            ->get();

        return view('StudentFinancial.scholarshipReport', compact('sessions', 'schools', 'scholarshipTypes'));
    }

    public function getSchoolScholarshipReport(Request $request)
    {
        $filters = $this->buildSchoolScholarshipReportFilters($request);
        $sessionId = $filters['session_id'];

        $scholarships = DB::select("
            SELECT scholarship_id, scholarship_name
            FROM scholarship_type
            WHERE scholarship_status = 'E'
              AND LOWER(scholarship_desc) IN ('academic', 'hostel')
              {$filters['scholarship_type']}
              {$filters['scholarship_desc']}
            ORDER BY scholarship_name
        ");

        $pivotColumns = '';
        foreach ($scholarships as $sch) {
            $scholarshipId = $sch->scholarship_id ?? $sch->SCHOLARSHIP_ID;
            $scholarshipName = $sch->scholarship_name ?? $sch->SCHOLARSHIP_NAME ?? '';
            $alias = strtolower(preg_replace('/[^A-Za-z0-9_]/', '_', $scholarshipName));
            $pivotColumns .= "NVL(SUM(CASE WHEN sc.scholarship_id = {$scholarshipId} THEN sc.scholarship_count END), 0) AS {$alias},\n                ";
        }

        $sql = "
            WITH scholarship_counts AS (
                SELECT
                    s.school_id,
                    s.school_code,
                    s.school_name,
                    st.scholarship_id,
                    st.scholarship_name,
                    COUNT(*) AS scholarship_count
                FROM students_scholarships_details ssd
                JOIN scholarship_type st
                    ON st.scholarship_id = ssd.ssd_type
                JOIN admission_apply_info aai
                    ON aai.aai_id = ssd.ssd_app_id
                JOIN schools s
                    ON s.school_id = aai.aai_sdpo_school_id
                WHERE ssd.status = 'E'
                  AND ssd.ssd_sess_id = {$sessionId}
                  AND st.scholarship_status = 'E'
                  AND LOWER(TRIM(st.scholarship_desc)) = 'academic'
                  {$filters['school_s']}
                  {$filters['scholarship_type_st']}
                  {$filters['scope_academic_counts']}
                  {$filters['date_academic']}
                GROUP BY
                    s.school_id, s.school_code, s.school_name,
                    st.scholarship_id, st.scholarship_name

                UNION ALL

                SELECT
                    s.school_id,
                    s.school_code,
                    s.school_name,
                    st.scholarship_id,
                    st.scholarship_name,
                    COUNT(*) AS scholarship_count
                FROM fin_students_scholarships_hostel_details sshd
                JOIN scholarship_type st
                    ON st.scholarship_id = sshd.sshd_type
                JOIN admission_apply_info aai
                    ON aai.aai_id = sshd.sshd_app_id
                LEFT JOIN students_apply_info sai
                    ON LOWER(TRIM(sai.sai_enrollment_no)) = LOWER(TRIM(NVL(aai.cms_sai_enrollment_no, sshd.sshd_sai_enrollment_no)))
                JOIN schools s
                    ON s.school_id = NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id)
                WHERE sshd.sshd_status = 'E'
                  AND sshd.sshd_sess_id = {$sessionId}
                  AND st.scholarship_status = 'E'
                  AND LOWER(TRIM(st.scholarship_desc)) = 'hostel'
                  AND NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id) IS NOT NULL
                  {$filters['school_s']}
                  {$filters['scholarship_type_st']}
                  {$filters['scope_hostel_counts']}
                  {$filters['date_hostel']}
                GROUP BY
                    s.school_id, s.school_code, s.school_name,
                    st.scholarship_id, st.scholarship_name
            ),
            school_unique_students AS (
                SELECT
                    x.school_id,
                    COUNT(DISTINCT x.app_id) AS unique_scholarship_students
                FROM (
                    SELECT
                        TO_NUMBER(aai.aai_sdpo_school_id) AS school_id,
                        TO_CHAR(ssd.ssd_app_id) AS app_id
                    FROM students_scholarships_details ssd
                    JOIN scholarship_type st
                        ON st.scholarship_id = ssd.ssd_type
                    JOIN admission_apply_info aai
                        ON aai.aai_id = ssd.ssd_app_id
                    WHERE ssd.status = 'E'
                      AND ssd.ssd_sess_id = {$sessionId}
                      AND st.scholarship_status = 'E'
                      AND LOWER(TRIM(st.scholarship_desc)) = 'academic'
                      {$filters['school_aai']}
                      {$filters['scholarship_type_st']}
                      {$filters['scope_academic_counts']}
                      {$filters['date_academic']}

                    UNION ALL

                    SELECT
                        TO_NUMBER(NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id)) AS school_id,
                        TO_CHAR(sshd.sshd_app_id) AS app_id
                    FROM fin_students_scholarships_hostel_details sshd
                    JOIN scholarship_type st
                        ON st.scholarship_id = sshd.sshd_type
                    JOIN admission_apply_info aai
                        ON aai.aai_id = sshd.sshd_app_id
                    LEFT JOIN students_apply_info sai
                        ON LOWER(TRIM(sai.sai_enrollment_no)) = LOWER(TRIM(NVL(aai.cms_sai_enrollment_no, sshd.sshd_sai_enrollment_no)))
                    WHERE sshd.sshd_status = 'E'
                      AND sshd.sshd_sess_id = {$sessionId}
                      AND st.scholarship_status = 'E'
                      AND LOWER(TRIM(st.scholarship_desc)) = 'hostel'
                      AND NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id) IS NOT NULL
                      {$filters['school_student']}
                      {$filters['scholarship_type_st']}
                      {$filters['scope_hostel_counts']}
                      {$filters['date_hostel']}
                ) x
                GROUP BY x.school_id
            ),
            school_challan_amounts AS (
                SELECT
                    NVL(sc.sc_school_id, aai.aai_sdpo_school_id) AS school_id,
                    SUM(NVL(sc.sc_scholarship_amount, 0)) AS total_scholarship_amount
                FROM student_challan sc
                LEFT JOIN admission_apply_info aai
                    ON aai.aai_id = sc.sc_app_id
                WHERE sc.sc_session_id = {$sessionId}
                  AND NVL(sc.sc_scholarship_amount, 0) > 0
                  AND sc.sc_challan_status != 'CANCELLED'
                  AND sc.invoice_status = 'E'
                  {$filters['school_challan']}
                  {$filters['date_challan']}
                  {$filters['fee_type_challan']}
                  {$filters['scholarship_type_challan']}
                GROUP BY NVL(sc.sc_school_id, aai.aai_sdpo_school_id)
            )
            SELECT
                s.school_code,
                s.school_name,
                {$pivotColumns}
                NVL(SUM(sc.scholarship_count), 0) AS total_scholarships_applied,
                NVL(MAX(sus.unique_scholarship_students), 0) AS unique_scholarship_students,
                NVL(MAX(sca.total_scholarship_amount), 0) AS total_scholarship_amount
            FROM schools s
            LEFT JOIN scholarship_counts sc
                ON TO_NUMBER(sc.school_id) = TO_NUMBER(s.school_id)
            LEFT JOIN school_unique_students sus
                ON TO_NUMBER(sus.school_id) = TO_NUMBER(s.school_id)
            LEFT JOIN school_challan_amounts sca
                ON TO_NUMBER(sca.school_id) = TO_NUMBER(s.school_id)
            WHERE s.school_status = 'E'
              AND s.school_id NOT IN (10, 11)
              {$filters['school_s']}
            GROUP BY
                s.school_id,
                s.school_code,
                s.school_name
            ORDER BY s.school_code
        ";

        $data = DB::select($sql);

        $scholarshipColumns = array_map(function ($sch) {
            $scholarshipId = $sch->scholarship_id ?? $sch->SCHOLARSHIP_ID ?? null;
            $scholarshipName = $sch->scholarship_name ?? $sch->SCHOLARSHIP_NAME ?? '';

            return [
                'id' => $scholarshipId,
                'name' => $scholarshipName,
                'key' => strtolower(preg_replace('/[^A-Za-z0-9_]/', '_', $scholarshipName)),
            ];
        }, $scholarships);

        return response()->json([
            'success' => true,
            'scholarship_columns' => $scholarshipColumns,
            'data' => $data,
        ]);
    }


    /**
     * Run EXAM_CGPA_UPDATE_PRO at most ONCE per calendar day (first request of the day).
     * The procedure populates ssr_sgpa / ssr_cgpa in students_semester_record (session 166
     * is hardcoded inside it) and logs its own run into jobs_details
     * (JD_TITLE = 'EXAM_CGPA_UPDATE_PRO', JD_ENTRY_DATETIME). We gate on that timestamp,
     * so we only re-run when there is no run recorded for today.
     */
    private function ensureExamCgpaUpdatedToday()
    {
        try {
            $ranToday = DB::selectOne("
                SELECT COUNT(*) AS cnt
                FROM jobs_details
                WHERE jd_title = 'EXAM_CGPA_UPDATE_PRO'
                  AND TRUNC(jd_entry_datetime) = TRUNC(SYSDATE)
            ");

            $count = $ranToday ? (int) ($ranToday->cnt ?? $ranToday->CNT ?? 0) : 0;

            if ($count > 0) {
                return; // Already refreshed today.
            }

            DB::statement("BEGIN EXAM_CGPA_UPDATE_PRO; END;");
        } catch (\Throwable $e) {
            // Never block the report if the refresh fails — existing CGPA values still show.
            \Illuminate\Support\Facades\Log::error('EXAM_CGPA_UPDATE_PRO refresh failed', ['message' => $e->getMessage()]);
        }
    }

    public function getScholarshipDetailReport(Request $request)
    {
        // Refresh ssr_sgpa / ssr_cgpa (session 166) at most once per day before reading.
        $this->ensureExamCgpaUpdatedToday();

        $filters = $this->buildSchoolScholarshipReportFilters($request);
        $sessionId = $filters['session_id'];

        // Scholarship Amount = student_challan.sc_scholarship_amount summed for the
        // student's session challans, scoped academic-vs-hostel (and by Fee Type).
        if (!empty($request->fee_type)) {
            $feeType = str_replace("'", "''", $request->fee_type);
            $academicAmountFilter = $this->scholarshipReportFeeTypeChallanFilter($feeType);
            $hostelAmountFilter = " AND TRIM(sc.sc_challan_type) IN ('Hostel_Fee', 'Hostel Fee')";
        } else {
            $academicAmountFilter = " AND TRIM(sc.sc_challan_type) NOT IN ('Hostel_Fee', 'Hostel Fee')";
            $hostelAmountFilter = " AND TRIM(sc.sc_challan_type) IN ('Hostel_Fee', 'Hostel Fee')";
        }

        $sql = "
            SELECT * FROM (
                SELECT
                    aai.cms_sai_enrollment_no            AS student_id,
                    NVL(api.api_name, spi.spi_name)      AS student_name,
                    s.school_name                        AS school,
                    sess.sess_name                       AS scholarship_session,
                    st.scholarship_name                  AS scholarship_type,
                    ssd.ssd_percentage                   AS scholarship_percentage,
                    ssr.ssr_sgpa                         AS cgpa_session,
                    ssr.ssr_cgpa                         AS cgpa_awarded,
                    (SELECT NVL(SUM(NVL(sc.sc_scholarship_amount, 0)), 0)
                       FROM student_challan sc
                      WHERE sc.sc_app_id = ssd.ssd_app_id
                        AND sc.sc_session_id = ssd.ssd_sess_id
                        AND sc.sc_challan_status != 'CANCELLED'
                        AND sc.invoice_status = 'E'
                        {$academicAmountFilter}
                    ) AS scholarship_amount
                FROM students_scholarships_details ssd
                JOIN scholarship_type st
                    ON st.scholarship_id = ssd.ssd_type
                   AND st.scholarship_status = 'E'
                JOIN admission_apply_info aai
                    ON aai.aai_id = ssd.ssd_app_id
                LEFT JOIN admission_personal_info api
                    ON api.api_id = aai.aai_api_id
                LEFT JOIN students_apply_info sai
                    ON sai.sai_enrollment_no = aai.cms_sai_enrollment_no
                LEFT JOIN students_personal_info spi
                    ON spi.spi_id = sai.sai_spi_id
                JOIN schools s
                    ON s.school_id = aai.aai_sdpo_school_id
                JOIN academic_sessions sess
                    ON sess.sess_id = ssd.ssd_sess_id
                LEFT JOIN students_semester_record ssr
                    ON ssr.sai_enrollment_no = aai.cms_sai_enrollment_no
                   AND ssr.sess_id = ssd.ssd_sess_id
                WHERE ssd.status = 'E'
                  AND aai.cms_sai_enrollment_no IS NOT NULL
                  AND LOWER(TRIM(st.scholarship_desc)) = 'academic'
                  AND ssd.ssd_sess_id = {$sessionId}
                  {$filters['school_aai']}
                  {$filters['scholarship_type_st']}
                  {$filters['scope_academic_counts']}
                  {$filters['date_academic']}

                UNION ALL

                SELECT
                    NVL(aai.cms_sai_enrollment_no, sshd.sshd_sai_enrollment_no) AS student_id,
                    NVL(api.api_name, spi.spi_name)      AS student_name,
                    s.school_name                        AS school,
                    sess.sess_name                       AS scholarship_session,
                    st.scholarship_name                  AS scholarship_type,
                    sshd.sshd_percentage                 AS scholarship_percentage,
                    ssr.ssr_sgpa                         AS cgpa_session,
                    ssr.ssr_cgpa                         AS cgpa_awarded,
                    (SELECT NVL(SUM(NVL(sc.sc_scholarship_amount, 0)), 0)
                       FROM student_challan sc
                      WHERE sc.sc_app_id = sshd.sshd_app_id
                        AND sc.sc_session_id = sshd.sshd_sess_id
                        AND sc.sc_challan_status != 'CANCELLED'
                        AND sc.invoice_status = 'E'
                        {$hostelAmountFilter}
                    ) AS scholarship_amount
                FROM fin_students_scholarships_hostel_details sshd
                JOIN scholarship_type st
                    ON st.scholarship_id = sshd.sshd_type
                   AND st.scholarship_status = 'E'
                JOIN admission_apply_info aai
                    ON aai.aai_id = sshd.sshd_app_id
                LEFT JOIN admission_personal_info api
                    ON api.api_id = aai.aai_api_id
                LEFT JOIN students_apply_info sai
                    ON LOWER(TRIM(sai.sai_enrollment_no)) = LOWER(TRIM(NVL(aai.cms_sai_enrollment_no, sshd.sshd_sai_enrollment_no)))
                LEFT JOIN students_personal_info spi
                    ON spi.spi_id = sai.sai_spi_id
                JOIN schools s
                    ON s.school_id = NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id)
                JOIN academic_sessions sess
                    ON sess.sess_id = sshd.sshd_sess_id
                LEFT JOIN students_semester_record ssr
                    ON ssr.sai_enrollment_no = NVL(aai.cms_sai_enrollment_no, sshd.sshd_sai_enrollment_no)
                   AND ssr.sess_id = sshd.sshd_sess_id
                WHERE sshd.sshd_status = 'E'
                  AND LOWER(TRIM(st.scholarship_desc)) = 'hostel'
                  AND NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id) IS NOT NULL
                  AND sshd.sshd_sess_id = {$sessionId}
                  {$filters['school_student']}
                  {$filters['scholarship_type_st']}
                  {$filters['scope_hostel_counts']}
                  {$filters['date_hostel']}
            ) scholarship_detail
            ORDER BY school, student_name
        ";

        $rows = DB::select($sql);

        $get = function ($row, $key) {
            $upper = strtoupper($key);
            if (isset($row->$key)) {
                return $row->$key;
            }
            if (isset($row->$upper)) {
                return $row->$upper;
            }
            return null;
        };

        $data = array_map(function ($row) use ($get) {
            return [
                'student_id' => $get($row, 'student_id'),
                'student_name' => $get($row, 'student_name'),
                'school' => $get($row, 'school'),
                'scholarship_session' => $get($row, 'scholarship_session'),
                'scholarship_type' => $get($row, 'scholarship_type'),
                'scholarship_percentage' => $get($row, 'scholarship_percentage'),
                'scholarship_amount' => $get($row, 'scholarship_amount') ?? 0,
                'cgpa_session' => $get($row, 'cgpa_session'),
                'cgpa_awarded' => $get($row, 'cgpa_awarded'),
            ];
        }, $rows);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    protected function buildSchoolScholarshipReportFilters(Request $request)
    {
        $sessionId = (int) ($request->session ?? $request->session_sel ?? 165);
        if ($sessionId <= 0) {
            $sessionId = 165;
        }

        $filters = [
            'session_id' => $sessionId,
            'school_s' => '',
            'school_aai' => '',
            'school_student' => '',
            'school_challan' => '',
            'scholarship_type' => '',
            'scholarship_type_st' => '',
            'scholarship_type_challan' => '',
            'scholarship_desc' => '',
            'scope_academic_counts' => '',
            'scope_hostel_counts' => '',
            'date_academic' => '',
            'date_hostel' => '',
            'date_challan' => '',
            'fee_type_challan' => '',
        ];

        if (!empty($request->school)) {
            $schoolId = (int) $request->school;
            $filters['school_s'] = " AND s.school_id = {$schoolId}";
            $filters['school_aai'] = " AND aai.aai_sdpo_school_id = {$schoolId}";
            $filters['school_student'] = " AND NVL(aai.aai_sdpo_school_id, sai.sai_sdpo_school_id) = {$schoolId}";
            $filters['school_challan'] = " AND NVL(sc.sc_school_id, aai.aai_sdpo_school_id) = {$schoolId}";
        }

        if (!empty($request->scholarship_type)) {
            $scholarshipId = (int) $request->scholarship_type;
            $filters['scholarship_type'] = " AND scholarship_id = {$scholarshipId}";
            $filters['scholarship_type_st'] = " AND st.scholarship_id = {$scholarshipId}";
            $filters['scholarship_type_challan'] = "
                AND (
                    EXISTS (
                        SELECT 1
                        FROM students_scholarships_details ssd2
                        WHERE ssd2.ssd_app_id = sc.sc_app_id
                          AND ssd2.ssd_sess_id = sc.sc_session_id
                          AND ssd2.ssd_type = {$scholarshipId}
                          AND ssd2.status = 'E'
                    )
                    OR EXISTS (
                        SELECT 1
                        FROM fin_students_scholarships_hostel_details sshd2
                        WHERE sshd2.sshd_app_id = sc.sc_app_id
                          AND sshd2.sshd_sess_id = sc.sc_session_id
                          AND sshd2.sshd_type = {$scholarshipId}
                          AND sshd2.sshd_status = 'E'
                    )
                )";
        }

        $filters['scope_academic_counts'] = $this->buildScholarshipReportCountCategoryScope($request, 'academic');
        $filters['scope_hostel_counts'] = $this->buildScholarshipReportCountCategoryScope($request, 'hostel');

        $filters['date_academic'] = $this->buildScholarshipReportChallanExists(
            'ssd.ssd_app_id',
            'ssd.ssd_sess_id',
            $request,
            'academic'
        );
        $filters['date_hostel'] = $this->buildScholarshipReportChallanExists(
            'sshd.sshd_app_id',
            'sshd.sshd_sess_id',
            $request,
            'hostel'
        );

        if (!empty($request->effective_from)) {
            $effectiveFrom = str_replace("'", "''", $request->effective_from);
            $filters['date_challan'] .= " AND TRUNC(sc.sc_issu_date) >= TO_DATE('{$effectiveFrom}', 'YYYY-MM-DD')";
        }

        if (!empty($request->effective_to)) {
            $effectiveTo = str_replace("'", "''", $request->effective_to);
            $filters['date_challan'] .= " AND TRUNC(sc.sc_issu_date) < TO_DATE('{$effectiveTo}', 'YYYY-MM-DD') + 1";
        }

        if (!empty($request->fee_type)) {
            $feeType = str_replace("'", "''", $request->fee_type);
            $filters['fee_type_challan'] = $this->scholarshipReportFeeTypeChallanFilter($feeType);

            // Restrict the pivot column list to match the fee-type scope already
            // applied to the counts: Hostel Fee shows only hostel scholarships,
            // academic fee types show only academic scholarships.
            $filters['scholarship_desc'] = $request->fee_type === 'Hostel_Fee'
                ? " AND LOWER(TRIM(scholarship_desc)) = 'hostel'"
                : " AND LOWER(TRIM(scholarship_desc)) = 'academic'";
        }

        return $filters;
    }

    protected function buildScholarshipReportCountCategoryScope(Request $request, $category = 'academic')
    {
        if (empty($request->fee_type)) {
            return '';
        }

        $feeType = str_replace("'", "''", $request->fee_type);

        if ($feeType === 'Hostel_Fee') {
            return $category === 'hostel'
                ? ''
                : ' AND 1 = 0';
        }

        if ($category === 'hostel') {
            return ' AND 1 = 0';
        }

        return $this->buildScholarshipReportFeeTypeChallanExists(
            $feeType,
            'ssd.ssd_app_id',
            'ssd.ssd_sess_id'
        );
    }

    protected function buildScholarshipReportFeeTypeChallanExists($feeType, $appIdColumn, $sessionIdColumn)
    {
        $challanTypes = $this->scholarshipReportFeeTypeSqlList($feeType, 'academic');

        if ($challanTypes === "'__NO_MATCH__'") {
            return ' AND 1 = 0';
        }

        return "
                AND EXISTS (
                    SELECT 1
                    FROM student_challan scf
                    WHERE scf.sc_app_id = {$appIdColumn}
                      AND scf.sc_session_id = {$sessionIdColumn}
                      AND scf.sc_challan_status != 'CANCELLED'
                      AND TRIM(scf.sc_challan_type) IN ({$challanTypes})
                )";
    }

    protected function buildScholarshipReportChallanExists($appIdColumn, $sessionIdColumn, Request $request, $category = 'academic')
    {
        $conditions = [
            "scf.sc_app_id = {$appIdColumn}",
            "scf.sc_session_id = {$sessionIdColumn}",
            "scf.sc_challan_status != 'CANCELLED'",
        ];

        if (!empty($request->effective_from)) {
            $effectiveFrom = str_replace("'", "''", $request->effective_from);
            $conditions[] = "TRUNC(scf.sc_issu_date) >= TO_DATE('{$effectiveFrom}', 'YYYY-MM-DD')";
        }

        if (!empty($request->effective_to)) {
            $effectiveTo = str_replace("'", "''", $request->effective_to);
            $conditions[] = "TRUNC(scf.sc_issu_date) < TO_DATE('{$effectiveTo}', 'YYYY-MM-DD') + 1";
        }

        if (!empty($request->fee_type) && $request->fee_type === 'Hostel_Fee' && $category === 'hostel') {
            $conditions[] = "TRIM(scf.sc_challan_type) IN ('Hostel_Fee', 'Hostel Fee')";
        }

        if (count($conditions) === 3) {
            return '';
        }

        return '
                AND EXISTS (
                    SELECT 1
                    FROM student_challan scf
                    WHERE ' . implode("\n                      AND ", $conditions) . '
                )';
    }

    protected function scholarshipReportFeeTypeSqlList($feeType, $category = 'academic')
    {
        if ($category === 'hostel') {
            if ($feeType === 'Full_semester') {
                return "'Hostel_Fee'";
            }

            return "'__NO_MATCH__'";
        }

        if ($feeType === 'Full_semester') {
            return "'Full_semester'";
        }

        if ($feeType === 'Short/Summer_Course') {
            return "'Short/Summer_Course'";
        }

        if ($feeType === 'Credit_hours') {
            return "'Credit_hours'";
        }

        if ($feeType === 'Hostel_Fee') {
            return $category === 'hostel' ? "'Hostel_Fee'" : "'__NO_MATCH__'";
        }

        return "'" . str_replace("'", "''", $feeType) . "'";
    }

    protected function scholarshipReportFeeTypeChallanFilter($feeType)
    {
        if ($feeType === 'Full_semester') {
            return " AND sc.sc_challan_type IN ('Full_semester')";
        }

        if ($feeType === 'Short/Summer_Course') {
            return " AND sc.sc_challan_type = 'Short/Summer_Course'";
        }

        if ($feeType === 'Credit_hours') {
            return " AND sc.sc_challan_type = 'Credit_hours'";
        }

        if ($feeType === 'Hostel_Fee') {
            return " AND TRIM(sc.sc_challan_type) IN ('Hostel_Fee', 'Hostel Fee')";
        }

        $feeType = str_replace("'", "''", $feeType);
        return " AND sc.sc_challan_type = '{$feeType}'";
    }

}
