@extends('layouts.app',['pageTitle' => ''])

@section('title')
    BNU CMS
@stop
@section('content')



<div class="row"  style="margin-top:10px;margin-right: 20px !important;width:99%;border-radius:10px">
    <div class="col-12 col-sm-12 col-lg-12">
        <div class="card ">
            <div class="card-header bg-primary" style="color:white !important;border-top-right-radius:10px;border-top-left-radius:10px">
                <div style="float:left">
                    <h3 class="card-title">Daily Library Visitor</h3>
                </div>
            </div>
            <div class="card-body text-center">       
                <form id="formData2">
                    <div class="row" id="type_desc_button">
                        <div class="col-sm-6">
                            <label for="completion_date"> <b>Visting Date:</b> </label>
                            <input type="date" name="date" class="form-control" id="date">
                        </div>
                        <div class="col-sm-6">
                            <label for=""> <b>Number Of Students:</b> </label>
                            <input type="Number" name="number" class="form-control" id="number">
                        </div>   
                    </div>
                    <div class="row" id="type_desc_button">
                        <div class="col-sm-12">
                            <!-- <input class="btn btn-primary mt-5 " type="Submit"  class="form-control" onclick="submitBtn" > -->
                            <button class="btn btn-primary mt-5 " type="button" id="submitBtn" >Submit</button>
                        </div>
                    </div>
                </form>               
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin-top:10px;margin-right: 20px !important;width:99%;border-radius:10px"> 
    <div class="col-12 col-sm-12 col-lg-12"> 
        <div class="card-header bg-primary" style="color:white !important;border-top-right-radius:10px;border-top-left-radius:10px">
            <div style="float:left">
                <h3 class="card-title">Visitors Details</h3>
            </div>
        </div>
        <div class="card text-center">     
            <table class="table table-striped">
                <thead>
                    <tr>
                        <td><b>Sr #</b></td>
                        <th><b>Date</b></th>
                        <th><b>No of Students</b></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(count($libaryData)>0){
                    $sr_no = 1;?>
                    @foreach($libaryData as $row)
                        <tr>             
                            <td>{{$sr_no++}}</td>
                            <td>{{substr("$row->ldv_date",0,10)}} </td>
                            <td>{{$row->ldv_nof_students}}</td>   
                        </tr>
                    @endforeach
                    <?php } ?>
                </tbody>
            </table>   
        </div>
    </div>  
</div>

@stop

@section('javascript')
<script>
    // $(document).ready(function(){
        $('#submitBtn').click(function(e)
        {  
         

            var bi_form = document.querySelector('#formData2');
            var formData = new FormData(bi_form);
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var url = '<?=url("library/store_daily_library_vistitor")?>';
            $.ajax({                
                url:url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false, // Ensure FormData is not processed
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                }
            });
        });
    // });
</script>


@stop