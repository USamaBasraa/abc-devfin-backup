@extends('layouts.app')
@section('title')
BNU CMS
@stop
@section('content')
<style type="text/css">
    .summary-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        background: #ffffff;
        margin: 15px auto;
        width: 98%;
        overflow: hidden;
    }

    .summary-card .card-header {
        background: linear-gradient(135deg, #1f3bb3, #3858f9);
        color: #ffffff;
        border-radius: 12px 12px 0 0;
        padding: 18px 25px;
        border-bottom: none;
    }

    .summary-card .card-header h3 {
        margin: 0;
        font-weight: 600;
        font-size: 1.35rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-panel {
        background-color: #fcfdfe;
        border: 1px solid #ebedf2;
        border-radius: 10px;
        padding: 20px;
        margin: 15px 25px 25px 25px;
    }

    .table-responsive-custom {
        padding: 0 25px 25px 25px;
    }

    .table-custom {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #ebedf2;
        border-radius: 8px;
        overflow: hidden;
    }

    .table-custom th {
        background-color: #f5f7ff;
        color: #1f3bb3;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.6px;
        border-bottom: 2px solid #e4e7ed;
        border-top: none;
        padding: 14px 16px;
        text-align: left;
    }

    .table-custom td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #ebedf2;
        font-size: 0.9rem;
        color: #333;
    }

    .table-custom tbody tr:hover {
        background-color: #f8f9ff;
        transition: background-color 0.15s ease-in-out;
    }

    .table-custom .total-row {
        background-color: #eaedf7;
        font-weight: 700;
    }

    .table-custom .total-row td {
        border-top: 2px solid #cbd4f0;
        border-bottom: none;
        color: #1f3bb3;
    }

    .btn-custom {
        border-radius: 6px;
        padding: 8px 18px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-custom:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-custom i {
        font-size: 1rem;
    }

    .form-control-custom {
        border-radius: 6px;
        border: 1px solid #cccccc;
        padding: 8px 12px;
        height: 38px;
        transition: border-color 0.2s;
    }

    .form-control-custom:focus {
        border-color: #3858f9;
        box-shadow: 0 0 0 3px rgba(56, 88, 249, 0.15);
        outline: none;
    }
</style>

<div class="card summary-card">
    <div class="card-header">
        <h3>
            <i class="fa fa-bar-chart" aria-hidden="true"></i>
            E-Portal Request Summary
        </h3>
    </div>

    <!-- Filter Form -->
    <div class="filter-panel">
        <form method="GET" action="{{ route('eportal-summary') }}" id="filterForm">
            <div class="row align-items-end">
                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <label for="from_date" class="font-weight-bold text-secondary mb-1">From Date</label>
                    <input type="date" name="from_date" id="from_date" class="form-control form-control-custom"
                        value="{{ $from_date ?? '' }}">
                </div>
                <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                    <label for="to_date" class="font-weight-bold text-secondary mb-1">To Date</label>
                    <input type="date" name="to_date" id="to_date" class="form-control form-control-custom"
                        value="{{ $to_date ?? '' }}">
                </div>
                <div class="col-md-6 col-sm-12 text-md-right text-left">
                    <button type="submit" class="btn btn-primary btn-custom mr-2">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('eportal-summary') }}" class="btn btn-secondary btn-custom mr-2">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                    <button type="button" onclick="pdf()" class="btn btn-danger btn-custom">
                        <i class="fa fa-file-pdf-o"></i> Download PDF
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Table -->
    <div class="table-responsive-custom">
        <div class="table-responsive">
            <table class="table table-custom table-hover">
                <thead>
                    <tr>
                        <th style="width: 7%; text-align: center;">Sr. No</th>
                        <th style="width: 25%;">Request Type</th>
                        <th style="width: 12%; text-align: center;">Total Request</th>
                        <th style="width: 14%; text-align: center;">Resolve in Time</th>
                        <th style="width: 14%; text-align: center;">Resolve in Time %</th>
                        <th style="width: 14%; text-align: center;">Resolved After Escalation</th>
                        <th style="width: 14%; text-align: center;">Resolved After Escalation %</th>
                        <th style="width: 10%; text-align: center;">Pending</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sr = 1;
$sumTotal = 0;
$inTime = 0;
$afterTime = 0;
$pending = 0;
                    ?>
                    @forelse($res as $rec)
                                        <?php 
                                            $sumTotal += $rec->total_requests;
                        $inTime += $rec->count_resolving_before_escalation;
                        $afterTime += $rec->count_resolving_after_escalation;
                        $pending += $rec->pending_request;
                                            ?>
                                        <tr>
                                            <td style="text-align: center;">{{ $sr++ }}</td>
                                            <td class="font-weight-bold text-dark">{{ $rec->ec_title }}</td>
                                            <td style="text-align: center;">{{ $rec->total_requests }}</td>
                                            <td style="text-align: center; color: #2dce89;">{{ $rec->count_resolving_before_escalation }}
                                            </td>
                                            <td style="text-align: center;" class="font-weight-bold text-success">
                                                {{ $rec->resolving_before_escalation_percentage }}%</td>
                                            <td style="text-align: center; color: #f5365c;">{{ $rec->count_resolving_after_escalation }}
                                            </td>
                                            <td style="text-align: center;" class="font-weight-bold text-danger">
                                                {{ $rec->resolving_after_escalation_percentage }}%</td>
                                            <td style="text-align: center;" class="font-weight-bold text-warning">
                                                {{ $rec->pending_request }}</td>
                                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fa fa-info-circle mr-1"></i> No records found for the selected date range.
                            </td>
                        </tr>
                    @endforelse

                    @if($sumTotal > 0)
                        <tr class="total-row">
                            <td></td>
                            <td>Total Summary</td>
                            <td style="text-align: center;">{{ $sumTotal }}</td>
                            <td style="text-align: center;">{{ $inTime }}</td>
                            <td style="text-align: center;">{{ ceil(($inTime / $sumTotal) * 100) }}%</td>
                            <td style="text-align: center;">{{ $afterTime }}</td>
                            <td style="text-align: center;">{{ ceil(($afterTime / $sumTotal) * 100) }}%</td>
                            <td style="text-align: center;">{{ $pending }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@stop
@section('javascript')
<script src="{{ asset('js/bootstrap-autocomplete.js') }}"></script>
<script type="text/javascript">
    function pdf() {
        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();
        var url = '/assets/cms/reports/eportal_summary.php?from_date=' + encodeURIComponent(fromDate) + '&to_date=' + encodeURIComponent(toDate);
        window.open(url, '_blank');
    }
</script>
@stop