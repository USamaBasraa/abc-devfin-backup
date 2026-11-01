<?php
namespace App\Http\Controllers\Grader\StudentRecord;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Grader\StudentRecord\Concerns\ResolvesCarryForwardSession;
use App\Services\Hostel\HostelAssignmentResolver;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use Response;
use Validator;
use DataTables;
use Carbon\Carbon;
use App\Models\User;
use Exception;


class StudentRecordController extends Controller
{
    use ResolvesCarryForwardSession;

    /**
     * Students billed Non-Regular for hostel regardless of what the page asks for.
     *
     * Their hostel fee does NOT come from FIN_HOSTEL_FEE_STRUCTURE — it is read off the
     * generated hostel challan (STUDENT_CHALLAN.FFS_HOSTEL_AMT), so until that challan
     * exists for a session there is no fee to price a hostel scholarship against.
     *
     * Was a local array inside getHostelFeeStructure(); lifted to a constant so the
     * scholarship bulk import resolves the same students the same way instead of
     * carrying a second copy of the list.
     */
    public const NON_REGULAR_HOSTEL_ENROLLMENTS = [
        'SUM26-0022',
        'SUM24-0015',
        'SUM26-0020',
        'SUM26-0015',
        'SUM24-0006',
        'SUM24-0022',
        'SUM25-0018',
        'SUM25-0005',
    ];

    function getprofileMgt($rollNo_OR_aai_id = null, $studentType = null, $session = null, $sch_type = null){
        // dd($rollNo_OR_aai_id);
        $scholarshipCondition='';
        if(!empty($sch_type)){
            $scholarshipCondition = 'AND FOS_SCHOLARSHIP_TYPE ='.$sch_type;
        }
        // $searchType = $this->checkEnrollmentORaai_id($rollNo_OR_aai_id);
        // dd($studentType);
        // 'Existing' and the three 'New Admission <SEASON>' types are all searched the
        // same way — by enrollment no. Only 'New without Registration' goes by AAI_ID.
        if ($this->isEnrolledStudentType($studentType)) {
            $roll_no = $rollNo_OR_aai_id;
            $uppcaseRollNo = '';
            if (!empty($roll_no)) {
                $uppcaseRollNo = strtoupper($roll_no);
            }
            $sql = "SELECT
                    spi_name student_name,
                    students_apply_info.sai_enrollment_no AS username,
                    (SELECT api.api_imagename FROM admission_personal_info api WHERE api.api_id = students_apply_info.sai_api_id) AS api_imagename,
                    school_name,
                    dept_name,
                    PROG_NAME,
                    (
                        SELECT
                            SUM(NVL(fos_admission_fee, 0) +
                                NVL(fos_security_fee, 0) +
                                NVL(fos_activity_charges, 0) +
                                NVL(fos_medical_insurance, 0) +
                                NVL(fos_stipend, 0))
                        FROM
                            fin_other_scholarships
                        WHERE
                            LOWER(sai_enrollment_no) = LOWER(fos_enrollment_no) AND FOS_SESS_ID = $session
                            $scholarshipCondition
                    ) AS arrears,
                    SAI_STUDENT_STATUS,
                    AAI_ID,
                    (select CGPA('$uppcaseRollNo') from dual) cgpa

                    FROM
                        students_apply_info
                        LEFT JOIN students_personal_info ON spi_id = sai_spi_id
                        LEFT JOIN ADMISSION_APPLY_INFO ON AAI_API_ID = SAI_API_ID and lower(CMS_SAI_ENROLLMENT_NO) = lower(SAI_ENROLLMENT_NO)
                        LEFT JOIN schools ON school_id = sai_sdpo_school_id
                        LEFT JOIN departments ON dept_id = sai_sdpo_dept_id
                        LEFT JOIN PROGRAMS ON PROG_ID = SAI_SDPO_PROG_ID
                        where sai_enrollment_no = '$uppcaseRollNo'";
                        // dd($sql)
            $res = DB::select($sql);
            $scholarships = $this->getStudentAppliedScholarships($uppcaseRollNo, $session);
            $arrearsBreakdown = $this->getStudentArrearsBreakdown($uppcaseRollNo, $session);
            $prevSession = $this->getPreviousSession($session);
            $prevScholarships = $prevSession ? $this->getStudentAppliedScholarships($uppcaseRollNo, $prevSession->sess_id) : [];
            $prevSessionName = $prevSession ? $prevSession->sess_name : null;
            // Pass the ACTUAL student type — a 'New Admission <SEASON>' student's fee
            // structure is keyed by that value, not by 'Existing'.
            $feeSummary = $this->getAcademicFeeStructureSummary($session, $uppcaseRollNo, $studentType);
            if (!empty(($res))) {
                return response()->json(['data' => $res, 'scholarships' => $scholarships, 'arrears_breakdown' => $arrearsBreakdown, 'prev_scholarships' => $prevScholarships, 'prev_session_name' => $prevSessionName, 'fee_structure_summary' => $feeSummary, 'status' => true, 'message' => '1']);
            } else {
                return response()->json(['data' => $res, 'scholarships' => $scholarships, 'arrears_breakdown' => $arrearsBreakdown, 'prev_scholarships' => $prevScholarships, 'prev_session_name' => $prevSessionName, 'fee_structure_summary' => $feeSummary, 'status' => true, 'message' => '0']);
            }
        } else {//New Without Registration
            $aai_id = $rollNo_OR_aai_id;
            $res = '';
            $student_sql = "select
                            aai.*
                            from admission_apply_info aai
                            where
                            aai.aai_id='$aai_id'";
            $student_data = DB::select($student_sql);
            // dd($student_data);
            if (!empty($student_data[0])) {
                // if(empty($student_data[0]->cms_sai_enrollment_no) && !empty($student_data[0]->aai_id)){
                // dd('new student');
                $sql = "SELECT
                            API_NAME student_name,
                            admission_apply_info.cms_sai_enrollment_no AS username,
                            admission_personal_info.api_imagename AS api_imagename,
                            school_name,
                            dept_name,
                            PROG_NAME,
                            AAI_ID,
                             (
                                SELECT
                                    SUM(NVL(fos_admission_fee, 0) +
                                        NVL(fos_security_fee, 0) +
                                        NVL(fos_activity_charges, 0) +
                                        NVL(fos_medical_insurance, 0) +
                                        NVL(fos_stipend, 0))
                                FROM
                                    fin_other_scholarships
                                WHERE
                                    FOS_APP_ID = AAI_ID AND FOS_SESS_ID = $session
                                    $scholarshipCondition
                            ) AS arrears
                            FROM
                                admission_apply_info
                                LEFT JOIN admission_personal_info ON API_ID = AAI_API_ID
                                LEFT JOIN schools ON school_id = AAI_SDPO_SCHOOL_ID
                                LEFT JOIN departments ON dept_id = AAI_SDPO_DEPT_ID
                                LEFT JOIN PROGRAMS ON PROG_ID = AAI_SDPO_PROG_ID
                                where AAI_ID = '$aai_id'";
                // AND CMS_SAI_ENROLLMENT_NO is null";

                // dd($sql);
                $res = DB::select($sql);
                $enrollmentNo = (!empty($res) && !empty($res[0]->username)) ? $res[0]->username : null;
                $scholarships = $this->getStudentAppliedScholarships($enrollmentNo, $session, $aai_id);
                $arrearsBreakdown = $this->getStudentArrearsBreakdown($enrollmentNo, $session, $aai_id);
                $prevSession = $this->getPreviousSession($session);
                $prevScholarships = $prevSession ? $this->getStudentAppliedScholarships($enrollmentNo, $prevSession->sess_id, $aai_id) : [];
                $prevSessionName = $prevSession ? $prevSession->sess_name : null;
                $feeSummary = $this->getAcademicFeeStructureSummary($session, $enrollmentNo ?: $aai_id, 'New without Registration');
                if (!empty(($res))) {
                    return response()->json(['data' => $res, 'scholarships' => $scholarships, 'arrears_breakdown' => $arrearsBreakdown, 'prev_scholarships' => $prevScholarships, 'prev_session_name' => $prevSessionName, 'fee_structure_summary' => $feeSummary, 'status' => true, 'message' => '1']);
                } else {
                    return response()->json(['data' => $res, 'scholarships' => $scholarships, 'arrears_breakdown' => $arrearsBreakdown, 'prev_scholarships' => $prevScholarships, 'prev_session_name' => $prevSessionName, 'fee_structure_summary' => $feeSummary, 'status' => true, 'message' => '0']);
                }
                // }else{
                //     // dd('not new student');
                //     return response()->json(['data'=>$res,'status' => true, 'message' => '0']);
                // }

            } else {
                // dd('not student');
                return response()->json(['data' => $res, 'scholarships' => [], 'arrears_breakdown' => [], 'prev_scholarships' => [], 'prev_session_name' => null, 'fee_structure_summary' => null, 'status' => true, 'message' => '0']);
            }

        }



    }

    function getStudentAppliedScholarships($enrollmentNo = null, $sessionId = null, $aaiId = null)
    {
        if (empty($sessionId)) {
            return [];
        }

        if (!empty($enrollmentNo)) {
            $enrollmentNo = strtoupper($enrollmentNo);
            $studentFilter = "AND LOWER(sai.sai_enrollment_no) = LOWER('$enrollmentNo')";
        } elseif (!empty($aaiId)) {
            $studentFilter = "AND ssd.ssd_app_id = '$aaiId'";
            $hostelStudentFilter = "AND sshd.sshd_app_id = '$aaiId'";
        } else {
            return [];
        }

        // Academic rows have no stored amount (it is computed from the fee
        // structure at display time), so scholarship_amount / hostel_fee come
        // back as NULL for academic and the real columns for hostel. row_id
        // drives the table edit / delete controls on the front-end.
        // is_locked = 1 when this credit-hour scholarship's regenerated challan
        // already carries a scholarship amount (SC_SCHOLARSHIP_AMOUNT > 0), which is
        // when the row becomes immutable. Mirrors isScholarshipRowLockedByChallan().
        $lockedExpr = "CASE
                        WHEN ssd.ssd_challan_category = 'Credit_hours' AND EXISTS (
                            SELECT 1 FROM student_challan sc
                            WHERE sc.sc_session_id = ssd.ssd_sess_id
                              AND sc.sc_challan_type = 'Credit_hours'
                              AND NVL(sc.sc_scholarship_amount, 0) > 0
                              AND UPPER(TRIM(NVL(sc.sc_challan_status, 'X'))) NOT IN ('CANCELLED', 'CANCEL FOR REISSUANCE')
                              AND (sc.sc_app_id = ssd.ssd_app_id
                                   OR UPPER(TRIM(sc.sc_enrollment_no)) = UPPER(TRIM(ssd.ssd_sai_enrollment_no)))
                        ) THEN '1'
                        WHEN ssd.ssd_challan_category = 'additional_Full_semester' AND EXISTS (
                            SELECT 1 FROM student_challan sc
                            WHERE sc.sc_session_id = ssd.ssd_sess_id
                              AND sc.sc_challan_type = 'Full_semester'
                              AND NVL(sc.sc_enrolled_cr_hr, 0) > 0
                              AND NVL(sc.sc_scholarship_amount, 0) > 0
                              AND UPPER(TRIM(NVL(sc.sc_challan_status, 'X'))) NOT IN ('CANCELLED', 'CANCEL FOR REISSUANCE')
                              AND (sc.sc_app_id = ssd.ssd_app_id
                                   OR UPPER(TRIM(sc.sc_enrollment_no)) = UPPER(TRIM(ssd.ssd_sai_enrollment_no)))
                        ) THEN '1'
                        ELSE '0'
                     END";

        $academicCols = "'ACADEMIC' AS scholarship_type,
                    TO_CHAR(st.scholarship_name) AS scholarship_name,
                    TO_CHAR(st.scholarship_desc) AS scholarship_desc,
                    TO_CHAR(ssd.ssd_percentage) AS scholarship_percentage,
                    TO_CHAR(NULL) AS scholarship_amount,
                    TO_CHAR(ssd.ssd_arrear) AS scholarship_arrear,
                    TO_CHAR(NULL) AS hostel_fee,
                    TO_CHAR(ssd.ssd_details) AS scholarship_details,
                    TO_CHAR(ssd.ssd_challan_category) AS scholarship_challan_category,
                    TO_CHAR(ssd.ssd_type) AS scholarship_type_id,
                    TO_CHAR(ssd.ssd_id) AS row_id,
                    TO_CHAR(ssd.ssd_sess_id) AS session_id,
                    TO_CHAR(ssd.ssd_app_id) AS app_id,
                    {$lockedExpr} AS is_locked";

        $hostelCols = "'HOSTEL' AS scholarship_type,
                    TO_CHAR(st.scholarship_name) AS scholarship_name,
                    TO_CHAR(st.scholarship_desc) AS scholarship_desc,
                    TO_CHAR(sshd.sshd_percentage) AS scholarship_percentage,
                    TO_CHAR(sshd.sshd_amount) AS scholarship_amount,
                    TO_CHAR(sshd.sshd_arrear) AS scholarship_arrear,
                    TO_CHAR(sshd.sshd_hostel_fee) AS hostel_fee,
                    TO_CHAR(sshd.sshd_details) AS scholarship_details,
                    TO_CHAR(NULL) AS scholarship_challan_category,
                    TO_CHAR(sshd.sshd_type) AS scholarship_type_id,
                    TO_CHAR(sshd.sshd_id) AS row_id,
                    TO_CHAR(sshd.sshd_sess_id) AS session_id,
                    TO_CHAR(sshd.sshd_app_id) AS app_id,
                    '0' AS is_locked";

        if (!empty($enrollmentNo)) {
            $hostelStudentFilter = "AND LOWER(sai.sai_enrollment_no) = LOWER('$enrollmentNo')";
            $sql = "SELECT
                    $academicCols
                FROM students_scholarships_details ssd
                JOIN scholarship_type st
                    ON st.scholarship_id = ssd.ssd_type
                JOIN admission_apply_info aai
                    ON aai.aai_id = ssd.ssd_app_id
                JOIN students_apply_info sai
                    ON sai.sai_api_id = aai.aai_api_id
                   AND LOWER(sai.sai_enrollment_no) = LOWER(aai.cms_sai_enrollment_no)
                WHERE ssd.ssd_sess_id = $sessionId
                  AND ssd.status = 'E'
                  $studentFilter
                  AND LOWER(st.scholarship_desc) = 'academic'
                UNION ALL
                SELECT
                    $hostelCols
                FROM fin_students_scholarships_hostel_details sshd
                JOIN scholarship_type st
                    ON st.scholarship_id = sshd.sshd_type
                JOIN admission_apply_info aai
                    ON aai.aai_id = sshd.sshd_app_id
                JOIN students_apply_info sai
                    ON sai.sai_api_id = aai.aai_api_id
                   AND LOWER(sai.sai_enrollment_no) = LOWER(aai.cms_sai_enrollment_no)
                WHERE sshd.sshd_sess_id = $sessionId
                  AND sshd.sshd_status = 'E'
                  $hostelStudentFilter
                  AND LOWER(st.scholarship_desc) = 'hostel'
                ORDER BY
                    scholarship_type,
                    scholarship_name";
        } else {
            $sql = "SELECT
                    $academicCols
                FROM students_scholarships_details ssd
                JOIN scholarship_type st
                    ON st.scholarship_id = ssd.ssd_type
                WHERE ssd.ssd_sess_id = $sessionId
                  AND ssd.status = 'E'
                  $studentFilter
                  AND LOWER(st.scholarship_desc) = 'academic'
                UNION ALL
                SELECT
                    $hostelCols
                FROM fin_students_scholarships_hostel_details sshd
                JOIN scholarship_type st
                    ON st.scholarship_id = sshd.sshd_type
                WHERE sshd.sshd_sess_id = $sessionId
                  AND sshd.sshd_status = 'E'
                  $hostelStudentFilter
                  AND LOWER(st.scholarship_desc) = 'hostel'
                ORDER BY
                    scholarship_type,
                    scholarship_name";
        }

        return DB::select($sql);
    }

    /**
     * Itemised arrears (admission fee, security, activity charges, medical
     * insurance, stipend) the student has under fin_other_scholarships for the
     * session, grouped by scholarship type so each tile can show its own
     * breakdown. Matches a student the same way getprofileMgt does: by
     * enrollment for Existing students, by app id for New students.
     */
    function getStudentArrearsBreakdown($enrollmentNo = null, $sessionId = null, $aaiId = null)
    {
        if (empty($sessionId)) {
            return [];
        }

        // Bound parameters — these values used to be interpolated straight into the
        // SQL string (injection surface).
        $bindings = [$sessionId];

        if (!empty($enrollmentNo)) {
            $studentFilter = 'AND UPPER(TRIM(fos_enrollment_no)) = ?';
            $bindings[] = strtoupper(trim($enrollmentNo));
        } elseif (!empty($aaiId)) {
            $studentFilter = 'AND fos_app_id = ?';
            $bindings[] = $aaiId;
        } else {
            return [];
        }

        // Arrears are recorded PER challan category, so each (scholarship type,
        // challan category) pair is its own bucket. Rows written before
        // FOS_CHALLAN_CATEGORY existed have NULL -> treated as Full_semester.
        $sql = "SELECT
                    TO_CHAR(fos_scholarship_type) AS scholarship_type_id,
                    NVL(fos_challan_category, 'Full_semester') AS challan_category,
                    SUM(NVL(fos_admission_fee, 0))     AS admission_fee,
                    SUM(NVL(fos_security_fee, 0))      AS security_fee,
                    SUM(NVL(fos_activity_charges, 0))  AS activity_charges,
                    SUM(NVL(fos_medical_insurance, 0)) AS medical_insurance,
                    SUM(NVL(fos_stipend, 0))           AS stipend,
                    SUM(NVL(fos_admission_fee, 0) +
                        NVL(fos_security_fee, 0) +
                        NVL(fos_activity_charges, 0) +
                        NVL(fos_medical_insurance, 0) +
                        NVL(fos_stipend, 0))           AS total
                FROM fin_other_scholarships
                WHERE fos_sess_id = ?
                  $studentFilter
                GROUP BY fos_scholarship_type, NVL(fos_challan_category, 'Full_semester')";

        return DB::select($sql, $bindings);
    }

    /**
     * The session the student's scholarships carry forward FROM, used to show the
     * previous-session scholarships on the profile card and to seed the Import
     * button on the revised page.
     *
     * Summer is skipped — see ResolvesCarryForwardSession for why, and for the
     * bulk page's copy of the same rule.
     */
    function getPreviousSession($sessionId = null)
    {
        return $this->resolveCarryForwardSession($sessionId);
    }

	  function verifyStudentCgpa($roll_no=null, $session=null){
        // dd($session);
        $uppcaseRollNo = '';
        if(!empty($roll_no)){
            $uppcaseRollNo = strtoupper($roll_no);
        }
        // dd($uppcaseRollNo);
        // $sql = "SELECT

        // spi_name,
        // school_name,
        // dept_name

        // FROM
        //     students_apply_info
        //     LEFT JOIN students_personal_info ON spi_id = sai_spi_id
        //     LEFT JOIN schools ON school_id = sai_sdpo_school_id
        //     LEFT JOIN departmen
        // ts ON dept_id = sai_sdpo_dept_id
        //     where sai_enrollment_no = '$uppcaseRollNo'";

        // $res =  DB::select($sql);

        // if(!empty(($res))){
        //     return response()->json(['data'=>$res,'status' => true, 'message' => '1']);
        // }else{
        //     return response()->json(['data'=>$res,'status' => true, 'message' => '0']);
        // }


    }

    function getRecord(){
        $sql = "SELECT
        ssd_id,
        ssd_sai_enrollment_no,
        ssd_type,
        ssd_percentage,
        ssd_details,
        sess_name,
        scholarship_name,
        spi_name,
        prog_name,
        school_name
        FROM
        students_scholarships_details
        left join SCHOLARSHIP_TYPE on  scholarship_id = ssd_type
        left join academic_sessions on sess_id = ssd_sess_id
        left join students_apply_info on sai_enrollment_no = ssd_sai_enrollment_no
        left join students_personal_info on sai_spi_id = spi_id
        left join programs on sai_sdpo_prog_id = prog_id
        left join schools on sai_sdpo_school_id = school_id
        where 1=1
        ORDER BY ssd_id DESC";
        //    / echo $sql;exit;
        $rec = DB::select($sql);

        $Scholarship_Type = "select * from Scholarship_Type where scholarship_status = 'E'";
        $recScholarship_Type = DB::select($Scholarship_Type);

        // These RO users must not see session 168 in form/filter dropdowns
        $hideSession168Users = ['talha.sarfraz', 'hassan.farooq', 'abdul.waheed', 'tayyaba.nawaz'];
        if (in_array(auth()->user()->username, $hideSession168Users, true)) {
            $sqlAcademicSessions = "select * from academic_sessions WHERE sess_id NOT IN (168) order by sess_id desc";
        } else {
            $sqlAcademicSessions = "select * from academic_sessions order by sess_id desc";
        }
        $recsqlAcademicSessions = DB::select($sqlAcademicSessions);

        return view('grader.StudentRecord.student_record',['studentRecord'=>$rec,'Scholarship_Type'=>$recScholarship_Type,'academic_session'=>$recsqlAcademicSessions]);
    }
    function getStudentChallanData(Request $request){
        $sessionId = $request->get('academic_session');
        $sc_challan_type = $request->get('sc_challan_type');
        $fine_category = $request->get('sc_fine_category');
        $sc_challan_status = $request->get('sc_challan_status');
        $sc_enrollment_no = strtoupper($request->get('sc_enrollment_no'));
        $formattedfromDate = $request->get('formattedfromDate');
        $formattedtoDate = $request->get('formattedtoDate');
        $from_date = $request->get('from_date');
        $to_date = $request->get('to_date');
        $schoolId   = $request->get('school_id');
        $deptId     = $request->get('dept_id');
        $progId     = $request->get('prog_id');
        $admissionType = $request->get('admission_type');
        $admissionCategory = $request->get('admission_category');
        $hostelCategory = $request->get('hostel_category');
        $utilitiesCategory = $request->get('utilities_category');
        $hostelType = $request->get('hostel_type');
        //  dd($fine_category);

        $student_challan = "select student_challan.id,sc_app_id,invoiceamount,sc_challan_type,sc_enrollment_no,sc_challan_status,
        CASE
        WHEN sc_category_id IS NULL THEN 'N/A'
        WHEN sc_challan_type = 'shortcourse' THEN
            (
                SELECT
                    category
                FROM
                    sc_shortcourse_category
                WHERE
                    id = student_challan.sc_category_id
            )
        WHEN sc_challan_type = 'misc_fee'    THEN
            (
                SELECT
                    e_event_name
                FROM
                    events
                WHERE
                    e_id = student_challan.sc_category_id
            )
        ELSE
            (
                SELECT
                    fc_category_name
                FROM
                    fine_category
                WHERE
                    id = student_challan.sc_category_id
            )
        END AS categ_name,
        (select sai_student_status from students_apply_info where sai_enrollment_no = sc_enrollment_no) as student_status,
            (select course_title from sc_shortcourse_applied
        left join sc_shortcourses on sc_shortcourses.id = sc_shortcourse_applied.course_id
        where sc_app_id = sc_shortcourse_applied.id
        ) as course_name,
             (
        SELECT
            discount
        FROM
            sc_shortcourse_applied left
            JOIN sc_shortcourses ON sc_shortcourses.id = sc_shortcourse_applied.course_id where
            sc_app_id = sc_shortcourse_applied.id
    ) AS discount,
        student_challan.sc_studentname,sc_discount,
        student_challan.ffs_admission_amt,
        student_challan.sc_student_status,
        student_challan.sc_scholarship_amount,
        school_name,
        dept_name,
        prog_name,
        sc_admission_round,
        sc_session_type,
        sc_hostel_category,
        sc_hostel_fee_type,
        sc_hostel_fee_details,
        callback_paid_on,
        sc_issu_date,
        donations.email,
        donations.phone,
        donations.donation_type,
        donations.affiliation
        from student_challan
        left join schools on sc_school_id=school_id
        left join departments on sc_department_id=DEPT_ID
        left join programs on sc_program_id=prog_id
        left join donations on donations.challan_id = student_challan.id and student_challan.sc_challan_type = 'donation'
        where sc_challan_type in ('Processing_Fee','Full_semester','Gym','Fine','Credit_hours','Hostel_Fee','Short/Summer_Course','shortcourse','donation','misc_fee')";
        if (isset($sessionId) && $sessionId !== '') {
            $student_challan .= " AND sc_session_id = '$sessionId'";
        }
        if (isset($schoolId) && $schoolId !== '') {
            $student_challan .= " AND sc_school_id = '$schoolId'";
        }
        if (isset($deptId) && $deptId !== '') {
            $student_challan .= " AND sc_department_id = '$deptId'";
        }
        if (isset($progId) && $progId !== '') {
            $student_challan .= " AND sc_program_id = '$progId'";
        }
        if (isset($admissionType) && $admissionType !== '') {
            $student_challan .= " AND sc_student_status = '$admissionType'";
        }
        if (isset($sc_challan_type) && $sc_challan_type !== '') {
            $student_challan .= " AND sc_challan_type = '$sc_challan_type'";
        }
        if (isset($admissionCategory) && $admissionCategory !== '') {
            $student_challan .= " AND LOWER(sc_admission_round) = LOWER('$admissionCategory')";
        }
         if (isset($hostelCategory) && $hostelCategory !== '') {
            if ($hostelCategory == 'Hostel_other_utilities') {
                $student_challan .= " AND sc_hostel_fee_type = '$hostelCategory'";
            } else {
                $student_challan .= " AND LOWER(sc_hostel_category) = LOWER('$hostelCategory')";
            }
        }
        if (isset($utilitiesCategory) && $utilitiesCategory !== '') {
            $student_challan .= " AND sc_hostel_fee_details LIKE '%$utilitiesCategory%'";
        }
        if (isset($hostelType) && $hostelType !== '') {
            $student_challan .= " AND sc_session_type = '$hostelType'";
        }
        if (isset($sc_challan_status) && $sc_challan_status !== '') {
            $student_challan .= " AND sc_challan_status = '$sc_challan_status'";
        }
        if (isset($sc_enrollment_no) && $sc_enrollment_no !== '') {
            // Support comma-separated enrollment nos / app ids, e.g. F2021-001, F2022-002, 12345
            $enrollmentList = array_values(array_filter(array_map(function ($v) {
                return strtoupper(trim(str_replace("'", "''", $v)));
            }, explode(',', $sc_enrollment_no))));

            if (!empty($enrollmentList)) {
                $inList = "'" . implode("','", $enrollmentList) . "'";
                $student_challan .= " AND (UPPER(TRIM(sc_enrollment_no)) IN ($inList)";

                $numericIds = array_values(array_filter($enrollmentList, function ($e) {
                    return ctype_digit($e);
                }));
                if (!empty($numericIds)) {
                    $student_challan .= " OR sc_app_id IN (" . implode(',', $numericIds) . ")";
                }
                $student_challan .= ")";
            }
        }
        if (isset($from_date) && isset($from_date)) {
            if (isset($formattedfromDate, $formattedtoDate) && $formattedfromDate !== '' && $formattedtoDate !== '') {
                $student_challan .= " AND TRUNC(sc_issu_date) BETWEEN TO_DATE('$formattedfromDate', 'DD-MON-YY') AND TO_DATE('$formattedtoDate', 'DD-MON-YY')";
            }
        }
        if (isset($fine_category) && $fine_category !== '') {
            $student_challan .= " AND sc_category_id = '$fine_category'";
        }


        //    echo $student_challan;exit;
        $recstudent_challan = DB::select($student_challan);
        return DataTables::of($recstudent_challan)->toJson();

    }

     public function updateDates(Request $request)
    {
        // dd($request->all());
        $ids = $request->input('ids', []);
        $due_date = $request->input('due_date');
        $valid_date = $request->input('valid_date');
        $remarks = $request->input('sc_remarks');

        //  echo $due_date;exit;

        // if (empty($ids) || !$due_date || !$valid_date) {
        //     return response()->json(['message' => 'Invalid data provided.'], 400);
        // }

        DB::table('student_challan')
            ->whereIn('id', $ids)
            ->where('sc_challan_status', '!=', 'PAID')
            ->update([
                'sc_due_date' => $due_date,
                'sc_valid_date' => $valid_date,
                'sc_remarks' => $remarks,
                'SC_UPDATED_BY_USERNAME' => auth()->user()->username,
                'UPDATED_AT' => now()
            ]);

        return response()->json(['message' => 'Dates updated successfully.']);
    }
    function challanBulkCancel(){
        $sqlAcademicSessions = "select * from academic_sessions order by sess_id desc";
        $recsqlAcademicSessions = DB::select($sqlAcademicSessions);
        $Scholarship_Type = "select * from Scholarship_Type";
        $recScholarship_Type = DB::select($Scholarship_Type);
        $challanType = "select distinct sc_challan_type  from student_challan
        where sc_challan_type in ('Processing_Fee','Full_semester','Fine','Credit_hours','Hostel_Fee','Short/Summer_Course','shortcourse','donation','misc_fee')";

        $recchallanType = DB::select($challanType);
        $fineCateg = " select * from fine_category where fc_status = 'E'";
        $resfineCateg = DB::select($fineCateg);
        return view('grader.StudentRecord.student_challan_cancel',['academic_session'=>$recsqlAcademicSessions,'Scholarship_Type'=>$recScholarship_Type,'challan_type'=>$recchallanType,'resfineCateg'=>$resfineCateg]);

    }
     public function cancelBulkChallan(Request $request){
        // dd($request->all());
        $records = $request->input('records'); // array of ['id' => ..., 'sc_enrollment_no' => ...]
        $remarks = $request->input('sc_remarks');
        $username = auth()->user()->username;
        $employeeID = auth()->user()->employee_id;
        foreach ($records as $record) {
            $id = $record['id'];
            $enroll = $record['sc_enrollment_no'];
            $app_id = $record['sc_app_id'];

            DB::table('student_challan')
            ->where('id', $id)
            ->update([
                'sc_remarks' => $remarks.' From Bulk Challan Cancellation',
                'SC_ENTRY_USER_NAME' => $username,
                'SC_ENTRY_USER_ID' => $employeeID,
                'SC_UPDATED_BY_USERNAME' => auth()->user()->username,
                'UPDATED_AT' => now()
            ]);

            $procedureName = 'fin_bulk_challan_cancellation';
            $bindings_notification = [
                'challan_no' => $id,
                'enrollemnt_no' => $enroll,
                'app_id' => $app_id,
                'P_CANCELLED_USER' => $username,
                'P_REMARKS' => $remarks .' From Bulk Challan Cancellation'
            ];
            // dd($bindings_notification);
            $result = DB::executeProcedure($procedureName, $bindings_notification);



            // Process each challan using $id and $enroll
        }

        return response()->json(['message' => 'Challans cancelled']);
    }

     function cancelCourse(){

        if(in_array(Auth::user()->username, ['noreen', 'hina.razzaq', 'bilal.saleem'])){
            $school_id = auth()->user()->school_id;

            $recsqlAcademicSessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
                $sql =  "select * from schools where school_status = 'E'";
                $schools = DB::select($sql);
                // $departments = array(); // $this->school->fetch();
            return view('grader.StudentRecord.student_course_disable',[
                'academic_session'=>$recsqlAcademicSessions,
                'schools'=>$schools,

            ]);
        }else{
            abort(403, 'Unauthorized | You are not allowed');
        }

    }

    function getOfferedCourses(Request $request){
        $sessions = $request->get('academic_session');
        $schools = $request->get('schools');
        $departments = $request->get('department');
        $no_of_enrolled = $request->get('no_of_enrolled');

        //dd($schools,$departments);

        $sqlGetCourses ="select * from ( SELECT
                    co_id,
                    sess_code,
                    school_code,
                    school_name,
                    course_code,
                    course_cd_name || ' (' || get_teachername(co_id) || ')' as course_cd_name,
                    mt.co_section,
                    get_teachername(co_id)         teacher_name,
                    get_course_grading_type(co_id) grading_type,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            student_courses
                        WHERE
                            sc_co_id IN (
                                SELECT
                                    co_id
                                FROM
                                    courses_offered
                                WHERE
                                    co_id = mt.co_id
                                    OR parent_co_id = mt.co_id
                            )
                            AND sc_enrollment_status = 1

                    )                              no_of_enrolled,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            grade_history
                        WHERE
                            gh_co_id IN (
                                SELECT
                                    co_id
                                FROM
                                    courses_offered
                                WHERE
                                    co_id = mt.co_id
                                    OR parent_co_id = mt.co_id
                            )
                            AND gh_grade = 'W'
                            AND is_star = '*'
                    )                              AS withdraw_on_attendance,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            grade_history
                        WHERE
                            gh_co_id IN (
                                SELECT
                                    co_id
                                FROM
                                    courses_offered
                                WHERE
                                    co_id = mt.co_id
                                    OR parent_co_id = mt.co_id
                            )
                            AND gh_grade = 'W'
                            AND is_star IS NULL
                    )                              AS withdraw_by_student,
                    co_course_cr,directed_course
                FROM
                    courses_offered mt
                    LEFT JOIN academic_sessions ON co_sess_id = sess_id
                    LEFT JOIN schools ON co_school_id = school_id
                    LEFT JOIN courses_two ON co_course_id = course_id
                WHERE
                    ( parent_co_id = 0
                    OR parent_co_id IS NULL )
                    and co_status = 'E'
                    and co_sess_id= 164";

			if(isset($sessions) and  $sessions!=''){
				$sqlGetCourses .=" AND co_sess_id = ".$sessions;
			}
			if(isset($schools) and  $schools!=''){
				$sqlGetCourses .=" AND co_school_id = ".$schools;
			}
            if(isset($departments) and  $departments!=''){
				$sqlGetCourses .=" AND co_dept_id = ".$departments;
			}

            $sqlGetCourses .=" ORDER BY co_school_id, co_dept_id,co_course_id,co_section, parent_co_id desc)";

            if(isset($no_of_enrolled) and  $no_of_enrolled!=''){
				$sqlGetCourses .=" where no_of_enrolled < ".$no_of_enrolled;
			}
            //    echo $sqlGetCourses;exit;

        $ressqlGetCourses = DB::select($sqlGetCourses);
        return DataTables::of($ressqlGetCourses)->toJson();


    }

    public function updateCourseStatus(Request $request){

        $ids = $request->input('ids', []);

        $remarks = $request->input('sc_remarks')
            . ' - remarks given by '
            . auth()->user()->username
            . ' at '
            . Carbon::now('Asia/Karachi')->format('Y-m-d H:i:s');

        //  dd($remarks);
        DB::table('courses_offered')
            ->whereIn('co_id', $ids)
            ->update([
                'co_status' => 'D',
                'co_desc' => $remarks
            ]);

        DB::table('student_courses')
            ->whereIn('sc_cms_course_co_id', $ids)
            ->update([
                'sc_enrollment_status' => '0',
                'sc_desc' => $remarks,
            ]);

        return response()->json(['message' => 'Course Disable successfully.']);
    }


    function getRecord_Ro(){
        $sql = "SELECT
        ssd_id,
        ssd_sai_enrollment_no,
        ssd_type,
        ssd_percentage,
        ssd_details,
        sess_name,
        scholarship_name,
        spi_name,
        prog_name,
        school_name
        FROM
        students_scholarships_details
        left join SCHOLARSHIP_TYPE on  scholarship_id = ssd_type
        left join academic_sessions on sess_id = ssd_sess_id
        left join students_apply_info on sai_enrollment_no = ssd_sai_enrollment_no
        left join students_personal_info on sai_spi_id = spi_id
        left join programs on sai_sdpo_prog_id = prog_id
        left join schools on sai_sdpo_school_id = school_id
        where 1=1
        ORDER BY ssd_id DESC";
        // echo $sql;exit;
        $rec = DB::select($sql);

        $Scholarship_Type = "select * from Scholarship_Type";
        $recScholarship_Type = DB::select($Scholarship_Type);

        $sqlAcademicSessions = "select * from academic_sessions order by sess_id desc";
        $recsqlAcademicSessions = DB::select($sqlAcademicSessions);

        return view('grader.StudentRecord.student_record_Ro',['studentRecord'=>$rec,'Scholarship_Type'=>$recScholarship_Type,'academic_session'=>$recsqlAcademicSessions]);
    }
    function getSchlrshipDetailRecord(Request $request){
        // dd($request->all());

        $allowed_accounts_usernames = [
            'saqib.jamil',
            'esha.abid',
            'asif_nadeem',
            'ahmad.arfeen'
        ];
        $allowed_admission_usernames = [
            'sidra.tanveer',
            'asbah.rehman',
            'zainabsanik'
        ];

        $usernames_account_str = "'" . implode("','", $allowed_accounts_usernames) . "'";
        $usernames_adm_str = "'" . implode("','", $allowed_admission_usernames) . "'";
        if($request->studentType == 'All' || empty($request->studentType)){
            ////////////Existing Student Query/////////////
                $sqlExisting = "SELECT
                            ssd.ssd_id                  AS id,
                            ssd.ssd_app_id              AS apply_id,
                            ssd.ssd_sai_enrollment_no   AS enrollmentno,
                            sai.sai_student_status      AS student_status_cms,
                            cgpa(ssd.ssd_sai_enrollment_no) AS cgpa,
                            ssd.ssd_type                AS schlr_type,
                            ssd.ssd_percentage          AS schlr_percent,
                            ssd.ssd_arrear              AS arrear,
                            ssd.ssd_details             AS schlr_detail,
                            sess.sess_name              AS sess_name,
                            ssd.ssd_sess_id             AS session_id,
                            st.scholarship_name         AS schlr_name,
                            spi.spi_name                AS studnt_name,
                            prog.prog_name              AS prog_name,
                            scho.school_name            AS school_name,
                            'Existing' AS studenttype, -- Hardcoded value for studentType to keep check while deleting
                            'Academic' AS scholarship_category --
                        FROM students_scholarships_details   ssd
                            LEFT JOIN scholarship_type                st ON st.scholarship_id = ssd.ssd_type
                            LEFT JOIN academic_sessions               sess ON sess.sess_id = ssd.ssd_sess_id
                            LEFT JOIN students_apply_info             sai ON sai.sai_enrollment_no = ssd.ssd_sai_enrollment_no
                            LEFT JOIN students_personal_info          spi ON spi.spi_id = sai.sai_spi_id
                            LEFT JOIN admission_personal_info         api ON api.api_id = sai.sai_api_id
                            LEFT JOIN admission_apply_info            aai ON aai.aai_api_id = api.api_id
                                                                AND lower(aai.cms_sai_enrollment_no) = lower(sai.sai_enrollment_no)
                            LEFT JOIN programs                        prog ON sai.sai_sdpo_prog_id = prog.prog_id
                            LEFT JOIN schools                         scho ON sai.sai_sdpo_school_id = scho.school_id
                        where 1=1
                    ";
                    // Add the session condition if $request->session is not empty
                    if (!empty($request->session)) {
                        $sqlExisting .= " AND ssd.ssd_sess_id = " .$request->session;
                    }
                    // if (in_array(auth()->user()->username, $allowed_accounts_usernames)) {
                    //     // If username is in the allowed list, include it in the SQL query with IN
                    //     $sqlExisting .= " AND ssd.scd_entry_username IN ($usernames_account_str)";
                    // } else  if (in_array(auth()->user()->username, $allowed_admission_usernames)) {
                    //     // If username is in the allowed list, include it in the SQL query with IN
                    //     $sqlExisting .= " AND ssd.scd_entry_username IN ($usernames_adm_str)";
                    // } else {
                    //     // If username is not in the allowed list, exclude it using NOT IN
                    //     $sqlExisting .= " AND (ssd.scd_entry_username NOT IN ($usernames_account_str) AND ssd.scd_entry_username NOT IN ($usernames_adm_str) ) OR  ssd.ssd_project ='STD_FIN' OR  ssd.ssd_project ='SCH_PRO_NEW' ";
                    // }
                    // Add the ORDER BY clause
                    $sqlExisting .= " AND ssd_sai_enrollment_no is not null ORDER BY ssd.ssd_id DESC";
                    $students_scholarships_details = DB::select($sqlExisting);

            ////////////NEW Student Query/////////////
                $sqlNewAdm = "SELECT
                            ssd.ssd_id                  AS id,
                            ssd.ssd_app_id AS apply_id,
                            ssd.ssd_sai_enrollment_no   AS enrollmentno,
                            aai.aai_status              AS student_status_cms,
                            NULL                        AS cgpa, -- new admissions have no CGPA yet
                            ssd.ssd_type                AS schlr_type,
                            ssd.ssd_percentage          AS schlr_percent,
                            ssd.ssd_arrear              AS arrear,
                            ssd.ssd_details             AS schlr_detail,
                            sess.sess_name              AS sess_name,
                            ssd.ssd_sess_id             AS session_id,
                            st.scholarship_name         AS schlr_name,
                            api.API_NAME                AS studnt_name,
                            prog.prog_name              AS prog_name,
                            scho.school_name            AS school_name,
                            'New Admission' AS studenttype,
                            'Academic' AS scholarship_category --
                        FROM students_scholarships_details   ssd
                            LEFT JOIN scholarship_type                st ON st.scholarship_id = ssd.ssd_type
                            LEFT JOIN academic_sessions               sess ON sess.sess_id = ssd.ssd_sess_id
                            LEFT JOIN admission_apply_info            aai ON ssd.ssd_app_id = aai.aai_id
                            LEFT JOIN admission_personal_info         api ON  api.api_id = aai.aai_api_id
                            LEFT JOIN programs                        prog ON aai.aai_sdpo_prog_id = prog.prog_id
                            LEFT JOIN schools                         scho ON aai.aai_sdpo_school_id = scho.school_id
                    where 1=1
                    ";

                    // Add the session condition if $request->session is not empty
                        if (!empty($request->session)) {
                            $sqlNewAdm .= " AND ssd.ssd_sess_id = " .$request->session;
                        }
                        // if (in_array(auth()->user()->username, $allowed_accounts_usernames)) {
                        //     // If username is in the allowed list, include it in the SQL query with IN
                        //     $sqlNewAdm .= " AND ssd.scd_entry_username IN ($usernames_account_str)";
                        // } else  if (in_array(auth()->user()->username, $allowed_admission_usernames)) {
                        //     // If username is in the allowed list, include it in the SQL query with IN
                        //     $sqlNewAdm .= " AND ssd.scd_entry_username IN ($usernames_adm_str)";
                        // } else {
                        //     // If username is not in the allowed list, exclude it using NOT IN
                        //     $sqlNewAdm .= " AND (ssd.scd_entry_username NOT IN ($usernames_account_str) AND ssd.scd_entry_username NOT IN ($usernames_adm_str) ) OR  ssd.ssd_project ='STD_FIN' OR  ssd.ssd_project ='SCH_PRO_NEW' ";
                        // }
                        // Add the ORDER BY clause
                        $sqlNewAdm .= " AND ssd_sai_enrollment_no is null  ORDER BY ssd.ssd_id DESC";
                        $admission_scholarships_details = DB::select($sqlNewAdm);

            ////////////HOSTEL Existing Student Query/////////////
                $sqlExistingHostel = "SELECT
                    SSHD_ID as id,
                    SSHD_SAI_ENROLLMENT_NO as enrollmentNo,
                    sai.sai_student_status as student_status_cms,
                    cgpa(SSHD_SAI_ENROLLMENT_NO) as cgpa,
                    SSHD_APP_ID as apply_id,
                    SSHD_TYPE as schlr_type,
                    SSHD_PERCENTAGE as schlr_percent,
                    SSHD_ARREAR as arrear,
                    SSHD_DETAILS as schlr_detail,
                    sess_name  as sess_name,
                    SSHD_SESS_ID  as session_id,
                    scholarship_name as schlr_name,
                    api_name as studnt_name,
                    prog_name as prog_name,
                    school_name as school_name,
                    'Existing' as studenttype,
                    'Hostel' AS scholarship_category --
                    FROM
                    fin_Students_Scholarships_Hostel_Details
                    left join SCHOLARSHIP_TYPE on  scholarship_id = SSHD_TYPE
                    left join academic_sessions on sess_id = SSHD_SESS_ID
                    left join ADMISSION_APPLY_INFO on AAI_ID = SSHD_APP_ID
                    left join ADMISSION_PERSONAL_INFO on API_ID = AAI_API_ID
                    left join students_apply_info sai on lower(sai.sai_enrollment_no) = lower(SSHD_SAI_ENROLLMENT_NO)
                    left join programs on AAI_SDPO_PROG_ID = prog_id
                    left join schools on AAI_SDPO_SCHOOL_ID = school_id
                    where 1=1
                    ";


                    if (!empty($request->session)) {
                        $sqlExistingHostel .= " AND SSHD_SESS_ID = " .$request->session;
                    }
                    if (in_array(auth()->user()->username, $allowed_accounts_usernames)) {
                        $sqlExistingHostel .= " AND fin_Students_Scholarships_Hostel_Details.SSHD_ENTRY_USERNAME IN ($usernames_account_str)";
                    }else if (in_array(auth()->user()->username, $allowed_admission_usernames)) {
                        $sqlExistingHostel .= " AND fin_Students_Scholarships_Hostel_Details.SSHD_ENTRY_USERNAME IN ($usernames_adm_str)";
                    } else {
                        $sqlExistingHostel .= " AND ( fin_Students_Scholarships_Hostel_Details.SSHD_ENTRY_USERNAME NOT IN ($usernames_account_str) AND fin_students_scholarships_hostel_details.SSHD_ENTRY_USERNAME NOT IN ($usernames_adm_str) )    ";
                    }
                    // Add the ORDER BY clause
                    $sqlExistingHostel .= "AND SSHD_SAI_ENROLLMENT_NO is not null ORDER BY SSHD_ID DESC";
                    // echo $sqlExistingHostel;exit;
                $student_scholarships_hostel_details = DB::select($sqlExistingHostel);
            ////////////HOSTEL NEW Student Query/////////////
                $sqlNewHostel = "SELECT
                    SSHD_ID as id,
                    SSHD_SAI_ENROLLMENT_NO as enrollmentNo,
                    aai_status as student_status_cms,
                    NULL as cgpa, -- new admissions have no CGPA yet
                    SSHD_APP_ID as apply_id,
                    SSHD_TYPE as schlr_type,
                    SSHD_PERCENTAGE as schlr_percent,
                    SSHD_ARREAR as arrear,
                    SSHD_DETAILS as schlr_detail,
                    sess_name  as sess_name,
                    SSHD_SESS_ID  as session_id,
                    scholarship_name as schlr_name,
                    api_name as studnt_name,
                    prog_name as prog_name,
                    school_name as school_name,
                    'New Admission' as studenttype,
                    'Hostel' AS scholarship_category --
                    FROM
                    fin_Students_Scholarships_Hostel_Details
                    left join SCHOLARSHIP_TYPE on  scholarship_id = SSHD_TYPE
                    left join academic_sessions on sess_id = SSHD_SESS_ID
                    left join ADMISSION_APPLY_INFO on AAI_ID = SSHD_APP_ID
                    left join ADMISSION_PERSONAL_INFO on API_ID = AAI_API_ID
                    left join programs on AAI_SDPO_PROG_ID = prog_id
                    left join schools on AAI_SDPO_SCHOOL_ID = school_id
                    where 1=1
                    ";


                    if (!empty($request->session)) {
                        $sqlNewHostel .= " AND SSHD_SESS_ID = " .$request->session;
                    }
                    if (in_array(auth()->user()->username, $allowed_accounts_usernames)) {
                        $sqlNewHostel .= " AND fin_Students_Scholarships_Hostel_Details.SSHD_ENTRY_USERNAME IN ($usernames_account_str)";
                    }else if (in_array(auth()->user()->username, $allowed_admission_usernames)) {
                        $sqlNewHostel .= " AND fin_Students_Scholarships_Hostel_Details.SSHD_ENTRY_USERNAME IN ($usernames_adm_str)";
                    } else {
                        $sqlNewHostel .= " AND ( fin_Students_Scholarships_Hostel_Details.SSHD_ENTRY_USERNAME NOT IN ($usernames_account_str) AND fin_students_scholarships_hostel_details.SSHD_ENTRY_USERNAME NOT IN ($usernames_adm_str) )    ";
                    }
                    // Add the ORDER BY clause
                    $sqlNewHostel .= "AND SSHD_SAI_ENROLLMENT_NO is null ORDER BY SSHD_ID DESC";
                    // echo $sqlNewHostel;exit;
                $admission_scholarships_hostel_details = DB::select($sqlNewHostel);

            $data = array_merge($students_scholarships_details,$admission_scholarships_details, $student_scholarships_hostel_details, $admission_scholarships_hostel_details);

        }elseif($request->studentType == 'Existing'){
            ////////////Existing Student Query/////////////
                $sqlExisting = "SELECT
                                ssd.ssd_id                  AS id,
                                ssd.ssd_app_id              AS apply_id,
                                ssd.ssd_sai_enrollment_no   AS enrollmentno,
                                sai.sai_student_status      AS student_status_cms,
                                cgpa(ssd.ssd_sai_enrollment_no) AS cgpa,
                                ssd.ssd_type                AS schlr_type,
                                ssd.ssd_percentage          AS schlr_percent,
                                ssd.ssd_arrear              AS arrear,
                                ssd.ssd_details             AS schlr_detail,
                                sess.sess_name              AS sess_name,
                                ssd.ssd_sess_id             AS session_id,
                                st.scholarship_name         AS schlr_name,
                                spi.spi_name                AS studnt_name,
                                prog.prog_name              AS prog_name,
                                scho.school_name            AS school_name,
                                'Existing' AS studenttype, -- Hardcoded value for studentType to keep check while deleting
                                'Academic' AS scholarship_category --
                            FROM students_scholarships_details   ssd
                                LEFT JOIN scholarship_type                st ON st.scholarship_id = ssd.ssd_type
                                LEFT JOIN academic_sessions               sess ON sess.sess_id = ssd.ssd_sess_id
                                LEFT JOIN students_apply_info             sai ON sai.sai_enrollment_no = ssd.ssd_sai_enrollment_no
                                LEFT JOIN students_personal_info          spi ON spi.spi_id = sai.sai_spi_id
                                LEFT JOIN admission_personal_info         api ON api.api_id = sai.sai_api_id
                                LEFT JOIN admission_apply_info            aai ON aai.aai_api_id = api.api_id
                                                                    AND lower(aai.cms_sai_enrollment_no) = lower(sai.sai_enrollment_no)
                                LEFT JOIN programs                        prog ON sai.sai_sdpo_prog_id = prog.prog_id
                                LEFT JOIN schools                         scho ON sai.sai_sdpo_school_id = scho.school_id
                            where 1=1
                        ";

                        // Add the session condition if $request->session is not empty
                        if (!empty($request->session)) {
                            $sqlExisting .= " AND ssd.ssd_sess_id = " .$request->session;
                        }
                        // if (in_array(auth()->user()->username, $allowed_accounts_usernames)) {
                        //     // If username is in the allowed list, include it in the SQL query with IN
                        //     $sqlExisting .= " AND ssd.scd_entry_username IN ($usernames_account_str)";
                        // } else  if (in_array(auth()->user()->username, $allowed_admission_usernames)) {
                        //     // If username is in the allowed list, include it in the SQL query with IN
                        //     $sqlExisting .= " AND ssd.scd_entry_username IN ($usernames_adm_str)";
                        // } else {
                        //     // If username is not in the allowed list, exclude it using NOT IN
                        //     $sqlExisting .= " AND (ssd.scd_entry_username NOT IN ($usernames_account_str) AND ssd.scd_entry_username NOT IN ($usernames_adm_str) ) OR  ssd.ssd_project ='STD_FIN' OR  ssd.ssd_project ='SCH_PRO_NEW' ";
                        // }
                        // Add the ORDER BY clause
                    $sqlExisting .= " AND ssd_sai_enrollment_no is not null ORDER BY ssd.ssd_id DESC";
                    $students_scholarships_details = DB::select($sqlExisting);
            ////////////HOSTEL Existing Student Query/////////////
                $sqlExistingHostel = "SELECT
                    SSHD_ID as id,
                    SSHD_SAI_ENROLLMENT_NO as enrollmentNo,
                    sai.sai_student_status as student_status_cms,
                    cgpa(SSHD_SAI_ENROLLMENT_NO) as cgpa,
                    SSHD_APP_ID as apply_id,
                    SSHD_TYPE as schlr_type,
                    SSHD_PERCENTAGE as schlr_percent,
                    SSHD_ARREAR as arrear,
                    SSHD_DETAILS as schlr_detail,
                    sess_name  as sess_name,
                    SSHD_SESS_ID  as session_id,
                    scholarship_name as schlr_name,
                    api_name as studnt_name,
                    prog_name as prog_name,
                    school_name as school_name,
                    'Existing' as studenttype,
                    'Hostel' AS scholarship_category --
                    FROM
                    fin_Students_Scholarships_Hostel_Details
                    left join SCHOLARSHIP_TYPE on  scholarship_id = SSHD_TYPE
                    left join academic_sessions on sess_id = SSHD_SESS_ID
                    left join ADMISSION_APPLY_INFO on AAI_ID = SSHD_APP_ID
                    left join ADMISSION_PERSONAL_INFO on API_ID = AAI_API_ID
                    left join students_apply_info sai on lower(sai.sai_enrollment_no) = lower(SSHD_SAI_ENROLLMENT_NO)
                    left join programs on AAI_SDPO_PROG_ID = prog_id
                    left join schools on AAI_SDPO_SCHOOL_ID = school_id
                    where 1=1
                    ";


                    if (!empty($request->session)) {
                        $sqlExistingHostel .= " AND SSHD_SESS_ID = " .$request->session;
                    }
                    if (in_array(auth()->user()->username, $allowed_accounts_usernames)) {
                        $sqlExistingHostel .= " AND fin_Students_Scholarships_Hostel_Details.SSHD_ENTRY_USERNAME IN ($usernames_account_str)";
                    }else if (in_array(auth()->user()->username, $allowed_admission_usernames)) {
                        $sqlExistingHostel .= " AND fin_Students_Scholarships_Hostel_Details.SSHD_ENTRY_USERNAME IN ($usernames_adm_str)";
                    } else {
                        $sqlExistingHostel .= " AND ( fin_Students_Scholarships_Hostel_Details.SSHD_ENTRY_USERNAME NOT IN ($usernames_account_str) AND fin_students_scholarships_hostel_details.SSHD_ENTRY_USERNAME NOT IN ($usernames_adm_str) )    ";
                    }
                    // Add the ORDER BY clause
                    $sqlExistingHostel .= "AND SSHD_SAI_ENROLLMENT_NO is not null ORDER BY SSHD_ID DESC";
                    // echo $sqlExistingHostel;exit;
                    $student_scholarships_hostel_details = DB::select($sqlExistingHostel);
            $data = array_merge($students_scholarships_details, $student_scholarships_hostel_details);

        }else{
            ////////////NEW Student Query/////////////
                $sqlNewAdm = "SELECT
                         ssd.ssd_id                  AS id,
                        ssd.ssd_app_id AS apply_id,
                         sai.sai_enrollment_no AS enrollmentno,
                        case when sai.sai_student_status is null then aai_status else sai.sai_student_status end as student_status_cms,
                        case when sai.sai_enrollment_no is not null then cgpa(sai.sai_enrollment_no) end as cgpa,
                        ssd.ssd_type                AS schlr_type,
                        ssd.ssd_percentage          AS schlr_percent,
                        ssd.ssd_arrear              AS arrear,
                        ssd.ssd_details             AS schlr_detail,
                        sess.sess_name              AS sess_name,
                        ssd.ssd_sess_id             AS session_id,
                        st.scholarship_name         AS schlr_name,
                        api.API_NAME                AS studnt_name,
                        prog.prog_name              AS prog_name,
                        scho.school_name            AS school_name,
                        'New Admission' AS studenttype,
                        'Academic' AS scholarship_category --
                    FROM students_scholarships_details   ssd
                        LEFT JOIN scholarship_type                st ON st.scholarship_id = ssd.ssd_type
                        LEFT JOIN academic_sessions               sess ON sess.sess_id = ssd.ssd_sess_id
                        LEFT JOIN admission_apply_info            aai ON ssd.ssd_app_id = aai.aai_id
                        LEFT JOIN admission_personal_info         api ON  api.api_id = aai.aai_api_id
                        LEFT JOIN students_apply_info             sai ON sai.sai_enrollment_no = aai.cms_sai_enrollment_no
                        LEFT JOIN programs                        prog ON aai.aai_sdpo_prog_id = prog.prog_id
                        LEFT JOIN schools                         scho ON aai.aai_sdpo_school_id = scho.school_id
                where 1=1
                 ";

                   // Add the session condition if $request->session is not empty
                    if (!empty($request->session)) {
                        $sqlNewAdm .= " AND ssd.ssd_sess_id = " .$request->session;
                    }
                    // if (in_array(auth()->user()->username, $allowed_accounts_usernames)) {
                    //     // If username is in the allowed list, include it in the SQL query with IN
                    //     $sqlNewAdm .= " AND ssd.scd_entry_username IN ($usernames_account_str)";
                    // } else  if (in_array(auth()->user()->username, $allowed_admission_usernames)) {
                    //     // If username is in the allowed list, include it in the SQL query with IN
                    //     $sqlNewAdm .= " AND ssd.scd_entry_username IN ($usernames_adm_str)";
                    // } else {
                    //     // If username is not in the allowed list, exclude it using NOT IN
                    //     $sqlNewAdm .= " AND (ssd.scd_entry_username NOT IN ($usernames_account_str) AND ssd.scd_entry_username NOT IN ($usernames_adm_str) ) OR  ssd.ssd_project ='STD_FIN' OR  ssd.ssd_project ='SCH_PRO_NEW' ";
                    // }
                    // Add the ORDER BY clause
                    $sqlNewAdm .= " AND ssd_sai_enrollment_no is null  ORDER BY ssd.ssd_id DESC";
                $admission_scholarships_details = DB::select($sqlNewAdm);
            ////////////HOSTEL NEW Student Query/////////////
                $sqlNewHostel = "SELECT
                    SSHD_ID as id,
                    sai.sai_enrollment_no AS enrollmentno,
                    CASE
                        WHEN sai.sai_student_status IS NULL THEN
                            aai_status
                        ELSE
                            sai.sai_student_status
                    END                   AS student_status_cms,
                    CASE WHEN sai.sai_enrollment_no IS NOT NULL THEN cgpa(sai.sai_enrollment_no) END AS cgpa,
                    SSHD_APP_ID as apply_id,
                    SSHD_TYPE as schlr_type,
                    SSHD_PERCENTAGE as schlr_percent,
                    SSHD_ARREAR as arrear,
                    SSHD_DETAILS as schlr_detail,
                    sess_name  as sess_name,
                    SSHD_SESS_ID  as session_id,
                    scholarship_name as schlr_name,
                    api_name as studnt_name,
                    prog_name as prog_name,
                    school_name as school_name,
                    'New Admission' as studenttype,
                    'Hostel' AS scholarship_category --
                    FROM
                    fin_Students_Scholarships_Hostel_Details
                    left join SCHOLARSHIP_TYPE on  scholarship_id = SSHD_TYPE
                    left join academic_sessions on sess_id = SSHD_SESS_ID
                    left join ADMISSION_APPLY_INFO on AAI_ID = SSHD_APP_ID
                    left join ADMISSION_PERSONAL_INFO on API_ID = AAI_API_ID
                    LEFT JOIN students_apply_info           sai ON sai.sai_enrollment_no = admission_apply_info.cms_sai_enrollment_no
                    left join programs on AAI_SDPO_PROG_ID = prog_id
                    left join schools on AAI_SDPO_SCHOOL_ID = school_id
                    where 1=1
                    ";


                    if (!empty($request->session)) {
                        $sqlNewHostel .= " AND SSHD_SESS_ID = " .$request->session;
                    }
                    if (in_array(auth()->user()->username, $allowed_accounts_usernames)) {
                        $sqlNewHostel .= " AND fin_Students_Scholarships_Hostel_Details.SSHD_ENTRY_USERNAME IN ($usernames_account_str)";
                    }else if (in_array(auth()->user()->username, $allowed_admission_usernames)) {
                        $sqlNewHostel .= " AND fin_Students_Scholarships_Hostel_Details.SSHD_ENTRY_USERNAME IN ($usernames_adm_str)";
                    } else {
                        $sqlNewHostel .= " AND ( fin_Students_Scholarships_Hostel_Details.SSHD_ENTRY_USERNAME NOT IN ($usernames_account_str) AND fin_students_scholarships_hostel_details.SSHD_ENTRY_USERNAME NOT IN ($usernames_adm_str) )    ";
                    }
                    // Add the ORDER BY clause
                    $sqlNewHostel .= "AND SSHD_SAI_ENROLLMENT_NO is null ORDER BY SSHD_ID DESC";
                    // echo $sqlNewHostel;exit;
                $admission_scholarships_hostel_details = DB::select($sqlNewHostel);
            $data = array_merge($admission_scholarships_details, $admission_scholarships_hostel_details);
        }
        return DataTables::of($data)->toJson();
    }
    public function syncScholarshipEnrollmentFromAdmission(Request $request)
    {
        if ((int) Auth::user()->group_id !== 644) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to run this action.',
            ], 403);
        }

        $sqlAcademic = "UPDATE students_scholarships_details sc
                    SET sc.ssd_sai_enrollment_no = (
                        SELECT aai.cms_sai_enrollment_no
                        FROM admission_apply_info aai
                        WHERE aai.aai_id = sc.ssd_app_id
                    )
                    WHERE EXISTS (
                        SELECT 1
                        FROM admission_apply_info aai
                        WHERE aai.aai_id = sc.ssd_app_id
                    ) AND sc.ssd_sai_enrollment_no IS NULL";

        $sqlHostel = "UPDATE FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS sc
                    SET sc.SSHD_SAI_ENROLLMENT_NO = (
                        SELECT aai.cms_sai_enrollment_no
                        FROM admission_apply_info aai
                        WHERE aai.aai_id = sc.SSHD_APP_ID
                    )
                    WHERE EXISTS (
                        SELECT 1
                        FROM admission_apply_info aai
                        WHERE aai.aai_id = sc.SSHD_APP_ID
                    ) AND sc.SSHD_SAI_ENROLLMENT_NO IS NULL";

        try {
            DB::beginTransaction();
            $academicRows = DB::update($sqlAcademic);
            $hostelRows = DB::update($sqlHostel);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Enrollment numbers synced: academic scholarships ' . (int) $academicRows . ' row(s), hostel scholarships ' . (int) $hostelRows . ' row(s).',
                'academic_updated' => (int) $academicRows,
                'hostel_updated' => (int) $hostelRows,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage(),
            ], 500);
        }
    }
    function insertRecord(Request $request)
    {
        //    dd($request->all());
        $hostel_scholarship = $request->input('hostel_scholarship');
        if ($hostel_scholarship == 'Yes') {
            return $this->insertScholarshipDetails($request);
        }
        $validator = Validator::make($request->all(), [
            'academic_session' => 'required',
            'student_type' => 'required',
            'sch_enrollment_no' => 'required',//this will either contain enrollment no or aai_id
            'sch_aai_id' => 'required',//this will contain aai_id in both cases (exisitng and new)
            'sch_type' => 'required',
            'sch_details' => 'required',
           'sch_percentage' => [
                                'required',
                                'numeric',
                                'between:0,100',
                                'regex:/^\d{1,3}(\.\d{1,5})?$/'//allow 5 decomal places
                            ],

            'sch_arrear' => [
                'regex:/^\d{1,5}$/', // Allows integers from 0 to 100000, no decimals
            ],

        ], [
            'academic_session.required' => 'The academic session is required.',
            'student_type.required' => 'The student type is required.',
            'sch_enrollment_no.required' => 'The enrollment number is required.',
            'sch_type.required' => 'The scholarship type is required.',
            'sch_details.required' => 'The remarks are required.',
            'sch_percentage.required' => 'The scholarship percentage is required.',
            'sch_percentage.numeric' => 'The scholarship percentage must be a valid number.',
            'sch_percentage.between' => 'The scholarship percentage must be between 0 and 100.',
            'sch_percentage.regex' => 'The scholarship percentage must have up to 5 decimal places.',
            'sch_arrear.regex' => 'The arrears can have integer value between 0 to 100000.', // Custom error message
        ]);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'error' => $validator->getMessageBag()->toArray()
            ), 400);
        }
        $id = strtoupper($request->get('sch_enrollment_no'));//this will either contain enrollment no or aai_id
        $aai_id = $request->get('sch_aai_id');
        $percentage = $request->get('sch_percentage');
        $hostel_scholarship = $request->input('hostel_scholarship');
        $arrear = $request->get('sch_arrear');
        $type = $request->get('sch_type');
        $details = $request->get('sch_details');
        $sessions = $request->get('academic_session');
        $student_type = $request->get('student_type');

        $sqlSessionCode = "select * from academic_sessions where sess_id = $sessions";
        $ressqlSessionCode = DB::select($sqlSessionCode);
        $sess_name = explode(' ', $ressqlSessionCode[0]->sess_name)[0];
        // dd($sess_name);

        if (!is_numeric($id)) {
            // id is not numeric → treat it as enrollment no
            $enrollment = $id;
        } else {
            // id contains number only
            $enrollment = null;
        }
        // dd($enrollment,  $aai_id);

        // if($request->student_type == 'Existing'){
        DB::beginTransaction();
        try {
            $duplicateCheck = DB::table('Students_Scholarships_Details')
                ->select(
                    'SSD_ID',
                    'SSD_SAI_ENROLLMENT_NO',
                    'SSD_APP_ID',
                    'SSD_TYPE',
                    'SSD_SESS_ID',
                    'STATUS',
                    'SSD_PERCENTAGE'
                )
                ->where('SSD_APP_ID', $aai_id)
                ->where('ssd_type', $type)
                ->where('ssd_sess_id', $sessions)
                ->where('STATUS', 'E')
                ->get();
            // dd($duplicateCheck);
            if (sizeof($duplicateCheck) == 0) {
                //insert case
                $sumOfAllSchlships = DB::table('Students_Scholarships_Details')
                    ->where('SSD_APP_ID', $aai_id)
                    ->where('SSD_SESS_ID', $sessions)  // Session ID
                    ->where('STATUS', 'E')         // Status
                    ->sum('SSD_PERCENTAGE') + $percentage;   // Calculate sum and add percentage
                // dd($sumOfAllSchlships);
                if ($sumOfAllSchlships <= 100) {
                    $insert_employees = DB::table('Students_Scholarships_Details')->insert([
                        'ssd_sai_enrollment_no' => $enrollment,//enrollment no
                        'ssd_app_id' => $aai_id,//aai_id
                        'ssd_type' => $type,
                        'ssd_percentage' => $percentage,
                        'SSD_ARREAR' => $arrear,
                        'ssd_details' => $details,
                        'ssd_sess_id' => $sessions,
                        'ssd_project' => 'SCH_PRO_NEW',
                        'scd_entry_username' => auth()->user()->username,
                    ]);
                    // return response()->json(['success' => true, 'message' => 'Record added successfully'], 200);
                } else {
                    return response()->json(['success' => false, 'messege' => 'Scholarship Percentage Limit Exceeds 100%']);
                }
            } else {
                // dd(1);
                //update case
                $sumOfAllSchlships = DB::table('Students_Scholarships_Details')
                    ->where('SSD_APP_ID', $aai_id)
                    ->where('SSD_SESS_ID', $sessions)  // Session ID
                    ->where('STATUS', 'E')      // Status
                    ->where('SSD_ID', '!=', $duplicateCheck[0]->ssd_id)//while sum dont include the percentage of that specific row becasue we are adding it separately
                    ->sum('SSD_PERCENTAGE') + $percentage;   // Calculate sum and add percentage
                    // dd($sumOfAllSchlships->get()->toArray());
                    // dd($sumOfAllSchlships);
                if ($sumOfAllSchlships <= 100) {
                    DB::table('Students_Scholarships_Details')
                        ->where('SSD_ID', $duplicateCheck[0]->ssd_id)
                        ->update([
                            'ssd_sai_enrollment_no' => $enrollment,//enrollment no
                            'ssd_app_id' => $aai_id,//aai_id
                            'ssd_type' => $type,
                            'ssd_percentage' => $percentage,
                            'SSD_ARREAR' => $arrear,
                            'ssd_details' => $details,
                            'ssd_sess_id' => $sessions,
                            'ssd_project' => 'SCH_PRO_NEW',
                            'scd_entry_username' => auth()->user()->username,
                            'UPDATE_DATETIME' => now()->toDateTimeString()
                        ]);
                    // return response()->json(['success' => true, 'message' => 'Record updated successfully'], 200);
                } else {
                    return response()->json(['success' => false, 'messege' => 'Scholarship Percentage Limit Exceeds 100%']);
                }
            }
            // dd($request->student_type,  $sessions, $sess_name );
            // Legacy single-save no longer runs challan procedures — the active
            // multi-scholarship flow (saveAndPostScholarships) handles that.


            DB::commit();
            return response()->json(['success' => true, 'message' => 'Record Updated successfully'], 200);
        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            DB::rollBack();
            return response()->json(['success' => false, 'messege' => 'Something went wrong. (Duplicate entries not allowed)', 'errors' => $e->getMessage()], 500);
        }
    }

    function deletRecord(Request $request){

        $id = (int) $request->id;
        if ($id <= 0) {
            return response()->json(['success' => false, 'data' => 'Invalid record.']);
        }

        if($request->deleteCheck == 'Existing'){
            // Scholarship rows are never destroyed — zeroed instead. See
            // zeroScholarshipRow() for why.
            $res_delete = $this->zeroScholarshipRow($id, 'academic');
        }else{
            // A different table, outside the no-delete rule, but the id was interpolated
            // straight into the statement here — bound now.
            $res_delete = DB::delete(
                'delete from FIN_ADMISSION_SCHOLARSHIPS_DETAILS where FASD_ID = ?',
                [$id]
            );
        }

        if ($res_delete) {
            return response()->json([
                'success' => true,
                'messege' => 'Removed Successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Some problem occured'
            ]);
        }
    }

    function getProfile($roll_no){
        $std_roll_no = strtoupper($roll_no);
        $profile = "select spi_name,school_name,dept_name from students_apply_info
        left join students_personal_info on sai_spi_id = spi_id
        left join schools on school_id = sai_sdpo_school_id
        left join departments on dept_id = sai_sdpo_dept_id
        where sai_enrollment_no = '$std_roll_no'";
        $recprofile = DB::select($profile);
        if($recprofile){

            return response()->json(['data'=>$recprofile,'status' => true, 'message' => '1']);

        }else{
            return response()->json(['data'=>'','status' => false, 'message' => '0']);
        }

    }

    function getProfile_other_user2($roll_no){
        $std_roll_no = strtoupper($roll_no);
         $profile = "select
        sai_enrollment_no,spi_name,school_name,dept_name,spi_mobile,cp_partner ,
        VEHICLE_REGISTRATION.vr_vehicle_token,vr_vehicle_no
        from students_apply_info
        left join students_personal_info on sai_spi_id = spi_id
        left join schools on school_id = sai_sdpo_school_id
        left join departments on dept_id = sai_sdpo_dept_id
        left join car_pooling on lower(car_pooling.CP_ENTRY_USERNAME) = lower(sai_enrollment_no)
          left join VEHICLE_REGISTRATION on lower(vr_username) = lower(sai_enrollment_no)
        where upper(sai_enrollment_no) = '$std_roll_no'";
        //return DB::select($profile);
        return DB::select($profile);
    }


    function getProfile2(Request $request){
    //    dd($request->all());
        $std_roll_no = trim(explode("||",strtolower($request->input('rol_no')))[0]);
       //echo $std_roll_no;exit();
        $profile = "SELECT
        sai_id rn,
        sai_enrollment_no AS sai_enrollment_no,
        spi_name,
        cp_partner,
        cp_vehical_no,
        vehicle_registration.vr_vehicle_token,
        vr_status,
        group_id,
        cp_share_with,
        dept_name,
        school_name,
        spi_mobile
        FROM
            students_apply_info
            LEFT JOIN students_personal_info ON spi_id = sai_spi_id
            LEFT JOIN schools ON school_id = sai_sdpo_school_id
            LEFT JOIN departments ON dept_id = sai_sdpo_dept_id
            LEFT JOIN car_pooling ON lower(cp_entry_username) = lower(sai_enrollment_no)
            LEFT JOIN vehicle_registration ON lower(vr_username) = lower(sai_enrollment_no)
            LEFT JOIN sisusers ON lower(username) = lower(sai_enrollment_no)
        WHERE
             students_apply_info.sai_student_status in ('Active','Expected-Graduate')
            AND
                lower(sai_enrollment_no || spi_name || cp_vehical_no) LIKE '%$std_roll_no%'
        UNION ALL
        SELECT
        emp_employees.employee_id,
        emp_ad_username AS sai_enrollment_no,
        emp_f_name || ' ' || emp_l_name AS spi_name,
        cp_partner,
        cp_vehical_no,
        vehicle_registration.vr_vehicle_token,
        vr_status,
        group_id,
        cp_share_with,
        'Staff' AS dept_name,
        'Management' AS school_name,
        emp_contact_no as spi_mobile
        FROM
        emp_employees
        LEFT JOIN car_pooling ON lower(cp_entry_username) = lower(emp_ad_username)
        LEFT JOIN vehicle_registration ON lower(vr_username) = lower(emp_ad_username)
        LEFT JOIN sisusers ON lower(username) = lower(emp_ad_username)
        WHERE
            lower(emp_ad_username || emp_f_name || cp_vehical_no) LIKE lower('%$std_roll_no%')";
            $recprofile = DB::select($profile);
        //  echo $profile;exit;

        $attach_user_array = array();
        foreach(explode("~",$recprofile[0]->cp_partner) as $key=>$value){
            if($value!=""){
                //echo $key."__".$value;
               $user_array = $this->getProfile_other_user2($value);
               $attach_user_array[] = $user_array;
            }
        }
        $chkCarPooling = "select * from car_pooling where CP_PARTNER like lower(('%$std_roll_no%'))";
        $resCarPooling = DB::select($chkCarPooling);
        // if(!empty($resCarPooling)){
            $attach_user_array2 = array();
        if(count($resCarPooling)> 0){
        foreach(explode("~",$resCarPooling[0]->cp_partner) as $key=>$value){
            if($value!=""){
            //echo $key."__".$value;
            $user_array = $this->getProfile_other_user2($value);
            $attach_user_array2[] = $user_array;
            }
        }
        }
        // }
        // return $get_full_name;

        $car_token = "select * from vehicle_registration where lower(vr_username)='$std_roll_no'";
        $rec_car_token = DB::select($car_token);
        if($recprofile){
            return response()->json(
                [
                 'data'=>$recprofile,
                 'sub_data'=>$attach_user_array,
                 'sub_data2'=>$attach_user_array2,
                 'resCarPooling'=>$resCarPooling,
                 'status' => true,
                 'message' => '1',
                 'rec_car_token' => $rec_car_token
                ]);
        }else{
            return response()->json(['data'=>'','sub_data'=>'','status' => false, 'message' => '0']);
        }

    }
    function checkEnrollmentORaai_id($rollNo_OR_aai_id){
        $containsHyphen = strpos($rollNo_OR_aai_id, '-') !== false;// to check if contains hyphen
        $containsLetter = false;
        for ($i = 0; $i < strlen($rollNo_OR_aai_id); $i++) {//to check if contain alphabet
            if (ctype_alpha($rollNo_OR_aai_id[$i])) {
                $containsLetter = true;
                break;
            }
        }
        $containsOnlyNumbers = ctype_digit($rollNo_OR_aai_id); // to check if the only numbers
        if ($containsHyphen && $containsLetter) {
           return 'enrollment';
        } elseif ($containsOnlyNumbers) {
           return 'aaiId';
        } else {
            return 'invalid';
        }
    }
    private function checkIfDecreasScholarship($studentType, $enrollment, $type, $sessions, $newPercentage){
        if($studentType == 'Existing'){
            $scholarshipData = DB::table('Students_Scholarships_Details')
                                ->select(
                                        'SSD_ID',
                                        'SSD_SAI_ENROLLMENT_NO',
                                        'SSD_TYPE',
                                        'SSD_SESS_ID',
                                        'SSD_PERCENTAGE as oldpercentage',
                                        'SSD_ARREAR'
                                        )
                                        ->whereRaw('LOWER(ssd_sai_enrollment_no) = ?', [strtolower($enrollment)])//enrollment no
                                        ->where('ssd_type', $type)
                                        ->where('ssd_sess_id', $sessions)
                                        ->where('STATUS', 'E')
                                        ->get();
        }else{
            $scholarshipData = DB::table('FIN_ADMISSION_SCHOLARSHIPS_DETAILS')
                                ->select(
                                        'FASD_ID',
                                        'FASD_APP_ID',
                                        'FASD_SCH_TYPE',
                                        'FASD_SESS_ID',
                                        'FASD_STATUS',
                                        'FASD_SCH_PERCENTAGE as oldpercentage'
                                        )
                                        ->where('FASD_APP_ID', $enrollment)//apply id
                                        ->where('FASD_SCH_TYPE', $type)
                                        ->where('FASD_SESS_ID', $sessions)
                                        ->where('FASD_STATUS', 'E')
                                        ->get();
        }
        if(sizeof($scholarshipData) !== 0 ){
            // dd($scholarshipData[0]);
            // dd('new '.$newPercentage , 'old '.$scholarshipData[0]->oldpercentage);
            if($newPercentage < $scholarshipData[0]->oldpercentage){
                // dd('true');
                return true;
            }else{
                // dd('false');
                return false;
            }
            // dd('outer false');
            return true;
        }else{
            // dd('outer 2 false');
            return false;
        }

    }
    function getStudentType($session, $rawStudentType){
        if($rawStudentType == 'Existing'){
            $studentType = 'Existing';
            return $studentType;
        }else{
            $data = DB::table('Academic_sessions')
                    ->select('SESS_NAME as session_name')
                    ->where('SESS_ID', $session)
                    ->first();
            $studentType =  'New Admission '.strtoupper(explode(' ', $data->session_name)[0]);
            return $studentType;
        }


    }
    function insertInPendingScholarships($request){
        $id       = strtoupper($request->get('sch_enrollment_no'));//this will either contain enrollment no or aai_id
        $aai_id            = $request->get('sch_aai_id');
        $percentage       = $request->get('sch_percentage');
        $hostel_scholarship       = $request->input('hostel_scholarship');
        $arrear           = $request->get('sch_arrear');
        $type             = $request->get('sch_type');
        $details          = $request->get('sch_details');
        $sessions         = $request->get('academic_session');

        if (!is_numeric($id)) {
            // id is not numeric → treat it as enrollment no
            $enrollment = $id;
        } else {
            // id contains number only
            $enrollment = null;
        }
        // dd($enrollment,  $aai_id);
        DB::beginTransaction();
            try {
                $duplicateCheck = DB::table('FIN_ADMISSION_SCHOLARSHIPS_DETAILS')
                                ->select(
                                        'FASD_ID',
                                        'FASD_APP_ID',
                                        'FASD_SCH_TYPE',
                                        'FASD_SESS_ID',
                                        'FASD_STATUS',
                                        'FASD_SCH_PERCENTAGE'
                                        )
                                        ->where('FASD_APP_ID', $aai_id)//apply id
                                        ->where('FASD_SCH_TYPE', $type)
                                        ->where('FASD_SESS_ID', $sessions)
                                        ->where('FASD_STATUS', 'E')
                                        ->get();
                                        // dd($duplicateCheck);
                if(sizeof($duplicateCheck) == 0 ){
                    //insert case
                    $sumOfAllSchlships = DB::table('FIN_ADMISSION_SCHOLARSHIPS_DETAILS')
                                    ->where('FASD_APP_ID', $aai_id) // Apply ID
                                    ->where('FASD_SESS_ID', $sessions)  // Session ID
                                    ->where('FASD_STATUS', 'E')         // Status
                                    ->sum('FASD_SCH_PERCENTAGE') + $percentage;   // Calculate sum and add percentage
                    if($sumOfAllSchlships <= 100){
                        $insert_employees = DB::table('FIN_ADMISSION_SCHOLARSHIPS_DETAILS')->insert([
                            'FASD_APP_ID'           => $aai_id,//apply id
                            'FASD_ENROLLMENT_NO'    => $enrollment,//enrollment no
                            'FASD_SCH_TYPE'         => $type,
                            'FASD_SCH_PERCENTAGE'   => $percentage,
                            'FASD_ARREAR'   => $arrear,
                            'FASD_SCH_DETAILS'      => $details,
                            'FASD_SESS_ID'          => $sessions,
                            'FASD_ENTRY_USERNAME'    => auth()->user()->username,
                        ]);
                        // return response()->json(['success' => true, 'message' => 'Record added successfully'], 200);
                    }else{
                        return response()->json(['success' => false, 'messege'=> 'Scholarship Percentage Limit Exceeds 100%']);
                    }

                }else{
                    //update case
                    $sumOfAllSchlships = DB::table('FIN_ADMISSION_SCHOLARSHIPS_DETAILS')
                                        ->where('FASD_APP_ID', $aai_id) // Apply ID
                                        ->where('FASD_SESS_ID', $sessions)  // Session ID
                                        ->where('FASD_STATUS', 'E')         // Status
                                        ->where('FASD_ID', '!=' ,$duplicateCheck[0]->fasd_id)//while sum dont include the percentage of that specific row becasue we are adding it separately
                                        ->sum('FASD_SCH_PERCENTAGE') + $percentage;   // Calculate sum and add percentage
                    if($sumOfAllSchlships <= 100){
                        DB::table('FIN_ADMISSION_SCHOLARSHIPS_DETAILS')
                                    ->where('FASD_ID', $duplicateCheck[0]->fasd_id)
                                    ->update([
                                        'FASD_APP_ID'           => $aai_id,//apply id
                                        'FASD_ENROLLMENT_NO'    => $enrollment,//enrollment no
                                        'FASD_SCH_TYPE'         => $type,
                                        'FASD_SCH_PERCENTAGE'   => $percentage,
                                        'FASD_ARREAR'   => $arrear,
                                        'FASD_SCH_DETAILS'      => $details,
                                        'FASD_SESS_ID'          => $sessions,
                                        'FASD_ENTRY_USERNAME'    => auth()->user()->username,
                                    ]);
                        // return response()->json(['success' => true, 'message' => 'Record updated successfully'], 200);
                    }else{
                        return response()->json(['success' => false, 'messege'=> 'Scholarship Percentage Limit Exceeds 100%']);
                    }

                }
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Record Updated successfully'], 200);
            } catch (\Exception $e) {
                // Rollback the transaction if any exception occurs
                DB::rollBack();
                return response()->json(['success' => false, 'messege'=> 'Something went wrong.', 'errors' => $e->getMessage()], 500);
            }
    }




private function sendNewChallanMessage($username){

    $username = strtolower($username);
    $userDetail = User::whereRaw('LOWER(username) = ?', [$username])->first();

    // $full_name=$userDetail->full_name;
    $mobile=$userDetail->mobile;

    $full_name='Hamid';
    // $mobile='923029574211';
   // $mobile='923002519048';
    $message = "Dear Student, your scholarship details have been updated. Please review your new fee challan in your CMS account.";

    //sendSMSMessage($mobile, $message);
    // $sms = new SendSms();
    //$sms->teacher_marks_change_sendmail( $userDetail->email,'CMS LOGIN OTP',$mess);
    try {
        sendSMSMessage($mobile, $message);
        return true; // Indicate success
    } catch (\Exception $e) {
        // Return false for SMS sending failure
        return false;
    }
}

public function insertScholarshipDetails(Request $request)
{
    // dd($request->all());
    $maxAmount = $request->input('allowed-amount');
    // dd($maxAmount);
    $validator = Validator::make($request->all(),[
        'academic_session' => 'required',
        'student_type' => 'required',
        'sch_enrollment_no' => 'required',
        'sch_type' => 'required',
        'sch_details' => 'required',
        'sch_amount' => [
            'required',
            'numeric',
            'max:' . $maxAmount, // Set max dynamically
        ],
        'hostelfee' => 'required',
        'sch_percentage' => [
                'required',
                'numeric',
                'between:0,100', // Ensures the value is between 0 and 100
                'regex:/^\d{1,3}(\.\d{1,2})?$/' // Allows numbers with up to 2 decimal places
            ],
        'sch_arrear' => [
                            'regex:/^\d{1,5}$/', // Allows integers from 0 to 100000, no decimals
                        ],

    ], [
        'academic_session.required' => 'The academic session is required.',
        'student_type.required' => 'The student type is required.',
        'sch_enrollment_no.required' => 'The enrollment number is required.',
        'sch_type.required' => 'The scholarship type is required.',
        'sch_details.required' => 'The remarks are required.',
        'sch_amount.required' => 'The scholarship amount is required.',
        'sch_amount.numeric' => 'The scholarship amount must be a valid number.',
        'sch_amount.max' => "The scholarship amount cannot exceed $maxAmount.", // Dynamic error message
        'sch_percentage.required' => 'The scholarship percentage is required.',
        'sch_percentage.numeric' => 'The scholarship percentage must be a valid number.',
        'sch_percentage.between' => 'The scholarship percentage must be between 0 and 100.',
        'sch_percentage.regex' => 'The scholarship percentage must have up to 2 decimal places.',
        'sch_arrear.regex' => 'The arrears can have integer value between 0 to 100000.', // Custom error message
    ]);
    if($validator->fails()) {
        return Response::json(array(
            'success' => false,
            'error' => $validator->getMessageBag()->toArray()
        ), 400);
    }
    $academicSession = $request->input('academic_session');
    $enrollmentNo = $request->input('sch_enrollment_no');
    $enrollment_aaiId = $request->input('sch_enrollment_no');
    $type = $request->input('sch_type');
     $aai_id = $request->get('sch_aai_id');
    $percentage = $request->input('sch_percentage');
    $sch_amount = $request->input('sch_amount');
    $hostelfee = $request->input('hostelfee');
    $arrear = $request->input('sch_arrear');
    $details = $request->input('sch_details');
    $hostelScholarship = $request->input('hostel_scholarship');
    $hostelFeeStruct_type = $request->input('hostle_fee_structure');
    $appId = null;

    $response = ['success' => false, 'messege' => 'An unknown error occurred.'];

    DB::beginTransaction();
    try {
        if (is_numeric($enrollmentNo)) {//if new student set coming enrollment number field as AppID and set enrollment no to null
            $appId = (int)$enrollmentNo;
            $enrollmentNo = null;
        } else {//if exisitng student then keep enrollment  no field unchanged (it contain the enrollment number) and get the appid from the DB
            $appId = DB::table('admission_apply_info')
                ->whereRaw('LOWER(cms_sai_enrollment_no) = ?', [strtolower($enrollmentNo)])
                ->value('aai_id');
        }

        if (!$enrollmentNo && !$appId) {
            throw new Exception('Invalid Enrollment Number or Applicant ID.');
        }
        if ($hostelScholarship === 'Yes') {

            $existingRecord = DB::table('fin_students_scholarships_hostel_details')
                // ->whereRaw('LOWER(sshd_sai_enrollment_no) = ?', [strtolower($enrollmentNo)])
                ->where('SSHD_APP_ID', $appId)
                ->where('SSHD_SESS_ID', $academicSession)
                ->where('sshd_type', $type)
                ->first();

            $sumFromDetails = (float)DB::table('Students_Scholarships_Details')
                // ->whereRaw('LOWER(ssd_sai_enrollment_no) = ?', [strtolower($enrollmentNo)])
                ->where('SSD_APP_ID', $appId)
                ->where('SSD_SESS_ID', $academicSession)
                ->where('STATUS', 'E')
                ->sum('SSD_PERCENTAGE') ?? 0;

            $sumFromHostelDetails = (float)DB::table('fin_students_scholarships_hostel_details')
                // ->whereRaw('LOWER(sshd_sai_enrollment_no) = ?', [strtolower($enrollmentNo)])
                ->where('SSHD_APP_ID', $appId)
                ->where('SSHD_SESS_ID', $academicSession)
                ->sum('SSHD_PERCENTAGE') ?? 0;

            // $totalScholarshipUsed = $sumFromDetails + $sumFromHostelDetails;
            $totalScholarshipUsed =  $sumFromHostelDetails;
            // dd($existingRecord);
            if ($existingRecord) {
                // Calculate new total by subtracting the existing percentage and adding the new one
                $currentPercentage = $existingRecord->sshd_percentage;
                $updatedSum = $totalScholarshipUsed - $currentPercentage + $percentage;

                if ($updatedSum > 100) {
                    throw new Exception("You have already utilized $totalScholarshipUsed% of your scholarship limit. Updating to $percentage% will exceed the limit.");
                }

                // Update the existing record
                DB::table('fin_students_scholarships_hostel_details')
                    ->where('sshd_id', $existingRecord->sshd_id)
                    ->update([
                        'sshd_percentage' => $percentage,
                        'SSHD_AMOUNT' => $sch_amount,
                        'SSHD_HOSTEL_FEE' => $hostelfee,
                        'SSHD_ARREAR' => $arrear,
                        'sshd_details' => $details,
                        'SSHD_STATUS' => 'E',
                        'SSHD_entry_username' => auth()->user()->username,
                        'SSHD_UPDATE_DATETIME' => Carbon::now('Asia/Karachi'),
                    ]);
                $response = ['success' => true, 'message' => 'Hostel Scholarship updated successfully!'];
            } else {
                $updatedSum = $totalScholarshipUsed + $percentage;

                if ($updatedSum > 100) {
                    throw new Exception("You have already utilized $totalScholarshipUsed% of your scholarship limit. Adding $percentage% will exceed the limit.");
                }

                // Insert new record
                DB::table('fin_students_scholarships_hostel_details')->insert([
                    'sshd_sai_enrollment_no' => strtoupper($enrollmentNo),
                    'sshd_type' => $type,
                    'sshd_percentage' => $percentage,
                    'SSHD_AMOUNT' => $sch_amount,
                    'SSHD_HOSTEL_FEE' => $hostelfee,
                    'SSHD_ARREAR' => $arrear,
                    'SSHD_APP_ID' => $appId,
                    'SSHD_STATUS' => 'E',
                    'SSHD_DETAILS' =>$details,
                    'SSHD_SESS_ID' => $academicSession,
                    'SSHD_entry_username' => auth()->user()->username,
                    'SSHD_ENTRY_DATETIME' => Carbon::now('Asia/Karachi'),
                ]);
                $response = ['success' => true, 'message' => 'Hostel Scholarship inserted successfully!'];
            }
        }
        DB::commit();
        // dd((Int)$academicSession, strtoupper($enrollment_aaiId));
        $this->runHostelChallanProcedures($aai_id, $enrollment_aaiId, $academicSession);
        // dd($result);
    } catch (\Exception $e) {
        DB::rollBack();
        $response = ['success' => false, 'messege' => $e->getMessage()];
    }

    return response()->json($response);
}
public function getHostelScholarship_student($session=null, $student_id=null, $scholarship_id=null){
    // dd($session,$student_id, $scholarship_id );
    $studentHostel_schlrshp_record = DB::table('FIN_STUDENTS_SCHOLARSHIPS_HOSTEL_DETAILS')
                                        ->select('SSHD_TYPE', 'SSHD_PERCENTAGE', 'SSHD_AMOUNT')
                                        ->where('SSHD_SESS_ID', $session)
                                        ->where(function ($query) use ($student_id) {
                                            $query->where('SSHD_SAI_ENROLLMENT_NO', strtoupper($student_id))
                                                ->orWhere('SSHD_APP_ID', strtoupper($student_id));
                                        })
                                        ->get();
    // dd($studentHostel_schlrshp_record);
    $totalAwarded_amount = 0;
    $totalAwarded_percentage = 0;
    if(!empty($studentHostel_schlrshp_record)){
        foreach($studentHostel_schlrshp_record as $value){
            if($value->sshd_type != $scholarship_id){//this ensures if the user is about to update already entered scholarship then the limit of amount should not include the amount of that scholarship  which is already entered because this is gonna update
                $totalAwarded_amount = round($totalAwarded_amount + (float) $value->sshd_amount, 2);
                $totalAwarded_percentage = round($totalAwarded_percentage + (float) $value->sshd_percentage, 2);
            }

        }
        // dd($totalAwarded_amount, $totalAwarded_percentage);
        return response()->json(['previous_hotel_Record'=>$studentHostel_schlrshp_record,'totalAwarded_amount'=>$totalAwarded_amount,'totalAwarded_percentage'=>$totalAwarded_percentage,'status' => true, 'message' => '1']);
    }else{
        return response()->json(['previous_hotel_Record'=>$studentHostel_schlrshp_record,'totalAwarded_amount'=>$totalAwarded_amount,'totalAwarded_percentage'=>$totalAwarded_percentage,'status' => false, 'message' => '0']);
    }
}

public function getHostelFeeStructure(Request $request, $session=null, $hostelfeeStruct_type=null, $student_id=null){

    // Regular -> col name=FHFS_AMOUNT_PER_SEMESTER
    // Non-Regular -> col name = FHFS_AMOUNT_PER_MONTH
    // dd($session, $hostelfeeStruct_type, $student_id);

    // Map the page's Student Type to the hostel fee-structure student type.
    $studentType = $request->query('student_type');
    $ffsStudentType = ($studentType === 'Existing') ? 'Existing' : 'New_Admission';

    // ---------------------------------------------------------------------
    // Resolve the fee type from the student's ACTUAL hostel assignment.
    //
    // The assignment row now records both halves of the answer directly —
    // HA_HOSTEL_SESSION_TYPE (regular / regular_outside / non-regular / …) and
    // HA_HOSTEL_LOCATION_PREFERENCE (on_campus / off_campus) — written by the bed
    // assignment page. HostelAssignmentResolver reads those first and keeps the old
    // HOSTEL_ASSIGN -> BEDS -> ROOMS -> TYPE join only as a fallback.
    //
    // That fixes two things the join could not:
    //   - an assignment carrying only HA_CHALLAN_ID has no bed, so HT_ID came back NULL
    //     and the campus silently fell back to whatever the dropdown said (104 rows from
    //     session 165 onward);
    //   - Regular vs Non-Regular was never read from the database at all, only from the
    //     NON_REGULAR_HOSTEL_ENROLLMENTS list below. The column names 27 Non-Regular
    //     students in session 167; the list names 8 of them.
    // ---------------------------------------------------------------------
    $normalizedId = strtoupper(trim((string) $student_id));

    $assignment = (new HostelAssignmentResolver())->resolve(
        $session,
        is_numeric($student_id) ? $student_id : null,
        is_numeric($student_id) ? null : $normalizedId
    );

    // Residency, in order of authority: the assignment's own session type, then the
    // hardcoded list (kept as a safety net for a student whose column is not set), then
    // the grader's dropdown choice.
    if ($assignment['residency'] !== null) {
        $residency = $assignment['residency'];
    } elseif (in_array($normalizedId, self::NON_REGULAR_HOSTEL_ENROLLMENTS, true)) {
        $residency = 'Non-Regular';
    } else {
        $residency = (stripos($hostelfeeStruct_type, 'Non-Regular') !== false)
            ? 'Non-Regular'
            : 'Regular';
    }

    // Shape kept for the code below, which reads ht_id / ht_name.
    $assigned = $assignment['assigned'] ? (object) [
        'ht_id'   => $assignment['htId'],
        'ht_name' => $assignment['htName'],
    ] : null;

    $autoDetected = false;
    $notice = null;
    $hostelAssigned = $assignment['assigned'];   // whether the student has a hostel this session

    if ($hostelAssigned) {
        if ($assignment['isBedian']) {
            // Bedian — intentionally not supported for now.
            return response()->json([
                'hostelFee'     => null,
                'status'        => false,
                'message'       => 'This student is assigned a Bedian hostel — Bedian hostel scholarships are not supported yet.',
                'resolved_type' => $hostelfeeStruct_type,
                'auto_detected' => false,
                'hostel_assigned' => $hostelAssigned,
                'notice'        => null,
            ]);
        }

        if ($assignment['sessionType'] !== null) {
            // The assignment knows the whole answer — residency AND campus — so the
            // dropdown is driven from it and locked. Rebuilt from $residency rather than
            // used verbatim so the hardcoded Non-Regular safety net still applies to a
            // student whose column says Regular.
            $hostelfeeStruct_type = (stripos($assignment['sessionType'], '_outside') !== false)
                ? $residency . '_outside'
                : $residency;
            $autoDetected = true;
        } else {
            // Assigned, but neither column nor bed chain says where. Keep the manual
            // choice — and say so, because silence here used to look like agreement.
            $notice = 'This student has a hostel assignment, but it does not record a campus '
                . 'or session type. Choose the Hostel Fee Structure manually.';
        }
    } else {
        // No assignment for this session: keep the manual selection, but inform the user.
        $notice = 'No hostel is assigned to this student in the selected session.';
    }

    // Determine which column to select based on session type
    // $amountColumn = ($hostelfeeStruct_type == 'Regular') ? 'FHFS_AMOUNT_PER_SEMESTER' : 'FHFS_AMOUNT_PER_MONTH';
    if (strpos($hostelfeeStruct_type, 'Non-Regular') === false) {//if regular or regular_outside
        // dd('regular');
        $hostelFee = DB::table('FIN_HOSTEL_FEE_STRUCTURE')
                    ->select('FHFS_AMOUNT_PER_SEMESTER as fee_amount')
                    ->where('FHFS_SESSION', $session)
                    ->where('FHFS_SESSION_TYPE', $hostelfeeStruct_type)
                    ->where('FHFS_STUDENT_TYPE', $ffsStudentType)
                    ->where('FHFS_STATUS', 'E')
                    ->first();
    }else{//non regular , non regular_outside
        //  dd('non regular');
        $hostelFee = DB::table('STUDENT_CHALLAN')
                ->select('FFS_HOSTEL_AMT as fee_amount')
                ->where('SC_SESSION_ID', $session)
                ->where('SC_HOSTEL_FEE_TYPE', '!=', 'Hostel_other_utilities');
                if (is_numeric($student_id)) {
                     $hostelFee = $hostelFee->where('SC_APP_ID', '=', $student_id);
                } else {
                   $hostelFee = $hostelFee->where('SC_ENROLLMENT_NO', '=', strtoupper($student_id));
                }
                $hostelFee = $hostelFee->first();

    }
    // dd($hostelFee);
    if(!empty($hostelFee)){
        // Get the dynamic column value
        $feeAmount = $hostelFee->fee_amount;
        return response()->json([
            'hostelFee'     => $feeAmount,
            'status'        => true,
            'message'       => '1',
            'resolved_type' => $hostelfeeStruct_type,
            'auto_detected' => $autoDetected,
            'hostel_assigned' => $hostelAssigned,
            'notice'        => $notice,
        ]);
    }else{
        if(strpos($hostelfeeStruct_type, 'Non-Regular') !== false){//means its Non Regular

            return response()->json([
                'hostelFee'     => $hostelFee,
                'status'        => false,
                'message'       => 'For Non-Regular please generate challan first',
                'resolved_type' => $hostelfeeStruct_type,
                'auto_detected' => $autoDetected,
                'hostel_assigned' => $hostelAssigned,
                'notice'        => $notice,
            ]);
        }else{
            return response()->json([
                'hostelFee'     => $hostelFee,
                'status'        => false,
                'message'       => 'Please define hostel fee structure first',//if no record found
                'resolved_type' => $hostelfeeStruct_type,
                'auto_detected' => $autoDetected,
                'hostel_assigned' => $hostelAssigned,
                'notice'        => $notice,
            ]);
        }

    }
}
    // public function updateHoltelType(Request $request){
    //     $allow = $this->isAllowEdit($request);
    //     if ($allow['status'] == false) {
    //         $challans = implode(', ', $allow['invalid_challans']); // convert array to comma-separated string
    //          return response()->json([
    //             'success' => false,
    //             'message' => "Challans ({$challans}) are scholarship challans and cannot be edited. Unselect these challans and try again."
    //         ]);
    //     }

    //     // dd($request->all());
    //     try {
    //         $ids = $request->input('ids', []);
    //         $fromType = $request->input('from_type');
    //         $toType = $request->input('to_type');
    //         $remarks = $request->input('remarks');

    //         // Validate inputs
    //         if (empty($ids)) {
    //             return response()->json(['success' => false, 'message' => 'No records selected'], 400);
    //         }

    //         if (empty($fromType)) {
    //             return response()->json(['success' => false, 'message' => 'From Hostel Type is required'], 400);
    //         }

    //         if (empty($toType)) {
    //             return response()->json(['success' => false, 'message' => 'To Hostel Type is required'], 400);
    //         }
    //         $challans = DB::table('student_challan')->whereIn('id', $ids)->get();
    //         // dd($challans);
    //         $updated=0;

    //         foreach($challans as $challan){
    //             if($challan->sc_challan_status != 'PAID'){
    //                 $st_school_id = $challan->sc_school_id;
    //                 $st_dept_id = $challan->sc_department_id;
    //                 $st_prog_id = $challan->sc_program_id;
    //                 $sessionId = $challan->sc_session_id;
    //                 $SinlgeChallanDueDate = $challan->sc_due_date;
    //                 $SinlgeChallanValidateDate = $challan->sc_valid_date;
    //                 $fee_type = $challan->sc_hostel_fee_type;
    //                 $hostelSecurityFee = $challan->ffs_security_amt;
    //                 $studentType = $challan->sc_student_status;
    //                 $daysNo = $challan->sc_hostel_days;
    //                 $refrigeratorCheckBox = $challan->sc_refrigerator;
    //                 $conditionerCheckBox = $challan->sc_conditioner;
    //                 $electricHeaterCheckBox = $challan->sc_electric;
    //                 $hostelRemarks = $challan->sc_remarks;
    //                 $sc_app_id = $challan->sc_app_id;

    //                 $editId = $challan->id;
    //                 $procedureName = 'fin_new_std_hostel_chln_updt';
    //                 $bindings = [
    //                     'p_schol_id' => $st_school_id,
    //                     'p_dept_id' => $st_dept_id,
    //                     'p_prog_id' => $st_prog_id,
    //                     'p_session' => $sessionId,
    //                     'p_due_date' => $SinlgeChallanDueDate,
    //                     'p_valid_date' =>  $SinlgeChallanValidateDate,
    //                     'p_ffs_student_type' => $fee_type,
    //                     'p_hostel_security' => $hostelSecurityFee,
    //                     'p_studentType' => $studentType,
    //                     'p_no_of_days' => $daysNo,
    //                     'refrigeratorCheckBox' => $refrigeratorCheckBox,
    //                     'conditionerCheckBox' => $conditionerCheckBox,
    //                     'electricHeaterCheckBox' => $electricHeaterCheckBox,
    //                     'hostelRemarks' => $hostelRemarks,
    //                     'p_AAI_ID' => $sc_app_id,
    //                     'session_type' => $toType,
    //                     'p_challan_no' => $editId,
    //                     'p_entry_username' => auth()->user()->username
    //                 ];
    //                 // dd($bindings);
    //                 $result = DB::executeProcedure($procedureName, $bindings);
    //                 $updated++;
    //             }

    //         }
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Hostel type updated successfully!',
    //             'updated_count' => $updated
    //         ]);

    //     } catch (\Exception $e) {
    //         // dd( $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error updating hostel type: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
    // public function isAllowEdit($request)
    // {
    //     $challan_id = $request->ids;
    //     $invalidChallans = [];
    //     foreach($challan_id as $challanId){
    //         $sc_challan = DB::table('student_challan')->select('sc_scholarship_amount', 'challan_no' )->where('id', $challanId)->first();

    //         if (!empty($sc_challan->sc_scholarship_amount) && $sc_challan->sc_scholarship_amount != 0) {
    //             $invalidChallans[] = $sc_challan->challan_no;
    //         }
    //     }
    //     if (!empty($invalidChallans)) {
    //         return [
    //             'status' => false,
    //             'invalid_challans' => $invalidChallans
    //         ];
    //     }else{
    //         return [
    //             'status' => true,
    //             'invalid_challans' => $invalidChallans
    //         ];
    //     }

    // }

    /**
     * Hostel challan work for a saved scholarship: cancel the existing hostel
     * challan and regenerate it with the scholarship deduction. Extracted from
     * insertScholarshipDetails so the Post flow reuses the same procedures.
     */
    public function runHostelChallanProcedures($aai_id, $enrollment_aaiId, $academicSession)
    {
        $hostelExisitngChallan = DB::table('STUDENT_CHALLAN')
            ->select('*')
            ->where('SC_SESSION_ID', $academicSession)
            ->where('SC_APP_ID', $aai_id)
            ->where('SC_CHALLAN_TYPE', 'Hostel_Fee')
            ->whereIn('SC_HOSTEL_FEE_TYPE', ['Hostel_Full_semster', 'Hostel_day_wise'])
            ->first();
        // dd($hostelExisitngChallan->ffs_security_amt > 0 ? 'New_Admission' : 'Existing');
        if (!empty($hostelExisitngChallan)) {//first cancel exisitng challan and then generate new challan
            $procedureName = 'fin_bulk_challan_cancellation';
            $bindings = [
                'P_SC_CHALLAN_ID' => $hostelExisitngChallan->challan_no,
                'P_ENROLLMENT_NO' => ctype_digit($hostelExisitngChallan->sc_enrollment_no) ? NULL : $hostelExisitngChallan->sc_enrollment_no,
                'P_SC_APP_D' => $hostelExisitngChallan->sc_app_id,
                'P_CANCELLED_USER' => auth()->user()->username,
                'P_REMARKS' => 'For scholarship revision / entry'

            ];
            // dd($bindings);
            // Execute the stored procedure
            $result = DB::executeProcedure($procedureName, $bindings);

            $procedureName = 'fin_new_std_hostel_chln_gen';
            $bindings = [
                'p_schol_id' => $hostelExisitngChallan->sc_school_id,
                'p_dept_id' => $hostelExisitngChallan->sc_department_id,
                'p_prog_id' => $hostelExisitngChallan->sc_program_id,
                'p_session' => $hostelExisitngChallan->sc_session_id,
                'p_due_date' => $hostelExisitngChallan->sc_due_date,
                'p_valid_date' => $hostelExisitngChallan->sc_valid_date,
                'p_ffs_student_type' => $hostelExisitngChallan->sc_hostel_fee_type,
                'p_hostel_security' => $hostelExisitngChallan->ffs_security_amt,
                'p_hostel_studentType' => $hostelExisitngChallan->ffs_security_amt > 0 ? 'New_Admission' : 'Existing',
                'p_no_of_days' => $hostelExisitngChallan->sc_hostel_days,
                'refrigeratorCheckBox' => 'NO',
                'conditionerCheckBox' => 'NO',
                'electricHeaterCheckBox' => 'NO',
                'hostelRemarks' => 'For scholarship revision / entry',
                'p_AAI_ID' =>  strtoupper($enrollment_aaiId),
                'session_type' => $hostelExisitngChallan->sc_session_type,
                'p_entry_username' => auth()->user()->username,
                'p_studentType' => $hostelExisitngChallan->sc_student_status,
            ];
            // dd($bindings);
            $update = DB::executeProcedure($procedureName, $bindings);
        }
    }

    /**
     * A student is "summer admission" when their enrollment number contains SUM
     * (e.g. SUM24-0021). Everyone else with an enrollment is a regular Fall /
     * Spring student who may take a summer course. New admissions have no
     * enrollment number yet and are treated as summer-admission (tuition based).
     *
     * The SUM substring test — not a first-letter prefix — is the convention used
     * across the challan flows (`sc_sai_enrollment_no NOT LIKE '%SUM%'` in
     * GenerateBulkChallanController / GenerateCreditHrBulkChallanController).
     * A first-letter test would be wrong: "SUM24-0021" also starts with "S".
     */
    /**
     * Student Type values that mean "this student is ENROLLED and has an enrollment
     * number". They are looked up in students_apply_info by enrollment no, exactly
     * like 'Existing'. The only thing that sets them apart is the FFS_STUDENT_TYPE
     * they match in fin_fee_structure — see resolveFeeStructureStudentType().
     *
     * 'New without Registration' is NOT here: that student has no enrollment number
     * yet and is looked up in admission_apply_info by AAI_ID.
     */
    public const NEW_ADMISSION_STUDENT_TYPES = [
        'New Admission FALL',
        'New Admission SPRING',
        'New Admission SUMMER',
    ];

    private function isEnrolledStudentType($studentType)
    {
        $studentType = trim((string) $studentType);
        return $studentType === 'Existing'
            || in_array($studentType, self::NEW_ADMISSION_STUDENT_TYPES, true);
    }

    /**
     * The FFS_STUDENT_TYPE to match in fin_fee_structure (compared upper-cased).
     *
     *   Existing                  -> 'EXISTING'
     *   New Admission FALL/…      -> that exact value, e.g. 'NEW ADMISSION FALL'
     *   New without Registration  -> derived from the selected session's season,
     *                                e.g. session "Fall 2026" -> 'NEW ADMISSION FALL'
     */
    private function resolveFeeStructureStudentType($studentType, $session)
    {
        $studentType = trim((string) $studentType);

        if (in_array($studentType, self::NEW_ADMISSION_STUDENT_TYPES, true)) {
            return strtoupper($studentType);
        }
        if ($studentType === 'Existing') {
            return 'EXISTING';
        }

        // New without Registration: the season comes from the session name.
        $sessName = DB::table('academic_sessions')->where('sess_id', $session)->value('sess_name');
        $season = $sessName ? strtoupper(strtok(trim($sessName), ' ')) : '';
        return 'NEW ADMISSION ' . $season;
    }

    private function isSummerAdmissionEnrollment($studentType, $enrollment)
    {
        if (!$this->isEnrolledStudentType($studentType)) {
            return true;   // New without Registration -> no enrollment no -> tuition based
        }
        return stripos((string) $enrollment, 'SUM') !== false;
    }

    /**
     * Challan categories that can carry arrears (one-time fee waivers recorded on
     * the Other Scholarship page as FIN_OTHER_SCHOLARSHIPS.FOS_CHALLAN_CATEGORY).
     *
     * additional_Full_semester is deliberately excluded — it bills only the extra
     * credit hours on an existing full-semester challan, whose one-time fees are
     * already waived by that challan's own Full_semester arrears.
     *
     * Must stay in sync with ScholarshipAssignController::ARREARS_CHALLAN_CATEGORIES.
     */
    public const ARREARS_CHALLAN_CATEGORIES = [
        'Full_semester',
        'Credit_hours',
        'Short/Summer_Course',
    ];

    private function challanCategoryHasArrears($challanCategory)
    {
        return in_array(trim((string) $challanCategory), self::ARREARS_CHALLAN_CATEGORIES, true);
    }

    /**
     * Intake ranges on FIN_FEE_STRUCTURE were only introduced from the session
     * AFTER this one, so they are meaningless for older sessions.
     */
    private const INTAKE_RANGE_FROM_SESSION = 167;

    /**
     * Sessions that charge for REPEAT credit hours (at half the per-credit rate),
     * on top of the enrolled credit hours:
     *   164 = Summer 2025, 165 = Fall 2025, 166 = Spring 2026.
     * No other session bills repeat credits.
     */
    private const REPEAT_CREDIT_SESSIONS = [164, 165, 166];

    /**
     * Repeat credit hours for a student in a session, from FIN_STUDENTS_CREDITS_DATA
     * (FSCD_REGNO = enrollment no, FSCD_STD_ENROLLMENT_SESSION = the selected session).
     * Returns 0 when the session does not bill repeat credits, or when there is no row.
     */
    private function resolveRepeatCreditHours($session, $enrollmentNo)
    {
        if (!in_array((int) $session, self::REPEAT_CREDIT_SESSIONS, true) || empty($enrollmentNo)) {
            return 0.0;
        }

        $repeat = DB::table('FIN_STUDENTS_CREDITS_DATA')
            ->whereRaw('UPPER(TRIM(FSCD_REGNO)) = ?', [strtoupper(trim((string) $enrollmentNo))])
            ->where('FSCD_STD_ENROLLMENT_SESSION', $session)
            ->orderByDesc('FSCD_ID')
            ->value('FSCD_STD_REPEAT_CREDITS');

        return max(0.0, (float) $repeat);
    }

    /**
     * Apply the fee-structure intake-range match to a fin_fee_structure query.
     *
     *  session >  167 : the student's intake (roadmap) session must fall inside the
     *                   row's range. Rows with no range defined (NULL columns) still
     *                   match — they are not excluded.
     *  session <= 167 : the intake range is NOT compared at all — the row matches
     *                   whether or not it defines one. (Requiring both columns to be
     *                   NULL here was wrong: real fee rows for these sessions DO carry
     *                   an intake range, and were being silently excluded.)
     */
    private function applyFeeStructureIntakeRange($query, $session, $roadmapSess)
    {
        if ((int) $session <= self::INTAKE_RANGE_FROM_SESSION) {
            return $query;   // no intake filtering for these sessions
        }

        if (!empty($roadmapSess)) {
            $query->where(function ($q) use ($roadmapSess) {
                $q->whereNull('FFS_INTAKE_FROM_SESS')
                  ->orWhere('FFS_INTAKE_FROM_SESS', '<=', $roadmapSess);
            })->where(function ($q) use ($roadmapSess) {
                $q->whereNull('FFS_INTAKE_TO_SESS')
                  ->orWhere('FFS_INTAKE_TO_SESS', '>=', $roadmapSess);
            });
        }

        return $query;
    }

    /**
     * Resolve the academic fee structure for a student by challan category and
     * return a structured result. School/dept/program + roadmap session come
     * from the student record (students_apply_info for Existing,
     * admission_apply_info for New). The challan category decides which
     * ffs_fee_type row and which base to use:
     *   - Full_semester -> FFS_TUTIONFEE_AMT (arrears apply)
     *   - Credit_hours -> FFS_CREDHR_AMT, requires enrolled courses this session
     *   - additional_Full_semester -> FFS_CREDHR_AMT, requires an UNPAID
     *     Full_semester challan with enrolled credit hours
     *   - Short/Summer_Course -> TWO rows may exist for the same key. The one to
     *     use is chosen by the enrollment number:
     *       SUM… (or new admission) -> the row with FFS_TUTIONFEE_AMT > 0
     *                                  (charged like a full semester, arrears apply)
     *       Fall / Spring           -> the row with FFS_CREDHR_AMT  > 0
     *                                  (per credit x enrolled summer credits;
     *                                   arrears still apply — only tuition is 0
     *                                   on that row, the one-time fees may not be)
     * For Credit_hours / additional_Full_semester the amount base = per-credit fee
     * x credit hours and arrears do not apply.
     */
    private function resolveAcademicFeeStructure($session, $student_id, $studentType, $challanCategory = 'Full_semester')
    {
        // 'Existing' AND the three 'New Admission <SEASON>' types are all enrolled
        // students looked up by enrollment no. Only 'New without Registration' is
        // looked up in admission_apply_info by AAI_ID.
        $isExisting = $this->isEnrolledStudentType($studentType);

        // 1) student school / dept / program + roadmap session
        if ($isExisting) {
            $stu = DB::table('students_apply_info')
                ->select(
                    'SAI_SDPO_SCHOOL_ID as school_id',
                    'SAI_SDPO_DEPT_ID as dept_id',
                    'SAI_SDPO_PROG_ID as prog_id',
                    'SAI_SDPO_SESS_ID as roadmap_sess'
                )
                ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower((string) $student_id)])
                ->first();
        } else {
            $stu = DB::table('admission_apply_info')
                ->select(
                    'AAI_SDPO_SCHOOL_ID as school_id',
                    'AAI_SDPO_DEPT_ID as dept_id',
                    'AAI_SDPO_PROG_ID as prog_id',
                    'AAI_SDPO_SESS_ID as roadmap_sess'
                )
                ->where('AAI_ID', $student_id)
                ->first();
        }

        if (empty($stu)) {
            return ['ok' => false, 'reason' => 'no_fee_structure', 'message' => 'Student record not found.'];
        }

        // 2) challan category -> ffs_fee_type + credit-hour checks
        $creditHours = null;
        $repeatCredits = 0.0;         // billed at HALF the per-credit rate (Credit_hours only)
        $creditBased = false;         // per-credit amount math
        $summerCreditBased = false;   // per-credit amount math, summer fee row
        $amountPredicate = null;      // disambiguates the two Short/Summer rows

        // Arrears applicability is INDEPENDENT of the amount math: Credit_hours uses
        // per-credit pricing AND carries arrears, while additional_Full_semester uses
        // per-credit pricing and carries none.
        $arrearsApplicable = $this->challanCategoryHasArrears($challanCategory);

        switch ($challanCategory) {
            case 'Credit_hours':
                $creditBased = true;
                $ffsFeeType = 'Credit_hours';
                $creditHours = (float) DB::table('student_courses')
                    ->where('SC_CO_SESS_ID', $session)
                    ->whereRaw('LOWER(SC_SAI_ENROLLMENT_NO) = ?', [strtolower((string) $student_id)])
                    ->where('SC_ENROLLMENT_STATUS', 1)
                    ->whereIn('SC_STATUS', ['Withdrawal', 'Block', 'A'])
                    ->sum('SC_PC_COURSE_CR');
                // Sessions 164-166 also bill REPEAT credit hours, at half the per-credit
                // rate, on top of the enrolled ones. Existing students only — a new
                // admission has no enrollment no to look up in FIN_STUDENTS_CREDITS_DATA.
                if ($isExisting) {
                    $repeatCredits = $this->resolveRepeatCreditHours($session, $student_id);
                }
                if (!$creditHours || $creditHours <= 0) {
                    return ['ok' => false, 'reason' => 'no_enrollment', 'message' => 'Student is not enrolled in any course for this session — scholarship not allowed.'];
                }
                // The degree-level credit limit (Undergraduate 1-9.5,
                // Postgraduate/Doctorate 1-6) is enforced ONLY when the student
                // has no existing Credit_hours challan for the session; if such a
                // challan exists, skip the limit check (PDF minor change).
                $hasCreditChallan = DB::table('student_challan')
                    ->where('SC_SESSION_ID', $session)
                    ->where('SC_CHALLAN_TYPE', 'Credit_hours')
                    ->where(function ($q) use ($student_id) {
                        if (is_numeric($student_id)) {
                            $q->where('SC_APP_ID', $student_id);
                        } else {
                            $q->whereRaw('LOWER(SC_ENROLLMENT_NO) = ?', [strtolower((string) $student_id)]);
                        }
                    })
                    ->exists();
                if (!$hasCreditChallan) {
                    $degreeLevel = DB::table('programs')->where('prog_id', $stu->prog_id)->value('PROG_DEGREE_LEVEL');
                    $isPostgrad  = in_array(strtolower((string) $degreeLevel), ['postgraduate', 'doctorate']);
                    $creditLimit = $isPostgrad ? 6 : 9.5;
                    if ($creditHours > $creditLimit) {
                        return [
                            'ok'       => false,
                            'reason'   => 'credit_out_of_range',
                            'switchTo' => 'Full_semester',
                            'message'  => 'Enrolled credit hours (' . $creditHours . ') exceed the credit-hour limit for ' . ($degreeLevel ?: 'this program') . ' (max ' . $creditLimit . '). Challan Category switched to Full Semester.',
                        ];
                    }
                }
                break;

            case 'additional_Full_semester':
                $creditBased = true;
                $ffsFeeType = 'Credit_hours';
                $challan = DB::table('student_challan')
                    ->select('SC_ENROLLED_CR_HR')
                    ->whereRaw('LOWER(SC_ENROLLMENT_NO) = ?', [strtolower((string) $student_id)])
                    ->where('SC_SESSION_ID', $session)
                    ->where('SC_CHALLAN_TYPE', 'Full_semester')
                    ->whereNotNull('SC_ENROLLED_CR_HR')
                    ->where('SC_ENROLLED_CR_HR', '!=', 0)
                    ->where('SC_CHALLAN_STATUS', 'UNPAID')
                    ->orderByDesc('id')
                    ->first();
                if (empty($challan)) {
                    return ['ok' => false, 'reason' => 'no_challan', 'message' => 'Please generate the additional credit-hour challan for this student first.'];
                }
                $creditHours = (float) $challan->sc_enrolled_cr_hr;
                break;

            case 'Short/Summer_Course':
                $ffsFeeType = 'Short/Summer_Course';
                // Two rows can share this fee type. Pick by enrollment number, and
                // filter on the populated amount so the choice is never left to
                // Oracle's row order. NVL is required: `FFS_CREDHR_AMT > 0` is
                // UNKNOWN (not false) for a NULL column, which silently drops the row.
                if ($this->isSummerAdmissionEnrollment($studentType, $student_id)) {
                    // SUM student / new admission -> charged like a full semester.
                    $amountPredicate = 'NVL(FFS_TUTIONFEE_AMT, 0) > 0';
                } else {
                    // Regular Fall/Spring student taking a summer course -> per credit.
                    $summerCreditBased = true;
                    $amountPredicate = 'NVL(FFS_CREDHR_AMT, 0) > 0';
                    $creditHours = (float) DB::table('student_courses')
                        ->where('SC_CO_SESS_ID', $session)
                        ->whereRaw('LOWER(SC_SAI_ENROLLMENT_NO) = ?', [strtolower((string) $student_id)])
                        ->where('SC_ENROLLMENT_STATUS', 1)
                        ->whereIn('SC_STATUS', ['Withdrawal', 'Block', 'A'])
                        ->sum('SC_PC_COURSE_CR');
                    if (!$creditHours || $creditHours <= 0) {
                        return ['ok' => false, 'reason' => 'no_enrollment', 'message' => 'Student is not enrolled in any course for this session — scholarship not allowed.'];
                    }
                    // No degree-level credit limit on the summer path.
                }
                break;

            case 'Full_semester':
            default:
                $ffsFeeType = 'Full_semester';
                break;
        }

        // 3) fee-structure row for the resolved fee type
        $query = DB::table('fin_fee_structure')
            ->select(
                'FFS_TUTIONFEE_AMT as fee_base',
                'FFS_CREDHR_AMT as credhr_amt',
                'FFS_ADMISSION_AMT as admission_amt',
                'FFS_SECURITY_AMT as security_amt',
                'FFS_ACTIVITY_AMT as activity_amt',
                'FFS_MEDICAL_AMT as medical_amt'
            )
            ->where('ffs_sess_id', $session)
            ->where('ffs_school_id', $stu->school_id)
            ->where('ffs_dept_id', $stu->dept_id)
            ->where('ffs_prog_id', $stu->prog_id)
            ->where('ffs_fee_type', $ffsFeeType)
            ->where('ffs_status', 'E');

        // Only Short/Summer_Course is ambiguous (two rows per key); the predicate
        // selects the tuition-style or the credit-hour-style row.
        if (!empty($amountPredicate)) {
            $query->whereRaw($amountPredicate);
        }

        // Match FFS_STUDENT_TYPE exactly: 'EXISTING', or the selected
        // 'NEW ADMISSION <SEASON>'. Case-insensitive so stored casing still matches.
        $query->whereRaw('UPPER(ffs_student_type) = ?', [
            $this->resolveFeeStructureStudentType($studentType, $session),
        ]);

        // The student's roadmap session must be within the fee structure intake
        // range — but only for sessions after 167; older sessions predate intake
        // ranges and must match the rows that have none. See applyFeeStructureIntakeRange().
        $this->applyFeeStructureIntakeRange($query, $session, $stu->roadmap_sess ?? null);

        // Deterministic pick: without an ORDER BY, Oracle may return either row
        // when a key is (mis)configured with duplicates. Newest wins.
        $fee = $query->orderByDesc('FFS_ID')->first();
        if (empty($fee)) {
            // Name the exact row that is missing. "Not defined for this student" is
            // useless when the profile card is showing amounts pulled from OTHER
            // fee types — the user needs to know which row to create.
            $sessName = DB::table('academic_sessions')->where('sess_id', $session)->value('sess_name');
            $needed = 'Fee Type "' . $ffsFeeType . '"';
            if ($amountPredicate) {
                $needed .= $summerCreditBased
                    ? ' with a Per Credit Fee greater than 0'
                    : ' with a Tuition Fee greater than 0';
            }
            return [
                'ok'      => false,
                'reason'  => 'no_fee_structure',
                'message' => 'No fee structure found for ' . $needed
                    . ' in ' . ($sessName ?: 'the selected session')
                    . ' for this student\'s school / department / program. Please define it in the Master Fee Structure form.',
            ];
        }

        // 4) build the structured result.
        //
        // The one-time fee total is the arrears CAP and comes from the SAME fee row
        // that priced the scholarship. For Credit_hours the Master Fee Structure form
        // keeps Tuition/Admission/Security at 0 and lets the user set Medical +
        // Activity — so in practice the Credit_hours cap is Medical + Activity.
        // Categories without arrears report a cap of 0.
        $oneTime = $arrearsApplicable
            ? (float) $fee->admission_amt + (float) $fee->security_amt
                + (float) $fee->activity_amt + (float) $fee->medical_amt
            : 0.0;

        // Per-credit pricing: Credit_hours and additional_Full_semester (own fee row),
        // and a Fall/Spring student on a summer course (the Short/Summer credit row).
        // They differ only in whether arrears apply — hence $arrearsApplicable, not
        // the pricing flag, drives the arrears fields.
        if ($creditBased || $summerCreditBased) {
            $perCredit = (float) $fee->credhr_amt;
            // Repeat credit hours are charged at HALF the per-credit rate:
            //   base = perCredit x enrolled  +  perCredit x (repeat / 2)
            // $repeatCredits is 0 outside sessions 164-166 and outside Credit_hours,
            // so this collapses to the plain per-credit total everywhere else.
            $base = ($perCredit * $creditHours) + ($perCredit * ($repeatCredits / 2));
            return [
                'ok'                => true,
                'reason'            => null,
                'message'           => '1',
                'feeType'           => $ffsFeeType,
                'referenceFee'      => $perCredit,
                'referenceLabel'    => 'Per Credit Fee',
                'amountBase'        => $base,
                'feeLineTotal'      => $base,
                'creditHours'       => $creditHours,
                'repeatCredits'     => $repeatCredits,
                'oneTimeFeeTotal'   => $oneTime,
                'arrearsApplicable' => $arrearsApplicable,
            ];
        }

        $tuition = (float) $fee->fee_base;

        // Tuition-based categories: Full_semester, and Short/Summer_Course for a
        // SUM / new-admission student. Percentage applies to the tuition fee.
        return [
            'ok'                => true,
            'reason'            => null,
            'message'           => '1',
            'feeType'           => $ffsFeeType,
            'referenceFee'      => $tuition,
            'referenceLabel'    => 'Tuition Fee',
            'amountBase'        => $tuition,
            'feeLineTotal'      => $tuition,
            'creditHours'       => null,
            'oneTimeFeeTotal'   => $oneTime,
            'arrearsApplicable' => $arrearsApplicable,
        ];
    }

    /**
     * Compact current-session fee-structure values for the profile card:
     * tuition + one-time fees from the Full_semester row, per-credit fee from
     * the Credit_hours row (fallback to the Full_semester row's per-credit).
     * Returns nulls when a row is missing (no error).
     */
    private function getAcademicFeeStructureSummary($session, $student_id, $studentType)
    {
        $isExisting = $this->isEnrolledStudentType($studentType);
        if ($isExisting) {
            $stu = DB::table('students_apply_info')
                ->select('SAI_SDPO_SCHOOL_ID as school_id', 'SAI_SDPO_DEPT_ID as dept_id', 'SAI_SDPO_PROG_ID as prog_id', 'SAI_SDPO_SESS_ID as roadmap_sess')
                ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower((string) $student_id)])
                ->first();
        } else {
            $stu = DB::table('admission_apply_info')
                ->select('AAI_SDPO_SCHOOL_ID as school_id', 'AAI_SDPO_DEPT_ID as dept_id', 'AAI_SDPO_PROG_ID as prog_id', 'AAI_SDPO_SESS_ID as roadmap_sess')
                ->where('AAI_ID', $student_id)
                ->first();
        }
        if (empty($stu)) {
            return null;
        }

        // Fresh query per fee type, with the same school/dept/prog/student_type
        // + NULL-tolerant intake-range match used by resolveAcademicFeeStructure.
        $ffsStudentType = $this->resolveFeeStructureStudentType($studentType, $session);

        $baseQuery = function ($feeType, $amountPredicate = null) use ($session, $stu, $ffsStudentType) {
            $q = DB::table('fin_fee_structure')
                ->where('ffs_sess_id', $session)
                ->where('ffs_school_id', $stu->school_id)
                ->where('ffs_dept_id', $stu->dept_id)
                ->where('ffs_prog_id', $stu->prog_id)
                ->where('ffs_fee_type', $feeType)
                ->where('ffs_status', 'E');
            if (!empty($amountPredicate)) {
                $q->whereRaw($amountPredicate);
            }
            $q->whereRaw('UPPER(ffs_student_type) = ?', [$ffsStudentType]);
            // Same intake-range rule as resolveAcademicFeeStructure(): compared only
            // for sessions after 167; older sessions match the rows that have no range.
            $this->applyFeeStructureIntakeRange($q, $session, $stu->roadmap_sess ?? null);
            return $q;
        };

        $amountCols = ['FFS_TUTIONFEE_AMT as tuition', 'FFS_ADMISSION_AMT as admission', 'FFS_SECURITY_AMT as security', 'FFS_MEDICAL_AMT as medical', 'FFS_ACTIVITY_AMT as activity', 'FFS_CREDHR_AMT as per_credit'];

        $full   = $baseQuery('Full_semester')->select($amountCols)->orderByDesc('FFS_ID')->first();
        $credit = $baseQuery('Credit_hours')->select($amountCols)->orderByDesc('FFS_ID')->first();

        // Summer sessions often define ONLY Short/Summer_Course rows, so the panel
        // would otherwise show "—" for everything. Two rows may exist: a
        // tuition-style one and a credit-hour-style one (see resolveAcademicFeeStructure).
        $summerTuition = $baseQuery('Short/Summer_Course', 'NVL(FFS_TUTIONFEE_AMT, 0) > 0')->select($amountCols)->orderByDesc('FFS_ID')->first();
        $summerCredit  = $baseQuery('Short/Summer_Course', 'NVL(FFS_CREDHR_AMT, 0) > 0')->select($amountCols)->orderByDesc('FFS_ID')->first();

        // A configured 0 means "not charged", which for this panel reads the same as
        // "not defined" — so coalesce past 0 as well as NULL. (?? only catches NULL,
        // and the master fee form now forces FFS_CREDHR_AMT = 0 on Full_semester rows.)
        $pick = function ($field, ...$rows) {
            foreach ($rows as $row) {
                if (empty($row)) {
                    continue;   // that fee type is not defined for this student
                }
                $v = $row->{$field} ?? null;
                if ($v !== null && (float) $v > 0) {
                    return $v;
                }
            }
            return null;
        };

        return [
            'tuition'    => $pick('tuition',   $full, $summerTuition, $summerCredit),
            'admission'  => $pick('admission', $full, $summerTuition, $summerCredit),
            'security'   => $pick('security',  $full, $summerTuition, $summerCredit),
            'medical'    => $pick('medical',   $full, $summerTuition, $summerCredit),
            'activity'   => $pick('activity',  $full, $summerTuition, $summerCredit),
            'per_credit' => $pick('per_credit', $credit, $summerCredit),
        ];
    }

    /**
     * Academic fee base for the page, by challan category. Returns the reference
     * fee (shown in place of Tuition fee), the amount base (percentage is applied
     * to this), enrolled credit hours, whether arrears apply, and the one-time
     * fee total (arrears cap). Blocks with a reason/message when the student
     * isn't enrolled (Credit_hours) or has no challan (additional_Full_semester).
     */
    public function getAcademicFeeStructure(Request $request, $session = null, $student_id = null)
    {
        $studentType = $request->query('student_type');
        $challanCategory = $request->query('challan_category', 'Full_semester');
        $res = $this->resolveAcademicFeeStructure($session, $student_id, $studentType, $challanCategory);

        if (empty($res['ok'])) {
            return response()->json([
                'status'            => false,
                'reason'            => $res['reason'] ?? 'no_fee_structure',
                'switchTo'          => $res['switchTo'] ?? null,
                'message'           => $res['message'] ?? 'Academic fee structure is not defined for this student.',
                'feeBase'           => null,
                'amountBase'        => 0,
                'creditHours'       => null,
                'referenceLabel'    => 'Tuition Fee',
                'oneTimeFeeTotal'   => 0,
                'arrearsApplicable' => false,
            ]);
        }

        return response()->json([
            'status'            => true,
            'message'           => '1',
            'feeBase'           => $res['referenceFee'],
            'referenceLabel'    => $res['referenceLabel'],
            'amountBase'        => $res['amountBase'],
            'feeLineTotal'      => $res['feeLineTotal'] ?? $res['amountBase'],
            'creditHours'       => $res['creditHours'],
            'repeatCredits'     => $res['repeatCredits'] ?? 0,
            'oneTimeFeeTotal'   => $res['oneTimeFeeTotal'],
            'arrearsApplicable' => $res['arrearsApplicable'],
        ]);
    }

    /**
     * Resolve the challan, student metadata and installment date pair required
     * to execute the scholarship challan procedures.
     */
    private function resolveScholarshipChallanExecutionContext($aai_id, $sessions, $challanCategory, $studentType, $enrollmentNo = null)
    {
        $challanCategory = trim((string) $challanCategory);
        $studentType = trim((string) $studentType);
        $isExisting = strcasecmp($studentType, 'Existing') === 0;
        $normalizedEnrollment = strtoupper(trim((string) $enrollmentNo));

        $challanQuery = DB::table('student_challan')
            ->select(
                'challan_no',
                'SC_APP_ID',
                'SC_SESSION_ID',
                'SC_CHALLAN_TYPE',
                'SC_STUDENT_STATUS',
                'SC_ENROLLED_CR_HR',
                'SC_ENROLLMENT_NO'
            )
            ->where('SC_APP_ID', $aai_id)
            ->where('SC_SESSION_ID', $sessions);

        if ($challanCategory === 'additional_Full_semester') {
            $challanQuery->where('SC_CHALLAN_TYPE', 'Full_semester')
                ->where('SC_ENROLLED_CR_HR', '>', 0);
        } else {
            $challanQuery->where('SC_CHALLAN_TYPE', $challanCategory);
        }

        // Credit-hour categories regenerate an EXISTING challan, so they require a
        // PRESENT (UNPAID) one. A paid or cancelled challan does not count — e.g. a
        // scholarship-bearing challan that was later cancelled. When absent, the
        // procedure has nothing to run against, so the caller must block the save.
        $isCreditHourCategory = in_array($challanCategory, self::LOCKED_CHALLAN_CATEGORIES, true);
        if ($isCreditHourCategory) {
            $challanQuery->whereRaw("UPPER(TRIM(NVL(SC_CHALLAN_STATUS, 'X'))) = 'UNPAID'");
        }

        $challan = $challanQuery->first();
        if (empty($challan)) {
            if ($isCreditHourCategory) {
                $label = $challanCategory === 'additional_Full_semester' ? 'Additional Credit Hr' : 'Credit Hour';
                return [
                    'ok' => false,
                    'reason' => 'credit_challan_required',
                    'message' => 'No unpaid ' . $label . ' challan exists for this student in the selected session. Please (re)generate the credit-hour challan first, then Save & Post.',
                ];
            }
            return [
                'ok' => false,
                'reason' => 'no_challan',
                'message' => 'Scholarship detail saved successfully. No existing challan was found for the selected category/session, so challan regeneration procedure was not executed.',
            ];
        }

        $scholarshipStudentType = $challan->sc_student_status ?? null;
        if (empty($scholarshipStudentType)) {
            return [
                'ok' => false,
                'reason' => 'missing_student_type',
                'message' => 'Scholarship process failed because the existing challan student status could not be resolved.',
            ];
        }

        // The challan exists, so the procedure WILL run (PDF §6). Dates come from
        // FIN_CHALLAN_INSTALLMENTS; on any gap (no student record, no round, no
        // future installment) fall back to current date + 7 days (PDF §9).
        $installment = null;

        if ($isExisting) {
            $lookupEnrollment = !empty($normalizedEnrollment)
                ? $normalizedEnrollment
                : strtoupper((string) ($challan->sc_enrollment_no ?? ''));

            $studentInfo = DB::table('students_apply_info')
                ->select(
                    'SAI_SDPO_SCHOOL_ID as school_id',
                    'SAI_SDPO_DEPT_ID as dept_id',
                    'SAI_SDPO_PROG_ID as prog_id'
                )
                ->whereRaw('LOWER(SAI_ENROLLMENT_NO) = ?', [strtolower($lookupEnrollment)])
                ->first();

            if (!empty($studentInfo)) {
                $installment = $this->resolveFutureInstallmentPair(
                    $sessions,
                    $studentInfo->school_id,
                    $studentInfo->dept_id,
                    $studentInfo->prog_id,
                    'CMS',
                    $scholarshipStudentType
                );
            }
        } else {
            $studentInfo = DB::table('admission_apply_info')
                ->select(
                    'AAI_SDPO_SCHOOL_ID as school_id',
                    'AAI_SDPO_DEPT_ID as dept_id',
                    'AAI_SDPO_PROG_ID as prog_id',
                    'AAI_ADMISSION_SCH_ID as admission_sch_id'
                )
                ->where('AAI_ID', $aai_id)
                ->first();

            if (!empty($studentInfo)) {
                $admissionRound = DB::table('admission_schedules')
                    ->where('ADMISSION_SCH_ID', $studentInfo->admission_sch_id)
                    ->value('ADMISSION_SCH_ROUND');

                $admissionTypeMap = [
                    '1' => 'early',
                    '2' => 'regular',
                    '3' => 'rolling',
                ];
                $admissionType = $admissionTypeMap[(string) $admissionRound] ?? (string) $admissionRound;
                $admissionTypeCandidates = array_values(array_filter(array_unique([
                    (string) $admissionRound,
                    $admissionType,
                ])));

                $installment = $this->resolveFutureInstallmentPair(
                    $sessions,
                    $studentInfo->school_id,
                    $studentInfo->dept_id,
                    $studentInfo->prog_id,
                    'Admission',
                    $scholarshipStudentType,
                    $admissionTypeCandidates
                );
            }
        }

        if (!empty($installment['ok'])) {
            $dueDate = $installment['due_date'];
            $validDate = $installment['valid_date'];
        } else {
            $dueDate = Carbon::now('Asia/Karachi')->addDays(7)->toDateString();
            $validDate = Carbon::now('Asia/Karachi')->addDays(7)->toDateString();
        }

        // Fall back to the challan's enrollment when the caller passed none, so the
        // SUM test still works for the procedure choice.
        $procedureEnrollment = $normalizedEnrollment !== ''
            ? $normalizedEnrollment
            : (string) ($challan->sc_enrollment_no ?? '');

        return [
            'ok' => true,
            'procedure_name' => $this->resolveScholarshipProcedureName($challanCategory, $studentType, $procedureEnrollment),
            'challan_category' => $challanCategory,
            'challan_student_status' => $scholarshipStudentType,
            'challan' => $challan,
            'due_date' => $dueDate,
            'valid_date' => $validDate,
        ];
    }

    /**
     * Which stored procedure regenerates the challan for a scholarship.
     *
     * Short/Summer_Course splits by enrollment number, mirroring the fee row it
     * was priced against (see resolveAcademicFeeStructure): a SUM / new-admission
     * student is charged like a full semester, a Fall/Spring student per credit.
     * p_sch_type stays the challan category in both cases.
     */
    private function resolveScholarshipProcedureName($challanCategory, $studentType, $enrollment)
    {
        if ($challanCategory === 'Full_semester') {
            return 'FIN_SCH_FULL_SEMESTER';
        }
        if ($challanCategory === 'Short/Summer_Course') {
            return $this->isSummerAdmissionEnrollment($studentType, $enrollment)
                ? 'FIN_SCH_FULL_SEMESTER'
                : 'FIN_SCH_CREDIT_HOUR';
        }
        // additional_Full_semester / Credit_hours
        return 'FIN_SCH_CREDIT_HOUR';
    }

    /**
     * Find the nearest future installment pair from the challan installment
     * setup. The future pair is the first one whose valid date is today or
     * later, ordered by the smallest due date.
     */
    private function resolveFutureInstallmentPair($sessions, $schoolId, $deptId, $progId, $flag, $challanStudentType, $admissionTypeCandidates = null)
    {
        $installment = null;
        $lastError = null;

        try {
            $query = DB::table('fin_challan_installments')
                ->select(
                    'FCI_FIRST_DUEDATE as first_due_date',
                    'FCI_FIRST_VALIDDATE as first_valid_date',
                    'FCI_SECOND_DUEDATE as second_due_date',
                    'FCI_SECOND_VALIDDATE as second_valid_date',
                    'FCI_THIRD_DUEDATE as third_due_date',
                    'FCI_THIRD_VALIDDATE as third_valid_date',
                    'FCI_FOURTH_DUEDATE as fourth_due_date',
                    'FCI_FOURTH_VALIDDATE as fourth_valid_date',
                    'FCI_FIFTH_DUEDATE as fifth_due_date',
                    'FCI_FIFTH_VALIDDATE as fifth_valid_date'
                )
                ->where('FCI_SESSION_ID', $sessions)
                ->where('FCI_SCHOOL_ID', $schoolId)
                ->where('FCI_DEPT_ID', $deptId)
                ->where('FCI_PROG_ID', $progId)
                ->where('FCI_STATUS', 'E')
                ->where('FCI_FLAG', $flag);

            if ($flag === 'Admission') {
                $query->where('FCI_STUDENT_TYPE', $challanStudentType);
                if (!empty($admissionTypeCandidates)) {
                    $query->where(function ($q) use ($admissionTypeCandidates) {
                        foreach (array_values((array) $admissionTypeCandidates) as $index => $candidate) {
                            $candidateValue = strtolower(trim((string) $candidate));
                            if ($candidateValue === '') {
                                continue;
                            }

                            if ($index === 0) {
                                $q->whereRaw('LOWER(TRIM(FCI_ADMISSION_TYPE)) = ?', [$candidateValue]);
                            } else {
                                $q->orWhereRaw('LOWER(TRIM(FCI_ADMISSION_TYPE)) = ?', [$candidateValue]);
                            }
                        }
                    });
                }
            }

            $installment = $query->first();
            // dd($installment);
        } catch (\Throwable $e) {
            $lastError = $e;
        }

        if (empty($installment)) {
            return [
                'ok' => false,
                'reason' => 'no_installment',
                'message' => 'Scholarship could not be saved because no active future installment due date/valid date was found for the selected student, session, program, and challan category.' . ($lastError ? ' Error: ' . $lastError->getMessage() : ''),
            ];
        }

        $pairs = [
            [$installment->first_due_date ?? null, $installment->first_valid_date ?? null],
            [$installment->second_due_date ?? null, $installment->second_valid_date ?? null],
            [$installment->third_due_date ?? null, $installment->third_valid_date ?? null],
            [$installment->fourth_due_date ?? null, $installment->fourth_valid_date ?? null],
            [$installment->fifth_due_date ?? null, $installment->fifth_valid_date ?? null],
        ];

        $today = Carbon::now('Asia/Karachi')->startOfDay();
        $selected = null;

        foreach ($pairs as $pair) {
            [$dueValue, $validValue] = $pair;
            if (empty($dueValue) || empty($validValue)) {
                continue;
            }

            try {
                $dueDate = Carbon::parse($dueValue)->startOfDay();
                $validDate = Carbon::parse($validValue)->startOfDay();
            } catch (\Throwable $e) {
                continue;
            }

            if ($validDate->lt($today)) {
                continue;
            }

            if (empty($selected) || $dueDate->lt($selected['due_date'])) {
                $selected = [
                    'due_date' => $dueDate,
                    'valid_date' => $validDate,
                ];
            }
        }

        if (empty($selected)) {
            return [
                'ok' => false,
                'reason' => 'no_future_installment',
                'message' => 'Scholarship could not be saved because no active future installment due date/valid date was found for the selected student, session, program, and challan category.',
            ];
        }
        // dd([
        //     'ok' => true,
        //     'due_date' => $selected['due_date']->toDateString(),
        //     'valid_date' => $selected['valid_date']->toDateString(),
        // ]);
        return [
            'ok' => true,
            'due_date' => $selected['due_date']->toDateString(),
            'valid_date' => $selected['valid_date']->toDateString(),
        ];
    }

    /**
     * Save the full set of scholarships for a student AND run the challan
     * procedures in one go (single "Save & Post"). Validates the percentage
     * caps (academic / hostel, separate) and the arrears total
     * (≤ fee-structure one-time fee) before writing anything. All-or-nothing:
     * inserts + procedures share one transaction and roll back on any failure.
     */
    public function saveAndPostScholarships(Request $request)
    {
        $scholarships = $request->input('scholarships');
        $deletedRows  = $request->input('deleted_rows');       // rows the user removed from the table
        $sessions     = $request->input('academic_session');
        $aai_id       = $request->input('sch_aai_id');
        $enrollmentNo = $request->input('sch_enrollment_no'); // enrollment no OR aai_id (raw)
        $studentType  = $request->input('student_type');
        $rawId        = strtoupper((string) $enrollmentNo);
        $enrollment   = is_numeric($rawId) ? null : $rawId;

        $scholarships = is_array($scholarships) ? $scholarships : [];
        $deletedRows  = is_array($deletedRows) ? $deletedRows : [];

        // A post may be saves only, deletions only, or both — but not nothing.
        if (empty($scholarships) && empty($deletedRows)) {
            return response()->json(['success' => false, 'messege' => 'No scholarships to save.'], 400);
        }
        if (empty($sessions) || empty($aai_id)) {
            return response()->json(['success' => false, 'messege' => 'Session and student are required.'], 400);
        }

        // Split the batch by category; collect the ids being updated so the
        // existing-sum query can exclude them (replaced, not added). Arrears
        // are academic-only.
        $hostelBatchSum   = 0;
        $academicUpdateIds = [];
        $hostelUpdateIds   = [];
        $hasAcademic = false;
        $hasHostel   = false;
        $academicCategories   = [];   // distinct challan categories among academic rows
        $academicCategorySums = [];   // challan category => batch % sum (capped per category)
        $academicArrearSums   = [];   // challan category => batch arrears sum (capped per category)
        foreach ($scholarships as $s) {
            $pct = (float) ($s['sch_percentage'] ?? 0);
            if (strtolower($s['category'] ?? '') === 'hostel') {
                $hasHostel = true;
                $hostelBatchSum += $pct;
                if (!empty($s['id'])) { $hostelUpdateIds[] = $s['id']; }
            } else {
                $hasAcademic = true;
                $cat = $s['challan_category'] ?? 'Full_semester';
                if (!empty($s['id'])) { $academicUpdateIds[] = $s['id']; }
                $academicCategories[$cat] = true;
                $academicCategorySums[$cat] = ($academicCategorySums[$cat] ?? 0) + $pct;
                // Arrears are per challan category, so the cap is checked per category.
                $academicArrearSums[$cat] = ($academicArrearSums[$cat] ?? 0) + (float) ($s['sch_arrear'] ?? 0);
            }
        }

        // Deleted ids must ALSO be excluded from the "existing rows" percentage
        // sums below — they are going away in this same transaction, so they must
        // not count against the 100% cap.
        // Removing a scholarship changes what the student owes, so a deletion must
        // regenerate that challan too — mark its category for procedure execution.
        $academicDeleteIds = [];
        $hostelDeleteIds   = [];
        foreach ($deletedRows as $d) {
            $id = $d['id'] ?? null;
            if (empty($id)) { continue; }
            if (strtolower($d['category'] ?? '') === 'hostel') {
                $hostelDeleteIds[] = $id;
                $hasHostel = true;
            } else {
                $academicDeleteIds[] = $id;
                $hasAcademic = true;
                $academicCategories[$d['challan_category'] ?? 'Full_semester'] = true;
            }
        }
        $academicExcludeIds = array_merge($academicUpdateIds, $academicDeleteIds);
        $hostelExcludeIds   = array_merge($hostelUpdateIds, $hostelDeleteIds);

        DB::beginTransaction();
        try {
            // Academic % cap is PER CHALLAN CATEGORY: a Full Semester scholarship and
            // a Credit Hour scholarship discount two different challans, so each gets
            // its own 100%. Existing rows are counted only within the same category,
            // excluding this batch's updates and deletions.
            // Legacy rows have a NULL SSD_CHALLAN_CATEGORY -> treated as Full_semester.
            foreach ($academicCategorySums as $cat => $batchSum) {
                $existing = DB::table('Students_Scholarships_Details')
                    ->where('SSD_APP_ID', $aai_id)
                    ->where('SSD_SESS_ID', $sessions)
                    ->where('STATUS', 'E')
                    ->whereRaw("NVL(SSD_CHALLAN_CATEGORY, 'Full_semester') = ?", [$cat])
                    ->when(!empty($academicExcludeIds), function ($q) use ($academicExcludeIds) {
                        $q->whereNotIn('SSD_ID', $academicExcludeIds);
                    })
                    ->sum('SSD_PERCENTAGE');
                if (($existing + $batchSum) > 100) {
                    throw new \Exception('Academic Scholarship Percentage Limit Exceeds 100% for "'
                        . $cat . '" (existing ' . $existing . '% + this batch ' . $batchSum . '%).');
                }
            }

            // Hostel % cap, independent of academic.
            $hostelExisting = DB::table('fin_students_scholarships_hostel_details')
                ->where('SSHD_APP_ID', $aai_id)
                ->where('SSHD_SESS_ID', $sessions)
                ->where('SSHD_STATUS', 'E')
                ->when(!empty($hostelExcludeIds), function ($q) use ($hostelExcludeIds) {
                    $q->whereNotIn('SSHD_ID', $hostelExcludeIds);
                })
                ->sum('SSHD_PERCENTAGE');
            if (($hostelExisting + $hostelBatchSum) > 100) {
                throw new \Exception('Hostel Scholarship Percentage Limit Exceeds 100%');
            }

            // Hostel scholarships require a hostel assigned this session. Same
            // matching rules as getHostelFeeStructure(), so the UI and this guard
            // can never disagree: an assignment counts when it has a bed OR a
            // hostel challan, and the enrollment compare ignores case/spaces.
            if ($hasHostel) {
                $hasHostelAssign = DB::table('hostel_assign')
                    ->where('ha_session', $sessions)
                    ->where(function ($q) {
                        $q->whereNotNull('ha_hb_id')
                          ->orWhereNotNull('ha_challan_id');
                    })
                    ->where(function ($q) use ($aai_id, $enrollment) {
                        $q->where('ha_aai_id', $aai_id);
                        if (!empty($enrollment)) {
                            $q->orWhereRaw('UPPER(TRIM(ha_enrolment_no)) = ?', [strtoupper(trim($enrollment))]);
                        }
                    })
                    ->exists();
                if (!$hasHostelAssign) {
                    throw new \Exception('No hostel is assigned to this student for the selected session — hostel scholarship not allowed.');
                }
            }

            // Arrears cap, PER CHALLAN CATEGORY: each category's arrears are capped by
            // the one-time fees of ITS OWN fee-structure row. Previously this always
            // resolved 'Full_semester', which both mis-capped the other categories and
            // silently skipped the check entirely on a session with no Full_semester
            // row (e.g. summer-only).
            foreach ($academicArrearSums as $cat => $catArrears) {
                if ($catArrears <= 0) {
                    continue;
                }
                if (!$this->challanCategoryHasArrears($cat)) {
                    throw new \Exception('Arrears are not applicable to the "' . $cat . '" challan category.');
                }
                $feeRes = $this->resolveAcademicFeeStructure($sessions, $enrollment ?: $aai_id, $studentType, $cat);
                if (empty($feeRes['ok'])) {
                    continue;   // fee structure unresolved — the add path already blocks this
                }
                $oneTime = (float) $feeRes['oneTimeFeeTotal'];
                if ($catArrears > $oneTime) {
                    throw new \Exception('Arrears Total (' . $catArrears . ') for "' . $cat
                        . '" exceeds that fee structure\'s one-time fee (' . $oneTime . ').');
                }
            }

            // Apply the staged deletions FIRST, so a row can be deleted and another
            // added in the same post without tripping the duplicate/percentage
            // checks. Deleting is part of the same all-or-nothing transaction.
            $deletedCount = 0;
            foreach ($deletedRows as $d) {
                $id = $d['id'] ?? null;
                if (empty($id)) { continue; }

                if (strtolower($d['category'] ?? '') === 'hostel') {
                    $hRow = DB::table('fin_students_scholarships_hostel_details')
                        ->where('SSHD_ID', $id)
                        ->first();
                    if (empty($hRow)) {
                        continue;   // already gone — nothing to do
                    }
                    // Hostel had no lock check while this was a delete; it needs the same
                    // protection academic rows get.
                    if ($this->isScholarshipRowLockedByChallan(
                            'Hostel_Fee',
                            $hRow->sshd_app_id ?? null,
                            $hRow->sshd_sai_enrollment_no ?? null,
                            $hRow->sshd_sess_id ?? null)) {
                        throw new \Exception('This hostel scholarship\'s challan already carries a '
                            . 'scholarship amount, so it can no longer be edited or removed.');
                    }
                    // Zeroed, never deleted — see zeroScholarshipRow().
                    $deletedCount += $this->zeroScholarshipRow($id, 'hostel');
                    continue;
                }

                $row = DB::table('Students_Scholarships_Details')->where('SSD_ID', $id)->first();
                if (empty($row)) {
                    continue;   // already gone — nothing to do
                }
                // Locked only when the challan already carries a scholarship amount.
                if ($this->isScholarshipRowLockedByChallan(
                        $row->ssd_challan_category ?? null,
                        $row->ssd_app_id ?? null,
                        $row->ssd_sai_enrollment_no ?? null,
                        $row->ssd_sess_id ?? null)) {
                    throw new \Exception('This scholarship was applied against "' . $row->ssd_challan_category
                        . '" and its challan already carries a scholarship amount, so it can no longer be edited or removed.');
                }
                $deletedCount += $this->zeroScholarshipRow($id, 'academic');
            }

            // Persist every row. Procedure execution is conditional per academic
            // category, so a missing challan can still allow the scholarship
            // detail save to commit.
            foreach ($scholarships as $s) {
                $this->persistScholarshipRow($s, $aai_id, $enrollment, $sessions);
            }

            $academicProcedureMessages = [];
            $academicProcedureExecuted = false;
            if ($hasAcademic) {
                // One procedure call PER distinct challan category (collected in the
                // parsing loop above). The awarded scholarships are already persisted
                // in students_scholarships_details; the procedure reads them itself.
                // p_sch_type carries the selected challan category (Full_semester /
                // additional_Full_semester / Credit_hours / Short/Summer_Course).
                foreach (array_keys($academicCategories) as $challanCategory) {
                    $context = $this->resolveScholarshipChallanExecutionContext(
                        $aai_id,
                        $sessions,
                        $challanCategory,
                        $studentType,
                        $enrollmentNo
                    );

                    if (empty($context['ok'])) {
                        $reason = $context['reason'] ?? null;

                        if ($reason === 'no_challan') {
                            $academicProcedureMessages[] = $context['message'];
                            continue;
                        }

                        // A credit-hour category with no present (unpaid) challan is
                        // fatal when a scholarship is being APPLIED in it (rows in the
                        // batch), but harmless when this category is only here because
                        // a row was deleted — that cleanup has nothing to regenerate.
                        if ($reason === 'credit_challan_required') {
                            if (isset($academicCategorySums[$challanCategory])) {
                                throw new \Exception($context['message']);
                            }
                            $academicProcedureMessages[] = $context['message'];
                            continue;
                        }

                        throw new \Exception($context['message'] ?? 'Scholarship process failed during challan regeneration.');
                    }
                    // dd($context);
                    try {
                        DB::executeProcedure($context['procedure_name'], [
                            'p_app_id' => $aai_id,
                            'p_session' => $sessions,
                            'p_arrear' => 0,
                            'p_ffs_student_type' => $context['challan_student_status'],
                            'p_sch_type' => $challanCategory,
                            'p_due_date' => $context['due_date'],
                            'p_valid_date' => $context['valid_date'],
                            'p_entry_user' => auth()->user()->username,
                        ]);
                        $academicProcedureExecuted = true;
                    } catch (\Throwable $procEx) {
                        throw new \Exception('Scholarship process failed during challan regeneration. Error: ' . $procEx->getMessage(), 0, $procEx);
                    }
                }
            }
            if ($hasHostel) {
                $this->runHostelChallanProcedures($aai_id, $enrollmentNo, $sessions);
            }

            DB::commit();
            $savedCount = count($scholarships);
            $parts = [];
            if ($savedCount > 0)   { $parts[] = $savedCount . ' scholarship(s) saved'; }
            if ($deletedCount > 0) { $parts[] = $deletedCount . ' deleted'; }
            $message = ucfirst(implode(', ', $parts) ?: 'Nothing to save');
            $message .= ($academicProcedureExecuted || $hasHostel)
                ? '. Challans processed successfully.'
                : '.';
            if (!empty($academicProcedureMessages)) {
                $message .= ' ' . implode(' ', array_unique($academicProcedureMessages));
            }

            return response()->json(['success' => true, 'message' => $message], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'messege' => $e->getMessage()], 500);
        }
    }

    /**
     * Insert / update a single scholarship row (no procedures). Updates the
     * matching row when the tile carries an id or an enabled row already exists
     * for the same app + type + session; otherwise inserts a fresh one.
     */
    /**
     * Challan categories whose challans are regenerated per credit-hour by the
     * procedure. These are the only categories that CAN lock — but the lock is now
     * conditional (see isScholarshipRowLockedByChallan).
     */
    private const LOCKED_CHALLAN_CATEGORIES = ['additional_Full_semester', 'Credit_hours'];

    private function isLockedChallanCategory($challanCategory)
    {
        return in_array(trim((string) $challanCategory), self::LOCKED_CHALLAN_CATEGORIES, true);
    }

    /**
     * Whether a saved credit-hour scholarship row is immutable (no edit / delete).
     *
     * The lock is CONDITIONAL: it applies only when the student's regenerated
     * challan actually carries a scholarship amount for this session. If no such
     * challan exists, or its SC_SCHOLARSHIP_AMOUNT is NULL / 0, the scholarship has
     * not yet been baked into a challan and stays editable.
     *
     *   Credit_hours             -> the Credit_hours challan
     *   additional_Full_semester -> the Full_semester challan with SC_ENROLLED_CR_HR > 0
     *
     * Student matched by app id OR enrollment no (same session), mirroring the rest
     * of the page. Any other category never locks.
     */
    private function isScholarshipRowLockedByChallan($challanCategory, $appId, $enrollment, $sessionId)
    {
        $cat = trim((string) $challanCategory);
        if (!in_array($cat, self::LOCKED_CHALLAN_CATEGORIES, true)) {
            return false;
        }

        $q = DB::table('student_challan')
            ->where('SC_SESSION_ID', $sessionId)
            ->whereRaw('NVL(SC_SCHOLARSHIP_AMOUNT, 0) > 0')
            // A CANCELLED challan releases the lock: the scholarship is no longer
            // baked into a live challan, so the row becomes editable / deletable again.
            ->whereRaw("UPPER(TRIM(NVL(SC_CHALLAN_STATUS, 'X'))) NOT IN ('CANCELLED', 'CANCEL FOR REISSUANCE')")
            ->where(function ($w) use ($appId, $enrollment) {
                if (!empty($appId)) {
                    $w->where('SC_APP_ID', $appId);
                }
                if (!empty($enrollment)) {
                    $w->orWhereRaw('UPPER(TRIM(SC_ENROLLMENT_NO)) = ?', [strtoupper(trim((string) $enrollment))]);
                }
            });

        if ($cat === 'additional_Full_semester') {
            $q->where('SC_CHALLAN_TYPE', 'Full_semester')->where('SC_ENROLLED_CR_HR', '>', 0);
        } else {   // Credit_hours
            $q->where('SC_CHALLAN_TYPE', 'Credit_hours');
        }

        return $q->exists();
    }

    private function persistScholarshipRow($tile, $aai_id, $enrollment, $sessions)
    {
        $type            = $tile['sch_type'] ?? null;
        $percentage      = $tile['sch_percentage'] ?? 0;
        $arrear          = $tile['sch_arrear'] ?? 0;
        $details         = $tile['sch_details'] ?? null;
        $rowId           = $tile['id'] ?? null;
        $challanCategory = $tile['challan_category'] ?? 'Full_semester';   // academic only
        $username        = auth()->user()->username;

        if (strtolower($tile['category'] ?? '') === 'hostel') {
            $amount    = $tile['sch_amount'] ?? 0;
            $hostelfee = $tile['hostelfee'] ?? 0;

            $existing = !empty($rowId)
                ? DB::table('fin_students_scholarships_hostel_details')->where('sshd_id', $rowId)->first()
                : DB::table('fin_students_scholarships_hostel_details')
                    ->where('SSHD_APP_ID', $aai_id)
                    ->where('SSHD_SESS_ID', $sessions)
                    ->where('sshd_type', $type)
                    ->first();

            if (!empty($existing)) {
                DB::table('fin_students_scholarships_hostel_details')
                    ->where('sshd_id', $existing->sshd_id)
                    ->update([
                        'sshd_type'            => $type,
                        'sshd_percentage'      => $percentage,
                        'SSHD_AMOUNT'          => $amount,
                        'SSHD_HOSTEL_FEE'      => $hostelfee,
                        'SSHD_ARREAR'          => $arrear,
                        'sshd_details'         => $details,
                        'SSHD_STATUS'          => 'E',
                        'SSHD_entry_username'  => $username,
                        'SSHD_UPDATE_DATETIME' => Carbon::now('Asia/Karachi'),
                    ]);
            } else {
                DB::table('fin_students_scholarships_hostel_details')->insert([
                    'sshd_sai_enrollment_no' => $enrollment ? strtoupper($enrollment) : null,
                    'sshd_type'              => $type,
                    'sshd_percentage'        => $percentage,
                    'SSHD_AMOUNT'            => $amount,
                    'SSHD_HOSTEL_FEE'        => $hostelfee,
                    'SSHD_ARREAR'            => $arrear,
                    'SSHD_APP_ID'            => $aai_id,
                    'SSHD_STATUS'            => 'E',
                    'SSHD_DETAILS'           => $details,
                    'SSHD_SESS_ID'           => $sessions,
                    'SSHD_entry_username'    => $username,
                    'SSHD_ENTRY_DATETIME'    => Carbon::now('Asia/Karachi'),
                ]);
            }
            return;
        }

        // Academic. Uniqueness is per (student, session, scholarship type, CHALLAN
        // CATEGORY) — the same scholarship may be awarded under two different challan
        // categories (e.g. Merit Base on Full Semester and again on Credit Hour),
        // because those discount two different challans and are capped independently.
        // Without the category in this lookup the second award would silently UPDATE
        // the first instead of inserting a new row.
        // Legacy rows have a NULL SSD_CHALLAN_CATEGORY -> treated as Full_semester.
        $existing = !empty($rowId)
            ? DB::table('Students_Scholarships_Details')->where('SSD_ID', $rowId)->first()
            : DB::table('Students_Scholarships_Details')
                ->where('SSD_APP_ID', $aai_id)
                ->where('ssd_type', $type)
                ->where('ssd_sess_id', $sessions)
                ->where('STATUS', 'E')
                ->whereRaw("NVL(SSD_CHALLAN_CATEGORY, 'Full_semester') = ?", [$challanCategory])
                ->first();

        if (!empty($existing)) {
            // A re-post must not overwrite a row whose challan already carries a
            // scholarship amount (Additional Credit Hr / Credit Hour). If it does
            // not yet, the row is still editable and the update proceeds.
            if ($this->isScholarshipRowLockedByChallan(
                    $existing->ssd_challan_category ?? null,
                    $existing->ssd_app_id ?? $aai_id,
                    $existing->ssd_sai_enrollment_no ?? $enrollment,
                    $existing->ssd_sess_id ?? $sessions)) {
                return;
            }

            DB::table('Students_Scholarships_Details')
                ->where('SSD_ID', $existing->ssd_id)
                ->update([
                    'ssd_sai_enrollment_no' => $enrollment,
                    'ssd_app_id'            => $aai_id,
                    'ssd_type'              => $type,
                    'ssd_percentage'        => $percentage,
                    'SSD_ARREAR'            => $arrear,
                    'ssd_details'           => $details,
                    'ssd_sess_id'           => $sessions,
                    'ssd_project'           => 'SCH_PRO_NEW',
                    'SSD_CHALLAN_CATEGORY'  => $challanCategory,
                    'scd_entry_username'    => $username,
                    'UPDATE_DATETIME'       => now()->toDateTimeString(),
                ]);
        } else {
            DB::table('Students_Scholarships_Details')->insert([
                'ssd_sai_enrollment_no' => $enrollment,
                'ssd_app_id'            => $aai_id,
                'ssd_type'              => $type,
                'ssd_percentage'        => $percentage,
                'SSD_ARREAR'            => $arrear,
                'ssd_details'           => $details,
                'ssd_sess_id'           => $sessions,
                'ssd_project'           => 'SCH_PRO_NEW',
                'SSD_CHALLAN_CATEGORY'  => $challanCategory,
                'scd_entry_username'    => $username,
            ]);
        }
    }

    /**
     * Does a column exist? Memoised per request.
     *
     * This controller predates the optional snapshot columns and has no schema helper of
     * its own, but zeroing has to clear SSD_AMOUNT where it exists or a removed row keeps
     * a stale money figure next to a 0%.
     */
    private function scholarshipColumnExists($table, $column)
    {
        static $cache = [];

        $key = strtolower($table . '.' . $column);
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        try {
            $cache[$key] = DB::table('user_tab_columns')
                ->whereRaw('UPPER(table_name) = ?', [strtoupper($table)])
                ->whereRaw('UPPER(column_name) = ?', [strtoupper($column)])
                ->exists();
        } catch (\Throwable $e) {
            $cache[$key] = false;
        }

        return $cache[$key];
    }

    /**
     * Remove a scholarship by ZEROING it, never by deleting the row.
     *
     * A deleted row takes its history with it and leaves any challan it already
     * discounted standing with the discount still applied and nothing to explain why.
     * Removing and editing down to 0% are therefore the same operation.
     *
     * Deliberately a local copy rather than the ResolvesScholarshipFees version: this
     * controller does not use that trait — it keeps its own copies of those methods (see
     * the trait's header note) — and grafting 1,800 lines of trait onto a legacy 3,700
     * line class to reach one method would risk collisions for no gain.
     *
     * SOURCE_PAGE is deliberately NOT stamped here. This page has never stamped it on any
     * write, and stamping it only on removal would change which page owns the student as
     * a side effect of deleting something.
     *
     * @return int rows affected
     */
    private function zeroScholarshipRow($recordId, $category)
    {
        $recordId = (int) $recordId;
        if ($recordId <= 0) {
            return 0;
        }

        $username = auth()->user()->username ?? 'system';

        if (strtolower(trim((string) $category)) === 'hostel') {
            $payload = [
                'SSHD_PERCENTAGE'      => 0,
                'SSHD_AMOUNT'          => 0,
                'SSHD_ARREAR'          => 0,
                'SSHD_entry_username'  => $username,
                'SSHD_UPDATE_DATETIME' => Carbon::now('Asia/Karachi'),
            ];
            if ($this->scholarshipColumnExists('fin_students_scholarships_hostel_details', 'SSHD_BULK_POST_STATUS')) {
                $payload['SSHD_BULK_POST_STATUS'] = 'PENDING';
            }

            return DB::table('fin_students_scholarships_hostel_details')
                ->where('SSHD_ID', $recordId)
                ->update($payload);
        }

        $payload = [
            'ssd_percentage'     => 0,
            'SSD_ARREAR'         => 0,
            'scd_entry_username' => $username,
            'UPDATE_DATETIME'    => now()->toDateTimeString(),
        ];
        if ($this->scholarshipColumnExists('Students_Scholarships_Details', 'SSD_AMOUNT')) {
            $payload['SSD_AMOUNT'] = 0;
        }
        if ($this->scholarshipColumnExists('Students_Scholarships_Details', 'SSD_BULK_POST_STATUS')) {
            $payload['SSD_BULK_POST_STATUS'] = 'PENDING';
        }

        return DB::table('Students_Scholarships_Details')
            ->where('SSD_ID', $recordId)
            ->update($payload);
    }

    /**
     * Remove a single scholarship row by id (academic or hostel).
     */
    public function deleteScholarshipRow(Request $request)
    {
        $category = strtolower($request->input('category'));
        $id       = $request->input('id');

        if (empty($id)) {
            return response()->json(['success' => false, 'messege' => 'Invalid record.'], 400);
        }

        if ($category === 'hostel') {
            $hRow = DB::table('fin_students_scholarships_hostel_details')->where('SSHD_ID', $id)->first();
            if (empty($hRow)) {
                return response()->json(['success' => false, 'messege' => 'Record not found.'], 404);
            }
            // Hostel had no lock check while this was a delete — same protection as academic.
            if ($this->isScholarshipRowLockedByChallan(
                    'Hostel_Fee',
                    $hRow->sshd_app_id ?? null,
                    $hRow->sshd_sai_enrollment_no ?? null,
                    $hRow->sshd_sess_id ?? null)) {
                return response()->json([
                    'success' => false,
                    'messege' => 'This hostel scholarship\'s challan already carries a scholarship amount, so it can no longer be edited or removed.',
                ], 403);
            }
            // Zeroed, never deleted — see zeroScholarshipRow().
            $deleted = $this->zeroScholarshipRow($id, 'hostel');
        } else {
            $row = DB::table('Students_Scholarships_Details')->where('SSD_ID', $id)->first();
            if (empty($row)) {
                return response()->json(['success' => false, 'messege' => 'Record not found.'], 404);
            }
            // Locked only when this student's regenerated challan already carries a
            // scholarship amount for the session (see isScholarshipRowLockedByChallan).
            if ($this->isScholarshipRowLockedByChallan(
                    $row->ssd_challan_category ?? null,
                    $row->ssd_app_id ?? null,
                    $row->ssd_sai_enrollment_no ?? null,
                    $row->ssd_sess_id ?? null)) {
                return response()->json([
                    'success' => false,
                    'messege' => 'This scholarship was applied against "' . $row->ssd_challan_category . '" and its challan already carries a scholarship amount, so it can no longer be edited or removed.',
                ], 403);
            }
            $deleted = $this->zeroScholarshipRow($id, 'academic');
        }

        if (!$deleted) {
            return response()->json(['success' => false, 'messege' => 'Record not found.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Scholarship set to 0% — the row is kept on record']);
    }

}
