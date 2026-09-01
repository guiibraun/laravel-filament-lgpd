<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieConsents;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieConsents\Pages\ListCookieConsents;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieConsents\Pages\ViewCookieConsent;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieConsents\Schemas\CookieConsentInfolist;
use Guiibraun\FilamentLgpd\Filament\Resources\CookieConsents\Tables\CookieConsentsTable;
use Guiibraun\FilamentLgpd\Models\CookieConsent;
use UnitEnum;

class CookieConsentResource extends Resource
{
    protected static ?string $model = CookieConsent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'visitor_id';

    protected static ?string $modelLabel = 'preferência';

    protected static ?string $pluralModelLabel = 'preferências';

    protected static ?string $navigationLabel = 'Registros';

    protected static string|UnitEnum|null $navigationGroup = 'Cookies';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'cookies/registros';

    protected static bool $hasTitleCaseModelLabel = false;

    /** @return class-string<Model> */
    public static function getModel(): string
    {
        $model = config('filament-lgpd.models.cookie_consent');

        return is_string($model) && is_a($model, Model::class, true)
            ? $model
            : parent::getModel();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return CookieConsentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CookieConsentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCookieConsents::route('/'),
            'view' => ViewCookieConsent::route('/{record}'),
        ];
    }
}
