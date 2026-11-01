<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Item Name</th>

            <th>Department</th>
            <th>Reasoning</th>
            <th>Budget Type</th>
            <th>Status</th>
            @if ($status === 'pending')
                <th class="text-center">Action</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse ($items as $index => $item)
         {{-- @dd($item) --}}
            <tr id="item-row-{{ $item->item_id }}">
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->item_title ?? 'N/A' }}</td>
                {{-- <td>{{ $item->item_title ?? 'N/A' }}</td> --}}
                <td>{{ $item->dept_name ?? 'N/A' }}</td>
                <td>{{ $item->reason_for_approval ?? 'N/A' }}</td>

                <td>{{ ucfirst($item->budget_typei) }}</td>
                <td>
                    @if ($status === 'pending')
                        <span class="badge badge-warning">Pending</span>
                    @elseif ($status === 'approved')
                        <span class="badge badge-success">Approved</span>
                    @else
                        <span class="badge badge-danger">Declined</span>
                    @endif
                </td>

                @if ($status === 'pending')
                <td class="text-center">
                    <button class="btn btn-sm btn-success approve-btn" data-item-id="{{ $item->item_id }}">Approve</button>
                    <button class="btn btn-sm btn-danger decline-btn" data-item-id="{{ $item->item_id }}">Decline</button>
                </td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="{{ $status === 'pending' ? 5 : 4 }}" class="text-center">
                    No {{ $status }} items found.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
