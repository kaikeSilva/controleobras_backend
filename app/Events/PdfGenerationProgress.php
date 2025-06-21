<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PdfGenerationProgress implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $jobId,
        public int $userId,
        public int $progressPercentage,
        public string $currentStep
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("pdf.{$this->userId}")
        ];
    }

    public function broadcastAs(): string
    {
        return 'pdf.generation.progress';
    }

    public function broadcastWith(): array
    {
        return [
            'job_id' => $this->jobId,
            'progress' => $this->progressPercentage,
            'current_step' => $this->currentStep,
            'status' => 'processing',
            'timestamp' => now()->toISOString()
        ];
    }
}
