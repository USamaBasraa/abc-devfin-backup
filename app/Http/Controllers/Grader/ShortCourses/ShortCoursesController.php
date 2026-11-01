<?php

namespace App\Http\Controllers\Grader\ShortCourses;

use Illuminate\Http\Request;
use App\Models\UserNotifications;
use DB;
use DataTables;
use Validator;
Validator::extend('no_quotes', function ($attribute, $value, $parameters, $validator) {
    return !preg_match('/[\'"]/', $value);
});
use Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;


class ShortCoursesController extends Controller
{
	public function index()
	{
		$schools = $this->getSchools();
		return view('grader.short_courses.shortcoursesList', compact("schools"));
	}

	public function store(Request $request)
	{
        // dd($request->all());
        $validator = Validator::make($request->all(),[
            'school' => 'required',
            'course_fee' => 'required|numeric|min:100',
            'course_title' => ['required', 'no_quotes'],
            'course_code' => 'required',
            'credit_hours' => 'required',
            'instructor_name' => 'required',
            'course_description' => 'required',
            'due_date' => 'required',
            'valid_date' => 'required',

        ]);

        if($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400);
        }
        $dueDate = strtotime($request->get('due_date'));
        $validDate = strtotime($request->get('valid_date'));

        if ($dueDate > $validDate) {
            return Response::json([
                'success' => false,
                'errors' => [
                    'due_date' => ['The due date must be a date before valid date or same as the valid date.'],
                ],
            ], 400);
        }
        if($request->school == '22'){//22 is should be  school id of ORIP in the school table which is not yet present there
            $validator = Validator::make($request->all(),[
                'category' => 'required'
            ]);
            if($validator->fails()) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 400);
            }
        }
		$data = [
			'OFFER_SCHOOL_ID' => $request->school,
			'COURSE_FEE' => $request->course_fee,
			'COURSE_TITLE' => $request->course_title,
			'COURSE_CODE' => $request->course_code,
			'CREDIT_HOURS' => $request->credit_hours,
			'CATEGORY' => $request->category,
			'course_description' => $request->course_description,
			'INSTRUCTOR_NAME' => $request->instructor_name,
			'DUE_DATE' => $request->due_date,
			'VALID_DATE' => $request->valid_date,
			'STATUS' => 'E'
		];
        DB::transaction(function () use ($request, $data) {
            if(empty($request->mainId)){// Insert case
                $res = DB::table('svad_short_coures_list')->insert($data);
                if ($res) {
                    return response()->json([
                        "msg" => "Data Save Successfully.",

                    ]);
                }
            }
            else{//update case

                $affectedRows = DB::table('svad_short_coures_list')
                                ->where('ID', $request->mainId)
                                ->update($data);
                if ($affectedRows > 0) {
                    return response()->json([
                        "msg" => "Data Updated Successfully.",

                    ]);
                }
            }
        });
        if(empty($request->mainId)){// Insert case
            return response()->json([
                "msg" => "Data Saved Successfully."
            ]);
        }else{//updation case
            return response()->json([
                "msg" => "Data Updated Successfully."
            ]);
        }

	}
	public function delete_course(Request $request){
        $affectedRows =  DB::table('SVAD_SHORT_COURES_LIST')
                            ->where('ID', $request->mainId)
                            ->update([
                                'STATUS' => 'D'
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
    protected function getShortCoursesList()
	{
        $data = DB::table('svad_short_coures_list as ssc')
                ->select('ssc.ID',
                    'ssc.COURSE_TITLE',
                    'ssc.OFFER_SCHOOL_ID',
                    'ssc.CATEGORY',
                    'ssc.INSTRUCTOR_NAME',
                    'ssc.COURSE_DESCRIPTION',
                    'ssc.CREDIT_HOURS',
                    'ssc.COURSE_CODE',
                    'ssc.COURSE_FEE',
                    'ssc.DUE_DATE',
                    'ssc.VALID_DATE',
                    'school.SCHOOL_NAME'
                    )
                    ->leftjoin('SCHOOLS as school', 'school.SCHOOL_ID', 'ssc.OFFER_SCHOOL_ID')
                    ->where('ssc.STATUS', 'E')
                    ->where('ssc.ID', '>' , '47')
                    ->orderBy('ssc.ID', 'desc')
                    ->get();
		return $data;
	}

	function getEmail(Request $request){
		$applicantId = $request->get('getEmail');
		$sql = "SELECT
					svad_short_coures_list.course_title,
					admission_short_courses_group.applicant_email,
					(select count(*) from admission_short_courses_applicant where email = applicant_email) as chkprofile,
					admission_short_courses_applied.id,
					discount_percentage,discount_status
				FROM
					admission_short_courses_group
					LEFT JOIN svad_short_coures_list ON svad_short_coures_list.id = course_id
					LEFT JOIN admission_short_courses_applicant ON email = applicant_email
					left join admission_short_courses_applied on admission_short_courses_applicant.id = admission_short_courses_applied.applicant_id
				WHERE
					admission_short_courses_group.applicant_id = $applicantId";
					return $res = DB::select($sql);
	}


	function updateIndDiscount(Request $request){
		$id = $request->get('id');
		$indDiscoun = $request->get('discount');
		$upd = "update ADMISSION_SHORT_COURSES_APPLIED set discount_percentage= $indDiscoun where id=$id";
		//echo $upd;exit;
		$res = DB::insert($upd);

	}

	function updateIndapproval(Request $request){
		$id = $request->get('id');
		$sql = "select * from ADMISSION_SHORT_COURSES_APPLIED where id=$id";
		$ressql = DB::select($sql);
		//echo $sql;exit;
		//echo $ressql[0]->discount_status;exit;
		if($ressql[0]->discount_status == 1){
			$upd = "update ADMISSION_SHORT_COURSES_APPLIED set discount_status = 0 where id=$id";
			$res = DB::insert($upd);
			if($res){
				return '0';
			}
		}else{
			$upd = "update ADMISSION_SHORT_COURSES_APPLIED set discount_status = 1 where id=$id";
			$res = DB::insert($upd);


			$sqlCheckMail = "select * from ADMISSION_SHORT_COURSES_APPLICANT where id = $id";
			$resCheckMail = DB::select($sqlCheckMail);
			$email = $resCheckMail[0]->email;

			$remarks = "We are pleased to inform you that your request for a discount has been approved.
                        Congratulations! To complete your registration and secure your place in Success Lab 2024,
                        please follow the instructions below to make the payment with the discounted rate:

                        1. Go to the BNU Short Courses website.
                        2. Click on the &quot;Log In&quot; button at the top right corner of the page.
                        3. Enter your login credentials to access your account.
                        4. Once logged in, navigate to the payment section.
                        5. Follow the prompts to complete your payment at the discounted rate.

                        If you encounter any issues or have any questions, please do not hesitate to contact us. Thank
                        you for your cooperation. We look forward to your participation in Success Lab 2024.

                        Regards";

			$display    = 'Discount Approval and Payment Instructions for Success Lab 2024';
			 $this->notificationOnCompletion($email,'Short Course Approved',$remarks,$display);

			if($res){
				return '1';
			}
		}
	}

	protected function getSchools()
	{

		$sql = "SELECT * FROM SCHOOLS";
		//echo $sql;exit;
		$res = DB::select($sql);
		return $res;
	}
    public function applicants(){
        $schools = $this->getSchools();
        $courses=$this->getShortCoursesList();
        return view('grader.short_courses.applicantsList', compact('schools', 'courses'));    }
	// protected function getDepartments(Request $request)
	// {
	// 	$sql = "SELECT * FROM DEPARTMENTS WHERE DEPT_SCHOOL_ID = '$request->school_id'";
	// 	//echo $sql;exit;
	// 	$res = DB::select($sql);a
	// 	return $res;
	// }
	// protected function getPrograms(Request $request)
	// {
	// 	$sql = "SELECT * FROM PROGRAMS WHERE PROG_DEPT_ID ='$request->dept_id'";
	// 	//echo $sql;exit;
	// 	$res = DB::select($sql);
	// 	return $res;
	// }

	// protected function getCourses(Request $request)
	// {
	// 	$sql = "select distinct courses.course_id, courses.course_name from program_courses left join courses ON pc_course_id = courses.course_id where program_courses.pc_prog_id ='$request->prog_id'";
	// 	// echo $sql;exit;
	// 	$res = DB::select($sql);
	// 	return $res;
	// }

    protected function getApplicantsList(Request $request){
        // dd($request->all());
        $data = DB::table('ADMISSION_SHORT_COURSES_APPLICANT as ascp')
        ->select(
            'ascp.ID',
            'ascp.APLICANTNAME',
            'ascp.APLICANT_LASTNAME',
            'ascp.DESIGNATION',
            'ascp.MOBILE',
            'ascp.QUALIFICATION',
            'ascp.DEGREE_NAME',
            'app.DISCOUNT',
            'app.DISCOUNT_STATUS',
            'ssc.COURSE_TITLE',
            'app.id as abc',
            'app.discount_percentage',
            'ascp.uploadimage',
            'ascp.email',
            'app.course_id',
            'app.fee_status',
            // Add subquery for student_challan
            DB::raw("(SELECT sc.sc_challan_status FROM student_challan sc WHERE sc.sc_app_id = ascp.id AND ssc.id = sc_category_id AND sc.sc_challan_type IN ('short_Fee', 'Success_Lab', 'Summer_School') AND invoice_status = 'E'  and sc_challan_status='PAID' AND ROWNUM = 1) as challan_field")
        )
        ->join('ADMISSION_SHORT_COURSES_APPLIED as app', 'app.applicant_id', '=', 'ascp.id')
        ->join('svad_short_coures_list as ssc', 'ssc.ID', '=', 'app.course_id')
        // ->join('Schools as s', 's.SCHOOL_ID', '=', 'ssc.offer_school_id')
        ->whereNull('ascp.group_course')
        ->where('app.status','E');

    if (!empty($request->school_id)) {
        $data = $data->where('ssc.offer_school_id', $request->school_id);
    }

    if (!empty($request->course_id)) {
        $data = $data->where('app.course_id', $request->course_id);
    }

    $data = $data->orderBy('ascp.ID', 'desc')->get();

        // dd($data);
        return DataTables::of($data)->toJson();
    }

	function updateDiscount(Request $request){
		$discountFee = $request->get('discountfee');
		$id = $request->get('id');
		$upd = "update ADMISSION_SHORT_COURSES_APPLIED set discount_percentage = $discountFee where id=$id";
		$res = DB::insert($upd);
	}

	public function approval(Request $request) {
        $id = $request->get('id');
        $applicant_id = $request->get('applicant_id');

        // Check if ID exists in the table
        $record = DB::table('ADMISSION_SHORT_COURSES_APPLIED')->where('id', $id)->first();

        if (!$record) {
            return "Invalid ID"; // or any appropriate error handling
        }

        $discount_status = $record->discount_status;

        // Toggle discount status
        $new_discount_status = $discount_status == 1 ? 0 : 1;

        // Update discount_status
        DB::table('ADMISSION_SHORT_COURSES_APPLIED')->where('id', $id)->update(['discount_status' => $new_discount_status]);

        // Send notification email upon approval
        if ($new_discount_status == 1) {
            $applicant = DB::table('ADMISSION_SHORT_COURSES_APPLICANT')->where('id', $applicant_id)->first();
            // dd($applicant);
            if ($applicant) {
                $email = $applicant->email;
                $remarks = "We are pleased to inform you that your request for a discount has been approved.
                Congratulations! To complete your registration and secure your place in Success Lab 2024,
                please follow the instructions below to make the payment with the discounted rate:

                1. Go to the BNU Short Courses website.
                2. Click on the &quot;Log In&quot; button at the top right corner of the page.
                3. Enter your login credentials to access your account.
                4. Once logged in, navigate to the payment section.
                5. Follow the prompts to complete your payment at the discounted rate.

                If you encounter any issues or have any questions, please do not hesitate to contact us. Thank
                you for your cooperation. We look forward to your participation in Success Lab 2024.

                Regards";

            $display    = 'Discount Approval and Payment Instructions for Success Lab 2024';
             $this->notificationOnCompletion($email,'Short Course Approved',$remarks,$display);

                // Assuming `notificationOnCompletion` returns something, otherwise remove this line

            }
        }

        return $new_discount_status; // Return the new discount status
    }



	public function paidMark(Request $request) {
        $id = $request->get('id');
        $applicant_id = $request->get('applicant_id');

        // Check if ID exists in the table
        $record = DB::table('ADMISSION_SHORT_COURSES_APPLIED')->where('id', $id)->first();

        if (!$record) {
            return "Invalid ID"; // or any appropriate error handling
        }



        // Toggle discount status
        $new_discount_status =$request->get('btntype') ;

        // Update discount_status
        DB::table('ADMISSION_SHORT_COURSES_APPLIED')->where('id', $id)->update(['fee_status' => $new_discount_status]);


        return $new_discount_status; // Return the new discount status
    }


	function notificationOnCompletion($to,$subject,$message,$display){
        // echo $to.'   '.$subject.'   '.$message.'   '.$display;exit;
        DB::table('testing')
        ->insert(
              ['description' => $display.' To: '.$to.' || Subject: '.$subject.' || Message: '.$message]
        );
        $procedurenotification = 'send_mail_one_by_one';
        $bindings_notification = [
        'to'           => $to,
        'subject'      => $subject ,
        'message'      => $message,
        'display'      => $display,
        ];
        // dd( $bindings_notification);
     $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
    }

    public function deleteApplicant(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'applicant_id' => 'required'
            ]);

           // Prepare the raw SQL query
           $applicantId = $request->applicant_id;
           $course_id = $request->course_id;
          // $sql = "DELETE FROM ADMISSION_SHORT_COURSES_APPLICANT WHERE id = :applicant_id";
        //    $sql = "update ADMISSION_SHORT_COURSES_APPLICANT set status='D' WHERE id = :applicant_id";

        $sql = "update admission_short_courses_applied set status='D' WHERE applicant_id = $applicantId and course_id = $course_id";

           // Execute the raw SQL query
        //    $deleted = DB::statement($sql, ['applicant_id' => $applicantId]);
        $deleted = DB::insert($sql);

            if ($deleted) {
                // Return success response if the record was deleted
                return response()->json(['message' => 'Applicant record deleted successfully.'], 200);
            } else {
                // Return error response if the record was not found
                return response()->json(['message' => 'Applicant record not found.'], 404);
            }
        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return response()->json(['message' => 'An error occurred while deleting the applicant record.','error'=>$e], 500,);
        }
    }
}
