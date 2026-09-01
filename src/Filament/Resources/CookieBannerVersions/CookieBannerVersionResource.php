<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Pages\CreateCookieBannerVersion;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Pages\EditCookieBannerVersion;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Pages\ListCookieBannerVersions;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Pages\ViewCookieBannerVersion;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Schemas\CookieBannerVersionForm;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Schemas\CookieBannerVersionInfolist;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieBannerVersions\Tables\CookieBannerVersionsTable;
use Guiibraun\FilamentLgpd\Models\CookieBannerVersion;
use UnitEnum;

class CookieBannerVersionResource extends Resource
{
    protected static ?string $model = CookieBannerVersion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?string $recordTitleAttribute = 'headline';

    protected static ?string $modelLabel = 'versão do aviso';

    protected static ?string $pluralModelLabel = 'versões do aviso';

    protected static ?string $navigationLabel = 'Versões';

    protected static string|UnitEnum|null $navigationGroup = 'Cookies';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'cookies/versoes';

    protected static bool $hasTitleCaseModelLabel = false;

    /** @return class-string<Model> */
    public static function getModel(): string
    {
        $model = config('filament-lgpd.models.cookie_banner_version');

        return is_string($model) && is_a($model, Model::class, true)
            ? $model
            : parent::getModel();
    }

    public static function canEdit(Model $record): bool
    {
        return $record instanceof CookieBannerVersion && ! $record->isPublished();
    }

    public static function canDelete(Model $record): bool
    {
        return $record instanceof CookieBannerVersion && ! $record->isPublished();
    }

    public static function form(Schema $schema): Schema
    {
        return CookieBannerVersionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CookieBannerVersionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CookieBannerVersionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCookieBannerVersions::route('/'),
            'create' => CreateCookieBannerVersion::route('/create'),
            'view' => ViewCookieBannerVersion::route('/{record}'),
            'edit' => EditCookieBannerVersion::route('/{record}/edit'),
        ];
    }
}
