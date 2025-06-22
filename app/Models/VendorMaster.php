<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorMaster extends Model
{
    use SoftDeletes;

    protected $table = 'vendor_masters';

    protected $fillable = [
        'VendorCode',
        'VendorName',
        'VendorType',
        'ContactPerson',
        'Email',
        'Phone',
        'AddressLine1',
        'AddressLine2',
        'City',
        'State',
        'Country',
        'PostalCode',
        'TaxID',
        'PaymentTerms',
        'Currency',
        'BankName',
        'BankAccountNumber',
        'RoutingNumber',
        'Status',
        'Notes',
    ];

    protected $casts = [
        // 'VendorType' => 'string',
        // 'Status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // protected function casts(): array
    // {
    //     return [
    //         'VendorType' => \App\Enums\VendorType::class,
    //         'Status' => \App\Enums\VendorStatus::class,
    //     ];
    // }
}
