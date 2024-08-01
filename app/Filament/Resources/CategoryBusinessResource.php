<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryBusinessResource\Pages;
use App\Filament\Resources\CategoryBusinessResource\RelationManagers;
use App\Models\CategoryBusiness;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryBusinessResource extends Resource
{
    protected static ?string $model = CategoryBusiness::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Kategori UMKM';

    protected static ?string $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralModelLabel = 'Kategori UMKM';

    protected static ?string $modelLabel = 'Kategori UMKM';

    protected static ?string $slug = 'kategori-umkm';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Kategori UMKM')
                    ->columnSpanFull()
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
                    ->sortable()
                    ->searchable()
                    ->label('Nama Kategori UMKM'),
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
                EditAction::make(),
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
            'index' => Pages\ListCategoryBusinesses::route('/'),
            'create' => Pages\CreateCategoryBusiness::route('/tambah'),
            'edit' => Pages\EditCategoryBusiness::route('/{record}/ubah'),
        ];
    }
}
