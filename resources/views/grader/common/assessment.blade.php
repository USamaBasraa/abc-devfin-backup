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


    <div class="card-header bnu-header" id="hidButton"><span style="color:white;">Assessment Type Form</span></div>
    <div class="card-body">

        <div  style="margin-bottom:5px;">
            <button id="model" type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">Add new</button>
        </div>

    <table id="assessment" class="table table-bordered display responsive nowrap">
        <thead style="background-color: #2F3B4D;">
        <tr>
            <th class="tab-title">Name</th>
            <th class="tab-title">Desc</th>
            <th class="tab-title-center">Status</th>
            <th class="tab-title-center">Short Name</th>
            <th class="tab-title-center">Order</th>
            <th class="tab-title-center">Action</th>

        </tr>
        </thead>
        @foreach ($assess as $row)
           {{-- @if($row->assessment_status=='E'){{'Enable'}} @endif--}}
           {{-- @if($row->assessment_status=='D'){{'Disable'}} @endif--}}
            <tr>
                <td>{{$row->assessment_name}}</td>
                <td>{{$row->assessment_desc}}</td>
                <td>
                    @if($row->assessment_status=='E'){{'Enable'}}
                    @elseif($row->assessment_status=='D'){{'Disable'}} @endif
                </td>
                <td>{{$row->assessment_short_name}}</td>
                <td>{{$row->assessment_order}}</td>
                <td>

                    <button class="btn btn-primary btn-lg"
                            data-myid    ="{{$row->id}}"
                            data-myname  ="{{$row->assessment_name}}"
                            data-mydesc  ="{{$row->assessment_desc}}"
                            data-mystatus="{{$row->assessment_status}}"
                            data-myshortname="{{$row->assessment_short_name}}"
                            data-myorder ="{{$row->assessment_order}}"
                            data-toggle  ="modal"
                            data-target  ="#myModal">Edit</button>
                </td>
            </tr>
        @endforeach
    </table>
    </div>


    <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal" aria-hidden="true" style="">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Assessment Form</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form role="form" class="f1" id="submit" name="submit" method="post" enctype="multipart/form-data">
                    @csrf

                        <input type="hidden" class="form-control" id="id"  name="id" value="">
                        <div class="form-group">
                            <label style="padding-top:5px;" for="assessment_name">Name</label>
                            <input type="text" class="form-control" id="name"  placeholder="Assessment Name" name="assessment_name" >
                            <small id="assessment_name_error" class="text-danger" style="display:none;"></small>
                        </div>
                        <div class="form-group">
                            <label style="padding-top:5px;" for="assessment_desc">Description</label>
                            <input type="text" class="form-control" id="desc"  placeholder="Assessment Desc" name="assessment_desc">
                            <div class="hid" id="hid"> <small id="assessment_desc_error" class="text-danger" name="assessment_desc_error" style="display:none;"></small></div>
                        </div>
                        <div class="form-group">
                            <label style="padding-top:5px;" for="assessment_status">Status</label>
                            <select class="form-control" id="status" name="assessment_status">
                                <option value="E" selected>Enable</option>
                                <option value="D">Disable</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="padding-top:5px;" for="assessment_desc">Short Name</label>
                            <input type="text" class="form-control" id="shortname"  placeholder="Short Name" name="assessment_short_name">

                        </div>
                        <div class="form-group">
                            <label style="padding-top:5px;" class="mr-sm-2" for="assessment_order">Order</label>
                            <input type="text" class="form-control" id="order" placeholder="Assessment Order" name="assessment_order" >
                            <small id="assessment_order_error" class="text-danger" style="display:none;"></small>
                        </div>
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
            $('#assessment').DataTable( {
                "order": [[ 4, "asc " ]],
                "fixedHeader": true,
                "scrollY": 350,
                "scrollX": true,
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
                }, {
                    "targets": 5,
                    "className": "text-center",

                }],
            } );
        } );

        $("#submit").submit(function(e)
        {
            e.preventDefault();
            var url = "{{url('course/assessment/create')}}";
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
                    var errorData = jQuery.parseJSON(data.responseText);
                    var error = errorData.errors;
                    $("#assessment_name_error").css('display','none');
                    $("#assessment_desc_error").css('display','none');
                    $("#assessment_order_error").css('display','none');
                    if(error.assessment_name){
                        $("#assessment_name_error").css('display','block');
                        $("#assessment_name_error").html(error.assessment_name);
                    }
                    if(error.assessment_desc){
                        $("#assessment_desc_error").css('display','block');
                        $("#assessment_desc_error").html(error.assessment_desc);
                    }
                    if(error.assessment_order){
                        $("#assessment_order_error").css('display','block');
                        $("#assessment_order_error").html(error.assessment_order);
                    }
                    }
            });
        });

        $('#myModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id          = button.data('myid')
            var name        = button.data('myname')
            var description = button.data('mydesc')
            var status      = button.data('mystatus')
            var shortname      = button.data('myshortname')
            var order       = button.data('myorder')
            var modal = $(this)

            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #name').val(name);
            modal.find('.modal-body #desc').val(description);
            modal.find('.modal-body #status').val(status);
            modal.find('.modal-body #shortname').val(shortname);
            modal.find('.modal-body #order').val(order);
        })

        $('#myModal').on('hidden.bs.modal', function () {
            $("#assessment_name_error").css('display','none');
            $("#assessment_desc_error").css('display','none');
            $("#assessment_order_error").css('display','none');
        });

        $('#model').click(function() {
            $.ajax({
                url: '{{url('course/assessment/order')}}/'+id,
                type: "GET",
                dataType: "json",
                success:function(data) {
                    $('#order').val(data);
                },
                error:function() {
                    alert('error');
                }
            });
        });


    </script>
@stop



