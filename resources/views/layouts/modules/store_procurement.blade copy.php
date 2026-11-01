<li class="slide">
 

   							<li class="slide">
							<a class="side-menu__item" data-toggle="slide" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-shopping-cart side-menu__icon"></i>
                                <span class="side-menu__label">Store Procurement</span>
                                <i class="angle fa fa-angle-right" aria-hidden="true"></i>
                            </a>
						<ul class="slide-menu" role="list">
                            @if(auth()->user()->group_id == 21)
							    <li><a class="slide-item" href="{{url('/budget')}}"><span> Create Department Budget </span></a></li>
							    <li><a class="slide-item" href="{{url('/budgets/list')}}"><span>Department Budgets</span></a></li>
                            @else

                             @if(auth()->user()->group_id == 644 || auth()->user()->group_id == 8)
                             <li><a class="slide-item" href="{{url('/reappropriation/approvals')}}"><span>Special VC Approval </span></a></li>
                             @endif

                             @if(auth()->user()->group_id == 644)
                             <li><a class="slide-item" href="{{url('/sp/quotations')}}"><span>Submit Vendor Quotation</span></a></li>
                             @endif

                             @if(auth()->user()->group_id == 644)
                            <li><a class="slide-item" href="{{url('/store-procurement/permissions')}}"><span>Rights Management</span></a></li>
                            <li><a class="slide-item" href="{{url('/approval-master/create')}}"><span>Master Approval Form</span></a></li>

                            <li><a class="slide-item" href="{{url('/vendor-category-create')}}"><span>Budgetary Head Categories</span></a></li>
                            <li><a class="slide-item" href="{{url('/vendor-create')}}"><span>Add Vendor</span></a></li>
                            <li><a class="slide-item" href="{{url('/items')}}"><span> Add Items </span></a></li>
                            <li><a class="slide-item" href="{{url('/cost-center-mapping')}}"><span>Cost Center Mapping</span></a></li>
                            <li><a class="slide-item" href="{{url('/budget')}}"><span> Create Department Budget </span></a></li>
                            @endif
                            @if(auth()->user()->group_id == 644 //|| auth()->user()->group_id == 884
                            )
							    <li><a class="slide-item" href="{{url('/store-items-budget')}}"><span> Store Items Budget </span></a></li>
                                <li><a class="slide-item" href="{{url('/store-items/quotation-request-create')}}"><span>Store Items Quotation Requests</span></a></li>
                                <li><a class="slide-item" href="{{url('/store-items/quotations')}}"><span>Store Items Quotations</span></a></li>
                                @endif

                                @if(auth()->user()->group_id == 644 || 1==1 )
                                <li><a class="slide-item" href="{{url('/quotation-requests')}}"><span>Department Head Approval</span></a></li>
                                @endif

							    <li><a class="slide-item" href="{{url('/budgets/list')}}"><span>Department Budgets</span></a></li>
                              
                                <li><a class="slide-item" href="{{url('/parent-dept-review')}}"><span>Relevant Executing Dept Review</span></a></li>

                               


								{{-- <li><a class="slide-item" href="{{url('/budget-item-details')}}"><span>Budget Item Insights</span></a></li> --}}


								<li><a class="slide-item" href="{{url('/quotation-request-create')}}"><span>Quotation Requests</span></a></li>
								{{-- <li><a class="slide-item" href="{{url('/quotation-request-purchase')}}"><span>Select Quotation Request</span></a></li> --}}


							
								<li><a class="slide-item" href="{{url('/item-recieved-acknowledgment')}}"><span>Items Acknowledgment</span></a></li>

                                {{-- Incorrect	Correct
                                Budgetry	Budgetary
                                Vendor Add	Add Vendor
                                Approvel	Approval
                                Approvels	Approvals
                                Recmondation	Recommendation
                                Approvel bodies	Approval Bodies --}}

								{{-- <li><a class="slide-item" target="__blank" href="{{url('/purchase-order-create')}}"><span>Purchase Order</span></a></li> --}}
								{{-- <li><a class="slide-item" target="__blank" href="{{url('/vendor-profiling')}}"><span>Vendor Profiling</span></a></li> --}}
                            @endif

                                @if( auth()->user()->group_id == 644 || 1==1
                                )
                                <li><a class="slide-item" href="{{url('/recommended-quotations')}}"><span>Purchase Order Approval Requests</span></a></li>

                                                            
                                @endif

                            @if(auth()->user()->group_id == 644)

								{{-- <li><a class="slide-item" href="{{url('/vendor/quotations')}}"><span>Vendor Quotations</span></a></li> --}}
								 {{-- <li><a class="slide-item" href="{{url('purchase-order-approval')}}"><span>Purchase Order Approval</span></a></li> --}}
								<li><a class="slide-item" href="{{url('gatepass')}}"><span>Purchase Order Gate Pass</span></a></li>
								<li><a class="slide-item" href="{{url('gatepass/list')}}"><span>Gate Pass List</span></a></li>
								<li><a class="slide-item" href="{{url('/sp_assets')}}"><span>Assets List</span></a></li>
								<li><a class="slide-item" href="{{url('/sp_assets/create')}}"><span>Add Asset</span></a></li>


                            @endif
</li>
