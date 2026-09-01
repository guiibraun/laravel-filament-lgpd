<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieScripts;

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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Guiibraun\FilamentLgpd\Enums\CookieScriptPosition;
use Guiibraun\FilamentLgpd\Enums\CookieScriptSourceType;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieScripts\Pages\ManageCookieScripts;
use Guiibraun\FilamentLgpd\Models\CookieScript;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class CookieScriptResource extends Resource
{
    protected static ?string $model = CookieScript::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCodeBracket;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'script';

    protected static ?string $pluralModelLabel = 'scripts';

    protected static ?string $navigationLabel = 'Scripts';

    protected static string|UnitEnum|null $navigationGroup = 'Cookies';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'cookies/scripts';

    protected static bool $hasTitleCaseModelLabel = false;

    /** @return class-string<Model> */
    public static function getModel(): string
    {
        $model = config('filament-lgpd.models.cookie_script');

        return is_string($model) && is_a($model, Model::class, true)
            ? $model
            : parent::getModel();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('cookie_category_id')
                            ->label('Categoria de consentimento')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->columnSpan(1),
                        Select::make('position')
                            ->label('Posição')
                            ->options(CookieScriptPosition::class)
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('name')
                            ->label('Nome interno')
                            ->helperText('Use um nome que identifique a integração no painel.')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        TextInput::make('provider')
                            ->label('Provedor')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        Textarea::make('purpose')
                            ->label('Finalidade')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Select::make('source_type')
                            ->label('Tipo de fonte')
                            ->options(CookieScriptSourceType::class)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, CookieScriptSourceType|string|null $state): void {
                                if (self::isSourceType($state, CookieScriptSourceType::External)) {
                                    $set('code', null);

                                    return;
                                }

                                $set('src', null);
                            })
                            ->columnSpan(1),
                        TextInput::make('src')
                            ->label('URL do script')
                            ->helperText('Somente URLs HTTP ou HTTPS. O elemento só será criado após o consentimento da categoria.')
                            ->url()
                            ->startsWith(['http://', 'https://'])
                            ->required(fn (Get $get): bool => self::isSourceType($get('source_type'), CookieScriptSourceType::External))
                            ->visible(fn (Get $get): bool => self::isSourceType($get('source_type'), CookieScriptSourceType::External))
                            ->maxLength(2048)
                            ->columnSpan(1),
                        Textarea::make('code')
                            ->label('Código inline')
                            ->helperText('Informe apenas o conteúdo JavaScript, sem as tags <script>. Trate este campo como código privilegiado.')
                            ->rows(10)
                            ->required(fn (Get $get): bool => self::isSourceType($get('source_type'), CookieScriptSourceType::Inline))
                            ->visible(fn (Get $get): bool => self::isSourceType($get('source_type'), CookieScriptSourceType::Inline))
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Ativo')
                            ->helperText('Scripts inativos não entram em novas versões publicadas do banner.')
                            ->default(true)
                            ->columnSpan(1),
                        TextInput::make('sort_order')
                            ->label('Ordem')
                            ->numeric()
                            ->integer()
                            ->default(0)
                            ->minValue(0)
                            ->columnSpan(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function isSourceType(mixed $state, CookieScriptSourceType $sourceType): bool
    {
        return $state === $sourceType || $state === $sourceType->value;
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
                TextColumn::make('position')
                    ->label('Posição')
                    ->badge(),
                TextColumn::make('source_type')
                    ->label('Fonte')
                    ->badge(),
                IconColumn::make('is_active')
                    ->label('Ativo')
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
            'index' => ManageCookieScripts::route('/'),
        ];
    }
}
