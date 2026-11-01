<?php

namespace App\Models\Grader;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class AttendanceSlots extends Model
{

    protected $primaryKey   = 'tt_id';
    protected $table        = 'attendance_slots';
    public    $timestamps   = false;

    public function slot_details($slot_id){
       
        $sql = "
        SELECT
            tt_id,
            tt_class_date,
            get_slotname(tt_subject_time_from_id) AS start_time,
            get_slotname(tt_subject_time_to_id) AS end_time,
            get_time_difference(ts_time_stamp),
            current_timestamp,
                CASE
                    WHEN
                        ( get_time_difference(ts_time_stamp) - 30 ) < 0
                    AND
                        ( (
                            SELECT
                                get_time_difference(ts_time_stamp)
                            FROM
                                timetable_slots
                            WHERE
                                ts_id = master_timetable.tt_subject_time_from_id
                        ) + 10 ) > 0
                    AND
                        substr(tt_class_date,0,10) = substr(
                            TO_DATE(SYSDATE),
                            0,
                            10
                        )
                    THEN 'YES'
                    ELSE 'NO'
                END
            AS today_class_status,
                CASE
                    WHEN tt_class_date < TO_DATE(SYSDATE) THEN 'ALLOWED'
                    WHEN
                        ( get_time_difference(ts_time_stamp) - 30 ) < 0
                    AND
                        ( (
                            SELECT
                                get_time_difference(ts_time_stamp)
                            FROM
                                timetable_slots
                            WHERE
                                ts_id = master_timetable.tt_subject_time_from_id
                        ) + 10 ) > 0
                    AND
                        substr(tt_class_date,0,10) = substr(
                            TO_DATE(SYSDATE),
                            0,
                            10
                        )
                    THEN 'ALLOWED'
                    WHEN
                        ( get_time_difference(ts_time_stamp) - 30 ) > 0
                    AND
                        ( (
                            SELECT
                                get_time_difference(ts_time_stamp)
                            FROM
                                timetable_slots
                            WHERE
                                ts_id = master_timetable.tt_subject_time_from_id
                        ) + 10 ) > 0
                    AND
                        substr(tt_class_date,0,10) = substr(
                            TO_DATE(SYSDATE),
                            0,
                            10
                        )
                    THEN 'ALLOWED'
                    ELSE 'NO'
                END
            AS if_allowed,
            get_time_difference(ts_time_stamp) - 30 AS time_left,
            (
                SELECT
                    get_time_difference(ts_time_stamp)
                FROM
                    timetable_slots
                WHERE
                    ts_id = master_timetable.tt_subject_time_from_id
            ) + 10 AS time_start,
            ts_time_stamp,
            tt_subject_time_to_id,
            TO_CHAR(TO_DATE(tt_class_date),'DAY') AS class_day_name,
            tt_room_id,
            tt_room_name,
            room_ip,
            room_name,
            mtt_co_id,
            tt_subject_time_from_id,
            (
                SELECT
                    COUNT(*)
                FROM
                    faculty_course_attendance
                WHERE
                    faculty_course_attendance.mtt_id = tt_id
            ) AS attendance_mark_status
        FROM
            master_timetable
            LEFT JOIN timetable_slots ON tt_subject_time_to_id = ts_id
            LEFT JOIN building_rooms ON master_timetable.tt_room_name = building_rooms.room_id
            LEFT JOIN courses_offered ON master_timetable.mtt_co_id = courses_offered.co_id
        WHERE
                1 = 1
            AND
                tt_status = 'E'
            AND
                tt_class_date <= TO_DATE(SYSDATE)
            AND
                mtt_co_id = 17663
        and tt_id = ".$slot_id;
        
       return  $result = DB::select($sql);
    }


}


