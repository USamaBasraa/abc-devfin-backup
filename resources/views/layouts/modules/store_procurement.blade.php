<?php

$storeAdminAccess =
    in_array(auth()->user()->group_id, array(644));

$storeEmployeeAccess =
    !empty(auth()->user()->employee_id);

if ( 1==1
    // $storeAdminAccess || ($storeEmployeeAccess && auth()->user()->group_id == '8' || auth()->user()->username == 'moeedyusuf')
)
 {
?>

<li class="slide">

    <a class="side-menu__item"
       data-toggle="slide"
       href="#"
       role="button"
       aria-haspopup="true"
       aria-expanded="false">

        <i class="fa fa-shopping-cart side-menu__icon"></i>

        <span class="side-menu__label">
            E-Procurement
        </span>

        <i class="angle fa fa-angle-right" aria-hidden="true"></i>
    </a>


    <?php if (auth()->user()->group_id == 21) { 
        ?>

        <ul class="slide-menu">

            <li>
                <a class="slide-item" href="{{ url('/budget') }}">
                    <span>Create Department Budget</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/budgets/list') }}">
                    <span>Department Budgets</span>
                </a>
            </li>

        </ul>

    <?php } else { ?>

        <ul class="slide-menu">

            <li>
                <a class="slide-item" href="{{ url('/vendor-category-create') }}">
                    <span>Budgetary Head Categories</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/vendor-create') }}">
                    <span>Add Vendor</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/items') }}">
                    <span>Add Items</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/approval-master/create') }}">
                    <span>Master Approval Form</span>
                </a>
            </li>


            {{-- <?php if ($storeAdminAccess) { ?>

                <li>
                    <a class="slide-item" href="{{ url('/store-procurement/permissions') }}">
                        <span>Rights Management</span>
                    </a>
                </li>

            <?php } ?> --}}


            <li>
                <a class="slide-item" href="{{ url('/cost-center-mapping') }}">
                    <span>Cost Center Mapping</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/budget') }}">
                    <span>Create Department Budget</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/budgets/list') }}">
                    <span>Department Budgets</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/quotation-requests') }}">
                    <span>Department Head Approval</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/parent-dept-review') }}">
                    <span>Relevant Executing Dept Review</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/sp/quotations') }}">
                    <span>Submit Vendor Quotation</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/budget-item-details') }}">
                    <span>Budget Item Insights</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/quotation-request-create') }}">
                    <span>Quotation Requests</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/recommended-quotations') }}">
                    <span>Purchase Order Approval Requests</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/reappropriation/approvals') }}">
                    <span>Special VC Approval</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('gatepass') }}">
                    <span>Purchase Order Gate Pass</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('gatepass/list') }}">
                    <span>Gate Pass List</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/sp_assets') }}">
                    <span>Assets List</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/sp_assets/create') }}">
                    <span>Add Asset</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/item-recieved-acknowledgment') }}">
                    <span>Items Acknowledgment</span>
                </a>
            </li>

        </ul>

    <?php } ?>

</li>

<?php } ?>





<?php

$storeAdminAccess =
    in_array(auth()->user()->group_id, array(644));

$storeEmployeeAccess =
    !empty(auth()->user()->employee_id);

if ( 1==1
    // $storeAdminAccess || ($storeEmployeeAccess && auth()->user()->group_id == '8' || auth()->user()->username == 'moeedyusuf')
)
 {
?>

<li class="slide">

    <a class="side-menu__item"
       data-toggle="slide"
       href="#"
       role="button"
       aria-haspopup="true"
       aria-expanded="false">

        <i class="fa fa-shopping-cart side-menu__icon"></i>

        <span class="side-menu__label">
            E-Store Requisitions
        </span>

        <i class="angle fa fa-angle-right" aria-hidden="true"></i>
    </a>


    <?php if (auth()->user()->group_id == 21) { 
        ?>

    
    <?php } else { ?>

        <ul class="slide-menu">

            <li>
                <a class="slide-item" href="{{ url('/store-items-budget') }}">
                    <span>Store Items Budget</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/store-items/quotation-request-create') }}">
                    <span>Store Items Quotation Requests</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/store-items/quotations') }}">
                    <span>Submit Vendor Quotation</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/store-items/inventory') }}">
                    <span>Store Items</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/store-items/requests') }}">
                    <span>Store Item Requests</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/store-items/issued') }}">
                    <span>Issued Store Items</span>
                </a>
            </li>

            <li>
                <a class="slide-item" href="{{ url('/store-items/gate-pass-list') }}">
                    <span>Store Items Gate Pass</span>
                </a>
            </li>

        </ul>

    <?php } ?>

</li>

<?php } ?>