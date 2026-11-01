<?php

namespace App\Http\Controllers\ACL;

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

class SportsApplicantsController extends Controller{

    function sportsApplicantForm(){
        $sessions = DB::select("select * from ACADEMIC_SESSIONS where SESS_STATUS = 'E' ORDER BY SESS_ID DESC");
        $scholarships = DB::select("select * from SCHOLARSHIP_TYPE");
        $categoryAccess = $this->getStudentCategoryAccess();
        return view('acl.sportsApplicants', compact('sessions', 'scholarships', 'categoryAccess'));
    }

    /**
     * Username-based access for Student Category options.
     * Sports team → Sports only; RO team → Scholarship only; others → both.
     */
    private function getStudentCategoryAccess()
    {
        $username = strtolower(trim((string) (auth()->user()->username ?? '')));

        $sportsTeam = [
            'aizaz.hussain',
        ];

        $roTeam = [
            'abdul.waheed',
            'talha.sarfraz',
            'tayyaba.nawaz',
            'hassan.farooq',
        ];

        $canSports = true;
        $canScholarship = true;

        if (in_array($username, $sportsTeam, true)) {
            $canSports = true;
            $canScholarship = false;
        } elseif (in_array($username, $roTeam, true)) {
            $canSports = false;
            $canScholarship = true;
        }

        return [
            'canSports' => $canSports,
            'canScholarship' => $canScholarship,
            'username' => $username,
        ];
    }
    function autoSearch(Request $request){
        // dd($request->all());
        $username_enrollmnt   =   $request->get('searchParam');

        return  DB::table('STUDENTS_APPLY_INFO as sai')
            ->select(DB::raw('sai.SAI_ENROLLMENT_NO as value, spi.spi_name as text'))
            ->leftjoin('STUDENTS_PERSONAL_INFO as spi', 'spi.SPI_ID', 'sai.SAI_SPI_ID')
            ->whereRaw("lower(spi.spi_name || ' ' || sai.SAI_ENROLLMENT_NO) like lower(?)", ["%$username_enrollmnt%"])
            ->whereIn('sai.sai_student_status', ['Active', 'Expected-Graduate'])
            ->limit(10)
            ->get();
    }
    function searchStudent(Request $request){

        $username= $request->get('username');
        // Use preg_match to find the string between square brackets
        if (preg_match('/\[(.*?)\]/', $username, $matches)) {
            // $matches[1] will contain the string inside the brackets
            $username = $matches[1];

        }
        $data = DB::table('STUDENTS_APPLY_INFO as sai')
                ->select(
                        'sai.SAI_ENROLLMENT_NO',
                        'spi.spi_name',

                        'sch.SCHOOL_NAME',
                        'dept.DEPT_NAME',
                        'prog.PROG_NAME',

                        'sai.SAI_SDPO_SCHOOL_ID',
                        'sai.SAI_SDPO_DEPT_ID',
                        'sai.SAI_SDPO_PROG_ID',
                        )
                ->leftjoin('STUDENTS_PERSONAL_INFO as spi', 'spi.SPI_ID', 'sai.SAI_SPI_ID')
                ->leftjoin('Schools as sch', 'sch.SCHOOL_ID', 'sai.SAI_SDPO_SCHOOL_ID')
                ->leftjoin('DEPARTMENTS as dept', 'dept.DEPT_ID', 'sai.SAI_SDPO_DEPT_ID')
                ->leftjoin('PROGRAMS as prog', 'prog.PROG_ID', 'sai.SAI_SDPO_PROG_ID')
                ->whereRaw('LOWER(sai.SAI_ENROLLMENT_NO) = LOWER(?)', [$username])
                ->first();

        if(!empty($data)){
            return response()->json(['success' => true, 'profileData' => $data]);
        }else{
            $data='';
            return response()->json(['success' => false, 'profileData' => $data]);
        }

    }
    public function addSportsApplicant(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'session' => 'required',
            'student-category' => 'required',
            'enrolment-no' => 'required',
            'school' => 'required',
            'dept' => 'required',
            'prog' => 'required',
            'student-name' => 'required',
        ]);

        // Conditional validation for sport-category
        $validator->sometimes('sport-category', 'required', function ($input) {
            return $input->get('student-category') == 'Sports';
        });

        // Conditional validation for scholarship-category
        $validator->sometimes('scholarship-category', 'required', function ($input) {
            return $input->get('student-category') == 'Scholarship';
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }

        // Enforce username-based Student Category access (Sports team / RO team)
        $categoryAccess = $this->getStudentCategoryAccess();
        $studentCategory = $request->get('student-category');
        if ($studentCategory === 'Sports' && !$categoryAccess['canSports']) {
            return response()->json([
                'success' => false,
                'msg' => 'You are not allowed to add Sports category applicants.',
            ], 403);
        }
        if ($studentCategory === 'Scholarship' && !$categoryAccess['canScholarship']) {
            return response()->json([
                'success' => false,
                'msg' => 'You are not allowed to add Scholarship category applicants.',
            ], 403);
        }
        $this->updateChallans($request->get('enrolment-no'), 'add');
        // dd(auth()->user()->employee_id);
        $userId = auth()->user()->id;
        $data = [
            'sa_sess_id' => $request->get('session'),
            'sa_enrolment_no' =>$request->get('enrolment-no'),
            'sa_student_category' =>$request->get('student-category'),
            'sa_sports_category' =>$request->get('sport-category'),
            'sa_scholarship_category' =>$request->get('scholarship-category'),
            'sa_student_name' =>$request->get('student-name'),
            'sa_entry_user_id' => $userId,
            'sa_created_at' =>  date('Y-m-d H:i:s'),
            'sa_updated_at' =>  date('Y-m-d H:i:s')
        ];

        // DB::beginTransaction();
        $mesg = '';
        $success = '';
        if(empty($request->mainId)) {
            $duplicateCheck = DB::table('sports_applicants')
                            ->select(
                                    'sa_id',
                                    'sa_enrolment_no',
                                    'sa_sess_id'
                                    )
                                    ->where(DB::raw('LOWER(sa_enrolment_no)'), strtolower($request->get('enrolment-no')))
                                    ->where('sa_sess_id', $request->get('session'))
                                    // ->where('SP_STATUS', 'E')
                                    ->get();
                                    // dd($duplicateCheck);
            if(sizeof($duplicateCheck) == 0 ){
                // INSERTION CASE
                DB::table('sports_applicants')->insert($data);
                $mesg = 'Saved Successfully';
                $success = true;
            }else{//UPDATE CASE
                $affectedRows = DB::table('sports_applicants')
                            ->where('sa_id', $duplicateCheck[0]->sa_id)
                            ->update($data);

                if ($affectedRows > 0) {
                    $mesg = 'Updated Successfully';
                    $success = true;
                }
            }
        }else{
            $duplicateCheck = DB::table('sports_applicants')
                                ->select(
                                    'sa_id',
                                    'sa_enrolment_no',
                                    'sa_sess_id'
                                    )
                                    ->where(DB::raw('LOWER(sa_enrolment_no)'), strtolower($request->get('enrolment-no')))
                                    ->where('sa_sess_id', $request->get('session'))
                                    // ->where('SP_STATUS', 'E')
                                    ->where('sa_id', '!=', $request->mainId)
                                    ->get();
                                    //  dd($duplicateCheck);
            if(sizeof($duplicateCheck) == 0 ){
                $affectedRows = DB::table('sports_applicants')
                            ->where('sa_id', $request->mainId)
                            ->update($data);

                if ($affectedRows > 0) {

                    $mesg = 'Updated Successfully';
                    $success = true;
                }
            }else{
                $mesg = 'Duplicate entries not allowed!';
                $success = false;
            }
            // Commit the transaction
            // DB::commit();
        }
        return response()->json([
            'success' => $success,
            'msg' => $mesg,
        ]);

    }
    public function getSportApplicanLlist(Request $request){
        $categoryAccess = $this->getStudentCategoryAccess();

        $query = DB::table('sports_applicants as sa')
            ->select(
                    'sa.SA_ID',
                    'sa.SA_ENROLMENT_NO',
                    'sa.SA_STUDENT_NAME',
                    'sa.SA_STUDENT_CATEGORY',
                    'sa.SA_SPORTS_CATEGORY',
                    'sa.SA_SCHOLARSHIP_CATEGORY',
                    'sa.SA_SESS_ID',
                    'scholshp.SCHOLARSHIP_NAME',
                    'sess.SESS_NAME',
                    )
                    ->leftjoin('ACADEMIC_SESSIONS as sess', 'sess.SESS_ID', 'sa.SA_SESS_ID')
                    ->leftjoin('SCHOLARSHIP_TYPE as scholshp', 'scholshp.SCHOLARSHIP_ID', 'sa.SA_SCHOLARSHIP_CATEGORY');

        // Username-wise table filter: Sports team → Sports, RO team → Scholarship
        if ($categoryAccess['canSports'] && !$categoryAccess['canScholarship']) {
            $query->where('sa.SA_STUDENT_CATEGORY', 'Sports');
        } elseif (!$categoryAccess['canSports'] && $categoryAccess['canScholarship']) {
            $query->where('sa.SA_STUDENT_CATEGORY', 'Scholarship');
        }

        $data = $query->orderBy('sa.SA_CREATED_AT', 'desc')->get();

        return DataTables::of($data)->toJson();
    }
    public function deleteSportApplicant(Request $request){
        // $userId = auth()->user()->id;
        $affectedRows = DB::table('sports_applicants')
                        ->where('SA_ID', $request->mainId)
                        ->delete(); // Use delete method

        // Check if any rows were affected by the delete operation
        if ($affectedRows > 0) {
            $this->updateChallans($request->enrollmentNo, 'delete');
            return response()->json([
                'success' => true,
                'data' => 'Deleted Successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Some problem occurred or no record found'
            ]);
        }

    }
    public function checkChallan(Request $request){
        // dd($request->enrollment_no);
        $challanData =  DB::table('STUDENT_CHALLAN as sc')
                            ->join('SUBSCRIPTIONS as sub', 'sub.CHALLAN_ID', 'sc.id')
                            ->where('sc.SC_ENROLLMENT_NO', $request->enrollment_no)
                            ->where('sc.SC_CHALLAN_TYPE', 'Gym')
                            //conditions to check unpaid
                            ->where('sc.SC_CHALLAN_STATUS', 'UNPAID')
                            ->whereNull('sc.RESPONSE_STATUS')
                            ->whereNull('sc.CALLBACK_PAID_ON')
                            ->get();
        // dd(sizeof($challanData));
        $totalChallans = sizeof($challanData);
        return response()->json([
            'success' => true,
            'data' => $totalChallans
        ]);
    }
    public function updateChallans($enrollmentNo, $action){
        // dd($enrollmentNo);
        if($action == 'add'){//
            $comments = 'Challan updated as per sports/scholarship applicant';
            $column = 'SP_SPORTS_PRICING';
        }else{//deletion from sport/scholarship applicant
            $comments=' Challan update as per regular applicant';
            $column = 'SP_PRICING';
        }
        $affectedRow = DB::table('STUDENT_CHALLAN as sc')
                            ->where('sc.SC_ENROLLMENT_NO', $enrollmentNo)
                            ->where('sc.SC_CHALLAN_TYPE', 'Gym')
                            ->where('sc.SC_CHALLAN_STATUS', 'UNPAID')
                            ->whereNull('sc.RESPONSE_STATUS')
                            ->whereNull('sc.CALLBACK_PAID_ON')
                            ->update([
                                'sc.SC_AMOUNT' => DB::raw("(SELECT subplan.$column
                                                            FROM SUBSCRIPTION_PLANS subplan
                                                            JOIN SUBSCRIPTIONS sub ON subplan.SP_ID = sub.SP_ID
                                                            WHERE sub.CHALLAN_ID = sc.id)"),
                                'sc.INVOICEAMOUNT' => DB::raw("(SELECT subplan.$column
                                                                FROM SUBSCRIPTION_PLANS subplan
                                                                JOIN SUBSCRIPTIONS sub ON subplan.SP_ID = sub.SP_ID
                                                                WHERE sub.CHALLAN_ID = sc.id)")
                            ]);
        if($affectedRow > 0){//after updating the challan also add comment in subscription table
            // Update SUBSCRIPTIONS.COMMENTS
            DB::table('SUBSCRIPTIONS as sub')
                    ->whereExists(function ($query) use ($enrollmentNo) {
                        $query->select(DB::raw(1))
                            ->from('STUDENT_CHALLAN as sc')
                            ->whereRaw('sc.id = sub.CHALLAN_ID')
                            ->where('sc.SC_ENROLLMENT_NO', $enrollmentNo)
                            ->where('sc.SC_CHALLAN_TYPE', 'Gym')
                            ->where('sc.SC_CHALLAN_STATUS', 'UNPAID')
                            ->whereNull('sc.RESPONSE_STATUS')
                            ->whereNull('sc.CALLBACK_PAID_ON');
                    })
                    ->update([
                        'sub.COMMENTS' => $comments
            ]);
                    }
        return $affectedRow;
    }
}
