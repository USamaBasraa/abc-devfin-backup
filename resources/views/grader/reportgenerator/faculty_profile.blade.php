<style type="text/css">
    .table_style{
        width: 100%;
        padding: 4px;
        cellspacing:4px;
        text-align: center;

    }
    .profile_td_title{
        text-align: right;
        font-weight: bold;
        padding: 0px;!important;
        margin: 0px;!important;

    }
    .profile_td{
        text-align: left;
        padding: 0px;!important;
        margin: 0px;!important;
    }
    .title_td{
        text-align: left;
        color: white;
        background-color:#2F3B4D;
        font-size: 14px;
        padding: 10px;


    }
    .sub_title_td{

        background-color: #6f42c1;

    }
</style>
<table width="100%" cellpadding="0" cellspacing="0">
 {{--  <tr>
        <td class="title_td">
            Evaluation Status
        </td>
    </tr>--}}
    <tr>
        <td>
            <table class="table table-sm">
                <tr>
                    <td class="profile_td_title" width="20%">Name : </td>
                    <td class="profile_td">
                        <?=$course_faculty_data[0]->prefix;?>
                        <?=$course_faculty_data[0]->emp_f_name;?>
                        <?=$course_faculty_data[0]->emp_l_name;?>
                    </td>
                    <td rowspan="4">
                      @if(isset($course_faculty_data[0]->emp_imagefile) && $course_faculty_data[0]->emp_imagefile!="")
     <img src="http://itrc-dev/bba/staffimages/<?=$course_faculty_data[0]->emp_imagefile;?>" width="140px" height="140px">
                      @else
     <img src="http://itrc-dev/bba/staffimages/noimage.jpg" width="140px" height="140px">
                      @endif
                    </td>
                </tr>
                <tr>
                    <td class="profile_td_title">School : </td>
                    <td class="profile_td">
                        <?=$course_faculty_data[0]->school_name;?>
                     </td>
                </tr>
                <tr>
                    <td class="profile_td_title">Department : </td>
                    <td class="profile_td">
                        <?=$course_faculty_data[0]->dept_name;?>
                    </td>
                </tr>
                <tr>
                    <td class="profile_td_title">Faculty Type : </td>
                    <td class="profile_td">
                        <?=$course_faculty_data[0]->emp_type;?>
                    </td>
                </tr>

            </table>
        </td>

    </tr>
</table>

