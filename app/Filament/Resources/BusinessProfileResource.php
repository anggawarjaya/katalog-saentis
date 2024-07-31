<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessProfileResource\Pages;
use App\Filament\Resources\BusinessProfileResource\RelationManagers;
use App\Models\BusinessProfile;
use App\Models\CategoryBusiness;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                    ]),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->label('Deskripsi UMKM'),
                Select::make('hamlet_id')
                    ->label('Nama Dusun')
                    ->relationship(name: 'hamlet', titleAttribute: 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->label('Nama Dusun'),
                    ]),
                TextInput::make('range')
                    ->maxLength(255)
                    ->default(null)
                    ->label('Rentang Harga'),
                TextInput::make('cover')
                    ->maxLength(255)
                    ->default(null),
                
                TextInput::make('facebook')
                    ->maxLength(255)
                    ->placeholder('https://facebook.com/NamaAkunUMKM/')
                    ->regex('/\b((http[s]?):\/\/)?([a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})(:[0-9]{1,5})?(\/.*)?\b/i'),
                TextInput::make('instagram')
                    ->maxLength(255)
                    ->placeholder('https://www.instagram.com/NamaAkunUMKM/')
                    ->regex('/\b((http[s]?):\/\/)?([a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})(:[0-9]{1,5})?(\/.*)?\b/i'),
                TextInput::make('tiktok')
                    ->maxLength(255)
                    ->placeholder('https://www.tiktok.com/@NamaAkunUMKM')
                    ->regex('/\b((http[s]?):\/\/)?([a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})(:[0-9]{1,5})?(\/.*)?\b/i'),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Toggle::make('approved')
                    ->required(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        $loggedInProgramId = Auth::user()->program_user->program_id ?? null;
        $hidden = false;
        $a_level_query = null;
        if ($loggedInProgramId !== null) {
            $hidden = true;
            $loggedInProgram = Program::find($loggedInProgramId);
            if ($loggedInProgram) {
                $assessmentTypeId = $loggedInProgram->assessment_type_id;
                $a_level_query = function ($query) use ($assessmentTypeId) {
                    $query->whereHas('assessment_type', function($q) use ($assessmentTypeId) {
                        $q->where('assessment_type_id', $assessmentTypeId);
                    });
                };
            }
        }

        $queryModifier = function (Builder $query) {
            $loggedInProgramId = Auth::user()->program_user->program_id ?? null;
            
            if ($loggedInProgramId !== null) {
                $query->where('program_id', $loggedInProgramId);
            }
        };
        
        $selectProgram = Program::with('education_level')
            ->get()
            ->groupBy(function ($item) {
                return $item->education_level->name;
            })
            ->map(function ($group) {
                return $group->mapWithKeys(function ($item) {
                    return [$item->id => $item->name];
                });
            })
            ->toArray();

        return $table
            ->modifyQueryUsing($queryModifier)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cover')
                    ->searchable(),
                Tables\Columns\TextColumn::make('range')
                    ->searchable(),
                Tables\Columns\TextColumn::make('facebook')
                    ->searchable(),
                Tables\Columns\TextColumn::make('instagram')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tiktok')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category_business_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('approved')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
