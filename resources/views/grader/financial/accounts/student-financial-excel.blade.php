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
                    <td colspan="31">
                    <?php

$date = new DateTime("now", new DateTimeZone('Asia/Karachi') );
echo 'Generated At : '.$date->format('Y-m-d H:i:s');
?>
                    </td>
                    </tr>
                    <tr>
                        <th>Sr.</th>
						<th>Req.ID</th>
                        <th>Reg No</th>
                        <th>APP ID</th>
                        <th>Student Name</th>
                        <th>Description</th>
                        <th>Entery Date Time</th>
                        <th>Forward to FO Date / Time</th>
                        <th>Complettion by FO</th>
                        <th>Fince User Name</th>
                        <th>Entry User Name</th>
                        <th>Session</th>
                        <th>Student Session</th>
                        <th>Email</th>
                        <th>CGPA</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Program</th>
                        <th>School</th>
                        <th>Department</th>
                        <th>Challan Status</th>
                        <!-- <th>Due Data</th>
                        <th>Valid Date</th> -->
                        <th>Total Pending Challans</th>
                        <!-- <th>Challan Type</th> -->

                        <?php
                            if($_REQUEST['get_type']=='challan_wise'){
                        ?>
                           <!-- <th > Challan Id </th>-->
                            <th > Challan Due Date </th>
                            <th > Challan Valid Date </th>
                            <th > Challan Valid Date  </th>
                            <th > Challan Valid Date  </th>
                        <?php
                                }
                        ?>
                        <?php
                        $scholor_ship_data =  StundentsFinancialController::student_scholorshaip_details('NOSTUDENT');
                        ?>
                        @foreach($scholor_ship_data as $rec_second)
                        <th> {{$rec_second->scholarship_name}} </th>
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
						<td>{{$rec->scd_id}}</td>
                        <td>{{$rec->sai_enrollment_no}}</td>
                        <td>{{$rec->sai_id}}</td>
                        <td>{{$rec->spi_name}}</td>
                        <td>{{$rec->challan_description}} -- {{$rec->scd_description}}</td>
                        <td>{{$rec->entry_datetime}}</td>
                        <td>{{$rec->challan_request_time}}</td>
                        <td>{{$rec->scd_req_details}}</td>
                        <td>{{$rec->scd_update_username}}</td>
                        <td>{{$rec->scd_entry_username}}</td>
                        <td>{{$rec->challan_session}}</td>
                        <td>{{$rec->student_session}}</td>
                        <td>{{$rec->spi_email}}</td>
                        <td>{{$rec->cgpa}}</td>
                        <td>{{$rec->semester_completed}}</td>
                        <td>{{$rec->semester_status}}</td>
                        <td>{{$rec->prog_name}}</td>
                        <td>{{$rec->school_code}}</td>
                        <td>{{$rec->dept_name}}</td>
                        <td>{{$rec->fee_type}}</td>
                        <!-- <td>$rec->challan_due_date</td>
                        <td>$rec->challan_valid_date</td> -->
                        <td>{{$rec->total_pending_challans}}</td>
                        <!-- <td>$rec->challan_type</td> -->


                        <?php
                            if($_REQUEST['get_type']=='challan_wise'){
                        ?>
                       <!-- <td>$rec->scd_challan_no</td>-->
                        <td>{{$rec->duedate}}</td>
                        <td>{{$rec->validdate}}</td>
                        <td>{{$rec->paidamt}}</td>
                        <td>{{$rec->paidon}}</td>
                        <?php
                            }
                        ?>
                        <?php
                        $scholor_ship_data =  StundentsFinancialController::student_scholorshaip_details($rec->sai_enrollment_no);
                        ?>
                        @foreach($scholor_ship_data as $rec_second)
                        <td>{{$rec_second->ssd_percentage}} </td>
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
