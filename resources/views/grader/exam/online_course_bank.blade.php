@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')


<div class="card">
  <div class="card-header" style="display:inline">
    <div style="float:left">
    <h3 class="card-title">Course Bank</h3>
    </div>
    <div style="float:right">
     
    </div>
  </div>
  <div class="card-body">
   
        <input type="hidden" id="employee_id" value=<?=$emp_id?>>
  
             <div id="course_div"></div>
  
  </div>
</div>



@stop


@section('javascript')
<script type="text/javascript">


$( document ).ready(function() {
    var employee_id =  $('#employee_id').val();
           
           $.ajax({
               type: 'get',
               url: '{{url('/get-emp-courses')}}/'+employee_id,

               success: function(data){
                var html = '';
                $.each(data,function( key, value ) {
                 
                    var url = '<?=url("teacher/quiz_question")?>?sch_id=0&co_id='+value.fc_co_id+'&from_dashboard=yes';
                    console.log('bilal',  value)

                 
                    html += '<ul>';
                    html += '<li>';
                    html += '<a href='+url+'>'+value.coursename+'</a>';
                    html += '</li>';
                    html += '</ul>';
                });

                if(html != ''){
                    $('#course_div').html(html);
                }else{
                    $('#course_div').html('No Course Found!');
                }

               }
           });
});


</script>

@stop
