@php
    $uid = $uid ?? ('item-' . ($overview['item_id'] ?? uniqid()));
    $vendorCount = count($vendorSubmissions ?? []);
@endphp

<div id="itemDetailAccordion-{{ $uid }}">
    <div class="card mb-4">
        <div class="card-header bg-white border-bottom-0 p-0">
            <button class="btn btn-link btn-block text-left d-flex justify-content-between align-items-center px-3 py-3"
                type="button"
                data-toggle="collapse"
                data-target="#itemOverviewSection-{{ $uid }}"
                aria-expanded="true"
                aria-controls="itemOverviewSection-{{ $uid }}">
                <span class="text-primary font-weight-semibold">Item Overview</span>
                <i class="fa fa-chevron-down"></i>
            </button>
        </div>
        <div id="itemOverviewSection-{{ $uid }}" class="collapse show" data-parent="#itemDetailAccordion-{{ $uid }}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Item Title</h6>
                        <p class="mb-0 font-weight-semibold">{{ $overview['item_title'] ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted mb-1">Budget Head</h6>
                        <p class="mb-0">{{ $overview['head_title'] ?? '-' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <h6 class="text-muted mb-1">Brand</h6>
                        <p class="mb-0">{{ $overview['brand'] ?? '-' }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6 class="text-muted mb-1">Model</h6>
                        <p class="mb-0">{{ $overview['model'] ?? '-' }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6 class="text-muted mb-1">Category</h6>
                        <p class="mb-0">{{ $overview['category'] ?? '-' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <h6 class="text-muted mb-1">Quantity</h6>
                        <p class="mb-0">{{ number_format((float) ($overview['quantity'] ?? 0)) }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6 class="text-muted mb-1">Rate (PKR)</h6>
                        <p class="mb-0">{{ number_format((float) ($overview['rate'] ?? 0), 2) }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6 class="text-muted mb-1">Total (PKR)</h6>
                        <p class="mb-0 font-weight-semibold">{{ number_format((float) ($overview['total'] ?? 0), 2) }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <h6 class="text-muted mb-1">Department</h6>
                        <p class="mb-0">{{ $overview['department'] ?? '-' }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6 class="text-muted mb-1">Budget Type</h6>
                        <p class="mb-0 text-uppercase">{{ $overview['budget_type'] ?? '-' }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6 class="text-muted mb-1">Academic Year</h6>
                        <p class="mb-0">{{ $overview['academic_year'] ?? '-' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-muted mb-1">Item Remarks</h6>
                        <p class="mb-0">{{ !empty($overview['remarks']) ? $overview['remarks'] : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white border-bottom-0 p-0">
            <button class="btn btn-link btn-block text-left d-flex justify-content-between align-items-center px-3 py-3"
                type="button"
                data-toggle="collapse"
                data-target="#vendorSubmissionSection-{{ $uid }}"
                aria-expanded="false"
                aria-controls="vendorSubmissionSection-{{ $uid }}">
                <div class="d-flex align-items-center">
                    <span class="text-primary font-weight-semibold">Vendor Submission History</span>
                    <span class="badge badge-pill badge-primary ml-2">{{ $vendorCount }} {{ \Illuminate\Support\Str::plural('Vendor', $vendorCount) }}</span>
                </div>
                <i class="fa fa-chevron-down"></i>
            </button>
        </div>
        <div id="vendorSubmissionSection-{{ $uid }}" class="collapse" data-parent="#itemDetailAccordion-{{ $uid }}">
            <div class="card-body">
                @if(empty($vendorSubmissions))
                    <div class="alert alert-secondary mb-0">
                        @if(empty($overview['has_quotation_request']))
                            No quotation request created for this item yet.
                        @else
                            No vendor quotations submitted yet for this item.
                        @endif
                    </div>
                @else
                    <div class="alert alert-secondary d-flex align-items-center">
                        <i class="fa fa-industry mr-2 text-primary"></i>
                        <span class="font-weight-semibold">Vendor quotation versions submitted against this budget item.</span>
                    </div>

                    @foreach($vendorSubmissions as $vendorIndex => $vendor)
                        @php
                            $vendorBlockId = $uid . '-v' . $vendorIndex;
                            $versions = $vendor['versions'] ?? [];
                            $defaultVersion = $versions[0]['version_number'] ?? 1;
                        @endphp
                        <div class="border rounded mb-3">
                            <div class="px-3 py-3 bg-light border-bottom">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 42px; height: 42px;">
                                            <i class="fa fa-building"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-dark">{{ $vendor['vendor_name'] ?? 'Vendor' }}</h6>
                                        </div>
                                    </div>
                                    <div class="mt-3 mt-lg-0 text-lg-right">
                                        <div class="badge badge-primary mb-0">
                                            Latest Offer:
                                            {{ number_format((float) ($vendor['latest_rate'] ?? 0), 2) }} PKR
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-3 py-3">
                                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-end mb-3">
                                    <div class="form-group mb-0 w-100 w-lg-25 ml-lg-auto">
                                        <label class="sr-only" for="versionSelect-{{ $vendorBlockId }}">Select Version</label>
                                        <select id="versionSelect-{{ $vendorBlockId }}"
                                                class="custom-select custom-select-sm vendor-version-select"
                                                data-target="#versionDetails-{{ $vendorBlockId }}">
                                            @foreach($versions as $version)
                                                <option value="{{ $vendorBlockId }}-ver-{{ $version['version_number'] }}"
                                                    {{ $loop->first ? 'selected' : '' }}>
                                                    Version {{ $version['version_number'] }} - Submitted {{ $version['submitted_at'] ?? '-' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div id="versionDetails-{{ $vendorBlockId }}">
                                    @foreach($versions as $version)
                                        <div class="vendor-version-card border rounded p-3 mb-3 {{ $loop->first ? '' : 'd-none' }}"
                                             data-version="{{ $vendorBlockId }}-ver-{{ $version['version_number'] }}">
                                            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-3">
                                                <div>
                                                    <span class="badge {{ !empty($version['is_current']) ? 'badge-primary' : 'badge-light border' }} text-uppercase mr-2">
                                                        {{ $version['status_label'] ?? 'Submitted' }}
                                                    </span>
                                                    <span class="text-muted small">{{ $version['submitted_at'] ?? '-' }}</span>
                                                </div>
                                                <div class="text-md-right mt-2 mt-md-0">
                                                    <span class="h5 mb-0 text-primary">{{ number_format((float) ($version['rate'] ?? 0), 2) }} PKR</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <span class="text-muted small d-block">Item Title</span>
                                                    <span class="font-weight-semibold text-dark">{{ $version['item_title'] ?? '-' }}</span>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <span class="text-muted small d-block">Brand</span>
                                                    <span>{{ $version['brand'] ?? '-' }}</span>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <span class="text-muted small d-block">Model</span>
                                                    <span>{{ $version['model'] ?? '-' }}</span>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <span class="text-muted small d-block">Quantity</span>
                                                    <span>{{ $version['quantity'] !== null && $version['quantity'] !== '' ? number_format((float) $version['quantity']) : '-' }}</span>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <span class="text-muted small d-block">Rate</span>
                                                    <span>{{ number_format((float) ($version['rate'] ?? 0), 2) }} PKR</span>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <span class="text-muted small d-block">Price</span>
                                                    <span>{{ number_format((float) ($version['price'] ?? 0), 2) }} {{ $version['currency'] ?? 'PKR' }}</span>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <span class="text-muted small d-block">Currency</span>
                                                    <span>{{ $version['currency'] ?? 'PKR' }}</span>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <span class="text-muted small d-block">Advance Payment</span>
                                                    <span>{{ $version['advance_required'] ?? 'No' }}</span>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <span class="text-muted small d-block">Advance %</span>
                                                    <span>{{ $version['advance_percent'] !== null && $version['advance_percent'] !== '' ? $version['advance_percent'] . '%' : '-' }}</span>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <span class="text-muted small d-block">Delivery</span>
                                                    <span>{{ $version['delivery_time'] ?? '-' }}</span>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <span class="text-muted small d-block">Warranty</span>
                                                    <span>{{ $version['warranty'] ?? '-' }}</span>
                                                </div>
                                                <div class="col-md-8 mb-3">
                                                    <span class="text-muted small d-block">Specifications</span>
                                                    <span>{{ $version['specifications'] ?? '-' }}</span>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <span class="text-muted small d-block">Remarks</span>
                                                    <span>{{ $version['remarks'] ?? '-' }}</span>
                                                </div>
                                                <div class="col-md-12">
                                                    <span class="text-muted small d-block">Attachment</span>
                                                    @if(!empty($version['attachment_url']))
                                                        <a href="{{ $version['attachment_url'] }}" target="_blank" class="d-inline-flex align-items-center">
                                                            <i class="fa fa-paperclip text-primary mr-2"></i>
                                                            {{ basename((string) ($version['attachment'] ?? 'Attachment')) }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
