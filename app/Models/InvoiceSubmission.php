<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\InvoiceStatus;


class InvoiceSubmission extends Model
{
    protected $table = 'invoice_submissions';

    use HasFactory;

    protected $fillable = [
        'invoice_date',
        'vendor_id',
        'invoice_number',
        'amount',
        'sent_to_user_id',
        'invoice_files',
        'status',
        'notes', // Optional notes field
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'amount' => 'decimal:2',
        'invoice_files' => 'array',
        'status' => InvoiceStatus::class,
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(VendorMaster::class);
    }

    public function sentToUser()
    {
        return $this->belongsTo(User::class, 'sent_to_user_id');
    }

    protected static function booted()
    {
        // Prevent update if status is accepted
        static::updating(function ($invoice) {
            if ($invoice->getOriginal('status') === 'accepted') {
                throw new \Exception("Approved invoice cannot be modified.");
            }
        });

        // Prevent delete if status is accepted
        static::deleting(function ($invoice) {
            if ($invoice->status === 'accepted') {
                throw new \Exception("Approved invoice cannot be deleted.");
            }
        });
    }
}
