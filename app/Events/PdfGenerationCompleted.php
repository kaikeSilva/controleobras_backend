<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PdfGenerationCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $jobId,
        public int $userId,
        public string $filename,
        public string $downloadUrl
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("pdf.{$this->userId}")
        ];
    }

    public function broadcastAs(): string
    {
        return 'pdf.generation.completed';
    }

    public function broadcastWith(): array
    {
        $disk = config('filesystems.pdf_disk', 'pdfs');
        $fileSize = Storage::disk($disk)->exists($this->filename) 
            ? Storage::disk($disk)->size($this->filename) 
            : 0;

        return [
            'job_id' => $this->jobId,
            'filename' => $this->filename,
            'download_url' => $this->downloadUrl,
            'file_size' => $fileSize,
            'file_size_formatted' => $this->formatFileSize($fileSize),
            'status' => 'completed',
            'timestamp' => now()->toISOString(),
            'message' => 'Relatório gerado com sucesso'
        ];
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1024 * 1024) {
            return round($bytes / (1024 * 1024), 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}
