<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Http\Controllers\Grader\EmpEmployeesController;
use DB;
use App\sessions;

class User extends Authenticatable{
  

    use HasFactory, Notifiable;

    protected $table = 'sisusers';
    //public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email','dept_id','prog_id','sis_emp_type','access_token','school_id','password','full_name','father_name', 'group_id','status','imagename','school', 'department','programs', 'designation', 'username','employee_id','dept_hod_status','mobile','personal_email','login_ip','program_level',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function employees()
    {
        return $this->hasOne('App\Models\Grader\EmpEmployees', 'emp_email', 'email');
    }

    public function check_token($token)
    {
        if($token){
            return self::select('sisusers.id')
                                ->where([
                                    ['sisusers.remember_token', $token] ])
                                ->get();
        }

    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'updated_at' => 'datetime'
    ];



    public function getUpdatedAtAttribute($value){
        return ''; //ucfirst($value);
    }

    public function getProfileSettingsAttribute($value){
        $json = json_decode($value);

        return $json;

    }
    function saveProfileSetting($id,$key,$value){
       $user = self::find($id);
        $user->forceFill([
            'profile_settings->'.$key => $value
        ])->save();
    }


    
    function profileImage(){
        if( isset($this->group_id)  ){
            if( $this->group_id == 4 ){
                return asset('staffimages/'.$this->username);
            }else{
                 return asset('staffimages/'.$this->imagename);
            }
        }else{
            return asset('images/photos/10.jpg');
        }
    }


    public function updateUserByUserName($username){


        $query = "SELECT sisusers.id, username, email,group_id,
        emp_f_name,emp_l_name , prefix, emp_f_name, emp_contact_no, emp_l_name emp_full_name,emp_email ,imagename emp_photo ,emp_school_id ,emp_sch.school_name AS emp_school_name,emp_dept.dept_name AS emp_dept_name,
        sai_spi_id, emp_employees.*,
        COALESCE(sai_spi_id,0) AS spi_id ,
        spi_enroll_prefix , spi_enroll_id , spi_name, spi_imagename, spi_fathername,
      --  designation,
        spi_sess_id,
        sai_sdpo_sess_id   AS SPI_ENROLLMENT_SESSION,
        sai_sdpo_school_id,
    --    sai_cgpa,
        sai_sdpo_dept_id   AS spi_dept_id ,
        sai_sdpo_prog_id   AS spi_prog_id ,
        sai_sdpo_shift_id,
        emp_employees.emp_type as sis_emp_type,
        sai_sdpo_shift_id AS spi_shift_id ,
        sai_semester      AS sai_active_semester,
        sai_enrollment_no AS SPI_ENROLLMENT_NO ,
        sai_student_status, spi_mobile,  spi_email,
        (SELECT MAX(sess_id) FROM academic_sessions WHERE sess_admission_active='Yes' ) spi_active_sess_id, sai_id,
        (SELECT max(gpm_active_sessl_id) as gpm_active_sessl_id FROM group_pages_management WHERE gpm_school_id = sai_sdpo_school_id ) AS active_enroll_sess, api_token
      FROM sisusers
      LEFT JOIN emp_employees           ON LOWER(email)      = LOWER(emp_email)
      LEFT JOIN schools emp_sch         ON emp_school_id     = emp_sch.school_id
      LEFT JOIN departments emp_dept    ON emp_dept_id       = emp_dept.dept_id
      LEFT JOIN students_apply_info     ON LOWER(username)   = LOWER(sai_enrollment_no)
      LEFT JOIN students_personal_info  ON sai_spi_id        = spi_id
                                       AND sai_student_status!='Transferred'
                                  
        WHERE sisusers.username = '" . $username."'";



        $result = DB::select($query);


        if (count($result) > 0) {
            $row                    = $result[0];
            $data['id']             = $row->id;
            $data['full_name']      = null;
            $data['father_name']    = '';
            $data['username']       = $row->username;
            $data['email']          = $row->email;
            $data['group_id']       = $row->group_id;
            $data['student_status'] = $row->sai_student_status;
            $data['user_type']      = ($row->group_id == 4) ? 'student' : 'staff';
          //  $data['designation']    = $row->designation;
            $data['image']          =  $data['school'] = $data['school_id'] = $data['dept_id'] = $data['department'] = $data['prog_id'] = $data['personal_email'] = $data['mobile'] = $data['programs'] = null;

            if (!($row->spi_name)) {
                $data['full_name'] = $row->prefix . ' ' . $row->emp_f_name . ' ' . $row->emp_l_name;
                // $data['image']          = "http://cmstest.bnu.edu.pk/staffimages/" . strtoupper($row->username) . ".jpg";
                //      Route::post('get_image/{type}/{id}', 'ApiAuthController@get_user_image');
                //      $data['image']          = url('get_image/emp/'.$row->username);

                $data['image']          = $row->emp_imagefile;
                $data['school']         = $row->emp_school_name;
                $data['school_id']      = $row->emp_school_id;
                $data['department']     = $row->emp_dept_name;
                $data['dept_id']        = $row->emp_dept_id;
                $data['personal_email'] = $row->emp_email;
                $data['sis_emp_type']   = $row->sis_emp_type;
                $data['mobile']         = $row->emp_contact_no;
            } else {

                // student side
                $data['full_name']              = $row->spi_name;
                $data['father_name']            = $row->spi_fathername;
                // $data['image']                  = "https://staff.bnu.edu.pk/studentpics/" . strtoupper($row->username) . ".jpg";
                // $data['image']               = url('get_image/student/'.$row->username);
                $data['image']                  = strtoupper($row->username) . ".jpg";
                $data['school_id']              = $row->sai_sdpo_school_id;

            

                $data['enrollment_no']          = strtoupper($row->spi_enrollment_no);
                $data['dept_id']                = $row->spi_dept_id;
                $data['prog_id']                = $row->spi_prog_id;
              //  $data['sai_cgpa']               = $row->sai_cgpa;
                $data['api_token']              = $row->api_token;
                $data['mobile']                 = $row->spi_mobile;
                $data['email']                  = $row->spi_email;

                if ($data['school_id']) {
                    $school_res = DB::select("SELECT school_name FROM schools where school_id = " . $data['school_id']);
                    $data['school']         = (isset($school_res[0]->school_name)) ? $school_res[0]->school_name : '';
                }

                if ($data['dept_id']) {
                    $dept_res                 = DB::select("SELECT dept_name FROM departments where dept_id = " . $data['dept_id']);
                    $data['department']           = (isset($dept_res[0]->dept_name)) ? $dept_res[0]->dept_name : '';
                }

                if ($data['prog_id']) {
                    $prog_res = DB::select("SELECT prog_title, prog_degree_level FROM programs where prog_id = " . $data['prog_id']);
                    if (count($prog_res) > 0) {
                        $data['programs']                 = $prog_res[0]->prog_title;
                        $data['program_level']                 = $prog_res[0]->prog_degree_level;
                    }
                }
            } // else end for student

            $data['login_ip'] = request()->ip();
            $data['employee_id'] = $row->employee_id;
            $data['imagename'] = $data['image']; 
    
            $empCont = new EmpEmployeesController();
           //  $data['dept_hod_status']   =   $empCont->checkHod($data['dept_id'])[0]->dept_hod_status;
    
            $values = array_diff_key($data, array_flip(['username','email','student_status','id','image','user_type','enrollment_no']));
            User::where('username', $username)->update($values);

            return $data;

        } else {
            return null;
        }




    }





}
