
<!----------  MODEL 1 START---------------------->
 <div class="modal fade" id="exampleModalCenter1223" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:75%!important;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Lecture Content</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" name="tt_id" id ="tt_id">


                        <div class="form-group"  style="width:96%!important;margin-left: auto!important;margin-right: auto!important;height:400px ;overflow-y: scroll;scrollbar-color: green white; scrollbar-width: thin;">
                            <table  class="table card-table table-bordered" >
                                <tr class="row">
                                    <th class="col-12 col-sm-1 col-md-1 col-lg-1 col-xl-1">Sr#</th>
                                    <th class="col-12 col-sm-9 col-md-9 col-lg-9 col-xl-9">Contents</th>
                                    <th style="text-align: center"  class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2">Action</th>
                                </tr>
                                <tr class="row">
                                    <?php
                                    $lec_no = 0;
                                    foreach($content as $row){
                                    if($row->cc_type == "content"){

                                    ?>
                                    <td class="col-12 col-sm-1 col-md-1 col-lg-1 col-xl-1"> {{$lec_no}}</td>
                                    <td class="col-12 col-sm-9 col-md-9 col-lg-9 col-xl-9">
                                        <p class="text-justify"> {{$row->cc_lecture_content}}</p>
                                        <textarea style="display: none" id="content{{$row->coc_id}}{{$row->type}}">{{$row->cc_lecture_content}}</textarea>
                                    </td>

                                    <?php if($row->coc_continuation >= 1 and $row->coc_covered >= 1){ ?>
                                    <td style="text-align: center" class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                                   <a href="#" id="add" class="btn btn-success btn-sm active" data-toggle="modal" data-target="#ContentModal"
                                           style="padding: 5px 24px;" role="button" aria-pressed="true" onclick="lecturecontent('{{$row->cc_lecture_no}}','{{$row->coc_id}}','{{$row->type}}','{{$row->coc_content_type}}')"> <i class="fe fe-check mr-2"></i> Covered</a>
                                    </td>
                                    <?php } else if($row->coc_continuation >= 1 and $row->coc_covered == 0) { ?>
                                    <td style="text-align: center" class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                                        <a href="#" id="add" class="btn btn-warning btn-sm active" data-toggle="modal" data-target="#ContentModal"
                                           style="padding: 5px 24px;" role="button" aria-pressed="true" onclick="lecturecontent('{{$row->cc_lecture_no}}','{{$row->coc_id}}','{{$row->type}}','{{$row->coc_content_type}}')">Continuation</a>
                                    </td>
                                    <?php } else { ?>
                                    <td style="text-align: center" class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                                        <a href="#" id="add" class="btn btn-primary btn-sm active" data-toggle="modal" data-target="#ContentModal"
                                           style="padding: 5px 24px;" role="button" aria-pressed="true" onclick="lecturecontent('{{$row->cc_lecture_no}}','{{$row->coc_id}}','{{$row->type}}','{{$row->coc_content_type}}')">Add</a>
                                    </td>
                                <?php } ?>
                                <?php
                                }
                                $lec_no++;
                                }
                                ?>
                            </table>
                            </tr>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#AddNewContentModal" onclick="addnewcontent()" >Add New</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    {{--<button type="button" class="btn btn-primary">Save changes</button>--}}
                </div>
            </div>
        </div>
    </div>


    <div class="modal" id="AddNewContentModal" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="h5 modal-title" id="ModalTitle">Add Lecture Content Description</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" method="post" name="content_form"  id="add_new_content_form">
                        @csrf
                        <input type="text" id="tt_new_content_id" name="tt_new_content_id">
                        <input type="text" name="co_id"  value="{{$co_id}}">
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Description</label>
                            <textarea class="form-control" id="add_new_desc" name="add_new_desc" rows="3"></textarea>
                        </div>

                        {{--<textarea id="getcontent" name="getcontent" rows="4" cols="72"></textarea>--}}
                    </form>
                </div>
                <div class="modal-footer">
                    <div id="saved_msg_div_new_content" style="display: none;color: green"><span>Data Update Successfully</span></div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="updtnewcontent()">Update</button>
                </div>
            </div>
        </div>
    </div>
    {{--Add New Content Modal End Here--}}
    {{--Content Modal--}}
    <div class="modal" id="ContentModal" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="h5 modal-title" id="ModalTitle">Add Lecture Content</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data" method="post" name="content_form"  id="content_form">
                        @csrf
                        <input type="text" id="cc">
                        <input type="text" id="cc_id" name="cc_id">
                        <input type="text" id="coc_content_type" name="coc_content_type">


                        <input type="hidden" id="mtt_co_id" name="mtt_co_id">

                        <div class="form-group" id="content_div">
                            <label for="exampleFormControlInput1">Content</label>
                            <input type="text" class="form-control" id="getcontent" name="getcontent" readonly="true"  placeholder="Title Here . . .">
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlInput1">Description</label>
                            <textarea class="form-control" id="desc" name="desc" rows="3"></textarea>
                        </div>

                        {{--<textarea id="getcontent" name="getcontent" rows="4" cols="72"></textarea>--}}
                    </form>
                </div>
                <div class="modal-footer">
                    <div id="saved_msg_div" style="display: none;color: green"><span>Data Update Successfully</span></div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="ContentUpdate()">Update11</button>
                </div>
            </div>
        </div>
    </div>
