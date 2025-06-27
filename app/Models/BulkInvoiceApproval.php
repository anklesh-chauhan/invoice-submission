<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkInvoiceApproval extends Model
{
    protected $fillable = ['token', 'invoice_ids', 'used'];
    protected $casts = [
        'invoice_ids' => 'array',
        'used' => 'boolean',
    ];
}

