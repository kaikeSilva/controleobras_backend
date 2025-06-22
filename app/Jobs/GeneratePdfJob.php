<?php

namespace App\Jobs;

use App\Services\PdfNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GeneratePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $maxExceptions = 3;
    public int $timeout = 240;

    public function __construct(
        public string $view,
        public array  $data,
        public string $filename,
        public string $jobId,
        public int    $userId,
        public array  $options = [],
    ) {}

    public function handle(): void
    {
        $notificationService = app(PdfNotificationService::class);

        try {
            // 1. Notificar início
            $notificationService->notifyStarted(
                $this->jobId, 
                $this->userId, 
                $this->filename
            );

            // 2. Progresso: Renderizando HTML
            $notificationService->notifyProgress(
                $this->jobId, 
                $this->userId, 
                25, 
                'Preparando dados do relatório'
            );

            $html = view($this->view, $this->data)->render();

            // 3. Progresso: Enviando para serviço PDF
            $notificationService->notifyProgress(
                $this->jobId, 
                $this->userId, 
                50, 
                'Gerando arquivo PDF'
            );

            $response = Http::timeout(240)
                ->post(config('services.pdf.url', env('PDF_SERVICE_URL')), [
                    'html'    => $html,
                    'options' => $this->options,
                ]);

            if ($response->failed()) {
                throw new \RuntimeException('PDF service error: '.$response->body());
            }

            // 4. Progresso: Salvando arquivo
            $notificationService->notifyProgress(
                $this->jobId, 
                $this->userId, 
                75, 
                'Salvando arquivo'
            );

            Storage::disk(config('filesystems.pdf_disk', 'pdfs'))
                ->put($this->filename, $response->body());

            // 5. Progresso: Finalizado
            $notificationService->notifyProgress(
                $this->jobId, 
                $this->userId, 
                100, 
                'Concluído'
            );

            // 6. Notificar conclusão
            // Construindo URL manualmente para evitar problemas de resolução de rota no worker
            $appUrl = config('app.url', 'http://localhost:8000');
            $downloadUrl = "{$appUrl}/api/relatorios/download/{$this->filename}";
            $notificationService->notifyCompleted(
                $this->jobId, 
                $this->userId, 
                $this->filename, 
                $downloadUrl
            );

        } catch (Throwable $e) {
            $this->handleFailure($e, $notificationService);
            throw $e;
        }
    }

    /**
     * Tratar falha no job
     */
    public function failed(Throwable $exception): void
    {
        $notificationService = app(PdfNotificationService::class);
        $this->handleFailure($exception, $notificationService);
    }

    private function handleFailure(Throwable $exception, PdfNotificationService $notificationService): void
    {
        $retryCount = $this->attempts() - 1;
        
        $notificationService->notifyFailed(
            $this->jobId,
            $this->userId,
            $exception->getMessage(),
            $retryCount
        );
    }
}
