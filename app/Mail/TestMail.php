<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $userTestName;
    public $testMessage;
    public $sentTestAt;

    public function __construct($userTestName, $testMessage)
    {
        $this->userTestName = $userTestName;
        $this->testMessage = $testMessage;
        $this->sentTestAt = now()->format('Y-m-d H:i:s');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Laravel Queue Test Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.test-mail',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
