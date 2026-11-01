<style>
    .section-title{margin:0 0 14px;color:#2d2d2d;font-size:24px;font-weight:700;line-height:1.2}
    .section-view-all{color:#2d2d2d;font-size:15px;font-weight:500;text-decoration:underline;transition:color .2s ease}
    .section-view-all:hover{color:#17675f;text-decoration:underline}

    .notification-wrapper{display:flex;min-height:0;flex-direction:column;overflow:hidden}
    .notification-wrapper>.card.notification-card{display:flex;height:0!important;min-height:0;margin-bottom:0!important;flex:1 1 0;flex-direction:column;overflow:hidden;border:0;border-radius:16px!important;background:#fff;box-shadow:0 2px 12px rgba(0,0,0,.05)}
    .notification-wrapper .notification-body{height:0!important;min-height:0;padding:24px;flex:1 1 0;overflow-x:hidden!important;overflow-y:auto!important;background:#fff;scrollbar-width:thin;scrollbar-color:#cfd8e3 transparent}
    .notification-body::-webkit-scrollbar{width:6px}
    .notification-body::-webkit-scrollbar-thumb{border-radius:20px;background:#cfd8e3}
    .notification-body::-webkit-scrollbar-track{background:transparent}

    .notification-row{display:flex;width:100%;min-height:64px;padding:10px 20px;align-items:center;gap:12px;border-bottom:1px solid #f1f5f9;color:inherit;text-decoration:none!important;transition:background-color .2s ease}
    .notification-row:last-child{border-bottom:0}
    .notification-row:hover{color:inherit;background:rgba(15,118,110,.035);text-decoration:none!important}
    .notification-row--unread{margin-bottom:20px;border-bottom:0;border-radius:5px;background:rgba(15,118,110,.05)}

    .notification-icon{display:flex;width:40px;height:40px;flex:0 0 40px;align-items:center;justify-content:center;border-radius:50%;background:#17675f;color:#fff;font-size:18px}
    .notification-content{min-width:0;flex:1 1 auto;color:#334155;font-family:Montserrat,sans-serif;line-height:18px}
    .notification-content-title,.notification-content-description{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .notification-content-title{font-size:12px;font-weight:600}
    .notification-content-description{margin-top:2px;font-size:12px;font-weight:500}
    .notification-time{flex:0 0 auto;color:#94a3b8;font-family:Montserrat,sans-serif;font-size:12px;font-weight:400;white-space:nowrap}
    .notification-unread-dot{width:8px;height:8px;flex:0 0 8px;border-radius:50%;background:#17675f}
    .notification-arrow{display:flex;width:16px;height:16px;flex:0 0 16px;align-items:center;justify-content:center;color:#94a3b8;font-size:18px;line-height:1;transition:color .2s ease,transform .2s ease}
    .notification-row:hover .notification-arrow{color:#17675f;transform:translateX(2px)}

    .no-notification-body{display:flex;flex-direction:column;align-items:center;justify-content:center}
    .no-notification-body .center{display:block;width:90px;margin:0}
    .no-notification-body .center1{display:block;margin:12px 0 0;text-align:center}

    @media(max-width:767.98px){
        .notification-wrapper{overflow:visible}
        .notification-wrapper>.card.notification-card{height:auto!important;flex:none}
        .notification-wrapper .notification-body{height:auto!important;max-height:420px;flex:none}
        .notification-row{padding-right:12px;padding-left:12px}
        .notification-time{max-width:74px;overflow:hidden;text-overflow:ellipsis}
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="section-title mb-0">Notifications</h4>
    <a href="{{ route('allNotification') }}" class="section-view-all">View All</a>
</div>

<div class="card notification-card">
    <div class="card-body notification-body {{ empty($all_notifications) ? 'no-notification-body' : '' }}">
        @forelse ($all_notifications as $notification)
            @php
                $isUnread = (int) $notification->is_read === 0;
            @endphp

            <a href="{{ route('allNotification') }}"
               data-notification-id="{{ $notification->id }}"
               class="notification-row {{ $isUnread ? 'notification-row--unread' : 'notification-row--read' }}"
               aria-label="View notification: {{ $notification->title }}">
                <span class="notification-icon" aria-hidden="true">
                    <i class="fa fa-bell-o"></i>
                </span>

                <span class="notification-content">
                    <span class="notification-content-title">{{ $notification->title }}</span>
                    <span class="notification-content-description">{{ $notification->description }}</span>
                </span>

                <span class="notification-time">{{ $notification->display_date }}</span>

                @if ($isUnread)
                    <span class="notification-unread-dot" aria-label="Unread notification"></span>
                @endif

                <span class="notification-arrow" aria-hidden="true">&rsaquo;</span>
            </a>
        @empty
            <img src="{{ asset('/staffimages/no.png') }}" class="center" alt="No notifications">
            <h5 class="center1">No Notifications</h5>
        @endforelse
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var notificationLinks = document.querySelectorAll(
            '.notification-row--unread[data-notification-id]'
        );

        var readUrlTemplate = @json(
            route('notifications.read', ['notification' => '__NOTIFICATION_ID__'])
        );

        notificationLinks.forEach(function (link) {
            link.addEventListener('click', function (event) {
                if (event.button !== 0 || event.ctrlKey || event.metaKey ||
                    event.shiftKey || event.altKey) {
                    return;
                }

                event.preventDefault();

                var destinationUrl = link.href;
                var notificationId = link.dataset.notificationId;
                var readUrl = readUrlTemplate.replace(
                    '__NOTIFICATION_ID__',
                    encodeURIComponent(notificationId)
                );

                fetch(readUrl, {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Unable to mark notification as read.');
                    }

                    link.classList.remove('notification-row--unread');
                    link.classList.add('notification-row--read');

                    var unreadDot = link.querySelector('.notification-unread-dot');
                    if (unreadDot) {
                        unreadDot.remove();
                    }
                })
                .catch(function (error) {
                    console.error(error);
                })
                .finally(function () {
                    window.location.assign(destinationUrl);
                });
            });
        });
    });
</script>
