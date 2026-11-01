@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<?php
                $count=1;
                use  App\Http\Controllers\Grader\Financial\StundentsFinancialController;
?>
<div class="card">
    <div class="card-header" style="display:inline">
        <h2>Students Financial <a style="font-size:14px;float:right"  type="button"   onclick="ExportToExcel('xlsx')"><i style="color:green" class="fa fa-file-excel-o fa-2x" aria-hidden="true">Print Excel</i></a> </h2>
        <!-- style="width:100%; overflow-x:scroll" class="" id="tbl_exporttable_to_xls" -->
    </div>
        <div class="card-body table-responsive">
            <div class="table-responsive pt-4 pb-4">            
<table  width="100%" class="table-bordered" cellpading="4" id="tbl_exporttable_to_xls">
                <thead>
                    <tr>
                    <td colspan="27">
                    <?php
                    $date = new DateTime("now", new DateTimeZone('Asia/Karachi') );
                    echo 'Generated At : '.$date->format('Y-m-d H:i:s').''.'</b>';
                    ?>
                    </td>
                    </tr>
                    <thead>
                    <tr>
                        <th>Sr.</th>
                        <th>Session</th>
                        <th>Reg No</th>
                        <th>Total<br>Generated</th>
                        <th>Total<br>Paid</th>
                        <th>Status</th>
                        <th>Total Enrolled Courses</th>
                        <th>Total cr.hr</th>
                        <th>Enrollment Status</th>
                        <th>Student Status</th>
                        <th>School Code</th>
                        <th>Challan Issue Date</th>
                        <th>Enrollment Datetime</th>
                    </tr>
                </thead>
                </thead>
                <tbody>
                    <?php
                        $count=1;
                    ?>
                    @foreach ($challan_status as $challan_status)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <th>{{$challan_status->session_id}} </th>
                            <th>{{$challan_status->regno}} </th>
                            <th> {{$challan_status->total_generated}}  </th>
                            <th> {{$challan_status->total_paid}} </th>
                            <th> {{$challan_status->payment_ststus}} </th>
                            <th> {{$challan_status->total_enrolled}} </th>
                            <th> {{$challan_status->total_cr}} </th>
                            <th> 
                                @if ($challan_status->total_enrolled>0)
                                    Enrolled
                                @else
                                   Not Enrolled
                                @endif
                            </th>
                            <th> {{$challan_status->student_status}} </th>
                            <th> {{$challan_status->school_code}} </th>
                            <th> {{$challan_status->challan_issue_datetime}} </th>
                            <th> {{$challan_status->enrollment_datetime}} </th>
                        </tr>
                     @endforeach
                </tbody>
            </table>








    </div>
    </div>





</div>
{{-- --------------------------------- --}}

{{-- ----------------------------- --}}






@stop
@section('javascript')
<script type="text/javascript">
function ExportToExcel(type, fn, dl) {
    var currentdate = new Date();
    var elt = document.getElementById('tbl_exporttable_to_xls');
    var wb = XLSX.utils.table_to_book(elt, { sheet: "sheet1" });
    return dl ?
      XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }):
      XLSX.writeFile(wb, fn || (currentdate+'MySheetName.' + (type || 'xlsx')));
 }


</script>
 @stop
