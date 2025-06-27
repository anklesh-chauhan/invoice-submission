<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BulkInvoiceStatusApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoices;
    public $approveUrl;
    public $rejectUrl;

    public function __construct($invoices, $approveUrl, $rejectUrl)
    {
        $this->invoices = $invoices;
        $this->approveUrl = $approveUrl;
        $this->rejectUrl = $rejectUrl;
    }

    public function build()
    {
        return $this->subject('Approve All Invoices')
                    ->markdown('emails.bulk-invoice-status-approval')
                    ->with([
                        'invoices' => $this->invoices,
                        'approveUrl' => $this->approveUrl,
                        'rejectUrl' => $this->rejectUrl,
                    ]);
    }
}
