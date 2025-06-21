<?php

namespace App\Services;

use App\Events\PdfGenerationStarted;
use App\Events\PdfGenerationProgress;
use App\Events\PdfGenerationCompleted;
use App\Events\PdfGenerationFailed;
use Illuminate\Support\Facades\Log;

class PdfNotificationService
{
    /**
     * Notificar início da geração
     */
    public function notifyStarted(string $jobId, int $userId, string $filename, string $reportType = 'gastos'): void
    {
        Log::info("PDF generation started", [
            'job_id' => $jobId,
            'user_id' => $userId,
            'filename' => $filename,
            'report_type' => $reportType
        ]);

        broadcast(new PdfGenerationStarted($jobId, $userId, $filename, $reportType));
    }

    /**
     * Notificar progresso da geração
     */
    public function notifyProgress(string $jobId, int $userId, int $progressPercentage, string $currentStep): void
    {
        Log::debug("PDF generation progress", [
            'job_id' => $jobId,
            'user_id' => $userId,
            'progress' => $progressPercentage,
            'step' => $currentStep
        ]);

        broadcast(new PdfGenerationProgress($jobId, $userId, $progressPercentage, $currentStep));
    }

    /**
     * Notificar conclusão da geração
     */
    public function notifyCompleted(string $jobId, int $userId, string $filename, string $downloadUrl): void
    {
        Log::info("PDF generation completed", [
            'job_id' => $jobId,
            'user_id' => $userId,
            'filename' => $filename,
            'download_url' => $downloadUrl
        ]);

        broadcast(new PdfGenerationCompleted($jobId, $userId, $filename, $downloadUrl));
    }

    /**
     * Notificar falha na geração
     */
    public function notifyFailed(string $jobId, int $userId, string $errorMessage, int $retryCount = 0): void
    {
        Log::error("PDF generation failed", [
            'job_id' => $jobId,
            'user_id' => $userId,
            'error' => $errorMessage,
            'retry_count' => $retryCount
        ]);

        broadcast(new PdfGenerationFailed($jobId, $userId, $errorMessage, $retryCount));
    }

    /**
     * Gerar um job ID único
     */
    public function generateJobId(): string
    {
        return 'pdf_' . uniqid() . '_' . time();
    }
}
