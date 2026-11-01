@extends('layouts.app')
@section('content')

<style>
  .card-header button:hover {
        background-color: #e2e2e2; /* Light grey on hover */
        transform: scale(1.05); /* Slightly increase size */
    }
</style>



<div class="card">  
    

<div class="card-header d-flex justify-content-between align-items-center" style="background-color:#2F3B4D !important; color:#FFF; display: flex; justify-content: space-between; align-items: center;">
    <h3 class="font-weight-bold mb-0" style="text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);">Hostel Survey Report</h3>
    <button type="button" class="btn btn-light btn-lg mr-2" onclick="viewPDF()" style="transition: background-color 0.3s, transform 0.2s;">
        <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Generate PDF Summary
    </button>
</div>


       
 

</div>    




<div class="card mt-3">    
    <div class="card-body">

    <h5 class="font-weight-bold">Number of Individual Submissions: <span class="text-primary"><?= htmlspecialchars($totalDistinctStudents) ?></span></h5>

        <table class="table table-sm table-bordered">
        <thead class="thead-light">
                <tr>
                    <th scope="col" class="left font-weight-bold">Survey Question</th>
                    <th scope="col" class="text-left font-weight-bold">NO</th>
                    <th scope="col" class="text-left font-weight-bold">YES</th>
                    <th scope="col" class="text-center font-weight-bold">In Al Kabir apartments</th>
                    <th scope="col" class="text-center font-weight-bold">In Book & Bed hostel</th>
                    <th scope="col" class="text-center font-weight-bold">On-campus hostel</th>
                    <th scope="col" class="text-center font-weight-bold">Somewhere else</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($res as $row): ?>
                    <tr>
                        <td class="text-left"><?= htmlspecialchars($row->question) ?></td>
                        <td class="text-left"><?= htmlspecialchars($row->no_count ?? 0) ?></td>
                        <td class="text-left"><?= htmlspecialchars($row->yes_count ?? 0) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row->ak_apartments ?? 0) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row->book_bed_count ?? 0) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row->on_campus ?? 0) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row->somewhere_else ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>    
@stop

@section('javascript')
<script>
    function viewPDF(){
       
        url = 'https://staff.bnu.edu.pk/HOD/student_hostel_survey_report.php?';
        window.open(url, '_blank');
    }

   


</script>
@stop