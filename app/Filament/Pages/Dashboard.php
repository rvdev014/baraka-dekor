<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BasePage;
use Filament\Widgets\AccountWidget;

class Dashboard extends BasePage
{
    protected static ?string $navigationLabel = 'Главная';
    protected static ?string $navigationIcon = 'heroicon-m-home';
    protected static ?string $title = 'Главная';

    public function getWidgets(): array
    {
        return [
            AccountWidget::class
        ];
    }
}
