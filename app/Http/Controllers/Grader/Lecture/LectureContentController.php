<?php
namespace App\Http\Controllers\Grader\Lecture;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

use Illuminate\Database\QueryException;


/*
*   Manage Lecture Contents (e.g Add Content, Update Content, Get Content)
*   Create By Ahmed For Garder 9
*/
class LectureContentController extends Controller{


      /************
    **  Its create new Content. And update Course Offering 
    **  Lecture Content
    **  Create  Content
    **  Ahmed Move it from FacultyCourseContent 
    ************/
    function addLectureContent(Request $request){
//echo '1230';exit;
      $tt_id          = $request->get('tt_id');
      $add_new_desc   = $request->get('desc');
      $mtt_co_id      = $request->get('mtt_co_id');

      $sqlnewlecturecontent       =   "select count(*)+1 as total from course_offering_contents where coc_content_type = 2";
      $ressqlnewlecturecontent    =   DB::select($sqlnewlecturecontent);
      $total_count                =   $ressqlnewlecturecontent[0]->total;

    //   DB::table('course_offering_contents')->where([
    //       'coc_continuation' => '1',
    //       'coc_co_id' => $mtt_co_id
    //   ])->whereNull('coc_covered')->whereNotNull('coc_continuation')->update([
    //       'coc_covered' => 1,
    //       'coc_continuation' => 1
    //   ]);

    $sqlcontinuation = "update COURSE_OFFERING_CONTENTS
    set coc_covered = 1,coc_continuation = 1
    where coc_co_id = '$mtt_co_id'
    and coc_continuation = 1
    and coc_covered is null
    and coc_continuation is not null";
   // echo $sqlcontinuation;exit;
    $res2 = DB::update($sqlcontinuation);


    //   print_r([
    //     'coc_details' => $add_new_desc,
    //     'coc_tt_id' => $tt_id,
    //     'coc_co_id' => $mtt_co_id,
    //     'coc_content_type' => 2,
    //     'coc_course_content_id' => $total_count,
    //     'coc_continuation' => 1
    // ]);

    // exit;

      

    //   DB::table('course_offering_contents')->insert([
    //       'coc_details' => $add_new_desc,
    //       'coc_tt_id' => $tt_id,
    //       'coc_co_id' => $mtt_co_id,
    //       'coc_content_type' => 2,
    //       'coc_course_content_id' => $total_count,
    //       'coc_continuation' => 1
    //   ]);

		$disallow_words = array("'",':',';','\\','/','"','','*','delete','drop','sleep','-','table','where','=','truncate','alter','update','alter table');
			$add_new_desc = str_replace($disallow_words,'',$add_new_desc);

			try {
			 
			  DB::table('course_offering_contents')->insert([
				'coc_details' => $add_new_desc,
				'coc_tt_id' => $tt_id,
				'coc_co_id' => $mtt_co_id,
				'coc_content_type' => 2,
				'coc_course_content_id' => $total_count,
				'coc_continuation' => 1
			  ]);
		  }catch(\Illuminate\Database\QueryException $ex){ 
			//dd($ex->getMessage());
			   if ($ex->getCode() == 1400) {
				return response()->json(['status' => false, 'message' => 'Record cannot insert NULL !'])->setStatusCode(400,'Record cannot insert NULL!');
			   }
		  }

      return response()->json(['status' => true, 'data' => [], 'message' => 'Updated Successfully!' ]);
     }// fun end 


     
    
      /************
    **  Its update the existing Content. Or concate with old content. 
    **  Lecture Content
    **  Update  Content
    **  Ahmed Move it from FacultyCourseContent 
    ************/
    function updateLectureContent(Request $request){
      //   echo "its update "; exit;


      $getcontent = $request->get('content_title');
      $id = $request->get('cc_id');
      

      $id = $request->get('cc_id');

      if($id == ''){
        $id = $request->get('lecture_num');
      }

      $coc_content_type = $request->get('coc_content_type');
      $mtt_co_id = $request->get('mtt_co_id');
      $desc = $request->get('desc');
      $tt_id            = $request->get('tt_id');
      $description=$getcontent.' '.$desc;

    //   $tt_id            = $request->get('tt_id');
    //   $getcontent       = $request->get('getcontent');
    //   $id               = $request->get('lecture_num');
    //   $coc_content_type = $request->get('coc_content_type');
    //   $mtt_co_id        = $request->get('co_id');
    //   $desc             = $request->get('desc');

    //   $description      = $getcontent.' '.$desc;



//       print_r([
//         'coc_details' => $description,
//         'coc_tt_id' => $tt_id,
//         'coc_co_id' => $mtt_co_id,
//         'coc_content_type' => $coc_content_type,
//         'coc_content_entry_type' => 2,
//         'coc_course_content_id' => $id,
//         'coc_continuation' => 1
//     ]);

// exit;

// echo $mtt_co_id; exit; 

    // DB::table('course_offering_contents')->where([
    //     'coc_continuation' => '1',
    //     'coc_co_id' => $mtt_co_id
    // ])->whereNull('coc_covered')->whereNotNull('coc_continuation')->update([
    //     'coc_covered' => 1,
    //     'coc_continuation' => 1
    // ]);

    // DB::table('course_offering_contents')->where([
    //    //  'coc_continuation' => '1',
    //     'coc_co_id' => $mtt_co_id,
    //     'coc_course_content_id' => $id,
    //     'coc_content_type' => $coc_content_type
    // ])->whereNull('coc_covered')->whereNotNull('coc_continuation')->update([
    //     'coc_covered' => null,
    //     'coc_continuation' => 1
    // ]);
                        
 
    $sqlcontinuation = "update COURSE_OFFERING_CONTENTS
    set coc_covered = 1,coc_continuation = 1
    where coc_co_id = '$mtt_co_id'
    and coc_continuation = 1
    and coc_covered is null
    and coc_continuation is not null";
    $res2 = DB::update($sqlcontinuation);


    $sql_cc_id = "update COURSE_OFFERING_CONTENTS
    set coc_covered =null,coc_continuation = 1
    where coc_co_id = '$mtt_co_id'
    and COC_COURSE_CONTENT_ID=".$id."
    and coc_content_type=".$coc_content_type;
    $res_cc_id = DB::update($sql_cc_id);
			$disallow_words = array("'",':',';','\\','/','"','','*','delete','drop','sleep','-','table','where','=','truncate','alter','update','alter table');
			$description = str_replace($disallow_words,'',$description);

    try {
     
        DB::table('course_offering_contents')->insert([
          'coc_details' => $description,
          'coc_tt_id' => $tt_id,
          'coc_co_id' => $mtt_co_id,
          'coc_continuation' => 1,
          'coc_course_content_id' => $id,
          'coc_content_type' => $coc_content_type,
          'coc_content_entry_type' => 1
        ]);
    }catch(\Illuminate\Database\QueryException $ex){ 
      // $ex->getMessage()
         if ($ex->getCode() == 1) {
          return response()->json(['status' => false, 'message' => 'Already added!'])->setStatusCode(400,'Already added!');
         }
    }
    return response()->json(['status' => true, 'message' => 'Insert & Updated Successfully!']);
  } // fun end 

}