@extends('layouts.app',['pageTitle' => 'Spine and Barcode'])

@section('title')
    BNU CMS
@stop
@section('content')
<?php
$item_typ_array =array("Book", "Reference Book", "Art Catalog", "Report", "Journal");
$library_typ_array =array("Main Library", "SVAD Resource Center");
$language_array = array("Arabic","English","Pubjabi","Urdu");

?>

<div class="row">
    <div class="col-12 col-sm-12 col-lg-12">
        <div class="card">
            {{-- <div class="card-header">
                <h3>Add Book</h3>
            </div> --}}
            <div class="card-body">       
                   <form enctype="multipart/form-data" method="post" name="library_items"  id="library_items">
                {{ csrf_field() }}                        
                    <div class="row">
                        <div class="col-3 col-sm-3 col-lg-3 form-group">
                            <label for="school_sel">Library</label>
                            <select name="school" id="school" class=" form-control">
                                <option value="">--Select--</option>
                                <option value="main">Main Library</option>
                                <option value="SMMC">SMMC</option>
                                <option value="DGHC">DGHC</option>
                                <option value="REPORTS">REPORTS</option>
                                <!-- @foreach ($schools as $row) 
                                    <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                                @endforeach  -->
                            </select>
                        </div>
                        <div class="col-3 col-sm-3 col-lg-3 form-group">
                            <label for="accession_number">Accession Number<span style="color:red">*</span></label>
                            <input type="text" class="form-control" id="accession_number" name="accession_number" aria-describedby="accessionNumber" placeholder="Accession Number" >
                        </div>    
                    </div>   
                    <div>
                        <button type="button" onclick="goToPDF()" class="btn btn-info">Spine</button>  
                        <button type="button" onclick="goToBarcode()" class="btn btn-info">Barcode</button>                   
                    </div>                        
                </form>            
            </div>
        </div>
    </div>
</div>

@stop


@section('javascript')
<script type="text/javascript">
       function goToPDF(){
        var base_url='<?=url("/") ?>';
        //alert(base_url);
        var link = base_url+"/assets/cms/reports/spine_report.php?accession_number="+$('#accession_number').val()+"&type="+$('#school').val();
      //  window.location = link;
        window.open(link);
    }
    function goToBarcode(){
        var base_url='<?=url("/") ?>';
       var link = base_url+"/assets/cms/reports/barcode_report.php?accession_number="+$('#accession_number').val()+"&type="+$('#school').val();
     //  window.location = link;
       window.open(link);
   }
</script>
@stop