{{-- @extends('layouts.app') --}}

@section('title')
    BNU CMS
@stop
@section('content')
<?php
$countRow = count($view_criteria);

?>
<style type="text/css">
	.headingtd {
		border:0px solid #000;
		background-color:#999;
		font-weight:bold;
	}
	.cntr {
		text-align:center;	
	}
    table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
  border: 1px solid #ddd;
}

 td {
  text-align: left;
  padding: 16px;
}

tr:nth-child(even) {
  background-color: #D3D3D3;
}
</style>
<div>
    <table class="table"  align='center' cellspacing="0" >   
	<?php
	if($countRow){
	?>		
	  <tr><td colspan="12" height="34" style="border:none;"  bgcolor="#D2D6F1" align="left"><h3 style="font-weight: bold;color:#6774CF">Library Book Criteria</h3></td></tr>
      <tr>
        <th style="text-align: center;font-weight:bold" valign="middle" width="70">Sr #</th>
        <th style="text-align: center;font-weight:bold" width="200">Category</th>
        <th style="text-align: center;font-weight:bold" width="340">Limit</th>
        <th style="text-align: center;font-weight:bold" width="340">Fine Per Day</th>
        <th style="text-align: center;font-weight:bold" width="340">Book Lost Fine</th>      
        <th style="text-align: center;font-weight:bold" width="340">Duration Issuance</th>
        <th style="text-align: center;font-weight:bold" width="340">Reissuance Policy</th>
        <th style="text-align: center;font-weight:bold" width="340">Action</th>
       
        
        
    </tr>  
       <?php   
            $index = 1;
        foreach($view_criteria as $row) {
        ?>	
       
        <tr>
            <td style="text-align: center" height="28"><?=$index?></td>
            <td align="left">&nbsp;&nbsp;<?=$row->lbc_categories?></td>
            <td align="center">&nbsp;&nbsp;<?=$row->lbc_limit?></td>
            <td style="text-align: center"><?=$row->lbc_fine_per_day?></td>
            <td style="text-align: left"><?=$row->lbc_book_lost?></td>       
            <td style="text-align: center"><?=$row->lbc_duration_issuance?></td>
            <td style="text-align: center"><?=$row->lbc_reissuance_policy?></td>
            <td style="text-align: center"><button type="button" class="btn btn-primary" onclick="updCriteria('<?=$row->id?>')" >Update</button></td>
        </tr>
	    <?php				
		$index++;				
		}//foreach
	    ?>
		 
		</table>
	<?php			
	}

	?>
        
</div>






@section('javascript')
<script type="text/javascript">

    function updCriteria(id){
        //alert(id);
        var url = '<?=url("library/get-library-criteria")?>/'+id;
        $.ajax({
        url: url,
        type: "get",
        //data: values ,
        success: function (response) {
           // categories,limit,fine,book_lost,duration,reissuance_poilicy
           // console.log( response['0']['lbc_book_lost'])       
         
           $('#updatebtn').show();
           $('#submitbtn').hide();       
            $('#categories').val(response['0']['lbc_categories'])
            $('#limit').val(response['0']['lbc_limit'])
            $('#fine').val(response['0']['lbc_fine_per_day'])
            $('#book_lost').val(response['0']['lbc_book_lost'])
            $('#duration').val(response['0']['lbc_duration_issuance'])
            $('#reissuance_poilicy').val(response['0']['lbc_reissuance_policy'])
            $('#lbc_id').val(response['0']['id'])
           // You will get response from your PHP page (what you echo or print)
        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }
    });
        
    }


</script>


