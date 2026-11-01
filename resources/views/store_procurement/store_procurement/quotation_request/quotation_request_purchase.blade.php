@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title mb-3">Request Quotation To Vendors</h4>



            <div id="quotationResponse" class="mt-3"></div>

    </div>

  <div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Submitted Quotation Requests</h5>
            <a href="{{ url('/budgets/list') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-wallet"></i> Go to Budgets
            </a>
        </div>

        <div class="table-responsive">
            @php
                // Group quotations by Category (Budget Head title)
                $groupedQuotations = $quotations->groupBy(function($q) {
                    return $q->budgetHead->head_title ?? 'Uncategorized';
                });
            @endphp

            @forelse ($groupedQuotations as $category => $items)
                <h5 class="mt-4 mb-3 text-primary border-bottom pb-2">
                    <i class="fas fa-folder-open"></i> {{ $category }}
                </h5>

                <table class="table table-bordered table-hover mb-4">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Mode</th>
                            <th>Remarks</th>
                            <th>Quantity</th>
                            <th>Head Approval</th>
                            <th>Date</th>
                            <th>Budget Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $quotation)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $quotation->budgetItem->item_title ?? '-' }}</td>
                                <td>{{ ucfirst($quotation->budgetItem->purchase_mode) }}</td>
                                <td>{{ $quotation->budgetItem->remarks ?? '-' }}</td>
                                <td>{{ $quotation->budgetItem->quantity ?? '-' }}</td>
                                <td>{{ $quotation->department_head_approval ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($quotation->created_at)->format('Y-m-d') }}</td>
                                <td>{{ $quotation->budgetItem->budget_type ?? '-' }}</td>
                                <td>
                                    {{-- <a href="{{ route('quotation.related.view', $quotation->qr_id) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> recommendation
                                        <span class="badge badge-light">{{ $quotation->vendorQuotations->count() }}</span>
                                    </a> --}}

                                    <a href="{{ route('select.quotation.related.view', $quotation->qr_id) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> select
                                        <span class="badge badge-light">{{ $quotation->vendorQuotations->count() }}</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @empty
                <p class="text-center text-muted">No quotation requests yet.</p>
            @endforelse
        </div>
    </div>
</div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let budgetTypeSelect = document.getElementById('budget_type');
    let budgetedField = document.getElementById('budgetedField');
    let reappropriateFields = document.getElementById('reappropriateFields');

    budgetTypeSelect.addEventListener('change', function() {
        let value = this.value;

        // Hide all first
        budgetedField.style.display = 'none';
        reappropriateFields.style.display = 'none';

        if(value === 'budgeted') {
            budgetedField.style.display = 'block';
        } else if(value === 're-appropriate') {
            reappropriateFields.style.display = 'block';
        }
    });
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
                $('#quotationResponse').html(`<div class="alert alert-success">${response.message}</div>`);
                $('#quotationForm')[0].reset();
                // Optionally: reload page or fetch updated list via AJAX
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let msg = '<ul>';
                $.each(errors, function(k, v) {
                    msg += `<li>${v[0]}</li>`;
                });
                msg += '</ul>';
                $('#quotationResponse').html(`<div class="alert alert-danger">${msg}</div>`);
            }
        });
    });
</script>
@endsection
