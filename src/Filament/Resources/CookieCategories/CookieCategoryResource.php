<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieCategories;

use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieCategories\Pages\ManageCookieCategories;
use Guiibraun\FilamentLgpd\Models\CookieCategory;
use UnitEnum;

class CookieCategoryResource extends Resource
{
    protected static ?string $model = CookieCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'categoria';

    protected static ?string $pluralModelLabel = 'categorias';

    protected static ?string $navigationLabel = 'Categorias';

    protected static string|UnitEnum|null $navigationGroup = 'Cookies';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'cookies/categorias';

    protected static bool $hasTitleCaseModelLabel = false;

    /** @return class-string<Model> */
    public static function getModel(): string
    {
        $model = config('filament-lgpd.models.cookie_category');

        return is_string($model) && is_a($model, Model::class, true)
            ? $model
            : parent::getModel();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->label('Slug')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Descrição')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('Slug'),
                IconColumn::make('is_required')
                    ->label('Obrigatória')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCookieCategories::route('/'),
        ];
    }
}
