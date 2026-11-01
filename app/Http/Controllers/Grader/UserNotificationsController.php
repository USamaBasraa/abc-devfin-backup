<?php

namespace App\Http\Controllers\Grader;
use Illuminate\Http\Request;
use App\Models\UserNotifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserNotificationsController extends Controller
{
    private $userNot;

    public function __construct(UserNotifications $notifications)
    {
        $this->userNot = $notifications;
    }

    public static function sendNotifications($co_id, $type)
    {

        $loginUser = Auth::user();
        $emp_user = $loginUser::find($loginUser->id)->employees;
        $rows = self::getHodID($co_id);

        if($type==3 || $type==4)
        {
            foreach($rows as $row)
            {
                $un = new UserNotifications();
                $un->un_to_user_id      = $row['hod'];
                $un->un_description     = $row['course_code']." - ".$row['course_cd_name']."(".$row['co_section'].") Submitted by Teacher";
                $un->un_entry_user_id   = $loginUser->id;
                $un->un_status          = 0;
                $un->un_seen_status     = 1;
                $res = $un->save();
            }
        }else if($type=='posting'){
            $un = new UserNotifications();
            $un->un_to_user_id      = 595;
            $un->un_description     = $rows['course_code'][0]." - ".$rows['course_cd_name'][0]."(".$rows['co_section'][0].") Result Posted by Teacher";
            $un->un_entry_user_id   = $loginUser->id;
            $un->un_status          = 0;
            $un->un_seen_status     = 1;
            $res = $un->save();
        }

        if($res)
        {
            return 1;
        }else{
            return 0;
        }
    }


    public static function getHodID($parent_co_id)
    {
        $query = "SELECT
                    co_id,
                    co_dept_id,
                    course_code,
                    course_cd_name,
                    co_section,
                    (SELECT
                            id
                        FROM
                            sisusers
                        WHERE
                            email = (
                                SELECT
                                    emp_email
                                FROM
                                    emp_employees
                                WHERE
                                    employee_id = check_dept_hod(co_dept_id)
                            )
                    ) hod
                FROM
                    courses_offered
                    LEFT JOIN courses_two ON co_course_id = course_id
                WHERE
                        co_id = ".$parent_co_id."
                    OR
                        parent_co_id = ".$parent_co_id;

        $result = DB::select($query);
        return json_decode(json_encode($result), true);
        
    }

}
