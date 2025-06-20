<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GeneratePdfJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct(
        public string $view,
        public array  $data,
        public string $filename,
        public array  $options = [],
    ) {}

    public function handle(): void
    {
        $html = view($this->view, $this->data)->render();

        $response = Http::timeout(240)
            ->post(config('services.pdf.url'), [
                'html'    => $html,
                'options' => $this->options,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('PDF service error: '.$response->body());
        }

        Storage::disk(config('filesystems.pdf_disk', 'pdfs'))
            ->put($this->filename, $response->body());
    }
}
