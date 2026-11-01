<style type="text/css">
    .table_style{
        width: 100%;
        padding: 4px;
        cellspacing:4px;
        text-align: center;

    }
    .div_row_title{

        border: solid 1px #2ca02c;
        width: 100%;
        background-color: #2F3B4D;
        color: white;
        height:35px;
        line-height: 30px;
    }
    .div_row_heading{
        float: left;
        width: 99.5%;
        margin: 1px;
        font-weight: bold;

     }
    .div_row{
        float: left;
        width: 99.5%;
        margin: 1px;
    }
    .div_row_subtitle{
        float: left;
        border: solid 0px #2ca02c;
        margin: 0px;
    }
    .div_icons{
        float: left;
        width: 50px;
    }
</style>
<div class="modal fade" tabindex="-1" role="dialog" id="raw_marks_Modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="model_label"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="raw_marks_detail"> </div>
                <div id="student_course_attendance" style="display: none;"> @include('grader.reportgenerator.student_course_attendance') </div>
            </div>
            <div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="datatable_div">
<table id="example" class="display responsive nowrap" width="100%" cellspacing="0">
    <thead>
       <tr class="div_row_title">
        <td width="35%" align="center">Course Name</td>
        <td width="10%" align="center">Section</td>
        <td width="10%" align="center">Cr.</td>
        <td width="10%" align="center">Attendance</td>
        <td width="10%" align="center">Faculty</td>
        <td width="10%" align="center">Result</td>
     </tr>
    </thead>

    <tbody>
    <?php
    $serial_no = 1;
    foreach ($student_courses as $rec_student_courses){
    ?>
    <tr>
        <td align="left">
            <?=$rec_student_courses->course_code;?>
            <?=$rec_student_courses->course_cd_name;?>
        </td>
        <td align="center">
            <?=$rec_student_courses->sc_pc_section;?>
        </td>
        <td align="center">
            <?=$rec_student_courses->sc_pc_course_cr;?>
        </td>
        <td> <img title="Assignments" src="{{asset('images/attendance.png')}}"  onclick="load_attendance_popup('<?=$rec_student_courses->parent_co_id ;?>','<?=$rec_student_courses->sc_co_id ;?>','<?=$rec_student_courses->sc_sai_enrollment_no;?>')" width="40"></td>
        <td align="center"> <img title="Assignments" src="{{asset('images/faculty.png')}}" onclick="load_faculty_popup(<?=$rec_student_courses->sc_co_id;?>)" width="40"></td>
        <td align="center"> <img title="Assignments" src="{{asset('images/result.jpg')}}" onclick="load_marks_popup('<?=$rec_student_courses->sc_sai_enrollment_no;?>',<?=$rec_student_courses->parent_co_id;?>,<?=$rec_student_courses->sc_co_id;?>)" width="40"></td>
    </tr>
    <?php
    $serial_no++;
    }
    ?>

    </tbody>
</table>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#example').DataTable({
            'responsive': true,
            "searching": false,
            "paging": false,
            "bInfo": false,
            "orderable":true

        });
    });
</script>
<script type="text/javascript">
    // ------------ Function 1 of popup ------------------ popup
    function load_marks_popup(get_enrollment_no,parent_co_id,sc_co_id) {
        $('#raw_marks_Modal').modal('show');
          $.ajax({
                type:'GET',
                url:"{{ URL::route('load_student_marks') }}/"+get_enrollment_no+"/"+parent_co_id+"/"+sc_co_id,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success:function(data){
                    $("#raw_marks_detail").html(data);
                    $("#model_label").html('Course Assessment Marks');
                    $("#student_course_attendance").hide();
                }
            });
    }
    function load_faculty_popup(get_co_id) {
        $('#raw_marks_Modal').modal('show');
        $.ajax({
            type:'GET',
            url:'{{ URL::route('load_faculty') }}/'+get_co_id,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(data){
                $("#raw_marks_detail").html(data);
                $("#model_label").html('Course Faculty');
                $("#student_course_attendance").hide();
            }
        });
    }
    function load_attendance_popup(parent_co_id,course_co_id,enrollment_no){
        $('#raw_marks_Modal').modal('show');
        $("#model_label").html('Student Course Attendance');
        $("#raw_marks_detail").html('');
        $("#student_course_attendance").show();
        var  url ='{{url('load_student_attendance')}}/'+ parent_co_id +"/"+course_co_id+"/"+ enrollment_no;
        var table = $('#StudentAttendanceTable').DataTable();
        table.destroy();
        $('#StudentAttendanceTable').DataTable({
            'responsive': true,
            "searching": false,
            "paging": false,
            "bInfo": false,
            "orderable":true,
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o fa-m" style="color: red"></i> PDF',
                    titleAttr: 'Export to PDF',
                    pageSize: 'A4',
                    orientation: 'portrait',
                    className: 'btn btn-default',
                    extension: ".pdf",
                    header: true,
                    body: true,
                    footer: true,
                    filename: 'Attendance Report',
                    title : "",
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6],
                        columns: ':visible'
                    },
                    customize: function (doc) {
                        doc.styles.tableHeader.fillColor = 'slategray';
                        doc.defaultStyle.alignment = 'center';
                        doc.styles.tableHeader.fontSize = 15;
                        doc.styles.title.fontSize = 15;
                        var objLayout = {};
                        objLayout['hLineWidth'] = function(i) { return .8; };
                        objLayout['vLineWidth'] = function(i) { return .5; };
                        objLayout['hLineColor'] = function(i) { return '#aaa'; };
                        objLayout['vLineColor'] = function(i) { return '#aaa'; };
                        objLayout['paddingLeft'] = function(i) { return 8; };
                        objLayout['paddingRight'] = function(i) { return 8; };
                        doc.content[0].layout = objLayout;
                        doc.content.splice(0, 0, {
                            text: [
                                {text: enrollment_no ,decoration:'underline',color:'midnightblue',bold:true,fontSize: 15}
                            ],
                            // margin: [left, top, right, bottom]
                            margin: [10, 20, 0, 15],
                            alignment: 'center'
                        });
                        doc.content.splice(0, 0, {
                            text: [
                                {text: 'Attendance Report',decoration:'underline',color:'midnightblue',bold:true,fontSize: 25}
                            ],
                            // margin: [left, top, right, bottom]
                            margin: [10, 40, 0, 20],
                            alignment: 'center'
                        });
                        doc['header'] = (function (page, pages) {
                            return {
                                columns: [
                                    {
                                        image: 'data:image/png;base64,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',
                                        width: 50,
                                        height: 65,
                                        alignment: 'left',
                                        margin: [20,0,0,0]
                                    },
                                    [
                                        {
                                            text: 'Beaconhouse National University',
                                            bold: true,
                                            fontSize: 12,
                                            alignment: 'left',
                                            height: 30,
                                            margin: [20,0,0,0]
                                        },
                                        {
                                            text: '13-km Raiwind Road, Lahore 54400, Pakistan',
                                            bold: true,
                                            fontSize: 10,
                                            alignment :'left',
                                            margin: [20,0,0,0]
                                        },
                                        {
                                            text: 'Tel: 92-42-35975705-10 Fax:(92) 42-5760254 Email: info@bnu.edu.pk',
                                            bold: true,
                                            fontSize: 10,
                                            height: 30,
                                            alignment :'left',
                                            margin: [20,0,0,0]
                                        }
                                    ]

                                ]
                            }
                        });
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o fa-o" style="color: green"></i> Excel',
                    titleAttr: 'Export to Excel',
                    title: 'BeaconHouse National university',
                    messageTop: 'BeaconHouse National University',
                    filename: 'Attendance Report',
                    className: 'btn btn-default',
                    extension: ".xlsx"
                }
            ],
            "ajax": {
                "url": url,
                "dataSrc": ""
            },
            "columnDefs": [
                {
                    "targets": 1, // your case first column
                    "className": "text-center"
                },
                {
                    "targets": 2,
                    "className": "text-center"
                },
                {
                    "targets": 3,
                    "className": "text-center"
                },
                {
                    "targets": 4,
                    "className": "text-center"
                },
                {
                    "targets": 5,
                    "className": "text-center"
                },
                {
                    "targets": 6,
                    "className": "text-center"
                }
            ],
            "columns": [
                {data: 'course_cd_name'},
                {data: 'co_total_classes'},
                {data: 'total_classes_held'},
                {data: 'total_student_present'},
                {data: 'total_absent'},
                {data: 'total_exmp'},
                {"mRender": function (data, type, row) {
                    var total_presents = parseInt(parseInt(row.total_student_present) + parseInt(row.total_exmp));
                    var total_held = parseInt(row.total_classes_held);
                    var prsent_percentage = total_presents*100/total_held;
                    if (isNaN(prsent_percentage))
                    {
                        return '0%'
                    }else{
                        return Math.round(prsent_percentage)+ '%';
                    }
                }}

            ]
        });
    }
</script>
