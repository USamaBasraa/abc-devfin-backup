<div id="today_lectures" style="margin-bottom:16px;margin-top:16px;">
    <div class="alert alert-info" role="alert" style="background-color: #D2D6F1; ">
        <div>
            {{-- <div style="float:left;">
                <i class="fa fa-bell-o mr-2" aria-hidden="true"></i>
             </div> --}}
            <div style="float:left; width:32% !important;border: solid 0px black;">
                Welcome! CMS is checking your today Timetable.
            </div>
            <div style="float:left; width:65% !important;border: solid 0px red;" text-align:center;>
                    <div class="progress progress-md mb-3" id="prog1">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-teal" style="width: 15%">15%</div>
                    </div>
                    <div class="progress progress-md mb-3"  id="prog2" style="display: none;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: 25%">25%</div>
                    </div>
                    <div class="progress progress-md mb-3"  id="prog3" style="display: none;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-yellow" style="width: 50%">50%</div>
                    </div>
                    <div class="progress progress-md mb-3"  id="prog4"  style="display: none;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-teal" style="width: 75%">75%</div>
                    </div>
                    <div class="progress progress-md mb-3"  id="prog5" style="display: none;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-yellow" style="width: 90%">90%</div>
                    </div>
                    <div class="progress progress-md mb-3"  id="prog6"  style="display: none;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-green" style="width: 100%">100%</div>
                    </div>
            </div>
            <div style="float:right; width:1% !important;border: solid 0px red;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

             </div>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>
<style>
    /* Progress Container */
#today_lectures .progress{
    height: 22px;
    margin-bottom: 0 !important;
    border-radius: 50px;
    background: #E7EFEF;
    border: 1px solid #17675F;
    overflow: hidden;
    box-shadow: inset 0 1px 3px rgba(0,0,0,.08);
}

/* Progress Fill */
#today_lectures .progress-bar{
    border-radius: 50px;
    background: linear-gradient(
        90deg,
        #17675F 0%,
        #FFFFFF 38.94%,
        #17675F 100%
    ) !important;

    color: #fff;
    font-size: 12px;
    font-weight: 600;
    line-height: 22px;
    text-align: center;

    background-size: 250% 100%;
    animation: progressShine 2.5s linear infinite;
}

/* Smooth moving shine */
@keyframes progressShine {
    from {
        background-position: 100% 0;
    }
    to {
        background-position: -100% 0;
    }
}
#today_lectures .alert{
    /* background: transparent !important; */
      background: #D2D6F1 !important;
    border: none !important;
    box-shadow: none !important;
}
</style>