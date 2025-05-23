<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class QueueTestJob implements ShouldQueue
{
    use Queueable;
    
    public $tries = 3;
    public $timeout = 60;
    public $message;
    public $testType;
    
    public function __construct($message, $testType = 'normal')
    {
        $this->message = $message;
        $this->testType = $testType;
    }
    
    public function handle()
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $logMessage = "🎉 [TESTE ROBUSTO] {$this->message} | Tipo: {$this->testType} | Executado: {$timestamp}";
        
        Log::info($logMessage);
        
        // Simular diferentes tipos de processamento
        switch ($this->testType) {
            case 'quick':
                // Job rápido
                break;
            case 'slow':
                // Job mais lento
                sleep(2);
                break;
            case 'memory':
                // Job que usa memória
                $data = range(1, 1000);
                $sum = array_sum($data);
                Log::info("Soma calculada: $sum");
                break;
        }
        
        Log::info("✅ Job concluído: {$this->message}");
    }
    
    public function failed(\Throwable $exception)
    {
        Log::error("❌ Job falhou: {$this->message} | Erro: " . $exception->getMessage());
    }
}
