<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ExportAction::make()->exports([
                ExcelExport::make()
                    ->fromTable()
                    ->withFilename(fn($resource) => $resource::getModelLabel() . '-' . date('Y-m-d'))
                    ->modifyQueryUsing(fn($query) => $this->getTableQuery())
                    ->withColumns([
                        Column::make('phone')
                            ->heading('Номер телефона')
                            ->formatStateUsing(function ($state) {
                                return sprintf(
                                    "+%s (%s) %s-%s-%s",
                                    substr($state, 0, 3),
                                    substr($state, 3, 2),
                                    substr($state, 5, 3),
                                    substr($state, 8, 2),
                                    substr($state, 10, 2)
                                );
                            }),
                    ])
                    ->except([
                        'is_admin',
                        'location',
                        'updated_at',
                    ]),
            ]),
        ];
    }
}
