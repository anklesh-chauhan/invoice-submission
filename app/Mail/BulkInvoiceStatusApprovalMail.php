<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BulkInvoiceStatusApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoices; // array of [invoice, approveUrl, rejectUrl]

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function build()
    {
        return $this->subject('Multiple Invoice Approvals Required')
                    ->markdown('emails.bulk-invoice-status-approval')
                    ->with([
                        'invoices' => $this->invoices,
                    ]);
    }
}
