<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Model;
use DB;

class AcademicSessions extends Model
{
    protected $fillable = [
        'sess_id',
        'sess_code',
        'sess_name',
    ];

    protected $hidden = [
        'id',
    ];

    public function fetch($id = 0)
    {
        if ($id) {
            return self::findOrFail($id);
        } else {
            return self::orderBy('sess_order', 'desc')->pluck("sess_name", "sess_id");
        }

    }

    public function fetchTopSession($count)
    {
        $query = "SELECT
                        sess_id,
                        sess_code,
                        sess_name
                    FROM
                        academic_sessions
                    WHERE
                        sess_status = 'E'
                    ORDER BY sess_id DESC FETCH NEXT " . $count . " ROWS ONLY";

        return $result = DB::select($query);
    }
    public static function getAcademicSessions()
    {
        return DB::table('ACADEMIC_SESSIONS')
            ->where('sess_status', 'E')
            ->orderBy('sess_id', 'desc')
            ->get();
    }
    public static function getAcademicSessionsSchool()
    {
        $user = auth()->user();
        $username = auth()->user()->username;
        $groupId = $user->group_id;
        $specialGroups = [223, 222, 644, 324, 544, 844];
        $specialUsers = ['moeedyusuf'];
        $query = DB::table('schools')
            ->where('school_status', 'E');

        if (in_array($groupId, $specialGroups) || in_array($username, $specialUsers)) {
            if ($username == 'ijaz.hussain') {
                return $query->where('school_id', 12)
                    ->orWhere('school_id', 21)
                    ->orderBy('school_id', 'asc')
                    ->get();
            } else {
                return $query->orderBy('school_id', 'asc')->get();
            }
        } else {
            if (
                $username == 'danyal.asgher'
            ) {
                return $query->where('school_id', 12)
                    ->orWhere('school_id', 21)
                    ->orderBy('school_id', 'asc')
                    ->get();
            } else {
                return $query->where('school_id', $user->school_id)
                    ->orderBy('school_id', 'asc')
                    ->get();
            }
        }
    }

    public static function getAcademicSessionsDepartments($school_id)
    {
        return DB::table('departments')
            ->where('dept_status', 'E')
            ->where('DEPT_SCHOOL_ID', $school_id)
            ->orderBy('dept_id', 'desc')
            ->get();
    }

    public static function getAcademicSessionsPrograms($school_id, $dept_id)
    {
        return DB::table('programs')
            ->where('PROG_SCHOOL_ID', $school_id)
            ->where('PROG_DEPT_ID', $dept_id)
            ->orderBy('prog_id', 'desc')
            ->get();
    }

    public static function getSessionWisePrograms($sess_id, $school_id, $dept_id)
    {
        return DB::select("
            SELECT DISTINCT
                sdpo.sdpo_prog_id AS prog_id,
                prog.prog_name
            FROM session_degree_program_offerin sdpo
            LEFT JOIN programs prog ON prog.prog_id = sdpo.sdpo_prog_id
            WHERE sdpo.sdpo_sess_id = ?
              AND sdpo.sdpo_school_id = ?
              AND sdpo.sdpo_dept_id = ?
              AND sdpo.sdpo_status = 'E'
            ORDER BY prog.prog_name ASC
        ", [$sess_id, $school_id, $dept_id]);
    }


}
