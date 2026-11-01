<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;
use DB;

class BookCirculation extends Model
{
    
    public function Book_criteria($user_id =''){


        $sel="select * from LIBRARY_BOOK_CRITERIA
       where lbc_categories in (
       SELECT
               case 
               when sisusers.GROUP_ID  = 4 and PROG_DEGREE_LEVEL='Graduate' then 'Graduation 2/4 Years'
               when sisusers.GROUP_ID  = 4 and PROG_DEGREE_LEVEL='Undergraduate' then 'Graduation 2/4 Years'
               when sisusers.GROUP_ID  = 4 and PROG_DEGREE_LEVEL='Postgraduate' then 'Post Graduate'
               when sisusers.GROUP_ID  = 4 and PROG_DEGREE_LEVEL='Doctorate' then 'Post Graduate'
               WHEN sisusers.GROUP_ID IN (222) THEN 'Permanent Faculty'
               when sisusers.GROUP_ID  in (223,364) then 'Dean / HOD'
               else 'Staff' end
                 as criteria_name
       FROM
           sisusers
           left join STUDENTS_APPLY_INFO on lower(STUDENTS_APPLY_INFO.SAI_ENROLLMENT_NO) = lower(sisusers.USERNAME)
           left join PROGRAMS on PROGRAMS.prog_id = sai_sdpo_prog_id
           left join EMP_EMPLOYEES on lower(EMP_EMPLOYEES.EMP_AD_USERNAME) = lower(sisusers.USERNAME) 
       WHERE
        sisusers.username in lower ('$user_id')
        )";

       //  echo $sel; exit;

                    $result = DB::select($sel);
                return $result[0];


    }

    public function Book_issue($user_id =''){

        $sel="SELECT count(*) as rec FROM library_circulation WHERE lower(sai_enrollment_no)=lower('$user_id') and lc_book_status='Issued' ";
         $result = DB::select($sel);
        return $result[0]->rec; 


    }

 
}
