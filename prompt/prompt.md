# Guia Backend: Implementação WebSockets para Relatórios PDF

## 📋 Pré-requisitos Verificados
- ✅ Laravel Reverb configurado
- ✅ Redis funcionando
- ✅ Queue worker ativo
- ✅ Broadcasting configurado

---

## 🏗️ Implementação Backend - Passo a Passo

### Etapa 1: Criar os Eventos de PDF

#### 1.1 Evento: PdfGenerationStarted
```bash
php artisan make:event PdfGenerationStarted
```

**Arquivo**: `app/Events/PdfGenerationStarted.php`
```php
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
```

#### 1.2 Evento: PdfGenerationProgress
```bash
php artisan make:event PdfGenerationProgress
```

**Arquivo**: `app/Events/PdfGenerationProgress.php`
```php
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
```

#### 1.3 Evento: PdfGenerationCompleted
```bash
php artisan make:event PdfGenerationCompleted
```

**Arquivo**: `app/Events/PdfGenerationCompleted.php`
```php
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
```

#### 1.4 Evento: PdfGenerationFailed
```bash
php artisan make:event PdfGenerationFailed
```

**Arquivo**: `app/Events/PdfGenerationFailed.php`
```php
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
```

---

### Etapa 2: Configurar Autorização de Canais

#### 2.1 Criar/Atualizar routes/channels.php
```php
<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// Canal privado para notificações de PDF do usuário
Broadcast::channel('pdf.{userId}', function ($user, $userId) {
    // Usuário só pode acessar seu próprio canal
    return (int) $user->id === (int) $userId;
});

// Canal de teste (manter o existente se houver)
Broadcast::channel('message-channel', function ($user) {
    return true; // ou sua lógica específica
});
```

---

### Etapa 3: Criar Serviço de Notificações

#### 3.1 Criar PdfNotificationService
```bash
php artisan make:class Services/PdfNotificationService
```

**Arquivo**: `app/Services/PdfNotificationService.php`
```php
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
```

---

### Etapa 4: Modificar o Job Existente

#### 4.1 Atualizar GeneratePdfJob
**Arquivo**: `app/Jobs/GeneratePdfJob.php`
```php
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
    use Dispatchable, Queueable, SerializesModels;

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
            $downloadUrl = route('dashboard.download.relatorio', $this->filename);
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
```

---

### Etapa 5: Modificar o Controller

#### 5.1 Atualizar DashboardController
**Arquivo**: `app/Http/Controllers/Api/DashboardController.php`

**Adicionar no topo da classe:**
```php
use App\Services\PdfNotificationService;
```

**Modificar o método relatorio():**
```php
public function relatorio(Request $request)
{
    // Definir período padrão se não fornecido
    $dataInicio = $request->get('data_inicio') 
        ? $request->get('data_inicio')
        : Carbon::now()->subMonths(6)->startOfMonth()->format('Y-m-d');
        
    $dataFim = $request->get('data_fim') 
        ? $request->get('data_fim')
        : Carbon::now()->endOfMonth()->format('Y-m-d');

    $filters = $this->validateFilters($request);
    $data = [
        'resumo' => $this->getResumoFinanceiro($filters),
        'evolucao_mensal' => $this->getEvolucaoMensal($filters),
        'grafico_data' => $this->getGraficoData($filters),
    ];

    // Buscar gastos detalhados para a tabela
    $gastos = Gasto::with(['categoriaGasto', 'obra', 'fontePagadora'])
        ->when(!empty($filters['obras']), fn($q) => $q->whereIn('obra_id', $filters['obras']))
        ->when(!empty($filters['categorias_gasto']), fn($q) => $q->whereIn('categoria_gasto_id', $filters['categorias_gasto']))
        ->whereBetween('data_pagamento', [$dataInicio, $dataFim])
        ->whereNotNull('data_pagamento')
        ->orderBy('data_pagamento', 'desc')
        ->get();

    $data['gastos'] = $gastos;

    // Gerar IDs únicos
    $notificationService = app(PdfNotificationService::class);
    $jobId = $notificationService->generateJobId();
    $filename = 'relatorio_gastos_' . now()->format('Ymd_His') . '.pdf';
    $userId = auth()->id();
    
    // Dispatch do job para a fila COM notificações
    dispatch(new GeneratePdfJob(
        view: 'reports.gastos',
        data: ['data' => $data],
        filename: $filename,
        jobId: $jobId,
        userId: $userId,
        options: [
            'landscape' => true,
            'format' => 'a4',
            'margin' => [
                'top' => '10mm',
                'right' => '10mm',
                'bottom' => '10mm',
                'left' => '10mm'
            ]
        ],
    ))->onQueue('pdf');
    
    // Retornar resposta com job_id
    return response()->json([
        'message' => 'Relatório está sendo gerado',
        'job_id' => $jobId,
        'filename' => $filename,
        'status' => 'processing',
        'websocket_channel' => "private-pdf.{$userId}"
    ]);
}
```

**Adicionar novos métodos:**
```php
/**
 * Cancelar geração de relatório
 */
public function cancelarRelatorio(Request $request)
{
    $jobId = $request->get('job_id');
    $userId = auth()->id();
    
    // Aqui você implementaria a lógica para cancelar o job
    // Por exemplo, usando Redis para sinalizar cancelamento
    
    return response()->json([
        'message' => 'Solicitação de cancelamento enviada',
        'job_id' => $jobId
    ]);
}

/**
 * Status do relatório (fallback para casos sem WebSocket)
 */
public function statusRelatorio(string $jobId)
{
    $userId = auth()->id();
    
    // Implementar lógica de verificação de status
    // Por exemplo, consultar Redis ou banco
    
    return response()->json([
        'job_id' => $jobId,
        'status' => 'processing', // ou 'completed', 'failed'
        'user_id' => $userId
    ]);
}
```

---

### Etapa 6: Atualizar Rotas

#### 6.1 Modificar routes/api.php
**Adicionar após as rotas do dashboard existentes:**
```php
// Relatórios PDF com WebSocket
Route::prefix('relatorios')->middleware('auth:sanctum')->group(function () {
    Route::post('gastos', [DashboardController::class, 'relatorio'])->name('relatorio');
    Route::delete('cancelar', [DashboardController::class, 'cancelarRelatorio']);
    Route::get('status/{jobId}', [DashboardController::class, 'statusRelatorio']);
    Route::get('download/{filename}', [DashboardController::class, 'downloadRelatorio'])->name('dashboard.download.relatorio');
});

// Manter rota legacy para compatibilidade (opcional)
Route::get('relatorios/gastos', [DashboardController::class, 'relatorio'])->name('relatorio.legacy')->middleware('auth:sanctum');
```

---

## ✅ Checklist de Implementação

- [ ] 1. Criar 4 eventos de PDF
- [ ] 2. Configurar canais em `routes/channels.php`
- [ ] 3. Criar `PdfNotificationService`
- [ ] 4. Modificar `GeneratePdfJob`
- [ ] 5. Atualizar `DashboardController`
- [ ] 6. Atualizar rotas
- [ ] 7. Testar com usuário real (solicitar ao usuario que ele teste)

---

O agente deve implementar os passos acima e se manter no escopo da tarefa, ou seja, ele não deve implementar mais coisas que não estão no checklist acima.
qualque duvida de como agir deve ser perguntada ao usuario.