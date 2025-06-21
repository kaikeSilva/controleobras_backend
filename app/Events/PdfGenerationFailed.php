<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PdfGenerationFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $jobId,
        public int $userId,
        public string $errorMessage,
        public int $retryCount = 0
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("pdf.{$this->userId}")
        ];
    }

    public function broadcastAs(): string
    {
        return 'pdf.generation.failed';
    }

    public function broadcastWith(): array
    {
        return [
            'job_id' => $this->jobId,
            'error_message' => $this->errorMessage,
            'retry_count' => $this->retryCount,
            'can_retry' => $this->retryCount < 3,
            'status' => 'failed',
            'timestamp' => now()->toISOString(),
            'message' => 'Falha na geração do relatório'
        ];
    }
}
