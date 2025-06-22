<?php

namespace App\Filament\Resources\VendorMasterResource\Pages;

use App\Filament\Resources\VendorMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendorMasters extends ListRecords
{
    protected static string $resource = VendorMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
