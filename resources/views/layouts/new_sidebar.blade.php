<style>

.list-group-item {
    padding: 5px 10px
}
.accordion {
  background-color: #eee;
  color: #444;
  cursor: pointer;
  padding: 18px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
  transition: 0.4s;
}

.active, .accordion:hover {
  background-color: #ccc; 
}

.panel {
  padding: 0 18px;
  display: none;
  background-color: white;
  overflow: hidden;
}
</style>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">

<!-- Bootstrap JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>

<!-- Custom JavaScript -->
<script src="path/to/your/javascript.js"></script>



<aside class="app-sidebar app-sidebar3 itrctest">
	<div class="app-sidebar__user">
		<div class="dropdown user-pro-body text-center">
			<div class="user-pic">
				<img src="{{asset('/staffimages/'.auth()->user()->employee_id.'.jpg')}}" alt="user-img" class="avatar-xl rounded-circle mb-1">
			</div>
			<div class="user-info">
			
				<h5 class=" mb-1 font-weight-bold"><?= auth()->user()->full_name ?></h5>
				<span class="text-muted app-sidebar__user-name text-sm"><?= auth()->user()->designation ?></span>
			</div>
		</div>
	</div>
<!-- Accordion Using List Group -->

  <div id="accordion">
    <div class="panel list-group newtest">
      </div>
  </div>


</aside>



@section('javascript')

<script type="text/javascript">



//     function ajaxdata(){
//         $.ajax({
//             type: 'GET',
//             url: '/getDataajax?token=<?=auth()->user()->group_id; ?>',
//             success: function(data) {
//                 localStorage.setItem('myarray', data);
//             },
//             error: function(xhr, status, error) {
//                 // Handle any errors that occurred during the AJAX request
//             }
//         });
//     }
//     function loadMenuTree() {
//         $("all-data").html('');
// 		alert('Please select');
//         const myArrayJson = localStorage.getItem("myarray");
//         // Parse the JSON-encoded array into a JavaScript array
//         const myArray = JSON.parse(myArrayJson);
//         if(!myArray){
//             ajaxdata();
//             setTimeout(loadMenuTree, 1000);
//         }
//         // Get the HTML div that will contain the data
//         const allDataDiv = document.getElementById("all-data");
//         // Loop through the elements in the myArray and create HTML elements to represent them
//         var set_module_id = '';
// 		var sr_no 		  = 1;
// 		for (let i = 0; i < myArray.length; i++) {

// 			if(set_module_id!=myArray[i]['gmp_module_id']){

// 					alert(myArray[i]['gmp_module_id']);

// 				var svg_class = '<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>';
// 				var span_icon = '<span class="side-menu__label">'+sr_no+' '+myArray[i]['gmp_module_name']+'</span><i class="angle fa fa-angle-right"></i>';
				
// 				var start_ul 	= '<ul class="slide-menu"><li><a class="slide-item" href="#"><span>Sub Page'+i+'</span></a></li></ul>';

// 				// var start_ul 	= '<ul class="slide-menu">';
// 				// var content_ul  = '<li><a class="slide-item" href="#"><span>Sub Page 1</span></a></li>';
// 				// var end_ul 	 	= '</ul>';

// 				//<ul class="slide-menu"><li><a class="slide-item" href="#"><span>Sub Page'+i+'</span></a></li></ul>

// 				$("#dynamic_menu").append('<li class="slide" ><a class="side-menu__item" data-toggle="slide" href="#">'+svg_class+''+span_icon+'</a><ul class="slide-menu loveyouall"></ul></li>');
// 				//$("#dynamic_menu").append(start_ul);
// //				$("#dynamic_menu").append('</li>');

// 				 set_module_id = myArray[i]['gmp_module_id'];
// 			}

// 			$(".loveyouall").append('<li><a class="slide-item" href="#"><span>Sub Page 1</span></a></li>');

// 			sr_no++;


//         }
//     }
    // $(document).ready(function(){  
    //     alert('All elements');  

    //   // setTimeout(loadMenuTree,20000);
    //    // loadMenuTree();
    // });
</script>
@stop