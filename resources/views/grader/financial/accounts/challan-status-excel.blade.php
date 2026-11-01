@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')



<?php

// print_r($challan_summary);exit;
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
                    <?php
                   // $total = 0;
                    $total_amt = 0;
                    ?>
                    
                    @foreach($challan_summary as $challan)

                    <strong>
                    (
                        {{$challan->total_callan}}
                        {{$challan->payment_ststus}}
                        <?php $total_amt = $total_amt+$challan->total_callan; ?>
                        )
                    </strong>

                     @endforeach
                     <b>(Total Challan #: {{$total_amt}})</b>


                    </td>
                    </tr>



                    <tr>
                        <th>Sr.</th>
                        <th>Reg No</th>
                        <th>School</th>
                        <th>Department</th>
                        <th>Challan Generate Status</th>
                        <th>Challan #</th>
                        <th>Paid Status</th>
                        <th>Request From Student</th>
                        <th>Request From RO</th>
                        <th>Challan Completion Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $count=1;
                        //use  App\Http\Controllers\Grader\Financial\StundentsFinancialController;
                    ?>
                    @foreach($result as $row)
                    <tr>
                        <td>{{$count}}</td>
                        <td>{{$row->regno}}</td>
                        <td>{{$row->school}}</td>
                        <td>{{$row->department}}</td>
                        <td>{{$row->challan_generate}}</td>
                        <td>{{$row->buyercodechallanno}}</td>
                        <td>{{$row->paidon}}</td>
                        <td>{{$row->requestfromstudent}}</td>
                        <td>{{$row->rosentrequest}}</td>
                        <td>{{$row->completed}}</td>



                    </tr>
                    <?php $count++;?>
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
