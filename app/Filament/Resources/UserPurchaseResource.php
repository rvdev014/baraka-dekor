<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserPurchaseResource\Pages;
use App\Filament\Resources\UserPurchaseResource\RelationManagers;
use App\Filament\Resources\UserPurchaseResource\UserPurchaseExport;
use App\Models\User;
use App\Models\UserPurchase;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class UserPurchaseResource extends Resource
{
    protected static ?string $model = UserPurchase::class;
    protected static ?string $pluralLabel = 'Покупки пользователей';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $isGloballySearchable = true;

    public static function getGloballySearchableAttributes(): array
    {
        return ['price'];
    }

    /*public static function canCreate(): bool
    {
        return false;
    }*/

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'firstname')
                    ->getOptionLabelFromRecordUsing(fn(User $user) => $user->getFilamentName())
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.firstname')
                    ->label('ФИО')
                    ->formatStateUsing(fn (UserPurchase $record) => $record->user->getFilamentName()),
                Tables\Columns\TextColumn::make('user.phone')
                    ->numeric()
                    ->label('Номер телефона')
                    ->formatStateUsing(fn (UserPurchase $record) => $record->user->phone),
                Tables\Columns\TextColumn::make('user.district')
                    ->label('Область')
                    ->formatStateUsing(fn (UserPurchase $record) => $record->user->district->name),
                Tables\Columns\TextColumn::make('user.region')
                    ->label('Регион')
                    ->formatStateUsing(fn (UserPurchase $record) => $record->user->region->name),
                Tables\Columns\TextColumn::make('user.dealer')
                    ->label('Дилер')
                    ->formatStateUsing(fn (UserPurchase $record) => $record->user->dealer->name),
                Tables\Columns\TextColumn::make('price')
                    ->numeric()
                    ->sortable()
                    ->label('Сумма покупки'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Время покупки'),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Время от'),
                        DatePicker::make('created_until')->label('Время до'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
                    ->label('Экспорт')
                    ->exports([UserPurchaseExport::make()])
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserPurchases::route('/'),
            'create' => Pages\CreateUserPurchase::route('/create'),
            'edit' => Pages\EditUserPurchase::route('/{record}/edit'),
        ];
    }
}
