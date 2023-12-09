<?php

namespace App\Filament\Resources\UserPurchaseResource\Pages;

use App\Filament\Resources\UserPurchaseResource;
use App\Filament\Resources\UserPurchaseResource\UserPurchaseExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class ListUserPurchases extends ListRecords
{
    protected static string $resource = UserPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ExportAction::make()
                ->label('Экспорт')
                ->exports([UserPurchaseExport::make()]),
        ];
    }
}
