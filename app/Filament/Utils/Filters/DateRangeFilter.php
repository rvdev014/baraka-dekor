<?php

namespace App\Filament\Utils\Filters;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class DateRangeFilter extends Filter
{
    protected string $attribute = 'created_at';

    protected function setUp(): void
    {
        $this->form([
            DatePicker::make('from')->label('Время от'),
            DatePicker::make('until')->label('Время до'),
        ]);

        $this->indicateUsing(function (array $data) {
            $indicators = [];

            if ($data['from']) {
                $indicators[] = Carbon::create($data['from'])->translatedFormat('d F Y');
            }

            if ($data['until']) {
                $indicators[] = Carbon::create($data['until'])->translatedFormat('d F Y');
            }

            return implode(' - ', $indicators);
        });

        $this->query(function (Builder $query, array $data): Builder {
            return $query
                ->when(
                    $data['from'],
                    fn(Builder $query, $date): Builder => $query->whereDate($this->attribute, '>=', $date),
                )
                ->when(
                    $data['until'],
                    fn(Builder $query, $date): Builder => $query->whereDate($this->attribute, '<=', $date),
                );
        });
    }

    public function setAttribute(string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }
}
