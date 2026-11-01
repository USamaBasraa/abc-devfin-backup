@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4>Quotation Request Details</h4>

    <!-- Request Info -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <p><strong>Item Name:</strong> {{ $quotationRequest->item_name }}</p>
            <p><strong>Category:</strong> {{ $quotationRequest->vendorCategory->category_name ?? '-' }}</p>
            <p><strong>Purchase Mode:</strong> {{ ucfirst($quotationRequest->purchase_mode) }}</p>
            <p><strong>Budget Type:</strong> {{ ucfirst($quotationRequest->budget_type) }}</p>
            <p><strong>Remarks:</strong> {{ $quotationRequest->remarks ?? '-' }}</p>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($quotationRequest->created_at)->format('Y-m-d') }}</p>
        </div>
    </div>

    <!-- Vendor Quotations -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <strong>Vendor Quotations</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered m-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vendor</th>
                        <th>Price</th>
                        <th>Currency</th>
                        <th>Delivery Time</th>
                        <th>Warranty</th>
                        <th>Remarks</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotationRequest->vendorQuotations as $q)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $q->vendor->vendor_name ?? '-' }}</td>
                            <td>{{ number_format($q->price, 2) }}</td>
                            <td>{{ $q->currency }}</td>
                            <td>{{ $q->delivery_time ?? '-' }}</td>
                            <td>{{ $q->warranty ?? '-' }}</td>
                            <td>{{ $q->remarks ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($q->created_at)->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No quotations submitted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recommendation Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <strong>Submit Department Recommendation</strong>
        </div>
        <div class="card-body">
            <form id="recommendationForm">
                @csrf
                <input type="hidden" name="qr_id" value="{{ $quotationRequest->qr_id }}">

                <div class="form-group">
                    <label>Recommendation</label>
                    <select name="recommended_flag" class="form-control" required>
                        <option value="">-- Select --</option>
                        <option value="recommended">Recommended</option>
                        <option value="not_recommended">Not Recommended</option>
                        <option value="conditional">Conditional</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit Recommendation</button>
                <div class="recommendationResponse mt-2"></div>
            </form>
        </div>
    </div>

    <!-- All Recommendations -->
    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Department Recommendations</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered m-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Department</th>
                        <th>Recommendation</th>
                        <th>Remarks</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotationRequest->recommendations as $rec)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $rec->department->dept_name ?? '-' }}</td>
                            <td>
                                <span class="badge
                                    @if($rec->recommended_flag == 'recommended') badge-success
                                    @elseif($rec->recommended_flag == 'not_recommended') badge-danger
                                    @else badge-warning @endif">
                                    {{ ucfirst($rec->recommended_flag) }}
                                </span>
                            </td>
                            <td>{{ $rec->remarks ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($rec->created_at)->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No recommendations yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#recommendationForm').submit(function(e){
    e.preventDefault();
    let form = $(this);
    $.post('{{ route("recommendation.store") }}', form.serialize(), function(res){
        $('.recommendationResponse').html(
            `<div class="alert alert-success">${res.message}</div>`
        );
        setTimeout(() => location.reload(), 1000);
    }).fail(function(xhr){
        let errors = xhr.responseJSON.errors;
        let msg = '<ul>';
        $.each(errors, function(key, val){ msg += `<li>${val[0]}</li>`; });
        msg += '</ul>';
        $('.recommendationResponse').html(
            `<div class="alert alert-danger">${msg}</div>`
        );
    });
});
</script>
@endsection
