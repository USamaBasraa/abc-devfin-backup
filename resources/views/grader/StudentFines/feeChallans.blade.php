@extends('layouts.app')

<style>
    .action-section {
        margin-bottom: 3rem;
    }

    .form-control-lg {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: border-color 0.3s ease;
    }

    .form-control-lg:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn-lg {
        border-radius: 50px;
        min-width: 200px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }

    .card-header {
        border-bottom: none;
        padding: 1.5rem;
    }

    .card-body {
        padding: 2rem;
    }

    .alert {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>

@section('content')

<?php
    // ==========================================
    // USER PERMISSIONS CHECK
    // ==========================================
    // Get current user information
    $username = auth()->user()->username;
    $groupId = auth()->user()->group_id;

    // Check if user can insert into middleware database
    // Only users with group_id 644 or username 'asif_nadeem' can do this
    $canInsertMiddleware = ($groupId == 644 || $username == 'asif_nadeem'|| $username == 'saqib.jamil');
?>

<!-- ==========================================
     FIRST SECTION: Students Fee Challan Details
     ========================================== -->
<div class="container my-5">
    <div class="card shadow-lg action-section">
        <div class="card-header text-white" style="background-color: #2f3b4d;">
            <h3 class="text-center mb-0">Students Fee Challan Details</h3>
        </div>
        <div class="card-body">
            <div class="filter-section">
                <div class="row mb-4">
                    <div class="col-md-6 offset-md-3 text-center">
                        <!-- Action Selection Dropdown -->
                        <select id="action-select" class="form-control form-control-lg">
                            <option value="" selected>Select Action</option>
                            <option value="export_with_version">Export to Excel (with version)</option>
                            <option value="export_without_version">Export to Excel (without version)</option>
                            <?php if ($canInsertMiddleware): ?>
                                <option value="insert">Insert into Middleware DB</option>
                            <?php endif; ?>
                        </select>
                        <div class="mt-4 d-none" id='filter_fields'>
                            <!-- Student ID / Enrollment No. -->
                            <div class="form-group row align-items-center">
                                <label for="studentEnrollment" class="col-sm-3 col-form-label text-right">Student ID / Enrollment No.</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="studentEnrollment" id="studentEnrollment" onkeypress="handleEnterPress(event)">
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <!-- From Challan No. -->
                                <label for="challanNo" class="col-sm-3 col-form-label text-right">Challan No.</label>
                                <div class="col-sm-4">
                                    <input type="number" class="form-control" name="challanNo" id="challanNo">
                                </div>
                            </div>
                            <div class="row mb-4 mt-5 align-items-center" id="issue-date-filter">
                                <!-- From Date -->
                                <label class="col-sm-2 col-form-label text-right">From</label>
                                <div class="col-sm-4">
                                    <input type="date" id="from_issue_date" class="form-control">
                                </div>

                                <!-- To Date -->
                                <label class="col-sm-1 col-form-label text-right">To</label>
                                <div class="col-sm-4">
                                    <input type="date" id="to_issue_date" class="form-control">
                                </div>
                            </div>

                        </div>
                    </div>


                </div>
                <div class="text-center mt-4">
                    <!-- Execute Button -->
                    <div class="d-flex justify-content-center">
                        <button id="action-btn" class="btn btn-primary btn-lg d-flex align-items-center gap-2">
                            <i class="fa fa-play-circle mr-2"></i>
                            <i id="button-icon" class="fa fa-spinner fa-spin d-none mr-2"></i>
                            <span id="button-text">Execute</span>
                        </button>
                    </div>
                    <!-- Download Button Container (will be added dynamically) -->
                    <div id="download-btn-container" class="d-flex justify-content-center mt-3"></div>
                </div>
                <!-- Notification Container -->
                <div id="notification-container" class="mt-4"></div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
     SECOND SECTION: Summer Fee Challan Breakup
     ========================================== -->
<div class="container my-5">
    <div class="card shadow-lg action-section">
        <div class="card-header text-white" style="background-color: #2f3b4d;">
            <h3 class="text-center mb-0">Summer Fee Challan Breakup</h3>
        </div>
        <div class="card-body">
            <div class="filter-section">
                <div class="row mb-4">
                    <div class="col-md-6 offset-md-3 text-center">
                        <!-- Session Selection Dropdown (only for Summer section) -->
                        <select id="session-select-summer" class="form-control form-control-lg mb-2">
                            <option value="" selected>Select Session</option>
                            <?php foreach ($sessions as $session): ?>
                                <option value="<?php echo $session->sess_id; ?>"><?php echo $session->sess_name; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Action Selection Dropdown -->
                        <select id="action-select-summer" class="form-control form-control-lg">
                            <option value="" selected>Select Action</option>
                            <option value="export">Export to Excel</option>
                            <?php if ($canInsertMiddleware): ?>
                                <option value="insert">Insert into Middleware DB</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <!-- Execute Button -->
                    <div class="d-flex justify-content-center">
                        <button id="action-btn-summer" class="btn btn-primary btn-lg d-flex align-items-center gap-2">
                            <i class="fa fa-play-circle mr-2"></i>
                            <i id="button-icon-summer" class="fa fa-spinner fa-spin d-none mr-2"></i>
                            <span id="button-text-summer">Execute</span>
                        </button>
                    </div>
                    <!-- Download Button Container (will be added dynamically) -->
                    <div id="download-btn-container-summer" class="d-flex justify-content-center mt-3"></div>
                </div>
                <!-- Notification Container -->
                <div id="notification-container-summer" class="mt-4"></div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
     JAVASCRIPT CODE
     ========================================== -->
<script src="{{ asset('weekStudentsReport/jquery-3.5.1.min.js') }}"></script>
<script>
$(document).ready(function () {
    $('#action-select').on('change', function () {

    const selectedAction = $(this).val();
    const $filterFields = $('#filter_fields');

    const isFilter =
        selectedAction === 'export_with_version' ||
        selectedAction === 'export_without_version';

    if (isFilter) {
        $filterFields.removeClass('d-none');
    } else {
        $filterFields.addClass('d-none');

        // Clear all filter fields when hidden
        $('#studentEnrollment').val('');
        $('#challanNo').val('');
        $('#from_issue_date').val('');
        $('#to_issue_date').val('');
    }
});
    // ==========================================
    // GLOBAL VARIABLES - Store important data
    // ==========================================

    // Track if any request is currently running
    let isAnyRequestRunning = false;

    // Store all button IDs in arrays for easy management
    const allExecuteButtons = ['#action-btn', '#action-btn-summer'];
    const allSpinnerIcons = ['#button-icon', '#button-icon-summer'];
    const allButtonTexts = ['#button-text', '#button-text-summer'];

    // ==========================================
    // HELPER FUNCTIONS - Reusable code blocks
    // ==========================================

    /**
     * Disable all execute buttons and show loading state
     * @param {string} clickedButtonId - ID of the button that was clicked
     */
    function disableAllButtonsAndShowLoading(clickedButtonId) {
        // Disable all buttons so user can't click multiple times
        allExecuteButtons.forEach(buttonId => {
            $(buttonId).prop('disabled', true);
        });

        // Only show spinner and processing text on the clicked button
        if (clickedButtonId === '#action-btn') {
            // First section button
            $('#button-icon').removeClass('d-none');
            $('#button-text').text('Processing... Please do not close this window, as it may take some time.');
        } else if (clickedButtonId === '#action-btn-summer') {
            // Second section button
            $('#button-icon-summer').removeClass('d-none');
            $('#button-text-summer').text('Processing... Please do not close this window, as it may take some time.');
        }

        // Mark that a request is running
        isAnyRequestRunning = true;
    }

    /**
     * Enable all execute buttons and hide loading state
     */
    function enableAllButtonsAndHideLoading() {
        // Enable all buttons
        allExecuteButtons.forEach(buttonId => {
            $(buttonId).prop('disabled', false);
        });

        // Hide all spinner icons
        allSpinnerIcons.forEach(iconId => {
            $(iconId).addClass('d-none');
        });

        // Reset all button texts to "Execute"
        allButtonTexts.forEach(textId => {
            $(textId).text("Execute");
        });

        // Mark that no request is running
        isAnyRequestRunning = false;
    }

    /**
     * Create and show a download button
     * @param {string} fileUrl - URL of the file to download
     * @param {string} containerId - ID of the container to add the button to
     */
    function createDownloadButton(fileUrl, containerId) {
        // Clean the file URL (remove extra slashes)
        const cleanFileUrl = fileUrl.replace(/\\\//g, '/');

        // Create the download button HTML
        const downloadButtonHtml = `
            <a href="${cleanFileUrl}" class="btn btn-outline-success btn-lg d-flex align-items-center gap-2" download>
                <i class="fa fa-download mr-2"></i>
                <span>Download Now</span>
            </a>
        `;

        // Add the button to the container
        $(containerId).html(downloadButtonHtml);
    }

    // ==========================================
    // FIRST BUTTON CLICK FUNCTION - Students Fee Challan
    // ==========================================

    function handleFirstButtonClick() {
        // Get selected action
        const selectedAction = $('#action-select').val();

        // Check if action is selected
        if (!selectedAction) {
            showNotification("Please select a valid action.", "danger", 'notification-container');
            return;
        }

        // Check if already processing
        if (isAnyRequestRunning) {
            return;
        }

        // Clear download button
        $('#download-btn-container').html('');

        // Disable both buttons and show loading on first button
        disableAllButtonsAndShowLoading('#action-btn');

        // Prepare data to send
        const requestData = {
            _token: '{{ csrf_token() }}',
            action: selectedAction
        };

        // Add Issue Date range ONLY for export_without_version
        if (selectedAction === 'export_without_version' || selectedAction === 'export_with_version') {
            requestData.from_issue_date = $('#from_issue_date').val();
            requestData.to_issue_date   = $('#to_issue_date').val();
            requestData.studentEnrollment   = $('#studentEnrollment').val();
            requestData.challanNo   = $('#challanNo').val();
        }

        // Send AJAX request
        $.ajax({
            url: "{{ route('insert-table-data') }}",
            type: "POST",
            data: requestData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                "Authorization": 'Bearer {{ auth()->user()->api_token }}',
                "Accept": "application/json"
            },
            success: function (response) {
                // Enable both buttons
                enableAllButtonsAndHideLoading();

                if (response.success) {
                    // Show success message
                    showNotification(response.success, "success", 'notification-container');

                    // Create download button if file exists
                    if (response.file_url) {
                        const fileUrl = response.file_url.replace(/\\\//g, '/');
                        const downloadButton = `
                            <a href="${fileUrl}" class="btn btn-outline-success btn-lg d-flex align-items-center gap-2" download>
                                <i class="fa fa-download mr-2"></i>
                                <span>Download Now</span>
                            </a>
                        `;
                        $('#download-btn-container').html(downloadButton);
                    }
                }
            },
            error: function (xhr, status, error) {
                // Enable both buttons
                enableAllButtonsAndHideLoading();

                console.error("Error: ", error);
                showNotification("An error occurred while processing your request.", "danger", 'notification-container');
            }
        });
    }

    // ==========================================
    // SECOND BUTTON CLICK FUNCTION - Summer Fee Challan
    // ==========================================

    function handleSecondButtonClick() {
        // Get selected values
        const selectedAction = $('#action-select-summer').val();
        const selectedSession = $('#session-select-summer').val();

        // Check if session is selected
        if (!selectedSession) {
            showNotification("Please select a valid session.", "danger", 'notification-container-summer');
            return;
        }

        // Check if action is selected
        if (!selectedAction) {
            showNotification("Please select a valid action.", "danger", 'notification-container-summer');
            return;
        }

        // Check if already processing
        if (isAnyRequestRunning) {
            return;
        }

        // Clear download button
        $('#download-btn-container-summer').html('');

        // Disable both buttons and show loading on second button
        disableAllButtonsAndShowLoading('#action-btn-summer');

        // Prepare data to send
        const requestData = {
            _token: '{{ csrf_token() }}',
            action: selectedAction,
            session_id: selectedSession
        };

        // Send AJAX request
        $.ajax({
            url: "{{ route('insert-table-data-summer') }}",
            type: "POST",
            data: requestData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
                "Authorization": 'Bearer {{ auth()->user()->api_token }}',
                "Accept": "application/json"
            },
            success: function (response) {
                // Enable both buttons
                enableAllButtonsAndHideLoading();

                if (response.success) {
                    // Show success message
                    showNotification(response.success, "success", 'notification-container-summer');

                    // Create download button if file exists
                    if (response.file_url) {
                        const fileUrl = response.file_url.replace(/\\\//g, '/');
                        const downloadButton = `
                            <a href="${fileUrl}" class="btn btn-outline-success btn-lg d-flex align-items-center gap-2" download>
                                <i class="fa fa-download mr-2"></i>
                                <span>Download Now</span>
                            </a>
                        `;
                        $('#download-btn-container-summer').html(downloadButton);
                    }
                }
            },
            error: function (xhr, status, error) {
                // Enable both buttons
                enableAllButtonsAndHideLoading();

                console.error("Error: ", error);
                showNotification("An error occurred while processing your request.", "danger", 'notification-container-summer');
            }
        });
    }

    // ==========================================
    // ATTACH CLICK EVENTS TO BUTTONS
    // ==========================================

    // First button click
    $('#action-btn').on('click', handleFirstButtonClick);

    // Second button click
    $('#action-btn-summer').on('click', handleSecondButtonClick);
});

// ==========================================
// NOTIFICATION FUNCTION - Show messages to user
// ==========================================

/**
 * Show a notification message to the user
 * @param {string} message - The message to show
 * @param {string} type - Type of message (success, danger, warning, info)
 * @param {string} targetDivId - ID of the container to show message in
 * @param {object} options - Additional options (duration, effects, etc.)
 */
function showNotification(message, type, targetDivId, options = {}) {
    // Default settings for notifications
    const defaultSettings = {
        duration: 5000,        // How long to show the message (5 seconds)
        bounceEffect: true,     // Add bounce animation
        slideInSpeed: 500,      // How fast to slide in
        fadeOutSpeed: 500       // How fast to fade out
    };

    // Combine default settings with any custom options
    const finalSettings = { ...defaultSettings, ...options };

    // Clear any existing notifications in the target container
    $('#' + targetDivId).empty();

    // Choose the right CSS class based on message type
    const alertClass = `alert-${type}`;
    let icon = '';

    // Choose the right icon based on message type
    switch (type) {
        case 'success':
            icon = '<i class="fa fa-check-circle"></i>';
            break;
        case 'danger':
            icon = '<i class="fa fa-times-circle"></i>';
            break;
        case 'warning':
            icon = '<i class="fa fa-exclamation-triangle"></i>';
            break;
        case 'info':
            icon = '<i class="fa fa-info-circle"></i>';
            break;
        default:
            icon = '';
            break;
    }

    // Create the notification HTML
    const notificationHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="display:none;">
            <strong>${icon} ${message}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

    // Add the notification to the page
    const notificationElement = $(notificationHtml);
    $('#' + targetDivId).append(notificationElement);

    // Animate the notification
    notificationElement.slideDown(finalSettings.slideInSpeed, function () {
        // Add bounce effect if enabled
        if (finalSettings.bounceEffect) {
            notificationElement.effect("bounce", { times: 2, distance: 10 }, 500);
        }

        // Automatically hide the notification after the specified time
        setTimeout(function () {
            notificationElement.fadeOut(finalSettings.fadeOutSpeed, function () {
                notificationElement.remove(); // Remove from the page
            });
        }, finalSettings.duration);
    });
}
</script>
@endsection
