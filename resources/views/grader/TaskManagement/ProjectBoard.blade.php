@extends('layouts.app',['pageTitle' => 'Task Board'])
<style type="text/css">
.container {
    border: solid 2px black;
    margin-left: auto;
    margin-right: auto;
    width: 90%;
}

@media (max-width: 767px) {
    .tabs-menu {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-gap: 10px;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }
}

.box_div{
    border: solid 2px black;
    /* margin-left:auto;
    margin-right:auto; */
    margin: 25px 50px 12px 15px; 
    /* margin:auto;
    margin-top:4px !important; */
    width: 80%;
    height: 50%;
    text-align:center;
    border-radius: 10px;
    /* vertical-align:center; 
    padding: 40% 0;*/
    /* //float:left; */
    background-color: #fff;
}
.left_div{
    vertical-align:center;
    text-align:center;
    float:left;
    width:30%;
    height:100px;
    margin-left:14%;
    margin-top:2%;
    background-color: #fff;
    border: solid 2px black;
    border-radius: 10px;
}
.right_div{
    vertical-align:center;
    text-align:center;
    float:left;
    width:30%;
    height:100px;
    margin-left:8%;
    margin-top:2%;
    background-color: #fff;
    border: solid 2px black;
    border-radius: 10px;
}
.div_web {
    display: block;
    background-color:white;
}

.div_mobile {
    display: none;
    /* background-color:green; */
}

/* Media query for mobile view */
@media (max-width: 767px) {
    .div_web {
        display: none;
    }

    .div_mobile {
        display: block;
    }
}
li{
    width:20% !important;
}
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.1/css/jquery.dataTables.min.css">

@section('content')

<div class="div_mobile">
    <div class="row" style="border:solid 0px red;">
        <?php
            $srno=1;
        ?>
        <div class="right_div" style="background-color:#87CEFA"> 
                    <a href="https://cms.bnu.edu.pk/staff/showTaskListMobile?id=All&category_name=All"> 
                        <i class="fa fa-braille" style="font-size:30px;"> </i> <br>
                        All
                    </a>     
        </div>  
        @foreach ($category as $rec_category)
            @if($srno%2==0 && $srno>1)
            </div><div class="row"  style="border:solid 0px red;"> 
            @endif
            <!-- <div class="col-6 col-sm-6"  style="border:solid 0px yellow;"> -->
            <?php
                if($srno%2==1){
                    $set_div = "left_div";
                }else{
                    $set_div = "right_div";
                }
           ?>
                <div class="right_div"> 
                    <a href="https://cms.bnu.edu.pk/staff/showTaskListMobile?id={{$rec_category->id}}&category_name={{$rec_category->category_title}}"> 
                        <i class="fa fa-laptop" style="font-size:30px;"> </i> <br>
                      {{$rec_category->category_title}}
                    </a>     
                </div>  
        <?php
            $srno++;
        ?>    
        @endforeach
        <div class="right_div" style="background-color:#90EE90"> 
                    <a href="http://cmstest.bnu.edu.pk/staff/showTaskListMobile?id=Completed&category_name=Completed"> 
                        <i class="fa fa-check-square-o" style="font-size:30px;"> </i> <br>
                        Completed
                    </a>    
        </div>            


    </div>    
</div>
</div> 

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.1/js/jquery.dataTables.min.js"></script>

@endsection
@section('javascript')
<script>
    $(document).ready(function() {
        $('#data-table').DataTable();
    });
  function initializeDataTable() {
    $('#data-table').DataTable();
  }
  function fetchData(categoryId) {
  const tabContent = document.getElementById('tab_innerdivabc');
  tabContent.innerHTML = ''; // Empty the div before appending new data

  fetch('/staff/showTaskList?id=' + categoryId)
    .then(response => response.text())
    .then(data => {
    //   const newDiv = document.createElement('div');
    //   newDiv.innerHTML = data;
    //   tabContent.appendChild(newDiv);
   // tabContent = document.getElementById('tab_innerdivabc' + categoryId);
    tabContent.innerHTML = data; // Empty the div before appending new data

      initializeDataTable(); // Re-initialize DataTable after content is appended
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

</script>
@stop
