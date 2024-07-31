<?php

namespace App\Filament\Resources;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Resources\BusinessProfileResource\Pages;
use App\Filament\Resources\BusinessProfileResource\RelationManagers;
use App\Models\BusinessProfile;
use App\Models\CategoryBusiness;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Traineratwot\FilamentOpenStreetMap\Forms\Components\MapInput;

class BusinessProfileResource extends Resource
{
    protected static ?string $model = BusinessProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Profil UMKM';

    protected static ?int $navigationSort = 1;

    protected static ?string $pluralModelLabel = 'Profil UMKM';

    protected static ?string $modelLabel = 'Profil UMKM';

    protected static ?string $slug = 'profil-umkm';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama UMKM')
                    ->required()
                    ->maxLength(255),
                Select::make('category_business_id')
                    ->label('Kategori UMKM')
                    ->relationship(name: 'category_business', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Kategori UMKM')
                            ->required(),
                    ]),
                TinyEditor::make('description')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsVisibility('public')
                    ->fileAttachmentsDirectory('uploads')
                    ->profile('full')
                    ->columnSpanFull()
                    ->label('Deskripsi'),
                Section::make('Detail Informasi UMKM')
                    ->schema([
                        Select::make('user_id')
                            ->relationship(name: 'user', titleAttribute: 'name')
                            ->required()
                            ->label('Pemilik UMKM')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Pemilik UMKM')
                                    ->required()
                                    ->label('Nama Pemilik UMKM'),
                                TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->required()
                                    ->email(),
                                TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    ->required(),
                            ]),
                        Select::make('hamlet_id')
                            ->label('Nama Dusun')
                            ->relationship(name: 'hamlet', titleAttribute: 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->label('Nama Dusun'),
                            ]),
                        TextInput::make('range')
                            ->maxLength(255)
                            ->label('Rentang Harga')
                            ->placeholder('Rp 10.000 - Rp 100.000')
                            ->required(),                       
                    ])
                    ->compact()
                    ->columns(3),
                Section::make('Media Sosial')
                    ->schema([
                        TextInput::make('facebook')
                            ->maxLength(255)
                            ->columns(3)
                            ->placeholder('https://facebook.com/NamaAkunUMKM/')
                            ->regex('/\b((http[s]?):\/\/)?([a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})(:[0-9]{1,5})?(\/.*)?\b/i'),
                        TextInput::make('instagram')
                            ->maxLength(255)
                            ->columns(3)
                            ->placeholder('https://www.instagram.com/NamaAkunUMKM/')
                            ->regex('/\b((http[s]?):\/\/)?([a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})(:[0-9]{1,5})?(\/.*)?\b/i'),
                        TextInput::make('tiktok')
                            ->maxLength(255)
                            ->columns(3)
                            ->placeholder('https://www.tiktok.com/@NamaAkunUMKM/')
                            ->regex('/\b((http[s]?):\/\/)?([a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})(:[0-9]{1,5})?(\/.*)?\b/i'),
                    ])
                    ->compact()
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(),
                    MapInput::make('location')
                        ->label('Lokasi UMKM')
                        ->saveAsArray()
                        ->placeholder('Geser ke lokasi UMKM')
                        ->coordinates(98.7438529, 3.6635647)
                        ->rows(20)
                        ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        $queryModifier = function (Builder $query) {
            $query->where('approved', 1);
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
            'index' => Pages\ListBusinessProfiles::route('/'),
            'create' => Pages\CreateBusinessProfile::route('/tambah'),
            'edit' => Pages\EditBusinessProfile::route('/{record}/ubah'),
        ];
    }
}
