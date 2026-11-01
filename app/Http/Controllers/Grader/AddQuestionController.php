<?php
/**
 * Created by PhpStorm.
 * User: bilal.saleem
 * Date: 30/03/2020
 * Time: 11:30 AM
 */

namespace App\Http\Controllers\Grader;
use Illuminate\Http\Request;
use App\sessions;
use App\Models\Grader\CourseCriterias;
use App\Models\Grader\CourseCriteriaDetails;
use DB;
use App\Http\Controllers\Controller;


class AddQuestionController extends Controller
{

	 function my_simple_crypt( $string, $action = 'd' ) {
  // you may change these values to your own
  $secret_key = 'my_simple_secret_key';
  $secret_iv = 'my_simple_secret_iv';

  $output = false;
  $encrypt_method = "AES-256-CBC";
  $key = hash( 'sha256', $secret_key );
  $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

  if( $action == 'e' ) {
      $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
  }
  else if( $action == 'd' ){
      $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
  }

  return $output;
}




public function question_bank($co_id,Request $request){
        $sess = new sessions();
        $sess->startSession();
        $login_id   = $sess->emp_user_id;

           /////decrypt co id /////
          $fc_co_id=$this->my_simple_crypt($co_id, 'd' );

          $sqlexam="select id,title, co_id, fc_co_course_id from EXAM_PAPERS left join faculty_courses on EXAM_PAPERS.co_id=faculty_courses.FC_CO_ID where co_id=$fc_co_id";
            $result_exam = DB::select($sqlexam);

       $sql="select fc_co_course_id, fc_co_id from  faculty_courses where fc_co_id=$fc_co_id";
        $result_course = DB::select($sql);

            $fc_course_id=$result_course[0]->fc_co_course_id ;

        $sqlquestion="select *from exam_question_bank where course_id=$fc_course_id or ENTRY_USER_ID = $login_id order by id desc";
       // echo   $sqlquestion;exit();
        $result = DB::select($sqlquestion);


        $course_name = DB::selectOne("select get_course_name($fc_co_id) as value from dual");
         $course_name=$course_name->value;

        return view('grader.exam.question_bank',['question_bank'=>$result,'exam_bank'=>$result_exam,'result_course'=>$result_course,'course_name'=> $course_name]);

    }

public function ques_status(Request $request){

          $exam_paper_id = $request->get('exam_paper_id');
          $status = $request->get('status');
          $question_id = $request->get('question_id');
          $desc = $request->get('desc');

      $result=DB::table('exam_student_paper')
            ->where('course_exam_papers_id', $exam_paper_id)
             ->where('QUESTION_BANK_ID', $question_id)
            ->update(['answer_status' =>$status,
                      'description' =>$desc
                      ]);


            $result11=DB::table('EXAM_QUESTION_PAPER')
             ->where('EXAM_QUESTION_BANK_ID', $question_id)
            ->where('EXAM_PAPERS_ID', $exam_paper_id)
            ->update(['question_status' =>1
                      ]);

  $data = array('status' => true, 'message' => 'Question Status change Successfully.');
                 return response()->json($data);
}

    public function exam_questionbank(Request $request){
      // echo $request->get('from_dashboard');exit;
      //  dd($request->all());
      $sess = new sessions();
      $sess->startSession();
      $login_id   = $sess->emp_user_id;
      $co_id=$request->get('co_id');
      $exam_id=$request->get('id');
      // dd($exam_id);
      $from_dashboard = $request->get('from_link');
      $course_id = $request->get('co_id');
      $result_examquestion ='1';
      $fc_co_id=$this->my_simple_crypt($co_id, 'd' );

    if(!isset($from_dashboard)){

      $sch_id=$request->get('sch_id');
      $sess = new sessions();
      $sess->startSession();
      $login_id   = $sess->emp_user_id;
      $adm_sch_name ='';

      $sqlexam="select exam_question_paper.exam_papers_id,
      exam_question_paper.EXAM_QUESTION_BANK_ID,question,title,EXAM_QUESTION_PAPER.id, exam_question_bank.oa, exam_question_bank.oe,exam_question_bank.ob,exam_question_bank.oc,exam_question_bank.od,exam_question_bank.CORRECT,
      exam_question_bank.exam_ques_type,word_limit,exam_section_id,
      file_required,OPTION_F,OG,OH,OI,neg_marks,upload_file
      from EXAM_QUESTION_PAPER
      left join EXAM_PAPERS on EXAM_QUESTION_PAPER.EXAM_PAPERS_ID = EXAM_PAPERS.id
      left join exam_question_bank on EXAM_QUESTION_PAPER.EXAM_QUESTION_BANK_ID = exam_question_bank.id
      where EXAM_PAPERS_ID=$exam_id
       order by exam_ques_type desc" ;
      //echo $sqlexam;exit();
      $result_examquestion = DB::select($sqlexam);

    }
    //dd($result_examquestion);
    //////////////////add question ////////////
    if($co_id or isset($from_dashboard)){

        if(isset($from_dashboard)){
          $sqlexam="select id,title, co_id, fc_co_course_id,total_questions from EXAM_PAPERS left join faculty_courses on co_id=faculty_courses.FC_CO_ID where co_id=$fc_co_id";
          $result_exam = DB::select($sqlexam);
          //echo $sqlexam;exit;
        }else{
          $sqlexam="select id,title, co_id, fc_co_course_id,total_questions from EXAM_PAPERS left join faculty_courses on co_id=faculty_courses.FC_CO_ID where co_id=$fc_co_id and id=$exam_id";
          $result_exam = DB::select($sqlexam);
        }
        $fc_course_id=(isset($result_exam[0]->fc_co_course_id)=='')? '0': $result_exam[0]->fc_co_course_id ;
        $data = session()->all();
        //echo $fc_course_id;exit;
        $sqlquestion="select ID,
                  QUESTION,
                  OA,
                  OB,
                  OC,
                  OD,
                  EXAM_STATUS,
                  CORRECT,
                  STATUS,
                  CO_ID,
                  COURSE_ID,
                  EXAM_ID,
                  ENTRY_USER_ID,
                  OE,
                  OPTION_F,
                  OG,
                  OH,
                  OI,
                  EXAM_QUES_TYPE,
                  WORD_LIMIT,
                  FILE_REQUIRED,
                  (SELECT COUNT(*)
                  FROM exam_question_paper
                  WHERE exam_question_bank_id = exam_question_bank.id
                  and exam_papers_id = '$exam_id'
                  ) as count,
                  (
                    SELECT
                        COUNT(*)
                    FROM
                        exam_question_paper
                    WHERE
                            exam_question_bank_id = exam_question_bank.id
                ) AS all_count
                  from exam_question_bank
                  where course_id=$fc_course_id  and    (ENTRY_USER_ID = $login_id or ENTRY_USER_ID is null)
                  order by id desc";

          // echo  $sqlquestion;exit;
         $result = DB::select($sqlquestion);
      }

        // echo $sqlquestion;exit();
        $examSection        = "select * from exam_sections order by id ";
        $examSectionResults = DB::select($examSection);

        //////////////////end /////////////////////////
        if($co_id){
          $course_name = DB::selectOne("select get_course_name($fc_co_id) as value from dual");
          $course_name=$course_name->value;

          return view('grader.exam.exam_question_bank',
                    [
                        'question_bank'     =>$result,
                        'examSectionResults'=>$examSectionResults,
                        'exam_questinbank'  =>$result_examquestion,
                        'exam_bank'         =>$result_exam,
                        'course_name'       => $course_name,
                        'sch_id'             =>'',
                        'exam_id'             =>$exam_id,
                        'dashboard'         =>$from_dashboard

                    ]
                  );
        }else if($sch_id){

          $sql_sch_name  = "select admission_sch_name from admission_schedules where admission_sch_id =$sch_id";

          $sch_name  = DB::select($sql_sch_name);

          $sqlTestName = "select * from exam_papers where id = $exam_id";
         // echo $sqlTestName;exit;
          $ressqlTestname = DB::select($sqlTestName);
          $admissionTestTitle = $ressqlTestname[0]->title;
          //$sch_name = $res_sch_name[0]->admission_sch_name;
          //echo $sch_name;exit;

$sqlquestion="select ID,
        QUESTION,
        OA,
        OB,
        OC,
        OD,
        EXAM_STATUS,
        CORRECT,
        STATUS,
        CO_ID,
        COURSE_ID,
        EXAM_ID,
        ENTRY_USER_ID,
        OE,
        OPTION_F,
        OG,
        OH,
        OI,
        EXAM_QUES_TYPE,
        WORD_LIMIT,
        FILE_REQUIRED,
          (SELECT COUNT(*)
          FROM exam_question_paper
          WHERE exam_question_bank_id = exam_question_bank.id
          and exam_papers_id =   $exam_id
          ) as count,
              (
            SELECT
                COUNT(*)
            FROM
                exam_question_paper
            WHERE
                    exam_question_bank_id = exam_question_bank.id
        ) AS all_count

          from exam_question_bank where EXAM_PAPER_ADMISSION_SCH_ID=$sch_id order by id desc";
          //echo $sqlquestion; exit;
         $result = DB::select($sqlquestion);
         $sqlexam="select id,title, co_id, fc_co_course_id,total_questions from EXAM_PAPERS left join faculty_courses on co_id=faculty_courses.FC_CO_ID where co_id=1";
         $result_exam = DB::select($sqlexam);

return view('grader.exam.exam_question_bank',
                    [
                        'question_bank'     =>$result,
                        'examSectionResults'=>$examSectionResults,
                        'exam_questinbank'  =>$result_examquestion,
                        'exam_bank'         =>$result_exam,
                        'exam_adm2'         =>$sch_name,
                        'course_name'       => $admissionTestTitle,
                        'sch_id'             =>$sch_id,
                        'exam_id'             =>$exam_id,
                        'dashboard'         =>$from_dashboard
                    ]
                  );
        }




    }

    public function viewpaper(Request $request){

      $exam_id=$request->get('id');
      $adm_sch_id=$request->get('sch_id');
            $co_id=$request->get('co_id');

            //dd($adm_sch_id);

        $sess = new sessions();
        $sess->startSession();
        $login_id   = $sess->emp_user_id;


     $sqlexam="select exam_question_paper.exam_papers_id,
    exam_question_paper.EXAM_QUESTION_BANK_ID,question,title,EXAM_QUESTION_PAPER.id, exam_question_bank.oa, exam_question_bank.oe,exam_question_bank.ob,exam_question_bank.oc,exam_question_bank.od,exam_question_bank.CORRECT,
    exam_question_bank.exam_ques_type,word_limit,exam_section_id,  exam_question_bank.og,
    exam_question_bank.option_f,
    file_required,
    neg_marks,
    question_marks,
    (select sum(question_marks) from exam_question_paper  WHERE EXAM_PAPERS_ID =$exam_id) as total_marks from EXAM_QUESTION_PAPER
                    left join EXAM_PAPERS on EXAM_QUESTION_PAPER.EXAM_PAPERS_ID = EXAM_PAPERS.id
                    left join exam_question_bank on EXAM_QUESTION_PAPER.EXAM_QUESTION_BANK_ID = exam_question_bank.id
                    where EXAM_PAPERS_ID=$exam_id order by exam_ques_type desc" ;
             //  echo $sqlexam;exit();
        $result_examquestion = DB::select($sqlexam);


//dd($result_examquestion);
//////////////////add question ////////////



        //$fc_course_id=$result_exam[0]->fc_co_course_id ;


        $examSection        = "select * from exam_sections order by id ";
        $examSectionResults = DB::select($examSection);

        //////////////////end /////////////////////////
if($co_id != 0){

   $sqlexam="select id,title, co_id, fc_co_course_id,total_questions from EXAM_PAPERS left join faculty_courses on co_id=faculty_courses.FC_CO_ID where co_id=$co_id and id=$exam_id";
       //echo $sqlexam;exit();
 $result_exam = DB::select($sqlexam);

 $sqlquestion="select * from exam_question_bank where co_id=$co_id  order by id desc";
         $result = DB::select($sqlquestion);


  $course_name = DB::selectOne("select get_course_name($co_id) as value from dual");
         $course_name=$course_name->value;

        return view('grader.exam.exam_view_paper',
                    [
                        'question_bank'     =>$result,
                        'examSectionResults'=>$examSectionResults,
                        'exam_questinbank'  =>$result_examquestion,
                        'exam_bank'         =>$result_exam,
                        'course_name'       => $course_name
                    ]
                  );

}

 if($adm_sch_id !=0){
 $sqlquestion="select * from exam_question_bank where EXAM_PAPER_ADMISSION_SCH_ID=$adm_sch_id  order by id desc";
         $result = DB::select($sqlquestion);
 $sqlexam="select id,title, co_id, fc_co_course_id,total_questions from EXAM_PAPERS left join faculty_courses on co_id=faculty_courses.FC_CO_ID where co_id=0 and id=$exam_id";
       //echo $sqlexam;exit();
 $result_exam = DB::select($sqlexam);


return view('grader.exam.exam_view_paper',
                    [
                        'question_bank'     =>$result,
                        'examSectionResults'=>$examSectionResults,
                        'exam_questinbank'  =>$result_examquestion,
                        'exam_bank'         =>$result_exam,
                        'course_name'       => ''
                    ]
                  );

 }





    }


/////////////////////insert direct question bank start////////////////////////////////////
public function insert_questionbank(Request $request){
        $sess = new sessions();
        $sess->startSession();
        $login_id   = $sess->emp_user_id;

        $co_id = $request->get('co_id');
        $course_id = $request->get('course_id');
          $question_name = $request->get('qname');
          $assessment_id = $request->get('assessment_id');
          $option_a = $request->get('option_a');
          $option_b = $request->get('option_b');
          $option_c = $request->get('option_c');
          $option_d = $request->get('option_d');
          $exam_id = $request->get('exam_id');
          $edit_id = $request->get('edit_id');
          $adm_sch_id = $request->get('adm_sch_id');
          $correct_option = $request->get('correct_option');


           // if($adm_sch_id){
              $result = DB::table('exam_question_bank')->insert(
                            ['QUESTION' => $question_name,
                            'OA' => $option_a,
                            'OB' => $option_b,
                            'OC' => $option_c,
                            'OD' =>  $option_d,
                            'CORRECT'=>$correct_option,
                            'EXAM_STATUS' => 0,
                            'STATUS' => 'E',
                            'CO_ID' => $co_id,
                            'COURSE_ID' => $course_id,
                            'EXAM_ID'=>$exam_id,
                            'ENTRY_USER_ID'=> $login_id

                          ]
                        );

          // $result = DB::table('exam_question_bank')->insert(
          //                   ['QUESTION' => $question_name,
          //                   'OA' => $option_a,
          //                   'OB' => $option_b,
          //                   'OC' => $option_c,
          //                   'OD' =>  $option_d,
          //                   'CORRECT'=>$correct_option,
          //                   'EXAM_STATUS' => 0,
          //                   'STATUS' => 'E',
          //                   'CO_ID' => $co_id,
          //                   'COURSE_ID' => $course_id,
          //                   'EXAM_ID'=>$exam_id,
          //                   'ENTRY_USER_ID'=> $login_id
          //                 ]
          //               );


}
/////////////////////////////insert direct question bank end///////////////////////////



/////// insert question in question bank ///////////////////
     public function insert_question(Request $request)
    {
         $sess = new sessions();
        $sess->startSession();
        $login_id   = $sess->emp_user_id;
        $current_date = date('Y-m-d H:i:s');

        $countoptions = $request->get('countoptions');

          $co_id = $request->get('co_id');
          $edit_id = $request->get('edit_id');
          $course_id = $request->get('course_id');
          $question_name = $request->get('qname');
          $assessment_id = $request->get('assessment_id');
          $array_opt = $request->get('option_arr');
          $previousFileName = $request->get('file-name');
          $option_a = $array_opt[0];
          $option_b = $array_opt[1];
          $option_c = $array_opt[2];
          $option_d = $array_opt[3];
          $option_e = $array_opt[4];
          $option_f = $array_opt[5];
          $option_g = $array_opt[6];
          $option_h = $array_opt[7];
          $option_i = $array_opt[8];
          if($request->get('dashboard') != 'yes'){
          $exam_id = $request->get('exam_id');
         
          }else{
          $exam_id = '';
          }
          $exam_type = $request->get('exam_type');
          $adm_sch_id = $request->get('adm_sch_id');
          $current_datetime = $request->get('current_date_time');
           $adm_exam_id = $request->get('adm_exam_id');
           $adm_sch_id = ($adm_sch_id == '') ? 0 : $adm_sch_id;
           if($adm_exam_id){
            $exam_id=$adm_exam_id;
           }

          if($exam_type == 1){
          $correct_option = $request->get('correct_option');
          $word_limit = '';
          $file_require = '';
          }else if($exam_type == 3){
          $correct_option = $request->get('true_false');
          $word_limit = '';
          $file_require = '';
          }else if($exam_type == 2){
            $correct_option = '';
            $word_limit = $request->get('sq_word_limit');
            $file_require = $request->get('sqfile_require');
          } else if($exam_type == 4){
            $correct_option = '';
            $word_limit = $request->get('word_limit');
            $file_require = $request->get('file_require');

          }
          //echo $edit_id;exit;
          if($edit_id!=''){
           $fileName = null; // Default, means no new file uploaded
            if ($request->hasFile('file')) {
                $validationRules['file'] = 'required|mimes:pdf,jpg,jpeg,png';
                $validationMessages['file.required'] = 'The file field is required.';
                $validationMessages['file.mimes'] = 'The file must be a PDF, JPG, PNG, or JPEG.';
                $validatedData = $request->validate($validationRules, $validationMessages);

                // Use query builder to avoid SQL injection
                $record = DB::table('exam_question_bank')->where('id', $edit_id)->first();
                $uploadedDBFileName = $record->upload_file ?? null;

                // Delete old file if it exists
                if (!empty($uploadedDBFileName)) {
                    $oldPath = public_path('docs/' . $uploadedDBFileName);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // Handle new file upload
                $file = $request->file('file');
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = uniqid() . '.' . $fileExtension;
                $fileUpload = $file->move(public_path('docs'), $fileName);

                if (!$fileUpload) {
                    return response()->json([
                        'message' => 'File not uploaded!',
                        'status' => false
                    ]);
                }

            }

          // Prepare update data
            $updateData = [
                'question'        => $question_name,
                'oa'              => $option_a,
                'ob'              => $option_b,
                'oc'              => $option_c,
                'od'              => $option_d,
                'oe'              => $option_e,
                'option_f'        => $option_f,
                'og'              => $option_g,
                'oh'              => $option_h,
                'oi'              => $option_i,
                'correct'         => $correct_option,
                'word_limit'      => $word_limit,
                'file_required'   => $file_require,
                'ENTRY_DATE_TIME' => $current_datetime,
            ];

            // Only set 'upload_file' if a new file was uploaded
            if ($fileName !== null) {
                $updateData['upload_file'] = $fileName;
            }else if (empty($previousFileName) ){
                $updateData['upload_file'] = null;
            }

            // Perform update
            $result = DB::table('exam_question_bank')
                ->where('id', $edit_id)
                ->update($updateData);



               $data = array('status' => true, 'message' => 1);
                     return response()->json($data);

        }else{

          if($request->get('dashboard') != 'yes'){
        $sqlexamquestion="select QUESTION_MARKS from EXAM_QUESTION_PAPER where EXAM_PAPERS_ID=$exam_id and QUESTION_MARKS > 1" ;

        $result_examquestion = DB::select($sqlexamquestion);
         $count=count($result_examquestion);
          }else{
            $count = 0;
          }
//dd($count);
 if($count==0){
    // dd('new');
  $fileName='';
  if ($request->hasFile('file')) {
      $validationRules['file'] = 'required|mimes:pdf,jpg,jpeg,png';

      $validationMessages['file.required'] = 'The file field is required.';
      $validationMessages['file.mimes'] = 'The file must be a PDF, JPG, PNG, or JPEG.';


      $validatedData = $request->validate($validationRules, $validationMessages);
      // Get the file extension
      $fileExtension = $request->file('file')->getClientOriginalExtension();
      // Generate a unique filename
      $fileName = uniqid().'.'.$fileExtension;

      $file = $request->file('file');


      $fileUpload=$request->file->move(public_path() . '/docs/', $fileName);

      if (!$fileUpload) {
          return response()->json([
              'message' => 'File not uploaded!',
              'status' => false
          ]);
          return 'Insert into EPORTAL_REQUEST failed';


      }

  }
 $id = DB::table('exam_question_bank')->insertGetId(
                            ['QUESTION' => $question_name,
                            'OA' => $option_a,
                            'OB' => $option_b,
                            'OC' => $option_c,
                            'OD' =>  $option_d,
                            'OE' =>  $option_e,
                            'option_f'      =>$option_f,
                            'og'            =>$option_g,
                            'oh'            =>$option_h,
                            'oi'            =>$option_i,
                            'CORRECT'=>$correct_option,
                            'EXAM_STATUS' => 0,
                            'STATUS' => 'E',
                            'CO_ID' => $co_id,
                            'COURSE_ID' => $course_id,
                            'EXAM_ID'=>$exam_id,
                            'ENTRY_USER_ID'=> $login_id,
                            'EXAM_QUES_TYPE' => $exam_type,
                            'WORD_LIMIT' => $word_limit,
                            'file_required'    =>$file_require,
                             'EXAM_PAPER_ADMISSION_SCH_ID'=> $adm_sch_id,
                             'ENTRY_DATE_TIME' => $current_datetime,
                             'upload_file'=>$fileName
                          ]
                        );


    if($exam_type == 1){
      $count=0;
      foreach ($array_opt as $key ) {
        if($count < $countoptions){
            DB::table('exam_question_options')->insert(
              [
              'QUESTION_BANK_ID' => $id,
              'QUESTION_OPTION_TEXT' => $key
              ]
            );
      $count++;
        }
      }
    }
    if($request->get('dashboard') != 'yes'){
                 if($exam_id!=''){
                 $result = DB::table('EXAM_QUESTION_PAPER')->insert(
                                          ['EXAM_QUESTION_BANK_ID' => $id,
                                          'EXAM_PAPERS_ID' => $exam_id,
                                           'STATUS' => 'E',
                                            'ENTRY_DATE_TIME' => $current_date,
                                            'ENTRY_USER_ID' => $login_id,
                                          ]);
                 }
                }
                $data = array('status' => true, 'message' => 'Question Saved Successfully.');
                 return response()->json($data);
                }else{

                $data = array('message' => 2);
                 return response()->json($data);

             }

           }
    }
//// add question in exam /////////////
  public function exam_question(Request $request,$id,$exam_id)
    {

        // echo $id.'---'.$exam_id;
        // exit;
        $sess = new sessions();
        $sess->startSession();
        $login_id   = $sess->emp_user_id;
        $current_date = date('Y-m-d H:i:s');
 		     $question = DB::table('exam_question_bank')->where('ID', '=', $id)->first();

    	  /////// question check in exam paper //////
    	 $sqlquestion="select QUESTION_MARKS from EXAM_QUESTION_PAPER where EXAM_QUESTION_BANK_ID=$id and EXAM_PAPERS_ID=$exam_id";
        $result = DB::select($sqlquestion);
        /////////////////////question check in exam paper///////////////////

   $sqlexamquestion="select QUESTION_MARKS from EXAM_QUESTION_PAPER where EXAM_PAPERS_ID=$exam_id and QUESTION_MARKS > 1" ;

        $result_examquestion = DB::select($sqlexamquestion);

        //$sqlexampaper="select total_questions from EXAM_PAPERS where ID=$exam_id";
        //$result_sqlexampaper = DB::select($sqlexampaper);
         //$total_question= $result_sqlexampaper[0]->total_questions;
        //$exam_total_marks= $result_examquestion->question_marks;
        $count=count($result_examquestion);
        //  if($exam_total_ques < $total_question){

    	  $total=count($result);

          		if($total==0 && $count==0){

          			 $result = DB::table('EXAM_QUESTION_PAPER')->insert(
												    ['EXAM_QUESTION_BANK_ID' => $id,
												    'EXAM_PAPERS_ID' => $exam_id,
                             'ENTRY_DATE_TIME' => $current_date,
                              'ENTRY_USER_ID' => $login_id,
												       'STATUS' =>'E',
													]
												);
				///// update status question bank /////
          			    $sqlupdatecontent = "UPDATE exam_question_bank
                                      SET EXAM_STATUS = 1
                                      WHERE  id=$id";

                $result = DB::insert($sqlupdatecontent);

				   ///// update status /////
 				       $data = array('status' => true, 'message' => 1);
                 return response()->json($data);
          		}else{

             if($total > 0){
               $data = array('message' => 0);
                 return response()->json($data);
               }else if($count >0)
				    $data = array('message' => 2);
                 return response()->json($data);

          		}

           // }
            // else{

            //     $data = array('message' => 2);
            //      return response()->json($data);

            // }

    }

///////////marks management view //////////////////
    public function marks_management($co_id,Request $request){

       //dd($co_id,$request->all());

      // dd($co_id);
      if ($co_id == 0) {
        $exam_id = $request->get('id');
        $co_id_encrpt =  $request->get('sch_id');
        $course_id = '';
        $sqlexam="select question,title,question_marks,EXAM_QUESTION_PAPER.id,neg_marks,total_with_neg,
        EXAM_PAPERS_ID from EXAM_QUESTION_PAPER
        left join EXAM_PAPERS on EXAM_QUESTION_PAPER.EXAM_PAPERS_ID = EXAM_PAPERS.id
        left join exam_question_bank on EXAM_QUESTION_PAPER.EXAM_QUESTION_BANK_ID = exam_question_bank.id
        where EXAM_PAPERS_ID=$exam_id order by id desc" ;
        // echo $sqlexam;exit;
        $result_examquestion = DB::select($sqlexam);

        // $course_name = DB::selectOne("select get_course_name($co_id) as value from dual");
        $course_name='Admission Test';
        $sqlexamquestion="select count(*) as question_count from EXAM_QUESTION_PAPER where EXAM_PAPERS_ID=$exam_id";
        $examquestion = DB::select($sqlexamquestion);
        $sqlexampaper="select total_questions from EXAM_PAPERS where ID=$exam_id";
        $result_sqlexampaper = DB::select($sqlexampaper);

        $total_question= $result_sqlexampaper[0]->total_questions;
        $exam_total_ques= $examquestion[0]->question_count;


      }else{
        $co_id_encrpt=$this->my_simple_crypt($co_id,'e');
        $exam_id=$request->get('id');
        //dd($exam_id);EXAM_PAPERS

        $sql = "select co_course_id from courses_offered where co_id = $co_id";
        $res_course_id = DB::select($sql);
        $course_id =$this->my_simple_crypt($res_course_id[0]->co_course_id,'e');

        $sqlexam="select question,title,question_marks,EXAM_QUESTION_PAPER.id,neg_marks,total_with_neg,
                  EXAM_PAPERS_ID from EXAM_QUESTION_PAPER
                  left join EXAM_PAPERS on EXAM_QUESTION_PAPER.EXAM_PAPERS_ID = EXAM_PAPERS.id
                  left join exam_question_bank on EXAM_QUESTION_PAPER.EXAM_QUESTION_BANK_ID = exam_question_bank.id
                  where EXAM_PAPERS_ID=$exam_id order by id desc" ;
        // echo $sqlexam;exit;
        $result_examquestion = DB::select($sqlexam);

        $course_name = DB::selectOne("select get_course_name($co_id) as value from dual");
        $course_name=$course_name->value;
        $sqlexamquestion="select count(*) as question_count from EXAM_QUESTION_PAPER where EXAM_PAPERS_ID=$exam_id";
        $examquestion = DB::select($sqlexamquestion);
        $sqlexampaper="select total_questions from EXAM_PAPERS where ID=$exam_id";
        $result_sqlexampaper = DB::select($sqlexampaper);

        $total_question= $result_sqlexampaper[0]->total_questions;
        $exam_total_ques= $examquestion[0]->question_count;
      }
      return view('grader.exam.exam_marks_management',['exam_questinbank'=>$result_examquestion,'course_name'=> $course_name,'exam_total_ques'=>$exam_total_ques,'total_question'=>$total_question,'co_id'=>$co_id_encrpt,'course_id'=>$course_id]);
    }

    ///////////marks management update marks //////////////////
    public function marks_update(Request $request,$id,$marks,$neg,$paper_id){

      //dd($id);
      $sql = "select  * from exam_papers where id = $paper_id";
      $result_sqlr = DB::select($sql);
      $negative_marks= $result_sqlr[0]->negative_marks;
     $negat_marks = ($marks/100)*$neg;
  //   echo $negat_marks;exit;

      if($negative_marks == 0){
        $result=DB::table('exam_question_paper')
            ->where('id', $id)
            ->update([
              'question_marks' => $marks,
              'neg_marks' => $neg,
              'total_with_neg' =>  $negat_marks


            ]);

          $data = array('status' => true, 'message' => 'Marks Update Successfully');
                     return response()->json($data);

            }else{

              $result=DB::table('exam_question_paper')
              ->where('id', $id)
              ->update([
                'question_marks' => $marks


              ]);

              return 2;
            }
      }

//////////Time Management view /////////////////////

public function time_management($co_id,Request $request){
  $exam_id=$request->get('id');
  //dd($exam_id);EXAM_PAPERS
 $sqlexam="select question,title,question_marks,EXAM_QUESTION_PAPER.id,
exam_question_time,neg_marks from EXAM_QUESTION_PAPER
                left join EXAM_PAPERS on EXAM_QUESTION_PAPER.EXAM_PAPERS_ID = EXAM_PAPERS.id
                left join exam_question_bank on EXAM_QUESTION_PAPER.EXAM_QUESTION_BANK_ID = exam_question_bank.id
                where EXAM_PAPERS_ID=$exam_id order by id desc" ;

    $result_examquestion = DB::select($sqlexam);

$course_name = DB::selectOne("select get_course_name($co_id) as value from dual");
     $course_name=$course_name->value;


$sqlexamquestion="select count(*) as question_count from EXAM_QUESTION_PAPER where EXAM_PAPERS_ID=$exam_id";
    $examquestion = DB::select($sqlexamquestion);
    $sqlexampaper="select total_questions from EXAM_PAPERS where ID=$exam_id";
    $result_sqlexampaper = DB::select($sqlexampaper);

     $total_question= $result_sqlexampaper[0]->total_questions;
    $exam_total_ques= $examquestion[0]->question_count;


    return view('grader.exam.time_management',['exam_questinbank'=>$result_examquestion,'course_name'=> $course_name,'exam_total_ques'=>$exam_total_ques,'total_question'=>$total_question]);
}

//////////update time management ///////////

public function time_exam_update($id,$marks){

 // echo $neg;exit;
    $result=DB::table('exam_question_paper')
        ->where('id', $id)
        ->update([
          'exam_question_time' => $marks
          ]);




      $data = array('status' => true, 'message' => 'Time Update Successfully');
                 return response()->json($data);
  }

///// delete question in question bank ////////////

public function delete_question($id)
{

		$result=DB::table('exam_question_bank')->where('id', '=', $id)->delete();
		if($result==1){

         $question = DB::table('EXAM_QUESTION_PAPER')->where('EXAM_QUESTION_BANK_ID', '=', $id)->first();


       if($question!=''){

        $examid=$question->id ;

            $delete_exam=DB::table('EXAM_QUESTION_PAPER')->where('id', '=', $examid)->delete();
       }

			$data = array('status' => true, 'message' => 'Question Delete Successfully');
		                 return response()->json($data);
		}

}


///// delete exam question in exam bank ////////////

public function delete_examquestion($id)
{
    $question = DB::table('EXAM_QUESTION_PAPER')->where('id', '=', $id)->first();
    //print_r($question);
     $question_id=$question->exam_question_bank_id ;
    //dd($question);
    $result=DB::table('EXAM_QUESTION_PAPER')->where('id', '=', $id)->delete();
       if($result==1){
          ///// update status question bank /////

                    $sqlupdatecontent = "UPDATE exam_question_bank
                                      SET EXAM_STATUS = 0
                                      WHERE  id=$question_id";

                $result = DB::insert($sqlupdatecontent);

        ///// update status /////
      $data = array('status' => true, 'message' => 'Question Delete Successfully');
                     return response()->json($data);
    }

}
public function exam_section($id,$quizid){


  $result=DB::table('exam_question_paper')
  ->where('id', $quizid)
  ->update([
    'exam_section_id' => $id
    ]);




}
}
