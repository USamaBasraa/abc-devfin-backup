@extends('layouts.master')

@section('title')
    BNU CMS
@stop
@section('mainBody')
    <style type="text/css">
        .form-control1 {
            display: block;
            width: 92%;
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
            margin-left: 20px;
        }
        .dataTables_wrapper .dataTables_scroll
        {
            clear: none;!important;
        }
    </style>


    <div class="card-header bnu-header" id="hidButton"><span style="color:white;">School Assessments</span></div>
    <div class="card-body">

        <div  style="margin-bottom:5px;">
            <button id="model" type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">Add new</button>
        </div>

        <table id="schoolassessment" class="table table-bordered display responsive nowrap" style="width:100%;!important;">
            <thead style="background-color: #2F3B4D;">
            <tr>
                <th class="tab-title">Assessment</th>
                <th class="tab-title">School</th>
                <th class="tab-title-center">Department</th>
                <th class="tab-title-center">Status</th>
                <th class="tab-title-center">Order</th>
                <th class="tab-title-center">Action</th>

            </tr>
            </thead>
            @foreach ($schoolassessment as $row)
                {{-- @if($row->assessment_status=='E'){{'Enable'}} @endif--}}
                {{-- @if($row->assessment_status=='D'){{'Disable'}} @endif--}}
                <tr>
                    <td>{{$row->assessment_name}}</td>
                    <td>{{$row->school_name}}</td>
                    <td>{{$row->dept_name}}</td>
                    <td>
                        @if($row->sc_status=='E'){{'Enable'}}
                        @elseif($row->sc_status=='D'){{'Disable'}} @endif
                    </td>
                    <td>{{$row->sc_order}}</td>
                    <td>

                        <button class="btn btn-primary btn-lg"
                                data-myid     ="{{$row->sc_id}}"
                                data-myname   ="{{$row->assessment_id}}"
                                data-myschool ="{{$row->school_id}}"
                                data-mydept   ="{{$row->department_id}}"
                                data-mystatus ="{{$row->sc_status}}"
                                data-myorder  ="{{$row->sc_order}}"
                                data-toggle   ="modal"
                                data-target   ="#myModal">Edit</button>
                    </td>

                </tr>
            @endforeach
        </table>
    </div>

    <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal" aria-hidden="true" style="">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add School Assessment</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form role="form" class="f1" id="submit" name="submit" method="post" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" class="form-control" id="id"  name="id" value="">

                        <div class="form-group">
                            <label style="padding-top:5px;  margin-left: 20px;" for="Assessment">Assessment</label>
                            <select class="form-control1"  id="assessment_id" name="assessment_id">
                                <option value="">--Select Assessment--</option>
                                @foreach($assessments as $row)
                                    <option value="{{ $row->id }}">{{ $row->assessment_name }}</option>
                                @endforeach
                            </select>
                            <small id="assessment_name_error" class="text-danger" style="display:none;"></small>
                        </div>
                        <div class="form-group">
                            <label style="padding-top:5px;  margin-left: 20px;" for="School">School</label>
                            <select class="form-control1" id="school_id" name="school_id">
                                <option value="">--Select School--</option>
                                @foreach($schools as $row)
                                    <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                                @endforeach
                            </select>
                            <small id="school_name_error" class="text-danger" style="display:none;"></small>
                        </div>
                        <div class="form-group">
                            <label style="padding-top:5px;  margin-left: 20px;" for="Department">Department</label>

                            <select  class="form-control1" id="dept_id" name="dept_id">
                                <option value="">--Select Department--</option>
                                @foreach($departments as $row)
                                    <option value="{{ $row->dept_id }}">{{ $row->dept_name }}</option>
                                @endforeach
                            </select>

                            <small id="dept_name_error" class="text-danger" style="display:none;"></small>
                        </div>
                        <div class="form-group">
                            <label style="padding-top:5px; margin-left: 20px;" for="Status">Status</label> as
                            <select class="form-control1" id="sc_status" name="sc_status">
                                <option value="E" selected="selected">Enable</option>
                                <option value="D">Disable</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="padding-top:5px;  margin-left: 20px;" class="mr-sm-2" for="Order">Order</label>
                            <input type="text" class="form-control1" id="sc_order" placeholder="Order" name="sc_order" >

                        </div>
                        <center>   <div id="div_error" name="div_error" class="text-danger" style="display:none;">Record Already Exist</div></center>
                        <br>
                        <center>  <button class="btn btn-primary btn-lg" type="submit" id="submit">Save</button></center>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop
@section('pageScripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#schoolassessment').DataTable( {
                "order": [[ 5, "asc " ]],
                "fixedHeader": true,
                "scrollY": 350,
                "scrollX": true,

                // "height":10,
                // "paging":   false,
                // "bInfo" : false,
                "columnDefs": [ {
                    "targets": 0,

                    "className": "text-left",
                },{
                    "targets": 1,
                    "className": "text-left",
                },{
                    "targets": 2,
                    "className": "text-left",

                },{
                    "targets": 3,
                    "className": "text-center",
                },{
                    "targets": 4,
                    "className": "text-center",
                },{
                    "targets": 5,
                    "className": "text-center",

                }],
            } );
        } );

        $("#submit").submit(function(e) {
            e.preventDefault();
            var url = "{{url('course/SchoolCriteria/create')}}";
            var formData = new FormData($(this)[0]);

            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: url,
                data: new FormData(this),
                dataType: "json",
                processData: false,
                contentType: false,

                success: function (json) {
                    $('#myModal').modal('hide');
                    window.top.location.reload();

                    //  $('#assessment').DataTable().ajax.reload();
                },
                error: function(data,status, response) {

                    $("#div_error").css('display','block');
                    var errorData = jQuery.parseJSON(data.responseText);
                    var error = errorData.errors;

                    $("#assessment_name_error").css('display','none');
                    $("#school_name_error").css('display','none');
                    $("#dept_name_error").css('display','none');


                    if(error.assessment_id){
                        $("#assessment_name_error").css('display','block');
                        $("#assessment_name_error").html(error.assessment_id);
                    }
                    if(error.school_id){
                        $("#school_name_error").css('display','block');
                        $("#school_name_error").html(error.school_id);
                    }
                    if(error.dept_id){
                        $("#dept_name_error").css('display','block');
                        $("#dept_name_error").html(error.dept_id);
                    }


                }
            });
        });

        $('#myModal').on('hidden.bs.modal', function () {
            $("#assessment_name_error").css('display','none');
            $("#school_name_error").css('display','none');
            $("#dept_name_error").css('display','none');
            $("#div_error").css('display','none');

        });

        $('#myModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id            = button.data('myid')
            var assessment    = button.data('myname')
            var school        = button.data('myschool')
            var department    = button.data('mydept')
            var status        = button.data('mystatus')
            var order         = button.data('myorder')
            var modal = $(this)

            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #assessment_id').val(assessment);
            modal.find('.modal-body #school_id').val(school);
            modal.find('.modal-body #dept_id').val(department);
            modal.find('.modal-body #sc_status').val(status);
            modal.find('.modal-body #sc_order').val(order);
        })

        $('#model').click(function() {
            //$("#div_error").html('');
            $.ajax({
                url: '{{url('course/SchoolCriteria/order')}}/'+id,
                type: "GET",
                dataType: "json",
                success:function(data) {
                    $('#sc_order').val(data);
                },
                error:function() {
                    alert('error');
                }
            });
        });

        $('select[name="school_id"]').on('change', function() {
            var school_id = $(this).val();
            if(school_id) {
                $.ajax({
                    url: '{{url('departments/get/')}}/'+school_id,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        $('select[name="dept_id"]').empty();
                        $('select[name="dept_id"]').append('<option value=""> </option>');
                        $.each(data, function(key, value) {
                            $('select[name="dept_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                        });
                    }
                });
            }else{
                $('select[name="dept_id"]').empty();
            }
        });

    </script>
@stop
