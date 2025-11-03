<?php

namespace App\Filament\Resources\OauthClients\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Http\RedirectResponse;
use Laravel\Passport\Client;

class OauthClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(name: 'id')
                    ->label(label: '#'),
                TextColumn::make(name: 'user_id')
                    ->label(label: __('user'))
                    ->action(
                        action: fn ($state): ?RedirectResponse => filled(value: $state) ? redirect()->route(route: 'filament.admin.resources.users.index', parameters: ['tableSearch' => $state]) : null
                    ),
                TextColumn::make(name: 'name')
                    ->label(label: __('name'))
                    ->searchable(),
                TextColumn::make(name: 'redirect')
                    ->label(label: __('redirection')),
                TextColumn::make(name: 'grant_types')
                    ->label(label: __('grant_types'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make(name: 'revoked')
                    ->label(label: __('revoked')),
                TextColumn::make(name: 'updated_at')
                    ->label(label: __('updated_at'))
                    ->dateTime(format: 'd/m/Y H:i:s')
                    ->toggleable(),
            ])
            ->filters(filters: [
                TernaryFilter::make(name: 'revoked')
                    ->label(label: __('revoked')),
            ])
            ->recordActions(actions: [
                ActionGroup::make(actions: [
                    ViewAction::make()
                        ->label(label: __('api_credentials.view.action.label'))
                        ->icon(icon: 'heroicon-o-lock-closed')
                        ->modalWidth(width: Width::Large)
                        ->modalHeading(heading: fn (Client $record) => __('authentication') . ' - ' . $record->name),
                    DeleteAction::make(),
                ])->button()->color(color: 'gray'),
            ])
            ->toolbarActions(actions: [
                BulkActionGroup::make(actions: [
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
