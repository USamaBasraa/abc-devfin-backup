@extends('layouts.app')
@section('title')
    BNU CMS
@stop
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- DataTables CSS -->
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"> --}}


</head>
<style>
    /* Custom checkbox style */
    .select-data {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
        outline: none;
    }

    /* Checked state */
    .select-data:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Checkmark icon */
    .select-data:checked::after {
        content: '\2713'; /* Unicode checkmark symbol */
        display: block;
        color: white;
        text-align: center;
        font-size: 16px;
        line-height: 20px;
    }
    .success-flag {
        position: fixed;
        top: 170px;
        right: -300px;
        background-color: #4CAF50;
        color: white;
        padding: 15px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1050;
        transition: right 0.5s;
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
    }
    .success-flag.show {
        right: 0px;
    }
    .dt-button {
    background-color: #4454c3;
    color: #4454c3;
    border: none;
    padding: 5px 10px;
    margin: 5px;
    cursor: pointer;
    font-size: 14px;
    border-radius: 4px;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dt-buttons {
        display: inline-block;
        align-items: center;
    }

    .dataTables_wrapper .dataTables_length label, .dataTables_wrapper .dataTables_filter label{
        margin-top:5px;
    }

    .dataTables_wrapper .dt-buttons {
        margin-right: 15px;
    }
    #clear{
        background-color: #25a4fe;
        border-color: #25a4fe;

    }
    #clear:hover {
    background-color: #1b82cb; /* New color on hover */
    border-color: #1b82cb;     /* Matching border color on hover */
    }
    .edit-btn, .edit-event-btn, .delete-btn{
        width:76px;
    }
    .container{
        background: #ffffff;
        border-radius: 10px;
        padding: 20px 50px !important; /* top-bottom = 20px, left-right = 50px */
        max-width: 80%;
    }
    .form-label {
        color: #333; /* text color */
        padding: 6px 12px;
        font-weight: 700;
        /* border-left: 5px solid #007bff; accent strip on the left */
        border-radius: 4px;
        margin-bottom: 6px;
    }
    .section-heading {
        background-color: #4658c0; /* main strip color */
        color: #fff; /* white text */
        padding: 10px 15px;
        font-weight: 700;
        border-radius: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 15px;
    }
    #sponsor-fees-panel {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 12px 14px;
        background: #f8f9fc;
    }
    .sponsor-fee-row {
        margin-left: 0;
        margin-right: 0;
    }


</style>
@section('content')
{{-- ---------------------- --}}
<div id="success-flag" class="success-flag"></div>
<div class="card" style="background:aliceblue;">
    <div class="container">
        <form id="eventForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
                <div class="row"><h3>Create Event:</h3></div>


               <!-- 1. Event Details -->
        <h5 class="section-heading mt-3">Event Details</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="E_EVENT_NAME" class="form-label">Event Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="E_EVENT_NAME" id="E_EVENT_NAME">
                <span class="text-danger error-text E_EVENT_NAME_error"></span>
            </div>
            <div class="col-md-6">
                <label for="E_EVENT_TYPE" class="form-label">Event Type <span class="text-danger">*</span></label>
                <select class="form-control" name="E_EVENT_TYPE" id="E_EVENT_TYPE">
                    <option value="">-- Select Type --</option>
                    <option value="workshop">Workshop</option>
                    <option value="conference">Conference</option>
                    <option value="tour">Tour</option>
                </select>
                <span class="text-danger error-text E_EVENT_TYPE_error"></span>
            </div>
            <div class="col-6">
                <label for="E_EVENT_DESCRIPTION" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control" name="E_EVENT_DESCRIPTION" id="E_EVENT_DESCRIPTION" rows="3" required></textarea>
                <span class="text-danger error-text E_EVENT_DESCRIPTION_error"></span>
            </div>
           <div class="col-md-6">
                <label for="E_EVENT_BANNER" class="form-label">Event Banner</label>

                <input type="file"
                    class="form-control"
                    name="E_EVENT_BANNER"
                    id="E_EVENT_BANNER"
                    accept="image/*"
                    onchange="previewEventImage(event)">

                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                    <label class="form-check-label" for="remove_image">
                        Remove existing image
                    </label>
                </div>

                <div class="mt-3">
                    <img id="event-preview" style="max-width:200px;border-radius:8px;display:none;">
                </div>
            </div>
        </div>

        <!-- 2. Schedule -->
        <h5 class="section-heading mt-4">Schedule</h5>
        <div class="row g-4">
            <div class="col-md-3">
                <label for="E_REGISTRATION_START" class="form-label">Registration Start Date <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" name="E_REGISTRATION_START" id="E_REGISTRATION_START" required>
                <span class="text-danger error-text E_REGISTRATION_START_error"></span>
            </div>
            <div class="col-md-3">
                <label for="E_REGISTRATION_END" class="form-label">Registration End Date <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" name="E_REGISTRATION_END" id="E_REGISTRATION_END" required>
                <span class="text-danger error-text E_REGISTRATION_END_error"></span>
            </div>
            <div class="col-md-3">
                <label for="E_FROM_DATE" class="form-label">Event Start Date <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" name="E_FROM_DATE" id="E_FROM_DATE" required>
                <span class="text-danger error-text E_FROM_DATE_error"></span>
            </div>
            <div class="col-md-3">
                <label for="E_TO_DATE" class="form-label">Event End Date <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" name="E_TO_DATE" id="E_TO_DATE" required>
                <span class="text-danger error-text E_TO_DATE_error"></span>
            </div>
        </div>

        <!-- 3. Location -->
        <h5 class="section-heading mt-4">Location</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="E_EVENT_VENUE" class="form-label">Venue <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="E_EVENT_VENUE" id="E_EVENT_VENUE" required>
                <span class="text-danger error-text E_EVENT_VENUE_error"></span>
            </div>
            {{-- <div class="col-md-6">
                <label for="E_EVENT_MAP_URL" class="form-label">Google Map URL</label>
                <input type="url" class="form-control" name="E_EVENT_MAP_URL" id="E_EVENT_MAP_URL">
            </div> --}}
        </div>

        <!-- 4. Organization -->
        <h5 class="section-heading mt-4">Organizing Body</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="school_sel">School <span class="text-danger">*</span></label>
                <select name="school" id="school_sel" class="form-control" onchange="getDepartments();">
                    @php
                        $allSchools = [];
                        foreach ($schools as $value) {
                            $allSchools[] = $value['school_id'];
                        }
                    @endphp
                    <option value="">Select School</option>
                    {{-- <option value="[{{implode(',', $allSchools) }}]">All School</option> --}}
                    @foreach ($schools as $row)
                    <option value="{{ $row->school_id }}">{{ $row->school_name }}</option>
                    @endforeach
                </select>
                <span class="text-danger error-text school_error"></span>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="department">Department <span class="text-danger">*</span></label>
                <select name="department" disabled id="department" class="form-control" onchange="getPrograms();">
                </select>
                <span class="text-danger error-text department_error"></span>
            </div>
            {{-- <div class="col-md-4">
                <label for="E_CONTACT_EMAIL" class="form-label">Contact Email</label>
                <input type="email" class="form-control" name="E_CONTACT_EMAIL" id="E_CONTACT_EMAIL">
                <span class="text-danger error-text E_CONTACT_EMAIL_error"></span>

            </div> --}}
            <div class="col-md-4">
                <label for="e_organizer" class="form-label">Organizer</label>
                <input type="text" class="form-control" name="e_organizer" id="e_organizer">
            </div>

           {{--  <div class="col-md-6">
                <label for="E_CONTACT_PHONE" class="form-label">Contact Phone</label>
                <input type="text" class="form-control" name="E_CONTACT_PHONE" id="E_CONTACT_PHONE">
            </div> --}}
        </div>

        <!-- 5. Registration & Status -->
        <h5 class="section-heading mt-4">Registration & Status</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label for="E_REGISTRATION_FEE" class="form-label">General registration fee (PKR) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="E_REGISTRATION_FEE" id="E_REGISTRATION_FEE" min="0" step="0.01" required>
                 <span class="text-danger error-text E_REGISTRATION_FEE_error"></span>
            </div>
            <div class="col-md-4">
                <label for="E_ARCHIVE_DATE" class="form-label">Archive date</label>
                <input type="date" class="form-control" name="E_ARCHIVE_DATE" id="E_ARCHIVE_DATE">
                <span class="text-danger error-text E_ARCHIVE_DATE_error"></span>
                {{-- <small class="text-muted">Leave blank if the event should not auto-hide by date.</small> --}}
            </div>
            <div class="col-md-4">
                <label for="E_SPONSOR_REGISTRATION_ENABLED" class="form-label">Are there any sponsors?</label>
                <select class="form-control" id="E_SPONSOR_REGISTRATION_ENABLED" autocomplete="off">
                    <option value="N">No</option>
                    <option value="Y">Yes</option>
                </select>
                <span class="text-danger error-text E_SPONSOR_REGISTRATION_ENABLED_error"></span>
            </div>
            <div id="sponsor-fees-wrapper" class="col-12 mt-2 d-none">
                <label class="form-label">Sponsors Registration <span class="text-danger">*</span></label>
                <div id="sponsor-fees-panel">
                    <div id="sponsor-fees-container"></div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-1" id="add-sponsor-fee-row">+ Add sponsor</button>
                </div>
                <span class="text-danger error-text E_SPONSOR_DETAILS_error"></span>
            </div>
            <div class="col-md-4 d-none">
                <label for="E_URL" class="form-label">Event Website / Registration Link</label>
                <input type="url" class="form-control" name="E_URL" id="E_URL">
                <span class="text-danger error-text E_URL_error"></span>
            </div>
            <div class="col-md-4">
                <label for="E_STATUS" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-control" name="E_STATUS" id="E_STATUS">
                    <option value="E">Active</option>
                    <option value="D">Inactive</option>
                </select>
                <span class="text-danger error-text E_STATUS_error"></span>
            </div>
        </div>

        <!-- 6. Discounts -->
        <h5 class="section-heading mt-4">Discounts</h5>
        {{-- <p class="text-muted mb-2" style="margin-top:-6px;">
            All discount fields are optional and represent a flat amount in PKR that is subtracted from the
            general registration fee on the public side. If a field is left blank, the general registration
            fee is used.
        </p> --}}
        <div class="row g-3">
            <div class="col-md-4">
                <label for="E_DISCOUNT_GENERAL" class="form-label">General discount (PKR)</label>
                <input type="number" class="form-control" name="E_DISCOUNT_GENERAL" id="E_DISCOUNT_GENERAL" min="0" step="0.01" placeholder="Optional">
                <span class="text-danger error-text E_DISCOUNT_GENERAL_error"></span>
                {{-- <small class="text-muted">Applies to anyone who is not BNU student / alumni / faculty / staff.</small> --}}
            </div>
            <div class="col-md-2">
                <label for="E_DISCOUNT_BNU_STUDENT" class="form-label">BNU student (PKR)</label>
                <input type="number" class="form-control" name="E_DISCOUNT_BNU_STUDENT" id="E_DISCOUNT_BNU_STUDENT" min="0" step="0.01" placeholder="Optional">
                <span class="text-danger error-text E_DISCOUNT_BNU_STUDENT_error"></span>
            </div>
            <div class="col-md-2">
                <label for="E_DISCOUNT_BNU_ALUMNI" class="form-label">BNU alumni (PKR)</label>
                <input type="number" class="form-control" name="E_DISCOUNT_BNU_ALUMNI" id="E_DISCOUNT_BNU_ALUMNI" min="0" step="0.01" placeholder="Optional">
                <span class="text-danger error-text E_DISCOUNT_BNU_ALUMNI_error"></span>
            </div>
            <div class="col-md-2">
                <label for="E_DISCOUNT_BNU_FACULTY" class="form-label">BNU faculty (PKR)</label>
                <input type="number" class="form-control" name="E_DISCOUNT_BNU_FACULTY" id="E_DISCOUNT_BNU_FACULTY" min="0" step="0.01" placeholder="Optional">
                <span class="text-danger error-text E_DISCOUNT_BNU_FACULTY_error"></span>
            </div>
            <div class="col-md-2">
                <label for="E_DISCOUNT_BNU_STAFF" class="form-label">BNU staff (PKR)</label>
                <input type="number" class="form-control" name="E_DISCOUNT_BNU_STAFF" id="E_DISCOUNT_BNU_STAFF" min="0" step="0.01" placeholder="Optional">
                <span class="text-danger error-text E_DISCOUNT_BNU_STAFF_error"></span>
            </div>
        </div>

        <!-- Hidden System Fields -->
        <input type="hidden" id='main-Id' name="mainId" value="">
                <div class="form-group col-sm-12 mt-5" style="display: flex;justify-content: center;">
                    <button type="button" style="width: 150px;" id="saveButton" class="btn btn-primary">Save</button>
                    <button type="button" style="width: 150px;margin-left:50px;" id="filterButton" class="btn btn-primary">Apply Filter</button>
                    <button type="button" style="width: 100px;margin-left:50px;" id="clear" class="btn btn-secondary" onclick="refreshFields()">Clear</button>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div id="processing_message" class="alert mb-2" style="display: none;background-color:#4454c36e;">Processing, please wait...</div>
                        <div id="error_message" class="alert alert-danger mb-2" style="display: none;" role="alert"></div>
                        <div id="success_message" class="alert alert-success mb-2" style="display: none;" role="status"></div>
                    </div>
                </div>
        </form>
    </div>

    <div class="row" style="margin:10px;">
        <div class="col-lg-12 col-md-12">
            <div class="card" style="padding:20px;">
                <div class="card-body pb-2 ml-0 mr-0 pl-0 pr-0">
                    <table id="data-table" class="table display" style="width:100%">
                        <thead>
                          <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Event Type</th>
                            <th scope="col">Description</th>
                            <th scope="col">Venue</th>
                            <th scope="col">Registration Start</th>
                            <th scope="col">Registration End</th>
                            <th scope="col">Event Start</th>
                            <th scope="col">Event End</th>
                            <th scope="col">Registration Fee</th>
                            <th scope="col">Archive date</th>
                            {{-- <th scope="col">Link</th> --}}
                            <th scope="col">Status</th>
                            <th scope="col">QR</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>

                      </table>

                </div>
            </div>
            {{-- <div class="d-flex justify-content-end mt-2">
                <div class="form-group col-sm-3 d-flex justify-content-end mt-2">
                  <button type="button" id="bulk-Del" class="btn btn-danger"><i class="fa fa-trash"></i> Bulk Delete</button>
                </div>
            </div> --}}
        </div>
    </div>
</div>




{{-- --------------------------- --}}






{{-- --------------------------- --}}

<div class="card" style="margin-top:10px;">
</div>
<div style="clear:both;"></div>

{{-- QR Code modal --}}
<div class="modal fade" id="eventQrModal" tabindex="-1" role="dialog" aria-labelledby="eventQrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventQrModalLabel">Event QR Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="eventQrModalEventName" style="font-weight:600;margin-bottom:8px;"></div>
                <img id="eventQrModalImage" alt="Event QR" style="max-width:280px;width:100%;height:auto;border:1px solid #dee2e6;border-radius:6px;padding:8px;background:#fff;" />
                <div class="mt-3">
                    <div class="small text-muted">Scan to open the event page:</div>
                    <a id="eventQrModalUrl" href="#" target="_blank" rel="noopener" style="word-break:break-all;display:inline-block;margin-top:4px;"></a>
                </div>
                <div id="eventQrModalMsg" class="small mt-2" style="min-height:1.4em;"></div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary mx-1" id="eventQrCopyUrl">
                    Copy URL
                </button>
                <button type="button" class="btn btn-primary mx-1" id="eventQrDownload">
                    Download PNG
                </button>
            </div>
        </div>
    </div>
</div>

@stop
@section('javascript')






<script>
const eventImageBase = "/docs/";

function utf8ToB64(str) {
    return btoa(unescape(encodeURIComponent(str)));
}
function b64ToUtf8(str) {
    return decodeURIComponent(escape(atob(str)));
}
/** Turn HTML-escaped JSON (e.g. &quot; &amp;) into plain text before JSON.parse. */
function decodeHtmlEntities(str) {
    if (str == null || typeof str !== 'string') return str;
    var ta = document.createElement('textarea');
    ta.innerHTML = str;
    return ta.value;
}

/** Public events host (configurable via EVENTS_URL); QR codes encode URLs from this host. */
@php
    $eventsBaseUrl = (string) (config('app.events_url') ?? '');
    if (trim($eventsBaseUrl) === '') {
        $eventsBaseUrl = (string) env('EVENTS_URL', 'https://events.bnu.edu.pk');
    }
@endphp
var EVENTS_URL = "{{ rtrim($eventsBaseUrl, '/') }}";

/** Build the public detail URL for an event id. */
function buildEventDetailUrl(eventId) {
    return EVENTS_URL + '/events/detail/' + eventId;
}

/** Escape a string for safe insertion into an HTML attribute (uses double quotes around the attr value). */
function escapeHtmlAttr(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}
function parseSponsorFeesFromServer(val) {
    if (val == null || val === '') return [];
    if (Array.isArray(val)) return val;
    if (typeof val === 'string') {
        var s = val.trim();
        if (s === '' || s.toLowerCase() === 'null' || s === '[]') return [];
        var p = null;
        try {
            p = JSON.parse(s);
        } catch (e1) {
            try {
                p = JSON.parse(decodeHtmlEntities(s).trim());
            } catch (e2) {
                return [];
            }
        }
        return Array.isArray(p) ? p : [];
    }
    return [];
}
/** Resolve sponsor JSON from API row (handles e_sponsor_details / E_SPONSOR_DETAILS casing). */
function getRowSponsorDetailsRaw(row) {
    if (!row || typeof row !== 'object') return null;
    var keys = Object.keys(row);
    for (var i = 0; i < keys.length; i++) {
        var k = keys[i];
        if (String(k).toLowerCase() !== 'e_sponsor_details') continue;
        var v = row[k];
        if (v == null || v === '') continue;
        if (typeof v === 'string' && v.trim() === '') continue;
        if (Array.isArray(v) && v.length === 0) continue;
        return v;
    }
    return null;
}
function collectSponsorFeesJson() {
    var items = [];
    $('#sponsor-fees-container .sponsor-fee-row').each(function() {
        var title = $(this).find('.sponsor-fee-title').val().trim();
        var feeRaw = $(this).find('.sponsor-fee-amount').val();
        if (title === '' && (feeRaw === '' || feeRaw == null)) return;
        var feeNum = (feeRaw === '' || feeRaw == null) ? 0 : parseFloat(feeRaw);
        if (isNaN(feeNum)) feeNum = 0;
        items.push({ title: title, fee: feeNum });
    });
    return JSON.stringify(items);
}
function clearSponsorFeeRows() {
    $('#sponsor-fees-container').empty();
}
function addSponsorFeeRow(title, fee) {
    var feeStr = (fee !== '' && fee != null && !isNaN(parseFloat(fee))) ? String(fee) : '';
    var row = $(
        '<div class="row sponsor-fee-row align-items-end g-2 mb-2">' +
        '<div class="col-md-6">' +
        '<label class="form-label small mb-0">Sponsor Name</label>' +
        '<input type="text" class="form-control sponsor-fee-title" placeholder="e.g. KFC" value="">' +
        '</div>' +
        '<div class="col-md-4">' +
        '<label class="form-label small mb-0">Amount (PKR)</label>' +
        '<input type="number" class="form-control sponsor-fee-amount" min="0" step="0.01" value="">' +
        '</div>' +
        '<div class="col-md-2">' +
        '<button type="button" class="btn btn-outline-danger btn-sm remove-sponsor-fee-row">Remove</button>' +
        '</div>' +
        '</div>'
    );
    row.find('.sponsor-fee-title').val(title || '');
    row.find('.sponsor-fee-amount').val(feeStr);
    $('#sponsor-fees-container').append(row);
}
function populateSponsorRowsFromJson(raw) {
    clearSponsorFeeRows();
    var list = parseSponsorFeesFromServer(raw);
    if (!list.length) {
        addSponsorFeeRow('', '');
        return;
    }
    list.forEach(function(item) {
        addSponsorFeeRow(item.title || '', item.fee != null ? item.fee : '');
    });
}
function toggleSponsorFeesPanel() {
    var on = $('#E_SPONSOR_REGISTRATION_ENABLED').val() === 'Y';
    $('#sponsor-fees-wrapper').toggleClass('d-none', !on);
    if (!on) {
        $('.E_SPONSOR_DETAILS_error').text('');
    }
}
function optionalNumericField(val) {
    if (val == null || val === '') return '';
    if (typeof val === 'string' && val.trim().toLowerCase() === 'null') return '';
    return val;
}
function buildEditPayload(row) {
    var sponsorsRaw = getRowSponsorDetailsRaw(row);
    var sponsorsStr = '';
    if (typeof sponsorsRaw === 'string') {
        sponsorsStr = sponsorsRaw;
    } else if (sponsorsRaw != null) {
        try {
            sponsorsStr = JSON.stringify(sponsorsRaw);
        } catch (e) {
            sponsorsStr = '[]';
        }
    } else {
        sponsorsStr = '[]';
    }
    return {
        e_id: row.e_id,
        e_event_name: row.e_event_name || '',
        e_event_type: row.e_event_type || '',
        e_event_description: row.e_event_description || '',
        e_event_venue: row.e_event_venue || '',
        e_from_date: row.e_from_date || '',
        e_to_date: row.e_to_date || '',
        e_school_id: row.e_school_id,
        e_registration_fee: row.e_registration_fee,
        e_discount_general: optionalNumericField(row.e_discount_general),
        e_discount_bnu_student: optionalNumericField(row.e_discount_bnu_student),
        e_discount_bnu_alumni: optionalNumericField(row.e_discount_bnu_alumni),
        e_discount_bnu_faculty: optionalNumericField(row.e_discount_bnu_faculty),
        e_discount_bnu_staff: optionalNumericField(row.e_discount_bnu_staff),
        e_archive_date: row.e_archive_date || '',
        e_sponsor_details: sponsorsStr,
        e_status: row.e_status,
        e_url: row.e_url || '',
        e_registration_start: row.e_registration_start || '',
        e_registration_end: row.e_registration_end || '',
        e_dept_id: row.e_dept_id,
        e_organizer: row.e_organizer || '',
        e_image: row.e_image || ''
    };
}

/** Clears server-side validation text under fields and the top duplicate/general error banner. */
function clearEventFormValidationMessages() {
    $('#eventForm span.error-text').text('');
    $('#error_message').text('').hide().stop(true, true);
}

function applyEventEditPayload(p) {
    clearEventFormValidationMessages();
    // console.log(p.e_event_type);
    $('#main-Id').val(p.e_id);
    $('#E_EVENT_NAME').val(p.e_event_name);
    $('#E_EVENT_TYPE').val(p.e_event_type);
    $('#E_EVENT_DESCRIPTION').val(!p.e_event_description || String(p.e_event_description).toLowerCase() === 'null' ? '' : p.e_event_description);
    $('#E_EVENT_VENUE').val(p.e_event_venue);
    $('#E_FROM_DATE').val(p.e_from_date);
    $('#E_TO_DATE').val(p.e_to_date);
    $('#E_REGISTRATION_START').val(p.e_registration_start);
    $('#E_REGISTRATION_END').val(p.e_registration_end);
    $('#e_organizer').val(!p.e_organizer || String(p.e_organizer).toLowerCase() === 'null' ? '' : p.e_organizer);
    $('#E_REGISTRATION_FEE').val(p.e_registration_fee != null && p.e_registration_fee !== '' ? p.e_registration_fee : '');
    $('#E_DISCOUNT_GENERAL').val(optionalNumericField(p.e_discount_general));
    $('#E_DISCOUNT_BNU_STUDENT').val(optionalNumericField(p.e_discount_bnu_student));
    $('#E_DISCOUNT_BNU_ALUMNI').val(optionalNumericField(p.e_discount_bnu_alumni));
    $('#E_DISCOUNT_BNU_FACULTY').val(optionalNumericField(p.e_discount_bnu_faculty));
    $('#E_DISCOUNT_BNU_STAFF').val(optionalNumericField(p.e_discount_bnu_staff));
    $('#E_ARCHIVE_DATE').val(p.e_archive_date && String(p.e_archive_date).toLowerCase() !== 'null' ? p.e_archive_date : '');
    var hasSponsors = parseSponsorFeesFromServer(p.e_sponsor_details).length > 0;
    $('#E_SPONSOR_REGISTRATION_ENABLED').val(hasSponsors ? 'Y' : 'N');
    toggleSponsorFeesPanel();
    populateSponsorRowsFromJson(p.e_sponsor_details);
    $('#E_URL').val(p.e_url);
    $('#E_STATUS').val(p.e_status);
    if (p.e_image && String(p.e_image).toLowerCase() !== 'null') {
        $('#event-preview').attr('src', eventImageBase + p.e_image).show();
    } else {
        $('#event-preview').attr('src', '').hide();
    }
    $('#school_sel').val(p.e_school_id);
    getDepartments();
    setTimeout(function() {
        $('#department').val(p.e_dept_id);
    }, 500);

    var formEl = document.getElementById('eventForm');
    if (formEl) {
        formEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    setTimeout(function () {
        var nameInput = document.getElementById('E_EVENT_NAME');
        if (nameInput) {
            nameInput.focus({ preventScroll: true });
        }
    }, 350);
}

/** Same order as the form (top to bottom) so the first invalid field is focused. */
var EVENT_FORM_VALIDATION_FIELD_ORDER = [
    'E_EVENT_NAME',
    'E_EVENT_TYPE',
    'E_EVENT_DESCRIPTION',
    'E_EVENT_BANNER',
    'E_REGISTRATION_START',
    'E_REGISTRATION_END',
    'E_FROM_DATE',
    'E_TO_DATE',
    'E_EVENT_VENUE',
    'school',
    'department',
    'e_organizer',
    'E_REGISTRATION_FEE',
    'E_ARCHIVE_DATE',
    'E_DISCOUNT_GENERAL',
    'E_DISCOUNT_BNU_STUDENT',
    'E_DISCOUNT_BNU_ALUMNI',
    'E_DISCOUNT_BNU_FACULTY',
    'E_DISCOUNT_BNU_STAFF',
    'E_SPONSOR_REGISTRATION_ENABLED',
    'E_SPONSOR_DETAILS',
    'E_URL',
    'E_STATUS'
];

function focusFirstEventFormFieldWithErrors(errors) {
    if (!errors || typeof errors !== 'object') {
        return;
    }
    var targetField = null;
    var i;
    for (i = 0; i < EVENT_FORM_VALIDATION_FIELD_ORDER.length; i++) {
        var f = EVENT_FORM_VALIDATION_FIELD_ORDER[i];
        if (errors[f] && errors[f].length) {
            targetField = f;
            break;
        }
    }
    if (!targetField) {
        var keys = Object.keys(errors);
        if (!keys.length) {
            return;
        }
        targetField = keys[0];
    }

    var $scrollTarget;
    var focusSelector;

    if (targetField === 'school') {
        focusSelector = '#school_sel';
        $scrollTarget = $(focusSelector);
    } else if (targetField === 'department') {
        focusSelector = '#department';
        $scrollTarget = $(focusSelector);
        if ($scrollTarget.length && $scrollTarget.prop('disabled')) {
            focusSelector = '#school_sel';
            $scrollTarget = $('#school_sel');
        }
    } else if (targetField === 'E_SPONSOR_DETAILS') {
        $('#sponsor-fees-wrapper').removeClass('d-none');
        $scrollTarget = $('#sponsor-fees-wrapper');
        if (!$scrollTarget.length) {
            $scrollTarget = $('#E_SPONSOR_REGISTRATION_ENABLED');
        }
        focusSelector = '#E_SPONSOR_REGISTRATION_ENABLED';
    } else {
        focusSelector = '#' + targetField;
        $scrollTarget = $(focusSelector);
    }

    var scrollEl = $scrollTarget.length ? $scrollTarget[0] : null;
    if (!scrollEl) {
        return;
    }
    scrollEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

    window.setTimeout(function () {
        var $focus = $(focusSelector);
        if (targetField === 'E_SPONSOR_DETAILS') {
            var $first = $('#sponsor-fees-container').find('input, select, textarea').filter(':visible').first();
            if ($first.length) {
                $first.trigger('focus');
                return;
            }
            $focus = $('#E_SPONSOR_REGISTRATION_ENABLED');
        }
        if ($focus.length && $focus.is(':visible') && !$focus.prop('disabled')) {
            $focus.trigger('focus');
        }
    }, 350);
}

document.getElementById('saveButton').addEventListener('click', function() {
    var form = document.getElementById("eventForm");
    var formData = new FormData(form);
    formData.set('mainId', $('#main-Id').val() || '');
    // Checkboxes only appear in FormData when checked; their submitted value must be '1'
    // (do not use .val(0) on this checkbox — it breaks removal after refreshFields).
    if ($('#remove_image').is(':checked')) {
        formData.set('remove_image', '1');
    } else {
        formData.delete('remove_image');
    }
    formData.set('E_SPONSOR_REGISTRATION_ENABLED', $('#E_SPONSOR_REGISTRATION_ENABLED').val() || 'N');
    if ($('#E_SPONSOR_REGISTRATION_ENABLED').val() === 'Y') {
        formData.set('E_SPONSOR_DETAILS', collectSponsorFeesJson());
    } else {
        formData.set('E_SPONSOR_DETAILS', '[]');
    }
    // formData.append('school', $('#school_sel').val());
    // formData.append('department', $('#department').val());
    // formData.append('program', $('#program').val());
    // formData.append('session', $('#session_sel').val());
    // formData.append('roadmap_session', $('#roadmap_session_sel').val());
    // formData.append('student_type', $('#studentTypeOption').val());
    // formData.append('fee_type', $('#feeType').val());
    // formData.append('effective_from', $('#eff-from').val());
    // formData.append('effective_to', $('#eff-to').val());
    // formData.append('tution_amount', $('#tutionAmt').val());
    // formData.append('admission_amount', $('#admissionAmt').val());
    // formData.append('security_amount', $('#securityAmt').val());
    // formData.append('medical_amount', $('#medicalAmt').val());
    // formData.append('activity_charges_amount', $('#activitychargesAmt').val());
    // formData.append('per_credit_fee', $('#credFee').val());
    // formData.append('hostel_amount', $('#hostelAmt').val());
    // console.log(formData);
    // console.log($('#school_sel').val());
    // console.log($('#department').val());
    // console.log($('#program').val());
    // console.log($('#session_sel').val());
    // console.log($('#studentTypeOption').val());
    // console.log($('#feeType').val());
    // console.log($('#eff-from').val());
    // console.log($('#eff-to').val());
    // console.log($('#tutionAmt').val());
    // console.log($('#admissionAmt').val());
    // console.log($('#securityAmt').val());
    // console.log($('#medicalAmt').val());
    // console.log($('#credFee').val());
    clearEventFormValidationMessages();
    $("#processing_message").show();
    fetch('add-event', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            refreshFields();
            $("#processing_message").hide();
            $('#success_message').html(data.msg || '');
            $('#success_message').stop(true, true).fadeIn('slow');
            $('#success_message').fadeOut(5000);
            var successEl = document.getElementById('success_message');
            if (successEl) {
                successEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
            LoadEvents();
        } else {
            clearEventFormValidationMessages();

            if ('error' in data) {
                var errors = data.error;
                $.each(errors, function (field, messages) {
                    // field is like "E_EVENT_NAME"
                    // messages is an array of error messages
                    $('span.' + field + '_error').text(messages[0]);
                });
                focusFirstEventFormFieldWithErrors(errors);
            }

            $("#processing_message").hide();
            $('#success_message').text('').hide().stop(true, true);

            if (data.msg) {
                $('#error_message').text(data.msg).stop(true, true).show();
                var errEl = document.getElementById('error_message');
                if (errEl) {
                    errEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            } else {
                $('#error_message').text('').hide();
            }
        }
    })
    .catch(error => {
        $("#processing_message").hide();
        // console.error('There was an error!', error);
    });
});

    var lastURL = '';
$(document).ready(function(){
    populateSponsorRowsFromJson(null);
    toggleSponsorFeesPanel();
    $('#E_SPONSOR_REGISTRATION_ENABLED').on('change', toggleSponsorFeesPanel);
    LoadEvents();
    $(document).on('click', '#add-sponsor-fee-row', function() {
        addSponsorFeeRow('', '');
    });
    $(document).on('click', '.remove-sponsor-fee-row', function() {
        $(this).closest('.sponsor-fee-row').remove();
        if ($('#sponsor-fees-container .sponsor-fee-row').length === 0) {
            addSponsorFeeRow('', '');
        }
    });
    $(document).on('click', '.edit-event-btn', function() {
        var b64 = $(this).attr('data-edit-payload');
        if (!b64) return;
        try {
            var payload = JSON.parse(b64ToUtf8(b64));
            // console.log(payload);
            applyEventEditPayload(payload);
        } catch (err) {
            alert('Could not load this row for editing.');
        }
    });
    $(document).on('click', '.event-qr-thumb', function () {
        var $img = $(this);
        var eventId = $img.data('event-id');
        var url = $img.data('qr-url') || buildEventDetailUrl(eventId);
        var name = $img.data('event-name') || '';
        var src = '/api/events/' + eventId + '/qr.png?t=' + Date.now();
        $('#eventQrModalImage').attr('src', src).data('event-id', eventId);
        $('#eventQrModalEventName').text(name);
        $('#eventQrModalUrl').text('Go To Register page').attr('href', url);
        $('#eventQrModalMsg').text('');
        $('#eventQrModal').modal('show');
    });
    function legacyCopyToClipboard(text) {
        // Append inside the modal so Bootstrap doesn't steal focus and break execCommand.
        var host = document.getElementById('eventQrModal') || document.body;
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.top = '0';
        ta.style.left = '0';
        ta.style.width = '1px';
        ta.style.height = '1px';
        ta.style.padding = '0';
        ta.style.border = 'none';
        ta.style.outline = 'none';
        ta.style.boxShadow = 'none';
        ta.style.background = 'transparent';
        ta.style.opacity = '0';
        host.appendChild(ta);
        var ok = false;
        try {
            ta.focus();
            ta.select();
            ta.setSelectionRange(0, ta.value.length);
            ok = !!document.execCommand('copy');
        } catch (e) { ok = false; }
        host.removeChild(ta);
        return ok;
    }
    function copyTextToClipboard(text) {
        return new Promise(function (resolve) {
            // navigator.clipboard.writeText only works in secure contexts (HTTPS/localhost).
            if (window.isSecureContext && navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text)
                    .then(function () { resolve(true); })
                    .catch(function () { resolve(legacyCopyToClipboard(text)); });
                return;
            }
            resolve(legacyCopyToClipboard(text));
        });
    }
    $(document).on('click', '#eventQrCopyUrl', function () {
        var url = $('#eventQrModalUrl').attr('href') || $('#eventQrModalUrl').text();
        var $msg = $('#eventQrModalMsg');
        if (!url) {
            $msg.text('No URL available.').css('color', '#dc3545');
            return;
        }
        copyTextToClipboard(url).then(function (ok) {
            if (ok) {
                $msg.text('URL copied to clipboard.').css('color', '#198754');
            } else {
                $msg.text('Auto-copy blocked. Press Ctrl+C to copy.').css('color', '#dc3545');
                window.prompt('Press Ctrl+C, then Enter to copy:', url);
            }
        });
    });
    $(document).on('click', '#eventQrDownload', function () {
        var src = $('#eventQrModalImage').attr('src');
        var eventId = $('#eventQrModalImage').data('event-id') || 'event';
        if (!src) return;
        var a = document.createElement('a');
        a.href = src;
        a.download = 'event-' + eventId + '-qr.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
    $('#feeType').change(function() {
        if($(this).val() == 'Hostel_fee'){
            $('.credFeeDiv').addClass('d-none');
            $('#credFee').val('');
        }else if($(this).val() == 'Credit_hours' || $(this).val() == 'Short/Summer_Course'){
            $('.credFeeDiv').removeClass('d-none');
        }else{
            $('.credFeeDiv').addClass('d-none');
            $('#credFee').val('');

        }
    });
    // $('#feeType').change(function() {
    //     if($(this).val() == 'Hostel_fee'){
    //         $('.otherThanHostelDivs').addClass('d-none');
    //         $('.otherThanHostelInputs').val('');
    //         $('.credFeeDiv').addClass('d-none');
    //         $('#credFee').val('');
    //         $('.hostelFeeDiv').removeClass('d-none');
    //     }else if($(this).val() == 'Credit_hours'){
    //         $('.credFeeDiv').removeClass('d-none');
    //         $('.otherThanHostelDivs').removeClass('d-none');
    //         $('.hostelFeeDiv').addClass('d-none');
    //         $('.hostelAmt').val('');
    //     }else{
    //         $('.credFeeDiv').addClass('d-none');
    //         $('#credFee').val('');
    //         $('.hostelFeeDiv').addClass('d-none');
    //         $('.hostelAmt').val('');
    //         $('.otherThanHostelDivs').removeClass('d-none');

    //     }
    // });
    $('#studentTypeOption').change(function() {
        if($(this).val() != 'Existing'){
            $('.admissionFeeDiv').removeClass('d-none');
            $('.securityAmtDiv').removeClass('d-none');
        }else{
            $('.admissionFeeDiv').addClass('d-none');
            $('#admissionAmt').val('');
            $('.securityAmtDiv').addClass('d-none');
            $('#securityAmt').val('');

        }
    });
    $(document).on('change', '#select-all', function() {
        if ($(this).prop("checked") == true) {
            $('.select-data').prop('checked', true);
            // console.log($('.select-data').val());
            // $('#bulk-Del').prop('disabled', false);

        } else {
            $('.select-data').prop('checked', false);
            // console.log($('.select-data').val());
            // $('#bulk-Del').prop('disabled', true);
        }
    });
    $(document).on('click', '#bulk-Del', function() {
        var fee_ids = new Array();
        $.each($("input[name='fee_ids[]']:checked"), function () {
            fee_ids.push($(this).val());
        });
        if (fee_ids.length == 0) {
            alert("Please select atleast one row to delete.");
        } else {
            // console.log(fee_ids);
            deleteRows(fee_ids);
        }
    });
    $(document).on('click', '#filterButton', function() {
        LoadEvents();
    });
});


</script>

{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>



<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script> -->


<script>

function LoadEvents(){
        let filterParams = getFilterParams();
        // console.log(filterParams);
        $('#data-table').DataTable({
            scrollX: true, // Enable horizontal scrolling
            responsive: true,
            fixedHeader: false,
            scrollCollapse: false,
            processing: true,
            serverSide: false, //to download all data once
            select: false,
            searching: true,
            destroy: true,
            lengthChange: true,
            paging: true,
            pageLength: 10,
            order: [1, 'desc'],
            ajax:{
                dataType: 'json',
                method: 'post',
                url:  "/api/get_event_list",
                data: function(d) {
                    return $.extend({}, d, filterParams);
                }
            },
            columns: [
                {data: "e_event_name", name: "e_event_name"},
                {data: "e_event_type", name: "e_event_type"},
                {
                    data: "e_event_description",
                    name: "e_event_description",
                    render: function (data, type, row) {
                        return data ? data : "NA"; // Show "NA" if null or empty
                    }
                },
                {
                    data: "e_event_venue",
                    name: "e_event_venue",
                    render: function (data, type, row) {
                        return data ? data : "NA"; // Show "NA" if null or empty
                    }
                },
                {data: "e_registration_start", name: "e_registration_start"},
                {data: "e_registration_end", name: "e_registration_end"},
                {data: "e_from_date", name: "e_from_date"},
                {data: "e_to_date", name: "e_to_date"},
                {data: "e_registration_fee", name: "e_registration_fee"},
                {
                    data: "e_archive_date",
                    name: "e_archive_date",
                    render: function (data, type, row) {
                        return data ? data : '—';
                    }
                },
                // {data: "e_url", name: "e_url"},
                {
                    data: "e_status",
                    name: "e_status",
                    render: function (data, type, row) {
                        return data === "D" ? "Inactive" : "Active";
                    }
                },
                {
                    data: "e_qr_code_present",
                    name: "e_qr_code_present",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        var url = buildEventDetailUrl(row.e_id);
                        var src = '/api/events/' + row.e_id + '/qr.png';
                        return '<img src="' + src + '" '
                            + 'class="event-qr-thumb" '
                            + 'data-event-id="' + row.e_id + '" '
                            + 'data-event-name="' + escapeHtmlAttr(row.e_event_name || '') + '" '
                            + 'data-qr-url="' + url + '" '
                            + 'style="width:48px;height:48px;cursor:pointer;border:1px solid #dee2e6;border-radius:4px;padding:2px;background:#fff;" '
                            + 'title="Click to view / copy / download" />';
                    }
                }


            ],
            columnDefs: [
                {
                    render: function (data, type, row) {
                        var payload = buildEditPayload(row);
                        var b64 = utf8ToB64(JSON.stringify(payload));
                        return '<div><button type="button" class="btn btn-primary btn-sm edit-event-btn" style="margin-top:16px;" data-edit-payload="' + b64 + '"><i class="fa fa-edit"></i> Edit</button>' +
                            '<span style="margin-left: 5px; margin-right: 5px;">&nbsp;</span>' +
                            '<button class="btn btn-danger btn-sm delete-btn" style="margin-top:5px;" onclick="deleteRows(' + row["e_id"] + ')"><i class="fa fa-trash"></i> Delete</button></div>';
                    },
                    targets: 12,
                },
                // {
                //     render: function (data, type, row) {
                //             var   selectbtn = '<div style="margin-top:40px;"><input name="fee_ids[]" class="select-data" idData='+row["ffs_id"]+' value='+row["ffs_id"]+' type="checkbox" ></div>';

                //         return selectbtn;

                //     },
                //     // className: 'dt-body-center',
                //     // className: 'dt-head-center',
                //     targets: 14,
                //     searchable: false,
                //     orderable: false
                // },

                {
                    "width": "100px", "targets": 1
                },
                // {
                //     "width": "5px", "targets": 14
                // },
            ],
            autoWidth: false, // Ensures DataTable does not set its own width
            dom: 'Blfrtip', // Adds the buttons to the DataTable
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Events List',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         exclude columns 12 and 13 (0-based index)
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Events List',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }

                },
                // {
                //     text: 'Export All Data (PDF)', // Custom button text
                //     action: function(e, dt, button, config) {
                //         // Call your custom function
                //         pdfExportFunction(dt, config);
                //     }
                // },


                {
                    extend: 'print',
                    title: 'Events List',
                    // exportOptions: {
                    //     columns: function (idx, data, node) {
                    //         // Exclude columns by index
                    //         return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10].indexOf(idx) !== -1;
                    //     }
                    // }
                }
            ]
        });
}
function getRequest(endPoint,successBlock,failBlock){
    $.ajax({
        url: "{{url('/')}}"+endPoint,
        type: "GET",
        dataType: "json",
        success:successBlock
    }).fail(failBlock);
}
function getPrograms() {
    const session_id = $('#session_sel').val(); // Assuming session_id is still used (remove if not)
    const dept_id = $('#department').val();
    // console.log(session_id, dept_id);
    const url = `/api/getProgramFeeStructureForm/get?dept_id=${dept_id}&session_sel=${session_id}`; // Template literals for URL
    getRequest(url,function(res){
                if(res.status){
                    // console.log(res.data);
                    let allProg = [];
                    let degreeLevels = [];
                    $.each(res.data, function(key, value) {
                        allProg.push(value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id);
                        degreeLevels.push(value.prog_degree_level);
                    });
                    populateDegreeLevel(degreeLevels);
                    if(dept_id == ''){
                        let html = `<option value=''>Select Program</option>`;
                        $.each(res.data, function(key, value) {
                        // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                        html += `<option deg-level="${value.prog_degree_level}" value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                        });
                        $('select[name="program"]').html(html); // Update "program" select
                    }else{
                        let html = `<option value=''>Select Program</option>`;
                        html += `<option value='${JSON.stringify(allProg)}'>All Program</option>`;
                        $.each(res.data, function(key, value) {
                        // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                        html += `<option deg-level="${value.prog_degree_level}" value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name}  &#x2192; ${value.prog_degree_level}</option>`;
                        });
                        $('select[name="program"]').html(html); // Update "course" select
                    }
                    ////////
                    action_onAllDepartmentSelection(dept_id);
                    ///////

                } else {
                    // console.error('Error fetching programs:', res.message); // Log error message
                    // Optionally display an error message to the user
                }

            },function(res){
                alert('Some Error found!');
    });
    // console.log(url);
    //   $.ajax({
    //     url: url,
    //     method: 'GET',
    //     success: function(response) {
    //       if (response.status) {
    //         let html = '<option value="">All Program</option>';
    //         $.each(response.data, function(key, value) {
    //           html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
    //         });
    //         $('select[name="course"]').html(html); // Update "course" select
    //       } else {
    //         console.error('Error fetching programs:', response.message); // Log error message
    //         // Optionally display an error message to the user
    //       }
    //     },
    //     error: function(response) {
    //       console.error('Error fetching programs:', response.statusText); // Log error details
    //       // Optionally display a generic error message to the user
    //     }
    //   });
}
function getDepartments(){
    var school_id = $('#school_sel').val();
    getRequest('/api/getDepartmentsFeeStructureForm/get?school_id='+school_id,function(res){
        // console.log(res);
        if(res.status){
            let allDep = [];
            $.each(res.data, function(key, value) {
                allDep.push(value.dept_school_id+'~~'+value.dept_id);
            });
            if(school_id == ''){
                    let html = `<option value=''>Select Department</option>`;
                    $.each(res.data,function( key, value ) {
                        html += '<option value="'+value.dept_id+'" >'+value.dept_name+'</option>';
                    });
                    $('select[name="department"]').html(html);
            }else{
                let html = `<option value=''>Select Department</option>`;
                // html += `<option value='${JSON.stringify(allDep)}'>All Departments</option>`;
                $.each(res.data,function( key, value ) {
                    html += '<option value="'+value.dept_id+'" >'+value.dept_name+'</option>';
                });
                $('select[name="department"]').html(html);
            }
            /////////
            let htmlProg = `<option value=''>Select Program</option>`;
                $('select[name="program"]').html(htmlProg);
                htmlProg = `<option value=''>Select Degree Level</option>`;
                $('select[name="degree_level"]').html(htmlProg);
            action_onAllSchoolSelection(school_id);
            ////
        }
    },function(res){
        alert('Some Error found!');
    });
}
function on_session_change(session){
    // console.log(session.value);
}
function exportToPDF(tableId){
  try {
    const doc = new jsPDF();
    doc.autoTable({ html: `#${tableId}` });
    doc.save('table.pdf');
  } catch (error) {
    // console.error('Error exporting to PDF:', error);
    alert('An error occurred while exporting to PDF. Please try again.');
  }
}
function deleteRows(mainId){
    var confirmation = window.confirm("Are you sure you want to delete?");
    if (confirmation) {
        $.ajax({
            type: "POST",
            url: "/api/delete_event",
            data: {
                mainId: mainId
            },
            success: function(response) {
                refreshFields();
                $('.select-data').prop('checked', false);
                $('#success-flag').text('Deleted Successfully');
                $('#success-flag').addClass('show');
                setTimeout(function() {
                    $('#success-flag').removeClass('show');
                }, 3000); // Hide after 3 seconds
                LoadEvents();
            },
            error: function(error) {
                // Handle the error
                // console.error("Error:", error);
            }
        });
    } else {

    }

}
function refreshFields() {
    $('#main-Id').val('');
    $('#E_EVENT_NAME').val('');
    $('#E_EVENT_TYPE').val('');
    $('#E_EVENT_DESCRIPTION').val('');
    $('#E_EVENT_VENUE').val('');
    $('#E_FROM_DATE').val('');
    $('#E_TO_DATE').val('');
    $('#E_REGISTRATION_START').val('');
    $('#E_REGISTRATION_END').val('');
    $('#e_organizer').val('');
    $('#E_REGISTRATION_FEE').val('');
    $('#E_DISCOUNT_GENERAL').val('');
    $('#E_DISCOUNT_BNU_STUDENT').val('');
    $('#E_DISCOUNT_BNU_ALUMNI').val('');
    $('#E_DISCOUNT_BNU_FACULTY').val('');
    $('#E_DISCOUNT_BNU_STAFF').val('');
    $('#E_ARCHIVE_DATE').val('');
    $('#E_SPONSOR_REGISTRATION_ENABLED').val('N');
    populateSponsorRowsFromJson(null);
    toggleSponsorFeesPanel();
    $('#E_URL').val('');
    $('#E_STATUS').val('');
    $('#school_sel').val('');
    $('#department').val('');
     // Clear file input (removes chosen file name)
    $('#E_EVENT_BANNER').val('');
    $('#remove_image').prop('checked', false);

    // Remove preview
    $('#event-preview').attr('src', '').hide();

    clearEventFormValidationMessages();
}

function action_onAllSchoolSelection(school_id){
    if(school_id != ''){
        school_ids_array = JSON.parse(school_id);
        if(Array.isArray(school_ids_array)){
            $('#department').prop('disabled', true);
            $('#program').prop('disabled', true);
            setTimeout(function() {
                $('#department').val($('#department option:nth-child(2)').val());
                getPrograms();
                setTimeout(function() {
                    $('#program').val($('#program option:nth-child(2)').val());
                }, 500);  // Adjust the delay as needed
            }, 500);  // Adjust the delay as needed
        }else{
            $('#department').prop('disabled', false);
            $('#program').prop('disabled', false);
        }
    }else{
        $('#department').prop('disabled', false);
        $('#program').prop('disabled', false);
    }

}
function action_onAllDepartmentSelection(dept_id){
    if(dept_id != ''){
        dept_ids_array = JSON.parse(dept_id);
        if(Array.isArray(dept_ids_array)){
            $('#program').prop('disabled', true);
            $('#program').val($('#program option:nth-child(2)').val());
        }else{
            $('#program').prop('disabled', false);
        }
    }else{
        $('#program').prop('disabled', false);
    }
    // console.log($('#program').val());

}
function populateDegreeLevel(degreeLevels){
    // console.log(degreeLevels);
    let filterDegreeLevels = degreeLevels.filter(item => item !== "Short_Course");
    let distinctDegreeLevels = [...new Set(filterDegreeLevels)];
    // console.log(distinctDegreeLevels);
    html='<option value="" >Select Degree Level</option>';
    $.each(distinctDegreeLevels,function( key, value ) {
                html += '<option value="'+value+'" >'+value+'</option>';
            });
    $('select[name="degree_level"]').html(html);
}
function setProgramsAccordingly(){
    let session_id = $('#session_sel').val(); // Assuming session_id is still used (remove if not)
    let dept_id = $('#department').val();
    let degreeLevel = $('#degree_level').val();
    let degreeLevelwithArrow = ((degreeLevel != '')? '&#x2192;'+degreeLevel: '');
    // console.log(session_id, dept_id);
    const url = `/api/getProgramFeeStructureForm/get?dept_id=${dept_id}&degreeLevel=${degreeLevel}`; // Template literals for URL
    getRequest(url,function(res){
                if(res.status){
                    // console.log(res.data);
                    let allProg = [];
                    $.each(res.data, function(key, value) {
                        allProg.push(value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id);
                    });
                    // let html = `<option value=''>Select Program</option>`;
                    html = `<option value='${JSON.stringify(allProg)}'>All Program `+degreeLevelwithArrow+`</option>`;
                    $.each(res.data, function(key, value) {
                    // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                    html += `<option deg-level="${value.prog_degree_level}"  value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                    });
                    $('select[name="program"]').html(html); // Update "course" select

                    $('#program option').filter(function() {
                        return $(this).text() === 'All Program';
                    }).prop('selected', true);
                    ////////
                    // action_onAllDepartmentSelection(dept_id);
                    ///////
                    // console.log($('#program').val());

                } else {
                    // console.error('Error fetching programs:', res.message); // Log error message
                    // Optionally display an error message to the user
                }

            },function(res){
                alert('Some Error found!');
    });
}
function getFilterParams() {
    const school_sel = $('#school_sel').val();
    const department = $('#department').val();
    const url = $('#E_URL').val();
    const status = $('#E_STATUS').val();
    const reg_fee = $('#E_REGISTRATION_FEE').val();
    const event_type = $('#E_EVENT_TYPE').val();
    let filterParams = {
        school_sel: school_sel,
        department: department,
        url: url,
        status: status,
        reg_fee: reg_fee,
        event_type: event_type
    };
    return filterParams;
}
function previewEventImage(event) {
    const reader = new FileReader();
    reader.onload = function(e) {
        $('#event-preview')
            .attr('src', e.target.result)
            .show();
    };
    reader.readAsDataURL(event.target.files[0]);
}

</script>

@stop

