<?php

declare(strict_types=1);

namespace App\Filament\Resources\OauthClients\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OauthClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getComponents());
    }

    public static function getComponents(): array
    {
        return [
            Section::make()
                ->contained(false)
                ->columnSpanFull()
                ->columns(1)
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
                    TextInput::make(name: 'secret')
                        ->label(label: __('api_credentials.view.secret'))
                        ->belowLabel('client_secret')
                        ->hidden(fn ($state) => blank($state))
                        ->password()
                        ->revealable()
                        ->copyable()
                        ->disabled(),
                    Select::make('grant_types')
                        ->label(__('grant_types'))
                        ->multiple()
                        ->disabled(),
                ]),
        ];
    }
}
