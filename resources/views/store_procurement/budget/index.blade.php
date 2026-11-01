@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />
<div class="container mt-4">
    <h4>All Department Budgets</h4>

    <a href="{{ route('budget.create') }}" class="btn btn-primary mb-3">+ Create New Budget</a>

    @foreach($budgets as $budget)
        <div class="card mb-4">
            <div class="card-header">
                <strong>{{ $budget->head_title }}</strong>
                ({{ $budget->department->dept_name ?? '-' }})
            </div>
            <div class="card-body">
                <p><strong>Remarks:</strong> {{ $budget->remarks ?? '-' }}</p>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach($budget->items as $item)
                            @php $total += ($item->quantity * $item->rate); @endphp
                            <tr>
                                <td>{{ $item->item_title }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->rate,2) }}</td>
                                <td>{{ number_format($item->quantity * $item->rate,2) }}</td>
                                <td>{{ $item->remarks ?? '-' }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-info">
                            <td colspan="3" class="text-right"><strong>Total</strong></td>
                            <td colspan="2"><strong>{{ number_format($total,2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
