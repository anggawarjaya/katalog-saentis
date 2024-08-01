<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 3;

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $slug = 'pengguna';

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
                    ->maxLength(255)
                    ->label('Nama Pengguna'),
                TextInput::make('email')
                    ->unique()
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->rule(Password::default())
                    ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                    ->required()
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                Select::make('roles')
                    ->label('Peran Pengguna')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
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
                    ->label('Nama Pengguna'),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
