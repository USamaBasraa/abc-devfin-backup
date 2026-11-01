@extends('layouts.app')

@section('content')
@include('components.toast-notifications')
<link href="/css/bnu-layout.css" rel="stylesheet" />
<div class="container-fluid">
    <div class="card-header" style="background: none !important; color: #252525 !important;border-bottom: none !important;">
        <h4 class="mb-0">
            Assets List
        </h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <ul class="nav nav-pills gap-2" id="quotationTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active rounded-pill px-4 py-2" id="pending-tab" data-bs-toggle="tab" href="#pending" role="tab">Pending Items</a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-4 py-2" id="reviewed-tab" data-bs-toggle="tab" href="#reviewed" role="tab">Recieved Items</a>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <!-- pending tab -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            <table class="table">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" style="background: #4562C3 !important; color: #fff !important;">PO ID</th>
                        <th class="text-center" style="background: #4562C3 !important; color: #fff !important;">Serial No</th>
                        <th class="text-center" style="background: #4562C3 !important; color: #fff !important;">Asset Code</th>
                        <th class="text-center" style="background: #4562C3 !important; color: #fff !important;">Warranty Period</th>
                        <th class="text-center" style="background: #4562C3 !important; color: #fff !important;">Name</th>
                        <th class="text-center" style="background: #4562C3 !important; color: #fff !important;">Item Title</th>
                        <th class="text-center" style="background: #4562C3 !important; color: #fff !important;">Action</th>                       
                    </tr>
                </thead>
                <tbody style="background: #fff !important;">
                    @forelse($resSql as $req)
                    {{-- @dd($req) --}}
                        <tr>
                            <td class="font-weight-bold">{{ $req->po_id ?? 'n/a' }}</td>
                            <td>{{ $req->serial_number ?? '-' }}</td>
                            <td>{{ $req->unique_asset_code ?? '-' }}</td>
                            <td class="text-center">{{ $req->warranty_period ?? '-' }}</td>
                            
                            <td>{{ $req->name ?? '-' }}</td>
                            <td>{{ $req->item_title ?? '-' }}</td>
                         
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary" 
                                        onclick="openModal('{{ $req->asset_id }}','{{ $req->serial_number }}')">
                                    Acknowledge
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center">no pending requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- reviewed tab -->
          
        <div class="tab-pane fade" id="reviewed" role="tabpanel">
            <table class="table" id="reviewedTable">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" style="background: #4562C3 !important; color: #fff !important;">PO ID</th>
                        <th class="text-center" style="background: #4562C3 !important; color: #fff !important;">Serial No</th>
                        <th class="text-center" style="background: #4562C3 !important; color: #fff !important;">Asset Code</th>
                        <th class="text-center" style="background: #4562C3 !important; color: #fff !important;">Warranty Period</th>
                        <th class="text-center" style="background: #4562C3 !important; color: #fff !important;">Name</th>
                        <th class="text-center" style="background: #4562C3 !important; color: #fff !important;">Item Title</th>
                        <th class="text-center" style="background: #4562C3 !important; color: #fff !important;">Item Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resSqlsqlrecieved as $req)
                        <tr>
                            <td class="font-weight-bold">{{ $req->po_id ?? 'n/a' }}</td>
                            <td>{{ $req->serial_number ?? '-' }}</td>
                            <td>{{ $req->unique_asset_code ?? '-' }}</td>
                            <td class="text-center">{{ $req->warranty_period ?? '-' }}</td>                            
                            <td>{{ $req->name ?? '-' }}</td>
                            <td>{{ $req->item_title ?? '-' }}</td>
                            <td>{{ $req->allocation_status ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center">no reviewed requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
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
            <h5 class="modal-title">add remarks</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <div class="mb-3">
                <input type="hidden" id="asset_id" name="asset_id">
                <input type="hidden" id="sr_no" name="sr_no">
                  <label class="form-label">Item Reciever Comments</label>
                  <textarea name="reciever_remarks" class="form-control" required></textarea>
              </div>
             
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" onclick="btn()" class="btn btn-success">Acknowledge</button>
          </div>
        </div>
    </form>
  </div>
</div>

@endsection
@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

$(document).ready(function () {
    // alert('123');
    let reviewedTableInitialized = false;

    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        // When "Received Items" tab is shown
        if ($(e.target).attr('id') === 'reviewed-tab' && !reviewedTableInitialized) {
            $('#reviewedTable').DataTable({
                pageLength: 10,
                ordering: false,
                responsive: true,
                language: {
                    search: "Filter records:",
                    emptyTable: "No Received Items Found"
                },
                dom:
                    "<'d-flex justify-content-between align-items-center mb-3'<'d-flex align-items-center gap-3'B l>f>" +
                    "rt" +
                    "<'d-flex justify-content-between align-items-center mt-3'i p>",
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        className: 'btn btn-sm btn-success'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        className: 'btn btn-sm btn-danger',
                        title: 'Received Items',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: { columns: ':not(:last-child)' }
                    }
                ]
            });

            reviewedTableInitialized = true;
        }
    });
});

// document.addEventListener('DOMContentLoaded', function() {
//     var remarksModal = document.getElementById('remarksModal');
//     remarksModal.addEventListener('show.bs.modal', function (event) {
//         var button = event.relatedTarget;
//         var id = button.getAttribute('data-id');
//         var form = document.getElementById('remarksForm');
//         form.action = '/quotation-requests/remarks/' + id;
//     });
// });

function openModal(assetId,sr_no) {
    // Example: Set asset ID inside the modal (if you have a hidden input or text)
    $('#remarksModal').find('input[name="asset_id"]').val(assetId);
     $('#remarksModal').find('input[name="sr_no"]').val(sr_no);

    // Show the Bootstrap modal using jQuery
    $('#remarksModal').modal('show');

    // Optional: you can also load content via AJAX if needed
    // $.get(`/assets/${assetId}/details`, function(response){
    //     $('#remarksModal .modal-body').html(response);
    // });
}




function btn() {
    let form = $('#remarksForm');
    let url = 'resource-acknow'; // Your endpoint
    let formData = form.serialize(); // serialize all inputs, including CSRF

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                $('#remarksModal').modal('hide'); // close modal
                showToast('success', 'Remarks saved successfully!');
                // alert('Remarks saved successfully!');
                form.trigger('reset'); // optionally reset form fields
                // ✅ Refresh Pending tab content (AJAX reload)
                $('#pending').load(location.href + " #pending > *");
                var tab = new bootstrap.Tab(document.querySelector('#reviewed-tab'));
                tab.show();
                $('#reviewed').load(location.href + " #reviewed > *");
            } else {
                showToast('error', 'Something went wrong.');
                // alert(response.message || 'Something went wrong.');
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                // handle validation errors
                let errors = xhr.responseJSON.errors;
                let messages = [];
                $.each(errors, function(key, value) {
                    messages.push(value[0]);
                });
                showToast('error', messages.join("\n"));
                // alert(messages.join("\n"));
            } else {
                showToast('error', 'Server error!');
                // alert('Server error!');
            }
        }
    });
}


</script>

