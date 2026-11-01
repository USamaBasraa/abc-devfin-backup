@php
    $history = $history ?? [];
    $emptyLabel = $emptyLabel ?? 'N/A';
@endphp

@once
<style>
    .grievance-remarks-history {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .grievance-remarks-entry {
        background: #f0f4ff;
        border: 1px solid #dbe3ff;
        border-radius: 8px;
        padding: 12px 14px;
    }
    .grievance-remarks-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-size: 11px;
        line-height: 1.4;
        color: #6b7280;
    }
    .grievance-remarks-meta-sep {
        color: #d1d5db;
    }
    .grievance-remarks-action {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 999px;
        background: #eef2ff;
        color: #4555C3;
    }
    .grievance-remarks-action.is-completed {
        background: #d4edda;
        color: #1c6b2f;
    }
    .grievance-remarks-action.is-revised {
        background: #fde8d8;
        color: #de690c;
    }
    .grievance-remarks-text {
        font-size: 14px;
        line-height: 1.55;
        color: #1f2937;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .grievance-remarks-empty {
        margin: 0;
        color: #9ca3af;
        font-size: 14px;
        font-style: italic;
    }
</style>
@endonce

@if(count($history) > 0)
    <ul class="grievance-remarks-history">
        @foreach($history as $entry)
            @php
                $action = $entry['action'] ?? null;
                $actionLabel = null;
                $actionClass = '';
                if ($action === 'completed') {
                    $actionLabel = 'Completed';
                    $actionClass = 'is-completed';
                } elseif ($action === 'revised') {
                    $actionLabel = 'Revised';
                    $actionClass = 'is-revised';
                }
                $atFormatted = null;
                if (!empty($entry['at'])) {
                    try {
                        $atFormatted = \Carbon\Carbon::parse($entry['at'])->format('d M Y, h:i A');
                    } catch (\Exception $e) {
                        $atFormatted = $entry['at'];
                    }
                }
            @endphp
            <li class="grievance-remarks-entry">
                <div class="grievance-remarks-meta">
                    @if($atFormatted)
                        <span>{{ $atFormatted }}</span>
                    @endif
                    @if($atFormatted && !empty($entry['by']))
                        <span class="grievance-remarks-meta-sep">·</span>
                    @endif
                    @if(!empty($entry['by']))
                        <span>Resolved by Admin</span>
                    @endif
                    @if($actionLabel)
                        @if($atFormatted || !empty($entry['by']))
                            <span class="grievance-remarks-meta-sep">·</span>
                        @endif
                        <span class="grievance-remarks-action {{ $actionClass }}">{{ $actionLabel }}</span>
                    @endif
                </div>
                <div class="grievance-remarks-text">{{ $entry['remarks'] }}</div>
            </li>
        @endforeach
    </ul>
@else
    <p class="grievance-remarks-empty">{{ $emptyLabel }}</p>
@endif
