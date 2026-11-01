@php
    $biDatasets = $biDatasets ?? [];
    $biDefinitions = $biDefinitions ?? [];
    $biFilter = $biFilter ?? [];
    $filterSchools = $filterSchools ?? collect();
    $filterSessions = $filterSessions ?? collect();
    $firstTabKey = $firstTabKey ?? ($biFilter['active_tab'] ?? (array_key_first($biDefinitions) ?: null));
@endphp

<header class="bi-page-header">
    <h1 class="bi-page-title">BI reporting dashboard</h1>
</header>

<div class="bi-card mb-3">
    <div class="bi-nav-wrap">
        <ul class="nav nav-pills flex-wrap" id="biReportTabs" role="tablist">
            @foreach ($biDefinitions as $key => $def)
                @php $tabEnabled = ($def['enabled'] ?? true) === true; @endphp
                <li class="nav-item">
                    @if ($tabEnabled)
                        <a class="nav-link @if ($key === $firstTabKey) active @endif"
                            id="tab-{{ $key }}-tab" data-toggle="pill" href="#tab-{{ $key }}"
                            role="tab" aria-controls="tab-{{ $key }}"
                            aria-selected="{{ $key === $firstTabKey ? 'true' : 'false' }}">
                            {{ $def['label'] }}
                        </a>
                    @else
                        <span class="nav-link disabled bi-tab-disabled"
                            id="tab-{{ $key }}-tab"
                            role="tab"
                            aria-disabled="true"
                            title="This report is temporarily disabled">
                            {{ $def['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
    <div class="bi-panel-body">
        @foreach ($biDefinitions as $key => $def)
            @php
                if (($def['enabled'] ?? true) !== true) {
                    continue;
                }
                $summaryDs = $biDatasets[$key] ?? [
                    'error' => 'Dataset missing.',
                    'displayed' => 0,
                    'stats' => ['extra_kpis' => [], 'compare_kpis' => null],
                ];
                $summaryStats = $summaryDs['stats'] ?? [];
                $summaryFeeStats = $summaryStats['fee_challan_summary'] ?? [];
                $summaryBypassEmptyState =
                    ! empty($summaryStats['compare_kpis']['rows'] ?? [])
                    || ($key === 'fee_challan_summary' && is_array($summaryFeeStats['summary'] ?? null));
                $showTopSummary =
                    empty($summaryDs['error'])
                    && (($summaryDs['displayed'] ?? 0) !== 0 || $summaryBypassEmptyState)
                    && ! empty($summaryStats['extra_kpis'] ?? []);
            @endphp
            <div class="bi-top-summary-pane @if ($key !== $firstTabKey) d-none @endif" data-summary-tab="{{ $key }}">
                @if ($showTopSummary)
                    <div class="bi-dashboard-block mb-0">
                        <div class="bi-section-title">Summary</div>
                        <div class="row">
                            @foreach ($summaryStats['extra_kpis'] as $ek)
                                <div class="col-6 col-md-4 col-lg-3 mb-2">
                                    <div class="bi-kpi bi-kpi-compact">
                                        <div class="kpi-label">{{ $ek['label'] }}</div>
                                        <div class="kpi-value">
                                            {{ number_format((float) ($ek['value'] ?? 0), 0, '.', ',') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

<form method="get" action="{{ route('reports/BIReport') }}" class="bi-filter-card" id="bi-filter-form">
    <input type="hidden" name="active_tab" id="bi_active_tab" value="{{ $firstTabKey }}">
    <div class="bi-filter-card-heading">
        <div>
            <h2 class="bi-filter-title">Filters</h2>
        </div>
        <div class="bi-filter-actions d-flex flex-wrap align-items-center">
            <a href="{{ route('reports/BIReport') }}" class="btn btn-sm btn-outline-secondary mr-2">Reset</a>
            <button type="submit" class="btn btn-sm btn-primary bi-btn-primary">Apply filters</button>
        </div>
    </div>
    <div class="bi-filter-card-inner">
        <div class="bi-filter-group">
            <span class="bi-filter-group-label">Organization scope</span>
            <div class="form-row align-items-end">
                <div class="form-group col-12 col-md-3 mb-2">
                    <label for="bi_filter_sess">Session</label>
                    <select class="form-control form-control-sm" name="sess_id" id="bi_filter_sess">
                        <option value="">All sessions</option>
                        @foreach ($filterSessions as $sess)
                            <option value="{{ $sess->sess_id }}"
                                @if (($biFilter['sess_id'] ?? '') == $sess->sess_id) selected @endif>
                                {{ $sess->sess_name }}@if (! empty($sess->sess_code))
                                    ({{ $sess->sess_code }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-3 mb-2">
                    <label for="bi_filter_school">School</label>
                    <select class="form-control form-control-sm" name="school_id" id="bi_filter_school">
                        <option value="">All schools</option>
                        @foreach ($filterSchools as $sch)
                            <option value="{{ $sch->school_id }}"
                                @if (($biFilter['school_id'] ?? '') == $sch->school_id) selected @endif>{{ $sch->school_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-3 mb-2">
                    <label for="bi_filter_dept">Department</label>
                    <select class="form-control form-control-sm" name="dept_id" id="bi_filter_dept" disabled>
                        <option value="">All departments</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-3 mb-2">
                    <label for="bi_filter_prog">Program</label>
                    <select class="form-control form-control-sm" name="prog_id" id="bi_filter_prog" disabled>
                        <option value="">All programs</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="bi-filter-group">
            <span class="bi-filter-group-label">Dates</span>
            <div class="form-row align-items-start">
                <div class="form-group col-6 col-md-3 mb-2">
                    <label for="bi_filter_date_from">From date</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" id="bi_filter_date_from"
                        value="{{ $biFilter['date_from'] ?? '' }}">
                </div>
                <div class="form-group col-6 col-md-3 mb-2">
                    <label for="bi_filter_date_to">
                        To date
                        <span class="bi-field-hint" title="Must be the same as or after From date. Session with blank dates defaults both to today. Analysis range max 45 days; Grand Summary uses To date as snapshot day.">ⓘ</span>
                    </label>
                    <input type="date" class="form-control form-control-sm" name="date_to" id="bi_filter_date_to"
                        value="{{ $biFilter['date_to'] ?? '' }}">
                    <div class="bi-input-disclaimer">To ≥ From · blank + session → today</div>
                </div>
                <div class="form-group col-6 col-md-3 mb-2">
                    <label for="bi_filter_cmp_a">Compare date A</label>
                    <input type="date" class="form-control form-control-sm" name="compare_date_a" id="bi_filter_cmp_a"
                        value="{{ $biFilter['compare_date_a'] ?? '' }}">
                </div>
                <div class="form-group col-6 col-md-3 mb-2">
                    <label for="bi_filter_cmp_b">
                        Compare date B
                        <span class="bi-field-hint" title="Must be greater than Compare date A. Used only for the Date comparison panel.">ⓘ</span>
                    </label>
                    <input type="date" class="form-control form-control-sm" name="compare_date_b" id="bi_filter_cmp_b"
                        value="{{ $biFilter['compare_date_b'] ?? '' }}">
                    <div class="bi-input-disclaimer">B must be after A</div>
                </div>
            </div>
            <div class="small text-danger mt-1 d-none" id="bi_filter_date_error" role="alert"></div>
        </div>
        <div class="bi-filter-group">
            <span class="bi-filter-group-label">Compare sessions <span class="text-muted font-weight-normal text-lowercase">(optional)</span></span>
            <div class="form-row align-items-end">
                <div class="form-group col-12 col-md-6 mb-2">
                    <label for="bi_filter_cmp_sess_a">Session for Compare date A</label>
                    <select class="form-control form-control-sm" name="compare_sess_id_a" id="bi_filter_cmp_sess_a">
                        <option value="">Same as main session</option>
                        @foreach ($filterSessions as $sess)
                            <option value="{{ $sess->sess_id }}"
                                @if (($biFilter['compare_sess_id_a'] ?? '') == $sess->sess_id) selected @endif>
                                {{ $sess->sess_name }}@if (! empty($sess->sess_code)) ({{ $sess->sess_code }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 mb-2">
                    <label for="bi_filter_cmp_sess_b">Session for Compare date B</label>
                    <select class="form-control form-control-sm" name="compare_sess_id_b" id="bi_filter_cmp_sess_b">
                        <option value="">Same as main session</option>
                        @foreach ($filterSessions as $sess)
                            <option value="{{ $sess->sess_id }}"
                                @if (($biFilter['compare_sess_id_b'] ?? '') == $sess->sess_id) selected @endif>
                                {{ $sess->sess_name }}@if (! empty($sess->sess_code)) ({{ $sess->sess_code }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="bi-card">
    <div class="tab-content bi-panel-body" id="biReportTabContent">
        @foreach ($biDefinitions as $key => $def)
            @php
                $ds = $biDatasets[$key] ?? [
                    'error' => 'Dataset missing.',
                    'columns' => [],
                    'rows' => collect(),
                    'displayed' => 0,
                    'total' => null,
                    'table' => $def['table'],
                    'label' => $def['label'],
                    'stats' => ['row_count' => 0, 'metric_sum' => null, 'metric_label' => null, 'extra_kpis' => [], 'compare_kpis' => null],
                    'filter_dims' => [],
                    'charts' => [],
                ];
                $charts = $ds['charts'] ?? [];
                $tabEnabled = ($def['enabled'] ?? true) === true;
            @endphp
            <div class="tab-pane fade @if ($key === $firstTabKey) show active @endif" id="tab-{{ $key }}"
                role="tabpanel" aria-labelledby="tab-{{ $key }}-tab">
                @if (! $tabEnabled)
                    <div class="bi-toolbar">
                        <div>
                            <span class="bi-badge-soft">{{ $def['label'] }}</span>
                        </div>
                    </div>
                    <div class="bi-empty">
                        <i class="fa fa-lock"></i>
                        This report is temporarily disabled.
                    </div>
                @else
                @php
                    $st = $ds['stats'] ?? [];
                    $_feeChStats = $st['fee_challan_summary'] ?? [];
                    $biBypassEmptyState =
                        ! empty($st['compare_kpis']['rows'] ?? [])
                        || ($key === 'fee_challan_summary' && is_array($_feeChStats['summary'] ?? null));
                @endphp
                <div class="bi-toolbar">
                    <div>
                        <span class="bi-badge-soft">{{ $def['label'] }}</span>
                    </div>
                    @if (empty($ds['error']) && $key !== 'admission_grand_summary')
                        <div class="bi-rows-pill">
                            @if (($ds['total'] ?? null) !== null)
                                <strong>{{ number_format($ds['displayed']) }}</strong> rows in this view
                                @if ($ds['total'] > $ds['displayed'])
                                    <span class="text-muted">(of {{ number_format($ds['total']) }} matching filters)</span>
                                @endif
                            @else
                                <strong>{{ number_format($ds['displayed']) }}</strong> rows loaded
                            @endif
                        </div>
                    @endif
                </div>

                @if (! empty($ds['error']))
                    <div class="alert alert-warning border-0 shadow-sm" role="alert">
                        <strong>Unable to load this report.</strong>
                        <div class="small mt-1 mb-0">{{ $ds['error'] }}</div>
                    </div>
                @elseif (($ds['displayed'] ?? 0) === 0 && ! $biBypassEmptyState)
                    <div class="bi-empty">
                        <i class="fa fa-table"></i>
                        No rows returned. The object exists but is empty, or filters at the database restrict data.
                    </div>
                @else
                    @if (! empty($st['compare_kpis']['rows'] ?? []))
                        <div class="bi-dashboard-block">
                            <div class="bi-section-title">Date comparison</div>
                            @php
                                $_cmpSessLabelA = '';
                                $_cmpSessLabelB = '';
                                if (! empty($biFilter['compare_sess_id_a'])) {
                                    foreach ($filterSessions as $_cs) {
                                        if ($_cs->sess_id == $biFilter['compare_sess_id_a']) {
                                            $_cmpSessLabelA = ' · ' . $_cs->sess_name;
                                            break;
                                        }
                                    }
                                }
                                if (! empty($biFilter['compare_sess_id_b'])) {
                                    foreach ($filterSessions as $_cs) {
                                        if ($_cs->sess_id == $biFilter['compare_sess_id_b']) {
                                            $_cmpSessLabelB = ' · ' . $_cs->sess_name;
                                            break;
                                        }
                                    }
                                }
                                $_isSelectedSchoolCompare = ! empty($biFilter['school_id']) && ! empty($st['compare_kpis']['school_compare'] ?? []);
                                $_hideMetricCompareTable = $_isSelectedSchoolCompare || $key === 'admission_grand_summary';
                            @endphp
                            <p class="small text-muted mb-2">
                                Date A{{ $_cmpSessLabelA }} vs Date B{{ $_cmpSessLabelB }} — compare dates ignore the main from/to range.
                                @if ($_isSelectedSchoolCompare)
                                    <span class="badge badge-info ml-1">Selected school only</span>
                                @endif
                                @if ($_cmpSessLabelA || $_cmpSessLabelB)
                                    <span class="badge badge-secondary ml-1">Cross-session</span>
                                @endif
                            </p>
                            @if (! $_hideMetricCompareTable && ! empty($st['compare_kpis']['highlight_cards'] ?? []))
                                @php
                                    try {
                                        $_cmpDaShort = \Carbon\Carbon::parse($st['compare_kpis']['date_a'])->format('M j');
                                    } catch (\Throwable $e) {
                                        $_cmpDaShort = (string) ($st['compare_kpis']['date_a'] ?? '');
                                    }
                                @endphp
                                <div class="row mb-3">
                                    @foreach ($st['compare_kpis']['highlight_cards'] as $hc)
                                        @php
                                            $_dhc = (float) ($hc['delta'] ?? 0);
                                            $_dhClass =
                                                $_dhc > 0 ? 'bi-compare-delta-pos' : ($_dhc < 0 ? 'bi-compare-delta-neg' : 'text-muted');
                                        @endphp
                                        <div class="col-6 col-lg-3 mb-2">
                                            <div class="bi-kpi bi-kpi-compact bi-compare-card-kpi">
                                                <div class="kpi-label">{{ $hc['label'] }}</div>
                                                <div class="kpi-value">{{ number_format((float) ($hc['val_b'] ?? 0), 0, '.', ',') }}</div>
                                                <div class="bi-compare-card-sub text-muted">
                                                    <span class="mr-2">{{ $_cmpDaShort }}:
                                                        <strong>{{ number_format((float) ($hc['val_a'] ?? 0), 0, '.', ',') }}</strong></span>
                                                    <span>Δ&nbsp;<span class="{{ $_dhClass }}"><strong>{{ number_format($_dhc, 0, '.', ',') }}</strong></span></span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if (! empty($st['compare_kpis']['school_compare'] ?? []))
                                <div class="mb-3">
                                    <div class="small font-weight-bold text-uppercase text-muted mb-2">
                                        School-wise comparison
                                        @if (! empty($biFilter['school_id']))
                                            <span class="badge badge-info ml-1">Selected school</span>
                                        @endif
                                    </div>
                                    <div class="table-responsive bi-compare-panel">
                                        <table class="table table-sm table-bordered bi-compare-table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>School</th>
                                                    <th>{{ $st['compare_kpis']['date_a'] }}</th>
                                                    <th>{{ $st['compare_kpis']['date_b'] }}</th>
                                                    <th>Difference <span class="text-muted font-weight-normal">(B − A)</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (array_slice($st['compare_kpis']['school_compare'], 0, empty($biFilter['school_id']) ? 8 : 1) as $scr)
                                                    @php
                                                        $_sd = (float) ($scr['delta'] ?? 0);
                                                        $_sdClass = $_sd > 0 ? 'bi-compare-delta-pos' : ($_sd < 0 ? 'bi-compare-delta-neg' : '');
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $scr['school'] }}</td>
                                                        <td>{{ number_format((float) ($scr['val_a'] ?? 0), 0, '.', ',') }}</td>
                                                        <td>{{ number_format((float) ($scr['val_b'] ?? 0), 0, '.', ',') }}</td>
                                                        <td class="{{ $_sdClass }}">{{ number_format($_sd, 0, '.', ',') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            @if (! $_hideMetricCompareTable)
                                <div class="table-responsive bi-compare-panel">
                                    <table class="table table-sm table-bordered bi-compare-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Metric</th>
                                            <th>{{ $st['compare_kpis']['date_a'] }}</th>
                                            <th>{{ $st['compare_kpis']['date_b'] }}</th>
                                            <th>Difference <span class="text-muted font-weight-normal">(B − A)</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($st['compare_kpis']['rows'] as $cr)
                                            @php
                                                $d = (float) ($cr['delta'] ?? 0);
                                                $dClass = $d > 0 ? 'bi-compare-delta-pos' : ($d < 0 ? 'bi-compare-delta-neg' : '');
                                            @endphp
                                            <tr>
                                                <td>{{ $cr['label'] }}</td>
                                                <td>{{ number_format((float) ($cr['val_a'] ?? 0), 0, '.', ',') }}</td>
                                                <td>{{ number_format((float) ($cr['val_b'] ?? 0), 0, '.', ',') }}</td>
                                                <td class="{{ $dClass }}">{{ number_format($d, 0, '.', ',') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    @endif
                    @php $rp = ($st['range_pivot'] ?? null); @endphp
                    @if ($key === 'admission_analysis' && is_array($rp) && ! empty($rp['days'] ?? []))
                        <div class="bi-dashboard-block">
                            <div class="bi-section-title">
                                @if (count($rp['days']) === 1)
                                    Day snapshot · selected date
                                @else
                                    Daily breakdown · selected date range
                                @endif
                            </div>
                            <p class="small text-muted mb-2"><strong>{{ $rp['range_label'] ?? '' }}</strong></p>
                            <div class="table-responsive bi-range-pivot-wrap">
                                <table class="table table-sm table-bordered bi-range-pivot-table mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="bi-range-pivot-sticky">Summary Section</th>
                                            @foreach ($rp['days'] as $d)
                                                @php
                                                    try {
                                                        $h = \Carbon\Carbon::parse($d)->format('M j');
                                                    } catch (\Throwable $e) {
                                                        $h = (string) $d;
                                                    }
                                                @endphp
                                                <th scope="col" class="text-right text-nowrap" title="{{ $d }}">{{ $h }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rp['rows'] ?? [] as $pr)
                                            <tr>
                                                <th scope="row" class="bi-range-pivot-sticky">{{ $pr['label'] ?? '' }}</th>
                                                @foreach ($rp['days'] as $di => $__day)
                                                    @php
                                                        $pv = isset($pr['values'][$di]) ? (float) $pr['values'][$di] : 0.0;
                                                    @endphp
                                                    <td class="text-right text-nowrap">{{ number_format($pv, 0, '.', ',') }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if ($key === 'fee_challan_summary')
                        @php
                            $feeRow = collect($ds['rows'] ?? [])->first();
                            $feePrepared = ($st['fee_challan_summary'] ?? [])['summary'] ?? null;
                            $feeColumnKeys = collect($ds['columns'] ?? [])
                                ->pluck('key')
                                ->filter()
                                ->values()
                                ->all();
                            $feeRowMap = [];
                            foreach ((array) $feeRow as $fk => $fv) {
                                $feeRowMap[strtolower(preg_replace('/[^a-z0-9]+/i', '', (string) $fk))] = $fv;
                            }
                            $feePick = function (array $names, ?int $fallbackIndex = null) use ($feeRow, $feeColumnKeys, $feeRowMap) {
                                foreach ($names as $name) {
                                    $nk = strtolower(preg_replace('/[^a-z0-9]+/i', '', (string) $name));
                                    if (array_key_exists($nk, $feeRowMap)) {
                                        return (float) ($feeRowMap[$nk] ?? 0);
                                    }
                                    foreach ($feeColumnKeys as $colKey) {
                                        $ck = strtolower(preg_replace('/[^a-z0-9]+/i', '', (string) $colKey));
                                        if ($ck === $nk) {
                                            return (float) (data_get($feeRow, $colKey) ?? 0);
                                        }
                                    }
                                }
                                if ($fallbackIndex !== null && isset($feeColumnKeys[$fallbackIndex])) {
                                    return (float) (data_get($feeRow, $feeColumnKeys[$fallbackIndex]) ?? 0);
                                }

                                return 0.0;
                            };

                            $feeDeferred = $feePick(['total_deferred_students', 'deferred_students', 'deferred'], 0);
                            $feeCancelled = $feePick(['cancelled', 'canceled', 'cancelled_challan', 'canceled_challan', 'total_cancelled_challan', 'total_canceled_challan'], 1);
                            $feeTotalWithCancelled = $feePick(['total_fee_challan_cancelled', 'total_fee_challan_canceled', 'total_fee_challan_plus_cancelled', 'total_fee_challan_plus_canceled', 'total_fee_challan'], 2);
                            $feeTotalPaidUnpaid = $feePick(['total_paid_unpaid_challan', 'total_paid_unpaid', 'paid_unpaid_challan'], 4);
                            if ($feeTotalPaidUnpaid <= 0 && $feeTotalWithCancelled > 0) {
                                $feeTotalPaidUnpaid = max(0, $feeTotalWithCancelled - $feeCancelled);
                            }
                            $feePaid = $feePick(['total_paid', 'paid', 'paid_challan', 'total_paid_challan'], 5);
                            $feeUnpaid = $feePick(['total_unpaid', 'unpaid', 'unpaid_challan', 'total_unpaid_challan'], 6);
                            if ($feeUnpaid <= 0 && $feeTotalPaidUnpaid > 0) {
                                $feeUnpaid = max(0, $feeTotalPaidUnpaid - $feePaid);
                            }
                            $feeWorkloadCompleted = $feePick(['workload_completed', 'total_workload_completed'], 7);
                            $feeNotEnrolled = $feePick(['not_enrolled', 'total_not_enrolled'], 9);
                            $feeEnrolledUnpaid = $feePick(['enrolled_unpaid', 'total_enrolled_unpaid'], 10);
                            $feeRevisedUnpaid = $feePick(['challan_revised_unpaid', 'revised_unpaid', 'total_challan_revised_unpaid'], 11);
                            $feeScholarshipDateExtend = $feePick(['scholarship_date_extend', 'scholarship_date_extended', 'scholarship_date_ext', 'date_extend', 'date_extended', 'date_ext', 'scholarship', 'total_scholarship_date_extend'], 12);
                            $feeSpringIntake = $feePick(['spring_intake', 'spring_intake_students', 'total_spring_intake'], null);
                            $feeOtherEnrolled = $feePick(['other_enrolled', 'other_students', 'fee_other_enrolled'], null);
                            $feeFrozen = $feePick(['total_frozen_students', 'frozen_students', 'frozen'], 14);
                            $feeFrozenPaid = $feePick(['frozen_paid', 'frozen_paid_students', 'total_frozen_paid', 'dormant_frozen_paid'], null);
                            $feeFrozenUnpaid = $feePick(['frozen_unpaid', 'frozen_unpaid_students', 'total_frozen_unpaid', 'dormant_frozen_unpaid'], null);
                            if (is_array($feePrepared)) {
                                $feeDeferred = (float) ($feePrepared['deferred'] ?? $feeDeferred);
                                $feeCancelled = (float) ($feePrepared['cancelled'] ?? $feeCancelled);
                                $feeTotalWithCancelled = (float) ($feePrepared['total_with_cancelled'] ?? $feeTotalWithCancelled);
                                $feeTotalPaidUnpaid = (float) ($feePrepared['total_paid_unpaid'] ?? $feeTotalPaidUnpaid);
                                $feePaid = (float) ($feePrepared['paid'] ?? $feePaid);
                                $feeUnpaid = (float) ($feePrepared['unpaid'] ?? $feeUnpaid);
                                $feeWorkloadCompleted = (float) ($feePrepared['workload_completed'] ?? $feeWorkloadCompleted);
                                $feeNotEnrolled = (float) ($feePrepared['not_enrolled'] ?? $feeNotEnrolled);
                                $feeEnrolledUnpaid = (float) ($feePrepared['enrolled_unpaid'] ?? $feeEnrolledUnpaid);
                                $feeRevisedUnpaid = (float) ($feePrepared['revised_unpaid'] ?? $feeRevisedUnpaid);
                                $feeScholarshipDateExtend = (float) ($feePrepared['scholarship_date_extend'] ?? $feeScholarshipDateExtend);
                                $feeSpringIntake = (float) ($feePrepared['spring_intake'] ?? $feeSpringIntake);
                                $feeFrozen = (float) ($feePrepared['frozen'] ?? $feeFrozen);
                                $feeFrozenPaid = (float) ($feePrepared['frozen_paid'] ?? $feeFrozenPaid);
                                $feeFrozenUnpaid = (float) ($feePrepared['frozen_unpaid'] ?? $feeFrozenUnpaid);
                            }
                            $feeStrengthParts = [
                                ['label' => 'Paid', 'value' => $feePaid, 'always' => true],
                                ['label' => 'Revised Unpaid', 'value' => $feeRevisedUnpaid, 'always' => false],
                                ['label' => 'Frozen', 'value' => $feeFrozen, 'always' => false],
                                ['label' => 'Frozen Paid', 'value' => $feeFrozenPaid, 'always' => false],
                                ['label' => 'Scholarship/Date Extend', 'value' => $feeScholarshipDateExtend, 'always' => false],
                                ['label' => 'Spring Intake', 'value' => $feeSpringIntake, 'always' => false],
                            ];
                            $feeStrength = array_sum(array_map(static function ($part) {
                                return (float) ($part['value'] ?? 0);
                            }, $feeStrengthParts));
                            $feeNotEnrolledDisplay = $feeNotEnrolled + $feeWorkloadCompleted;
                            $feeEnrolledUnpaidDisplay = $feeScholarshipDateExtend > 0
                                ? $feeUnpaid + $feeScholarshipDateExtend
                                : $feeEnrolledUnpaid;
                        @endphp
                        @if ($feeRow || is_array($feePrepared))
                            <div class="bi-dashboard-block bi-fee-summary bi-report-summary-source" data-summary-tab="{{ $key }}">
                                <div class="bi-section-title">Fee Challan Paid / Unpaid vs Enrollment</div>
                                <div class="row">
                                    <div class="col-lg-7 mb-3 mb-lg-0">
                                        <div class="bi-fee-formula-card">
                                            <div class="bi-fee-formula-row">
                                                <span>Total Fee Challan + Canceled</span>
                                                <strong>{{ number_format($feeTotalWithCancelled, 0, '.', ',') }}</strong>
                                            </div>
                                            <div class="bi-fee-formula-row">
                                                <span>Canceled</span>
                                                <strong>{{ number_format($feeCancelled, 0, '.', ',') }}</strong>
                                            </div>
                                            <div class="bi-fee-formula-row">
                                                <span>Total Paid / Unpaid Challan</span>
                                                <strong>{{ number_format($feeTotalWithCancelled, 0, '.', ',') }} - {{ number_format($feeCancelled, 0, '.', ',') }} = {{ number_format($feeTotalPaidUnpaid, 0, '.', ',') }}</strong>
                                            </div>
                                            <div class="bi-fee-formula-row">
                                                <span>Total Paid</span>
                                                <strong>{{ number_format($feePaid, 0, '.', ',') }}</strong>
                                            </div>
                                            <div class="bi-fee-formula-row bi-fee-attention">
                                                <span>Total Unpaid</span>
                                                <strong>{{ number_format($feeTotalPaidUnpaid, 0, '.', ',') }} - {{ number_format($feePaid, 0, '.', ',') }} = {{ number_format($feeUnpaid, 0, '.', ',') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="bi-fee-mini-grid">
                                            <div class="bi-fee-mini-card">
                                                <span>Not Enrolled</span>
                                                <strong>{{ number_format($feeNotEnrolledDisplay, 0, '.', ',') }}</strong>
                                                <small>{{ number_format($feeWorkloadCompleted, 0, '.', ',') }} workload completed + {{ number_format($feeNotEnrolled, 0, '.', ',') }} not enrolled</small>
                                            </div>
                                            <div class="bi-fee-mini-card bi-fee-mini-warn">
                                                <span>Enrolled Unpaid</span>
                                                <strong>{{ number_format($feeEnrolledUnpaidDisplay, 0, '.', ',') }}</strong>
                                                <small>Unpaid {{ number_format($feeUnpaid, 0, '.', ',') }} + scholarship/date extend {{ number_format($feeScholarshipDateExtend, 0, '.', ',') }}</small>
                                            </div>
                                            <div class="bi-fee-mini-card">
                                                <span>Challan Revised Unpaid</span>
                                                <strong>{{ number_format($feeRevisedUnpaid, 0, '.', ',') }}</strong>
                                            </div>
                                            <div class="bi-fee-mini-card">
                                                <span>Scholarship / Date Extend</span>
                                                <strong>{{ number_format($feeScholarshipDateExtend, 0, '.', ',') }}</strong>
                                            </div>
                                            <div class="bi-fee-mini-card">
                                                <span>Spring Intake</span>
                                                <strong>{{ number_format($feeSpringIntake, 0, '.', ',') }}</strong>
                                            </div>
                                            <div class="bi-fee-mini-card">
                                                <span>Frozen Students</span>
                                                <strong>{{ number_format($feeFrozen, 0, '.', ',') }}</strong>
                                                @if ($feeFrozenPaid > 0 || $feeFrozenUnpaid > 0)
                                                    <small>Paid {{ number_format($feeFrozenPaid, 0, '.', ',') }} + unpaid {{ number_format($feeFrozenUnpaid, 0, '.', ',') }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bi-fee-conclusion">
                                    <div class="bi-fee-strength-row">
                                        <span class="bi-fee-strength-label">Total Students Strength</span>
                                        <span class="bi-fee-strength-formula">
                                            @foreach ($feeStrengthParts as $part)
                                                @if (($part['always'] ?? false) || (float) ($part['value'] ?? 0) > 0)
                                                    @if (! $loop->first) + @endif
                                                    {{ number_format((float) ($part['value'] ?? 0), 0, '.', ',') }} ({{ $part['label'] }})
                                                @endif
                                            @endforeach
                                            = <strong>{{ number_format($feeStrength, 0, '.', ',') }}</strong>
                                        </span>
                                    </div>
                                    <div>
                                        <span>Total Deferred Students (old)</span>
                                        <strong>{{ number_format($feeDeferred, 0, '.', ',') }}</strong>
                                    </div>
                                    <div>
                                        <span>Total Frozen Students</span>
                                        <strong>{{ number_format($feeFrozen, 0, '.', ',') }}</strong>
                                        @if ($feeFrozenPaid > 0 || $feeFrozenUnpaid > 0)
                                            <small class="text-muted ml-1">(Paid {{ number_format($feeFrozenPaid, 0, '.', ',') }} + Unpaid {{ number_format($feeFrozenUnpaid, 0, '.', ',') }})</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if ($key !== 'admission_analysis')
                        @php
                            $schoolBar = $charts['by_school'] ?? [];
                            $schoolLabels = $schoolBar['labels'] ?? [];
                            $schoolValues = $schoolBar['values'] ?? [];
                            $schoolIds    = $schoolBar['ids']    ?? [];
                            $schoolCardPairs = [];
                            $nSchools = $key === 'scholarship_withdrawal_summary'
                                ? min(count($schoolLabels), count($schoolValues))
                                : ($key === 'admission_grand_summary' && ! empty($biFilter['school_id'])
                                    ? min(count($schoolLabels), count($schoolValues))
                                    : min(8, count($schoolLabels), count($schoolValues)));
                            for ($si = 0; $si < $nSchools; $si++) {
                                $schoolCardPairs[] = [
                                    'name'      => (string) ($schoolLabels[$si] ?? ''),
                                    'value'     => (float) ($schoolValues[$si] ?? 0),
                                    'school_id' => $schoolIds[$si] ?? null,
                                ];
                            }
                            $schoolDetailGroups = [];
                            if ($key === 'admission_grand_summary') {
                                $controllerSchoolDetails = $charts['by_school_details'] ?? [];
                                $schoolNameById = [];
                                foreach ($filterSchools as $fs) {
                                    $schoolNameById[(string) ($fs->school_id ?? '')] = (string) ($fs->school_name ?? '');
                                }
                                $rowValue = function ($row, array $keys) {
                                    foreach ($keys as $key) {
                                        if ($key === null || $key === '') {
                                            continue;
                                        }
                                        $value = data_get($row, $key);
                                        if ($value === null && is_string($key)) {
                                            $value = data_get($row, strtolower($key));
                                        }
                                        if ($value === null && is_string($key)) {
                                            $value = data_get($row, strtoupper($key));
                                        }
                                        if ($value !== null && $value !== '') {
                                            return $value;
                                        }
                                    }

                                    return null;
                                };
                                $rowFuzzyValue = function ($row, array $mustContain, array $avoid = []) {
                                    foreach ((array) $row as $rk => $rv) {
                                        $lk = strtolower((string) $rk);
                                        $ok = true;
                                        foreach ($mustContain as $needle) {
                                            if (strpos($lk, strtolower((string) $needle)) === false) {
                                                $ok = false;
                                                break;
                                            }
                                        }
                                        foreach ($avoid as $bad) {
                                            if (strpos($lk, strtolower((string) $bad)) !== false) {
                                                $ok = false;
                                                break;
                                            }
                                        }
                                        if ($ok && $rv !== null && $rv !== '') {
                                            return $rv;
                                        }
                                    }

                                    return null;
                                };
                                $normSchool = function ($value) {
                                    $v = mb_strtolower(trim((string) $value));
                                    $v = str_replace('&', ' and ', $v);
                                    $v = preg_replace('/[^a-z0-9]+/i', '', $v);

                                    return (string) $v;
                                };
                                $schoolDimKey = $ds['filter_dims']['school'] ?? null;
                                $deptDimKey = $ds['filter_dims']['dept'] ?? null;
                                $progDimKey = $ds['filter_dims']['prog'] ?? null;
                                foreach (collect($ds['rows'] ?? []) as $sr) {
                                    $schoolName = $rowValue($sr, ['bi_school_name', 'school_name', 'school', $schoolDimKey, 'school_id'])
                                        ?? $rowFuzzyValue($sr, ['school'], ['dept', 'program', 'prog', 'session', 'date']);
                                    if (is_numeric($schoolName) && isset($schoolNameById[(string) $schoolName])) {
                                        $schoolName = $schoolNameById[(string) $schoolName];
                                    }
                                    if ($schoolName === null || $schoolName === '') {
                                        continue;
                                    }
                                    $deptName = $rowValue($sr, ['bi_department_name', 'dept_name', 'department_name', 'department', $deptDimKey, 'dept_id', 'department_id'])
                                        ?? $rowFuzzyValue($sr, ['dept'], ['school', 'program', 'prog', 'session', 'date'])
                                        ?? $rowFuzzyValue($sr, ['department'], ['school', 'program', 'prog', 'session', 'date'])
                                        ?? 'No department';
                                    $progName = $rowValue($sr, ['bi_program_name', 'prog_name', 'program_name', 'program', $progDimKey, 'prog_id', 'program_id'])
                                        ?? $rowFuzzyValue($sr, ['prog'], ['school', 'dept', 'department', 'session', 'date'])
                                        ?? $rowFuzzyValue($sr, ['program'], ['school', 'dept', 'department', 'session', 'date'])
                                        ?? 'No program';
                                    $metricValue = $rowValue($sr, ['total_applied_applicants', 'TOTAL_APPLIED_APPLICANTS', 'total_count', 'TOTAL_COUNT'])
                                        ?? $rowFuzzyValue($sr, ['total', 'applied'], ['school', 'dept', 'program', 'prog', 'session', 'date'])
                                        ?? 0;
                                    $schoolKey = $normSchool($schoolName);
                                    $deptKey = trim((string) $deptName) !== '' ? trim((string) $deptName) : 'No department';
                                    $progKey = trim((string) $progName) !== '' ? trim((string) $progName) : 'No program';
                                    if (! isset($schoolDetailGroups[$schoolKey])) {
                                        $schoolDetailGroups[$schoolKey] = [];
                                    }
                                    if (! isset($schoolDetailGroups[$schoolKey][$deptKey])) {
                                        $schoolDetailGroups[$schoolKey][$deptKey] = [];
                                    }
                                    if (! isset($schoolDetailGroups[$schoolKey][$deptKey][$progKey])) {
                                        $schoolDetailGroups[$schoolKey][$deptKey][$progKey] = 0.0;
                                    }
                                    $schoolDetailGroups[$schoolKey][$deptKey][$progKey] += (float) $metricValue;
                                }

                                $schoolDetailGroups = [];
                                foreach ($schoolCardPairs as $ci => $card) {
                                    $schoolKey = $normSchool($card['name'] ?? '');
                                    $details = $controllerSchoolDetails[$schoolKey] ?? [];
                                    foreach (($schoolDetailGroups[$schoolKey] ?? []) as $deptName => $programs) {
                                        foreach ($programs as $progName => $countValue) {
                                            $details[] = [
                                                'department' => (string) $deptName,
                                                'program' => (string) $progName,
                                                'count' => (float) $countValue,
                                            ];
                                        }
                                    }
                                    usort($details, function ($a, $b) {
                                        return ($b['count'] <=> $a['count'])
                                            ?: strcmp((string) $a['department'], (string) $b['department'])
                                            ?: strcmp((string) $a['program'], (string) $b['program']);
                                    });
                                    $schoolCardPairs[$ci]['details'] = $details;
                                }
                            }
                            $gymSummaryCards = [];
                            $gymTotals = ['total' => 0.0, 'paid' => 0.0, 'unpaid' => 0.0];
                            $gymSnapshotLabel = null;
                            $attendanceCards = [];
                            $attendanceTotal = 0.0;
                            $attendanceSnapshotLabel = null;
                            if ($key === 'gym_subscription_summary') {
                                $gymRows = collect($ds['rows'] ?? []);
                                $latestCreatedOn = null;
                                foreach ($gymRows as $gr) {
                                    $created = data_get($gr, 'created_on') ?? data_get($gr, 'CREATED_ON');
                                    if ($created !== null && ($latestCreatedOn === null || strcmp((string) $created, (string) $latestCreatedOn) > 0)) {
                                        $latestCreatedOn = $created;
                                    }
                                }
                                if ($latestCreatedOn !== null) {
                                    $gymRows = $gymRows->filter(function ($gr) use ($latestCreatedOn) {
                                        $created = data_get($gr, 'created_on') ?? data_get($gr, 'CREATED_ON');

                                        return (string) $created === (string) $latestCreatedOn;
                                    });
                                }
                                foreach ($gymRows->take(8) as $gr) {
                                    $labelName = data_get($gr, 'label_name') ?? data_get($gr, 'LABEL_NAME') ?? 'Subscription';
                                    $month = data_get($gr, 'subscription_month') ?? data_get($gr, 'SUBSCRIPTION_MONTH');
                                    $totalCount = (float) (data_get($gr, 'total_count') ?? data_get($gr, 'TOTAL_COUNT') ?? 0);
                                    $paidCount = (float) (data_get($gr, 'paid_count') ?? data_get($gr, 'PAID_COUNT') ?? 0);
                                    $unpaidCount = (float) (data_get($gr, 'unpaid_count') ?? data_get($gr, 'UNPAID_COUNT') ?? 0);
                                    if ($totalCount <= 0 && ($paidCount > 0 || $unpaidCount > 0)) {
                                        $totalCount = $paidCount + $unpaidCount;
                                    }
                                    $gymSummaryCards[] = [
                                        'label' => (string) $labelName,
                                        'month' => $month ? (string) $month : null,
                                        'total' => $totalCount,
                                        'paid' => $paidCount,
                                        'unpaid' => $unpaidCount,
                                    ];
                                    $gymTotals['total'] += $totalCount;
                                    $gymTotals['paid'] += $paidCount;
                                    $gymTotals['unpaid'] += $unpaidCount;
                                }
                                if ($latestCreatedOn !== null) {
                                    $gymSnapshotLabel = substr((string) $latestCreatedOn, 0, 19);
                                } elseif (! empty($gymSummaryCards[0]['month'] ?? null)) {
                                    $gymSnapshotLabel = $gymSummaryCards[0]['month'];
                                }
                            }
                            if ($key === 'school_attendance_summary') {
                                $attendanceRow = collect($ds['rows'] ?? [])->first();
                                $attendanceSchoolLabels = [
                                    'school_1' => 'SVAD',
                                    'school_2' => 'SA',
                                    'school_3' => 'SLASS',
                                    'school_4' => 'SMC',
                                    'school_5' => 'SCIT',
                                    'school_6' => 'SE',
                                    'school_7' => 'IP',
                                    'school_8' => 'SLA',
                                    'school_9' => 'DLA',
                                    'school_10' => 'ELU',
                                    'school_11' => 'MGT',
                                    'school_12' => 'SB',
                                    'school_22' => 'SS',
                                    'school_21' => 'SMS',
                                    'school_12_21' => 'SMS',
                                ];
                                if ($attendanceRow) {
                                    foreach ($attendanceSchoolLabels as $schoolColumn => $schoolCode) {
                                        $present = data_get($attendanceRow, $schoolColumn);
                                        if ($present === null) {
                                            $present = data_get($attendanceRow, strtoupper($schoolColumn));
                                        }
                                        if ($present === null) {
                                            continue;
                                        }
                                        $val = (float) $present;
                                        $attendanceCards[] = [
                                            'code' => $schoolCode,
                                            'value' => $val,
                                        ];
                                        $attendanceTotal += $val;
                                    }
                                    $attendanceCreated = data_get($attendanceRow, 'created_on') ?? data_get($attendanceRow, 'CREATED_ON');
                                    if ($attendanceCreated !== null) {
                                        $attendanceSnapshotLabel = substr((string) $attendanceCreated, 0, 19);
                                    }
                                }
                            }
                        @endphp
                        @if (in_array($key, ['admission_grand_summary', 'fee_challan_summary', 'scholarship_withdrawal_summary'], true) && $schoolCardPairs !== [])
                            @php
                                $schoolCardMetricLabel = $key === 'admission_grand_summary'
                                    ? 'Total applied applicants'
                                    : ($key === 'scholarship_withdrawal_summary'
                                        ? 'Scholarship withdrawal total'
                                        : ($schoolBar['title'] ?? 'Fee challan total'));
                                $schoolCardTotal = array_sum(array_map(static function ($card) {
                                    return (float) ($card['value'] ?? 0);
                                }, $schoolCardPairs));
                            @endphp
                            <div class="bi-dashboard-block @if ($key !== 'admission_grand_summary') bi-report-summary-source @endif" data-summary-tab="{{ $key }}">
                                <div class="bi-section-title">Summary</div>
                                <div class="row">
                                    @if ($key === 'scholarship_withdrawal_summary')
                                        <div class="col-6 col-md-4 col-lg-3 mb-2">
                                            <div class="bi-kpi bi-kpi-compact h-100">
                                                <div class="kpi-label">Total</div>
                                                <div class="kpi-value">
                                                    {{ number_format($schoolCardTotal, 0, '.', ',') }}
                                                </div>
                                                <div class="small text-muted mt-1">All schools sum</div>
                                            </div>
                                        </div>
                                    @endif
                                    @foreach ($schoolCardPairs as $scp)
                                        <div class="col-6 col-md-4 col-lg-3 mb-2">
                                            <div class="bi-kpi bi-kpi-compact h-100 @if ($key === 'admission_grand_summary') bi-school-card-clickable @endif"
                                                @if ($key === 'admission_grand_summary')
                                                    role="button" tabindex="0"
                                                    data-tab-key="{{ $key }}"
                                                    data-school-name="{{ $scp['name'] }}"
                                                    data-school-id="{{ $scp['school_id'] ?? '' }}"
                                                    data-school-total="{{ number_format((float) ($scp['value'] ?? 0), 0, '.', ',') }}"
                                                @endif>
                                                <div class="kpi-label">{{ $scp['name'] }}</div>
                                                <div class="kpi-value">
                                                    {{ number_format($scp['value'], 0, '.', ',') }}
                                                </div>
                                                <div class="small text-muted mt-1">{{ $schoolCardMetricLabel }}</div>
                                                @if ($key === 'admission_grand_summary')
                                                    <div class="small text-muted mt-1">Click for program detail</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif ($key === 'gym_subscription_summary' && $gymSummaryCards !== [])
                            <div class="bi-dashboard-block bi-gym-summary bi-report-summary-source" data-summary-tab="{{ $key }}">
                                <div class="bi-section-title">Gym Subscription Summary</div>
                                <div class="row">
                                    @foreach ($gymSummaryCards as $gc)
                                        <div class="col-6 col-lg-3 mb-2">
                                            <div class="bi-gym-card h-100">
                                                <div class="bi-gym-card-label">{{ $gc['label'] }}</div>
                                                @if (! empty($gc['month']))
                                                    <div class="bi-gym-card-month">{{ $gc['month'] }}</div>
                                                @endif
                                                <div class="bi-gym-total">{{ number_format((float) ($gc['total'] ?? 0), 0, '.', ',') }}</div>
                                                <div class="bi-gym-split">
                                                    <span class="bi-gym-paid">Paid {{ number_format((float) ($gc['paid'] ?? 0), 0, '.', ',') }}</span>
                                                    <span class="bi-gym-unpaid">Unpaid {{ number_format((float) ($gc['unpaid'] ?? 0), 0, '.', ',') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="bi-gym-totals">
                                    <div>
                                        <span>Total Count</span>
                                        <strong>{{ number_format($gymTotals['total'], 0, '.', ',') }}</strong>
                                    </div>
                                    <div>
                                        <span>Paid Count</span>
                                        <strong>{{ number_format($gymTotals['paid'], 0, '.', ',') }}</strong>
                                    </div>
                                    <div>
                                        <span>Unpaid Count</span>
                                        <strong>{{ number_format($gymTotals['unpaid'], 0, '.', ',') }}</strong>
                                    </div>
                                </div>
                            </div>
                        @elseif ($key === 'school_attendance_summary' && $attendanceCards !== [])
                            <div class="bi-dashboard-block bi-attendance-summary bi-report-summary-source" data-summary-tab="{{ $key }}">
                                <div class="bi-section-title">School Attendance Summary</div>
                                <div class="row">
                                    @foreach ($attendanceCards as $ac)
                                        <div class="col-6 col-md-4 col-lg-2 mb-2">
                                            <div class="bi-attendance-card h-100">
                                                <div class="bi-attendance-code">{{ $ac['code'] }}</div>
                                                <div class="bi-attendance-value">{{ number_format((float) ($ac['value'] ?? 0), 0, '.', ',') }}</div>
                                                <div class="bi-attendance-label">Students</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="bi-attendance-total">
                                    <span>Total Attendance</span>
                                    <strong>{{ number_format($attendanceTotal, 0, '.', ',') }}</strong>
                                </div>
                            </div>
                        @elseif ($key !== 'fee_challan_summary')
                            <div class="bi-dashboard-block bi-report-summary-source" data-summary-tab="{{ $key }}">
                                <div class="bi-section-title">Overview</div>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="bi-kpi">
                                            <div class="kpi-label">Matching rows</div>
                                            <div class="kpi-value">{{ number_format((int) ($st['row_count'] ?? 0)) }}</div>
                                        </div>
                                    </div>
                                    @if (isset($st['metric_sum']) && $st['metric_sum'] !== null)
                                        <div class="col-md-4 mb-2">
                                            <div class="bi-kpi">
                                                <div class="kpi-label">Sum · {{ $st['metric_label'] ?? 'Key metric' }}</div>
                                                <div class="kpi-value">
                                                    {{ number_format((float) $st['metric_sum'], 0, '.', ',') }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-4 mb-2">
                                        <div class="bi-kpi">
                                            <div class="kpi-label">Rows loaded</div>
                                            <div class="kpi-value">{{ number_format((int) ($ds['displayed'] ?? 0)) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{--
                    @if (! empty($charts['by_date_line']) || ! empty($charts['by_school']))
                        <div class="bi-dashboard-block">
                            <div class="bi-section-title">Charts</div>
                            <div class="row">
                                @if (! empty($charts['by_date_line']))
                                    <div class="col-12 mb-2">
                                        <div class="bi-chart-box">
                                            <div id="bi-chart-line-{{ $key }}" style="min-height:280px;"></div>
                                        </div>
                                    </div>
                                @endif
                                @if (! empty($charts['by_school']))
                                    <div class="col-lg-7 mb-2">
                                        <div class="bi-chart-box">
                                            <div id="bi-chart-bar-{{ $key }}" style="min-height:300px;"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    --}}

                    {{--
                    <div class="bi-dashboard-block">
                        <div class="bi-section-title">Detail grid</div>
                        @if ($key === 'admission_analysis' && ! empty(($ds['stats']['range_pivot'] ?? [])['days']))
                            <p class="small text-muted mb-2">
                                Raw warehouse grain (typically one logical row per day / refresh below). For executive review use
                                <strong>Daily breakdown</strong> above. Search, paging, or export uses the toolbar on this table.
                            </p>
                        @else
                            <p class="small text-muted mb-2">Search, change page size, or export (Excel / print) using the toolbar under the headers.</p>
                        @endif
                        <div class="bi-table-wrap">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 bi-datatable"
                                id="bi-dt-{{ $key }}" width="100%"
                                data-title="{{ $def['label'] }}">
                                <thead>
                                    <tr>
                                        @foreach ($ds['columns'] as $col)
                                            <th>{{ $col['label'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ds['rows'] as $row)
                                        <tr>
                                            @foreach ($ds['columns'] as $col)
                                                @php
                                                    $v = data_get($row, $col['key']);
                                                    if ($v instanceof \Carbon\Carbon) {
                                                        $cell = $v->format('Y-m-d H:i:s');
                                                    } elseif (is_bool($v)) {
                                                        $cell = $v ? 'Yes' : 'No';
                                                    } elseif ($v === null) {
                                                        $cell = '';
                                                    } else {
                                                        $cell = $v;
                                                    }
                                                @endphp
                                                <td>{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                    --}}
                @endif
                @endif
            </div>
        @endforeach
    </div>
</div>

<div class="modal fade" id="biSchoolBreakdownModal" tabindex="-1" role="dialog" aria-labelledby="biSchoolBreakdownTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content bi-school-modal">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="biSchoolBreakdownTitle">School detail</h5>
                    <div class="small text-muted" id="biSchoolBreakdownSubtitle"></div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0 bi-school-breakdown-table">
                        <thead>
                            <tr>
                                <th>Program</th>
                                <th class="text-right">Count</th>
                            </tr>
                        </thead>
                        <tbody id="biSchoolBreakdownRows">
                            <tr>
                                <td colspan="2" class="text-muted text-center">No detail found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
