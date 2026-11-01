<?php

namespace App\Http\Controllers\Grader\DeanHonor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use DB;
use Validator;
use DataTables;

class DeanHonorController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->username == 'hina.razzaq' || auth()->user()->username == 'bilal.saleem') {
            return view('grader.DeanHonor.index');
        }else{
            abort(403, 'Unauthorized User.');
        }

    }

    public function updateTestSlip(Request $request)
    {
        $sqlAcademicSessions = "select * from academic_sessions order by sess_id desc";
        $recsqlAcademicSessions = DB::select($sqlAcademicSessions);

        $Scholarship_Type = "select * from Scholarship_Type";
        $recScholarship_Type = DB::select($Scholarship_Type);
        $challanType = "select distinct sc_challan_type  from student_challan
    where sc_challan_type in ('Processing_Fee','Full_semester','Fine','Credit_hours','Hostel_Fee','Short/Summer_Course')";
        $recchallanType = DB::select($challanType);
        $fineCateg = " select * from fine_category where fc_status = 'E'";
        $resfineCateg = DB::select($fineCateg);
        return view('grader.DeanHonor.updatetestslip', ['academic_session' => $recsqlAcademicSessions, 'Scholarship_Type' => $recScholarship_Type, 'challan_type' => $recchallanType, 'resfineCateg' => $resfineCateg]);


    }


    public function filterTestSlipRecords(Request $request)
    {
        // dd($request->test_city);

        $where = " WHERE 1=1 ";

        if (!empty($request->session)) {
            $where .= " AND aai.AAI_SDPO_SESS_ID = " . $request->session;
        }

        if (!empty($request->school)) {
            $where .= " AND aai.AAI_SDPO_SCHOOL_ID = " . $request->school;
        }

        if (!empty($request->department)) {
            $where .= " AND aai.AAI_SDPO_DEPT_ID = " . $request->department;
        }

        if (!empty($request->program)) {
            $where .= " AND aai.AAI_SDPO_PROG_ID = " . $request->program;
        }

        if (!empty($request->adm_sch)) {
            $where .= " AND aai.aai_admission_sch_id = " . $request->adm_sch;
        }

        if (!empty($request->selected_date)) {
            $where .= " AND TRUNC(aai.TEST_SLIP_DATE) = TO_DATE('" . $request->selected_date . "','DD/MM/YYYY')";
        }

        if (!empty($request->selected_int_date)) {
            $where .= " AND TRUNC(aai.aai_new_interview_date) = TO_DATE('" . $request->selected_int_date . "','DD/MM/YYYY')";
        }

        if (!empty($request->test_city)) {
            $where .= " AND UPPER(TRIM(aai.AAI_TEST_CITY)) = UPPER(TRIM('" . $request->test_city . "'))";
        }

        if (!empty($request->selected_int_time)) {
            $where .= " AND aai.aai_new_admission_time = '" . $request->selected_int_time . "'";
        }

        $sql = "
        SELECT
            aai.aai_id,
            api.api_name,
            p.prog_name,
            aai.TEST_SLIP_DATE AS test_slip_date,
            ads.admission_sch_test_date,
            aai.aai_new_interview_date,
            ads.admission_sch_interview_date,
            aai.aai_new_admission_time,
            ads.admission_sch_test_time,
            aai.aai_test_city,
            aai.aai_new_test_city,
            0 AS tickmark
        FROM admission_apply_info aai
        INNER JOIN admission_personal_info api
            ON api.api_id = aai.aai_api_id
        INNER JOIN programs p
    ON p.prog_id = aai.AAI_SDPO_PROG_ID
        LEFT JOIN admission_schedules ads
            ON ads.admission_sch_id = aai.aai_admission_sch_id
        $where
    ";

        // Check generated SQL
        // dd($sql);

        $ressqlMeritList = DB::select($sql);

        // dd($ressqlMeritList);

        return response()->json([
            'ressqlMeritList' => $ressqlMeritList
        ]);
    }


    function getapplicantInformation(Request $request)
    {
        $session = $request->input('session');
        $school = $request->input('school');
        $dept = $request->input('department');
        $prog = $request->input('program');
        $adm_sch = $request->input('adm_sch');

        $sqlMeritList = "SELECT
    aai_test_marks,
    aai_interview_marks,
    api_name,
    api_fathername,
    api_email,
    api_imagename,
    aai_id,
    aai_api_form_id,
    school_code,
    aai_programid,
    prog_name,
    aai_degreecode,
    aai_aggregate_required,
    aai_test_aggregate,
    aai_interview_aggregate,
    (aai_aggregate_required + aai_test_aggregate + aai_interview_aggregate) AS total_aggregate,aai_test_city,aai_new_test_city,
    aai_status,test_slip_date,admission_sch_test_date,admission_sch_interview_date,aai_new_interview_date,    admission_sch_test_time,
    aai_new_admission_time,
   
    RANK() OVER (
        PARTITION BY aai_sdpo_school_id, aai_sdpo_dept_id, aai_sdpo_prog_id
        ORDER BY aai_id
    ) AS user_rank
    FROM admission_apply_info   
    LEFT JOIN admission_personal_info ON api_id = aai_api_id
    LEFT JOIN schools ON school_id = aai_sdpo_school_id
    LEFT JOIN programs ON prog_id = aai_sdpo_prog_id
    left join admission_schedules on aai_admission_sch_id = admission_sch_id
    WHERE aai_api_id=api_id
    and aai_pf_status='Fee Verified'";
        //   --and aai_qualification_status = 'Accepted'
        if (isset($session)) {
            $sqlMeritList .= " AND aai_sdpo_sess_id = $session";
        }
        if (isset($school)) {
            $sqlMeritList .= " and aai_sdpo_school_id = $school";
        }
        if (isset($dept)) {
            $sqlMeritList .= " AND aai_sdpo_dept_id = $dept";
        }
        if (isset($prog)) {
            $sqlMeritList .= " and aai_sdpo_prog_id = $prog";
        }
        if (isset($adm_sch)) {
            $sqlMeritList .= " and admission_sch_round = $adm_sch";
        }

        $sqlMeritList .= " ORDER BY aai_id";
        // echo $sqlMeritList;exit;
        $ressqlMeritList = DB::select($sqlMeritList);


        // return DataTables::of($ressqlMeritList)->toJson();

        return response()->json([
            'ressqlMeritList' => $ressqlMeritList

        ]);
    }
    public function submitDeanHonorList2(Request $request)
    {
        $updateData = [
            'aai_test_city' => $request->testCity,
            'aai_new_test_city' => $request->testCity,
        ];

        if (!empty($request->selectedDateObj)) {
            $updateData['test_slip_date'] = \Carbon\Carbon::createFromFormat('m/d/Y', $request->selectedDateObj)->format('Y-m-d');
        }

        if (!empty($request->selectedIntDateObj)) {
            $updateData['aai_new_interview_date'] = \Carbon\Carbon::createFromFormat('m/d/Y', $request->selectedIntDateObj)->format('Y-m-d');
        }

        if (!empty($request->selectedIntTime)) {
            $updateData['aai_new_admission_time'] = $request->selectedIntTime;
        }

        foreach ($request->ids as $aaiId) {
            DB::table('admission_apply_info')
                ->where('aai_id', $aaiId)
                ->update($updateData);
        }

        return response()->json(['success' => true, 'messege' => 'Submitted Successfully']);
    }
    public function submitDeanHonorList2Backup(Request $request)
    {
        // Convert the date to proper format if necessary (e.g., Y-m-d)
        $selectedDate = \Carbon\Carbon::createFromFormat('m/d/Y', $request->selectedDateObj)->format('Y-m-d');
        $selectedIntDate = \Carbon\Carbon::createFromFormat('m/d/Y', $request->selectedIntDateObj)->format('Y-m-d');
        $selectedAdmTime = $request->get('selectedIntTime');
        //  dd($request->all());
        foreach ($request->ids as $aaiId) {
            \DB::table('admission_apply_info')
                ->where('aai_id', $aaiId)
                // ->update([
                //     'test_slip_date' => $selectedDate,
                //     'aai_new_interview_date' => $selectedIntDate,
                //     'aai_new_admission_time' => $selectedAdmTime,
                //     'aai_new_test_city' => $request->testCity
                // ]);
                ->update([
                    'test_slip_date' => $selectedDate,
                    'aai_new_interview_date' => $selectedIntDate,
                    'aai_new_admission_time' => $selectedAdmTime,

                    // Update both columns
                    'aai_test_city' => $request->testCity,
                    'aai_new_test_city' => $request->testCity,
                ]);
        }

        return response()->json(['success' => true, 'messege' => 'Submitted Successfully']);
    }

    // public function getStudentInformation (Request $request){
//    // dd($request->all());
//     $top7PercentRecords =[];
//     if(!empty($request->school)){
//         $username = auth()->user()->username;
//         $session = $request->input('session');
//         $school = $request->input('school');
//         $dept = $request->input('department');
//         $prog = $request->input('program');
//         // dd($prog);
//         $sql = "select * from (SELECT
//             students_apply_info.sai_sdpo_prog_id AS aai_sdpo_prog_id,
//             students_apply_info.sai_sdpo_sess_id AS aai_sdpo_sess_id,
//             students_apply_info.sai_api_id AS api_id,
//             prog_name,
//             SEMESTER_SGPA(students_apply_info.sai_enrollment_no, 165) as sgpa,           
//             students_personal_info.spi_mobile AS api_mobile,
//             students_personal_info.spi_name AS api_name,
//             students_personal_info.spi_formid AS api_formid,
//             students_personal_info.spi_nic AS api_nic,
//             students_personal_info.spi_guardian_cnic AS api_guardian_cnic,
//             students_personal_info.spi_fathername AS api_fathername,
//             students_personal_info.spi_imagename AS api_imagename,
//             students_apply_info.sai_id AS aai_id,
//             students_apply_info.sai_api_form_id AS aai_api_form_id,
//             programs.prog_title AS aai_programid,
//             programs.prog_title AS aai_degreecode,
//             students_apply_info.sai_interview_marks AS aai_interview_marks,
//             students_apply_info.sai_enrollment_no AS CMS_sai_enrollment_no,
//             students_apply_info.sai_interview_totalmarks AS aai_interview_totalmarks,
//             students_personal_info.spi_email AS uemail,
//             students_apply_info.sai_pf_remarks AS aai_pf_remarks,
//             students_apply_info.sai_qualification_status AS aai_qualification_status,
//             students_apply_info.sai_qualification_remarks AS aai_qualification_remarks,
//             students_apply_info.sai_student_status AS sai_student_status,
//             students_apply_info.sai_prog_total_credits AS sai_prog_total_credits,
//             (select sum(sc_pc_course_cr) from student_courses where sc_sai_enrollment_no = sai_enrollment_no and sc_co_sess_id = $session) as workload_curr,
//             (select sum(gh_co_course_cr) from grade_history where gh_sai_enrollment_no = sai_enrollment_no AND (gh_exemption_status != 'Yes'
//             OR gh_exemption_status IS NULL) ) as total_earn_crhr,            
//             CASE
//                 WHEN EXISTS (
//                     SELECT 1 FROM SESSIONSWISE_DEANHONOR_LIST
//                     WHERE SESSIONSWISE_DEANHONOR_LIST.SDL_ENROLLMENT_NO = students_apply_info.sai_enrollment_no
//                         AND SESSIONSWISE_DEANHONOR_LIST.SDL_SESSION_ID = $session
//                         AND SESSIONSWISE_DEANHONOR_LIST.SDL_SCHOOL_ID = $school";
//                         // if (!empty($prog) && !empty($school)) {
//                         //     $sql .= "AND SESSIONSWISE_DEANHONOR_LIST.SDL_SCHOOL_ID = $prog";
//                         // } 
//                         if (!empty($session)) {
//                             $sql .= " AND SESSIONSWISE_DEANHONOR_LIST.SDL_SESSION_ID = $session";
//                         }

    //                         if (!empty($school)) {
//                             $sql .= " AND SESSIONSWISE_DEANHONOR_LIST.SDL_SCHOOL_ID = $school";
//                         }

    //                         if (!empty($dept)) {
//                             $sql .= " AND SESSIONSWISE_DEANHONOR_LIST.SDL_DEPARTMENT_ID = $dept";
//                         }     
//                         if (!empty($prog)) {
//                             $sql .= " AND SESSIONSWISE_DEANHONOR_LIST.SDL_PROGRAM_ID = $prog";
//                         }                    
//                         $sql .= "AND SESSIONSWISE_DEANHONOR_LIST.SDL_TYPE = 'new'
//                         AND SESSIONSWISE_DEANHONOR_LIST.STATUS = 'E'
//                 ) THEN '1'
//                 ELSE ''
//             END AS tickmark
//         FROM
//             students_apply_info
//         LEFT JOIN students_personal_info ON students_apply_info.sai_spi_id = students_personal_info.spi_id
//         LEFT JOIN programs ON students_apply_info.sai_sdpo_prog_id = programs.prog_id
//         LEFT JOIN schools ON students_apply_info.sai_sdpo_school_id = schools.school_id
//         LEFT JOIN departments ON students_apply_info.sai_sdpo_dept_id = departments.dept_id
//         LEFT JOIN admission_schedules ON admission_schedules.admission_sch_id = students_apply_info.sai_admission_sch_id
//         WHERE
//             students_apply_info.sai_sdpo_sess_id IS NOT NULL 
//         AND students_apply_info.sai_student_status='Active'
//         and sai_enrollment_no not in (select gh_sai_enrollment_no from grade_history where gh_grade in ('W','I'))";

    //         if (!empty($session)) {
//             $sql .= " AND students_apply_info.sai_sdpo_sess_id in (162,163,165)";
//         }

    //         if (!empty($school)) {
//             $sql .= " AND students_apply_info.sai_sdpo_school_id = $school";
//         }

    //         if (!empty($dept)) {
//             $sql .= " AND students_apply_info.sai_sdpo_dept_id = $dept";
//         }

    //         // Apply different ORDER BY conditions based on $prog
//         if (empty($prog) && !empty($school)) {
//             $sql .= " ORDER BY aai_sdpo_prog_id, TO_NUMBER(NVL(sgpa, '-1')) DESC     ) where sgpa>=3.0";
//         } else {
//             $sql .= " AND students_apply_info.sai_sdpo_prog_id = $prog";
//             $sql .= " ORDER BY TO_NUMBER(NVL(sgpa, '-1')) DESC     ) where sgpa>=3.0";
//         }

    //         // Restrict data access for specific users
//         // if (!in_array($username, ['haider.raza', 'bilal.saleem', 'muhammad.hamid', 'zonia.akbar'])) {
//             // $sql .= " AND students_apply_info.sai_sdpo_school_id IN (
//             //     SELECT emp_school_id
//             //     FROM emp_employees
//             //     WHERE emp_ad_username = '$username'
//             // )";
//         // }s
//         // Execute the query
//         echo $sql;exit;
//         $res = DB::select($sql);

    //         // dd($res);
//         // $total_students = sizeof($res);
//         // $top7Percent = round($total_students * 0.07);//7 percent of the total records
//         // // dd($top7Percent);
//         // $top7PercentRecords = array_slice($res, 0, ceil($top7Percent));
//         // Return the results as JSON

    //         $groupedData = [];
//         $programWiseCount = [];

    //         $pslStudents = [];     // Literature + PSL
//         $lassStudents = [];    // Minor + History -> Base LASS

    //     // Group students by program ID
//     foreach ($res as $row) {
//         $groupedData[$row->aai_sdpo_prog_id][] = $row;
//     }

    //     $top7PercentRecords = [];

    //     foreach ($groupedData as $progId => $students) {

    //         $programName = $students[0]->prog_name ?? 'Unknown';

    //         // RULE 1: Literature -> PSL
//         if (
//             $programName === 'BS in Liberal Arts & Social Sciences with Specialization in Literature'
//             || $programName === 'BS in Liberal Arts & Social Sciences with Specialization in Political Science and Literature'
//         ) {
//             $pslStudents = array_merge($pslStudents, $students);
//             continue;
//         }

    //         // RULE 2: Minor + History -> Base LASS
//         if (
//             $programName === 'BS in Liberal Arts & Social Sciences with Minor in Media Studies'
//             || $programName === 'BS in Liberal Arts & Social Sciences with Minor in Theatre , Film and TV'
//             || $programName === 'BS in Liberal Arts & Social Sciences with Specialization in History'
//         ) {
//             $lassStudents = array_merge($lassStudents, $students);
//             continue;
//         }

    //         // NORMAL PROCESSING
//         usort($students, function ($a, $b) {
//             return $b->sgpa <=> $a->sgpa;
//         });

    //         $total_students = count($students);
//         $top7Percent = ceil($total_students * 0.07);

    //         $selectedStudents = array_slice($students, 0, $top7Percent);

    //         $lastSGPA = end($selectedStudents)->sgpa;

    //         foreach (array_slice($students, $top7Percent) as $student) {
//             if ($student->sgpa == $lastSGPA) {
//                 $selectedStudents[] = $student;
//             } else {
//                 break;
//             }
//         }

    //         $top7PercentRecords = array_merge($top7PercentRecords, $selectedStudents);

    //         $programWiseCount[$progId] = [
//             'program_name' => $programName,
//             'total_students' => $total_students,
//             'count' => count($selectedStudents),
//         ];
//     }

    //         //
//         // PROCESS PSL COMBINED
//         //
//         if (!empty($pslStudents)) {

    //             usort($pslStudents, function ($a, $b) {
//                 return $b->sgpa <=> $a->sgpa;
//             });

    //             $total = count($pslStudents);
//             $limit = ceil($total * 0.07);

    //             $selected = array_slice($pslStudents, 0, $limit);

    //             $lastSGPA = end($selected)->sgpa;

    //             foreach (array_slice($pslStudents, $limit) as $student) {
//                 if ($student->sgpa == $lastSGPA) {
//                     $selected[] = $student;
//                 } else {
//                     break;
//                 }
//             }

    //             $top7PercentRecords = array_merge($top7PercentRecords, $selected);

    //             $programWiseCount['PSL_COMBINED'] = [
//                 'program_name' =>
//                 'BS in Liberal Arts & Social Sciences with Specialization in Political Science and Literature',
//                 'total_students' => $total,
//                 'count' => count($selected),
//             ];
//         }

    //         //
//         // PROCESS LASS COMBINED
//         //
//         if (!empty($lassStudents)) {

    //             usort($lassStudents, function ($a, $b) {
//                 return $b->sgpa <=> $a->sgpa;
//             });

    //             $total = count($lassStudents);
//             $limit = ceil($total * 0.07);

    //             $selected = array_slice($lassStudents, 0, $limit);

    //             $lastSGPA = end($selected)->sgpa;

    //             foreach (array_slice($lassStudents, $limit) as $student) {
//                 if ($student->sgpa == $lastSGPA) {
//                     $selected[] = $student;
//                 } else {
//                     break;
//                 }
//             }

    //             $top7PercentRecords = array_merge($top7PercentRecords, $selected);

    //             $programWiseCount['LASS_COMBINED'] = [
//                 'program_name' => 'BS in Liberal Arts & Social Sciences',
//                 'total_students' => $total,
//                 'count' => count($selected),
//             ];
//         }       

    //     }    
//     return response()->json([
//         'total_students' => count($res),
//         'count' => count($res),
//         'countrow' => count($top7PercentRecords),
//         'percentage' => $percentage ?? 0,
//         'top_7_percent' => $top7Percent ?? 0,
//         'top_7_percent_records' => $top7PercentRecords,
//         'programWiseCount' => $programWiseCount
//     ]);
// }

    public function getStudentInformation(Request $request)
    {
        // dd($request->all());
        $top7PercentRecords = [];
        if (!empty($request->school)) {
            $username = auth()->user()->username;
            $session = $request->input('session');
            $school = $request->input('school');
            $dept = $request->input('department');
            $prog = $request->input('program');
            // dd($prog);
            $sql = "select * from (SELECT
            students_apply_info.sai_sdpo_prog_id AS aai_sdpo_prog_id,
            students_apply_info.sai_sdpo_sess_id AS aai_sdpo_sess_id,
            students_apply_info.sai_api_id AS api_id,
            prog_name,
            SEMESTER_SGPA(students_apply_info.sai_enrollment_no, 166) as sgpa,
           
            students_personal_info.spi_mobile AS api_mobile,
            students_personal_info.spi_name AS api_name,
            students_personal_info.spi_formid AS api_formid,
            students_personal_info.spi_nic AS api_nic,
            students_personal_info.spi_guardian_cnic AS api_guardian_cnic,
            students_personal_info.spi_fathername AS api_fathername,
            students_personal_info.spi_imagename AS api_imagename,
            students_apply_info.sai_id AS aai_id,
            students_apply_info.sai_api_form_id AS aai_api_form_id,
            programs.prog_title AS aai_programid,
            programs.prog_title AS aai_degreecode,
            students_apply_info.sai_interview_marks AS aai_interview_marks,
            students_apply_info.sai_enrollment_no AS CMS_sai_enrollment_no,
            students_apply_info.sai_interview_totalmarks AS aai_interview_totalmarks,
            students_personal_info.spi_email AS uemail,
            students_apply_info.sai_pf_remarks AS aai_pf_remarks,
            students_apply_info.sai_qualification_status AS aai_qualification_status,
            students_apply_info.sai_qualification_remarks AS aai_qualification_remarks,
            students_apply_info.sai_student_status AS sai_student_status,
            students_apply_info.sai_prog_total_credits AS sai_prog_total_credits,
            (select sum(sc_pc_course_cr) from student_courses where sc_sai_enrollment_no = sai_enrollment_no and sc_co_sess_id = $session) as workload_curr,
            (select sum(gh_co_course_cr) from grade_history where gh_sai_enrollment_no = sai_enrollment_no AND (gh_exemption_status != 'Yes'
            OR gh_exemption_status IS NULL) ) as total_earn_crhr,            
            CASE
                WHEN EXISTS (
                    SELECT 1 FROM SESSIONSWISE_DEANHONOR_LIST
                    WHERE SESSIONSWISE_DEANHONOR_LIST.SDL_ENROLLMENT_NO = students_apply_info.sai_enrollment_no
                        AND SESSIONSWISE_DEANHONOR_LIST.SDL_SESSION_ID = $session
                        AND SESSIONSWISE_DEANHONOR_LIST.SDL_SCHOOL_ID = $school";
            // if (!empty($prog) && !empty($school)) {
            //     $sql .= "AND SESSIONSWISE_DEANHONOR_LIST.SDL_SCHOOL_ID = $prog";
            // }              
            if (!empty($session)) {
                $sql .= " AND SESSIONSWISE_DEANHONOR_LIST.SDL_SESSION_ID = $session";
            }

            if (!empty($school)) {
                $sql .= " AND SESSIONSWISE_DEANHONOR_LIST.SDL_SCHOOL_ID = $school";
            }

            if (!empty($dept)) {
                $sql .= " AND SESSIONSWISE_DEANHONOR_LIST.SDL_DEPARTMENT_ID = $dept";
            }
            if (!empty($prog)) {
                $sql .= " AND SESSIONSWISE_DEANHONOR_LIST.SDL_PROGRAM_ID = $prog";
            }
            $sql .= "AND SESSIONSWISE_DEANHONOR_LIST.SDL_TYPE = 'new'
                        AND SESSIONSWISE_DEANHONOR_LIST.STATUS = 'E'
                ) THEN '1'
                ELSE ''
            END AS tickmark
        FROM
            students_apply_info
        LEFT JOIN students_personal_info ON students_apply_info.sai_spi_id = students_personal_info.spi_id
        LEFT JOIN programs ON students_apply_info.sai_sdpo_prog_id = programs.prog_id
        LEFT JOIN schools ON students_apply_info.sai_sdpo_school_id = schools.school_id
        LEFT JOIN departments ON students_apply_info.sai_sdpo_dept_id = departments.dept_id
        LEFT JOIN admission_schedules ON admission_schedules.admission_sch_id = students_apply_info.sai_admission_sch_id
        WHERE
            students_apply_info.sai_sdpo_sess_id IS NOT NULL 
        -- AND students_apply_info.sai_student_status='Active'
        and sai_enrollment_no not in (select gh_sai_enrollment_no from grade_history where gh_grade in ('W','I'))";

            if (!empty($session)) {
                $sql .= " AND students_apply_info.sai_sdpo_sess_id  in (163,162,165,166)";
            }

            if (!empty($school)) {
                $sql .= " AND students_apply_info.sai_sdpo_school_id = $school";
            }

            if (!empty($dept)) {
                $sql .= " AND students_apply_info.sai_sdpo_dept_id = $dept";
            }

            // Apply different ORDER BY conditions based on $prog
            // if (empty($prog) && !empty($school)) {
            //     $sql .= " ORDER BY aai_sdpo_prog_id, TO_NUMBER(NVL(sgpa, '-1')) DESC";
            // } else {
            //     $sql .= " AND students_apply_info.sai_sdpo_prog_id = $prog";
            //     $sql .= " ORDER BY TO_NUMBER(NVL(sgpa, '-1')) DESC";
            // }

            if (empty($prog) && !empty($school)) {
                $sql .= " ORDER BY aai_sdpo_prog_id, TO_NUMBER(NVL(sgpa, '-1')) DESC     ) where sgpa>=3.0";
            } else {
                $sql .= " AND students_apply_info.sai_sdpo_prog_id = $prog";
                $sql .= " ORDER BY TO_NUMBER(NVL(sgpa, '-1')) DESC     ) where sgpa>=3.0";
            }

            // Restrict data access for specific users
            // if (!in_array($username, ['haider.raza', 'bilal.saleem', 'muhammad.hamid', 'zonia.akbar'])) {
            // $sql .= " AND students_apply_info.sai_sdpo_school_id IN (
            //     SELECT emp_school_id
            //     FROM emp_employees
            //     WHERE emp_ad_username = '$username'
            // )";
            // }s
            // Execute the query
            // echo $sql;exit;
            $res = DB::select($sql);

            // dd($res);
            // $total_students = sizeof($res);
            // $top7Percent = round($total_students * 0.07);//7 percent of the total records
            // // dd($top7Percent);
            // $top7PercentRecords = array_slice($res, 0, ceil($top7Percent));
            // Return the results as JSON

            $groupedData = [];
            $programWiseCount = [];
            $top7PercentRecords = [];


            // 🔥 ALL combined programs defined here (UNLIMITED)
            $combinedPrograms = [
                '944_967_965_964_966_968' => [
                    'ids' => [944, 967, 965, 964, 966, 968],
                    'program_name' => 'BS in Liberal Arts & Social Sciences'
                ],
                '3_364_1067' => [
                    'ids' => [3, 364, 1067],
                    'program_name' => 'Bachelor of Fine Arts in Visual Arts'
                ],
                '784_1065' => [
                    'ids' => [784, 1065],
                    'program_name' => 'Master of Art and Design Studies'
                ],
                '1205_905' => [
                    'ids' => [1205, 905],
                    'program_name' => 'Bachelor of Design in Visual Communication Design'
                ],
                '1066_1204' => [
                    'ids' => [1066, 1204],
                    'program_name' => 'Bachelor of Arts (Hons) Interdisciplinary Expanded Design & Art'
                ],
                '1068_1206' => [
                    'ids' => [1068, 1206],
                    'program_name' => 'Bachelor of Design in Textile, Fashion & Accessories Design'
                ],
                '8_1207' => [
                    'ids' => [8, 1207],
                    'program_name' => 'Bachelor of Architecture'
                ],
                '885_1208' => [
                    'ids' => [885, 1208],
                    'program_name' => 'Bachelor in Interior Design (BID)'
                ],
                '14_1213' => [
                    'ids' => [14, 1213],
                    'program_name' => 'MS Public Relations & Advertising'
                ],
                '886_1212' => [
                    'ids' => [886, 1212],
                    'program_name' => 'BS Journalism and Media Studies'
                ],
                '887_1211' => [
                    'ids' => [887, 1211],
                    'program_name' => 'BS Communication and Immersive Media'
                ],
                '1084_1215' => [
                    'ids' => [1084, 1215],
                    'program_name' => 'BS in Theatre, Film & TV'
                ],
                '20_1220' => [
                    'ids' => [20, 1220],
                    'program_name' => 'BSc (Hons) in Management & Business Computing'
                ],
                '22_1218' => [
                    'ids' => [22, 1218],
                    'program_name' => 'BS Software Engineering'
                ],
                '366_1217' => [
                    'ids' => [366, 1217],
                    'program_name' => 'BS Computer Science'
                ],
                '524_1224' => [
                    'ids' => [524, 1224],
                    'program_name' => 'BS in Applied Psychology'
                ],
                '1026_1228' => [
                    'ids' => [1026, 1228],
                    'program_name' => 'BBA (Hons.)'
                ],
                '1038_1044' => [
                    'ids' => [1038, 1044],
                    'program_name' => 'BS (Hons.) in Economics with Minor in Data Sciences and Business Analytics'
                ],
                '1041_1225' => [
                    'ids' => [1041, 1225],
                    'program_name' => 'BS Economics'
                ],
                '1184_1037' => [
                    'ids' => [1184, 1037],
                    'program_name' => 'BS. (Hons.) Business Intelligence and Analytics'
                ],
                '1040_1226' => [
                    'ids' => [1040, 1226],
                    'program_name' => 'BS Economics and Finance'
                ],
                '367_1223' => [
                    'ids' => [367, 1223],
                    'program_name' => 'M. Phil Linguistics and TESOL'
                ],
                '25_1222' => [
                    'ids' => [25, 1222],
                    'program_name' => 'M.Phil Educational Leadership and Management'
                ],
            ];

            // This will hold students for every program (normal + combined)
            $finalProgramGroups = [];


            // =====================================
            // Group Students
            // =====================================
            foreach ($res as $row) {

                $progId = $row->aai_sdpo_prog_id;
                $assigned = false;

                // Check if program belongs to any combined group
                foreach ($combinedPrograms as $key => $config) {
                    if (in_array($progId, $config['ids'])) {
                        $finalProgramGroups[$key][] = $row;
                        $assigned = true;
                        break;
                    }
                }

                // If not combined → keep original program ID
                if (!$assigned) {
                    $finalProgramGroups[$progId][] = $row;
                }
            }


            // =====================================
            // SINGLE Processing Loop (NO IF BLOCKS)
            // =====================================
            foreach ($finalProgramGroups as $key => $students) {
                $result = $this->processTopSevenPercent($students);
                // $result = processTopSevenPercent($students);

                $top7PercentRecords = array_merge(
                    $top7PercentRecords,
                    $result['selectedStudents']
                );

                $countRow = count($top7PercentRecords);

                // Determine program name
                if (isset($combinedPrograms[$key])) {
                    $programName = $combinedPrograms[$key]['program_name'];
                } else {
                    $programName = $students[0]->prog_name ?? 'Unknown';
                }

                $programWiseCount[$key] = [
                    'program_name' => $programName,
                    'total_students' => $result['total_students'],
                    'count' => count($result['selectedStudents']),
                ];

                // Keeping your variables unchanged for JSON
                $total_students = $result['total_students'];
                $percentage = $result['percentage'];
                $top7Percent = $result['top7Percent'];
            }


            // =====================================
            // Final JSON (UNCHANGED STRUCTURE)
            // =====================================

            return response()->json([
                'total_students' => $total_students,
                'count' => count($res),
                'countrow' => $countRow,
                'percentage' => $percentage,
                'top_7_percent' => $top7Percent,
                'top_7_percent_records' => $top7PercentRecords,
                'programWiseCount' => $programWiseCount
            ]);
        }
    }

    function processTopSevenPercent($students)
    {
        $total_students = count($students);

        usort($students, function ($a, $b) {
            return $b->sgpa <=> $a->sgpa;
        });

        $top7Percent = ceil($total_students * 0.07);
        $originalPercent = ($total_students * 0.07);
        $percentage = ($originalPercent / $total_students) * 100;

        $selectedStudents = array_slice($students, 0, $top7Percent);

        if (!empty($selectedStudents)) {
            $lastSGPA = end($selectedStudents)->sgpa;

            foreach (array_slice($students, $top7Percent) as $student) {
                if ($student->sgpa == $lastSGPA) {
                    $selectedStudents[] = $student;
                } else {
                    break;
                }
            }
        }

        return [
            'selectedStudents' => $selectedStudents,
            'total_students' => $total_students,
            'percentage' => $percentage,
            'top7Percent' => $top7Percent
        ];
    }

    public function submitDeanHonorList(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'session' => 'required',
            'school' => 'required',
            // 'dept' => 'required',

            'students' => 'required'
        ]);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'error' => $validator->getMessageBag()->toArray()
            ), 400);
        }
        $userId = auth()->user()->id;
        foreach ($request->students as $value) {
            // dd($value);
            $data = [
                'SDL_APPLY_ID' => $value['aai_id'],
                'SDL_SESSION_ID' => $request->session,
                'SDL_SCHOOL_ID' => $request->school,
                'SDL_DEPARTMENT_ID' => $request->dept,
                'SDL_PROGRAM_ID' => $request->program,
                'SDL_ENROLLMENT_NO' => $value['cms_sai_enrollment_no'],
                'SDL_PAGE_NAME' => 'dean_honor',
                'SDL_GPA' => $value['sgpa'],
                'ENTRY_USER_ID' => $userId,
                'STATUS' => ($value['checked'] ? 'E' : 'D'),
                'CREATED_AT' => now()
            ];
            $duplicateCheck = DB::table('SESSIONSWISE_DEANHONOR_LIST')
                ->select(
                    'SDL_ID',
                    'SDL_APPLY_ID',
                    'SDL_SESSION_ID',
                    'SDL_SCHOOL_ID',
                    'SDL_DEPARTMENT_ID',
                    'SDL_PROGRAM_ID',
                    'SDL_ENROLLMENT_NO'
                )
                ->where('SDL_APPLY_ID', $value['aai_id'])
                ->where('SDL_SESSION_ID', $request->session)
                ->where('SDL_SCHOOL_ID', $request->school)
                ->where('SDL_DEPARTMENT_ID', $request->dept)
                ->where('SDL_PROGRAM_ID', $request->program)

                ->where('SDL_TYPE', 'new')
                ->get();
            //  dd(sizeof($duplicateCheck));
            if (sizeof($duplicateCheck) == 0) {
                DB::table('SESSIONSWISE_DEANHONOR_LIST')->insert($data);
            } else {
                //dd($data);
                DB::table('SESSIONSWISE_DEANHONOR_LIST')
                    ->where('SDL_ID', $duplicateCheck[0]->sdl_id)
                    ->update($data);
            }

        }
        return response()->json(['success' => true, 'messege' => 'Submitted Successfully']);
    }

    public function approveDeanHonorList(Request $request)
    {
        // dd($request->all());
        // $validator = Validator::make($request->all(),[
        //     'session' => 'required',
        //     'school' => 'required',
        //     'dept' => 'required',
        //     'program' => 'required',
        //     'students'=> 'required'

        // ]);
        // if($validator->fails()) {
        //     return Response::json(array(
        //         'success' => false,
        //         'error' => $validator->getMessageBag()->toArray()
        //     ), 400);
        // }
        $userId = auth()->user()->id;
        foreach ($request->students as $value) {
            // dd($value);
            $data = [
                // 'SDL_APPLY_ID' => $value['aai_id'],
                // 'SDL_SESSION_ID' => $request->session,
                // 'SDL_SCHOOL_ID' => $request->school,
                // 'SDL_DEPARTMENT_ID' => $request->dept,
                // 'SDL_PROGRAM_ID' => $request->program,
                // 'SDL_ENROLLMENT_NO' => $value['cms_sai_enrollment_no'],
                // 'SDL_GPA' => $value['sgpa'],
                // 'ENTRY_USER_ID' => $userId,
                'STATUS' => ($value['checked'] ? 'E' : 'D'),
                'CREATED_AT' => now()
            ];
            // $duplicateCheck = DB::table('SESSIONSWISE_DEANHONOR_LIST')
            //                 ->select(
            //                         'SDL_ID',
            //                         'SDL_APPLY_ID',
            //                         'SDL_SESSION_ID',
            //                         'SDL_SCHOOL_ID',
            //                         'SDL_DEPARTMENT_ID',
            //                         'SDL_PROGRAM_ID',
            //                         'SDL_ENROLLMENT_NO'
            //                         )
            //                         ->where('SDL_APPLY_ID',  $value['aai_id'])
            //                         ->where('SDL_SESSION_ID', $request->session)
            //                         ->where('SDL_SCHOOL_ID', $request->school)
            //                         ->where('SDL_DEPARTMENT_ID', $request->dept)
            //                         ->where('SDL_PROGRAM_ID', $request->program)
            //                         ->where('SDL_TYPE', 'new')
            //                         ->get();
            // if(sizeof($duplicateCheck) == 0 ){
            //     DB::table('SESSIONSWISE_DEANHONOR_LIST')->insert($data);
            // }else{
            DB::table('SESSIONSWISE_DEANHONOR_LIST')
                ->where('SDL_ID', $value['sdl_id'])
                ->update($data);
            // }

        }
        return response()->json(['success' => true, 'messege' => 'Submitted Successfully']);
    }
    public function getDeanHonor_oldData(Request $request)
    {
        // dd($request->all());
        // WHEN '$request->category' = 'probation' THEN  SEMESTER_CGPA(sai_enrollment_no,$request->session)
        // ELSE SDL_GPA 
        if ($request->category == 'probation') {
            $sql = "
        SELECT 
            SDL_ID,
            SDL_APPLY_ID,
            SDL_SESSION_ID,
            SDL_SCHOOL_ID,
            SDL_DEPARTMENT_ID,
            SDL_PROGRAM_ID,
            SDL_ENROLLMENT_NO,            
            CASE          
                WHEN '$request->category' = 'probation' THEN NVL(SEMESTER_CGPA(sai_enrollment_no,$request->session), 0)
                ELSE NVL(SDL_GPA, 0)
            END AS SDL_GPA,
            ENTRY_USER_ID,
            prog_name,
            dept_name,
            STATUS,
            CREATED_AT,
            SDL_TYPE,
            acd1.SESS_NAME AS SESS_NAME, 
            school.SCHOOL_NAME,
            (SELECT SUM(sc_pc_course_cr) 
             FROM student_courses 
             WHERE sc_sai_enrollment_no = SESSIONSWISE_DEANHONOR_LIST.SDL_ENROLLMENT_NO 
             AND sc_co_sess_id = $request->session) AS workload_curr,
            (SELECT SUM(gh_co_course_cr) 
             FROM grade_history 
             WHERE gh_sai_enrollment_no = SESSIONSWISE_DEANHONOR_LIST.SDL_ENROLLMENT_NO) AS total_earn_crhr
        FROM SESSIONSWISE_DEANHONOR_LIST
        JOIN ACADEMIC_SESSIONS  acd1 ON acd1.SESS_ID = SDL_SESSION_ID
       
         left join students_apply_info on sai_enrollment_no = sdl_enrollment_no
        LEFT JOIN schools           school ON school.school_id = sai_sdpo_school_id
        LEFT JOIN departments           dept ON dept.dept_id = sai_sdpo_dept_id
        LEFT JOIN programs           prog ON prog.prog_id = sai_sdpo_prog_id
        WHERE SDL_TYPE = 'old'
        and sdl_page_name = 'Probation'
    ";



            if (!empty($request->session)) {
                $sql .= " AND SDL_SESSION_ID =$request->session";

            }
            if (!empty($request->school)) {
                $sql .= " AND SDL_SCHOOL_ID = $request->school";

            }
            if (isset($request->department)) {
                $sql .= " AND sai_sdpo_dept_id = $request->department";
            }
            if (isset($request->program)) {
                $sql .= " and sai_sdpo_prog_id = $request->program";
            }

            $data = DB::select($sql);
            //     dd($data);
            return DataTables::of($data)->toJson();

        } else {
            $sql = "SELECT 
                    SDL_ID,
                    SDL_APPLY_ID,
                    SDL_SESSION_ID,
                    SDL_SCHOOL_ID,
                    SDL_DEPARTMENT_ID,
                    SDL_PROGRAM_ID,
                    SDL_ENROLLMENT_NO,
                    SDL_GPA,
                    ENTRY_USER_ID,
                    STATUS,
                    CREATED_AT,
                    SDL_TYPE,
                    acd1.SESS_NAME AS SESS_NAME,
                    school.SCHOOL_NAME,
                    prog_name,
                    dept_name,
                    (
                        SELECT SUM(gh_co_course_cr) 
                        FROM grade_history 
                        WHERE gh_sai_enrollment_no = SESSIONSWISE_DEANHONOR_LIST.SDL_ENROLLMENT_NO
                        and gh_grade not in ('W','F')
                        and gh_co_sess_id = {$request->session}
                    ) AS workload_curr,
                    (
                        SELECT SUM(gh_co_course_cr) 
                        FROM grade_history 
                        WHERE gh_sai_enrollment_no = SESSIONSWISE_DEANHONOR_LIST.SDL_ENROLLMENT_NO
                        and gh_grade not in ('W','F')
                    ) AS total_earn_crhr,
                    SAI_PROG_TOTAL_CREDITS as total_req_crhr
                FROM 
                    SESSIONSWISE_DEANHONOR_LIST
                INNER JOIN 
                    ACADEMIC_SESSIONS  acd1 ON acd1.SESS_ID = SESSIONSWISE_DEANHONOR_LIST.SDL_SESSION_ID
                INNER JOIN 
                    SCHOOLS  school ON school.SCHOOL_ID = SESSIONSWISE_DEANHONOR_LIST.SDL_SCHOOL_ID
                    left join students_apply_info on sai_enrollment_no = sdl_enrollment_no
                    LEFT JOIN schools           school ON school.school_id = sai_sdpo_school_id
                    LEFT JOIN departments           dept ON dept.dept_id = sai_sdpo_dept_id
                    LEFT JOIN programs           prog ON prog.prog_id = sai_sdpo_prog_id
                WHERE 
                    SDL_TYPE = 'old'
                    and sai_sdpo_sess_id < 162
                    and sdl_page_name = 'dean_honor'";
            if (isset($request->session)) {
                $sql .= " AND SDL_SESSION_ID =$request->session";
            }
            if (isset($request->school)) {
                $sql .= " AND SDL_SCHOOL_ID = $request->school";
            }
            if (isset($request->department)) {
                $sql .= " AND sai_sdpo_dept_id = $request->department";
            }
            if (isset($request->program)) {
                $sql .= " and sai_sdpo_prog_id = $request->program";
            }
            //    echo $sql;exit;

            $data = DB::select($sql);
            // dd($data);
            return DataTables::of($data)->toJson();
        }
    }
    public function getDeanHonor_newData(Request $request)
    {
        $data = DB::table('SESSIONSWISE_DEANHONOR_LIST')
            ->select(
                'SDL_ID',
                'SDL_APPLY_ID',
                'SDL_SESSION_ID',
                'SDL_SCHOOL_ID',
                'SDL_DEPARTMENT_ID',
                'SDL_PROGRAM_ID',
                'SDL_ENROLLMENT_NO',
                'SDL_GPA',
                'ENTRY_USER_ID',
                'STATUS',
                'CREATED_AT',
                'SDL_TYPE',

                'acd1.SESS_NAME as SESS_NAME', // Session name for FFS_SESS_ID
                'school.SCHOOL_NAME',
                'dept.DEPT_NAME',
                'prog.PROG_NAME',
            )
            ->join('ACADEMIC_SESSIONS as acd1', 'acd1.SESS_ID', '=', 'SDL_SESSION_ID') // Join for FFS_SESS_ID
            ->join('SCHOOLS as school', 'school.SCHOOL_ID', 'SDL_SCHOOL_ID')
            ->join('DEPARTMENTS as dept', 'dept.DEPT_ID', 'SDL_DEPARTMENT_ID')
            ->join('PROGRAMS as prog', 'prog.PROG_ID', 'SDL_PROGRAM_ID')
            ->where('SDL_TYPE', 'new')
            ->where('STATUS', 'E')
            ->get();
        // dd($data);
        return DataTables::of($data)->toJson();
    }


}
