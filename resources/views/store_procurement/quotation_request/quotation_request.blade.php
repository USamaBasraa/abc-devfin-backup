@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />

<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        {{-- Header --}}

          <div class="card-header" style="background: none !important; color: #252525 !important;">
                <h4 class="mb-0">
                    Quotation Requests
                </h4>

            </div>
        
        <div class="card-body p-4">
            {{-- Response Messages --}}
            <div id="quotationResponse"></div>

            {{-- Quotation Requests by Category --}}
            @php
                $groupedQuotations = $quotations->groupBy(function($q) {
                    return $q->budgetHead->head_title ?? 'Uncategorized';
                });
            @endphp

            @forelse ($groupedQuotations as $category => $items)
                {{-- Category Header --}}
                <div class="mb-4">
                    <div class="px-4 py-3 mb-3 rounded d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold">
                            {{ $category }}
                        </h5>
                        <span class="badge badge-info border px-3 py-2">
                            {{ $items->count() }} {{ Str::plural('Item', $items->count()) }}
                        </span>
                    </div>

                    {{-- Category Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-5 custom-table">
                            <thead>
                                <tr>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Serial</th>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Item Title</th>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Purchase Mode</th>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Remarks</th>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Quantity</th>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Head Approval</th>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Date</th>
                                    <th class="font-weight-bold text-uppercase text-nowrap">Budget Type</th>
                                    <th class="font-weight-bold text-uppercase text-center text-nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $quotation)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="font-weight-bold text-dark">
                                                {{ $quotation->budgetItem->item_title ?? '-' }}
                                            </div>
                                        </td>
                                        <td>{{ ucfirst($quotation->budgetItem->purchase_mode ?? '-') }}</td>
                                        <td><small>{{ Str::limit($quotation->budgetItem->remarks ?? '-', 50) }}</small></td>
                                        <td>{{ $quotation->budgetItem->quantity ?? '-' }}</td>
                                        <td>
                                            @if($quotation->department_head_approval == 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($quotation->department_head_approval == 'rejected')
                                                <span class="badge badge-danger"><i class="fa fa-times mr-1"></i>Rejected</span>
                                            @else
                                                <span class="badge badge-warning"><i class="fa fa-clock-o mr-1"></i>Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($quotation->created_at)->format('M d, Y') }}</td>
                                        <td>{{ ucfirst($quotation->budgetItem->budget_type ?? '-') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('quotation.related.view', $quotation->qr_id) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-primary d-inline-flex align-items-center">
                                                Process
                                                @if($quotation->vendorQuotations->count() > 0)
                                                    <span class="text-white ml-1">({{ $quotation->vendorQuotations->count() }})</span>
                                                @endif
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fa fa-inbox fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">No quotation requests yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let budgetTypeSelect = document.getElementById('budget_type');
    let budgetedField = document.getElementById('budgetedField');
    let reappropriateFields = document.getElementById('reappropriateFields');

    if(budgetTypeSelect) {
        budgetTypeSelect.addEventListener('change', function() {
            let value = this.value;

            // Hide all first
            if(budgetedField) budgetedField.style.display = 'none';
            if(reappropriateFields) reappropriateFields.style.display = 'none';

            if(value === 'budgeted') {
                if(budgetedField) budgetedField.style.display = 'block';
            } else if(value === 're-appropriate') {
                if(reappropriateFields) reappropriateFields.style.display = 'block';
            }
        });
    }
});
</script>

<script>
    $('#quotationForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("quotation.request.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#quotationResponse').html(`<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle mr-2"></i>${response.message}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>`);
                $('#quotationForm')[0].reset();
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let msg = '<ul class="mb-0">';
                $.each(errors, function(k, v) {
                    msg += `<li>${v[0]}</li>`;
                });
                msg += '</ul>';
                $('#quotationResponse').html(`<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-triangle mr-2"></i><strong>Error:</strong><br>${msg}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>`);
            }
        });
    });
</script>
@endsection
