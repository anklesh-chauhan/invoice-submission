<?php

namespace App\Filament\Resources\VendorMasterResource\Pages;

use App\Filament\Resources\VendorMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVendorMaster extends EditRecord
{
    protected static string $resource = VendorMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
