@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<?php

//print_r($result);exit;
?>

<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>

<?php 
                $count=1; 
                use  App\Http\Controllers\Grader\Financial\StundentsFinancialController;          
?>

<div class="card">
    <div class="card-header" style="display:inline">
        <h2>Students Financial <a style="font-size:14px;float:right"  type="button"   onclick="ExportToExcel('xlsx')"><i style="color:green" class="fa fa-file-excel-o fa-2x" aria-hidden="true">Print Excel</i></a> </h2>
        <!-- style="width:100%; overflow-x:scroll" class="" id="tbl_exporttable_to_xls" --> 
        <span> {{$totalCount}}</span>
    </div>
   
        <div class="card-body table-responsive">

            <table  width="100%" class="table-bordered" cellpading="4" id="tbl_exporttable_to_xls">
                <thead>
                    <tr>
                    <td colspan="4">
                    <?php

$date = new DateTime("now", new DateTimeZone('Asia/Karachi') );
echo 'Generated At : '.$date->format('Y-m-d H:i:s');
?>
                    </td>
                    </tr>
                    <tr>
                        <th>Sr.</th>
                        <th>App ID</th>
                        <th>Reg No</th>
                        <th>Name</th>
                        <th>School Name</th>
                        <th>Prog Name</th>
                        <th>Room ID</th>
                        <th>Category</th>
                        <th>Status</th>
                        <?php 
                        $scholor_ship_data =  StundentsFinancialController::student_scholorshaip_details('NOSTUDENT'); 
                        ?>
                        @foreach($scholor_ship_data as $rec_second)
                        @if($rec_second->scholarship_id>=21)
                        <th> {{$rec_second->scholarship_name}} </th>
                        @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $count=1; 
                        //use  App\Http\Controllers\Grader\Financial\StundentsFinancialController;          
                    ?>
                    @foreach($result as $rec)
                    <tr>
                        <td>{{$count}}</td>
                        <td>{{$rec->sai_id}}</td>
                        <td>{{$rec->hor_sai_enrollment_no}}</td>
                        <td>{{$rec->spi_name}}</td>
                        <td>{{$rec->school_name}}</td>
                        <td>{{$rec->sai_programid}}</td>
                        <td>{{$rec->hor_room_id}}</td>
                         <td>{{$rec->hor_category}}</td>
                         <td>{{$rec->sai_student_status}}</td>
                         <?php 
                        $scholor_ship_data =  StundentsFinancialController::student_scholorshaip_details($rec->hor_sai_enrollment_no); 
                        ?>
                        @foreach($scholor_ship_data as $rec_second)
                        @if($rec_second->scholarship_id>=21)
                        <td>{{$rec_second->ssd_percentage}} </td>
                        @endif
                        @endforeach 
                       
                    </tr>
                    <?php $count++;?>
                    @endforeach
                </tbody>
            </table>
    </div>
</div>
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