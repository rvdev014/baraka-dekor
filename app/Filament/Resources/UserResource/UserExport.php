<?php

namespace App\Filament\Resources\UserResource;

use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class UserExport extends ExcelExport
{
    public function setUp(): void
    {
        $this->fromTable();
        $this->withFilename(fn($resource) => $resource::getModelLabel() . '-' . date('Y-m-d'));
        $this->withColumns([
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
        ]);
        $this->except([
            'is_admin',
            'location',
            'updated_at',
        ]);
    }
}
