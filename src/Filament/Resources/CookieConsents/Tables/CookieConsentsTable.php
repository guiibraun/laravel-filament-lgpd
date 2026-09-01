<?php

namespace Guiibraun\FilamentLgpd\Filament\Resources\CookieConsents\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CookieConsentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Quando')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('action')
                    ->label('Escolha'),
                TextColumn::make('source')
                    ->label('Origem'),
                TextColumn::make('visitor_id')
                    ->label('Visitante')
                    ->limit(8),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
