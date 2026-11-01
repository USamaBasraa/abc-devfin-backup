@extends('layouts.master')
@section('title')
BNU CMS
@stop
@section('mainBody')
<style type="text/css">
    div{
        border:0px solid black;
    }
    </style>
    <?php
    $bookStatus = array("Active","Pending","Update");
    ?>
<div class="row">
    <div class="col-12 col-sm-12 col-lg-12">
        <div class="card">
            <div class="card-header" style="background-color: #2F3B4D;color:white" >
                <div style="float:left;">
                    <h3 style="font-weight: bold">Library Fine Slip</h3>
                </div>
                <div style="float:right">
                    <button type="button" class="btn btn-primary" onclick="searchByCatalog()" >View Circulation</button>
                </div>
            </div>
            <div class="card-body">
                <table class="table"> <?php //print_r($data); ?>
                    <tr>
                        <th> User Id   : </th> <td> {{$data[0]->sai_enrollment_no}} </td>
                    </tr>
                    <tr>
                        <th> Book Name : </th> <td> {{$data[0]->library_title}}  </td>
                    </tr>
                    <tr>
                        <th> Fine      :   </th>      <td> {{$data[0]->lc_fine_total}}  </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button type="button" class="btn-lg btn-success" onclick="window.print()" >Print</button>
                           
                            <button type="button" class="btn-lg btn-success"  onclick="print_pdf('<?=request()->get('id')?>')" >Print PDF</button>

                        </td>
                    </tr>
                </table>
            <div>
        </div>
    </div>
</div>

<button style="width:1252px;display:none" id="issueBtn" type="button" class="btn btn-primary" onclick="issueBook()" >Print Slip</button>

@stop
@section('pageScripts')
<script type="text/javascript">

    function print_pdf(id){

        window.open( "{{URL::to('library/fine/print/pdf?id=')}}"+id, '_blank');
    }

</script>
@stop
