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
        <h2>School wise Challan Summary <a style="font-size:14px;float:right"  type="button"   onclick="ExportToExcel('xlsx')"><i style="color:green" class="fa fa-file-excel-o fa-2x" aria-hidden="true">Print Excel</i></a> </h2>
        <!-- style="width:100%; overflow-x:scroll" class="" id="tbl_exporttable_to_xls" -->

    </div>

        <div class="card-body table-responsive">
            <div class="table-responsive pt-4 pb-4">

<table  width="100%" class="table-bordered" cellpading="4" id="tbl_exporttable_to_xls">
                <thead>


                    <tr>
                        <th>School Code</th>
                        <th>Payment Status</th>
                        <th>Total Challan</th>

                    </tr>
                </thead>
                <tbody>

                    @foreach($challan_status as $row)
                    <tr>

                        <td>{{$row->school_code}}</td>
                        <td>{{$row->payment_status}}</td>
                        <td>{{$row->total_challan}}</td>

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
