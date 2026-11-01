@extends('layouts.app')

@section('content')
<link href="/css/bnu-layout.css" rel="stylesheet" />
<style>
   .table th {
    color: #fff;
    text-transform: uppercase;
    font-size: 0.875rem;
    font-weight: 400;
    white-space: nowrap;
}
   .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

   /* Tab Styling */
   #quotationTabs {
       gap: 0.75rem;
       border-bottom: none !important;
   }
   #quotationTabs .nav-link {
       border-radius: 999px;
       padding: 0.6rem 1.6rem;
       background: #f3f4f6 !important;
       color: #6b7280 !important;
       border: none !important;
       transition: all 0.2s ease-in-out;
   }
   #quotationTabs .nav-link:not(.active):hover {
       background: #e5e7eb !important;
       color: #4b5563 !important;
   }
   #quotationTabs .nav-link.active {
       background: #17675F !important;
       color: #ffffff !important;
   }
</style>
<div class="container-fluid py-4">
    <div class="" style="background: none !important; color:#000 !important;padding-left:0px !important;padding: 20px !important;">
        <h4 class="mb-0">
            Relevant Executing Department Review
        </h4>
    </div>
    <div class="card shadow-sm border-0" style="padding: 20px 0px !important;">

            

    <div id="alert-container"></div>

    <ul class="nav nav-pills" id="quotationTabs" role="tablist" style="padding-left: 20px !important;">
        <li class="nav-item">
            <a class="nav-link active" id="pending-tab" data-bs-toggle="tab" href="#pending" role="tab">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="reviewed-tab" data-bs-toggle="tab" href="#reviewed" role="tab">Reviewed</a>
        </li>
    </ul>

    <div class="tab-content mt-3" style="padding-left: 20px !important; padding-right: 20px !important;">
        <!-- pending tab -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            <div class="table-responsive">
            <table class="table align-middle">
                <thead style="background-color: #17675F; color: white; text-transform: uppercase;">
                    <tr>
                        <th style="border-top-left-radius: 8px;">Item Title</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Amount</th>
                        <th>Budget Head</th>
                        <th>Requested By</th>
                        <th class="text-center">Type</th>
                        <th class="text-center" style="border-top-right-radius: 8px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pending as $req)
                    {{-- @dd($req) --}}
                        <tr>
                            <td><strong>{{ $req->item_title ?? 'N/A' }}</strong></td>
                            <td>{{ $req->brand ?? '-' }}</td>
                            <td>{{ $req->model ?? '-' }}</td>
                            <td class="text-center">{{ $req->quantity ?? '-' }}</td>
                            <td class="text-end">{{ number_format(($req->quantity ?? 0) * ($req->rate ?? 0), 2) }}</td>
                            <td>{{ $req->head_title ?? $req->category_name ?? '-' }}</td>
                            <td>{{ $req->entry_usrname ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ ucfirst($req->budget_type ?? 'N/A') }}</span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#remarksModal"
                                    data-id="{{ $req->qr_id }}"
                                    data-brand="{{ e($req->brand ?? '') }}"
                                    data-model="{{ e($req->model ?? '') }}">
                                    Add Remarks
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center">No pending requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
     

        <!-- reviewed tab -->
        <div class="tab-pane fade" id="reviewed" role="tabpanel" style="padding-left: 20px !important; padding-right: 20px !important;">
            <div class="table-responsive">
            <table class="table align-middle">
                <thead style="background-color: #17675F; color: white; text-transform: uppercase;">
                    <tr>
                        <th style="border-top-left-radius: 8px;">Item Title</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Amount</th>
                        <th>Budget Head</th>
                        <th>Requested By</th>
                        <th class="text-center">Type</th>
                        <th>Remarks</th>
                        <th style="border-top-right-radius: 8px;">Specifications</th>
                    </tr>
                </thead>
                <tbody id="reviewed-body">
                    @forelse($reviewed as $req)
                        <tr>
                            <td>{{ $req->item_title ?? 'N/A' }}</td>
                            <td>{{ $req->brand ?? '-' }}</td>
                            <td>{{ $req->model ?? '-' }}</td>
                            <td class="text-center">{{ $req->quantity ?? '-' }}</td>
                            <td class="text-end">{{ number_format(($req->quantity ?? 0) * ($req->rate ?? 0), 2) }}</td>
                            <td>{{ $req->head_title ?? '-' }}</td>
                            <td>{{ $req->entry_usrname ?? '-' }}</td>
                            <td class="text-center"><span class="badge bg-success">{{ ucfirst($req->budget_type ?? 'N/A') }}</span></td>
                            <td>{{ $req->parent_department_remarks ?? '-' }}</td>
                            <td>{{ $req->specifications_prnt_dept ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center">No reviewed requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- remarks modal -->
<div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="remarksForm">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Remarks</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <div class="row mb-3">
                  <div class="col-md-6">
                      <label class="form-label">Brand</label>
                      <input type="text" name="brand" id="remarks_brand" class="form-control" placeholder="Enter brand">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label">Model</label>
                      <input type="text" name="model" id="remarks_model" class="form-control" placeholder="Enter model">
                  </div>
              </div>
              <div class="mb-3">
                  <label class="form-label">Relevant Executing Department Remarks</label>
                  <textarea name="parent_department_remarks" class="form-control" required></textarea>
              </div>
              <div class="mb-3">
                  <label class="form-label">Specifications </label>
                  <textarea name="specifications_prnt_dept" class="form-control" required></textarea>
              </div>
              <input type="hidden" name="qr_id" id="qr_id">
          </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Save</button>
        </div>


        </div>
    </form>
  </div>
</div>
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var remarksModal = document.getElementById('remarksModal');
    remarksModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        document.getElementById('qr_id').value = id;
        document.getElementById('remarks_brand').value = button.getAttribute('data-brand') || '';
        document.getElementById('remarks_model').value = button.getAttribute('data-model') || '';
    });

    // AJAX form submission
    $('#remarksForm').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let formData = form.serialize();
        let saveBtn = $('#saveBtn');
        let spinner = $('#spinner');

        saveBtn.prop('disabled', true);
        spinner.removeClass('d-none');

        $.ajax({
            url: '/quotation-requests/remarks/' + $('#qr_id').val(),
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#remarksModal').modal('hide');
                showAlert('success', 'Remarks saved successfully!');
                // Optionally reload or move the updated row to "Reviewed"
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                showAlert('danger', 'Error saving remarks. Please try again.');
            },
            complete: function() {
                saveBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    function showAlert(type, message) {
        $('#alert-container').html(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
    }
});
</script>
@endsection
