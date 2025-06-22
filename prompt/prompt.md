logs do envio do evento pelo laravel:
 *  Executing task: docker exec -it 30bc764bab9d4f1c7207d097d3d572b4286de8e47dbd30f2bd8cb5fc9e7c7b6d bash 

30bc764bab9d:/var/www$ php artisan tinker
Psy Shell v0.12.8 (PHP 8.4.8 — cli) by Justin Hileman
> event(new \App\Events\TestMessageEvent([
    'id' => 'abc123',
    'timestamp' => now()->toDateTimeString(),
    'user_info' => 'TestSessionID:123',
    'message' => 'Mensagenzinha 🚀',
    'event_type' => 'TestMessageEvent',
]));
= []

> 


terminal wscat:
kaike@kaike-Inspiron-3583:~$ wscat -c "ws://localhost:8087/app/demo_key?protocol=7&client=js&version=5.0.0"
Connected (press CTRL+C to quit)
< {"event":"pusher:connection_established","data":"{\"socket_id\":\"757313518.537770719\",\"activity_timeout\":30}"}
> {"event":"pusher:subscribe","data":{"channel":"message-channel"}}
< {"event":"pusher_internal:subscription_succeeded","data":"{}","channel":"message-channel"}
< {"event":"test.message.event","data":"{\"id\":\"abc123\",\"timestamp\":\"2025-06-22 07:42:02\",\"user_info\":\"TestSessionID:123\",\"message\":\"Mensagenzinha \\ud83d\\ude80\",\"event_type\":\"TestMessageEvent\"}","channel":"message-channel"}
> 


logs do nginx do reverb:
 *  Executing task: docker logs --tail 1000 -f a22608b06f44297245d98fb871c3b0998ebe27d184df78ccf7bff86b2fc6b410 

/docker-entrypoint.sh: /docker-entrypoint.d/ is not empty, will attempt to perform configuration
/docker-entrypoint.sh: Looking for shell scripts in /docker-entrypoint.d/
/docker-entrypoint.sh: Launching /docker-entrypoint.d/10-listen-on-ipv6-by-default.sh
10-listen-on-ipv6-by-default.sh: info: Getting the checksum of /etc/nginx/conf.d/default.conf
10-listen-on-ipv6-by-default.sh: info: Enabled listen on IPv6 in /etc/nginx/conf.d/default.conf
/docker-entrypoint.sh: Sourcing /docker-entrypoint.d/15-local-resolvers.envsh
/docker-entrypoint.sh: Launching /docker-entrypoint.d/20-envsubst-on-templates.sh
/docker-entrypoint.sh: Launching /docker-entrypoint.d/30-tune-worker-processes.sh
/docker-entrypoint.sh: Configuration complete; ready for start up
172.20.0.5 - - [22/Jun/2025:10:42:04 +0000] "POST /apps/101/events?auth_key=demo_key&auth_timestamp=1750588923&auth_version=1.0&body_md5=04ba9a5f84ef6e316f3f7cd575a66604&auth_signature=9cbd76f1ea9102eac11dd03f28dcabd4e7ef9689b387bbea99ff69ae7c370bf0 HTTP/1.1" 200 2 "-" "GuzzleHttp/7"
172.20.0.1 - - [22/Jun/2025:10:43:04 +0000] "GET /app/demo_key?protocol=7&client=js&version=5.0.0 HTTP/1.1" 101 454 "-" "-"

logs do evento do pdf na fila:

  2025-06-22 07:44:40 App\Jobs\GeneratePdfJob ........................ RUNNING
  2025-06-22 07:44:45 App\Jobs\GeneratePdfJob ........................ 4s DONE
  2025-06-22 07:44:45 App\Events\PdfGenerationStarted ................ RUNNING
  2025-06-22 07:44:45 App\Events\PdfGenerationStarted ............ 3.30ms DONE
  2025-06-22 07:44:45 App\Events\PdfGenerationProgress ............... RUNNING
  2025-06-22 07:44:45 App\Events\PdfGenerationProgress ........... 2.19ms DONE
  2025-06-22 07:44:45 App\Events\PdfGenerationProgress ............... RUNNING
  2025-06-22 07:44:45 App\Events\PdfGenerationProgress ........... 2.27ms DONE
  2025-06-22 07:44:45 App\Events\PdfGenerationProgress ............... RUNNING
  2025-06-22 07:44:45 App\Events\PdfGenerationProgress ........... 2.71ms DONE
  2025-06-22 07:44:45 App\Events\PdfGenerationProgress ............... RUNNING
  2025-06-22 07:44:45 App\Events\PdfGenerationProgress ........... 2.56ms DONE
  2025-06-22 07:44:45 App\Events\PdfGenerationCompleted .............. RUNNING
  2025-06-22 07:44:45 App\Events\PdfGenerationCompleted .......... 4.09ms DONE

logs do evento do pdf no nginx do reverb:

172.20.0.5 - - [22/Jun/2025:10:44:45 +0000] "POST /apps/101/events?auth_key=demo_key&auth_timestamp=1750589085&auth_version=1.0&body_md5=b618c62cb84896945fcc1dbbec5810aa&auth_signature=ac09780a800e00bc8f0aba606fa7c989a6acb09cc7428d123ba1cda43aaa4ce0 HTTP/1.1" 200 2 "-" "GuzzleHttp/7"
172.20.0.5 - - [22/Jun/2025:10:44:45 +0000] "POST /apps/101/events?auth_key=demo_key&auth_timestamp=1750589085&auth_version=1.0&body_md5=ba557df93a041a550c8c3bedac219b35&auth_signature=fc341f582d794b0f7852a33d9f8e6e167517eb436cd0be556c1e5accd32a8686 HTTP/1.1" 200 2 "-" "GuzzleHttp/7"
172.20.0.5 - - [22/Jun/2025:10:44:45 +0000] "POST /apps/101/events?auth_key=demo_key&auth_timestamp=1750589085&auth_version=1.0&body_md5=d3b5da83546c9bd1156c632dabf6ddb3&auth_signature=b01269eac615640470bd85312577f3aaadb647e8e9a386026feb7fe834f2efa7 HTTP/1.1" 200 2 "-" "GuzzleHttp/7"
172.20.0.5 - - [22/Jun/2025:10:44:45 +0000] "POST /apps/101/events?auth_key=demo_key&auth_timestamp=1750589085&auth_version=1.0&body_md5=c6a4d5fc8595678eafd4469087500a14&auth_signature=692cfaedc73c1202ceb4768c8995b78971fd22a9765606f197581f0e14034c94 HTTP/1.1" 200 2 "-" "GuzzleHttp/7"
172.20.0.5 - - [22/Jun/2025:10:44:45 +0000] "POST /apps/101/events?auth_key=demo_key&auth_timestamp=1750589085&auth_version=1.0&body_md5=e9173026535f43a2159035ea60f87838&auth_signature=2b1e89e178607366ef97a06f56d8523ce093724ba466ce451b79689ceeb5fe19 HTTP/1.1" 200 2 "-" "GuzzleHttp/7"
172.20.0.5 - - [22/Jun/2025:10:44:45 +0000] "POST /apps/101/events?auth_key=demo_key&auth_timestamp=1750589085&auth_version=1.0&body_md5=54d06ef2997dd2827daa631ba328556e&auth_signature=960c92ef5ffcdd743b7bd7ad6659a516581e96765eab78e6d69b300c9b1100ca HTTP/1.1" 200 2 "-" "GuzzleHttp/7"

Funcao envia o job para geracao do pdf:
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

job faz as notificacoes via websocket:
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


segue o servico de notificacoes:
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

Segue um exemplo de evento de inicio de geração:
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

os outros eventos tem um formato parecido:
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

etc ...

segue tambem meu arquivo de chanels:
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

// Canal padrão do Laravel (manter o existente)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal de teste (manter o existente se houver)
Broadcast::channel('message-channel', function ($user) {
    return true; // ou sua lógica específica
});
