<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SiteMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, string>  $lines
     */
    public function __construct(
        public string $subjectLine,
        public string $heading,
        public array $lines = [],
        public ?string $actionUrl = null,
        public ?string $actionLabel = null,
        public ?string $footerNote = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.site');
    }
}
