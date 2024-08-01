<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HamletResource\Pages;
use App\Filament\Resources\HamletResource\RelationManagers;
use App\Models\Hamlet;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HamletResource extends Resource
{
    protected static ?string $model = Hamlet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Dusun';

    protected static ?string $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Dusun';

    protected static ?string $modelLabel = 'Dusun';

    protected static ?string $slug = 'dusun';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Nama Dusun')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->rowIndex()
                    ->label('No')
                    ->width(40),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Dusun'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Ditambah pada')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Diubah pada')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name', 'asc')
            ->persistSortInSession()
            ->striped()
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListHamlets::route('/'),
            'create' => Pages\CreateHamlet::route('/tambah'),
            'edit' => Pages\EditHamlet::route('/{record}/ubah'),
        ];
    }
}
