@forelse($budgets as $budget)
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-light">
            <strong>{{ $budget->department->dept_name ?? '-' }}</strong> - {{ $budget->head_title }}
            <span class="float-right">Total:
                {{ number_format($budget->items->sum(fn($i) => $i->quantity * $i->rate), 2) }}
            </span>
        </div>
        <div class="card-body p-0">
            <p class="px-3 py-2"><strong>Remarks:</strong> {{ $budget->remarks ?? '-' }}</p>
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Item Title</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Budget Type</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($budget->items as $item)
                        <tr>
                            <td>{{ $item->item_title }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->rate, 2) }}</td>
                            <td>{{ number_format($item->quantity * $item->rate, 2) }}</td>
                            <td>{{ $item->budget_type }}</td>
                            <td>{{ $item->remarks ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <p class="text-muted">No budget heads available.</p>
@endforelse
