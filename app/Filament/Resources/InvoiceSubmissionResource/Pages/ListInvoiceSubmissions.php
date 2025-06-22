<?php

namespace App\Filament\Resources\InvoiceSubmissionResource\Pages;

use App\Filament\Resources\InvoiceSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoiceSubmissions extends ListRecords
{
    protected static string $resource = InvoiceSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
