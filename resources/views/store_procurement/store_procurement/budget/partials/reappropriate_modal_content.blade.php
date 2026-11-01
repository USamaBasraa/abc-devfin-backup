@php
    $sourceItems = $sourceItems ?? collect();
    $targetBudgetHeads = collect($targetBudgetHeads ?? collect())->unique('bh_id')->values();
    $targetBudgetHeadTitles = $targetBudgetHeads->unique(function ($budgetHead) {
        return strtolower(trim((string) $budgetHead->head_title));
    })->values();
@endphp

<script>
    window.reappropriationSourceItemIds = @json($sourceItems->pluck('item_id')->values());

</script>

<form id="multiReappropriateForm">
    @csrf

    <div class="modal-header reappropriate-modal-header">
        <h5 class="modal-title">
            <i class="fa fa-exchange"></i> Re-appropriate Budget Across Heads
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <div class="modal-body">
        <p class="text-muted mb-3">
            <i class="fa fa-info-circle"></i> Transfer budget amounts from selected items to create a new item in a target budget head.
        </p>

        <h5 class="font-weight-bold mb-3">
            <i class="fa fa-arrow-up text-primary"></i> Source Items (From)
        </h5>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="selectedItemsTable">
                <thead class="thead-light">
                    <tr>
                        <th>Budget Head</th>
                        <th>Item</th>
                        <th class="text-right">Available Amount</th>
                        <th>Amount to Transfer</th>
                        <th class="text-right">Remaining Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sourceItems as $item)
                        @php
                            $availableAmount = ($item->quantity ?? 0) * ($item->rate ?? 0);
                            $availableDisplay = number_format($availableAmount, 2);
                            $availableValue = number_format($availableAmount, 2, '.', '');
                            $budgetHeadName = $item->budgetHead->head_title ?? '-';
                            $departmentName = $item->budgetHead->department->dept_name ?? '-';
                        @endphp
                        <tr>
                            <td class="align-middle">
                                <strong>{{ $budgetHeadName }}</strong>
                                <div class="text-muted small">{{ $departmentName }}</div>
                            </td>
                            <td class="align-middle">
                                <input type="hidden" name="sources[{{ $item->item_id }}][id]" value="{{ $item->item_id }}">
                                {{ $item->item_title }}
                            </td>
                            <td class="align-middle text-right">
                                <strong>{{ $availableDisplay }}</strong>
                            </td>
                            <td class="align-middle">
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    max="{{ $availableValue }}"
                                    class="form-control transfer-amount"
                                    name="sources[{{ $item->item_id }}][amount]"
                                    placeholder="0.00"
                                    data-available="{{ $availableValue }}"
                                    data-item-title="{{ $item->item_title }}"
                                    required
                                >
                            </td>
                            <td class="align-middle text-right font-weight-bold remaining-amount">
                                <strong>{{ $availableDisplay }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fa fa-info-circle"></i> No source items found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <label class="font-weight-bold">Re-appropriation Type <span class="text-danger">*</span></label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="reappropriation_type" id="existingItem" value="existing" checked>
                    <label class="form-check-label" for="existingItem">
                        <i class="fa fa-plus-circle text-info"></i> Re-appropriate to Existing Item
                    </label>
                </div>
                <div class="form-check mt-2" style="display: none;">
                    <input class="form-check-input" type="radio" name="reappropriation_type" id="newItem" value="new">
                    <label class="form-check-label" for="newItem">
                        <i class="fa fa-file-text-o text-success"></i> Create New Item
                    </label>
                </div>
            </div>
        </div>

        <div class="row mt-4" id="existingItemSection">
            <div class="col-md-12">
                <div class="form-group mb-3">
                    <label class="font-weight-bold">Target Budget Head <span class="text-danger">*</span></label>
                    <select name="target_bh_id_selector" id="targetBudgetHeadExisting" class="form-control" required>
                        <option value="">-- Select Target Budget Head --</option>
                        @foreach ($targetBudgetHeadTitles as $budgetHead)
                            <option value="{{ $budgetHead->head_title }}">
                                {{ $budgetHead->head_title }} ({{ $budgetHead->department->dept_name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Choose the budget head where you want to add the amount</small>
                </div>

                <input type="hidden" name="target_bh_id" id="selectedTargetBudgetHead" value="">

                <div class="form-group mb-3">
                    <label class="font-weight-bold">Select Target Item <span class="text-danger">*</span></label>
                    <select name="existing_item_id" id="existingItemDropdown" class="form-control" required>
                        <option value="">-- Select Item --</option>
                    </select>
                    <small class="form-text text-muted">Select the item to which the amount will be added</small>
                </div>

                <div id="targetItemDetailsSection" class="d-none">
                    <hr class="my-3">
                    <h6 class="font-weight-bold text-primary"><i class="fa fa-info-circle"></i> Target Item Details</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Item Title</th>
                                    <th class="text-right">Current Amount</th>
                                    <th class="text-right">Additional Amount</th>
                                    <th class="text-right">Total After Transfer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="targetItemTitle">-</td>
                                    <td id="targetItemCurrentAmount" class="font-weight-bold text-right">0.00</td>
                                    <td id="targetItemAdditionalAmount" class="text-right">0.00</td>
                                    <td id="targetItemNewAmount" class="font-weight-bold text-success text-right">0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p id="targetItemHint" class="text-muted small mb-0">Please select an item from the list above to view details.</p>
                </div>
            </div>
        </div>

        <div class="row mt-4 d-none" id="newItemSection">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fa fa-calculator"></i>
                    Total amount to re-appropriate: <strong><span id="totalTransferDisplay">0.00</span></strong>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">Target Budget Head <span class="text-danger">*</span></label>
                        <select name="target_bh_id" id="targetBudgetHeadNew" class="form-control">
                            <option value="">-- Select Target Budget Head --</option>
                            @foreach ($targetBudgetHeads as $budgetHead)
                                <option value="{{ $budgetHead->bh_id }}">
                                    {{ $budgetHead->head_title }} ({{ $budgetHead->department->dept_name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">New Item Title <span class="text-danger">*</span></label>
                        <input type="text" name="new_item_title" class="form-control" placeholder="Enter new item title">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Brand</label>
                        <input type="text" name="brand" class="form-control" placeholder="Enter brand (optional)">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Model</label>
                        <input type="text" name="model" class="form-control" placeholder="Enter model (optional)">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control" min="1" placeholder="Enter quantity">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Rate <span class="text-danger">*</span></label>
                        <input type="number" name="rate" class="form-control" min="0.01" step="0.01" placeholder="Enter rate">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Total Amount</label>
                        <input type="text" id="newItemAmount" class="form-control" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="1" placeholder="Add remarks (optional)"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-paper-plane"></i> Submit Re-appropriation
        </button>
    </div>
</form>
<script>
(function ($) {
    'use strict';

    function getTotalTransferAmount() {
        var total = 0;
        $('#selectedItemsTable .transfer-amount').each(function () {
            total += parseFloat($(this).val()) || 0;
        });
        return total;
    }

    function updateTotalTransferDisplay() {
        var totalTransferDisplay = $('#totalTransferDisplay');
        if (totalTransferDisplay.length) {
            totalTransferDisplay.text(getTotalTransferAmount().toFixed(2));
        }
    }

    function updateTargetNewAmount() {
        var totalTransfer = getTotalTransferAmount();
        var currentAmount = parseFloat($('#targetItemCurrentAmount').text()) || 0;
        var newAmount = currentAmount + totalTransfer;
        $('#targetItemAdditionalAmount').text(totalTransfer.toFixed(2));
        $('#targetItemNewAmount').text(newAmount.toFixed(2));
    }

    function toggleReappropriationMode(selectedType) {
        if (selectedType === 'existing') {
            $('#existingItemSection').removeClass('d-none');
            $('#newItemSection').addClass('d-none');

            $('#newItemSection [name="target_bh_id"]').prop('disabled', true);
            $('#newItemSection [name="new_item_title"]').prop('disabled', true);
            $('#newItemSection [name="brand"]').prop('disabled', true);
            $('#newItemSection [name="model"]').prop('disabled', true);
            $('#newItemSection [name="quantity"]').prop('disabled', true);
            $('#newItemSection [name="rate"]').prop('disabled', true);
            $('#newItemSection [name="remarks"]').prop('disabled', true);

            $('#existingItemSection [name="target_bh_id_selector"]').prop('disabled', false).attr('required', true);
            $('#existingItemDropdown').prop('disabled', false).attr('required', true);
        } else {
            $('#existingItemSection').addClass('d-none');
            $('#newItemSection').removeClass('d-none');

            $('#existingItemSection [name="target_bh_id_selector"]').prop('disabled', true);
            $('#existingItemDropdown').prop('disabled', true).removeAttr('required').val('').trigger('change');

            $('#newItemSection [name="target_bh_id"]').prop('disabled', false).attr('required', true);
            $('#newItemSection [name="new_item_title"]').prop('disabled', false).attr('required', true);
            $('#newItemSection [name="brand"]').prop('disabled', false);
            $('#newItemSection [name="model"]').prop('disabled', false);
            $('#newItemSection [name="quantity"]').prop('disabled', false).attr('required', true);
            $('#newItemSection [name="rate"]').prop('disabled', false).attr('required', true);
            $('#newItemSection [name="remarks"]').prop('disabled', false);
        }
    }

    $(document)
        .off('change.reappropriation', 'input[name="reappropriation_type"]')
        .on('change.reappropriation', 'input[name="reappropriation_type"]', function () {
            var selectedType = $(this).val();
            toggleReappropriationMode(selectedType);
        });

    $(document)
        .off('change.reappropriation', '#existingItemSection select[name="target_bh_id_selector"]')
        .on('change.reappropriation', '#existingItemSection select[name="target_bh_id_selector"]', function () {
            var budgetHeadTitle = $(this).val();
            var itemDropdown = $('#existingItemDropdown');

            itemDropdown.empty().append('<option value="">-- Select Item --</option>');
            $('#targetItemDetailsSection').addClass('d-none');
            $('#targetItemHint').removeClass('text-success').addClass('text-muted').text('Please select an item from the list above to view details.');
            $('#selectedTargetBudgetHead').val('');

            if (!budgetHeadTitle) {
                return;
            }

            itemDropdown.append('<option disabled value="">Loading items...</option>');

            $.ajax({
                url: '/get-items-by-budget-head-title',
                data: { head_title: budgetHeadTitle },
                type: 'GET',
                success: function (items) {
                    populateExistingItemsDropdown(itemDropdown, items);
                },
                error: function () {
                    itemDropdown.empty().append('<option disabled value="">Failed to load items</option>');
                    showToast('error', 'Failed to load items. Please try again.', 'Error');
                }
            });
        });

    $(document)
        .off('change.reappropriation', '#existingItemDropdown')
        .on('change.reappropriation', '#existingItemDropdown', function () {
            var selectedOption = $(this).find('option:selected');

            if (selectedOption.val() === '') {
                $('#targetItemDetailsSection').addClass('d-none');
                $('#selectedTargetBudgetHead').val('');
                return;
            }

            var itemTitle = selectedOption.data('title');
            var itemQty = selectedOption.data('qty');
            var itemRate = parseFloat(selectedOption.data('rate'));
            var itemAmount = parseFloat(selectedOption.data('amount'));
            var targetBhId = selectedOption.data('bh');

            $('#targetItemTitle').text(itemTitle);
            $('#targetItemCurrentAmount').text(itemAmount.toFixed(2));
            $('#targetItemAdditionalAmount').text('0.00');
            $('#targetItemNewAmount').text(itemAmount.toFixed(2));
            $('#selectedTargetBudgetHead').val(targetBhId || '');

            $('#targetItemHint').removeClass('text-muted').addClass('text-success').text('Ready to re-appropriate to the selected item.');
            $('#targetItemDetailsSection').removeClass('d-none');
            updateTargetNewAmount();
        });

    $(document)
        .off('input.reappropriation', '#selectedItemsTable .transfer-amount')
        .on('input.reappropriation', '#selectedItemsTable .transfer-amount', function () {
            var inputField = $(this);
            var transferAmount = parseFloat(inputField.val()) || 0;
            var availableAmount = parseFloat(inputField.attr('data-available'));

            if (transferAmount > availableAmount) {
                inputField.addClass('border-danger');
                inputField.val(availableAmount.toFixed(2));
                transferAmount = availableAmount;
                showToast('warning', 'Transfer amount cannot exceed available amount. Auto-corrected to maximum.', 'Invalid Amount');
            } else {
                inputField.removeClass('border-danger');
            }

            var remaining = availableAmount - transferAmount;
            var remainingCell = inputField.closest('tr').find('.remaining-amount strong');
            remainingCell.text(remaining.toFixed(2));

            if (remaining < 0) {
                remainingCell.addClass('text-danger');
            } else if (remaining === 0) {
                remainingCell.removeClass('text-danger').addClass('text-success');
            } else {
                remainingCell.removeClass('text-danger text-success');
            }

            updateTotalTransferDisplay();

            var selectedType = $('input[name="reappropriation_type"]:checked').val();
            if (selectedType === 'existing') {
                updateTargetNewAmount();
            } else {
                $('#newItemSection [name="quantity"]').trigger('input');
            }
        });

    $(document)
        .off('input.reappropriation', '#newItemSection [name="quantity"]')
        .on('input.reappropriation', '#newItemSection [name="quantity"]', function () {
            var quantity = parseFloat($(this).val()) || 0;
            var totalTransfer = getTotalTransferAmount();

            updateTotalTransferDisplay();

            if (quantity <= 0 || totalTransfer <= 0) {
                $('#newItemSection [name="rate"]').val('');
                $('#newItemAmount').val('');
                return;
            }

            var rate = totalTransfer / quantity;
            $('#newItemSection [name="rate"]').val(rate.toFixed(2));
            $('#newItemAmount').val((quantity * rate).toFixed(2));
        });

    $(document)
        .off('submit.reappropriation', '#multiReappropriateForm')
        .on('submit.reappropriation', '#multiReappropriateForm', function (e) {
            e.preventDefault();

            var isFormValid = true;
            var validationErrors = [];
            var transferInputFields = $(this).find('.transfer-amount');

            transferInputFields.each(function () {
                var currentInput = $(this);
                var enteredAmount = parseFloat(currentInput.val()) || 0;
                var maximumAllowed = parseFloat(currentInput.attr('data-available'));
                var itemName = currentInput.attr('data-item-title');

                if (enteredAmount <= 0 || !currentInput.val()) {
                    isFormValid = false;
                    validationErrors.push('Please enter transfer amount for: ' + itemName);
                    currentInput.addClass('border-danger');
                } else if (enteredAmount > maximumAllowed) {
                    isFormValid = false;
                    validationErrors.push('Transfer amount exceeds available for: ' + itemName);
                    currentInput.addClass('border-danger');
                } else {
                    currentInput.removeClass('border-danger');
                }
            });

            if (!isFormValid) {
                showToast('error', validationErrors.join('<br>'), 'Validation Error');
                return false;
            }

            if ($('input[name="reappropriation_type"]:checked').val() === 'existing' && !$('#selectedTargetBudgetHead').val()) {
                showToast('error', 'Please select a target item before submitting.', 'Validation Error');
                return false;
            }

            var submitButton = $(this).find('button[type="submit"]');
            var originalButtonText = submitButton.html();
            submitButton.html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            submitButton.prop('disabled', true);

            var formData = $(this).serialize();

            $.ajax({
                url: '{{ url("/budgets/reappropriate/cross") }}',
                type: 'POST',
                data: formData,
                timeout: 30000
            })
                .done(function (response) {
                    var successMessage = (response && response.message) ? response.message : 'Re-appropriation successful!';

                    try {
                        if (typeof forceHideModalNow === 'function') {
                            forceHideModalNow('#multiReappropriateModal');
                        } else {
                            $('#multiReappropriateModal').modal('hide');
                        }
                    } catch (e) {
                        $('#multiReappropriateModal').removeClass('show').hide();
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css('padding-right', '');
                    }

                    showToast('success', successMessage, 'Success');
                    setTimeout(function () {
                        window.location.reload();
                    }, 900);
                })
                .fail(function (xhr, textStatus) {
                    var errorMessage = 'Something went wrong. Please try again.';
                    if (textStatus === 'timeout') {
                        errorMessage = 'Request timed out. Please try again.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    showToast('error', errorMessage, 'Error');
                })
                .always(function () {
                    submitButton.html(originalButtonText);
                    submitButton.prop('disabled', false);
                });
        });

    $('input[name="reappropriation_type"]:checked').trigger('change');
    updateTotalTransferDisplay();

    function populateExistingItemsDropdown(selectElement, items) {
        var sourceItemIds = window.reappropriationSourceItemIds || [];

        selectElement.empty().append('<option value="">-- Select Item --</option>');

        if (!items || !items.length) {
            selectElement.append('<option disabled value="">No items found for this budget head</option>');
            return;
        }

        items.forEach(function (item) {
            var itemId = item.id;
            if (sourceItemIds.indexOf(itemId) !== -1) {
                return;
            }

            var title = item.item_title || item.title || '-';
            var quantity = item.quantity || 0;
            var rate = item.rate || 0;
            var amount = parseFloat(item.amount || 0);
            var bhId = item.bh_id || '';

            var option = $('<option></option>');
            option.val(itemId)
                .text(title + ' (' + amount.toFixed(2) + ')')
                .attr('data-title', title)
                .attr('data-qty', quantity)
                .attr('data-rate', rate)
                .attr('data-amount', amount)
                .attr('data-bh', bhId);

            selectElement.append(option);
        });

        if (selectElement.children('option').length === 1) {
            selectElement.append('<option disabled value="">No items available</option>');
        }
    }
})(jQuery);
</script>
