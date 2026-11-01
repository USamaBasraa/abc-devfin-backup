<!DOCTYPE html>
<html>
<head>
    <title>Budget Import</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<div class="container">

    <h2 class="mb-4">Budget Import Panel</h2>

    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Import Button -->
    <form method="POST" action="{{ route('budget.import') }}">
        @csrf
        <button class="btn btn-primary mb-3">
            🔄 Refresh / Import Data
        </button>
    </form>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header">
            Latest Budget Items
        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>BH ID</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Remarks</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->item_id }}</td>
                            <td>{{ $item->bh_id }}</td>
                            <td>{{ $item->item_title }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->rate }}</td>
                            <td>{{ $item->quantity * $item->rate }}</td>
                            <td>{{ $item->remarks }}</td>
                            <td>{{ $item->created_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No data found</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

</body>
</html>