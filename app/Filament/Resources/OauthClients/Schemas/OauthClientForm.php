<?php

namespace App\Filament\Resources\OauthClients\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OauthClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('oauth_token')
                    ->label('Token')
                    ->formatStateUsing(fn () => route('passport.token'))
                    ->copyable()
                    ->disabled(),
                TextInput::make('id')
                    ->label('ID')
                    ->copyable()
                    ->belowLabel('client_id')
                    ->disabled(),
                Select::make('grant_types')
                    ->label(__('grant_types'))
                    ->multiple()
                    ->disabled(),
            ])
            ->columns(1);
    }
}
