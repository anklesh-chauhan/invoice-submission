<?php

namespace App\Enums;

enum VendorStatus: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';
    case Suspended = 'Suspended';
}
