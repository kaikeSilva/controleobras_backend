@php
    $statusClass = 'status-disconnected';
    if ($status === 'connected') {
        $statusClass = 'status-connected';
    } elseif ($status === 'connecting') {
        $statusClass = 'status-connecting';
    }
@endphp

<div class="status-indicator {{ $statusClass }}">
    Status: <strong>{{ strtoupper($status) }}</strong><br>
    @if(isset($app_id) && $app_id !== 'N/A')
        App ID: {{ $app_id }}<br>
    @endif
    @if(isset($host) && isset($port) && $host !== 'N/A')
        Reverb: {{ $host }}:{{ $port }}<br>
    @endif
    @if(isset($message) && $message)
        Message: {{ $message }}
    @endif
</div>
