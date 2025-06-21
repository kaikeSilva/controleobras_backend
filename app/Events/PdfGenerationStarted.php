<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PdfGenerationStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $jobId,
        public int $userId,
        public string $filename,
        public string $reportType = 'gastos'
    ) {}

    /**
     * Canal privado específico do usuário
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("pdf.{$this->userId}")
        ];
    }

    /**
     * Nome do evento no frontend
     */
    public function broadcastAs(): string
    {
        return 'pdf.generation.started';
    }

    /**
     * Dados enviados para o frontend
     */
    public function broadcastWith(): array
    {
        return [
            'job_id' => $this->jobId,
            'filename' => $this->filename,
            'report_type' => $this->reportType,
            'status' => 'started',
            'timestamp' => now()->toISOString(),
            'message' => 'Geração do relatório iniciada'
        ];
    }
}
