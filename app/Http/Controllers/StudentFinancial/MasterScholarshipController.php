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

class MasterScholarshipController extends Controller{

        function masterScholarshipForm(){
            $sessions = \App::call('App\Http\Controllers\Grader\AcademicSessionController@retrieve');
            $schools =  Schools::all();
            $scholarships = DB::select("select * from SCHOLARSHIP_TYPE where SCHOLARSHIP_DONER_TYPE='private'");
            $employees = DB::select('select * from EMP_EMPLOYEES');
            // dd($sessions, $scholarships, $employees);
            $departments = array(); // $this->school->fetch();
            $facultyCourses = \App::call('App\Http\Controllers\Grader\FacultyCoursesController@retrieve');
            $offeredFaculty = \App::call('App\Http\Controllers\Grader\CoursesOfferedController@retrieveFaculty');
            $programs =  DB::select("select * from Programs where PROG_STATUS = 'E'");
            $query = 'select * from fin_fee_category';
            $feeCategories = DB::select($query);
            return view('StudentFinancial.master_scholarship',compact('sessions','schools','scholarships','employees','facultyCourses','offeredFaculty','feeCategories','programs'));

        }
        public function addScholarship(Request $request){
            $validator = Validator::make($request->all(),[
                'scholarship' => 'required',
                'user' => 'required',
                'validity-type' => 'required',
                'amount' => 'required|numeric'
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
            }

            if($request->get('validity-type') == 'session'){
                $validator = Validator::make($request->all(),[
                    'session' => 'required',
                ]);
                if($validator->fails()) {
                    return Response::json(array(
                        'success' => false,
                        'errors' => $validator->getMessageBag()->toArray()
                    ), 400);
                }
            }elseif($request->get('validity-type') == 'annual'){
                $validator = Validator::make($request->all(),[
                    'effective_from' => 'required',
                    'effective_to' => 'required',
                ]);
                if($validator->fails()) {
                    return Response::json(array(
                        'success' => false,
                        'errors' => $validator->getMessageBag()->toArray()
                    ), 400);
                }
            }
            // dd(auth()->user()->employee_id);
            $userId = auth()->user()->id;
            $data = [
                'FSB_SCHLR_SHIP_ID' => $request->get('scholarship'),
                'FSB_SESSION_ID' =>$request->get('session'),
                'FSB_VALIDITY_TYPE' =>$request->get('validity-type'),
                'FSB_TO_USERID' => $request->get('user'),
                'FSB_EFFECT_FROM' => $request->get('effective_from'),
                'FSB_EFFECT_TO' => $request->get('effective_to'),
                'FSB_AMOUNT' => $request->get('amount'),
                'FSB_REMAINIG_AMOUNT' => $request->get('amount'),
                'FSB_ENTRY_USERID' => $userId,
                'FSB_STATUS' => 'E'
            ];

            DB::beginTransaction();
            $mesg = '';
            // try {
                if(empty($request->mainId)) {
                    $duplicateCheck = DB::table('FIN_SCHOLARSHIP_BUDGET')
                                    ->select(
                                            'FSB_ID',
                                            'FSB_SCHLR_SHIP_ID',
                                            'FSB_SESSION_ID',
                                            'FSB_TO_USERID'
                                            )
                                            ->where('FSB_SCHLR_SHIP_ID', $request->get('scholarship'))
                                            ->where('FSB_SESSION_ID', $request->get('session'))
                                            ->where('FSB_TO_USERID', $request->get('user'))
                                            ->where('FSB_STATUS', 'E')
                                            ->get();
                                            // dd($duplicateCheck);
                    if(sizeof($duplicateCheck) == 0 ){
                        // INSERTION CASE
                        DB::table('FIN_SCHOLARSHIP_BUDGET')->insert($data);
                        $lastId = DB::table('FIN_SCHOLARSHIP_BUDGET')->latest('FSB_ID')->first()->fsb_id;
                        $form_id = $lastId . $request->get('scholarship');
                        $mesg = 'Saved Successfully';
                    }else{//UPDATE CASE
                        $affectedRows = DB::table('FIN_SCHOLARSHIP_BUDGET')
                                    ->where('FSB_ID', $duplicateCheck[0]->fsb_id)
                                    ->update($data);

                        if ($affectedRows > 0) {
                            $lastId = $request->mainId;
                            $form_id = $lastId . $request->get('scholarship');
                            $mesg = 'Updated Successfully';
                        }
                    }
                }else{
                    // EDIT CASE
                    // First, empty the session, effFrom, and effTo columns of the specific row
                    DB::table('FIN_SCHOLARSHIP_BUDGET')
                        ->where('FSB_ID', $request->mainId)
                        ->update([
                            'FSB_EFFECT_FROM' => null,
                            'FSB_EFFECT_TO' => null,
                            'FSB_SESSION_ID' => null
                        ]);

                    // Then, update the row with the new data
                    $affectedRows = DB::table('FIN_SCHOLARSHIP_BUDGET')
                                    ->where('FSB_ID', $request->mainId)
                                    ->update($data);

                    if ($affectedRows > 0) {
                        $lastId = $request->mainId;
                        $form_id = $lastId . $request->get('scholarship');
                        $mesg = 'Updated Successfully';
                    }
                }
                // If a new row was inserted or an existing row was updated, update FSB_FORM_ID
                if (!empty($lastId)) {
                    DB::table('FIN_SCHOLARSHIP_BUDGET')
                        ->where('FSB_ID', $lastId)
                        ->update(['FSB_FORM_ID' => $form_id]);
                }
                // Commit the transaction
                DB::commit();

                // If no errors occurred, return a success response
                return response()->json([
                    'success' => true,
                    'msg' => $mesg,
                ]);
            // } catch (\Exception $e) {
            //     // Something went wrong, rollback the transaction
            //     DB::rollback();

            //     // Log the error
            //     \Log::error('Error: ' . $e->getMessage());

            //     // Return a failure response
            //     return response()->json([
            //         'success' =getProgramFeeStructureForm> false,
            //         'msg' => 'Something went wrong'
            //     ]);
            // }
        }
        public function getScholarshipList(Request $request){
            $data = DB::table('FIN_SCHOLARSHIP_BUDGET as fsb')
                ->select(
                        'fsb.FSB_ID',
                        'fsb.FSB_FORM_ID',
                        'st.SCHOLARSHIP_NAME',
                        'fsb.FSB_VALIDITY_TYPE',
                        'acdSess.SESS_NAME',
                        'emp.PREFIX',
                        'emp.EMP_F_NAME',
                        'emp.EMP_L_NAME',
                        'fsb.FSB_EFFECT_FROM',
                        'fsb.FSB_EFFECT_TO',
                        'fsb.FSB_AMOUNT',
                        'fsb.fsb_remainig_amount',
                        'fsb.FSB_SCHLR_SHIP_ID',
                        'fsb.FSB_TO_USERID',
                        'fsb.FSB_SESSION_ID',

                        )
                        ->leftjoin('SCHOLARSHIP_TYPE as st', 'st.SCHOLARSHIP_ID', 'fsb.FSB_SCHLR_SHIP_ID')
                        ->leftjoin('ACADEMIC_SESSIONS as acdSess', 'acdSess.SESS_ID', 'fsb.FSB_SESSION_ID')
                        ->leftjoin('EMP_EMPLOYEES as emp', 'emp.EMPLOYEE_ID', 'fsb.FSB_TO_USERID')
                        ->where('fsb.fsb_status', 'E')
                        ->orderBy('fsb.FSB_ENTRY_DATE', 'desc')
                        ->get();
                    // dd($data);
                return response()->json([
                    'success' => true,
                    'data' => $data
            ]);
        }
        public function delete_scholarship(Request $request){
            $userId = auth()->user()->id;
            $affectedRows =  DB::table('FIN_SCHOLARSHIP_BUDGET')
                                ->where('FSB_ID', $request->mainId)
                                ->update([
                                    'FSB_STATUS' => 'D',
                                    'FSB_ENTRY_USERID' => $userId,
                                ]);
            if ($affectedRows > 0) {
                return response()->json([
                    'success' => true,
                    'data' => 'Deleted Successfully'
            ]);
            } else {
                return response()->json([
                    'success' => false,
                    'data' => 'Some problem occured'
                ]);
            }

        }
    
}
