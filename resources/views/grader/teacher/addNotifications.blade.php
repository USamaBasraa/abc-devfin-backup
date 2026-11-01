@extends('layouts.app', ['pageTitle' => 'Add Notifications'])
{{-- @dd("!23"); --}}
@section('content')
 
<style>
    .add-notification-page {
        /* padding: 18px 0 28px; */
        font-family: 'Montserrat', sans-serif;
    }

    .add-notification-shell {
        border-radius: 12px;
        /* background: linear-gradient(115deg, #ffffff 0%, #edf6f5 100%); */
    }

    .add-notification-heading {
        margin-bottom: 18px;
    }

    .add-notification-heading h3,
    .notification-list-heading {
        margin: 0;
        color: #202938;
        font-size: 17px;
        font-weight: 700;
        line-height: 1.4;
    }

    .add-notification-heading p {
        margin: 2px 0 0;
        color: #4b5565;
        font-size: 14px;
    }

    .notification-form-card {
        margin-bottom: 25px;
        padding: 22px;
        border: 0;
        border-radius: 9px;
        background: #fff;
        box-shadow: none;
    }

    .notification-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px 24px;
    }

    .notification-field--full {
        grid-column: 1 / -1;
    }

    .notification-field label {
        display: block;
        margin-bottom: 7px;
        color: #475467;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .notification-field .form-control {
        width: 100%;
        height: 38px;
        padding: 0 13px;
        border: 1px solid #dbe3ea;
        border-radius: 5px;
        background: #fff;
        color: #344054;
        font-size: 14px;
        box-shadow: none;
    }

    .notification-field textarea.form-control {
        height: 95px;
        padding-top: 12px;
        resize: vertical;
    }

    .notification-field .form-control::placeholder {
        color: #a7b4c4;
    }

    .notification-field .form-control:focus {
        border-color: #17675f;
        box-shadow: 0 0 0 2px rgba(23, 103, 95, .08);
    }

    .notification-form-actions {
        margin-top: 19px;
    }

    .add-notification-btn {
        min-width: 158px;
        min-height: 40px;
        padding: 0 20px;
        border: 0;
        border-radius: 6px;
        background: #17675f;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        transition: background-color .2s ease;
    }

    .add-notification-btn:hover,
    .add-notification-btn:focus {
        background: #14564f;
        color: #fff;
        outline: 0;
    }

    .notification-list-heading {
        margin-bottom: 10px;
    }

    .notification-table-card {
        overflow: hidden;
        border-radius: 8px;
        background: #fff;
    }

    .notification-table-responsive {
        overflow-x: auto;
    }

    .notification-table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        color: #526174;
        font-size: 9px;
    }

    .notification-table thead th {
        height: 42px;
        padding: 0 13px;
        border: 0;
        background: #176f65;
        color: #fff;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .35px;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .notification-table tbody tr:nth-child(even) {
        background: #f1f7f7;
    }

    .notification-table tbody td {
        height: 53px;
        padding: 9px 13px;
        border: 0;
        border-bottom: 1px solid #edf1f4;
        vertical-align: middle;
        white-space: nowrap;
    }

    .notification-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .notification-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 58px;
        height: 22px;
        padding: 0 9px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .notification-status--disabled {
        background: #ffecef;
        color: #fa3e57;
    }

    .notification-status--enabled {
        background: #e2f8ed;
        color: #00a865;
    }

    .notification-switch {
        position: relative;
        display: inline-block;
        width: 37px;
        height: 21px;
        margin: 0;
        vertical-align: middle;
    }

    .notification-switch input {
        width: 0;
        height: 0;
        opacity: 0;
    }

    .notification-switch__slider {
        position: absolute;
        inset: 0;
        cursor: pointer;
        border-radius: 20px;
        background: #dce6f0;
        transition: .2s;
    }

    .notification-switch__slider::before {
        position: absolute;
        bottom: 3px;
        left: 3px;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #fff;
        content: '';
        transition: .2s;
    }

    .notification-switch input:checked + .notification-switch__slider {
        background: #16b981;
    }

    .notification-switch input:checked + .notification-switch__slider::before {
        transform: translateX(16px);
    }

    .notification-actions {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .notification-action {
        color: #506176;
        font-size: 12px;
        text-decoration: none;
    }

    .notification-action:hover {
        color: #17675f;
        text-decoration: none;
    }

    .notification-view-modal .modal-dialog {
        max-width: 900px;
    }

    .notification-view-modal .modal-content {
        overflow: hidden;
        border: 0;
        border-radius: 12px;
        box-shadow: 0 18px 55px rgba(16, 24, 40, .18);
    }

    .notification-view-modal .modal-header {
        align-items: center;
        padding: 18px 22px;
        border-bottom: 1px solid #e8edf1;
        background: #fff;
    }

    .notification-view-modal .modal-title {
        margin: 0;
        color: #202938;
        font-size: 17px;
        font-weight: 700;
    }

    .notification-view-modal .close {
        margin: -8px -4px -8px auto;
        color: #667085;
        font-size: 25px;
        opacity: 1;
    }

    .notification-view-modal .modal-body {
        padding: 24px;
        background: #f7faf9;
    }

    .notification-detail-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .notification-detail-box {
        padding: 16px;
        border: 1px solid #e2e9ed;
        border-radius: 8px;
        background: #fff;
    }

    .notification-detail-box--full {
        grid-column: 1 / -1;
    }

    .notification-detail-label {
        display: block;
        margin-bottom: 7px;
        color: #7a899c;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
    }

    .notification-detail-value {
        margin: 0;
        color: #344054;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.6;
        word-break: break-word;
    }

    .notification-view-modal .modal-footer {
        padding: 14px 22px;
        border-top: 1px solid #e8edf1;
        background: #fff;
    }

    .notification-modal-close {
        min-width: 90px;
        min-height: 36px;
        border: 0;
        border-radius: 6px;
        background: #17675f;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
    }

    .notification-modal-close:hover {
        background: #14564f;
        color: #fff;
    }

    @media (max-width: 767.98px) {
        .add-notification-shell {
            padding: 16px 12px;
        }

        .notification-form-card {
            padding: 16px;
        }

        .notification-form-grid {
            grid-template-columns: 1fr;
        }

        .notification-field--full {
            grid-column: auto;
        }

        .add-notification-btn {
            width: 100%;
        }

        .notification-detail-grid {
            grid-template-columns: 1fr;
        }

        .notification-detail-box--full {
            grid-column: auto;
        }
    }
    .notification-flash {
    position: fixed !important;
    top: 85px;
    right: 25px;
    z-index: 99999 !important;
    display: flex !important;
    align-items: center;
    justify-content: space-between;
    gap: 25px;
    min-width: 330px;
    max-width: 500px;
    padding: 15px 18px;
    border-radius: 8px;
    color: #ffffff;
    font-family: 'Montserrat', sans-serif;
    font-size: 12px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.18);
}

.notification-flash--success {
    background: #17675f;
    border-left: 5px solid #10b981;
}

.notification-flash--error {
    background: #dc3545;
    border-left: 5px solid #8b0000;
}

.notification-flash strong {
    display: block;
    margin-bottom: 3px;
    font-size: 13px;
}

.notification-flash button {
    padding: 0;
    border: 0;
    background: transparent;
    color: #ffffff;
    font-size: 22px;
    line-height: 1;
    cursor: pointer;
}
.notification-field .form-control::placeholder {
    color: #a7b4c4;
    font-size: 14px;
}
.notification-field select.form-control,
.notification-field select.form-control option {
    font-size: 14px !important;
}
.notification-field .form-control::placeholder {
    color: #a7b4c4;
    font-size: 14px;
}
.notification-field select.form-control option:checked {
    background: #17675F !important;
    background-color: #17675F !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 100px #17675F inset !important;
}

.notification-field select.form-control option:hover,
.notification-field select.form-control option:focus {
    background: #17675F !important;
    background-color: #17675F !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 100px #17675F inset !important;
}
.notification-field select.form-control,
.notification-field select.form-control option {
    font-size: 14px !important;
}
.select2-container--default
.select2-results__option--highlighted[aria-selected] {
    background-color: #17675F !important;
    color: #ffffff !important;
}

.select2-container--default
.select2-results__option[aria-selected="true"] {
    background-color: #17675F !important;
    color: #ffffff !important;
}

.select2-container--default .select2-results__option {
    padding: 10px 16px;
    font-family: Montserrat, sans-serif;
    font-size: 14px;
}

.select2-container--default .select2-selection--single {
    height: 45px;
    border: 1px solid #dbe3ea;
    border-radius: 5px;
}

.select2-container--default
.select2-selection--single
.select2-selection__rendered {
    padding-left: 16px;
    font-size: 14px;
    line-height: 43px;
}

.select2-container--default
.select2-selection--single
.select2-selection__arrow {
    height: 43px;
}
.notification-action .fa-pencil {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 19px;
    height: 19px;
    font-size: 19px;
    line-height: 19px;
}
.notification-action .fa-pencil,
.notification-action .fa-trash-o,
.notification-action .fa-eye {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 19px;
    height: 19px;
    font-size: 19px;
    line-height: 19px;
}
#editNotificationModal input,
#editNotificationModal select,
#editNotificationModal textarea,
#editNotificationModal input::placeholder,
#editNotificationModal textarea::placeholder {
    font-size: 14px !important;
}
.notification-detail-value {
    margin: 0;
    color: #344054;
    font-size: 14px !important;
    font-weight: 500;
    line-height: 1.6;
    word-break: break-word;
}
</style>

<div class="page-body add-notification-page">
    <div class="container-fluid" style="padding-left: 0px !important;padding-right: 0px !important;">
        <div class="add-notification-shell">

            @if (session('success') || session('error') || $errors->any())
    <div class="notification-flash
        {{ session('success') ? 'notification-flash--success' : 'notification-flash--error' }}"
         id="notificationFlash">

        <div>
            <strong>
                {{ session('success') ? 'Success!' : 'Failed!' }}
            </strong>

            <span>
                @if (session('success'))
                    {{ session('success') }}
                @elseif (session('error'))
                    {{ session('error') }}
                @else
                    {{ $errors->first() }}
                @endif
            </span>
        </div>

        <button type="button" onclick="closeNotificationFlash()">
            &times;
        </button>
    </div>
@endif


            <div class="add-notification-heading">
                <h3 style="color: #252525;
leading-trim: both;
text-edge: cap;
font-family: Montserrat;
font-size: 20px;
font-style: normal;
font-weight: 600;
line-height: normal;">Add a New Notification</h3>
                <p style="color: #252525;
leading-trim: both;
text-edge: cap;
font-family: Montserrat;
font-size: 16px;
font-style: normal;
font-weight: 400;
line-height: normal;">Stay Updated, Stay Informed</p>
            </div>

            <div class="card notification-form-card">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}

        <button type="button"
                class="close"
                data-dismiss="alert"
                aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}

        <button type="button"
                class="close"
                data-dismiss="alert"
                aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

        <button type="button"
                class="close"
                data-dismiss="alert"
                aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
                <form method="POST" action="{{ route('notifications.store') }}">
                    @csrf
                    <div class="notification-form-grid">
                        <div class="notification-field">
                            <label for="notificationTitle" style="color: #475569;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: normal;
text-transform: uppercase;">Notification Title</label>
                            <input type="text"
                                   class="form-control"
                                   id="notificationTitle"
                                   name="title"
                                   value="{{ old('title') }}"
                                   required
                                   placeholder="Add title">
                        </div>

                        <div class="notification-field">
                            <label for="notificationCategory" style="color: #475569;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: normal;
text-transform: uppercase;">Notification Category</label>
                            <select class="form-control notification-category-select" id="notificationCategory" name="category" required>
                                <option value="" selected>Select</option>
                                <option value="Faculty" {{ old('category') === 'Faculty' ? 'selected' : '' }}>Faculty</option>
                                <option value="Student" {{ old('category') === 'Student' ? 'selected' : '' }}>Student</option>
                                <option value="Staff" {{ old('category') === 'Staff' ? 'selected' : '' }}>Staff</option>
                                <option value="Faculty &amp; Staff" {{ old('category') === 'Faculty & Staff' ? 'selected' : '' }}>Faculty &amp; Staff</option>
                            </select>
                        </div>

                        <div class="notification-field notification-field--full">
                            <label for="notificationDescription" style="color: #475569;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: normal;
text-transform: uppercase;">Description</label>
                            <textarea class="form-control"
                                      id="notificationDescription"
                                      name="description"
                                      required
                                      placeholder="Detailed description of notification...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="notification-form-actions">
                        <button type="submit" class="add-notification-btn" style="color: #FFF;
font-family: Montserrat;
font-size: 15px;
font-style: normal;
font-weight: 700;
line-height: normal;">
                            Add Notification
                        </button>
                    </div>
                </form>
            </div>

            <h3 class="notification-list-heading" style="color: #111827;
leading-trim: both;
text-edge: cap;
font-family: Montserrat;
font-size: 20px;
font-style: normal;
font-weight: 600;
line-height: normal;">Notifications List</h3>

            <div class="notification-table-card">
                <div class="notification-table-responsive">
                    <table class="notification-table">
                        <thead>
                            <tr>
                                <th style="color: #FFF;
font-family: Montserrat;
font-size: 11px;
font-style: normal;
font-weight: 700;
line-height: normal;
letter-spacing: 1px;
text-transform: uppercase;">Sr.</th>
                                <th style="color: #FFF;
font-family: Montserrat;
font-size: 11px;
font-style: normal;
font-weight: 700;
line-height: normal;
letter-spacing: 1px;
text-transform: uppercase;">Title</th>
                                <th style="color: #FFF;
font-family: Montserrat;
font-size: 11px;
font-style: normal;
font-weight: 700;
line-height: normal;
letter-spacing: 1px;
text-transform: uppercase;">Description</th>
                                <th style="color: #FFF;
font-family: Montserrat;
font-size: 11px;
font-style: normal;
font-weight: 700;
line-height: normal;
letter-spacing: 1px;
text-transform: uppercase;">Category</th>
                                <th style="color: #FFF;
font-family: Montserrat;
font-size: 11px;
font-style: normal;
font-weight: 700;
line-height: normal;
letter-spacing: 1px;
text-transform: uppercase;">Status</th>
                                <th style="color: #FFF;
font-family: Montserrat;
font-size: 11px;
font-style: normal;
font-weight: 700;
line-height: normal;
letter-spacing: 1px;
text-transform: uppercase;">Mode</th>
                                <th style="color: #FFF;
font-family: Montserrat;
font-size: 11px;
font-style: normal;
font-weight: 700;
line-height: normal;
letter-spacing: 1px;
text-transform: uppercase;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $notification)
                                <tr>
                                    <td style="overflow: hidden;
color: #334155;
text-overflow: ellipsis;
font-family: Montserrat;
font-size: 13px;
font-style: normal;
font-weight: 400;
line-height: normal;">{{ $loop->iteration }}</td>
                                    <td style="overflow: hidden;
color: #334155;
text-overflow: ellipsis;
font-family: Montserrat;
font-size: 13px;
font-style: normal;
font-weight: 400;
line-height: normal;">{{ $notification->title }}</td>
                                    <td style="overflow: hidden;
color: #334155;
text-overflow: ellipsis;
font-family: Montserrat;
font-size: 13px;
font-style: normal;
font-weight: 400;
line-height: normal;" title="{{ $notification->description }}">{{ \Illuminate\Support\Str::limit($notification->description, 45) }}</td>
                                    <td style="overflow: hidden;
color: #334155;
text-overflow: ellipsis;
font-family: Montserrat;
font-size: 13px;
font-style: normal;
font-weight: 400;
line-height: normal;">{{ $notification->category }}</td>
                                    <td style="overflow: hidden;
color: #334155;
text-overflow: ellipsis;
font-family: Montserrat;
font-size: 13px;
font-style: normal;
font-weight: 400;
line-height: normal;">
                                        <span class="notification-status {{ (int) $notification->status === 1 ? 'notification-status--enabled' : 'notification-status--disabled' }}">
                                            {{ (int) $notification->status === 1 ? 'Enable' : 'Disable' }}
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('notifications.toggle', $notification->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <label class="notification-switch">
                                                <input type="checkbox" onchange="this.form.submit()" {{ (int) $notification->status === 1 ? 'checked' : '' }}>
                                                <span class="notification-switch__slider"></span>
                                            </label>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="notification-actions">
                                            <a href="javascript:void(0)" class="notification-action notification-edit-action"
                                               data-toggle="modal" data-target="#notificationEditModal"
                                               data-id="{{ $notification->id }}"
                                               data-title="{{ $notification->title }}"
                                               data-category="{{ $notification->category }}"
                                               data-description="{{ $notification->description }}" aria-label="Edit"><i class="fa fa-pencil"></i></a>

                                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}"
                                                  onsubmit="return confirm('Are you sure you want to delete this notification?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="notification-action" style="padding:0;border:0;background:transparent" aria-label="Delete"><i class="fa fa-trash-o"></i></button>
                                            </form>

                                            <a href="javascript:void(0)" class="notification-action notification-view-action"
                                               data-toggle="modal" data-target="#notificationViewModal"
                                               data-id="{{ $notification->id }}"
                                               data-title="{{ $notification->title }}"
                                               data-description="{{ $notification->description }}"
                                               data-category="{{ $notification->category }}"
                                               data-status="{{ (int) $notification->status === 1 ? 'Enable' : 'Disable' }}"
                                               data-mode="{{ (int) $notification->status === 1 ? 'On' : 'Off' }}"><i class="fa fa-eye"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">No notifications found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade notification-view-modal"
     id="notificationViewModal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="notificationViewModalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #17675F !important;color:#fff !important;">
                <h5 class="modal-title" id="notificationViewModalTitle" style="color:#fff !important;">Notification Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color:white !important;">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="notification-detail-grid">
                    <div class="notification-detail-box">
                        <span class="notification-detail-label" style="color: #475569;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: normal;
text-transform: uppercase;">Notification ID</span>
                        <p class="notification-detail-value" id="viewNotificationId"></p>
                    </div>

                    <div class="notification-detail-box">
                        <span class="notification-detail-label" style="color: #475569;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: normal;
text-transform: uppercase;">Category</span>
                        <p class="notification-detail-value" id="viewNotificationCategory"></p>
                    </div>

                    <div class="notification-detail-box">
                        <span class="notification-detail-label" style="color: #475569;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: normal;
text-transform: uppercase;">Status</span>
                        <p class="notification-detail-value" id="viewNotificationStatus"></p>
                    </div>

                    <div class="notification-detail-box">
                        <span class="notification-detail-label" style="color: #475569;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: normal;
text-transform: uppercase;">Mode</span>
                        <p class="notification-detail-value" id="viewNotificationMode"></p>
                    </div>

                    <div class="notification-detail-box notification-detail-box--full">
                        <span class="notification-detail-label" style="color: #475569;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: normal;
text-transform: uppercase;">Notification Title</span>
                        <p class="notification-detail-value" id="viewNotificationTitle"></p>
                    </div>

                    <div class="notification-detail-box notification-detail-box--full">
                        <span class="notification-detail-label" style="color: #475569;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: normal;
text-transform: uppercase;">Description</span>
                        <p class="notification-detail-value" id="viewNotificationDescription"></p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn notification-modal-close"
                        data-dismiss="modal" style="    font-weight: 400;
    font-size: 0.9375rem;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade notification-view-modal" id="notificationEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="notificationEditForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header" style="background:#17675F !important;color:#fff !important;">
                    <h5 class="modal-title" style="color:#fff !important;">Edit Notification</h5>
                    <button type="button" class="close" data-dismiss="modal"><span style="color:#fff !important;">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="notification-form-grid">
                        <div class="notification-field">
                            <label for="editNotificationTitle" style="color: #475569;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: normal;
text-transform: uppercase;">Notification Title</label>
                            <input type="text" class="form-control" id="editNotificationTitle" name="title" required>
                        </div>
                        <div class="notification-field">
                            <label for="editNotificationCategory" style="color: #475569;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: normal;
text-transform: uppercase;">Notification Category</label>
                            <select class="form-control notification-category-select" id="editNotificationCategory" name="category" required>
                                <option value="Faculty">Faculty</option>
                                <option value="Student">Student</option>
                                <option value="Staff">Staff</option>
                                <option value="Faculty &amp; Staff">Faculty &amp; Staff</option>
                            </select>
                        </div>
                        <div class="notification-field notification-field--full">
                            <label for="editNotificationDescription" style="color: #475569;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: normal;
text-transform: uppercase;">Description</label>
                            <textarea class="form-control" id="editNotificationDescription" name="description" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn notification-modal-close" style="font-weight: 400; font-size:0.9375rem;">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script>
    $('#notificationViewModal').on('show.bs.modal', function (event) {
        var viewButton = $(event.relatedTarget);

        $('#viewNotificationId').text(viewButton.data('id'));
        $('#viewNotificationTitle').text(viewButton.data('title'));
        $('#viewNotificationDescription').text(viewButton.data('description'));
        $('#viewNotificationCategory').text(viewButton.data('category'));
        $('#viewNotificationStatus').text(viewButton.data('status'));
        $('#viewNotificationMode').text(viewButton.data('mode'));
    });

    $('#notificationEditModal').on('show.bs.modal', function (event) {
        var editButton = $(event.relatedTarget);
        var updateUrl = @json(route('notifications.update', ['notification' => '__ID__']));

        $('#notificationEditForm').attr('action', updateUrl.replace('__ID__', editButton.data('id')));
        $('#editNotificationTitle').val(editButton.attr('data-title'));
        $('#editNotificationCategory').val(editButton.attr('data-category'));
        $('#editNotificationDescription').val(editButton.attr('data-description'));
    });
    function closeNotificationFlash() {
    $('#notificationFlash').fadeOut(200);
}

$(document).ready(function () {
    if ($('#notificationFlash').length) {
        setTimeout(function () {
            $('#notificationFlash').fadeOut(400);
        }, 4000);
    }
});
$(document).ready(function () {
    $('.notification-category-select').select2({
        width: '100%',
        minimumResultsForSearch: Infinity
    });
});
</script>
@endsection
