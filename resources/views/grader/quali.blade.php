
@extends('theme.bgTemplate')


@section('form_content')
<?php

//echo $sch_id.'======='.$uid;
//echo count($result);exit;

//dd($result);exit;
// foreach($result as $row){
// echo $row->aq_degree_name;
// echo "====".$row->aq_id;
// echo "====".$row->aq_degree_name.'<br>';
// }
?>

<style>
    .form-check-input{
        margin-left:0px !important;
    }
    .span:hover{
            color:red;

    }
    .btn-outline-info:hover{
        color:white !important;
    }
    div {
        /* border: solid 0px red; */

        /* border: 2px solid black;
  padding: 10px; */


    }
    .custom{
        padding-left: 2px;
        padding-right: 2px;
        padding-top: 2px;
        padding-bottom: 2px;

    }
</style>
<!-- Page body start -->
    <!--Main-body start -->
<!--jjjj Main-body start -->
<div class="main-body">
    <div class="page-wrapper">
        <style>
            .form-check-input {
                margin-left: 0px !important;
            }

            .span:hover {
                color: red;

            }

            .btn-outline-info:hover {
                color: white !important;
            }
        </style>
        <!-- Page body start -->
        <div class="page-body">
            <div class="row">
                <div class="col-sm-12">

                    <!-- Form Basic Wizard card start -->
                    <div class="card">
                        <div class="card-header">
                            <h5 style="margin-right:20%">Applied For :&nbsp;&nbsp;<i style="color:#003459" class="fa fa-hand-o-right fa-2x"></i>&nbsp;&nbsp;<span class="badge btn-outline-info " style="padding: 14px 7px;border-radius:4px ;border:1px solid #024f78;font-size:14px;font-family:bold">{{$obj_get_queryof[0]->prog_name}}</span> </h5>
                            <button align="center" type="button" class="btn btn-outline-info" style="font-size:14px;font-family:bold;padding: 10px 90px;" data-toggle="modal" data-target="#large-Modal">Admission Instruction</button>

                            {{--<div class="card-header-right">--}}
                            {{--<i class="icofont icofont-rounded-down"></i>--}}
                            {{--<i class="icofont icofont-refresh"></i>--}}
                            {{--<i class="icofont icofont-close-circled"></i>--}}

                            {{--</div>--}}
                        </div>

                        @include('applicantform.guideline')

                        <div style="margin-top:25px; margin-bottom:25px; z-index:0;">

                            <!-- progressbar -->
                            <ul id="progressbar">
                                <li class="active" id="account"><strong style="margin-left:140px;" class="wizard">Personal Information</strong></li>
                                <li class="active" id="personal"><strong style="margin-left:140px;" class="wizard">Qualification Details</strong></li>
                                <li id="payment"><strong style="margin-left:140px;" class="wizard">Health Information</strong></li>

                            </ul>

                        </div>
                        <div id="loader" style="display: none; margin-left: auto; margin-right: auto; text-align: center;">
                            <img src="{{asset('assets/loader.gif')}}" width="50%">
                        </div>
<div class="main-body" id="main_body">
    <div class="page-body">
        <div class="card-block" style="border: solid 0px green;padding:0px;" >
            <div class="row">
                <div class="col-md-12">
                    <form enctype="multipart/form-data" method="post"  name="education_form"  id="education_form">
                        {{ csrf_field() }}
                        <input type="hidden" name="pid" id="pid" value="{{$api_id}}">
                        <input type="hidden" name="form_id" id="form_id" value="{{$form_id}}">
                        <input type="hidden" name="sch_id" id="sch_id" value="{{$sch_id}}">
                            <?php
                            //print_r($result);
                            $array_index =0;
                            $card_array =0;
                            $print_matirc_card       = "YES";
                            $last_degree_name        = "";
                            foreach($obj_get_queryof as $row){
                                $degreelevel = $row->adcr_requirement;
                                if($row->aq_degree_name== "HSD" && $row->adcr_requirement=="Matric"){
                                    $print_matirc_card       = "YES";
                                    $last_degree_name = $row->aq_degree_name;
                                }elseif($last_degree_name== "HSD" && $row->adcr_requirement=="Intermediate"){
                                    $print_matirc_card       = "NO";
                                }else{
                                    $print_matirc_card       = "YES";
                                }
                                ?>
                            @if($print_matirc_card==="YES")
                            <div class="card" style="margin-bottom: 0px;!important" id="card_{{$card_array}}">
                                <div class="card-header">
                                    <div class="row" style="padding-top: 0px:!important">
                                        <div class="col-md-4" style="font-weight: bold">
                                            <label class="form-control" style="  border-radius: 12px;!important">
                                               <?php if($row->adcr_requirement == 'Matric'){?>
                                                SSC / Matriculation
                                               <?php }elseif($row->adcr_requirement == 'Intermediate'){ ?>
                                               HSSC  / Intermediate
                                               <?php }else{ ?>
                                                {{$row->adcr_requirement}}
                                                <?php }?>
                                            </label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row" id="{{$row->adcr_requirement}}">
                                                 <select name="mi_selector[]" class="form-control" onchange="EduShowHide(this);" style="border-radius: 12px;!important;width:50%">
                                                        <option value="0">--Select Level -- </option>
                                                    <?php if($row->adcr_requirement == 'Matric'){?>
                                                        <option value="Matric"  <?=('Matric' == $row->aq_degree_name) ? 'selected' : '';?>>Matric</option>
                                                        <option value="O_LEVEL" >O Level</option>
                                                        <option value="HSD"  <?=('HSD' == $row->aq_degree_name) ? 'selected' : '';?>>HSD Level</option>
                                                    <?php }else if($row->adcr_requirement == 'Intermediate'){ ?>
                                                        <option value="Intermediate" <?=('Intermediate' == $row->aq_degree_name) ? 'selected' : '';?>>Intermediate</option>
                                                        <option value="A_LEVEL">A Level</option>
                                                    <?php }else{ ?>
                                                        <option value="<?=str_replace('/','',$row->adcr_requirement)?>">{{$row->adcr_requirement}}</option>
                                                    <?php }?>
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                      <div class="card-body" id="GeneralInfo<?=str_replace('/','',$row->adcr_requirement)?>" style="<?=($row->aq_id=='') ? 'display:none' : 'display:block';?>">
                                        <input type="text" name="aq_id[]" id="aq_id{{$array_index}}" value="{{$row->aq_id}}">
                                        <input type="hidden" name="aq_degree_type[]" id="aq_degree_type{{$array_index}}" value="">
                                        <input type="hidden" name="qual_type[]" id="qual_type{{$array_index}}" value="{{$row->adcr_requirement}}">
                                        @if($row->aq_degree_name=== "HSD" && $row->adcr_requirement==="Matric")
                                        {{-- HSD Start Here --}}
                                            <div class="row" id="information_headerHSD" style=" <?=('HSD' == $row->aq_degree_name) ? 'background-color:#024F78;color:white;font-size: 16px;text-align:center;padding:5px' : 'display:none;background-color:#024F78;color:white;font-size: 16px;text-align:center;padding:5px';?>" >
                                                <div class="col-md-3 custom">
                                                School/Institue
                                                </div>
                                                <div class="col-md-2 custom">
                                                    City
                                                </div>
                                                <div class="col-md-2 custom">
                                                    Year
                                                </div>
                                                <div class="col-md-2 custom">
                                                    Obtained CGPA
                                                </div>
                                                <div class="col-md-1 custom">
                                                    Total CGPA
                                                </div>
                                                <div class="col-md-2 custom">
                                                    Result Status
                                                </div>
                                            </div>
                                            <div class="row" id="hsd_detail"  >
                                                <div class="col-md-3 custom">
                                                    <input type="text" name="hsd_school[]" id="hsd_school" class="form-control" placeholder="School/Institute" style="  border-radius: 12px;!important">
                                                </div>
                                                <div class="col-md-2 custom">
                                                    <input type="text" name="hsd_city[]" id="hsd_city" class="form-control" placeholder="City" style="  border-radius: 12px;!important">
                                                </div>
                                                <div class="col-md-2 custom">
                                                    <select class='form-control' name='hsd_year[]' id='hsd_year' style="  border-radius: 12px;!important">
                                                        <option value=''>--Year--</option>
                                                        <option value='2020'>2020</option>
                                                            <option value='2019'>2019</option>
                                                            <option value='2018'>2018</option>
                                                            <option value='2017'>2017</option>
                                                            <option value='2016'>2016</option>
                                                            <option value='2015'>2015</option>
                                                        </select>
                                                    {{-- <input type="text" name="year[]" id="year" class="form-control" placeholder="Year"> --}}
                                                </div>
                                                <div class="col-md-2 custom">
                                                    <input style="border-radius: 12px;!important" type="text" name="hsd_obtained_cgpa[]" id="hsd_obtained_cgpa" class="form-control" placeholder="Obtained CGPA">
                                                </div>
                                                <div class="col-md-1 custom">
                                                    <input style="border-radius: 12px;!important" type="text" name="hsd_total_cgpa[]" id="hsd_total_cgpa" class="form-control" placeholder="Total CGPA">
                                                </div>
                                                <div class="col-md-2 custom">
                                                    <select class='form-control' name="hsd_status[]"   id="hsd_status"    class='cptextbox' style="  border-radius: 12px;!important" >
                                                        <option value='Declared'>Declared</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row" id="hsd_equivalent_detail" >
                                                <div class="col-md-3 custom" style="margin: auto">
                                                    <label class="form-control">Equivalent Marks Obtained</label>
                                                </div>
                                                <div class="col-md-3 custom ">
                                                    <input type="text" name="hsd_equivalent_marks[]" class="form-control" placeholder="Equivalent Marks">
                                                </div>
                                                <div class="col-md-3 custom" style="margin: auto">
                                                    <label class="form-control">Equivalent Marks Total</label>
                                                </div>
                                                <div class="col-md-3 custom ">
                                                    <input type="text" name="hsd_equivalent_marks_total[]" class="form-control" placeholder="Equivalent Marks Total">
                                                </div>
                                            </div>
                                        {{-- HSD End Here --}}
                                        @else
                                        {{-- Header --}}
                                        <div class="row" id="information_header<?=str_replace('/','',$row->adcr_requirement)?>" style="background-color:#024F78;color:white;font-size: 16px;text-align:center;padding:5px">
                                            <div class="col-md-3 custom">
                                            School/Institue
                                            </div>
                                            <div class="col-md-2 custom">
                                                City
                                            </div>
                                            <div class="col-md-2 custom">
                                                Year
                                            </div>
                                            <div class="col-md-2 custom">
                                                Obtained Marks
                                            </div>
                                            <div class="col-md-1 custom">
                                            Total Marks
                                            </div>
                                            <div class="col-md-1 custom">
                                                Result Status
                                            </div>
                                            <div class="col-md-1 custom">
                                                Degree type
                                            </div>
                                        </div>
                                        {{-- Header End --}}
                                        {{-- Header Input --}}
                                        <div class="row" id="information_data_<?=str_replace('/','',$row->adcr_requirement)?>">
                                            <div class="col-md-3 custom">
                                                <input type="text" name="school[]" value="{{$row->aq_degree_institute}}"   id="school" class="form-control" placeholder="School/Institute" style="  border-radius: 12px;!important">
                                            </div>
                                            <div class="col-md-2 custom">
                                                <input type="text" name="city[]" id="city" class="form-control" placeholder="City" value="{{$row->aq_institute_city}}" style="border-radius: 12px;!important">
                                            </div>
                                            <div class="col-md-2 custom">
                                                <select class='form-control'  name='year[]'  id='year' style="  border-radius: 12px;!important">
                                                    <option value=''>--Year--</option>
                                                    <?php
                                                    $year = date("Y");
                                                    while($year >= 1950){?>
                                                        <option value='<?=$year?>' <?=($year == $row->aq_passing_year) ? 'selected' : '';?>><?=$year?></option>
                                                    <?php
                                                    $year--;
                                                    } ?>

                                                    </select>

                                            </div>
                                            <div class="col-md-2 custom">
                                                <input style="border-radius: 12px;!important" type="text" value="{{$row->aq_obtained_marks}}" name="obtained[]" id="obtained" class="form-control" placeholder="Obtained Marks">
                                            </div>
                                            <div class="col-md-1 custom">
                                                <input style="border-radius: 12px;!important" type="text" value="{{$row->aq_total_marks}}" name="total[]" id="total" class="form-control" placeholder="Total Marks">
                                            </div>
                                            <div class="col-md-1 custom">
                                                <select class='form-control' name="status[]"   id="status"    class='cptextbox' style="  border-radius: 12px;!important" >
                                                       <?php if ($row->adcr_requirement == 'Matric'){?>
                                                        <option value='Declared' <?=('Declared' == $row->aq_result_status) ? 'selected' : '';?>>Declared</option>
                                                        <?php }else{ ?>
                                                        <option value='Declared' <?=('Declared' == $row->aq_result_status) ? 'selected' : '';?>>Declared</option>
                                                        <option value='Waiting' <?=('Waiting' == $row->aq_result_status) ? 'selected' : '';?>>Waiting</option>
                                                         <?php }?>
                                                </select>
                                            </div>
                                            <div class="col-md-1 custom">
                                                <select class='form-control' name="matric_degree_type[]" id="matric_degree_type"    class='cptextbox' style="  border-radius: 12px;!important">
                                                    <option value=''>--Select Degree Type--</option>
                                                    <option value='Pre-Engineering'  <?=('Pre-Engineering' == $row->aq_degree_type) ? 'selected' : '';?>>Pre-Engineering</option>
                                                    <option value='Pre-Medical'      <?=('Pre-Medical' == $row->aq_degree_type) ? 'selected' : '';?>>Pre-Medical</option>
                                                    <option value='Commerce'         <?=('Commerce' == $row->aq_degree_type) ? 'selected' : '';?>>Commerce</option>
                                                    <option value='Faculty_of_Arts'  <?=('Faculty_of_Arts' == $row->aq_degree_type) ? 'selected' : '';?>>Faculty of Arts</option>
                                                    <option value='Others'           <?=('Others' == $row->aq_degree_type) ? 'selected' : '';?>>Others</option>
                                                </select>
                                            </div>
                                        </div>
                                        {{-- Header Input Ends --}}
                                        {{-- For O-Level Start Here--}}
                                        <div class="row" id="olevel_subject_info_header" style="display:none;background-color:#024F78;color:white;font-size: 16px;text-align:center;padding:5px">
                                            <div class="col-md-4 custom">
                                            School/Institue
                                            </div>
                                            <div class="col-md-4 custom">
                                                City
                                            </div>
                                            <div class="col-md-4 custom">
                                                Status
                                            </div>
                                        </div>
                                        <div class="row" id="olevel_subject_info" style="display:none">
                                            <div class="col-md-4 custom">
                                                <input type="text" name="olevel_school[]"   id="olevel_school" class="form-control" placeholder="School/Institute" style="  border-radius: 12px;!important">
                                            </div>
                                            <div class="col-md-4 custom">
                                                <input type="text" name="olevel_city[]" id="olevel_city" class="form-control" placeholder="City" style="  border-radius: 12px;!important">
                                            </div>
                                            <div class="col-md-4 custom">
                                                <select class='form-control' name="olevel_status[]"   id="olevel_status"    class='cptextbox' style="  border-radius: 12px;!important" >
                                                    <option value='Declared'>Declared</option>
                                                    <option value='Waiting'>Waiting</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div style="display:none;" id="olevelInfo"  >
                                                <div class="row" style="background-color:#024F78;color:white;font-size: 16px;text-align:center;padding:5px" >
                                                    <div class="col-md-4 custom">
                                                    Subject
                                                    </div>
                                                    <div class="col-md-4 custom">
                                                        Grade
                                                    </div>
                                                    <div class="col-md-4 custom">
                                                        Year
                                                    </div>
                                                </div>
                                                <div class="row">
                                                <?php
                                                $x = 1;
                                                while($x <= 8) { ?>
                                                    <div class="col-md-4 custom ">
                                                    <input type="text" name="subject[]" class="form-control" placeholder="Subject {{$x}}">
                                                        </div>
                                                        <div class="col-md-4 custom ">
                                                        <input type="text" name="grade[]" class="form-control" placeholder="Grade {{$x}}">
                                                        </div>
                                                        <div class="col-md-4 custom ">
                                                        <select class='form-control' name='levelgradeyear[]' id='olevel_year'>
                                                            <option value=''>--Passing Year Subject {{$x}}--</option>
                                                            <option value='2020'>2020</option>
                                                                <option value='2019'>2019</option>
                                                                <option value='2018'>2018</option>
                                                                <option value='2017'>2017</option>
                                                                <option value='2016'>2016</option>
                                                                <option value='2015'>2015</option>
                                                            </select>
                                                        </div>
                                                <?php
                                                    $x++;
                                                }
                                                ?>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3 custom" style="margin: auto">
                                                        <span>Equivalent Marks Obtained</span>
                                                    </div>
                                                    <div class="col-md-3 custom ">
                                                        <input type="text" name="ol_equivalent_marks[]" class="form-control" placeholder="Equivalent Marks">
                                                    </div>
                                                    <div class="col-md-3 custom" style="margin: auto">
                                                        <span>Equivalent Marks Total</span>
                                                        </div>
                                                    <div class="col-md-3 custom ">
                                                        <input type="text" name="ol_equivalent_marks_total[]" class="form-control" placeholder="Equivalent Marks Total">
                                                    </div>
                                                </div>
                                        </div>
                                        {{--O-Level Ends Here  --}}
                                        {{--A-Level Starts Here  --}}
                                        <?php if($row->adcr_requirement == 'Intermediate'){?>
                                        <div  id="alevelInfo" style="display:none" >
                                            <div class="row" id="alevel_subject_info_header" style="display:none;background-color:#024F78;color:white;font-size: 16px;text-align:center;padding:5px">
                                                <div class="col-md-4 custom">
                                                School/Institue
                                                </div>
                                                <div class="col-md-4 custom">
                                                    City
                                                </div>
                                                <div class="col-md-4 custom">
                                                    Status
                                                </div>
                                            </div>
                                            <div class="row" id="alevel_subject_info" >
                                                <div class="col-md-4 custom">
                                                    <input type="text" name="alevel_school"   id="alevel_school" class="form-control" placeholder="School/Institute" style="  border-radius: 12px;!important">
                                                </div>
                                                <div class="col-md-4 custom">
                                                    <input type="text" name="alevel_city" id="alevel_city" class="form-control" placeholder="City" style="  border-radius: 12px;!important">
                                                </div>
                                                <div class="col-md-4 custom">
                                                    <select class='form-control' name="alevel_status"   id="alevel_status"    class='cptextbox' style="  border-radius: 12px;!important" >
                                                        <option value='Declared'>Declared</option>
                                                        <option value='Waiting'>Waiting</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row" style="background-color:#024F78;color:white;font-size: 16px;text-align:center;padding:5px" >
                                                <div class="col-md-4 custom">
                                                    Subject
                                                </div>
                                                <div class="col-md-4 custom">
                                                    Grade
                                                </div>
                                                <div class="col-md-4 custom">
                                                    Year
                                                </div>
                                            </div>
                                            <div class="row">
                                                <?php
                                                $x = 1;
                                                while($x <= 3) { ?>

                                                    <div class="col-md-4 custom ">
                                                    <input type="text" name="asubject[]" class="form-control" placeholder="Subject {{$x}}">
                                                        </div>
                                                        <div class="col-md-4 custom ">
                                                        <input type="text" name="agrade[]" class="form-control" placeholder="Grade {{$x}}">
                                                        </div>
                                                        <div class="col-md-4 custom ">
                                                        <select class='form-control' name='alevelgradeyear[]' id='olevel_year'>
                                                            <option value=''>--Passing Year Subject {{$x}}--</option>
                                                            <option value='2020'>2020</option>
                                                                <option value='2019'>2019</option>
                                                                <option value='2018'>2018</option>
                                                                <option value='2017'>2017</option>
                                                                <option value='2016'>2016</option>
                                                                <option value='2015'>2015</option>
                                                            </select>
                                                        </div>
                                                <?php
                                                    $x++;
                                                }
                                                ?>
                                            </div>
                                                <div class="row">
                                                    <div class="col-md-3 custom" style="margin: auto">
                                                        <span>Equivalent Marks Obtained</span>
                                                    </div>
                                                    <div class="col-md-3 custom ">
                                                        <input type="text" name="aol_equivalent_marks" class="form-control" placeholder="Equivalent Marks">
                                                    </div>
                                                    <div class="col-md-3 custom" style="margin: auto">
                                                        <span>Equivalent Marks Total</span>
                                                        </div>
                                                    <div class="col-md-3 custom ">
                                                        <input type="text" name="aol_equivalent_marks_total" class="form-control" placeholder="Equivalent Marks Total">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        {{-- A Level Ends Here --}}
                                        </div>
                                        @endif

                                </div>
                            </div>
                            @endif
                        <?php
                            $array_index++;
                            $card_array++;
                        } // foreach end
                        ?>
                        <div id="error" ></div>
<div>

    <a href="{{route('main')}}/<?=$sch_id?>?user_id=<?=$uid?>"><button type="button" class="btn btn-dark"   style="outline: none; cursor: inherit;float: left; width:10%">Previous</button></a>
<button type="button"  onclick="registerBtn();"  class="btn btn-success"   style="outline: none; cursor: inherit;float:right; width:13%">Save Qualification</button>
<span id="registeredmsg" style="color:green;"></span>
<span id="errormsg" style="color:red;"></span>

</div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>

function registerBtn(){
    var bi_form = document.querySelector('#education_form');
    var fd = new FormData(bi_form);
    // var msg_div     = '<div class="col-md-12 alert alert-danger border-danger" style="text-align: left;"  ><button type="button" class="close" data-dismiss="alert" aria-label="Close"><i class="fa fa-times-circle-o" style="color:#e74c3c"></i></button><strong>Error! &nbsp &nbsp</strong>'
    // var msg_div1     = '<div class="col-md-12 alert alert-success border-success" style="text-align: left;"  ><button type="button" class="close" data-dismiss="alert" aria-label="Close"><i class="fa fa-times-circle-o" style="color:#218838"></i></button><strong>Success! &nbsp &nbsp</strong>'

    sendPostRequest('insert-qualification-info',function(res){
        // alert(res);

        // $("#"+res).focus();
        // $("#school0").css({'border' : '2px solid red !important'});


        // $("#res").html(res.message);
        // if(res.status==false){

        //     document.getElementById("error").focus();
        //     msg_div += res.message;
        //     msg_div += '</div>';
        //     $('#error').html(msg_div).focus();

        // }else
        if(res.status){
               $( "#registeredmsg" ).html(res.message);
                $("#main_body").hide();
                $("#loader").show();
                      setTimeout(function(){

                        if(res.action){
                            $("#loader").hide();
                            window.location = res.action.url;
                        }
                     }, 3000);
        }
    },fd);

}

 function EduShowHide(get_val) {
  // alert(get_val.value);
   if(get_val.value == 'Matric'){
    $("#information_headerHSD").hide();
    $("#information_headerMatric").show();
    $("#information_data_Matric").show();
    $("#olevel_subject_info").hide();
    $("#olevel_subject_info_header").hide();
    $("#alevelInfo").hide();
    $("#qual_type0").val('Matric');
    $("#aq_degree_type0").val('');

    $("#GeneralInfoMatric ").show();
	$("#matric_degree_type").show();
    $("#school").show();
    $("#city").show();
    $("#total").show();
    $("#year").show();
    $("#status").show();
    $("#obtained").show();
    $("#hsdInfo").hide();
    $("#matricInfo").show();
    $("#olevelInfo").hide();
  //  $("#alevelInfo").hide();
    $("#interInfo").hide();
    $("#card_1").show();
    $("#card_2").show();
    $("#card_3").show();
    $("#card_4").show();
    $("#card_5").show();
    $("#hsd_total_cgpa").hide();
    $("#hsd_year").hide();
    $("#hsd_obtained_cgpa").hide();
    $("#hsd_school").hide();
    $("#hsd_city").hide();
    $("#hsd_status").hide();
    $("#hsd_equivalent_detail").hide();

  }else if(get_val.value == 'O_LEVEL'){
    $("#information_headerHSD").hide();
    //   $("#school0").css({'width':'100%'});
    $("#school").show();
    $("#city").show();
    $("#status").show();
    $("#information_headerMatric").hide();
    $("#information_data_Matric").hide();
    $("#olevel_subject_info_header").show();
    $("#olevel_subject_info").show();

    $("#qual_type0").val('O_Level');
    $("#aq_degree_type0").val('O_Level');
    $("#matric_degree_type").hide();
	$("#GeneralInfoMatric ").show();
    $("#total").hide();
    $("#year").hide();
    $("#obtained").hide();
    $("#olevelInfo").show();
    $("#card_1").show();
    $("#card_2").show();
    $("#card_3").show();
    $("#card_4").show();
    $("#card_5").show();
    $("#hsd_total_cgpa").hide();
    $("#hsd_year").hide();
    $("#hsd_obtained_cgpa").hide();
    $("#hsd_school").hide();
    $("#hsd_city").hide();
    $("#hsd_status").hide();
    $("#hsd_equivalent_detail").hide();

  }else if(get_val.value == 'A_LEVEL'){
    $("#information_headerHSD").hide();
    $("#information_data_Intermediate").hide();
    $("#aq_degree_type1").val('A_Level');
     // alert('ok');
   // $("#qual_type").val('A_Level');
    $("#qual_type1").val('A_Level');
	  $("#GeneralInfoIntermediate").show();
      $("#information_headerIntermediate").hide();
  //  $("#hsdInfo").hide();
    $("#interInfo").hide();
   // $("#matricInfo").hide();
   // $("#olevelInfo").hide();
   $("#alevelInfo").show();
   $("#card_1").show();
   $("#card_2").show();
    $("#card_3").show();
    $("#card_4").show();
    $("#card_5").show();
  }
  else if(get_val.value == 'Intermediate'){
    $("#information_headerHSD").hide();
    $("#olevel_subject_info").hide();
    $("#olevel_subject_info_header").hide();
    $("#information_headerIntermediate").show();

    $("#qual_type1").val('Intermediate');
    $("#aq_degree_type1").val('');

    $("#GeneralInfoIntermediate").show();
  //  $("#hsdInfo").hide();
    //$("#matricInfo").hide();
    //$("#olevelInfo").hide();
    $("#alevelInfo").hide();
    $("#interInfo").show();
    $("#card_1").show();
    $("#card_2").show();
    $("#card_3").show();
    $("#card_4").show();
    $("#card_5").show();
    $("#information_data_Intermediate").show();
  } else if(get_val.value == 'HSD'){
    $("#hsd_detail").show();
    $("#qual_type0").val('HSD');
    $("#aq_degree_type0").val('');
    $("#information_headerHSD").show();
    $("#information_headerMatric").hide();
    $("#GeneralInfoMatric ").show();
    $("#hsd_detail").show();
    $("#olevelInfo").hide();
    $("#card_1").hide();
    $("#card_2").hide();
    $("#card_3").hide();
    $("#card_4").hide();
    $("#card_5").hide();
    $("#total").hide();
    $("#year").hide();
    $("#obtained").hide();
    $("#school").hide();
    $("#city").hide();
    $("#status").hide();
    $("#matric_degree_type").hide();
    $("#hsd_total_cgpa").show();
    $("#hsd_year").show();
    $("#hsd_obtained_cgpa").show();
    $("#hsd_school").show();
    $("#hsd_city").show();
    $("#hsd_status").show();
    $("#hsd_equivalent_detail").show();

} else if(get_val.value == 'Bachelors'){
    alert('ok');
    $("#information_headerBachelors").show();
    $("#GeneralInfoBachelors").show();
    $("#GeneralInfoBachelors").show();

    $("#card_1").show();
    $("#card_2").show();
    $("#card_3").show();
    $("#card_4").show();
    $("#card_5").show();

}else if(get_val.value == 'Masters'){

$("#information_headerMasters").show();
$("#GeneralInfoMasters").show();
$("#card_1").show();
    $("#card_2").show();
    $("#card_3").show();
    $("#card_4").show();
    $("#card_5").show();

}else if(get_val.value == 'MSMPhill'){

$("#information_headerMSMPhill").show();
$("#GeneralInfoMSMPhill").show();
$("#card_1").show();
    $("#card_2").show();
    $("#card_3").show();
    $("#card_4").show();
    $("#card_5").show();

}else if(get_val.value == 'GAT'){

$("#information_headerGAT").show();
$("#GeneralInfoGAT").show();
$("#card_1").show();
    $("#card_2").show();
    $("#card_3").show();
    $("#card_4").show();
    $("#card_5").show();

}
 }
</script>
