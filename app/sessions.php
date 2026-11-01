<?php
namespace App;


class sessions{
    public $emp_school_id;
    public $emp_dept_id;
    public $emp_group_id;
    public $emp_user_id;
    public $emp_user_name;
    public $emp_type;
    public $sisuser_id;
    public $active_session;
    public $emp_image;
    public $dept_hod_status;
    public $login_ip;
    public $phone_no;
    public $emp_email;

    function startSession(){
        $user = auth()->user();
        // print_r($user); exit;

        $this->sisuserID = $user->id;
        $this->emp_school_id    = $user->school_id;
        $this->emp_dept_id      = (isset($user->dept_id)?$user->dept_id:0);
        $this->emp_group_id     = $user->group_id;
        $this->emp_user_id      = $user->employee_id;
        $this->emp_user_name    = $user->full_name;
        $this->emp_type         = null; // $_SESSION["emp_type"];
        $this->sisuser_id       = $user->id;
        $this->active_session_id   = $user->profile_settings->active_session; // $user->profile_settings->active_session;//148; // $_SESSION["active_session_id"];
        $this->emp_image        = ''; // $user->emp_image; // null; // $_SESSION['emp_image'];
        $this->dept_hod_status  = $user->dept_hod_status;
        $this->login_ip          = $user->login_ip;
        $this->phone_no         = $user->mobile;
        $this->emp_email        = $user->personal_email;
        $this->active_session_name        =$user->profile_settings->active_session;
       // $this->active_session   = 150;
    }
}
