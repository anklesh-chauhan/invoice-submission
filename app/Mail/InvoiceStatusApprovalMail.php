<?php

namespace App\Mail;

use App\Models\InvoiceSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InvoiceStatusApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public InvoiceSubmission $invoice;
    public string $approveUrl;
    public string $rejectUrl;

    public function __construct(InvoiceSubmission $invoice, string $approveUrl, string $rejectUrl)
    {
        $this->invoice = $invoice;
        $this->approveUrl = $approveUrl;
        $this->rejectUrl = $rejectUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Status Approval Request',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice-status-approval',
            with: [
                'invoice' => $this->invoice,
                'approveUrl' => $this->approveUrl,
                'rejectUrl' => $this->rejectUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
