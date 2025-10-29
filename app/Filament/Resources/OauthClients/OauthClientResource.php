<?php

namespace App\Filament\Resources\OauthClients;

use App\Filament\Resources\OauthClients\Pages\ListOauthClients;
use App\Filament\Resources\OauthClients\Schemas\OauthClientForm;
use App\Filament\Resources\OauthClients\Tables\OauthClientsTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Laravel\Passport\Passport;

class OauthClientResource extends Resource
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-command-line';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return OauthClientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OauthClientsTable::configure(table: $table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOauthClients::route(path: '/'),
        ];
    }

    public static function getModel(): string
    {
        return Passport::clientModel();
    }

    public static function getModelLabel(): string
    {
        return __('api_credentials.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('api_credentials.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('configuration');
    }
}
