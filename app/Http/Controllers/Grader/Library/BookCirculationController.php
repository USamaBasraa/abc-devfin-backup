<?php
namespace App\Http\Controllers\Grader\Library;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Table;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\sessions;
use App\Models\Grader\Schools;
use App\Models\Grader\BookItems;
use App\Models\Grader\LibraryCirculation;
use App\Models\Grader\LibraryCriteria;
use App\Models\Grader\BookCirculation;
use App\Models\Departments;
use App\Models\FacultyCourses;
use App\Models\EmpEmployees;
use Symfony\Component\HttpFoundation\Response;
use \Milon\Barcode\DNS1D;

class BookCirculationController extends Controller{

public function view_book(){

    // $res_books = DB::table('LIBRARY_CIRCULATION')
    // ->leftJoin('library_books', 'library_accession_number', '=', 'lc_accession_no')
    // ->select( 'LIBRARY_CIRCULATION.id as id,')
    // ->get();

$sel="SELECT LIBRARY_CIRCULATION.id as id,lc_due_date ,lc_fine_status,lc_desc,lc_fine_total,lc_return_date ,LIBRARY_CIRCULATION.lc_accession_no as lc_accession_no ,lc_issue_no_of_days,lc_issue_date ,lc_book_status ,sai_enrollment_no ,library_title ,library_books_id
       FROM LIBRARY_CIRCULATION
       left join library_books on library_books_id = library_books.id
       LEFT JOIN library_accession_number ON library_accession_number.lan_id = library_books.id";
$res_books = DB::select($sel);
//echo $sel;exit;

return view('grader.library.circulation.return_book',compact('res_books'));

    dd($res_books);
}
public function circulation_book_history(){
    $sel="SELECT
    library_circulation.id AS id,
    lc_due_date,
    lc_reminder,
    lc_fine_status,
    lc_desc,
    lc_fine_total,
    lc_return_date,
    library_circulation.lc_accession_no AS lc_accession_no,
    lc_issue_no_of_days,
    lc_issue_date,
    lc_book_status,
    library_circulation.sai_enrollment_no,
    library_title,
    library_books_id,
    reminder2,
    spi_email,
    sai_sdpo_sess_id
    FROM
        library_circulation
        LEFT JOIN library_books ON library_books_id = library_books.id
        LEFT JOIN library_accession_number ON library_accession_number.lan_id = library_books.id
        left   join students_apply_info on lower(students_apply_info.sai_enrollment_no) = lower(library_circulation.sai_enrollment_no)
        left  join students_personal_info on sai_spi_id = spi_id
    ORDER BY lc_issue_date DESC";
        //echo $sel;exit;
    $res_books = DB::select($sel);

    return view('grader.library.circulation.circulation_history',compact('res_books'));
}
public function overdue_book(){
    $sel="SELECT
    library_circulation.id AS id,
    SYSDATE,
    sisusers.email as spi_email,
    lc_due_date,
    lc_reminder,
    lc_fine_status,
    lc_desc,
    lc_fine_total,
    lc_return_date,
    library_circulation.lc_accession_no AS lc_accession_no,
    lc_issue_no_of_days,
    lc_issue_date,
    lc_book_status,
    library_circulation.sai_enrollment_no,
    library_title,
    library_books_id,
    reminder2,
    -- spi_email,
    sai_sdpo_sess_id
    FROM
        library_circulation
        LEFT JOIN library_books ON library_books_id = library_books.id
        LEFT JOIN library_accession_number ON library_accession_number.lan_id = library_books.id
        left   join students_apply_info on lower(students_apply_info.sai_enrollment_no) = lower(library_circulation.sai_enrollment_no)
        left join sisusers on sisusers.username = library_circulation.sai_enrollment_no
    WHERE
        lc_due_date < SYSDATE
    AND
        lc_book_status = 'Issued'
    ORDER BY lc_issue_date DESC";
    //echo $sel;exit;
    $res_books = DB::select($sel);

    return view('grader.library.circulation.overdue_book',compact('res_books'));
}
public function library_fine_slip(Request $request){

   // echo $request->input('id');exit();
    $sql = "SELECT
            library_circulation.sai_enrollment_no,
            library_books.library_title,
            library_circulation.lc_fine_total
        FROM
            library_circulation
            LEFT JOIN library_books ON library_books.id = library_circulation.library_books_id
        WHERE
            library_circulation.id = ".$request->input('id');

    //return $sql;exit();

    $data = DB::select($sql);
    return view('library.library_fine_slip',compact('data'));

}


function getResultuser(Request $request,$slipID){

   
    //$slipID=$request->get('slipID');

    $sql = "SELECT library_author_name,lc_reminder,library_author_mark,library_ddc_number,LIBRARY_CIRCULATION.id as id,lc_due_date ,lc_return_date ,
    LIBRARY_CIRCULATION.lc_accession_no as lc_accession_no ,lc_issue_no_of_days,lc_issue_date ,lc_book_status ,lc_fine_total,sai_enrollment_no ,
    library_title ,library_books_id,lc_total_late_days,lc_fine_status,lc_desc
    FROM LIBRARY_CIRCULATION
    left join library_books on library_books_id = library_books.id
    LEFT JOIN library_accession_number ON library_accession_number.book_id = library_books.id
    where LIBRARY_CIRCULATION.sai_enrollment_no = '$slipID'";
   // echo  $sql ;exit;
   $view_books = DB::select($sql);
   return response()->json(['data' => $view_books, 'status'=> true, 'message' => count($view_books).' record(s) found!']);
}
function getResult(Request $request,$slipID){

   
    //$slipID=$request->get('slipID');

    $sql = "SELECT library_author_name,lc_reminder,lc_fine_status,lc_desc,library_author_mark,library_ddc_number,LIBRARY_CIRCULATION.id as id,lc_due_date ,lc_return_date ,LIBRARY_CIRCULATION.lc_accession_no as lc_accession_no ,lc_issue_no_of_days,lc_issue_date ,lc_book_status ,lc_fine_total,sai_enrollment_no ,library_title ,library_books_id,lc_total_late_days
    FROM LIBRARY_CIRCULATION
    left join library_books on library_books_id = library_books.id
    LEFT JOIN library_accession_number ON library_accession_number.book_id = library_books.id
    where LIBRARY_CIRCULATION.id = '$slipID'";
    //echo  $sql ;
   $view_books = DB::select($sql);
   return response()->json(['data' => $view_books, 'status'=> true, 'message' => count($view_books).' record(s) found!']);
}

function printFinePDF(Request $request){
    $sql = "SELECT
    library_circulation.sai_enrollment_no,library_circulation.id,
    library_books.library_title,
    library_circulation.lc_fine_total
FROM
    library_circulation
    LEFT JOIN library_books ON library_books.id = library_circulation.library_books_id
WHERE
    library_circulation.id = ".$request->input('id');

//echo $sql;exit();


    $data = DB::select($sql);
    if(count($data)){
        return view('library.circulation.fine_pdf',compact('data'));
    }else{
        return '<h1>No Record Found !</h1>';
    }   
}


function circulation_dashboard(){
    return view('grader.library.circulation.circulation_dashboard');
}


public function SendMail(Request $request){

        //dd($request->all());
        $enrollment=$request->get('user-id');
        $activeSess=$request->get('act_sess_id');
        $spiEmail=$request->get('spiEmail');
        if($activeSess > 140){
            // email of students whose bnu email is in working mode
            $to=strtolower($enrollment).'@bnu.edu.pk';
        }else{
            // personal email of old students whose bnu email was not that time
            $to=$spiEmail;
        }
        $issueDate=$request->get('issue-date');
        $reminder =$request->get('reminder');
        $id=$request->get('id');
        $reminderNo=$request->get('reminder-no');
        $startDate = date("Y-m-d");
        $timestampStartDateTime = strtotime($startDate);

         $new_StartDateTime = date("d-M-y", $timestampStartDateTime);
        
        date_default_timezone_set("Asia/Karachi");
       
        $time = array($new_StartDateTime);
        $json = json_encode($time);
        // print_r($time);
        // exit;


      $reminderRes =   DB::table('library_circulation')->where('id',$id)->select('reminder2')->first();
      //  print_r($reminderRes );exit;
      $reminderArr = json_decode($reminderRes->reminder2);
     // $reminderArr[] = array(); //  date('d-m-Y');
      //echo sizeof($reminderArr);
     // exit;

      if($reminderRes->reminder2 == ''){  
        $reminderArr[] = date('d-m-Y');
      }else{
     
        $reminderArr = json_decode($reminderRes->reminder2);
        $reminderArr[] = date('d-m-Y');
      }

      $reminderJson = json_encode($reminderArr);



        $sel="update library_circulation set reminder2='$reminderJson' where id=$id and sai_enrollment_no='$enrollment' ";
  //  echo  $sel;exit;
         $view_criteria = DB::insert($sel);

         //exit;


 // echo $sel;exit;
        try {
            $procedurenotification = 'send_mail_one_by_one';
            $bindings_notification = [
                'to'       => $to,
                'subject'  => 'Over Due Book Reminder',
                'message'  => $reminder,
                'display'  => 'BNU Library Reminder',

            ];
            // print_r( $bindings_notification);exit();
            $result_notification = DB::executeProcedure($procedurenotification, $bindings_notification);
        } catch (Exception $e) {

            //This calls the render() method of `App\Exceptions\Handler`
        }
        return response()->json(array('status' => true,'data'=> [], 'message' => 'Reminder Send to User Successfully'));



}


public function fineBookUpdate(Request $request){


    $fine=$request->get('fine');
    $fineStatus=$request->get('fine-status');
    $remarks=$request->get('remarks');
    $user_id=$request->get('user-id');
    $id=$request->get('id');
    $sel="update library_circulation set lc_fine_total=$fine,lc_desc='$remarks',lc_fine_status='$fineStatus' where id=$id and sai_enrollment_no='$user_id' ";
  //echo  $sel ;exit;
     $view_criteria = DB::insert($sel);
     return response()->json(array('status' => true,'data'=> [], 'message' => 'Book Fine Update Successfully'));


}
public function return_book(Request $request){


    $Book=new BookCirculation;
    $checkcriteria=$Book->Book_criteria($request->get('user-id'));
    $Bookissue=$Book->Book_issue($request->get('user-id'));

    $bookStatus=$request->get('book-status');
    $user_id=$request->get('user-id');
    $id=$request->get('id');
    $fine=$request->get('fine');
    $remarks=$request->get('remarks');
    $returnDate=$request->get('return-date');
    $date=$request->get('issue-date');
    $IssueDays=$request->get('issue-days');

    $sess = new sessions();
    $sess->startSession();
    $login_id = $sess->emp_user_id;
    $days=$fine/100;
   // dd($request->all());
//echo $bookStatus."===".$date;exit;
  if($bookStatus =='Issued'){
    
    if($checkcriteria->lbc_limit > $Bookissue){
        // echo "1234";
        // exit;
       $sel="update library_circulation set lc_return_date='$returnDate', lc_book_status='Received',lc_fine_total=$fine,lc_desc='$remarks',lc_total_days=$IssueDays where id=$id and sai_enrollment_no='$user_id' ";
        $view_criteria = DB::insert($sel);
    //   echo $sel;exit;
        $sel="select lc_total_days from library_circulation  where id=$id and sai_enrollment_no='$user_id' ";
        $sql = DB::select($sel);
        $total_days=$sql[0]->lc_total_days+$IssueDays;

       // dd( $total_days);
        if($sql[0]->lc_total_days <= $checkcriteria->lbc_reissuance_policy){
            $circulation=new libraryCirculation;
            $due_date = date('Y-m-d', strtotime($date . '+'.$checkcriteria->lbc_duration_issuance.'days'));
            $insertArr = array();
            $insertArr['SAI_ENROLLMENT_NO']      = $request->get('user-id');
            $insertArr['LIBRARY_BOOKS_ID']       = $request->get('book-id');
            $insertArr['LC_ACCESSION_NO']        = $request->get('accession-no');
            $insertArr['LC_ISSUE_DATE']          = $date;
            $insertArr['LC_ISSUE_NO_OF_DAYS']    = $checkcriteria->lbc_duration_issuance;
            $insertArr['LC_BOOK_STATUS']         = 'Issued';
            $insertArr['LC_TOTAL_DAYS']          = $total_days;
            $insertArr['ENTRY_USER_ID']          = $login_id ;
            $insertArr['LC_DUE_DATE']            = $due_date;
            $insert_issue_status  = $circulation->create($insertArr)->id;
            if($insert_issue_status){

                 return response()->json(array('status' => true,'data'=> [], 'message' => 'Book Issued Successfully'));
               }


        }else{
            return response()->json(array('status' => false,'data'=> [], 'message' => 'Book ReIssue Policy Exceeded'));

        }

        }else{
            return response()->json(array('status' => false,'data'=> [], 'message' => 'Book Issue Limit Exceeded'));
        }
    }elseif($bookStatus =='Reissued'){
        $selreissue="update library_circulation set lc_book_status='Received',lc_fine_total='$fine',lc_desc='$remarks',lc_total_late_days='$days' where id=$id and sai_enrollment_no='$user_id' ";
      //  echo $selreissue;exit;
        $view_selreissue = DB::insert($selreissue);

        $selInsertreissue="INSERT
        INTO  LIBRARY_CIRCULATION 
          (
             SAI_ENROLLMENT_NO ,
             LIBRARY_BOOKS_ID ,
             LC_ACCESSION_NO ,
             LC_ISSUE_DATE ,
             LC_ISSUE_NO_OF_DAYS ,
             LC_BOOK_STATUS ,
             ENTRY_USER_ID ,
             LC_DUE_DATE ,
             UPDATED_AT ,
             CREATED_AT 
          )
        SELECT SAI_ENROLLMENT_NO,
          LIBRARY_BOOKS_ID,
          LC_ACCESSION_NO,
          TO_DATE('$date', 'yyyy/mm/dd'),
          LC_ISSUE_NO_OF_DAYS,
          'Issued',
          ENTRY_USER_ID,
          LC_DUE_DATE,
          UPDATED_AT,CREATED_AT from LIBRARY_CIRCULATION where id = $id ";
      //echo $selInsertreissue;exit;
          $view_selInsertreissue = DB::insert($selInsertreissue);
          return response()->json(array('status' => true,'data'=> [], 'message' => 'Book Reissued Successfully'));

    }else{
        //dd($request->all());
        $Book=new BookCirculation;
        $checkcriteria=$Book->Book_criteria($request->get('user-id'));
        $r_date = $request->get('r_date');
        $checkcriteria->lbc_duration_issuance;
        $sel="update library_circulation set lc_book_status='$bookStatus',lc_fine_total='$fine',lc_desc='$remarks',lc_total_late_days='$days',lc_return_date=TO_DATE('$returnDate', 'yyyy/mm/dd'),return_date=TO_TIMESTAMP('".$r_date."','DD/MM/YYYY HH24:MI:SS') where id=$id and sai_enrollment_no='$user_id' ";
           // echo $sel;exit;
        $view_criteria = DB::insert($sel);
        return response()->json(array('status' => true,'data'=> [], 'message' => 'Book Received Successfully'));

    }





    } // -------------

    function searchUser(Request $request){
        $query = $request->get('query');
        return  DB::table('sisusers')
                ->whereRaw("full_name || username like '%$query%' ")
                ->paginate(15);

               //  print_r($orders); exit;

        return response()->json(array('status' => true,'data'=> $users, 'message' => '' ));
    }
    function updateStatus($status,$id){

        $update_library_status = DB::table('SISUSERS') ->where('id', $id)->update([
            'LIBRARY_STATUS' => $status           
        ]);
        if($update_library_status){
            return response()->json(array('status' => true,'data'=> [], 'message' => 'Status Updated Successfully',
              'action' => [ 'type' => 'redirect', 'url' => 'abc' ]));
          }  
    }

    function viewauthordetail(Request $request){
        $author = $request->get('author');
//echo $author;exit;
       
        
     $author_books = DB::table('LIBRARY_BOOKS')
                    ->leftJoin('library_accession_number', 'library_books.id', '=', 'library_accession_number.book_id')
                    ->leftjoin('library_circulation','library_books_id','=','LIBRARY_BOOKS.id')
                    ->where('LIBRARY_AUTHOR_NAME', $author)
                   ->get(); 
                   // ->toSql();

       // return view('library.author_detail',compact('author_books'));
        return view('grader.library.author_detail',['author_books'=>$author_books]);
        

    }

    function inboxlog(){

        $inboxSQL = DB::table('LIBRARY_CIRCULATION')
        ->leftJoin('LIBRARY_BOOKS', 'LIBRARY_CIRCULATION.library_books_id', '=', 'library_books.id')
        ->leftJoin('library_accession_number', 'library_books.id', '=', 'library_accession_number.book_id')
       
        ->whereNotNull('reminder2')
       // ->where('reminder2', $author)
       ->get(); 
        return view('grader.library.circulation.inbox',['inbox'=>$inboxSQL]);

    }

    function viewemployee(){
        $sel="select username  from sisusers ";
        $sql = DB::select($sel);
       return response()->json(array('status' => true,'data'=> $sql, 'message' => 'Item Updated Successfully',
       'action' => [ 'type' => 'redirect', 'url' => 'abc' ]));

    }



}
