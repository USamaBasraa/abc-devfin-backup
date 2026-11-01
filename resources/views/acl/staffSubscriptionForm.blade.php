@extends('layouts.app')
{{-- @dd("123"); --}}
@section('title')
    BNU CMS
@stop
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- DataTables Bootstrap4 CSS -->
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap4.min.css') }}"><!-- dataTables.bootstrap4.min-->

    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.bootstrap4.min.css') }}"><!-- responsive.bootstrap4.min-->

    <!-- jQuery DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.dataTables.min.css') }}"><!-- jquery.dataTables.min-->

    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/buttons.dataTables.min.css') }}"><!-- buttons.dataTables.min--> --}}

</head>
@section('content')
<style>
    /* Gym Subscription - Figma aligned visual layer. Business logic / IDs stay unchanged. */
    .gym-page {
        --gym-primary: #17675f;
        --gym-primary-soft: rgba(23, 103, 95, 0.05);
        --gym-text: #111827;
        --gym-body: #6b7280;
        --gym-border: #e5e7eb;
        --gym-blue-soft: rgba(43, 127, 255, 0.10);
        min-height: calc(100vh - 40px);
        /* padding: 28px 30px 42px; */
        border-radius: 10px;
        /* background: linear-gradient(90deg, rgba(255,255,255,0.15) 0%, rgba(23,103,95,0.15) 100%); */
        color: var(--gym-text);
        font-family: 'Montserrat', Arial, sans-serif;
    }

    .gym-shell {
        width: 100%;
        /* max-width: 1088px; */
        margin: 0 auto;
    }

    .gym-heading {
        margin-bottom: 24px;
    }

    .gym-heading h1 {
        margin: 0 0 4px;
        color: #252525;
        font-size: 20px;
        font-weight: 600;
        line-height: 1.25;
    }

    .gym-heading p {
        margin: 0;
        color: #252525;
        font-size: 18px;
        font-weight: 400;
        line-height: 1.35;
    }

    .gym-hero-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 30px 23px 20px;
        box-shadow: 0 1px 2px rgba(17, 24, 39, 0.02);
    }

    .pricing-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
    }

    .plan-card {
        min-height: 144px;
        padding: 32px;
        border: 2px solid transparent;
        border-radius: 16px;
        background: var(--gym-primary-soft);
    }

    .plan-card--featured {
        border-color: var(--gym-primary);
    }

    .plan-label {
        margin: 0 0 4px;
        color: var(--gym-primary);
        font-size: 14px;
        font-weight: 600;
        line-height: 1.35;
        text-transform: capitalize;
    }

    .plan-price {
        display: flex;
        align-items: baseline;
        gap: 3px;
        margin: 0 0 12px;
        line-height: 1.15;
    }

    .plan-price strong {
        color: var(--gym-text);
        font-size: 28px;
        font-weight: 700;
    }

    .plan-price span {
        color: var(--gym-body);
        font-size: 14px;
        font-weight: 500;
    }

    .plan-description {
        margin: 0;
        color: var(--gym-body);
        font-size: 14px;
        font-weight: 400;
        line-height: 1.5;
    }

    #subscribeButtons {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 24px;
        margin: 24px 0 20px;
    }

    #subscribeButtons .form-check {
        order: -1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        min-height: 24px;
        margin: 0;
        padding: 0;
    }

    #subscribeButtons .custom-label {
        order: 1;
        margin: 0 !important;
        padding: 0;
        color: var(--gym-text);
        font-size: 15px;
        font-weight: 600;
        line-height: 24px;
        cursor: pointer;
    }

    #subscribeButtons .custom-checkbox {
        order: 2;
        position: relative;
        width: 44px;
        height: 24px;
        margin: 0 !important;
        border: 0;
        border-radius: 999px;
        outline: 0;
        background: #d1d5db;
        appearance: none;
        -webkit-appearance: none;
        cursor: pointer;
        transition: background-color .2s ease;
        flex: 0 0 44px;
    }

    #subscribeButtons .custom-checkbox::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(17, 24, 39, 0.18);
        transition: transform .2s ease;
    }

    #subscribeButtons .custom-checkbox:checked {
        background: var(--gym-primary);
    }

    #subscribeButtons .custom-checkbox:checked::after {
        transform: translateX(20px);
    }

    #subscribeButtons .custom-checkbox:focus-visible {
        box-shadow: 0 0 0 3px rgba(23, 103, 95, 0.18);
    }

    #subscribeButtons .subscribe-button,
    #subscribeButtons .unsubscribe-button,
    #subscribeButtons .unsubscribe-button-disabled {
        width: 280px !important;
        min-height: 50px;
        padding: 12px 24px;
        border: 0;
        border-radius: 10px;
        color: #ffffff;
        font-size: 16px;
        font-weight: 500;
        line-height: 1.2;
        text-decoration: none;
        transition: background-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    #subscribeButtons .subscribe-button {
        background: var(--gym-primary);
        cursor: pointer;
    }

    #subscribeButtons .subscribe-button:hover {
        background: #12574f;
        color: #ffffff;
        box-shadow: 0 6px 14px rgba(23, 103, 95, 0.14);
    }

    #subscribeButtons .unsubscribe-button {
        background: #b42318;
        cursor: pointer;
    }

    #subscribeButtons .unsubscribe-button:hover {
        background: #912018;
        color: #ffffff;
    }

    #subscribeButtons .unsubscribe-button-disabled {
        background: #9ca3af;
        cursor: not-allowed;
        opacity: .85;
    }

    .gym-disclaimer {
        margin-top: 0;
        padding: 16px 24px;
        border: 1px solid var(--gym-border);
        border-radius: 12px;
        background: var(--gym-blue-soft);
        text-align: left;
    }

    .gym-disclaimer .disclaimer-title {
        display: block;
        margin-bottom: 8px;
        color: var(--gym-text);
        font-size: 12px;
        font-weight: 700;
        line-height: 1.3;
        text-transform: uppercase;
    }

    .gym-disclaimer small {
        display: block;
        margin: 0;
        color: var(--gym-body);
        font-size: 13px;
        font-weight: 400;
        line-height: 1.6;
    }

    .gym-feedback {
        margin-top: 16px;
    }

    .gym-feedback .alert {
        margin: 0 0 10px;
        border-radius: 10px;
        font-size: 14px;
    }

    .gym-subscriptions-section {
        margin-top: 24px;
    }

    .gym-subscriptions-section h2 {
        margin: 0 0 15px;
        color: var(--gym-text);
        font-size: 20px;
        font-weight: 600;
        line-height: 1.3;
    }

    .subscriptions-table-card {
        overflow: hidden;
        border: 1px solid var(--gym-border);
        border-radius: 16px;
        background: #ffffff;
    }

    .subscriptions-table-card .dataTables_wrapper {
        width: 100%;
        color: var(--gym-body);
        font-family: 'Montserrat', Arial, sans-serif;
    }

    .subscriptions-table-card .dataTables_scroll,
    .subscriptions-table-card .dataTables_scrollHead,
    .subscriptions-table-card .dataTables_scrollBody {
        border: 0 !important;
    }

    .subscriptions-table-card .dataTables_scrollBody {
        min-height: 260px;
    }

    .subscriptions-table-card table.dataTable,
    .subscriptions-table-card #data-table {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: collapse !important;
        border-spacing: 0 !important;
    }

    .subscriptions-table-card #data-table thead th,
    .subscriptions-table-card table.dataTable thead th {
        /* padding: 14px 16px !important; */
            background: #17675F !important;
            color: white !important;
        border: 0 !important;
        /* background: var(--gym-primary-soft) !important; */
        /* color: var(--gym-primary) !important; */
        font-size: 13px;
        font-weight: 700;
        line-height: 1.25;
        text-transform: uppercase;
        white-space: normal;
        vertical-align: middle;
    }

    .subscriptions-table-card #data-table tbody td,
    .subscriptions-table-card table.dataTable tbody td {
        padding: 15px 16px !important;
        border-top: 1px solid #f0f2f4 !important;
        color: #4b5563;
        font-size: 14px;
        line-height: 1.45;
        vertical-align: middle;
    }

    .subscriptions-table-card table.dataTable tbody tr:hover {
        background: rgba(23, 103, 95, 0.025);
    }

    .subscriptions-table-card td.dataTables_empty {
        height: 270px !important;
        position: relative;
        color: transparent !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    .subscriptions-table-card td.dataTables_empty::after {
        content: "No subscriptions found\A You don't have any active or past gym subscriptions yet.";
        display: block;
        color: var(--gym-text);
        font-size: 18px;
        font-weight: 600;
        line-height: 1.8;
        white-space: pre-line;
    }

    .subscriptions-table-card td.dataTables_empty::first-line {
        color: var(--gym-text);
    }

    .subscriptions-table-card .dataTables_info {
        padding: 20px !important;
        color: var(--gym-body) !important;
        font-size: 14px;
        line-height: 32px;
    }

    .subscriptions-table-card .dataTables_paginate {
        padding: 20px !important;
    }

    .subscriptions-table-card .dataTables_paginate .paginate_button {
        min-width: 34px !important;
        height: 34px;
        margin: 0 2px !important;
        padding: 7px 10px !important;
        border: 1px solid var(--gym-border) !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        color: var(--gym-body) !important;
        box-shadow: none !important;
        font-size: 14px;
        line-height: 18px;
    }

    .subscriptions-table-card .dataTables_paginate .paginate_button.current,
    .subscriptions-table-card .dataTables_paginate .paginate_button.current:hover {
        border-color: var(--gym-primary) !important;
        background: var(--gym-primary) !important;
        color: #ffffff !important;
    }

    .subscriptions-table-card .dataTables_paginate .paginate_button:hover {
        border-color: var(--gym-primary) !important;
        background: rgba(23, 103, 95, 0.08) !important;
        color: var(--gym-primary) !important;
    }

    .subscriptions-table-card .dataTables_paginate .paginate_button.disabled,
    .subscriptions-table-card .dataTables_paginate .paginate_button.disabled:hover {
        opacity: .5;
        cursor: default !important;
        border-color: var(--gym-border) !important;
        background: #ffffff !important;
        color: #9ca3af !important;
    }

    .subscriptions-table-card .badge {
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }

    .subscriptions-table-card .badge-success {
        background-color: rgba(23, 103, 95, 0.12);
        color: var(--gym-primary);
    }

    .subscriptions-table-card .badge-warning {
        background-color: rgba(245, 158, 11, 0.12);
        color: #92400e;
    }

    .subscriptions-table-card .badge-danger {
        background-color: rgba(180, 35, 24, 0.12);
        color: #b42318;
    }

    .success-flag {
        position: fixed;
        top: 170px;
        right: -320px;
        z-index: 1050;
        padding: 14px 18px;
        border-radius: 12px 0 0 12px;
        background: var(--gym-primary, #17675f);
        color: #ffffff;
        box-shadow: 0 8px 22px rgba(17, 24, 39, 0.12);
        transition: right .35s ease;
    }

    .success-flag.show {
        right: 0;
    }

    .gym-page .modal-content {
        overflow: hidden;
        border: 0;
        border-radius: 16px;
        box-shadow: 0 18px 50px rgba(17, 24, 39, 0.18);
    }

    .gym-page .modal-header,
    .gym-page .modal-footer {
        border-color: var(--gym-border);
    }

    .gym-page .modal-title {
        color: var(--gym-text);
        font-weight: 600;
    }

    .gym-page .modal-footer .btn-primary {
        border-color: var(--gym-primary);
        background: var(--gym-primary);
    }

    @media (max-width: 991.98px) {
        .gym-page {
            padding: 24px 20px 36px;
        }

        .pricing-grid {
            grid-template-columns: 1fr;
        }

        .plan-card {
            min-height: auto;
        }
    }

    @media (max-width: 575.98px) {
        .gym-page {
            padding: 20px 14px 30px;
            border-radius: 0;
        }

        .gym-heading p {
            font-size: 15px;
        }

        .gym-hero-card {
            padding: 18px 14px;
            border-radius: 16px;
        }

        .plan-card {
            padding: 22px 18px;
        }

        #subscribeButtons .subscribe-button,
        #subscribeButtons .unsubscribe-button,
        #subscribeButtons .unsubscribe-button-disabled {
            width: 100% !important;
        }

        .gym-disclaimer {
            padding: 16px;
        }

        .subscriptions-table-card .dataTables_info,
        .subscriptions-table-card .dataTables_paginate {
            float: none !important;
            width: 100%;
            text-align: center !important;
        }
    }
</style>
<div id="success-flag" class="success-flag"></div>

<div class="gym-page">
    <div class="gym-shell">
        <div class="gym-heading">
            <h1>Gym Subscription</h1>
            <p>Fitness Without Limits</p>
        </div>

        <form id="subcriptionForm" method="POST" action="">
            @csrf

            <section class="gym-hero-card">
                <div class="pricing-grid">
                    <article class="plan-card">
                        <p class="plan-label">For under 100k</p>
                        <p class="plan-price">
                            <strong>Rs. 2500</strong>
                            <span>/month</span>
                        </p>
                        <p class="plan-description">Employees with salary up to Rs. 100,000.</p>
                    </article>

                    <article class="plan-card plan-card--featured">
                        <p class="plan-label">For Above 100k</p>
                        <p class="plan-price">
                            <strong>Rs. 4000</strong>
                            <span>/month</span>
                        </p>
                        <p class="plan-description">Employees with salary exceeding Rs. 100,000.</p>
                    </article>
                </div>

                {{-- Subscription state logic intentionally preserved exactly. --}}
                <div id='subscribeButtons'>
                    @if(!empty($user_sub_status) || !empty($currentMonthStatus_autoYes) )  {{-- if not empty means user has subscribed then show unsubscribe button and set value as inactive--}}
                    {{-- || (!empty($currentMonthStatus) && $currentMonthStatus[0]->auto_subscription=='yes') --}}
                        @if($isLastFiveDays){{-- if date is between 25 and 31 show unSubButton --}}
                            <input type="text" name="subscription-status" id="subscription-status" value='inactive' class="form-control" hidden>
                            <button type="button" style="width: 230px;" id="saveButton" class="unsubscribe-button">Unsubscribe for Next Month</button>

                        @else
                            <input type="text" name="subscription-status" id="subscription-status" value='' class="form-control" hidden>
                            <button type="button" style="width: 150px;" class="unsubscribe-button-disabled">Unsubscribe</button>

                        @endif

                    @else {{--user has not subscribed then show subscribe button and set value as active--}}
                        @if($isLastFiveDays)
                            <input type="text" name="subscription-status" id="subscription-status" value='active' class="form-control" hidden>
                            <button type="button" style="width: 210px;" id="saveButton" tenure='nextMonth' class="subscribe-button">Subscribe for Next Month</button>
                        @else
                            <input type="text" name="subscription-status" id="subscription-status" value='active' class="form-control" hidden>
                            <button type="button" style="width: 150px;" id="saveButton" tenure='currentMonth' class="subscribe-button">Subscribe Now</button>
                        @endif
                        <div class="form-check">
                            <input type="checkbox" name="auto-subscription" style="margin-top: 10px;" id="autoSubscription" class="form-check-input custom-checkbox">
                            <label for="autoSubscription" style="margin-top: 10px;" class="form-check-label custom-label">Auto Subscription</label>
                        </div>
                    @endif
                </div>

                <div class="gym-disclaimer">
                    <span class="disclaimer-title">Disclaimer:</span>
                    <small>
                        Monthly fee will be deducted in advance of every month through Payroll deduction, Rs. 2,500 for employees with salary up to Rs. 100,000, and Rs. 4,000 for those exceeding Rs. 100,000.
                    </small>
                </div>
            </section>

            <div class="gym-feedback">
                <div id="error_message" class="alert alert-danger" style="display: none;"></div>
                <div id="success_message" class="alert alert-success" style="display: none;"></div>
            </div>
        </form>

        <section class="gym-subscriptions-section">
            <h2>My Subscriptions</h2>

            <div class="subscriptions-table-card">
                <table id="data-table" class="table display">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Subscription Status</th>
                            <th scope="col">Current Status</th>
                            <th scope="col">Subscription Start</th>
                            <th scope="col">Subscription Expiry</th>
                            <th scope="col">Auto Renewal</th>
                        </tr>
                    </thead>
                    <tbody id="tbody"></tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<!-- Current month Subscription Disclaimer Modal -->
<div id="SubsDisclaimerModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 553px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Disclaimer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p style="font-size: 1.1em; font-weight: bold; text-align: justify;">Please read and confirm before proceeding with the subscription.</p>
                <p style="text-align: justify;">By confirming your subscription, you agree to the following terms:</p>
                <ul style="list-style-type: disc; margin-left: 20px; text-align: justify;">
                    <li><strong>Unsubscription:</strong>
                        <ul style="list-style-type: circle; margin-left: 20px; text-align: justify;">
                            <li>Once subscribed, you will not be able to unsubscribe for the same month.</li>
                            <li>You can unsubscribe during the last 5 days of the month, and your unsubscription will take effect in the upcoming month.</li>
                        </ul>
                    </li>
                    <li><strong>Monthly Fee Deduction:</strong> A monthly fee will be automatically deducted from your payroll:
                        <ul style="list-style-type: circle; margin-left: 20px; text-align: justify;">
                            <li><strong>Rs. 2,500</strong> for employees with a monthly salary up to <strong>Rs. 100,000</strong>.</li>
                            <li><strong>Rs. 4,000</strong> for employees with a monthly salary exceeding <strong>Rs. 100,000</strong>.</li>
                        </ul>
                    </li>
                </ul>
                <p style="text-align: justify;"><strong>Are you sure you want to proceed with this subscription?</strong></p>
            </div>


            <div class="modal-footer">
                <button type="button" id="confirmSubscription" class="btn btn-primary">Confirm</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- Next month Subscription Disclaimer Modal -->
<div id="SubsDisclaimerModalNextMonth" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 553px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Disclaimer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p style="font-size: 1.1em; font-weight: bold; text-align: justify;">Please read and confirm before proceeding with the subscription.</p>
                <p style="text-align: justify;">By confirming your subscription, you agree to the following terms:</p>
                <ul style="list-style-type: disc; margin-left: 20px; text-align: justify;">
                    <li><strong>Unsubscription:</strong>
                        <ul style="list-style-type: circle; margin-left: 20px; text-align: justify;">
                            <li>This subscription will take effect from the upcoming month.</li>
                            <li>Unsubscription is not allowed once the new month begins.</li>
                            <li>You can unsubscribe during the last 5 days of this month, and your unsubscription will take effect in the upcoming month.</li>
                        </ul>
                    </li>
                    <li><strong>Monthly Fee Deduction:</strong> A monthly fee will be automatically deducted from your payroll:
                        <ul style="list-style-type: circle; margin-left: 20px; text-align: justify;">
                            <li><strong>Rs. 2,500</strong> for employees with a monthly salary up to <strong>Rs. 100,000</strong>.</li>
                            <li><strong>Rs. 4,000</strong> for employees with a monthly salary exceeding <strong>Rs. 100,000</strong>.</li>
                        </ul>
                    </li>
                </ul>
                <p style="text-align: justify;"><strong>Are you sure you want to proceed with this subscription?</strong></p>
            </div>


            <div class="modal-footer">
                <button type="button" id="confirmSubscription" class="btn btn-primary">Confirm</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!--Unsubscription Disclaimer Modal -->
<div id="UnSubsDisclaimerModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 553px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Disclaimer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- <p style="font-size: 1.1em; font-weight: bold; text-align: justify;">Are you sure to unsubscribe?</p>
                <p style="text-align: justify;">By confirming your subscription, you agree to the following terms:</p>
                <ul style="list-style-type: disc; margin-left: 20px; text-align: justify;">
                    <li><strong>Unsubscription:</strong>
                        <ul style="list-style-type: circle; margin-left: 20px; text-align: justify;">
                            <li>Once subscribed, you will not be able to unsubscribe for the same month.</li>
                            <li>You can unsubscribe during the last 5 days of the month, and your unsubscription will take effect in the upcoming month.</li>
                        </ul>
                    </li>
                    <li><strong>Monthly Fee Deduction:</strong> A monthly fee will be automatically deducted from your payroll:
                        <ul style="list-style-type: circle; margin-left: 20px; text-align: justify;">
                            <li><strong>Rs. 2,500</strong> for employees with a monthly salary up to <strong>Rs. 100,000</strong>.</li>
                            <li><strong>Rs. 4,000</strong> for employees with a monthly salary exceeding <strong>Rs. 100,000</strong>.</li>
                        </ul>
                    </li>
                </ul> --}}
                <p style="text-align: justify;"><strong>Are you sure you want to proceed with this Unsubscription?</strong></p>
            </div>


            <div class="modal-footer">
                <button type="button" id="confirmUnSubscription" class="btn btn-primary">Confirm</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div style="clear:both;"></div>
@stop

@section('javascript')
<script>
    $(document).ready(function(){
        LoadStaffSubscriptions();

        $(document).on('click', '.unsubscribe-button-disabled', function() {
            alert('You can only unsubscribe in the last 5 days of each month. The cancellation will take effect the following month.');
        });
        $(document).on('click', '#saveButton', function() {
            var subscribeAction = $('#subscription-status').val();
            var tenure = $(this).attr('tenure');
            // console.log(tenure);
            if (subscribeAction == 'active') {
                if(tenure == 'nextMonth'){
                    console.log(tenure);
                    $('#SubsDisclaimerModalNextMonth').modal('show'); // Show the disclaimer modal
                }else{
                    console.log(tenure);
                    $('#SubsDisclaimerModal').modal('show'); // Show the disclaimer modal
                }
            }else if(subscribeAction == 'inactive'){
                $('#UnSubsDisclaimerModal').modal('show'); // Show the disclaimer modal
            }else{
                // confirmation = 'dontAllow';
            }
        });
    });
    // Event listener for the "Confirm" button in the disclaimer modal
    $(document).on('click', '#confirmSubscription', function() {
        $('#SubsDisclaimerModal').modal('hide'); // Hide the disclaimer modal
        $('#SubsDisclaimerModalNextMonth').modal('hide'); // Hide the disclaimer modal
        actionOn_Sub_Unsub_BtnClick(); // Call the function for subscription/unsubscription
    });
    $(document).on('click', '#confirmUnSubscription', function() {
        $('#UnSubsDisclaimerModal').modal('hide'); // Hide the disclaimer modal
        actionOn_Sub_Unsub_BtnClick(); // Call the function for subscription/unsubscription
    });




</script>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
{{-- jquery.dataTables.min.js --}}
{{-- <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> --}}
{{-- dataTables.buttons.min.js --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script> --}}
{{-- jszip.min --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> --}}
{{-- buttons.html5.min --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script> --}}
{{-- buttons.print.min --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script> --}}
{{-- pdfmake.min --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script> --}}
{{-- vfs_fonts --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> --}}

{{-- LOCAL FILES --}}
{{-- jQuery DataTables JS --}}
{{-- <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script> --}}

{{-- DataTables Buttons JS --}}
{{-- <script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script> --}}

{{-- JSZip JS --}}
{{-- <script src="{{ asset('assets/js/jszip.min.js') }}"></script> --}}

{{-- Buttons HTML5 JS --}}
{{-- <script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script> --}}

{{-- Buttons Print JS --}}
{{-- <script src="{{ asset('assets/js/buttons.print.min.js') }}"></script> --}}

{{-- pdfmake JS --}}
{{-- <script src="{{ asset('assets/js/pdfmake.min.js') }}"></script> --}}

{{-- VFS Fonts JS --}}
{{-- <script src="{{ asset('assets/js/vfs_fonts.js') }}"></script> --}}

<script>
    function actionOn_Sub_Unsub_BtnClick(){
        // var subscribeAction = $('#subscription-status').val();
        //     var confirmation = '';
        //     if (subscribeAction == 'active') {
        //         confirmation = getSubsctiptionConfirmation();
        //     }else if(subscribeAction == 'inactive'){
        //         confirmation = getUnsubscribeConfirmation();
        //     }else{
        //         confirmation = 'dontAllow';
        //     }
        //     if(confirmation == 'yes'){
                var form = document.getElementById("subcriptionForm");
                var formData = new FormData(form);
                fetch('add-staff-subscription', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // refreshFields();
                        console.log(data);
                        $("#error_message").text('').hide();
                        $('#success_message').html(data.msg);
                        $('#success_message').fadeIn('slow');
                        $('#success_message').fadeOut(5000);
                        let contnt='';
                        if(data.newStatus == 'active'){ // // If user is subscribed, show unsubscribe button and set value to 'inactive'
                            if(data.isLastFiveDays){
                                var subBtn = '<input type="text" name="subscription-status" id="subscription-status" value="inactive" class="form-control" hidden>'+
                                            '<button type="button" style="width: 230px;" id="saveButton" class="unsubscribe-button">Unsubscribe for Next Month</button>'
                                $('#subscribeButtons').html(subBtn);
                            }else{
                                var subBtn ='<button type="button" style="width: 150px;" class="unsubscribe-button-disabled">Unsubscribe</button>'
                                $('#subscribeButtons').html(subBtn);
                            }
                            // $('#subscription-status').val('');
                            // $('#saveButton').removeClass('subscribe-button');
                            // $('#saveButton').addClass('unsubscribe-button-disabled');
                            // $('#saveButton').text('Unsubscribe');

                        }else{ //If user is not subscribed, show subscribe button and set value to 'active'
                            if(data.isLastFiveDays){
                                var btn='<input type="text" name="subscription-status" id="subscription-status" value="active" class="form-control" hidden>'+
                                            '<button type="button" style="width: 210px;" id="saveButton" class="subscribe-button">Subscribe for Next Month</button>'+
                                            '<div class="form-check">'+
                                    '<input type="checkbox" name="auto-subscription" style="margin-top: 10px;" id="autoSubscription" class="form-check-input custom-checkbox">'+
                                    '<label for="autoSubscription" style="margin-top: 10px;" class="form-check-label custom-label">Auto Subscription</label>'+
                                '</div> ';
                            }else{
                                var btn='<input type="text" name="subscription-status" id="subscription-status" value="active" class="form-control" hidden>'+
                                            '<button type="button" style="width: 150px;" id="saveButton" class="subscribe-button">Subscribe Now</button>'+
                                            '<div class="form-check">'+
                                    '<input type="checkbox" name="auto-subscription" style="margin-top: 10px;" id="autoSubscription" class="form-check-input custom-checkbox">'+
                                    '<label for="autoSubscription" style="margin-top: 10px;" class="form-check-label custom-label">Auto Subscription</label>'+
                                '</div> ';
                            }
                            $('#subscribeButtons').html(btn);
                            // $('#subscription-status').val('active');
                            // $('#saveButton').removeClass('unsubscribe-button');
                            // $('#saveButton').addClass('subscribe-button');
                            // $('#saveButton').text('Subscribe Now');
                        }
                        LoadStaffSubscriptions();
                    } else {
                        console.log(data);
                        var message = "";
                        if ('msg' in data) {
                            message = data.msg;
                        } else if ('errors' in data) {
                            var errors = data.errors;
                            $.each(errors, function (i, obj) {
                                message += "<p>" + obj[0] + "</p>";
                            });
                        }
                        $("#processing_message").hide();
                        $('#success_message').text('').hide();
                        $('#error_message').html(message);
                        $('#error_message').fadeIn('slow');
                        $('#error_message').fadeOut(5000);
                    }
                })
                .catch(error => {
                    console.error('There was an error!', error);
                });
            // }
    }
    function LoadStaffSubscriptions(){
        // let filterParams = getFilterParams();
        // console.log(filterParams);
        $('#data-table').DataTable({
            scrollX: true, // Enable horizontal scrolling
            responsive: true,
            fixedHeader: false,
            scrollCollapse: false,
            processing: true,
            serverSide: true,
            select: false,
            searching: false,
            destroy: true,
            // lengthChange: true,
            paging: true,
            // pageLength: 10,
            order: [1, 'desc'],
            ajax:{
                dataType: 'json',
                method: 'post',
                url:  "/get-staff-subscription-list",
                data: function(d) {
                        // Include the CSRF token in the request
                        d._token = $('meta[name="csrf-token"]').attr('content')
                    }
                },
            columns: [
                {data: "student_full_name"},
                {
                    data: "subscription_status",
                    render: function (data, type, row) {
                        if (data == 'active') {
                            // If sc_paid_on is present return "Paid"
                            return '<span class="badge badge-success">Active</span>';

                        } else {
                            // If sc_paid_on is null or empty, return "Pending"
                            return '<span class="badge badge-warning">Inactive</span>';
                        }
                    }
                },
                {
                    data: "current_status",
                    render: function(data, type, row) {
                        if (data == 'active') {
                            return '<span class="badge badge-success">Active</span>';
                        } else if(data == 'inactive') {
                            return '<span class="badge badge-warning">Inactive</span>';
                        }else if(data == 'Expired') {
                            return '<span class="badge badge-danger">Expired</span>';
                        }else{
                            return '<span class="badge badge-warning">NA</span>';
                        }
                    }
                },
                {
                    data: "subscription_date",
                    render: function(data, type, row) {
                        return data ? data.split(' ')[0] : '';
                    }
                },
                {
                    data: "expiration_date",
                    render: function(data, type, row) {
                        return data ? data.split(' ')[0] : '';
                    }
                },
                {
                    data: "auto_subscription",
                    render: function(data, type, row) {
                        return data ? data.split(' ')[0] : '';
                    }
                }

            ],
            columnDefs: [
                // {
                //     render: function (data, type, row) {
                //         console.log(row);
                //         var button = '<div><button class="btn btn-primary btn-sm edit-btn" style="margin-top:5px;" id="'+row["sp_id"]+'" onclick="editRow(`'+row["sp_id"]+'`,`'+row["sp_category"]+'`,`'+row["sp_user_type"]+'`,`'+row["sp_plan_tenure"]+'`,`'+row["sp_pricing"]+'`,`'+row["sp_detail"]+'`,`'+row["sp_discount"]+'`,`'+row["sp_sem_start"]+'`,`'+row["sp_sem_end"]+'`,`'+row["sp_session"]+'`,`'+row["sp_sports_pricing"]+'`)"><i class="fa fa-edit"></i> Edit</button>'+
                //                                     '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
                //                                 '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRow('+row["sp_id"]+')"><i class="fa fa-trash"></i> Delete</button></div>';
                //         return button;

                //     },
                //     // className: 'dt-body-center',
                //     // className: 'dt-head-center',
                //     targets: 10,
                // },
                // {
                //     render: function (data, type, row) {
                //             var   selectbtn = '<div style="margin-top:40px;"><input name="fee_ids[]" class="select-data" idData='+row["fsc_id"]+' value='+row["fsc_id"]+' type="checkbox" ></div>';

                //         return selectbtn;

                //     },
                //     // className: 'dt-body-center',
                //     // className: 'dt-head-center',
                //     targets: 8,
                //     searchable: false,
                //     orderable: false
                // },

                // {
                //     "width": "100px",
                //     "targets": 3
                // },
                // {
                //     "width": "50px", "targets": 6
                // },
                // {
                //     "width": "10px", "targets": 8
                // },
            ],
            autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'frtip', // Adds the buttons to the DataTable
            // buttons: [
            //     {
            //         extend: 'excelHtml5',
            //         title: 'Subscription Plans',
            //         // exportOptions: {
            //         //     columns: function (idx, data, node) {
            //         //         // Exclude columns by index
            //         //         exclude columns 12 and 13 (0-based index)
            //         //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
            //         //     }
            //         // }
            //     },
            //     {
            //         extend: 'pdfHtml5',
            //         title: 'Subscription Plans',
            //         orientation: 'landscape',
            //         pageSize: 'A4',
            //         // exportOptions: {
            //         //     columns: function (idx, data, node) {
            //         //         // Exclude columns by index
            //         //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
            //         //     }
            //         // }
            //     },
            //     {
            //         extend: 'print',
            //         title: 'Subscription Plans',
            //         // exportOptions: {
            //         //     columns: function (idx, data, node) {
            //         //         // Exclude columns by index
            //         //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
            //         //     }
            //         // }
            //     },
            // ]
        });
    }
    function getSubsctiptionConfirmation(){
        var confirmation='no';
        // if (confirm("Once subscribed, you cannot unsubscribe before the end of this month. Are you sure to subscribe?")) {
        if (confirm("Are you sure to subscribe?")) {
            confirmation = 'yes';
        }
        return confirmation;
    }
    function getUnsubscribeConfirmation(){
        var confirmation='no';
        if (confirm("Are you sure to unsubscribe?")) {
            confirmation = 'yes';
        }
        return confirmation;
    }
    function exportToExcel() {
        const table = document.getElementById('htmlTable');
        const rows = table.querySelectorAll('tr');

        let csvContent = "data:text/csv;charset=utf-8,";
        rows.forEach(row => {
            const cells = row.querySelectorAll('td, th');
            const rowData = Array.from(cells).map(cell => cell.textContent.replace(',', ''));
            csvContent += rowData.join(',') + "\n";
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', 'table.csv');
        document.body.appendChild(link);
        link.click();
    }
    function exportToExcel(tableId) {
        $(`#${tableId}`).tableExport({type:'xlsx', escape:'false'});
    }
    function exportToPDF(tableId) {
    try {
        const doc = new jsPDF();
        doc.autoTable({ html: `#${tableId}` });
        doc.save('table.pdf');
    } catch (error) {
        console.error('Error exporting to PDF:', error);
        alert('An error occurred while exporting to PDF. Please try again.');
    }
    }
    function editRow(mainId, category, usertype, plantenure, pricing, detail, discount, start, end, session, sportsPricing){
        $('#main-Id').val(mainId);
        $('#session_sel').val(session);
        $('#category').val(category);
        $('#user_type').val(usertype);
        $('#plan').val(plantenure);
        $('#regular-pricing').val(pricing);
        $('#sports-pricing').val(sportsPricing);
        $('#detail').val(detail);
        $('#discount').val(discount);

        var startDate = start.split(' ')[0];
        $('#semester-start').val(startDate);

        var endDate = end.split(' ')[0];
        $('#semester-end').val(endDate);



        $('html, body').animate({scrollTop: 0}, 'slow');
    }
    function deleteRow(mainId){
        var confirmation = window.confirm("Are you sure you want to delete?");
        if (confirmation) {
            $.ajax({
                type: "POST",
                url: "/delete-subscriptionPlan",
                data: {
                    mainId: mainId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#success-flag').text('Deleted Successfully');
                    $('#success-flag').addClass('show');
                    setTimeout(function() {
                        $('#success-flag').removeClass('show');
                    }, 3000); // Hide after 3 seconds
                    LoadStaffSubscriptions();

                },
                error: function(error) {
                    // Handle the error
                    console.error("Error:", error);
                }
            });
        } else {

        }

    }
    function refreshFields(){
        $('#main-Id').val('');
        // $('#category').val('');
        // $('#user_type').val('');
        // $('#plan').val('');
        $('#discount').val('');
        $('#regular-pricing').val('');
        $('#sports-pricing').val('');
        $('#detail').val('');
    }
    function refreshAllFields(){
        $('#main-Id').val('');
        $('#session_sel').val('');
        $('#category').val('');
        $('#user_type').val('');
        $('#plan').val('');
        $('#discount').val('');
        $('#regular-pricing').val('');
        $('#sports-pricing').val('');
        $('#semester-start').val('');
        $('#semester-end').val('');
        $('#detail').val('');
    }
</script>

@stop

