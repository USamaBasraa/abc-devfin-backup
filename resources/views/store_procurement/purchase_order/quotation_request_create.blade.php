@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="card-title mb-3">Request Quotation To Vendors</h4>

            <form id="quotationForm">
                @csrf

                <div class="form-group">
                    <label for="item_category">Item Category</label>
                    <select class="form-control" name="vendor_category_id" required>
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- <div class="form-group">
                    <label for="budget_type">Budget Type</label>
                    <select class="form-control" name="budget_type" required>
                        <option value="">-- Select Budget Type --</option>
                        <option value="budgeted">Budgeted</option>
                        <option value="unbudgeted">Unbudgeted</option>
                        <option value="re-appropriate">Re-appropriate</option>
                    </select>
                </div> --}}
                <div class="form-group">
                    <label for="budget_type">Budget Type</label>
                    <select class="form-control" name="budget_type" id="budget_type" required>
                        <option value="">-- Select Budget Type --</option>
                        <option value="budgeted">Budgeted</option>
                        <option value="unbudgeted">Unbudgeted</option>
                        <option value="re-appropriate">Re-appropriate</option>
                    </select>
                </div>

                <!-- Budget ID for Budgeted -->
                <div class="form-group" id="budgetedField" style="display:none;">
                    <label for="budget_id">Budget ID</label>
                    <input type="text" class="form-control" name="budget_id" placeholder="Enter Budget ID">
                </div>

                <!-- Two Budget IDs for Re-appropriate -->
                <div id="reappropriateFields" style="display:none;">
                    <div class="form-group">
                        <label for="from_budget_id">From Budget ID</label>
                        <input type="text" class="form-control" name="from_budget_id" placeholder="Enter From Budget ID">
                    </div>
                    <div class="form-group">
                        <label for="to_budget_id">To Budget ID</label>
                        <input type="text" class="form-control" name="to_budget_id" placeholder="Enter To Budget ID">
                    </div>
                </div>

                <div class="form-group">
                    <label for="item_name">Item Name</label>
                    <input type="text" class="form-control" name="item_name" placeholder="Enter item name" required>
                </div>

                <div class="form-group">
                    <label for="purchase_mode">Purchase Mode</label>
                    <select class="form-control" name="purchase_mode" required>
                        <option value="">-- Select Mode --</option>
                        <option value="purchase">Purchase</option>
                        <option value="rent">Rent</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="model_no">Model No</label>
                    <input type="text" class="form-control" name="model_no" placeholder="Enter model number">
                </div>

                <div class="form-group">
                    <label for="remarks">Remarks</label>
                    <textarea class="form-control" name="remarks" placeholder="Enter any remarks"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Quotation Request
                </button>
            </form>

            <div id="quotationResponse" class="mt-3"></div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Submitted Quotation Requests</h5>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Mode</th>
                            {{-- <th>Model No</th> --}}
                            <th>Remarks</th>
                            <th>Quantity</th>
                            <th>Head Approval</th>
                            <th>Date</th>
                            <th>Budget Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                   <tbody>
    @forelse ($quotations as $quotation)
        <tr>
            <td>{{ $loop->iteration }}</td>

            {{-- Show Budget Item Name instead of ID --}}
            <td>{{ $quotation->budgetItem->item_title ?? '-' }}</td>

            <td>{{ $quotation->budgetHead->head_title  ?? '-' }}</td>
            <td>{{ ucfirst($quotation->budgetItem->purchase_mode) }}</td>
            {{-- <td>{{ $quotation->model_no ?? '-' }}</td> --}}
            <td>{{ $quotation->budgetItem->remarks ?? '-' }}</td>
            <td>{{ $quotation->budgetItem->quantity ?? '-' }}</td>
            <td>{{ $quotation->department_head_approval ?? '-' }}</td>
            <td>{{ \Carbon\Carbon::parse($quotation->created_at)->format('Y-m-d') }}</td>

            {{-- Show Budget Head Name + Budget Type --}}
            <td>{{ $quotation->budgetItem->budget_type ?? '-' }} </td>

            <td>
                <a href="{{ route('quotation.related.view', $quotation->qr_id) }}"
                   target="_blank"
                   class="btn btn-sm btn-info">
                    <i class="fas fa-eye"></i> View
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="10" class="text-center">No quotation requests yet.</td>
        </tr>
    @endforelse
</tbody>

                </table>
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
