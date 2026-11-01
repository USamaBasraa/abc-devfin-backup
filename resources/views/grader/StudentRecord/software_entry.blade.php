
@extends('layouts.app')
@section('title')
    BNU CMS
@stop
@section('content')
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

<style>
.invisible { display: none; }
</style>
<?php
// print_r($Scholarship_Type);exit;
// $sch_type = array("Need Base","Merit Base","BSS Emp","BSS Emp Ward","BNU Emp","BNU Emp Ward","Nest","UMISAA","SHEHLA Siagol");
?>

<div class="card">
  <div class="card-header" style="display:inline">
    <div style="float:left">
        <h3 class="card-title">Software Entry List </h3>
    </div>
  </div>
  <div class="card-body">
    <form id="studentRecord" method="post" onsubmit="event.preventDefault(); autoSearch(this);">
        
        
        
            
        <div class="form-group row" style="margin-bottom:0px;">
            <div class="col-lg-3" style="text-align: right">
                <label class="col-form-label">Software Name</label>
            </div>
            <div class="col-lg-3">
                <input type="text" class="form-control" name ="software_name" id ="software_name" >
            </div>
        
        </div>
        <div class="form-group row" style="margin-bottom:0px;">
            <div class="col-lg-3" style="text-align: right">
                <label class="col-form-label">Software Desc</label>
            </div>
            <div class="col-lg-3">
                <input type="text" class="form-control" name ="software_desc" id ="software_desc" >
            </div>
        
        </div>
        <div class="row" style="margin-top:20px;">
            <div class="col-lg-3">
            </div>
            <div style="text-align:center;" class="col-lg-3">
                <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>
            </div>
            <span id="successMsg"></span>
        </div>
    </form>
    <table class="table table-striped" width="100%" id="tbl_exporttable_to_xls">
        <button id="downloadExcel" class="btn btn-primary" onclick="downloadExcel()">
            <i class="fa fa-download"></i> Download Excel
        </button>&nbsp
     
      <thead>
          <tr>
            <th colspan="9"></th>
          <tr>
            <th style="text-align:center;" scope="col">Sr.No</th>
            <th style="text-align:center;" scope="col">Software Name</th>
            <th style="text-align:center;" scope="col">Software Description</th>
        
            <th style="text-align:center;" scope="col">Action </th>
          </tr>
      </thead>
      <tbody id="result-body" >
          <?php $count = 1; ?>
            @foreach($studentRecord as $row)
                <tr>
                    <td  style="text-align:center;" >{{$count++}}</td>
                    
                    <td style="text-align:left;" >{{$row->facility_name}}</td>
                    <td style="text-align:center;" >{{$row->facility_desc}}</td>
                    
                    <td style="text-align:center;" ><button type="button" class="btn btn-primary" onclick="deleteRow('<?=$row->facility_id?>')">Delete</button></td>
                </tr>
            @endforeach
      </tbody>
  </table>

  </div>
</div>
@stop
@section('javascript')
<script src="<?=asset('js/bootstrap-autocomplete.js')?>"></script>
<script type="text/javascript">

function autoSearch(ele){
   alert('aaaaaaaaa');
   $('#filter_message').hide();
   var bi_form = document.querySelector('#filterForm');
   var fd      = new FormData(bi_form);

   if( (fd.get('username') == '') && (fd.get('accesstion_user') == '') ){
       $('#filter_message').show();
       $('#filter_message').html('No Record Found!');
       return false;
   }

   $('#user-search-btn').html('Searching...');

   sendPostRequest('library/search-by-filter',function(res){
       $('#user-search-btn').html('Load Detail');
       if(res.username){
           var username = res.username;
           $('#sai_enrollment_no').val(username);
           $('#sisuserUsername').val(username);
           createProfileView(res);
           loadUserBookIssuedHistory(username);
       }else{

           $('#filter_message').show();
           $('#filter_message').html('No Record Found!');
       }
   },fd,function(error){
       $('#user-search-btn').html('Load Detail');
       console.log('error : ',error);
   });
   return false;
}

function downloadExcel2(){

    url = 'http://devstaff.bnu.edu.pk/HOD/student_record_excel.php?sess_id='+$('#academic_session').val(),
    window.open(url, '_blank');
}

function getProfile(){

   var rol_no = $('#sai_enrollment_no').val();
   // if you want to show alert msg at the top write this (.message_div) as second parem in sendGetRequest
   $.ajax({
       url: '{{route("get-student-profile")}}/'+rol_no,
       type: 'GET',
       success: function(res) {
           // Set a timeout to close the loader after two seconds
           if(res.message == 1){
           $('#sch_type').removeAttr("disabled");
           $('#sch_percentage').removeAttr("disabled");
           $('#sch_details').removeAttr("disabled");
           var tbody = '';
           $(res.data).each(function(index,row){
               $('#student_name').html('<b>Name: </b>'+row.spi_name);
               $('#school_name').html('<b>School: </b>'+row.school_name);
               $('#dept_name').html('<b>Department: </b>'+row.dept_name);
           });
       }else{
           $('#student_name').html('<b>Kindly Enter Valid Enrollment No');
           $('#school_name').html('');
           $('#dept_name').html('');
           $('#sch_type').attr('disabled', true);
           $('#sch_percentage').attr('disabled', true);
           $('#sch_details').attr('disabled', true);
       }

       },
       error: function(xhr, status, error) {
           // Request failed, handle the error
           console.log("Error: " + error);
       }
   });

}
function submitForm() {
    var bi_form = document.querySelector('#studentRecord');
    var data = new FormData(bi_form);
    sendPostRequest('', '', 'save-software-record', function(res) {
        if(res == 1){
            $('#successMsg').css('color', 'green');
            $('#successMsg').html('Record Entered Successfully');
            setTimeout(function(){
            $('#successMsg').html('');
            $("#studentRecord")[0].reset();
            location.reload();
            },1000);
        }
    }, data);
}

function deleteRow(id){

    sendPostRequest('', '', 'delete-software-record/'+id, function(res) {
        //console.log('Hello Baby', res);
        //alert(res.status);
        if(res == 1){
            location.reload();
        }
    },'');
}

$('#sai_enrollment_no').autoComplete({
resolver: 'custom',
autoSelect: false,
formatResult: function (item) {
                return {
                    value: 'hello ap',
                    text: item.text + "[" + item.value + "] ",
                    // html: [
                    // 		$('<img>').attr('src', item.icon).css("height", 18), ' ',
                    // 		item.text
                    // 	]
                };
            },
            select : function (evt, item){
    console.log('Hello Ahmed',item);
},
events: {
    search: function (qry, callback) {
        // let's do a custom ajax call
        $.ajax(
            '<?=url('api/library/search-user-for-suggestion')?>',
            {
                data: { 'qry': qry}
            }
        ).done(function (res) {
            console.log(res);
            callback(res)
        });
    }

}
});

$('#sai_enrollment_no').on('autocomplete.select', function (evt, item) {
					// $('.basicAutoCompleteSelected').html(JSON.stringify(item));
					// $('.basicAutoCompleteCustom').html('');
                    $('#sai_enrollment_no').val(item.value);
});


function downloadExcel() {
    // Select the table element
    const table = document.querySelector('.table');

    // Clone the table to avoid modifying the original table
    const clonedTable = table.cloneNode(true);

    // Remove the rightmost column (ACTION)
    const rows = clonedTable.querySelectorAll('tr');
    rows.forEach(row => {
        const lastCellIndex = row.cells.length - 1; // Index of the last cell
        row.deleteCell(lastCellIndex); // Remove the last cell (rightmost column)
    });

    // Convert the modified table to an Excel workbook
    const wb = XLSX.utils.table_to_book(clonedTable);

    // Download the Excel file
    XLSX.writeFile(wb, 'student-record.xlsx');
}

</script>
@stop

