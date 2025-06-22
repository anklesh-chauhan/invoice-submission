<?php

namespace App\Enums;

enum VendorType: string
{
    case Supplier = 'Supplier';
    case Service = 'Service';
    case Contractor = 'Contractor';
    case Manufacturer = 'Manufacturer';
}
