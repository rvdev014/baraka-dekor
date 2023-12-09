<?php

namespace App\Filament\Resources\UserPurchaseResource\Pages;

use App\Filament\Resources\UserPurchaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserPurchase extends EditRecord
{
    protected static string $resource = UserPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
