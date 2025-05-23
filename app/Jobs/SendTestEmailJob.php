<?php

namespace App\Jobs;

use App\Mail\TestMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTestEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userEmail;
    public $userName;
    public $messageContent;

    public function __construct($userEmail, $userName, $messageContent)
    {
        $this->userEmail = $userEmail;
        $this->userName = $userName;
        $this->messageContent = $messageContent;
    }

    public function handle(): void
    {
        try {
            Log::info('SendTestEmailJob: Processing job for ' . $this->userEmail);
            $email = new TestMail($this->userName, $this->messageContent);
            Mail::to($this->userEmail)->send($email);
            Log::info('SendTestEmailJob: Email sent to ' . $this->userEmail);
        } catch (\Exception $e) {
            Log::error('SendTestEmailJob: Error sending email to ' . $this->userEmail . ' - ' . $e->getMessage());
            // Optionally, re-throw the exception if you want the job to be retried
            // throw $e;
        }
    }
}
