<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
?>
@extends('layouts.updated_app',['pageTitle' => 'Uploaded Files for the Courses'])
@section('content')

<table border="1" align="center" cellspacing="4" cellpadding="4" width="90%">
    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Course Name</th>
            <th>Faculty</th>
            <th>File Upload Status</th>
        </tr>
    </thead>
    <?php
    $sr = 1;
    foreach($rec as $row){
    ?>
    <tr>
    <td><?= $sr++; ?></td>
    <td><?= $row->course_name. ' |'.$row->co_id; ?></td>   
    <td><?= $row->teacher_name; ?></td>    
    <td <?php if($row->content_status != 1) { echo 'style="background-color: red;"'; } ?>>
    <?php if($row->content_status == 1){ ?>
        <b>Course File Uploaded</b>
    <?php } else { ?>
        <b>Course File Missing</b>
    <?php } ?>
    </td>

    <!-- <td>
        <a href="http://cmstest.bnu.edu.pk/teacher/course_content/<?=$row->co_id;?>" target="_blank">
            <button type="button" class="btn btn-success">Success</button>
        </a>
    </td> -->
</tr>    
<?php
    }
?>
</tr>
</table>


@stop
@section('javascript')
<script type="text/javascript">

</script>
@stop

