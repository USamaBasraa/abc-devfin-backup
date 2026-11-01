@extends('layouts.app')
@section('content')
<style type="text/css">
.canvasjs-chart-canvas{
    color:white !important;
}
</style>
<div class="card">     
    <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-family:'Times New Roman', Times, serif;">Student Health Report</h3>
        <h3 style="font-family:'Times New Roman', Times, serif;">Total Students: <?php  echo count($res);?></h3>
    </div>
</div>    

    <div class="card" style="margin-top:10px;">    
        <!-- <button class="btn primary" onclick="downloadExcel2();" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> GET Excel</button> -->
        <div class="card-body">
            <table class="table table-sm  table-bordered">
                <thead>
                    <tr>
                        <th style="text-align:center;font-weight:bold;" scope="col">Sr.No</th>
                        <th style="text-align:center;font-weight:bold;" scope="col">Roll #</th>
                        <th style="text-align:center;font-weight:bold;" scope="col">Student Name</th>
                        <th style="text-align:center;font-weight:bold;" scope="col">School</th>
                        <th style="text-align:center;font-weight:bold;" scope="col">Program</th>    
                        <th style="text-align:center;font-weight:bold;" scope="col">PDF</th>             
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sr = 1;
                    foreach($res as $row){?>
                        <tr>
                            <td style="text-align:center;"><?=$sr++?></td>
                            <td style="text-align:left;width:100px"><?=$row->sai_enrollment_no?></td>
                            <td style="text-align:left;"><?=$row->spi_name?></td>
                            <td style="text-align:left;"><?=$row->school_name?></td>
                            <td style="text-align:left;"><?=$row->prog_name?></td>                            
                            <td style="text-align:center;">
                                <div class="d-inline">
                                    <button type="button" class="btn btn-info" onclick="viewPDF('<?=$row->sai_enrollment_no?>')">View Detail</button>
                                    <?php if($row->shr_file != '') { ?>
                                        <a target="_blank" href="<?=$row->shr_file?>" class="btn btn-primary" download>Download File</a>
                                    <?php } ?>
                                </div>
                            </td>
                            
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>    
@stop
@section('javascript')
<script>
    function viewPDF(roll_no){
       
        url = 'https://staff.bnu.edu.pk/HOD/studentHealthReport.php?id='+roll_no;
        window.open(url, '_blank');
    }

   


</script>
@stop