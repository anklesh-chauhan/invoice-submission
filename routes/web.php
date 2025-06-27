<?php

use Illuminate\Support\Facades\Route;
use App\Models\InvoiceSubmission;

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
});
