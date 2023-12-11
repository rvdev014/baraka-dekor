<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\UserExport;
use App\Filament\Utils\Filters\DateRangeFilter;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $pluralLabel = 'Пользователи';
    protected static ?string $navigationIcon = 'heroicon-m-user-group';
    protected static bool $isGloballySearchable = true;

    public static function getGloballySearchableAttributes(): array
    {
        return ['firstname', 'lastname', 'phone', 'email'];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('firstname')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('lastname')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_admin')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('firstname')
                    ->searchable()
                    ->forceSearchCaseInsensitive()
                    ->label('Имя'),
                Tables\Columns\TextColumn::make('lastname')
                    ->searchable()
                    ->forceSearchCaseInsensitive()
                    ->label('Фамилия'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->forceSearchCaseInsensitive()
                    ->label('Номер телефона'),
                Tables\Columns\TextColumn::make('dealer.name')
                    ->label('Дилер'),
                Tables\Columns\TextColumn::make('district.name')
                    ->label('Область'),
                Tables\Columns\TextColumn::make('region.name')
                    ->label('Регион'),
                Tables\Columns\TextColumn::make('location')
                    ->label('Локация')
                    ->formatStateUsing(function (User $record) {
                        $location = json_decode($record->location, true);
                        if (!empty($location)) {
                            $lat = $location['latitude'];
                            $lon = $location['longitude'];
                            return "$lat, $lon";
                        }

                        return $record->location;
                    }),
                /*Tables\Columns\IconColumn::make('is_admin')
                    ->boolean()
                    ->label('Админ?'),*/
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Время создания'),
            ])
            ->filters([
                DateRangeFilter::make('created_at'),
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
                    ->exports([UserExport::make()])
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
