@extends('layouts.app', ['pageTitle' => 'Car pooling'])
@section('content')

<div class="card" style="margin-top:10px;margin-right: 20px !important;width:99%;border-radius:10px">
    <div class="card-header bg-primary" style="color:white !important;border-top-right-radius:10px;border-top-left-radius:10px">
        <div style="float:left">
            <h3 class="card-title">Rider Profile</h3>
        </div>
    </div>
    <div class="card-body">
<!-- Include the necessary CSS and JavaScript files for the datatable library -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<!-- Add the table element with an ID -->
<table id="car-pulling-table" class="display">
    <thead>
        <tr>
            <th>Sr #</th>
            <th>Username</th>
            <th>Vehicle No</th>
            <th>Vehicle Token</th>
            <!-- Add other column headers if needed -->
        </tr>
    </thead>
    <tbody>
        <?php
        $a=1;
        ?>
        @foreach ($data as $row)
        <tr>
            <td>{{$a++}}</td>
            <td>{{$row->vr_username}}</td>
            <td>{{$row->vr_vehicle_no}}</td>
            <td>{{$row->vr_vehicle_token}}</td>
            <!-- Display other data columns as needed -->
        </tr>
        @endforeach
    </tbody>
</table>



      
     </div>
</div>
@endsection
@section('javascript')     
<!-- Initialize the datatable -->
<script>
    $(document).ready(function() {
        $('#car-pulling-table').DataTable({
            "dom": '<"top"lf>iBrt<"bottom"lp><"clear">',
            "language": {
                "info": "Showing _START_ to _END_ of _TOTAL_ records"
            }
        });
        
    });
</script>
@endsection

