<!-- The Modal -->
<div class="modal fade" role="dialog" id="courseCriteriaModal" style=" margin-left: auto; margin-right: auto;">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header" style="border:solid 0px #FF0000;"> 

                
                <div id="show_heading_criteria" style=" font-size:16px; padding-left:0px; border: solid 0px;text-align: left; padding-right: 4px; color:#FFF; float:left; display: none;"></div>
                <i class="fa fa-times " data-dismiss="modal" style="color:#FFF;text-align:right;"></i>
            </div>
			
            <!-- Modal body -->
            <div class="modal-body" style="width: 100%; padding: 0px; margin: 0px;">
                

                {{-- @include('teacher.coursecriterias')--}}
                <style type="text/css">
                    .grid_center{
                        text-align: center;
                        padding: 0px;
                    }
                    .main_div{
                        width: 100%;
                        border: solid 2px;
                        min-height:600px;

                    }
                    .panel_sub_div{
                        width: 100%;
                        float: left;
                        height: 45px;
                        padding: 10px;
                        background-color: #2F3B4D;
                        border: solid 2px #2F3B4D;
                        color: white;
                        font-size: 14px;
                        font-weight: bold;
                    }
                    .sub_div{
                        width: 100%;
                        float: left;
                    }
                    .td_title{
                        background-color: #2F3B4D;
                        color: white;
                        font-size: 14px;
                    }
                    /* Course Criteria Modal - restore icon fonts */

#courseCriteriaModal .fa {
    font-family: FontAwesome !important;
    font-style: normal !important;
}

/* Themify icons */
#courseCriteriaModal [class^="ti-"],
#courseCriteriaModal [class*=" ti-"] {
    font-family: 'themify' !important;
}

/* Pe7 icons */
#courseCriteriaModal [class^="pe-7s-"],
#courseCriteriaModal [class*=" pe-7s-"] {
    font-family: 'Pe-icon-7-stroke' !important;
}


/* =====================================
   COURSE CRITERIA MODAL - FIGMA COLORS
===================================== */

#courseCriteriaModal .modal-content {
    background: #FFFFFF !important;
    border-color: #E5E7EB !important;
}

/* Modal header */
#courseCriteriaModal .modal-header {
    background: #17675F !important;
    border-color: #17675F !important;
}

/* Header title + close icon */
#courseCriteriaModal #show_heading_criteria,
#courseCriteriaModal .fa-times {
    color: #FFFFFF !important;
}

/* Modal body */
#courseCriteriaModal .modal-body,
#courseCriteriaModal .sub_div,
#courseCriteriaModal #load_date {
    background: #FFFFFF !important;
    color: #252525 !important;
}

/* Main criteria area */
#courseCriteriaModal .main_div {
    background: #FFFFFF !important;
    border-color: #E5E7EB !important;
}

/* Criteria section headers */
#courseCriteriaModal .panel_sub_div,
#courseCriteriaModal .td_title {
    background: #17675F !important;
    border-color: #17675F !important;
    color: #FFFFFF !important;
}
                </style>
                <div class="sub_div" id="load_date" style="width: 100%;">
                </div>
            </div>
        </div>
    </div>
</div>
