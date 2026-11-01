@extends('layouts.app',['pageTitle' => 'BNU Fines'])
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

@section('content')
<div class="container-fluid">
    <div class="card p-4">
        <form id="submitForm">
            @csrf
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="studentID">Student ID/Reg#:</label>
                    <input type="text" name="studentID" id="studentID" class="form-control" placeholder="Enter Registration #" style="width: 100%;">
                    <h6 data-error-for="studentID" class="validation-error pt-4"></h6>
                </div>
                <div class="form-group col-md-6">
                    <label for="challanNO">Challan #:</label>
                    <input type="text" name="challanNO" id="challanNO" class="form-control" placeholder="Enter Challan #" style="width: 100%;">
                    <h6 data-error-for="challanNO" class="validation-error pt-4"></h6>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="fromDate">From Date:</label>
                    <input type="date" name="fromDate" id="fromDate" class="form-control" placeholder="From Date">
                    <h6 data-error-for="fromDate" class="validation-error pt-4"></h6>
                </div>
                <div class="form-group col-md-4">
                    <label for="toDate">To Date:</label>
                    <input type="date" name="toDate" id="toDate" class="form-control" placeholder="To Date">
                    <h6 data-error-for="toDate" class="validation-error pt-4"></h6>
                </div>
                <div class="form-group col-md-4">
                    <label for="payment">Payment Type:</label>
                    <select name="paymenttype" id="paymenttype" class="form-control">
                        <option value="" selected disabled>--- Select Type ---</option>
                        <option value="blinq_app"  >Blinq</option>
                        <option value="ZindigiAPP"  >Zindagi</option>
                        <option value="UBL">UBL OTC</option>
                        <option value="ubl_card">UBL Master Card</option>
                        <option value="MCB">MCB OTC</option>
                        <option value="Askari">Askari</option>  
                        <option value="KUICKPAY">KuickPay</option>                              
                       
                    </select>
                    <h6 data-error-for="payment" class="validation-error pt-4"></h6>
                </div>
                <div class="form-group col-md-4">
                    <label for="category">Category:</label>
                    <select name="catgoryType" id="catgoryType" class="form-control">
                        <option value="">--- Select Type ---</option>
                        <?php foreach($resSql as $row) {?>               
                            <option value="<?=$row->id?>"  ><?=$row->fc_category_name?></option>
                        <?php } ?>
                    </select>                    
                </div>
            </div>
            <h2 id="successMessage" class="text-success"></h2>
            <div class="row">
                <div class="form-group col-md-6">
                     <button style="float: right;" type="button" onclick="submitForm()" class="btn btn-primary">Submit</button>
                </div>
                <div class="form-group col-md-1">
                     <button style="float: right;" type="button" onclick="reForm()" class="btn btn-primary">Re Sync </button>
                    
                </div>
                <div id="loader" style="display: none;font-weight: 900;font-size: 18px;">Please wait, syncing...</div>
            </div>
            <span style="color:red;font-weight:bold" id="errorContainer" ></span>
        </form>
        <div class="row form-group col-md-6">
        <!-- <button id="downloadExcel" class="btn btn-primary" onclick="downloadExcel()">
            <i class="fa fa-download"></i> Download Excel
        </button> -->
        <button id="downloadExcel" class="btn btn-primary" onclick="downloadExcel2()">
            <i class="fa fa-download"></i> Download Excel 
        </button>
        <button id="downloadExcel" style="margin-left:30px;" class="btn btn-info" onclick="pdf()">
            <i class="fa fa-download"></i> Download PDF
        </button>
        </div>
       
        <table id="studentFinesTable" class="table table-striped table-bordered dt-responsive" style="width:100%">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>StudentID</th>
                    <th>Category</th>
                    <th>Violation Type</th>
                    <th>Challan Amount</th>
                    <th>Fine</th>
                    <th>Bank charges</th>
                    <th>Total Paid</th>
                    <th>Challan Number </th>
                    <th>Paid On</th>
                    <th>Challan Status </th>
                    <th>Validate Date</th>
                    <th>Due Date</th>
                    <th>Type</th>
                    <th>Payement Method</th>
                    <th>Course Name</th>
                </tr>
            </thead>
            <tbody id="event_table_body">
                        </tbody>
        </table>
      
    </div>
</div>
@stop

@section('javascript')
<script>
function pdf(){
    
    stdId = $("#studentID").val();
    challanNo = $("#challanNO").val();
    fromDate = $("#fromDate").val();
    toDate = $("#toDate").val();
    paymentType = $("#paymenttype").val();
    if (paymentType === null || paymentType === "") {
        paymentType = 'undefined';
    }
    catgoryType = $("#catgoryType").val();
    var link = 'https://staff.bnu.edu.pk/HOD/online_payment_list.php?studentID='+stdId+'&challanNO='+challanNo+'&fromDate='+fromDate+'&toDate='+toDate+'&paymentType='+paymentType+'&catgoryType='+catgoryType;
    setTimeout(function(){
        $('.hidegif').hide();
        window.open(
            link,
            '_blank' // <- This is what makes it open in a new window.
            );
        // $('#pdfIcon').show();
    }, 2000);
    // var user_id = $("#staff_for_filter").val()
    //url = '/assets/cms/reports/online_payment_list.php?studentID='+stdId+'&challanNO='+challanNo+'&fromDate='+fromDate+'&toDate='+toDate,
    // url = '/assets/cms/reports/online_payment_list.php',
    // window.open(url, '_blank');

}
function pdf2(){
    
    stdId = $("#studentID").val();
    challanNo = $("#challanNO").val();
    fromDate = $("#fromDate").val();
    toDate = $("#toDate").val();
    // var user_id = $("#staff_for_filter").val()
    //url = '/assets/cms/reports/online_payment_list.php?studentID='+stdId+'&challanNO='+challanNo+'&fromDate='+fromDate+'&toDate='+toDate,
    catgoryType = $("#catgoryType").val();
    url = 'https://staff.bnu.edu.pk/HOD/online_payment_list_excel.php?studentID='+stdId+'&challanNO='+challanNo+'&fromDate='+fromDate+'&toDate='+toDate+'&paymentType='+paymentType+'&catgoryType='+catgoryType;
    window.open(url, '_blank');
}
function downloadExcel2(){
    stdId = $("#studentID").val();
    challanNo = $("#challanNO").val();
    fromDate = $("#fromDate").val();
    toDate = $("#toDate").val();
    paymentType = $("#paymenttype").val();
    if (paymentType === null || paymentType === "") {
        paymentType = 'undefined';
    }
    catgoryType = $("#catgoryType").val();
    url = 'https://staff.bnu.edu.pk/HOD/online_payment_list_excel.php?studentID='+stdId+'&challanNO='+challanNo+'&fromDate='+fromDate+'&toDate='+toDate+'&paymentType='+paymentType+'&catgoryType='+catgoryType;
    window.open(url, '_blank');
}
function downloadExcel() {
    const wb = XLSX.utils.table_to_book(document.querySelector('.table'));
    XLSX.writeFile(wb, 'ApplicantDataReport.xlsx');
}
      function submitForm() {
            $('#studentFinesTable').DataTable().clear().draw();
            $("#event_table_body").empty();

             var studentID   = $('#studentID').val();
             var challanNO   = $('#challanNO').val();
             var fromDate    = $('#fromDate').val();
             var toDate      = $('#toDate').val();
             var paymenttype = $('#paymenttype').val();
             var catgoryType = $('#catgoryType').val();


            $.ajax({
                type: "GET",
                url: '{{ route("get-student-fine-list") }}',
                headers: {
                    Authorization: 'Bearer {{ auth()->user()->api_token }}',
                    Accept: 'application/json'
                },
                data: {
                    studentID: studentID,
                    challanNO: challanNO,
                    fromDate: fromDate,
                    toDate: toDate,
                    paymenttype: paymenttype,
                    catgoryType: catgoryType
                },
                success: function(response) {
                    var html;
                    var cate;
                     response=response.student_challan;
                    response.forEach(function(item) {
                        var dateObject = new Date(item.created_at);
                        var challanNoShow='';
                        // if(item.category_name !=null){
                        //         cate=item.category_name;
                        // } else{
                        //     cate=item.sc_challan_type;
                        // }
                        if(item.sc_challan_type == 'short_Fee'){
                            challanNoShow=item.sc_app_id;
                                
                        } else{
                            challanNoShow=item.sc_feechallanno;
                        }
                       // let payable=item.invoiceamount+(item.fine==null?'':item.fine);
                        let payable = item.invoiceamount ;
                        let fine=(item.fine==null?0:item.fine)
                        
                        // let bankCharges = (item.callback_amount-item.sc_amount).toFixed(2);
                        let bankCharges = '';
                        if(item.sc_paytype == 'blinq_app' || item.sc_paytype == 'ubl_card'){
                            bankCharges = (item.callback_amount-item.sc_amount-item.fine).toFixed(2);
                        }else if (item.sc_paytype == 'KUICKPAY'){
                            bankCharges = 70;
                        } else{
                            bankCharges = 0;
                        }
                        let challanamount = item.sc_amount-item.fine;
                        let totalPaid = '';

                        if (item.sc_paytype === 'KUICKPAY') {
                            totalPaid = (parseFloat(item.callback_amount) + 70).toFixed(2);
                        } else {
                            totalPaid = parseFloat(item.callback_amount).toFixed(2);
                        }
                        var newData = [
                            item.sc_enrollment_no,
                            item.sc_app_id,
                            item.sc_challan_type,
                            item.category_name,
                            item.invoiceamount,
                            fine,
                            bankCharges,
                            totalPaid,
                            challanNoShow,
                            item.callback_at,
                            item.sc_challan_status,
                            item.sc_validate_date,
                            item.duedate,
                            item.sc_paytype,
                            item.sc_payment_mode,
                            item.course_name
            
                           // capitalizeFirstLetter(removeUnderscores(item.event_type))

                        ];
                        $('#studentFinesTable').DataTable().row.add(newData);

                    });
                    $('#studentFinesTable').DataTable().draw();
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        function reForm() {
          
          var studentID   = $('#studentID').val();
          var challanNO   = $('#challanNO').val();
          var fromDate    = $('#fromDate').val();
          var toDate      = $('#toDate').val();
          var paymenttype = $('#paymenttype').val();

          $('#errorContainer').text('');
          document.getElementById("loader").style.display = "block";

         $.ajax({
             type: "GET",
             url: '{{ route("sync-student-list") }}',
             headers: {
                 Authorization: 'Bearer {{ auth()->user()->api_token }}',
                 Accept: 'application/json'
             },
             data: {
                 challanNO: challanNO,
                 fromDate: fromDate,
                 toDate: toDate,
                 paymenttype: paymenttype,

             },
             success: function(response) {
                 document.getElementById("loader").style.display = "none";
                alert("syncing Data Successfully");
             },
             error: function(xhr, status, error) {
                 console.error('Error:', error);
                 var errorResponse = xhr.responseJSON;

                 document.getElementById("loader").style.display = "none";

                var errorMessage = '';
                //console.error('Erroddddr:', errorResponse["errors"]);
                var errors=errorResponse["errors"];
                for (const field in errors) {
                    if (errors.hasOwnProperty(field)) {
                        const errorMessages = errors[field];
                        console.log(`${field}:`);
                        errorMessages.forEach(errorMessage => {
                        $('#errorContainer').text(errorMessage);
                        console.log(`- ${errorMessage}`);
                        });
                    }
                }
        
             }
         });
     }
    $(document).ready(function() {
        submitForm();

        // $('#studentFinesTable').DataTable({
        //     responsive: true,
        //     processing: true,
        //     serverSide: true,
        //     ajax: {
        //         url: '{{ route("get-student-fine-list") }}',
        //         type: 'GET',
        //         data: function (d) {
        //             // d.studentID = $('#studentID').val();
        //             // d.challanNO = $('#challanNO').val();
        //             // d.fromDate = $('#fromDate').val();
        //             // d.toDate = $('#toDate').val();
        //         }
        //     },
        //     columns: [
        //         { data: 'sc_enrollment_no', name: 'sc_enrollment_no' },
        //         { data: 'sess_name', name: 'sess_name' },
        //         { data: 'category_name', name: 'category_name' },
        //         { data: 'violation_type', name: 'violation_type' },
        //         { data: 'sc_amount', name: 'sc_amount' },
        //         { data: 'created_at', name: 'created_at' },
        //         { data: 'entry_username', name: 'entry_username' },
        //         // { data: 'sc_challan_status', name: 'sc_challan_status' },
        //        // { data: 'sc_paid_on', name: 'sc_paid_on' },
        //        // { data: 'sc_feechallanno', name: 'sc_feechallanno' },
        //        // { data: 'sc_validate_date', name: 'sc_validate_date' }
        //     ]
        // });
    });
</script>
@stop
