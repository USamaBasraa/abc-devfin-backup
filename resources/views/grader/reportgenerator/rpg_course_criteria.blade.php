<?php
        if(count($criteria)<=0){ echo "Not submitted yet";exit();};
    ?>
    
    <div class="datatable_div" style="border:none; overflow-x:hidden;">
    <div class="row">
        <div class="col-lg-12 text-left" >
            <a target="_blank" type="button" class="btn btn-lg btn-info" href="{{ route('/assessment_marks_report', ['co_id' => $criteria[0]->co_id]) }}"> Get Report </a>
        </div>
    </div>
  
    <input type="text" id="get_co_id" name="get_co_id" value="{{$criteria[0]->co_id}}" hidden="true" />
    <input type="text" id="get_course_name" name="get_course_name"
           value="{{  $criteria[0]->course_cd_name }}  {{ $criteria[0]->co_section }}  {{ $criteria[0]->co_course_cr }} " hidden="true" />
    <table id="getcourseCriteria" name="getcourseCriteria" class="display" width="100%">
        <thead style="background-color: #2F3B4D;color:white" >
            <tr>
                <th class="tab-title"> Assesment Name </th>
                <th class="tab-title"> Grading Method </th>
                <th class="tab-title"> Total Entered </th>
                <th class="tab-title"> Selected  </th>
                <th class="tab-title"> Weightage </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($criteria as $row_criteria)
             <tr>
                 <td align="left"> {{$row_criteria->assessment_name}} </td>
                 <td align="left"> {{$row_criteria->method_name}}     </td>
                 <td> {{$row_criteria->total_enter}}     </td>
                 <td> {{$row_criteria->best_of_no}}      </td>
                 <td> {{$row_criteria->cc_weightage}}%   </td>
             </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        var course_name = $('#get_course_name').val();
        var table = $('#getcourseCriteria').DataTable({
            "responsive": true,
            "searching": false,
            "paging": false,
            "bInfo": false,
            "orderable": true,
            "fixedHeader": true,
            "scrollY": 240,
            "scrollX": false
        });
    });
</script>