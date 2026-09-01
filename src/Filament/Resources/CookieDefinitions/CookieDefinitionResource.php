<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieDefinitions;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieDefinitions\Pages\ManageCookieDefinitions;
use Guiibraun\FilamentLgpd\Models\CookieDefinition;
use UnitEnum;

class CookieDefinitionResource extends Resource
{
    protected static ?string $model = CookieDefinition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'definição';

    protected static ?string $pluralModelLabel = 'definições';

    protected static ?string $navigationLabel = 'Catálogo';

    protected static string|UnitEnum|null $navigationGroup = 'Cookies';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'cookies/catalogo';

    protected static bool $hasTitleCaseModelLabel = false;

    /** @return class-string<Model> */
    public static function getModel(): string
    {
        $model = config('filament-lgpd.models.cookie_definition');

        return is_string($model) && is_a($model, Model::class, true)
            ? $model
            : parent::getModel();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cookie_category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('provider')
                    ->label('Provedor')
                    ->required()
                    ->maxLength(255),
                TextInput::make('duration')
                    ->label('Duração')
                    ->required()
                    ->maxLength(255),
                Textarea::make('purpose')
                    ->label('Finalidade')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                Toggle::make('is_first_party')
                    ->label('First-party')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Categoria'),
                TextColumn::make('provider')
                    ->label('Provedor'),
                TextColumn::make('duration')
                    ->label('Duração'),
                IconColumn::make('is_first_party')
                    ->label('First-party')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCookieDefinitions::route('/'),
        ];
    }
}
