<?php


namespace App\Exports;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Grader\Hostel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ApplicantsExport implements FromCollection, WithHeadings
{
    protected $session_id;

    public function __construct($session_id)
    {
        $this->session_id = $session_id;
    }

    public function collection()
    {
        return DB::table('admission_apply_info')
            ->select([
                'admission_personal_info.api_allow_hostel',
                'aai_id',
                'api_id',
                'CMS_SAI_ENROLLMENT_NO',
                'admission_personal_info.API_NAME',
                'admission_personal_info.API_GENDER',
                'admission_personal_info.API_PERMANENTADDRESS',
                'admission_personal_info.api_permanentcity',
                'admission_apply_info.aai_apply_date',
                'admission_personal_info.API_gender',
                'admission_personal_info.API_ishostel',
                'admission_apply_info.aai_sdpo_sess_id',
                'ADMISSION_SCHEDULES.admission_sch_round',
                'ADMISSION_SCHEDULES.ADMISSION_SCH_NAME'
            ])
            ->join('admission_personal_info', 'admission_apply_info.AAI_API_FORM_ID', '=', 'admission_personal_info.API_FORMID')
            ->join('ADMISSION_SCHEDULES', 'admission_apply_info.AAI_ADMISSION_SCH_ID', '=', 'ADMISSION_SCHEDULES.ADMISSION_SCH_ID')
            ->where('admission_apply_info.AAI_STATUS', 'Selected')
            ->where('admission_personal_info.API_ISHOSTEL', 'Yes')
            ->where('admission_apply_info.AAI_SDPO_SESS_ID', $this->session_id)
            ->orderByRaw("CASE WHEN LOWER(admission_personal_info.API_PERMANENTCITY) = 'lahore' THEN 1 ELSE 0 END")
            ->orderBy('admission_apply_info.aai_apply_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Allow Hostel',
            'AAI ID',
            'API ID',
            'Enrollment No',
            'Name',
            'Gender',
            'Permanent Address',
            'City',
            'Apply Date',
            'Gender',
            'Is Hostel',
            'Session ID',
            'Admission Round',
            'Schedule Name'
        ];
    }
}
