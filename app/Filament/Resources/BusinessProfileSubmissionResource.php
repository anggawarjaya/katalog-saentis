<?php

namespace App\Filament\Resources;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Resources\BusinessProfileSubmissionResource\Pages;
use App\Filament\Resources\BusinessProfileSubmissionResource\RelationManagers;
use App\Models\BusinessProfile;
use Filament\Forms;
use Filament\Forms\Components\Section;
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
use Traineratwot\FilamentOpenStreetMap\Forms\Components\MapInput;

class BusinessProfileSubmissionResource extends Resource
{
    protected static ?string $model = BusinessProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Pengajuan UMKM';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Pengajuan UMKM';

    protected static ?string $modelLabel = 'Pengajuan UMKM';

    protected static ?string $slug = 'pengajuan-umkm';

    public static function getNavigationBadge(): ?string
    {
        $queryModifier = function ($query) {
            $query->where('approved', 0);
        };

        $count = static::getModel()::where($queryModifier)->count();

        return (string) $count;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama UMKM')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                Select::make('category_business_id')
                    ->label('Kategori UMKM')
                    ->relationship(name: 'category_business', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(),
                TinyEditor::make('description')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsVisibility('public')
                    ->fileAttachmentsDirectory('uploads')
                    ->profile('full')
                    ->columnSpanFull()
                    ->label('Deskripsi')
                    ->disabled(),
                Section::make('Detail Informasi UMKM')
                    ->schema([
                        Select::make('user_id')
                            ->relationship(name: 'user', titleAttribute: 'name')
                            ->required()
                            ->label('Pemilik UMKM')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        Select::make('hamlet_id')
                            ->label('Nama Dusun')
                            ->relationship(name: 'hamlet', titleAttribute: 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        TextInput::make('range')
                            ->maxLength(255)
                            ->label('Rentang Harga')
                            ->placeholder('Rp 10.000 - Rp 100.000')
                            ->required()
                            ->disabled(),                       
                    ])
                    ->compact()
                    ->columns(3),
                Section::make('Media Sosial')
                    ->schema([
                        TextInput::make('facebook')
                            ->maxLength(255)
                            ->columns(3)
                            ->regex('/\b((http[s]?):\/\/)?([a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})(:[0-9]{1,5})?(\/.*)?\b/i')
                            ->disabled(),
                        TextInput::make('instagram')
                            ->maxLength(255)
                            ->columns(3)
                            ->regex('/\b((http[s]?):\/\/)?([a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})(:[0-9]{1,5})?(\/.*)?\b/i')
                            ->disabled(),
                        TextInput::make('tiktok')
                            ->maxLength(255)
                            ->columns(3)
                            ->regex('/\b((http[s]?):\/\/)?([a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})(:[0-9]{1,5})?(\/.*)?\b/i')
                            ->disabled(),
                    ])
                    ->compact()
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(),
                MapInput::make('location')
                    ->zoom('20')
                    ->label('Lokasi UMKM')
                    ->saveAsArray()
                    ->placeholder('Geser ke lokasi UMKM')
                    ->rows(20)
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        $queryModifier = function (Builder $query) {
            $query->where('approved', 0);
        };

        return $table
            ->modifyQueryUsing($queryModifier)
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama UMKM'),
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Pemilik'),
                TextColumn::make('category_business.name')
                    ->sortable()
                    ->searchable()
                    ->label('Kategori UMKM'),
                TextColumn::make('range')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Rentang Harga'),
                TextColumn::make('facebook')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('instagram')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tiktok')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\EditAction::make()
                    ->label('Detail'),
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
            'index' => Pages\ListBusinessProfileSubmissions::route('/'),
            'edit' => Pages\EditBusinessProfileSubmission::route('/{record}/detail'),
        ];
    }
}
