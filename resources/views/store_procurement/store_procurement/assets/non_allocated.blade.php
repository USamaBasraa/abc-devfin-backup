@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />
<div class="container">
    <h2>Non-Allocated Assets</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Serial No</th>
                <th>Unique Asset Code</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr id="item-{{ $item->asset_item_id }}">
                <td>{{ $item->serial_number }}</td>
                <td>{{ $item->unique_asset_code }}</td>
                <td>{{ $item->allocation_status }}</td>
                <td>
                    <button class="btn btn-primary btn-sm allocate-btn"
                        data-id="{{ $item->asset_item_id }}"
                        data-serial="{{ $item->serial_number }}">
                        Allocate
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Allocation Modal -->
<div class="modal fade" id="allocateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="allocateForm">
      @csrf
      <input type="hidden" name="asset_item_id" id="asset_item_id">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Allocate Asset</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <p id="serial_display"></p>
          <div class="mb-3">
            <label>Department</label>
            <input type="number" name="department_id" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Location</label>
            <input type="text" name="location" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Allocate</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    // Open modal
    $('.allocate-btn').on('click', function () {
        let id = $(this).data('id');
        let serial = $(this).data('serial');

        $('#asset_item_id').val(id);
        $('#serial_display').text("Serial No: " + serial);
        $('#allocateModal').modal('show');
    });

    // Handle allocation
    $('#allocateForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('assets.allocate') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function (response) {
                if (response.success) {
                    $('#allocateModal').modal('hide');
                    alert(response.message);
                    location.reload();
                }
            },
            error: function (xhr) {
                alert("Error: " + xhr.responseJSON.message);
            }
        });
    });
});
</script>
@endsection
