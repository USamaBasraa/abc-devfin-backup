@extends('layouts.app',['pageTitle' => 'Course Content'])
@section('content')
<style type="text/css">
.canvasjs-chart-canvas{
    color:white !important;
}
</style>

<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<div class="row" style="width: 98%; margin-left: auto; margin-right: auto;">
    <div class="col-md-12 col-lg-12 col-12">
        <div class="card">
        <div class="card-header" style="background-color:#2F3B4D !important; color:#FFF; ">
                    <h3 style="font-family:'Times New Roman', Times, serif; display: inline-block; float:left" >CGPA SUMMARY</h3>
        </div>
        </div>
        <div class="card" style="margin-top:10px;">
            <div class="card-header" style="border: solid 2px green;">
                <div style="float: left;width:90%">
                    <h5><strong>School Name:</strong> <span style="color:#F95700FF">{{$rec_ses[0]->school_name}}</span></h5>
                    <h5><strong>Department Name:</strong> <span style="color:#00539CFF" >{{$rec_ses[0]->dept_name}}</span></h5>
                </div>
                <div style="float: left;width:10%">
                            <i class="fa fa-file-excel-o" data-toggle="tooltip" title="fa fa-file-excel-o" style="color: green; cursor:pointer" onclick="ExportToExcel('xlsx')"> &nbsp; Export to Excel</i>
                </div>
            </div>
            <div class="card-body">
                    <table  border="1px solid black;" style="border-collapse:collapse;" width="100%" id="tbl_exporttable_to_xls">
                        <thead>
                            <?php
                            $sess_id='';
                            $pr=0;
                            ?>
                            <tr>
                            <th colspan="9"></th>
                            @foreach($rec_que as $rec)
                            <th colspan="2">{{$rec->sess_code}}</th>
                            @endforeach
                            <tr>
                            <th style="text-align:center;" scope="col">Sr.No</th>
                            <th style="text-align:center;" scope="col">Pros:ID</th>
                            <th style="text-align:center;" scope="col">Registration</th>
                            <th style="text-align:center;" scope="col">Name </th>
                            <th style="text-align:center;" scope="col">Status </th>
                            <th style="text-align:center;" scope="col">Total Credit </th>
                            <th style="text-align:center;"  scope="col">Transfer Cr.Hrs </th>
                            <th style="text-align:center;" scope="col">Earn Credit </th>
                            <th style="text-align:center;" scope="col">Short Cr.Hrs </th>
                            @foreach($rec_que as $rec)
                            <th style="writing-mode: vertical-rl; ">SGPA</th>
                            <th style="writing-mode: vertical-rl; ">CGPA</th>
                            <?php $pr++; ?>
                            @endforeach

                            </tr>
                        </thead>
                        <tbody id="result-body" >

                        <?php $count=1;
                            $enrollment='';
                            $enrollment_no='';
                            $total=0;
                            $color='';
                            $total=count($rec_ses);
                            $total_sub_td = count($rec_que);
                        ?>




<pre>

                    <?php
                            function cgpa_color($var_cgpa,$get_level){
                                $get_cgpa = number_format($var_cgpa, 2);
                                if($get_cgpa<2.00 && $get_level=="Undergraduate"){
                                    $color='#ff4d4d';
                                }elseif ($get_cgpa<2.50 && $get_level=="Graduate") {
                                    $color='#ff4d4d';
                                }elseif ($get_cgpa<3.00 && $get_level=="Postgraduate") {
                                    $color='#ff4d4d';
                                }else{
                                    $color='';
                                }
                                return $color;
                             }
                    ?>
                            </pre>
                            <?php
                           // exit();
                            ?>
                            @foreach($rec_ses as $rec)
                            @if($enrollment != $rec->sai_enrollment_no )
                            <?php
                            // if($rec->prog_degree_level =='Stuck-Off'){
                            // // $color='#F2AA4CFF';
                            // }else if($rec->sai_student_status =='Left'){
                            //     //$color='#9CC3D5FF';
                            // }else{

                            //     $color='#97BC62Fg';
                            // }
                            if($rec->prog_degree_level=='Undergraduate' && $rec->sai_cgpa < 2 ){
                               // $color='#F95700FF';
                            }
                            if(($rec->prog_degree_level=='Graduate' || $rec->prog_degree_level=='Postgraduate' ) && $rec->sai_cgpa < 2.5){
                               // $color='#D01C1FFF';
                            }


                            ?>
                        <tr style="background-color:<?=$color;?>">
                            <td style="text-align:center;" >{{$count}}</td>
                            <td style="text-align:center;" >{{$rec->sai_id}}</td>
                            <td style="text-align:left;" >{{$rec->sai_enrollment_no}}</td>
                            <td style="text-align:left;" >{{$rec->spi_name}}</td>
                            <td style="text-align:center;" >{{$rec->sai_student_status}} <br> {{$rec->prog_degree_level}}</td>
                            <td style="text-align:center;" >{{$rec->sai_prog_total_credits}}</td>
                            <td style="text-align:center;">{{$rec->sai_transfer_cr}}</td>
                            <td style="text-align:center;">{{$rec->sai_earn_credit}}</td>
                            <td style="text-align:center;">{{$rec->sai_prog_total_credits-$rec->sai_earn_credit}}</td>
                        <!-- -------- CGPA and GPA Print in Row loop start--------- -->
                            @php
                                $row_end = 0;
                            @endphp
                            @foreach($rec_ses as $key)
                            @if($key->sai_enrollment_no == $rec->sai_enrollment_no  )
{{-- <td style="background-color: yellow;">
                            @if($key->ssr_cgpa<3.00 && $rec->prog_degree_level=="Postgraduate")
                                {{$color=$key->ssr_cgpa}}
                            @else
                                00
                            @endif
                        </td> --}}
                            <td style="text-align:center;">

                               <?=($key->ssr_sgpa != '') ? $key->ssr_sgpa : '-';?>

                            </td>
                            <td style="text-align:center;background-color:<?=cgpa_color($key->ssr_cgpa,$rec->prog_degree_level);?>">
                                <?=($key->ssr_cgpa != '') ? $key->ssr_cgpa : '-';?></td>
                            <!-- <td>{{$key->ssr_cgpa}}</td> -->
                               @php
                                   $row_end++;
                               @endphp
                            @endif
                            @endforeach
                            @while ($row_end<$total_sub_td)
                            <td style="text-align:center;"> - </td>
                            <td style="text-align:center;"> - </td>
                                @php
                                   $row_end++;
                                @endphp
                            @endwhile
                        <!-- -------- CGPA and GPA Print in Row loop end--------- -->
                        <?php $count++;
                            $total++;
                            ?>
                            @endif
                            <?php $enrollment=$rec->sai_enrollment_no;?>
                            @endforeach
                            </tr>
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
</div>
@stop
@section('javascript')
<script>
function ExportToExcel(type, fn, dl) {
    var elt = document.getElementById('tbl_exporttable_to_xls');
    var wb = XLSX.utils.table_to_book(elt, { sheet: "sheet1" });
    return dl ?
      XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }):
      XLSX.writeFile(wb, fn || ('MySheetName.' + (type || 'xlsx')));
 }

</script>
@stop
