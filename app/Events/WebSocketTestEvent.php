<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WebSocketTestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $testType;
    public $timestamp;
    public $testId;

    public function __construct($message, $testType = 'normal', $testId = null)
    {
        $this->message = $message;
        $this->testType = $testType;
        $this->timestamp = now()->format('Y-m-d H:i:s');
        $this->testId = $testId ?? uniqid('test_');
        
        Log::info("🎯 [WEBSOCKET TEST] Event criado: {$this->message} | ID: {$this->testId}");
    }

    public function broadcastOn()
    {
        return [
            new Channel('test-channel'),
            new Channel("test-channel-{$this->testType}")
        ];
    }

    public function broadcastAs()
    {
        return 'websocket.test';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'test_type' => $this->testType,
            'timestamp' => $this->timestamp,
            'test_id' => $this->testId,
            'server_time' => now()->toISOString()
        ];
    }
}
