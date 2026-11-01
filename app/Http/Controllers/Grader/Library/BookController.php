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
use Carbon\Carbon;
class BookController extends Controller{



    //////----------------- NEW WORK

function getBook($id){
        $sql = "SELECT
        (SELECT COUNT(*)
                                    FROM library_circulation
                                    WHERE library_books_id = library_books.id
                                    and lc_book_status = 'Issued'
                                    ) AS issued,
                                    (SELECT lc_due_date
                                    FROM library_circulation
                                    WHERE library_books_id = library_books.id
                                    and lc_book_status = 'Issued'
                                    ) AS due_date,
                                    library_accession_number.accession_no,
        library_books.*
    FROM
        library_books
    left join library_accession_number on library_accession_number.book_id = library_books.id    
    WHERE
        id = $id";
        return $res = DB::select($sql);
        
        
       
    }

    function allocation_new(Request $request){
        $roll_no = $request->get('roll_no');
        return view('grader.library.allocation.allocation',['roll_no'=>$roll_no]);
    }

function deleterows($id){
        
        $sql = "select count(*) as count from library_circulation where library_books_id = $id and lc_book_status = 'Issued'";
        $res = DB::select($sql);
        //echo $res[0]->count;exit;
        if($res[0]->count == '0'){
            $del = "delete from library_books where id = $id";
            $row_del = DB::delete($del);
            echo '1';
        }
    }



    function addBook(){
      
       
         return view('library.add-book');
    }

    function pro_add_book(Request $request){
        // pro_add_book
      //  return view('library.pro_add_book');




        
        $id = $request->get('id');
        $view_books = DB::table('LIBRARY_BOOKS')       
                            ->where('id', '=', $id)       
                            ->get();   

        $schools =  Schools::all();
        return view('library.pro_add_book',['schools'=>$schools,'view_library'=>$view_books,'library_id'=>$id]);
    }
    // Bilal Saleem Works start
    function addBookItem(Request $request){
        //  /echo "abc";exit;
        $id = $request->get('id');
        $view_books = DB::table('LIBRARY_BOOKS')       
                            ->where('id', '=', $id)       
                            ->get();   
                           
        $library_accession_number = DB::table('LIBRARY_ACCESSION_NUMBER')       
        ->where('book_id', '=', $id)       
        ->get();   

        $schools = DB::table('SCHOOLS')       
        ->whereRaw("school_id not in (8,9,10,11)")       
        ->get();   

       
       // $schools =  Schools::all();
        return view('grader.library.add-book-item',['schools'=>$schools,'view_library'=>$view_books,'library_id'=>$id,'library_accession_number'=>$library_accession_number]);
   }
   function InsertBookItem(Request $request){
    $sess = new sessions(); 
    $sess->startSession();  
    $array_access = $request->get('access_no');
    $count_array =  ($array_access)?count($array_access):[];
    $login_id = $sess->emp_user_id;  

    $validator = Validator::make(
        $request->all(),[
          
            'ddc' => 'required',
            'author_mark' => 'required',
            'item_title' => 'required',
            'publisher' => 'required',
            'source' => 'required',
            'subject' => 'required'
            
        ],[
           
            'ddc.required' => 'DDC Number is missing'   ,
            'author_mark.required' => 'Author Mark is missing'   ,
            'item_title.required' => 'Item Title is missing'   ,
            'publisher.required' => 'Publisher is missing'   ,
            'source.required' => 'Source is missing'   ,
            'subject.required' => 'Subject is missing'   
        ]);
        
    if ($validator->fails()) {
        return response()->json(array('status' => false, 'data' => [], 'message' => $validator->errors()->first()));
       exit;
    }
        $file = $request->file('attachment');     
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $file = $request->file('attachment');
            $file_name = $file->getClientOriginalName();
            // $file_name = $user_id.$file->getClientOriginalExtension();
            //dd($file_name);
            $file->move(public_path() . '/library_images/', $file_name);
        }else {
            $remoteImageURL = $request->get('remoteIamge');
            $file_name      = 'No File';
            if($remoteImageURL != ''){
                    $file_name      = "lib_".rand(99,99999).'.gif';
                    $willImage      = public_path() . '/library_images/'.$file_name;
                    $ch = curl_init($remoteImageURL);
                    $fp = fopen( $willImage, 'wb');
                    curl_setopt($ch, CURLOPT_FILE, $fp);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_exec($ch);
                    curl_close($ch);
                    fclose($fp);                    
            }else{
                $file_name = '';
            }
        }       

        $file = $request->file('e_book');     
        if ($request->hasFile('e_book')) {
            $file = $request->file('e_book');
            $file = $request->file('e_book');
            $file_name_eBook = $file->getClientOriginalName();
           
        $file->move(public_path() . '/library_images/', $file_name_eBook);
        }else {
            $file_name_eBook = '';
        }   
      
        $library_data=new BookItems;
        $formdata=$this->dataset($request->all());
        $formdata = array_merge($formdata, ['LIBRARY_ATTACHMENT' => $file_name,'LIBRAY_EBOOK' => $file_name_eBook,'LIBRARY_ACCESSION_NUMBER' => $count_array,'LIBRARY_ENTRY_USER' => $login_id,'BOOK_STATUS' => 'Pending']);
        
        //print_r($formdata);exit;
        $insert_book_items  = $library_data->create($formdata)->id;       
        //  print_r($array_access);
        foreach($array_access as $row){
        $insert_library_accession = DB::table('LIBRARY_ACCESSION_NUMBER')->insert([
           
            'BOOK_ID' => $insert_book_items,
            'ACCESSION_NO' => $row
        ]);
        }

        if($insert_library_accession){           
             return response()->json(array('status' => true,'data'=> [], 'message' => 'Item Successfully Entered',
               'action' => [ 'type' => 'redirect', 'url' => 'abc' ]));
           }    

   }  

   function viewBookItem(){        

    $view_books = DB::table('LIBRARY_BOOKS')
    ->leftJoin('emp_employees', 'library_entry_user', '=', 'employee_id')
    ->leftJoin('library_accession_number', 'library_books.id', '=', 'library_accession_number.book_id')
    ->where('LIBRARY_BOOKS.book_status', 'Pending')
    ->orderByRaw('library_books.id DESC')
    ->Paginate(50);
  //   echo $sql;exit;
    return view('grader.library.view-book',['viewBooks'=>$view_books, 'pagination' => $view_books->links()]);

   }
   function catalog(){   
      
    $view_books = DB::table('LIBRARY_BOOKS')
                        ->leftJoin('library_accession_number', 'library_books.id', '=', 'library_accession_number.book_id')
                        ->Paginate(50);
    dd($view_books);
    return view('library.catalog',['viewBooks'=>$view_books]);

   }

 
   function student_details(){
    $query = "SELECT sisusers.id ,
                            username, 
                            email ,
                            group_id ,
                      --      employee_id,
                            emp_f_name,
                            emp_l_name ,
                            prefix, emp_f_name, emp_l_name emp_full_name,
                            emp_email ,
                            imagename emp_photo ,
                            emp_school_id ,
                            emp_sch.school_name AS emp_school_name,
                            emp_dept_id ,
                            emp_dept.dept_name AS emp_dept_name,
                            emp_type,
                            sai_spi_id,
                            COALESCE(sai_spi_id,0) AS spi_id ,
                            spi_enroll_prefix ,
                            spi_enroll_id ,
                            spi_name,
                            spi_imagename,
                            spi_fathername,
                            spi_presentaddress,
                            designation,
                            spi_sess_id,
                            sai_sdpo_sess_id   AS SPI_ENROLLMENT_SESSION,
                            sai_sdpo_school_id,
                            sai_cgpa,
                            sai_sdpo_dept_id   AS spi_dept_id ,
                            sai_sdpo_prog_id   AS spi_prog_id ,
                            sai_sdpo_shift_id,
                            sai_sdpo_shift_id AS spi_shift_id ,
                            sai_semester      AS sai_active_semester,
                            sai_enrollment_no AS SPI_ENROLLMENT_NO ,
                            sai_student_status,
                            spi_mobile,
                            spi_email,
                            (SELECT MAX(sess_id)
                            FROM academic_sessions
                            WHERE sess_admission_active='Yes'
                            ) spi_active_sess_id,
                            sai_id,
                            (SELECT max(gpm_active_sessl_id) as gpm_active_sessl_id
                            FROM group_pages_management
                            WHERE gpm_school_id = sai_sdpo_school_id
                            ) AS active_enroll_sess,
                            api_token                                 
                          FROM sisusers
                          LEFT JOIN emp_employees           ON LOWER(email)      = LOWER(emp_email)
                          LEFT JOIN schools emp_sch         ON emp_school_id     = emp_sch.school_id
                          LEFT JOIN departments emp_dept    ON emp_dept_id       = emp_dept.dept_id
                          LEFT JOIN students_apply_info     ON LOWER(username)   = LOWER(sai_enrollment_no)
                          LEFT JOIN students_personal_info  ON sai_spi_id        = spi_id
                                                           AND sai_student_status!='Transferred'
                      WHERE sai_enrollment_no = 'F2019-422' ";
      $result = DB::select($query);
      //print_r($result); exit;
      if(count($result) > 0){
          $row = $result[0];
          $data['id']             = $row->id;
          $data['user_full_name'] = null;
          $data['user_father_name']         = '';
          $data['username']       = $row->username;
          $data['email']          = $row->email;
          $data['group_id']       = $row->group_id;
          $data['student_status']       = $row->sai_student_status;
          
        //   $data['dashboard_set']  = $row->dashboard_set;
        //   if(trim($row->dashboard_set) == ''){
        //       $data['dashboard_set']  = '["user","noti","course-wise","today-timetable"]';
        //   }

          $data['user_type']      = ($row->group_id == 4)?'student':'staff';
          $data['image']          = null;
          $data['school']         = null;
          $data['school_id']      = null;
          $data['dept_id']        = null;
          $data['dept']           = null;
          $data['prog_id']        = null;
          $data['prog']       	  = null;
          $data['prog_degree_level']       	= null;
          $data['designation']              =  $row->designation;

          if(!($row->spi_name)){
            $data['user_full_name'] = $row->prefix.' '.$row->emp_f_name.' '.$row->emp_l_name;
            $data['image']          = "http://cmstest.bnu.edu.pk/staffimages/".strtoupper($row->username).".jpg";
            //      Route::post('get_image/{type}/{id}', 'ApiAuthController@get_user_image');
            //      $data['image']          = url('get_image/emp/'.$row->username);
            $data['school']         = $row->emp_school_name;
            $data['school_id']      = $row->emp_school_id;
            $data['dept']           = $row->emp_dept_name;
            $data['dept_id']        = $row->emp_dept_id;
          }else{
            // student side
            $data['user_full_name']             = $row->spi_name;
            $data['user_father_name']           = $row->spi_fathername;
            $data['image']                      = "http://stafftest.bnu.edu.pk/studentpics/".strtoupper($row->username).".jpg.png";
           // $data['image']          = url('get_image/student/'.$row->username);
            $data['school_id']      = $row->sai_sdpo_school_id;
            $data['enrollment_no']  = strtoupper($row->spi_enrollment_no);
            $data['dept_id']        = $row->spi_dept_id;
            $data['prog_id']        = $row->spi_prog_id;
            $data['sai_cgpa']        = $row->sai_cgpa;
            $data['api_token']        = $row->api_token;
            $data['spi_presentaddress']        = $row->spi_presentaddress;
            // SPI_ACTIVE_SESS_ID
            $data['active_sess_id']         =   $row->spi_active_sess_id;
            $data['mobile']                 =   $row->spi_mobile;
            $data['email']                  =   $row->spi_email;
            $sai_enrollment_no              =   ucfirst($row->spi_enrollment_no);
            $semester_no                    =   DB::selectOne("select get_semester_no('$sai_enrollment_no') as value from dual");
            $curr_semester                  =   explode("~",$semester_no->value);
            $data['current_session']        =   (isset($curr_semester[1]))?$curr_semester[1]:'Contact RO'; //      $curr_semester[1];
            $data['current_semester']       =   (isset($curr_semester[0]))?$curr_semester[0]:'Contact RO'; //$curr_semester[0]


          if($data['school_id']){
              $school_res = DB::select("SELECT school_name FROM schools where school_id = ".$data['school_id']);
              $data['school']         = (isset($school_res[0]->school_name))?$school_res[0]->school_name:'';
          }

          if($data['dept_id']){
              $dept_res 				= DB::select("SELECT dept_name FROM departments where dept_id = ".$data['dept_id']);
              $data['dept']           = (isset($dept_res[0]->dept_name))?$dept_res[0]->dept_name:'';
          }


          if($data['prog_id']){
              $prog_res = DB::select("SELECT prog_title, prog_degree_level FROM programs where prog_id = ".$data['prog_id']);
              if(count($prog_res) > 0){
                  $data['prog'] 				= $prog_res[0]->prog_title;
                  $data['prog_degree_level']  = $prog_res[0]->prog_degree_level;
              }
          }
          // if (@getimagesize($data['image'])) {
          // 	$data['img'] ="image exists";
          // } else {
          // 	$data['img'] ="image does not exist";
          // }
          }
        //  $data['image'] = url('public/assets/images/F2019-005.jpg'); //url('public/assets/images/user.png');
         // return $data;
      }else{
          return false;
      }
      return view('library.student_details');
}




   function viewBookSearchItem($keyword,$title,$author,$checkStatus){        

    $sql= " 1 = 1 and book_status='Pending' ";

    if($keyword == 'undefined' && $author == 'undefined' && $checkStatus == 'undefined' && $title != 'undefined'){      	
        $sql .= " AND LOWER(LIBRARY_BOOKS.library_title) like LOWER('%$title%')";    
    }elseif($title == 'undefined' && $author == 'undefined' && $checkStatus == 'undefined' && $keyword != 'undefined'){
        $sql .= " AND LOWER(LIBRARY_BOOKS.library_keyword) like LOWER('%$keyword%')";
    }elseif($title == 'undefined' && $keyword == 'undefined' && $author != 'undefined' && $checkStatus == 'undefined'){
        $sql .= " AND LOWER(LIBRARY_BOOKS.library_author_name) like LOWER('%$author%')"; 
    }elseif($title == 'undefined' && $keyword == 'undefined' && $author == 'undefined' && $checkStatus != 'undefined'){
        $sql .= " AND LIBRARY_BOOKS.book_status = '$checkStatus'";
    }

   
   return   $view_books = DB::table('LIBRARY_BOOKS')
        ->leftJoin('emp_employees', 'library_entry_user', '=', 'employee_id')
        ->leftJoin('library_accession_number', 'library_books.id', '=', 'library_accession_number.book_id')->whereRaw($sql)->get(); // ->Paginate(50);   

   }

   
   function viewBookSearchItemCatlog(Request $request){


            // dd($request->all());
            $search  = str_replace("'","", $request->get('search-input'));
            $title   = $request->get('searchTitle');
            $author   = $request->get('searchAuthorName');
            $accession   = $request->get('searchAccession');



            $searchBY  = $request->get('search_by');
            



            $sql= " 1 = 1 and book_status='Active' ";


            /*
            if($keyword == '' && $author == '' && $title != '' && $accession ==''){
                $sql .= " AND LOWER(LIBRARY_BOOKS.library_title) like LOWER('%$title%')";
            }elseif($title == '' && $author == ''  && $keyword != '' && $accession ==''){
                $sql .= " AND LOWER(LIBRARY_BOOKS.library_keyword) like LOWER('%$keyword%')";
            }elseif($title == '' && $keyword == '' && $author != '' && $accession =='' ){
                $sql .= " AND LOWER(LIBRARY_BOOKS.library_author_name) like LOWER('%$author%')";
            }elseif($title == '' && $keyword == '' && $author == '' && $accession !='' ){
                $sql .= " AND LOWER(library_accession_number.accession_no) like LOWER('%$accession%')";
            }
            */
        // REPLACE(recp_co_nm_clean,chr(39),'')
            if( $searchBY == 'keyword' ){
                $sql .= " AND LOWER(LIBRARY_BOOKS.library_keyword) like LOWER('%$search%')";
            }elseif( $searchBY == 'title' ){
                $sql .= " AND  REPLACE(LOWER(LIBRARY_BOOKS.library_title),chr(39),'') like LOWER('%$search%')";
            }elseif( $searchBY == 'author' ){
                $sql .= " AND LOWER(LIBRARY_BOOKS.library_author_name) like LOWER('%$search%')";
            }elseif( $searchBY == 'ddc' ){
                $sql .= " AND LOWER(LIBRARY_BOOKS.LIBRARY_DDC_NUMBER) like LOWER('%$search%')";
            }elseif( $searchBY == 'isbn' ){
                $sql .= " AND LOWER(LIBRARY_BOOKS.LIBRARY_NUMBER_ISSN) like LOWER('%$search%')";
            }elseif( $searchBY == 'acc_no' ){
                $sql .= " AND LOWER(library_accession_number.accession_no) = $search";
            }elseif( $searchBY == 'subject' ){
                $sql .= " AND LOWER(LIBRARY_BOOKS.LIBRARY_SUBJECT) like LOWER('%$search%')";
            }elseif( $searchBY == 'author' ){
                $sql .= " AND LOWER(LIBRARY_BOOKS.library_author_name) like LOWER('%$search%')";
            }elseif( $searchBY == 'school' ){
                $sql .= " AND LOWER(schools.school_name) like LOWER('%$search%')";
            }else{
                //  ->whereRaw("full_name || username like '%$query%' ")
                $sql .= " AND LOWER( LIBRARY_BOOKS.library_author_name ||
                LIBRARY_BOOKS.library_title ||
                LIBRARY_BOOKS.library_keyword ||
                LIBRARY_BOOKS.library_author_name ||
                LIBRARY_BOOKS.LIBRARY_SUBJECT ||
                LIBRARY_BOOKS.LIBRARY_NUMBER_ISSN
                ) like LOWER('%$search%')";
            }
            if($request->get('school')!=''){
                $school = $request->get('school');
                $sql .= " AND library_books.library_purchased_by = $school";
            }
            
            if(($request->get('date_from')!='') && ($request->get('date_to')!='')){
            // Both dates provided — filter between them
            $sql .= " AND TRUNC(LIBRARY_BOOKS.created_at) between TO_DATE ('".$request->get('date_from')."', 'yyyy-mm-dd') and TO_DATE ('".$request->get('date_to')."', 'yyyy-mm-dd')";
                } elseif($request->get('date_from')!='' && $request->get('date_to')==''){
                    // Only from-date — filter from that date to today
                    $sql .= " AND TRUNC(LIBRARY_BOOKS.created_at) >= TO_DATE ('".$request->get('date_from')."', 'yyyy-mm-dd')";
                } elseif($request->get('date_from')=='' && $request->get('date_to')!=''){
                    // Only to-date — filter everything up to that date
                    $sql .= " AND TRUNC(LIBRARY_BOOKS.created_at) <= TO_DATE ('".$request->get('date_to')."', 'yyyy-mm-dd')";
                }




        
            if($request->get('catalog_type')!=''){
                $query = DB::table('LIBRARY_BOOKS')
                    ->select(DB::raw("
                        library_title,
                        MIN(LIBRARY_BOOKS.id) as id,
                        MIN(library_author_name) as library_author_name,
                        MIN(LIBRARY_BOOKS.created_at) as created_at,
                        MIN(accession_no) as accession_no,
                        MIN(library_ddc_number) as library_ddc_number,
                        MIN(library_author_mark) as library_author_mark,
                        MIN(library_price) as library_price,
                        MIN(LIBRARY_BOOKS.LIBRARY_DATE_PUBLICATION) as DATE_PUBLICATION,
                        MIN(LIBRARY_BOOKS.CREATED_AT) as date_of_purchase,
                        MIN(school_name) as school_name,
                        MIN(emp_f_name) as emp_f_name,
                        MIN(library_attachment) as library_attachment,
                        MIN(LIBRARY_BOOKS.id) as library_id,
                        MIN(library_item_type) as library_item_type
                    "))
                    ->leftJoin('emp_employees', 'library_entry_user', '=', 'employee_id')
                    ->leftJoin('library_accession_number', 'LIBRARY_BOOKS.id', '=', 'library_accession_number.book_id')
                    ->leftJoin('schools', 'schools.school_id', '=', 'library_books.library_purchased_by')
                    ->whereRaw($sql);

                    if ($request->get('catalog_type') != '') {
                        $query->groupBy('library_title');
                    }

                $view_books = $query->get();
            }else{
                $view_books = DB::table('LIBRARY_BOOKS')
                    ->select(DB::raw("library_title,LIBRARY_BOOKS.id as id
                    ,library_author_name
                    ,LIBRARY_BOOKS.created_at
                    ,accession_no
                    ,library_ddc_number
                    ,library_author_mark
                    ,library_price
                    ,LIBRARY_BOOKS.LIBRARY_DATE_PUBLICATION as DATE_PUBLICATION
                    ,LIBRARY_BOOKS.CREATED_AT as date_of_purchase
                    ,school_name
                    ,emp_f_name,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            library_circulation
                        WHERE
                                lc_accession_no = LIBRARY_ACCESSION_NUMBER.accession_no
                            AND
                                lc_book_status = 'Issued'
                    ) AS issued,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            library_circulation
                        WHERE
                                lc_accession_no = LIBRARY_ACCESSION_NUMBER.accession_no
                            AND
                                lc_book_status = 'Received'
                    ) AS received
                    ,library_attachment
                    ,LIBRARY_BOOKS.id as library_id
                    ,library_item_type"))
                    ->leftJoin('emp_employees', 'library_entry_user', '=', 'employee_id')
                    ->leftJoin('library_accession_number', 'LIBRARY_BOOKS.id', '=', 'library_accession_number.book_id') // ->Paginate(50);
                    ->leftJoin('schools', 'schools.school_id', '=', 'library_books.library_purchased_by') // ->Paginate(50);
                    ->whereRaw($sql)->get();
            }
        


            
            
            return $view_books;
    }

   function SearchFromAllocation( $accession,$username){
  //echo $username;exit;
    $Book           =   new BookCirculation;
    $checkcriteria  =   $Book->Book_criteria($username);
    $Bookissue      =   $Book->Book_issue($username); 
    // echo $checkcriteria->lbc_duration_issuance;exit;  

    $checkBookIssuance = DB::table('library_circulation') 
    ->whereRaw("lc_accession_no = $accession and lc_book_status = 'Issued' ")      
      
    ->get();   

    if(count($checkBookIssuance) > 0) {
        return response()->json(array('status' => false,'data'=> [], 'message' => 'Book Already Issued')); 
    }
    

    $sql="select * from (
        SELECT CURRENT_DATE,
          lc_issue_date,lc_issue_no_of_days,lc_issue_date + lc_issue_no_of_days as due_date
        FROM library_circulation
        WHERE lower(sai_enrollment_no)=lower('$username')
        AND lc_book_status            ='Issued'
        ) where current_date > due_date";
    $view_sql = DB::select($sql);
    
       // echo count($view_sql);exit;
    // THIS CHECK WAS REMOVED ON REQUEST OF MR ZAFAR LIBRARIAN
    // if(count($view_sql)>0){
    //     return response()->json(array('status' => false,'data'=> [], 'message' => 'Book Cannot be issued due to Overdue Date'));      
    // }

    $sql= " 1 = 1 and book_status in ('Active','Lost') ";

   if($accession !='' ){
    $sql .= " AND LOWER(library_accession_number.accession_no) = $accession"; 
    }


    $view_books = DB::table('LIBRARY_BOOKS')
    ->leftJoin('emp_employees', 'library_entry_user', '=', 'employee_id')
    ->leftJoin('library_accession_number', 'library_books.id', '=', 'library_accession_number.book_id')->whereRaw($sql)->get(); // ->Paginate(50);   


    //print_r($view_books[0]);

    // exit;

    return $view_books;

     return   ( isset($view_books[0]) )?$view_books[0]:array(); 

   }

   function UpdateBookItem(Request $request,$id){

    $sess = new sessions(); 
    $sess->startSession();  
    $login_id = $sess->emp_user_id;
    $array_access = $request->get('access_no');
    $array_id = $request->get('access_id');

   $count_array =   count($array_access);

   if($request->get('libraryImg') !='' && $request->file('attachment') =='' ){
        $file_name = $request->get('libraryImg');
   }else { 

    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $file_name = $file->getClientOriginalName();
        // $file_name = $user_id.$file->getClientOriginalExtension();
        //dd($file_name);
    $file->move(public_path() . '/library_images/', $file_name);
    }else {
        $file_name = '';
    }     
   }
 
   if($request->get('libraryEbook') !='' && $request->file('e_book') ==''){
    $file_name_eBook = $request->get('libraryEbook');
    
   }else{

    if ($request->hasFile('e_book') ) {
        $file = $request->file('e_book');
       
        $file_name_eBook = $file->getClientOriginalName();
       
    $file->move(public_path() . '/library_images/', $file_name_eBook);
    }else {
        $file_name_eBook = '';
    }   

   }
  
    $library_data= BookItems::where('id',$id);
    $formdata=$this->dataset($request->all());    
    $formdata = array_merge($formdata, ['LIBRARY_ATTACHMENT' => $file_name,'LIBRAY_EBOOK' => $file_name_eBook,'LIBRARY_ACCESSION_NUMBER' => $count_array,'LIBRARY_ENTRY_USER' => $login_id,'BOOK_STATUS' => $request->get('book_status')    ]);  
 
    $update_book_item  = $library_data->update($formdata);
    for( $i=0; $i<count($array_id) ;$i++){     
        $update_library_accession = DB::table('LIBRARY_ACCESSION_NUMBER') ->where('LAN_ID', $array_id[$i])->update([
            'BOOK_ID' => $id,
            'ACCESSION_NO' => $array_access[$i]
           
        ]);
    }   
    if($update_library_accession){
         return response()->json(array('status' => true,'data'=> [], 'message' => 'Item Updated Successfully',
           'action' => [ 'type' => 'redirect', 'url' => 'abc' ]));
       }  

   }

   function dataset($dataset){
  //  dd($dataset['school']);

    $bookitem = array();
    $bookitem['LIBRARY_PURCHASED_BY']        = $dataset['school'];
    $bookitem['LIBRARY_ITEM_TYPE']           = $dataset['item_type'];
    $bookitem['LIBRARY_NAME']                = $dataset['library_main'];   
    $bookitem['LIBRARY_NUMBER_ISSN']         = $dataset['isbn_issn'];
    $bookitem['LIBRARY_DDC_NUMBER']          = $dataset['ddc'];
    $bookitem['LIBRARY_AUTHOR_MARK']         = $dataset['author_mark'];
    $bookitem['LIBRARY_AUTHOR_NAME']         = $dataset['author_name'];
    $bookitem['LIBRARY_SUB_AUTHOR_NAME']     = $dataset['sub_author_name'];
    $bookitem['LIBRARY_TITLE']               = $dataset['item_title'];
    $bookitem['LIBRARY_SUBTITLE']            = $dataset['item_subtitle'];
    $bookitem['LIBRARY_EDITION']             = $dataset['edition'];
    $bookitem['LIBRARY_VOLUME']              = $dataset['volume'];
    $bookitem['LIBRARY_PRICE']               = $dataset['price_item'];
    $bookitem['LIBRARY_PUBLISHER']           = $dataset['publisher'];
    $bookitem['LIBRARY_PLACE_PUBLICATION']   = $dataset['place_of_publication'];
    $bookitem['LIBRARY_DATE_PUBLICATION']    = $dataset['Date_of_publication'];
    $bookitem['LIBRARY_PAGES']               = $dataset['pages'];
    $bookitem['LIBRARY_SOURCES']             = $dataset['source'];
    $bookitem['LIBRARY_SERIES_STATEMENT']    = $dataset['series_statement'];
    $bookitem['LIBRARY_KEYWORD']             = $dataset['keyword'];
    $bookitem['LIBRARY_SUBJECT']             = $dataset['subject'];
    $bookitem['LIBRARY_BINDING']             = $dataset['binding'];
    $bookitem['LIBRARY_LANGUAGE']            = $dataset['language'];

    return $bookitem;


    
   }

   public function getDownload()
{
    //PDF file is stored under project/public/download/info.pdf
    $file= public_path(). "/images/bilalsale.pdf";

    $headers = array(
              'Content-Type: application/pdf',
            );

    return Response::download($file, 'filename.pdf', $headers);
}

function spineBarcode(){
    $schools =  Schools::all();
    return view('grader.library.spinebarcode',['schools'=>$schools]);
}
function dailyLibraryVisitor()
{
    $sql = "select * from library_daily_visitor";
    $resSql = DB::select($sql);
    return view ('grader.library.daily-library-visitor',['libaryData'=>$resSql ]);
}
function storeDailyLibraryVistitor(Request $request)
{
        
    // dd($request->all());
    $date = $request->get('date');
    $noOfStudents = $request->get('number');
    $insertLibraryDailyVisitor = DB::table('library_daily_visitor')->insert([

        'ldv_date' => $date,
        'ldv_nof_students' => $noOfStudents,
        'created_at' => Carbon::now('Asia/Karachi')->format('Y-m-d')
    ]);
    
    
    

}
function UpdateBookStatus($bookstatus,$id){
  
    $updateBookStatus = "update library_books set book_status = '$bookstatus' where id  = $id";
   // echo $updateBookStatus;exit;
    $result = DB::insert($updateBookStatus);
    if($result){
        echo '1';
    }
}
function BookCorrectionStatus($bookstatus,$id){
  
    $updateBookStatus = "update library_books set status = '$bookstatus' where id  = $id";
   // echo $updateBookStatus;exit;
    $result = DB::insert($updateBookStatus);
    if($result){
        echo '1';
    }
}
function datacorrection(){
    $sql = "select * from library_books where LIBRARY_NUMBER_ISSN in (select LIBRARY_NUMBER_ISSN from (
        SELECT
        count(distinct lower(ltrim(rtrim(LIBRARY_TITLE)))),LIBRARY_NUMBER_ISSN
        -- LIBRARY_TITLE,library_books.*
        FROM
           library_books
          group by LIBRARY_NUMBER_ISSN
           having count(distinct lower(ltrim(rtrim(LIBRARY_TITLE)))) > 1
        )
        where LIBRARY_NUMBER_ISSN is not null)
        order by LIBRARY_NUMBER_ISSN";
    $view_sql = DB::select($sql);
    return view('grader.library.correction',['viewBooks'=>$view_sql]);
}

public static function accession_no($id) {
 //   return $id;
   return DB::select("select * from library_accession_number where book_id=".$id);
    }
// public function accession_no(){
//     return 'ok';
// //    return DB::select("select * from library_accession_number");
// }


function updateAccessionNo(Request $request,$id){

    $bookIdArray = $request->get('acc_no');

    $validator = Validator::make($request->all(), [
        // "acc_no"    => "required|array|min:1",
        //"acc_no"  => "required|integer",
    ],
    [
        'acc_no.integer' => 'Accassion No must be Number Value.!',
    ]
    );

    if ($validator->fails()) {
        return response()->json(array('status' => false,'data'=> [], 'message' => $validator->errors()->first()));
    }
    foreach($bookIdArray as $row){

        if($row != ''){

            $insert = "INSERT INTO library_accession_number (book_id, accession_no)
            VALUES ($id, $row)";
            $res = DB::insert($insert);
            if($res){
                echo "1";
            }else{
                echo "0";
            }
        }
    }


}

function accessionRegister(Request $request){   
    // ,(SELECT COUNT(*)
    //                       FROM library_circulation
    //                       WHERE library_books_id = library_books.id
    //                       and lc_book_status = 'Issued'
    //                     ) AS issued    
    // ,lc_book_status
   
    $sqlRaw= "book_status in ('Active','Lost') and library_accession_number.accession_no is not null";
    $sql  = DB::table('LIBRARY_BOOKS')
    ->select(DB::raw("library_title
                        ,library_author_name
                        ,accession_no
                        ,library_ddc_number
                        ,library_author_mark
                        ,emp_f_name
                        ,(SELECT COUNT(*)
                          FROM library_circulation
                          WHERE  lc_accession_no = LIBRARY_ACCESSION_NUMBER.accession_no
                          and lc_book_status = 'Issued'
                        ) AS issued
                        ,(SELECT COUNT(*)
                        FROM library_circulation
                        WHERE  lc_accession_no = LIBRARY_ACCESSION_NUMBER.accession_no
                        and lc_book_status = 'Received'
                        ) AS Received
                        ,library_attachment
                        ,LIBRARY_BOOKS.id as library_id
                        ,library_books.library_edition
                        ,library_item_type
                        
                        ,book_status "))
    ->leftJoin('emp_employees', 'library_entry_user', '=', 'employee_id')
    //->leftjoin('library_circulation','library_books_id','=','LIBRARY_BOOKS.id')
    ->leftJoin('library_accession_number', 'library_books.id', '=', 'library_accession_number.book_id')
    //->where('book_status',"Active")
    
    ->whereRaw($sqlRaw);

    //      ->orderBy('LIBRARY_BOOKS.id', 'DESC')
    //      ->Paginate(50);
    //      $keys = ['title_order','access_order','',''];

    if($request->get('title_order') != ''){
        $order = ($request->get('title_order') == 'asc')?'ASC':'DESC';
        $view_books = $sql->orderBy('library_title',$order)->paginate(50);
    }elseif( $request->get('access_order') != ''){
        $order = ($request->get('access_order') == 'asc')?'ASC':'DESC';
        $view_books = $sql->orderBy('library_accession_number.accession_no',$order)->paginate(50);
    }elseif( $request->get('ddc_order') != ''){
        $order = ($request->get('ddc_order') == 'asc')?'ASC':'DESC';
        $view_books = $sql->orderBy('library_ddc_number', $order)->paginate(50);
    }elseif( $request->get('author_mark_order') != ''){
        $order = ($request->get('author_mark_order') == 'asc')?'ASC':'DESC';
        $view_books = $sql->orderBy('library_author_mark', $order)->paginate(50);
    }else{
        
        $view_books = $sql->orderBy('LIBRARY_BOOKS.id', 'DESC')->paginate(50);
    }
    
    //dd($view_books);
    //print_r($view_books); exit;


    return view('grader.library.accession_register',['viewBooks'=>$view_books]);

   }

   function getLibraryBooks(Request $request){ 
       
    $search = $request->get('search');
 
 
 
    if($search == 'All'){
    
             $sqlRaw= "book_status in ('Active','Lost')";
             $sql  = DB::table('LIBRARY_BOOKS')
             ->select(DB::raw("library_title
                                 ,library_author_name
                                 ,accession_no
                                 ,library_ddc_number
                                 ,library_author_mark
                                 ,emp_f_name
                                 ,(SELECT COUNT(*)
                                 FROM library_circulation
                                 WHERE library_books_id = library_books.id
                                 and lc_book_status = 'Issued'
                                 ) AS issued
                                 ,library_attachment
                                 ,LIBRARY_BOOKS.id as library_id
                                 ,library_item_type
                                 ,library_publisher
								 ,library_edition
                                 ,library_date_publication
                                 ,lc_book_status
                                 ,book_status "))
 
 
                                 
             ->leftJoin('emp_employees', 'library_entry_user', '=', 'employee_id')
             ->leftjoin('library_circulation','library_books_id','=','LIBRARY_BOOKS.id')
             ->leftJoin('library_accession_number', 'library_books.id', '=', 'library_accession_number.book_id')
             //->where('book_status',"Active")
             
             ->whereRaw($sqlRaw);
 
             //      ->orderBy('LIBRARY_BOOKS.id', 'DESC')
             //      ->Paginate(50);
             //      $keys = ['title_order','access_order','',''];
 
             if($request->get('title_order') != ''){
                 $order = ($request->get('title_order') == 'asc')?'ASC':'DESC';
                 $view_books = $sql->orderBy('library_title',$order)->paginate(12);
             }elseif( $request->get('access_order') != ''){
                 $order = ($request->get('access_order') == 'asc')?'ASC':'DESC';
                 $view_books = $sql->orderBy('library_accession_number.accession_no',$order)->paginate(12);
             }elseif( $request->get('ddc_order') != ''){
                 $order = ($request->get('ddc_order') == 'asc')?'ASC':'DESC';
                 $view_books = $sql->orderBy('library_ddc_number', $order)->paginate(12);
             }elseif( $request->get('author_mark_order') != ''){
                 $order = ($request->get('author_mark_order') == 'asc')?'ASC':'DESC';
                 $view_books = $sql->orderBy('library_author_mark', $order)->paginate(12);
             }else{
                 
                 $view_books = $sql->orderBy('LIBRARY_BOOKS.id', 'DESC')->paginate(12);
             }
              
 //dd($view_books);
             return response()->json(array('status' => true,'data'=> $view_books));
             //dd($view_books);
             //print_r($view_books); exit;
         }else{
             $searchBy = $request->get('searchBy');
 
             $sql= " 1 = 1";
             
             if( $searchBy == 'Keyword' ){
                 $sql .= " AND LOWER(LIBRARY_BOOKS.library_keyword) like LOWER('%$search%')";
             }elseif( $searchBy == 'Title' ){
                 $sql .= " AND  REPLACE(LOWER(LIBRARY_BOOKS.library_title),chr(39),'') like LOWER('%$search%')";  
             }elseif( $searchBy == 'Author' ){
                 $sql .= " AND LOWER(LIBRARY_BOOKS.library_author_name) like LOWER('%$search%')"; 
             }elseif( $searchBy == 'DDC' ){
                 $sql .= " AND LOWER(LIBRARY_BOOKS.LIBRARY_DDC_NUMBER) like LOWER('%$search%')"; 
             }elseif( $searchBy == 'ISBN' ){
                 $sql .= " AND LOWER(LIBRARY_BOOKS.LIBRARY_NUMBER_ISSN) like LOWER('%$search%')"; 
             }elseif( $searchBy == 'Accession No' ){
                 $sql .= " AND LOWER(library_accession_number.accession_no) = $search"; 
             }elseif( $searchBy == 'Subject' ){
                 $sql .= " AND LOWER(LIBRARY_BOOKS.LIBRARY_SUBJECT) like LOWER('%$search%')"; 
             }elseif( $searchBy == 'Publisher' ){
                 $sql .= " AND LOWER(LIBRARY_BOOKS.library_publisher) like LOWER('%$search%')"; 
             }else{
                 //  ->whereRaw("full_name || username like '%$query%' ")
                 $sql .= " AND LOWER( LIBRARY_BOOKS.library_author_name || 
                 LIBRARY_BOOKS.library_title ||  
                 LIBRARY_BOOKS.library_keyword || 
                 LIBRARY_BOOKS.library_author_name || 
                 LIBRARY_BOOKS.LIBRARY_SUBJECT || 
                 LIBRARY_BOOKS.LIBRARY_NUMBER_ISSN
                 ) like LOWER('%$search%')"; 
             }
         
            // echo ucfirst($search);exit;
             
               $searchedBooks=DB::table('LIBRARY_BOOKS')
             ->select(DB::raw("library_attachment,LIBRARY_BOOKS.id as library_id,library_title,library_edition,library_author_name,library_publisher,library_date_publication,library_item_type"))
             ->leftJoin('library_accession_number', 'library_books.id', '=', 'library_accession_number.book_id')
             ->whereRaw($sql)->get();
             
 
 
         //    $view_books = DB::select($sql); 
             return response()->json(array('status' => true,'data'=> $searchedBooks));
 
 
 
         }
    }

   function libraryCirculation(){
   // group by library_accession_number
 //  $status  = 'Issued';
    $sql= "book_status='Active' ";
     $view_books = DB::table('LIBRARY_BOOKS')
      ->select(DB::raw("(select count(*)from library_accession_number where book_id = library_books.id) as total
                        ,library_title
                        ,library_author_name
                        ,accession_no
                        ,library_ddc_number
                        ,library_author_mark
                        ,emp_f_name
                        ,(SELECT COUNT(*)
                          FROM library_circulation
                          WHERE library_books_id = library_books.id
                          and lc_book_status = 'Issued'
                        ) AS issued
                        ,library_attachment
                        ,LIBRARY_BOOKS.id
                        ,library_item_type"))
      ->leftJoin('emp_employees', 'library_entry_user', '=', 'employee_id')
      ->leftJoin('library_accession_number', 'library_books.id', '=', 'library_accession_number.book_id')      
      ->orderBy('LIBRARY_BOOKS.id', 'DESC')
      ->whereRaw($sql)     
      ->Paginate(50);      //->get();
    //  ->toSql();
    //   exit;
    
   
       //echo $sql;exit;
      return view('grader.library.circulation',['viewBooks'=>$view_books]);

   }

//    function libraryAllocation($id,$access_no){

//     $employees = DB::table('EMP_EMPLOYEES')       
                           
//                             ->get();   

//     return view('library.allocation',['id'=>$id,'access_no'=>$access_no,'employees'=>$employees]);

//    }
   function libraryAllocation(){

    $employees = DB::table('EMP_EMPLOYEES')       
                           
                            ->get();   

    return view('grader.library.allocation',['employees'=>$employees]);

   }
   function libraryAllocation2(){

    $employees = DB::table('EMP_EMPLOYEES')       
                           
                            ->get();   

    return view('library.allocationbkp',['employees'=>$employees]);

   }

   function getAllocation($roll_no,$book_id){
     return   $view_books = DB::table('LIBRARY_CIRCULATION')       
        ->where('sai_enrollment_no', '=', $roll_no)       
        ->get();  
   }

   function loadDetails($roll_no,$id,$access_no){  

  
    $sqlProfile = "SELECT * FROM sisusers
    left join students_apply_info on lower(sai_enrollment_no) = username
    left join students_personal_info on sai_spi_id = spi_id
    left join schools on students_apply_info.sai_sdpo_school_id = schools.school_id
    left join emp_employees on emp_employees.emp_email = sisusers.email
    left join departments on emp_employees.emp_dept_id = departments.dept_id
    where lower(username) = lower('$roll_no')";
    // echo $sqlProfile;
    
       $view_profile = DB::select($sqlProfile);

       $sql = "SELECT library_author_name,library_author_mark,library_ddc_number,LIBRARY_CIRCULATION.id as id,lc_due_date ,lc_return_date ,LIBRARY_CIRCULATION.lc_accession_no as lc_accession_no ,lc_issue_no_of_days,lc_issue_date ,lc_book_status ,lc_fine_total,sai_enrollment_no ,library_title ,library_books_id,lc_total_late_days
       FROM LIBRARY_CIRCULATION
       left join library_books on library_books_id = library_books.id
       LEFT JOIN library_accession_number ON library_accession_number.book_id = library_books.id
       -- where LIBRARY_CIRCULATION.SAI_ENROLLMENT_NO = '$roll_no'
       ";
       //echo  $sql ;
  
       $view_books = DB::select($sql);
  
       $sqlLibraryBooks = "SELECT library_title,library_author_name,accession_no,(select count(*)from library_accession_number where book_id = library_books.id) as total_books
       FROM LIBRARY_BOOKS   
       left join library_accession_number on book_id = library_books.id    
       where LIBRARY_BOOKS.id = '$id'
       and library_accession_number.accession_no = '$access_no'
       and book_status = 'Active'";
  
       $view_sqlLibraryBooks = DB::select($sqlLibraryBooks);

       
       return view('grader.library.load-details',['viewBooks'=>$view_books,'view_profile'=>$view_profile,'view_sqlLibraryBooks'=>$view_sqlLibraryBooks]);
  }
  function loadDetails2($roll_no,$id,$access_no){   

  
    $sqlProfile = "SELECT * FROM sisusers
    left join students_apply_info on lower(sai_enrollment_no) = username
    left join students_personal_info on sai_spi_id = spi_id
    left join schools on students_apply_info.sai_sdpo_school_id = schools.school_id
    left join emp_employees on emp_employees.emp_email = sisusers.email
    left join departments on emp_employees.emp_dept_id = departments.dept_id
    where lower(username) = lower('$roll_no')";
    // echo $sqlProfile;    
       $view_profile = DB::select($sqlProfile);

    $sql="SELECT library_author_name,lc_total_late_days,library_author_mark,library_ddc_number,LIBRARY_CIRCULATION.id as id,lc_due_date ,lc_return_date ,LIBRARY_CIRCULATION.lc_accession_no as lc_accession_no ,lc_issue_no_of_days,lc_issue_date ,lc_book_status ,lc_fine_total,sai_enrollment_no ,library_title ,library_books_id
    FROM LIBRARY_CIRCULATION
    left join library_books on library_books_id = library_books.id
    LEFT JOIN library_accession_number ON library_accession_number.lan_id = library_books.id
    where lower(LIBRARY_CIRCULATION.SAI_ENROLLMENT_NO) = lower('$roll_no')";
     //  echo  $sql ;
  
       $view_books = DB::select($sql);
  
       $sqlLibraryBooks = "SELECT library_title,library_author_name,accession_no,(select count(*)from library_accession_number where book_id = library_books.id) as total_books
       FROM LIBRARY_BOOKS   
       left join library_accession_number on book_id = library_books.id    
       where LIBRARY_BOOKS.id = '$id'
       and library_accession_number.accession_no = '$access_no'
       and book_status = 'Active'";
  
       $view_sqlLibraryBooks = DB::select($sqlLibraryBooks);

       
       return view('library.load-detailsbkp',['viewBooks'=>$view_books,'view_profile'=>$view_profile,'view_sqlLibraryBooks'=>$view_sqlLibraryBooks]);
  }

  function issueBook2(Request $request){

    //echo $date  = date('Y-m-d',time());exit;
    $accessionNumArr    = $request->get('accession_nos');
    $username           = $request->get('username');

    //  echo $username; exit;
    $checkFine = "select COALESCE(SUM(lc_fine_total), 0) as total_fine from library_circulation where sai_enrollment_no = '$username' and lc_fine_status = 'UnPaid'";
    //echo $checkFine ;exit;
    $res = DB::select($checkFine);
    if($res[0]->total_fine == 0){
        $libraryStatus = "SELECT library_status FROM sisusers   
        where lower(username) = lower('$username')";
        $view_status = DB::select($libraryStatus);
        
        if($view_status[0]->library_status != 'Active'){
            return response()->json(array('status' => false,'data'=> [], 'message' => 'User Library Status is Blocked'));
        }

        $Book           =   new BookCirculation;
        $checkcriteria  =   $Book->Book_criteria($username);
        $Bookissue      =   $Book->Book_issue($username); 
        // echo $checkcriteria;exit;
        
        $alreadyTaken   = $Bookissue;
        $bookRequest    = count($accessionNumArr);
        $allow          = $checkcriteria->lbc_limit;

        if( ($alreadyTaken+$bookRequest) > $allow ){
            $msg = "You only take ".$allow." Books. ".( ($alreadyTaken)?' And you already taken '.$alreadyTaken.' Books.':'' ) ;
            return response()->json(array('status' => false, 'data'=> ['alreadyTaken' => $alreadyTaken, 'bookRequest' => $bookRequest, 'allow' => $allow ], 'message' => $msg));
        }

        $date  = date('Y-m-d',time());

        $booksIDsArr = $request->get('book_ids');

        $index = 0;
        $data = [];
        foreach($accessionNumArr as $accessNum ):
            $circulation    =   new libraryCirculation; 
            $due_date                            = date('Y-m-d', strtotime($date . '+'.$checkcriteria->lbc_duration_issuance.'days'));
            $insertArr                           = array();
            $insertArr['SAI_ENROLLMENT_NO']      = strtolower($username);
            $insertArr['LIBRARY_BOOKS_ID']       = $booksIDsArr[$index];
            $insertArr['LC_ACCESSION_NO']        = $accessionNumArr[$index];
            $insertArr['LC_ISSUE_DATE']          = $date;
            $insertArr['LC_ISSUE_NO_OF_DAYS']    = $checkcriteria->lbc_duration_issuance;
            $insertArr['LC_BOOK_STATUS']         = 'Issued';
        //  $insertArr['ENTRY_USER_ID']          = $login_id ;
            $insertArr['LC_DUE_DATE']          = $due_date;
            $insert_issue_status  = $circulation->create($insertArr)->id;
            $data[]= $insertArr;
            $index++;
        endforeach;
        return response()->json(array('status' => true,'data'=>$data, 'message' => 'Book Issued Successfully'));
    }else{
        return response()->json(array('status' => true, 'message' => 'Book cannot Issue due Unpaid Fine'));
    }
  }













  function issueBook(Request $request,$date,$access_no,$book_id){
  

    $sess = new sessions(); 
    $sess->startSession();  
    $login_id = $sess->emp_user_id;
    $username = $request->get('sai_enrollment_no');

    $libraryStatus = "SELECT library_status FROM sisusers   
    where lower(username) = lower('$username')";
    $view_status = DB::select($libraryStatus);
    //echo  $view_status[0]->library_status;exit;

    $Book=new BookCirculation;
    $checkcriteria=$Book->Book_criteria($request->get('sai_enrollment_no'));
    $Bookissue=$Book->Book_issue($request->get('sai_enrollment_no')); 

    if($view_status[0]->library_status == 'Active'){
        if($checkcriteria->lbc_limit > $Bookissue){
            $circulation=new libraryCirculation; 
            $due_date = date('Y-m-d', strtotime($date . '+'.$checkcriteria->lbc_duration_issuance.'days'));
            $insertArr = array();
            $insertArr['SAI_ENROLLMENT_NO']      = strtolower($request->get('sai_enrollment_no'));
            $insertArr['LIBRARY_BOOKS_ID']       = $book_id;
            $insertArr['LC_ACCESSION_NO']        = $access_no;
            $insertArr['LC_ISSUE_DATE']          = $date;
            $insertArr['LC_ISSUE_NO_OF_DAYS']    = $checkcriteria->lbc_duration_issuance;
            $insertArr['LC_BOOK_STATUS']         = 'Issued';
            $insertArr['ENTRY_USER_ID']          = $login_id ;
            $insertArr['LC_DUE_DATE']          = $due_date;
        // print_r($insertArr);exit;
            $insert_issue_status  = $circulation->create($insertArr)->id;
            if($insert_issue_status){
            
                return response()->json(array('status' => true,'data'=> [], 'message' => 'Book Issued Successfully'));
            }
            }else{
                return response()->json(array('status' => false,'data'=> [], 'message' => 'Book Issue Limit Exceeded'));
            }
    }else{
        return response()->json(array('status' => false,'data'=> [], 'message' => 'User Library Status is Blocked'));
    }
  }
  function librarycriteria(){
   
    return view('grader.library.library-criteria');
}

function insertCriteria(Request $request){


    $sess = new sessions(); 
    $sess->startSession();  
    $login_id = $sess->emp_user_id;
    $issuance=new librarycriteria;     
    $insertArr = array();
    $insertArr['LBC_CATEGORIES']         = $request->get('categories');
    $insertArr['LBC_LIMIT']              = $request->get('limit');
    $insertArr['LBC_FINE_PER_DAY']       = $request->get('fine');
    $insertArr['LBC_BOOK_LOST']          = $request->get('book_lost');
    $insertArr['LBC_DURATION_ISSUANCE']  = $request->get('duration');
    $insertArr['LBC_REISSUANCE_POLICY']  = $request->get('reissuance_poilicy');    
    $insertArr['ENTRY_USER_ID']          = $login_id ;
    $insert_library_criteria             = $issuance->insert($insertArr);
    //$insert_library_criteria             = $issuance->create($insertArr)->id;

    if($insert_library_criteria){
     
         return response()->json(array('status' => true,'data'=> [], 'message' => 'Criteria Saved Successfully'));
       }
  }

  function showlibrarycriteria(){

    $sql = "SELECT *
    FROM LIBRARY_BOOK_CRITERIA";

    $view_criteria = DB::select($sql); 

    return view('grader.library.view_library_criteria',['view_criteria'=>$view_criteria]);


  }
  function getlibrarycriteria($id){

    $sql = "SELECT *
    FROM LIBRARY_BOOK_CRITERIA where id = $id";

return  $view_criteria = DB::select($sql); 



  }
  function updateCriteria(Request $request,$id){


    $sess = new sessions(); 
    $sess->startSession();  
    $login_id = $sess->emp_user_id;
    $issuance= LibraryCriteria::where('id',$id);    
    $insertArr = array();
    $insertArr['LBC_CATEGORIES']         = $request->get('categories');
    $insertArr['LBC_LIMIT']              = $request->get('limit');
    $insertArr['LBC_FINE_PER_DAY']       = $request->get('fine');
    $insertArr['LBC_BOOK_LOST']          = $request->get('book_lost');
    $insertArr['LBC_DURATION_ISSUANCE']  = $request->get('duration');
    $insertArr['LBC_REISSUANCE_POLICY']  = $request->get('reissuance_poilicy');    
    $insertArr['ENTRY_USER_ID']          = $login_id ;
    $update_library_criteria             = $issuance->update($insertArr);
    //$insert_library_criteria             = $issuance->create($insertArr)->id;

    if($update_library_criteria){
     
         return response()->json(array('status' => true,'data'=> [], 'message' => 'Criteria Update Successfully'));
       }
  }


  function accessionRegister_excel(Request $request){ 
    // return $this->libraryReport($request); 
//    dd($request->all()); 
        return view('grader.library.cataloging_reports_excel',
            [
                'viewBooks'=>$this->viewBookSearchItemCatlog($request)
            ]
        );
 

   }


   
  public function report_cataloging(Request $request){

   
    $sqlRaw= "book_status in ('Active','Lost') and library_accession_number.accession_no is not null";
    $sql  = DB::table('LIBRARY_BOOKS')
    ->select(DB::raw("library_title
                        ,library_author_name
                        ,accession_no
                        ,library_ddc_number
                        ,library_author_mark
                        ,emp_f_name
                        ,(SELECT COUNT(*)
                          FROM library_circulation
                          WHERE library_books_id = library_books.id
                          and lc_book_status = 'Issued'
                        ) AS issued
                        ,library_attachment
                        ,LIBRARY_BOOKS.id as library_id
                        ,library_books.library_edition
                        ,NVL(library_item_type,'No Type') as library_item_type
                        ,lc_book_status
                        ,book_status "))
    ->leftJoin('emp_employees', 'library_entry_user', '=', 'employee_id')
    ->leftjoin('library_circulation','library_books_id','=','LIBRARY_BOOKS.id')
    ->leftJoin('library_accession_number', 'library_books.id', '=', 'library_accession_number.book_id')
    //->where('book_status',"Active")
    
    ->whereRaw($sqlRaw);

    //      ->orderBy('LIBRARY_BOOKS.id', 'DESC')
    //      ->Paginate(50);
    //      $keys = ['title_order','access_order','',''];

    if($request->get('title_order') != ''){
        $order = ($request->get('title_order') == 'asc')?'ASC':'DESC';
        $view_books = $sql->orderBy('library_title',$order)->paginate(50);
    }elseif( $request->get('access_order') != ''){
        $order = ($request->get('access_order') == 'asc')?'ASC':'DESC';
        $view_books = $sql->orderBy('library_accession_number.accession_no',$order)->paginate(50);
    }elseif( $request->get('ddc_order') != ''){
        $order = ($request->get('ddc_order') == 'asc')?'ASC':'DESC';
        $view_books = $sql->orderBy('library_ddc_number', $order)->paginate(50);
    }elseif( $request->get('author_mark_order') != ''){
        $order = ($request->get('author_mark_order') == 'asc')?'ASC':'DESC';
        $view_books = $sql->orderBy('library_author_mark', $order)->paginate(50);
    }else{
        
        $view_books = $sql->orderBy('LIBRARY_BOOKS.id', 'DESC')->paginate(50);
    }
    
    //dd($view_books);
    //print_r($view_books); exit;


    return view('grader.library.report_cataloging',['viewBooks'=>$view_books])->with(['schools' => DB::table('SCHOOLS')->whereRaw("school_id not in (8,9,10,11)")->get()]);


   }

}
