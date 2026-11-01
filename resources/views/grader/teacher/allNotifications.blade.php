<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
?>
@dd("124");
<?php
$file_name = "app";

if(auth()->user()->cms_sidebar=='new'){
    $file_name = "updated_app";
}
?>

@extends('layouts.' . $file_name, ['pageTitle' => 'All Notifications'])

@section('content')

<style>
    .all-notifications-page {
        padding-top: 18px;
        padding-bottom: 24px;
        font-family: 'Montserrat', sans-serif;
    }

    .all-notifications-card {
        overflow: hidden;
        min-height: 320px;
        border: 0;
        border-radius: 14px;
        /* background: #fff; */
        box-shadow: none;
    }

    .all-notifications-card__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 54px;
        padding: 0 28px;
    }

    .all-notifications-card__title {
        margin: 0;
        color: #101828;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.3;
    }

    .all-notifications-card__read-all {
        color: #17675f;
        font-size: 9px;
        font-weight: 500;
        text-decoration: none;
    }

    .all-notifications-card__read-all:hover {
        color: #14564f;
        text-decoration: underline;
    }

    .all-notifications-list {
        padding: 24px;
    }

    .all-notification-row {
        display: flex;
        align-items: center;
        min-height: 50px;
        padding: 8px 13px;
        border-bottom: 1px solid #edf1f4;
    }

    .all-notification-row--unread {
        border-bottom-color: transparent;
        border-radius: 5px;
        background: #f1f7f6;
    }

    .all-notification-row__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 27px;
        width: 27px;
        height: 27px;
        margin-right: 10px;
        border-radius: 50%;
        background: #17675f;
        color: #fff;
        font-size: 12px;
    }

    .all-notification-row__main {
        min-width: 0;
        flex: 1 1 auto;
    }

    .all-notification-row__line {
        display: flex;
        align-items: center;
        width: 100%;
    }

    .all-notification-row__message {
        overflow: hidden;
        flex: 1 1 auto;
        margin: 0;
        color: #344054;
        font-size: 9px;
        font-weight: 500;
        line-height: 1.45;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .all-notification-row__time {
        display: flex;
        align-items: center;
        flex: 0 0 auto;
        margin-left: 16px;
        color: #9aabc0;
        font-size: 8px;
        font-weight: 400;
        white-space: nowrap;
    }

    .all-notification-row__dot {
        width: 5px;
        height: 5px;
        margin-right: 9px;
        border-radius: 50%;
        background: #17675f;
    }

    .all-notification-row__detail {
        max-width: 850px;
        margin: 4px 0 0;
        color: #667085;
        font-size: 8px;
        font-weight: 400;
        line-height: 1.5;
    }

    .side-app {
        margin-bottom: 0 !important;
    }

    @media (max-width: 767.98px) {
        .all-notifications-card__header {
            padding-right: 16px;
            padding-left: 16px;
        }

        .all-notifications-list {
            padding-right: 12px;
            padding-left: 12px;
        }

        .all-notification-row {
            align-items: flex-start;
            padding: 12px 10px;
        }

        .all-notification-row__line {
            align-items: flex-start;
            flex-direction: column;
        }

        .all-notification-row__message {
            overflow: visible;
            white-space: normal;
        }

        .all-notification-row__time {
            margin-top: 5px;
            margin-left: 0;
        }
    }
    .all-notifications-card__read-all {
    padding: 0;
    border: 0;
    background: transparent;
    color: #17675f;
    font-size: 9px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
}

.all-notifications-card__read-all:hover {
    color: #14564f;
    text-decoration: underline;
}

.all-notifications-card__read-all:disabled {
    cursor: wait;
    opacity: 0.6;
}
.all-notifications-list
.all-notification-row:not(:first-child) {
    padding-top: 20px;
}
</style>

<div class="page-body all-notifications-page" style="padding-top: 0 !important;">
    <div class="container-fluid" style="padding-left: 0px !important;padding-right: 0px !important;">
        <div class="row">
            <div class="col-12">
                <div class="all-notifications-card">
                    <div class="all-notifications-card__header" style="padding-left: 0 !important;padding-right: 0 !important;">
                        <h4 class="all-notifications-card__title" style="color: #0F172A;
font-family: Montserrat;
font-size: 20px;
font-style: normal;
font-weight: 700;
line-height: normal;">Notifications</h4>

                      <button type="button"
        id="markAllNotificationsRead"
        class="all-notifications-card__read-all"
        onclick="markAllNotificationsAsRead(this)" style="color: #17675F;
text-align: right;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 500;
line-height: normal;">
    Mark all as read
</button>
                    </div>

                    <div class="card all-notifications-list">
                        {{-- @php
                            $notifications = \Illuminate\Support\Facades\DB::select('
                                SELECT ID AS "id",
                                       TITLE AS "title",
                                       CATEGORY AS "category",
                                       DESCRIPTION AS "description",
                                       STATUS AS "status",
                                       CREATED_AT AS "created_at",
                                       UPDATED_AT AS "updated_at"
                                FROM NOTIFICATIONS
                                WHERE STATUS = 1
                                ORDER BY ID DESC
                            ');
                        @endphp --}}

                        @forelse($notifications as $notification)
                            @php
                                $categoryIcons = [
                                    'Faculty' => 'fa-users',
                                    'Student' => 'fa-graduation-cap',
                                    'Staff' => 'fa-user',
                                    'Faculty & Staff' => 'fa-users',
                                ];

                                $notificationIcon = $categoryIcons[$notification->category] ?? 'fa-bell';
                                $notificationTime = $notification->created_at
                                    ? \Carbon\Carbon::parse($notification->created_at)->diffForHumans()
                                    : '';
                            @endphp

<div class="all-notification-row
    {{ (int) $notification->is_read === 0
        ? 'all-notification-row--unread'
        : '' }}">
                                <span class="all-notification-row__icon" aria-hidden="true">
                                    <i class="fa {{ $notificationIcon }}"></i>
                                </span>

                                <div class="all-notification-row__main">
                                    <div class="all-notification-row__line">
                                        <p class="all-notification-row__message" style="color: #334155;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: 18px;">
                                            {{ $notification->title }}
                                        </p>

                                     <span class="all-notification-row__time" style="color: #94A3B8;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 400;
line-height: normal;">
    @if ((int) $notification->is_read === 0)
        <span class="all-notification-row__dot"></span>
    @endif

    {{ $notificationTime }}
</span>
                                    </div>

                                    <p class="all-notification-row__detail" style="color: #334155;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 400;
line-height: 18px; ">
                                        {{ $notification->description }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="all-notification-row">
                                <span class="all-notification-row__icon" aria-hidden="true">
                                    <i class="fa fa-bell-o"></i>
                                </span>

                                <div class="all-notification-row__main">
                                    <p class="all-notification-row__message" style="color: #334155;
font-family: Montserrat;
font-size: 12px;
font-style: normal;
font-weight: 600;
line-height: 18px;">
                                        No active notifications available.
                                    </p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function markAllNotificationsAsRead(button) {
        button.disabled = true;

        fetch("{{ route('notifications.readAll') }}", {
            method: "PATCH",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Request failed');
            }

            return response.json();
        })
        .then(function (data) {
            if (!data.success) {
                throw new Error(data.message);
            }

            window.location.reload();
        })
        .catch(function (error) {
            console.error(error);
            button.disabled = false;
            alert('Notifications could not be marked as read.');
        });
    }
</script>

@endsection
