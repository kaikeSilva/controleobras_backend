<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AutoTestJob implements ShouldQueue
{
    use Queueable;
    
    public $message;
    
    public function __construct($message)
    {
        $this->message = $message;
    }
    
    public function handle()
    {
        Log::info("🤖 [AUTO-TEST] {$this->message} - " . now());
    }
}
