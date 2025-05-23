<?php

namespace App\Listeners;

use App\Events\WebSocketTestEvent;
use Illuminate\Support\Facades\Log;

class WebSocketTestListener
{
    public function handle(WebSocketTestEvent $event)
    {
        Log::info("👂 [WEBSOCKET TEST] Listener executado para: {$event->message} | ID: {$event->testId}");
        
        // Simular processamento baseado no tipo
        switch ($event->testType) {
            case 'quick':
                // Processamento rápido
                break;
            case 'slow':
                // Processamento mais lento
                sleep(1);
                break;
            case 'data':
                // Processamento com dados
                $data = ['processed_at' => now(), 'event_id' => $event->testId];
                Log::info("📊 Dados processados: " . json_encode($data));
                break;
        }
        
        Log::info("✅ Listener concluído para: {$event->message}");
    }
}
