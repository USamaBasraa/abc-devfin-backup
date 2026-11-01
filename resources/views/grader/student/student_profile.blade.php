<style type="text/css">
    .profile_td_title{
        display: none;
    }
    .profile_td{
        padding: 0 !important;
        margin: 0 !important;
    }
    .row_height{
        padding: 0 !important;
        margin: 0 !important;

    }

</style>
<table class="table table-bordered table-condensed col-md-12">
    <tr class="row_height">
        <td class="profile_td_title">Name : </td>
        <td class="profile_td"><?=$student_profile[0]->spi_name;?></td>
        <td rowspan="7" align="center">
            <img src="http://itrc-dev/bba/studentpics/<?=$student_profile[0]->sai_enrollment_no;?>.jpg" width="140px" height="140px">
            <div style="margin-left: auto; margin-right: auto; text-align: center; color: green; font-weight: bold; font-size: 14px">
                <?=$student_profile[0]->sai_student_status;?>
            </div>
        </td>
    </tr>
    <tr class="row_height">
        <td class="profile_td_title">Enrollment No : </td>
        <td class="profile_td"><?=$student_profile[0]->sai_enrollment_no;?></td>
    </tr>
    <tr class="row_height">
        <td class="profile_td_title">Session : </td>
        <td class="profile_td"><?=$student_profile[0]->sess_name;?></td>
    </tr>
    <tr class="row_height">
        <td class="profile_td_title">School : </td>
        <td class="profile_td"><?=$student_profile[0]->school_name;?></td>
    </tr>
    <tr class="row_height">
        <td class="profile_td_title">Department : </td>
        <td class="profile_td"><?=$student_profile[0]->dept_name;?>
        </td>
    </tr>
    <tr class="row_height">
        <td class="profile_td_title">Programe : </td>
        <td class="profile_td"><?=$student_profile[0]->prog_name;?></td>
    </tr>
    <tr class="row_height">
        <td class="profile_td_title">Shift : </td>
        <td class="profile_td"><?=$student_profile[0]->shift_name;?></td>
    </tr>
</table>