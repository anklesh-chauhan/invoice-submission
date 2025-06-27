<?php

use Illuminate\Support\Facades\Route;
use App\Models\InvoiceSubmission;
use App\Models\BulkInvoiceApproval;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web'])->group(function () {
    Route::get('/invoices/{invoice}/approve', function (InvoiceSubmission $invoice) {
        abort_unless(request()->hasValidSignature(), 403);
        $invoice->update(['status' => 'accepted']);
        return view('invoices.approval-response', ['status' => 'accepted']);
    })->name('invoices.approve');

    Route::get('/invoices/{invoice}/reject', function (InvoiceSubmission $invoice) {
        abort_unless(request()->hasValidSignature(), 403);
        $invoice->update(['status' => 'rejected']);
        return view('invoices.approval-response', ['status' => 'rejected']);
    })->name('invoices.reject');

    Route::get('/invoices/bulk-approve', function () {
        abort_unless(request()->hasValidSignature(), 403);

        $token = request('token');
        $record = BulkInvoiceApproval::where('token', $token)->firstOrFail();

        if ($record->used) {
            abort(403, 'This approval link has already been used.');
        }

        InvoiceSubmission::whereIn('id', $record->invoice_ids)->update(['status' => 'accepted']);
        $record->update(['used' => true]);

        return view('invoices.approval-response', ['status' => 'accepted']);
    })->name('invoices.bulk-approve');

    Route::get('/invoices/bulk-reject', function () {
        abort_unless(request()->hasValidSignature(), 403);

        $token = request('token');
        $record = BulkInvoiceApproval::where('token', $token)->firstOrFail();

        if ($record->used) {
            abort(403, 'This rejection link has already been used.');
        }

        InvoiceSubmission::whereIn('id', $record->invoice_ids)->update(['status' => 'rejected']);
        $record->update(['used' => true]);

        return view('invoices.approval-response', ['status' => 'rejected']);
    })->name('invoices.bulk-reject');
});
