@extends('layouts.app') <!-- Adjust based on your layout -->

@section('content')

<div class="card">
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-family:'Times New Roman', Times, serif;">Tmetable View</h3>

    </div>
    <div class="card-body">
        <div class="row">
            <!-- First Column -->
            <div class="col-6">
                <div id="mentor-details">
                    <h3>Mentor Timetable</h3>
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Week Day</th>
                                <th>From Time</th>
                                <th>To Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($resmentorTimeSlots as $row){?>
                                <tr>
                                    <td><?=$row->tt_subject_day?></td>
                                    <td><?=$row->tt_subject_time_from?></td>
                                    <td><?=$row->tt_subject_time_to?></td>
                                </tr>

                            <?php } ?>
                            <!-- Mentor details rows will be dynamically added here -->
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Second Column -->
            <div class="col-6">
                <div id="additional-info">
                    <h3>Student Timetable</h3>
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Week Day</th>
                                <th>From Time</th>
                                <th>To Time</th>
                            </tr>
                        </thead>
                        <tbody id="details-table-3">
                            <?php foreach($resSTudentTimeSlots as $row){?>
                                <tr>
                                    <td><?=$row->tt_subject_day?></td>
                                    <td><?=$row->tt_subject_time_from?></td>
                                    <td><?=$row->tt_subject_time_to?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
       
      
    </div>
</div>





@endsection
@section('javascript')
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>


</script>

@endsection
