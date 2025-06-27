<?php

namespace App\Filament\Resources\InvoiceSubmissionResource\Pages;

use App\Filament\Resources\InvoiceSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceStatusApprovalMail;

class CreateInvoiceSubmission extends CreateRecord
{
    protected static string $resource = InvoiceSubmissionResource::class;

    // protected function afterCreate(): void
    // {
    //     $invoice = $this->record;

    //     $approveUrl = URL::signedRoute('invoices.approve', ['invoice' => $invoice->id]);
    //     $rejectUrl = URL::signedRoute('invoices.reject', ['invoice' => $invoice->id]);

    //     Mail::to($invoice->sentToUser->email)
    //         ->send(new InvoiceStatusApprovalMail($invoice, $approveUrl, $rejectUrl));
    // }
}
